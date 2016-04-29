<?php
/**
 * Ep_Message_AutoEmails
 * @author Arun
 * @package Message
 * @version 1.0
 */
class Ep_Ticket_AutoEmails extends Ep_Db_Identifier
{
	function getUserDetails($user)
	{
		//IF(u.type='client',company_name,CONCAT(first_name,' ',UPPER(SUBSTRING(last_name, 1,1))))
        $senderQuery="select IF(u.type='client',company_name,first_name) as username ,email,type from User u
		                    LEFT JOIN UserPlus up ON u.identifier=up.user_id
		                    LEFT JOIN Client c ON u.identifier=c.user_id
		                    where identifier='".$user."'";
		//echo $senderQuery;exit;
		if(($result=$this->getQuery($senderQuery,true))!=NULL)
		{
			if(!$result[0]['username'])
            {
                        $email=explode("@",$result[0]['email']);
                        $result[0]['username']=$email[0];                
            }

            return $result;
		}

	}
    /* *** added on 17.02.2016 *** */
    function getContribUserDetails($user)
    {
        $senderQuery="select first_name as firstname , UPPER(LEFT(last_name,1)) as lastname, email from User u
		                    LEFT JOIN UserPlus up ON u.identifier=up.user_id
		                    LEFT JOIN Client c ON u.identifier=c.user_id
		                    where identifier='".$user."'";
        //echo $senderQuery;exit;
        if(($result=$this->getQuery($senderQuery,true))!=NULL)
        {
            return $result;
        }

    }

	function getUserEmailId($user){
		//echo $user;
		 $typeQuery="select identifier,email from User u where identifier='".$user."'";
		//echo $senderQuery;exit;
		if(($result=$this->getQuery($typeQuery,true))!=NULL)
		{	//print_r($result);
			$email=$result[0];
			return $email;
		}
	}
	function getUserType($user)
    {
        $typeQuery="select * from User u where identifier='".$user."'";
		//echo $senderQuery;exit;
		if(($result=$this->getQuery($typeQuery,true))!=NULL)
		{
			return $result;
		}
    }
    function getAutoEmail($id)
    {
        $query="select * from AutoemailsNewversion where Id=".$id;
        if(($result=$this->getQuery($query,true))!=NULL)
         {
             return $result;
         }

    }
    public function sendAutoEmail($to,$mailid,$parameters)
    {
        
        $AO_Creation_Date='<b>'.$parameters['created_date'].'</b>';
        $link='<a href="http://'.$_SERVER['HTTP_HOST'].$parameters['document_link'].'">Click here</a>';
        $AO_title='<b>'.stripslashes($parameters['AO_title']).'</b>';
        $AO_end_date='<b>'.$parameters['AO_end_date'].'</b>';
        $verifylink=$parameters['verify_link'];
        $sitelink='<a href="http://www.edit-place.co.uk">www.edit-place.co.uk</a>';
        $invoicelink='<a href="http://'.$_SERVER['HTTP_HOST'].'/client/invoice">ici</a>';
        if($parameters['invoice_link'])
            $invoicelink='<a href="http://'.$_SERVER['HTTP_HOST'].'/client/invoice">cliquant ici</a>';
        $homelink='<a href="http://'.$_SERVER['HTTP_HOST'].'/client/home">Click here</a>';



        $email=$this->getAutoEmail($mailid);

        $Object=html_entity_decode($email[0]['Object']);

        $Message=stripslashes($email[0]['Message']);
        eval("\$Message= \"$Message\";");

            $mail = new Zend_Mail();
            $mail->addHeader('Reply-To','support@edit-place.com');
            $mail->setBodyHtml($Message)
                ->setFrom('support@edit-place.com','Support Edit-place')
                ->addTo($to)
                ->setSubject($Object);
           // if($mail->send())
            //    return true;

        //print_r($mail);exit;
    }
    /**Send mail to EP mail box**/
    public function messageToEPMail($receiverId,$mailid,$parameters,$personal=NULL)
    {

        $AO_Creation_Date='<b>'.$parameters['created_date'].'</b>';
        $link='<a href="http://'.$_SERVER['HTTP_HOST'].$parameters['document_link'].'">Click here</a>';
        $contributor='<b>'.$parameters['contributor_name'].'</b>';
        $bo_user='<b>'.$parameters['bo_user'].'</b>';
        $corrector='<b>'.$parameters['corrector_name'].'</b>';
        $AO_title="<b>".$parameters['AO_title']."</b>";
        $submitdate_bo="<b>".$parameters['submitdate_bo']."</b>";
        $total_articles="<b>".$parameters['noofarts']."</b>";
        $invoicelink='<a href="http://'.$_SERVER['HTTP_HOST'].$parameters['invoice_link'].'">Click here</a>';
        $article_link='<a href="'.$parameters['article_link'].'">Click here</a>';
        $articlewithlink='<a href="'.$parameters['articlename_link'].'">'.stripslashes($parameters['article_title']).' </a>';
        $client='<b>'.$parameters['client_name'].'</b>';
        $royalty='<b>'.$parameters['royalty'].'</b>';
        $ongoinglink='<a href="http://'.$_SERVER['HTTP_HOST'].$parameters['ongoinglink'].'">Click here</a>';
        $ongoinglink1='<a href="http://'.$_SERVER['HTTP_HOST'].$parameters['ongoinglink'].'">Click here</a>';

        $AO_end_date='<b>'.$parameters['AO_end_date'].'</b>';
        $article='<b>'.stripslashes($parameters['article_title']).'</b>';
        $AO_title='<b>'.stripslashes($parameters['AO_title']).'</b>';
        $sitelink='<a href="http://www.edit-place.co.uk">Edit-place</a>';
        $resubmit_time=$parameters['resubmit_time'];
        $resubmit_hours=$parameters['resubmit_hours'];
        $submit_hours=$parameters['submit_hours'];
        $comments=$parameters['comments'];
        $article_sent_date='<b>'.$parameters['article_sent_date'].'</b>';
        $corrector_ao_link='<a href="'.$parameters['corrector_ao_link'].'">Click here</a>';
        $accept_refuse_at='<b>'.$parameters['accept_refuse_at'].'</b>';
        $correctorcomments=$parameters['correctorcomments'];
		
        $moderator_link= '<a href="http://admin-test.edit-place.co.uk/correction/moderation?submenuId=ML3-SL10">Click here</a>';
        if($parameters['ongoing_bolink'])
            $ongoinglink= '<a href="http://admin-test.edit-place.co.uk'.$parameters['ongoing_bolink'].'">Click here</a>';

        $comment_bo_link='<a href="http://admin-test.edit-place.co.uk'.$parameters['comment_bo_link'].'">Click here</a>';
        
        $participation_limit='<b>'.$parameters['participation_limit'].'</b>';
        $pollcategory='<b>'.$parameters['pollcategory'].'</b>';
        $poll_title='<b>'.$parameters['poll_title'].'</b>';
        $poll_enddate='<b>'.$parameters['poll_enddate'].'</b>';
        $client_polllink='<b>'.$parameters['client_polllink'].'</b>';
		$category=trim($parameters['category']);
		$poll_link=$parameters['poll_link'];
		$aowithlink='<a href="/contrib/aosearch">'.$parameters['AO_title'].'</a>';

        //bank info
        $old_bank_info='<b>'.$parameters['old_bank_info'].'</b>';
        $new_bank_info='<b>'.$parameters['new_bank_info'].'</b>';
        $email_address='<b>'.$parameters['email_address'].'</b>';
        $paypal_emailaddress='<b>'.$parameters['paypal_emailaddress'].'</b>';
        $writeremail_address='<b>'.$parameters['writeremail_address'].'</b>';
		$profilelink='<a href="/contrib/private-profile">your account</a>';


        if($parameters['sender_id'])
            $sender=$parameters['sender_id'];
        else
            $sender=NULL;


        $email=$this->getAutoEmail($mailid);
        $Object=$email[0]['Object'];
        $Object=strip_tags($Object);
        eval("\$Object= \"$Object\";");
        $Message=$email[0]['Message'];
        eval("\$Message= \"$Message\";");
        
        if($personal==='out_editplace')
        {
            //sending email to a emaild not in the site (Example sending bank details email to external edit place user)
            $email=$receiverId;
	    //echo $Message;exit;
            $this->sendMailPersonalMailBox(NULL,$Object,$Message,$email);
        }

        elseif($personal)
        {
            /**sending to personal mail Box**/
            $this->sendMailPersonalMailBox($receiverId,$Object,$Message);
        }

        else
        {
            /**Inserting into EP mail Box**/
            $this->sendMailEpMailBox($receiverId,$Object,$Message,$sender);
        }

    }

    public function sendMailEpMailBox($receiverId,$object,$content,$sender=NULL)
    {
        //$sender=$this->adminLogin->userId;
        if(!$sender)
            $sender='111201092609847';
        $ticket=new Ep_Ticket_Ticket();
        $ticket->sender_id=$sender;
        $ticket->recipient_id=$receiverId;

        $ticket->title=strip_tags($object);
        $ticket->status='0';
        $ticket->created_at=date("Y-m-d H:i:s", time());
       // echo "<pre>";print_r($ticket);exit;
        try
        {
            if($ticket->insert())
               {
                   
                    $ticket_id=$ticket->getIdentifier();
                    $message=new Ep_Ticket_Message();
                    $message->ticket_id=$ticket_id;
                    $message->content=$content;
                    $message->type='0' ;
                    $message->status='0';
                    $message->created_at=$ticket->created_at;
                    $message->approved='yes';
                    $message->auto_mail='yes';
                    $message->insert();
					
				    $messageId=$message->getIdentifier();

					$UserDetails=$this->getUserType($receiverId);
                    $email=$UserDetails[0]['email'];
                    $password=$UserDetails[0]['password'];
                    $type=$UserDetails[0]['type'];
					
					if(!$object)
                    $object="You have received an Edit-place email";

                    $object=strip_tags($object); 

					if($UserDetails[0]['type']=='client')
					{
						$text_mail="<p>Dear client,<br><br>
										You have received an email from Edit-place !<br><br>
										Please <a href=\"http://".$_SERVER['HTTP_HOST']."/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">click here</a> to read it.<br><br>
										Regards,<br>
										<br>
										Edit-place team</p><br><br>
                                        You do not wish to receive notifications ? <a href=\"http://".$_SERVER['HTTP_HOST']."/user/alert-unsubscribe?uaction=unsubscribe&user=".MD5('ep_login_'.$email)."\">Click here</a>."
									;
					}
					else if($UserDetails[0]['type']=='contributor')
					{
						$text_mail="<p>Dear writer,<br><br>
										You have received an email from Edit-place !<br><br>
										Please <a href=\"http://".$_SERVER['HTTP_HOST']."/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">click here</a> to read it.<br><br>
										Regards,<br>
										<br>
										Edit-place team<br><br>
                                        You do not wish to receive notifications ? <a href=\"http://".$_SERVER['HTTP_HOST']."/user/alert-unsubscribe?uaction=unsubscribe&user=".MD5('ep_login_'.$email)."\">Click here</a>.</p>"
									;
					}
                    else
                        $text_mail=$content;

                    $content = $this->autoLoginlinkReplace($content, $email, $password, $type);
					
					$content.="<br><br>
								You do not wish to receive notifications ? <a href=\"http://ep-test.edit-place.co.uk/user/alert-unsubscribe?uaction=unsubscribe&user=".MD5('ep_login_'.$email)."\">Click here</a>";
								
                    if($UserDetails[0]['alert_subscribe']=='yes' || $UserDetails[0]['type']=='client')
                    {
    					$config_obj=new Ep_User_Configuration();
                        $config=$config_obj->getAllConfigurations();

                        $critsend=$config['critsend'];
                        $from=$config['mail_from'];

                        if($critsend=='yes')
                        {

                            critsendMail($from, $UserDetails[0]['email'], $object, $content);
                            return true;
                        }
                        else
                        {
                            $mail = new Zend_Mail();
        					$mail->addHeader('Reply-To',$from);
        					$mail->setBodyHtml($content)
        						 ->setFrom($from,'Support Edit-place')
        						 ->addTo($UserDetails[0]['email'])
                                 //->setSubject(utf8_decode($object));
                                 ->setSubject($object);
        					if($mail->send())
        						return true;
                        }    
                    }    
               }
        }
        catch(Exception $e)
        {
                echo $e->getMessage();
        }
    }
    /**send notification email to personal mail box*/
    public function sendAutoPersonalEmail($receiverId,$object=NULL,$messageId=NULL,$ticketId=NULL)
    {

        $UserDetails=$this->getUserType($receiverId);
        $email=$UserDetails[0]['email'];
        $password=$UserDetails[0]['password'];
        $type=$UserDetails[0]['type'];

        if(!$object)
        $object="You have received an Edit-place email";
		
		$object=strip_tags($object);	
		
        if($UserDetails[0]['type']=='client')
        {
            $text_mail="<p>Dear Client,<br><br>
                            You have received an email from Edit-place !<br><br>
                            Please <a href=\"http://".$_SERVER['HTTP_HOST']."/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticketId."\">Click here</a> to read it.<br><br>
                            Regards,<br>
                            <br>
                            Edit-place team<br><br>
							You do not wish to receive notifications ? <a href=\"http://".$_SERVER['HTTP_HOST']."/user/alert-unsubscribe?uaction=unsubscribe&user=".MD5('ep_login_'.$email)."\">Click here</a>.</p>"
                        ;
        }
        else if($UserDetails[0]['type']=='contributor')
        {
            $text_mail="<p>Dear writer,<br><br>
                            You have received an email from Edit-place !<br><br>
                            Please <a href=\"http://".$_SERVER['HTTP_HOST']."/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticketId."\">Click here</a> to read it.<br><br>
                            Regards,<br>
                            <br>
                            Edit-place team<br><br>
							You do not wish to receive notifications ? <a href=\"http://".$_SERVER['HTTP_HOST']."/user/alert-unsubscribe?uaction=unsubscribe&user=".MD5('ep_login_'.$email)."\">Click here</a>.</p>"
                        ;
        }
        //$config_obj=new Ep_User_Configuration();
        //$support=$config_obj->getConfiguration('mail_from');
        //if($support=="NO")
        //{
           $support='support@edit-place.com';
        //}
        $text_mail = $this->autoLoginlinkReplace($text_mail, $email, $password, $type);
       if($UserDetails[0]['alert_subscribe']=='yes')
       {
            
            $config_obj=new Ep_User_Configuration();
            $config=$config_obj->getAllConfigurations();
            $critsend=$config['critsend'];
            $from=$config['mail_from'];

            if($critsend=='yes')
            {
                
                critsendMail($from, $UserDetails[0]['email'], $object, $text_mail);
                return true;
            }
            else
            {
                $mail = new Zend_Mail();
                $mail->addHeader('Reply-To',$from);
                $mail->setBodyHtml($text_mail)
                     ->setFrom($from,'Support Edit-place')
                     ->addTo($UserDetails[0]['email'])
                     ->setSubject($object);
                if($mail->send())
                    return true;
            }    
        }         
    }
    /**send notification email to personal mail box*/
    public function sendNotificationEmail($recipient=NULL)
    {
        $config_obj=new Ep_User_Configuration();
        $config=$config_obj->getAllConfigurations();
        $notify_to=$config['notify_emails'];
        $from=$config['mail_from'];
            if(!$from)
                $from='support@edit-place.com';

        if($notify_to)
            $notify_to=explode(",",$notify_to);

        
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
        $user_details=$this->getUserDetails($userIdentifier);
        $contributor_name=$user_details[0]['username'];

        $object="New email from customer service";       

          $text_mail="<p>Dear Project Manager,</p>
                    <p>You just received an email from <b>$contributor_name</b>. <a href=\"http://admin-test.edit-place.co.uk/mastermails/inbox-ep?submenuId=ML6-SL2\">Click here</a> to read it. </p>
                    <p>Sincerely,</p>
                    <p>Edit-place team.</p>";          
        

        $to=$recipient;

        $BoUserDetails=$this->getUserType($to);
        $to=$BoUserDetails[0]['email'];

        $critsend=$config['critsend'];
        
        if($to)
        {
            if($critsend=='yes')
            {
                
                critsendMail($from, $to, $object, $text_mail);
                return true;
            }
            else
            {
                $mail = new Zend_Mail();
                $mail->addHeader('Reply-To',$from);
                $mail->setBodyHtml($text_mail)
                     ->setFrom($from);
                //foreach($notify_to as $to)
                    $mail->addTo($to);

                $mail->setSubject($object);
                if($mail->send())
                    return true;
            }    
        }    
    }
    /**send email to personal mail box*/
    public function sendMailPersonalMailBox($receiverId=NULL,$object,$message,$email=NULL)
    {
        if(!$email)
        {
            $UserDetails=$this->getUserType($receiverId);
            $email=$UserDetails[0]['email'];            
        }    
        $object=strip_tags($object);
        $message=stripslashes($message);

        //echo $message;exit;

        $support='support@edit-place.com';

        $config_obj=new Ep_User_Configuration();
        $config=$config_obj->getAllConfigurations();

        $critsend=$config['critsend'];
        $from=$config['mail_from'];
		
		$UserDetails=$this->getUserType($receiverId);
        $password=$UserDetails[0]['password'];
        $type=$UserDetails[0]['type'];
					
        $message = $this->autoLoginlinkReplace($message, $email, $password, $type);

        if($critsend=='yes')
        {
            critsendMail($from, email, $object, $message);
            return true;
        }
        else
        {

            $mail = new Zend_Mail();
            $mail->addHeader('Reply-To',$support);
            $mail->setBodyHtml($message)
                ->setFrom($support,'Support Edit-place')
                ->addTo($email)
                ->setSubject($object);
            if($mail->send())
                return true;
        } 
        
        
    }
	
	public function autoLoginlinkReplace($content, $email, $password, $type)
    {
        // preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $content, $matches);
        // $urls = $matches[2];
        preg_match_all("/(?<=href=(\"|'))[^\"']+(?=(\"|'))/",$content,$matches);
        $urls = $matches[0];
		//print_r($urls);
        if(count($urls) != 0)
        {
            $alllinks=array("/contrib/aosearch",          "http://ep-test.edit-place.co.uk/contrib/aosearch",
                "/client/quotes",                         "http://ep-test.edit-place.co.uk/client/quotes",
                "/contrib/mission-deliver",               "http://ep-test.edit-place.co.uk/contrib/mission-deliver",
                "/contrib/ongoing",                       "http://ep-test.edit-place.co.uk/contrib/ongoing",
                "/contrib/refused",                       "http://ep-test.edit-place.co.uk/contrib/refused",
                "/contrib/mission-corrector-deliver",     "http://ep-test.edit-place.co.uk/contrib/mission-corrector-deliver",
                "/client/ongoingao",                      "http://ep-test.edit-place.co.uk/client/ongoingao",
                "/client/invoice",                        "http://ep-test.edit-place.co.uk/client/invoice",
                "/contrib/mission-published",             "http://ep-test.edit-place.co.uk/contrib/mission-published",
                "/ftvchaine/index",                       "http://ep-test.edit-place.co.uk/ftvchaine/index",
                "/ftvedito/index",                        "http://ep-test.edit-place.co.uk/ftvedito/index",
                "/contrib/compose-mail",                  "http://ep-test.edit-place.co.uk/contrib/compose-mail",
                "/client/ongoingnopremium",               "http://ep-test.edit-place.co.uk/client/ongoingnopremium",
                "/client/order1",                         "http://ep-test.edit-place.co.uk/client/order1",
                "/contrib/royalties",                     "http://ep-test.edit-place.co.uk/contrib/royalties",
				"/contrib/modify-profile",                "http://ep-test.edit-place.co.uk/contrib/modify-profile", 
				"/contrib/private-profile",                "http://ep-test.edit-place.co.uk/contrib/private-profile"); 

            for($i=0; $i<count($urls); $i++)
            {
                $linkarr = explode("?", $urls[$i]); //seperating the domainand and params///
                $domain[$i] = $linkarr[0];
                ///removing the ../ adn replace with / //
                $domain[$i] = str_replace('../','/',$domain[$i]);
                if($linkarr[1]!= '')
                    $params[$i] = $linkarr[1];
                else
                    $params[$i] = '';
                if(in_array($domain[$i], $alllinks))
                {
                    $domaindetials[$i] = explode("/", $domain[$i]); //spliting the domain///
                    $action[$i] = $domaindetials[$i][count($domaindetials[$i])-1];
                    $toURL[$i]="http://ep-test.edit-place.co.uk/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&red_to=".$action[$i]."&parameters=".$params[$i];
                    if($params[$i] != '')
                        $content=str_replace($domain[$i]."?".$params[$i],$toURL[$i],$content);
                    else
                        $content=str_replace($domain[$i],$toURL[$i],$content);
                }
            }
            return  $content;
        }else{
            return $content;
        }

    }
    /* *** added on 19.02.2016 *** */
    // functio to send an email with or with out attachment//
    function sendMail($to, $subj, $msg, $path=false, $attach=false, $cc="") {
        $mail = new Zend_Mail;
        $fullattched = $path.$attach;

        //echo "file name  : " .$path.$fullattched. PHP_EOL;

        $at = new Zend_Mime_Part(file_get_contents($fullattched)); //.xls is included in the filename
        $at->type = 'application/octet-stream';
        $at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
        $at->encoding = Zend_Mime::ENCODING_BASE64;
        $at->filename = basename($fullattched); //.xls is included in the filename

        $mail->setFrom('work@edit-place.com');
        $mail->addTo($to);
        if($cc)
        {
            $mail->addCc($cc);
        }
        $mail->setSubject($subj);
        $mail->setBodyHtml($msg);
        if($attach != '')//avoid attachment if no attachment present.
            $mail->addAttachment($at);

        try {
            $mail->send();
            //	echo "Mail Sent".PHP_EOL;
        } catch (Zend_Mail_Exception $e) {
            print_r($e->getMessage());
        }
    }
       
}


