<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'server_id'); ?>
		<?php echo $form->textField($model,'server_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'crossfadeTime'); ?>
		<?php echo $form->textField($model,'crossfadeTime'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'transcodingBitrate'); ?>
		<?php echo $form->textField($model,'transcodingBitrate'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'openPopup'); ?>
		<?php echo $form->textField($model,'openPopup'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->