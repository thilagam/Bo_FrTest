<?php
/**
 * Responsible for Tasks in Tech and SEO Followup
 * @version 1.0
 */
	class Ep_Quote_Task extends Ep_Db_Identifier
	{
		protected $_name = 'MissionTasks';
		
		function getIdentifier()
		{
			return number_format(microtime(true),0,'','').mt_rand(10000,99999);
		}

		function insertTask($save)
		{
			$save['task_id'] = $this->getIdentifier();
			$this->insertQuery($save);
			return $save['task_id'];
		}
		
		function updateTask($data,$identifier)
		{
			$where=" task_id='".$identifier."'";
			$this->updateQuery($data,$where);
		}
		
		function getTask($task_id)
		{
			$query = "SELECT t.* FROM MissionTasks t WHERE t.task_id='".$task_id."'";
			if(($contractList=$this->getQuery($query,true))!=NULL)
				return $contractList;
			else
				return NULL;
		}
		
		function getTasks($search=NUll)
		{
			if($search['cmid'])
			$condition = " AND contract_mission_id='".$search['cmid']."'";
			
			$query = "SELECT * FROM MissionTasks 
					  WHERE 1=1 $condition ORDER BY	created_at ASC
					 ";
					
			if(($result = $this->getQuery($query,true))!=NULL)
			{
				return $result;
			}
			else
				return NULL;
		}
		
		function getTechTaskMissions($search=NULL)
		{
			$where = "";
			if($search['tid'])
			$where .= " AND t.task_id ='".$search['tid']."'";

			if($search['cmid'])
			$where .= " AND t.contract_mission_id  ='".$search['cmid']."'";

			if($search['m_status'])
			$where .= " AND t.m_status ='".$search['m_status']."'";

			if($search['mission_id'])
				$where .= " AND cm.type_id ='".$search['mission_id']."'";

			if($search['contract_id'])
				$where .= " AND qc.quotecontractid ='".$search['contract_id']."'";

			if($search['cm_status']=='all')
				$where .= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed' OR cm.cm_status ='ongoing')";
			elseif($search['cm_status']=='' || $search['cm_status']=='ongoing')
				$where .= " AND cm.cm_status ='ongoing' AND cm.cm_status != 'validated' AND cm.cm_status != 'closed'";
			else
				$where .= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed') ";

						

			$query = "SELECT cm.comment as cmcomment,cm.assigned_by,cm.updated_by,cm.updated_at,cm.is_survey,cm.is_recruitment,cm.contractmissionid,cm.privatedelivery,cm.assigned_to,cm.contract_id,cm.type_id,cm.type,cm.external_mission,cm.freelance_name,cm.freelance_cost,tm.title,qc.expected_launch_date,qc.expected_end_date,tm.turnover,tm.cost,tm.package,tm.team_fee,tm.currency,tm.before_prod,tm.from_contract,tm.documents_path,tm.documents_name,tm.identifier as tmid,tm.comments,tm.created_by,tm.created_at,tm.delivery_time,tm.delivery_option,tm.team_packs,qc.quoteid,qc.contractname,qc.comment as contractcomment,qc.sales_creator_id,qc.quotecontractid,qc.created_at as qctime,q.sales_suggested_currency,q.is_new_quote,DATE(cm.assigned_at) as assigned_at,cm.cm_status,tm.volume,tm.volume,tm.oneshot,tm.volume_max,tm.tempo,tm.delivery_volume_option,tm.tempo_length,tm.tempo_length_option,t.volume as task_volume,t.task_title,t.m_status,DATE_FORMAT( t.validated_at, '%Y' ) as validatedyear,DATE_FORMAT( t.validated_at, '%Y-%m' ) as validatedyearmonth,DATE_FORMAT( t.validated_at, '%m' ) as validatedmonth,t.comments as task_comments,t.created_by as task_created_by,t.created_at,t.documents_name as task_documents_name,t.documents_path as task_documents_path,t.volume as task_volume,t.turnover as task_turnover,t.cost as task_cost,t.updated_by as task_updated_by
			FROM `ContractMissions` cm 
			JOIN MissionTasks t ON t.contract_mission_id = cm.contractmissionid  
			JOIN TechMissions tm ON tm.identifier = cm.type_id
			JOIN QuoteContracts qc ON qc.quotecontractid = cm.contract_id
			JOIN Quotes q ON q.identifier = qc.quoteid
			WHERE 1=1 AND cm.type='tech' $where";
			
			//echo $query; exit;
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return array();
		}
		
		function getSeoTaskMissions($search=NULL)
		{
			$where = "";
			if($search['tid'])
			$where .= " AND t.task_id ='".$search['tid']."'";
			
			$query = "SELECT qm.misson_user_type,qm.mission_length_option,qm.mission_length,qm.product,qc.quotecontractid,qc.expected_launch_date,qc.contractname,qc.expected_end_date,qm.identifier as qmid,qm.language_source,qm.product_type,qm.product_type_other,qm.comments,qm.created_by,qm.turnover,qm.package,qm.team_fee,qm.team_packs,qm.cost,	q.sales_suggested_currency,qm.language_dest,q.identifier as quote_id,q.client_id,qm.before_prod,qm.documents_path,qm.documents_name,cm.contractmissionid,cm.comment as cmcomment,cm.updated_by,cm.assigned_by,cm.assigned_to,up.first_name,up.last_name,qc.comment as contractcomment,qc.sales_creator_id,qc.created_at as qctime,DATE(cm.assigned_at) as assigned_at,cm. 	privatedelivery,cm.is_survey,cm.is_recruitment,cm.type_id,cm.cm_status,t.task_title,t.comments as task_comments,t.created_by as task_created_by,t.documents_name as task_documents_name,t.documents_path as task_documents_path,t.updated_by as task_updated_by,t.linked_to
			FROM `QuoteContracts` qc
			JOIN Quotes q ON q.identifier = qc.quoteid 
			JOIN  QuoteMissions qm ON qm.quote_id = q.identifier
			LEFT JOIN ContractMissions cm ON cm.type_id = qm.identifier AND cm.contract_id = qc.quotecontractid
			JOIN MissionTasks t ON t.contract_mission_id = cm.contractmissionid  
			JOIN User u ON u.identifier=qm.created_by 
			JOIN UserPlus up ON up.user_id = u.identifier
			WHERE 1=1 AND qc.status='validated' $where
			ORDER BY qc.quotecontractid DESC, field(qm.misson_user_type, 'sales', 'seo'), qm.identifier ASC";
			
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return array();
		}
		
	}
	
?>