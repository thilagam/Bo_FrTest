<?php

/**
 * Ep_Document_DocTrack
 *  
 * @author Ram
 * @package DocTrack
 * @version 1.0
 */


class Ep_Document_DocTrack extends Ep_Db_Identifier
{
	/**
	 * The default table name 
	 */
	protected $_name = 'DOCTRACK';
	
	//Private properties
	
	/**
	 * Column names
	 *
	 * @var string $correctorId The Corrector Id
	 * @var integer $documentId The document Id
	 * @var timpstamp $date Date
	 * @var tinyint $state
	 */
      
	private $note;            //current note (from 0 to 5)
	private $correctorId;      
	private $documentId;      
	private $date;
	private $comments;       	  
	private $state;      	  
	
	//abstract method
	
	/**
	 * loadData
	 * 
	 * Sets the array elements fetched from the database
	 *
	 * @param array $array
	 * @return $this
	 */
	protected function loadData($array)
	{	
		$this->setCorrectorId($array["correctorId"]);
		$this->setDocumentId($array["documentId"]);
		$this->setComments($array["comments"]);
		$this->setDate($array["date"]);
		$this->setState($array["status"]);
		return $this;
	}
	
	/**
	 * loadIntoArray
	 * 
	 * Loads the array $array with respective records fetched from the database
	 *
	 * @return array
	 */
	protected function loadIntoArray()
	{
		$array = array();
		$array["identifier"] = $this->getIdentifier();
		$array["correctorId"] = $this->getCorrectorId();
		$array["documentId"] = $this->getDocumentId();
		$array["comments"] = $this->getComments();
		$array["status"] = $this->getStatus();
		$array["date"] = $this->getDate();
		return $array;
	}

	
	/**
	 * setCorrectorId
	 * sets value of correctorId variable
	 *
	 * @param string $correctorId
	 */
	public function setCorrectorId($correctorId)
	{
		$this->correctorId = $correctorId;
	}
	
	/**
	 * setDocumentId
	 * sets value of documentId variable
	 *
	 * @param integer $documentId
	 */
	public function setDocumentId($documentId)
	{
		$this->documentId = $documentId;
	}
	
	/**
	 * setComments
	 * sets value of documentId variable
	 *
	 * @param integer $documentId
	 */
	public function setComments($comments)
	{
		$this->comments = $comments;
	}	
	
	/**
	 * setDate
	 * sets value of date variable
	 *
	 * @param timestamp $date
	 */
	public function setDate($date)
	{
		$this->date = $date;
	}
	
	/**
	 * setState
	 * sets value of state variable
	 *
	 * @param tinyint $state
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	//get methods
	
	/**
	 * getComment
	 * this method will be used to obtain "comment" value
	 *
	 * @return text
	 */
	
	
	/**
	 * getCorrectorId
	 * this method will be used to obtain "correctorId" value
	 *
	 * @return string
	 */
	public function getComments()
	{
		return $this->comments;
	}
	
	/**
	 * getCorrectorId
	 * this method will be used to obtain "correctorId" value
	 *
	 * @return string
	 */
	public function getCorrectorId()
	{
		return $this->correctorId;
	}
	
	/**
	 * getDocumentId
	 * this method will be used to obtain "documentId" value
	 *
	 * @return integer
	 */
	public function getDocumentId()
	{
		return $this->documentId;
	}
	
	/**
	 * getDate
	 * this method will be used to obtain "date" value
	 *
	 * @return timestamp
	 */
	public function getDate()	
	{
		return $this->date;
	}
	
	/**
	 * getState
	 * this method will be used to obtain "state" value
	 *
	 * @return tinyint
	 */
	public function getStatus()	
	{
		return $this->status;
	}

	public function getdocdetails($docid){
		$query = "SELECT T.*, C.name FROM ".$this->_name." as T, CORRECTOR AS C WHERE T.documentId =".$docid." AND T.correctorId=C.identifier ORDER BY T.identifier DESC";
		if (($result = $this->getQuery($query, true)) != NULL)
			return $result;
	}
	
}
