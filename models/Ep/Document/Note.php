<?php

/**
 * Ep_Document_Note
 *  
 * @author Farid
 * @package Document
 * @version 1.0
 */


class Ep_Document_Note extends Ep_Db_Identifier
{
	/**
	 * The default table name 
	 */
	protected $_name = 'NOTE';
	
	//Private properties
	
	/**
	 * Column names
	 *
	 * @var text $comment Comment
	 * @var double $note current note (from 0 to 5)
	 * @var string $customerId The customer Id
	 * @var integer $documentId The document Id
	 * @var timpstamp $date Date
	 * @var tinyint $state
	 */
	private $comment;        
	private $note;            //current note (from 0 to 5)
	private $customerId;      
	private $documentId;      
	private $date;      	  
	private $state;      	  
	private $spelling;
	private $biblio;
	private $content;
	
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
		$this->setCustomerId($array["customerId"]);
		$this->setDocumentId($array["documentId"]);
		$this->setNote($array["note"]);
		$this->setComment($array["comment"]);
		$this->setDate($array["date"]);
		$this->setState($array["state"]);
		if ($array["spelling"] != "")
		$this->setspelling($array["spelling"]);
		if ($array["biblio"] != "")
		$this->setState($array["biblio"]);
		if ($array["content"] != "")
		$this->setcontent($array["content"]);
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
		$array["customerId"] = $this->getCustomerId();
		$array["documentId"] = $this->getDocumentId();
		$array["note"] = $this->getNote();
		$array["comment"] = $this->getComment();
		$array["state"] = $this->getState();
		$array["date"] = $this->getDate();
		if($this->getspelling() != "")
		$array["spelling"] = $this->getspelling();
		if($this->getbiblio() != "")
		$array["biblio"] = $this->getbiblio();
		if($this->getcontent() != "")
		$array["content"] = $this->getcontent();
		return $array;
	}

	/**
	 * getStar
	 * Returns an image
	 *
	 * @return image returns the image from the absolute path
	 */
	public function getStar()
	{
		$note = round($this->getNote());
		$note = $note/2;
		return "/image/star/eval".$note.".gif";
	}
	public function getRatedStar()
	{
		$note = round($this->getNote());
		$note = $note/2;
		return "/image/star/rating/eval".$note.".png";
	}
	
	//set methods
	
	/**
	 * setComment
	 * sets value of comment variable
	 *
	 * @param text $comment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
	}
	
	/**
	 * setNote
	 * sets value of note variable
	 *
	 * @param double $note
	 */
	public function setNote($note)
	{
		$this->note = $note;
	}
	
	/**
	 * setCustomerId
	 * sets value of customerId variable
	 *
	 * @param string $customerId
	 */
	public function setCustomerId($customerId)
	{
		$this->customerId = $customerId;
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
	public function setState($state)
	{
		$this->state = $state;
	}
	/**
	 * setspelling
	 * sets value of spelling variable
	 *
	 * @param text $spelling
	 */
	public function setspelling($spelling)
	{
		$this->spelling = $spelling;
	}
	/**
	 * setbiblio
	 * sets value of biblio variable
	 *
	 * @param text $biblio
	 */
	public function setbiblio($biblio)
	{
		$this->biblio = $biblio;
	}
	/**
	 * setcontent
	 * sets value of content variable
	 *
	 * @param text $content
	 */
	public function setcontent($content)
	{
		$this->content = $content;
	}	
	//get methods
	
	/**
	 * getComment
	 * this method will be used to obtain "comment" value
	 *
	 * @return text
	 */
	public function getComment()
	{
		return $this->comment;
	}
	
	/**
	 * getNote
	 * this method will be used to obtain "note" value
	 *
	 * @return double
	 */
	public function getNote()
	{
		return $this->note;
	}
	
	/**
	 * getCustomerId
	 * this method will be used to obtain "customerId" value
	 *
	 * @return string
	 */
	public function getCustomerId()
	{
		return $this->customerId;
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
	public function getState()	
	{
		return $this->state;
	}

	/**
	 * getspelling
	 * this method will be used to obtain "spelling" value
	 *
	 * @return double
	 */
	public function getspelling()
	{
		return $this->spelling;
	}
	
	/**
	 * getbiblio
	 * this method will be used to obtain "biblio" value
	 *
	 * @return string
	 */
	public function getbiblio()
	{
		return $this->biblio;
	}
	
	/**
	 * getDocumentId
	 * this method will be used to obtain "content" value
	 *
	 * @return integer
	 */
	public function getcontent()
	{
		return $this->content;
	}
	
	public function getNotesInRange($date1, $date2,$wsite){
	  if($wsite!=2){$wherestr=" AND DOCUMENT.website=".$wsite;}
		$query = "SELECT NOTE.documentId AS docId,content,spelling,biblio,DOCUMENT.website AS website, CUSTOMER.email AS email, DOCUMENT.title AS title, NOTE.note AS note, NOTE.customerId AS clientId, NOTE.comment AS comment, NOTE.date AS published, NOTE.state AS state FROM $this->_name,DOCUMENT,CUSTOMER WHERE NOTE.customerId = CUSTOMER.identifier AND DOCUMENT.identifier = NOTE.documentId AND DATE($this->_name.date) >= '$date1' AND DATE($this->_name.date) <= '$date2'".$wherestr." ORDER BY $this->_name.state ASC, $this->_name.date DESC";
		if (($result = $this->getQuery($query, false))!=NULL){
			return $result;
		}
		return Null;
	}
	public function updateNote($docId, $custId, $note, $comment, $state){
		$udata = array('state'=>$state);
		if ($note)
		{			
			$udata['spelling'] = $this->getspelling();
			$udata['content'] = $this->getcontent();
			$udata['biblio'] = $this->getbiblio();
			$udata['note'] = $note;
		}
		if ($comment)
			$udata['comment'] = $comment;
		$ev = new Ep_Document_Evaluation();
		$ev->loadById($docId);
		$ev->updateNote($note);
		$this->updateQuery($udata, "documentId = $docId AND customerId = '$custId' ");
	}
	public function getNotesByDocumentId($docId,$wsite){
	  if($wsite!=2){$wherestr=" AND DOCUMENT.website=".$wsite;}
		$query ="SELECT NOTE.documentId AS docId,content,spelling,biblio,DOCUMENT.website AS website, CUSTOMER.email AS email, DOCUMENT.title AS title, NOTE.note AS note, NOTE.customerId AS clientId, NOTE.comment AS comment, NOTE.date AS published, NOTE.state AS state FROM $this->_name,DOCUMENT,CUSTOMER WHERE NOTE.customerId = CUSTOMER.identifier AND DOCUMENT.identifier = NOTE.documentId AND documentId=$docId".$wherestr." ORDER BY $this->_name.state ASC, $this->_name.date DESC";
		//echo $query;
 		if (($result = $this->getQuery($query, false))!=Null){
			return $result;
		}
		return Null;
	}
	public function getNotesByCustomerId($custId,$wsite){
	  if($wsite!=2){$wherestr=" AND DOCUMENT.website=".$wsite;}
		$query ="SELECT NOTE.documentId AS docId,content,spelling,biblio,DOCUMENT.website AS website, CUSTOMER.email AS email, DOCUMENT.title AS title, NOTE.note AS note, NOTE.customerId AS clientId, NOTE.comment AS comment, NOTE.date AS published, NOTE.state AS state FROM $this->_name,DOCUMENT,CUSTOMER WHERE NOTE.customerId = CUSTOMER.identifier AND DOCUMENT.identifier = NOTE.documentId AND NOTE.customerId='$custId' ".$wherestr." ORDER BY $this->_name.state ASC, $this->_name.date DESC";
		if (($result = $this->getQuery($query, false))!=Null){
			return $result;
		}
		return Null;
	}
	public function getNoteByCustomerIdDocumentId($custId, $docId){
		$query = "SELECT NOTE.comment AS comment,content,spelling,biblio,DOCUMENT.website AS website, DOCUMENT.extension AS extension FROM $this->_name, DOCUMENT WHERE $this->_name.customerId='".$custId."' AND documentId=$docId AND DOCUMENT.identifier = $this->_name.documentId";
		if (($result = $this->getQuery($query, false))!=Null){
			return $result;
		}
		return Null;

	}
	
	public function getNoteAvg()
	{
		if($this->getspelling() != "")
		$spelling = $this->getspelling();
		else
		$spelling = 0;
		if($this->getcontent() != "")
		$content = $this->getcontent();
		else 
		$content = 0;
		if($this->getbiblio() != "")
		$biblio = $this->getbiblio();
		else 
		$biblio = 0;
		$this->my_obj = new Ep_Db_ArrayDb2();
		$notesWeight = $this->my_obj->loadArrayv2("notesWeight", $_COOKIE['_country']);
		$Wspelling = $notesWeight['spelling'];
		$Wbiblio = $notesWeight['biblio'];
		$Wcontent = $notesWeight['content'];
		$total = ($spelling*$Wspelling)+($content*$Wcontent)+($biblio*$Wbiblio);
		$Wtotal = $Wspelling+$Wcontent+$Wbiblio;
		if($total != 0)
		{
			return round($total/$Wtotal);
		}
		else
		return 0;
	}
	
	/**
	 * this will calculates which doc has to be moved from the dacodoc site to oboulo
	 *
	 * 
	 */
	public function calNoteAvg()
	{
		$my_obj = new Ep_Db_ArrayDb2();
		$marksAvg = $my_obj->loadArrayv2("marksAvg", $_COOKIE['_country']);
		$positiveMark = $marksAvg['positiveMark'];
		$minPostMarks = $marksAvg['minPostMarks'];
		$propPercentage = $marksAvg['propPercentage'];
		$markFormula = $marksAvg['markFormula'];
		$query = "SELECT count(`note`) AS cnt, N.`documentId` FROM ".$this->_name." as N, DOCUMENT as D WHERE N.`documentId` = D.identifier AND   
		N.note >= $positiveMark AND N.`documentId` NOT IN (SELECT documentId from MARKNOTE) AND D.online = 1 AND D.website = 1 GROUP BY N.`documentId` HAVING count(*)>$minPostMarks ORDER BY N.`documentId` ";
        $tempRes = $this->getQuery($query,true);
        //echo "<br/>";
        foreach ($tempRes as $in)
        {
        	$query = "SELECT count(`note`) AS cnt FROM ".$this->_name." WHERE  note < $positiveMark AND `documentId`=".$in['documentId']." ";
        	$negRes = $this->getQuery($query,true);
        	$positive = $in['cnt'];
        	$negative = $negRes[0]['cnt'];
        	//echo 'dddd-'.$markFormula;       	
        	eval("\$resMark= $markFormula;");
        	if($resMark)
        	{
        		//$resArr[]['markresult'] = (1-$in['cnt']/$negRes[0]['cnt']);
        		$resArr[$in['documentId']]['negative'] = $negRes[0]['cnt'];
        		$resArr[$in['documentId']]['positive'] = $in['cnt'];
        		//$resArr[]['documentId'] = $in['documentId'];
        	}
        	//echo "<br/>";
        }
        return $resArr;
	}
}
