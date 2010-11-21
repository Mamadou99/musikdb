<?php

Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/css/app.css");

Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery-ui-1.8.1.custom.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/css/jquery-ui-1.8.1.custom.css");
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.jplayer.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.lightbox-0.5.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.timer.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/js/jquery.lightbox-0.5.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/js/jplayer.css");

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/main-functions.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/player-functions.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/playlist-functions.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/search-functions.js');

$varsJS="\n";
foreach($vars as $key=>$value) {
	if(is_string($value))
		$varsJS.="\t$key = \"$value\";\n";
	else
		$varsJS.="\t$key = $value;\n";
}

Yii::app()->clientScript->registerScript('vars', $varsJS.'

	accesstoken = "'.$accesstoken.'";

	currentSong = "";
	priPlayerID = "jquery_jplayer";
	secPlayerID = "jquery_jplayer2";

	modified = false;
	queueCount = 0;

	initUi();
');
?>

<div id="header">
	<div id="user">Hello <strong><?php echo CHtml::encode(Yii::app()->user->name); ?></strong>! |
	<?php if(Yii::app()->user->isAdmin()): ?>
		<a href="<?php echo Yii::app()->request->baseUrl.'/backend.php' ?>">Administration</a> |
	<?php endif; ?>
	<a href="<?php echo Yii::app()->createUrl('/site/logout'); ?>">Logout</a>
	</div>

	<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>

	<div id="cover"></div>

	<div id="jquery_jplayer"></div>
	<div id="jquery_jplayer2"></div>

	<div id="jquery_jplayer_elements" class="jp-single-player">
		<div class="jp-interface">
			<ul class="jp-controls">
				<li id="jplayer_play" class="jp-play">play</li>
				<li id="jplayer_pause" class="jp-pause">pause</li>
				<li id="jplayer_stop" class="jp-stop">stop</li>
				<li id="next_song" class="next_song">next</li>

				<li id="jplayer_volume_min" class="jp-volume-min">min volume</li>
				<li id="jplayer_volume_max" class="jp-volume-max">max volume</li>
			</ul>
			<div class="jp-progress">
				<div id="jplayer_load_bar" class="jp-load-bar">
					<div id="jplayer_play_bar" class="jp-play-bar"></div>
				</div>
			</div>
			<div id="jplayer_volume_bar" class="jp-volume-bar">
				<div id="jplayer_volume_bar_value" class="jp-volume-bar-value"></div>
			</div>
			<div id="jplayer_play_time" class="jp-play-time"></div>
			<div id="jplayer_total_time" class="jp-total-time"></div>
		</div>
	</div>

	<div id="jquery_jplayer2_elements" class="jp-single-player" style="display:none">
		<div class="jp-interface">
			<ul class="jp-controls">
				<li id="jplayer_play2" class="jp-play">play</li>
				<li id="jplayer_pause2" class="jp-pause">pause</li>
				<li id="jplayer_stop2" class="jp-stop">stop</li>
				<li id="next_song2" class="next_song">next</li>

				<li id="jplayer_volume_min2" class="jp-volume-min">min volume</li>
				<li id="jplayer_volume_max2" class="jp-volume-max">max volume</li>
			</ul>
			<div class="jp-progress">
				<div id="jplayer_load_bar2" class="jp-load-bar">
					<div id="jplayer_play_bar2" class="jp-play-bar"></div>
				</div>
			</div>
			<div id="jplayer_volume_bar2" class="jp-volume-bar">
				<div id="jplayer_volume_bar_value2" class="jp-volume-bar-value"></div>
			</div>
			<div id="jplayer_play_time2" class="jp-play-time"></div>
			<div id="jplayer_total_time2" class="jp-total-time"></div>
		</div>
	</div>

</div>

<?php

$this->widget('application.extensions.filetree.SFileTree',
	array(
		"script"=>Yii::app()->createUrl('directory/listing'),
		"div"=>"filetree",
		"multiFolder"=>"true",
		"callback"=>"refreshDraggables",
	)
);
?>

<div id="columns">

	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">File Tree</a></li>
			<li><a href="#tabs-2">Search</a></li>
			<li><a href="#tabs-3">Playlists</a></li>
			<li><a href="#tabs-4">Settings</a></li>
		</ul>

		<div id="tabs-1">
			<div class="scroll" id="filetree"></div>
			<div class="bar">
			</div>
		</div>

		<div id="tabs-2"><?php $this->widget('application.components.SearchresultsWidget'); ?></div>
		<div id="tabs-3"><?php $this->widget('application.components.PlaylistbrowserWidget'); ?></div>
		<div id="tabs-4"><?php $this->widget('application.components.SettingsWidget'); ?></div>

	</div>

	<div id="lists">
		<?php $this->widget('application.components.PlaylistWidget'); ?>
	</div>
</div>