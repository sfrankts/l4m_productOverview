<?php

if($linki = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
	if(DEBUG == true) echo "==DEBUG==: connected:selected";	
} else {
	die(mysql_error());
}

?>