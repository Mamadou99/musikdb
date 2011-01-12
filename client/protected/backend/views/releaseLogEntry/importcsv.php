<?php
$this->breadcrumbs=array(
	'Release Log Entries'=>array('index'),
	'Import Releases (CSV)',
);

$this->menu=array(
	array('label'=>'Create ReleaseLogEntry', 'url'=>array('create')),
	array('label'=>'Manage ReleaseLogEntry', 'url'=>array('admin')),
);

?>

<h1>Import Release Log Entries (CSV)</h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'csv-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php if($step==1 || $step==2): ?>
		<p class="note">Fields with <span class="required">*</span> are required.</p>
		<p>The table must have the following structure:</p>
		<ul>
			<li>Type <em>(Album|VA)</em></li>
			<li>Artist <em>(only when Type == Album)</em></li>
			<li>Release Title</li>
			<li>User ID</li>
			<li>Date <em>(DD.MM.YYYY)</em></li>
			<li>Average Bitrate <em>(optional)</em></li>
			<li>MusicBrainz Album ID <em>(optional)</em></li>
		</ul>

		<?php echo $form->errorSummary($model, '<p><strong>Please note the following errors or conflicts.</strong></p>'); ?>

		<?php if(count($taggingDeleteScript)): ?>
		<h2>Tagging Delete Script</h2>
		<div class="code">
			#!/bin/sh<br /><br />
			<br />
			# TAGGING DELETE SCRIPT<br />
			# <?php echo date('Y-m-d H:i:s')  ?><br />
			<br />
			<?php foreach($taggingDeleteScript as $delete_cmd): ?>
			<?php echo $delete_cmd ?><br />
			<?php endforeach; ?>
		</div>
		<div class="row">
			<?php echo $form->checkBox($model, 'execTagging'); ?>
			<?php echo $form->labelEx($model,'execTagging'); ?>
		</div>
		<?php endif; ?>

		<?php if(count($collectionDeleteScript)): ?>
		<h2>Collection Delete Script</h2>
		<div class="code">
			#!/bin/sh<br /><br />
			<br />
			# COLLECTION DELETE SCRIPT<br />
			# <?php echo date('Y-m-d H:i:s')  ?><br />
			<br />
			<?php foreach($collectionDeleteScript as $delete_cmd): ?>
			<?php echo $delete_cmd ?><br />
			<?php endforeach; ?>
		</div>
		<div class="row">
			<?php echo $form->checkBox($model, 'execCollection'); ?>
			<?php echo $form->labelEx($model,'execCollection'); ?>
		</div>
		<?php endif; ?>

		<h2>Release List</h2>
		<div class="row">
			<?php echo $form->labelEx($model,'input'); ?>
			<?php if($disableInput): ?>
				<?php echo $form->textArea($model, 'input', array('rows' => 20, 'cols' => 80,
					'class' => 'readonly', 'readonly' => 'readonly')); ?>
			<?php else:?>
				<?php echo $form->textArea($model, 'input', array('rows' => 20, 'cols' => 80)); ?>
			<?php endif;?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton('Process'); ?>
		</div>
	<?php endif; ?>

	<?php if($step==3): ?>
		<p>Please check the data and hit &quot;Save&quot; to confirm (at the bottom of the page).</p>

		<table>
			<tr>
				<th>Type</th>
				<th>Artist</th>
				<th>Release Title</th>
				<th>User ID</th>
				<th>Date</th>
				<th>Avg. Bitrate</th>
				<th>MusicBrainz Album ID</th>
			</tr>
			<?php foreach($releases as $release): ?>
			<tr>
				<td><?php echo CHtml::encode($release->type) ?></td>
				<td><?php echo CHtml::encode($release->artist) ?></td>
				<td><?php echo CHtml::encode($release->title) ?></td>
				<td><?php echo CHtml::encode($release->user_id) ?></td>
				<td><?php echo CHtml::encode($release->date) ?></td>
				<td><?php echo CHtml::encode($release->avg_bitrate) ?></td>
				<td><?php echo CHtml::encode($release->musicbrainz_albumid) ?></td>
			</tr>
			<?php endforeach; ?>
		</table>

		<?php echo $form->hiddenField($model, 'input'); ?>
		<?php echo $form->hiddenField($model, 'execTagging'); ?>
		<?php echo CHtml::hiddenField('sure',1); ?>
		<?php echo CHtml::submitButton('Save'); ?>
	<?php endif; ?>

<?php $this->endWidget(); ?>

</div><!-- form -->