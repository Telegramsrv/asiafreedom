(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/client:plusone.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	$('#clicker').attr('href', $('#clicker').attr('href').replace('{{host}}', window.location.host));
})();

$(window).on('hashchange', function(e){
    var hash = window.location.hash;
    if(hash && hash.length) {
        hash = hash.split('&');
        for(var i=0; i< hash.length; i++){
            hash[i] = hash[i].split('=');
            if(hash[i][0] == 'code') {
                $.post('/zh/index.php?pages/about-us&refresh', {
                    code: hash[i][1],
                    redirect: window.location.href.replace(window.location.hash, ''),
                    _xfToken: $('#xfToken').val()
                }, function(e) {
                    $('#ctrl_custom_field_access_token').val(e.access_token);
                    $('#ctrl_custom_field_refresh_token').val(e.refresh_token);
                    $.get("https://www.googleapis.com/youtube/v3/channels?part=id,contentDetails&mine=true"+
                        "&fields=items(id%2CcontentDetails(relatedPlaylists(uploads)))&access_token="+e.access_token,
                        function(data) {
                            $('#ctrl_optionsNewsChannel').val(data.items[0].id);
                            $('#ctrl_optionsNewsPlaylist').val(data.items[0].contentDetails.relatedPlaylists.uploads);
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
