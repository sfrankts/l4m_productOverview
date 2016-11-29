<?php

ini_set('display_errors','true');
error_reporting(-1);

	include("../../system/config.php");
	include("../../system/mysqli.connect.php");

	$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : exit ;
	$template_name = isset($_GET['template_name']) ? $_GET['template_name'] : exit ;

	$rslti_check = mysqli_query($linki, "SELECT product_id, template_id FROM wpt_templates WHERE template_name = '".$template_name."' AND product_id = '".$product_id."'");
	if($rslti_check->num_rows == 1 ) {
		echo "1";
	} else {
		echo "0";
	}
	