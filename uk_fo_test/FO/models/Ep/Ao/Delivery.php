<?php



class Ep_Ao_Delivery extends Ep_Db_Identifier

{

	protected $_name = 'Delivery';

	

	public function InsertDelivery($user,$funnel)

	{

		$darray = array(); 		

		$darray["id"] = $this->getIdentifier(); 

		$darray["user_id"] = $user; 

		$darray['title']=$this->utf8dec($funnel['title']);
		$darray['language']="uk";
		

		//$darray['delivery_date']=date('y:m:d', strtotime("+15 days"));

		

		$darray["premium_option"] = "0";

		$darray["premium_total"] = "0";	 

		

		$darray["premium_option"] = "0";
	
		

		//Forming Delivery time
		
			if($funnel['deliverytime_option']=='min')

				$delivery_time=$funnel['deliverytime'];

			elseif($funnel['deliverytime_option']=='hour')

				$delivery_time=$funnel['deliverytime']*60;

			elseif($funnel['deliverytime_option']=='day')	

				$delivery_time=$funnel['deliverytime']*60*24;

			

			$darray["senior_time"]=$delivery_time;

			$darray["junior_time"]=$delivery_time/2;

			$darray["submit_option"]=$funnel['deliverytime_option'];
		
			//$darray["senior_time"]=$this->getConfiguration('sc_time');
			//$darray["junior_time"]=$this->getConfiguration('jc_time');
			//$darray["submit_option"]="min";
		
		
		//Forming participation time
		
			if($funnel['participationtime_option']=='min')

			{

				$participation_time=$funnel['participationtime'];

				$darray['delivery_date']=date('y:m:d', strtotime("+1 days"));

			}

			elseif($funnel['participationtime_option']=='hour')

			{

				$participation_time=$funnel['participationtime']*60;

				$darray['delivery_date']=date('y:m:d', strtotime("+1 days"));

			}

			elseif($funnel['participationtime_option']=='day')

			{	

				$participation_time=$funnel['participationtime']*60*24;

				$days=$funnel['participationtime'];

				$darray['delivery_date']=date('y:m:d', strtotime("+".$days." days"));

			}

			$darray["participation_time"]=$participation_time;
		
			//$darray["participation_time"]=$this->getConfiguration('participation_time');
			//$darray['delivery_date']=date('y:m:d', strtotime("+1 days"));
		
		

		//files

		

		if(count($funnel['filename'])>0)

		{

			$darray['file_name']=implode("|",$funnel['filename']);

			$darray['file_name']=utf8_decode($darray['file_name']);

			

			for($f=0;$f<count($funnel['filename']);$f++)

				$filepath[]="/".$user."/".utf8_decode($funnel['filename'][$f]);

			

			$darray['filepath']=implode("|",$filepath);

			

		}

		

		$darray['view_to']="sc";

		$darray['created_by']="FO";

		$darray['client_type']=$funnel['whoIs'];

		$darray['category']="other";

		$darray['total_article']="1";

		

		//AO type

		if($funnel['privatecontrib']=="on")

			$darray['AOtype']="private";

		else

			$darray['AOtype']="public";

		

		if(count($funnel['contribselect'])>0 && $funnel['privatecontrib']=="on")

			$darray['contribs_list']=implode(",",$funnel['contribselect']);

		

		//Price
		if($funnel['price_min_total']!="")
			$darray['price_min']=$funnel['price_min_total'];
		
		if($funnel['price_max_total']!="")
			$darray['price_max']=$funnel['price_max_total'];
		
		if($funnel['privatepublish']=="yes" && $funnel['privatecontrib']=="on")
		{	
			$darray["status_bo"] = "active";
			$darray["updated_at"] = date('Y-m-d');
			$darray["published_at"] = time();
		}
		
		//print_r($darray);exit;
		 if($this->insertQuery($darray))
			return $this->getIdentifier(); 
		 else
			return "NO";
	}
	
	public function InsertLiberte($user,$funnel,$funnel2)
	{
		$darray = array(); 		
		$darray["id"] = $this->getIdentifier(); 
		$darray["user_id"] = $user; 
		$darray['title']=$this->utf8dec($funnel['aotitle']);
		$darray['total_article']=$funnel['total_article'];
		$darray['type']=$funnel['type'];		
		$darray['other_type']=$funnel['textforother'];		
		$darray['language']="uk";
		
		$darray['min_sign']=$funnel['min_sign'];		
		$darray['max_sign']=$funnel['max_sign'];		
		
		$darray["premium_option"] = "0";
		$darray["premium_total"] = "0";	 
		
		//Forming Delivery time
		
			if($funnel2['deliverytime']!="")
			{
				if($funnel2['delivery_option']=='min')
					$delivery_time=$funnel2['deliverytime'];
				elseif($funnel2['delivery_option']=='hour')
					$delivery_time=$funnel2['deliverytime']*60;
				elseif($funnel2['delivery_option']=='day')	
					$delivery_time=$funnel2['deliverytime']*60*24;
				
				$darray["senior_time"]=$delivery_time;
				$darray["junior_time"]=$delivery_time/2;
				$darray["submit_option"]=$funnel2['delivery_option'];
			}
			else
			{
				$darray["senior_time"]=$this->getConfiguration('sc_time');
				$darray["junior_time"]=$this->getConfiguration('jc_time');
				$darray["submit_option"]='min';
			}
			$darray["participation_time"]=$this->getConfiguration('participation_time');
			
		//files
		if(count($funnel2['filename'])>0)
		{
			$darray['file_name']=implode("|",$funnel2['filename']);
			$darray['file_name']=utf8_decode($darray['file_name']);
			
			for($f=0;$f<count($funnel2['filename']);$f++)
				$filepath[]="/".$user."/".utf8_decode($funnel2['filename'][$f]);
			
			$darray['filepath']=implode("|",$filepath);
		}
		
		$darray['view_to']="sc";
		$darray['created_by']="FO";
		$darray['language']="fr";
	
		$darray['category']="other";
		
		//AO type
		if($funnel2['privatecontrib']=="1")
			$darray['AOtype']="private";
		else
			$darray['AOtype']="public";
		
		if(count($funnel2['contribselect'])>0 && $funnel2['privatecontrib']=="1")
			$darray['contribs_list']=implode(",",$funnel2['contribselect']);
		
		//Price
		if($funnel2['pricecheck']=="1")
		{
			if($funnel2['price_min_total']!="")
			{
				$funnel2['price_min_total']=str_replace(",",".",$funnel2['price_min_total']);
				$darray['price_min']=$funnel2['price_min_total'];
			}
			if($funnel2['price_max_total']!="")
			{
				$funnel2['price_max_total']=str_replace(",",".",$funnel2['price_max_total']);
				$darray['price_max']=$funnel2['price_max_total'];
			}
		}
		
		if($funnel['privatepublish']=="yes" && $funnel2['privatecontrib']=="1")
		{	
			$darray["status_bo"] = "active";
			$darray["updated_at"] = date('Y-m-d');
			$darray["published_at"] = time();
		}
		if(count($funnel['objectives'])>0)
		{
			$darray["objective"]=implode(",",$funnel['objectives']); 
			$darray["other_objective"]=$funnel['objOtherText']; 
		}
		$darray["remindtime"]=$funnel2['remindtime']; 
		if($funnel['dontknowcheck']==1)
			$darray["dontknowcheck"]="yes"; 
		
		$darray["content_type"]=$funnel['con_type']; 
		$darray["quoteid"]=$funnel['quoteid']; 		
		 if($this->insertQuery($darray))

			return $this->getIdentifier(); 

		 else

			return "NO";

	}

	

	//Fetching Configuration

	public function getConfiguration($columns)

    {

        $SelConfg="SELECT configure_value FROM Configurations WHERE configure_name='".$columns."' ";

       

		if(($resultconfg = $this->getQuery($SelConfg,true)) != NULL)

            return $resultconfg[0]['configure_value'];

    }

	

	public function utf8dec($s_String)

    {

            $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));

            return substr($s_String, 0, strlen($s_String)-1); 

    }

	

	public function currentquotes($user)

	{

		$displayvals=$this->getdisplayval($user);

		

		$whereplus="";

		

		if($displayvals[0]['premiumdisplay']=="Hide" && $displayvals[0]['nopremiumdisplay']=="Show")

			$whereplus.=" AND d.premium_option='0'";

		elseif($displayvals[0]['premiumdisplay']=="Show" && $displayvals[0]['nopremiumdisplay']=="Hide")

			$whereplus.=" AND d.premium_option!='0'";

		elseif($displayvals[0]['premiumdisplay']=="Hide" && $displayvals[0]['nopremiumdisplay']=="Hide")

			return array();

			

		$quotesQuery="	SELECT 

							d.id as did,a.title,count(p.id) as partcount,d.created_at,a.id as id 

						FROM 

							Delivery d INNER JOIN Article a ON d.id=a.delivery_id 

							LEFT JOIN Participation p ON a.id=p.article_id 

						WHERE 

							d.user_id='".$user."' AND (d.status_bo IS NULL OR d.status_bo='active') ".$whereplus."

						GROUP BY 

							a.id 

						ORDER BY d.created_at DESC 

						";

		$quotesSet = $this->getQuery($quotesQuery,true);

		

			for($q=0;$q<count($quotesSet);$q++)

			{

				if($quotesSet[$q]['partcount']>0)

					$quotesSet[$q]['valid']=$this->getvalidquote($quotesSet[$q]['id']);

				else

					$quotesSet[$q]['valid']='no';

					

				$quotesSet[$q]['publish']=$this->CheckartPublished($quotesSet[$q]['id']);	

				$quotesSet[$q]['participations']=$this->getPartcount($quotesSet[$q]['id']);	

			}

		return $quotesSet;

		

	}

	

	public function currentquotesall()

	{

		$quotesQuery="	SELECT 

							d.id as did,a.title,count(p.id) as partcount,d.created_at,a.id as id 

						FROM 

							Delivery d INNER JOIN Article a ON d.id=a.delivery_id 

							LEFT JOIN Participation p ON a.id=p.article_id

						WHERE a.participation_expires>UNIX_TIMESTAMP() AND d.AOtype='public'			

						GROUP BY 

							a.id 

						ORDER BY d.created_at DESC LIMIT 8

						";

		$quotesSet = $this->getQuery($quotesQuery,true);

		

			for($q=0;$q<count($quotesSet);$q++)

			{

				$quotesSet[$q]['participations']=$this->getPartcount($quotesSet[$q]['id']);	

			}

		return $quotesSet;

		

	}

	

	public function getdisplayval($client)

	{

		$clientQuery="SELECT * FROM Client WHERE user_id='".$client."'";

		$clientSet = $this->getQuery($clientQuery,true);

		return $clientSet;

	}

	

	public function getvalidquote($article)

	{

		$quotesQuery="	SELECT 

							id

						FROM 

							Participation 
						WHERE article_id='".$article."' AND status NOT IN ('bid_premium','bid_nonpremium','bid_temp','bid_refused_temp','bid_nonpremium_timeout') AND cycle='0'";
					

		$quotesSet = $this->getQuery($quotesQuery,true);

		

		if(count($quotesSet)>0)

			return "yes";

		else

			return "no";

	}

	

	public function getPartcount($quote)

	{

		$quotesQuery="	SELECT 

							id

						FROM 

							Participation 

						WHERE article_id='".$quote."' AND status IN ('bid_premium','bid_nonpremium','bid_temp','bid_refused_temp') AND cycle='0'";

					

		$quotesSet = $this->getQuery($quotesQuery,true);

		return count($quotesSet);

	}

	

	public function Deliverydetails($article)

	{

		$quotesQuery="	SELECT 
							d.id,d.premium_option,d.filepath,d.status_bo,d.created_by,d.client_type,d.AOtype,d.created_at,d.contribs_list,d.senior_time,d.submit_option,d.price_min,d.price_max,d.file_name,
							a.title,a.currency,a.id as article_id,a.category,a.language,a.num_min,a.num_max,a.status as articlestatus,count(p.id) as partcount,a.participation_time,a.participation_expires,TIMESTAMPDIFF( MINUTE ,now( ), FROM_UNIXTIME( a.participation_expires ) ) AS minutediff 
						FROM 

							Delivery d INNER JOIN Article a ON d.id=a.delivery_id 

							LEFT JOIN Participation p ON a.id=p.article_id 

						WHERE a.id='".$article."' AND p.cycle='0'";

						

		$quotesSet = $this->getQuery($quotesQuery,true);

		return $quotesSet;
	}
	
	public function DeliveryPremiumdetails($article)
	{
		$quotesQuery="	SELECT 
							d.title as dtitle,d.file_name,d.submit_option,d.senior_time,d.AOtype,d.contribs_list,d.price_min,d.price_max,p.*
						FROM 
							Delivery d INNER JOIN Article a ON d.id=a.delivery_id 
							INNER JOIN PremiumQuotes p ON p.id=d.quoteid 
						WHERE a.id='".$article."'";
						
		$quotesSet = $this->getQuery($quotesQuery,true);
		return $quotesSet;
	}

	

	public function getMaildetails($article)

	{

		$articleQuery="	SELECT 

							a.title,a.currency,up.first_name,up.last_name,d.id,d.delivery_date,d.senior_time,d.junior_time,u.profile_type,a.paid_status,a.invoice_id,d.premium_total

						FROM 

							Delivery d INNER JOIN Article a ON d.id=a.delivery_id 

							LEFT JOIN Participation p ON a.id=p.article_id 

							INNER JOIN User u ON u.identifier=p.user_id

							INNER JOIN UserPlus up ON p.user_id=up.user_id

						WHERE a.id='".$article."'";

						

		$articleresult = $this->getQuery($articleQuery,true);

		return $articleresult;

	}

	

	public function checkfirstao($user)

	{

		$quotesQuery="	SELECT 

							count(*) as count

						FROM 

							".$this->_name." 

						WHERE user_id='".$user."' ";

						

		$quotesSet = $this->getQuery($quotesQuery,true);

		

		if($quotesSet[0]['count']>1)

			return "NO";

		else

			return "YES";

	}

	

	public function CheckartPublished($art)

	{

		$PubParticipationQuery="SELECT user_id FROM Participation WHERE article_id='".$art."' AND status IN ('published') AND cycle='0' ";

		$PubParticipations = $this->getQuery($PubParticipationQuery,true);

		if(count($PubParticipations)>0)

			return "YES";

		else

			return "NO";

	}

	
	public function publishtimeao()
	{
		$PublishAOQuery="SELECT d.id,d.title,d.premium_option,d.publishtime,d.participation_time,d.mailsubject,d.mailcontent,d.nltitle,d.fbcomment,d.mail_sender, d.created_user,GROUP_CONCAT(DISTINCT da.user_id) as alertuser FROM Delivery d LEFT JOIN DeliveryAlert da ON d.id = da.delivery_id WHERE d.publishtime!='' AND d.publishtime <= UNIX_TIMESTAMP() AND d.publishtime >= UNIX_TIMESTAMP(DATE_SUB(now(), INTERVAL 15 minute))AND d.cronmail_publish = 'no' AND da.alert='yes' AND da.mission_type='article' GROUP BY d.id";
 
		$PublishAOs = $this->getQuery($PublishAOQuery,true);

		return $PublishAOs;
	}
	public function publishtimeaoclientmail()
	{
		$PublishAOQuery="SELECT d.id,d.title,d.premium_option,d.publishtime,d.participation_time,d.nltitle,d.fbcomment,d.user_id as client_id, a.id AS artId FROM Delivery d LEFT JOIN Article a ON d.id = a.delivery_id WHERE d.publishtime!='' AND d.publishtime <= UNIX_TIMESTAMP() AND d.cronmailclient_publish = 'no' AND d.premium_option = 0 ";
		$PublishAOs = $this->getQuery($PublishAOQuery,true);
		return $PublishAOs;

	}

	

	public function publishtimefbpost()

	{

		$PublishAOQuery="SELECT d.id,d.title,d.user_id,d.premium_option,d.publishtime,d.participation_time,d.nltitle,d.fbcomment FROM Delivery d WHERE d.publishtime!='' AND d.publishtime <= UNIX_TIMESTAMP() AND fbpost='no' GROUP BY d.id";
		
		$PublishAOs = $this->getQuery($PublishAOQuery,true);

		return $PublishAOs;

	}

	

	public function getNewsletterMissions()

	{

		$NLmissionQuery="SELECT d.id, d.title, d.premium_option, d.publishtime,d.language, d.category,d.user_id,d.created_user,d.missioncomment,c.company_name,d.view_to,d.AOtype,d.total_article,d.nltitle,d.deli_anonymous,GROUP_CONCAT(DISTINCT a.contribs_list) as contribs_list

						FROM Delivery d INNER JOIN Article a ON d.id=a.delivery_id 

						LEFT JOIN Client c ON d.user_id = c.user_id

						WHERE d.publishtime != ''

						AND d.publishtime > UNIX_TIMESTAMP() 

						AND d.cronmail_publish = 'no' AND d.AOtype='public' GROUP BY d.id ORDER BY  (d.total_article * d.price_min) DESC";

		$NLmission = $this->getQuery($NLmissionQuery,true);

		return $NLmission;

	}

	

	public function getEditorofDay()

	{

		//$CEQuery="SELECT sum(a.price_max) as price,d.missioncomment,d.language,d.id,d.created_user,u.first_name,u.last_name FROM Delivery d INNER JOIN Article a ON d.id=a.delivery_id LEFT JOIN UserPlus u ON d.created_user=u.user_id WHERE d.premium_option!='0' AND d.publishtime != '' AND d.publishtime > UNIX_TIMESTAMP() AND d.cronmail_publish = 'no' GROUP BY d.id ORDER BY price DESC limit 1 ";
		$CEQuery="SELECT (d.price_min * d.total_article) as price,d.missioncomment,d.language,d.id,d.created_user,u.first_name,u.last_name FROM Delivery d INNER JOIN Article a ON d.id=a.delivery_id LEFT JOIN UserPlus u ON d.created_user=u.user_id WHERE d.premium_option!='0' AND d.publishtime != '' AND d.publishtime > UNIX_TIMESTAMP() AND d.cronmail_publish = 'no' AND d.AOtype='public' GROUP BY d.id ORDER BY price DESC limit 1 ";
		$CEresult = $this->getQuery($CEQuery,true);

		return $CEresult;

	}

	

	public function getArticles($did)

	{

		$ArtQuery="SELECT id FROM Article WHERE delivery_id='".$did."'";

		$Artresult = $this->getQuery($ArtQuery,true);

		return $Artresult;

	}

	

	public function updateDelivery($data,$query)

    {
	  $data["updated_at"] = date("Y-m-d");  	
      $this->updateQuery($data,$query);

    }
	
	public function checkfbpost($client)
	{
		$today=date("Y-m-d");
		$tommorrow=date('Y-m-d', strtotime("+1 days"));
		$ChkQuery="SELECT * FROM Delivery WHERE postoftheday='yes' AND user_id='".$client."' AND FROM_UNIXTIME(publishtime, '%Y-%m-%d')='".$today."'";
		$Chkresult = $this->getQuery($ChkQuery,true);
		if(count($Chkresult)>0)
			return "yes";
		else
			return "no";
	}
	
	public function mailmissionsnow()
	{$d = new Date();
		echo $d->getSubDate(2,14);
		$PublishAOQuery="SELECT d.id,d.title,d.premium_option,d.mailsubject,d.mailcontent,d.view_to FROM Delivery d WHERE d.mailnow='yes'";
 		$PublishAOs = $this->getQuery($PublishAOQuery,true);
		return $PublishAOs;
	}
	
	public function getContributorsOfAllCategories($type,$profiles)
    {
        $profs=explode(",",$profiles);
        $proflist=array();
        for($p=0;$p<count($profs);$p++)
        {
            if($profs[$p]=="jc")
                $proflist[]="junior";
            elseif($profs[$p]=="sc")
                $proflist[]="senior";
            elseif($profs[$p]=="jc0")
                $proflist[]="sub-junior";
        }
        $profile1=implode("','",$proflist);
        if($type=='public')
        {
           // $condition="WHERE type='contributor' AND status='Active' AND blackstatus='no' AND profile_type IN ('".$profile1."')";
            $condition="WHERE type='contributor' AND status='Active' AND blackstatus='no'";
            $query="select DISTINCT u.identifier, u.profile_type  FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition;
        }

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function getDeliverybyid($ao)
	{
		$DeliveryQuery="SELECT file_name,filepath FROM Delivery WHERE id='".$ao."'";
		$Deliveryresult = $this->getQuery($DeliveryQuery,true);
		return $Deliveryresult;
	}
	
	//Quotes
	public function listquotescount($user,$status)
	{
		$where="";
		if($status=="published")
			$where=" AND p.status='published' AND a.status NOT IN ('closed','closed_client')";
		elseif($status=="closed")
			$where=" AND a.status IN ('closed','closed_client')";
		elseif($status=="ongoing")
			$where=" AND p.status IN ('bid','under_study','bid_premium','bid_nonpremium','bid_nonpremium_timeout') AND a.status NOT IN ('closed','closed_client')";
		elseif($status=="new")
			$where=" AND (p.status IS NULL  OR p.status IN ('bid_premium','bid_nonpremium')) AND a.status NOT IN ('closed','closed_client')"; 	
			
		$SelectcntQuery="SELECT 
							*
						FROM 
							Delivery d INNER JOIN Article a ON d.id=a.delivery_id
							LEFT JOIN Participation p ON p.article_id=a.id
						WHERE 
							d.user_id='".$user."'AND (d.status_bo IS NULL OR d.status_bo='active') ".$where."
							GROUP BY 
							a.id ";
		//echo $SelectcntQuery;exit;
		$ResultCnt = $this->getQuery($SelectcntQuery,true);
		return count($ResultCnt);
	}
	
	public function listquotes($var,$user,$status)
	{
		$where="";
		if($status=="published")
			$where=" AND p.status='published' AND a.status NOT IN ('closed','closed_client')";
		elseif($status=="closed")
			$where=" AND a.status IN ('closed','closed_client') ";
		elseif($status=="ongoing")
			$where=" AND p.status IN ('bid','under_study','bid_nonpremium_timeout') AND a.status NOT IN ('closed','closed_client')"; 
		elseif($status=="new")
			$where=" AND (p.status IS NULL  OR p.status IN ('bid_premium','bid_nonpremium')) AND a.status NOT IN ('closed','closed_client')"; 
			
		$quotesQuery="	SELECT 
							d.id as did,a.title,count(p.id) as partcount,d.created_at,a.id as id,a.participation_expires,p.article_submit_expires 
						FROM 
							Delivery d INNER JOIN Article a ON d.id=a.delivery_id
							LEFT JOIN Participation p ON p.article_id=a.id
						WHERE 
							d.user_id='".$user."' AND (d.status_bo IS NULL OR d.status_bo='active') ".$where."
						GROUP BY 
							a.id 
						ORDER BY d.created_at DESC Limit ".$var.",10";
						
		$quotesSet = $this->getQuery($quotesQuery,true);
		
			for($q=0;$q<count($quotesSet);$q++)
			{
				if($quotesSet[$q]['partcount']>0)
					$quotesSet[$q]['valid']=$this->getvalidquote($quotesSet[$q]['id']);
				else
					$quotesSet[$q]['valid']='no';
					
				$quotesSet[$q]['publish']=$this->CheckartPublished($quotesSet[$q]['id']);	
				$quotesSet[$q]['participations']=$this->getPartcount($quotesSet[$q]['id']);	
			}
		return $quotesSet;
	}
	
	public function listnewquotes($user)
	{
		$where=" AND (p.status IS NULL  OR p.status IN ('bid_premium','bid_nonpremium')) AND a.status NOT IN ('closed','closed_client')"; 
			
		$quotesQuery="	SELECT 
							a.title,a.id 
						FROM 
							Delivery d INNER JOIN Article a ON d.id=a.delivery_id
							LEFT JOIN Participation p ON p.article_id=a.id
						WHERE 
							d.user_id='".$user."' AND (d.status_bo IS NULL OR d.status_bo='active') ".$where."
						GROUP BY 
							a.id 
						ORDER BY d.created_at DESC Limit 5";
						
		$quotesSet = $this->getQuery($quotesQuery,true);
		return $quotesSet;
	}
	
	public function getEmailUser($user)
	{
		$query="SELECT email FROM User WHERE identifier='".$user."'";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result[0]['email'];
		else
			return "NO";	
	}
}	