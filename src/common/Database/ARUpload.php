<?php

namespace Common\Database;

trait ARUpload
{

  public function uploadFile($file, $fileField = null)
  {
    if (!$file || !$file["name"])
      return false;
    $targetFilename = basename($file["name"]);
    $i = 0;
    $pathParts = pathinfo($targetFilename);
    while (file_exists(self::$directory . "/" . $targetFilename)) {
      $i++;
      $targetFilename = $pathParts["filename"] . "-" . $i . "." . $pathParts["extension"];
    }
    move_uploaded_file($file["tmp_name"], self::$directory . "/" . $targetFilename);
    if ($fileField === null)
      $fileField = self::$fileField;
    $this->$fileField = basename($targetFilename);
    return true;
  }
}
