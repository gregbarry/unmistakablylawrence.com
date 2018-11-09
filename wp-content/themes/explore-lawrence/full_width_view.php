<?php

 /* Template Name: Full Width */
 // DO NOT PLACE ANYTHING BEFORE THIS!!!!


// queue - js + css
add_action('wp_enqueue_scripts', 'queue_full_add');
function queue_full_add() {
  // .less file
  wp_enqueue_style( 'less-style-full', get_stylesheet_directory_uri() . '/views-styles/full-width-view.less' );
}

add_filter( 'the_content', 'full_width_view_func', 20 ); // Full Width View

function full_width_view_func($content) {
  $fullwidthtitle = get_field( 'headline_title' );
  $fullwidthfeaturedcontentimage = get_field('headline_image');
    if( !empty($fullwidthfeaturedcontentimage) ){
      $contentfeaturedimage = '<div class="fw-headline-image" style="background-image: url('.$fullwidthfeaturedcontentimage["url"].')"></div>';
    } else{
      $contentfeaturedimage = '';
    }
  $output = '';
  if ((get_field('show_headline') == "yes") ){
    $output .= '<div class="fw-headline-area">';
    if(get_field('show_headline') == "yes"){
      $output .= '<div class="fw-headline-title"><h2>'.$fullwidthtitle.'</h2></div>';
    }
    $output .= $contentfeaturedimage;
    $output .= '</div>';
  }
  $output .= '<div class="fw-content-area">';
  $output .= $content;
  $output .= '</div>';
  return $output;
}
add_action('genesis_after', 'img_height_biz');
function img_height_biz() { ?>
  <script>
(function($){
  function sectionHeights(){
      var imgwidth = $('.fw-headline-image').innerWidth();
      // alert(sectionheight);
      $('.fw-headline-image').css('min-height', imgwidth * 0.75066667);
  }
  
  $(document).ready(function(){    
    sectionHeights();
  }); //end doc ready

  $(window).load(function(){
    sectionHeights();
  }); //end window load
  
  $(window).on('resize', function(){
    sectionHeights();
  }); //end window resize

})(jQuery);

  </script>
<?php }
// LEAVE THIS AT THE END
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' ); // Genesis Force Full Width Page
genesis();