<?php

namespace Common\Database;
use Common\Exception\InternalException;

class Connection {

  protected static $connections=[];
  
  public static function get($connection="default") {
    if (!isset(self::$connections[$connection])) {
      $config=config()["db"];
      if (!isset($config[$connection]))
        throw new InternalException("No database configuration for $configuration");
      self::$connections[$connection]=new Connection($config[$connection]);
    }
    return self::$connections[$connection];
  }
  
  protected $connection;
  protected $columns;
  
  public function __construct($params) {
    $dsn="mysql:host=$params[host];dbname=$params[name];charset=utf8";
    $options=[
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
//        \PDO::ATTR_EMULATE_PREPARES   => false,
        \PDO::ATTR_EMULATE_PREPARES   => true,
    ];
    try {
      $this->connection=new \PDO($dsn, $params["user"], $params["pass"], $options);
    } catch (\PDOException $e) {
      throw new InternalException("Database connection failed");
    }
  }
  
  public function query($query, $args=[]) {
    //var_dump($query, $args);
    $stmt=$this->connection->prepare($query);
    if (!$stmt->execute($args))
      return false;
    // if nothing in result;
    if ($stmt->columnCount()==0)
      return true;
    $this->columns=[];
    for($i=0;$i<$stmt->columnCount();$i++)
      $this->columns[$i]=$stmt->getColumnMeta($i);
    return $stmt->fetchAll();
  }
  
  public function first($query, $args=[]) {
    $result=$this->query($quest, $args);
    if (is_array($result))
      return $result[0]??null;
    else
      return $result;
  }
  
  public function getColumns() {
    return $this->columns;
  }
  
  public function insertId() {
    return $this->connection->lastInsertId();
  }
  
  public function select($fields) {
    return new Builder($this, $fields);
  }
  
  public function insert($table, $data) {
    list($fields, $values)=Helpers::makeInsert($data);
    if ($this->query("INSERT INTO `$table` ($fields) VALUES($values)", $data)) {
      $insertId=$this->insertId();
      if ($insertId)
        return $insertId;
      else
        return true;
    } else
      return false;
  }
  
  public function update($table, $data, $where) {
    $fields=Helpers::makeUpdate($data);
    list($whereQ, $whereVal)=Helpers::makeWhere($where);
    if (!$fields)
      return true;
    return $this->query("UPDATE `$table` SET $fields WHERE $whereQ", $data+$whereVal);
  }
  
  public function delete($table, $where) {
    list($whereQ, $whereVal)=Helpers::makeWhere($where);
    return $this->query("DELETE FROM `$table` WHERE $whereQ", $whereVal);
  }
  
}
