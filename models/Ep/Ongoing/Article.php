<?php
/**
 * This Model  is responsible for ongoing operations
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Ongoing_Article extends Ep_Db_Identifier
{
	protected $_name = 'Article';
	
	function getOngoingArticleDetails($searchParameters=NULL,$limit=NULL)
	{
		
		if($searchParameters['client_id'])
			$condition.=" AND d.user_id='".$searchParameters['client_id']."'";

		if($searchParameters['ao_id'])
			$condition.=" AND d.id='".$searchParameters['ao_id']."'";

		
		if($limit)
            $limitCondition=" limit 0,$limit";


        if($searchParameters['missiontest']=='yes')
        {	
        	$ongoingQuery="SELECT (SELECT count(pa.id) 
								FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 
								WHERE pa.article_id=a.id AND cycle=0) as totalParticipations,
							(SELECT count(pa1.id) 
								FROM Participation pa1 INNER JOIN Article a3 ON a3.id=pa1.article_id 
								WHERE pa1.article_id=a.id AND pa1.cycle=0 AND pa1.status='bid_refused') as totalRefusedParticipations,
								
							(SELECT count(cp.id) 
								FROM CorrectorParticipation cp INNER JOIN Article a2 ON a2.id=cp.article_id 
								WHERE cp.article_id=a.id AND cycle=0) as totalCorrectionParticipations,	
							(SELECT count(cp1.id) 
								FROM CorrectorParticipation cp1 INNER JOIN Article a4 ON a4.id=cp1.article_id 
								WHERE cp1.article_id=a.id AND cp1.cycle=0 AND cp1.status='bid_refused') as totalRefusedCorrectionParticipations,	
							a.*,
							
							(SELECT login FROM User u where u.identifier=IF(d.created_by='BO',d.created_user,d.user_id))as incharge,
							(SELECT p.id FROM Participation p 
								WHERE p.status IN ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','published','plag_exec') AND p.cycle=0 AND  p.article_id=a.id LIMIT 1
							) as writerParticipation,
							
							(SELECT ex.id From Participation ex 
								WHERE ex.id=writerParticipation and (ex.status='time_out' OR (ex.status in ('bid','disapproved')))
							) as expiredWriterParticipation,	

							(SELECT cp.id FROM CorrectorParticipation cp 
								WHERE cp.status IN ('bid', 'under_study','disapproved_temp', 'disapproved','closed_temp', 'published') AND cp.cycle=0 AND  cp.article_id=a.id AND cp.id=cp1.id LIMIT 1
							) as correctorParticipation,
								
							(SELECT ex1.id From CorrectorParticipation ex1 
								WHERE ex1.id=correctorParticipation and (ex1.status='time_out' OR (ex1.status in ('bid','disapproved')))
							) as expiredcorrectorParticipation,
							
							(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN Participation p ON p.user_id=up.user_id
								WHERE p.id=writerParticipation LIMIT 1) 
							as writerName,
							
							(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN CorrectorParticipation cp ON cp.corrector_id=up.user_id
								WHERE cp.id=correctorParticipation LIMIT 1) 
							as correctorName,
							d.publishtime,
							d.missiontest
							
							FROM Article a  
							INNER JOIN Delivery d ON d.id=a.delivery_id
							INNER JOIN User u ON u.identifier=d.user_id	
							LEFT JOIN Participation p1 ON p1.article_id=a.id	
							LEFT JOIN CorrectorParticipation cp1 ON cp1.article_id=a.id					
							WHERE 1=1 $condition						
							$haveQuery
							ORDER BY a.title ASC
							 $limitCondition
							";


        }
        else
        {
			$ongoingQuery="SELECT (SELECT count(pa.id) 
								FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 
								WHERE pa.article_id=a.id AND cycle=0) as totalParticipations,
							(SELECT count(pa1.id) 
								FROM Participation pa1 INNER JOIN Article a3 ON a3.id=pa1.article_id 
								WHERE pa1.article_id=a.id AND cycle=0 AND pa1.status='bid_refused') as totalRefusedParticipations,
							(SELECT count(cp.id) 
								FROM CorrectorParticipation cp INNER JOIN Article a2 ON a2.id=cp.article_id 
								WHERE cp.article_id=a.id AND cycle=0) as totalCorrectionParticipations,	
							(SELECT count(cp1.id) 
								FROM CorrectorParticipation cp1 INNER JOIN Article a4 ON a4.id=cp1.article_id 
								WHERE cp1.article_id=a.id AND cycle=0 AND cp1.status='bid_refused') as totalRefusedCorrectionParticipations,		
							a.*,
							
							(SELECT login FROM User u where u.identifier=IF(d.created_by='BO',d.created_user,d.user_id))as incharge,
							(SELECT p.id FROM Participation p 
								WHERE p.status IN ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','published','plag_exec') AND p.cycle=0 AND  p.article_id=a.id LIMIT 1
							) as writerParticipation,
							
							(SELECT ex.id From Participation ex 
								WHERE ex.id=writerParticipation and (ex.status='time_out' OR (ex.status in ('bid','disapproved')))
							) as expiredWriterParticipation,	

							(SELECT cp.id FROM CorrectorParticipation cp 
								WHERE cp.status IN ('bid', 'under_study','disapproved_temp', 'disapproved','closed_temp', 'published') AND cp.cycle=0 AND  cp.article_id=a.id LIMIT 1
							) as correctorParticipation,
								
							(SELECT ex1.id From CorrectorParticipation ex1 
								WHERE ex1.id=correctorParticipation and (ex1.status='time_out' OR (ex1.status in ('bid','disapproved')))
							) as expiredcorrectorParticipation,
							
							(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN Participation p ON p.user_id=up.user_id
								WHERE p.id=writerParticipation LIMIT 1) 
							as writerName,
							
							(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN CorrectorParticipation cp ON cp.corrector_id=up.user_id
								WHERE cp.id=correctorParticipation LIMIT 1) 
							as correctorName,
							d.publishtime,
							d.missiontest
							
							FROM Article a  
							INNER JOIN Delivery d ON d.id=a.delivery_id
							INNER JOIN User u ON u.identifier=d.user_id						
							WHERE 1=1 $condition						
							$haveQuery
							ORDER BY a.title ASC
							 $limitCondition
							";
		}				
		//echo $ongoingQuery;exit; 				
		    
        if(($count=$this->getNbRows($ongoingQuery))>0)
        {
            $ongoingAO=$this->getQuery($ongoingQuery,true);
            return $ongoingAO;

        }
        else
        	return NULL;
	}

	public function getEditArticleDetails($artId)
	{
		$query = "select a.*,d.AOtype, d.user_id,d.correction_type,d.contract_mission_id,d.correction as correctionao
                  FROM Article a  INNER JOIN Delivery d ON a.delivery_id=d.id
                  WHERE a.id = '".$artId."' LIMIT 0,1";
      
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
	}

    //update delivery
	public function updateArticle($data,$query)
    {
        $data['updated_at']=date("Y-m-d H:i:s", time());
        $this->updateQuery($data,$query);
    }

    //super client Article details w.r.t AO
    function OngoingSuperClientArticleDetails($searchParameters=NULL,$limit=NULL)
	{
		
		if($searchParameters['client_id'])
			$condition.=" AND c.user_id='".$searchParameters['client_id']."'";
		

		
		if($limit)
            $limitCondition=" limit 0,$limit";


        
		$ongoingSuperClientsQuery="SELECT d.user_id,(SELECT count(pa.id) 
							FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 
							WHERE pa.article_id=a.id AND cycle=0) as totalParticipations,
						
						(SELECT count(cp.id) 
							FROM CorrectorParticipation cp INNER JOIN Article a2 ON a2.id=cp.article_id 
							WHERE cp.article_id=a.id AND cycle=0) as totalCorrectionParticipations,	

						(SELECT company_name FROM Client c1 where c1.user_id=d.user_id) as client,
						d.title as delivery_title,
						d.id as delivery_id,

						a.*,
						
						(SELECT login FROM User u where u.identifier=IF(d.created_by='BO',d.created_user,d.user_id))as incharge,
						(SELECT p.id FROM Participation p 
							WHERE p.status IN ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','published','plag_exec') AND p.cycle=0 AND  p.article_id=a.id LIMIT 1
						) as writerParticipation,
						
						(SELECT ex.id From Participation ex 
							WHERE ex.id=writerParticipation and (ex.status='time_out' OR (ex.status in ('bid','disapproved')))
						) as expiredWriterParticipation,	

						(SELECT cp.id FROM CorrectorParticipation cp 
							WHERE cp.status IN ('bid', 'under_study','disapproved_temp', 'disapproved','closed_temp', 'published','closed') AND cp.cycle=0 AND  cp.article_id=a.id LIMIT 1
						) as correctorParticipation,
							
						(SELECT ex1.id From CorrectorParticipation ex1 
							WHERE ex1.id=correctorParticipation and (ex1.status='time_out' OR (ex1.status in ('bid','disapproved')))
						) as expiredcorrectorParticipation,
						
						(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN Participation p ON p.user_id=up.user_id
							WHERE p.id=writerParticipation LIMIT 1) 
						as writerName,
						
						(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN CorrectorParticipation cp ON cp.corrector_id=up.user_id
							WHERE cp.id=correctorParticipation LIMIT 1) 
						as correctorName,
						d.publishtime,
						d.missiontest
						
						FROM Article a  
						INNER JOIN Delivery d ON d.id=a.delivery_id
						JOIN User u
						LEFT JOIN Client c ON c.user_id=u.identifier
						
						WHERE u.type in ('client','superclient') and u.status='Active' 
						$condition AND ((find_in_set(d.user_id, c.access_clients_list )>0 ) OR d.user_id=c.user_id) 						
						
						ORDER BY d.user_id,d.id,LENGTH(a.title), a.title
						";
						
		//echo $ongoingSuperClientsQuery;exit;
		    
        if($ongoingClientsAO=$this->getQuery($ongoingSuperClientsQuery,true))
        {
            
            return $ongoingClientsAO;

        }
        else
        	return NULL;
	}

	//get contract text from watchlist table
	function getContractText($watchlist_id,$user_id)
	{

		$query="SELECT contract_text,created_at FROM Watchlist where id='".$watchlist_id."' AND user_id='".$user_id."'";

		// $query;exit;

		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return NULL;       
        
	}
	
	 //Reference Article details w.r.t AO
    function OngoingBOUserArticleDetails($searchParameters=NULL)
	{
		$ongoingSuperClientsQuery="SELECT d.user_id,(SELECT count(pa.id) 
							FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 
							WHERE pa.article_id=a.id AND cycle=0) as totalParticipations,
						
						(SELECT count(cp.id) 
							FROM CorrectorParticipation cp INNER JOIN Article a2 ON a2.id=cp.article_id 
							WHERE cp.article_id=a.id AND cycle=0) as totalCorrectionParticipations,	

						(SELECT company_name FROM Client c1 where c1.user_id=d.user_id) as client,
						d.title as delivery_title,
						d.id as delivery_id,

						a.*,
						
						(SELECT login FROM User u where u.identifier=IF(d.created_by='BO',d.created_user,d.user_id))as incharge,
						(SELECT p.id FROM Participation p 
							WHERE p.status IN ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','published','plag_exec') AND p.cycle=0 AND  p.article_id=a.id LIMIT 1
						) as writerParticipation,
						
						(SELECT ex.id From Participation ex 
							WHERE ex.id=writerParticipation and (ex.status='time_out' OR (ex.status in ('bid','disapproved')))
						) as expiredWriterParticipation,	

						(SELECT cp.id FROM CorrectorParticipation cp 
							WHERE cp.status IN ('bid', 'under_study','disapproved_temp', 'disapproved','closed_temp', 'published','closed') AND cp.cycle=0 AND  cp.article_id=a.id LIMIT 1
						) as correctorParticipation,
							
						(SELECT ex1.id From CorrectorParticipation ex1 
							WHERE ex1.id=correctorParticipation and (ex1.status='time_out' OR (ex1.status in ('bid','disapproved')))
						) as expiredcorrectorParticipation,
						
						(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN Participation p ON p.user_id=up.user_id
							WHERE p.id=writerParticipation LIMIT 1) 
						as writerName,
						
						(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN CorrectorParticipation cp ON cp.corrector_id=up.user_id
							WHERE cp.id=correctorParticipation LIMIT 1) 
						as correctorName,
						d.publishtime,
						d.missiontest
						
						FROM Article a  
						INNER JOIN Delivery d ON d.id=a.delivery_id
						INNER JOIN DeliveryReference r
						
						WHERE r.id='".$searchParameters['ref_id']."' AND (find_in_set(d.user_id, r.access_client )>0 )			
						
						";
						
		//echo $ongoingSuperClientsQuery;exit;
		    
        if($ongoingClientsAO=$this->getQuery($ongoingSuperClientsQuery,true))
        {
            
            return $ongoingClientsAO;

        }
        else
        	return NULL;
	}

}
