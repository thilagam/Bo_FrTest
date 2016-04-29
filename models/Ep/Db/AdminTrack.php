<?

class Ep_Db_AdminTrack 
{
	
	/**
	 * String Variable
	 * @var String $_filePathName holds the xml file path
	 */
	var $_filePathName;
	
	/**
	 * Array Variable
	 * @var Array $data holds the attributes for a node
	 */
	var $data = array();
	
	/**
	 * SimpleXMLElement object
	 * @var SimpleXMLElement $_xmlElement 
	 */
	var $_xmlElement;

	/**
	 * Constructor
	 * loads the contents of a XML file and creates an instance of the SimpleXMLElement class
	 * @param String $filePath holds file path name for the xml file to be loaded
	 * @param String $rootName root node name
	 * @return SimpleXmlElement
	 */
	function __construct($filePath, $rootName = 'root')
	{
		try
		{
			$fs = @fopen($filePath, "r");
		} 
		catch (exception $e)
		{
			echo $e->getTrace();
		}
		if ($fs == false)
		{
			//@fclose($fs);
			$fh = @fopen($filePath, 'w+');
			$contents = '<?xml version="1.0" encoding="UTF-8"?>';
			$contents .= '<' . $rootName . '> </' . $rootName . '>';
			//$contents = '<'.$rootName.'> </'.$rootName.'>';
			@fwrite($fh, $contents);
			
			$this->_filePathName = $filePath;
			$xml = simplexml_load_file($filePath);
			$this->setXmlElement(@simplexml_load_file($filePath));
			@fclose($fh);
		} 
		else
		{
			$this->_filePathName = $filePath;
			$this->setXmlElement(@simplexml_load_file($filePath));
		}
	}
	
	protected function setXmlElement($sxe)
	{
		$this->_xmlElement = $sxe;
	}
	protected function getXmlElement()
	{
		return $this->_xmlElement;
	}
	public function saveXMLChanges($xmlFile)
	{
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $xmlFile->asXML());
		@fclose($fp);
	}
	
	public function insert($lang,$id,$corId,$page,$url,$inTime)
	{
		$xml = $this->getXmlElement();
		$a = @$xml->xpath("/root/identifier[@id=".$id."]");
		if(empty($a[0]))
		{
			$a = $xml->addChild("identifier");
			$a->addAttribute("lang",$lang);
			$a->addAttribute("id",$id);
			$a->addAttribute("corId",$corId);
			$b = $a->addChild("pageName", $page);
			$b->addAttribute("url",$url);
			$b->addAttribute("inTime",$inTime);
			$b->addAttribute("outTime","");
		}
		else 
		{
			$b = $a[0]->addChild("pageName", $page);
			$b->addAttribute("url",$url);
			$b->addAttribute("inTime",$inTime);
			$b->addAttribute("outTime","");
		}
		$this->saveXMLChanges($xml);
	}
	
	public function update($id,$page,$outTime)
	{
		$xml = $this->getXmlElement();
		$a = @$xml->xpath("/root/identifier[@id='".$id."' and pageName='".$page."']/pageName");
		//print_r($a);
		$i = 0;
		if(!empty($a))
		foreach ($a as $b)
		{
			if($b["outTime"] == "")
			$b["outTime"]=$outTime;
			//echo 'B '.$b;
			$i++;
		}
		//print_r($a);
		$this->saveXMLChanges($xml);
	}
	
	public function lasttimeoutupdate($id,$page,$outTime)
	{
		$xml = $this->getXmlElement();
		$a = @$xml->xpath("/root/identifier[@id='".$id."']/pageName[pageName='".$page."']");
		//print_r($a);
		$i = 0;
		if(!empty($a))
		foreach ($a as $b)
		{
			if($b["outTime"] == "")
			$b["outTime"]=$outTime;
			//echo 'B '.$b;
			$i++;
		}
		//print_r($a);
		$this->saveXMLChanges($xml);
	}
	
	public function add2times($time1, $time2)
	{
		$resulttime  = date("his",mktime(date($time1['hour'])+$time2['hour'] , date($time1['minutes'])+$time2['minutes'], date($time1['seconds'])+$time2['seconds']));
		return $resulttime;
	}

	public function timeDiff($firstTime,$lastTime)
	{
		// convert to unix timestamps
		if($lastTime == '' || $lastTime == '00:00:00')
		return 0;
		
		$firstTime=strtotime($firstTime);
		$lastTime=strtotime($lastTime);
		
		if($firstTime > $lastTime)
		return 0;
		
		// perform subtraction to get the difference (in seconds) between times
		$timeDiff=$lastTime-$firstTime;
		
		// return the difference
		return $timeDiff;
	}
	
	
	public function get_time_difference( $start, $end )
	{
	    $uts['start']      =    strtotime( $start );
	    $uts['end']        =    strtotime( $end );
	    if($uts['end'] == '')
    	$uts['end'] = $uts['start'];
	    if( $uts['start']!==-1 && $uts['end']!==-1 )
	    {
	        if( $uts['end'] >= $uts['start'] )
	        {
	            $diff    =    $uts['end'] - $uts['start'];
	            if( $hours=intval((floor($diff/3600))) )
	                $diff = $diff % 3600;
	            if( $minutes=intval((floor($diff/60))) )
	                $diff = $diff % 60;
	            $diff    =    intval( $diff );            
	            return( array('hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
	        }
	        else
	        {
	            //trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
	        }
	    }
	    else
	    {
	        //trigger_error( "Invalid date/time data detected", E_USER_WARNING );
	    }
	    return( false );
	}
	
	public function getLogdetail1($corid,$file,$lang)
	{
		foreach ($corid as $id)
		{
			$obj = array();
			foreach ($file as $xpat)
			{
				$xpath = str_replace(".xml","",$xpat);
				$objf = new Date($xpath);
				$xpath = $objf->getDateFormatedForAdmin();
				unset($objf);
				$tempObj = @simplexml_load_file(DATA_PATH.'/CorrLog/'.$xpat);
				if($tempObj != FALSE)
				$obj[$xpath] = $tempObj;
			}
			
			$query = "/root/identifier[@corId=$id and @lang='".$lang."']/pageName";
			$res = array();
			$tempTime = $sumAvg = 0;
			foreach ($obj as $key => $q)
			{
				$result = $q->xpath($query);
				if(!empty($result))
				{
					$i = 0;
					foreach ($result as $simObj)
					{
						$getT = $this->get_time_difference($simObj['inTime'],$simObj['outTime']);
						$tempTime	= $tempTime + $this->timeDiff($simObj['inTime'],$simObj['outTime']);
						$simObj['TotalHr'] = $this->timeDiff($simObj['inTime'],$simObj['outTime']);
					}
					foreach ($result as $simObj)
					{
						//echo 'Time '. $simObj['TotalHr'] .'<br> Temp '.$tempTime.' TimeSec '.(strtotime($simObj['TotalHr']));
						//echo $simObj['TotalHr']."-".$tempTime."<br/>";
						if($tempTime != 0)
						{
							$number = ($simObj['TotalHr']/$tempTime)*100;
							$english_format_number = number_format($number, 2, '.', '');
							$simObj['avg'] = $english_format_number;
						}
						//$sumAvg+= $number; 
					}
					//echo 'Avg '.$sumAvg;
					unset($tempTime);
					$res[$key] = $result;
				}
				//print_r($result);
			}	
			$resd[$id] = $res;
		}
		return $resd;
	}
		
	
	public function getLogdetail($corid,$file,$lang)
	{
		foreach ($corid as $id)
		{
			$obj = array();
			foreach ($file as $xpat)
			{
				$xpath = str_replace(".xml","",$xpat);
				$objf = new Date($xpath);
				$xpath = $objf->getDateFormatedForAdmin();
				unset($objf);
				$tempObj = @simplexml_load_file(DATA_PATH.'/CorrLog/'.$xpat);
				if($tempObj != FALSE)
				$obj[$xpath] = $tempObj;
			}
			
			$query = "/root/identifier[@corId=$id and @lang='".$lang."']/pageName";
			
			$queryForinTime = "/root/identifier[@corId=$id and @lang='".$lang."']/pageName[1]";
			
			//$queryForoutTime = "/root/identifier[@corId=$id and @lang='".$lang."']/pageName[last()]";
			
			$res = array();
			$tempTime = $sumAvg = 0;
			
			foreach ($obj as $key => $q)
			{
				$result = $q->xpath($query);
				$arrAddPage = array();
				$arrRemovePage = array();
				
				$resultinTime = $q->xpath($queryForinTime);
				//$resultoutTime = $q->xpath($queryForoutTime);
				
				if(!empty($resultinTime))
				{
					foreach ($resultinTime as $in)
					{
						$inTime = $in['inTime'];
						break;
					}
				}
				$tempoutTime = NULL;
				/*if(!empty($resultoutTime))
				{
					$a = count($resultoutTime);
					echo '</br>Count '.$a;
					$i = 0;
					foreach ($resultoutTime as $as)
					{
						if($i == ($a-1))
						{
							if((string)$as['outTime'] == '')
							$outTime = $tempoutTime;
							else 
							$outTime = $as['outTime'];
							break;
						}
						$tempoutTime = (string)$as['outTime'];
						$i++;
					}
				}*/
				//count no. of entries for each page 
				$a = count($result);
				//echo '</br>Count '.$a;
				$i = 0;
				if(!empty($result))
				{
					foreach ($result as $simObj)
					{
						$getT = $this->get_time_difference($simObj['inTime'],$simObj['outTime']);
						$tempTime	= $tempTime + $this->timeDiff($simObj['inTime'],$simObj['outTime']);
						$totalTime = $this->timeDiff($simObj['inTime'],$simObj['outTime']);
						$simObj['TotalHr'] = $totalTime;
						$page = (string)$simObj[0];
						if(!array_key_exists($page, $arrAddPage))
						$arrAddPage[$page] = $totalTime;
						else 
						$arrAddPage[$page] = $arrAddPage[$page] + $totalTime;
						
						if($i == ($a-1))
						{
							if((string)$simObj['outTime'] == '')
							$outTime = $tempoutTime;
							else 
							$outTime = $simObj['outTime'];
						}
						$tempoutTime = (string)$simObj['outTime'];
						$i++;
					}
					//calculate total average per page
					$arrAvg = array(); 
					foreach ($result as $simObj)
					{
						if($tempTime != 0)
						{
							$number = ($simObj['TotalHr']/$tempTime)*100;
							$english_format_number = number_format($number, 2, '.', '');
							$totalAvg = $english_format_number;
							$page = (string)$simObj[0];
							if(!array_key_exists($page, $arrAvg))
							$arrAvg[$page] = $totalAvg;
							else 
							$arrAvg[$page] = $arrAvg[$page] + $totalAvg;
						}
					}
					
					$i = 0;
					foreach ($result as $simObj)
					{
						$page = (string)$simObj[0];
						if(in_array($page, $arrRemovePage))
						{
							unset($result[$i]);
						}
						else 
						{
							$arrRemovePage[] = $page;
							$simObj['pageTotal'] = $arrAddPage[$page];
							$simObj['pageAvg'] = $arrAvg[$page];
						}
						$i++;
					}
					unset($totalAvg);					
					unset($tempTime);
					unset($arrAddPage);
					unset($arrRemovePage);
					$result['track'] = $inTime.'-'.$outTime;
					$res[$key] = $result;
					//array_push($res[$key], $inTime, $outTime);
					unset($resultinTime);					
					unset($resultoutTime);
				}
					
				//print_r($res);
			}	
			$resd[$id] = $res;
		}
		return $resd;
	}
}