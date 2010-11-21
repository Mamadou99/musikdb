	<ul class="jqueryFileTree" style="display:none;">

	<?php foreach($model->contents as $entry): ?>

		<?php if($entry->type=='dir'): ?>
		<li class="directory collapsed">

			<a href="#" rel="<?php echo Helpers::encodeUrl(ltrim($model->directory.$entry->name.'/','/')) ?>">
				<?php echo $entry->name ?>
			</a>
		</li>

		<?php elseif($entry->type=='download'): ?>
		<li class="download ext_<?php echo $entry->ext ?>">

			<a target="_blank" href="<?php echo Yii::app()->params['vars']['serverBaseUrl'].
				'/index.php/download/do/dir/'.Helpers::encodeUrl($model->directory).'/accesstoken/'.$accesstoken ?>">
				<?php echo $entry->name; ?>
			</a>

		</li>

		<?php else: ?>
		<li class="<?php echo $entry->type ?> ext_<?php echo $entry->ext ?>">

			<a href="#" rel="<?php echo Helpers::encodeUrl(ltrim(trim($model->directory,'/').'/'.$entry->name,'/')) ?>">
				<span class="filename"><?php echo $entry->name; ?></span>
			</a>

		</li>
		<?php endif; ?>

	<?php endforeach; ?>

	</ul>