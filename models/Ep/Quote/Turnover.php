<?php
/**
 * Ep_Quote_Turnover
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_Turnover extends Ep_Db_Identifier
{
	function getRealTurnovers($search = NULL)
	{
		$where = $join = "";
		if($search['year'])
		{
			$where .= " AND YEAR(p.updated_at)='".$search['year']."'";
		}
		if($search['month'])
		{
			$where .= " AND MONTH(p.updated_at)='".$search['month']."'";
		}
		if($search['client'])
		{
			$where .= " AND d.user_id='".$search['client']."'";
		}
		if($search['contract_mission_id'])
		{
			$where .= " AND d.contract_mission_id='".$search['contract_mission_id']."'";
		}
		/*if($search['assigned_to'])
		{
			$where .= " AND cm.assigned_to='".$search['assigned_to']."'";
		}*/
		if($search['sales_id'])
		{
			//$where .= " AND cm.assigned_by='".$search['sales_id']."'";
			//$join = ' JOIN QuoteContracts qc on cm.contract_id = qc.quotecontractid ';
			$where .= " AND qc.sales_creator_id = '".$search['sales_id']."'";
		}

		if($search['cm_status']=='' || $search['cm_status']=='all')
			$where.= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed' OR cm.cm_status ='ongoing')";
		elseif( $search['cm_status']=='ongoing')
			$where.= " AND cm.cm_status ='ongoing' AND cm.cm_status != 'validated' AND cm.cm_status != 'closed'";
		else
			$where.= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed') ";
		
		$query = "SELECT sum(a.price_final) as total_price,count(a.id) as artcount,sum(a.price_max)+IFNULL(sum(a.correction_pricemax),0) as total_corrector_writer,d.files_pack,SUM(p.price_user)+SUM(IFNULL(cp.price_corrector,0)) as publishedprice,p.updated_at,c.company_name,c.client_code,DATE_FORMAT( p.updated_at, '%Y-%m' ) as yearmonth,d.user_id,cm.currency as ind_currency,sum(a.files_pack) as total_packs,(sum(a.files_pack * qm.unit_price)) as total
		FROM Delivery d 
		JOIN Article a ON a.delivery_id = d.id 
		JOIN Participation p ON p.article_id = a.id
		LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
		JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id
		JOIN QuoteContracts qc on cm.contract_id = qc.quotecontractid
		JOIN QuoteMissions qm ON qm.identifier = cm.type_id
		JOIN Client c ON c.user_id = d.user_id
		$join
		WHERE d.user_id NOT IN ('130425154610169') AND d.missiontest='no' AND p.status='published' AND qc.status NOT IN('sales')
		$where
		GROUP BY MONTH(p.updated_at),d.user_id";
		//echo $query;exit;
		//AND cm.cm_status != 'deleted' 
		//qc.status NOT IN('deleted','sales')
		if(($res=$this->getQuery($query,true))!=NULL)
			return $res;
		else
			return NULL;	
	}
	
	function getRealSeoMission($search=NULL)
	{
		$where = "";
		if($search['year'])
		{
			$where .= " AND YEAR(cm.validated_at)='".$search['year']."'";
		}
		if($search['month'])
		{
			$where .= " AND MONTH(cm.validated_at)='".$search['month']."'";
		}
		if($search['client'])
		{
			$where .= " AND c.user_id='".$search['client']."'";
		}
		if($search['contract_mission_id'])
		{
			$where .= " AND cm.contractmissionid='".$search['contract_mission_id']."'";
		}
		if($search['sales_id'])
		{
			//$where .= " AND cm.assigned_by='".$search['sales_id']."'";
			//$join = ' JOIN QuoteContracts qc on cm.contract_id = qc.quotecontractid ';
			$where .= " AND qc.sales_creator_id = '".$search['sales_id']."'";
		}
		$query = "SELECT SUM(qm.turnover) as 			missionturnover,c.user_id,c.company_name,c.client_code,DATE_FORMAT(cm.validated_at, '%Y-%m' ) as yearmonth,cm.currency as ind_currency,q.sales_suggested_currency as currency
				  FROM ContractMissions cm 
				  JOIN QuoteMissions qm ON qm.identifier = cm.type_id 
				  JOIN Quotes q ON q.identifier = qm.quote_id 
				  JOIN QuoteContracts qc ON qc.quotecontractid = cm.contract_id
				  JOIN Client c ON c.user_id = q.client_id 
				  WHERE q.client_id NOT IN ('130425154610169') AND cm.type='seo' 
				  $where 
				  GROUP BY MONTH(cm.validated_at),c.user_id";
		if(($res=$this->getQuery($query,true))!=NULL)
			return $res;
		else
			return NULL;
	}
	
	function getRealTechMission($search=NULL)
	{
		$where = "";
		
		if($search['month'])
		{
			$where .= " AND MONTH(cm.validated_at)='".$search['month']."'";
		}
		if($search['client'])
		{
			$where .= " AND c.user_id='".$search['client']."'";
		}
		if($search['contract_mission_id'])
		{
			$where .= " AND cm.contractmissionid='".$search['contract_mission_id']."'";
		}
		if($search['sales_id'])
		{
			$where .= " AND qc.sales_creator_id = '".$search['sales_id']."'";
		}
		
		$query = "SELECT SUM(tm.turnover) as missionturnover,tm.*,c.user_id,c.company_name,c.client_code,DATE_FORMAT(cm.validated_at, '%Y-%m' ) as yearmonth,cm.currency as ind_currency,qc.quotecontractid,q.sales_suggested_currency as currency
				  FROM ContractMissions cm 
				  JOIN TechMissions tm ON tm.identifier = cm.type_id 
				  JOIN QuoteContracts qc ON qc.quotecontractid = cm.contract_id 
				  JOIN Quotes q ON q.identifier = qc.quoteid 
				  JOIN Client c ON c.user_id = q.client_id 
				  WHERE q.client_id NOT IN ('130425154610169') AND cm.type='tech' 
				  $where
				  GROUP BY MONTH(cm.validated_at),c.user_id";

		if(($res=$this->getQuery($query,true))!=NULL)
			return $res;
		else
			return NULL;
	}
	
	function getRealMissionTurnovers($search=NULL)
	{
		$query = "SELECT sum(a.price_final) as total_price,sum(a.price_max)+IFNULL(sum(a.correction_pricemax),0) as total_corrector_writer,d.files_pack,(SUM(p.price_user)+IFNULL(cp.price_corrector,0)) as publishedprice,p.updated_at,c.company_name,DATE_FORMAT( p.updated_at, '%Y-%m' ) as yearmonth,d.user_id
		FROM Delivery d 
		JOIN Article a ON a.delivery_id = d.id 
		JOIN Participation p ON p.article_id = a.id
		LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
		JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id
		JOIN Client c ON c.user_id = d.user_id
		WHERE d.missiontest='no' AND p.status='published' 
		$where
		GROUP BY MONTH(p.updated_at),d.user_id";
		if(($res=$this->getQuery($query,true))!=NULL)
			return $res;
		else
			return NULL;
	}
	
	function getProdSeoMissions($search=NULL,$type="")
	{
		$where = "";
		if($search['contract_id'])
		$where .= " AND qc.quotecontractid='".$search['contract_id']."'";
		
		if($search['assigned_to'])
		$where .= " AND cm.assigned_to='".$search['assigned_to']."'";
		
		if($search['cmid'])
		$where .= " AND cm.contractmissionid='".$search['cmid']."'";
		
		if($search['type'])
		$where .= " AND cm.type='".$search['type']."'";
		
		if($search['mission_type'])
		$where .= $search['mission_type'];
		
		/*if($search['cm_status'])
		$where .= " AND cm.cm_status='".$search['cm_status']."'";*/
		
		if($search['product'])
		$where .= " AND qm.product='".$search['product']."'";
	
		if($search['product_type'])
		$where .= " AND qm.product_type='".$search['product_type']."'";
		

		if($search['cm_status']=='' || $search['cm_status']=='all')
			$where.= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed' OR cm.cm_status ='ongoing')";
		elseif($search['cm_status']=='ongoing')
			$where.= " AND (cm.cm_status ='ongoing' AND cm.cm_status != 'validated' AND cm.cm_status != 'closed') ";
		else
			$where.= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed') ";
		
		/* Contract status */
		$where .= " AND (qc.status='validated' OR qc.status='closed' OR qc.status='deleted') ";
		$query = "SELECT qc.quotecontractid,cm.contractmissionid,cm.type,qm.identifier as qmid,qm.product,qm.product_type,qm.language_source,qm.language_dest,cm.validated_at,DATE_FORMAT( cm.validated_at, '%Y' ) as validatedyear,DATE_FORMAT( cm.validated_at, '%Y-%m' ) as validatedyearmonth,DATE_FORMAT( cm.validated_at, '%m' ) as validatedmonth,qm.turnover as missionturnover,qm.unit_price,qm.internal_cost,cm.external_mission,cm.freelance_name,cm.freelance_cost,qm.from_contract,qm.is_edited,qm.updated_at,cm.cm_status,cm.updated_at as cmupdated_at,
		cm.freeze_start_date,cm.freeze_end_date,DATE_FORMAT( cm.freeze_start_date, '%Y' ) as year_freeze_start_date,DATE_FORMAT( cm.freeze_end_date, '%Y' ) as year_freeze_end_date,cm.assigned_to,assigned_name.first_name,assigned_name.last_name
			FROM `QuoteContracts` qc
			JOIN Quotes q ON q.identifier = qc.quoteid 
			JOIN  QuoteMissions qm ON qm.quote_id = q.identifier
			LEFT JOIN ContractMissions cm ON cm.type_id = qm.identifier AND cm.contract_id = qc.quotecontractid
			JOIN User u ON u.identifier=qm.created_by 
			JOIN UserPlus up ON up.user_id = u.identifier
			JOIN Client c ON c.user_id = q.client_id
			JOIN UserPlus client ON client.user_id = q.client_id 
			JOIN User clientuser ON clientuser.identifier= q.client_id
			LEFT JOIN UserPlus assigned_name ON assigned_name.user_id = cm.assigned_to
			WHERE q.client_id NOT IN ('130425154610169') AND qm.include_final='yes' $where
			ORDER BY qc.quotecontractid DESC, field(qm.misson_user_type, 'sales', 'seo'), qm.identifier ASC";

			//echo $query; exit;
			
		if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return array();
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
		if($searchParameters['product'] && $searchParameters['p_type']=="")
			$condition.=" AND t.title ='".$searchParameters['product']."'";
		elseif($searchParameters['p_type'] && $searchParameters['product']=="")
			$condition.=" AND t.title ='".$searchParameters['p_type']."'";
		elseif($searchParameters['p_type'] && $searchParameters['product'])
			$condition.=" AND t.title IN('".$searchParameters['p_type']."','".$searchParameters['product']."')";
		if($searchParameters['assigned_to'])
			$to= " AND cm.assigned_to='".$searchParameters['assigned_to']."'";

		if($searchParameters['cm_status']=='all' || $searchParameters['cm_status']=='')
			$condition.= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed' OR cm.cm_status ='ongoing')";
		elseif($searchParameters['cm_status']=='ongoing')
			$condition.= " AND (cm.cm_status ='ongoing' AND cm.cm_status != 'validated' AND cm.cm_status != 'closed') ";
		else
			$condition.= " AND (cm.cm_status ='validated' OR cm.cm_status = 'closed') ";

		$join = " LEFT JOIN ContractMissions cm ON cm.type_id=t.identifier AND cm.contract_id='".$searchParameters['contract_id']."' $to
		LEFT JOIN UserPlus up ON up.user_id = cm.assigned_to
		";
		$missionQuery="SELECT up.first_name,up.last_name,cm.contractmissionid,cm.assigned_to,cm.cm_status,cm.external_mission,cm.freelance_name,cm.freelance_cost,cm.validated_at,DATE_FORMAT( cm.validated_at, '%Y' ) as validatedyear,DATE_FORMAT( cm.validated_at, '%Y-%m' ) as validatedyearmonth,DATE_FORMAT( cm.validated_at, '%m' ) as validatedmonth,t.*
							FROM TechMissions t	
							$join
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
	
	
	// split/month
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
				$where .=" AND qc.sales_creator_id='".$search['sales_id']."'";
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
					  WHERE q.client_id NOT IN ('130425154610169','150810162747837') AND qc.status NOT IN ('deleted','sales' ) $where
					  ORDER BY qc.quotecontractid DESC";
            //qc.status = 'validated' OR qc.status='closed' OR qc.status='deleted'
			//echo $query;	
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
			
			
		}
		
		
		function getMissionDetails($mission_id,$search=NULL)
		{
			$where = $select = $join = '';
			if($search['product'])
				{
				$where .= " AND product='".$search['product']."'";
				}
			if($search['cid'])
				{
				if($search['assigned_to'])	$to= " AND cm.assigned_to='".$search['assigned_to']."'";
				
				if( $search['cm_status']=='' ||  $search['cm_status']=='all')
					$where.= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed' OR cm.cm_status ='ongoing')";
				elseif($search['cm_status']=='ongoing')
					$where.= " AND cm.cm_status ='ongoing' AND cm.cm_status != 'validated' AND cm.cm_status != 'closed'";
				else
					$where.= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed') ";

				$select = ",cm.assigned_to,up.first_name,up.last_name,cm.cm_status";
				$join = "LEFT JOIN ContractMissions cm ON cm.type_id = QuoteMissions.identifier AND cm.contract_id ='".$search['cid']."' $to
				LEFT JOIN UserPlus up ON up.user_id = cm.assigned_to";
				}
			$query="SELECT *,IF(product='smo_audit',cost,(IF(product='seo_audit',cost,internal_cost))) as internal_cost $select  FROM QuoteMissions $join WHERE identifier='".$mission_id."' $where";
			//echo $query; exit;
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
			
			
		}
			
			public function getTechMission($id,$cid = "",$assigned_to="")
			{
			$select = $join =$to= "";
			$where="";
			if($cid)
			{
				 if($assigned_to)	$to= " AND cm.assigned_to='".$assigned_to."'";
				$select = ",cm.assigned_to,up.first_name,up.last_name";
				$join = " LEFT JOIN ContractMissions cm ON cm.type_id=TechMissions.identifier AND cm.contract_id='".$cid."'
						  LEFT JOIN UserPlus up ON up.user_id = cm.assigned_to";
						 
			}
			
				$query = "SELECT TechMissions.* $select from TechMissions $join WHERE identifier='".$id."' $to" ;
			
			//echo $query; exit;
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
			
		
		function getSplitTurnoversclients($search=Null)
		{

			$where="";
			if($search['contract_id']){
				$where.=" AND csv.contract_id='".$search['contract_id']." '";
				}
			if($search['name'] && $search['product']==""){
				$where.=" AND csv.name='".$search['name']."'";
				} elseif($search['product'] && $search['name']=="" && $search['product']!='Traduction' && $search['product']!='R&eacute;daction'){
					$where .=" AND csv.name='".$search['product']."'";
				}elseif($search['product']!='' && $search['name']!=''){
					$where.=" AND name IN ('".$search['product']."','".$search['name']."')";
						}elseif($search['mission_id']){
						$where.=" AND csv.mission_id='".$search['mission_id']."'";	
						}

				if($search['pm'] || $search['cm_status'])
				{
					if($search['assigned_to']) 
						$where.="AND cm.assigned_to='".$search['assigned_to']."'";

					if($search['cm_status']=='' ||  $search['cm_status']=='all')
						$where.= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed' OR cm.cm_status ='ongoing')";
					elseif( $search['cm_status']=='ongoing')
						$where.= " AND cm.cm_status ='ongoing' AND cm.cm_status != 'validated' AND cm.cm_status != 'closed'";
					else
						$where.= " AND (cm.cm_status = 'validated' OR cm.cm_status = 'closed') ";

							
				$select=$join='';
				$join = "JOIN ContractMissions cm ON cm.type_id=csv.mission_id AND cm.contract_id=csv.contract_id
						JOIN UserPlus up ON up.user_id = cm.assigned_to";
				$query = "SELECT up.first_name,up.last_name,cm.*,csv.* FROM ContractSplitValues as csv $join WHERE 1=1 and csv.mission_id !='' $where";
				}
				else
				{
				$query = "SELECT * FROM ContractSplitValues as csv WHERE 1=1 and csv.mission_id !='' $where";	
				}
			//echo $query; exit;
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

							
			$query = "SELECT * FROM ContractSplitValues WHERE 1=1 and mission_id !=''  $where ";
			
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
		
		function clientQuotesdetails($sales_id)
		{
			
			$query="select q.sales_review,IF( q.final_turnover, q.final_turnover, q.sales_suggested_price ) AS turnover
			 FROM Quotes  q where q.quote_by='".$sales_id."' ";
			
			 if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
			
		}
		
		function insertSplitTurnovers($save)
		{
			$this->_name =  'ContractSplitValues';
			$this->insertQuery($save);
			return true;
		}
		
	  function getSplitTurnovers($cid)
		{
                $query = "SELECT CP . * , CM.`currency` as ind_currency
                FROM ContractSplitValues AS CP
                JOIN `ContractMissions` AS CM ON CM.`type_id` = CP.`mission_id`
                AND CP.contract_id = CM.contract_id
                WHERE CP.contract_id = '".$cid."'
                AND CP.mission_id != ''
                ORDER BY TYPE , month_year";
            //echo $query;
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}
	


		/* Theorical based on the assigned missoin */

		function getClientContractMissions($search=NULL)
		{
						
			$where="";
				if($search['client_id'])
				{
				$where .= " AND q.client_id='".$search['client_id']."'";
				}
				if($search['sales_id'])
				{
				$where .=" AND qc.sales_creator_id='".$search['sales_id']."'";
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
					  WHERE (qc.status='validated' OR qc.status='closed' OR qc.status='deleted') $where 
					  ORDER BY qc.quotecontractid DESC";
			//echo $query;	
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
			
			
		}


		function checkingContract($assigned_val)
		{

				$query="SELECT cm.contract_id,q.client_id 
				FROM ContractMissions as cm 
				JOIN QuoteContracts qc ON qc.quotecontractid=cm.contract_id 
				JOIN Quotes q ON q.identifier = qc.quoteid
				JOIN Client c ON c.user_id = q.client_id
				where cm.assigned_to='".$assigned_val."' AND (qc.status='validated' OR qc.status='closed' OR qc.status='deleted')";
			//echo $query; exit;
			 if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return false;
		}

    /* *** added on 04.02.2016 *** */
    /* To get Extraction of the previous month */
    function getExtract($first_date,$last_date)
    {
        if(date('Y-m',strtotime($last_date)) < date('Y-m',strtotime('2016-02-01')) ){
            $condition = "p.status IN ('published') AND DATE(p.updated_at) >= '".$first_date."' AND DATE(p.updated_at) <= '".$last_date."'";
        }
        else{
            $condition = "p.status IN ('under_study','disapproved','published','plag_exec','disapproved_temp','disapproved_client')  AND a.delivered='yes' AND DATE(a.delivered_updated_at) >= '".$first_date."' AND DATE(a.delivered_updated_at) <= '".$last_date."'";
        }
        $query = "SELECT count(a.id) as deliveredcount,(count(p.id)) as part_count,ROUND(sum(a.files_pack),2) as tot_pub_art,CONCAT(up.first_name,up.last_name) as owner,c.company_name as client,
		qc.contractname,qm.product,qm.product_type,qm.language_source,qm.language_dest,qm.unit_price,q.sales_suggested_currency,sum(a.price_final) as total_price,
		DATE_FORMAT(qc.expected_end_date,'%d-%m-%Y') as enddate,qm.margin_percentage,(sum(p.price_user)+IF(sum(cp.price_corrector)>0,sum(cp.price_corrector),0)) as production_cost,
		ROUND(sum(p.price_user),2) as writer_cost,ROUND(sum(cp.price_corrector),2) as proofreader_cost,qc.quotecontractid,qm.quote_id,d.contract_mission_id,
		qm.tempo,qm.oneshot
		FROM Article a
		JOIN Participation p ON p.article_id = a.id
		LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
		JOIN Delivery d ON d.id = a.delivery_id
		JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id
		JOIN QuoteContracts qc ON qc.quotecontractid = cm.contract_id
		JOIN Quotes q ON q.identifier = qc.quoteid
		JOIN QuoteMissions qm ON qm.quote_id = q.identifier
		JOIN User u ON u.identifier = cm.assigned_to
		JOIN UserPlus up ON up.user_id = u.identifier
		JOIN User u1 ON u1.identifier = q.client_id
		JOIN Client c ON c.user_id = u1.identifier
		WHERE $condition
		AND p.cycle=0 AND d.missiontest = 'no'

		 AND cm.type_id = qm.identifier
		GROUP BY cm.contractmissionid
		";
        //echo $query;exit;
        //p.status = 'published'
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return NULL;
    }
}
?>
