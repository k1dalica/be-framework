<?php

namespace Site\Controller;

use Site\Entity\User;
use Site\Controller\AuthController;

class Controller {
  
  protected $data = null;
  protected $user = null;

  public function __construct() {
    $this->data = $this->getCommon();
    $this->user = $this->data['user'];
  }

  protected function mapObject($obj) {
    return $obj->getApiObject();
  }

  static public function getCurrentUser($data)
  {
    $headers = apache_request_headers();
    $token = isset($headers['Authorization']) ? $headers['Authorization'] : $data["token"] ?? null;
    if (!$token) return null;
    $token = str_replace("Bearer ", "", $token);
    return User::getCurrentUser($token);
  }
  
  protected function getInputData()
  {
    if (count($_POST) || count($_FILES))
      return $_POST;
    if (!in_array($_SERVER["REQUEST_METHOD"], ["POST", "PUT", "PATCH"]))
      return $_GET;
    return json_decode(file_get_contents("php://input"), true);
  }

  protected function getCommon() {
    $data=[];
    $data["user"] = $this->getCurrentUser([]);
    if ($data["user"]) unset($data["user"]->password);

    return $data;
  }
  
}
