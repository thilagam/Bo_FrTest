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
class Ep_User_BoUser extends Ep_Db_Identifier
{
	protected $_name = 'BoUser';
	private $user_id;
	private $email_personal;
	private $birth_date;
	private $birth_city;
	private $telephone;
    private $join_date;
    private $interview;
	private $workplace;
    private $work_telephone;
    private $yahoo_id;
    private $skype_id;
    private $contact_language;
    private $rib;
    private $ssn;
    private $ssn_file;
    private $computer_code;
    private $job_title;
    private $job_description;
    private $manager_incharge;
    private $bo_menuId;
    private $url;
    private $signature;
    private $status;
    private $webmail_password;

	public function loadData($array)
	{
		$this->user_id=$array["user_id"] ;
		$this->email_personal=$array["email_personal"];
		$this->birth_date=$array["birth_date"];
		$this->birth_city=$array["birth_city"] ;
		$this->telephone=$array["telephone"] ;
		$this->join_date=$array["join_date"] ;
		$this->interview=$array["interview"] ;
        $this->workplace=$array["workplace"] ;
        $this->work_telephone=$array["work_telephone"] ;
        $this->yahoo_id=$array["yahoo_id"] ;
        $this->skype_id=$array["skype_id"] ;
        $this->contact_language=$array["contact_language"] ;
        $this->rib=$array["rib"] ;
        $this->ssn=$array["ssn"] ;
        $this->ssn_file=$array["ssn_file"] ;
        $this->computer_code=$array["computer_code"] ;
        $this->job_title=$array["job_title"] ;
        $this->job_description=$array["job_description"] ;
        $this->manager_incharge=$array["manager_incharge"] ;
        $this->bo_menuId=$array["bo_menuId"] ;
        $this->url=$array["url"] ;
        $this->signature=$array["signature"] ;
        $this->status=$array["status"] ;
        $this->webmail_password=$array["webmail_password"] ;
        return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["user_id"] = $this->user_id;
		$array["email_personal"] = $this->email_personal;
        $array["birth_date"] = $this->birth_date;
		$array["birth_city"] = $this->birth_city;
		$array["telephone"] = $this->telephone;
		$array["join_date"] = $this->join_date;
		$array["interview"] = $this->interview;
		$array["workplace"] = $this->workplace;
        $array["work_telephone"] = $this->work_telephone;
        $array["yahoo_id"] = $this->yahoo_id;
        $array["skype_id"] = $this->skype_id;
        $array["contact_language"] = $this->contact_language;
        $array["rib"] = $this->rib;
        $array["ssn"] = $this->ssn;
        $array["ssn_file"] = $this->ssn_file;
        $array["computer_code"] = $this->computer_code;
        $array["job_title"] = $this->job_title;
        $array["job_description"] = $this->job_description;
        $array["manager_incharge"] = $this->manager_incharge;
        $array["bo_menuId"] = $this->bo_menuId;
        $array["url"] = $this->url;
        $array["signature"] = $this->signature;
        $array["status"] = $this->status;
        $array["webmail_password"] = $this->webmail_password;
        return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    /* Returns Articles Count for a given user Id */
    public function getBoUserExtraDetails($userId)
    {
         $query = "SELECT * FROM ".$this->_name." WHERE user_id = ".$userId ;
        if(($result=$this->getQuery($query,true))!=NULL)
            return $result;
        else
            return "NO";
    }
    /* Listing clients info including AO & profile info */
    public function ListStatsClientsinfo($sWhere, $sOrder, $sLimit, $condition)
    {
           $query="SELECT u.identifier,up.first_name,up.last_name,u.email,u.password,u.type,
                 u.profile_type,u.created_at,u.last_visit,u.sc_name, c.company_name,
                 (SELECT COUNT(id) AS aoCount FROM Delivery d WHERE d.user_id = u.identifier) AS ao_count,
                 (SELECT COUNT(id) AS artCount FROM Article a WHERE a.delivery_id IN (SELECT d.id FROM Delivery d WHERE d.user_id = u.identifier)) AS art_count,
                 (SELECT COUNT(a.id) AS pubartCount FROM Article a LEFT JOIN Participation p ON a.id=p.article_id WHERE a.delivery_id IN (SELECT d.id FROM Delivery d WHERE d.user_id = u.identifier) AND p.status='published') AS art_pcount
                    FROM User u
                    LEFT JOIN UserPlus up ON u.identifier = up.user_id
                    LEFT JOIN Client c ON c.user_id = u.identifier
                    WHERE u.type in ('client','superclient', 'sccontact')
                    AND u.status = 'Active' " . (!empty($condition) ? 'AND ' . $condition : '') . "
                    ".$sWhere." ".$sOrder." ".$sLimit."";
		//echo $query;  exit;
        /* Adding AO infos & date formatting */
        if(($result=$this->getQuery($query,true))!=NULL)
            return $result;
        else
            return "NO";

    }

    /////////////user details for edit user page based on user id////////////
     public function getUsersDetailsOnId($id)
    {
        $msg_query="select u.identifier, u.login, u.password, up.initial, up.first_name, up.last_name, up.city, up.state,
                up.address, up.zipcode, up.country, up.phone_number, u.type, u.status, u.last_visit, u.email, u.profile_type, u.profile_type2, u.type2
                from User u LEFT JOIN  UserPlus up ON up.user_id=u.identifier  where u.identifier=".$id;
      //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";

    }
    public function updateBoUser($data,$query)
    {
       // $where=" user_id='".$identifier."'";
        //echo $query;
       // print_r($data);exit;
        echo $this->updateQuery($data,$query);
    }
    ///////check the email is exits////
    public function getExistingIds($messenger, $ids, $userstatus, $userId)
    {  $ids = trim($ids);
        if($userstatus != 'new')
            $where = " AND user_id != ".$userId;
        else
            $where = " AND user_id = '' ";
        $query = "SELECT user_id FROM ".$this->_name." WHERE ".$messenger." = '".$ids."' $where  ";
        if(($result = $this->getQuery($query,false)) != NULL)
        {
            return "yes";
        }
        else
            return "no";
    }
    /* ***added on 12.01.2016 *** */
    public function updateBoUserAjax($data,$query)
    {
        return $this->updateQuery($data,$query);
    }

}

