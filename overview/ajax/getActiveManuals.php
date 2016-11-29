<?php

ini_set('display_errors','true');
error_reporting(-1);

	include("../../system/config.php");
	include("../../system/mysqli.connect.php");
$sql_ext = '';
if(isset($_GET['product_short_name'])) {
	$sql_ext = 'WHERE product_short_name = \''.$_GET['product_short_name'].'\'';
}
$rslti_activemanuals = mysqli_query($linki, "SELECT id, product_name, product_short_name FROM activated_manuals $sql_ext");

if(isset($rslti_activemanuals->num_rows)) {
	///$i = 0;
	while($dsi_activemanuals = mysqli_fetch_assoc($rslti_activemanuals)) {
		$activemanuals[] = $dsi_activemanuals;
		//$activemanuals[$i]['section_content_de'] = utf8_encode($activemanuals[$i]['section_content_de']);
		// $sections[$i]['section_content_en'] = utf8_encode($sections[$i]['section_content_en']);
	///	$i++;
	}
	
	echo json_encode($activemanuals);
}
?>