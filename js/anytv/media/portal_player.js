var channelId;
var key = 'AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk';
var value;
var reserveClick;
var clicked;
var stateChange = true;

var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;

var documentReady = function(){
	channelId = $('#UserYoutube').val();
	if(channelId) {
		channelId = $.grep(channelId.split('/'), function(e) { return e; });
		channelId = channelId[channelId.length - 1];
	}
	
	uploads = $('#UserUploads').val();

	getPlaylistContents(uploads);
	getUserPlaylists(channelId);

	$('.cat_navbars .filters').click(function(e) {
		$('.cat_navbars .SelectedLink').removeClass('SelectedLink');
		$(this).parent().addClass('SelectedLink');
	})

	$('body').on('click', '.PaginationButton', function(e){
		e.preventDefault();
		getVideos($(this).attr('data-token'));
	});

	$('body').on('click', '.listContainer.playlist .ytVideo .pointer', function(e) {
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
};

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
var queue = [];

function onPlayerReady(event) {
	console.log('ready');
    playerReady = true;
    for(var i=0; i<queue.length; i++) {
    	eval(queue[i]);
    }

    documentReady();
    queue = [];
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

var hashVideo = function(params) {
	params.splice(0,1);
	var video = params[0];
	if(!playerReady){
		if(!isShows)
			queue.push("showTab($('a[data-context=media]'), 'media')");
		queue.push("showVideo($('img[data-id="+video+"]'), '"+video+"', 'https://www.youtube.com/watch?v="+video+"', 'false', 'false')");
		return;
	}
	//showTab($('a[data-context=media]'), 'media');
	showVideo($('img[data-id='+video+']'), video, 'https://www.youtube.com/watch?v='+video, 'false', 'false');
};

var hashPlaylist = function(params) {
	params.splice(0,1);
	var playlistId = params.splice(0,1);
	params.splice(0,1);
	var video = params[0];
	var position = params[1];

	if(!playerReady){
		console.log('here');
		if(!isShows)
			queue.push("showTab($('a[data-context=media]'), 'media')");
		queue.push("getPlaylistContents('"+playlistId+"')");
		queue.push("showVideo($('img[data-index="+position+"]'), '"+video+"', 'https://www.youtube.com/watch?v="+video+"', '"+playlistId+"', '"+position+"')");
		return;
	}

	if(player.getPlaylistId()!=playlistId) {
		getPlaylistContents(playlistId);
	}
	
	showVideo($('img[data-index='+position+']'), video, 'https://www.youtube.com/watch?v='+video, playlistId, position);
}

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
				params['SEARCH'] = 0;

				html += template($('#PlaylistsTemplate').html(), params);
			}


			if(e.prevPageToken) {
				var a = $('<a/>', { 
					href: '#',
					class:'PrevButton PlaylistPaginationButton',
					'data-token': updateQueryStringParameter(link, 'pageToken', e.prevPageToken),
					text: 'Prev'
				});
				a.append($('<i/>', { class: 'fa fa-arrow-left' }));
				html += a[0].outerHTML;
			}

			if(e.nextPageToken) {
				var a = $('<a/>', {
					href: '#',
					class:'NextButton PlaylistPaginationButton',
					'data-token': updateQueryStringParameter(link, 'pageToken', e.nextPageToken),
					text: 'Next'
				});
				a.prepend($('<i/>', { class: 'fa fa-arrow-right' }));
				html += a[0].outerHTML;
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

var createHash = function(data) {
	var hash = '#!/';
	hash = '#!/video/'+ data['id'] && data['id']['videoId'] ? data['id']['videoId'] : data['snippet']['resourceId']['videoId'];
	if(data['snippet']['playlistId']) {	
		hash ='#!/playlist/'+data['snippet']['playlistId']+'/video/'
			+(data['id'] && data['id']['videoId'] ? data['id']['videoId'] : data['snippet']['resourceId']['videoId'])+'/'
			+(data['snippet']['position']);
	}

	return hash;
};

var getVideos = function(link, search) {
	if(!search) {
		search = 0;
	}
	$.getJSON(link,
		function(e) { 
			var videos = e.items;
			var html = '';
			if(videos.length < 1) {
				html = $('<div />', {class: 'empty-container'}).html('No Results Found')[0].outerHTML;
			}

			for(var i=0; i<videos.length; i++) {
				channelId = videos[i]['snippet']['channelId'];
				var date = videos[i]['snippet']['publishedAt'];
				var time = date.split('T');
				date = time[0];
				time = time[1];
				date = date.split('-');
				time = time.split('.');
				var params = {};
				params['ID'] = videos[i]['id'] && videos[i]['id']['videoId'] 
					? videos[i]['id']['videoId']
					: videos[i]['snippet']['resourceId']['videoId'];
				params['YTLINK'] = 'https://www.youtube.com/watch?v='+params['ID'];
				params['PLID'] = videos[i]['snippet']['playlistId'] ? videos[i]['snippet']['playlistId'] : 'false';
				params['INDEX'] = videos[i]['snippet']['playlistId'] ? videos[i]['snippet']['position'] : 'false';
				params['IMG'] = videos[i]['snippet']['thumbnails']['default']['url'];
				params['LINK'] = window.location.href.replace(window.location.hash, '')+createHash(videos[i]);
				params['TITLE'] = videos[i]['snippet']['title'];
				params['MONTH'] = (numToMonth(Number.parseInt(date[1])));
				params['DAY'] = date[2];
				params['YEAR'] = date[0];
				params['TIME'] = time[0];
				params['DETAILS'] = videos[i]['snippet']['description'].replace(/\n/g, "<br />");
				params['SEARCH'] = search;

				globalVideos[params['ID']] = params;

				html += template($('#VideosTemplate').html(), params);
			}

			if(e.prevPageToken) {
				var a = $('<a/>', { 
					href: '#',
					class:'PrevButton PaginationButton',
					'data-token': updateQueryStringParameter(link, 'pageToken', e.prevPageToken),
					text: 'Prev'
				});
				a.append($('<i/>', { class: 'fa fa-arrow-left' }));
				html += a[0].outerHTML;
			}

			if(e.nextPageToken) {
				var a = $('<a/>', {
					href: '#',
					class:'NextButton PaginationButton',
					'data-token': updateQueryStringParameter(link, 'pageToken', e.nextPageToken),
					text: 'Next'
				});
				a.prepend($('<i/>', { class: 'fa fa-arrow-right' }));
				html += a[0].outerHTML;
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

var numToMonth = function(m) {
	var arr = ['', 'Janary', 'Feburary', 'March', 'April', 'May', 'June', 
		'July', 'August', 'September', 'October', 'November', 'December'];
	return arr[m];
}

var template = function(templateHTML, data) {
	for(var x in data) {
		templateHTML = templateHTML.replace(new RegExp('{{'+x+'}}', 'g'), data[x]);
	}

	return templateHTML;
};

var showVideo = function(context, id, link, playlist, index) {
	console.log(context);
	$('.ytVideo.active').removeClass('active');
	$(context).parent().addClass('active');
	$('.VideoLoader').show();
	$('.MediaContainer iframe').addClass('hide');
	var src = 'http://www.youtube.com/embed/'+id+'?autohide=1&autoplay=1&enablejsapi=1';
	stateChange = false;
	if(index!=='false') {
		src = 'http://www.youtube.com/embed/videoseries?list='+playlist+'&autoplay=1&enablejsapi=1&index='+index;
		if(player.getPlaylistId()!=playlist) {
			var params = {
				list: playlist,
				index: index
			};
			if(playlist == $('#UserUploads').val()) {
				// params['listType'] = 'user_uploads';
				// params['list'] = 'adin2344';
			}
			player.loadPlaylist(params);
		} else {
			player.playVideoAt(index);
		}
		
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
	loadShares(link);
};

var loadShares = function(link) {
	gapi.plus.render("gplusShare", {action: "share", href: link});
};

var showDetails = function(id) {
	$("#mediaDetails").html(template($('#VideoDetailTemplate').html(), globalVideos[id]));
};

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

var capitaliseFirstLetter = function(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/*****************************************************************************************/
$(window).on('hashchange', function(e) {
	var hash = window.location.hash;
	var arr = hash.split('/');
	if(arr[0] == "#!") {
		//videos or playlist
		arr.splice(0,1);
		eval("hash"+capitaliseFirstLetter(arr[0])+"(arr)");
	}

	if(hash.split('=').length==2) {
		var type = hash.split('=')[0];
		value = hash.split('=')[1];
	}

	if(type && type=='#filter') {
		getVideos('https://www.googleapis.com/youtube/v3/search?'
			+'q='+value
			+'&maxResults=20'
			+'&channelId='+channelId+'&part=snippet'
			+'&type=video&key='+key+'', 1);
		_getUserPlaylists('https://www.googleapis.com/youtube/v3/search?'
			+'q='+value
			+'&maxResults=20'
			+'&channelId='+channelId+'&part=snippet'
			+'&type=playlist&key='+key+'');
	}
});


	$(window).trigger('hashchange');