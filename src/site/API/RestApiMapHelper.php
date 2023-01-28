<?php

namespace Site\API;

use Common\Util\UserEnvInfo;

trait RestApiMapHelper
{

  public function getApiObject()
  {
    $result = new \stdClass;
    $result->id = intval($this->id);

    foreach (static::$apiMap as $field => $desc) {
      $dbField = isset($desc['field']) ? $desc['field'] : $field;
      $type = isset($desc['type']) ? $desc['type'] : 'text';

      if ($type === 'multi') {
        $options = explode(',', $this->$dbField);
        $result->$field = array_filter($options, function ($a) {
          return $a;
        });
      }

      if ($type === 'rel') {
        $objClass = $desc['target'];
        $value = call_user_func([$objClass, 'get'], $this->$dbField);
        $result->$field = $value ? $value->getApiObject() : $value;
      }

      if ($type === 'relmany') {
        $objClass = $desc['target'];
        $value = call_user_func([$objClass, 'select'])->where([$desc['field'] => $this->id])->get();
        $result->$field = array_map(function ($a) {
          return $a->getApiObject();
        }, $value);
      }

      if ($type === 'flag') {
        $result->$field = $this->$dbField === null ? null : $this->$dbField;
      }

      if ($type === 'json') {
        $value = @json_decode($this->$dbField);
        $result->$field = $value;
      }

      if ($type === 'bool') {
        $result->$field = boolval($this->$dbField);
      }
      if ($type === 'number') {
        $result->$field = $this->$dbField ? floatval($this->$dbField) : null;
      }

      if ($type === 'password') {
        $result->$field = null;
      }

      if (!isset($result->$field)) {
        $result->$field = $this->$dbField;
      }
    }

    // Parse attributes as parameters
    $class_methods = get_class_methods($this);
    foreach ($class_methods as $method_name) {
      if (strpos($method_name, 'attribute') === 0) {
        $name = str_replace('attribute', '', $method_name);
        $key = strtolower(substr($name, 0, 1)) . '' . substr($name, 1);
        if (isset($_GET['skip']) && is_array($_GET['skip'])) {
          if (in_array($key, $_GET['skip'])) {
            continue;
          }
        }
        $result->{$key} = call_user_func([$this, $method_name]);
      }
    }

    // Use transform
    if (in_array('transform', $class_methods)) {
      return call_user_func([$this, 'transform'], $result);
    }

    return $result;
  }

  public function updateApiObject($data)
  {
    if (!isset($this->id) || !$this->id) {
      $this->save();
    }

    $updatedFields = [];
    foreach (static::$apiMap as $field => $desc) {
      $type = isset($desc['type']) ? $desc['type'] : 'text';
      $dbField = isset($desc['field']) ? $desc['field'] : $field;
      if (!array_key_exists($field, $data)) {
        continue;
      }

      $fieldData = $data[$field];
      if ($fieldData === 'CURRENT_OS') {
        $fieldData = UserEnvInfo::os();
      }
      if ($fieldData === 'CURRENT_BROWSER') {
        $fieldData = UserEnvInfo::browser();
      }
      if ($fieldData === 'CURRENT_IP') {
        $fieldData = UserEnvInfo::ip();
      }
      if ($fieldData === 'CURRENT_TIMESTAMP') {
        $fieldData = date('Y-m-d H:i:s');
      }

      $oldFieldValue = isset($this->$dbField) ? $this->$dbField : null;
      if ($type == 'multi') {
        $this->$dbField = implode(',', $fieldData);
      }

      if ($type === 'rel') {
        if (is_null($fieldData)) {
          if (isset($desc['cascade']) && $desc['cascade'] === 'delete') {
            $targetClass = $desc['target'];
            $targetObj = $targetClass::get($this->$dbField);
            if ($targetObj) {
              $targetObj->delete();
            }
          }
          $this->$dbField = $fieldData;
        } elseif (!is_array($fieldData)) {
          $this->$dbField = $fieldData;
        } else {
          $targetClass = $desc['target'];
          if (isset($fieldData['id']) && $fieldData['id']) {
            $targetObj = $targetClass::get($fieldData['id']);
          } else {
            $targetObj = new $targetClass();
          }

          if (method_exists($targetObj, 'updateApiObject')) {
            $updatedFields = array_merge($updatedFields, $targetObj->updateApiObject($fieldData));
          }

          $targetObj->save();
          $this->$dbField = $targetObj->id;
        }
      }

      if ($type === 'relmany') {
        if (!is_array($fieldData)) {
          continue;
        }

        $objsToDelete = [];
        $targetClass = $desc['target'];
        foreach ($targetClass::select()->where([$desc['field'] => $this->id])->get() as $targetObj) {
          if (isset($desc['cascade']) && $desc['cascade'] === 'delete') {
            $objsToDelete[$targetObj->id] = $targetObj;
          }
        }
        foreach ($fieldData as $fieldTarget) {
          if (!is_array($fieldTarget)) {
            $fieldTarget = ['id' => $fieldTarget];
          }

          if (isset($fieldTarget['id']) && $fieldTarget['id']) {
            $targetObj = $targetClass::get($fieldTarget['id']);
          } else {
            $targetObj = new $targetClass();
            $targetObj->$dbField = $this->id;
          }
          $updatedFields = array_merge($updatedFields, $targetObj->updateApiObject($fieldTarget));
          $targetObj->save();
          unset($objsToDelete[$targetObj->id]);
        }
        foreach ($objsToDelete as $objToDelete) {
          $objToDelete->deleteApiObject();
        }
      }

      if ($type === 'flag') {
        $this->$dbField = $fieldData ? '1' : '0';
      }

      if ($type === 'json') {
        $this->$dbField = json_encode($fieldData);
      }

      if ($type === 'bool') {
        $this->$dbField = $fieldData === true || $fieldData === 1 ? 1 : 0;
      }

      if ($type === 'text') {
        $this->$dbField = nl2br(htmlentities($fieldData));
      }

      if ($type === 'number') {
        $this->$dbField = $fieldData ? floatval($fieldData) : null;
      }

      if ($type === 'password') {
        $setter = $desc['setter'];
        $this->$setter($fieldData);
      }

      if (!isset($this->$dbField)) {
        $this->$dbField = $fieldData;
      }

      if ($oldFieldValue !== null && $this->$dbField !== 0 && $this->$dbField != $oldFieldValue) {
        $updatedFields[] = $dbField;
      }
    }

    return $updatedFields;
  }

  public function deleteApiObject()
  {
    foreach (static::$apiMap as $field => $desc) {
      $type = isset($desc['type']) ? $desc['type'] : 'text';
      if (isset($desc['cascade']) && $desc['cascade'] == 'delete') {
        $field = $desc['field'];
        $targetClass = $desc['target'];
        if ($type == 'rel') {
          $obj = $targetClass::get($this->$field);
          if ($obj) {
            $obj->deleteApiObject();
          }
        }
        if ($type == 'relmany') {
          foreach ($targetClass::select()->where(['$field' => $this->id])->get() as $obj) {
            $obj->deleteApiObject();
          }
        }
      }
    }

    $this->delete();
  }

  public static function getApiDesc($sub = false)
  {
    $result = new \stdClass;
    foreach (static::$apiMap as $field => $desc) {
      $type = isset($desc['type']) ? $desc['type'] : 'text';
      if (($type == 'rel' || $type == 'relmany') && ($desc['target'] != self::class || !$sub)) {
        $desc['desc'] = call_user_func([$desc['target'], 'getApiDesc'], true);
      }
      $result->$field = $desc;
    }

    return $result;
  }

  public function validateData($data)
  {
    $invalidFields = [];
    if (!$data || !is_array($data)) {
      return $invalidFields;
    }

    $validator = new \Common\Util\Validator();
    $apiMap = static::$apiMap;
    foreach ($apiMap as $field => $afField) {
      if (strpos($afField['req'] ?? '', 'required') !== false) {
        $data[$field] = $data[$field] ?? '';
      }
    }
    foreach ($data as $field => $value) {
      if (!isset($apiMap[$field])) {
        continue;
      }

      $apiField = $apiMap[$field];
      unset($apiMap[$field]);
      if (isset($apiField['reqIf'])) {
        if (!$data[$apiField['reqIf']]) {
          continue;
        }
      }
      $fieldValid = $validator->validate($apiField['req'] ?? '', $value);
      if (!$fieldValid) {
        $invalidFields[] = $field;
      }

      if ($apiField['type'] == 'relmany') {
        $targetClass = $apiField['target'];
        $target = new $targetClass;
        foreach ($value as $i => $val) {
          foreach ($target->validateData($val) as $invalidField) {
            $invalidFields[] = $field . '.' . $i . '.' . $invalidField;
          }
        }
      }
      if ($apiField['type'] == 'rel') {
        $targetClass = $apiField['target'];
        $target = new $targetClass;
        foreach ($target->validateData($value) as $invalidField) {
          $invalidFields[] = $field . '.' . $invalidField;
        }
      }
    }

    return $invalidFields;
  }
}
