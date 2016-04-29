<?php

class Ep_Poll_Poll extends Ep_Db_Identifier
{
    protected $_name = 'Poll';
	
	public function insertPoll($Pollarr)
	{
		$darray = array(); 		
		$darray["id"] = $this->getIdentifier(); 
		$darray['client']=$Pollarr['client'];
		$darray['title']=$this->utf8dec($Pollarr['tender_name']);
		
		$Pollarr['poll_date']=str_replace("/","-",$Pollarr['poll_date']);
		$darray["poll_date"] = date("Y-m-d H:i:s", strtotime($Pollarr['poll_date'])); 
		//$darray["poll_date"] = $Pollarr['poll_date']; 
		
		$darray["poll_anonymous"] = $Pollarr['poll_anonymous'];	 
		
		$darray["language"] = $Pollarr['lang'];	 
		$darray["type"] = $Pollarr['art_type'];	 
		$darray["category"] = $Pollarr['art_cat'];	 
		$darray["signtype"] = $Pollarr['art_sign_type'];	 
	
		$Pollarr['art_minsign']=str_replace(",",".",$Pollarr['art_minsign']);
		$darray["min_sign"] = $Pollarr['art_minsign'];	 

		$Pollarr['art_maxsign']=str_replace(",",".",$Pollarr['art_maxsign']);
		$darray["max_sign"] = $Pollarr['art_maxsign'];	 

		$darray["file_name"] = $this->utf8dec($Pollarr['spec_file_name']);	 
		$darray["total_article"] = $Pollarr['TotArt'];	 
		
		$darray["priority_hours"] = $this->getConfiguredval('priority_hours');	
		$darray["black_contrib"] = $this->getConfiguredval('poll_blackcontrib_fo');	
		$darray["contributors"] = $this->getConfiguredval('poll_contributors_fo');				
		$darray["send_mail"] = $this->getConfiguredval('poll_sendmail_fo');		
		$darray["mail_client"] = "yes";		
		$darray["contrib_percentage"] = $this->getConfiguredval('pollfo_contribpercent');		
		$darray["created_by"] = "client";			
		
		$Pollarr['price_min']=str_replace(",",".",$Pollarr['price_min']);
		$darray["price_min"] = $Pollarr['price_min'];	 

		$Pollarr['price_max']=str_replace(",",".",$Pollarr['price_max']);
		$darray["price_max"] = $Pollarr['price_max'];	
		
		$darray["poll_max"] = $Pollarr['poll_max'];
		
		$this->insertQuery($darray);
		return $darray["id"];
	}
	
	public function ListPoll($client)
	{
		$getPoll="	SELECT 
						p.id,p.title,p.created_at,p.poll_date,p.priority_hours,p.category,count(pp.id) as participation,max(pp.price_user) as maxprice,min(pp.price_user) as minprice,sum(pp.price_user) as sumprice,p.client,p.created_by,p.poll_max,now() as now,p.contrib_percentage 
					FROM 
						Poll p LEFT JOIN Poll_Participation pp 
					ON 
						p.id=pp.poll_id 
					WHERE 
						p.client=".$client." 
					GROUP BY 
						p.id";
		$resultPoll = $this->getQuery($getPoll,true);
		return $resultPoll;
	}
	
	
	public function Polldetail($id)
	{
		$SelPoll="SELECT 
						p.*,count(pp.id) as participationcount
					FROM 
						Poll p LEFT JOIN Poll_Participation pp
					ON 
						p.id=pp.poll_id 	
					WHERE 
						p.id='".$id."'
					GROUP BY 
						p.id";
		$resultPoll = $this->getQuery($SelPoll,true);
		return $resultPoll;
	}
	
	public function ListPollPartcipation($id,$sort="",$filter="")
	{
		$orderby="";
		if($sort=="dateasc")
			$orderby=" ORDER BY p.created_at asc";
		elseif($sort=="datedesc")
			$orderby=" ORDER BY p.created_at desc";
		elseif($sort=="priceasc")
			$orderby=" ORDER BY p.price_user asc";
		elseif($sort=="pricedesc")
			$orderby=" ORDER BY p.price_user desc";	
			
		$filterby="";
		if($filter!="")
			$filterby=" AND ROUND((p.price_user*100)/po.contrib_percentage,2)='".$filter."'";
			
			$SelPoll="SELECT 
						p.*,po.title,po.poll_date,po.contrib_percentage,u.first_name,u.last_name 
					FROM 
						Poll po LEFT JOIN Poll_Participation p ON po.id=p.poll_id 
						LEFT JOIN UserPlus u ON p.user_id=u.user_id 
					WHERE 
						p.poll_id='".$id."' ".$filterby.$orderby;
		$resultPoll = $this->getQuery($SelPoll,true);
		return $resultPoll;
	}
	
	public function PollContributorDetails($user)
	{
		$SelPoll="SELECT * FROM User us LEFT JOIN UserPlus u ON us.identifier=u.user_id LEFT JOIN Contributor c ON u.user_id=c.user_id WHERE u.user_id=".$user;
		$resultPoll = $this->getQuery($SelPoll,true);
		return $resultPoll;
	}
	
	public function Clientdetail($user)
	{
		$GetClient="SELECT first_name,last_name FROM UserPlus WHERE user_id=".$user;
		$resultclient = $this->getQuery($GetClient,true);
		return $resultclient[0]['first_name'].'&nbsp;'.$resultclient[0]['last_name'];
	}
	
	public function getPollrights($user)
	{
		$GetClient="SELECT poll_rights FROM Client WHERE user_id=".$user;
		$resultclient = $this->getQuery($GetClient,true);
		return $resultclient[0]['poll_rights'];
	}
	
	public function pollstosendcronmails($sendmail)
	{
		$wheremail="";
		if($sendmail=="yes")
			$wheremail=" AND p.publish_time<=CURRENT_TIMESTAMP() ";
		
		$getPolls="SELECT 
					p.*,count(pp.id) as pollparticipationcnt 
				FROM 
					Poll p LEFT JOIN Poll_Participation pp ON p.id=pp.poll_id 
				WHERE 
					p.created_by!='client' AND p.cron_mail='no' AND p.valid_status='active' AND p.send_mail='".$sendmail."' AND p.poll_date>CURRENT_TIMESTAMP() ".$wheremail." 
				GROUP BY p.id
				ORDER BY p.id";
		$resultpolls = $this->getQuery($getPolls,true);//echo $getPolls;
		return $resultpolls;
	}
	
	public function getContributors($poll,$cat,$contrib,$black)
	{
		$where='';
		
		if($contrib==0)
			$where.=" AND u.profile_type='senior'";
		elseif($contrib==1)
			$where.=" AND u.profile_type='junior'";
		elseif($contrib==2)
			$where.=" AND u.profile_type in ('senior','junior')";
		elseif($contrib==3)
			$where.=" AND u.profile_type in ('sub-junior')";
		else
			$where.=" AND u.profile_type in ('senior','junior','sub-junior')";			
			
		//Black list
		if($black=='no')
			$where.=" AND blackstatus='no'";
			
		$SelContrib="SELECT 
							u.identifier 
					FROM 
							User u 
					INNER JOIN 
							Contributor c 
					ON 
							u.identifier=c.user_id 
					WHERE 
							u.type='contributor' AND 
							u.status='Active' ".$where;
		//AND favourite_category Like '%".str_replace(",","%' OR favourite_category Like '%",$cat)."%'
		$resultcontrib = $this->getQuery($SelContrib,true); //echo $SelContrib;
			
		return $resultcontrib;
		
	}
	
	public function updatepollcronmail($poll)
	{
		$this->_name="Poll";
		
		$Upoll=array();
		$Upoll['cron_mail']='yes';
		$Uwhere=" id='".$poll."'";
		$this->updateQuery($Upoll,$Uwhere);
	}
	
	public function Updatepollmailclient($poll)
	{
		$this->_name="Poll";
		
		$Upoll=array();
		$Upoll['mail_client']='yes';
		$Uwhere=" id='".$poll."'";
		$this->updateQuery($Upoll,$Uwhere);
	}
	
	public function pollpartstatus($partid,$status)
	{
		$this->_name="Poll_Participation";
		$pollWhere=" id=".$partid;
		
		$Pparray=array();
		
		if($status=='active')
			$Pparray['status']="inactive";
		else	
			$Pparray['status']="active";
		
		$this->updateQuery($Pparray,$pollWhere);
		return $Pparray['status'];
	}
	
	public function getPollpriceset($poll)
	{
		$SelectQueryall="SELECT 
							count(p.id) as participation,max(p.price_user) as maxprice,min(p.price_user) as minprice,sum(p.price_user) as sumprice,po.contrib_percentage 
						FROM 
							Poll po INNER JOIN Poll_Participation p ON po.id=p.poll_id INNER JOIN User u ON p.user_id=u.identifier 
						WHERE 
							p.poll_id='".$poll."' AND p.status='active'";
		$resultPollall = $this->getQuery($SelectQueryall,true);	//echo $SelectQueryall;
		return $resultPollall;
	}
	
	public function PollPartcipationsAll($id)
	{
		$SelPoll="SELECT 
						p.price_user,DATE_FORMAT(p.created_at, '%d/%m/%Y %H:%i:%s') as created,
						u.email,u.status,DATE_FORMAT(u.created_at, '%d/%m/%Y') as created_at,u.profile_type,u.blackstatus,
						up.first_name,up.last_name,up.initial,up.address,up.city,up.state,up.zipcode,up.country,up.phone_number,
						DATE_FORMAT(c.dob, '%d/%m/%Y') as dob1,c.* 
					FROM 
						Poll_Participation p INNER JOIN User u ON p.user_id=u.identifier 
						LEFT JOIN UserPlus up ON u.identifier=up.user_id 
						LEFT JOIN Contributor c ON up.user_id=c.user_id 
					WHERE p.poll_id='".$id."' AND p.status='active'";
		$resultPoll = $this->getQuery($SelPoll,true);
		return $resultPoll;
	}
	
	public function pollclientdetails($poll_id)
	{
		$SelPollclient="SELECT 
						p.title,u.first_name,u.last_name 
						FROM Poll p LEFT JOIN UserPlus u ON p.client=u.user_id
					WHERE p.id='".$poll_id."'";
					
		$resultPollclient = $this->getQuery($SelPollclient,true);
		return $resultPollclient;
	}
	
	public function pollcontribjob($contrib)
	{
		$SelJobContib="SELECT title FROM ContributorExperience WHERE user_id='".$contrib."' AND type='job' AND to_year='0'";
		$resultJobContib = $this->getQuery($SelJobContib,true);
		return $resultJobContib;
	}
	
	public function pollcontribeducation($contrib)
	{
		$SeleducationContib="SELECT title,institute FROM ContributorExperience WHERE user_id='".$contrib."' AND type='education'";
		$resulteducationContib = $this->getQuery($SeleducationContib,true);
		return $resulteducationContib;
	}
	
	public function updatepollfavcontribs($poll,$contribs)
	{
		$this->_name="Poll";
		$pollar=array();
		$pollar['fav_contribs']=implode(",",$contribs);
		$polwhere= "id='".$poll."'";
		$this->updateQuery($pollar,$polwhere);
	}
	
	public function checkPollExists($poll,$client)
	{
		$ArtQuery="SELECT * FROM Poll WHERE id='".$poll."' AND client='".$client."'";
		$Artdetails= $this->getQuery($ArtQuery,true);
        if(count($Artdetails)>0)   
		   return "YES";
		else
			return "NO";
	}
	
	public function getNewsletterDevis()
	{
		$NLpollQuery="SELECT p.id, p.title, p.publish_time, p.language, p.category, p.client, c.company_name, p.contributors
							FROM Poll p
							LEFT JOIN Client c ON p.client = c.user_id
							WHERE p.publish_time > CURRENT_TIMESTAMP( )
							ORDER BY p.id";
		$NLpoll = $this->getQuery($NLpollQuery,true);
		return $NLpoll;
	}
	
	//Fetching Configuration
	public function getConfiguredval($constraint)
	{
		$conf_obj=new Ep_User_Configuration();
		$conresult=$conf_obj->getConfiguration($constraint);
		return $conresult;
	}
	
	public function utf8dec($s_String)
    {
            $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
            return substr($s_String, 0, strlen($s_String)-1); 
    }
	
	
}	