<?php
/**
 * Header file for the Twenty Twenty WordPress default theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

?><!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri();?>/assets/images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri();?>/assets/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri();?>/assets/images/favicon-16x16.png">
		<link rel="manifest" href="<?php echo get_template_directory_uri();?>/assets/images/site.webmanifest">
		<link rel="mask-icon" href="<?php echo get_template_directory_uri();?>/assets/images/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="msapplication-TileColor" content="#da532c">

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?> id="<?php the_title(); ?>">

		<?php
		wp_body_open();

		global $broadcast_channel_id; // this in used for single broadcasts

		?>
		<div class="dt">
			<nav>
				<div class="fm-logo-menu"><a href="https://fleximodal.tv"></a></div>

				<div class="mb">

					<div class="separator"><div></div></div>

					<div>				

					<div class="channels-controller">

					<?php

						$channel_post_id = get_the_ID();

						global $post;

						$channels = get_posts( array(
						    'posts_per_page' => -1,
						    'post_type'      => 'channel',
						    'order'          => 'ASC',
						));
						?>

						<?php  if( $channels ): ?>
							<div class="channel-menu" id="c-menu">

						   <?php  foreach( $channels as $post ): ?>

						   	<?php setup_postdata($post); ?>				   	

						   	<?php $image = get_field('channel_logo'); ?>

						   	<?php if(get_field('channel_id') !=-1): ?>

						   		<?php 
						   			$pid = get_the_ID(); 

						   			$class = '';
						   			if($pid == $channel_post_id) $class = 'active';

						   		?>

						   		<div class="<?php echo $class; ?>">
						   			<a href="<?php echo the_permalink(); ?>"><div><?php the_field('channel_number'); ?></div></a>
						   		</div>

						   	<?php endif; ?>

						   <?php endforeach; ?>
						   <?php wp_reset_postdata(); ?>
						   	</div>
						<?php endif; ?>	
							<div class="btn tv-guide-btn"><div class="">TV GUIDE</div></div>
							<div class="btn about"><div class="">?</div></div>
						</div>	

					</div>							

					<div class="social">
						<a href="https://www.instagram.com/fleximodal_tv/" target="_blank" class="insta"></a>						
					</div>

					
					<div class="crew">
						<a href="https://www.wigflex.com/" target="_blank" class="wigflex"></a>
						<a href="https://multimodal.live/" target="_blank" class="multimodal"></a>
					</div>

					
				</div>
			</nav>
		</div>
		<div class="m">
			<div class="fm-logo-menu"><a href="<?php echo get_site_url(); ?>"></a></div>
		</div>

		<div class="mb">							

			<div class="channels-controller">

			<?php

				global $post;

				$channels = get_posts( array(
				    'posts_per_page' => -1,
				    'post_type'      => 'channel',
				    'order'          => 'ASC',
				));
				?>

				<?php  if( $channels ): ?>
					<div class="channel-menu" id="c-menu">

				   <?php  foreach( $channels as $post ): ?>

				   	<?php setup_postdata($post); ?>				   	

				   	<?php $image = get_field('channel_logo'); ?>

				   	<?php if(get_field('channel_id') !=-1): ?>

				   		<?php 
				   			$pid = get_the_ID(); 

				   			$class = '';
				   			if($pid == $channel_post_id) $class = 'active';

				   		?>

				   		<div class="<?php echo $class; ?>">
				   			<a href="<?php echo the_permalink(); ?>"><div><?php the_field('channel_number'); ?></div></a>
				   		</div>

				   	<?php endif; ?>

				   <?php endforeach; ?>
				   <?php wp_reset_postdata(); ?>
				   	</div>
				<?php endif; ?>	
					<div class="gen-btns">
						<div class="btn tv-guide-btn"><div class="">TV GUIDE</div></div>
						<div class="btn about"><div class="">?</div></div>
					</div>
				</div>	

									
				
			<div class="crew">
				<a href="https://www.instagram.com/fleximodal_tv/" target="_blank" class="insta"></a>	
				<a href="https://www.wigflex.com/" target="_blank" class="wigflex"></a>
				<a href="https://multimodal.live/" target="_blank" class="multimodal"></a>
			</div>

			</div>			
			
		</div>

		<header class="header">

				<?php  if(get_post_type() == 'broadcast'): ?>

				<div class="lbl on-demand"><span>ON DEMAND</span></div>

				<?php else: ?>

					<div class="lbl live"><span>LIVE</span></div>
				<?php endif; ?>

				<div class="channel-msgs">

					<div class="ticker">

				<?php

				    // Loop through rows.
				    while( have_rows('scrolling_news') ) : the_row();

				        ?>

				        <div class="ticker__item" data-time="<?php the_sub_field('headline_display_duration');?>"><span><?php the_sub_field('the_news_headline'); ?></span></div>

				        <?php
				        // Do something...

				    // End loop.
				    endwhile; ?>

				    </div>
				</div>

				<?php //endif; ?>

				<div id="channel-logo-mark">

					<?php  if(get_post_type() == 'broadcast'): ?>

					<?php 					

					// args
					$args = array(
						'post_type'	=> 'channel',
						'meta_query' => array(
							array(
								'key' => 'channel_playlists_%_channel_broadcasts',
								'value' => '"' . get_the_ID() . '"',
								'compare' => 'LIKE'
							)
						)
					);
					 
					
					$the_query = new WP_Query( $args ); ?>			
					

					<?php if( $the_query->have_posts() ): ?>
						
						<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

							<?php $broadcast_channel_id = get_the_ID(); ?>


							<?php $image = get_field('channel_logo_mark');
							if( !empty( $image ) ): ?>
							    <span><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" title="Channel <?php echo esc_attr($image['title']); ?>" /></span>
							<?php endif; ?>

						<?php endwhile; ?>
						
					<?php endif; ?>
					 
					<?php wp_reset_query();  // Restore global post data stomped by the_post(). ?>

					<?php else: ?>

						<?php 
						$image = get_field('channel_logo_mark');
						if( !empty( $image ) ): ?>
						    <span><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" title="Channel <?php echo esc_attr($image['title']); ?>" /></span>
						<?php endif; ?>

					<?php endif; ?>
				</div>		
		</header>