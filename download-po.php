<?php
$path = '/BO/contractDocuments/';
if($_REQUEST['cid'])
{
	
	include $_SERVER['DOCUMENT_ROOT'].'/BO/dbconfig.php';
	//echo "SELECT contractfilepaths,contractcustomfilenames,contractname from QuoteContracts WHERE quotecontractid='".$_REQUEST['cid']."'";
	$res = mysql_query("SELECT contractfilepaths,contractcustomfilenames,contractname from QuoteContracts WHERE quotecontractid='".$_REQUEST['cid']."'");
	//echo mysql_num_rows($res); exit;
	if(mysql_num_rows($res))
	{
		$zip = new ZipArchive();
		$row = mysql_fetch_row($res);
		
		$zipname = $_SERVER['DOCUMENT_ROOT'].$path.$row[2].'.zip';
		$zip->open($zipname, ZipArchive::CREATE);
		$exploaded = explode('|',$row[0]);
		$exploadedfn = explode('|',$row[1]);
		
		foreach($exploaded as $key => $value)
		{
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$value) && !is_dir($_SERVER['DOCUMENT_ROOT'].$path.$value))
			{
				$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$value);
				$filename = str_replace(" ","",basename($exploadedfn[$key]));
				if($filename=="")
					$filename = $pathinfo['filename'];
				$filename .=".".strtolower($pathinfo['extension']);
				$zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$value,$filename);
			}
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
?>	