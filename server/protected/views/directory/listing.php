	<ul class="jqueryFileTree" style="display:none;">
	
	<?php if($model->upperDir && $model->upperDir !== $model->directory): ?>
		<li><a href="<?php echo Yii::app()->createUrl('/', array(
			'dir'=>$this->encode($this->upperDir))) ?>">[Go up]</a></li>
	<?php endif; ?>
	
	
	<?php foreach($model->contents as $entry): ?>
	
		<?php if($entry['type']=='dir'): ?>
		<li class="directory collapsed">
		
			<a href="#" rel="<?php echo Helpers::encodeUrl(ltrim($model->directory.$entry['name'].'/','/')) ?>">
				<?php echo $entry['name'] ?>
			</a>
		</li>
		
		<?php elseif($entry['type']=='download'): ?>
		<li class="download ext_<?php echo $entry['ext']?>">

			<a target="_blank" href="<?php echo Yii::app()->createUrl('download/do',
					array('dir'=>$model->directory)) ?>">
				<?php echo $entry['name']; ?>
			</a>
		
		</li>		
			
		<?php else: ?>
		<li class="<?php echo $entry['type'] ?> ext_<?php echo $entry['ext']?>">

			<a href="#" rel="<?php echo Helpers::encodeUrl(ltrim(trim($model->directory,'/').'/'.$entry['name'],'/')) ?>">
				<span class="filename"><?php echo $entry['name']; ?></span>
			</a>
		
		</li>
		<?php endif; ?>
		
	<?php endforeach; ?>
	
	</ul>