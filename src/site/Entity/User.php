<?php

namespace Site\Entity;

use Common\Database\ActiveRecord;
use Site\API\RestApiMap;
use Site\API\RestApiMapHelper;

class User extends ActiveRecord implements RestApiMap
{

  use RestApiMapHelper {
    updateApiObject as protected intUpdateApiObject;
  }

  static public $tableName = 'users';
  static public $connection = 'default';

  static public $directory = 'files/avatars';
  static public $fileField = 'avatar';


  static public $apiMap = [
    'name' => [],
    'avatar' => ['basepath' => 'files/avatars'],
    'email' => [],
    'companies' => [
      'type' => 'relmany',
      'field' => 'userId',
      'target' => Company::class,
    ],
    'password' => ['type' => 'password', 'setter' => 'setPassword'],
    'token' => [],
    'activatedAt' => [],
    'createdAt' => [],
  ];

  public function transform($data)
  {
    return [
      'id' => $data->id,
      'name' => $data->name,
      'email' => $data->email,
      'token' => $data->token,
      'activatedAt' => $data->activatedAt,
      'createdAt' => $data->createdAt,
    ];
  }

  public function updateApiObject($data)
  {
    return $this->intUpdateApiObject($data);
  }

  // public function actionWelcomeEmail()
  // {
  //   $email = new Email();
  //   $data = ['item' => $this];
  //   $data["system"] = $system;
  //   $data['logo'] = $system->mailLogo ? $system->url . '/' . $system->mailLogo : null;
  //   $email->Subject = "Welcome to " . $system->dbaName;
  //   $email->addAddress($this->email);
  //   $email->htmlView("site:Email/WelcomeEmail.html", $data)->textView("site:Email/WelcomeEmail.txt", $data);
  //   $email->send();
  //   return true;
  // }

  public function setPassword($password)
  {
    if ($password) $this->password = password_hash($password, PASSWORD_DEFAULT);
  }

  public function login($password)
  {
    if (password_verify($password, $this->password)) {
      $this->token = bin2hex(random_bytes(20));
      $this->save();
      return $this->token;
    }

    return null;
  }

  public function logout() {
    $this->toke = null;
    $this->save();
    return true;
  }

  static public function getCurrentUser($token)
  {
    $user = static::select()->where(["token" => $token])->first();
    return $user ?? null;
  }
}
