<?php

class Ep_Document_FsmTrack extends Ep_Db_Identifier
{

  private $documentId;
  private $correctorId;
  private $date;
  private $title;
  private $descrip;
  private $page;
  private $category;
  
  protected $_name = "FSMTRACK";
	
  
  public function __construct($tablename=NULL)
  {
	parent::__construct();
		if($tablename)$this->_setupTableName($tablename);
   }
  	//check is the customer class is not ready yet to be inserted or updated
  public function loadData($array)
  {
    $this->setIdentifier($array["identifier"]);
    $this->setDocumentId($array["documentId"]);
    $this->setCorrectorId($array["correctorId"]);    
    $this->setDate($array["date"]);
	$this->setTitle($array["title"]);
	$this->setDescrip($array["descrip"]);
	$this->setPage($array["page"]);
	$this->setCategory($array["category"]);
    return $this;
  }
  //return an array from the instancied Class
  public function loadIntoArray()
  {
    $array = array();
    $array["identifier"]= $this->getIdentifier();
    $array["documentId"]= $this->getDocumentId();
    $array["correctorId"] = $this->getCorrectorId();
    $array["date"] = $this->getDate();
    $array["title"] = $this->getTitle();
    $array["descrip"] = $this->getDescrip();
    $array["page"] = $this->getPage(); 
    $array["category"] = $this->getCategory();
    return $array;
  }
    
  public function find()
  {
	$query = "SELECT * FROM ". $this->_name." WHERE correctorId = '". $this->getCorrectorId()."' AND documentId = ". $this->getDocumentId();
	return $this->exist($query);
  }
  
  public function getIdent()
  {
	$query = "SELECT * FROM ". $this->_name." WHERE correctorId = '". $this->getCorrectorId()."' AND documentId = ". $this->getDocumentId();
	return $this->getQuery($query, true);
  }
  
	  public function setDocumentId($documentId)
	  {
	    $this->documentId = $documentId;
	  }
	  public function setCorrectorId($correctorId)
	  {
	    $this->correctorId = $correctorId;
	  }
	  public function setDate($date)
	  {
	    $this->date = $date;
	  }
	  public function setTitle($title)
	  {
	    $this->title = $title;
	  }
	public function setDescrip($stage)
	  {
	    $this->descrip = $stage;
	  }
	public function setPage($stage)
	  {
	    $this->page = $stage;
	  }
	  public function setCategory($stage)
	  {
	    $this->category = $stage;
	  }
  
  
	  public function getDocumentId()
	  {
	    return $this->documentId;
	  }
	  public function getCorrectorId()
	  {
	    return $this->correctorId;
	  }
	  public function getDate()
	  {
	    return $this->date;
	  }
	  public function getTitle()
	  {
	    return $this->title;
	  }
	  public function getDescrip()
	  {
	    return $this->descrip;
	  }
	  public function getPage()
	  {
	    return $this->page;
	  }
	  public function getCategory()
	  {
	    return $this->category;
	  }
		
	public function getRecords2($date1, $date2, $listCorr)
	{
		$query = "SELECT SUM(title) as titles, SUM(descrip) as synopsis, SUM(category) as categorys, SUM(page) as pages FROM $this->_name WHERE DATE_FORMAT(FSMTRACK.date,\"%Y%m%d\") BETWEEN $date1 AND $date2 AND $listCorr ORDER BY date DESC";
		
		if ($result = $this->getQuery($query, true))
		{
			return $result;
		}
		return false;
	}
	  
	
	
}

?>