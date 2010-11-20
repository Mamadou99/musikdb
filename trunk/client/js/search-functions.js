// Get similar tracks from last.fm and return only the ones
// which are present in our music collection
function lookupSimilar(trackID) {

	if(trackID == null) {
		alert("last.fm lookup not possible (either no song is selected or the current song has no ID)");
		return;
	};

	$("#searchresults").html('');
	$("#searchresults").addClass("loading");

	$.ajax({
		'type':'POST',
		'url':similarTracksUrl,
		'data': { 'id': trackID },
		'cache':false,
		'complete': function() {
			$("#searchresults").removeClass("loading");
		},
		'success':function(html){
			$("#searchresults").html(html);
			refreshDraggables();
		}
	});
}