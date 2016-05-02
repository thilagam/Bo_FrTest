ca<?php
if($_REQUEST['ao_id'])
{
	include $_SERVER['DOCUMENT_ROOT'].'/BO/dbconfig.php';
	$res = mysql_query("SELECT filepath  from Delivery where id ='".$_REQUEST['ao_id']."'");
	if(mysql_num_rows($res))
	{
		$row = mysql_fetch_row($res);
		$value = $row[0];
		
		$pathinfo = pathinfo(fopath."/client_spec".$value);
		$filename = $pathinfo['filename'];
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: application/".strtolower($pathinfo['extension']));
		header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize(fopath."/client_spec".$value));
		ob_end_flush();
		readfile(fopath."/client_spec".$value);
	}
}
?>