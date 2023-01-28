<?php

namespace Common\Util;


trait FormatterDate {
  
  public static function date($date, $format) {
    if (!$date instanceOf \DateTime)
      $date=new \DateTime($date);
    return $date->format($format);
  }

}
