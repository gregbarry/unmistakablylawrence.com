<?php

include_once('/home/unmistakable/public_html/credentials/simpleview.php');

/* Contents
Theme Stuff
Misc - Useful
Mobile Menu
Header
  Add Search To The Nav
Body
  Shortcodes
Footer
Blog
Custom Posts
Custom Widgets
Custom Functions
jQuery
ACF Filters
*/

// err'stuff
add_action ('doing_it_wrong_run', 'deprecated_argument_run', 10, 3);
function deprecated_argument_run ($function, $message, $version) {
    error_log ('Deprecated Argument Detected');
    $trace = debug_backtrace ();
    foreach ($trace as $frame) {
        //error_log (var_export ($frame, true));
    }
}

add_action( 'get_header', 'remove_primary_sidebar_single_pages' );

function remove_primary_sidebar_single_pages() {
  if ( is_singular('page') && !is_page_template( 'page_blog.php' ) ) {
    remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
    remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );
  }
}

/* Theme Stuff
-------------------------------------------------------------------------------------------------------------- */
// start the engine
require_once( get_template_directory() . '/lib/init.php' );

// child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Genesis Sample Theme' );
define( 'CHILD_THEME_URL', 'https://www.studiopress.com/' );

// add viewport meta tag for mobile browsers
add_action( 'genesis_meta', 'sample_viewport_meta_tag' );
function sample_viewport_meta_tag() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';
}

// add HTML5 and schema support
add_theme_support( 'html5' );

// disable gene SEO
remove_action( 'admin_menu', 'genesis_add_inpost_seo_box' );
remove_theme_support( 'genesis-seo-settings-menu' );

// disable XML-RPC for security
/* add_filter( ‘xmlrpc_methods’, function( $methods ) {
	unset( $methods['pingback.ping'] );
	return $methods;
}); */

// Add Support for woocommerce
add_theme_support( 'genesis-connect-woocommerce' );

/* Misc - Useful
-------------------------------------------------------------------------------------------------------------- */
// ajax cron job

add_action('admin_bar_menu', 'add_toolbar_items', 100);
function add_toolbar_items($admin_bar) {
  $admin_bar->add_menu( array(
    'id'    => 'run-cron',
    'title' => 'Run Cron',
    'href'  => 'http://unmistakablylawrence.com/simpleview/cron.php',
    'meta'  => array(
      'title' => __('Run Cron'),
      'target' => '_blank',
      ),
    ));
}

// queue - css for login page
add_action( 'login_enqueue_scripts', 'login_custom_styles', 1 );
function login_custom_styles() {
  wp_enqueue_style( 'login-style', get_stylesheet_directory_uri() . '/login-style.css' );
}

// queue - js + css
add_action('wp_enqueue_scripts', 'queue_header');
function queue_header() {

  // font awesome - https://fortawesome.github.io/Font-Awesome/icons/ or https://fortawesome.github.io/Font-Awesome/cheatsheet/
  wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css', null, '4.3.0' );


  if (is_page('events-calendar')) {
    wp_enqueue_script( 'event-colorbox', get_stylesheet_directory_uri() . '/lib/js/eventbox.js', array('jquery'), null, true);
  }
  // lessify
  wp_enqueue_style( 'less-style', get_stylesheet_directory_uri() . '/style.less' );
  // wp_enqueue_style( 'flex-style', get_stylesheet_directory_uri() . '/views-styles/flex.less' );

  wp_enqueue_script('gcse-init', get_stylesheet_directory_uri() . '/lib/js/gcse-init.js');
  wp_enqueue_script('tripadvisor', get_stylesheet_directory_uri() . '/lib/js/tripadvisor.js', array('jquery'));
  wp_enqueue_script('weatherbutton', get_stylesheet_directory_uri() . '/lib/js/weatherbutton.js', array('jquery'));
  wp_enqueue_script('stickykit', get_stylesheet_directory_uri() . '/lib/js/jquery.sticky-kit.min.js', array('jquery'));

  // jQuery Mobile
  // wp_enqueue_script('jqm_js', 'https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js', array('jquery'), '1.4.5');
  // wp_register_style('jqm_css', 'https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css', '', '1.4.5');
  // wp_enqueue_style('jqm_css', 'https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css', '', '1.4.5');

  wp_enqueue_script('sidr', get_stylesheet_directory_uri() .'/lib/sidr/jquery.sidr.min.js', array ('jquery') , null, true);
  wp_enqueue_script('sidr-func', get_stylesheet_directory_uri() .'/lib/js/sidrfunctions.js', array ('jquery', 'sidr') , null, true);
  wp_enqueue_style( 'mobile-menu-style', get_stylesheet_directory_uri() . '/lib/sidr/stylesheets/jquery.sidr.dark.css');

  // Enqueue dashicons
  // wp_enqueue_style( 'dashicons' );
}


// breadcrumbs
// add_action('genesis_after_content_sidebar_wrap', 'breadcrumb_nav');
function breadcrumb_nav() {
  if( is_page() && !is_front_page() ) {
    if ( function_exists( 'breadcrumb_trail' ) ) breadcrumb_trail();
  }
}

// shortcode in widgets
add_filter('widget_text', 'do_shortcode');

// page excerpts
add_action('init', 'page_excerpt');
function page_excerpt() { add_post_type_support( 'page', 'excerpt' ); }

/* Mobile Menu
-------------------------------------------------------------------------------------------------------------- */

remove_action( 'genesis_after_header', 'genesis_do_subnav' );


// custom mobile menu
function register_mobile_menu() {
  register_nav_menu('mobile-menu',__( 'Mobile Menu' ));
}
//add_action( 'init', 'register_mobile_menu' );
//add_action( 'genesis_before_header', 'mobile_header_menu' );
function mobile_header_menu() {
  $moptions = array(
    'theme_location'  => 'mobile-menu',
    'menu'            => 'nav-mobile, mobile-menu, Mobile Menu',
    'container'       => 'nav',
    'container_class' => 'nav-mobile',
    'container_id'    => '',
    'menu_class'      => 'mobile',
    'menu_id'         => '',
    'echo'            => true,
    'fallback_cb'     => 'wp_page_menu',
    'before'          => '',
    'after'           => '',
    'link_before'     => '',
    'link_after'      => '',
    'items_wrap'      => '<div id="%1$s" class="su-spoiler su-spoiler-style-default su-spoiler-icon-plus su-spoiler-closed %2$s ">%3$s</div>',
    'depth'           => 0,
    'walker'          => ''
  );
  wp_nav_menu( $moptions );
}

/* Header
-------------------------------------------------------------------------------------------------------------- */
// reconstruct header
remove_action( 'genesis_after_header', 'genesis_do_nav' );
remove_action( 'genesis_header', 'genesis_do_header' );
// remove titles (H1)
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
//* Remove the header right widget area
unregister_sidebar( 'header-right' );

add_action( 'genesis_header', 'genesis_do_logo_header' );
add_action( 'genesis_header', 'genesis_do_header_left' );
add_action( 'genesis_header', 'genesis_do_new_header_right' );
add_action( 'genesis_header', 'genesis_do_nav' );
add_action( 'genesis_after_header', 'featured_title');
// add_action( 'genesis_after_header', 'dynamic_search_header');
add_action( 'genesis_before_header', 'genesis_do_mobile_app_cta' );

function genesis_do_mobile_app_cta() {
  if ( is_active_sidebar( 'mobile-cta-above-header' ) ) {
    echo '<div class="mobile-cta-wrap">';
      if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('mobile-cta-above-header') );
    echo '</div>';
  }
}
function genesis_do_logo_header() {
  if ( is_active_sidebar( 'header-logo' ) ) {
    echo '<div class="widget-area header-logo">';
      if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('header-logo') );
    echo '</div>';
  }
}
function genesis_do_header_left() {
  if ( is_active_sidebar( 'header-left' ) ) {
    echo '<div class="widget-area header-left">';
      if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('header-left') );
    echo '</div>';
  }
}
function genesis_do_new_header_right() {
  if ( is_active_sidebar( 'new-header-right' ) ) {
    echo '<div class="widget-area header-right">';
      if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('new-header-right') );
    echo '</div>';
    echo '<a href="#sidr" class="mobile-menu-button button">MENU <i class="fa fa-bars"></i></a>';
  }
}
function featured_title() {
  if(get_field('show_headline') == "no") {
    return;
  }
  if( !is_front_page() ){
    echo '<div class="featured-area">';
          // featured image
          if ( has_post_thumbnail() && is_page() ) {
            global $post;
            $full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
              echo '<style type="text/css">';
                echo '.featured-area{ background-image: url(' . $full_image_url[0] . ');}';
              echo '</style>';
          }
          else {
            if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('default-featured-image') );
          }
      echo '<div class="the-post-title"><div class="the-post-title-wrap"><h1 class="post-title">';
      echo get_the_title();
      if (is_404()){
        echo '<h1 class="post-title">Page not found</h1>';
      }
      echo '</h1></div></div>';
    echo '</div>';
  }
}

function regular_title() {
    echo '<div class="featured-area">';
          // featured image
      echo '<div class="reg-post-title"><div class="regular-post-title-wrap"><h1 class="reg-post-title">';
      echo get_the_title();
      echo '</h1></div></div>';
    echo '</div>';
}

/* function dynamic_search_header() {
  echo '<div class="header-search-area"><div class="header-search-area-wrap">';
    echo 'Search ________ <br />Form.....';
  echo '</div></div>';
} */



/* Add Search To The Nav
-------------------------------------------------------------------------------------------------------------- */
add_filter( 'wp_nav_menu_items', 'theme_menu_extras', 10, 2 );
/**
 * Filter menu items, appending either a search form or today's date.
 *
 * @param string   $menu HTML string of list items.
 * @param stdClass $args Menu arguments.
 *
 * @return string Amended HTML string of list items.
 */
function theme_menu_extras( $menu, $args ) {

	//* Change 'primary' to 'secondary' to add extras to the secondary navigation menu
	if ( 'primary' !== $args->theme_location )
		return $menu;

	ob_start();
	// get_search_form();
  ?>
  <div style="float:right; width: 325px;">
    <gcse:search></gcse:search>
  </div>
  <?php

	$search = ob_get_clean();
	$menu  .= '<li class="search">' . $search . '</li>';


	//* Uncomment this block to add the date to the navigation menu
	/*
	$menu .= '<li class="right date">' . date_i18n( get_option( 'date_format' ) ) . '</li>';
	*/

	return $menu;

}

//* Customize search form input button text
add_filter( 'genesis_search_button_text', 'sp_search_button_text' );
function sp_search_button_text( $text ) {
	return esc_attr( '&#xf002;' );
}
//* Customize search form input box text
add_filter( 'genesis_search_text', 'sp_search_text' );
function sp_search_text( $text ) {
	return esc_attr( 'Search' );
}


/* Body
-------------------------------------------------------------------------------------------------------------- */
//* Unregister sidebar/content layout setting
genesis_unregister_layout( 'sidebar-content' );

//* Unregister content/sidebar/sidebar layout setting
genesis_unregister_layout( 'content-sidebar-sidebar' );

//* Unregister sidebar/sidebar/content layout setting
genesis_unregister_layout( 'sidebar-sidebar-content' );

//* Unregister sidebar/content/sidebar layout setting
genesis_unregister_layout( 'sidebar-content-sidebar' );

//* Unregister secondary sidebar
unregister_sidebar( 'sidebar-alt' );

// Land Var for Pagination #
add_action('genesis_before_content', 'pagecounter');
function pagecounter() {
 $perPage = get_option('biz_per_page');
 echo '<div class="page-var" data-per-page="'.$perPage.'"></div>';
}

function listing_item_loop($items) {
	foreach($items as $listing){
		$company    = $listing['sortcompany'];
		$url        = $listing['weburl'];
		$offertitle = $listing['offertitle'];
		$offertext  = $listing['offertext'];
		$couponcat  = $listing['couponcatname'];
		$mediafile  = $listing['mediafile'];
		$imageurl   = str_replace("http://", "https://", $listing['imgpath']);
		$mediafile  = $listing['thumbfile'];
		$startdate  = $listing['redeemstart'];
		$enddate    = $listing['redeemend'];

		if ($mediafile) {
			$image = '<img src="'.$imageurl . $mediafile.'"/>';
		} else {
		    $imagePath = 'https://unmistakablylawrence.com/wp-content/themes/explore-lawrence/images/placeholders/See.jpg';
		    $image     = '<img src="'.$imagePath.'"/>';
		}

		$output .= "<div class='event-listing-item'><div class='listing-item-wrap'>";
		$output .= "<div class='listing-image'>".$image."</div>";
		$output .= "<div class='listing-content-area'>";
		$output .= "<div class='listing-title'><a target='_blank' href='".$url."'><h3>".$offertitle."</h3></a></div>";
    $output .= "<p><b>Venue: </b>".$company . "<br>";
    if (!empty($startdate) && !empty($enddate)) {
      $output .= "<b>Valid: </b>".$startdate . " - " . $enddate ."<br>";
    }
		$output .= $couponcat . "</p>";
		$output .= "<p>".$offertext."</p>";
		$output .= "</div></div></div>";
  }

  return $output;
}

/* Shortcodes
--------------------------------------------*/
add_shortcode( 'sv-coupon', 'sv_coupon_func');
function sv_coupon_func($atts) {
    $atts = shortcode_atts( array(
        'id'    => ''
    ), $atts);

    $id = $atts['id'];
    $html = '';

    global $wpdb;

    $sql = "SELECT * FROM sv_coupon_categories scc, sv_coupons sc
            WHERE scc.couponid = sc.couponid
            AND couponcatid = ".$id;
    $coupons = $wpdb->get_results($sql,ARRAY_A);

    $html = listing_item_loop($coupons);
    return '<div class="generic-wrap">'.$html.'</div>';
}

add_shortcode( 'sv-form', 'sv_form_func');
function sv_form_func($atts) {
  $atts = shortcode_atts( array(
    'id'    => '',
    'class'   => '',
    'name'   => '',
  ), $atts);
  $id = $atts['id'];
  $name = trim($atts['name']);
  $html = '';


  global $wpdb;
  if (($name != '') && ($id == '')){
    $forms = $wpdb->get_results('SELECT *
                    FROM `sv_forms`
                    WHERE is_deleted = 0', OBJECT);

    $html = '';
    foreach($forms as $string){
      $haystack = $string->html;
      preg_match_all('/<td class=\"sv\_api\_section\_head\">(.*?)<\/td>/s',$haystack,$out);
      $value = $out[0][0];
      $value = trim($value);
        if(strlen(strstr($value,$name))>0){
          $html .= $haystack;
          $id = $string->form_id;
        }
    }
  } else{
    $html = $wpdb->get_results("SELECT html
                    FROM `sv_forms`
                    WHERE form_id = '".$id."'
                    AND is_deleted = 0");

    $html = $html[0];
    $html = $html->html;
  }
  $html = str_replace('http://lawrence.simpleviewcrm.com', 'https://lawrence.simpleviewcrm.com', $html);
  $form = "<div class='sv-form {$atts['class']}' id='sv-form-id-{$id}'>";
  $form .= $html;
  $form .= "</div>";
  return $form;
}

/* Footer
-------------------------------------------------------------------------------------------------------------- */
//* Add support for 4-column footer widgets

add_action('get_header', 'show_instagram');

remove_action( 'genesis_footer', 'genesis_do_footer' );
// do instagram hooked on genesis_before_footer, 1 //do not override
add_action( 'genesis_before_footer', 'footer_booking', 2 );
add_action( 'genesis_before_footer', 'footer_menu', 3 );
//add_theme_support( 'genesis-footer-widgets', 4 );
add_action( 'genesis_before_footer', 'footer_logos' );
add_action( 'genesis_footer', 'ttp_footer' );
function footer_booking() {
  echo '<div class="footer-booking"><div class="footer-booking-wrap">';
    if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('foot-booking') );
  echo '</div></div>';
}
function footer_menu() {
  echo '<div class="footer-menu"><div class="footer-menu-wrap">';
    if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('foot-menu') );
  echo '</div></div>';
}
function footer_logos() {
  echo '<div class="footer-logos"><div class="footer-logos-wrap">';
    if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('foot-logos') );
  echo '</div></div>';
}
function ttp_footer() {
  echo '<div class="footer-right">';
    if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('foot-right') );
  echo '</div>';
  echo '<div class="footer-left"><div id="schema-area">';
    if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('foot-info') );
  echo '</div></div>';
  echo '<div class="footer-bottom-border"></div>';
}

function show_instagram() {
  if (get_field('show_instagram')) {
    add_action('genesis_before_footer', 'cvb_do_instagram', 1);
  }
}

function cvb_do_instagram() {
  //$instagram = get_field('instagram_shortcode');
  $instagram = '[fts_instagram instagram_id=1402241842 type=user]';
  echo '<div class="home-bottom"><div class="instagram-feed-area"><div class="instagram-icon"><i class="fa fa-instagram"></i></div><div class="instagram-feed">';
  echo do_shortcode('[fts_instagram instagram_id=1402241842 type=user]');
  echo '</div></div></div>';
}

/* Blog
-------------------------------------------------------------------------------------------------------------- */

// read more link
add_filter( 'excerpt_more', 'custom_read_more_link');
add_filter('get_the_content_more_link', 'custom_read_more_link');
add_filter('the_content_more_link', 'custom_read_more_link');
function custom_read_more_link() {
  return '&nbsp;<a class="more-link readMore" href="' . get_permalink() . '" rel="nofollow">Read More &hellip;</a>';
}


// blog post information
add_filter( 'genesis_post_info', 'custom_post_info_filter' );
function custom_post_info_filter( $post_info ) {
  // default: 'By [post_author_link] on [post_date] [post_comments] [post_edit]'
  $post_info = '[post_date] [post_comments] [post_edit]';
  return $post_info;
}


// custom blog loop - replaces the_content with the_excerpt
add_action( 'get_header', 'blog_detect');

function blog_detect() {
if( is_home() ) {
  remove_action( 'genesis_loop', 'genesis_do_loop' );
  add_action( 'genesis_loop', 'my_custom_loop' );
  } elseif (is_single() ) {
  remove_action( 'genesis_after_header', 'featured_title');
  add_action( 'genesis_before_content', 'regular_title');
  }
}

function my_custom_loop() {

  if( !genesis_html5() ) {
    genesis_legacy_loop();
    return;
	}

	if( have_posts() ) : while ( have_posts() ) : the_post();
    do_action( 'genesis_before_entry' );
    printf( '<article %s>', genesis_attr( 'entry' ) );
      do_action( 'genesis_entry_header' );
		  do_action( 'genesis_before_entry_content' );
			printf( '<div %s>', genesis_attr( 'entry-content' ) );
			  echo the_post_thumbnail();
        the_excerpt();
        //do_action( 'genesis_entry_content' );
				do_action( 'genesis_after_entry_content' );
				do_action( 'genesis_entry_footer' );
      echo '</div>'; //* this goes above "do_action( 'genesis_after_entry_content' );" normally
	  echo '</article>';
	  do_action( 'genesis_after_entry' );
		endwhile; //* end of one post
		do_action( 'genesis_after_endwhile' );
	else : //* if no posts exist
		do_action( 'genesis_loop_else' );
	endif; //* end loop
}


/* Custom Posts
-------------------------------------------------------------------------------------------------------------- */

add_action( 'init', 'create_post_type' );
function create_post_type() {
  // custom post
  /*register_post_type( 'custom_post',
    array(
      'labels' => array(
      'name' => __( 'Custom Post' ),
      'singular_name' => __( 'Custom Post' )
    ),
    'taxonomies' => array('category'),
		'public' => true,
		'has_archive' => true,
    'rewrite' => array('slug' => 'custom-post'),
    'supports' => array('title','editor','thumbnail','page-attributes'),
    'menu_icon' => get_stylesheet_directory_uri() . '/icon-small.png',
		)
  );*/
}
if ( ! function_exists('custom_tweet') ) {

// Register Custom Post Type
function custom_tweet_func() {

	$labels = array(
		'name'                => 'Tweets',
		'singular_name'       => 'Tweet',
		'menu_name'           => 'Twitter',
		'parent_item_colon'   => 'Parent Item:',
		'all_items'           => 'All Tweets',
		'view_item'           => 'View Tweet',
		'add_new_item'        => 'Add New Tweet',
		'add_new'             => 'Add New',
		'edit_item'           => 'Edit Tweet',
		'update_item'         => 'Update Tweet',
		'search_items'        => 'Search Tweet',
		'not_found'           => 'Not found',
		'not_found_in_trash'  => 'Not found in Trash',
	);
	$args = array(
		'label'               => 'custom_tweet',
		'description'         => 'Handpicked Tweets',
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes', ),
		'taxonomies'          => array( 'twitter_sources' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-twitter',
		'can_export'          => false,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'custom_tweet', $args );

}

// Hook into the 'init' action
add_action( 'init', 'custom_tweet_func', 0 );

}

if ( ! function_exists( 'tweet_source_func' ) ) {

// Register Custom Taxonomy
function tweet_source_func() {

	$labels = array(
		'name'                       => 'Sources',
		'singular_name'              => 'Tweet Source',
		'menu_name'                  => 'Sources',
		'all_items'                  => 'All Sources',
		'parent_item'                => 'Parent Source',
		'parent_item_colon'          => 'Parent Source:',
		'new_item_name'              => 'New Tweet Source',
		'add_new_item'               => 'Add New Source',
		'edit_item'                  => 'Edit Source',
		'update_item'                => 'Update Source',
		'separate_items_with_commas' => 'Separate items with commas',
		'search_items'               => 'Search',
		'add_or_remove_items'        => 'Add or remove Sources',
		'choose_from_most_used'      => 'Choose from the most used items',
		'not_found'                  => 'Not Found',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'twitter_sources', array( 'custom_tweet' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'tweet_source_func', 0 );

}

if ( ! function_exists('news_story_func') ) {

// Register Custom Post Type
function news_story_func() {

	$labels = array(
		'name'                => 'News Stories',
		'singular_name'       => 'News Story',
		'menu_name'           => 'News Stories',
		'parent_item_colon'   => 'Parent Item',
		'all_items'           => 'All Stories',
		'view_item'           => 'View Story',
		'add_new_item'        => 'Add New Story',
		'add_new'             => 'Add New',
		'edit_item'           => 'Edit Story',
		'update_item'         => 'Update Story',
		'search_items'        => 'Search Stories',
		'not_found'           => 'Not Found',
		'not_found_in_trash'  => 'Not found in Trash',
	);
	$args = array(
		'label'               => 'news_story',
		'description'         => 'News stories from Explore Lawrence',
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-megaphone',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'news_story', $args );

}

// Hook into the 'init' action
add_action( 'init', 'news_story_func', 0 );

}

// custom post columns
add_filter('manage_posts_columns', 'order_column_head');
add_action('manage_posts_custom_column', 'order_column_content', 10, 2);
function order_column_head($defaults) {
  $defaults['post_order'] = 'Post Order';
  return $defaults;
}

// custom post menu order
function order_column_content($column_name, $post) {
	if( $column_name == 'post_order' ){
    echo '<span class="menuOrder">'. get_post_field('menu_order', $post->ID) . '</span>';
	}
}

// adds a custom post type to the query to display on a categories page but not the blog
//add_action( 'pre_get_posts', 'add_my_post_types_to_query' );
function add_my_post_types_to_query( $query ) {
	if ( !is_home() && $query->is_main_query() )
		$query->set( 'post_type', array( 'post', 'page', 'movie', 'custom_post' ) );
	return $query;
}


/* Custom Widgets
-------------------------------------------------------------------------------------------------------------- */

// usage: if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('custom-widget') );
if ( function_exists('register_sidebar') ) {
  // custom widget
  /*register_sidebar(array(
    'id' => 'custom-widget',
		'description' => __( 'Custom Widget description.'),
		'name' => 'Custom Widget',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));*/
  // Above Header
  register_sidebar(array(
    'id' => 'mobile-cta-above-header',
		'description' => __( ''),
		'name' => 'Mobile App Link',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
  ));
  // Header Logo
  register_sidebar(array(
    'id' => 'header-logo',
		'description' => __( 'Logo at the top in the header. Small screen sizes.'),
		'name' => 'Header Logo',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));
  // Header Left
  register_sidebar(array(
    'id' => 'header-left',
		'description' => __( 'Area top left in the header.'),
		'name' => 'Header Left',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));
  // Header Middle
  /* register_sidebar(array(
    'id' => 'header-middle',
		'description' => __( 'Area top middle in the header.'),
		'name' => 'Header Middle',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  )); */
  // Header Right
  register_sidebar(array(
    'id' => 'new-header-right',
		'description' => __( 'Area top right in the header.'),
		'name' => 'Header Right',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));
  register_sidebar(array(
    'id' => 'search-sidebar',
		'description' => __( 'Search Sidebar'),
		'name' => 'Appears on search results page',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '',
		'after_title' => '',
  ));
  // Default Featured Image
  register_sidebar(array(
    'id' => 'default-featured-image',
		'description' => __( 'Default Featured Image On Secondary Pages.'),
		'name' => 'Default Featured Image',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '',
		'after_title' => '',
  ));
  // footer booking
  register_sidebar(array(
    'id' => 'foot-booking',
		'description' => __( 'Online Booking in the footer.'),
		'name' => 'Footer Booking',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));
  // footer menu
  register_sidebar(array(
    'id' => 'foot-menu',
		'description' => __( 'Menu in the footer.'),
		'name' => 'Footer Menu',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));
  // footer logos
  register_sidebar(array(
    'id' => 'foot-logos',
		'description' => __( 'Logos in the footer.'),
		'name' => 'Footer Logos',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));
  // footer into
  register_sidebar(array(
    'id' => 'foot-info',
		'description' => __( 'Info For The Footer.'),
		'name' => 'Footer Info',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));
  // footer right
  register_sidebar(array(
    'id' => 'foot-right',
		'description' => __( 'Right Of Info The Footer.'),
		'name' => 'Footer Right',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));
  // footer right
  register_sidebar(array(
    'id' => 'board',
		'description' => __( 'Board pages.'),
		'name' => 'Board Pages',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
  ));
}


/* Custom Functions
--------------------------------------------------------------------------------------------------------------

  is_child()
  is_blog()
  get_upcoming_events()
-------------------------------------------------------------------------------------------------------------- */

add_action( 'genesis_before', 'add_pixel' );

function add_pixel() {
  if ( current_filter() == 'genesis_before' ) {
    ?>

<script data-obct type="text/javascript">
/** DO NOT MODIFY THIS CODE**/
!function(_window, _document) {
  var OB_ADV_ID='009cdb348b6742f12b1022fe2dc9b17f41';
  if (_window.obApi) {var toArray = function(object) {return Object.prototype.toString.call(object) === '[object Array]' ? object : [object];};_window.obApi.marketerId = toArray(_window.obApi.marketerId).concat(toArray(OB_ADV_ID));return;}
  var api = _window.obApi = function() {api.dispatch ? api.dispatch.apply(api, arguments) : api.queue.push(arguments);};api.version = '1.1';api.loaded = true;api.marketerId = OB_ADV_ID;api.queue = [];var tag = _document.createElement('script');tag.async = true;tag.src = '//amplify.outbrain.com/cp/obtp.js';tag.type = 'text/javascript';var script = _document.getElementsByTagName('script')[0];script.parentNode.insertBefore(tag, script);}(window, document);

obApi('track', 'PAGE_VIEW');
</script>

<!-- Activity name for this tag: TA_Lawrence_Kansas_Daily Counter_12.01.16 -->
<script type='text/javascript'>
var axel = Math.random()+"";
var a = axel * 10000000000000;
document.write('<img src="https://pubads.g.doubleclick.net/activity;xsp=635411;ord=1;num='+ a +'?" width=1 height=1 border=0/>');
</script>
<noscript>
<img src="https://pubads.g.doubleclick.net/activity;xsp=635411;ord=1;num=1?" width=1 height=1 border=0/>
</noscript>

<img style="position:absolute;" src="https://dc.arrivalist.com/px/?pixel_id=1090&a_source=Explore_Lawrence&a_medium=Site_Visit&a_campaign=2016_Run_of_Site" height="1" width="1">
    <?
  }
}

function composeAmenitySection($list, $title) {
  $amenitySection = '';
  if ($list != '') {
    $amenitySection = '
      <div class="amenity-sec">
        <div class="amenity-sec-title">'.$title.' Amenities</div>
        <ul>' . $list . '</ul>
      </div>';
  }
  return $amenitySection;
}

// usage: if( is_page('about-us') || is_child('about-us') )
function is_child( $parent = '' ) {
  global $post;

  $parent_obj = get_page( $post->post_parent, ARRAY_A );
  $parent = (string) $parent;
  $parent_array = (array) $parent;

  if ( in_array( (string) $parent_obj['ID'], $parent_array ) ) {
    return true;
  } elseif ( in_array( (string) $parent_obj['post_title'], $parent_array ) ) {
    return true;
  } elseif ( in_array( (string) $parent_obj['post_name'], $parent_array ) ) {
    return true;
  } else {
    return false;
  }
}

// usage: if( is_blog() )
function is_blog() {
	global $post;
	$posttype = get_post_type($post );
	return ( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post')  ) ? true : false ;
}
// returns html for displaying the upcoming events (used in the details view)
/* jQuery
-------------------------------------------------------------------------------------------------------------- */

function get_upcoming_events(){
    $events_title = get_field( 'event_title_text', 23 );
    $events_view_link_text = get_field( 'event_link_text', 23 );
    $events_view_link = get_field( 'event_link', 23 );
    $event_listing = '<div class="event-list-wrap">';
    global $wpdb;
    $listings = $wpdb->get_results("SELECT e.title, e.location, d.eventdate, e.eventid FROM sv_events e, sv_event_dates d
                                    WHERE d.eventid = e.eventid
                                    AND d.is_deleted = '0' AND e.is_deleted = '0'
                                    AND e.featured = 'yes'
                                    AND eventdate != '0000-00-00'
                                    AND eventdate > NOW() - INTERVAL 1 DAY
                                    GROUP BY e.eventid
                                    ORDER BY d.eventdate ASC LIMIT 5");

    foreach($listings as $listing){
        $date1 = DateTime::createFromFormat('Y \- m \- d', $listing->eventdate);
          $eventdate = $date1->format('M d');
        $eventtitle = ($listing->title != NULL) ? $listing->title : 'N/A';
        $eventlocation = ( $listing->location != NULL) ? $listing->location : '';

        $event_listing .= '<div class="event-item">
          <div class="event-date">'.$eventdate.'</div>
          <div class="event-title">'.$eventtitle.'</div>
          <div class="event-location">'.$eventlocation.'</div>
        </div>';
      }
    $event_listing .= '</div>';

    return'
  <div class="upcoming-events-area height2">
    <div class="upcoming-events-title">
      <h2>'.$events_title.'</h2>
    </div>
    <div class="upcoming-events-link">
      <a href="'.$events_view_link.'" target="_blank">'.$events_view_link_text.' <i class="fa fa-calendar-o"></i></a>
    </div>
    <div class="upcoming-events-list">'.$event_listing.'</div>
  </div>
  ';
  }

add_action('genesis_after', 'jquery_additions');
function jquery_additions() { ?>
  <script>
(function($){

  $(window).load(function(){
    if ($('.nav-primary > .wrap > ul > li').hasClass('current-menu-ancestor')){
      $('.current-menu-ancestor').addClass('active');
    } else if ($('.nav-primary > .wrap > ul > li').hasClass('current-menu-item')){
      $('.current-menu-item').addClass('active');
    }
    if ($(window).width() > 768) { // Stop below this media query
      if ($('.instagram-feed-area').length > 0) {
        var instagramimageheight = $('.instagram-feed-area .instagram-image').outerHeight();
        var instagramiconpadding = instagramimageheight / 2;
        $('.instagram-icon').css('height', instagramimageheight);
        // $('.instagram-icon').css('padding-left', instagramiconpadding - 15);
        $('.instagram-icon').css('padding-top', instagramiconpadding - 22);
      }
    }

  }); //end window load

  $(document).ready( function() {
    $('.nav-primary > .wrap > ul > li > a').mouseover(function(){
      $('.nav-primary > .wrap > ul > li').removeClass('active');
      $(this).parent('li').addClass('active');
    });

    //sticky map: disabled
    if ($('.listing-right .maps').length > 0) {
      var maph = '';
      // $('.listing-right').stick_in_parent({offset_top: 20});

    }
  });


})(jQuery);
  </script>
  <script type="text/javascript">
  var metaslider_2228 = function($) {
    if ($('.flex-container').length > 0) {
    console.log('init');
            $('#slider').flexslider({
                slideshowSpeed:5000,
                animation:"slide",
                controlNav:false,
                directionNav:true,
                pauseOnHover:true,
                direction:"horizontal",
                reverse:false,
                animationSpeed:600,
                prevText:"&lt;",
                nextText:"&gt;",
                easing:"linear",
                slideshow:true
            });
            $('#slider').css('opacity','1');
            }
            };
        var timer_metaslider_2228 = function() {
            var slider = !window.jQuery ? window.setTimeout(timer_metaslider_2228, 100) : !jQuery.isReady ? window.setTimeout(timer_metaslider_2228, 1) : metaslider_2228(window.jQuery);
        };
        timer_metaslider_2228();
  </script>
<?php }

// add_action('genesis_after', 'fixed_header_biz');
function fixed_header_biz() { ?>
  <script>
(function($){
  function onLoadonResize(){
    if ($(window).width() > 640) { // Stop below this media query
      var fromtop = 40;  // distance from top of page
      var itemwraper = $(".site-header"); // what item you want to change class
      var loadedscrolled = $(window).scrollTop();
      if (loadedscrolled > fromtop){
        $(itemwraper).addClass('fixed');
        var shown = true;
      }else{
        var shown = false;
      }
       $(window).scroll(function() {
       var height = $(window).scrollTop();
       if (!shown) {
         if (height  > fromtop ) {
            $(itemwraper).addClass('fixed');
             shown = true;
         }}
         if (shown) {
         if (height <= fromtop ) {
            $(itemwraper).removeClass('fixed');
             shown = false;
         }}
      });
    }
  }

  $(window).load(function(){
    onLoadonResize();
  }); //end window load

  $(window).on('resize', function(){
    onLoadonResize();
  }); //end window resize

})(jQuery);

  </script>
<?php }


// mobile menu stuff

    add_shortcode('mobile-menu', 'mob_menu_func');
    function mob_menu_func( $atts ) {
      extract( shortcode_atts( array(
        'menu' => 'main-menu',
        'order' => 'ASC',
        'orderby' => 'menu_order',
      ), $atts), EXTR_SKIP);

      global $post;
      $output = '';
      $menus = array(
        'main-menu',
        'global-header',
        'global-footer'
        );

      $output .= '<div id="sidr" class="mobile-menu-container">';
      $output .= '<div class="mobile-menu-wrap">';

      $output .= "<ul>";
      $output .= "<li><a class='mobile-menu-title'>".get_bloginfo('blog_name')."</a><a href='#' class='close'><i class='fa fa-times'></i></a></li>";
      $output .= "</ul>";

      ob_start();
      if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('mobile-before') );
      $output .= ob_get_contents();
      ob_end_clean();

      foreach ($menus as $menu) {
      $o = '';
      $args = array(
        'order'                  => 'ASC',
        'orderby'                => 'menu_order',
        'post_type'              => 'nav_menu_item',
        'post_status'            => 'publish',
        'output_key'             => 'menu_order',
        'nopaging'               => true,
        'menu_item_parent'       => 0,
        'update_post_term_cache' => false );


      $items = wp_get_nav_menu_items( $menu, $args );







      $o .= '<ul class="'.$menu.'">';
      if ($items) {
          foreach ($items as $item) {
          $current = ($item->url == $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]) ? "current-page" : "";
          $classes = (is_array($item->classes)) ? implode($item->classes) : $item->classes;

          if ($item->menu_item_parent == "0") {
              $subitems = array();
              $usedids = array();
              foreach ($items as $sub) {
                if ($sub->menu_item_parent == $item->ID && ! in_array($sub->ID, $usedids)) {
                  $subitems[] = $sub;
                  $usedids[] = $sub->ID;
                }
              }
            $parent = (count($subitems) > 0) ? "parent-item" : '';
            $o .= "<li class='menu-item {$current} {$classes} {$parent}'><a href='{$item->url}'>{$item->title}</a>";
            if ($parent != '') {
            $o .= "<a href='#' class='arrow'><i class='fa fa-angle-double-right'></i></a>";
            }
              if (count($subitems) > 0) {
                  $o .= "<ul class='sub-menu'>";
                  foreach ($subitems as $subitem) {
                    $o .= "<li class='{$current} {$classes} sub-item'><a href='{$subitem->url}'>{$subitem->title}</a>";

                    // new code for sub sub items
                    $subsubs = array();
                    foreach ($items as $subsub) {
                      if ($subsub->menu_item_parent == $subitem->ID && ! in_array($subsub->ID, $usedids)) {
                      $subsubs[] = $subsub;
                      $usedids[] = $subsub->ID;
                      }
                    }

                    $subparent = ( count($subsubs) > 0 ) ? "sub-parent-item" : '';
                    if ($subparent != '') {
                      $o .= "<a href='#' class='sub-arrow'><i class='fa fa-angle-double-right'></i></a>";
                      $o .= "<ul class='sub-sub-menu'>";
                      foreach ($subsubs as $subsub) {
                        //if ($subsub->menu_item_parent == $subitem->ID && ! in_array($subsub->ID, $usedids)) {
                          $o .= "<li class='menu-item {$current} {$classes} sub-sub-item'><a href='{$subsub->url}'>{$subsub->title}</a>";

                          // new code for sub sub sub items
                          $subsubsubs = array();
                          foreach ($items as $subsubsub) {
                            if ($subsubsub->menu_item_parent == $subsub->ID && ! in_array($subsubsub->ID, $usedids)) {
                            $subsubsubs[] = $subsubsub;
                            $usedids[] = $subsubsub->ID;
                            }
                          }

                          $subsubparent = ( count($subsubsubs) > 0 ) ? "sub-sub-parent-item" : '';
                          if ($subsubparent != '') {
                            $o .= "<a href='#' class='sub-sub-arrow'><i class='fa fa-angle-double-right'></i></a>";
                            $o .= "<ul class='sub-sub-sub-menu'>";
                            foreach ($subsubsubs as $subsubsub) {
                              //if ($subsub->menu_item_parent == $subitem->ID && ! in_array($subsub->ID, $usedids)) {
                                $o .= "<li class='menu-item {$current} {$classes} sub-sub-sub-item'><a href='{$subsubsub->url}'>{$subsubsub->title}</a>";
                                $o .= "</li>";
                             //}
                            }
                            $o .= "</ul>";
                          }


                          $o .= "</li>";
                       //}
                      }
                      $o .= "</ul>";
                    }
                    $o .= "</li>";
                  }
                  $o .= "</ul>";
                }
            $o .= "</li>";
            }
          }
      }
      $o .= '</ul>';

      $output .= $o;
    }

      ob_start();
      if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('mobile-after') );
      $output .= ob_get_contents();
      ob_end_clean();

    $output .= '</div>';
    $output .= '</div>';
    return $output;
  }

add_action('genesis_after', 'mobile_menu_wrap', 100);
function mobile_menu_wrap() {
  echo do_shortcode('[mobile-menu menu="main-menu"]');
}

/* ACF Filters
-------------------------------------------------------------------------------------------------------------- */
// pass list of categories to a select box on the listing template
function get_category_list( $field ) {
	$field['choices'] = array();
  global $wpdb;
	$cats = $wpdb->get_results("SELECT * FROM `sv_categories` WHERE is_deleted = 0");
	foreach($cats as $cat){
		$choices[$cat->cat_id] = $cat->name;
	}
  $choices['map'] = 'Map Page';
	$field['choices'] = $choices;
	return $field;
}

add_filter('acf/load_field/name=select_a_category', 'get_category_list');

/*
  Update SimpleView Tracking
*/
function updateHits($recID) {
  /*
   * HIT TYPE ID
   * 1 - Listings
   * 4 - Listing Website
   * 5 - Coupons
   * 6 - Locations
   * 58 - Added to Itenerary
   * 59 - Map View
   */
	$fields = array(
		'action'=>urlencode("updateHits"),
		'username'=>urlencode($username),
    'password'=>urlencode($password),
    'hittypeid'=>1,
    'recid'=>$recID,
    'hitdate'=>date('m/d/Y')
	);

  $fields_string = '';
  foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
  rtrim($fields_string,'&');
  $url = "http://lawrence.simpleviewcrm.com/webapi/listings/xml/listings.cfm";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_POST, count($fields));
  curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
  $output = curl_exec($ch);
  curl_close($ch);
}
