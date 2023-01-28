<?php

namespace Common\Util;

class Validator {



  public function validate($requirements, $value) {
    $valid=true;
    foreach(explode(" ", $requirements) as $requirement) {
      if (method_exists($this, $requirement))
        if (!$this->$requirement($value))
          $valid=false;
    }
    return $valid;
  }




  protected function regexValidatorWithNull($regex, $value) {
    if ($value===null || $value==="")
      return true;
    return !!preg_match($regex, $value);
  }

  public function boolean($value) { return !!$value; }
  public function required($value) { return $value!==null && $value!==""; }
  public function password($value) { return true; }
  public function email($value) { return $this->regexValidatorWithNull("/.+@.+\..+/", $value); }
  public function phone($value) { return $this->regexValidatorWithNull("/^[0-9 +-]{1,20}$/", $value); }
  public function url($value) { return $this->regexValidatorWithNull("/^(https?\:\/\/)?.+\..+\/?.*$/", $value); }
  public function number($value) { return $this->regexValidatorWithNull("/^[0-9]+\.?[0-9]*$/", $value); }
  public function numberPercent($value) { return $this->regexValidatorWithNull("/^[0-9%]+\.?[0-9]*$/", $value); }
  public function numberPercentWhole($value) { return $this->regexValidatorWithNull("/^(100|[0-9]{1,2})$/", $value); }
  public function letters($value) { return $this->regexValidatorWithNull("/^[, a-zA-ZÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ]+$/", $value); }
  public function legalName($value) { return $this->regexValidatorWithNull("/^[-, a-zA-Z0-9ÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ]{0,50}$/", $value); }
  public function regnum($value) { return $this->regexValidatorWithNull("/^[a-zA-Z0-9ÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ]{1,20}$/", $value); }
  public function descriptor($value) { return $this->regexValidatorWithNull("/^[ 0-9a-zA-Z]{0,22}$/", $value); }
  public function address($value) { return $this->regexValidatorWithNull("/^[\/#, 0-9a-zA-ZÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ]{0,50}$/", $value); }
  public function city($value) { return $this->regexValidatorWithNull("/^[, a-zA-ZÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ]{0,30}$/", $value); }
  public function postcode($value) { return $this->regexValidatorWithNull("/^[-, 0-9a-zA-ZÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ]{0,20}$/", $value); }
  public function postcode2($value) { return $this->regexValidatorWithNull("/^[0-9a-zA-Z]{3,3}-[0-9a-zA-Z]{3,3}$/", $value); }
  public function state($value) { return $this->regexValidatorWithNull("/^[-–, a-zA-ZÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ]{0,30}$/", $value); }
  public function country($value) { return $this->regexValidatorWithNull("/^[-, a-zA-ZÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ]{0,30}$/", $value); }
  public function num5($value) { return $this->regexValidatorWithNull("/^[0-9.]{0,5}$/", $value); }
  public function num6($value) { return $this->regexValidatorWithNull("/^[0-9.]{0,6}$/", $value); }
  public function num10($value) { return $this->regexValidatorWithNull("/^[0-9.]{0,10}$/", $value); }
  public function num9e($value) { return $this->regexValidatorWithNull("/^[0-9.]{9,9}$/", $value); }
  public function num20($value) { return $this->regexValidatorWithNull("/^[0-9.]{0,20}$/", $value); }
  public function alnum15($value) { return $this->regexValidatorWithNull("/^[ a-zA-ZÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ0-9.]{0,15}$/", $value); }
  public function alnum20($value) { return $this->regexValidatorWithNull("/^[ a-zA-ZÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ0-9.]{0,20}$/", $value); }
  public function alnum30($value) { return $this->regexValidatorWithNull("/^[ a-zA-ZÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ0-9.]{0,30}$/", $value); }
  public function businessType($value) { return $this->regexValidatorWithNull("/^[ .A-Za-zÀàÂâÆæÇçÉéÈèÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ]{0,50}$/", $value); }
  public function money($value) { return $this->regexValidatorWithNull("/^[0-9.]+ [0-9]{3,3}$/", $value); }
  public function currency($value) { return $this->regexValidatorWithNull("/^[0-9]{3,3}$/", $value); }
  public function sum100($value) {
    $sum=0;
    foreach($value as $v)
      $sum+=$v;
    return $sum==100;
  }
  public function char100($value) { return strlen($value)<100; }
  public function char250($value) { return strlen($value)<250; }
  public function char500($value) { return strlen($value)<500; }
  public function char1000($value) { return strlen($value)<1000; }
  public function numval10($value) { return $value*1<10000000000; }
  
  
  

}
