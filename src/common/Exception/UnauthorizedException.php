<?php

namespace Common\Exception;
use Common\Exception\Exception;

class UnauthorizedException extends Exception {

  public function __construct($message = "Unauthorized", $code = 401) {
    $this->message = $message;
    $this->code = $code;
  }
  
}
