  <?php
   /* Template Name: Events View */

  // queue - js + css
  add_action('wp_enqueue_scripts', 'queue_event_add');
  function queue_event_add() {
    // .less file
    wp_enqueue_style( 'less-style-event', get_stylesheet_directory_uri() . '/views-styles/events-listing-view.less' );

    // Date Picker
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
  }

  add_action( 'genesis_after_header', 'event_dynamic_search_header', 99); // Search Bar
  add_filter( 'the_content', 'events_listing_view_content' ); // Listing And Search View

  function event_dynamic_search_header() {
    global $wpdb;

    $fd = (!empty($_GET['FromDate'])) ? $_GET['FromDate'] : '';
    $td = (!empty($_GET['ToDate'])) ? $_GET['ToDate'] : '';

    $subcats = $wpdb->get_results("SELECT DISTINCT c.categoryid, c.categoryname
                                   FROM `sv_event_categories` c
                                   WHERE is_deleted = 0
                                   ORDER BY c.categoryname ASC");

    foreach($subcats as $subcat){
      $selected = ($subcat->categoryid == $_GET['cat']) ? 'selected' : '';
      $options .= '<option value="'.$subcat->categoryid.'" '.$selected.'>'.$subcat->categoryname.'</option>';
    }
    ?>
      <div class="header-search-area">
        <div class="header-search-area-wrap">
          <div class="listing-search-title">
            Search <?=get_field( "event_search_text" )?>
          </div>
          <div class="listing-search-form">
            <form action="" method="get">
              <input type="text" class="MyDate" placeholder="From Date" name="FromDate" value="<?=$fd?>"/>
              <input type="text" class="MyDate" placeholder="To Date" name="ToDate" value="<?=$td?>"/>
              <select name="cat">
                <option style="display: none;" value="">Type</option>
                <option value="ALL">All</option>
                <?=$options?>
              </select>
              <input type="submit" value="Search">
            </form>
          </div>
        </div>
      </div>
  <?
  }

  function events_listing_view_content() {
    // add all the output in this function
    global $wpdb;
    global $post;

    $getstartbetween = (!empty($_GET['FromDate'])) ? DateTime::createFromFormat("m \/ d \/ Y", $_GET['FromDate']) : '';
    $startbetween    = (!empty($_GET['FromDate'])) ? $getstartbetween->format("Y-m-d") : '';

    $eventlandingheadline  = get_field( 'event_landing_headline' );
    $eventlandingcontent   = get_field( 'event_landing_content' );
    $addeventtitle         = get_field( 'side_add_event_title' );
    $addeventcontent       = get_field( 'side_add_event_content' );
    $addeventlink          = get_field( 'side_add_event_link' );
    $eventlandingpageimage = get_field( 'event_landing_side_image');

    $evensimage = ( !empty($eventlandingpageimage) ) ? '<img src="'.$eventlandingpageimage["url"].'" alt="'.$eventlandingpageimage["alt"].'" />' : '';

    $content = $post->post_content;

    $output .= $content;

    $output .= '<div id="scroll-to buddybuddybuddy" class="event-listings-area"><div class="listings-wrap-wrap"><div class="listings-left-wrap">';
    $output .= '<div class="landing-heading-area"><div class="landing-heading-wrap"><div class="landing-headline"><h2>'.$eventlandingheadline.'</h2></div><div class="landing-content-area">'.$eventlandingcontent.'</div></div></div>';
    $output .= '<div class="event-listings-title"><h2>Events</h2></div>';

    $output .= '<div id="results"><!-- content will be loaded here --></div>';

    $output .= $list.'<div class="pagination"><div class="pagination-wrap"></div></div>';
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
              [su_lightbox type="inline" src="#eventform"]
              <a class="add-event-link button" href="#">
                Add Event
              </a>
              [/su_lightbox]
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
        if( document.location.hash === '#thankyou' ) {
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

        jQuery("#results" ).load( "/data/fetch_pages.php", {
              "getcat"    : "<?=$_GET['cat']?>",
              "fromDate"  : "<?=$_GET['FromDate']?>",
              "toDate"    : "<?=$_GET['ToDate']?>",
        }); //load initial records

        //executes code below when user click on pagination links
        jQuery("#results").on( "click", ".pagination a", function (e){
            e.preventDefault();
            jQuery("html, body").animate({ scrollTop: 650 }, "slow");
            jQuery(".loading-div").show(); //show loading element
            var page = jQuery(this).attr("data-page"); //get page number from link
            jQuery("#results").load("/data/fetch_pages.php",{
              "page"      : page,
              "getcat"    : "<?=$_GET['cat']?>",
              "fromDate"  : "<?=$_GET['FromDate']?>",
              "toDate"    : "<?=$_GET['ToDate']?>"
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