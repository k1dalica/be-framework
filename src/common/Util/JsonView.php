<?php

namespace Common\Util;

class JsonView extends View
{

  public function __construct()
  {
    parent::__construct(":");
  }

  public function render($data = null, $code = 200)
  {
    $this->extras["headers"]["Content-Type"] = "application/json; charset=utf-8";
    $this->extras['code'] = $code;
    return new Response(
      json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
      $this->extras
    );
  }
}
