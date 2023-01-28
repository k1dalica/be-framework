<?php

namespace Common\Database;
use Common\Exception\InternalException;

class Builder {
  
  protected $connection;
  protected $select;
  protected $from;
  protected $where;
  protected $groupBy;
  protected $orderBy;
  protected $limit;

  public function __construct($connection, $select=null) {
    $this->connection=$connection;
    $this->select=[];
    if ($select)
      $this->select($select);
    $this->from=[];
    $this->where=[];
    $this->groupBy=[];
    $this->orderBy=[];
  }
  
  public function select($select) {
    $this->select=$select;
    return $this;
  }
  
  public function from($from) {
    $this->from=$from;
    return $this;
  }
  
  public function where($where) {
    $this->where=$where;
    return $this;
  }
  
  public function groupBy($groupBy) {
    $this->groupBy=$groupBy;
    return $this;
  }
  
  public function orderBy($orderBy) {
    $this->orderBy=$orderBy;
    return $this;
  }
  
  public function limit($count, $start=null) {
    if ($start)
      $this->limit=[$count, $start];
    else
      $this->limit=[$count];
    return $this;
  }

  public function get() {
    if (is_array($this->select))
      $selectQ=Helpers::makeSelect($this->select);
    else
      $selectQ=$this->select;
    if (is_array($this->from))
      $fromQ=Helpers::makeSelect($this->from);
    else
      $fromQ=$this->from;
    if (is_array($this->where))
      list($whereQ, $whereVal)=Helpers::makeWhere($this->where);
    else {
      $whereQ=$this->where;
      $whereVal=[];
    }
    $limit="";
    if (is_array($this->limit))
      $limit=(count($this->limit)>1?$this->limit[1].",":"").$this->limit[0];
    
    $groupByQ=Helpers::makeGroupBy($this->groupBy);
    $orderByQ=Helpers::makeOrderBy($this->orderBy);
    $query="SELECT $selectQ FROM $fromQ";
    if ($whereQ)
      $query.=" WHERE $whereQ";
    if ($groupByQ)
      $query.=" GROUP BY $groupByQ";
    if ($orderByQ)
      $query.=" ORDER BY $orderByQ";
    if ($limit)
      $query.=" LIMIT $limit";
    return $this->connection->query($query, $whereVal);
  }
  
  
  public function first() {
    if (!$this->limit)
      $this->limit(1);
    $result=$this->get();
    if (is_array($result))
      return $result[0]??null;
    else
      return $result;
  }
  
  public function scalar() {
    $result=$this->first();
    if (is_array($result))
      return array_shift($result);
    else
      return $result;
  }

}

