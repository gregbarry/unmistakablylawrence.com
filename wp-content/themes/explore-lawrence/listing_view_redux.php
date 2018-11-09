<?php
 /* Template Name: Listing View - Part Two*/

	function mapHeightBiz() { ?>
		<script>
			(function($){
				function sectionHeights() {
					var mapwidth = $('.maps').innerWidth(),
						mapheight = $('.maps').outerHeight();

					$('.listing-right .maps').css('height', mapwidth);
					$('.listing-right').css('min-height', mapwidth);

	        		$('.details-view .map-area').css('min-height', mapheight + 4);

	        		mapwidth = $('.map').innerWidth();

	        		$('.details-view .map').css('height', mapheight).children().css('width', mapwidth);
				}

				$(document).ready(function(){
					sectionHeights();
					if ($(window).width() < 480){}
				}); //end doc ready

				$(window).load(function(){
					sectionHeights();
					if ($(window).width() < 480){}
				}); //end window load

				$(window).on('resize', function(){
					sectionHeights();
					if ($(window).width() < 480){}
				}); //end window resize

			})(jQuery);
		</script>
	<?php
	}

	function addIncludes() {
		// .less file
		wp_enqueue_style( 'less-style-listing', get_stylesheet_directory_uri() . '/views-styles/listing-view.less' );
		wp_enqueue_style( 'less-style-details', get_stylesheet_directory_uri() . '/views-styles/details-view.less' );

	    //slider
	    wp_enqueue_style( 'flex-style-p', get_stylesheet_directory_uri() . '/lib/ml/public.css' );
	    wp_enqueue_style( 'flex-style-s', get_stylesheet_directory_uri() . '/views-styles/flex.less' );
	    wp_enqueue_style( 'flex-style-flex', get_stylesheet_directory_uri() . '/lib/ml/flexslider.css' );
	    wp_enqueue_script('flex-js', get_stylesheet_directory_uri() . '/lib/ml/jquery.flexslider-min.js', array('jquery'));

		// pagination
		wp_enqueue_style(  'pagenationcss', get_stylesheet_directory_uri(). '/lib/css/simplePagination.css' );
		wp_enqueue_script( 'pagenationjs', get_stylesheet_directory_uri(). '/lib/js/jquery.simplePagination.js', array('jquery'), null, true );

		if (empty($_REQUEST['listingID']) && (!empty($_REQUEST['r']) || (!empty($_REQUEST['sc'])) || get_field('select_a_category') == 'map' )) wp_enqueue_script( 'pagenationinit', get_stylesheet_directory_uri(). '/lib/js/paginate.js', array( 'jquery', 'pagenationjs' ), null, true );
		if (!empty($_REQUEST['listingID'])) wp_enqueue_script( 'maptogglejs', get_stylesheet_directory_uri(). '/lib/js/maptoggle.js', array('jquery'), null, false );

		wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAQAK1IB5dghlOj8vdrcq0uRGbw1Gz32uA&sensor=false', array(), null, false );
		wp_enqueue_script( 'categoryselect', get_stylesheet_directory_uri(). '/lib/js/categoryselect.js', array('jquery'), null, false );
	}

	function addSearchBar() {
		global $wpdb;

		$r  = $_REQUEST['r'];
		$c  = $_REQUEST['c'];
		$sc = $_REQUEST['sc'];

		$getcat = get_field('select_a_category');

		$searchText = get_field( "search_text" );
		$submitURL  = $_SERVER["REQUEST_URI"];
		$hideLocBox; $hideIntBox;

		if($getcat == 'map') {
			$cats = $wpdb->get_results('SELECT * FROM `sv_categories` WHERE is_deleted = 0 ORDER BY name');

			$selected_all_0 = ($c && $c == 'ALL') ? 'selected' : '';
			$catResults = '<option value="ALL" '.$selected_all_0.'>All</option>';

			foreach($cats as $cat){
				if (in_array($cat->cat_id, array('2', '9', '19', '20'))) {
					$selected = (!empty($c) && $cat->cat_id == $c) ? 'selected' : '';
					$catResults .= '<option value="'.$cat->cat_id.'" '.$selected.'>'.$cat->name.'</option>';
				}
			}
			$selectedCategory = ((empty($c) || $c == 'ALL') ?  'NULL': $c);
		} else {
			$hideIntBox = 'hide';
			$selectedCategory = $getcat || 'NULL';
		}

		$cid = (!empty($c)) ? $c : $getcat;
		$subcats = $wpdb->get_results("SELECT * FROM `sv_subcategories` WHERE is_deleted = 0 AND cat_id = ifnull(".$cid.",cat_id) ORDER BY name");

		// Collect all of the regions for the region dropdown
		$regions = $wpdb->get_results('SELECT DISTINCT region_id,name FROM `sv_regions` WHERE is_deleted = 0 ORDER BY name');
		$selected_all_2 = ($r && $r == 'ALL') ? 'selected' : '';
		$regionResults = '<option value="ALL" '.$selected_all_2.'>All</option>';

		foreach($regions as $region){
			$selected = (!empty($r) && $region->region_id == $r) ? 'selected' : '';

			// Remove "OTHER" region from view
			if ($region->region_id != '6') {
	      	    $regionResults .= '<option value="'.$region->region_id.'" '.$selected.'>'.$region->name.'</option>';
	        }
		}

		if($getcat != '4') {
			$selected_all_1 = ($sc && $sc == 'ALL') ? 'selected' : '';
		    $subcatResults = '<option value="ALL" '.$selected_all_1.'>All</option>';

			if ($sc == '88') {
			  	$subcatResults .= '<option value="88" '.$selected.'>JO 2017</option>';
			}

			foreach($subcats as $subcat){
		  		$selected = (!empty($sc) && $subcat->sub_cat_id == $sc) ? 'selected' : '';

	  		    if ($subcat->sub_cat_id != '88') {
			      	$subcatResults .= '<option value="'.$subcat->sub_cat_id.'" '.$selected.'>'.$subcat->name.'</option>';
			    }
			}
		} else {
			$hideLocBox = 'hide';
		}
	?>
		<div class="header-search-area">
			<div class="header-search-area-wrap">
				<div class="listing-search-title">
					Search <?=$searchText?>
				</div>
				<div class="listing-search-form">
					<form action="<?=$submitURL?>" method="get">
						<!-- Category Select Dropdown-->
						<select name="c" class="category_select <?=$hideIntBox?>">
							<option value="">Interest</option>
							<?=$catResults?>
						</select>
						<select name="sc" class="sub-categories <?=$hideLocBox?>">
							<option value="">Type</option>
							<?=$subcatResults?>
						</select>
						<select name="r">
							<option value="">Location</option>
							<?=$regionResults?>
						</select>
						<input type="submit" value="Search">
					</form>
				</div>
			</div>
		</div>
	<?
	}

	function addContent() {
		global $wpdb;

		$r  = $_REQUEST['r'];
		$c  = $_REQUEST['c'];
		$sc = $_REQUEST['sc'];
		$listing_id = $_REQUEST['listingID'];
		$getcat = get_field('select_a_category');
		$selectedCategory = $getcat;
		$listingright = '';

		/* ------------------------------------------------- Call SQL ------------------------------------------------- */

		$reg_id     = ((empty($r))  || $r  == 'ALL' ) ? "NULL" : "'".$r."'";
		$sub_cat_id = ((empty($sc)) || $sc == 'ALL' ) ? "NULL" : "'".$sc."'";
		$featured   = "NULL";

		// set view for content
		if (!empty($listing_id)) {
			$view = '3'; // show details view
		} else if ((!empty($r) || !empty($sc)) || in_array($getcat, array('map', '4'))) {
	    	$view = '2'; // show search results view
		    if (get_field('subcategory') != "") {
		    	$sub_cat_id = get_field('subcategory');
		    }
		    $listing_id = "NULL";
		    $featured = "NULL";
	  	} else {
			$view = '1'; // show landing view
			$featured = "'Yes'";
			$listing_id = "NULL";
		}

		if ($sc != '88') {
			$noOther = "AND r.region_id != 6";
		}

		$sql = "
			SELECT DISTINCT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`, r.name as `region`
			FROM sv_listings l
			INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
			INNER JOIN sv_subcategories sc ON sc.cat_id = l.cat_id AND sc.is_deleted = '0'
			INNER JOIN sv_listings_subcategories_XREF x ON x.sub_cat_id = sc.sub_cat_id AND x.listing_id = l.listing_id AND x.is_deleted = '0'
			INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.region_id = l.region_id AND r.is_deleted = '0'
			LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
			LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
			WHERE l.is_deleted = '0'
				AND c.cat_id = IFNULL(".$selectedCategory.",c.cat_id)
				AND sc.sub_cat_id = IFNULL(".$sub_cat_id.",sc.sub_cat_id)
				AND r.region_id = IFNULL(".$reg_id.",r.region_id)
				".$noOther."
				AND l.listing_id = IFNULL(".$listing_id.",l.listing_id)
				AND ai.value = IFNULL(".$featured.",ai.value)
			ORDER BY featured DESC, l.sort_company ASC;";

		$listings = $wpdb->get_results($sql);

		// VIEW 1

		$perPage = get_option('biz_per_page');
		$perPage = $perPage + 1;

		// Landing Page
		if($view == '1') {
			$listinglandingheadline  = get_field( 'landing_page_title' );
			$listinglandingcontent   = get_field( 'landing_page_content' );
			$listinglandingpageimage = get_field( 'landing_page_image');

			$listingright = ($listinglandingpageimage) ? '<img src="'.$listinglandingpageimage["url"].'" alt="'.$listinglandingpageimage["alt"].'" />' : '';

			foreach($listings as $key=>$listing){
				if ($key <= 2 && $listing->Featured == "Yes") {
					$listingid = $listing->listing_id;

					$company = ($listing->company) ? $listing->company : 'N/A';
					$weburl  = ($listing->web_url) ? '<a href="'.$listing->web_url.'" target="_blank" class="site-link">Visit Website</a><br />' : '';
					$region  = ($listing->region)  ? $listing->region : 'N/A/';

					if ($listing->toll_free) {
						$phone = $listing->toll_free;
					} else if ($listing->phone) {
						$phone = $listing->phone;
					} else if ($listing->alt_phone) {
						$phone = $listing->alt_phone;
					} else {
						$phone = 'none';
					}

					if ( $phone != 'none'){
						$tel = str_replace(array(' ','(',')','-','.','_'),'',$phone);
						$phonelink = "<a href='tel:{$tel}'>{$phone}</a>";
					} else {
						$phonelink = 'N/A';
					}

					$logo_file = $listing->logo_file;

					if ( ($logo_file != NULL) && (strpos($logo_file, 'eps') === FALSE) ){
						$image = $listing->img_path;
						$image = preg_replace("/^http:/i", "https:", $image);
						$image .= $listing->logo_file;
						$alt = $company . ' Featured Image';
					} else{
						$image = get_stylesheet_directory_uri() . '/images/placeholder.jpg';
						$alt = 'Two light gray placeholder image silhouettes on white background';
					}

					$address_string = '';
					$street_address = '';
					$city_address   = '';

					$address_string .= ($company != 'N/A') ? $company : '';

					$street_address .= ($listing->addr1)   ? $listing->addr1 : '';
					$street_address .= ($listing->addr2)   ? $listing->addr2 : '';
					$street_address .= ($listing->addr3)   ? $listing->addr3 : '';

					$address_string .= $street_address;

					if ( $listing->city != NULL){
						$city_address    = $listing->city;
						$address_string .= $city_address .' ';
					}
					if ( $listing->state != NULL){
						$state_address   = $listing->state;
						$address_string .= $state_address .' ';
					}
					if ( $listing->zip != NULL ){
						$zip_address     = $listing->zip;
						$address_string .= $zip_address;
					}

					$address_string = str_replace(' ', '+', $address_string);

					$featured = '';

					$list.='<div class="listing-item featured">
								<div class="listing-item-wrap">
									<a href="?listingID='.$listingid.'">
										<div class="listing-image">
											<img src="'.$image.'" alt="'.$alt.'">
										</div>
									</a>
									<div class="listing-content-area">
										<div class="listing-title">
											<a href="?listingID='.$listingid.'">
												<h3>'.$company.'</h3>
											</a>
										</div>
										<div class="listing-content">
											Region: '.$region.' <br />
											Phone: '.$phonelink.' <br />
											'.$weburl.'
											<a href="https://www.google.com/maps/dir/Current+Location/'.$address_string.'" target="_blank" class="directions-link">Get Directions</a>
										</div>
									</div>
								</div>
								'.$featured.'
							</div>';
				}
			}

			$output .= $list;
			$list = '';

			?>
				<div id="scroll-to" class="listings-area">
					<div class="listings-wrap">
						<div class="landing-heading-area">
							<div class="landing-heading-wrap">
								<div class="landing-headline">
									<h2><?=$listinglandingheadline?></h2>
								</div>
								<div class="landing-content-area">
									<?=$listinglandingcontent?>
								</div>
							</div>
						</div>
						<div class="listing-right">
							<?=$listingright?>
						</div>
						<div class="listing-left">
							<span class="featured">FEATURED</span>
							<?=$output?>
						</div>
					</div>
				</div>
			<?
		}

		// Search Results
		if($view == '2') {

		    $num = 1;

		    foreach($listings as $listing){
					$listingid = $listing->listing_id;

					$company = ($listing->company) ? $listing->company : 'N/A';
					$weburl  = ($listing->web_url) ? '<a href="'.$listing->web_url.'" target="_blank" class="site-link">Visit Website</a><br />' : '';
					$region  = ($listing->region)  ? $listing->region : 'N/A/';

					if ($listing->toll_free) {
						$phone = $listing->toll_free;
					} else if ($listing->phone) {
						$phone = $listing->phone;
					} else if ($listing->alt_phone) {
						$phone = $listing->alt_phone;
					} else {
						$phone = 'none';
					}

					if ( $phone != 'none'){
						$tel = str_replace(array(' ','(',')','-','.','_'),'',$phone);
						$phonelink = "<a href='tel:{$tel}'>{$phone}</a>";
					} else {
						$phonelink = 'N/A';
					}

					$logo_file = $listing->logo_file;

					if ( ($logo_file != NULL) && (strpos($logo_file, 'eps') === FALSE) ){
						$image = $listing->img_path;
						$image = preg_replace("/^http:/i", "https:", $image);
						$image .= $listing->logo_file;
						$alt = $company . ' Featured Image';
					} else{
						$image = get_stylesheet_directory_uri() . '/images/placeholder.jpg';
						$alt = 'Two light gray placeholder image silhouettes on white background';
					}

					$address_string = '';
					$street_address = '';
					$city_address   = '';

					$address_string .= ($company != 'N/A') ? $company : '';

					$street_address .= ($listing->addr1)   ? $listing->addr1 : '';
					$street_address .= ($listing->addr2)   ? $listing->addr2 : '';
					$street_address .= ($listing->addr3)   ? $listing->addr3 : '';

					$address_string .= $street_address;

					if ( $listing->city != NULL){
						$city_address    = $listing->city;
						$address_string .= $city_address .' ';
					}
					if ( $listing->state != NULL){
						$state_address   = $listing->state;
						$address_string .= $state_address .' ';
					}
					if ( $listing->zip != NULL ){
						$zip_address     = $listing->zip;
						$address_string .= $zip_address;
					}

					$address_string = str_replace(' ', '+', $address_string);
		            $featured = '';
		            $list.='<div class="listing-item featured">
		                		<div class="listing-item-wrap">
		                  			<a href="?listingID='.$listingid.'">
		                  				<div class="listing-image">
		            						<img src="'.$image.'" alt="'.$alt.'">
		                  				</div>
		                  			</a>
		                  			<div class="listing-content-area">
		                  				<div class="listing-title">
		                  					<a href="?listingID='.$listingid.'">
		                    					<h3>'.$company.'</h3>
		                  					</a>
		                  				</div>
		                  				<div class="listing-content">
		                  					Region: '.$region.' <br />
		                  					Phone: '.$phonelink.' <br />
		                  					'.$weburl.'
		                  					<a href="https://www.google.com/maps/dir/Current+Location/'.$address_string.'" target="_blank" class="directions-link">Get Directions</a>
		                  				</div>
		                  			</div>
		                		</div>
		                		'.$featured.'
		                	</div>';
		          }

			$mapIndex = 1;
			$i = 1;
			$mapsArray = array();

			foreach($listings as $listing){
				$linkAddress  = str_replace(" ","+",$listing->addr1);
				$linkAddress .= "+".str_replace(" ","+",str_replace("'","",$listing->city));
				$linkAddress .= "+".$listing->state;
				$link = "https://www.google.com/maps/dir//{$linkAddress}/@".$listing->latitude.",".$listing->longitude.",17z";
				$link_to_directions ='<a href="'.$link.'" class="listingButton linkDirections" target="_blank">Get Directions</a>';
				$mpContent  = "<div style='font-size: 14px; height: 125px;'><b>{$listing->company}</b>";
				$mpContent .= "<p>{$listing->addr1}<br />";
				$mpContent .= ($listing->addr2 == "")? "" : "{$listing->addr2}<br />";
				$mpContent .= "{$listing->city}, {$listing->state} {$listing->zip}<br />";

				if (!empty($listing->toll_free)) {
					$mpContent .= "{$listing->toll_free}</p>";
				} else {
					$mpContent .= "{$listing->phone}</p>";
				}

				$mpContent .= "<p><a href=\"{$listing->web_url}\" class=\"listingButton listingButtonAdd\" target=\"_blank\">View Website</a>".$link_to_directions."</p></div>";
				$mpContent  =  str_replace("\n",'',str_replace("'",'&#39;',$mpContent));
				$listingsJson .= "['".str_replace("\n",'',str_replace("'",'&#39;',$listing->region))."',".$listing->latitude.", ".$listing->longitude.", ".$i.", '".$mpContent."'],";
				$i++;

				if ($i == $perPage) {
					$listingright.= '<div class="maps mapsAdd" id="map'.$mapIndex.'"></div>';
					$mapsArray[] = $listingsJson;
					$listingsJson = '';
					$i = 1;
					$mapIndex++;
				}
			}

			if ($i != $perPage) {
	      		if (!empty($listingsJson)){
	        		$mapsArray[] = $listingsJson;
	        		$listingright.= '<div class="maps mapsAdd" id="map'.$mapIndex.'"></div>';
	      		}
			}
			$i = 1;

			?>
				<div id="scroll-to" class="listings-area">
					<div class="listings-wrap">
						<div class="listing-right">
							<?=$listingright?>
						</div>
						<div class="listing-left">
							<?=$list?>
						</div>
					</div>
				</div>

				<script type="text/javascript">
					var locations = [], infowindow = [], map = [], alreadyInit = [],
						perPage   = jQuery('.page-var').data('per-page');

				    function initialize() {
						if (alreadyInit[0]){
							return;
						}
						alreadyInit[0] = true;

						<?php
						foreach($mapsArray as $locationsJson) {
						    if (!empty($locationsJson)){
						?>
						        locations[<?=$i?>]  = [<?=$locationsJson?>];
								infowindow[<?=$i?>] = new google.maps.InfoWindow();
								map[<?=$i?>]        = new google.maps.Map(document.getElementById('map<?=$i?>'), {
									zoom: 14,
									mapTypeId: google.maps.MapTypeId.ROADMAP
								});
						<?php
						    }

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
					            google.maps.event.addListener(marker, 'click', (function(marker, i) {
					                return function() {
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

			<?

		}

		// Detail View
		if($view == '3') {
			$listing = $listings[0];
			$listingid = $listing->listing_id;
			$address_string; $street_address;
			$social_string;

			$company     = (!empty($listing->company)) ? $listing->company : 'N/A';
			$description = (!empty($listing->description)) ? $listing->description : 'N/A';
			$weburl      = (!empty($listing->web_url)) ? '<a href="'.$listing->web_url .'" target="_blank" class="website">Visit Website</a><br />' : '';
			$region      = (!empty($listing->region)) ? $listing->region : 'N/A';

			if ($listing->toll_free) {
				$phone = $listing->toll_free;
			} else if ($listing->phone) {
				$phone = $listing->phone;
			} else if ($listing->alt_phone) {
				$phone = $listing->alt_phone;
			} else {
				$phone = 'none';
			}

			if ($phone != 'none') {
				$tel = str_replace(array(' ','(',')','-','.','_'),'',$phone);
				$phonelink = "<a href='tel:{$tel}'>{$phone}</a>";
			} else {
				$phonelink = 'N/A';
			}

			if ($company != 'N/A'){
				$address_string .= $company .' ';
			}

			// Form Street Address
			if ( $listing->addr1 != NULL){
				$street_address .= $listing->addr1 .' ';
			}
			if ( $listing->addr2 != NULL){
				$street_address .= $listing->addr2 .' ';
			}
			if ( $listing->addr3 != NULL){
				$street_address .= $listing->addr3 .' ';
			}
			$address_string .= $street_address;
			if ( $listing->city != NULL){
				$city_address = $listing->city;
				$address_string .= $city_address .' ';
			}
			if ( $listing->state != NULL){
				$state_address = $listing->state;
				$address_string .= $state_address .' ';
			}
			if ( $listing->zip != NULL){
				$zip_address = $listing->zip;
				$address_string .= $zip_address;
			}
			$address_string = str_replace(' ', '+', $address_string);

			$images = $wpdb->get_results("SELECT i.*
				FROM sv_listings l
				INNER JOIN sv_images i ON i.listing_id = l.listing_id AND i.is_deleted = '0'
				WHERE l.is_deleted = '0' AND l.listing_id = '".$listingid."'
				ORDER BY i.sortorder ASC;");

		    foreach($images as $image) {
			    $file = ($image->mediafile != NULL) ? $image->mediafile : '';
			    $path = ($image->imgpath != NULL) ? $image->imgpath : '';
			    $alt  = ($image->medianame != NULL) ? $image->medianame : '';
			    $imagesrc = $path . $file;
			    $imagesout .= '<li class="slide"><div class="slide-wrap"><img src="'.$imagesrc.'" alt="'. $alt .'"></div></li>';
		    }

			$amenities = $wpdb->get_results("SELECT a.*, x.multi_values
				FROM sv_listings l
				INNER JOIN sv_listings_amenities_XREF x ON x.listing_id = l.listing_id AND x.is_deleted = '0'
				INNER JOIN sv_amenities a ON a.amenity_id = x.amenity_id AND a.is_deleted = '0'
				WHERE l.is_deleted = '0' AND l.listing_id = '".$listingid."';");

			$socials = $wpdb->get_results("SELECT s.*
				FROM sv_listings l
				INNER JOIN sv_socialmedia s ON s.listing_id = l.listing_id AND s.is_deleted = '0'
				WHERE l.is_deleted = '0' AND l.listing_id = '".$listingid."';");

			$coupons = $wpdb->get_results("SELECT c.*, cc.couponcatname
				FROM sv_listings l
				INNER JOIN sv_coupons c ON c.listingid = l.listing_id AND c.is_deleted = '0'
				LEFT JOIN sv_coupon_categories cc ON cc.couponid = c.couponid AND cc.is_deleted = '0'
				WHERE l.is_deleted = '0' AND l.listing_id = '".$listingid."';");

			if (( $listing->img_path == 'http://Lawrence.simpleviewcrm.com/images//listings/') || ( $listing["IMGPATH"] == 'http://Lawrence.simpleviewcrm.com/images/') || ( $listing->img_path == 'https://Lawrence.simpleviewcrm.com/images//listings/') || ( $listing["IMGPATH"] == 'https://Lawrence.simpleviewcrm.com/images/') || ($listing["IMGPATH"] == NULL)){
				$image = get_stylesheet_directory_uri() . '/images/placeholder.jpg';
				$alt = 'Two light gray placeholder image silhouettes on white background';
			} else{
				$image = $listing->img_path;
				$image = preg_replace("/^http:/i", "https:", $image);
				$alt = $company . ' Featured Image';
			}

		    if (!empty($socials)){
		        foreach($socials as $social){
		        	$socialLow = strtolower($social->service);
		      	    $social_string .= '<a href="'.$social->value.'" class="details-social-icon '.$socialLow.'"><i class="fa fa-'.$socialLow.'"></i></a>';
		        }
		    }

			?>

				<div class="details-view">
					<div class="details-view-wrap">
						<div class="biz-title">
							<h3>VIEW 3</h3>
							<h3><?=$company?></h3>
						</div>
						<div class="details-left">
						<div class="flex-container">
							<div class="flexslider" id="slider">
								<ul class="slides">
									<?=$imagesout?>
								</ul>
							</div>
						</div>
						<div class="content-area">
							<div class="content-area-wrap">
								<?=$info_spot_wrap_open . $guest_rooms . $guest_suites . $meeting_room_space . $meeting_room_number . $info_spot_wrap_close .$facility_description?>
								<div class="facility-description info-section">
									<div class="info-title">
										Welcome To The <?=$company?>!
									</div>
									<div class="info">
										<?=$description?>
									</div>
								</div>
								<?=$list_of_amenities?>
							</div>
						</div>
					</div>
					<div class="details-right">
						<div class="info">
							<div class="info-title">
								<h4>Details</h4>
							</div>
							<div class="region">
								Region: <?=$region?>
							</div>
							<div class="address">
								Address:<br />
								<?=$street_address?><br />
								<?=$city_address?> <?=$state_address?>, <?=$zip_address?>
							</div>
							<a href="https://www.google.com/maps/dir/Current+Location/<?=$address_string?>" target="_blank" class="directions">Get Directions</a>
							<div class="phone-number">
								Phone: <?=$phonelink?>
							</div>
							<?=$weburl?>
							<div class="social-media">
								<p><?=$social_string?></p>
							</div>
							<?=$deals_area?>
							<div class="clear">
								<?=get_upcoming_events()?>
							</div>
						</div>
					</div>
				</div>
				<div class="map-area" style="min-height: 450px;">
					<div class="map-area-wrap">
						<div class="map"><?=$mapPlaceHolders?></div>
							<div class="near-links">
								<div class="near-title">
									<h4>What's Nearby</h4>
									<div class="links">
										<span data-cat="3" class="nearlink" id="link1">Places to Eat</span>
										<span data-cat="2" class="nearlink" id="link2">Attractions</span>
										<span data-cat="5" class="nearlink" id="link3">Entertainment</span>
										<span data-cat="6" class="nearlink" id="link5">Shopping</span>
										<span data-cat="75" class="nearlink" id="link6">Sports &amp; Recreation</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="not-found">
					Uh-oh. Looks like your search came up empty.  Try again, or head back to the <a href="[.URL.]">home page</a>.
				</div>
			<?
		}
	}
	add_action( 'wp_enqueue_scripts', 'addIncludes'); // JS and CSS to include
	add_action( 'genesis_after_header', 'addSearchBar', 99); // Search Bar
	add_action( 'genesis_before_loop', 'addContent' ); // Content Addition
	add_action( 'genesis_after', 'mapHeightBiz');
?>

<?
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' ); // Genesis Force Full Width Page
genesis();