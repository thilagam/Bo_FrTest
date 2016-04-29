<?php
/**
 * Ep_Participation_CorrectorParticipation
 * @author Arun
 * @package Participation
 * @version 1.0
 */
class Ep_Participation_CorrectorParticipation extends Ep_Db_Identifier
{
    protected $_name = 'CorrectorParticipation';
    private $id;
    private $article_id;
    private $corrector_id;
    private $participate_id;
    private $price_corrector;
    private $status;
    private $created_at;
    private $updated_at;
    private $accept_specifications;
    private $cidentifier;
	private $ipaddress;

    public function loadData($array)
    {
        $this->article_id=$array["article_id"];
        $this->corrector_id=$array["corrector_id"];
        $this->participate_id=$array["participate_id"];
        $this->price_corrector=$array["price_corrector"];
        $this->status=$array["status"];
        $this->created_at=$array["created_at"];
        $this->updated_at=$array["updated_at"];
        $this->accept_specifications=$array["accept_specifications"];
		$this->ipaddress=$array["ipaddress"];
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["id"] = $this->getIdentifier();
        $array["article_id"] = $this->article_id;
        $array["corrector_id"] = $this->corrector_id;
        $array["participate_id"] = $this->participate_id;
        $array["price_corrector"] = $this->price_corrector;
        $array["status"] = $this->status;
        $array["created_at"] = $this->created_at;
        $array["accept_specifications"]=$this->accept_specifications;
        $array["watchlist_id"]=$this->watchlist_id;   
		$array["ipaddress"]=$this->ipaddress;	
        return $array;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }
    public function __get($name){
        return $this->$name;
    }
    public function getParticipantCount($articleId)
    {
        $query="select count(*) as totalParticipants from ".$this->_name." where article_id='".$articleId."' and cycle=0";// and status not in ('bid_premium','bid_refused')";
        $participantsCount=$this->getQuery($query,true);
        return $participantsCount[0]['totalParticipants'];
    }
    /**setting time to expire to participate in FO**/
    public function updateCorrectorArticleParticipationExpires($articleId,$participation_expire=60)
    {
        if(!$participation_expire)
            $participation_expire=60;
        $this->_name='Article';
        $expires=time()+(60*$participation_expire);
        $data = array("correction_participationexpires"=>$expires,"updated_at"=>date("Y-m-d H:i:s"));////////updating
        $query="id='".$articleId."' and correction_participationexpires=0";
        $this->updateQuery($data,$query);
    }
    /////////get all corrector paritcipated users ids in article who involved in bidding///////////////////////////
    public function  getGroupParticipants($artId)
    {
        //$whereQuery = " AND status NOT IN ('bid_premium','bid_refused')";
        $query = "select corrector_id,id  FROM ".$this->_name." WHERE cycle=0 and article_id=".$artId.$whereQuery;
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    public function ongoingArticles($identifier,$from=NULL,$article_identifier=NULL)
    {
        if($from=='popup')
        {
            
            //$statusQuery="p.status in ('bid','under_study','disapproved') and cp.status='under_study'";
              $statusQuery="p.status in ('bid','under_study','disapproved')";
        }
        else
            $statusQuery=" (p.status in ('bid','under_study','bid_corrector','validated','disapproved','time_out','bid_temp','bid_refused_temp'))";
            //$statusQuery=" (p.status in ('bid','under_study','bid_corrector','validated','disapproved','time_out') and cp.status in ('under_study','closed_temp','disapproved_temp','disapproved','plag_exec','closed'))";
        if($article_identifier!=NULL)
            $statusQuery.=" And p.article_id='".$article_identifier."'";
        $ongoingCorrectorArticlesQuery="SELECT p.id as participationId,a.title,d.user_id as clientId,p.price_corrector,
                                a.id as article_id,a.correction_participationexpires,d.title as deliveryName,
                              p.status,p.corrector_submit_expires,ap.article_sent_at,cp.user_id as writer,
                               a.language,a.category,a.num_min,a.num_max,d.premium_total,d.filepath,d.premium_option,d.correction_type,a.corrector_privatelist,
                              cp.status as writer_status,d.missiontest,cp.current_stage as writer_stage,p.participate_id,
							  a.files_pack,d.stencils_ebooker,a.ebooker_sampletxt_id,a.ebooker_tokenids,a.currency,a.product,
							  (
							    SELECT `theme_name`
                                FROM `EB_themes` EB_t
                                LEFT JOIN EB_category EB_c ON EB_t.`theme_id` = EB_c.`themes_id`
                                WHERE EB_c.`cat_id` = a.ebooker_cat_id
                              ) AS category_name,
                              a.bnp_city_id,a.bnp_sampletext_id,a.bnp_no_of_articles,
                              (SELECT CM.`bnp_mission` FROM `ContractMissions` AS CM WHERE CM.`contractmissionid`=d.`contract_mission_id`) AS bnp_mission
                             FROM ".$this->_name." p
                             INNER JOIN Article a ON a.id=p.article_id
                             INNER JOIN Delivery d ON a.delivery_id=d.id
                             LEFT JOIN Participation cp ON cp.id=p.participate_id and cp.status in ('under_study','closed_temp','disapproved_temp','disapproved','plag_exec','closed','bid_temp','bid_refused_temp')
                             LEFT JOIN ArticleProcess ap ON  p.corrector_id=ap.user_id
                             where ".$statusQuery." and p.corrector_id='".$identifier."' 
                             AND ((p.corrector_submit_expires=0 OR p.corrector_submit_expires > UNIX_TIMESTAMP())OR(p.corrector_submit_expires <= UNIX_TIMESTAMP() AND p.status in ('bid','under_study','time_out','disapproved') ))                                                            
                             GROUP BY p.id
                             ORDER BY p.status,p.created_at DESC";
        //echo $ongoingCorrectorArticlesQuery;exit;
        if(($count=$this->getNbRows($ongoingCorrectorArticlesQuery))>0)
        {
            $ongoingArticles=$this->getQuery($ongoingCorrectorArticlesQuery,true);
            return $ongoingArticles;
        }
    }
    public function refusedArticles($identifier,$status=NULL,$article_identifier=NULL)
    {
        if($status=='time_out')
        {
            $statusQuery="p.status in ('bid','time_out','on_hold','refused','disapproved')";
            $refusedArticleQuery="SELECT p.id as participationId,a.title,d.user_id as clientId,p.price_corrector,a.id as article_id,IF(p.updated_at,p.updated_at,p.created_at) as delivery_date,
                              p.status,ap.article_sent_at,a.currency
                             FROM ".$this->_name." p
                             INNER JOIN Article a ON a.id=p.article_id
                             INNER JOIN Delivery d ON a.delivery_id=d.id
                             LEFT JOIN ArticleProcess ap ON p.corrector_id=ap.user_id
                             where ".$statusQuery." and p.corrector_id='".$identifier."' AND ((d.submitdate_bo < CURDATE())OR (p.corrector_submit_expires < UNIX_TIMESTAMP() and p.status in ('bid','under_study','disapproved','time_out')) )
                             GROUP BY p.id
                             ORDER BY p.status,p.created_at DESC";
        }
        else
        {
            if($article_identifier!=NULL)
                $statusQuery="  p.article_id='".$article_identifier."'";
            $refusedArticleQuery="SELECT p.id as participationId ,a.title,p.status,d.user_id as clientId,
                                p.price_corrector,a.id as article_id,
                               p.article_id,p.updated_at as delivery_date,p.corrector_submit_expires,a.currency
                             FROM ".$this->_name." p
                             INNER JOIN Article a ON a.id=p.article_id
                             INNER JOIN Delivery d ON a.delivery_id=d.id
                             where ".$statusQuery." p.status in ('disapproved','closed') and p.corrector_id='".$identifier."'
                             AND d.submitdate_bo >= CURDATE()
                             AND (p.corrector_submit_expires=0 OR p.corrector_submit_expires > UNIX_TIMESTAMP())
                             GROUP BY p.id
                             ORDER BY p.status ASC,p.created_at ASC";
        }
       // echo $refusedArticleQuery;exit;
        if(($count=$this->getNbRows($refusedArticleQuery))>0)
        {
            $refusedArticles=$this->getQuery($refusedArticleQuery,true);
            return $refusedArticles;
        }
    }
    public function getParticipantCountOngoing($articleId)
    {
        $query="select count(*) as totalParticipants from ".$this->_name." where article_id='".$articleId."'and status in ('bid','under_study')";
        $participantsCount=$this->getQuery($query,true);
        return $participantsCount[0]['totalParticipants'];
    }
    /**get article path to download a article in brief**/
    public function getArticlePath($articleIdentifier)
    {
        $articleQuery="select ap.article_path,ap.version,p.user_id,d.title as DeliveryTitle,a.title as AOTitle,a.currency
                        from Participation p
                        INNER JOIN Article a ON a.id=p.article_id
                        INNER JOIN Delivery d ON d.id=a.delivery_id
                        INNER JOIN ArticleProcess ap ON p.id=ap.participate_id
                        LEFT JOIN ArticleProcess ap1 ON (p.id=ap1.participate_id AND ap.version < ap1.version)
                        WHERE p.article_id='".$articleIdentifier."' and ap1.id IS NULL";
        //echo $articleQuery;exit;
        if(($count=$this->getNbRows($articleQuery))>0)
        {
            $articleDetails=$this->getQuery($articleQuery,true);
            return $articleDetails[0]['article_path'];
        }
        else
            return "NOT EXIST";
    }
    /**get Participation details*/
    public function getParticipationDetails($participationId)
    {
        $query="select * from ".$this->_name." where id='".$participationId."'";
        if(($count=$this->getNbRows($query))>0)
        {
            $details=$this->getQuery($query,true);
            return $details;
        }
    }
    public function updateParticipationDetails($data,$participationId)
    {
        $where=" id='".$participationId."'";
		$data['updated_at']=date("Y-m-d H:i:s");
        return $this->updateQuery($data,$where);
    }
    /**check correction participation exists or not for a article*/
    public function checkCorrectorParticipationExists($articleId)
    {
        $query="select id from ".$this->_name." where article_id='".$articleId."'";
        if(($count=$this->getNbRows($query))>0)
        {
            $details=$this->getQuery($query,true);
            return $details[0]['id'];
        }
        else
            return "NO";
    }
    /*get Corrector participation based on contributor participation Id**/
    public function getCorrectorParticipationDetails($contrib_participationId)
    {
        $query="select * from ".$this->_name." where participate_id='".$contrib_participationId."'";
        if(($count=$this->getNbRows($query))>0)
        {
            $details=$this->getQuery($query,true);
            return $details;
        }
    }
     /*get Corrector participation based on contributor participation Id**/
    public function getCorrectorParticipationDetails2($contrib_participationId)
    {
        $query="select * from ".$this->_name." where participate_id='".$contrib_participationId."'";
		echo $query;
		$details=$this->getQuery($query,true);
		return $details;
    }
    /** cron function ->Getting all participation's in cron based on the participation time expires for a article**/
    public function getParticipationWithSenior()
    {
        //IF(u.profile_type='senior',count(p.article_id),0)
        $articleQuery="SELECT a.id,count(p.article_id) as totalParticipates
                            FROM CorrectorParticipation p
                       INNER JOIN Article a ON a.id=p.article_id
                       INNER JOIN Delivery d ON a.delivery_id=d.id
                       INNER JOIN User u ON p.corrector_id=u.identifier
                       WHERE p.status='bid_corrector' and UNIX_TIMESTAMP() > (a.correction_participationexpires+(a.correction_selection_time*60))
                       Group BY p.article_id
                       HAVING totalParticipates >0
                       ORDER By p.price_corrector ASC,u.profile_type2 DESC,p.created_at ASC
                       ";
       // echo $articleQuery;exit;
        if(($result = $this->getQuery($articleQuery,true))!= NULL)
            return $result;
        else
            return "NO";
    }
    /**cron function**/
    public function getParticipationLessPrice($articleId)
    {
        $articleQuery="SELECT a.title,p.id,p.price_corrector,u.profile_type,u.profile_type2,u.type2,p.corrector_id,p.article_id
                            FROM CorrectorParticipation p
                       INNER JOIN Article a ON a.id=p.article_id
                       INNER JOIN Delivery d ON a.delivery_id=d.id
                       INNER JOIN User u ON p.corrector_id=u.identifier
                       WHERE p.status='bid_corrector' and u.blackstatus='no' and a.correction_participationexpires < UNIX_TIMESTAMP() and p.article_id='".$articleId."'
                       ORDER By p.price_corrector ASC,u.type2 DESC,u.profile_type2 DESC,p.created_at ASC
                       ";
        //echo $articleQuery;  exit;
        if(($result = $this->getQuery($articleQuery,true))!= NULL)
            return $result;
        else
            return "NO";
    }
    /**setting time to expire to participate in FO
    time in Hours*
     **/
    public function updateArticleSubmitExpires($data,$query)
    {
        $this->_name='CorrectorParticipation';
		$data['updated_at']=date("Y-m-d H:i:s");
        $this->updateQuery($data,$query);
    }
    public function updateParticipation($data,$query)
    {
        //echo $query;
//        print_r($data);exit;
        //$this->_name= 'Participation';
        $data['updated_at']=date("Y-m-d H:i:s", time());
        $this->updateQuery($data,$query);
    }
    /**Get all Participation's w.r.t Article Id**/
    public function getAllParticipationByArticle($articleId)
    {
        $query = "Select * from ".$this->_name." WHERE article_id='".$articleId."' and status='bid_corrector'";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return "NO";
    }
    /////////get aritcle and participation deatails w.r.t participation id  ///////////////////////////
    public function getParticipateDetails($partId)
    {
        $query = "select p.id as participateId,p.article_id,p.corrector_id,p.price_corrector,a.title, a.category, d.submitdate_bo, a.price_bo, a.type, a.nbwords,a.sign_type, d.id AS deliveryId, d.title AS deliveryTitle,
         d.created_at,d.user_id as clientId,d.deli_anonymous,IF(d.created_user,d.created_user,d.user_id) as created_user,p.*,          a.correction_jc_resubmission,a.correction_sc_resubmission,a.participation_time,a.senior_time,a.junior_time,a.subjunior_time,
		 a.correction_jc_submission,a.correction_sc_submission,d.contract_mission_id,a.currency
         from CorrectorParticipation p INNER JOIN Article a
                  ON a.id=p.article_id INNER JOIN Delivery d ON a.delivery_id=d.id WHERE p.id='".$partId."'";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return "NO";
    }
    /////////get writer profile in participate table  with corrector part id  ///////////////////////////
    public function getContributorDetails($crtpartId)
    {
         $query = "select p.user_id, cp.id, u.profile_type from CorrectorParticipation cp 
                  INNER JOIN Participation p ON p.id=cp.participate_id
                  INNER JOIN User u ON u.identifier = p.user_id   WHERE cp.id='".$crtpartId."'";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true))!= NULL)
            return $result;
        else
            return "NO";
    }
    /**corrector Published Articles**/
    public function publishedArticles($searchParams,$identifier)
    {
        $addQuery='';
        $orderQuery=' ORDER BY p.created_at ASC';
        if($searchParams['search_article']!=NULL)
        {
            //$addQuery.=" and a.title like '%".($searchParams['search_article'])."%'";
            $searchParams['search_article'] = preg_replace('/\s*$/','',$searchParams['search_article']);
            $words=explode(" ",$searchParams['search_article']);
            //print_r($words);
            if(count($words)>1)
            {
                $addQuery.=" and (a.title like '%".($searchParams['search_article'])."%' or";
                foreach($words as $key=>$word)
                {
                    if($word!='')
                    {
                        $addQuery.=" a.title like '%".($word)."%'";
                        if($key!=(count($words))-1)
                            $addQuery.=" or";
                    }
                }
                $addQuery.=")";
            }
            else
                $addQuery.=" and a.title like '%".($searchParams['search_article'])."%'";
        }
        if($searchParams['order_date']!=NULL)
        {
            if($searchParams['order_date']=='da')
                $orderQuery=' ORDER BY p.created_at ASC';
            else
                $orderQuery=' ORDER BY p.created_at DESC';
        }
         if($searchParams['search_article_id']!=NULL)
        {
            $addQuery.=" and a.id='".$searchParams['search_article_id']."'";
        } 
        else if($searchParams['order_price']!=NULL)
        {
            if($searchParams['order_price']=='pa')
                $orderQuery=' ORDER BY p.price_corrector ASC';
            else
                $orderQuery=' ORDER BY p.price_corrector DESC';
        }
        $publishedArticleQuery="SELECT a.title,d.user_id as clientId,p.price_corrector,p.article_id,r.created_at,a.paid_status,
                                a.language,a.category,a.num_min,a.num_max,d.premium_total,d.filepath,d.title as deliveryName,
                                r.participate_id,r.crt_participate_id,d.premium_option,r.correction,d.missiontest,a.currency
                             FROM ".$this->_name." p
                             INNER JOIN Article a ON a.id=p.article_id
                             INNER JOIN Delivery d ON a.delivery_id=d.id
                             INNER JOIN Royalties r ON r.crt_participate_id=p.id
                             where p.status='published' and p.corrector_id='".$identifier."'".
            $addQuery." GROUP BY p.id".$orderQuery;
        //echo $publishedArticleQuery;
        if(($count=$this->getNbRows($publishedArticleQuery))>0)
        {
            $publishedArticles=$this->getQuery($publishedArticleQuery,true);
            return $publishedArticles;
        }
    }
    /**get Total corrector Published Amount**/
    public function getPublishedAmount($ContributorIdentifier)
    {
        $publishedArticleQuery="SELECT sum(r.price) as totalPrice
                             FROM ".$this->_name." p
                             INNER JOIN Article a ON a.id=p.article_id
                             INNER JOIN Delivery d ON a.delivery_id=d.id
                             INNER JOIN Royalties r ON r.crt_participate_id=p.id
                             where p.status='published' and p.corrector_id='".$ContributorIdentifier."'";
        //echo $publishedArticleQuery;
        //if(($count=$this->getNbRows($publishedArticleQuery))>0)
        //{
        $publishedArticles=$this->getQuery($publishedArticleQuery,true);
        return $publishedArticles[0]['totalPrice'];
        //}
    }
    /*getting latest proposed price for a Correction particiapation */
    public  function getLatestProposedPrice($articleId)
    {
        $Query="Select p.price_corrector From  ".$this->_name." p
                Where p.article_id='".$articleId."' and cycle=0
                ORDER BY p.created_at DESC LIMIT 1;
                ";
        //echo $Query;    exit;
        if(($result = $this->getQuery($Query,true))!= NULL)
            return $result[0]['price_corrector'];
        else
            return 0;
    }


    //get Refuse and Definitive refuse templates

    public function getAllTemplates($temptype)
    {
        /*if($identifier)
            $condition = " AND identifier='".$identifier."'";*/
        $TemplateQuery="SELECT identifier,type,title,content,active 
                            FROM Template 
                        WHERE active='yes' AND templatetype = '".$temptype."' ";
        //echo $TemplateQuery;  exit;
        if(($templates = $this->getQuery($TemplateQuery,true))!= NULL)
            return $templates;
        else
            return 0;
    }
    //get Refuse and Definitive refuse templates
    public function getTemplates($type,$identifier=NULL)
    {

        if($identifier)
            $condition = " AND identifier='".$identifier."'";

        $TemplateQuery="SELECT identifier,type,title,content,active 
                            FROM Template 
                        WHERE type='".$type."' AND active='yes' $condition ";
                    
        
        //echo $TemplateQuery;  exit;
        if(($templates = $this->getQuery($TemplateQuery,true))!= NULL)
            return $templates;
        else
            return 0;

    }


     public function CorrectorParticipation()
    {
        
        $this->createIdentifier();
    }
    public function getIdentifier()
    {
        return $this->cidentifier;
    }   
    
    public function createIdentifier()
    {
        $s=new String();        
        $this->cidentifier=number_format(microtime(true),0,'','').mt_rand(10000,99999);
    }
	 /////////check whether record is present with given statuses/////////////////////////
    public function checkCrtRecordPresent($artId, $status, $current_stage)
    {
        $query = "select id FROM ".$this->_name." WHERE article_id=".$artId." AND status = '".$status."' AND current_stage = '".$current_stage."' AND cycle=0";
        if(($result = $this->getQuery($query,true)) != NULL)
            return "YES";
        else
            return "NO";
    }

    /////////check any corrector participated in article or not for progressbar/////////////////////////
    public function checkCrtParticipation($artId)
    {
        $query = "select id FROM ".$this->_name." WHERE article_id=".$artId." AND cycle=0";
        if(($result = $this->getQuery($query,true)) != NULL)
            return "YES";
        else
            return "NO";
    }
    /////////check whether record is present with given statuses/////////////////////////
   /* public function checkCrtParticipationProgress($artId, $wrtstatus, $wrtcurrent_stage, $crtstatus, $crtcurrent_stage)
    {
        $query = "select cp.id FROM ".$this->_name." cp INNER JOIN Participation p ON cp.participation_id = p.identifier WHERE cp.article_id=".$artId."
                            AND cp.status = '".$crtstatus."' AND cp.current_stage = '".$crtcurrent_stage."'
                            AND p.status = '".$wrtstatus."' AND p.current_stage = '".$wrtcurrent_stage."' AND cycle=0";
        if(($result = $this->getQuery($query,true)) != NULL)
            return "YES";
        else
            return "NO";
    }*/
	
	public function getNotRefusedCrtParticipationsUserIds($artId)
    {
        $query = "select corrector_id  FROM ".$this->_name." WHERE article_id=".$artId." AND status NOT IN ('bid_refused')";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function getSelectedCorrector($article)
	{
		$query1 = "select corrector_id  FROM ".$this->_name." WHERE article_id=".$article." AND status IN ('bid','under_study','validated','published','disapproved') AND cycle=0"; 

        if(($result1 = $this->getQuery($query1,true)) != NULL)
            return $result1[0]['corrector_id'];
        else
            return "NO";
	}
	
	 /**cron function**/
    public function getParticipationLessPrice1($articleId,$selectedwriter=NULL)
    {
        if($selectedwriter)
			$WhereW=" AND p.corrector_id!='".$selectedwriter."'";
			
		$articleQuery="SELECT a.title,p.id,p.price_corrector,u.profile_type,u.profile_type2,u.type2,p.corrector_id,p.article_id
                            FROM CorrectorParticipation p
                       INNER JOIN Article a ON a.id=p.article_id
                       INNER JOIN Delivery d ON a.delivery_id=d.id
                       INNER JOIN User u ON p.corrector_id=u.identifier
                       WHERE p.status='bid_corrector' and u.blackstatus='no' and a.correction_participationexpires < UNIX_TIMESTAMP() and p.article_id='".$articleId."' ".$WhereW."
                       ORDER By p.price_corrector ASC,u.type2 DESC,u.profile_type2 DESC,p.created_at ASC
                       ";
        //echo $articleQuery;  exit;
        if(($result = $this->getQuery($articleQuery,true))!= NULL)
            return $result;
        else
            return "NO";
    }
	
	public function checkParticipationInCorrection($art,$user)
	{
		$query2 = "select *  FROM ".$this->_name." WHERE article_id='".$art."' AND corrector_id='".$user."' AND status IN ('bid_corrector','bid_temp') AND cycle=0"; 
		
       if(($result2 = $this->getQuery($query2,true)) != NULL)
            return $result2;
        else
            return "NO";
	}
    /* *** added on 17.02.2016 ** */
    /////////get all corrector participates in article for stage2///////////////////////////
    public function getAllCrtParticipationsStage2($artId)
    {
        $query = "select id  FROM ".$this->_name." WHERE article_id=".$artId." AND current_stage='stage2' AND status='under_study'";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get aritcle and participation deatails w.r.t participation id  ///////////////////////////
    public function getCrtParticipateDetails($partId)
    {
        $query = "select cp.id as crtparticipateId,cp.article_id,cp.corrector_id,cp.price_corrector, cp.status,a.title, a.category, d.submitdate_bo,
a.currency,
                    a.price_bo, a.type, a.nbwords,a.sign_type, d.id AS deliveryId, d.title AS deliveryTitle,d.created_at,cp.corrector_submit_expires,
                    d.user_id as clientId,d.deli_anonymous from ".$this->_name." cp
		           INNER JOIN Article a  ON a.id=cp.article_id
		           INNER JOIN Delivery d ON a.delivery_id=d.id WHERE cp.id=".$partId;//." where ".$whereQuery;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ////////////udate the Participation table//////////////////////
    public function updateCrtParticipation($data,$query)
    {
        //echo $query;
        //print_r($data);
        $data['updated_at']=date("Y-m-d H:i:s", time());
        $this->updateQuery($data,$query);
    }
    /* *** end of added on 17.02.2016 ** */
}
