<?php
$path = $_SERVER['DOCUMENT_ROOT']."/wp-load.php";
include_once($path);
require_wp_db();
$subcatsJson = '{"subcatsJson":[';
$cat_id = (!empty($_REQUEST['cat_id'])) ? $_REQUEST['cat_id'] : 'NULL';
$subcats = $wpdb->get_results("SELECT * FROM `sv_subcategories` WHERE is_deleted = 0 AND cat_id = IFNULL(".$cat_id.",cat_id) ORDER BY name");
foreach($subcats as $subcat){
	$subcatsJson.= '{"cat_id":"'.$subcat->cat_id.'", "sub_cat_id":"'.$subcat->sub_cat_id.'", "name":"'.$subcat->name.'"},';
}
$subcatsJson = rtrim($subcatsJson, ",");
$subcatsJson.= ']}';
echo $subcatsJson;