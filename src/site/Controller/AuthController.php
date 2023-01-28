<?php

namespace Site\Controller;

use Site\Entity\User;
use Common\Exception\UnauthorizedException;

class AuthController extends Controller {

  public function login() {
    $data = $this->getInputData();

    $result = (array) $this->doLogin($data);

    if ($result['success']) {
      return jsonView([
        'user' => $result['user']->getApiObject(),
        'token' => $result['token'],
      ]);
    }

    return jsonView($result, 400);
  }

  private function doLogin($data)
  {
    $user = null;
    $email = isset($data['email']) ? $data['email'] : null;
    $password = isset($data['password']) ? $data['password'] : null;

    if (!$email || !$password) {
      return [
        'success' => false,
        'error' => 'Missing required data',
      ];
    }

    $user = User::select()->where(['email' => $email])->first();

    if (!$user) {
      return [
        'success' => false,
        'error' => 'User doesn\'t exist!'
      ];
    }

    $result = ['success' => false];

    if (!$user->activatedAt) {
      $result['error'] = 'Account is not active. Please verify your account via email!';
    } else {
      $token = $user->login(@$password);

      if (!isset($token)) {
        $result['error'] = 'Wrong password!';
      } else {
        $result = [
          'success' => true,
          'token' => $token,
          'user' => $user
        ];

        $user->save();
      }
    }

    return $result;
  }

  public function logout()
  {
    if (!$this->user) throw new UnauthorizedException();

    $user = User::get($this->user->id);
    $user->logout();

    return jsonView(['success' => true]);
  }

  public function me() {
    if (!$this->user) throw new UnauthorizedException();

    return jsonView($this->user);
  }
}
