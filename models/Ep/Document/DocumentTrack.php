<?php

class Ep_Document_DocumentTrack extends Ep_Db_Identifier
{

  private $documentId;
  private $correctorId;
  private $date;
  private $stage;
  private $upload;
  
  protected $_name = "DOCUMENTTRACK";
  
  private $indian = "1 AND 80";
  private $argentina = "81 AND 99";  
  
  public function __construct($tablename=NULL){

	parent::__construct();

		if($tablename)$this->_setupTableName($tablename);	

	}
  	//check is the customer class is not ready yet to be inserted or updated
  public function loadData($array)
  {
  	//print_r($array);
  	//echo 'load';
    $this->setIdentifier($array["identifier"]);
    $this->setDocumentId($array["documentId"]);
    $this->setCorrectorId($array["correctorId"]);
    $this->setStage($array["stage"]);
    $this->setDate($array["date"]);
  	if($_COOKIE['_country'] == 'fr')
	{
		if ($array["upload"] != "")
		$this->setUpload($array["upload"]);
	}
    return $this;
  }
  //return an array from the instancied Class
  public function loadIntoArray()
  {
    $array = array();
    $array["identifier"]= $this->getIdentifier();
    $array["documentId"]= $this->getDocumentId();
    $array["correctorId"] = $this->getCorrectorId();
    $array["stage"] = $this->getStage();
    $array["date"] = $this->getDate();
  	if($_COOKIE['_country'] == 'fr')
    if($this->getUpload() != "")
	{
		$array["upload"] = $this->getUpload();
	}
    //print_r($array);
    return $array;
  }
  
  public function getdocInfo($documentId)
  {
	$query = "SELECT * FROM ". $this->_name." WHERE documentId = '". $documentId."'";
	return $this->getQuery($query,true);
  }
  
  public function find()
  {
	$query = "SELECT * FROM ". $this->_name." WHERE correctorId = '". $this->getCorrectorId()."' AND documentId = '". $this->getDocumentId()."' AND stage = ".$this->getStage();
	return $this->exist($query);
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
  public function setStage($stage)
  {
    $this->stage = $stage;
  }
	public function setUpload($upload)  // it will set random number
	{
		$this->upload = $upload;
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
  public function getStage()
  {
    return $this->stage;
  }
	public function getUpload()  // it will set random number
	{
		return $this->upload;
	}

  public function loadByDocId($ids, $stage)
  {
  	$this->group = $_COOKIE['groupid'];
	if($_COOKIE['_country'] == 'fr')
	$this->randSelection = "dt.upload = 0 AND ";
	else 
	$this->randSelection = "dt.upload = 1 AND ";
  	$data = array();
	$id_cond = "'".implode("', '", $ids)."'";
  	$query="select documentId from ".$this->_name." as dt where dt.documentId IN ($id_cond) AND $this->randSelection dt.stage= $stage order by dt.date DESC";
  	foreach($this->getQuery($query,true) as $row)
  	{
		$data[$row['documentId']] = 1;
	}
	return $data; 
  }
	
  public function uploadUpdate($docid,$stage,$corrId)
  {
  		$where = " documentId = $docid AND stage = $stage ";
  		$Date = new Date("",$_COOKIE["_country"]);
  		$a = array("upload" => 1,"date" => $Date->getDate(),"correctorId" => $corrId);
		if (($result = $this->updateQuery($a, $where)) != NULL)
			return true;
		else
			return false;
  }
  
  public function onupload()
  {
  		$where = " upload = 0 ";
  		$a = array("upload" => 1);
		if (($result = $this->updateQuery($a, $where)) != NULL)
			return true;
		else
			return false;
  }
  
  public function getUploadDocs($stage,$state,$corr = NULL)
  {
  		$this->group = $_COOKIE['groupid'];
		if($_COOKIE['_country'] == 'fr')
		$this->randSelection = "dt.upload = 0 AND ";
		else
		$this->randSelection = "dt.upload = 1 AND ";
	
  		$query="select dt.documentId, d.title,dt.correctorId,dt.date,d.extension from ".$this->_name." as dt, DOCUMENT as d where dt.documentId = d.identifier AND $this->randSelection dt.stage= $stage
  		 AND d.state =$state ";
  		if(!is_null($corr))
  		$query.=" AND dt.correctorId = $corr ";
  		$query.=" GROUP BY dt.documentId order by dt.date DESC";
  		//echo $query;
  		//exit();
  		return $this->getQuery($query,true);
  }

  public function getspellingdocs($date1,$date2,$random)
  {
  		$query="SELECT DT.documentId FROM `DOCUMENTTRACK` DT,DOCUMENT D WHERE DT.documentId = D.identifier AND DT.`stage` =1 AND DATE_FORMAT(DT.date, \"%Y%m%d\") BETWEEN $date1 AND $date2 ";
  		//return $this->getQuery($query,true);
  		if($random == 1)
		$query .= "AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($random == 2)
		$query .= "AND (D.random BETWEEN $this->argentina OR D.random=0) ";
        //echo "<br/>";
        //echo $query;
  		return $this->getNbRows($query);
  }
  
  public function getspellinglang($date1,$date2,$random,$lang = NULL)
  {
  		$query="SELECT DT.documentId FROM `DOCUMENTTRACK` DT,DOCUMENT D WHERE DT.documentId = D.identifier AND DT.`stage` =1 AND DATE_FORMAT(DT.date, \"%Y%m%d\") BETWEEN $date1 AND $date2 ";
  		//return $this->getQuery($query,true);
  		if($random == 1)
		$query .= "AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($random == 2)
		$query .= "AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		if($lang == "fr")
		$query .= "AND D.language = '$lang' ";
		else
		$query .= "AND D.language != 'fr' ";
        ///echo "<br/>";
        //echo $query;
  		return $this->getNbRows($query);
  }
  
  public function getsynopsisdocs($date1,$date2,$random)
  {
  		$query="SELECT DT.documentId FROM `DOCUMENTTRACK` DT,DOCUMENT D WHERE DT.documentId = D.identifier AND DT.`stage` =2 AND DATE_FORMAT(DT.date, \"%Y%m%d\") BETWEEN $date1 AND $date2 ";
  		//return $this->getQuery($query,true);
  		if($random == 1)
		$query .= "AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($random == 2)
		$query .= "AND (D.random BETWEEN $this->argentina OR D.random=0) ";
        //echo "<br/>";
        //echo $query;
  		return $this->getNbRows($query);
  }
  
  public function getproblemdocs($date1,$date2,$random)
  {
  		$query="SELECT DT.documentId FROM `DOCUMENTTRACK` DT,DOCUMENT D WHERE DT.documentId = D.identifier AND DT.`stage` =3 AND DATE_FORMAT(DT.date, \"%Y%m%d\") BETWEEN $date1 AND $date2 ";
  		if($random == 1)
		$query .= "AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($random == 2)
		$query .= "AND (D.random BETWEEN $this->argentina OR D.random=0) ";
        //echo "<br/>";
        //echo $query;
  		//return $this->getQuery($query,true);
  		return $this->getNbRows($query);
  }
  
  public function getphase9docs($date1,$date2,$random)
  {
  		$query="SELECT DT.documentId FROM `DOCUMENTTRACK` DT,DOCUMENT D WHERE DT.documentId = D.identifier AND DT.`stage` =4 AND DATE_FORMAT(DT.date, \"%Y%m%d\") BETWEEN $date1 AND $date2 ";
  		//return $this->getQuery($query,true);
  		if($random == 1)
		$query .= "AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($random == 2)
		$query .= "AND (D.random BETWEEN $this->argentina OR D.random=0) ";
  		return $this->getNbRows($query);
  }
  
}

?>