<?php
/**
 * EditPlaceController
 * This controller is responsible for viewing of Editplace site Contrib homepage *
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class UserController extends Ep_Controller_Action
{
    public function init()
	{
		parent::init();
	    $this->_view->livesite = $this->_config->www->baseurl;
		$this->_view->livesite_ssl = $this->_config->www->baseurlssl;
		$this->_view->lang = $this->_lang;
        $this->attachment_path=APP_PATH_ROOT.$this->_config->path->attachments;
		//print_r($_SERVER);
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
		if($this->EP_Contrib_reg->clientemail!='')
		$this->_view->client_email=strtolower($this->EP_Contrib_reg->clientemail);
         $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
         if($contrib_identifier!='')
         {
            $app_path=APP_PATH_ROOT;
            $profiledir=$this->_config->path->contrib_profile_pic_path.$contrib_identifier.'/';
             $pic=$contrib_identifier."_h.jpg";
             
             if(file_exists($app_path.$profiledir.$pic))
             {
                 $this->_view->contrib_home_picture="http://ep-test.edit-place.co.uk/FO/".$profiledir.$pic;
             }
             else
             {
                $this->_view->contrib_home_picture="http://ep-test.edit-place.co.uk/FO/images/Contrib/profile-img-def.png";
             }
             
         }
	}
	public function indexAction()
	{
		$this->render("index_home");//EPFO_HP
	}
	
	public function forgotpasswordAction()
	{
		$this->render("EP_User_Forgot_Password");
	}
	public function checkusermailexistAction()
	{
		$r_email_check=$this->_request->getParams();
        $res_obj = new EP_Contrib_Registration();
        $res= $res_obj->checkUserMailid($r_email_check['login_id']);
		echo $res;
		exit;
	}
	public function updateuserpwAction()
	{
			$arr=$this->_request->getParams();	
			$res_obj = new EP_Contrib_Registration();
			echo $res= $res_obj->updateUserPw($arr['user_id'],$arr['hash_key'],$arr['pw']);
			exit;
			
			
	}
	public function resetpwAction()
	{
		$arr=$this->_request->getParams();	
		$res_obj = new EP_Contrib_Registration();
		$val_res=$res_obj->validateresetlink($arr['username'],$arr['hash']);
		if($val_res=="No")
		{
			$this->_view->error_msg="D&eacute;sol&eacute;! La liaison a expiration.";
		}
		else
		{
			$this->_view->error_msg="";
		}
		$this->_view->hashkey=$arr['hash'];
		$this->_view->login_id=$arr['username'];
		$this->render("EP_User_Forgot_Password_Form");
	}
	
    /**UTF8 DECODE function work for msword character also**/
    public function utf8dec($s_String)
    {
            $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
            return substr($s_String, 0, strlen($s_String)-1);
    }
	public function uservalidationajaxAction()
	{
		$emailcheck_params_login=$this->_request->getParams();
		$obj = new Ep_User_User();	
		$res= $obj->checkUserMailidLogin($emailcheck_params_login['login_name'],$emailcheck_params_login['login_password']);
		
		$result=explode("@",$res);
		
		//update last visit
		if($res!="NO")
			$obj->updatevisit($result[0]);
			
		if($result[1]=='client')
		{	$username=$emailcheck_params_login['login_name'];
			$this->EPClient_reg = Zend_Registry::get('EPClient_reg');
			$this->EPClient_reg->clientidentifier =$result[0];
			$this->EPClient_reg->clientemail =$username;			
			
		}
		else if($result[1]=='contributor')
		{
			$username=$emailcheck_params_login['login_name'];
			$this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
			$this->EP_Contrib_reg->clientidentifier =$result[0];
			$this->EP_Contrib_reg->clientemail =strtolower($username);
		}
		
		//Insert UserLogins
		$userl_obj=new Ep_User_UserLogins();
			$userl_data=array("user_id"=>$result[0],"type"=>$result[1],"login_type"=>"manual","ip"=>$_SERVER['REMOTE_ADDR']);
		$userl_obj->InsertLogin($userl_data);	
		
		echo $result[1];
		exit;		
	}
    /**function for auto email login***/
    public function emailLoginAction()
    {
        $email_params_login=$this->_request->getParams();
        if($email_params_login['user'] && $email_params_login['hash'] && $email_params_login['type'])
        {
            $user_obj=new Ep_User_User();
            $encrypted_email=$email_params_login['user'];
            $encrypted_password=$email_params_login['hash'];
            $type=$email_params_login['type'];
            $details=$user_obj->checkEmailLoginDetails($encrypted_email,$encrypted_password,$type);
			
			//Zend_Session::destroy('EP_Contrib_reg');			
			//Zend_Session::destroy('EP_Client');
			
            if($details!="NO" && is_array($details))
            {
                if($details[0]['type']=='client')
                {
                
                    $this->EPClient_reg = Zend_Registry::get('EP_Client');
                    $this->EPClient_reg->clientidentifier =$details[0]['identifier'];
                    $this->EPClient_reg->clientemail =$details[0]['email'];
					$this->EPClient_reg->usertype=$details[0]['type'];
					
					//Insert UserLogins
					$userl_obj=new Ep_User_UserLogins();
						$userl_data=array("user_id"=>$details[0]['identifier'],"type"=>$details[0]['type'],"login_type"=>"link","ip"=>$_SERVER['REMOTE_ADDR']);
					$userl_obj->InsertLogin($userl_data);	
					
					if($email_params_login['redirectpage']=='home')
					{
						$this->_redirect("/client/home");
					}
					if($email_params_login['poll']=='1')
					{
						$this->_redirect("/client/pollclient");
					}
					else
					{
						$message=$email_params_login['message'];
						$ticket=$email_params_login['ticket'];
						$comment_article=$email_params_login['comment'];
						if($message!='' && $ticket!='')
						{
							$this->_redirect("/client/view-mail?type=inbox&message=".$message."&ticket=".$ticket);
						}
						else if($comment_article)
						{
							$this->_redirect("/client/quotes?id=".$comment_article); 
						}
						else
							$this->_redirect("/client/inbox");
					}
                }
                else if($details[0]['type']=='contributor')
                {
                    $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
                    $this->EP_Contrib_reg->clientidentifier =$details[0]['identifier'];
                    $this->EP_Contrib_reg->clientemail =strtolower($details[0]['email']);
                    
					//Insert UserLogins
					$userl_obj=new Ep_User_UserLogins();
						$userl_data=array("user_id"=>$details[0]['identifier'],"type"=>$details[0]['type'],"login_type"=>"link","ip"=>$_SERVER['REMOTE_ADDR']);
					$userl_obj->InsertLogin($userl_data);
					
					if($email_params_login['red_to']=='aosearch')
					{
						$this->_redirect("/contrib/aosearch");
					}
					
					if($email_params_login['poll']=='1')
					{
						$this->_redirect("/poll/participation");
					}
					elseif($email_params_login['nl']=='1')
					{
						$this->_redirect("/contrib/aosearch?user=".$encrypted_email."&user_id=".$email_params_login['user_id']."&mission_id=".$email_params_login['mission_id']);
					}
					else
					{
						$message=$email_params_login['message'];
						$ticket=$email_params_login['ticket'];
						if($message!='' && $ticket!='')
						{
							$this->_redirect("/contrib/view-mail?type=inbox&message=".$message."&ticket=".$ticket);
						}
						elseif($email_params_login['red_to'] != '')
                        {
                            if($email_params_login['parameters'] != '')
                                $this->_redirect("/contrib/".$email_params_login['red_to']."?".$email_params_login['parameters']);
                            else
                                $this->_redirect("/contrib/".$email_params_login['red_to']); 
                        }
						else
							$this->_redirect("/contrib/inbox");
					}	
                }
				
				
            }
            else
            {
                $this->_redirect("/");
            }
        }
        else
        {
            $this->_redirect("/");
        }
        
    }
	
 public function test1Action()
 {
	
	$this->render("test041212"); 	
 }
	
 public function showusersAction()
 {
	$obj = new Ep_User_User();
	$u=$obj->listUsersByType();
	$user = array();
	foreach ($u as $ua)
		$user[$ua['type']][]	=	$ua ;
	$mmm=$this->_arrayDb->loadArrayv2("anoop_categories", $this->_lang) ;
	$this->_view->user_details = $mmm ;
	$this->_view->u=$user;	//echo '<pre>';print_r($mmm);print_r($u);
	
	$this->render("showusersbytype"); 	
 }
 
 //Aler email unsubscribe
	public function alertUnsubscribeAction()
	{
				
		$user_obj = new Ep_User_User();
		
		$alert_params=$this->_request->getParams();
		$action=$alert_params['uaction'];
		$encrypted_email=$alert_params['user'];
		
		if($action=="unsubscribe" && $encrypted_email)
		{
			$user_details=$user_obj ->getAlertEmailUser($encrypted_email);
			if($user_details!="NO")
			{
				$user_id=$user_details[0]['identifier'];
				if($user_id)
				{
					$data['alert_subscribe']='no';
					$query="identifier='".$user_id."'";
					$user_obj->updateUser($data,$query);
				}	
			}	
		}
		
		if($action=="subscribe" && $encrypted_email)
		{
			$user_details=$user_obj ->getAlertEmailUser($encrypted_email);
			if($user_details!="NO")
			{
				$user_id=$user_details[0]['identifier'];
				if($user_id)
				{
					$data['alert_subscribe']='yes';
					$query="identifier='".$user_id."'";
					$user_obj->updateUser($data,$query);
				}	
			}
		}
		
		$this->render("user_alert_unsubscribe"); 	
	}
}
