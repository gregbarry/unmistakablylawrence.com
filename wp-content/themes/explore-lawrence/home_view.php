<?php
   /* Template Name: Home Page */
add_action('wp_enqueue_scripts', 'queue_home_add');

function queue_home_add() {
    wp_enqueue_style( 'less-style-home', get_stylesheet_directory_uri() . '/views-styles/home-page-view.less' );
}

add_filter( 'the_content', 'home_view_func' ); // Full Width View

function twitter($source, $author) {
    $args = array (
      'post_type'       => 'custom_tweet',
      'twitter_sources' => $source,
      'orderby'         => 'rand',
      'posts_per_page'  => '1',
    );

    $twitter_hash_query = new WP_Query($args);

    if ($twitter_hash_query->have_posts()) {
        while ($twitter_hash_query->have_posts()) {
          $twitter_hash_query->the_post();
          $twitter_hash_content = get_the_content();
          $twitter_hash_author= ($author) ? $author : get_field('author', get_the_ID());

          if(!$twitter_hash_author) {
              $twitter_hash_author = '@noauthor';
          }

          $twittercontent .= '<div class="twitter-body">'.$twitter_hash_content.'</div>
          <div class="twitter-footer">
              <div class="twitter-author">'.$twitter_hash_author.'</div>
              <div class="twitter-icon">
                  <i class="fa fa-twitter"></i>
              </div>
          </div>';
        }
    }

    wp_reset_postdata();

    return $twittercontent;
}

function home_view_func() {
    global $wpdb;

    $slidermain  = get_field( 'main_slider' );
    $slidersmall = get_field( 'small_slider' );

    $i = 1;
    $x = 3;
    $featured_listing;

    while($i <= 7) {
        ${"title"     . $i} = get_field('title_'.$i);
        ${"content"   . $i} = get_field('content_'.$i);
        ${"link"      . $i} = get_field('link_'.$i);
        ${"image"     . $i} = get_field('image_'.$i);
        ${"linktext"  . $i} = get_field('link_text_'.$i);
        //var_dump(${"image".$i});
        ${"secimage"  . $i} = (!empty(${"image" . $i})) ? 'style="background-image: url('.${"image" . $i}["url"].')"' : '';
        ${"morelink"  . $i} = (!empty(${"link" . $i}))  ? '<a href="'.${"link" . $i}.'" class="sec-more-link">'.${"linktext" . $i}.'</a>' : '';

        if ($i <= 5) {
            ${"featured_listing_area_" . $i}       = get_field('set_featured_area_'.$i);
            ${"faimage" . $i}                      = get_field('section_'.$x.'_featured_image');
            ${"featured_listing_title_" . $i}      = get_field('section_'.$x.'_featured_title');
            ${"featured_listing_link_" . $i}       = get_field('section_'.$x.'_featured_title_link');

            if (${"featured_listing_area_" . $i}) {
                ${"featured_listing_" . $i} .= '<div class="featured-listing height1">';

                if (!empty(${"faimage" . $i})) {
                    ${"featured_listing_" . $i} .= '<div class="featured-listing-img';
                    if((!empty(${"featured_listing_area_" . $i}))){
                      ${"featured_listing_" . $i} .= ' height3';
                    } else {
                      ${"featured_listing_" . $i} .= ' height1';
                    }
                    ${"featured_listing_" . $i} .= '" style="background-image: url('.${"faimage" . $i}["url"].')"></div>';
                }

                if((!empty(${"featured_listing_title_" . $i}))) {
                    ${"featured_listing_" . $i} .= '<div class="fl-content">';
                    if( !empty(${"featured_listing_title_" . $i}) ){
                        if( !empty(${"featured_listing_link_" . $i}) ){
                            ${"featured_listing_" . $i} .= '<h4><a href="'.${"featured_listing_link_" . $i}.'">'.${"featured_listing_title_" . $i}.'</a></h4>';
                        } else {
                            ${"featured_listing_" . $i} .= '<h4>'.${"featured_listing_title_" . $i}.'</h4>';
                        }
                    }
                    ${"featured_listing_" . $i} .= '</div>';
                }

                ${"featured_listing_" . $i} .= '</div>';
            } else {
                ${"featured_listing_" . $i} .= '<div class="featured-listing height1"></div>';
            }
            $x++;
        }
        $i++;
    }

    $featured_listings  = $featured_listing_1 . $featured_listing_2 . $featured_listing_3 . $featured_listing_4 . $featured_listing_5;

    $instagram = get_field( 'instagram_shortcode' );

    /* ------------------------------------------------- Start Events ------------------------------------------------- */
    $listings = $wpdb->get_results("SELECT e.title, e.location, d.eventdate, e.eventid FROM sv_events e, sv_event_dates d
                                    WHERE d.eventid = e.eventid
                                    AND d.is_deleted = '0' AND e.is_deleted = '0'
                                    AND e.featured = 'yes'
                                    AND eventdate != '0000-00-00'
                                    AND eventdate > NOW() - INTERVAL 1 DAY
                                    GROUP BY e.eventid
                                    ORDER BY d.eventdate ASC LIMIT 5");

    $events_title          = get_field( 'event_title_text' );
    $events_view_link_text = get_field( 'event_link_text' );
    $events_view_link      = get_field( 'event_link' );

    $event_listing = '<div class="event-list-wrap">';

    foreach($listings as $listing){
        $date1          = DateTime::createFromFormat('Y \- m \- d', $listing->eventdate);

        $eventdate      = $date1->format('M d');
        $eventtitle     = ($listing->title != NULL) ? $listing->title : 'N/A';
        $eventlocation  = ($listing->location != NULL) ? $listing->location : '';

        $linkName = preg_replace('/[^A-Za-z0-9]/', "", $eventtitle) . '-' . $listing->eventid;

        $event_listing .= '<div class="event-item ">
                              <div class="event-date">'.$eventdate.'</div>
                              <div class="event-title"><a class="white" href="/events-calendar/#'.$linkName.'">'.$eventtitle.'</a></div>
                              <div class="event-location">'.$eventlocation.'</div>
                           </div>';
    }

    $event_listing .= '</div>';

  /* ------------------------------------------------- Start Twitter ------------------------------------------------- */

  $twittercontent1 = twitter('hashtag', false);
  $twittercontent2 = twitter('explorelks', '@exploreLKS');

  /* ------------------------------------------------- Start of Output ------------------------------------------------- */

  $output .= '<div class="home-page">
    <div class="home-page-wrap">
      <div class="home-top">
        <div class="home-slider">
          '.$slidermain.'
        </div>
        <div class="top-right">
        <div class="home-section height1 homesection1">
          <div class="sec-left image-side height1" '.$secimage1.'></div>
            <div class="sec-right content-side">
              <div class="sec-title">
                <h2>'.$title1.'</h2>
              </div>
              <div class="sec-content">'.$content1.'</div>
              '.$morelink1.'
            </div>
          </div>
          <div class="home-section height1 homesection2">
            <div class="sec-right image-side height1" '.$secimage2.'></div>
            <div class="sec-left content-side">
              <div class="sec-title">
                <h2>'.$title2.'</h2>
              </div>
              <div class="sec-content">'.$content2.'</div>
              '.$morelink2.'
            </div>
          </div>
        </div>
      </div>
      <div class="home-middle">
        <div class="home-middle-left">
          '.$featured_listings.'
        </div>
        <div class="home-middle-center">
          <div class="home-section height1 homesection3">
            <div class="sec-right image-side height1" '.$secimage3.'></div>
            <div class="sec-left content-side">
              <div class="sec-title">
                <h2>'.$title3.'</h2>
              </div>
              <div class="sec-content">'.$content3.'</div>
              '.$morelink3.'
            </div>
          </div>
          <div class="home-section height1 homesection4">
            <div class="sec-right image-side height1" '.$secimage4.'></div>
            <div class="sec-left content-side">
              <div class="sec-title">
                <h2>'.$title4.'</h2>
              </div>
              <div class="sec-content">'.$content4.'</div>
              '.$morelink4.'
            </div>
          </div>
          <div class="home-section height1 homesection5">
            <div class="sec-right image-side height1" '.$secimage5.'></div>
            <div class="sec-left content-side">
              <div class="sec-title">
                <h2>'.$title5.'</h2>
              </div>
              <div class="sec-content">'.$content5.'</div>
              '.$morelink5.'
            </div>
          </div>
          <div class="home-section height1 homesection6">
            <div class="sec-right image-side height1" '.$secimage6.'></div>
            <div class="sec-left content-side">
              <div class="sec-title">
                <h2>'.$title6.'</h2>
              </div>
              <div class="sec-content">'.$content6.'</div>
              '.$morelink6.'
            </div>
          </div>
          <div class="home-section height1 homesection7">
            <div class="sec-right image-side height1" '.$secimage7.'></div>
            <div class="sec-left content-side">
              <div class="sec-title">
                <h2>'.$title7.'</h2>
              </div>
              <div class="sec-content">'.$content7.'</div>
              '.$morelink7.'
            </div>
          </div>
        </div>
        <div class="home-middle-left mobile">
          '.$featured_listings.'
        </div>
        <div class="home-middle-right">
          <div class="upcoming-events-area height2">
            <div class="upcoming-events-title">
              <h2>'.$events_title.'</h2>
            </div>
            <div class="upcoming-events-link">
              <a href="'.$events_view_link.'" target="_blank">'.$events_view_link_text.' <i class="fa fa-calendar-o"></i></a>
            </div>
            <div class="upcoming-events-list">'.$event_listing.'</div>
          </div>
          <div class="twitter-area twitter-area-1 height1">
            <div class="twitter-content">
              '.$twittercontent1.'
            </div>
          </div>
          <div class="home-mini-slider height1">
            '.$slidersmall.'
          </div>
          <div class="twitter-area twitter-area-2 height1">
            <div class="twitter-content">
              '.$twittercontent2.'
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>';
  return $output;
}

add_action('genesis_after', 'section_height_biz');

function section_height_biz() { ?>
    <script>
        (function($){
          function sectionHeights(){
            $('.height1').css('height', '');
            $('.height2').css('height', '');
            $('.height3').css('height', '');
            if ($(window).width() > 768) { // Stop below this media query
              var sliderheight = $('.home-slider').outerHeight(),
                  flcontent = $('.fl-content').outerHeight(),
                  sectionheight = sliderheight / 2,
                  instagramimageheight = $('.instagram-feed-area .instagram-image').outerHeight(),
                  instagramiconpadding = instagramimageheight / 2;
              $('.height1').css('height', sectionheight);
              $('.height2').css('height', sectionheight*2);
              $('.height3').css('height', sectionheight - flcontent);
              $('.instagram-icon').css('height', instagramimageheight);
              $('.instagram-icon').css('padding-top', instagramiconpadding - 22);
            }
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
<? }

add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' ); // Genesis Force Full Width Page
genesis();