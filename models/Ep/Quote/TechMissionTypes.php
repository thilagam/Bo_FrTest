<?php

class Ep_Quote_TechMissionTypes extends Ep_Db_Identifier
{
	protected $_name = 'TechMissionTypes';
	
	public function getTechMissionTypes($tid)
	{
		$where="";
		if($tid)
			$where=" WHERE tid='".$tid."'";
		$query=" SELECT * From TechMissionTypes ".$where." ORDER BY created_at DESC";	
		$techDetails=$this->getQuery($query,true);
        return $techDetails;
    }
	
	public function AddTechmission($postarray)
	{
		$this->insertQuery($postarray);
	}
	
	public function UpdateTechmission($updatearray,$tid)
	{
		$where= "tid= '".$tid."'";
		$this->updateQuery($updatearray,$where);
	}
	
	public function deletetechmissiontype($tid)
	{
		$wheretm=" tid='".$tid."'";
		$this->deleteQuery($wheretm);
	}
}