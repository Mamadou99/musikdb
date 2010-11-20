<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii-1.1.x/framework/yii.php';
$config=dirname(__FILE__).'/protected/backend/config/main.php';

// remove the following lines when in production mode
//defined('YII_DEBUG') or define('YII_DEBUG',true);
//defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
require_once(dirname(__FILE__).'/protected/components/MyApplication.php');
$app = new MyApplication($config);
$app->run();
