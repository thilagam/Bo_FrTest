<?php

/**
 * Ep_Db_Category
 * 
 * This class provides all necessary methods to maintain category details
 * 
 * @date 19 Feb 09
 * @category EditPlace
 * @author Shiva
 * @package Db
 * @version 1.0
 */


class Ep_Db_Category3
{
	protected $_language = array();
	protected $_filePathName;

	protected $xml_path = "/home/sites/site6/users/xmldb/dacodoc";
	protected $live_path = "/home/sites/site6/users/xmldb/dacodoc";
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
	 * categoryDisplay
	 * this is used to manage category details
	 * @param String $curLang current language selected
	 * @return Array $return detail of category
	 */
	public function categoryDisplay($curLang)
	{
		$allSub = array("subject", "type", "language");
		$i = 0;
		$return = array();
		
		foreach($allSub as $type)
		{			
			$strTemp = '/root/subject.php/'.$curLang.'/'.$type;
			$xml =  @simplexml_load_file($this->xml_path."/$type.xml");
			$result = $xml->xpath($strTemp);
			
			foreach($result[0] as $Code)
			{
				$xml = @simplexml_load_file($this->xml_path."/displaySubjectLabel.xml");
				$strTemp2 = '/root/subject.php/'.$curLang.'/displaySubjectLabel/'.$Code[0]->getName();
				$HPName = $xml->xpath($strTemp2);
				
				$xml = @simplexml_load_file($this->xml_path."/subjectPicto.xml");
				$Picto = '/root/subject.php/'.$curLang.'/subjectPicto/'.$Code[0]->getName();
				$Picto = $xml->xpath($Picto);
				
				$xml = @simplexml_load_file($this->xml_path."/urlGoogle.xml");
				$Restrict = '/root/subject.php/'.$curLang.'/urlGoogle/'.$Code[0]->getName();
				$Restrict = $xml->xpath($Restrict);
				
				$xml = @simplexml_load_file($this->xml_path."/urlSubject.xml");
				$URL = '/root/subject.php/'.$curLang.'/urlSubject/'.$Code[0]->getName();
				$URL = $xml->xpath($URL);
				
				$xml = @simplexml_load_file($this->xml_path."/codeparentid.xml");
				$Parent = '/root/subject.php/'.$curLang.'/codeparentid/'.$Code[0]->getName();
				$Parent = $xml->xpath($Parent);
				
				$xml = @simplexml_load_file($this->xml_path."/categorytype.xml");
				$typeof = '/root/process.php/'.$curLang.'/categorytype/element'.(string)$i;
				$typeof = $xml->xpath($typeof);
				
				$return[] = array("code"=>$Code[0]->getName(),"name"=>(string)$Code[0],"label"=>(string)$HPName[0],"picto"=>(string)$Picto[0],"urlGoogle"=>(string)$Restrict[0],"urlHtml"=>(string)$URL[0],"parentId"=>(string)$Parent[0],"type"=> (string)$typeof[0]);
			}
			$i++;
		}
		return $return;
	}
	
	// to display live related xml files categories
	public function categoryDisplay2($curLang)
	{
		$allSub = array("subject", "type", "language");
		$i = 0;
		$return = array();
		
		foreach($allSub as $type)
		{			
			$strTemp = '/root/subject.php/'.$curLang.'/'.$type;
			$xml =  @simplexml_load_file($this->live_path."/$type.xml");
			$result = $xml->xpath($strTemp);
			
			foreach($result[0] as $Code)
			{
				$xml = @simplexml_load_file($this->live_path."/displaySubjectLabel.xml");
				$strTemp2 = '/root/subject.php/'.$curLang.'/displaySubjectLabel/'.$Code[0]->getName();
				$HPName = $xml->xpath($strTemp2);
				
				$xml = @simplexml_load_file($this->live_path."/subjectPicto.xml");
				$Picto = '/root/subject.php/'.$curLang.'/subjectPicto/'.$Code[0]->getName();
				$Picto = $xml->xpath($Picto);
				
				$xml = @simplexml_load_file($this->live_path."/urlGoogle.xml");
				$Restrict = '/root/subject.php/'.$curLang.'/urlGoogle/'.$Code[0]->getName();
				$Restrict = $xml->xpath($Restrict);
				
				$xml = @simplexml_load_file($this->live_path."/urlSubject.xml");
				$URL = '/root/subject.php/'.$curLang.'/urlSubject/'.$Code[0]->getName();
				$URL = $xml->xpath($URL);
				
				$xml = @simplexml_load_file($this->live_path."/codeparentid.xml");
				$Parent = '/root/subject.php/'.$curLang.'/codeparentid/'.$Code[0]->getName();
				$Parent = $xml->xpath($Parent);
				
				$xml = @simplexml_load_file($this->live_path."/categorytype.xml");
				$typeof = '/root/process.php/'.$curLang.'/categorytype/element'.(string)$i;
				$typeof = $xml->xpath($typeof);
				
				$return[] = array("code"=>$Code[0]->getName(),"name"=>(string)$Code[0],"label"=>(string)$HPName[0],"picto"=>(string)$Picto[0],"urlGoogle"=>(string)$Restrict[0],"urlHtml"=>(string)$URL[0],"parentId"=>(string)$Parent[0],"type"=> (string)$typeof[0]);
			}
			$i++;
		}
		return $return;
	}
	
	public function categoryupdate($filename, $arrayNodeName, $oldNodeName, $currentLang, $value="")
	{
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$xml =  @simplexml_load_file($this->xml_path."/$arrayNodeName.xml");
		$this->_filePathName = $this->xml_path."/$arrayNodeName.xml";
		$result = $xml->xpath($strTemp);
		$result[0]->$oldNodeName = utf8_encode(urlencode($value));
		$this->saveXMLChanges($xml);
	}
	
	public function categoryupdate2($filename, $arrayNodeName, $oldNodeName, $currentLang, $value="")
	{
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$this->_filePathName = $this->xml_path."/$arrayNodeName.xml";
		$xml =  @simplexml_load_file($this->_filePathName);
		
		$result = $xml->xpath($strTemp);
		foreach ($result[0] as $subnode)
		{
			if($subnode->getName() == $value)
			$result[0]->$oldNodeName = 2;	
		}
		$this->saveXMLChanges($xml);
	}
	
	public function categorydelete($filename, $arrayNodeName, $indexName, $currentLang)
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
	
}

?>
