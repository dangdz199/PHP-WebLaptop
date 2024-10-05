<?php
// init.php

define('ROOT_PATH',  __DIR__);

define('INCLUDES', get_include_path().PATH_SEPARATOR . __DIR__ . '/includes/');

define('CONTROLLERS', get_include_path().PATH_SEPARATOR . __DIR__ . '/controllers/');

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ );
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/includes');


$baseURL = 'http://localhost/PhamDucDang_2230140008/';

include_once 'db.php';
