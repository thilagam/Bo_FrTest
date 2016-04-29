<?php

class EP_Delivery_Pricenbwords extends Ep_Db_Identifier
{
	protected $_name = 'Pricenbwords';
	
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
	
	public function ListCategoryPrice($cat="")
	{
		if($cat!="")
			$where= " WHERE category_id='".$cat."'";
		else
			$where="";
			
		$SelQuery="SELECT * FROM ".$this->_name. $where;
		$resultset = $this->getQuery($SelQuery,true);
		return $resultset;
	}
	
	public function UpdatePriceCategory($parr,$catid)
	{
		$query= "category_id='".$catid."'";
		$this->updateQuery($parr,$query);
	}
}

