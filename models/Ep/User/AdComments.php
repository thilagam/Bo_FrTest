<?php

class Ep_User_AdComments extends Ep_Db_Identifier
{
	protected $_name = 'AdComments';

	public function InsertComment($carray)
	{
		$carray['identifier']=$this->createIdentifier(); 
		//print_r($carray);
		if($this->insertQuery($carray))
			return $this->getIdentifier(); 
		else
			return "NO";
	}
	
	public function inactivateComment($cid)
	{
		$wherecm=" identifier='".$cid."' ";
		
		$cmarray=array();
		$cmarray['active']="no"; 
		
		if($this->updateQuery($cmarray,$wherecm))
			return "YES"; 
		 else
			return "NO";
	}
	
	public function getComments($article)
	{
		$CommentQuery="	SELECT 
							d.identifier,d.user_id,d.created_at,d.comments,TIMESTAMPDIFF( MINUTE , d.created_at, now( ) ) as minutediff, TIMESTAMPDIFF( HOUR , d.created_at, now( ) ) as hourdiff,
							u.email,LOWER(up.first_name) as first_name,LOWER(up.last_name) as last_name,u.type 
						FROM 
							".$this->_name." d INNER JOIN User u ON d.user_id=u.identifier 
							LEFT JOIN UserPlus up ON u.identifier=up.user_id 
						WHERE 
							d.type_identifier='".$article."'AND d.type='article' AND d.active='yes'
						ORDER BY 
							d.created_at DESC";
		
		if(($Commentresult= $this->getQuery($CommentQuery,true)) != NULL)
            return $Commentresult;
	}
	
	public function createIdentifier()
    {
        $d = new Date();
		return $d->getSubDate(5,14).mt_rand(100000,999999);
  	}
}	