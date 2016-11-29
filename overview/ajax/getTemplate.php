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

function hasTemplateIdents($content,$req='count') {
	
$regex_idents = array(
	'(?![:])[a-z_]*?',
	'[a-z0-9_]*?:[a-z0-9_]*?',
	'[a-z0-9_]*?:[a-z0-9_]*?:\\d+'
);
	
	//var_dump($content);
	
	if(preg_match_all("/##(".implode("|",$regex_idents).")##/im",$content,$matches)) {
		if(isset($matches) && is_array($matches[1])) {
			switch($req) {
				case 'count':
					return count($matches[1]);
				break;
					
				case 'idents':
					foreach($matches[1] as $ident) {
						$idents_arr[] = $ident;
					}
					if(is_array($idents_arr)){
						return $idents_arr;
					}
				break;
			}
			
		} else {
			return 0;
		}
	} else {
		return "nomatch";
	}
	
}

function getTemplateIdents($content,$regex_idents) {
	if(preg_match_all("/##(".implode("|",$regex_idents).")##/im",$content,$matches)) {
		if(isset($matches) && is_array($matches[1])) {
			return count($matches[1]);
		} else {
			return 0;
		}
	}
}

function getIdentContent($mysqlData_arr,$product_id) {
	//var_dump($mysqlData_arr["identifier"]);
	//if($mysqlData_arr["identifier"] == "wp_dd_products:dimensions:1") {
		//var_dump("1");
	//}
	if( preg_match("/^([a-z0-9_]*?):([a-z0-9_]*?)$/im",$mysqlData_arr["identifier"],$matches) ) {
		$type='mysql';
		
	} else if( preg_match("/([a-z0-9_]*?):([a-z0-9_]*?):(\\d+)/im",$mysqlData_arr["identifier"],$matches) ) {
		$type='mysql:3';
		// var_dump(3);
	} else if( preg_match("/^((?![:])[a-z0-9_]*?)$/im",$mysqlData_arr["identifier"],$matches) ) {
		$type='normal';
	} else { 
		return $mysqlData_arr["identifier"].":0";
	}
	
	switch($type) {
			case 'normal':
			
			$rslti = mysqli_query($mysqlData_arr["link"], $mysqlData_arr["type"]." ".$mysqlData_arr["row"]." FROM ".$mysqlData_arr["table"]." ".$mysqlData_arr["condition"]);
			if($rslti->num_rows == 1) {
				$dsi = mysqli_fetch_assoc($rslti);
				return utf8_encode($dsi[$mysqlData_arr["row"]]);
			} else {
				return "0:".$mysqlData_arr["identifier"];
			}
			
			break;
			
			case 'mysql':
			
			$sql_prepare = array( 
				'type' => 'SELECT',
				'row' => $matches[2],
				'table' => $matches[1],
				'identifier' => $mysqlData_arr["identifier"],
				'condition' => "WHERE product_id = '".$product_id."'",
				'link' => $mysqlData_arr["link"]
			);
			$mysqlData_arr = $sql_prepare;
			
			$rslti = mysqli_query($mysqlData_arr["link"], $mysqlData_arr["type"]." ".$mysqlData_arr["row"]." FROM ".$mysqlData_arr["table"]." ".$mysqlData_arr["condition"]);
			if($rslti->num_rows == 1) {
				$dsi = mysqli_fetch_assoc($rslti);
				return utf8_encode($dsi[$mysqlData_arr["row"]]);
			} else {
				return 0;
			}			
			break;
			
			case 'mysql:3':
			
			$sql_prepare = array( 
				'type' => 'SELECT',
				'row' => $matches[2],
				'table' => $matches[1],
				'identifier' => $mysqlData_arr["identifier"],
				'condition' => "WHERE product_id = '".$product_id."'",
				'link' => $mysqlData_arr["link"]
			);
			$mysqlData_arr = $sql_prepare;
			$rslti = mysqli_query($mysqlData_arr["link"], $mysqlData_arr["type"]." ".$mysqlData_arr["row"]." FROM ".$mysqlData_arr["table"]." ".$mysqlData_arr["condition"]);
			if($rslti->num_rows == 1) {
				$dsi = mysqli_fetch_assoc($rslti);
					$rowData = $dsi[$mysqlData_arr["row"]];
					$exploded = explode(";",$rowData);
				
				return utf8_encode($exploded[$matches[3]]);
			} else {
				return 0;
			}			
			break;
	}
	/*
	if( preg_match("/^([a-z_]*?):([a-z_]*?)$/im",$mysqlData_arr["identifier"],$matches) ) {
		if(isset($matches[1]) && !empty($matches[1]) && isset($matches[2]) && !empty($matches[2])) {
					### WENN 
					## IdentContent enthält ^Word+_:Word+_$
					##
					## RETURN: Array(Table,Row);

					#return array('table' => $matches[1], 'row' => $matches[2]);
			
			$rslti = mysqli_query($mysqlData_arr["link"], $mysqlData_arr["type"]." ".$mysqlData_arr["row"]." FROM ".$mysqlData_arr["table"]." ".$mysqlData_arr["condition"]);
			if($rslti->num_rows == 1) {
				return 1;
			}
		}
		// return "2";
	} else if( preg_match("/^([a-z_]*?):([a-z_]*?):([a-z_]*?)$/im",$mysqlData_arr["identifier"],$matches) ) {
		if(isset($matches[1]) && !empty($matches[1]) && isset($matches[2]) && !empty($matches[2]) && isset($matches[3]) && !empty($matches[3])) {
					### WENN 
					## IdentContent enthält ^Word+_:Word+_$
					##
					## RETURN: Array(Table,Row);

					return array('table' => $matches[1], 'row' => $matches[2], 'pos' => $matches[3]);
		}
		//return "3";
	} else {
		$rslti = mysqli_query($mysqlData_arr["link"], $mysqlData_arr["type"]." ".$mysqlData_arr["row"]." FROM ".$mysqlData_arr["table"]." ".$mysqlData_arr["condition"]);
		if($rslti->num_rows == 1) {
			$dsi = mysqli_fetch_assoc($rslti);
			if(array_key_exists($mysqlData_arr["row"],$dsi)) {
				return $dsi[$mysqlData_arr["row"]];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	/*
	$rslti = mysqli_query($mysqlData_arr["link"], $mysqlData_arr["type"]." ".$mysqlData_arr["row"]." FROM ".$mysqlData_arr["table"]." ".$mysqlData_arr["condition"]);
	if($rslti->num_rows == 1) {
		$dsi = mysqli_fetch_assoc($rslti);
		if(array_key_exists($mysqlData_arr["row"],$dsi)) {
			
			if( preg_match("/^([a-z_]*?):([a-z_]*?)$/im",$mysqlData_arr["identifier"],$matches) ) {
				if(isset($matches[1]) && !empty($matches[1]) && isset($matches[2]) && !empty($matches[2])) {
					### WENN 
					## IdentContent enthält ^Word+_:Word+_$
					##
					## RETURN: Array(Table,Row);

					return array('table' => $matches[1], 'row' => $matches[2]);

					/*
					echo "Table: ".$matches[1];
					echo "Row: ". $matches[2];

					$sql_prepare = array(
						'type' => 'SELECT',
						'row' => $row,
						'table' => $table,
						'identifier' => $current_match_identifier,
						'condition' => "WHERE product_id = '".$product_id."'",
						'link' => $linki
					);
					
				}
		} else if( preg_match("/^([a-z_]*?):([a-z_]*?):(\\d+)$/im",$mysqlData_arr["identifier"],$matches) ) {
			return array('table' => $matches[1], 'row' => $matches[2], 'pos' => $matches[3]);
		} else {
			
			return utf8_encode($dsi[$mysqlData_arr["row"]]);
		}
		
	} else {
		#if(preg_match("/([a-z_]*?):([a-z_]*?)/is",$mysqlData_arr["identifier"],$matches)) {
		#	var_dump($matches);
		#} else {
		if( preg_match("/^([a-z_]*?):([a-z_]*?)$/im",$mysqlData_arr["identifier"],$matches) ) {
			if(isset($matches[1]) && !empty($matches[1]) && isset($matches[2]) && !empty($matches[2])) {
				### WENN 
				## IdentContent enthält ^Word+_:Word+_$
				##
				## RETURN: Array(Table,Row);
				
				return array('table' => $matches[1], 'row' => $matches[2]);
				
				/*
				echo "Table: ".$matches[1];
				echo "Row: ". $matches[2];
				
				$sql_prepare = array(
					'type' => 'SELECT',
					'row' => $row,
					'table' => $table,
					'identifier' => $current_match_identifier,
					'condition' => "WHERE product_id = '".$product_id."'",
					'link' => $linki
				);
				
			}
		} else if( preg_match("/^([a-z_]*?):([a-z_]*?):(\\d+)$/im",$mysqlData_arr["identifier"],$matches) ) {
			return array('table' => $matches[1], 'row' => $matches[2], 'pos' => $matches[3]);
		} else {
			return $mysqlData_arr["identifier"].":0<br>";
		}
		#}
	}
	*/
}

	$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : exit ;
	$template_name = isset($_GET['template_name']) ? $_GET['template_name'] : exit ;
	$lang = isset($_GET['template_lang']) ? $_GET['template_lang'] : 'de';
	
	$rslti_content = mysqli_query($linki, "SELECT product_id, template_id,template_content_de FROM wpt_templates WHERE template_name = '".$template_name."' AND product_id = '".$product_id."'");
	if($rslti_content->num_rows == 1 ) {
		$dsi_content = mysqli_fetch_assoc($rslti_content);
		$template_id = $dsi_content['template_id'];
		$content = utf8_encode($dsi_content['template_content_'.$lang]);
		$count_idents = hasTemplateIdents($content);
		##
		# write to Global Variable
		$_L4M['template_ident_count'] = $count_idents;
		##
		if($count_idents > 0) {
			$tmp_idents = hasTemplateIdents($content,'idents');
			if(count($tmp_idents) == $_L4M['template_ident_count']) {
				$_L4M['template_idents'] = $tmp_idents;
				$_L4M['count_ok'] = true;
				
				foreach($_L4M['template_idents'] as $template_ident_key => $current_ident) {
						$sql_prepare = array(
							'type' => 'SELECT',
							'row' => 'content_'.$lang,
							'table' => 'wpt_product_properties',
							'identifier' => $current_ident,
							'condition' => "WHERE identifier = '".$current_ident."' AND template_id = '".$template_id."'",
							'link' => $linki
						);
					
						$ident_content = getIdentContent($sql_prepare,$product_id);
						$_L4M['ident_contents'][$current_ident] = $ident_content;
						
				}
				$ident_content_keys = array_keys($_L4M['ident_contents']);
				
				$tmp_content = preg_replace("/##(".$ident_content_keys[0].")##/i", $_L4M['ident_contents'][$ident_content_keys[0]], $content);
				// var_dump(hasTemplateIdents($_L4M['ident_contents']['satellite_characteristics_htmlblock']));
				for($i = 1; $i < count($ident_content_keys); $i++) {
					
					
					if(hasTemplateIdents($_L4M['ident_contents'][$ident_content_keys[$i]]) > 0) {
						
						
						
						$tmp_idents = hasTemplateIdents($_L4M['ident_contents'][$ident_content_keys[$i]], 'idents');
						//var_dump($ident_content_keys[$i]);
						// var_dump($_L4M['ident_contents'][$ident_content_keys[$i]]);
						for($i_2 = 0; $i_2 < count($tmp_idents); $i_2++) {
							
							$sql_prepare = array(
								'type' => 'SELECT',
								'row' => 'content_'.$lang,
								'table' => 'wpt_product_properties',
								'identifier' => $tmp_idents[$i_2],
								'condition' => "WHERE identifier = '".$tmp_idents[$i_2]."' AND template_id = '".$template_id."'",
								'link' => $linki
							);
							
							$current_ident_content = getIdentContent($sql_prepare,$product_id);
							if(!in_array($tmp_idents[$i_2],$_L4M['template_idents'])) {
								$_L4M['template_idents'][] = $tmp_idents[$i_2];
								$_L4M['ident_contents'][$tmp_idents[$i_2]] = $current_ident_content;
							}
							$_L4M['ident_contents'][$ident_content_keys[$i]] = preg_replace("/##(".$tmp_idents[$i_2].")##/i",$current_ident_content,$_L4M['ident_contents'][$ident_content_keys[$i]]);
						}
						// var_dump($tmp_idents);
					} else {
						if($ident_content_keys[$i] == 'satellite_characteristics_htmlblock') {
						//var_dump("ELSE: ".$ident_content_keys[$i]);
						//var_dump($ident_content_keys[$i]);
						//var_dump(hasTemplateIdents($_L4M['ident_contents'][$ident_content_keys[$i]]));
						}
					}
					$tmp_content = preg_replace("/##(".$ident_content_keys[$i].")##/i", $_L4M['ident_contents'][$ident_content_keys[$i]], $tmp_content);
					
					

				}
				
				echo json_encode(array('content' => $tmp_content));
				//foreach($_L4M['ident_contents'] as $ident_name => $current_ident_content) {
				//	$tmp_content = preg_replace("/##(".$ident_name.")##/im",$current_ident_content);
				//}
				#var_dump($tmp_content);
			}
		}
/*		
		$dsi_content = mysqli_fetch_assoc($rslti_content);
			$template_id = $dsi_content['template_id'];
			$content = $dsi_content['template_content_'.$lang];
		
			$count_idents = hasTemplateIdents($content);
			if($count_idents > 0) {
				$_L4M['template_ident_count'] = $count_idents;
				
				$tmp_idents = hasTemplateIdents($content,'idents');
				
				if(count($tmp_idents) == $_L4M['template_ident_count']) {
					$_L4M['template_idents'] = $tmp_idents;
					
					foreach($_L4M['template_idents'] as $template_ident_key => $current_ident) {
						$sql_prepare = array(
							'type' => 'SELECT',
							'row' => 'content_'.$lang,
							'table' => 'wpt_product_properties',
							'identifier' => $current_ident,
							'condition' => "WHERE identifier = '".$current_ident."' AND template_id = '".$template_id."'",
							'link' => $linki
						);
						
						$ident_content = getIdentContent($sql_prepare);
						if(is_array($ident_content) && array_key_exists('table',$ident_content) && array_key_exists('row',$ident_content)){
							
							$table = $ident_content['table'];
							$row = $ident_content['row'];
							
							$sql_prepare = array(
								'type' => 'SELECT',
								'row' => $row,
								'table' => $table,
								'identifier' => $current_ident,
								'condition' => "WHERE product_id = '".$product_id."'",
								'link' => $linki
							);
							
							$_L4M['ident_contents'][$current_ident] = getIdentContent($sql_prepare);
						} else {
							$count_sub = hasTemplateIdents($ident_content);
							if($count_sub > 0) {
								$sub_idents = hasTemplateIdents($ident_content,'idents');
								foreach($sub_idents as $current_sub_ident) {
									if(!array_key_exists($current_sub_ident,$_L4M['ident_contents'])) {
										echo $current_sub_ident."<br>";
										
						$sql_prepare = array(
							'type' => 'SELECT',
							'row' => 'content_'.$lang,
							'table' => 'wpt_product_properties',
							'identifier' => $current_sub_ident,
							'condition' => "WHERE identifier = '".$current_sub_ident."' AND template_id = '".$template_id."'",
							'link' => $linki
						);
						$ident_sub_content = getIdentContent($sql_prepare);
										
						if(is_array($ident_sub_content) && array_key_exists('table',$ident_sub_content) && array_key_exists('row',$ident_sub_content)){
							echo "tablerow<br>";
						} else if(is_array($ident_sub_content) && array_key_exists('table',$ident_sub_content) && array_key_exists('row',$ident_sub_content) && array_key_exists('pos',$ident_sub_content)) {
							echo "tablerowpos<br>";
						}
										
										echo "SUB;<br>";
										var_dump($ident_sub_content);
										
										
										
									}
								}
								
							} else {
								$_L4M['ident_contents'][$current_ident] = $ident_content;
							}
						}
						
					}
				}
				
			}
			*/
			//echo nl2br($content);
			// var_dump($_L4M);
	} else {
		echo "0";
	}
	