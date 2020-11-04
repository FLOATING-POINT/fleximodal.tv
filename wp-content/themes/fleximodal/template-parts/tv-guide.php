
<?php

	//date_default_timezone_set('Europe/London');

	$date_now 		= new DateTime("now", new DateTimeZone('Europe/London'));		
	$date_now_ts_guide 	= $date_now->getTimestamp();
	$daylight_saving = $date_now->format('I');

	//update the timestamp depending on BST
	$date_now_ts_guide += $daylight_saving * 3600;	

	//$channels_live 		= array();
	$channels_now 		= [];
	$channels_first_start_dates = [];
	$channels_future 		= [];
	$channels_past 		= [];
	$channels_all 		= [];
	$channels 		= [];
	$num_channels = 0;

	$posts = get_posts( array(
	    'posts_per_page' => -1,
	    'post_type'      => 'channel',
	    'meta_key'		=> 'channel_number',
	    'orderby'		=> 'meta_value',
		'order'			=> 'ASC'

	));

	if( $posts ) {
	    foreach( $posts as $post ) {

	    	$channel_id 		= get_field('channel_id');
	    	$channel_number 	= get_field('channel_number');
	    	$channel_id 		= $channel_number -1;
	    	$channel_url		= get_permalink();
	    	$channel_logo_mark	= get_field('channel_logo_schedule');
	    	$playlist_duration_ts 	= 0;

	    	if($channel_id != -1 ) $num_channels++;	
	    	//$channels[] = [];	    	
	    	//if($channel_id  < 5) array_push($channels, []);

	    	if( have_rows('channel_playlists') ):

		    	// Loop through rows.
		    	while( have_rows('channel_playlists') ) : the_row();

		    		if($channel_id !=-1){ 			    			

		    			$playlist_start_date = 0;
		    			//date_default_timezone_set('UTC'); // <!!!! do not remove this - WP stores all dates as UTC but we're working on GMT for the date comparisons. If this changes to anyting other than UTC there will be an offset//
		    			//date_default_timezone_set('Europe/London');
		    			
			    		$playlist_start_date_f 	= get_sub_field('playlist_start_date');
			    		$playlist_start_date 	= new DateTime($playlist_start_date_f);
			    		//$playlist_start_date->setTimezone(new DateTimeZone('Europe/London'));
			       		$playlist_start_date_ts = $playlist_start_date->getTimestamp();//strtotime(get_sub_field('playlist_start_date'));

			       		$playlist_start_date_display = date("d.m.Y H:i", $playlist_start_date_ts);

			       		$playlist_duration_ts 	= 0;
				        $broadcasts_content 	= get_sub_field('channel_broadcasts');

				        if(!isset($channels_first_start_dates[$channel_id])) $channels_first_start_dates[$channel_id] = $playlist_start_date_ts;

				    	if($broadcasts_content){

				    		foreach( $broadcasts_content as $broadcast_content ){

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
		    					
		    					$url 						= get_field('the_broadcast_url',$broadcast_content->ID);
		    					preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
		    			
								$youtube_id 						= $match[1];
								

						    	$playlist_start_date_display 		= date("d.m.Y H:i:s", $broadcast_start_time);
						    	$data 								= array("live", $broadcast_title, $playlist_start_date_display, $broadcast_duration, $channel_url, $channel_logo_mark, $broadcast_type, $broadcast_start_time, $broadcast_duration_ts);
						    		
								$channels_all[$channel_id][] 		=  $data;
		    					
			    			
				    		}
				    	}
				    }

			    endwhile;				   

			endif;

	    }
	}

	wp_reset_postdata();

	$channel_start_ids = [];

	for($i =0; $i < $num_channels; $i++){


			$recurring_start_time_ts 	= $channels_first_start_dates[$i];
			while($recurring_start_time_ts < $date_now_ts_guide){	
				for($k =0; $k < count($channels_all[$i]); $k++){ //broadcasts

					$channels_all[$i][$k][7] 		= $recurring_start_time_ts;
					$playlist_start_date_display 	= date("d.m.Y H:i:s",  $recurring_start_time_ts);
					$channels_all[$i][$k][2] 		= $playlist_start_date_display;

		    		$recurring_start_time_ts += $channels_all[$i][$k][8];

				}			

			}

				
			for($k =0; $k < count($channels_all[$i]); $k++){ 

				$channel_start_ids[$i] = $k;

				if($channels_all[$i][$k][7]< $date_now_ts_guide && $channels_all[$i][$k][7]+$channels_all[$i][$k][8] > $date_now_ts_guide){ // now broadcast
					
					break; 
					
				}						
	    		
			}

	}
	
	


?>

<section class="channels">	
	<div class="channels-header"></div>

	<?php 
	for($i = 0; $i < count($channels_all); $i++): 

		$start_id = $channel_start_ids[$i];
		$next_id = $start_id+1;
		if($next_id >= count($channels_all[$i])) {

			$next_id = 0;
			$channels_all[$i][$next_id][2] = date("d.m.Y H:i:s",  $channels_all[$i][$start_id][7] + $channels_all[$i][$start_id][8]) ;
		}
		?>

	<div class="channel">
		<div class="channel-header">
			<div class="chnl_num"><?php echo $i+1; ?></div>
			<div class="logo"><img src="<?php echo esc_url($channels_all[$i][$start_id][5]); ?>" /></div>
			</div>

					
			<div class="live">
				<div class="type">ON NOW! ON NOW! ON NOW!</div>
			
				
					<a href="<?php echo  $channel[$i][$start_id][4]; ?>" class="<?php echo $channels_all[$i][$start_id][6]; ?>">
						<div class="t"><?php echo $channels_all[$i][$start_id][1]; ?></div><?php //title ?>
						<div class="bt"><div>START</div><div><?php echo $channels_all[$i][$start_id][2]; ?></div></div><?php //broadcast date ?>
						<div class="bd"><div>DURATION</div><div><?php echo $channels_all[$i][$start_id][3]; ?></div></div> <?php //broadcast duration ?>
					</a>
				
			
			</div>
			<div class="future">
				<div class="type">ON NEXT! ON NEXT! ON NEXT!</div>	
				
					<a href="<?php echo $channels_all[$i][$next_id][4]; ?>"  class="<?php echo $channels_all[$i][$next_id][6]; ?>">
						<div class="t"><?php echo $channels_all[$i][$next_id][1]; ?></div> <?php //title ?>
						<div class="bt"><div>START</div><div><?php echo $channels_all[$i][$next_id][2]; ?></div></div> <?php //broadcast time ?>
						<div class="bd"><div>DURATION</div><div><?php echo $channels_all[$i][$next_id][3]; ?></div></div> <?php //broadcast duration ?>
					</a>				

			</div>	

			
	</div>
<?php endfor; ?>
</section>
