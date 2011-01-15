<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'userprofile-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="scroll" id="settings">
	<div class="form">

		<?php echo $form->errorSummary($model); ?>

		<fieldset>
			<legend>Streaming Server</legend>

			<div class="row">
				<?php echo $form->labelEx($model,'server_id'); ?>
				<?php echo $form->dropDownList($model,'server_id',
						CHtml::listData(Server::model()->findAll(), 'id', 'name')); ?>
				<?php echo $form->error($model,'server_id'); ?>
			</div>
		</fieldset>

		<fieldset>
			<legend>Playback</legend>

			<div class="row">
				<?php echo $form->labelEx($model,'crossfadeTime'); ?>
				<?php echo $form->textField($model,'crossfadeTime'); ?>
				<?php echo $form->error($model,'crossfadeTime'); ?>
			</div>

			<div class="row">
				<?php echo $form->labelEx($model,'transcodingBitrate'); ?>
				<?php echo $form->dropDownList($model,'transcodingBitrate',
						CHtml::listData(Yii::app()->params['transcodingBitrates'], 'ab', 'desc')); ?>
				<?php echo $form->error($model,'transcodingBitrate'); ?>
			</div>

			<div class="row">
				<?php echo $form->labelEx($model,'alwaysTranscode'); ?>
				<?php echo $form->checkBox($model,'alwaysTranscode'); ?>
				<?php echo $form->error($model,'alwaysTranscode'); ?>
				(When turned off, transcoding is only used to make non-mp3 files streamable.)
			</div>

		</fieldset>

		<fieldset>
			<legend>Application</legend>

			<div class="row">
				<?php echo $form->labelEx($model,'openPopup'); ?>
				<?php echo $form->checkBox($model,'openPopup'); ?>
				<?php echo $form->error($model,'openPopup'); ?>
			</div>
		</fieldset>

		<div class="row">
			<p id="saveStatus"></p>
		</div>

	</div><!-- form -->
</div>
<div class="bar">
	<ul>
		<li><?php echo CHtml::ajaxSubmitButton('Save',Yii::app()->createUrl('userprofile/update'),
			array('update'=>'#saveStatus')); ?></li>
	</ul>
</div>

<?php $this->endWidget(); ?>