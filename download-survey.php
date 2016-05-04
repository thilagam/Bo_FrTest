<?php
ob_start();

if($_GET['filename'])
{
	if($_GET['testart'])
	{
		$file1 = $file_name = $_SERVER['DOCUMENT_ROOT'].'/BO/recruitmentDocuments/'.$_GET['filename'];
		$pathinfo = pathinfo($file_name);
		$file_name = substr($pathinfo['filename'],0,-4).".".$pathinfo['extension'];
	}
	elseif($_GET['recruitmenttestartid'])
	{
		$file1 = $file_name =  '/home/sites/site5/web/FO/recruitmentTestArticles/'.$_GET['recruitmenttestart'];
		$pathinfo = pathinfo($file_name);
		$file_name = $_GET['filename'];
	}
	else
	{
		$file1 = $file_name = '/home/sites/site5/web/FO/poll_spec/'.$_GET['filename'];
		$pathinfo = pathinfo($file_name);
		$file_name = substr($pathinfo['filename'],0,strrpos($pathinfo['filename'],"_")).".".$pathinfo['extension'];
	}
	
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Type: application/".strtolower($pathinfo['extension']));
	header("Content-Disposition: attachment; filename=".html_entity_decode($file_name, ENT_COMPAT, 'UTF-8'));
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($file1));
	ob_end_flush();
	readfile($file1);
}
?>