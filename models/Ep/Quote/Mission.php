<?php
/**
 * Ep_Quote_Mission
 * @author Arun
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_Mission extends Ep_Db_Identifier
{
	protected $_name = 'Missions_archieve';

	function getMissionDetails($searchParameters=NULL,$limit=NULL)
	{
		if($searchParameters['mission_id'])
			$condition.=" AND m.id='".$searchParameters['mission_id']."'";

		$missionQuery="SELECT m.*,c.company_name,ec.turnover as contract_turnover,ec.turnover_currency
							FROM Missions_archieve m  
							INNER JOIN Ep_contract ec ON ec.id=m.contract_id
							LEFT JOIN Client c ON c.user_id=ec.client_id
							WHERE 1=1 $condition							
							ORDER BY m.starting_date DESC LIMIT 0,3							
							";

		//echo $missionQuery."<br>";exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           if($searchParameters['mission_id'])
           {
           		$missionDetails=$this->getQuery($missionQuery,true);
           		return $missionDetails;
           }
           else
           {
	          $final_array=array();
	          foreach($searchParameters as $parameter=>$value)
				{					
					$QueryParameters[$parameter]=$value;					
					$resutls=$this->getMissionSelection1($QueryParameters,3);
					if(count($resutls)==0)
					{					
						unset($QueryParameters[$parameter]);
						$resutls=$this->getMissionSelection1($QueryParameters,3);						
						break;
						
					}
					$missionDetails=$resutls;
					array_push($final_array,$resutls);

					
					
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
				 $missionDetails=array_reverse($missionDetails);
				 $missionDetails = array_map("unserialize", array_unique(array_map("serialize", $missionDetails)));
				 $missionDetails=array_values($missionDetails);
		       	 //echo "<pre>";print_r($missionDetails);exit;

		       	return  array_slice($missionDetails, 0, 3);			
	       	}	     
	       	
		}
		else
        	return NULL;
	}	

	//executing if getting more than 3 result in above query
	function getMissionSelection1($searchParameters=NULL,$limit=NULL)
	{
		if($searchParameters['language'])
			$condition.=" AND m.language1='".$searchParameters['language']."'";

		if($searchParameters['product']=='redaction' || $searchParameters['product']=='autre')
			$condition.=" AND m.type='".$searchParameters['product']."'";
		else if($searchParameters['product']=='translation')
		{
			$condition.=" AND m.type='".$searchParameters['product']."'";
			if($searchParameters['languagedest'])
			$condition.=" AND m.language2='".$searchParameters['languagedest']."'";
		}

		if($searchParameters['producttype'])
			$condition.=" AND m.type_of_article='".$searchParameters['producttype']."'";
		
		
		if($searchParameters['volume'])
		{
			$volume_min=$searchParameters['volume']*0.8;
			$volume_max=$searchParameters['volume']*1.2;

			$volume_cnd="m.num_of_articles >= $volume_min AND m.num_of_articles <= $volume_max";
			$haveQuery.=($haveQuery)? ' AND '.$volume_cnd : " Having $volume_cnd";
			$orderQuery=" m.num_of_articles ASC";
				
		}
		if($searchParameters['nb_words'])
		{
			$words_min=$searchParameters['nb_words']*0.8;
			$words_max=$searchParameters['nb_words']*1.2;

			$words_cnd="m.article_length >= $words_min AND m.article_length <= $words_max";
			$haveQuery.=($haveQuery)? ' AND '.$words_cnd : " Having $words_cnd";
			$orderQuery=" m.article_length ASC";
				
		}

		$orderQuery=($orderQuery)? $orderQuery : " m.starting_date ASC" ;

		if($limit)
            $limitCondition=" limit 0,$limit";

        $mission1_Query="SELECT m.*,c.company_name,ec.turnover as contract_turnover,ec.turnover_currency
							FROM Missions_archieve m  
							INNER JOIN Ep_contract ec ON ec.id=m.contract_id
							LEFT JOIN Client c ON c.user_id=ec.client_id
							WHERE 1=1 $condition						
							$haveQuery
							ORDER BY $orderQuery
							 $limitCondition
							";
		//echo $mission1_Query."<br>";//exit; 					

		if(($count=$this->getNbRows($mission1_Query))>0)
		{
			$missionDetails=$this->getQuery($mission1_Query,true);
			return $missionDetails;
		}
		else
        	return NULL;

	}


}	