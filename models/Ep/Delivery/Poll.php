<?php

class Ep_Delivery_Poll extends Ep_Db_Identifier
{
	protected $_name = 'Poll';
	 
	public function insertPoll($Pollarr)
	{
		$darray = array(); 		
		$darray["id"] = $this->getIdentifier(); 
		$darray['client']=$Pollarr['client_list'];
		$darray['title']=utf8_decode(stripslashes($Pollarr['title']));
		
		$Pollarr['poll_date']=str_replace("/","-",$Pollarr['poll_date']);
		$darray["poll_date"] = date("Y-m-d H:i:s", strtotime($Pollarr['poll_date'])); 
		
		if($Pollarr['poll_anonymous']=="on"){$darray["poll_anonymous"]=1;}else{$darray["poll_anonymous"]=0;}	 
		
		$darray["language"] = $Pollarr['language'];	 
		$darray["type"] = $Pollarr['type'];	 
		$darray["category"] = $Pollarr['category'];	 
		$darray["signtype"] = $Pollarr['signtype'];	 
	
		$Pollarr['min_sign']=str_replace(",",".",$Pollarr['min_sign']);
		$darray["min_sign"] = $Pollarr['min_sign'];	 

		$Pollarr['min_sign']=str_replace(",",".",$Pollarr['min_sign']);
		$darray["max_sign"] = $Pollarr['min_sign'];	 
		
		$darray["file_name"] = $Pollarr['poll_spec_name'];	 
		$darray["priority_hours"] = $Pollarr['priority_hours'];	
		if($Pollarr['black_contrib']=="on"){$darray["black_contrib"]="yes";}else{$darray["black_contrib"]="no";}	
		$darray["contributors"] = $Pollarr['contrib'];			
		$darray["created_by"] = $Pollarr['created_by'];			
		if($Pollarr['send_mail']=="on"){$darray["send_mail"]="yes";}else{$darray["send_mail"]="no";}	
			
		$darray["poll_max"] = $Pollarr['poll_max'];	
		
		if($Pollarr['publishnow']=="checked")
			$darray["publish_time"]=date("Y-m-d H:i:s");
		else
		{	
			$Pollarr['publish_time']=str_replace("/","-",$Pollarr['publish_time']);
			$darray["publish_time"] = date("Y-m-d H:i:s", strtotime($Pollarr['publish_time'])); 
		}
		
		$darray['valid_status']='active';
		
		$Pollarr['contrib_percentage']=str_replace(",",".",$Pollarr['contrib_percentage']);
		$darray['contrib_percentage']=$Pollarr['contrib_percentage'];
		
		$this->_name='Poll';
		
		if($this->insertQuery($darray))
			return $this->getIdentifier(); 
		else
			return "NO";
	}
	
	public function editupdatePoll($pollArray)
	{
		$pollWhere=" id=".$pollArray['poll'];
		
		$Parray=array();
		$Parray['client']=$pollArray['client_list'];
		$Parray['title']=$pollArray['title'];
		$Parray['category']=$pollArray['categoryedit'];
		
		$pollArray['poll_date']=str_replace("/","-",$pollArray['poll_date']);
		$Parray["poll_date"] = date("Y-m-d H:i:s", strtotime($pollArray['poll_date'])); 
		
		if($pollArray['poll_spec_file']!="")
			$Parray["file_name"] = $pollArray['poll_spec_file'];	
			
		if($pollArray['poll_max']!="")
			$Parray['poll_max']=$pollArray['poll_max'];
			
		if($pollArray['publish_time']!="")
		{
			$pollArray['publish_time']=str_replace("/","-",$pollArray['publish_time']);
			$Parray["publish_time"] = date("Y-m-d H:i:s", strtotime($pollArray['publish_time']));
		}		
			
		if($pollArray["send_mail"]=="yes"){$Parray['send_mail']="yes";}else{$Parray['send_mail']="no";}	

		$this->updateQuery($Parray,$pollWhere);
	}
	
	public function getContributors($poll,$cat)
	{
		$PollDetails=$this->getPolldetails($poll);
		
		/*$where='';
		
		if($PollDetails[0]['contributors']==0)
			$where.=" AND u.profile_type='senior'";
		elseif($PollDetails[0]['contributors']==1)
			$where.=" AND u.profile_type='junior'";
		else
			$where.=" AND u.profile_type in ('senior','junior')";*/
		
		$where=" AND u.profile_type='senior'";
			
		//Black list
		if($PollDetails[0]['black_contrib']=='no')
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
							u.status='Active'" . $where ;

    /* AND
		favourite_category Like '%".str_replace(",","%' OR favourite_category Like '%",$cat)."%'*/

		$resultcontrib = $this->getQuery($SelContrib,true);
			
			for($r=0;$r<count($resultcontrib);$r++)
			{
				$resultcontrib[$r]['end']=$PollDetails[0]['priority_hours'];
			}
			
		return $resultcontrib;
		
	}
	
	public function ListPoll()
	{
		$SelPoll="SELECT * FROM ".$this->_name." WHERE valid_status is NULL";
		$resultPoll = $this->getQuery($SelPoll,true);
		return $resultPoll;
	}
	
	public function getPolldetails($pll)
	{
		$getPoll="SELECT * FROM ".$this->_name." WHERE id=".$pll;
		$resultPoll = $this->getQuery($getPoll,true);
		return $resultPoll;
	}
	
	public function UpdatePoll($poll,$param)
	{
		$Pollwhere=" id='".$poll."'";
		
		$PollArray=array();
		
		if($param=='0')
			$PollArray['valid_status']='inactive';
		elseif($param=='1')
			$PollArray['valid_status']='active';
			
		$this->updateQuery($PollArray,$Pollwhere);
	}
	
	public function UpdatePollall($pollar,$par)
	{
		$poll=implode(",",$pollar);
		$Pollwhere=" id in (".$poll.")";
		
		$PollArray=array();
		
		if($par=='0')
			$PollArray['valid_status']='inactive';
		elseif($par=='1')
			$PollArray['valid_status']='active';
			
		$this->updateQuery($PollArray,$Pollwhere);
	}
	
	public function getPollspecfilename($pid)
	{
		$getPoll="SELECT title,file_name FROM ".$this->_name." WHERE id=".$pid;
		$resultPoll = $this->getQuery($getPoll,true);
		return $resultPoll;
	}
	
	public function ListPollactive()
	{
		$SelPoll="SELECT id,title FROM ".$this->_name;
		$resultPoll = $this->getQuery($SelPoll,true);
		return $resultPoll;
	}
	
	public function ListPollresult($poll="")
	{
		$where="";
		if($poll!="")
			$where=" AND pp.status='active' AND p.id=".$poll;
		
		$SelPoll="	SELECT 
						p.id,p.title,p.created_at,p.poll_date,p.priority_hours,p.category,p.cron_mail,p.created_by
						,count(pp.id) as participation,max(pp.price_user) as maxprice,min(pp.price_user) as minprice,sum(pp.price_user) as sumprice
						,p.client,p.file_name,p.status 
					FROM
						Poll p LEFT JOIN Poll_Participation pp
					ON
						p.id=pp.poll_id 
					WHERE 
						p.valid_status='active'  ". $where."
					GROUP BY p.id ORDER BY created_at DESC";
						//echo $SelPoll;exit;
		$resultPoll = $this->getQuery($SelPoll,true);
		
		if($poll=="")
		{
			for($ao=0;$ao<count($resultPoll);$ao++)
			{
				//$resultPoll[$ao]['aocompare']=count($this->AoPollCompare($resultPoll[$ao]['id']));	
				$cdetail=$this->PollContributorDetails($resultPoll[$ao]['client']);
				$resultPoll[$ao]['clientname']=$cdetail[0]['first_name'].'&nbsp;'.$cdetail[0]['last_name'];
				
				if($cdetail[0]['first_name']=="")
					$resultPoll[$ao]['clientname']=$cdetail[0]['email'];
			}		
		}
		
		return $resultPoll;
	}
	
	public function ListPollsearchresult($search)
	{
		$searchby = "";
		
		if($search['sorttype']=="")
			$search['sorttype']='notclosed';
		if($search['start_date']!="" && $search['end_date']!="")
			$searchby.=" AND DATE_FORMAT( p.created_at, '%d/%m/%Y' ) >= '".$search['start_date']."' AND DATE_FORMAT( p.created_at, '%d/%m/%Y' ) <= '".$search['end_date']."'";
		if($search['start_datepublish']!="" && $search['end_datepublish']!="")
			$searchby.=" AND DATE_FORMAT( p.poll_date, '%d/%m/%Y' ) >= '".$search['start_datepublish']."' AND DATE_FORMAT( p.poll_date, '%d/%m/%Y' ) <= '".$search['end_datepublish']."'";
		if($search['client']!="all" && $search['client']!="")
			$searchby.=" AND p.client ='".$search['client']."'";
		if($search['category']!="all" && $search['category']!="")
			$searchby.=" AND p.category ='".$search['category']."'";
		if($search['sorttype']!="all" && $search['sorttype']!="")
			$searchby.=" AND p.status ='".$search['sorttype']."'";

			
			
		$SelPoll="	SELECT 
						p.id,p.title,p.created_at,p.poll_date,p.priority_hours,p.category,p.cron_mail,p.created_by
						,count(pp.id) as participation,max(pp.price_user) as maxprice,min(pp.price_user) as minprice,sum(pp.price_user) as sumprice
						,p.client,p.file_name,p.status 
					FROM
						Poll p LEFT JOIN Poll_Participation pp
					ON
						p.id=pp.poll_id 
					WHERE 
						p.valid_status='active'  ".$searchby."
					GROUP BY p.id ORDER BY created_at DESC";
						//echo $SelPoll;//exit;
		$resultPoll = $this->getQuery($SelPoll,true);
		
		if($poll=="")
		{
			for($ao=0;$ao<count($resultPoll);$ao++)
			{
				$resultPoll[$ao]['aocompare']=count($this->AoPollCompare($resultPoll[$ao]['id']));	
				$cdetail=$this->PollContributorDetails($resultPoll[$ao]['client']);
				$resultPoll[$ao]['clientname']=$cdetail[0]['first_name'].'&nbsp;'.$cdetail[0]['last_name'];
				
				if($cdetail[0]['first_name']=="")
					$resultPoll[$ao]['clientname']=$cdetail[0]['email'];
			}		
		}
		
		return $resultPoll;
	}
	
	public function ListPollresultedit($poll)
	{
		$SelPoll="	SELECT 
						* 
					FROM 
						Poll p 
					WHERE 
						p.valid_status='active' AND p.id=".$poll."
					";
						//echo $SelPoll;
		$resultPoll = $this->getQuery($SelPoll,true);
		
		return $resultPoll;
	}
	
	public function getPollpriceset($poll)
	{
		$SelectQueryJC="SELECT 
							count(p.id) as jcparticipation,max(p.price_user) as jcmaxprice,min(p.price_user) as jcminprice,sum(p.price_user) as jcsumprice 
						FROM 
							Poll_Participation p INNER JOIN User u ON p.user_id=u.identifier 
						WHERE 
							p.poll_id='".$poll."' AND p.status='active' AND u.profile_type='junior'";
		$resultPollJC = $this->getQuery($SelectQueryJC,true);					
	
		$SelectQuerySC="SELECT 
							count(p.id) as scparticipation,max(p.price_user) as scmaxprice,min(p.price_user) as scminprice,sum(p.price_user) as scsumprice 
						FROM 
							Poll_Participation p INNER JOIN User u ON p.user_id=u.identifier 
						WHERE 
							p.poll_id='".$poll."' AND p.status='active' AND u.profile_type='senior'";
		$resultPollSC = $this->getQuery($SelectQuerySC,true);

		$SelectQueryall="SELECT 
							count(p.id) as participation,max(p.price_user) as maxprice,min(p.price_user) as minprice,sum(p.price_user) as sumprice 
						FROM 
							Poll_Participation p INNER JOIN User u ON p.user_id=u.identifier 
						WHERE 
							p.poll_id='".$poll."' AND p.status='active'";
		$resultPollall = $this->getQuery($SelectQueryall,true);	
		
		$resultall=array_merge($resultPollJC,$resultPollSC,$resultPollall);
		return $resultall;
	}
	
	public function ListPollPartcipation($id,$var="")
	{
		if($var=='min')
			$OrderBy=' ORDER BY p.price_user ASC limit 1';
		elseif($var=='max')
			$OrderBy=' ORDER BY p.price_user DESC limit 1';
		else
			$OrderBy='';
		
		$wherest="";
		if($var!="")
			$wherest=" p.status='active' AND ";
			
		$SelPoll="SELECT p.*,u.first_name,u.last_name,us.email FROM Poll_Participation p LEFT JOIN UserPlus u ON p.user_id=u.user_id INNER JOIN User us ON us.identifier=p.user_id WHERE ".$wherest." p.poll_id=".$id.$OrderBy;
		$resultPoll = $this->getQuery($SelPoll,true);
		return $resultPoll;
	}
	
	public function ListPollPartcipationmoderate($id,$smic="")
	{
		$smicCond="";
		if($smic==1)
		{
			$smicval=$this->getSMICvalue($id);  
			//$smicval=11;
			$smicCond=" p.price_user>".$smicval." AND ";
		}	
			
		$SelPoll="SELECT pl.title,p.*,u.first_name,u.last_name,us.email FROM Poll pl INNER JOIN Poll_Participation p ON pl.id=p.poll_id LEFT JOIN UserPlus u ON p.user_id=u.user_id INNER JOIN User us ON us.identifier=p.user_id WHERE ".$smicCond." p.poll_id=".$id.$OrderBy;
		$resultPoll = $this->getQuery($SelPoll,true);
		return $resultPoll;
	}
	
	public function PollContributorDetails($user)
	{
		$SelPoll="SELECT * FROM User us LEFT JOIN UserPlus u ON us.identifier=u.user_id LEFT JOIN Contributor c ON u.user_id=c.user_id WHERE u.user_id=".$user;
		$resultPoll = $this->getQuery($SelPoll,true);
		return $resultPoll;
	}
	
	public function clientpolls($cl)
	{
		$SelPolls="SELECT * FROM Poll WHERE poll_date < CURRENT_TIMESTAMP() AND client=".$cl;
		$resultPolls = $this->getQuery($SelPolls,true);
		return $resultPolls;
	}
	
	public function AoPollCompare($pp)
	{
		$Pollres="SELECT * FROM Delivery d INNER JOIN Article a ON d.id=a.delivery_id INNER JOIN Participation p ON a.id=p.article_id WHERE d.poll_id='".$pp."' AND p.status in ('bid','under_study','time_out','validated','published','disapproved','approved','on_hold')";
		$resultPolls = $this->getQuery($Pollres,true);
		return $resultPolls;
	}
	
	public function CompareAOdetails($pol)
	{
		$aodetres="SELECT d.id,d.title,d.total_article,min(p.price_user) as price_min,max(p.price_user) as price_max, avg(p.price_user) as price_avg FROM Delivery d INNER JOIN Article a ON d.id=a.delivery_id INNER JOIN Participation p ON a.id=p.article_id WHERE d.poll_id='".$pol."' AND p.status in ('bid','under_study','time_out','validated','published','disapproved','approved','on_hold') ";
		$resultao = $this->getQuery($aodetres,true);
		
		//Participation 
		$aoparts="SELECT count(id) as participation FROM Participation WHERE article_id in (SELECT id FROM Article WHERE delivery_id='".$resultao[0]['id']."')";
		$resultpart = $this->getQuery($aoparts,true);
		$resultao[0]['participation']=$resultpart[0]['participation'];
		return $resultao;
	}
	
	public function ComparePolldetails($pol)
	{
		$polldetres="SELECT p.title,p.total_article,min(pp.price_user) as price_min,max(pp.price_user) as price_max, avg(pp.price_user) as price_avg, count(pp.id) as participation FROM Poll p LEFT JOIN Poll_Participation pp ON p.id=pp.poll_id WHERE p.id='".$pol."'";
		$resultpol = $this->getQuery($polldetres,true);
		return $resultpol;
	}
	
	public function getMailcount($user,$black,$cat)
	{
		if($user==0)
			$where.=" AND u.profile_type='senior'";
		elseif($user==1)
			$where.=" AND u.profile_type='junior'";
		elseif($user==2)
			$where.=" AND u.profile_type in ('senior','junior')";
		elseif($user==3)
			$where.=" AND u.profile_type in ('sub-junior')";
		else
			$where.=" AND u.profile_type in ('junior','senior','sub-junior')";
			
		//Black list
		if($black=='no')
			$where.=" AND blackstatus='no'";
			
		$SelContrib11="SELECT 
							count(*) as mailcount
					FROM 
							User u 
					LEFT JOIN 
							Contributor c 
					ON 
							u.identifier=c.user_id 
					WHERE 
							u.type='contributor' AND 
							u.status='Active' ".$where;
							//AND favourite_category Like '%".str_replace(",","%' OR favourite_category Like '%",$cat)."%'
		//echo $SelContrib11;
		$resultcontrib = $this->getQuery($SelContrib11,true);
			
		return $resultcontrib[0]['mailcount'];
	}
	
	public function getMailids($user,$black,$cat)
	{
		if($user==0)
			$where.=" AND u.profile_type='senior'";
		elseif($user==1)
			$where.=" AND u.profile_type='junior'";
		elseif($user==2)
			$where.=" AND u.profile_type in ('senior','junior')";
		elseif($user==3)
			$where.=" AND u.profile_type in ('sub-junior')";
		else
			$where.=" AND u.profile_type in ('junior','senior','sub-junior')";
			
		//Black list
		if($black=='no')
			$where.=" AND blackstatus='no'";
			
		$SelContrib11="SELECT 
							u.email
					FROM 
							User u 
					LEFT JOIN 
							Contributor c 
					ON 
							u.identifier=c.user_id 
					WHERE 
							u.type='contributor' AND 
							u.status='Active' ".$where." ORDER BY u.email";
		//echo $SelContrib11;
		$resultcontrib = $this->getQuery($SelContrib11,true);
			
		return $resultcontrib;
	}
	
	public function closepoll($poll,$param)
	{
		$pollWhere=" id=".$poll;
		
		$Parray=array();
		$Parray['status']=$param;
		$this->_name="Poll";
		$this->updateQuery($Parray,$pollWhere);
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
	
	public function PollPartcipationsAll($id)
	{
		$SelPoll="SELECT 
						p.price_user,
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
	
	public function pollstosendmails($polls)
	{
		$getPolls="SELECT * FROM Poll WHERE id IN (".$polls.")";
		$resultpolls = $this->getQuery($getPolls,true);//echo $getPolls;
		return $resultpolls;
	}
	
	public function getContributorsPoll($poll,$cat,$contrib,$black)
	{
		$where='';
		
		if($contrib==0)
			$where.=" AND u.profile_type='senior'";
		elseif($contrib==1)
			$where.=" AND u.profile_type='junior'";
		else
			$where.=" AND u.profile_type in ('senior','junior')";
			
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
							u.status='Active' AND
							favourite_category Like '%".str_replace(",","%' OR favourite_category Like '%",$cat)."%'".$where;
		
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
	
	public function InactiveBelowSMIC($poll)
	{
		$smic = $this->getSMICvalue($poll);
		
		$this->_name="Poll_Participation";
		$pollWhere=" price_user<".$smic;
		
		$Pparray=array();
		$Pparray['status']="inactive";
		
		$this->updateQuery($Pparray,$pollWhere);
	}
	
	public function getSMICvalue($poll)
	{
		$SMICQuery="SELECT l.SMIC,c.percentage FROM Poll p INNER JOIN  LanguageSMIC l ON p.language=l.id INNER JOIN  CategoryDifficultyPercent c ON p.category=c.id WHERE p.id='".$poll."'";
		
		if(($SMICresult=$this->getQuery($SMICQuery,true))!=NULL)
			return $SMICresult[0]['SMIC'] * ($SMICresult[0]['percentage']/100);
	}
	
	public function getSMICpoll($lang,$cat)
	{
		$SMICQuery="SELECT l.SMIC,c.percentage FROM LanguageSMIC l ,CategoryDifficultyPercent c WHERE l.id='".$lang."' AND c.id='".$cat."'";
		
		if(($SMICresult=$this->getQuery($SMICQuery,true))!=NULL)
			return $SMICresult[0]['SMIC'] * ($SMICresult[0]['percentage']/100);
	}
	
	public function pollquestiondetails($poll,$user)
	{
		$PollqQuery="SELECT 
					p.title,p.language,p.category,pq.title as question,pq.type,pq.option,pu.response,u.first_name,u.last_name 
				FROM 
					Poll p LEFT JOIN PollUserResponse pu ON p.id=pu.poll_id 
					LEFT JOIN Poll_question pq ON p.id=pq.pollid AND pu.question_id=pq.id 
					LEFT JOIN UserPlus u ON u.user_id=pu.user_id 
				WHERE 
					p.id='".$poll."'  AND pu.user_id='".$user."' ";
		
		$Pollqresult=$this->getQuery($PollqQuery,true);
		return $Pollqresult;
	}
}