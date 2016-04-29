<?php

class Ep_Document_DocumentPartner extends Ep_Document_Document
{
	/**
	 * The default table name 
	 */
	protected $_name = 'DOCUMENTPARTNER';
	
	//Private properties
	private $id;    	//current DocumentPartner id
	private $flowId;	//current flow identifier
	
	public function __construct($tablename=NULL)
	{
		parent::__construct();
		if($tablename)$this->_setupTableName($tablename);
	}
	
	protected function loadData($array)
	{
		$this->setIdentifier($array["identifier"]);
		$this->setId($array["id"]);
		$this->setFlowId($array["flowId"]);
	}
	
	/**
	 * loadIntoArray
	 * Loads the array $array with respective records fetched from the database
	 *
	 * @return array $array
	 */
	public function loadIntoArray()
	{
		$array = array();
		$array["identifier"] = $this->getIdentifier();
		$array["id"] = $this->getId();
		$array["flowId"] = $this->getFlowId();
		return $array;
	}
	
	public function loadById($id)
	{
		$query = "SELECT * FROM ".$this->_name." where id = '".$id."'";
		if(($result = $this->getQuery($query,false)) != NULL)
			return true;
		else
			return false;
	}
	
	public function selectByCustomerId($customerId,$flowId)
	{
		$query = "SELECT D.identifier,D.title,D.online,DP.* FROM ".$this->_name." DP, DOCUMENT D WHERE DP.identifier = D.identifier AND D.customerId = '$customerId' AND DP.flowId = '$flowId'";
		return $this->getQuery($query, true);
	}
	
	public function getId()
	{
		return $this->id;
	}
	public function getFlowId()
	{
		return $this->flowId;
	}
	public function getIdentifier()
	{
		return $this->identifier;
	}

	public function setId($id)
	{
		$this->id = $id;
	}
	public function selectById($id)
	{
		$query = "SELECT * FROM ".$this->_name." WHERE identifier = '$id'";
		return $this->loadByQuery($query);
	}
	public function setFlowId($flowId)
	{
		$this->flowId = $flowId;
	}
	public function setIdentifier($identifier)
	{
		$this->identifier = $identifier;
	}
	
	public function dpexists($getCustomerId, $getId)
	{
		$query = "SELECT * FROM DOCUMENTPARTNER,DOCUMENT WHERE DOCUMENT.identifier = DOCUMENTPARTNER.identifier AND DOCUMENT.customerId = '".$getCustomerId."' AND DOCUMENTPARTNER.id = '".$getId."' ORDER BY date DESC";
		return $this->exist($query);
	}
	
	public function getIdenti()
	{
		$query = "SELECT DOCUMENTPARTNER.identifier FROM DOCUMENTPARTNER,DOCUMENT WHERE DOCUMENT.identifier = DOCUMENTPARTNER.identifier AND DOCUMENT.customerId = '".$this->getCustomerId()."' AND DOCUMENTPARTNER.id = '".$this->getId()."' ORDER BY date DESC";
		$result = $this->getQuery($query,true);
		foreach ($result as $resul)
		{
			return $resul;
		}
	}
	
	/**
	 * insert
	 * this method will insert data into the pre-selected table
	 * 
	 * @abstract insertRecord
	 * 
	 * @return boolean returns true if data successfully inserted
	 */
	public function insert()
	{
		$data=$this->loadIntoArray();
		//print_r($data);
		$data2=parent::loadIntoArray();
		//print_r($data2);
		$parent=new Ep_Document_Document();
		$return = $parent->insertplus(parent::loadIntoArray());
		$return.= $this->insertQuery($data);
		return $return;
	}
	
	public function insertDP()
	{
		$data=$this->loadIntoArray();
		$return = $this->insertQuery($data);
		return $return;
	}
	
	public function fewUpdateDP($docid,$on)
  	{
  		$data= Array('id'=>$on);
		$where = "identifier = $docid";
		$this->updateQuery($data,$where);
			return true;
  	}
	
	
	public function loaddataprice()
	{
		return $this->getPrice();
	}
	
	public function loadidentprice()
	{
		return $this->getIdentifier();
	}
	
}
	
	
	