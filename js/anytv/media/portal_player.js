var yt;
var key = 'AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk';
var channelId;
var value;
var reserveClick;
var clicked;
var globalVideos = {};
var stateChange = true;

$(document).ready(function(){
	yt = $('#youtubeIds').val();

	getVideos('https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&id='+yt+'&key='+key);

	$('.cat_navbars .filters').click(function(e) {
		$('.cat_navbars .SelectedLink').removeClass('SelectedLink');
		$(this).parent().addClass('SelectedLink');
	})

	$('body').on('click', '.PaginationButton', function(e){
		e.preventDefault();
		getVideos($(this).attr('data-token'));
	});

	$('body').on('click', '.ytVideo .pointer', function(e) {
		e.preventDefault();
		$(this).siblings('img').click();
	});

	$('body').on('click', '.PlaylistPaginationButton', function(e){
		e.preventDefault();
		_getUserPlaylists($(this).attr('data-token'));
	});

	$('.MediaContainer iframe').load(function(e) {
		$('.MediaContainer iframe').removeClass('hide');
		$('.VideoLoader').hide();
	});


	twtch = $('#UserTwitch').val();
	if(twtch) {
		twtch = twtch.replace(/\s/g, '');
		twtch = $.grep(twtch.split(','), function(e) { return $.grep(e.split('\n'), function(e) { return e; }) });
		getUserStreams(twtch);
	}
});

var avblstrms = 0;
var usrstrms;
var loaded = 0;
var getUserStreams = function(arg) {
	avblstrms = loaded = 0;
	if(!Array.isArray(arg)) {
		arg = [arg];
	}

	usrstrms = arg;
	for(var i=0; i<arg.length; i++) {
		getStream(arg[i]);
	}
}

var getStream = function(channel) {
	$.ajax({
		url: 'https://api.twitch.tv/kraken/streams/'+channel+'?callback=getStreamCallback',
		dataType: 'jsonp',
		beforeSend: function(xhr) {
			 xhr.setRequestHeader('Accept', 'application/vnd.twitchtv.v2+json');
		},
		success: getStreamCallback
	});
}

var getStreamCallback = function(res) {
	loaded++;
	if(!res.stream) {
		if(loaded == usrstrms.length) {
			setStreams();
		}
		return;
	}

	avblstrms++;
	var data= {};

	data['IMAGE'] = res.stream.preview.medium;
	data['TITLE'] = res.stream.channel.status;
	data['VIEWERS'] = res.stream.viewers;
	data['NAME'] = res.stream.channel.display_name;
	data['GAME'] = res.stream.game;
	data['CHANNEL'] = res.stream.channel.name;

	var tpl = template($('#streamTpl').html(), data);

	$('#streamsContainer').html($('#streamsContainer').html()+tpl);

	if(loaded == usrstrms.length) {
		setStreams();
	}
}

var setStreams = function() {
	if(avblstrms > 0) {
		$('.blockLinksList li.streams').append($('<span/>', { class: 'badge', text:avblstrms }));
		return;
	}

	//$('.blockLinksList li.streams').remove();
}

var loadStream = function(channel) {
	var context = $('.streamContainer > .left.stream');

	var obj = $('<object/>', {
		type: 'application/x-shockwave-flash',
		height: 500,
		width: '100%',
		id: 'live_embed_player_flash',
		data: 'http://www.twitch.tv/widgets/live_embed_player.swf?channel='+channel,
		bgcolor: '#000000'
	});

	obj.append($('<param/>', { name: 'allowFullScreen', value:'true' }));
	obj.append($('<param/>', { name: 'allowScriptAccess', value:'always' }));
	obj.append($('<param/>', { name: 'allowNetworking', value:'all' }));
	obj.append($('<param/>', { name: 'movie', value:'http://www.twitch.tv/widgets/live_embed_player.swf' }));
	obj.append($('<param/>', { name: 'flashvars', value:'hostname=www.twitch.tv&channel='+channel+'&auto_play=true&start_volume=25' }));

	$('object', context).replaceWith(obj);

	context = $('.streamContainer > .left.comments');
	$('iframe', context).attr('src', 'http://twitch.tv/'+channel+'/chat?popout=');
}

/*****************************************************************************************/
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;
function onYouTubeIframeAPIReady() {
    player = new YT.Player('player', {
        height: '460',
        width: '100%',
        playerVars: { 'autoplay': 1, 'controls': 1, 'showInfo' : 1 },
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
        }
    });
}

var playerReady = false;

function onPlayerReady(event) {
    playerReady = true;
}

function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.UNSTARTED ) {
    	var videoId = event.target.getVideoData().video_id;           
    	var index = event.target.getPlaylistIndex();
    	var context = $('img[data-index='+index+']');
    	var contextContainer = context.parent();
    	var videosContainer = contextContainer.parent();
    	
    	videosContainer.scrollTop(
    		(context.height() + 10) 
    		* $('.ytVideo', videosContainer).index(contextContainer[0])
    	);

    	$('.ytVideo.active').removeClass('active')
    	contextContainer.addClass('active');
    	if(stateChange) {
        	loadComments('https://www.youtube.com/watch?v='+videoId);
			showDetails(videoId);
		}

		stateChange = true;
    }
}
/*****************************************************************************************/
$(window).on('hashchange', function(e) {
	var hash = window.location.hash;
	if(hash.split('=').length==2) {
		var type = hash.split('=')[0];
		value = hash.split('=')[1];
	}

	if(type && type=='#filter') {
		getVideos('https://www.googleapis.com/youtube/v3/search?'
			+'q='+value
			+'&maxResults=20'
			+'&channelId='+channelId+'&part=snippet'
			+'&type=video&key='+key+'');
		_getUserPlaylists('https://www.googleapis.com/youtube/v3/search?'
			+'q='+value
			+'&maxResults=20'
			+'&channelId='+channelId+'&part=snippet'
			+'&type=playlist&key='+key+'');
	}
});



var getUploadedVideos = function(id) {
	$.getJSON('https://www.googleapis.com/youtube/v3/channels?id='+id+
		'&key='+key+
		'&part=contentDetails', function(e){
			var playlistId = null; 
			if(e.items[0] && e.items[0].contentDetails &&
				e.items[0].contentDetails.relatedPlaylists && 
				e.items[0].contentDetails.relatedPlaylists.uploads) {
				playlistId = e.items[0].contentDetails.relatedPlaylists.uploads;
			}

			getPlaylistContents(playlistId); 
		}
	);
};

var getUserPlaylists = function(channelId, token) {
	var pagination = token ? '&pageToken='+token : '';
	_getUserPlaylists('https://www.googleapis.com/youtube/v3/playlists'+
		'?key='+key+
		'&part=snippet'+
		'&channelId='+channelId+
		'&maxResults=20'+pagination);
};

var _getUserPlaylists = function(link) {
	$.getJSON(link,
		function(e) { 
			var videos = e.items;
			var html = '';
			if(videos.length < 1) {
				html = $('<div />', {class: 'empty-container'}).html('No Results Found')[0].outerHTML;
			}

			for(var i=0; i<videos.length; i++) {
				channelId = videos[i]['snippet']['channelId'];
				var params = {};
				params['ID'] = videos[i]['id']['playlistId'] ? videos[i]['id']['playlistId'] : videos[i]['id'];
				params['YTLINK'] = 'https://www.youtube.com/watch?v='+params['ID'];
				params['PLID'] = videos[i]['snippet']['playlistId'] ? videos[i]['snippet']['playlistId'] : 'false';
				params['INDEX'] = videos[i]['snippet']['playlistId'] ? [i] : 'false';
				params['IMG'] = videos[i]['snippet']['thumbnails']['default']['url'];
				params['LINK'] = 'https://www.youtube.com/watch?v='+params['ID'];
				params['TITLE'] = videos[i]['snippet']['title'];
				params['MONTH'] = 'Mar';
				params['DAY'] = '7';
				params['YEAR'] = '2014';
				params['TIME'] = '3:33AM';

				html += template($('#PlaylistsTemplate').html(), params);
			}

			if(e.prevPageToken) {
				html += $('<a/>', { 
					href: '#',
					class:'PrevButton PlaylistPaginationButton',
					'data-token': updateQueryStringParameter(link, 'pageToken', e.prevPageToken),
					text: 'Prev <'
				})[0].outerHTML;
			}

			if(e.nextPageToken) {
				html += $('<a/>', {
					href: '#',
					class:'NextButton PlaylistPaginationButton',
					'data-token': updateQueryStringParameter(link, 'pageToken', e.nextPageToken),
					text: '> Next'
				})[0].outerHTML;
			}

			$('#UserPlaylists').html(html);
		}
	);
}

var getPlaylistContents = function(playlistId, nextPageToken, play) {
	getVideos('https://www.googleapis.com/youtube/v3/playlistItems/'+
		'?playlistId='+playlistId+
		'&key='+key+
		'&part=snippet'+
		'&maxResults=20');

	if(play) {
		clicked = false;
	}
};

var getVideos = function(link) {
	$.getJSON(link,
		function(e) { 
			var videos = e.items;
			var html = '';
			if(videos.length < 1) {
				html = $('<div />', {class: 'empty-container'}).html('No Results Found')[0].outerHTML;
			}

			console.log(videos);

			for(var i=0; i<videos.length; i++) {
				channelId = videos[i]['snippet']['channelId'];
				var params = {};
				params['ID'] = videos[i]['id'] && videos[i]['id']['videoId'] 
					? videos[i]['id']['videoId']
					: videos[i]['snippet']['resourceId']['videoId'];
				params['YTLINK'] = 'https://www.youtube.com/watch?v='+params['ID'];
				params['PLID'] = videos[i]['snippet']['playlistId'] ? videos[i]['snippet']['playlistId'] : 'false';
				params['INDEX'] = videos[i]['snippet']['playlistId'] ? videos[i]['snippet']['position'] : 'false';
				params['IMG'] = videos[i]['snippet']['thumbnails']['default']['url'];
				params['LINK'] = 'https://www.youtube.com/watch?v='+params['ID'];
				params['TITLE'] = videos[i]['snippet']['title'];
				params['MONTH'] = 'Mar';
				params['DAY'] = '7';
				params['YEAR'] = '2014';
				params['TIME'] = '3:33AM';
				params['DETAILS'] = videos[i]['snippet']['description'].replace(/\n/g, "<br />");

				globalVideos[params['ID']] = params;

				html += template($('#VideosTemplate').html(), params);
			}

			if(e.prevPageToken) {
				html += $('<a/>', { 
					href: '#',
					class:'PrevButton PaginationButton',
					'data-token': updateQueryStringParameter(link, 'pageToken', e.prevPageToken),
					text: '<'
				})[0].outerHTML;
			}

			if(e.nextPageToken) {
				html += $('<a/>', {
					href: '#',
					class:'NextButton PaginationButton',
					'data-token': updateQueryStringParameter(link, 'pageToken', e.nextPageToken),
					text: '>'
				})[0].outerHTML;
			}

			$('#UserVideos').html(html).promise().done(function() {
				if($('.mediaPage.customTabShown').length == 1 && !clicked) {
					$('#UserVideos .ytVideo:first > img').click();
					return;
				}

				reserveClick = true;
			});
		}
	);
};

var template = function(templateHTML, data) {
	for(var x in data) {
		templateHTML = templateHTML.replace(new RegExp('{{'+x+'}}', 'g'), data[x]);
	}

	return templateHTML;
};

var showVideo = function(context, id, link, playlist, index) {
	$('.ytVideo.active').removeClass('active');
	$(context).parent().addClass('active');
	$('.VideoLoader').show();
	$('.MediaContainer iframe').addClass('hide');
	var src = 'http://www.youtube.com/embed/'+id+'?autohide=1&autoplay=1&enablejsapi=1';
	stateChange = false;
	if(index!=='false') {
		src = 'http://www.youtube.com/embed/videoseries?list='+playlist+'&autoplay=1&enablejsapi=1&index='+index;
		player.loadPlaylist({
			list:playlist,
			index: index
		});
		
	} else {
		player.destroy();
		player = new YT.Player('player', {
            height: '460',
            width: '100%',
            videoId: id,
            playerVars: { 'autoplay': 1, 'controls': 1 },
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
	}

	baseUrl = baseUrl.replace(window.location.hash, '');
	loadComments(link);
	showDetails(id);
};

var showDetails = function(id) {
	$("#mediaDetails").html(template($('#VideoDetailTemplate').html(), globalVideos[id]));
}

var loadComments = function(link) {
	var iframeLink = 'https://plusone.google.com/_/widget/render/comments?bsv&href=http%3A%2F%2Fwww.google.com&first_party_property=BLOGGER&view_type=FILTERED_POSTMOD&width='+
		($('.MediaContainer .videoContainer iframe').width()-20);
	iframeLink = updateQueryStringParameter(iframeLink, 'href', link);
	$('#gcom').attr('src', iframeLink);
	$('#comments, #gcom').width($('.MediaContainer .videoContainer iframe').width());
}

var updateQueryStringParameter = function (uri, key, value) {
	var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i");
	separator = uri.indexOf('?') !== -1 ? "&" : "?";
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
};

var embedLoaded = function() {
}