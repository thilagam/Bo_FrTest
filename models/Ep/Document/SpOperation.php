<?php

class Ep_Document_SpOperation extends Ep_Db_Identifier
{
	//Private properties
	private  $begin;     	//current beginDate
	private  $deadline;		//current end Date
	private  $duration;		//current duration of operation
	private  $remunerationV;	//current remuneration of the sponsor
	private  $remunerationP;	//current remuneration of the sponsored

	/**
	 * The default table name 
	 */
	protected $_name = 'SPOPERATION';
	
	public function __construct($tablename=NULL, $identifier="")
	{
		parent::__construct();
		if($tablename)$this->_setupTableName($tablename);
		if(strlen($identifier)==15)
		{
			$this->loadById($identifier);
		}
		else
		{
			$query = "SELECT * FROM ". $this->_name." ORDER BY begin DESC LIMIT 0,1 ";
			if(($result = $this->getQuery($query,true)) != NULL)
			foreach ($result as $resul)
			{
				$this->loadData($resul);
			}
		}
	}

	//validity of the class
	public function isNotValid()
	{			
		return "";
	}

	  //return a class from the array parameter
	public function loadData($array)
	{
		$this->setIdentifier($array["identifier"]);
		$this->setBegin($array["begin"]);
		$this->setDeadLine($array["deadline"]);
		$this->setDuration($array["duration"]);	
		$this->setRemunerationV($array["remunerationV"]);
		$this->setRemunerationP($array["remunerationP"]);	 
	    return $this;
	}
	
	//return an array from the instancied Class
	protected function loadIntoArray()
	{
		$array = array();
		$array["identifier"] = $this->getIdentifier();
		$array["begin"] = $this->getBegin();
		$array["deadline"] = $this->getDeadline();
		$array["duration"] = $this->getDuration();
		$array["remunerationV"] = $this->getRemunerationV();
		$array["remunerationP"] = $this->getRemunerationP();
		return $array;
	}
	
	public function loadById($id)
	{
		$query = "SELECT * FROM ".$this->_name." where id = '".$id."'";
		if(($result = $this->getQuery($query,false)) != NULL)
		foreach ($result as $resul)
		{
			$this->loadData($resul);
		}
	}
	
	public function enable()
	{
		$d = new Date();
		$d->getDateFormat();
		if($this->getBegin() <= $d->getDateFormat() && $d->getDateFormat() <= $this->getDeadline())
		return true;
		return false;
	}
	
	public function selectOperation()
	{
		$query = "SELECT * FROM ". $this->_getTableName();
		return $this->loadArrayFromQuery($query);
	}
	
	public function setBegin($begin)
	{
		$this->begin = $begin;
	}
	public function setDeadline($deadline)
	{
		$this->deadline = $deadline;
	}
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	public function setRemunerationV($remunerationV)
	{
		$this->remunerationV = $remunerationV;
	}
	public function setRemunerationP($remunerationP)
	{
		$this->remunerationP = $remunerationP;
	}
	
	//get methods
	public function getBegin()
	{
		return $this->begin;
	}
	public function getDeadline()
	{
		return $this->deadline;
	}
	public function getDuration()
	{
		return $this->duration;
	}
	public function getRemunerationV()
	{
		return $this->remunerationV;
	}
	public function getRemunerationP()
	{
		return $this->remunerationP;
	}
	//end get methods
}

?>