<?php

/**

 * Ep_Participation_Participation

 * @author Admin

 * @package Participation

 * @version 1.0

 */

class Ep_Participation_Participation extends Ep_Db_Identifier

{

	protected $_name = 'Participation';

	private $id;

	private $article_id;

	private $watchlist_id;

	private $user_id;

	private $price_user;

	private $status;

	private $created_at;

	private $updated_at;

    private $accept_specifications;

    private $valid_date;
	private $ipaddress;



	public function loadData($array)

	{

		$this->article_id=$array["article_id"];

		$this->watchlist_id=$array["watchlist_id"];

		$this->user_id=$array["user_id"];

		$this->price_user=$array["price_user"];

		$this->status=$array["status"];

		$this->created_at=$array["created_at"];

		$this->updated_at=$array["updated_at"];

        $this->accept_specifications=$array["accept_specifications"];

        $this->valid_date=$array["valid_date"];
		$this->ipaddress=$array["ipaddress"];

		return $this;

	}

	public function loadintoArray()

	{

		$array = array();

		$array["id"] = $this->getIdentifier();

		$array["article_id"] = $this->article_id;

		$array["watchlist_id"] = $this->watchlist_id;

		$array["user_id"] = $this->user_id;

		$array["price_user"] = $this->price_user;

		$array["status"] = $this->status;

		$array["created_at"] = $this->created_at;

        $array["accept_specifications"]=$this->accept_specifications;

        $array["valid_date"]=$this->valid_date;
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

        $query="select count(*) as totalParticipants from Participation where article_id='".$articleId."' and cycle=0";// and status not in ('bid_premium','bid_refused')";



        $participantsCount=$this->getQuery($query,true);

        return $participantsCount[0]['totalParticipants'];

    }

    public function getParticipantCountOngoing($articleId)

    {

        $query="select count(*) as totalParticipants from Participation where article_id='".$articleId."'and status in ('bid','under_study')";



        $participantsCount=$this->getQuery($query,true);

        return $participantsCount[0]['totalParticipants'];

    }

    public function getParticipationDetails($participationId)

    {

        $query="select * from Participation where id='".$participationId."'";



         if(($count=$this->getNbRows($query))>0)

         {

            $details=$this->getQuery($query,true);

             return $details;

         }



    }
    /* *** function edited on 10.12.2015 *** */
    //adding the fetching of themes(caetogry name) so that only 20 charater is limit if theme(category) is weather//
    public function ongoingArticles($identifier,$from=NULL)

    {

        if($from=='popup')

        {

            $statusQuery="p.status in ('bid','under_study','bid_premium','disapprove_client')";

        }

        else

            $statusQuery=" ((p.status in ('bid','under_study','time_out','on_hold','bid_premium','bid_nonpremium','bid_temp','bid_refused_temp',

                            'disapproved_temp','closed_temp','disapproved','disapprove_client','closed_client_temp','plag_exec'))

                            OR (p.status='published' AND a.paid_status='notpaid') OR (p.status in ('closed','closed_client') AND p.updated_at >= ( CURDATE() - INTERVAL 2 DAY )))";



        $ongoingArticleQuery="SELECT d.*,p.id as participationId,d.title as deliveryName,a.title,d.user_id as clientId,p.price_user,a.id as article_id,

                              p.status,ap.article_sent_at,p.article_submit_expires,a.paid_status,a.participation_expires,

                              d.premium_total,c.company_name,d.AOtype,

                              a.language,a.category,a.num_min,a.num_max,d.premium_option,d.filepath,a.contribs_list,u.email,a.currency,

                              up.first_name,up.last_name,up.phone_number,(100-a.contrib_percentage) as ep_commission,

                              (p.price_user*(1+((100-a.contrib_percentage)/a.contrib_percentage))) as final_price,
                              IF(d.created_user,d.created_user,d.user_id) as created_user,a.participation_expires,a.subjunior_time,a.junior_time,
                              a.senior_time,a.jc_resubmission,a.sc_resubmission,p.contract_signed,d.free_article,
							  a.files_pack,d.stencils_ebooker,a.ebooker_sampletxt_id,a.ebooker_tokenids,a.product,
							  (
							    SELECT `theme_name`
                                FROM `EB_themes` EB_t
                                LEFT JOIN EB_category EB_c ON EB_t.`theme_id` = EB_c.`themes_id`
                                WHERE EB_c.`cat_id` = a.ebooker_cat_id
                              ) AS category_name,
                              a.bnp_city_id,a.bnp_sampletext_id,a.bnp_no_of_articles,
                              (SELECT CM.`bnp_mission` FROM `ContractMissions` AS CM WHERE CM.`contractmissionid`=d.`contract_mission_id`) AS bnp_mission


                             FROM Participation p

                             INNER JOIN Article a ON a.id=p.article_id

                             INNER JOIN Delivery d ON a.delivery_id=d.id

							 INNER JOIN User u ON d.user_id=u.identifier

                             LEFT JOIN  UserPlus up  ON d.user_id=up.user_id

                             LEFT JOIN  Client c  ON d.user_id=c.user_id

                             LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id and p.user_id=ap.user_id

                             where ".$statusQuery." and p.user_id='".$identifier."'

                             AND ((p.article_submit_expires=0 OR p.article_submit_expires > UNIX_TIMESTAMP())OR(p.article_submit_expires <= UNIX_TIMESTAMP() AND p.status in ('bid','time_out','under_study','plag_exec','disapproved')) OR (p.status in ('closed','closed_client') AND p.updated_at >= ( CURDATE() - INTERVAL 2 DAY ))) AND cycle=0

                             GROUP BY p.id

                             ORDER BY p.status,p.created_at DESC";

        //echo $ongoingArticleQuery;

        if(($count=$this->getNbRows($ongoingArticleQuery))>0)

        {

            $ongoingArticles=$this->getQuery($ongoingArticleQuery,true);

            return $ongoingArticles;

        }



    }

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



        if($searchParams['search_article_id']!=NULL)

        {

            $addQuery.=" and a.id='".$searchParams['search_article_id']."'";

        }



        if($searchParams['order_date']!=NULL)

        {

            if($searchParams['order_date']=='da')

                 $orderQuery=' ORDER BY p.created_at ASC';

            else

                $orderQuery=' ORDER BY p.created_at DESC';

        }

        else if($searchParams['order_price']!=NULL)

        {

            if($searchParams['order_price']=='pa')

                 $orderQuery=' ORDER BY p.price_user ASC';

            else

                $orderQuery=' ORDER BY p.price_user DESC';

        }







        $publishedArticleQuery="SELECT a.title,d.user_id as clientId,p.price_user,p.article_id,r.created_at,a.paid_status,

                                a.language,a.category,a.num_min,a.num_max,d.premium_total,d.filepath,d.title as deliveryName,

                                r.participate_id,d.premium_option,r.correction,d.missiontest,u.email,a.currency,

                                up.phone_number, (100-a.contrib_percentage) as ep_commission,

                                (r.price*(1+((100-a.contrib_percentage)/a.contrib_percentage))) as final_price,
                                d.free_article


                             FROM Participation p

                             INNER JOIN Article a ON a.id=p.article_id

                             INNER JOIN Delivery d ON a.delivery_id=d.id

			     INNER JOIN User u ON d.user_id=u.identifier

                             LEFT JOIN  UserPlus up  ON d.user_id=up.user_id

                             INNER JOIN Royalties r ON r.participate_id=p.id

                             where p.status='published' and r.user_id='".$identifier."' and p.user_id='".$identifier."'".

                             $addQuery." GROUP BY p.id".$orderQuery;

        //echo $publishedArticleQuery;exit;

        if(($count=$this->getNbRows($publishedArticleQuery))>0)

        {

            $publishedArticles=$this->getQuery($publishedArticleQuery,true);

            return $publishedArticles;

        }



    }

    public function getPublishedAmount($ContributorIdentifier)

    {

        $publishedArticleQuery="SELECT sum(r.price) as totalPrice

                             FROM Participation p

                             INNER JOIN Article a ON a.id=p.article_id

                             INNER JOIN Delivery d ON a.delivery_id=d.id

                             INNER JOIN Royalties r ON r.participate_id=p.id

                             where p.status='published' and p.user_id='".$ContributorIdentifier."'";



          //echo $publishedArticleQuery;

        //if(($count=$this->getNbRows($publishedArticleQuery))>0)

        //{

            $publishedArticles=$this->getQuery($publishedArticleQuery,true);

            return $publishedArticles[0]['totalPrice'];

        //}

    }

    public function refusedArticles($identifier,$status=NULL)

    {



       if($status=='time_out')

       {

                $statusQuery="p.status in ('bid','time_out','on_hold','refused','disapproved')";



        $refusedArticleQuery="SELECT p.id as participationId,a.title,d.user_id as clientId,p.price_user,a.id as article_id,IF(p.updated_at,p.updated_at,p.created_at) as delivery_date,

                              p.status,ap.article_sent_at,a.currency

                             FROM Participation p

                             INNER JOIN Article a ON a.id=p.article_id

                             INNER JOIN Delivery d ON a.delivery_id=d.id

                             LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id and p.user_id=ap.user_id

                             where ".$statusQuery." and p.user_id='".$identifier."' AND ((p.article_submit_expires < UNIX_TIMESTAMP() and p.status in ('bid','under_study','disapproved','time_out')) )

                             GROUP BY p.id

                             ORDER BY p.status,p.created_at DESC";

       }

        else

       {

         $refusedArticleQuery="SELECT ref.reasons,p.id as participationId ,a.title,p.status,d.user_id as clientId,p.price_user,

                               p.article_id,p.updated_at as delivery_date,p.article_submit_expires,a.currency

                             FROM Participation p

                             INNER JOIN Article a ON a.id=p.article_id

                             INNER JOIN Delivery d ON a.delivery_id=d.id

                             LEFT JOIN ArticleReassignReasons ref ON p.id=ref.participate_id

                             LEFT JOIN ArticleReassignReasons ref1 ON (p.id=ref1.participate_id AND ref.created_at < ref1.created_at)

                             where p.status in ('disapproved','closed') and p.user_id='".$identifier."'



                             AND (p.article_submit_expires=0 OR p.article_submit_expires > UNIX_TIMESTAMP())

                             and ref1.identifier IS NULL

                             GROUP BY p.id

                             ORDER BY p.status ASC,p.created_at ASC";

       }

        //echo $refusedArticleQuery;exit;

        if(($count=$this->getNbRows($refusedArticleQuery))>0)

        {

            $refusedArticles=$this->getQuery($refusedArticleQuery,true);

            return $refusedArticles;

        }



    }



//Get Premanant disapproved articles

    public function closedArticles($identifier)

    {



        $closeddArticleQuery="SELECT a.title,d.user_id as clientId,p.price_user,p.article_id,a.paid_status,

                                a.language,a.category,a.num_min,a.num_max,d.premium_total,d.filepath,d.title as deliveryName,d.premium_option,a.currency

                             FROM Participation p

                             INNER JOIN Article a ON a.id=p.article_id

                             INNER JOIN Delivery d ON a.delivery_id=d.id



                             where p.status='closed' and p.user_id='".$identifier."'

                             GROUP BY p.id

                             ORDER BY p.created_at ASC";

        //echo $publishedArticleQuery;exit;

        if(($count=$this->getNbRows($closeddArticleQuery))>0)

        {

            $closeddArticleQuery=$this->getQuery($closeddArticleQuery,true);

            return $closeddArticleQuery;

        }

    }



    public function getReasons($reasonIds)

    {

        $reasonIds="'".str_replace(",","','",$reasonIds)."'";

        $query="select group_concat( DISTINCT title separator '- ') as reasons

                from Template

                where identifier in (".$reasonIds.")";

       // echo $query;exit;

        if($reasons=$this->getQuery($query,true))

            return $reasons[0]['reasons'];

        else

            return NULL;

    }

    public function updateParticipationDetails($data,$participationId)

    {

        $where=" id='".$participationId."'";
		$data['updated_at']=date("Y-m-d H:i:s");
        //print_r($data);echo $where;exit;
        return $this->updateQuery($data,$where);

    }

    public function getLockStatus($articleId)

    {

        $query="select lock_status from LockSystem where article_id='".$articleId."'";

        if(($count=$this->getNbRows($query))>0)

        {

            $lock_status=$this->getQuery($query,true);

            if($lock_status[0]['lock_status']=='yes')

                return "YES";

            else

                return "NO";

        }

        else return "NO";



    }



    /////////get all paritcipated users ids in article who involved in bidding///////////////////////////

	public function  getGroupParticipants($artId)

	{

        //$whereQuery = " AND status NOT IN ('bid_premium','bid_refused')";



        $query = "select user_id,id  FROM ".$this->_name." WHERE cycle=0 and article_id=".$artId.$whereQuery.

                   " Group By user_id

                    ORDER By created_at ASC

                   ";

        //echo $query;exit;

        if(($result = $this->getQuery($query,true)) != NULL)

			return $result;

		else

			return "NO";

	}

    ///////getting the details of recent verison in process///////////////

	public function getRecentVersion($partId)

	{

	   $query = "SELECT participate_id, user_id, stage, status, article_path, article_name, version, article_sent_at FROM ArticleProcess WHERE

		         participate_id=".$partId." AND version=(select max(version) FROM ArticleProcess WHERE participate_id=".$partId.")";//." where ".$whereQuery;



	    if(($result = $this->getQuery($query,true)) != NULL)

			return $result;

		else

			return "NO";

	}

     ////////////udate the Participation table//////////////////////

    public function updateParticipation($data,$query)

    {

       //echo $query;

//		  print_r($data);exit;

	   //$this->_name= 'Participation';

        $data['updated_at']=date("Y-m-d H:i:s", time());

       $this->updateQuery($data,$query);

    }

	  /////////get all cycles count to know how many time the article got permanently refused in all stage///////////////////////////

    public function   getParticipationCycles($artId)

    {

        $query = "select max(cycle) as cycle  FROM ".$this->_name." WHERE article_id='".$artId."'";



        if(($result = $this->getQuery($query,true)) != NULL)

            return $result;

        else

            return "NO";

    }

    ////////////udate the articleProcess table//////////////////////

    public function updateArticleProcess($data,$query)

    {

         //echo $query;

        //print_r($data);exit;

        $this->_name= 'ArticleProcess';

       $this->updateQuery($data,$query);

    }

    /////////get aritcle and participation deatails w.r.t participation id  ///////////////////////////

	public function getParticipateDetails($partId)

	{

		 $query = "select p.id as participateId,p.article_id,p.user_id,p.price_user,a.title, a.category, d.submitdate_bo, a.price_bo, a.type, a.nbwords,a.sign_type, d.id AS deliveryId, d.title AS deliveryTitle,a.currency,

		 d.created_at,d.user_id as clientId,d.deli_anonymous,IF(d.created_user,d.created_user,d.user_id) as created_user,p.*

		 from Participation p INNER JOIN Article a

		          ON a.id=p.article_id INNER JOIN Delivery d ON a.delivery_id=d.id WHERE p.id='".$partId."'";



		//echo $query;exit;

         if(($result = $this->getQuery($query,true))!= NULL)

			return $result;

		else

			return "NO";

	}

    /**setting time to expire to participate in FO**/

     public function updateArticleParticipationExpires($articleId,$participation_expire=60)

    {

        if(!$participation_expire)

            $participation_expire=60;



        $this->_name='Article';

        $expires=time()+(60*$participation_expire);

        $data = array("participation_expires"=>$expires,"updated_at"=>date("Y-m-d H:i:s"));////////updating

        $query="id='".$articleId."' and participation_expires='0'";

        $this->updateQuery($data,$query);



    }

    /**setting time to expire to participate in FO

     time in Hours*

     **/



     public function updateArticleSubmitExpires($data,$query)

    {

        $this->_name='Participation';
		$data['updated_at']=date("Y-m-d H:i:s");
        $this->updateQuery($data,$query);



    }

    /**Get all Participation's w.r.t Article Id**/

    public function getAllParticipationByArticle($articleId)

    {

        $query = "Select * from ".$this->_name." WHERE article_id='".$articleId."' and status='bid_premium'";



		//echo $query;exit;

         if(($result = $this->getQuery($query,true))!= NULL)

			return $result;

		else

			return "NO";

    }



    /**Get user Participation's Count to allow the user to participate***/

    public function getCurrentParticipationCount($userId)

    {

        $correctionCount=$this->getCurrentCorrectorParticipationCount($userId);
	$correctionCount=0;


        $query = "Select count(*) as ongoingParticipation from ".$this->_name."

                WHERE status in ('bid','bid_premium','disapproved','disapprove_client','approved','on_hold','bid_nonpremium')

                 and user_id='".$userId."'";

        //echo $query;exit;

         if(($result = $this->getQuery($query,true))!= NULL)

			return ($result[0]['ongoingParticipation']+$correctionCount);

		else

			return $correctionCount;

    }

    //GET Corrector Participation Count;

    public function getCurrentCorrectorParticipationCount($userId)

    {

        $query = "Select count(*) as ongoingParticipation from CorrectorParticipation

                WHERE status in ('bid','bid_corrector','disapproved')

                 and corrector_id='".$userId."'";

        //echo $query;exit;

         if(($result = $this->getQuery($query,true))!= NULL)

            return $result[0]['ongoingParticipation'];

        else

            return 0;

    }



    /**Getting all participation's in cron based on the participation time expires for a article**/
    public function getParticipationWithSenior($type = NULL)
    {
        //edited by naseer on 27.11.2015//
        //if translator then give condition as translator else let the old flow continue//
        if($type == 'translator')
            $condition = "a.product = 'translation' AND ";
        else
            $condition = "";//a.product = 'redaction' AND
        //IF(u.profile_type='senior',count(p.article_id),0)

        $articleQuery="SELECT a.id,count(p.article_id) as totalParticipates

                            FROM Participation p

                       INNER JOIN Article a ON a.id=p.article_id

                       INNER JOIN Delivery d ON a.delivery_id=d.id

                       INNER JOIN User u ON p.user_id=u.identifier
                       WHERE ".$condition." p.status='bid_premium' and UNIX_TIMESTAMP() > (a.participation_expires+(a.selection_time*60))
                       Group BY p.article_id

                       HAVING totalParticipates >0

                       ORDER By p.price_user ASC,FIELD(u.profile_type,'senior','junior','sub-junior'),p.created_at ASC

                       ";

        //echo $articleQuery;exit;

         if(($result = $this->getQuery($articleQuery,true))!= NULL)

			return $result;

		else

			return "NO";

    }

    /**cron function**/

    public function getParticipationLessPrice($articleId)

    {



        $articleQuery="SELECT a.title,p.id,p.price_user,u.profile_type,p.user_id,p.article_id,
                        ((a.price_min*a.contrib_percentage)/100) as price_min,((a.price_max*a.contrib_percentage)/100) as price_max


                            FROM Participation p

                       INNER JOIN Article a ON a.id=p.article_id

                       INNER JOIN Delivery d ON a.delivery_id=d.id

                       INNER JOIN User u ON p.user_id=u.identifier
					   INNER JOIN Contributor con ON con.user_id=u.identifier
                       WHERE p.status='bid_premium' and u.blackstatus='no' and a.participation_expires < UNIX_TIMESTAMP() and p.article_id='".$articleId."'
                       Having p.price_user BETWEEN price_min AND price_max
                       ORDER By p.price_user ASC,FIELD(u.profile_type,'senior','junior','sub-junior'),p.created_at ASC LIMIT 1

                       ";

       // echo $articleQuery;//exit;

         if(($result = $this->getQuery($articleQuery,true))!= NULL)

			return $result;

		else

			return "NO";





    }



    /**cron function to Get all participation that are not submitted the articles and having 30min time to submit***/

    public function getParticipationThirtyMinLeft()

    {

        $articleQuery="SELECT a.title,p.id,p.price_user,u.profile_type,p.user_id,p.article_id

                       FROM Participation p

                   INNER JOIN Article a ON a.id=p.article_id

                   INNER JOIN Delivery d ON a.delivery_id=d.id

                   INNER JOIN User u ON p.user_id=u.identifier

                   WHERE p.status='bid' and p.article_submit_expires > 0 and p.article_submit_expires <= UNIX_TIMESTAMP()+(60*30)

                   and p.article_submit_expires >= UNIX_TIMESTAMP()-(60*15)

                   ORDER By p.price_user ASC,FIELD(u.profile_type,'senior','junior','sub-junior'),p.created_at ASC

                   ";

       // echo $articleQuery;

         if(($result = $this->getQuery($articleQuery,true))!= NULL)

			return $result;

		else

			return "NO";

    }

     /**cron function to Get all participation that are not submitted the articles and having 6hours time to submit***/

    public function getParticipationSixHoursLeft()

    {

        $articleQuery="SELECT a.title,p.id,p.price_user,u.profile_type,p.user_id,p.article_id

                       FROM Participation p

                   INNER JOIN Article a ON a.id=p.article_id

                   INNER JOIN Delivery d ON a.delivery_id=d.id

                   INNER JOIN User u ON p.user_id=u.identifier

                   WHERE p.status='bid' and p.article_submit_expires > 0 and p.article_submit_expires <= UNIX_TIMESTAMP()+(60*60*6)

                   and p.article_submit_expires >= UNIX_TIMESTAMP()-(60*60)

                   ORDER By p.price_user ASC,FIELD(u.profile_type,'senior','junior','sub-junior'),p.created_at ASC

                   ";

       // echo $articleQuery;

         if(($result = $this->getQuery($articleQuery,true))!= NULL)

			return $result;

		else

			return "NO";

    }



	 /*cron function to update time out status*/

	public function updatetimeout()

	{

        $timeoutParticipationQuery="SELECT p.id

                             FROM Participation p

                             INNER JOIN Article a ON a.id=p.article_id

                             INNER JOIN Delivery d ON a.delivery_id=d.id

                             LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id and p.user_id=ap.user_id

                             where p.status in ('bid','time_out','on_hold','refused','disapproved') AND ((p.article_submit_expires < UNIX_TIMESTAMP() and p.status in ('bid','under_study','disapproved')) )

                             GROUP BY p.id";



		 if(($result_time = $this->getQuery($timeoutParticipationQuery,true))!= NULL)

		{

			$Participation=array();

			for($t=0;$t<count($result_time);$t++)

			{

				$Participation[]=$result_time[$t]['id'];

			}



				$Part_str=implode(",",$Participation);



				$this->_name="Participation";

				$WhereQry=" id in (".$Part_str.")";





				$PartArray=array();



				$PartArray['status']='time_out';
				$PartArray['updated_at']=date("Y-m-d H:i:s");
				$this->updateQuery($PartArray,$WhereQry);

		}

	}

    public function timeoutParticipations()

    {





        $timeoutParticipationQuery="SELECT p.id as participationId,a.title,d.title as AOtitle,d.user_id as clientId,

                                p.price_user,a.id as article_id, p.status,ap.article_sent_at,

                                p.article_submit_expires,a.paid_status,d.id,count(*) as participationCount,u.email,

                                d.submitdate_bo,a.currency

                              FROM Participation p

                              INNER JOIN Article a ON a.id=p.article_id

                              INNER JOIN Delivery d ON a.delivery_id=d.id

                              INNER JOIN User u on d.user_id=u.identifier

                              LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id and p.user_id=ap.user_id

                              where p.status in ('bid_premium','bid_nonpremium')

                             GROUP BY p.article_id ORDER BY p.status,p.created_at DESC ";

        //echo $ongoingArticleQuery;

        if(($count=$this->getNbRows($timeoutParticipationQuery))>0)

        {

            $timeoutParticipations=$this->getQuery($timeoutParticipationQuery,true);

            return $timeoutParticipations;

        }

        else

            return "NO";



    }

    /**cron get Finished AO's(time out with not all articles validated)*/

    public function AoFinishedTimeout()

    {

        //in ('bid_premium','bid','disapproved','under_study','time_out')



        $timeoutAoQuery="SELECT d.* FROM Delivery d

                    INNER JOIN Article a ON a.delivery_id=d.id

                    INNER JOIN Participation p ON p.article_id=a.id

                    WHERE d.submitdate_bo < CURDATE() and p.status not in ('closed','published','time_out','on_hold')

                    Group BY d.id ";

        //echo $ongoingArticleQuery;

        if(($count=$this->getNbRows($timeoutAoQuery))>0)

        {

            $timeoutAos=$this->getQuery($timeoutAoQuery,true);

            return $timeoutAos;

        }

        else

            return "NO";



    }

    /**cron function to Get all participation that are not submitted the articles and having 1hours time to submit***/

    public function getParticipationOneHourLeft()

    {

        $articleQuery="SELECT a.title as article,p.id,p.price_user,u.profile_type,p.user_id,p.article_id,d.id as delivery,d.title,

                        d.created_user,d.user_id as client,d.premium_option

                       FROM Participation p

                   INNER JOIN Article a ON a.id=p.article_id

                   INNER JOIN Delivery d ON a.delivery_id=d.id

                   INNER JOIN User u ON p.user_id=u.identifier

                   WHERE p.status='bid' and p.article_submit_expires > 0 and p.article_submit_expires <= (UNIX_TIMESTAMP()+(60*60))

                   and p.article_submit_expires >= (UNIX_TIMESTAMP()+(55*60))

                   ORDER By p.price_user ASC,FIELD(u.profile_type,'senior','junior','sub-junior'),p.created_at ASC

                   ";

         //echo $articleQuery;

        if(($result = $this->getQuery($articleQuery,true))!= NULL)

            return $result;

        else

            return "NO";

    }

    /**cron function to Get all participation that are not submitted the articles with in time expires***/

    public function getArticleSubmissionExpires()

    {

        $articleQuery="SELECT a.title as article,p.id,p.price_user,u.profile_type,p.user_id,p.article_id,d.id as delivery,d.title,

                        d.created_user,d.user_id as client,d.premium_option

                       FROM Participation p

                   INNER JOIN Article a ON a.id=p.article_id

                   INNER JOIN Delivery d ON a.delivery_id=d.id

                   INNER JOIN User u ON p.user_id=u.identifier

                   WHERE p.status in ('bid') and p.article_submit_expires!=0 AND p.article_submit_expires < UNIX_TIMESTAMP()

                    and p.article_submit_expires >= (UNIX_TIMESTAMP()-(60*60))

                   GROUP BY p.id,a.id

                        ORDER BY p.article_submit_expires ASC

                   ";

        //echo $articleQuery;exit;

        if(($result = $this->getQuery($articleQuery,true))!= NULL)

            return $result;

        else

            return "NO";

    }

    //Cron function to get all Articles with  Contest time is over - time to select someone

    public function getTimeOutContestWithQuotes()

    {

        $articleQuery="SELECT a.title as article,count(p.article_id) as totalParticipates,p.article_id,d.id as delivery,d.title,

                        d.created_user,d.user_id as client,d.premium_option

                       FROM Participation p

                   INNER JOIN Article a ON a.id=p.article_id

                   INNER JOIN Delivery d ON a.delivery_id=d.id

                   INNER JOIN User u ON p.user_id=u.identifier

                   WHERE p.status in ('bid_premium','bid_nonpremium') and a.participation_expires!=0 AND a.participation_expires < UNIX_TIMESTAMP()

                    and a.participation_expires >= (UNIX_TIMESTAMP()-(60*60))

                   GROUP BY p.article_id

                   HAVING totalParticipates >0

                   ORDER BY a.participation_expires ASC

                   ";

        //echo $articleQuery;exit;

        if(($result = $this->getQuery($articleQuery,true))!= NULL)

            return $result;

        else

            return "NO";

    }

     //Cron function to get all Articles with  Contest time is over - no quotes sent

    public function getTimeOutContestWithoutQuotes()

    {

        $articleQuery="SELECT a.title as article,a.id as article_id,d.id as delivery,d.title,

                        d.created_user,d.user_id as client,d.premium_option

                       FROM Article a

                   INNER JOIN Delivery d ON a.delivery_id=d.id

                   INNER JOIN User u ON d.user_id=u.identifier

                   WHERE a.participation_expires!=0 AND a.participation_expires < UNIX_TIMESTAMP()

                    and a.participation_expires >= (UNIX_TIMESTAMP()-(60*60)) and a.id not in (select  DISTINCT article_id from

                        Participation)

                   GROUP BY a.id

                   ORDER BY a.participation_expires ASC

                   ";

        //echo $articleQuery;exit;

        if(($result = $this->getQuery($articleQuery,true))!= NULL)

            return $result;

        else

            return "NO";

    }









	 /////////get refused participates count in article for all stage///////////////////////////

	public function  getRefusedCount($partId)

	{

         $query = "select refused_count  FROM ".$this->_name." WHERE id='".$partId."'";

      //  echo $query;



		if(($result = $this->getQuery($query,true)) != NULL)

			return $result;

		else

			return "NO";

	}

    /*getting ongoing contributor participation by Article id  */

    public  function getContribParticipation($articleId)

    {

        $Query="Select p.id From Participation p

                Where p.status='under_study' and p.article_id='".$articleId."'";

        //echo $Query;   exit;

        if(($result = $this->getQuery($Query,true))!= NULL)

            return $result;

        else

            return "NO";
    }
     /*getting participation Count by Article id  */
    public  function getParticipationCountOnArticle($articleId)
    {
        $Query="Select p.id From Participation p
                Where  p.article_id='".$articleId."'";
        //echo $Query;   exit;
        if(($result = $this->getQuery($Query,true))!= NULL)
            return count($result);
        else
            return "0";
    }

    /*getting latest proposed price for a article */

    public  function getLatestProposedPrice($articleId)

    {

        $Query="Select p.price_user From Participation p

                Where p.article_id='".$articleId."' and cycle='0'

                ORDER BY p.created_at DESC LIMIT 1;

                ";

        //echo $Query;    exit;

        if(($result = $this->getQuery($Query,true))!= NULL)

            return $result[0]['price_user'];

        else

            return 0;

    }

	/////////get all paritcipated users ids in article who involved in bidding///////////////////////////

    public function  getGroupParticipantsOfComments($artId,$user_id)

    {

        $whereQuery = " AND status IN ('bid_premium','bid_nonpremium','under_study','bid') AND user_id!='".$user_id."'";



        $query = "select user_id  FROM ".$this->_name." WHERE cycle=0 and article_id=".$artId.$whereQuery.

                   " Group By user_id

                   UNION

                   select user_id FROM  AdComments  WHERE type='article' AND user_id!='".$user_id."' and type_identifier=".$artId.

                   " Group By user_id

                   ";

        //echo $query;exit;

        if(($result = $this->getQuery($query,true)) != NULL)

            return $result;

        else

            return "NO";

    }



    /////////Check any ongoing Particpation Exists or not///////////////////////////

    public function  checkOngoingParticipation($artId)

    {

        $whereQuery = " AND status IN ('under_study','bid','disapproved','disapproved_temp')";



        $query = "select user_id  FROM ".$this->_name." WHERE cycle=0 and article_id=".$artId.$whereQuery.

                   " Group By user_id

                   ";

        //echo $query;exit;

        if(($count=$this->getNbRows($query))>0)

            return "YES";

        else

            return "NO";

    }









	 // function to update NOn premium participation time

    public function updateLiberteParticipationTimeout($data)

    {

        $where=" status='bid_nonpremium' and valid_date IS NOT NULL AND DATE(valid_date) < CURDATE()";

        //print_r($data);echo $where;exit;
		$data['updated_at']=date("Y-m-d H:i:s");
        return $this->updateQuery($data,$where);



    }

	public function getPublishedPartsInLastHour()

	{

		$query = "select u.identifier from User u left join Participation p ON u.identifier = p.user_id where u.type='contributor' and u.profile_type='sub-junior' and p.status='published' AND p.current_stage = 'client'";

		if(($result = $this->getQuery($query,true)) != NULL)

            return $result;

        else

            return "NO";

	}



    //function get finished article  to display in aosearch finished AO block

    public function finishedArticles($searchParameters)

    {



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

            //$privateQuery="((d.AOtype='public' $view_to AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";

            $privateQuery="((d.AOtype='public' AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";

        }

         if($searchParameters['articleId']!=NULL)

            $addQuery.=" and a.id in ('".$searchParameters['articleId']."')";



        $statusQuery="('under_study','plag_exec','disapproved','disapprove_client','disapproved_temp','closed_client_temp','closed_temp','time_out','on_hold','bid','bid_premium','bid_nonpremium','bid_temp','bid_refused_temp')";



        $finishedArticleQuery="SELECT  count( pa.article_id ) as totalParticipation,d.AOtype,d.deli_anonymous,u.identifier,d.id as deliveryid,a.id as articleid,

                                a.title as title,a.language,a.category,a.type,a.created_at,a.updated_at,

                                a.sign_type,a.num_min,a.num_max,IF(a.contrib_price,a.contrib_price,(((a.price_min)*a.contrib_percentage)/100)) as price_min,

                                IF(a.contrib_price,a.contrib_price,(((a.price_max)*a.contrib_percentage)/100)) as price_max,email,

                                DATEDIFF(IF(a.participation_expires,a.participation_expires,d.submitdate_bo),NOW()) as remainingDays,a.participation_expires,d.subjunior_time,d.junior_time,d.senior_time,u.profile_type,

                                d.participation_time,d.jc_resubmission,d.sc_resubmission,d.link_quiz,d.quiz,d.quiz_duration,

                                GROUP_CONCAT(a.category) as filter_category,GROUP_CONCAT(a.language) as filter_language,a.type ,

                                d.premium_total,d.filepath,d.premium_option,a.contribs_list,

                                d.publishtime,d.view_to,IF(pa.accept_refuse_at,pa.accept_refuse_at,pa.created_at) as action_time,a.currency



                             FROM Delivery d

                             INNER JOIN  User u ON u.identifier=d.user_id

                             INNER JOIN Payment p ON p.delivery_id=d.id

                             INNER JOIN Article a ON a.delivery_id=d.id

                             INNER JOIN Participation pa ON pa.article_id = a.id

                             where p.status='Paid' and a.status!='validated' and d.status_bo='active' and pa.user_id!='".$userIdentifier."'

                             AND ".$privateQuery."

                             $addQuery

                             AND a.participation_expires < UNIX_TIMESTAMP()

                             GROUP BY articleid

                             Having NOW() <= action_time + INTERVAL 1 DAY

                             ORDER BY a.title ASC,FIND_IN_SET(pa.status, 'under_study,plag_exec,disapproved,disapprove_client,disapproved_temp,closed_client_temp,closed_temp,time_out,on_hold,bid,bid_premium,bid_nonpremium,bid_temp,bid_refused_temp')";

        //echo $finishedArticleQuery;exit;

        if(($count=$this->getNbRows($finishedArticleQuery))>0)

        {

            $finishedArticles=$this->getQuery($finishedArticleQuery,true);

            return $finishedArticles;

        }



    }
	 /////////check whether record is present with given statuses/////////////////////////

    public function checkRecordPresent($artId, $status, $current_stage)

    {

        $query = "select id FROM ".$this->_name." WHERE article_id='".$artId."' AND status = '".$status."' AND current_stage = '".$current_stage."' AND cycle=0";

        if(($result = $this->getQuery($query,true)) != NULL)

            return "YES";

        else

            return "NO";

    }

     public function checkAoParticipateDuplicates(){
		$statusQuery=" (p.status in ('bid','under_study','on_hold',
                            'disapproved_temp','closed_temp','disapproved','disapprove_client','closed_client_temp','plag_exec'))";

        $ongoingArticleQuery="SELECT a.id,a.delivery_id,a.title, COUNT(a.id) as count , d.created_user as created_user ,d.user_id as client,d.title AS deliveryTitle
                             FROM Participation p
                             INNER JOIN Article a ON a.id=p.article_id
                             INNER JOIN Delivery d ON a.delivery_id=d.id
							 where ".$statusQuery."
                             AND cycle=0
                             GROUP BY a.id
                             HAVING COUNT(cycle)>1
                             ORDER BY p.status,p.created_at DESC";
       // echo $ongoingArticleQuery;	exit;
        if(($count=$this->getNbRows($ongoingArticleQuery))>0)
        {
            $ongoingArticles=$this->getQuery($ongoingArticleQuery,true);
            return $ongoingArticles;
        }
	}
	public function checkAoParticipateCorrectionDuplicates(){
		$statusQuery=" (p.status in ('bid_corrector','under_study','on_hold',
                            'disapproved_temp','closed_temp','disapproved','disapprove_client','closed_client_temp','plag_exec'))";

        $ongoingArticleQuery="SELECT a.id,a.delivery_id,a.title, COUNT(a.id) as count , d.created_user as created_user ,d.user_id as client,d.title AS deliveryTitle
                             FROM  CorrectorParticipation p
                             INNER JOIN Article a ON a.id=p.article_id
                             INNER JOIN Delivery d ON a.delivery_id=d.id
							 where ".$statusQuery."
                             AND cycle=0
                             GROUP BY a.id
                             HAVING COUNT(cycle)>1
                             ORDER BY p.status,p.created_at DESC";
        //echo $ongoingArticleQuery;	exit;
        if(($count=$this->getNbRows($ongoingArticleQuery))>0)
        {
            $ongoingArticles=$this->getQuery($ongoingArticleQuery,true);
            return $ongoingArticles;
        }
	}
	 /**cron function to get all the parcitiaopations to check any same profile and price **/
    public function getParticipationSameProfilePrice($articleId,$price,$type)
    {

        $articleQuery="SELECT count(p.id) as samecount
                            FROM Participation p
                       INNER JOIN User u ON p.user_id=u.identifier
                       WHERE p.status='bid_premium' and u.blackstatus='no' and p.article_id='".$articleId."'
                       AND u.profile_type='".$type."' AND p.price_user='".$price."'";
        //echo $articleQuery;exit;
         if(($result = $this->getQuery($articleQuery,true))!= NULL)
			return $result;
		else
			return "NO";


    }
	 public function getParticipationsUserIds($artId)
    {
         $query = "select p.user_id, u.profile_type  FROM ".$this->_name." p  INNER JOIN User u ON p.user_id=u.identifier WHERE p.article_id=".$artId."";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all paritcipated users count in article for all stage///////////////////////////
    public function  getCountOnStatus($contribId)
    {
         $query = "select count(id) AS partscount  FROM ".$this->_name." WHERE user_id=".$contribId." AND status IN ('bid_premium', 'bid', 'bid_nonpremium', 'disapproved')";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	/*edited by naseer on 27-11-2015*/
	public function getParticipationLessPrice1($articleId,$corrector=NULL)
    {
		if($corrector)
			$WhereCorr=" AND p.user_id!='".$corrector."'";

		$articleQuery="SELECT a.title,a.product,p.id,p.price_user,u.profile_type,p.user_id,p.article_id,
		                con.translator,con.translator_type,
                        ((a.price_min*a.contrib_percentage)/100) as price_min,((a.price_max*a.contrib_percentage)/100) as price_max

                            FROM Participation p
                       INNER JOIN Article a ON a.id=p.article_id
                       INNER JOIN Delivery d ON a.delivery_id=d.id
                       INNER JOIN User u ON p.user_id=u.identifier
                       INNER JOIN Contributor con ON con.user_id=u.identifier
                       WHERE p.status='bid_premium' and u.blackstatus='no' and a.participation_expires < UNIX_TIMESTAMP() and p.article_id='".$articleId."'
                        ".$WhereCorr." Having p.price_user BETWEEN price_min AND price_max
                       ORDER By p.price_user ASC,FIELD(u.profile_type,'senior','junior','sub-junior'),p.created_at ASC limit 1
                       ";
        //echo $articleQuery;//exit;
        if(($result = $this->getQuery($articleQuery,true))!= NULL)
            return $result;
        else
            return "NO";
    }

	public function getSelectedwriters($article)
	{
		$query1 = "select user_id  FROM ".$this->_name." WHERE article_id=".$article." AND status IN ('bid','plag_exec','under_study','validated','published','disapproved','time_out') AND cycle=0";

        if(($result1 = $this->getQuery($query1,true)) != NULL)
            return $result1[0]['user_id'];
        else
            return "NO";
	}

	public function getParticipateId($user,$art)
	{
		$query2 = "select id  FROM ".$this->_name." WHERE article_id=".$art." AND user_id='".$user."' AND cycle=0";
		$result2 = $this->getQuery($query2,true);
        return $result2[0]['id'];

	}

	public function getParticipationDetail($part)
	{
		$query4 = "select *  FROM ".$this->_name." WHERE id='".$part."'";
		$result4 = $this->getQuery($query4,true);
        return $result4;
	}

	public function checkParticipation($art,$user)
	{
		$query2 = "select *  FROM ".$this->_name." WHERE article_id='".$art."' AND user_id='".$user."' AND status IN ('bid_premium','bid_nonpremium','bid_temp') AND cycle=0";

       if(($result2 = $this->getQuery($query2,true)) != NULL)
            return $result2;
        else
            return "NO";
	}

	public function getArticleStatus($art)
	{
		$query5 = "select * FROM ".$this->_name." WHERE article_id=".$art." AND cycle='0'";
		$result5 = $this->getQuery($query5,true);
        return $result5[0]['status'];
	}
    /* *** added on 17.02.2016 *** */
    /////////get aritcle and participation deatails w.r.t participation id  for cron purpose///////////////////////////
    public function getParticipateDetailsCron($partId)
    {
        $query = "select p.id as participateId,p.article_id,p.user_id,p.price_user,p.updated_at,p.clientcommentfile,a.title, a.category,
                    d.submitdate_bo, a.price_bo, a.type, a.nbwords,a.sign_type, d.id AS deliveryId, d.title AS deliveryTitle,p.article_submit_expires,
                    d.created_at, a.jc0_resubmission, a.jc_resubmission, a.sc_resubmission, d.user_id as clientId,d.deli_anonymous,d.free_article,d.missiontest,a.correction,
                    a.currency,
                    p.*
                    FROM ".$this->_name." p
                    INNER JOIN Article a  ON a.id=p.article_id
                    INNER JOIN Delivery d ON a.delivery_id=d.id WHERE p.id=".$partId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
}

