<?php

class EP_User_WhitebookDownloads extends Ep_Db_Identifier
{
	protected $_name = 'WhitebookDownloads';
	
    public function listWdownloads($search)
	{
		$searchby = "";
		
		if($search['start_date']!="" && $search['end_date']!="")
			$searchby.=" AND DATE_FORMAT( created_at, '%d/%m/%Y' ) >= '".$search['start_date']."' AND DATE_FORMAT( created_at, '%d/%m/%Y' ) <= '".$search['end_date']."'";
			
		$query1="SELECT * FROM WhitebookDownloads WHERE 1=1 ".$searchby." ORDER BY created_at DESC";  
		$result1 = $this->getQuery($query1,true);
		return $result1;
	}
	
	public function deleteWhitebook($wbid)
	{
		$whereQuery ="id ='".$wbid."'";
		$this->deleteQuery($whereQuery);
	}
}