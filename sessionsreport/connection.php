<?php

	$hostname = "souvnear-mysql-2016.c1pvtrtoohwn.us-east-1.rds.amazonaws.com";
	$username = "souvnear_dba";
	$password = "souvnear_dba";
	$database = "souvneardb";


	$conn = mysql_connect("$hostname","$username","$password") or die(mysql_error());
	mysql_select_db("$database", $conn) or die(mysql_error());
      
?>