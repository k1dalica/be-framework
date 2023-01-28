<?php

namespace Common\Util;

class Redirect {

  public function __construct($url) {
    $this->url=$url;
  }
  
  public function render() {
    return new Response("", [
      "code"=>302,
      "headers"=>[  
        "Location"=>$this->url,
      ],
    ]);
  }

}
