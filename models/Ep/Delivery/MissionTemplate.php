<?php

class Ep_Delivery_MissionTemplate extends Ep_Db_Identifier
{
	protected $_name = 'MissionTemplate';
	
	public function inserttemplate($arrlist)
    {
		$CheckQuery="SELECT id FROM ".$this->_name." WHERE client_id='".$arrlist['client_id']."' AND title='".trim($arrlist['title'])."'";
		$Checkarray = $this->getQuery($CheckQuery,true);
		
		if(count($Checkarray)>0)
		{
			$arrlist1=array();
			$arrlist1['delivery_id']=$arrlist['delivery_id'];
			$where=" id='".$Checkarray[0]['id']."'";
			$this->updateQuery($arrlist1,$where);
		}
		else
		{
			$arrlist["id"] = $this->getIdentifier();
			$this->insertQuery($arrlist);
		}
	}
	
	public function listtemplates($client)
	{
		$SelectQuery="SELECT m.id,m.title FROM ".$this->_name." m INNER JOIN Delivery d ON m.delivery_id=d.id WHERE m.client_id='".$client."'";
		$res_array = $this->getQuery($SelectQuery,true);
		return $res_array;
	}
	
	public function loadDeliverybytemplate($temp)
	{
		$SelectQuery="SELECT d.*,do.* 
						FROM ".$this->_name." m
						INNER JOIN Delivery d ON m.delivery_id=d.id
						LEFT JOIN DeliveryOptions do ON d.id=do.delivery_id
						WHERE m.id='".$temp."'";
		$res_array = $this->getQuery($SelectQuery,true);
		return $res_array;
	}
	
	public function loadArticlebytemplate($temp)
	{
		$SelectQuery="SELECT a.* 
						FROM ".$this->_name." m
						INNER JOIN Article a ON m.delivery_id=a.delivery_id
						WHERE m.id='".$temp."'ORDER BY a.id";
		$res_array = $this->getQuery($SelectQuery,true);
		return $res_array;
	}
}	