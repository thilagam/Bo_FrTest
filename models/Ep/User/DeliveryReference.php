<?php

class Ep_User_DeliveryReference extends Ep_Db_Identifier
{
	protected $_name = 'DeliveryReference';
	
    public function InsertReference($refarray)
	{
		$refarray['id']=$this->getIdentifier();
		$this->insertQuery($refarray);
		return $refarray['id'];
	}
	
	public function UpdateReference($data,$identifier)
    {
        $where=" id='".$identifier."'";
        $this->updateQuery($data,$where);
    }
	
	public function ListReference()
    {
       $query = "SELECT * FROM ".$this->_name."";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
    }
    
	public function getRefRecord($rid)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE  id = '".$rid."'";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
    }
	
	public function OngoingDeliveryRefDetails()
	{

		/*$superClientAOquery="SELECT r.id,r.created_at,r.title,
					(SELECT group_concat(IF(c11.company_name!='',c11.company_name,u11.email))
						FROM User u11, Client c11
						WHERE u11.identifier=c11.user_id AND (find_in_set(c11.user_id, r.access_client )>0)
					)as clients,
					
					
					FORMAT((SELECT  sum(p.price_user) as totalAmount	
						  FROM Participation p
						INNER JOIN Article a1 ON a1.id=p.article_id
						INNER JOIN Delivery d3 ON a1.delivery_id=d3.id
						JOIN Client c3
						WHERE (find_in_set(d3.user_id, r.access_client )>0 )  and p.status in ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','plag_exec','published')
						AND p.cycle=0),2) as totalDeals,
					FORMAT((SELECT  sum(p.price_user) as totalAmount	
						  FROM Participation p
						INNER JOIN Article a1 ON a1.id=p.article_id
						INNER JOIN Delivery d3 ON a1.delivery_id=d3.id
						JOIN Client c3
						WHERE (find_in_set(d3.user_id, r.access_client )>0 ) and p.status in ('published')
						AND p.cycle=0),2) as paidDeals,
					
					r.access_client
				FROM 
				 DeliveryReference r 
				 
			 ";*/
		$superClientAOquery="SELECT r.id,r.created_at,r.title,
					(SELECT group_concat(IF(c11.company_name!='',c11.company_name,u11.email))
						FROM User u11, Client c11
						WHERE u11.identifier=c11.user_id AND (find_in_set(c11.user_id, r.access_client )>0)
					)as clients,r.access_client
				FROM 
				 DeliveryReference r ";
			 
		  
        if(($count=$this->getNbRows($superClientAOquery))>0)
        {
            $superClientOngoingAO=$this->getQuery($superClientAOquery,true);
            return $superClientOngoingAO;

        }
        else
        	return NULL;	 

	}
	
	public function OngoingDeliveryRefDetailseach($refid)
	{

		$superClientAOquery="SELECT r.*,
					FORMAT((SELECT  sum(p.price_user) as totalAmount	
						  FROM Participation p
						INNER JOIN Article a1 ON a1.id=p.article_id
						INNER JOIN Delivery d3 ON a1.delivery_id=d3.id
						JOIN Client c3
						WHERE (find_in_set(d3.user_id, r.access_client )>0 )  and p.status in ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','plag_exec','published')
						AND p.cycle=0),2) as totalDeals,
					FORMAT((SELECT  sum(p.price_user) as totalAmount	
						  FROM Participation p
						INNER JOIN Article a1 ON a1.id=p.article_id
						INNER JOIN Delivery d3 ON a1.delivery_id=d3.id
						JOIN Client c3
						WHERE (find_in_set(d3.user_id, r.access_client )>0 ) and p.status in ('published')
						AND p.cycle=0),2) as paidDeals,
					
					r.access_client
				FROM 
				 DeliveryReference r WHERE r.id='".$refid."'
				 
			 ";
			
		//echo  $superClientAOquery;exit;        
        if(($count=$this->getNbRows($superClientAOquery))>0)
        {
            $superClientOngoingAO=$this->getQuery($superClientAOquery,true);
            return $superClientOngoingAO;

        }
        else
        	return NULL;	 

	}
	
	public function getpublishedAOs($ref_id)
	{

		$publishedAOquery="SELECT d.id,count(DISTINCT pa.id) as totalArticle,

							 (SELECT  count(DISTINCT p.article_id) as totalArticles
													  FROM Participation p
													INNER JOIN Article a1 ON a1.id=p.article_id
													INNER JOIN Delivery d1 ON a1.delivery_id=d1.id						
													WHERE p.status in ('published') AND p.cycle=0 AND d1.id=d.id
							) as publishedArticle

							FROM 
								Participation pa
								INNER JOIN Article a ON a.id=pa.article_id
								INNER JOIN Delivery d ON a.delivery_id=d.id
								JOIN DeliveryReference r

							WHERE r.id='".$ref_id."' AND (find_in_set(d.user_id, r.access_client )>0 ) 
							Group By d.id

							Having totalArticle=publishedArticle
			 ";
		//echo  $superClientAOquery;exit;        
        if($superClienpublisehdAO=$this->getQuery($publishedAOquery,true))
        {
            
            return $superClienpublisehdAO;

        }
        else
        	return NULL;
	}
	
	public function deleteRef($ref)
	{
		$whereq=" id='".$ref."'";
		$this->deleteQuery($whereq);
	}
}
