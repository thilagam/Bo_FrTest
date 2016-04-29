<?php
/**
 * Ep_Message_AutoEmails
 * @author Admin
 * @package Message
 * @version 1.0
 */
class Ep_Message_AutoEmails extends Ep_Db_Identifier
{

    function getUserDetails($user)
	{
		$senderQuery="select IF(u.type='client',company_name,CONCAT(first_name,' ',UPPER(SUBSTRING(last_name, 1,1)))) as username ,email from User u
		                    LEFT JOIN UserPlus up ON u.identifier=up.user_id
		                    LEFT JOIN Client c ON u.identifier=c.user_id
		                    where identifier='".$user."'";
		//echo $senderQuery;exit;
		if(($result=$this->getQuery($senderQuery,true))!=NULL)
		{
			return $result;
		}

	}
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
    function getUserType($user)
    {
        $typeQuery="select email,type,profile_type,password from User u where identifier='".$user."'";
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
        //$automail=new Ep_Message_AutoEmails();

        $AO_Creation_Date='<b>'.$parameters['created_date'].'</b>';
        $link='<a href="http://mmm-new.edit-place.com'.$parameters['document_link'].'">Cliquez ici </a>';
        $contributor='<b>'.$parameters['contributor_name'].'</b>';
        $client='<b>'.$parameters['client_name'].'</b>';
        $royalty='<b>'.$parameters['royalty'].'</b>';
        $ongoinglink='<a href="http://mmm-new.edit-place.com'.$parameters['ongoinglink'].'">Cliquez ici </a>';
        $AO_end_date='<b>'.$parameters['AO_end_date'].'</b>';
        $article='<b>'.stripslashes($parameters['article_title']).'</b>';
        $AO_title='<b>'.stripslashes($parameters['AO_title']).'</b>';

        $email=$this->getAutoEmail($mailid);

        $Object=html_entity_decode($email[0]['Object']);
        $Message=stripslashes($email[0]['Message']);
        eval("\$Message= \"$Message\";");

            $mail = new Zend_Mail();
            $mail->addHeader('Reply-To','support@edit-place.com');
            $mail->setBodyHtml($Message)
                ->setFrom('support@edit-place.com')
                ->addTo($to)
                ->setSubject($Object);
 //           if($mail->send())
  //              return true;

        //print_r($mail);exit;
    }
    public function sendAutoPersonalEmail($receiverId,$object=NULL,$messageId=NULL,$ticketId=NULL)
    {


        $UserDetails=$this->getUserType($receiverId);
        $email=$UserDetails[0]['email'];
        $password=$UserDetails[0]['password'];
        $type=$UserDetails[0]['type'];

        if(!$object)
            $object="Vous avez reçu un email-Edit-place";

        if($UserDetails[0]['type']=='client')
        {
            $text_mail="<p>Cher client, ch&egrave;re  cliente,<br><br>
                            Vous avez re&ccedil;u un email d'Edit-place&nbsp;!<br><br>
                            Merci de cliquer <a href=\"http://mmm-new.edit-place.com/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">ici</a> pour le lire.<br><br>
                            Cordialement,<br>
                            <br>
                            Toute l'&eacute;quipe d&rsquo;Edit-place</p>"
                        ;
        }
        else if($UserDetails[0]['type']=='contributor')
        {
            $text_mail="<p>Cher contributeur,  ch&egrave;re contributrice,<br><br>
                            Vous avez re&ccedil;u un email d'Edit-place&nbsp;!<br><br>
                            Merci de cliquer <a href=\"http://mmm-new.edit-place.com/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">ici</a> pour le lire.<br><br>
                            Cordialement,<br>
                            <br>
                            Toute l'&eacute;quipe d&rsquo;Edit-place</p>"
                        ;
        }

        $mail = new Zend_Mail();
        $mail->addHeader('Reply-To','support@edit-place.com');
        $mail->setBodyHtml($text_mail)
             ->setFrom('support@edit-place.com')
             ->addTo($UserDetails[0]['email'])
             ->setSubject($object);
 //       if($mail->send())
          //  return true;
    }
    /*get the mail message from automails table for all the pages in mail content area**/
    public function getMailComments($receiverId,$mailid,$parameters)
    {    //echo $mailid; print_r($parameters);  exit;
        $automail=new Ep_Message_AutoEmails();

        $AO_Creation_Date='<b>'.$parameters['created_date'].'</b>';
        $link='<a href="http://mmm-new.edit-place.com'.$parameters['document_link'].'">Cliquant ici</a>';
        $contributor='<b>'.$parameters['contributor_name'].'</b>';
        $AO_title="<b>".$parameters['AO_title']."</b>";
        $submitdate_bo="<b>".$parameters['submitdate_bo']."</b>";
        $total_articles="<b>".$parameters['noofarts']."</b>";
        $article_link='<a href="'.$parameters['articlename_link'].'">Cliquez-ici</a>';
        $invoicelink='<a href="http://mmm-new.edit-place.com'.$parameters['invoice_link'].'">cliquant ici</a>';
        $client='<b>'.$parameters['client_name'].'</b>';
        $royalty='<b>'.$parameters['royalty'].'</b>';
        $ongoinglink='<a href="http://mmm-new.edit-place.com'.$parameters['ongoinglink'].'">cliquez-ici</a>';
        $AO_end_date='<b>'.$parameters['AO_end_date'].'</b>';
        $article='<b>'.stripslashes($parameters['article_title']).'</b>';
        $articlewithlink='<a href="'.$parameters['articlename_link'].'">'.stripslashes($parameters['article_title']).'</a>';
        $AO_title='<b>'.stripslashes($parameters['AO_title']).'</b>';
        $aowithlink='<a href="'.$parameters['aoname_link'].'">'.stripslashes($parameters['AO_title']).'</a>';
        $resubmit_time='<b>'.stripslashes($parameters['resubmit_time']).'</b>';
        $resubmit_hours='<b>'.stripslashes($parameters['resubmit_hours']).'</b>';
        $site='<a href="http://mmm-new.edit-place.com">Edit-place</a>';
        $corrector_date='<b>'.$parameters['correcteddate'].'</b>';
        $submit_hours = "<b>".$parameters['crtsubmitdate_bo']."</b>";
        $corrector_ao_link = '<a href="http://mmm-new.edit-place.com'.$parameters['corrector_ao_link'].'">cliquant ici</a>';
        $editplace='<a href="http://mmm-new.edit-place.com">www.edit-place.com</a>';

        $email=$automail->getAutoEmail($mailid);
        $Object=$email[0]['Object'];
        $Message=$email[0]['Message'];
        eval("\$Message= \"$Message\";");
        return $Message;
        /*Inserting into EP mail Box**/
        $this->sendMailEpMailBox($receiverId,$Object,$Message);
    }
    /*Send mail to EP mail box**/
    public function messageToEPMail($receiverId,$mailid,$parameters)
    {
        $automail=new Ep_Message_AutoEmails();

        $AO_Creation_Date='<b>'.$parameters['created_date'].'</b>';
        $link='<a href="http://mmm-new.edit-place.com'.$parameters['document_link'].'">Cliquant ici</a>';
        $contributor='<b>'.$parameters['contributor_name'].'</b>';
        $AO_title="<b>".$parameters['AO_title']."</b>";
        $submitdate_bo="<b>".$parameters['submitdate_bo']."</b>";
        $total_articles="<b>".$parameters['noofarts']."</b>";
        $invoicelink='<a href="http://mmm-new.edit-place.com'.$parameters['invoice_link'].'">cliquant ici</a>';
        $article_link='<a href="'.$parameters['articlename_link'].'">Cliquant-ici</a>';
        $client='<b>'.$parameters['client_name'].'</b>';
        $royalty='<b>'.$parameters['royalty'].'</b>';
        $ongoinglink='<a href="http://mmm-new.edit-place.com'.$parameters['ongoinglink'].'">cliquez-ici</a>';
        $AO_end_date='<b>'.$parameters['AO_end_date'].'</b>';
        $article='<b>'.stripslashes($parameters['article_title']).'</b>';
        $articlewithlink='<a href="'.$parameters['articlename_link'].'">'.stripslashes($parameters['article_title']).'</a>';
        $AO_title='<b>'.stripslashes($parameters['AO_title']).'</b>';
        $aowithlink='<a href="'.$parameters['aoname_link'].'">'.stripslashes($parameters['AO_title']).'</a>';
        $aosearch_link = '<a href="'.$parameters['aoname_link'].'">Cliquant-ici</a>';
        $articleclient_link  = '<a href="'.$parameters['clientartname_link'].'">'.stripslashes($parameters['AO_title']).'</a>';
        $client_link = '<a href="'.$parameters['clientartname_link'].'">Cliquant-ici</a>';
        $site='<a href="http://mmm-new.edit-place.com">Edit-place</a>';
        $editplace='<a href="http://mmm-new.edit-place.com">www.edit-place.com</a>';
        $resubmit_hours=$parameters['resubmit_hours'];
        $submit_hours="<b>".$parameters['submitdate_bo']."</b>";
        $corrector_ao_link='<a href="'.$parameters['aoname_link'].'">Cliquant-ici</a>';
        $comments=$parameters['comments'];
        $article_sent_date=$parameters['article_sent_date'];
        $corrector_date='<b>'.$parameters['correcteddate'].'</b>';
        $correctorcomments=$parameters['correctorcomments'];

        //Poll Priority contribs
        $poll=$parameters['poll'];
        $date='<b>'.$parameters['date'].'</b>';
        $poll_link=$parameters['poll_link'];
        $price='<b>'.$parameters['price'].'</b>';
        $hours='<b>'.$parameters['hours'].'</b>';

        $email=$automail->getAutoEmail($mailid);
        $Object=$email[0]['Object'];

        $Object=strip_tags($Object);
        eval("\$Object= \"$Object\";");

        $Message=$email[0]['Message'];
        eval("\$Message= \"$Message\";");
        /**Inserting into EP mail Box**/
        $this->sendMailEpMailBox($receiverId,$Object,$Message);
    }

    public function sendMailEpMailBox($receiverId,$object,$content)
    {   
        //echo $receiverId ; echo $object; echo $content; exit;
        $sender=$this->adminLogin->userId;
        $sender='111201092609847';
        $ticket=new Ep_Message_Ticket();
        $ticket->sender_id=$sender;
        $ticket->recipient_id=$receiverId;

        $ticket->title=$object;
        $ticket->status='0';
        $ticket->created_at=date("Y-m-d H:i:s", time());
        try
        {
           
            if($ticket->insert())
            {
                $ticket_id=$ticket->getIdentifier();
                $message=new Ep_Message_Message();
                $message->ticket_id=$ticket_id;
                $message->content=$content;
                $message->type='0' ;
                $message->status='0';
                $message->created_at=$ticket->created_at;
                $message->approved='yes';
                $message->auto_mail='yes';                

                $message->insert();

                

                $messageId=$message->getIdentifier();


                $automail=new Ep_Message_AutoEmails();
                $UserDetails=$automail->getUserType($receiverId);
                $email=$UserDetails[0]['email'];
                $password=$UserDetails[0]['password'];
                $type=$UserDetails[0]['type'];

                if(!$object)
                    $object="Vous avez reçu un email-Edit-place";

                $object=strip_tags($object);

                if($UserDetails[0]['type']=='client')
                {
                    $text_mail="<p>Cher client, ch&egrave;re  cliente,<br><br>
										Vous avez re&ccedil;u un  email d'Edit-place&nbsp;!<br><br>
										Merci de cliquer <a href=\"http://mmm-new.edit-place.com/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">ici</a> pour le lire.<br><br>
										Cordialement,<br>
										<br>
										Toute l'&eacute;quipe d&rsquo;Edit-place</p>"
                    ;
                }
                else if($UserDetails[0]['type']=='contributor')
                {
                    $text_mail="<p>Cher contributeur,  ch&egrave;re contributrice,<br><br>
										Vous avez re&ccedil;u un  email d'Edit-place&nbsp;!<br><br>
										Merci de cliquer <a href=\"http://mmm-new.edit-place.com/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">ici</a> pour le lire.<br><br>
										Cordialement,<br>
										<br>
										Toute l'&eacute;quipe d&rsquo;Edit-place</p>"
                    ;
                }

              //  critsendMail($this->configval['mail_from'], $UserDetails[0]['email'], $object, $text_mail);
                $this->configval['mail_from'] = "support@edit-place.com";
                $mail = new Zend_Mail();
                $mail->addHeader('Reply-To',$this->configval['mail_from']);
                $mail->setBodyHtml($text_mail)
                     ->setFrom($this->configval['mail_from'])
                     ->addTo($UserDetails[0]['email'])
                     ->setSubject($object);

//                if($mail->send())
 //               return true;
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }

    }
    /**get the mail object from automails table**/
    public function getMailObject($mailid)
    {
        $automail=new Ep_Message_AutoEmails();
        $email=$automail->getAutoEmail($mailid);
        $Object=$email[0]['Object'];
        $Object=strip_tags($Object);
        eval("\$Object= \"$Object\";");
        return $Object;
    }



}

