<?php

class Ep_Article_DeliveryAlert extends Ep_Db_Identifier
{
	protected $_name = 'DeliveryAlert';
	
	public function insertDeliveryAlert($alertArray)
	{
		$this->insertQuery($alertArray);
	}	
	public function checkDeliveryAlert($deliveryId,$user_identifier,$type='writing')
	{
		$checkQuery="SELECT 
							user_id 
						FROM 
							".$this->_name." 
						WHERE 
								user_id='".$user_identifier."' 
							AND	delivery_id='".$deliveryId."' AND type='".$type."'
						";
		//echo $checkQuery;exit;
		if(($count=$this->getNbRows($checkQuery))>0)
        {
            $alertDetails=$this->getQuery($checkQuery,true);
            return $alertDetails;
        }
		else
		{
			return "NO";
		}
	}
	public function getAllDeliveryAlerts($user_identifier,$mission_type)
	{
		if($mission_type=='article-correction')
		{
			$mission_type='article';
			$type='correction';
		}
		else
		{
			$type='writing';
		}
		$getQuery="SELECT 
							* 
						FROM 
							".$this->_name." 
						WHERE 
							user_id='".$user_identifier."' 
						AND	alert='yes'	
						AND mission_type='".$mission_type."' AND type='".$type."'						
						";
		//echo $getQuery;exit;
		if(($count=$this->getNbRows($getQuery))>0)
        {
            $allAlertDetails=$this->getQuery($getQuery,true);
            return $allAlertDetails;
        }
		else
		{
			return "NO";
		}
	}
	
	public function updateDeliveryAlertDetails($data,$where)
    {
        return $this->updateQuery($data,$where);
    }
}