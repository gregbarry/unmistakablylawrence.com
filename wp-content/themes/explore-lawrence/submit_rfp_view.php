<?php

 /* Template Name: RFP Form View */
 // DO NOT PLACE ANYTHING BEFORE THIS!!!!


// queue - js + css
add_action('wp_enqueue_scripts', 'queue_rfp_view_add');
function queue_rfp_view_add() {
  // .less file
  wp_enqueue_style( 'less-style-rfp-view', get_stylesheet_directory_uri() . '/views-styles/submit-rfp-view.less' );
}

add_filter( 'the_content', 'full_width_view_func' ); // Full Width View

function full_width_view_func($content) {
  $form_script_field = get_field( 'form_script' );
  if ( !empty($form_script_field) ){
    $form_script = $form_script_field;
  } else{
    $form_script = '<script type="text/javascript" src="https://lawrence.simpleviewcrm.com/webapi/widgets/submitevent/submiteventjs.cfm"></script>';
  }
  $output = '';
  $output .= '<div class="rfp-content-area">';
  $output .= $content;
  $output .= '</div>';
  $output .= '<div class="rfp-form-area">';
  $output .= $form_script;
  $output .= '</div>';
  return $output;
}
// LEAVE THIS AT THE END
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' ); // Genesis Force Full Width Page
genesis();
