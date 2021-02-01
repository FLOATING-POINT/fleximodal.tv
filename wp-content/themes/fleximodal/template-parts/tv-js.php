	    window.onPlayerReady = function(event) {

			setTicker();
			player.playVideo();

	    }
      
	    window.onPlayerStateChange = function(event) {

	      	if(event.data == YT.PlayerState.PLAYING){

	      	} else if (event.data == YT.PlayerState.ENDED) {
	      		//console.log("YT.PlayerState.ENDED");
	      		playNextVideo(); //<-- this is causing lots of issues

	        } else if (event.data == YT.PlayerState.PAUSED){				        	
	        	$('.logo-pause').css({'visibility':'visible'});
	        	var m_l = $('.logo-pause > div').width()*-.5;
				var m_t = $('.logo-pause > div').outerHeight()*-.5;							
				$('.logo-pause > div').css({'margin-top':m_t+'px', 'margin-left':m_l+'px'});
	        } else if(event.data == YT.PlayerState.CUED){				        	

	        }
	      }
	      window.stopVideo = function() {
	        player.stopVideo();
	      }
			
		window.playFullscreen = function(){

			player.playVideo();
			var e = document.getElementById("content");
		    if (e.requestFullscreen) {
		        e.requestFullscreen();
		    } else if (e.webkitRequestFullscreen) {
		        e.webkitRequestFullscreen();
		    } else if (e.mozRequestFullScreen) {
		        e.mozRequestFullScreen();
		    } else if (e.msRequestFullscreen) {
		        e.msRequestFullscreen();
		    }
		}
		$('.fullscreen-btn').click(function(){
			playFullscreen();
		});

		$(document).on('click', '#content', function(){  

			var state = player.getPlayerState();

			if(!player.isMuted() ){
				//player.pauseVideo();
				player.mute();
				$('.logo-pause').css({'visibility':'visible'});
	        	var m_l = $('.logo-pause > div').width()*-.5;
				var m_t = $('.logo-pause > div').outerHeight()*-.5;							
				$('.logo-pause > div').css({'margin-top':m_t+'px', 'margin-left':m_l+'px'});
			}else {
				player.playVideo();
				player.unMute();
				$('.logo-pause').css({'visibility':'hidden'});
			}	

		});
		window.hideAbout = function(){
				$('.aboutFMTV').css('display','none');
				$('.btn.about').removeClass('active');
				
				$(document).scrollTop(0);
		}
		window.hideTVGuide = function(){
			$('.tv-guide').css('display','none');
			$('.tv-guide-btn').removeClass('active');
			$(document).scrollTop(0);
		}
		window.hideSchedule = function(){
			$('.schedule').css('display','none');
			$('.schedule-btn').removeClass('active');
			$(document).scrollTop(0);
		}
		window.hideOnDemand = function(){
			$('.schedule').css('display','none');
			$('.on-demand-btn').removeClass('active');
			$(document).scrollTop(0);
		}
		$('.logo-pause > div:first-child').click(function(){
			$('.logo-pause').css({'visibility':'hidden'});
			//player.playVideo();
			player.unMute();
		});

		$('.tv-guide-btn').click(function(){

			if ( $('.tv-guide').css('display') == 'none' ){

				$('.tv-guide').css('display','block');		
				$('.tv-guide-btn').addClass('active');					 

				var request = $.ajax({
				  url: ajaxurl,
				  type: "POST",
				  data: {action: 'get_tv_guide'},
				  dataType: "html",
				   success: function(html){
					    $( ".tv-guide .inner .channels" ).replaceWith(html);
			    	

					  }
				});			    

			    
				hideAbout();
				hideSchedule();
				hideOnDemand();

			 } else{
			    $('.tv-guide').css('display','none');
			    $('.tv-guide-btn').removeClass('active');
			}
			
		});		

		$('.btn.about').click(function(){

			if ( $('.aboutFMTV').css('display') == 'none' ){
			    $('.aboutFMTV').css('display','block');
			    $('.btn.about').addClass('active');
			    
				hideTVGuide();
				hideSchedule();
				hideOnDemand();
			 } else{
			    $('.aboutFMTV').css('display','none');
			    $('.btn.about').removeClass('active');
			}
			
		});	

		$('.schedule-btn').click(function(){

			if ( $('.schedule').css('display') == 'none' ){

				$('.schedule-btn').addClass('active');

				var request = $.ajax({
				url: ajaxurl,
				type: "POST",
			    data: {action: 'get_schedule', pid:<?php echo get_the_ID();?>, post_type:"<?php echo get_post_type();?>" },
				  dataType: "html",
				   success: function(html){
				   
					    $( ".schedule .inner section" ).replaceWith(html);
			    		$('.schedule').css('display','block');
			    		

					}
				});	

				hideTVGuide();
				hideAbout();
				hideOnDemand();
			  } else{
			    $('.schedule').css('display','none');
			    $('.schedule-btn').removeClass('active');
			}
			
		});		   

		$('.mb-close-btn').click(function(){
			hideTVGuide();
			hideAbout();
			hideSchedule();
		});

});


$(window).resize(function(){

	var m_l = $('.logo-pause > div').width()*-.5;
	var m_t = $('.logo-pause > div').outerHeight()*-.5;

	$('.logo-pause > div').css({'margin-top':m_t+'px', 'margin-left':m_l+'px'});

	setTimeout(function(){	$('#iframe-youtube-player').css({'width':'110%'}); }, 100);

	var h = $(window).height() - $('.fm-logo-menu').outerHeight();
	$('nav div.mb').height(h);

});		

$(window).resize();	


function centerImage(parent, img){

	 	var parent_opacity = $(parent).css('opacity');

    var w = $(parent).outerWidth();
    var h = $(parent).outerHeight();

    var i_w = $(img).outerWidth();
    var i_h = $(img).outerHeight();

    var ar = i_w/i_h;
    
    var l_m = Math.ceil((w - i_w)*.5);
    var t_m = Math.ceil((h - i_h)*.5);

    if(i_h < h){

        i_w = $(img).width();
        i_h = $(img).height();

        l_m = Math.ceil((w - i_w)*.5);
        t_m = Math.ceil((h - i_h)*.5);
    }


    $(img).css({'margin-left':l_m+'px', 'margin-top':t_m+'px'});

}
