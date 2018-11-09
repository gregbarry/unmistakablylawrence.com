<?php

 /* Template Name: News Stories */
 // DO NOT PLACE ANYTHING BEFORE THIS!!!!

 
// queue - js + css
remove_action( 'genesis_after_header', 'featured_title');
add_action( 'genesis_before_content', 'regular_title');

add_action('wp_enqueue_scripts', 'queue_full_add');
function queue_full_add() {
  // .less file
  wp_enqueue_style( 'less-style-blog-roll', get_stylesheet_directory_uri() . '/views-styles/blog-roll-view.less' );
	wp_enqueue_style( 'pagenationcss', get_stylesheet_directory_uri(). '/lib/css/simplePagination.css' );
	wp_enqueue_script( 'pagenationjs', get_stylesheet_directory_uri(). '/lib/js/jquery.simplePagination.js', array('jquery'), null, true );
	wp_enqueue_script( 'pagenationinit', get_stylesheet_directory_uri(). '/lib/js/paginate-blog.js', array( 'jquery', 'pagenationjs' ), null, true );
}

// add_filter( 'the_content', 'blog_roll_view_func' ); // Full Width View
add_action( 'genesis_entry_content', 'blog_roll_view_func', 20 ); // Blog Roll View

function blog_roll_view_func($content) {
  global $post;
  /*$blogImg = get_field('right_sidebar_image');
  if( !empty($blogImg) ){
			$blogImgStr = '<img src="'.$blogImg["url"].'" alt="'.$blogImg["alt"].'" />';
		}
  else{
    $blogImgStr = '';
  }
  echo '<div class="blog-roll-right">';
  echo '<div class="right-image">'.$blogImgStr.'</div>';
    if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar') );
  echo '</div>';
*/

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




  $output .= '<div class="blog-roll-left">';
  
  $storie_cats = get_field('which_stories');
  $stories = '';
  foreach ($storie_cats as $storie_key=>$storie_cat){
    $stories .= $storie_cat .', ';
  }
//The Query
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
// WP_Query arguments
$args = array (
	'post_type'              => 'news_story',
	'category_name'          => $stories,
	'pagination'             => true,
	'posts_per_page'         => '-1',
);

// The Query
$news_query = new WP_Query( $args );
$output .= '<div id="scroll-to"></div>';
//The Loop
while ( $news_query->have_posts() ) : $news_query->the_post();
  $excerpt = get_the_excerpt();
  // if (!$excerpt){$excerpt = 'word';}
  $output .= '<div class="individual-post listing-item">';
                if (has_post_thumbnail()){
                  $output.= '<a class="post-thumb" href="'.get_permalink().'">'.get_the_post_thumbnail( get_the_ID(), 'thumbnail').'</a>';
                }
                $output .= '<a class="title" href="'.get_permalink().'">'.get_the_title().'</a>
                '.$excerpt.'
              </div>';
endwhile;

// pager
$output .= "<div class='pagination'></div></div>";

    
  wp_reset_postdata();

  echo $output;

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
genesis();
/*if($news_query->max_num_pages>1){
  
   $output.= '<p class="pager"><div class="pagination-loc">Page '.$paged.' of '.$news_query->max_num_pages.'</div>';
   $active_class = 'class="active"';
    if($paged>=5){
       $output.= '<div class="pagination-first"><a href="'.get_category_link($category_id).'>First</a></div>';
    }
    if($paged!=1){
       $output.= '<div class="pagination-prev">'.get_previous_posts_link('<<').'</div>';
    }
    $i = 1;
    $end_for = $news_query->max_num_pages;
    $dots_pre = false;
    $dot_post = false;
    if ($paged >=3){
      $i=$paged-2;
      $dots_pre = !$dots_pre;
    }
    if ($paged + 2 <= $end_for){
      $end_for = $paged + 2;
      $dots_post = !$dots_post;
    }
    
    if ($dots_pre){$output.='<div class="pagination-num">...</div>';}
    for($i;$i<=$news_query->max_num_pages;$i++){
       $output.='<div class="pagination-num"><a href="'.get_category_link($category_id).'page/'.$i.'" '.(($paged==$i)? $active_class:'').'>'.$i.'</a></div>';
    }
    if ($dots_post){$output.='<div class="pagination-num">...</div>';}
    if($paged < $news_query->max_num_pages){
       $output.= get_next_posts_link('Older stories>>', $news_query->max_num_pages);
    }
    $output.='</p>';
} */