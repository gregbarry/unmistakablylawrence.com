<?php

/* Template Name: Deals View */

add_action('wp_enqueue_scripts', 'queue_event_add');
function queue_event_add() {
    wp_enqueue_style( 'less-style-event', get_stylesheet_directory_uri() . '/views-styles/events-listing-view.less' );
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
}

add_filter( 'the_content', 'deals_listing_view_content' ); // Deals View
function deals_listing_view_content() {
    global $wpdb;
    global $post;

    $eventlandingcontent   = get_field( 'event_landing_content' );
    $addeventtitle         = get_field( 'side_add_event_title' );
    $addeventcontent       = get_field( 'side_add_event_content' );
    $addeventlink          = get_field( 'side_add_event_link' );
    $eventlandingpageimage = get_field( 'event_landing_side_image');

    $output .= "<script>var postID=" . $post->ID . "</script>";
    $evensimage = ( !empty($eventlandingpageimage) ) ? '<img src="'.$eventlandingpageimage["url"].'" alt="'.$eventlandingpageimage["alt"].'" />' : '';

    $content = $post->post_content;

    $output .= '<div id="scroll-to" class="event-listings-area"><div class="listings-wrap-wrap"><div class="listings-left-wrap">';
    $output .= '<div class="landing-heading-area"><div class="landing-heading-wrap"><div class="landing-headline"></div><div class="landing-content-area">'.$eventlandingcontent.'</div></div></div>';
    $output .= '<div class="event-listings-title"></div>';

    $output .= '<div id="results"><!-- content will be loaded here --></div>';

    $output .= '<div class="pagination"><div class="pagination-wrap"></div></div>';
    $output .= '</div>
          <div class="listing-right-wrap">
            <div class="add-event-link-area">
              <div class="add-event-title">
                <h3>
                  '.$addeventtitle.'
                </h3>
              </div>
              <div class="add-event-text">
                '.$addeventcontent.'
              </div>
            </div>
            <div class="events-image">
              '.$evensimage.'
            </div>
          </div>
        </div>
      </div>';

  return do_shortcode( $output );
}

add_action('genesis_after', 'jquery_biz');
function jquery_biz() { ?>

    <script>
    jQuery(document).ready(function() {
        if(document.location.hash === '#thankyou') {
          window.location.replace('https://unmistakablylawrence.com/thank-you/');
        }

        jQuery('.MyDate').datepicker({
            dateFormat : 'mm/dd/yy'
        });

        jQuery('.details-link').live('click', function() {
            event.preventDefault();
            jQuery(this).parent().children('.details-link').toggle();
            jQuery(this).parent().parent().children('.listing-content').children('.more-details').toggle(500);
        });

        jQuery('.listing-title').live('click', function() {
            jQuery(this).parent().children('.buttons').children('.details-link').toggle();
            jQuery(this).parent().children('.listing-content').children('.more-details').toggle(500);
        });

        jQuery("#results" ).load( "/data/fetch_deals.php", {
              "getcat"    : "<?=$_GET['cat']?>",
              "fromDate"  : "<?=$_GET['FromDate']?>",
              "toDate"    : "<?=$_GET['ToDate']?>",
              "postID"    : postID
        }, function() {
            var hash = location.hash;

            if(hash != '' && hash != '#thankyou'){
                jQuery('html, body').animate({
                    scrollTop: jQuery(hash).offset().top
                }, 1000);
            }
        }); //load initial records

        //executes code below when user click on pagination links
        jQuery("#results").on( "click", ".pagination a", function (e){
            e.preventDefault();
            jQuery("html, body").animate({ scrollTop: 325 }, "slow");
            jQuery(".loading-div").show(); //show loading element
            var page = jQuery(this).attr("data-page"); //get page number from link
            jQuery("#results").load("/data/fetch_deals.php",{
              "page"      : page,
              "getcat"    : "<?=$_GET['cat']?>",
              "fromDate"  : "<?=$_GET['FromDate']?>",
              "toDate"    : "<?=$_GET['ToDate']?>",
              "postID"    : postID
              }, function(){ //get content from PHP page
                jQuery(".loading-div").hide(); //once done, hide loading element
            });

        });
    });
    </script>
  <?php
    $output = '[su_lightbox_content id="eventform" class="rfp-form-area"  text_align="left" width="60%"]<script type="text/javascript" src="https://lawrence.simpleviewcrm.com/webapi/widgets/submitevent/submiteventjs.cfm"></script>[/su_lightbox_content]';
    echo do_shortcode( $output );

}

// LEAVE THIS AT THE END
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' ); // Genesis Force Full Width Page
genesis();
