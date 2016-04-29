<?php
/**
* Ep_Quote_HistoryQuoteMissions
* @author Arun
* @package Quote
* @version 1.0
*/

class Ep_Quote_HistoryQuoteMissions extends Ep_Db_Identifier
{
	function getMissionDetails($searchParameters=NULL,$limit=NULL)
	{
		//echo "<prE>";print_r($searchParameters);
		if($searchParameters['language_source'])
			$condition.=" AND qm.language_source='".$searchParameters['language_source']."'";

		if($searchParameters['product']=='redaction' || $searchParameters['product']=='autre')
			$condition.=" AND qm.product='".$searchParameters['product']."'";
		else if($searchParameters['product']=='translation')
		{
			$condition.=" AND qm.product='".$searchParameters['product']."'";
			if($searchParameters['language_dest'])
			$condition.=" AND qm.language_dest='".$searchParameters['language_dest']."'";
		}
		if($searchParameters['product_type'])
			$condition.=" AND qm.product_type='".$searchParameters['product_type']."'";
		
		/* if($searchParameters['nb_words'])
		{
			$words_min=$searchParameters['nb_words']*0.8;
			$words_max=$searchParameters['nb_words']*1.2;

			$words_cnd="qm.nb_words >= $words_min AND qm.nb_words <= $words_max";
			$haveQuery.=($haveQuery)? ' AND '.$words_cnd : " Having $words_cnd";			
				
		} */		
		if($searchParameters['mission_id'])
			$condition.=" AND qm.identifier='".$searchParameters['mission_id']."'";
		
		if($searchParameters['mcurrency'])
			$condition.=" AND cm.currency='".$searchParameters['mcurrency']."'";
		
		if($searchParameters['selected_mission'])
			$orderCondition.=" qm.identifier='".$searchParameters['selected_mission']."' DESC,";
		
		if($searchParameters['client_id'])
			$orderCondition.=" q.client_id='".$searchParameters['client_id']."' DESC,";
		
		
		if($limit)
            $limitCondition=" LIMIT 0,$limit";
		
		$historyQuery="SELECT qm.*,qm.identifier as mission_id,qm.quote_id,qm.product,qm.product_type,qm.language_source,
					   qm.language_dest,qm.nb_words,qm.volume,qm.unit_price,qm.margin_percentage,
					   qm.internal_cost,qm.mission_length,qm.mission_length_option,q.signed_at,q.title,
					   qc.signaturedate,qc.quotecontractid,qc.contractname,d.contract_mission_id,
					   FORMAT(((SUM(p.price_user)+SUM(IFNULL(cp.price_corrector,0)))/sum(a.files_pack)),2)as real_cost,					   FORMAT((((SUM(p.price_user)+SUM(IFNULL(cp.price_corrector,0)))/sum(a.files_pack))/(1-margin_percentage/100)),2) as real_unit_price,
					   cm.currency,q.client_id,sum(a.files_pack) as published_articles,'fr' as from_site
				FROM Delivery d 
				JOIN Article a ON a.delivery_id = d.id 
				JOIN Participation p ON p.article_id = a.id
				LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
				JOIN ContractMissions cm ON cm.contractmissionid=d.contract_mission_id
				JOIN QuoteContracts qc ON cm.contract_id=qc.quotecontractid AND qc.status='validated'
				JOIN QuoteMissions  qm ON qm.identifier=cm.type_id
				JOIN Quotes q ON q.identifier=qm.quote_id AND q.sales_review='signed'						
				WHERE
					d.missiontest='no' AND p.status='published' AND d.contract_mission_id IS NOT NULL AND cm.history_bo='yes'
					$condition
				GROUP BY
					d.contract_mission_id
				$haveQuery	
				ORDER BY $orderCondition
					qc.signaturedate DESC
				$limitCondition
				";
		//echo $historyQuery."<br>";	//exit;
		if(	$missionDetails=$this->getQuery($historyQuery,true))
		{			 
			if($missionDetails==$limit)
			{
				return $missionDetails;
			}			
			else
			{
				$final_array=array();
				array_push($final_array,$missionDetails);
				//$final_array=$missionDetails;			
				$results=$this->getMissionSelection1($searchParameters,$limit,$missionDetails);
				
				
				if(count($resutls)==0)
				{
					unset($searchParameters['nb_words']);
					$results=$this->getMissionSelection1($searchParameters,$limit,$missionDetails);	
				}		
				
				if($results)
				{	
					array_push($final_array,$results);
				}				
				//Added to show always 3 missions  	
				$missionDetails =array();
				foreach ($final_array as $val)
				{
					foreach($val as $val2)
					{
						$missionDetails[] = $val2;
					}
				}
				//$missionDetails=array_reverse($missionDetails);
				$missionDetails = array_map("unserialize", array_unique(array_map("serialize", $missionDetails)));
				$missionDetails=array_values($missionDetails);	
				
				//echo "<pre>";print_r($missionDetails);//exit;
				//echo "<pre>";print_r(array_unique(array_unique(array_map("serialize", $missionDetails))));//exit;
				 
				if(count($missionDetails)>0)
					return  $missionDetails;		
				else 
					return NULL;
			}
		}
		else{
			$missionDetails=$this->getMissionSelection1($searchParameters,$limit);
			return $missionDetails;
		}
	}
	//executing if getting less  than 3 result in above query
	function getMissionSelection1($searchParameters=NULL,$limit=NULL,$missionDetails=NULL)
	{
		if($missionDetails)
		{
			$hmission_ids=array();
			foreach($missionDetails as $hmission)
			{
				$hmission_ids[]=$hmission['mission_id'];
			}
			$hmission_ids="'".implode("','",$hmission_ids)."'";
			$condition.=" AND qm.identifier NOT IN ($hmission_ids)";
		}
		
		
		
		if($searchParameters['language_source'])
			$condition.=" AND qm.language_source='".$searchParameters['language_source']."'";

		if($searchParameters['product']=='redaction' || $searchParameters['product']=='autre')
			$condition.=" AND qm.product='".$searchParameters['product']."'";
		else if($searchParameters['product']=='translation')
		{
			$condition.=" AND qm.product='".$searchParameters['product']."'";
			if($searchParameters['language_dest'])
			$condition.=" AND qm.language_dest='".$searchParameters['language_dest']."'";
		}
		if($searchParameters['product_type'])
			$condition.=" AND qm.product_type='".$searchParameters['product_type']."'";
		
		if($searchParameters['mission_id'])
			$condition.=" AND qm.identifier='".$searchParameters['mission_id']."'";
		
		if($searchParameters['selected_mission'])
			$orderCondition.=" qm.identifier='".$searchParameters['selected_mission']."' DESC,";
		
		if($searchParameters['client_id'])
			$orderCondition.=" q.client_id='".$searchParameters['client_id']."' DESC,";
		
		/* if($searchParameters['nb_words'])
		{
			$words_min=$searchParameters['nb_words']*0.8;
			$words_max=$searchParameters['nb_words']*1.2;

			$words_cnd="qm.nb_words >= $words_min AND qm.nb_words <= $words_max";
			$haveQuery.=($haveQuery)? ' AND '.$words_cnd : " Having $words_cnd";			
				
		} */
		if($searchParameters['mcurrency'])
			$condition.=" AND q.sales_suggested_currency='".$searchParameters['mcurrency']."'";
		
		if($limit)
            $limitCondition=" LIMIT 0,$limit";

			$mission1_Query="SELECT qm.*,qm.identifier as mission_id,qm.quote_id,qm.product,qm.language_source,
							qm.language_dest,
						    qm.nb_words,qm.volume,qm.unit_price,qm.margin_percentage,qm.internal_cost,
						    qm.mission_length,qm.mission_length_option,q.signed_at,q.title,
							q.sales_suggested_currency as currency,q.client_id,(qm.volume) as published_articles,
							'fr' as from_site
						FROM 
					        QuoteMissions  qm
						JOIN Quotes q ON q.identifier=qm.quote_id
					    WHERE
							q.sales_review='signed' AND qm.include_final='yes'
							$condition
						GROUP BY mission_id
						$haveQuery
						ORDER BY $orderCondition
							q.signed_at DESC				
						$limitCondition
							";
		//echo $mission1_Query."<br>";exit;

		if(($count=$this->getNbRows($mission1_Query))>0)
		{
			$missionDetails=$this->getQuery($mission1_Query,true);
			return $missionDetails;
		}
		else
        	return NULL;

	}


	function getTechMissionDetails($searchParameters=NULL,$limit=NULL)
	{

		if($searchParameters['include_final'])
			$condition.=" AND t.include_final='".$searchParameters['include_final']."'";


		$missionQuery="SELECT t.*
							FROM TechMissions t							
							WHERE 1=1 $condition							
							ORDER BY t.identifier ASC LIMIT $limit
							";

		//echo $missionQuery;exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;

	}
	/* getting all  Package Staffing Configurations*/
	function getPackageStaffingConfig()
	{
		$packageQuery="SELECT * FROM PackageStaffingConfig";
		
		if(($count=$this->getNbRows($packageQuery))>0)
        {           
           $configDetails=$this->getQuery($packageQuery,true);
		   $packageConfiguration=array();
		   
		   foreach($configDetails as $config)
		   {
			   $packageConfiguration[$config['language']]=array('lead'=>$config['lead'],'team'=>$config['team']);
		   }
       		return $packageConfiguration;
		}
		else
        	return NULL;
	}
	
}	