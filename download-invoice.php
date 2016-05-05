<?php
/web/BO/download_invoice.php
$path = '/BO/contract_mission_invoice/';
if($_REQUEST['cid'])
{
	include $_SERVER['DOCUMENT_ROOT'].'/BO/dbconfig.php';
	
	if($_REQUEST['final']=='yes')
	{
	$res = mysql_query("SELECT file_path,invoice_number from ContractMissionInvoice WHERE contract_id='".$_REQUEST['cid']."' AND archive=0 AND invoice_type='final'");
	}
	else
	{		
	$res = mysql_query("SELECT file_path,invoice_number from ContractMissionInvoice WHERE contract_id='".$_REQUEST['cid']."' AND archive=0 AND invoice_type!='final'");
	}

	if(mysql_num_rows($res))
	{
		$zip = new ZipArchive();
		$zipname = $_SERVER['DOCUMENT_ROOT'].$path."contract-inv-".$_REQUEST['cid'].'.zip';
		$zip->open($zipname, ZipArchive::CREATE);
		
		while($row = mysql_fetch_assoc($res))
		{
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$row['file_path']))
			$zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$row['file_path'],$row['invoice_number'].".xlsx");
		}
		$zip->close();
		if(file_exists($zipname)){
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header('Content-type: application/zip');
			header('Content-Disposition: attachment; filename="'.basename($zipname).'"');
			readfile($zipname);
			unlink($zipname);
		}
	}
}
else
{
	$filename = $_REQUEST['fname'];
	$reqfilea = explode("/",$filename);
	$reqfile = $reqfilea[count($reqfilea)-1];
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Type: application/xlsx");
	header("Content-Disposition: attachment; filename=".$reqfile);
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT'].$path.$filename));
	ob_end_flush();
	readfile($_SERVER['DOCUMENT_ROOT'].$path.$filename);
}
?>	