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

	$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : exit ;
	$template_name = isset($_GET['template_name']) ? $_GET['template_name'] : exit ;
	$lang = isset($_GET['template_lang']) ? $_GET['template_lang'] : 'de';
	
	$rslti_content = mysqli_query($linki, "SELECT product_id, template_id,template_content_de FROM wpt_templates WHERE template_name = '".$template_name."' AND product_id = '".$product_id."'");
	if($rslti_content->num_rows == 1 ) {
		$dsi_content = mysqli_fetch_assoc($rslti_content);
			$template_id = $dsi_content['template_id'];
			$content = $dsi_content['template_content_'.$lang];
			//echo nl2br($content);
		
		if(preg_match_all("/##([a-z_]*?|[a-z_]*?:[a-z_]*?)##/im",$content,$matches_ident)) {
			
			
			
			if(is_array($matches_ident[1])) {
				foreach($matches_ident[1] as $current_match) {
					
					if(preg_match("/:/im",$current_match)) {
						
						
						
						$mysqls[] = $current_match;
						if(list($table,$row) = explode(":", $current_match)) {
							// echo $row;
							
							//echo "SELECT $row FROM ".$table." WHERE product_id = '".$product_id."'";
							$rslti_ext = mysqli_query($linki, "SELECT $row FROM ".$table." WHERE product_id = '".$product_id."'");
							if($rslti_ext->num_rows == 1) {
								$dsi_ext = mysqli_fetch_assoc($rslti_ext);
								$returnVal = array_key_exists($row,$dsi_ext) ? $dsi_ext[$row] : $current_match;
								
								if($returnVal != $current_match) {
									$ident_contents[$current_match] = $returnVal;
									// $results['final_identifier'] = 1;
									foreach($mysqls as $k => $v) {
										if($v == $current_match) unset($mysqls[$k]);
									}
								}
							} else {
								exit;
							}
						} else if(list($table,$row,$pos) = explode(":", $current_match)) {
							
						}
					} else {
						
						$rslti_c2 = mysqli_query($linki, "SELECT * FROM wpt_product_properties WHERE identifier = '".$current_match."' AND product_id = '".$product_id."'");
						if($rslti_c2->num_rows == 1) {
							$dsi_c2 = mysqli_fetch_assoc($rslti_c2);
							//var_dump($current_match);
							$htmls[$current_match] = utf8_encode($dsi_c2['content_'.$lang]);
							
							if(preg_match_all("/##([a-z_]*?)|([a-z_]*?:[a-z_]*?)|([a-z_]*?:[a-z_]*?:[a-z_]*?)##/im",$dsi_c2['content_'.$lang],$matches_identsub)) {
								if(isset($matches_identsub[1])) {
									var_dump($matches_identsub);
									if(is_array($matches_identsub[1])) {
										foreach($matches_identsub[1] as $identsub_key => $identsub_value) {
											$rslti_identsub_property = mysqli_query($linki, "SELECT * FROM wpt_product_properties WHERE identifier = '".$identsub_value."'");
											if($rslti_identsub_property->num_rows == 1) {
												$dsi_identsub_property = mysqli_fetch_assoc($rslti_identsub_property);
												
												//var_dump($dsi_identsub_property['content_'.$lang]);
												
												if(preg_match("/:/im",$dsi_identsub_property['content_'.$lang],$m_5)) {
													//echo $dsi_identsub_property['content_'.$lang];
													$exploded_ext_2 = explode(':',$dsi_identsub_property['content_'.$lang]);
														$table = isset($exploded_ext_2[0]) ? $exploded_ext_2[0] : '';
														$row = isset($exploded_ext_2[1]) ? $exploded_ext_2[1] : '';
														$pos = isset($exploded_ext_2[2]) ? $exploded_ext_2[2] : '';
												$rslti_ext_2 = mysqli_query($linki, "SELECT ".$row." FROM ".$table." WHERE product_id = '".$product_id."'");
												if($rslti_ext_2->num_rows == 1) {
													$dsi_ext_2 = mysqli_fetch_assoc($rslti_ext_2);
													
													if(array_key_exists($row,$dsi_ext_2)) {
														$exploded_ext_3 = explode(";",$dsi_ext_2[$row]);
														if(isset($exploded_ext_3[$pos])) {
															$results['final_identifier'][$identsub_value] = utf8_encode($exploded_ext_3[$pos]);
														}
													}
													
												} else {
													
												}
													
													// $num_rows[] = $rslt_ext_2->num_rows;
												} else {
												// echo "<pre>###<br>";
													
													$results['final_identifier'][$identsub_value] = utf8_encode($dsi_identsub_property['content_'.$lang]);
												}
												
											} else {
												// exit;
											}
										}
									}
								}
								
							} else {
								$contents[$current_match] = utf8_encode($dsi_c2['content_'.$lang]);
							}
							
							//$current_ident_content = $dsi_c2['content_'.$lang];
							//$ident_contents[$current_match] = $current_ident_content;
						} else {
							
						}
						// $idents[] = $current_match;
					}
					
					
				}
				if(is_array($ident_contents)) {
					
					// 'contents_sub' => $contents_sub,
					// 'identsub_contents' => $identsub_contents,
					// 'matches_identsub' => $matches_identsub,
					// 'idents' => $idents,
					// 'matches_ident' => $matches_ident[1], 'mysql' => $mysqls,
					
					foreach($ident_contents as $k_ident_content => $val_ident_content) {
						$results['final_identifier'][$k_ident_content] = utf8_encode($val_ident_content);
						unset($ident_contents[$k_ident_content]);
					}
					
					foreach($matches_ident[1] as $k_m_ident => $val_m_ident) {
						if(array_key_exists($val_m_ident, $results['final_identifier'])) {
							unset($matches_ident[1][$k_m_ident]);
						}
					}
					
					# $htmls['highlights_prosa'] = preg_replace();
					
					$found = array();
					
					$matches_html = $matches_ident[1];
					
					echo "<pre>";
					$final_results = $results['final_identifier'];
					// var_dump($final_results);
					
					foreach($htmls as $html_key => $current_html) {
						if(preg_match_all("/(##([a-z_]*?)##)/im",$current_html,$matches_html)) {
							echo "<pre>";
							// var_dump($matches_html);
							echo "</pre>";
							
							if(isset($matches_html[2])) {
								if(is_array($matches_html[2])) {
									foreach($matches_html[2] as $multi_match_item) {
										echo "M: ".$multi_match_item."<br>";
										if(array_key_exists($multi_match_item,$final_results)) {
											$htmls[$html_key] = (preg_replace("/(##".$multi_match_item."##)/im", utf8_decode($final_results[$multi_match_item]),$htmls[$html_key]));
										}
									}
									var_dump($htmls[$html_key]);
								}
							}
						}
						echo "<pre>";
						//var_dump($current_html);
						echo "</pre>";
					}
					
					//foreach($matches_html as $html_ident) {
					//	if(array_key_exists($html_ident,$htmls)) {
					//		$found[] = '##'.$html_ident.'## | '.$htmls[$html_ident];
					//		$content = (preg_replace("/(##".$html_ident."##)/im", utf8_decode($htmls[$html_ident]),$content));
					//	}
					//	if(in_array($html_ident,))
					//}
					
					echo "<pre>";
					// var_dump(utf8_encode($content));
					echo "</pre>";
					
					//echo json_encode(array('html_content' => utf8_encode($content), 'matches' => $matches_ident[1]));
					//echo json_encode(array('content' => utf8_encode($content), 'matches_ident' => $matches_ident[1], 'ident_contents' => $ident_contents, 'contents' => $contents, 'htmls' => $htmls, 'final_l' => count($results['final_identifier']), 'final' => $results['final_identifier'], 'found' => $found));
				} else {
					##echo json_encode(array('content' => utf8_encode($content), 'matches_ident' => $matches_ident[1],'idents' => $idents, 'mysql' => $mysqls));
				}
			}
		}
		
		/*	
		if(preg_match_all("/##([a-z_]*?)##/im",$content,$matches)) {
			
			//has template_idents
				$template_idents = $matches[1];
				
				foreach($template_idents as $key => $ident) {
					echo $ident."<br>";
					
					
					if($ident == "powerconsumption_htmlblock") {
											
						$rslt_properties = mysqli_query($linki,"SELECT content_de FROM wpt_product_properties WHERE identifier = '".$ident."' AND product_id = '".$product_id."'");
							$dsi_properties = mysqli_fetch_assoc($rslt_properties);
							$ident_content['de'] = $dsi_properties['content_de'];
						/*
						if(preg_match_all("/##([a-z_]*?)##/im",$ident_content['de'],$matches_ident)) {
							echo "<pre>";
							var_dump($matches_ident);
							echo "</pre>";
						} else 
						*/
/*							
						if(preg_match_all("/##([a-z_]*?|[a-z_]*?:[a-z_]*?)##/im",$ident_content['de'],$matches_ident)) {
							if(is_array($matches_ident[1])) {
								
								foreach($matches_ident[1] as $m_ident_sub) {
									if(preg_match("/:/im",$m_ident_sub)) {
										
									} else {
										
					$rslt_properties = mysqli_query($linki, "SELECT * FROM wpt_product_properties WHERE product_id = '".$product_id."' AND template_id = '".$template_id."' AND identifier = '".$ident."'");
					if($rslt_properties->num_rows == 1) {
						$dsi_properties = mysqli_fetch_assoc($rslt_properties);
							$ident_prop_content['de'] = $dsi_properties['content_de'];
						## $result[$ident] = nl2br($ident_content['de']);
						if(preg_match_all("/##([a-z_]*?)##/im",$ident_prop_content['de'],$matches_ident)) {
							
						} else {
							$result[$ident] = $ident_prop_content['de'];
						}
					}
										
										
									}
								}
							}
							
						}
											
					}
					
					
					
					
					$rslt_properties = mysqli_query($linki, "SELECT * FROM wpt_product_properties WHERE product_id = '".$product_id."' AND template_id = '".$template_id."' AND identifier = '".$ident."'");
					if($rslt_properties->num_rows == 1) {
						$dsi_properties = mysqli_fetch_assoc($rslt_properties);
							$ident_content['de'] = $dsi_properties['content_de'];
						## $result[$ident] = nl2br($ident_content['de']);
						if(preg_match_all("/##([a-z_]*?)##/im",$ident_content['de'],$matches_ident)) {
							
							
							//  && !preg_match("/##([a-z_].*):([a-z_].*)##/im",$ident_content['de'])
							// 
							//echo $ident."<br>";
							//echo "###<br>";
							
								//echo "<pre>";
								if(is_array($matches_ident[1])) {
									foreach($matches_ident[1] as $m_sub) {
										echo "> $m_sub<br>";
										
										
										
										
											$rslt_ident_sub_contents = mysqli_query($linki,"SELECT content_de FROM wpt_product_properties WHERE identifier = '".$m_sub."' AND product_id = '".$product_id."'");
											if($rslt_ident_sub_contents->num_rows == 1) {

												$dsi_ident_sub_contents = mysqli_fetch_assoc($rslt_ident_sub_contents);
												$ident_sub_content['de'] = utf8_encode($dsi_ident_sub_contents['content_de']);

												if(preg_match_all("/:/im",$ident_sub_content['de'],$m_d)) {
													if(list($table,$row,$pos) = explode(':',$ident_sub_content['de'])) {

														$rslti_ext = mysqli_query($linki, "SELECT ".$row." FROM ".$table." WHERE product_id = '".$product_id."'");
														//echo $m_sub."<br>";
														if($rslti_ext->num_rows == 1) {
															$dsi_ext = mysqli_fetch_assoc($rslti_ext);
															$result[$m_sub] = utf8_encode($dsi_ext[$row]);
														}

													} else if(list($table,$row) = explode(':',$ident_sub_content['de'])) {

														$rslti_ext = mysqli_query($linki, "SELECT ".$row." FROM ".$table." WHERE product_id = '".$product_id."'");
														//echo $m_sub."<br>";
														if($rslti_ext->num_rows == 1) {
															$dsi_ext = mysqli_fetch_assoc($rslti_ext);
															$result[$m_sub] = utf8_encode($dsi_ext[$row]);
														}

													}
												}
												//$result[$m_sub] = $dsi_ident_sub_contents['content_de'];


											} else {

												echo "$m_sub NOTHING!";
											}
										
									}
								}
								//echo "</pre>";
							
							foreach($matches_ident as $match_content) {
							//	echo "<pre>";
							//	print_r($match_content);
							//	echo "</pre>";
							}
						}
						// echo $content_de;
					} else {
						if(preg_match("/:/im",$ident)) {
							// search in db
							if(list($table,$row) = explode(':',$ident)) {
								//echo $table."\r\n";
								//echo $row."\r\n";
								$rslti_ext = mysqli_query($linki, "SELECT ".$row." FROM ".$table." WHERE product_id = '".$product_id."'");
								if($rslti_ext->num_rows == 1) {
									$dsi_ext = mysqli_fetch_assoc($rslti_ext);
									$result[$ident] = utf8_encode($dsi_ext[$row]);
								}
							} else {
								$result[$ident] = 'NOTHING!';
							}
							
						} else {
							$result[$ident] = 'NOTHING!';
						}
					}
				}
				if(is_array($result)) {
					
				}
			//echo "<pre>";
			//var_dump($template_idents);
			//echo "</pre>";	
			}
		*/	
	} else {
		echo "0";
	}
	