<?php
/**
 * Ep_Article_Delivery
 * @author Admin
 * @package Delivery
 * @version 1.0
 */
class Ep_Article_Delivery extends Ep_Db_Identifier
{
  protected $_name = 'Delivery';
  private $id;
  private $user_id;
  private $title;
  private $delivery_date;
  private $contract_type;
  private $contract_text;
  private $price_min;
  private $price_max;
  private $type;
  private $user_brief;
  private $status;
  private $created_at;
  private $updated_at;
    
  public function loadData($array)
  {
    $this->setId($array["id"]) ;
    $this->setUser_id($array["user_id"]) ;
    $this->setTitle($array["title"]) ;
    $this->setDelivery_date($array["delivery_date"]) ;
    $this->setContract_type($array["contract_type"]) ;
    $this->setContract_text($array["contract_text"]) ;
    $this->setPrice_min($array["price_min"]) ;
    $this->setPrice_max($array["price_max"]) ;
    $this->setType($array["type"]) ;
    $this->setUser_brief($array["user_brief"]) ;
    $this->setStatus($array["status"]) ;        
    $this->setCreated_at($array["created_at"]) ;
    $this->setUpdated_at($array["updated_at"]) ;        
    return $this;
  }
  public function loadintoArray()
  {
    $array = array();
    $array["id"] = $this->getId();
    $array["user_id"] = $this->getUser_id();
    $array["title"] = $this->getTitle();
    $array["delivery_date"] = $this->getDelivery_date();
    $array["contract_type"] = $this->getContract_type();
    $array["contract_text"] = $this->getContract_text();
    $array["price_min"] = $this->getPrice_min();
    $array["price_max"] = $this->getPrice_max();
    $array["type"] = $this->getType();
    $array["user_brief"] = $this->getUser_brief();
    $array["status"] = $this->getStatus();
    $array["created_at"] = $this->getCreated_at() ;
    $array["updated_at"] = $this->getUpdated_at() ;
    return $array;
  }
  ////////////////////////////////////////////////////////////Set methods ////////////////////////////////////////////////////////////////////////////////  
  public function setId($x = NULL) { $this->id = $x; } 
  public function setUser_id($x = NULL) { $this->user_id = $x; } 
  public function setTitle($x = NULL) { $this->title = $x; } 
  public function setDelivery_date($x = NULL) { $this->delivery_date = $x; } 
  public function setContract_type($x = NULL) { $this->contract_type = $x; } 
  public function setContract_text($x = NULL) { $this->contract_text = $x; } 
  public function setPrice_min($x = NULL) { $this->price_min = $x; } 
  public function setPrice_max($x = NULL) { $this->price_max = $x; } 
  public function setType($x = NULL) { $this->type = $x; } 
  public function setUser_brief($x = NULL) { $this->user_brief = $x; } 
  public function setStatus($x = NULL) { $this->status = $x; } 
  public function setCreated_at($x = NULL) { $this->created_at = $x; } 
  public function setUpdated_at($x = NULL) { $this->updated_at = $x; }  
  ////////////////////////////////////////////////////////////Get methods //////////////////////////////////////////////////////////////////////////////  
  public function getId() { return $this->id; } 
  public function getUser_id() { return $this->user_id; } 
  public function getTitle() { return $this->title; } 
  public function getDelivery_date() { return $this->delivery_date; } 
  public function getContract_type() { return $this->contract_type; } 
  public function getContract_text() { return $this->contract_text; } 
  public function getPrice_min() { return $this->price_min; } 
  public function getPrice_max() { return $this->price_max; } 
  public function getType() { return $this->type; } 
  public function getUser_brief() { return $this->user_brief; } 
  public function getStatus() { return $this->status; } 
  public function getCreated_at() { return $this->created_at; } 
  public function getUpdated_at() { return $this->updated_at; }
    public function getRecentAoOffers($fav_category=NULL,$limit=5,$searchParameters=NULL)
    {
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
        $user_language=$this->EP_Contrib_reg->user_language;

        //get contrib test field
        $profileContrib_obj = new Ep_Contrib_ProfileContributor();
        $profile_contribinfo=$profileContrib_obj->getProfileInfo($userIdentifier);
        if(count($profile_contribinfo)>0 && is_array($profile_contribinfo))
        {
          $contributorTest=$profile_contribinfo[0]['contributortest'];
          $contributortestmarks=$profile_contribinfo[0]['contributortestmarks'];
          if(!$contributortestmarks)
            $contributortestmarks=0;
        } 
        else
        {
          $contributorTest='no';
          $contributortestmarks=0;
        }  


        if(!$userIdentifier)
        {
            $privateQuery="(d.AOtype='public')";
        }
        else
        {
            $privateUser=$userIdentifier;
             if($searchParameters['profile_type']=='senior')
                $view_to=" AND find_in_set('sc', a.view_to)>0";
            elseif($searchParameters['profile_type']=='junior')
                $view_to=" AND find_in_set('jc', a.view_to)>0";
            elseif($searchParameters['profile_type']=='sub-junior')
                $view_to=" AND find_in_set('jc0', a.view_to)>0";
            //$privateQuery="((d.AOtype='public' $view_to AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
            
            //removed $view_to to display all articles

            $publish_users=" AND find_in_set('".$user_language."', a.language)>0";

            $privateQuery="((d.AOtype='public' $publish_users AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";              
        }
        $condition=" and a.id not in( select article_id from Participation pa where pa.user_id='".$userIdentifier."' or pa.status in ('published','bid','under_study','on_hold'))";
         /*added w.r.t article should not show to the respective corrector**/
        $condition.=" and a.id not in( select article_id from  CorrectorParticipation cp where cp.corrector_id='".$userIdentifier."' and cp.status in ('published','bid','under_study','on_hold','disapproved'))";
        //Added w.r.t QuizParticipation
        $condition.=" and d.quiz not in(select quiz_id from  QuizParticipation qp where qp.qualified='no' and qp.user_id='".$userIdentifier."')";                     

        //test required condition 
        $condition.=" AND ((a.testrequired='yes' AND a.testmarks<=$contributortestmarks AND find_in_set('".$contributorTest."', a.testrequired)>0)|| a.testrequired='no') ";

        //differed AO publish condition
        if($searchParameters['upcoming'])
            $publish_condition=" AND (publishtime > UNIX_TIMESTAMP()) ";
        else
            $publish_condition=" AND (publishtime < UNIX_TIMESTAMP() OR publishtime=0 OR publishtime is NULL) ";


         //show only premium articles if user is in black list
        if($searchParameters['black_status']=='yes')
        {
            $condition.=" and d.premium_option!='0' and d.premium_option!=''";           

        } 


        /*Query With Dynamic percentage*/
        $query=" SELECT u.identifier,d.id as deliveryid,d.AOtype,d.deli_anonymous,a.id as articleid,a.title as title,
                 email,CONCAT(up.first_name,' ',up.last_name) as username,c.company_name,
                 IF(a.contrib_price,a.contrib_price,(((a.price_min)*a.contrib_percentage)/100)) as price_min,
                 IF(a.contrib_price,a.contrib_price,(((a.price_max)*a.contrib_percentage)/100)) as price_max,
                 a.category,d.premium_total,a.participation_expires,d.premium_option,d.link_quiz,d.quiz,a.contribs_list,
                 d.publishtime,a.view_to,d.missiontest,a.language,a.category,a.type,
                 d.missiontest,d.mission_volume,a.currency,
                    a.product,a.language_source,
                    cont.language AS mother_tounge, cont.language_more,
                    d.sourcelang_nocheck
                 FROM Delivery d
                 INNER JOIN  User u ON u.identifier=d.user_id
                 LEFT JOIN UserPlus up ON d.user_id=up.user_id
                 LEFT JOIN  Client c  ON u.identifier=c.user_id
                 INNER JOIN Payment p ON p.delivery_id=d.id
                 INNER JOIN Article a ON a.delivery_id=d.id
                 LEFT JOIN Participation pa ON pa.article_id = a.id
                    LEFT JOIN Contributor cont ON cont.user_id = '".$userIdentifier."'
                 where p.status='Paid' and a.status!='validated' and d.status_bo='active' ".$condition."
                 and ".$privateQuery." AND (a.participation_expires > UNIX_TIMESTAMP())
                 $publish_condition
                 and missiontest!='yes'
                    AND (
                            (a.product = 'redaction')
                            OR
                            (
                                a.product = 'translation'
                                AND cont.translator = 'yes'
                                AND cont.language = a.language

                            )
                        )
                 GROUP BY articleid
                 ORDER BY a.participation_expires ASC,title ASC LIMIT 0,".$limit;
                 //ORDER BY a.participation_expires ASC,title ASC,deliveryid DESC,articleid DESC LIMIT 0,".$limit;

         //echo $query;exit;        
                 
        if(($count=$this->getNbRows($query))>0)
        {
            $latest_AoOffers=$this->getQuery($query,true);
            return $latest_AoOffers;
        }
    }
    public function getArticleBrief($articleIdentifier)
    {
        $query="select a.id,a.title,d.filepath,d.title as delivery,d.user_id as client,a.delivery_id,d.	recruitment_file_path  from ".$this->_name." d
                INNER JOIN Article a ON a.delivery_id=d.id
                where a.id='".$articleIdentifier."'";
        //echo $query;exit;
        if(($count=$this->getNbRows($query))>0)
        {
            $articleBrief=$this->getQuery($query,true);
            return $articleBrief;
        }
    }
    public function checkpublicPremiumAO($articleId)
    {
        $query="select premium_option,AOtype from ".$this->_name." d INNER JOIN Article a ON d.id=a.delivery_id
                where a.id='".$articleId."'";
        //echo $query;
        if(($count=$this->getNbRows($query))>0)
        {
            $premium=$this->getQuery($query,true);
             if($premium[0]['premium_option']!='0' && ($premium[0]['AOtype']=='public' || $premium[0]['AOtype']=='private'))
                return "premium";
           //  else if($premium[0]['AOtype']=='private')
           //      return "private";
            // else if($premium[0]['premium_option']=='0' && $premium[0]['AOtype']=='public')
             else if($premium[0]['premium_option']=='0' && ($premium[0]['AOtype']=='public' || $premium[0]['AOtype']=='private'))
                return "nonpremium";               
        }
        else
            return "NO";
    }
    public function checkPremiumAO($articleId)
    {
        $query="select premium_option,AOtype from ".$this->_name." d INNER JOIN Article a ON d.id=a.delivery_id
                where a.id='".$articleId."'";
        //echo $query;exit;
        if(($count=$this->getNbRows($query))>0)
        {
            $premium=$this->getQuery($query,true);
             if($premium[0]['premium_option']!='0' && $premium[0]['premium_option']!='' )
                return "YES";
             else
                return "NO";
        }
        
    }
  public function getEndAOOfToday()
    {
       //$deliverydate=date("Y-m-d", strtotime("-1 day"));
        $deliverydate=date("Y-m-d");
       $query="SELECT d.created_at,u.email,u.identifier,d.title,d.mail_send FROM Delivery d
                INNER JOIN Article a ON a.delivery_id=d.id
                INNER JOIN User u ON d.user_id=u.identifier
                WHERE d.delivery_date='".$deliverydate."'
                GROUP BY d.id";
        //echo $query;exit;
       if(($count=$this->getNbRows($query))>0)
        {
            $end_AoOffers=$this->getQuery($query,true);
            return $end_AoOffers;
        }
        else
            return "NO";
    }
     /////////get delivery full detials  w.r.t article id  ///////////////////////////
  public function getDeliveryDetails($articleId)
  {
      $query = "select d.*,a.title as articleName,d.mail_send, a.language,
                  d.title AS deliveryTitle, d.premium_option,d.submitdate_bo,a.contribs_list ,d.user_id as client,
                  IF(d.created_user,d.created_user,d.user_id) as created_user,a.*,a.id as article_id,a.paid_status,a.correction_type as correctionType,d.id, a.nomoderation,a.sc_resubmission as article_sc_resubmission,a.jc_resubmission as article_sc_resubmission,a.jc0_resubmission as article_sc_resubmission,a.correction as articlecorrection,a.correction_type as articlecorrectiontype,d.correction_type as deliverycorrectiontype,a.corrector_privatelist as articlecorrectors, a.contribs_list as articlewriters,a.currency,
				  a.correction_sc_submission,a.correction_jc_submission,a.correction_participation
                FROM ".$this->_name." d
                INNER JOIN Article a ON d.id=a.delivery_id
                WHERE a.id='".$articleId."'";
    if(($result = $this->getQuery($query,true)) != NULL)
      return $result;
    else
      return "NO";
  }
    public function checkLastArticleAO($deliveryId)
    {
        $query="SELECT count(*) as ArticleCount,(SELECT count(*) FROM Participation p
                    INNER JOIN Article a ON a.id=p.article_id
                    INNER JOIN Delivery d ON d.id=a.delivery_id
                where d.id='".$deliveryId."' and p.status='published') ParticipationCount FROM Article WHERE delivery_id='".$deliveryId."'"
                ;
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
        {
              if($result[0]['ArticleCount']==$result[0]['ParticipationCount'])
                  return "YES";
              else
                  return "NO";
        }
    else
      return "NO";
    }
    /**get corrector brief path**/
    public function getCorrectorArticleBrief($articleIdentifier)
    {
        $query="select a.id,a.title,d.filepath,d.correction_file,d.id as delivery_id,d.title as delivery_name from ".$this->_name." d
                INNER JOIN Article a ON a.delivery_id=d.id
                where a.id='".$articleIdentifier."'";
        //echo $query;exit;
        if(($count=$this->getNbRows($query))>0)
        {
            $articleBrief=$this->getQuery($query,true);
            return $articleBrief;
        }
    }
	
	// republish without moderation
	public function getArtDeliveryDetails($articleId)
	{
	     $query = "select d.*,a.*, d.id, a.id AS artId, a.title as articleName, a.publish_language AS publang, d.mail_send, d.title AS deliveryTitle, d.premium_option, d.correction_type AS delcrttype,
	 	 d.submitdate_bo, a.contribs_list AS contribslist from ".$this->_name." d
		 INNER JOIN Article a ON d.id=a.delivery_id
    	 WHERE a.id=".$articleId;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
	
	public function getDeliveryID($articleId)
    {
        $query="select delivery_id from Article where id='".$articleId."'";
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result[0]['delivery_id'];
		else
			return "NO";
    }
	
	public function getPrAoDetails($delId)
	{
		  $query = "SELECT d.*, a.*, d.category AS del_category, a.id as articleid, d.title AS aoName, d.participation_time AS participation_time, d.senior_time AS senior_time, 						                   a.title as artname, cl.company_name, u.email, u.identifier,
		          GROUP_CONCAT(DISTINCT a.category) as fav_category, GROUP_CONCAT(DISTINCT a.contribs_list) as article_contribs, d.contribs_list,d.missiontest,
				  IF(d.created_by='BO',d.created_user,d.user_id) as created_user
		          FROM ".$this->_name." d
		          INNER JOIN  Article a ON a.delivery_id=d.id
		          LEFT JOIN DeliveryOptions o ON o.delivery_id=d.id
		          LEFT JOIN Client cl ON d.user_id=cl.user_id
		          INNER JOIN User u ON d.user_id=u.identifier
		          WHERE d.id ='".$delId."'";//echo $query;exit;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
	
	public function getArticlesOfDel($delId)
    {
        $query="SELECT d.id, d.title, d.user_id, a.participation_time, GROUP_CONCAT( a.title
            SEPARATOR '|') AS artTitles, GROUP_CONCAT( a.id SEPARATOR '@') AS artIds,a.correction_participation FROM ".$this->_name." d
            LEFT JOIN  Article a ON a.delivery_id=d.id
            WHERE d.id = ".$delId;
        //echo $query;exit;
        $result = $this->getQuery($query,true);
    }

    public function getPrAoDetailsWithArtid($artId)
    {
        $query ="SELECT d.*, a.id as articleid, a.title as artname, a.price_min, a.price_max, a.contribs_list,a.corrector_privatelist,d.missiontest,a.language AS artLanguage,
                a.priority_contributors, a.republish_object, a.republish_mail, a.participation_expires, count(a.id) as noofarts, cl.company_name, u.email,a.estimated_worktime,a.estimated_workoption,
                u.identifier,GROUP_CONCAT(DISTINCT a.category) as fav_category, GROUP_CONCAT(DISTINCT a.contribs_list) as article_contribs,
                GROUP_CONCAT(DISTINCT a.corrector_privatelist) as private_correctors,
                (select p.status FROM Participation p Where p.cycle=0 and p.article_id=a.id ORDER BY IF(p.updated_at!='',p.updated_at,p.created_at) DESC LIMIT 0,1) as status
                FROM  Delivery d
                INNER JOIN Article a ON a.delivery_id=d.id
                LEFT JOIN DeliveryOptions o ON o.delivery_id=d.id
               LEFT JOIN Client cl ON d.user_id=cl.user_id
                INNER JOIN User u ON d.user_id=u.identifier
                WHERE a.id ='".$artId."'";
        
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

	public function getPollcontribDetails($poll,$contrib)
	{
		$Pollquery = "SELECT DATE_FORMAT(p.poll_date, '%d/%m/%y %H:%i:%s') AS poll_date,p.title,pp.price_user FROM Poll p INNER JOIN Poll_Participation pp ON p.id=pp.poll_id
		          WHERE p.id =".$poll." AND pp.user_id=".$contrib;

		if(($resultpoll = $this->getQuery($Pollquery,true)) != NULL)
			return $resultpoll;
	else
			return "NO";
	}	
	
	public function getContributorsAO($type,$condition1,$profiles)
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
            $condition="WHERE status='Active' AND blackstatus='no' AND (favourite_category Like '%".str_replace(",","%' OR favourite_category Like '%",$condition1)."%') AND profile_type IN ('".$profile1."')";
           $query="select DISTINCT u.identifier, u.profile_type  FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition;

        }
		//echo $query;exit;
       if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
	   else
			return "NO";
    }
    /////////get delivery created Bo user details///////////
    public function getDelCreatedUser($delId)
    {
        $query="SELECT d.id, d.title, d.user_id, d.created_user, u.email, up.first_name, up.last_name FROM ".$this->_name." d
            LEFT JOIN User u ON u.identifier=d.created_user
            LEFT JOIN UserPlus up ON up.user_id=d.created_user
            LEFT JOIN  Article a ON a.delivery_id=d.id
            LEFT JOIN Participation p ON p.article_id=a.id
             WHERE d.id = ".$delId;
        //echo $query;exit;
        $result = $this->getQuery($query,true);
        return $result;
    }

}

