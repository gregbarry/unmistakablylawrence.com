<?php

 /* Template Name: Meet Landing Page */
 // DO NOT PLACE ANYTHING BEFORE THIS!!!!

  // queue - js + css
add_action('wp_enqueue_scripts', 'queue_meet_add');
function queue_meet_add() {
  // .less file
  wp_enqueue_style( 'less-style-meet', get_stylesheet_directory_uri() . '/views-styles/meet-landing-view.less' );
}

add_filter( 'the_content', 'meet_landing_view_func' ); // Full Width View


function meet_landing_view_func() {
  $slider = get_field( 'slider_shortcode' );
  $image1 = get_field( 'featured_area_1_image' );
    if( !empty($image1) ){
          $featuredimage1 = '<div class="featured-area-image" style="background-image: url('.$image1["url"].')"></div>';// 
        } else{
          $featuredimage1 = '';
        }
  $title1 = get_field( 'featured_area_1_title' );
  $content1 = get_field( 'featured_area_1_content' );
  $link1 = get_field( 'featured_area_1_link' );
  $linktext1 = get_field( 'featured_area_1_link_text' );
  $image2 = get_field( 'featured_area_2_image' );
    if( !empty($image2) ){
          $featuredimage2 = '<div class="featured-area-image" style="background-image: url('.$image2["url"].')"></div>';
        } else{
          $featuredimage2 = '';
        }
  $title2 = get_field( 'featured_area_2_title' );
  $content2 = get_field( 'featured_area_2_content' );
  $link2 = get_field( 'featured_area_2_link' );
  $linktext2 = get_field( 'featured_area_2_link_text' );
  $icon1 = get_field( 'icon_column_1' );
    if( !empty($icon1) ){
          $iconimage1 = '<div class="icon"><img src="'.$icon1["url"].'" alt="'.$icon1["alt"].'" /></div>';
        } else{
          $iconimage1 = '';
        }
  $titlecol1 = get_field( 'column_1_title' );
  $contentcol1 = get_field( 'column_1_content' );
  $linkcol1 = get_field( 'column_1_link' );
  $linktextcol1 = get_field( 'column_1_link_text' );
  $link2col1 = get_field( 'column_1_link_2' );
  $link2textcol1 = get_field( 'column_1_link_text_2' );
  $icon2 = get_field( 'icon_column_2' );
    if( !empty($icon2) ){
          $iconimage2 = '<div class="icon"><img src="'.$icon2["url"].'" alt="'.$icon2["alt"].'" /></div>';
        } else{
          $iconimage2 = '';
        }
  $titlecol2 = get_field( 'column_2_title' );
  $contentcol2 = get_field( 'column_2_content' );
  $linkcol2 = get_field( 'column_2_link' );
  $linktextcol2 = get_field( 'column_2_link_text' );
  $icon3 = get_field( 'icon_column_3' );
    if( !empty($icon3) ){
          $iconimage3 = '<div class="icon"><img src="'.$icon3["url"].'" alt="'.$icon3["alt"].'" /></div>';
        } else{
          $iconimage3 = '';
        }
  $titlecol3 = get_field( 'column_3_title' );
  $contentcol3 = get_field( 'column_3_content' );
  $linkcol3 = get_field( 'column_3_link' );
  $linktextcol3 = get_field( 'column_3_link_text' );
  $icon4 = get_field( 'icon_column_4' );
    if( !empty($icon4) ){
          $iconimage4 = '<div class="icon"><img src="'.$icon4["url"].'" alt="'.$icon4["alt"].'" /></div>';
        } else{
          $iconimage4 = '';
        }
  $titlecol4 = get_field( 'column_4_title' );
  $contentcol4 = get_field( 'column_4_content' );
  $linkcol4 = get_field( 'column_4_link' );
  $linktextcol4 = get_field( 'column_4_link_text' );
  $link2col4 = get_field( 'column_4_link_2' );
  $link2textcol4 = get_field( 'column_4_link_text_2' );

  $output = '';
  $output .= '<div class="meet-landing">
    <div class="meet-wrap">
      <div class="meet-top">
        <div class="meet-slider">
        '.$slider.'
        </div>
        <div class="meet-featured-area" id="featured-1">
        '.$featuredimage1.'
        <div class="title">
          <h2>'.$title1.'</h2>
        </div>
        <div class="content-area">'.$content1.'</div>
        <a href="'.$link1.'" class="link-text">'.$linktext1.'</a>
        </div>
        <div class="meet-featured-area" id="featured-2">
        '.$featuredimage2.'
        <div class="title">
          <h2>'.$title2.'</h2>
        </div>
        <div class="content-area">'.$content2.'</div>
        <a href="'.$link2.'" class="link-text">'.$linktext2.'</a>
        </div>
      </div>
      <div class="meet-bottom">
        <div class="columns-area">
          <div class="column-area" id="column1">
            '.$iconimage1.'
            <div class="column-right">
              <div class="title">
                <h2>'.$titlecol1.'</h2>
              </div>
              <div class="content-area">'.$contentcol1.'</div>
              <a href="'.$linkcol1.'" class="link-text">'.$linktextcol1.'</a><br />
              <a href="'.$link2col1.'" class="link-text">'.$link2textcol1.'</a>
            </div>
          </div>
          <div class="column-area" id="column2">
            '.$iconimage2.'
            <div class="column-right">
              <div class="title">
                <h2>'.$titlecol2.'</h2>
              </div>
              <div class="content-area">'.$contentcol2.'</div>
              <a href="'.$linkcol2.'" class="link-text">'.$linktextcol2.'</a>
            </div>
              </div>
          <div class="column-area" id="column3">
            '.$iconimage3.'
            <div class="column-right">
              <div class="title">
                <h2>'.$titlecol3.'</h2>
              </div>
              <div class="content-area">'.$contentcol3.'</div>
              <a href="'.$linkcol3.'" class="link-text">'.$linktextcol3.'</a>
            </div>
          </div>
          <div class="column-area" id="column4">
            '.$iconimage4.'
            <div class="column-right">
              <div class="title">
                <h2>'.$titlecol4.'</h2>
              </div>
              <div class="content-area">'.$contentcol4.'</div>
              <a href="'.$linkcol4.'" class="link-text">'.$linktextcol4.'</a><br />
              <a href="'.$link2col4.'" class="link-text">'.$link2textcol4.'</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>';
  return $output;
}

add_action('genesis_after', 'img_height_biz');
function img_height_biz() { ?>
  <script>
(function($){
  function sectionHeights(){
      var imgwidth = $('.featured-area-image').innerWidth();
      // alert(sectionheight);
      $('.featured-area-image').css('min-height', imgwidth);
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