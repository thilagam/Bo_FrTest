<?php

class Ep_Ao_Participation extends Ep_Db_Identifier
{
	protected $_name = 'Participation';

	public function getAoParticipations($aid,$sort="")
	{
		$sortby="";
		
		if($sort=="date")
			$sortby="ORDER BY p.created_at";
		if($sort=="price")
			$sortby="ORDER BY p.price_user";
			
		$SelQuery="	SELECT 
						a.id,a.title,a.id as artid,a.contrib_percentage,p.user_id,p.price_user,p.created_at,p.valid_date,LOWER(u.first_name) as first_name,LOWER(u.last_name) as last_name,p.cycle 
					FROM 
						Delivery d INNER JOIN Article a ON d.id=a.delivery_id 
						INNER JOIN Participation p ON a.id=p.article_id 
						LEFT JOIN UserPlus u ON p.user_id=u.user_id 
					WHERE 
						a.id='".$aid."' ".$sortby;
		
		if(($ResultSet= $this->getQuery($SelQuery,true)) != NULL)
            return $ResultSet;
	}
	
	public function clientWriters($client)
	{
		$writerQuery="	SELECT 
							DISTINCT(p.user_id),u.first_name,u.last_name 
						FROM 
							Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id 
						WHERE 
							p.status NOT IN ('bid_premium','bid_nonpremium','bid_refused') AND 
							p.article_id in (SELECT a.id FROM Article a INNER JOIN Delivery d ON a.delivery_id=d.id 
						WHERE 
							d.user_id='".$client."') LIMIT 4";
		
		if(($writerSet= $this->getQuery($writerQuery,true)) != NULL)
            return $writerSet;
	}
	
	public function publishedWriters($client)
	{
		$writerQuery="	SELECT 
							DISTINCT(p.user_id),u.first_name,u.last_name 
						FROM 
							Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id 
						WHERE 
							p.status NOT IN ('bid_premium','bid_nonpremium','bid_refused') AND 
							p.article_id in (SELECT a.id FROM Article a INNER JOIN Delivery d ON a.delivery_id=d.id 
						WHERE 
							d.user_id='".$client."') AND p.status='published' AND p.cycle='0' ";
		
		if(($writerSet= $this->getQuery($writerQuery,true)) != NULL)
            return $writerSet;
	}
	
	public function writercontact($article)
	{
		$writerQuery="	SELECT 
							p.id,u.email,u.identifier,LOWER(up.first_name) as first_name,LOWER(up.last_name) as last_name,up.phone_number 
						FROM 
							Participation p INNER JOIN UserPlus up ON p.user_id=up.user_id 
							INNER JOIN User u  ON up.user_id=u.identifier 
						WHERE 
							p.status IN ('bid','under_study','published','plag_exec','closed_client_temp','disapprove_client','closed','disapproved','time_out','closed_client') 
							AND p.article_id='".$article."' AND p.cycle='0' ";
		//echo $writerQuery;
		if(($writerSet= $this->getQuery($writerQuery,true)) != NULL)
            return $writerSet;
	}
	
	public function selectedparticipation($article)
	{
		$AoQuery="	SELECT 
						d.id,d.premium_option,d.premium_total,d.client_type,d.filepath,d.created_by,a.title,p.article_submit_expires,a.id as article_id,a.contrib_percentage,a.category,a.language,a.num_min,a.num_max,ap.article_name,a.status as articlestatus,p.price_user,p.status,p.id as participateid,ap.article_sent_at,ap.article_path,a.paid_status,a.invoice_id,a.currency,
						TIMESTAMPDIFF( MINUTE ,now( ), FROM_UNIXTIME( p.article_submit_expires ) ) AS minutediff,ap.id as artproId,a.invoice_generated
					FROM 
						Delivery d INNER JOIN Article a ON d.id=a.delivery_id 
						INNER JOIN Participation p ON a.id=p.article_id 
						LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id 
					WHERE 
						a.id='".$article."' AND p.status IN ('bid','under_study','published','plag_exec','closed_client_temp','disapprove_client','disapproved','closed','time_out','closed_client') AND p.cycle='0' ORDER BY ap.version DESC";
	
		if(($AoResult= $this->getQuery($AoQuery,true)) != NULL)
            return $AoResult;
	}
	
	public function getProfileDetails($participation,$article)
	{
		$detailQuery="	SELECT 
							EXTRACT( YEAR FROM c.dob ) AS byear,EXTRACT(YEAR FROM CURDATE()) as curryear,a.id as artid, p . * , up . * , a . *,a.status as articlestatus,c.*,d.premium_option,d.created_by
						FROM 
							UserPlus up INNER JOIN Contributor c ON up.user_id=c.user_id 
							LEFT JOIN Participation p ON up.user_id=p.user_id 
							INNER JOIN Article a ON p.article_id=a.id
							INNER JOIN Delivery d ON d.id=a.delivery_id
						WHERE 
							up.user_id='".$participation."' AND p.article_id='".$article."' ";
				
		$details = $this->getQuery($detailQuery,true);
			
				$details[0]['participations']=$this->participationcount($participation,'total');
				$details[0]['selectedcount']=$this->participationcount($participation,'selected');
				$details[0]['clients']=$this->getcustomersPublished($participation);
			
		return $details;
	}
	
	public function getPollProfileDetails($participation,$poll)
	{
		$detailQuery="	SELECT 
							EXTRACT( YEAR FROM c.dob ) AS byear,EXTRACT(YEAR FROM CURDATE()) as curryear,p.id as pollid, p.*,pp . * , up . * ,c.*
						FROM 
							UserPlus up INNER JOIN Contributor c ON up.user_id=c.user_id 
							LEFT JOIN Poll_Participation pp ON up.user_id=pp.user_id 
							INNER JOIN Poll p ON pp.poll_id=p.id
						WHERE 
							up.user_id='".$participation."' AND p.id='".$poll."' ";
			
		$details = $this->getQuery($detailQuery,true);
			
				$details[0]['participations']=$this->participationcount($participation,'total');
				$details[0]['selectedcount']=$this->participationcount($participation,'selected');
				$details[0]['clients']=$this->getcustomersPublished($participation);
			
		return $details;
	}
	
	public function getcontribProfileDetails($contrib)
	{
		$detailQuery="	SELECT 
							EXTRACT( YEAR FROM c.dob ) AS byear,EXTRACT(YEAR FROM CURDATE()) as curryear,p . * , up . * ,c.*,u.*
						FROM 
							User u INNER JOIN UserPlus up ON u.identifier=up.user_id 
							INNER JOIN Contributor c ON up.user_id=c.user_id 
							LEFT JOIN Participation p ON up.user_id=p.user_id 
						WHERE 
							up.user_id='".$contrib."'";
				
		$details = $this->getQuery($detailQuery,true);
		$details[0]['clients']=$this->getcustomersPublished($contrib);
		return $details;
	}
	
	public function getContribexp($contrib,$type)
	{
		$expQuery="	SELECT * FROM ContributorExperience WHERE user_id='".$contrib."' AND type='".$type."'";
		$expdetails = $this->getQuery($expQuery,true);
		return $expdetails;
	}
	
	public function participationcount($part,$typ)
	{
		$whereQry="";
		if($typ=='selected')
			$whereQry=" AND status IN ('bid','under_study','published','plag_exec','disapprove_client','disapproved')";
		
		$ParticipationQuery="SELECT count(*) as count FROM Participation WHERE user_id='".$part."' ".$whereQry;
		$Participations = $this->getQuery($ParticipationQuery,true);
		return $Participations[0]['count'];
	}
	
	public function getcustomers($part)
	{
		$ParticipationQuery="SELECT 
								DISTINCT(c.company_name),c.user_id 
							FROM 
								Client c INNER JOIN Delivery d ON c.user_id=d.user_id 
								INNER JOIN Article a ON d.id=a.delivery_id 
								INNER JOIN Participation p ON a.id=p.article_id 
							WHERE 
								p.user_id='".$part."'";
		$Participations = $this->getQuery($ParticipationQuery,true);
		return $Participations;
	}
	
	public function getcustomersPublished($part)
	{
		$ParticipationQuery="SELECT 
								DISTINCT(c.company_name),c.user_id 
							FROM 
								Client c INNER JOIN Delivery d ON c.user_id=d.user_id 
								INNER JOIN Article a ON d.id=a.delivery_id 
								INNER JOIN Participation p ON a.id=p.article_id 
							WHERE 
								p.user_id='".$part."' AND p.status IN ('published')";
		$Participations = $this->getQuery($ParticipationQuery,true);
		return $Participations;
	}
	
	public function updateparticipation($Parray,$Where)
	{
		$Parray["updated_at"] = date("Y-m-d H:i:s");  
		$this->updateQuery($Parray,$Where);
	}
	
	public function Participationtorefuse($art)
	{
		$ParticipationQuery="SELECT user_id FROM Participation WHERE article_id='".$art."' AND status IN ('bid_premium','bid_nonpremium') AND cycle='0' ";
		$Participations = $this->getQuery($ParticipationQuery,true);
		return $Participations;
	}
	
	public function CheckPublished($art)
	{
		$PubParticipationQuery="SELECT user_id FROM Participation WHERE article_id='".$art."' AND status IN ('published') AND cycle='0' ";
		$PubParticipations = $this->getQuery($PubParticipationQuery,true);
		if(count($PubParticipations)>0)
			return "YES";
		else
			return "NO";
	}
	
	public function getLanguage($uid)
	{
		$LangParticipationQuery="SELECT language FROM Contributor WHERE user_id='".$uid."'";
		$LangParticipations = $this->getQuery($LangParticipationQuery,true);
		return $LangParticipations[0]['language'];
	}
	
	public function CheckOnlineAgain($article)
	{
		$CheckQuery1="SELECT * FROM Participation WHERE article_id='".$article."' AND cycle='0'";
		$Checkcondition1 = $this->getQuery($CheckQuery1,true);
		
		$CheckQuery2="SELECT * FROM Participation WHERE article_id='".$article."' AND cycle='0' AND status in ('bid_premium','bid_nonpremium')";
		$Checkcondition2 = $this->getQuery($CheckQuery2,true);
		
		if(count($Checkcondition1)==0 || count($Checkcondition2)>1)
			return "YES";
		else
			return "NO";
	}
	
	public function getCarousel1Contribs()
	{
		/*$ContribQuery="SELECT 
							u.identifier,up.first_name,c.language,c.category_more,c.favourite_category,count(p.id) as pubcount 
						FROM 
							User u INNER JOIN UserPlus up ON u.identifier=up.user_id 
							INNER JOIN Contributor c ON u.identifier=c.user_id 
							LEFT JOIN Participation p ON u.identifier=p.user_id 
						WHERE 
							u.type='contributor' AND u.profile_type='senior' AND p.status IN ('published','under_study') 
						GROUP BY u.identifier 
						ORDER BY pubcount DESC";*/
						
		$ContribQuery="SELECT 
							u.identifier,up.first_name,c.language,c.category_more,c.favourite_category
						FROM 
							User u INNER JOIN UserPlus up ON u.identifier=up.user_id 
							INNER JOIN Contributor c ON u.identifier=c.user_id 
						WHERE 
							u.type='contributor' AND u.status = 'Active' AND u.blackstatus = 'no' AND up.first_name!='' AND up.last_name!='' ORDER BY RAND()";
						
		$ContribResult = $this->getQuery($ContribQuery,true);
		return $ContribResult;
	}
    
    public function getCarousel2Contribs($cat)
    {
        $ContribQuery2="SELECT 
                            u.identifier,up.first_name,c.language,c.category_more,c.favourite_category
                        FROM 
                            User u INNER JOIN UserPlus up ON u.identifier=up.user_id 
                            INNER JOIN Contributor c ON u.identifier=c.user_id 
                        WHERE 
                            u.type='contributor' AND u.status = 'Active' AND u.blackstatus = 'no' AND up.first_name!='' AND up.last_name!='' AND c.favourite_category like '%".$cat."%' ORDER BY RAND()";
                        
        $ContribResult2 = $this->getQuery($ContribQuery2,true);
        return $ContribResult2;
    }
    
    public function getContribsByLanCat($lan, $cat)
    {
        $langCondition = (!empty($lan)) ? "AND c.language='$lan'" : '' ;
        $ContribQuery="SELECT 
                            u.identifier,up.first_name,c.language,c.category_more,c.favourite_category
                        FROM 
                            User u INNER JOIN UserPlus up ON u.identifier=up.user_id 
                            INNER JOIN Contributor c ON u.identifier=c.user_id 
                        WHERE 
                            u.type='contributor' AND u.status = 'Active' AND u.blackstatus = 'no' $langCondition ORDER BY RAND()";
        
        $ContribResults = $this->getQuery($ContribQuery,true);
        
        if(!empty($cat))
        {
            //echo '<pre>';print_r($ContribResults);
            foreach($ContribResults as $key=>$ContribResult)
            {
                if(is_array(unserialize($ContribResult['category_more'])))
                {
                    $serArr = unserialize($ContribResult['category_more']);
                    if(!$serArr[$cat] || ($serArr[$cat] < 80))
                        unset($ContribResults[$key]);
                }
                else
                {
                    unset($ContribResults[$key]);
                }
            }
        }
        elseif(!empty($cat))
        {
            //echo '<pre>';print_r($ContribResults);
            foreach($ContribResults as $key=>$ContribResult)
            {
                if(is_array(unserialize($ContribResult['language_more'])))
                {
                    $serArr = unserialize($ContribResult['language_more']);
                    if(!$serArr[$cat] || ($serArr[$cat] < 80))
                        unset($ContribResults[$key]);
                }
                else
                {
                    unset($ContribResults[$key]);
                }
            }
        }
        $ContribResults = array_values($ContribResults);
        return $ContribResults;
    }
	
	public function checkbidrefusal($article)
	{
		$refuseQuery="SELECT id FROM Participation WHERE article_id='".$article."' AND status='bid_refused' AND cycle='0'";
		$refuseResult = $this->getQuery($refuseQuery,true);
		
		$countQuery="SELECT id FROM Participation WHERE article_id='".$article."' AND cycle='0'";
		$countResult = $this->getQuery($countQuery,true);
		
		if((count($refuseResult)==count($countResult)) && count($countResult)>0)
			return "no";
		else
			return "yes";
	}
	
	public function ListallfavContribs($client)
	{
		$ContriballQuery="SELECT 
							u.identifier,u.email,up.first_name,up.last_name
						FROM 
							User u INNER JOIN UserPlus up ON u.identifier=up.user_id 
							INNER JOIN Favourite_contributor f ON u.identifier=f.contrib_id
						WHERE 
							u.type='contributor' AND u.status='Active' AND f.client_id='".$client."' AND f.status='1'";
						
		$ContriballResult = $this->getQuery($ContriballQuery,true);
		return $ContriballResult;
	}
	
	public function getmailcontribs($article)
	{
		$mailcontribsQuery="SELECT 
							user_id
						FROM 
							Participation
						WHERE 
							article_id='".$article."' AND cycle='0' AND status IN ('bid_premium','bid_nonpremium','bid','under_study','published','plag_exec','closed_client_temp','disapprove_client','disapproved','closed','time_out','closed_client')";
						
		$mailcontribsResult = $this->getQuery($mailcontribsQuery,true);
		return $mailcontribsResult;
	}
	
	public function getMaxcycle($art)
	{
		$cycleQuery="SELECT 
							max(cycle) as mcycle
						FROM 
							Participation
						WHERE 
							article_id='".$art."' ";
						
		$cycleResult = $this->getQuery($cycleQuery,true);
		return $cycleResult[0]['mcycle'];
	}
	
	public function getarticletime($artid)
	{
		$timeQuery="SELECT 
							TIMESTAMPDIFF( MINUTE ,now( ), FROM_UNIXTIME( article_submit_expires ) ) AS minutediff
						FROM 
							Participation
						WHERE 
							article_id = '".$artid."' AND article_submit_expires IS NOT NULL";
						
		$timeResult = $this->getQuery($timeQuery,true);
		return $timeResult[0]['minutediff'];
	}
	
	public function getParticipationCount($article)
	{
		$Query="SELECT * FROM Participation WHERE article_id='".$article."' AND cycle='0'";
		$result = $this->getQuery($Query,true);
		return count($result);
	}
	
	public function getAttendCount($article)
	{
		$Query1="SELECT * FROM Participation WHERE article_id='".$article."' AND cycle='0' AND status IN ('bid_premium','bid_nonpremium')";
		$result1 = $this->getQuery($Query1,true);
		return count($result1);
	}
	
	public function getparticipation($art)
	{
		$Query2="SELECT a.title,p.user_id,p.status,p.id FROM Article a INNER JOIN Participation p ON a.id=p.article_id WHERE p.article_id='".$art."' AND p.cycle='0' AND 
		p.status IN ('bid_nonpremium','bid','under_study','published','disapproved')";
		$result2 = $this->getQuery($Query2,true);
		return $result2;
	}
	
	public function getCategorywriters($cat)
	{
		$Query3="SELECT 
					DISTINCT(p.user_id),up.first_name,up.last_name 
				FROM 
					Article a INNER JOIN Participation p ON a.id=p.article_id 
					INNER JOIN UserPlus up ON p.user_id=up.user_id 
					INNER JOIN Contributor c ON c.user_id=up.user_id
				WHERE 
					a.category like '%".$cat."%' AND p.cycle='0' AND 
					p.status IN ('under_study','published','disapproved')";
		$result3 = $this->getQuery($Query3,true);
		return $result3;
	}
	
}	