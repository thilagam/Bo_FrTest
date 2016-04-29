<?php

/**
 * Ep_document_Price
 * 
 * Provides all the methods to fetch data from the specific table "PAYMENT" in DB.
 * This class extends Ep_Db_Identifier class and implements Ep_Db_Identifier
 * 
 * @author Ram
 * @package Payment
 * @version 1.1
 * 
 */

class Ep_Document_Price extends Ep_Db_Identifier
{
	protected $_name = 'PRICE';
	private $documentId;
	private $customerPrice;
	private $customerPrice2;
	private $customerPriceUS;	 //current customerPriceUS
	private $customerPriceUS2;	 //current customerPriceUS
	private $customerPriceEur;	 //current customerPriceEur
	private $customerPriceEur2;	 //current customerPriceEur
	private $customerPriceAud;	 //current customerPriceAud
	private $customerPriceAud2;	 //current customerPriceAud
	private $customerPricePound;	 //current customerPricePound
	private $customerPricePound2;	 //current customerPricePound
	private $customerPriceCad;	 //current customerPrice
	private $customerPriceCad2;	 //current customerPrice
	private $customerPriceInr;	 //current customerPriceInr
	private $customerPriceInr2;	 //current customerPriceInr2
	private $scaleId;
	private $date;
	private $idnt;
	
	public function loadData($array){
		//$this->setBankname($array['bankName']);
		//print_r($array);
		$this->setDocumentId($array['documentId']);
		$this->setCustomerprice($array['customerPrice']);
		$this->setCustomerprice2($array['customerPrice2']);
		if($_COOKIE["_country"] == "en")
    	{
    		$this->setCustomerPriceUS($array['customerPriceUS']);
			$this->setCustomerPriceEur($array['customerPriceEur']);
			$this->setCustomerPriceAud($array['customerPriceAud']);
			$this->setCustomerPricePound($array['customerPricePound']);
			$this->setCustomerPriceCad($array['customerPriceCad']);
			$this->setCustomerPriceInr($array['customerPriceInr']);
			$this->setCustomerPriceUS2($array['customerPriceUS2']);
			$this->setCustomerPriceEur2($array['customerPriceEur2']);
			$this->setCustomerPriceAud2($array['customerPriceAud2']);
			$this->setCustomerPricePound2($array['customerPricePound2']);
			$this->setCustomerPriceCad2($array['customerPriceCad2']);
			$this->setCustomerPriceInr2($array['customerPriceInr2']);
    	}
		$this->setScaleId($array['scaleId']);
		$this->setDate($array['date']);
	}

	public function loadIntoArray(){
		
		$array["documentId"] = utf8_decode($this->getDocumentId());
		$array['identifier'] = $this->getIdentifier();
		$array["customerPrice"] = $this->getCustomerprice();
		$array["customerPrice2"] = $this->getCustomerprice2();
		if($_COOKIE["_country"] == "en")
    	{
    		$array["customerPriceUS"] = $this->getCustomerPriceUS();
			$array["customerPriceEur"] = $this->getCustomerPriceEur();
			$array["customerPriceAud"] = $this->getCustomerPriceAud();
			$array["customerPricePound"] = $this->getCustomerPricePound();
			$array["customerPriceCad"] = $this->getCustomerPriceCad();
			$array["customerPriceInr"] = $this->getCustomerPriceInr();
			$array["customerPriceUS2"] = $this->getCustomerPriceUS2();
			$array["customerPriceEur2"] = $this->getCustomerPriceEur2();
			$array["customerPriceAud2"] = $this->getCustomerPriceAud2();
			$array["customerPricePound2"] = $this->getCustomerPricePound2();
			$array["customerPriceCad2"] = $this->getCustomerPriceCad2();
			$array["customerPriceInr2"] = $this->getCustomerPriceInr2();
    	}		
		$array["scaleId"] = utf8_decode($this->getScaleId());
		$array["date"] = utf8_decode($this->getDate());	
		//print_r($array);
		return $array;
	}
	
	public function __construct($tablename=NULL)
	{
		parent::__construct();
		if($tablename)$this->_setupTableName($tablename);
	}
	
	public function setNewIdentifier()
	{
		$d = new Date();
		$tier = $d->getSubDate(2,14).rand(100,999);
		return $tier;
	}
	
	public function insertData($array)
	{
		try 
		{
			$in = $this->insertQuery($array);
			return $in;
		}
		catch (Exception $e)
		{
			echo "Some problem inserting into the database";
			echo $e;
			exit();
		}
		
	}
	
	/*** Commented By Ram ****/
	/*public function insert()
	{
		$data=$this->loadIntoArray();
		$return = $this->insertQuery($data);
		return $return;
	}*/
	
	public function loadArrayUpdate()
	{
		$array["documentId"] = utf8_decode($this->getDocumentId());
		$array["customerPrice"] = $this->getCustomerprice();
		$array["customerPrice2"] = $this->getCustomerprice2();
		if($_COOKIE["_country"] == "en")
    	{
    		$array["customerPriceUS"] = $this->getCustomerPriceUS();
			$array["customerPriceEur"] = $this->getCustomerPriceEur();
			$array["customerPriceAud"] = $this->getCustomerPriceAud();
			$array["customerPricePound"] = $this->getCustomerPricePound();
			$array["customerPriceCad"] = $this->getCustomerPriceCad();
			$array["customerPriceInr"] = $this->getCustomerPriceInr();
    	}
		$array["scaleId"] = utf8_decode($this->getScaleId());
		$array["date"] = utf8_decode($this->getDate());	
		return $array;
	}
	
	//backoffice update
	public function loadArrayUpdate2()
	{
		$array["documentId"] = utf8_decode($this->getDocumentId());
		$array["customerPrice"] = $this->getCustomerprice();
		$array["customerPrice2"] = $this->getCustomerprice2();
		$array["scaleId"] = utf8_decode($this->getScaleId());
		if($_COOKIE["_country"] == "en")
    	{
    		$array["customerPriceUS"] = $this->getCustomerPriceUS();
			$array["customerPriceEur"] = $this->getCustomerPriceEur();
			$array["customerPriceAud"] = $this->getCustomerPriceAud();
			$array["customerPricePound"] = $this->getCustomerPricePound();
			$array["customerPriceCad"] = $this->getCustomerPriceCad();
			$array["customerPriceInr"] = $this->getCustomerPriceInr();
			$array["customerPriceUS2"] = $this->getCustomerPriceUS2();
			$array["customerPriceEur2"] = $this->getCustomerPriceEur2();
			$array["customerPriceAud2"] = $this->getCustomerPriceAud2();
			$array["customerPricePound2"] = $this->getCustomerPricePound2();
			$array["customerPriceCad2"] = $this->getCustomerPriceCad2();
			$array["customerPriceInr2"] = $this->getCustomerPriceInr2();
    	}
    	//print_r($array);
		return $array;
	}
	
	public function update($identifier = NULL)
	{
		$data = $this->loadArrayUpdate();
		$whereQuery = "identifier = '".$identifier."'";
		$return = $this->updateQuery($data,$whereQuery);
		return $return;
	}
	
	//backoffice update
	public function update2($identifier = NULL)
	{
		$data = $this->loadArrayUpdate2();
		$whereQuery = "identifier = '".$identifier."'";
		$return = $this->updateQuery($data,$whereQuery);
		return $return;
	}
	
	
	public function updatefromxml($data, $identifier = NULL)
	{
		//data has to be in array format
		//print_r($data);
		$whereQuery = "documentId = '".$identifier."'";
		$return = $this->updateQuery($data,$whereQuery);
		return $return;
	}
	
	public function getdistinctpriceEn()
	{
		$query = "SELECT distinct(customerPriceUS) FROM ".$this->_name." WHERE customerPriceUs!=0 AND documentId NOT IN (select identifier FROM DOCUMENTPARTNER) ORDER BY customerPriceUS";
		if(($result = $this->getQuery($query,true)) != NULL){
			return $result;
		}else
			return false;
	}
	
	public function getdistinctprice()
	{
		$query = "SELECT distinct(customerPrice) FROM ".$this->_name." WHERE documentId NOT IN (select identifier FROM DOCUMENTPARTNER) ORDER BY customerPrice";
		if(($result = $this->getQuery($query,true)) != NULL){
			return $result;
		}else
			return false;
	}
	
	public function getpricebylang()
	{
		if($country == "fr")
		$query = "SELECT distinct(customerPrice2) as customerPrice FROM PRICE ORDER BY customerPrice2";
		else
		$query = "SELECT distinct(customerPrice) as customerPrice FROM PRICE ORDER BY customerPrice";
		$result = $this->getQuery($query,true);
		return $result;
	}
	
	public function setDocumentId($value = NULL)
	{
		$this->documentId = $value;
	}
	
	public function setCustomerprice($value = NULL)
	{
		$this->customerPrice = $value;
	}
	
	public function setCustomerprice2($value = NULL)
	{
		$this->customerPrice2 = $value;
	}

	public function setScaleId($value = NULL)
	{
		$this->scaleId = $value;
	}
	
	public function setDate($value = NULL)
	{
		$this->date = $value;
	}
	
	public function setCustomerPriceUS2($value = NULL)
	{
		$this->customerPriceUS2 = $value;
	}
	
	public function setCustomerPriceEur2($value = NULL)
	{
		$this->customerPriceEur2 = $value;
	}
	
	public function setCustomerPriceAud2($value = NULL)
	{
		$this->customerPriceAud2 = $value;
	}
	
	public function setCustomerPricePound2($value = NULL)
	{
		$this->customerPricePound2 = $value;
	}
	
	public function setCustomerPriceCad2($value = NULL)
	{
		$this->customerPriceCad2 = $value;
	}
	
	public function setCustomerPriceInr2($value = NULL)
	{
		$this->customerPriceInr2 = $value;
	}
	
	public function getDocumentId()
	{
		return $this->documentId;
	}
	
	public function getCustomerprice()
	{
		return $this->customerPrice;
	}
	
	public function getCustomerprice2()
	{
		return $this->customerPrice2;
	}

	public function getScaleId()
	{
		return $this->scaleId;
	}
	
	public function getDate()
	{
		return $this->date;
	}

	public function loadByDocumentId($id)
	{
		$query = "SELECT * FROM ".$this->_name." where documentId = '".$id."'";
		$result = $this->getQuery($query,true);
		foreach ($result as $resul)
		{
			$this->loadData($resul);
			$this->setIdentifierprice($resul['identifier']);
		}
	}

	public function setIdentifierprice($id)
	{
		$this->idnt = $id;
	}
	
	public function getIdentifierprice()
	{
		return $this->idnt;
	}

	public function getCustomerPriceUS()
	{
		return $this->customerPriceUS;
	}
	
	public function getCustomerPriceEur()
	{
		return $this->customerPriceEur;
	}
	
	public function getCustomerPriceAud()
	{
		return $this->customerPriceAud;
	}
	
	public function getCustomerPricePound()
	{
		return $this->customerPricePound;
	}
	
	public function getCustomerPriceCad()
	{
		return $this->customerPriceCad;
	}
	
	public function getCustomerPriceInr()
	{
		return $this->customerPriceInr;
	}
	
	public function setCustomerPriceUS($value = NULL)
	{
		$this->customerPriceUS = $value;
	}
	
	public function setCustomerPriceEur($value = NULL)
	{
		$this->customerPriceEur = $value;
	}
	
	public function setCustomerPriceAud($value = NULL)
	{
		$this->customerPriceAud = $value;
	}
	
	public function setCustomerPricePound($value = NULL)
	{
		$this->customerPricePound = $value;
	}
	
	public function setCustomerPriceCad($value = NULL)
	{
		$this->customerPriceCad = $value;
	}
	
	public function setCustomerPriceInr($value = NULL)
	{
		$this->customerPriceInr = $value;
	}
	
	
	public function getCustomerPriceUS2()
	{
		return $this->customerPriceUS2;
	}
	
	public function getCustomerPriceEur2()
	{
		return $this->customerPriceEur2;
	}
	
	public function getCustomerPriceAud2()
	{
		return $this->customerPriceAud2;
	}
	
	public function getCustomerPricePound2()
	{
		return $this->customerPricePound2;
	}
	
	public function getCustomerPriceCad2()
	{
		return $this->customerPriceCad2;
	}

	public function getCustomerPriceInr2()
	{
		return $this->customerPriceInr2;
	}
	
	public function getPriceByCurrency($docId,$currency=null)
	{
		switch($country)
		{
			case 'IND':
				$field='customerPriceInr';
				break;
			case 'USA':
				$field='customerPriceUS';
				break;
			case 'AUD':
				$field='customerPriceAud';
				break;
			case 'FRA':
				$field='customerPriceEur';
				break;
			case 'GBR':
				$field='customerPricePound';
			case 'CAN':
				$field='customerPriceCAD';
			default :
				$field='customerPrice';
				break;
		}
		$query="select $field from $this->_name where documentId = $docId";
		$result = $this->getQuery($query,false);
		if(!empty($result))
			return($result[0]->$field);
	}
	
	
	
	public function updatedocPrice($priceArr,$id)
	{		
		$data = $priceArr;
		//$whereQuery = "documentId = '".$id."'";
		echo $whereQuery = "documentId IN (".$id.")";
		$return = $this->updateQuery($data,$whereQuery);

		return $return;
	}
	
	
	public function checkdocidPrice($docId)
	{
		$query="select * from $this->_name where documentId = $docId";
		return $this->exist($query);
	}
	
	//test purpose only
	public function testinsertprice()
	{
		$query="SELECT * FROM DOCUMENT WHERE identifier NOT IN (SELECT documentId FROM PRICE ) ORDER BY `DOCUMENT`.`identifier`";
		$result = $this->getQuery($query,true);
		
		return $result;
	}
			
}
	