<?php
/**
 * This Model  is responsible for ongoing operations
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Ongoing_Delivery extends Ep_Db_Identifier
{

	protected $_name = 'Delivery';
	function getOngoingAOList($searchParameters=NULL,$limit=NULL)
	{
		
		if($searchParameters['client_id'])
			$condition.=" AND d.user_id='".$searchParameters['client_id']."'";

		if($searchParameters['ao_id'])
			$condition.=" AND d.id='".$searchParameters['ao_id']."'";

		if($searchParameters['pay_status'])
		{
			if($searchParameters['pay_status']!='all')
				$condition.=" AND a.paid_status='".$searchParameters['pay_status']."'";
			$searchParameters['sorttype']='all';
		}

		if($searchParameters['manager_id'])
			$condition.=" AND d.created_user='".$searchParameters['manager_id']."'";
			
		if($searchParameters['startdate'] !='' && $searchParameters['enddate']!='')
        {
             $start_date = str_replace('/','-',$searchParameters['startdate']);
             $end_date = str_replace('/','-',$searchParameters['enddate']);
             $start_date = date('Y-m-d', strtotime($start_date));
             $end_date = date('Y-m-d', strtotime($end_date));
             $condition.= " AND DATE(d.created_at) BETWEEN '".$start_date."' AND DATE_ADD('".$end_date."', INTERVAL 1 DAY)";
        }

		//Have query for tier par
		if($searchParameters['sorttype']=='close')
			$haveQuery=" Having totalArticle=published_articles";
		else if($searchParameters['sorttype']=='new')
			$haveQuery=" Having totalParticipations<=0";
		else if($searchParameters['sorttype']=='all')
			$haveQuery='';
        else if($searchParameters['sorttype']=='allongoing')
            $haveQuery=" Having totalArticle != published_articles ";
		else
          $haveQuery=" Having totalArticle != published_articles ";

			//$haveQuery=" Having totalArticle > published_articles AND totalParticipations >0  ";

		if($limit)
            $limitCondition=" limit 0,$limit";


		$ongoingQuery="SELECT IF(c.company_name!='',c.company_name,u.email) as client,

						count(distinct a.id) as totalArticle,

						(SELECT count(DISTINCT pa.article_id)
							FROM Participation pa INNER JOIN Article a3 ON a3.id=pa.article_id 
							WHERE a3.delivery_id=d.id and pa.status='published') as published_articles,


                        (SELECT avg(a4.progressbar_percent)
							FROM Article a4
							INNER JOIN Delivery d4 ON a4.delivery_id=d4.id WHERE d4.id = d.id) as avgprogpercentage,

						(SELECT  count(pa.id) as partIds
						FROM Delivery d2
					    INNER JOIN Article a4 ON d2.id=a4.delivery_id
						INNER JOIN Participation pa ON a4.id=pa.article_id
						WHERE d2.id=d.id) as totalParticipations,

						(SELECT login FROM User u where u.identifier=IF(d.created_by='BO',d.created_user,d.user_id))as incharge,
						(SELECT CONCAT(up.first_name,' ',up.last_name) FROM User u1 INNER JOIN UserPlus up ON u1.identifier=up.user_id where u1.identifier=IF(d.created_by='BO',d.created_user,d.user_id))as projectmanager,						
						IF(d.created_by='BO',d.created_user,d.user_id) as incharge_id,

						d.*								

						FROM  Delivery d
						INNER JOIN Article a ON d.id=a.delivery_id
						INNER JOIN User u ON u.identifier=d.user_id
						LEFT  JOIN Client c ON u.identifier=c.user_id
						WHERE $this->visibility $condition
						
						Group BY d.id
						$haveQuery
						ORDER BY d.created_at DESC
						 $limitCondition
						";

		//echo  $ongoingQuery;//exit;
        if(($count=$this->getNbRows($ongoingQuery))>0)
        {
            $ongoingAO=$this->getQuery($ongoingQuery,true);
            return $ongoingAO;

        }
        else
        	return NULL;


	}

	function getOngoingAODetails($searchParameters=NULL,$limit=NULL)
	{
		if($searchParameters['client_id'])
			$condition.=" AND d.user_id='".$searchParameters['client_id']."'";

		if($searchParameters['ao_id'])
			$condition.=" AND d.id='".$searchParameters['ao_id']."'";

		if($searchParameters['pay_status'])
		{
			if($searchParameters['pay_status']!='all')
				$condition.=" AND a.paid_status='".$searchParameters['pay_status']."'";
			$searchParameters['sorttype']='all';
		}


		if($searchParameters['startdate'] !='' && $searchParameters['enddate']!='')
        {
             $start_date = str_replace('/','-',$searchParameters['startdate']);
             $end_date = str_replace('/','-',$searchParameters['enddate']);
             $start_date = date('Y-m-d', strtotime($start_date));
             $end_date = date('Y-m-d', strtotime($end_date));
             $condition.= " AND DATE(d.created_at) BETWEEN '".$start_date."' AND DATE_ADD('".$end_date."', INTERVAL 1 DAY)";
        }

		//Have query for tier par
		if($searchParameters['sorttype']=='close')
			$haveQuery=" Having totalArticle=published_articles";
		else if($searchParameters['sorttype']=='new')
			$haveQuery=" Having totalParticipations<=0";
		else if($searchParameters['sorttype']=='all')
			$haveQuery='';
		else	
			$haveQuery=" Having totalArticle > published_articles AND totalParticipations >0  ";		

		if($limit)
            $limitCondition=" limit 0,$limit";

        //for edit-AO details
        if($searchParameters['edit_ao'])
        {
        	$selectQuery.=", (select group_concat( DISTINCT option_id separator ',') as child_options From  DeliveryOptions  where delivery_id=d.id) as child_options";
			$selectQuery.=", (select count(*) From User where status='Active' and type='contributor' and blackstatus='no' and profile_type='senior') as sc_count , 
							 (select count(*) From User where status='Active' and type='contributor' and blackstatus='no' and profile_type='junior') as jc_count , 
							 (select count(*) From User where status='Active' and type='contributor' and blackstatus='no' and profile_type='sub-junior') as jc0_count ";	        	
        }

		/*
        FORMAT((SELECT  sum(a1.price_final+d.premium_total) as totalAmount	
											FROM Article a1 ,Payment p						
											WHERE a1.delivery_id=d.id and p.delivery_id=d.id
										),2) as totalAmount,

						FORMAT((SELECT  (	SUM(IF(paid_status='paid' ,a2.price_final+d.premium_total,0))+(IF (a2.created_by!='BO',p.amount,0))) as amount_paid
											FROM Article a2 ,Payment p						
											WHERE a2.delivery_id=d.id and p.delivery_id=d.id
										),2) as amount_paid,*/


		$ongoingQuery="SELECT IF(c.company_name!='',c.company_name,u.email) as client,
						FORMAT((SELECT  sum(p.price_user) as totalAmount	
								  FROM Participation p
								INNER JOIN Article a1 ON a1.id=p.article_id
								INNER JOIN Delivery d1 ON a1.delivery_id=d1.id
								WHERE a1.delivery_id=d.id and p.status in ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','plag_exec','published')
								AND p.cycle=0),2) as totalAmount,
						
						FORMAT((SELECT  sum(cp.price_corrector) as totalAmount	
								  FROM CorrectorParticipation cp
								INNER JOIN Article a1 ON a1.id=cp.article_id
								INNER JOIN Delivery d1 ON a1.delivery_id=d1.id
								WHERE a1.delivery_id=d.id and cp.status in ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','plag_exec','published')
								AND cp.cycle=0),2) as totalCorrectorAmount,	

						FORMAT((SELECT  sum(p.price_user) as amount_paid	
								  FROM Participation p
								INNER JOIN Article a1 ON a1.id=p.article_id
								INNER JOIN Delivery d1 ON a1.delivery_id=d1.id
								WHERE a1.delivery_id=d.id and p.status in ('published')
								AND p.cycle=0),2) as amount_paid,

						FORMAT((SELECT  sum(cp.price_corrector) as totalAmount	
								  FROM CorrectorParticipation cp
								INNER JOIN Article a1 ON a1.id=cp.article_id
								INNER JOIN Delivery d1 ON a1.delivery_id=d1.id
								WHERE a1.delivery_id=d.id and cp.status in ('published')
								AND cp.cycle=0),2) as corrector_amount_paid,

						count(distinct a.id) as totalArticle,

						(SELECT count(DISTINCT pa.article_id) 
							FROM Participation pa INNER JOIN Article a3 ON a3.id=pa.article_id 
							WHERE a3.delivery_id=d.id and pa.status='published') as published_articles,

						(SELECT  count(pa.id) as partIds
						FROM Delivery d2
					    INNER JOIN Article a4 ON d2.id=a4.delivery_id
						INNER JOIN Participation pa ON a4.id=pa.article_id
						WHERE d2.id=d.id) as totalParticipations,

						(SELECT login FROM User u where u.identifier=IF(d.created_by='BO',d.created_user,d.user_id))as incharge,			
						IF(d.created_by='BO',d.created_user,d.user_id) as incharge_id,


						FORMAT((Select Avg(marks) as avg_marks FROM Participation p

						INNER JOIN Article a1 ON p.article_id=a1.id
						INNER JOIN Delivery d1 ON a1.delivery_id=d1.id

						WHERE d1.id=d.id),1) as avg_marks ,

						(Select count(*) FROM Participation p

							INNER JOIN Article a1 ON p.article_id=a1.id
							INNER JOIN Delivery d1 ON a1.delivery_id=d1.id
							WHERE d1.id=d.id and marks IS NOT NULL and marks!=''
						) as article_count_marks,	

						d.*	

						$selectQuery		

						FROM  Delivery d
						INNER JOIN Article a ON d.id=a.delivery_id
						INNER JOIN User u ON u.identifier=d.user_id
						LEFT  JOIN Client c ON u.identifier=c.user_id"
						. (!empty($condition) ? ' WHERE 1=1 ' . $condition : '') . "
						
						Group BY d.id
						$haveQuery
						ORDER BY d.created_at DESC
						 $limitCondition
						";

		//echo  $ongoingQuery;//exit;
        if(($count=$this->getNbRows($ongoingQuery))>0)
        {
            $ongoingAO=$this->getQuery($ongoingQuery,true);
            return $ongoingAO;

        }
        else
        	return NULL;


	}
	//update delivery
	public function updateDelivery($data,$query)
    {
        $data['updated_at']=date("Y-m-d", time());
        $this->updateQuery($data,$query);
    }
	//delete Delivery
	public function DeleteDelivery($id)
	{
		//$SelQuery="DELETE FROM Delivery WHERE id='".$id."'";
		//$this->getQuery($SelQuery,true);
		$whereQuery ="id ='".$id."'";
		$this->deleteQuery($whereQuery);
	}

	public function getDeliveryDetails($id)
	{
		$SelQuery = "SELECT * FROM Delivery WHERE id='".$id."'";
		$result = $this->getQuery($SelQuery,true);
		return $result;
	}


	//get Ongoing SuperClient AO Details
	public function OngoingSuperClientAODetails($searchParameters=NULL,$limit=NULL)
	{

		if($searchParameters['client_id'])
		{
			$condition.=" AND u.identifier='".$searchParameters['client_id']."'";

			/*$selectQuery=", (SELECT group_concat(IF(u2.login!='',u2.login,u2.email) separator ' , ') FROM User u2 where u2.identifier in (
							SELECT IF(d3.created_by='BO',d3.created_user,d3.user_id) as client From Delivery d3 WHERE d3.id IN (
								SELECT DISTINCT(d2.id)
								FROM Delivery d2, Client c2
								WHERE c2.user_id=c.user_id  AND (find_in_set(d2.id, c2.access_deliveries_list )>0 OR d2.user_id=c.user_id)
							)
						)
					)as incharge";*/
				$selectQuery=", (SELECT IF(u2.login!='',u2.login,u2.email) as created_user FROM User u2  where u2.identifier=u.created_user) as created_user";
		}


		$superClientAOquery="SELECT u.identifier,IF(c.company_name!='',c.company_name,u.email) as client, u.type,u.created_at,
					u.email,c.reference_title,
					(SELECT group_concat(IF(c11.company_name!='',c11.company_name,u11.email))
						FROM User u11, Client c11
						WHERE u11.identifier=c11.user_id AND (find_in_set(c11.user_id, c.access_clients_list )>0)
					)as clients,
					(SELECT count(DISTINCT(d.id))
						FROM Delivery d, Client c1
						WHERE c1.user_id=c.user_id  AND ((find_in_set(d.user_id, c.access_clients_list )>0 ) OR d.user_id=c.user_id)
					)as totalAO,					
					(SELECT group_concat(DISTINCT(d2.id) separator ',')
						FROM Delivery d2, Client c2
						WHERE c2.user_id=c.user_id  AND ((find_in_set(d2.user_id, c.access_clients_list )>0) OR d2.user_id=c.user_id)
					)as clientDeliveries,
					(SELECT group_concat(DISTINCT(IF(c2.company_name!='',c2.company_name,u1.email)) separator '<br>')  as super_client 
						FROM Client c2
						INNER JOIN User u1 ON u1.identifier=c2.user_id
						WHERE find_in_set(u.identifier, c2.access_clients_list )>0
					) as superClient,
					FORMAT((SELECT  sum(p.price_user) as totalAmount	
						  FROM Participation p
						INNER JOIN Article a1 ON a1.id=p.article_id
						INNER JOIN Delivery d3 ON a1.delivery_id=d3.id
						JOIN Client c3
						WHERE c3.user_id=c.user_id  AND ((find_in_set(d3.user_id, c.access_clients_list )>0 ) OR d3.user_id=c.user_id) and p.status in ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','plag_exec','published')
						AND p.cycle=0),2) as totalDeals,
					FORMAT((SELECT  sum(p.price_user) as totalAmount	
						  FROM Participation p
						INNER JOIN Article a1 ON a1.id=p.article_id
						INNER JOIN Delivery d3 ON a1.delivery_id=d3.id
						JOIN Client c3
						WHERE c3.user_id=c.user_id  AND ((find_in_set(d3.user_id, c.access_clients_list )>0 ) OR d3.user_id=c.user_id) and p.status in ('published')
						AND p.cycle=0),2) as paidDeals,
					
					c.access_clients_list

					$selectQuery

				FROM 
				 User u 
				 LEFT  JOIN Client c ON u.identifier=c.user_id
				 WHERE u.type in ('superclient') and u.status='Active'
				 $condition
			 ";
			 //u.type in ('client','superclient')
		//echo  $superClientAOquery;exit;        
        if(($count=$this->getNbRows($superClientAOquery))>0)
        {
            $superClientOngoingAO=$this->getQuery($superClientAOquery,true);
            return $superClientOngoingAO;

        }
        else
        	return NULL;	 

	}
	
	public function getpublishedAOs($client_id)
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
								JOIN Client c

							WHERE c.user_id='".$client_id."'  AND ((find_in_set(d.user_id, c.access_clients_list )>0 ) OR d.user_id=c.user_id)

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
	
	public function OngoinggetSuperClients($user)
	{
		$getsuperclientquery="SELECT DISTINCT u1.identifier,IF(c2.company_name!='',c2.company_name,u1.email) as super_client,email,password
						FROM Client c2
						INNER JOIN User u1 ON u1.identifier=c2.user_id
						WHERE find_in_set('".$user."', c2.access_clients_list )>0";
		//echo $getsuperclientquery;exit;
        if($getsuperclient=$this->getQuery($getsuperclientquery,true))
            return $getsuperclient;
		else
        	return NULL;
	}
	
	public function getContributorsAo($ao,$ctype)
	{
		$ctypearr=explode("|",$ctype);
		
		if(in_array('writer',$ctypearr))
		{
			$getwriterquery="SELECT 
								u.identifier,u.email,up.first_name,up.last_name 
							FROM 
								Article a INNER JOIN Participation p ON a.id=p.article_id 
								INNER JOIN User u ON p.user_id=u.identifier 
								LEFT JOIN UserPlus up ON u.identifier=up.user_id 
							WHERE 
								a.delivery_id='".$ao."' AND p.status IN ('bid','under_study','disapproved','disapproved_temp','closed_temp','plag_exec','time_out','published') 
								AND p.cycle='0'";
			
			$resultwriter=$this->getQuery($getwriterquery,true);
		}
		else
			$resultwriter=array();
			
		if(in_array('corrector',$ctypearr))
		{
			$getcorrectorquery="SELECT 
								u.identifier,u.email,up.first_name,up.last_name 
							FROM 
								Article a INNER JOIN CorrectorParticipation cp ON a.id=cp.article_id 
								INNER JOIN User u ON cp.corrector_id=u.identifier 
								LEFT JOIN UserPlus up ON u.identifier=up.user_id 
							WHERE 
								a.delivery_id='".$ao."' AND cp.status IN ('bid','under_study','disapproved','disapproved_temp','closed_temp','plag_exec','time_out','published') 
								AND cp.cycle='0'";
			
			$resultcorrector=$this->getQuery($getcorrectorquery,true);
		}
		else
			$resultcorrector=array();
			
		return array_merge($resultwriter,$resultcorrector);
		
	}
    public function getDeliveryArticleDetails($id){
        $SelQuery = "SELECT A.* FROM Article AS A
INNER JOIN Delivery AS D ON A.delivery_id = D.id
WHERE D.id='".$id."'";
        $result = $this->getQuery($SelQuery,true);
        return $result;
    }
}
