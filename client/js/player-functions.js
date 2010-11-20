// Play song from the given element
function playSong(listElement) {
	
	var songTitle = listElement.children("a").children("span.title").html();
	var songArtist = listElement.children("a").children("span.artist").html();
	var songFilename = listElement.children("a").children("span.filename").html();
	var songRelpath = listElement.children("a").attr("rel");
	
	var songUrl =  serverBaseUrl + streamUrl + '/' + accesstoken + '.' + songRelpath + '.' + transcodingBitrate + '.mp3';
	var coverUrl = serverBaseUrl + streamUrl + '/' + accesstoken + '.' + songRelpath + '.0.jpg';
	checkAccesstoken();
	
	if('console' in window && 'log' in window.console)
	{
		console.log("Playing " + songUrl);
		console.log("Cover from " + coverUrl);
	}
	
	listElement.siblings().attr("id", "");
	listElement.attr("id", "currentSong");
	
	modified=true;
	
	if(songArtist || songTitle)
		currentSong = songArtist + " - " + songTitle;
	else currentSong = songFilename;

	if($("#" + priPlayerID).jPlayer("getData","diag.isPlaying") == true) {
	
		// currently playing, do crossfade
	
		$("#" + secPlayerID).jPlayer("volume",0);
		$("#" + secPlayerID).jPlayer("setFile", songUrl);
		$("#" + secPlayerID).jPlayer("play");
		
		invokeCoverThrobber();
		switchPlayers();
		
		// wait until the (new) primary player starts playing
		$.timer(100, function (timer) {
				
			if($("#" + priPlayerID).jPlayer("getData", "diag.playedTime") > 100) {
				crossfadePlayers(crossfadeTime);
				loadCover(coverUrl);
				timer.stop();
			}
		});
	
	}
	else {
	
		// currently not playing, no crossfade
	
		$("#" + priPlayerID).jPlayer("volume",90);
		$("#" + priPlayerID).jPlayer("setFile", songUrl);
		$("#" + priPlayerID).jPlayer("play");
		
		invokeCoverThrobber();
		
		// load cover after song started playing
		$.timer(100, function (timer) {
				
			if($("#" + priPlayerID).jPlayer("getData", "diag.playedTime") > 100) {
				loadCover(coverUrl);
				timer.stop();
			}
		});
	}
}


function switchPlayers() {

	var oldPriPlayerID = priPlayerID;
	
	priPlayerID = secPlayerID;
	secPlayerID = oldPriPlayerID;
}


// Update the progress
function updateProgress(playerID, playedTime, totalTime) {

	if(playerID != priPlayerID) return;
	
	setWindowTitle('[' + $.jPlayer.convertTime(playedTime) + '/' +
		$.jPlayer.convertTime(totalTime) + 
		'] ' + currentSong);

	// preload the next song
	if($("#" + priPlayerID).jPlayer("getData", "diag.loadPercent") == 100
		&& $("#" + secPlayerID).jPlayer("getData", "diag.src") == '') {
		
		// not implemented yet
	}
	
	// play next song
	if($("#" + priPlayerID).jPlayer("getData", "diag.loadPercent") == 100
			&& totalTime - playedTime < crossfadeTime) {
		nextSong();
	}
}


// Do the crossfading 
function crossfadePlayers(crossfadeTime) {

	var currentVolume = 90; //$("#" + secPlayerID).jPlayer("getData", "volume");
	var interval = crossfadeTime / currentVolume;
	
	// mute primary player
	$("#" + priPlayerID).jPlayer("volume",0);
	
	// secondary player fades out and stops
	$.timer(interval, function (timerOut) {
		var volume = $("#" + secPlayerID).jPlayer("getData", "volume") -1;
		$("#" + secPlayerID).jPlayer("volume",volume);
		if(volume < 1) {
			$("#" + secPlayerID).jPlayer("stop");
			timerOut.stop();
		}
	});
	$("#" + secPlayerID + '_elements').hide();

	// primary player fades in (twice as fast)
	$.timer(interval/2, function (timerIn) {
		var volume = $("#" + priPlayerID).jPlayer("getData", "volume") +2;
		$("#" + priPlayerID).jPlayer("volume",volume);
		if(volume > 90) timerIn.stop();
	});
	$("#" + priPlayerID + '_elements').show();
}


// Stop the playback
function stopPlayback() {
	setWindowTitle("[Stopped]");
	$("#playlist li").siblings().attr("id", "");
}


// Get cover image from URL
function loadCover(url) {
	
	$.ajax({
		'type':'POST',
		'url':coverUrl,
		'cache':false,
		'data':{ 'coverUrl': url },
		'success':function(html){
			$("#cover").html(html);
			$("#cover").removeClass('loading');
			}
		});
	
	return false;
}

function invokeCoverThrobber() {

	$("#cover").html('');
	$("#cover").addClass('loading');
}