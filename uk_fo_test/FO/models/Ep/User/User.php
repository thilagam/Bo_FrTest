<?php

class Ep_User_User extends Ep_Db_Identifier
{
	protected $_name = 'User';
	
	public function InsertUser($userarray)
	{
		$userarray['identifier']=$this->getIdentifier();
		$this->insertQuery($userarray);
		return $userarray['identifier'];
	}
	
	public function checkUserMailidLogin($mailid,$password)
	{
		//$whereQuery = "email = '".$mailid."' and password='".$password."' and verified_status='YES' and status='Active'";
		$whereQuery = "email = '".$mailid."' and password='".$password."' and status='Active'";
		$query = "select * from ".$this->_name." where ".$whereQuery;		
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result[0]["identifier"].'@'.$result[0]["type"];
		else
			return "NO";
	}
	
	public function updatevisit($iden)
	{
		$Uwhere=" identifier='".$iden."'";
		
		$Uarray=array();
		$Uarray['last_visit']=date("Y-m-d H:i:s");
		$Uarray['updated_at']=date("Y-m-d H:i:s");
		$this->updateQuery($Uarray,$Uwhere);
	}
	
	public function checkClientMailid($mailid)
	{
		$whereQuery = "email = '".$mailid."'";
		$query = "select * from ".$this->_name." where ".$whereQuery;
		
		if(($result = $this->getQuery($query,true)) != NULL)
			return "false";
		else
			return "true";
	}
	
	public function checkClientMailidLogin($mailid,$password)
	{
		//$whereQuery = "email = '".$mailid."' and password='".$password."' and verified_status='YES' and status='Active' and type='client'";
		$whereQuery = "email = '".$mailid."' and password='".$password."' and status='Active' and type='client'";
		$query = "select * from ".$this->_name." where ".$whereQuery;	
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result[0]["identifier"];
		else
			return "NO";
	}
	
	public function checkClientPassword($password,$client)
	{
		$whereQuery = "	password = '".$password."' AND identifier='".$client."' ";
		$query = "select * from ".$this->_name." where ".$whereQuery;
		
		if(($result = $this->getQuery($query,true)) != NULL)
			return "true";
		else
			return "false";
	}
	
	public function getUsername($user)
	{
		$query = "SELECT u.email,up.first_name,up.last_name FROM ".$this->_name." u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.identifier='".$user."'";	

		if(($result = $this->getQuery($query,true)) != NULL)
		{
			if($result[0]['first_name']!="")
				return $result[0]['first_name'];
			else
				return $result[0]['email'];
		}		
		else
			return "NO";
	}
	
	public function getClientdetails($client)
	{
		$Clientquery = "SELECT 
							u.identifier,up.first_name,up.last_name,u.email,u.password,u.alert_subscribe,up.phone_number,up.address,up.city,up.zipcode,up.country,c.company_name,
							c.rcs,c.vat,c.fax_number,c.logotype,c.twitterid							
						FROM 
							".$this->_name." u LEFT JOIN UserPlus up ON u.identifier=up.user_id 
							LEFT JOIN Client c ON c.user_id=u.identifier 
						WHERE 
							u.identifier='".$client."'";	

		if(($Clientresult = $this->getQuery($Clientquery,true)) != NULL)
			return $Clientresult;
		else
			return "NO";
	}
	
	public function verifyemail($mail)
	{
		$query = "SELECT identifier FROM ".$this->_name." WHERE email='".$mail."'";
		
		if(($result = $this->getQuery($query,true)) != NULL)
		{
			$verificationcode=md5("edit-place_".$mail);
			$data=array();
			$data['reset_pw_verification_code']=$verificationcode;
			$data['updated_at']=date("Y-m-d H:i:s");
			$where=" email='".$mail."'";
			$this->updateQuery($data,$where);
			return $verificationcode;
		}
		else
			return "NO";
		
	}
	
	public function validateresetlink($username,$hash)
	{
		$query = "SELECT identifier FROM ".$this->_name." WHERE  email = '".$username."' and reset_pw_verification_code='".$hash."'";
		if(($result = $this->getQuery($query,true)) != NULL)
			return "YES";
		else
			return "NO";
		
	}
	
	public function updateUserPw($user_id,$hash_key,$pw)
    {
		$data=array();
		$data['password']=$pw;
		$data['reset_pw_verification_code']=NULL;
		$data['updated_at']=date("Y-m-d H:i:s");
        $where=" email='".$user_id."' AND reset_pw_verification_code='".$hash_key."'";
//echo $hash_key."-".$where;exit;
        //$return=$this->updateQuery($data,$where);
		if(($result = $this->updateQuery($data,$where)) != NULL)
			return "YES";
		else
			return "NO";
	}
	
	public function journalcount()
	{
		$query = "SELECT count(*) as journals FROM ".$this->_name." WHERE  type = 'contributor' and profile_type IN ('senior','junior','sub-junior') AND status='Active'";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result[0]['journals'];
	}
	
	public function getClientname($clientid)
	{
		$query = "SELECT u.email,up.first_name,up.last_name FROM ".$this->_name." u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.identifier='".$clientid."'";	

		if(($result = $this->getQuery($query,true)) != NULL)
		{
			if($result[0]['first_name']!="")
			{
				$name=ucfirst(strtolower($result[0]['first_name'])).'&nbsp;'.ucfirst(strtolower($result[0]['last_name']));
				return $name;
			}
			else
				return $result[0]['email'];
		}		
		
	}
	
	public function Updatepassword($pass,$user)
	{
		$where="identifier='".$user."'";
		$arr['password']=$pass;
		$arr['updated_at']=date("Y-m-d H:i:s");
		$this->updateQuery($arr,$where);
	}
	
	 /** Check encrypted Email and Password Details and Auto Login in to Site ***/
    public function checkEmailLoginDetails($email,$password,$type)
    {
        $whereQuery = "md5(CONCAT('ep_login_',email)) = '".$email."' and md5(CONCAT('ep_login_',password))='".$password."' and status='Active' and type='".$type."'";
		$loginQuery = "select * from ".$this->_name." where ".$whereQuery;

        //echo $loginQuery;

		if(($result = $this->getQuery($loginQuery,true)) != NULL)
			return $result;
		else
			return "NO";
    }
	
	//Updating sub junior to Junior after on validating
	public function updatesubjunior($user)
	{
		$where="identifier='".$user."'";
		$CheckQuery = "SELECT profile_type FROM ".$this->_name." where ".$where;
		$resultstatus = $this->getQuery($CheckQuery,true);
		
		if($resultstatus[0]['profile_type']=='sub-junior')
		{
			$arr=array();
			$arr['profile_type']='junior';
			$arr['updated_at']=date("Y-m-d H:i:s");
			$this->updateQuery($arr,$where);
		}
	}
	
	public function getContributors()
	{
		$contribsQuery="SELECT u.identifier, u.email, u.password, u.type, u.profile_type, up.first_name, up.last_name, c.language
						FROM User u
						LEFT JOIN UserPlus up ON u.identifier = up.user_id
						LEFT JOIN Contributor c ON c.user_id=u.identifier
						WHERE u.type = 'contributor'
						AND u.status = 'Active'
						AND u.subscribe = 'yes'
						AND u.blackstatus = 'no'
						AND u.profile_type !=''"; 
						//AND u.identifier in ('111202081601320')";
		$contribsRessult = $this->getQuery($contribsQuery,true);
		return $contribsRessult;
	}
	
	public function getContributorDetail($id)
	{
		$contribsQuery="SELECT u.identifier, u.email, u.profile_type, up.first_name, up.last_name,u.password,u.type,u.*
						FROM User u
						LEFT JOIN UserPlus up ON u.identifier = up.user_id
						WHERE u.identifier='".$id."'";
		$contribsRessult = $this->getQuery($contribsQuery,true);
		return $contribsRessult;
	}
	////////////udate the user table//////////////////////
    public function updateUser($data,$query)
    {
        $data['updated_at']=date("Y-m-d H:i:s", time());
		$this->updateQuery($data,$query);
    }
	
	public function getGmailContributors()
	{
		$GcontribsQuery="SELECT u.identifier, u.email, u.password, u.type, u.profile_type, up.first_name, up.last_name
						FROM User u
						LEFT JOIN UserPlus up ON u.identifier = up.user_id
						WHERE u.type = 'contributor'
						AND u.status = 'Active'
						AND u.subscribe = 'yes'
						AND u.blackstatus = 'no'
						AND u.profile_type !=''
						AND u.identifier in (111201073158558)";//AND u.email LIKE '%gmail.com'
		$GcontribsRessult = $this->getQuery($GcontribsQuery,true);
		return $GcontribsRessult;
	}
	
	//get Alert email user id
	public function getAlertEmailUser($encryptedmail)
    {
        $whereQuery = "md5(CONCAT('ep_login_',email)) = '".$encryptedmail."'";
		$alertQuery = "select * from ".$this->_name." where ".$whereQuery;

        //echo $loginQuery;

		if(($result = $this->getQuery($alertQuery,true)) != NULL)
			return $result;
		else
			return "NO";
    }
	
	public function checkSuperClientLogin($mailid,$password)
	{
		$whereQuery = "email = '".$mailid."' and password='".$password."' and verified_status='YES' and status='Active' AND type IN ('superclient','chiefodigeo')";
		$query = "select * from ".$this->_name." where ".$whereQuery;	
		//echo $query;	
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
	
	public function getFrContributors()
	{
		$query = "SELECT u.email,c.language_more,c.language,u.identifier
					FROM User u
					INNER JOIN UserPlus up ON u.identifier = up.user_id
					INNER JOIN Contributor c ON u.identifier = c.user_id
					WHERE u.type = 'contributor'
					AND u.status = 'Active'
					AND alert_subscribe = 'yes'
					AND  (
					c.language = 'fr'
					OR c.language_more LIKE '%fr%'
					)";	
		
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
	
	public function getPrivateDelivaryList(){
		$sql="SELECT  a.contribs_list as contributer,a.id as article,p.id as participation, p.status as status ,
				u.identifier as user_id , d.created_user as createdBy , d.title as title , d.user_id as clientId ,
				d.id as delivaryId ,u.identifier as user_id , d.created_by as created_by_type ,     
			   a.title as articleTitle,a.junior_time as jt,a.senior_time as st,a.subjunior_time as sjt ,
			   u.profile_type as profileType
			   FROM Participation p 
			   INNER JOIN Article a ON a.id=p.article_id
			   INNER JOIN Delivery d ON a.delivery_id=d.id
			   INNER JOIN  User u ON p.user_id=u.identifier             
			   WHERE d.AOtype= 'private'
			   AND   p.status = 'bid_premium' 
			   AND p.cycle = '0'
			   AND  a.contribs_list NOT LIKE '%,%' 
			  ";//  AND ( p.status = 'bid_nonpremium' || p.status='bid_premium')
		if(($result = $this->getQuery($sql,true)) != NULL)
			return $result;
		else
			return "NO";	
	
	}
	
	public function getPrivateCorrectionList(){
		$sqlcorr="SELECT  a.corrector_privatelist as corrector,a.id as article,cp.id as participation, cp.status as status ,
				u.identifier as user_id , d.created_user as createdBy , d.title as title , d.user_id as clientId ,
				d.id as delivaryId ,u.identifier as user_id , 
			   a.title as articleTitle,a.correction_jc_submission as jt,a.correction_sc_submission as st,a.correction_jc_resubmission as jrt,a.correction_sc_resubmission as srt,a.correction_resubmit_option,
			   u.profile_type2 as profileType,a.participation_time,a.senior_time,a.junior_time,a.subjunior_time
			   FROM CorrectorParticipation cp 
			   INNER JOIN Article a ON a.id=cp.article_id
			   INNER JOIN Delivery d ON a.delivery_id=d.id
			   INNER JOIN  User u ON cp.corrector_id=u.identifier             
			   WHERE d.correction_type= 'private' AND d.created_by='BO' 
			   AND  cp.status = 'bid_corrector' 
			   AND cp.cycle = '0'
			   AND  a.corrector_privatelist NOT LIKE '%,%'";
		if(($resultcorr = $this->getQuery($sqlcorr,true)) != NULL)
			return $resultcorr;
		else
			return "NO";	
	
	}
	
	public function getEmailUser($user)
	{
		$query="SELECT email,login,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE identifier='".$user."'";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";	
	}
	
	public function getSeniorContributors()
    {
        $query="select identifier FROM ".$this->_name." WHERE profile_type ='senior' AND status = 'Active'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function getJobs() 
	{
		$jobQuery = "SELECT * FROM Jobs WHERE status='active' ORDER BY id desc";
        $jobDetails = $this->getQuery($jobQuery, true);
        return $jobDetails;
    }
	
	public function getTheteam() 
	{
		$teamQuery = "SELECT * FROM Theteam WHERE status='active'";
        $teamDetails = $this->getQuery($teamQuery, true);
        return $teamDetails;
    }
	
	public function getPartners() 
	{
		$PartQuery = "SELECT * FROM Partners WHERE status='active'";
        $PartDetails = $this->getQuery($PartQuery, true);
        return $PartDetails;
    }
	
	public function getReferences() 
	{
		$RefQuery = "SELECT * FROM `References` WHERE status='active'";
        $RefDetails = $this->getQuery($RefQuery, true);
        return $RefDetails;
    }
    /* added by naseer on 10-11-2015 */
    //fetch the user type//
	public function getUserType($user_id)
	{
		$query = "SELECT `type` FROM `User` WHERE `identifier`='$user_id'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result[0]['type'];
        else
            return "NO";
    }

    /* end of added by naseer on 10-11-2015 */
	
	/*cron function to get black list contributor with out _deleted in their emails*/
	function getBlacklistWriters()
	{
		$blQuery="SELECT * FROM `User` WHERE `blackstatus`='yes' AND type='contributor' AND email NOT LIKE '%_deleted%' AND email!=''";
		//echo $blQuery;exit;
		if(($users = $this->getQuery($blQuery,true)) != NULL)
            return $users;
        else
            return NULL;
	}
    /* *** added on 17.02.2016 *** */ //////////get all user details
    public function getAllUsersDetails($userid)
    {
        $query = "SELECT u.identifier, u.login, u.profile_type, u.profile_type2, u.type2, u.email, up.first_name, up.last_name, u.menuId,
	 	             up.country, u.groupId, up.city,up.phone_number	FROM " . $this->_name . " u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.identifier = " . $userid;
        //echo $query."<br>";//exit;
        if (($result = $this->getQuery($query, true)) != NULL) {
            return $result;
        } else
            return false;
    }
    public function getUserDetails($userid)
    {
        $query = "SELECT u.email,up.first_name,up.last_name FROM ".$this->_name." u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.identifier='".$userid."'";
        //echo $query."<br>";//exit;
        if (($result = $this->getQuery($query, true)) != NULL) {
            return $result;
        } else
            return false;
    }
    /* *** end of added on 17.02.2016 *** */

}

