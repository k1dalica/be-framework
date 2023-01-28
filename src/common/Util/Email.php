<?php

namespace Common\Util;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

  public $mailer;
  protected $isHtml;

  public function __construct() {
    $mailConfig=config()["mail"];
    $mailAddress = 'TODO@live.com';
    $mailName = 'TODO';
    
    $mailConfig['fromEmail'] = $mailAddress;
    $mailConfig['fromName'] = $mailName;
    
    $this->mailer=new PHPMailer();
    if ($mailConfig["transport"]=="smtp")
      $this->mailer->isSMTP();
    // $this->mailer->SMTPDebug=3;
    if (@$mailConfig["disableverify"])
      $this->mailer->SMTPOptions=array (
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true));
    $this->mailer->Host = $mailConfig["host"];
    if (@$mailConfig["username"])
      $this->mailer->SMTPAuth = true;
    if (@$mailConfig["username"])
      $this->mailer->Username = $mailConfig["username"];
    if (@$mailConfig["password"])
      $this->mailer->Password = $mailConfig["password"];
    if (@$mailConfig["mode"]=="ssl")
      $this->mailer->SMTPSecure = "ssl";
    if (@$mailConfig["mode"]=="starttls")
      $this->mailer->SMTPSecure = "starttls";
    $this->mailer->Port = $mailConfig["port"];
    $this->mailer->CharSet = 'UTF-8';
    $this->mailer->setFrom($mailConfig["fromEmail"], $mailConfig["fromName"]);
  }
  
  public function __call($name, $args) {
    return call_user_func_array([$this->mailer, $name], $args);
  }
  
  
  public function __set($property, $value) {
    $this->mailer->$property=$value;
  }
  
  public function __get($property) {
    return $this->mailer->$property;
  }
  
  public function __isset($property) {
    return isset($this->mailer->$property);
  }
  
  public function htmlView($template, $data) {
    $this->mailer->isHtml(true);
    $this->isHtml=true;
    $this->mailer->Body=view($template, $data)->contents;
    return $this;
  }
  
  public function textView($template, $data) {
    if ($this->isHtml)
      $this->mailer->AltBody=view($template, $data)->contents;
    else
      $this->mailer->Body=view($template, $data)->contents;
    return $this;
  }
  

}
