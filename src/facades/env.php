<?php

$environmentFileData=[];
function envValLoad() {
  global $environmentFileData;
  if (file_exists(".env"))
    foreach(file(".env") as $line) {
      $line=trim($line);
      if (!$line || $line[0]=="#")
        continue;
      list($name, $value)=explode("=", $line, 2);
      $name=trim($name);
      $value=trim($value);
      $value=preg_replace("/^\"(.*)\"$/", "\\1", $value);
      $environmentFileData[$name]=$value;
    }
  else
    $environmentFileData["MISSING_FILE"]=true;
}

function envVal($name, $default) {
  global $environmentFileData;
  if (!count($environmentFileData))
    envValLoad();
  return $environmentFileData[$name]??$default;
}
