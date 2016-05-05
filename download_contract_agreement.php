<?php
$user_id=$_REQUEST['user_id'];
$type=$_REQUEST['type'];
$file=$_REQUEST['file'];

if($type=='zip')
{
	$zipname = '/home/sites/site6/web/BO/contract_agreements/'.$user_id.'/contract_agreements.zip';
}
else if($type=='pdf' && $file )
{
	$zipname = '/home/sites/site6/web/BO/contract_agreements/'.$user_id.'/'.$file.".pdf";
}

//echo $zipname;exit;

if(file_exists($zipname))
{
		//echo $zipname;  exit;
	chmod($zipname,0777);	
	
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Type: application/$type");
	header("Content-Disposition: attachment; filename=".basename($zipname));
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($zipname));
	ob_end_flush();
	readfile($zipname);
	exit;	
}
else
{
	echo "Zip not Exists";exit;
}
?>