<?php

$backend=dirname(dirname(__FILE__));
$frontend=dirname($backend);
Yii::setPathOfAlias('backend', $backend);

return CMap::mergeArray(
	require($frontend.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'main.php'),
	array(
	    'basePath' => $frontend,

	    'controllerPath' => $backend.DIRECTORY_SEPARATOR.'controllers',
	    'viewPath' => $backend.DIRECTORY_SEPARATOR.'views',
	    'runtimePath' => $backend.DIRECTORY_SEPARATOR.'runtime',

	    'import' => array(
	        'backend.models.*',
	        'backend.components.*',
	        'application.models.*',
	        'application.components.*',
	    ),
	)
);