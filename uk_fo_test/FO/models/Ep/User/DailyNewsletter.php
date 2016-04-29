<?php

class Ep_User_DailyNewsletter extends Ep_Db_Identifier
{
	protected $_name = 'DailyNewsletter';
	 
	public function insertNewsletter($inarray)
	{
		$inarray['id']=$this->getIdentifier();
		$this->insertQuery($inarray);
		return $inarray['id'];
	}
	 
	public function getNLByDate($datestamp)
	{
		$contribsQuery="SELECT *
							FROM DailyNewsletter 
						WHERE 
							DATE(created_at)='".$datestamp."' ORDER BY created_at DESC";
		$contribsRessult = $this->getQuery($contribsQuery,true);
		return $contribsRessult;
	}
	
	public function updateNewsletter($nlarray,$id)
	{
		$where = "id='".$id."'";
		$this->updateQuery($nlarray,$where);
	}
}	
	