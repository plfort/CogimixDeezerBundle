function deezerPlayer(musicPlayer){
this.name = "Deezer";
this.interval;
this.musicPlayer = musicPlayer;
this.currentState = null;
this.currentSoundObject=null;
this.currentDuration=0;
this.widgetElement =$("#deezerWidgetContainer");
var self = this;

DZ.Event.subscribe('player_paused', function(evt_name){
	self.musicPlayer.unbinCursorStop();
});

DZ.Event.subscribe('player_play', function(evt_name){
	self.musicPlayer.enableControls();
	self.musicPlayer.bindCursorStop(function(value) {
		  
		DZ.player.seek(value*100/self.currentDuration);
    });
});

DZ.Event.subscribe('player_position', function(args,evt_name){
	loggerDeezer.debug(args);
	if(args[0]>=(args[1]-1)){
		self.musicPlayer.next();
	}
	if(self.musicPlayer.cursor.data('isdragging')==false){
		  self.musicPlayer.cursor.slider("value", args[0]*1000);
		}
});

DZ.Event.subscribe('current_track', function(args, evt_name){
	loggerDeezer.debug('current trask changed');
	
	self.currentDuration=args.track.duration*1000;
	 self.musicPlayer.cursor.slider("option", "max", self.currentDuration).progressbar();
});

this.play = function(item) {
	
	DZ.player.playTracks([item.entryId], 0, function(response){
		
	});
};

this.stop = function(){
	loggerDeezer.debug('call stop deezer');	
	DZ.player.pause();	
}

this.pause = function(){
	loggerDeezer.debug('call pause deezer');
	DZ.player.pause();
	
}
this.resume = function(){
	loggerDeezer.debug('call resume deezer');
	DZ.player.play();
}
}

$(document).ready(function(){

	$.get('bundles/cogimixdeezer/js/template/track.html',function(html){
		tplFiles['trackDz']=html;
	},'text');
	
	$(document).on('click','#loginGroovesharkBtn',function(event){
		$("#modalLoginGroovehsark").modal("toggle");
	});
	
	$("#playlist-container").on('click','.showPlaylistDeezerBtn',function(event){
		var playlistElement = $(this).closest('.dz-playlist-item');
		var playlistName = $(this).html();
		var playlistAlias = playlistElement.data('alias');
		$.get(Routing.generate('_deezer_playlist_songs',{'playlistId':playlistElement.data('id')}),function(response){
			if(response.success == true){
				renderResult(response.data.tracks,{tpl:'trackDz',tabName:playlistName,alias:playlistAlias});
            	$("#wrap").animate({scrollTop:0});
	
			}else{
				loggerDeezer.debug('Error with deezer');
			}
		},'json');
		return false;
	});
	
	$("#playlist-container").on('click','.playPlaylistDeezerBtn',function(event){
		
		$.get(Routing.generate('_deezer_playlist_songs',{'playlistId':$(this).closest('.dz-playlist-item').data('id')}),function(response){
			if(response.success == true){
				musicPlayer.removeAllSongs();
				musicPlayer.addSongs(response.data.tracks);
                musicPlayer.play();
			}else{
				loggerDeezer.debug('Error with deezer');
			}
		},'json');
		return false;
	});
	
	$("#deezer-menu").on('click','#loginDeezerBtn',function(event){
		  var loginLink = $(this);
			DZ.login(function(response) {
				if (response.authResponse) {
					var postData = response;
					$.post(Routing.generate('_deezer_login_success'),postData,function(response){
						if(response.success){
							loginLink.replaceWith(response.html);
							$("#dz-playlist-container").empty();
					    	 $("#dz-playlist-container").replaceWith(response.data.playlistsHtml);
					    	 $(".dz-playlist-item").draggable(draggableOptionsPlaylistListItem);
						}	
					},'json');
				}
				
			}, {perms: 'offline_access'});
    	    return false;
		
	});
	
	$("#deezer-menu").on('click','#logoutDeezerBtn',function(event){
		loggerDeezer.debug('Deezer logout clicked');
		var logoutLink = $(this);
		DZ.logout(function(response){
			loggerDeezer.debug('Deezer logout success');
			$.get(Routing.generate('_deezer_logout'),function(response){
				if(response.success==true){
					logoutLink.replaceWith(response.html);
					$("#dz-playlist-container").empty();
				}	
			},'json');
		});
		
   	    return false;
	});


    $(".dz-playlist-item").draggable(draggableOptionsPlaylistListItem);
});


droppedHookArray['dz-playlist'] = function(droppedItem,callback){
		var playlistId=droppedItem.data('id');
		$.get(Routing.generate('_deezer_playlist_songs',{'playlistId':playlistId}),function(response){
            if(response.success==true){
                loggerDeezer.debug(response.data.tracks);
                callback(response.data.tracks);
                }
            },'json');
	}