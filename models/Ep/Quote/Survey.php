<?php
/**
 * Ep_Quote_Survey
 * @package Client
 * @version 1.0
 */
class Ep_Quote_Survey extends Ep_Db_Identifier
{
	protected $_name = 'Poll';
	
	/* Poll Questions for Creating poll */
	public function getPollquestions($id=null,$searchparameters=NULL)
    {
        $where="";
		if($id!="")
			$where = "WHERE id = '".$id."'";
			
		if($searchparameters['type'])
			$where = " WHERE type IN(".$searchparameters['type'].") AND status='active'";
			
		$QuesQuery="SELECT * FROM Poll_configuration ".$where." GROUP BY type order by type, id DESC";
		
		if(($Quesresult=$this->getQuery($QuesQuery,true))!=NULL)
			return $Quesresult;
		else
				return NUll;
    }
	
	/* Insert Poll */
	function insertPoll($save,$id=NULL)
	{
		if($id)
		{
			$where=" id='".$id."'";
			$this->updateQuery($save,$where);
			return $id;
		}
		else
		{
			$save['id'] = $this->getIdentifier();
			if($this->insertQuery($save))
				return $save['id']; 
			else
				return ""; 
		}
	}
	
	/* Insert Poll Questions */
	function insertPollQuestions($save,$id=NULL)
	{
		$this->_name = "Poll_question";
		if($id)
		{
			$where=" id='".$id."'";
			$this->updateQuery($save,$where);
			return $id;
		}
		else
		{	
			$save['id'] = $this->getIdentifier();

			if($this->insertQuery($save))
				return $save['id']; 
			else
				return "";
		}
	}
	
	/* Check and Get Poll associated to Mission */
	function getPoll($searchParametes=NULL)
	{
		$where = "";
		if($searchParametes['contract_mission_id'])
		$where = " AND p.contract_mission_id='".$searchParametes['contract_mission_id']."' ORDER BY pq.questionorder";
		
		if($searchParametes['survey_id'])
		$where = " AND p.id='".$searchParametes['survey_id']."' ORDER BY pq.questionorder";
		
		$query="SELECT p.title as ptitle,p.id as pid,p.*, pq.* 
				FROM Poll p 
				LEFT JOIN Poll_question pq ON pq.pollid = p.id 
			WHERE 1=1 ".$where;
		
		if(($Quesresult=$this->getQuery($query,true))!=NULL)
			return $Quesresult;
		else
			return NULL;
	}
	
	//survey participants in survey follow up
	function getSurveyPartcipants($survey_id,$where = NULL)
	{
		if($where)
		{
			$where = substr($where,3);
			$whereq = "poll_id='".$survey_id."' AND (". $where.")";
		}
		else
		$whereq = "poll_id='".$survey_id."'";
		
		$participantsQuery="Select pp.*,pp.id as participate_id ,u.first_name,u.last_name,us.email,CASE us.profile_type WHEN 'senior' THEN 'S' WHEN 'sub-junior' THEN 'J' WHEN 'junior' THEN 'D' ELSE '' END as profile_type
								From Poll_Participation pp
							INNER JOIN Poll p ON p.id=pp.poll_id 
							INNER JOIN User us ON us.identifier=pp.user_id
							INNER JOIN UserPlus u ON pp.user_id=u.user_id
							WHERE ".$whereq;	
	
		if(($participants=$this->getQuery($participantsQuery,true))!=NULL)
			return $participants;
		else
			return NULL;
	}	
	
	function getUserResponses($survey_id,$user_id)
	{
		$responseQuery="Select pr.*,pp.price_user,pq.type,pp.status
								From PollUserResponse pr
							INNER JOIN Poll_Participation pp ON pp.poll_id=pr.poll_id AND pp.user_id=pr.user_id
							INNER JOIN Poll_question pq ON pq.id=pr.question_id
							WHERE pr.poll_id='".$survey_id."' AND pr.user_id='".$user_id."'
							ORDER BY field(pq.type, 'price', 'bulk_price','timing') ";
		//echo $responseQuery;exit;					
	
		if(($responses=$this->getQuery($responseQuery,true))!=NULL)
			return $responses;
		else
			return NULL;
	
	}
	
	//get poll/mission/quote details on Survey id
	function getPollMissionQuoteDetails($survey_id)
	{
		$allDetailsQuery="SELECT p.*,p.id as poll_id,qm.*,qm.identifier as mission_id,q.*,q.identifier as quote_identifier,qc.contractname,
							c.company_name,c.ca_number,q.category as quote_category,
							q.created_at as quote_created,qm.created_at as quoteMissionCreated_at,cm.assigned_at,cm.updated_by,qm.created_by as missionCreated_by,
							cm.comment as contractMissionComments,qm.comments as quoteMissionComments
							FROM Poll p
							INNER JOIN ContractMissions cm ON cm.contractmissionid=p.contract_mission_id
							INNER JOIN QuoteContracts qc ON cm.contract_id=qc.quotecontractid
							INNER JOIN QuoteMissions qm ON qm.identifier=cm.type_id							
							INNER JOIN Quotes q ON q.identifier=qm.quote_id
							INNER JOIN Client c ON c.user_id=q.client_id
							WHERE p.id='".$survey_id."'
							Group By p.id
							";
		//echo $responseQuery;exit;					
	
		if(($allDetails=$this->getQuery($allDetailsQuery,true))!=NULL)
			return $allDetails;
		else
			return NULL;
	
	}
	//get SMIC value of survey
	public function getSMICvalue($survey_id)
	{
		$SMICQuery="SELECT l.SMIC,c.percentage FROM Poll p 
					INNER JOIN  LanguageSMIC l ON p.language=l.id 
					INNER JOIN  CategoryDifficultyPercent c ON p.category=c.id
				WHERE p.id='".$survey_id."'";
		
		if(($SMICresult=$this->getQuery($SMICQuery,true))!=NULL)
			return $SMICresult[0]['SMIC'] * ($SMICresult[0]['percentage']/100);
	}
	
	//get average price details
	function getAvgPriceSurvey($survey_id)
	{
		$avgQuery="SELECT AVG(price_user) as avg_price
					FROM Poll_Participation
					WHERE poll_id='".$survey_id."' AND status='active'
					GROUP By poll_id";
		//echo $avgQuery;exit;			
		if(($avgresult=$this->getQuery($avgQuery,true))!=NULL)			
			return	$avgresult[0]['avg_price'];
		else
			return 0;
	}
	//get average price details
	function getAvgBulkPriceSurvey($survey_id)
	{
		$avgBulkQuery="SELECT AVG(pr.response) as avg_bulk_price
					FROM PollUserResponse pr
					INNER JOIN Poll_Participation pp ON pp.poll_id=pr.poll_id
					INNER JOIN Poll_question pq ON pq.id=pr.question_id
					WHERE pp.poll_id='".$survey_id."' AND pp.status='active' AND pq.type='bulk_price'
					GROUP By pp.poll_id";
		//echo $avgBulkQuery;exit;			
		if(($avgBulkresult=$this->getQuery($avgBulkQuery,true))!=NULL)			
			return	$avgBulkresult[0]['avg_bulk_price'];
		else
			return 0;
	}
	//update survey participation
	function updateSurveyPartcipation($update,$id,$survey_id=false)
	{
		$this->_name = 'Poll_Participation';
		if($survey_id)
			$where="poll_id='".$id."'";
		else
		$where="id='".$id."'";
		$this->updateQuery($update,$where);
	}
	
	/* Get Polls List */
	function getPolls($search=NULL)
	{
		$where = "";
		if($search['cmid'])
		$where = " AND p.contract_mission_id='".$search['cmid']."'";
		$query = "SELECT qm.product,qm.product_type,qm.language_source,qm.language_dest,p.poll_date as count_down_end,p.title as ptitle,p.id as pollid,p.status as pstatus,qc.contractname,p.publish_time,p.closed_at FROM Poll p
					JOIN ContractMissions cm ON cm.contractmissionid=p.contract_mission_id
					JOIN QuoteContracts qc ON cm.contract_id=qc.quotecontractid
					JOIN QuoteMissions qm ON qm.identifier=cm.type_id							
					JOIN Quotes q ON q.identifier=qm.quote_id
					WHERE p.contract_mission_id IS NOT NULL $where";
		if(($result = $this->getQuery($query,true))!=NULL)
		return $result;
		else
		return null;
	}
	
	/* Get Survey Status to know the creation of Delivery */
	function getSurveyStatus($search=NULL)
	{
		if($search['cmid'])
		$where .= " AND contract_mission_id='".$search['cmid']."'";
		$query = "SELECT * FROM Poll 
				 WHERE status='closed' $where
				";
		if(($result = $this->getQuery($query,true))!=NULL)
		return $result;
		else
		return null;	
	}
	
	/* Insert in Adcomment to display in FO */
	function insertAdcomment($save)
	{
		$this->_name = "AdComments";
		$save['identifier'] = $this->getIdentifier();
		$this->insertQuery($save);
	}
}
?>