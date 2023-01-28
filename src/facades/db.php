<?php

use Common\Database\Connection;

function mydb($connection='default') {
  return Connection::get($connection);
}
