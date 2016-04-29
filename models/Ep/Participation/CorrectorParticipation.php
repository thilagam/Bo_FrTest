<?php

class Ep_Participation_CorrectorParticipation extends Ep_Db_Identifier
{
    protected $_name = 'CorrectorParticipation';
    private $id;
    private $article_id;
    private $corrector_id;
    private $price_corrector;
    private $status;
    private $created_at;
    private $updated_at;

    public function loadData($array)
    {
        $this->article_id=$array["article_id"];
        $this->corrector_id=$array["corrector_id"];
        $this->price_corrector=$array["price_corrector"];
        $this->status=$array["status"];
        $this->created_at=$array["created_at"];
        $this->updated_at=$array["updated_at"];
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["id"] = $this->getIdentifier();
        $array["article_id"] = $this->article_id;
        $array["corrector_id"] = $this->corrector_id;
        $array["price_corrector"] = $this->price_corrector;
        $array["status"] = $this->status;
        $array["created_at"] = $this->created_at;
        return $array;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }

    /////////get all records of corrector cum writer profile ///////////////////////////
   /* public function correctorProfilesList($condition)
    {
        $query = "select a.id as artid, a.title, a.category, d.submitdate_bo, a.bo_closed_status, a.price_bo, a.type, a.nbwords, a.correction_closed_status,
                  a.sign_type, a.price_min, a.price_max, a.contrib_percentage, a.correction_participationexpires, a.contrib_price, d.title AS deliveryTitle,
                  cp.*, u.email, u.profile_type, up.first_name, up.last_name, count(cp.corrector_id) AS userCount, d.delivery_date, u.identifier,max(cp.cycle) +1 AS step  FROM ".$this->_name." cp
                  INNER JOIN Article a ON a.id=cp.article_id
                  INNER JOIN Delivery d ON a.delivery_id=d.id
                  INNER JOIN User u ON u.identifier=cp.corrector_id
                  LEFT JOIN UserPlus up ON up.user_id=cp.corrector_id
                  WHERE d.premium_option != '0' AND a.correction='yes' ".$condition." GROUP BY cp.article_id ORDER BY cp.status='bid_temp' desc";
        //echo $query;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }*/
    /////////get all records of writers profile ///////////////////////////
    public function correctorProfilesList($params)
    {
        $condition = '';
        if($params['search'] == 'search')
        {

            if($params['startdate'] !='' && $params['enddate']!='')
            {
                $start_date = str_replace('/','-',$params['startdate']);
                $end_date = str_replace('/','-',$params['enddate']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $condition.= " AND d.created_at BETWEEN '".$start_date."' AND DATE_ADD('".$end_date."', INTERVAL 1 DAY)";
            }
            if($params['aoId']!='0')
            {
                $condition.= " AND d.id =".$params['aoId']."";
            }
            if($params['inchargeId']!='0')
            {
                $condition.= " AND d.created_user =".$params['inchargeId']."";
            }
            if($params['clientId']!='0')
            {
                $condition.= " AND d.user_id =".$params['clientId']."";
            }
            /*if($params['closed']!='0')
            {
                if($params['closed'] != 'all')
                    $condition.= " AND d.id IN (".$params['searchaosarray'].")";
                else
                    $condition.= " ";
            }*/
        }
        if($params['loginUserType'] != 'superadmin')
        {
            if($params['profilelist'] == 'own')
            {
                $condition.= " AND d.created_user='".$params['loginUserId']."'";
            }
        }else{
            $condition.= '';
        }
            $query = "select d.id, a.id as artid, count(a.title) AS artCount, a.category, a.correction_closed_status, a.price_bo, a.type, a.nbwords,
                a.sign_type, a.price_min, a.price_max, a.contrib_percentage, a.correction_participationexpires, a.contrib_price, d.created_at,
                d.title AS deliveryTitle, d.AOtype, u.email, c.company_name, up.first_name as incharge, u.created_at AS doj FROM Delivery d
                INNER JOIN Article a ON a.delivery_id=d.id
                INNER JOIN User u ON u.identifier=d.user_id
                LEFT JOIN Client c ON c.user_id=u.identifier
                LEFT JOIN UserPlus up ON up.user_id = d.created_user WHERE d.premium_option != '0' AND a.correction='yes' AND a.correction_participationexpires!=0 AND a.correction_closed_status IS NULL ".$condition." GROUP BY d.id ORDER BY d.created_at DESC";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all participated users who are just refused to send mails///////////////////////////
    public function getNotRefusedCrtParticipationsUserIds($artId)
    {
        $query = "select corrector_id  FROM ".$this->_name." WHERE article_id=".$artId." AND status NOT IN ('bid_refused')";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////checking for last paricipation when it is refused the article will be sent back to FO again to publish from Fo///////////////////////////
    public function getlastArticlesBackToFo($artId)
    {
        //$query = "select count(id) AS lastpartcount  FROM ".$this->_name." WHERE article_id='".$artId."' AND status IN ('bid','bid_premium','under_study','disapproved','published','time_out')";
        $query = "select count(id) AS lastpartcount  FROM ".$this->_name." WHERE article_id='".$artId."' AND status IN ('bid_corrector','bid','under_study','disapproved','published','time_out')";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all paritcipated users ids in article who involved in bidding///////////////////////////
    public function  getGroupCrtParticipants($artId)
    {
        $query = "select corrector_id, id  FROM ".$this->_name." WHERE article_id=".$artId." ORDER BY FIELD(status, 'bid_corrector','bid','under_study','validated','published','bid_temp','bid_refused_temp','disapproved','bid_refused','closed','on_hold')";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all paritcipated users count in article for some stages///////////////////////////
    public function  getCrtPartsCount($artId)
    {
        $query = "(select count(id) AS partcount  FROM ".$this->_name." WHERE article_id=".$artId." AND status='bid')
                UNION ALL
                       (select count(id) AS partcount  FROM ".$this->_name." WHERE article_id=".$artId." AND status='bid_refused')
                UNION ALL
                       (select count(id) AS partcount  FROM ".$this->_name." WHERE article_id=".$artId." AND status='bid_corrector')";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	/////////get all paritcipated users count in article///////////////////////////
    public function  getUserCountInArticle($artId)
    {
       $query = "select count(id) AS userCount  FROM ".$this->_name." WHERE article_id=".$artId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all cycles count to know how many time the article got permanently refused in all stage///////////////////////////
    public function getCrtParticipationCyclesOnPartId($partId)
    {
        $query = "select cycle  FROM ".$this->_name." WHERE id=".$partId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all cycles count to know how many time the article got permanently refused in all stage///////////////////////////
    public function getCrtParticipationCycles($artId)
    {
        $query = "select max(cycle) as cycle  FROM ".$this->_name." WHERE article_id=".$artId;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////////get refused contributors to show in republish popup///////////////////////////
    public function getRefusedCrtParts($artId)
    {
        $query = "select count(id) AS refusedcount  FROM ".$this->_name." WHERE article_id='".$artId."' AND status IN ('bid_refused')";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result[0]['refusedcount'];
        else
            return "NO";
    }
    /////////checking for articles to go back again to publesh from Fo///////////////////////////
    public function getArticlesBackToFo($artId)
    {
        $query = "select article_id  FROM ".$this->_name." WHERE article_id='".$artId."' AND current_stage='contributor' AND status IN ('bid','bid_corrector','under_study','disapproved','published','time_out')";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get refused contributors when one of them got selected in selection profile after the bid///////////////////////////
    public function getRefusedCorrectors($artId)
    {
        $query = "select id, corrector_id  FROM ".$this->_name." WHERE article_id='".$artId."' AND current_stage IN ('contributor','corrector') AND status IN ('bid_refused', 'bid_corrector', 'bid_temp', 'bid_refused_temp')";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////finding the any contirbutor validiate in selection profiles//////////////////////////
    public function anyValidatedCorrector($artId)
    {
        $query = "select article_id  FROM ".$this->_name." WHERE article_id='".$artId."' AND status IN ('bid','under_study','disapproved', 'published')";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get aritcle and participation deatails w.r.t participation id  ///////////////////////////
    public function getCrtParticipateDetails($partId)
    {
        $query = "select cp.id as crtparticipateId,cp.article_id,cp.corrector_id,cp.price_corrector, cp.status,a.title, a.category, d.submitdate_bo,
                    a.price_bo, a.type, a.nbwords,a.sign_type, d.id AS deliveryId, d.title AS deliveryTitle,d.created_at,cp.corrector_submit_expires,
                    d.user_id as clientId,d.deli_anonymous from ".$this->_name." cp
		           INNER JOIN Article a  ON a.id=cp.article_id
		           INNER JOIN Delivery d ON a.delivery_id=d.id WHERE cp.id=".$partId;//." where ".$whereQuery;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all participated users to send mails///////////////////////////
    public function getCrtParticipationsUserIds($artId)
    {
        $query = "select corrector_id  FROM ".$this->_name." WHERE article_id=".$artId." AND cycle=0";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function getCrtParticipationsUserIdsnotcycle0($artId)
    {
        $query = "select corrector_id  FROM ".$this->_name." WHERE article_id=".$artId;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function getCrtParticipationsUserIdsAll($artId)
    {
        $query = "select corrector_id  FROM ".$this->_name." WHERE article_id=".$artId;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////checking for last paricipation when it is refused the article will be sent back to FO again to publish from Fo///////////////////////////
    public function getlastCrtArticlesBackToFo($artId)
    {
        $query = "select count(id) AS lastpartcount  FROM ".$this->_name." WHERE article_id='".$artId."' AND status IN ('bid','bid_corrector','bid_nonpremium','under_study','disapproved','published','time_out','bid_temp','bid_refused_temp')";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all participated users to send mails///////////////////////////
    public function getCurrenctCycleCorrector($artId)
    {
        $query = "select corrector_id  FROM ".$this->_name." WHERE article_id=".$artId." AND cycle='0'";

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
    /////////get  corrector id to refused the corrector in moderation reuse///////////////////////////
    public function getParticipationsCorrectorToDisapprove($artId)
    {
         $query = "select id FROM ".$this->_name." WHERE article_id=".$artId." AND current_stage IN ('contributor', 'corrector') AND status IN ('bid')";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all participates in article for stage1///////////////////////////
    public function getCrtParticipationsStatus($partId)
    {
        $query = "select status, corrector_id, refused_count, DATE_FORMAT(accept_refuse_at, '%d/%m/%Y %k:%i') AS correcteddate FROM ".$this->_name." WHERE id=".$partId;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    public function getNotClosedSelectProfiles($aoId)
    {
        $query1 = "select a.id AS artId, d.total_article FROM Delivery d LEFT JOIN Article a ON a.delivery_id=d.id  Where d.id=".$aoId;
        $result1 = $this->getQuery($query1,true);
        if(count($result1) != ''){
            for($i=0; $i<count($result1); $i++)
            {
                if($result1[$i]['artId'] != '') {
                    $query2 = "select id  FROM ".$this->_name." WHERE status IN ('published', 'closed') AND cycle=0 AND article_id=".$result1[$i]['artId'];
                    $result2 = $this->getQuery($query2,true);
                    if($result2 != "")
                        $resarr[$i] = $result2[0]['id'];
                    else
                        $resarr[$i] = '';
                }
            }
        }

        $arrfinal = count(array_filter($resarr));
        if($result1[0]['total_article'] == $arrfinal)
            return "yes"; ///means closed
        else
            return  "NO"; ///means not closed

    }
    /////////get paricipation Id from the corrector participation table  ///////////////////////////
    public function getParticipateId($partId)
    {
        $query = "select participate_id from ".$this->_name." WHERE id=".$partId;//." where ".$whereQuery;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get  corrector id 2///////////////////////////
    public function getCorrectorId($partId, $artId)
    {
        $query = "select corrector_id  FROM ".$this->_name." WHERE article_id=".$artId." AND id=".$partId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////////get all articles which in correction mode of total articles in ao ///////////////////////////
    public function getArticlesInCorrection($delId)
    {
        $query = "SELECT count(DISTINCT a.id) AS artsincrt FROM Delivery d
        	INNER JOIN Article a ON a.delivery_id=d.id
        	WHERE a.correction = 'yes' AND  d.id=".$delId."";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all articles which profile are selected ///////////////////////////
    public function getCrtAffectedArticles($delId)
    {
        $query = "SELECT count(DISTINCT a.id) AS affectedart FROM Delivery d
        	INNER JOIN Article a ON a.delivery_id=d.id
        	INNER JOIN ".$this->_name." cp ON cp.article_id=a.id
        	WHERE cp.status  IN ('bid', 'under_study','published','plag_exec', 'disapproved', 'disapproved_temp', 'disapproved_client' ) AND cp.cycle = 0 AND d.id=".$delId."";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all articles which profile are not selected or article not with profiles//////////////////
    public function getCrtNotAffectedArticles($delId)
    {
        /*$query = "SELECT count( DISTINCT a.id) AS notaffectedart FROM Delivery d
        	INNER JOIN Article a ON a.delivery_id=d.id
        	LEFT JOIN Participation p ON p.article_id=a.id
        	WHERE  p.cycle != 0 AND a.id NOT in (select article_id from Participation where cycle=0 ) AND d.id='".$delId."'";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";*/

        $query1 = "SELECT count(DISTINCT a.id) AS firstcount FROM Delivery d
            INNER JOIN Article a ON a.delivery_id=d.id
            LEFT JOIN ".$this->_name." cp ON cp.article_id=a.id
            WHERE  cp.status IN ('closed', 'bid_refused') AND d.id='".$delId."'";
        $result1 = $this->getQuery($query1,true);
        $query2 = "SELECT count(DISTINCT a.id) AS secondcount FROM Delivery d
            INNER JOIN Article a ON a.delivery_id=d.id
            LEFT JOIN ".$this->_name." cp ON cp.article_id=a.id
            WHERE d.id='".$delId."' AND a.id NOT IN (SELECT article_id FROM CorrectorParticipation)";
        $result2 = $this->getQuery($query2,true);
        $query3 = "SELECT count(DISTINCT a.id) AS thirdcount FROM Delivery d
            INNER JOIN Article a ON a.delivery_id=d.id
            LEFT JOIN ".$this->_name." cp ON cp.article_id=a.id
            WHERE  cp.status IN ('bid_corrector', 'bid_temp','bid', 'under_study','published','plag_exec', 'disapproved', 'disapproved_temp', 'disapproved_client') AND cp.cycle = 0 AND d.id='".$delId."'";
        $result3 = $this->getQuery($query3,true);
        if($result1[0]['firstcount']+$result2[0]['secondcount'] > $result3[0]['thirdcount'])
            $result4 = $result1[0]['firstcount']+$result2[0]['secondcount']-$result3[0]['thirdcount'];
        else
            $result4 = $result1[0]['firstcount']+$result2[0]['secondcount'];
        if($result4 != 0)
            return $result4;
        else
            return 0;

    }
    /////////get all articles which are ongoing///////////////////////////
    public function getCrtBidEncoursArticles($delId)
    {
        $query = "SELECT count(DISTINCT a.id) AS bidencours FROM Delivery d
        	INNER JOIN Article a ON a.delivery_id=d.id
        	INNER JOIN ".$this->_name." cp ON cp.article_id=a.id
        	WHERE cp.status IN ('bid_corrector', 'bid_temp') AND cp.cycle = 0 AND d.id=".$delId."";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all number of paritcipation in article //////////////////////////
    public function  getCrtPartsCountInArticle($artId)
    {
        $query = "select count(distinct corrector_id) AS partscountinart  FROM ".$this->_name." WHERE article_id=".$artId."";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all participates in article at any status///////////////////////////
    public function getAllPartsStatusOfArt($artId)
    {
        // $query = "select status FROM ".$this->_name." WHERE article_id=".$artId."";

        $query = "select cp.status, u.identifier, u.type2, u.profile_type, u.profile_type2, u.email, up.first_name, up.last_name FROM ".$this->_name." cp

				INNER JOIN User u ON u.identifier=cp.corrector_id
				LEFT JOIN UserPlus up ON up.user_id=u.identifier WHERE article_id=".$artId."";

        //echo $query;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all records of rest of detials fot writers profile ///////////////////////////
    public function profilesListParticipation($artId, $condition1)
    {
       /* $query = "select  cp.*, u.email, u.profile_type, up.first_name, up.last_name, count(cp.corrector_id) AS userCount, u.identifier,max(cp.cycle) +1 AS step
          FROM ".$this->_name." cp
                  INNER JOIN User u ON u.identifier=cp.corrector_id
                  LEFT JOIN UserPlus up ON up.user_id=cp.corrector_id
                  WHERE cp.article_id = ".$artId." ".$condition1." ";*/
        $status_array="'bid', 'under_study', 'disapproved', 'published', 'bid_refused'";
        $query = "select  cp.*, u.email, u.profile_type, up.first_name, up.last_name,
                   (select count(cp1.id) FROM CorrectorParticipation cp1 Where cp1.cycle=0 and cp1.article_id=cp.article_id) as cycle0UserCount
                  FROM ".$this->_name." cp
                  INNER JOIN User u ON u.identifier=cp.corrector_id
                  LEFT JOIN UserPlus up ON up.user_id=cp.corrector_id
                  WHERE cp.article_id = '".$artId."' AND cp.cycle='0'
                  ORDER BY IF(cp.updated_at!='',cp.updated_at,cp.created_at) DESC, FIELD(cp.status,$status_array)
                   ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
        /*$query = "select a.id as artid, a.title, a.category, d.submitdate_bo, a.bo_closed_status, a.price_bo, a.type, a.nbwords,
                    a.sign_type, a.price_min, a.price_max, a.contrib_percentage, a.participation_expires, a.contrib_price, d.title AS deliveryTitle,
                  p.*, u.email, u.profile_type, up.first_name, up.last_name, count(p.user_id) AS userCount, d.delivery_date, u.identifier,max(p.cycle) +1 AS step  FROM ".$this->_name." p
                  INNER JOIN Article a ON a.id=p.article_id
                  INNER JOIN Delivery d ON a.delivery_id=d.id
                  INNER JOIN User u ON u.identifier=p.user_id
                  LEFT JOIN UserPlus up ON up.user_id=p.user_id
                  WHERE d.premium_option != '0' ".$condition." GROUP BY p.article_id ORDER BY p.status='bid_temp' desc";*/
    }
    /*get refused count of a article**/
    public function getRefusedCrtPartsCount($artId)
    {
        $query = "select count(id) AS refused_count  FROM ".$this->_name." WHERE article_id='".$artId."' AND status IN ('bid_refused')";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result[0]['refused_count'];
        else
            return 0;
    }
    /////////checking for articles to go back again to publesh from Fo when its in correction///////////////////////////
    public function getCrtArticlesBackToFo($artId)
    {
        $query = "select article_id  FROM ".$this->_name." WHERE article_id='".$artId."' AND current_stage='corrector' AND status IN ('bid_corrector','bid_premium','under_study','disapproved','published','time_out')";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all cycles count to know how many time the article got permanently refused in all stage///////////////////////////
    public function findAnyCycleZero($artId)
    {
        $query = "select cycle FROM ".$this->_name." WHERE cycle=0 AND article_id=".$artId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all active participants to send refuse mail when article republished //////////////////////////
    public function  getActiveParicipants($artId)
    {
        $query = "select corrector_id FROM ".$this->_name." WHERE cycle=0 AND status IN ('closed') AND article_id=".$artId."";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all corrector participates in article for stage2///////////////////////////
    public function getAllCrtParticipationsStage2($artId)
    {
        $query = "select id  FROM ".$this->_name." WHERE article_id=".$artId." AND current_stage='stage2' AND status='under_study'";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////////get refused participates count in article for all stage///////////////////////////
    public function  getCrtRefusedCount($partId)
    {
        $query = "select refused_count  FROM ".$this->_name." WHERE id=".$partId;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get corrector details with the corrector participate id  ///////////////////////////
    public function getCorrectorDetails($partId)
    {
        $query = "select u.email from ".$this->_name." cp
		          INNER JOIN User u ON u.identifier=cp.corrector_id WHERE cp.id=".$partId; //." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all participants in cycle 0/////////////////////////
    public function getNoOfCrtParticipants($artId)
    {
        $query = "select count(corrector_id) AS partsCount  FROM ".$this->_name." WHERE article_id=".$artId." AND cycle!=0";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all participants detials wiht article id in currrent cycle/////////////////////////
    public function getCrtParticipantsDetailsCycle0($artId)
    {
        $query = "select status, corrector_id  FROM ".$this->_name." WHERE article_id=".$artId." AND cycle=0";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function getCorrector($article)
	{
		$query = "select id,corrector_id ,participate_id,price_corrector FROM ".$this->_name." WHERE article_id='".$article."' ";
        $result = $this->getQuery($query,true);
        return $result;
        
	}
    /////////get all counts w r t corrector///////////////////////////
    /*public function  getCorrectorParticipantsStatistics($params)
    {
        $userId = $params['userid'];
        $condition = '';
        if($params['langs'] != '')
        {
            $condition.= " AND a.language = '".$params['langs']."' ";
        }
        else if($params['categs'] != '')
        {
            $condition.= " AND a.category = '".$params['categs']."' ";
        }
        $query = "(select count(p.id) AS partcount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                        WHERE p.corrector_id=".$userId." AND p.status='bid' ".$condition.")
                UNION ALL
                       (select count(p.id) AS partcount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                       WHERE p.corrector_id=".$userId." AND p.status='bid_refused' ".$condition.")
                UNION ALL
                       (select count(p.id) AS partcount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                       WHERE p.corrector_id=".$userId." AND p.status='bid_premium' ".$condition.")
                UNION ALL
                       (select count(p.id) AS partcount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                        WHERE p.corrector_id=".$userId." ".$condition.")
                UNION ALL
                       (select count(p.id) AS partcount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                        WHERE p.corrector_id=".$userId." AND p.status='published' ".$condition.")
                UNION ALL
                       (select count(p.id) AS partcount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                         INNER JOIN Delivery d ON d.id = a.delivery_id
                         INNER JOIN User u ON u.identifier = d.user_id
                         INNER JOIN Client cl ON cl.user_id = u.identifier
                         WHERE p.corrector_id=".$userId." ".$condition." GROUP BY cl.user_id)
                UNION ALL
                       (select count(p.id) AS partcount FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                           WHERE p.corrector_id = ".$userId." AND a.republish_count != '0' ".$condition.")
                UNION ALL
                       (select count(p.id) AS partcount FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                           WHERE p.corrector_id = ".$userId." AND p.corrector_submit_expires < NOW() ".$condition.")
                UNION ALL
                       (select rr.reasons AS partcount FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                            INNER JOIN ArticleReassignReasons rr ON p.id = rr.participate_id
                            WHERE p.corrector_id=".$userId." ".$condition.")
                UNION ALL
                       (select count(identifier) AS partcount FROM Template)";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }*/
    public function  getCorrectorParticipantsStatistics($params)
    {
        $userId = $params['userid'];
        $condition = '';
        if($params['langs'] != '')
        {
            $condition.= " AND a.language = '".$params['langs']."' ";
        }
        if($params['categs'] != '')
        {
            $condition.= " AND a.category = '".$params['categs']."' ";
        }
        $query1 = "select count(p.id) AS selectedCount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                        WHERE p.corrector_id=".$userId." AND p.status NOT IN ('bid','bid_refused') ".$condition;
        $query2 =  "select count(p.id) AS refusedCount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                       WHERE p.corrector_id=".$userId." AND p.status='bid_refused' ".$condition;
        $query3 =  "select count(p.id) AS inprocessCount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                       WHERE p.corrector_id=".$userId." AND p.status='bid' ".$condition;
        $query4 =  "select count(p.id) AS totalCount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                        WHERE p.corrector_id=".$userId." ".$condition."";
        $query5 =  "select count(p.id) AS publishedCount  FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                        WHERE p.corrector_id=".$userId." AND p.status='published' ".$condition;
        $query6 =  "select count(p.id) AS republishedCount FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                           WHERE p.corrector_id = ".$userId." AND a.republish_count != '0' ".$condition;
        $query7 =  "select count(p.id) AS notsubmittedCount FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                           WHERE p.corrector_id = ".$userId." AND p.corrector_submit_expires < NOW()  AND p.status NOT IN ('validated','published')".$condition;
        $query8 =  "select GROUP_CONCAT(reasons)  AS crttempreasons  FROM ArticleReassignReasons WHERE refused_by = ".$userId." AND type = 'temporaire' ";
        $query9 =  "select count(identifier) AS totaltemplateCount FROM Template";
        $query10 =  "select GROUP_CONCAT(reasons) AS crtpermreasons FROM ArticleReassignReasons WHERE refused_by = ".$userId." AND type = 'permanent' ";
        $result1 = $this->getQuery($query1,true);
        $result2 = $this->getQuery($query2,true);
        $result3 = $this->getQuery($query3,true);
        $result4 = $this->getQuery($query4,true);
        $result5 = $this->getQuery($query5,true);
        $result6 = $this->getQuery($query6,true);
        $result7 = $this->getQuery($query7,true);
        $result8 = $this->getQuery($query8,true);
        $result9 = $this->getQuery($query9,true);
        $result10 = $this->getQuery($query10,true);
        if(empty($result1))
            $result1[1]['selectedCount'] = 0;
        if(empty($result2))
            $result2[2]['refusedCount'] = 0;
        if(empty($result3))
            $result3[3]['inprocessCount'] = 0;
        if(empty($result4))
            $result4[4]['totalCount'] = 0;
        if(empty($result5))
            $result5[5]['publishedCount'] = 0;
        if(empty($result6))
            $result6[6]['republishedCount'] = 0;
        if(empty($result7))
            $result7[7]['notsubmittedCount'] = 0;
        if(empty($result8))
            $result8[8]['crttempreasons'] = 0;
        if(empty($result9))
            $result9[9]['totaltemplateCount'] = 0;
        if(empty($result10))
            $result10[10]['crtpermreasons'] = 0;
        $result11=array_merge($result1, $result2, $result3, $result4, $result5, $result6, $result7, $result8, $result9, $result10);
        return $result11;
    }
    /////////get all counts w r t contributor///////////////////////////
    public function  getCrtParticipantWtitersLIst($params)
    {
        $userId = $params['userid'];
        $query = "select p.user_id FROM ".$this->_name." cp INNER JOIN Participation p ON p.id = cp.participate_id
                         WHERE cp.corrector_id=".$userId." GROUP BY p.user_id";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ///get top 3 contributor who involved with this proofreader//
    public function topCorrrectorParticipated($params)
    {
        $userId = $params['userid'];
        $condition = '';
        if($params['langs'] != '')
        {
            $condition.= " AND a.language = '".$params['langs']."' ";
        }
        if($params['categs'] != '')
        {
            $condition.= " AND a.category = '".$params['categs']."' ";
        }
        $query11 =  "select concat(up.first_name, ' ', up.last_name) AS topcorrector, COUNT(*) AS topcorrectorscount  FROM ".$this->_name." p
                       INNER JOIN Article a ON a.id = p.article_id
                       INNER JOIN Delivery d ON d.id = a.delivery_id
                       INNER JOIN User u ON u.identifier = p.corrector_id
                       INNER JOIN UserPlus up ON up.user_id = u.identifier
                       WHERE d.created_user=".$userId."  ".$condition." GROUP BY up.first_name ORDER BY topcorrectorscount DESC LIMIT 3";
        if(($result = $this->getQuery($query11,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all records of rest of detials fot writers profile ///////////////////////////
    public function crtStatsArticles($userId, $searchParameters)
    {
        if($searchParameters['client_id'])
            $condition.=" AND d.user_id='".$searchParameters['client_id']."'";

        if($searchParameters['ao_id'])
            $condition.=" AND d.id='".$searchParameters['ao_id']."'";

        if($searchParameters['pay_status'])
        {
            if($searchParameters['pay_status']!='all')
                $condition.=" AND a.paid_status='".$searchParameters['pay_status']."'";
            $searchParameters['sorttype']='all';
        }
        if($searchParameters['startdate'] !='' && $searchParameters['enddate']!='')
        {
            $start_date = str_replace('/','-',$searchParameters['startdate']);
            $end_date = str_replace('/','-',$searchParameters['enddate']);
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));
            $condition.= " AND DATE(d.created_at) BETWEEN '".$start_date."' AND DATE_ADD('".$end_date."', INTERVAL 1 DAY)";
        }
        if($searchParameters['sorttype'])
        {
            if($searchParameters['sorttype']=='close')
                $condition.=" AND p.status IN ('published', 'closed', 'client_closed') ";
            else if($searchParameters['sorttype']=='new')
                $condition.=" AND p.status IN ('bid_premium') ";
            else if($searchParameters['sorttype']=='ongoing')
                $condition.=" AND p.status IN ('bid','under_study','plag_exec','disapproved','bid_refused') ";
            elseif($searchParameters['sorttype']=='all')
                $condition.=" ";
        }

        //$status_array="'bid', 'under_study', 'disapproved', 'published', 'bid_refused'";
        $query = "select  p.id, p.corrector_id, p.status, p.participate_id, a.title, d.title AS deliveryName, d.user_id AS clientid, d.created_user, d.language, d.category, u.email, u.profile_type, up.first_name, up.last_name
                  FROM ".$this->_name." p
                  INNER JOIN User u ON u.identifier=p.corrector_id
                  LEFT JOIN UserPlus up ON up.user_id=p.corrector_id
                  INNER JOIN Article a ON a.id = p.article_id
                  INNER JOIN Delivery d ON d.id = a.delivery_id
                  WHERE p.corrector_id = '".$userId."' AND p.cycle='0' AND d.created_by = 'BO'"
                 .(!empty($condition) ? $condition : '') ."  ";
        //echo $query; exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all count of participations w r t project manager///////////////////////////
    public function  getCrtParticipantOfProjectManager($params)
    {
        $userId = $params['userid'];
        $query = "select count(p.id) AS PMcrtpartscount FROM ".$this->_name." p INNER JOIN Article a ON p.article_id = a.id
                         INNER JOIN Delivery d ON d.id = a.delivery_id
                         WHERE d.created_user=".$userId." ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////checking for accepted and undergoing participants///////////////////////////
    public function getAcceptedCrtParticipant($artId)
    {
        $query = "select id, corrector_id FROM ".$this->_name." WHERE article_id='".$artId."' AND cycle=0 AND status IN ('bid', 'disapproved', 'under_study')";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////checking for just participated corrector///////////////////////////
    public function getNewCorrector($artId)
    {
        $query = "select id, corrector_id FROM ".$this->_name." WHERE article_id='".$artId."' AND cycle=0 AND status IN ('bid_corrector', 'bid_temp')";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
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
    /////////for testing of wrong date in article process/////////////////////////

    public function checkUserWithCrtParticipation($partId, $userId)
    {
        $query = "select id FROM ".$this->_name." WHERE participate_id=".$partId." AND corrector_id = '".$userId."' ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return "YES";
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
	
	public function checkParticipationInCorrection($art,$user)
	{
		$query2 = "select *  FROM ".$this->_name." WHERE article_id='".$art."' AND corrector_id='".$user."' AND status IN ('bid_corrector','bid_temp') AND cycle=0"; 
		
       if(($result2 = $this->getQuery($query2,true)) != NULL)
            return $result2;
        else
            return "NO";
	}
	
	 /////////check whether record is present with given statuses/////////////////////////
    public function checkRecordPresent($artId, $status)
    {
        $query = "select id FROM ".$this->_name." WHERE article_id=".$artId." AND status = '".$status."' AND cycle=0";
        if(($result = $this->getQuery($query,true)) != NULL)
            return "YES";
        else
            return "NO";
    }
    /*added by Naseer on 25.11.2015*/

    //to fecth the langauge and category related to statistique//
    public function getCrtLangCategoryStatis($userId){
        $query = "SELECT a.language, a.category
            FROM CorrectorParticipation p
            INNER JOIN Article a ON p.article_id = a.id
            WHERE p.corrector_id = '".$userId."'
            GROUP BY a.language, a.category";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
}



