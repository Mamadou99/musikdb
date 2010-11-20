<?php if(count($tracks)): ?>

<ul class="main">
	<?php foreach($tracks as $track): ?>
	<li class="item">
		<ul>
			<li class="media"><a href="#" rel="<?php echo Helpers::encodeUrl($track->file->relpath) ?>">
			<span class="id"><?php echo $track->id ?></span>
			<span class="length"><?php echo Helpers::secToMinSec($track->file->length) ?></span>
			<span class="artist"><?php echo $track->artist->name ?></span> &ndash;
			<span class="title"><?php echo $track->name ?></span>
			</a></li>
		</ul>
		<span class="info">#<?php echo $track->number ?> on <?php echo $track->release->name ?>
		(<?php echo $track->release->year ?>)</span>
	</li>
	<?php endforeach; ?>
</ul>

<script type="text/javascript">
	refreshDraggables();
</script>

<?php else: ?>

<p>Sorry, no matches.</p>

<?php endif; ?>