<?php
/**
 * Responsible for ongoing operations
 * @version 1.0
 */
	class Ep_Quote_Ongoing extends Ep_Db_Identifier
	{
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
						   sum(a.progressbar_percent) as progress,
						  
						   d.*,q.sales_suggested_currency,qm.product,qm.product_type,qm.language_source,qm.language_dest, 	(SELECT max(proofread_end) FROM Article  WHERE delivery_id=d.id) as proofread_end,
							((SELECT max(participation_time) FROM Article  WHERE delivery_id=d.id)+
							(SELECT max(selection_time) FROM Article  WHERE delivery_id=d.id )+
							(SELECT max(GREATEST(senior_time,junior_time,subjunior_time)) FROM Article  WHERE delivery_id=d.id )) as    total_time	

							$selectQuery

							FROM  Delivery d
							INNER JOIN Article a ON d.id=a.delivery_id
							INNER JOIN User u ON u.identifier=d.user_id
							LEFT  JOIN Client c ON u.identifier=c.user_id
							JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id
							JOIN QuoteMissions qm ON qm.identifier = cm.type_id
							JOIN QuoteContracts qc ON cm.contract_id = qc.quotecontractid
							JOIN Quotes q ON qc.quoteid = q.identifier
							"
							. (!empty($condition) ? ' WHERE 1=1 ' . $condition : '') . "
							
							Group BY d.id
							$haveQuery
							ORDER BY d.created_at DESC
							$limitCondition
							";

			//echo  $ongoingQuery;exit;
			if(($count=$this->getNbRows($ongoingQuery))>0)
			{
				$ongoingAO=$this->getQuery($ongoingQuery,true);
				return $ongoingAO;

			}
			else
				return NULL;


		}
		
	function getOngoingArticleDetails($searchParameters=NULL,$limit=NULL)
	{
		if($searchParameters['client_id'])
			$condition.=" AND d.user_id='".$searchParameters['client_id']."'";

		if($searchParameters['ao_id'])
			$condition.=" AND d.id='".$searchParameters['ao_id']."'";

		if($searchParameters['article_ids'])
			$condition.=" AND a.id IN(".$searchParameters['article_ids'].")";
			
		if($limit)
            $limitCondition=" limit 0,$limit";

        if($searchParameters['missiontest']=='yes')
        {	
        	$ongoingQuery="SELECT (SELECT count(pa.id) 
								FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 
								WHERE pa.article_id=a.id AND cycle=0) as totalParticipations,
								(SELECT count(pa.id) 
								FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 
								WHERE pa.article_id=a.id AND cycle=0 AND (pa.status IN('bid_premium','bid_temp','bid_refused','bid_refused_temp'))) as unselectedwriters,
							(SELECT count(pa.id) 
								FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 
								WHERE pa.article_id=a.id) as totalcycleParticipations,
							(SELECT count(cp.id) 
								FROM CorrectorParticipation cp INNER JOIN Article a2 ON a2.id=cp.article_id 
								WHERE cp.article_id=a.id AND cycle=0) as totalCorrectionParticipations,	
								(SELECT count(cp.id) 
								FROM CorrectorParticipation cp INNER JOIN Article a2 ON a2.id=cp.article_id 
								WHERE cp.article_id=a.id AND cycle=0 AND (cp.status IN('bid_corrector','bid_temp','bid_refused','bid_refused_temp'))) as unselectedcorrectors,
							(SELECT count(cp.id) 
								FROM CorrectorParticipation cp INNER JOIN Article a4 ON a4.id=cp.article_id 
								WHERE cp.article_id=a.id AND cp.cycle=0 AND cp.status='bid_refused') as totalRefusedCorrectionParticipations,	
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
							d.missiontest,d.AOtype,d.user_id as clientid,d.title as deliveryTitle,d.mailsubject,d.mailcontent,d.correctormailsubject,d.correctormailcontent,d.correction_type as dcorrection_type,
							 a.delivered
							
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
								(SELECT count(pa.id) 
								FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 
								WHERE pa.article_id=a.id AND cycle=0 AND (pa.status IN('bid_premium','bid_temp','bid_refused','bid_refused_temp'))) as unselectedwriters,
							(SELECT count(pa.id) 
								FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 
								WHERE pa.article_id=a.id) as totalcycleParticipations,
							(SELECT count(cp.id) 
								FROM CorrectorParticipation cp INNER JOIN Article a2 ON a2.id=cp.article_id 
								WHERE cp.article_id=a.id AND cycle=0) as totalCorrectionParticipations,	
							(SELECT count(cp.id) 
								FROM CorrectorParticipation cp INNER JOIN Article a2 ON a2.id=cp.article_id 
								WHERE cp.article_id=a.id AND cycle=0 AND (cp.status IN('bid_corrector','bid_temp','bid_refused','bid_refused_temp'))) as unselectedcorrectors,	
							(SELECT count(cp.id) 
								FROM CorrectorParticipation cp INNER JOIN Article a4 ON a4.id=cp.article_id 
								WHERE cp.article_id=a.id AND cp.cycle=0 AND cp.status='bid_refused') as totalRefusedCorrectionParticipations,	
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
							d.missiontest,d.AOtype,d.user_id as clientid,d.title as deliveryTitle,d.mailsubject,d.mailcontent,cm.writing,cm.proofreading,d.correctormailsubject,d.correctormailcontent,d.correction_type as dcorrection_type,d.id as did,
							a.delivered
							FROM Article a  
							INNER JOIN Delivery d ON d.id=a.delivery_id
							INNER JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id
							INNER JOIN User u ON u.identifier=d.user_id						
							WHERE 1=1 $condition						
							$haveQuery
							ORDER BY a.title ASC
							 $limitCondition
							";
		}				
		 /*   echo $ongoingQuery;
		  exit;  */
        if(($count=$this->getNbRows($ongoingQuery))>0)
        {
            $ongoingAO=$this->getQuery($ongoingQuery,true);
            return $ongoingAO;
        }
        else
        	return NULL;
	}
		
	function getOngoingArticleStencilDetails($searchParameters=NULL,$limit=NULL)
	{
		if($searchParameters['client_id'])
			$condition.=" AND d.user_id='".$searchParameters['client_id']."'";

		if($searchParameters['ao_id'])
			$condition.=" AND d.id='".$searchParameters['ao_id']."'";

		if($searchParameters['article_ids'])
			$condition.=" AND a.id IN(".$searchParameters['article_ids'].")";
			
		if($limit)
            $limitCondition=" limit 0,$limit";

      //  if($searchParameters['missiontest']=='yes')
        {	
        	$ongoingQuery="SELECT a.*,
							(SELECT p.status FROM Participation p 
								WHERE p.status IN ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','published','plag_exec') AND p.cycle=0 AND  p.article_id=a.id LIMIT 1
							) as writerParticipation,
							(SELECT p.id FROM Participation p 
								WHERE p.status IN ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','published','plag_exec') AND p.cycle=0 AND  p.article_id=a.id LIMIT 1
							) as writerParticipationId,
							d.publishtime,
							d.missiontest,d.AOtype,d.user_id as clientid,d.title as deliveryTitle,d.mailsubject,d.mailcontent,d.correctormailsubject,d.correctormailcontent,d.correction_type as dcorrection_type 
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
		 /*   echo $ongoingQuery;
		  exit;  */
        if(($count=$this->getNbRows($ongoingQuery))>0)
        {
            $ongoingAO=$this->getQuery($ongoingQuery,true);
            return $ongoingAO;
        }
        else
        	return NULL;
	}
		
	public function getLatestCorrectionArticle($artId)
    {
        $query = "SELECT id, stage, status, article_path, article_name, version FROM ArticleProcess
                            WHERE participate_id = (select p.id FROM Participation p
                            INNER JOIN Article a ON a.id = p.article_id WHERE a.id='" . $artId . "'
                            AND p.status NOT IN ('bid_refused','closed') AND p.cycle = 0 LIMIT 1) AND stage = 'corrector' AND status IS NULL AND version != 0
                            ORDER BY version DESC LIMIT 1";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function checkNewCommentsCount($tidentifier,$type,$user)
	{
		if($type=='article' OR $type=='correction')
			$typecondition=" c.type in ('article','correction')";
		else
			$typecondition=" c.type='".$type."'";

		$commentsQuery="select count(*) as commentcount from AdComments c 
						where c.active='yes' and $typecondition and c.type_identifier='".$tidentifier."' and c.user_id!='".$user."' order by created_at ASC";	
		
		//echo $commentsQuery;exit;
		if(($count=$this->getNbRows($commentsQuery))>0)
		{
			$commentsDetails=$this->getQuery($commentsQuery,true);
			return $commentsDetails[0]['commentcount'];
		}
		else
		{
			return 0;
		}
	
	}

	function checkCommentsCount($tidentifier,$type,$user)
	{
		if($type=='article' OR $type=='correction')
			$typecondition=" c.type in ('article','correction')";
		else
			$typecondition=" c.type='".$type."'";

		$commentsQuery="select count(*) as commentcount from AdComments c 
						where c.active='yes' and $typecondition and c.type_identifier='".$tidentifier."' and c.user_id='".$user."' order by created_at ASC";	
		
		//echo $commentsQuery;exit;
		if(($count=$this->getNbRows($commentsQuery))>0)
		{
			$commentsDetails=$this->getQuery($commentsQuery,true);
			return $commentsDetails[0]['commentcount'];
		}
		else
		{
			return 0;
		}
	}
	
	// TO fetch Histories
	 public function getAOHistory($params)
    {
        
        if($params['article_id'])
            $condition =" article_id='".$params['article_id']."'";

         $historyQuery="SELECT ah.action_at,ah.user_id,ah.action_sentence,ah.action,up.first_name,up.last_name,u.type
						FROM ArticleHistory ah                    
						JOIN User u ON u.identifier = ah.user_id
						LEFT JOIN UserPlus up ON up.user_id = u.identifier
						WHERE  $condition
						ORDER BY  action_at DESC";

        if(($count=$this->getNbRows($historyQuery))>0)
        {
            $aoHistory=$this->getQuery($historyQuery,true);
            return $aoHistory;
        }
        else
            return NULL;        
    }
	
	// To fetch Contributers
	public function getAllContribAO($type,$lang="")
	{
		$black=$this->getConfiguration('blacked_writers');
 		$where1="";
		if($black=='no')
			$where1=" AND blackstatus='no' ";
		if($type=='1')
			$where1.=" AND profile_type='senior' ";
        elseif($type=='2')
            $where1.=" AND profile_type='junior' ";
        elseif($type=='3')
            $where1.=" AND profile_type='sub-junior' ";
        elseif($type=='1,2')
            $where1.=" AND ( profile_type IN ('senior','junior') ) ";
        else
             $where1.=" AND ( profile_type IN ('senior','junior','sub-junior' ) )";
		
		if($lang)
		{
			$where1.=" AND c.language='".$lang."'";
		}
			 
		$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u 
		INNER JOIN UserPlus up ON u.identifier=up.user_id 
		JOIN Contributor c ON u.identifier=c.user_id
		where u.type='contributor' AND u.status='Active' ".$where1;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
    
    /*  ADDED BY NASEER ON 04.12.2015*/
	// To fetch Contributers if product is translator//
	public function getAllTranslatorContribAO($type,$lang="",$srclang,$srccheck)
	{
		$black=$this->getConfiguration('blacked_writers');
 		$where1="";
		if($black=='no')
			$where1=" AND blackstatus='no' ";
		
		if($type!=NULL && is_array($type))
		{
			$profile_type = $type;
				if(in_array('sc',$profile_type))
				$where .= " OR c.translator_type='senior'";
				if(in_array('jc',$profile_type))
				$where .= " OR c.translator_type='junior'";
			$where=substr($where,4);

			$where1.=" AND ($where)";
		}	

		if($lang)
		{
			$where1.=" AND c.language='".$lang."'";
		}

		if($srccheck=='yes')
		{	
			$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u
			INNER JOIN UserPlus up ON u.identifier=up.user_id
			JOIN Contributor c ON u.identifier=c.user_id
			where u.type='contributor' AND u.status='Active'  AND c.translator = 'yes'".$where1;
			$resultall = $this->getQuery($SelectallContrib,true);
			return $resultall;
		}
		else
		{
			$SelectallContrib="SELECT identifier,email,first_name,last_name,c.language_more FROM User u
			INNER JOIN UserPlus up ON u.identifier=up.user_id
			JOIN Contributor c ON u.identifier=c.user_id
			where u.type='contributor' AND u.status='Active'  AND c.translator = 'yes'".$where1;
			$resultall = $this->getQuery($SelectallContrib,true);
			$var=0;
				foreach($resultall as $writer)
				{
					$sourcearray=unserialize($writer['language_more']);
					if($sourcearray[$srclang]>=50)
					{
						$resultArray[$var]['identifier']=$writer['identifier'];
						$resultArray[$var]['email']=$writer['email'];
						$resultArray[$var]['first_name']=$writer['first_name'];
						$resultArray[$var]['last_name']=$writer['last_name'];
						$var++;
					}
				}
				return $resultArray;
		}
	}
	
	// To get Article Details
	public function getEditArticleDetails($artId)
	{
		$query = "select a.*,d.AOtype,d.user_id,d.correction_type,d.contract_mission_id,d.correction as correctionao,cm.assigned_to,cm.writing,cm.proofreading,d.correction_file,d.id as did,d.user_id as clientid,(SELECT count(id) FROM Participation WHERE article_id='".$artId."' AND cycle=0) as totalpart,(SELECT count(id) FROM CorrectorParticipation WHERE article_id='".$artId."' AND cycle=0) as totalcorrectpart,(SELECT count(id) FROM Participation WHERE article_id='".$artId."' AND cycle=0 AND (status='bid_premium' OR status='bid_temp' OR status='bid_refused' OR status='bid_refused_temp')) as unselectedwriters,(SELECT count(id) FROM CorrectorParticipation WHERE article_id='".$artId."' AND cycle=0 AND (status='bid_corrector' OR status='bid_temp' OR status='bid_refused' OR status='bid_refused_temp')) as unselectedcorrectors             
				  FROM Article a  INNER JOIN Delivery d ON a.delivery_id=d.id
				  JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id
                  WHERE a.id = '".$artId."' LIMIT 0,1";
      
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
	}
	
	 ////get the user price /////
    public function getUserPrice($artId)
    {
       $query = "select p.*, u.login, up.first_name, up.last_name
                    FROM Participation p
					INNER JOIN User u ON p.user_id=u.identifier
					LEFT JOIN  UserPlus up ON up.user_id=u.identifier WHERE p.article_id = '".$artId."' AND p.status IN ('bid','under_study','disapproved','disapproved_temp','closed_temp','published') and cycle=0";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	function getExtract()
	{
		$query = "SELECT u.identifier as uid,d.id as did,d.title as dtitle,qm.identifier as qmid,qm.product,qm.product_type,qm.language_source,qm.language_dest,a.title as atitle,u.email,a.id as aid,c.company_name,d.created_at,a.created_at as artcreatedate,a.bo_closed_status,a.id as artid,(SELECT p.id FROM Participation p WHERE p.status IN ('time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','published','plag_exec') AND p.cycle=0 AND  p.article_id=a.id LIMIT 1) as writerParticipation,(SELECT cp.id FROM CorrectorParticipation cp  WHERE cp.status IN ('under_study','disapproved_temp', 'disapproved','closed_temp', 'published') AND cp.cycle=0 AND  cp.article_id=a.id LIMIT 1) as correctorParticipation 
		FROM Delivery d JOIN Article a ON d.id = a.delivery_id 
		JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id
		JOIN QuoteMissions qm ON qm.identifier = cm.type_id
		JOIN Quotes q ON q.identifier = qm.quote_id
		JOIN User u ON u.identifier = q.client_id JOIN Client c ON c.user_id = u.identifier
		WHERE d.contract_mission_id IS NOT NULL AND (c.company_name LIKE '%_new%' OR u.email LIKE '%_new@%') ORDER BY u.identifier ASC,`qm`.`identifier` ASC,d.id ASC";
		if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return NULL;
	
	}
	
	function getExtract2()
	{
		$query = "SELECT (count(p.id)) as part_count,ROUND(sum(a.files_pack),2) as tot_pub_art,CONCAT(up.first_name,up.last_name) as owner,c.company_name as client,qc.contractname,qm.product,qm.product_type,qm.language_source,qm.language_dest,qm.unit_price,q.sales_suggested_currency,sum(a.price_final) as total_price,DATE_FORMAT(qc.expected_end_date,'%d-%m-%Y') as enddate,qm.margin_percentage,(sum(p.price_user)+IF(sum(cp.price_corrector)>0,sum(cp.price_corrector),0)) as production_cost,ROUND(sum(p.price_user),2) as writer_cost,ROUND(sum(cp.price_corrector),2) as proofreader_cost,qc.quotecontractid,qm.quote_id
		FROM Article a 
		JOIN Participation p ON p.article_id = a.id 
		LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
		JOIN Delivery d ON d.id = a.delivery_id
		JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id 
		JOIN QuoteContracts qc ON qc.quotecontractid = cm.contract_id
		JOIN Quotes q ON q.identifier = qc.quoteid
		JOIN QuoteMissions qm ON qm.quote_id = q.identifier
		JOIN User u ON u.identifier = cm.assigned_to
		JOIN UserPlus up ON up.user_id = u.identifier
		JOIN User u1 ON u1.identifier = q.client_id
		JOIN Client c ON c.user_id = u1.identifier
		WHERE p.status = 'published' AND d.missiontest = 'no' AND DATE(p.updated_at) >= '2015-06-01' AND DATE(p.updated_at) <= '2015-06-30' AND cm.type_id = qm.identifier
		GROUP BY cm.contractmissionid
		";
		if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return NULL;
	}
	
	function getSeoTurnover($quote_id)
	{
		$where = " include_final='yes' AND product IN('seo_audit','smo_audit')";
		$where .= " AND quote_id='".$quote_id."'";
		$query = "SELECT SUM(IF(`package`='team',turnover+(team_fee*team_packs),turnover)) as calcturnover
		FROM `QuoteMissions`  
		WHERE $where
		";
		$result = $this->getQuery($query,true);
		return $result[0]['calcturnover'];
	}
	
	function getExtract3()
	{
		 $query = "SELECT CONCAT(up.first_name,up.last_name) as owner,c.company_name as client,d.title as dtitle,a.title as atitle,qc.contractname,d.created_at,a.price_max,a.price_min,p.price_user,(SELECT concat(w.first_name,' ',w.last_name) FROM User writer LEFT JOIN UserPlus w ON w.user_id = writer.identifier WHERE writer.identifier = p.user_id) as writer_name,p.accept_refuse_at as writer_sdate,(SELECT article_sent_at FROM ArticleProcess ap WHERE ap.participate_id = p.id ORDER BY article_sent_at DESC LIMIT 1) as article_sent_at,IF(p.cycle>0,'Yes','No') as relaunch,IF(p.selection_type='bo','No','Yes') as stype,a.correction_pricemin,a.correction_pricemax,cp.price_corrector,(SELECT concat(pr.first_name,' ',pr.last_name) FROM User corrector LEFT JOIN UserPlus pr ON pr.user_id = corrector.identifier WHERE corrector.identifier = cp.corrector_id) as corrector_name,cp.accept_refuse_at as corrector_sdate,IF(cp.cycle>0,'Yes','No') as crelaunch,IF(cp.selection_type='bo','No','Yes') as sctype,cp.updated_at as corrector_article_sent_at,d.correction
		FROM Article a 
		JOIN Participation p ON p.article_id = a.id 
		LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
		JOIN Delivery d ON d.id = a.delivery_id
		JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id 
		JOIN QuoteContracts qc ON qc.quotecontractid = cm.contract_id
		JOIN Quotes q ON q.identifier = qc.quoteid
		JOIN QuoteMissions qm ON qm.quote_id = q.identifier
		JOIN User u ON u.identifier = cm.assigned_to
		JOIN UserPlus up ON up.user_id = u.identifier
		JOIN User u1 ON u1.identifier = q.client_id
		JOIN Client c ON c.user_id = u1.identifier
		WHERE p.status = 'published' AND (cm.assigned_to = '140808133749604' OR cm.assigned_to = '150121104838458') AND d.created_at >= now()-interval 3 month;
		"; 
		if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return NULL;
	}
	
	// To fetch Contributers
	public function getAllwritersAO($type,$lang="")
	{
		$black=$this->getConfiguration('blacked_writers');
 		$where1="";
		if($black=='no')
			$where1=" AND blackstatus='no' ";
		
		if($type!=NULL && is_array($type))
		{
			$profile_type = $type;
				if(in_array('sc',$profile_type))
				$where .= " OR u.profile_type='senior'";
				if(in_array('jc',$profile_type))
				$where .= " OR u.profile_type='junior'";
				if(in_array('jc0',$profile_type))
				$where .= " OR u.profile_type='sub-junior'";
			$where=substr($where,4);

			$where1.=" AND ($where)";
		}	
		
		if($lang)
			$where1.=" AND c.language='".$lang."'";
			 
		$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u 
		INNER JOIN UserPlus up ON u.identifier=up.user_id 
		JOIN Contributor c ON u.identifier=c.user_id
		where u.type='contributor' AND u.status='Active' ".$where1;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
	
}
	
?>