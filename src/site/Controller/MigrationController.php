<?php

namespace Site\Controller;
use Site\Entity\Migration;
use Common\Database\ActiveRecord;

class MigrationController extends Controller {

  public function index() {
    $files = scandir('./migrations');

    $migrations = Migration::all();

    $formattedMigrations = [];
    foreach($migrations as $migration) $formattedMigrations[] = $migration->name;

    $doneMigrations = [];
    foreach ($files as $fileName) {
      if (in_array($fileName, $formattedMigrations) || in_array($fileName, [".", "..", ".gitkeep"])) continue;


      $commands = file_get_contents('./migrations/' . $fileName);

      //delete comments
      $lines = explode("\n", $commands);
      $commands = '';
      foreach($lines as $line){
        $line = trim($line);
        if( $line && !$this->startsWith($line, '--') ) {
          $commands .= $line . "\n";
        }
      }

      //convert to array
      $commands = explode(";", $commands);

      //run commands
      foreach($commands as $command) {
        if(trim($command)) {
          ActiveRecord::getConnection()->query($command);
        }
      }

      $item = new Migration();
      $item->name = $fileName;
      $item->save();

      $doneMigrations[] = $fileName;
    }
    

    return jsonView($doneMigrations);
  }

  function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }
}
