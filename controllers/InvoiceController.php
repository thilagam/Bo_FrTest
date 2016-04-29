<?php
/**
 * Invoice for the missions based on Month, Delivery and Articles
 * @version 1.0
*/
class InvoiceController extends Zend_Controller_Action
{
	// To generate Invoices on Monthly
	function monthlyAction()
	{
		$invoice_obj = new Ep_Quote_Invoice();
		$monthly_invoices = $invoice_obj->generateMonthly();
		
		if($monthly_invoices)
		{
			$prev_id = $monthly_invoices[0]['contractmissionid'];
			$total_turnover = 0;
			$last_invoice_id = "";
			$invoice_details = array(); 
			$invoice = array();
			$invoice_details = array();
			$length = count($monthly_invoices)-1;
			$i = $j = 0;
			foreach($monthly_invoices as $row)
			{
				$total_turnover += $row['unit_price'];
				$invoice_details[$i]['description'] = $row['title'];
				$invoice_details[$i]['volume'] = 1;
				$invoice_details[$i]['price_per_art'] = $row['unit_price'];
				$invoice_details[$i]['article_id'] = $row['artid'];
				if($prev_id !=  $row['contractmissionid'] || $length==$j)
				{
					$invoice['invoice_type'] = 'month';
					$invoice['invoice_number'] = $this->getInvoiceNumber($row['client_code'],$invoice_obj,$row['contract_id']);
					$invoice['total_turnover'] = $total_turnover;
					$invoice['contract_id'] = $row['contract_id'];
					$invoice['client_id'] = $row['user_id'];
					$invoice['cmid'] = $row['contractmissionid'];
					$invoice['created_at'] = date('Y-m-d H:i:s');
					$invoice['file_path'] = "/$row[contractmissionid]/".$invoice['invoice_number'].".xlsx";
					$last_invoice_id = $invoice_obj->insertInvoice($invoice);
					$total_turnover = 0;
					$i = -1;
					$k = 0;
					foreach($invoice_details as $invoices)
					{
						$invoice_details[$k]['invoice_id'] = $last_invoice_id;
						$invoice_obj->insertInvoiceDetails($invoice_details[$k++]);
					}
					$missionDir= $_SERVER['DOCUMENT_ROOT']."/BO/contract_mission_invoice/".$row['contractmissionid']."/";
					if(!is_dir($missionDir))
					mkdir($missionDir,TRUE);
					chmod($missionDir,0777);
					$filename = $_SERVER['DOCUMENT_ROOT']."/BO/contract_mission_invoice/$row[contractmissionid]/$invoice[invoice_number].xlsx"; 
					$invoice['invoice_display'] = 'Monthly';
					$invoice['cname'] = $row['first_name']." ".$row['last_name'];
					$invoice['currency'] = $row['currency'];
					$htmltable = $this->generateTable($invoice,$invoice_details);
					$this->convertHtmltableToXlsx($htmltable,$filename,FALSE);
					$invoice = array();
				}
				$i++;
				$j++;
				$prev_id = $row['contractmissionid'];
			}
		}
	}
	
	function getInvoiceNumber($client_number,$invoice_obj,$cid)
	{
		if($client_number)
		$format = "INV-CL-".$client_number;
		else
		$format = "INV-CL-";
		return $invoice_obj->getInvoiceNumber($format,$cid);
	} 
	
	
	/* To generate XLSX File */
	function convertHtmltableToXlsx($htmltable,$filename,$extract=FALSE)
	{		
		require_once APP_PATH_ROOT.'nlibrary/tools/PHPExcel.php';
		
		$htmltable = strip_tags($htmltable, "<table><tr><th><thead><tbody><tfoot><td><br><br /><b><span>");
		$htmltable = str_replace("<br />", "\n", $htmltable);
		$htmltable = str_replace("<br/>", "\n", $htmltable);
		$htmltable = str_replace("<br>", "\n", $htmltable);
		$htmltable = str_replace("&nbsp;", " ", $htmltable);
		$htmltable = str_replace("\n\n", "\n", $htmltable);
		
		$dom = new domDocument;
		$dom->loadHTML($htmltable);
		if(!$dom) {
		  echo "<br />Invalid HTML DOM, nothing to Export.";
		  exit;
		}
		$dom->preserveWhiteSpace = false;   
		$tables = $dom->getElementsByTagName('table');
		if(!is_object($tables)) {
		echo "<br />Invalid HTML Table DOM, nothing to Export.";
		exit;
		}
		
		$tbcnt = $tables->length - 1;   
		
		
		$username = "EditPlace";            
		$usermail = "user@edit-place.com";        
		$usercompany = "Edit Place"; 
		$debug = false;
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Verdana');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$tm = date("YmdHis");
		$pos = strpos($usermail, "@");
		$user = substr($usermail, 0, $pos);
		$user = str_replace(".","",$user);
		
		
		$objPHPExcel->getProperties()->setCreator($username)
							 ->setLastModifiedBy($username)
							 ->setTitle("Sales Generation")
							 ->setSubject("Sales Final Validation")
							 ->setDescription("Sales Report")
							 ->setKeywords("Sales")
							 ->setCompany($usercompany)
							 ->setCategory("Export");
		
		$xcol = '';
		$xrow = 1;
		$usedhdrows = 0;
		for($z=0;$z<=$tbcnt;$z++) {
			$headrows = array();
			$bodyrows = array();
		  $r = 0;
		  $h = 0;
		  $maxcols = 0;
		  $totrows = 0;
		  $rows = $tables->item($z)->getElementsByTagName('tr');
		  $totrows = $rows->length;
		 
		  foreach ($rows as $row) {
			  $ths = $row->getElementsByTagName('th');
			  if(is_object($ths)) {
				if($ths->length > 0) {
				  $headrows[$h]['colcnt'] = $ths->length;
				  if($ths->length > $maxcols) {
					$maxcols = $ths->length;
				  }
				  $nodes = $ths->length - 1;
				  for($x=0;$x<=$nodes;$x++) {
					$thishdg = $ths->item($x)->nodeValue;
					$headrows[$h]['th'][] = $thishdg;
					$headrows[$h]['bold'][] = $this->findBoldText($this->innerHTML($ths->item($x)));
					if($ths->item($x)->hasAttribute('style')) {
					  $style = $ths->item($x)->getAttribute('style');
					  $stylecolor = $this->findStyleColor($style);
					  if($stylecolor == '') {
						$headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
					  }else{
						$headrows[$h]['color'][] = $stylecolor;
					  }
					  $fontsize = $this->findFontSize($style);
					  if($fontsize=='')
					   $headrows[$h]['size'][] = 11;
					  else
					   $headrows[$h]['size'][] = $fontsize;
					}else{
					  $headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
					  $headrows[$h]['size'][] = 11;
					}
					if($ths->item($x)->hasAttribute('colspan')) {
					  $headrows[$h]['colspan'][] = $ths->item($x)->getAttribute('colspan');
					}else{
					  $headrows[$h]['colspan'][] = 1;
					}
					if($ths->item($x)->hasAttribute('align')) {
					  $headrows[$h]['align'][] = $ths->item($x)->getAttribute('align');
					}else{
					  $headrows[$h]['align'][] = 'left';
					}
					if($ths->item($x)->hasAttribute('valign')) {
					  $headrows[$h]['valign'][] = $ths->item($x)->getAttribute('valign');
					}else{
					  $headrows[$h]['valign'][] = 'top';
					}
					if($ths->item($x)->hasAttribute('bgcolor')) {
					  $headrows[$h]['bgcolor'][] = str_replace("#", "", $ths->item($x)->getAttribute('bgcolor'));
					}else{
					  $headrows[$h]['bgcolor'][] = 'FFFFFF';
					}
				  }
				  $h++;
				}
			  }
			  /* Getting TD's */
			  
			  $tds = $row->getElementsByTagName('td');
			  if(is_object($tds)) {
				if($tds->length > 0) {
				  $bodyrows[$r]['colcnt'] = $tds->length;
				  if($tds->length > $maxcols) {
					$maxcols = $tds->length;
				  }
				  $nodes = $tds->length - 1;
				  for($x=0;$x<=$nodes;$x++) {
					$thistxt = $tds->item($x)->nodeValue;
					$bodyrows[$r]['td'][] = $thistxt;
					$bodyrows[$r]['bold'][] = $this->findBoldText($this->innerHTML($tds->item($x)));
					if($tds->item($x)->hasAttribute('style')) {
					  $style = $tds->item($x)->getAttribute('style');
					  $stylecolor = $this->findStyleColor($style);
					  if($stylecolor == '') {
						$bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
					  }else{
						$bodyrows[$r]['color'][] = $stylecolor;
					  }
					  $fontsize = $this->findFontSize($style);
					  if($fontsize=='')
					   $bodyrows[$r]['size'][] = 10;
					  else
					   $bodyrows[$r]['size'][] = $fontsize;
					}else{
					  $bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
					  $bodyrows[$r]['size'][] = 10;
					}
					if($tds->item($x)->hasAttribute('colspan')) {
					  $bodyrows[$r]['colspan'][] = $tds->item($x)->getAttribute('colspan');
					}else{
					  $bodyrows[$r]['colspan'][] = 1;
					}
					if($tds->item($x)->hasAttribute('align')) {
					  $bodyrows[$r]['align'][] = $tds->item($x)->getAttribute('align');
					}else{
					  $bodyrows[$r]['align'][] = 'left';
					}
					if($tds->item($x)->hasAttribute('valign')) {
					  $bodyrows[$r]['valign'][] = $tds->item($x)->getAttribute('valign');
					}else{
					  $bodyrows[$r]['valign'][] = 'top';
					}
					if($tds->item($x)->hasAttribute('bgcolor')) {
					  $bodyrows[$r]['bgcolor'][] = str_replace("#", "", $tds->item($x)->getAttribute('bgcolor'));
					}else{
					  $bodyrows[$r]['bgcolor'][] = 'FFFFFF';
					}
				  }
				  $r++;
				}
			  }
			  
			  /* End of TD's */	  
		  }
		  
		  $worksheet = $objPHPExcel->getActiveSheet();                // set worksheet we're working on
		  $style_overlay = array('font' =>
							array('color' =>
							  array('rgb' => '000000'),'bold' => false,),
								  'fill' 	=>
									  array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCFF')),
								  'alignment' =>
									  array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP),
								  /*'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
													 'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
													 'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
													 'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),*/
							   );

		  $heightvars = array(1=>'42', 2=>'42', 3=>'48', 4=>'52', 5=>'58', 6=>'64', 7=>'68', 8=>'76', 9=>'82');
		  for($h=0;$h<count($headrows);$h++) {
			$th = $headrows[$h]['th'];
			$colspans = $headrows[$h]['colspan'];
			$aligns = $headrows[$h]['align'];
			$valigns = $headrows[$h]['valign'];
			$bgcolors = $headrows[$h]['bgcolor'];
			$colcnt = $headrows[$h]['colcnt'];
			$colors = $headrows[$h]['color'];
			$bolds = $headrows[$h]['bold'];
			$sizes = $headrows[$h]['size'];
			$usedhdrows++;
			$mergedcells = false;
			for($t=0;$t<count($th);$t++) {
			  if($xcol == '') {$xcol = 'A';}else{$xcol++;}
			  $thishdg = $th[$t];
			  $thisalign = $aligns[$t];
			  $thisvalign = $valigns[$t];
			  $thiscolspan = $colspans[$t];
			  $thiscolor = $colors[$t];
			  $thisbg = $bgcolors[$t];
			  $thisbold = $bolds[$t];
			  $thissize = $sizes[$t];
			  $strbold = ($thisbold==true) ? 'true' : 'false';
			  if($thisbg == 'FFFFFF') {
				$style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
			  }else{
				$style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
			  }
			  $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
			  $style_overlay['alignment']['horizontal'] = $thisalign;
			  $style_overlay['font']['color']['rgb'] = $thiscolor;
			  $style_overlay['font']['bold'] = $thisbold;
			  $style_overlay['font']['size'] = $thissize;
			  $style_overlay['fill']['color']['rgb'] = $thisbg;
			  $worksheet->setCellValue($xcol.$xrow, $thishdg);
			  $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);
			 
			  if($thiscolspan > 1) {                                                // spans more than 1 column
				$mergedcells = true;
				$lastxcol = $xcol;
				for($j=1;$j<$thiscolspan;$j++) {
				  $lastxcol++;
				  $worksheet->setCellValue($lastxcol.$xrow, '');
				  $worksheet->getStyle($lastxcol.$xrow)->applyFromArray($style_overlay);
				}
				$cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;
			   
				$worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
				$num_newlines = substr_count($thishdg, "\n");                       // count number of newline chars
				if($num_newlines > 1) {
				  $rowheight = $heightvars[1];                                      // default to 35
				  if(array_key_exists($num_newlines, $heightvars)) {
					$rowheight = $heightvars[$num_newlines];
				  }else{
					$rowheight = 75;
				  }
				  $worksheet->getRowDimension($xrow)->setRowHeight($rowheight);     // adjust heading row height
				}
				$xcol = $lastxcol;
			  }
			}
			$xrow++;
			$xcol = '';
		  }
		  
		  $usedhdrows++;
		
		  for($b=0;$b<count($bodyrows);$b++) {
			$td = $bodyrows[$b]['td'];
			$colcnt = $bodyrows[$b]['colcnt'];
			$colspans = $bodyrows[$b]['colspan'];
			$aligns = $bodyrows[$b]['align'];
			$valigns = $bodyrows[$b]['valign'];
			$bgcolors = $bodyrows[$b]['bgcolor'];
			$colors = $bodyrows[$b]['color'];
			$bolds = $bodyrows[$b]['bold'];
			$sizes = $bodyrows[$b]['size'];
			for($t=0;$t<count($td);$t++) {
			  if($xcol == '') {$xcol = 'A';}else{$xcol++;}
			  $thistext = $td[$t];
			  $thisalign = $aligns[$t];
			  $thisvalign = $valigns[$t];
			  $thiscolspan = $colspans[$t];
			  $thiscolor = $colors[$t];
			  $thisbg = $bgcolors[$t];
			  $thisbold = $bolds[$t];
			  $thissize = $sizes[$t];
			  $strbold = ($thisbold==true) ? 'true' : 'false';
			  if($thisbg == 'FFFFFF') {
				$style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
			  }else{
				$style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
			  }
			  $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
			  $style_overlay['alignment']['horizontal'] = $thisalign;
			  $style_overlay['font']['color']['rgb'] = $thiscolor;
			  $style_overlay['font']['bold'] = $thisbold;
			  $style_overlay['font']['size'] = $thissize;
			  $style_overlay['fill']['color']['rgb'] = $thisbg;
			  if($thiscolspan == 1) {
				$worksheet->getColumnDimension($xcol)->setWidth(20);
			  }
			  else
			  {
			  	$worksheet->getColumnDimension($xcol)->setWidth($thiscolspan*5);
			  }
			  $worksheet->setCellValue($xcol.$xrow, $thistext);
			 
			  $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);
			  if($thiscolspan > 1) {                                                // spans more than 1 column
				$lastxcol = $xcol;
				for($j=1;$j<$thiscolspan;$j++) {
				  $lastxcol++;
				}
				$cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;
				$worksheet->mergeCells($cellRange);
				$worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
				$xcol = $lastxcol;
			  }
			}
			$xrow++;
			$xcol = '';
		  }
		 
		  $azcol = 'A';
		  for($x=1;$x==$maxcols;$x++) {
			$worksheet->getColumnDimension($azcol)->setAutoSize(true);
			$azcol++;
		  }
		  
		}
		// $objPHPExcel->setActiveSheetIndex(0);                      
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		$objWriter->save($filename);
	}
	
	function innerHTML($node) 
	{
	  $doc = $node->ownerDocument;
	  $frag = $doc->createDocumentFragment();
	  foreach ($node->childNodes as $child) {
		$frag->appendChild($child->cloneNode(TRUE));
	  }
	  return $doc->saveXML($frag);
	}
	
	function findSpanColor($node) 
	{
	  $pos = stripos($node, "color:");       
	  if ($pos === false) {                  
		return '000000';                     
	  }
	  $node = substr($node, $pos);           
	  $start = "#";                          
	  $end = ";";                            
	  $node = " ".$node;                     
		$ini = stripos($node,$start);        
		if ($ini === false) return "000000"; 
		$ini += strlen($start);              
		$len = stripos($node,$end,$ini) - $ini; 
		return substr($node,$ini,$len);       
	}
	
	function findStyleColor($style) 
	{
	  $pos = stripos($style, "color:");     
	  if ($pos === false) {                 
		return '';                          
	  }
	  $style = substr($style, $pos);        
	  $start = "#";                         
	  $end = ";";                           
	  $style = " ".$style;                  
		$ini = stripos($style,$start);      
		if ($ini === false) return "";      
		$ini += strlen($start);             
		$len = stripos($style,$end,$ini) - $ini;
		return substr($style,$ini,$len);        
	}
	
	function findFontSize($style) 
	{
	  $pos = stripos($style, "font-size:");      
	  if ($pos === false) {                 
		return '';                          
	  }
	  $style = substr($style, $pos);     
      return substr($style,stripos($style,":")+1,strlen(stripos($style,"px")));        
    }
	
	function findBoldText($node) 
	{
	  $pos = stripos($node, "<b>");          
	  if ($pos === false) {                  
		return false;                        
	  }
	  return true;                           
	}
	/* End of Generation of XLSX File */
	
	// Genrating HTML Table for XLS
	function generateTable($invoice,$invoice_details)
	{
		$html ='<table>';
		$html .= "<tr>";
		$html .= "<td bgcolor='#95B3D7' style='font-size:15px' colspan='3'><b>EP generated invoice</b></td>";
		$html .= "</tr>";
		$html  .= '<tr><td></td></tr><tr><td></td></tr>';
		$html  .= '<tr><td></td></tr><tr><td></td></tr>';
		$html  .= '<tr><td></td><td><b>Number</b></td><td bgcolor="#ffff00"><b>'.$invoice['invoice_number'].'</b></td></tr>';
		$html  .= '<tr><td></td><td><b>Invoice Type</b></td><td bgcolor="#ffff00"><b>'.$invoice['invoice_display'].'</b></td></tr>';
		$html  .= '<tr><td></td><td></td><td bgcolor="#ffff00"><b>'.$invoice['cname'].'</b></td></tr>';
		$html  .= '<tr><td></td></tr><tr><td></td></tr>';
		$html  .= "<tr><td bgcolor='#dadada'><b>DESCRIPTION</b></td><td bgcolor='#dadada'><b>VOLUME</b></td><td bgcolor='#dadada'><b>PRICE / ART</b></td></tr>";
		$i=0;
		foreach($invoice_details as $invoices)
		{
				$html  .="<tr>";
				$html .="<td>".$invoice_details[$i]['description']."</td>";
				$html .="<td>".$invoice_details[$i]['volume']."</td>";
				$html .="<td>".$invoice_details[$i++]['price_per_art']." &".$invoice['currency'].";</td>";
				$html .="</tr>";
		}
		$html .='</table>';
		return $html;
	}
	
	//To generate Invoices on Delivery
	function deliveryAction()
	{
		$invoice_obj = new Ep_Quote_Invoice();
		$delivery_invoices = $invoice_obj->generateDelivery();
		
		if($delivery_invoices)
		{
			$prev_id = $delivery_invoices[0]['contractmissionid'];
			$total_turnover = 0;
			$last_invoice_id = "";
			$invoice_details = array(); 
			$invoice = array();
			$invoice_details = array();
			$length = count($delivery_invoices)-1;
			$i = $j = 0;
			foreach($delivery_invoices as $row)
			{
				$total_turnover += $row['unit_price']*$row['volume'];
				$invoice_details[$i]['description'] = $row['title'];
				$invoice_details[$i]['volume'] = $row['volume'];
				$invoice_details[$i]['price_per_art'] = $row['unit_price'];
				$invoice_details[$i]['delivery_id'] = $row['did'];
				if($prev_id !=  $row['contractmissionid'] || $length==$j)
				{
					$invoice['invoice_type'] = 'delivery';
					$invoice['invoice_number'] = $this->getInvoiceNumber($row['client_code'],$invoice_obj,$row['contract_id']);
					$invoice['total_turnover'] = $total_turnover;
					$invoice['contract_id'] = $row['contract_id'];
					$invoice['client_id'] = $row['user_id'];
					$invoice['cmid'] = $row['contractmissionid'];
					$invoice['created_at'] = date('Y-m-d H:i:s');
					$invoice['file_path'] = "/$row[contractmissionid]/".$invoice['invoice_number'].".xlsx";
					$last_invoice_id = $invoice_obj->insertInvoice($invoice);
					$total_turnover = 0;
					$i = -1;
					$k = 0;
					foreach($invoice_details as $invoices)
					{
						$invoice_details[$k]['invoice_id'] = $last_invoice_id;
						$invoice_obj->insertInvoiceDetails($invoice_details[$k++]);
					}
					$missionDir= $_SERVER['DOCUMENT_ROOT']."/BO/contract_mission_invoice/".$row['contractmissionid']."/";
					if(!is_dir($missionDir))
					mkdir($missionDir,TRUE);
					chmod($missionDir,0777);
					$filename = $_SERVER['DOCUMENT_ROOT']."/BO/contract_mission_invoice/$row[contractmissionid]/$invoice[invoice_number].xlsx"; 
					$invoice['invoice_display'] = 'Delivery';
					$invoice['cname'] = $row['first_name']." ".$row['last_name'];
					$invoice['currency'] = $row['currency'];
					$htmltable = $this->generateTable($invoice,$invoice_details);
					$this->convertHtmltableToXlsx($htmltable,$filename,FALSE);
					$invoice = array();
				}
				$i++;
				$j++;
				$prev_id = $row['contractmissionid'];
			}
		}
	}
	
	//To generate Invoice Action based on Mission
	function missionAction()
	{
		$invoice_obj = new Ep_Quote_Invoice();
		$mission_invoices = $invoice_obj->generateMission();
		
		if($mission_invoices)
		{
			$prev_id = $mission_invoices[0]['contractmissionid'];
			$total_turnover = 0;
			$last_invoice_id = "";
			$invoice_details = array(); 
			$invoice = array();
			$invoice_details = array();
			$length = count($mission_invoices)-1;
			$i = $j = 0;
			foreach($mission_invoices as $row)
			{
				$total_turnover += $row['unit_price']*$row['volume'];
				$invoice_details[$i]['description'] = $row['title'];
				$invoice_details[$i]['volume'] = $row['volume'];
				$invoice_details[$i]['price_per_art'] = $row['unit_price'];
				$invoice_details[$i]['delivery_id'] = $row['did'];
				$invoice_details[$i]['mission_id'] = $row['contractmissionid'];
				if($prev_id !=  $row['contractmissionid'] || $length==$j)
				{
					$invoice['invoice_type'] = 'mission';
					$invoice['invoice_number'] = $this->getInvoiceNumber($row['client_code'],$invoice_obj,$row['contract_id']);
					$invoice['total_turnover'] = $total_turnover;
					$invoice['contract_id'] = $row['contract_id'];
					$invoice['client_id'] = $row['user_id'];
					$invoice['cmid'] = $row['contractmissionid'];
					$invoice['created_at'] = date('Y-m-d H:i:s');
					$invoice['file_path'] = "/$row[contractmissionid]/".$invoice['invoice_number'].".xlsx";
					$last_invoice_id = $invoice_obj->insertInvoice($invoice);
					$total_turnover = 0;
					$i = -1;
					$k = 0;
					foreach($invoice_details as $invoices)
					{
						$invoice_details[$k]['invoice_id'] = $last_invoice_id;
						$invoice_obj->insertInvoiceDetails($invoice_details[$k++]);
					}
					$missionDir= $_SERVER['DOCUMENT_ROOT']."/BO/contract_mission_invoice/".$row['contractmissionid']."/";
					if(!is_dir($missionDir))
					mkdir($missionDir,TRUE);
					chmod($missionDir,0777);
					$filename = $_SERVER['DOCUMENT_ROOT']."/BO/contract_mission_invoice/$row[contractmissionid]/$invoice[invoice_number].xlsx"; 
					$invoice['invoice_display'] = 'Mission';
					$invoice['cname'] = $row['first_name']." ".$row['last_name'];
					$invoice['currency'] = $row['currency'];
					$htmltable = $this->generateTable($invoice,$invoice_details);
					$this->convertHtmltableToXlsx($htmltable,$filename,FALSE);
					$invoice = array();
				}
				$i++;
				$j++;
				$prev_id = $row['contractmissionid'];
			}
		}
	}
}
?>