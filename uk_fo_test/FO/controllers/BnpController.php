<?php
/**
 * EditPlaceController
 * This controller is responsible for Bnp Operations*
 * @author Arun
 * @editor Naseer
 * @version 1.0
 */
 require_once('ContribController.php');
Class BnpController Extends ContribController
{
    public function init()
	{
		parent::init();
		
	}
	//connecting to linode server
	public function getSFTPobjectAction()
    {
        if(!is_object($this->sftp)) :
            $this->ssh2_server = "50.116.62.9" ;
            $this->ssh2_user_name = "oboulo" ;
            $this->ssh2_user_pass = "3DitP1ace" ;

            require_once APP_PATH_ROOT.'nlibrary/script/Net/SFTP.php' ;

            $this->sftp = new Net_SFTP($this->ssh2_server);
            if (!$this->sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }
        endif ;
    }
	
	/**Send stencils Articles of contributor to Ep team**/
    public function sendStencilsAction()
    {
		
		
		$participation=new Ep_Participation_Participation();
        $corrector_obj=new Ep_Participation_CorrectorParticipation();
        $article_obj=new Ep_Article_Article();
        $userIdentifier=$this->contrib_identifier;
        $autoEmails=new Ep_Ticket_AutoEmails();

        $missionParams=$this->_request->getParams();
        $participation_id=$missionParams['participation_id'];
        $client_id=$missionParams['clientId'];

		/*getting and combining stencils text*/
//        $patterns = array('/œ/','/Œ/');
//        $replace = array('oe','OE');
		//$patterns = array("'œ'", "'Œ'", "'Æ'","'æ'");
		//$replace = array('oe', 'Oe', 'Ae', 'ae');
        $missionParams['bnp_text'] = str_replace('<br />','<br>',$missionParams['bnp_text']);
        $bnp_text=$bnp_text_file=implode( "###$$$###",$missionParams['bnp_text'] );
		//$bnp_text= utf8_decode($bnp_text);//utf8_decode(str_replace(array('&#39;','&nbsp;'),array("'",' '),html_entity_decode($bnp_text,ENT_COMPAT,"UTF-8")));//converting into htmlentity to normal text ->replacing single quotes -> utf8_decoding before saving into DB
		$bnp_text= utf8_decode(str_replace(array('&#39;','&nbsp;','Œ','œ','€','’','‘'),array("'",' ','&OElig;','&oelig;','&euro;','&#8217;','&#8216;'),html_entity_decode($bnp_text,ENT_COMPAT,"UTF-8")));//converting into htmlentity to normal text ->replacing single quotes -> utf8_decoding before saving into DB
//        echo $bnp_text;exit;
//		echo "<pre>";print_r($missionParams); exit;

		

        if($this->_helper->EpCustom->checksession())
        {	
			
			if($bnp_text && $participation_id!='')
			{
				$participationId=$participation_id;				
				$participationDetails=$participation->getParticipationDetails($participationId);

				$participation_status=$participationDetails[0]['status'];
				//echo $participationId."--".$participation_status;exit;

				if($participation_status=='bid' || $participation_status=='disapproved' || $participation_status=='disapprove_client')
				{
					$articleDir=$this->articles_path.$participationDetails[0]['article_id']."/";
					$articleName=$participationDetails[0]['article_id']."_".$participationDetails[0]['user_id']."_".mt_rand(10000,99999);//.txt
                    $bnp_text_file_plain = utf8_decode(strip_tags(str_replace(array('&#39;','&nbsp;','Œ','œ','€','’','‘'),array("'",' ','&OElig;','&oelig;','&euro;','&#8217;','&#8216;'),html_entity_decode($bnp_text_file,ENT_COMPAT,"UTF-8"))));
                    $bnp_text_file_html = utf8_decode((str_replace(array('&#39;','&nbsp;','Œ','œ','€','’','‘'),array("'",' ','&OElig;','&oelig;','&euro;','&#8217;','&#8216;'),html_entity_decode($bnp_text_file,ENT_COMPAT,"UTF-8"))));
                    $article_path = $this->createArticleFile($articleDir,$articleName,$bnp_text_file_plain,"");
                    $article_path_html = $this->createArticleFile($articleDir,$articleName,$bnp_text_file_html,"html");

					//echo $article_path;exit;				
					

					if (file_exists($article_path))
					{
						chmod($article_path,0777);
						
						/*updating participation table*/
						$participationUpdate['updated_at']=date('Y-m-d h:i:s');
						$participationUpdate['current_stage']='stage0';
						$participationUpdate['status']='under_study';						
						$participation->updateParticipationDetails($participationUpdate,$participationId);
						
						
						//Antiword obj to get content from uploaded article
						$antiword_obj=new Ep_Antiword_Antiword($article_path);
						$article_doc_content=$bnp_text;
						$article_words_count=$antiword_obj->count_words(str_replace("###$$$###",' ',$bnp_text));
						
						/**Insert into Article Process Table**/
						$ArticleProcess=new EP_Article_ArticleProcess();
						$maxversion=$ArticleProcess->getLatestVersion($participationId);
						if($maxversion[0]['latestVersion'])
							$version=$maxversion[0]['latestVersion'];
						else
							$version=1;
						
						$ArticleProcess->participate_id=$participationId;
						$ArticleProcess->user_id=$participationDetails[0]['user_id'];
						$ArticleProcess->article_path=$participationDetails[0]['article_id']."/".$articleName.".txt";
						$ArticleProcess->version=$version;
						$ArticleProcess->article_doc_content=($article_doc_content);
						$ArticleProcess->article_words_count=$article_words_count;
						$ArticleProcess->stage='contributor';
						$ArticleProcess->article_name="BNP_file_".$participationDetails[0]['article_id'].".txt";
						$ArticleProcess->insert();
						

						//Article History insertion
						$hist_obj = new Ep_Article_ArticleHistory();
						$action_obj = new Ep_Article_ArticleActions();
						$history9=array();
						$history9['user_id']=$this->contrib_identifier;
						$history9['article_id']=$participationDetails[0]['article_id'];
						$sentence9=$action_obj->getActionSentence(9);
						$ArtDetails=$article_obj->getArticleAOdetails($participationDetails[0]['article_id']);
						$article_name='<b>'.$ArtDetails[0]['title'].'</b>';						
						$contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$this->contrib_identifier.'" target="_blank"><b>'.$this->_view->client_email.'</b></a>';
						$actionmessage=strip_tags($sentence9);
						eval("\$actionmessage= \"$actionmessage\";");
						$history9['stage']=$participationUpdate['current_stage'];
						$history9['action']='article_sent';
						$history9['action_sentence']=$actionmessage;
						$hist_obj->insertHistory($history9);
						
						
						/**Code to launch/assign artilce to proofreader if it is a Correction Mission*/
												
						
						$delivery=new Ep_Article_Delivery();
						$delivery_details=$delivery->getDeliveryDetails($participationDetails[0]['article_id']);						
						/**plagairism check*/
						$plagiarism_check=$delivery_details[0]['plagiarism_check'];
						$correctorAO=$article_obj->checkCorrectorAO($participationDetails[0]['article_id']);
//						if($correctorAO=='YES')
//						{
//
//							/**check whether article is already assigned to a corrector or not**/
//							$corrector_participation_id=$corrector_obj->checkCorrectorParticipationExists($participationDetails[0]['article_id']);
//
//							$corrector_participation_details=$corrector_obj->getCorrectorParticipationDetails($participation_id);
//
//							if($this->corrector_type=='senior')
//							{
//								if($delivery_details[0]['correction_sc_submission'])
//									$correction_submission=$delivery_details[0]['correction_sc_submission'];
//								else
//									$correction_submission=$this->config['correction_sc_submission'];
//							}
//							else
//							{
//								if($delivery_details[0]['correction_jc_submission'])
//									$correction_submission=$delivery_details[0]['correction_jc_submission'];
//								else
//									$correction_submission=$this->config['correction_jc_submission'];
//							}
//
//							if($corrector_participation_id!="NO")
//							{
//
//								//$expires=time()+(60*60*$correction_submission);
//								$expires=time()+(60*$correction_submission);
//								$cparticipationUpdate['participate_id']=$participationId;
//								$cparticipationUpdate['corrector_submit_expires']=$expires;
//								$corrector_obj->updateParticipationDetails($cparticipationUpdate,$corrector_participation_id);
//
//								/**send mail to corrector*/
//								$autoEmail=new Ep_Ticket_AutoEmails();
//								$correctorId=$corrector_participation_details[0]['corrector_id'];
//								$refused_at=$participationDetails[0]['updated_at'];
//								$parameters['article_title']=$deliveryDetails[0]['articleName'];
//								$parameters['updated_at']=date("d-m-Y H:i:s",strtotime($refused_at));
//								$parameters['ongoinglink']='/contrib/mission-corrector-deliver?article_id='.$participationDetails[0]['article_id'];
//								//$autoEmail->messageToEPMail($correctorId,62,$parameters);
//
//
//							}
//							else
//							{
//								if($delivery_details[0]['correction_participation'])
//								{
//									$article_obj=new Ep_Article_Article();
//									$expires=time()+(60*$delivery_details[0]['correction_participation']);
//									$data_array = array("correction_participationexpires"=>$expires);////////updating
//									$query=" id='".$participationDetails[0]['article_id']."'";
//									$article_obj->updateArticle($data_array,$query);
//								}
//
//							}
//
//						}
//
//						/*launching Corrector AO*/
///*						if($correctorAO=='YES' && $corrector_participation_id=="NO")
//						{
//
//							$emailSend=$delivery_details[0]['corrector_mail'];
//							$corrector_list=$delivery_details[0]['corrector_list'];
//							$parameters['article_title']=$delivery_details[0]['articleName'];
//							$parameters['corrector_ao_link']='/contrib/aosearch';
//							$parameters['participation_expires']=$expires;
//
//							if($emailSend=='yes')
//							{
//								$this->sendCorrectionAOCreationMail($parameters,$corrector_list,$participationDetails[0]['article_id']);
//							}
//						}
//						$uploaded_status=1;
//*/

						//Inserting Recent Activities
						$activity_obj=new Ep_User_RecentActivities();
						$client_id=$delivery_details[0]['user_id'];
						if($delivery_details[0]['premium_option']=='0')
						{
							if($client_id)
							{
								$activity_array['type']='sentarticle';
								$activity_array['created_at']=date("Y-m-d H:i:s");
								$activity_array['user_id']=$client_id;
								$activity_array['activity_by']=$userIdentifier;
								$activity_array['article_id']=$participationDetails[0]['article_id'];
								$activity_obj->insertRecentActivities($activity_array);
							}
						}

						//sending notify emails to bo user when writersends the article
						$writer_notify=$delivery_details[0]['writer_notify'];
						if($writer_notify=='yes')
						{
							$autoEmails=new Ep_Ticket_AutoEmails();
							$ticket_obj= new Ep_Ticket_Ticket();

							$bo_user=$delivery_details[0]['created_user'];

							$mail_params['AO_title']=$delivery_details[0]['deliveryTitle'];
							$mail_params['article_name']=$delivery_details[0]['articleName'];
							$mail_params['contributor_name']=$this->_view->client_email;
							$mail_params['bo_user']=$ticket_obj->getUserName($bo_user,TRUE);

							$mail_params['comment_bo_link']='/ongoing/ao-details?client_id='.$delivery_details[0]['user_id'].'&ao_id='.$delivery_details[0]['id'].'&submenuId=ML2-SL4';

							$autoEmails->messageToEPMail($bo_user,101,$mail_params,TRUE);
						}						
					}
					else
					{
						$status='error';
						//exit;
					}
				}
				else
				{
					$status='file_sent';
				}
			}

            echo "success";
			//$this->_redirect($referer."&status=$status");

            //$this->_helper->FlashMessenger('Article envoy&eacute;');
            //$this->_redirect("/contrib/ongoing");
        }
    }

    public function createArticleFile($articleDir,$articleName,$bnp_text_file,$type=false){
        if($type === 'html')
            $articleName=$articleName."_".$type.".txt";
        else
            $articleName=$articleName.".txt";
        if(!is_dir($articleDir))
            mkdir($articleDir,TRUE);
        chmod($articleDir,0777);
        $article_path=$articleDir.$articleName;

        //create text bnp file
        $bnp_file = fopen($article_path,"w");
        fwrite($bnp_file,$bnp_text_file);
        fclose($bnp_file);
        return $article_path;
    }
	
	/**Send Corrector  stecils Articles of contributor to Ep team**/
    public function sendCorrectorStencilsAction()
    {
        header('Content-Type: text/html; charset=utf-8');
        error_reporting(E_ERROR | E_PARSE);
        $participation=new Ep_Participation_Participation();
        $corrector_participation=new Ep_Participation_CorrectorParticipation();
        $article_obj=new Ep_Article_Article();
        $autoEmails=new Ep_Ticket_AutoEmails();
        $userIdentifier=$this->contrib_identifier;
        $delivery_obj = new Ep_Article_Delivery();
        $corrector_params=$this->_request->getParams();        

        $participatedetails= $corrector_participation->getParticipationDetails($corrector_params['cparticipation_id']);  ///getting the paricipate id from participate atable/
        $crtpartcipate_id = $corrector_params['cparticipation_id'];
        $partcipate_id = $participatedetails[0]['participate_id'];   /////participate id of participation table////
        $refused_count= $participation->getRefusedCount($partcipate_id);
        if($refused_count!="NO")
            $refusedcountupdated =$refused_count[0]['refused_count'];
        $refusedcountupdated++;
		
		
		/*getting and combining stencils text*/
        $corrector_params['bnp_text'] = str_replace('<br />','<br>',$corrector_params['bnp_text']);
        $bnp_text=$bnp_text_file=implode("###$$$###",$corrector_params['bnp_text']);
		$bnp_text= utf8_decode($bnp_text);//utf8_decode(str_replace(array('&#39;','&nbsp;'),array("'",' '),html_entity_decode($bnp_text,ENT_COMPAT,"UTF-8")));//converting into htmlentity to normal text ->replacing single quotes -> utf8_decoding before saving into DB
		$bnp_text= utf8_decode(str_replace(array('&#39;','&nbsp;','Œ','œ','€','’','‘'),array("'",' ','&OElig;','&oelig;','&euro;','&#8217;','&#8216;'),html_entity_decode($bnp_text,ENT_COMPAT,"UTF-8")));//converting into htmlentity to normal text ->replacing single quotes -> utf8_decoding before saving into DB
//        utf8_decode($bnp_text);

		//print_r($corrector_params);  exit;
		

        if($this->_helper->EpCustom->checksession())
        {

            $action_function=$corrector_params['function'];
            $marks=$corrector_params['marksvald'];
            $marksreasons = $corrector_params["marksvaldwithreason"];
            $client_id = $corrector_params['client_id'];
            $comments=$corrector_params['corrector-comment'];
			
            if($action_function=='approve')
            {
                if($bnp_text && $crtpartcipate_id!='')
				{                  
					
					$participationId = $crtpartcipate_id;
					$corrector_participationDetails=$corrector_participation->getParticipationDetails($participationId);					
					$participation_status=$corrector_participationDetails[0]['status'];					

					//echo $participation_status;exit;
					
					if($participation_status=='bid' || $participation_status=='disapproved')
					{
						$articleDir=$this->articles_path.$corrector_participationDetails[0]['article_id']."/";
						$articleName=$corrector_participationDetails[0]['article_id']."_".$corrector_participationDetails[0]['corrector_id']."_".mt_rand(10000,99999);//.".txt";
                        $bnp_text_file_plain = utf8_decode(str_replace(array('&#39;','&nbsp;','Œ','œ','€','’','‘'),array("'",' ','&OElig;','&oelig;','&euro;','&#8217;','&#8216;'),html_entity_decode($bnp_text_file,ENT_COMPAT,"UTF-8")));
                        $bnp_text_file_html = utf8_decode((str_replace(array('&#39;','&nbsp;','Œ','œ','€','’','‘'),array("'",' ','&OElig;','&oelig;','&euro;','&#8217;','&#8216;'),html_entity_decode($bnp_text_file,ENT_COMPAT,"UTF-8"))));
                        $article_path = $this->createArticleFile($articleDir,$articleName,$bnp_text_file_plain,"");
                        $article_path_html = $this->createArticleFile($articleDir,$articleName,$bnp_text_file_html,"html");




						
						//echo $article_path;exit;
						
						if (file_exists($article_path))
						{
							chmod($article_path,0777);
							
							$delivery=new Ep_Article_Delivery();
							$deliveryDetails = $delivery_obj->getDeliveryDetails($corrector_participationDetails[0]['article_id']);							
							
							$participationUpdate['updated_at']=date('Y-m-d H:i:s');	
							$missionTest=$deliveryDetails[0]['missiontest'];
							$correctionType=$deliveryDetails[0]['correctionType'];

							if($missionTest=='yes' &&  $correctionType=='multi_external')
							{
								$participationUpdate['status']='under_study';
								$participationUpdate['current_stage']='mission_test';
							}
							else
							{
								$participationUpdate['status']='under_study';
								$participationUpdate['current_stage']='stage2';
							}							
							//print_r($participationUpdate); exit;
							
							$corrector_participation->updateParticipationDetails($participationUpdate,$participationId);
							

							/***get contributor Participation **/
							$contrib_participation_details=$participation->getContribParticipation($corrector_participationDetails[0]['article_id']);
							$contrib_participation_id= $contrib_participation_details[0]['id'];
							$contrib_participationUpdate['updated_at']=date('Y-m-d H:i:s');

							if($missionTest=='yes' &&  $correctionType=='multi_external')
							{
								$contrib_participationUpdate['status']='under_study';
								$contrib_participationUpdate['current_stage']='mission_test';
							}
							else
							{
								$contrib_participationUpdate['status']='under_study';
								$contrib_participationUpdate['current_stage']='stage2';
							}
							$contrib_participationUpdate['marks']=$marks;
							$contrib_participationUpdate['corrector_id']=$corrector_participationDetails[0]['corrector_id'];							
							//echo "<pre>";print_r($contrib_participationUpdate); echo $contrib_participation_id; exit;
							
							$participation->updateParticipationDetails($contrib_participationUpdate,$contrib_participation_id);
							
							
							//Antiword obj to get content from uploaded article
							$antiword_obj=new Ep_Antiword_Antiword($article_path);
							$article_doc_content=$bnp_text;
							$article_words_count=$antiword_obj->count_words(str_replace("###$$$###",' ',$bnp_text));
						    //echo $article_doc_content."--".$article_words_count;exit;
							
							
							/**get latest version*/
							$ArticleProcess=new EP_Article_ArticleProcess();
							$latest_version=$ArticleProcess->getLatestVersion($contrib_participation_id) ;
							
							/**Insert into Article Process Table**/
							$ArticleProcess->participate_id=$contrib_participation_id;
							$ArticleProcess->user_id=$corrector_participationDetails[0]['corrector_id'];
							$ArticleProcess->article_path=$corrector_participationDetails[0]['article_id']."/".$articleName.".txt";
							$ArticleProcess->version=$latest_version[0]['latestVersion'];
							$ArticleProcess->article_doc_content=($article_doc_content);
							$ArticleProcess->article_words_count=$article_words_count;
							$ArticleProcess->stage='corrector';
							$ArticleProcess->article_name="BNP_file_".$corrector_participationDetails[0]['article_id'].".txt";
							$ArticleProcess->comments=utf8_decode($comments);
							$ArticleProcess->marks=$marks;
							$ArticleProcess->reasons_marks=$corrector_params["marksvaldwithreason"] ;
							
							//echo "<pre>";print_r($ArticleProcess);exit;							
							$ArticleProcess->insert();
							
							
							/**sending mail to AO created User**/
							$ao_created_user= $deliveryDetails[0]['created_user'];
							$parameters_valid['article_title']=$deliveryDetails[0]['articleName'];
							$parameters_valid['AO_title']=$deliveryDetails[0]['deliveryTitle'];

							$ticket_obj=new Ep_Ticket_Ticket();
							$parameters_valid['bo_user']= $ticket_obj->getUsername($ao_created_user,true);
							$parameters_valid['contributor_name']=$ticket_obj->getUsername($participatedetails[0]['user_id']);
							$parameters_valid['corrector_name']=$ticket_obj->getUsername($userIdentifier);
							$autoEmails->messageToEPMail($ao_created_user,65,$parameters_valid,true);
						}
						$this->_helper->FlashMessenger('Article envoy&eacute;');                   

						//action sentence
						$contributor_identifier=$participatedetails[0]['user_id'];
						$action_sentence_id=39;
					}
                }
            }
            /////when dissaporved by corrector//////////
            else if($action_function=='disapprove')
            {

                $deliveryDetails = $delivery_obj->getDeliveryDetails($corrector_params['article_id']);
				//print_r($deliveryDetails);exit; 
                if($this->profileType=='senior')
                {
                    if($deliveryDetails[0]['article_sc_resubmission'])
                        $resubmission=$deliveryDetails[0]['article_sc_resubmission'];
                    else
                        $resubmission=$this->config['sc_resubmission'];
                }
                else if($this->profileType=='junior')
                {
                    if($deliveryDetails[0]['article_jc_resubmission'])
                        $resubmission=$deliveryDetails[0]['article_jc_resubmission'];
                    else
                        $resubmission=$this->config['jc_resubmission'];
                }
                else if($this->profileType=='sub-junior')
                {
                    if($deliveryDetails[0]['article_jc0_resubmission'])
                        $resubmission=$deliveryDetails[0]['article_jc0_resubmission'];
                    else
                        $resubmission=$this->config['jc0_resubmission'];
                }
                //$expires=time()+(60*60*$resubmission);
                $expires=time()+(60*$resubmission);
                $accept_refuse_at=date("Y-m-d H:i:s");
                /**updating the contributor participation for resubmitting the article*/
                /*$data = array("status"=>"disapproved", "current_stage"=>"corrector",
                "article_submit_expires"=>$expires, "refused_count"=>$refusedcountupdated,
                "corrector_id"=>$userIdentifier,"marks"=>$marks);*/
                //$data = array("status"=>"disapproved_temp", "current_stage"=>"corrector",                 "corrector_id"=>$userIdentifier,"updated_at"=>$accept_refuse_at);
				
				if($deliveryDetails[0]['nomoderation']=="yes")
				{
					$data = array("status"=>"disapproved", "current_stage"=>"contributor", "article_submit_expires"=>$expires, "refused_count"=>$refusedcountupdated, "corrector_id"=>$userIdentifier,"marks"=>$marks,"moderate_closed"=>"yes");
				}
				else
				{
					$data = array("status"=>"disapproved_temp", "current_stage"=>"corrector", "corrector_id"=>$userIdentifier,"updated_at"=>$accept_refuse_at);
				}
                $participation_obj=new Ep_Participation_Participation();
                $participation_obj->updateParticipationDetails($data,$partcipate_id);
                /** Updating corrector Participation*/
                $correctorParticipationUpdate['status']='bid';
                $correctorParticipationUpdate['corrector_reparticipation']=$corrector_params['corrector_reparticipation'];
                $corrector_participation->updateParticipationDetails($correctorParticipationUpdate,$corrector_params['cparticipation_id']);
                /**get contributor id to send the email*/
                $contributor_participation=$participation_obj->getParticipateDetails($partcipate_id);
                $contributor_identifier= $contributor_participation[0]['user_id'];
                
				
                if($comments)
                {
                    /**get latest version*/
                    $ArticleProcess=new EP_Article_ArticleProcess();
                    $latest_version=$ArticleProcess->getLatestVersionDetails($partcipate_id) ;
                    /**Insert into Article Process Table**/
                    $ArticleProcess->participate_id=$partcipate_id;
                    $ArticleProcess->user_id=$userIdentifier;
                    $ArticleProcess->article_path=$latest_version[0]['article_path'];
                    $ArticleProcess->version=0;//$latest_version[0]['version']+1;
                    $ArticleProcess->article_doc_content=$latest_version[0]['article_doc_content'];
                    $ArticleProcess->article_words_count=$latest_version[0]['article_words_count'];
                    $ArticleProcess->stage='corrector';
                    $ArticleProcess->status='disapproved';
                    $ArticleProcess->article_name=$latest_version[0]['article_name'];
                    $ArticleProcess->comments=$this->utf8dec(nl2br(strip_tags($comments, '<p><a><br></br>')));
                    $ArticleProcess->marks=$marks;

                    $ArticleProcess->insert();
                }
                //$autoEmails->messageToEPMail($contributor_identifier,47,$parameters);
				if($deliveryDetails[0]['nomoderation']=="yes")
				{
					$parameters['resubmit_hours']=$deliveryDetails[0]['correction_resubmission'];
					
					if($resubmission <= '60')
						$parameters['resubmit_hours']=$resubmission." minutes";
					else
						$parameters['resubmit_hours']= $this->minutesToHours($resubmission)." heures";
					$parameters['comments']=$partParams['Moderator_comment'];
					$parameters['article_title']=$deliveryDetails[0]['articleName'];
					$parameters['articlename_link']="/contrib/mission-deliver?article_id=".$corrector_params['article_id'];
					$parameters['article_link']="http://ep-test.edit-place.com/contrib/ongoing";
					$parameters['correctorcomments']=$comments;
					$autoEmails->messageToEPMail($contributor_identifier,57,$parameters);//    sending mail to contributor
				}
				else
				{
					/**sending mail to AO created User**/
					$ao_created_user= $deliveryDetails[0]['created_user'];
					$parameters_refused['article_title']=$deliveryDetails[0]['articleName'];
					$parameters_refused['AO_title']=$deliveryDetails[0]['deliveryTitle'];
					/* $bo_user_Details=$autoEmails->getUserDetails($ao_created_user);
					$contrib_user_Details=$autoEmails->getUserDetails($contributor_identifier);
					$corrector_user_Details=$autoEmails->getUserDetails($userIdentifier);
					$parameters_refused['bo_user']=$bo_user_Details[0]['username'];
					$parameters_refused['contributor_name']=$contrib_user_Details[0]['username'];
					$parameters_refused['corrector_name']=$corrector_user_Details[0]['username'];*/
					$ticket_obj=new Ep_Ticket_Ticket();
					$parameters_refused['bo_user']= $ticket_obj->getUsername($ao_created_user,true);
					$parameters_refused['contributor_name']=$ticket_obj->getUsername($contributor_identifier,true);
					$parameters_refused['corrector_name']=$ticket_obj->getUsername($userIdentifier,true);

					$autoEmails->messageToEPMail($ao_created_user,64,$parameters_refused,true);
					//$this->_helper->FlashMessenger('Article envoy&eacute;');
				}
                //action sentence
                $action_sentence_id=40;

            }
            /////when permanently dissaporved by corrector//////////
            else if($action_function=='closed')
            {
                $marks=$corrector_params['marksclose'];
                $deliveryDetails = $delivery_obj->getDeliveryDetails($corrector_params['article_id']);
                ///////check the cycle count in participation tabel and increament//////////
                $cycleCount = $participation->getParticipationCycles($corrector_params['article_id']);
                $cycleCountnext = $cycleCount[0]['cycle']+1;

                /////udate status participation table///////
                $accept_refuse_at=date("Y-m-d H:i:s");
                //$data = array("status"=>"closed_temp", "current_stage"=>"corrector", "marks"=>$marks, "corrector_id"=>$userIdentifier,"article_submit_expires"=>0,"updated_at"=>$accept_refuse_at);////////updating
				if($deliveryDetails[0]['nomoderation']=="yes")
				{
					$data = array("status"=>"closed", "current_stage"=>"corrector",
                    "marks"=>$marks, "corrector_id"=>$userIdentifier,"article_submit_expires"=>0,"updated_at"=>$accept_refuse_at,"moderate_closed"=>"yes");
					
					$datap = array("cycle"=>$cycleCountnext);////////updating
                    $queryp = "article_id= '".$corrector_params['article_id']."' and cycle=0";
                    $participation->updateParticipation($datap,$queryp);
					
					$this->WriterParticipationExpire($corrector_params['article_id']);
				}
				else
				{
					$data = array("status"=>"closed_temp", "current_stage"=>"corrector", "marks"=>$marks, "corrector_id"=>$userIdentifier,"article_submit_expires"=>0,"updated_at"=>$accept_refuse_at);
				}
                $query = "id= '".$partcipate_id."'";
                $participation->updateParticipation($data,$query);
                /** Updating corrector Participation*/
                $correctorParticipationUpdate['corrector_reparticipation']=$corrector_params['corrector_reparticipation'];
                $corrector_participation->updateParticipationDetails($correctorParticipationUpdate,$corrector_params['cparticipation_id']);
                /**get contributor id to send the email*/
                $contributor_participation=$participation->getParticipateDetails($partcipate_id);
                $contributor_identifier= $contributor_participation[0]['user_id'];
                
                if($marks)
                {
                    /**get latest version*/
                    $ArticleProcess=new EP_Article_ArticleProcess();
                    $latest_version=$ArticleProcess->getLatestVersionDetails($partcipate_id) ;
                    /**Insert into Article Process Table**/
                    $ArticleProcess->participate_id=$partcipate_id;
                    $ArticleProcess->user_id=$userIdentifier;
                    $ArticleProcess->article_path=$latest_version[0]['article_path'];
                    $ArticleProcess->version=0;//$latest_version[0]['version']+1;
                    $ArticleProcess->article_doc_content=$this->utf8dec($latest_version[0]['article_doc_content']);
                    $ArticleProcess->article_words_count=$latest_version[0]['article_words_count'];
                    $ArticleProcess->stage='corrector';
                    $ArticleProcess->status='closed';
                    $ArticleProcess->article_name=$latest_version[0]['article_name'];
                    $ArticleProcess->comments=$this->utf8dec(nl2br($comments));
                    $ArticleProcess->marks=$marks;
                    $ArticleProcess->reasons_marks=$corrector_params["marksclosevaldwithreason"] ;

                    $ArticleProcess->insert();
                }
                // $autoEmails->messageToEPMail($contributor_identifier,48,$parameters);
                
				if($deliveryDetails[0]['nomoderation']=="yes")
				{
					/** Sending mail to the writer **/
					$parameters['article_sent_date']=date("d/m/Y",strtotime($contributor_participation[0]['updated_at']));
					$parameters['article_title']=$deliveryDetails[0]['articleName'];
					$parameters['articlename_link']="/contrib/mission-deliver?article_id=".$corrector_params['article_id'];
					$parameters['article_link']="http://ep-test.edit-place.com/contrib/ongoing";
					$parameters['correctorcomments']=$comments;
					
					$autoEmails->messageToEPMail($contributor_identifier,60,$parameters);
					
					//Republish mails
					$this->sendMailToContribs($corrector_params['article_id']);
				}
				else
				{
					/**sending mail to AO created User**/
					$ao_created_user= $deliveryDetails[0]['created_user'];
					$parameters_trefused['article_title']=$deliveryDetails[0]['articleName'];
					$parameters_trefused['AO_title']=$deliveryDetails[0]['deliveryTitle'];
					/* $bo_user_Details=$autoEmails->getUserDetails($ao_created_user);
					$contrib_user_Details=$autoEmails->getUserDetails($contributor_identifier);
					$corrector_user_Details=$autoEmails->getUserDetails($userIdentifier);
					$parameters_trefused['bo_user']=$bo_user_Details[0]['username'];
					$parameters_trefused['contributor_name']=$contrib_user_Details[0]['username'];
					$parameters_trefused['corrector_name']=$corrector_user_Details[0]['username'];*/

					$ticket_obj=new Ep_Ticket_Ticket();
					$parameters_trefused['bo_user']= $ticket_obj->getUsername($ao_created_user,true);
					$parameters_trefused['contributor_name']=$ticket_obj->getUsername($contributor_identifier,true);
					$parameters_trefused['corrector_name']=$ticket_obj->getUsername($userIdentifier,true);

					$autoEmails->messageToEPMail($ao_created_user,66,$parameters_trefused,true);
				}
                //action sentence
                $action_sentence_id=38;
            }


            //Inserting Article reasons
            if($action_function=='closed' OR $action_function=='disapprove')
            {
                $reason_obj=new Ep_Article_ArticleReassignReasons();

                if($action_function=='closed')
                {
                    $reasons=implode(",",$corrector_params['close_template']);
                    $type='permanent';
                }
                else if($action_function=='disapprove')
                {
                    $reasons=implode(",",$corrector_params['refuse_template']);
                    $type='temporaire';
                }


                $reason_obj->participate_id=$partcipate_id;
                $reason_obj->refused_by=$userIdentifier;
                $reason_obj->contributor=$contributor_identifier;
                $reason_obj->stage='corrector';
                $reason_obj->reasons=$reasons;
                $reason_obj->edited_content=utf8dec($comments);
                $reason_obj->type=$type;
                $reason_obj->created_at=date("Y-m-d H:i:s");

                $reason_obj->insert();
            }

            //Insert action in history table
            $action_obj=new Ep_Article_ArticleActions();
            $history_obj=new Ep_Article_ArticleHistory();

            $action_sentence= $action_obj->getActionSentence($action_sentence_id);

            $ticket=new Ep_Ticket_Ticket();
            $contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$contributor_identifier.'" target=_blank""><b>'.htmlentities($contrib_user_Details[0]['username']).'</b></a>';
            $corrector_name='<a class="corrector" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$userIdentifier.'" target=_blank""><b>'.htmlentities($this->_view->client_email).'</b></a>';
            $corrector_comment=$comments;


            eval("\$action_sentence= \"$action_sentence\";");


            if($action_sentence)
            {
                $history_array['article_id']=$corrector_params['article_id'];
                $history_array['user_id']=$userIdentifier;
                $history_array['stage']='FO';
                $history_array['action']='Corrector Validation';
                $history_array['action_at']=date("Y-m-d H:i:s");
                $history_array['action_sentence']=$action_sentence;

                $history_obj->insertHistory($history_array);
            }


            //sending notify emails to bo user whencorrector corrects the article
            $corrector_notify=$deliveryDetails[0]['corrector_notify'];
            if($corrector_notify=='yes')
            {
                $autoEmails=new Ep_Ticket_AutoEmails();
                $ticket_obj= new Ep_Ticket_Ticket();

                $bo_user=$deliveryDetails[0]['created_user'];


                $mail_params['AO_title']=$deliveryDetails[0]['deliveryTitle'];
                $mail_params['article_title']=$deliveryDetails[0]['articleName'];
                $mail_params['corrector_name']=$this->_view->client_email;
                $mail_params['bo_user']=$ticket_obj->getUserName($bo_user,TRUE);
                $mail_params['comment_bo_link']='/ongoing/ao-details?client_id='.$deliveryDetails[0]['user_id'].'&ao_id='.$deliveryDetails[0]['id'].'&submenuId=ML2-SL4';

                $autoEmails->messageToEPMail($bo_user,102,$mail_params,TRUE);
            }

            $this->_redirect("/contrib/ongoing");
        }
    }
	function replaceTokensInStencils($tokens,$stencilsText)
	{
		$ebookerObj=new Ep_Article_Ebooker();
		$whiteListKeywords=$ebookerObj->getWhiteListKeywords();
		if($whiteListKeywords)
		{
			foreach($whiteListKeywords as $i=>$keyword)
			{
				$whiteListKeywords[$i]='/\b'.($keyword).'\b/i';
			}
		}
		//echo "<pre>";print_r($whiteListKeywords);exit;		
		
		foreach($tokens as $i=>$token)
		{
			$tokens[$i]='/\b'.urlencode(utf8_encode($token)).'\b/';
		}	
		$stencil_text=preg_replace('/[\?\,\.\!\/\\\(\)]/',' ',$stencilsText);		
		$stencil_text=urlencode($stencil_text);
		$stencil_text=preg_replace($tokens, " ",$stencil_text );
		$stencil_text=preg_replace($whiteListKeywords, " ",$stencil_text);		
		$stencil_text=urldecode($stencil_text);
		$stencil_text=preg_replace('/\s+/', ' ', $stencil_text);
		return $stencil_text;
	}
	//check plagarism for stencils
	public function checkStencilPlagarismAction()
	{
		ini_set('max_execution_time', 1200);
		
		$stencilParams=$this->_request->getParams();
        $article_id = $stencilParams['article_id'];
        for($i=0;$i<count($stencilParams['bnp_text']);$i++){
            $bnp_text = $stencilParams['bnp_text'][$i];
//            $bnp_text = (str_replace(array('&#39;','&nbsp;'),array("'",' '),html_entity_decode($bnp_text,ENT_COMPAT,"UTF-8")));//converting into htmlentity to normal text ->replacing single quotes -> utf8_decoding before saving into DB
             $stencilParams['stencil_text'][$i] = (preg_replace('/\s+/', ' ',(strip_tags(str_replace(array('&#39;','&nbsp;'),array("'",' '),html_entity_decode($bnp_text,ENT_COMPAT,"UTF-8")))) ));//str_replace(array(')','(',"\\",'/',"\n","\t"),'',$bnp_text);
//             $stencilParams['stencil_text'][$i] = utf8_decode(preg_replace('/\s+/', ' ',(strip_tags(str_replace(array('&#39;','&nbsp;'),array("'",' '),$bnp_text))) ));//str_replace(array(')','(',"\\",'/',"\n","\t"),'',$bnp_text);
        }
//        print_r($stencilParams['stencil_text']);
        $resultArray=array();
		
//		$ebookerObj=new Ep_Article_Ebooker();
		$bnp_obj=new Ep_Bnp_Bnp();
//		$article_id=$stencilParams['article_id'];
//		$articleTokens=$ebookerObj->getArticleTokens($article_id);
		//echo "<Pre>";print_r($articleTokens);exit;
		
//		if($articleTokens)
//		{
//			$stencilsText=$this->replaceTokensInStencils($articleTokens,implode("|",$stencilParams['stencil_text']));
//			$stencilsArray=explode("|",$stencilsText);
//
//		}
//		else
        {
			$stencilsText=implode("|",$stencilParams['stencil_text']);
			$stencilsArray=$stencilParams['stencil_text'];
		}
		//echo $stencilsText;exit;
		//echo "<pre>";print_r(($stencilParams));exit;
		
		
		
		$plag_result_array=array();
		
		if(is_array($stencilsArray) && count($stencilsArray)>0)
		{
			$stencilsCount=count($stencilsArray);
			$plagarism=new Plagarism();
			/* Set options */
			$plagarism->options=array(
						'chunk_size'=>5,
						'highlight'=>true,
						'replacements'=>array('<span class="duplicatedText">','</span>')
						);
						
			for($i=0;$i<$stencilsCount;$i++)
			{
				for($j=0;$j<$stencilsCount;$j++)
				{
					if($j==$i)continue;
					$plag_result_array[$i+1][$j+1]=$plagarism->str_match($stencilsArray[$j],$stencilsArray[$i]);
				}
			}
            //echo "<pre>";print_r($plag_result_array);exit;
			$plagTextResultArray=$this->checkDuplicateTextPercentage($plag_result_array);
			//echo "<pre>";print_r($plagTextResultArray);exit;
			
			/*skipping DB plagairism for given deliveries*/
//			$skip_plag_deliveries=array('145028891397035','145028915966615','145028922873894','145037605291484','145037665586112','145037727994380');
			$skip_plag_deliveries=array();

			$articleDetails=$bnp_obj->getArticleDetails($article_id);
			$delivery_skip_id=$articleDetails[0]['delivery_id'];		
			
			//echo "<pre>";print_r($plagTextResultArray);print_r($articleDetails);exit;
            //$finalPlagResultArray=$plagTextResultArray[1];echo json_encode($finalPlagResultArray);exit;
            if($plagTextResultArray[0]==1 || ($plagTextResultArray[0]!=1 && in_array($delivery_skip_id,$skip_plag_deliveries))) //check if stencils plagarised with in stencils
			{
				$finalPlagResultArray=$plagTextResultArray[1];		
								
			}
			else
			{ 
				$articleDetails=$bnp_obj->getArticleDetails($article_id);
				if($articleDetails)
				{
                    $bnp_sample_text_ids = $articleDetails[0]['bnp_sampletext_id'];
                    $bnp_sample_text_ids = explode('|',$bnp_sample_text_ids);
                    $bnp_sample_text_ids = implode(',',$bnp_sample_text_ids);
					$language=$articleDetails[0]['language'];
				}	

				//check Database plagarism
				$dbPlagResultArray=$this->chekcDBStencilsTextPercentage($article_id,"",$stencilsArray,$bnp_sample_text_ids,$language,$bnp_sample_text_ids);
				//echo "<pre>";print_r($dbPlagResultArray);exit;
				if($dbPlagResultArray[0]==1 || $dbPlagResultArray[0]==0) //check if stencils plagarised in DB
				{
					$finalPlagResultArray=$dbPlagResultArray[1];
				}

			}
			echo json_encode($finalPlagResultArray);exit;
		}
	}
	//get duplicates array with percentage
	function checkDuplicateTextPercentage($plag_result_array){
		$contentCount= count($plag_result_array);
		for($k=1;$k<=$contentCount;$k++)
		{
			for($l=1;$l<=$contentCount;$l++)
			{
				if($k==$l)continue;
				
				$current=$k;
				$next=$l;
				
				$currentPercentage=$plag_result_array[$current][$next][0];
				$nextPercentage=$plag_result_array[$next][$current][0];
				
				//echo $current."--".$next;
				
				if(($currentPercentage >= 85 || $nextPercentage >=85) && ($currentPercentage >= $nextPercentage || $nextPercentage >= $currentPercentage ))
				{
					if($currentPercentage >= $nextPercentage)
					{
						if(count($maxPerArray[$next][$current])==0)
						{							
							$maxPerArray[$next][$current]['percentage']=$currentPercentage;
							$maxPerArray[$next][$current]['text']=$plag_result_array[$current][$next][1];
							//$maxPerArray[$next][$current]['text']=$plag_result_array[$next][$current][1]; //for old  plagiarism
						}	
					}
					else if($nextPercentage >= $currentPercentage)
					{
						if(count($maxPerArray[$current][$next])==0)
						{
							$maxPerArray[$current][$next]['percentage']=$nextPercentage;
							$maxPerArray[$current][$next]['text']=$plag_result_array[$next][$current][1];
							//$maxPerArray[$current][$next]['text']=$plag_result_array[$current][$next][1]; //for old  plagiarism
						}	
					}
				}
				else{
					$maxPerArray[$current][$next]['percentage']=max($currentPercentage,$nextPercentage);
					$maxPerArray[$current][$next]['text']='';
				}

				if(($currentPercentage==$nextPercentage)&& ($k<$l))
				{										
					$maxPerArray[$current][$next]['percentage']=0;
				}				
			}	
		}
		//echo "<pre>";print_r($maxPerArray);exit;
		
		$finalArray=array();
		$plagarisedFlag=FALSE;	
		//check max percentage with each string
		foreach($maxPerArray as $version => $matchedArray)
		{
			$finalArray[$version]=$this->checkMaxPercentageStencil($matchedArray);
			if($finalArray[$version]['plagarised']=='yes')
				$plagarisedFlag=TRUE;
		}
		//echo "<pre>";print_r($finalArray);
		
		return array($plagarisedFlag,$finalArray);	
		
		
		
		 
	}
	//max of multidimensional array with key
	function checkMaxPercentageStencil($matchedArray)
	{
		$max=0;
		$version=0;
		$maxstencil=NULL;
		foreach($matchedArray as $k=>$v){
			if($v['percentage']>$max){
				$max=$v['percentage'];
				$maxstencil=$v;
				$version=$k;
			}
		}
		$maxstencil['version']=$version;
		//echo $max;
		if($max >= 85){$maxstencil['plagarised']='yes';}
		else{$maxstencil['plagarised']='no';}
		return $maxstencil;
	}
	
	
	function chekcDBStencilsTextPercentage($article_id,$tokens=NULL,$stencilsArray,$bnp_sampletext_id,$language=NULL)
	{
		$bnp_obj=new Ep_Bnp_Bnp();
		/*getting all DB stencils except given articles*/
		
		//$DBStencils=$bnp_obj->getAllDBStencils($article_id,'',$language);
		if(!$DBStencils)
			$DBStencils=array();		
		//echo "<pre>";print_r($DBStencils);exit;
		
		/*getting all sample texts*/
		$sampleTexts=$bnp_obj->getAllSampleText($bnp_sampletext_id);//($bnp_sampletext_id)
		//print_r($sampleTexts);exit;
		if(!$sampleTexts)
			$sampleTexts=array();
		
		$allDBStencils=array_merge($sampleTexts,$DBStencils);
		$allDBStencils=array_unique($allDBStencils);
//        echo "<pre>";print_r($stencilsArray);echo "</pre>";
//		echo "<pre>";print_r($allDBStencils);exit;
		
			
		$stencilsCount=count($stencilsArray);
		$dbStencilCount=count($allDBStencils);		
		$plagarisedFlag=0;
			
		
		if($allDBStencils)
		{	
			$plagarism=new plagarism();
			/* Set options */
			$plagarism->options=array(
						'chunk_size'=>5,
						'highlight'=>true,
						'replacements'=>array('<span class="duplicatedText">','</span>')
						);
						
			for($i=0;$i<$stencilsCount;$i++)
			{
				$plag_result_array[$i+1]['percentage']=0;
				$plag_result_array[$i+1]['text']='';
				$plag_result_array[$i+1]['plagarised']='no';
				$plag_result_array[$i+1]['version']='db';
				
				$percentage=0;
				for($j=0;$j<$dbStencilCount;$j++)
				{					
//					$allDBStencils[$j]=$this->replaceTokensInStencils($tokens,$allDBStencils[$j]);
					//echo $stencilsArray[$i]."--".$allDBStencils[$j]."<br>";
					$plagDetails=$plagarism->str_match($stencilsArray[$i],$allDBStencils[$j]);
					$percentage=$plagDetails[0];
					if($percentage>=85){
						$plag_result_array[$i+1]['percentage']=$plagDetails[0];
						$plag_result_array[$i+1]['text']=$plagDetails[1];
						$plag_result_array[$i+1]['plagarised']='yes';
						$plag_result_array[$i+1]['version']='db';	

						$plagarisedFlag=TRUE;						
						break;
					}
				}
			}
		}
		else{
			for($i=0;$i<$stencilsCount;$i++)
			{
				$plag_result_array[$i+1]['percentage']=0;
				$plag_result_array[$i+1]['text']='';
				$plag_result_array[$i+1]['plagarised']='no';
				$plag_result_array[$i+1]['version']='db';
			}	
		}
		//echo "<pre>";print_r($plag_result_array);exit;
		return array($plagarisedFlag,$plag_result_array);
		
		
	}
	
	/**function to connect to the linode server, uploading the csv and processing the csv file**/	
	public function uploadAndProcess($srcFile,$u_file_name,$article_id)
    {
        $this->getSFTPobjectAction() ;

        //Path to execute ruby command
        $file_exec_path=$this->sftp->exec("./test_ebookers_plag_exec.sh"); //ruby execution path

        /**getting upload path from alias**/
        $file_upload_path=$this->sftp->exec("./test_ebookers_plag_upload.sh");


        /**getting download path from alias**/
        $file_download_path=$this->sftp->exec("./test_ebookers_plag_download.sh");

        /**sending uploaded file to the server**/
        $this->sftp->chdir(trim($file_upload_path));

        $this->sftp->put($u_file_name,$srcFile,NET_SFTP_LOCAL_FILE);

        /**processing the file**/

        /**passing file name**/
        $src=pathinfo($u_file_name);
        $download_fname=$src['filename'];
        $dstfile=$download_fname.".".$src['extension'];
        $dstfile_xml=$download_fname.".xml";
        $ext = $src['extension'];
        
        /**processing File based on Options**/
        
        $ruby_file="ebookers_plag.rb ";
        $user_id = $this->contrib_identifier;
        $user_name = $this->_view->client_email;
        
        $cmd="bundle exec ruby -W0 $ruby_file '$user_id' '$user_name' '$ext' '$u_file_name' '$dstfile_xml' 2>&1";        
        //////////////////
        $this->sftp->setTimeout(3000);
        
		//echo $ssh->exec("whoami; source ~/.rvm/scripts/rvm; rvm use 1.9.3-head; which ruby");
        //exit(0);		
        $file_exec_path=trim($file_exec_path);
        //$ruby_switch_prefix = "source ~/.rvm/scripts/rvm; rvm use 1.9.3-head ";
		$ruby_switch_prefix = "source ~/.rvm/scripts/rvm;rvm use 2.2.2-head >> /dev/null";
        
		///writing cmd to file for reference///
        $cmdfilename = $this->articles_path.$article_id."/plagairism/cmdfile.txt";
        $cmdfile = fopen($cmdfilename, "w") or die("Unable to open file!");
        $txt = "$ruby_switch_prefix ;cd $file_exec_path;$cmd ;"." at ".date('Y-m-d H:i:s')."\n";
        fwrite($cmdfile, $txt);
        fclose($cmdfile);		
		
		
		
		//echo "$ruby_switch_prefix ;cd $file_exec_path;$cmd ;";
		$output= $this->sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");		
		//$output= $this->sftp->exec("cd $file_exec_path;$cmd ;");

        /**Downloading the Processed File**/

        /**processed file path**/
        $remoteFile=trim($file_download_path)."/".$dstfile_xml;

        $this->sftp->chdir(trim($file_download_path));
        $file_path=pathinfo($remoteFile);
        $xmlfilefolder = pathinfo($srcFile, PATHINFO_FILENAME);
        // echo  "<br>".$localFile=APP_PATH_ROOT."plagarism/".$xmlfilefolder."/".$filename;
        
		$localFile=$this->articles_path.$article_id."/plagairism/".$dstfile_xml;
        $serverfile=$file_path;
        $fname=$file_path['filename'];
        $ext=$file_path['extension'];
        
        if(strlen(strip_tags(nl2br(trim($output)))) == strlen('Using /home/oboulo/.rvm/gems/ruby-1.9.3-head File Size is more Than 800kb'))
        {
            if($cron)   return 0 ;  else    return 'File Size is more Than 800kb';
        }
        else {
            //downloading the file from remote server
            $this->sftp->get($dstfile_xml,$localFile);
            if(file_exists($localFile))
                return  $localFile;
            else
                return  $output;
        }
    }
	public function XMLParser($file,$stencilsCount)
    {
        $xmlstring = file_get_contents($file);        
        $j=0;
		
		$xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
		$json = json_encode($xml);
		$XMLarray = json_decode($json,TRUE);		
		//echo "<pre>";print_r($XMLarray);
		//echo count($XMLarray['article']['results']);
		if($stencilsCount==1)
			$loop_xml=$XMLarray['article']['results'];
		else
			$loop_xml=$XMLarray['article']['results']['result'];
		
		foreach($loop_xml as $index=>$result)
		{
			$pIndex=$index+1;			
			
			//echo $index."--".print_r($result);exit;
			
			$plag_data[$pIndex]['percentage']=round($result['percentage']['p'],2);
			$plag_data[$pIndex]['text']=$result['content']['p'];
			$plag_data[$pIndex]['url']=$result['url']['p'];
			$plag_data[$pIndex]['version']='web';
			
			if($plag_data[$pIndex]['percentage'] >= 85)
				$plag_data[$pIndex]['plagarised']='yes';
			else
				$plag_data[$pIndex]['plagarised']='no';
			
		}
        return $plag_data;
        exit;
    }
}