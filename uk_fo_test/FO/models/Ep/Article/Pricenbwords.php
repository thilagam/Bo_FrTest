<?php
/**
 * Ep_Articles_Article
 * @author Admin
 * @package Articles
 * @version 1.0
 */
class Ep_Article_Pricenbwords extends Ep_Db_Identifier
{
	protected $_name = 'Pricenbwords';
	private $category_id;
	private $charprice;
	private $wordprice;
	private $sheetprice;
	
	public function loadData($array)
	{
		$this->setCategory_id($array["category_id"]) ;
		$this->setCharprice($array["charprice"]) ;
		$this->setWordprice($array["wordprice"]) ;
		$this->setSheetprice($array["sheetprice"]) ;
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["category_id"] = $this->getCategory_id();
		$array["charprice"] = $this->getCharprice;	
		$array["wordprice"] = $this->getWordprice;
		$array["sheetprice"] = $this->getSheetprice;
			
		return $array;
	}
	////////////////////////////////////////////////////////////Set methods ////////////////////////////////////////////////////////////////////////////////	
	public function setCategory_id($category_id) { $this->category_id = $category_id; } 
	public function setCharprice($charprice) { $this->charprice = $charprice; } 
	public function setWordprice($wordprice) { $this->wordprice = $wordprice; } 
	public function setSheetprice($sheetprice) { $this->sheetprice = $sheetprice; } 
	////////////////////////////////////////////////////////////Get methods //////////////////////////////////////////////////////////////////////////////	
	public function getCategory_id() { return $this->category_id; } 
	public function getCharprice() { return $this->charprice; } 
	public function getWordprice() { return $this->wordprice; } 
	public function getSheetprice() { return $this->sheetprice; } 
	
	public function getnbwordsprice($art_cat,$art_sign_type)
	{
		if($art_sign_type=="chars")
		{
			$whereQuery = "	category_id = '".$art_cat."'";
			$query = "select charprice from ".$this->_name." where ".$whereQuery;		
			if(($result = $this->getQuery($query,true)) != NULL)
				return $result[0]['charprice'];
			else
				return "NO";
		}
		else if($art_sign_type=="words")
		{
			$whereQuery = "	category_id = '".$art_cat."'";
			$query = "select wordprice from ".$this->_name." where ".$whereQuery;		
			//echo $query;
			if(($result = $this->getQuery($query,true)) != NULL)
				return $result[0]['wordprice'];
			else
				return "NO";
		}
		else if($art_sign_type=="sheets")
		{
			$whereQuery = "	category_id = '".$art_cat."'";
			$query = "select sheetprice from ".$this->_name." where ".$whereQuery;		
			if(($result = $this->getQuery($query,true)) != NULL)
				return $result[0]['sheetprice'];
			else
				return "NO";
		}
		
	}
}

