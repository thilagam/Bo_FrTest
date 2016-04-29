<?php

/**
 * Ep_Controller_Module
 * 
 *
 * @date 9 Mai 08
 * @category EditPlace
 * @author Farid
 * @package Controller
 * @version version 1.0
 *
 */

class Ep_Controller_Module extends Ep_Db_Xml
{

	/**
	 * String Variable
	 * @var Integer $rank module access rank
	 * @var String $name module name
	 * @var String $file module filename
	 * @var String $language module language
	 * @var Integer $online online attribution
	 * @var Integer $position module description
	 * @var Integer $order module description
	 * 
	 */
	private $rank;
	private $language;
	private $file;
	private $online;
	private $position;
	private $access;
	private $order;
	private $modDate;
	private $daysBetDate;
	private $active;
	private $site;
	
	/**
     * Constructor
     * this is used to set xml file path and root node
     * @param String $filePath file path
     * @param String $rootName root node name
     * @return vorank
     */
	function __construct($filePath = '',$rootName = 'module') 
	{
		if($filePath == '')$filePath = DATA_PATH.'module.xml';
		parent::__construct($filePath , $rootName);
	}

	/**
	 * setDatas
	 * 
	 * this method will set $data array as attributes for the specified node
	 */
	public function setDatas()
	{
		$this->data["rank"] = $this->getRank();
		$this->data["file"] = $this->getFile();
		$this->data["online"] = $this->getOnline();
		$this->data["language"] = $this->getLanguage();
		$this->data["position"] = $this->getPosition();
		$this->data["access"] = $this->getAccess();
		$this->data["order"] = $this->getOrder();
		$this->data["modDate"] = $this->getmodDate();
		$this->data["daysBetDate"] = $this->getdaysBetDate();
		$this->data["active"] = $this->getactive();
		$this->data["site"] = $this->getSite();
		
		$this->_nodeName = $this->getNodeName();
		$this->_value = $this->getNodeValue();
	}
	
	/**
	 * getDatas
	 * 
	 * this method will return $data array as attributes for the specified node
	 * 
	 */
	public function getDatas()
	{
		$this->rank = $this->data["rank"];
		$this->file = $this->data["file"];
		$this->language = $this->data["language"];
		$this->online = $this->data["online"];
		$this->position = $this->data["position"];
		$this->access = $this->data["access"];
		$this->order = $this->data["order"];
		$this->modDate = $this->data["modDate"];
		$this->daysBetDate = $this->data["daysBetDate"];
		$this->active = $this->data["active"];
		$this->site = $this->data["site"];
	}
	
	/**
	 * getRank
	 * 
	 * @return Rankentifier $data contains modified price attribute
	 */
	public function getRank()
	{
		return $this->rank;
	}
	
	/**
	 * getFile
	 * 
	 * @return String $name module file name
	 */
	public function getFile()
	{
		return $this->file;
	}
	/**
	 * getLanguage
	 * 
	 * @return String $language module language
	 */
	public function getLanguage()
	{
		return $this->language;
	}
	/**
	 * getOnline
	 * 
	 * @return String $language module online value
	 */
	public function getOnline()
	{
		return $this->online;
	}
	/**
	 * getPosition
	 * 
	 * @return Integer $language module online value
	 */
	public function getPosition()
	{
		return $this->position;
	}
	/**
	 * getAccess
	 * 
	 * @return Integer $access 
	 */
	public function getAccess()
	{
		return $this->access;
	}
	/**
	 * getOrder
	 * 
	 * @return Integer $order
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * getmodDate
	 * 
	 * @return Date $modDate
	 */
	public function getmodDate()
	{
		return $this->modDate;
	}
	
	/**
	 * daysBetDate
	 * 
	 * @return Date $daysBetDate
	 */
	public function getdaysBetDate()
	{
		return $this->daysBetDate;
	}
	
	/**
	 * getactive
	 * 
	 * @return Date $active
	 */
	public function getactive()
	{
		return $this->active;
	}

	/**
	 * getSite
	 * 
	 * @return String $site
	 */
	public function getSite()
	{
		return $this->site;
	}
	
	/**
	 * getSite
	 * 
	 * @return String $site
	 */
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
	 * setRank
	 * 
	 * @param Integer Rank
	 */
	public function setRank($rank)
	{
		$this->rank = trim($rank);
	}
	
	/**
	 * setFile
	 * 
	 * @param String file
	 */
	public function setFile($file)
	{
		$this->file = trim($file);
	}

	/**
	 * setLanguage
	 * 
	 * @param String language
	 */
	public function setLanguage($language)
	{
		$this->language = trim($language);
	}

	/**
	 * setOnline
	 * 
	 * @param Integer language
	 */
	public function setOnline($online)
	{
		$this->online = trim($online);
	}

	/**
	 * setAccess
	 * 
	 * @param Integer access
	 */
	public function setAccess($access)
	{
		$this->access = trim($access);
	}

	/**
	 * setPosition
	 * 
	 * @param Integer $position
	 */
	public function setPosition($position)
	{
		$this->position = trim($position);
	}

	/**
	 * setOrder
	 * 
	 * @param Integer $order
	 */
	public function setOrder($order)
	{
		$this->order = trim($order);
	}

	 /** setmodDate
	 * 
	 * @param Date $modDate
	 */
	public function setmodDate($modDate)
	{
		$this->modDate = $modDate;
	}
	
	/**
	 * daysBetDate
	 * 
	 * @param Date $modDate
	 */
	public function setdaysBetDate($daysBetDate)
	{
		$this->daysBetDate = $daysBetDate;
	}
	
	/**
	 * setSite
	 * 
	 * @param String $site
	 */
	public function setSite($site)
	{
		$this->site = $site;
	}

	/**
	 * setactive
	 * 
	 * @param Date $active
	 */
	public function setactive($active)
	{
		$this->active = $active;
	}
	
	/**
	 * count_days
	 * 
	 * @param Date
	 */
	public function count_days( $a, $b )
	{
		$gd_a = getdate( $a );
		$gd_b = getdate( $b );
		
		$a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
		$b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
		
		return round( abs( $a_new - $b_new ) / 86400 );
	}

}
?>
