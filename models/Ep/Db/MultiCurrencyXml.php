<?php

/**
 * Ep_Db_MultiCurrency
 * 
 * This class provides all necessary xml methods for currency
 * 
 * @date 09 Dec 09
 * @author Aravind
 * @package Db
 * @version 1.0
 */


class Ep_Db_MultiCurrencyXml extends Ep_Db_Identifier
{
 	private $xml;
 	private $lang;
 	private $name = 'PRICE';
 	private $table2 = 'EXCHANGERATE';
 	
 	private $filePathName = '/home/sites/site6/users/xmldb/currencydetails.xml';
 	
    public function __construct($xmlFile='/home/sites/site6/users/xmldb/currencydetails.xml')
    {
		parent::__construct();
		
		if($this->tableName == NULL)
			$this->_setupTableName($this->name);
		else
			$this->_setupTableName($tableName);			
	
    	if(!file_exists($xmlFile))
        {
            throw new Exception('Invalid XML file');
        }
        // read XML file
        if(!$this->xml=simplexml_load_file($xmlFile))
        {
            throw new Exception('Error reading XML file');
        }		
    }    
    
    
    // fetch specific nodes according to node name
    public function fetchNodes($nodeName)
    {
        if(!$nodeName){
            throw new Exception('Invalid node name');
        }
        $nodes=array();
        foreach($this->xml as $node){
            $nodes[]=$node->$nodeName;
        }
        return $nodes;
    }
    
    
    // fetch all nodes as array of objects
    public function fetchNodesAsObjects()
    {
        $nodes=array();
        foreach($this->xml as $node){
            $nodes[]=$node;
        }
        return $nodes;
    }
    

    // count nodes of type $nodeName
    public function countNodes($lang)
    {
    	$xmlstr = $this->xml;
    	return count($xmlstr->$lang->details);
    	/*    	 
    	$count=0;
		foreach($xmlstr->$lang->details as $val)
		{
			$count++;
		}
		return $count;
		*/
    }
    
    

    //gets only enabled (name) and (column name) pair 
    public function getcolNamePair($lang)
    {
    	$colNamePair = array();
    	$nameArray = array();
		$colArray = array();
		$count=0;
		
		$xmlstr = $this->xml;
		
   	 	foreach ($xmlstr->$lang->details as $val)
		{
			if($val->enable == '1')
			{	
				//$nameArray[] = utf8_encode((iconv("ISO-8859-1", "UTF-8", (string)$val->name)));	//(string)$val->name;
				$nameArray[] = (string)$val->name;
				$colArray[] = (string)$val->columnname;
			}			
		}
		
		$colNamePair = array_combine($colArray, $nameArray);
		
		return $colNamePair;
    }

    
 	public function getvalNamePair($lang)
    {
    	$valNamePair = array();
    	$nameArray = array();
		$valArray = array();
		$count=0;
		
		$xmlstr = $this->xml;
		
   	 	foreach ($xmlstr->$lang->details as $val)
		{
			//$nameArray[] = utf8_encode((iconv("ISO-8859-1", "UTF-8", (string)$val->name)));	//(string)$val->name;
			$nameArray[] = (string)urldecode($val->name);
			$valArray[] = (string)$val->value;	
		}
		
		$valNamePair = array_combine($valArray, $nameArray);
		
		return $valNamePair;
    }

    public function getValue($colname,$lang)
    {
    	$xmlstr = $this->xml;
		
   	 	foreach ($xmlstr->$lang->details as $val)
   	 	{
   	 		if($val->columnname == $colname)
			{
				$value = (string)$val->value;
			}			
   	 	}
   	 	return $value;
    
    } 
    
    
    public function readxml()
	{
		$xmlstr = $this->xml;

		//print_r($xmlstr);
		return $xmlstr;	    
	}
	

	public function readname($val)
	{

        $nodes = $this->fetchNodes($val);
        $currrencyNames = array();
        
		foreach($nodes as $name)
	        $names[] = (string)$name;
	    
	    //print_r($currrencyNames);
		return $names;		
    }
    
    
    
    public function readvalue($lang)
    {
    	$count = 0;
    	foreach ($this->xml as $a)
		{
			if($a[0]->getName() == "$lang")
			{
				foreach($a[0] as $b)
				{
					if($b[0]->getName() == 'details')
						$value[] = (string)$this->xml->$lang->details[$count]->value;
						
				$count++;
				}	
			}
		}	

		return $value;
    }
    
    
    public function readcurrencycodes($lang)
    {
    	$count = 0;
    	foreach ($this->xml as $a)
		{
			if($a[0]->getName() == "$lang")
			{
				foreach($a[0] as $b)
				{
					if($b[0]->getName() == 'details')
						$value[] = (string)$this->xml->$lang->details[$count]->currencycode;
						
				$count++;
				}	
			}
		}	

		return $value;
    }
    
    
    public function readsymbol($colname,$lang)
    {
    	$xmlstr = $this->xml;
		if($lang == 'en' || $lang == 'fr')	//there was an error in PT site. So included if condition
		{
	   	 	foreach ($xmlstr->$lang->details as $val)
	   	 	{
	   	 		if($val->columnname == $colname)
					$symbol = (string)urldecode($val->symbol);		
	   	 	}
	
			//$symbol = utf8_decode(utf8_decode(iconv("ISO-8859-1", "UTF-8", $symbol)));		
			return $symbol;
		}
    }
    

    public function readallsymbol($lang)
    {
   		$xmlstr = $this->xml;
		$symbol = array();

		if($lang == 'en' || $lang == 'fr')	//there was an error in PT site. So included if condition
		{
	   	 	foreach ($xmlstr->$lang->details as $val)
	   	 	{
	   	 		$indx = (string)$val->value;
				//$symbol[$indx] = utf8_decode(utf8_decode(iconv("ISO-8859-1", "UTF-8", $val->symbol)));
				$symbol[$indx] = urldecode($val->symbol);		
	   	 	}
			return $symbol;
		}
    }
    
    
    public function getcurrencycode($colname,$lang)
    {
    	$xmlstr = $this->xml;
				
   	 	foreach ($xmlstr->$lang->details as $val)
   	 	{
   	 		if($val->columnname == $colname)
			{
				$currencycode = (string)$val->currencycode;
				break;
			}
   	 	}
   	 	
   	 	return $currencycode;
    }
    
	public function getcurrencycodeVal($country,$lang)
    {
    	$xmlstr = $this->xml;
				
   	 	foreach ($xmlstr->$lang->details as $val)
   	 	{
   	 		if($val->value == $country)
			{
				$currencycode = (string)$val->currencycode;
				break;
			}
   	 	}
   	 	return $currencycode;
    }
	public function getSymbolVal($country,$lang)
    {
    	$xmlstr = $this->xml;				
   	 	foreach ($xmlstr->$lang->details as $val)
   	 	{
   	 		if($val->value == $country)
			{
				$symbol = (string)$val->symbol;
				return urldecode($symbol);
			}
   	 	}

    }
    public function getColumnnameSymbol($currencycode,$lang)
    {
    	$xmlstr = $this->xml;
		$returnArr = array();
		
   	 	foreach ($xmlstr->$lang->details as $val)
   	 	{
   	 		if($val->currencycode == $currencycode)
			{
				$returnArr[columnname] = (string)$val->columnname;
				$returnArr[symbol] = (string)urldecode($val->symbol); 
				break;
			}
   	 	}
   	 	
   	 	
   	 	return $returnArr;
    }

    public function readcolumnname($value,$lang)
	{
    	foreach ($this->xml as $a)
		{
			if($a[0]->getName() == "$lang")
			{
				foreach($a[0] as $b)
				{
					if($b[0]->getName() == 'details' && $b[0]->value == $value)
					{
						$val = (string)$b[0]->columnname;
						break 2;
					}
					else
					{
						if($lang == 'fr')
							$val = "customerPriceEur";	//defalut column for languages EN and FR
						else
							$val = "customerPriceUS";	//defalut column for languages EN and FR
					}
				}	
			}
			else
			{
				$val = "customerPrice";	//defalut column for languages other than EN and FR
			}	
		}	
		return $val;
	} 

	private function roundto($number,$val=NULL)
	{
		if($val == 'JPN' || $val == 'HUN' || $val == 'TWN')
			$num1 = round($number);
		else
			$num1 = number_format($number, 2,'.','');
    	
    	
		$numw = (string)($num1*100.00);
		$num2 = ((int)$numw)%10.00;
		
		if($num2>5)
		{	
			$to = .10;
			$num3 = round($num1/$to, 0)* $to;
		}	
		elseif($num2<=5 && $num2 !=0)
		{
			$n = (5-$num2)/100;
			$num3 = $num1+$n;
			
		}
		elseif ($num2==0)
			$num3=$num1;
		
		//	echo $num3;
		
		//suneel
		$new= round($num3);
		if($new < $num3) $num3=$new+0.45; else $num3=$new-0.05;
		
    	return number_format($num3, 2,'.','');
    	
	} 

	public function addcurrency($lang,$currencyDetailsArray)
	{		
		//print_r($currencyDetailsArray);
		$xmlstr = $this->xml;
		
		$count=0;
		foreach($xmlstr->$lang->details as $val)
		{
			$count++;
		}
		
		$xmlstr->$lang->details[$count]->name = urlencode($currencyDetailsArray[0]);	//"India";
		$xmlstr->$lang->details[$count]->value = $currencyDetailsArray[1];	//"IND";
		$xmlstr->$lang->details[$count]->symbol = urlencode($currencyDetailsArray[2]);	//"INR";
		$xmlstr->$lang->details[$count]->currencycode = $currencyDetailsArray[3];	
		$xmlstr->$lang->details[$count]->exchangerate = $currencyDetailsArray[4];	//"47.00";
		$xmlstr->$lang->details[$count]->pageprice = "";
		$xmlstr->$lang->details[$count]->columnname = $currencyDetailsArray[5];	//"customerPriceInr";
		$xmlstr->$lang->details[$count]->enable = $currencyDetailsArray[6];	//"1";
				
		$this->xml = $xmlstr;
		
		if(file_put_contents($this->filePathName, $this->xml->asXML()))
		{
		
			$margin = "margin".strtolower($currencyDetailsArray[3]);
			
			//add primary column and column2 to take care of load balancing
			$query = "SELECT '1'; ALTER TABLE `".$this->name."` ADD `".$currencyDetailsArray[5]."` DOUBLE NOT NULL DEFAULT ".$currencyDetailsArray[4].";";
			$query .= "ALTER TABLE `".$this->name."` ADD `".$currencyDetailsArray[5]."2` DOUBLE NOT NULL DEFAULT ".$currencyDetailsArray[4].";";
			$query .= "ALTER TABLE ".$this->table2." ADD `".$currencyDetailsArray[3]."` DOUBLE NOT NULL DEFAULT '".$currencyDetailsArray[4]."';";
			$query .= "ALTER TABLE ".$this->table2." ADD `".$margin."` DOUBLE NOT NULL DEFAULT '5';"; 		
			$res = $this->getQuery($query,FALSE);	
			
			//To automatically add prices based on the category and nbpage
			$xmlstr = $this->xml;
			
			if($lang == 'fr')
				$primaryval = 'FRA';
			elseif($lang == 'en')
				$primaryval = 'USA';
				
			$resArr = $this->displayset($lang,$primaryval);
			
			foreach($resArr as $key)
			{
				if($lang == 'fr')
					$pri = $key[price]*$currencyDetailsArray[4];
				elseif($lang == 'en')
					$pri = $key[nbpage]*$currencyDetailsArray[4];
					
				$price = $this->roundto($pri);
			
				// add new node with value condition
				$querystr = '/currencydetails/'.$lang.'/details[value = "'.$currencyDetailsArray[1].'"]/pageprice';
				$result = $xmlstr->xpath($querystr);
				$result[0]->addChild('page',$price);
				
				$querystr = '/currencydetails/'.$lang.'/details[value = "'.$currencyDetailsArray[1].'"]/pageprice/page';
				$result = $xmlstr->xpath("$querystr");
				$dd = end($result);
				
				$cat = $key[category];
				$dd->addAttribute('nbpage',$key[nbpage]);
				$dd->addAttribute('category',$key[category]);
			}
			
			$this->xml = $xmlstr;
			file_put_contents($this->filePathName, $this->xml->asXML());
			
			return true;
		}	
		else
			return false; 	
		
	} 
	
	public function deletecurrency($value,$lang='en')
	{
		$xmlstr = $this->xml;
		//print_r($xmlstr);
		
		$count=0;
		foreach($xmlstr->$lang->details as $val)
		{
			//print_r($val);
			if($val->value == $value)
			{
				$columnname = $val->columnname;
				$margin = "margin".strtolower($val->currencycode);
				
				$query = "SELECT '1'; ALTER TABLE ".$this->name." DROP `".$columnname."`;";
				$query .= "ALTER TABLE ".$this->name." DROP `".$columnname."2`;";
				$query .= "ALTER TABLE ".$this->table2." DROP `".$val->currencycode."`;";
				$query .= "ALTER TABLE ".$this->table2." DROP `".$margin."`;";
				$res = $this->getQuery($query,FALSE);
				unset($xmlstr->$lang->details[$count]);
			}	
			$count++;
		}
		
		//print_r($xmlstr);
		
		$this->xml = $xmlstr;
		
		if(file_put_contents($this->filePathName, $this->xml->asXML()))
		{
			return true;
		}	
		else
			return false;
	}
	
		
	public function enablecurrency($check,$lang)
	{		
		$xmlstr = $this->xml;
		//print_r($xmlstr->details[2]);
		foreach($xmlstr->$lang->details as $val)
		{
			if($val->value == $check)
			{
				$val->enable = 1;
			}
		}
		//print_r($xmlstr);
		
		$this->xml = $xmlstr;
		
		if(file_put_contents($this->filePathName, $this->xml->asXML()))
		{
			/*$xmlstr = $this->xml;
			//print_r($xmlstr);*/
			return $xmlstr;
		}
		else
			return false; 		
	}
    
	
	public function disablecurrency($check,$lang)
	{	
		$xmlstr = $this->xml;
		//print_r($xmlstr->details[2]);
		foreach($xmlstr->$lang->details as $val)
		{
			if($val->value == $check)
			{
				$val->enable = 0;
			}
		}
		
		$this->xml = $xmlstr;
		
		if(file_put_contents($this->filePathName, $this->xml->asXML()))
		{
			/*$xmlstr = $this->xml;
			//print_r($xmlstr);*/
			return true;
		}
		else
			return false; 	
	}
	
	
	public function updatevaluesforcron($arr,$old_value,$old_columnname,$old_currencycode,$lang)
	{	
		$nodecimal=array('TWD','HUF','JPY');
		if($arr[value] == "")
		{
			return false;
		}	
		else
		{			
			//To automatically add prices based on the category and nbpage
			$xmlstr = $this->xml;
			
			if($lang == 'fr')
				$primaryval = 'FRA';
			elseif($lang == 'en')
				$primaryval = 'USA';
				
			$resArr = $this->displayset($lang,$primaryval);
			//echo "resarr<br>";
			//print_r($resArr);
			
			$querystr2 = '/currencydetails/'.$lang.'/details[currencycode = "'.$old_currencycode.'"]';
			$result2 = $xmlstr->xpath("$querystr2");
			$result2[0]->exchangerate=$arr[exchangerate];
			
			$querystr = '/currencydetails/'.$lang.'/details[value = "'.$arr[value].'"]/pageprice/page';
			$result = $xmlstr->xpath("$querystr");
			
			if($lang == 'en')
			{
				if($old_value != 'USA')
				{
					$count=0;
					foreach($resArr as $key)
					{
						$pri = $key[price]*$arr[exchangerate];
						$price = $this->roundto($pri,$old_value);
						//echo $price."--".;
						if(in_array($old_currencycode,$nodecimal))$price = round($price);
						$result[$count][0] = $price;
						$count++;
					}
				}					
			
			}
			elseif($lang == 'fr')
			{
				if($old_value != 'FRA')
				{
					$count=0;
					foreach($resArr as $key)
					{
						$pri = $key[price]*$arr[exchangerate];
						$price = $this->roundto($pri,$old_value);
						echo "--".$price."--";	
						$result[$count][0] = $price;
						$count++;
					}
				}					
			}
			$this->xml = $xmlstr;
			//echo "<br><br>xmldata<br><br>";
			//echo $this->xml->asXML();
			if(file_put_contents($this->filePathName, $this->xml->asXML()))
				$updatedPrices = $this->fetchprices($result[$i][category],$arr[value],$lang);
			
			return $updatedPrices;
		}
	}
	
	
	public function updatevalues($arr,$old_value,$old_columnname,$old_currencycode,$lang)
	{	
		if($arr[value] == "")
		{
			return false;
		}	
		else
		{
			$xmlstr = $this->xml;		
			
			foreach($xmlstr->$lang->details as $val)
			{				
				if($val->value == $old_value)
				{
					$val->name = urlencode($arr[name]);//$arr[name];
					$val->value = $arr[value];
					$val->symbol = urlencode($arr[symbol]);//$arr[symbol];
					$val->currencycode = $arr[currencycode];
					$val->exchangerate = $arr[exchangerate];
					$val->columnname = $arr[columnname];
				}
			}
			
			$this->xml = $xmlstr;
			
			if(file_put_contents($this->filePathName, $this->xml->asXML()))
			{
				$old_margin = "margin".strtolower($old_currencycode);
				$new_margin = "margin".strtolower($arr[currencycode]);
				
				$exRate1 = (double)$arr[exchangerate];
				$exRate =  number_format($exRate1, 2,'.','');
				
				$query = "SELECT '1'; ALTER TABLE ".$this->name." CHANGE `".$old_columnname."` `".$arr[columnname]."` DOUBLE NOT NULL DEFAULT '".$exRate."';";
				$query .= "ALTER TABLE ".$this->name." CHANGE `".$old_columnname."2` `".$arr[columnname]."2` DOUBLE NOT NULL DEFAULT '".$exRate."';";
				$query .= "ALTER TABLE `".$this->table2."` CHANGE `".$old_currencycode."` `".$arr[currencycode]."` DOUBLE NOT NULL DEFAULT '".$exRate."';"; 
				$query .= "ALTER TABLE `".$this->table2."` CHANGE `".$old_margin."` `".$new_margin."` DOUBLE NOT NULL DEFAULT '5.00';";
				
				$res = $this->getQuery($query,FALSE);
				
				//To automatically add prices based on the category and nbpage
				$xmlstr = $this->xml;
				
				if($lang == 'fr')
					$primaryval = 'FRA';
				elseif($lang == 'en')
					$primaryval = 'USA';
					
				$resArr = $this->displayset($lang,$primaryval);
				
				$querystr = '/currencydetails/'.$lang.'/details[value = "'.$arr[value].'"]/pageprice/page';
				$result = $xmlstr->xpath("$querystr");
				
				if($lang == 'en')
				{
					/*for($i=0;$i<count($result);$i++)
					{
						$pri = $result[$i][nbpage]*$arr[exchangerate];
						$price = $this->roundto($pri,$old_value);	
						$result[$i][0] = $price;
					
						//$result[$i][0] = $result[$i][nbpage]*$arr[exchangerate];
					}*/
				
					if($old_value == 'USA')
					{
						for($i=0;$i<count($result);$i++)
						{
							$pri = $result[$i][nbpage]*$arr[exchangerate];
							$price = $this->roundto($pri);	
							$result[$i][0] = $price;
						
							//$result[$i][0] = $result[$i][nbpage]*$arr[exchangerate];
						}
					}
					else
					{
						$count=0;
						foreach($resArr as $key)
						{
							$pri = $key[price]*$arr[exchangerate];
							$price = $this->roundto($pri,$old_value);	
							$result[$count][0] = $price;
							$count++;
						}
					}					
				
				}
				elseif($lang == 'fr')
				{
					if($old_value == 'FRA')
					{
						for($i=0;$i<count($result);$i++)
						{
							$pri = $result[$i][nbpage]*$arr[exchangerate];
							$price = $this->roundto($pri);	
							$result[$i][0] = $price;
						
							//$result[$i][0] = $result[$i][nbpage]*$arr[exchangerate];
						}
					}
					else
					{
						$count=0;
						foreach($resArr as $key)
						{
							$pri = $key[price]*$arr[exchangerate];
							$price = $this->roundto($pri,$old_value);	
							$result[$count][0] = $price;
							$count++;
						}
					}					
				}
				
				$this->xml = $xmlstr;
				//file_put_contents($this->filePathName, $this->xml->asXML());
				
				if(file_put_contents($this->filePathName, $this->xml->asXML()))
					$updatedPrices = $this->fetchprices($result[$i][category],$arr[value],$lang);
				
				return $updatedPrices;
			}
			else
				return false; 
		}
	} 
	
	public function fetchprices($category=NULL,$country,$lang)
	{
		$xmlstr = $this->xml;	

		foreach($xmlstr->$lang->details as $val)
		{				
			if($val->value==$country)
			{
			  if($lang=='fr')
			  {
			  	$querystr = '/currencydetails/'.$lang.'/details[value = "'.$country.'"]/pageprice/page[@category="'.$category.'"]';
			  }
			  elseif($lang == 'en' || $lang == 'pt')
			  {
			  	$querystr = '/currencydetails/'.$lang.'/details[value = "'.$country.'"]/pageprice/page';
			  }
				
				$result = $xmlstr->xpath("$querystr");
				
				foreach($result as $key=>$val)
				{	
					  $arrk[$key] = (string)$result[$key]['nbpage'];		
					  $arrc[$key] = (string)$result[$key]['category'];	
					  $arrp[$key] = (string)$val;
				}
				
				if($arrc != NULL)
				{
					asort($arrc);
				}
				else
				{
					return NULL;
					exit();
				}
				//print_r($arrc);
		
				foreach($arrc as $key=>$val)
					$nbar[$val][$arrk[$key]] = $key; 
				
				$count=0;
				foreach($nbar as $key=>$val)
				{
					ksort($val);
								
					foreach($val as $k=>$v)
					{						
						$pric1 = (double)$arrp[$v];
						$pric = number_format($pric1, 2,'.','');
						
						$finalres[$k] = $pric;
						$count++;
					}
				}				
			}
		}
		
		//print_r($finalres);
		return $finalres;	
			
	}
	
	
	public function displayset($lang=fr,$value=NULL)
	{
		$xmlstr = $this->xml;
		
		$querystr = '/currencydetails/'.$lang.'/details[value = "'.$value.'"]/pageprice/page[@*]';
		$result = $xmlstr->xpath("$querystr");
		
		foreach($result as $key=>$val)
		{	
			  $arrk[$key] = (string)$result[$key]['nbpage'];		
			  $arrc[$key] = (string)$result[$key]['category'];	
			  $arrp[$key] = (string)$val;
		}
		
		if($arrc != NULL)
		{
			asort($arrc);
		}
		else
		{
			return NULL;
			exit();
		}

		foreach($arrc as $key=>$val)
			$nbar[$val][$arrk[$key]] = $key; 

		$count=0;
		foreach($nbar as $key=>$val)
		{
			ksort($val);
						
			foreach($val as $k=>$v)
			{
				$finalres[$count][nbpage] = (string)$k;				
			 	$finalres[$count][category] = (string)$key;	
			 	$finalres[$count][price] = (string)$arrp[$v];
				$count++;
			}
		}
		
		return $finalres;	
	}
	
	
	public function fetchheadingdetails($lang='en')
	{
		$xmlstr = $this->xml;				
		
		$count = $this->countNodes($lang);
		
		for($d=0;$d<$count;$d++)
		{
			$arr[name] = (string)$xmlstr->$lang->details[$d]->name;
			$arr[value] = (string)$xmlstr->$lang->details[$d]->value;
			$arr[symbol] = (string)urldecode($xmlstr->$lang->details[$d]->symbol);
			$arr[currencycode] = (string)$xmlstr->$lang->details[$d]->currencycode;
			$arr[exchangerate] = (string)$xmlstr->$lang->details[$d]->exchangerate;
			$arr[columnname] = (string)$xmlstr->$lang->details[$d]->columnname;
			
			$farr[$d] = $arr;
		}
		return $farr;
	}
	
	
	public function addcategorypageprice($lang=fr,$country,$nbpage=NULL, $category=NULL,$price=NULL)
	{
		$xmlstr = $this->xml;
		
		// add new node with value condition
		$querystr = '/currencydetails/'.$lang.'/details[value = "'.$country.'"]/pageprice';
		$result = $xmlstr->xpath($querystr);
		
		$result[0]->addChild('page',$price);
		
		$querystr = '/currencydetails/'.$lang.'/details[value = "'.$country.'"]/pageprice/page';
		$result = $xmlstr->xpath("$querystr");
		$dd = end($result);

		
		$dd->addAttribute('nbpage',$nbpage);
		$dd->addAttribute('category',$category);
		
		$this->xml = $xmlstr;
		file_put_contents($this->filePathName, $this->xml->asXML());
	}
	
	
	public function editcategorypageprice($lang=fr,$country,$nbpage=NULL,$category=NULL,$price=NULL,$nbpageNew=NULL, $categoryNew=NULL,$priceNew=NULL)
	{
		$xmlstr = $this->xml;
		
		//	to update a node's attribute
		 $querystr = '/currencydetails/'.$lang.'/details[value = "'.$country.'"]/pageprice/page[@nbpage="'.$nbpage.'" and @category="'.$category.'"]';
		 $result = $xmlstr->xpath($querystr);
		
		
		$result[0]['nbpage'] = $nbpageNew;
		$result[0]['category'] = $categoryNew;
		$result[0][0] = $priceNew;
		
		$this->xml = $xmlstr;
		file_put_contents($this->filePathName, $this->xml->asXML());
			
	}
	

	public function deletecategorypageprice($lang=fr,$country,$nbpage=NULL, $category=NULL,$price=NULL)
	{
		$xmlstr = $this->xml;
		
		$count=0;
		foreach($xmlstr->$lang->details as $val)
		{
			$countcat=0;

			if($val->value == $country)
			{
				foreach($xmlstr->$lang->details[$count]->pageprice->page as $pagedet)
				{					
					if($pagedet[0]['category'] == $category && $pagedet[0]['nbpage'] == $nbpage && $pagedet[0][0] == $price)
					{
						unset($xmlstr->$lang->details[$count]->pageprice->page[$countcat]);
					}
					$countcat++;
				}
			}	
			$count++;
		}
		
		$this->xml = $xmlstr;
		file_put_contents($this->filePathName, $this->xml->asXML());
	}
	
	
	
	

	public function example($lang=fr,$nbpage=NULL, $category=NULL)
	{
		$xmlstr = $this->xml;
		/*$nbpage = 8;
		$category = "EXP";*/
		

		/*$querystr = '/currencydetails/'.$lang.'/details/pageprice/page[@nbpage='.$nbpage.' and @category="'.$category.'"]';
		
		$result = $xmlstr->xpath("$querystr");
		//print_r($result);*/
		
		
		
		$querystr = '/currencydetails/'.$lang.'/details[value = "AUS"]/pageprice/page[@*]';
		$result = $xmlstr->xpath("$querystr");
			
		foreach($result as $key=>$val)
		{	
			$result[$key]['nbpage'];//echo " : ";			
			$result[$key]['category'];
			//echo "<br>";
		}
		//print_r($farr);
			
	
		/*$file = "/home/sites/site6/web/pet.xml";

		// load file
		$xml = simplexml_load_file($file) or die ("Unable to load XML file!");
		
		// access XML data
		echo "Name: " . $xml->name . "\n";
		echo "Age: " . $xml->age . "\n";
		echo "Species: " . $xml->species . "\n";
		echo "Parents: " . $xml->parents->mother . " and " .  $xml->parents->father . "\n"; 
		
		$xml->name = "Sammy aravind";
		$xml->age = 4;
		$xml->species = "snail";
		$xml->parents->mother = "Sue Snail";
		$xml->parents->father = "Sid Snail";
		
		// write new data to file
		file_put_contents($file, $xml->asXML()); 
		
		echo "<br>";echo "<br>";echo "<br>";
		// access XML data
		echo "Name: " . $xml->name . "\n";
		echo "Age: " . $xml->age . "\n";
		echo "Species: " . $xml->species . "\n";
		echo "Parents: " . $xml->parents->mother . " and " .  $xml->parents->father . "\n"; */
	}
}

