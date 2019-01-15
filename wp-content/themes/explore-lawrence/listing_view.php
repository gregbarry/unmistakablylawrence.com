<?php
/* Template Name: Listing View */
// DO NOT PLACE ANYTHING BEFORE THIS!!!!

/* Contents:
Enqueue - js + css
Add Actions and Filters
Handle Search Form Output
Handle Content Output
  Call SQL
  View 1 & 2
    Google Maps
  View 3 aka Details
THE END */

// Enqueue - js + css
function queue_listing_add()
{
	global $post;
	$pagename = $post->post_name;

    // .less file
    wp_enqueue_style('less-style-listing', get_stylesheet_directory_uri() . '/views-styles/listing-view.less');
    wp_enqueue_style('less-style-details', get_stylesheet_directory_uri() . '/views-styles/details-view.less');

    //slider
    wp_enqueue_style('flex-style-p', get_stylesheet_directory_uri() . '/lib/ml/public.css');
    wp_enqueue_style('flex-style-s', get_stylesheet_directory_uri() . '/views-styles/flex.less');
    wp_enqueue_style('flex-style-flex', get_stylesheet_directory_uri() . '/lib/ml/flexslider.css');
    wp_enqueue_script('flex-js', get_stylesheet_directory_uri() . '/lib/ml/jquery.flexslider-min.js', array('jquery'));

    // pagination
    //wp_enqueue_style( 'pagenationcss', get_stylesheet_directory_uri(). '/lib/css/simplePagination.css' );
    wp_enqueue_script('pagenationjs', get_stylesheet_directory_uri() . '/lib/js/jquery.simplePagination.js', array('jquery'), null, true);
    if (empty($_REQUEST['listingID']) && (!empty($_REQUEST['r']) || (!empty($_REQUEST['sc'])) || get_field('select_a_category') == 'map' || $pagename == "junior-olympics-accommodations")) wp_enqueue_script('pagenationinit', get_stylesheet_directory_uri() . '/lib/js/paginate.js', array('jquery', 'pagenationjs'), null, true);
    if (!empty($_REQUEST['listingID'])) wp_enqueue_script('maptogglejs', get_stylesheet_directory_uri() . '/lib/js/maptoggle.js', array('jquery'), null, false);
    wp_enqueue_script('googlemaps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAQAK1IB5dghlOj8vdrcq0uRGbw1Gz32uA&sensor=false', array(), null, false);
    wp_enqueue_script('categoryselect', get_stylesheet_directory_uri() . '/lib/js/categoryselect.js', array('jquery'), null, false);
}

global $post;
$pagename = $post->post_name;

// Add Actions and Filters
add_action('wp_enqueue_scripts', 'queue_listing_add');

if ($pagename == "junior-olympics-accommodations") {
    add_action('genesis_after_header', 'joa_search_header', 99); // Search Bar
} else {
    add_action('genesis_after_header', 'dynamic_search_header_new', 99); // Search Bar
}

add_filter('the_content', 'listing_view_content_new'); // Listing And Search View

function joa_search_header() {

    $rate_sel = urldecode($_REQUEST['rate']);
    $distance_sel = $_REQUEST['distance'];

    $rates = ['$', '$$', '$$$'];
    $distances = ['10 Miles or less', '11-20 Miles', '21-30 Miles', '31+ Miles'];

    echo '<div class="header-search-area"><div class="header-search-area-wrap">';
    echo '<div class="listing-search-title">Search ' . get_field("search_text") . '</div>';
    echo '<div class="listing-search-form">';
    echo '<form action="' . $_SERVER["REQUEST_URI"] . '" method="get">';

    echo '<select name="rate" class="category_select">';
    echo '<option value="">Rate</option>';

    foreach ($rates as $rate) {
        $rate_selected = "";

        if ($rate_sel == $rate) {
            $rate_selected = " selected ";
        }

        echo '<option ' . $rate_selected . ' value="' . $rate . '">' . $rate . '</option>';
    }

    echo '</select>';

    echo '<select name="distance" class="category_select">';
    echo '<option value="">Distance</option>';
    foreach ($distances as $distance) {
        $distance_selected = "";

        if ($distance_sel == $distance) {
            $distance_selected = " selected ";
        }

        echo '<option ' . $distance_selected . ' value="' . $distance . '">' . $distance . '</option>';
    }
    echo '</select>';

    echo '<input type="submit" value="Search"></form></div></div></div>';
}

// Handle Search Form Output
function dynamic_search_header_new()
{
    global $wpdb;

    $getcat = get_field('select_a_category');

    if (get_field('select_a_category') == '4') {
        $facility = true;
    }

    $subcatsJson = '';
    echo '<div class="header-search-area"><div class="header-search-area-wrap">';
    echo '<div class="listing-search-title">Search ' . get_field("search_text") . '</div>';
    echo '<div class="listing-search-form">';
    echo '<form action="' . $_SERVER["REQUEST_URI"] . '" method="get">';
    if (get_field('select_a_category') == 'map') {
        $cats = $wpdb->get_results("SELECT * FROM `sv_categories` WHERE is_deleted = 0 ORDER BY name");
        echo '<select name="c" class="category_select">';
        echo '<option value="" style="display: none;">Interest</option>';
        $selected_all_0 = (array_key_exists('c', $_REQUEST) && 'ALL' == $_REQUEST['c']) ? 'selected' : '';
        echo '<option value="ALL" ' . $selected_all_0 . '>All</option>';
        foreach ($cats as $cat) {
            if (in_array($cat->cat_id, array('2', '9', '19', '20'))) {
                continue;
            }
            $selected = (!empty($_REQUEST['c']) && $cat->cat_id == $_REQUEST['c']) ? 'selected' : '';
            echo '<option value="' . $cat->cat_id . '" ' . $selected . '>' . $cat->name . '</option>';
        }
        echo '</select>';
        $cid = (!empty($_REQUEST['c'])) ? $_REQUEST['c'] : 'NULL';
        $subcats = $wpdb->get_results("SELECT * FROM `sv_subcategories` WHERE is_deleted = 0 AND cat_id = ifnull(" . $cid . ",cat_id) ORDER BY name");
    } else {
        $subcats = $wpdb->get_results("SELECT * FROM `sv_subcategories` WHERE is_deleted = 0 AND cat_id = " . get_field('select_a_category') . " ORDER BY name");
    }
    if ($getcat != '4') {
        echo '<select name="sc" class="sub-categories">';
        echo '<option value="" style="display: none;">Type</option>';

        $selected_all_1 = (array_key_exists('sc', $_REQUEST) && 'ALL' == $_REQUEST['sc']) ? 'selected' : '';
        echo '<option value="ALL" ' . $selected_all_1 . '>All</option>';

        if ($_REQUEST['sc'] == '88') {
            echo '<option value="88" ' . $selected . '>JO 2017</option>';
        }

        foreach ($subcats as $subcat) {
            $selected = (!empty($_REQUEST['sc']) && $subcat->sub_cat_id == $_REQUEST['sc']) ? 'selected' : '';

            // Add JO 2017 if that param is present
            if ($subcat->sub_cat_id != '88') {
                echo '<option value="' . $subcat->sub_cat_id . '" ' . $selected . '>' . $subcat->name . '</option>';
            }
        }

        echo '</select>';
    }
    $regions = $wpdb->get_results("SELECT DISTINCT region_id,name FROM `sv_regions` WHERE is_deleted = 0 ORDER BY name");
    echo '<select name="r">';
    echo '<option value="" style="display: none;">Location</option>';
    $selected_all_2 = (array_key_exists('r', $_REQUEST) && 'ALL' == $_REQUEST['r']) ? 'selected' : '';
    echo '<option value="ALL" ' . $selected_all_2 . '>All</option>';
    foreach ($regions as $region) {
        $selected = (!empty($_REQUEST['r']) && $region->region_id == $_REQUEST['r']) ? 'selected' : '';

        // Remove "OTHER" region from view
        if ($region->region_id != '6') {
            echo '<option value="' . $region->region_id . '" ' . $selected . '>' . $region->name . '</option>';
        }
    }
    echo '</select>';
    echo '<input type="submit" value="Search"></form></div></div></div>';
}


// Handle Content Output
function listing_view_content_new()
{
    global $wpdb;
    global $post;

    $pagename = $post->post_name;

    $distance = $_REQUEST['distance'];
    $rate = $_REQUEST['rate'];

    // Add All The Output In This Function
    $output = '';
    $perPage = get_option('biz_per_page');
    $perPage = $perPage + 1;
    $getcat = get_field('select_a_category');

    if (get_field('select_a_category') != 'map') {
        $selectedCategory = get_field('select_a_category');
    } else {
        $selectedCategory = ((empty($_REQUEST['c']) || $_REQUEST['c'] == 'ALL') ? 'NULL' : $_REQUEST['c']);
    }
    // Get Fields From ACF
    $listinglandingheadline = get_field('landing_page_title');
    $listinglandingcontent = get_field('landing_page_content');

    $listingright = '';
    $listingsJson = '';
    $landingheaderarea = '';

    /* ------------------------------------------------- Call SQL ------------------------------------------------- */
    $listing_id = (!empty($_REQUEST['listingID'])) ? "'" . $_REQUEST['listingID'] . "'" : "NULL";
    $reg_id = ((empty($_REQUEST['r'])) || $_REQUEST['r'] == 'ALL') ? "NULL" : "'" . $_REQUEST['r'] . "'";
    $sub_cat_id = ((empty($_REQUEST['sc'])) || $_REQUEST['sc'] == 'ALL') ? "NULL" : "'" . $_REQUEST['sc'] . "'";
    $featured = "NULL";

    // set view for content
    if (!empty($_REQUEST['listingID'])) {
        // show details view
        $view = '3';
    } elseif ((!empty($distance) || !empty($rate) || !empty($_REQUEST['r']) || !empty($_REQUEST['sc'])) || in_array(get_field('select_a_category'), array('map', '4'))) {
        // show search results view
        $view = '2';

        if (get_field('subcategory') != "") {
            $sub_cat_id = get_field('subcategory');
        }
    } else {
        // show landing view
        $view = '1';
        $featured = "'Yes'";
    }

    // If you want to show the landing content for the map page remove "|| (get_field('select_a_category') == 'map')" from the view 2 above

    if ($_REQUEST['sc'] != '88') {
        $noOther = "AND r.region_id != 6";
    }

    if ($pagename == "junior-olympics-accommodations") {

        $featured = "NULL";

        $distance_query = ($distance) ? "AND i2.value = '" . $distance . "'" : "";
        $rate_query = ($rate) ? "AND i.value = '" . $rate . "'" : "";

        // Add Listings for Rate/Venue
        $listings = $wpdb->get_results("
			SELECT *, i3.value as region FROM sv_listings l
			INNER JOIN sv_additional_information i ON l.listing_id = i.listing_id
			INNER JOIN sv_additional_information i2 ON i.listing_id = i2.listing_id
            INNER JOIN sv_additional_information i3 ON i.listing_id = i3.listing_id
			INNER JOIN sv_additional_information i4 ON i.listing_id = i4.listing_id
            INNER JOIN sv_listings_subcategories_XREF xref ON l.listing_id = xref.listing_id
			WHERE (i.name = 'Rate Range')
			AND (i2.name = 'Distance to Venue')
			AND (i3.name = 'Region')
            AND (i4.name = 'Room Block Full')
            AND xref.sub_cat_id = 88
            AND i3.value != ''
			AND l.cat_id = 1
			" . $distance_query . "
			" . $rate_query . "
			AND l.listing_id = IFNULL(" . $listing_id . ",l.listing_id)
            ORDER BY i4.value ASC, i2.value ASC, l.sort_company ASC");

            //ORDER BY i2.value ASC");
			//ORDER BY ,l.sort_company ASC")
            //
    } else {
        $listings = $wpdb->get_results("
			SELECT DISTINCT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`, r.name as `region`
			FROM sv_listings l
			INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
			INNER JOIN sv_subcategories sc ON sc.cat_id = l.cat_id AND sc.is_deleted = '0'
			INNER JOIN sv_listings_subcategories_XREF x ON x.sub_cat_id = sc.sub_cat_id AND x.listing_id = l.listing_id AND x.is_deleted = '0' AND x.sub_cat_id != 88
			INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.region_id = l.region_id AND r.is_deleted = '0'
			LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
			LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
			WHERE l.is_deleted = '0'
				AND c.cat_id = IFNULL(" . $selectedCategory . ",c.cat_id)
				AND sc.sub_cat_id = IFNULL(" . $sub_cat_id . ",sc.sub_cat_id)
				AND r.region_id = IFNULL(" . $reg_id . ",r.region_id)
				" . $noOther . "
				AND l.listing_id = IFNULL(" . $listing_id . ",l.listing_id)
				AND ai.value = IFNULL(" . $featured . ",ai.value)
			ORDER BY l.sort_company ASC;");

        $temp = $featured;
        $featured = "'Yes'";
        $featured_listings = $wpdb->get_results("
			SELECT DISTINCT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`, r.name as `region`
			FROM sv_listings l
			INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
			INNER JOIN sv_subcategories sc ON sc.cat_id = l.cat_id AND sc.is_deleted = '0'
			INNER JOIN sv_listings_subcategories_XREF x ON x.sub_cat_id = sc.sub_cat_id AND x.listing_id = l.listing_id AND x.is_deleted = '0' AND x.sub_cat_id != 88
			INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.region_id = l.region_id AND r.is_deleted = '0'
			LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
			LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
			WHERE l.is_deleted = '0'
				AND c.cat_id = IFNULL(" . $selectedCategory . ",c.cat_id)
				AND sc.sub_cat_id = IFNULL(" . $sub_cat_id . ",sc.sub_cat_id)
				AND r.region_id = IFNULL(" . $reg_id . ",r.region_id)
				AND l.listing_id = IFNULL(" . $listing_id . ",l.listing_id)
				AND ai.value = IFNULL(" . $featured . ",ai.value)
			ORDER BY ai.value DESC, l.sort_company ASC;");
        $featured_count = count($featured_listings);

        $featured = $temp;
    }

    if ($view == '1') {
        // Use ACF Fields For Output
        $landingheaderarea = '<div class="landing-heading-area"><div class="landing-heading-wrap"><div class="landing-headline"><h2>' . $listinglandingheadline . '</h2></div><div class="landing-content-area">' . $listinglandingcontent . '</div></div></div>';
        $listinglandingpageimage = get_field('landing_page_image');
        if (!empty($listinglandingpageimage)) {
            $listingimage = '<img src="' . $listinglandingpageimage["url"] . '" alt="' . $listinglandingpageimage["alt"] . '" />';
        } else {
            $listingimage = '';
        }
        if (!empty($listinglandingpageimage)) {
            $listingright = '<img src="' . $listinglandingpageimage["url"] . '" alt="' . $listinglandingpageimage["alt"] . '" />';
        }
    }

    if ($view == '1' || $view == '2') {

        // Google Maps
        $mapIndex = 1;
        $i = 1;
        $mapsArray = array();
        foreach ($listings as $listing) {
            $linkAddress = str_replace(" ", "+", $listing->addr1);
            $linkAddress .= "+" . str_replace(" ", "+", str_replace("'", "", $listing->city));
            $linkAddress .= "+" . $listing->state;
            $link = "https://www.google.com/maps/dir//{$linkAddress}/@" . $listing->latitude . "," . $listing->longitude . ",17z";
            $link_to_directions = '<a href="' . $link . '" style=\"margin-top: 3px; padding: 3px; margin-left: 0px; font-size: 10px;\" class=\"listingButton\" target=\"_blank\">Get Directions</a>';
            $mpContent = "<div style=\"font-size: 14px; height: 125px;\"><b>{$listing->company}</b>";
            $mpContent .= "<p>{$listing->addr1}<br />";
            $mpContent .= ($listing->addr2 == "") ? "" : "{$listing->addr2}<br />";
            $mpContent .= "{$listing->city}, {$listing->state} {$listing->zip}<br />";
            if (!empty($listing->toll_free)) $mpContent .= "{$listing->toll_free}</p>";
            else $mpContent .= "{$listing->phone}</p>";
            $mpContent .= "<p><a href=\"{$listing->web_url}\" style=\"margin-top: 3px; padding: 3px; margin-left: 0px; font-size: 10px;\" class=\"listingButton\" target=\"_blank\">View Website</a>" . $link_to_directions . "</p></div>";
            $mpContent = str_replace("\n", '', str_replace("'", '&#39;', $mpContent));
            $listingsJson .= "['" . str_replace("\n", '', str_replace("'", '&#39;', $listing->region)) . "'," . $listing->latitude . ", " . $listing->longitude . ", " . $i . ", '" . $mpContent . "'],";
            $i++;
            if ($i == $perPage) {
                $listingright .= '<div style="float:left; width: 365px; height: 365px; position: absolute;" class="maps" id="map' . $mapIndex . '"></div>';
                $mapsArray[] = $listingsJson;
                $listingsJson = '';
                $i = 1;
                $mapIndex++;
            }
        }
        if ($i != $perPage) {
            if (!empty($listingsJson)) {
                $mapsArray[] = $listingsJson;
                $listingright .= '<div style="float:left; width: 365px; height: 365px; position: absolute;" class="maps" id="map' . $mapIndex . '"></div>';
            }
        }
        $i = 1;

        ob_start();
        ?>
        <script type="text/javascript">
            var locations = [];
            var infowindow = [];
            var map = [];
            var perPage = jQuery('.page-var').data('per-page');
            var alreadyInit = [];
            function initialize() {
                if (alreadyInit[0]) {
                    return;
                }
                alreadyInit[0] = true;
                <?php
                        foreach($mapsArray as $locationsJson) {
                      if (!empty($locationsJson)){?>
                locations[<?=$i?>] = [<?=$locationsJson?>];
                infowindow[<?=$i?>] = new google.maps.InfoWindow();
                map[<?=$i?>] = new google.maps.Map(document.getElementById('map<?=$i?>'), {
                    zoom: 14,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                <?php    }
                            $i++;
                      }
                ?>
                mapInit(1);
            }

            function mapInit(whichMap) {
                if (alreadyInit[whichMap]) {
                    return;
                }
                if (!alreadyInit[0]) {
                    initialize();
                }
                alreadyInit[whichMap] = true;
                var marker, i, icoImg, position;
                var listingCount = whichMap * perPage - perPage + 1;
                var mapBounds = new google.maps.LatLngBounds();
                for (i = 0; i < locations[whichMap].length; i++) {

                    icoImg = {
                        url: 'https://unmistakablylawrence.com/wp-content/themes/explore-lawrence/images/map-pins/' + listingCount + '.png',
                        size: new google.maps.Size(50, 50),
                        anchor: new google.maps.Point(25, 25),
                        scaledSize: new google.maps.Size(36, 36),
                    }
                    if (locations[whichMap][i][1] != null) {
                        position = new google.maps.LatLng(locations[whichMap][i][1], locations[whichMap][i][2]);
                        markerOptions = {
                            position: position,
                            map: map[whichMap],
                            icon: icoImg
                        }
                        marker = new google.maps.Marker(markerOptions);
                        mapBounds.extend(position);
                        google.maps.event.addListener(marker, 'click', (function (marker, i) {
                            return function () {
                                infowindow[whichMap].setContent(locations[whichMap][i][4]);
                                infowindow[whichMap].open(map[whichMap], marker);
                            }
                        })(marker, i));
                    }
                    listingCount++;
                }
                var theMap = map[whichMap];
                theMap.fitBounds(mapBounds);
                var classname = document.getElementsByClassName("classname");
            }
            google.maps.event.addDomListener(window, "load", initialize);
        </script>
        <?php
        $temp = ob_get_contents();
        ob_end_clean();
        $output .= $temp;

    }

    /* ------------------------------------------------- View 1 & 2 ------------------------------------------------- */
    if (($view == '1') || ($view == '2')) {
        $output .= '<div id="scroll-to" class="listings-area"><div class="listings-wrap">' . $landingheaderarea . '<div class="listing-right">' . $listingright . '</div><div class="listing-left">';

        if ($view == '1' && $featured != "NULL") {
            $list = '<span class="featured">FEATURED</span>';
        } else {

            if ($featured_count > 0) {
                $num = 1;
                $list = "<span class='featured'>FEATURED</span>";
                foreach ($featured_listings as $listing) {
                    if ($num <= 2) {
                        $listingid = $listing->listing_id;
                        if ($listing->company != NULL) {
                            $company = $listing->company;
                        } else {
                            $company = 'N/A';
                        }
                        if ($listing->web_url != NULL) {
                            $url = $listing->web_url;
                            $weburl = '<a href="' . $url . '" target="_blank" class="site-link">Visit Website</a><br />';
                        } else {
                            $weburl = '';
                        }
                        if ($listing->region != NULL) {
                            $region = $listing->region;
                        } else {
                            $region = 'N/A';
                        }
                        if ($listing->toll_free != NULL) {
                            $phone = $listing->toll_free;
                        } else if ($listing->phone != NULL) {
                            $phone = $listing->phone;
                        } else if ($listing->alt_phone != NULL) {
                            $phone = $listing->alt_phone;
                        } else {
                            $phone = 'none';
                        }
                        if ($phone != 'none') {
                            $tel = str_replace(array(' ', '(', ')', '-', '.', '_'), '', $phone);
                            $phonelink = "<a href='tel:{$tel}'>{$phone}</a>";
                        } else {
                            $phonelink = 'N/A';
                        }
                        $logo_file = $listing->logo_file;
                        if (($logo_file != NULL) && (strpos($logo_file, 'eps') === FALSE)) {
                            $image = $listing->img_path;
                            $image = preg_replace("/^http:/i", "https:", $image);
                            $image .= $listing->logo_file;
                            $alt = $company . ' Featured Image';
                        } else {
                            $image = get_stylesheet_directory_uri() . '/images/placeholder.jpg';
                            $alt = 'Two light gray placeholder image silhouettes on white background';
                        }
                        $address_string = '';
                        if ($company != 'N/A') {
                            $address_string .= $company . ' ';
                        }
                        $street_address = '';
                        if ($listing->addr1 != NULL) {
                            $street_address .= $listing->addr1 . ' ';
                        }
                        if ($listing->addr2 != NULL) {
                            $street_address .= $listing->addr2 . ' ';
                        }
                        if ($listing->addr3 != NULL) {
                            $street_address .= $listing->addr3 . ' ';
                        }
                        $address_string .= $street_address;
                        if ($listing->city != NULL) {
                            $city_address = $listing->city;
                            $address_string .= $city_address . ' ';
                        }
                        if ($listing->state != NULL) {
                            $state_address = $listing->state;
                            $address_string .= $state_address . ' ';
                        }
                        if ($listing->zip != NULL) {
                            $zip_address = $listing->zip;
                            $address_string .= $zip_address;
                        }
                        $address_string = str_replace(' ', '+', $address_string);
                        $featured = '';
                        $list .= '<div class="listing-item featured">
                <div class="listing-item-wrap">
                    <a href="?listingID=' . $listingid . '">
                        <div class="listing-image">';
                            $list .= '<img src="' . $image . '" alt="' . $alt . '">
                        </div>
                    </a>
                  <div class="listing-content-area">
                  <div class="listing-title">
                  <a href="?listingID=' . $listingid . '">
                    <h3>' . $company . '</h3>
                  </a>
                  </div>
                  <div class="listing-content">
                  Region: ' . $region . ' <br />
                  Phone: ' . $phonelink . ' <br />
                  ' . $weburl . '
                  <a href="https://www.google.com/maps/dir/Current+Location/' . $address_string . '" target="_blank" class="directions-link">Get Directions</a>
                  </div>
                  </div>
                </div>
                ' . $featured . '
                </div>';
                        $num++;
                    }
                }

                $output .= $list;
                $list = '';
            } else {
                $list = '';
            }
            $list = "<span class='featured'>RESULTS</span>";

            if (count($listings) == 0) {
                $list .= "<p>I'm sorry.  There were no matches for your criteria.</p>";
            }

        }

        $num = 1;
        $fcount = 0;

        foreach ($listings as $listing) {
            if (($view == '1') || $view == '2') {
                $listingid = $listing->listing_id;
                if ($listing->company != NULL) {
                    $company = $listing->company;
                } else {
                    $company = 'N/A';
                }

                if ($listing->web_url != NULL) {
                    $url = $listing->web_url;
                    $weburl = '<a href="' . $url . '" target="_blank" class="site-link">Visit Website</a><br />';
                } else {
                    $weburl = '';
                }
                if ($listing->region != NULL) {
                    $region = $listing->region;
                } else {
                    $region = 'N/A';
                }
                if ($listing->toll_free != NULL) {
                    $phone = $listing->toll_free;
                } else if ($listing->phone != NULL) {
                    $phone = $listing->phone;
                } else if ($listing->alt_phone != NULL) {
                    $phone = $listing->alt_phone;
                } else {
                    $phone = 'none';
                }
                if ($phone != 'none') {
                    $tel = str_replace(array(' ', '(', ')', '-', '.', '_'), '', $phone);
                    $phonelink = "<a href='tel:{$tel}'>{$phone}</a>";
                } else {
                    $phonelink = 'N/A';
                }
                $logo_file = $listing->logo_file;
                if (($logo_file != NULL) && (strpos($logo_file, 'eps') === FALSE)) {
                    $image = $listing->img_path;
                    $image = preg_replace("/^http:/i", "https:", $image);
                    $image .= $listing->logo_file;
                    $alt = $company . ' Featured Image';
                } else {
                    $image = get_stylesheet_directory_uri() . '/images/placeholder.jpg';
                    $alt = 'Two light gray placeholder image silhouettes on white background';
                }
                $address_string = '';
                if ($company != 'N/A') {
                    $address_string .= $company . ' ';
                }
                $street_address = '';
                if ($listing->addr1 != NULL) {
                    $street_address .= $listing->addr1 . ' ';
                }
                if ($listing->addr2 != NULL) {
                    $street_address .= $listing->addr2 . ' ';
                }
                if ($listing->addr3 != NULL) {
                    $street_address .= $listing->addr3 . ' ';
                }
                $address_string .= $street_address;
                if ($listing->city != NULL) {
                    $city_address = $listing->city;
                    $address_string .= $city_address . ' ';
                }
                if ($listing->state != NULL) {
                    $state_address = $listing->state;
                    $address_string .= $state_address . ' ';
                }
                if ($listing->zip != NULL) {
                    $zip_address = $listing->zip;
                    $address_string .= $zip_address;
                }
                $address_string = str_replace(' ', '+', $address_string);
                $featured = '';
                $list .= '<div class="listing-item"><div class="listing-item-wrap"><a href="?listingID=' . $listingid . '"><div class="listing-image">';

                if ($view == '2') {
                    $list .= '<div class="list-number">' . $num . '</div>';
                }
                $list .= '<img src="' . $image . '" alt="' . $alt . '">
					</div>
				  </a>
				  <div class="listing-content-area">
					<div class="listing-title">
					<a href="?listingID=' . $listingid . '">
					  <h3>' . $company . '</h3>
					</a>
					</div>
					<div class="listing-content">
					Region: ' . $region . ' <br />
					Phone: ' . $phonelink . ' <br />
					' . $weburl . '
					<a href="https://www.google.com/maps/dir/Current+Location/' . $address_string . '" target="_blank" class="directions-link">Get Directions</a>
					</div>
				  </div>
				</div>
				' . $featured . '
				</div>';
                $num++;
                $fcount++;
            }
        }
        $output .= $list . '<div class="pagination cleaned"><div class="pagination-wrap"></div></div>';
        $output .= '</div></div>';
        if ($list == '') $output = '<div class="not-found">Uh-oh. Looks like your search came up empty.  Try again, or head back to the <a href="[.URL.]">home page</a>.</div>';
    } // end of view 1 & 2

    /* ------------------------------------------------- View 3 aka Details ------------------------------------------------- */
    if ($view == '3') {

        $listing = $listings[0];
        $listingid = $listing->listing_id;
        $images = $wpdb->get_results("SELECT i.*
			FROM sv_listings l
			INNER JOIN sv_images i ON i.listing_id = l.listing_id AND i.is_deleted = '0'
			WHERE l.is_deleted = '0' AND l.listing_id = '" . $listingid . "'
			ORDER BY i.sortorder ASC;");
        // unserialize function will turn multi_values into a php array
        $amenities = $wpdb->get_results("SELECT a.*, x.multi_values
			FROM sv_listings l
			INNER JOIN sv_listings_amenities_XREF x ON x.listing_id = l.listing_id AND x.is_deleted = '0'
			INNER JOIN sv_amenities a ON a.amenity_id = x.amenity_id AND a.is_deleted = '0'
			WHERE l.is_deleted = '0' AND l.listing_id = '" . $listingid . "';");
        $coupons = $wpdb->get_results("SELECT c.*, cc.couponcatname
			FROM sv_listings l
			INNER JOIN sv_coupons c ON c.listingid = l.listing_id AND c.is_deleted = '0'
			LEFT JOIN sv_coupon_categories cc ON cc.couponid = c.couponid AND cc.is_deleted = '0'
            WHERE l.is_deleted = '0' AND l.listing_id = '" . $listingid . "';");
        $menus = $wpdb->get_results("SELECT l.web_url, ai.value
            FROM sv_listings l
            INNER JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0'
            WHERE l.is_deleted = '0' AND ai.name = 'Menu Upload' AND ai.value != '' AND l.listing_id = '" . $listingid . "';");
        $socials = $wpdb->get_results("SELECT s.*
			FROM sv_listings l
			INNER JOIN sv_socialmedia s ON s.listing_id = l.listing_id AND s.is_deleted = '0'
			WHERE l.is_deleted = '0' AND l.listing_id = '" . $listingid . "';");
        $company = (!empty($listing->company)) ? $listing->company : 'N/A';
        $description = (!empty($listing->description)) ? $listing->description : 'N/A';
        $weburl = (!empty($listing->web_url)) ? '<a href="' . $listing->web_url . '" target="_blank" class="website">Visit Website</a><br />' : '';
        $region = (!empty($listing->region)) ? $listing->region : 'N/A';
        if (!empty($listing->toll_free)) $phone = $listing->toll_free;
        elseif (!empty($listing->phone)) $phone = $listing->phone;
        elseif (!empty($listing->alt_phone)) $phone = $listing->alt_phone;
        else $phone = 'none';
        if ($phone != 'none') {
            $tel = str_replace(array(' ', '(', ')', '-', '.', '_'), '', $phone);
            $phonelink = "<a href='tel:{$tel}'>{$phone}</a>";
        } else {
            $phonelink = 'N/A';
        }
        if ($listing->img_path == 'https://res.cloudinary.com/simpleview/image/upload/' || $listing->img_path == 'http://res.cloudinary.com/simpleview/image/upload/') {
            $image = get_stylesheet_directory_uri() . '/images/placeholder.jpg';
            $alt = 'Two light gray placeholder image silhouettes on white background';
        } else {
            $image = $listing->img_path;
            $image = preg_replace("/^http:/i", "https:", $image);
            $alt = $company . ' Featured Image';
        }
        $address_string = '';
        if ($company != 'N/A') {
            $address_string .= $company . ' ';
        }
        $street_address = '';
        if ($listing->addr1 != NULL) {
            $street_address .= $listing->addr1 . ' ';
        }
        if ($listing->addr2 != NULL) {
            $street_address .= $listing->addr2 . ' ';
        }
        if ($listing->addr3 != NULL) {
            $street_address .= $listing->addr3 . ' ';
        }
        $address_string .= $street_address;
        if ($listing->city != NULL) {
            $city_address = $listing->city;
            $address_string .= $city_address . ' ';
        }
        if ($listing->state != NULL) {
            $state_address = $listing->state;
            $address_string .= $state_address . ' ';
        }
        if ($listing->zip != NULL) {
            $zip_address = $listing->zip;
            $address_string .= $zip_address;
        }
        $address_string = str_replace(' ', '+', $address_string);
        if ($listing->facilityinformation_sleepingrooms != NULL) {
            $guest_rooms = '<div class="guest-rooms info-spot">
        <div class="info-title">Number of Guest Rooms</div>
        <div class="info">' . $listing->facilityinformation_sleepingrooms . '</div>
      </div>';
        } else {
            $guest_rooms = '';
        }
        if ($listing->facilityinformation_suites != NULL) {
            $guest_suites = '<div class="guest-suites info-spot">
        <div class="info-title">Number of Suites</div>
        <div class="info">' . $listing->facilityinformation_suites . '</div>
      </div>';
        } else {
            $guest_suites = '';
        }
        if ($listing->facilityinformation_totalsqft != NULL) {
            $meeting_room_space = '<div class="meeting-space info-spot">
        <div class="info-title">Total Meeting Room Space</div>
        <div class="info">' . $listing->facilityinformation_totalsqft . '</div>
      </div>';
        } else {
            $meeting_room_space = '';
        }
        if ($listing->facilityinformation_classroom != NULL) {
            $meeting_room_number = '<div class="meeting-space-number info-spot">
        <div class="info-title">Number of Meeting Rooms</div>
        <div class="info">' . $listing->facilityinformation_classroom . '</div>
      </div>';
        } else {
            $meeting_room_number = '';
        }
        if (($guest_rooms != '') || ($guest_suites != '') || ($meeting_room_space != '') || ($meeting_room_number != '')) {
            $info_spot_wrap_open = '<div class="info-section">';
            $info_spot_wrap_close = '</div>';
        } else {
            $info_spot_wrap_open = '';
            $info_spot_wrap_close = '';
        }
        if ($listing->facilityinformation_description != NULL) {
            $facility_description = '<div class="facility-description info-section">
        <div class="info">' . $listing->facilityinformation_description . '</div>
      </div>';
        } else {
            $facility_description = '';
        }
        $imagesout = '<div class="flex-container"><div class="flexslider" id="slider"><ul class="slides">';

        foreach ($images as $image) {
            $file = ($image->mediafile != NULL) ? $image->mediafile : '';
            $path = ($image->imgpath != NULL) ? $image->imgpath : '';
            $alt = ($image->medianame != NULL) ? $image->medianame : '';
            $imagesrc = $path . $file;
            $imagesout .= '<li class="slide"><div class="slide-wrap"><img src="' . $imagesrc . '" alt="' . $alt . '"></div></li>';
        }
        $imagesout .= "</ul></div></div>";
        if (!empty($amenities)) {
            $list_of_amenities = '<div class="facility-amenities info-section">
            <div class="info">';
            $blacklist = ['Bus Route Information'];
            $boldlist = [
                'Delivery',
                'Type of Food',
                'Dining Cost',
                'Open For',
                'Kids Menu',
                'Motorcoach Parking',
                'Outdoor Seating',
                'Private Rooms Available',
                'Reservations Accepted',
                'Reservations Required'
            ];
            $list_of_general_amenities = '';
            $list_of_in_room_amenities = '';
            $list_of_on_site_amenities = '';
            $list_of_group_dining_amenities = '';
            $list_of_dining_amenities = '';
            $list_of_accessibility_amenities = '';
            $list_of_other_amenities = '';
            foreach ($amenities as $amenity) {
                if ($amenity->type_name == 'Multi-Select') {
                    $multi_values = unserialize($amenity->multi_values);
                    $multi_value = '<ul class="multi-value">';
                    $i = 0;
                    foreach ($multi_values as $key) {
                        if (is_array($key)) {
                            foreach ($key as $keyname => $value) {
                                $multi_value .= ($keyname != 'LISTID') ? '<li>' . $value . '</li>' : '';
                            }
                        } else {
                            $multi_value .= ($i == 1) ? '<li>' . $key . '</li>' : '';
                            $i++;
                        }
                    }
                    $multi_value .= '</ul>';
                } else {
                    $multi_value = '';
                }

                $name = $amenity->name;
                $formattedName = in_array($name, $boldlist) ? '<span class="amenity-sub-header">'.$name.'</span>' : $name;
                $groupName = $amenity->group_name;
                $nameWithValue = $formattedName . $multi_value;
                $amenityEntity = '<li class="amenity-title">' . $nameWithValue . '</li>';

                if (!in_array($name, $blacklist)) {
                    switch ($groupName) {
                        case 'General':
                            $list_of_general_amenities .= $amenityEntity;
                            break;
                        case 'In-Room':
                            $list_of_in_room_amenities .= $amenityEntity;
                            break;
                        case 'On-Site':
                            $list_of_on_site_amenities .= $amenityEntity;
                            break;
                        case 'Group Dining':
                            $list_of_group_dining_amenities .= $amenityEntity;
                            break;
                        case 'Dining':
                            $list_of_dining_amenities .= $amenityEntity;
                            break;
                        case 'Accessibility':
                            $list_of_accessibility_amenities .= $amenityEntity;
                            break;
                        case 'Pricing':
                            // Pricing intentionally left without a destination
                            break;
                        default:
                            $list_of_other_amenities .= $amenityEntity;
                    }
                }
            }

            $list_of_amenities .= composeAmenitySection($list_of_general_amenities, 'General');
            $list_of_amenities .= composeAmenitySection($list_of_in_room_amenities, 'In-Room');
            $list_of_amenities .= composeAmenitySection($list_of_on_site_amenities, 'On-Site');
            $list_of_amenities .= composeAmenitySection($list_of_group_dining_amenities, 'Group Dining');
            $list_of_amenities .= composeAmenitySection($list_of_dining_amenities, 'Dining');
            $list_of_amenities .= composeAmenitySection($list_of_accessibility_amenities, 'Accessibility');
            $list_of_amenities .= composeAmenitySection($list_of_other_amenities, 'Other');
            $list_of_amenities .= '</div></div>';
        } else {
            $list_of_amenities = '';
        }

        if (!empty($menus)) {
            $menu_url = $menus[0]->web_url . '/' . $menus[0]->value;

            $menu = '<div>';
            $menu .= '<a class="menu-link" target="_blank" href="'.$menu_url.'">'.$company.' Menu</a>';
            $menu .= '</div>';
        }

        if (!empty($socials)) {
            $social_facebook = '';
            $social_twitter = '';
            $social_pinterest = '';
            $social_instagram = '';
            $social_rss = '';
            foreach ($socials as $social) {
                if ($social->service == 'Facebook') {
                    $social_facebook = '<a href="' . $social->value . '" class="details-social-icon facebook"><i class="fa fa-facebook"></i></a>';
                } else if ($social->service == 'Twitter') {
                    $social_twitter = '<a href="' . $social->value . '" class="details-social-icon twitter"><i class="fa fa-twitter"></i></a>';
                } else if ($social->service == 'Pinterest') {
                    $social_pinterest = '<a href="' . $social->value . '" class="details-social-icon pinterest"><i class="fa fa-pinterest-p"></i></a>';
                } else if ($social->service == 'Instagram') {
                    $social_instagram = '<a href="' . $social->value . '" class="details-social-icon instagram"><i class="fa fa-instagram"></i></a>';
                } else if (($social->service == 'Rss') || ($social->service == 'RSS')) {
                    $social_rss = '<a href="' . $social->value . '" class="details-social-icon rss"><i class="fa fa-rss"></i></a>';
                }
            }
            $social_icons = $social_facebook . $social_twitter . $social_pinterest . $social_instagram . $social_rss;
        } else {
            $social_icons = '';
        }
        if (!empty($coupons)) {
            $deals_area = '<div class="deals-area">
            <div class="deals-title">
                <h4>Deals</h4>
            </div>
            <div class="deals">';
                foreach ($coupons as $deals) {
                    $offer_title = ($deals->offertitle != NULL) ? $deals->offertitle : '';
                    $offer_img = ($deals->thumbfile != NULL) ? $deals->imgpath . $deals->thumbfile : '';
                    $offer_img_full = ($deals->mediafile != NULL) ? $deals->imgpath . $deals->mediafile : '';
                    $offer_alt = ($deals->medianame != NULL) ? $deals->medianame : 'Offer Featured Image';
                    $offer_text = ($deals->offertext != NULL) ? $deals->offertext : '';
                    $offer_link = ($deals->offerlink != NULL) ? $deals->offerlink : '';

                    $deals_area .= '<div class="deal-item">';
                    $deals_area .= ($offer_title != '') ? '<div class="deal-title">' . $offer_title . '</div>' : '';
                    $deals_area .= ($offer_img_full != '') ? '<a href="' . $offer_img_full . '" target="_blank" class="deal-img-link">' : '';
                    $deals_area .= ($offer_img != '') ? '<img src="' . $offer_img . '" alt="' . $offer_alt . '" class="deal-img">' : '';
                    $deals_area .= ($offer_img_full != '') ? '</a>' : '';
                    $deals_area .= ($offer_link != '') ? '<a href="' . $offer_link . '" target="_blank" class="deal-link">' : '';
                    $deals_area .= ($offer_text != '') ? '<div class="deal-text">' . $offer_text . '</div>' : '';
                    $deals_area .= ($offer_link != '') ? '</a>' : '';
                    $deals_area .= '</div>';
                }
            $deals_area .= '</div></div>';
        } else {
            $deals_area = '';
        }

        // create maps
        $listings = $wpdb->get_results("
				(SELECT DISTINCT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`, r.name as region, '0' as `map_cat`, sc.sub_cat_id as subcat, sc.name as subname
				FROM sv_listings l
				INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
				INNER JOIN sv_subcategories sc ON sc.cat_id = l.cat_id AND sc.is_deleted = '0'
				INNER JOIN sv_listings_subcategories_XREF x ON x.sub_cat_id = sc.sub_cat_id AND x.listing_id = l.listing_id AND x.is_deleted = '0'
				INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.region_id = l.region_id AND r.is_deleted = '0'
				LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
				LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
				WHERE l.is_deleted = '0'
					AND l.listing_id = '" . $listingid . "')
			UNION
				(SELECT DISTINCT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`, r.name as region, c.cat_id as `map_cat`, sc.sub_cat_id as subcat, sc.name as subname
				FROM sv_listings l
				INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
				INNER JOIN sv_subcategories sc ON sc.cat_id = l.cat_id AND sc.is_deleted = '0'
				INNER JOIN sv_listings_subcategories_XREF x ON x.sub_cat_id = sc.sub_cat_id AND x.listing_id = l.listing_id AND x.is_deleted = '0'
				INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.region_id = l.region_id AND r.is_deleted = '0'
				LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
				LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
				WHERE l.is_deleted = '0'
					AND (c.cat_id IN('3','2','5','6','4') OR sc.sub_cat_id IN('75'))
					AND l.listing_id != '" . $listingid . "')
			ORDER BY map_cat ASC;
		");
        // Create JSON for Maps
        $currentMapCategory = '0';
        $mapsArray = array();
        $listingsJson = '';
        $selectedListingsJson = '';
        $mapPlaceHolders = '';
        $center_lat = '';
        $center_long = '';

        foreach ($listings as $listing) {
            $cat = ($listing->subcat != '75') ? $listing->map_cat : $listing->subcat;
            $linkAddress = str_replace(" ", "+", $listing->addr1);
            $linkAddress .= "+" . str_replace(" ", "+", str_replace("'", "", $listing->city));
            $linkAddress .= "+" . $listing->state;
            $link = "https://www.google.com/maps/dir//{$linkAddress}/@" . $listing->latitude . "," . $listing->longitude . ",17z";
            $link_to_directions = '<a href="' . $link . '" style=\"margin-top: 3px; padding: 3px; margin-left: 0px; font-size: 10px;\" class=\"listingButton\" target=\"_blank\">Get Directions</a>';
            $mpContent = "<div style=\"font-size: 14px; height: 125px;\"><b>{$listing->company}</b>";
            $mpContent .= "<p>{$listing->addr1}<br />";
            $mpContent .= ($listing->addr2 == "") ? "" : "{$listing->addr2}<br />";
            $mpContent .= "{$listing->city}, {$listing->state} {$listing->zip}<br />";
            if (!empty($listing->toll_free)) $mpContent .= "{$listing->toll_free}</p>";
            else $mpContent .= "{$listing->phone}</p>";
            $mpContent .= "<p><a href=\"//{$listing->web_url}\" style=\"margin-top: 3px; padding: 3px; margin-left: 0px; font-size: 10px;\" class=\"listingButton\" target=\"_blank\">View Site</a>" . $link_to_directions . "</p></div>";
            $mpContent = str_replace("\n", '', str_replace("'", '&#39;', $mpContent));
            if ($cat == '0') {
                $selectedListingsJson = "['" . str_replace("\n", '', str_replace("'", '&#39;', $listing->region)) . "'," . $listing->latitude . ", " . $listing->longitude . ", '" . $cat . "', '" . $mpContent . "'],";
                $mapPlaceHolders .= '<div style="float:left; width: 733px; height: 408px; position: absolute;" class="maps" id="map' . $currentMapCategory . '"></div>';
                $center_lat = $listing->latitude;
                $center_long = $listing->longitude;
            }
            if ($cat != $currentMapCategory) {
                $mapsArray[$currentMapCategory] = $listingsJson;
                $mapPlaceHolders .= '<div style="float:left; width: 733px; height: 408px; position: absolute;" class="maps" id="map' . $currentMapCategory . '"></div>';
                $currentMapCategory = $cat;
                $listingsJson = $selectedListingsJson;
            }
            $listingsJson .= "['" . str_replace("\n", '', str_replace("'", '&#39;', $listing->region)) . "'," . $listing->latitude . ", " . $listing->longitude . ", '" . $cat . "', '" . $mpContent . "'],";
        }
        if ($listingsJson != $selectedListingsJson) {
            $mapsArray[$currentMapCategory] = $listingsJson;
            $mapPlaceHolders .= '<div style="float:left; width: 733px; height: 408px; position: absolute;" class="maps" id="map' . $currentMapCategory . '"></div>';
        }

        updateHits($listingid);
        ob_start();
        ?>

        <script type="text/javascript">
            function initialize() {
                var marker, i, icoImg;
                var listingCount = 1;
                <?php
                        foreach($mapsArray as $i=>$locationsJson) {
                ?>
                var locations<?=$i?> = [<?=$locationsJson?>];
                var infowindow<?=$i?> = new google.maps.InfoWindow();
                var lat = '<?=$center_lat?>';
                var lng = '<?=$center_long?>';
                var map<?=$i?> = new google.maps.Map(document.getElementById('map<?=$i?>'), {
                    zoom: 14,
                    center: new google.maps.LatLng(lat, lng),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                var pinName = 'star';
                for (i = 0; i < locations<?=$i?>.length; i++) {
                    icoImg = {
                        url: 'https://unmistakablylawrence.com/wp-content/themes/explore-lawrence/images/map-pins/' + pinName + '.png',
                        size: new google.maps.Size(50, 50),
                        anchor: new google.maps.Point(25, 25),
                        scaledSize: new google.maps.Size(36, 36),
                    }
                    pinName = 'blank';
                    position = new google.maps.LatLng(locations<?=$i?>[i][1], locations<?=$i?>[i][2]);
                    markerOptions = {
                        position: position,
                        map: map<?=$i?>,
                        icon: icoImg
                    }
                    marker = new google.maps.Marker(markerOptions);
                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            infowindow<?=$i?>.setContent(locations<?=$i?>[i][4]);
                            infowindow<?=$i?>.open(map<?=$i?>, marker);
                        }
                    })(marker, i));
                }
                var classname = document.getElementsByClassName("classname");
                <?php
                        }
                ?>
            }
            google.maps.event.addDomListener(window, "load", initialize);
        </script>

        <?php
        $temp = ob_get_contents();
        ob_end_clean();
        $output .= $temp;

        $output .= '<div class="details-view">
		  <div class="details-view-wrap">
			<div class="biz-title">
			<h3>' . $company . '</h3>
			</div>
			<div class="details-left">
			' . $imagesout . '
			<div class="content-area">
			  <div class="content-area-wrap">
          <div class="facility-description info-section">
            <div class="info-title">' . $company . '</div>
            <div class="info">' . $description . '</div>
          </div>
          ' . $info_spot_wrap_open . $guest_rooms . $guest_suites . $meeting_room_space . $meeting_room_number . $info_spot_wrap_close . '
          ' . $facility_description . '
          ' . $list_of_amenities . '
			  </div>
			</div>
			</div>
			<div class="details-right">
			<div class="info">
              <div class="info-title"><h4>Details</h4></div>
			  <div class="region">Region: ' . $region . '</div>
			  <div class="address">Address:<br />' . $street_address . '<br />' . $city_address . ' ' . $state_address . ' ' . $zip_address . '</div>
			  <a href="https://www.google.com/maps/dir/Current+Location/' . $address_string . '" target="_blank" class="directions">Get Directions</a>
              ' . $menu . '
              <div class="phone-number">Phone: ' . $phonelink . '</div>
			  ' . $weburl . '
			  <div class="social-media">
				' . $social_icons . '
              </div>
        ' . $deals_area . '
        ' . get_upcoming_events() . '
			</div>
			</div>
		  </div>
		  <div class="map-area" style="min-height: 450px;">
			<div class="map-area-wrap">
				 <div class="map">' . $mapPlaceHolders . '</div>
			<div class="near-links">
			  <div class="near-title">
				<h4>What\'s Nearby</h4>
				<div class="links">
				<span data-cat="3" class="nearlink" id="link1">Places to Eat</span>
				<span data-cat="2" class="nearlink" id="link2">Attractions</span>
				<span data-cat="5" class="nearlink" id="link3">Entertainment</span>
				<span data-cat="6" class="nearlink" id="link5">Shopping</span>
				<span data-cat="75" class="nearlink" id="link6">Sports & Recreation</span>
				</div>
			  </div>
			</div>
			</div>
		  </div>
		</div>';
        if ($output == '') $output = '<div class="not-found">Uh-oh. Looks like your search came up empty.  Try again, or head back to the <a href="[.URL.]">home page</a>.</div>';
    } // end of view 3

    return $output;
}

add_action('genesis_after', 'map_height_biz');
function map_height_biz()
{ ?>
    <style>

    </style>
    <script>
        (function ($) {
            function sectionHeights() {
                var mapwidth = $('.maps').innerWidth();
                $('.listing-right .maps').css('height', mapwidth);
                $('.listing-right').css('min-height', mapwidth);
                var mapheight = $('.maps').outerHeight();

                $('.details-view .map-area').css('min-height', mapheight + 4);
                mapwidth = $('.map').innerWidth();

                $('.details-view .map').css('height', mapheight).children().css('width', mapwidth);
            }

            $(document).ready(function () {
                sectionHeights();
                //injectCrowdRiff();
                if ($(window).width() < 480) {
                }
            }); //end doc ready

            $(window).load(function () {
                sectionHeights();
                if ($(window).width() < 480) {
                }
            }); //end window load

            $(window).on('resize', function () {
                sectionHeights();
                if ($(window).width() < 480) {
                }
            }); //end window resize

        })(jQuery);
    </script>
    <?php
}

// LEAVE THIS AT THE END
add_filter('genesis_pre_get_option_site_layout', '__genesis_return_full_width_content'); // Genesis Force Full Width Page
genesis();