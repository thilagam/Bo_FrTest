<?php
class Ep_DeliveryDashboard_MissionList extends Ep_Db_Identifier{

	public function getClintInContract(){
	
		$clientQuery = "SELECT C.user_id AS clientId,trim(C.company_name) AS clientName 
	                	FROM Client AS C 
						INNER JOIN Quotes AS Q ON C.user_id = Q.client_id
						INNER JOIN QuoteContracts AS QC ON Q.identifier  = QC.quoteid
				   		ORDER BY clientName ASC";
			if($clientResult = $this->getQuery($clientQuery,true))
			{
				return $clientResult;		
			}
			else{
				return NULL;
			}
	}

	public function getquotesInContract($searchParameters=array())
	{
		$condition='';
			if($searchParameters['client_id'])
			{
				$condition.=" AND Q.client_id ='".$searchParameters['client_id']."'";
			}
	
		$quoteTitleQuery = "SELECT Q.identifier AS QuoteId,Q.title as QuoteTitle 
	                    	FROM Quotes AS Q 
							INNER JOIN QuoteContracts AS QC ON Q.identifier  = QC.quoteid
							WHERE 1=1  ".$condition."
							ORDER BY Q.created_at DESC";
			if($quoteTitleResult = $this->getQuery($quoteTitleQuery,true))
			{
				return $quoteTitleResult;
			}
			else{
				return NULL;
			}
	}
	
	public function serachProSeoMission($client_id,$contract_id)
	{
	
	/*$proMissionQuery ="SELECT Q.*,QM.*,PM.* 
	                 FROM Quotes AS Q 
					 INNER JOIN QuoteMissions AS QM ON Q.identifier = QM.quote_id
					 INNER JOIN ProdMissions AS PM ON QM.identifier  = PM.quote_mission_id
					 WHERE Q.identifier =".$contract_id." AND Q.client_id =".$client_id." ";
	$promissionResult = $this->getQuery($proMissionQuery,true);
	return $promissionResult;*/
	
		$proMissionQuery ="SELECT QM.* 
	                 	   FROM Quotes AS Q 
					 	   INNER JOIN QuoteMissions AS QM ON Q.identifier = QM.quote_id
						   WHERE Q.identifier =".$contract_id." AND Q.client_id =".$client_id." ";
	
		if($promissionResult = $this->getQuery($proMissionQuery,true))
		{
			return $promissionResult;
		}
		else{	
			return NULL;
		}
	}
	
	public function serachTechMission($client_id,$contract_id)
	{
	
	/*$techMissionQuery ="SELECT Q.*,TM.*
	                 FROM Quotes AS Q 
					 INNER JOIN TechMissions AS TM ON Q.techmissions_assigned = TM.identifier
					 WHERE Q.identifier =".$contract_id." AND Q.client_id =".$client_id." ";
	$techmissionResult = $this->getQuery($techMissionQuery,true);
	return $techmissionResult;*/
	$techMissionQuery ="SELECT TM.*
	                 	FROM Quotes AS Q 
					 	INNER JOIN TechMissions AS TM ON Q.techmissions_assigned = TM.identifier
						WHERE Q.identifier =".$contract_id." AND Q.client_id =".$client_id." ";
	
		if($techmissionResult = $this->getQuery($techMissionQuery,true))
		{
			return $techmissionResult;
		}
		else{	
			return NULL;
		}
	}

}
?>