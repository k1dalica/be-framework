<?php

namespace Common\Util;
use Common\Exception\InternalException;
use Common\Util\Response;

class View {

  protected $template;
  protected $bundle;
  public $extras;
  public $extendsView;
  
  public function __construct($template) {
    list($this->bundle, $this->template)=explode(":", $template);
    $this->extras=[
      "headers"=>[],
      "code"=>null,
      "cookies"=>[],
    ];
  }
  
  public function setCookie($name, $value="", $expires=0, $path="", $domain="", $secure=false, $httponly=false) {
    $this->extras["cookies"][]=[
      "name"=>$name, "value"=>$value, "expires"=>$expires,
      "path"=>$path, "domain"=>$domain,
      "secure"=>$secure, "httponly"=>$httponly
    ];
    return $this;
  }
  
  public function setCode($code) {
    $this->extras["code"]=$code;
    return $this;
  }
  
  public function setHeader($name, $value) {
    if (!isset($this->extras["headers"][$name]))
      $this->extras["headers"][$name]=$value;
    else
      $this->extras["headers"][$name]=array_merge(
        (array)$this->extras["headers"][$name],
        [$value]);
    return $this;
  }
  
  public function render($data=null) {
    $contents=$this->doRender($data);
    return new Response($contents, $this->extras);
  }
  
  public function doRender($_data=null) {

    extract($_data??[]);
    ob_start();
    if (!file_exists(__DIR__."/../../{$this->bundle}/views/{$this->template}.php"))
      throw new InternalException("View {$this->bundle}:{$this->template} doesn't exist");
    include(__DIR__."/../../{$this->bundle}/views/{$this->template}.php");
    $_contents=ob_get_clean();
    if (isset($_extendsView)) {
      unset($_data);
      $_data=get_defined_vars();
      unset($_data["_extendsView"]);
      $_parentView=new View($_extendsView);
      $_contents=$_parentView->doRender($_data);
      foreach($_parentView->extras["headers"] as $_headerName=>$_headerVal) {
        if (isset($this->extras["headers"][$_headerName]))
          $this->extras["headers"][$_headerName]=array_merge(
            (array)$this->extras["headers"][$_headerName],
            (array)$_headerVal);
        else
          $this->extras["headers"][$_headerName]=$_headerVal;
      }
      $this->extras["cookies"]=$_parentView->extras["cookies"]+$this->extras["cookies"];
      if ($_parentView->extras["code"]!==null)
        $this->extras["code"]=$_parentView->extras["code"];
    }
    return $_contents;
  }

}
