<div class="schedule">
		<div class="inner">

    <div id="titlebar" class="green">
    	<div>
    		<a href="<?php echo site_url(); ?>">
		    	<div>P100</div>
		    	<div>fleximodal.tv</div>
		    </a>
	    </div>
	    <div>
	    	<div id="TTcounter"></div>
	    	<div id="TTdate"></div>
	    	<div id="TTtime"></div>
	    </div>
    </div>

    <script type="text/javascript" language="JavaScript">
		<!--
		var initTeletext = function() {
		    window.setInterval(updateClock, 1000);
		    window.setTimeout(updateCounter, 100);
		}
		var addleadingspace = function(number) {
		    return (number < 10 ? " " : "") + number; 
		}
		var addleadingzero = function(number) {
		    return (number < 10 ? "0" : "") + number; 
		}
		var dayofweek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
		var monthname = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		var updateClock = function() {
		    var d = new Date();
		    document.getElementById("TTdate").innerHTML = dayofweek[d.getDay()] + " " + addleadingspace(d.getDate()) + " " + monthname[d.getMonth()];
		    document.getElementById("TTtime").innerHTML = addleadingzero(d.getHours()) + ":" + addleadingzero(d.getMinutes()) + "/" + addleadingzero(d.getSeconds());
		}
		var counter=100;
		var updateCounter = function() {
		    document.getElementById("TTcounter").innerHTML = counter;
		    counter +=1;
		    if (counter > 199) {
		        counter = 100;
		    }  
		    if (Math.random() < 0.05) {
		        window.setTimeout(updateCounter, 1000 + Math.random()*3000);    
		    } else {
		        window.setTimeout(updateCounter, 100);    
		    }
		}
		window.onload = initTeletext;
		//-->
	</script>

	<div class="headlines bluebg yellow">

		<?php if( have_rows('news_headlines') ): 

		 	// loop through the rows of data
		    while ( have_rows('news_headlines') ) : the_row(); ?>    

		       <div>
		       	<div class="msg"><?php  the_sub_field('the_headline'); ?></div>
		       	<div class="pgnum"><?php  the_sub_field('page_number'); ?></div>
		       </div>

		   <?php endwhile; ?>

		<?php endif;  ?>

		
	</div>

	<div class="headline one yellow flashing">Fleximodal.tv</div>

	<div class="welcome white">
		<img src="<?php echo get_template_directory_uri(); ?>/assets/images/face.gif" width=240 height=250 />
		<?php the_field('introduction'); ?>
			
		</div>


	<?php

		$date_now 	= date('Y-m-d H:i:s');
		$live 		= array();
		$upcoming 	= array();
		$finished 	= array();

		$posts = get_posts( array(
		    'posts_per_page' => -1,
		    'post_type'      => 'broadcast',
		    'order'          => 'ASC',
		    'orderby'        => 'meta_value',
		    'meta_key'       => 'broadcast_start_date_and_time',
		    'meta_type'      => 'DATETIME',
		));

		$posts = get_field('channel_broadcasts');

		if( $posts ) {
		    foreach( $posts as $post ) {

		    	$start_date = get_field('broadcast_start_date_and_time');
		    	$end_date 	= get_field('broadcast_end_date_and_time');
		    	$title 		= get_the_title();
		    	
		    	for($i=mb_strlen($title); $i < 50; $i++){
		    		$title .='.';

		    	}
		    	$url 		= get_permalink();

		    	$data = array($title, $url, $start_date, $end_date);

		    	if($start_date<= $date_now && $end_date >= $date_now){
		    		// live
		    		array_push($live, $data);
		    	} else if($end_date <= $date_now){
		    		// finished
		    		array_push($finished, $data);
		    	} else{
		    		// upcoming
		    		array_push($upcoming, $data);
		    	}
	    	
		    }
		}
	?>
	
	<?php if(count($live)>0): ?>
	<section class="flashing">
		<div>
			<div class="header  white"><span class="">Live now !!</span></div>				
			<div class="broadcasts">
				<?php foreach($live as $data): ?>
					<div>
						<a href="<?php echo $data[1]; ?>">
							<div class="t"><?php echo $data[0]; ?></div>
							<div class="c"></div>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
			
			
		</div>
	</section>
	<?php endif; ?>

	<section>
		<div>
			<div class="header white">Upcoming</div>
			
			<div class="broadcasts">
				<?php if(count($upcoming)>0): ?>
				<?php foreach($upcoming as $data): ?>
					<div>
						<a href="<?php echo $data[1]; ?>">
							<div class="t"><?php echo $data[0]; ?></div>
							<div class="c"></div>
						</a>
					</div>
				<?php endforeach; ?>
				<?php else: ?>
					<div>Check back for upcoming broadcasts.............................</div>
				<?php endif; ?>
			</div>				
		</div>
	</section>

	<section>
		<div>
			<div class="header white">Watch again</div>
			<?php if(count($finished)>0): ?>
			<div class="broadcasts">
				<?php foreach($finished as $data): ?>
					<div>
						<a href="<?php echo $data[1]; ?>">
							<div class="t"><?php echo $data[0]; ?></div>
							<div class="c"></div>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
			<?php else: ?>
			<?php endif; ?>
		</div>
	</section>

	<div class="section-inner">

		<div class="footer-credits">
			<div class="headlines bluebg white">Fleximodal.tv : The world at your fingertips </div>

			<p class="footer-copyright">&copy;
				<?php
				echo date_i18n(
					/* translators: Copyright date format, see https://secure.php.net/date */
					_x( 'Y', 'copyright date format', 'twentytwenty' )
				);
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a> - 
				<a href="https://www.wigflex.com/">Wigflex</a> - 
				<a href="https://multimodal.live/">Multimodal</a>
			</p><!-- .footer-copyright -->

			

		</div><!-- .footer-credits -->
	</div>

	</div>
</div>