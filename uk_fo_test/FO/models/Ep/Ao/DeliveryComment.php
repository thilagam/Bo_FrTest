<?php

class Ep_Ao_DeliveryComment extends Ep_Db_Identifier
{
	protected $_name = 'DeliveryComment';

	public function InsertComment($carray)
	{
		$carray['id']=$this->getIdentifier(); 
		
		if($this->insertQuery($carray))
			return $this->getIdentifier(); 
		 else
			return "NO";
	}
	
	public function inactivateComment($cid)
	{
		$wherecm=" id='".$cid."' ";
		
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
							d.id,d.user_id,d.created_at,d.comment,TIMESTAMPDIFF( MINUTE , d.created_at, now( ) ) as minutediff, TIMESTAMPDIFF( HOUR , d.created_at, now( ) ) as hourdiff,
							u.email,LOWER(up.first_name) as first_name,LOWER(up.last_name) as last_name 
						FROM 
							".$this->_name." d INNER JOIN User u ON d.user_id=u.identifier 
							LEFT JOIN UserPlus up ON u.identifier=up.user_id 
						WHERE 
							d.article_id='".$article."' AND d.active='yes'
						ORDER BY 
							d.created_at DESC";
		
		if(($Commentresult= $this->getQuery($CommentQuery,true)) != NULL)
            return $Commentresult;
	}
}	