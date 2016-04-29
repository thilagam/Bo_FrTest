<?php
/* Quotes and CMD stats */

class Ep_Quote_Stats extends Ep_Db_Identifier
{
	//recently signed quotes
	function recentlySignedQuotes()
	{
		$signedQuery="SELECT q.* ,c.company_name
				From Quotes q
				INNER JOIN Client c ON q.client_id=c.user_id
				WHERE q.sales_review='signed'
				ORDER BY signed_at DESC LIMIT 0,5
				";

		if(($signedQuotes = $this->getQuery($signedQuery,true)) != NULL)
            return $signedQuotes;
        else
            return NULL;

	}

	//Contract to be finished this month
	function monthlyContracts()
	{
		$contractQuery="SELECT qc.*,q.sales_suggested_currency,q.final_turnover,q.client_id,c.company_name
				From QuoteContracts qc
				JOIN Quotes q ON q.identifier=qc.quoteid
				INNER JOIN Client c ON q.client_id=c.user_id
				WHERE date_format(qc.expected_end_date, '%Y-%m')>=date_format(now(), '%Y-%m') AND date_format(qc.expected_end_date, '%Y-%m')<=date_format(NOW() + INTERVAL 2 MONTH, '%Y-%m')
				ORDER BY qc.expected_end_date ASC
				";

		if(($monthlyContracts = $this->getQuery($contractQuery,true)) != NULL)
            return $monthlyContracts;
        else
            return NULL;
	}

	//total number of active clients
	function getClientCount()
	{
		$query="SELECT count(*) as clients
			   FROM User
			   where Status='Active' AND type='client'
				";
		
		if($userCount=$this->getQuery($query,true))
        {
           return $userCount[0]['clients'];
        }
        else
             return 0;

	}	

	//ongoing contracts count
	function ongoingContractsCount()
	{
		/*$query="SELECT count(*) as contracts
			   FROM QuoteContracts
			   where status='validated'
				"; */
		$query="SELECT AVG(cm.progress_percent) as percent, qc.`contractname` as contracts
				FROM QuoteContracts qc 
				LEFT JOIN ContractMissions cm ON cm.contract_id=qc.quotecontractid
				where qc.status='validated'
				GROUP BY qc.quotecontractid
				HAVING percent<100 OR percent IS NULL
				";
				
		
		if($ongContractsCount=$this->getQuery($query,true))
        {
           return count($ongContractsCount);//$ongContractsCount[0]['contracts'];
        }
        else
             return 0;
	}

	//ongoing contract missions missions count
	function ongoingContractMissionsCount()
	{
		$query="SELECT count(*) as missions,cm.type
			   FROM ContractMissions cm
               JOIN QuoteContracts qc ON cm.contract_id=qc.quotecontractid
			   WHERE cm.cm_status='ongoing'
			   GROUP BY cm.type 
				";
		
		if($ongContractMissionsCount=$this->getQuery($query,true))
        {
           return $ongContractMissionsCount;
        }
        else
             return 0;
	}

	//ca of current month signed quotes
	function monthlyCASignedQuotes()
	{
		$signedQuery="SELECT SUM(final_turnover) as ca_month,sales_suggested_currency
				From Quotes
				WHERE sales_review='signed' AND date_format(signed_at, '%Y-%m')=date_format(now(), '%Y-%m')	
				GROUP BY sales_suggested_currency			
				";

		if(($signedQuotes = $this->getQuery($signedQuery,true)) != NULL)
            return $signedQuotes;
        else
            return NULL;
	}

	//open contracts turnover for the current year
	function CAOpenedcontractofCurrentYear()
	{
		$contractCAQuery="SELECT SUM(qc.turnover) as ca_year,q.sales_suggested_currency
						From QuoteContracts qc
						JOIN Quotes q ON q.identifier=qc.quoteid
						WHERE date_format(qc.expected_launch_date, '%Y')=date_format(now(), '%Y')
						GROUP BY sales_suggested_currency				
						";	

		if(($CAofYear = $this->getQuery($contractCAQuery,true)) != NULL)
            return $CAofYear;
        else
            return NULL;
	}

	//All team members that are currently assigned + # of missions 
	function assignedTeamMembers()
	{
		$membersQuery="SELECT u.identifier,up.first_name,up.last_name,count(u.identifier) as num_missions
						FROM User u
						JOIN  ContractMissions cm ON cm.assigned_to=u.identifier
						LEFT JOIN UserPlus up ON up.user_id=u.identifier
						WHERE cm.cm_status NOT IN ('validated','closed')
						GROUP BY u.identifier
						ORDER BY num_missions DESC
						";
		if(($assignedTeamMembers = $this->getQuery($membersQuery,true)) != NULL)
            return $assignedTeamMembers;
        else
            return NULL;				

	}

}