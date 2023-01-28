<?php

use Common\Util\Router;

function route($name, $args=null, $abs=true) {
  return Router::get()->make($name, $args, $abs);
}

function currentRequest() {
  return Router::get()->currentRequest;
}
