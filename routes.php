<?php
return [
  'me' => ['method' => 'POST', 'path' => 'me', 'ctl' => 'AuthController:me'],
  'login' => ['method' => 'POST', 'path' => 'login', 'ctl' => 'AuthController:login'],
  'logout' => ['method' => 'GET', 'path' => 'logout', 'ctl' => 'AuthController:logout'],

  'migrate' => ['method' => 'GET', 'path' => 'migrate', 'ctl' => 'MigrationController:index'],
  
  'model_list' => ['method' => 'GET', 'path' => 'model/:entity', 'ctl' => 'API:list'],
  'model_get' => ['method' => 'GET', 'path' => 'model/:entity/:id', 'ctl' => 'API:get'],
  'model_update' => ['method' => 'PUT', 'path' => 'model/:entity/:id', 'ctl' => 'API:update'],
  'model_delete' => ['method' => 'DELETE', 'path' => 'model/:entity/:id', 'ctl' => 'API:delete'],
  'model_create' => ['method' => 'POST', 'path' => 'model/:entity', 'ctl' => 'API:create'],
  'model_upload' => ['method' => 'POST', 'path' => 'model/:entity/:id', 'ctl' => 'API:upload'],
  'model_action' => ['method' => 'POST', 'path' => 'model/:entity/:id/:action', 'ctl' => 'API:action'],
  'download' => ['method' => 'GET', 'path' => 'download/:entity/:id', 'ctl' => 'API:download'],
];
