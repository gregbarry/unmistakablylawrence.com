<?php
	include_once('../wp-load.php');
	require_wp_db();

	//$q="ALTER TABLE sv_temp_events MODIFY `description` text NOT NULL;";
	//$wpdb->query($q);

  // }
  // $images = $wpdb->get_results("SELECT mediafile FROM sv_event_images WHERE eventid = '59' ORDER BY sortorder ASC");
  /*
  $q = "				(SELECT DISTINCT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`, r.name as region, '0' as `map_cat`, sc.sub_cat_id as subcat, sc.name as subname
				FROM sv_listings l
				INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
				INNER JOIN sv_subcategories sc ON sc.cat_id = l.cat_id AND sc.is_deleted = '0'
				INNER JOIN sv_listings_subcategories_XREF x ON x.sub_cat_id = sc.sub_cat_id AND x.listing_id = l.listing_id AND x.is_deleted = '0'
				INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.region_id = l.region_id AND r.is_deleted = '0'
				LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
				LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
				WHERE l.is_deleted = '0'
					AND l.listing_id = '255')
			UNION
				(SELECT DISTINCT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`, r.name as region, c.cat_id as `map_cat`, sc.sub_cat_id as subcat,  sc.name as subname
				FROM sv_listings l
				INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
				INNER JOIN sv_subcategories sc ON sc.cat_id = l.cat_id AND sc.is_deleted = '0'
				INNER JOIN sv_listings_subcategories_XREF x ON x.sub_cat_id = sc.sub_cat_id AND x.listing_id = l.listing_id AND x.is_deleted = '0'
				INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.region_id = l.region_id AND r.is_deleted = '0'
				LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
				LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
				WHERE l.is_deleted = '0'
					AND c.cat_id IN('3','2','5','6','20')
					AND l.listing_id != '255')
			ORDER BY map_cat ASC;";
      */

	// $q="SELECT DISTINCT e.*
		// FROM `sv_events` e
		// INNER JOIN `sv_event_dates` d ON d.eventid = e.eventid AND d.is_deleted = '0'
		// INNER JOIN `sv_event_categories` c ON c.eventid = e.eventid AND d.is_deleted = '0'
		// WHERE e.is_deleted = '0'
			// AND c.categoryid = ifnull('6', c.categoryid)
			// AND d.eventdate BETWEEN '2015-04-01' AND '2015-05-01'
		// ORDER BY d.eventdate ASC";
    $selectedCategory = 'NULL';
    $sub_cat_id = 'NULL';
    $reg_id = 'NULL';
    $listing_id = 'NULL';
    $featured = 'NULL';
    $listings = $wpdb->get_results("
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
			AND l.listing_id = IFNULL(".$listing_id.",l.listing_id)
			AND ai.value = IFNULL(".$featured.",ai.value)
		ORDER BY l.sort_company ASC;
	");
  echo "<pre>";
      print_r($listings);
      echo "</pre>";
  $q = "SELECT * FROM sv_events";
	$results = $wpdb->get_results($q);
  foreach($results as $r) {

    echo "<br>";

  }

	 // echo"EVENTIMAGES*********************<pre>";
   // echo "<pre>";
	 // var_dump($results);
   // die();

   // $q = "SELECT * FROM sv_events";
   // $results = $wpdb->get_results($q);

	 // echo"EVENTS****************<pre>";
	 // var_dump($results);
  /*foreach($results as $r) {
    if (in_array($r->listing_id, $ids)) {
      foreach($r as $k => $v) {
        echo $k . ": " . $v . "<br>";
      }
    echo "<br>";
    }
  }*/


/*
sv_additional_information
sv_amenities
sv_categories
sv_events
sv_forms
sv_images
sv_listings
sv_listings_amenities_XREF
sv_listings_subcategories_XREF
sv_regions
sv_subcategories
sv_coupons
sv_coupon_categories
sv_event_categories
sv_event_dates
sv_event_images
sv_socialmedia

ALTER TABLE tablename MODIFY columnname INTEGER;

-- get list from any table (sv_categories for example - always include the deleted check)
SELECT * FROM sv_categories WHERE is_deleted = '0';

-- regions by cat_id
SELECT r.*
FROM sv_categories c
INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.is_deleted = '0'
WHERE c.is_deleted = '0' AND c.cat_id = '1'
ORDER BY r.name ASC;

-- get listings by category (1 = 'Accommodations') featured at top
SELECT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`
FROM sv_listings l
INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
WHERE l.is_deleted = '0' AND c.cat_id = '1'
ORDER BY ai.value DESC, l.sort_company ASC;

-- get listings by category and region featured at top
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
	AND l.listing_id = IFNULL(".$listing_id.",l.listing_id)
	AND ai.value = IFNULL(".$featured.",ai.value)
ORDER BY ai.value DESC, l.sort_company ASC;

-- get listings by category including a selected listing
	(SELECT DISTINCT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`, r.name as region, '0' as `map_cat`
	FROM sv_listings l
	INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
	INNER JOIN sv_subcategories sc ON sc.cat_id = l.cat_id AND sc.is_deleted = '0'
	INNER JOIN sv_listings_subcategories_XREF x ON x.sub_cat_id = sc.sub_cat_id AND x.listing_id = l.listing_id AND x.is_deleted = '0'
	INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.region_id = l.region_id AND r.is_deleted = '0'
	LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
	LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
	WHERE l.is_deleted = '0'
		AND l.listing_id = '".$listingid."')
UNION
	(SELECT DISTINCT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`, r.name as region, c.cat_id as `map_cat`
	FROM sv_listings l
	INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
	INNER JOIN sv_subcategories sc ON sc.cat_id = l.cat_id AND sc.is_deleted = '0'
	INNER JOIN sv_listings_subcategories_XREF x ON x.sub_cat_id = sc.sub_cat_id AND x.listing_id = l.listing_id AND x.is_deleted = '0'
	INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.region_id = l.region_id AND r.is_deleted = '0'
	LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
	LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
	WHERE l.is_deleted = '0'
		AND c.cat_id IN('3','2','5','6','20')
		AND l.listing_id != '".$listingid."')
ORDER BY map_cat ASC;

-- gets amenities by the listing_id (unserialize function will turn multi_values into a php array)
SELECT a.*, x.multi_values
FROM sv_listings l
INNER JOIN sv_listings_amenities_XREF x ON x.listing_id = l.listing_id AND x.is_deleted = '0'
INNER JOIN sv_amenities a ON a.amenity_id = x.amenity_id AND a.is_deleted = '0'
WHERE l.is_deleted = '0' AND l.listing_id = '255';

-- gets listing images by the listing_id
SELECT i.*
FROM sv_listings l
INNER JOIN sv_images i ON i.listing_id = l.listing_id AND i.is_deleted = '0'
WHERE l.is_deleted = '0' AND l.listing_id = '255'
ORDER BY i.sortorder ASC;

-- get event categories by event ID (same sql format for dates and images also works for coupon categories)
SELECT c.*
FROM sv_events e
INNER JOIN sv_event_categories c ON c.eventid = e.eventid AND c.is_deleted = '0'
WHERE e.is_deleted = '0' AND e.eventid = '36';



-- get list from any table (sv_categories for example - always include the deleted check)
SELECT * FROM sv_categories WHERE is_deleted = '0';

-- regions by cat_id
SELECT r.*
FROM sv_categories c
INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.is_deleted = '0'
WHERE c.is_deleted = '0' AND c.cat_id = '1'
ORDER BY r.name ASC;

-- get listings by category (1 = 'Accommodations') featured at top
SELECT c.name as category, l.*, ai.value as `Featured`, ai2.value as `Teaser`
FROM sv_listings l
INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
WHERE l.is_deleted = '0' AND c.cat_id = '1'
ORDER BY ai.value DESC, l.sort_company ASC;

-- get listings by category and region featured at top
SELECT c.name as category, r.name as region, l.*, ai.value as `Featured`, ai2.value as `Teaser`
FROM sv_listings l
INNER JOIN sv_categories c ON c.cat_id = l.cat_id AND c.is_deleted = '0'
INNER JOIN sv_regions r ON r.cat_id = c.cat_id AND r.is_deleted = '0'
LEFT JOIN sv_additional_information ai ON ai.listing_id = l.listing_id AND ai.is_deleted = '0' AND ai.name = 'Featured'
LEFT JOIN sv_additional_information ai2 ON ai2.listing_id = l.listing_id AND ai2.is_deleted = '0' AND ai2.name = 'Teaser'
WHERE l.is_deleted = '0' AND c.cat_id = '1' AND r.region_id = '2'
ORDER BY ai.value DESC, l.sort_company ASC;

-- gets amenities by the listing_id (unserialize function will turn multi_values into a php array)
SELECT a.*, x.multi_values
FROM sv_listings l
INNER JOIN sv_listings_amenities_XREF x ON x.listing_id = l.listing_id AND x.is_deleted = '0'
INNER JOIN sv_amenities a ON a.amenity_id = x.amenity_id AND a.is_deleted = '0'
WHERE l.is_deleted = '0' AND l.listing_id = '255';

-- gets listing images by the listing_id
SELECT i.*
FROM sv_listings l
INNER JOIN sv_images i ON i.listing_id = l.listing_id AND i.is_deleted = '0'
WHERE l.is_deleted = '0' AND l.listing_id = '255'
ORDER BY i.sortorder ASC;

-- get event categories by event ID (same sql format for dates and images also works for coupon categories)
SELECT c.*
FROM sv_events e
INNER JOIN sv_event_categories c ON c.eventid = e.eventid AND c.is_deleted = '0'
WHERE e.is_deleted = '0' AND e.eventid = '36';



sv_additional_information
sv_amenities
sv_categories
sv_events
sv_forms
sv_images
sv_listings
sv_listings_amenities_XREF
sv_listings_subcategories_XREF
sv_regions
sv_subcategories
sv_coupons
sv_coupon_categories
sv_event_categories
sv_event_dates
sv_event_images
sv_socialmedia



CREATE TABLE `sv_additional_information` (
  `listing_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_amenities` (
  `amenity_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `type_name` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `amenity_id` (`amenity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_categories` (
  `cat_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `cat_id` (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_events` (
  `eventid` int(11) NOT NULL,
  `eventtype` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `recurrence` varchar(255) NOT NULL,
  `times` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `admission` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `imagefile` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `featured` varchar(255) NOT NULL,
  `listingid` int(11) NOT NULL,
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `listingid` (`listingid`),
  KEY `startdate` (`startdate`),
  KEY `enddate` (`enddate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_forms` (
  `form_id` int(11) NOT NULL,
  `html` mediumtext NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_images` (
  `listing_id` int(11) NOT NULL,
  `mediaid` int(11) NOT NULL,
  `mediafile` varchar(255) NOT NULL,
  `sortorder` varchar(255) NOT NULL,
  `medianame` varchar(255) NOT NULL,
  `mediadesc` varchar(255) NOT NULL,
  `thumfile` varchar(255) NOT NULL,
  `imgpath` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_listings` (
  `listing_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `company` varchar(255) NOT NULL,
  `sort_company` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `addr1` varchar(255) NOT NULL,
  `addr2` varchar(255) NOT NULL,
  `addr3` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `alt_phone` varchar(255) NOT NULL,
  `toll_free` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `web_url` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `logo_file` varchar(255) NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `photo_file` varchar(255) NOT NULL,
  `acct_id` int(11) NOT NULL,
  `facilityinformation_exhibitspace` varchar(255) NOT NULL,
  `facilityinformation_description` text NOT NULL,
  `facilityinformation_updatedate` varchar(255) NOT NULL,
  `facilityinformation_ceiling` varchar(255) NOT NULL,
  `facilityinformation_exhibits` varchar(255) NOT NULL,
  `facilityinformation_largestroom` varchar(255) NOT NULL,
  `facilityinformation_tollfree` varchar(255) NOT NULL,
  `facilityinformation_imagefile` varchar(255) NOT NULL,
  `facilityinformation_reception` varchar(255) NOT NULL,
  `facilityinformation_totalsqft` varchar(255) NOT NULL,
  `facilityinformation_spacenotes` varchar(255) NOT NULL,
  `facilityinformation_theatre` varchar(255) NOT NULL,
  `facilityinformation_villas` varchar(255) NOT NULL,
  `facilityinformation_bigfile` varchar(255) NOT NULL,
  `facilityinformation_createdate` varchar(255) NOT NULL,
  `facilityinformation_numrooms` varchar(255) NOT NULL,
  `facilityinformation_banquet` varchar(255) NOT NULL,
  `facilityinformation_booths` varchar(255) NOT NULL,
  `facilityinformation_suites` varchar(255) NOT NULL,
  `facilityinformation_floorplanpath` varchar(255) NOT NULL,
  `facilityinformation_acctid` varchar(255) NOT NULL,
  `facilityinformation_classroom` varchar(255) NOT NULL,
  `facilityinformation_sleepingrooms` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`),
  KEY `cat_id` (`cat_id`),
  KEY `region_id` (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_listings_amenities_XREF` (
  `amenity_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `multi_values` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `amenity_id` (`amenity_id`),
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_listings_subcategories_XREF` (
  `listing_id` int(11) NOT NULL,
  `sub_cat_id` int(11) NOT NULL,
  `is_primary` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`),
  KEY `sub_cat_id` (`sub_cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_regions` (
  `cat_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `cat_id` (`cat_id`),
  KEY `region_id` (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_subcategories` (
  `cat_id` int(11) NOT NULL,
  `sub_cat_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `cat_id` (`cat_id`),
  KEY `sub_cat_id` (`sub_cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_coupons` (
  `sortcompany` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `mediafile` varchar(255) NOT NULL,
  `listingid` int(11) NOT NULL,
  `updatedate` varchar(255) NOT NULL,
  `mediatype` varchar(255) NOT NULL,
  `weburl` varchar(255) NOT NULL,
  `primarycontacttitle` varchar(255) NOT NULL,
  `imgpath` varchar(255) NOT NULL,
  `addr2` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `addr1` varchar(255) NOT NULL,
  `addr3` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `offertext` varchar(255) NOT NULL,
  `redeemstart` varchar(255) NOT NULL,
  `thumbfile` varchar(255) NOT NULL,
  `redeemend` varchar(255) NOT NULL,
  `catname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `poststart` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `acctstatus` varchar(255) NOT NULL,
  `couponid` int(11) NOT NULL,
  `company` varchar(255) NOT NULL,
  `subcatid` varchar(255) NOT NULL,
  `medianame` varchar(255) NOT NULL,
  `tollfree` varchar(255) NOT NULL,
  `catid` varchar(255) NOT NULL,
  `altphone` varchar(255) NOT NULL,
  `offertitle` varchar(255) NOT NULL,
  `mediaid` varchar(255) NOT NULL,
  `subcatname` varchar(255) NOT NULL,
  `mediatypeid` varchar(255) NOT NULL,
  `postend` varchar(255) NOT NULL,
  `primarycontactfullname` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `offerlink` varchar(255) NOT NULL,
  `acctid` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listingid` (`listingid`),
  KEY `couponid` (`couponid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_coupon_categories` (
  `couponcatname` varchar(255) NOT NULL,
  `couponcatid` varchar(255) NOT NULL,
  `couponid` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `couponid` (`couponid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_event_categories` (
  `eventid` int(11) NOT NULL,
  `categoryid` int(11) NOT NULL,
  `categoryname` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `categoryid` (`categoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_event_dates` (
  `eventid` int(11) NOT NULL,
  `eventdate` date NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `eventid` (`eventid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_event_images` (
  `eventid` int(11) NOT NULL,
  `mediafile` varchar(255) NOT NULL,
  `sortorder` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `eventid` (`eventid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sv_socialmedia` (
  `listing_id` int(11) NOT NULL,
  `fieldname` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

****************************************************************************************************************8


$q="CREATE TABLE `sv_temp_additional_information` (
  `listing_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_amenities` (
  `amenity_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `type_name` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `amenity_id` (`amenity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_categories` (
  `cat_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `cat_id` (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_events` (
  `eventid` int(11) NOT NULL,
  `eventtype` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `recurrence` varchar(255) NOT NULL,
  `times` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `admission` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `imagefile` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `featured` varchar(255) NOT NULL,
  `listingid` int(11) NOT NULL,
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `listingid` (`listingid`),
  KEY `startdate` (`startdate`),
  KEY `enddate` (`enddate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_forms` (
  `form_id` int(11) NOT NULL,
  `html` mediumtext NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_images` (
  `listing_id` int(11) NOT NULL,
  `mediaid` int(11) NOT NULL,
  `mediafile` varchar(255) NOT NULL,
  `sortorder` varchar(255) NOT NULL,
  `medianame` varchar(255) NOT NULL,
  `mediadesc` varchar(255) NOT NULL,
  `thumfile` varchar(255) NOT NULL,
  `imgpath` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_listings` (
  `listing_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `company` varchar(255) NOT NULL,
  `sort_company` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `addr1` varchar(255) NOT NULL,
  `addr2` varchar(255) NOT NULL,
  `addr3` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `alt_phone` varchar(255) NOT NULL,
  `toll_free` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `web_url` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `logo_file` varchar(255) NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `photo_file` varchar(255) NOT NULL,
  `acct_id` int(11) NOT NULL,
  `facilityinformation_exhibitspace` varchar(255) NOT NULL,
  `facilityinformation_description` text NOT NULL,
  `facilityinformation_updatedate` varchar(255) NOT NULL,
  `facilityinformation_ceiling` varchar(255) NOT NULL,
  `facilityinformation_exhibits` varchar(255) NOT NULL,
  `facilityinformation_largestroom` varchar(255) NOT NULL,
  `facilityinformation_tollfree` varchar(255) NOT NULL,
  `facilityinformation_imagefile` varchar(255) NOT NULL,
  `facilityinformation_reception` varchar(255) NOT NULL,
  `facilityinformation_totalsqft` varchar(255) NOT NULL,
  `facilityinformation_spacenotes` varchar(255) NOT NULL,
  `facilityinformation_theatre` varchar(255) NOT NULL,
  `facilityinformation_villas` varchar(255) NOT NULL,
  `facilityinformation_bigfile` varchar(255) NOT NULL,
  `facilityinformation_createdate` varchar(255) NOT NULL,
  `facilityinformation_numrooms` varchar(255) NOT NULL,
  `facilityinformation_banquet` varchar(255) NOT NULL,
  `facilityinformation_booths` varchar(255) NOT NULL,
  `facilityinformation_suites` varchar(255) NOT NULL,
  `facilityinformation_floorplanpath` varchar(255) NOT NULL,
  `facilityinformation_acctid` varchar(255) NOT NULL,
  `facilityinformation_classroom` varchar(255) NOT NULL,
  `facilityinformation_sleepingrooms` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`),
  KEY `cat_id` (`cat_id`),
  KEY `region_id` (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_listings_amenities_XREF` (
  `amenity_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `multi_values` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `amenity_id` (`amenity_id`),
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_listings_subcategories_XREF` (
  `listing_id` int(11) NOT NULL,
  `sub_cat_id` int(11) NOT NULL,
  `is_primary` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`),
  KEY `sub_cat_id` (`sub_cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_regions` (
  `cat_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `cat_id` (`cat_id`),
  KEY `region_id` (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_subcategories` (
  `cat_id` int(11) NOT NULL,
  `sub_cat_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `cat_id` (`cat_id`),
  KEY `sub_cat_id` (`sub_cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_coupons` (
  `sortcompany` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `mediafile` varchar(255) NOT NULL,
  `listingid` int(11) NOT NULL,
  `updatedate` varchar(255) NOT NULL,
  `mediatype` varchar(255) NOT NULL,
  `weburl` varchar(255) NOT NULL,
  `primarycontacttitle` varchar(255) NOT NULL,
  `imgpath` varchar(255) NOT NULL,
  `addr2` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `addr1` varchar(255) NOT NULL,
  `addr3` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `offertext` varchar(255) NOT NULL,
  `redeemstart` varchar(255) NOT NULL,
  `thumbfile` varchar(255) NOT NULL,
  `redeemend` varchar(255) NOT NULL,
  `catname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `poststart` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `acctstatus` varchar(255) NOT NULL,
  `couponid` int(11) NOT NULL,
  `company` varchar(255) NOT NULL,
  `subcatid` varchar(255) NOT NULL,
  `medianame` varchar(255) NOT NULL,
  `tollfree` varchar(255) NOT NULL,
  `catid` varchar(255) NOT NULL,
  `altphone` varchar(255) NOT NULL,
  `offertitle` varchar(255) NOT NULL,
  `mediaid` varchar(255) NOT NULL,
  `subcatname` varchar(255) NOT NULL,
  `mediatypeid` varchar(255) NOT NULL,
  `postend` varchar(255) NOT NULL,
  `primarycontactfullname` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `offerlink` varchar(255) NOT NULL,
  `acctid` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listingid` (`listingid`),
  KEY `couponid` (`couponid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_coupon_categories` (
  `couponcatname` varchar(255) NOT NULL,
  `couponcatid` varchar(255) NOT NULL,
  `couponid` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `couponid` (`couponid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_event_categories` (
  `eventid` int(11) NOT NULL,
  `categoryid` int(11) NOT NULL,
  `categoryname` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `categoryid` (`categoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_event_dates` (
  `eventid` int(11) NOT NULL,
  `eventdate` date NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `eventid` (`eventid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_event_images` (
  `eventid` int(11) NOT NULL,
  `mediafile` varchar(255) NOT NULL,
  `sortorder` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `eventid` (`eventid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);

$q="CREATE TABLE `sv_temp_socialmedia` (
  `listing_id` int(11) NOT NULL,
  `fieldname` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL,
  KEY `listing_id` (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"; $wpdb->query($q);


*/
