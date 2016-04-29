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
        if(!$userIdentifier)
        {
            $privateQuery="(d.AOtype='public')";
        }
        else
        {
            $privateUser=$userIdentifier;
            /*added to restrict Ao's for only for a group of profiles*/
             if($searchParameters['profile_type']=='senior')
               $view_to=" AND find_in_set('sc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='junior')
                $view_to=" AND find_in_set('jc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='sub-junior')
                $view_to=" AND find_in_set('jc0', d.view_to)>0";
            $privateQuery="((d.AOtype='public' $view_to AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
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

        if($searchParameters['search_ao_type']!=NULL)
        {
            if($searchParameters['search_ao_type']=='m_premium')
                $addQuery.=" and d.premium_option!='0' and d.premium_option!=''";
            else if($searchParameters['search_ao_type']=='m_npremium')
                $addQuery.=" and (d.premium_option='0' OR d.premium_option='')";

        }
            


        if(!$searchParameters['req_from'])
        {
            $condition=" and a.id not in( select article_id from Participation pa where pa.user_id='".$userIdentifier."' or pa.status in ('published','bid','under_study','on_hold','disapproved'))";
            /*added w.r.t article should not show to the respective corrector**/
            $condition.=" and a.id not in( select article_id from  CorrectorParticipation cp where cp.corrector_id='".$userIdentifier."')";

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
                        DATEDIFF(IF(a.participation_expires,a.participation_expires,d.submitdate_bo),NOW()) as remainingDays,a.participation_expires,d.subjunior_time,d.junior_time,d.senior_time,u.profile_type,
                        d.participation_time,d.jc_resubmission,d.sc_resubmission,d.link_quiz,d.quiz,d.quiz_duration,
                        GROUP_CONCAT(a.category) as filter_category,GROUP_CONCAT(a.language) as filter_language,a.type ,
                        d.premium_total,d.filepath,d.premium_option,a.contribs_list,
                        d.publishtime,d.view_to
                        
                 FROM Delivery d
                 INNER JOIN  User u ON u.identifier=d.user_id
                 LEFT JOIN  Client c  ON u.identifier=c.user_id
                 INNER JOIN Payment p ON p.delivery_id=d.id
                 INNER JOIN Article a ON a.delivery_id=d.id
                 ".$participationJoin."
                 where p.status='Paid' and a.status!='validated' and d.status_bo='active' ".$addQuery.$condition."
                 and ".$privateQuery.$expiresCondition."
                 $publish_condition
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
    /**Added for showing all articles that were partcipated too**/
    public function getArticleSearchDetails($searchParameters,$limit=NULL)
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
            //$privateQuery="((d.AOtype='public') OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
            /*added to restrict Ao's for only for a group of profiles*/
             if($searchParameters['profile_type']=='senior')
               $view_to=" AND find_in_set('sc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='junior')
                $view_to=" AND find_in_set('jc', d.view_to)>0";
            elseif($searchParameters['profile_type']=='sub-junior')
                $view_to=" AND find_in_set('jc0', d.view_to)>0";
            $privateQuery="((d.AOtype='public' $view_to AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
        }
        $addQuery='';
        if($searchParameters['articleId']!=NULL)
            $addQuery.=" and a.id='".$searchParameters['articleId']."'";
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
               //$addQuery.=" and (((((a.price_min)*42.5)/100) BETWEEN ".$price[0]." and ".$price[1].") OR ((((a.price_max)*42.5)/100) BETWEEN ".$price[0]." and ".$price[1]."))" ;
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
       // $condition=" and a.id not in( select article_id from Participation pa where pa.user_id='".$userIdentifier."' or pa.status in ('published','bid','under_study','on_hold'))";
       //  $condition=" and a.id not in( select article_id from Participation pa where pa.user_id='".$userIdentifier."' or pa.status in ('published'))";
        /*added w.r.t article should not show to the respective corrector**/
       // $condition.=" and a.id not in( select article_id from  CorrectorParticipation cp where cp.corrector_id='".$userIdentifier."')";
        if($limit)
            $limitCondition=" limit 0,10";
        else
            $limitCondition="";
        $participationJoin=" LEFT JOIN Participation pa ON pa.article_id = a.id  ";
        /**Article Query With Dynamic percent **/
        $articleQuery="SELECT count( pa.article_id ) as totalParticipation,c.company_name,d.AOtype,d.deli_anonymous,u.identifier,d.id as deliveryid,a.id as articleid,
                        a.title as title,a.language,a.category,a.type,a.created_at,a.updated_at,
                        a.sign_type,a.num_min,a.num_max,IF(a.contrib_price,a.contrib_price,(((a.price_min)*a.contrib_percentage)/100)) as price_min,
                        IF(a.contrib_price,a.contrib_price,(((a.price_max)*a.contrib_percentage)/100)) as price_max,email,
                        DATEDIFF(d.submitdate_bo,NOW()) as remainingDays,a.participation_expires,d.junior_time,d.senior_time,u.profile_type,
                        d.participation_time,d.jc_resubmission,d.sc_resubmission,
                         GROUP_CONCAT(a.category) as filter_category,GROUP_CONCAT(a.language) as filter_language,a.type ,
                        d.premium_total,d.filepath,d.premium_option,a.contribs_list
                 FROM Delivery d
                 INNER JOIN  User u ON u.identifier=d.user_id
                 LEFT JOIN  Client c  ON u.identifier=c.user_id
                 INNER JOIN Payment p ON p.delivery_id=d.id
                 INNER JOIN Article a ON a.delivery_id=d.id
                 ".$participationJoin."
                 where p.status='Paid' and a.status!='validated' and d.status_bo='active' ".$addQuery.$condition."
                 and ".$privateQuery."  
                 GROUP BY deliveryid,articleid
                 ".$haveQuery."
                ORDER BY a.participation_expires ASC,title ASC,deliveryid DESC,articleid DESC ".$limitCondition;
        // AND (a.participation_expires=0 OR a.participation_expires > UNIX_TIMESTAMP())
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
        $query="select a.title,a.id,d.title as deliveryTitle
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
        $addQuery='';
        if($searchParameters['profile_type']=='senior')
        {
            $addQuery.=" AND (FIND_IN_SET('WSC',d.corrector_list) > 0 OR FIND_IN_SET('WB',d.corrector_list) > 0";
            if($searchParameters['corrector']=='corrector')
            {
                if($searchParameters['corrector_type']=='senior')
                    $addQuery.="  OR FIND_IN_SET('CSC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
               else
                    $addQuery.="  OR FIND_IN_SET('CJC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
            }
            $addQuery.=")";
        }
        else if($searchParameters['profile_type']=='junior')
        {

            $addQuery.=" AND (FIND_IN_SET('WJC',d.corrector_list) > 0 OR FIND_IN_SET('WB',d.corrector_list) > 0";
            if($searchParameters['corrector']=='corrector')
            {
                if($searchParameters['corrector_type']=='senior')
                    $addQuery.="  OR FIND_IN_SET('CSC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
                else
                    $addQuery.="  OR FIND_IN_SET('CJC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
            }
            $addQuery.=")";
        }
        else
		{
             $addQuery.=" AND (FIND_IN_SET('WJC0',d.corrector_list) > 0";
			 
			 if($searchParameters['corrector']=='corrector')
            {
                if($searchParameters['corrector_type']=='senior')
                    $addQuery.="  OR FIND_IN_SET('CSC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
                else
                    $addQuery.="  OR FIND_IN_SET('CJC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
            }
            $addQuery.=")";
			
		}
		
		/** Private correction**/
		
		$privateQuery=" AND ((d.correction_type='public' $addQuery) OR (d.correction_type='private' and find_in_set($userIdentifier, a.corrector_privatelist )>0))";
		
        if($searchParameters['articleId']!=NULL)
            $privateQuery.=" and a.id='".$searchParameters['articleId']."'";        

       if(!$searchParameters['req_from'])
        {
            $condition=" and a.id not in(select article_id from Participation pa where pa.user_id='".$userIdentifier."' and pa.status in ('published','bid','under_study','on_hold','disapproved'))";
            $condition.=" and a.id not in(select article_id from CorrectorParticipation ca where ca.corrector_id='".$userIdentifier."' or ca.status in ('published','bid','under_study','on_hold','disapproved'))";

            $expiresCondition=' AND (a.correction_participationexpires=0 OR a.correction_participationexpires > UNIX_TIMESTAMP())';
        }  
        if($searchParameters['article_ids']!=NULL)
            $condition.=" and a.id in (".$searchParameters['article_ids'].")";

       

        $correctorAoQuery="SELECT count(DISTINCT ca.id ) as totalParticipation,a.title,a.id as articleid,d.correction_enddate as delivery_date,
                            a.category,a.type,a.language,d.min_sign,d.max_sign,DATEDIFF(d.correction_enddate,NOW()) as remainingDays,
                            d.deli_anonymous,d.correction_pricemin,d.correction_pricemax,d.correction_jc_submission,
                            d.correction_sc_submission,d.correction_jc_resubmission, d.correction_sc_resubmission,
                            d.correction_participation,a.num_min,a.num_max,a.correction_participationexpires,d.user_id as clientId,
                            p.user_id as writer,d.correction_type,a.corrector_privatelist
                            FROM Delivery d
                            INNER JOIN Article a ON d.id=a.delivery_id
                            INNER JOIN Participation p ON a.id=p.article_id
                            LEFT JOIN CorrectorParticipation ca ON ca.article_id = a.id
                            where a.correction='yes' and p.status='under_study' and p.current_stage='corrector'
                             ".$privateQuery.$condition. $expiresCondition ."
                             GROUP BY a.id  ORDER BY a.correction_participationexpires ASC,title ASC,articleid DESC";
        //echo $correctorAoQuery;//exit;
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
            $addQuery.=" AND (FIND_IN_SET('WSC',d.corrector_list) > 0 OR FIND_IN_SET('WB',d.corrector_list) > 0";
            if($searchParameters['corrector']=='corrector')
            {
               if($searchParameters['corrector_type']=='senior')
                   $addQuery.="  OR FIND_IN_SET('CSC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
               else
                   $addQuery.="  OR FIND_IN_SET('CJC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
            }
            $addQuery.=")";
        }
        else if($searchParameters['profile_type']=='junior')
        {
            $addQuery.=" AND (FIND_IN_SET('WJC',d.corrector_list) > 0 OR FIND_IN_SET('WB',d.corrector_list) > 0";
            if($searchParameters['corrector']=='corrector')
            {
                if($searchParameters['corrector_type']=='senior')
                    $addQuery.="  OR FIND_IN_SET('CSC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
                else
                    $addQuery.="  OR FIND_IN_SET('CJC',d.corrector_list) > 0 OR FIND_IN_SET('CB',d.corrector_list) > 0";
            }
            $addQuery.=")";
        }
        $condition=" and a.id not in(select article_id from Participation pa where pa.user_id='".$userIdentifier."' and pa.status in ('published','bid','under_study','on_hold','disapproved'))";
        $correctorAoQuery="SELECT count(DISTINCT ca.id ) as totalParticipation,a.title,a.id as articleid,d.correction_enddate as delivery_date,
                            a.category,a.type,a.language,d.min_sign,d.max_sign,DATEDIFF(d.correction_enddate,NOW()) as remainingDays,
                            d.deli_anonymous,d.correction_pricemin,d.correction_pricemax,d.correction_jc_submission, d.correction_sc_submission,
                            d.correction_jc_resubmission, d.correction_sc_resubmission,
                            d.correction_participation,a.num_min,a.num_max,a.correction_participationexpires
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
}
