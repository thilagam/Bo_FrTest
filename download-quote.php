<?php
ob_start();
 
include $_SERVER['DOCUMENT_ROOT'].'/BO/dbconfig.php';

$index = $_REQUEST['index'];
$type = $_REQUEST['type'];

if($type=="seo_mission")
{
	$path = "/BO/misssionDocuments/";
	$identifier = $_REQUEST['mission_id'];
	$res = mysql_query("SELECT documents_path,documents_name from QuoteMissions WHERE  identifier='".$identifier."'");
}
else if($type=="quote" || $type=="cm_tech" || $type=="cm_seo" || $type=="cm_prod" || $type=="cm_staff")
{
	$path = "/BO/quote_documents/";
	$identifier = $_REQUEST['quote_id'];
	$res= mysql_query("SELECT documents_path,documents_name from Quotes WHERE identifier='".$identifier."'");
}elseif($type=='saleslog'){
	$path = "/BO/quote_documents/";
	$logid = $_REQUEST['logid'];
	$sale= mysql_query("SELECT custom from QuotesLog WHERE id='".$logid."'");
}elseif($type=="saleslogdown"){
	
	$path = "/BO/quote_documents/";
    $quote_id=$_REQUEST['quote_id'];
    $file_name=$_REQUEST['filename'];
    
    $sales_down=mysql_query("SELECT sales_final_documents_path,sales_final_documents_names from Quotes WHERE identifier='".$quote_id."'");
    
	}
else if($type=="contract")
{
	$path = "/BO/contractDocuments/";
	$identifier = $_REQUEST['contract_id'];
	$res= mysql_query("SELECT contractfilepaths,contractcustomfilenames from QuoteContracts WHERE quotecontractid='".$identifier."'");
}
else if($type=="tech_mission")
{
	$path = "/BO/misssionDocuments/";
	$identifier = $_REQUEST['mission_id'];
	$res = mysql_query("SELECT documents_path,documents_name from TechMissions WHERE  identifier='".$identifier."'");
}
else if($type=="staff_mission")
{
	$path = "/BO/misssionDocuments/";
	$identifier = $_REQUEST['mission_id'];
	$res = mysql_query("SELECT documents_path,documents_name from StaffMissions WHERE  staff_missionId='".$identifier."'");
}
else if($type=="testarticle")
{
	$path = "/BO/recruitmentDocuments/";
	$identifier = $_REQUEST['contract_id'];
	$res = mysql_query("SELECT test_art_files_path,test_art_files_name from Recruitment WHERE recruitment_id='".$identifier."'");
}
else if($type=="recruitment")
{
	$path = "/BO/recruitmentDocuments/";
	$identifier = $_REQUEST['contract_id'];
	$res = mysql_query("SELECT files_path,files_name from Recruitment WHERE recruitment_id='".$identifier."'");
}
else if($type=="task")
{
	$path = "/BO/taskDocuments/";
	$identifier = $_REQUEST['task_id'];
	$res = mysql_query("SELECT documents_path,documents_name from MissionTasks WHERE task_id ='".$identifier."'");
}
elseif($type=="prod_mission")
{
	$path = "/BO/misssionDocuments/";
	$identifier = $_REQUEST['mission_id'];
	$res = mysql_query("SELECT documents_path,documents_name from ContractMissions WHERE  contractmissionid='".$identifier."'");
}
elseif($type='turnover')
{
	$filename=$_REQUEST['filename'];
	
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: application/".strtolower($filename));
		header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT']."/BO/quotes_weekly_report/$filename"));
		ob_end_flush();
		readfile($_SERVER['DOCUMENT_ROOT']."/BO/turnover-report/$filename");
}

if($index=="-1" && ($type=="cm_tech" || $type=="cm_seo" || $type=="cm_staff"))
{
	$zipname = $_SERVER['DOCUMENT_ROOT']."/BO/quotexls/".strtoupper($type)."_".$identifier.".zip";
	$zip=new ZipArchive();
	$zip->open($zipname, ZipArchive::OVERWRITE);
	
	if(mysql_num_rows($res))
	{
		$row = mysql_fetch_row($res);
		$documents_paths = array_filter(explode("|",$row[0]));
		$documents_names = explode("|",$row[1]);
		
		foreach ($documents_paths as $key => $value)
		{
			$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$value);
			
			$filename = str_replace(" ","",basename($documents_names[$key]));
			if($filename=="")
				$filename = $pathinfo['filename'];
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$value))
			$zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$value,html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
		}
	}
	
	
	if($type=="cm_tech")
	{
		$path = "/BO/misssionDocuments/";
		$identifier = $_REQUEST['mission_id'];
		$res = mysql_query("SELECT documents_path,documents_name from TechMissions WHERE  identifier='".$identifier."'");
	}
	elseif($type=="cm_seo")
	{
		$path = "/BO/misssionDocuments/";
		$identifier = $_REQUEST['mission_id'];
		$res = mysql_query("SELECT documents_path,documents_name from QuoteMissions WHERE  identifier='".$identifier."'");
	}
	else
	{
		$path = "/BO/misssionDocuments/";
		$identifier = $_REQUEST['mission_id'];
		$res = mysql_query("SELECT documents_path,documents_name from StaffMissions WHERE  staff_missionId='".$identifier."'");
	}
	
	if(mysql_num_rows($res))
	{
		$row = mysql_fetch_row($res);
		$documents_paths = array_filter(explode("|",$row[0]));
		$documents_names = explode("|",$row[1]);
		
		foreach ($documents_paths as $key => $value)
		{
			$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$value);
			
			$filename = str_replace(" ","",basename($documents_names[$key]));
			if($filename=="")
				$filename = $pathinfo['filename'];
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$value))
			$zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$value,html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
		}
	}
	
	$zip->close();
		
	if(file_exists($zipname))
	{
		chmod($zipname,0777);	
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".basename($zipname));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($zipname));
		ob_end_flush();
		readfile($zipname);
		unlink($zipname);
	}
}
elseif($index=="-1" && $type=="cm_prod")
{
	$zipname = $_SERVER['DOCUMENT_ROOT']."/BO/quotexls/".strtoupper($type)."_".$identifier.".zip";
	$zip=new ZipArchive();
	$zip->open($zipname, ZipArchive::OVERWRITE);
	
	if(mysql_num_rows($res))
	{
		$row = mysql_fetch_row($res);
		$documents_paths = array_filter(explode("|",$row[0]));
		$documents_names = explode("|",$row[1]);
		
		foreach ($documents_paths as $key => $value)
		{
			$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$value);
			
			$filename = str_replace(" ","",basename($documents_names[$key]));
			if($filename=="")
				$filename = $pathinfo['filename'];
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$value))
			$zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$value,html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
		}
	}
	
	$path = "/BO/misssionDocuments/";
	$quote_id = $_REQUEST['quote_id'];
	$res = mysql_query("SELECT t.documents_path,t.documents_name from TechMissions t WHERE find_in_set(t.identifier, (select techmissions_assigned From Quotes where identifier='".$quote_id."') )>0 AND t.include_final='yes'");
	while($row = mysql_fetch_array($res))
	{
		$documents_paths = array_filter(explode("|",$row[0]));
		$documents_names = explode("|",$row[1]);
		foreach ($documents_paths as $key => $value)
		{
			$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$value);
			
			$filename = str_replace(" ","",basename($documents_names[$key]));
			if($filename=="")
				$filename = $pathinfo['filename'];
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$value))
			$zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$value,html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
		}
	}
	
	$res = mysql_query("SELECT m.documents_path,m.documents_name
							FROM QuoteMissions m  
							INNER JOIN Quotes q ON q.identifier=m.quote_id
							WHERE 1=1  AND m.quote_id='".$quote_id."' AND m.include_final='yes' AND m.product IN ('seo_audit','smo_audit') AND (m.misson_user_type='sales' OR m.misson_user_type='seo')							
							ORDER BY field(m.misson_user_type, 'sales', 'seo'), m.identifier ASC ");
	while($row = mysql_fetch_array($res))
	{
		$documents_paths = array_filter(explode("|",$row[0]));
		$documents_names = explode("|",$row[1]);
		foreach ($documents_paths as $key => $value)
		{
			$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$value);
			
			$filename = str_replace(" ","",basename($documents_names[$key]));
			if($filename=="")
				$filename = $pathinfo['filename'];
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$value))
			$zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$value,html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
		}
	}
	
	if($_REQUEST['mission_id'])
	{
		$path = "/BO/misssionDocuments/";
		$identifier = $_REQUEST['mission_id'];
		$res = mysql_query("SELECT documents_path,documents_name from ContractMissions WHERE  contractmissionid='".$identifier."'");
		
		while($row = mysql_fetch_array($res))
		{
			$documents_paths = array_filter(explode("|",$row[0]));
			$documents_names = explode("|",$row[1]);
			foreach ($documents_paths as $key => $value)
			{
				$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$value);
				
				$filename = str_replace(" ","",basename($documents_names[$key]));
				if($filename=="")
					$filename = $pathinfo['filename'];
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$value))
				$zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$value,html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
			}
		}
	}
	
	$zip->close();
		
	if(file_exists($zipname))
	{
		chmod($zipname,0777);	
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".basename($zipname));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($zipname));
		ob_end_flush();
		readfile($zipname);
		unlink($zipname);
	}
}
elseif(mysql_num_rows($res))
{
	$row = mysql_fetch_row($res);
	$documents_paths = array_filter(explode("|",$row[0]));
	$documents_names = explode("|",$row[1]);
	
	if($index=="-1")
	{
		$zipname = $_SERVER['DOCUMENT_ROOT']."/BO/quotexls/".strtoupper($type)."_".$identifier.".zip";
		$zip=new ZipArchive();
		$zip->open($zipname, ZipArchive::OVERWRITE);
		
		foreach ($documents_paths as $key => $value)
		{
			$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$value);
			
			$filename = str_replace(" ","",basename($documents_names[$key]));
			if($filename=="")
				$filename = $pathinfo['filename'];
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$value))
			$zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$value,html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
			// $zip->addFile($fname);
		}
		$zip->close();
		
		if(file_exists($zipname))
		{
			chmod($zipname,0777);	
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers
			header("Content-Type: application/zip");
			header("Content-Disposition: attachment; filename=".basename($zipname));
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($zipname));
			ob_end_flush();
			readfile($zipname);
			unlink($zipname);
		}
	}
	else
	{
		$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$documents_paths[$index]);
		$pathinfo2 = pathinfo($documents_names[$index]);
		
		$filename = str_replace(" ","",basename($documents_names[$index]));
		if($filename=="")
			$filename = $pathinfo['filename'];
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: application/".strtolower($pathinfo['extension']));
		header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT'].$path.$documents_paths[$index]));
		ob_end_flush();
		readfile($_SERVER['DOCUMENT_ROOT'].$path.$documents_paths[$index]);
	}
}elseif(mysql_num_rows($sale)){
	$row = mysql_fetch_row($sale);
	
	$documents_paths = array_filter(explode("|",$row[0]));
		$documents_names = explode("|",$row[1]);
		
	  $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$documents_paths[$index]);
		$pathinfo2 = pathinfo($documents_names[$index]);
		
		$filename = str_replace(" ","",basename($documents_names[$index]));
		if($filename=="")
			$filename = $pathinfo['filename'];
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: application/".strtolower($pathinfo['extension']));
		header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT'].$path.$documents_paths[$index]));
		ob_end_flush();
		readfile($_SERVER['DOCUMENT_ROOT'].$path.$documents_paths[$index]);
	
	}
	elseif(mysql_num_rows($sales_down)){
				$row_sale = mysql_fetch_row($sales_down);
			$currentFile=$quote_id.'/'.$file_name;
					
			 $sales_paths = array_filter(explode("|",$row_sale[0]));
				$sales_names = explode("|",$row_sale[1]);
				if(in_array($currentFile,$sales_paths)){
				$key= array_search($currentFile,$sales_paths);
				}
				
			$pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$sales_paths[$key]);
			$pathinfo2 = pathinfo($documents_names[$index]);
			
			$filename = str_replace(" ","",basename($sales_names[$key]));
			if($filename=="")
				$filename = $pathinfo['filename'];
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers
			header("Content-Type: application/".strtolower($pathinfo['extension']));
			header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT'].$path.$sales_paths[$key]));
			ob_end_flush();
			readfile($_SERVER['DOCUMENT_ROOT'].$path.$sales_paths[$key]);
		
	}

?>
