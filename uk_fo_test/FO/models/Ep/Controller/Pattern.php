<?php

/**
 * Ep_Controller_Pattern
 * 
 *
 * @date 9 Mai 08
 * @category EditPlace
 * @author Farid
 * @package Controller
 * @version version 1.0
 *
 */

class Ep_Controller_Pattern extends Ep_Db_Xml
{

	/**
	 * String Variable
	 * @var String $skeleton
	 * @var Array $css
	 * @var Array $javascript
	 * @var Array $module
	 * 
	 */
	private $skeleton;
	private $css;
	private $javascript;
	private $module;
	private $site;

	/**
     * Constructor
     * this is used to set xml file path and root node
     * @param String $filePath file path
     * @param String $rootName root node name
     * @return void
     */
	function __construct($filePath = '',$rootName='pattern') 
	{
		if($filePath == '')$filePath = DATA_PATH.'pattern.xml';
		parent::__construct($filePath , $rootName);
	}
	
	/**
	 * setDatas
	 * this method will set $data array as attributes for the specified node
	 */
	public function setDatas()
	{
		$this->data["skeleton"] = $this->getSkeleton();
		$this->data["css"] = $this->getCss();
		$this->data["javascript"] = $this->getJavascript();
		$this->data["module"] = $this->getModule();
		$this->data["site"] = $this->getSite();
						
		$this->_nodeName = $this->getNodeName();
		$this->_value = $this->getNodeValue();
	}
	
	/**
	 * getDatas
	 * this method will return $data array as attributes for the specified node
	 * @return Array $datas contains modified attributes
	 */
	public function getDatas()
	{
		$this->skeleton = $this->data["skeleton"];
		$this->javascript = $this->data["javascript"];
		$this->css = $this->data["css"];
		$this->module = $this->data["module"];
		$this->site = $this->data["site"];
	}
	
	/**
	 * getDescription
	 * 
	 * @return String $description contains module list
	 */
	public function getDescription()
	{
		return $this->_nodeValue;
	}
	/**
	 * getSkeleton
	 * 
	 * @return String $skeleton contains modified title attribute
	 */
	public function getSkeleton()
	{
		return $this->skeleton;
	}
	/**
	 * getCss
	 * 
	 * @return Array $css css list
	 */
	public function getCss()
	{
		return $this->css;
	}
	/**
	 * getCssList
	 * 
	 * @return Array $cssList
	 */
	public function getCssList()
	{
		$cssList = explode("|",$this->css);
		
		foreach ($cssList as $css)
		{
			$array[] = $css;
		}
		return $array;
	}
	/**
	 * getJavascript
	 * 
	 * @return Array $javascript javascript list
	 */
	public function getJavascript()
	{
		return $this->javascript;
	}
	/**
	 * getJavascriptList
	 * 
	 * @return Array $javascriptList
	 */
	public function getJavascriptList()
	{
		$javascriptList = explode("|",$this->javascript);
		
		foreach ($javascriptList as $jl)
		{
			$array[] = $jl;
		}
		return $array;
	}
	/**
	 * getModule
	 * 
	 * @return Array $module
	 */
	public function getModule()
	{
		$mod = str_replace("\n","",$this->module);
		$mod = str_replace("\r","",$this->module);
		return trim($mod);
	}
	/**
	 * getModuleList
	 * 
	 * @return Array $moduleList
	 */
	public function getModuleList()
	{
		$moduleList = explode("|",$this->module);
		
		foreach ($moduleList as $jl)
		{
			$array[] = $jl;
		}
		return $array;
	}
	
	/**
	 * getSite
	 * 
	 * @return String site
	 */
	public function getSite()
	{
		return $this->site;
	}
	
	public function getSiteasarray()
	{
		$pos = strpos($this->site, "|");
		if ($pos === false)
		{
			return $this->site;
		}
		else
		{
			$patrn = explode('|', $this->site);
			return $patrn;
		}
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
	 * Set Name attribute
	 *
	 * @param String $name attribute
	 */
	public function setName($name)
	{
		$this->_nodeName = $name;
	}
	
	/**
	 * Set Skeleton attribute
	 *
	 * @param String $skeleton Price attribute
	 */
	public function setSkeleton($skeleton)
	{
		$this->skeleton = $skeleton;
	}

	/**
	 * Set Javascript attribute
	 *
	 * @param Array $javascript
	 */
	public function setJavascript($javascript)
	{
		$this->javascript = implode("|",$javascript);
	}

	/**
	 * Set Module attribute
	 *
	 * @param Array $module
	 */
	public function setModule($module)
	{
		$this->module = implode("|",$module);
	}

	/**
	 * Set site attribute
	 *
	 * @param String $site
	 */
	public function setSite($site)
	{
		$this->site = $site;
	}

	/**
	 * Set css attribute
	 *
	 * @param Array $css
	 */
	public function setCss($css)
	{
		$this->css = implode("|",$css);
	}
	
	
	/**
	 * getAllPattern
	 * 
	 * @return Array of class $array list of pattern
	 */
	public function getAllPattern()
	{
		return array_keys($this->simplexml_to_array($this->getAllNodes()));
	}
		
	public function checkpatternexistence($patternName)
	{
		$xml = $this->getXmlElement();
		
		$result = $xml->xpath('//*');
		
		$a = (array)$result[0];
		if(key_exists($patternName,$a))
		return true;
		else
		return false;
	}
	
	/*public function getProperPatternold($pattList, $domain)
	{
		$xml = $this->getXmlElement();
		foreach ($pattList as $pList)
		{
			$result = $xml->xpath("/pattern/".$pList."[@site='$domain']");
			if(!empty($result))
			return $pList;
			unset($result);
		}
		return false;
	}*/
	
	
	public function getProperPattern($pattList, $domain)
	{
		$xml = $this->getXmlElement();
		foreach ($pattList as $pList)
		{
			$result = $xml->xpath("/pattern/".$pList);
			if(!empty($result))
			{
				$sit = $result[0]['site'];
				$site = explode('|', $sit);
				if(in_array($domain,$site))
				return $pList;
			}
			unset($result);
		}
		return false;
	}
	
}