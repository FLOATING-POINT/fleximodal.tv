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
		<div class="mb-close-btn">CLOSE</div>
		<div class="inner"><section></section>
		<?php //this is loaded via ajax from tv-guide.php ?>
		</div>
	</div>
	<div class="schedule" id="on-demand-schedule">
		<div class="mb-close-btn">CLOSE</div>
		<div class="inner"><section></section>
		<?php //this is loaded via ajax from tv-guide.php ?>
		</div>
	</div>
	<?php get_template_part('template-parts/about'); ?>

	<div class="fm-logo-back"></div>


	<?php get_template_part('template-parts/channel-controls'); ?>	

	<div class="section-inner" id="content">

		<div class="logo-pause">
			<div>
				<div>
					<?php 
					$image = get_field('channel_logo');
					if( !empty( $image ) ): ?>
					    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" title="Channel <?php echo esc_attr($image['title']); ?>" />
					<?php endif; ?>

				</div>
				<div>Click to resume</div>
			</div>
		</div>		

		<div id="iframe-youtube-player" ></div>

		<?php

			$date_now 		= new DateTime("now", new DateTimeZone('Europe/London'));	
			$date_now->setTimezone(new DateTimeZone('Europe/London'));	
			$date_now_ts 	= $date_now->getTimestamp();
			$daylight_saving = $date_now->format('I');

			//update the timestamp depending on BST
			$date_now_ts += $daylight_saving * 3600;	
			//echo 'date_now_ts date '.date("d.m.Y H:i:s", $date_now_ts).'<br />';
			$broadcasts 		= [];			
	    	$youtube_url 		= '';
	    	$start_secs 		= 0;
			$end_secs 			= 0;

			$playlist_start_date_ts 		= 0;
			$playlist_first_start_date_ts 	=  0;


	    	if( have_rows('channel_playlists') ):

		    	// Loop through rows.
		    	while( have_rows('channel_playlists') ) : the_row();

		    		$playlist_start_date_f 	= get_sub_field('playlist_start_date');
				   	$playlist_start_date 	= new DateTime($playlist_start_date_f);

				    $playlist_start_date_ts = $playlist_start_date->getTimestamp();

		       		$playlist_start_date_display = date("d.m.Y H:i", $playlist_start_date_ts);

		       		$playlist_duration_ts 	= 0;
			        $broadcasts_content 	= get_sub_field('channel_broadcasts');		

			        if($playlist_first_start_date_ts == 0) $playlist_first_start_date_ts = 	 $playlist_start_date_ts;     

			    	if($broadcasts_content){

			    		foreach( $broadcasts_content as $broadcast_content ){

			    			$headlines = array();

			    			if( have_rows('broadcast_headlines',$broadcast_content->ID) ): 

			    				while( have_rows('broadcast_headlines',$broadcast_content->ID) ): the_row();

			    					array_push($headlines,get_sub_field('the_headline',$broadcast_content->ID) );	   					

			    				endwhile;

			    			endif;

							$broadcast_video_start_sec 	= 0 ;
							$broadcast_video_end_sec = 0;


			    			$broadcast_duration = get_field('broadcast_duration', $broadcast_content->ID);
			    			$broadcast_type 	= get_field('broadcast_type', $broadcast_content->ID);

			    			$broadcast_type 	= 'main_broadcast';

			    			sscanf($broadcast_duration, "%d:%d:%d", $hours, $minutes, $seconds);

			    			$broadcast_duration_ts = isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;									
			    			$broadcast_title 			= get_the_title($broadcast_content->ID);	

			    			$broadcast_video_start 	= get_field('start_time', $broadcast_content->ID);
	    					$broadcast_video_end 	= get_field('end_time', $broadcast_content->ID);	

	    					if(isset($broadcast_video_start) && isset($broadcast_video_end)){

			    				sscanf($broadcast_video_start, "%d:%d:%d", $hours_s, $minutes_s, $seconds_s);
								$broadcast_video_start_sec 	= isset($hours_s) ? $hours_s * 3600 + $minutes_s * 60 + $seconds_s : $minutes_s * 60 + $seconds_s;	
			    			
			    				sscanf($broadcast_video_end, "%d:%d:%d", $hours_e, $minutes_e, $seconds_e);
			    				$broadcast_video_end_sec 		= isset($hours_e) ? $hours_e * 3600 + $minutes_e * 60 + $seconds_e : $minutes_e * 60 + $seconds_e;

			    			} else {
			    				$broadcast_video_start_sec = 0;
								$broadcast_video_end_sec = -1;
			    			}

			    			$broadcast_start_time 		= $playlist_start_date_ts + $playlist_duration_ts;
	    					$broadcast_end_time 		= $broadcast_start_time + $broadcast_duration_ts;
	    					$playlist_duration_ts 		+= $broadcast_duration_ts;	

			    			$broadcast_title 			= get_the_title($broadcast_content->ID);			    				
			    			$url 						= get_field('the_broadcast_url',$broadcast_content->ID);
			    			preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
			    			
							$youtube_id = $match[1];

							array_push($broadcasts, array($youtube_id,$broadcast_start_time, $broadcast_end_time, $broadcast_video_start_sec, $broadcast_video_end_sec, $headlines, $url, $broadcast_duration_ts));															

							if($broadcast_start_time <  $date_now_ts && $broadcast_end_time > $date_now_ts){ // current video

								$youtube_url = $url;
								$start_secs = $date_now_ts - $broadcast_start_time + $broadcast_video_start_sec;
								$end_secs = $broadcast_video_end_sec;
	
							}	


			    		}
			    	}

			    endwhile;

			endif;

			$recurring_start_time_ts = $playlist_first_start_date_ts;

			$broadcast_id = 0;

			if(empty($youtube_url)){ // looping playlists


				while($recurring_start_time_ts < $date_now_ts){			
					
					$youtube_id 		= $broadcasts[$broadcast_id][0];
					$youtube_url 		= $broadcasts[$broadcast_id][6];				
					$start_secs 		= $date_now_ts - $recurring_start_time_ts + $broadcasts[$broadcast_id][3];
					$end_secs 			= $broadcasts[$broadcast_id][4];

					$recurring_start_time_ts += $broadcasts[$broadcast_id][7];

					$broadcast_id++;

					if($broadcast_id == count($broadcasts)){
						$broadcast_id = 0;
					}

				}				

			}	?>

	      <script>

	      	(function($){	

				jQuery(document).ready(function($){


					var pl = <?php echo json_encode($broadcasts); ?>;
					var pl_s = <?php echo $playlist_start_date_ts; ?>;

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
				          playerVars: { 'autoplay': 1, 'controls': 0, 'modestbranding':1,'playsinline':1, 'autohide':1,'wmode':'opaque', 'loop':0 <?php if($start_secs > 0): ?>, 'start':<?php echo $start_secs; ?> <?php endif; ?> <?php if($end_secs > 0): ?>, 'end':<?php echo $end_secs; ?> <?php endif; ?> },
				          events: {
				            'onReady': onPlayerReady,
				            'onStateChange': onPlayerStateChange
				          }
				        }); 				        
				        
				     }		   



				      window.setTicker = function(){

				      	$('.ticker').empty();

					    for(var i = 0; i < pl.length; i++) {
	    					
	    					if(pl[i][0] == video_id){	  			

	    						for(var k = 0; k < pl[i][5].length; k++){

	    							$('.ticker').append('<div class="ticker__item" <span>'+pl[i][5][k]+'</span></div>')

	    						}
	    					}
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
						setTimeout(function(){
							player.playVideo();

						}, 1000);

				    }
			      
				    window.onPlayerStateChange = function(event) {

				      	if(event.data == YT.PlayerState.PLAYING){

				      	} else if (event.data == YT.PlayerState.ENDED) {
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
				    window.pauseVideo = function() {
				        player.pauseVideo();
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

					<?php include_once(get_template_directory().'/template-parts/tv-js.js'); ?>

			});

 
			$(window).resize(function(){

				var m_l = $('.logo-pause > div').width()*-.5;
				var m_t = $('.logo-pause > div').outerHeight()*-.5;

				$('.logo-pause > div').css({'margin-top':m_t+'px', 'margin-left':m_l+'px'});

				setTimeout(function(){	$('#iframe-youtube-player').css({'width':'120%'}); }, 100);

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
