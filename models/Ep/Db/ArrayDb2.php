<?php

/**
 * ArrayDb
 * This class provides all necessary methods to maintain php files of all country directories
 * @date 10 july 08
 * @category EditPlace
 * @author Shiva
 * @package ArrayDb
 * @version 1.0
 */

class Ep_Db_ArrayDb2 extends Ep_Db_NewXmlDb
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
	function __construct($xmlfilePath = '', $root = '')
	{
		parent::__construct($xmlfilePath);
	}
}
