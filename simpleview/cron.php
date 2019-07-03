#!/usr/local/bin/php -q
<?php
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);

	if(!empty($_REQUEST['manual'])) {
	    $path = $_SERVER['DOCUMENT_ROOT']."/wp-load.php";
	} else {
	    $path = "/home/unmistakable/public_html/wp-load.php";
	}

	include_once($path);
	include_once('../credentials/simpleview.php');
	require_wp_db();

	global $wpdb;

	$pagenum 			= "1";
	$pagesize 			= "100";
	$displayamenities 	= "0";

    $rows = 0;

	function getResults($fields){
		$fields_string = '';
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string,'&');
		$url = "http://lawrence.simpleviewcrm.com/webapi/listings/xml/listings.cfm";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
		$output = curl_exec($ch);
		curl_close($ch);
		$xml = simplexml_load_string($output, null, LIBXML_NOCDATA);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		return($array);
	}

	function mysql_escape_mimic($inp) {
		if(is_array($inp) || empty($inp)) return '';
		if(!empty($inp) && is_string($inp)) return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
		return $inp;
	}

	$params = array(
		'action'=>urlencode("getListingAmenities"),
		'username'=>urlencode($username),
		'password'=>urlencode($password)
	);

	$results = getResults($params);

	if($results["REQUESTSTATUS"]["HASERRORS"] == 0 && $results["REQUESTSTATUS"]["RESULTS"] > 0){
		$wpdb->query("DELETE FROM `sv_temp_amenities`");
		$amenities = ($results["REQUESTSTATUS"]["RESULTS"] > 1) ? $results["AMENITIES"]["AMENITY"] : $results["AMENITIES"];
		foreach($amenities as $amenity){
			$wpdb->query("INSERT INTO `sv_temp_amenities` (`amenity_id`,`name`,`group_name`,`type_name`) values ('".$amenity["FIELDID"]."','".mysql_escape_mimic($amenity["LABEL"])."','".mysql_escape_mimic($amenity["AMENITYGROUPNAME"])."','".mysql_escape_mimic($amenity["TYPENAME"])."')");
		}
	}

	$params = array(
		'action'=>urlencode("getListingCats"),
		'username'=>urlencode($username),
		'password'=>urlencode($password)
	);

	$results = getResults($params);

	if($results["REQUESTSTATUS"]["HASERRORS"] == 0 && $results["REQUESTSTATUS"]["RESULTS"] > 0){
		$wpdb->query("DELETE FROM `sv_temp_categories`");
		$categories = ($results["REQUESTSTATUS"]["RESULTS"] > 1) ? $results["LISTINGCATEGORIES"]["LISTINGCATEGORY"] : $results["LISTINGCATEGORIES"];
		foreach($categories as $cat){
			$wpdb->query("INSERT INTO `sv_temp_categories` (`cat_id`, `name`) values ('".$cat["CATID"]."','".mysql_escape_mimic($cat["NAME"])."')");
			$params = array(
				'action'=>urlencode("getListingSubCats"),
				'username'=>urlencode($username),
				'password'=>urlencode($password),
				'listingcatid'=>urlencode($cat["CATID"])
			);
			$subresults = getResults($params);
			if($subresults["REQUESTSTATUS"]["HASERRORS"] == 0 && $subresults["REQUESTSTATUS"]["RESULTS"] > 0){
				$wpdb->query("DELETE FROM `sv_temp_subcategories` WHERE `cat_id` = '".$cat["CATID"]."'");
				$listingsubcategories = ($subresults["REQUESTSTATUS"]["RESULTS"] > 1) ? $subresults["LISTINGSUBCATEGORIES"]["SUBCATEGORY"] : $subresults["LISTINGSUBCATEGORIES"];
				foreach($listingsubcategories as $subcat){
					if(!empty($subcat["SUBCATID"]))
						$wpdb->query("INSERT INTO `sv_temp_subcategories` (`cat_id`,`sub_cat_id`, `name`) values ('".$cat["CATID"]."','".$subcat["SUBCATID"]."','".mysql_escape_mimic($subcat["SUBCATNAME"])."')");
				}
			}
			$params = array(
				'action'=>urlencode("getListingRegions"),
				'username'=>urlencode($username),
				'password'=>urlencode($password),
				'catid'=>urlencode($cat["CATID"])
			);
			$region_results = getResults($params);
			if($region_results["REQUESTSTATUS"]["HASERRORS"] == 0 && $region_results["REQUESTSTATUS"]["RESULTS"] > 0){
				$wpdb->query("DELETE FROM `sv_temp_regions` WHERE `cat_id` = '".$cat["CATID"]."'");
				$listingregions = ($region_results["REQUESTSTATUS"]["RESULTS"] > 1) ? $region_results["LISTINGREGIONS"]["LISTINGREGION"] : $region_results["LISTINGREGIONS"];
				foreach($listingregions as $region){
					if(!empty($region["REGIONID"]))
						$wpdb->query("INSERT INTO `sv_temp_regions` (`cat_id`,`region_id`, `name`) values ('".$cat["CATID"]."','".$region["REGIONID"]."','".mysql_escape_mimic($region["REGION"])."')");
				}
			}
		}
	}

	$recCount = 0;
	$moreRecords = true;
	$wpdb->query("DELETE FROM `sv_temp_listings`");

	while($moreRecords) {
		$params = array(
			'action'=>urlencode("getListings"),
			'username'=>urlencode($username),
			'password'=>urlencode($password),
			'pagenum'=>urlencode($pagenum),
			'pagesize'=>urlencode($pagesize),
			'displayamenities'=>urlencode($displayamenities)
		);
		$results = getResults($params);
		if($results["REQUESTSTATUS"]["HASERRORS"] == 0 && $results["REQUESTSTATUS"]["RESULTS"] > $recCount){
			$listings = ($results["REQUESTSTATUS"]["RESULTS"] > 1) ? $results["LISTINGS"]["LISTING"] : $results["LISTINGS"];
			foreach($listings as $listing){
				try{
					if(!empty($listing["FACILITYINFORMATION"]))
						$wpdb->query("INSERT INTO `sv_temp_listings` (`listing_id`,`cat_id`,`region_id`,`company`,`sort_company`,`description`,`addr1`,`addr2`,`addr3`,`city`,`state`,`zip`,`email`,`phone`,`alt_phone`,`toll_free`,`fax`,`web_url`,`latitude`,`longitude`,`logo_file`,`img_path`,`photo_file`,`acct_id`,`facilityinformation_exhibitspace`,`facilityinformation_description`,`facilityinformation_updatedate`,`facilityinformation_ceiling`,`facilityinformation_exhibits`,`facilityinformation_largestroom`,`facilityinformation_tollfree`,`facilityinformation_imagefile`,`facilityinformation_reception`,`facilityinformation_totalsqft`,`facilityinformation_spacenotes`,`facilityinformation_theatre`,`facilityinformation_villas`,`facilityinformation_bigfile`,`facilityinformation_createdate`,`facilityinformation_numrooms`,`facilityinformation_banquet`,`facilityinformation_booths`,`facilityinformation_suites`,`facilityinformation_floorplanpath`,`facilityinformation_acctid`,`facilityinformation_classroom`,`facilityinformation_sleepingrooms`) values ('".mysql_escape_mimic($listing["LISTINGID"])."','".mysql_escape_mimic($listing["CATID"])."','".mysql_escape_mimic($listing["REGIONID"])."','".mysql_escape_mimic($listing["COMPANY"])."','".mysql_escape_mimic($listing["SORTCOMPANY"])."','".mysql_escape_mimic($listing["DESCRIPTION"])."','".mysql_escape_mimic($listing["ADDR1"])."','".mysql_escape_mimic($listing["ADDR2"])."','".mysql_escape_mimic($listing["ADDR3"])."','".mysql_escape_mimic($listing["CITY"])."','".mysql_escape_mimic($listing["STATE"])."','".mysql_escape_mimic($listing["ZIP"])."','".mysql_escape_mimic($listing["EMAIL"])."','".mysql_escape_mimic($listing["PHONE"])."','".mysql_escape_mimic($listing["ALTPHONE"])."','".mysql_escape_mimic($listing["TOLLFREE"])."','".mysql_escape_mimic($listing["FAX"])."','".mysql_escape_mimic($listing["WEBURL"])."','".mysql_escape_mimic($listing["LATITUDE"])."','".mysql_escape_mimic($listing["LONGITUDE"])."','".mysql_escape_mimic($listing["LOGOFILE"])."','".mysql_escape_mimic($listing["IMGPATH"])."','".mysql_escape_mimic($listing["PHOTOFILE"])."','".mysql_escape_mimic($listing["ACCTID"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["EXHIBITSPACE"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["DESCRIPTION"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["UPDATEDATE"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["CEILING"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["EXHIBITS"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["LARGESTROOM"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["TOLLFREE"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["IMAGEFILE"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["RECEPTION"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["TOTALSQFT"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["SPACENOTES"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["THEATRE"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["VILLAS"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["BIGFILE"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["CREATEDATE"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["NUMROOMS"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["BANQUET"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["BOOTHS"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["SUITES"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["FLOORPLANPATH"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["ACCTID"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["CLASSROOM"])."','".mysql_escape_mimic($listing["FACILITYINFORMATION"]["ITEM"]["SLEEPINGROOMS"])."')");
					else
						$wpdb->query("INSERT INTO `sv_temp_listings` (`listing_id`,`cat_id`,`region_id`,`company`,`sort_company`,`description`,`addr1`,`addr2`,`addr3`,`city`,`state`,`zip`,`email`,`phone`,`alt_phone`,`toll_free`,`fax`,`web_url`,`latitude`,`longitude`,`logo_file`,`img_path`,`photo_file`,`acct_id`) values ('".mysql_escape_mimic($listing["LISTINGID"])."','".mysql_escape_mimic($listing["CATID"])."','".mysql_escape_mimic($listing["REGIONID"])."','".mysql_escape_mimic($listing["COMPANY"])."','".mysql_escape_mimic($listing["SORTCOMPANY"])."','".mysql_escape_mimic($listing["DESCRIPTION"])."','".mysql_escape_mimic($listing["ADDR1"])."','".mysql_escape_mimic($listing["ADDR2"])."','".mysql_escape_mimic($listing["ADDR3"])."','".mysql_escape_mimic($listing["CITY"])."','".mysql_escape_mimic($listing["STATE"])."','".mysql_escape_mimic($listing["ZIP"])."','".mysql_escape_mimic($listing["EMAIL"])."','".mysql_escape_mimic($listing["PHONE"])."','".mysql_escape_mimic($listing["ALTPHONE"])."','".mysql_escape_mimic($listing["TOLLFREE"])."','".mysql_escape_mimic($listing["FAX"])."','".mysql_escape_mimic($listing["WEBURL"])."','".mysql_escape_mimic($listing["LATITUDE"])."','".mysql_escape_mimic($listing["LONGITUDE"])."','".mysql_escape_mimic($listing["LOGOFILE"])."','".mysql_escape_mimic($listing["IMGPATH"])."','".mysql_escape_mimic($listing["PHOTOFILE"])."','".mysql_escape_mimic($listing["ACCTID"])."')");
					$params = array(
						'action'=>urlencode("getListing"),
						'username'=>urlencode($username),
						'password'=>urlencode($password),
						'listingid'=>urlencode($listing["LISTINGID"])
					);
					$listingId = $listing["LISTINGID"];
					$listingSubcatId = $listing["SUBCATID"];
					$listing = getResults($params);
					if($listing["REQUESTSTATUS"]["HASERRORS"] == 0 && $listing["REQUESTSTATUS"]["RESULTS"] > 0){
						$wpdb->query("DELETE FROM `sv_temp_listings_subcategories_XREF` WHERE `listing_id` = '".$listingId."'");
						$wpdb->query("INSERT INTO `sv_temp_listings_subcategories_XREF` (`listing_id`,`sub_cat_id`,`is_primary`) values ('".$listingId."','".$listingSubcatId."',1)");
						if(!empty($listing["LISTING"]["ADDITIONALSUBCATS"]["ITEM"]))
						foreach($listing["LISTING"]["ADDITIONALSUBCATS"]["ITEM"] as $subcat){
							$wpdb->query("INSERT INTO `sv_temp_listings_subcategories_XREF` (`listing_id`,`sub_cat_id`) values ('".$listingId."','".$subcat["SUBCATID"]."')");
						}
						$wpdb->query("DELETE FROM `sv_temp_additional_information` WHERE `listing_id` = '".$listingId."'");
						if(!empty($listing["LISTING"]["ADDITIONALINFORMATION"]["ITEM"]))
						foreach($listing["LISTING"]["ADDITIONALINFORMATION"]["ITEM"] as $adinfo){
							$wpdb->query("INSERT INTO `sv_temp_additional_information` (`listing_id`,`name`,`value`) values ('".$listingId."','".mysql_escape_mimic($adinfo["NAME"])."','".mysql_escape_mimic($adinfo["VALUE"])."')");
						}
						$wpdb->query("DELETE FROM `sv_temp_listings_amenities_XREF` WHERE `listing_id` = '".$listingId."'");
						if(!empty($listing["LISTING"]["AMENITIES"]["ITEM"]))
						foreach($listing["LISTING"]["AMENITIES"]["ITEM"] as $amenity){
							if ($amenity["VALUE"] == '1') {
								$wpdb->query("INSERT INTO `sv_temp_listings_amenities_XREF` (`listing_id`,`amenity_id`) values ('".$listingId."','".mysql_escape_mimic($amenity["FIELDID"])."')");
							} else if (!empty($amenity["VALUEARRAY"]["ITEM"]) && is_array($amenity["VALUEARRAY"]["ITEM"]) && count($amenity["VALUEARRAY"]["ITEM"]) > 0) {
								$items = serialize($amenity["VALUEARRAY"]["ITEM"]);
								$wpdb->query("INSERT INTO `sv_temp_listings_amenities_XREF` (`listing_id`,`amenity_id`,`multi_values`) values ('".$listingId."','".mysql_escape_mimic($amenity["FIELDID"])."','".mysql_escape_mimic($items)."')");
							}
						}
						$wpdb->query("DELETE FROM `sv_temp_images` WHERE `listing_id` = '".$listingId."'");
						$images = (!empty($listing["LISTING"]["IMAGES"]["ITEM"][0]["MEDIAID"])) ? $listing["LISTING"]["IMAGES"]["ITEM"] : ((!empty($listing["LISTING"]["IMAGES"]["MEDIAID"])) ? $listing["LISTING"]["IMAGES"] : array());
						foreach($images as $media){
							if (!empty($media["MEDIAID"]))
								$wpdb->query("INSERT INTO `sv_temp_images` (`listing_id`,`mediaid`,`mediafile`,`sortorder`,`medianame`,`mediadesc`,`thumfile`,`imgpath`) values ('".$listingId."','".mysql_escape_mimic($media["MEDIAID"])."','".mysql_escape_mimic($media["MEDIAFILE"])."','".mysql_escape_mimic($media["SORTORDER"])."','".mysql_escape_mimic($media["MEDIANAME"])."','".mysql_escape_mimic($media["MEDIADESC"])."','".mysql_escape_mimic($media["THUMFILE"])."','".mysql_escape_mimic($media["IMGPATH"])."')");
						}
						$wpdb->query("DELETE FROM `sv_temp_socialmedia` WHERE `listing_id` = '".$listingId."'");
						$socialmedias = (!empty($listing["LISTING"]["SOCIALMEDIA"]["ITEM"][0]["SERVICE"])) ? $listing["LISTING"]["SOCIALMEDIA"]["ITEM"] : ((!empty($listing["LISTING"]["SOCIALMEDIA"]["SERVICE"])) ? $listing["LISTING"]["SOCIALMEDIA"] : array());
						foreach($socialmedias as $media){
							if (!empty($media["SERVICE"]))
								$wpdb->query("INSERT INTO `sv_temp_socialmedia` (`listing_id`,`fieldname`,`value`,`service`,`level`) values ('".$listingId."','".mysql_escape_mimic($media["FIELDNAME"])."','".mysql_escape_mimic($media["VALUE"])."','".mysql_escape_mimic($media["SERVICE"])."','".mysql_escape_mimic($media["LEVEL"])."')");
						}
					}
				} catch (Exception $e) {
					echo 'Caught exception: ',  $e->getMessage(), "\n";
					die();
				}
				$recCount++;
			}
		} else {
			$moreRecords = false;
		}
		$pagenum++;
	}

	// forms
	$wpdb->query("DELETE FROM `sv_temp_forms`");

	for ($x = 1; $x <= 100; $x++) {
		$url = 'http://lawrence.simpleviewcrm.com/webapi/forms/getform.cfm';
		$fields = 'username='.$username.'&password='.$password.'&FORMID='.$x;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$results = curl_exec($ch);
		curl_close($ch);
		if (strpos($results, '[Error Code]') === FALSE)
			$wpdb->query("INSERT INTO `sv_temp_forms` (`form_id`,`html`) values ('".$x."','".mysql_escape_mimic($results)."')");
		else
			continue;
	}

    	// events
	$url = 'http://cs.simpleviewinc.com/feeds/events.cfm?apikey=05B4B655-5056-A36A-1C70E499C44CD955';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$myXMLData = curl_exec($ch);
	curl_close($ch);
	$xml=simplexml_load_string($myXMLData, null, LIBXML_NOCDATA) or die("Error: Cannot create object");
	$json = json_encode($xml);
    	$eventsArray = json_decode($json,TRUE);
    	$currentTime = time();

	if ($eventsArray["success"] == 'Yes'){
	    $wpdb->query("DELETE FROM `sv_temp_events`");
	    if(!empty($eventsArray["events"]["event"])) {
	        foreach($eventsArray["events"]["event"] as $event){
		    $eventType = $event['eventtype'];
		    $eventStartTime = strtotime($event['startdate']);
		    $futureEvent = $eventType == "One-Time Event" && $eventStartTime >= $currentTime;
		    $recurringEvent = $eventType == "Ongoing Event";
		    // Only cache events that are in the future or are recurring
		    if ($futureEvent || $recurringEvent) {
		        $wpdb->query("INSERT INTO `sv_temp_events` (`eventid`,`eventtype`,`title`,`description`,`startdate`,`enddate`,`recurrence`,`times`,`location`,`phone`,`admission`,`website`,`imagefile`,`address`,`city`,`state`,`zip`,`latitude`,`longitude`,`featured`,`listingid`,`starttime`,`endtime`) values ('".mysql_escape_mimic($event["eventid"])."','".mysql_escape_mimic($event["eventtype"])."','".mysql_escape_mimic($event["title"])."','".mysql_escape_mimic($event["description"])."','".str_replace('1970-01-01','',date('Y-m-d', strtotime(mysql_escape_mimic($event["startdate"]))))."','".str_replace('1970-01-01','',date('Y-m-d', strtotime(mysql_escape_mimic($event["enddate"]))))."','".mysql_escape_mimic($event["recurrence"])."','".mysql_escape_mimic($event["times"])."','".mysql_escape_mimic($event["location"])."','".mysql_escape_mimic($event["phone"])."','".mysql_escape_mimic($event["admission"])."','".mysql_escape_mimic($event["website"])."','".mysql_escape_mimic($event["imagefile"])."','".mysql_escape_mimic($event["address"])."','".mysql_escape_mimic($event["city"])."','".mysql_escape_mimic($event["state"])."','".mysql_escape_mimic($event["zip"])."','".mysql_escape_mimic($event["latitude"])."','".mysql_escape_mimic($event["longitude"])."','".mysql_escape_mimic($event["featured"])."','".mysql_escape_mimic($event["listingid"])."','".mysql_escape_mimic($event["starttime"])."','".mysql_escape_mimic($event["endtime"])."')");
		        $wpdb->query("DELETE FROM `sv_temp_event_categories` WHERE `eventid` = '".$event["eventid"]."'");
		        $eventcategories = (!empty($event["eventcategories"]["eventcategory"]["categoryid"])) ? $event["eventcategories"] : $event["eventcategories"]["eventcategory"];

			foreach($eventcategories as $eventcategory){
			    if (!empty($eventcategory["categoryname"])) {
			        $wpdb->query("INSERT INTO `sv_temp_event_categories` (`eventid`,`categoryid`,`categoryname`) values ('".mysql_escape_mimic($event["eventid"])."','".mysql_escape_mimic($eventcategory["categoryid"])."','".mysql_escape_mimic($eventcategory["categoryname"])."')");
			    }
			}

		        $wpdb->query("DELETE FROM `sv_temp_event_dates` WHERE `eventid` = '".$event["eventid"]."'");
		        $eventdates = (is_array($event["eventdates"]["eventdate"])) ? $event["eventdates"]["eventdate"] : $event["eventdates"];

		        foreach($eventdates as $eventdate) {
			    if (!empty($eventdate)) {
			        $wpdb->query("INSERT INTO `sv_temp_event_dates` (`eventid`,`eventdate`) values ('".mysql_escape_mimic($event["eventid"])."','".str_replace('1970-01-01','',date('Y-m-d', strtotime(mysql_escape_mimic($eventdate))))."')");
			    }
		        }

		        $wpdb->query("DELETE FROM `sv_temp_event_images` WHERE `eventid` = '".$event["eventid"]."'");

		        $eventimages = (!empty($event["images"]["image"])) ? ((!empty($event["images"]["image"]["mediafile"])) ? $event["images"] : $event["images"]["image"]) : array();

		        foreach($eventimages as $eventimage) {
			    if (!empty($eventimage["mediafile"])) {
			        $wpdb->query("INSERT INTO `sv_temp_event_images` (`eventid`,`mediafile`,`sortorder`) values ('".mysql_escape_mimic($event["eventid"])."','".mysql_escape_mimic($eventimage["mediafile"])."','".mysql_escape_mimic($eventimage["sortorder"])."')");
			    }
		        }
		    }
	        }
	    }
    	}

	// coupons
	$recCount = 0;
	$pagenum = 1;
	$moreRecords = true;
	$wpdb->query("DELETE FROM `sv_temp_coupons`");
	$wpdb->query("DELETE FROM `sv_temp_coupon_categories`");
	while($moreRecords) {
		$params = array(
			'action'=>urlencode("getCoupons"),
			'username'=>urlencode($username),
			'password'=>urlencode($password),
			'pagenum'=>urlencode($pagenum),
			'pagesize'=>urlencode($pagesize)
		);
		$results = getResults($params);
		if($results["REQUESTSTATUS"]["HASERRORS"] == 0 && $results["REQUESTSTATUS"]["RESULTS"] > $recCount){
			$coupons = ($results["REQUESTSTATUS"]["RESULTS"] > 1) ? $results["COUPONS"]["COUPON"] : $results["COUPONS"];
			foreach($coupons as $coupon){
				try{
					$wpdb->query("INSERT INTO `sv_temp_coupons` (`sortcompany`,`state`,`mediafile`,`listingid`,`updatedate`,`mediatype`,`weburl`,`primarycontacttitle`,`imgpath`,`addr2`,`phone`,`addr1`,`addr3`,`zip`,`offertext`,`redeemstart`,`thumbfile`,`redeemend`,`catname`,`email`,`poststart`,`fax`,`acctstatus`,`couponid`,`company`,`subcatid`,`medianame`,`tollfree`,`catid`,`altphone`,`offertitle`,`mediaid`,`subcatname`,`mediatypeid`,`postend`,`primarycontactfullname`,`city`,`offerlink`,`acctid`) values ('".mysql_escape_mimic($coupon["SORTCOMPANY"])."','".mysql_escape_mimic($coupon["STATE"])."','".mysql_escape_mimic($coupon["MEDIAFILE"])."','".mysql_escape_mimic($coupon["LISTINGID"])."','".mysql_escape_mimic($coupon["UPDATEDATE"])."','".mysql_escape_mimic($coupon["MEDIATYPE"])."','".mysql_escape_mimic($coupon["WEBURL"])."','".mysql_escape_mimic($coupon["PRIMARYCONTACTTITLE"])."','".(isset($coupon["IMGPATH"]) ? mysql_escape_mimic($coupon["IMGPATH"]) : null)."','".mysql_escape_mimic($coupon["ADDR2"])."','".mysql_escape_mimic($coupon["PHONE"])."','".mysql_escape_mimic($coupon["ADDR1"])."','".mysql_escape_mimic($coupon["ADDR3"])."','".mysql_escape_mimic($coupon["ZIP"])."','".mysql_escape_mimic($coupon["OFFERTEXT"])."','".mysql_escape_mimic($coupon["REDEEMSTART"])."','".mysql_escape_mimic($coupon["THUMBFILE"])."','".mysql_escape_mimic($coupon["REDEEMEND"])."','".mysql_escape_mimic($coupon["CATNAME"])."','".mysql_escape_mimic($coupon["EMAIL"])."','".mysql_escape_mimic($coupon["POSTSTART"])."','".mysql_escape_mimic($coupon["FAX"])."','".mysql_escape_mimic($coupon["ACCTSTATUS"])."','".mysql_escape_mimic($coupon["COUPONID"])."','".mysql_escape_mimic($coupon["COMPANY"])."','".mysql_escape_mimic($coupon["SUBCATID"])."','".mysql_escape_mimic($coupon["MEDIANAME"])."','".mysql_escape_mimic($coupon["TOLLFREE"])."','".mysql_escape_mimic($coupon["CATID"])."','".mysql_escape_mimic($coupon["ALTPHONE"])."','".mysql_escape_mimic($coupon["OFFERTITLE"])."','".mysql_escape_mimic($coupon["MEDIAID"])."','".mysql_escape_mimic($coupon["SUBCATNAME"])."','".mysql_escape_mimic($coupon["MEDIATYPEID"])."','".mysql_escape_mimic($coupon["POSTEND"])."','".mysql_escape_mimic($coupon["PRIMARYCONTACTFULLNAME"])."','".mysql_escape_mimic($coupon["CITY"])."','".mysql_escape_mimic($coupon["OFFERLINK"])."','".mysql_escape_mimic($coupon["ACCTID"])."')");
					if(!empty($coupon["COUPONCATS"]["ITEM"]))
					foreach($coupon["COUPONCATS"] as $couponcat){
						if(!empty($couponcat["COUPONCATNAME"]) && !empty($couponcat["COUPONCATID"]) && !empty($couponcat["COUPONID"]))
							$wpdb->query("INSERT INTO `sv_temp_coupon_categories` (`couponcatname`,`couponcatid`,`couponid`) values ('".mysql_escape_mimic($couponcat["COUPONCATNAME"])."','".mysql_escape_mimic($couponcat["COUPONCATID"])."','".mysql_escape_mimic($couponcat["COUPONID"])."')");
					}
				} catch (Exception $e) {
					echo 'Caught exception: ',  $e->getMessage(), "\n";
					die();
				}
				$recCount++;
			}
		} else {
			$moreRecords = false;
		}
		$pagenum++;
	}

$q="RENAME TABLE sv_additional_information TO tmp_sv_additional_information, sv_temp_additional_information TO sv_additional_information, tmp_sv_additional_information TO sv_temp_additional_information;"; $wpdb->query($q);
$q="RENAME TABLE sv_amenities TO tmp_sv_amenities, sv_temp_amenities TO sv_amenities, tmp_sv_amenities TO sv_temp_amenities;"; $wpdb->query($q);
$q="RENAME TABLE sv_categories TO tmp_sv_categories, sv_temp_categories TO sv_categories, tmp_sv_categories TO sv_temp_categories;"; $wpdb->query($q);
$q="RENAME TABLE sv_events TO tmp_sv_events, sv_temp_events TO sv_events, tmp_sv_events TO sv_temp_events;"; $wpdb->query($q);
$q="RENAME TABLE sv_forms TO tmp_sv_forms, sv_temp_forms TO sv_forms, tmp_sv_forms TO sv_temp_forms;"; $wpdb->query($q);
$q="RENAME TABLE sv_images TO tmp_sv_images, sv_temp_images TO sv_images, tmp_sv_images TO sv_temp_images;"; $wpdb->query($q);
$q="RENAME TABLE sv_listings TO tmp_sv_listings, sv_temp_listings TO sv_listings, tmp_sv_listings TO sv_temp_listings;"; $wpdb->query($q);
$q="RENAME TABLE sv_listings_amenities_XREF TO tmp_sv_listings_amenities_XREF, sv_temp_listings_amenities_XREF TO sv_listings_amenities_XREF, tmp_sv_listings_amenities_XREF TO sv_temp_listings_amenities_XREF;"; $wpdb->query($q);
$q="RENAME TABLE sv_listings_subcategories_XREF TO tmp_sv_listings_subcategories_XREF, sv_temp_listings_subcategories_XREF TO sv_listings_subcategories_XREF, tmp_sv_listings_subcategories_XREF TO sv_temp_listings_subcategories_XREF;"; $wpdb->query($q);
$q="RENAME TABLE sv_regions TO tmp_sv_regions, sv_temp_regions TO sv_regions, tmp_sv_regions TO sv_temp_regions;"; $wpdb->query($q);
$q="RENAME TABLE sv_subcategories TO tmp_sv_subcategories, sv_temp_subcategories TO sv_subcategories, tmp_sv_subcategories TO sv_temp_subcategories;"; $wpdb->query($q);
$q="RENAME TABLE sv_coupons TO tmp_sv_coupons, sv_temp_coupons TO sv_coupons, tmp_sv_coupons TO sv_temp_coupons;"; $wpdb->query($q);
$q="RENAME TABLE sv_coupon_categories TO tmp_sv_coupon_categories, sv_temp_coupon_categories TO sv_coupon_categories, tmp_sv_coupon_categories TO sv_temp_coupon_categories;"; $wpdb->query($q);
$q="RENAME TABLE sv_event_categories TO tmp_sv_event_categories, sv_temp_event_categories TO sv_event_categories, tmp_sv_event_categories TO sv_temp_event_categories;"; $wpdb->query($q);
$q="RENAME TABLE sv_event_dates TO tmp_sv_event_dates, sv_temp_event_dates TO sv_event_dates, tmp_sv_event_dates TO sv_temp_event_dates;"; $wpdb->query($q);
$q="RENAME TABLE sv_event_images TO tmp_sv_event_images, sv_temp_event_images TO sv_event_images, tmp_sv_event_images TO sv_temp_event_images;"; $wpdb->query($q);
$q="RENAME TABLE sv_socialmedia TO tmp_sv_socialmedia, sv_temp_socialmedia TO sv_socialmedia, tmp_sv_socialmedia TO sv_temp_socialmedia;"; $wpdb->query($q);

die('Complete ');
