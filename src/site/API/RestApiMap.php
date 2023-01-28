<?php
namespace Site\API;

interface RestApiMap {

  public function getApiObject();
  public function updateApiObject($data);
  public function validateData($data);

}

