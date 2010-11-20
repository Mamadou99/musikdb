<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/css/welcome.css");

$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<div id="login" class="centerscreen">
	<h1>Welcome</h1>

<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableAjaxValidation'=>true,
		'htmlOptions'=>array('autocomplete'=>'off'),
	)); ?>

		<div class="row">
			<?php echo $form->labelEx($model,'username'); ?>
			<?php echo $form->textField($model,'username'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password'); ?>
		</div>

		<div class="row submit">
			<?php echo CHtml::submitButton('Login', array('class'=>'button')); ?>
		</div>

		<div class="row errorMessages">
			<?php echo $form->error($model,'username'); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>

	<?php $this->endWidget(); ?>
	</div><!-- form -->
</div>