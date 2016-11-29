<?php

ini_set('display_errors','true');
error_reporting(-1);

	include("../../system/config.php");
	include("../../system/mysqli.connect.php");

$rslti_sections = mysqli_query($linki, "SELECT * FROM wpt_sections ORDER BY order_id");

if(isset($rslti_sections->num_rows)) {
	$i = 0;
	while($dsi_sections = mysqli_fetch_assoc($rslti_sections)) {
		$current_take_part_in_productid = !empty($dsi_sections['take_part_in_productid']) ? $dsi_sections['take_part_in_productid'] : '';
		if(!empty($current_take_part_in_productid)) {
			$exploded_take_part = explode(",",$current_take_part_in_productid);
			if(in_array($_POST['product_id'],$exploded_take_part)) {
				$entrys[] = array('id' => $dsi_sections['id'],'headline' => $dsi_sections['section_headline'], 'content' => utf8_encode($dsi_sections['section_content_de']));
			}
		}
		$sections[] = $dsi_sections;
		$sections[$i]['section_content_de'] = utf8_encode($sections[$i]['section_content_de']);
		// $sections[$i]['section_content_en'] = utf8_encode($sections[$i]['section_content_en']);
		$i++;
	}
	
	echo json_encode(array("entrys" => $entrys, "section_contents" => $sections));
	
}
?>