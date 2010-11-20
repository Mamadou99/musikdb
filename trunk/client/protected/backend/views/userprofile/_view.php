<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode($data->user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('server_id')); ?>:</b>
	<?php echo CHtml::encode($data->server_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('crossfadeTime')); ?>:</b>
	<?php echo CHtml::encode($data->crossfadeTime); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('transcodingBitrate')); ?>:</b>
	<?php echo CHtml::encode($data->transcodingBitrate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('openPopup')); ?>:</b>
	<?php echo CHtml::encode($data->openPopup); ?>
	<br />


</div>