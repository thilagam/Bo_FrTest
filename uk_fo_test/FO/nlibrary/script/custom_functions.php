<?php
function frenchCharsToEnglish($word)
{
    $pattern = array("'œ'", "'Œ'", "'Æ'","'æ'");

    $replace = array('oe', 'Oe', 'Ae', 'ae');

    $replaced	=	preg_replace($pattern, $replace, $word);
	//added By arun
	$replaced= utf8_decode($replaced);	
	$replaced   =   str_replace("?",'',$replaced);

    return	$replaced ;
}


// DISPLAYS COMMENT POST TIME AS "1 year, 1 week ago" or "5 minutes, 7 seconds
function time_ago($date,$granularity=1) 
	{
		$date = strtotime($date);
		$difference = time() - $date;
		$periods = array('decade' => 315360000,
							'year' => 31536000,
							'month' => 2628000,
							'week' => 604800,
							'd' => 86400,
							'h' => 3600,	
							'mn' => 60,
							'sec' => 1);
		if ($difference < 5) 
		{ // less than 5 seconds ago, let's say "just now"
			$retval = "This minute";
			return $retval;
		} 
		else 
		{
			foreach ($periods as $key => $value)
			{
				if ($difference >= $value) 
				{
					$time = floor($difference/$value);
					$difference %= $value;
					$retval .= ($retval ? ' ' : '').$time.' ';
					//$retval .= ($retval ? ' ' : '').$time;
					//$retval .= (($time > 1) ? $key.'s' : $key);
					$retval .= (($time > 1) ? $key.'' : $key);
					$granularity--;
				}
				if ($granularity == '0') { break; }
			}
			
			return $retval.' ago';
		}
	}
	//age calculation
	function calculateAge($dob)
  	{
  		 //date in yyyy-mm-dd format; or it can be in other formats as well
  		 $birthDate = $dob;
           //explode the date to get month, day and year
           $birthDate = explode("-", $birthDate);
           //get age from date or birthdate
           $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
  		 
           return $age;
  	
  	}	
  	//html2txt function
  	function html2txt($document){
        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                       '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                       '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                       '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
        );
        $text = preg_replace($search, '', $document);
        return $text;
    }

    //format file sizes in kb/mb/gb
    function formatSizeUnits($path)
    {
         if(file_exists($path))
            $bytes = sprintf('%u', filesize($path));
         else
            $bytes=0;

          if ($bytes > 0)
          {
              $unit = intval(log($bytes, 1024));
              $units = array('B', 'KB', 'MB', 'GB');

              if (array_key_exists($unit, $units) === true)
              {
                  return sprintf('%d%s', round($bytes / pow(1024, $unit)), $units[$unit]);
              }
          }

          return $bytes;
    }
   	function sortByTimestamp($a, $b) {
    	return $a['timestamp'] > $b['timestamp'];
  	}
   	function sortByTimestampDESC($a, $b) {
    	return $a['timestamp'] < $b['timestamp'];
  	}
  	 function sortByPriceDESC($a, $b) {
    	return $a['latestPrice'] < $b['latestPrice'];
  	}
  	 function sortByPriceASC($a, $b) {
    	return $a['latestPrice'] > $b['latestPrice'];
  	}
  	 function sortByTitleDESC($a, $b) {
    	return $a['title'] < $b['title'];
  	}
  	 function sortByTitleASC($a, $b) {
    	return $a['title'] > $b['title'];
  	}
    function sortByParticipantsDESC($a, $b) {
      return $a['participants'] < $b['participants'];
    }
     function sortByParticipantsASC($a, $b) {
      return $a['participants'] > $b['participants'];
    }

    /**create zip file**/
    function create_zip($files = array(),$destination = '',$overwrite = true)
    {
        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }
        //vars
        $valid_files = array();
        //if files were passed in...
        if(is_array($files)) {
            //cycle through each file
            foreach($files as $file) {
                //make sure the file exists
                if(file_exists($file) && !is_dir($file)) {
                    //echo $file."<br>";
                    $valid_files[] = $file;
                }
            }
        }
        //if we have good files...
        if(count($valid_files)) {
            //create the archive
            $zip = new ZipArchive();
            if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            $numItems = count($valid_files);
            $i = 0;
            foreach($valid_files as $file) {
                //$zip->addFile($file,$file);
                /*if(++$i === $numItems) {
                       $fileInfo=pathinfo($file);
                    $file_name=$this->utf8dec("Article-a-corriger").".".$fileInfo['extension'] ;
                }
                else  */
                $file_name= basename($file);
                $zip->addFile($file,$file_name);
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            //close the zip -- done!
            $zip->close();
            //check to make sure the file exists
            return file_exists($destination);
        }
        else
        {
            return false;
        }
    }
    function utf8dec($s_String)
    {
        $s_String=str_replace("e&#769;","&eacute;",$s_String);
        $s_String=str_replace("E&#769;","&Eacute;",$s_String);
        $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
        return substr($s_String, 0, strlen($s_String)-1);
    }

    function popup_paginate( $page_array,$mission_identifier,$upcoming=NULL,$finished=NULL,$mission_type=NULL)
    {
        $prev='';
        $next='';

        $offerCnt=count($page_array)-1;

        //echo "<pre>";print_r($page_array);
        if($upcoming)
            $differed='upcoming=yes&';
        else if($finished)
            $differed='finished=yes&';  

        if($offerCnt>0)
        {
            $paginate='';

            foreach($page_array as $key=>$page)
            {
				if($page['ao_type']=='correction')
					$page_ao_type='correction';
				else if(!$page['ao_type'] && $page['premium_option']!='0' && $page['premium_option']!='')
					$page_ao_type='premium';
				else if(!$page['ao_type'] && $page['premium_option']=='0')
					$page_ao_type='nopremium';
			   if($page['articleid']==$mission_identifier && ($page_ao_type==$mission_type))
                {
					//echo $key.mission_type."--".$page['ao_type'];
                   //echo $key."--".$page['articleid']."--".$identifier."<br>";
                   if($key==0) 
                   {
                      $prev='';
                      
                      $ncnt=$key+1;

                      if($page_array[$ncnt]['articleid'])
                      {
                        $ao_type=$page_array[$ncnt]['ao_type'];
                        $premium_option=$page_array[$ncnt]['premium_option'];
                        $identifier=$page_array[$ncnt]['articleid'];

                        if($ao_type=='correction')
							$ao_type='correction';
						else if(!$ao_type && $premium_option!='0' && $premium_option!='')
                          $ao_type='premium';
                        else if(!$ao_type && $premium_option=='0')
                          $ao_type='nopremium';
                        

                        $next='<a class="rgt" id="to_modal_'.$ncnt.'" href="/contrib/article-details?'.$differed.'misson_type='.$ao_type.'&mission_identifier='.$identifier.'">&rsaquo;</a>';
                      } 
                      else
                         $next='';
                    }  
                    elseif($key==$offerCnt)
                    {
                      $next='';
                      $ncnt=$key-1;

                      if($page_array[$ncnt]['articleid'])
                      {
                        $ao_type=$page_array[$ncnt]['ao_type'];
                        $premium_option=$page_array[$ncnt]['premium_option'];
                        $identifier=$page_array[$ncnt]['articleid'];
						if($ao_type=='correction')
							$ao_type='correction';
						else if(!$ao_type && $premium_option!='0' && $premium_option!='')
                          $ao_type='premium';
                       else if(!$ao_type && $premium_option=='0')
                          $ao_type='nopremium';
                        $prev='<a class="lft" id="to_modal_'.$ncnt.'" href="/contrib/article-details?'.$differed.'misson_type='.$ao_type.'&mission_identifier='.$identifier.'">&lsaquo;</a>';
                      } 
                      else
                         $prev='';
                    }
                    else
                    {
                      
                      $ncnt=$key+1;
                      $pcnt=$key-1;
                      //echo  $page_array[$ncnt]['articleid']."--". $page_array[$pcnt]['articleid'];

                        if($page_array[$ncnt]['articleid'])
                        {
                          $ao_type=$page_array[$ncnt]['ao_type'];
                          $premium_option=$page_array[$ncnt]['premium_option'];
                          $identifier=$page_array[$ncnt]['articleid'];
                        if($ao_type=='correction')
							$ao_type='correction';
						else if(!$ao_type && $premium_option!='0' && $premium_option!='')
                            $ao_type='premium';
                          else if(!$ao_type && $premium_option=='0')
                            $ao_type='nopremium';
                          $next='<a class="rgt" id="to_modal_'.$ncnt.'" href="/contrib/article-details?'.$differed.'misson_type='.$ao_type.'&mission_identifier='.$identifier.'">&rsaquo;</a>';
                        } 
                        else
                           $next='';

                         if($page_array[$pcnt]['articleid'])
                          { 
                            $ao_type=$page_array[$pcnt]['ao_type'];
                            $premium_option=$page_array[$pcnt]['premium_option'];
                            $identifier=$page_array[$pcnt]['articleid'];
                            if($ao_type=='correction')
								$ao_type='correction';
							else if(!$ao_type && $premium_option!='0' && $premium_option!='')
                              $ao_type='premium';
                           else if(!$ao_type && $premium_option=='0')
                              $ao_type='nopremium';
                            $prev='<a class="lft" id="to_modal_'.$pcnt.'" href="/contrib/article-details?'.$differed.'misson_type='.$ao_type.'&mission_identifier='.$identifier.'">&lsaquo;</a>';
                          } 
                          else
                             $prev='';
                    }
                      
                }
            }

              
            $paginate.= $prev.$next;
            $paginate.='</div>';
         }   
        //echo $_SERVER['HTTP_USER_AGENT'];
        if(!preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT']))
        {
          echo $paginate;
        } 
         //return $paginate;
    }


    function quiz_paginate($questions,$question_id=NULL)
    {
        $next='';

        $questionCount=count($questions)-1;
        $total_questionCount=count($questions);

        //echo "<pre>";print_r($page_array);

        if($total_questionCount>0)
        {
            $paginate='';

            foreach($questions as $key=>$page)
            {
                if(!$question_id)
                    $question_id=$questions[0]['id'];
                if($page['id']==$question_id)
                {
                   //echo $key."--".$page['articleid']."--".$identifier."<br>";
                   if($key==0) 
                   {
                      $prev='';
                      
                      $ncnt=$key+1;

                      if($questions[$ncnt]['id'])
                      {
                         $next=$questions[$ncnt]['id'];
                      } 
                      else
                         $next='';
                    }  
                    elseif($key==$questionCount)
                    {
                      $next='';                      
                    }
                    else
                    {
                       $ncnt=$key+1;        
                       if($questions[$ncnt]['id'])
                       {
                         $next=$questions[$ncnt]['id'];
                       }                         
                    }
                    return array("current"=>$key,"next"=>$next,"total"=>($questionCount+1));
                      
                }
            }              
            
         }   
        
    }

    //get date of next day(s) from today
    function getDateNextDays($days)
    {
      $date_format = 'Y-m-d 23:59:59';      
      $timestamp = strtotime('+'.$days.' day');
      //echo date($date_format,$timestamp);
      return $timestamp;//date($date_format,$nextdays);

    }

    //Convert timeZone of publish time
    function converLocalTimeZone($format,$timestamp)
    {
        setlocale(LC_TIME, "en_US");

        $local_time_zone=$_COOKIE['local_timezone'];
		
        if(!$local_time_zone)
		{
			$local_time_zone=system('date +%Z');
			ob_clean(); 
		}
		// $local_time_zone=ini_get('date.timezone');
		//echo $format." ".$timestamp;
		$date = new DateTime("@".$timestamp);
		$date->setTimezone(new DateTimeZone($local_time_zone));  
        return strftime($format,strtotime($date->format('Y-m-d H:i:s')));
    }
    function isodec($string)
    {
      $string=iconv("ISO-8859-1","UTF-8",$string);
      $string=utf8_decode($string);
      return $string;
    }

    ///////////critsend mail sending action/////
function critsendMail($emailfrom, $emailto, $subject, $msg)
{
    require_once('critsendmailconnector.php');
    $mxm = new MxmConnect();

    $content = array('subject'=> $subject, 'html'=> $msg, 'text' =>'');

    $param = array('tag'=>array('invoice1'), 'mailfrom'=> $emailfrom, 'mailfrom_friendly'=> 'Support Edit-place', 'replyto'=>$emailfrom, 'replyto_filtered'=> 'true');

    $emails[0] = array('email'=>$emailto, 'field1'=> 'test');

    try {

        $mxm->sendCampaign($content, $param, $emails);
    } catch (MxmException $e) {
        echo $e->getMessage(); }
}

function secondsToTime($seconds)
{
    return gmdate("H:i:s",$seconds);
}

 function currencyToDecimal($string)
    {
        $string=str_replace(",",".",$string);
        return $string;
    }
	
	/**
	  * Function write_Xlsx to write xlsx files and return file
	  * It will rrequired PHP Excel class 
	  * The Values will be given with decoded format for  linux with different decoding pattern and for windows it will be not changed	  
	  *
	  */
    function writeXlsx($data,$file_path)
    {    	 
		/** PHPExcel */
        include_once CUSTOM_SCRIPT_PATH.'/PHPExcel.php';
        
        /** PHPExcel_Writer_Excel2007 */
        include_once CUSTOM_SCRIPT_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        /* Create new PHPExcel object*/
        $objPHPExcel = new PHPExcel();
        
        /* Set properties*/
        $objPHPExcel->getProperties()->setCreator("Edit-Place");
        
        /* Add some data */
        $objPHPExcel->setActiveSheetIndex(0);

        $rowCount=0;
        foreach ($data as $row)
        {
           
			$col = 'A';
            foreach ($row as $key => $value)
            {	/* Based on OS Apply Encoding */
				if (getOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
                {      
					$value = iconv("ISO-8859-1", "UTF-8", $value) ;
					$value = str_replace("", htmlentities("œ"), $value) ;
					$value = str_replace("", "'", $value) ;
					$value = str_replace("", "'", $value) ;
					$value = html_entity_decode(htmlentities($value,  ENT_QUOTES, 'UTF-8'), ENT_QUOTES ,mb_detect_encoding($value));
					$value=html_entity_decode($value);exit;
					//$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
					//$value = isset($value) ? html_entity_decode(htmlentities($value,ENT_QUOTES,"UTF-8")) : '';
                        
				}				
				$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                $col++;
            }
            $rowCount++;
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ; 
        
        if(file_exists($file_path))
            return true ;
    }
	function getOS($userAgent) {
        // Create list of operating systems with operating system name as array key
        $oses = array('iPhone' => '(iPhone)', 'Windows' => 'Win16', 'Windows' => '(Windows 95)|(Win95)|(Windows_95)', // Use regular expressions as value to identify operating system
        'Windows' => '(Windows 98)|(Win98)', 'Windows' => '(Windows NT 5.0)|(Windows 2000)', 'Windows' => '(Windows NT 5.1)|(Windows XP)', 'Windows' => '(Windows NT 5.2)', 'Windows' => '(Windows NT 6.0)|(Windows Vista)', 'Windows' => '(Windows NT 6.1)|(Windows 7)', 'Windows' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)', 'Windows' => 'Windows ME', 'Open BSD' => 'OpenBSD', 'Sun OS' => 'SunOS', 'Linux' => '(Linux)|(X11)', 'Safari' => '(Safari)', 'Macintosh' => '(Mac_PowerPC)|(Macintosh)', 'QNX' => 'QNX', 'BeOS' => 'BeOS', 'OS/2' => 'OS/2', 'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)');

        foreach ($oses as $os => $pattern) {// Loop through $oses array

            // Use regular expressions to check operating system type
            if (strpos($userAgent, $os)) {// Check if a value in $oses array matches current user agent.
                return $os;
                // Operating system was matched so return $oses key
            }
        }
        return 'Unknown';
        // Cannot find operating system so return Unknown
    }
	
	// Get Colorcode to display in progress bar
    function getColor($percentage)
    {
        if($percentage>=0 && $percentage<15)
                $offset = '#ff0000';
        else if($percentage>=15 && $percentage<25)
            $offset = '#ff7200';
        else if($percentage>=25 && $percentage<35)
            $offset = '#ffa200';
        else if($percentage>=35 && $percentage<50)
            $offset = '#ffd21d';
        else if($percentage>=60 && $percentage<80)
            $offset = '#f2f43c';
        else if($percentage>=80 && $percentage<90)
            $offset = '#cbf43c';
        else 
            $offset = '#5eb95e';
        return $offset;
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
	/* To generate XLSX File */
	function convertHtmltableToXlsx($htmltable,$filename,$extract=FALSE)
	{		
		require_once APP_PATH_ROOT.'nlibrary/script/PHPExcel.php';
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
					$headrows[$h]['bold'][] = findBoldText(innerHTML($ths->item($x)));
					if($ths->item($x)->hasAttribute('style')) {
					  $style = $ths->item($x)->getAttribute('style');
					  $stylecolor = findStyleColor($style);
					  if($stylecolor == '') {
						$headrows[$h]['color'][] = findSpanColor(innerHTML($ths->item($x)));
					  }else{
						$headrows[$h]['color'][] = $stylecolor;
					  }
					  $fontsize = findFontSize($style);
					  if($fontsize=='')
					   $headrows[$h]['size'][] = 11;
					  else
					   $headrows[$h]['size'][] = $fontsize;
					}else{
					  $headrows[$h]['color'][] = findSpanColor(innerHTML($ths->item($x)));
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
					$bodyrows[$r]['bold'][] = findBoldText(innerHTML($tds->item($x)));
					if($tds->item($x)->hasAttribute('style')) {
					  $style = $tds->item($x)->getAttribute('style');
					  $stylecolor = findStyleColor($style);					  
					  if($stylecolor == '') {
						$bodyrows[$r]['color'][] = findSpanColor(innerHTML($tds->item($x)));
					  }else{
						$bodyrows[$r]['color'][] = $stylecolor;
					  }
					  $fontsize = findFontSize($style);
					  if($fontsize=='')
					   $bodyrows[$r]['size'][] = 10;
					  else
					   $bodyrows[$r]['size'][] = $fontsize;
					}else{
					  $bodyrows[$r]['color'][] = findSpanColor(innerHTML($tds->item($x)));
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
		//echo "<pre>";print_r($objPHPExcel);exit;
		// $objPHPExcel->setActiveSheetIndex(0);   
//exit;		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($filename);
	}
?>