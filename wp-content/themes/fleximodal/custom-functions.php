<?php

/*-------------------------------------
  Move the Yoast SEO Meta Box to the Bottom of the edit screen in WordPress
---------------------------------------*/
function yoasttobottom() {
  return 'low';
}
add_filter( 'wpseo_metabox_prio', 'yoasttobottom');

show_admin_bar( false );

add_filter( 'get_pages', function ( $pages, $args )
{
    // First make sure this is an admin page, if not, bail
    if ( !is_admin() )
        return $pages;

    // Make sure that we are on the reading settings page, if not, bail
    global $pagenow;
    if ( 'options-reading.php' !== $pagenow )
        return $pages;

    // Remove the filter to avoid infinite loop
    remove_filter( current_filter(), __FUNCTION__ );

    $args = [
        'post_type'      => 'channel',
        'posts_per_page' => -1
    ];
    // Get the post type posts with get_posts to allow non hierarchical post types
    $new_pages = get_posts( $args );    

    /**
     * You need to decide if you want to add custom post type posts to the pages
     * already in the dropdown, or just want the custom post type posts in
     * the dropdown. I will handle both, just remove what is not needed
     */
    // If we only need custom post types
    $pages = $new_pages;

    // If we need to add custom post type posts to the pages
    // $pages = array_merge( $new_pages, $pages );

    return $pages;
}, 10, 2 );

function add_ajax_library() {
 
    $html = '<script type="text/javascript">';

        $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"; ';
       // $html .= 'var templateurl = "' . get_template_directory_uri() . '"; ';
        $html .=' var pid = "'.get_the_ID() .'"; ';

    $html .= '</script>';
 
    echo $html;
 
}
add_action('wp_head', 'add_ajax_library' );


function get_tv_guide(){    

    echo get_template_part('template-parts/tv-guide');

    die(); 

}


add_action('wp_ajax_get_tv_guide', 'get_tv_guide'); // for admin logged in
add_action('wp_ajax_nopriv_get_tv_guide', 'get_tv_guide'); // for general public*/

function get_schedule(){    

    echo get_template_part('template-parts/schedule');

    die(); 

}

add_action('wp_ajax_get_schedule', 'get_schedule'); // for admin logged in
add_action('wp_ajax_nopriv_get_schedule', 'get_schedule'); // for general public*/


function custom_1245_login_logo() { ?>
    <style type="text/css">

        body.login{
            background:#000;
            color:#fff;
        }
        .login form{
            background:#000;
            border-radius:4px;
            
        }
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/images/Fleximodal_TV_Logo_1_white.svg);
    height:70px;
    width:300px;
    background-size: 300px 70px;
    background-repeat: no-repeat;
    padding-bottom: 20px;
        }
    </style>
<?php }
add_action( 'login_head', 'custom_1245_login_logo' );

/*
Hide Built in post types
*/
add_action( 'admin_menu', 'remove_default_admin_types' );

function remove_default_admin_types() {
    remove_menu_page( 'edit.php' );
    remove_menu_page( 'link-manager.php' );


}

add_action( 'admin_bar_menu', 'remove_default_post_type_menu_bar', 999 );
function remove_default_post_type_menu_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'new-post' );
}

add_action( 'wp_dashboard_setup', 'remove_draft_widget', 999 );
function remove_draft_widget(){
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
}


// ACF Fix
add_filter( 'acf/settings/remove_wp_meta_box', '__return_false', PHP_INT_MAX, 1 ); 
/* Custom php functions 

*/

// 'channel' CPT
add_action( 'init', 'register_cpt_channel' );

function register_cpt_channel() {

    $labels = array( 
        'name' => _x( 'Channels', 'channel' ),
        'singular_name' => _x( 'Channels', 'channel' ),
        'add_new' => _x( 'Add a new Channel', 'channel' ),
        'add_new_item' => _x( 'Add a new Channel', 'channel' ),
        'edit_item' => _x( 'Edit the Channel', 'channel' ),
        'new_item' => _x( 'New Channel', 'channel' ),
        'view_item' => _x( 'View Channel', 'channel' ),
        'search_items' => _x( 'Search Channels', 'channel' ),
        'not_found' => _x( 'No Channels found', 'channel' ),
        'not_found_in_trash' => _x( 'No Channels found in Trash', 'channel' ),
        'parent_item_colon' => _x( 'Parent Channel:', 'channel' ),
        'menu_name' => _x( 'Channels', 'channel' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Channels',
        'supports' => array('title','page-attributes' ),
        'taxonomies' => array(),
        
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 10,
        'menu_icon' => 'dashicons-editor-video',
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug' => 'channel'),
        'capability_type' => 'post'
    );

    register_post_type( 'channel', $args );
   
    
}

// 'broadcasts' CPT
add_action( 'init', 'register_cpt_broadcasts' );

function register_cpt_broadcasts() {

    $labels = array( 
        'name' => _x( 'Broadcasts', 'broadcast' ),
        'singular_name' => _x( 'Broadcasts', 'broadcast' ),
        'add_new' => _x( 'Add a new Broadcast', 'broadcast' ),
        'add_new_item' => _x( 'Add a new Broadcast', 'broadcast' ),
        'edit_item' => _x( 'Edit the Broadcast', 'broadcast' ),
        'new_item' => _x( 'New Broadcast', 'broadcast' ),
        'view_item' => _x( 'View Broadcast', 'broadcast' ),
        'search_items' => _x( 'Search Broadcasts', 'broadcast' ),
        'not_found' => _x( 'No Broadcasts found', 'channel' ),
        'not_found_in_trash' => _x( 'No Broadcasts found in Trash', 'broadcast' ),
        'parent_item_colon' => _x( 'Parent Broadcast:', 'broadcast' ),
        'menu_name' => _x( 'Broadcasts', 'broadcast' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Broadcasts',
        'supports' => array('title' ),
        'taxonomies' => array(),
        
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 10,
        'menu_icon' => 'dashicons-admin-media',
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug' => 'broadcast'),
        'capability_type' => 'post'
    );

    register_post_type( 'broadcast', $args );
   
    
}
/*
function remove_customise_theme_menus(){
    remove_submenu_page( 'themes.php', 'customize.php' );
    remove_submenu_page( 'themes.php', 'themes.php' );
    remove_submenu_page( 'themes.php', 'theme-editor.php' ); 

}
add_action( 'admin_init', 'remove_customise_theme_menus' );
*/
function setup(){

 /* javascript */
  wp_register_script( 'TweenMax', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/1.16.1/TweenMax.min.js', array('jquery'));       
  wp_enqueue_script( 'TweenMax'); 

  wp_register_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/1.14.2/jquery.gsap.min.js', array('jquery'));       
  wp_enqueue_script( 'gsap'); 
 

  wp_register_script( 'easePack', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/latest/easing/EasePack.min.js', array('jquery'));       
  wp_enqueue_script( 'easePack');

  wp_register_script( 'a-js', get_template_directory_uri() . '/assets/js/app.js', array('jquery'));
  wp_enqueue_script( 'a-js' );

  /* CSS */

  wp_enqueue_style( 'reset', get_template_directory_uri() . '/assets/css/reset.css', array(), '1.0', 'screen, projection' );

  global $template;

  if(basename($template) == 'template-tv.php' || basename($template) == 'template-channels.php' ){
    wp_enqueue_style( 'style', get_template_directory_uri() . '/assets/css/style.css', array('reset'), '1.0', 'screen, projection' );
    wp_enqueue_style( 'style-tv', get_template_directory_uri() . '/assets/css-custom/style-tv.css', array('reset'), '1.0', 'screen, projection' );
    wp_enqueue_style( 'style-ticker', get_template_directory_uri() . '/assets/css-custom/ticker.css', array('reset'), '1.0', 'screen, projection' );
    
  } else{
    $theme_version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'twentytwenty-style', get_stylesheet_uri(), array(), $theme_version );
    wp_style_add_data( 'twentytwenty-style', 'rtl', 'replace' );
  }
       

}
add_action( 'wp_enqueue_scripts', 'setup' ); 

function limitText( $txt, $sze ) {
        $txt = strip_tags($txt);
             if ( strlen( $txt ) > 0 ) {
                  if ( strlen( $txt ) > intval($sze) ){
                    $txt = substr( $txt, 0, $sze );
                    $lastSpace = strrpos( $txt, " " );
                    if ( $lastSpace !== false )
                        $txt = substr( $txt, 0, $lastSpace );
                  }
             }
        if ( strlen( $txt ) > $sze-10) {
            $txt .= "...";
        }
    return $txt;
}


/**
 * Load a part into a template while supplying data.
 *
 * @param string $slug The slug name for the generic template.
 * @param array $params An associated array of data that will be extracted into the templates scope
 * @param bool $output Whether to output component or return as string.
 * @return string
 */
function get_part($slug, array $params = array(), $output = true) {
    if(!$output) ob_start();
    if (!$template_file = locate_template("{$slug}.php", false, false)) {
      trigger_error(sprintf(__('Error locating %s for inclusion', 'flp'), $slug), E_USER_ERROR);
    }
    extract($params, EXTR_SKIP);
    require($template_file);
    if(!$output) return ob_get_clean();
}


/*
*
* Gets the effect class from the options in the ACF - Image rollover effects
*
*
**/
function getEffectClass($effect){

  $effectClass = '';
    
    switch($effect){

      case 'Image scale in':
          $effectClass = 'img_sc_in';
      break;

      case 'White border fade in':
          $effectClass = 'bdr_fade_in';
      break;
    }

    echo $effectClass;

}

define('WP_EXPORTER_ADMIN_BAR', true);

/*
*
* Add support for Gutenberg styles
*
*
**/

 add_theme_support( 'wp-block-styles' );

 /* AUTOMATIC UPDATES FOR THE CORE AND PLUGINS */
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'auto_update_theme', '__return_true' );