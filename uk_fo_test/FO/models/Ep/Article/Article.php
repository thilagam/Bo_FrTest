<?php
/**
 * Ep_Articles_Article
 * @author Arun
 * @package Articles
 * @version 1.0
 */
class Ep_Article_Article extends Ep_Db_Identifier
{
    protected $_name = 'Article';
    private $id;
    private $delivery_id;
    private $title;
    private $language;
    private $category;
    private $type;
    private $nbwords;
    private $status;
    private $summary;
    private $created_at;
    private $updated_at;
    public function loadData($array)
    {
        $this->setId($array["id"]) ;
        $this->setDelivery_id($array["delivery_id"]) ;
        $this->setTitle($array["title"]) ;
        $this->setLanguage($array["language"]) ;
        $this->setCategory($array["category"])  ;
        $this->setType($array["type"])  ;
        $this->setNbwords($array["nbwords"]) ;
        $this->setStatus($array["status"])  ;
        $this->setSummary($array["summary"])  ;
        $this->setCreated_at($array["created_at"]) ;
        $this->setUpdated_at($array["updated_at"])  ;
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["id"] = $this->getId();
        $array["delivery_id"] = $this->getDelivery_id;
        $array["title"] = $this->getTitle;
        $array["language"] = $this->getLanguage;
        $array["category"] = $this->getCategory;
        $array["type"] = $this->getType;
        $array["nbwords"] = $this->getNbwords;
        $array["status"] = $this->getStatus;
        $array["summary"] = $this->getSummary;
        $array["created_at"] = $this->getCreated_at;
        $array["updated_at"] = $this->getUpdated_at;
        return $array;
    }
    ////////////////////////////////////////////////////////////Set methods ////////////////////////////////////////////////////////////////////////////////
    public function setId($id) { $this->id = $id; }
    public function setDelivery_id($delivery_id) { $this->delivery_id = $delivery_id; }
    public function setTitle($title) { $this->title = $title; }
    public function setLanguage($language) { $this->language = $language; }
    public function setCategory($category) { $this->category = $category; }
    public function setType($type) { $this->type = $type; }
    public function setNbwords($nbwords) { $this->nbwords = $nbwords; }
    public function setStatus($status) { $this->status = $status; }
    public function setSummary($summary) { $this->summary = $summary; }
    public function setCreated_at($created_at) { $this->created_at = $created_at; }
    public function setUpdated_at($updated_at) { $this->updated_at = $updated_at; }
    ////////////////////////////////////////////////////////////Get methods //////////////////////////////////////////////////////////////////////////////
    public function getId() { return $this->id; }
    public function getDelivery_id() { return $this->delivery_id; }
    public function getTitle() { return $this->title; }
    public function getLanguage() { return $this->language; }
    public function getCategory() { return $this->category; }
    public function getType() { return $this->type; }
    public function getNbwords() { return $this->nbwords; }
    public function getStatus() { return $this->status; }
    public function getSummary() { return $this->summary; }
    public function getCreated_at() { return $this->created_at; }
    public function getUpdated_at() { return $this->updated_at; }
   
    public function getArticleDetails($searchParameters,$limit=NULL)
    {
        setlocale(LC_TIME, "fr_FR");
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
            /*added to restrict Ao's for only for a group of profiles*/
             if($searchParameters['profile_type']=='senior')
               $view_to=" AND find_in_set('sc', a.view_to)>0";
            elseif($searchParameters['profile_type']=='junior')
                $view_to=" AND find_in_set('jc', a.view_to)>0";
            elseif($searchParameters['profile_type']=='sub-junior')
                $view_to=" AND find_in_set('jc0', a.view_to)>0";
            
            $publish_users=" AND find_in_set('".$user_language."', a.language)>0";

            $privateQuery="((d.AOtype='public' $view_to $publish_users AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
        }

        $addQuery='';
        if($searchParameters['articleId']!=NULL)
            $addQuery.=" and a.id in ('".$searchParameters['articleId']."')";
        if($searchParameters['search_type']!=NULL)
            $addQuery.=" and a.type='".$searchParameters['search_type']."'";
        if($searchParameters['search_timeremaining']!=NULL)
        {
            $days=explode("-",$searchParameters['search_timeremaining']);
            if($days[0]!=NULL && $days[1]!=NULL)
               $haveQuery=" Having remainingDays>=".$days[0]." and remainingDays<=".$days[1];
        }
        if($searchParameters['search_participants']!=NULL)
        {
            $paricipants=explode("-",$searchParameters['search_participants']);
            if($paricipants[0]!=NULL && $paricipants[1]!=NULL)
            {
               if(!$haveQuery)
                    $haveQuery=" Having totalParticipation>=".$paricipants[0]." and totalParticipation<=".$paricipants[1];
               else
                   $haveQuery.=" and totalParticipation>=".$paricipants[0]." and totalParticipation<=".$paricipants[1];
            }
        }
        if($searchParameters['search_price']!=NULL)
        {
            $price=explode("-",$searchParameters['search_price']);
            if($price[0]!=NULL && $price[1]!=NULL)
                $addQuery.=" and ((IF(a.contrib_price,a.contrib_price,(((a.price_min)*a.contrib_percentage)/100)) BETWEEN ".$price[0]." and ".$price[1].") OR (IF(a.contrib_price,a.contrib_price,(((a.price_max)*a.contrib_percentage)/100)) BETWEEN ".$price[0]." and ".$price[1]."))" ;
        }
        if($searchParameters['search_article']!=NULL)
        {
            //$words=str_word_count($searchParameters['search_article'],1);
            $searchParameters['search_article'] = preg_replace('/\s*$/','',$searchParameters['search_article']);
            $searchParameters['search_article']=preg_replace('/\(|\)/','',$searchParameters['search_article']);
            $searchParameters['search_article']=addslashes($searchParameters['search_article']);
            $words=explode(" ",$searchParameters['search_article']);
           if(count($words)>1)
           {
                $addQuery.=" and (a.title like '%".($searchParameters['search_article'])."%' or";
                foreach($words as $key=>$word)
                {
                   if($word!='')
                   {
                       $addQuery.=" a.title REGEXP '[[:<:]]".($word)."[[:>:]]'";
                       //$addQuery.=" a.title like '%".($word)."%'";
                       if($key!=(count($words))-1)
                            $addQuery.=" or";
                   }
                }
                $addQuery.=")";
            }
            else
                $addQuery.=" and a.title  REGEXP '[[:<:]]".$searchParameters['search_article']."[[:>:]]'";
                //$addQuery.=" and a.title like '%".($searchParameters['search_article'])."%'";
        }
        if($searchParameters['search_relate']!=NULL)
            $addQuery.=" and a.id not in ('".$searchParameters['search_relate']."')";
        if($searchParameters['search_related_ids']!=NULL)
            $addQuery.=" and a.id not in (".$searchParameters['search_related_ids'].")";
        if($searchParameters['userId']!=NULL)
        {
            if($searchParameters['userId']=='anonymous')
                $addQuery.=" and d.deli_anonymous='1'";
            else
                $addQuery.=" and d.user_id='".$searchParameters['userId']."' and d.deli_anonymous='0'";
        }
        
        if($searchParameters['search_category']!=NULL)
            $addQuery.=" and a.category in ('".$searchParameters['search_category']."')";
        
        if($searchParameters['search_language']!=NULL)
            $addQuery.=" and a.language='".$searchParameters['search_language']."'";    
        //show only premium articles if user is in black list
        if($searchParameters['search_ao_type']!=NULL OR $searchParameters['black_status']=='yes')
        {
            if($searchParameters['search_ao_type']=='m_premium' OR $searchParameters['black_status']=='yes')
                $addQuery.=" and d.premium_option!='0' and d.premium_option!=''";
            else if($searchParameters['search_ao_type']=='m_npremium')
                $addQuery.=" and (d.premium_option='0' OR d.premium_option='')";
        }
            
        if(!$searchParameters['req_from'])
        {
            $condition=" and a.id not in( select article_id from Participation pa where pa.user_id='".$userIdentifier."' or pa.status in ('published','bid','under_study','on_hold','disapproved'))";
            /*added w.r.t article should not show to the respective corrector**/
            $condition.=" and a.id not in( select article_id from  CorrectorParticipation cp where cp.corrector_id='".$userIdentifier."' and cp.status in ('published','bid','under_study','on_hold','disapproved'))";
            //Added w.r.t QuizParticipation
            $condition.=" and d.quiz not in(select quiz_id from  QuizParticipation qp where qp.qualified='no' and qp.user_id='".$userIdentifier."')";
            $expiresCondition=' AND (a.participation_expires=0 OR a.participation_expires > UNIX_TIMESTAMP())';
        }                
        
        //differed AO publish time condition
        if($searchParameters['upcoming'])
            $publish_condition=" AND (publishtime > UNIX_TIMESTAMP()) ";
        else
            $publish_condition=" AND (publishtime < UNIX_TIMESTAMP() OR publishtime=0 OR publishtime is NULL) ";
        if($searchParameters['cnt_nextday'])
        {
            $today=getDateNextDays(0);
            $tomorrow=getDateNextDays(1);
            $publish_condition.=" AND publishtime BETWEEN '".$today."' AND '".$tomorrow."'";
        }    

        //test required condition 
        $condition.=" AND ((a.testrequired='yes' AND a.testmarks<=$contributortestmarks AND find_in_set('".$contributorTest."', a.testrequired)>0)|| a.testrequired='no') ";
        
        /**order By Condition**/
        
        if($searchParameters['orderByTitle'])
        {
            if($searchParameters['orderByTitle']=='desc')
                $orderQuery.="a.title DESC  ";
            else if($searchParameters['orderByTitle']=='asc')
                $orderQuery.="a.title ASC  ";   
        }
        else if($searchParameters['orderByLang'])
        {
            if($searchParameters['orderByLang']=='desc')
                $orderQuery.="a.language DESC  ";
            else if($searchParameters['orderByLang']=='asc')
                $orderQuery.="a.language ASC  ";    
        }
        else if($searchParameters['orderByAttendee'])
        {
            if($searchParameters['orderByAttendee']=='desc')
                $orderQuery.="totalParticipation DESC  ";
            else if($searchParameters['orderByAttendee']=='asc')
                $orderQuery.="totalParticipation ASC  ";    
        }
        else if($searchParameters['orderByQuotePrice'])
        {
            if($searchParameters['orderByQuotePrice']=='desc')
                $orderQuery.="price_min DESC  ";
            else if($searchParameters['orderByQuotePrice']=='asc')
                $orderQuery.="price_min ASC  "; 
        }
        else if($searchParameters['orderBytime'])
        {
            if($searchParameters['orderBytime']=='desc')
                $orderQuery.="remainingDays DESC  ";
            else if($searchParameters['orderBytime']=='asc')
                $orderQuery.="remainingDays ASC  "; 
        }
        //for upcoming articles
        else if($searchParameters['uorderByTitle'])
        {
            if($searchParameters['uorderByTitle']=='desc')
                $orderQuery.="a.title DESC  ";
            else if($searchParameters['uorderByTitle']=='asc')
                $orderQuery.="a.title ASC  ";   
        }
        else if($searchParameters['uorderByLang'])
        {
            if($searchParameters['uorderByLang']=='desc')
                $orderQuery.="a.language DESC  ";
            else if($searchParameters['uorderByLang']=='asc')
                $orderQuery.="a.language ASC  ";    
        }
        else if($searchParameters['uorderBytime'])
        {
            if($searchParameters['uorderBytime']=='desc')
                $orderQuery.="publishtime DESC  ";
            else if($searchParameters['uorderBytime']=='asc')
                $orderQuery.="publishtime ASC  "; 
        }
        
        if($orderQuery)
            $orderQuery=" ORDER BY ".$orderQuery;
        else
            $orderQuery=" ORDER BY a.participation_expires ASC,title ASC,deliveryid DESC,articleid DESC ";
        
        if($limit)
            $limitCondition=" limit 0,10";
        else
            $limitCondition="";
        $participationJoin=" LEFT JOIN Participation pa ON pa.article_id = a.id  ";
        
        /**Article Query With Dynamic percent **/
        $articleQuery="SELECT  count( pa.article_id ) as totalParticipation,c.company_name,d.AOtype,d.deli_anonymous,u.identifier,d.id as deliveryid,a.id as articleid,
                        a.title as title,a.language,a.category,a.type,a.created_at,a.updated_at,a.contrib_percentage,a.refusalreasons,
                        a.sign_type,a.num_min,a.num_max,IF(a.contrib_price,a.contrib_price,(((a.price_min)*a.contrib_percentage)/100)) as price_min,
                        IF(a.contrib_price,a.contrib_price,(((a.price_max)*a.contrib_percentage)/100)) as price_max,email,
                        DATEDIFF(IF(a.participation_expires,a.participation_expires,d.submitdate_bo),NOW()) as remainingDays,a.participation_expires,a.subjunior_time,a.junior_time,a.senior_time,u.profile_type,
                        d.participation_time,a.jc_resubmission,a.sc_resubmission,d.link_quiz,d.quiz,d.quiz_duration,d.pricedisplay,
                        GROUP_CONCAT(a.category) as filter_category,GROUP_CONCAT(a.language) as filter_language,a.type ,
                        d.premium_total,d.filepath,d.premium_option,a.contribs_list,
                        d.publishtime,a.view_to,d.missiontest,d.mission_volume,a.currency,a.product
                        
                 FROM Delivery d
                 INNER JOIN  User u ON u.identifier=d.user_id
                 LEFT JOIN  Client c  ON u.identifier=c.user_id
                 INNER JOIN Payment p ON p.delivery_id=d.id
                 INNER JOIN Article a ON a.delivery_id=d.id
                 ".$participationJoin."
                 where p.status='Paid' and a.status!='validated' and d.status_bo='active' ".$addQuery.$condition."
                 and ".$privateQuery.$expiresCondition."
                 $publish_condition
                 and missiontest!='yes'
                 GROUP BY deliveryid,articleid
                 ".$haveQuery.
                $orderQuery.$limitCondition;

                //
        //echo  $articleQuery;exit;
        
        if(($count=$this->getNbRows($articleQuery))>0)
        {
            $ArticleDetails=$this->getQuery($articleQuery,true);
            return $ArticleDetails;
        }
        //else
          //  return NULL;
    }
    /// know how many articles in a delivery//
    public function getArticleCountDelivery($delId)
    {
        $query = "select count(a.id) AS artCount, d.id
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 WHERE d.id='".$delId."' GROUP BY d.id";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /// get the list of article which done with participation time with participation table //
    public function getCountArticleDeliveryTimeUp()
    {
        $query = "select count(a.id) AS artCount
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 WHERE a.participation_expires < UNIX_TIMESTAMP() AND a.participation_expires >= (UNIX_TIMESTAMP()-(60)) GROUP BY d.id";
      //echo $query; exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /// get the list of correction article which done with participation time with participation table //
    public function getCountCrtArticleDeliveryTimeUp()
    {
        $query = "select count(a.id) AS artCount
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 WHERE a.correction_participationexpires < UNIX_TIMESTAMP() AND a.correction_participationexpires >= (UNIX_TIMESTAMP()-(60)) GROUP BY d.id";
        //echo $query; exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /// get the list of article which done with participation time with participation table //
    public function getArticleDeliveryTimeUp()
    {
        $query = "select a.id AS artId, a.title, a.republish_count, a.republish_by_at, d.id, d.title AS deliveryTitle, d.created_user, d.user_id, u.email, u.login, up.first_name, up.last_name
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 INNER JOIN User u ON u.identifier=d.created_user
                 LEFT JOIN UserPlus up ON up.user_id=d.created_user
                 WHERE a.participation_expires < UNIX_TIMESTAMP() AND a.participation_expires >= (UNIX_TIMESTAMP()-(60))";
        //echo $query; exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /// get the list of correction article which done with participation time with participation table //
    public function getCrtArticleDeliveryTimeUp()
    {
        $query = "select a.id AS artId, a.title, a.correction_republish_count, a.republish_by_at, d.id, d.title AS deliveryTitle, d.created_user, d.user_id, u.email, u.login, up.first_name, up.last_name
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 INNER JOIN User u ON u.identifier=d.created_user
                 LEFT JOIN UserPlus up ON up.user_id=d.created_user
                 WHERE a.correction_participationexpires < UNIX_TIMESTAMP() AND a.correction_participationexpires >= (UNIX_TIMESTAMP()-(60))";
        //echo $query; exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    public function getCycleInArts($artId)
    {
          $query = "select article_id FROM  Participation WHERE article_id = '".$artId."' AND status = 'bid_premium'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    public function getCycleInCrtArts($artId)
    {
        $query = "select article_id FROM  CorrectorParticipation WHERE article_id = '".$artId."' AND status = 'bid_corrector'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    // getting the max cycle in participation tabel wrt article//
    public function getMaxCycleParts($artId)
    {
        $query = "select max(cycle) AS maxcycle FROM Participation WHERE article_id = '".$artId."'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    // getting the max cycle in corrector participation tabel wrt article//
    public function getMaxCycleCrtParts($artId)
    {
        $query = "select max(cycle) AS maxcycle FROM CorrectorParticipation WHERE article_id = '".$artId."'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /**Added for showing all articles that were partcipated too**/
    public function getArticleSearchDetails($searchParameters,$limit=NULL)
    {
        setlocale(LC_TIME, "fr_FR");
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
            /*added to restrict Ao's for only for a group of profiles*/
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
        $addQuery='';
        if($searchParameters['articleId']!=NULL)
            $addQuery.=" and a.id in ('".$searchParameters['articleId']."')";
        if($searchParameters['search_type']!=NULL)
            $addQuery.=" and a.type='".$searchParameters['search_type']."'";
        if($searchParameters['search_timeremaining']!=NULL)
        {
            $days=explode("-",$searchParameters['search_timeremaining']);
            if($days[0]!=NULL && $days[1]!=NULL)
               $haveQuery=" Having remainingDays>=".$days[0]." and remainingDays<=".$days[1];
        }
        if($searchParameters['search_participants']!=NULL)
        {
            $paricipants=explode("-",$searchParameters['search_participants']);
            if($paricipants[0]!=NULL && $paricipants[1]!=NULL)
            {
               if(!$haveQuery)
                    $haveQuery=" Having totalParticipation>=".$paricipants[0]." and totalParticipation<=".$paricipants[1];
               else
                   $haveQuery.=" and totalParticipation>=".$paricipants[0]." and totalParticipation<=".$paricipants[1];
            }
        }
        if($searchParameters['search_price']!=NULL)
        {
            $price=explode("-",$searchParameters['search_price']);
            if($price[0]!=NULL && $price[1]!=NULL)
                $addQuery.=" and ((IF(a.contrib_price,a.contrib_price,(((a.price_min)*a.contrib_percentage)/100)) BETWEEN ".$price[0]." and ".$price[1].") OR (IF(a.contrib_price,a.contrib_price,(((a.price_max)*a.contrib_percentage)/100)) BETWEEN ".$price[0]." and ".$price[1]."))" ;
        }
        if($searchParameters['search_article']!=NULL)
        {
            //$words=str_word_count($searchParameters['search_article'],1);
            $searchParameters['search_article'] = preg_replace('/\s*$/','',$searchParameters['search_article']);
            $searchParameters['search_article']=preg_replace('/\(|\)/','',$searchParameters['search_article']);
            $searchParameters['search_article']=addslashes($searchParameters['search_article']);
            $words=explode(" ",$searchParameters['search_article']);
           if(count($words)>1)
           {
                $addQuery.=" and (a.title like '%".($searchParameters['search_article'])."%' or";
                foreach($words as $key=>$word)
                {
                   if($word!='')
                   {
                       $addQuery.=" a.title REGEXP '[[:<:]]".($word)."[[:>:]]'";
                       //$addQuery.=" a.title like '%".($word)."%'";
                       if($key!=(count($words))-1)
                            $addQuery.=" or";
                   }
                }
                $addQuery.=")";
            }
            else
                $addQuery.=" and a.title  REGEXP '[[:<:]]".$searchParameters['search_article']."[[:>:]]'";
                //$addQuery.=" and a.title like '%".($searchParameters['search_article'])."%'";
        }
        if($searchParameters['search_relate']!=NULL)
            $addQuery.=" and a.id not in ('".$searchParameters['search_relate']."')";
        if($searchParameters['search_related_ids']!=NULL)
            $addQuery.=" and a.id not in (".$searchParameters['search_related_ids'].")";
        if($searchParameters['userId']!=NULL)
        {
            if($searchParameters['userId']=='anonymous')
                $addQuery.=" and d.deli_anonymous='1'";
            else
                $addQuery.=" and d.user_id='".$searchParameters['userId']."' and d.deli_anonymous='0'";
        }
        
        if($searchParameters['search_category']!=NULL)
            $addQuery.=" and a.category in ('".$searchParameters['search_category']."')";
        
        if($searchParameters['search_language']!=NULL)
            $addQuery.=" and a.language='".$searchParameters['search_language']."'";    

        //show only premium articles if user is in black list
        if($searchParameters['search_ao_type']!=NULL OR $searchParameters['black_status']=='yes')
        {
            if($searchParameters['search_ao_type']=='m_premium' OR $searchParameters['black_status']=='yes')
                $addQuery.=" and d.premium_option!='0' and d.premium_option!=''";
            else if($searchParameters['search_ao_type']=='m_npremium')
                $addQuery.=" and (d.premium_option='0' OR d.premium_option='')";

        }
            


        if(!$searchParameters['req_from'])
        {
            $condition=" and a.id not in( select article_id from Participation pa where pa.user_id='".$userIdentifier."' or pa.status in ('published','bid','under_study','on_hold','disapproved'))";
            /*added w.r.t article should not show to the respective corrector**/
            $condition.=" and a.id not in( select article_id from  CorrectorParticipation cp where cp.corrector_id='".$userIdentifier."' and cp.status in ('published','bid','under_study','on_hold','disapproved'))";

            //Added w.r.t QuizParticipation
            $condition.=" and d.quiz not in(select quiz_id from  QuizParticipation qp where qp.qualified='no' and qp.user_id='".$userIdentifier."')";

            $expiresCondition=' AND (a.participation_expires=0 OR a.participation_expires > UNIX_TIMESTAMP())';
        }                
        

        //differed AO publish time condition
        if($searchParameters['upcoming'])
            $publish_condition=" AND (publishtime > UNIX_TIMESTAMP()) ";
        else
            $publish_condition=" AND (publishtime < UNIX_TIMESTAMP() OR publishtime=0 OR publishtime is NULL) ";

        if($searchParameters['cnt_nextday'])
        {
           // $today=getDateNextDays(0);
	   // $tomorrow=getDateNextDays(1);
			$ts = strtotime(date('Y-m-d 23:59:59'));
			$today = $ts;
			$tomorrow = $ts+86400;
		
            $publish_condition.=" AND publishtime BETWEEN '".$today."' AND '".$tomorrow."'";
        }    


        //test required condition 
        $condition.=" AND ((a.testrequired='yes'  AND a.testmarks<=$contributortestmarks AND find_in_set('".$contributorTest."', a.testrequired)>0)|| a.testrequired='no') "; 
        
        /**order By Condition**/
        
        if($searchParameters['orderByTitle'])
        {
            if($searchParameters['orderByTitle']=='desc')
                $orderQuery.="a.title DESC  ";
            else if($searchParameters['orderByTitle']=='asc')
                $orderQuery.="a.title ASC  ";   
        }
        else if($searchParameters['orderByLang'])
        {
            if($searchParameters['orderByLang']=='desc')
                $orderQuery.="a.language DESC  ";
            else if($searchParameters['orderByLang']=='asc')
                $orderQuery.="a.language ASC  ";    
        }
        else if($searchParameters['orderByAttendee'])
        {
            if($searchParameters['orderByAttendee']=='desc')
                $orderQuery.="totalParticipation DESC  ";
            else if($searchParameters['orderByAttendee']=='asc')
                $orderQuery.="totalParticipation ASC  ";    
        }
        else if($searchParameters['orderByQuotePrice'])
        {
            if($searchParameters['orderByQuotePrice']=='desc')
                $orderQuery.="price_min DESC  ";
            else if($searchParameters['orderByQuotePrice']=='asc')
                $orderQuery.="price_min ASC  "; 
        }
        else if($searchParameters['orderBytime'])
        {
            if($searchParameters['orderBytime']=='desc')
                $orderQuery.="remainingDays DESC  ";
            else if($searchParameters['orderBytime']=='asc')
                $orderQuery.="remainingDays ASC  "; 
        }
        //for upcoming articles
        else if($searchParameters['uorderByTitle'])
        {
            if($searchParameters['uorderByTitle']=='desc')
                $orderQuery.="a.title DESC  ";
            else if($searchParameters['uorderByTitle']=='asc')
                $orderQuery.="a.title ASC  ";   
        }
        else if($searchParameters['uorderByLang'])
        {
            if($searchParameters['uorderByLang']=='desc')
                $orderQuery.="a.language DESC  ";
            else if($searchParameters['uorderByLang']=='asc')
                $orderQuery.="a.language ASC  ";    
        }
        else if($searchParameters['uorderBytime'])
        {
            if($searchParameters['uorderBytime']=='desc')
                $orderQuery.="publishtime DESC  ";
            else if($searchParameters['uorderBytime']=='asc')
                $orderQuery.="publishtime ASC  "; 
        }



        
        if($orderQuery)
            $orderQuery=" ORDER BY ".$orderQuery;
        else
            $orderQuery=" ORDER BY a.participation_expires ASC,title ASC,deliveryid DESC,articleid DESC ";
        
        if($limit)
            $limitCondition=" limit 0,10";
        else
            $limitCondition="";
        $participationJoin=" LEFT JOIN Participation pa ON pa.article_id = a.id  ";
        
        /**Article Query With Dynamic percent **/
        $articleQuery="SELECT  count( pa.article_id ) as totalParticipation,c.company_name,d.AOtype,d.deli_anonymous,u.identifier,d.id as deliveryid,a.id as articleid,
                        a.title as title,a.language,a.category,a.type,a.created_at,a.updated_at,
                        a.sign_type,a.num_min,a.num_max,IF(a.contrib_price,a.contrib_price,(((a.price_min)*a.contrib_percentage)/100)) as price_min,
                        IF(a.contrib_price,a.contrib_price,(((a.price_max)*a.contrib_percentage)/100)) as price_max,email,
                        DATEDIFF(IF(a.participation_expires,a.participation_expires,d.submitdate_bo),NOW()) as remainingDays,a.participation_expires,a.subjunior_time,a.junior_time,a.senior_time,u.profile_type,
                        d.participation_time,a.jc_resubmission,a.sc_resubmission,d.link_quiz,d.quiz,d.quiz_duration,d.pricedisplay,
                        GROUP_CONCAT(a.category) as filter_category,GROUP_CONCAT(a.language) as filter_language,a.type ,
                        d.premium_total,d.filepath,d.premium_option,a.contribs_list,
                        d.publishtime,a.view_to,d.missiontest,a.currency,d.mission_volume,a.product,a.language_source,
                            cont.language AS mother_tounge, cont.language_more,
                            d.sourcelang_nocheck
                        
                 FROM Delivery d
                 INNER JOIN  User u ON u.identifier=d.user_id
                 LEFT JOIN  Client c  ON u.identifier=c.user_id
                 INNER JOIN Payment p ON p.delivery_id=d.id
                 INNER JOIN Article a ON a.delivery_id=d.id
                 LEFT JOIN Contributor cont ON cont.user_id = '".$userIdentifier."'
                 ".$participationJoin."
                 where p.status='Paid' and a.status!='validated' and d.status_bo='active' ".$addQuery.$condition."
                 and ".$privateQuery.$expiresCondition."
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
                 GROUP BY deliveryid,articleid
                 ".$haveQuery.
                $orderQuery.$limitCondition;

                
        //echo  $articleQuery;exit;
        
        if(($count=$this->getNbRows($articleQuery))>0)
        {
            $ArticleDetails=$this->getQuery($articleQuery,true);
            return $ArticleDetails;
        }
        //else
          //  return NULL;
    }
    /**Get Article count in each category***/
    public function getArticlesInCategory($profileType=NULL)
    {
        setlocale(LC_TIME, "fr_FR");
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
        if(!$userIdentifier)
        {
            $privateQuery="(d.AOtype='public')";
        }
        else
        {
            $privateUser=$userIdentifier;
            /*added to restrict Ao's for only for a group of profiles*/
            if($profileType=='senior')
                $view_to=" AND find_in_set('sc', d.view_to)>0";
            elseif($profileType=='junior')
                $view_to=" AND find_in_set('jc', d.view_to)>0";
            elseif($profileType=='sub-junior')
                $view_to=" AND find_in_set('jc0', d.view_to)>0";
            $privateQuery="((d.AOtype='public' $view_to AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
        }
        $addQuery='';
        
        $condition=" and a.id not in( select article_id from Participation pa where pa.user_id='".$userIdentifier."' or pa.status in ('published','bid','under_study','on_hold','disapproved'))";

         /*added w.r.t article should not show to the respective corrector**/
        $condition.=" and a.id not in( select article_id from  CorrectorParticipation cp where cp.corrector_id='".$userIdentifier."')";
        //Added w.r.t QuizParticipation
        $condition.=" and d.quiz not in(select quiz_id from  QuizParticipation qp where qp.qualified='no' and qp.user_id='".$userIdentifier."')";

        //differed AO publish condition
        if($searchParameters['upcoming'])
            $publish_condition=" AND (publishtime > UNIX_TIMESTAMP()) ";
        else
            $publish_condition=" AND (publishtime < UNIX_TIMESTAMP() OR publishtime=0 OR publishtime is NULL) ";


        $limitCondition="";
        $participationJoin=" LEFT JOIN Participation pa ON pa.article_id = a.id  ";
        /**Article Query With Dynamic percent **/
        $articleQuery="SELECT a.category,count(DISTINCT a.id) as num_articles
                 FROM Delivery d
                 INNER JOIN  User u ON u.identifier=d.user_id
                 LEFT JOIN  Client c  ON u.identifier=c.user_id
                 INNER JOIN Payment p ON p.delivery_id=d.id
                 INNER JOIN Article a ON a.delivery_id=d.id
                 ".$participationJoin."
                 where p.status='Paid' and a.status!='validated' and d.status_bo='active' ".$addQuery.$condition."
                 and ".$privateQuery."  AND (a.participation_expires=0 OR a.participation_expires > UNIX_TIMESTAMP())
                 $publish_condition
                 GROUP BY a.category";
        //echo  $articleQuery;exit;
        if(($count=$this->getNbRows($articleQuery))>0)
        {
            $ArticleDetails=$this->getQuery($articleQuery,true);
            return $ArticleDetails;
        }
        //else
          //  return NULL;
    }
    
    public function getArticleContract($articleIds)
    {
        $articleIds="'".str_replace(",","','",$articleIds)."'";
        $query="select a.title,a.id,d.title as deliveryTitle,a.currency
                from Article a
                INNER JOIN Delivery d ON d.id=a.delivery_id
                where a.id in (".$articleIds.")
                ";
        //echo $query;exit;
        if($articles=$this->getQuery($query,true))
        {
            return $articles;
        }
    }
    public function getArticleName($articleId)
    {
        $query="select title from Article where id='".$articleId."'";
        if($ArticleDetails=$this->getQuery($query,true))
            return $ArticleDetails[0]['title'];
    }
    /**Get Relative Articles of a article in search**/
    public function getAjaxRelateArticleDetails($articleId,$details=NULL)
    {
        if($details=='yes')
            $search['articleId']=$articleId;
        else
        {
            $articleName=$this->getArticleName($articleId);
            $articleName=preg_replace('/[0-9][?]/','',$articleName);
            $articleName=preg_replace('/\?/','',$articleName);
            $articleName=preg_replace('/\(|\)/','',$articleName);
            $search['search_article']=utf8_encode($articleName);
            $search['search_relate']=$articleId;
        }    
        //echo $search['search_article'];exit;
        return $this->getArticleDetails($search,10);
        //echo utf8_encode($articleName);
    }
    /**Get Relative Articles of a article in cartpart1**/
    public function getRelatedArticleDetails($articleIds)
    {
        //ECHO $articleIds;
        //$articleIds="'".str_replace(",","','",$articleIds)."'";
        $query="select group_concat( DISTINCT title separator ' ') as search_article
                from Article
                where id in (".$articleIds.")
                ";
        
        if($search_string=$this->getQuery($query,true))
        {
            $string=$search_string[0]['search_article'];
            $string=preg_replace('/[0-9]/','',$string);
            $string=preg_replace('/\?/','',$string);
            $string=preg_replace('/\(|\)/','',$string);
            
            $words=array_unique(explode(" ",$string));
            $articleString=implode(" ",$words);
            
            
            $search['search_article']=utf8_encode($articleString);
            $search['search_related_ids']=$articleIds;
            //echo $search['search_article'];exit;
            return $this->getArticleDetails($search,10);
            //echo utf8_encode($articleName);
        }
    }
    public function getMultipleArticleNames($articleIds,$separator=NULL)
    {
        if(!$separator)
            $separator="<br>";
        $articleIds="'".str_replace(",","','",$articleIds)."'";
        $query="select group_concat( DISTINCT title separator '".$separator."') as premiumArticles
                from Article
                where id in (".$articleIds.")
                ";
        //echo $query;exit;
        if($article=$this->getQuery($query,true))
        {
            return $article[0]['premiumArticles'];
        }
    }
    /**Get payment status details of a article**/
    public function checkPaymentStatus($articleId)
    {
        $query="select paid_status from Article where id='".$articleId."'";
        if($ArticleDetails=$this->getQuery($query,true))
            return $ArticleDetails[0]['paid_status'];
    }
    ////////////udate the articles table//////////////////////
    public function updateArticle($data,$query)
    {
         $data['updated_at']=date("Y-m-d H:i:s",time());
		 $this->updateQuery($data,$query);
    }
    
    public function updateParticipationTrigger($artId)
    {
         $query1="select IF (a.contrib_percentage,( (p1.price_user/a.contrib_percentage)*100),a.price_final) as p_final
                    from Participation p1
                  INNER JOIN Article a ON a.id = p1.article_id
                  where p1.article_id = '".$artId."' and p1.status='published' and p1.current_stage='client'";
        $price_final=$this->getQuery($query1,true);
        //Update
        if(count($price_final)>0)
        {
            $Parray=array();
            $Parray['price_final']=$price_final[0]['p_final'];
			$Parray['updated_at']=date("Y-m-d H:i:s");
            $wherep="id = '".$artId."'";
            $this->updateQuery($Parray,$wherep);
        }
    }
    //function to check article is Correct AO or not
    public function checkCorrectorAO($articleId)
    {
         $query="select a.correction from ".$this->_name." a INNER JOIN Delivery d ON d.id=a.delivery_id
                 where a.id='".$articleId."'";
            //echo $query;
        if(($count=$this->getNbRows($query))>0)
        {
            $correctorAO=$this->getQuery($query,true);
            if($correctorAO[0]['correction']=='yes' )
                return "YES";
        }
        else
            return "NO";
    }
    //function to get corrector  article details
    public function getCorrectorArticleDetails($articleId)
    {
        $query="select a.id as articleid,d.correction_pricemin,d.correction_pricemax,
                d.correction_jc_submission, d.correction_sc_submission, d.correction_jc_resubmission, d.correction_sc_resubmission,
                a.title from ".$this->_name." a INNER JOIN Delivery d ON d.id=a.delivery_id
                 where a.id='".$articleId."' and correction='yes'";
        //echo $query;
        if(($count=$this->getNbRows($query))>0)
        {
            $correctorArticle=$this->getQuery($query,true);
            return $correctorArticle;
        }
        else
            return "NO";
    }
    /**get all corrector AO(having participation expires conditions)**/
    public function getCorrectorAOs($searchParameters=NULL)
    {
        setlocale(LC_TIME, "fr_FR");
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
        $user_language=$this->EP_Contrib_reg->user_language;
        $addQuery='';
        if($searchParameters['profile_type']=='senior')
        {
            $addQuery.=" AND (FIND_IN_SET('WSC',a.corrector_list) > 0 OR FIND_IN_SET('WB',a.corrector_list) > 0";
            if($searchParameters['corrector']=='corrector')
            {
                if($searchParameters['corrector_type']=='senior')
                    $addQuery.="  OR FIND_IN_SET('CSC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
               else
                    $addQuery.="  OR FIND_IN_SET('CJC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
            }
            $addQuery.=")";
        }
        else if($searchParameters['profile_type']=='junior')
        {

            $addQuery.=" AND (FIND_IN_SET('WJC',a.corrector_list) > 0 OR FIND_IN_SET('WB',a.corrector_list) > 0";
            if($searchParameters['corrector']=='corrector')
            {
                if($searchParameters['corrector_type']=='senior')
                    $addQuery.="  OR FIND_IN_SET('CSC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
                else
                    $addQuery.="  OR FIND_IN_SET('CJC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
            }
            $addQuery.=")";
        }
        else
		{
             $addQuery.=" AND (FIND_IN_SET('WJC0',a.corrector_list) > 0";
			 
			 if($searchParameters['corrector']=='corrector')
            {
                if($searchParameters['corrector_type']=='senior')
                    $addQuery.="  OR FIND_IN_SET('CSC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
                else
                    $addQuery.="  OR FIND_IN_SET('CJC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
            }
            $addQuery.=")";
			
		}
        $publish_users=" AND find_in_set('".$user_language."', a.language)>0";
		
		/** Private correction**/
		
        $privateQuery=" AND ((d.correction_type='public' $publish_users $addQuery) OR (d.correction_type='private' and find_in_set($userIdentifier, a.corrector_privatelist )>0))";
		
        if($searchParameters['articleId']!=NULL)
            $privateQuery.=" and a.id='".$searchParameters['articleId']."'";        

       if(!$searchParameters['req_from'])
        {
            $condition=" and a.id not in(select article_id from Participation pa where pa.user_id='".$userIdentifier."' and pa.status in ('published','bid','under_study','on_hold','disapproved','plag_exec'))";
            //$condition.=" and a.id not in(select article_id from CorrectorParticipation ca where ca.corrector_id='".$userIdentifier."' or ca.status in ('published','bid','under_study','on_hold','disapproved'))";

            //added mission test condition w.r.t AO Correction

            $condition.=" and a.id not in(select article_id from CorrectorParticipation ca where ca.corrector_id='".$userIdentifier."' or ( ca.status in ('published','bid','under_study','on_hold','disapproved') AND d.missiontest='no'))";

            $expiresCondition=' AND (a.correction_participationexpires > UNIX_TIMESTAMP())';
        }  
        if($searchParameters['article_ids']!=NULL)
            $condition.=" and a.id in (".$searchParameters['article_ids'].")";

		if($searchParameters['upcoming'])
            $publish_condition=" AND (publishtime > UNIX_TIMESTAMP()) ";
        else
            $publish_condition=" AND (publishtime < UNIX_TIMESTAMP() OR publishtime=0 OR publishtime is NULL) ";

        $correctorAoQuery="SELECT count(DISTINCT ca.id ) as totalParticipation,a.title,a.id as articleid,d.correction_enddate as delivery_date,d.id as deliveryid,
                            a.category,a.type,a.language,d.min_sign,d.max_sign,DATEDIFF(d.correction_enddate,NOW()) as remainingDays,
                            d.deli_anonymous,a.correction_pricemin,a.correction_pricemax,a.correction_jc_submission,
                            a.correction_sc_submission,a.correction_jc_resubmission, a.correction_sc_resubmission,
                            a.correction_participation,a.num_min,a.num_max,a.correction_participationexpires,d.user_id as clientId,
                            p.user_id as writer,d.correction_type,a.corrector_privatelist,d.missiontest,d.publishtime,a.currency,
                                a.product,a.language_source,
                                cont.language AS mother_tounge, cont.language_more,
                                d.sourcelang_nocheck_correction
                            FROM Delivery d
                            INNER JOIN Article a ON d.id=a.delivery_id
                            LEFT JOIN Participation p ON a.id=p.article_id and p.status='under_study' and p.current_stage='corrector'
                            LEFT JOIN CorrectorParticipation ca ON ca.article_id = a.id
                                LEFT JOIN Contributor cont ON cont.user_id = '".$userIdentifier."'
                            where a.correction='yes'
                             ".$privateQuery.$condition. $expiresCondition ."
							 $publish_condition
							 AND (
                                    (a.product = 'redaction')
                                    OR
                                    (
                                        a.product = 'translation'
                                        AND cont.translator = 'yes'
                                        AND cont.language = a.language
                                    )
                                )
                             GROUP BY a.id  ORDER BY a.correction_participationexpires ASC,title ASC,articleid DESC";
        //echo $correctorAoQuery;exit;
        if(($count=$this->getNbRows($correctorAoQuery))>0)
        {
            $correctorDetails=$this->getQuery($correctorAoQuery,true);
            return $correctorDetails;
        }
    }
    /**get all corrector AO(show untill the delivery ends)**/
    public function getAllCorrectorAOs($searchParameters=NULL)
    {
        setlocale(LC_TIME, "fr_FR");
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
        $addQuery='';
        if($searchParameters['profile_type']=='senior')
        {
            $addQuery.=" AND (FIND_IN_SET('WSC',a.corrector_list) > 0 OR FIND_IN_SET('WB',a.corrector_list) > 0";
            if($searchParameters['corrector']=='corrector')
            {
               if($searchParameters['corrector_type']=='senior')
                   $addQuery.="  OR FIND_IN_SET('CSC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
               else
                   $addQuery.="  OR FIND_IN_SET('CJC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
            }
            $addQuery.=")";
        }
        else if($searchParameters['profile_type']=='junior')
        {
            $addQuery.=" AND (FIND_IN_SET('WJC',a.corrector_list) > 0 OR FIND_IN_SET('WB',a.corrector_list) > 0";
            if($searchParameters['corrector']=='corrector')
            {
                if($searchParameters['corrector_type']=='senior')
                    $addQuery.="  OR FIND_IN_SET('CSC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
                else
                    $addQuery.="  OR FIND_IN_SET('CJC',a.corrector_list) > 0 OR FIND_IN_SET('CB',a.corrector_list) > 0";
            }
            $addQuery.=")";
        }
        $condition=" and a.id not in(select article_id from Participation pa where pa.user_id='".$userIdentifier."' and pa.status in ('published','bid','under_study','on_hold','disapproved','plag_exec'))";
        $correctorAoQuery="SELECT count(DISTINCT ca.id ) as totalParticipation,a.title,a.id as articleid,d.correction_enddate as delivery_date,
                            a.category,a.type,a.language,d.min_sign,d.max_sign,DATEDIFF(d.correction_enddate,NOW()) as remainingDays,
                            d.deli_anonymous,d.correction_pricemin,d.correction_pricemax,d.correction_jc_submission, d.correction_sc_submission,
                            d.correction_jc_resubmission, d.correction_sc_resubmission,
                            d.correction_participation,a.num_min,a.num_max,a.correction_participationexpires,a.currency
                            FROM Delivery d
                            INNER JOIN Article a ON d.id=a.delivery_id
                            INNER JOIN Participation p ON a.id=p.article_id
                            LEFT JOIN CorrectorParticipation ca ON ca.article_id = a.id
                            where a.correction='yes' and p.status='under_study' and p.current_stage='corrector'
                             ".$addQuery.$condition." 
                             GROUP BY a.id
                             ORDER BY a.correction_participationexpires ASC, d.correction_enddate ASC,title ASC,articleid DESC";
        //echo $correctorAoQuery;
        if(($count=$this->getNbRows($correctorAoQuery))>0)
        {
            $correctorDetails=$this->getQuery($correctorAoQuery,true);
            return $correctorDetails;
        }
    }

    //cron function to get all article with script tag and URl
    public function tagScriptArticle()
    {
        $tagArticlesQuery="SELECT a.client_site_url,a.tag_script From
                            ".$this->_name." a 
                        INNER JOIN Delivery d ON d.id=a.delivery_id                
                        WHERE a.client_site_url IS NOT NULL and a.tag_script IS NOT NULL";

        //echo $tagArticlesQuery;exit;

        if(($count=$this->getNbRows($tagArticlesQuery))>0)
        {
            $tagArticles=$this->getQuery($tagArticlesQuery,true);
            return $tagArticles;
        }
        else
            return "NO";                    
     
    }
    public function getArticleAOdetails($aid)
    {
        $AOArticleQuery="SELECT d.user_id,d.created_user,a.delivery_id,a.title, a.language, a.refusalreasons, a.correction, a.product From
                            Delivery d INNER JOIN Article a ON a.delivery_id=d.id
                            WHERE a.id='".$aid."'";

           $AOArticles=$this->getQuery($AOArticleQuery,true);
           return $AOArticles;
    }
    public function getClientArticleAOdetails($artid)
    {
        $AOArticleQuery="SELECT d.user_id,a.delivery_id,a.title, a.language, a.refusalreasons, a.correction, a.product From
                            Delivery d INNER JOIN Article a on a.delivery_id = d.id
                            WHERE a.id='".$artid."'";
        $AOArticles=$this->getQuery($AOArticleQuery,true);
        return $AOArticles;
    }
    /**
     * createIdentifier
     * this method is used to create an identifier value for the SENDDOC
     */
    public function createIdentifier()
    {
        $s = new String();
        $d = new date();
        $this->setIdentifier($s->randomString(15));
    }

     public function getDupCronArticleList(){
		$Query="SELECT cr_conf_value
						 FROM cron_reference
                         WHERE cr_conf_name='dup_select_art_list'";
           $Articles=$this->getQuery($Query,true);
           $art=array();
           if(!empty($Articles)){
			$art=($Articles[0]['cr_conf_value']!='')?explode(',',$Articles[0]['cr_conf_value']):array();
			}
           return $art;
	}

	public function getCorDupCronArticleList(){
		$Query="SELECT cr_conf_value
						 FROM cron_reference
                         WHERE cr_conf_name='cor_dup_select_art_list'";
           $Articles=$this->getQuery($Query,true);
           $art=array();
           if(!empty($Articles)){
			$art=($Articles[0]['cr_conf_value']!='')?explode(',',$Articles[0]['cr_conf_value']):array();
			}
           return $art;
	}

	public function storeDupCronArt($arr){
		$this->_name = 'cron_reference';
		$art='';
		foreach($arr as $key=>$value){
			$art.=$value.",";
		}
		$art=rtrim($art,',');
		$data['cr_conf_value']=$art;
        $wherep="cr_conf_name='dup_select_art_list'";
        $this->updateQuery($data,$wherep);
		
	}

	public function storeCorDupCronArt($arr){
		$this->_name = 'cron_reference';
		$art='';
		foreach($arr as $key=>$value){
			$art.=$value.",";
		}
		$art=rtrim($art,',');
		$data['cr_conf_value']=$art;
        $wherep="cr_conf_name='cor_dup_select_art_list'";
        $this->updateQuery($data,$wherep);
		
	}
	
	public function getArticleTitles($idslist)
	{
		$ArticlesQuery="SELECT group_concat( title separator ', ') as title FROM Article WHERE id IN ( ".$idslist.")";
        $Articles=$this->getQuery($ArticlesQuery,true);
        return $Articles[0]['title'];
		
	}
    //test duplication on stenciles related to same article//
    public function testDuplication(){
        $query = "SELECT `language` , `translation_id`
        FROM `ValidStencils`
        WHERE `translation_id` IS NOT NULL
        AND TRIM(`translation_id`) <> ''
        ORDER BY `translation_id` DESC";
        if($Result=$this->getQuery($query,true))
            return $Result;
        else
            return NULL;
    }
    //test duplication on stenciles related to same article from article table//
    public function testTranslationDuplicationArticle(){
        $query = "SELECT `delivery_id` , `language` , `title` , `id` , `stencils_translate`
FROM `Article`
WHERE `stencils_translate` IS NOT NULL
AND TRIM( `stencils_translate` ) <> ''
ORDER BY `stencils_translate` DESC";
        if($Result=$this->getQuery($query,true))
            return $Result;
        else
            return NULL;
    }
    /* ***added on 17.02.2016 *** */
    /////////get aritcle and participation deatails w.r.t participation id  ///////////////////////////
    public function getArticleDetailsCron($artId)
    {
        $query = "select a.id, a.title, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords, a.sign_type, a.num_min, a.num_max, a.price_min, a.price_max, a.correction_pricemin, a.correction_pricemax, a.corrector_privatelist, a.contribs_list, a.progressbar_percent, a.participation_expires,
                    a.correction_closed_status, a.correction,a.client_validated,a.validated_by,a.refusalreasons, a.product, d.id AS deliveryId, d.title AS deliveryTitle, d.user_id AS clientId, d.filepath, d.created_user, d.missiontest,u.email, up.first_name, up.last_name, a.correction_jc_resubmission,a.correction_sc_resubmission,a.correction_participationexpires,
                    a.language_source,
                    a.sourcelang_nocheck,a.sourcelang_nocheck_correction,a.delivered,UNIX_TIMESTAMP(a.delivered_updated_at) as delivered_updated_at,
                    a.delivered,d.type,a.delivered_updated_at,a.delivered_updated_by,d.contract_mission_id,a.currency


                    from ".$this->_name." a
		           INNER JOIN Delivery d ON a.delivery_id=d.id
                   LEFT JOIN User u ON u.identifier=d.created_user
                   LEFT JOIN UserPlus up ON up.user_id = u.identifier WHERE a.id=".$artId;//." where ".$whereQuery;
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    public function getArticlePost15DaysDelivered(){
        $query = "SELECT A. * , P.`article_id` , P.`status` , P.`current_stage` , CONCAT( A.`id` , '_', P.`id` ) AS art_id_part_id
            FROM `Article` AS A
            LEFT JOIN `Participation` AS P ON A.`id` = P.`article_id`
            WHERE (
                P.`status` != 'published'
                AND P.`current_stage` != 'client'
                AND P.`current_stage` IN('stage2','stage1')
            )
            AND (
                A.`delivered` = 'yes'
                AND A.`delivered_updated_at` < DATE_SUB( CURDATE( ) , INTERVAL 15 DAY )
            )
            ";//." where ".$whereQuery;
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
}

