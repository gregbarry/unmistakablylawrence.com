<?php
/* Template Name: Weather */
// DO NOT PLACE ANYTHING BEFORE THIS!!!!
// queue - js + css
add_action('wp_enqueue_scripts', 'queue_weather_scripts');
function queue_weather_scripts() {
  // .less file
  wp_enqueue_style( 'weather-view', get_stylesheet_directory_uri() . '/views-styles/weather-view.less' );
}

add_filter( 'the_content', 'weather_view_func' ); // Weather View

function weather_view_func($content) {
  $current_conditions_title = '';
  $currentconditions= '';
  $forcast_title= '';
  $forcast= '';
  $currentconditions = get_field( 'current_conditions_shortcode' );
  if(!$currentconditions){
    $currentconditions = '';
  }
  $current_conditions_title = get_field( 'current_conditions_title' );
  if(!$current_conditions_title){
    $current_conditions_title = '';
  }
  $forcast_title = get_field( 'forcast_title' );
  if(!$forcast_title){
    $forcast_title = '';
  }
  $forcast = get_field( 'forcast_shortcode' );
  if(!$forcast){
    $forcast = '';
  }
  $output = '';
  $output .= '<div class="weather-current">';
  $output .= '<div class="w-c-header"><h2>';
  $output .= $current_conditions_title;
  $output .= '</h2></div>';
  $output .= '<div class="w-c-content">';
  $output .= $currentconditions;
  $output .= '</div></div>';
  $output .= '<div class="weather-ten-day">';
  $output .= '<div class="w-t-d-header"><h2>';
  $output .= $forcast_title;
  $output .= '</h2></div>';
  $output .= '<div class="w-t-d-content">';
  $output .= $forcast;
  $output .= '</div></div>';
  $output .= '<div class="weather-content-area">';
  $output .= $content;
  $output .= '</div>';
  return $output;
}


// LEAVE THIS AT THE END
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' ); // Genesis Force Full Width Page
genesis();

