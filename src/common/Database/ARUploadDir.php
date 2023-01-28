<?php

namespace Common\Database;

trait ARUploadDir
{

  public function uploadFile($file)
  {
    if (!$file || !$file["name"])
      return false;
    $targetFilename = basename($file["name"]);
    $i = 0;
    $pathParts = pathinfo($targetFilename);
    $folderField = self::$folderField;
    $directory = self::$directory . "/" . $this->$folderField;
    if (!file_exists($directory)) {
      mkdir($directory);
      chmod($directory, 0777);
    }
    while (file_exists($directory . "/" . $targetFilename)) {
      $i++;
      $targetFilename = $pathParts["filename"] . "-" . $i . "." . $pathParts["extension"];
    }
    move_uploaded_file($file["tmp_name"], $directory . "/" . $targetFilename);
    $fileField = self::$fileField;
    $this->$fileField = basename($targetFilename);
    return true;
  }
}
