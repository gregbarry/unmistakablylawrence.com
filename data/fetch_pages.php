<?
require('utils.php');

$today     = date("Y-m-d");
$limit     = date("Y-m-d", strtotime(date("Y-m-d", strtotime($today)) . " + 28 day"));

$getstartbetween = (!empty($_POST['fromDate'])) ? DateTime::createFromFormat("m \/ d \/ Y", $_POST['fromDate']) : '';
$startbetween    = (!empty($_POST['fromDate'])) ? $getstartbetween->format("Y-m-d") : '';

$getendbetween = (!empty($_POST['toDate'])) ? DateTime::createFromFormat("m \/ d \/ Y", $_POST['toDate']) : '';
$endbetween    = (!empty($_POST['toDate'])) ? $getendbetween->format("Y-m-d") : '';

$fdate = (!empty($_POST['fromDate'])) ? $startbetween : $today;
$tdate = (!empty($_POST['toDate'])) ? $endbetween : $limit;

$getcat = '';

if(!empty($_POST['getcat'])){
  if ($_POST['getcat'] != 'ALL'){
    $getcat = "AND c.categoryid = '".$_POST['getcat']."'";
  }
}

//get total number of records from database
$sql = "SELECT count(DISTINCT (e.eventid + '-' + d.eventdate)) as eventCount
        FROM sv_events e, sv_event_dates d, sv_event_categories c
        WHERE d.eventid = e.eventid
        AND e.eventid = c.eventid
        AND d.is_deleted = 0
		AND e.is_deleted = 0 ". $getcat ."
		AND d.eventdate BETWEEN '".$fdate."' AND '".$tdate."'";

$statement = $dbh->query($sql);

$page_count = $statement->fetch(PDO::FETCH_ASSOC)['eventCount'];

$total_pages   = ceil($page_count/$item_per_page);
$page_position = (($page_number-1) * $item_per_page);

$sql = "SELECT DISTINCT (e.eventid + '-' + d.eventdate), e.*, d.eventdate,
		(SELECT GROUP_CONCAT(CONCAT(categoryname,':',categoryid))
		FROM sv_event_categories c WHERE c.eventid = e.eventid ) AS concats,
		(SELECT group_concat(mediafile)
		FROM sv_event_images i WHERE e.eventid = i.eventid) as event_images
		FROM sv_events e, sv_event_dates d, sv_event_categories c
		WHERE d.eventid = e.eventid
		AND e.eventid = c.eventid
		AND d.is_deleted = 0
		AND d.is_deleted = 0
		AND e.is_deleted = 0 ". $getcat ."
		AND d.eventdate BETWEEN '".$fdate."' AND '".$tdate."'
		ORDER BY d.eventdate ASC
		LIMIT ".$page_position. ", " . $item_per_page;

$statement = $dbh->query($sql);
$events = $statement->fetchAll(PDO::FETCH_OBJ);

foreach($events as $listing){

  $categorylist = '';
  $images = ($listing->event_images) ? $listing->event_images : null;
  $date1      = DateTime::createFromFormat('Y \- m \- d', $listing->eventdate);
  $event_date = $date1->format('F d, Y');
  $sd_data    = $date1->format('Ymd');
  $cat_array = explode(",", $listing->concats);

  foreach($cat_array as $ct => $cat_items) {
        $cat_item = explode(":", $cat_items);
        $cat_name = $cat_item[0];
        $cat_id   = $cat_item[1];

        $categorylist .= ", <a href='?cat=".$cat_id."'>".$cat_name."</a>";

        // set first category as default category for placeholder images
        if ($ct == 0) {
            $imageCat = $cat_name;
        }
  }

  if ($listing->startdate != NULL){
    $date2 = DateTime::createFromFormat('Y \- m \- d', $listing->startdate);
      $start_date = $date2->format('F d, Y');
  } else{
    $start_date = '';
  }
  if ($listing->enddate != NULL){
    $end_date = ' to ';
    $date2 = DateTime::createFromFormat('Y \- m \- d', $listing->enddate);
      $end_date .= $date2->format('F d, Y');
  } else{
    $end_date = '';
  }

  $eventtitle = ($listing->title != NULL) ? $listing->title : 'N/A';
  $eventlocation = ( $listing->location != NULL) ? $listing->location : '';
  $eventdescription = ( $listing->description != NULL) ? $listing->description : 'No Description Available';

  if ( $listing->website != NULL){
    $weburl = $listing->website;
    $weblink = '<a href="'.$weburl.'" target="_blank" class="site-link">Visit Website</a>';
  } else {
    $weblink = '';
  }

  $address_string = '';
  if ( $eventlocation != ''){
    $address_string .= $eventlocation .' ';
  }
  $street_address = '';
  if ( $listing->address != NULL){
    $street_address .= $listing->address .' ';
    $street_address = str_replace('\r\n',"<br>",$street_address);
  }
  $address_string .= $street_address;

  $city_address = '';
  if ( $listing->city != NULL){
    $city_address = $listing->city;
    $address_string .= $city_address .' ';
  }

  $state_address = '';
  if ( $listing->state != NULL){
    $state_address = $listing->state;
    $address_string .= $state_address .' ';
  }

  $zip_address = '';
  if ( $listing->zip != NULL){
    $zip_address = $listing->zip;
    $address_string .= $zip_address;
  }

  $address_string = str_replace(' ', '+', $address_string);

  $admission = '';
  if ( $listing->admission != NULL){
    $admission = "Admission: " . $listing->admission;
  }
  $phone = '';
  if ( $listing->phone != NULL){
    $phone = $listing->phone;
  }

  if ($listing->starttime != NULL){
    $time1 = DateTime::createFromFormat('G \: i \: s', $listing->starttime);
      $start_time = $time1->format('g:i A');
  }
  if ($listing->endtime != NULL){
    $time1 = DateTime::createFromFormat('G \: i \: s', $listing->endtime);
      $end_time = $time1->format('g:i A');
  }
  $times = '';
  if ($listing->times != NULL){
    $times = '<div class="detail-item">'.$listing->times.'</div>';
  }
  $showSrtEndTimes = true;
  if ($start_time == '12:00 AM' && $end_time == '12:00 AM' && $times != ''){
    $showSrtEndTimes = false;
  }
  $more_details = "";
  $more_details = $times;
  if ($showSrtEndTimes){
    $more_details .= ($listing->starttime != NULL) ? '<div class="detail-item">Start Time: '.$start_time.'</div>' : '';
    $more_details .= ($listing->endtime != NULL) ? '<div class="detail-item">End Time: '.$end_time.'</div>' : '';
  }
  $more_details .= ($street_address  != '') ? '<div class="detail-item">'.$street_address.'</div>' : '';
  $more_details .= (($city_address  != '') || ($state_address  != '') || ($zip_address  != '') ) ? '<div class="detail-item">'.$city_address.', '.$state_address.' '.$zip_address.'</div>' : '';
  $more_details .= '</br><div class="detail-item">'.$phone.'</div>';
  $more_details .= '</br><div class="detail-item">'.$admission.'</div>';

  $extraimages = '';

  if (count($images) > 1) {
    $i = 0;
    foreach ($images as $theImgURL){
      $extraimages .= ($i > 0) ? '<img class="event-thumb" rel="gal-'.$listing->eventid.'" src="'.$theImgURL.'" alt="">' : "";
      $i++;
    }
    $more_details .= '</br><div class="detail-item images">'.$extraimages.'</div>';
  } else if (count($images) == 1) {
    $extraimages = '<img class="event-thumb" rel="gal-'.$listing->eventid.'" src="'.$image.'" alt="">';
  }

  if (count($images) >= 1) {
    $alt = '';
    $image = '<img src="'.$images[0].'" alt="'.$alt.'">';
  } else {
    $imagePath = 'https://unmistakablylawrence.com/wp-content/themes/explore-lawrence/images/placeholders/' .$imageCat. '.jpg';
    $image = '<img src="'.$imagePath.'" alt="'.$category->categoryname.' Event">';
  }

  $event_thumb = $listing->imagefile;

  $linkName = preg_replace('/[^A-Za-z0-9]/', "", $eventtitle) . '-' . $listing->eventid;

  $eventdescription_split = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $eventdescription);

  $event_intro = array_shift($eventdescription_split);
  $event_desc  = implode('', $eventdescription_split);
  $paginate = paginate_function($item_per_page, $page_number, $page_count, $total_pages);

  $list .= '<div id="'.$linkName.'" name="'.$linkName.'" class="event-listing-item listing-'.$listing->eventid.'" data-date="'.$sd_data.'">
        <div class="listing-item-wrap">
          <div class="listing-image">
            <div class="listing-image-wrap">
              '.makeImage($event_thumb).'
            </div>
          </div>
          <div class="listing-content-area">
            <div class="listing-title">
              <h3>'.$eventtitle.'</h3>
            </div>
            <div class="listing-content">
              '.$event_date.'
              <br />
              '.$eventlocation.'
              '.$weblink.'
              <div class="listing-cats">Categories: '.substr($categorylist,1).'</div>
              <div class="listing-details">'.$event_intro.'</div>
              <div class="more-details" style="display: none;"><p>'.$event_desc . "</p>" . $more_details.'</div>
            </div>
            <div class="buttons">
              <a href="" class="details-link more button">Details</a>
              <a href="" class="details-link less button" style="display: none;" >Show Less</a>
              <a href="https://www.google.com/maps/dir/Current+Location/'.$address_string.'" target="_blank" class="directions-link button">Map It</a>
            </div>
          </div>
        </div>
      </div>';
}

echo $paginate . $list . $paginate;
?>