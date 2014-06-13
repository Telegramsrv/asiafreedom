(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/client:plusone.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();

var signinCallback = function(authResult) {
	if (authResult['status']['signed_in']) {
		document.getElementById('signinButton').setAttribute('style', 'display: none');
		$.ajax({
         	url: "https://www.googleapis.com/youtube/v3/channels?part=id,contentDetails&mine=true&fields=items(id%2CcontentDetails(relatedPlaylists(uploads)))&access_token="+authResult.access_token,
         	type: "GET",
         	success: function(data) {
         		$('#ctrl_custom_field_access_token').val(authResult.access_token);
         		$('#ctrl_custom_field_youtube_id').val(data.items[0].contentDetails.id);
         		$('#ctrl_custom_field_youtubeUploads').val(data.items[0].contentDetails.relatedPlaylists.uploads);
         		alert('Successfully grabbed the data from your Youtube Account. \n\nClick the "Save Changes" button below to save your profile.')
         	}
		});
	} else {
		console.log('Sign-in state: ' + authResult['error']);
	}
}