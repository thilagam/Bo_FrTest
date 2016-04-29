<?php
	
	class Ep_Quote_Quotecontract extends Ep_Db_Identifier
	{
		protected $_name = 'QuoteContracts';
		
	 	function getIdentifier()
		{
			return number_format(microtime(true),0,'','').mt_rand(10000,99999);
		}  
		
		function insertQuotecontract($save)
		{
			$this->_name =  'QuoteContracts';
			$save['quotecontractid'] = $this->getIdentifier();
			$this->insertQuery($save);
			return $save['quotecontractid'];
		}
		
		function getContracts($search=NULL)
		{
			
			//TEST CLIETN CONDITION
			if($this->adminLogin->type!='superadmin')
			{
				$conf_obj=new Ep_Delivery_Configuration();
				$test_client_id=$conf_obj->getConfiguration('test_client_id');			
				$testCondition=" AND q.client_id!='".$test_client_id."'";
			}
			$where = "";
			$having = "";
			if($search['status']=='all')
				$where.= " AND qc.status IN( 'validated','closed')";
			elseif($search['status']=='' || $search['status']=='validated')
				$where.= " AND qc.status IN( 'validated')";
			else
				$where.= " AND qc.status IN('".$search['status']."')";

			if($search['mulitple_status'])
				$where.= " AND qc.status IN(". $search['mulitple_status'] .") ";
			if($search['not_mulitple_status'])
				$where.= " AND qc.status NOT IN(". $search['not_mulitple_status'] .") ";
			if($search['sales_id'])
				$where.= " AND qc.sales_creator_id ='". $search['sales_id'] ."' ";
			if($search['cid'])
				$where.= " AND qc.quotecontractid ='". $search['cid'] ."' ";
			if($search['client_id'])
				$where.= " AND q.client_id ='". $search['client_id'] ."' ";
			if($search['percentage']==100)
				$having = ' HAVING percentage=100';
			elseif($search['percentage'])
				$having = ' HAVING percentage!=100';
			$query = "SELECT qc.quotecontractid, qc.contractname, qc.quoteid, qc.signaturedate, qc.expected_end_date, qc.expected_launch_date, qc.status, IF( q.final_turnover, q.final_turnover, q.sales_suggested_price ) AS turnover_quote,qc.turnover,DATEDIFF(qc.expected_end_date,qc.expected_launch_date) AS duration,
			CASE q.techmissions_assigned WHEN '' THEN '' ELSE 'Tech' END AS tech_team,
			CASE WHEN q.seo_review='auto_skipped' THEN '' WHEN q.seo_review='skipped' THEN '' WHEN EXISTS (SELECT * FROM QuoteMissions qm WHERE qm.quote_id=qc.quoteid AND qm.product IN('seo_audit','smo_audit','content_strategy') AND qm.misson_user_type='seo') THEN 'Seo' ELSE '' END AS seo_team,
			CASE q.prod_review WHEN 'auto_skipped' THEN '' WHEN 'skipped' THEN '' ELSE 'Prod' END AS prod_team,
			q.identifier, q.sales_suggested_currency, q.client_id, up.first_name, up.last_name,client.first_name as clfname,client.last_name as cllname,u.email as clemail,u.identifier as clientid, if(round(avg(cm.progress_percent)),round(avg(cm.progress_percent)),0) as percentage,c.company_name,
			qc.sales_creator_id,qc.closed_comment		
			FROM QuoteContracts qc
			JOIN Quotes q ON q.identifier = qc.quoteid
			JOIN UserPlus up ON up.user_id = qc.sales_creator_id 
			JOIN UserPlus client ON client.user_id = q.client_id 
			JOIN User u ON u.identifier = q.client_id 
			LEFT JOIN ContractMissions cm ON cm.contract_id=qc.quotecontractid
			LEFT JOIN Client c ON c.user_id = q.client_id 
			WHERE 1=1 ".$where."
			$testCondition
			GROUP BY qc.quotecontractid
			$having
			ORDER BY qc.quotecontractid DESC";
			//echo $query;exit;
			if(($contractList=$this->getQuery($query,true))!=NULL)
				return $contractList;
			else
				return array();
		}
		
		function getContract($contract_id)
		{
			$query = "SELECT qc.* FROM QuoteContracts qc WHERE quotecontractid='".$contract_id."'";
			if(($contractList=$this->getQuery($query,true))!=NULL)
				return $contractList;
			else
				return NULL;
		}	
		
		public function updateContract($data,$identifier)
		{
			$this->_name =  'QuoteContracts';
			$where=" quotecontractid='".$identifier."'";
			$this->updateQuery($data,$where);
		}
		
		function getUsers($type,$superadmin=false,$mulitple=false,$quoteby=false)
		{
			
			//TEST CLIETN CONDITION
			if($this->adminLogin->type!='superadmin')
			{
				$conf_obj=new Ep_Delivery_Configuration();
				$test_client_id=$conf_obj->getConfiguration('test_client_id');			
				$testCondition=" AND identifier!='".$test_client_id."'";
			}
			if($quoteby)
				$query = "SELECT * FROM User LEFT JOIN UserPlus ON User.identifier = UserPlus.user_id WHERE User.status='Active' AND User.identifier='".$quoteby."'";
			else if($mulitple)
				$query = "SELECT * FROM User LEFT JOIN UserPlus ON User.identifier = UserPlus.user_id WHERE (type=$type) AND User.status='Active' $testCondition";
			else if($superadmin)
				$query = "SELECT * FROM User LEFT JOIN UserPlus ON User.identifier = UserPlus.user_id WHERE (type='".$type."' OR type='superadmin') AND User.status='Active' $testCondition";
			else
				$query = "SELECT * FROM User LEFT JOIN UserPlus ON User.identifier = UserPlus.user_id LEFT JOIN Client ON Client.user_id = UserPlus.user_id WHERE (type='".$type."') AND User.status='Active' $testCondition";
			//echo $query;exit;
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
		
		function insertContractMission($save)
		{
			$this->_name =  'ContractMissions';
			$save['contractmissionid'] = $this->getIdentifier();
			$this->insertQuery($save);
			return $save['contractmissionid'];
		}
		
		function getContractMission($cid,$type,$type_id,$cmid=NULL)
		{
			if($cmid)
			$query = "SELECT * from ContractMissions WHERE contractmissionid='".$cmid."'";
			else
			$query = "SELECT * from ContractMissions WHERE contract_id='".$cid."' AND type='".$type."' AND type_id='".$type_id."'";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
		
		/* get count of Contract Missions */
		function getCountContractMission($cid,$type)
		{
			$query = "SELECT * from ContractMissions WHERE contract_id='".$cid."' AND type='".$type."'";
			return  $this->getNbRows($query);
		}
		
		public function updateContractMission($data,$identifier)
		{
			$this->_name =  'ContractMissions';
			$where=" contractmissionid='".$identifier."'";
			return $this->updateQuery($data,$where);
		}
		
		public function updateBulkContractMission($data,$identifier)
		{
			$this->_name =  'ContractMissions';
			$where=" contractmissionid IN (".$identifier.")";
			return $this->updateQuery($data,$where);
		}
		
		public function getTechMission($id)
		{
			$query = "SELECT * from TechMissions WHERE identifier='".$id."'";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
		
		public function getSeoMission($id)
		{
			$query = "SELECT * from QuoteMissions WHERE identifier='".$id."'";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
		
		function insertTechMission($save)
		{
			$this->_name =  'TechMissions';
			$save['identifier'] = $this->getIdentifier();
			$this->insertQuery($save);
			return $save['identifier'];
		}
		
		function getTechMissionDetails($searchParameters=NULL,$limit=NULL)
		{
			if($searchParameters['mission_id'])
				$condition.=" AND t.mission_id='".$searchParameters['mission_id']."'";		
			if($searchParameters['quote_id'])
				$condition.=" AND find_in_set(t.identifier, (select techmissions_assigned From Quotes where identifier='".$searchParameters['quote_id']."') )>0";
			if($searchParameters['include_final'])
				$condition.=" AND t.include_final='".$searchParameters['include_final']."'";
			if($searchParameters['identifier'])
				$condition.=" AND t.identifier='".$searchParameters['identifier']."'";
			$missionQuery="SELECT t.*
								FROM TechMissions t							
								WHERE 1=1 $condition							
								ORDER BY from_contract DESC, t.identifier DESC
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
		
		function getClientId($email,$name,$comp_name,$fname)
		{
			$query = "SELECT * FROM User WHERE email='".$email."'";
						
			if(($count=$this->getNbRows($query))>0)
			{           
			  $res=$this->getQuery($query,true);           	
			  return $res[0]['identifier'];
			}
			else
			{
				$email = $this->checkUserExists($email,$name);
				$id= $this->getIdentifier();
				
				//User
				$this->_name='User';
				$U_array=array();
				$U_array['identifier']=$id;
				$U_array['email']=$email;
				$U_array['password']="test";
				$U_array['status']='Active';
				$U_array['type']='Client';
				$U_array['verified_status']='YES';
				$U_array['created_by']='backend';
				$this->insertQuery($U_array);
				//UserPlus
				$this->_name='UserPlus';
				$Up_array=array();
				$Up_array['user_id']=$id;
				$Up_array['first_name']= $fname;
				$this->insertQuery($Up_array);
				$this->_name='Client';
				$C_array=array();
				$C_array['user_id']=$id;
				$C_array['company_name']=$comp_name;
				$this->insertQuery($C_array);
		
				return $id;
			}
		}
		
		function checkUserExists($email,$cname)
		{
			$cname = str_replace(" ","",strtolower($cname));
			$i=0;
			do
			{
				$query = "SELECT * FROM User WHERE email='".$email."'";
				if(($count=$this->getNbRows($query))>0)
				{           
					$email = $cname.++$i."@test.com";
				}
				else
				break;
			}
			while(true);
			return $email;
		}
		
		/* function getUniqueIdentifier()
		{
			do
			{
				$id = $this->getIdentifier();
				$query = "SELECT * FROM User WHERE identifier='".$id."'";
				if(($count=$this->getNbRows($query))==0)
				break;
			}
			while(true);
			return $id;
		} */
		
		function insertuploadContract($save)
		{
			$this->_name =  'Ep_contract';
			$save['id'] = $this->getUniqueIdentifier('Ep_contract','id');
			$this->insertQuery($save);
			return $save['id'];
		}
		
		 public function getEPContactsMaster($name)
		{
				$query="select
						u.identifier as uid, CONCAT(first_name,' ',last_name) as contact_name, u.login as login_name, u.email
					from User u
						left JOIN UserPlus  up ON u.identifier=up.user_id
				   where  u.type not in ('client','contributor','chiefodigeo','superclient')
					and u.status='Active' AND u.blackstatus='no' AND (first_name='".$name."' OR last_name='".$name."')
					Group BY u.identifier,contact_name
					ORDER BY contact_name
					";		
		   //echo $query;exit;
			if(($ep_users=$this->getQuery($query,true))!=NULL)
				return $ep_users[0]['uid'];
			else
				return " ";
		}
		
		function insertMissions($save)
		{
			$this->_name =  'Missions_archieve';
			$save['id'] = $this->getUniqueIdentifier('Missions_archieve','id');
			$this->insertQuery($save);
			return $save['id'];
		}
		
		function getUniqueIdentifier($tbname,$id_table)
		{
			do
			{
				$id = $this->getIdentifier();
				$query = "SELECT * FROM $tbname WHERE $id_table='".$id."'";
				if(($count=$this->getNbRows($query))==0)
				break;
			}
			while(true);
			return $id;
		}
		
		function updateProdmissions($data,$identifier,$type)
		{
			$this->_name =  'ProdMissions';
			$where=" quotecontractid='".$identifier."' AND product='".$type."'";
			$this->updateQuery($data,$where);
		}
		
		function getContractDetails($search)
		{
			if($search['contractmissionid'])
			$where .= "AND contractmissionid='".$search['contractmissionid']."'";
			$query = "SELECT ContractMissions.comment as cmcomment,ContractMissions.updated_by as cmupdated_by,ContractMissions.updated_at as cmupdated_at,ContractMissions.*,QuoteContracts.* FROM ContractMissions JOIN QuoteContracts ON QuoteContracts.quotecontractid=ContractMissions.contract_id WHERE 1=1 $where";
			if(($res=$this->getQuery($query,true))!=NULL):
				 //$res=$this->getQuery($query,true);           	
				return $res;
			else:
				return false;
				endif;
		}
		
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
			
			$query = "SELECT  rp.identifier as rpid,rp.articles_per_week,rp.proposed_cost,rp.is_hired,rp.uploaded_files,up.initial,up.first_name,up.last_name,u.identifier as user_id,CASE u.profile_type WHEN 'senior' THEN 'S' WHEN 'sub-junior' THEN 'J' WHEN 'junior' THEN 'D' ELSE '' END as profiletype, r. 	no_contrib,r.is_quiz,r.is_test_article,qp.num_correct,qp.num_total,qp.qualified FROM RecruitmentParticipation as rp JOIN Recruitment r ON r.recruitment_id=rp.recruitment_id LEFT JOIN QuizParticipation as qp ON qp.id=rp.quiz_partcipation_id JOIN User as u ON u.identifier=rp.user_id LEFT JOIN UserPlus as up ON up.user_id=u.identifier WHERE ". $whereq;
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
		
		/* To List out missions from Contracts for Follow Up */
		function getContractMissions($searchparameters=NULL)
		{
			if($searchparameters['type'])
				$where = " AND cm.type='".$searchparameters['type']."'";
			if($searchparameters['contract_id'])
				$where = " AND cm.contract_id='".$searchparameters['contract_id']."'";
				
			$query = "SELECT qm.product,qm.product_type,qm.product_type_other,qm.language_source,qm.language_dest,qc.expected_launch_date,qc.expected_end_date,qc.contractname,up.first_name,up.last_name,cm.is_survey,cm.is_recruitment,cm.contractmissionid,cm.privatedelivery,cm.assigned_to,cm.contract_id,cm.type_id,cm.type,cm.sales_title FROM `ContractMissions` cm 
			JOIN QuoteContracts qc ON qc.quotecontractid=cm.contract_id 
			JOIN QuoteMissions qm ON qm.identifier=cm.type_id 
			JOIN User u ON u.identifier=cm.assigned_to 
			JOIN UserPlus up ON up.user_id = u.identifier  WHERE 1=1 $where";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return array();
		}
		
		function getSalesSeoMissionsContracts($search=NULL,$type="")
		{
			
			//TEST CLIETN CONDITION
			if($this->adminLogin->type!='superadmin')
			{
				$conf_obj=new Ep_Delivery_Configuration();
				$test_client_id=$conf_obj->getConfiguration('test_client_id');			
				$testCondition=" AND q.client_id!='".$test_client_id."'";
			}
			$where = "";
			if($search['contract_id'])
			$where .= " AND qc.quotecontractid='".$search['contract_id']."'";

			if($search['client_id'])
			$where .= " AND q.client_id='".$search['client_id']."'";
			
			if($search['assigned_to'])
			$where .= " AND cm.assigned_to='".$search['assigned_to']."'";
			
			if($search['cmid'])
			$where .= " AND cm.contractmissionid='".$search['cmid']."'";
			
			if($search['type'])
			$where .= " AND cm.type='".$search['type']."'";
			
			if($search['mission_type'])
			$where .= $search['mission_type'];
			
			if($search['cm_status'])
			$where .= " AND cm.cm_status='".$search['cm_status']."'";
			
			$query = "SELECT qm.misson_user_type,qm.mission_length_option,qm.mission_length,qm.product,qc.quotecontractid,qc.contractfilepaths,qc.expected_launch_date,qc.contractname,qc.expected_end_date,qm.identifier as qmid,qm.language_source,qm.product_type,qm.product_type_other,qm.volume,qm.comments,qm.created_by,qm.turnover,qm.package,qm.team_fee,qm.cost,qm.team_packs, 	q.sales_suggested_currency,qm.language_dest,q.identifier as quote_id,q.client_id,qm.before_prod,qm.documents_path,qm.documents_name,cm.contractmissionid,cm.comment as cmcomment,cm.updated_by,cm.assigned_by,cm.assigned_to,cm.external_mission,cm.freelance_name,cm.freelance_cost,up.first_name,up.last_name,qc.comment as contractcomment,qc.sales_creator_id,qc.created_at as qctime,DATE(cm.assigned_at) as assigned_at,cm.privatedelivery,cm.is_survey,cm.is_recruitment,cm.type_id,cm.cm_status,cm.progress_percent,cm.documents_path as cmdocuments_path,cm.documents_name as cmdocuments_name,cm.freeze_start_date,cm.freeze_end_date,		qm.oneshot,qm.volume_max,qm.delivery_volume_option,qm.tempo_length,qm.tempo_length_option,c.company_name,IF(client.first_name!=\"\" OR client.first_name!=\"\", CONCAT(client.first_name,\" \",client.last_name),clientuser.email)  as client_name,qm.tempo,qm.free_mission,qm.nb_words,cm.cm_turnover 
			FROM `QuoteContracts` qc
			JOIN Quotes q ON q.identifier = qc.quoteid 
			JOIN  QuoteMissions qm ON qm.quote_id = q.identifier
			LEFT JOIN ContractMissions cm ON cm.type_id = qm.identifier AND cm.contract_id = qc.quotecontractid
			JOIN User u ON u.identifier=qm.created_by 
			JOIN UserPlus up ON up.user_id = u.identifier
			JOIN Client c ON c.user_id = q.client_id
			JOIN UserPlus client ON client.user_id = q.client_id 
			JOIN User clientuser ON clientuser.identifier= q.client_id
			WHERE qm.include_final='yes' AND (qc.status='validated' OR qc.status='closed') $where
			$testCondition
			ORDER BY qc.quotecontractid DESC, field(qm.misson_user_type, 'sales', 'seo'), qm.identifier ASC
			";
			//echo $query; exit
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return array();
		}
		
		function getContractTechMissions($search=NULL)
		{
			
			//TEST CLIETN CONDITION
			if($this->adminLogin->type!='superadmin')
			{
				$conf_obj=new Ep_Delivery_Configuration();
				$test_client_id=$conf_obj->getConfiguration('test_client_id');			
				$testCondition=" AND q.client_id!='".$test_client_id."'";
			}
			$where = "";
			if($search['contract_id'])
			$where .= " AND cm.contract_id='".$search['contract_id']."'";
			
			if($search['assigned_to'])
			$where .= " AND cm.assigned_to='".$search['assigned_to']."'";
			
			if($search['cmid'])
			$where .= " AND cm.contractmissionid='".$search['cmid']."'";
			
			if($search['cm_status'])
			$where .= " AND cm.cm_status='".$search['cm_status']."'";

			if($search['client_id'])
			$where .= " AND q.client_id='".$search['client_id']."'";
			
			$query = "SELECT cm.comment as cmcomment,cm.progress_percent ,cm.assigned_by,cm.updated_by,cm.updated_at,cm.is_survey,cm.is_recruitment,cm.contractmissionid,cm.privatedelivery,cm.assigned_to,cm.contract_id,cm.type_id,cm.type,cm.external_mission,cm.freelance_name,cm.freelance_cost,tm.title,qc.expected_launch_date,qc.expected_end_date,tm.turnover,tm.cost,tm.package,tm.team_fee,tm.currency,tm.before_prod,tm.from_contract,tm.documents_path,tm.documents_name,tm.identifier as tmid,tm.comments,tm.free_mission,tm.created_by,tm.created_at,tm.delivery_time,tm.delivery_option,tm.volume,tm.oneshot,tm.volume_max,tm.tempo,tm.delivery_volume_option,tm.tempo_length,tm.tempo_length_option,tm.team_packs,qc.quoteid,qc.contractname,qc.comment as contractcomment,qc.sales_creator_id,qc.contractfilepaths,qc.quotecontractid,qc.created_at as qctime,q.sales_suggested_currency,q.is_new_quote,q.identifier as qid,DATE(cm.assigned_at) as assigned_at,cm.cm_status,c.company_name,IF(client.first_name!=\"\" OR client.first_name!=\"\", CONCAT(client.first_name,\" \",client.last_name),clientuser.email)  as client_name,q.client_id
			FROM `ContractMissions` cm 
			JOIN TechMissions tm ON tm.identifier = cm.type_id
			JOIN QuoteContracts qc ON qc.quotecontractid = cm.contract_id
			JOIN Quotes q ON q.identifier = qc.quoteid
			JOIN Client c ON c.user_id = q.client_id
			JOIN UserPlus client ON client.user_id = q.client_id 
			JOIN User clientuser ON clientuser.identifier= q.client_id
		    WHERE tm.include_final='yes' AND cm.type='tech' $where
		    $testCondition
		    ";
			
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return array();
		}
		
		/* To get Split/Month of Quote Missions based on Product Type */
		function getSplitMissions($search = array())
		{
			$where = " include_final='yes'";
			if($search['quote_id'])
			$where .= " AND qm.quote_id='".$search['quote_id']."'";
			/*$query = "SELECT `identifier`,`quote_id`,SUM(IF(`package`='team',turnover+(team_fee*team_packs),turnover)) as calturnover,product_type,package,turnover,team_fee,team_packs,max(mission_length) as maxlength,mission_length_option 
			FROM `QuoteMissions` qm
			WHERE  $where AND qm.product_type !=''
			GROUP BY `product_type`"; */
			$query = "SELECT identifier,product,language_source,IF(free_mission ='no',SUM(IF(`package`='team',turnover+(team_fee*team_packs),turnover)),0) as calturnover,max(mission_length) as maxlength,mission_length_option, IF(product_type=' ',product,product_type) as pptype,qm.product_type_other,oneshot,staff_time,staff_time_option,tempo,tempo_length_option,volume_max,tempo_length,qm.volume,cm.cm_turnover
			FROM `QuoteMissions` qm 
			LEFT JOIN ContractMissions cm ON cm.type_id=qm.identifier
			WHERE $where  
			GROUP BY qm.identifier
			ORDER BY field(qm.misson_user_type, 'sales', 'seo'), qm.identifier ASC
			";
			//echo $query; exit;
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return array();
		}
		
		/* To get Info of Avg. time between prod validate and sales validate */
		function getAvgProdSales()
		{
			$query = "SELECT q.identifier as quote_id,ql.action, ql.action_at FROM `Quotes` q JOIN QuotesLog ql ON q.identifier = ql.quote_id WHERE ql.action = 'prod_validated_delay' OR ql.action='prod_validated_ontime' OR ql.action='sales_validated_ontime' GROUP BY q.identifier,action ORDER BY `q`.`identifier` ASC,ql.action,ql.action_at ASC";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return array();
		}
		
		/* To insert Split values */
		function insertSplitTurnovers($save)
		{
			$this->_name =  'ContractSplitValues';
			$this->insertQuery($save);
			return true;
		}
		
		/* To delete split values */
		function deleteSplitTurnovers($cid,$mid="")
		{
			$where = "contract_id='".$cid."' AND from_followup=0";
			if($mid)
				$where .= " AND mission_id='".$mid."'";
			$this->_name =  'ContractSplitValues';
			$this->deleteQuery($where);
		}
		
		/* To get split values */
		function getSplitTurnovers($cid)
		{
			$query = "SELECT * FROM ContractSplitValues WHERE contract_id='".$cid."' ORDER BY type,month_year";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}
		
		/* To get contract count */
		function checkcontract($client_id)
		{
			$query = "SELECT qc.quotecontractid FROM `QuoteContracts` qc JOIN Quotes q ON q.identifier = qc.quoteid WHERE q.client_id='".$client_id."'";
			if(($result = $this->getQuery($query,true))!=NULL)
				return true;
			else
				return false;
		}
		
		/* Insert staffing mission */
		function insertStaffMission($save)
		{
			$this->_name =  'StaffMissions';
			$save['staff_missionId'] = $this->getIdentifier();
			$this->insertQuery($save);
			return $save['staff_missionId'];
		}
		
		/* Update staff mission details */
		function updateStaffMission($update,$id)
		{
			$this->_name =  'StaffMissions';
			$where=" staff_missionId='".$id."'";
			$this->updateQuery($update,$where);
		}
        /*added by naseer on 10-11-2015*/
        /*update the deleted prod */

        function updateProdMission($update,$id)
        {
            $this->_name =  'ContractMissions';
            $where=" contractmissionid='".$id."'";
            $this->updateQuery($update,$where);
        }
        /*end of added by naseer on 10-11-2015*/
		// To get Staffing Prodmissions
		function getStaffMissionDetails($search = NULL)
		{
			$where = '1=1';
			if($search['staff_missionId'])
				$where = " sm.staff_missionId = '".$search['staff_missionId']."'";
			if($search['contract_id'])
				$where .= " AND sm.contract_id = '".$search['contract_id']."'";
			$query = "SELECT sm.* FROM `StaffMissions` sm 
					  WHERE $where
					   ORDER BY sm.contract_id  DESC, sm.created_at ASC
					  ";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}
        /*added by naseer on 10-11-2015
		// To get Staffing Prodmissions
		function getStaffMissionDetails($search = NULL)
		{
			$where = '1=1';
			if($search['staff_missionId'])
				$where = " sm.staff_missionId = '".$search['staff_missionId']."'";
			if($search['contract_id'])
				$where .= " AND sm.contract_id = '".$search['contract_id']."'";
			$query = "SELECT sm.* FROM `StaffMissions` sm
					  WHERE $where
					   ORDER BY sm.contract_id  DESC, sm.created_at ASC
					  ";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}
        /*end of added by naseer on 10-11-2015*/
		
		// To get All staffmissiondetails
		function getStaffMissions($search = NULL)
		{
			$where = '1=1';
			if($search['staff_missionId'])
				$where = " sm.staff_missionId = '".$search['staff_missionId']."'";
			if($search['contract_id'])
				$where .= " AND sm.contract_id = '".$search['contract_id']."'";
			if($search['assigned_to'])
				$where .= " AND cm.assigned_to = '".$search['assigned_to']."'";
			if($search['cmid'])
				$where .= " AND cm.contractmissionid = '".$search['cmid']."'";
			if($search['client_id'])
				$where .= " AND q.client_id = '".$search['client_id']."'";

			$query = "SELECT sm.*,cm.contractmissionid as cmid,cm.assigned_at,cm.cm_status,cm.comment as cmcomment,qc.expected_launch_date,qc.expected_end_date,cm.assigned_to,cm.privatedelivery,cm.type,cm.type_id,cm.progress_percent,cm.assigned_by,cm.updated_by,cm.updated_at,up.first_name,up.last_name,qc.quoteid,qc.contractname,qc.comment as contractcomment,qc.sales_creator_id,qc.contractfilepaths,qc.quotecontractid,qc.created_at as qctime,c.company_name,q.client_id,IF(client.first_name!=\"\" OR client.first_name!=\"\", CONCAT(client.first_name,\" \",client.last_name),clientuser.email)  as client_name
					  FROM `StaffMissions` sm 
					  LEFT JOIN ContractMissions cm ON cm.type_id = sm.staff_missionId
					  JOIN QuoteContracts qc ON qc.quotecontractid = sm.contract_id 
					  LEFT JOIN User u ON u.identifier=cm.assigned_to 
					  LEFT JOIN UserPlus up ON up.user_id = u.identifier
					  JOIN Quotes q ON q.identifier = qc.quoteid
					  JOIN Client c ON c.user_id = q.client_id
					  JOIN UserPlus client ON client.user_id = q.client_id 
					  JOIN User clientuser ON clientuser.identifier= q.client_id
					  WHERE $where
					  ORDER BY sm.contract_id  DESC, sm.created_at ASC
					  ";
		
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}
		
		function insertTempoOneshots($save)
		{
			$this->_name = "TempoOneshot";
			$this->insertQuery($save);
		}
		
		function deleteTempoOneshots($identifier,$missionid)
		{
			$this->_name = "TempoOneshot";
			$where=" contract_id='".$identifier."' AND mission_id = '".$missionid."'";    
			//if($identifier)	
			$this->deleteQuery($where);
		}
		
		function getOneshotTempos($cid,$missionid)
		{
			$query = "SELECT * FROM TempoOneshot WHERE contract_id='".$cid."' AND mission_id = '".$missionid."'";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}
		
		function getSalesMissions($search=NULL)
		{
			$where = "";
			if($search['type'])
				$where .= " AND cm.type='".$search['type']."'";
			if($search['contract_id'])
				$where .= " AND cm.contract_id='".$search['contract_id']."'";
			if($search['cmid'])
				$where .= " AND cm.contractmissionid='".$search['cmid']."'";
			if($search['assigned_to'])
				$where .= " AND cm.assigned_to = '".$search['assigned_to']."'";
			if($search['client_id'])
				$where .= " AND q.client_id = '".$search['client_id']."'";
			$query = "SELECT cm.*,qc.expected_launch_date,qc.expected_end_date,up.first_name,up.last_name,qc.quoteid,qc.contractname,qc.comment as contractcomment,qc.sales_creator_id,qc.contractfilepaths,qc.quotecontractid,qc.created_at as qctime,c.company_name,q.client_id,q.final_mission_length,q.final_mission_length_option,IF(client.first_name!=\"\" OR client.first_name!=\"\", CONCAT(client.first_name,\" \",client.last_name),clientuser.email) as client_name
					  FROM ContractMissions cm 
					  JOIN QuoteContracts qc ON qc.quotecontractid = cm.contract_id 
					  JOIN User u ON u.identifier = cm.assigned_to
					  JOIN UserPlus up ON up.user_id = u.identifier
					  JOIN Quotes q ON q.identifier = qc.quoteid
					  JOIN Client c ON c.user_id = q.client_id
					  JOIN UserPlus client ON client.user_id = q.client_id 
					  JOIN User clientuser ON clientuser.identifier= q.client_id
					  WHERE 1=1 $where
					  ";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}
		
		/* to get Freeze Missions */
		function getFreezeMissions()
		{
			$query = "SELECT cm.*,u.email 
					  FROM ContractMissions cm
					  JOIN User u ON u.identifier=cm.assigned_to
					  WHERE cm.freeze_email_date LIKE '".date('Y-m-d H:i')."%'
					  ";
					 
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;		  
		}
		
		//get Client Contract
		function getClientContracts($search=NULL)
		{
						
			$where="";
				if($search['client_id'])
				{
				$where .= " AND q.client_id='".$search['client_id']."'";
				}
				if($search['sales_id'])
				{
				$where .=" OR qc.sales_creator_id='".$search['sales_id']."'";
				}
				if($search['start_date']!="" && $search['end_date']=="")
				{
				$where .=" AND qc.expected_launch_date='".$search['start_date']."' OR qc.expected_end_date='".$search['start_date']."' ";
				}elseif($search['end_date']!="" && $search['start_date']=="")
				{
				$where .=" AND qc.expected_launch_date='".$search['end_date']."' OR qc.expected_end_date='".$search['end_date']."' ";
				}
				elseif($search['start_date']!=""  && $search['end_date']!="")
				{
				$where .=" AND (qc.expected_launch_date BETWEEN '".$search['end_date']."' AND '".$search['end_date']."') OR (qc.expected_end_date BETWEEN '".$search['end_date']."' AND '".$search['end_date']."' )";	
				}
				
			
			$query = "SELECT qc.expected_launch_date,qc.expected_end_date,qc.quoteid,q.sales_suggested_currency,
			qc.contractname,qc.comment as contractcomment,qc.sales_creator_id,qc.contractfilepaths,q.sales_suggested_currency,
			qc.quotecontractid,qc.created_at as qctime,c.company_name,c.client_code,q.client_id,q.final_mission_length,q.final_mission_length_option,
			IF(client.first_name!='' OR client.first_name!='', CONCAT(client.first_name,' ',client.last_name),clientuser.email) as client_name,
			IF( q.final_turnover, q.final_turnover, q.sales_suggested_price ) AS turnover,DATEDIFF(qc.expected_end_date,qc.expected_launch_date) AS duration
					  FROM  QuoteContracts qc 
					  JOIN Quotes q ON q.identifier = qc.quoteid
					  JOIN Client c ON c.user_id = q.client_id
					  JOIN UserPlus client ON client.user_id = q.client_id 
					  JOIN User clientuser ON clientuser.identifier= q.client_id
					  WHERE (qc.status='validated' OR qc.status='closed' ) $where ";
			//echo $query;	 exit;
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
			
			
		}
		
		function getMissionDetails($mission_id){
			
			$query="SELECT * FROM QuoteMissions WHERE identifier='".$mission_id."'";
			
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
			
			
			}
		
		function getSplitTurnoversclients($search=Null)
		{
			$where="";
			if($search['contract_id']){
				$where .=" AND contract_id='".$search['contract_id']."'";
				}
			if($search['name'] && $search['product']==""){
				$where .=" AND name='".$search['name']."'";
				} elseif($search['product'] && $search['name']==""){
					$where .=" AND name='".$search['product']."'";
				}elseif($search['product'] && $search['name']){
					$where .=" AND name IN ('".$search['product']."','".$search['name']."')";
						}
			
			$query = "SELECT * FROM ContractSplitValues WHERE 1=1 $where";
			//echo $query;
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}
		
		function getsplitmonthexists($search=Null)
		{
			$where="";
			if($search['contract_id']){
				$where .=" AND contract_id='".$search['contract_id']."'";
				}
			if($search['quote_id']){
				$where .=" AND quote_id='".$search['quote_id']."'";
				}
			if($search['mission_id']){
			$where .=" AND mission_id='".$search['mission_id']."'";
			}
			if($search['month_year']){
			$where .=" AND month_year='".$search['month_year']."'";
				}
				
			$query = "SELECT * FROM ContractSplitValues WHERE 1=1 $where ";
			
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}
		
		function UpdatesplitMonth($data,$mission_id,$contract_id,$month_year,$quote_id)
		{
					
			$this->_name = "ContractSplitValues";
								
			$where="  contract_id='".$contract_id."' AND mission_id='".$mission_id."' AND month_year='".$month_year."' AND quote_id='".$quote_id."' ";
			
			$this->updateQuery($data,$where);
			
		}
		
		function clientQuotesdetails($sales_id){
			
			$query="select q.sales_review,IF( q.final_turnover, q.final_turnover, q.sales_suggested_price ) AS turnover
			 FROM Quotes  q where q.quote_by='".$sales_id."' ";
			
			 if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
			
			}


		function clientContractCurrentYear($year,$client_id)
		{
				if($year=="") $year="date_format(now(), '%Y')"; 
				
				$contractCAQuery="SELECT SUM(qc.turnover) as ca_year,q.sales_suggested_currency
								From QuoteContracts qc
								JOIN Quotes q ON q.identifier=qc.quoteid
								WHERE date_format(qc.expected_launch_date, '%Y')=$year 
								AND q.client_id='".$client_id."'
								GROUP BY sales_suggested_currency				
								";	
				if(($CAofYear = $this->getQuery($contractCAQuery,true)) != NULL)
		            return $CAofYear;
		        else
		            return NULL;
		}

		function techmissionlinked($quote_id,$mission_id,$before_prod)
		{

			$techmissionQuery= "SELECT * from TechMissions where prod_linked='".$mission_id."' and before_prod='".$before_prod."' 
			and find_in_set(TechMissions.identifier, (select techmissions_assigned From Quotes where Quotes.identifier='".$quote_id."'))>0";			

			//echo $techmissionQuery.'<br>'; 
				if(($result = $this->getQuery($techmissionQuery,true))!=NULL)
				return $result;
			else
				return false;

		}


		//getting the tech flag details
		function getTechFlag($tech_id)
		{
			$techmissionQuery= "SELECT tmt.tech_type_assign from TechMissions as tm 
			LEFT JOIN TechMissionTypes as tmt ON tm.tech_type_id=tmt.tid
				where tm.identifier='".$tech_id."' 
				";

				if(($result = $this->getQuery($techmissionQuery,true))!=NULL)
				return $result;
				else
				return false;
		}
		//Flg user details
		function getmissionuser($type)
		{
					
			$query = " SELECT * FROM User LEFT JOIN UserPlus ON User.identifier = UserPlus.user_id 
				WHERE User.type IN ($type) AND User.status='Active' ";

			

			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return array();
		}
		
		
		
	}
?>
