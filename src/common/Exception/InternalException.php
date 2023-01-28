<?php

namespace Common\Exception;
use Common\Util\Router;
use Common\Exception\Exception;

class InternalException extends Exception {

  public function __construct($message = 'Internal Server Error', $code = 500) {
    $this->message = $message;
    $this->code = $code;
  }
  
}
