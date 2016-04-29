<?php

/**
 * @package Quote Cron
 * @version 1
 */
 
 class Ep_Quote_Cron extends Ep_Db_Identifier
 {
	
	protected $_name = 'Quotes';
		 
	function getQuote($search="")
	{
		$where = "";
		if($search['sales_status'])
		$where .=" AND q.sales_review='".$search['sales_status']."'";
		if($search['sign_expire_timeline'])
		$where .=" AND q.sign_expire_timeline<='".$search['sign_expire_timeline']."'";
		
		$query = "SELECT * FROM Quotes q
				  WHERE 1=1 
				  $where
				 ";
		
		if(($res=$this->getQuery($query,true))!=NULL)
				return $res;
			else
				return NULL;
	}
	
	public function updateQuote($data,$identifier)
	{
		$where="identifier='".$identifier."'";
		$this->updateQuery($data,$where);
	}
	
	function getLateQuotes($search=NULL)
	{
		$where = "1=1";
		$from_time = time()-60*60;
		$to_time = time();
		$from_date_time = date('Y-m-d H:i:s');
		$to_date_time = date('Y-m-d H:i:s',strtotime('-1 hour'));
	
		if($search['tech_review'])
		{
			$where = "( q.tec_review='not_done' AND q.response_time<='".$to_time."' AND q.response_time>'".$from_time."') OR (q.tec_review='challenged' AND q.tech_timeline<'".$from_date_time."' AND q.tech_timeline>='".$to_date_time."')";
		}
		
		if($search['seo_review'])
		{
			$where = "( q.seo_review='not_done' AND q.response_time<='".$to_time."' AND q.response_time>'".$from_time."') OR (q.seo_review='challenged' AND q.seo_timeline<'".$from_date_time."' AND q.seo_timeline>='".$to_date_time."')";
		}
		
		if($search['prod_review'])
		{
			 $where = "( q.	prod_review='challenged' AND q.prod_timeline<='".$to_time."' AND q.prod_timeline>'".$from_time."' )";
		}
		
		if($search['sales_review'])
		{
			 $where = "( q.sales_review='not_done' AND q.sales_validation_expires<='".$to_time."' AND q.sales_validation_expires>'".$from_time."' )";
		}
		
		$query = "SELECT * FROM Quotes q
				  WHERE 
				  $where
				 ";
		//echo $query;exit;
		
		if(($res=$this->getQuery($query,true))!=NULL)
			return $res;
		else
			return NULL;
	}

	//function to get repeat deliveries info to create deliveries automatically
	function getRepeatDeliveries($delivery_id=NULL)
	{
		if($delivery_id)
		{
			$condition=" AND delivery_id='".$delivery_id."'";
		}

		$repeatQuery="SELECT * FROM DeliveryRepeat
						WHERE enabled='yes' AND repeat_start <= (CURDATE()+1) $condition
						Having end_date is NULL OR end_date >=CURDATE()
						ORDER BY repeat_start ASC
					";
		if(($repeatDeliveries=$this->getQuery($repeatQuery,true))!=NULL)
			return $repeatDeliveries;
		else
			return NULL;			
	}
	//get all details of a delivery
	function getDeliveryDetails($delivery_identifier)
	{	
		$deliveryQuery="SELECT * FROM Delivery WHERE id='".$delivery_identifier."'";
		//echo $deliveryQuery;exit;
		if(($deliveryDetails=$this->getQuery($deliveryQuery,true))!=NULL)
			return $deliveryDetails;
		else
			return NULL;
	}
	//get all details of a delivery
	function getArticleDetails($delivery_identifier)
	{

		$articleQuery="SELECT * FROM Article WHERE delivery_id='".$delivery_identifier."'";
		
		//echo $articleQuery;exit;

		if(($articleDetails=$this->getQuery($articleQuery,true))!=NULL)
			return $articleDetails;
		else
			return NULL;
	}

	function getContribUserDetails($user)
	{
		$senderQuery="select first_name,last_name, email from User u
		                    LEFT JOIN UserPlus up ON u.identifier=up.user_id
		                    LEFT JOIN Contributor c ON u.identifier=c.user_id
		                    where identifier='".$user."'";
		//echo $senderQuery;exit;
		if(($result=$this->getQuery($senderQuery,true))!=NULL)
		{
			return $result;
		}

	}

	/**cron function to Get all participation that are not submitted the articles with in time expires***/
    public function getWriterArticleSubmissionExpires()
    {
        $articleQuery="SELECT a.title as article,p.id,p.price_user,u.profile_type,p.user_id,p.article_id,d.id as delivery,d.title,
                        d.created_user,d.user_id as client,d.premium_option
                       FROM Participation p
                   INNER JOIN Article a ON a.id=p.article_id
                   INNER JOIN Delivery d ON a.delivery_id=d.id
                   INNER JOIN User u ON p.user_id=u.identifier
                   WHERE p.status in ('bid') and d.missiontest='no' AND p.article_submit_expires!=0 AND p.article_submit_expires < UNIX_TIMESTAMP()
                   and p.article_submit_expires >= (UNIX_TIMESTAMP()-(60*60))
                    
                   GROUP BY p.id,a.id
                        ORDER BY p.article_submit_expires ASC
                   ";
                   
        //echo $articleQuery;exit;
        if(($result = $this->getQuery($articleQuery,true))!= NULL)
            return $result;
        else
            return "NO";
    }

    /**cron function to Get all participation that are not submitted the articles with in time expires***/
    public function getCorrectorArticleSubmissionExpires()
    {
        $articleQuery="SELECT a.title as article,p.id,p.price_corrector,u.profile_type2,p.corrector_id,p.article_id,d.id as delivery,d.title,
                        d.created_user,d.user_id as client,d.premium_option
                       FROM  CorrectorParticipation p
                   INNER JOIN Article a ON a.id=p.article_id
                   INNER JOIN Delivery d ON a.delivery_id=d.id
                   INNER JOIN User u ON p.corrector_id=u.identifier
                   WHERE p.status in ('bid') AND p.corrector_submit_expires!=0 AND p.corrector_submit_expires < UNIX_TIMESTAMP()                    
                   	and d.missiontest='no' and p.corrector_submit_expires >= (UNIX_TIMESTAMP()-(60*60))
                   GROUP BY p.id,a.id
                        ORDER BY p.corrector_submit_expires ASC
                   ";
                   
        //echo $articleQuery;exit;
        if(($result = $this->getQuery($articleQuery,true))!= NULL)
            return $result;
        else
            return "NO";
    }

    /**cron function to Get all article with Bid time over and no participations*/
    public function getBidOverArticles()
    {
        $articleQuery="SELECT a.title,a.id, (Select count(p.id) as count From Participation p Where p.article_id=a.id)as participation_count
						FROM Article a
						WHERE a.participation_expires < UNIX_TIMESTAMP() AND a.participation_expires >= (UNIX_TIMESTAMP()-(60*60))
						Having participation_count=0
						Order By a.participation_expires ASC
                   ";
                   
                   
        //echo $articleQuery;exit;
        if(($result = $this->getQuery($articleQuery,true))!= NULL)
            return $result;
        else
            return "NO";
    }
	
	/* To get Deliveries with running late */
	function deliveryLate()
	{
		$query = "SELECT  d.id,d.title,d.user_id as client_id,d.published_at,count(distinct a.id) as totalArticle,
					(SELECT count(DISTINCT pa.article_id) 
					FROM Participation pa INNER JOIN Article a3 ON a3.id=pa.article_id 
					WHERE a3.delivery_id=d.id and pa.status='published') as published_articles, 
					(SELECT count(DISTINCT pa.article_id) 
					FROM Participation pa INNER JOIN Article a ON a.id=pa.article_id 
					WHERE a.delivery_id=d.id and (pa.status='closed' OR pa.status='closed_client')) as closed_articles,
					(SELECT count(pa.id) as partIds FROM Delivery d2 INNER JOIN Article a4 ON d2.id=a4.delivery_id INNER JOIN Participation pa ON a4.id=pa.article_id WHERE d2.id=d.id) as totalParticipations,(SELECT max(proofread_end) FROM Article WHERE delivery_id=d.id) as proofread_end, ((SELECT max(participation_time) FROM Article WHERE delivery_id=d.id)+ (SELECT max(selection_time) FROM Article WHERE delivery_id=d.id )+ (SELECT max(GREATEST(senior_time,junior_time,subjunior_time)) FROM Article WHERE delivery_id=d.id )) as total_time,
					cm.assigned_to,cm.assigned_by
					FROM Delivery d INNER JOIN Article a ON d.id=a.delivery_id INNER JOIN User u ON u.identifier=d.user_id LEFT JOIN Client c ON u.identifier=c.user_id JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id JOIN QuoteMissions qm ON qm.identifier = cm.type_id JOIN QuoteContracts qc ON cm.contract_id = qc.quotecontractid JOIN Quotes q ON qc.quoteid = q.identifier 
					WHERE d.missiontest='no'
					Group BY d.id 
					HAVING totalArticle >(published_articles+closed_articles) AND IF(proofread_end,proofread_end >= NOW() AND proofread_end<DATE_ADD(NOW(),INTERVAL 1 HOUR),total_time) ORDER BY d.created_at DESC ";
		 //echo $articleQuery;exit;
        if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return NULL;
	}
	
	// Bidding over
	function biddingOver()
	{
		/* JOIN QuoteMissions qm ON qm.identifier = cm.type_id 
					JOIN QuoteContracts qc ON cm.contract_id = qc.quotecontractid 
					JOIN Quotes q ON qc.quoteid = q.identifier  */
		$query = "SELECT  d.id,d.title,d.user_id as client_id,d.published_at,count(distinct a.id) as totalArticle,
					(SELECT MAX( participation_expires) as maxtime from Article a2 WHERE a2.delivery_id = d.id) as maxparticipation,
					(SELECT count(DISTINCT pa.article_id) 
					FROM Participation pa INNER JOIN Article a ON a.id=pa.article_id 
					WHERE a.delivery_id=d.id and (pa.status='bid_premium') AND pa.current_stage='contributor') as particpation_count,cm.assigned_by
					FROM Delivery d 
					INNER JOIN Article a ON d.id=a.delivery_id 
					JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id 
					WHERE d.missiontest='no'
					Group BY d.id 
					HAVING maxparticipation <'".time()."' AND maxparticipation>='".(time()-3600)."'
					ORDER BY d.created_at DESC";
			
		if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return NULL;
	}
	
	// Article Delay
	function getDelayArticle()
	{
		$query = "SELECT d.title,d.id,d.user_id as client_id,p.user_id as contrib_id,up.first_name,up.last_name,cm.assigned_by
				FROM Participation p 
				JOIN Article a ON a.id = p.article_id 
				JOIN Delivery d ON d.id = a.delivery_id 
				JOIN User u ON u.identifier = p.user_id 
				LEFT JOIN UserPlus up ON u.identifier = up.user_id 
				JOIN ContractMissions cm ON cm.contractmissionid = d.contract_mission_id 
				WHERE p.article_submit_expires<'".time()."' AND p.article_submit_expires>='".(time()-3600)."' AND p.status='bid' AND p.current_stage='contributor' ";
		if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return NULL;
	}
	
	// Contracts Late 
	function getLateContracts()
	{
		$query = "SELECT qc.*,if(round(avg(cm.progress_percent)),round(avg(cm.progress_percent)),0) as percentage,q.created_by as quote_by
				  FROM QuoteContracts qc 
				  JOIN ContractMissions cm ON cm.contract_id = qc.quotecontractid 
				  JOIN Quotes q ON q.identifier = qc.quoteid
				GROUP BY qc.quotecontractid
				HAVING percentage < 100 AND qc.expected_end_date >'".date('Y-m-d')."' AND qc.expected_end_date <='".date('Y-m-d',strtotime('+1 day'))."'
				 ";
		
		if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return NULL;
	}
	
	// Contract Close
	function getCloseContracts()
	{
		$query = "SELECT qc.*,if(round(avg(cm.progress_percent)),round(avg(cm.progress_percent)),0) as percentage,q.created_by as quote_by
				  FROM QuoteContracts qc 
				  JOIN ContractMissions cm ON cm.contract_id = qc.quotecontractid 
				  JOIN Quotes q ON q.identifier = qc.quoteid
				GROUP BY qc.quotecontractid
				HAVING percentage = 100 AND qc.expected_end_date >'".date('Y-m-d')."' AND qc.expected_end_date <='".date('Y-m-d',strtotime('+1 day'))."'
				 ";
		
		if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return NULL;
	}

	//quote challenge running late
	function getQuoteChallengeLate()
	{
		$query="SELECT identifier,title,quote_by,tec_review,seo_review,final_turnover,client_id,c.company_name,is_new_quote,migrated_quote FROM Quotes
		INNER JOIN Client c ON c.user_id=Quotes.client_id
				Where (tec_review='not_done' OR seo_review='not_done') AND response_time < UNIX_TIMESTAMP()
				AND response_time >= (UNIX_TIMESTAMP()-(60*60))";
//echo $query;   exit;
		if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return NULL;		
	}
	
	function getArticleCount()
	{
		// AND cm.alertemail='yes'
		$query = "SELECT count(a.id) as article_count,d.contract_mission_id,qm.oneshot,qm.quote_id,qm.tempo_length,qm.product_type,qm.product,qm.tempo_length_option,cm.assigned_at,DATE(cm.assigned_at) as assigned_date,qm.delivery_volume_option,qm.volume_max,c.company_name,IF(client.first_name!=\"\" OR client.first_name!=\"\", CONCAT(client.first_name,\" \",client.last_name),clientuser.email)  as client_name,up.first_name,up.last_name,u.email,cm.type_id,d.user_id as client_id
				  FROM ContractMissions cm
				  LEFT JOIN Delivery d ON d.contract_mission_id = cm.contractmissionid
				  JOIN QuoteMissions qm ON qm.identifier = cm.type_id
				  JOIN Article a ON a.delivery_id = d.id
				  JOIN Participation p ON p.article_id = a.id
				  LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
				  JOIN Quotes q ON q.identifier = qm.quote_id
				  JOIN Client c ON c.user_id = q.client_id
				  JOIN UserPlus client ON client.user_id = q.client_id 
				  JOIN User clientuser ON clientuser.identifier= q.client_id
				  JOIN User u ON u.identifier=cm.assigned_to 
				  JOIN UserPlus up ON up.user_id = u.identifier
				  WHERE d.contract_mission_id IS NOT NULL AND p.status='published' AND d.missiontest='no'  AND cm.alertemail='yes' AND cm.cm_status NOT IN ('validated','closed')
				  GROUP BY d.contract_mission_id
				  ";
		if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return NULL;		
	}
	
	function getTempos($cmid,$type)
	{
		$query = "SELECT * FROM TempoOneshot WHERE mission_id = '".$cmid."'";
		//echo $query;
		if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return NULL;
	}
	
	function getrelanceClient(){
		$query="SELECT q.*,c.*,	IF(q.final_turnover,q.final_turnover,q.sales_suggested_price) as turnover,
		(SELECT ql.comments FROM QuotesLog ql WHERE ql.action = 'quote_forum' AND ql.quote_id = q.identifier 
		 order by ql.action_at desc LIMIT 1) as latestcommets
                    FROM Quotes q INNER JOIN Client c ON c.user_id=q.client_id 
                                 
                     WHERE DATE(q.boot_customer)=CURDATE() ";        
        if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return NULL;
		
	}
		
	function getclosedquotes(){
		$query="SELECT q.*,c.*,IF(q.final_turnover,q.final_turnover,q.sales_suggested_price) as turnover,
		(SELECT ql.action_at FROM QuotesLog ql WHERE ql.action = 'sales_closed' AND ql.quote_id = q.identifier 
		 order by ql.action_at desc LIMIT 1)
		  AS closedate,
		  (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action = 'sales_validated_ontime' AND ql.quote_id = q.identifier 
		 order by ql.action_at desc LIMIT 1)
		  AS validateddate
		 FROM Quotes q
		  INNER JOIN Client c ON c.user_id=q.client_id  
		WHERE q.closed_reason!='quote_permanently_lost'";        
		//echo $query; exit;
        if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return NULL;
		
	}
		
	function getweeklyquotes(){
		$onemonthbefore=date('Y-m-d',strtotime('-1 month',time()));
		$onemonth7day=date('Y-m-d',strtotime('-1 month',time()-(7*86400)));
		$current_date=date('Y-m-d');
		$current_date7day=date('Y-m-d',time()-(7*86400));
		$before5day=date('Y-m-d',time()-(5*86400));
		$before20day=date('Y-m-d',time()-(20*86400));
		$before30day=date('Y-m-d',time()-(30*86400));
		
		$query="SELECT q.*,c.company_name,
		IF(q.final_turnover,q.final_turnover,q.sales_suggested_price) as turnover,
		 (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action = 'sales_closed' AND ql.quote_id = q.identifier 
		 AND DATE(ql.action_at) BETWEEN '$onemonth7day' AND '$onemonthbefore' order by ql.action_at desc LIMIT 1)
		  AS releaceraction,
		  (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action = 'sales_validated_ontime' AND ql.quote_id = q.identifier 
		 AND DATE(ql.action_at) BETWEEN '$current_date7day' AND '$current_date' order by ql.action_at desc LIMIT 1)
		  AS validateaction,
		  (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action = 'sales_closed' AND ql.quote_id = q.identifier 
		 AND DATE(ql.action_at) BETWEEN '$current_date7day' AND '$current_date' order by ql.action_at desc LIMIT 1)
		  AS closeaction,
		  (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action = 'sales_closed' AND ql.quote_id = q.identifier 
		 AND DATE(ql.action_at)= '$before5day' order by ql.action_at desc LIMIT 1)
		  AS close5dayaction,
		  (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action = 'sales_closed' AND ql.quote_id = q.identifier 
		 AND DATE(ql.action_at)= '$before20day' order by ql.action_at desc LIMIT 1)
		  AS close20dayaction,
		  (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action = 'sales_closed' AND ql.quote_id = q.identifier 
		 AND DATE(ql.action_at)= '$before30day' order by ql.action_at desc LIMIT 1)
		  AS close30dayaction,
		  (SELECT us.login FROM User us where us.identifier = q.quote_by) as bosalesuser
		FROM Quotes q 
		INNER JOIN Client c ON c.user_id=q.client_id where 1=1";     
		   
			//echo $query; exit();	
		if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return NULL;
	
	}
		
	function getquotesvalidatelog($identifier){
		$query="SELECT ql.action_at FROM
		 QuotesLog ql WHERE ql.action = 'sales_validated_ontime'
		  AND ql.quote_id ='$identifier' order by ql.action_at desc LIMIT 1";        
		if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return NULL;
	}
			
	function getquoteslastsixmonths()
	{
		$lastsixmonth=date('Y-m-d h:m:s',strtotime("-6 month"));
		$query="
		SELECT q.*,c.company_name,
			IF(q.final_turnover,q.final_turnover,q.sales_suggested_price) as turnover,
			
			(SELECT ql.action_at FROM QuotesLog ql WHERE (ql.action = 'prod_validated_ontime' OR ql.action ='prod_validated_delay' )AND ql.quote_id = q.identifier order by ql.action_at ASC LIMIT 1) AS prodontime,
			
			(SELECT ql.action_at FROM QuotesLog ql WHERE (ql.action = 'tech_validated_ontime' OR ql.action='tech_validated_delay') AND ql.quote_id = q.identifier order by ql.action_at ASC LIMIT 1)	AS techontime,
			
			(SELECT ql.action_at FROM QuotesLog ql WHERE (ql.action = 'seo_validated_delay' OR ql.action='seo_validated_ontime') AND ql.quote_id = q.identifier order by ql.action_at ASC LIMIT 1)	AS seoontime,
			
			(SELECT ql.action_at FROM QuotesLog ql WHERE ql.action = 'sales_validated_ontime' AND ql.quote_id = q.identifier AND ql.action_at iS NOT NULL order by ql.action_at ASC LIMIT 1) AS salevalidaetime,
			
			(SELECT ql.action_at FROM QuotesLog ql WHERE ql.action ='sales_closed' AND ql.quote_id = q.identifier order by ql.action_at ASC LIMIT 1) AS saleclosetime
			
			
		FROM Quotes q 
		INNER JOIN Client c ON c.user_id=q.client_id 
		where q.created_at>='$lastsixmonth'";
	
		if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return NULL;
	
	}
	/* To update mission percentage */
	function getDeliveries()
	{
		$query = "SELECT sum(a.files_pack) as published_articles,d.contract_mission_id,qm.volume,qm.identifier,cm.cm_status 
		FROM Delivery d 
		JOIN Article a ON a.delivery_id = d.id 
		JOIN Participation p ON p.article_id = a.id
		LEFT JOIN CorrectorParticipation cp ON cp.article_id = a.id AND cp.status='published'
		JOIN ContractMissions cm on cm.contractmissionid = d.contract_mission_id
		JOIN QuoteMissions qm ON qm.identifier = cm.type_id
		WHERE d.missiontest='no' AND d.contract_mission_id IS NOT NULL AND p.status='published'
		GROUP BY d.contract_mission_id";
		if(($res=$this->getQuery($query,true))!=NULL)
			return $res;
		else
			return NULL;	
	}
	/* To get Extraction of the previous month */
	function getExtract($first_date,$last_date)
	{
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
		WHERE p.status IN ('under_study','disapproved','published','plag_exec','disapproved_temp','disapproved_client') AND p.cycle=0 AND a.delivered='yes' AND d.missiontest = 'no' AND DATE(a.delivered_updated_at) >= '".$first_date."' AND DATE(a.delivered_updated_at) <= '".$last_date."' AND cm.type_id = qm.identifier 
		GROUP BY cm.contractmissionid
		";
		/*$query = "SELECT count(a.id) as deliveredcount,(count(p.id)) as part_count,ROUND(sum(a.files_pack),2) as tot_pub_art,CONCAT(up.first_name,up.last_name) as owner,c.company_name as client,
		qc.contractname,qm.product,qm.product_type,qm.language_source,qm.language_dest,qm.unit_price,q.sales_suggested_currency,sum(a.price_final) as total_price,
		DATE_FORMAT(qc.expected_end_date,'%d-%m-%Y') as enddate,qm.margin_percentage,(sum(p.price_user)+IF(sum(cp.price_corrector)>0,sum(cp.price_corrector),0)) as production_cost,
		ROUND(sum(p.price_user),2) as writer_cost,ROUND(sum(cp.price_corrector),2) as proofreader_cost,qc.quotecontractid,qm.quote_id,d.contract_mission_id 
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
		WHERE p.status IN ('under_study','disapproved','published','plag_exec','disapproved_temp','disapproved_client') AND p.cycle=0 AND a.delivered='yes' AND d.missiontest = 'no' AND DATE(p.updated_at) >= '".$first_date."' AND DATE(p.updated_at) <= '".$last_date."' AND cm.type_id = qm.identifier 
		GROUP BY cm.contractmissionid
		";*/
		//echo $query;
		if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return NULL;
	}
	/* To get Total seo turnover in Extraction */
	function getSeoTurnover($quote_id)
	{
		$where = " include_final='yes' AND product IN('seo_audit','smo_audit','content_strategy')";
		$where .= " AND quote_id='".$quote_id."'";
		$query = "SELECT SUM(IF(`package`='team',turnover+(team_fee*team_packs),turnover)) as calcturnover
		FROM `QuoteMissions`  
		WHERE $where
		";
		$result = $this->getQuery($query,true);
		return $result[0]['calcturnover'];
	}
	/* to get Freeze Missions */
	function getFreezeMissions()
	{
		$query = "SELECT cm.*,u.email 
				  FROM ContractMissions cm
				  JOIN User u ON u.identifier=cm.assigned_to
				  WHERE cm.freeze_email_date LIKE '".date('Y-m-d')."%'
				  ";
				 
		if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return false;		  
	}
	/*get only sales quotes missions*/
	function getOnlySalesQuoteMissions()
	{
		$query = "SELECT CONCAT(up.first_name,' ',up.last_name) as sales_owner,q.sales_suggested_currency as currency ,q.sales_delivery_time,qc.contractname,qc.quotecontractid,qm.* FROM QuoteMissions  qm
				JOIN Quotes q ON qm.quote_id=q.identifier
				JOIN User u ON u.identifier = q.quote_by
				JOIN UserPlus up ON up.user_id = u.identifier
				LEFT JOIN QuoteContracts qc ON qc.quoteid=q.identifier
				WHERE q.sales_review='signed' AND qm.include_final='yes' AND qm.product NOT IN ('smo_audit','seo_audit','content_strategy') AND qm.identifier NOT IN (Select quote_mission_id FROM ProdMissions)
				ORDER BY qc.contractname DESC


				  ";
		//echo $query;exit;		  
				 
		if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return false;
	}


	/*get histiry BO details from contract Missions*/
	function getHistoryBOQuoteMissions()
	{
		$query = "SELECT cm.contractmissionid,cm.type_id,qm.product,qm.product_type,qm.language_source,qm.language_dest,qm.nb_words,qm.unit_price,qm.volume,qm.oneshot,IF(cm.cm_turnover,cm.cm_turnover,qm.turnover) as Turnover,q.sales_suggested_currency as Currency,cm.history_bo,
				(SELECT cost FROM ProdMissions WHERE quote_mission_id=cm.type_id AND (product='redaction' OR product='translation') LIMIT 1) as WritingCost,
				(SELECT cost FROM ProdMissions WHERE quote_mission_id=cm.type_id AND product='proofreading'  LIMIT 1) as ProofreadingCost,
				c.company_name as Client
				FROM ContractMissions cm
				JOIN QuoteMissions qm ON qm.identifier=cm.type_id
				JOIN Quotes q ON q.identifier=qm.quote_id
				JOIN User u ON u.identifier=q.client_id
				LEFT JOIN  Client c ON c.user_id=u.identifier
				WHERE cm.type='prod' and q.sales_review='signed'

				  ";
		//echo $query;exit;		  
				 
		if(($result = $this->getQuery($query,true))!=NULL)
			return $result;
		else
			return false;
	}
 }
 ?>
 
