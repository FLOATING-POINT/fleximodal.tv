<?php
/**
 * The default template for displaying content
 *
 * Used for both singular and index.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

?>

<article <?php post_class(); ?> id="channel">


	<div class="loader">
		<div class="inner">
			<div class="p1"></div>
			<div class="p2"></div>
			<div class="p3"></div>
			<div class="p4"></div>
			<div class="p5"></div>
		</div>
	</div>

	
	<div class="tv-guide">
		<div class="mb-close-btn">CLOSE</div>
		<div class="inner">

		<?php get_template_part('template-parts/tv-guide'); ?>
		</div>
	</div>
	<div class="schedule" id="schedule">
		<div class="mb mb-close-btn">CLOSE</div>
		<div class="inner"><section></section>
		<?php //get_template_part('template-parts/schedule'); ?>
		</div>
	</div>
	<div class="schedule" id="on-demand-schedule">
		<div class="mb mb-close-btn">CLOSE</div>
		<div class="inner"><section></section>
		<?php //get_template_part('template-parts/schedule'); ?>
		</div>
	</div>
	<?php get_template_part('template-parts/about'); ?>

	<div class="fm-logo-back"></div>

	<?php get_template_part('template-parts/channel-controls'); ?>	

	<div class="section-inner on-demand" id="content">

		<div class="logo-pause">
			<div>
				<div>
					<?php 
					$image = get_field('channel_logo');
					if( !empty( $image ) ): ?>
					    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" title="Channel <?php echo esc_attr($image['title']); ?>" />
					<?php else: ?>
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/Fleximodal_TV_Logo_2_white.svg" alt="FLEXIMODAL.TV" title="FLEXIMODAL.TV" />
					<?php endif; ?>

				</div>
				<div>Click to resume</div>
			</div>
		</div>		

		<div id="iframe-youtube-player" ></div>	

		

	<?php


	$headlines = array();

	if( have_rows('broadcast_headlines') ): 

		while( have_rows('broadcast_headlines') ): the_row();

			array_push($headlines,get_sub_field('the_headline'));	   					

		endwhile;

	endif;


	$url = get_field('the_broadcast_url');
	$youtube_url = $url;


	preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
	$youtube_id = $match[1];


	?>



	      <script>

	      	(function($){	

				jQuery(document).ready(function($){

					var headlines = <?php echo json_encode($headlines); ?>;
					console.log("headlines "+headlines);

					var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

					var ua = window.navigator.userAgent;
					var iOS = !!ua.match(/iPad/i) || !!ua.match(/iPhone/i);
					var webkit = !!ua.match(/WebKit/i);
					var iOSSafari = iOS && webkit && !ua.match(/CriOS/i);	


				      var tag = document.createElement('script');

				      tag.src = "https://www.youtube.com/iframe_api";
				      var firstScriptTag = document.getElementsByTagName('script')[0];
				      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

				     function YouTubeGetID(url){
					  var ID = '';
					  url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
					  if(url[2] !== undefined) {
					    ID = url[2].split(/[^0-9a-z_\-]/i);
					    ID = ID[0];
					  }
					  else {
					    ID = url;
					  }
					    return ID;
					}

					var video_id = YouTubeGetID('<?php echo $youtube_url; ?>');
					var player;
				    var iframe = $('iframe-youtube-player');

				    window.onYouTubeIframeAPIReady = function() {

				        player = new YT.Player('iframe-youtube-player', {
				          height: '100%',
				          width: '100%',
				          videoId: video_id,
				          playerVars: { 'autoplay': 0, 'controls': 0, 'modestbranding':1,'playsinline':1, 'autohide':1,'wmode':'opaque', 'loop':1 },
				          events: {
				            'onReady': onPlayerReady,
				            'onStateChange': onPlayerStateChange
				          }
				        }); 				       
				      }

				      window.setTicker = function(){

				      	$('.ticker').empty();

					    for(var i = 0; i < headlines.length; i++) {

	    					$('.ticker').append('<div class="ticker__item" <span>'+headlines[i]+'</span></div>')

	    				}
	    				
					}
					setTicker();
				     

				    window.playNextVideo = function(){

				      	//
				      	var start_secs = 0;
				      	var end_secs = -1;

				      	for(var i = 0; i < pl.length; i++) {
	    					
	    					if(pl[i][0] == video_id){	  	

	    						if(i == pl.length-1){
	    							video_id 	= pl[0][0];
	    							start_secs 	= pl[0][3];
	    							end_secs 	= pl[0][4];
	    							break;   

	    						} else {
	    							video_id  	= pl[i+1][0];
	    							start_secs 	= pl[i+1][3];
	    							end_secs 	= pl[i+1][4];
	    							break;   
	    							
	    						}	    						
	    					}	    					
	    				}	

	    				player.seekTo(0); 
						player.stopVideo();

	    				if(end_secs != -1){
							player.loadVideoById({'videoId': video_id, 'startSeconds': start_secs, 'endSeconds': end_secs});
							
	    				}else if(start_secs !=0){
	    					player.loadVideoById({'videoId': video_id, 'startSeconds': start_secs});	    					
	    					
	    				} else{
	    					player.loadVideoById({'videoId': video_id,'startSeconds': 0});
	    					
	    				}
	    				
	    				setTicker();
	    				
				    }


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

					window.showPlayerLogo = function(){

						$('.logo-pause').css({'visibility':'visible'});
					    var m_l = $('.logo-pause > div').width()*-.5;
						var m_t = $('.logo-pause > div').outerHeight()*-.5;							
						$('.logo-pause > div').css({'margin-top':m_t+'px', 'margin-left':m_l+'px'});

					}
					window.hidePlayerLogo = function(){
						$('.logo-pause').css({'visibility':'hidden'});
					}

					$(document).on('click', '#content', function(){  

						console.log('on-demand '+$('#content').hasClass('on-demand'));

						if($('#content').hasClass('on-demand')){

							if(player.getPlayerState() == 1 ){ // video playing
								player.pauseVideo();
								showPlayerLogo();
							} else{
								player.playVideo();
								hidePlayerLogo();
							}

						} else {

							if(!player.isMuted() ){
								player.mute();
								showPlayerLogo();
							}else {
								player.playVideo();
								player.unMute();
								hidelayerLogo();
							}	
					}

					});


					<?php include_once(get_template_directory().'/template-parts/tv-js.js'); ?>
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

			})(jQuery);



		    </script>


	</div>

</article>
