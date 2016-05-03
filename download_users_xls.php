<?php
include("nconfig/path.php");
	//include class definition file
include("nconfig/class.php");
//include session file
include("nconfig/session.php");

$content=$_SESSION['content'];

//echo $content;exit;

 $file = 'excelFile-'.date("Y-M-D")."-".time().'.xls';
if($content)
{
	unset($_SESSION['content']);
	ob_end_clean();
	header("Expires: 0");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); 
	header("Content-type: application/vnd.ms-excel;charset:UTF-8");
	header('Content-length: '.strlen($content));
	header('Content-disposition: attachment; filename='.basename($file));
	echo $content;
	exit;
}
