var deferredDeezerPluginReady = $.Deferred();
var nextDeezerTimeout = null;

function initCogimixDeezer(){
	DZ.Event.subscribe('player_buffering', function(evt){
		musicPlayer.cursor.progressbar('value',evt);
		  });

	DZ.Event.subscribe('player_paused', function(evt_name){
		loggerDeezer.debug('player_paused');
		musicPlayer.unbinCursorStop();
	});



	DZ.Event.subscribe('player_play', function(evt_name){
		loggerDeezer.debug('player_play');
		//DZ.Event.unsubscribe('player_position');
		DZ.Event.subscribe('player_position', function(args,evt_name){

			loggerDeezer.debug('player_position '+args[0]+' '+(args[1]));

			if(args[1]>0 && args[0]>=(args[1]-2) && nextDeezerTimeout ==null){
				//DZ.Event.unsubscribe('player_position');
				nextDeezerTimeout = setTimeout(function(){
					try{
						DZ.Event.unsubscribe('player_position');
						musicPlayer.plugin['dz'].pause();
						musicPlayer.next();
					}catch(err){
						loggerDeezer.error(err);
					}
				},2000);
			
			}
			if(musicPlayer.cursor.data('isdragging')==false){
			  musicPlayer.cursor.slider("value", args[0]);
			}
		});
		musicPlayer.enableControls();

		musicPlayer.bindCursorStop(function(value) {
		
			try{
				DZ.player.seek(value*100/musicPlayer.plugin['dz'].currentDuration);
			}catch(err){
				loggerDeezer.error(err);
			}
		});
	});



	DZ.Event.subscribe('current_track', function(args, evt_name){
		loggerDeezer.debug('current track change');
		nextDeezerTimeout = null;
		musicPlayer.plugin['dz'].currentDuration=args.track.duration;
		musicPlayer.cursor.slider("option", "max", musicPlayer.plugin['dz'].currentDuration).progressbar();
	});
	
}
function deezerPlayer(musicPlayer){
this.name = "Deezer";
this.cancelRequested=false;
this.interval;
this.musicPlayer = musicPlayer;
this.currentState = null;
this.currentSoundObject=null;
this.currentDuration=0;
this.widgetElement =$("#deezerWidgetContainer");
var self = this;

initCogimixDeezer();

this.requestCancel=function(){
	self.cancelRequested=true;
};


this.play = function(item) {
	self.musicPlayer.cursor.progressbar();
	loggerDeezer.debug('call play deezer');	
	self.setVolume(self.musicPlayer.volume);
	DZ.player.playTracks([item.entryId], 0, function(response){
		
	});
};


this.suscribe=function(){

}
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

this.setVolume = function(value){
	loggerDeezer.debug('call setvolume deezer');
	DZ.player.setVolume(value);
}

}


function addDeezerPlugin(){
	musicPlayer.addPlugin('dz',new deezerPlayer(musicPlayer));
	musicPlayer.play();
}

$(document).ready(function(){
	musicPlayer.lazyLoadPlugin['dz']=function(){
		loadDeezer();
	}
	

	$(document).on('click','#loginGroovesharkBtn',function(event){
		$("#modalLoginGroovehsark").modal("toggle");
	});
	
	$("#playlist-container").on('click','.showPlaylistDeezerBtn',function(event){
		var playlistElement = $(this).closest('.dz-playlist-item');
		var playlistName = $(this).html();
		var playlistAlias = playlistElement.data('alias');
		$.get(Routing.generate('_deezer_playlist_songs',{'playlistId':playlistElement.data('id')}),function(response){
			if(response.success == true){
				renderResult(response.data.tracks,{tpl:'trackNoSortTpl',tabName:playlistName,alias:playlistAlias});
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

iconMap['dz'] = '/bundles/cogimixdeezer/images/deezer-icon.png';
droppedHookArray['dz-playlist'] = function(droppedItem,callback){
		var playlistId=droppedItem.data('id');
		$.get(Routing.generate('_deezer_playlist_songs',{'playlistId':playlistId}),function(response){
            if(response.success==true){
                loggerDeezer.debug(response.data.tracks);
                callback(response.data.tracks);
                }
            },'json');
	}