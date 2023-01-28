<?php

namespace Site\Entity;

use Common\Database\ActiveRecord;
use Site\API\RestApiMap;
use Site\API\RestApiMapHelper;


class Migration extends ActiveRecord implements RestApiMap {
  use RestApiMapHelper;

  static public $tableName = 'migrations';
  static public $connection = 'default';

  static public $apiMap = [
    'name' => []
  ];
}
