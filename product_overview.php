<?php

include("system/config.php");
include("system/mysqli.connect.php");

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Product_overview</title>

<style type="text/css">

 /* Style the list */
ul.tab {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Float the list items side by side */
ul.tab li {float: left;}

/* Style the links inside the list items */
ul.tab li a {
    display: inline-block;
    color: black;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    transition: 0.3s;
    font-size: 17px;
}

/* Change background color of links on hover */
ul.tab li a:hover {background-color: #ddd;}

/* Create an active/current tablink class */
ul.tab li a:focus, .active {background-color: #ccc;}

/* Style the tab content */
.tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
}



.section_content {
	line-height: 0.4em;
	font-size: 9px;	
}

</style>
<script type="text/javascript" src="//code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript">

function openTab(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the link that opened the tab
    document.getElementById(cityName).style.display = "block";
    // evt.currentTarget.className += " active";
}

function getSections() {
	var response = "";
	$.ajax({
		url: '/overview/ajax/getSections.php',
		type: 'GET',
		async: false,
		data: {
			dummy: ''
		},
		statusCode: {
			404: function() {
				console.log('not there!');
			}
		},
		success: function(data) {
			response = data;		
		}
	});
	if(response.length > 0) {
		if(validateJSON(response)) {
			var response_obj = $.parseJSON(response);
			return response_obj;
		} else {
			console.log('sections:mysql_data > json = failed!');
		}
	}
}

function getActiveManuals() {
	var response = "";
	$.ajax({
		url: '/overview/ajax/getActiveManuals.php',
		type: 'GET',
		async: false,
		data: {
			dummy: ''
			//,
			//product_short_name: 'max_s8'
		},
		statusCode: {
			404: function() {
				console.log('not there!');
			}
		},
		success: function(data) {
			response = data;		
		}
	});
	if(response.length > 0) {
		if(validateJSON(response)) {
			var response_obj = $.parseJSON(response);
			return response_obj;
		} else {
			console.log('mysql_data > json = failed!');
		}
	}
}

function getProductSections(product_id_value) {
				var resp = '';
				$.ajax({
					url: '/overview/ajax/sortProductContent.php',
					async: false,
					type:'POST',
					data: {
						product_id: product_id_value
					},
					statusCode: {
						404: function() {
							console.log('not there');
						}
					},
					success: function(data) {
						resp = data;
					}
				});
				if(validateJSON(resp)) {
					var resp_obj = ($.parseJSON(resp))
					return resp_obj;
				} else {
					console.log('parse_error: '+resp);	
				}	
}

function validateJSON(json_string) {
	if (/^[\],:{}\s]*$/.test(json_string.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
	return true;
	  //the json is ok
	
	} else {
		console.log(json_string);
	return false;
	  //the json is not ok
	
	}	
}

function trim11 (str) {
    str = str.replace(/^\s+/, '');
    for (var i = str.length - 1; i >= 0; i--) {
        if (/\S/.test(str.charAt(i))) {
            str = str.substring(0, i + 1);
            break;
        }
    }
    return str;
}
	
String.prototype.replaceArray = function(find, replace) {
  var replaceString = this;
  var regex; 
  for (var i = 0; i < find.length; i++) {
    regex = new RegExp(find[i], "g");
    replaceString = replaceString.replace(regex, replace[i]);
  }
  return replaceString;
};
	
String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

function inArray(search_str,array) {
	if(typeof array == 'object'){
		var found = 0;
		for (var i = 0; i < array.length; i++) {
			if(array[i] == search_str) {
				found = 1;
				return true;
			}
		}
		if(found == 0) {
			return false;
		}
	} else {
		return false;
	}
}

function getProductNameByShortName(short_name) {
	var response_obj = ''
	$.ajax({
		url: '/overview/ajax/getProductByShort.php',
		type: 'GET',
		async: false,
		data: {
			product_short: short_name
		},
		statusCode: {
			404: function() {
				console.log('404');
			}
		},
		success: function(data) {
			if(validateJSON(data)) {
				response_obj = $.parseJSON(data);
				
			} else {
				response_obj = data;
			}
		}
	});
	return response_obj;
}

$(document).ready(function(e) {
   // console.log($("ul.tab li:first").find('a').click())
	
	var sections = getSections();
	console.log(sections)	
	
	var manuals = getActiveManuals();
	//console.log('manuals: ');
	//console.log(manuals);
	
	$.each(manuals, function(index, value) {
		//console.log(value.product_short_name)
		$('<div id="'+value.product_short_name+'" class="tabcontent"><h3>'+value.product_name+'</h3><div class="section_wrapper"><h4>Sections</h4><div id="sections_list_'+value.product_short_name+'"></div></div><div class="content_wrapper"><div id="content_list_'+value.product_short_name+'"></div></div></div>').insertAfter('ul.tab');
		if($('#sections_list_'+value.product_short_name)) {
			$.each(sections, function(index_s, value_s) {
				//if(value.id == 1) {
				//console.log("##")
				//console.log(inArray(value.id,value_s.take_part_in_productid.split(',')));
				//console.log(value_s.take_part_in_productid.split(','))
				//}
				if(inArray(value.id,value_s.take_part_in_productid.split(','))) {
					// checked
					if(value_s.section_headline == 'Sicherheitshinweise' || value_s.section_headline == 'Impressum' ) {
						$('#sections_list_'+value.product_short_name).append('<div class="product_'+value.id+'_section_'+value_s.id+'"><input OnChange="toggleCheckbox(this, \'product_'+value.id+'_section_'+value_s.id+'\')" type="checkbox" disabled="true" checked><span> ('+value.id+') '+value_s.section_headline+'</span></div>');
					} else {
						$('#sections_list_'+value.product_short_name).append('<div class="product_'+value.id+'_section_'+value_s.id+'"><input OnChange="toggleCheckbox(this, \'product_'+value.id+'_section_'+value_s.id+'\')" type="checkbox" checked><span> ('+value.id+') '+value_s.section_headline+'</span></div>');
					}
				}
			});
			if($('#content_list_'+value.product_short_name)) {
				var productContent = getProductSections(value.id);
				// console.log(productContent)
				if(typeof productContent.entrys == 'object') {
					$.each(	productContent.entrys , function(index_pc, value_pc) {
						console.log(value_pc);
						// check for template by NAME and PRODUCT_ID
						
						var response_checkfortemplate = ''
						$.ajax({
							url: '/overview/ajax/checkForTemplate.php',
							async: false,
							type: 'GET',
							data: {
								product_id: value.id, 
								template_name: value_pc.headline
							},
							statusCode: {
								404: function() {
									console.log('404:checkForTemplate')
								}
							},
							success: function(data) {
								response_checkfortemplate = data
							}
						})
						if(response_checkfortemplate == 1) {
							
							// getTemplate
							
							var response_gettemplatecontent = ''
							$.ajax({
								url: '/overview/ajax/getTemplate.php',
								async: false,
								type: 'GET',
								data: {
									product_id: value.id, 
									template_name: value_pc.headline
								},
								statusCode: {
									404: function() {
										console.log('404:getTemplate')
									}
								},
								success: function(data) {
									response_gettemplatecontent = data
								}
							})
							console.log(response_gettemplatecontent);
							if(validateJSON(response_gettemplatecontent)) {
								var response_gettemplatecontent_obj = $.parseJSON(response_gettemplatecontent);
								$('#content_list_'+value.product_short_name).append('<div id="product_'+value.id+'_section_'+value_pc.id+'">'+response_gettemplatecontent_obj.content+'</div>');
							} else {
								console.log(value.id+'not valid');
							}
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
						} else {
							console.log(value.id);
							$('#content_list_'+value.product_short_name).append('<div id="product_'+value.id+'_section_'+value_pc.id+'">'+value_pc.content+'</div>');
						}
					});
				}
			}
		}
	});
	$("ul.tab li:nth-child(1)").find('a').click() 
	
});





function toggleCheckbox(checkbox_element, element_id) {
	var checked_state = ($(checkbox_element).prop('checked'))
	if(checked_state) {
		$("#"+element_id).css('display','')
		
	} else {
		$("#"+element_id).css('display','none')
	}
}

</script>

</head>

<body>
<?php

	$rslt_active_manuals = mysqli_query($linki, "SELECT * FROM activated_manuals");
	if(isset($rslt_active_manuals->num_rows) && $rslt_active_manuals->num_rows != 0) {
		while($dsi_active_manuals = mysqli_fetch_assoc($rslt_active_manuals)) {
			//var_dump($dsi_active_manuals['product_short_name']);
			$product_short_name_active = !empty($dsi_active_manuals['product_short_name']) ? $dsi_active_manuals['product_short_name'] : exit;
			$active_product_short_names[] = $product_short_name_active;
			// echo $product_short_name_active;
			
			$rslt_product_id = mysqli_query($linki,"SELECT * FROM wp_dd_products WHERE product_short_name = '".$product_short_name_active."'");
			// var_dump($rslt_product_id->num_rows == 1);
			
			if(isset($rslt_product_id->num_rows) && $rslt_product_id->num_rows == 1) {
				$dsi_product_id = mysqli_fetch_assoc($rslt_product_id);
				$product_id = !empty($dsi_product_id['product_id']) ? $dsi_product_id['product_id'] : exit;
				$product_name = !empty($dsi_product_id['product_name']) ? $dsi_product_id['product_name'] : exit;
				$active_product_ids[] = $product_id;
				$active_product_names[$product_id]['name'] =$product_name;
				$active_product_names[$product_id]['short_name'] = $product_short_name_active;
			}
			/*	$dsi_product_id = mysqli_fetch_assoc($rslt_active_manuals);
				$product_id = !empty($dsi_product_id['product_id']) ? $dsi_product_id['product_id'] : exit;
			} else {
				die(mysqli_error($linki));
			}
			*/
		}
		

		
	} else {
		die(mysqli_error($linki));
	}

?>
<ul class="tab">
<?php

		if(isset($active_product_ids) && is_array($active_product_ids)) {
			foreach($active_product_short_names as $key => $short_name) {
				// var_dump($key." _ ".$short_name);
				?>
                <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(event, '<?php echo $short_name; ?>')"><?php echo $short_name; ?></a></li>
                <?php
			}
		}

?>

  <!-- <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(event, 'London')">London</a></li>
  <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(event, 'Paris')">Paris</a></li>
  <li><a href="javascript:void(0)" class="tablinks" onclick="openTab(event, 'Tokyo')">Tokyo</a></li> -->
</ul>


<?php
/*
$rslti_sections = mysqli_query($linki, "SELECT * FROM wpt_sections ORDER BY order_id");
if(isset($rslti_sections->num_rows)) {
	while($dsi_sections = mysqli_fetch_assoc($rslti_sections)) {
		$section_id = $dsi_sections['id'];
		$section_headline = $dsi_sections['section_headline'];
		$section_take_part_in_productid = explode(',',$dsi_sections['take_part_in_productid']);
		// var_dump($section_take_part_in_productid);
		$sections[$section_id]['headline'] = $section_headline;
		$sections[$section_id]['take_part_in_productid'] = $section_take_part_in_productid;
		$sections[$section_id]['content']['de'] = !empty($dsi_sections['section_content_de']) ? $dsi_sections['section_content_de'] : exit;
	}
}

if(isset($active_product_names) && is_array($active_product_names)) {
	foreach($active_product_names as $product_names_key => $val) {
		//var_dump($key." - ".$val['name']);
		//echo "<br>";
		?>
        <div id="<?php echo $val['short_name']; ?>" class="tabcontent">
        	<h3><?php echo $val['name']; ?></h3>
            <div style="margin-bottom: 20px; border: 1px solid #A3A3A3;" id="product_<?php echo $product_names_key; ?>_checkboxes">
            <?php
			foreach($sections as $section_key => $value) {
				?>
            	<input class="section_checkbox" type="checkbox" onClick="toggleCheckbox(this,this.name)" name="product_<?php echo $product_names_key; ?>_section_<?php echo $section_key; ?>" checked="checked"> <?php echo $value['headline']; ?><br>
                <?php
				
			}
			?>
            
            </div>
            <div style="display: table; width: 100%;">
            
            
            <?php
				foreach($sections as $section_key => $value) {
					if(in_array($product_names_key,$value['take_part_in_productid'])) {
						// true:
						// OUTPUT SECTION
						?>
                        <div style="display: table-row; width: 100%;">
                        <div style="display: table-cell; width: 10px; text-align:center; margin-top: 8px; vertical-align: top;">
                        <!-- <input type="checkbox" onClick="toggleCheckbox(this,this.name)" name="product_<?php echo $product_names_key; ?>_section_<?php echo $section_key; ?>" checked="checked"> -->
                        </div>
                        <div style="display: table-cell; width: auto; vertical-align: top;">
                        <div class="section_content" id="product_<?php echo $product_names_key; ?>_section_<?php echo $section_key; ?>">
                        <?php echo utf8_encode($value['content']['de']); ?>
                        
                        </div>
                        </div>
                        </div>
                        <div style="display: table-row; width: 100%; height: 20px; background: #ECECAA;">
                        <div style="display: table-cell; width: 10px;">
                        </div>
                        <div style="display: table-cell; width: auto;">
                        </div>
                        </div>
                        <?php
						
					}
				}
			?>
            </div>
         <!--  <p>London is the capital city of England.</p> -->
        </div>
        <?php
	}
	/*
	foreach($active_product_names as $key => $short_name) {
		?>
		<div id="<?php echo $short_name; ?>" class="tabcontent">
        	<h3><?php echo $active_product_names[$key]; ?></h3>
            <?php
				foreach($sections as $key => $value) {
					if(in_array($active_product_ids[$key], $value['take_part_in_productid'])) {
						echo $active_product_ids[$key]." - ".$active_product_names[$key]."/ taking part";
					}
				}
			?>
         <!--  <p>London is the capital city of England.</p> -->
        </div>
		<?php
	}
	
}
*/
?>
<!--
<div id="London" class="tabcontent">
  <h3>London</h3>
  <p>London is the capital city of England.</p>
</div>

<div id="Paris" class="tabcontent">
  <h3>Paris</h3>
  <p>Paris is the capital of France.</p>
</div>

<div id="Tokyo" class="tabcontent">
  <h3>Tokyo</h3>
  <p>Tokyo is the capital of Japan.</p>
</div>
 -->
</body>
</html>