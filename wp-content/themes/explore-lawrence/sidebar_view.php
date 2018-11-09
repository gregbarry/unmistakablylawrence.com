<?php

 /* Template Name: Two Column */
 // DO NOT PLACE ANYTHING BEFORE THIS!!!!
add_action('wp_enqueue_scripts', 'queue_full_add');
function queue_full_add() {
  // .less file
  wp_enqueue_style( 'less-two-column', get_stylesheet_directory_uri() . '/views-styles/two-column.less' );
}
if ( get_field( 'use_right_sidebar' ) == 'yes'){
  add_filter( 'the_content', 'sidebar_view_func' );
}
function sidebar_view_func($content) {
  the_title();
  $output .= '<div class="right-side">';
  $output .= get_field('right_sidebar_content');
  $output .= '</div>';
  $output .= '<div class="left-side">';
  $output .= $content;
  $output .= '</div>';
  return output;
}
genesis();