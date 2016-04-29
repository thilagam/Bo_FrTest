<?php

/**
 * Ep_Db_Identifier
 * 
 * This is a child class of dbTable (abstract class). It will provide support for
 * identifier variable which is found in most of the db tables.
 * 
 * @author Milan
 * @package Db	
 * @version 3.0
 * new features - createIndentifier, constructor
 * Update: - insert() updated with if..else and now returns boolean values
 */

require_once 'Zend/Db/Table/Abstract.php';


abstract class Ep_Db_Identifier extends Ep_Db_dbTable  
{
	// identifier is the name of column found in most tables of database
	private $identifier;
	
	public function __construct($tableName=NULL)
	{
		$this->_setupTableName($this->_name);
		$this->createIdentifier();
        $this->adminLogin  = Zend_Registry::get('adminLogin');
        //// to visible the ao or not based on the superadmin and oter users
        if($this->adminLogin->groupId != 1)
            $this->visibility = " d.ao_visibility IN ('show', 'hide')";
        else
            $this->visibility = " d.ao_visibility IN ('show','hide')";
	}


	/**
	 * selectById
	 * This method is used to select row by id
	 */
	public function selectById($id)
	{
		$query = "SELECT * FROM ".$this->_name." WHERE identifier = '$id'";
		//echo $query;
		return $this->loadByQuery($query);
	}

	/**
	 * altSelectById alternate to selectById
	 * This method is used to select row by id
	 */
	public function altSelectById($id)
	{
		$query = "SELECT * FROM ".$this->_name." WHERE identifier = '$id'";
		//echo $query;
		return $this->loadByQuery($query);
	}
	
	
	
	/**
	 * Insert
	 * This method will insert a row
	 */
	public function insert()
	{
		$data = $this->loadIntoArray();
		
		/*echo "from identifier.php:";
		print_r($data);
		echo "end";
		            exit;*/
		//print_r($data);
		if(parent::insertQuery($data))
		{			//return parent::insertQuery($data);	
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Update
	 * This method will update a row
	 */
	public function update()
	{
		$data = $this->loadIntoArray();
		$query = "identifier = '".$this->getIdentifier()."'";
		//print_r($data);
		//echo $query;
		return parent::updateQuery($data,$query);
	}
		
	public function altUpdate()
	{
		$data = $this->loadIntoArray();
		$query = "identifier = '".$this->getIdentifier()."'";
		//print_r($data);
		//echo $query;
		return parent::updateQuery($data,$query);
	}
	
	
	/**
	 * loadData
	 * This method is used to load data from an array
	 */
	protected function loadData($array){}

	/**
	 * loadData
	 * This method is used to load data from an array
	 */
	protected function loadIntoArray(){}
	
	/**
	 * setIdentifier
	 * This method is used to set indentifier variable
	 */
	public function setIdentifier($identifier)
	{
		$this->identifier = $identifier;
	}
	
	/**
	 * getIdentifier
	 * This method will set identifier variable by calling  predefined method
	 * @return Integer identifier 
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}
	
	/**
	 * create identifier
	 * This method will create (generate) identifier number
	 * @return Integer identifier
	 */
	protected function createIdentifier()
	{
		//$d = new Date();
		//$this->identifier = $d->getSubDate(2,14).rand(100,999);
		$this->identifier=number_format(microtime(true),0,'','').mt_rand(10000,99999);
	}
	
	//Fetching Configuration
	public function getConfiguration($columns)
    {
        $SelConfg="SELECT configure_value FROM Configurations WHERE configure_name='".$columns."' ";
		if(($resultconfg = $this->getQuery($SelConfg,true)) != NULL)
            return $resultconfg[0]['configure_value'];
    }
}
