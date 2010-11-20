<a href="#" rel="<?php echo Helpers::encodeUrl($track->file->relpath) ?>">
	<span class="id"><?php echo $track->id ?></span>
	<span class="length"><?php echo Helpers::secToMinSec($track->file->length) ?></span>
	<span class="artist"><?php echo $track->artist->name ?></span> &ndash;
	<span class="title"><?php echo $track->name ?></span>
</a>