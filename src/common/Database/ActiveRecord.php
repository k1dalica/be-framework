<?php

namespace Common\Database;
use Common\Exception\InternalException;

abstract class ActiveRecord {

  static public $tableName="";
  static public $connection=null;
  
  static public function getConnection() {
    return Connection::get(static::$connection??"default");
  }
  
  static public function select() {
    return (new Builder(new static(), "base.*"))->from([static::$tableName=>"base"]);
  }
  
  static public function all($order=null) {
    return static::select()->orderBy($order??["id"=>"ASC"])->get();
  }
  
  static public function get($id) {
    return static::select()->where(["id"=>$id])->first();
  }
  
  static public function query($query, $args=[]) {
    $res=[];
    foreach(static::getConnection()->query($query, $args) as $row)
      $res[]=new static($row);
    return $res;
  }
  
  static public function count() {
    return (new Builder(static::getConnection(), "COUNT(DISTINCT base.id) as count"))->from([static::$tableName=>"base"]);
  }
  
  public function __construct($row=[]) {
    foreach($row as $field=>$value)
      $this->$field=$value;
  }
  
  public function delete() {
    if (isset($this->id) && $this->id*1)
      static::getConnection()->delete(static::$tableName, ["id"=>$this->id]);
  }
  
  public function save() {
    $fields=(array)$this;
    unset($fields["id"]);
    if (isset($this->id) && $this->id*1)
      static::getConnection()->update(static::$tableName, $fields, ["id"=>$this->id]);
    else
      $this->id=static::getConnection()->insert(static::$tableName, $fields);
  }


}
