<?php

namespace Common\Database;
use Common\Exception\InternalException;

class Helpers {

  static public function makeInsert($data) {
    $fields=[];
    $values=[];
    foreach($data as $field=>$value) {
      $fields[]="$field";
      $values[]=":$field";
    }
    return [implode(", ", $fields), implode(", ", $values)];
  }
  
  static public function makeUpdate($data) {
    $fields=[];
    foreach($data as $field=>$value) {
      $fields[]="$field=:$field";
    }
    return implode(", ", $fields);
  }
  
  static public function makeWhere($data) {
    $fields=[];
    $vals=[];
    foreach($data as $field=>$value) {
      $paramField=str_replace(".", "_", $field);
      if (is_null($value)) {
        $fields[]="$field IS NULL";
      } elseif (is_array($value)) {
        $actualField=$field;
        if (count($value)==3)
          $actualField=$value[2];
        if ($value[0]=="IS NOT NULL") {
          $fields[]="$actualField IS NOT NULL";
        } elseif ($value[0]=="IS NULL") {
          $fields[]="$actualField IS NULL";
        } elseif (count($value) > 1 && $value[1]=="IN") {
          $fields[]="$actualField IN ($value[0])";
        } elseif (count($value) > 1 && $value[1]=="NOT IN") {
          $fields[]="$actualField NOT IN ($value[0])";
        } elseif (count($value)==1 || $value[1]=="NOPARAM") {
          $fields[]="$actualField $value[0]";
        } else {
          $fields[]="$actualField $value[0] :$paramField";
          $vals[$paramField]=$value[1];
        }
      } else {
        $fields[]="$field = :$paramField";
        $vals[$paramField]=$value;
      }
    }
    return [implode(" AND ", $fields), $vals];
  }
  
  static public function makeOrderBy($data) {
    $order=[];
    foreach($data as $field=>$direction)
      $order[]="$field $direction";
    return implode(", ", $order);
  }
  
  static public function makeGroupBy($data) {
    return implode(", ", $data);
  }
  
  static public function makeSelect($data) {
    $select=[];
    foreach($data as $field=>$as) {
      if (is_numeric($field))
        $select[]="$as";
      else
        $select[]="$field AS $as";
    }
    return implode(", ", $select);
  }

}
