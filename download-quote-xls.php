<?php
ob_start();

if($_GET['art_id'])
{
$session_id=$_GET['art_id'];
$path = "/BO/participantsxls/";
$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
}
else
{
$session_id=$_GET['session_id'];
$path = "/BO/quotexls/";
$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
}
$filename = str_replace(" ","",basename($session_id));
if($filename=="")
	$filename = $pathinfo['filename'];

header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers
header("Content-Type: application/".strtolower($pathinfo['extension']));
header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT'].$path.$session_id));
ob_end_flush();
readfile($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
//unlink($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
?>