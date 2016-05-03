<?php
ob_start();
	$post_array=$_POST;
	$post_array['hide_total']=str_replace('select_rows,','',$post_array['hide_total']);
	//print_r($post_array);exit;
 $ao_id=$_REQUEST['invoice_id']; 
 $merge_pdf=$_REQUEST['merge_pdf']; 
 
 
$con = mysql_connect("localhost","epweb","293PA3Y4577KjVUM");
if (!$con)
{
	die('Could not connect: ' . mysql_error());
}
mysql_select_db("dev_editplace1", $con);


        //$ckecks = explode(',', $post_array['hide_total']);
        $ckecks = explode(',', $_REQUEST['invoice_id']);

        $i=0;
		foreach($ckecks as $key=>$value)
		{
			$invoice_id_array=explode("_",$value);			
			//$value=str_replace('R','',end($invoice_id_array));			
			$value=preg_replace('/R[?\d{1}]?/','',end($invoice_id_array));
			
			//added w.r.t date change in invoice name
			if(strstr($value, '/'))
			{
				$invoiceId_array=explode("-",$value);
				$date_array=explode("/",$invoiceId_array[0]);
				$value=$date_array[2]."-".$date_array[1]."-".$date_array[0]."-".$invoiceId_array[1];
			}
			
			$invoiceids[$i]=$value;
			$i++;
		}  
		//echo "<pre>";
		//print_r($invoiceids); exit;
        
        for($j=0; $j<count($invoiceids); $j++)
        {
           $invoiceid='ep_invoice_'.$invoiceids[$j];
           $invoiceQuery="select i.user_id,i.invoice_path from Royalties r LEFT JOIN Invoice i ON r.invoiceId=i.invoiceId 
		   where i.invoiceId='".$invoiceid."' GROUP BY i.invoiceId  ORDER BY r.created_at DESC";
		   $result = mysql_query($invoiceQuery);
		   while($row = mysql_fetch_array($result))
			{
				$invoice_path[$j]=$row['invoice_path'];
				$user_id[$j]=$row['user_id'];
			}
			if($invoice_path[$j])
				$invoicePDFPath[$j]="/home/sites/site9/web/FO/invoice/".$invoice_path[$j];
           $file_path[$j]=$invoicePDFPath[$j];
		   $file_user_id[$j]=$user_id[$j];
        }  
		//print_r($file_path);

if(count($file_path)>0 &&  $merge_pdf=='yes')
{	
		
		$input_files=implode(" ",$file_path);
		
		$merge_pdf="/home/sites/site9/web/FO/invoice/merge_files/".date("YmdHis").".pdf";
		
		//$merge_pdf="/home/sites/site9/web/FO/invoice/merge_files/20140415093637.pdf";
		
		
		$cmd="gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite  -sOutputFile=$merge_pdf $input_files";
		exec($cmd,$output);
				
		
		sleep(5);
		
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename='.basename($merge_pdf));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($merge_pdf));
		readfile($merge_pdf);
		exit;
		
}		
else if(count($file_path)>0)
{
        $zipname = "/home/sites/site9/web/FO/invoice/zip/invoice_".rand(1000, 9999).".zip";
		$zip=new ZipArchive();
		$zip->open($zipname, ZipArchive::OVERWRITE);
		foreach ($file_path as $key => $value)
		{
			 $fname=$value;
			 $new_filename = substr($fname,strrpos($fname,'/') + 1);
			 
			 //Added for invoice id date changes
			$invoiceId_array=explode("-",$new_filename);
			$new_filename=$invoiceId_array[2]."-".$invoiceId_array[1]."-".$invoiceId_array[0]."-".$invoiceId_array[3];
			//ended
			 
			 
			 $user_full_name=str_replace(" ","_",trim(getUserName($file_user_id[$key])));
			 
			 $new_filename = frenchCharsToEnglish($user_full_name)."_".$new_filename;
			 
			 //echo $fname."--".$new_filename."<br>";
			 $zip->addFile($fname,$new_filename);
			// $zip->addFile($fname);
		}
		//exit;
			$zip->close();
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($zipname));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($zipname));
		readfile($zipname);
		exit;	
}		
		
function getUserName($user)
{
	//CONCAT(first_name,' ',UPPER(SUBSTRING(last_name, 1,1)))) as sendername

	$userQuery="select CONCAT(first_name,' ',last_name) as full_name from User u
						INNER JOIN UserPlus up ON u.identifier=up.user_id						
						where identifier='".$user."'";
	//echo $senderQuery;exit;
	$result = mysql_fetch_array(mysql_query($userQuery));
	return $result['full_name'];
	
	   
}		
function frenchCharsToEnglish($word)
{
    $pattern = array("'é'", "'è'", "'ë'", "'ê'", "'É'", "'È'", "'Ë'", "'Ê'", "'á'", "'à'", "'ä'", "'â'", "'å'", "'Á'", "'À'", "'Ä'", "'Â'", "'Å'", "'ó'", "'ò'", "'ö'", "'ô'", "'Ó'", "'Ò'", "'Ö'", "'Ô'", "'í'", "'ì'", "'ï'", "'î'", "'Í'", "'Ì'", "'Ï'", "'Î'", "'ú'", "'ù'", "'ü'", "'û'", "'Ú'", "'Ù'", "'Ü'", "'Û'", "'ý'", "'ÿ'", "'Ý'", "'ø'", "'Ø'", "'œ'", "'Œ'", "'Æ'", "'ç'", "'Ç'", "'æ'", "'î'", "'ï'", "'«'", "'»'", "'€'", "';'");

    $replace = array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'i', 'i', 'i', 'I', 'I', 'I', 'I', 'I', 'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', 'y', 'y', 'Y', 'o', 'O', 'a', 'A', 'A', 'c', 'C', 'A', 'I', 'I', '', '', '', '');

    $pattern_1	=	array("e´", "(", ")", "[", "]", "{", "}", "/", "!", "@", "#", "$", "^", "&", "*", "-", "__", "'", '"', "%");

    $replace_1 = array("e","_","_","_","_","_","_","_","_","_","_","_","_","_","_","_","_","_","_","");

    $replaced	=	str_replace($pattern_1, $replace_1, preg_replace($pattern, $replace, $word)) ;
	//added By arun
	$replaced= utf8_decode($replaced);	
	$replaced   =   str_replace("?",'',$replaced);

    return	$replaced ;
}	
//////////////////////////////////////////////
?>
