<?php
/**
 * Ep_Article_DeliveryOptions
 * @author Admin
 * @package DeliveryOptions
 * @version 1.0
  */
class Ep_Delivery_DeliveryOptions extends Ep_Db_Identifier
{
	protected $_name = 'DeliveryOptions';

	public function insertOptions($did,$optarr)
	{
		$this->_name = 'DeliveryOptions';
	
		for($p=0;$p<count($optarr);$p++)
		{
			$Oparray = array(); 
			$Oparray["delivery_id"] = $did; 
			$Opp=explode("_",$optarr[$p]);
			$Oparray['option_id']=$Opp[0];
			$result=$this->insertQuery($Oparray);
		}
	}
	
	/////////get delivery options full detials  w.r.t delivery id  ///////////////////////////
	public function getDelOptions($delId)
	{
		$query = "select * from ".$this->_name."  WHERE delivery_id=".$delId;

		if(($result = $this->getQuery($query,true)) != NULL)
         return $result;
     	else
		 return "NO";
	}
	
	public function deleteDeliveryOptions($delId)
	{
		$whereQuery ="delivery_id ='".$delId."'";
		$this->deleteQuery($whereQuery);
	}
}

