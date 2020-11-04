<?php
/*
Plugin Name: Custom Admin Styles
Plugin URI: 
Description: Colour scheme for admin
Author: Katapult
Version: 1.0
Author URI: katapult.co.uk
*/

function my_admin_theme_style() {
	if (is_user_logged_in() || $GLOBALS['pagenow'] == 'wp-login.php') {
   	wp_enqueue_style('my-admin-theme', plugins_url('custom-admin-styles.css', __FILE__));
   }
}
add_action('admin_enqueue_scripts', 'my_admin_theme_style');
add_action('login_enqueue_scripts', 'my_admin_theme_style');
add_action('wp_enqueue_scripts', 'my_admin_theme_style');	

?>