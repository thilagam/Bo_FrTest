<?php
if(isset($_GET['saction']) && $_GET['saction']=='download' && isset($_GET['file']) && isset($_GET['ext']))
{
	$filename=$_GET['file'].".".$_GET['ext'];
	$path_file="/home/sites/site6/web/BO/seo_download/position/".$filename;
	if(file_exists($path_file))
    { 
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($path_file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($path_file));
		readfile($path_file);
		exit;
	}
	else	
	{
		echo "File Not Exists";
	}
}	
?>