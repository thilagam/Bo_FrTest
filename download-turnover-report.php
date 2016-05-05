<?php
ob_start();
$type = $_REQUEST['type'];
if($type='turnover')
{
	$filename=$_REQUEST['filename'];
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: application/".strtolower($filename));
		header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT']."/BO/turnover-report/$filename"));
		ob_end_flush();
		readfile($_SERVER['DOCUMENT_ROOT']."/BO/turnover-report/$filename");
		exit;
}

?>