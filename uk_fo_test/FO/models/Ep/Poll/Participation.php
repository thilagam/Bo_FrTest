<?php
/**
 * Ep_Poll_Participation
 * @author Arun
 * @package Poll
 * @version 1.0
 */
class Ep_Poll_Participation extends Ep_Db_Identifier
{
    protected $_name = 'Poll_Participation';
	private $id;
	private $poll_id;
	private $user_id;
	private $price_user;
	private $comments;
    private $per_week;
    private $status;
	private $created_at;
	private $updated_at;
	
	private $possible_hours;
	
	private $availability;
	
	private $slot_from;
	
	private $slot_to;
	
	private $slot_ampm;
	
	private $slot_confirm;
	
	
	public function loadData($array)
	{
		$this->poll_id=$array["poll_id"];
		$this->user_id=$array["user_id"];
		$this->price_user=$array["price_user"];
        $this->comments=$array["comments"];
		$this->per_week=$array["per_week"];
        $this->status=$array["status"];
		$this->created_at=$array["created_at"];
		$this->updated_at=$array["updated_at"];
		
		$this->possible_hours=$array["possible_hours"];
		
		$this->availability=$array["availability"];
		
		$this->slot_from=$array["slot_from"];
		
		$this->slot_to=$array["slot_to"];
		
		$this->slot_ampm=$array["slot_ampm"];
		
		$this->slot_confirm=$array["slot_confirm"];
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["id"] = $this->getIdentifier();
		$array["poll_id"] = $this->poll_id;
		$array["user_id"] = $this->user_id;
        $array["price_user"] = $this->price_user;
        $array["comments"] = $this->comments;
        $array["per_week"] = $this->per_week;
		$array["status"] = $this->status;
		$array["created_at"] = $this->created_at;
		
		$array["possible_hours"] = $this->possible_hours;
		
		$array["availability"] = $this->availability;
		
		$array["slot_from"] = $this->slot_from;
		
		$array["slot_to"] = $this->slot_to;
		
		$array["slot_ampm"] = $this->slot_ampm;
		
		$array["slot_confirm"] = $this->slot_confirm;
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }
    public function __get($name){
            return $this->$name;
    }
    public function getAllPollAODetails($poll_search_params,$limit=NULL)
    {
        setlocale(LC_TIME, "fr_FR");
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;

        if($limit)
        	$limit_condition=" LIMIT 0,".$limit;

        if($poll_search_params['profile_type']=='senior')
        {
            $condition=" AND (po.contributors=0 or po.contributors=2 or po.contributors=4) ";
        }
        else if($poll_search_params['profile_type']=='junior')
        {
            $condition=" AND (po.contributors=1 or po.contributors=2 or po.contributors=4) ";
        }
        else if($poll_search_params['profile_type']=='sub-junior')
        {
            $condition=" AND (po.contributors=3 or po.contributors=4) ";
        }

        if($poll_search_params['search_ao_type']=='p_premium')
            $condition.=" AND po.created_by!='client'";
        elseif($poll_search_params['search_ao_type']=='p_npremium')
            $condition.=" AND (po.created_by='client')";

         if($poll_search_params['search_category']!=NULL)
            $condition.=" and po.category in ('".$poll_search_params['search_category']."')";
        
        if($poll_search_params['search_language']!=NULL)
            $condition.=" and po.language='".$poll_search_params['search_language']."'";  

        if($poll_search_params['search_article']!=NULL)
        {
            //$words=str_word_count($poll_search_params['search_article'],1);
            $poll_search_params['search_article'] = preg_replace('/\s*$/','',$poll_search_params['search_article']);
            $poll_search_params['search_article']=preg_replace('/\(|\)/','',$poll_search_params['search_article']);
            $poll_search_params['search_article']=addslashes($poll_search_params['search_article']);
            $words=explode(" ",$poll_search_params['search_article']);
           if(count($words)>1)
           {
                $condition.=" and (po.title like '%".($poll_search_params['search_article'])."%' or";
                foreach($words as $key=>$word)
                {
                   if($word!='')
                   {
                       $condition.=" po.title REGEXP '[[:<:]]".($word)."[[:>:]]'";
                       //$addQuery.=" a.title like '%".($word)."%'";
                       if($key!=(count($words))-1)
                            $condition.=" or";
                   }
                }
                $condition.=")";
            }
            else
                $condition.=" and po.title  REGEXP '[[:<:]]".$poll_search_params['search_article']."[[:>:]]'";
                //$addQuery.=" and a.title like '%".($poll_search_params['search_article'])."%'";
        }

        if(!$poll_search_params['req_from'])
            $condition.=" and po.id not in( select poll_id from Poll_Participation pp where pp.user_id='".$userIdentifier."')";
       
        $participationJoin=" LEFT JOIN Poll_Participation pa ON pa.poll_id = po.id 
                             LEFT JOIN  Client c  ON po.client=c.user_id ";


        //differed AO publish time condition
        if($poll_search_params['upcoming'])
            $publish_condition=" AND (po.publish_time > NOW()) ";
        else
            $publish_condition=" AND (po.publish_time < NOW()) ";                             

        if($poll_search_params['cnt_nextday'])
        {
           $publish_condition.=" AND DATE(po.publish_time)=CURDATE() + INTERVAL 1 DAY";
        }


        
		$pollQuery="SELECT count(DISTINCT pa.id ) as totalParticipation,po.total_article as totalArticles,po.client,c.company_name, 
					po.title,po.id as pollId,po.poll_date as delivery_date,po.category,po.type,po.language,po.price_min,
                    IF(po.contrib_percentage,(((po.price_max)*po.contrib_percentage)/100),po.price_max) as price_max,
					po.min_sign,po.max_sign,DATEDIFF(po.poll_date,NOW()) as remainingDays,po.poll_anonymous,po.poll_max,
					po.publish_time,po.client,IF(po.created_by='client','poll_nopremium','poll_premium') as ao_type,
                    po.priority_hours,po.file_name,po.production_time,po.contributors,po.created_by,po.currency
					FROM Poll po ".$participationJoin." 
					where po.valid_status='active'".$condition." AND  po.poll_date >=NOW() 
                    $publish_condition
					GROUP BY po.id ".$haveQuery." 
                    Having (po.poll_max>totalParticipation or po.poll_max='' or po.poll_max=0)
					ORDER BY po.poll_date ASC,title ASC,pollId DESC $limit_condition";
					
		/*$pollQuery="SELECT count(DISTINCT pa.id ) as totalParticipation,po.total_article as totalArticles,po.client,c.company_name, 
					po.title,po.id as pollId,po.poll_date as delivery_date,po.category,po.type,po.language,po.price_min,
                    IF(po.contrib_percentage,(((po.price_max)*po.contrib_percentage)/100),po.price_max) as price_max,
					po.min_sign,po.max_sign,DATEDIFF(po.poll_date,NOW()) as remainingDays,po.poll_anonymous,po.poll_max,
					po.publish_time,po.client,IF(po.created_by='client','poll_nopremium','poll_premium') as ao_type,
                    po.priority_hours,po.file_name,po.production_time
					FROM Poll po ".$participationJoin." 
					where po.valid_status='active'".$condition." AND  po.poll_date >=NOW() 
					GROUP BY po.id ".$haveQuery." 
					ORDER BY po.poll_date ASC,title ASC,pollId DESC $limit_condition";*/			
    
        //echo  $pollQuery;
        if(($count=$this->getNbRows($pollQuery))>0)
        {
            $PollDetails=$this->getQuery($pollQuery,true);
            return $PollDetails;
        }
        //else
          //  return NULL;
    }
    /**get Poll spec file**/
     public function getPollBrief($pollIdentifier)
    {
        $query="select p.id,p.title,p.file_name,p.priority_hours as poll_duration,p.price_min,IF(p.contrib_percentage,(((p.price_max)*p.contrib_percentage)/100),p.price_max) as price_max,,p.show_slot,p.contrib_percentage,p.noprice from Poll p
                where p.id='".$pollIdentifier."'";
        //echo $query;exit;
        if(($count=$this->getNbRows($query))>0)
        {
            $pollBrief=$this->getQuery($query,true);
            return $pollBrief;
        }
    }
    /**check for the poll participation of a user**/
    public function checkPollParticipation($pollIdentifier,$userIdentifier,$profile_type)
    {
        if($profile_type=='senior')
        {
            $condition=" AND (p.contributors=0 or p.contributors=2) ";
        }
        else if($profile_type=='junior')
        {
            $condition=" AND (p.contributors=1 or p.contributors=2) ";
        }
        $query="select pp.*,p.priority_hours as poll_duration from Poll p
                INNER JOIN Poll_Participation pp ON p.id=pp.poll_id
                where pp.poll_id='".$pollIdentifier."' and pp.user_id='".$userIdentifier."'".$condition;
        
        //echo  $query;exit;
        if(($count=$this->getNbRows($query))>0)
        {
            $pollBrief=$this->getQuery($query,true);
            return $pollBrief;
        }
        else
            return "NO";
    }
    /**update Poll Participation**/
     public function updatePollParticipation($data,$query)
    {
         $this->updateQuery($data,$query);
    }
    /////////get all paritcipated users ids in article who involved in bidding///////////////////////////
	public function  getGroupParticipants($pollId)
	{
        //$whereQuery = " AND status NOT IN ('bid_premium','bid_refused')";
        $query = "select user_id,id  FROM ".$this->_name." WHERE poll_id='".$pollId."' Group By user_id
                    ORDER By created_at ASC
                   ";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    public function Listpollclientmail()
	{
		$pollquery = "SELECT count( DISTINCT pp.id ) AS totalParticipation, p.id,p.poll_max, p.created_at, p.client, p.title, now() as now,p.poll_date,p.created_by,u.email,u.password,up.first_name,up.last_name
					FROM Poll p
					LEFT JOIN Poll_Participation pp ON p.id = pp.poll_id 
					INNER JOIN User u ON p.client=u.identifier
					LEFT JOIN UserPlus up ON u.identifier=up.user_id
					WHERE mail_client = 'no'
					GROUP BY p.id";
        
        if(($pollresult = $this->getQuery($pollquery,true)) != NULL)
			return $pollresult;
		else
			return "NO";
	}
	
	public function getSalesMail($sales)
	{
		 $query = "select u.email,up.first_name,up.last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.identifier='".$sales."'";
        
			if(($result = $this->getQuery($query,true)) != NULL)
				return $result;
			else
				return "NO";
	}
	/**get Poll details**/
	public function getPollDetails($searchParameters,$limit=NULL)
    {
        setlocale(LC_TIME, "fr_FR");
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;

        if($limit)
        	$limit_condition=" LIMIT 0,".$limit;

        if($searchParameters['profile_type']=='senior')
        {
            $condition=" AND (po.contributors=0 or po.contributors=2 or po.contributors=4) ";
        }
        else if($searchParameters['profile_type']=='junior')
        {
            $condition=" AND (po.contributors=1 or po.contributors=2 or po.contributors=4) ";
        }
        else if($searchParameters['profile_type']=='sub-junior')
        {
            $condition=" AND (po.contributors=3 or po.contributors=4) ";
        }
        if($searchParameters['pollid']!=NULL)
            $condition.=" and po.id='".$searchParameters['pollid']."'";

        if($searchParameters['poll_ids']!=NULL)
            $condition.=" and po.id in (".$searchParameters['poll_ids'].")";

       if(!$searchParameters['req_from'])
        $condition.=" and po.id not in( select poll_id from Poll_Participation pp where pp.user_id='".$userIdentifier."')";
        
        $participationJoin=" LEFT JOIN Poll_Participation pa ON pa.poll_id = po.id
                             LEFT JOIN  Client c  ON po.client=c.user_id  ";

         //differed AO publish time condition
        if($searchParameters['upcoming'])
            $publish_condition=" AND (po.publish_time > NOW()) ";
        else
            $publish_condition=" AND (po.publish_time < NOW()) ";

        if($poll_search_params['cnt_nextday'])
        {
           $publish_condition.=" AND DATE(po.publish_time)=CURDATE() + INTERVAL 1 DAY";
        }



        
		$pollQuery="SELECT count(DISTINCT pa.id ) as totalParticipation,po.total_article as totalArticles,po.client,c.company_name, 
					po.title,po.id as pollId,po.poll_date as delivery_date,po.category,po.type,po.language,po.price_min,
                    IF(po.contrib_percentage,(((po.price_max)*po.contrib_percentage)/100),po.price_max) as price_max,
					po.min_sign,po.max_sign,DATEDIFF(po.poll_date,NOW()) as remainingDays,po.poll_anonymous,po.poll_max,
					po.publish_time,po.client,IF(po.created_by='client','poll_nopremium','poll_premium') as ao_type,
                    po.priority_hours,po.file_name,po.production_time,po.contributors,po.currency
					FROM Poll po ".$participationJoin." 
					where po.valid_status='active'".$condition." AND  po.poll_date >=NOW() 
                    $publish_condition
					GROUP BY po.id ".$haveQuery." 
                    Having (po.poll_max>totalParticipation or po.poll_max='' or po.poll_max=0 )
					ORDER BY po.poll_date ASC,title ASC,pollId DESC $limit_condition";
    
        //echo  $pollQuery;exit;
        if(($count=$this->getNbRows($pollQuery))>0)
        {
            $PollDetails=$this->getQuery($pollQuery,true);
            return $PollDetails;
        }
        //else
          //  return NULL;
    }
    /*getting latest proposed price for a poll */
    public  function getLatestProposedPrice($pollId)
    {
        $Query="Select p.price_user From  Poll_Participation p
                Where p.poll_id='".$pollId."'
                ORDER BY p.created_at DESC LIMIT 1;
                ";
        //echo $Query;    exit;
        if(($result = $this->getQuery($Query,true))!= NULL)
            return $result[0]['price_user'];
        else
            return 0;
    }
    public function getPollsInCategory($poll_search_params,$limit=NULL)
    {
        setlocale(LC_TIME, "fr_FR");
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;

        if($limit)
            $limit_condition=" LIMIT 0,".$limit;

        if($poll_search_params['profile_type']=='senior')
        {
            $condition=" AND (po.contributors=0 or po.contributors=2 or po.contributors=4) ";
        }
        else if($poll_search_params['profile_type']=='junior')
        {
            $condition=" AND (po.contributors=1 or po.contributors=2 or po.contributors=4) ";
        }
        else if($poll_search_params['profile_type']=='sub-junior')
        {
            $condition=" AND (po.contributors=3 or po.contributors=4) ";
        }
   

        $condition.=" and po.id not in( select poll_id from Poll_Participation pp where pp.user_id='".$userIdentifier."')";
        $participationJoin=" LEFT JOIN Poll_Participation pa ON pa.poll_id = po.id 
                             LEFT JOIN  Client c  ON po.client=c.user_id ";
        
        $pollQuery="SELECT count(DISTINCT po.id ) as num_polls,po.category
                    FROM Poll po ".$participationJoin." 
                    where po.valid_status='active'".$condition." AND  po.poll_date >=NOW() 
                    GROUP BY po.category ";
                   
    
        //echo  $pollQuery;exit;
        if(($count=$this->getNbRows($pollQuery))>0)
        {
            $PollDetails=$this->getQuery($pollQuery,true);
            return $PollDetails;
        }
        //else
          //  return NULL;
    }

    //function to get Questions linked to Poll
    public  function getPollQuestions($pollId)
    {
        $questionQuery="Select * From  Poll_question p
                Where p.pollid='".$pollId."'
                LIMIT 4;
                ";
        //echo $Query;    exit;
        if(($questions = $this->getQuery($questionQuery,true))!= NULL)
            return $questions;
        else
            return false;
    }

}
 
