<?php

/**
 * Ep_Document_Document
 * 
 * this class will come over ep_document class. Hence, ep_document is not needed for project 3.1 
 * 
 * @author Milan
 * @package Document
 * @version 3.0
 * Update:- new methods added
 * Update:- createDocumentObject( ) is been renamed to setDetails( ) and does not return the document array anymore
 * Update:- modified search() with hp parameter to work just like HpSearch()
 */

class Ep_Document_Document extends Ep_Db_Identifier 
{
	/**
	 * The default table name 
	 */
	protected $_name = 'DOCUMENT';
		
	//column names
	private $subject;
	private $category;
	private $type;
	private $language;
	private $nbConsult;
	private $customerId;
	private $name;
	private $date;
	private $title;
	private $url;
	private $ssd;
	private $description;
	private $extension;
	private $nbPage;
	private $sendingId;
	private $sendDocId;
	private $correctorId;
	private $state;
	private $level;
	private $online;
	private $random;
	private $website;
	private $dlMode=0;
	private $colname;
	
	private $indian = "1 AND 80";
	private $argentina = "81 AND 99";
		
	//external
	private $price;
	private $price2;
	
	//related docs array from ssd
	private $relatedDocsArray;
	

	public function __construct($name = NULL) 
    {
		parent::__construct ();
		if ($name)
			$this->_setupTableName($name);
			
		//$this->colname = Zend_Registry::get('_ipcountry');
	}
	
	protected function loadData($array) {
		$this->setIdentifier ( $array ["identifier"] );
		$this->setSubject ( $array ["subject"] );
		$this->setCategory ( $array ["category"] );
		$this->setType ( $array ["type"] );
		$this->setLanguage ( $array ["language"] );
		$this->setNbConsult ( $array ["nbConsult"] );
		$this->setCustomerId ( $array ["customerId"] );
		$this->setName ( $array ["name"] );
		$this->setDate ( $array ["date"] );
		$this->setTitle ( $array ["title"]);
		$this->setDescription ( $array ["description"]);
		$this->setUrl ( $array ["url"] );
		$this->setSsd ( $array ["ssd"] );
		$this->setExtension ( $array ["extension"] );
		$this->setNbPage ( $array ["nbPage"] );
		
		$this->setPrice ( $array ["price"] );
		$this->setPrice2 ( $array ["price2"] );
		
		$this->setRating ( $array ["rating"] );
		$this->setScaleId ( $array ["scaleId"] );
		$this->setSendingId ( $array ["sendingId"] );
		$this->setSendDocId ( $array ["sendDocId"] );
		$this->setCorrectorId ( $array ["correctorId"] );
		$this->setState ( $array ["state"] );
		$this->setLevel ( $array ["level"] );
		$this->setOnline ( $array ["online"] );
		$this->setToken ( $array ["token"] );
		if($_COOKIE['_country'] == 'fr')
		{
			if ($array["random"] != "")
			$this->setRandom($array["random"]);
		}
		if($_COOKIE['_country'] == 'fr')
		{
			if($array["website"] != "")
			$this->setWebsite($array["website"]);
		}
		
		if($array ["dlMode"]){
			$this->setdlMode ( $array ["dlMode"] );
		}
        
        if($array ["lastupdate"])
        {
            $this->setlastupdate($array["lastupdate"]);
        }		
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
		$array = array ( );
		$array ["identifier"] = $this->getIdentifier ();
		$array ["subject"] = $this->getSubject ();
		$array ["category"] = $this->getCategory ();
		$array ["type"] = $this->getType ();
		$array ["language"] = $this->getLanguage ();
		$array ["nbConsult"] = $this->getNbConsult ();
		$array ["customerId"] = $this->getCustomerId ();
		$array ["name"] = $this->getName ();
		$array ["date"] = $this->getDate ();
		$array ["title"] = $this->getTitle ();
		$array ["description"] = $this->getDescription ();
		//echo 'Load '.$array ["description"] . 
		$array ["url"] = $this->getUrl ();
		$array ["ssd"] = $this->getSsd ();
		$array ["extension"] = $this->getExtension ();
		$array ["nbPage"] = $this->getNbPage ();
		$array ["sendingId"] = $this->getSendingId ();
		if(!is_null($this->getSendDocId()))
		$array ["sendDocId"] = $this->getSendDocId ();
		$array ["correctorId"] = $this->getCorrectorId ();
		$array ["state"] = $this->getState ();
		$array ["level"] = $this->getLevel ();
		$array ["online"] = $this->getOnline ();
		$array ["token"] = $this->getToken ();
		if($_COOKIE['_country'] == 'fr')
	    if($this->getRandom() != "")
		{
			$array["random"] = $this->getRandom();
		}
		if($_COOKIE['_country'] == 'fr')
	    if($this->getWebsite() != "")
		{
			$array ["website"] = $this->getWebsite ();
		}
		if($this->getdlMode() != "")
        {
			$array ["dlMode"] = $this->getdlMode();
		}
        if($this->getlastupdate() != "")
        {
            $array ["lastupdate"] = $this->getlastupdate();
        }
		return $array;
	}
	
	/**
	 * setDetails
	 * 
	 * Set the details, fetched information from the database
	 *
	 * @param string $docID
	 * @return true/false
	 */
	public function setDetails($docID = NULL, $wsite = 0) 
	{
		if($docID != NULL)
		{
			$this->setIdentifier ( intval ( $docID ) );
			$query = "select * from " . $this->_name . " where identifier = " . $docID ." AND website = ".$wsite;
			if (($result = $this->getQuery ( $query, TRUE )) != NULL) {
				$this->type = $result [0] ['type'];
				$this->subject = $result [0] ['subject'];
				$this->language = $result [0] ['language'];
				$this->nbConsult = $result [0] ['nbConsult'];
				$this->customerId = $result [0] ['customerId'];
				$this->name = $result [0] ['name'];
				$this->date = $result [0] ['date'];
				$this->title = $result [0] ['title'];
				$this->description = $result [0] ['description'];
				$this->url = $result [0] ['url'];
				$this->ssd = $result [0] ['ssd'];
				$this->extension = $result [0] ['extension'];
				$this->nbPage = $result [0] ['nbPage'];
				$this->sendingId = $result [0] ['sendingId'];
				$this->sendDocId = $result [0] ['sendDocId'];
				$this->correctorId = $result [0] ['correctorId'];
				$this->state = $result [0] ['state'];
				$this->level = $result [0] ['level'];
				$this->online = $result [0] ['online'];
				$this->category = $result [0] ['category'];
				$this->website = $result [0] ['website'];
				return true;
			} else
				return false;
		}
		else 
		return false;
	
	}
	/**
     * lastupdate
     * Sets value of lastupdate variable
     *
     * @param date lastupdate
     */
    public function setlastupdate($lastupdate) {
        $this->lastupdate = $lastupdate;
    }    
    
    public function getlastupdate() {
        return $this->lastupdate;
    }
    
    
    /**
	 * setdlMode
	 * Sets value of website variable
	 *
	 * @param tinyint $website
	 */
	public function setdlMode($dlMode) {
		$this->dlMode = $dlMode;
	}	
	/**
	 * getDlmode
	 * this method will be used to obtain "dlMode" value
	 *
	 * @return website
	 */
	public function getdlMode() {
		return $this->dlMode;
	}
	
	/**
	 * getDetails
	 * This method is used to return an array of all the variables
	 * the naming convention used is based on the format used to array the results from server
	 * 
	 * @return mixed Array
	 */
	public function getDetails()
	{
		return array ("title" => $this->title, "context" => urldecode ( $this->description ), "format" => $this->extension, "type" => $this->type, "subject" => $this->subject, "nb" => $this->nbPage, "lang" => $this->language, "date" => $this->getDate ( $this->date ), "rating" => 0, "level" => $this->level, "state" => $this->state, "nbConsult" => $this->nbConsult, "website" => $this->website, "price" => 0 / 100 );
	}
	
	//load by sendDocId
 	public function loadBySendDocId($sendDocId)
 	 {
  		$query = "SELECT * FROM ".$this->_name." where sendDocId = '$sendDocId'";
		return $this->loadByQuery($query);
  	}
		
	//load by sendingId
 	public function loadBySendingId($sendingId)
 	 {
  		$query = "SELECT * FROM ".$this->_name." where sendingId = '$sendingId' and state=8" ;
		return $this->getQuery ( $query, TRUE );
  	}
		
	/**
	 * getAllDetails
	 * This method will gather every details, including price and ratings and
	 * will return back in the form of an array
	 * @return Array an associative array with all columnNames are index and values
	 */
	public function getAllDetails()
	{
		$tempID = $this->getIdentifier ();
		
		/******* making the query and executing it *********/
		$query = "select *,D.date AS ddate from " . $this->_name . " AS D,PRICE AS P,EVALUATION AS E where D.identifier = " . $tempID . " and P.documentId = " . $tempID . " and E.identifier = " . $tempID;
		//echo $query;
		if (($result = $this->getQuery ( $query, TRUE )) != NULL) 
		{
			$this->setPrice($result [0] ['customerPrice']);
			$this->setPrice2($result [0] ['customerPrice2']);
			
			if(Zend_Registry::get('_country')=='en' || Zend_Registry::get('_country')=='fr')
				$price = $this->getEffectivePriceCountry($this->checkIp()); //$this->colname
			else
				$price = $this->getEffectivePrice();
					
			$res = array ('id' => $result [0] ['identifier'],
			"type" => $result [0] ['type'],
			"subject" => $result [0] ['subject'],
			"lang" => $result [0] ['language'],
			"nbConsult" => $result [0] ['nbConsult'],
			"date" => $result [0] ['ddate'],
			"title" => $result [0] ['title'],
			"context" => $result [0] ['description'],
			"format" => $result [0] ['extension'],
			"nb" => $result [0] ['nbPage'],
			"state" => $result [0] ['state'],
			"level" => $result [0] ['level'],
			"rating" => $result [0] ['totalAverage'],
			"price" => 	$price,
			"customerId" => $result [0] ['customerId'],
			'extension'=>$result[0]['extension'],
			'category' =>$result[0]['category'],
			'random' =>$result[0]['random'] );
			return $res;
		} 
		else
			return NULL;
   }
    
	/** Search the subjects within 2 dates for NewsLetter Page***/
    public function searchForNewsletter($subject = NULL, $type = NULL, $extension = NULL, $nb = NULL, $lang = NULL, $start = 0, $number=100, $order = "id", $orderBy, $ratio = false, $hp=false, $toDate, $endDate) 
	{
		$query = "select D.identifier as id,D.title,D.subject,D.type,D.language,D.extension,D.nbConsult as nbConsult,D.nbPage,D.date,D.level,D.description"; 
		$query.= " FROM DOCUMENT D ";
		if($ratio)$query .= ",DOCUMENTRATIO R ";
		$query .="WHERE ";
		if($ratio)$query .= "R.identifier = D.identifier AND ";
		if($hp)$query .= "token = 0 AND ";
		
		// criteria definition
		if ($subject != NULL)
		    //$query = "select count(D.subject) as counter,D.identifier as id,D.title,D.subject,D.type,D.language,D.extension,P.customerPrice as price,D.nbConsult as nbConsult,D.nbPage,D.date,D.level,D.description,E.totalAverage as rating"; 
			$query .= "D.subject='" . strtoupper ( $subject ) . "' AND ";
		if ($type != NULL)
			$query .= "D.type='" . strtoupper ( $type ) . "' AND ";
		if ($lang != NULL)
			$query .= "D.language='" . strtolower ( $lang ) . "' AND ";
		if ($nb != NULL)
			if ($nb <= 10)
				$query .= "D.nbPage < " . $nb . " AND "; elseif ($nb == 1000)
				$query .= "D.nbPage > 50 AND "; else
				$query .= "D.nbPage >= " . ($nb - 10) . " AND ";
		   
		if ($extension != NULL)
			//$query .= "D.extension=" . $extension . " AND ";
		$query .= "D.identifier = P.documentID AND D.identifier = E.identifier AND D.online = 1";
		
		if($toDate != NULL && $endDate != NULL)
		   $date1 = $endDate.'000000';
		   $date2 = $toDate.'000000';
		   $query .= " D.date BETWEEN $date1 AND $date2 ";  

		if($ratio)$orderBy2 = "ratio $orderBy, ";
			$query .= " ORDER BY $orderBy2 $order $orderBy LIMIT $start, $number";
		
		$result = $this->getQuery ( $query, TRUE );
		return $result;
	}
	public function checkIp()
    {
  	  	$country = Zend_Registry::get('_country');
  		$ip = Zend_Registry::get('_ipcountry');
  		
  		if(strlen($ip)>3)
  		{
  			return Zend_Registry::get('_ipcountry');
  		}
  		else
  		{	
  			$mcObj = new Ep_Db_MultiCurrencyXml();
  			$columnname = $mcObj->readcolumnname($ip,$country);
  			//echo $columname; 
  			return $columnname;
  		}
  		
    }
		
	/**
	 * search
	 * 
	 * Search a document on based on some criteria
	 *
	 * @param string $subject
	 * @param string $type
	 * @param string $extension
	 * @param integer $nb
	 * @param string $lang
	 * @param integer $start
	 * @param integer $number
	 * @param string $order
	 * @param string $orderBy
	 * @param boolean $ratio
	 * 
	 * @return array
	 */
	public function search($subject = NULL, $type = NULL, $extension = NULL, $nb = NULL, $year = NULL, $lang = NULL, $start = 0, $number = 100, $order = "id", $orderBy = "DESC", $ratio = false, $hp=false) 
	{
			// check what is the default column name if it is not for US. Earlier it was "customerPrice" 

			$query = "select D.identifier as id,D.title,D.subject,D.category,D.type,D.language,D.extension,D.nbConsult as nbConsult,D.nbPage,D.date,D.level,D.description,D.url,D.ssd,E.totalAverage as rating"; 
			
			$query.= " FROM DOCUMENT D, EVALUATION E ";
			if($ratio)$query .= ",DOCUMENTRATIO R ";
			$query .="WHERE ";
			if($ratio)$query .= "R.identifier = D.identifier AND ";
			if($hp)$query .= "token = 0 AND ";
            if($year) $query .= "D.date LIKE '$year%' AND ";
			
			// criteria definition
			if ($subject != NULL)
			    //$query = "select count(D.subject) as counter,D.identifier as id,D.title,D.subject,D.type,D.language,D.extension,P.customerPrice as price,D.nbConsult as nbConsult,D.nbPage,D.date,D.level,D.description,E.totalAverage as rating"; 
				$query .= "D.subject='" . strtoupper ( $subject ) . "' AND ";
			if ($type != NULL)
				$query .= "D.type='" . strtoupper ( $type ) . "' AND ";
			if ($lang != NULL)
				$query .= "D.language='" . strtolower ( $lang ) . "' AND ";
			if ($nb != NULL)
				if ($nb <= 10)
					$query .= "D.nbPage < " . $nb . " AND "; elseif ($nb == 1000)
					$query .= "D.nbPage > 50 AND "; else
					$query .= "D.nbPage >= " . ($nb - 10) . " AND ";
			if ($extension != NULL)
				$query .= "D.extension=" . $extension . " AND ";
			
			$query .= "D.online = 1 AND D.identifier = E.identifier";
	
			if($ratio)
			$orderBy2 = "ratio $orderBy, ";
			
			$query .= " ORDER BY $orderBy2 $order $orderBy LIMIT $start, $number";

			$result = $this->getQuery ( $query, TRUE );
			
	//		print_r($result);
		return $result;
	}

	/**
	 * searchCat
	 * 
	 * Search by category
	 *
	 * @param array $array
	 * @param string $type
	 * @param string $extension
	 * @param integer $nb
	 * @param string $lang
	 * @param integer $start
	 * @param integer $number
	 * @param string $order
	 * @param string $orderBy
	 * @param boolean $ratio
	 * @return array
	 */
	public function searchCat($array = NULL, $type = NULL, $extension = NULL, $nb = NULL, $year=NULL, $lang = NULL, $start = 0, $number = 100, $order = "id", $orderBy = "DESC", $ratio = false) 
	{
		$query = "select D.identifier as id,D.title,D.subject,D.type,D.category,D.language,D.extension,D.nbConsult as nbConsult,D.nbPage,D.date,D.level,D.description,D.url,D.ssd,E.totalAverage as rating 
		FROM " . $this->_name . " as D, EVALUATION E ";
		if($ratio)$query .= ",DOCUMENTRATIO R";
		$query .= ",PRICE P ";
		$query .="WHERE D.identifier = P.documentID ";
		if($ratio)$query .= " AND R.identifier = D.identifier ";
        if($year) $query .=" AND D.date LIKE '$year%' ";
				
		$query .= "AND (";
		if(isset($array['subject']))
		{
			foreach($array['subject'] as $subject)
			{
				$query .= "$OR D.subject = '".$subject."' " ;
				$OR = "OR";
			}
		}
		if(isset($array['type']))
		{ 
			foreach($array['type'] as $subject)
			{
				$query .= "$OR D.type = '".$subject."' " ;
				$OR = "OR";
			}
		}	
		if(isset($array['language']))
		{
			foreach($array['language'] as $subject)
			{
				$query .= "$OR D.language = '".$subject."' " ;
				$OR = "OR";
			}
		}
		$query .= ") ";	
		if ($type != NULL)
			$query .= "AND D.type='" . strtoupper ( $type ) . "'  ";
		if ($lang != NULL)
			$query .= "AND D.language='" . strtolower ( $lang ) . "' ";
		if ($nb != NULL)
			if ($nb <= 10)
				$query .= "AND D.nbPage < " . $nb . " "; 
			elseif ($nb == 1000)
				$query .= "AND D.nbPage > 50 "; 
			else
				$query .= "AND D.nbPage >= " . ($nb - 10) . " ";
		if ($extension != NULL)
			$query .= "AND D.extension=" . $extension . " ";
		
		$query .= " AND D.online = 1 AND D.identifier = E.identifier";
		
		if($ratio)$orderBy2 = "ratio $orderBy, ";
		$query .= " ORDER BY $orderBy2 $order $orderBy LIMIT $start, $number";
		
		//var_dump($query);
		//$start = microtime(true);
		$result = $this->getQuery ( $query, TRUE );
		//var_dump("total time consumed by query:".(microtime(true)-$start));
		return $result;
	}
	
	/**
	 * searchTopConsult
	 * Searches the best seller document
	 * 
	 * @param string $subject
	 * @param char $type
	 * @param string $extension
	 * @param integer $nb
	 * @param string $lang
	 * @param string $date
	 * @param integer $begin
	 * @param integer $end
	 * @return array
	 */
	function searchTopConsult($subject = NULL, $type = NULL, $extension = NULL, $nb = NULL, $year=NULL,  $lang = NULL, $date, $begin = 0, $end = 10) 
	{
		$query = "select D.identifier as id,D.category AS category, D.title,D.subject,D.type,D.language,D.extension,P.customerPrice as price,DT.nbConsult as nbConsult,D.nbPage,D.date,D.level,D.description,D.url,D.ssd,E.totalAverage as rating
			  FROM DOCUMENT D, DOCUMENTTOP DT, PRICE P ,EVALUATION E WHERE ";
		// criteria definition
		if ($subject != NULL)
			$query .= "D.subject='" . strtoupper ( $subject ) . "' AND ";
		if ($type != NULL)
			$query .= "D.type='" . strtoupper ( $type ) . "' AND ";
		if ($lang != NULL)
			$query .= "D.language='" . strtolower ( $lang ) . "' AND ";
        if($year != NULL)
            $query .= "D.date LIKE '$year%' AND ";
		if ($nb != NULL)
			if ($nb <= 10)
				$query .= "D.nbPage < " . $nb . " AND "; elseif ($nb == 1000)
				$query .= "D.nbPage > 50 AND "; else
				$query .= "D.nbPage >= '" . ($nb - 10) . "' AND ";
		if ($extension != NULL)
			$query .= "D.extension=" . $extension . " AND ";
		$query .= "DT.identifier = D.identifier AND D.online = 1
			  AND D.identifier = E.identifier
			  AND D.identifier = P.documentId
			  ORDER BY DT.nbConsult DESC LIMIT $begin,$end";
	return $this->getQuery ( $query, true );
	}
	
	/**Returns the top consulted documents with given parent category.
	 * 
	 * @param string $category
	 * @param integer $limit
	 */
	function searchTopConsultByCategory($category, $limit=5){
		$query = "SELECT * FROM DOCUMENT WHERE category='".$category."' ORDER BY nbConsult DESC LIMIT 0, $limit";
		return $this->getQuery($query);
	}

	/**
	 * Returns top consulted documents in specified category.
	 * @param string $category
	 * @param integer $limit
	 */
	function searchByCategory($category, $limit=5){
		$query = "SELECT * FROM DOCUMENT WHERE category='".$category."' ORDER BY identifier DESC LIMIT 0, $limit";
		return $this->getQuery($query);
	}
	
	/**
	 * searchNb
	 * 
	 * Search by number of pages
	 *
	 * @param char $subject
	 * @param char $type
	 * @param tinyint $extension
	 * @param integer $nb
	 * @param string $lang
	 * @param booloean $ratio
	 * @return array
	 */
	public function searchNb($subject = NULL, $type = NULL, $extension = NULL, $nb = NULL, $year=NULL, $lang = NULL, $ratio = false) 
	{
		$query = "select D.identifier from " . $this->_name . " as D, PRICE P, EVALUATION E ";
		if($ratio)$query .= ",DOCUMENTRATIO R ";
		$query .="WHERE ";
		if($ratio)$query .= "R.identifier = D.identifier AND ";
			
		// criteria definition
		if ($subject != NULL)
			$query .= "D.subject='" . strtoupper ( $subject ) . "' AND ";
		if ($type != NULL)
			$query .= "D.type='" . strtoupper ( $type ) . "' AND ";
		if ($lang != NULL)
			$query .= "D.language='" . strtolower ( $lang ) . "' AND ";
        if($year != NULL)
            $query .="D.date LIKE '$year%' AND ";
		if ($nb != NULL)
			if ($nb <= 10)
				$query .= "D.nbPage < " . $nb . " AND "; elseif ($nb == 1000)
				$query .= "D.nbPage > 50 AND "; else
				$query .= "D.nbPage >= " . ($nb - 10) . " AND ";
		if ($extension != NULL)
			$query .= "D.extension=" . $extension . " AND ";

		$query .= "D.identifier = P.documentID AND D.identifier = E.identifier AND D.online = 1 ";
		//if($lang == 'fr')
		//echo $query."<br/>";
		return $this->getNbRows ($query);
	}

	/**
	 * searchNbCat
	 * Search by number of category
	 *
	 * @param array $array
	 * @param char $type
	 * @param tinyint $extension
	 * @param integer $nb
	 * @param string $lang
	 * @param booloean $ratio
	 * @return array
	 */
	public function searchNbCat($array = NULL, $type = NULL, $extension = NULL, $nb = NULL, $year=NULL, $lang = NULL, $ratio = false) 
	{
		$query = "select D.identifier as id 
		FROM " . $this->_name . " as D, PRICE P, EVALUATION E ";
		if($ratio)$query .= ",DOCUMENTRATIO R ";
		$query .="WHERE D.identifier = P.documentID ";
        if($year) $query .=" AND D.date LIKE '$year%' ";
		if($ratio)$query .= " AND R.identifier = D.identifier ";
		if(isset($array['subject']) || isset($array['type']) || isset($array['language']))
		$query .= "AND (";
		if(isset($array['subject']))
		{
			foreach($array['subject'] as $subject)
			{
				$query .= "$OR D.subject = '".$subject."' " ;
				$OR = "OR";
			}
		}
		if(isset($array['type']))
		{
			foreach($array['type'] as $subject)
			{
				$query .= "$OR D.type = '".$subject."' " ;
				$OR = "OR";
			}
		}	
		if(isset($array['language']))
		{
			foreach($array['language'] as $subject)
			{
				$query .= "$OR D.language = '".$subject."' " ;
				$OR = "OR";
			}
		}
		if(isset($array['subject']) || isset($array['type']) || isset($array['language']))
		$query .= ") ";	
		if ($type != NULL)
			$query .= "AND D.type='" . strtoupper ( $type ) . "'  ";
		if ($lang != NULL)
			$query .= "AND D.language='" . strtolower ( $lang ) . "' ";
		if ($nb != NULL)
			if ($nb <= 10)
				$query .= "AND D.nbPage < " . $nb . " "; 
			elseif ($nb == 1000)
				$query .= "AND D.nbPage > 50 "; 
			else
				$query .= "AND D.nbPage >= " . ($nb - 10) . " ";
		if ($extension != NULL)
			$query .= "AND D.extension=" . $extension . " ";
		
		$query .= " AND D.identifier = E.identifier AND D.online = 1 ";
		$result = $this->getNbRows ($query);
		

		return $result;
	}
	
	/**
	 * searchNbTopConsult
	 * Searches the number of best seller document
	 * 
	 * @param char $subject
	 * @param char $type
	 * @param tinyint $extension
	 * @param integer $nb
	 * @param string $lang
	 * @param string $date
	 * @return array
	 */
	public function searchNbTopConsult($subject = NULL, $type = NULL, $extension = NULL, $nb = NULL, $year = NULL, $lang = NULL, $date) 
	{
		$query = "SELECT D.identifier FROM DOCUMENT D, DOCUMENTTOP DT, PRICE P ,EVALUATION E WHERE ";
		// criteria definition
		if ($subject != NULL)
			$query .= "D.subject='" . strtoupper ( $subject ) . "' AND ";
		if ($type != NULL)
			$query .= "D.type='" . strtoupper ( $type ) . "' AND ";
		if ($lang != NULL)
			$query .= "D.language='" . strtolower ( $lang ) . "' AND ";
        if ($year != NULL)
            $query .= "D.date LIKE '$year%' AND ";
		if ($nb != NULL)
			if ($nb <= 10)
				$query .= "D.nbPage < " . $nb . " AND "; elseif ($nb == 1000)
				$query .= "D.nbPage > 50 AND "; else
				$query .= "D.nbPage >= '" . ($nb - 10) . "' AND ";
		if ($extension != NULL)
			$query .= "D.extension=" . $extension . " AND ";
		$query .= " DT.identifier = D.identifier AND D.online = 1
			  AND D.identifier = E.identifier
			  AND D.identifier = P.documentId";
		return $this->getNbRows ($query);
	}

	/**
	 * selectByCustomerId
	 * Select * from the table for a particular customerId and the document which is online
	 *
	 * @param string $customerId
	 * @param integer $idMax
	 * @param string $orderBy
	 * @param string $orderType
	 * @param integer $limit
	 * @return array
	 */
	public function selectByCustomerId($customerId, $idMax = "", $orderBy = "", $orderType = "DESC", $limit = "")
	{
		/*
		$query = "SELECT * FROM " . $this->_name . " WHERE customerId = '$customerId' AND online = 1 ";
		if ($idMax)
			$query .= "AND identifier < $idMax ";
		if ($orderBy)
			$query .= "ORDER BY $orderBy $orderType ";
		if ($limit)
			$query .= "LIMIT 0,$limit";
		*/
		$query = "SELECT * FROM " . $this->_name . " WHERE customerId = '$customerId' AND online = 1 ";
		if ($idMax)
			$query .= "AND identifier < $idMax ";
		if ($orderBy)
			$query .= "ORDER BY $orderBy $orderType ";
		if ($limit)
			$query .= "LIMIT 0,$limit";

		return $this->getQuery ( $query, true );
	}

	/**
	 * searchSubCat
	 * Select * from from the table "SUBJECT" where the parentId is $code
	 * 
	 * @param string $code
	 * @return array
	 */
	public function searchSubCat($code)
	{
		$array = array();
		$query = "select * from SUBJECT where parentId = '$code'";
		$tab = $this->getQuery($query,true);
		foreach($tab as $t)
			$array[$t["type"]][] = $t["code"];
		return $array;
	}
	
	/**
	 * selectById
	 * Select by Id from DOCUMENT,PRICE and EVALUATION table
	 *
	 * @param string $id
	 * @return array
	 */
	public function selectById($id) 
	{
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating FROM DOCUMENT D,PRICE P,EVALUATION E WHERE D.identifier = P.documentId AND E.identifier = D.identifier AND D.identifier = '$id'";
		return $this->loadByQuery($query);	// ORIGINAL LOCATION
	}

	/**
	 * checkById
	 * Check if this doc is online
	 *
	 * @param string $id
	 * @return boolean
	 */
	public function checkById($id) 
	{
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating FROM DOCUMENT D,PRICE P,EVALUATION E WHERE D.identifier = P.documentId AND E.identifier = D.identifier AND D.identifier = '$id' AND D.online=1";
		return $this->loadByQuery($query);	// ORIGINAL LOCATION
	}
	
	/**
	 * searchRelation
	 * this method will look into relation table to see if the document has another document
	 * been suggested
	 * @param array id id of the document
	 * @return array document_id document id of suggested document
	 */
	public function searchRelation($array)
	{
		if(count($array))
		{
			$query = "select DISTINCT documentIdR as id, DOCUMENT.title from RELATION,DOCUMENT where DOCUMENT.identifier = RELATION.documentIdR ";
			foreach($array as $a)
			{
				$query .= "AND documentIdR != ".$a." ";
			}
			
			$query .= " AND (";
			$OR = '';
			foreach($array as $a)
			{
				$query .= $OR." RELATION.documentId = ".$a." ";
				$OR = "OR";
			}
	
			$query .= ") ORDER BY nbRelation DESC";
			return $this->getQuery($query,TRUE);
		}
		else
			return array();
	}
	
	public function findSubject($articleID)
	{
		$query = "select subject from DOCUMENT where identifier = '$articleID'";
		$result = $this->getQuery ( $query, TRUE );
		return $result;
	}
	//set methods
	
	public function setRandom($random)  // it will set random number
	{
		$this->random = $random;
	}
	
	/**
	 * setSubject
	 * Sets value of subject variable
	 * 
	 *
	 * @param char $subject
	 */
	
	public function setSubject($subject) 
	{
		$this->subject = $subject;
	}
	
	/**
	 * setCategory
	 * Sets value of category variable
	 * 
	 *
	 * @param char $category
	 */
	
	public function setCategory($category) 
	{
		$this->category = $category;
	}
	
	/**
	 * setType
	 * Sets value of type variable
	 *
	 * @param char $type
	 */
	public function setType($type) 
	{
		$this->type = $type;
	}
	
	/**
	 * setlanguage
	 * Sets value of language variable
	 *
	 * @param char $language
	 */
	public function setLanguage($language) 
	{
		$this->language = $language;
	}
	
	/**
	 * setNbConsult
	 * Sets value of nbConsult variable
	 *
	 * @param integer $nbConsult
	 */
	public function setNbConsult($nbConsult) 
	{
		$this->nbConsult = $nbConsult;
	}
	
	/**
	 * setCustomerId
	 * Sets value of customerId variable
	 *
	 * @param string $customerId
	 */
	public function setCustomerId($customerId) {
		$this->customerId = $customerId;
	}
	
	/**
	 * setName
	 * Sets value of name variable
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * setDate
	 * Sets value of date variable
	 *
	 * @param string $date
	 */
	public function setDate($date) {
		$this->date = $date;
	}
	
	/**
	 * setTitle
	 * Sets value of title variable
	 *
	 * @param tinytext $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * setDescription
	 * Sets value of description variable
	 *
	 * @param text $description
	 */
	public function setDescription($description) {
		$this->description = $description;
		//echo 'Des '.$description;
	}
	
	/**
	 * setUrl
	 * Sets value of url variable
	 *
	 * @param String $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}
	
	/**
	 * setUrl
	 * Sets value of ssd variable
	 *
	 * @param tinytext $ssd
	 */
	public function setSsd($ssd) {
		$this->ssd = $ssd;
	}
	
	/**
	 * setExtension
	 * Sets value of extension variable
	 *
	 * @param tinyint $extension
	 */
	public function setExtension($extension) {
		$this->extension = $extension;
	}
	
	/**
	 * setNbPage
	 * Sets value of nbPage variable
	 *
	 * @param smallint $nbPage
	 */
	public function setNbPage($nbPage) {
		$this->nbPage = $nbPage;
	}
	
	/**
	 * setPrice
	 * Sets value of price variable
	 *
	 * @param integer $price
	 */
	public function setPrice($price) {
		$this->price = $price;
	}
	
	/**
	 * setPrice2
	 * Sets value of price2 variable
	 *
	 * @param integer $price
	 */
	public function setPrice2($price) {
		$this->price2 = $price;
	}

	/**
	 * setRating
	 * Sets value of rating variable
	 *
	 * @param --------------------------------- $rating
	 */
	public function setRating($rating) {
		$this->rating = $rating;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $scaleId
	 */
	public function setScaleId($scaleId) {
		$this->scaleId = $scaleId;
	}

	/**
	 * setSendingId
	 * Sets value of sendingId variable
	 *
	 * @param string $sendingId
	 */
	public function setSendingId($sendingId) {
		$this->sendingId = $sendingId;
	}
	
	/**
	 * setSendDocId
	 * Sets value of sendDocId variable
	 *
	 * @param string $sendDocId
	 */
	public function setSendDocId($sendDocId) {
		$this->sendDocId = $sendDocId;
	}

	/**
	 * setCorrectorId
	 * Sets value of correctorId variable
	 *
	 * @param string $correctorId
	 */
	public function setCorrectorId($correctorId) {
		$this->correctorId = $correctorId;
	}

	/**
	 * setState
	 * Sets value of state variable
	 *
	 * @param tinyint $state
	 */
	public function setState($state) {
		$this->state = $state;
	}

	/**
	 * setLevel
	 * Sets value of level variable
	 *
	 * @param tinyint $level
	 */
	public function setLevel($level) {
		$this->level = $level;
	}

	/**
	 * setOnline
	 * Sets value of online variable
	 *
	 * @param tinyint $online
	 */
	public function setOnline($online) {
		$this->online = $online;
	}
	
	/**
	 * setToken
	 * Sets value of token variable
	 *
	 * @param tinyint $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}
	
	/**
	 * setWebsite
	 * Sets value of website variable
	 *
	 * @param tinyint $website
	 */
	public function setWebsite($website) 
	{
		$this->website = $website;
	}	
	
	//get methods
	
	public function getRandom()  // it will set random number
	{
		return $this->random;
	}
	
	/**
	 * getSubject
	 * This method will be used to obtain "subject" value
	 *
	 * @return char
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * getCategory
	 * This method will be used to obtain "category" value
	 *
	 * @return char
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * getType
	 * this method will be used to obtain "type" value
	 *
	 * @return char
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * getName
	 * this method will be used to obtain "name" value
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * getLanguage
	 * this method will be used to obtain "language" value
	 *
	 * @return char
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * getNbConsult
	 * this method will be used to obtain "nbConsult" value
	 *
	 * @return integer
	 */
	public function getNbConsult() {
		return $this->nbConsult;
	}

	/**
	 * getCustomerId
	 * this method will be used to obtain "customerId" value
	 *
	 * @return string
	 */
	public function getCustomerId() {
		return $this->customerId;
	}
	
	/**
	 * getDate
	 * this method will be used to obtain "date" value
	 *
	 * @return string	
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * getTitle
	 * this method will be used to obtain "title" value
	 *
	 * @return tinytext
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * getDescription
	 * this method will be used to obtain "description" value
	 *
	 * @return text
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * getUrl
	 * this method will be used to obtain "url" value
	 *
	 * @return String
	 */
	public function getUrl() {
		return $this->url;
	}
	/**
	 * getUrl
	 * this method will be used to obtain "ssd" value
	 *
	 * @return tinytext
	 */
	public function getSsd() {
		return $this->ssd;
	}
	
	/**
	 * getExtension
	 * this method will be used to obtain "extension" value
	 *
	 * @return tinyint
	 */
	public function getExtension() {
		return $this->extension;
	}
	
	/**
	 * getNbPage
	 * this method will be used to obtain "nbPage" value
	 *
	 * @return smaillint
	 */
	public function getNbPage() {
		return $this->nbPage;
	}
	
	/**
	 * getPrice
	 * this method will be used to obtain $price value
	 *
	 * @return string
	 */
	public function getPrice() {
		$price = new String ( );
		return $price->addZero ( $this->price );
	}

	/**
	 * getPrice2
	 * this method will be used to obtain $price2 value
	 *
	 * @return string
	 */
	public function getPrice2() {
		$price = new String ( );
		return $price->addZero ( $this->price2 );
	}

	/**
	 * getRating
	 * this method will be used to obtain "rating" value
	 *
	 * @return -------------------------------
	 */
	public function getRating() {
		return $this->rating;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function getScaleId() {
		return $this->scaleId;
	}

	/**
	 * getSendingId
	 * this method will be used to obtain "sendingId" value
	 *
	 * @return string
	 */
	public function getSendingId() {
		return $this->sendingId;
	}
	
	/**
	 * getSendDocId
	 * this method will be used to obtain "sendDocId" value
	 *
	 * @return string
	 */
	public function getSendDocId() {
		return $this->sendDocId;
	}
	
	/**
	 * getCorrectorId
	 * this method will be used to obtain "correctorId" value
	 *
	 * @return string
	 */
	public function getCorrectorId() {
		return $this->correctorId;
	}

	/**
	 * getState
	 * this method will be used to obtain "state" value
	 *
	 * @return tinyint
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * getLevel
	 * this method will be used to obtain "level" value
	 *
	 * @return tinyint
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 * getOnline
	 * this method will be used to obtain "online" value
	 *
	 * @return tinyint
	 */
	public function getOnline() {
		return $this->online;
	}
	
/**
	 * getToken
	 * this method will be used to obtain "token" value
	 *
	 * @return tinyint
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * getWebsite
	 * this method will be used to obtain "website" value
	 *
	 * @return website
	 */
	public function getWebsite() {
		return $this->website;
	}
		
	/**
	 * getNextDoc
	 * This method is used to get results of publish document pro
	 * @return Array $result
	 */
	public function getNextDoc($identifier,$wsite=0)
	{
		$query = "select * FROM DOCUMENT WHERE identifier > ".$identifier ." AND website = ".$wsite." ORDER BY identifier ASC LIMIT 0,1";	
		return $this->getQuery($query,FALSE);
	}
	/**
	 * getPreviousDoc
	 * This method is used to get results of publish document pro
	 * @return Array $result
	 */
	public function getPreviousDoc($identifier,$wsite=0)
	{
		$query = "select * FROM DOCUMENT WHERE identifier < ".$identifier ." AND website = ".$wsite." ORDER BY identifier DESC  LIMIT 0,1";	
		return $this->getQuery($query,FALSE);
	}

	public function loadarrayforupdate()
	{
		return $this->loadIntoArray();
	}

	public function updatedoc($upddata, $where)
	{
		return $this->updateQuery($upddata,$where);
	}

	/** Get document ids starting from $index of $count rows.
	 *
	 * @param  integer $index
	 * @param  integer $count
	 */
	public function getIds($index, $count)
	{
		$query = "SELECT identifier, title, language FROM $this->_name WHERE online = 1 ORDER By identifier ASC LIMIT $index, $count";
		return $this->getQuery($query, FALSE);
	}

	/**
	 * Return document info by searching its title.
	 * @param string $title
	 */
	public function getDocbyTitle($title,$wsite=0)
	{
		$title = new String(stripslashes($title));
		$title->_fuckWord();
		$title->_fuckTheBreaks();
		$title->_replaceAB();
		$title->replace('"',"");
		$title = $title->getArrayKeyword(2);
		$count = count($title);
		$i = 0;
		foreach($title as $val){
			$where.= " title LIKE '%".addslashes($val)."%'";
			if($i < $count-1)
			$where.= " AND";
			$i++;
		}
		$where.=" AND website=".$wsite;
		$query = "SELECT identifier, title FROM DOCUMENT WHERE".$where;
		return $this->getQuery($query,FALSE);
	}
	
	public function getDocbyEmail($email,$wsite=0)
	{
		if (isset($email) && $email!="")
		$query = "SELECT D.identifier, D.title FROM DOCUMENT D,CUSTOMER C WHERE D.customerId = C.identifier AND C.email = '$email' AND D.website= ".$wsite;
		//echo 'Query '.$query
		return $this->getQuery($query,FALSE);
	}
	
	/**
	 * getResourceTitle
	 * get title from Resource file
	 *
	 * @param Integer id
	 * @param String lang
	 * 
	 * @return String $title;
	 */
	public function getResourceTitle($id,$lang)
	{
		$location = RESOURCES_PATH . 'docsTxt/titleTxt/' . $lang . "/" . $id . ".txt";
		if(file_exists($location))
		{
			$title = new String(file_get_contents($location));
			$title->_fuckWord();
			$title->_fuckTheBreaks();
			$title->replace('"',"&quot;");
			$title = $title->getString();
		}

		return $title;
	}

	/**
	 * getResourceUrl
	 * get url from Resource file
	 *
	 * @param Integer id
	 * @param String lang
	 * 
	 * @return String $title;
	 */
	public function getResourceUrl($id,$lang)
	{
		$location = RESOURCES_PATH . 'docsTxt/urlTxt/' . $lang . "/" . $id . ".txt";
		if(file_exists($location))
		{
			$title = new String(file_get_contents($location));
			$title->_fuckWord();
			$title->_fuckTheBreaks();
			$title->replace('"',"&quot;");
			$title = $title->getString();
		}
		return $title;
	}

	public function getEffectivePriceCountry($column,$rank = NULL)
	{
		if($rank==NULL)
		{
			$balance = new Ep_Controller_Balance(2);
			$rank = $balance->getRank();
		}
		if ($rank == 2)
			$column .= "2";
		$query = "select ".$column." from PRICE where documentId=".$this->getIdentifier();  // $this->getIdentifier () gives the documentId ofthe document.
		$res = $this->getQuery($query,TRUE);		
		$res1 = $res[0][$column];
		return $res1;
	}
	
	// check this function's usefullness
	public function getEffectivePriceCountryCart($documentId)
	{
		if($rank==NULL)
		{
			$balance = new Ep_Controller_Balance(2);
			$rank = $balance->getRank();
		}
		$column = "";
		if ($rank == 2)$column = "2";
		$query = "select ".$this->checkIp().$column." from PRICE where documentId=".$documentId.";"; //$this->colname // $this->getIdentifier () gives the documentId ofthe document.
		$res = $this->getQuery($query,TRUE);		
		$res1 = $res[0][$column_name];
		return $res1;
	}
	
	/**
	 * This method will return load balanced price.
	 */
	public function getEffectivePrice($rank=NULL)
	{
		if($rank==NULL)
		{
			$balance = new Ep_Controller_Balance(2);
			$rank = $balance->getRank();
		}
		if ($rank == 1)return $this->getPrice();
		if ($rank == 2)return $this->getPrice2();
	}

	/**
	 * Get the document info by search for its ssd url part.
	 * @param string $ssd
	 */
	public function selectBySsd($ssd){
		$ssd = str_replace(" ", "+", $ssd);
		$query = "SELECT * FROM $this->_name WHERE ssd='".addslashes($ssd)."'";
		$res = $this->getQuery($query, true);
		$this->loadData($res[0]);
	}
	
	// set doc online  - shiva
	public function online()
	{
		$this->setOnline(1);
		$arr = $this->getAllDetails();
		$whr = " identifier='".$this->getIdentifier()."'"; 
		$this->updatedoc($arr, $whr);
	}

	// set doc offline  - shiva
	public function notOnline()
	{
		$this->setOnline(0);
		$arr = $this->getAllDetails();
		$whr = " identifier='".$this->getIdentifier()."'"; 
		$this->updatedoc($arr, $whr);
	}
	
	/**
	 * returns last identifier  - By Shiva
	 *
	 * 
	 * @return sets array
	 */
	public function lastidentifier()
	{
		$query = "SELECT identifier FROM DOCUMENT ORDER BY identifier DESC LIMIT 0,1";
		$result = $this->getQuery($query,true);
		foreach ($result as $resul)
		$this->loadData($resul);
	}
	
	public function isNotValid()
	{
		$title = new String($this->getTitle());
		$description = new String($this->getDescription());
		$customerId = new String($this->getCustomerId());

		$return = "";

		if($title->isNull()) $return .= "DOC1!";
		if($description->isNull()) $return .= "DOC2!";
		if(!$customerId->isAlnum()||$customerId->isNull()) $return = "ERROR";

		return $return;
	}
	
	/**
	 * insertplus
	 * 
	 * Inserts the data into the DOCUMENT table
	 *
	 * @param array $dataplus
	 * @return true/false
	 */
	public function insertplus($dataplus=NULL)
	{
		if($dataplus==NULL)
		$dataplus=$this->loadIntoArray();
		if($this->insertQuery($dataplus))
			return true;
		else
			return false;
	}
	
	public function loadarrayforpartner()
	{
		return $this->partnerupdate();
	}
	
	public function partnerupdate() 
	{
		$array = array ( );
		$array ["subject"] = $this->getSubject ();
		$array ["category"] = $this->getCategory ();
		$array ["type"] = $this->getType ();
		$array ["language"] = $this->getLanguage ();
		$array ["nbConsult"] = $this->getNbConsult ();
		$array ["customerId"] = $this->getCustomerId ();
		$array ["name"] = $this->getName ();
		$array ["date"] = $this->getDate ();
		$array ["title"] = $this->getTitle ();
		$array ["description"] = $this->getDescription ();
		$array ["url"] = $this->getUrl ();
		$array ["ssd"] = $this->getSsd ();
		$array ["extension"] = $this->getExtension ();
		$array ["nbPage"] = $this->getNbPage ();
		$array ["sendingId"] = $this->getSendingId ();
		$array ["sendDocId"] = $this->getSendDocId ();
		$array ["correctorId"] = $this->getCorrectorId ();
		$array ["state"] = $this->getState ();
		$array ["level"] = $this->getLevel ();
		$array ["online"] = $this->getOnline ();
		$array ["token"] = $this->getToken ();
		return $array;
	}
	
	public function getDocByState($state, $sort,$start = NULL,$end = NULL,$extS = NULL,$langS = NULL,$pageSort = NULL,$sortByIdent = 'ASC')
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT * FROM ".$this->_name." WHERE state = $state ";
		if(!is_null($extS) && !empty($extS))
		{
			$id_cond = "'".implode("', '", $extS)."'";
			if($id_cond!="''")
			$query .=" AND extension IN ($id_cond) ";
		}
		if(!is_null($langS) && !empty($langS))
		{
			$id_cond = "'".implode("', '", $langS)."'";
			if($id_cond!="''")
			$query .=" AND language IN ($id_cond) ";
		}
		if($this->group == 1)
		$query .= "AND ($this->_name.random BETWEEN $this->indian OR $this->_name.random=0) ";
		if($this->group == 2)
		$query .= "AND ($this->_name.random BETWEEN $this->argentina OR $this->_name.random=0) ";
		$query .= " ORDER BY ";
		if(!is_null($pageSort) & $pageSort != "")
        {
           $query .= " `nbPage` $pageSort "; 
        }        
        elseif(isset($sort))
		$query .= " `lastupdate` $sort ";
		else
		$query .= " `identifier` $sortByIdent ";
		if(isset($start))
		$query .= " LIMIT $start,$end ";
		return $this->getQuery($query,false);
	}
	
	public function getDocByStateNbRecords($state, $sort,$extS = NULL,$langS = NULL,$sortByIdent = 'ASC')
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT * FROM ".$this->_name." WHERE state = $state ";
		if(!is_null($extS) && !empty($extS))
		{
			$id_cond = "'".implode("', '", $extS)."'";
			if($id_cond!="''")
			$query .=" AND extension IN ($id_cond) ";
		}
		if(!is_null($langS) && !empty($langS))
		{
			$id_cond = "'".implode("', '", $langS)."'";
			if($id_cond!="''")
			$query .=" AND language IN ($id_cond) ";
		}
		if($this->group == 1)
		$query .= "AND ($this->_name.random BETWEEN $this->indian OR $this->_name.random=0) ";
		if($this->group == 2)
		$query .= "AND ($this->_name.random BETWEEN $this->argentina OR $this->_name.random=0) ";
		$query .= " ORDER BY ";
		if(isset($sort))
		$query .= " `lastupdate` $sort ";
		else
		$query .= " `identifier` $sortByIdent ";
		if(isset($start))
		$query .= " LIMIT $start,$end ";
		return $this->getNbRows($query);
	}
	
	public function getDocByStateasArray($state, $sort = 'DESC',$start = NULL,$end = NULL,$pageSort = NULL,$sortByIdent = 'ASC')
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT * FROM ".$this->_name." WHERE state = $state ";
        if($_COOKIE['_country'] == 'fr')
        {
            $query .= "AND language NOT IN('en','us') ";
        } 
		if($this->group == 1)
		$query .= "AND ($this->_name.random BETWEEN $this->indian OR $this->_name.random=0) ";
		if($this->group == 2)
		$query .= "AND ($this->_name.random BETWEEN $this->argentina OR $this->_name.random=0) ";
		$query .= " ORDER BY ";
		if(!is_null($pageSort) & $pageSort != "")
        {
           $query .= " `nbPage` $pageSort "; 
        }        
        elseif(isset($sort))
		$query .= " `lastupdate` $sort ";
		else
		$query .= " `identifier` $sortByIdent ";
		if(isset($start))
		$query .= " LIMIT $start,$end ";
		return $this->getQuery($query,true);
	}
		
	public function getDocByStateasArrayAlt($listOfLocDoc ,$state ,$sort = 'DESC',$start = NULL,$end = NULL,$sortByIdent = 'ASC')
	{
		$id_cond = "'".implode("', '", $listOfLocDoc)."'";
		$query = "SELECT * FROM ".$this->_name." WHERE state = $state AND identifier IN ($id_cond) ORDER BY ";
		if(isset($sort))
		$query .= " `lastupdate` $sort ";
		else
		$query .= " `identifier` $sortByIdent ";
		return $this->getQuery($query,true);
	}
	
	public function getDocByStateAlt($listOfLocDoc ,$state, $sort,$start = NULL,$end = NULL,$sortByIdent = 'ASC')
	{
		$id_cond = "'".implode("', '", $listOfLocDoc)."'";
		$query = "SELECT * FROM ".$this->_name." WHERE state = $state AND identifier IN ($id_cond) ORDER BY";
		if(isset($sort))
		$query .= " `lastupdate` $sort ";
		else
		$query .= " `identifier` $sortByIdent ";
		return $this->getQuery($query,false);
	}
		
	public function getDocByDocumentRefuse($state,$refusestatus = 0, $sort = 'DESC',$start = NULL,$end = NULL,$corS = NULL)
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,MR.status as mr_status FROM ".$this->_name." D, MESSAGEREFUSE MR WHERE D.identifier = MR.identifier AND ";
		if($refusestatus == 0)
		$query .= " MR.status = 0 ";
		else
		$query .= " MR.status != 0 ";
		if(!is_null($corS) && !empty($corS))
        {
            $id_cond = "'".implode("', '", $corS)."'";
            if($id_cond!="''")
            $query .=" AND D.correctorId IN ($id_cond) ";
        }
		if($this->group == 1)
		$query .= "AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= "AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		if(isset($sort) && $sort!="")
        $query .= " ORDER BY D.lastupdate $sort ";
        else
        $query .= " ORDER BY D.lastupdate DESC ";
		if(isset($start))
		$query .= " LIMIT $start,$end ";
		return $this->getQuery($query,true);
	}
	
	public function getDocByStateasArraySandrine($state,$group = NULL)
	{
		$query = "SELECT D.*,C.correctorId,C.comment,C.status FROM DOCUMENT D, MESSAGECORRECTOR C, CORRECTOR CC WHERE D.identifier = C.documentId AND CC.identifier = C.correctorId AND C.status = 1 AND D.state = $state ";
		if(!is_null($group) && $group!="" && $group!=0)
        $query .=" AND CC.group = '$group' ";
        $query .= " GROUP BY D.identifier ORDER BY C.date DESC ";
        //echo $query;
        return $this->getQuery($query,true);
	}
	
	public function getDocByStateasArraySandrineAll($group = NULL)
	{
		$query = "SELECT DISTINCT(C.documentId),D.*,C.correctorId,C.comment,C.status FROM DOCUMENT D, MESSAGECORRECTOR C, CORRECTOR CC WHERE D.identifier = C.documentId AND CC.identifier = C.correctorId AND C.status = 1 ";
		if(!is_null($group) && $group!="" && $group!=0)
        $query .=" AND CC.group = '$group' ";
        $query .= " GROUP BY C.documentId ORDER BY C.date DESC";
        //echo $query;
        return $this->getQuery($query,true);
	}
	
	public function getDetailsDocByStatesfr($state,$start=0,$end=10,$extS = NULL,$cateS = NULL,$corS = NULL,$page = NULL,$dateSd =0)
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating,P.scaleId , S.cessionType FROM DOCUMENT D,PRICE P,EVALUATION E ,CUSTOMER C, SENDING S
				  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId AND D.identifier = P.documentId AND E.identifier = D.identifier  AND D.online = 0 ";
        if($_COOKIE['_country'] == 'fr')
        {
            $query .= " AND D.language NOT IN('en','us') ";
        }
        $query .= "AND ("; 
		$OR = "";
		foreach($state as $s)
		{
			$query .= " $OR D.state = $s";
			$OR = "OR";
		}
		$query .=") ";
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		if(!is_null($extS) && !empty($extS))
		{
			$id_cond = "'".implode("', '", $extS)."'";
			if($id_cond!="''")
			$query .=" AND D.extension IN ($id_cond) ";
		}		
		if(!is_null($cateS) && !empty($cateS))
		{
			$id_cond = "'".implode("', '", $cateS)."'";
			if($id_cond!="''")
			$query .=" AND D.subject IN ($id_cond) ";
		}		
		if(!is_null($corS) && !empty($corS))
		{
			$id_cond = "'".implode("', '", $corS)."'";
			if($id_cond!="''")
			$query .=" AND D.correctorId IN ($id_cond) ";
		}		
		if(!is_null($page) && $page!="")
		$query .=" AND D.nbPage BETWEEN $page ";		
		$query .= " ORDER BY ";
		if($dateSd ==0)
		$query .= " D.lastupdate ASC LIMIT $start,$end";
		else
		$query .= " D.lastupdate DESC LIMIT $start,$end";
		///echo $query;
		return $this->getQuery($query,false);
	}
	
	public function getDetailsDocByStatesen($state,$start=0,$end=10)
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating,P.scaleId ,S.cessionType FROM DOCUMENT D,PRICE P,EVALUATION E ,CUSTOMER C, SENDING S, SENDDOC SD
				  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId AND D.identifier = P.documentId AND E.identifier = D.identifier AND SD.identifier = D.sendDocId AND SD.statut != 1 AND SD.statut != 3 AND SD.statut != 13 AND D.online = 0 AND (";

		$OR = "";
		foreach($state as $s)
		{
			$query .= " $OR D.state = $s";
			$OR = "OR";
		}
		$query .=") ";
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		$query .= " ORDER BY ";
		$query .= " D.date ASC LIMIT $start,$end";
		return $this->getQuery($query,false);
	}
		
	public function getDetailsDocByStatesfrArray($state,$start=0,$end=10,$group = NULL)
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating,P.scaleId FROM DOCUMENT D,PRICE P,EVALUATION E ,CUSTOMER C, SENDING S";
        if(!is_null($group) && $group!="" && $group!=0)        
        $query .= ",CORRECTOR CC WHERE CC.identifier = C.identifier AND ";
        else
        $query .= " WHERE ";
        $query .= " S.identifier = D.sendingId AND C.identifier = D.customerId AND D.identifier = P.documentId AND E.identifier = D.identifier  AND D.online = 0 AND (";        
		$OR = "";
		foreach($state as $s)
		{
			$query .= " $OR D.state = $s";
			$OR = "OR";
		}
		$query .=") ";
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
        if(!is_null($group) && $group!="" && $group!=0)
        $query .=" AND CC.group = '$group' ";
		$query .= " ORDER BY ";
		$query .= " D.date ASC LIMIT $start,$end";
		return $this->getQuery($query,true);
	}
	
	public function getDetailsDocByStatesenArray($state,$start=0,$end=10)
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating,P.scaleId FROM DOCUMENT D,PRICE P,EVALUATION E ,CUSTOMER C, SENDING S, SENDDOC SD
				  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId AND D.identifier = P.documentId AND E.identifier = D.identifier AND SD.identifier = D.sendDocId AND SD.statut != 1 AND SD.statut != 3 AND SD.statut != 13 AND D.online = 0 AND (";

		$OR = "";
		foreach($state as $s)
		{
			$query .= " $OR D.state = $s";
			$OR = "OR";
		}
		$query .=") ";
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		$query .= " ORDER BY ";
		$query .= " D.date ASC LIMIT $start,$end";
		return $this->getQuery($query,true);
	}
		
	public function docPhase10()
	{
		$this->setState(10);
		$this->update();
	}
	
    public function docPhaseState($docid,$state)
	{
        $Date = new Date("",$_COOKIE["_country"]);
		$data= Array('state'=>$state,'lastupdate'=>$Date->getDate());
			$where = " identifier = $docid ";
		$this->updateQuery($data,$where);
		return true;
	}

	public function getNumberDocByStatesfr($state,$extS = NULL,$cateS = NULL,$corS = NULL,$page = NULL)
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating,P.scaleId , S.cessionType FROM DOCUMENT D,PRICE P,EVALUATION E ,CUSTOMER C, SENDING S
                  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId AND D.identifier = P.documentId AND E.identifier = D.identifier  AND D.online = 0 ";
		if($_COOKIE['_country'] == 'fr')
        {
            $query .= " AND D.language NOT IN('en','us') ";
        }
        $query .= "AND (";        
        $OR = "";
		foreach($state as $s)
		{
			$query .= " $OR D.state = $s";
			$OR = "OR";
		}
		$query .=") ";
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		
		if(!is_null($extS) && !empty($extS))
		{
			$id_cond = "'".implode("', '", $extS)."'";
			if($id_cond!="''")
			$query .=" AND D.extension IN ($id_cond) ";
		}		
		if(!is_null($cateS) && !empty($cateS))
		{
			$id_cond = "'".implode("', '", $cateS)."'";
			if($id_cond!="''")
			$query .=" AND D.subject IN ($id_cond) ";
		}		
		if(!is_null($corS) && !empty($corS))
		{
			$id_cond = "'".implode("', '", $corS)."'";
			if($id_cond!="''")
			$query .=" AND D.correctorId IN ($id_cond) ";
		}		
		if(!is_null($page) && $page!="")
		$query .=" AND D.nbPage BETWEEN $page ";

		$query .= " ORDER BY ";
		$query .= " S.date ASC";
		return $this->getNbRows($query);
	}
	
	public function getNumberDocByStatesen($state)
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,S.date as price,C.email as rating FROM ".$this->_name." D, SENDING S,CUSTOMER C, SENDDOC SD
				  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId AND SD.identifier = D.sendDocId AND SD.statut != 1 AND SD.statut != 3 AND SD.statut != 13 AND D.online = 0 AND (";
		$OR = "";
		foreach($state as $s)
		{
			$query .= " $OR D.state = $s";
			$OR = "OR";
		}
		$query .=") ";
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		$query .= " ORDER BY ";
		$query .= " S.date ASC";
		return $this->getNbRows($query);
	}

	public function getdocIdentifier()
	{
		return $this->getIdentifier();
	}
		
	// this is a fn to update the PRICE table.
	public function getalldocs()
	{
		$query = "select identifier, nbPage from DOCUMENT where online=1;";
		$res = $this->getQuery($query, true);
		return $res;
	}
	
	public function getDocsByCat($category,$nbpage,$flag=0,$stLimit=NULL,$endLimit=NULL)
	{		
		if($flag == 0)
		{
			$query = "select identifier, nbPage from DOCUMENT where";
			if($category != '')
				$query .= " category='".$category."' and nbPage=".$nbpage;
			elseif($category == '')
				$query .= " nbPage=".$nbpage."";
		}
		elseif($flag ==1)
		{
			$query = "select identifier, nbPage from DOCUMENT where";
			if($category != '')
				$query .= " category='".$category."' and nbPage>=".$nbpage;
			elseif($category == '')
				$query .= " nbPage>=".$nbpage;
		}
		
		if(isset($stLimit) && isset($endLimit))
			$query .=" limit ".$stLimit.",".$endLimit;
		
		//$query .= " and online=1";
		$res = $this->getQuery($query, true);
		return $res;
	}

	public function selectByIdasObj($id)
 	{
  		$query = "SELECT * FROM ".$this->_name." where identifier = '$id'";
		return $this->getQuery($query,false);
  	}
  	
  	public function fewUpdate($docid,$on)
  	{
  		$data= Array('online'=>$on);
		$where = "identifier = $docid";
		$this->updateQuery($data,$where);
			return true;
  	}
  	
	public function getDocByStates($state, $sort,$start = NULL,$end = NULL,$extS = NULL,$langS = NULL,$sortByIdent = 'ASC')
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,S.date as price,C.email as rating FROM ".$this->_name." D, SENDING S,CUSTOMER C
		  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId ";
		if(!is_null($extS) && !empty($extS))
		{
			$id_cond = "'".implode("', '", $extS)."'";
			if($id_cond!="''")
			$query .=" AND D.extension IN ($id_cond) ";
		}
		if(!is_null($langS) && !empty($langS))
		{
			$id_cond = "'".implode("', '", $langS)."'";
			if($id_cond!="''")
			$query .=" AND D.language IN ($id_cond) ";
		}
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		$query .= " AND (";
		$OR = "";
		foreach($state as $s)
		{
		$query .= " $OR D.state = $s";
		$OR = "OR";
		}
		if(isset($sort) && $sort!='')
		$query .=") ORDER BY D.lastupdate $sort";
		else
		$query .= ") ORDER BY D.identifier $sortByIdent ";
		if(isset($start))
		$query .= " LIMIT $start,$end ";
		return $this->getQuery($query,true);
	}
	
	public function getDocProblem($sort,$start = NULL,$end = NULL,$pageSort = NULL,$sortByIdent = 'ASC')
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT * FROM ".$this->_name." WHERE state = 3 ";
		if($this->group == 1)
		$query .= "AND ($this->_name.random BETWEEN $this->indian OR $this->_name.random=0) ";
		if($this->group == 2)
		$query .= "AND ($this->_name.random BETWEEN $this->argentina OR $this->_name.random=0) ";
		$query .= " ORDER BY ";
		if(!is_null($pageSort) & $pageSort != "")
        {
           $query .= " `nbPage` $pageSort "; 
        }        
        elseif(isset($sort))
		$query .= " `lastupdate` $sort ";
		else
		$query .= " `identifier` $sortByIdent ";
		if(isset($start))
		$query .= " LIMIT $start,$end ";
		return $this->getQuery($query,true);
	}

	public function getDocProblemSandrine($group = NULL)
	{
		$query = "SELECT D.*,C.correctorId,C.comment,C.status FROM DOCUMENT D, MESSAGECORRECTOR C, CORRECTOR CC WHERE D.identifier = C.documentId AND CC.identifier = C.correctorId AND C.status = 1 AND D.state = 3 ";
		if(!is_null($group) && $group!="" && $group!=0)
        $query .=" AND CC.group = '$group' ";
        $query .= " GROUP BY D.identifier ORDER BY C.date DESC ";
        return $this->getQuery($query,true);
	}
	
	public function getPrevNavId($id){
		$query = "SELECT identifier FROM $this->_name WHERE identifier < $id AND online=1 ORDER BY identifier DESC LIMIT 0, 1";
		$data = $this->getQuery($query, true);
		if ($data){
			return $data[0]['identifier'];
		}
		return $id;
	}
	public function getNextNavId($id){
		$query = "SELECT identifier FROM $this->_name WHERE identifier > $id AND online=1 ORDER BY identifier ASC LIMIT 0, 1";
		$data = $this->getQuery($query, true);
		if ($data){
			return $data[0]['identifier'];
		}
		return NULL;
	}

	public function docstocrss($begin=0,$end=10)
	{
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2"; 		
		$query.= " FROM DOCUMENT D,PRICE P ";
		$query .="
		WHERE 
		D.identifier = P.documentId 
		AND D.state =8";
		$query .= " LIMIT $begin, $end";
		return $this->getQuery($query,false);
	}
	public function selectDocsByCustomerId($customerId)
	{
		$query = "SELECT doc.*, send.cessionType FROM " . $this->_name . " AS doc, SENDING as send WHERE doc.customerId = '$customerId' AND 
				  doc.customerId=send.customerId AND doc.sendingId=send.identifier AND doc.online = 1 ";
		return $this->getQuery ( $query, true );
	}

	public function getPrices($doc_ids)
	{
		$balance = new Ep_Controller_Balance(2);
		$rank = $balance->getRank();
		$doc_ids_str = " ('".implode("','",$doc_ids)."')";
		$query = "SELECT * FROM PRICE WHERE documentId IN $doc_ids_str ";
		$results = $this->getQuery($query, true);
		
		$country = Zend_Registry::get('_country');
		if ($country == "en" || $country == "fr"){
			$column = $this->checkIp(); //$this->colname
			if ($rank == 2)
				$column .= 2;
			
		}
		$prices = array();
		foreach($results as $result)
		{
			//var_dump($result);
			if ($country == "en" || $country == "fr")
			{
				$prices[$result['documentId']] = $result[$column];
				/*
				if ($rank == 2)
				{
					$this->setIdentifier($result->documentId);
					$col = $this->getEffectivePriceCountry($column,2);	
					$prices[$result->documentId] = $col;	//$result->customerPrice2;	
				}
				else
				{
					$this->setIdentifier($result->documentId);
					$col = $this->getEffectivePriceCountry($column,1);	
					$prices[$result->documentId] = $col;	//$result->customerPrice;	
				}
				*/
			}
			else
			{
				if ($rank == 2)
					$prices[$result['documentId']] = $result['customerPrice2'];	
				else
					$prices[$result['documentId']] = $result['customerPrice'];
			}
		}
		return $prices;
	}

	public function relationdocs($docid = NULL)
	{
		$query = "SELECT DocumentIdR, title FROM RELATION, DOCUMENT WHERE documentId = '".$docid."'AND DocumentIdR = identifier ORDER BY nbRelation DESC LIMIT 0,5";
		return $this->getQuery($query,true);
	}
	public function getTopDocuments($count = 6){
		$query = "SELECT * FROM DOCUMENT WHERE identifier IN (SELECT identifier FROM DOCUMENTTOP ORDER BY nbConsult DESC) LIMIT 0, $count";
		return $this->getQuery($query, true);
	}

	//for cache category calculation
	public function searchBySubject($subject){
		$query = "SELECT * FROM DOCUMENT WHERE subject='".$subject."' AND online =1";
		return $this->getQuery($query);
	}
	
	//for cache category calculation
	public function searchByCategory2($category){
		$query = "SELECT * FROM DOCUMENT WHERE category='".$category."' AND online =1";
		return $this->getQuery($query);
	}
	
	public function getAllOnline()
	{
		$query = "SELECT * FROM DOCUMENT WHERE online =1";
		return $this->getQuery($query, true);
	}
	public function getDocbyIdOrTitle($id=null,$title=null,$wsite= 0)
	{
		$query = "SELECT identifier, title FROM ".$this->_name."  WHERE";
		if (!empty($id))
		{
			$query .= " identifier=".$id;
			if (!empty($title))
				$query .= " OR";
		}
		if (!empty($title))
				$query .= " title = '".$title."'";
		 
		$query .= " AND website = '".$wsite."'";
		$query .= ' LIMIT 0,1';
		$doc = $this->getQuery($query,true);
		if(!empty($doc))
			return $doc[0]['identifier'];
	}
	public function selectAllDocs($offset=0,$limit)
	{
		$query="SELECT identifier, title, description from ".$this->_name." WHERE online=1 AND subject in ('MI','MT','MP','MMS','MMO','MD','MV','MTM','MMI','MM','MS','ML','MC','MB','MSP','IMM')";
		if(!empty($limit))
			$query .=" LIMIT ".$offset*$limit.",".$limit;
		$docs=$this->getQuery($query,false);
		if(!empty($docs))
			return $docs;
	}

	public function getTopCategories(){
		$query="SELECT D.category, SUM(DT.nbConsult) AS nc FROM DOCUMENT D, DOCUMENTTOP DT WHERE D.identifier = DT.identifier GROUP BY D.category ORDER BY nc DESC;";
		$docs=$this->getQuery($query,true);
		if(!empty($docs))
			return $docs;
	
	}
	public function getDocumentIndex($id, $subject = NULL, $online = 1){
		$query = "SELECT count(identifier) AS cnt FROM DOCUMENT WHERE ";
		if ($subject)
			$query .= " DOCUMENT.subject = '$subject' AND ";
		$query .=" identifier >= $id ";
        if($online)
            $query .=" AND online=1 ";
        $query .= " ORDER by identifier DESC";
		$docs = $this->getQuery($query, true);
		//echo $docs[0]['cnt'];
		return $docs[0]['cnt'];
	}
	public function getTopDocumentsCount($subject, $excludeId){
		$query = "SELECT COUNT(DOCUMENT.identifier) AS cnt FROM DOCUMENT,DOCUMENTTOP WHERE DOCUMENT.identifier != $excludeId AND DOCUMENT.subject = '$subject' AND DOCUMENT.identifier = DOCUMENTTOP.identifier AND DOCUMENT.online = 1 ORDER BY DOCUMENTTOP.nbConsult DESC";
		$res = $this->getQuery($query, true);
		return $res[0]['cnt'];
	}
	public function getTopDocumentsPart($subject, $start, $limit, $excludeId){
		$cnt = $this->getTopDocumentsCount($subject, $excludeId);
		if ($cnt >= 5)
			$cnt -= $cnt%5;
		//var_dump($cnt." => ".$start);
        $cnt = floor($cnt/5);
		if ($cnt == 0)
			$start = 0;
		elseif($start) 
			$start = ($start % $cnt)*5;
		$query = "SELECT * FROM DOCUMENT,DOCUMENTTOP WHERE DOCUMENT.subject = '$subject' AND DOCUMENT.identifier != $excludeId AND DOCUMENT.identifier = DOCUMENTTOP.identifier AND DOCUMENT.online = 1 ORDER BY DOCUMENTTOP.nbConsult DESC LIMIT $start,$limit";
        //echo "<br /> top document <br /> $query";
		$docs = $this->getQuery($query, true);
		return $docs;
	}
    public function getFreshDocumentsCount($subject, $excludeId){
        $start_date = date("Ymd000000", strtotime("-1 month"));
		$query = "SELECT COUNT(DOCUMENT.identifier) AS cnt FROM DOCUMENT WHERE DOCUMENT.identifier!=$excludeId AND DOCUMENT.subject = '$subject' AND DOCUMENT.date >= $start_date AND DOCUMENT.online = 1";
        $res = $this->getQuery($query, true);
        return $res[0]['cnt'];
    }
	public function getFreshDocuments($subject, $start, $limit, $excludeId){
        $start_date = date("Ymd000000", strtotime("-1 month"));
        $cnt = $this->getFreshDocumentsCount($subject, $excludeId);
		//var_dump("before cnt $cnt");
		if ($cnt >= 5)
			$cnt -= $cnt%5;
		//var_dump("after cnt $cnt");
        
//        $count = $this->getSlot($cnt)
		$cnt = floor($cnt / 5);
		//var_dump("status $start $cnt");
		if ($cnt == 0)
			$start = 0;
		elseif($start)
			$start = ($start % $cnt)*5;
        $query = "SELECT * FROM DOCUMENT WHERE DOCUMENT.subject = '$subject' AND DOCUMENT.identifier != $excludeId AND DOCUMENT.date >= $start_date AND DOCUMENT.online = 1 ORDER BY identifier DESC LIMIT $start,$limit";
        //echo "<br /> fresh document <br /> $query";
        $docs = $this->getQuery($query, true);
		return $docs;
	}
	//no restriction online field
	public function getDocumentIndex2x($id, $subject = NULL){
		$query = "SELECT count(identifier) AS cnt FROM DOCUMENT WHERE ";
		if ($subject)
			$query .= " DOCUMENT.subject = '$subject' AND ";
		$query .=" identifier >= $id ORDER by identifier DESC";
		$docs = $this->getQuery($query, true);
		return $docs[0]['cnt'];
	}
    private function getSlot($index){
        $slot = intval($index/5);
        if($index%5 != 0)
            $slot += 1;
        return $slot;
    }
    public function getDocsByVoucherExchange($cid){
    	 $query="SELECT D.title, TD.price, TR.date, TR.time FROM DOCUMENT D, TRDOCUMENT TD, TRANSACTION TR, VEXTRDOC VXT WHERE VXT.trdocumentId=TD.identifier AND TD.transactionId=TR.identifier AND D.identifier = TD.documentId AND TR.customerId='".$cid."' ORDER BY TR.date, TR.time DESC";
   	 $xdocs = $this->getQuery($query, true);
   	 return $xdocs;
    }
    
	public function getDocProblemAlt($listOfLocDoc,$sort,$start = NULL,$end = NULL,$sortByIdent = 'ASC')
	{
		$id_cond = "'".implode("', '", $listOfLocDoc)."'";
		$query = "SELECT * FROM ".$this->_name." WHERE state = 3  AND identifier IN ($id_cond) ORDER BY ";
		if(isset($sort))
		$query .= " `lastupdate` $sort ";
		else
		$query .= " `identifier` $sortByIdent ";
		return $this->getQuery($query,true);
	}
	
	public function getDocByStatesAlt($listOfLocDoc,$state, $sort,$start = NULL,$end = NULL,$sortByIdent = 'ASC')
	{
		$id_cond = "'".implode("', '", $listOfLocDoc)."'";
		$query = "SELECT D.*,S.date as price,C.email as rating FROM ".$this->_name." D, SENDING S,CUSTOMER C
		  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId AND D.identifier IN ($id_cond)  AND (";
		$OR = "";
		foreach($state as $s)
		{
		$query .= " $OR D.state = $s";
		$OR = "OR";
		}
		if(isset($sort) && $sort!='')
		$query .=") ORDER BY D.lastupdate $sort";
		else
		$query .= ") ORDER BY D.identifier $sortByIdent ";
		return $this->getQuery($query,true);
	}
	
	public function getDocByStatesNbRecords($state, $sort,$extS = NULL,$langS = NULL,$sortByIdent = 'ASC')
	{
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,S.date as price,C.email as rating FROM ".$this->_name." D, SENDING S,CUSTOMER C
		  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId ";
		
		if(!is_null($extS) && !empty($extS))
		{
			$id_cond = "'".implode("', '", $extS)."'";
			if($id_cond!="''")
			$query .=" AND D.extension IN ($id_cond) ";
		}
		if(!is_null($langS) && !empty($langS))
		{
			$id_cond = "'".implode("', '", $langS)."'";
			if($id_cond!="''")
			$query .=" AND D.language IN ($id_cond) ";
		}
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		$query .= " AND (";
		$OR = "";
		foreach($state as $s)
		{
		$query .= " $OR D.state = $s";
		$OR = "OR";
		}
		if(isset($sort) && $sort!='')
		$query .=") ORDER BY D.lastupdate $sort";
		else
		$query .= ") ORDER BY D.identifier $sortByIdent ";
		return $this->getNbRows($query,true);
	}

  	//function to update the utf8 char with ascii ex &#339; œ
	public function updatedocissue($a,$id)
	{
		$where = " identifier = $id ";
		if (($result = $this->updateQuery($a, $where)) != NULL)
			return true; // return $result
		else
			return false;
	}
	
	//function to replace the utf8 char with ascii ex &#339; œ
	public function docRemove()
	{
		//$query = "SELECT identifier,title,description FROM DOCUMENT WHERE title LIKE '%&#%' ";// OR description LIKE '%&#%' ";
		//$query = "SELECT identifier,title,description FROM DOCUMENT WHERE description LIKE '%&#%' ";
		//$query = "SELECT identifier,url FROM DOCUMENT WHERE url LIKE '%html-%.html%' ";
		$query = "SELECT `identifier` , url FROM `DOCUMENT` WHERE url LIKE CONCAT( '%', identifier, '-', identifier, '%' )";
		if (($result = $this->getQuery($query, true)) != NULL)
			return $result;
		else
			return false;
	}
	
	public function getDDMetatitle($title,$docid)
	{
		//$metatitle=mysql_escape_string($metatitle);
		$title = new String(stripslashes($title));
		$title->_fuckWord();
		$title->_fuckTheBreaks();
		$title->_replaceAB();
		$title->replace('"',"");
		$title = $title->getArrayKeyword(2);
		$count = count($title);
		$i = 0;
		foreach($title as $val)
		{
			$where.= " title LIKE '%".addslashes($val)."%'";
			if($i < $count-1)
			$where.= " AND ";
			$i++;
		}
		$query="SELECT identifier FROM ".$this->_name." WHERE $where AND identifier!= $docid ";
		$records=$this->getQuery($query,true);
		if(!empty($records))
			return $records;
		else
			return false;
	}
	
	public function getDetailsDocByStatesenAlt($listOfLocDoc,$state,$start=NULL,$end=NULL)
	{
		$id_cond = "'".implode("', '", $listOfLocDoc)."'";
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating,P.scaleId ,S.cessionType FROM DOCUMENT D,PRICE P,EVALUATION E ,CUSTOMER C, SENDING S, SENDDOC SD
				  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId AND D.identifier = P.documentId AND E.identifier = D.identifier AND SD.identifier = D.sendDocId AND SD.statut != 1 AND SD.statut != 3 AND SD.statut != 13 AND D.online = 0 AND (";

		$OR = "";
		foreach($state as $s)
		{
			$query .= " $OR D.state = $s";
			$OR = "OR";
		}
		$query .=") ";
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		$query .= " AND D.identifier IN ($id_cond) ORDER BY ";
		$query .= " D.date ASC ";
		return $this->getQuery($query,false);
	}
	
	public function getDetailsDocByStatesfrAlt($listOfLocDoc,$state,$start=NULL,$end=NULL)
	{
		$id_cond = "'".implode("', '", $listOfLocDoc)."'";
		$this->group = $_COOKIE['groupid'];
		$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating,P.scaleId , S.cessionType FROM DOCUMENT D,PRICE P,EVALUATION E ,CUSTOMER C, SENDING S
				  WHERE S.identifier = D.sendingId AND C.identifier = D.customerId AND D.identifier = P.documentId AND E.identifier = D.identifier  AND D.online = 0 ";
        if($_COOKIE['_country'] == 'fr')
        {
            $query .= " AND D.language NOT IN('en','us') ";
        }
        $query .= "AND (";
		$OR = "";
		foreach($state as $s)
		{
			$query .= " $OR D.state = $s";
			$OR = "OR";
		}
		$query .=") ";
		if($this->group == 1)
		$query .= " AND (D.random BETWEEN $this->indian OR D.random=0) ";
		if($this->group == 2)
		$query .= " AND (D.random BETWEEN $this->argentina OR D.random=0) ";
		$query .= " AND D.identifier IN ($id_cond) ORDER BY ";
		$query .= " D.date ASC";
		return $this->getQuery($query,false);
	}
	public function updatedocdetails($did, $data){
		$where = "identifier = $did";
		$this->updateQuery($data,$where);
		return true;
		
	}
    public function getEmailsDoc($did)
    {
        $query="SELECT email from CUSTOMER as c, TRDOCUMENT as trd, TRANSACTION as tr where tr.identifier=trd.transactionId and tr.customerId=c.identifier and trd.documentId=".$did;
        $docsdets = $this->getQuery($query, false);
        return $docsdets;
    }
    public function selectByUrl($url)
    {
        $query = "SELECT * from DOCUMENT where url REGEXP '^$url-[0-9]+.html$'";
        echo $query."<br/>";
        $result = $this->getQuery($query, false);
        if(count($result))return 1;
        else return 0;
    }
}
