<?php

 /* Template Name: Featured Event */
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
  
  /*$args = array(
            'category_name' => 'featured-event', 
            'orderby' => 'post_date', 
            'order' => 'DESC', 
            'posts_per_page' => 1,
            'post_status' => 'publish'
          );
  
  $posts_array = get_posts( $args ); 

  $title = $posts_array[0]->post_title;
  $this_content = $posts_array[0]->post_content;*/

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
  $output .= '<h1 style="text-align:center;">'.$title.'</h1>';
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

  /*
  function addCss(url, id) {
      console.log('add CSS!');
      var l    = document.createElement('link'),
          head = document.getElementsByTagName('head')[0];

      l.rel = 'stylesheet';
      l.href = url;
      if (id) {
          l.id = id;
      }

      head.appendChild(l);
  }

  function addCrowdRiff() {
      console.log('Add Crowd Riff');

      var d        = document, w = window,
          target   = jQuery('#crowdRiffContainer'),
          url      = w.location.href, 
          hostname = d.location.hostname,
          idExists = d.getElementById('cr__a1db7898') || false,
          lens     = (!idExists) ? d.createElement('div'): '',
          script   = d.createElement('script');

    if (idExists) { 
        return console.log('Duplicate Lens ID: a1db7898');
    }

    lens.setAttribute('id', 'cr__a1db7898');
    target.html(lens);

    script.type = 'text/javascript';
    script.src  = 'https://embed.crowdriff.com/js/config?hash=a1db7898&hostname=' + hostname + '&url=' + url;

    if (!d.getElementById('cr__styles')) {
        addCss('https://embed.crowdriff.com/assets/css/app.css', 'cr__styles');
    }
    
    addCss('https://embed.crowdriff.com/css?hash=a1db7898&hostname=' + hostname);   
    target.append(script);
  }*/

  $(document).ready(function(){    
    sectionHeights();
    //addCrowdRiff();
  }); //end doc ready

  $(window).load(function(){
    sectionHeights();
  }); //end window load
  
  $(window).on('resize', function(){
    sectionHeights();
  }); //end window resize

})(jQuery);
  </script>
  <style>
    @import url(https://fonts.googleapis.com/css?family=Bevan);
    @import url(https://fonts.googleapis.com/css?family=Open+Sans:400,300,700);

    .blue_square {
      width:15px;
      height:15px;
      margin-right:10px;
      background-color:#00adef;
      float:left;
    }

    .sep {
      background-image:url('https://unmistakablylawrence.com/wp-content/uploads/2016/05/repeating_background.jpg');
      background-repeat:repeat-x;
      height:24px;
      margin-top:10px;
      margin-bottom:10px;
    }

    .yellow_border {
      border:7px solid #fdb31c !important;
    }

    .blue_border {
      border:7px solid #0067af !important;
    }

    .green_border {
      border:7px solid #6dbe4b !important;
    }

    .teal_border {
      border:7px solid #00ada7 !important;
    }

    .orange_border {
      border:7px solid #f7901e !important;
    }

    .entry-content a {
      color:#f36b22;
      text-decoration: underline;
    }

    .entry-content p {
      text-align:left;
      font-size:1.5em;
    }

    .entry-content {
      font-family: 'Open Sans', sans-serif;
      margin-top:20px;
    }

    .entry-content h2 {
      font-family: 'Bevan', cursive;
    }

    .entry-content h3 {
      color:#00adef;
    }

    .shotput h2, .shotput h4, .shotput h5, .shotput p, .shotput .content-column img {
      text-align:center !important;
    }
    .shotput h5 {
      color:#f18904;
      font-size:2em;
    }
    .shotput p{
      margin-bottom:10px !important;
      font-size:14px;
    }
    .noborder {
      border:none !important;
      box-shadow:none !important;
    }

    @media screen and (max-width: 768px) {
        .entry-content img {
            width: 95%;
        }
    }
  </style>
<?php }

// LEAVE THIS AT THE END
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' ); // Genesis Force Full Width Page
genesis();