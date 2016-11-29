<?php

ini_set('display_errors','true');
error_reporting(-1);

	include("../../system/config.php");
	include("../../system/mysqli.connect.php");

$rslti_sections = mysqli_query($linki, "SELECT id,product_id,section_headline,take_part_in_productid FROM wpt_sections ORDER BY order_id");

if(isset($rslti_sections->num_rows)) {
	// $i = 0;
	while($dsi_sections = mysqli_fetch_assoc($rslti_sections)) {
		$sections[] = $dsi_sections;
		// $sections[$i]['section_content_de'] = utf8_encode($sections[$i]['section_content_de']);
		// $sections[$i]['section_content_en'] = utf8_encode($sections[$i]['section_content_en']);
		// $i++;
	}
	
	echo json_encode($sections);
}
?>