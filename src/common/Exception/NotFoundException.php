<?php

namespace Common\Exception;

use Common\Util\Router;
use Common\Exception\Exception;

class NotFoundException extends Exception {

  public function __construct($message = 'Not Found', $code = 404) {
    $this->message = $message;
    $this->code = $code;
  }
  
}
