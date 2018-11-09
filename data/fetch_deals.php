<?
	require('utils.php');
	$path = "/home/unmistakable/public_html/wp-load.php";
	include_once($path);

	$sql = "SELECT count(*) as dealCount from sv_coupons c
			LEFT JOIN sv_coupon_categories cc ON c.couponid = cc.couponid
			WHERE c.acctstatus = 'Active'";

	$statement = $dbh->query($sql);

	$page_count = $statement->fetch(PDO::FETCH_ASSOC)['dealCount'];

	$total_pages   = ceil($page_count/$item_per_page);
	$page_position = (($page_number-1) * $item_per_page);

	$sql = "SELECT c.sortcompany, c.listingid, c.weburl, c.offertitle, c.offertext,
					c.thumbfile, c.imgpath, cc.couponcatname, c.redeemstart, c.redeemend
			FROM sv_coupons c
			LEFT JOIN sv_coupon_categories cc ON c.couponid = cc.couponid
			WHERE c.acctstatus = 'Active'";

	if ($_POST['postID'] == 5134) {
		$sql .= " AND cc.couponcatid = 5 ";
	} else {
		$sql .= " AND cc.couponcatid != 5 ";
	}

	$sql .= "LIMIT " . $page_position . ", " . $item_per_page;
	$deals = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	$output = listing_item_loop($deals);
	$paginate = paginate_function($item_per_page, $page_number, $page_count, $total_pages);
	echo $paginate . $output . $paginate;
?>