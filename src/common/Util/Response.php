<?php

namespace Common\Util;

class Response {

  public $contents;
  public $headers;
  public $code;
  public $cookies;
  
  static protected $responseCodes=[
    "200"=>"OK",
    "201"=>"Created",
    "301"=>"Moved Permanently",
    "302"=>"Found",
    "304"=>"Not Modified",
    "401"=>"Unauthorized",
    "403"=>"Forbidden",
    "404"=>"Not Found",
    "500"=>"Internal Server Error",
    "503"=>"Service Unavailable",
  ];

  public function __construct($contents, $args = []) {
    $this->contents=$contents;
    $this->headers=$args["headers"]??[];
    $this->code = $args["code"] ?? 200;
    $this->cookies=$args["cookies"]??[];
  }
  
  public function __toString() {
    http_response_code($this->code);
    if ($this->code!==null && $this->code!=200 && isset(self::$responseCodes[$this->code]))
      header("HTTP/1.1 {$this->code} ".self::$responseCodes[$this->code], $this->code);
    foreach($this->headers as $headerName=>$headerValues)
      foreach((array)$headerValues as $headerValue)
        header("$headerName: $headerValue");
    foreach($this->cookies as $cookie)
      setcookie($cookie["name"], $cookie["value"]??"", $cookie["expires"]??0, $cookie["path"]??"", $cookie["domain"]??"", $cookie["secure"]??false, $cookie["httponly"]??false);
    return (string)$this->contents;
  }

}
