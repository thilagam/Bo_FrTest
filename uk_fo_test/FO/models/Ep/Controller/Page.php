<?php

/**
 * Ep_Controller_Page
 * 
 *
 * @date 9 Mai 08
 * @category EditPlace
 * @author Farid
 * @package Controller
 * @version version 1.1
 * Update:- getModuleList($lang) now needs $lang parameter to return modules specific to languages - Milan
 *
 */

class Ep_Controller_Page extends Ep_Db_Xml
{

	/**
	 * String Variable
	 * @var String $module list of module
	 * @var String $pattern
	 * @var String $segment
	 *  
	 */
	private $module;
	private $pattern;
	private $segment;
	private $metaDescription;
	private $metaKeywords;
	private $metaTitle;
	private $accessCode;
	private $section;
	private $processCode;
	
	
	/**
     * Constructor
     * this is used to set xml file path and root node
     * @param String $filePath file path
     * @param String $rootName root node name
     * @return void
     */
	function __construct($filePath = '',$rootName='page') 
	{
		if($filePath == '')$filePath = DATA_PATH.'page.xml';
		parent::__construct($filePath , $rootName);
	}
	
	/**
	 * setDatas
	 * this method will set $data array as attributes for the specified node
	 */
	public function setDatas()
	{
		$this->data["pattern"] = $this->getPatterns();
		$this->data["module"] = $this->getModule();
		$this->data["segment"] = $this->getSegment();
		$this->data["metaDescription"] = $this->getmetaDescription();
		$this->data["metaKeywords"] = $this->getmetaKeywords();
		$this->data["metaTitle"] = $this->getmetaTitle();
		$this->data["accessCode"] = $this->getaccessCode();
		$this->data["section"] = $this->getsection();
		$this->data["processCode"] = $this->getprocessCode();
		
		$this->_nodeName = $this->getNodeName();
		$this->_nodeValue = $this->getNodeValue();
	}
	
	/**
	 * getDatas
	 * this method will return $data array as attributes for the specified node
	 * @return Array $datas contains modified attributes
	 */
	public function getDatas()
	{
		$this->pattern = $this->data["pattern"];
		$this->module = $this->data["module"];
		$this->segment = $this->data["segment"];
		$this->metaDescription = $this->data["metaDescription"];
		$this->metaKeywords = $this->data["metaKeywords"];
		$this->metaTitle = $this->data["metaTitle"];
		$this->accessCode = $this->data["accessCode"];
		$this->section = $this->data["section"];
		$this->processCode = $this->data["processCode"];
	}
	
	/**
	 * getModule
	 * 
	 * @return Array $module contains module list
	 */
	public function getModule()
	{
		return trim($this->module);
	}
	
	/**
	 * getName
	 * 
	 * @return String $name
	 */
	public function getName()
	{
		return $this->_nodeName;
	}
	
	/**
	 * getSegment
	 * 
	 * @return String $segment
	 */
	public function getSegment()
	{
		return $this->segment;
	}
	
	/**
	 * getDescription
	 * 
	 * @return String $description
	 */
	public function getDescription()
	{
		return $this->_nodeValue;
	}
	
	/**
	 * getPattern
	 * 
	 * @return Pattern $pattern
	 */
	public function getPattern()
	{
		$pattern = new Ep_Controller_pattern();
		$pattern->getNodeMap($this->getPatternName());		
		return $pattern;
	}
	
	public function getPatterns()
	{
		return $this->pattern;
	}
	
	public function getPatternsasarray()
	{
		$pos = strpos($this->pattern, "|");
		if ($pos === false)
		{
			return $this->pattern;
		}
		else
		{
			$patrn = explode('|', $this->pattern);
			return $patrn;
		}
	}
	
	/**
	 * getPatternName
	 * 
	 * @return String $pattern
	 */
	public function getPatternName()
	{
		$this->_config = Zend_Registry::get('_config');
		$pos = strpos($this->pattern, "|");
	
		if ($pos === false)
		{
			return $this->pattern;
		}
		else
		{
			$pos = strpos($_SERVER["SERVER_NAME"], '.', 1);
			$domain = substr($_SERVER["SERVER_NAME"], 0,$pos);
			if($domain == "www")
			$domain = "fr";
			$patrn = explode('|', $this->pattern);
			$pattern = new Ep_Controller_pattern();
			$corectPat = $pattern->getProperPattern($patrn, $domain);
			if($corectPat == false)
			{
				return $patrn[0];//return NULL; //
			}
			else
			{
				return $corectPat;
			}
		}
	}

		/**
	 * getmetaDescription
	 * 
	 * @return String $metaDescription
	 */
	public function getmetaDescription()
	{
		return $this->metaDescription;
	}	
	
	/**
	 * getmetaKeywords
	 * 
	 * @return String $metaKeywords
	 */
	public function getmetaKeywords()
	{
		return $this->metaKeywords;
	}

	/**
	 * getmetaTitle
	 * 
	 * @return String $metaTitle
	 */
	public function getmetaTitle()
	{
		return $this->metaTitle;
	}	

	/**
	 * getaccessCode
	 * 
	 * @return String $metaTitle
	 */
	public function getaccessCode()
	{
		return $this->accessCode;
	}

	/**
	 * getprocessCode
	 * 
	 * @return String $processCode
	 */
	public function getprocessCode()
	{
		return $this->processCode;
	}
	
	/**
	 * getsection
	 * 
	 * @return String $section
	 */
	public function getsection()
	{
		return $this->section;
	}
	
	/**
	 * Set module attribute
	 * 
	 * @param String $title Title attribute
	 */
	public function setModule($module)
	{
		$this->module = $module;
	}
	
	/**
	 * Set Name attribute
	 *
	 * @param String $name
	 */
	public function setName($name)
	{
		$this->_nodeName = $name;
	}
	
	/**
	 * Set Description attribute
	 *
	 * @param String $description
	 */
	public function setDescription($description)
	{
		$this->_nodeValue = $description;
	}
	
	/**
	 * Set Pattern attribute
	 *
	 * @param String $pattern
	 */
	public function setPattern($pattern)
	{
		$this->pattern = $pattern;
	}
	
	/**
	 * Set Segment attribute
	 *
	 * @param String $segment
	 */
	public function setSegment($segment)
	{
		$this->segment = $segment;
	}
	
	/**
	 * Set metaDescription attribute
	 *
	 * @param String $segment
	 */
	public function setmetaDescription($metaDescription)
	{
		$this->metaDescription = $metaDescription;
	}	

	/**
	 * Set metaKeywords attribute
	 *
	 * @param String $metaKeywords
	 */
	public function setmetaKeywords($metaKeywords)
	{
		$this->metaKeywords = $metaKeywords;
	}		
	
	/**
	 * Set metaTitle attribute
	 *
	 * @param String $metaTitle
	 */
	public function setmetaTitle($metaTitle)
	{
		$this->metaTitle = $metaTitle;
	}	
		
	/**
	 * Set section attribute
	 *
	 * @param String $section
	 */
	public function setsection($section)
	{
		$this->section = $section;
	}	

	/**
	 * Set processCode attribute
	 *
	 * @param String $accessCode
	 */
	public function setprocessCode($processCode)
	{
		$this->processCode = $processCode;
	}
	
	/**
	 * Set accessCode attribute
	 *
	 * @param String $accessCode
	 */
	public function setaccessCode($accessCode)
	{
		$this->accessCode = $accessCode;
	}
	
	/**
	 * getModuleList
	 * 
	 * @return Array $array contains available module list
	 * 
	 */
	public function getModuleList($displayLang = "en")
	{
		$rank = $this->getRank();		
		$module = new Ep_Controller_Module();
		//get module list

		$moduleList = explode("|",$this->getModule());
		//echo "<p> modulelist array index = ".print_r($moduleList)."</p>";
				
		foreach ($moduleList as $mod)
		{
			$module->getNodeMap($mod);
			//echo 'Site '.$module->getSite();
			if($this->displayQualify($module->getLanguage(),$displayLang, $module->getSite()))
				if($module->getRank()==$rank || $module->getRank()==0)
				{
					$array[$module->getPosition()][$module->getOrder()]= $module->getFile();
				}
		}
		
		$pattern = new Ep_Controller_Pattern();
		$pattern->getNodeMap($this->getPatternName());
		$moduleList = explode("|",$pattern->getModule());
		//echo "<p> pattern modulelist array index = ".print_r($moduleList)."</p>";
		foreach ($moduleList as $mod)
		{
			$module->getNodeMap($mod);
			if($this->displayQualify($module->getLanguage(),$displayLang, $module->getSite()))
				if($module->getRank()==$rank || $module->getRank()==0)
				{
					$array[$module->getPosition()][$module->getOrder()]= $module->getFile();
				}
		}
		return $array;
	}
	
	/**
	 * getAllModule
	 * 
	 * @return Array of class $array contains all module
	 */
	public function getAllModule()
	{
		$module = new Ep_Controller_Module();
		$moduleList = explode("|",$this->getModule());
		
		foreach ($moduleList as $mod)
		{
			if($mod)
			{
				$module->getNodeMap($mod);
				$array[] = clone $module;
			}
		}
		$pattern = new Ep_Controller_Pattern();
		
		$pList = $this->getPatternsasarray();
		if(is_array($pList))
		{
			foreach ($pList as $pl)
			{
				$pattern->getNodeMap($pl);
				$moduleList = explode("|",$pattern->getModule());
				foreach ($moduleList as $mod)
				{
					if($mod)
					{
						$module->getNodeMap(trim($mod));
						$array[] = clone $module;
					}
				}
			}
		}
		else 
		{
			$pattern->getNodeMap($pList);
			$moduleList = explode("|",$pattern->getModule());
			foreach ($moduleList as $mod)
			{
				if($mod)
				{
					$module->getNodeMap(trim($mod));
					$array[] = clone $module;
				}
			}
		}
		
		return $array;
	}
	
	/**
	 * addModule
	 * 
	 * @param String moduleName
	 */
	public function addModule($moduleName)
	{
		$this->setModule($this->getModule()."|".$moduleName);
		$this->update();
	}
	
	/**
	 * deleteModule
	 * 
	 * @param String moduleName
	 * @return 1 if deletion complete, 0 elsewhere
	 * 
	 */
	public function deleteModule($moduleName)
	{
		$moduleList = explode("|",$this->getModule());
		foreach ($moduleList as $mod)
		{
			if((trim($mod) != trim($moduleName))&&$mod!="")
			{
				$content.= $separator.$mod;
				$separator = "|";
			}
			$i++;
		}
		$this->setModule($content);
		$this->update();
	}
	
	/**
	 * selectAllPagesBySegment
	 * 
	 * @param String moduleName
	 * @return Array page array
	 * 
	 */
	public function selectAllPagesBySegment($segment)
	{
		$pageList = $this->getAllNodesMap();
		foreach ($pageList as $page)
		{
			if($page->getSegment()==$segment)
			{
				$array[] = $page;
			}
		}
		return $array;
	}
	
	/**
	 * getRank
	 * 
	 * Get user rank
	 * 
	 * @param String moduleName
	 * @return Array page array
	 * 
	 */
	private function getRank()
	{
		$balance = new Ep_Controller_Balance($this->getMaxRank());
		return	$balance->getRank();
	}
	
	/**
	 * getRank
	 * 
	 * Get user rank
	 * 
	 * @param String moduleName
	 * @return Array page array
	 */
	private function getMaxRank()
	{
		$moduleList = $this->getAllModule();
		$maxRank = 0;
		foreach ($moduleList as $mod)
		{
			if($maxRank < $mod->getRank())$maxRank = $mod->getRank();
		}
		return $maxRank;
	}
	
	/**
	 * checkModuleExistence
	 * 
	 * @param String moduleName
	 * @return true if Exists, false elsewhere
	 */
	public function checkModuleExistence($moduleName)
	{
		$moduleList = explode("|", $this->getModule());
		
		foreach ($moduleList as $mod)
		{
			if ($mod == $moduleName)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * getPageNameByModule 
	 * this method will return the belonging page name for a given module name
	 * @param String $moduleName Module Name
	 * @return String $val page name if module exists else will return null
	 */
	public function getPageNameByModule($moduleName)
	{
		$pageList = $this->getAllNodesMap();
		foreach ($pageList as $page)
		{
			$mod = $page->getModule();
			$div = split('\|', $mod);
			foreach ($div as $aa)
			{
				if($aa == $moduleName)
				foreach ($page as $key => $val)
				if($key == '_nodeName')
				{
					return $val;
				}
			}
		}
		return null;
	}
	
	/**
	 * getAllPageNameByModule
	 * 
	 * @return Array $array array of page's if module exists else will return null
	 */
	public function getAllPageNameByModule($moduleName)
	{
		$pageList = $this->getAllNodesMap();
		foreach ($pageList as $page)
		{
			$mod = $page->getModule();
			$div = split('\|', $mod);
			foreach ($div as $aa)
			{
				if($aa == $moduleName)
				foreach ($page as $key => $val)
				if($key == '_nodeName')
				{
					$i = 1;
					$array[] = $val;
				}
			}
		}
		if($i == 1)
		return $array;
		else
		return null;
	}

	public function getModuleLang()
	{
		$balance = new Ep_Controller_Balance();
		return	$balance->getModuleLang();
	}

	/**
	 * displayQualify
	 * this method is used to check if the lang of particular module is set to the current settings and
	 * if it is okay for display
	 * @param String $s string specifing the language
	 * @return true if module lang matches with the lang settings
	 */
	private function displayQualify($s = NULL,$displayLang, $moddomain)
	{
		$dom = false;
		$pos = strpos($_SERVER["SERVER_NAME"], '.', 1);
		$domain = substr($_SERVER["SERVER_NAME"], 0,$pos);
		if($domain == "www")
		$domain = "fr";
		if($moddomain != '')
		{
			if(strstr($moddomain, $domain) != NULL)
			{
				$dom = true;
			}
		}
		else 
		{
			$dom = true;
		}
		
		$langList = explode("|",$s);
		
		foreach ($langList as $langElement)
		{
			if($langElement == $displayLang && $dom == true)
				return true;
		}
		
		
		return false;
	}
	
	public function getPageAccesscode($pageName)
	{
		$xml = $this->getXmlElement();
		
		$result = $xml->xpath($pageName);
		$accessCode = $result[0]['accessCode'];
		
		return $accessCode;
	}
	
	public function getSectionpages()
	{
		$xml = $this->getXmlElement();
		
		$result = $xml->xpath("//*[@section!='']");
		
		return $result;
	}
	
	public function getTranslationpages()
	{
		$xml = $this->getXmlElement();
		
		$result = $xml->xpath("//*[@section=1]");
		
		return $result;
	}
	
	public function getProcesspages()
	{
		$xml = $this->getXmlElement();
		
		$result = $xml->xpath("//*[@section=0]");
		
		return $result;
	}
	
	public function getsubsectionpages($no)
	{
		$xml = $this->getXmlElement();
		
		$result = $xml->xpath("//*[@section=".$no."]");
		
		return $result;
	}
	
	public function getMetadetails($pageName)
	{
		$xml = $this->getXmlElement();
		
		$result = $xml->xpath($pageName);
		$Metadetails = array($result[0]['metaTitle'],$result[0]['metaKeywords'],$result[0]['metaDescription']);
		return $Metadetails;
	}
	
	public function checkpageexistence($pageName)
	{
		$xml = $this->getXmlElement();
		
		$result = $xml->xpath('//*');
		
		$a = (array)$result[0];
		if(key_exists($pageName,$a))
		return true;
		else
		return false;
	}
	
}