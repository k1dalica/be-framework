<?php

namespace Common\Util;
use Common\Entity\User;

class Session {

  protected static $instance;
  
  public static function get() {
    if (!self::$instance)
      self::$instance=new Session(config("session"));
    self::$instance->start();
    return self::$instance;
  }
  
  protected $config;
  
  public function __construct($config) {
    $this->config=$config;
  }
  
  public function start() {
    session_start();
  }
  
  public function currentUser() {
    if (!isset($_SESSION["userId"]))
      return null;
    return User::get($_SESSION["userId"]);
  }
  
  public function login() {
    
  }
  
  public function logout() {
    
  }
  
  public function message() {
  }
  
  public function currentMessage() {
    if (isset($_SESSION["message"]))
      return $_SESSION["message"];
    unset($_SESSION["message"]);
  }

}
