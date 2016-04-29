<?php

/**
 * @package Quote Extract
 * @version 1
 */
 
 class Ep_Quote_Extract extends Ep_Db_Identifier
 {


 	/*get histiry BO details from contract Missions*/
	function getProofreadingCost($search)
	{	
		$conditions="";

		if($search['year'])
			$conditions .=" AND YEAR(q.signed_at)= '".$search['year']."' ";

		$query = "SELECT qm.product as mission_product,qm.language_dest,qm.language_source,(pm.cost*qm.volume) as totalprice,pm.*,qc.expected_launch_date,c.company_name as Client,DATE_FORMAT(qc.expected_launch_date, '%Y-%m' ) as yearmonth,DATE_FORMAT(qc.expected_launch_date, '%Y' ) as year,qm.mission_length,q.sales_suggested_currency from ProdMissions as pm JOIN QuoteMissions qm ON qm.identifier=pm.quote_mission_id JOIN Quotes q ON q.identifier=qm.quote_id JOIN QuoteContracts as qc ON qc.quoteid=q.identifier JOIN User u ON u.identifier=q.client_id LEFT JOIN Client c ON c.user_id=u.identifier WHERE pm.product='proofreading' AND q.sales_review='signed' $conditions HAVING totalprice > 0 ";

		//echo $query; exit;
				 
		if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return false;
	}
 }

 ?>