<?php
/**
 * Ep_Quote_TempoEmailsTrack
 * @author Arun
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_TempoEmailsTrack extends Ep_Db_Identifier
{
	protected $_name = 'TempoEmailsTrack';
	public $missionIdentifier;

	//Insert Quote data
	public function insertTemoEmail($data)
	{		
		$this->insertQuery($data);	
	}
	function getWeeklyEmails()
	{
		$monday =DATE("Y-m-d",strtotime( 'monday last week' ));
		$current_date =DATE("Y-m-d",strtotime( 'friday last week'));
		
		$query="SELECT contract_mission_id,count(*) as not_respected_count
				FROM TempoEmailsTrack
				WHERE DATE(created_at)>='$monday' AND DATE(created_at)<='$current_date'			
				GROUP BY contract_mission_id
				";
		//echo $query;
		//WHERE DATE(created_at)>=$monday AND DATE(created_at)<=$current_date

		if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return NULL;	
	}
	function getMissionDetails($contract_mission_id)
	{
		$query="SELECT qm.*,qc.contractname FROM QuoteMissions qm
				JOIN ContractMissions cm ON qm.identifier=cm.type_id
				JOIN QuoteContracts qc ON qc.quotecontractid=cm.contract_id
				WHERE cm.contractmissionid='".$contract_mission_id."'
				";
		//echo $query;	

		if(($result = $this->getQuery($query,true))!= NULL)
            return $result[0];
        else
            return NULL;
		
	}
}	