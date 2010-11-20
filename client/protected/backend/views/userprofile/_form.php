<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'userprofile-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'server_id'); ?>
		<?php echo $form->textField($model,'server_id'); ?>
		<?php echo $form->error($model,'server_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'crossfadeTime'); ?>
		<?php echo $form->textField($model,'crossfadeTime'); ?>
		<?php echo $form->error($model,'crossfadeTime'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'transcodingBitrate'); ?>
		<?php echo $form->textField($model,'transcodingBitrate'); ?>
		<?php echo $form->error($model,'transcodingBitrate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'openPopup'); ?>
		<?php echo $form->textField($model,'openPopup'); ?>
		<?php echo $form->error($model,'openPopup'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->