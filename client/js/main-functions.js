function initUi() {

	adjustSizes();

	// Create tabs
	$("#tabs").tabs();

	// Load jplayer
	$("#jquery_jplayer").jPlayer({
		swfPath: jPlayerSwfPath,
		volume: 90,
		nativeSupport: false,
		customCssIds: false
	});
	$("#jquery_jplayer2").jPlayer({
		swfPath: jPlayerSwfPath,
		volume: 90,
		nativeSupport: false,
		customCssIds: true
	});
	
	// First instance
	$("#jquery_jplayer").jPlayer("onProgressChange", function(lp,ppr,ppa,pt,tt) {
		updateProgress('jquery_jplayer',pt,tt);
	});
	// Seconds instance
	$("#jquery_jplayer2").jPlayer("onProgressChange", function(lp,ppr,ppa,pt,tt) {
		updateProgress('jquery_jplayer2',pt,tt);
	})
	.jPlayer("cssId", "pause", "jplayer_pause2")
	.jPlayer("cssId", "play", "jplayer_play2")
	.jPlayer("cssId", "stop", "jplayer_stop2")
	.jPlayer("cssId", "loadBar", "jplayer_load_bar2")
	.jPlayer("cssId", "playBar", "jplayer_play_bar2")
	.jPlayer("cssId", "volumeMin", "jplayer_volume_min2")
	.jPlayer("cssId", "volumeMax", "jplayer_volume_max2")
	.jPlayer("cssId", "volumeBar", "jplayer_volume_bar2")
	.jPlayer("cssId", "volumeBarValue", "jplayer_volume_bar_value2");
	
	// Activate next song button
	$("#next_song").bind("click", function() { nextSong(); });
	$("#next_song2").bind("click", function() { nextSong(); });

	// Make the playlist sortable
	$("#playlist ul").sortable( {
		axis: "y",
		distance: 18,
		containment: "#playlist",
		start: function(event, ui) { $(ui.item).addClass('beingDragged'); },
		stop: function(event, ui) { $(ui.item).removeClass('beingDragged'); modified=true; }
	});
	
	// Activate lookup similar tracks button (last.fm)
	$("a.find_similar").bind("click", function() { 
		lookupSimilar($("#playlist li.selected span.id").html());
	});
	
	// Activate create playlist button
	$("a.create_playlist").bind("click", function() { 
		var name = prompt("Playlist name:");
		createPlaylist(name);
	});
	
	// Activate rename playlist button
	$("a.rename_playlist").bind("click", function() { 
		var name = prompt("Playlist name:");
		renamePlaylist(getCurrentPlaylistID(), name);
	});
	
	// Activate delete playlist button
	$("a.delete_playlist").bind("click", function() {
		var sure = confirm("Are you sure that you want to delete the selected playlist?");
		if(sure) deletePlaylist(getCurrentPlaylistID());
	});
	
	// Activate clear playlist button
	$("a.clear_playlist").bind("click", function() { 
		var sure = confirm("Are you sure that you want to clear the whole playlist?");
		if(sure) deleteItem($("#playlist li"));
	});
	
	// Activate clear queue button
	$("a.clear_queue").bind("click", function() { 
		clearQueue();
	});
	
	// Activate save playlist button
	$("a.save").bind("click", function() { 
		savePlaylist();
	});
	
	// Activate onbeforeunload confirm dialog
	window.onbeforeunload = function() {
		if(modified) {
			return 'You have made modifications to your playlist. ' +
				'Do you really want to quit without saving these changes?';
		}
		else return;
	};
	
	// Activate regeneration of accesstoken
	setInterval('generateAccesstoken()', accesstokenRefreshPeriod*1000);

	loadPlaylistbrowser();
	loadPlaylist();
}


// Set window title
function setWindowTitle(string) {
	document.title = string + " - " + windowTitle;
}


function refreshDraggables() {

	// Make files draggable to the playlist
	
	$("#filetree .media").draggable({
		helper:'clone',
		connectToSortable:'#playlist ul',
		start: function() {
			$(this).addClass("fetching-meta");			
		},
		stop: function(event, ui) {
			getMetaData(this);
			modified=true;
		}
	});
	
	$("#searchresults .media").draggable({
		helper:'clone',
		connectToSortable:'#playlist ul',
		stop: function(event, ui) {
			refreshPlaylistBindings();
			modified=true;
		}
	});
}


function adjustSizes() {

	playlistWidth = 366;
	tabsWidth = Math.floor((window.innerWidth-playlistWidth-3));
	
	$("#tabs").width(tabsWidth);
	$("#tabs div.scroll").height(window.innerHeight-181);
	$("#lists div.scroll").height(window.innerHeight-150);
}


// Resize handler
$(window).resize(function() {
	adjustSizes();
});


function checkAccesstoken() {
	
	$.ajax({
		'type':'POST',
		'url':accesstokenValidUrl,
		'cache':false,
		'data':{ 'accesstoken': accesstoken },
		'success':function(value){
			if(value == '1' || value == '2')
					alert("Invalid accesstoken! Make sure you don't have any other " +
							"instances running and then try refreshing this page.");
			}
		});
}


function generateAccesstoken() {
	
	$.ajax({
		'type':'POST',
		'url':accesstokenGenerateUrl,
		'cache':false,
		'success':function(value){
				if(value.length > 0) accesstoken = value;
			}
		});	
}