<?php

namespace Common\Util;

class Request {
  
  public $protocol;
  public $host;
  public $ip;
  public $method;
  public $root;
  public $absRoot;
  public $path;
  public $absPath;
  
  public function __construct($serverData) {
    if (isset($serverData["REQUEST_SCHEME"]))
      $this->protocol=$serverData["REQUEST_SCHEME"];
    elseif (isset($serverData["HTTPS"]) && $serverData["HTTPS"]!=="off")
      $this->protocol="https";
    else
      $this->protocol="http";
    $this->host=$serverData["HTTP_HOST"];
    $this->ip=$serverData["REMOTE_ADDR"];
    $this->method=$serverData["REQUEST_METHOD"];
    $root=preg_replace("/index\\.php$/", "", $serverData["SCRIPT_NAME"]);
    $path=substr($serverData["REQUEST_URI"], strlen($root));
    $path=preg_replace("/\\/$/", "", $path);
    $path=preg_replace("/\\?.*$/", "", $path);
    $this->root=$root;
    $this->absRoot=$this->protocol."://".$this->host.$root;
    $this->path=$path;
    $this->absPath=$this->absRoot.$path;    
  }
  
  
}
