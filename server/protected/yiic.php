<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../../yii-1.1.x/framework/yii.php';
$config=dirname(__FILE__).'/config/console.php';

require_once($yii);

Yii::createConsoleApplication($config)->run();