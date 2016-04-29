<?php
/**
 * Ep_Quote_Invoice
 * @package Quote
 * @version 1.0
 */ 
	class Ep_Quote_Invoice	extends Ep_Db_Identifier
	{
		protected $_name = 'ContractMissionInvoice';
		
		function getIdentifier()
		{
			return number_format(microtime(true),0,'','').mt_rand(10000,99999);
		}  
		// To generate Monthly Invoice
		function generateMonthly()
		{
			$query = "SELECT cm.contractmissionid,cm.invoice_per,cm.contract_id,cm.unit_price,cm.currency,up.first_name,up.last_name,a.title,cm.unit_price,a.id as artid,d.user_id,cl.ca_number,cl.client_code FROM ContractMissions cm 
					  JOIN Delivery d ON d.contract_mission_id = cm.contractmissionid
					  JOIN Article a ON a.delivery_id = d.id
					  JOIN User u ON u.identifier = d.user_id
					  JOIN UserPlus up ON up.user_id = d.user_id
					  LEFT JOIN Client cl ON cl.user_id = d.user_id
					  WHERE cm.invoice_per='month' AND a.progressbar_percent=100 AND a.id NOT IN (SELECT article_id FROM ContractMissionInvoiceDetails WHERE article_id IS NOT NULL ) AND LAST_DAY(CURDATE()) = CURDATE() ORDER BY cm.contractmissionid DESC";
			if(($res=$this->getQuery($query,true))!=NULL)
				return $res;
			else
				return NULL;
		}
		// To get unique Invoice Number
		function getInvoiceNumber($format,$cid)
		{
			/* do
			{
				$id = $this->getRandInvoice($format,$cid);
				$query = "SELECT * FROM ContractMissionInvoice WHERE invoice_number='".$id."'";
				if(($count=$this->getNbRows($query))==0)
				break;
			}
			while(true);
			return $id; */
			return $this->getRandInvoice($format,$cid);
		}	
		// To get Random Invoice Id
		function getRandInvoice($format,$cid)
		{
			/* $str = mt_rand(10000,99999);
			$format = $format."-".substr($str,0,4)."-".date('y-m');
			return $format; */
			$query = "SELECT count(*) as count FROM ContractMissionInvoice WHERE contract_id='".$cid."' AND date_format(created_at, '%Y-%m')=date_format(now(), '%Y-%m')";
			$res=$this->getQuery($query,true);
			$count = $res[0]['count']+1;
			return $format."-".date('Y-m')."-".$count;
		}
		// To Insert Invoice 
		function insertInvoice($save)
		{
			$this->_name =  'ContractMissionInvoice';
			$save['invoice_id'] = $this->getUniqueIdentifier('ContractMissionInvoice','invoice_id');
			$this->insertQuery($save);
			return $save['invoice_id'];
		}
		// Get UniqueIdentifier
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
		// To Insert Invoice Details
		function insertInvoiceDetails($save)
		{	
			$this->_name =  'ContractMissionInvoiceDetails';
			$save['invoice_detail_id'] = $this->getUniqueIdentifier('ContractMissionInvoiceDetails','invoice_detail_id');
			$this->insertQuery($save);
			return $save['invoice_detail_id'];
		}
		//TO Fetch Invoice
		function getInvoices($search = NULL)
		{
			if($search['contract_id'])
				$where = " AND cmi.contract_id='".$search['contract_id']."'";
			if($search['final_invoice'])
			{
				$where .= " AND cmi.is_final=$search[final_invoice]";
				$select = "";
				$join = "";
			}
			else
			{
				$join = " JOIN ContractMissions cm ON cm.contractmissionid = cmi.cmid 
					  JOIN QuoteMissions qm ON qm.identifier = cm.type_id ";
				$select = ",qm.product,qm.product_type,qm.product_type_other,qm.language_source,qm.language_dest ";
			}
			$query = "SELECT cmi.*$select FROM ContractMissionInvoice cmi
					  $join
					  WHERE 1=1 AND archive=0 $where
					 ";
	
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
		//To generate Delivery Invoices with 100% completion
		function generateDelivery()
		{
			$query = "SELECT count(distinct a.id) as totalArticle, 
			(SELECT count(DISTINCT pa.article_id) FROM Participation pa INNER JOIN Article a3 ON a3.id=pa.article_id WHERE a3.delivery_id=d.id and pa.status='published') as published_articles, 
			(SELECT count(pa.id) as partIds FROM Delivery d2 INNER JOIN Article a4 ON d2.id=a4.delivery_id INNER JOIN Participation pa ON a4.id=pa.article_id WHERE d2.id=d.id) as totalParticipations,  
			(Select count(*) FROM Participation p INNER JOIN Article a1 ON p.article_id=a1.id INNER JOIN Delivery d1 ON a1.delivery_id=d1.id WHERE d1.id=d.id and marks IS NOT NULL and marks!='' ) as article_count_marks, sum(a.progressbar_percent) as progress, 
			d.id as did,cm.contractmissionid,cm.invoice_per,cm.unit_price,cm.volume,cm.contract_id,cm.currency,d.title,d.user_id,c.ca_number,c.client_code,up.first_name,up.last_name FROM Delivery d 
			INNER JOIN Article a ON d.id=a.delivery_id JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id 
			INNER JOIN User u ON u.identifier=d.user_id
			JOIN UserPlus up ON up.user_id = d.user_id
			LEFT  JOIN Client c ON u.identifier=c.user_id
			WHERE 1=1 AND cm.invoice_per='delivery' AND d.id NOT IN (SELECT delivery_id FROM ContractMissionInvoiceDetails WHERE delivery_id IS NOT NULL ) Group BY d.id Having totalArticle = published_articles AND totalParticipations >0 ORDER BY cm.contractmissionid DESC";
			
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
		//To generate Mission invoice with all delivery completion
		function generateMission()
		{
			$query ="SELECT d.id as did,d.title,d.user_id,cm.contractmissionid,cm.invoice_per,cm.unit_price,cm.volume,cm.contract_id,cm.currency,c.ca_number,c.client_code,up.first_name,up.last_name FROM Delivery d
			JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id
			INNER JOIN User u ON u.identifier=d.user_id
			JOIN UserPlus up ON up.user_id = d.user_id
			LEFT  JOIN Client c ON u.identifier=c.user_id
			WHERE cm.cm_status = 'validated' AND cm.invoice_per='mission' AND d.id NOT IN (SELECT delivery_id FROM ContractMissionInvoiceDetails WHERE delivery_id IS NOT NULL ) ORDER BY cm.contractmissionid DESC
					";
			if(($result = $this->getQuery($query,true))!=NULL)
				return $result;
			else
				return NULL;
		}
		
		function updateInvoice($data,$identifier)
		{
			$where="invoice_id='".$identifier."'";
			$this->updateQuery($data,$where);
		}
	}
?>