<section>

	<?php

	// this page now serves as on demand and scheduled broadcasts

	if($_POST['post_type'] == 'channel'){

		$posts = get_posts( array(
	    'posts_per_page' => -1,
	    'post_type'      => 'channel',
	    'include'        => array($_POST['pid'])

		));

	} else if($_POST['post_type'] == 'broadcast'){ // reverse lookup

		// args
		$args = array(
			'post_type'	=> 'channel',
			'meta_query' => array(
				array(
					'key' => 'channel_playlists_%_channel_broadcasts',
					'value' => '"' . $_POST['pid'] . '"',
					'compare' => 'LIKE'
				)
			)
		);		 
		
		$query = new WP_Query( $args ); 
		$posts = $query->get_posts();		

	}


	if( $posts ) :
	    foreach( $posts as $post ) : ?>

	<div class="chnl-header">

		<div style="background-image:url(<?php echo esc_url(get_field('channel_cover_image')['url']); ?>);"></div>
		<div class="t">
			<div><div>Ch <?php echo get_field('channel_id')+1; ?> - <?php the_title(); ?></div></div>
			<div style="background-image:url(<?php echo esc_url(get_field('channel_logo')['url']); ?>);" class="chnl-logo"></div>
		</div>
		<div><?php the_field('channel_description'); ?></div>			

	</div>

	<div class="chnl-schedule">

		<ul>
			<?php //echo gettype($_POST['onDemand']); ?>
			<?php if($_POST['onDemand'] == 'false'): ?>
				<li><div>Schedule</div></li>	
			<?php else: ?>
				<li><div class="od">On Demand</div></li>
			<?php endif; ?>	
		
			<?php

				$date_now 		= new DateTime("now", new DateTimeZone('Europe/London'));		
				$date_now_ts 	= $date_now->getTimestamp();
				$daylight_saving = $date_now->format('I');

				//update the timestamp depending on BST
				$date_now_ts += $daylight_saving * 3600;	

				$broadcasts 	= array();

				if( have_rows('channel_playlists') ):

				    // Loop through rows.
				    while( have_rows('channel_playlists') ) : the_row();

				        // Load sub field value.				       

				        $playlist_start_date_f 	= get_sub_field('playlist_start_date');
				    	$playlist_start_date 	= new DateTime($playlist_start_date_f);
				       	$playlist_start_date_ts = $playlist_start_date->getTimestamp();
				        $playlist_duration_ts 	= 0;
				        $broadcasts_content 	= get_sub_field('channel_broadcasts');

				    	if($broadcasts_content){

				    		foreach( $broadcasts_content as $broadcast_content ){

				    			$broadcast_duration = get_field('broadcast_duration', $broadcast_content->ID);
				    			$broadcast_type 	= get_field('broadcast_type', $broadcast_content->ID);
				    			$broadcast_type 	= 'main_broadcast';
				    			$broadcast_url 		= get_the_permalink($broadcast_content->ID);
				    			$broadcast_on_demand = get_field('on_demand', $broadcast_content->ID);			
				    			$broadcast_duration_display = '';
				    			$broadcast_mix_title 	= get_field('broadcast_title', $broadcast_content->ID);	    			

				    			sscanf($broadcast_duration, "%d:%d:%d", $hours, $minutes, $seconds);

				    			$broadcast_duration_display_hours = '';
				    			$broadcast_duration_display_mins = '';

				    			$broadcast_duration_display_hours = $hours.'HR';
				    			if($hours>1) $broadcast_duration_display_hours .= 'S';
				    			$broadcast_duration_display_mins = $minutes.'MIN';	
				    			if($minutes>1) $broadcast_duration_display_mins .= 'S';
				    			if($minutes<10) $broadcast_duration_display_mins = '0'.$broadcast_duration_display_mins;

				    			$broadcast_duration_display = '0'.$broadcast_duration_display_hours.' '.$broadcast_duration_display_mins;
				    			

				    			$broadcast_duration_ts = isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;									
				    			$broadcast_title 		= get_the_title($broadcast_content->ID);	

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
						    	$data 								= array("live", $broadcast_title, $playlist_start_date_display, $broadcast_duration, null, null, $broadcast_type, $broadcast_start_time, $broadcast_duration_ts, $broadcast_url, $broadcast_on_demand, $broadcast_duration_display,$broadcast_mix_title);

						    	array_push($broadcasts, $data); 		
														


				    		}
				    	}

				    endwhile;

				endif;	

				endforeach; 

			endif;
			?>

	<?php

	$recurring_start_time_ts 	= $playlist_start_date_ts;
	$start_id 					= 0;
	$broadcast_start_arr 		= [];
	$broadcast_end_arr 			= [];

	while($recurring_start_time_ts < $date_now_ts){	

		for($k =0; $k < count($broadcasts); $k++){ //broadcasts

			$broadcasts[$k][7] 		= $recurring_start_time_ts;
			$playlist_start_date_display 	= date("d.m.Y H:i:s",  $recurring_start_time_ts);
			$broadcasts[$k][2] 		= $playlist_start_date_display;

    		$recurring_start_time_ts += $broadcasts[$k][8];

		}			

	}
		
	for($k =0; $k < count($broadcasts); $k++){ 

		$start_id = $k;

		if($broadcasts[$k][7]< $date_now_ts && $broadcasts[$k][7]+$broadcasts[$k][8] > $date_now_ts){ // now broadcast
			
			break; 
			
		}						
		
	}

	$broadcast_start_arr = array_slice($broadcasts, $start_id);
	$broadcast_end_arr = array_slice($broadcasts, 0, $start_id);

	$broadcasts = [];
	$broadcasts = array_merge($broadcast_start_arr,$broadcast_end_arr);

	foreach ($broadcasts as $broadcast): ?>

		<?php if($_POST['onDemand'] == 'false'): ?>
		<li><?php echo $broadcast[1]; ?></li>
		<?php else: ?>
			<?php if($broadcast[10]): // broadcast is marked as on demand ?>
				<li><a href="<?php echo $broadcast[9]; ?>">
					<div><?php echo $broadcast[1]; ?><?php if(!empty($broadcast[12])):?> - <?php echo $broadcast[12]; ?><?php endif; ?></div>
					<div><?php echo $broadcast[11]; ?></div>
					</a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
		
	<?php endforeach; ?>

	</div>

</section>
