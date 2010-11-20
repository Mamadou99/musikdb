<ul class="playlists">
	<?php $firstrun=true; foreach($playlists as $playlist): ?>
	<li<?php if($firstrun): ?> class="selected"<?php endif; ?>>
		<span class="id"><?php echo $playlist->id ?></span>
		<a href="#"><?php echo $playlist->name ?></a>
	</li>
	<?php $firstrun=false; endforeach; ?>
</ul>