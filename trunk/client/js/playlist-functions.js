// Load playlist
function loadPlaylist(playlistID) {
	
	$("#playlist").addClass("loading");
	
	jQuery.ajax({
		'type':'POST',
		'url':loadPlaylistUrl,
		'data': { 'id': playlistID },
		'cache':false,
		'success':function(html){
			$("#playlist").removeClass("loading");
			jQuery("#playlist ul").html(html);
			refreshPlaylistBindings();
			queueCount = $("#playlist ul li span.queue").size();
			}
		});
	
	return false;
}


// Load playlist browser
function loadPlaylistbrowser() {

	$("#playlist").addClass("loading");

	jQuery.ajax({
		'type':'POST',
		'url':loadPlaylistbrowserUrl,
		'cache':false,
		'success':function(html){
			$("#playlistbrowser").removeClass("loading");
			jQuery("#playlistbrowser").html(html);
			refreshPlaylistbrowserBindings();
			}
		});
	
	return false;
}


// Select entry in playlist/playlistbrowser
function selectItem(listItem) {
	listItem.siblings().removeClass("selected");
	listItem.addClass("selected");
	listItem.children("a").focus();
}


// Delete entry from playlist/playlistbrowser
function deleteItem(listItem) {
	listItem.remove();
	modified=true;
}


// Create new playlist
function createPlaylist(playlistName) {

	if(!playlistName) playlistName = "";
	
	jQuery.ajax({
		'type':'POST',
		'url':createPlaylistUrl,
		'data': { 'name': playlistName },
		'cache':false,
		'success':function(html){
			loadPlaylistbrowser();
			}
		});
}


// Rename playlist
function renamePlaylist(playlistID, playlistName) {

	if(!playlistName) playlistName = "";
	
	jQuery.ajax({
		'type':'POST',
		'url':renamePlaylistUrl,
		'data': { 'id': playlistID, 'name':playlistName },
		'cache':false,
		'success':function(html){
			loadPlaylistbrowser();
			}
		});
}


// Delete playlist
function deletePlaylist(playlistID) {

	jQuery.ajax({
		'type':'POST',
		'url':deletePlaylistUrl,
		'data': { 'id': playlistID },
		'cache':false,
		'success':function(html){
			loadPlaylistbrowser();
			}
		});
}


// Save the current playlist to database
function savePlaylist() {

	var playlistID = getCurrentPlaylistID();
	var playlistData = $("#playlist ul").html();

	jQuery.ajax({
		'type':'POST',
		'url':savePlaylistUrl,
		'data':{ 'id': playlistID, 'playlist': playlistData },
		'cache':false
	});
	
	modified = false;
	return false;
}


// Play the next song in the playlist or in the queue
function nextSong() {

	var nextSong=null;
	
	// No queue
	if(queueCount == 0) {
		nextSong = $("#currentSong").next();
	}
	// Queue exists
	else {
		$("#playlist ul li span.queue").each(function(i) {
			queueNo = parseInt($(this).html());
			if(queueNo == 1) {
				nextSong = $(this).parent();
				$(this).remove();
			}
		});
		reenumerateQueue(1);
	}
	
	if(nextSong.has("a").length==1) playSong(nextSong);
	else stopPlayback();
}


// Queue item
function queueItem(listItem) {
	
	if(listItem.has("span.queue").length==0) {
		queueCount++;
		listItem.prepend('<span class="queue">' + queueCount + '</span>');
	}
	else {
		var removedNo = parseInt(listItem.children("span.queue").html());
		listItem.children("span.queue").remove();
		reenumerateQueue(removedNo);
	}
	modified=true;
}


// Re-enumerate queue
function reenumerateQueue(removedNo) {

	var queueNo=0;

	$("#playlist ul li span.queue").each(function(i) {
		queueNo = parseInt($(this).html());
		if(queueNo > removedNo) {
			$(this).html(queueNo-1);
		}
	});
	
	queueCount--;
}


// Clear queue
function clearQueue() {
	$("#playlist ul li span.queue").remove();
	queueCount=0;
	modified=true;
}


function getCurrentPlaylistID() {
	return parseInt($("#playlistbrowser li.selected span.id").html());
}


function getMetaData(listItem) {

	var relpath = $(listItem).children("a").attr("rel");

	$.ajax({
		'type':'POST',
		'url':metaDataUrl,
		'cache':false,
		'data':{ relpath: relpath },
		'success':function(html){
			var listItem = $(".fetching-meta");
			if(html) $(listItem).html(html);
			$(listItem).removeClass("fetching-meta");
			refreshPlaylistBindings();
			}
		});
}


function refreshPlaylistBindings() {

	// Select song on click
	$("#playlist li").unbind("click");
	$("#playlist li").bind("click", function() {
		selectItem($(this));
	});
	
	// Play song on doubleclick
	$("#playlist li").unbind("dblclick");
	$("#playlist li").bind("dblclick", function() { 
		playSong($(this));
	});
	
	// Handle keypresses
	$("#playlist li a").unbind("keydown");
	$("#playlist li a").bind("keydown", function(event) { 
	
		event.preventDefault();
		keyCode = event.which;
		if(keyCode == 0) keyCode = event.keyCode;

		// Delete
		if(keyCode == 46) {
			selectItem($(this).parent().next());
			deleteItem($(this).parent());
		}
		// Up
		else if(keyCode == 38) {
			selectItem($(this).parent().prev());
		}
		// Down
		else if(keyCode == 40) {
			selectItem($(this).parent().next());
		}
		// Enter
		else if(keyCode == 13) {
			playSong($(this).parent());
		}
		// Q
		else if(keyCode == 81) {
			queueItem($(this).parent());
		}
		
		
	});
}

function refreshPlaylistbrowserBindings() {

	// Load playlist on click
	$("#playlistbrowser li").unbind("click");
	$("#playlistbrowser li").bind("click", function() {
		if(modified) savePlaylist();
		selectItem($(this));
		loadPlaylist(parseInt($(this).children('span.id').html()));
	});
}