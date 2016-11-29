<?php

ini_set('display_errors','true');
error_reporting(-1);

// ##echo json_encode(array('content' => utf8_encode($content), 'matches_ident' => $matches_ident[1],'idents' => $idents, 'mysql' => $mysqls, 'ident_contents' => $ident_contents, 'contents' => $contents, 'matches_identsub' => $matches_identsub));

$content = '';
	$contents = '';
$idents = '';
	$ident_contents = '';
		$matches_identsub = '';
$mysqls = '';


	include("../../system/config.php");
	include("../../system/mysqli.connect.php");

function getProperties($mysqlData_arr) {
	if(isset($mysqlData_arr) && is_array($mysqlData_arr)) {
		 $sql = $mysqlData_arr["type"]." ".$mysqlData_arr["row"]." FROM ".$mysqlData_arr["table"]." ".$mysqlData_arr["condition"];
		// echo $sql."<br>";
		$rslti = mysqli_query($mysqlData_arr["link"],$sql);
		if($rslti->num_rows ==1) {
			$dsi = mysqli_fetch_assoc($rslti);
			return utf8_encode($dsi[$mysqlData_arr["row"]]);
			//return $rslti->num_rows;
		} else {
			return "0:".$mysqlData_arr["identifier"];
		}
		// var_dump(mysqli_error($mysqlData_arr["link"]));
	} else {
		return "0:".$mysqlData_arr["identifier"];
	}
}
echo "<pre>";
	$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : exit ;
	$template_name = isset($_GET['template_name']) ? $_GET['template_name'] : exit ;
	$lang = isset($_GET['template_lang']) ? $_GET['template_lang'] : 'de';
	
	$rslti_content = mysqli_query($linki, "SELECT product_id, template_id,template_content_de FROM wpt_templates WHERE template_name = '".$template_name."' AND product_id = '".$product_id."'");
	if($rslti_content->num_rows == 1 ) {
		$dsi_content = mysqli_fetch_assoc($rslti_content);
			$template_id = $dsi_content['template_id'];
			$content = $dsi_content['template_content_'.$lang];
			//echo nl2br($content);
			if(preg_match_all("/##([a-z_]*?|[a-z_]*?:[a-z_]*?|[a-z_]*?:[a-z_]*?:[a-z_]*?)##/im",$content,$match_template_identifiers)) {
				if(isset($match_template_identifiers[1]) && is_array($match_template_identifiers[1])) {
					foreach($match_template_identifiers[1] as $current_match_identifier) {
						$identifiers_to_resolve[] = $current_match_identifier;
						
						$sql_prepare = array(
							'type' => 'SELECT',
							'row' => 'content_'.$lang,
							'table' => 'wpt_product_properties',
							'identifier' => $current_match_identifier,
							'condition' => "WHERE identifier = '".$current_match_identifier."' AND template_id = '".$template_id."'",
							'link' => $linki
						);
						
						$identifier_content = getProperties($sql_prepare);
						if(preg_match("/^0:.*/i", $identifier_content)) {
							if(preg_match("/^0:([a-z_]*?:[a-z_]*?)/i",$identifier_content)) {
								$explode_3 = explode(":",$current_match_identifier);
								list($table,$row) = $explode_3;
								
								$sql_prepare = array(
									'type' => 'SELECT',
									'row' => $row,
									'table' => $table,
									'identifier' => $current_match_identifier,
									'condition' => "WHERE product_id = '".$product_id."'",
									'link' => $linki
								);
								
								$identifier_sub_content = getProperties($sql_prepare);
								if($identifier_sub_content != "0:".$current_match_identifier) {
									$identifier_contents[$current_match_identifier] = $identifier_sub_content;
								}
								
							}
						} else {//getProductPropertiesContentMySQLI('wpt_product_properties',$current_match_identifier,"content_".$lang,$product_id,$template_id,$lang,$linki);
							
							if(preg_match_all("/##([a-z_]*?|[a-z_]*?:[a-z_]*?|[a-z_]*?:[a-z_]*?:[a-z_]*?)##/im",$identifier_content,$match_template_identifier_content)) {
								// var_dump();
								if(isset($match_template_identifier_content[1]) && is_array($match_template_identifier_content[1])) {
									foreach($match_template_identifier_content[1] as $match_template_identifier_content_key => $match_template_identifier_content_val) {
										echo $match_template_identifier_content_val."<br>";
										
										$sql_prepare = array(
											'type' => 'SELECT',
											'row' => 'content_'.$lang,
											'table' => 'wpt_product_properties',
											'identifier' => $current_match_identifier,
											'condition' => "WHERE identifier = '".$match_template_identifier_content_val."' AND template_id = '".$template_id."'",
											'link' => $linki
										);
										
										if(!in_array($match_template_identifier_content_val,$identifier_contents)) {
											$identifier_sub_sub_content = getProperties($sql_prepare);
											var_dump($identifier_sub_sub_content);
										}
									}
								}
							} else {
								$identifier_contents[$current_match_identifier] = $identifier_content;

							}
						}
					}
					
					foreach($identifier_contents as $i_contents_key => $i_contents_val) {
						
						echo "<br>###<br>";echo $i_contents_key."<br>";
?><textarea style="width: 80%; height: 300px;"><?php echo $i_contents_val; ?></textarea><br><?php
					}
						
				}
				echo "<pre>";
				var_dump(count($identifier_contents));
				echo "</pre>";
			}
	} else {
		echo "0";
	}
	