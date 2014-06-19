(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/client:plusone.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    console.log($('#clicker').attr('href'), '1231231');
	$('#clicker').attr('href', $('#clicker').attr('href').replace('{{host}}', window.location.host));
	console.log($('#clicker').attr('href'), '123123123');
})();

var signinCallback = function(authResult) {
	console.log(authResult);
	if (authResult['status']['signed_in']) {
		document.getElementById('signinButton').setAttribute('style', 'display: none');
		$.ajax({
         	url: "https://www.googleapis.com/youtube/v3/channels?part=id,contentDetails&mine=true"+
         	    "&fields=items(id%2CcontentDetails(relatedPlaylists(uploads)))&access_token="+authResult.access_token,
         	type: "GET",
         	success: function(data) {
         		$('#ctrl_custom_field_access_token').val(authResult.access_token);
         		$('#ctrl_custom_field_youtube_id').val(data.items[0].id);
         		$('#ctrl_custom_field_youtubeUploads').val(data.items[0].contentDetails.relatedPlaylists.uploads);
         		alert('Successfully grabbed the data from your Youtube Account. \n\nClick the "Save Changes" button below to save your profile.')
         	}
		});
	} else {
		console.log('Sign-in state: ' + authResult['error']);
	}

    authResult['_xfToken'] = $('#xfToken').val();
    console.log('here');
	$.post('/zh/index.php?pages/about-us&refresh=1', authResult, function(e) {
        console.log(e);
    }, 'json');
}

$(window).on('hashchange', function(e){
    var hash = window.location.hash;
    if(hash && hash.length) {
        hash = hash.split('&');
        for(var i=0; i< hash.length; i++){
            hash[i] = hash[i].split('=');
            if(hash[i][0] == 'code') {
                $.post('/zh/index.php?pages/about-us&refresh', {
                    code: hash[i][1],
                    _xfToken: $('#xfToken').val()
                }, function(e) {
                    $('#ctrl_custom_field_access_token').val(e.access_token);
                    $('#ctrl_custom_field_refresh_token').val(e.refresh_token);
                    $.get("https://www.googleapis.com/youtube/v3/channels?part=id,contentDetails&mine=true"+
                        "&fields=items(id%2CcontentDetails(relatedPlaylists(uploads)))&access_token="+e.access_token,
                        function(data) {
                            $('#ctrl_custom_field_youtube_id').val(data.items[0].id);
                                            $('#ctrl_custom_field_youtubeUploads').val(data.items[0].contentDetails.relatedPlaylists.uploads);
                                                            alert('Successfully grabbed the data from your Youtube Account. \n\nClick the "Save Changes" button below to save your profile.');
                        }
                    );
                } , 'json');
                break;
            }
        };
    }

    console.log(hash);
});

$(window).trigger('hashchange');
