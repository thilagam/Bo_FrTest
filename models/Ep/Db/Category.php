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


class Ep_Db_Category extends Ep_Db_XmlDb
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
	 * categoryDisplay
	 * this is used to manage category details
	 * @param String $curLang current language selected
	 * @return Array $return detail of category
	 */
	public function categoryDisplay($curLang)
	{
		$xml = $this->getXmlElement();
		$allSub = array("subject", "type", "language");
		$i = 0;
		$return = array();
		
		foreach($allSub as $type)
		{
			$strTemp = '/root/subject.php/'.$curLang.'/'.$type;
			$result = $xml->xpath($strTemp);
			
			foreach($result[0] as $Code)
			{
				$strTemp2 = '/root/subject.php/'.$curLang.'/displaySubjectLabel/'.$Code[0]->getName();
				$HPName = $xml->xpath($strTemp2);
				
				$Picto = '/root/subject.php/'.$curLang.'/subjectPicto/'.$Code[0]->getName();
				$Picto = $xml->xpath($Picto);
				
				$Restrict = '/root/subject.php/'.$curLang.'/urlGoogle/'.$Code[0]->getName();
				$Restrict = $xml->xpath($Restrict);
				
				$URL = '/root/subject.php/'.$curLang.'/urlSubject/'.$Code[0]->getName();
				$URL = $xml->xpath($URL);
				
				$Parent = '/root/subject.php/'.$curLang.'/codeparentid/'.$Code[0]->getName();
				$Parent = $xml->xpath($Parent);
				
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
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$result = $xml->xpath($strTemp);
		$result[0]->$oldNodeName = utf8_encode(urlencode($value));
		$this->saveXMLChanges($xml);
	}
	
	public function categorydelete($filename, $arrayNodeName, $indexName, $currentLang)
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
