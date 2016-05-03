<?php
	$con = mysql_connect("127.0.0.1","ep_fr","8tJEzHnFCh9B3VbS");
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db("editplace_live_09_02_2016", $con);
	define('fopath','/home/sites/site5/web/FO');
?>