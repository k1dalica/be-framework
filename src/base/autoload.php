<?php

// PSR-4 conforming class autoloader

// loading main application classes
spl_autoload_register(function($class) {
  $namespaces=config()["namespaces"];

  foreach($namespaces as $prefix=>$baseDir) {
    if (strncmp($prefix, $class, strlen($prefix))!==0)
      continue;
    $relativeClass=substr($class, strlen($prefix));
    $file=$baseDir.str_replace('\\', '/', $relativeClass).'.php';
    if (file_exists($file))
      require_once($file);
  }
});

foreach(config()["autoloaders"] as $autoloader) {
  include_once($autoloader);
}
unset($autoloader);

