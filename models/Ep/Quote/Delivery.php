<?php
/**
 * Ep_Quote_Delivery
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_Delivery extends Ep_Db_Identifier
{
	protected $_name = 'Delivery';
	
	public function insertDelivery($delivery_array)
 	{
 		$delivery_array["id"] = $this->getIdentifier();
		
		$this->insertQuery($delivery_array);
		return $delivery_array["id"];
	}

	public function insertArticle($article_array)
 	{
 		$this->_name='Article';

 		$article_array["id"] = $this->getIdentifier();
		
		$this->insertQuery($article_array);
		return $article_array["id"];
	}

	public function updatePaidarticle($d_id,$invoiceId)
	{
		$this->_name='Article';

		$Where3= "delivery_id=".$d_id;
		
		$Part3Array=array();
		$Part3Array['paid_status']='paid';
		$Part3Array['invoice_id']=$invoiceId;
		
		$this->updateQuery($Part3Array,$Where3);
	}
	
	//get poll/mission/quote details on Survey id
	function getMissionQuoteDetails($mission_id)
	{
		$allDetailsQuery="SELECT qm.*,qm.identifier as mission_id,q.*,q.identifier as quote_identifier,qc.contractname,
							c.company_name,c.ca_number,q.category as quote_category,q.client_id,
							q.created_at as quote_created,qm.created_at as quoteMissionCreated_at,cm.assigned_at,cm.updated_by,qm.created_by as missionCreated_by,
							cm.comment as contractMissionComments,qm.comments as quoteMissionComments,
							cm.min_cost,cm.max_cost,cm.writing,qm.nb_words,cm.privatedelivery,cm.correction,cm.proofreading,qm.volume,cm.files_pack,
							qm.mission_length as mission_end_days,qc.expected_end_date,qc.quotecontractid as contract_id,qm.product_type,cm.currency,cm.assigned_by as prod_manager,cm.stencils_ebooker,
							cm.bnp_mission
							FROM ContractMissions cm
							INNER JOIN QuoteContracts qc ON cm.contract_id=qc.quotecontractid
							INNER JOIN QuoteMissions qm ON qm.identifier=cm.type_id							
							INNER JOIN Quotes q ON q.identifier=qm.quote_id
							INNER JOIN Client c ON c.user_id=q.client_id
							WHERE cm.contractmissionid='".$mission_id."'
							Group By cm.contractmissionid
							";
		//echo $allDetailsQuery;exit;
		
		if(($allDetails=$this->getQuery($allDetailsQuery,true))!=NULL)
			return $allDetails;
		else
			return NULL;
	
	}
	//all contributors
	function getContributorsList($searchParameters=NULL)
	{
		if($searchParameters['language']!=NULL)
			$condition.=" AND c.language='".$searchParameters['language']."'"; 

		if($searchParameters['profile_type']!=NULL && is_array($searchParameters['profile_type']))
		{
			$profile_type = $searchParameters['profile_type'];
				if(in_array('sc',$profile_type))
				$where .= " OR u.profile_type='senior'";
				if(in_array('jc',$profile_type))
				$where .= " OR u.profile_type='junior'";
				if(in_array('jc0',$profile_type))
				$where .= " OR u.profile_type='sub-junior'";
			$where=substr($where,4);

			$condition.=" AND ($where)";
		}		

		$SelectallContrib="SELECT identifier,email,first_name,last_name
							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
							where u.type='contributor' AND blackstatus='no'  AND u.status='Active'
							$condition
							";
		//echo $SelectallContrib;exit;					
							
		if(($resultall=$this->getQuery($SelectallContrib,true))!=NULL)
		{
			
			$writers=array();
			foreach($resultall as $writer)
			{
				$writers[$writer['identifier']]=utf8_encode($writer['email']." (".$writer['first_name']." ".$writer['last_name'].")");
			}		
			
			return $writers;
		}
		else
			return NULL;		
		
	}
	
	function getContributorsListPrivate($searchParameters=NULL)
	{
		if($searchParameters['language']!=NULL)
			$condition.=" AND c.language='".$searchParameters['language']."'"; 

		if($searchParameters['profile_type']!=NULL && is_array($searchParameters['profile_type']))
		{
			$profile_type = $searchParameters['profile_type'];
				if(in_array('sc',$profile_type))
				$where .= " OR u.profile_type='senior'";
				if(in_array('jc',$profile_type))
				$where .= " OR u.profile_type='junior'";
				if(in_array('jc0',$profile_type))
				$where .= " OR u.profile_type='sub-junior'";
			$where=substr($where,4);

			$condition.=" AND ($where)";
		}		

		$SelectallContrib="SELECT identifier,email,first_name,last_name
							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
							where u.type='contributor' AND blackstatus='no'  AND u.status='Active' AND u.last_visit > '2015-08-01%'
							$condition
							";
		//echo $SelectallContrib;exit;					
							
		if(($resultall=$this->getQuery($SelectallContrib,true))!=NULL)
		{
			
			$writers=array();
			foreach($resultall as $writer)
			{
				$writers[$writer['identifier']]=utf8_encode($writer['email']." (".$writer['first_name']." ".$writer['last_name'].")");
			}
			
			return $writers;
		}
		else
			return NULL;		
		
	}
	
	//all correctors
	function getCorrectorsList($searchParameters=NULL)
	{
		if($searchParameters['language']!=NULL)
			$condition.=" AND c.language='".$searchParameters['language']."'"; 


		if($searchParameters['profile_type2']!=NULL && is_array($searchParameters['profile_type2']))
		{
			$profile_type = $searchParameters['profile_type2'];
				if(in_array('sc',$profile_type) || in_array('CB',$profile_type) || in_array('CSC',$profile_type))
				$where .= " OR u.profile_type2='senior'";
				if(in_array('jc',$profile_type) || in_array('CB',$profile_type) || in_array('CJC',$profile_type))
				$where .= " OR u.profile_type2='junior'";				
			$where=substr($where,4);
			$condition.=" AND ($where)";
		}


		$SelectallCorrectors="SELECT identifier,email,first_name,last_name
							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
							where u.type='contributor' and u.type2='corrector' AND blackstatus='no'  AND u.status='Active'
							$condition
							";
		
		//echo $SelectallCorrectors;exit;

		if(($resultall=$this->getQuery($SelectallCorrectors,true))!=NULL)
		{
			
			$correctors=array();
			foreach($resultall as $corrector)
			{
				$correctors[$corrector['identifier']]=utf8_encode($corrector['email']." (".$corrector['first_name']." ".$corrector['last_name'].")");
			}			
			return $correctors;
		}
		else
			return NULL;		
	}
	// get articles
	public function getArticles($did)
	{
		$ArtQuery="SELECT * FROM Article WHERE delivery_id='".$did."' ORDER BY LENGTH(title), title ASC";
		
		if(($Artresult=$this->getQuery($ArtQuery,true))!=NULL)
			return $Artresult;
		else
			return NULL;
		
	}	

	// get delivery details
	public function getDeliveryDetails($did)
	{
		$deliveryQuery="SELECT * FROM Delivery WHERE id='".$did."'";		

		if(($deliveryDetails=$this->getQuery($deliveryQuery,true))!=NULL)
			return $deliveryDetails;
		else
			return NULL;
	}

	public function checkfbpost($client)
	{
		$today=date("Y-m-d");
		$tommorrow=date('Y-m-d', strtotime("+1 days"));
		$ChkQuery="SELECT * FROM Delivery WHERE ((publishtime IS NOT NULL AND FROM_UNIXTIME(publishtime, '%Y-%m-%d')='".$today."') OR (publishtime IS NULL AND DATE_FORMAT(created_at, '%Y-%m-%d')='".$today."')) AND postoftheday='yes' AND user_id='".$client."'";
		$Chkresult = $this->getQuery($ChkQuery,true);
		if(count($Chkresult)>0)
			return "yes";
		else
			return "no";
	}	
	//update Delivery
	public function updateDelivery($data,$identifier)
    {
        //print_r($data);exit;
      //  if($data['volume'])
        	//$condition=' AND volume=0';
    	$this->_name='Delivery';
		
		$where=" id='".$identifier."'";	
        $this->updateQuery($data,$where);
    }
	
	/* To show in Prod Followup */
	function getMissionDeliveries($search=array())
	{
		$condition = "";
		if($search['cmid'])
		$condition .= " AND d.contract_mission_id='".$search['cmid']."'";
		if($search['mission_test'])
		$condition .= " AND d.missiontest='".$search['mission_test']."'";
		$query = "SELECT d.*,DATE(d.created_at) as startdate,
				(SELECT max(proofread_end) FROM Article  WHERE delivery_id=d.id) as proofread_end,
				((SELECT max(participation_time) FROM Article  WHERE delivery_id=d.id)+
				(SELECT max(selection_time) FROM Article  WHERE delivery_id=d.id )+
				(SELECT max(GREATEST(senior_time,junior_time,subjunior_time)) FROM Article  WHERE delivery_id=d.id )) as    total_time,
				(SELECT COUNT(delivery_id) FROM Article WHERE delivery_id=d.id) as art_count,
				(SELECT COUNT(p.article_id) FROM Participation p JOIN Article a ON a.id = p.article_id WHERE a.delivery_id=d.id AND p.status = 'published') as published_count,
				IF ((SELECT COUNT(delivery_id) FROM Article WHERE delivery_id=d.id)= (SELECT COUNT(p.article_id) FROM Participation p JOIN Article a ON a.id = p.article_id WHERE a.delivery_id=d.id AND p.status = 'published'),
				(SELECT p.updated_at FROM Participation p JOIN Article a ON a.id = p.article_id WHERE a.delivery_id=d.id AND p.status = 'published' ORDER BY p.updated_at DESC LIMIT 1),'') as max_date,
				(SELECT max(delivered_updated_at) FROM Article  WHERE delivery_id=d.id) as max_delivered_updated_at,
				(
                    SELECT delivered
                    FROM Article
                    WHERE delivery_id = d.id
                    AND (
                    `delivered_updated_at` IS NULL
                    OR `delivered` != 'yes'
                    )
                    LIMIT 1
                ) AS delivered_test
				FROM Delivery d 
				WHERE 1=1 $condition
				ORDER BY d.published_at DESC";
		//echo $query;exit;
				
		if(($res=$this->getQuery($query,true))!=NULL)
			return $res;
		else
			return NULL;	
	}
	
	/* To check atleast one recruitment closed or not */
	function getRecruitmentStatus($search=NULL)
	{
		$condition = "recruitment_status='closed'";
		if($search['cmid'])
		$condition .= " AND d.contract_mission_id='".$search['cmid']."'";
		if($search['mission_test'])
		$condition .= " AND d.missiontest='".$search['mission_test']."'";
		$query = "SELECT count(id) as dcount FROM Delivery d 
				  WHERE $condition";
		return $this->getQuery($query,true);
	}

	//get profile type count for writers
	function getProfileTypeCountWriters($language)
	{
		if($language!=NULL)
			$condition.=" AND c.language='".$language."'";			

		$profilecountQuery="SELECT SUM(if(u.profile_type='senior',1,0)) as sc_count,SUM(if(u.profile_type='junior',1,0)) as jc_count,
							SUM(if(u.profile_type='sub-junior',1,0)) as jc0_count 							
 							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
						   WHERE u.type='contributor' AND u.blackstatus='no'  AND u.status='Active'
						  $condition
						  ";
		//echo $profilecountQuery;exit;					
							
		if(($profileCounts=$this->getQuery($profilecountQuery,true))!=NULL)
		{
			return $profileCounts[0];
		}
		else
			return NULL;
	}

	//get profile type count for correctors
	function getProfileTypeCountCorrectors($language)
	{
		if($language!=NULL)
			$condition.=" AND c.language='".$language."'";			

		$profilecountQuery="SELECT SUM(if(u.profile_type2='senior',1,0)) as sc_count,SUM(if(u.profile_type2='junior',1,0)) as jc_count						
 							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
						    WHERE u.type='contributor' and u.type2='corrector' AND blackstatus='no'  AND u.status='Active'
						  $condition
						  ";
		//echo $profilecountQuery;exit;					
							
		if(($profileCounts=$this->getQuery($profilecountQuery,true))!=NULL)
		{
			if(!$profileCounts[0]['sc_count'])
				$profileCounts[0]['sc_count']=0;

			if(!$profileCounts[0]['jc_count'])
				$profileCounts[0]['jc_count']=0;


			return $profileCounts[0];
		}
		else
			return NULL;
	}

	/* To get Stats of Deliveries with and without published */
	function getProdStats($search=NULL,$published=false)
	{
		if($search['cmid'])
		$condition = " AND d.contract_mission_id='".$search['cmid']."'";
		$group_by = $select = $join = "";
		
		if($published)
		{
			$select = ",(SUM(p.price_user)+IFNULL(cp.price_corrector,0)) as publishedprice";
			$join = " JOIN Participation p ON p.article_id = a.id
					  LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
				";
			$condition .= " AND p.status='published'";
			$group_by = 'GROUP BY d.id';
		}
		
		$query = "SELECT count(a.id) as total_art_old,sum(a.files_pack) as total_art,sum(a.price_final) as total_price,sum(a.price_max)+IFNULL(sum(a.correction_pricemax),0) as total_corrector_writer,d.files_pack $select
		FROM Delivery d 
		JOIN Article a ON a.delivery_id = d.id 
		$join
		WHERE d.missiontest='no' $condition $group_by";
		//echo $query;
		if(($res=$this->getQuery($query,true))!=NULL)
		{
			$total_art = $total_price = 0;
			if($published)
			{
				foreach($res as $row)
				{
					//$total_art += $row['total_art']*$row['files_pack'];
					$total_art += $row['total_art'];
					//$total_price += $row['total_price'];
					$total_price += $row['publishedprice'];
				}
			}
			else
			{
				foreach($res as $row)
				{
					//$total_art += $row['total_art']*$row['files_pack'];
					$total_art += $row['total_art'];
					//$total_price += $row['total_price'];
					$total_price += $row['total_corrector_writer'];
				}
			}
			$array[] = array('total_art'=>$total_art,'total_price'=>$total_price);
			return $array;
		}
		else
			return NULL;	
	}
	
	function getDeliveries()
	{
		$query = "SELECT sum(a.files_pack) as published_articles,d.contract_mission_id,qm.volume,qm.identifier FROM Delivery d 
		JOIN Article a ON a.delivery_id = d.id 
		JOIN Participation p ON p.article_id = a.id
		LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
		JOIN ContractMissions cm on cm.contractmissionid = d.contract_mission_id
		JOIN QuoteMissions qm ON qm.identifier = cm.type_id
		WHERE d.missiontest='no' AND d.contract_mission_id IS NOT NULL AND p.status='published' GROUP BY d.contract_mission_id";
		if(($res=$this->getQuery($query,true))!=NULL)
			return $res;
		else
			return NULL;	
	}
	
	//all translator
	function getTranslatorList($searchParameters=NULL)
	{
		if($searchParameters['language']!=NULL)
			$condition.=" AND c.language='".$searchParameters['language']."'"; 

		if($searchParameters['product']=='translation')
		{
			$condition.=" AND c.translator='yes'";
		}
		
		if($searchParameters['profile_type']!=NULL && is_array($searchParameters['profile_type']))
		{
			$profile_type = $searchParameters['profile_type'];
				if(in_array('sc',$profile_type))
				$where .= " OR c.translator_type='senior'";
				if(in_array('jc',$profile_type))
				$where .= " OR c.translator_type='junior'";
			$where=substr($where,4);

			$condition.=" AND ($where)";
		}		
		
		$SelectallContrib="SELECT identifier,email,first_name,last_name,language_more
							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
							where u.type='contributor' AND blackstatus='no'  AND u.status='Active'
							$condition
							";
		//echo $SelectallContrib;exit;					
							
		if(($resultall=$this->getQuery($SelectallContrib,true))!=NULL)
		{
			$writers=array();
			if($searchParameters['sourcelang_nocheck']=='yes')
			{
				foreach($resultall as $writer)
				{
					$writers[$writer['identifier']]=utf8_encode($writer['email']." (".$writer['first_name']." ".$writer['last_name'].")");
				}		
			}
			else
			{
				foreach($resultall as $writer)
				{
					$sourcearray=unserialize($writer['language_more']);
					if($sourcearray[$searchParameters['language_source']]>=50)
						$writers[$writer['identifier']]=utf8_encode($writer['email']." (".$writer['first_name']." ".$writer['last_name'].")");
				}	
			}
			return $writers;
		}
		else
			return NULL;		
		
	}
	
	//all translation correctors
	function getCorrectorsTranslationList($searchParameters=NULL)
	{
		if($searchParameters['language']!=NULL)
			$condition.=" AND c.language='".$searchParameters['language']."'"; 


		if($searchParameters['profile_type2']!=NULL && is_array($searchParameters['profile_type2']))
		{
			$profile_type = $searchParameters['profile_type2'];
				if(in_array('sc',$profile_type) || in_array('CB',$profile_type) || in_array('CSC',$profile_type))
				$where .= " OR u.profile_type2='senior'";
				if(in_array('jc',$profile_type) || in_array('CB',$profile_type) || in_array('CJC',$profile_type))
				$where .= " OR u.profile_type2='junior'";				
			$where=substr($where,4);
			$condition.=" AND ($where)";
		}

		if($searchParameters['product']=='translation')
		{
			$condition.=" AND c.translator='yes'";
		}
		$SelectallCorrectors="SELECT identifier,email,first_name,last_name,language_more
							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
							where u.type='contributor' and u.type2='corrector' AND blackstatus='no'  AND u.status='Active'
							$condition
							";
		
		//echo $SelectallCorrectors;//exit;

		if(($resultall=$this->getQuery($SelectallCorrectors,true))!=NULL)
		{
			$correctors=array();
			if($searchParameters['sourcelang_nocheck_correction']=='yes')
			{
				foreach($resultall as $corrector)
				{
					$correctors[$corrector['identifier']]=utf8_encode($corrector['email']." (".$corrector['first_name']." ".$corrector['last_name'].")");
				}
			}
			else
			{
				foreach($resultall as $corrector)
				{
					$sourcearray=unserialize($corrector['language_more']);
					if($sourcearray[$searchParameters['language_source']]>=50)
						$correctors[$corrector['identifier']]=utf8_encode($corrector['email']." (".$corrector['first_name']." ".$corrector['last_name'].")");
				}	
			}			
			return $correctors;
		}
		else
			return NULL;		
	}
	
	//get profile type count for translators
	function getProfileTypeCountTranslators($language,$srclang,$srccheck)
	{
		if($srccheck=='yes')
			{
				$profilecountQuery="SELECT SUM(if(c.translator_type='senior',1,0)) as sc_count,SUM(if(c.translator_type='junior',1,0)) as jc_count					
 							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
						   WHERE u.type='contributor' AND u.blackstatus='no'  AND u.status='Active' AND c.translator='yes' AND c.language='".$language."'";						 
				 $profileCounts=$this->getQuery($profilecountQuery,true);
				 return $profileCounts[0];	
			}
			else
			{
		
				$profilecountQuery="SELECT c.translator_type,c.language_more					
 							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
						   WHERE u.type='contributor' AND u.blackstatus='no'  AND u.status='Active' AND c.translator='yes' AND c.language='".$language."'";
				$profileCounts=$this->getQuery($profilecountQuery,true);
				
				$resultArray=array();
				$resultArray['sc_count']=0;
				$resultArray['jc_count']=0;
				foreach($profileCounts as $writer)
				{
					$sourcearray=unserialize($writer['language_more']);
					if($sourcearray[$srclang]>=50)
					{
						if($writer['translator_type']=='senior')
							$resultArray['sc_count']++;
						elseif($writer['translator_type']=='junior')
							$resultArray['jc_count']++;
					}
				}
				return $resultArray;					
			}
			
	}
	
	//get profile type count for translator correctors
	function getProfileTypeCountTranslatorCorrectors($language,$srclang,$srccheck)
	{ 
		if($srccheck=='yes')
			{
				$profilecountQuery="SELECT SUM(if(u.profile_type2='senior',1,0)) as sc_count,SUM(if(u.profile_type2='junior',1,0)) as jc_count					
 							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
						   WHERE u.type='contributor' AND u.type2='corrector' AND u.blackstatus='no'  AND u.status='Active' AND c.translator='yes' AND c.language='".$language."'";					 
				 $profileCounts=$this->getQuery($profilecountQuery,true);
				 return $profileCounts[0];	
			}
			else
			{
		
				$profilecountQuery="SELECT u.profile_type2,c.language_more					
 							FROM User u 
							INNER JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Contributor c ON u.identifier=c.user_id
						   WHERE u.type='contributor' AND u.type2='corrector' AND u.blackstatus='no'  AND u.status='Active' AND c.translator='yes' AND c.language='".$language."'";//echo $profilecountQuery;
				$profileCounts=$this->getQuery($profilecountQuery,true);
				
				$resultArray=array();
				$resultArray['sc_count']=0;
				$resultArray['jc_count']=0;
				foreach($profileCounts as $writer)
				{
					$sourcearray=unserialize($writer['language_more']);
					if($sourcearray[$srclang]>=50)
					{
						if($writer['profile_type2']=='senior')
							$resultArray['sc_count']++;
						elseif($writer['profile_type2']=='junior')
							$resultArray['jc_count']++;
					}
				}
				return $resultArray;					
			}
			
	}
	
}