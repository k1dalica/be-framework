<?php

namespace Site\Controller;

use Site\Entity\User;
use Site\Util\Watcher;
use Site\Entity\UserLogin;
use Common\Exception\UnauthorizedException;

class API extends Controller
{

  public function root()
  {
    $data = $this->getInputData();
    $user = $this->getCurrentUser($data);
    $result = [
      "loggedIn" => !!$user,
      "currentUser" => $user ? [
        "id" => $user->id,
        "email" => $user->email,
        "lastLogin" => $user->lastLogin,
        "lastRead" => $user->lastRead,
      ] : null,
    ];
    if ($user) {
      $result["messages"] = [];
    }
    return jsonView($result);
  }

  public function login($rawData = null)
  {
    $data = $this->getInputData();
    $result = $this->doLogin($data);
    $view = jsonViewRaw();
    $cookieName = config()["session"]["cookieName"];
    if ($result["success"])
      $view->setCookie($cookieName, $result["token"] ?? "", 0, "/");
    return $view->render($result);
  }

  public function doLogin($data)
  {
    $user = User::select()->where(["email" => $data["email"]])->first();
    $result = ["success" => false];
    if ($user) {
      $key = $user->login(@$data["password"]);
      $result["key"] = $key;
      if ($key && $key != "passwordChange" && $key != "activation") {
        $result["success"] = true;
        $result["token"] = $key;
        $user->lastLogin = (new \DateTime())->format("Y-m-d H:i:s");
        $user->save();

        $loginRec = new UserLogin();
        $loginRec->createdBy = $user->createdBy ? $user->createdBy : $user->id;
        $loginRec->userId = $user->id;
        $loginRec->recorded = date("Y-m-d H:i:s");
        $loginRec->ipAddress = currentRequest()->ip;
        $loginRec->userAgent = $_SERVER["HTTP_USER_AGENT"];
        $loginRec->save();
      }

      if ($key == "passwordChange") {
        $result["user"] = $user;
        $result["passwordChange"] = true;
      }
      if ($key == "activation") {
        $result["user"] = $user;
        $result["activation"] = true;
      }

      if (!$key && $user->active === "Blocked") {
        $result["blocked"] = true;
      }
    }
    return $result;
  }

  protected function view($activeObj, $view = null)
  {
    $data = (array)$activeObj;
    if (!$view)
      return $data;
  }

  protected function doUpdate($activeObj, $data, $view = null)
  {
    if (!$view) {
      foreach ($data as $field => $value)
        $activeObj->$field = $value;
    } else {
    }
    $activeObj->save();
  }

  public function list($entity)
  {
    $data = $this->getInputData();

    if (!$this->getCurrentUser($data)) throw new UnauthorizedException();

    $entityClass = "\\Site\\Entity\\$entity";
    $from = [$entityClass::$tableName => "base"];
    $query = $entityClass::select();
    $countQuery = $entityClass::count();
    if (isset($data["limit"]))
      $query->limit($data["limit"], isset($data["start"]) ? $data["start"] : 0);
    if (isset($data["q"]) && is_array($data["q"])) {
      $targetQ = [];
      foreach ($data["q"] as $qField => $qVal) {
        $qFields = explode(".", $qField);
        $qField = $qFields[0];
        $fieldDesc = $entityClass::$apiMap[$qField] ?? [];
        $dbField = "base." . ($fieldDesc["field"] ?? $qField);
        if ($qVal === "null") $qVal = null;

        if (($fieldDesc["type"] ?? "") == "rel" && count($qFields) > 1) {
          $targetClass = $fieldDesc["target"];
          $targetTable = $targetClass::$tableName;
          if (!isset($from[$targetTable])) {
            $from[$targetTable] = $targetTable;
            $targetQ[$dbField] = ["= $targetTable.id"];
          }
          $targetDbField = $targetClass::$apiMap[$qFields[1]]["field"] ?? $qFields[1];
          $targetQ[$targetTable . "." . $targetDbField] = $qVal;
        } elseif (($fieldDesc["type"] ?? "") == "relmany") {
          $targetClass = $fieldDesc["target"];
          $targetTable = $targetClass::$tableName;
          if (!isset($from[$targetTable])) {
            $from[$targetTable] = $targetTable;
            $targetQ["${targetTable}_id"] = ["= $targetTable.$fieldDesc[field]", "NOPARAM", "base.id"];
          }
          $targetDbField = $targetClass::$apiMap[$qFields[1]]["field"] ?? $qFields[1];
          $targetQ[$targetTable . "." . $targetDbField] = $qVal;
        } else
          $targetQ[$dbField] = $qVal;
      }
      $query->where($targetQ);
      $countQuery->where($targetQ);
    }
    if (isset($data["by"])) {
      $orderBy = [];
      foreach ((array)$data["by"] as $byItem) {
        $byItem = explode(":", $byItem);
        $orderBy[$byItem[0]] = $byItem[1] ?? "ASC";
      }
      $query->orderBy($orderBy);
    }
    if (count($from) > 1) {
      $query->groupBy(["base.id"]);
      $query->from($from);
      $countQuery->from($from);
    }
    $result = array_map([$this, "mapObject"], $query->get());
    $countResult = $countQuery->scalar();
    return jsonView($result);
  }

  public function get($entity, $id)
  {
    $data = $this->getInputData();
    if (!$this->getCurrentUser($data)) throw new UnauthorizedException();

    $entityClass = "\\Site\\Entity\\$entity";
    $obj = $entityClass::get($id);
    $result = null;
    if ($obj)
      $result = $this->mapObject($obj);
    return jsonView($result);
  }

  public function update($entity, $id)
  {
    $data = $this->getInputData();
    if (!$this->getCurrentUser($data)) throw new UnauthorizedException();

    $entityClass = "\\Site\\Entity\\$entity";
    $obj = $entityClass::get($id);
    $success = false;

    if ($obj) {
      $updatedFields = $obj->updateApiObject($data["obj"] ?? []);
      $obj->save();
      $success = true;
      Watcher::onUpdate($entity, $obj, $data["obj"]);
    }

    if ($data["returnObj"] ?? false) {
      return jsonView([
        "success" => $success,
        "item" => $this->mapObject($obj),
      ]);
    }

    return jsonView([
      "success" => $success,
    ]);
  }

  public function create($entity)
  {
    $data = $this->getInputData();
    $user = $this->getCurrentUser($data);

    if (!$user) throw new UnauthorizedException();

    $GLOBALS["currentUser"] = $user;
    $entityClass = "\\Site\\Entity\\$entity";
    $obj = new $entityClass();
    $success = false;
    if ($obj) {
      $obj->updateApiObject($data["obj"] ?? []);
      $obj->save();
      if ($obj->id) {
        $success = true;
        Watcher::onCreate($entity, $obj, $data["obj"]);
      }
    }

    if ($data["returnObj"] ?? false) {
      return jsonView([
        "success" => $success,
        "item" => $this->mapObject($obj),
        "id" => $obj->id ?? null,
      ]);
    }

    return jsonView([
      "success" => $success,
      "id" => $obj->id ?? null,
    ]);
  }

  public function delete($entity, $id)
  {
    $data = $this->getInputData();
    if (!$this->getCurrentUser($data)) throw new UnauthorizedException();

    $entityClass = "\\Site\\Entity\\$entity";
    $obj = $entityClass::get($id);
    $success = false;
    if ($obj) {
      $obj->delete();
      $success = true;
    }
    return jsonView([
      "success" => $success,
    ]);
  }

  public function upload($entity, $id)
  {
    $data = $this->getInputData();
    $currentUser = $this->getCurrentUser($data);
    if (!$this->getCurrentUser($data)) throw new UnauthorizedException();

    $entityClass = "\\Site\\Entity\\$entity";
    $obj = $entityClass::get($id);
    $success = false;
    if ($obj) {
      if (method_exists($obj, "uploadFile"))
        $success = $obj->uploadFile($_FILES["file"], $_POST["field"] ?? null);
    }
    if ($success) {
      $obj->save();
      $lastLogin = UserLogin::select()->where(["userId" => $currentUser->id])->orderBy(["id" => "DESC"])->first();
      if ($lastLogin) {
        $lastLogin->transferred += $_FILES["file"]["size"];
        $lastLogin->save();
      }
    }
    return jsonView([
      "success" => $success,
    ]);
  }

  public function action($entity, $id, $action)
  {
    $data = $this->getInputData();
    $data["currentUser"] = $this->getCurrentUser($data);
    $entityClass = "\\Site\\Entity\\$entity";
    if ($id == "new")
      $obj = new $entityClass();
    else
      $obj = $entityClass::get($id);
    if (!$obj)
      return jsonView([
        "success" => false,
      ]);
    $actionMethod = "action" . ucfirst($action);
    $actionResult = false;
    if (method_exists($obj, $actionMethod) && is_callable([$obj, $actionMethod]))
      $actionResult = $obj->$actionMethod($data);
    if (!$actionResult)
      return jsonView([
        "success" => false,
      ]);
    if (!is_array($actionResult) || !$actionResult["noSave"])
      $obj->save();

    if ($obj)
      $result = $this->mapObject($obj);

    $done = call_user_func([$entityClass, "getApiDesc"]);
    return jsonView($done ?? ['success' => true]);
  }

  public function download($entity, $id)
  {
    $entityClass = "\\Site\\Entity\\$entity";
    $fileField = $entityClass::$fileField;
    if (property_exists($entityClass, "folderField"))
      $folderField = $entityClass::$folderField;

    $obj = $entityClass::get($id);
    if (!$obj)
      throw new NotFoundException("");
    if ($folderField)
      $filePath = $entityClass::$directory . "/" . $obj->$folderField . "/" . $obj->$fileField;
    else
      $filePath = $entityClass::$directory . "/" . $obj->$fileField;

    $data = $this->getInputData();
    $currentUser = $this->getCurrentUser($data);
    $lastLogin = UserLogin::select()->where(["userId" => $currentUser->id])->orderBy(["id" => "DESC"])->first();
    if ($lastLogin) {
      $lastLogin->transferred += filesize($filePath);
      $lastLogin->save();
    }
    header("Location: /$filePath");
    die();
  }
}
