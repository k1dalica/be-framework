<?php

namespace Common\Exception;
use Common\Util\Router;

class Exception extends \Exception {

  public $message = null;
  public $code = null;

  public function __construct($message=null, $code = 0) {
    $this->$message = $message;
    $this->$code = $code;
  }
  
}
