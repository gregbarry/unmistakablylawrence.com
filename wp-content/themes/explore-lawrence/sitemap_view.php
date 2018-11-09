<?php
/* Template Name: Sitemap */
// DO NOT PLACE ANYTHING BEFORE THIS!!!!
remove_action( 'genesis_after_header', 'featured_title');
add_action( 'genesis_before_content', 'regular_title');
add_action('wp_enqueue_scripts', 'queue_sitemap_add');

function queue_sitemap_add() {
  wp_enqueue_style( 'sitemap-view', get_stylesheet_directory_uri() . '/views-styles/sitemap-view.less' );
  wp_enqueue_script( 'sitemapjs', get_stylesheet_directory_uri(). '/lib/js/sitemap.js', array('jquery'), null, true );
}

genesis();