<?php
/** Cron Controller
 */
class CronController extends Ep_Controller_Action
{
    public function init()
    {
        parent::init();
         /*SSH server details**/
        $this->ssh2_server = "50.116.62.9" ;
        $this->ssh2_user_name = "oboulo" ;
        $this->ssh2_user_pass = "3DitP1ace" ;
        /**Loading Configuration Settings**/
        $config_obj=new Ep_User_Configuration();
        $config=$config_obj->getAllConfigurations();
        $this->config=$config;
    }    
    /**cron function to send auto Reminder mail to the Ao created user writing 1hour before submit the article**/
    public function reminderMailEpOneHourAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        $participation_details=$paticipation_obj->getParticipationOneHourLeft();
        if($participation_details!="NO")
        {
            foreach($participation_details AS $paticipants)
            {
                $ep_user_id=$paticipants['created_user'];
                $contributor_id= $paticipants['user_id'];
                $automail=new Ep_Ticket_AutoEmails();
                $ep_details=$automail->getUserDetails($ep_user_id);
                $contributor_details= $automail->getUserDetails($contributor_id);
                $ep_user=$ep_details[0]['username'];
                $contributor='<b>'.$contributor_details[0]['username'].'</b>';
                $ongoing_bo='<a href="http://admin-test.edit-place.co.uk/ao/ongoingao?submenuId=ML2-SL4&client='.$paticipants['client'].'&ao='.$paticipants['delivery'].'">click here</a>';
                $AO_title='<b>'.stripslashes($paticipants['title']).'</b>';
                $article='<b>'.stripslashes($paticipants['article']).'</b>';
                if($paticipants['premium_option']!='0' && $paticipants['premium_option']!='' )
                    $mail_id=46;
                else
                    $mail_id=41;
                $email=$automail->getAutoEmail($mail_id);
                $Object=$email[0]['Object'];
                eval("\$Object= \"$Object\";");
                $Object=strip_tags($Object);
                $Message=$email[0]['Message'];
                eval("\$Message= \"$Message\";");
                //echo $Message;exit;
                $mail = new Zend_Mail();
                $mail->addHeader('Reply-To','support@edit-place.com');
                $mail->setBodyHtml($Message)
                    ->setFrom('support@edit-place.com','Support Edit-place')
                    ->addTo($ep_details[0]['email'])
                //->setSubject(utf8_decode($object));
                    ->setSubject($Object);
                //$mail->send();
                //exit;
            }
        }
    }
    /**Auto profiles selection of articles every one hour**/
    public function autoProfileSelectionAction()
    {
	$paticipation_obj=new Ep_Participation_Participation();
        $participation_details=$paticipation_obj->getParticipationWithSenior();
        if($participation_details!="NO")
        {
            foreach($participation_details as $autoValidate)
            {
                $articleId=$autoValidate['id'];
                $lessPrice_obj=new Ep_Participation_Participation();
                $delivery=new Ep_Article_Delivery();
                $delivery_details=$delivery->getDeliveryDetails($articleId);
                $lessPriceContributor=$lessPrice_obj->getParticipationLessPrice($articleId);                        
                if($lessPriceContributor!="NO")
                {
                        if($lessPriceContributor[0]['profile_type']=='senior')
                        {
                            if($delivery_details[0]['senior_time'])
                            {
                                $time=$delivery_details[0]['senior_time'];//2days
                            }
                            else
                                $time=$this->config['sc_time'];//2days
                            //$expires=time()+(60*60*$time);
                            $expires=time()+(60*$time);
                            $articleIdentifier=$lessPriceContributor[0]['article_id'];
                            $userIdentifier=$lessPriceContributor[0]['user_id'];
                            $participationId=$lessPriceContributor[0]['id'];
                            /**Updating 48hours time to submit the article**/
                            $data_array = array("article_submit_expires"=>$expires);////////updating
                            $query="id='".$participationId."'";
                            $lessPrice_obj->updateArticleSubmitExpires($data_array,$query);
                            
                            /**Sending Auto Accept and Refuse Mails**/
                            $this->seniorSelectionAutoEmails($articleIdentifier,$userIdentifier,$expires);
                           
                            /**Accepting the Bid and Refusing other bids**/
                            $data = array("status"=>"bid","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                            $query = "user_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
                            $lessPrice_obj->updateParticipation($data,$query);
                            $dataref = array("status"=>"bid_refused","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                            $queryref = "user_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";                            
                            $lessPrice_obj->updateParticipation($dataref,$queryref);
                            /**ENDED**/
                        }
                        else  if($lessPriceContributor[0]['profile_type']=='junior' || $lessPriceContributor[0]['profile_type']=='sub-junior' )
                        {
                            $articleIdentifier=$lessPriceContributor[0]['article_id'];
                            $userIdentifier=$lessPriceContributor[0]['user_id'];
                            $participationId=$lessPriceContributor[0]['id'];
                            /**Accepting the Bid temporarily and Refusing other bids temporarily**/
                            $data = array("status"=>"bid_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                            $query = "user_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
                            $lessPrice_obj->updateParticipation($data,$query);
                            $dataref = array("status"=>"bid_refused_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                            $queryref = "user_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";
                            $lessPrice_obj->updateParticipation($dataref,$queryref);
                            /**ENDED**/
                        }
                }        
            }
        }
    }
    /**sending auto accept/refusals when senior contributor selected**/
    public function seniorSelectionAutoEmails($articleId,$userid,$expires)
    {
        
        $participation_obj=new Ep_Participation_Participation();
        $AllPartcipations=$participation_obj->getAllParticipationByArticle($articleId);
        if($AllPartcipations!="NO")
        {
            foreach($AllPartcipations AS $paticipants)
            {
                $participation=new Ep_Participation_Participation();
                $automail=new Ep_Ticket_AutoEmails();
                $participationDetails=$participation->getParticipateDetails($paticipants['id']);
                $parameters['AO_end_date']=date("d/m/Y",$expires)." at ".date("h:i:s A",$expires);//date("d/m/Y",strtotime($participationDetails[0]['submitdate_bo']));
                $parameters['article_title']=$participationDetails[0]['title'];
                $parameters['articlename_link']="/contrib/mission-deliver?article_id=".$participationDetails[0]['article_id'];
                        if($paricipationDetails[0]['deli_anonymous']=='1')
                            $parameters['client_name']='inconnu';
                       else
                       {
                           $clientDetails=$automail->getUserDetails($participationDetails[0]['clientId']);
                           if($clientDetails[0]['username']!=NULL)
                               $parameters['client_name']= $clientDetails[0]['username'];
                           else
                               $parameters['client_name']= $clientDetails[0]['email'];
                       }
                    $parameters['article_link']="/contrib/ongoing";
                    $parameters['royalty']=$participationDetails[0]['price_user']."&".$participationDetails[0]['currency'].";";
                    $parameters['resubmit_time']=$this->config['sc_resubmission'];
                    if($paticipants['user_id']==$userid)
                    {
                        $automail->messageToEPMail($paticipants['user_id'],25,$parameters);
                              
                              //Insert action in history table
                              $action_obj=new Ep_Article_ArticleActions();
                              $history_obj=new Ep_Article_ArticleHistory();

                              $action_sentence= $action_obj->getActionSentence(3);

                              $ticket=new Ep_Ticket_Ticket();                                                  
                              $contributor_name='<a href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$userid.'" target=_blank""><b>'.$ticket->getUserName($userid,TRUE).'</b></a>';
                              $article_name='<b>'.$parameters['article_title'].'</b>';

                              eval("\$action_sentence= \"$action_sentence\";");

                              if($action_sentence)
                              {  
                                $history_array['article_id']=$participationDetails[0]['article_id'];                                                
                                $history_array['user_id']=$userid;
                                $history_array['stage']='system_bot'; 
                                $history_array['action']='profile_accepted';
                                $history_array['action_at']=date("Y-m-d H:i:s");
                                $history_array['action_sentence']=$action_sentence;                                  

                                $history_obj->insertHistory($history_array);
                              }

                              //send notification email to Project manage
                              $bo_user=$participationDetails[0]['created_user'];
                              $mail_params['AO_title']=$participationDetails[0]['deliveryTitle'];
                              $mail_params['contributor_name']=$ticket->getUserName($userid,TRUE);
                              $mail_params['article_title']=$participationDetails[0]['title'];
                              $mail_params['bo_user']=$ticket->getUserName($bo_user,TRUE);
                              
                              $mail_params['comment_bo_link']='/ongoing/ao-details?client_id='.$participationDetails[0]['clientId'].'&ao_id='.$participationDetails[0]['deliveryId'].'&submenuId=ML2-SL4';

                              $automail_bo=new Ep_Ticket_AutoEmails();
                              $automail_bo->messageToEPMail($bo_user,109,$mail_params,TRUE);
                    }    
                    else
                        $automail->messageToEPMail($paticipants['user_id'],27,$parameters);
            }
        }
    }
    /** Corrector Auto profiles selection of articles every one hour**/
    public function autoCorrectorProfileSelectionAction()
    {
        $paticipation_obj=new Ep_Participation_CorrectorParticipation();
        $participation_details=$paticipation_obj->getParticipationWithSenior();
        if($participation_details!="NO")
        {
            foreach($participation_details as $autoValidate)
            {
                $articleId=$autoValidate['id'];
                $lessPrice_obj=new Ep_Participation_CorrectorParticipation();
                $delivery=new Ep_Article_Delivery();
                $delivery_details=$delivery->getDeliveryDetails($articleId);
                $lessPriceContributor=$lessPrice_obj->getParticipationLessPrice($articleId);
                $scount=0;
                $cnt=0;
                foreach($lessPriceContributor as $corrector)
                {
                    if(!$corrector['profile_type2'])
                          $lessPriceContributor[$cnt]['profile_type2']='junior';
                     if($corrector['profile_type2']=='senior')
                               $scount++;
                     $cnt++;
                }
                //if($lessPriceContributor[0]['type2']=='corrector' && (($lessPriceContributor[0]['profile_type2']=='senior') OR ($lessPriceContributor[0]['profile_type2']=='junior' && $scount==0 )))
                if($lessPriceContributor[0]['type2']=='corrector' && $lessPriceContributor[0]['profile_type2']=='senior') 
                {
                    if($delivery_details[0]['correction_sc_submission'])
                    {
                        $time=$delivery_details[0]['correction_sc_submission'];//2days
                    }
                    else
                        $time=$this->config['correction_sc_submission'];//2days
                    //$expires=time()+(60*60*$time);
                    $expires=time()+(60*$time);
                    $articleIdentifier=$lessPriceContributor[0]['article_id'];
                    $userIdentifier=$lessPriceContributor[0]['corrector_id'];
                    $participationId=$lessPriceContributor[0]['id'];
                    /**Updating 48hours time to submit the article**/
                    $data_array = array("corrector_submit_expires"=>$expires);////////updating
                    $query="id='".$participationId."'";
                    $lessPrice_obj->updateArticleSubmitExpires($data_array,$query);
                    /**Sending Auto Accept and Refuse Mails**/
                    $this->seniorCorrectorSelectionAutoEmails($articleIdentifier,$userIdentifier,$expires,$corrector['profile_type2']);
                    /**Accepting the Bid and Refusing other bids**/
                    $data = array("status"=>"bid","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                    $query = "corrector_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
                    $lessPrice_obj->updateParticipation($data,$query);
                    $dataref = array("status"=>"bid_refused","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                    $queryref = "corrector_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";
                    $lessPrice_obj->updateParticipation($dataref,$queryref);
                    /**ENDED**/
                }
                else  if($lessPriceContributor[0]['profile_type2']=='junior')
                {
                    $articleIdentifier=$lessPriceContributor[0]['article_id'];
                    $userIdentifier=$lessPriceContributor[0]['corrector_id'];
                    $participationId=$lessPriceContributor[0]['id'];
                    /**Accepting the Bid temporarily and Refusing other bids temporarily**/
                    $data = array("status"=>"bid_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                    $query = "corrector_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
                    $lessPrice_obj->updateParticipation($data,$query);
                    $dataref = array("status"=>"bid_refused_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                    $queryref = "corrector_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";
                    $lessPrice_obj->updateParticipation($dataref,$queryref);
                    /**ENDED**/
                }
            }
        }
    }
    /* * when corrector article participation time is up**/
    /*public function articleParticipationTimeUpAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        $article_obj=new Ep_Article_Article();
        $automail_bo=new Ep_Ticket_AutoEmails();
        $parameters['ongoing_bolink'] = 'yes';
        $artdeldetails = $article_obj->getArticleDeliveryTimeUp(); //print_r($artdeldetails); exit;
        if($artdeldetails!="NO")
        {
            foreach($artdeldetails as $artdetail)
            {
                $artcount = $article_obj->getArticleCountDelivery($artdetail['id']);
                $partsdetails = $article_obj->getCycleInArts($artdetail['artId']);
                $cycledetails = $article_obj->getMaxCycleParts($artdetail['artId']);

                $parameters['bo_user'] = $artdetail['login'];
                //echo $artcount[0]['artCount']; echo "helo".$artdetail['artCount']; exit;
                if($artcount[0]['artCount'] == $artdetail['artCount'])
                {
                    $parameters['AO_title'] = $artdetail['deliveryTitle'];
                    $this->sendMailToEPPersonal($artdetail['email'],119,$parameters);
                }
                // print_r($partsdetails); exit;
               // echo $partsdetails[0]['status']; echo "sel".$partsdetails[0]['cycle']; exit;
                if($partsdetails != 'NO')
                {
                    if($cycledetails[0]['maxcycle'] > 0)
                    {
                        $parameters['article_title'] = $artdetail['title'];
                        $parameters['cyclecount'] = $cycledetails[0]['maxcycle'];
                        $this->sendMailToEPPersonal($artdetail['email'],120,$parameters);
                    }
                    elseif($cycledetails[0]['maxcycle'] == 0)
                    {
                        $parameters['AO_title'] = $artdetail['deliveryTitle'];
                        $this->sendMailToEPPersonal($artdetail['email'],119,$parameters);
                    }
                }
                else
                {  //echo $artdetail['email']; echo $artdetail['user_id']; exit;
                    $parameters['AO_title'] = $artdetail['deliveryTitle'];
                    $this->sendMailToEPPersonal($artdetail['email'],119,$parameters);
                }
            }
        }

        //print_r($artdeldetails);
        echo "successfully done"; exit;
       /* $artdetails = $article_obj->getArticleTimeUp();
        if($artdetails!="NO")
        {
            foreach($artdetails as $artdetail)
            {
                $partsdetails = $article_obj->getCycleInArts($artdetail['artId']);
                $parameters['ongoing_bolink'] = 'yes';
                if($partsdetails == "NO")
                {
                    $automail_bo->messageToEPMail($artdetails[0]['user_id'],119,$parameters);
                }
                else if($partsdetails[0]['status'] == "bid_premium" && $partsdetails[0]['cycle'] != '0')
                {
                    $automail_bo->messageToEPMail($artdetails[0]['user_id'],120,$parameters);
                }
                $crtpartsdetails = $article_obj->getCycleInCrtArts($artdetail['artId']);
                if($crtpartsdetails == "NO")
                {
                    $automail_bo->messageToEPMail($artdetails[0]['user_id'],119,$parameters);
                }
                else if($crtpartsdetails[0]['status'] == "bid_premium" && $crtpartsdetails[0]['cycle'] != '0')
                {
                    $automail_bo->messageToEPMail($artdetails[0]['user_id'],120,$parameters);
                }
            }
        }

    }*/
    /* * when article participation time is up**/
    public function articleParticipationTimeUpAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        $article_obj=new Ep_Article_Article();
        $artdeldetails = $article_obj->getArticleDeliveryTimeUp();
        $countartdeldetails = $article_obj->getCountArticleDeliveryTimeUp();
        if($artdeldetails!="NO")
        {     //echo $artcount[0]['artCount']; echo $artdetail['deliveryTitle']; exit;
            $delarray = array();
            foreach($artdeldetails as $artdetail)
            {
                $artcount = $article_obj->getArticleCountDelivery($artdetail['id']);
                $parameters['bo_user'] = $artdetail['login'];
                if($artcount[0]['artCount'] == $countartdeldetails[0]['artCount'] && !in_array($artdetail['id'], $delarray))
                {
                    $parameters['AO_title'] = $artdetail['deliveryTitle'];
                    $this->sendMailToEPPersonal($artdetail['email'],119,$parameters);
                    $delarray[] = $artdetail['id'];
                    continue;
                }
                if($artdetail['republish_count'] != '0')
                {
                    $parameters['article_title'] = $artdetail['title'];
                    $parameters['cyclecount'] = $artdetail['republish_count']+1;
                    $this->sendMailToEPPersonal($artdetail['email'],120,$parameters);

                }
            }
        }
       // print_r($artdeldetails);
        echo "successfully done"; exit;
    }
    /* * when corrector article participation time is up**/
    public function correctorArticleParticipationTimeUpAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        $article_obj=new Ep_Article_Article();
        $artdeldetails = $article_obj->getCrtArticleDeliveryTimeUp();
        $countartdeldetails = $article_obj->getCountCrtArticleDeliveryTimeUp();
        if($artdeldetails!="NO")
        {     //echo $artcount[0]['artCount']; echo $artdetail['deliveryTitle']; exit;
            $delarray = array();
            foreach($artdeldetails as $artdetail)
            {
                $artcount = $article_obj->getArticleCountDelivery($artdetail['id']);
                $parameters['bo_user'] = $artdetail['login'];
                /*if($artcount[0]['artCount'] == $countartdeldetails[0]['artCount'] && !in_array($artdetail['id'], $delarray))
                {
                    $parameters['AO_title'] = $artdetail['deliveryTitle'];
                    $this->sendMailToEPPersonal($artdetail['email'],121,$parameters);
                    $delarray[] = $artdetail['id'];
                    continue;
                }*/
                if($artdetail['correction_republish_count'] != '0')
                {
                    $parameters['article_title'] = $artdetail['title'];
                    $parameters['cyclecount'] = $artdetail['correction_republish_count']+1;
                    $this->sendMailToEPPersonal($artdetail['email'],122,$parameters);

                }
            }
        }
        // print_r($artdeldetails);
        echo "successfully done"; exit;
    }

    public function sendMailToEPPersonal($useremailId, $emailId, $parameters )
    {
        $AO_title='<b>'.stripslashes($parameters['AO_title']).'</b>';
        $article='<b>'.stripslashes($parameters['article_title']).'</b>';
        $bo_user='<b>'.$parameters['bo_user'].'</b>';
        $cyclecount='<b>'.$parameters['cyclecount'].'e</b>';
        $ongoinglink= '<a href="http://admin-test.edit-place.co.uk/ongoing/list?submenuId=ML2-SL4">cliquant-ici</a>';
        $automail=new Ep_Ticket_AutoEmails();
        $email=$automail->getAutoEmail($emailId);
        $Object=$email[0]['Object'];
        eval("\$Object= \"$Object\";");
        $Object=strip_tags($Object);
        $Message=$email[0]['Message'];
        eval("\$Message= \"$Message\";");
        $mail = new Zend_Mail();
        $mail->addHeader('Reply-To','support@edit-place.com');
        $mail->setBodyHtml($Message)
            ->setFrom('support@edit-place.com', 'Support Edit-place')
            ->addTo($useremailId)
            //->setSubject(utf8_decode($object));
            ->setSubject($Object);
        $mail->send();
        //exit;
    }
    /** Corrector Auto profiles selection 2of articles every one hour**/
    public function autoCorrectorProfileSelection2Action()
    {
        $paticipation_obj=new Ep_Participation_CorrectorParticipation();
        $participation_details=$paticipation_obj->getParticipationWithSenior();
        if($participation_details!="NO")
        {
            foreach($participation_details as $autoValidate)
            {
                $articleId=$autoValidate['id'];
                $lessPrice_obj=new Ep_Participation_CorrectorParticipation();
                $delivery=new Ep_Article_Delivery();
                $delivery_details=$delivery->getDeliveryDetails($articleId);
                $lessPriceContributor=$lessPrice_obj->getParticipationLessPrice($articleId);
                if($lessPriceContributor[0]['type2']=='corrector')
                {
                   if($lessPriceContributor[0]['profile_type2']=='senior')
                   {
                        if($delivery_details[0]['correction_sc_submission'])
                        {
                            $time=$delivery_details[0]['correction_sc_submission'];//2days
                        }
                   }
                    else
                    {
                        if($delivery_details[0]['correction_jc_submission'])
                        {
                            $time=$delivery_details[0]['correction_jc_submission'];//2days
                        }
                    }
                    //$expires=time()+(60*60*$time);
                    $expires=time()+(60*$time);
                    $articleIdentifier=$lessPriceContributor[0]['article_id'];
                    $userIdentifier=$lessPriceContributor[0]['corrector_id'];
                    $participationId=$lessPriceContributor[0]['id'];
                    /**Updating 48hours time to submit the article**/
                    $data_array = array("corrector_submit_expires"=>$expires);////////updating
                    $query="id='".$participationId."'";
                    $lessPrice_obj->updateArticleSubmitExpires($data_array,$query);
                    /**Sending Auto Accept and Refuse Mails**/
                    $this->seniorCorrectorSelectionAutoEmails($articleIdentifier,$userIdentifier,$expires,$corrector['profile_type2']);
                    /**Accepting the Bid and Refusing other bids**/
                    $data = array("status"=>"bid","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                    $query = "corrector_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
                    $lessPrice_obj->updateParticipation($data,$query);
                    $data = array("status"=>"bid_refused","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                    $query = "corrector_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";
                    $lessPrice_obj->updateParticipation($data,$query);
                    /**ENDED**/
                }
                else
                {
                    $articleIdentifier=$lessPriceContributor[0]['article_id'];
                    $userIdentifier=$lessPriceContributor[0]['corrector_id'];
                    $participationId=$lessPriceContributor[0]['id'];
                    /**Accepting the Bid temporarily and Refusing other bids temporarily**/
                    $data = array("status"=>"bid_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                    $query = "corrector_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
                    $lessPrice_obj->updateParticipation($data,$query);
                    $data = array("status"=>"bid_refused_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
                    $query = "corrector_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";
                    $lessPrice_obj->updateParticipation($data,$query);
                    /**ENDED**/
                }
            }
        }
    }
    /**sending auto accept/refusals when senior contributor selected**/
    public function seniorCorrectorSelectionAutoEmails($articleId,$userid,$expires,$corrector_type)
    {
        $participation_obj=new Ep_Participation_CorrectorParticipation();
        $AllPartcipations=$participation_obj->getAllParticipationByArticle($articleId);
        if($AllPartcipations!="NO")
        {
            foreach($AllPartcipations AS $paticipants)
            {
                $participation=new Ep_Participation_CorrectorParticipation();
                $automail=new Ep_Ticket_AutoEmails();
                $participationDetails=$participation->getParticipateDetails($paticipants['id']);
                $parameters['AO_end_date']=date("d/m/Y",$expires)." at ".date("h:i:s A",$expires);//date("d/m/Y",strtotime($participationDetails[0]['submitdate_bo']));
                $parameters['article_title']=$participationDetails[0]['title'];
                if($paricipationDetails[0]['deli_anonymous']=='1')
                    $parameters['client_name']='inconnu';
                else
                {
                    $clientDetails=$automail->getUserDetails($participationDetails[0]['clientId']);
                    if($clientDetails[0]['username']!=NULL)
                        $parameters['client_name']= $clientDetails[0]['username'];
                    else
                        $parameters['client_name']= $clientDetails[0]['email'];
                }
                $parameters['ongoinglink']="/contrib/ongoing";
                $parameters['royalty']=$participationDetails[0]['price_corrector']."&".$participationDetails[0]['currency'].";";
                if($corrector_type=='senior')
                    $parameters['resubmit_time']=$participationDetails[0]['correction_sc_resubmission'];
                else
                    $parameters['resubmit_time']=$participationDetails[0]['correction_jc_resubmission'];
                if($paticipants['corrector_id']==$userid)
                {
                    $automail->messageToEPMail($paticipants['corrector_id'],28,$parameters);

                    //Insert action in history table
                      $action_obj=new Ep_Article_ArticleActions();
                      $history_obj=new Ep_Article_ArticleHistory();

                      $action_sentence= $action_obj->getActionSentence(15);

                      $ticket=new Ep_Ticket_Ticket();                                                  
                      $contributor_name='<a href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$userid.'" target=_blank""><b>'.$ticket->getUserName($userid,TRUE).'</b></a>';
                      $article_name='<b>'.$parameters['article_title'].'</b>';

                      eval("\$action_sentence= \"$action_sentence\";");

                      if($action_sentence)
                      {  
                        $history_array['article_id']=$participationDetails[0]['article_id'];                                                
                        $history_array['user_id']=$userid;
                        $history_array['stage']='system_bot'; 
                        $history_array['action']='correction_profile_accepted';
                        $history_array['action_at']=date("Y-m-d H:i:s");
                        $history_array['action_sentence']=$action_sentence;                                  

                        $history_obj->insertHistory($history_array);
                      }

                      //send notification email to Project manage
                      $bo_user=$participationDetails[0]['created_user'];
                      $mail_params['AO_title']=$participationDetails[0]['deliveryTitle'];
                      $mail_params['contributor_name']=$ticket->getUserName($userid,TRUE);
                      $mail_params['article_title']=$participationDetails[0]['title'];
                      $mail_params['bo_user']=$ticket->getUserName($bo_user,TRUE);
                      
                      $mail_params['comment_bo_link']='/ongoing/ao-details?client_id='.$participationDetails[0]['clientId'].'&ao_id='.$participationDetails[0]['deliveryId'].'&submenuId=ML2-SL4';

                      $automail_bo=new Ep_Ticket_AutoEmails();
                      $automail_bo->messageToEPMail($bo_user,109,$mail_params,TRUE);
                }    
                else
                    $automail->messageToEPMail($paticipants['corrector_id'],29,$parameters);
            }
        }
    }
    /**cron function to send auto Reminder mail to the Ao created user when article submission time expires for a contributor**/
    public function reminderMailEpSubmitExpiresAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        $participation_details=$paticipation_obj->getArticleSubmissionExpires();        
        if($participation_details!="NO")
        {
            foreach($participation_details AS $paticipants)
            {
                $ep_user_id=$paticipants['created_user'];
                $contributor_id= $paticipants['user_id'];
                $client_id=$paticipants['client'];
                $automail=new Ep_Ticket_AutoEmails();
                
                $ep_details=$automail->getUserDetails($ep_user_id);
                $contributor_details= $automail->getUserDetails($contributor_id);
                $client_details= $automail->getUserDetails($client_id);
                $ep_user=$ep_details[0]['username'];
                $contributor='<b>'.$contributor_details[0]['username'].'</b>';
                $ongoing_bo='<a href="http://admin-test.edit-place.co.uk/ao/ongoingao?submenuId=ML2-SL4&client='.$paticipants['client'].'&ao='.$paticipants['delivery'].'">click here</a>';
                $ongoing_fo= '<a href="http://ep-test.edit-place.co.uk/client/order1?id='.$paticipants['article_id'].'">click here</a>';
                $article_client='<a href="http://ep-test.edit-place.co.uk/client/order1?id='.$paticipants['article_id'].'"><b>'.stripslashes($paticipants['article']).'</b></a>';   
                $article_contrib='<a href="http://ep-test.edit-place.co.uk/contrib/mission-deliver?article_id='.$paticipants['article_id'].'"><b>'.stripslashes($paticipants['article']).'</b></a>';   
                $link= $ongoing_fo;  
                $message_ids=array("client"=>35,"contrib"=>36);
                foreach($message_ids as $key=> $mid)
                {
                    if($key=='client')
                    {
                        $article=$article_client;
                        $AO_title=$article;
                        $user_email=$client_details[0]['email'];
                    }    
                    else
                    {
                        $article=$article_contrib;
                        $AO_title=$article;
                        $user_email=$contributor_details[0]['email'];
                    }    
                    $email=$automail->getAutoEmail($mid);
                    $Object=$email[0]['Object'];
                    eval("\$Object= \"$Object\";");
                    $Object=strip_tags($Object);
                    $Message=$email[0]['Message'];
                    eval("\$Message= \"$Message\";");
                    
                    if($paticipants['premium_option']=='0')
                    {
                        //echo $user_email."--".$Message."--".$Object."<br>";
                        $mail = new Zend_Mail();
                        $mail->addHeader('Reply-To','support@edit-place.com');
                        $mail->setBodyHtml($Message)
                            ->setFrom('support@edit-place.com','Support Edit-place')
                            ->addTo($user_email)
                        //->setSubject(utf8_decode($object));
                            ->setSubject($Object);
                        $mail->send();
                    }        
                }    
                
            }
        }
    }
    //Cron function to get all Articles with  Contest time is over - time to select someone
    public function timeOutContestWithQuotesAction()
    {   
        $paticipation_obj=new Ep_Participation_Participation();
        $participation_details=$paticipation_obj->getTimeOutContestWithQuotes();       
        if($participation_details!="NO")
        {
            foreach($participation_details AS $paticipants)
            {
                $article_id=$paticipants['article_id'];
                
                $ep_user_id=$paticipants['created_user'];
                $client_id= $paticipants['client'];
                $automail=new Ep_Ticket_AutoEmails();
                $ep_details=$automail->getUserDetails($ep_user_id);
                $client_details= $automail->getUserDetails($client_id);
                $selection_bo='<a href="http://admin-test.edit-place.co.uk/processao/profileslist?submenuId=ML2-SL2">click here</a>';
                $selection_fo='<a href="http://ep-test.edit-place.co.uk/client/quotes?id='.$article_id.'">click here</a>';
                
                $AO_title='<a href="http://ep-test.edit-place.co.uk/client/quotes?id='.$article_id.'"><b>'.stripslashes($paticipants['article']).'</b></a>';
                
                 if($paticipants['premium_option']!='0' && $paticipants['premium_option']!='' )
                 {
                    $selectionlink=$selection_bo;
                    $user_email=$ep_details[0]['email'];
                 }   
                else
                {
                    $selectionlink=$selection_fo;
                    $user_email=$client_details[0]['email'];
                }    
                $email=$automail->getAutoEmail(8);
                $Object=$email[0]['Object'];
                eval("\$Object= \"$Object\";");
                $Object=strip_tags($Object);
                $Message=$email[0]['Message'];
                $Message=stripslashes($Message);
                eval("\$Message= \"$Message\";");
                //echo $user_email.$Message;exit;
                $mail = new Zend_Mail();
                $mail->addHeader('Reply-To','support@edit-place.com');
                $mail->setBodyHtml($Message)
                    ->setFrom('support@edit-place.com','Support Edit-place')
                    ->addTo($user_email)
                //->setSubject(utf8_decode($object));
                    ->setSubject($Object);
                $mail->send();
                //exit;
            }
        }
    }
    //Cron function to get all Articles with  Contest time is over - no quotes sent
    public function timeOutContestWithoutQuotesAction()
    {   
        $paticipation_obj=new Ep_Participation_Participation();
        $participation_details=$paticipation_obj->getTimeOutContestWithoutQuotes();       
        if($participation_details!="NO")
        {
            foreach($participation_details AS $paticipants)
            {
                $article_id=$paticipants['article_id'];
                
                $ep_user_id=$paticipants['created_user'];
                $client_id= $paticipants['client'];
                $automail=new Ep_Ticket_AutoEmails();
                $ep_details=$automail->getUserDetails($ep_user_id);
                $client_details= $automail->getUserDetails($client_id);
                $selection_bo='<a href="http://admin-test.edit-place.co.uk/ao/ongoingao?submenuId=ML2-SL4&client='.$paticipants['client'].'&ao='.$paticipants['delivery'].'">click here</a>';
                $selection_fo='<a href="http://ep-test.edit-place.co.uk/client/quotes?id='.$article_id.'">click here</a>';
                $compose_link_bo='<a href="http://admin-test.edit-place.co.uk/mails/composemail?submenuId=ML4-SL1">click here</a>';
                $compose_link_fo='<a href="http://ep-test.edit-place.co.uk/client/compose-mail?serviceid=110923143523902">click here</a>';
                $time_extend_link_bo='<a href="http://admin-test.edit-place.co.uk/ao/ongoingao?submenuId=ML2-SL4&client='.$paticipants['client'].'&ao='.$paticipants['delivery'].'">click here</a>';
                $time_extend_link_fo='<a href="http://ep-test.edit-place.co.uk/client/quotes?id='.$article_id.'">click here</a>';
                
                $AO_title='<a href="http://ep-test.edit-place.co.uk/client/quotes?id='.$article_id.'"><b>'.stripslashes($paticipants['article']).'</b></a>';
                
                 if($paticipants['premium_option']!='0' && $paticipants['premium_option']!='' )
                 {
                    $selectionlink=$selection_bo;
                    $user_email=$ep_details[0]['email'];
                    $compose_link=$compose_link_bo;
                    $time_extend_link=$time_extend_link_bo;
                 }   
                else
                {
                    $selectionlink=$selection_fo;
                    $user_email=$client_details[0]['email'];
                    $compose_link=$compose_link_fo;
                    $time_extend_link=$time_extend_link_fo;
                }    
                $email=$automail->getAutoEmail(9);
                $Object=$email[0]['Object'];
                eval("\$Object= \"$Object\";");
                $Object=strip_tags($Object);
                $Message=$email[0]['Message'];
                $Message=stripslashes($Message);
                eval("\$Message= \"$Message\";");
                //echo $user_email.$Message;exit;
                $mail = new Zend_Mail();
                $mail->addHeader('Reply-To','support@edit-place.com');
                $mail->setBodyHtml($Message)
                    ->setFrom('support@edit-place.com','Support Edit-place')
                    ->addTo($user_email)
                //->setSubject(utf8_decode($object));
                    ->setSubject($Object);
                $mail->send();
                //exit;
            }
        }
    }
    /**upto this*/
    public function endofaomailAction()
    {
        $delivery= new Ep_Article_Delivery();
        $EndAoOffers=$delivery->getEndAOOfToday();
        if(is_array($EndAoOffers) && $EndAoOffers!="NO")
        {
            foreach($EndAoOffers as $offer)
            {
                $automail=new Ep_Ticket_AutoEmails();
                $parameters['created_date']=date("d/m/Y",strtotime($offer['created_at']));
                $parameters['document_link']="/client/ongoingnopremium";
                $parameters['invoice_link']="/client/invoice";
                $parameters['AO_title']=$offer['title'];
                if($offer['mail_send']=='yes')
                //$automail->sendAutoEmail($offer['email'],10,$parameters);
                 $automail->messageToEPMail($offer['identifier'],10,$parameters);// sendAutoEmail($offer['email'],10,$parameters);
            }
        }
    }
    
    /**cron function to send auto Reminder mail to the contributors writing before 30mins**/
    public function reminderMailThirtyMinAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        $participation_details=$paticipation_obj->getParticipationThirtyMinLeft();
        if($participation_details!="NO")
        {
            foreach($participation_details AS $paticipants)
            {
                $automail=new Ep_Ticket_AutoEmails();
                $user_id=$paticipants['user_id'];
                $parameters['article_title']=$paticipants['title'];
                 $automail->messageToEPMail($user_id,28,$parameters);
                
                
            }
        }
    }
    /**cron function to send auto Reminder mail to the contributors writing before 6hours**/
    public function reminderMailSixHoursAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        $participation_details=$paticipation_obj->getParticipationSixHoursLeft();
        if($participation_details!="NO")
        {
            foreach($participation_details AS $paticipants)
            {
                $automail=new Ep_Ticket_AutoEmails();
                $user_id=$paticipants['user_id'];
                 $parameters['article_title']=$paticipants['title'];
                 $automail->messageToEPMail($user_id,27,$parameters);
            }
        }
    }
    /**cron function to update status for participates that are not validated**/
    public function timeOutParticipationsAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        $article_details=$paticipation_obj->timeoutParticipations();
        $data='';
        if($article_details!="NO")
        {
            $data='<table border="1">';
            $data.='<tr>
                        <th>Article Title</th>
                        <th>Ao Title</th>
                        <th>Client</th>
                        <th>Delivery Date</th>
                        <th>Type</th>
                        <th>No.of Participations</th>
                    </tr>';
            foreach($article_details AS $article)
            {
               if($article['status']=='bid_premium')
                   $type='Premium';
               else
                   $type='Non premium';
                $data.='<tr>
                        <td>'.$article['title'].'</td>
                        <td>'.$article['AOtitle'].'</td>
                        <td>'.$article['email'].'</td>
                        <td>'.$article['submitdate_bo'].'</td>
                        <td>'.$type.'</td>
                        <td>'.$article['participationCount'].'</td>
                    </tr>';
            }
            $data.='</table>';
        }
        //echo $data;
        if($data)
        {
            $mail = new Zend_Mail();
            $mail->addHeader('Reply-To','support@edit-place.com');
            $mail->setBodyHtml($data)
                ->setFrom('support@edit-place.com', 'Support Edit-place')
                ->addTo('mailpearls@gmail.com')
                ->setSubject('TimeOut Participation');
            //if($mail->send())
               // return true;
        }
    }
    /* cron function to update time out status */
    public function updatetimeoutparticipationAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        
        //Update time_out in Participation table
        $paticipation_obj->updatetimeout();
    }
    
    /* Cron function to send validation mails for AOs linked with polls */
    public function aovalidationmailsAction()
    {
        $Delivery_obj=new Ep_Client_Register();
        $automail=new Ep_Ticket_AutoEmails();
        $aoslist=$Delivery_obj->Listaocronmail();
        
            for($ao=0;$ao<count($aoslist);$ao++)
            {
                //Check if there are any new articles
                if($aoslist[$ao]['newartscount']>0)
                {
                    //Parameters list
                    $parameters['AO_title']=$aoslist[$ao]['title'];
                    $parameters['submitdate_bo']=$aoslist[$ao]['submitdate_bo'];
                    $parameters['noofarts']=$aoslist[$ao]['total_article'];
                    
                    if($aoslist[$ao]['deli_anonymous']=='0')
                        $parameters['article_link']="/contrib/aosearch?client_contact=".$aoslist[$ao]['user_id'];
                    else
                        $parameters['article_link']="/contrib/aosearch?client_contact=anonymous";
            
                    //To get poll participants
                        $pollcontribs=$Delivery_obj->getPollParticipations($aoslist[$ao]['poll_id']);
                    
                    if($aoslist[$ao]['AOtype']=='private')
                    {   
                        $contributors=array_unique(explode(",",$aoslist[$ao]['contribs_list']));
                        if(is_array($contributors) && count($contributors)>0)
                        {
                            foreach($contributors as $contributor)
                            {   
                                if(!in_array($contributor,$pollcontribs))
                                {
                                    $automail->messageToEPMail($contributor,15,$parameters);
                                    //echo $contributor."<br>";
                                }
                            }
                        }
                    }
                    elseif($aoslist[$ao]['AOtype']=='public')
                    {
                        $contributors=$Delivery_obj->getContributorsAO('public',$aoslist[$ao]['fav_category']);
                        
                        if(is_array($contributors) && count($contributors)>0)
                        {
                            foreach($contributors as $contributor)
                            {
                                if(!in_array($contributor['identifier'],$pollcontribs))
                                {
                                    //echo $contributor['identifier']."<br>";
                                    $automail->messageToEPMail($contributor['identifier'],14,$parameters);
                                }   
                                    
                            }
                        }
                    }
                    
                    //Update cronmail status in Delivery
                    $Delivery_obj->UpdateCronmailStatus($aoslist[$ao]['id']);
                }
            }       
    }
    /**cron to send email to EP team if AO is finished and time out with not all articles validated*/
    public function  aoFinishedTimeOutAction()
    {
        $paticipation_obj=new Ep_Participation_Participation();
        $timeoutAos=$paticipation_obj->AoFinishedTimeout();
        if($timeoutAos!="NO")
        {
            foreach($timeoutAos AS $delivery)
            {
                $ep_user_id=$delivery['created_user'];
                $automail=new Ep_Ticket_AutoEmails();
                $ep_details=$automail->getUserDetails($ep_user_id);
                $ep_user=$ep_details[0]['username'];
                $ongoing_bo='<a href="http://admin-test.edit-place.co.uk/ao/ongoingao?submenuId=ML2-SL4&client='.$delivery['user_id'].'&ao='.$delivery['id'].'">click here</a>';
                $AO_title='<b>'.stripslashes($delivery['title']).'</b>';
                $email=$automail->getAutoEmail(39);
                $Object=$email[0]['Object'];
                eval("\$Object= \"$Object\";");
                $Object=strip_tags($Object);
                $Message=$email[0]['Message'];
                eval("\$Message= \"$Message\";");
                $mail = new Zend_Mail();
                $mail->addHeader('Reply-To','support@edit-place.com');
                $mail->setBodyHtml($Message)
                    ->setFrom('support@edit-place.com', 'Support Edit-place')
                    ->addTo($ep_details[0]['email'])
                //->setSubject(utf8_decode($object));
                    ->setSubject($Object);
                //$mail->send();
                //exit;
            }
        }
    }
    
    
    /* cron function to send poll validation mails */
    public function sendpollmailsAction()
    {
        $poll_obj=new Ep_Poll_Poll();
        $poll_set=$poll_obj->pollstosendcronmails('yes');
        //print_r($poll_set);//exit;
        $art_cat_type_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
         
            for($p=0;$p<count($poll_set);$p++)
            {
                if($poll_set[$p]['poll_max']=="" || ($poll_set[$p]['poll_max']>$poll_set[$p]['pollparticipationcnt']))
                { echo $poll_set[$p]['title']."<br>";
                    $cat[]=$art_cat_type_array[$poll_set[$p]['category']];
                    $group=0;
                    $poll_cat=$poll_set[$p]['category'];
                    
                    // To get prev polls which have mail_send 'no'
                    $poll_no=$poll_obj->pollstosendcronmails('no');
                    
                    if(count($poll_no)>0)
                    {
                        for($n=0;$n<count($poll_no);$n++)
                        {
                            if($poll_no[$n]['poll_max']=="" ||($poll_no[$n]['poll_max']>$poll_no[$n]['pollparticipationcnt']))
                            {
                                $group=1;
                                $cat[]=$art_cat_type_array[$poll_no[$n]['category']];
                                $poll_cat.=",".$poll_no[$n]['category'];
                                echo $poll_no[$n]['title']."<br>";
                                $poll_obj->updatepollcronmail($poll_no[$n]['id']);
                            }
                        }   
                    }
                    
                    if(count($cat)>1)
                    {   
                        $cat1=array_unique($cat);//print_r($cat1);//exit;
                        $catlist=implode("<br/>",$cat1);
                        $parameters['category']=trim($catlist);
                    }
                    else
                    {
                        $parameters['category']=trim($art_cat_type_array[$poll_set[$p]['category']]);
                        
                        if($poll_set[$p]['category']=="finance")
                            $parameters['category']="Immobilier/Finances/Bourse";
                    }
                    echo $parameters['category']."<br>";
                    
                    $parameters['poll_link']='<a href="http://ep-test.edit-place.co.uk/contrib/aosearch">here</a>';
                    
                        if($group=='1')
                            $mailid=72;
                        else
                            $mailid=16;
                            
                    $contrb_arr=$poll_obj->getContributors($poll_set[$p]['id'],$poll_cat,$poll_set[$p]['contributors'],$poll_set[$p]['black_contrib']);
                    $contribcount=count($contrb_arr);
                    
                    $automail=new Ep_Ticket_AutoEmails();
                    //Mail Contributor
                    for($c=0;$c<$contribcount;$c++)
                    {
                        $automail->messageToEPMail($contrb_arr[$c]['identifier'],$mailid,$parameters);
                    }
                    
                
                //Update cron_mail
                $poll_obj->updatepollcronmail($poll_set[$p]['id']);
                unset($cat);
                }
            }
    }
    
    public function pollmessageToContrib($receiverId,$parameters,$grp)
    {
        
        $sender='111201092609847';
        
            if($grp=='1')
                $mailid=72;
            else
                $mailid=16;
            $automail=new Ep_Ticket_AutoEmails();
            $UserDetails=$automail->getUserType($receiverId); 
            $user=MD5('ep_login_'.$UserDetails[0]['email']);
            $password=MD5('ep_login_'.$UserDetails[0]['password']);
            $type=$UserDetails[0]['type'];
            
            $poll_link='<a href="http://ep-test.edit-place.co.uk/client/emaillogin?user='.$user.'&hash='.$password.'&type='.$type.'&poll='.$parameters['poll'].'">ici</a>';
            $category=$parameters['category'];
                
            $email=$automail->getAutoEmail($mailid);
            $Object=$email[0]['Object'];
            $Object=strip_tags($Object);
            eval("\$Object= \"$Object\";");
            
            $Message=$email[0]['Message'];
            eval("\$Message= \"$Message\";");
            $text_mail="Dear writer,<br><br>
                        Edit-place has created a new poll for a mission of writing in the category: <b>".$category."</b> <br><br>
                        Thank you click  ".$poll_link." to participate. <br>
                        Participants who offered the best offer will be given priority if the mission is actually launched.<br><br>
                        Yours sincerely,<br><br>
                        The Edit-place team
                        ";
                
            $mail = new Zend_Mail();
            $mail->addHeader('Reply-To','support@edit-place.com');
            $mail->setBodyHtml($Message)
                 ->setFrom('support@edit-place.com', 'Support Edit-place')
                 ->addTo($UserDetails[0]['email'])
                 //->addTo('kavithashree_r@yahoo.in')
                 ->addCC('kavithashree.r@gmail.com')
                 ->setSubject($Object);
            //echo $UserDetails[0]['email']."<br>";
           // if($mail->send())
              //  return true;
    }
    
    /**Mail client and sales after poll completion **/
    public function pollcompletedmailAction()
    {
        $poll_obj=new Ep_Poll_Participation();
        $poll=new Ep_Poll_Poll();
        $poll_set=$poll_obj->Listpollclientmail();
        
        if($poll_set!="NO")
        {
            for($p=0;$p<count($poll_set);$p++)
            {
                if(($poll_set[$p]['poll_max']!="" && $poll_set[$p]['poll_max']<=$poll_set[$p]['totalParticipation']) || $poll_set[$p]['poll_date']<=$poll_set[$p]['now'])
                {
                    $automail=new Ep_Ticket_AutoEmails();   
                    //echo $poll_set[$p]['title'];exit;
                    
                    //Client
                    $email=$automail->getAutoEmail(12);
                    $created_at=date('d/m/Y',strtotime($poll_set[$p]['created_at']));
                        $user=MD5('ep_login_'.$poll_set[$p]['email']);
                        $password=MD5('ep_login_'.$poll_set[$p]['password']);
                    $pollclient_link='<a href="http://ep-test.edit-place.co.uk/client/emaillogin?user='.$user.'&hash='.$password.'&type=client&poll='.$poll_set[$p]['id'].'">Click here</a>';
                    $poll_link='<a href="http://admin-test.edit-place.co.uk/ao/poll?submenuId=ML2-SL17">Click here</a>';
                    $partcount=$poll_set[$p]['totalParticipation'];
                    $client=$poll_set[$p]['first_name'].'&nbsp;'.$poll_set[$p]['last_name'];
                    
                    if($poll_set[$p]['created_by']!="client")
                        $salesemail=$poll_obj->getSalesMail($poll_set[$p]['created_by']);
                    
                    $Object=$email[0]['Object'];
                    eval("\$Object= \"$Object\";");
                    $Object=strip_tags($Object);
                    $Message=$email[0]['Message'];
                    eval("\$Message= \"$Message\";");
                    $mail = new Zend_Mail();
                    $mail->addHeader('Reply-To','support@edit-place.com');
                    $mail->setBodyHtml($Message)
                        ->setFrom('support@edit-place.com', 'Support Edit-place')
                        ->addTo($poll_set[$p]['email'])
                    //->setSubject(utf8_decode($object));
                        ->setSubject($Object);
                   // $mail->send();
                    
                        if($salesemail!="NO")
                        {
                            //Sales
                            $email1=$automail->getAutoEmail(13);
                            $Object1=$email1[0]['Object'];
                            eval("\$Object1= \"$Object1\";");
                            $Object1=strip_tags($Object1);
                            $Message1=$email1[0]['Message'];
                            eval("\$Message1= \"$Message1\";");
                            $mail1 = new Zend_Mail();
                            $mail1->addHeader('Reply-To','support@edit-place.com');
                            $mail1->setBodyHtml($Message1)
                                ->setFrom('support@edit-place.com', 'Support Edit-place')
                                ->addTo($salesemail[0]['email'])
                            //->setSubject(utf8_decode($object1));
                                ->setSubject($Object1);
                            //$mail1->send();
                        }
                    $poll->Updatepollmailclient($poll_set[$p]['id']);
                }
            }
        }
    }
    //cron to update timeout for liberte participations
    public function liberteParticipationTimeoutAction()
    {
        $participation_update_obj=new Ep_Participation_Participation();
        $data['status']='bid_nonpremium_timeout';
        $participation_update_obj->updateLiberteParticipationTimeout($data);
        //Update Participation SET status='bid_nonpremium_timeout' where status='bid_nonpremium' and valid_date IS NOT NULL AND DATE(valid_date) < CURDATE()
    }
    //cron to update timeout for liberte participations
    public function jc0tojuniorAction()
    {
        $user_obj=new Ep_User_User();
        /*$paticipation_obj=new Ep_Participation_Participation();
        $getparts = $paticipation_obj->getPublishedPartsInLastHour();
        if($getparts != 'NO')
        {
            foreach($getparts as $jc0users)
            {
                $data['profile_type']='junior';
                $query="identifier='".$jc0users['identifier']."'";
                $user_obj->updateUser($data, $query);
            }
        }*/
        
                $data['profile_type']='junior';
                $query="type='contributor' and profile_type='sub-junior' and identifier in (select distinct  user_id from  Participation where status='published')";;
                $user_obj->updateUser($data, $query);
    }
    //tag script validation Cron
    public function tagsValidationScriptAction()
    {
        
        error_reporting(0);
        
        require_once APP_PATH_ROOT.'nlibrary/script/Net/SFTP.php';
        $articleObj=new Ep_Article_Article();
        $tagArticles=$articleObj->tagScriptArticle();
        if($tagArticles!="NO" && is_array($tagArticles))
        {
             /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server) ;
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                 throw new Exception('Login Failed') ;
            }     
            foreach($tagArticles as $article)     
            {
                $url =   trim($article['client_site_url']) ;                
                $tag =   $article['tag_script'];
                $tag =   trim(str_replace('\"', '"', $tag)) ;
                $email="rakeshm@edit-place.com";
                //echo "<pre>".htmlentities($tag)."</pre>";
                //echo $url;exit;
                //Path to execute ruby command
                $file_exec_path=$sftp->exec("./test_check_script.sh"); //ruby execution path
                $file_exec_path=trim($file_exec_path);                
                $ruby_file  =   "checkscript.rb" ;   // ruby file
                
                $sftp->setTimeout(300);
                $cmd    =   "ruby -W0 $ruby_file '$url' '$tag' '$email'" ;
                $ruby_switch_prefix = "source ~/.rvm/scripts/rvm; rvm use 1.9.3-head " ;
                //echo "$ruby_switch_prefix;cd $file_exec_path;$cmd;";exit;
                $out_put= trim(str_replace('Using /home/oboulo/.rvm/gems/ruby-1.9.3-head', '',$sftp->exec("$ruby_switch_prefix;cd $file_exec_path;$cmd;"))) ;
                //$out_put=$sftp->exec("$ruby_switch_prefix;cd $file_exec_path;$cmd;");
                echo $output."<br>";
                /*if($out_put) :
                    $response['type'] = 'success';
                    $response['message'] = 'Client page validated successfully.';//<br>cmd='.$cmd ;
                else :
                    $response['type'] = 'error';
                    $response['message']= 'Validation failed.' ;
                endif ;*/
            }                                 
           
        }          
       
    }
    
	/** Mission alert mail based on publishtime**/  
    public function missionalertAction()
    {
        $ao_obj=new Ep_Ao_Delivery();
        
        $missions=$ao_obj->publishtimeao();
        print_r($missions);//exit;
        if(count($missions)>0)
        {
            for($m=0;$m<count($missions);$m++)
            {
                if($missions[$m]['premium_option']=='0')
                    $mailId=14;
                else
                    $mailId=15;
                    
                $parameters['AO_title']=$missions[$m]['title'];
                    $expires=$missions[$m]['publishtime']+(60*$missions[$m]['participation_time']);
                $parameters['submitdate_bo']=date('d/m/Y H:i', $expires);
				$parameters['subject']=$missions[$m]['mailsubject'];
				$parameters['content']=$missions[$m]['mailcontent'];
				
				if($missions[$m]['mail_sender']=='me')
						$parameters['sender_id']=$missions[$m]['created_user'];
                
                if($missions[0]['alertuser']!="")
                {
                    $alertcontribs=explode(",",$missions[$m]['alertuser']);
                    //print_r($alertcontribs);
                    foreach($alertcontribs as $alert)
                        $this->messageToEPMail($alert,$mailId,$parameters); 
                }
                
				/** Post comments on FB**/  
				
                $array['cronmail_publish']='yes';
                $where=" id='".$missions[$m]['id']."'";
                $ao_obj->updateDelivery($array,$where); 
            }
        }       
			//Correction alert
			$missioncorrection=$ao_obj->publishtimeaocorrection();
			print_r($missioncorrection);//exit;
			if(count($missioncorrection)>0)
			{
				for($m=0;$m<count($missioncorrection);$m++)
				{
					$parameterscorr['subject']=$missioncorrection[$m]['correctormailsubject'];
					$parameterscorr['content']=$missioncorrection[$m]['correctormailcontent'];
					
					if($missioncorrection[$m]['correctorsendfrom']=='me')
						$parameterscorr['sender_id']=$missioncorrection[$m]['created_user'];
					
					if($missioncorrection[0]['alertuser']!="")
					{
						$alertcontribs=explode(",",$missioncorrection[$m]['alertuser']);
						//print_r($alertcontribs);
						foreach($alertcontribs as $alert)
							$this->messageToEPMail($alert,178,$parameterscorr); 
					}
					
					/** Post comments on FB**/  
					
					$array['cronmail_publishcorrection']='yes';
					$where=" id='".$missioncorrection[$m]['id']."'";
					$ao_obj->updateDelivery($array,$where); 
				}
			} 
			
		
    }
	/** Mission alert mail based on publishtime to clients only**/  
    public function missionclientalertAction()
    {
        $ao_obj=new Ep_Ao_Delivery();
        
        $missions=$ao_obj->publishtimeaoclientmail();
        print_r($missions);//exit;
        if(count($missions)>0)
        {
            for($m=0;$m<count($missions);$m++)
            {
				$parameters['clientartname_link'] = "/client/quotes?id=".$missions[$m]['artId'];
                $parameters['AO_title']=$missions[$m]['title'];
                    $expires=$missions[$m]['publishtime']+(60*$missions[$m]['participation_time']);
                $parameters['submitdate_bo']=date('d/m/Y H:i', $expires);
                
                 $this->messageToEPMail($missions[0]['client_id'],5,$parameters);
					
				/** Post comments on FB**/  
                $array['cronmailclient_publish']='yes';
                $where=" id='".$missions[$m]['id']."'";
                $ao_obj->updateDelivery($array,$where); 
            }
        }       
    }
	
	/** Post comments on FB**/  
    public function postfbcommentsAction()
    {
		require_once 'postfb/facebook.php';
		$ao_obj=new Ep_Ao_Delivery();
        
        $missions=$ao_obj->publishtimefbpost();
		
		print_r($missions);//exit;
		$appId = '162345687286811';
		$secret = '88f1f23bbeaf975c954b93f0f083e3af';
		//$returnurl = 'http://localhost:8086/posttofb/';
		$permissions = 'publish_stream,offline_access';
		
		$fb = new Facebook(array("appId" => $appId, "secret" => $secret,'scope' => $permissions));
		
			for($m=0;$m<count($missions);$m++)
            {
				if($missions[$m]['fbcomment']!="")
				{
					$message = array(
								'access_token'=>'CAACTpw0FhBsBACgioyG6neMZBOnZCl74sjtR0WMhQg5INDvgj2lDZC1spN2o42VRY0uZBHbYqaPjZCigSjq0AAts4Q5z0fg4rZCkcYR04FfijkOwn7He051U1iU5RUQLwqWi41VGHuj4cTJoX4OBGyqhxNvTd0eDPGMOhbbNtH51CLoAYjkMNY',
								'message'=>utf8_encode(stripslashes($missions[$m]['fbcomment']))
								);
					$url='/420321404748767/feed';
					$result = $fb->api($url, 'POST', $message);
					
				}
				
				// update fbpost status
				$array['fbpost']='yes';
				$where=" id='".$missions[$m]['id']."'";
				$ao_obj->updateDelivery($array,$where);
			}
	}
	
	/**Send mail to EP mail box**/
    public function messageToEPMail($receiverId,$mailid,$parameters,$personal=NULL)
    {
		$automail=new Ep_Ticket_AutoEmails();
		$submitdate_bo="<b>".$parameters['submitdate_bo']."</b>";
        $aowithlink='<a href="/contrib/aosearch">'.$parameters['AO_title'].'</a>';
		$articleclient_link  = '<a href="'.$parameters['clientartname_link'].'">'.stripslashes($parameters['AO_title']).' </a>';
        $client_link = '<a href="'.$parameters['clientartname_link'].'">Click here</a>';
		
        if($parameters['sender_id'])
            $sender=$parameters['sender_id'];
        else
            $sender=NULL;


        $email=$automail->getAutoEmail($mailid);
        if($parameters['subject']!="")
			$Object=$parameters['subject'];
		else
			$Object=$email[0]['Object'];
        $Object=strip_tags($Object);
        eval("\$Object= \"$Object\";");
		
		if($parameters['content']!="")
			$Message=$parameters['content'];
		else
			$Message=$email[0]['Message'];
        eval("\$Message= \"$Message\";");

            /**Inserting into EP mail Box**/
            $this->sendMailEpMailBox($receiverId,$Object,$Message,$sender);
    }

    public function sendMailEpMailBox($receiverId,$object,$content,$sender=NULL)
    {
       	$automail=new Ep_Ticket_AutoEmails();
         if($sender)
            $sendfrom=$sender;
		else
			$sendfrom='111201092609847';
        $ticket=new Ep_Ticket_Ticket();
        $ticket->sender_id=$sendfrom;
        $ticket->recipient_id=$receiverId;

        $ticket->title=strip_tags($object);
        $ticket->status='0';
        $ticket->created_at=date("Y-m-d H:i:s", time());
       
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
                     if($sender!=NULL)
						$message->auto_mail='no';
					else
						$message->auto_mail='yes';
                    $message->insert();
					
				    $messageId=$message->getIdentifier();

					$UserDetails=$automail->getUserType($receiverId);
                    $email=$UserDetails[0]['email'];
                    $password=$UserDetails[0]['password'];
                    $type=$UserDetails[0]['type'];
					
					if(!$object)
                    $object="Vous avez reu un email-Edit-place";

                    $object=strip_tags($object); 

					if($UserDetails[0]['type']=='client')
					{
						$text_mail="<p>Dear client,<br><br>
										You have received an email from Edit-place&nbsp;!<br><br>
										Thank you <a href=\"http://".$_SERVER['HTTP_HOST']."/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">click here</a> to Read.<br><br>
										Yours sincerely,<br>
										<br>
										The Edit-place team</p>"
									;
					}
					else if($UserDetails[0]['type']=='contributor')
					{
						$text_mail="<p>Dear writer,<br><br>
										You have received an email from Edit-place&nbsp;!<br><br>
										Thank you <a href=\"http://".$_SERVER['HTTP_HOST']."/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">click here</a> to Read.<br><br>
										Yours sincerely,<br>
										<br>
										The Edit-place team</p>"
									;
					}
                    else
                        $text_mail=$content;
                    if($UserDetails[0]['subscribe']=='yes' || $UserDetails[0]['type']=='client')
                    {
    					
    					if($sender!=NULL)
						{
							//$fromname=$this->adminLogin->loginName;
								$fromURL="http://ep-test.edit-place.co.uk/contrib/aosearch";
								$toURL="http://ep-test.edit-place.co.uk/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&red_to=aosearch";
							$content=str_replace($fromURL,$toURL,$content);
							$body=$content;
							$user_obj= new Ep_User_User();
							$todetail=$user_obj->getEmailUser($sender);
							$from=$todetail[0]['email'];
							if($todetail[0]['first_name']!="")
								$fromname=$todetail[0]['first_name'].' '.$todetail[0]['last_name'];
							else
								$fromname=$todetail[0]['login'];
						}
						else
						{
							$body=$text_mail;
							$from='support@edit-place.com';
							$fromname='Support Edit-place';
						} 
						
						
						$mail = new Zend_Mail();
    					$mail->addHeader('Reply-To','support@edit-place.com');
    					$mail->setBodyHtml($body)
    						 ->setFrom($from,$fromname)
    						 ->addTo($UserDetails[0]['email'])
                             //->setSubject(utf8_decode($object));
                             ->setSubject($object);
    					if($mail->send())
    						return true;
                    }    
               }
        }
        catch(Exception $e)
        {
                echo $e->getMessage();
        }
    }
	
	/** Mission newsletters**/ 
	public function newsletterAction()
	{
		$ao_obj=new Ep_Ao_Delivery();
		$user_obj=new Ep_User_User();
		$poll_obj=new Ep_Poll_Poll();
		
		$NewsletterMissions=$ao_obj->getNewsletterMissions();
		$NewsletterDevis=$poll_obj->getNewsletterDevis();
		$contributors=$user_obj->getContributors(); 
		//print_r($NewsletterMissions);exit;
		$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		
		//Chief editor of the Day
		$ceday=$ao_obj->getEditorofDay();
		
		if(file_exists(APP_PATH_ROOT."profiles/bo/".$ceday[0]['created_user']."/logo.jpg"))
		{
			$ccarename=$ceday[0]['first_name'].'&nbsp;'.$ceday[0]['last_name'];
			$ccareprofile="http://ep-test.edit-place.co.uk/FO/profiles/bo/".$ceday[0]['created_user']."/logo.jpg";
		}
		else
		{
			$ccarename="Alix Keslassy";
			$ccareprofile="http://ep-test.edit-place.co.uk/FO/profiles/bo/110823103540627/logo.jpg";
		}
		//Footer statistics
		$stat_obj=new Ep_Stats_Stats();
		$configstat=array('stats_display' => $this->getConfiguredval('stats_display'), 'stats_days_value' => $this->getConfiguredval('stats_days_value'));
		$stats=$stat_obj->getAllStatistics($configstat);
		
		//setlocale(LC_TIME, "fr_FR");
		$writerarray=array();
		$num=0;
			foreach($contributors as $contrib)
			{
				//User details
				$user = MD5('ep_login_'.$contrib['email']);
				$password = MD5('ep_login_'.$contrib['password']);
				$type = $contrib['type'];
				
				$missionprem=array();
				$missionnoprem=array();
				$missionlanguage=array();
				$nlprem=0;
				$nlnoprem=0;
				for($m=0;$m<count($NewsletterMissions);$m++)
				{
						$missionlanguage[]=$NewsletterMissions[$m]['language'];
						//Mission Liberte
						if($NewsletterMissions[$m]['premium_option']=='0')
						{
							$missionnoprem[$nlnoprem]['id']=$NewsletterMissions[$m]['id'];
							if($NewsletterMissions[$m]['nltitle']!="")
								$missionnoprem[$nlnoprem]['title']=stripslashes($NewsletterMissions[$m]['nltitle']);
							else	
								$missionnoprem[$nlnoprem]['title']=stripslashes($NewsletterMissions[$m]['title']);
							$missionnoprem[$nlnoprem]['publishtime']=$NewsletterMissions[$m]['publishtime'];
							if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$NewsletterMissions[$m]['user_id']."/".$NewsletterMissions[$m]['user_id']."_global1.png") && $NewsletterMissions[$m]['deli_anonymous']=='0')	
								$missionnoprem[$nlnoprem]['clientlogo']="/FO/profiles/clients/logos/".$NewsletterMissions[$m]['user_id']."/".$NewsletterMissions[$m]['user_id']."_global1.png";
							else
								$missionnoprem[$nlnoprem]['clientlogo']="/FO/images/ep-feed-logo.png";
							$missionnoprem[$nlnoprem]['category']=$cat_array[$NewsletterMissions[$m]['category']];
							$missionnoprem[$nlnoprem]['total_article']=$NewsletterMissions[$m]['total_article'];	
							$missionnoprem[$nlnoprem]['language']=$NewsletterMissions[$m]['language'];	
							$missionnoprem[$nlnoprem]['languagetitle']=$language_array[$NewsletterMissions[$m]['language']];	
							$missionnoprem[$nlnoprem]['company_name']=stripslashes($NewsletterMissions[$m]['company_name']);	
							if($NewsletterMissions[$m]['view_to']=='sc')
							$missionnoprem[$nlnoprem]['ptype']='<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-s.png" width="41" height="21">';		
							elseif($NewsletterMissions[$m]['view_to']=='jc')
							$missionnoprem[$nlnoprem]['ptype']='<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-j.png" width="41" height="21">';		
							elseif($NewsletterMissions[$m]['view_to']=='jc0')
							$missionnoprem[$nlnoprem]['ptype']='<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-d.png" width="41" height="21">';		
							$nlnoprem++;
							
						}
						else
						{
							//Mission Premiums
							$missionprem[$nlprem]['id']=$NewsletterMissions[$m]['id'];
							if($NewsletterMissions[$m]['nltitle']!="")
								$missionprem[$nlprem]['title']=stripslashes($NewsletterMissions[$m]['nltitle']);
							else	
								$missionprem[$nlprem]['title']=stripslashes($NewsletterMissions[$m]['title']);
							$missionprem[$nlprem]['publishtime']=$NewsletterMissions[$m]['publishtime'];
							if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$NewsletterMissions[$m]['user_id']."/".$NewsletterMissions[$m]['user_id']."_global1.png") && $NewsletterMissions[$m]['deli_anonymous']=='0')	
								$missionprem[$nlprem]['clientlogo']="/FO/profiles/clients/logos/".$NewsletterMissions[$m]['user_id']."/".$NewsletterMissions[$m]['user_id']."_global1.png";
							else
								$missionprem[$nlprem]['clientlogo']="/FO/images/ep-feed-logo.png";
							$missionprem[$nlprem]['category']=$cat_array[$NewsletterMissions[$m]['category']];
							$missionprem[$nlprem]['total_article']=$NewsletterMissions[$m]['total_article'];	
							$missionprem[$nlprem]['language']=$NewsletterMissions[$m]['language'];	
							$missionprem[$nlprem]['languagetitle']=$language_array[$NewsletterMissions[$m]['language']];	
							$missionprem[$nlprem]['company_name']=stripslashes($NewsletterMissions[$m]['company_name']);	
							if($NewsletterMissions[$m]['view_to']=='sc')
							$missionprem[$nlprem]['ptype']='<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-s.png" width="41" height="21">';		
							elseif($NewsletterMissions[$m]['view_to']=='jc')
							$missionprem[$nlprem]['ptype']='<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-j.png" width="41" height="21">';		
							elseif($NewsletterMissions[$m]['view_to']=='jc0')
							$missionprem[$nlprem]['ptype']='<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-d.png" width="41" height="21">';		
							$nlprem++;
							
						}
					
				}
				$devisprem=array();
				$nldevis=0;
				for($d=0;$d<count($NewsletterDevis);$d++)
				{
						$devisprem[$nldevis]['id']=$NewsletterDevis[$d]['id'];
						$devisprem[$nldevis]['title']=stripslashes($NewsletterDevis[$d]['title']);
						$devisprem[$nldevis]['publish_time']=stripslashes($NewsletterDevis[$d]['publish_time']);
						if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$NewsletterDevis[$d]['client']."/".$NewsletterDevis[$d]['client']."_global1.png"))	
							$devisprem[$nldevis]['clientlogo']="/FO/profiles/clients/logos/".$NewsletterDevis[$d]['client']."/".$NewsletterDevis[$d]['client']."_global1.png";
						else
							$devisprem[$nldevis]['clientlogo']="/FO/images/ep-feed-logo.png";
						$devisprem[$nldevis]['category']=substr($cat_array[$NewsletterDevis[$d]['category']],0,25);
						$devisprem[$nldevis]['language']=$NewsletterDevis[$d]['language'];	
						$devisprem[$nldevis]['languagetitle']=$language_array[$NewsletterDevis[$d]['language']];	
						$devisprem[$nldevis]['company_name']=stripslashes($NewsletterDevis[$d]['company_name']);	
						if($NewsletterDevis[$d]['contributors']=='0')
						$devisprem[$nldevis]['ptype']='<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-s.png" width="41" height="21">';		
						elseif($NewsletterMissions[$m]['view_to']=='1')
						$devisprem[$nldevis]['ptype']='<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-j.png" width="41" height="21">';		
						elseif($NewsletterMissions[$m]['view_to']=='3')
						$devisprem[$nldevis]['ptype']='<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-d.png" width="41" height="21">';			
						$nldevis++;
					
				}//print_r($devisprem);
				if($contrib['first_name']!='')
					$name=ucfirst($contrib['first_name']).'&nbsp;'.ucfirst($contrib['last_name']);
				else
				{
					$mailname=explode("@",$contrib['email']);
					$name=$mailname[0];
				}
				
				
				if($contrib['profile_type']=='senior')
					$profilesrc="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-senior-header.png";
				elseif($contrib['profile_type']=='junior')	
					$profilesrc="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-junior-header.png";	
				else
					$profilesrc="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-debutant-header.png";	
					
				if(count($missionprem)>0 || count($missionnoprem)>0 || count($devisprem)>0)
				{
					if(in_array($contrib['language'],$missionlanguage))
					{
						//Insert Newsletter
						if($num==0)
						{
							$nl_obj=new Ep_User_DailyNewsletter();
							$insertarray=array();
							$nl_id=$nl_obj->insertNewsletter($insertarray);
						}
						
						$todaydate=date("Y-m-d");
					//Newsletter content  
					$newscontent='	<html lang="fr">
								<head>
									<meta name="viewport" content="width=device-width;initial-scale=1.0; user-scalable=1;" />
									<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
									<title>Les annonces du jour d\'edit-place</title>
								</head> 
								<body bgcolor="#e4e4e4" style="margin: 0; padding: 0; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;">
									<style type="text/css">
										table {margin-top: 0;}
									</style> 
									<div id="htmllink"><div align="center" style="padding-bottom: 10px;color: #999"><font size="-2">Problems viewing this email ? <a href="http://ep-test.edit-place.co.uk/client/newsletter?stamp='.strtotime(date("Y-m-d")).'&wrid='.$contrib['identifier'].'">View it in your browser</a></font></div></div>
										<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#3a3a3a">
											<tr>
												<td bgcolor="#3a3a3a" width="100%">    
												   <table width="660" cellpadding="0" cellspacing="0" border="0" align="center" style="table-layout:fixed">
														<tr>
															<td width="50%"><a href="http://www.edit-place.com"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/logo.png" alt="edit-place logo" width="163" height="25" border="0"/></a></td>
															<td width="114" style="padding-right:2px"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/top-title.png" alt="Annonces du" width="114" height="67" border="0" style="display: block"></td>
															<td width="50%" align="right" nowrap style="color: #999999; font-size: 12px;"> <span id="wrname" >'.stripslashes($name).'</span>  
															<img src="'.$profilesrc.'" id="wrprofile" width="75" height="21" hspace="5" align="absmiddle"></td>
														</tr>
													</table>   
												</td>
											</tr>  
										</table>
										<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#e4e4e4">
											<tr>
												<td bgcolor="#e4e4e4" width="100%">    
													<table width="660" cellpadding="0" cellspacing="0" border="0" align="center">
														<tr>
															<td width="50%"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="" height="1"></td>
															<td width="114" align="center"><table width="114" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td width="18" height="30"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/l-title.png" width="18" height="30" style="display: block"></td>
															<td width="78" align="center" bgcolor="#FFFFFF" style="font-family: Georgia, \'Times New Roman\', Times, serif; font-size: 22px; color: #555; line-height: 12px">'.strftime("%d %b").'</td>
															<td width="18" height="30"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/r-title.png" width="18" height="30" style="display: block"></td>
														</tr>
														<tr>
															<td colspan="3" valign="top" height="28"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/btm-title.png" width="114" height="28"></td>
														</tr>
													</table>
												</td>
												<td width="100%">&nbsp;</td>
											</tr>
										</table>';
    
    
			/*************************************************  Mission Premium **********************************************************/
								if(count($missionprem)>0)
								{	
									$newscontent.='<br>
										<table width="660" cellpadding="0" cellspacing="0" border="0" align="center">
											<tr>
												<td width=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/double-line.gif" width="144" height="4"></td>
												<td width="100%" align="center" style="letter-spacing: -1px; color: #ff6000; font-family: Arial, Helvetica, sans-serif; font-size: 30px; font-weight: bold;">PREMIUM MISSIONS</td>
												<td width=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/double-line.gif" alt="" width="144" height="4"></td>
											</tr>
										</table>
										<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="15">
    
										<!-- Start, Introducing message -->
										<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td rowspan="2"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/intro-block-l.png" width="38" height="136" style="display: block;" ></td>
												<td width="100%" bgcolor="#3a3a3a" valign="bottom">
												<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td width="130" align="center" style="color: #999999; font-size: 12px">
															<img src="'.$ccareprofile.'" alt="Project manager" width="70" height="70" vspace="5" style="display: block;">
														  <div style="font-size: 15px">'.$ccarename.'</div>
														  Project manager</td>
														<td width="1" valign="middle"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/dotted.gif" width="1" height="101"></td>
														<td width="10" valign="middle"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="15" height="110"></td>
														<td valign="middle" style="color: #FFF; font-size: 15px; font-style:italic; line-height:20px">
															<div><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="10"></div>
															'.stripslashes($ceday[0]['missioncomment']).'
														</td>
													</tr>
												</table>
												<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/intro-block-b.png" width="584" height="16" style="display: block"></td>
												<td rowspan="2"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/intro-block-r.png" width="38" height="136" style="display: block;" ></td>
											</tr>
										</table>';
										
										/**** Mission premium list ****/
										for($n=0;$n<count($missionprem);$n++)
										{
										 $newscontent.='<a id="premiumlink" href="http://ep-test.edit-place.co.uk/user/email-login?user='.$user.'&hash='.$password.'&type='.$type.'&nl=1&user_id='.$contrib['identifier'].'&mission_id='.$missionprem[$n]['id'].'" style="text-decoration:none;">
														<table width="660" cellpadding="5" cellspacing="0" border="0" align="center" bgcolor="#3A3A3A" style="border-top-color: #444444; border-top-width:1px; border-top-style:solid;">
															<tr>
																<td style="color: #eee; font-size: 12px; text-transform:uppercase; font-weight:bold">
																	<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="10" height="25" align="absmiddle">'.$missionprem[$n]['total_article'].'
																	<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/icon-file.gif" width="9" height="12" align="absmiddle">
																	<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/sep.png" alt="" width="29" height="25" align="absmiddle">'.$missionprem[$n]['category'].'
																	<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/sep.png" width="29" height="25" align="absmiddle">writing 
																	<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/flag/'.$missionprem[$n]['language'].'.jpg" alt="'.$missionprem[$n]['languagetitle'].'" width="16" height="11" hspace="5" align="absmiddle">
																</td>
																<td align="right">
																	'.$missionprem[$n]['ptype'].'
																	<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="10" height="1" align="absmiddle"></td>
															</tr>
														</table>
														<table width="660" border="0" cellspacing="0" cellpadding="0" align="center">
															<tr>
																<td bgcolor="#dadada">
																	<table width="100%" border="0" cellspacing="1" cellpadding="0">
																		<tr>
																			<td bgcolor="#ffffff"><div style="font-family: Arial, \'helvetica\', Times, serif; font-size: 26px; color: #555; padding-top: 10px;padding-left: 20px; padding-right: 20px;"><a href="http://ep-test.edit-place.co.uk/user/email-login?user='.$user.'&hash='.$password.'&type='.$type.'&nl=1" style="color: #444; text-decoration: none;">'.$missionprem[$n]['title'].'</a></div>
																			<div align="center"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/pointer.png" width="620" height="40"></div>
																				<table width="100%" border="0" cellspacing="0" cellpadding="5">
																					<tr valign="top">
																						<td width="20"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="20" height="1"></td>
																						<td width="270" align="center">
																							<span style="font-size: 12px; color: #999999;"> 
																							<span style="font-size: 10px; color: #999999;"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/gui.png" width="14" height="12" align="top"></span> You will work for</span><br>
																							<img src="http://ep-test.edit-place.co.uk'.$missionprem[$n]['clientlogo'].'" alt="'.$missionprem[$n]['company_name'].'" vspace="10" style="border-width: 1px; border-style: solid; border-color: #ddd;">
																						</td>
																						<td align="center" style="font-size: 12px;color: #999999;"><span style="font-size: 10px; color: #999999;"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/gui.png" width="14" height="12" align="top"></span> Participations from
																							<div><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="10"></div>
																							<table width="200" border="0" cellspacing="0" cellpadding="0">
																								<tr>
																									<td width="60"><a href=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/cta-orange-l.png" width="60" height="63" border="0" style="display: block"></a></td>
																									<td align="center" bgcolor="#ff6000" style="font-size: 37px; font-style: normal; text-align: center; color: #FFF;"><a href="" style="color: #ffffff;text-decoration: none;">'.date("H A",$missionprem[$n]['publishtime']).'</a></td>
																									<td width="29"><a href=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/cta-orange-r.png" width="29" height="63" border="0" style="display: block"></a></td>
																								</tr>
																							</table>
																						</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																		<tr>
																			<td bgcolor="#ffffff" height="3"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="3" style="display: block;"></td>
																		</tr>
																		<tr>
																			<td bgcolor="#ffffff" height="3"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="3" style="display: block;"></td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
														</a><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="30">';	
										}
								}		
									
		/*************************************************  Mission Liberte **********************************************************/
								if(count($missionnoprem)>0)
								{	
									$newscontent.='
												<br><br>
												<table width="660" cellpadding="0" cellspacing="0" border="0" align="center">
													<tr>
														<td width=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/double-line.gif" width="144" height="4"></td>
														<td width="100%" align="center" style="letter-spacing: -1px; color: #00a1a7; font-family: Arial, Helvetica, sans-serif; font-size: 30px; font-weight: bold;">FREEDOM MISSIONS</td>
														<td width=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/double-line.gif" alt="" width="144" height="4"></td>
													</tr>
												</table>  
												<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="15">';
													
										for($p=0;$p<count($missionnoprem);$p++)
										{
										if($p%2==0)
											$newscontent.='<table width="660" border="0" cellspacing="0" cellpadding="0" align="center"><tr>';
										
										if($p%2==0 && $p==count($missionnoprem)-1)
											$newscontent.='<td colspan="2" style="padding-left:170px;padding-right:150px;">';
										else
											$newscontent.='<td width="50%">';
											
										if(strlen($missionnoprem[$p]['category'])>15)
											$catt = substr($missionnoprem[$p]['category'], 0, 15)."...";
										else		
											$catt = $missionnoprem[$p]['category'];	
											
											$newscontent.='	<a href="http://ep-test.edit-place.co.uk/user/email-login?user='.$user.'&hash='.$password.'&type='.$type.'&nl=1&user_id='.$contrib['identifier'].'&mission_id='.$missionnoprem[$p]['id'].'" style="text-decoration:none;">
															<table width="100%" cellpadding="1" cellspacing="0" border="0" align="center" bgcolor="#3A3A3A" style="border-top-color: #444444; border-top-width:1px; border-top-style:solid;">
																<tr>
																	<td style="color: #eee; font-size: 12px; text-transform:uppercase; font-weight:bold"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="10" height="25" align="absmiddle">'.$catt.' <img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/sep.png" width="29" height="25" align="absmiddle">writing
																	<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/flag/'.$missionnoprem[$p]['language'].'.jpg" alt="'.$missionnoprem[$p]['languagetitle'].'" width="16" height="11" hspace="5" align="absmiddle"></td>
																	<td align="right">'.$missionnoprem[$p]['ptype'].'<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="10" height="1" align="absmiddle"></td>
																</tr>
															</table>
															<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
																<tr>
																	<td bgcolor="#dadada">
																		<table width="100%" border="0" cellspacing="1" cellpadding="8">
																			<tr>
																				<td bgcolor="#ffffff"><div style="font-family: \'helvetica neue\', \'Arial\' serif; font-size: 20px; color: #555; margin-bottom:10px"><a href="" style="color: #444;text-decoration: none;">'.$missionnoprem[$p]['title'].'</a></div>
																					<div><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/pointer-grid.png" width="300" height="28" style="display: block">
																					<table width="100%" border="0" cellspacing="0" cellpadding="5">
																						<tr valign="top">
																							<td width="270" align="center" style="font-size: 10px; color: #999999;"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/gui.png" width="14" height="12" align="absmiddle"> You will work for
																								<div><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="10"></div>
																								<img src="http://ep-test.edit-place.co.uk'.$missionnoprem[$p]['clientlogo'].'" alt="'.$missionnoprem[$p]['company_name'].'" style="border-width: 1px; border-style: solid; border-color: #ddd;">
																							</td>
																							<td align="center" style="font-size: 10px;color: #999999;"><span style="font-size: 10px; color: #999999;"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/gui.png" width="14" height="12" align="absmiddle"></span> Participations from
																								<div><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="10"></div>
																								<table width="132" border="0" cellspacing="0" cellpadding="0">
																									<tr>
																										<td width="44"><a href=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/btn_small-blue-l.png" width="44" height="48" border="0" style="display: block"></a></td>
																										<td height="48" align="center" nowrap bgcolor="#00a1a7" style="font-size: 26px; font-style: normal; text-align: center; color: #FFF;"><a href="" style="color: #ffffff; text-decoration: none;">'.date("H A",$missionnoprem[$p]['publishtime']).'</a></td>
																										<td width="24"><a href=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/btn_small-blue-r.png" width="24" height="48" border="0" style="display: block"></a></td>
																								   </tr>
																								</table>
																							</td>
																						</tr>
																					</table>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
															</a>
														</td>';
												if($p%2==0)
													$newscontent.='<td width="10%"><img src="/img/shim.gif" width="10" height="1" style="display: block"></td>';
												if($p%2!=0 || ($p%2==0 && $p==count($missionnoprem)-1))
													$newscontent.='</tr></table><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="20"/> ';
												
										}
								}			
 
			
			/*************************************************  Devis Premium **********************************************************/
								if(count($devisprem)>0)
								{
								$newscontent.='<br><br>
												<table width="660" cellpadding="0" cellspacing="0" border="0" align="center">
													<tr>
														<td width="110" bgcolor="#ffffff"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="110" height="4"></td>
														<td width="100%" align="center" bgcolor="#ffffff"> <p style="letter-spacing: -1px; color: #c1b46c; font-family: Arial, Helvetica, sans-serif; font-size: 30px; font-weight: bold;">PREMIUM QUOTE</p>
															<p style="color: #999999; font-size:16px">Respond to these quote requests and become a priority candidate for <b>24h</b> if the offer becomes a premium mission
														</td>
														<td width="" valign="top"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/corner.png" alt="" width="110" height="165" style="display: block"></td>
													</tr>
												</table>';     
												
											for($k=0;$k<count($devisprem);$k++)
											{	
												if($k%2==0)
													$newscontent.='<table width="660" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"><tr>';
													
													if($k%2==0 && $k==count($devisprem)-1)
														$newscontent.='<td bgcolor="#ffffff" colspan="2" style="padding-left:170px;padding-right:150px;">';
													else
														$newscontent.='<td bgcolor="#ffffff" width="50%">';
														
													if(strlen($missionnoprem[$p]['category'])>15)
														$pollcatt = substr($devisprem[$k]['category'], 0, 15)."...";
													else		
														$pollcatt = $devisprem[$k]['category'];
													$newscontent.='<a id="devislink" href="http://ep-test.edit-place.co.uk/user/email-login?user='.$user.'&hash='.$password.'&type='.$type.'&nl=1&utm_source=Newsletter&utm_medium=e-mail&utm_campaign=Newsletter&newsletter_id='.$nl_id.'&sent_at='.$todaydate.'" style="text-decoration:none;">
																<table width="98%" cellpadding="1" cellspacing="0" border="0" align="center" bgcolor="#3A3A3A" style="border-top-color: #444444; border-top-width:1px; border-top-style:solid;">
																<tr>
																	<td style="color: #eee; font-size: 12px; text-transform:uppercase; font-weight:bold">
																		<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="10" height="25" align="absmiddle">'.$pollcatt.'
																		<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/sep.png" width="29" height="25" align="absmiddle">writing
																		<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/flag/'.$devisprem[$k]['language'].'.jpg" alt="'.$devisprem[$k]['languagetitle'].'" width="16" height="11" hspace="5" align="absmiddle"> 
																	</td>
																	<td align="right">'.$devisprem[$k]['ptype'].'<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="10" height="1" align="absmiddle"></td>
																</tr>
															</table>
															<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
																<tr>
																	<td bgcolor="#dadada">
																		<table width="100%" border="0" cellspacing="1" cellpadding="8">
																			<tr>
																				<td bgcolor="#ffffff">
																					<div style="font-family: \'helvetica neue\', \'Arial\' serif; font-size: 20px; color: #555; margin-bottom:10px"><a href="http://ep-test.edit-place.co.uk/FO" style="color: #444;text-decoration: none;">'.$devisprem[$k]['title'].'</a></div>
																					<div><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/pointer-grid.png" width="300" height="28" style="display: block">
																					<table width="100%" border="0" cellspacing="0" cellpadding="5">
																						<tr valign="top">
																							<td width="270" align="center" style="font-size: 10px; color: #999999;"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/gui.png" width="14" height="12" align="absmiddle"> You will work for
																								<div><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="10"></div>
																								<img src="http://ep-test.edit-place.co.uk'.$devisprem[$k]['clientlogo'].'" alt="'.$devisprem[$k]['company_name'].'" style="border-width: 1px; border-style: solid; border-color: #ddd;">
																							</td>
																							<td align="center" style="font-size: 10px; color: #999999;"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/gui.png" width="14" height="12" align="absmiddle"> Participations from
																								<div><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/shim.gif" width="1" height="10"></div>
																								<table width="132" border="0" cellspacing="0" cellpadding="0">
																									<tr>
																										<td width="44"><a href=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/btn_small-brown-l.png" width="44" height="48" border="0" style="display: block"></a></td>
																										<td height="48" align="center" nowrap bgcolor="#c1b46c" style="font-size: 26px; font-style: normal; text-align: center; color: #FFF;"><a href="" style="color: #ffffff; text-decoration: none;">'.date("H A",strtotime($devisprem[$k]['publish_time'])).'</a></td>
																										<td width="24"><a href=""><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/btn_small-brown-r.png" width="24" height="48" border="0" style="display: block"></a></td>
																									</tr>
																								</table>
																							</td>
																						</tr>
																					</table>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
															</a>
														</td>';
													if($k%2==0)	
														$newscontent.='<td width="1"><img src="/img/shim.gif" width="1" height="1"></td>';
													if($k%2!=0 || ($k%2==0 && $k==count($devisprem)-1))
													{
														$newscontent.='</tr>
													<tr><td colspan="3"><img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/ship.gif" width="1" height="3" border="0" style="display: block"></td></tr>
													</table>';
													}
												}
								}		
												
												$missionsum=count($missionprem)+count($missionnoprem);
												$newscontent.='<br>
													<p align="center" style="font-size: 22px; font-family: Georgia, \'Times New Roman\', Times, serif; color: #555;">Edit-place live</p>
													<table width="660" border="0" cellspacing="0" cellpadding="0" align="center">
														<tr>
															<td bgcolor="#cccccc">
																<table width="100%" border="0" cellspacing="1" cellpadding="15">
																	<tr>
																		<td bgcolor="#ecebeb">
																			<table width="100%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td bgcolor="#2e2e2e">
																						<table width="100%" border="0" cellspacing="1" cellpadding="10">
																							<tr>
																								<td bgcolor="#3a3a3a"  width="33%" align="center" style="color: #FFF; font-size: 12px;">
																									<div style="font-size: 48px;">'.$stats['participants'].'</div>participants
																								</td>
																								<td bgcolor="#3a3a3a"  width="33%" align="center" style="color: #FFF; font-size: 12px;">
																									<div style="font-size: 48px;">'.$stats['totalUploadedArticles'].'</div>written articles       
																								</td>
																								<td bgcolor="#3a3a3a"  width="33%" align="center" style="color: #FFF;font-size: 12px;">
																									<div style="font-size: 48px;">'.$stats['donation'].' &euro;</div> paid
																								</td>
																							</tr>
																							<tr>
																								<td bgcolor="#3a3a3a"  width="33%" align="center" style="color: #FFF; font-size: 12px;">
																									<div style="font-size: 48px;">'.$missionsum.'</div> new missions
																								</td>
																								<td bgcolor="#3a3a3a"  width="33%" align="center" style="color: #FFF; font-size: 12px;">
																									<div style="font-size: 48px;">'.$stats['newWrites'].'</div> new writers
																								</td>
																								<td bgcolor="#484848" width="33%" align="center" style="color: #aaa; font-size: 12px;">
																									<img src="http://ep-test.edit-place.co.uk/FO/images/newsletter/thanks.png" width="123" height="35" alt="Merci  tous" style="display: block"><div>The Edit-place team</div>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
													<p></p>
													<div align="center" style="padding-bottom: 10px;color: #999"><font size="-2">If you wish to stop receiving our newsletter, please? <a id="unsublink" href="http://ep-test.edit-place.co.uk/user/unsubscribe?unsid='.$contrib['identifier'].'">Click here.</a></font></div> 
											<!-- end main table -->
										</td>
									</tr>
								</table>
							</body>
						</html>';
						
				echo $newscontent;//exit;
				if($num==0)
				{
					$nl_obj=new Ep_User_DailyNewsletter();
						//Reference mail
						$mailref = new Zend_Mail();
						$mailref->addHeader('Reply-To','support@edit-place.com');
						$mailref->setBodyText($newscontent)
							 ->setFrom('support@edit-place.com','Support Edit-place')
							 ->addTo('mailpearls@gmail.com')
							 ->addCc('kavithashree.r@gmail.com') 
							 ->setSubject('Newsletter reference');
						//$mailref->send();
						
						$insertarray=array("content"=>stripslashes($newscontent));
						$nl_obj->updateNewsletter($insertarray,$nl_id);
				}
					$mail = new Zend_Mail();
					$mail->addHeader('Reply-To','support@edit-place.com');
					$mail->setBodyHtml($newscontent)
						 ->setFrom('support@edit-place.com','Support Edit-place')
						 ->addTo($contrib['email'])
						 //->addTo('kavithashree@edit-place.com') 
						 //->setSubject(utf8_decode($object));
						 ->setSubject('Newsletter - test site');
					$mail->send();
					$writerarray[]=$contrib['identifier'];
						
					$num++;
					}
				}
			} 
		
		//DailyNewsletter insertion
		$nl_obj=new Ep_User_DailyNewsletter();
		if(count($writerarray)>0)
		{
			
			$writers=implode(",",$writerarray);	
			$insertarray=array("content"=>$newscontent,"contributors"=>$writers);
			$nl_obj->updateNewsletter($insertarray,$nl_id);
		}
		
		//Comment insertion
		$date=date("Y-m-d");
		$nl_count = $nl_obj->getNLByDate($date);
		
		/*if(count($nl_count)==1)
		{
			$comm_obj=new Ep_Ao_AdComments();
			for($ma=0;$ma<count($NewsletterMissions);$ma++)
			{
				if($NewsletterMissions[$ma]['missioncomment']!="")
				{
					$artids=$ao_obj->getArticles($NewsletterMissions[$ma]['id']);
					
					for($a=0;$a<count($artids);$a++)
					{
						$commentarray=array();
						$commentarray['user_id']=$NewsletterMissions[$ma]['created_user'];
						$commentarray['type']="article";
						$commentarray['type_identifier']=$artids[$a]['id'];
						$commentarray['comments']=$NewsletterMissions[$ma]['missioncomment'];
						//print_r($commentarray);
						$comm_obj->InsertComment($commentarray);
					}
				}
			}
		}*/
	}
	
	public function sendnewslettercronAction()
    {
        $newsletter_obj = new EP_User_Newsletter();
        $usersmailids=$newsletter_obj->getnotsentnewsletter();print_r($usersmailids);//exit;
        $countMails = count($usersmailids);
        if($usersmailids!="NO")
		{
		for($i=0; $i<$countMails; $i++)
        {
            $id = $usersmailids[$i]['newsletterId']; 
            $email_to = $usersmailids[$i]['email']; 
            $email_from = $usersmailids[$i]['mail_from']; 
            $email_subject = $usersmailids[$i]['subject']; 
            $email_message = $usersmailids[$i]['message']; 
            $attachments = $usersmailids[$i]['attachment']; 
            $files = explode("|", $attachments);
            $path = "/home/sites/site8/web/BO/attachments/";
            //////////////////////////////////////////////////
            if($attachments != NULL)
                $ok=$this->mail_attachment($files, $path, $email_to, $email_from, $email_subject, $email_message);
            else
            {
                $mail = new Zend_Mail();
                $mail->addHeader('Reply-To',$email_from);
                $mail->setBodyHtml($email_message)
                    ->setFrom($email_from)
                    ->addTo($email_to)
                    ->setSubject($email_subject);
                $ok = $mail->send();
            }
            
			//update newsletter status 
            $data = array("status"=>"sent");
            $query = "id= ".$id;
            $newsletter_obj->updateNewsletter($data,$query);
        }
        
		if($ok) 
            echo "sent successfully";
        else
            die("Sorry but the email could not be sent. Please go back and try again!");
    }	
	else
		echo "NO Newsletters";
		
    }
	
	public function mail_attachment($files, $path, $mailto, $from_mail, $subject, $message) 
	{
        $uid = md5(uniqid(time()));
        $header = "From: ".$from_mail." <".$from_mail.">\r\n";
        $header .= "Reply-To: ".$from_mail."\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed;\r\n\tboundary=\"$uid\"\r\n\r\n";
        $header .= "This is a multi-part message in MIME format.\r\n";
        $header .= "--".$uid."\r\n";
        $header .= "Content-type:text/html; charset=iso-8859-1\r\n";
        $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $header .= $message."\r\n\r\n";
			
			foreach ($files as $filename) 
			{
				$file = $path.$filename;
				$name = basename($file);
				$file_size = filesize($file);
				$handle = fopen($file, "r");
				$content = fread($handle, $file_size);
				fclose($handle);
				$content = chunk_split(base64_encode($content));
				$file_name =  explode("_",$filename);
				$header .= "--".$uid."\r\n";
				$header .= "Content-Type: application/octet-stream; name=\"".$file_name[2]."\"\r\n"; // use different content types here
				$header .= "Content-Transfer-Encoding: base64\r\n";
				$header .= "Content-Disposition: attachment; filename=\"".$file_name[2]."\"\r\n\r\n";
				$header .= $content."\r\n\r\n";
			}
        $header .= "--".$uid."--";
        return mail($mailto, $subject, "", $header);
    }
	
	public function infonewsletterAction()
	{
		$user_obj=new Ep_User_User();
		
		$contributors=$user_obj->getGmailContributors();
		
		foreach($contributors as $contrib)
		{
			if($contrib['profile_type']=='senior')
				$profilesrc="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-senior-header.png";
			elseif($contrib['profile_type']=='junior')	
				$profilesrc="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-junior-header.png";	
			else
				$profilesrc="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-debutant-header.png";	
			
			if($contrib['first_name']!='')
				$name=ucfirst($contrib['first_name']).'&nbsp;'.ucfirst($contrib['last_name']);
			else
			{
				$mailname=explode("@",$contrib['email']);
				$name=$mailname[0];
			}
			//<div align="center" style="padding-bottom: 10px;color: #999"><font size="-2">Des problmes pour visualiser cet email ? <a href="http://ep-test.edit-place.co.uk/ep-gmail-alert.php?cid='.$contrib['identifier'].'">Visualisez le dans votre navigateur</a></font></div>	
			$newscontent='';
			$newscontent.=' <html lang="fr">
								<head>
									<meta name="viewport" content="width=device-width;initial-scale=1.0; user-scalable=1;" />
								    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
									<title>Les annonces du jour d\'edit-place</title>
								</head>
								<body bgcolor="#e4e4e4" style="margin: 0; padding: 0; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;">
									<style type="text/css">
									table {	margin-top: 0; }
									</style>
									
									<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#3a3a3a">
										<tr>
											<td bgcolor="#3a3a3a" width="100%">    
												<table width="660" cellpadding="0" cellspacing="0" border="0" align="center" height="70px">
													<tr>
														<td width="50%"  height="70px"><a href="http://www.edit-place.com"><img src="http://www.edit-place.com/extra/epgmail/logo.png"  width="163" height="25" border="0"></a></td>
														<td width="50%" align="right" nowrap style="color: #999999; font-size: 12px;"> <a href="#" style="color: #999999;text-decoration: none;">'.stripslashes($name).'</a>  
														<img src="'.$profilesrc.'" align="absmiddle"></td>
													</tr>
												</table>   
											</td>
										</tr>
									</table>
									<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#e4e4e4">
										<tr>
											<td bgcolor="#e4e4e4" width="100%">    
												<table width="660" cellpadding="15" cellspacing="0" border="0" align="center" style="box-shadow: 0 2px 2px #ccc; border-bottom: solid 1px #aaa">
													<tr>
														<td bgcolor="#f3f3f3" style="font-size: 14px;">
															<p style="padding-left:11px; padding-right:11px;">Cher contributeur,</p>
															<p style="font-size: 24px; font-weight: 300; padding-left:11px; padding-right:11px;">Votre messagerie Gmail a chang&eacute;, et vos alertes Edit-place peuvent appara&icirc;tre dans un onglet o&ugrave; elles sont moins accessibles</p>
															<img src="http://www.edit-place.com/extra/epgmail/gmail-email.png">
															<p style="padding-left:11px; padding-right:11px;">Afin de continuer &agrave; recevoir correctement nos emails, rien de plus simple : </p>
															<ol>
																<li>Retrouvez les emails envoy&eacute;s par Edit-place dans l&rsquo;onglet  Promotions </li>
																<li>Faites glisser cet email dans l\'onglet "Principale" et cliquez sur OUI !</li>
															</ol>
															<p style="padding-left:11px; padding-right:11px;"><br>
															A bient&ocirc;t sur Edit-place !<br><br>L\'&eacute;quipe<br></p>
															<img src="http://ep-test.edit-place.co.uk/gmail_tracking.php?mailid='.$contrib['identifier'].'" width="0" height="0"/>
														</td> 
													</tr>
												</table>
												<br><br> 
											</td>
										</tr>
									</table>
								</body>
							</html>';
				//echo $newscontent;
				$mail = new Zend_Mail();
				$mail->addHeader('Reply-To','francois.pelletier@edit-place.com');
				$mail->setBodyHtml($newscontent)
					 ->setFrom('mailpearls@yahoo.com','Francois Pelletier')
					 ->addTo($contrib['email'])
					 //->addTo('kavithashree.r@gmail.com')
					 //->addCc('kavithashree.r@gmail.com')
					 ->setSubject("Ouvrirez vous cet email d'Edit-Place... ?");
				//$mail->send();	
		}
	}
	

	//Auto publish liberte after 24h
	public function liberteautopublishAction()
	{
		$article_obj=new Ep_Ao_Article();
		$articles=$article_obj->checkPublishArticle();
		//print_r($articles);exit;
		for($a=0;$a<count($articles);$a++)
		{
			$part_obj=new Ep_Ao_Participation();
			$participate_obj=new Ep_Participation_Participation();
			$royalty_obj=new Ep_Royalty_Royalties();
				
				$paricipationdetails=$participate_obj->getParticipateDetails($articles[$a]['partid']);
				if($royalty_obj->checkRoyaltyExists($articles[$a]['id'])=='NO')
				{
					$royalty_obj->participate_id=$paricipationdetails[0]['participateId'];
					$royalty_obj->article_id=$paricipationdetails[0]['article_id'];
					$royalty_obj->user_id=$paricipationdetails[0]['user_id'];
					$royalty_obj->price=$paricipationdetails[0]['price_user'];
					$royalty_obj->currency=$paricipationdetails[0]['currency'];
					$royalty_obj->insert();
					
					//Client name
					$client_obj=new Ep_User_Client();
					$clientdetails=$client_obj->getClientFulldetails($paricipationdetails[0]['clientId']);
					if($clientdetails[0]['company_name']!="")
						$cname=$clientdetails[0]['company_name'];
					else
						$cname=$clientdetails[0]['email'];
					
					//Writer info					
					$user_obj=new Ep_User_UserPlus();
					$writerdetails=$user_obj->getUserPlus($paricipationdetails[0]['user_id']);
					
					$body='<table border="0" cellpadding="2" cellspacing="2">
							<tr><td><b>Article title :</b></td><td> '.$paricipationdetails[0]['title'].'</td></tr>
							<tr><td><b>AO title :</b> </td><td>'.$paricipationdetails[0]['deliveryTitle'].'</td></tr>
							<tr><td><b>Client :</b> </td><td>'.$cname.'</td></tr>
							<tr><td><b>Writer :</b> </td><td>'.$writerdetails[0]['first_name'].'&nbsp;'.$writerdetails[0]['last_name'].'</td></tr>
							<tr><td><b>Writer email :</b> </td><td>'.$writerdetails[0]['email'].'</td></tr>
							<tr><td><b>Royalties</b> :</b> </td><td>'.$paricipationdetails[0]['price_user'].'&euro; </td></tr>
						</table>	';
						
					$mail = new Zend_Mail();
					$mail->addHeader('Reply-To','support@edit-place.com');
					$mail->setBodyHtml($body)
						 ->setFrom('support@edit-place.com','Support Edit-place')
						 ->addTo('mailpearls@gmail.com')
						 ->setSubject('Royalty added through cron test site EN');
					if($mail->send())
						return true; 
				}
				
				//Update Participation
				$where_art=" id='".$articles[$a]['partid']."'";
				$arr_art=array();
				$arr_art['status']='published';
				$arr_art['current_stage']='client';
				$part_obj->updateparticipation($arr_art,$where_art);  
				
				$invoiceid= $articles[$a]['id'];
				$invoicedir='/home/sites/site7/web/FO/invoice/client/'.$articles[$a]['user_id'].'/';
			   
				if(!file_exists($invoicedir.'/'.$invoiceid.'.pdf'))
				{
					$pay_obj = new Ep_Ao_PaymentArticle();
					$art_obj = new Ep_Ao_Article();
					$country_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);

					//Payment details
					$payment=$pay_obj->getpaymentdetails($invoiceid);
				
					//Dates
					setlocale(LC_TIME, 'fr_FR');
					$date_invoice_full= strftime("%e %B %Y",strtotime($payment[0]['delivery_date']));
					$date_invocie = date("d/m/Y",strtotime($payment[0]['delivery_date']));
					$date_invoice_ep=date("Y/m",strtotime($payment[0]['delivery_date']));

				   //Address
					$profileinfo=$pay_obj->getClientdetails($articles[$a]['user_id']);
					$address=$profileinfo[0]['company_name'].'<br>';
					$address.=$profileinfo[0]['address'].'<br>';
					$address.=$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city'].'  '.$country_array[$profileinfo[0]['country']].'<br>';

					//Invoice details
					$invoice_details_pdf='
						<div align="center" style="font-size:16pt;"><b>Appel d\'offres : '.$payment[0]['title'].'</b></div>
							<table class="change_order_items">
											<tbody>
												<tr>
													<th>DESIGNATION</th>
													<th>MONTANT</th>
													<th>MONTANT PAY&Eacute;</th>
												</tr>';

						$total=0;
						$total=number_format($payment[0]['amount'],2);
						
						$invoice_details_pdf.='<tr>
													<td>'.$payment[0]['title'].'</td>
													<td class="change_order_total_col">'.number_format($total,2,',','').'</td>
													<td class="change_order_total_col">'.number_format($total,2,',','').'</td>
													</tr>';

						$invoice_details_pdf.='<tr>
													<td style="border-top:1pt solid black;text-align:right;margin-right:10px;font-size: 12pt;" colspan="2">
														Total de la prestation HT
													</td>
													<td style="border-top:1pt solid black;font-size: 12pt;" class="change_order_total_col" >
														'.number_format($total,1,',','').'
													</td>
												</tr>
											</tbody>
										</table>';

					//Pay info number
					$payinfo_number="";

					if($payment[0]['amount']!="" && $payment[0]['client_type']!="personal")		
					{	
					  //Tax details
					   $tax=(($total*$payment[0]['tax'])/100);
					   $tax_details_pdf='<table class="change_order_items">
														<tbody>
															<tr>
																<td>TVA</td>
																<td>taux : '.str_replace('.', ',',$payment[0]['tax']).'%</td>
																<td class="change_order_total_col" style="border-top:1pt solid black;text-align:right;font-size: 12pt;">'.number_format($tax,2,',','').' &#x80; </td>
															</tr>
														</tbody>
														</table>';
					}
					else
						$tax=0;
						
					/**Final Total**/
					$final_invoice_amount='<table class="change_order_items" width="100%">
												<tr>
													<td  style="width:82%;font-size:12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Montant TTC</td>
													<td style="width:18%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format(($total+$tax),2,',','').' &#x80;</td>
												</tr>
											</table>';
					if(!is_dir($invoicedir))
					{
					   mkdir($invoicedir,0777);
					   chmod($invoicedir,0777);
					}
					include('/home/sites/site7/web/FO/dompdf/dompdf_config.inc.php');
					$html=file_get_contents('/home/sites/site7/web/FO/views/scripts/Client/Client_invoice_pdf.phtml');
					$html=str_replace('$$$$invoice_details_pdf$$$$',$invoice_details_pdf,$html);
					$html=str_replace('$$$$tax_details_pdf$$$$',$tax_details_pdf,$html);
					$html=str_replace('$$$$final_invoice_amount$$$$',$final_invoice_amount,$html);
					$html=str_replace('$$$$date_invoice_full$$$$',$date_invoice_full,$html);
					$html=str_replace('$$$$date_invoice$$$$',$date_invocie,$html);
					$html=str_replace('$$$$address$$$$',$address,$html);
					$html=str_replace('$$$$payinfo_number$$$$',$payinfo_number,$html);
					$html=str_replace('$$$$date_invoice_ep$$$$',$date_invoice_ep,$html);
					$html=str_replace('$$$$invoice_identifier$$$$',$payment[0]['payid'],$html);

						   if ( get_magic_quotes_gpc() )
							   $html = stripslashes($html);

							//echo  $html;exit;
						   //$old_limit = ini_set("memory_limit", "16M");

							$dompdf = new DOMPDF();
							$dompdf->load_html( $html);
							$dompdf->set_paper("a4");
							$dompdf->render();error_reporting(0);
							
							$pdf = $dompdf->output();

					file_put_contents($invoicedir.'/'.$invoiceid.'.pdf', $pdf);
					ob_clean();
					flush();
				}	
		}
	}	 
	/**Send invoices through mail everymonth **/
	public function sendmonthlyinvoiceAction()
	{
		$currentmonth=date("m");
		$currentyear=date("Y");
		
		//Fetching clients who have invoices for this month
		$pay_obj=new Ep_Ao_PaymentArticle();
		$clientlist=$pay_obj->ListPaymentClient($currentmonth,$currentyear);
		
		if(count($clientlist)>0)
		{
			$main_array=array();
			for($c=0;$c<count($clientlist);$c++)
			{
				$payartlist=$pay_obj->ListPaymentsByMonth($clientlist[$c]['user_id'],$currentmonth,$currentyear);
				
				//Invoice zip
				$invoice_array=array();
				for($z=0;$z<count($payartlist);$z++)
				{
					$invoicepath='/home/sites/site7/web/FO/invoice/client/'.$clientlist[$c]['user_id'].'/'.$payartlist[$z]['article'].'.pdf';
					
					if(!file_exists($invoicepath))
						$this->generateInvoice($payartlist[$z]['article']);
					
					$invoice_array[]=$invoicepath;
					
				}
				//print_r($files_array);exit;	
				$zipname=$payartlist[0]['company_name'].'_'.date("M", mktime(0, 0, 0, $currentmonth, 10)).'_'.$currentyear.'_Invoice';
				$invoicezipname='/home/sites/site7/web/FO/invoice/zip/'.$zipname.'.zip';
				$this->create_zip($invoice_array,$invoicezipname);
				
				//XLS
				$this->generateInvoiceXLS($clientlist[$c]['user_id'],$currentmonth,$currentyear);  
				
				//Client zip
				$client_array=array();
				$client_array[]=$invoicezipname;
				$client_array[]='/home/sites/site7/web/FO/invoice/client/xls/'.$payartlist[0]['company_name'].'_'.date("M", mktime(0, 0, 0, $currentmonth, 10)).'_'.$currentyear.'_XLS.xls';;
				$clientzipname='/home/sites/site7/web/FO/invoice/zip/'.$payartlist[0]['company_name'].'.zip';
				$this->create_zip($client_array,$clientzipname);
				
				$main_array[]=$clientzipname;
			}
			//print_r($main_array);exit;
			$mainzipname='/home/sites/site7/web/FO/invoice/zip/'.date("M", mktime(0, 0, 0, $currentmonth, 10)).'-'.$currentyear.'.zip';
			$this->create_zip($main_array,$mainzipname);
			
			//header('Content-Type: application/zip;charset=utf-8');
			//header('Content-Disposition: attachment; filename=cronzip.zip');
			//readfile($mainzipname);
			
			//Mail
			$email_to='jwolff20@gmail.com,mailpearls@gmail.com';
			$from_mail='support@edit-place.com,Support Edit-place';
			$email_subject='Mission liberte Invoices of '.date("M", mktime(0, 0, 0, $currentmonth, 10)).' - '.$currentyear;
			$email_message='Please find the attachment';
				$zip=date("M", mktime(0, 0, 0, $currentmonth, 10)).'-'.$currentyear.'.zip';
			$path='/home/sites/site7/web/FO/invoice/zip/';
			
			$uid = md5(uniqid(time()));
			$header = "From: ".$from_mail." <".$from_mail.">\r\n";
			$header .= "Reply-To: ".$from_mail."\r\n";
			$header .= "MIME-Version: 1.0\r\n";
			$header .= "Content-Type: multipart/mixed;\r\n\tboundary=\"$uid\"\r\n\r\n";
			$header .= "This is a multi-part message in MIME format.\r\n";
			$header .= "--".$uid."\r\n";
			$header .= "Content-type:text/html; charset=iso-8859-1\r\n";
			$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$header .= $email_message."\r\n\r\n";
			
				$file = $path.$zip;
				$name = basename($file);
				$file_size = filesize($file);
				$handle = fopen($file, "r");
				$content = fread($handle, $file_size);
				fclose($handle);
				$content = chunk_split(base64_encode($content));
				$header .= "--".$uid."\r\n";
				$header .= "Content-Type: application/octet-stream; name=\"".$name."\"\r\n"; // use different content types here
				$header .= "Content-Transfer-Encoding: base64\r\n";
				$header .= "Content-Disposition: attachment; filename=\"".$name."\"\r\n\r\n";
				$header .= $content."\r\n\r\n";
				$header .= "--".$uid."--";
				mail($email_to, $email_subject, "", $header);
		
			//$this->mail_attachment($files, $path, $email_to, $email_from, $email_subject, $email_message);
		}
		
	
	}
	
	public function create_zip($files = array(),$destination = '',$overwrite = true)
	{
		if(file_exists($destination) && !$overwrite) { return false; }
		  
		  $valid_files = array();
		  
			if(is_array($files)) {
				foreach($files as $file) {
			
				  if(file_exists($file)) {
					$valid_files[] = $file;
				  }
				}
			}

			if(count($valid_files)) {
				$zip = new ZipArchive();
				if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			
			foreach($valid_files as $file) {
			  $zip->addFile($file, basename($file));
			}
			
			$zip->close();
			return file_exists($destination);
		  }
		  else
		  {
			return false;
		  }
	}
	
	public function generateInvoiceXLS($client,$month,$year)
	{
		$pay_obj=new Ep_Ao_PaymentArticle();
		$paymentartlist=$pay_obj->getPaymentLiberte($client,$month,$year);
			
		$xlsFile = '/home/sites/site7/web/FO/invoice/client/xls/'.$paymentartlist[0]['company_name'].'_'.date("M", mktime(0, 0, 0, $month, 10)).'_'.$year.'_XLS.xls';
		$fh = fopen($xlsFile, 'w') or die("can't open file");
			
			$stringData.='<table style="border: 3px solid;" width="75%">
							<tr>
								<td style="color:#0066FF;font-size:32px;font-weight:bold;" width="50%">EDIT-PLACE.COM</td>
								<td style="font-size:32px;font-weight:bold;text-align:right" width="50%">FACTURE</td> 
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									16 Rue Jesse Owens<br>
									93200 SAINT DENIS<br>
									RCS Bobigny B 521 287 193<br>
									TVA INTRA COMMUNAUTAIRE : FR43-521287193.<br>								
								</td>
								<td>
									<b>DATE :</b> '.date("F d,Y").'<br>
									<b>N&deg; FACTURE :</b>'.date("m/Y").' 
									<br><br>
									<b>'.$paymentartlist[0]['company_name'].'</b><br>
									'.$paymentartlist[0]['address'].' <br>
									'.$paymentartlist[0]['zipcode'].' '.$paymentartlist[0]['city'].'
								</td>	
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td colspan="3" width="100%">
								<table border="1" width="100%">
									<tr style="background:rgb(212, 227, 235);">
										<th>DESCRIPTION</th>
										<th>NBRE d\'articles</th>
										<th>Prix  H.T.(&euro;)</th>
										<th>Montant(&euro;)</th>
									</tr>
									<tr>
										<td style="background:rgb(204, 204, 204)">R&eacute;daction d\'articles - '.$paymentartlist[0]['company_name'].'</td>
										<td></td>
										<td></td>
										<td></td>
									</tr>';
						$total=0;
						$totalpay=0;
						for($p=0;$p<count($paymentartlist);$p++)
						{
							$stringData.='<tr>
											<td nowrap>'.utf8_encode($paymentartlist[$p]['title']).'</td>
											<td>1</td>
											<td>'.number_format($paymentartlist[$p]['amount'],2,'.','').'</td>
											<td style="background:#FFCC99">'.number_format($paymentartlist[$p]['amount'],2,'.','').'&euro;</td>
										</tr>';	
							$total+=$paymentartlist[$p]['amount'];
							$totalpay+=$paymentartlist[$p]['amount_paid'];
						}
						
						$taxamount=0.2*$total;
						$stringData.='</table>
									</td></tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
										<td></td>
										<td align="right">
											<table width="80%">
												<tr>
													<td>SOUS-TOTAL H.T.</td>
													<td style="border:1px solid;">'.number_format($total,2,'.','').'&euro;</td>
												</tr>
												<tr>
													<td>TAUX DE T.V.A.</td>
													<td style="border:1px solid;">20%</td>
												</tr>
												<tr>
													<td>T.V.A.</td>
													<td style="background:#FFCC99;border:1px solid;">'.number_format($taxamount,2,'.','').'&euro;</td>
												</tr>
												<tr>
													<td>Acompte </td>
													<td></td>
												</tr>
												<tr>
													<td><b>TOTAL TTC</b></td>
													<td style="background:#FFFF99;border:1px solid;"><b>'.number_format($totalpay,2,'.','').'&#8364;</b></td>
												</tr>
											</table>	
										</td>
									</tr><tr><td>&nbsp;</td></tr>';	
										
						$stringData.='<tr>
										<td colspan="2">
											TVA pay&eacute;e sur les d&eacute;bits<br>
											Aucun escompte accord&eacute; en cas de paiement anticip&eacute;.<br>
											<span style="color:red;font-weight:bold;">Date d\'&eacute;ch&eacute;ance 30 jours</span> - Tout retard de paiement &agrave; &eacute;ch&eacute;ance  entra&icirc;ne le paiement d\'int&eacute;r&ecirc;ts moratoires d\'un taux &eacute;gal &agrave; trois fois<br>
											le taux d\'int&eacute;r&ecirc;t l&eacute;gal en vigueur, ainsi que le paiement de frais forfaitaires de mise en recouvrement d\'un montant de 40 Euros<br>
											BNP PARIBAS FACTOR devra &ecirc;tre avis&eacute; de toute demande de rensiegnements ou r&eacute;clamations.
										</td>
									</tr><tr><td>&nbsp;</td></tr>';		

						$stringData.='<tr align="center">
										<td colspan="2">
											<div style="padding-left:300px;">
											<table style="border:1px solid;">
												<tr>
													<td>
													<span style="font-weight:bold">Coordonn&eacute;es bancaires pour le paiement:</span><br>
													BNP PARIBAS FACTOR : 51 boulevard des Dames - 13345 Marseille<br>
													Cedex 20<br>
													RIB: 18020 00001 14851000000 66<br>
													RIB: 18020 00001 14851000000 66
													</td>
												</tr>
											</table>
											</div>											
										</td>
									</tr><tr><td>&nbsp;</td></tr>';	

						$stringData.='<tr>
										<td colspan="2">
										Pour &ecirc;tre lib&eacute;ratoire, le r&egrave;glement de cette facture doit &ecirc;tre effectu&eacute; directement &agrave; l\'ordre de BNP PARIBAS FACTOR <br>
										(coordonn&eacute;es ci-dessus) qui le re&ccedil;oit par subrogation dans le cadre d\'un contrat d\'affacturage
										</td>
									</tr><tr><td>&nbsp;</td></tr>';	
						
						$stringData.='<tr>
									<td colspan="2" style="text-align:center;color:#0066FF;">
									EDIT-PLACE.COM - 16 rue Jesse Owens - 93200 St DENIS LA PLAINE - T&eacute;l. 33 (0) 1 77 68 64 61 - contact@edit-place.com<br> 
									S.A.R.L AU CAPITAL DE 20.140 &euro; - RCS BOBIGNY 521 287 193 - N&deg;INTRACOMMUNAUTAIRE : FR 43521287193 
									</td>
								</tr><tr><td>&nbsp;</td></tr>';	
								
						$stringData.='</table>';					
		//echo $stringData;exit;	
		fwrite($fh, $stringData);
		fclose($fh);
	}
	
	public function aoPrivateSingleAutomaticAction(){
	
		$user_obj = new Ep_User_User();
		$privateDelivaryList=$user_obj ->getPrivateDelivaryList();
		//print_r($privateDelivaryList);
		//echo "test";exit;
		if(!empty($privateDelivaryList) && $privateDelivaryList!='NO'){
			$participate_obj=new Ep_Participation_Participation();
			foreach($privateDelivaryList as $key => $value){
				
				//Only For BO 
				if($value['created_by_type']=='BO'){
					$time='';
					switch($value['profileType']){
						case 'senior': $time=$value['st'];
						break;
						case 'junior': $time=$value['jt'];
						break;
						case 'sub-junior': $time=$value['sjt'];
						break;
					}
					if($time==''){
						$time=$this->config['sc_time'];//2days
					}
					
					$lessPrice_obj=new Ep_Participation_Participation();
					$expires=time()+(60*$time);
					//echo $expires;
					$articleIdentifier=$value['article'];
					$userIdentifier=$value['user_id'];
					$participationId=$value['participation'];
					/**Updating 48hours OR Submission User Time allwed to submit the article**/
					$data_array = array("article_submit_expires"=>$expires);////////updating
					$query="id='".$participationId."'";
					$lessPrice_obj->updateArticleSubmitExpires($data_array,$query);
					
					/**Sending Auto Accept and Refuse Mails**/
					/** The Auto Accept mail Will be sent to Contributer and
					 * Refuse mails will not be Sent as he is only One person to Bid as single person private  **/
					$this->seniorSelectionAutoEmails($articleIdentifier,$userIdentifier,$expires);
					//Update Participation from bid_premium to bid Automatically
				$data = array("status"=>"bid","selection_type"=>"auto");////////updating
				$query = "article_id= '".$value['article']."' AND id = '".$value['participation']."'";
				$participate_obj->updateParticipation($data,$query);
				
			
				}
			}
		//echo "<pre>";print_r($privateDelivaryList);
		}
	
	}
	
	/*
	 * function AoCorrectionPrivateSingleAutomaticAction
	 * Correction AO private with 1 person -> selection automatic once corrector has participated.
	 * Tables used : Delivary , Article , Contributors , CorrectorParticipation
	 *
	 */
	public function aoCorrectionPrivateSingleAutomaticAction()
	{
		$user_obj = new Ep_User_User();
		$privateCorrectionList=$user_obj ->getPrivateCorrectionList();
		//echo "<pre>";print_r($privateCorrectionList);exit;
		
		if(!empty($privateCorrectionList) && $privateCorrectionList!='NO')
		{
			$participate_obj=new Ep_Participation_CorrectorParticipation();
			foreach($privateCorrectionList as $key => $value)
			{
					$time='';
					switch($value['profileType']){
						case 'senior': $time=$value['st'];
						break;
						case 'junior': $time=$value['jt'];
						break;
					}
					if($time==''){
						$time=$this->config['correction_sc_submission'];//2days
					}
					$expires=time()+(60*$time);
					
					//Update CorrectionParticipation from bid_premium to bid Automatically
					$data = array("status"=>"bid","corrector_submit_expires"=>$expires,"selection_type"=>"auto");
					$participate_obj->updateParticipationDetails($data,$value['participation']);
				
					//sending selection mail 
					$automail=new Ep_Ticket_AutoEmails();
					$parameters['AO_end_date']=date("d/m/Y",$expires)." &agrave; ".date("H:i:s",$expires);
					$parameters['article_title']=$value['articleTitle'];
					$parameters['ongoinglink']="/contrib/mission-corrector-deliver?article_id=".$value['article'];
					 
					  if($value['profileType']=='senior')
						  $parameters['resubmit_time']=$value['srt'];
					  else
						  $parameters['resubmit_time']=$value['jrt'];
						  
						if($value['correction_resubmit_option']=="day")
							$parameters['resubmit_time']=($parameters['resubmit_time']/(60*24)). " jour(s)";
						elseif($value['correction_resubmit_option']=="hour")	
							$parameters['resubmit_time']=($parameters['resubmit_time']/60). " heure(s)";
						else
							$parameters['resubmit_time']=$parameters['resubmit_time']. " min(s)";
						$parameters['resubmit_time']="<b>".$parameters['resubmit_time']."</b>";
					$automail->messageToEPMail($value['user_id'],28,$parameters);
			}
		}
	}
	
	//Fetching Configuration
	public function getConfiguredval($constraint)
	{
		$conf_obj=new Ep_User_Configuration();
		$conresult=$conf_obj->getConfiguration($constraint);
		return $conresult;
	}	
	////progress bar///
    public function progressbarAction()
    {  
        $aoObject=new EP_AO_Delivery();
        $del_obj=new EP_AO_Article();
        $part_obj=new EP_Participation_Participation();
        $crtpart_obj=new Ep_Participation_CorrectorParticipation();
        $user_obj=new Ep_User_User();
        $searchParams['sorttype']="allongoing";
        $ongoingList=$del_obj->getAllArticles();
        if($ongoingList != 'NO')
        {
            for($i=0; $i<count($ongoingList); $i++)
            {
                $artdetails[$i] = $del_obj->getArticleDetails($ongoingList[$i]);
                $percentage = 0;
                if($artdetails[$i][0]['correction'] == 'no')
                {
                    $newuser = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'bid_premium', 'contributor');
                    if($newuser == 'YES')
                        $percentage = 0;
                    $userselected = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'bid_temp', 'contributor');
                    if($userselected == 'YES')
                        $percentage = 15;
                    $useryettowrite = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'bid', 'contributor');
                    if($useryettowrite == 'YES')
                        $percentage = 30;
                    $plagiarism = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'plag_exec', 'stage0');
                    if($plagiarism == 'YES')
                        $percentage = 45;
                    $stage1 = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'under_study', 'stage1');
                    if($stage1 == 'YES')
                        $percentage = 65;
                    $stage2 = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'under_study', 'stage2');
                    if($stage2 == 'YES')
                        $percentage = 85;
                    $published = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'published', 'client');
                    if($published == 'YES')
                        $percentage = 100;
                }
                elseif($artdetails[$i][0]['correction'] == 'yes')
                { // checkCrtRecordPresent
                    $newuser = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'bid_premium', 'contributor');
                    if($newuser == 'YES')
                        $percentage = 0;
                    $userselected = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'bid_temp', 'contributor');
                    if($userselected == 'YES')
                        $percentage = 12;
                    $useryettowrite = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'bid', 'contributor');
                    if($useryettowrite == 'YES')
                        $percentage = 25;
                    $useryettowritetimeout = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'time_out', 'contributor');
                    if($useryettowritetimeout == 'YES')
                        $percentage = 25;
                    $plagiarism = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'plag_exec', 'stage0');
                    if($plagiarism == 'YES')
                        $percentage = 37;
                    //////////plagiarism is done in writer and waiting to corrector paritcipation////
                    $plagiarism = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'under_study', 'corrector');
                    if($plagiarism == 'YES')
                    {
                        $crtparticipated = $crtpart_obj->checkCrtParticipation($artdetails[$i][0]['id']);
                        if($crtparticipated == 'NO')
                            $percentage = 50;
                    }
                    $crtnotselected = $crtpart_obj->checkCrtRecordPresent($artdetails[$i][0]['id'], 'bid_corrector', 'contributor');
                    if($crtnotselected == 'YES')
                        $percentage = 50;
                    $crtselected = $crtpart_obj->checkCrtRecordPresent($artdetails[$i][0]['id'], 'bid_temp', 'contributor');
                    if($crtselected == 'YES')
                        $percentage = 50;
                    $crtyettowrite = $crtpart_obj->checkCrtRecordPresent($artdetails[$i][0]['id'], 'bid', 'contributor');
                    if($crtyettowrite == 'YES')
                        $percentage = 62;
                    $moderate = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'disapproved_temp', 'corrector');
                    if($moderate == 'YES')
                        $percentage = 85;
                    $stage2 = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'under_study', 'stage2');
                    if($stage2 == 'YES')
                        $percentage = 97;
                    $published = $part_obj->checkRecordPresent($artdetails[$i][0]['id'], 'published', 'client');
                    if($published == 'YES')
                        $percentage = 100;
                }
                  echo "<pre>".$artdetails[$i][0]['id']."--".$artdetails[$i][0]['correction']."--".$percentage;
                $data = array("progressbar_percent"=>$percentage);////////updating
                $query = "id= '".$artdetails[$i][0]['id']."'";
                $del_obj->updateArticle($data,$query);
                //  echo "<pre>".$artdetails[$i][0]['id']."--".$artdetails[$i][0]['correction']."--".$percentage;
            }
        }
    }

     /*
	 * Function doubleSelectionCheckAction
	 * Use it To Find Double Entry For Ongoing Ao's and Mail to manager
	 * @auther vinayak k
	 *
	 */
	
    public function doubleSelectionCheckAction(){
		//$cron_obj = new Ep_Ao_CronLock();
      //  $cron = $cron_obj->getCronLock('doubleSelectionCheck');
       // $lockstatus = $cron[0]['locked'];
      //  if($lockstatus == 'locked')
     //   { echo "in process"; exit; }
       // else
      //  {	//Lock Cron 
            //$this->updateCronLock('doubleSelectionCheck', 'locked');
            $part_obj=new EP_Participation_Participation();
			$artList=$part_obj->checkAoParticipateDuplicates();
		
            if(empty($artList)){
				return;
			}
			$duplist=array();
			$artObj=new Ep_Article_Article();
			$savedArt=array();
			$savedArt=$artObj->getDupCronArticleList();
			
			if(!empty($savedArt)){
				foreach($artList as $key => $value){
					foreach($savedArt as $key2 => $val2){
						
						if($val2==$value['id']){
							unset($artList[$key]);
						}
					}	
				}
			}
			
			if(empty($artList)){
				return;
			}
			$ticket=new Ep_Ticket_Ticket();
			$automail=new Ep_Ticket_AutoEmails();
			foreach($artList as $key => $value){
				if($value['count']>1){
					$duplist[$value['delivery_id']][]=$value;
					if(!isset($duplist[$value['delivery_id']]['aoDetails'])){
						
						$aodet=array(
								'created_user'=>$value['created_user'],
								'client'=>$value['client'],
								'deliveryTitle'=>$value['deliveryTitle'],
								'deliveryId'=>$value['delivery_id']
								);
						$duplist[$value['delivery_id']]['aoDetails']=$aodet;
						$bo_user=$aodet['created_user'];
						$duplist[$value['delivery_id']]['bo_user_id']=$bo_user;
						$duplist[$value['delivery_id']]['bo_user_name']=$ticket->getUserName($bo_user,TRUE);
						$userDet=$automail->getUserEmailId($bo_user);
						$duplist[$value['delivery_id']]['bo_user_email']=$userDet['email'];
						$duplist[$value['delivery_id']]['ao_link']='<a href="http://admin-test.edit-place.com/ao/ongoingao?submenuId=ML2-SL4&client='.$aodet['client'].'&ao='.$aodet['deliveryId'].'">Delivary</a>';
					}
					
				}
			}
			
			$bolist=array();
			if(!empty($duplist)){
				foreach($duplist as $key =>$value){
					$bolist[$value['bo_user_id']][]=$value;
				}
			}
			
			
			if(!empty($bolist)){
				$toSaveArt=array();
				foreach($bolist as $key =>$value){
					$articles=array();
					$artNamesList=array();
					if(!empty($value)){
						foreach($value as $key2 => $value2){
							
							foreach($value2 as $key3 => $value3){
								
								if(is_numeric($key3)){
									$articles[$value2['aoDetails']['deliveryTitle']][]=$value3['title']."";
									$artNamesList[]=$value3['id'];
									$toSaveArt[]=$value3['id'];	
								}
							}
						}
					}
					
					$str='';
					$i=0;
					
					foreach($articles as $ke => $va){
						$str.=" <h4>AO Name :<a href='http://admin-test.edit-place.com/ongoing/ao-details?client_id=".$value[0]['aoDetails']['client']."&ao_id=".$value[0]['aoDetails']['deliveryId']."&submenuId=ML2-SL4'>".$ke."</a></h4><ul>";
						foreach($va as $k => $v){
							$str.="<li> Article Title : ".$v."</li>";
							
						}
						$str.="</ul>";
						$i++;
					}
				
					
					
				
					$body="Hello ".$value[0]['bo_user_name'].",<br />

							We noticed that there are more than 1 active writers In Aos Listed Below
							Pls take the necessary actions to avoid reminder emails ;<br />
							Article Are :".$str." <br />

							Regards,<br />
							EP alert team";
					
					$mail = new Zend_Mail();
					$mail->addHeader('Reply-To','support@edit-place.com');
					$mail->setBodyHtml($body)
						 ->setFrom('support@edit-place.com','Support Edit-place')
						 ->addTo($value[0]['bo_user_email'])
						 ->addCc('mailpearls@gmail.com') 
						 ->setSubject('Double Selection of the Contributers for Single article ');
					$mail->send();
					
					
				}
				$dbArt=array();
				if(empty($savedArt)){
					$dbArt=$toSaveArt;
				}else{
					$dbArt=$savedArt+$toSaveArt;
				}
				$artObj->storeDupCronArt($dbArt);
					
					
					
			}
			
			//unlock Cron
           // $this->updateCronLock('doubleSelectionCheck', 'unlocked');
		}

	/*
	 * Function doubleSelectionCheckAction
	 * Use it To Find Double Entry For Ongoing Ao's and Mail to manager
	 * @auther vinayak k
	 *
	 */
	
    public function correctionDoubleSelectionCheckAction(){
		//$cron_obj = new Ep_Ao_CronLock();
      //  $cron = $cron_obj->getCronLock('doubleSelectionCheck');
       // $lockstatus = $cron[0]['locked'];
      //  if($lockstatus == 'locked')
     //   { echo "in process"; exit; }
       // else
      //  {	//Lock Cron 
            //$this->updateCronLock('doubleSelectionCheck', 'locked');
            $part_obj=new EP_Participation_Participation();
            $corpart_obj=new Ep_Participation_CorrectorParticipation();
			$artList=$part_obj->checkAoParticipateCorrectionDuplicates();
		
			if(empty($artList)){
				return;
			}
			$duplist=array();
			$artObj=new Ep_Article_Article();
			$savedArt=array();
			$savedArt=$artObj->getCorDupCronArticleList();
			
			if(!empty($savedArt)){
				foreach($artList as $key => $value){
					foreach($savedArt as $key2 => $val2){
						
						if($val2==$value['id']){
							unset($artList[$key]);
						}
					}	
				}
			}
			
			if(empty($artList)){
				return;
			}
			$ticket=new Ep_Ticket_Ticket();
			$automail=new Ep_Ticket_AutoEmails();
			foreach($artList as $key => $value){
				if($value['count']>1){
					
						$duplist[$value['delivery_id']][]=$value;
						if(!isset($duplist[$value['delivery_id']]['aoDetails'])){
							//$aodet=$part_obj->getParticipateDetails($value['pid']);
							$aodet=array(
								'created_user'=>$value['created_user'],
								'client'=>$value['client'],
								'deliveryTitle'=>$value['deliveryTitle'],
								'deliveryId'=>$value['delivery_id']
								);
							$duplist[$value['delivery_id']]['aoDetails']=$aodet;
							$bo_user=$aodet['created_user'];
							$duplist[$value['delivery_id']]['bo_user_id']=$bo_user;
							$duplist[$value['delivery_id']]['bo_user_name']=$ticket->getUserName($bo_user,TRUE);
							$userDet=$automail->getUserEmailId($bo_user);
							$duplist[$value['delivery_id']]['bo_user_email']=$userDet['email'];
							$duplist[$value['delivery_id']]['ao_link']='<a href="http://admin-test.edit-place.com/ao/ongoingao?submenuId=ML2-SL4&client='.$aodet['client'].'&ao='.$aodet['deliveryId'].'">Delivary</a>';
						}
					
					
				}
			}
			
			$bolist=array();
			if(!empty($duplist)){
				foreach($duplist as $key =>$value){
					$bolist[$value['bo_user_id']][]=$value;
				}
			}
			
			
			if(!empty($bolist)){
				$toSaveArt=array();
				foreach($bolist as $key =>$value){
					$articles=array();
					$artNamesList=array();
					if(!empty($value)){
						foreach($value as $key2 => $value2){
							
							foreach($value2 as $key3 => $value3){
								
								if(is_numeric($key3)){
									$articles[$value2['aoDetails']['deliveryTitle']][]=$value3['title']."";
									$artNamesList[]=$value3['id'];
									$toSaveArt[]=$value3['id'];	
								}
							}
						}
					}
					
					$str='';
					$i=0;
					
					foreach($articles as $ke => $va){
						$str.=" <h4>AO Name :<a href='http://admin-test.edit-place.com/ongoing/ao-details?client_id=".$value[0]['aoDetails']['client']."&ao_id=".$value[0]['aoDetails']['deliveryId']."&submenuId=ML2-SL4'>".$ke."</a></h4><ul>";
						foreach($va as $k => $v){
							$str.="<li> Article Title : ".$v."</li>";
							
						}
						$str.="</ul>";
						$i++;
					}
					
				
					$body="Hello ".$value[0]['bo_user_name'].",<br />

							We noticed that there are more than 1 active Correctors In Aos Listed Below
							Pls take the necessary actions to avoid reminder emails ;<br />
							Article Are :".$str." <br />

							Regards,<br />
							EP alert team";
					
					$mail = new Zend_Mail();
					$mail->addHeader('Reply-To','support@edit-place.com');
					$mail->setBodyHtml($body)
						 ->setFrom('support@edit-place.com','Support Edit-place')
						 ->addTo($value[0]['bo_user_email'])
						 ->addCc('mailpearls@gmail.com') 
						 ->setSubject('Double Selection of the Contributers for Single article Correction ');
					$mail->send();

					
				}
				$dbArt=array();
					if(empty($savedArt)){
						$dbArt=$toSaveArt;
					}else{
						$dbArt=$savedArt+$toSaveArt;
					}
					$artObj->storeCorDupCronArt($dbArt);
				
					
					
			}
			
			//unlock Cron
           // $this->updateCronLock('doubleSelectionCheck', 'unlocked');
		}
	
/******************************************************* Simultaneous Correction**********************************************************/

	/**Auto profiles selection of articles every one hour**/
    public function autoProfileSelection1Action()
    {//exit;
		/*$cron_obj = new Ep_Ao_CronLock();
        $cron = $cron_obj->getCronLock('autoProfileSelection1');
        $lockstatus = $cron[0]['locked'];
        if($lockstatus == 'locked')
        { echo "in process"; exit; }
        else
        { */
            //$this->updateCronLock('autoProfileSelection', 'locked'); 
			$paticipation_obj=new Ep_Participation_Participation();
			$correctionpaticipation_obj=new Ep_Participation_CorrectorParticipation();
			$participation_details=$paticipation_obj->getParticipationWithSenior();
			//print_r($participation_details);exit; 
			if($participation_details!="NO")
			{
				foreach($participation_details as $autoValidate)
				{
					$articleId=$autoValidate['id'];
					$lessPrice_obj=new Ep_Participation_Participation();
					$delivery=new Ep_Article_Delivery();
					$delivery_details=$delivery->getDeliveryDetails($articleId);
					
					//simultaneous correction conditions
					if($delivery_details[0]['articlecorrection']=="yes" && $delivery_details[0]['articlecorrectiontype']=="extern")
					{
						//For public & multiple private case
						$correctors=explode(",",$delivery_details[0]['articlecorrectors']);
						if($delivery_details[0]['deliverycorrectiontype']== "private" && count($correctors)==1)
							$selectedcorrector=$delivery_details[0]['articlecorrectors'];
						else
							$selectedcorrector=$correctionpaticipation_obj->getSelectedCorrector($articleId);
					}
					//echo $selectedcorrector;
					$lessPriceContributor=$lessPrice_obj->getParticipationLessPrice1($articleId,$selectedcorrector);
                   //print_r($lessPriceContributor);exit;
					
                    if($lessPriceContributor!="NO")
					{
							//Check for participation with same type and same price
							$sameProfilPriceContributor=$lessPrice_obj->getParticipationSameProfilePrice($articleId,$lessPriceContributor[0]['price_user'],$lessPriceContributor[0]['profile_type']);
							if($sameProfilPriceContributor!="NO") {
								if($sameProfilPriceContributor[0]['samecount']>1)
								{
								 //$this->updateCronLock('autoProfileSelection1', 'unlocked');
								  break;
								}
							}
							
							//Profile type based on product
							if($lessPriceContributor[0]['product']=='translation')
								$profiletype=$lessPriceContributor[0]['translator_type'];
							else
								$profiletype=$lessPriceContributor[0]['profile_type'];
							
							if($profiletype=='senior')
							{
								if($delivery_details[0]['senior_time'])
								{
									$time=$delivery_details[0]['senior_time'];//2days
								}
								else
								$time=$this->config['sc_time'];//2days
								//$expires=time()+(60*60*$time);
								$expires=time()+(60*$time);
								$articleIdentifier=$lessPriceContributor[0]['article_id'];
								$userIdentifier=$lessPriceContributor[0]['user_id'];
								$participationId=$lessPriceContributor[0]['id']; 
								/**Updating 48hours time to submit the article**/
								$data_array = array("article_submit_expires"=>$expires);////////updating
								$query="id='".$participationId."'";
								$lessPrice_obj->updateArticleSubmitExpires($data_array,$query);
								
								/**Sending Auto Accept and Refuse Mails**/
								$this->seniorSelectionAutoEmails($articleIdentifier,$userIdentifier,$expires);
							   
								/**Accepting the Bid and Refusing other bids**/
								$data = array("status"=>"bid","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
								$query = "user_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
								$lessPrice_obj->updateParticipation($data,$query);
								$data = array("status"=>"bid_refused","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
								$query = "user_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";                            
								$lessPrice_obj->updateParticipation($data,$query);
								/**ENDED**/
								
								//refuse correction participation is there is same user
									$corrPart=$correctionpaticipation_obj->checkParticipationInCorrection($articleIdentifier,$userIdentifier);
									if($corrPart!='NO')
									{
										$corr1data = array("status"=>"bid_refused","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));
										$Corr1Where= "corrector_id='".$userIdentifier."' AND article_id='".$articleIdentifier."' AND cycle='0'";
										$correctionpaticipation_obj->updateParticipation($corr1data,$Corr1Where);
										
										//Mail
										$automail=new Ep_Ticket_AutoEmails();
										$parameters['article_title']=$lessPriceContributor[0]['title'];
										$parameters['articlename_link']="/contrib/mission-deliver?article_id=".$articleIdentifier;
										$automail->messageToEPMail($userIdentifier,29,$parameters);
									}
								
								//CorrectionParticipation participate_id update
								if($delivery_details[0]['articlecorrection']=="yes" && $delivery_details[0]['articlecorrectiontype']=="extern")
								{
									$corrdata=array("participate_id"=>$participationId);
									$CorrWhere= "corrector_id='".$selectedcorrector."' AND article_id='".$articleIdentifier."'";
									$correctionpaticipation_obj->updateParticipation($corrdata,$CorrWhere);
								}
								
							}
							else  if($profiletype=='junior' || $profiletype=='sub-junior' )
							{
								$articleIdentifier=$lessPriceContributor[0]['article_id'];
								$userIdentifier=$lessPriceContributor[0]['user_id'];
								$participationId=$lessPriceContributor[0]['id'];
								/**Accepting the Bid temporarily and Refusing other bids temporarily**/
								$data = array("status"=>"bid_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
								$query = "user_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
								$lessPrice_obj->updateParticipation($data,$query);
								$data = array("status"=>"bid_refused_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
								$query = "user_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";
								$lessPrice_obj->updateParticipation($data,$query);
								/**ENDED**/
							}
					}        
				}
			}
		//$this->updateCronLock('autoProfileSelection1', 'unlocked');
		//}
    }
	
	 /** Corrector Auto profiles selection of articles every one hour**/
    public function autoCorrectorProfileSelection1Action()
    {//exit;
       /* $cron_obj = new Ep_Ao_CronLock();
        $cron = $cron_obj->getCronLock('autoCorrectorProfileSelection1');
        $lockstatus = $cron[0]['locked'];
        if($lockstatus == 'locked')
        { echo "in process"; exit; }
        else
        {*/
           // $this->updateCronLock('autoCorrectorProfileSelection', 'locked'); 
			$paticipation_obj=new Ep_Participation_CorrectorParticipation();
			$part_obj=new Ep_Participation_Participation();
			$participation_details=$paticipation_obj->getParticipationWithSenior();
			//print_r($participation_details);exit;
			if($participation_details!="NO")
			{
				foreach($participation_details as $autoValidate)
				{
					$articleId=$autoValidate['id'];
					$lessPrice_obj=new Ep_Participation_CorrectorParticipation();
					$delivery=new Ep_Article_Delivery();
					$delivery_details=$delivery->getDeliveryDetails($articleId);
					
					//simultaneous correction conditions
					
					//For public & multiple private case
					$writers=explode(",",$delivery_details[0]['articlewriters']);
					if($delivery_details[0]['AOtype']== "private" && count($writers)==1)
						$selectedwriter=$delivery_details[0]['articlewriters'];
					else
						$selectedwriter=$part_obj->getSelectedwriters($articleId);
					
					$participate_id=$part_obj->getParticipateId($selectedwriter,$articleId);
					$ParticipationDetail=$part_obj->getParticipationDetail($participate_id);
					
					$lessPriceContributor=$lessPrice_obj->getParticipationLessPrice1($articleId,$selectedwriter);
					//print_r($lessPriceContributor);exit;
					$scount=0;
					$cnt=0;
					foreach($lessPriceContributor as $corrector)
					{
						if(!$corrector['profile_type2'])
							  $lessPriceContributor[$cnt]['profile_type2']='junior';
						 if($corrector['profile_type2']=='senior')
								   $scount++;
						 $cnt++;
					}
					if($lessPriceContributor[0]['type2']=='corrector' && (($lessPriceContributor[0]['profile_type2']=='senior') OR ($lessPriceContributor[0]['profile_type2']=='junior' && $scount==0 )))
					{
						if($lessPriceContributor[0]['profile_type2']=='senior') {
                            if ($delivery_details[0]['correction_sc_submission'])
                                $time = $delivery_details[0]['correction_sc_submission'];//2days
                            else
                                $time = $this->config['correction_sc_submission'];//2days
                        }    else {
                            if ($delivery_details[0]['correction_jc_submission'])
                                $time = $delivery_details[0]['correction_jc_submission'];//2days
                            else
                                $time = $this->config['correction_jc_submission'];//2days
                        }
						//$expires=time()+(60*60*$time);
						$expires=time()+(60*$time);
						$articleIdentifier=$lessPriceContributor[0]['article_id'];
						$userIdentifier=$lessPriceContributor[0]['corrector_id'];
						$participationId=$lessPriceContributor[0]['id'];
						/**Updating 48hours time to submit the article**/
						if($ParticipationDetail[0]['status']=='under_study' && $ParticipationDetail[0]['current_stage']=='corrector')
						{
							$data_array = array("corrector_submit_expires"=>$expires);////////updating
							$query="id='".$participationId."'";
							$lessPrice_obj->updateArticleSubmitExpires($data_array,$query);
						}
						/**Sending Auto Accept and Refuse Mails**/
						$this->seniorCorrectorSelectionAutoEmails1($articleIdentifier,$userIdentifier,$expires,$corrector['profile_type2']);
						/**Accepting the Bid and Refusing other bids**/
						$data = array("status"=>"bid","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()),"participate_id"=>$participate_id);////////updating
						$query = "corrector_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
						$lessPrice_obj->updateParticipation($data,$query);
						$data = array("status"=>"bid_refused","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
						$query = "corrector_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";
						$lessPrice_obj->updateParticipation($data,$query);
						/**ENDED**/
						
						//refuse writer participation is there is same user
						$corrPart=$part_obj->checkParticipation($articleIdentifier,$userIdentifier);
						print_r($corrPart);
						if($corrPart!='NO')
						{
							$corr1data = array("status"=>"bid_refused","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));
							$Corr1Where= "user_id='".$userIdentifier."' AND article_id='".$articleIdentifier."' AND cycle='0'";
							$part_obj->updateParticipation($corr1data,$Corr1Where);
							
							//Mail
							$automail=new Ep_Ticket_AutoEmails();
							$parameters['article_title']=$delivery_details[0]['articleName'];
							$parameters['articlename_link']="/contrib/mission-deliver?article_id=".$articleIdentifier;
							   if($delivery_details[0]['deli_anonymous']=='1')
									$parameters['client_name']='inconnu';
							   else
							   {
								   $clientDetails=$automail->getUserDetails($delivery_details[0]['client']);
								   if($clientDetails[0]['username']!=NULL)
									   $parameters['client_name']= $clientDetails[0]['username'];
								   else
									   $parameters['client_name']= $clientDetails[0]['email'];
							   }
							$automail->messageToEPMail($userIdentifier,27,$parameters);
						}	
					}
					else  if($lessPriceContributor[0]['profile_type2']=='junior')
					{
                        ////updated code by chandu (A00360)
                        if ($delivery_details[0]['correction_jc_submission'])
                            $time = $delivery_details[0]['correction_jc_submission'];//2days
                        else
                            $time = $this->config['correction_jc_submission'];//2days
						$expires=time()+(60*$time);
                        $participationId=$lessPriceContributor[0]['id'];
                        /**Updating 48hours time to submit the article**/
                        $data_array = array("corrector_submit_expires"=>$expires);////////updating
                        $query="id='".$participationId."'";
                        $lessPrice_obj->updateArticleSubmitExpires($data_array,$query);
                        /////end ////
                        $articleIdentifier=$lessPriceContributor[0]['article_id'];
						$userIdentifier=$lessPriceContributor[0]['corrector_id'];
						$participationId=$lessPriceContributor[0]['id'];
						/**Accepting the Bid temporarily and Refusing other bids temporarily**/
						$data = array("status"=>"bid_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
						$query = "corrector_id= '".$userIdentifier."' AND article_id = '".$articleIdentifier."'";
						$lessPrice_obj->updateParticipation($data,$query);
						$data = array("status"=>"bid_refused_temp","selection_type"=>"auto_temp", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));////////updating
						$query = "corrector_id!= '".$userIdentifier."' AND article_id = '".$articleIdentifier."' And cycle=0";
						$lessPrice_obj->updateParticipation($data,$query);
						/**ENDED**/
					}
				}
			}
			//$this->updateCronLock('autoCorrectorProfileSelection1', 'unlocked');
		//}
    }
	
	/*
	 * function AoPrivateSingleAutomaticAction
	 * AO private with 1 person -> selection automatic once contributor has participated.
	 * Tables used : Delivary , Article , Contributers , Participation
	 *
	 */
	public function aoPrivateSingleAutomatic1Action()
	{
		/*$cron_obj = new Ep_Ao_CronLock();
        $cron = $cron_obj->getCronLock('aoPrivateSingleAutomatic1');
        $lockstatus = $cron[0]['locked'];
        if($lockstatus == 'locked')
        { echo "in process"; exit; }
        else
        {*/
           // $this->updateCronLock('aoPrivateSingleAutomatic1', 'locked'); 

			$user_obj = new Ep_User_User();
			$privateDelivaryList=$user_obj ->getPrivateDelivaryList();
			echo "<pre>";print_r($privateDelivaryList);//exit;  
			if(!empty($privateDelivaryList) && $privateDelivaryList!='NO'){
				$participate_obj=new Ep_Participation_Participation();
				foreach($privateDelivaryList as $key => $value){
					
					//Only For BO 
					if($value['created_by_type']=='BO'){
						$time='';
						switch($value['profileType']){
							case 'senior': $time=$value['st'];
							break;
							case 'junior': $time=$value['jt'];
							break;
							case 'sub-junior': $time=$value['sjt'];
							break;
						}
						if($time==''){
							$time=$this->config['sc_time'];//2days
						}
						
						$lessPrice_obj=new Ep_Participation_Participation();
						$expires=time()+(60*$time);
						//echo $expires;
						$articleIdentifier=$value['article'];
						$userIdentifier=$value['user_id'];
						$participationId=$value['participation'];
						
						//Simultaneous correction conditions
						$delivery=new Ep_Article_Delivery();
						$correctionpaticipation_obj=new Ep_Participation_CorrectorParticipation();
						$delivery_details=$delivery->getDeliveryDetails($articleIdentifier);
						
						if($delivery_details[0]['articlecorrection']=="yes" && $delivery_details[0]['articlecorrectiontype']=="extern")
						{
							//For public & multiple private case
							$correctors=explode(",",$delivery_details[0]['articlecorrectors']);
							if($delivery_details[0]['deliverycorrectiontype']== "private" && count($correctors)==1)
								$selectedcorrector=$delivery_details[0]['articlecorrectors'];
							else
								$selectedcorrector=$correctionpaticipation_obj->getSelectedCorrector($articleIdentifier);
						}
						//echo $selectedcorrector;exit;
						if($userIdentifier!=$selectedcorrector)
						{
							/**Updating 48hours OR Submission User Time allwed to submit the article**/
							$data_array = array("article_submit_expires"=>$expires);////////updating
							$query="id='".$participationId."'";
							$lessPrice_obj->updateArticleSubmitExpires($data_array,$query);
							
							/**Sending Auto Accept and Refuse Mails**/
							/** The Auto Accept mail Will be sent to Contributer and
							 * Refuse mails will not be Sent as he is only One person to Bid as single person private  **/
							$this->seniorSelectionAutoEmails($articleIdentifier,$userIdentifier,$expires);
							//Update Participation from bid_premium to bid Automatically
							$data = array("status"=>"bid","selection_type"=>"auto");////////updating
							$query = "article_id= '".$value['article']."' AND id = '".$value['participation']."'";
							$participate_obj->updateParticipation($data,$query);
							
							//refuse correction participation is there is same user
								$corrPart=$correctionpaticipation_obj->checkParticipationInCorrection($articleIdentifier,$userIdentifier);
								if($corrPart!='NO')
								{
									$corr1data = array("status"=>"bid_refused","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));
									$Corr1Where= "corrector_id='".$userIdentifier."' AND article_id='".$articleIdentifier."' AND cycle='0'";
									$correctionpaticipation_obj->updateParticipation($corr1data,$Corr1Where);
									
									//Mail
									$automail=new Ep_Ticket_AutoEmails();
									$parameters['article_title']=$delivery_details[0]['articleName'];
									$parameters['articlename_link']="/contrib/mission-deliver?article_id=".$articleIdentifier;
									$automail->messageToEPMail($userIdentifier,29,$parameters);
								}	
									
							//CorrectionParticipation participate_id update
							if($delivery_details[0]['articlecorrection']=="yes" && $delivery_details[0]['articlecorrectiontype']=="extern")
							{
								$corrdata=array("participate_id"=>$participationId);
								$CorrWhere= "corrector_id='".$selectedcorrector."' AND article_id='".$articleIdentifier."'";
								$correctionpaticipation_obj->updateParticipation($corrdata,$CorrWhere);
							}
						}
					}
				}
			}
			//$this->updateCronLock('aoPrivateSingleAutomatic1', 'unlocked');
		//}
	}
	
	/*
	 * function AoPrivateSingleAutomaticAction
	 * Correction AO private with 1 person -> selection automatic once corrector has participated.
	 * Tables used : Delivary , Article , Contributers , CorrectorParticipation
	 *
	 */
	public function aoCorrectionPrivateSingleAutomatic1Action()
	{
		/*$cron_obj = new Ep_Ao_CronLock();
        $cron = $cron_obj->getCronLock('aoCorrectionPrivateSingleAutomatic1');
        $lockstatus = $cron[0]['locked'];
        if($lockstatus == 'locked')
        { echo "in process"; exit; }
        else
        {*/
            //$this->updateCronLock('aoCorrectionPrivateSingleAutomatic1', 'locked'); 
			$user_obj = new Ep_User_User();
			$privateCorrectionList=$user_obj ->getPrivateCorrectionList();
			echo "<pre>";print_r($privateCorrectionList);//exit;
			
			if(!empty($privateCorrectionList) && $privateCorrectionList!='NO')
			{
				$participate_obj=new Ep_Participation_CorrectorParticipation();
				foreach($privateCorrectionList as $key => $value)
				{
						$time='';
						switch($value['profileType']=="senior"){
							case 'senior': $time=$value['st'];
							break;
							case 'junior': $time=$value['jt'];
							break;
						}
						if($time==''){
							$time=$this->config['correction_sc_submission'];//2days
						}
						$expires=time()+(60*$time);
						
						//simultaneous correction conditions
						
						$delivery=new Ep_Article_Delivery();
						$delivery_details=$delivery->getDeliveryDetails($value['article']);
						$part_obj=new Ep_Participation_Participation();
						
						//For public & multiple private case
						$writers=explode(",",$delivery_details[0]['articlewriters']);
						if($delivery_details[0]['AOtype']== "private" && count($writers)==1)
							$selectedwriter=$delivery_details[0]['articlewriters'];
						else
							$selectedwriter=$part_obj->getSelectedwriters($value['article']);
							
						$participate_id=$part_obj->getParticipateId($selectedwriter,$value['article']);	
						$ParticipationDetail=$part_obj->getParticipationDetail($participate_id);
						
						//echo $participate_id;exit;  
						if($value['user_id']!=$selectedwriter)  
						{
							//Update CorrectionParticipation from bid_premium to bid Automatically
							if($ParticipationDetail[0]['status']=='under_study' && $ParticipationDetail[0]['current_stage']=='corrector')
								$data = array("status"=>"bid","corrector_submit_expires"=>$expires,"selection_type"=>"auto","participate_id"=>$participate_id);
							else
								$data = array("status"=>"bid","selection_type"=>"auto","participate_id"=>$participate_id);
							$participate_obj->updateParticipationDetails($data,$value['participation']);
							
							//refuse writer participation is there is same user
							$corrPart=$part_obj->checkParticipation($articleIdentifier,$userIdentifier);
							if($corrPart!='NO')
							{
								$corr1data = array("status"=>"bid_refused","selection_type"=>"auto", "accept_refuse_at"=>date("Y-m-d H:i:s", time()));
								$Corr1Where= "user_id='".$userIdentifier."' AND article_id='".$articleIdentifier."' AND cycle='0'";
								$part_obj->updateParticipation($corr1data,$Corr1Where);
								
								//Mail
								$automail=new Ep_Ticket_AutoEmails();
								$parameters['article_title']=$delivery_details[0]['articleName'];
								$parameters['articlename_link']="/contrib/mission-deliver?article_id=".$articleIdentifier;
								   if($delivery_details[0]['deli_anonymous']=='1')
										$parameters['client_name']='inconnu';
								   else
								   {
									   $clientDetails=$automail->getUserDetails($delivery_details[0]['client']);
									   if($clientDetails[0]['username']!=NULL)
										   $parameters['client_name']= $clientDetails[0]['username'];
									   else
										   $parameters['client_name']= $clientDetails[0]['email'];
								   }
								$automail->messageToEPMail($userIdentifier,27,$parameters);
							}	
						
							//sending selection mail 
							$automail=new Ep_Ticket_AutoEmails();
							$parameters['AO_end_date']=date("d/m/Y",$expires)." &agrave; ".date("H:i:s",$expires);
							$parameters['article_title']=$value['articleTitle'];
							$parameters['ongoinglink']="/contrib/mission-corrector-deliver?article_id=".$value['article'];
							 
							  if($value['profileType']=='senior')
								  $parameters['resubmit_time']=$value['srt'];
							  else
								  $parameters['resubmit_time']=$value['jrt'];
								  
								if($value['correction_resubmit_option']=="day")
									$parameters['resubmit_time']=($parameters['resubmit_time']/(60*24)). " jour(s)";
								elseif($value['correction_resubmit_option']=="hour")	
									$parameters['resubmit_time']=($parameters['resubmit_time']/60). " heure(s)";
								else
									$parameters['resubmit_time']=$parameters['resubmit_time']. " min(s)";
								$parameters['resubmit_time']="<b>".$parameters['resubmit_time']."</b>";
								
								$clientDetails=$automail->getUserDetails($value[0]['clientId']);
								if($clientDetails[0]['username']!=NULL)
									$parameters['client_name']= $clientDetails[0]['username'];
								else
									$parameters['client_name']= $clientDetails[0]['email'];
					
									$max=max($value[0]['subjunior_time'], $value[0]['junior_time'], $value[0]['senior_time']);
								$writersubmission=time()+(60 * $max);
								$writerparticipation=60*$value[0]['participation_time'];			
								$parameters['max_reception_writer_file_date_hour']=strftime("%d/%m/%Y &agrave; %H:%M",($writerparticipation+$writersubmission));
								
								if($ParticipationDetail[0]['status']=='under_study')
									$automail->messageToEPMail($value['user_id'],28,$parameters);
								else
									$automail->messageToEPMail($value['user_id'],182,$parameters);
						}
				}
			}
			//$this->updateCronLock('aoCorrectionPrivateSingleAutomatic1', 'unlocked');
		//}
	}
	
	/**sending auto accept/refusals when senior contributor selected**/
    public function seniorCorrectorSelectionAutoEmails1($articleId,$userid,$expires,$corrector_type)
    {
        $participation_obj=new Ep_Participation_CorrectorParticipation();
		$part_obj=new Ep_Participation_Participation();
        $AllPartcipations=$participation_obj->getAllParticipationByArticle($articleId);
		
		//writer status
		$writerstatus=$part_obj->getArticleStatus($articleId);
		
        if($AllPartcipations!="NO")
        {
            foreach($AllPartcipations AS $paticipants)
            {
                $participation=new Ep_Participation_CorrectorParticipation();
                $automail=new Ep_Ticket_AutoEmails();
                $participationDetails=$participation->getParticipateDetails($paticipants['id']);
                $parameters['AO_end_date']=date("d/m/Y",$expires)." &agrave; ".date("H:i:s",$expires);//date("d/m/Y",strtotime($participationDetails[0]['submitdate_bo']));
                $parameters['article_title']=$participationDetails[0]['title'];
                if($paricipationDetails[0]['deli_anonymous']=='1')
                    $parameters['client_name']='inconnu';
                else
                {
                    $clientDetails=$automail->getUserDetails($participationDetails[0]['clientId']);
                    if($clientDetails[0]['username']!=NULL)
                        $parameters['client_name']= $clientDetails[0]['username'];
                    else
                        $parameters['client_name']= $clientDetails[0]['email'];
                }
                $parameters['ongoinglink']="/contrib/ongoing";
                $parameters['royalty']=$participationDetails[0]['price_corrector'];
                if($corrector_type=='senior')
                    $parameters['resubmit_time']=$participationDetails[0]['correction_sc_resubmission'];
                else
                    $parameters['resubmit_time']=$participationDetails[0]['correction_jc_resubmission'];
					
				$max=max($participationDetails[0]['subjunior_time'], $participationDetails[0]['junior_time'], $participationDetails[0]['senior_time']);
					$writersubmission=time()+(60 * $max);
					$writerparticipation=60*$participationDetails[0]['participation_time'];			
					$parameters['max_reception_writer_file_date_hour']=strftime("%d/%m/%Y &agrave; %H:%M",($writerparticipation+$writersubmission));
								
                if($paticipants['corrector_id']==$userid)
                {
					if($writerstatus=='under_study')     
						$automail->messageToEPMail($paticipants['corrector_id'],28,$parameters);
					else
						$automail->messageToEPMail($paticipants['corrector_id'],182,$parameters);

                    //Insert action in history table
                      $action_obj=new Ep_Article_ArticleActions();
                      $history_obj=new Ep_Article_ArticleHistory();

                      $action_sentence= $action_obj->getActionSentence(15);

                      $ticket=new Ep_Ticket_Ticket();                                                  
                      $contributor_name='<a class="corrector" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$userid.'" target=_blank""><b>'.$ticket->getUserName($userid,TRUE).'</b></a>';
                      $article_name='<b>'.$parameters['article_title'].'</b>';

                      eval("\$action_sentence= \"$action_sentence\";");

                      if($action_sentence)
                      {  
                        $history_array['article_id']=$participationDetails[0]['article_id'];                                                
                        $history_array['user_id']=$userid;
                        $history_array['stage']='system_bot'; 
                        $history_array['action']='correction_profile_accepted';
                        $history_array['action_at']=date("Y-m-d H:i:s");
                        $history_array['action_sentence']=$action_sentence;                                  

                        $history_obj->insertHistory($history_array);
                      }

                      //send notification email to Project manage
                      $bo_user=$participationDetails[0]['created_user'];
                      $mail_params['AO_title']=$participationDetails[0]['deliveryTitle'];
                      $mail_params['contributor_name']=$ticket->getUserName($userid,TRUE);
                      $mail_params['article_title']=$participationDetails[0]['title'];
                      $mail_params['bo_user']=$ticket->getUserName($bo_user,TRUE);
                      
                      $mail_params['comment_bo_link']='/ongoing/ao-details?client_id='.$participationDetails[0]['clientId'].'&ao_id='.$participationDetails[0]['deliveryId'].'&submenuId=ML2-SL4';

                      $automail_bo=new Ep_Ticket_AutoEmails();
                      $automail_bo->messageToEPMail($bo_user,109,$mail_params,TRUE);
                }    
                else
                    $automail->messageToEPMail($paticipants['corrector_id'],29,$parameters);
            }
        }
    }
	
	//Simultaneous correction bidding end mail to PM
	public function correctionBiddingEndMailAction()
	{
		$article_obj=new Ep_Article_Article();
		$artdeldetails = $article_obj->getCrtArticleDeliveryTimeUp();
		
		if($artdeldetails!="NO")
		{    
			foreach($artdeldetails as $artdetail)
			{
				$artcount = $article_obj->getArticleCountDelivery($artdetail['id']);
				
				$parameters['bo_user'] = $artdetail['login'];
				$parameters['article_title'] = $artdetail['title'];
				$parameters['mission_link'] = "<a href='http://admin-test.edit-place.com/ongoing/ao-details?client_id=".$artdetail['user_id']."&ao_id=".$artdetail['id']."&submenuId=ML2-SL4'>Cliquez-ici</a>";
				$parameters['particpation_count'] = $artdetail['correctorcount'];
				//$parameters['time_to_manual_select'] = ($artdetail['correction_selection_time']/60).' heures';
				$parameters['time_to_manual_select'] = '1 hour';
				//$parameters['time_of_automatic_selection'] = ($artdetail['correction_selection_time']/60).' heures';
				$parameters['time_of_automatic_selection'] = '1 hour';
				$parameters['correction_selection_profile'] = "<a href='http://admin-test.edit-place.com/correction/corrector-profiles-list?submenuId=ML2-SL18'>Cliquez-ici</a>"; 
				
				if($artdetail['correctorcount']>0)
					$this->sendMailToEPPersonal($artdetail['email'],189,$parameters);
				else
					$this->sendMailToEPPersonal($artdetail['email'],188,$parameters);
			}
		}
			
	}
    /*added by naseer on 07-10-2015*/
    //to check duplication of tranlation in validstencils//
    public function checkTranslationDuplicationAction(){
        $article_obj=new Ep_Article_Article();
        $stencils = $article_obj->testDuplication();
        $flag = false;
        for($i=0;$i<count($stencils);$i++){
            for($j=0;$j<count($stencils);$j++) {
                if( $stencils[$i]['translation_id'] == $stencils[$j]['translation_id'] && $stencils[$i]['language'] == $stencils[$j]['language'] ){
                    $duplication[$i]['language'] = $stencils[$i]['language'];
                    $duplication[$i]['translation_id']=$stencils[$i]['translation_id'] ;
                    $flag = true;
                }
            }
        }
        if($flag){
            $count = count($duplication);
            $duplication = array_unique($duplication, SORT_REGULAR);
            $table = '<table>
                        <tr>
                            <th>
                                <td>Langauge</td>
                                <td>Translation ID</td>
                            </th>
                        </tr>';
            for($i=0;$i<$count;$i++){
                if($duplication[$i]["language"] != '')
                $table .='<tr>
                            <th>
                                <td>'.$duplication[$i]["language"].'</td>
                                <td>ID= '.$duplication[$i]["translation_id"].'</td>
                            </th>
                        </tr>';
            }
            $table .='</table>';
            $xlsFile = ROOT_PATH.'FO/invoice/client/xls/translation_duplication_'.time().'_XLS.xls';
            $fh = fopen($xlsFile, 'w') or die("can't open file");

            fwrite($fh, $table);
            fclose($fh);

            $sentto = 'rakeshm@edit-place.com';
            $sub = 'Multiple Translation for same stencils and same langauge found';
            $body = 'Hi,<br/>Multiple Translation for same stencils and same langauge found.<br/>Regards.';
                $mail = new Zend_Mail();
                $mail->addHeader('Reply-To','support@edit-place.com');
                $mail->setBodyHtml($body)
                    ->setFrom('support@edit-place.com','support@edit-place')
                    ->addTo($sentto)
                    ->setSubject($sub);
            $content = file_get_contents($xlsFile); // e.g. ("attachment/abc.pdf")
            $attachment = new Zend_Mime_Part($content);
            $attachment->type = 'application/xls';
            $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
            $attachment->encoding = Zend_Mime::ENCODING_BASE64;
            $attachment->filename = 'file.xls'; // name of file

            $mail->addAttachment($attachment);
            if($mail->send())
                    echo "Email Successfully sent to inform Multiple records existence.";
                else
                    echo "Failed to send to inform Multiple records existence.";
        }else{
            echo "Multiple records does not exist.";
        }
        if (file_exists($xlsFile)) {
            unlink($xlsFile);
        }
        exit;
    }
    //to check duplication of tranlation in validstencils//
    public function checkTranslationDuplicationArticleAction(){
        $article_obj=new Ep_Article_Article();
        $stencils = $article_obj->testTranslationDuplicationArticle();
        $start = 0;
        for($i=0;$i<count($stencils);$i++){
            $stencils[$i]['stencils_translate'] = explode(",",$stencils[$i]['stencils_translate']);
            for($j=0;$j<count($stencils[$i]['stencils_translate']);$j++){
                $stencilstemp[$start]['id'] = $stencils[$i]['id'];
                $stencilstemp[$start]['delivery_id'] = $stencils[$i]['delivery_id'];
                $stencilstemp[$start]['title'] = $stencils[$i]['title'];
                $stencilstemp[$start]['language'] = $stencils[$i]['language'];
                $stencilstemp[$start]['stencils_translate'] = $stencils[$i]['stencils_translate'][$j];
                $start++;
            }

        }
        $flag = false;
        for($i=0;$i<count($stencilstemp);$i++){
            for($j=0;$j<count($stencilstemp);$j++) {
                if( $stencilstemp[$i]['stencils_translate'] == $stencilstemp[$j]['stencils_translate'] && $stencilstemp[$i]['language'] == $stencilstemp[$j]['language'] ){
                    $duplication[$i]['id'] = $stencilstemp[$i]['id'];
                    $duplication[$i]['delivery_id'] = $stencilstemp[$i]['delivery_id'];
                    $duplication[$i]['title']=$stencilstemp[$i]['title'] ;
                    $duplication[$i]['language'] = $stencilstemp[$i]['language'];
                    $duplication[$i]['stencils_translate']=$stencilstemp[$i]['stencils_translate'] ;
                    $flag = true;
                }
            }
        }
        if($flag){
            $count = count($duplication);
            $duplication = array_unique($duplication, SORT_REGULAR);
            $table = '<table border="1" style="border:1px solid #333" cellpadding="5" cellspacing="0">
                        <tr style="background-color: #4683EA;color:#fff;">

                                <th>Article Id &nbsp;</th>
                                <th>Delivery_id &nbsp;</th>
                                <th>title &nbsp;</th>
                                <th>Langauge &nbsp;</th>
                                <th>Translation ID &nbsp;</th>

                        </tr>';
            for($i=0;$i<$count;$i++){
                if($duplication[$i]["language"] != '')
                    $table .='<tr style="background-color: #F8F8F8;">

                                <td>'.$duplication[$i]["id"].' &nbsp;</td>
                                <td>'.$duplication[$i]["delivery_id"].' &nbsp;</td>
                                <td>'.$duplication[$i]["title"].' &nbsp;</td>
                                <td>'.$duplication[$i]["language"].' &nbsp;</td>
                                <td>'.$duplication[$i]["stencils_translate"].' &nbsp;</td>

                        </tr>';
            }
            $table .='</table>';
            /*$xlsFile = ROOT_PATH.'FO/invoice/client/xls/translation_duplication_'.time().'_XLS.xls';
            $fh = fopen($xlsFile, 'w') or die("can't open file");

            fwrite($fh, $table);
            fclose($fh); */


            $sentto = 'rakeshm@edit-place.com'; //'nass0069@gmail.com'; //
            $sub = 'Multiple Translation for same stencils and same langauge found';
            $body = 'Hi,<br/>Multiple Translation for same stencils and same langauge found.<br/><br />
                    '.$table.'<br/>Regards.';
            $mail = new Zend_Mail();
            $mail->addHeader('Reply-To','support@edit-place.com');
            $mail->setBodyHtml($body)
                ->setFrom('support@edit-place.com','support@edit-place')
                ->addTo($sentto)
                ->setSubject($sub);
            /*$content = file_get_contents($xlsFile); // e.g. ("attachment/abc.pdf")
            $attachment = new Zend_Mime_Part($content);
            $attachment->type = 'application/xls';
            $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
            $attachment->encoding = Zend_Mime::ENCODING_BASE64;
            $attachment->filename = 'file.xls'; // name of file

            $mail->addAttachment($attachment);*/
            if($mail->send())
                echo "Email Successfully sent to inform Multiple records existence.";
            else
                echo "Failed to send to inform Multiple records existence.";
        }else{
            echo "Multiple records does not exist.";
        }
        if (file_exists($xlsFile)) {
            unlink($xlsFile);
        }
        exit;
    }
	
	/**update black list users status and email*/
	public function updateBlacklistEmailsAction()
	{
		$userObj=new Ep_User_User();
		$blackListUsers=$userObj->getBlacklistWriters();
		//echo "<pre>";print_r($blackListUsers);		
		if($blackListUsers)
		{
			$htmltable='<table border=1>
				<tr><th><b>Old Email</b></th><th><b>New Email</b></th></tr>';
			foreach($blackListUsers as $user)
			{	
				$email=$user['email'];				
				if($email)
				{
					$explodeEmail=explode('@',$email);				
					$new_email=$explodeEmail[0].'_deleted'.'@'.$explodeEmail[1];				
					$htmltable.="<tr><td>$email</td><td>$new_email</td></tr>";
					
					$userObj=new Ep_User_User();
					$identifier=$user['identifier'];
					$data['status']='Inactive';
					$data['email']=$new_email;
					$query =" identifier='".$identifier."'";
					//echo "<pre>";print_r($data);
					
					$userObj->updateUser($data,$query);
					
					
				}
				
			}
			$htmltable.='</table>';
			
			//echo $htmltable;exit;
			
			
			$file_name = "UK-BL-users-to-inactive-users-".date("Y-m-d").".xlsx"; 
            $file = APP_PATH_ROOT."blacklist-users/".$file_name;
			//echo $file;exit;
			convertHtmltableToXlsx($htmltable,$file);
			
			$email_to = "arunravuri@edit-place.com";
			$email_cc = "mailpearls@gmail.com";	
			$email_subject = 'UK-BL users to inactive users on '.date("Y-m-d");
            $email_message = '<html>
                    <head>
                      <title>BL users to inactive users - UK</title>
                    </head>
                    <body>
                        Hi,<br/><br />
                        PFA XLSX file which contains UK BL users converted to inactive users on '.date("Y-m-d").'
                        <br /><br />
                        Regards,<br />
                        Edit-place.com
                    </body>
                    </html>';			
			$mail = new Zend_Mail; 			
			//echo "file name  : " .$file. PHP_EOL; 
			$at = new Zend_Mime_Part(file_get_contents($file)); //.xls is included in the filename 
			$at->type = 'application/octet-stream'; 
			$at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT; 
			$at->encoding = Zend_Mime::ENCODING_BASE64; 
			$at->filename = basename($file); //.xls is included in the filename 
			$mail->setFrom('support@edit-place.com'); 
			$mail->addTo($email_to); 
			if($email_cc)
			{
				$mail->addCc($email_cc); 
			}
			$mail->setSubject($email_subject); 
			$mail->setBodyHtml($email_message); 
			$mail->addAttachment($at); 
			try { 
					$mail->send(); 
				//	echo "Mail Sent".PHP_EOL; 
			} catch (Zend_Mail_Exception $e) { 
					print_r($e->getMessage()); 
			} 
			exit;		
			
		}
		else{
			echo '<h2>No Black list users found </h1>';
		}
	}
	
	
    /* *** added on 16.02.2016*** */
    //cron to automatically validated a article after 15days of delivered date//
    public function autoFinalValidationPost15DaysAction()
    {
        $this->configval['mail_from'] = 'support@edit-place.com';
        $participate_obj = new EP_Participation_Participation();
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $autoEmails = new Ep_Ticket_AutoEmails();//Ep_Message_AutoEmails();
        $article_obj = new Ep_Article_Article();//EP_Delivery_Article();
        $del_obj = new Ep_Article_Delivery();//Ep_Delivery_Delivery();
        $artProcess_obj = new EP_Article_ArticleProcess();//EP_Delivery_ArticleProcess();
        $user_obj = new Ep_User_User();
        $recent_acts_obj = new Ep_User_RecentActivities();

        $res_validate_art = $article_obj->getArticlePost15DaysDelivered();
        $price_writer = $price_corrector =0;
        if ($res_validate_art != 'NO' ){
            $i = 0;
            $validateDetails = array();
            while ($res_validate_art[$i]) {
                $validateDetails[$i] = $res_validate_art[$i]['art_id_part_id'];
                $i++;
            }
            /* $s2art_params = //$this->_request->getParams();
             $validateDetails = explode('|', $s2art_params['validate_art']) ;*/

            echo "Validating :<pre>";print_r($validateDetails);//exit; //exit('partId='.$partId);
            $htmltablebody ="";
            $limit = 0;//for testing purpose only
            foreach ($validateDetails as $validateDetail) :
                if($limit < 2)//for testing purpose only
                {
                    $validateArt_ = explode('_', $validateDetail);
                    $artId = $validateArt_[0];
                    $partId = $validateArt_[1];
                    $details = $article_obj->getArticleDetailsCron($artId);

                    //if(1==2){
                    if ($details[0]['correction'] == 'yes') {
                        $crtpartsOfArt = $crtparticipate_obj->getAllCrtParticipationsStage2($artId);
                        $crtpartId = $crtpartsOfArt[0]['id'];
                    }
                    $recentversion = $artProcess_obj->getRecentVersion($partId);
                    ////udate status participation table for stage///////
                    $premium = $del_obj->checkPremiumAO($artId);
                    $paricipationdetails = $participate_obj->getParticipateDetailsCron($partId);
                    $participent_user_id = $paricipationdetails[0]['user_id'];
                    $corrector_user_id = $paricipationdetails[0]['corrector_id'];

                    if ($premium == 'NO')
                        $data = array("current_stage" => "client", "status" => "under_study", "marks" => $recentversion[0]['marks']);////////updating
                    else {
                        $data = array("current_stage" => "client", "status" => "published", "marks" => $recentversion[0]['marks']);////////updating

                        $royalty_obj = new Ep_Royalty_Royalties();//Ep_Payment_Royalties();

                        if ($royalty_obj->checkRoyaltyExists($paricipationdetails[0]['article_id']) == 'NO') {
                            //Added w.r.t Recruitment
                            if ($paricipationdetails[0]['free_article'] == 'yes' && $paricipationdetails[0]['missiontest'] == 'yes')
                                $price_writer = 0;
                            else
                                $price_writer = $paricipationdetails[0]['price_user'];

                            $royalty_obj->participate_id = $paricipationdetails[0]['participateId'];
                            $royalty_obj->article_id = $paricipationdetails[0]['article_id'];
                            $royalty_obj->user_id = $paricipationdetails[0]['user_id'];
                            $royalty_obj->price = $price_writer;
                            $royalty_obj->correction = "no";
                            $royalty_obj->currency=$paricipationdetails[0]['currency'];
                            $royalty_obj->insert();
                            /* *Sending Mails ***/
                            /////////send mail to contributor///////////////////////////////////////
                            $paricipationdetails = $participate_obj->getParticipateDetailsCron($partId);
                            $contribDetails = $autoEmails->getContribUserDetails($paricipationdetails[0]['user_id']);
                            ///////////////if user is sub-junior then update him to jc/////////
                            $userDetails = $user_obj->getAllUsersDetails($paricipationdetails[0]['user_id']);
                            if ($userDetails[0]['profile_type'] == "sub-junior") {
                                $data = array("profile_type" => "junior");
                                $query = "identifier = '" . $userDetails[0]['identifier'] . "' ";
                                $user_obj->updateUser($data, $query);
                            }

                            if ($contribDetails[0]['firstname'] != NULL)
                                $parameters['contributor_name'] = $contribDetails[0]['firstname'] . " " . $contribDetails[0]['lastname'];
                            else
                                $parameters['contributor_name'] = $contribDetails[0]['email'];

                            $parameters['created_date'] = date("d/m/Y", strtotime($paricipationdetails[0]['created_at']));
                            $parameters['document_link'] = "/client/ongoingao";
                            $parameters['invoice_link'] = "/client/invoice";
                            $parameters['royalty'] = $paricipationdetails[0]['price_user']." &".$details[0]['currency'].";";
                            $receiverId = $paricipationdetails[0]['user_id'];
                            $parameters['article_title'] = $paricipationdetails[0]['title'];
                            $parameters['articlename_link'] = "/contrib/mission-published?article_id=" . $artId;
                            $autoEmails->messageToEPMail($receiverId, 53, $parameters);
                            /////*
                            //unset($royalty_obj);
                            //sleep(5);
                            if ($details[0]['correction'] == "yes") {
                                $royalty_obj_c = new Ep_Royalty_Royalties();//Ep_Payment_Royalties();
                                $crtparicipationdetails = $crtparticipate_obj->getCrtParticipateDetails($crtpartId);
                                $price_corrector = $crtparicipationdetails[0]['price_corrector'];
                                if ($crtparicipationdetails != 'NO') {
                                    $royalty_obj_c->participate_id = $partId;
                                    $royalty_obj_c->crt_participate_id = $crtpartId;
                                    $royalty_obj_c->article_id = $crtparicipationdetails[0]['article_id'];
                                    $royalty_obj_c->user_id = $crtparicipationdetails[0]['corrector_id'];
                                    $royalty_obj_c->price = $crtparicipationdetails[0]['price_corrector'];
                                    $royalty_obj_c->correction = "yes";
                                    $royalty_obj_c->currency=$crtparicipationdetails[0]['currency'];
                                    $royalty_obj_c->insert();
                                    /////////send mail to corrector/////////////////////////////////
                                    if ($details[0]['correction'] == "yes") {
                                        $paricipationdetails = $crtparticipate_obj->getCrtParticipateDetails($crtpartId);
                                        $correctorDetails = $autoEmails->getContribUserDetails($paricipationdetails[0]['corrector_id']);
                                        if ($correctorDetails[0]['firstname'] != NULL)
                                            $parameters['corrector_name'] = $correctorDetails[0]['firstname'] . " " . $correctorDetails[0]['lastname'];
                                        else
                                            $parameters['corrector_name'] = $correctorDetails[0]['email'];

                                        $parameters['created_date'] = date("d/m/Y", strtotime($paricipationdetails[0]['created_at']));
                                        $parameters['document_link'] = "/client/ongoingao";
                                        $parameters['invoice_link'] = "/client/invoice";
                                        $parameters['royalty'] = $paricipationdetails[0]['price_corrector']." &".$details[0]['currency'].";";
                                        $parameters['article_title'] = $paricipationdetails[0]['title'];
                                        $parameters['articlename_link'] = "/contrib/mission-published?article_id=" . $artId;
                                        //sendidng the mail to corrector //
                                        $receiverId = $paricipationdetails[0]['corrector_id'];
                                        $autoEmails->messageToEPMail($receiverId, 59, $parameters);
                                    }
                                    /////*
                                    //unset($royalty_obj_c);
                                }
                            }
                        }
                        //unset($royalty_obj);    unset($royalty_obj_c);
                    }

                    $query = "article_id= '" . $artId . "' AND id = '" . $partId . "'";
                    $participate_obj->updateParticipation($data, $query);/////*

                    if ($details[0]['correction'] == "yes") {
                        if ($premium == 'NO')
                            $data = array("current_stage" => "client", "status" => "under_study");////////updating
                        else
                            $data = array("current_stage" => "client", "status" => "published");////////updating

                        $query = "article_id= '" . $artId . "' AND id = '" . $crtpartId . "'";
                        $crtparticipate_obj->updateCrtParticipation($data, $query);/////*
                    }

                    ///////update in article///////////
                    $data = array("file_path" => $recentversion[0]['article_path']);////////updating
                    $query = "id= '" . $artId . "'";

                    $article_obj->updateArticle($data, $query);/////*

                    /////udate status article process table///////
                    $this->insertStageRecord($partId, $recentversion[0]["version"], 's2', 'approved');


                    /////////////article history////////////////
                    $actparams['artId'] = $artId;
                    $actparams['stage'] = "stage2";
                    $actparams['action'] = "validated";
                    /////*$this->articleHistory(25,$actparams);
                    /////////////end of article history////////////////
                    /* *sending mail to Client**/
                    $clientDetails = $autoEmails->getUserDetails($paricipationdetails[0]['clientId']);

                    if ($clientDetails[0]['username'] != NULL)
                        $parameters['client_name'] = $clientDetails[0]['username'];
                    else
                        $parameters['client_name'] = $clientDetails[0]['email'];

                    //Insert Recent Activities
                    $ract = array("type" => "bopublish", "user_id" => $paricipationdetails[0]['clientId'], "activity_by" => "bo", "article_id" => $artId);
                    $recent_acts_obj->insertRecentActivities($ract);/////*

                    $deliveryId = $del_obj->getDeliveryID($artId);
                    if ($deliveryId != "NO") {
                        $checkLastAO = $del_obj->checkLastArticleAO($deliveryId);
                        if ($checkLastAO == "YES") {
                            $delcreateduser = $del_obj->getDelCreatedUser($deliveryId);          // print_r($delcreateduser);  exit;
                            $object = "L'appel d'offres " . $delcreateduser[0]['title'] . " vient d'&ecirc;tre valid&eacute; ";
                            $text_mail = "<p>Cher " . $delcreateduser[0]['first_name'] . " ,<br><br>
                                            Le dernier article de l'appel d'offres " . $delcreateduser[0]['title'] . " vient d'&ecirc;tre valid&eacute;!<br><br>
                                            Cliquez <a href=\"http://admin-ep-test.edit-place.com/ongoing/ao-details?client_id=" . $delcreateduser[0]['user_id'] . "&ao_id=" . $delcreateduser[0]['id'] . "&submenuId=ML2-SL4\">ici</a> si vous souhaitez acc&eacute;der &agrave; la page de suivi de l'AO.<br><br>
                                            Cordialement,<br>
                                            <br>
                                            Toute l'&eacute;quipe d&rsquo;Edit-place</p>";
                            $mail = new Zend_Mail();
                            $mail->addHeader('Reply-To', $this->configval['mail_from']);
                            $mail->setBodyHtml($text_mail)
                                ->setFrom($this->configval['mail_from'])
                                ->addTo($delcreateduser[0]['email'])
                                ->setSubject($object);
                            $mail->send();
                        }
                    }
                    //exit($partId.'***'.$artId);
                    //}
                    $partdetails = $user_obj->getUserDetails($participent_user_id);
                    if ($details[0]['correction'] === 'yes'){
                        $crtpartdetails = $user_obj->getUserDetails($corrector_user_id);
                        $price_corrector .= " &".$details[0]['currency'].";";
                    }
                    else {
                        $crtpartdetails[0]['first_name'] = $crtpartdetails[0]['last_name'] = $crtpartdetails[0]['email'] = $price_corrector = "N/A";
                    }
                    if ($details[0]['delivered_updated_by'] != '')
                        $delivered_updated_by = $user_obj->getUsername($details[0]['delivered_updated_by']);
                    else
                        $delivered_updated_by = "N/A{cron or auto}";

                    $htmltablebody .= "<tr>
                                <td>$artId</td>" .
                        "<td>" . $details[0]['title'] . "</td>" .
                        "<td>" . $details[0]['deliveryTitle'] . "</td>" .
                        "<td>http://admin-test.edit-place.co.uk/followup/prod?submenuId=ML13-SL4&cmid=" . $details[0]['contract_mission_id'] . "&index=0</td>" .
                        "<td>" . $details[0]['delivered_updated_at'] . "</td>" .
                        "<td>" . $delivered_updated_by . "</td>" .
                        "<td>" . $partdetails[0]['first_name'] . " " . $partdetails[0]['last_name'] . "</td>" .
                        "<td>" . $partdetails[0]['email'] . "</td>" .
                        "<td>" . $price_writer ." &".$details[0]['currency'].";</td>" .
                        "<td>" . $crtpartdetails[0]['first_name'] . " " . $crtpartdetails[0]['last_name'] . "</td>" .
                        "<td>" . $crtpartdetails[0]['email'] . "</td>" .
                        "<td>" . $price_corrector . "</td>
                            </tr>";

                    $limit++;//for testing purpose only
                }
             endforeach;
            $htmltableheading =  "<table border='1'>
                    <tr>
                        <th><b>Article Id</b></th>
                        <th><b>Article title</b></th>
                        <th><b>Delivery title</b></th>
                        <th><b>URL of mission</b></th>
                        <th><b>Delivered on</b></th>
                        <th><b>Delivered by</b></th>
                        <th><b>Writer Full Name</b></th>
                        <th><b>Writer Email</b></th>
                        <th><b>Royalties</b></th>
                        <th><b>Proofreader Full name</b></th>
                        <th><b>Proofreader Email</b></th>
                        <th><b>Royalties</b></th>
                    </tr>";
            $htmltable = $htmltableheading.$htmltablebody."</table>";

            echo $htmltable;
            $path =$_SERVER['DOCUMENT_ROOT']."/FO/documents/cronxlsx/";
            $filename = "auto-published-article-".date('d-M-Y-H-i-s').".xlsx";
            $cfilename = $path.$filename;
            $this->convertHtmltableToXlsx($htmltable,$cfilename,true);
            $email_obj =  new Ep_Ticket_AutoEmails();
            $email_obj->sendMail("rakeshm@edit-place.com", "Auto published article post 15 dyas of delivered UK TEST", "PFA", $path, $filename,"nass0069@gmail.com");
        }
        else{
            echo "scanned on ".date("d-M-Y H:i:s",time())." no record found";
        }
//        $this->_helper->FlashMessenger(utf8_decode('Articles Approuvé avec succès'));
//        $this->_redirect("/proofread/stage-deliveries?submenuId=ML3-SL3");
    }
    ////////////////inserting a new record into article process tabel when any action done in correction stages ////////////////////////
    public function insertStageRecord($partId,$version,$stage,$status)
    {
        $artProcess_obj = new EP_Article_ArticleProcess();//EP_Delivery_ArticleProcess();
        $recentDetials = $artProcess_obj->getVersionDetailsByVersion($partId, $version);
        $artProcess_obj->participate_id=$partId ;
        $artProcess_obj->user_id= "cron";
        $artProcess_obj->stage=$stage ;
        $artProcess_obj->status=$status ;
        $artProcess_obj->article_path=$recentDetials[0]["article_path"] ;
        $artProcess_obj->article_name=$recentDetials[0]["article_name"] ;
        $version = $version+1;
        $artProcess_obj->version=$version ;
        $artProcess_obj->article_doc_content=$recentDetials[0]["article_doc_content"] ;
        $artProcess_obj->article_words_count=$recentDetials[0]["article_words_count"] ;
        $artProcess_obj->comments=$recentDetials[0]["marks_comments"] ;
        $artProcess_obj->client_comments=$recentDetials[0]["client_comments"] ;
        $artProcess_obj->marks=$recentDetials[0]["marks"] ;
        $artProcess_obj->plag_percent=$recentDetials[0]["plag_percent"] ;
        $artProcess_obj->plagxml=$recentDetials[0]["plagxml"] ;
        $artProcess_obj->moderate_epdecision=$recentDetials[0]["moderate_epdecision"] ;
        $artProcess_obj->art_file_size_limit_email=$recentDetials[0]["art_file_size_limit_email"] ;
        $artProcess_obj->insert();
        if($stage == 'corrector' &&  $status == 'disapproved'){
            $recentversion= $artProcess_obj->getRecentVersion($partId);
            $data = array("moderate_epdecision"=>"accepted");////////updating
            $query = "id= '".$recentversion[0]["id"]."'";
            $artProcess_obj->updateArticleProcess($data,$query);
        }

    }
    /* *** added on 19.02.2016 *** */
    /* To generate XLSX File */
    function convertHtmltableToXlsx($htmltable,$filename,$extract=FALSE)
    {
        require_once APP_PATH_ROOT.'nlibrary/script/PHPExcel.php';

        $htmltable = strip_tags($htmltable, "<table><tr><th><thead><tbody><tfoot><td><br><br /><b><span>");
        $htmltable = str_replace("<br />", "\n", $htmltable);
        $htmltable = str_replace("<br/>", "\n", $htmltable);
        $htmltable = str_replace("<br>", "\n", $htmltable);
        $htmltable = str_replace("&nbsp;", " ", $htmltable);
        $htmltable = str_replace("\n\n", "\n", $htmltable);


        $dom = new domDocument;
        $dom->loadHTML($htmltable);

        if(!$dom) {
            echo "<br />Invalid HTML DOM, nothing to Export.";
            exit;
        }
        $dom->preserveWhiteSpace = false;
        $tables = $dom->getElementsByTagName('table');

        if(!is_object($tables)) {
            echo "<br />Invalid HTML Table DOM, nothing to Export.";
            exit;
        }

        $tbcnt = $tables->length - 1;


        $username = "EditPlace";
        $usermail = "user@edit-place.com";
        $usercompany = "Edit Place";
        $debug = false;

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Verdana');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
        $tm = date("YmdHis");
        $pos = strpos($usermail, "@");
        $user = substr($usermail, 0, $pos);
        $user = str_replace(".","",$user);


        $objPHPExcel->getProperties()->setCreator($username)
            ->setLastModifiedBy($username)
            ->setTitle("Sales Generation")
            ->setSubject("Sales Final Validation")
            ->setDescription("Sales Report")
            ->setKeywords("Sales")
            ->setCompany($usercompany)
            ->setCategory("Export");

        $xcol = '';
        $xrow = 1;
        $usedhdrows = 0;
        for($z=0;$z<=$tbcnt;$z++) {
            $headrows = array();
            $bodyrows = array();
            $r = 0;
            $h = 0;
            $maxcols = 0;
            $totrows = 0;
            $rows = $tables->item($z)->getElementsByTagName('tr');
            $totrows = $rows->length;

            foreach ($rows as $row) {
                $ths = $row->getElementsByTagName('th');
                if(is_object($ths)) {
                    if($ths->length > 0) {
                        $headrows[$h]['colcnt'] = $ths->length;
                        if($ths->length > $maxcols) {
                            $maxcols = $ths->length;
                        }
                        $nodes = $ths->length - 1;
                        for($x=0;$x<=$nodes;$x++) {
                            $thishdg = $ths->item($x)->nodeValue;
                            $headrows[$h]['th'][] = $thishdg;
                            $headrows[$h]['bold'][] = $this->findBoldText($this->innerHTML($ths->item($x)));
                            if($ths->item($x)->hasAttribute('style')) {
                                $style = $ths->item($x)->getAttribute('style');
                                $stylecolor = $this->findStyleColor($style);
                                if($stylecolor == '') {
                                    $headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
                                }else{
                                    $headrows[$h]['color'][] = $stylecolor;
                                }
                                $fontsize = $this->findFontSize($style);
                                if($fontsize=='')
                                    $headrows[$h]['size'][] = 11;
                                else
                                    $headrows[$h]['size'][] = $fontsize;
                            }else{
                                $headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
                                $headrows[$h]['size'][] = 11;
                            }
                            if($ths->item($x)->hasAttribute('colspan')) {
                                $headrows[$h]['colspan'][] = $ths->item($x)->getAttribute('colspan');
                            }else{
                                $headrows[$h]['colspan'][] = 1;
                            }
                            if($ths->item($x)->hasAttribute('align')) {
                                $headrows[$h]['align'][] = $ths->item($x)->getAttribute('align');
                            }else{
                                $headrows[$h]['align'][] = 'left';
                            }
                            if($ths->item($x)->hasAttribute('valign')) {
                                $headrows[$h]['valign'][] = $ths->item($x)->getAttribute('valign');
                            }else{
                                $headrows[$h]['valign'][] = 'top';
                            }
                            if($ths->item($x)->hasAttribute('bgcolor')) {
                                $headrows[$h]['bgcolor'][] = str_replace("#", "", $ths->item($x)->getAttribute('bgcolor'));
                            }else{
                                $headrows[$h]['bgcolor'][] = 'FFFFFF';
                            }
                            if($ths->item($x)->hasAttribute('class')) {
                                $headrows[$h]['class'][] = str_replace("#", "", $ths->item($x)->getAttribute('bgcolor'));
                            }else{
                                $headrows[$h]['bgcolor'][] = 'FFFFFF';
                            }
                        }
                        $h++;
                    }
                }
                /* Getting TD's */

                $tds = $row->getElementsByTagName('td');
                if(is_object($tds)) {
                    if($tds->length > 0) {
                        $bodyrows[$r]['colcnt'] = $tds->length;
                        if($tds->length > $maxcols) {
                            $maxcols = $tds->length;
                        }
                        $nodes = $tds->length - 1;
                        for($x=0;$x<=$nodes;$x++) {
                            $thistxt = $tds->item($x)->nodeValue;
                            $bodyrows[$r]['td'][] = $thistxt;
                            $bodyrows[$r]['bold'][] = $this->findBoldText($this->innerHTML($tds->item($x)));

                            if($tds->item($x)->hasAttribute('style')) {
                                $style = $tds->item($x)->getAttribute('style');
                                $stylecolor = $this->findStyleColor($style);
                                if($stylecolor == '') {
                                    $bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
                                }else{
                                    $bodyrows[$r]['color'][] = $stylecolor;
                                }
                                $fontsize = $this->findFontSize($style);
                                if($fontsize=='')
                                    $bodyrows[$r]['size'][] = 10;
                                else
                                    $bodyrows[$r]['size'][] = $fontsize;
                            }else{
                                $bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
                                $bodyrows[$r]['size'][] = 10;
                            }
                            if($tds->item($x)->hasAttribute('colspan')) {
                                $bodyrows[$r]['colspan'][] = $tds->item($x)->getAttribute('colspan');
                            }else{
                                $bodyrows[$r]['colspan'][] = 1;
                            }
                            if($tds->item($x)->hasAttribute('align')) {
                                $bodyrows[$r]['align'][] = $tds->item($x)->getAttribute('align');
                            }else{
                                $bodyrows[$r]['align'][] = 'left';
                            }
                            if($tds->item($x)->hasAttribute('valign')) {
                                $bodyrows[$r]['valign'][] = $tds->item($x)->getAttribute('valign');
                            }else{
                                $bodyrows[$r]['valign'][] = 'top';
                            }
                            if($tds->item($x)->hasAttribute('bgcolor')) {
                                $bodyrows[$r]['bgcolor'][] = str_replace("#", "", $tds->item($x)->getAttribute('bgcolor'));
                            }else{
                                $bodyrows[$r]['bgcolor'][] = 'FFFFFF';
                            }
                        }
                        $r++;
                    }
                }

                /* End of TD's */
            }

            $worksheet = $objPHPExcel->getActiveSheet();                // set worksheet we're working on
            $style_overlay = array('font' =>
                array('color' =>
                    array('rgb' => '000000'),'bold' => false,),
                'fill' 	=>
                    array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCFF')),
                'alignment' =>
                    array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP),
                /*'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                   'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                   'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                   'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),*/
            );

            $heightvars = array(1=>'42', 2=>'42', 3=>'48', 4=>'52', 5=>'58', 6=>'64', 7=>'68', 8=>'76', 9=>'82');
            for($h=0;$h<count($headrows);$h++) {
                $th = $headrows[$h]['th'];
                $colspans = $headrows[$h]['colspan'];
                $aligns = $headrows[$h]['align'];
                $valigns = $headrows[$h]['valign'];
                $bgcolors = $headrows[$h]['bgcolor'];
                $colcnt = $headrows[$h]['colcnt'];
                $colors = $headrows[$h]['color'];
                $bolds = $headrows[$h]['bold'];
                $sizes = $headrows[$h]['size'];
                $usedhdrows++;
                $mergedcells = false;
                for($t=0;$t<count($th);$t++) {
                    if($xcol == '') {$xcol = 'A';}else{$xcol++;}
                    $thishdg = $th[$t];
                    $thisalign = $aligns[$t];
                    $thisvalign = $valigns[$t];
                    $thiscolspan = $colspans[$t];
                    $thiscolor = $colors[$t];
                    $thisbg = $bgcolors[$t];
                    $thisbold = $bolds[$t];
                    $thissize = $sizes[$t];
                    $strbold = ($thisbold==true) ? 'true' : 'false';
                    if($thisbg == 'FFFFFF') {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
                    }else{
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                    }
                    $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
                    $style_overlay['alignment']['horizontal'] = $thisalign;
                    $style_overlay['font']['color']['rgb'] = $thiscolor;
                    $style_overlay['font']['bold'] = $thisbold;
                    $style_overlay['font']['size'] = $thissize;
                    $style_overlay['fill']['color']['rgb'] = $thisbg;
                    $worksheet->setCellValue($xcol.$xrow, $thishdg);
                    $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);

                    if($thiscolspan > 1) {                                                // spans more than 1 column
                        $mergedcells = true;
                        $lastxcol = $xcol;
                        for($j=1;$j<$thiscolspan;$j++) {
                            $lastxcol++;
                            $worksheet->setCellValue($lastxcol.$xrow, '');
                            $worksheet->getStyle($lastxcol.$xrow)->applyFromArray($style_overlay);
                        }
                        $cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;

                        $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
                        $num_newlines = substr_count($thishdg, "\n");                       // count number of newline chars
                        if($num_newlines > 1) {
                            $rowheight = $heightvars[1];                                      // default to 35
                            if(array_key_exists($num_newlines, $heightvars)) {
                                $rowheight = $heightvars[$num_newlines];
                            }else{
                                $rowheight = 75;
                            }
                            $worksheet->getRowDimension($xrow)->setRowHeight($rowheight);     // adjust heading row height
                        }
                        $xcol = $lastxcol;
                    }
                }
                $xrow++;
                $xcol = '';
            }

            $usedhdrows++;

            for($b=0;$b<count($bodyrows);$b++) {
                $td = $bodyrows[$b]['td'];
                $colcnt = $bodyrows[$b]['colcnt'];
                $colspans = $bodyrows[$b]['colspan'];
                $aligns = $bodyrows[$b]['align'];
                $valigns = $bodyrows[$b]['valign'];
                $bgcolors = $bodyrows[$b]['bgcolor'];
                $colors = $bodyrows[$b]['color'];
                $bolds = $bodyrows[$b]['bold'];
                $sizes = $bodyrows[$b]['size'];
                for($t=0;$t<count($td);$t++) {
                    if($xcol == '') {$xcol = 'A';}else{$xcol++;}
                    $thistext = $td[$t];
                    $thisalign = $aligns[$t];
                    $thisvalign = $valigns[$t];
                    $thiscolspan = $colspans[$t];
                    $thiscolor = $colors[$t];
                    $thisbg = $bgcolors[$t];
                    $thisbold = $bolds[$t];
                    $thissize = $sizes[$t];
                    $strbold = ($thisbold==true) ? 'true' : 'false';
                    if($thisbg == 'FFFFFF') {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
                    }else{
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                    }

                    $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
                    $style_overlay['alignment']['horizontal'] = $thisalign;
                    $style_overlay['font']['color']['rgb'] = $thiscolor;
                    $style_overlay['font']['bold'] = $thisbold;
                    $style_overlay['font']['size'] = $thissize;
                    $style_overlay['fill']['color']['rgb'] = $thisbg;
                    if($thiscolspan == 1) {
                        $worksheet->getColumnDimension($xcol)->setWidth(20);
                    }
                    else
                    {
                        $worksheet->getColumnDimension($xcol)->setWidth($thiscolspan*5);
                    }
                    $worksheet->setCellValue($xcol.$xrow, $thistext);

                    $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);
                    if($thiscolspan > 1) {                                                // spans more than 1 column
                        $lastxcol = $xcol;
                        for($j=1;$j<$thiscolspan;$j++) {
                            $lastxcol++;
                        }
                        $cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;
                        $worksheet->mergeCells($cellRange);
                        $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
                        $xcol = $lastxcol;
                    }
                }



                $xrow++;
                $xcol = '';
            }

            $azcol = 'A';
            for($x=1;$x==$maxcols;$x++) {
                $worksheet->getColumnDimension($azcol)->setAutoSize(true);
                $azcol++;
            }

        }
        // $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save($filename);
    }

    function innerHTML($node)
    {
        $doc = $node->ownerDocument;
        $frag = $doc->createDocumentFragment();
        foreach ($node->childNodes as $child) {
            $frag->appendChild($child->cloneNode(TRUE));
        }
        return $doc->saveXML($frag);
    }

    function findSpanColor($node)
    {
        $pos = stripos($node, "color:");
        if ($pos === false) {
            return '000000';
        }
        $node = substr($node, $pos);
        $start = "#";
        $end = ";";
        $node = " ".$node;
        $ini = stripos($node,$start);
        if ($ini === false) return "000000";
        $ini += strlen($start);
        $len = stripos($node,$end,$ini) - $ini;
        return substr($node,$ini,$len);
    }

    function findStyleColor($style)
    {
        $pos = stripos($style, "color:");
        if ($pos === false) {
            return '';
        }
        $style = substr($style, $pos);
        $start = "#";
        $end = ";";
        $style = " ".$style;
        $ini = stripos($style,$start);
        if ($ini === false) return "";
        $ini += strlen($start);
        $len = stripos($style,$end,$ini) - $ini;
        return substr($style,$ini,$len);
    }

    function findFontSize($style)
    {
        $pos = stripos($style, "font-size:");
        if ($pos === false) {
            return '';
        }
        $style = substr($style, $pos);
        return substr($style,stripos($style,":")+1,strlen(stripos($style,"px")));
    }

    function findBoldText($node)
    {
        $pos = stripos($node, "<b>");
        if ($pos === false) {
            return false;
        }
        return true;
    }
    /* End of Generation of XLSX File */
}

