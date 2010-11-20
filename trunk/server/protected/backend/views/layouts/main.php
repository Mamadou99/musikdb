<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<title>Administration - <?php echo Yii::app()->name.' '.Yii::app()->version ?></title>
</head>

<body>

<div class="container" id="page">

<p>You are logged on as <strong><?php echo Yii::app()->user->name ?></strong> |
<a href="<?php echo Yii::app()->request->baseUrl.'/'; ?>">Leave Backend</a></p>

<ul id="menu">
	<li><?php echo CHtml::link('Users',array('user/admin')); ?></li>
</ul>

<?php echo $content; ?>

</div>

</body>
</html>