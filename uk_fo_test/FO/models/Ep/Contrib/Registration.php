<?php
/**
 * Registration Model
 * This Model  is responsible for Registration actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Contrib_Registration extends Ep_Db_Identifier
{
	protected $_name = 'User';
	private $identifier;
	private $email;
	private $password;
	private $status;
    private $type='contributor';
	private $created_at;
	private $updated_at;
	private $last_visit;
    private $verification_code;
    private $verified_status ;
	private $reset_pw_verification_code;
    private $reset_pw_verified_status ;
    private $profile_type ;




	public function loadData($array)
	{
		$this->email=$array["email"] ;
		$this->password=$array["password"] ;
		$this->status=$array["status"] ;
		$this->created_at=$array["created_date"] ;
		$this->updated_at=$array["updated_date"] ;
		$this->last_visit=$array["last_visit"] ;
        $this->verification_code=$array["verification_code"] ;
        $this->verified_status=$array["verified_status"] ;
    	$this->reset_pw_verification_code=$array["reset_pw_verification_code"] ;
        $this->reset_pw_verified_status=$array["reset_pw_verified_status"] ;

		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["identifier"] = $this->getIdentifier();
		$array["email"] = $this->email;
		$array["password"] = $this->password;
		$array["status"] = $this->status;
		$array["type"] = $this->type;
        $array["created_at"] = $this->created_at;
		//$array["updated_at"] = $this->updated_at;
		$array["last_visit"] = $this->last_visit;
        $array["verification_code"] = $this->verification_code;
        $array["verified_status"] = $this->verified_status;
	    $array["reset_pw_verification_code"] = $this->reset_pw_verification_code;
        $array["reset_pw_verified_status"] = $this->reset_pw_verified_status;
        $array["profile_type"] = $this->profile_type;


		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }

	public function checkContribMailid($mailid)
	{
		$whereQuery = "email = '".$mailid."'";
		$query = "select * from ".$this->_name." where ".$whereQuery;
		if(($result = $this->getQuery($query,true)) != NULL)
			return "YES";
		else
			return "NO";
	}
    public function checkContribMailidLogin($mailid,$password)
	{
		$whereQuery = "email = '".$mailid."' and password='".$password."' and type='contributor' and verified_status='YES' and status='Active'";
		$query = "select * from ".$this->_name." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL)
			return "YES";
		else
			return "NO";
	}
    public function getContribMailidLogin($mailid,$password)
	{
		$whereQuery = "email = '".$mailid."' and password='".$password."' and type='contributor' and verified_status='YES' and status='Active'";
		$query = "select * from ".$this->_name." where ".$whereQuery;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result[0]["identifier"];
		else
			return "NO";
	}
    public function getLatestContributors($currentIdentifier)
    {
        $join=" LEFT JOIN UserPlus up ON u.identifier=up.user_id
                LEFT JOIN Contributor cp ON u.identifier =cp.user_id";
        $whereQuery=" where u.identifier !='".$currentIdentifier."' and u.Type='contributor' and u.status='Active' and u.verified_status='YES' order by u.created_at DESC limit 3";
        $getquery="select u.email,u.identifier ,CONCAT(first_name,' ',last_name) as username,cp.favourite_category from ".$this->_name." u".$join.$whereQuery;

        if(($count=$this->getNbRows($getquery))>0)
        {
            $latest_contributors=$this->getQuery($getquery,true);
            return $latest_contributors;
        }


    }
        
    public function getFavouriteCategory($identifier)
    {
        $query="select favourite_category from Contributor where user_id='".$identifier."'";
        

        if(($result = $this->getQuery($query,true)) != NULL)
		{
			$categories=explode(",",$result[0]['favourite_category']);
            $fav_category='';
            
            foreach($categories as $category)
            {
                $fav_category.="'".$category."',";
            }
            $fav_category=substr($fav_category,0,-1);

            return $fav_category;
		}
    }
    public function checkEmailHash($hash)
    {

        $data['verified_status']='YES';

        $where=" verification_code='".$hash."'";

        $return=$this->updateQuery($data,$where);
        if($return)
        {

		    $query = "select identifier,email from ".$this->_name." where ".$where;
		    if(($result = $this->getQuery($query,true)) != NULL)
            {
                return $result[0];
            }
        }
        else
            return NULL;

    }
///////////////////////////////////////////////////Coded By Rakesh :: Start //////////////////////////////////////////////////////
	public function checkUserMailid($mailid)
	{
		$whereQuery = "email = '".$mailid."'";
		$query = "select * from ".$this->_name." where ".$whereQuery;
		if(($result = $this->getQuery($query,true)) != NULL)
		{
			return $this->sendresetVerifyAccountMail(md5("edit-place_".$mailid),$mailid);
			return "Yes";
		}
		else
		{
			
			return "No";
		}
	}
	 public function sendresetVerifyAccountMail($verificationcode,$username)
    {
            
		$data['reset_pw_verification_code']=$verificationcode;

        $where=" email='".$username."'";

        $return=$this->updateQuery($data,$where);
		$body="
                    Bonjour, <br/><br/>

                    Pour r�initialiser votre mot de passe, cliquez sur le lien ci-dessous :<br/>
					<a href='http://ep-test.edit-place.co.uk/user/resetpw?hash=".$verificationcode."&username=".$username."'>R�initialiser mot de passe</a><br/><br/>                 
                    
                    Votre login: ".$username." <br/><br/> 
                    

                    Merci de votre confiance,<br/><br/>
                    Toute l��quipe d�Edit-place";

            $mail = new Zend_Mail();
            $mail->addHeader('Reply-To','support@edit-place.com');
            $mail->setBodyHtml($body)
                ->setFrom('support@edit-place.com')
                ->addTo($username)
                ->setSubject('Edit-place : changement de mot de passe');
            if($mail->send())
                return true;

    }
	public function updateUserPw($user_id,$hash_key,$pw)
    {
		$data['password']=$pw;
		$data['reset_pw_verification_code']=NULL;

        $where=" email='".$user_id."' and reset_pw_verification_code='".$hash_key."'";

        $return=$this->updateQuery($data,$where);
		if($return)
		{
			$body="
                    Bonjour,<br/><br/>

					Votre mot de passe a bien �t� r�initialis� !<br/>  
                    Login :".$user_id."<br/>                
                    Nouveau mot de passe : ".$pw." <br/><br/> 
                    
					Merci de votre confiance,<br/><br/>
                    Toute l��quipe d�Edit-place
                    ";

            $mail = new Zend_Mail();
            $mail->addHeader('Reply-To','support@edit-place.com');
            $mail->setBodyHtml($body)
                ->setFrom('support@edit-place.com')
                ->addTo($user_id)
                ->setSubject('confirmation de nouveau mot de passe');
            if($mail->send())
                return "Yes";
		}
		else
		{
			return "No";
		}
	}
	public function validateresetlink($username,$hash)
	{
		$whereQuery = "email = '".$username."' and reset_pw_verification_code='".$hash."'";
		$query = "select * from ".$this->_name." where ".$whereQuery;
		if(($result = $this->getQuery($query,true)) != NULL)
		{
			return "Yes";
		}
		else
		{
			
			return "No";
		}
	}
///////////////////////////////////////////////////Coded By Rakesh :: End //////////////////////////////////////////////////////

    public function getClientDetails($clientIdentifier)
    {
         $join=" LEFT JOIN UserPlus up ON u.identifier=up.user_id
                LEFT JOIN Client cp ON u.identifier =cp.user_id";
        $whereQuery=" where u.identifier ='".$clientIdentifier."' and u.Type='client'";
        $getquery="select u.email,u.identifier ,cp.company_name as username,CONCAT(first_name,' ',last_name) as username1
                    from ".$this->_name." u".$join.$whereQuery;

       if(($result = $this->getQuery($getquery,true)) != NULL)
        {
            return $result ;
        }
    }
     public function getUserInfo($profile_identifier)
    {
        $whereQuery = "identifier = '".$profile_identifier."'";
		$profileQuery = "select * from ".$this->_name." where ".$whereQuery;

		$result = $this->getQuery($profileQuery,true);
        return $result;
	}

    /**get all correctors/writers to send correction AO creation email**/
    public function getAOCorrectors($corrector_list,$article_id)
    {
        $corrector_list=explode(",",$corrector_list);
		 $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
		$user_language=$this->EP_Contrib_reg->user_language;

        $addQuery=' AND (';
        $cnt=0;
        foreach($corrector_list as $option){

            if($cnt>0)
                $addQuery.=" OR ";

              if($option=='CB')
                  $addQuery.="  (u.type2='corrector' AND (u.profile_type2='senior' OR u.profile_type2='junior' OR u.profile_type2='')) ";
              elseif($option=='CSC')
                  $addQuery.="  (u.type2='corrector' AND u.profile_type2='senior') ";
              elseif($option=='CJC')
                  $addQuery.="  (u.type2='corrector' AND (u.profile_type2='junior' OR u.profile_type2=''))";
              elseif($option=='WB')
                    $addQuery.=" (u.profile_type='senior' OR u.profile_type='junior') ";
              elseif($option=='WSC')
                    $addQuery.=" (u.profile_type='senior') ";
              elseif($option=='WJC')
                    $addQuery.=" (u.profile_type='junior') ";

              $cnt++;
        }
        $addQuery.=')';

        $whereQuery=" WHERE u.Type='contributor' and status='Active' AND c.language='".$user_language."'
					  AND u.identifier NOT IN (SELECT user_id FROM CorrectorParticipation WHERE article_id='".$article_id."')";
        $getQuery="select u.email,u.identifier,u.profile_type2,u.type2
                    from ".$this->_name." u
					JOIN Contributor c ON c.user_id=u.identifier					
					".$whereQuery.$addQuery;

        //echo $getQuery;exit;

        if(($result = $this->getQuery($getQuery,true)) != NULL)
        {
            return $result ;
        }
        else{
            return "NO";
        }
    }
	
	public function addContributor($contrib,$lang)
	{
		$this->_name='UserPlus';
		$Uparray=array();
		$Uparray['user_id']=$contrib;
		$this->insertQuery($Uparray);
		
		$this->_name='Contributor';
		$Conarray=array();
		$Conarray['user_id']=$contrib;
		$Conarray['language']=$lang;
		$this->insertQuery($Conarray);
	}
}
