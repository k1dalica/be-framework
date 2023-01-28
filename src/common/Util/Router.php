<?php

namespace Common\Util;
use Common\Exception\NotFoundException;
use Common\Exception\InternalException;

class Router {
  
  protected $routes=[];
  public $currentRequest=null;
  
  protected static $instance;
  
  public static function get() {
    if (!self::$instance)
      self::$instance=new Router(config()["routes"]);
    return self::$instance;
  }
  
  public function __construct($routes) {
    $this->routes=$routes;
  }
  

  public function resolve($requestData) {
    $this->currentRequest = $cr = new Request($requestData);
    $cr->name=null;
    $cr->params=[];
    $cr->defaults=[];
    
    foreach($this->routes as $routeName=>$route) {
      $routeKeys=[];
      $path = 'api/' . $route["path"];
      preg_match_all("/:[^\/]+/", $path, $routeParams);
      $path = preg_replace("/::[^\/]+/", "(.*)", $path);
      $path = preg_replace("/:[^\/]+/", "([^/]*)", $path);
      if ($routeParams[0]) {
        foreach($routeParams[0] as $param) {
          $key=str_replace(":", "", $param);
          $routeKeys[]=$key;
        }
      }
      if (preg_match("%^$path$%", $cr->path, $matches)) {
        if (isset($route["method"]) && $route["method"])
          if ($route["method"]!=$cr->method)
            continue;
        $cr->name=$routeName;
        $cr->defaults=$route["defaults"]??[];
        foreach($routeKeys as $rkIndex=>$routeKey) {
          if (isset($matches[$rkIndex+1]))
            $cr->params[$routeKey]=$matches[$rkIndex+1];
        }
        return $this->callController($cr->name, $cr->params);
      }
    }
    throw new NotFoundException("Route not found.");
  }
  
  public function callController($name, $params) {
    $route=$this->routes[$name];
    $controller=explode(":", $route["ctl"]);
    $ctlClass="\\Site\\Controller\\$controller[0]";
    $ctlMethod=$controller[1];
    if (!class_exists($ctlClass))
      throw new InternalException("Controller $ctlClass not found");
    if (!method_exists($ctlClass, $ctlMethod))
      throw new InternalException("Controller $ctlClass has no public method $ctlMethod");
    
    $reflMethod=new \ReflectionMethod($ctlClass, $ctlMethod);
    $callArr=[];
    foreach($reflMethod->getParameters() as $parameter)
      $callArr[]=$params[$parameter->getName()]??null;
    $controller=new $ctlClass($this->currentRequest);
    return call_user_func_array([$controller, $ctlMethod], $callArr);
  }
  
  public function make($name, $args=null, $abs=false) {
    if (!isset($this->routes[$name]))
      throw new InternalException("No route named $name found");
    $route=$this->routes[$name];
    $path=$route["path"];
    $targetArgs=$args??[];
    $targetArgs+=$route["def"]??[];
    foreach($targetArgs as $argKey=>$argVal) {
      $path=preg_replace("/:$argKey(\/)|:$argKey($)/", $argVal."\\1", $path);
    }
    if ($abs)
      return $this->currentRequest->absRoot.$path;
    else
      return $path;
  }
}
