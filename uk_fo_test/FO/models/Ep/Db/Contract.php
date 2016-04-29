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


class Ep_Db_Contract extends Ep_Db_XmlDb
{
	protected $_language = array();
	protected $_filePathName;

	/**
	 * Constructor
	 * this is used to set xml file path and root node
	 * @param String $filePath file path
	 * @param String $rootName root node name
	 * @return void
	 */
	function __construct($xmlfilePath, $rootName = '')
	{
		parent::__construct($xmlfilePath, $rootName);
	}

	/**
	 * contractDisplay
	 * this is used to manage category details
	 * @param String $curLang current language selected
	 * @return Array $return detail of category
	 */
	public function contractDisplay($curLang)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/root/sending.php/'.$curLang.'/remunerationType';
		$result = $xml->xpath($strTemp);
		$return = array();
		$active =  $this->loadArrayv2("activeCession" , $curLang);
		
		foreach($result[0] as $Code)
		{
			$strTemp2 = '/root/sending.php/'.$curLang.'/remunerationTypeCat/'.$Code[0]->getName();
			$user = $xml->xpath($strTemp2);
				
			$Picto = '/root/sending.php/'.$curLang.'/remunerationTypeSpecial/'.$Code[0]->getName();
			$special = $xml->xpath($Picto);
			
			$Restrict = '/root/sending.php/'.$curLang.'/remuneration/'.$Code[0]->getName();
			$variable = $xml->xpath($Restrict);
				
			$URL = '/root/sending.php/'.$curLang.'/fixed/'.$Code[0]->getName();
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
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$result = $xml->xpath($strTemp);
		$result[0]->$oldNodeName = utf8_encode(urlencode($value));
		$this->saveXMLChanges($xml);
	}
	
	public function contractdelete($filename, $arrayNodeName, $indexName, $currentLang)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$result = $xml->xpath($strTemp);
		foreach ($result[0]->children() as $a)
		if($a[0]->getName() == $indexName)
		{
			unset($a[0]);
			break;
		}
	}
		
	/**
	 * setDatas
	 * 
	 * this method will set $data array as attributes for the specified node
	 */
	public function setDatas()
	{
	}

	/**
	 * getDatas
	 * 
	 * this method will return $data array as attributes for the specified node
	 * 
	 */
	public function getDatas()
	{
	}
}

?>
