<?php

/**
 * Module
 * 
 *
 * @date 9 Mai 08
 * @category EditPlace
 * @author Farid
 * @package Controller
 * @version version 1.0
 *
 */

class Ep_Controller_Scriptversion extends Ep_Db_Xml
{
	/*
	private $file;
	private $folder;
	private $reason;
	*/
	
	private $modDate;
	private $online;
	
	
	/**
	 * Constructor
	 * this is used to set xml file path and root node
	 * @param String $filePath file path
	 * @param String $rootName root node name
	 */
	function __construct($filePath = '', $rootName = 'script')
	{
		if ($filePath == '')
			$filePath = DATA_PATH . 'scriptversion.xml';
		parent::__construct($filePath, $rootName);
	}
	
	public function setDatas()
	{
		
	}
	
	public function getDatas()
	{
		
	}

	public function setChildDatas()
	{
		$this->data["modDate"] = $this->getmodDate();
		$this->data["online"] = $this->getOnline();
		
		$this->_parentNode = $this->getparentNode();
		$this->_nodeName = $this->getNodeName();
		$this->_value = $this->getNodeValue();
	}
	
	public function getChildDatas()
	{
		$this->modDate = $this->data["modDate"];
		$this->online = $this->data["online"];
	}
	
	/**
	 * getmodDate
	 * 
	 * @return modDate $data contains modified date attribute
	 */
	public function getmodDate()
	{
		return $this->modDate;
	}
	
	/**
	 * setmodDate
	 * 
	 * @param Date modDate
	 */
	public function setmodDate($modDate)
	{
		$this->modDate = $modDate;
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
	
	 /** setOnline
	 * 
	 * @param Integer language
	 */
	public function setOnline($online)
	{
		$this->online = $online;
	}

}
?>
