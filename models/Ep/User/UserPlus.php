<?php
/**
 * Ep_User_UserPlus
 * @author admin
 * @package Ticket
 * @version 1.0
 */
 /*Status
        0 => sent by sender / received by recipient
        1 => received by sender / sent by recipient
        2 => classified by sender
        3 => classified by recipient
*/
class Ep_User_UserPlus extends Ep_Db_Identifier
{
	protected $_name = 'UserPlus';
	private $user_id;
	private $initial;
	private $first_name;
	private $last_name;
	private $address;
    private $city;
    private $state;
	private $zipcode;
    private $country;
    private $phone_number;

	public function loadData($array)
	{
		$this->user_id=$array["user_id"] ;
		$this->initial=$array["initial"];
		$this->first_name=$array["first_name"];
		$this->last_name=$array["last_name"] ;
		$this->address=$array["address"] ;
		$this->city=$array["city"] ;
		$this->state=$array["state"] ;
        $this->zipcode=$array["zipcode"] ;
        $this->country=$array["country"] ;
        $this->phone_number=$array["phone_number"] ;
        return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["user_id"] = $this->user_id;
		$array["initial"] = $this->initial;
        $array["first_name"] = $this->first_name;
		$array["last_name"] = $this->last_name;
		$array["address"] = $this->address;
		$array["city"] = $this->city;
		$array["state"] = $this->state;
		$array["zipcode"] = $this->zipcode;
        $array["country"] = $this->country;
        $array["phone_number"] = $this->phone_number;
        return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    ///////user details for user grid page//////////////
    public function getUsersDetailsdd()
    {
          $msg_query="select u.identifier, up.first_name, up.zipcode, up.country, up.phone_number, u.type, u.status, u.last_visit, u.email, u.created_at, u.profile_type from  User u LEFT JOIN
   UserPlus up ON up.user_id=u.identifier where 1=1 ORDER BY u.created_at DESC";
       //   echo $msg_query;//exit;
       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }
     ///////user details for user grid page on search//////////////
    public function getUsersDetails()
    {
          $msg_query="select u.identifier, up.first_name, up.last_name, up.zipcode, up.country, up.phone_number, u.type, u.groupId, u.status,
        u.last_visit, u.email, u.created_at, u.profile_type from
           User u LEFT JOIN UserPlus up ON up.user_id=u.identifier  where u.type NOT IN ('contributor','client') ORDER BY u.created_at DESC";  // exit;
            ///echo $msg_query;exit;
       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }
    ///////user details for user grid page on search//////////////
    public function getContributorsDetails($where)
    {
        $msg_query="select u.identifier, up.user_id, up.first_name, up.last_name, up.phone_number, u.email, u.password, u.created_at, u.type,
                    u.profile_type, c.language, c.favourite_category, c.category_more from  User u
            LEFT JOIN UserPlus up ON up.user_id=u.identifier
            LEFT JOIN Contributor c ON c.user_id=u.identifier where u.type = 'contributor'  ORDER BY u.created_at DESC";
        //  echo $msg_query;//exit;
        if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
        else
            return "No Messages Found";
    }
    ///////user details for user grid page on search//////////////
    public function getClientDetails($where)
    {
        $msg_query="select u.identifier, up.first_name, up.last_name, up.phone_number, u.email, u.password, u.created_at, u.type, up.address, cl.company_name  from  User u
                LEFT JOIN UserPlus up ON up.user_id = u.identifier
                LEFT JOIN Client cl ON cl.user_id = u.identifier where u.type = 'client' AND u.status = 'Active' ORDER BY u.created_at DESC";
        //  echo $msg_query;//exit;
        if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
        else
            return "No Messages Found";
    }
    /* Listing clients info including AO & profile info */
    public function ListStatsClientsinfoold($condition)
    {
        $query="SELECT u.identifier,up.first_name,up.last_name,u.email,u.password,
                 u.profile_type,u.created_at,u.last_visit,c.company_name
                    FROM User u
                    LEFT JOIN UserPlus up ON u.identifier = up.user_id
                    LEFT JOIN Client c ON c.user_id = u.identifier
                    WHERE u.type = 'client'
                    AND u.status = 'Active' " . (!empty($condition) ? 'AND ' . $condition : '') . "
                    ORDER BY u.updated_at DESC";
        /* Adding AO infos & date formatting */
        foreach ( $this->getQuery($query,true) as $client ) :
            $aoInfo =   $this->clientAoCount($client['identifier']) ;
            $artInfo =   $this->clientArticleCount($client['identifier'], 0) ;
            $client['art_count']       =   $artInfo['art_count'] ;
            $artInfo =   $this->clientArticleCount($client['identifier'], 1) ;
            $client['art_pcount']      =   $artInfo['art_count'] ;
            $client['ao_count']        =   $aoInfo['ao_count'] ;
            $client['ao_id']           =   $aoInfo['ao_id'] ;
            $client['created_at']      =   $client['created_at'];
            $client['last_visit']      =   date( "d/m/Y" , strtotime( $client['last_visit'] ) ) ;
		    $result[]   =   $client ;
        endforeach ;
		//print_r($result); 
        return $result;
    }
    /* Returns AO Count for a given user Id */
    public function clientAoCount($user_id)
    {
        $query="SELECT id AS ao_id, COUNT(*) AS ao_count, title AS company_name
                    FROM Delivery
                    WHERE user_id = '" . $user_id . "'";
        $result = $this->getQuery($query,true);
        return $result[0];
    }

    /* Returns Articles Count for a given user Id */
    public function clientArticleCount($userId, $published)
    {
        if($published)
            $query  =   "SELECT COUNT(a.id) AS art_count FROM Article a LEFT JOIN Participation p ON a.id=p.article_id WHERE a.delivery_id IN( SELECT id FROM Delivery WHERE user_id='" . $userId . "') AND p.status='published'" ;
        else
            $query  =   "SELECT COUNT(id) AS art_count FROM Article WHERE delivery_id IN( SELECT id FROM Delivery WHERE user_id='" . $userId . "')" ;
        $result = $this->getQuery($query,true);
        return $result[0];
    }
    /* Listing clients info including AO & profile info */
    public function ListStatsClientsinfo($sWhere, $sOrder, $sLimit, $condition)
    {
           $query="SELECT u.identifier,up.first_name,up.last_name,u.email,u.password,u.type,
                 u.profile_type,u.created_at,u.last_visit,u.created_by,u.created_user,c.company_name,
                 (SELECT COUNT(id) AS aoCount FROM Delivery d WHERE d.user_id = u.identifier) AS ao_count,
                 (SELECT COUNT(id) AS artCount FROM Article a WHERE a.delivery_id IN (SELECT d.id FROM Delivery d WHERE d.user_id = u.identifier)) AS art_count,
                 (SELECT COUNT(a.id) AS pubartCount FROM Article a LEFT JOIN Participation p ON a.id=p.article_id WHERE a.delivery_id IN (SELECT d.id FROM Delivery d WHERE d.user_id = u.identifier) AND p.status='published') AS art_pcount,
				 (SELECT COUNT(identifier) AS clientcontact FROM ClientContacts cc WHERE cc.client_id = u.identifier) AS clientcontact
                    FROM User u
                    LEFT JOIN UserPlus up ON u.identifier = up.user_id
                    LEFT JOIN Client c ON c.user_id = u.identifier
                    WHERE u.type in ('client','superclient', 'sccontact')
                    AND u.status = 'Active' " . (!empty($condition) ? 'AND ' . $condition : '') . "
                    ".$sWhere." ".$sOrder." ".$sLimit."";
                //return $query;
		//echo $query;  exit;
        /* Adding AO infos & date formatting */
        if(($result=$this->getQuery($query,true))!=NULL)
            return $result;
        else
            return "NO";

        /*foreach ( $this->getQuery($query,true) as $client ) :
            $result[]   =   $client ;
        endforeach ;
          print_r($result);     exit;
      //  return $result;*/
    }
    /* Returns AO Count for a given user Id */
    public function clientAoCountOptimise($clientIds)
    {
        $query="SELECT id AS ao_id, COUNT(id) AS ao_count, user_id title AS company_name
                    FROM Delivery
                    WHERE user_id IN  ('" . $clientIds . "')";
        $result = $this->getQuery($query,true);
        return $result[0];
    }
    /////////////user details for edit user page based on user id////////////
     public function getUsersDetailsOnId($id)
    {
        $msg_query="select u.identifier, u.login, u.password, u.menuId, up.initial, up.first_name, up.last_name, up.city, up.state, up.address, up.zipcode,
                 up.country, up.phone_number, u.type, u.status as usrStatus, u.last_visit, u.email,u.parentId, u.profile_type, u.profile_type2, u.type2, bu.*
                 FROM User u LEFT JOIN  UserPlus up ON up.user_id=u.identifier
                 LEFT JOIN  BoUser bu ON bu.user_id=u.identifier
                  where u.identifier=".$id;
      //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";

    }
    /////////////contributor  details for tooltip in profile list page based on article id////////////
    public function getUserstooltipdetails($id,$artId)
    {
        $msg_query="select u.identifier, u.login, u.password, up.initial, up.first_name, up.last_name, up.city,
                 u.type, u.status, u.last_visit, u.email, u.profile_type, p.status, p.price_user from User u
                 LEFT JOIN  UserPlus up ON up.user_id=u.identifier
                 LEFT JOIN Participation p ON u.identifier=p.user_id
                where u.identifier=".$id." and p.article_id=".$artId." ORDER BY p.status='published' desc,p.status='under_study' desc, p.status='on_hold' desc,p.status='bid' desc, p.status='bid_premium' desc, p.status='bid_refused' desc, p.status='disapproved' desc,p.status='closed' desc";
      //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }
    /////////////corrector  details for tooltip in profile list page based on article id////////////
    public function getCorrectortooltipdetails($id,$artId)
    {
        $msg_query="select u.identifier, u.login, u.password, up.initial, up.first_name, up.last_name, up.city,
                 u.type, u.status, u.last_visit, u.email, u.profile_type, u.profile_type2, u.type2, cp.status, cp.price_corrector from User u
                 LEFT JOIN  UserPlus up ON up.user_id=u.identifier
                 LEFT JOIN CorrectorParticipation cp ON u.identifier=cp.corrector_id
                where u.identifier=".$id." and cp.article_id=".$artId." ORDER BY cp.status='published' desc,cp.status='under_study' desc, cp.status='on_hold' desc,cp.status='bid' desc, cp.status='bid_corrector' desc, cp.status='bid_refused' desc, cp.status='disapproved' desc,cp.status='closed' desc";
        //echo $msg_query;exit;

        if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
        else
            return "No Messages Found";
    }
    /////////////contributor  details for tooltip in stats contributor page mouse over on contributor name////////////
    public function getContributorstooltipdetails($id)
    {
        $msg_query="select u.identifier, u.login, u.password, up.initial, up.first_name, up.last_name, up.city,
                 u.type, u.status, u.last_visit, u.email, u.profile_type, u.last_visit, u.created_at from User u
                 LEFT JOIN  UserPlus up ON up.user_id=u.identifier where u.identifier=".$id."  ";
        $published = "select count(id) AS published from Participation where user_id = '".$id."' AND status='published'";
        $no_disapproved = "select count(id) AS no_disapproved from Participation where user_id = '".$id."' AND status IN ('bid_refused')";
        $no_paritcipations = "select count(user_id) AS no_paritcipations from Participation where user_id = '".$id."'";
        $not_validated = "select count(user_id) AS not_validated from Participation where user_id = '".$id."' AND status IN ('bid_premium', 'bid_refused', 'bid_nonpremium', 'bid_temp', 'bid_refused_temp' )";
        $validated = "select count(user_id) AS validated from Participation where user_id = '".$id."' AND status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold')";
        $closed = "select count(user_id) AS closed from Participation where user_id = '".$id."' AND status='closed'";
        $sent = "select count(user_id) AS sent from Participation where user_id = '".$id."' AND status IN ('under_study','disapproved', 'published')";
        $last_participated = "select max(created_at) AS last_participated from Participation where user_id = '".$id."' ";
        $last_validated = "select max(accept_refuse_at) AS last_validated from Participation where user_id = '".$id."' AND status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold')";

		$result = $this->getQuery($msg_query,true);
		$result2 = $this->getQuery($published,true);
        $result3 = $this->getQuery($no_disapproved,true);
        $result4 = $this->getQuery($no_paritcipations,true);
        $result5 = $this->getQuery($not_validated,true);
        $result6 = $this->getQuery($validated,true);
        $result7 = $this->getQuery($closed,true);
        $result8 = $this->getQuery($sent,true);
        $result9 = $this->getQuery($last_participated,true);
        $result10 = $this->getQuery($last_validated,true);
        $result11=array_merge($result, $result2, $result3, $result4, $result5, $result6, $result7, $result8, $result9, $result10);
        return $result11;
    }
    /////////////contributor details for comparing contributors ////////////
    public function getCompareContributors($id)
    {
        $msg_query="select u.identifier, u.login, u.password, up.initial, up.first_name, up.last_name, up.city, u.type, u.status,
                  u.last_visit, u.email, u.profile_type, u.last_visit, u.created_at, u.blackstatus, cb.favourite_category,
                  cb.dob, cb.language, cb.language_more, up.address, up.country, u.last_visit from User u
                 LEFT JOIN  UserPlus up ON up.user_id=u.identifier
                 LEFT JOIN Contributor cb ON u.identifier=cb.user_id WHERE  u.identifier=".$id."  ";
        $published = "select count(id) AS published from Participation where user_id = '".$id."' AND status='published'";
        $no_disapproved = "select count(id) AS no_disapproved from Participation where user_id = '".$id."' AND status IN ('bid_refused')";
        $no_paritcipations = "select count(user_id) AS no_paritcipations from Participation where user_id = '".$id."'";
        $not_validated = "select count(user_id) AS not_validated from Participation where user_id = '".$id."' AND status IN ('bid_premium', 'bid_refused', 'bid_nonpremium', 'bid_temp', 'bid_refused_temp' )";
        $validated = "select count(user_id) AS validated from Participation where user_id = '".$id."' AND status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold')";
        $closed = "select count(user_id) AS closed from Participation where user_id = '".$id."' AND status='closed'";
        $sent = "select count(user_id) AS sent from Participation where user_id = '".$id."' AND status IN ('under_study','disapproved', 'published')";
        $last_participated = "select max(created_at) AS last_participated from Participation where user_id = '".$id."' ";
        $last_published = "select max(created_at) AS last_published from Participation where user_id = '".$id."' AND status = 'published'";
        $royalties = "select sum(price) AS royalties_earned from Royalties where user_id = '".$id."' ";

        $result = $this->getQuery($msg_query,true);
        $result2 = $this->getQuery($published,true);
        $result3 = $this->getQuery($no_disapproved,true);
        $result4 = $this->getQuery($no_paritcipations,true);
        $result5 = $this->getQuery($not_validated,true);
        $result6 = $this->getQuery($validated,true);
        $result7 = $this->getQuery($closed,true);
        $result8 = $this->getQuery($sent,true);
        $result9 = $this->getQuery($last_participated,true);
        $result10 = $this->getQuery($last_published,true);
        $result11 = $this->getQuery($royalties,true);
        $result12=array_merge($result, $result2, $result3, $result4, $result5, $result6, $result7, $result8, $result9, $result10, $result11);
        return $result12;
    }
    ////////get the contributors who are suited for the criteria selected in statistics search contributor //////////
    public function getContibsIdsForSearch($whereQuery)
    {
        $existsQuery = "SELECT user_id FROM Participation ".$whereQuery;
        if(($result = $this->getQuery($existsQuery,true)) != NULL)
        {
            $countIds =  count($result);
            for($i=0; $i<$countIds; $i++)
            {
                $ids[$i] = $result[$i]['user_id'];
            }
            return $idstring = implode(',',$ids);
        }
        else
            return 0;
    }
    ///////get contributor list who involved in particular ao//////////////
    public function getContribsInAo($aoid)
    {
         $msg_query="select distinct p.user_id from  Participation p
		    INNER JOIN Article a ON a.id = p.article_id
		    INNER JOIN Delivery d ON d.id = a.delivery_id
	        where d.id='".$aoid."'";
        if(($result = $this->getQuery($msg_query,true)) != NULL)
        {
            $countIds =  count($result);
            for($i=0; $i<$countIds; $i++)
            {
                $ids[$i] = $result[$i]['user_id'];
            }
            return $idstring = implode(',',$ids);
        }
        else
            return 0;
    }
    /////////////contributor  list of status contributor////////////
    public function getStatsContributorsCount()
    {
        $profileQuery1 = "select count(identifier) AS never_participated FROM User WHERE  status='active' AND blackstatus='no'  AND type='contributor' and identifier not in (select distinct user_id from Participation)";
        $profileQuery2 = "select count(identifier) AS never_sent from User where  status='active' AND blackstatus='no'  AND type='contributor' and identifier IN (select distinct user_id from Participation where  status NOT IN ('closed','disapproved','published', 'under_study', 'disapprove_client','closed_client') )";

        /*edited by naseer on 15-09-2015*/
        /*$profileQuery3 = "select count(identifier) AS never_validated from User where type='contributor' and identifier IN (select distinct user_id from Participation where  status IN ('bid_premium', 'bid_refused', 'bid_nonpremium', 'bid_temp', 'bid_refused_temp' ))
                            and identifier NOT IN (select distinct user_id from Participation where  status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold'))"; */
        $profileQuery3 = "select count(identifier) AS never_validated from User where  status='active' AND blackstatus='no'  AND type='contributor' and
                            identifier IN (select distinct user_id from Participation where  status NOT IN
                            ('bid','under_study','time_out','validated','published','refused','ignored','disapproved','approved','closed','on_hold','bid_temp','bid_refused_temp','bid_nonpremium_timeout','bid_premium_timeout','disapproved_temp','closed_temp','plag_exec','closed_client_temp','disapprove_client','closed_client')
        )";
        /*end of edited by naseer on 15-09-2015*/
        $profileQuery4 = "select count(identifier) AS once_validated from User where  status='active' AND blackstatus='no'  AND type='contributor' and identifier  IN (select distinct user_id from Participation where  status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold'))";
        $profileQuery5 = "select count(identifier) AS total_contribs FROM User WHERE  status='active' AND blackstatus='no' AND type='contributor'";
        $profileQuery6 = "select count(identifier) AS once_published FROM User WHERE  status='active' AND blackstatus='no' AND type='contributor' and identifier  IN (select distinct user_id from Participation where  status IN ('published'))";

		$result1 = $this->getQuery($profileQuery1,true);
		$result2 = $this->getQuery($profileQuery2,true);
        $result3 = $this->getQuery($profileQuery3,true);
        $result4 = $this->getQuery($profileQuery4,true);
        $result5 = $this->getQuery($profileQuery5,true);
        $result6 = $this->getQuery($profileQuery6,true);

        $result=array_merge($result1, $result2, $result3, $result4, $result5, $result6);
        return $result;
    }
    public function checkProfileExist($identifier)
    {
        $whereQuery = "u.user_id = '".$identifier."'";
		$existsQuery = "select u.user_id,first_name,payment_type from ".$this->_name." u
		                LEFT JOIN Contributor c ON u.user_id=c.user_id
		                where ".$whereQuery;

		if(($result = $this->getQuery($existsQuery,true)) != NULL)
			return $result;//[0]["user_id"];
		else
			return "NO";

    }
    public function getProfileInfo($profile_identifier)
    {
        $whereQuery = "user_id = '".$profile_identifier."'";
		$profileQuery = "select * from ".$this->_name." where ".$whereQuery;

		$result = $this->getQuery($profileQuery,true);
        return $result;
	}
     public function updateUserPlus($data,$query)
    {
        //$where=" user_id='".$identifier."'";
       // print_r($data);exit;
        echo $this->updateQuery($data,$query);
    }
	
	public function getUsersName($user_id)
    {
	$query	=	"SELECT login FROM User WHERE identifier='".$user_id."'" ;

       if(($result=$this->getQuery($query,true))!=NULL)
            return $result;
    }
    /* *** added on 12.01.2015*** */

    public function updateUserPlusAjax($data,$query)
    {
        return $this->updateQuery($data,$query);
    }
}

