<?php
/**
 * Ep_Quote_Recruitment
 * @package Quote
 * @version 1.0
 *recruitment_id is nothing but the Delivery id
 */
class Ep_Quote_Recruitment extends Ep_Db_Identifier
{
		protected $_name =  'Delivery';
		
	// get contract mission id
	public function getContractMissionId($recruitment_id)
	{
		$ArtQuery="SELECT contract_mission_id FROM Delivery WHERE id='".$recruitment_id."'";
		$Artresult = $this->getQuery($ArtQuery,true);
		return $Artresult[0]['contract_mission_id'];
	}
	public function updateRecruitmentPartcipation($data,$identifier)
	{		
		$this->_name='Participation';
		$where=" id='".$identifier."'";
        $this->updateQuery($data,$where);

        $this->_name='Delivery';
	}
	
	function getRecruitmentParticipations($recruitment_id,$where='')
	{
		if($where)
			{
				$where = substr($where,3);
				$whereq = " AND d.id='".$recruitment_id."' AND (". $where.")";
			}
			else
			$whereq = " AND d.id='".$recruitment_id."'";
			
				$query = "SELECT  d.title,rp.id as rpid,rp.articles_per_week,rp.price_user,rp.is_hired,rp.article_id,rp.status,rp.current_stage,
					    	rp.article_submit_expires,up.initial,up.first_name,up.last_name,u.identifier as user_id,u.email,
							CASE u.profile_type WHEN 'senior' THEN 'S' WHEN 'sub-junior' THEN 'D' WHEN 'junior' THEN 'J' ELSE '' END as profiletype,d.total_article,d.link_quiz,d.quiz,
							qp.num_correct,qp.num_total,qp.qualified,qp.percent,d.min_mark,
							(SELECT avg(marks) FROM ArticleProcess WHERE participate_id=rp.id AND marks IS NOT NULL) as marks
							FROM Participation as rp 
							INNER JOIN Article a ON a.id=rp.article_id
							INNER JOIN Delivery d ON d.id=a.delivery_id
							LEFT JOIN QuizParticipation as qp ON qp.user_id=rp.user_id AND qp.article_id=rp.article_id 
							INNER JOIN User as u ON u.identifier=rp.user_id 							
							INNER JOIN UserPlus as up ON up.user_id=u.identifier 
							WHERE rp.status not in ('bid_pending') ". $whereq." 
							ORDER BY rp.created_at";
							;
			//echo $query;exit;
			if(($result = $this->getQuery($query,true))!=NULL)
			{
				return $result;
			}
			else
				return NULL;
	
	
	}
	//getting hired participants
	function getHiredParticipants($contract_mission_id)
	{
		$query="SELECT p.*,u.email,up.first_name,up.last_name,u.identifier 
			FROM
				Participation p
			INNER JOIN Article a ON a.id=p.article_id
			INNER JOIN Delivery d ON d.id=a.delivery_id	
			INNER JOIN User u ON u.identifier=p.user_id	
			INNER JOIN UserPlus up ON up.user_id=u.identifier	
			WHERE d.contract_mission_id='".$contract_mission_id."' AND p.is_hired='yes'
			";

		if(($hiredUsers=$this->getQuery($query,true))!=NULL)
			return $hiredUsers;
		else
			return NULL;	
	}


	//get poll/mission/quote details on Survey id
	function getRecruitmentQuoteDetails($mission_id,$recruitment_id)
	{
		$allDetailsQuery="SELECT d.title as recruitment_title,qm.*,qm.identifier as mission_id,q.*,q.identifier as quote_identifier,qc.contractname,
							c.company_name,c.ca_number,q.category as quote_category,q.client_id,
							q.created_at as quote_created,qm.created_at as quoteMissionCreated_at,cm.assigned_at,cm.updated_by,qm.created_by as missionCreated_by,
							cm.comment as contractMissionComments,qm.comments as quoteMissionComments,
							qm.volume,cm.files_pack,d.correction,
							qm.mission_length as mission_end_days,qc.expected_end_date,
							d.published_at as recruitment_launch,d.total_article,d.missiontest,d.num_hire_writers,d.recruitment_status,
							(SELECT max(proofread_end) FROM Article  WHERE delivery_id=d.id) as proofread_end,
							(SELECT max(participation_expires) FROM Article  WHERE delivery_id=d.id) as max_participation_time,
							(
								(SELECT max(participation_time) FROM Article  WHERE delivery_id=d.id)+
								(SELECT max(selection_time) FROM Article  WHERE delivery_id=d.id )+
								(SELECT max(GREATEST(senior_time,junior_time,subjunior_time)) FROM Article  WHERE delivery_id=d.id )
							) as  total_time,
							(SELECT max(pa.article_submit_expires) FROM Participation pa INNER JOIN  Article a1  ON pa.article_id=a1.id WHERE a1.delivery_id=d.id) as max_submit_time,d.stoprecruitment
							FROM ContractMissions cm
							INNER JOIN QuoteContracts qc ON cm.contract_id=qc.quotecontractid
							INNER JOIN QuoteMissions qm ON qm.identifier=cm.type_id							
							INNER JOIN Quotes q ON q.identifier=qm.quote_id
							INNER JOIN Client c ON c.user_id=q.client_id
							INNER JOIN Delivery d ON cm.contractmissionid=d.contract_mission_id 
							WHERE cm.contractmissionid='".$mission_id."' AND d.id='".$recruitment_id."'
							Group By cm.contractmissionid
							";
		//echo $allDetailsQuery;exit;
	
		if(($allDetails=$this->getQuery($allDetailsQuery,true))!=NULL)
			return $allDetails;
		else
			return NULL;
	
	}
	
	public function stopRecruitment($identifier,$act)
	{		
		/*$this->_name='Article';
		$where=" delivery_id='".$identifier."'";
		$Artdata=array();
		$Artdata['participation_expires']=strtotime('now');
        $this->updateQuery($Artdata,$where);

        $this->_name='Delivery';*/
		
		$where=" id='".$identifier."'";
		$Deldata=array();
		$Deldata['stoprecruitment']=$act;
        $this->updateQuery($Deldata,$where);
	}
	
	// to check participation expires// added by kavitha
	public function CheckParticipationExpired($delivery)
	{
		$ArticleQuery="SELECT count(*) as artcount FROM `Article` WHERE delivery_id= '".$delivery."' AND participation_expires >= UNIX_TIMESTAMP()";
		$ArticleDetails=$this->getQuery($ArticleQuery,true);
		if($ArticleDetails[0]['artcount']>0)
			return "YES";
		else
			return "NO";
		
	}
	
	public function getRecruitmentDetail($rec)
	{
		$ArticleQuery="SELECT * FROM `Delivery` WHERE id= '".$rec."'"; 
		$ArticleDetails=$this->getQuery($ArticleQuery,true);
		return $ArticleDetails[0]['title'];
	}
}
?>