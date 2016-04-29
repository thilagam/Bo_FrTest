<?php
/**
 * Ep_Quote_Recruitment
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_Recruitment extends Ep_Db_Identifier
{
		protected $_name =  'Recruitment';
		
		/* Insert Update Recruitment */
		function insertupdaterecruitement($save,$id="")
		{
			$this->_name =  'Recruitment';
			if($id)
			{
				$where=" recruitment_id='".$id."'";
				$this->updateQuery($save,$where);
				return $id;
			}
			else
			{
				$save['recruitment_id'] = $this->getIdentifier();
				$this->insertQuery($save);
				return $save['recruitment_id'];
			}
		}
		
		/* get Recruitment */
		function getRecruitment($id)
		{
			$query = "SELECT * from Recruitment WHERE recruitment_id='".$id."'";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
		
		/* get Recruitment based on contract mission id */
		function getRecruitmentContractMission($id)
		{
			$query = "SELECT * from Recruitment WHERE contract_mission_id='".$id."'";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
		
		/* Fetch Partcipation Details */
		function getRecruitmentParticipations($recruitment_id,$where='')
		{
			if($where)
			{
				$where = substr($where,3);
				$whereq = "r.recruitment_id='".$recruitment_id."' AND (". $where.")";
			}
			else
			$whereq = "r.recruitment_id='".$recruitment_id."'";
			
			$query = "SELECT  rp.identifier as rpid,rp.articles_per_week,rp.proposed_cost,rp.is_hired,rp.article_path,rp.article_submit_expires,rp.article_name,up.initial,up.first_name,up.last_name,u.identifier as user_id,CASE u.profile_type WHEN 'senior' THEN 'S' WHEN 'sub-junior' THEN 'J' WHEN 'junior' THEN 'D' ELSE '' END as profiletype,r.no_contrib,r.is_quiz,r.is_test_article,qp.num_correct,qp.num_total,qp.qualified,qp.percent FROM RecruitmentParticipation as rp 
			JOIN Recruitment r ON r.recruitment_id=rp.recruitment_id 
			LEFT JOIN QuizParticipation as qp ON qp.id=rp.quiz_partcipation_id 
			JOIN User as u ON u.identifier=rp.user_id LEFT JOIN UserPlus as up ON up.user_id=u.identifier WHERE ". $whereq;
			if(($result = $this->getQuery($query,true))!=NULL)
			{
				return $result;
			}
			else
				return NULL;
		}
		
		/* Get count of Hired and to be hired */
		function getrecruitmentcounts($rid)
		{
			$query = "SELECT count(is_hired) as count FROM RecruitmentParticipation WHERE recruitment_id='".$rid."' AND is_hired='yes'";
			return $this->getQuery($query,true);
		}
		
		/* Hiring Contributors */
		function updateRecruitmentPartcipation($update,$id,$rid=false)
		{
			$this->_name =  'RecruitmentParticipation';
			if($rid)
			$where="recruitment_id='".$id."'";
			else
			$where="identifier='".$id."'";
			$this->updateQuery($update,$where);
		}
		
		/* Get Recuruitments */
		function getRecruitments()
		{
			$query = "SELECT r.status as rstatus,qm.product,qm.product_type,qm.language_source,qm.language_dest,r.count_down_end,r.is_quiz,r.is_test_article,r.max_cost_art,r.recruitment_id,qc.contractname,q.sales_suggested_currency 
			FROM Recruitment r 
			JOIN ContractMissions cm ON r.contract_mission_id=cm.contractmissionid 
			JOIN QuoteContracts qc ON qc.quotecontractid=cm.contract_id 
			JOIN QuoteMissions qm ON qm.identifier=cm.type_id
			JOIN Quotes q ON q.identifier=qm.quote_id
			";
			if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
			else
			return null;
		}
		/* get hired recruitment participants based on mission id*/
		function getHiredParticipants($contract_mission_id)
		{
			$hiredQuery="SELECT  rp.* 
							FROM RecruitmentParticipation rp
							INNER JOIN Recruitment r ON r.recruitment_id=rp.recruitment_id
							WHERE r.contract_mission_id='".$contract_mission_id."' AND rp.is_hired='yes' AND r.status='closed'
							";
			if(($hiredProfiles = $this->getQuery($hiredQuery,true))!=NULL)
				return $hiredProfiles;
			else
				return null;				
		
		}		
}
?>