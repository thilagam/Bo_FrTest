<?php
/**
 * Ep_Recruitment_Participation
 * @author Arun
 * @package Poll
 * @version 1.0
 */
class Ep_Recruitment_Participation extends Ep_Db_Identifier
{
    protected $_name = 'Participation';

    public function InsertParticipation($recruitArray)
	{
		$Aarray = array();
		$Aarray['id']=$this->getIdentifier(); 
		$Aarray['article_id']=$recruitArray['recruitment_article_id'];
		$Aarray['watchlist_id']=$recruitArray['watchlist_id'];		
		$Aarray['user_id']=$recruitArray['user_id'];
		$Aarray['price_user']=$recruitArray['proposed_cost'];
		$Aarray['status']=$recruitArray['status'];
        $Aarray['created_at']=date("Y-m-d H:i:s");
        $Aarray['accept_refuse_at']=date("Y-m-d H:i:s");
		$Aarray['article_submit_expires']=$recruitArray['article_submit_expires'];        
        $Aarray["accept_specifications"]='yes';
        //$Aarray["contract_signed"]='';
		$Aarray['articles_per_week']=$recruitArray['articles_per_week'];						
		

		//echo "<pre>";print_r($Aarray);exit;
		
		if($this->insertQuery($Aarray))
			return $this->getIdentifier(); 
		 else
			return "NO";
	}

    /*public function getRecruitmentDetails($recruitment_id)
    {
    	setlocale(LC_TIME, "fr_FR");
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
        $profileType=$this->EP_Contrib_reg->profileType;
        $addQuery='';

        $privateUser=$userIdentifier;
            //added to restrict Ao's for only for a group of profiles
             if($searchParameters['profile_type']=='senior')
               $view_to=" AND find_in_set('sc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='junior')
                $view_to=" AND find_in_set('jc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='sub-junior')
                $view_to=" AND find_in_set('jc0', d.view_to)>0";
            
            $publish_users=" AND find_in_set('".$user_language."', a.publish_language)>0";

            $privateQuery="((d.AOtype='public' $view_to $publish_users AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
	        
        

    	$recruitQuery="SELECT a.*,a.id as recruitment_id,p.price_user,p.articles_per_week,p.id as recruitmentParticipationId,p.article_submit_expires,p.is_hired,
    					a.participation_expires,d.max_articles_per_contrib,d.delivery_period,d.mission_volume,d.total_article,
    					d.created_user,d.editorial_chief_review,d.quiz as quiz_id,
    					d.link_quiz,d.id as deliveryid,p.status,d.contract_mission_id,d.user_id as client_id
    			FROM Delivery d
    			INNER JOIN Article a ON a.delivery_id=d.id
    			LEFT JOIN Participation p ON p.article_id=a.id AND p.user_id='".$userIdentifier."'    			
    			WHERE 
       			 a.id='".$recruitment_id."' AND
    			$privateQuery    			
    			GROUP BY a.id    			
    			";
                //(a.participation_expires > UNIX_TIMESTAMP()) OR (((p.article_submit_expires=0 OR p.article_submit_expires > UNIX_TIMESTAMP())OR(p.article_submit_expires <= UNIX_TIMESTAMP() AND p.status in ('bid','time_out','under_study','plag_exec','disapproved')) OR (p.status in ('closed','closed_client') AND p.updated_at >= ( CURDATE() - INTERVAL 2 DAY ))) AND cycle=0) AND

    	//echo $recruitQuery;exit;

    	if(($count=$this->getNbRows($recruitQuery))>0)
        {
            $RecruitmentDetails=$this->getQuery($recruitQuery,true);
            return $RecruitmentDetails;
        }		
    }*/
    public function getRecruitmentDetails($recruitment_id,$article_id=NULL)
    {
        setlocale(LC_TIME, "fr_FR");
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
        $profileType=$this->EP_Contrib_reg->profileType;
        $addQuery='';

        $privateUser=$userIdentifier;
            //added to restrict Ao's for only for a group of profiles
             if($searchParameters['profile_type']=='senior')
               $view_to=" AND find_in_set('sc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='junior')
                $view_to=" AND find_in_set('jc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='sub-junior')
                $view_to=" AND find_in_set('jc0', d.view_to)>0";
            
            //$publish_users=" AND find_in_set('".$user_language."', a.publish_language)>0";

            $privateQuery="((d.AOtype='public' $view_to $publish_users AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
            
        if($article_id)
           $article_condition=" AND a.id='".$article_id."'";

        $recruitQuery="SELECT a.*,a.id as recruit_art_id,d.id as recruitment_id,d.title as recruitment_title,d.price_min,d.price_max,
                        a.participation_expires,d.max_articles_per_contrib,d.delivery_period,d.delivery_time_frame,
                        d.mission_volume,d.total_article,d.product,d.num_hire_writers,d.created_at as recruitment_created_at,d.free_article ,
                        d.created_user,d.editorial_chief_review,d.quiz as quiz_id,
                        d.link_quiz,d.id as deliveryid,d.contract_mission_id,d.user_id as client_id,d.recruitment_file_name,
                        p.id as recruitmentParticipationId,p.article_submit_expires,p.articles_per_week,
                        p.price_user as proposed_cost,p.status,p.contract_signed,p.article_id,
						(SELECT count(pa.id) FROM Participation pa 
							INNER JOIN Article a1 ON a1.id=pa.article_id
							INNER JOIN Delivery d1 ON d1.id=a1.delivery_id
							WHERE d1.id=d.id
						) as active_participations,a.currency
                FROM Delivery d
                INNER JOIN Article a ON a.delivery_id=d.id 
                LEFT JOIN Participation p ON p.article_id=a.id AND p.user_id='".$userIdentifier."'                 
                WHERE 
                 d.id='".$recruitment_id."' AND
                $privateQuery  
                $article_condition             
                GROUP BY d.id                
                ";
                //(a.participation_expires > UNIX_TIMESTAMP()) OR (((p.article_submit_expires=0 OR p.article_submit_expires > UNIX_TIMESTAMP())OR(p.article_submit_expires <= UNIX_TIMESTAMP() AND p.status in ('bid','time_out','under_study','plag_exec','disapproved')) OR (p.status in ('closed','closed_client') AND p.updated_at >= ( CURDATE() - INTERVAL 2 DAY ))) AND cycle=0) AND

        //echo $recruitQuery;exit;

        if(($count=$this->getNbRows($recruitQuery))>0)
        {
            $RecruitmentDetails=$this->getQuery($recruitQuery,true);
            return $RecruitmentDetails;
        }       
    }
    public function updateParticipation($data,$participationId)
    {
        $where=" id='".$participationId."'";
       // print_r($data);echo $where;exit;
        return $this->updateQuery($data,$where);
    }

    //get recent Recruitments created displayed in home page
    public function getRecentRecruitmentOffers($limit=5,$searchParameters=NULL)
  {
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
        $user_language=$this->EP_Contrib_reg->user_language;

        if(!$userIdentifier)
        {
            $privateQuery="(d.AOtype='public')";
        }
        else
        {
            $privateUser=$userIdentifier;
             if($searchParameters['profile_type']=='senior')
                $view_to=" AND find_in_set('sc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='junior')
                $view_to=" AND find_in_set('jc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='sub-junior')
                $view_to=" AND find_in_set('jc0', d.view_to)>0";
            //$privateQuery="((d.AOtype='public' $view_to AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
            
            //removed $view_to to display all articles

            $publish_users=" AND find_in_set('".$user_language."', a.publish_language)>0";

            $privateQuery="((d.AOtype='public' $publish_users AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";              
        }
        
        if($searchParameters['request_page']=='aosearch')
		{
            $condition=" and d.id not in(SELECT d1.id From Delivery d1 INNER JOIN  Article a1 ON a1.delivery_id=d1.id INNER JOIN Participation pa ON pa.article_id=a1.id Where pa.status!='bid_pending' AND pa.user_id='".$userIdentifier."')";
			$having='Having ParticipateCount <= max_participants';
		}	
        else
		{
            $condition=" and d.id not in(SELECT d1.id From Delivery d1 INNER JOIN  Article a1 ON a1.delivery_id=d1.id INNER JOIN Participation pa ON pa.article_id=a1.id Where pa.user_id='".$userIdentifier."')";       
			$having='Having ParticipateCount < max_participants';
		}	
        

         /*added w.r.t article should not show to the respective corrector**/
        $condition.=" and a.id not in( select article_id from  CorrectorParticipation cp where cp.corrector_id='".$userIdentifier."')";
        //Added w.r.t QuizParticipation
        //$condition.=" and d.id not in(select delivery_id from  QuizParticipation qp where qp.qualified='no' and qp.user_id='".$userIdentifier."')";                     
		$condition.=" and d.quiz not in(select quiz_id from  QuizParticipation qp where qp.qualified='no' and qp.user_id='".$userIdentifier."')";
        $publish_condition=" AND (publishtime < UNIX_TIMESTAMP() OR publishtime=0 OR publishtime is NULL) ";

        /*Query With Dynamic percentage*/
        $query=" SELECT u.identifier,d.id as deliveryid,d.AOtype,d.deli_anonymous,a.id as articleid,d.title as title,d.user_id as client_id,
                 email,CONCAT(up.first_name,' ',up.last_name) as username,c.company_name,
                 IF(a.contrib_price,a.contrib_price,(((a.price_min)*a.contrib_percentage)/100)) as price_min,
                 IF(a.contrib_price,a.contrib_price,(((a.price_max)*a.contrib_percentage)/100)) as price_max,
                 a.participation_expires,d.premium_option,d.link_quiz,d.quiz,d.contribs_list,
                 d.publishtime,d.view_to,d.language,d.category,d.type,d.product,
                 d.missiontest,d.mission_volume,d.delivery_period,d.delivery_time_frame,d.editorial_chief_review,
                 (SELECT count(*) as ParticipateCount FROM `Participation` p2
                    JOIN  Article a2 ON a2.id=p2.article_id
                    JOIN Delivery d2 ON d2.id=a2.delivery_id
                    WHERE d2.id=d.id
                  ) as ParticipateCount,d.total_article as max_participants,
                 (SELECT p1.article_id 
                        FROM Participation p1 
                        INNER JOIN Article a1 ON a1.id=p1.article_id 
                        INNER JOIN Delivery d1 ON d1.id=a1.delivery_id
                        WHERE p1.status='bid_pending' AND p1.user_id=$userIdentifier AND d1.id=d.id
                 ) as recruitment_article_id,
d.max_articles_per_contrib,
a.currency


                 FROM Delivery d
                 INNER JOIN  User u ON u.identifier=d.user_id
                 INNER JOIN UserPlus up ON d.user_id=up.user_id
                 INNER JOIN  Client c  ON u.identifier=c.user_id
                 INNER JOIN Payment p ON p.delivery_id=d.id
                 INNER JOIN Article a ON a.delivery_id=d.id
                 LEFT JOIN Participation pa ON pa.article_id = a.id
                 where p.status='Paid' and a.status!='validated' and d.status_bo='active' AND (d.stoprecruitment IS NULL OR d.stoprecruitment!='stop') ".$condition."
                 and ".$privateQuery." AND (a.participation_expires > UNIX_TIMESTAMP()) 
                 $publish_condition
                 and missiontest='yes'                 
                 GROUP BY deliveryid
                 $having
                 ORDER BY a.participation_expires ASC,title ASC LIMIT 0,".$limit;
                 //ORDER BY a.participation_expires ASC,title ASC,deliveryid DESC,articleid DESC LIMIT 0,".$limit;

         //echo $query;exit;
                 
        if(($count=$this->getNbRows($query))>0)
        {
            $latest_RecruitmentOffers=$this->getQuery($query,true);
            return $latest_RecruitmentOffers;
        }
    }
    //get not assigned article of a delivery to assign to a user of a recruitment
    public function getNotAssignedArticle($delivery_id)
    {

        $articleQuery="SELECT a.* FROM Article a
                INNER JOIN Delivery d ON d.id=a.delivery_id
                Where missiontest='yes' AND d.id='".$delivery_id."' AND
                a.id not in(
                    SELECT article_id FROM Participation p 
                    INNER JOIN Article a1 ON a1.id=p.article_id
                    INNER JOIN Delivery d1 ON d1.id=a1.delivery_id AND d1.id='".$delivery_id."'
                )
                ORDER BY a.id LIMIT 0,1
                ";


         //echo $articleQuery;exit;
        if(($articleDetails=$this->getQuery($articleQuery,true))!=NULL)
            return $articleDetails;
        else
            return NULL;    
    }   

    //participation details
    public function getParticipationDetails($participation_id)     
    {
        $query="Select * From Participation Where id='".$participation_id."'";
        if(($participationDetails=$this->getQuery($query,true))!=NULL)
            return $participationDetails;
        else
            return NULL;

    }
	public function checkParticipationExist($user_id,$recruitment_id)
	{
		$participationQuery="SELECT *
					  FROM  $this->_name p
					  JOIN Article a ON a.id=p.article_id
					  JOIN Delivery d ON d.id=a.delivery_id
  	 				  WHERE p.user_id='".$user_id."' AND a.delivery_id='".$recruitment_id."'";
		//echo $participationQuery;exit;
		if(($count=$this->getNbRows($participationQuery))>0)
        {            
	        return "YES";
        }
        else
            return "NO";
	}
	
	// to check participation expires// added by kavitha
	public function CheckParticipationExpired($delivery)
	{
		$ArticleQuery="SELECT count(*) as artcount,d.stoprecruitment FROM `Article` a INNER JOIN Delivery d ON a.delivery_id=d.id WHERE a.delivery_id= '".$delivery."' AND a.participation_expires >= UNIX_TIMESTAMP()";
		$ArticleDetails=$this->getQuery($ArticleQuery,true);
		return $ArticleDetails;
	}
	
}