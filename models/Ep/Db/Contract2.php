<?php

/**
 * Ep_Db_Contract
 * 
 * This class provides all necessary methods to maintain category details
 * 
 * @date 19 Feb 09
 * @category EditPlace
 * @author Shiva
 * @package Db
 * @version 1.0
 */


class Ep_Db_Contract2
{
	protected $_language = array();
	protected $_filePathName;

	protected $xml_path = "/home/sites/site6/users/xmldb/NewDb";
	protected $live_path = "/home/sites/site6/users/xmldb/livedb";
	
	/**
	 * Constructor
	 * this is used to set xml file path and root node
	 * @param String $filePath file path
	 * @param String $rootName root node name
	 * @return void
	 */
	function __construct($xmlfilePath = '', $rootName = '')
	{
		
	}

	/**
	 * saveXMLChanges
	 * Function to save changes to xml files
	 * @param simpleXmlElement $xmlFile  
	 * @return void
	 */
	public function saveXMLChanges($xmlFile)
	{
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $xmlFile->asXML());
		@fclose($fp);
	}
	
	/**
	 * contractDisplay
	 * this is used to manage category details
	 * @param String $curLang current language selected
	 * @return Array $return detail of category
	 */
	public function contractDisplay($curLang)
	{
		$strTemp = '/root/sending.php/'.$curLang.'/remunerationType';
		$xml =  @simplexml_load_file($this->xml_path."/remunerationType.xml");
		$result = $xml->xpath($strTemp);
		$return = array();
		$active =  $this->newloadArray("activeCession" , $curLang);
		
		foreach($result[0] as $Code)
		{
			$strTemp2 = '/root/sending.php/'.$curLang.'/remunerationTypeCat/'.$Code[0]->getName();
			$xml =  @simplexml_load_file($this->xml_path."/remunerationTypeCat.xml");
			$user = $xml->xpath($strTemp2);
				
			$Picto = '/root/sending.php/'.$curLang.'/remunerationTypeSpecial/'.$Code[0]->getName();
			$xml =  @simplexml_load_file($this->xml_path."/remunerationTypeSpecial.xml");
			$special = $xml->xpath($Picto);
			
			$Restrict = '/root/sending.php/'.$curLang.'/remuneration/'.$Code[0]->getName();
			$xml =  @simplexml_load_file($this->xml_path."/remuneration.xml");
			$variable = $xml->xpath($Restrict);
				
			$URL = '/root/sending.php/'.$curLang.'/fixed/'.$Code[0]->getName();
			$xml =  @simplexml_load_file($this->xml_path."/fixed.xml");
			$static = $xml->xpath($URL);
			
			$act = str_replace("element", "",$Code[0]->getName());
			
			if(in_array($act,$active))
			$active1 = "Yes";
			else
			$active1 = "No";
		
			$return[] = array("code"=>$act,"type"=>(string)$Code[0],"user"=>(string)$user[0],"special"=>(string)$special[0],"variable"=>(string)$variable[0],"static"=>(string)$static[0],"active"=>$active1);
		}
		return $return;
	}
	
	// displays live contracts
	public function contractDisplay2($curLang)
	{
		$strTemp = '/root/sending.php/'.$curLang.'/remunerationType';
		$xml =  @simplexml_load_file($this->live_path."/remunerationType.xml");
		$result = $xml->xpath($strTemp);
		$return = array();
		$active =  $this->newloadArray("activeCession" , $curLang);
		
		foreach($result[0] as $Code)
		{
			$strTemp2 = '/root/sending.php/'.$curLang.'/remunerationTypeCat/'.$Code[0]->getName();
			$xml =  @simplexml_load_file($this->live_path."/remunerationTypeCat.xml");
			$user = $xml->xpath($strTemp2);
				
			$Picto = '/root/sending.php/'.$curLang.'/remunerationTypeSpecial/'.$Code[0]->getName();
			$xml =  @simplexml_load_file($this->live_path."/remunerationTypeSpecial.xml");
			$special = $xml->xpath($Picto);
			
			$Restrict = '/root/sending.php/'.$curLang.'/remuneration/'.$Code[0]->getName();
			$xml =  @simplexml_load_file($this->live_path."/remuneration.xml");
			$variable = $xml->xpath($Restrict);
				
			$URL = '/root/sending.php/'.$curLang.'/fixed/'.$Code[0]->getName();
			$xml =  @simplexml_load_file($this->live_path."/fixed.xml");
			$static = $xml->xpath($URL);
			
			$act = str_replace("element", "",$Code[0]->getName());
			
			if(in_array($act,$active))
			$active1 = "Yes";
			else
			$active1 = "No";
		
			$return[] = array("code"=>$act,"type"=>(string)$Code[0],"user"=>(string)$user[0],"special"=>(string)$special[0],"variable"=>(string)$variable[0],"static"=>(string)$static[0],"active"=>$active1);
		}
		return $return;
	}
	
	public function contractupdate($filename, $arrayNodeName, $oldNodeName, $currentLang, $value="")
	{
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$this->_filePathName = $this->xml_path."/$arrayNodeName.xml";
		$xml =  @simplexml_load_file($this->_filePathName);
		$result = $xml->xpath($strTemp);
		$result[0]->$oldNodeName = utf8_encode(urlencode($value));
		$this->saveXMLChanges($xml);
	}
	
	public function contractdelete($filename, $arrayNodeName, $indexName, $currentLang)
	{
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$this->_filePathName = $this->xml_path."/$arrayNodeName.xml";
		$xml =  @simplexml_load_file($this->_filePathName);
		$result = $xml->xpath($strTemp);
		foreach ($result[0]->children() as $a)
		if($a[0]->getName() == $indexName)
		{
			unset($a[0]);
			break;
		}
	}
	
	public function addIndexNodes($filename, $arrayNodeName, $newNodeName, $allLang, $value="")
	{
		$this->_filePathName = $this->xml_path."/$arrayNodeName.xml";
		$xml =  @simplexml_load_file($this->_filePathName);
		
		foreach($allLang as $currentLang)
		{
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			if(is_numeric(substr($newNodeName,0,1)) == "true" & is_numeric($newNodeName) == "true")
			{
				$newNodeName = 'element'.$newNodeName;
			}
			$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName.'/'.$newNodeName;
			$result2 = $xml->xpath($strTemp);
			if(empty($result2))
			$result[0]->addChild($newNodeName, utf8_encode(urlencode($value)));
			$this->saveXMLChanges($xml);
		}
	}	
	
	public function newloadArray($arryName, $language)
	{
		$xml =  @simplexml_load_file($this->xml_path."/$arryName.xml");
		$result = $xml->xpath('/*/*/'.$language.'/'.$arryName);
		$typeofIndex = $result[0]['index'];
		$type = $result[0]['type'];
		if($type == 'Simple')
		{
			$final = utf8_decode(urldecode($result[0]));
		}
		else
		{
			if($typeofIndex == 'Yes')
			{
				foreach ($result as $kv)
		    	foreach ($kv as $k => $v)
		    	{
		    		$k1 = str_replace("element", "", $k);
		    		if($k1 == '')
		    		$key[] = $k;
		    		else 
		    		$key[] = $k1;
		    		$val[] = utf8_decode(urldecode((string)$v));
		    	}	
			}
			else 
			{
				foreach ($result as $kv)
		    	foreach ($kv as $k => $v)
		    	{
		    		$k = str_replace("element", "", $k);
		    		$key[] = $k;
		    		$val[] = utf8_decode(urldecode((string)$v));
		    	}
			}
			$final = array();
			if(count($key))	$final = array_combine($key, $val);
		}
		
		return $final;
	}
	
	public function deleteContractNode($filename, $arrayNodeName, $indexvalue, $allLang)
	{
		$this->_filePathName = $this->xml_path."/$arrayNodeName.xml";
		$xml =  @simplexml_load_file($this->_filePathName);
		foreach ($allLang as $currentLang)
		{
			$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			foreach ($result[0]->children() as $a)
			if($indexvalue == $a[0])
			{
				unset($a[0]);
				break;
			}
		}
		$this->saveXMLChanges($xml);
	}
	
	public function addContractNode($filename, $arrayNodeName, $value, $currentLang)
	{
		$this->_filePathName = $this->xml_path."/$arrayNodeName.xml";
		$xml =  @simplexml_load_file($this->_filePathName);
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName.'/*';
		$result = $xml->xpath($strTemp);
		$newNodeName = 0;
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName.'/element'.$newNodeName;
		$res = $xml->xpath($strTemp);
		while(!empty($res))
		{
			$newNodeName = $newNodeName + 1;
			$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName.'/element'.$newNodeName;
			$res = $xml->xpath($strTemp);
		}
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$result2 = $xml->xpath($strTemp);
		$result2[0]->addChild('element'.$newNodeName, utf8_encode(urlencode($value)));
		$this->saveXMLChanges($xml);
	}
	
	public function loadArrayv2($arryName, $language)
	{
		$xml =  @simplexml_load_file($this->xml_path."/$arryName.xml");
		$result = $xml->xpath('/*/*/'.$language.'/'.$arryName);
		$typeofIndex = $result[0]['index'];
		$type = $result[0]['type'];
		if($type == 'Simple')
		{
			$final = utf8_decode(urldecode($result[0]));
		}
		else
		{
			if($typeofIndex == 'Yes')
			{
				foreach ($result as $kv)
		    	foreach ($kv as $k => $v)
		    	{
		    		$k1 = str_replace("element", "", $k);
		    		if($k1 == '')
		    		$key[] = $k;
		    		else 
		    		$key[] = $k1;
		    		$val[] = utf8_decode(urldecode((string)$v));
		    	}	
			}
			else 
			{
				foreach ($result as $kv)
		    	foreach ($kv as $k => $v)
		    	{
		    		$k = str_replace("element", "", $k);
		    		$key[] = $k;
		    		$val[] = utf8_decode(urldecode((string)$v));
		    	}
			}
			$final = array();
			if(count($key))	$final = array_combine($key, $val);
		}
		
		return $final;
	}
	
}

?>
