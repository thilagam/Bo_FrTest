<?php

class Ep_Quote_QuotesLog extends Ep_Db_Identifier
{
	protected $_name = 'QuotesLog';	
	
	
	public function getActionSentence($quote_action)
	{
		$Actionquery = "SELECT Message FROM QuoteActions WHERE identifier='".$quote_action."'";
       
        if(($Actionresult = $this->getQuery($Actionquery,true)) != NULL)
            return $Actionresult[0]['Message'];
        else
            return "NO";
	}
	
	public function insertLog($quote_action,$log_params)
	{
		if($quote_action)
		{
			$actionmessage=$this->getActionSentence($quote_action);
			if($actionmessage!='NO')
			{
				$bo_user_id=$log_params['bo_user'];
				if($bo_user_id)
				{
					$client_obj=new Ep_Quote_Client();
					$bo_user_details=$client_obj->getQuoteUserDetails($bo_user_id);
					$bo_user=$bo_user_details[0]['first_name'].' '.$bo_user_details[0]['last_name'];
				}

				//Action parameters
				$quote_size=$log_params['quote_size']=='small' ? "un petit" : "un gros";
				$urgent =$log_params['urgent'];
				$version=$log_params['version'];
				$created_date=$log_params['created_date'];
				$created_date=$log_params['skip_date'];	
				$challenge_time=$log_params['challenge_time'] >=1 ? $log_params['challenge_time'].' heure(s)' : round($log_params['challenge_time']*60).' mns';
				$delay_hours=$log_params['delay_hours'] >=1 ? $log_params['delay_hours'].' heure(s)' : round($log_params['delay_hours']*60).' mns';	
				$recruitment_title='<b>'.$log_params['recruitment_title'].'</b>';
				$hired_name='<b>'.$log_params['hired_name'].'</b>';

				$actionmessage=strip_tags($actionmessage);
      			eval("\$actionmessage= \"$actionmessage\";");
							
				
				if($log_params['quote_id'])
				{
					//log array
					$log_array['id']=$this->getIdentifier();
					$log_array['quote_id']=$log_params['quote_id'];

					$log_array['contract_id']=$log_params['contract_id'];
					$log_array['mission_id']= $log_params['mission_id'];
					$log_array['mission_type']= $log_params['mission_type'];

					$log_array['user_id']=$this->adminLogin->userId;
					$log_array['user_type']=$this->adminLogin->type;
					$log_array['action']=$log_params['action'];
					$log_array['action_at']=date("Y-m-d H:i:s");
					$log_array['action_sentence']=$actionmessage;
					$log_array['custom']=$log_params['custom'];
					$log_array['comments']=$log_params['comments'];
					$log_array['version']=$log_params['version'];
					
					//echo "<pre>";print_r($log_array);exit;
					$this->insertQuery($log_array);
				}	
			
			}
		}
		
		//$this->insertQuery($HistArr);
	}
	
	public function getQuotesLog($quote_id)
	{
		if($quote_id)
			$condition=" AND quote_id='".$quote_id ."'";

		$logQuery="SELECT l.*
							FROM QuotesLog l
							WHERE 1=1 $condition						
							$haveQuery
							ORDER BY l.action_at  DESC
							 $limitCondition
							";
		//echo $logQuery;exit; 					

		if(($count=$this->getNbRows($logQuery))>0)
		{
			$logDetails=$this->getQuery($logQuery,true);
			return $logDetails;
		}
		else
        	return NULL;
	}
	
	public function getquoteslogvalid($quotes_id,$action){
		$logQuery="SELECT action_at,action,quote_id,custom FROM QuotesLog where quote_id= '".$quotes_id."' AND action='".$action."'
		 order by action_at desc LIMIT 1";
		
		if(($count=$this->getNbRows($logQuery))>0)
		{
			$logDetails=$this->getQuery($logQuery,true);
			return $logDetails;
		}
		else
        	return NULL;
		
		}
	//function to get seo/tech/prod user details based on the quote_id
	public function getBoMissionUserDetails($quote_id,$type)
	{
		if($type=='seo')
		{
			$condition=" AND action in ('seo_validated_ontime','seo_validated_delay','seo_challenged') ";
		}
		if($type=='tech')
		{
			$condition=" AND action in ('tech_validated_ontime','tech_validated_delay','tech_challenged') ";
		}
		if($type=='prod')
		{
			$condition=" AND action in ('prod_validated_ontime','prod_validated_delay','prod_challenged') ";
		}

		$boUserQuery="SELECT u.email,up.first_name,up.last_name,l.user_id FROM QuotesLog l
					 INNER JOIN User u ON u.identifier=l.user_id
					 LEFT JOIN UserPlus up ON up.user_id=u.identifier
					 WHERE quote_id='".$quote_id."' $condition
					 ORDER BY action_at DESC LIMIT 1
							";
		//echo $boUserQuery;exit;					

		if(($logDetails=$this->getNbRows($boUserQuery))>0)
		{
			$logDetails=$this->getQuery($boUserQuery,true);
			return $logDetails;
		}
		else
        	return NULL;					
	}
	// Insert log by cron
	function logCron($log_array)
	{
		$this->insertQuery($log_array);
	}
	
	// To insert Logs from Contracts, Missions, Recruitments and soon
	function insertLogs($log_array)
	{
		$log_array['id']=$this->getIdentifier();
		$this->insertQuery($log_array);
	}
	public function updateQuoteLog($data,$identifier)
    {
        //print_r($data);exit;
		$where=" id='".$identifier."'";
        $this->updateQuery($data,$where);
    }
    //delete quotes log permenently
    public function deleteQuoteLog($identifier)
	{
		if($identifier!=""){
		$whereQuery =" id='".$identifier."'";
		$this->deleteQuery($whereQuery);
		}
	}
	
	// To get Logs for Followup
	function getLogs($search = NULL)
	{
		if($search['contract_id'])
		$condition = " AND l.contract_id='".$search['contract_id']."'";
		
		if($search['mission_id'])
		$condition .= " AND l.mission_id='".$search['mission_id']."'";
		
		if($search['mission_type'])
		$condition .= " AND l.mission_type IN('".$search['mission_type']."')";
		
		if($search['action'])
		$condition .= " AND l.action IN('".$search['action']."')";
		
		if($search['delay'])
		$condition .= ' AND ontime=0';
		
		if($search['time'])
		$case = " WHEN l.action IN('".$search['time']."') THEN ''";
		else
		$case = "";
		
		$logQuery=" SELECT l.*,up.first_name,up.last_name,CASE $case WHEN l.ontime=1 THEN '<span class=\'label label-info pull-right \'>On time</span>' ELSE '<span class=\'label pull-right label-important\'>Delay</span>' END as time
					FROM QuotesLog l
					INNER JOIN User u ON u.identifier=l.user_id
					LEFT JOIN UserPlus up ON up.user_id=u.identifier
					WHERE 1=1 $condition
					ORDER BY l.action_at  DESC
							";
	
		if(($count=$this->getNbRows($logQuery))>0)
		{
			$logDetails=$this->getQuery($logQuery,true);
			return $logDetails;
		}
		else
        	return NULL;
	}

	// Contribuitr Details
	public function getContributorDetails($user_id)
	{
		$query=" SELECT u.*,CONCAT(up.first_name,' ',up.last_name) as writer_name From User u 
					INNER JOIN UserPlus up ON up.user_id=u.identifier
				WHERE u.type='contributor' AND u.identifier='".$user_id."' LIMIT 1";	
		//echo $query;exit;
		if(($count=$this->getNbRows($query))>0)
        {
            $writerDetails=$this->getQuery($query,true);
            return $writerDetails;
        }
        else
            return "NO";		
	
	}


	public function getquotescomments($quotes_id,$action)
	{
		$logQuery="SELECT ql.*,up.first_name,up.last_name FROM QuotesLog as ql
		INNER JOIN User u ON u.identifier=ql.user_id
		LEFT JOIN UserPlus up ON up.user_id=u.identifier
		where ql.quote_id= '".$quotes_id."' AND ql.action='".$action."'
		ORDER BY ql.action_at DESC
		";
		
		if(($count=$this->getNbRows($logQuery))>0)
		{
			$logDetails=$this->getQuery($logQuery,true);
			return $logDetails;
		}
		else
        	return NULL;
		
	}
	/**get all quotes activities in sales quotes list page*/
	public function getQuotesActivities($page=1,$limit=10)
	{
		if($quote_id)
			$condition=" AND quote_id='".$quote_id ."'";
		if($page > 1)
		{
			$start=$page*$limit;
			$limitCondition= " LIMIT $start,$limit";
		}
		else if($page==1)
		{
			$limitCondition= " LIMIT $limit";
		}

		$logQuery="SELECT l.*
							FROM QuotesLog l
							JOIN Quotes q ON l.quote_id=q.identifier
							WHERE  l.contract_id IS NULL AND q.is_new_quote=1 $condition						
							$haveQuery
							ORDER BY l.action_at  DESC
							 $limitCondition
							";
		//echo $logQuery;exit; 			

		if(($count=$this->getNbRows($logQuery))>0)
		{
			$logDetails=$this->getQuery($logQuery,true);
			return $logDetails;
		}
		else
        	return NULL;
	}
}
?>
