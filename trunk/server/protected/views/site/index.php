<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/css/welcome.css");

?>

<div id="welcome" class="centerscreen">
	<h1>Welcome</h1>
	<div id="menu">
		<p><a class="button" href="<?php echo Yii::app()->request->baseUrl.'/backend.php' ?>">Administration</a></p>
	</div>
	<p class="secondary_menu"><a href="<?php echo Yii::app()->createUrl('/site/logout'); ?>">Logout</a></p>
</div>