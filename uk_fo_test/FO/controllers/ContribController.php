<?php
class ContribController extends Ep_Controller_Action
{
  public function init()
  {
            parent::init();
            $this->_view->livesite = $this->_config->www->baseurl;
            $this->_view->livesite_ssl = $this->_config->www->baseurlssl;
            $this->_view->lang = $this->_lang;
            $this->attachment_path=APP_PATH_ROOT.$this->_config->path->attachments;
            $this->articles_path=APP_PATH_ROOT.$this->_config->path->articles;
        
            $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
            $profileplus_obj = new Ep_Contrib_ProfilePlus();
            $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
            $this->contrib_identifier=$contrib_identifier;
       $this->_view->contrib_identifier=$this->contrib_identifier;
             if(($profile=$profileplus_obj->checkProfileExist($contrib_identifier))!='NO' && $contrib_identifier!='' )
             {
                if($profile[0]['first_name'])
                   $this->_view->client_email=ucfirst($profile[0]['first_name'])." ".($profile[0]['last_name']);
				else
					$this->_view->client_email=$this->EP_Contrib_reg->clientemail;
                   $this->EP_Contrib_reg->user_language=$profile[0]['language'];
            /*added by naseer on 04.12.2015*/
            $this->translator = $profile[0]['translator'];
            $this->translator_type = $profile[0]['translator_type'];
            $this->_view->translator = $profile[0]['translator'];
            $this->_view->translator_type = $profile[0]['translator_type'];
            /* ednd of added by naseer on 04.12.2015*/
        }
        else if($this->EP_Contrib_reg->clientemail!='')
            $this->_view->client_email=strtolower($this->EP_Contrib_reg->clientemail);

        /**Unread message count in inbox**/
        $ticket=new Ep_Ticket_Ticket();
        $this->_view->unreadCount=$ticket->getUnreadCount('contributor',$contrib_identifier);

        /**cache for statistics && config**/
        $this->EP_Cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        //Loading Configuration Settings see if a cache already exists:
        if( ($configurations = $this->EP_Cache->load('configurations')) === false ) {
            $config_obj=new Ep_User_Configuration();
            $configurations=$config_obj->getAllConfigurations();
            $this->EP_Cache->save($configurations, 'configurations');
        }
        $this->config=$configurations;
        //statistic see if a cache already exists:
        if( ($statistics = $this->EP_Cache->load('statistics')) === false ) {
            $stats_obj=new Ep_Stats_Stats();
            $statistics=$stats_obj->getAllStatistics($configurations);
            $this->EP_Cache->save($statistics, 'statistics');
        }
        $this->_view->stats=$statistics;
        /**getting Profile whether senior / junior**/
        $user_obj = new EP_Contrib_Registration();
        $profile_contribinfo=$user_obj->getUserInfo($contrib_identifier);
        if(!$profile_contribinfo[0]['profile_type'])
            $profile_contribinfo[0]['profile_type']='sub-junior';
        $this->profileType=$profile_contribinfo[0]['profile_type'];
        $this->corrector=$profile_contribinfo[0]['type2'];
        $this->corrector_type=$profile_contribinfo[0]['profile_type2'];
        $this->black_status=$profile_contribinfo[0]['blackstatus'];
        $this->_view->profileType=$this->profileType;
        $this->_view->corrector=$this->corrector;
        /**Selected AO in CART*/
        $this->EP_Contrib_Cart = Zend_Registry::get('EP_Contrib_Cart');
        $selected_ao_count=(count($this->EP_Contrib_Cart->cart)+count($this->EP_Contrib_Cart->poll)+count($this->EP_Contrib_Cart->correction));
        $this->selected_ao_count=$selected_ao_count;
        $this->_view->selected_ao_count=$this->selected_ao_count;

      //private and quiz icon
      $this->private_icon='<img data-original-title="$toolTitle" rel="tooltip" data-placement="right" src="/FO/images/private-icon.gif">';
      $this->ptoolTitle='This mission is exclusively yours';
      $this->ptoolTitleMulti='This mission includes X writers';
      $this->quiz_icon='<img data-original-title="Quiz" rel="tooltip" data-placement="right" src="/FO/images/quiz-icon.png">';
     
           /**Quiz Session*/
            $this->EP_Contrib_Quiz = Zend_Registry::get('EP_Contrib_Quiz');

            $quiz_obj=new Ep_Quiz_Participation();
            $this->qualifiedQuiz=$quiz_obj->getAllQuizPassed($this->contrib_identifier);                        
      //echo system('date +%Z');
      //echo $_COOKIE['local_timezone'];
        $this->producttype_array=array(
    							"article_de_blog"=>"Blog article",
								"descriptif_produit"=>"Product description",
								"article_seo"=>"SEO article",
								"guide"=>"Guide",
								"news"=>"News",
								"autre"=>"Others"
        						);
		$this->duration_array=array(
							"days"=>"Days",
							"week"=>"Week",
							"month"=>"Month",
							"year"=>"Year"
						);
  }
  
    /**Registration Action*/
  public function indexAction()
  {
     
    if($_GET['target'])
      $_SESSION['target']=$_GET['target'];
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
    if($this->EP_Contrib_reg->clientidentifier!="")
    {
      $this->_redirect("/contrib/home");
      exit;
    }
    else
    {
      $this->_redirect("/index");
      exit;
    }
  }
    /**Log out Action**/
    public function logoutAction()
  {
        Zend_Session::destroy('EP_Contrib_reg');
        Zend_Session::destroy('EP_Contrib_Cart');
        Zend_Session::destroy('EP_Contrib_Quiz');
     header("location:/index");
  }
    /**Home action **/
    public function homeAction() 
    {
        if($this->_helper->EpCustom->checksession())
        {
             //redirecting to the URL accessed
             if(isset($_SESSION['target']))
             {
                 $prevurl= $_SESSION['target'];
                 unset($_SESSION['target']);
                 $this->_redirect($prevurl);
             }
       
            //Getting categories  from XML array
        $categories_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        natsort($categories_array);
       
        $this->_view->meta_title="Author-Home";
      /**ENd*/
     
           /**profile IMage*/
          $this->_view->contrib_home_picture=$this->getPicPath($this->EP_Contrib_reg->clientidentifier);       
            /**Total Royalties**/
            $royalty_obj=new Ep_Royalty_Royalties();
            $totalRoyalty=$royalty_obj->getTotalRoyalty($this->EP_Contrib_reg->clientidentifier);
            $this->_view->userRoyalty=$totalRoyalty;
            
            /**get Recent Ao Offers**/
            $this->_view->recent_AO_Offers=$this->recentAoOffers();
              //print_r($recent_AO_Offers);
            /**Contributor Ongoing Articles**/
            $participation=new Ep_Participation_Participation();
            $participation_ongoing=$participation->ongoingArticles($this->EP_Contrib_reg->clientidentifier);
            $participation_corrector=new Ep_Participation_CorrectorParticipation();
            $ongoingCorrectorArticles=$participation_corrector->ongoingArticles($this->EP_Contrib_reg->clientidentifier);
            $participation_Count=count($participation_ongoing)+count($ongoingCorrectorArticles);
            if($participation_Count>0)
            {
                $latestParticipation=$participation_ongoing[0]['title'];
            }   
            $this->_view->participation_Count=$participation_Count;
            $this->_view->latestParticipation=$latestParticipation;           

            /**profile completion percentage*/
            $this->_view->profile_percentage=$this->calculateProfilePercentage();

            
            //upcoming count for next day
            $articleParams['cnt_nextday']=true; 
			 
            $upcoming_count=count($this->upcomingDeliveries($articleParams));

			setlocale(LC_TIME, "en_US");
            $this->_view->publish_date=strftime("%d %B",getDateNextDays(1));
            $this->_view->upcoming_count=$upcoming_count;
			 
            $this->render("Contrib_home");
        }    
    }
  
  /**get Recent Ao Offers**/
  public function recentAoOffers()
  {
    /**Toget Recently Added Ao Offers**/
       $contrib=new EP_Contrib_Registration();
             $fav_category=$contrib->getFavouriteCategory($this->EP_Contrib_reg->clientidentifier);
        /** Original Results Article Array**/
        $article_obj=new Ep_Article_Article();
        $params['profile_type']=$this->profileType;
        $params['black_status']=$this->black_status;
        $article_details=$article_obj->getArticleDetails($params);
        $cnt=0;
        if(count($article_details)>0)
        {
          foreach($article_details as $user_article)
          {
            $articles[$cnt]=$user_article['articleid'];
            $cnt++;
          }
        }
        //echo "<pre>";print_r($recruitmentDetails);exit;         
        /**ENDED*/
              $delivery=new Ep_Article_Delivery();  
              $searchParameters['profile_type']=$this->profileType;
              $searchParameters['black_status']=$this->black_status;
              $recent_AO_Offers=array();
              $recent_AO_Offers=$delivery->getRecentAoOffers($fav_category,5,$searchParameters);              
              $ao=0;
        $i = 0;
        $recruitment=0;
        if(count($recent_AO_Offers)>0)
        {
            foreach($recent_AO_Offers as $AO_Offer)
            {
                $flag = true;//default value at begining
                //checking if source laug is compulsury to check, if source laung is compulsary to check then unserialize launge_more and check if the laung fluence is above 50%//
                if($AO_Offer['sourcelang_nocheck'] != 'yes' && $AO_Offer['product'] === 'translation') {
                    $language_more = unserialize($AO_Offer['language_more']);
                    $flag = fasle;
                    foreach($language_more as $key => $value){
                        if($key === $AO_Offer['language_source'] && (int)$value >= 50){
                            $flag = true;//false true if the source launge is in lang more of contributor
                        }
                    }
                }
                if($flag === true) {
                $recent_AO_Offers[$ao]['client_pic']=$this->getClientPicPath($AO_Offer['identifier']);
               if($AO_Offer['participation_expires'])
                  $recent_AO_Offers[$ao]['timestamp']= $AO_Offer['participation_expires'];
               //else
              //    $recent_AO_Offers[$ao]['timestamp']= strtotime(date('Y-m-d H:i:s',strtotime($AO_Offer['delivery_date']." 23:59:59")));
                if(count($articles)>0)
                {
                    if(!in_array($AO_Offer['articleid'],$articles))
                          $recent_AO_Offers[$ao]['display']='no';
                    else
                          $recent_AO_Offers[$ao]['display']='yes';
                }
                else
                    $recent_AO_Offers[$ao]['display']='no';    

                 //private icon
                 if($recent_AO_Offers[$ao]['AOtype']=='private')
                 {
                    $private_icon=$this->private_icon;
                    $writers_count=count(explode(",",$recent_AO_Offers[$ao]['contribs_list']));
                    if($writers_count>1)
                      $toolTitle=$this->ptoolTitleMulti;
                    else
                      $toolTitle=$this->ptoolTitle;

                      $toolTitle=str_replace('X',$writers_count,$toolTitle);
                      $private_icon=str_replace('$toolTitle',$toolTitle,$private_icon);
                                  
                    $recent_AO_Offers[$ao]['picon']=$private_icon;
                    
                  }  
                 else
                  $recent_AO_Offers[$ao]['picon']=''; 



                //quiz icon
                if($recent_AO_Offers[$ao]['link_quiz']=='yes' && $recent_AO_Offers[$ao]['quiz'] && !in_array($recent_AO_Offers[$ao]['quiz'],$this->qualifiedQuiz))
                  $recent_AO_Offers[$ao]['qicon']=$this->quiz_icon;
                 else
                  $recent_AO_Offers[$ao]['qicon']=''; 

                $recent_AO_Offers[$ao]['product_type_name']= $this->producttype_array[$AO_Offer['type']];
                $recent_AO_Offers[$ao]['language_name']= $this->getLanguageName($AO_Offer['language']);
                    $recent_AO_offers_temp[$i++] = $recent_AO_Offers[$ao];// initailizing recent offerts to temp variable//
                }//if($lang_present)
                $ao++;
               }
            $recent_AO_Offers = $recent_AO_offers_temp;//inititailze temp array to original array after filtering the array//
            if(count($recent_AO_Offers)>0)
            {
              /**sorting array for displaying trod articles in last**/
                foreach ($recent_AO_Offers as $key => $row) {
                    $articles_array[$key]  = $row['display'];
                    // of course, replace 0 with whatever is the date field's index
                }
                 array_multisort($articles_array, SORT_DESC, $recent_AO_Offers);
                 //echo "<pre>";print_r($recent_AO_Offers);
                 $recent_AO_Offers=array_chunk($recent_AO_Offers,5,true);
        }
        }
        //Added w.r.t recruitment
        $recruitment_obj=new Ep_Recruitment_Participation();
        //$recruitmentDetails=$recruitment_obj->getRecentRecruitmentOffers(5,$searchParameters);
        $recruitmentCount=count($recruitmentDetails);
        if($recruitmentCount==0)
            $recruitmentDetails=array();
        foreach($recruitmentDetails as $r=>$recruitment)
        {
            if($recruitment['participation_expires'])
                $recruitmentDetails[$r]['timestamp']= $recruitment['participation_expires'];
                $recruitmentDetails[$r]['product_type_name']= $this->producttype_array[$recruitment['type']];
                $recruitmentDetails[$r]['language_name']= $this->getLanguageName($recruitment['language']);
        }    
        //echo "<pre>";print_r($recruitmentDetails);exit;
        /**poll AOs**/
        
        $pollDetails=$this->pollAoSearch(NULL,5);
        $pollCount=count($pollDetails);
        if($pollCount==0)
            $pollDetails=array();

        /**correction AO's**/
        $correctionAoDetails=$this->correctionAoSearch($searchParameters);  
        
        $correctionCount=count($correctionAoDetails);  
         if($correctionCount==0)
            $correctionAoDetails=array();
         
		//Private icons
		$co=0;
        $j = 0;
        foreach($correctionAoDetails as $corr_offer){
            $flag1 = true;//default value at begining
            //checking if source laug is compulsury to check, if source laung is compulsary to check then unserialize launge_more and check if the laung fluence is above 50%//
            if($corr_offer['sourcelang_nocheck_correction'] != 'yes' && $corr_offer['product'] === 'translation') {
                $language_more1 = unserialize($corr_offer['language_more']);
                $flag1 = fasle;
                foreach($language_more1 as $key => $value){
                    if($key === $corr_offer['language_source'] && (int)$value >= 50){

                        $flag1 = true;//false true if the source launge is in lang more of contributor
                    }
                }
            }
            if($flag1 === true) {
                if ($correctionAoDetails[$co]['correction_type'] == 'private') {
                    $private_icon = $this->private_icon;
                    $writers_count = count(explode(",", $correctionAoDetails[$co]['corrector_privatelist']));
                    if ($writers_count > 1)
                        $toolTitle = $this->ptoolTitleMulti;
                    else
                        $toolTitle = $this->ptoolTitle;

                    $toolTitle = str_replace('X', $writers_count, $toolTitle);
                    $private_icon = str_replace('$toolTitle', $toolTitle, $private_icon);

                    $correctionAoDetails[$co]['picon'] = $private_icon;

                } else
                    $correctionAoDetails[$co]['picon'] = '';
                $correctionAoDetails_temp[$j++] = $correctionAoDetails[$co];// initailizing recent offerts to temp variable filtering manually//
            }
		$co++;	  
        }//foreach($correctionAoDetails as $corr_offer)
        if($j > 0)
            $correctionAoDetails = $correctionAoDetails_temp;//inititailze temp array to original array after filtering the array//
        else
            $correctionAoDetails = array();
        if(!$recent_AO_Offers[0])
          $recent_AO_Offers[0]=array();

        $recentAds=array_merge($recent_AO_Offers[0],$pollDetails,$correctionAoDetails,$recruitmentDetails);
       
        if(count($recentAds)>0)
        {
          usort($recentAds,'sortByTimestamp');
          $recent_AO_Offers=array_chunk($recentAds,5,true);
        }  
          
          
          //echo "<pre>";print_r($recentAds);
          return $recent_AO_Offers[0];
          
        
  
  }
  //get all Poll AO details
  public function pollAoSearch($poll_search_params=NULL,$limit=NULL)
  {
      $poll_obj=new Ep_Poll_Participation();
      $poll_search_params['profile_type']=$this->profileType;
      $pollDetails=$poll_obj->getAllPollAODetails($poll_search_params,$limit);
      $pollCount=count($pollDetails);
       if($pollCount>0)
        {
              $pollDetails=$this->formatPollDetials($pollDetails);
        } 
        return $pollDetails;
  }
  //get all Correction AO details
    public function correctionAoSearch($correction_search_params=NULL,$limit=NULL,$upcoming=NULL)
  {
      $article_obj=new Ep_Article_Article();
      $participants_obj=new Ep_Participation_CorrectorParticipation();
      $correction_search_params['profile_type']=$this->profileType;
      $correction_search_params['corrector']=$this->corrector;
      $correction_search_params['corrector_type']=$this->corrector_type;
        if($upcoming)
            $correction_search_params['upcoming']=true;
      $correctorDetails=$article_obj->getCorrectorAOs($correction_search_params);
      $correctionCount=count($correctorDetails);
       if($correctionCount>0)
        {
              $correctorDetails=$this->formatCorrectionDetials($correctorDetails);
        } 
        return $correctorDetails;
  }
  /*Function to get the picture of a contributor**/
    public function getPicPath($identifer,$action='home')
    {
        $app_path=APP_PATH_ROOT;
        if($action=='bo_user')
          $profiledir=$this->_config->path->bo_profile_pic_path.$identifer.'/';
        else  
          $profiledir=$this->_config->path->contrib_profile_pic_path.$identifer.'/';


        if($action=='home')
            $pic=$identifer."_h.jpg";
        else if($action=='bo_user') 
            $pic="logo.jpg";
        else
            $pic=$identifer."_p.jpg";
        if(file_exists($app_path.$profiledir.$pic))
        {
            $pic_path="/FO/".$profiledir.$pic;
        }
        else
        {
            if($action=='home' OR $action=='bo_user')
              $pic_path="/FO/images/editor-noimage_60x60.png";
            else
              $pic_path="/FO/images/editor-noimage.png";
        }
        return $pic_path;
    }
    /*Function to get the picture of a client**/
    public function getClientPicPath($identifer,$action='home')
    {
        $app_path=APP_PATH_ROOT;
        $profiledir=$this->_config->path->client_profile_pic_path.$identifer.'/';
        if($action=='home')
            $pic=$identifer."_global.png";
        else if($action=='profile')
            $pic=$identifer."_p.jpg";
        else
            $pic=$identifer."_ao.jpg";
        if(file_exists($app_path.$profiledir.$pic))
        {
            $pic_path="/FO/".$profiledir.$pic;
        }
        else
        {
           if($action=='home')
            $pic_path="/FO/images/customer-no-logo.png";
           else
             $pic_path="/FO/images/customer-no-logo90.png";
        }
        return $pic_path;
    }
    
     /**Contributor's Ongoing Articles*/
    public function ongoingAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
                  
             $ContributorIdentifier=$this->contrib_identifier;
            $participation=new Ep_Participation_Participation();
            $ongoingArticles=$participation->ongoingArticles($ContributorIdentifier);			
            if($this->_helper->FlashMessenger->getMessages()) {
                $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }
            //echo "<prE>";print_r($ongoingArticles);exit;

            $encoursArticles=array();
            $encoursCorrectorArticles=array();
            $awaitingArticles=array();
            $awaitingCorrectorArticles=array();
            $awaitingPolls=array();				
            if(count($ongoingArticles)>0)
            {
                  $cnt=0;
                  $en_cnt=0;
                   $await_cnt=0;
                foreach($ongoingArticles as $Article)
                {        
					
                  /**encours Articles**/
                    if((($Article['status']=='bid' || $Article['status']=='under_study' || $Article['status']=='disapproved' || $Article['status']=='disapprove_client') && $Article['article_submit_expires']>0) OR ($Article['status']=='disapproved_temp' || $Article['status']=='closed_temp' || $Article['status']=='closed' || $Article['status']=='closed_client' || $Article['status']=='closed_client_temp' || $Article['status']=='plag_exec' || $Article['status']=='time_out') OR ( $Article['missiontest']=='yes'))
                  {
                    $encoursArticles[$en_cnt]=$ongoingArticles[$cnt];
                    if($Article['status']=='on_hold' || $Article['status']=='disapproved_temp' || $Article['status']=='closed_temp' || $Article['status']=='closed_client_temp' || $Article['status']=='plag_exec')
                    {
                        $Article['status']='under_study';
                    }    


                     if($Article['status']=='disapprove_client') 
                     {
                        $Article['status']='disapproved';   
                     }   
                   

                    $current_time=time(); 
                    $time_expires=$Article['article_submit_expires'];
                    $timestap_diff= $current_time - $time_expires;                    
                        if($Article['missiontest']=='yes')
                        {
                            $participation_expires=$Article['participation_expires'];
                            if($time_expires >0)
                            {
                    if($Article['status']=='bid' && $timestap_diff > 0 )
                    {
                      $encoursArticles[$en_cnt]['status']='time_out';
                      $Article['status']='time_out';
                    }  
                            }
                            else{
                                if($Article['status']=='bid' && $participation_expires < time())
                                {
                                    $encoursArticles[$en_cnt]['status']='time_out';
                                    $Article['status']='time_out';
                                }
                            }

                        }
                        else if($Article['status']=='bid' && $timestap_diff > 0 )
                        {
                            $encoursArticles[$en_cnt]['status']='time_out';
                            $Article['status']='time_out';
                        }
                     $encoursArticles[$en_cnt]['status_trans']=$this->getAOStatus($Article['status']);
                     


                    $en_cnt++;
                  } 
                  /**awaiting award articles**/
                  if($Article['status']=='bid_premium' || $Article['status']=='bid_nonpremium' || $Article['status']=='bid_temp' || $Article['status']=='bid_refused_temp')
                  {
                    $awaitingArticles[$await_cnt]=$ongoingArticles[$cnt];
                    //private icon
                     if($awaitingArticles[$await_cnt]['AOtype']=='private')
                     {
                        $private_icon=$this->private_icon;
                        $writers_count=count(explode(",",$awaitingArticles[$await_cnt]['contribs_list']));
                        if($writers_count>1)
                          $toolTitle=$this->ptoolTitleMulti;
                        else
                          $toolTitle=$this->ptoolTitle;

                          $toolTitle=str_replace('X',$writers_count,$toolTitle);
                          $private_icon=str_replace('$toolTitle',$toolTitle,$private_icon);
                                      
                        $awaitingArticles[$await_cnt]['picon']=$private_icon;
                        
                      }  
                     else
                      $awaitingArticles[$await_cnt]['picon']=''; 

                    //quiz icon
                    if($awaitingArticles[$await_cnt]['link_quiz']=='yes' && $awaitingArticles[$await_cnt]['quiz'] && !in_array($awaitingArticles[$await_cnt]['quiz'],$this->qualifiedQuiz))
                     $awaitingArticles[$await_cnt]['qicon']=$this->quiz_icon;
                    else
                     $awaitingArticles[$await_cnt]['qicon']='';

                    $await_cnt++;
                  }
                  
                  $cnt++;
                }
            }

            //Corrector Ongoing Articles
            $participation_corrector=new Ep_Participation_CorrectorParticipation();
            $ongoingCorrectorArticles=$participation_corrector->ongoingArticles($ContributorIdentifier);
            if(count($ongoingCorrectorArticles)>0)
            {
                $crcnt=0;
                $en_crcnt=0;
                $await_crcnt=0;
                foreach($ongoingCorrectorArticles as $CArticle)
                {
                    
                    // && $CArticle['corrector_submit_expires']>0
                    /**encours Corrector Articles**/
                    if((($CArticle['status']=='bid' || $CArticle['status']=='under_study' || $CArticle['status']=='disapproved' )) || ($CArticle['status']=='time_out'))
                  {
                      $encoursCorrectorArticles[$en_crcnt]=$ongoingCorrectorArticles[$crcnt];
                      $encoursCorrectorArticles[$en_crcnt]['ao_type']='correction';
                      if($CArticle['status']=='on_hold')
                          $CArticle['status']='under_study';

                        $current_time=time(); 
                        $time_expires=$CArticle['corrector_submit_expires'];
                        $timestap_diff= $current_time - $time_expires;
                        $encoursCorrectorArticles[$en_crcnt]['status']=$CArticle['status'];
                        $encoursCorrectorArticles[$en_crcnt]['status_trans']='';
                        if($CArticle['writer_status']=='bid' || $CArticle['writer_status']=='disapproved' || !$CArticle['participate_id'] )//newly added for simultaneous correction
                        {
                           $encoursCorrectorArticles[$en_crcnt]['status']='writing_ongoing';
                           $encoursCorrectorArticles[$en_crcnt]['status_trans']='R&eacute;daction en cours';
                        }
                        else 
                        if($CArticle['writer_status']!='under_study' || ($CArticle['writer_status']=='under_study' && $CArticle['writer_stage']=='stage0'))
                        {
                            $CArticle['status']='under_study';
                            $encoursCorrectorArticles[$en_crcnt]['status']='under_study';
                        }
                        else if(($CArticle['status']=='bid' || $CArticle['status']=='disapproved')  && $timestap_diff > 0 )
                        {
                          $encoursCorrectorArticles[$en_crcnt]['status']='time_out';
                          $CArticle['status']='time_out';
                        }                       

                        if(!$encoursCorrectorArticles[$en_crcnt]['status_trans'])
                       $encoursCorrectorArticles[$en_crcnt]['status_trans']=$this->getAOStatus($CArticle['status']);
                      $en_crcnt++;
                  }
                   /**awaiting award Corrector articles**/
                    if($CArticle['status']=='bid_corrector' || $CArticle['status']=='bid_temp' || $CArticle['status']=='bid_refused_temp')
                  {
                    $awaitingCorrectorArticles[$await_crcnt]=$ongoingCorrectorArticles[$crcnt];
                    $awaitingCorrectorArticles[$await_crcnt]['ao_type']='correction';
                     
                      if($awaitingCorrectorArticles[$await_crcnt]['correction_type']=='private')
                     {
                        $private_icon=$this->private_icon;
                        $writers_count=count(explode(",",$awaitingCorrectorArticles[$await_crcnt]['corrector_privatelist']));
                        if($writers_count>1)
                          $toolTitle=$this->ptoolTitleMulti;
                        else
                          $toolTitle=$this->ptoolTitle;

                          $toolTitle=str_replace('X',$writers_count,$toolTitle);
                          $private_icon=str_replace('$toolTitle',$toolTitle,$private_icon);
                                      
                        $awaitingCorrectorArticles[$await_crcnt]['picon']=$private_icon;
                        
                      }  
                     else
                      $awaitingCorrectorArticles[$await_crcnt]['picon']=''; 
					         $cnt++;
                    $await_crcnt++;
                  }
                  $crcnt++;
                }
            }
            //echo "<pre>";print_r($ongoingCorrectorArticles);exit;

            //Ongoing Poll Details
            $poll_params['req_from']='ongoing';
            $ongoingPollDetails=$this->pollAoSearch($poll_params);
            $ongoingPollCount=count($ongoingPollDetails); 

            if($ongoingPollCount > 0)
            {
              $await_pollcnt=0;
              foreach($ongoingPollDetails as $key=>$pollDetails)
              {
                  if($pollDetails['action']=='update')
                  {
                    $awaitingPolls[$await_pollcnt]=$pollDetails;     
                    $await_pollcnt++;
                  }  
              }
            }



            $encoursArticles=array_merge($encoursArticles,$encoursCorrectorArticles);
            $awaitingArticles=array_merge($awaitingArticles,$awaitingCorrectorArticles,$awaitingPolls);

            $this->_view->encoursArticles=$encoursArticles;
            $this->_view->awaitingArticles=$awaitingArticles;
            //echo "<prE>";print_r($encoursArticles);exit;
            /**get Recent Ao Offers**/
            $this->_view->recent_AO_Offers=$this->recentAoOffers();
            
            
            /*get Published Articles**/
            $publishedArticles=$this->publishedArticles();
            //echo "<pre>";print_r($publishedArticles);
            $this->_view->publishedArticles=$publishedArticles;
      
      
            $participation_corrector=new Ep_Participation_CorrectorParticipation();
            $ongoingCorrectorArticles=$participation_corrector->ongoingArticles($ContributorIdentifier);
            if(count($ongoingCorrectorArticles)>0)
            {
                $cnt=0;
                foreach($ongoingCorrectorArticles as $Article)
                {
                    $ongoingCorrectorArticles[$cnt]['clientLogo']=$this->getClientPicPath($Article['clientId'],'profile');
                    $ongoingCorrectorArticles[$cnt]['participants']=$participation_corrector->getParticipantCountOngoing($Article['article_id']);
                    if($ongoingCorrectorArticles[$cnt]['participants']==0)
                        $ongoingCorrectorArticles[$cnt]['participants']=1;
                    if($Article['status']=='on_hold')
                        $Article['status']='under_study';
                    $ongoingCorrectorArticles[$cnt]['status_trans']=$this->getAOStatus($Article['status']);
                    $cnt++;
                }
            }
            // echo "<pre>";print_r($ongoingArticles);exit;
       
            $this->_view->meta_title="Author-Author participation";
            $this->_view->ongoingArticles=$ongoingArticles;
            $this->_view->ongoingCorrectorArticles=$ongoingCorrectorArticles;
            $this->_view->ContributorIdentifier=$ContributorIdentifier;
            $this->render("Contrib_ongoing");
        }    
    } 
   /**Contributor's published Articles*/
    public function publishedArticles($searchArticleParams=NULL,$ContributorIdentifier=NULL)
    {
        
        if($this->_helper->EpCustom->checksession())
        {
            if(!$ContributorIdentifier)
                $ContributorIdentifier=$this->contrib_identifier;
             //$searchArticleParams=$this->_request->getParams();
             if($searchArticleParams['search_article']!=NULL)
             {
                 $publishSearchParams['search_article']=($searchArticleParams['search_article']);
                 $publishSearchParams['order_date']=$searchArticleParams['order_date'];
                 $publishSearchParams['order_price']=$searchArticleParams['order_price'];
             }
            $publishSearchParams['search_article_id']=$searchArticleParams['search_article_id'];              

            $participation=new Ep_Participation_Participation();
            $ticket=new Ep_Ticket_Ticket();
            $publishedArticles=$participation->publishedArticles($publishSearchParams,$ContributorIdentifier);
            if(count($publishedArticles)==0)
                $publishedArticles=array();


            //Correction Published Articles
               $cparticipation=new Ep_Participation_CorrectorParticipation();
               $cpublishedArticles=$cparticipation->publishedArticles($publishSearchParams,$ContributorIdentifier);
               if(count($cpublishedArticles)==0)
                 $cpublishedArticles=array();

               $publishedArticles=array_merge($publishedArticles,$cpublishedArticles);

            

            $totalPublishedAmount=$participation->getPublishedAmount($ContributorIdentifier);
            if(count($publishedArticles)>0)
            {
                $cnt=0;
                foreach($publishedArticles as $Article)
                {
                    $publishedArticles[$cnt]['client_pic']=$this->getClientPicPath($Article['clientId'],'home');
                    $publishedArticles[$cnt]['company_name']= $ticket->getUserName($Article['clientId']);  
                    $publishedArticles[$cnt]['category']= $this->getCategoryName($Article['category']);
                    $publishedArticles[$cnt]['language_name']= $this->getLanguageName($Article['language']);
                    //specfile path
                    $spec_path=SPEC_FILE_PATH.$Article['filepath']; 
                    if(file_exists($spec_path) && !is_dir($spec_path))
                        $publishedArticles[$cnt]['spec_exists']='yes';

                     if($Article['premium_option']!='0' && $Article['premium_option']!='' )
                         $publishedArticles[$cnt]['ao_type']='premium';
                      else
                        $publishedArticles[$cnt]['ao_type']='nopremium';  
                   
                    $cnt++;
                }
            }
                       
           return $publishedArticles;
        }      
    }
  
    /*AO SEARCH ACTION*/
    public function aosearchAction()
    {
       // $this->render("Contrib_aosearch");exit;
    if($this->_helper->EpCustom->checksession())
        {
            $articleParams=$this->_request->getParams();
            $article=new Ep_Article_Article();
            $participants=new Ep_Participation_Participation();
            /**Cartsession**/
            $this->EP_Contrib_Cart = Zend_Registry::get('EP_Contrib_Cart');
            $paramCount=count($articleParams);
            if($paramCount>3)
            {
                $searchParameters['search_article']=utf8_decode($articleParams['search_article']);
                $searchParameters['articleId']=$articleParams['articleid'];
                $searchParameters['search_category']=$articleParams['search_category'];
                $searchParameters['search_type']=$articleParams['search_type'];
                $searchParameters['search_language']=$articleParams['search_language'];
                $searchParameters['search_ao_type']=$articleParams['search_ao_type'];                
                $searchParameters['orderByTitle']=$articleParams['orderByTitle'];
                $searchParameters['orderByLang']=$articleParams['orderByLang'];
                $searchParameters['orderByAttendee']=$articleParams['orderByAttendee'];
                $searchParameters['orderByQuotePrice']=$articleParams['orderByQuotePrice'];
                $searchParameters['orderBytime']=$articleParams['orderBytime']; 
                
            }
            $searchParameters['profile_type']=$this->profileType;
            $searchParameters['black_status']=$this->black_status;  

            /**AO types Array**/
            $aotype_list=array( "m_premium"=>"Premium Mission ",
                                   "m_npremium"=>"Freedom Mission",
                                   "p_premium"=>"Premium Quote",//"p_npremium"=>"Devis libert&eacute;",                                   
                                   "correction"=>"Proofreading Mission"
                                 );
           
           //$articleDetails=$article->getArticleDetails($searchParameters);
            $articleDetails=$article->getArticleSearchDetails($searchParameters);
           
            $articleCount=count($articleDetails);
           if($articleCount>0)
            {
                  $cnt=0;
                $recruitment=0;
                $i = 0;
                foreach($articleDetails as $details) {
                    $flag = "true";//default value at begining
                    //checking if source laug is compulsury to check, if source laung is compulsary to check then unserialize launge_more and check if the laung fluence is above 50%//
                    if ($details['sourcelang_nocheck'] != 'yes' && $details['product'] === 'translation') {
                        $language_more = unserialize($details['language_more']);
                        $flag = "fasle";
                        foreach ($language_more as $key => $value) {
                            if ($key === $details['language_source'] && (int)$value >= 50) {
                                $flag = "true";//false true if the source launge is in lang more of contributor
                            }
                        }
                    }
                    if ($flag === "true") {
                     $articleDetails[$cnt]['client_pic']= $this->getClientPicPath($details['identifier']);
                     $articleDetails[$cnt]['category']= $this->getCategoryName($details['category']);
                     $articleDetails[$cnt]['type']= $this->getArticleTypeName($details['type']);
                    $articleDetails[$cnt]['product_type_name']= $this->producttype_array[$details['type']];
                     $articleDetails[$cnt]['language_name']= $this->getLanguageName($details['language']);
                     $articleDetails[$cnt]['delivery_date']= date('d/m/Y',strtotime($details['delivery_date']));
                     $articleDetails[$cnt]['DaysDifference']= $this->getDaysDiff($details['delivery_date']);
                     if($details['participation_expires'])
                        $articleDetails[$cnt]['timestamp']= $details['participation_expires'];
                    // else
                      //  $articleDetails[$cnt]['timestamp']= strtotime(date('Y-m-d H:i:s',strtotime($details['delivery_date']." 23:59:59")));
                      /**time stamp for ao end date**/
                     // $articleDetails[$cnt]['timestamp_aoend']= strtotime(date('Y-m-d H:i:s',strtotime($details['delivery_date']." 23:59:59")));
                       $articleDetails[$cnt]['timestamp_aoend']= $details['participation_expires'];
                     /**submit and resubmit times**/
                      if($this->profileType=='senior')
                      {
                          if($details['senior_time'])
                            $articleDetails[$cnt]['article_submit_time']=$details['senior_time'];
                          else
                              $articleDetails[$cnt]['article_submit_time']=$this->config['sc_time'];
                          if($details['sc_resubmission'])
                              $articleDetails[$cnt]['article_resubmit_time']=$this->config['sc_resubmission'];
                          else
                              $articleDetails[$cnt]['article_resubmit_time']=$this->config['sc_resubmission'];
                      }
                      else if($this->profileType=='junior')
                      {
                          if($details['junior_time'])
                              $articleDetails[$cnt]['article_submit_time']=$details['junior_time'];
                          else
                              $articleDetails[$cnt]['article_submit_time']=$this->config['jc_time'];
                          if($details['jc_resubmission'])
                              $articleDetails[$cnt]['article_resubmit_time']=$details['jc_resubmission'];
                          else
                              $articleDetails[$cnt]['article_resubmit_time']=$this->config['jc_resubmission'];
                      }
                      else if($this->profileType=='sub-junior')
                      {
                          if($details['subjunior_time'])
                              $articleDetails[$cnt]['article_submit_time']=$details['subjunior_time'];
                          else
                              $articleDetails[$cnt]['article_submit_time']=$this->config['jco_time'];
                          if($details['jco_resubmission'])
                              $articleDetails[$cnt]['article_resubmit_time']=$details['jco_resubmission'];
                          else
                              $articleDetails[$cnt]['article_resubmit_time']=$this->config['jc0_resubmission'];
                      }
                      /**participation time**/
                      if($details['participation_time'])
                        $articleDetails[$cnt]['participation_time']=$details['participation_time'];
                      else
                        $articleDetails[$cnt]['participation_time']=$this->config['participation_time'];
                      if($articleDetails[$cnt]['participation_time']>=60)
                           $articleDetails[$cnt]['participation_time_text']=($articleDetails[$cnt]['participation_time']/60)."heure(s)";
                      else
                           $articleDetails[$cnt]['participation_time_text']=$articleDetails[$cnt]['participation_time']."mns";  
                     $articleDetails[$cnt]['participants']= $participants->getParticipantCount($details['articleid']);
                      if(!$details['company_name'] && $details['deli_anonymous']=='0')
                      {
                          $articleDetails[$cnt]['company_name']='Anonyme';     
                      }
                     
                     if(isset($this->EP_Contrib_Cart->cart[$articleDetails[$cnt]['articleid']]))
                         $articleDetails[$cnt]['attended']='YES';
                     else
                         $articleDetails[$cnt]['attended']='NO';   

                      //private icon
                       if($articleDetails[$cnt]['AOtype']=='private')
                       {
                          $private_icon=$this->private_icon;
                          $writers_count=count(explode(",",$articleDetails[$cnt]['contribs_list']));
                          if($writers_count>1)
                            $toolTitle=$this->ptoolTitleMulti;
                          else
                            $toolTitle=$this->ptoolTitle;

                            $toolTitle=str_replace('X',$writers_count,$toolTitle);
                            $private_icon=str_replace('$toolTitle',$toolTitle,$private_icon);
                                        
                          $articleDetails[$cnt]['picon']=$private_icon;
                          
                        }  
                       else
                        $articleDetails[$cnt]['picon']='';

                      //quiz icon
                      if($articleDetails[$cnt]['link_quiz']=='yes' && $articleDetails[$cnt]['quiz'] && !in_array($articleDetails[$cnt]['quiz'],$this->qualifiedQuiz))
                       $articleDetails[$cnt]['qicon']=$this->quiz_icon;
                      else
                       $articleDetails[$cnt]['qicon']='';
                      
                      //latest price proposed.
                    $articleDetails[$cnt]['latestPrice']=$participants->getLatestProposedPrice($details['articleid']);    
             
                    $filter_category.=$details['filter_category'].",";
                    $filter_language.=$details['filter_language'].",";   
              
                      
                    /*//Added w.r.t recruitment
                    if($details['missiontest']=='yes')
                    {
                        $articleDetails[$cnt]['turnover']=($details['price_max']*$details['mission_volume']);
                        $recruitmentDetails[$recruitment]=$articleDetails[$cnt];
                        $recruitment++;
                        unset($articleDetails[$cnt]);
                    }*/
                        $articleDetails_temp[$i++] = $articleDetails[$cnt];// initailizing recent offerts to temp variable//
                    }
                     $cnt++;
                 }
                if($i > 0)
                    $articleDetails = $articleDetails_temp;//inititailze temp array to original array after filtering the array//
                else
                    $articleDetails = array();//or just assign a empty array if there are no result after filter//.
                //Added w.r.t recruitment
                /*if($recruitment>0)
                {
                    $this->_view->recruitmentDetails=$recruitmentDetails;
                    array_values($articleDetails);
                }*/
                // echo "<pre>";print_r($recruitmentDetails);echo"</pre>";
                          
                $this->_view->searchCount= $articleCount;
                if($searchParameters['search_article']!='')
                    $this->_view->searchText=($searchParameters['search_article']);
                if($searchParameters['search_category']!='')
                    $this->_view->searchText=$this->getCategoryName($searchParameters['search_category']).$this->utf8dec(" Cat&eacute;gorie");
            }
            else if($paramCount>0 && $articleCount==0)
            {
                 $this->_view->searchCount= "No";
                 if($searchParameters['search_article']!='')
                    $this->_view->searchText=($searchParameters['search_article']);
                else if($searchParameters['search_category']!='')
                    $this->_view->searchText=$this->getCategoryName($searchParameters['search_category']).$this->utf8dec(" Cat&eacute;gorie");
            }
            

         /***********getting POll AO Details**************/
                $pollDetails=$this->pollAoSearch($searchParameters);
                $pollCount=count($pollDetails); 
                if($pollCount==0)  
                    $pollDetails=array();
                if(count($pollCount)>0)
                {
                  foreach($pollDetails as $count_poll)
                  {
                    $filter_category.=$count_poll['category_key'].",";                    
                    $filter_language.=$count_poll['language'].",";
                  }
                }         

         /***********getting Correction AO Details**************/
                  $correctionAoDetails=$this->correctionAoSearch($searchParameters);  
                  $correctionCount=count($correctionAoDetails);
                  if($correctionCount==0)  
                      $correctionAoDetails=array();  
				
				$co=0;		
            $j=0;
                  if(count($correctionCount)>0)
                  {
                    foreach($correctionAoDetails as $count_correction)
                    {
                    $flag1 = true;//default value at begining
                    //checking if source laug is compulsury to check, if source laung is compulsary to check then unserialize launge_more and check if the laung fluence is above 50%//
                    if($count_correction['sourcelang_nocheck_correction'] != 'yes' && $count_correction['product'] === 'translation') {
                        $language_more1 = unserialize($count_correction['language_more']);
                        $flag1 = fasle;
                        foreach($language_more1 as $key => $value){
                            if($key === $count_correction['language_source'] && (int)$value >= 50){
                                $flag1 = true;//false true if the source launge is in lang more of contributor
                            }
                        }
                    }
                    if($flag1 === true) {
                      $filter_category.=$count_correction['category'].",";
                      $filter_language.=$count_correction['language'].",";
					  
					  //Private icons
						 if($correctionAoDetails[$co]['correction_type']=='private')
						 {
							$private_icon=$this->private_icon;
							$writers_count=count(explode(",",$correctionAoDetails[$co]['corrector_privatelist']));
							if($writers_count>1)
							  $toolTitle=$this->ptoolTitleMulti;
							else
							  $toolTitle=$this->ptoolTitle;

							  $toolTitle=str_replace('X',$writers_count,$toolTitle);
							  $private_icon=str_replace('$toolTitle',$toolTitle,$private_icon);
										  
							$correctionAoDetails[$co]['picon']=$private_icon;
							
						  }  
						 else
						  $correctionAoDetails[$co]['picon']=''; 		
                        $correctionAoDetails_temp[$j++] = $correctionAoDetails[$co];// initailizing recent offerts to temp variable filtering manually//
                    }
					$co++;
                    }
                  }
            if($j > 0 )
                $correctionAoDetails = $correctionAoDetails_temp;//inititailze temp array to original array after filtering the array//*/
            else
                $correctionAoDetails=array();
                  if(count($articleDetails)==0 OR !$articleDetails)
                    $articleDetails=array();
                  
                  if($searchParameters['search_ao_type']=='p_premium' ||  $searchParameters['search_ao_type']=='p_npremium')
                  {
                     $articleDetails=$pollDetails; 
                  }
                  elseif($searchParameters['search_ao_type']=='m_premium' ||  $searchParameters['search_ao_type']=='m_npremium')
                  {
                     $articleDetails=$articleDetails;
                  }
                  elseif($searchParameters['search_ao_type']=='correction')
                  {
                     $articleDetails=$correctionAoDetails;
                  }
                  else if($pollCount>0 || count($articleDetails)>0 || $correctionCount > 0)
                  {
                      $articleDetails=array_merge($articleDetails,$pollDetails,$correctionAoDetails);                                           
                  }  
                  
                  
                  if($searchParameters['orderByTitle']=='asc')
                    usort($articleDetails, 'sortByTitleASC');
                  elseif($searchParameters['orderByTitle']=='desc')
                    usort($articleDetails, 'sortByTitleDESC');
                  elseif($searchParameters['orderBytime']=='desc')
                     usort($articleDetails, 'sortByTimestampDESC');  
                  elseif($searchParameters['orderByQuotePrice']=='desc')
                     usort($articleDetails,'sortByPriceDESC'); 
                  else if($searchParameters['orderByQuotePrice']=='asc')
                     usort($articleDetails,'sortByPriceASC'); 
                  elseif($searchParameters['orderByAttendee']=='desc')
                     usort($articleDetails,'sortByParticipantsDESC'); 
                  else if($searchParameters['orderByAttendee']=='asc')
                     usort($articleDetails,'sortByParticipantsASC');
                  else
                    usort($articleDetails, 'sortByTimestamp');
                  
                    /**search Pagination**/
                 
                if(count($articleDetails)>0) 
                {
                  //Added for popup prev next 
                  $_SESSION['all_offers']=$articleDetails;

                  $page = $this->_getParam('page',1);
                  $paginator = Zend_Paginator::factory($articleDetails);
                  $paginator->setItemCountPerPage($this->config['pagination_fo']);
                  $paginator->setCurrentPageNumber($page);
                  //$this->_view->pagination=print_r($paginator->getPages(),true);
                  $patterns='/[? &]page=[0-9]{1,2}/';
                  $replace="";
                  $this->_view->articles = $paginator;
                  $this->_view->pages = $paginator->getPages();
                  $this->_view->pageURL=preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
                } 
                     
                 
                
                
            
              $this->_view->searchgetText=($searchParameters['search_article']);
            
             $clients=new Ep_Ticket_Ticket();
            //$get_clients=$clients->getContacts('client');
            $get_clients=$clients->ongoingClients();
            $client_count=0;
            //$filter_category='';
            //$filter_language='';
            if($get_clients!="Not Exists")
            {
              foreach($get_clients as $client)
              {
                if($client['contact_name']!=NULL)
                  $get_clients[$client_count]['clientName']=$client['contact_name'];
                else
                {
                  $client['email']=explode("@",$client['email']);
                  $get_clients[$client_count]['clientName']=$client['email'][0];
                }
                if($articleParams['client_contact'] && $articleParams['client_contact']==$client['identifier'])
                {
                  $this->_view->clientFilter=$get_clients[$client_count]['clientName'];
                }
                else if($articleParams['client_contact']=='anonymous')
                {
                  $this->_view->clientFilter='Anonyme';
                }
               // $filter_category.=$client['filter_category'].",";
                //$filter_language.=$client['filter_language'].","; 
                $client_count++;
              }
          } 

          //Added for favicon
          if($articleParams['favicon']=='ao_details')
          {
            echo count($articleDetails);exit;
          }  




      
      /**categories having result**/
        $filter_category=array_unique(explode(",",substr($filter_category,0,-1)));
        $this->_view->filter_category=$filter_category;
        $categories_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        natsort($categories_array);
        $this->_view->ep_categories_list=$categories_array;
      
      /**languages Having result**/
        $filter_language=array_unique(explode(",",substr($filter_language,0,-1)));
        $this->_view->filter_language=$filter_language;
        $language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        natsort($language_array);
        $this->_view->ep_languages_list=$language_array;
            if($articleParams['search_type'])
                $this->_view->typeFilter=$this->getArticleTypeName($articleParams['search_type']);
           if($articleParams['search_category'])
                $this->_view->categoryFilter=$this->getCategoryName($searchParameters['search_category']);
        
           if($articleParams['search_language'])
                $this->_view->languageFilter=$this->getLanguageName($searchParameters['search_language']);  
            if($articleParams['search_ao_type'])
                $this->_view->aoTypeFilter=$aotype_list[$searchParameters['search_ao_type']];   



          //Upcoming Artilce/Polls info
          $upcomingArticles=$this->upcomingDeliveries($articleParams);
          if(count($upcomingArticles)>0) 
          {
            //Added for popup prev next 
            $_SESSION['upcoming_offers']=$upcomingArticles;
          }  
           $this->_view->upcomingArticles=$upcomingArticles;


          //Finished Artilce/polls info
          $terminatedArticles=$this->finishedDeliveries($articleParams);
          if(count($terminatedArticles)>0) 
          {
            //Added for popup prev next 
            $_SESSION['finished_offers']=$terminatedArticles;
          }  
           $this->_view->terminatedArticles=$terminatedArticles; 
           
        
            //Added w.r.t recruitment
            $recruitment_obj=new Ep_Recruitment_Participation();
            $ticket_obj= new Ep_Ticket_Ticket();
            $searchParameters['request_page']='aosearch';
            $recruitmentDetails=$recruitment_obj->getRecentRecruitmentOffers(10,$searchParameters);
			//echo "<pre>";print_r($recruitmentDetails);exit;
            $recruitmentCount=count($recruitmentDetails);
            if($recruitmentCount>0)
            {
                foreach($recruitmentDetails as $r=>$recruitment)
                {

                    if($recruitment['participation_expires'])
                        $recruitmentDetails[$r]['timestamp']= $recruitment['participation_expires'];
            
                    $recruitmentDetails[$r]['product_type_name']= $this->producttype_array[$recruitment['type']];
		    $recruitmentDetails[$r]['language_name']= $this->getLanguageName($recruitment['language']);
		    $recruitmentDetails[$r]['turnover']=($recruitment['price_max']*$recruitment['mission_volume']);
                    $recruitmentDetails[$r]['product_name']=$recruitment['product']=='redaction' ? 'Write' : 'Translate';
                    $recruitmentDetails[$r]['client_name']=$ticket_obj->getUserName($recruitment['client_id']);
                    if($recruitment['delivery_period']=='day')
                        $recruitmentDetails[$r]['total_weeks']=round($recruitment['delivery_time_frame']/7);
                    else if($recruitment['delivery_period']=='month')
                        $recruitmentDetails[$r]['total_weeks']=round(($recruitment['delivery_time_frame']*30)/4);
                    else
                        $recruitmentDetails[$r]['total_weeks']=round($recruitment['delivery_time_frame']);
                 //$recruitmentDetails[$r]['turnover']=round($recruitment['price_max']*$recruitmentDetails[$r]['total_weeks']*$recruitment['max_articles_per_contrib']);
                }
                $this->_view->recruitmentDetails=$recruitmentDetails;
             }
             /*if($_REQUEST['debug'])
             {
                echo "<pre>";print_r($recruitmentDetails);exit;
             }*/     
            $this->_view->ep_aotype_list=$aotype_list;
            $this->_view->contributor_identifier=$this->contrib_identifier;
            $this->_view->meta_title="contributor classifieds";
            $this->render("Contrib_aosearch");
        }
  }
  /**public profile**/
  public function publicProfileAction()
  {
      //setlocale(LC_TIME, "fr_FR");
      $nationality_array=$this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
      if($this->_helper->EpCustom->checksession())
        {
            $profileplus_obj = new Ep_Contrib_ProfilePlus();
            $profileContrib_obj = new Ep_Contrib_ProfileContributor();
            $user_obj=new EP_Contrib_Registration();

            $profileParams=$this->_request->getParams();
            if($profileParams['user_id'])
              $user_identifier=$profileParams['user_id'];
            else
              $user_identifier=$this->contrib_identifier;

            /**getting user email*/
            $userDetails=$user_obj->getUserInfo($user_identifier);

            $profile_identifier_info=$profileplus_obj->checkProfileExist($user_identifier);
            if($profile_identifier_info!='NO')
            {
               $profile_identifier=$profile_identifier_info[0]['user_id'];
               $profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
               $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);
         
                $this->_view->ep_contrib_profile_civ=$profileinfo[0]['initial'];
                $this->_view->ep_contrib_profile_fname=$profileinfo[0]['first_name'];
                $this->_view->ep_contrib_profile_lname=$profileinfo[0]['last_name'];
                $this->_view->ep_contrib_profile_address=$profileinfo[0]['address'];
                $this->_view->ep_contrib_profile_city=$profileinfo[0]['city'];
                $this->_view->ep_contrib_profile_telephone=$profileinfo[0]['phone_number'];
                $this->_view->ep_contrib_profile_postalcode=$profileinfo[0]['zipcode'];
                $this->_view->ep_contrib_profile_country=$profileinfo[0]['country'];
                $this->_view->ep_contrib_profile_dob=$profile_contribinfo[0]['dob'];
                $this->_view->ep_contrib_age=calculateAge($profile_contribinfo[0]['dob']);
        
                /**cv Uploaded Date && path**/
                if($profile_contribinfo[0]['cv_uploaded_at'])
                $this->_view->cv_uploaded_at=date("d F Y",strtotime($profile_contribinfo[0]['cv_uploaded_at']));
                $cv_path=APP_PATH_ROOT.$this->_config->path->contrib_cv_path.$user_identifier.'/'.$profile_contribinfo[0]['cv_file'];
                if(!is_dir($cv_path) && file_exists($cv_path))
                  $this->_view->cv_exists='yes';
                $this->_view->ep_contrib_profile_profession=$profile_contribinfo[0]['profession'];
                $this->_view->ep_contrib_profession_other= $profile_contribinfo[0]['profession_other'];
                $this->_view->ep_contrib_profile_university=$profile_contribinfo[0]['university'];
                $this->_view->ep_contrib_profile_education=$profile_contribinfo[0]['education'];
                $this->_view->ep_contrib_profile_degree=$profile_contribinfo[0]['degree'];
                $mother_lang=$this->getLanguageName($profile_contribinfo[0]['language']);
                $this->_view->ep_language=$mother_lang;
                
                /**getting all languages and percentages**/
                if($profile_contribinfo[0]['language_more'])
                $languages_more=unserialize($profile_contribinfo[0]['language_more']);
                
                if(count($languages_more)>0)
                {
                  $lang=0;
                  foreach($languages_more as $key=>$percent)
                  {
                    $more_languages[$lang]['name']=$this->getLanguageName($key);
                    $more_languages[$lang]['percent']=$percent;
                    $all_languages.=$more_languages[$lang]['name']." , ";
                    $lang++;
                  }
                }
                $this->_view->language_more=$more_languages;
                
                $this->_view->allLanguages=substr($all_languages,0,-2);
                
                
                
                 if($profile_contribinfo[0]['category_more'])
                    $categories_more=unserialize($profile_contribinfo[0]['category_more']);
                if(count($categories_more)>0)
                {
                  $cat=0;
                  foreach($categories_more as $key=>$percent)
                  {
                    $more_categories[$cat]['name']=$this->getCategoryName($key);
                    $more_categories[$cat]['percent']=$percent;
                    $all_categories.=$more_categories[$cat]['name']." , ";
                    $cat++;
                  }
                }
                $this->_view->category_more=$more_categories;
                $this->_view->allCategories=substr($all_categories,0,-2);
                
                        
                $this->_view->ep_contrib_profile_nationality=$profile_contribinfo[0]['nationality'];
                $this->_view->nationality=$nationality_array[$profile_contribinfo[0]['nationality']];
                
                $this->_view->ep_contrib_profile_category=explode(",",$profile_contribinfo[0]['favourite_category']);
                $this->_view->ep_contrib_profile_self_details=strip_tags($profile_contribinfo[0]['self_details']);
                $this->_view->ep_contrib_profile_payment_type=$profile_contribinfo[0]['payment_type'];
                /**iNOVICE inFO ***/
                $this->_view->ep_contrib_profile_pay_info_type=$profile_contribinfo[0]['pay_info_type'];
                $this->_view->ep_contrib_profile_SSN=$profile_contribinfo[0]['SSN'];
                $this->_view->ep_contrib_profile_company_number=$profile_contribinfo[0]['company_number'];
                $this->_view->ep_contrib_profile_vat_check=$profile_contribinfo[0]['vat_check'];
                $this->_view->ep_contrib_profile_VAT_number=$profile_contribinfo[0]['VAT_number'];
                /**Paypal and RIB info**/
                $this->_view->ep_contrib_paypal_id=$profile_contribinfo[0]["paypal_id"] ;
                $RIB_ID=array_filter(explode("|",$profile_contribinfo[0]["rib_id"]));
                if( count($RIB_ID)==2)
                {
                    $this->_view->ep_contrib_rib_id_6=$RIB_ID[0];
                    $this->_view->ep_contrib_rib_id_7=$RIB_ID[1];
                }
                else
                {
                    $this->_view->ep_contrib_rib_id_1=$RIB_ID[0];
                    $this->_view->ep_contrib_rib_id_2=$RIB_ID[1];
                    $this->_view->ep_contrib_rib_id_3=$RIB_ID[2];
                    $this->_view->ep_contrib_rib_id_4=$RIB_ID[3];
                    $this->_view->ep_contrib_rib_id_5=$RIB_ID[4];
                }
              /**profile IMage*/
                $this->_view->profile_image=$this->getPicPath($user_identifier,'profile'); 
              /**Total Royalties**/
                $royalty_obj=new Ep_Royalty_Royalties();
                $totalRoyalty=$royalty_obj->getTotalRoyalty($this->EP_Contrib_reg->clientidentifier);
                $this->_view->userRoyalty=$totalRoyalty;  
              /**getting User expeience details**/
              $experience_obj=new Ep_Contrib_Experience();
              $jobDetails=$experience_obj->getExperienceDetails($user_identifier,'job');
              if($jobDetails!="NO")
              {
                $jcnt=0;
                foreach($jobDetails as $job)
                {
                  $jobDetails[$jcnt]['start_date']=date('F Y',strtotime($job['from_year']."-".$job['from_month']));
                  if($job['still_working']=='yes')
                    $jobDetails[$jcnt]['end_date']='Actuel';
                  else
                    $jobDetails[$jcnt]['end_date']=date('F Y',strtotime($job['to_year']."-".$job['to_month']));
                  $jcnt++;
                }
                $this->_view->jobDetails=$jobDetails;
              } 
              $educationDetails=$experience_obj->getExperienceDetails($user_identifier,'education');
              if($educationDetails!="NO")
              {
                $ecnt=0;
                foreach($educationDetails as $education)
                {
                  $educationDetails[$ecnt]['start_date']=date('F Y',strtotime($education['from_year']."-".$education['from_month']));
                  if($education['still_working']=='yes')
                    $educationDetails[$ecnt]['end_date']='Actuel';
                  else
                    $educationDetails[$ecnt]['end_date']=date('F Y',strtotime($education['to_year']."-".$education['to_month']));
                  $ecnt++;
                }
                $this->_view->educationDetails=$educationDetails; 
              } 

              //Published articles
              $publishedArticles=$this->publishedArticles(NULL,$user_identifier);
              if(count($publishedArticles)>0)
              {
                  $publishedClients=array();
                  foreach($publishedArticles as $particle)
                  {
                    $publishedClients[$particle['clientId']]=$particle;
                  }

                    //$publishedClients=array_unique($publishedClients);
                  $this->_view->publishedClients=$publishedClients;

              }

              $this->_view->email=$userDetails[0]['email'];
              $this->_view->meta_title="contributor public profile";
               $this->render("Contrib_public_profile");
          }
          else
             $this->_redirect("/contrib/modify-profile");
        }
  }  
  
  /**private profile**/
  public function privateProfileAction()
  {
      //setlocale(LC_TIME, "fr_FR");
      /**nationality array**/
      $nationality_array=$this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
      if($this->_helper->EpCustom->checksession())
        {
            $profileplus_obj = new Ep_Contrib_ProfilePlus();
            $profileContrib_obj = new Ep_Contrib_ProfileContributor();
            $profile_identifier_info=$profileplus_obj->checkProfileExist($this->contrib_identifier);
            if($profile_identifier_info!='NO')
            {
               $profile_identifier=$profile_identifier_info[0]['user_id'];
               $profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
               $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);
         
                $this->_view->ep_contrib_profile_civ=$profileinfo[0]['initial'];
                $this->_view->ep_contrib_profile_fname=$profileinfo[0]['first_name'];
                $this->_view->ep_contrib_profile_lname=$profileinfo[0]['last_name'];
                $this->_view->ep_contrib_profile_address=$profileinfo[0]['address'];
                $this->_view->ep_contrib_profile_city=$profileinfo[0]['city'];
                $this->_view->ep_contrib_profile_telephone=$profileinfo[0]['phone_number'];
                $this->_view->ep_contrib_profile_postalcode=$profileinfo[0]['zipcode'];
                $this->_view->ep_contrib_profile_country=$profileinfo[0]['country'];
                $this->_view->ep_contrib_profile_dob=$profile_contribinfo[0]['dob'];
                $this->_view->ep_contrib_age=calculateAge($profile_contribinfo[0]['dob']);

                /**profile percentage**/
                $this->_view->profile_percentage=$this->calculateProfilePercentage();//$profile_contribinfo[0]['profile_percentage'];
        
                /**cv Uploaded Date && path**/
                if($profile_contribinfo[0]['cv_uploaded_at'])
                $this->_view->cv_uploaded_at=strftime("%d %B %Y",strtotime($profile_contribinfo[0]['cv_uploaded_at']));
                $cv_path=APP_PATH_ROOT.$this->_config->path->contrib_cv_path.$this->contrib_identifier.'/'.$profile_contribinfo[0]['cv_file'];
                if(!is_dir($cv_path) && file_exists($cv_path))
                  $this->_view->cv_exists='yes';
                $this->_view->ep_contrib_profile_profession=$profile_contribinfo[0]['profession'];
                $this->_view->ep_contrib_profession_other= $profile_contribinfo[0]['profession_other'];
                $this->_view->ep_contrib_profile_university=$profile_contribinfo[0]['university'];
                $this->_view->ep_contrib_profile_education=$profile_contribinfo[0]['education'];
                $this->_view->ep_contrib_profile_degree=$profile_contribinfo[0]['degree'];
                $mother_lang=$this->getLanguageName($profile_contribinfo[0]['language']);
                $this->_view->ep_language=$mother_lang;
                
                /**getting all languages and percentages**/
                if($profile_contribinfo[0]['language_more'])
                $languages_more=unserialize($profile_contribinfo[0]['language_more']);
                
                if(count($languages_more)>0 && is_array($languages_more))
                {
                  $lang=0;
                  foreach($languages_more as $key=>$percent)
                  {
                    $more_languages[$lang]['name']=$this->getLanguageName($key);
                    $more_languages[$lang]['percent']=$percent;
                    $all_languages.=$more_languages[$lang]['name']." , ";
                    $lang++;
                  }
                }
                $this->_view->language_more=$more_languages;
                
                $this->_view->allLanguages=substr($all_languages,0,-2);
                
                
                
                 if($profile_contribinfo[0]['category_more'])
                    $categories_more=unserialize($profile_contribinfo[0]['category_more']);
                if(count($categories_more)>0)
                {
                  $cat=0;
                  foreach($categories_more as $key=>$percent)
                  {
                    $more_categories[$cat]['name']=$this->getCategoryName($key);
                    $more_categories[$cat]['percent']=$percent;
                    $all_categories.=$more_categories[$cat]['name']." , ";
                    $cat++;
                  }
                }
                $this->_view->category_more=$more_categories;
                $this->_view->allCategories=substr($all_categories,0,-2);
                
                        
                $this->_view->ep_contrib_profile_nationality=$profile_contribinfo[0]['nationality'];
                $this->_view->nationality=$nationality_array[$profile_contribinfo[0]['nationality']];
                
                $this->_view->ep_contrib_profile_category=explode(",",$profile_contribinfo[0]['favourite_category']);
                $this->_view->ep_contrib_profile_self_details=strip_tags($profile_contribinfo[0]['self_details']);
                $this->_view->ep_contrib_profile_payment_type=$profile_contribinfo[0]['payment_type'];
                /**iNOVICE inFO ***/
                $this->_view->ep_contrib_profile_pay_info_type=$profile_contribinfo[0]['pay_info_type'];
                $this->_view->ep_contrib_profile_SSN=$profile_contribinfo[0]['SSN'];
                $this->_view->ep_contrib_profile_company_number=$profile_contribinfo[0]['company_number'];
                $this->_view->ep_contrib_profile_vat_check=$profile_contribinfo[0]['vat_check'];
                $this->_view->ep_contrib_profile_VAT_number=$profile_contribinfo[0]['VAT_number'];
                /**Paypal and RIB info**/
                $this->_view->ep_contrib_paypal_id=$profile_contribinfo[0]["paypal_id"] ;
                $RIB_ID=array_filter(explode("|",$profile_contribinfo[0]["rib_id"]));
                if(count($RIB_ID)==2)
                {
                    $this->_view->ep_contrib_rib_id_6=$RIB_ID[0];
                    $this->_view->ep_contrib_rib_id_7=$RIB_ID[1];
                }
              /**profile IMage*/
                $this->_view->profile_image=$this->getPicPath($this->contrib_identifier,'profile'); 
              /**Total Royalties**/
                $royalty_obj=new Ep_Royalty_Royalties();
                $totalRoyalty=$royalty_obj->getTotalRoyalty($this->EP_Contrib_reg->clientidentifier);
                if(!$totalRoyalty)
                    $totalRoyalty=0;
                $this->_view->userRoyalty=$totalRoyalty;  
              /**getting User expeience details**/
              $experience_obj=new Ep_Contrib_Experience();
              $jobDetails=$experience_obj->getExperienceDetails($this->contrib_identifier,'job');
              if($jobDetails!="NO")
              {
                $jcnt=0;
                foreach($jobDetails as $job)
                {
                  $jobDetails[$jcnt]['start_date']=date('F Y',strtotime($job['from_year']."-".$job['from_month']));
                  if($job['still_working']=='yes')
                    $jobDetails[$jcnt]['end_date']='Actuel';
                  else
                    $jobDetails[$jcnt]['end_date']=date('F Y',strtotime($job['to_year']."-".$job['to_month']));
                  $jcnt++;
                }
                $this->_view->jobDetails=$jobDetails;
              } 
              $educationDetails=$experience_obj->getExperienceDetails($this->contrib_identifier,'education');
              if($educationDetails!="NO")
              {
                $ecnt=0;
                foreach($educationDetails as $education)
                {
                  $educationDetails[$ecnt]['start_date']=date('F Y',strtotime($education['from_year']."-".$education['from_month']));
                  if($education['still_working']=='yes')
                    $educationDetails[$ecnt]['end_date']='Actuel';
                  else
                    $educationDetails[$ecnt]['end_date']=date('F Y',strtotime($education['to_year']."-".$education['to_month']));
                  $ecnt++;
                }
                $this->_view->educationDetails=$educationDetails; 
              } 
                /*added by naseer on 04-11-2015 */
                $software_array=$this->_arrayDb->loadArrayv2("EP_SOFTWARE_LIST", $this->_lang);
                //explodig all the software list and saing in multidimentaional array for later use//
                foreach($software_array as $k => $v){
                    $software_array[$k] = explode("-",$v);
                }
                $this->_view->ep_software_array=$software_array;
                //exploding and saving the values since i was imploded at the time of insertion//
                $software_list_temp = explode("###$$$###",$profile_contribinfo[0]['software_list']);
                for($i=0;$i<count($software_list_temp);$i++){
                    $software_list[$i] = explode('|',$software_list_temp[$i]);
                }
                $this->_view->software_list = $software_list;
                $this->_view->software_list_count = count($software_list);//saving the count for later use in phtml file//

                /*end of added by naseer on 04-11-2015 */

              //Published articles
              $publishedArticles=$this->publishedArticles();
              if(count($publishedArticles)>0)
              {
                  $publishedClients=array();
                  foreach($publishedArticles as $particle)
                  {
                    $publishedClients[$particle['clientId']]=$particle;
                  }

                    //$publishedClients=array_unique($publishedClients);
                  $this->_view->publishedClients=$publishedClients;

              }


              $this->_view->meta_title="Author private profile";
              $this->render("Contrib_private_profile");
          }
          else
             $this->_redirect("/contrib/modify-profile");
              
      } 
    
  }
  /*Upload cv*/
    public function uploadCvAction()
    {
      
      //setlocale(LC_TIME, "fr_FR");
      if($this->_helper->EpCustom->checksession())
      {
        $profileplus_obj = new Ep_Contrib_ProfilePlus();
        $profileContrib_obj = new Ep_Contrib_ProfileContributor();
        $profile_identifier_info=$profileplus_obj->checkProfileExist($this->contrib_identifier);
        if($profile_identifier_info!='NO')
        {
          error_reporting(E_ERROR | E_PARSE);
          $path=pathinfo($_FILES['cv_file']['name']);
          $uploadCv=$_FILES['cv_file']['name'];
          $ext=$path['extension'];//$this->findexts($uploadpicname);
         
          $contrib_identifier= $this->contrib_identifier;
          $app_path=APP_PATH_ROOT;
          $profiledir=$this->_config->path->contrib_cv_path.$contrib_identifier.'/';
          $uploadCvdir = $app_path.$profiledir;
          if(!is_dir($uploadCvdir))
          mkdir($uploadCvdir,TRUE);
          chmod($uploadCvdir,0777);
          $contrib_cv=$uploadCvdir.$contrib_identifier.".".$ext;
              
          if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $contrib_cv))
          {
              $cv_data['cv_uploaded_at']=date("Y-m-d H:i:s");
              $cv_data['cv_file']=$contrib_identifier.".".$ext;
              $profileContrib_obj->updateprofile($cv_data,$contrib_identifier);
              $published=utf8_encode(strftime("%d %B %Y",strtotime(date("Y-m-d"))));
              $array=array("status"=>"success","published"=>"$published");
              echo json_encode($array);    
          }
        }    
      }  
    }            
    /**downlaod file Action**/
    public function downloadFileAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
           $contrib_identifier=$this->contrib_identifier;
           
           $fileParams=$this->_request->getParams();
           $article_id=$fileParams['article_id'];
           $royalty=new Ep_Royalty_Royalties();
           $poll_obj=new Ep_Poll_Participation();
           $attachment=new Ep_Ticket_Attachment();
           $message=new Ep_Ticket_Message();
           $profileContrib_obj = new Ep_Contrib_ProfileContributor();
           $profileplus_obj = new Ep_Contrib_ProfilePlus();
            $delivery=new Ep_Article_Delivery();
           $root_path=APP_PATH_ROOT;
           $file_type=$fileParams['type'];


           if($file_type == 'guide_mission_liberte')
           {
              $file_path=$root_path."/brief_sample/Guide_Mission_liberte.docx";             
              $file_name="Guide_Mission_liberte.doc";
           }

           elseif($file_type == 'cv')
           {
              $info=$profileContrib_obj->getProfileInfo($contrib_identifier);
              $file_path=$root_path.$this->_config->path->contrib_cv_path.$contrib_identifier.'/'.$info[0]['cv_file'];
              $file_info=pathinfo($file_path);
              
              $contrib_info=$profileplus_obj->getProfileInfo($contrib_identifier);
              $file_name=$contrib_info[0]['first_name']."-".$contrib_info[0]['last_name'].".".$file_info['extension'];
           }
           else if($file_type == 'clientbrief' && $article_id!='' )
           {
              $articleDetails=$delivery->getArticleBrief($article_id);

              $specfiles=explode("|",$articleDetails[0]['filepath']);
              if(count($specfiles)>1)
              {

                  $delivery_name=str_replace(" ","_",trim($articleDetails[0]['delivery']));
                  $delivery_name=str_replace("__ ","_",$delivery_name);

                  foreach($specfiles as $sfile)
                  {
                      $files_array[]=SPEC_FILE_PATH. $sfile;    
                   } 
                  

                  $file_path=SPEC_FILE_PATH."/".$articleDetails[0]['client']."/".$delivery_name.".zip";
                  //echo $file_path;exit;
                  if(file_exists($file_path))
                  unlink($file_path);
                  $result = create_zip($files_array,$file_path,true);
              }
              else              
                $file_path=SPEC_FILE_PATH. $articleDetails[0]['filepath'];   
              //set cookie that user has downloaded the brief
              $delivery_id=$articleDetails[0]['delivery_id'];


              setcookie("client_brief_".$delivery_id,1,NULL,"/");    
           }
           else if($file_type == 'pollbrief' && $article_id!='' )
           {
              $searchParameters['profile_type']=$this->profileType;
              $searchParameters['pollid']=$article_id;
               $searchParameters['req_from']='ongoing';
              $pollDetails=$poll_obj->getPollDetails($searchParameters);  
              $file_path=POLL_SPEC_FILE_PATH."/".$pollDetails[0]['file_name']; 

			    //set cookie that user has downloaded the brief
              setcookie("poll_brief_".$article_id,1,NULL,"/");	
           }
          else if($file_type == 'viewattachment' && $fileParams['attachment']!='')
           {
              $messageId=$fileParams['attachment'];
              
              if(($file=$message->getAttachmentName( $messageId))!=NULL)
              {
                  if($mail_params['index'])
                      $index=$mail_params['index'];
                  else
                      $index=0;
                  $view_files=explode("|",$file[0]['attachment']);
                  $file[0]['attachment']=$view_files[$index];
                  $file_path=$this->attachment_path.$file[0]['attachment'];
              }    
           } 
           else if($file_type == 'correctionbrief')
           {
              $delivery=new Ep_Article_Delivery();
              $partcipation=new Ep_Participation_CorrectorParticipation();
              $articleDetails=$delivery->getCorrectorArticleBrief($article_id);
              $client_spec_full_path=SPEC_FILE_PATH. $articleDetails[0]['filepath'];
              $corrector_spec_full_path=CORRECTOR_SPEC_FILE_PATH. $articleDetails[0]['correction_file'];
              $article_path=$partcipation->getArticlePath($article_id);
              $article_path=$this->articles_path.$article_path;
              
              if((file_exists($client_spec_full_path) && $articleDetails[0]['filepath']!=NULL && !is_dir($client_spec_full_path)) OR
                (file_exists($corrector_spec_full_path) && $articleDetails[0]['correction_file']!=NULL && !is_dir($corrector_spec_full_path))
                // )OR (file_exists($article_path) && !is_dir($article_path)
              )
              {
                  
                  //set cookie that user has downloaded the brief
                  $delivery_id=$articleDetails[0]['delivery_id'];
                 setcookie("correction_brief_".$delivery_id,1,NULL,"/");  
                                   
                 // setcookie("correction_brief_".$article_id, 1,NULL,"/");

                  $delivery_name=str_replace(" ","_",trim($articleDetails[0]['title']));
                  $delivery_name=str_replace("__ ","_",$delivery_name);
                    $delivery_name=str_replace("/","_",$delivery_name);
                  $files_array[0]=$client_spec_full_path;
                  $files_array[1]=$corrector_spec_full_path;
                  //$files_array[2]=$article_path;
                  $filename=CORRECTOR_SPEC_FILE_PATH."/".$delivery_name.".zip";
                  //echo $filename;exit;
                  if(file_exists($filename))
                  unlink($filename);
                  $result = create_zip($files_array,$filename,true);
                  /*header('Content-Description: File Transfer');
                  header('Content-Type: application/octet-stream');
                  header('Content-Disposition: attachment; filename='.basename($filename));
                  header('Content-Transfer-Encoding: binary');
                  header('Expires: 0');
                  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                  header('Pragma: public');
                  header('Content-Length: ' . filesize($filename));*/
                 /* header("Content-Transfer-Encoding: binary");
                  header("Expires: 0");
                  header("Pragma: private");
                  header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
                  header("Content-Type: application/force-download");
                  header("Accept-Ranges: bytes");
                  header('Content-Disposition: attachment; filename='.basename($filename));
                  header('Content-Length: ' . filesize($filename));                  
                  readfile($filename);*/
                  $attachment->downloadAttachment($filename,"attachment",basename($filename));
                  exit;
              }
              else
              {
                  echo "File Not Found";
                  exit;
              }
           }
            else if($file_type == 'recruitment')
            {
                $recruitmentIdentifier=$fileParams['recruitment_id'];
                $articleDetails=$delivery->getArticleBrief($recruitmentIdentifier);
				//echo "<pre>";print_r($articleDetails);exit;
                $specfiles=explode("|",$articleDetails[0]['recruitment_file_path']);
				//echo count($specfiles); 
				$file_path = SPEC_FILE_PATH."/".$articleDetails[0]['recruitment_file_path'];
				if(file_exists($file_path))
				{
					$attachment->downloadAttachment($file_path,"attachment",$articleDetails[0]['delivery']);
					exit;
				}
                if(count($specfiles)>0)
                {
                    $delivery_name=str_replace(" ","_",trim($articleDetails[0]['delivery']));
                    $delivery_name=str_replace("__ ","_",$delivery_name);
                    foreach($specfiles as $sfile)
                    {
                        $files_array[]=SPEC_FILE_PATH. $sfile;
                    }
                    $file_path=SPEC_FILE_PATH."/".$articleDetails[0]['client']."/".$delivery_name.".zip";
           //echo $file_path;exit;
                    if(file_exists($file_path))
                        unlink($file_path);
                    $result = create_zip($files_array,$file_path,true);
                }
                $recruitObj=new Ep_Recruitment_Participation();
                $recruitmentDetails=$recruitObj->getRecruitmentDetails($recruitmentIdentifier);
                if($this->profileType=='senior')
                {
                    if($recruitment['senior_time'])
                        $submit_time=$recruitment['senior_time'];
                    else
                        $submit_time=$this->config['sc_time'];
                }
                else if($this->profileType=='junior')
                {
                    if($recruitment['junior_time'])
                        $submit_time=$recruitment['junior_time'];
                    else
                        $submit_time=$this->config['jc_time'];
                }
                else if($this->profileType=='sub-junior')
                {
                    if($recruitment['subjunior_time'])
                        $submit_time=$recruitment['subjunior_time'];
                    else
                        $submit_time=$this->config['jco_time'];
                }
                //update test article submit time
                if(!$recruitmentDetails[0]['article_submit_expires'])
                {
                    $participationId=$recruitmentDetails[0]['recruitmentParticipationId'];
                    $upload_time= $submit_time*60*60;
                    $article_submit_expires=time()+$upload_time;
                    $updateRecruitArray['article_submit_expires']=$article_submit_expires;
                    $recruitObj->updateParticipation($updateRecruitArray,$participationId);
                }
                //exit;
                if($recruit_brief_path)
                    $file_path=$this->_config->path->recruitment_brief.$recruit_brief_path;
                $pathinfo = pathinfo($file_path);
            }
            //echo $file_path;exit;
            if(file_exists($file_path) && !is_dir($file_path))
            {
              $attachment->downloadAttachment($file_path,"attachment",$file_name);
            }
            else
              echo "File Not found";
            
        }
    }
  /**Modify or Create Profile**/
  
  public function modifyProfileAction()
  {
        setlocale(LC_TIME, "fr_FR");
        if($this->_helper->EpCustom->checksession())
        {
            $profileplus_obj = new Ep_Contrib_ProfilePlus();
            $profileContrib_obj = new Ep_Contrib_ProfileContributor();
            if($this->_helper->FlashMessenger->getMessages()) {
                $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }
            $contrib_identifier=$this->contrib_identifier;           
            if($profileplus_obj->checkProfileExist($contrib_identifier)!='NO')
            {
               $profile_identifier_info=$profileplus_obj->checkProfileExist($contrib_identifier);
                $profile_identifier=$profile_identifier_info[0]['user_id'];
                $profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
                $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);

                $this->_view->email=$profileinfo[0]['email'];
                $this->_view->password=$profileinfo[0]['password'];
                $this->_view->subscribe=$profileinfo[0]['subscribe'];
                $this->_view->alert_subscribe=$profileinfo[0]['alert_subscribe'];


                $this->_view->civ=$profileinfo[0]['initial'];
                $this->_view->fname=$profileinfo[0]['first_name'];
                $this->_view->lname=$profileinfo[0]['last_name'];
                $this->_view->address=$profileinfo[0]['address'];
                $this->_view->city=$profileinfo[0]['city'];
                $this->_view->phonenumber=$profileinfo[0]['phone_number'];
                if($profileinfo[0]['country']==156)
				{
					$postcode=explode("|",$profileinfo[0]['zipcode']);
					$this->_view->zipcode1=$postcode[0];
					$this->_view->zipcode2=$postcode[1];
				}
				else
					$this->_view->zipcode=$profileinfo[0]['zipcode'];
					
                $this->_view->country=$profileinfo[0]['country'];
                $this->_view->dob=$profile_contribinfo[0]['dob'];
                $this->_view->profile_mother_language=$profile_contribinfo[0]['language'];
                //$this->_view->language_more=explode(",",$profile_contribinfo[0]['language_more']);
                $this->_view->language_more=unserialize($profile_contribinfo[0]['language_more']);
                $this->_view->category_more=unserialize($profile_contribinfo[0]['category_more']);
        
                $this->_view->nationality=$profile_contribinfo[0]['nationality'];
                $this->_view->category=explode(",",$profile_contribinfo[0]['favourite_category']);
                $this->_view->self_details=strip_tags($profile_contribinfo[0]['self_details']);
                $this->_view->payment_type=$profile_contribinfo[0]['payment_type'];
                /**cv path**/
                $cv_path=APP_PATH_ROOT.$this->_config->path->contrib_cv_path.$this->contrib_identifier.'/'.$profile_contribinfo[0]['cv_file'];
                if(!is_dir($cv_path) && file_exists($cv_path))
                  $this->_view->cv_exists='yes';
                /**iNOVICE inFO ***/
                $this->_view->pay_info_type=$profile_contribinfo[0]['pay_info_type'];

                $this->_view->entreprise=$profile_contribinfo[0]['entreprise'];

                $this->_view->SSN=$profile_contribinfo[0]['SSN'];
                $this->_view->company_number=$profile_contribinfo[0]['company_number'];
                $this->_view->vat_check=$profile_contribinfo[0]['vat_check'];
                $this->_view->VAT_number=$profile_contribinfo[0]['VAT_number'];
                /**Paypal and RIB info**/
                $this->_view->paypal_id=$profile_contribinfo[0]["paypal_id"] ;
                $RIB_ID=array_filter(explode("|",$profile_contribinfo[0]["rib_id"]));
                if(count($RIB_ID)==2)
                {
                    $this->_view->rib_id_6=$RIB_ID[0];
                    $this->_view->rib_id_7=$RIB_ID[1];
                }

                $this->_view->bank_account_name=$profile_contribinfo[0]['bank_account_name'];//bank account name
            }
            else
            {
                $user_obj = new Ep_User_User();
                $user_details=$user_obj->getContributorDetail($contrib_identifier);

                $this->_view->email=$user_details[0]['email'];
                $this->_view->password=$user_details[0]['password'];
                $this->_view->subscribe=$user_details[0]['subscribe'];
                $this->_view->alert_subscribe=$user_details[0]['alert_subscribe'];
				
				$this->_view->country=156;
				$this->_view->nationality=156;			 
             }
            
            /*added by naseer on 04-11-2015 */
            $software_array=$this->_arrayDb->loadArrayv2("EP_SOFTWARE_LIST", $this->_lang);
            //explodig all the software list and saing in multidimentaional array for later use//
            foreach($software_array as $k => $v){
                $software_array[$k] = explode("-",$v);
            }
            $this->_view->ep_software_array=$software_array;
            //exploding and saving the values since i was imploded at the time of insertion//
            $software_list_temp = explode("###$$$###",$profile_contribinfo[0]['software_list']);
            for($i=0;$i<count($software_list_temp);$i++){
                $software_list[$i] = explode('|',$software_list_temp[$i]);
            }
            $this->_view->software_list = $software_list;
            $this->_view->software_list_count = count($software_list);//saving the count for later use in phtml file//

            /*end of added by naseer on 04-11-2015 */
            /*added by naseer on 26.11.2015*/
            $this->_view->writer_preference=$profile_contribinfo[0]["writer_preference"] ;
            $this->_view->translator=$profile_contribinfo[0]["translator"] ;
            $this->_view->twitter_id=$profile_contribinfo[0]["twitter_id"] ;
            $this->_view->facebook_id=$profile_contribinfo[0]["facebook_id"] ;
            $this->_view->website=$profile_contribinfo[0]["website"] ;
            /* end of added by naseer on 31-07-2015 */
            $profession_array=$this->_arrayDb->loadArrayv2("CONTRIB_PROFESSION", $this->_lang);
            //natsort($profession_array);
            $categories_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
            natsort($categories_array);
            $language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
            natsort($language_array);
            $nationality_array=$this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
            natsort($nationality_array);
            $pays_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);
            natsort($pays_array);
           $this->_view->ep_professions_list=$profession_array;
            $this->_view->ep_language_list=$language_array;
            $this->_view->ep_categories_list=$categories_array;
            $this->_view->ep_nationality_list=$nationality_array;
            $this->_view->ep_pays_list=$pays_array;
      
            /**profile IMage*/
            $this->_view->profile_image=$this->getPicPath($this->contrib_identifier,'profile');
            
            
            /**getting User expeience details**/
            $experience_obj=new Ep_Contrib_Experience();
            $jobDetails=$experience_obj->getExperienceDetails($contrib_identifier,'job');
            if($jobDetails!="NO")
              $this->_view->jobDetails=$jobDetails;
              $educationDetails=$experience_obj->getExperienceDetails($contrib_identifier,'education');
            if($educationDetails!="NO")
              $this->_view->educationDetails=$educationDetails; 
            
              $this->_view->meta_title="Author profile";
              $this->render("Contrib_modify_profile");
        }   
  }
  public function saveProfileAction() 
  {
    if($this->_helper->EpCustom->checksession())
    {
       if($this->_request-> isPost())            
         {        
                $profile_params=$this->_request->getParams();
                //echo "<pre>";print_r($profile_params);exit;
                //Added w.r.t new fee
                $profile_params["payinfo"]='ep_admin';


                $profileplus_obj = new Ep_Contrib_ProfilePlus();
                $profileContrib_obj = new Ep_Contrib_ProfileContributor();
                $experience_obj=new Ep_Contrib_Experience();
                $contrib_identifier= $this->contrib_identifier; 
                $profileplus_obj->user_id=$contrib_identifier;
                $profileplus_obj->initial=$profile_params["initial"] ;
                $profileplus_obj->first_name=$this->utf8dec($profile_params["first_name"]) ;
                $profileplus_obj->last_name=$this->utf8dec($profile_params["last_name"]) ;
                $profileplus_obj->address=$this->utf8dec($profile_params["address"] );
                $profileplus_obj->city=$this->utf8dec($profile_params["city"] );
                $profileplus_obj->phone_number=$profile_params["phone_number"] ;
                if($profile_params["country"]==156)
                  $profileplus_obj->zipcode=$profile_params["zipcode1"]."|". $profile_params["zipcode2"];
                else
                  $profileplus_obj->zipcode=$profile_params["zipcode"];
                $profileplus_obj->country=$profile_params["country"] ;
                $profileContrib_obj->user_id=$contrib_identifier;
                $profileContrib_obj->dob=$profile_params["Date_Year"]."-".sprintf("%02d",$profile_params["Date_Month"])."-".sprintf("%02d",$profile_params["Date_Day"]) ;
                $profileContrib_obj->profession=$profile_params["profession"] ;
                $profileContrib_obj->profession_other=$this->utf8dec($profile_params["profession_other"]) ;
                $profileContrib_obj->university=$this->utf8dec($profile_params["university"]) ;
                $profileContrib_obj->education=$this->utf8dec($profile_params["edulevel"]) ;
                $profileContrib_obj->degree=$this->utf8dec($profile_params["graduation"]) ;
                $profileContrib_obj->language=$profile_params["language"] ;
                
        
                if(count($profile_params['language_more'])>0)       
                {
                  $languages_more=$profile_params['language_more'];         
                  $languages_sliders_more=$profile_params['lang_slider_more'];
                    foreach($languages_more as $key=>$language)         
                    {           
                      if($language)             
                        $moreLanguages[$language]=str_replace("%","",$languages_sliders_more[$key]);
                    }       
                        
                    $profileContrib_obj->language_more=serialize($moreLanguages);             
                } 
                if(count($profile_params['ep_category'])>0)   
                {         
                  $category_more=$profile_params['ep_category'];    
                  $category_sliders_more=$profile_params['category_slider_more'];
                  foreach($category_more as $key=>$category)        
                  {         
                    if($category)       
                      $moreCategories[$category]=str_replace("%","",$category_sliders_more[$key]);
                  }       
                  
                  $profileContrib_obj->category_more=serialize($moreCategories);
                  $profileContrib_obj->favourite_category=implode(",",$profile_params["ep_category"]);          
                }         
                /* added by naseer on 04-11-2015*/
                $software_name=$profile_params['software_name'];
                $software_level=$profile_params['software_level'];
                $software_own=$profile_params['software_own'];
                $software_list = '';
                foreach($software_name as $key=>$name)
                {
                    if($name)
                        $software_list .=  $software_name[$key].'|'.$software_level[$key].'|'.$software_own[$key]."###$$$###";
                }
                $profileContrib_obj->software_list=$software_list;

                /* end of added by naseer on 04-11-2015*/
                $profileContrib_obj->nationality=$profile_params["nationality"] ;
                $profileContrib_obj->self_details=nl2br($this->utf8dec($profile_params["self_details"]));
                $profileContrib_obj->payment_type=$profile_params["payment_type"];
                /**Inserting Pay info details**/
                $profileContrib_obj->pay_info_type=$profile_params["payinfo"] ;

                $profileContrib_obj->entreprise=$profile_params['entreprise'];

                if($profile_params["payinfo"]!='ep_admin')
                {
                  $profileContrib_obj->SSN=$profile_params["ssn"] ;
                  $profileContrib_obj->company_number=$profile_params["company_number"] ;
                  $profileContrib_obj->vat_check=$profile_params["vat_check"] ;
                  $profileContrib_obj->VAT_number=$profile_params["VAT_number"] ;
                }  
                /**Inserting Paypal and RIB info**/
                $profileContrib_obj->paypal_id=$profile_params["paypal_id"] ;
                if($profile_params["payment_type"]=='virement')
                {
                    $profileContrib_obj->rib_id=$this->utf8dec($profile_params["rib_id_6"])."|".$this->utf8dec($profile_params["rib_id_7"]);
                    $profileContrib_obj->bank_account_name=$this->utf8dec($profile_params["bank_account_name"]);
                }
                     
                $profileContrib_obj->staus_self_details_updated='no';
                if($profileplus_obj->checkProfileExist($contrib_identifier)!='NO')
                {
                    //contributor details
                    $contrib_info=$profileContrib_obj->getProfileInfo($contrib_identifier);
                    //echo "<pre>";print_r($contrib_info);exit;

                    $profile_identifier_info=$profileplus_obj->checkProfileExist($contrib_identifier);
                    $profile_identifier=$profile_identifier_info[0]['user_id'];
                    $profileplus_obj->user_id= $profile_identifier;
                    $profileContrib_obj->user_id= $profile_identifier;
                    //$profile_obj->updated_date= date("Y-m-d H:i:s", time());
                    $updatearray= $profileplus_obj->loadintoArray();
                    /*added by naseer on 09-11-2015*/
                    //fetch the old values saved in the datbase//
                    $old_profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
                    $this->updateuserlogs($old_profileinfo[0],$updatearray);
                    /*end of added by naseer on 09-11-2015*/
                    $profileplus_obj->updateprofile($updatearray,$profile_identifier);
                    //$profileContrib_obj->payment_type=$profile_identifier_info[0]["payment_type"];
                    /**updating staus_self_details_updated column when user updated details**/
                    $old_details=strip_tags(trim(strtolower($profile_identifier_info[0]['self_details'])));
                    $new_details=$this->utf8dec(trim(strtolower($profile_params["self_details"])));
                    if(strcmp($old_details,$new_details))
                    {
                       $profileContrib_obj->staus_self_details_updated='yes';
                    }
                     else
                    {
                        if($profile_identifier_info[0]['staus_self_details_updated'])
                          $profileContrib_obj->staus_self_details_updated=$profile_identifier_info[0]['staus_self_details_updated'];
                        else  
                          $profileContrib_obj->staus_self_details_updated='no';
                    }
                    $profileContrib_obj->writer_preference = ($profile_params['writer_preference'] === 'on') ? 'yes' : 'no';
                    $profileContrib_obj->translator = ($profile_params['translator'] === 'on') ? 'yes' : 'no';
                    $profileContrib_obj->twitter_id = $profile_params['twitter_id'];
                    $profileContrib_obj->facebook_id = $profile_params['facebook_id'];
                    $profileContrib_obj->website = $profile_params['website'];
                    $profileContrib_obj->updated_at=date('Y-m-d h:i:s');
                    $updatearray_contrib= $profileContrib_obj->loadintoArray();
                    //print_r($updatearray_contrib);echo $profile_identifier;exit;
                    if($profile_identifier_info[0]['contributor'])
                    {                      
                        /*added by naseer on 09-11-2015*/
                        //fetch the old values saved in the datbase//
                        $old_profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);
                        $this->updateuserlogs($old_profile_contribinfo[0],$updatearray_contrib);
                        /*end of added by naseer on 09-11-2015*/
                      $profileContrib_obj->updateprofile($updatearray_contrib,$profile_identifier);
                    } 
                    else
                    {         
                      $profileContrib_obj->insert();              
                    } 
                    
                    /**Inserting or Updating User Experince**/
                    $profile_params['contrib_identifier']=$contrib_identifier;
                    $this->updateExperienceDetails($profile_params,'job');
                    $this->updateExperienceDetails($profile_params,'education');        
                
          
          
                    $profile_percentage=$this->calculateProfilePercentage();
                    $profile_update['profile_percentage']=$profile_percentage;
                    $profileContrib_obj->updateprofile($profile_update,$contrib_identifier);


                    //seding email if writer update bank details                    

                    $old_rib_id=$contrib_info[0]['rib_id'];
                    $new_rib_id= $profileContrib_obj->rib_id;

                    $old_paypal_id=$contrib_info[0]['paypal_id'];
                    $new_paypal_id= $profileContrib_obj->paypal_id;
				
                   if((strcasecmp ($old_rib_id,$new_rib_id) && $new_rib_id) || ((strcasecmp ($old_paypal_id,$new_paypal_id) && $new_paypal_id) || ($old_rib_id && $new_paypal_id )))
				   {
						 //OLD Bank details
                        $old_bank_info=explode("|",$old_rib_id);
                        if(count($old_bank_info)<5)
                        {
                            $old_bank_code="BIC : ".$old_bank_info[0]."&nbsp;&nbsp;&nbsp;IBAN : ".$old_bank_info[1];  
                        }
                        else
                        {
                            $old_bank_code="Nom de l'&eacute;tablissement : ".$old_bank_info[0]."<br/>"; 
                            $old_bank_code.="Code Banque : ".$old_bank_info[1]."<br/>"; 
                            $old_bank_code.="Code Guichet : ".$old_bank_info[2]."<br/>"; 
                            $old_bank_code.="Num&eacute;ro de compte : ".$old_bank_info[3]."<br/>"; 
                            $old_bank_code.="Cl&eacute; RIB : ".$old_bank_info[4]."<br/>"; 

                        }
						
						if($contrib_info[0]['payment_type']=="paypal")
							$parameters['old_bank_info']="Paypal email : ".$old_paypal_id;
						elseif($contrib_info[0]['payment_type']=="virement")
							$parameters['old_bank_info']=$old_bank_code;
						else
							$parameters['old_bank_info']="None";
					
					   if((strcasecmp ($old_rib_id,$new_rib_id) && $new_rib_id) && $profile_params["payment_type"]=="virement")
						{
							//New Bank details
							$new_bank_info=explode("|",$new_rib_id);
							if(count($new_bank_info)<5)
							{
								$new_bank_code="BIC : ".$new_bank_info[0]."&nbsp;&nbsp;&nbsp;IBAN : ".$new_bank_info[1];  
							}
							else
							{
								$new_bank_code="Nom de l'&eacute;tablissement : ".$new_bank_info[0]."<br/>"; 
								$new_bank_code.="Code Banque : ".$new_bank_info[1]."<br/>"; 
								$new_bank_code.="Code Guichet : ".$new_bank_info[2]."<br/>"; 
								$new_bank_code.="Num&eacute;ro de compte : ".$new_bank_info[3]."<br/>"; 
								$new_bank_code.="Cl&eacute; RIB : ".$new_bank_info[4]; 

							}           
							
							$mail_params['old_bank_info']=$old_bank_code;
							$mail_params['new_bank_info']=$new_bank_code;
							$mail_params['contributor_name']=$this->_view->client_email;
							$mail_params['email_address']=$this->EP_Contrib_reg->clientemail;

							//$send_email='arunravuri@edit-place.com';
							//$send_email='comptabilite@edit-place.com';
                            $send_email='bo-test@edit-place.com';
							
							$autoEmail=new Ep_Ticket_AutoEmails();
							$autoEmail->messageToEPMail($send_email,106,$mail_params,'out_editplace');
							
							//Mail writer
							$parameters['writeremail_address']=$this->EP_Contrib_reg->clientemail;
							$parameters['new_bank_info']=$new_bank_code;
							$autoEmail->messageToEPMail($this->contrib_identifier,191,$parameters); 
						}  
						else if(((strcasecmp ($old_paypal_id,$new_paypal_id) && $new_paypal_id) || ($old_rib_id && $new_paypal_id )) && $profile_params["payment_type"]=="paypal") 
						{
							$mail_params['contributor_name']=$this->_view->client_email;
							$mail_params['email_address']=$this->EP_Contrib_reg->clientemail;
							$mail_params['paypal_emailaddress']=$new_paypal_id;
							//$send_email='arunravuri@edit-place.com';
							//$send_email='comptabilite@edit-place.com';
							$send_email='bo-test@edit-place.com';
							
							$autoEmail=new Ep_Ticket_AutoEmails();
							$autoEmail->messageToEPMail($send_email,129,$mail_params,'out_editplace');
							
							//Mail writer
							$parameters['writeremail_address']=$this->EP_Contrib_reg->clientemail;
							$parameters['new_bank_info']="Paypal email : ".$new_paypal_id;
							$autoEmail->messageToEPMail($this->contrib_identifier,191,$parameters);
						} 
					}
                 
                    //echo $old_rib_id."--".$new_rib_id;exit;
                    //update password
                    if(($profile_params["password"]==$profile_params["password2"]) && strlen(trim($profile_params["password"]))>=6)
                    {
                        
                        $update_user['password']=$profile_params["password"];

                    }
                    //update subscription details
                    $update_user['subscribe']=$profile_params['subscribe'] ? 'yes' : 'no';
                    $update_user['alert_subscribe']=$profile_params['alert_subscribe'] ? 'yes' : 'no';
                    
                    $user_obj = new Ep_User_User();

                    $query = "identifier= '".$contrib_identifier."'";
                    
                    /*added by naseer on 09-11-2015*/
                    //fetch the old values saved in the datbase//
                    $old_profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
                    $this->updateuserlogs($old_profileinfo[0],$update_user);
                    /*end of added by naseer on 09-11-2015*/
                    $user_obj->updateUser($update_user,$query);                  

                    



                    $this->_helper->FlashMessenger('Your profile has been successfully updated');

                 }
                else
                {
                    try
                    {
                        
                         
                        // exit;
                        if($profileplus_obj->insert())
                        {
                                            $profileContrib_obj->insert();
                            /**Inserting or Updating User Experince**/
                            $profile_params['contrib_identifier']=$contrib_identifier;
                            $this->updateExperienceDetails($profile_params,'job');
                            $this->updateExperienceDetails($profile_params,'education');
                        }   
                        $profile_percentage=$this->calculateProfilePercentage();
                        $profile_update['profile_percentage']=$profile_percentage;
                        $profileContrib_obj->updateprofile($profile_update,$contrib_identifier);


                        //update password
                        if(($profile_params["password"]==$profile_params["password2"]) && strlen(trim($profile_params["password"]))>=6)
                        {
                            
                            $update_user['password']=$profile_params["password"];

                        }
                        //update subscription details
                        $update_user['subscribe']=$profile_params['subscribe'] ? 'yes' : 'no';
                        $update_user['alert_subscribe']=$profile_params['alert_subscribe'] ? 'yes' : 'no';
                        
                        $user_obj = new Ep_User_User();

                        $query = "identifier= '".$contrib_identifier."'";
                        
                        $user_obj->updateUser($update_user,$query);

                        

                        $this->_helper->FlashMessenger('Your profile has been successfully created');

                    }
                    catch(Zend_Exception $e)
                    {
                        echo $e->getMessage();exit;
                        $this->_view->error_msg =$e->getMessage()." D&eacute;sol&eacute;! Mise en erreur.";
                        $this->render("EP_Contrib_Profile");
                        exit;
                     }
                }               
                /*added by naseer on 05-11-2015*/
                //this function will check if values have been edited and insert into userLogs table *if any//
                /*$profileplus_data = $profileplus_obj->loadintoArray();
                $profileContrib_data = $profileContrib_obj->loadintoArray();
                $this->updateUserLogs($profileplus_data,$profileContrib_data);*/
                //exit;
                if($this->selected_ao_count)
                    $this->_redirect("/cart/cart-selection");
                else
                    $this->_redirect("/contrib/modify-profile");
                /*end of added by naseer on 05-11-2015*/
         }  
      }
  }
    /* added by naseer on 05-11-2015*/
    //this function will check if values have been edited and insert into userLogs table *if any//
    public function updateUserLogs($old_data,$new_data){
        $updated_by = "self";
        //arrays that will be used for log_type field//
        $profile = array('password', 'initial', 'first_name', 'last_name', 'dob', 'language', 'address', 'city', 'country', 'nationality', 'zipcode', 'phone_number','self_details','passport_no','id_card','writer_preference','translator','twitter_id','facebook_id','website');//edited by naseer on 26.11.2015//
        $language_update = array('language_more');
        $category_update = array('category_more');
        $job_edu_update = array('title', 'institute', 'contract', 'from_month', 'from_year', 'still_working', 'to_month', 'to_year');
        $skill_update = array('software_list');
        $company_entrepreneur_update = array('tav_number','com_name','com_address','com_city','com_zipcode','com_country','com_siren','com_tva_number','com_phone');
        $payment = array('paypal_id','bank_account_name','virement','rib_id');
        $payment_type = array('payment_type','options_flag');
        $subscription = array('alert_subscribe','subscribe');
        // end arrays that will be used for log_type field//
        $userlogs_obj = new Ep_User_UserLogs();

        foreach($new_data  as $key =>  $value) {

            // enterprise/updated_at doesnt matter if its chained//
                if ($old_data[$key] !== $value && $key !== 'updated_at' && $key !== 'entreprise' && $key !== 'identifier' && $key !== 'staus_self_details_update') {
                    if(in_array($key, $profile))
                        $log_type = 'profile';
                    elseif( in_array($key,$language_update))
                        $log_type = 'language_update';
                    elseif( in_array($key,$category_update))
                        $log_type = 'category_update';
                    elseif( in_array($key,$job_edu_update)){
                        if($new_data['type'] === 'job' )
                            $log_type = 'job_update';
                        elseif($new_data['type'] === 'education')
                            $log_type = 'edu_update';
                    }
                    elseif( in_array($key,$skill_update))
                        $log_type = 'skill_update';
                    elseif( in_array($key,$company_entrepreneur_update))
                        $log_type = 'company_entrepreneur_update';
                    elseif( in_array($key,$payment))
                        $log_type = 'payment';
                    elseif( in_array($key,$payment_type))
                        $log_type = 'payment_type';
                    elseif( in_array($key,$subscription))
                        $log_type = 'subscription';
                    else
                        $log_type = 'other';
                    //fetch user type from user table //
                    $user_obj = new Ep_User_User();
                    $user_type = $user_obj->getUserType($old_data['user_id']);
                    $data = array("user_id" => $old_data['user_id'], "type" => $user_type, "old_value" => utf8dec($old_data[$key]), "new_value" => utf8dec($value), "log_type" => $log_type, "field" => $key,"updated_by"=>$updated_by);
                    $userlogs_obj->InsertLogs($data);
                    /*echo "inserted: ======>>>";
                    echo "Key: $key; old value : $old_data[$key] ;new Value: $value<br /><br />\n";*/
                }
            }
    }
    /* end of added by naseer on 05-11-2015*/
  /**Inserting or Updating User Experince**/
  public function updateExperienceDetails($profile_params,$type)
  {
    /**Inserting or Updating User Experince**/
    $contrib_identifier=$profile_params['contrib_identifier'];
    if($type=='job')
      $details=$profile_params['job_title'];
    else if($type=='education') 
      $details=$profile_params['training_title'];
    if(count($details)>0)
    {
      foreach($details as $key=>$title)
      {
        $experience_obj=new Ep_Contrib_Experience();    
        
        if($type=='job')
        {
          $institute=$this->utf8dec($profile_params['job_institute'][$key]);
          $contract=$profile_params['ep_job'][$key];
          $start_month=$profile_params['start_month'][$key];
          $start_year=$profile_params['start_year'][$key];
          $end_month=$profile_params['end_month'][$key];
          $end_year=$profile_params['end_year'][$key];
          $still_working=$profile_params['still_working'][$key];
          $job_identifier=$profile_params['job_identifier'][$key];
          
          $condition=$title && $institute && $contract && $start_month && $start_year && (($end_month && $end_year)||$still_working);
        } 
        else if($type=='education')
        {
          $institute=$this->utf8dec($profile_params['training_institute'][$key]);
          $start_month=$profile_params['start_train_month'][$key];
          $start_year=$profile_params['start_train_year'][$key];
          $end_month=$profile_params['end_train_month'][$key];
          $end_year=$profile_params['end_train_year'][$key];
          $still_working=$profile_params['still_training'][$key];
          $contract='';
          $job_identifier=$profile_params['training_identifier'][$key];
          
          $condition=$title && $institute && $start_month && $start_year && (($end_month && $end_year)||$still_working);
        }   
        
        if($condition)
        {
          $experience_obj->user_id=$contrib_identifier;
          $experience_obj->title=$this->utf8dec($title);
          $experience_obj->institute=$institute;
          $experience_obj->contract=$contract;
          $experience_obj->type=$type;
          $experience_obj->from_month=$start_month;
          $experience_obj->from_year=$start_year;
          if($still_working)
          {
            $experience_obj->still_working='yes';
                        $experience_obj->to_month='0';
                        $experience_obj->to_year='0';
          } 
          else
          {
            $experience_obj->to_month=$end_month;
            $experience_obj->to_year=$end_year;
            $experience_obj->still_working='no';
          } 
          
          if($job_identifier)
          {
            $experience_obj->updated_at=date('Y-m-d h:i:s');
            $updateExperienceArray= $experience_obj->loadintoArray();
            
                        /*added by naseer on 09-11-2015*/
                        //fetch the old values saved in the datbase//
                        $jobDetails=$experience_obj->getIndividualExperienceDetails($job_identifier,$type);
                        $this->updateuserlogs($jobDetails[0],$updateExperienceArray);
                        /*end of added by naseer on 09-11-2015*/
            $experience_obj->updateExperience($updateExperienceArray,$job_identifier);
          }               
          else
          {
            
            try
            {
              $experience_obj->insert();
            }
            catch(Zend_Exception $e)
            {
              echo $e->getMessage();exit;
              $this->_view->error_msg =$e->getMessage()." D&eacute;sol&eacute;! Mise en erreur.";
              $this->render("EP_Contrib_Profile");
              exit;
            }
            
          } 
        }   
      }
    }
  
  }
  /*Upload Profile Photo*/
    public function uploadprofilepicAction()
    {
        error_reporting(E_ERROR | E_PARSE);
        $path=pathinfo($_FILES['uploadpic']['name']);
        $uploadpicname=$_FILES['uploadpic']['name'];
    $ext="jpg";//$path['extension'];//$this->findexts($uploadpicname);
       
        $contrib_identifier= $this->contrib_identifier;
        $app_path=APP_PATH_ROOT;
        $profiledir=$this->_config->path->contrib_profile_pic_path.$contrib_identifier.'/';
        $uploadpicdir = $app_path.$profiledir;
      if(!is_dir($uploadpicdir))
      mkdir($uploadpicdir,TRUE);
    chmod($uploadpicdir,0777);
    $contrib_picture=$uploadpicdir.$contrib_identifier.".".$ext;
        $contrib_picture_home= $uploadpicdir.$contrib_identifier."_h.".$ext;
        $contrib_picture_profile= $uploadpicdir.$contrib_identifier."_p.".$ext;
        $contrib_picture_offer= $uploadpicdir.$contrib_identifier."_ao.".$ext;
        $contrib_picture_crop= $uploadpicdir.$contrib_identifier."_crop.".$ext;
       list($width, $height)  = getimagesize($_FILES['uploadpic']['tmp_name']);
       if($width>=90 && $height>=90)
       {
            if (move_uploaded_file($_FILES['uploadpic']['tmp_name'], $contrib_picture))
            {
                chmod($contrib_picture,0777);
        /**Image for cropping**/
                $newimage_crop= new EP_Contrib_Image();
                $newimage_crop->load($contrib_picture);
                list($width, $height) = getimagesize($contrib_picture);
                if($width>400)
                    $newimage_crop->resizeToWidth(400);
                elseif($height>600)
                    $newimage_crop->resizeToHeight(600);
                else
                    $newimage_crop->resize($width,$height);
                $newimage_crop->save($contrib_picture_crop);
                chmod($contrib_picture_crop,0777);
                $array=array("status"=>"success","identifier"=>$contrib_identifier,"path"=>$profiledir,"ext"=>$ext);
                echo json_encode($array);
                
            }
            else
            {
                $array=array("status"=>"error"  );
                echo json_encode($array);
             }
       }
       else
       {
            $array=array("status"=>"smallfile"  );
            echo json_encode($array);
       }
    }
  /**Cropping Profile images**/
    public function cropprofilepicAction()
    {
      if($this->_request-> isPost())
      {
        $image_params=$this->_request->getParams();
        $function=$image_params['function'];
    $new_x=$image_params['x'];
    $new_y=$image_params['y'];
    $post_width=$image_params['w'];
    $post_height=$image_params['h'];    
        
    $contrib_identifier= $this->contrib_identifier;
        $ext="jpg";
        $app_path=APP_PATH_ROOT;
        
    $profiledir=$this->_config->path->contrib_profile_pic_path.$contrib_identifier.'/';
        $uploadpicdir = $app_path.$profiledir;
        $contrib_picture_home= $uploadpicdir.$contrib_identifier."_h.".$ext;
        $contrib_picture_profile= $uploadpicdir.$contrib_identifier."_p.".$ext;
        $contrib_picture_offer= $uploadpicdir.$contrib_identifier."_ao.".$ext;
        $contrib_picture=$uploadpicdir.$contrib_identifier.".".$ext;
        $contrib_picture_crop= $uploadpicdir.$contrib_identifier."_crop.".$ext;
       if($function=="saveimage")
       {
         /*Contrib home image with 60x60**/
            $newimage_h= new EP_Contrib_Image();
            $newimage_h->load($contrib_picture_crop);
      $newimage_h->cropImage($new_x,$new_y,60,60,$post_width,$post_height);
            $newimage_h->save($contrib_picture_home);
           // chmod($contrib_picture_home,777);
            unset($newimage_h);
           /*Contrib Profile image with 90x90**/
            $newimage_p= new EP_Contrib_Image();
            $newimage_p->load($contrib_picture_crop);
      $newimage_p->cropImage($new_x,$new_y,90,90,$post_width,$post_height);
            $newimage_p->save($contrib_picture_profile);
            //chmod($contrib_picture_profile,777);
            unset($newimage_p);
            /*Contrib Profile image with width 90**/
            $newimage_p= new EP_Contrib_Image();
            $newimage_p->load($contrib_picture_crop);
            list($width, $height) = getimagesize($contrib_picture_crop);
            $ao_image_height=(($height/$width)*90);
            $newimage_p->cropImage($new_x,$new_y,90,$ao_image_height,$post_width,$post_height);
      $newimage_p->save($contrib_picture_offer);
            //chmod($contrib_picture_offer,777);
            unset($newimage_p);
         }
         elseif($function=="original")
         {
            /*Contrib home image with 60x60**/
            $newimage_h= new EP_Contrib_Image();
            $newimage_h->load($contrib_picture);
      $newimage_h->resize(60,60);
            $newimage_h->save($contrib_picture_home);
            //chmod($contrib_picture_home,0777);
            unset($newimage_h);
            /*Contrib Profile image with 90x90**/
            $newimage_p= new EP_Contrib_Image();
            $newimage_p->load($contrib_picture);
      $newimage_p->resize(90,90);
            $newimage_p->save($contrib_picture_profile);
           // chmod($contrib_picture_profile,0777);
            unset($newimage_h);
            $newimage_p= new EP_Contrib_Image();
            $newimage_p->load($contrib_picture);
            list($width, $height) = getimagesize($contrib_picture);
            $ao_image_height=(($height/$width)*90);
      $newimage_p->resize(90,$ao_image_height);
            $newimage_p->save($contrib_picture_offer);
            // chmod($contrib_picture_offer,0777);
             unset($newimage_p);
         }
           /**Unlink the Original file**/
            if(file_exists($contrib_picture) && !is_dir($contrib_picture))
                unlink($contrib_picture);
            $array=array("identifier"=>$contrib_identifier,"path"=>$profiledir,"ext"=>$ext);
            echo json_encode($array);
    }    
    }
  public function delteProfileDataAction()
  {
    if($this->_helper->EpCustom->checksession())
    {
      $profile_params=$this->_request->getParams();
      $experience_obj=new Ep_Contrib_Experience();
      
      if($profile_params['type'] && $profile_params['identifier'])
      {
        $identifier=$profile_params['identifier'];
        if($profile_params['type']=='education' || $profile_params['type']=='job')
        {
          $experience_obj->deleteExperience($identifier);
        }
      }     
    } 
  }
  /**Royalties details**/
  public function royaltiesAction()
   {
      if($this->_helper->EpCustom->checksession())
        {
            $ContributorIdentifier=$this->contrib_identifier;
            $royalty_obj=new Ep_Royalty_Royalties();
            $ticket_obj=new Ep_Ticket_Ticket();
			
			
			/*showing alert while requesting payment if already have unpaid invoices for that month*/
			$invoice_obj=new Ep_Royalty_Invoice();
			$unPaidEuroInvoices=$invoice_obj->getMonthlyUnpaidInvoices($ContributorIdentifier,'euro');
			$unpaidEuroCount=0;
			if($unPaidEuroInvoices)			
				$unpaidEuroCount=count($unPaidEuroInvoices);
			$this->_view->unpaidEuroCount=$unpaidEuroCount;
			
			$unPaidPoundInvoices=$invoice_obj->getMonthlyUnpaidInvoices($ContributorIdentifier,'pound');
			$unpaidPoundCount=0;
			if($unPaidPoundInvoices)			
				$unpaidPoundCount=count($unPaidPoundInvoices);
			$this->_view->unpaidPoundCount=$unpaidPoundCount;
            
            /**pending royalties*/
              $pending_royalties=$royalty_obj->getTotalRoyalty($ContributorIdentifier);
              $this->_view->pending_royalties=$pending_royalties;
            
            /**total user royalties*/
              $total_royalties=$royalty_obj->getTotalUserRoyalty($ContributorIdentifier);
              $this->_view->total_royalties=$total_royalties;
            
            $royalties=$royalty_obj->getAllRoyaltiesWithInvoice($ContributorIdentifier);
            
            if(count($royalties)>0)
            {
                
                foreach($royalties as $key=>$royalty)
                { 
                    if($royalty['pay_later_month'])
                        $royalties[$key]['pay_later_month_name']=strftime( '%B', strtotime( 'last day of +'.$royalty['pay_later_month'].' month', strtotime($royalty['invoicedate'])));
                    
                }


                $page = $this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($royalties);
                $paginator->setItemCountPerPage($this->config['pagination_fo']);
                $paginator->setCurrentPageNumber($page);
                //$this->_view->pagination=print_r($paginator->getPages(),true);
                //$patterns='/[? &]page=[\d{1,2}]/';
                $patterns='/[? &]page=[0-9]{1,2}/';
                $replace="";
                $this->_view->royalties = $paginator;
                $this->_view->pages = $paginator->getPages();
                $this->_view->pageURL=preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
            }
            /**unpaid royalties details**/
            $invoiceDetails=$royalty_obj->getInvoiceDetails($ContributorIdentifier);
            if(count($invoiceDetails)>0 && is_array($invoiceDetails))
            {
              $total=0;$i=0;
              foreach( $invoiceDetails as $details)
              {
                if($details['currency']=='pound')
					$po_total+=$details['price'];
				else	
					$eu_total+=$details['price'];
                $client_id= $details['client_id'];

                $invoiceDetails[$i]['client_name']=$ticket_obj->getUserName($client_id);
                $invoiceDetails[$i]['article_created_date']=ucfirst(strftime("%b %Y",strtotime($details['article_created_date'])));
                
                $i++;

                
              }
              $this->_view->unpaidEuroTotal=$eu_total;
			  $this->_view->unpaidPoundTotal=$po_total;
              $this->_view->unpaidRoyalties=$invoiceDetails;
            } 
            $this->_view->ep_contrib_min_amount=$this->config['contrib_minamount'];
            $this->_view->meta_title="Author royalties";
            $this->render("Contrib_royalties");            
        }
    }
  /**Invoice Details**/
  public function invoicedetailsAction()
  {
    
    if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
    {
      $invoiceParams=$this->_request->getParams();
	  $oldinvoice=$invoiceParams['oldinvoice'];
	  
	  /***Profile Info***/
      $profileplus_obj = new Ep_Contrib_ProfilePlus();
      $profileContrib_obj = new Ep_Contrib_ProfileContributor();
      $ticket_obj=new Ep_Ticket_Ticket();

      $contrib_identifier= $this->contrib_identifier;
      
      if($profileplus_obj->checkProfileExist($contrib_identifier)!='NO')
      {
        $profile_identifier_info=$profileplus_obj->checkProfileExist($contrib_identifier);
        $profile_identifier=$profile_identifier_info[0]['user_id'];
        $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);
        $profileplus_contribinfo=$profileplus_obj->getProfileInfo($profile_identifier);
      }
	  //echo "<pre>";print_r($_GET);
      /**ENDED**/
      $royalty=new Ep_Royalty_Royalties();
	  $invoiceId=$this->_request->getParam('invoiceid');
	  //Added w.r.t Currency
	  $currency=$this->_request->getParam('currency');
	  $this->_view->currency=$currency;
	  
	  
	  /*showing alert while requesting payment if already have unpaid invoices for that month*/
		$invoice_obj=new Ep_Royalty_Invoice();
		$unPaidInvoices=$invoice_obj->getMonthlyUnpaidInvoices($contrib_identifier,$currency);
		$unpaidCount=0;
		$unpaidInvoiceDetails=array();			
		if($unPaidInvoices)		
		{				
			$unpaidCount=count($unPaidInvoices);
			
			if($oldinvoice=='yes')
			{
				foreach($unPaidInvoices as $invoice)
				{
					$unpaidInvoiceDetails=$royalty->getInvoiceDetails($contrib_identifier,$invoice['invoiceId']);
				}
			}
		}
		$this->_view->unpaidCount=$unpaidCount;		
		//echo "<pre>";print_r($unpaidInvoiceDetails);exit;
		/*END*/
	  
	  
	  
      if($invoiceId)
      {
        $invoiceId=$this->_request->getParam('invoiceid');
        $invoiceId='ep_invoice_'.$invoiceId;
      }
      
		if($invoiceId || $currency)
		{			
			$invoiceDetails=$royalty->getInvoiceDetails($contrib_identifier,$invoiceId,$currency); 
			
			if($oldinvoice=='yes')
				$invoiceDetails=array_merge($invoiceDetails,$unpaidInvoiceDetails);
		
		  
            //echo "<pre>";print_r($invoiceDetails);exit;
		  if(count($invoiceDetails)>0 && is_array($invoiceDetails))
		  {
			  $total=0;$i=0;
			  foreach( $invoiceDetails as $details)
			  {
				$total+=$details['price'];

				$client_id= $details['client_id'];
				$invoiceDetails[$i]['client_name']=$ticket_obj->getUserName($client_id);
				$invoiceDetails[$i]['article_created_date']=ucfirst(strftime("%b %Y",strtotime($details['article_created_date'])));

				$i++;
			  }


			  if($invoiceDetails[0]['payment_info_type'])// && $invoiceDetails[0]['status']!='refuse')
			  {
				$profile_contribinfo[0]['pay_info_type']=$invoiceDetails[0]['payment_info_type'];
			  }
			  if($invoiceDetails[0]['vat_check'])//&& $invoiceDetails[0]['status']!='refuse')
			  {
				$profile_contribinfo[0]['vat_check']=$invoiceDetails[0]['vat_check'];
			  }
			  if($invoiceDetails[0]['payment_type'])//&& $invoiceDetails[0]['status']!='refuse')
				$profile_contribinfo[0]['payment_type']=$invoiceDetails[0]['payment_type'];
			  /**Total Invoice*/
			  $total=number_format($total,2,'.','');
			  $this->_view->totalInvoice=$total;
			  /**Tax Calculation */
			  $totalTax=0;
			  if($profile_contribinfo[0]['pay_info_type']=='ssn')
			  {
				$veuvage=number_format((($total*0.85)/100),2,'.','');
				$csg=number_format((($total*7.36875)/100),2,'.','');
				$crds=number_format((($total*0.49125)/100),2,'.','');
				$formation=number_format((($total*0.35)/100),2,'.','');
				if($invoiceDetails[0]['Invoice_date'] && $invoiceDetails[0]['status']!='refuse')
				{
				  $tax_date=date("Y-m-d",strtotime($invoiceDetails[0]['Invoice_date']));
				}
				else
				{
				  $tax_date=date("Y-m-d");
				}
				if($tax_date >= date("Y-m-d",strtotime('2012-07-01')))
				{
				  $this->_view->formation_display="yes";
				}
				else
				{
				  $formation=0;
				  $this->_view->formation_display="no";
				}
				$totalTax=$veuvage+$csg+$crds+$formation;
			  }
			  else if($profile_contribinfo[0]['pay_info_type']=='comp_num' && $profile_contribinfo[0]['vat_check']=='YES' )
			  {
				

					$TVA=number_format((($total*19.6)/100),2,'.','');
				
				$totalTax=$TVA;
			  }
			  else if($profile_contribinfo[0]['pay_info_type']=='ep_admin') //Added new tax ep_admin by default
			  {
				  
				  if($invoiceDetails[0]['Invoice_date'])
		                        $ep_admin_fee_percentage=$invoiceDetails[0]['ep_admin_fee_percentage'];
		                    else if($profile_contribinfo[0]['payment_type']=='paypal')
		                        $ep_admin_fee_percentage=0;
		                    else
		                        $ep_admin_fee_percentage=2;
				  
				  if($invoiceDetails[0]['Invoice_date'] && $invoiceDetails[0]['status']!='refuse')
				  {
					$tax_date=date("Y-m-d",strtotime($invoiceDetails[0]['Invoice_date']));
				  }
				  else
				  {
					$tax_date=date("Y-m-d");
				  }

				  $epTax=number_format((($total*$ep_admin_fee_percentage)/100),2,'.','');

				  $totalTax=$epTax;

                     $this->_view->ep_admin_fee_percentage=$ep_admin_fee_percentage;
			  } 
			  
			  //Added w.r.t new fees based on payment period
			  if($invoiceDetails[0]['pay_later_month'] || $invoiceDetails[0]['pay_later_percentage'] )//months
			  {
				$this->_view->pay_later_month=$invoiceDetails[0]['pay_later_month'];
				$this->_view->pay_later_percentage=$pay_later_percentage=$invoiceDetails[0]['pay_later_percentage'];

				$this->_view->period_fees=$period_fees=number_format(((($total-$totalTax)*$pay_later_percentage)/100),2,'.','');            

           $this->_view->pay_later_month_name=strftime( '%B', strtotime( 'last day of +'.$invoiceDetails[0]['pay_later_month'].' month', strtotime($invoiceDetails[0]['created_at']))); 

				setcookie("period_month",$invoiceDetails[0]['pay_later_month'],NULL,"/");
			  } 
			  else if($invoiceDetails[0]['status']!='refuse' && !$invoiceDetails[0]['invoiceId '])
			  {
				  $this->_view->pay_later_month=2;
				  $pay_later_percentage=0;
				  $this->_view->period_fees=$period_fees=number_format(((($total-$totalTax)*$pay_later_percentage)/100),2,'.','');              
				  setcookie("period_month",2,NULL,"/");
			  }

			  $this->_view->totalTax=$totalTax;
			  if($profile_contribinfo[0]['pay_info_type']=='ssn')
				$this->_view->FinaltotalInvoice=number_format(($total-$totalTax),2,'.','');
			  else if($profile_contribinfo[0]['pay_info_type']=='comp_num' && $profile_contribinfo[0]['vat_check']=='YES' )
				$this->_view->FinaltotalInvoice=number_format(($total+$totalTax),2,'.','');
			  else if($profile_contribinfo[0]['pay_info_type']=='ep_admin')
				$this->_view->FinaltotalInvoice=number_format(($total-$totalTax-$period_fees),2,'.','');
			  else
				$this->_view->FinaltotalInvoice=number_format($total,2,'.','');

			  if($this->_view->FinaltotalInvoice >=$this->config['contrib_minamount'] && !$invoiceDetails[0]['invoice_path'])
			  {
				$this->_view->getpaid="YES";
			  }
			  else if($invoiceDetails[0]['invoice_path'])
			  {
				$this->_view->downloadPDF="YES";
				$this->_view->invoiceId=$invoiceId;
			  }
			  if($invoiceDetails[0]['status'])
				$this->_view->status=$invoiceDetails[0]['status'];
			  
			  
			  $this->_view->invoiceDetails=$invoiceDetails;
			  $this->_view->invoiceId=$invoiceDetails[0]['invoiceId'];
			  $this->_view->created_at=$invoiceDetails[0]['created_at'];
		  }
		  else
			$this->_redirect("/contrib/royalties");
		  /**Paypal charges**/
		  if($profile_contribinfo[0]['payment_type']=='paypal')
		  {
			$total_amount=$this->_view->FinaltotalInvoice;
			$bank_charges=0;//0.25+((3.4*$total_amount)/100);
			$final_total_amount=$total_amount+$bank_charges;
			$this->_view->bank_charges=$bank_charges;
			$this->_view->final_total_amount=$final_total_amount;
		  }
			  else if($profile_contribinfo[0]['payment_type']=='virement' && ($profile_contribinfo[0]['pay_info_type']=='out_uk' OR $profile_contribinfo[0]['pay_info_type']=='freelance'))
		  {
			$total_amount=$this->_view->FinaltotalInvoice;
				$bank_charges=0;//16+((1*$total_amount)/100); 
			$final_total_amount=$total_amount+$bank_charges;
			$this->_view->bank_charges=$bank_charges;
			$this->_view->final_total_amount=$final_total_amount;
		  }
		  /**iNOVICE inFO ***/
			  $this->_view->ep_contrib_profile_pay_info_type=$profile_contribinfo[0]['pay_info_type'];
			  $this->_view->ep_contrib_profile_SSN=$profile_contribinfo[0]['SSN'];
			  $this->_view->ep_contrib_profile_company_number=$profile_contribinfo[0]['company_number'];
			  $this->_view->ep_contrib_profile_vat_check=$profile_contribinfo[0]['vat_check'];
			  $this->_view->ep_contrib_profile_VAT_number=$profile_contribinfo[0]['VAT_number'];
			  $this->_view->ep_contrib_payment_type=$profile_contribinfo[0]['payment_type'];
			  $this->_view->ep_contrib_min_amount=$this->config['contrib_minamount'];
		  
			  $this->_view->meta_title="Author Invoice";
			  
			 /*added w.r.t if already have unpaid invoices for that month*/
			if($oldinvoice=='yes')
				$this->_view->invoiceId=''; 
						 
			//echo "<pre>";print_r($profileplus_contribinfo);exit; 
			 if($profile_contribinfo[0]['pay_info_type']=='out_uk' && $profileplus_contribinfo[0]['country']=='156')
				$this->_redirect("/contrib/royalties");			   
			  else if($profile_contribinfo[0]['pay_info_type'])
				$this->render("Contrib_invoicedetails_popup");
			
		}	
          else
            $this->_redirect("/contrib/royalties");
    }
    else
        $this->_redirect("/contrib/royalties");
  }
  /**Download Invoice PDF***/
    public function downloadinvoiceAction()
    {
        $invoiceParams=$this->_request->getParams();
        $attachment=new Ep_Ticket_Attachment();
        $identifier= $this->EP_Contrib_reg->clientidentifier;
        $royalty=new Ep_Royalty_Royalties();
        $invoice_path=$royalty->getInvoicePDFPath($invoiceParams['invoiceid'],$identifier);
        
        if($invoice_path!='NOT EXIST')
        {
            $invoicePDFPath=APP_PATH_ROOT.'invoice/'.$invoice_path;
            
            if(file_exists($invoicePDFPath))
            {
                $attachment->downloadAttachment($invoicePDFPath,"attachment");
            }
        }
        else
            $this->_redirect("/contrib/royalties");
    }
   /**INVOICE PDF GENERATION*/ 
   public function generatepdfAction()
    {
       
	   if($this->_helper->EpCustom->checksession())
        {
			$invoice_params=$this->_request->getParams();
			$currency=$invoice_params['currency'];
			$pound='&#163;';
			$euro='&#x80;';
			if($currency=='euro' OR $currency=='pound')
			{
            /***Profile Info***/
				//setlocale(LC_TIME, 'fr_FR');
				$date_invoice_full= strftime("%B %Y");
            $date_invocie = date("d/m/Y");
            $date_invoice_ep=date("Y/m");
                $profileplus_obj = new Ep_Contrib_ProfilePlus();
                $profileContrib_obj = new Ep_Contrib_ProfileContributor();
                $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
                //$this->_view->client_email=strtolower($this->EP_Contrib_reg->clientemail);
                if($profileplus_obj->checkProfileExist($contrib_identifier)!='NO')
                {
                    $profile_identifier_info=$profileplus_obj->checkProfileExist($contrib_identifier);
                    $profile_identifier=$profile_identifier_info[0]['user_id'];
                    $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);
					$profile_contribinfo[0]['pay_info_type']='ep_admin';//added w.r.t new tax fees
                     /**iNOVICE inFO ***/
                    $this->_view->ep_contrib_profile_pay_info_type=$profile_contribinfo[0]['pay_info_type'];
                    $this->_view->ep_contrib_profile_SSN=$profile_contribinfo[0]['SSN'];
                    $this->_view->ep_contrib_profile_company_number=$profile_contribinfo[0]['company_number'];
                    $this->_view->ep_contrib_profile_vat_check=$profile_contribinfo[0]['vat_check'];
                    $this->_view->ep_contrib_profile_VAT_number=$profile_contribinfo[0]['VAT_number'];
                    $profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
					$address='<b>'.$profileinfo[0]['first_name'].' '.$profileinfo[0]['last_name'].'</b><br><br>';
                    $address.=$profileinfo[0]['address'].'<br>';
                    $address.=$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city'].'  '.$this->getCountryName($profileinfo[0]['country']).'<br>';
					//$address.='Phone :'.$profileinfo[0]['phone_number'].'<br>';
					//$address.='Email :'.$this->EP_Contrib_reg->clientemail;
					
					$full_name='<b>'.$profileinfo[0]['first_name'].' '.$profileinfo[0]['last_name'].'</b>';
					
          }
            /**ENDED**/

            $identifier= $this->EP_Contrib_reg->clientidentifier;
            

            /*Get All current month not paid invoices if any and delete the invoices and creating all in a single invoice**/
              $invoice_obj=new Ep_Royalty_Invoice();

              //get current month unpaid invoices
              $unPaidInvoices=$invoice_obj->getMonthlyUnpaidInvoices($identifier,$currency);
              
              // echo "<pre>";print_r($unPaidInvoices);exit;

              if($unPaidInvoices)
              {
                foreach($unPaidInvoices as $invoice)  
                {
                    $update_invoice_obj=new Ep_Royalty_Invoice();
                    $update_royalty_obj=new Ep_Royalty_Royalties();

                    $invoice_id=$invoice['invoiceId'];

                     //Update Royalties Table

                      $data['invoiceId']=NULL;
                     $update_royalty_obj->updateRoyalty($invoice_id,$data);

                    //delete invoice files
                      $invoice_file=APP_PATH_ROOT.'invoice/'.$invoice['invoice_path'];
                      if(!is_dir($invoice_file) && file_exists($invoice_file))
                      {
                         unlink($invoice_file);
                      }

                    //delete invoice record from DB  
                      $update_invoice_obj->DeleteInvoice($invoice_id);


                }
                  //print_r($unPaidInvoices);

              }


            

            /*ALl article with royalties*/   
            $royalty=new Ep_Royalty_Royalties();
            $ticket_obj=new Ep_Ticket_Ticket();         
				$invoiceDetails=$royalty->getInvoiceDetails($identifier,NULL,$currency);

            //print_r($invoiceDetails);exit;

            $invoice_details_pdf='<table class="change_order_items">
                                    <tbody>
                                        <tr>
												<th style="border-bottom:1px solid black;font-size: 12pt;">Description</th>
												<th style="border-bottom:1px solid black;font-size: 12pt;">Amount</th>
                                        </tr>';
            if(count($invoiceDetails)>0 && is_array($invoiceDetails))
            {
                $total=0;
                foreach( $invoiceDetails as $details)
                {
                    $total+=$details['price'];

                    $client_id= $details['client_id'];
                    $client_name=$ticket_obj->getUserName($client_id);
                    $article_created_date=ucfirst(strftime("%b %Y",strtotime($details['article_created_date'])));


                     $invoice_details_pdf.='<tr>
												<td style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;">'.$details['AOTitle'].' / '.$client_name.' / '.$article_created_date.'</td>
												<td style="padding: 0.5em;padding-right: 4pt;text-align: right;font-size: 12pt;" class="change_order_total_col">'.number_format($details['price'],2).'</td>
                                            </tr>';
          }
                $invoice_details_pdf.='<tr>
											<td style="border-right: 1px solid black;border-top:1pt solid black;text-align:right;margin-right:10px;font-size: 12pt;">Total</td>
										<td style="border-top:1pt solid black;font-size: 12pt;" class="change_order_total_col">'.number_format($total,2).'</td>
                                        </tr>
                                    </tbody>
                                </table>';
               /**Total Invoice*/
					$this->_view->totalInvoice=number_format($total,2);
                /**Tax Calculation */
                $totalTax=0;
                $tax_details_pdf='';
                $payinfo_number='';
                $profile_contribinfo[0]['pay_info_type']=='ep_admin';//added w.r.t new tax fees
				
                /*if($profile_contribinfo[0]['pay_info_type']=='ssn')
                {
                    $veuvage=number_format((($total*0.85)/100),2,'.','');
                    $csg=number_format((($total*7.36875)/100),2,'.','');
                    $crds=number_format((($total*0.49125)/100),2,'.','');
                    $formation=number_format((($total*0.35)/100),2,'.','');
                    $tax_date=date("Y-m-d");
                    if($tax_date < date("Y-m-d",strtotime('2012-07-01')))
                    {
                        $formation=0;
                    }
                    $totalTax=$veuvage+$csg+$crds+$formation;
            $tax_details_pdf='<table class="change_order_items">
                                            <tbody>
                                            <tr>
                          <th style="border-bottom:1px solid black;font-size: 12pt;">PRECOMPTE</th>
                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                                            </tr>
                                            <tr>
                          <td style="border-right: 1px solid black;padding: 0.5em; width:60%;">Cotisation maladie veuvage</td>
                          <td style="border-right: 1px solid black;padding: 0.5em; width:25%;">taux : 0,85%</td>
							  <td style="border-right: 1px solid black;padding: 0.5em;width:15%;" class="change_order_total_col">'.number_format((($total*0.85)/100),2).'</td>
                                            </tr>
                                            <tr>
                          <td style="border-right: 1px solid black;padding: 0.5em;">CSG</td>
                          <td style="border-right: 1px solid black;padding: 0.5em;">taux : 7,36875%</td>
							  <td style="border-right: 1px solid black;padding: 0.5em;" class="change_order_total_col">'.number_format((($total*7.36875)/100),2).'</td>
                                            </tr>
                                            <tr>
                          <td style="border-right: 1px solid black;padding: 0.5em;">CRDS</td>
                          <td style="border-right: 1px solid black;padding: 0.5em;">taux : 0,49125% </td>
							  <td style="border-right: 1px solid black;padding: 0.5em;" class="change_order_total_col">'.number_format((($total*0.49125)/100),2).'</td>
                                            </tr>
                                        </table>';
              if($tax_date >= date("Y-m-d",strtotime('2012-07-01')))
              {
                  $tax_details_pdf.='<table class="change_order_items">
                           <tr>
                            <td style="border-right: 1px solid black;padding: 0.5em; width:60%;">Formation Professionnelle</td>
                            <td style="border-right: 1px solid black;padding: 0.5em; width:25%;">taux : 0,35% </td>
									<td class="change_order_total_col" style="width:15%;">'.number_format((($total*0.35)/100),2).'</td>
                          </tr>
                          </table>';
              }    
                          $tax_details_pdf.='<table class="change_order_items">
                                            <tr>
                          <td colspan="2" style="width:85%;border-right: 1px solid black;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;text-align:right;margin-right:10px">A VERSER A L\'AGESSA</td>
							  <td style="width:15%;font-weight:bold;border-top:1pt solid black;padding-right:10pt;text-align: right;font-size: 12pt;">'.number_format($totalTax,2).' '.$$currency.'</td>
                                            </tr>
                                            </tbody>
                                            </table>';
                $payinfo_number="N&deg; de  S&eacute;curit&eacute; sociale : ".$profile_contribinfo[0]['SSN']."<br>";
                }
                else if($profile_contribinfo[0]['pay_info_type']=='comp_num' && $profile_contribinfo[0]['vat_check']=='YES' )
                {
						$TVA=number_format((($total*19.6)/100),2);
                    $totalTax=$TVA;
                    $tax_details_pdf='<table class="change_order_items">
                                            <tbody>
                                            <tr>
                          <th style="border-bottom:1px solid black;font-size: 12pt;">PRECOMPTE</th>
                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                                            </tr>
                                            <tr>
                          <td style="border-right: 1px solid black;padding: 0.5em;">TVA</td>
							  <td style="border-right: 1px solid black;padding: 0.5em;">taux : 19,6%</td>
							  <td style="border-right: 1px solid black;padding: 0.5em;" class="change_order_total_col">'.number_format((($total*19.6)/100),2).'</td>
                                            </tr>
                                            <tr>
                          <td colspan="2" style="width:85%;border-right: 1px solid black;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;text-align:right;margin-right:10px">A VERSER A L\'AGESSA</td>
							  <td style="width:15%;font-weight:bold;border-top:1pt solid black;padding-right:10pt;text-align: right;font-size: 12pt;">'.number_format($totalTax,2).' '.$$currency.'</td>
                                            </tr>
                                            </tbody>
                                            </table>';
                  // $payinfo_number="Siret : ".$profile_contribinfo[0]['company_number']."<br>";
                }
                if($profile_contribinfo[0]['pay_info_type']=='comp_num')
						$payinfo_number="Siret : ".$profile_contribinfo[0]['company_number']."<br>"; */
				
				if($profile_contribinfo[0]['pay_info_type']=='ep_admin') //Added w.r.t new admin fees
                {
                    if($profile_contribinfo[0]['payment_type']=="paypal")
                        $admin_fee_percentage=0;
                    else
                         $admin_fee_percentage=2;
                    $epTax=number_format((($total*$admin_fee_percentage)/100),2,'.','');
                    $totalTax=$epTax;

                    $tax_details_pdf='<table class="change_order_items">
                                            <tbody>                                            
                                            <tr>
                          <td colspan="2" style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;">Transfer and administrative charges : '.$admin_fee_percentage.'%</td>
                          
                          <td style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;" class="change_order_total_col">'.number_format((($total*$admin_fee_percentage)/100),2,'.','').'</td>
                                            </tr>                                            
                                            </tbody>
                                            </table>';
                }
                if($_COOKIE['period_month'])
                {
                    $period_month=$_COOKIE['period_month'];

                    if($period_month==1)
                    {
                        $fees_percentage=8;
                        $fees_paid_month=strftime( '%B', strtotime( 'last day of +1 month', time() ) );
                    }    
                    if($period_month==2)
                    {
                        $fees_percentage=0;
                         $fees_paid_month=strftime( '%B', strtotime( 'last day of +2 month',time() ) );
                    }    
                        

                     $period_fees=number_format(((($total-$totalTax)*$fees_percentage)/100),2,'.',''); 
                     
                      $tax_details_pdf.='<table class="change_order_items">
                                            <tbody>                                                                                       
                                            <tr>
                          <td colspan="2" style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;">Advance payment charge : '.$fees_percentage.'% (payment on the 15th '.$fees_paid_month.' ) </td>
                          
                          <td style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;" class="change_order_total_col">'.number_format($period_fees,2,'.','').'</td>
                                            </tr>                                            
                                            </tbody>
                                            </table>'; 

                      $totalTax=$totalTax+$period_fees;                      

                }	
						
          
				$this->_view->totalTax=$totalTax;
                if($profile_contribinfo[0]['pay_info_type']=='ssn')
                    $this->_view->FinaltotalInvoice=number_format(($total-$totalTax),2,'.','');
                else if($profile_contribinfo[0]['pay_info_type']=='comp_num' && $profile_contribinfo[0]['vat_check']=='YES' )
                    $this->_view->FinaltotalInvoice=number_format(($total+$totalTax),2,'.','');
				else if($profile_contribinfo[0]['pay_info_type']=='ep_admin')
                    $this->_view->FinaltotalInvoice=number_format(($total-$totalTax),2,'.','');	
                else
                    $this->_view->FinaltotalInvoice=number_format($total,2,'.','');
					
                $final_invoice_amount='<table class="change_order_items" width="100%">
                                            <tr>
							  <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Total Amount payable to the writer</td>
							  <td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($this->_view->FinaltotalInvoice,2).' '.$$currency.'</td>
                                            </tr>
                                        </table>';
            }
            /**Wire OR paypal info**/
                $total_transfer_amount='';
                $bank_transfer_price='';
            if($profile_contribinfo[0]['payment_type']=="paypal")
            {
                $bank_charges=0;//0.25+((3.4*$this->_view->FinaltotalInvoice)/100);
               /* $bank_transfer_price='<table class="change_order_items" width="100%">
                                            <tr>
                                            <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Frais de virement paypal 0,25 + (3,4% x Montant &agrave; verser &agrave; l\'auteur)</td>
												<td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($bank_charges,2,',','').' '.$$currency.'</td>
                                            </tr>
                                        </table>';*/
                $total_transfer_amount_final=$this->_view->FinaltotalInvoice+$bank_charges;
                $total_transfer_amount='<table class="change_order_items" width="100%">
                                            <tr>
							  <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Final Amount</td>
							  <td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($total_transfer_amount_final,2).' '.$$currency.'</td>
                                            </tr>
                                        </table>';
                $remuneration="Paypal : ".$profile_contribinfo[0]['paypal_id'];
                $profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['paypal_id'];
            }
            else if($profile_contribinfo[0]['payment_type']=="virement")
            {
					
				if($profile_contribinfo[0]['pay_info_type']=='out_uk')
                {
                    $bank_charges=0;//16+((1*$this->_view->FinaltotalInvoice)/100); Updated by Rakesh on 17.08.2012
                    /*$bank_transfer_price='<table class="change_order_items" width="100%">
                                                <tr>
                                                <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Frais de virement &eacute;tranger 16 + (1% x Montant &agrave; verser &agrave; l\'auteur))</td>
													<td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($bank_charges,2,',','').' '.$$currency.'</td>
                                                </tr>
                                            </table>';*/
						/* $total_transfer_amount_final=$this->_view->FinaltotalInvoice+$bank_charges;
                    $total_transfer_amount='<table class="change_order_items" width="100%">
                                                <tr>
													<td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Final Amount</td>
													<td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($total_transfer_amount_final,2).' '.$$currency.'</td>
                                                </tr>
												</table>'; */


					$bank_codes=explode("|",$profile_contribinfo[0]['rib_id']);  
						
					$remuneration="IBAN  : ".$bank_codes[1]."<br>";
					$remuneration.="BIC swift Bank Identification Code  : ".$bank_codes[0];								 
					$profile_contribinfo[0]['rib_id']= str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']);                
					$profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['rib_id'];
						
                }  
                else
                {	  
					$bank_codes=explode("|",$profile_contribinfo[0]['rib_id']);  
					$profile_contribinfo[0]['rib_id']= str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']); 

					if(count($bank_codes)<5)
					{
						$remuneration="IBAN  : ".$bank_codes[1]."<br>";
						$remuneration.="BIC swift Bank Identification Code  : ".$bank_codes[0]; 
					}	
					else 
						$remuneration="RIB : ".str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']);

					
                }   
		$profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['rib_id'];
		$bank_account_name="Account name : ".$profile_contribinfo[0]['bank_account_name'].'<br>';
					
            }
				
				//$this->_view->totalInvoice=str_replace(",",".",$this->_view->totalInvoice);
            //echo $profile_contribinfo[0]['pay_info_type'] ."-". $this->_view->totalInvoice."-". $profile_contribinfo[0]['payment_info_id'];
			//exit;
           // if($profile_contribinfo[0]['pay_info_type'] && $this->_view->totalInvoice >=$this->config['contrib_minamount'] && $profile_contribinfo[0]['payment_info_id']) // commented by Rakesh
			 if($profile_contribinfo[0]['pay_info_type'] && $total >=$this->config['contrib_minamount'] && $profile_contribinfo[0]['payment_info_id'])
            {
                    /**INserting INvoice Details**/
                    $invoice=new Ep_Royalty_Invoice();
                    //$invoiceId=uniqid();
						//$invoice_count=$invoice->getMonthlyCount($contrib_identifier);						
						//$cnt=$invoice_count+1;
	                    if($currency=='euro')
							$cnt=1;
	                    else if($currency=='pound')
							$cnt=2;
                    $invoiceId=date("Y-m-").$cnt."-".$contrib_identifier;
                    
                    $invoicedir=APP_PATH_ROOT.'invoice/contributor/'.$contrib_identifier.'/';
            
                    if(!is_dir($invoicedir))
                        mkdir($invoicedir,TRUE);
                        chmod($invoicedir,0777);
                    $invoice->invoiceId='ep_invoice_'.$invoiceId;
                     $invoice->user_id=$contrib_identifier;
                    $invoice->total_invoice=$this->_view->totalInvoice;
                    $invoice->total_invoice_paid=$this->_view->FinaltotalInvoice;
                    $invoice->tax=$this->_view->totalTax;
                    $invoice->payment_info_type=$profile_contribinfo[0]['pay_info_type'];
                    $invoice->vat_check=$profile_contribinfo[0]['vat_check'];
                    $invoice->payment_type=$profile_contribinfo[0]['payment_type'];
                    $invoice->payment_info_id=$profile_contribinfo[0]['payment_info_id'];
                    $invoice->invoice_path='contributor/'.$contrib_identifier.'/'.$invoiceId.'.pdf';
                    $invoice->status='Not paid';
					$invoice->currency=$currency;
                    $invoice->created_at=date("Y-m-d H:i:s", time());
					
					//Added w.r.t new admin fees
                    $invoice->ep_admin_fee='yes';
					$invoice->ep_admin_fee_percentage=$admin_fee_percentage;
                    $invoice->pay_later_month=$period_month;
                    $invoice->pay_later_percentage=$fees_percentage;
					$invoice->bank_account_name=$profile_contribinfo[0]['bank_account_name'];
                    $invoice->insert();
				   
                    /**Update Royalties Table**/
                    $data['invoiceId']=$invoice->invoiceId;
                        $where="user_id='".$contrib_identifier."' and invoiceId IS NULL  and currency='".$currency."'";
					$royalty->updateInvoice($data,$where);
            
                   require_once(APP_PATH_ROOT.'dompdf/dompdf_config.inc.php');
                      
                    $html=file_get_contents(SCRIPT_VIEW_PATH.'Contrib/invoice_pdf.phtml');
                    //eval("\$html= \"$html\";");
                    $html=str_replace('$$$$invoice_details_pdf$$$$',$invoice_details_pdf,$html);
                    $html=str_replace('$$$$tax_details_pdf$$$$',$tax_details_pdf,$html);
                    $html=str_replace('$$$$final_invoice_amount$$$$',$final_invoice_amount,$html);
                    $html=str_replace('$$$$date_invoice_full$$$$',$date_invoice_full,$html);
                    $html=str_replace('$$$$date_invoice$$$$',$date_invocie,$html);
                    $html=str_replace('$$$$address$$$$',$address,$html);
                    $html=str_replace('$$$$payinfo_number$$$$',$payinfo_number,$html);
                    $html=str_replace('$$$$date_invoice_ep$$$$',$date_invoice_ep,$html);
                    $html=str_replace('$$$$invoice_identifier$$$$',$invoiceId,$html);
					$html=str_replace('$$$$bank_account_name$$$$',$bank_account_name,$html);
                    $html=str_replace('$$$$remuneration$$$$',$remuneration,$html);
                    $html=str_replace('$$$$bank_transfer_price$$$$',$bank_transfer_price,$html);
                    $html=str_replace('$$$$total_transfer_amount$$$$',$total_transfer_amount,$html);
						$html=str_replace('$$$$full_name$$$$',$full_name,$html);						
                    $html =str_replace('<tbody>', '', $html);
                    $html =str_replace('</tbody>', '', $html);
                    
                    if ( get_magic_quotes_gpc() )
                        $html = stripslashes($html);
                    
                    //echo $html;exit;
                    $dompdf = new DOMPDF();
                    $dompdf->load_html( $html);
                    $dompdf->set_paper("a4");
                    $dompdf->render();
                    // $dompdf->stream("dompdf_out.pdf");
                    $pdf = $dompdf->output();
                    file_put_contents($invoicedir.'/'.$invoiceId.'.pdf', $pdf);
                    // file_put_contents($invoicedir.'/test.pdf', $pdf);
                    $this->_redirect("/contrib/royalties");
                    exit(0);
        }
            else
                $this->_redirect("/contrib/royalties");
        }
			else
				$this->_redirect("/contrib/royalties");	
        }
    }
	
	
    public function generatepdffakeAction()
    {
       
	   if($this->_helper->EpCustom->checksession())
        {
			$invoice_params=$this->_request->getParams();
			$currency=$invoice_params['currency'];
			$pound='&#163;';
			$euro='&#x80;';
			if($currency=='euro' OR $currency=='pound')
			{
            /***Profile Info***/
				//setlocale(LC_TIME, 'fr_FR');
				$date_invoice_full= strftime("%B %Y");
            $date_invocie = date("d/m/Y");
            $date_invoice_ep=date("Y/m");
                $profileplus_obj = new Ep_Contrib_ProfilePlus();
                $profileContrib_obj = new Ep_Contrib_ProfileContributor();
                $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
                //$this->_view->client_email=strtolower($this->EP_Contrib_reg->clientemail);
                if($profileplus_obj->checkProfileExist($contrib_identifier)!='NO')
                {
                    $profile_identifier_info=$profileplus_obj->checkProfileExist($contrib_identifier);
                    $profile_identifier=$profile_identifier_info[0]['user_id'];
                    $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);
					$profile_contribinfo[0]['pay_info_type']='ep_admin';//added w.r.t new tax fees
                     /**iNOVICE inFO ***/
                    $this->_view->ep_contrib_profile_pay_info_type=$profile_contribinfo[0]['pay_info_type'];
                    $this->_view->ep_contrib_profile_SSN=$profile_contribinfo[0]['SSN'];
                    $this->_view->ep_contrib_profile_company_number=$profile_contribinfo[0]['company_number'];
                    $this->_view->ep_contrib_profile_vat_check=$profile_contribinfo[0]['vat_check'];
                    $this->_view->ep_contrib_profile_VAT_number=$profile_contribinfo[0]['VAT_number'];
                    $profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
					$address='<b>'.$profileinfo[0]['first_name'].' '.$profileinfo[0]['last_name'].'</b><br><br>';
                    $address.=$profileinfo[0]['address'].'<br>';
                    $address.=$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city'].'  '.$this->getCountryName($profileinfo[0]['country']).'<br>';
					//$address.='Phone :'.$profileinfo[0]['phone_number'].'<br>';
					//$address.='Email :'.$this->EP_Contrib_reg->clientemail;
					
					$full_name='<b>'.$profileinfo[0]['first_name'].' '.$profileinfo[0]['last_name'].'</b>';
					
          }
            /**ENDED**/

            $identifier= $this->EP_Contrib_reg->clientidentifier;
            

            /*Get All current month not paid invoices if any and delete the invoices and creating all in a single invoice**/
              $invoice_obj=new Ep_Royalty_Invoice();

              //get current month unpaid invoices
              $unPaidInvoices=$invoice_obj->getMonthlyUnpaidInvoices($identifier,$currency);
              
              // echo "<pre>";print_r($unPaidInvoices);exit;

              if($unPaidInvoices)
              {
                foreach($unPaidInvoices as $invoice)  
                {
                    $update_invoice_obj=new Ep_Royalty_Invoice();
                    $update_royalty_obj=new Ep_Royalty_Royalties();

                    $invoice_id=$invoice['invoiceId'];

                     //Update Royalties Table

                      $data['invoiceId']=NULL;
                     $update_royalty_obj->updateRoyalty($invoice_id,$data);

                    //delete invoice files
                      $invoice_file=APP_PATH_ROOT.'invoice/'.$invoice['invoice_path'];
                      if(!is_dir($invoice_file) && file_exists($invoice_file))
                      {
                         unlink($invoice_file);
                      }

                    //delete invoice record from DB  
                      $update_invoice_obj->DeleteInvoice($invoice_id);


                }
                  //print_r($unPaidInvoices);

              }


            

            /*ALl article with royalties*/   
            $royalty=new Ep_Royalty_Royalties();
            $ticket_obj=new Ep_Ticket_Ticket();         
				$invoiceDetails=$royalty->getInvoiceDetails($identifier,NULL,$currency);

            //print_r($invoiceDetails);exit;

            $invoice_details_pdf='<table class="change_order_items">
                                    <tbody>
                                        <tr>
												<th style="border-bottom:1px solid black;font-size: 12pt;">Description</th>
												<th style="border-bottom:1px solid black;font-size: 12pt;">Amount</th>
                                        </tr>';
            if(count($invoiceDetails)>0 && is_array($invoiceDetails))
            {
                $total=0;
                foreach( $invoiceDetails as $details)
                {
                    $total+=$details['price'];

                    $client_id= $details['client_id'];
                    $client_name=$ticket_obj->getUserName($client_id);
                    $article_created_date=ucfirst(strftime("%b %Y",strtotime($details['article_created_date'])));


                     $invoice_details_pdf.='<tr>
												<td style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;">'.$details['AOTitle'].' / '.$client_name.' / '.$article_created_date.'</td>
												<td style="padding: 0.5em;padding-right: 4pt;text-align: right;font-size: 12pt;" class="change_order_total_col">'.number_format($details['price'],2).'</td>
                                            </tr>';
          }
                $invoice_details_pdf.='<tr>
											<td style="border-right: 1px solid black;border-top:1pt solid black;text-align:right;margin-right:10px;font-size: 12pt;">Total</td>
										<td style="border-top:1pt solid black;font-size: 12pt;" class="change_order_total_col">'.number_format($total,2).'</td>
                                        </tr>
                                    </tbody>
                                </table>';
               /**Total Invoice*/
					$this->_view->totalInvoice=number_format($total,2);
                /**Tax Calculation */
                $totalTax=0;
                $tax_details_pdf='';
                $payinfo_number='';
                $profile_contribinfo[0]['pay_info_type']='ep_admin';//added w.r.t new tax fees
				
                /*if($profile_contribinfo[0]['pay_info_type']=='ssn')
                {
                    $veuvage=number_format((($total*0.85)/100),2,'.','');
                    $csg=number_format((($total*7.36875)/100),2,'.','');
                    $crds=number_format((($total*0.49125)/100),2,'.','');
                    $formation=number_format((($total*0.35)/100),2,'.','');
                    $tax_date=date("Y-m-d");
                    if($tax_date < date("Y-m-d",strtotime('2012-07-01')))
                    {
                        $formation=0;
                    }
                    $totalTax=$veuvage+$csg+$crds+$formation;
            $tax_details_pdf='<table class="change_order_items">
                                            <tbody>
                                            <tr>
                          <th style="border-bottom:1px solid black;font-size: 12pt;">PRECOMPTE</th>
                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                                            </tr>
                                            <tr>
                          <td style="border-right: 1px solid black;padding: 0.5em; width:60%;">Cotisation maladie veuvage</td>
                          <td style="border-right: 1px solid black;padding: 0.5em; width:25%;">taux : 0,85%</td>
							  <td style="border-right: 1px solid black;padding: 0.5em;width:15%;" class="change_order_total_col">'.number_format((($total*0.85)/100),2).'</td>
                                            </tr>
                                            <tr>
                          <td style="border-right: 1px solid black;padding: 0.5em;">CSG</td>
                          <td style="border-right: 1px solid black;padding: 0.5em;">taux : 7,36875%</td>
							  <td style="border-right: 1px solid black;padding: 0.5em;" class="change_order_total_col">'.number_format((($total*7.36875)/100),2).'</td>
                                            </tr>
                                            <tr>
                          <td style="border-right: 1px solid black;padding: 0.5em;">CRDS</td>
                          <td style="border-right: 1px solid black;padding: 0.5em;">taux : 0,49125% </td>
							  <td style="border-right: 1px solid black;padding: 0.5em;" class="change_order_total_col">'.number_format((($total*0.49125)/100),2).'</td>
                                            </tr>
                                        </table>';
              if($tax_date >= date("Y-m-d",strtotime('2012-07-01')))
              {
                  $tax_details_pdf.='<table class="change_order_items">
                           <tr>
                            <td style="border-right: 1px solid black;padding: 0.5em; width:60%;">Formation Professionnelle</td>
                            <td style="border-right: 1px solid black;padding: 0.5em; width:25%;">taux : 0,35% </td>
									<td class="change_order_total_col" style="width:15%;">'.number_format((($total*0.35)/100),2).'</td>
                          </tr>
                          </table>';
              }    
                          $tax_details_pdf.='<table class="change_order_items">
                                            <tr>
                          <td colspan="2" style="width:85%;border-right: 1px solid black;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;text-align:right;margin-right:10px">A VERSER A L\'AGESSA</td>
							  <td style="width:15%;font-weight:bold;border-top:1pt solid black;padding-right:10pt;text-align: right;font-size: 12pt;">'.number_format($totalTax,2).' '.$$currency.'</td>
                                            </tr>
                                            </tbody>
                                            </table>';
                $payinfo_number="N&deg; de  S&eacute;curit&eacute; sociale : ".$profile_contribinfo[0]['SSN']."<br>";
                }
                else if($profile_contribinfo[0]['pay_info_type']=='comp_num' && $profile_contribinfo[0]['vat_check']=='YES' )
                {
                    $TVA=number_format((($total*20)/100),2,'.','');
                    $totalTax=$TVA;
                    $tax_details_pdf='<table class="change_order_items">
                                            <tbody>
                                            <tr>
                          <th style="border-bottom:1px solid black;font-size: 12pt;">PRECOMPTE</th>
                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                                            </tr>
                                            <tr>
                          <td style="border-right: 1px solid black;padding: 0.5em;">TVA</td>
							  <td style="border-right: 1px solid black;padding: 0.5em;">taux : 19,6%</td>
							  <td style="border-right: 1px solid black;padding: 0.5em;" class="change_order_total_col">'.number_format((($total*19.6)/100),2).'</td>
                                            </tr>
                                            <tr>
                          <td colspan="2" style="width:85%;border-right: 1px solid black;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;text-align:right;margin-right:10px">A VERSER A L\'AGESSA</td>
							  <td style="width:15%;font-weight:bold;border-top:1pt solid black;padding-right:10pt;text-align: right;font-size: 12pt;">'.number_format($totalTax,2).' '.$$currency.'</td>
                                            </tr>
                                            </tbody>
                                            </table>';
                  // $payinfo_number="Siret : ".$profile_contribinfo[0]['company_number']."<br>";
                }
				
                else if($profile_contribinfo[0]['pay_info_type']=='ep_admin') //Added w.r.t new admin fees
                {
                    if($profile_contribinfo[0]['payment_type']=="paypal")
                        $admin_fee_percentage=0;
                    else
                         $admin_fee_percentage=2;
                    $epTax=number_format((($total*$admin_fee_percentage)/100),2,'.','');
                    $totalTax=$epTax;

                    $tax_details_pdf='<table class="change_order_items">
                                            <tbody>                                            
                                            <tr>
                          <td colspan="2" style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;">Transfer and administrative charges : '.$admin_fee_percentage.'%</td>
                          
                          <td style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;" class="change_order_total_col">'.number_format((($total*$admin_fee_percentage)/100),2,'.','').'</td>
                                            </tr>                                            
                                            </tbody>
                                            </table>';
                }
                if($_COOKIE['period_month'])
                {
                    $period_month=$_COOKIE['period_month'];

                    if($period_month==1)
                    {
                        $fees_percentage=8;
                        $fees_paid_month=strftime( '%B', strtotime( 'last day of +1 month', time() ) );
                    }    
                    if($period_month==2)
                    {
                        $fees_percentage=0;
                         $fees_paid_month=strftime( '%B', strtotime( 'last day of +2 month',time() ) );
                    }    
                        

                     $period_fees=number_format(((($total-$totalTax)*$fees_percentage)/100),2,'.',''); 
                     
                      $tax_details_pdf.='<table class="change_order_items">
                                            <tbody>                                                                                       
                                            <tr>
                          <td colspan="2" style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;">Advance payment charge : '.$fees_percentage.'% (payment on the 15th '.$fees_paid_month.' ) </td>
                          
                          <td style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;" class="change_order_total_col">'.number_format($period_fees,2,'.','').'</td>
                                            </tr>                                            
                                            </tbody>
                                            </table>'; 

                      $totalTax=$totalTax+$period_fees;                      
                }	
						
          
                if($profile_contribinfo[0]['pay_info_type']=='comp_num')
                    $payinfo_number="Siret : ".$profile_contribinfo[0]['company_number']."<br>";
				$this->_view->totalTax=$totalTax;
                if($profile_contribinfo[0]['pay_info_type']=='ssn')
                    $this->_view->FinaltotalInvoice=number_format(($total-$totalTax),2,'.','');
                else if($profile_contribinfo[0]['pay_info_type']=='comp_num' && $profile_contribinfo[0]['vat_check']=='YES' )
                    $this->_view->FinaltotalInvoice=number_format(($total+$totalTax),2,'.','');
				else if($profile_contribinfo[0]['pay_info_type']=='ep_admin')
                    $this->_view->FinaltotalInvoice=number_format(($total-$totalTax),2,'.','');	
                else
                    $this->_view->FinaltotalInvoice=number_format($total,2,'.','');
					
                $final_invoice_amount='<table class="change_order_items" width="100%">
                                            <tr>
							  <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Total Amount payable to the writer</td>
							  <td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($this->_view->FinaltotalInvoice,2).' '.$$currency.'</td>
                                            </tr>
                                        </table>';
            }
            /**Wire OR paypal info**/
                $total_transfer_amount='';
                $bank_transfer_price='';
            if($profile_contribinfo[0]['payment_type']=="paypal")
            {
                $bank_charges=0;//0.25+((3.4*$this->_view->FinaltotalInvoice)/100);
               /* $bank_transfer_price='<table class="change_order_items" width="100%">
                                            <tr>
                                            <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Frais de virement paypal 0,25 + (3,4% x Montant &agrave; verser &agrave; l\'auteur)</td>
												<td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($bank_charges,2,',','').' '.$$currency.'</td>
                                            </tr>
                                        </table>';*/
                $total_transfer_amount_final=$this->_view->FinaltotalInvoice+$bank_charges;
                $total_transfer_amount='<table class="change_order_items" width="100%">
                                            <tr>
							  <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Final Amount</td>
							  <td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($total_transfer_amount_final,2).' '.$$currency.'</td>
                                            </tr>
                                        </table>';
                $remuneration="Paypal : ".$profile_contribinfo[0]['paypal_id'];
                $profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['paypal_id'];
            }
            else if($profile_contribinfo[0]['payment_type']=="virement")
            {
					
				if($profile_contribinfo[0]['pay_info_type']=='out_uk')
                {
                    $bank_charges=0;//16+((1*$this->_view->FinaltotalInvoice)/100); Updated by Rakesh on 17.08.2012
                    /*$bank_transfer_price='<table class="change_order_items" width="100%">
                                                <tr>
                                                <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Frais de virement &eacute;tranger 16 + (1% x Montant &agrave; verser &agrave; l\'auteur))</td>
													<td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($bank_charges,2,',','').' '.$$currency.'</td>
                                                </tr>
                                            </table>';*/
						/* $total_transfer_amount_final=$this->_view->FinaltotalInvoice+$bank_charges;
                    $total_transfer_amount='<table class="change_order_items" width="100%">
                                                <tr>
													<td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Final Amount</td>
													<td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($total_transfer_amount_final,2).' '.$$currency.'</td>
                                                </tr>
												</table>'; */


					$bank_codes=explode("|",$profile_contribinfo[0]['rib_id']);  
						
                    $remuneration="BIC : ".$bank_codes[0]."&nbsp;&nbsp;&nbsp;IBAN : ".$bank_codes[1]; 
					$profile_contribinfo[0]['rib_id']= str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']);                
					$profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['rib_id'];
						
                }  
                else
                {	  
					$bank_codes=explode("|",$profile_contribinfo[0]['rib_id']);  
					$profile_contribinfo[0]['rib_id']= str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']); 

					if(count($bank_codes)<5)
                        $remuneration="BIC : ".$bank_codes[0]."&nbsp;&nbsp;&nbsp;IBAN : ".$bank_codes[1]; 
					else 
						$remuneration="RIB : ".str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']);

					
		$profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['rib_id'];
                //}   
                $bank_account_name="Account name : ".$profile_contribinfo[0]['bank_account_name'].'<br>';
                //$mode="Mode de paiement : <strong>VIREMENT</strong>";
					
            }
				
				//$this->_view->totalInvoice=str_replace(",",".",$this->_view->totalInvoice);
            //echo $profile_contribinfo[0]['pay_info_type'] ."-". $this->_view->totalInvoice."-". $profile_contribinfo[0]['payment_info_id'];
			//exit;
           // if($profile_contribinfo[0]['pay_info_type'] && $this->_view->totalInvoice >=$this->config['contrib_minamount'] && $profile_contribinfo[0]['payment_info_id']) // commented by Rakesh
			 if($profile_contribinfo[0]['pay_info_type'] && $total >=$this->config['contrib_minamount'] && $profile_contribinfo[0]['payment_info_id'])
            {
                    /**INserting INvoice Details**/
                    $invoice=new Ep_Royalty_Invoice();
                    //$invoiceId=uniqid();
						//$invoice_count=$invoice->getMonthlyCount($contrib_identifier);						
						//$cnt=$invoice_count+1;
	                       	                if($currency=='euro')
							$cnt=1;
	                       	                else if($currency=='pound')
							$cnt=2;
                    $invoiceId=date("Y-m-").$cnt."-".$contrib_identifier;
                    
                    $invoicedir=APP_PATH_ROOT.'invoice/contributor/'.$contrib_identifier.'/';
            
                    if(!is_dir($invoicedir))
                        mkdir($invoicedir,TRUE);
                        chmod($invoicedir,0777);
                    $invoice->invoiceId='ep_invoice_'.$invoiceId;
                     $invoice->user_id=$contrib_identifier;
                    $invoice->total_invoice=$this->_view->totalInvoice;
                    $invoice->total_invoice_paid=$this->_view->FinaltotalInvoice;
                    $invoice->tax=$this->_view->totalTax;
                    $invoice->payment_info_type=$profile_contribinfo[0]['pay_info_type'];
                $invoice->vat_check=NULL;
                    $invoice->payment_type=$profile_contribinfo[0]['payment_type'];
                    $invoice->payment_info_id=$profile_contribinfo[0]['payment_info_id'];
                    $invoice->invoice_path='contributor/'.$contrib_identifier.'/'.$invoiceId.'.pdf';
                    $invoice->status='Not paid';
					$invoice->currency=$currency;
                    $invoice->created_at=date("Y-m-d H:i:s", time());
					
					//Added w.r.t new admin fees
                    $invoice->ep_admin_fee='yes';
                $invoice->ep_admin_fee_percentage=$admin_fee_percentage;
                    $invoice->pay_later_month=$period_month;
                    $invoice->pay_later_percentage=$fees_percentage;
                $invoice->bank_account_name=$profile_contribinfo[0]['bank_account_name'];
					
                    //print_r($invoice);exit;
                    $invoice->insert();
                    /**Update Royalties Table**/
                    $data['invoiceId']=$invoice->invoiceId;
                        $where="user_id='".$contrib_identifier."' and invoiceId IS NULL  and currency='".$currency."'";
		    $royalty->updateInvoice($data,$where);
            
                   require_once(APP_PATH_ROOT.'dompdf/dompdf_config.inc.php');
                      
                    $html=file_get_contents(SCRIPT_VIEW_PATH.'Contrib/invoice_pdf.phtml');
                    //eval("\$html= \"$html\";");
                    $html=str_replace('$$$$invoice_details_pdf$$$$',$invoice_details_pdf,$html);
                    $html=str_replace('$$$$tax_details_pdf$$$$',$tax_details_pdf,$html);
                    $html=str_replace('$$$$final_invoice_amount$$$$',$final_invoice_amount,$html);
                    $html=str_replace('$$$$date_invoice_full$$$$',$date_invoice_full,$html);
                    $html=str_replace('$$$$date_invoice$$$$',$date_invocie,$html);
                    $html=str_replace('$$$$address$$$$',$address,$html);
                    $html=str_replace('$$$$payinfo_number$$$$',$payinfo_number,$html);
                    $html=str_replace('$$$$date_invoice_ep$$$$',$date_invoice_ep,$html);
                    $html=str_replace('$$$$invoice_identifier$$$$',$invoiceId,$html);
                $html=str_replace('$$$$bank_account_name$$$$',$bank_account_name,$html);
                    $html=str_replace('$$$$remuneration$$$$',$remuneration,$html);
                    $html=str_replace('$$$$bank_transfer_price$$$$',$bank_transfer_price,$html);
                    $html=str_replace('$$$$total_transfer_amount$$$$',$total_transfer_amount,$html);
						$html=str_replace('$$$$full_name$$$$',$full_name,$html);						
                    $html =str_replace('<tbody>', '', $html);
                    $html =str_replace('</tbody>', '', $html);
                    
                    if ( get_magic_quotes_gpc() )
                        $html = stripslashes($html);
                    
                   // echo $html;exit;
                    $dompdf = new DOMPDF();
                    $dompdf->load_html( $html);
                    $dompdf->set_paper("a4");
                    $dompdf->render();
                    // $dompdf->stream("dompdf_out.pdf");
                    $pdf = $dompdf->output();
                    file_put_contents($invoicedir.'/'.$invoiceId.'.pdf', $pdf);
                    // file_put_contents($invoicedir.'/test.pdf', $pdf);
                    $this->_redirect("/contrib/royalties");
                    exit(0);
			}
				else
					$this->_redirect("/contrib/royalties");
			}
				else
					$this->_redirect("/contrib/royalties");	
			}
		}
		}
	}
    public function regeneratepdfAction()
     {
          if($this->_helper->EpCustom->checksession())
          {
              $invoice_params=$this->_request->getParams();
			  $pound='&#163;';
			  $euro='&#x80;';
              if($invoice_params['invoiceid'])
              {
                  $invoiceId=$invoice_params['invoiceid'];
              }
              else
              {
                  $this->_redirect("/contrib/royalties");
                  exit;
              }
              /***Profile Info***/
              //setlocale(LC_TIME, 'fr_FR');
              $date_invoice_full= strftime("%B %Y");
              $date_invocie = date("d/m/Y");
              $date_invoice_ep=date("Y/m");
              $profileplus_obj = new Ep_Contrib_ProfilePlus();
              $profileContrib_obj = new Ep_Contrib_ProfileContributor();
              $contrib_identifier= $this->contrib_identifier;
              $invoiceuser=new Ep_Royalty_Invoice();
              
              if($profileplus_obj->checkProfileExist($contrib_identifier)!='NO')
              {
                  $profile_identifier_info=$profileplus_obj->checkProfileExist($contrib_identifier);
                  $profile_identifier=$profile_identifier_info[0]['user_id'];
                  $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);				  
                   /**iNOVICE inFO ***/
                  $this->_view->ep_contrib_profile_pay_info_type=$profile_contribinfo[0]['pay_info_type'];
                  $this->_view->ep_contrib_profile_SSN=$profile_contribinfo[0]['SSN'];
                  $this->_view->ep_contrib_profile_company_number=$profile_contribinfo[0]['company_number'];
                  $this->_view->ep_contrib_profile_vat_check=$profile_contribinfo[0]['vat_check'];
                  $this->_view->ep_contrib_profile_VAT_number=$profile_contribinfo[0]['VAT_number'];
                  $profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
                  $address='<b>'.$profileinfo[0]['first_name'].' '.$profileinfo[0]['last_name'].'</b><br><br>';
                  $address.=$profileinfo[0]['address'].'<br>';
                  $address.=$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city'].'  '.$this->getCountryName($profileinfo[0]['country']).'<br>';
					//$address.='Phone :'.$profileinfo[0]['phone_number'].'<br>';
					//$address.='Email :'.$this->EP_Contrib_reg->clientemail;
					
					$full_name='<b>'.$profileinfo[0]['first_name'].' '.$profileinfo[0]['last_name'];
              }
              /**ENDED**/
              $identifier= $contrib_identifier;
              $royalty=new Ep_Royalty_Royalties();
              $ticket_obj=new Ep_Ticket_Ticket();
              
              $invoiceDetails=$royalty->getInvoiceDetails($identifier, 'ep_invoice_'.$invoiceId);
              
              $date_invoice_full= strftime("%B %Y",strtotime($invoiceDetails[0]['created_at']));
              $date_invocie = date("d/m/Y",strtotime($invoiceDetails[0]['created_at']));
              $date_invoice_ep=date("Y/m",strtotime($invoiceDetails[0]['created_at']));
              $invoice_details_pdf='<table class="change_order_items">
                                      <tbody>
                                          <tr>
                                              <th style="border-bottom:1px solid black;font-size: 12pt;">Description</th>
                                              <th style="border-bottom:1px solid black;font-size: 12pt;">Amount</th>
                                          </tr>';
              
              if(count($invoiceDetails)>0 && is_array($invoiceDetails) && $invoiceDetails[0]['status']=='refuse' )
              {
                  $total=0;
                  foreach( $invoiceDetails as $details)
                  {
                      $currency=$details['currency'];
                      $total+=$details['price'];

                    $client_id= $details['client_id'];
                    $client_name=$ticket_obj->getUserName($client_id);
                    $article_created_date=ucfirst(strftime("%b %Y",strtotime($details['article_created_date'])));

                      $invoice_details_pdf.='<tr>
                                              <td style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;">'.$details['AOTitle'].' / '.$client_name.' / '.$article_created_date.'</td>
                                              <td style="padding: 0.5em;padding-right: 4pt;text-align: right;font-size: 12pt;" class="change_order_total_col">'.number_format($details['price'],2).'</td>
                                              </tr>';
                  }
                  $invoice_details_pdf.='<tr>
                                          <td style="border-right: 1px solid black;border-top:1pt solid black;text-align:right;margin-right:10px;font-size: 12pt;">Total</td>
                                          <td style="border-top:1pt solid black;font-size: 12pt;" class="change_order_total_col">'.number_format($total,2).'</td>
                                          </tr>
                                      </tbody>
                                  </table>';
                 /**Total Invoice*/
                  $this->_view->totalInvoice=number_format($total,2,'.','');
                  /**Tax Calculation */
                  $totalTax=0;
                  $tax_details_pdf='';
                  $payinfo_number='';
                  /* if($profile_contribinfo[0]['pay_info_type']=='ssn')
                  {
                      $veuvage=number_format((($total*0.85)/100),2);
                      $csg=number_format((($total*7.36875)/100),2);
                      $crds=number_format((($total*0.49125)/100),2);
                      $formation=number_format((($total*0.35)/100));
                      $tax_date=date("Y-m-d");
                      if($tax_date < date("Y-m-d",strtotime('2012-07-01')))
                      {
                          $formation=0;
                      }
                      
                      $totalTax=$veuvage+$csg+$crds+$formation;
                      $tax_details_pdf='<table class="change_order_items">
                                          <tbody>
                                          <tr>
                                          <th style="border-bottom:1px solid black;font-size: 12pt;">PRECOMPTE</th>
                                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                                          </tr>
                                          <tr>
                                          <td style="border-right: 1px solid black;padding: 0.5em;width:60%;">Cotisation maladie veuvage</td>
                                          <td style="border-right: 1px solid black;padding: 0.5em;width:25%;">taux : 0,85%</td>
                                          <td class="change_order_total_col" style="width:15%;">'.number_format((($total*0.85)/100),2).'</td>
                                          </tr>
                                          <tr>
                                          <td style="border-right: 1px solid black;padding: 0.5em;">CSG</td>
                                          <td style="border-right: 1px solid black;padding: 0.5em;">taux : 7,36875%</td>
                                          <td class="change_order_total_col">'.number_format((($total*7.36875)/100),2).'</td>
                                          </tr>
                                          <tr>
                                          <td style="border-right: 1px solid black;padding: 0.5em;">CRDS</td>
                                          <td style="border-right: 1px solid black;padding: 0.5em;">taux : 0,49125% </td>
                                          <td class="change_order_total_col">'.number_format((($total*0.49125)/100),2).'</td>
                                          </tr>
                                          </table>';
                      if($tax_date >= date("Y-m-d",strtotime('2012-07-01')))
                      {
                          $tax_details_pdf.='<table class="change_order_items">
                                          <tr>
                                          <td style="border-right: 1px solid black;padding: 0.5em;width:60%;">Formation Professionnelle</td>
                                          <td style="border-right: 1px solid black;padding: 0.5em;width:25%;">taux : 0,35% </td>
                                          <td class="change_order_total_col" style="width:15%;">'.number_format((($total*0.35)/100),2).'</td>
                                          </tr>
                                          </table>';
                      }
                      $tax_details_pdf.='<table class="change_order_items">
                                          <tr>
                                          <td colspan="2" style="width:85%;border-right: 1px solid black;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;text-align:right;margin-right:10px">A VERSER A L\'AGESSA</td>
                                          <td style="width:15%;font-weight:bold;border-top:1pt solid black;padding-right:10pt;text-align: right;font-size: 12pt;">'.number_format($totalTax,2).' '.$$currency.'</td>
                                          </tr>
                                          </tbody>
                                          </table>';
                      $payinfo_number="N&deg; de  S&eacute;curit&eacute; sociale : ".$profile_contribinfo[0]['SSN']."<br>";
                  }
                else if($invoiceDetails[0]['payment_info_type']=='comp_num' && $invoiceDetails[0]['vat_check']=='YES' )
                  {
                      $TVA=number_format((($total*19.6)/100),2);
                      $totalTax=$TVA;
                      $tax_details_pdf='<table class="change_order_items">
                                          <tbody>
                                          <tr>
                                          <th style="border-bottom:1px solid black;font-size: 12pt;">PRECOMPTE</th>
                                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                                          <th style="border-bottom:1px solid black;font-size: 12pt;"></th>
                                          </tr>
                                          <tr>
                                          <td style="border-right: 1px solid black;padding: 0.5em;">TVA</td>
                                          <td style="border-right: 1px solid black;padding: 0.5em;">taux : 19,6%</td>
                                          <td class="change_order_total_col" >'.number_format((($total*19.6)/100),2).'</td>
                                          </tr>
                                          <tr>
                                          <td colspan="2" style="width:85%;border-right: 1px solid black;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;text-align:right;margin-right:10px">A VERSER A L\'AGESSA</td>
                                          <td style="width:15%;font-weight:bold;border-top:1pt solid black;padding-right:10pt;text-align: right;font-size: 12pt;">'.number_format($totalTax,2).' '.$$currency.'</td>
                                          </tr>
                                          </tbody>
                                          </table>';
                      //$payinfo_number="Siret : ".$profile_contribinfo[0]['company_number']."<br>";
                  } if($invoiceDetails[0]['payment_info_type']=='comp_num')
                    $payinfo_number="Siret : ".$profile_contribinfo[0]['company_number']."<br>";*/

                if($invoiceDetails[0]['payment_info_type']=='ep_admin') //Added w.r.t new admin fees
                {
                    $admin_fee_percentage=$invoiceDetails[0]['ep_admin_fee_percentage'];
                    $epTax=number_format((($total*$admin_fee_percentage)/100),2,'.','');
                    $totalTax=$epTax;

                    $tax_details_pdf='<table class="change_order_items">
                                            <tbody>                                            
                                            <tr>
                          <td colspan="2" style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;">Transfer and administrative charges : '.$admin_fee_percentage.'%</td>
                          
                          <td style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;" class="change_order_total_col">'.number_format((($total*$admin_fee_percentage)/100),2,'.','').'</td>
                                            </tr>                                            
                                            </tbody>
                                            </table>';
                }
                if($invoiceDetails[0]['ep_admin_fee']=='yes' && $invoiceDetails[0]['pay_later_month'])
                {
                    $period_month=$invoiceDetails[0]['pay_later_month'];
					$fees_percentage=$invoiceDetails[0]['pay_later_percentage'];

					if($period_month==1)
					{                          
						$fees_paid_month=strftime( '%B', strtotime( '+1 month', strtotime($invoiceDetails[0]['created_at'])));
					}    
					if($period_month==2)
					{                          
						$fees_paid_month=strftime( '%B', strtotime( '+2 month',strtotime($invoiceDetails[0]['created_at'])));
					}    
					    

                     $period_fees=number_format(((($total-$totalTax)*$fees_percentage)/100),2,'.',''); 
                     
                      $tax_details_pdf.='<table class="change_order_items">
                                            <tbody>                                                                                       
                                            <tr>
                          <td colspan="2" style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;">Advance payment charge : '.$fees_percentage.'% (payment on the 15th '.$fees_paid_month.' ) </td>
                          
                          <td style="border-right: 1px solid black;padding: 0.5em;font-size: 12pt;" class="change_order_total_col">'.number_format($period_fees,2,'.','').'</td>
                                            </tr>                                            
                                            </tbody>
                                            </table>'; 

                      $totalTax=$totalTax+$period_fees;                      

                }		  
               
                $this->_view->totalTax=$totalTax;
				
				if($invoiceDetails[0]['payment_info_type']=='ssn')
					$this->_view->FinaltotalInvoice=number_format(($total-$totalTax),2,'.','');
				else if($invoiceDetails[0]['payment_info_type']=='comp_num' && $invoiceDetails[0]['vat_check']=='YES' )
					$this->_view->FinaltotalInvoice=number_format(($total+$totalTax),2,'.','');
				else if($invoiceDetails[0]['payment_info_type']=='ep_admin')
					$this->_view->FinaltotalInvoice=number_format(($total-$totalTax),2,'.','');   
				else
					$this->_view->FinaltotalInvoice=number_format($total,2,'.','');
                 $final_invoice_amount='<table class="change_order_items" width="100%">
                                          <tr>
                                          <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Total Amount payable to the writer</td>
                                          <td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($this->_view->FinaltotalInvoice,2).' '.$$currency.'</td>
                                          </tr>
                                         </table>';
                    /**Wire OR paypal info**/
                  $total_transfer_amount='';
                  $bank_transfer_price='';
                         if($invoiceDetails[0]['payment_type']=="paypal")
                         {
                             $bank_charges=0;//0.25+((3.4*$this->_view->FinaltotalInvoice)/100);
                              /*$bank_transfer_price='<table class="change_order_items" width="100%">
                                                          <tr>
                                                          <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Frais de virement paypal 0,25 + (3,4% x Montant &agrave; verser &agrave; l\'auteur)</td>
                                                          <td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($bank_charges,2,',','').' '.$$currency.'</td>
                                                          </tr>
                                                      </table>';*/
                              $total_transfer_amount_final=$this->_view->FinaltotalInvoice+$bank_charges;
                              $total_transfer_amount='<table class="change_order_items" width="100%">
                                                          <tr>
                                                          <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Final Amount</td>
                                                          <td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($total_transfer_amount_final,2).' '.$$currency.'</td>
                                                          </tr>
                                                      </table>';
                             $remuneration="Paypal : ".$profile_contribinfo[0]['paypal_id'];
                             $profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['paypal_id'];
                             $mode="Mode de paiement : <strong>PAYPAL</strong> ";
                         }
                         else if($invoiceDetails[0]['payment_type']=="virement")
                         {
                              if($invoiceDetails[0]['payment_info_type']=='out_uk')
                              {
                                  $bank_charges=0;//16+((1*$this->_view->FinaltotalInvoice)/100); Updated by Rakesh on 17.08.2012
                                  /*$bank_transfer_price='<table class="change_order_items" width="100%">
                                                              <tr>
                                                              <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Frais de virement &eacute;tranger 16 + (1% x Montant &agrave; verser &agrave; l\'auteur))</td>
                                                              <td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($bank_charges,2,',','').' '.$$currency.'</td>
                                                              </tr>
                                                          </table>';*/
                                  /*$total_transfer_amount_final=$this->_view->FinaltotalInvoice+$bank_charges;
                                  $total_transfer_amount='<table class="change_order_items" width="100%">
                                                              <tr>
                                                              <td  style="width:85%;border-right: 1px solid black;width:82%;font-size: 12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">MONTANT FINAL</td>
                                                              <td  style="width:15%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format($total_transfer_amount_final,2).' '.$$currency.'</td>
                                                              </tr>
                                
                                                          </table>';*/
              
								$bank_codes=explode("|",$profile_contribinfo[0]['rib_id']);  						
                        if(count($bank_codes)<5)
                            $remuneration="BIC : ".$bank_codes[0]."&nbsp;&nbsp;&nbsp;IBAN : ".$bank_codes[1];
                        else
                            $remuneration="RIB : ".str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']);
								$profile_contribinfo[0]['rib_id']= str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']);                
								$profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['rib_id'];
									
							}  
							else
							{	  
								$bank_codes=explode("|",$profile_contribinfo[0]['rib_id']);  								
								$profile_contribinfo[0]['rib_id']= str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']); 

								if(count($bank_codes)<5)
								$remuneration="BIC : ".$bank_codes[0]."&nbsp;&nbsp;&nbsp;IBAN : ".$bank_codes[1];
								else 
									$remuneration="RIB : ".str_ireplace("|",' ',$profile_contribinfo[0]['rib_id']);

								$profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['rib_id'];
							}   
                           
							$profile_contribinfo[0]['payment_info_id']=$profile_contribinfo[0]['rib_id'];
							$bank_account_name="Account name : ".$profile_contribinfo[0]['bank_account_name'].'<br>';
                         }
						
                      $invoice=new Ep_Royalty_Invoice();
                      $updated_at=date("Y-m-d %h:%i:%s");
                      $invoice_identifier="ep_invoice_".$invoiceId;
                      $invoiceId_array=explode("-",$invoiceId);
                      if($invoiceDetails[0]['refuse_count']>1)
                      {
                          $rcnt="R".(int)($invoiceDetails[0]['refuse_count']-1);
                      }
                      else
                          $rcnt="R";
                      $invoiceId_new=$invoiceId_array[0]."-".$invoiceId_array[1]."-".$invoiceId_array[2].$rcnt."-".$invoiceId_array[3];
                      $data = array("total_invoice"=>$this->_view->totalInvoice,
                                    "total_invoice_paid"=>$this->_view->FinaltotalInvoice,
                                    "tax"=>$this->_view->totalTax,
                                    "payment_info_type"=>$invoiceDetails[0]['payment_info_type'],
                                    "vat_check"=>$invoiceDetails[0]['vat_check'],
                                    "payment_type"=>$invoiceDetails[0]['payment_type'],
                                    "payment_info_id"=>$profile_contribinfo[0]['payment_info_id'],
                                    "invoice_path"=>'contributor/'.$contrib_identifier.'/'.$invoiceId_new.'.pdf',
                                    "status"=>'Not paid',
									"ep_admin_fee"=>$invoiceDetails[0]['ep_admin_fee'],
                                    "ep_admin_fee_percentage"=>$invoiceDetails[0]['ep_admin_fee_percentage'],
                                    "pay_later_month"=>$invoiceDetails[0]['pay_later_month'],
                                    "pay_later_percentage"=>$invoiceDetails[0]['pay_later_percentage'],
                    "bank_account_name"=>$profile_contribinfo[0]['bank_account_name'],
                                    "updated_at"=>$updated_at);////////updating
                      $invoice->updateInvoiceDetails($invoice_identifier,$data);
                      $invoicedir=APP_PATH_ROOT.'invoice/contributor/'.$contrib_identifier.'/';
                     
                      if(!is_dir($invoicedir))
                    mkdir($invoicedir,TRUE);
                      chmod($invoicedir,0777);
                         require_once(APP_PATH_ROOT.'dompdf/dompdf_config.inc.php');
                        
                      $html=file_get_contents(SCRIPT_VIEW_PATH.'Contrib/invoice_pdf.phtml');
                      //eval("\$html= \"$html\";");
                      $html=str_replace('$$$$invoice_details_pdf$$$$',$invoice_details_pdf,$html);
                      $html=str_replace('$$$$tax_details_pdf$$$$',$tax_details_pdf,$html);
                      $html=str_replace('$$$$final_invoice_amount$$$$',$final_invoice_amount,$html);
                      $html=str_replace('$$$$date_invoice_full$$$$',$date_invoice_full,$html);
                      $html=str_replace('$$$$date_invoice$$$$',$date_invocie,$html);
                      $html=str_replace('$$$$address$$$$',$address,$html);
                      $html=str_replace('$$$$payinfo_number$$$$',$payinfo_number,$html);
                      $html=str_replace('$$$$date_invoice_ep$$$$',$date_invoice_ep,$html);
                      $html=str_replace('$$$$invoice_identifier$$$$',$invoiceId_new,$html);
						$html=str_replace('$$$$bank_account_name$$$$',$bank_account_name,$html);
                      $html=str_replace('$$$$remuneration$$$$',$remuneration,$html);
                      $html=str_replace('$$$$bank_transfer_price$$$$',$bank_transfer_price,$html);
                      $html=str_replace('$$$$total_transfer_amount$$$$',$total_transfer_amount,$html);
					  $html=str_replace('$$$$full_name$$$$',$full_name,$html);	
                       $html =str_replace('<tbody>', '', $html);
                      $html =str_replace('</tbody>', '', $html);
                      
                      if ( get_magic_quotes_gpc() )
                          $html = stripslashes($html);
						//echo $html;exit;
                       
                     // $old_limit = ini_set("memory_limit", "24M");
                        $dompdf = new DOMPDF();
                        $dompdf->load_html( $html);
                        $dompdf->set_paper("a4");
                        $dompdf->render();
                       // $dompdf->stream("dompdf_out.pdf");
                         $pdf = $dompdf->output();
                         file_put_contents($invoicedir.'/'.$invoiceId_new.'.pdf', $pdf);
                         // file_put_contents($invoicedir.'/test.pdf', $pdf);
                        $this->_redirect("/contrib/royalties");
                         exit(0);
              }
              else
              {
                  
                  $this->_redirect("/contrib/royalties");
                  exit;
              }
          }
     }
    /**mail box functionalities***/
    //Compose email
    public function composeMailAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
             $ticket=new Ep_Ticket_Ticket();
            $mail_params=$this->_request->getParams();
            $contrib_identifier=$this->contrib_identifier;
            /**profile IMage*/
              $this->_view->profile_picture=$this->getPicPath($contrib_identifier);  
            //for disaplying action messages
            if($this->_helper->FlashMessenger->getMessages()) {
                    $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }
            
            
            $mail=new Ep_Ticket_Ticket();
            $ongoingWriterContacts=array();            
            $ongoingWriterContacts=$mail->ongoingWriterContacts($contrib_identifier);
            $ongoingCorrectorContacts=array();
            $ongoingCorrectorContacts=$mail->ongoingCorrectorContacts($contrib_identifier);
            
                       
            $ongoingPollContacts=array();
            /*$ongoingPollDetails=$this->pollAoSearch($poll_params);
            if(count($ongoingPollDetails)>0)
            {
                $p=0;
                foreach($ongoingPollDetails as $poll)
                {                    
                    $ongoingPollContacts[$p]['title']=$poll['title'];
                    $ongoingPollContacts[$p]['created_user']=$poll['created_by'];
                    $p++;
                }
                
            }*/
            
            $ongoingContacts=array_merge($ongoingWriterContacts,$ongoingCorrectorContacts,$ongoingPollContacts);            
          
            /*$get_contacts=$mail->getContacts('client');
            if($get_contacts!='Not Exists')
            {
                foreach($get_contacts as $contact)
                {
                    if($contact['contact_name']!=NULL)
                        $clients_contacts[$contact['identifier']]=$contact['contact_name'];
                    else
                    {
                        $contact['email']=explode("@",$contact['email']);
                        $clients_contacts[$contact['identifier']]=$contact['email'][0];
                    }
                }
            }
            //Edit-Place Contacts
            $get_EP_contacts=$mail->getEPContacts('"salesuser","partner","customercare","facturation"');
            foreach($get_EP_contacts as $contact)
            {
                if($contact['contact_name']!=NULL)
                    $EP_contacts[$contact['identifier']]=$contact['contact_name'];
                else
                {
                    $contact['email']=explode("@",$contact['email']);
                    $EP_contacts[$contact['identifier']]=$contact['email'];
                }
            }
            if($EP_contacts!=='Not Exists')
                $this->_view->EP_contacts=$EP_contacts;
            if($clients_contacts!=='Not Exists')
                $this->_view->Cients_contacts=$clients_contacts;*/


            // echo "<pre>";print_r($ongoingContacts);exit; 

             $this->_view->ongoingContacts=$ongoingContacts;   

            if($mail_params['senduser'])
                $this->_view->touser=$mail_params['senduser'];
            if($mail_params['obj']=='complaint')  
                $this->_view->mail_object='Faire une r&eacute;clamation';
			if($mail_params['invoice_id'])   
                $this->_view->mail_object.= ' #'.$mail_params['invoice_id'];	
            //classified tickets Count
            $class_tickets= $ticket->getClassifyTicket($contrib_identifier);
            if(is_array($class_tickets))
            $this->_view->class_ticket_count=count($class_tickets);
            $this->_view->meta_title="Mailbox";  
            $this->render("Conrib_compose_mail");
        }  
    }
    //send mail action
    public function sendMailAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            if($this->_request-> isPost())
            {
                $sender=$this->contrib_identifier;
                $ticket_params=$this->_request->getParams();
                $ticket=new Ep_Ticket_Ticket();
                $user_obj = new EP_Contrib_Registration();
                $delivery_obj= new Ep_Article_Delivery();


                if($ticket_params["sendto"])
                {
                  $article_id=$recipient=$ticket_params["sendto"];

                  $delivery_details=$delivery_obj->getDeliveryDetails($recipient);
                  if($delivery_details!="NO" && is_array($delivery_details))
                  {
                     $recipient=$delivery_details[0]['created_user'];
                     $ticket->article_id=$article_id;
                  }
                  $userInfo=$user_obj->getUserInfo($recipient);
                }                
                $ticket->sender_id=$sender;
                if($recipient && $userInfo[0]['type']=='client')
                {
                    $ticket->recipient_id=$recipient;
                    $ticket->bo_user_action_type=NULL;
                }
                if($recipient && $userInfo[0]['type']!='client' && $userInfo[0]['type']!='contributor' )
                {
                    $ticket->recipient_id=$recipient;
                    $ticket->bo_user_action_type='recipient';
                }



                $ticket->title=$this->utf8dec($ticket_params['mail_object']);
                $ticket->status='0';
                $ticket->created_at=date("Y-m-d H:i:s", time());
                try
                {
                    //print_r($ticket);
                    if($ticket->insert())
                    {
                        $ticket_id=$ticket->getIdentifier();
                        $message=new Ep_Ticket_Message();
                        $message->ticket_id=$ticket_id;
                        $message->content=($this->utf8dec($ticket_params["mail_message"]));
                        $message->type='0' ;
                        $message->status='0';
                        $message->created_at=$ticket->created_at;
                        if($userInfo[0]['type']=='client')
                        {
                            //$message->approved=NULL;
                          $message->approved='yes';
                            $message->bo_user_type=NULL;
                        }
                        else
                        {
                            $message->approved='yes';
                            $bo_user_type=$ticket->getBoUserType($ticket->recipient_id);
                            if($bo_user_type)
                                $message->bo_user_type=$bo_user_type;
                            else
                                $message->bo_user_type=NULL;
                        }
                        
                        if($_FILES['attachment']['name'][0]!=NULL)
                        {
                            $file_attachemnts='';
                            $cnt=1;
                            foreach($_FILES['attachment']['name'] as $file)
                            {
                                $file_attachemnt[$cnt-1]=$message->getIdentifier()."_".$cnt."_".$this->utf8dec($file);
                                $file_attachemnts.= $message->getIdentifier()."_".$cnt."_".$this->utf8dec($file)."|";
                                $cnt++;
                            }
                            $file_attachemnts=substr($file_attachemnts,0,-1);
                            $message->attachment=$file_attachemnts;
                        }
                        if($message->insert())
                        {
                            /**Sending notification mail to notify uses if mail send to BO users**/
                            if($recipient && $userInfo[0]['type']!='client' && $userInfo[0]['type']!='contributor')
                            {
                                $auto_mail=new Ep_Ticket_AutoEmails();
                                $auto_mail->sendNotificationEmail($recipient);
                            }
                            //$auto_mail->sendAutoPersonalEmail($ticket->recipient_id,$ticket->title,$message->getIdentifier(),$ticket_id);
                            $attachment=new Ep_Ticket_Attachment();
                            if($_FILES['attachment']['name'][0]!=NULL)
                            {
                                $fileCount=0;
                                foreach($_FILES['attachment']['tmp_name'] as $file)
                                {
                                    $attachFile['tmp_name']=$file;
                                    $attachment->uploadAttachment($this->attachment_path,$attachFile,$file_attachemnt[$fileCount]);
                                    $fileCount++;
                                }
                            }
                            //$this->_helper->FlashMessenger('Message sent.');
                            $this->_helper->FlashMessenger("Your message has been sent successfully. Our team will deal with it within 24h maximum.");
                            $this->_redirect("/contrib/sentbox");
                        }
                    }
                }
                catch(Exception $e)
                {
                    echo $e->getMessage();
                }
            }    
        }
    }   
    //inbox action
    public function inboxAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $contrib_identifier=$this->contrib_identifier;
            $ticket=new Ep_Ticket_Ticket();
            $inbox_messages= $ticket->getUserInbox('client',$contrib_identifier);
            if($this->_helper->FlashMessenger->getMessages()) {
                $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }
            //$inbox_messages=print_r($inbox_messages,true);
			//echo "<pre>";print_r($inbox_messages);
            if(is_array($inbox_messages) && count($inbox_messages)>0)
            {
                $i=0;
                foreach($inbox_messages as $message)
                {
                    
                    $message['content']=html2txt($message['content']);
                    if(strlen($message['content']) > 100)
                    {
                        $inbox_messages[$i]['text_message']=strip_tags(stripslashes(substr(trim($message['content']),0,99)));
                        $inbox_messages[$i]['read_more']=TRUE;
                    }
                    else{
                        $inbox_messages[$i]['text_message']=strip_tags(stripslashes(trim($message['content'])));
                        $inbox_messages[$i]['read_more']=FALSE;                        
                    }                    
                    
                   //$inbox_messages[$i]['sendername']=$ticket->getUserName($message['userid']);

                    if($message['type']=='superadmin')
                       $inbox_messages[$i]['sendername']="Support Edit-Place"; 
                    else if($message['article_id']) 
                    {
                        $delivery_obj=new Ep_Article_Delivery();
                        $delivery_details= $delivery_obj->getDeliveryDetails($message['article_id']);
                        if($delivery_details!="NO")
                          $inbox_messages[$i]['sendername']=$delivery_details[0]['articleName'];
                        else
                          $inbox_messages[$i]['sendername']=$ticket->getUserName($message['userid']);
                        
                    }                                          
                    else 
                      $inbox_messages[$i]['sendername']=$ticket->getUserName($message['userid']);

                    $i++;
                }
                $page = $this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($inbox_messages);
                $paginator->setItemCountPerPage($this->config['pagination_fo']);
                $paginator->setCurrentPageNumber($page);
                //$this->_view->pagination=print_r($paginator->getPages(),true);
                //$patterns='/[? &]page=[\d{1,2}]/';
                $patterns='/[? &]page=[0-9]{1,2}/';
                $replace="";
                $this->_view->paginator = $paginator;
                $this->_view->pages = $paginator->getPages();
                $this->_view->pageURL=preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
                //$this->_view->Inbox_Messages=$inbox_messages;
                
                $this->_view->InboxCount=count($inbox_messages);
            }
            else
                $this->_view->Inbox_Messages="You have no messages";
            //classified tickets Count
            $class_tickets= $ticket->getClassifyTicket($contrib_identifier);
            if(is_array($class_tickets))
            $this->_view->class_ticket_count=count($class_tickets);  
            $this->_view->meta_title="Mailbox";
            $this->render("Contrib_inbox");
        }
    }  
    
    //sentbox action
    public function sentboxAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $ticket=new Ep_Ticket_Ticket();
            $contrib_identifier=$this->contrib_identifier;
            $sent_messages= $ticket->getUserSentBox('contributor',$contrib_identifier);
            if($this->_helper->FlashMessenger->getMessages()) {
                $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }
            //$inbox_messages=print_r($inbox_messages,true);
            if(is_array($sent_messages) && count($sent_messages)>0)
            {
                $i=0;
                foreach($sent_messages as $message)
                {
                    $message['content']=html2txt($message['content']);
                    if(strlen($message['content']) > 100)
                    {
                        $sent_messages[$i]['text_message']=stripslashes(substr($message['content'],0,99));
                        $sent_messages[$i]['read_more']=TRUE;
                    }
                    else{
                        $sent_messages[$i]['text_message']=stripslashes($message['content']);
                        $sent_messages[$i]['read_more']=FALSE;
                    }
                    
                    if($message['type']=='superadmin')
                       $sent_messages[$i]['sendername']="Support Edit-Place"; 
                    else if($message['article_id']) 
                    {
                        $delivery_obj=new Ep_Article_Delivery();
                        $delivery_details= $delivery_obj->getDeliveryDetails($message['article_id']);
                         if($delivery_details!="NO")
                          $sent_messages[$i]['sendername']=$delivery_details[0]['articleName'];
                        else
                          $sent_messages[$i]['sendername']=$ticket->getUserName($message['userid']);
                        
                        
                    }                                          
                    else 
                      $sent_messages[$i]['sendername']=$ticket->getUserName($message['userid']);
                    $i++;
                }
                $page = $this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($sent_messages);
                $paginator->setItemCountPerPage($this->config['pagination_fo']);
                $paginator->setCurrentPageNumber($page);
                //$this->_view->pagination=print_r($paginator->getPages(),true);
                //$patterns='/[? &]page=[\d{1,2}]/';
                $patterns='/[? &]page=[0-9]{1,2}/';
                $replace="";
                $this->_view->paginator = $paginator;
                $this->_view->pages = $paginator->getPages();
                $this->_view->pageURL=preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
            }
            else
                $this->_view->sent_messages="You have no messages"; 
             //classified tickets Count
            $class_tickets= $ticket->getClassifyTicket($contrib_identifier);
            if(is_array($class_tickets))
            $this->_view->class_ticket_count=count($class_tickets); 
           
            $this->_view->meta_title="Mailbox";
            $this->render("Contrib_sentbox");
        } 
    }    
    //classify box
    public function classifyboxAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $contrib_identifier=$this->contrib_identifier;
            $ticket_obj=new Ep_Ticket_Ticket();            
            $class_ticket= $ticket_obj->getClassifyTicket($contrib_identifier);
            //$inbox_messages=print_r($inbox_messages,true);
            if(is_array($class_ticket) && count($class_ticket)>0)
            {
                $i=0;
                foreach($class_ticket as $ticket)
                {
                    $class_ticket[$i]['sendername']=$ticket_obj->getUserName($ticket['classified_by']);
                    $i++;
                }
                $page = $this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($class_ticket);
                $paginator->setItemCountPerPage($this->config['pagination_fo']);
                $paginator->setCurrentPageNumber($page);
                //$this->_view->pagination=print_r($paginator->getPages(),true);
                //$patterns='/[? &]page=[\d{1,2}]/';
                $patterns='/[? &]page=[0-9]{1,2}/';
                $replace="";
                $this->_view->paginator = $paginator;
                $this->_view->pages = $paginator->getPages();
                $this->_view->pageURL=preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
            }
            else
                $this->_view->ticket_classes="You have no messages";
            //classified tickets Count
            
            if(is_array($class_ticket))
            $this->_view->class_ticket_count=count($class_ticket); 
            
            $this->_view->meta_title="Mailbox";
            $this->render("Contrib_classifybox");
            
        } 
    }

    //archived messages of a Ticket
    public function classifyMessagesAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $ticket_params=$this->_request->getParams();
            if($ticket_params['ticket']!='')
            {
                $ticket=new Ep_Ticket_Ticket();
                $contrib_identifier=$this->contrib_identifier;
                $ticketId=$ticket_params['ticket'];

                $archieve_messages= $ticket->getUserClassifyBox($ticketId,$contrib_identifier);
                // /echo "<pre>";print_r($archieve_messages);
                if(is_array($archieve_messages) && count($archieve_messages)>0)
                {
                    $i=0;
                    foreach($archieve_messages as $message)
                    {                  
                        $archieve_messages[$i]['sendername']=$ticket->getUserName($message['userid']);
                        $archieve_messages[$i]['recipient']=$ticket->getUserName($message['receiverId']);
                        $this->_view->Identifier=$message['userid'];
                        $i++;
                    }                
                    $this->_view->archieve_messages = $archieve_messages;
                    
                }
                else
                    $this->_view->ticket_classes="You have no messages";

            }
            //echo "<pre>";print_r($archieve_messages);  
            $this->_view->profile_picture=$this->getPicPath($contrib_identifier); 

            $class_tickets= $ticket->getClassifyTicket($contrib_identifier);
            if(is_array($class_tickets))
              $this->_view->class_ticket_count=count($class_tickets); 

            $this->_view->meta_title="Mailbox"; 
            $this->render("Contrib_classify_messages");
        }   

    }

    //view email
    public function viewMailAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $contrib_identifier=$this->contrib_identifier;
            /**profile IMage*/
            $this->_view->profile_picture=$this->getPicPath($contrib_identifier);  
            
            $mail_params=$this->_request->getParams();
            
            $ticket=new Ep_Ticket_Ticket();
            $message=new Ep_Ticket_Message();
            if($mail_params['type']=="inbox" && $mail_params['message']!='' && $mail_params['ticket']!='')
            {
                
                $messageId=$mail_params['message'];
                $ticketId=$mail_params['ticket'];
                if(($viewMessage=$message->checkMessageInbox($contrib_identifier,$messageId,$ticketId))!=NULL)
                {
                    $message->updateMessageStatus($messageId);

                    if(is_array($viewMessage) && count($viewMessage)>0)
                    {
                       
                        if($viewMessage[0]['user_type']=='superadmin')
                            $viewMessage[0]['sendername']="Support Edit-Place"; 
                        else 
                          $viewMessage[0]['sendername']=$ticket->getUserName($viewMessage[0]['userid']);

                        $viewMessage[0]['text_message']=stripslashes ($viewMessage[0]['content']);
                       
                        if( $viewMessage[0]['attachment']!='')
                        {
                            $file_attachments=explode("|",$viewMessage[0]['attachment']);
                            $count=1;
                            foreach($file_attachments as $file_attachment)
                            {
                                if(file_exists($this->attachment_path.$file_attachment) && !is_dir($this->attachment_path.$file_attachment))
                                {
                                    $attachment_name[]=str_replace($messageId."_".$count."_",'',$file_attachment);
                                    $count++;
                                    $viewMessage[0]['attachment_name']=$attachment_name;
                                }
                            }
                        }
                    }
                }
                else
                {
                    $this->_redirect("/contrib/inbox");
                    exit;
                }
            }
            else if($mail_params['type']=="sentbox" && $mail_params['message']!='' && $mail_params['ticket']!='')
            {
                    
                    $messageId=$mail_params['message'];
                    $ticketId=$mail_params['ticket'];
                    if(($viewMessage=$message->checkMessageSentbox($contrib_identifier,$messageId,$ticketId))!=NULL)
                    {
                        
                        if(is_array($viewMessage) && count($viewMessage)>0)
                        {
                            if($viewMessage[0]['user_type']=='superadmin')
                              $viewMessage[0]['sendername']="Support Edit-Place"; 
                            else 
                              $viewMessage[0]['sendername']=$ticket->getUserName($viewMessage[0]['userid']);
                            $viewMessage[0]['text_message']=stripslashes ($viewMessage[0]['content']);
                            if( $viewMessage[0]['attachment']!='')
                            {
                                $file_attachments=explode("|",$viewMessage[0]['attachment']);
                                $count=1;
                                foreach($file_attachments as $file_attachment)
                                {
                                    if(file_exists($this->attachment_path.$file_attachment) && !is_dir($this->attachment_path.$file_attachment))
                                    {
                                        $attachment_name[]=str_replace($messageId."_".$count."_",'',$file_attachment);
                                        $count++;
                                        $viewMessage[0]['attachment_name']=$attachment_name;
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        $this->_redirect("/contrib/sentbox");
                        exit;
                    }                    
                }    
                 //classified tickets Count
                $class_tickets= $ticket->getClassifyTicket($contrib_identifier);
                if(is_array($class_tickets))
                $this->_view->class_ticket_count=count($class_tickets); 
                $this->_view->attachments=$attachment_name;
                $this->_view->viewMessage = $viewMessage;
              
                $this->_view->meta_title="Mailbox";
                $this->render("Contrib_view_mail");
             
        } 
    }
    //send reply mail
    public function sendReplyAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            if($this->_request-> isPost())
            {
              $ticket_params=$this->_request->getParams();
              $identifier=$this->contrib_identifier;
              $ticket_Identifier=$ticket_params['ticket_id'];
              $ticket=new Ep_Ticket_Ticket();
              if(($ticket_details=$ticket->getUserTypeTicket($ticket_Identifier,$identifier))!="NO")
              {
                  if($ticket_details[0]['usertype']=='recipient')
                      $update_ticket['status']='1';
                  else
                      $update_ticket['status']='0';
                  $update_ticket['updated_at']=date("Y-m-d H:i:s", time());
                  
                  $ticket->updateTicketStatus($ticket_Identifier,$update_ticket);
                  try
                  {
                      $message=new Ep_Ticket_Message();
                      $message->ticket_id=$ticket_Identifier;
                      $message->content=nl2br($this->utf8dec($ticket_params["mail_message"]));
                      if($ticket_details[0]['usertype']=='recipient')
                          $message->type='1' ;
                      else
                          $message->type='0' ;
                      $message->status='0';
                      $message->created_at=$ticket->created_at;
                      $userTypes=$ticket->getSenderRecipientType($ticket_Identifier);
                      if(($userTypes[0]['type']=='client' OR $userTypes[0]['type']=='contributor') AND
                          ($userTypes[1]['type']=='client' OR $userTypes[1]['type']=='contributor'))
                      {
                          $message->approved=NULL;
                      }
                      else
                      {
                          if($userTypes[0]['type']!='client' AND $userTypes[0]['type']!='contributor')
                              $message->bo_user_type=$userTypes[0]['type'];
                          else if($userTypes[1]['type']!='client' AND $userTypes[1]['type']!='contributor')
                              $message->bo_user_type=$userTypes[1]['type'];
                          else
                              $message->bo_user_type=NULL;
                          $message->approved='yes';
                          $auto_mail=new Ep_Ticket_AutoEmails();
                          $auto_mail->sendNotificationEmail();
                      }                      
                      if($_FILES['attachment']['name'][0]!=NULL)
                      {
                          $file_attachemnts='';
                          $cnt=1;
                          foreach($_FILES['attachment']['name'] as $file)
                          {
                              $file_attachemnt[$cnt-1]=$message->getIdentifier()."_".$cnt."_".$this->utf8dec($file);
                              $file_attachemnts.= $message->getIdentifier()."_".$cnt."_".$this->utf8dec($file)."|";
                              $cnt++;
                          }
                          $file_attachemnts=substr($file_attachemnts,0,-1);
                          $message->attachment=$file_attachemnts;
                      }
                     
                      if($message->insert())
                      {
                          $attachment=new Ep_Ticket_Attachment();
                          if($_FILES['attachment']['name'][0]!=NULL)
                          {
                              $fileCount=0;
                              foreach($_FILES['attachment']['tmp_name'] as $file)
                              {
                                  $attachFile['tmp_name']=$file;
                                  $attachment->uploadAttachment($this->attachment_path,$attachFile,$file_attachemnt[$fileCount]);
                                  $fileCount++;
                              }
                          }
                          //$attachment->uploadAttachment($this->attachment_path,$_FILES['attachment'],$message->attachment);
                          $this->_helper->FlashMessenger('Message sent.');
                          $this->_redirect("/contrib/sentbox");
                      }
                  }
                  catch(Exception $e)
                  {
                      echo $e->getMessage();exit;
                  }
              }
              else
                  $this->_redirect("/contrib/compose-mail");
            }
            else
                $this->_redirect("/contrib/compose-mail");
              
        }      
    }
    public function classifyAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $mail_params=$this->_request->getParams();
            $ticket_Identifier=$mail_params['ticket'];
            $identifier=$this->contrib_identifier;
            $ticket=new Ep_Ticket_Ticket();
            if(($ticket_details=$ticket->getUserTypeTicket($ticket_Identifier,$identifier))!="NO")
            {
                if($ticket_details[0]['usertype']=='recipient')
                    $update_ticket['status']='3';
                else
                    $update_ticket['status']='2';
                $update_ticket['classified_by']=$identifier;
                $update_ticket['updated_at']=date("Y-m-d H:i:s", time());
                $ticket->updateTicketStatus($ticket_Identifier,$update_ticket);
                $this->_helper->FlashMessenger('Message saved in folder.');
                $this->_redirect("/contrib/inbox");
            }
            else
                $this->_redirect("/contrib/inbox");
        }    
    }
    //get misson details(premium,liberte and devis premium)
    public function articleDetailsAction()
    {
        if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
            $artilce_obj=new Ep_Article_Article();
            $poll_obj=new Ep_Poll_Participation();
            $responsePollObj=new Ep_Poll_UserResponse();
            $cparticipation=new Ep_Participation_Participation();
            $comments_obj=new Ep_Comments_Adcomments();
            $contrib_identifier=$this->contrib_identifier;
            $article_params=$this->_request->getParams();
            $mission_identifier=$article_params['mission_identifier'];
            $mission_type=$article_params['misson_type'];            
            $req_from=$article_params['req_from'];

            //flag set to show upcoming ao popup    
            $upcoming=$article_params['upcoming'];

            //flag set to show finished ao popup    
            $finished=$article_params['finished'];   

            $this->_view->profile_pic=$this->getPicPath($contrib_identifier);

            //$req_from='dfs';
            
            
            if (strpos($_SERVER['HTTP_REFERER'],'aosearch') !== false && !$article_params['req_from'] && !$article_params['item']) 
            {               
                 if($upcoming)
                   $recentOffers=$_SESSION['upcoming_offers'];
                 else if($finished)
                  $recentOffers=$_SESSION['finished_offers'];                         
                 else
                  $recentOffers=$_SESSION['all_offers'];  
				//echo "<pre>";print_r($recentOffers);exit;

                $paginate=popup_paginate( $recentOffers,$mission_identifier,$upcoming,$finished,$mission_type);
            }
            else if(!$article_params['item'] && !$article_params['req_from'] )
            {
               /**Recent AO Offers**/
                $recentOffers=$this->recentAoOffers();
                $paginate=popup_paginate( $recentOffers,$mission_identifier,NULL,NULL,$mission_type);
            }  



            if($article_params['item']=='cart')
                $disabled='yes';
            if($mission_identifier!='' && $mission_type!='' )
            {
                if($mission_type=='premium')
                {
                    $searchParameters['articleId']=$mission_identifier;
                    $searchParameters['req_from']= $req_from;
                    $searchParameters['upcoming']= $upcoming;
                    $searchParameters['profile_type']=$this->profileType; 
                    $searchParameters['black_status']=$this->black_status;


                    $comment_type='article';
                    
                    //added if for finished articles
                    if($finished)
                    {            
                                           
                       $articleDetails=$cparticipation->finishedArticles($searchParameters);
                    }  
                    else
                    {
                      $articleDetails=$artilce_obj->getArticleDetails($searchParameters);
                      if(count($articleDetails)==0)
                      {
                        $articleDetails=$artilce_obj->getArticleSearchDetails($searchParameters);
                        // user does not have permission to access this Artilce
                        $this->_view->no_permission="yes";
                      }  
                    }

                    if(count($articleDetails)>0)
                        $articleDetails=$this->formatArticleDetails($articleDetails);

                    

                    
                    //Comment Details
                    $commentDetails=$comments_obj->getAdComments($mission_identifier,$comment_type);
                    if(count($commentDetails)>0)
                        $commentDetails=$this->formatCommentDetails($commentDetails);                 
                      
                  
                    $this->_view->mission_type='premium';
                    $this->_view->comment_type= $comment_type;
                    $this->_view->identifier=$mission_identifier;
                    $this->_view->articleDetails=$articleDetails;
                    $this->_view->commentDetails=$commentDetails;
                    $this->_view->commentCount=count($commentDetails);
                    $this->_view->request_from=$req_from;
                    $this->_view->disabled=$disabled;
                    $this->_view->profile_type=$this->profileType;

                    if($upcoming)
                      $this->_view->upcoming='yes';
                    else if($finished)
                      $this->_view->finished='yes';

                    $this->render("Contrib_misson_popup");
                    
                }    
                else if($mission_type=='nopremium')
                {
                    $searchParameters['articleId']=$mission_identifier;
                    $searchParameters['req_from']= $req_from;
                    $searchParameters['upcoming']= $upcoming;
                    $searchParameters['profile_type']=$this->profileType; 
                    $searchParameters['black_status']=$this->black_status;


                    $comment_type='article';

                     //added if for finished articles
                    if($finished)
                    {
                      $articleDetails=$cparticipation->finishedArticles($searchParameters);
                    }  
                    else
                    {
                      $articleDetails=$artilce_obj->getArticleDetails($searchParameters);
                      if(count($articleDetails)==0)
                      {
                        $articleDetails=$artilce_obj->getArticleSearchDetails($searchParameters);
                        //if user does not have permission to access this Artilce
                        $this->_view->no_permission="yes";
                      }  
                    }                  

                    if(count($articleDetails)>0)
                        $articleDetails=$this->formatArticleDetails($articleDetails);
                     //Comment Details
                    $commentDetails=$comments_obj->getAdComments($mission_identifier,$comment_type);
                    if(count($commentDetails)>0)
                      $commentDetails=$this->formatCommentDetails($commentDetails);    


                    $this->_view->mission_type='nopremium';
                    $this->_view->comment_type= $comment_type;
                    $this->_view->identifier=$mission_identifier;
                    $this->_view->articleDetails=$articleDetails;
                    $this->_view->commentDetails=$commentDetails;
                    $this->_view->commentCount=count($commentDetails);
                    $this->_view->request_from=$req_from;
                    $this->_view->disabled=$disabled;
                    $this->_view->profile_type=$this->profileType;

                    if($upcoming)
                      $this->_view->upcoming='yes';
                    else if($finished)
                      $this->_view->finished='yes';

                    $this->render("Contrib_misson_popup");
                }   
                else if($mission_type=='poll_premium')
                {
                    
                    $searchParameters['profile_type']=$this->profileType;
                    $searchParameters['pollid']=$mission_identifier;
                    $searchParameters['req_from']= $req_from;
                    $searchParameters['upcoming']= $upcoming;
                    $searchParameters['black_status']=$this->black_status;

                    $comment_type='poll';
                    $pollDetails=$poll_obj->getPollDetails($searchParameters);                  
                    if(count($pollDetails)>0)
                        $pollDetails=$this->formatPollDetials($pollDetails);
                    //Comment Details
                    $commentDetails=$comments_obj->getAdComments($mission_identifier,$comment_type);
                    if(count($commentDetails)>0)
                      $commentDetails=$this->formatCommentDetails($commentDetails);     


                    //  Check already given response for this poll or not
                    $checkResponseExist=$responsePollObj->checkPollResponse($contrib_identifier,$mission_identifier);
                    if($checkResponseExist!="NO")
                    {
                        $this->_view->ResponseExist='yes';
                        foreach($checkResponseExist as $response)
                        {
                          if (strpos($response['response'], '|') !== false) 
                          {
                            $poll_response[$response['question_id']]=explode("|",$response['response']);
                          } 
                          else
                            $poll_response[$response['question_id']]=$response['response'];
                        }
                        
                     }   
                     else if(is_array($this->EP_Contrib_Cart->poll_response[$mission_identifier]))
                     {
                        $this->_view->ResponseExist='yes';
                        foreach($this->EP_Contrib_Cart->poll_response[$mission_identifier] as $key=> $response)
                        {
                          if (strpos($response['response'], '|') !== false) 
                          {
                            $poll_response[$response['question_id']]=explode("|",$response['response']);
                          } 
                          else
                            $poll_response[$response['question_id']]=$response['response'];
                        }
                     }
                    

                    //Devis new functionalities (getting questions linked to poll)

                    $poll_questions=$poll_obj->getPollQuestions($mission_identifier);
                    if($poll_questions)
                    {
                        $i=0;
                        foreach($poll_questions as $question)
                        {
                            if($question['type']=='radio')
                            {
                                $options=explode("|",$question['option']);
                                $j=1;
                                foreach($options as $option)
                                {
                                  $radio_options['option'.$j]=$option;
                                  $j++;
                                }
                                $poll_questions[$i]['option']=$radio_options;                                
                            }
                            if($question['type']=='checkbox')
                            {
                                $options=explode("|",$question['option']);
                                $j=1;
                                foreach($options as $option)
                                {
                                  $check_options['option'.$j]=$option;
                                  $j++;
                                }
                                $poll_questions[$i]['option']=$check_options;
                            }

                            //assigning answer if already given reply to this poll question
                            if(is_array($poll_response))
                            {
                              
                                if(array_key_exists($question['id'], $poll_response))
                                {
                                    if($question['type']=='checkbox') //if it is checkbox change the answer in to array if not array
                                    {
                                        if(is_array($poll_response[$question['id']]))  
                                            $poll_questions[$i]['answer']=$poll_response[$question['id']];
                                        else  
                                            $poll_questions[$i]['answer']=array($poll_response[$question['id']]);
                                    }
                                    else
                                        $poll_questions[$i]['answer']=$poll_response[$question['id']];
                                    
                                }   
                            }  
								$poll_questions[$i]['currency']=$pollDetails[0]['currency'];


                            $i++;

                        }

                        $this->_view->poll_questions=$poll_questions;

                     }   




                    $this->_view->mission_type='poll_premium';
                    $this->_view->comment_type= $comment_type;
                    $this->_view->identifier=$mission_identifier;
                    $this->_view->pollDetails=$pollDetails;
                    $this->_view->commentDetails=$commentDetails;
                    $this->_view->commentCount=count($commentDetails);
                    $this->_view->request_from=$req_from;
                    $this->_view->disabled=$disabled;
                    $this->_view->profile_type=$this->profileType;

                    if($upcoming)
                      $this->_view->upcoming='yes';

                    $this->render("Contrib_devis_popup");  
                }   
                else if($mission_type=='poll_nopremium')
                {
                    
                    $searchParameters['profile_type']=$this->profileType;
                    $searchParameters['pollid']=$mission_identifier;
                    $searchParameters['req_from']= $req_from;
                    $searchParameters['upcoming']= $upcoming;
                    $searchParameters['black_status']=$this->black_status;

                    $comment_type='poll';
                    $pollDetails=$poll_obj->getPollDetails($searchParameters);                  
                    if(count($pollDetails)>0)
                        $pollDetails=$this->formatPollDetials($pollDetails);
                    //Comment Details
                    $commentDetails=$comments_obj->getAdComments($mission_identifier,$comment_type);
                    if(count($commentDetails)>0)
                      $commentDetails=$this->formatCommentDetails($commentDetails);   


                    //Devis new functionalities (getting questions linked to poll)
                    $poll_questions=$poll_obj->getPollQuestions($mission_identifier);
                    if($questions)
                        $this->_view->poll_questions=$poll_questions;

                    //  Check already given response for this poll or not
                    $checkResponseExist=$responsePollObj->checkPollResponse($contrib_identifier,$mission_identifier);
                    if($checkResponseExist!="NO")
                        $this->_view->ResponseExist='yes';  



                    $this->_view->mission_type='poll_nopremium';
                    $this->_view->comment_type= $comment_type;
                    $this->_view->identifier=$mission_identifier;
                    $this->_view->pollDetails=$pollDetails;
                    $this->_view->commentDetails=$commentDetails;
                    $this->_view->commentCount=count($commentDetails);
                    $this->_view->request_from=$req_from;
                    $this->_view->disabled=$disabled;
                    $this->_view->profile_type=$this->profileType;

                    if($upcoming)
                      $this->_view->upcoming='yes';

                    $this->render("Contrib_devis_popup");  
                } 
                else if($mission_type=='correction')
                {
                    
                    $searchParameters['profile_type']=$this->profileType;
                    $searchParameters['articleId']=$mission_identifier;
                    $searchParameters['corrector']=$this->corrector;
                    $searchParameters['corrector_type']=$this->corrector_type;
                    $searchParameters['req_from']= $req_from;
                    $searchParameters['upcoming']= $upcoming;

                    $comment_type='correction';
                    $correctionDetails=$artilce_obj->getCorrectorAOs($searchParameters);                  
                    if(count($correctionDetails)>0)
                        $correctionDetails=$this->formatCorrectionDetials($correctionDetails);

                     
                    //Comment Details
                    $commentDetails=$comments_obj->getAdComments($mission_identifier,$comment_type);
                    if(count($commentDetails)>0)
                      $commentDetails=$this->formatCommentDetails($commentDetails);     
                    $this->_view->mission_type='correction';
                    $this->_view->comment_type= $comment_type;
                    $this->_view->identifier=$mission_identifier;
                    $this->_view->correctionDetails=$correctionDetails;
                    $this->_view->commentDetails=$commentDetails;
                    $this->_view->commentCount=count($commentDetails);
                    $this->_view->request_from=$req_from;
                    $this->_view->disabled=$disabled;
                    $this->_view->profile_type=$this->profileType;

                    if($upcoming)
                      $this->_view->upcoming='yes';

                    $this->render("Contrib_correction_popup");  
                } 
                
                  
            }    
             
        }  
        else
          $this->_redirect("/contrib/aosearch");  
    }
    //format article details
    public function formatArticleDetails($articleDetails)
    {
        $participants=new Ep_Participation_Participation();
        $cnt=0;

        //get Delivery alerts of user
          $alert_obj=new Ep_Article_DeliveryAlert();
          $allDeliveryAlerts=$alert_obj->getAllDeliveryAlerts($this->contrib_identifier,'article');
          $allAlerts=array();
          if($allDeliveryAlerts!="NO" && is_array($allDeliveryAlerts))
          {
             foreach($allDeliveryAlerts as  $alert) 
             {
                $allAlerts[]=$alert['delivery_id'];
             }
          }

        //
        
        foreach($articleDetails as $details)
        {
           $articleDetails[$cnt]['client_pic']= $this->getClientPicPath($details['identifier']);
           $articleDetails[$cnt]['category']= $this->getCategoryName($details['category']);
           $articleDetails[$cnt]['type']= $this->getArticleTypeName($details['type']);
            $articleDetails[$cnt]['product_type_name']= $this->producttype_array[$details['type']];
           $articleDetails[$cnt]['language_name']= $this->getLanguageName($details['language']);
           $articleDetails[$cnt]['delivery_date']= date('d/m/Y',strtotime($details['delivery_date']));
           $articleDetails[$cnt]['DaysDifference']= $this->getDaysDiff($details['delivery_date']);
           if($details['participation_expires'])
              $articleDetails[$cnt]['timestamp']= $details['participation_expires'];
           //else
           //   $articleDetails[$cnt]['timestamp']= strtotime(date('Y-m-d H:i:s',strtotime($details['delivery_date']." 23:59:59")));
            /**time stamp for ao end date**/
           // $articleDetails[$cnt]['timestamp_aoend']= strtotime(date('Y-m-d H:i:s',strtotime($details['delivery_date']." 23:59:59")));
             $articleDetails[$cnt]['timestamp_aoend']= $details['participation_expires'];
           /**submit and resubmit times**/
            if($this->profileType=='senior')
            {
                if($details['senior_time'])
                  $articleDetails[$cnt]['article_submit_time']=$details['senior_time'];
                else
                    $articleDetails[$cnt]['article_submit_time']=$this->config['sc_time'];
                if($details['sc_resubmission'])
                    $articleDetails[$cnt]['article_resubmit_time']=$details['sc_resubmission'];
                else
                    $articleDetails[$cnt]['article_resubmit_time']=$this->config['sc_resubmission'];
            }
           else if($this->profileType=='junior')
              {
                  if($details['junior_time'])
                      $articleDetails[$cnt]['article_submit_time']=$details['junior_time'];
                  else
                      $articleDetails[$cnt]['article_submit_time']=$this->config['jc_time'];
                  if($details['jc_resubmission'])
                      $articleDetails[$cnt]['article_resubmit_time']=$details['jc_resubmission'];
                  else
                      $articleDetails[$cnt]['article_resubmit_time']=$this->config['jc_resubmission'];
              }
              else if($this->profileType=='sub-junior')
              {
                  if($details['subjunior_time'])
                      $articleDetails[$cnt]['article_submit_time']=$details['subjunior_time'];
                  else
                      $articleDetails[$cnt]['article_submit_time']=$this->config['jco_time'];
                  if($details['jco_resubmission'])
                      $articleDetails[$cnt]['article_resubmit_time']=$details['jco_resubmission'];
                  else
                      $articleDetails[$cnt]['article_resubmit_time']=$this->config['jc0_resubmission'];
              }

              //Submit and Re-submit Time in Mins
              if($articleDetails[$cnt]['article_submit_time']>=60)
                 $articleDetails[$cnt]['article_submit_time_text']="<em>".($articleDetails[$cnt]['article_submit_time']/60)."</em> h";
              else
                 $articleDetails[$cnt]['article_submit_time_text']="<em>".$articleDetails[$cnt]['article_submit_time']."</em> mn";  

               if($articleDetails[$cnt]['article_resubmit_time']>=60)
                 $articleDetails[$cnt]['article_resubmit_time_text']="<em>".($articleDetails[$cnt]['article_resubmit_time']/60)."</em> h";
              else
                 $articleDetails[$cnt]['article_resubmit_time_text']="<em>".$articleDetails[$cnt]['article_resubmit_time']."</em> mn";  



            /**participation time**/
            if($details['participation_time'])
              $articleDetails[$cnt]['participation_time']=$details['participation_time'];
            else
              $articleDetails[$cnt]['participation_time']=$this->config['participation_time'];
            if($articleDetails[$cnt]['participation_time']>=60)
                 $articleDetails[$cnt]['participation_time_text']=($articleDetails[$cnt]['participation_time']/60)."heure(s)";
            else
                 $articleDetails[$cnt]['participation_time_text']=$articleDetails[$cnt]['participation_time']."mns";  
           $articleDetails[$cnt]['participants']= $participants->getParticipantCount($details['articleid']);
           if($articleDetails[$cnt]['participants'] >0)
           {
              if($articleDetails[$cnt]['currency']=='pound') $currency='&pound;';else $currency='&euro;';
              	$article_participant_info=$this->showgroupprofiles($details['articleid']);
              $cntp=0;
              foreach($article_participant_info as $a_participant)
              {
                $articleDetails[$cnt]['participants_pic'].="<img src='".$a_participant[0]['profile_picture']."'></img> ";
                $articleDetails[$cnt]['participants_price'].="<span class='badge'>".$a_participant[0]['price_user']." $currency</span> ";
                $cntp++;
                    if($details['pricedisplay']=='yes' && $cntp==2 )
                    {
                        break;
              }
                    else if($details['pricedisplay']!='yes' && $cntp==5 )
                    {
                        break;
           }   
                }
            }
            if(!$details['company_name'] && $details['deli_anonymous']=='0')
            {
                $articleDetails[$cnt]['company_name']='Anonyme';     
            }
            
           if(isset($this->EP_Contrib_Cart->cart[$articleDetails[$cnt]['articleid']]))
               $articleDetails[$cnt]['attended']='YES';
           else
               $articleDetails[$cnt]['attended']='NO';    

            //private icon
             if($articleDetails[$cnt]['AOtype']=='private')
             {
                $private_icon=$this->private_icon;
                $writers_count=count(explode(",",$articleDetails[$cnt]['contribs_list']));
                if($writers_count>1)
                  $toolTitle=$this->ptoolTitleMulti;
                else
                  $toolTitle=$this->ptoolTitle;

                  $toolTitle=str_replace('X',$writers_count,$toolTitle);
                  $private_icon=str_replace('$toolTitle',$toolTitle,$private_icon);
                              
                $articleDetails[$cnt]['picon']=$private_icon;
                
              }  
             else
              $articleDetails[$cnt]['picon']='';

            //quiz icon
            if($articleDetails[$cnt]['link_quiz']=='yes' && $articleDetails[$cnt]['quiz'] && !in_array($articleDetails[$cnt]['quiz'],$this->qualifiedQuiz))
             $articleDetails[$cnt]['qicon']=$this->quiz_icon;
            else
             $articleDetails[$cnt]['qicon']='';
            
           //latest price proposed.
           $articleDetails[$cnt]['latestPrice']=$participants->getLatestProposedPrice($details['articleid']);
           //specfile path
           $details['filepath']=explode("|",$details['filepath']);
           foreach($details['filepath'] as $spath)
           {
              $spec_path=SPEC_FILE_PATH.$spath; 
              
              if(file_exists($spec_path) && !is_dir($spec_path))
              {
                $articleDetails[$cnt]['spec_exists']='yes';
                break;
              }  
           } 
  
          $filter_category.=$details['filter_category'].",";
          $filter_language.=$details['filter_language'].",";   


          //publish time format
          if($details['publishtime'])
          {
            $articleDetails[$cnt]['publishtime_format']=converLocalTimeZone("<em>%d<\/\e\m> %B - <em>%H<\/\e\m> H <em>%M<\/\e\m> M",$details['publishtime']);
            //$articleDetails[$cnt]['publishtime_format']=strftime("<\e\m>%d<\/\e\m> %B - <\e\m>%H<\/\e\m> H <\e\m>%M<\/\e\m> M",$details['publishtime']);//date("d<\e\m>Y<\e\m>",$details['publishtime']);

            if(in_array($details['deliveryid'],$allAlerts))
                $articleDetails[$cnt]['alert_subscribe']='yes';
            else    
                $articleDetails[$cnt]['alert_subscribe']='no';

          }            
    
            
           $cnt++;
       }
        // echo "<pre>";print_r($articleDetails);
       return $articleDetails;
    }
     //format Poll details
    public function formatPollDetials($pollDetails)
    {
        $poll_obj=new Ep_Poll_Participation();
        $cnt=0;

        //get Poll alerts of user
          $alert_obj=new Ep_Article_DeliveryAlert();
          $allPollAlerts=$alert_obj->getAllDeliveryAlerts($this->contrib_identifier,'poll');
          $allAlerts=array();
          if($allPollAlerts!="NO" && is_array($allPollAlerts))
          {
             foreach($allPollAlerts as  $alert) 
             {
                $allAlerts[]=$alert['delivery_id'];
             }
          }

        //


        foreach($pollDetails as $details)
        {
           $pollDetails[$cnt]['client_pic']= $this->getClientPicPath($details['client']);
           $pollDetails[$cnt]['category']= $this->getCategoryName($details['category']);
           $pollDetails[$cnt]['category_key']=$details['category'];
           $pollDetails[$cnt]['type']= $this->getArticleTypeName($details['type']);
           $pollDetails[$cnt]['language_name']= $this->getLanguageName($details['language']);
           $pollDetails[$cnt]['delivery_date']= date('d/m/Y',strtotime($details['delivery_date']));
           $pollDetails[$cnt]['DaysDifference']= $this->getDaysDiff($details['delivery_date']);
           $pollDetails[$cnt]['participants']= $details['totalParticipation'];

           $pollDetails[$cnt]['num_min']= $details['min_sign'];
           $pollDetails[$cnt]['num_max']= $details['max_sign'];
		   $pollDetails[$cnt]['poll_max']= $details['poll_max'];
           
           
           if($details['participation_expires'])
              $pollDetails[$cnt]['timestamp']= $details['participation_expires'];
           else
              $pollDetails[$cnt]['timestamp']= strtotime($details['delivery_date']);
           //$pollDetails[$cnt]['participants']= $participants->getParticipantCount($details['articleid']);
           $checkParticipate=$poll_obj->checkPollParticipation($details['pollId'],$this->EP_Contrib_reg->clientidentifier,$this->profileType);
            if($checkParticipate=='NO')
                $pollDetails[$cnt]['action']='new';
            else
                $pollDetails[$cnt]['action']='update';
              if(isset($this->EP_Contrib_Cart->poll[$details['pollId']]))
               $pollDetails[$cnt]['attended']='YES';
           else
               $pollDetails[$cnt]['attended']='NO'; 
            $pollDetails[$cnt]['articleid']=$details['pollId'];
             //latest price proposed.
           $pollDetails[$cnt]['latestPrice']=$poll_obj->getLatestProposedPrice($details['pollId']);  
           
           if($pollDetails[$cnt]['totalParticipation'] > 0)
           {
              if($pollDetails[$cnt]['currency']=='pound') $currency='&pound;';else $currency='&euro;';
			   
			  $poll_participant_info=$this->showgroupprofiles($details['pollId'],'poll');
              $cntp=0;
              foreach($poll_participant_info as $a_participant)
              {
                $pollDetails[$cnt]['participants_pic'].="<img src='".$a_participant[0]['profile_picture']."'></img> ";
                $pollDetails[$cnt]['participants_price'].="<span class='badge'>".$a_participant[0]['price_user']." $currency</span> ";
                $cntp++;
              }
               
           }
           //specfile path
            $spec_path=POLL_SPEC_FILE_PATH."/".$details['file_name']; 
            
            if(file_exists($spec_path) && !is_dir($spec_path))
              $pollDetails[$cnt]['spec_exists']='yes';


            //publish time format
            if($details['publish_time'])
            {
               $pollDetails[$cnt]['publishtime_format']=converLocalTimeZone("<em>%d<\/\e\m> %B - <em>%H<\/\e\m> H <em>%M<\/\e\m> M",strtotime($details['publish_time']));
			   
              if(in_array($details['pollId'],$allAlerts))
                  $pollDetails[$cnt]['alert_subscribe']='yes';
              else    
                  $pollDetails[$cnt]['alert_subscribe']='no';

            }  

            
           $cnt++;
        }
        return $pollDetails;
    }
    //format Correction Ao Details
    public function formatCorrectionDetials($correctorDetails)
    {
        //get Delivery alerts of user
        $alert_obj=new Ep_Article_DeliveryAlert();
        $allDeliveryAlerts=$alert_obj->getAllDeliveryAlerts($this->contrib_identifier,'article-correction');
        $allAlerts=array();
        if($allDeliveryAlerts!="NO" && is_array($allDeliveryAlerts))
        {
            foreach($allDeliveryAlerts as  $alert)
            {
                $allAlerts[]=$alert['delivery_id'];
            }
        }
        $cnt=0;
         $participants_obj=new Ep_Participation_CorrectorParticipation();
          $ticket=new Ep_Ticket_Ticket();
        foreach($correctorDetails as $details)
        {
            if(!$details['articleid'])
                $details['articleid']=$details['article_id'];
            $correctorDetails[$cnt]['client_pic']= $this->getClientPicPath($details['clientId'],'home');
            $correctorDetails[$cnt]['writer_pic']= $this->getPicPath($details['writer'],'home');
            $correctorDetails[$cnt]['writer_pic_profile']= $this->getPicPath($details['writer'],'profile');
            $correctorDetails[$cnt]['writer_name']= $ticket->getUserName($details['writer']);  
            $correctorDetails[$cnt]['category']= $this->getCategoryName($details['category']);
            $correctorDetails[$cnt]['type']= $this->getArticleTypeName($details['type']);
            $correctorDetails[$cnt]['language_name']= $this->getLanguageName($details['language']);
            $correctorDetails[$cnt]['delivery_date']= date('d/m/Y',strtotime($details['delivery_date']));
            $correctorDetails[$cnt]['DaysDifference']= $this->getDaysDiff($details['delivery_date']);
            if($details['correction_participationexpires'])
                $correctorDetails[$cnt]['timestamp']= $details['correction_participationexpires'];
            //else
            //    $correctorDetails[$cnt]['timestamp']= strtotime(date('Y-m-d H:i:s',strtotime($details['delivery_date']." 23:59:59")));
            /**time stamp for ao end date**/
            $correctorDetails[$cnt]['timestamp_aoend']=$correctorDetails[$cnt]['timestamp'];// strtotime(date('Y-m-d H:i:s',strtotime($details['delivery_date']." 23:59:59")));
            /**submit and resubmit times dinchic**/
            if($this->corrector_type=='senior')
            {
                if($details['correction_sc_submission'])
                    $correctorDetails[$cnt]['correction_submission']=$details['correction_sc_submission'];
                else
                    $correctorDetails[$cnt]['correction_submission']=$this->config['correction_sc_submission'];
                if($details['correction_sc_resubmission'])
                    $correctorDetails[$cnt]['correction_resubmission']=$details['correction_sc_resubmission'];
                else
                    $correctorDetails[$cnt]['correction_resubmission']=$this->config['correction_sc_resubmission'];
            }
            else
            {
                if($details['correction_jc_submission'])
                    $correctorDetails[$cnt]['correction_submission']=$details['correction_jc_submission'];
                else
                    $correctorDetails[$cnt]['correction_submission']=$this->config['correction_jc_submission'];
                if($details['correction_jc_resubmission'])
                    $correctorDetails[$cnt]['correction_resubmission']=$details['correction_jc_resubmission'];
                else
                    $correctorDetails[$cnt]['correction_resubmission']=$this->config['correction_jc_resubmission'];
            }
            $correctorDetails[$cnt]['participants']= $participants_obj->getParticipantCount($details['articleid']);

            //Correction submission in Mins
            if($correctorDetails[$cnt]['correction_submission']>=60)
                $correctorDetails[$cnt]['correction_submission_text']="<em>".($correctorDetails[$cnt]['correction_submission']/60)."</em> h";
            else
                $correctorDetails[$cnt]['correction_submission_text']="<em>".$correctorDetails[$cnt]['correction_submission']."</em> mns";

            if($correctorDetails[$cnt]['correction_resubmission']>=60)
                $correctorDetails[$cnt]['correction_resubmission_text']="<em>".($correctorDetails[$cnt]['correction_resubmission']/60)."</em> h";
            else
                $correctorDetails[$cnt]['correction_resubmission_text']="<em>".$correctorDetails[$cnt]['correction_resubmission']."</em> mns";



            /**participation time**/
            if($details['correction_participation'])
                $correctorDetails[$cnt]['correction_participation']=$details['correction_participation'];
            else
                $correctorDetails[$cnt]['correction_participation']=$this->config['correction_participation'];
            if($correctorDetails[$cnt]['correction_participation']>=60)
                $correctorDetails[$cnt]['participation_time_text']=($correctorDetails[$cnt]['correction_participation']/60)."heure(s)";
            else
                $correctorDetails[$cnt]['participation_time_text']=$correctorDetails[$cnt]['correction_participation']."mns";
            if(!$details['company_name'] && $details['deli_anonymous']=='0')
            {
                $correctorDetails[$cnt]['company_name']='Anonyme';
            }
            
            if(isset($this->EP_Contrib_Cart->correction[$details['articleid']]))
               $correctorDetails[$cnt]['attended']='YES';
             else
               $correctorDetails[$cnt]['attended']='NO'; 

            //latest price proposed.
           $correctorDetails[$cnt]['latestPrice']=$participants_obj->getLatestProposedPrice($details['articleid']);  
           
           //All participants info
           if($correctorDetails[$cnt]['participants'] > 0)
           {
              if($correctorDetails[$cnt]['currency']=='pound') $currency='&pound;';else $currency='&euro;';
			  $correction_participant_info=$this->showgroupprofiles($details['articleid'],'correction');
              $cntp=0;
              foreach($correction_participant_info as $c_participant)
              {
                $correctorDetails[$cnt]['participants_pic'].="<img src='".$c_participant[0]['profile_picture']."'></img> ";
                $correctorDetails[$cnt]['participants_price'].="<span class='badge'>".$c_participant[0]['price_corrector']." $currency</span> ";
                $cntp++;
              }
              
           }             

            $correctorDetails[$cnt]['spec_exists']=$this->checkCorrectorBrief($details['articleid']);
            $correctorDetails[$cnt]['ao_type']='correction';


            //publish time format
            if($details['publishtime'])
            {
                //$articleDetails[$cnt]['publishtime_format']=converLocalTimeZone("<\e\m>%d<\/\e\m> %B - <\e\m>%H<\/\e\m> H <\e\m>%M<\/\e\m> M",$details['publishtime']);
                $correctorDetails[$cnt]['publishtime_format']=converLocalTimeZone("<em>%d<\/\e\m> %B - <em>%H<\/\e\m> H <em>%M<\/\e\m> M",$details['publishtime']);
                if(in_array($details['deliveryid'],$allAlerts))
                    $correctorDetails[$cnt]['alert_subscribe']='yes';
                else
                    $correctorDetails[$cnt]['alert_subscribe']='no';
            }
            $cnt++;
        }
        
        return $correctorDetails;
    }
    //Format Comment Details
    public function formatCommentDetails($commentDetails)
    {
        $ticket=new Ep_Ticket_Ticket();
        $contrib_identifier=$this->contrib_identifier;
        $cnt=0;
        foreach($commentDetails as $details)
        {
           if($details['user_type']=='contributor')
              $commentDetails[$cnt]['profile_pic']= $this->getPicPath($details['user_id']);
           else if($details['user_type']=='client')
              $commentDetails[$cnt]['profile_pic']= $this->getClientPicPath($details['user_id'],'home');
            else
              $commentDetails[$cnt]['profile_pic']= $this->getPicPath($details['user_id'],'bo_user');
           $commentDetails[$cnt]['profile_name']= $ticket->getUserName($details['user_id']);   
           $commentDetails[$cnt]['time']= time_ago($details['created_at']);

           if($contrib_identifier==$details['user_id'])
              $commentDetails[$cnt]['delete']='yes';

           $cnt++;
        }   
        return $commentDetails;
    }
    //save Comments Action
    public function saveCommentsAction()
    {
      
        if($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {       
            $comments_obj=new Ep_Comments_Adcomments();
            $contrib_identifier=$this->contrib_identifier;
            $comment_params=$this->_request->getParams();
            $type=$comment_params['comment_type'];
            $type_identifier=$comment_params['identifier'];
            $comments=$this->utf8dec($comment_params['article_comments']);

            //for deleting
            $action=$comment_params['comment_action'];
            $comment_identifier=$comment_params['comment_id'];
             
            if($action=='delete' && $comment_identifier!='' && $type && $type_identifier)
            {
                $is_comment_user=$comments_obj->checkCommentUser($comment_identifier,$contrib_identifier);
                
                if($is_comment_user=='YES')
                {
                  $comment_update['active']='no';
                  $comments_obj->updateCommentDetails($comment_update,$comment_identifier);
                }
            }

            else if($type && $type_identifier && $comments)
            {
                $comments_obj->user_id=$contrib_identifier;
                $comments_obj->type=$type;
                $comments_obj->type_identifier=$type_identifier;
                $comments_obj->comments=$comments;
                $comments_obj->created_at=date("Y-m-d H:i:s");
                $comments_obj->active='yes';
  
                try
                {
                   $comments_obj->insert();

                   //Inserting Recent Activities
                   $activity_obj=new Ep_User_RecentActivities();
                   $delivery_obj=new Ep_Article_Delivery();

                   if($type=='article')
                   {
                      $deliveryDetails=$delivery_obj->getDeliveryDetails($type_identifier);
                      $client_id=$deliveryDetails[0]['user_id'];


                      if($client_id)
                      {  
                        $activity_array['type']='comment';
                        $activity_array['created_at']=date("Y-m-d H:i:s");
                        $activity_array['user_id']=$client_id;
                        $activity_array['activity_by']=$contrib_identifier;
                        $activity_array['article_id']=$type_identifier;
                        $activity_array['commentid']= $comments_obj->getIdentifier();
                        $activity_obj->insertRecentActivities($activity_array);
                      }  
                       //send notification about comment to Client/EP
                       $autoEmail=new Ep_Ticket_AutoEmails();
                       $ticket_obj=new Ep_Ticket_Ticket();                       
                       $parameters['article_title']=$deliveryDetails[0]['articleName'];
                       
                        if($deliveryDetails[0]['premium_option']!='0' && $deliveryDetails[0]['premium_option']!='' )
                        {
                            
                            $parameters['contributor_name']=$ticket_obj->getUserName($contrib_identifier);
                            //$parameters['ongoinglink']="/client/quotes?id=".$type_identifier; 
                            //Get Client Username & password
                            $client_obj=new Ep_User_User();
                            $client_details=$client_obj->getClientdetails($client_id);
                            $email=$client_details[0]['email'];
                            $password=$client_details[0]['password'];

                            //$parameters['ongoinglink']="/user/email-login?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=client&comment=".$type_identifier;
                            $parameters['comment_bo_link']="/followup/delivery?client_id=".$deliveryDetails[0]['client']."&ao_id=".$deliveryDetails[0]['id']."&submenuId=ML3-SL10";                                  
                            $bo_created_user=$deliveryDetails[0]['created_user'];
                            $parameters['bo_user']=$ticket_obj->getUserName($bo_created_user,TRUE);
                            $parameters['comments']=$comments;
                            $mtype='premium';
                            $autoEmail->messageToEPMail($bo_created_user,77,$parameters,true);
                        }    
                        /*else  
                        {
                            //Notification Email to Client for Mission Liberte                          
                            $parameters['contributor_name']=$ticket_obj->getUserName($contrib_identifier);
                            $parameters['ongoinglink']="/client/quotes?id=".$type_identifier;   
                            $mtype='nopremium';                       
                            $autoEmail->messageToEPMail($client_id,76,$parameters);
                        } */   
                        //sending notification all other contributors who have participated or commented
                        /*$participation_obj=new Ep_Participation_Participation();
                        $ongogingParticipation=$participation_obj->checkOngoingParticipation($type_identifier);

                        if($ongogingParticipation=="NO")
                        {
                          $allParticipants=$participation_obj->getGroupParticipantsOfComments($type_identifier,$contrib_identifier);                      
                          if($allParticipants!="NO" && is_array($allParticipants))
                          {                          
                              foreach($allParticipants as $participant)
                              {
                                $parameters['contributor_name']=$ticket_obj->getUserName($contrib_identifier);
                                $parameters['ongoinglink']="/contrib/ongoing?mission_type=".$mtype."&mission_identifier=".$type_identifier;                          
                                $autoEmail->messageToEPMail($participant['user_id'],78,$parameters);
                              }
                          }
                        }*/  

                   }                   
                   
                                        
                }
                catch(Zend_Exception $e)
                {
                    echo $e->getMessage();exit;                    
                 }
            }
            $commentDetails=$comments_obj->getAdComments($type_identifier,$type);
             $commentsData='';
             $cmtCount=count($commentDetails);
            if($cmtCount>0)
            {
                
                $commentDetails=$this->formatCommentDetails($commentDetails);
                $commentsData='';
                $cnt=0;
                foreach($commentDetails as $comment)  
                {
                  $commentsData.=
                  '<li class="media" id="comment_'.$comment['identifier'].'">';

                    if($comment['user_id']==$contrib_identifier)
                      $commentsData.='<button  class="close" type="button" id="delete_comment_'.$comment['identifier'].'">&times;</button>';

                  $commentsData.='<a class="pull-left imgframe" href="#" role="button" data-toggle="modal" data-target="#viewProfile-ajax">
                        <img alt="Topito" class="media-object" width="60px" src="'.$comment['profile_pic'].'">
                      </a>
                      <div class="media-body">
                        <h4 class="media-heading">
                          <a href="#" role="button" data-toggle="modal" data-target="#viewProfile-ajax">'.utf8_encode($comment['profile_name']).'</a></h4>
                          '.utf8_encode(stripslashes($comment['comments'])).'
                        <p class="muted">'.$comment['time'].'</p>
                      </div>
                    </li>';
                }
            }
            echo  json_encode(array('comments'=>$commentsData,'count'=>$cmtCount));
        }
        else
          $this->_redirect("/contrib/home");
           
    }
     /**To get Article Price Ranges**/
    public function getarticlepricerangeAction()
    {
        $articleParams=$this->_request->getParams();
        $articleId= $articleParams['articleid'];
        $article=new Ep_Article_Article();
        $searchParameters['articleId']=$articleId;
        $articleDetails=$article->getArticleDetails($searchParameters);
			//print_r($articleDetails);exit;
			if($articleDetails)
			{
				$price['price_min']=number_format($articleDetails[0]['price_min'], 2, '.', '');
				$price['price_max']=number_format($articleDetails[0]['price_max'], 2, '.', '');
				$price['pricedisplay']=$articleDetails[0]['pricedisplay'];
				//header("Content-type: text/plain");
				echo json_encode($price);
			}
			else
				echo json_encode(array("error"=>"time_out"));
    }
    /**To get Poll Price Ranges**/
    public function getpollpricerangeAction()
    {
        $pollParams=$this->_request->getParams();
        $pollid= $pollParams['pollid'];
        $poll_obj=new Ep_Poll_Participation();
        $searchParameters['pollid']=$pollid;
        $pollDetails=$poll_obj->getPollDetails($searchParameters);
        $price['poll_price_min']=number_format($pollDetails[0]['price_min'], 2, '.', '');
        $price['poll_price_max']=number_format($pollDetails[0]['price_max'], 2, '.', '');
        //header("Content-type: text/plain");
        echo json_encode($price);
    }
    /**To get Article Price Ranges of a corrector article**/
    public function getcorrectorarticlepricerangeAction()
    {
        $articleParams=$this->_request->getParams();
        $articleId= $articleParams['articleid'];
        $article=new Ep_Article_Article();
        $searchParameters['articleId']=$articleId;
        $searchParameters['profile_type']=$this->profileType;
        $searchParameters['corrector']=$this->corrector;
        $searchParameters['corrector_type']=$this->corrector_type;
        $articleDetails=$article->getCorrectorAOs($searchParameters);
		if($articleDetails)
		{	
			$price['price_min']=number_format($articleDetails[0]['correction_pricemin'], 2, '.', '');
			$price['price_max']=number_format($articleDetails[0]['correction_pricemax'], 2, '.', '');
			//header("Content-type: text/plain");
			echo json_encode($price);
		}
		else
			echo json_encode(array("error"=>"time_out"));
	}

    
    /**participated users for a article**/
    public function showgroupprofiles($mission_id,$type='article')
    {
        $contrib_obj=new EP_Contrib_ProfileContributor();
        if($type=='article')
          $participate_obj=new Ep_Participation_Participation();
        else if($type=='poll')
          $participate_obj=new Ep_Poll_Participation();
        else if($type=='correction')
          $participate_obj=new Ep_Participation_CorrectorParticipation();
        
       
        if($mission_id!=NULL)
        {
            
             $participants=$participate_obj->getGroupParticipants($mission_id);
             for($i=0; $i<count($participants);$i++)
             {
                if($type=='article')
                    $contribDetails[$i]=$contrib_obj->getGroupProfilesInfo($participants[$i]['user_id'], $participants[$i]['id']);
                else if($type=='poll')
                    $contribDetails[$i]=$contrib_obj->getGroupProfilesPollInfo($participants[$i]['user_id'], $participants[$i]['id']);  
                else if($type=='correction')
                    $contribDetails[$i]=$contrib_obj->getCorrectorGroupProfilesInfo($participants[$i]['corrector_id'], $participants[$i]['id']);    
                 
                 $cnt=0;
                 foreach($contribDetails[$i] as $details)
                 {
                     /*Assign profile picture**/
                     $contribDetails[$i][$cnt]['profile_picture']=$this->getPicPath($details['identifier']); 
                     $cnt++;
                 }
             }
            
             return $contribDetails;
          }
    }

    //Mission premium/No premium articles deliver
    public function missionDeliverAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
           $missionParams=$this->_request->getParams();
           $article_id=$missionParams['article_id'];
           
           if($article_id)
           {
              $contrib_identifier=$this->contrib_identifier;
              $participation=new Ep_Participation_Participation();
              $article_obj=new Ep_Article_Article();
              $ticket_obj= new Ep_Ticket_Ticket();
              $ongoingArticles=$participation->ongoingArticles($contrib_identifier);

              if(count($ongoingArticles)>0)
              {  
                $cnt=0;
                foreach($ongoingArticles as $article)
                {
                    if($article['article_id']== $article_id)
                    {
                      $participationDetails[$cnt]=$article;
                      $participationDetails[$cnt]['client_pic']=$this->getClientPicPath($article['clientId'],'home');

                      $participationDetails[$cnt]['bo_user_pic']= $this->getPicPath($article['created_user'],'bo_user');
                      $participationDetails[$cnt]['bo_user']=$ticket_obj->getUserName($article['created_user'],TRUE);

                      $participationDetails[$cnt]['category']= $this->getCategoryName($article['category']);
                      $participationDetails[$cnt]['language_name']= $this->getLanguageName($article['language']);
                       if($article['status']=='on_hold' || $article['status']=='disapproved_temp' || $article['status']=='closed_temp' || $article['status']=='closed_client_temp' || $article['status']=='plag_exec')
                         $article['status']='under_study';
                        if($article['status']=='disapprove_client')
                            $article['status']='disapproved';
                       $current_time=time(); 
                       $time_expires=$article['article_submit_expires'];
                       $timestap_diff= $current_time - $time_expires;
                  
                      
                            if(($article['status']=='bid' || $article['status']=='disapproved' ) && $timestap_diff > 0 &&  $article['article_submit_expires'] >0 )
                      {
                        $participationDetails[$cnt]['status']='time_out';
                        $article['status']='time_out';
                      }  
                      if($this->profileType=='senior')
                      {
                          if($article['senior_time'])
                            $participationDetails[$cnt]['article_submit_time']=$article['senior_time'];
                          else
                              $participationDetails[$cnt]['article_submit_time']=$this->config['sc_time'];
                          if($article['sc_resubmission'])
                              $participationDetails[$cnt]['article_resubmit_time']=$article['sc_resubmission'];
                          else
                              $participationDetails[$cnt]['article_resubmit_time']=$this->config['sc_resubmission'];
                      }
                     else if($this->profileType=='junior')
                        {
                            if($article['junior_time'])
                                $participationDetails[$cnt]['article_submit_time']=$article['junior_time'];
                            else
                                $participationDetails[$cnt]['article_submit_time']=$this->config['jc_time'];
                            if($article['jc_resubmission'])
                                $participationDetails[$cnt]['article_resubmit_time']=$article['jc_resubmission'];
                            else
                                $participationDetails[$cnt]['article_resubmit_time']=$this->config['jc_resubmission'];
                        }
                        else if($this->profileType=='sub-junior')
                        {
                            if($article['subjunior_time'])
                                $participationDetails[$cnt]['article_submit_time']=$article['subjunior_time'];
                            else
                                $participationDetails[$cnt]['article_submit_time']=$this->config['jco_time'];
                            if($article['jco_resubmission'])
                                $participationDetails[$cnt]['article_resubmit_time']=$article['jco_resubmission'];
                            else
                                $participationDetails[$cnt]['article_resubmit_time']=$this->config['jc0_resubmission'];
                        }
                        //Submit and Re-submit Time in Mins
                      if($participationDetails[$cnt]['article_submit_time']>=60)
                         $participationDetails[$cnt]['article_submit_time_text']=($participationDetails[$cnt]['article_submit_time']/60)." h";
                      else
                         $participationDetails[$cnt]['article_submit_time_text']=$participationDetails[$cnt]['article_submit_time']." mn";  
                       if($participationDetails[$cnt]['article_resubmit_time']>=60)
                         $participationDetails[$cnt]['article_resubmit_time_text']=($participationDetails[$cnt]['article_resubmit_time']/60)." h";
                      else
                         $participationDetails[$cnt]['article_resubmit_time_text']=$participationDetails[$cnt]['article_resubmit_time']." mn";  
                      $participationDetails[$cnt]['status_trans']=$this->getAOStatus($article['status']);
                      //specfile path
                      $spec_path=SPEC_FILE_PATH.$article['filepath']; 
                      if(file_exists($spec_path) && !is_dir($spec_path))
                          $participationDetails[$cnt]['spec_exists']='yes';
                        if($article['premium_option']!='0' && $article['premium_option']!='' )
                           $participationDetails[$cnt]['ao_type']='premium';
                        else
                          $participationDetails[$cnt]['ao_type']='nopremium';   
                        $cnt++;
                        
                        $participation_id=$article['participationId'];
                        $this->_view->participation_id=$participation_id;
                        $deliveryName=$article['deliveryName'];
							/*ebookers stencils*/
							$article_status=$article['status'];
							$this->_view->article_status=$article_status;
							$files_pack=$article['files_pack'];
							$stencils_ebooker=$article['stencils_ebooker'];
							$sample_text_id=$article['ebooker_sampletxt_id'];
							$token_ids=$article['ebooker_tokenids'];
							$product_type=$article['product'];
                        /* *** added on 25.02.216 *** */
                        // bnp Related fields for later use in function//

                        $bnp_mission=$article['bnp_mission'];
                        $bnp_sample_text_id=$article['bnp_sampletext_id'];
                        $bnp_city_id=$article['bnp_city_id'];
                        $bnp_no_of_articles=$article['bnp_no_of_articles'];
                    }
                }
                //ebooker update
				if($participation_id && $files_pack && $stencils_ebooker=='yes')
				{
					$articleProcessObj=new EP_Article_ArticleProcess();
					$versionArticleDetails= $participation->getParticipationDetails($participation_id);  ///getting the paricipate id from participate atable/
					$participationId = $versionArticleDetails[0]['participate_id'];
					$user_versions=$contrib_identifier;
					$stencilsVersions=$articleProcessObj->getLatestEbookerVersionDetails($participation_id,$user_versions);
					//echo "<pre>";print_r($stencilsVersions);exit;
					$stencilsDetails=array();
					if($stencilsVersions)
					{
						$article_doc_content=$stencilsVersions[0]['article_doc_content'];
						$stencils_text=explode("###$$$###",$article_doc_content);
						for ($s = 0; $s < $files_pack; $s++) {
							$stencilsDetails[$s]=$stencils_text[$s];
						}	
					}
					else{
						for ($s = 0; $s < $files_pack; $s++) {
							$stencilsDetails[$s]='';
						}	
					}						
					$this->_view->stencilsDetails=$stencilsDetails;
					
					//get sample texts and tokens
					$ebooker_obj=new Ep_Article_Ebooker();
					
					//getting translation stencils to display
					if($product_type=='translation')
					{
						$translateStencils=$ebooker_obj->getStencilstoTranslate($article_id);
						if($translateStencils)
							$this->_view->translateStencils=$translateStencils;
							
						//echo "<pre>";print_r($translateStencils);exit;
					}				
					if($sample_text_id)
						$this->_view->sample_text=$sample_text=$ebooker_obj->getSampleText($sample_text_id);
					if($token_ids)
						$tokens=$ebooker_obj->getTokens($token_ids);
					//echo "<pre>";print_r($tokens);
					$mandatory_tokens=array();
					$optional_tokens=array();
					if($tokens)
					{	
						$mandatory_tokens=$tokens[0];
						$optional_tokens=$tokens[1];
						$js_tokens_array = json_encode($mandatory_tokens);
					}
					else{
						$js_tokens_array = json_encode(array());
						$token_array=array();
					}			
					
					//whitelist keywords$ebookerObj=new Ep_Article_Ebooker();
					$whiteListKeywords=$ebooker_obj->getWhiteListKeywords();
					if($whiteListKeywords)
					{
						$js_wl_keywords = json_encode($whiteListKeywords);
					}else{
						$js_wl_keywords =  json_encode(array());
					}
						
					$this->_view->js_tokens_array=$js_tokens_array;
					$this->_view->js_wl_keywords=$js_wl_keywords;
					$this->_view->mandatory_tokens=$mandatory_tokens;
					$this->_view->optional_tokens=$optional_tokens;
					
										
					//echo "<pre>";print_r($js_tokens_array);exit;
				}
                elseif($participation_id && $bnp_mission=='yes'){
                    $bnp_obj = new Ep_Bnp_Bnp();
                    $articleProcessObj=new EP_Article_ArticleProcess();
                    $versionArticleDetails= $participation->getParticipationDetails($participation_id);  ///getting the paricipate id from participate atable/
                    $participationId = $versionArticleDetails[0]['participate_id'];
                    $user_versions=$contrib_identifier;
                    $Versions=$articleProcessObj->getLatestEbookerVersionDetails($participation_id,$user_versions);
                    //
                    $bnp_city_id = explode('|',$bnp_city_id);
                    $bnp_no_of_articles= explode('|',$bnp_no_of_articles);
                    $bnp_sample_text_id= explode('|',$bnp_sample_text_id);
                    $this->_view->bnp_sample_text_id=$bnp_sample_text_id;
                    $this->_view->bnp_city_id=$bnp_city_id;
                    $this->_view->bnp_no_of_articles=$bnp_no_of_articles;
                    $bnpDetails=array();
                    $bnp_text=array();
                    if($Versions)
                    {
                        $article_doc_content=$Versions[0]['article_doc_content'];
                        $bnp_text=explode("###$$$###",$article_doc_content);
                        $count_no_of_art = count($bnp_no_of_articles);
                        $s=0;
                        $int=0;
                        $end=0;
                        for($a=0;$a<$count_no_of_art;$a++){
                            $end+=$bnp_no_of_articles[$a];
                            for ($s=$int; $s < $end; $s++) {
                                $bnpDetails[$a][$s]=$bnp_text[$s];
                            }
                            $int+=$bnp_no_of_articles[$a];
                        }
                    }
                    else{
                        $count_no_of_art = count($bnp_no_of_articles);
                        $int=0;
                        $end=0;
                        for($a=0;$a<$count_no_of_art;$a++){
                            $end+=$bnp_no_of_articles[$a];
                            for ($s=$int; $s < $end; $s++) {
                                $bnpDetails[$a][$s]='';
                            }
                            $int+=$bnp_no_of_articles[$a];
                        }
                    }
                    $this->_view->textcount=count($bnpDetails,1)-count($bnpDetails);
//                    echo "<pre>";print_r($bnpDetails);exit;
                    $this->_view->bnpDetails=$bnpDetails;
                    //get sample texts and tokens
                    $ebooker_obj=new Ep_Article_Ebooker();
                    $citys = (array) $bnp_obj->getCity();
                    $scitys = array();
                    foreach($citys as $city)
                    {
                        $scitys[$city['city_id']] = $city['city_name'];
                    }
                    $this->_view->scitys = $scitys;

                    $sample_texts = (array) $bnp_obj->getSampleTextIds();
                    $ssample_texts = array();
                    foreach($sample_texts as $sample_text)
                    {
                        $ssample_texts[$sample_text['sample_id']] =  "<h3>".$sample_text['title']."</h3><br/>".$sample_text['description'];
                    }
                    $this->_view->ssample_text=$ssample_texts;
//                    //getting translation stencils to display
//                    if($product_type=='translation')
//                    {
//                        $translateStencils=$ebooker_obj->getStencilstoTranslate($article_id);
//                        if($translateStencils)
//                            $this->_view->translateStencils=$translateStencils;
//
//                        //echo "<pre>";print_r($translateStencils);exit;
//                    }
//                    if($bnp_sample_text_id)
//                        $this->_view->sample_text=$sample_text=$bnp_obj->getSampleText($bnp_sample_text_id);
//                    if($token_ids)
//                        $tokens=$ebooker_obj->getTokens($token_ids);
//                    //echo "<pre>";print_r($tokens);
//                    $mandatory_tokens=array();
//                    $optional_tokens=array();
//                    if($tokens)
//                    {
//                        $mandatory_tokens=$tokens[0];
//                        $optional_tokens=$tokens[1];
//                        $js_tokens_array = json_encode($mandatory_tokens);
//                    }
//                    else{
//                        $js_tokens_array = json_encode(array());
//                        $token_array=array();
//                    }
//
//                    //whitelist keywords$ebookerObj=new Ep_Article_Ebooker();
//                    $whiteListKeywords=$ebooker_obj->getWhiteListKeywords();
//                    if($whiteListKeywords)
//                    {
//                        $js_wl_keywords = json_encode($whiteListKeywords);
//                    }else{
//                        $js_wl_keywords =  json_encode(array());
//                    }
                    $mandatory_tokens=array();
                    $optional_tokens=array();
                    $js_wl_keywords =  json_encode(array());
                    $js_tokens_array = json_encode(array());
                    $this->_view->js_tokens_array=$js_tokens_array;
                    $this->_view->js_wl_keywords=$js_wl_keywords;
                    $this->_view->mandatory_tokens=$mandatory_tokens;
                    $this->_view->optional_tokens=$optional_tokens;


                    //echo "<pre>";print_r($js_tokens_array);exit;
                }
                else if($participation_id) /**getting All versions of Articles w.r.t User**/
                {
					$articleProcessObj=new EP_Article_ArticleProcess();
					$versionArticleDetails= $participation->getParticipationDetails($participation_id);  ///getting the paricipate id from participate atable/
					$participationId = $versionArticleDetails[0]['participate_id'];
					$corrector_identifier=$versionArticleDetails[0]['corrector_id'];
					if($corrector_identifier)
						$user_versions=array($contrib_identifier,$corrector_identifier);
					else
						$user_versions=$contrib_identifier;
					$AllVersionArticles=$articleProcessObj->getAllVersionDetails($participation_id,$user_versions) ;
					if($AllVersionArticles!="NO" && is_array($AllVersionArticles))
					{
					  foreach($AllVersionArticles as $key=>$varticle)
					  {
						
						  $file_full_path=$this->articles_path.$varticle['article_path'];
						  
						  $AllVersionArticles[$key]['file_size']=formatSizeUnits($file_full_path);
						
					  }


					  $this->_view->AllVersionArticles=$AllVersionArticles;
					}
                }                



                if(count($participationDetails)==0 || !is_array($participationDetails))
                  $this->_redirect("/contrib/ongoing");                            
              }  
              else
                $this->_redirect("/contrib/ongoing");
              

              //Comment Details
              $comment_type='article';
              $comments_obj=new Ep_Comments_Adcomments();
              $commentDetails=$comments_obj->getAdComments($article_id,$comment_type);
              if(count($commentDetails)>0)
              {
                  $commentDetails=$this->formatCommentDetails($commentDetails);                  
              }    
              


              $this->_view->missionDetails=$participationDetails;
              $this->_view->comment_type= $comment_type;
              $this->_view->identifier=$article_id;
              $this->_view->commentDetails=$commentDetails;
              $this->_view->clientId=$participationDetails[0]['user_id'];
				if($deliveryName)
                   $this->_view->meta_title="Project Management [$deliveryName]";
              
                if($stencils_ebooker=='yes' && $product_type=='redaction')
					$this->render("Contrib_mission_deliver_ebooker");
				else if($stencils_ebooker=='yes' && $product_type=='translation')
					$this->render("Contrib_mission_deliver_ebooker_translation");
				else if($bnp_mission=='yes' && $product_type=='redaction')
					$this->render("Contrib_mission_deliver_bnp");
				else
                    $this->render("Contrib_mission_deliver");
            } 
            else
              $this->_redirect("/contrib/ongoing");           
        }  
    }
    
    ///the array of blacklist words to search in the document
    protected function textToArray($file_path=NULL,$file_content=NULL)
    {
        if($file_path)
            $filecontent = file_get_contents($file_path);
        else if($file_content)
            $filecontent=$file_content;
        if($filecontent)
        {
           $filecontent = utf8_decode(strtolower($filecontent));
            $words = explode('|',$filecontent);
            return $words;
        }
        else
            return array();
    }
    ///the array of blacklist to the words in popup
    protected function textToArraydisplay($file_path=NULL,$file_content=NULL)
    {
        if($file_path)
            $filecontent = file_get_contents($file_path);
        else if($file_content)
            $filecontent=$file_content;
        if($filecontent)
        {
            $filecontent=html_entity_decode(htmlentities($filecontent, ENT_COMPAT, 'UTF-8'), ENT_QUOTES , 'ISO-8859-1');
            $words = explode('|',$filecontent);
            return $words;
        }
        else
            return array();
    } 

    //fetching the whitelist keyword from the document through Geo id///
    protected function getWlKeywordWithGeoId($file_path=NULL, $client_id,$file_content=NULL)
    {
        if($file_content)
            $lines =$file_content;
        else if($file_path)
            $lines =file_get_contents($file_path);

        if($lines) {
            /// getting the geo id form the document///
            $pattern = "/^.*\bGeo ID\b.*$/m";
            $matches = array();
            preg_match($pattern, $lines, $matches);
            $in = $matches[0];
            preg_match_all('/\[([A-Za-z0-9 ]+?)\]/', $in, $out);
            $geoid = $out[1][0];
            /// getting the language id form the document///
            $langpattern = "/^.*\bLanguage Code\b.*$/m";
            $langmatches = array();
            preg_match($langpattern, $lines, $langmatches);
            $langin = $langmatches[0];
            preg_match_all('/\[([A-Za-z0-9 ]+?)\]/', $langin, $langout);
            $langid = strtolower($langout[1][0]);
            //echo $geoid; echo $langid; exit;
            //echo $out[1][0]; exit; //geo id in the documetn//
            $wlkeywords_obj = new Ep_Contrib_WhiteListKeywords();
            $wlworddetials = $wlkeywords_obj->getWlKeywords($geoid, $langid, $client_id);
            return $wlkeyword = $wlworddetials[0]['keyword'];
        }
        else
            return NULL;

    }
    ///unzipping the zip and rar files /////
    public function uncompression($type,$srcZipFile, $unzipdir)
    {
        if($type == 'rar' )
        {
            $rar_file = rar_open($srcZipFile);
            $list = rar_list($rar_file);
            foreach($list as $file) {
                preg_match('/RarEntry for file "(.*)"/', $file, $matches) ;
                if(strstr($file, 'RarEntry for file'))
                {
                    $entry = rar_entry_get($rar_file, $matches[1]) or die("Failed to find such entry") ;
                    $entry->extract($unzipdir) ;
                    $filepath = pathinfo($matches[1]) ;
                }
            }
            rar_close($rar_file);
        }else{
            $zip = new ZipArchive;
            $res = $zip->open(utf8_decode($srcZipFile));
            if ($res === TRUE) {
                $zip->extractTo($unzipdir);
                $zip->close();
            }
        }
    }
    ////cheching the whitelist keyword is missing or not//if misses returns true or false
    protected function checkBlackListKeyword($content)
    {
        ///checking for the white list keyword in each paragraphs
        $content = preg_replace('!\s+!', ' ', $content);
        $contentres = array();
        preg_match_all("/(PARAGRAPH.*?)\]/si", $content, $matches);
        $paraarray = $matches[0];
        foreach($paraarray as $k=>$v)
        {
            $contentres[] = $v;
        }
        preg_match_all("/(Title.*?)\]/si", $content, $tmatches);
        $titlearray = $tmatches[0];
        foreach($titlearray as $tk=>$tv)
        {
            $contentres[] = $tv;
        }
        $contentresult = implode("|", $contentres);
        // get the not covered white list words//
        return $contentresult; ///missed the keyword
    }
   // Venere client required the black list and white list check//
    protected function checkBlWlLists($articleDir,$articleName, $contribId, $userfilename, $participationId, $articleId, $client_id)
    {
        /* *sending mail to Contributor**/
        $autoEmails=new Ep_Ticket_AutoEmails();
        $wlkeywords_obj = new Ep_Contrib_WhiteListKeywords();
        $article_obj = new Ep_Article_Article();
        $artDetails=$article_obj->getArticleAOdetails($articleId);
        $artLang = $artDetails[0]['language'];
         $autoEmails->messageToEPMail($contribId,153,$parameters=NULL);
        $textfile = explode('.',$articleName);
        $filetypeerror = 'no';
        $docfileerror = 'no';
        if($textfile[1] == 'zip' || $textfile[1] == 'rar')
        {
            $srcZipFile =  $articleDir.$articleName;
            chmod($srcZipFile,0777) ;
            $articleid = explode("/",$articleDir);
            $zippath = $articleid[7]."/".$articleName;
            $unzipdir = $articleDir.$textfile[0].'/';
            if($textfile[1] == 'rar' )
                $this->uncompression('rar',$srcZipFile, $unzipdir);
            else
                $this->uncompression('zip',$srcZipFile, $unzipdir);
            chmod($unzipdir,0777) ;
            $zipping = new RecursiveDirectoryIterator($unzipdir);
            foreach (new RecursiveIteratorIterator($zipping) as $filename => $file) {
                $fileinfo = pathinfo($filename);
                chmod($filename,0777) ;
                if($fileinfo['extension'] == 'zip')
                    $this->uncompression('zip',$filename, $unzipdir);
                else
                    $this->uncompression('rar',$filename, $unzipdir);
            }
            $direader = new RecursiveDirectoryIterator($unzipdir);
            foreach (new RecursiveIteratorIterator($direader) as $filename => $file) {
                $fileinfo = pathinfo($filename);
                if($fileinfo['extension'] == 'doc' && !preg_match("/._/", $fileinfo['basename']))
                    rename($filename, $unzipdir.$fileinfo['basename']);
            }
            $dhr  = opendir($unzipdir);
            while (false !== ($filename = readdir($dhr))) {
                $fileinfo = pathinfo($filename);
                //echo "<pre>".$fileinfo['extension'];
                if($fileinfo['extension'] != '' && $fileinfo['extension'] != 'doc'){
                    $filetypeerror = 'yes';
                    break;
                }elseif($fileinfo['extension'] == 'doc') {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimetype = finfo_file($finfo, $unzipdir.$filename);
                    if($mimetype != 'application/msword'){
                        $docfileerror = 'yes';
                        $result[] = 'no';
                        break;
                    }
                }
            }
            if (is_dir($unzipdir) && $docfileerror != 'yes'){
                /*foreach (glob("*.docx") as $docfile) {
                    echo $docfile;
                    if(preg_match("/._/", $docfile))
                        unlink($unzipdir.$docfile);
                    else
                        $docfilearray[] = $docfile;
                }
                print_r($docfilearray); exit;*/
                ///black list block ///
                if ($dh = opendir($unzipdir) ){
                    $i=0;//echo $unzipdir."<br>"; exit;
                    $dir = new DirectoryIterator($unzipdir);
                    $blkeywordsall = array();
                    foreach ($dir as $fileinfo) {
                        if (!$fileinfo->isDot()) {
                            $entry = $fileinfo->getFilename();
                            $filedetails = pathinfo($entry);
                            $output[$i]['blkeyword'] = '';
                            $blintersected = array();
                            $blmacthed = array();
                            //echo "<br>".$filedetails['basename'];
                            if($filedetails['extension'] == 'doc')
                            {
                                //echo $unzipdir.$entry."<br>";
                                $antiword_obj = new Ep_Antiword_Antiword($unzipdir . $entry);
                                $article_doc_content = $antiword_obj->getContent();

                                $article_doc_content=html_entity_decode(htmlentities($article_doc_content, ENT_QUOTES, 'ISO-8859-1'), ENT_QUOTES , 'UTF-8');
                                $blcontent = $this->checkBlackListKeyword($article_doc_content);
                                ////get the lang code from doc ///
                                $langpattern = "/^.*\bLanguage Code\b.*$/m";
                                $langmatches = array();
                                preg_match($langpattern, $article_doc_content, $langmatches);
                                $langin = $langmatches[0];
                                preg_match_all('/\[([A-Za-z0-9 ]+?)\]/', $langin, $langout);
                                $langid = strtolower($langout[1][0]);

                                $blacklistpath = ROOT_PATH . "FO/articles/blacklist/".$client_id."/blacklist_".$langid.".txt";
                               // $blacklistpath = ROOT_PATH . "FO/articles/blacklist.txt";
                                $blarray = $this->textToArray($blacklistpath, NULL);
                                $blarray2 = $this->textToArraydisplay($blacklistpath, NULL); //print_r($blarray);
                                $output[$i]['filename'] = utf8_decode(basename($entry));
                                foreach ($blarray AS $key => $value) {
                                    if(preg_match("/".rawurlencode($value)."\b/i",rawurlencode(utf8_decode($blcontent)))) {
                                        $blmacthed[] = $key;//urldecode(str_replace("%26rsquo%3B","",urlencode($value)));
                                    }
                                }
                              //  echo "<pre>".$output[$i]['filename']."----".print_r($blmacthed);
                                if(!empty($blmacthed)){
                                    foreach ($blmacthed AS $key) {
                                        $blintersected[] = $blarray2[$key];
                                    }
                                }

                               // echo "<pre>".$output[$i]['filename']."======".print_r($blintersected[$i]);
                                foreach ($blintersected AS $key => $value) {
                                    $blintersected[$key] = str_replace('<','&lt;',$value);
                                }
                                foreach ($blintersected AS $key => $value) {
                                    $blintersected[$key] = str_replace('>','&gt;',$value);
                                }
                                if (empty($blintersected[$i])) {
                                    $blackstatus[] = 'blno';
                                    $result[] = 'yes';
                                    $output[$i]['blkeyword'] = '';
                                    //$blkeywordsall = array_merge($blkeywordsall, $blintersected);
                                } else {
                                    $blackstatus[] = 'blyes';
                                    $result[] = 'no';
                                    $this->_view->blblock = 'yes';
                                    //print_r($result); exit;
                                    $output[$i]['singlefile'] = "no";
                                    $output[$i]['articlepath'] = $zippath;
                                    $output[$i]['articleName'] = $userfilename;
                                    $output[$i]['participationId'] = $participationId;
                                    $output[$i]['wordcount'] = str_word_count($article_doc_content);
                                    $output[$i]['zipfilename'] = $userfilename;
                                    $output[$i]['blkeyword'] = implode(', ', $blintersected);
                                    $blkeywordsall = array_merge($blkeywordsall, $blintersected);
                                    $i++;
                                }
                                //unlink text file
                                $ext=pathinfo($output[$i]['filename']);
                                $txtfile=$unzipdir.$ext['filename'].'.txt';
                                unlink($txtfile);
                            }else{
                                $result[] = 'no';
                            }
                        }
                    }
                    ///white list block ///
                    if(!in_array('blyes', $blackstatus)) {
                        $this->_view->blblock = 'no';
                        $j=0;
                        foreach ($dir as $fileinfo) {
                            if (!$fileinfo->isDot()) {
                                $entry = $fileinfo->getFilename();
                                $filedetails = pathinfo($entry);
                                if($filedetails['extension'] == 'doc') {
                                    //echo $unzipdir.$entry."<br>";
                                    $antiword_obj = new Ep_Antiword_Antiword($unzipdir . $entry);
                                    $article_doc_content = $antiword_obj->getContent();
                                    /*if($filedetails['extension'] == 'docx')
                                        $article_doc_content = utf8_decode($article_doc_content);*/
                                    $wlkeyword = $this->getWlKeywordWithGeoId(NULL, $client_id, $article_doc_content); //first param is NULl if content is passing
                                    $textarray = $this->textToArray(NULL, $article_doc_content);
                                    $wlresult = $this->checkWhiteListKeywordMissed($unzipdir . $entry, $wlkeyword);
                                    if($wlresult == true) { //keyword is missing in anyone of the paragraphs in documents
                                        $result[] = 'no';
                                        $output[$j]['filename'] = utf8_decode(basename($entry));
                                        $output[$j]['wlkeyword'] = $wlkeyword;
                                        $output[$j]['singlefile'] = "no";
                                        $output[$j]['articlepath'] = $zippath;
                                        $output[$j]['articleName'] = $userfilename;
                                        $output[$j]['participationId'] = $participationId;
                                        $output[$j]['wordcount'] = str_word_count($article_doc_content);
                                        $output[$j]['zipfilename'] = utf8_decode($userfilename);
                                        $j++;
                                    }else{
                                        $result[] = 'yes';
                                    }
                                }
                                else{
                                    $result[] = 'no';
                                }
                            }
                        }
                    }
                    if($this->_view->blblock == 'yes')
                    {
                        $parameters['filename'] = utf8_decode($filename);
                        $parameters['blwords'] = implode(",",$blkeywordsall);
                        $autoEmails->messageToEPMail($contribId,156,$parameters);
                    }
                  // echo "<pre>";print_r($output); exit;
                 //  exit;
                }

            }
            $this->_view->participationId = $participationId;
            $this->_view->singlefile = "no";

            $this->_view->output = $output;
            closedir($unzipdir);//echo "<pre>";print_r($result); exit;
        }else
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($finfo, $articleDir.$articleName);
            $filedetails = pathinfo($articleName);
            $typearr = array('application/vnd.ms-office', 'application/msword');
            if(!in_array($mimetype, $typearr) || $filedetails['extension'] != 'doc'){
                $docfileerror = 'yes';
                $result[] = 'no';
            }
            else
            {
                $antiword_obj = new Ep_Antiword_Antiword($articleDir.$articleName);
                $content = $antiword_obj->getContent();
                //$content =  str_replace("’","'",$content);
                $content=html_entity_decode(htmlentities($content, ENT_QUOTES, 'ISO-8859-1'), ENT_QUOTES , 'UTF-8');
                $blcontent = $this->checkBlackListKeyword($content);

                $textfilepath = $articleDir . $textfile[0] . '.txt';
                ////get the lang code from doc ///
                $langpattern = "/^.*\bLanguage Code\b.*$/m";
                $langmatches = array();
                preg_match($langpattern, $content, $langmatches);
                $langin = $langmatches[0];
                preg_match_all('/\[([A-Za-z0-9 ]+?)\]/', $langin, $langout);
                $langid = strtolower($langout[1][0]);
                $blacklistpath = ROOT_PATH . "FO/articles/blacklist/".$client_id."/blacklist_".$langid.".txt"; 

                $blarray = $this->textToArray($blacklistpath, NULL);
                $blarray2 = $this->textToArraydisplay($blacklistpath, NULL); //print_r($blarray);
                $wlkeyword = $this->getWlKeywordWithGeoId(null, $client_id, $content); //getting the wl keyword id from user file
               // $textarray = $this->textToArray(NULL, $content);
               /// print_r($blarray); echo "<pre>".rawurlencode(utf8_decode($blcontent));
                $output['filename'] = utf8_decode($userfilename);
                foreach ($blarray AS $key => $value) {
                   // echo "<pre>".rawurlencode($value);
                   //if(strstr(utf8_encode($content), utf8_encode($blarray[$key]))){
                    if(preg_match_all("/%20".rawurlencode($value)."\b/i",rawurlencode(utf8_decode($blcontent)))) {
                        $blmacthed[] = $key;//urldecode(str_replace("%26rsquo%3B","",urlencode($value)));
                    }
                }

                foreach ($blmacthed AS $key) {
                    $blintersected[] = $blarray2[$key];
                }
                //print_r($blintersected); exit;
                $blintersected = array_unique($blintersected);
                foreach ($blintersected AS $key => $value) {
                    $blintersected[$key] = str_replace('<','&lt;',$value);
                }
                foreach ($blintersected AS $key => $value) {
                    $blintersected[$key] = str_replace('>','&gt;',$value);
                }
                $output['blkeyword'] = implode(', ', $blintersected); //exit;
                if (empty($blintersected)) {   //echo $articleDir.$articleName; echo $wlkeyword; exit;
                    $wlresult = $this->checkWhiteListKeywordMissed($articleDir . $articleName, $wlkeyword);
                    if ($wlresult == true) { //keyword is missing in anyone of the paragraphs in documents
                        $result[] = 'no';
                        $output['wlkeyword'] = $wlkeyword;
                        $this->_view->blblock = 'no';
                    } else {
                        $result[] = 'yes';
                    }
                } else {
                    $parameters['filename'] = utf8_decode($userfilename);
                    $parameters['blwords'] = $output['blkeyword'];
                  //  $autoEmails->messageToEPMail($contribId, 156, $parameters);
                    $result[] = 'no';
                    $this->_view->blblock = 'yes';
                }

                $this->_view->singlefile = "yes";
                $this->_view->blwloutput = $output;
                $this->_view->articlepath = $articleDir . $articleName;
                $this->_view->articleName = $articleName;
                $this->_view->participationId = $participationId;
            }
        }
       // print_r($result);  echo $filetypeerror; echo $docfileerror; exit;
        if(!in_array('no',$result)){
            return 'pass';
        }elseif($filetypeerror == 'yes'){
            return  'filetypeerror';
        }elseif($docfileerror == 'yes'){
            return  'docfileerror';
        }
        else{
            /* *sending mail to Contributor */
            //$autoEmails->messageToEPMail($contribId,154,$parameters=NULL);
            return  $this->_view->renderHtml("Contrib_wlblcheck");
        }
    }
    //FUNCTION :: read a docx file and return the string
    function readDocx($filePath) {
        // Create new ZIP archive
        $zip = new ZipArchive;
        $dataFile = 'word/document.xml';
        // Open received archive file
        if (true === $zip->open($filePath)) {
            // If done, search for the data file in the archive
            if (($index = $zip->locateName($dataFile)) !== false) {
                // If found, read it to the string
                $data = $zip->getFromIndex($index);
                // Close archive file
                $zip->close();
                // Load XML from a string
                // Skip errors and warnings
                $xml = DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                // Return data without XML formatting tags
                //echo $xml->saveXML();exit;
                $contents = explode('\n',strip_tags($xml->saveXML()));
                $text = '';
                foreach($contents as $i=>$content) {
                    $text .= $contents[$i];
                }
                return  $xml->saveXML();
            }
            $zip->close();
        }
        // In case of failure return empty string
        return "";
    }
    function process_xmlData($text){
        /*things need to change end*/
         // echo "<pre>".htmlentities($text);
        $text = str_replace("[","####",$text);
        $text = str_replace("]","#####",$text);
        $text =preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $text);
        $text = str_replace("</w:tr>","]",$text);
        $text = str_replace("<w:tr>","[",$text);
        $matches = array();
        //$pattern = '/\tblrowstart([^\]]*)\tblrowend/';
        //$pattern = '/\[(tblrowstart^\tblrowend]*)\]/';
        /*$pattern='/\[([^\]]*)\]/';
        preg_match_all($pattern, $text, $matches);
        echo "<pre>";print_r($matches);exit;*/
        $matches = explode("]",$text);
         //  echo "<pre>";print_r($matches);exit;
        $ind=1;
        $newArr=array();
        foreach($matches as $key => $value){
            //$value = str_replace("</w:tc>","]",$value);
           // $value = str_replace("<w:tc>","[",$value);
            //echo $text2;
           // $pattern='/\[([^\]]*)\]/';
            //$htmldata.="<tr id='row_".$key."'>";
           // preg_match_all($pattern, $value, $matches2);
            $arrData='';
            //$htmldata.="<td >".$ind."</td>";
            //foreach($matches2[1] as $k=>$v){
                $arrData=trim(strip_tags($value))." ";
                //$htmldata.="<td >".trim(strip_tags($v))."</td>";
            //}
            $arrData = str_replace("####","[",$arrData);
            $arrData = str_replace("#####","]",$arrData);
            $newArr[$ind]=$arrData;
            //$htmldata.="</tr>";
            //	echo "<pre>";print_r($matches2[0]);
            $ind++;
        }
        //$htmldata.="</table>";
        //echo "<pre>"; print_r($matches);
        //$htmldata = str_replace("####","[",$htmldata);
        //$htmldata = str_replace("#####","]",$htmldata);
        return $newArr;
    }
    //////Hetel.com dev ////
    protected function checkHotelDevLists($articleDir,$articleName, $contribId, $userfilename, $participationId, $articleId, $client_id, $crtpartcipate_id)
    {
        /* *sending mail to Contributor**/
        //ini_set('default_charset', 'UTF-8');
       // mb_internal_encoding('UTF-8');
        $autoEmails=new Ep_Ticket_AutoEmails();
        $hotelkeywords_obj = new Ep_Contrib_HotelsKeywords();
        $article_obj = new Ep_Article_Article();
        $artDetails=$article_obj->getArticleAOdetails($articleId);
        $artLang = $artDetails[0]['language'];
        $textfile = explode('.',$articleName);
        $filetypeerror = 'no';
        $docfileerror = 'no';
        $typeext = array('docx', 'DOCX');
        $spacekeywordarray = array('mn','min','km','eur','$','°C','h','%',':',';','!','?','"','"', 'january', 'february',
            'march','april','may','june','july','august','september','october','november','december');
        $fileroot = ROOT_PATH . "FO/articles/";
        $realfilepath = $articleId."/hotelsblwl_".$participationId."_".time().".html";
        $htmlfilepath = $fileroot.$realfilepath;
        if($textfile[1] == 'zip' || $textfile[1] == 'rar')
        {
            $srcZipFile =  $articleDir.$articleName;
            chmod($srcZipFile,0777) ;
            $articleid = explode("/",$articleDir);
            $zippath = $articleid[7]."/".$articleName;
            $unzipdir = $articleDir.$textfile[0].'/';
            if($textfile[1] == 'rar' ){
                $this->uncompression('rar',$srcZipFile, $unzipdir);
            }
            else
                $this->uncompression('zip',$srcZipFile, $unzipdir);
            chmod($unzipdir,0777) ;
            $zipping = new RecursiveDirectoryIterator($unzipdir);
            foreach (new RecursiveIteratorIterator($zipping) as $filename => $file) {
                $fileinfo = pathinfo($filename);
                chmod($filename,0777) ;
                if($fileinfo['extension'] == 'zip')
                    $this->uncompression('zip',$filename, $unzipdir);
                else
                    $this->uncompression('rar',$filename, $unzipdir);
            }
            $direader = new RecursiveDirectoryIterator($unzipdir);
            foreach (new RecursiveIteratorIterator($direader) as $filename => $file) {
                 $fileinfo = pathinfo($filename);
                if(in_array($fileinfo['extension'],$typeext) )
                {
                    if(!preg_match("/._/", $fileinfo['basename']))
					{
						rename($filename, $unzipdir.$fileinfo['basename']);
					}
                }
            }
            $dhr  = opendir($unzipdir);
            while (false !== ($filename = readdir($dhr))) {
                $fileinfo = pathinfo($filename);
               // echo "<pre>".$fileinfo['extension'];
                if($fileinfo['extension'] != '' && (!in_array($fileinfo['extension'],$typeext))){
                   // $filetypeerror = 'yes';
                    $docfileerror = 'yes';
                    $result[] = 'no';
                    break;
                }elseif(in_array($fileinfo['extension'],$typeext)) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $typearr = array('application/vnd.ms-office', 'application/zip', 'application/octet-stream');
                    $mimetype = finfo_file($finfo, $unzipdir.$filename);
                    if(!in_array($mimetype, $typearr)){
                        $docfileerror = 'yes';
                        $result[] = 'no';
                        break;
                    }
                }
            } //echo "----hello---";
            if (is_dir($unzipdir) && $docfileerror != 'yes'){
                if ($dh = opendir($unzipdir) ){
                    $i=0;//echo $unzipdir."<br>"; exit;
                    $dir = new DirectoryIterator($unzipdir);
                    $blkeywordsall = array();
                    $html_start="<!DOCTYPE html><html lang=\"en\"><head><meta charset='UTF-8'></head><body>";
                    $outputcontent = "<table class='table table-bordered'><tr><th>Nom du fichier</th><th>Mots-cl&eacute;s interdits</th><th>Mots-cl&eacute;s autoris&eacute;s</th><th>Probl&egrave;mes d'espace</th></tr>";
                    foreach ($dir as $fileinfo) {
                        if (!$fileinfo->isDot()) {
                            $entry = $fileinfo->getFilename();
                            $filedetails = pathinfo($entry);
                            $output[$i]['blkeyword'] = '';
                            $output[$i]['wlkeyword'] = '';
                            $output[$i]['filename'] = '';
                            $blmacthed = array();
                            $wlmacthed = array();
                            if(in_array($filedetails['extension'],$typeext))
                            {
                                $antiword_obj = new Ep_Antiword_Antiword($unzipdir . $entry);
                                $content = $antiword_obj->getContent();
                                //$content = utf8_decode($content);
                                //$article_doc_content=html_entity_decode(htmlentities($article_doc_content, ENT_QUOTES, 'ISO-8859-1'), ENT_QUOTES , 'UTF-8');
                                $blarray = $hotelkeywords_obj->getBlWlKeywords('blacklist',$artLang);
                                $wlarray = $hotelkeywords_obj->getBlWlKeywords('whitelist',$artLang);
                                $text = $this->readDocx($unzipdir . $entry);
                                $content_array = $this->process_xmlData(utf8_decode($text));
                                $badspace = array();
                                foreach ($spacekeywordarray as $ky)
                                {
                                    for($j=1; $j<count($content_array); $j++)
                                    {
                                        $contentres = preg_replace('!\s+!', ' ', $content_array[$j]);
                                        $keywords[$i] = preg_split("/[\s]+/", $contentres);
                                       // print_r($keywords[$i]);
                                        if(in_array($ky,$keywords[$i]))
                                            $badspace[] = "&laquo; ".$ky." &raquo; ligne ".$j;
                                        $sentence = implode(" ", $keywords[$i]);
                                        if(preg_match('/\d{3}/',$sentence))
                                        {
                                            for($n=0; $n<count($keywords[$i]); $n++)
                                            {
                                                if(preg_match('/\d{3}/',$keywords[$j][$n]))
                                                {
                                                    if(is_numeric($keywords[$j][$n-1]))
                                                        $badspace[] = "&laquo; ".$keywords[$j][$n]." &raquo; ligne ".$j;
                                                        break;
                                                }
                                            }
                                        }
                                    }
                                }
                                $badspace = array_unique($badspace);
                                $output[$i]['nospace'] = implode("<br>",$badspace);
                                $output[$i]['filename'] = utf8_decode(basename($entry))."<br>";
                                $wlintersected = array();
                                $wlmacthed = array();
                                foreach ($wlarray AS $wkey => $wvalue) {
                                    if(!preg_match("/".$wvalue."/i",$content)) {
                                        $wlmacthed[] = $wkey;
                                        $wlintersected[] = $wvalue;
                                    }
                                }
                                $blintersected = array();
                                $blmacthed= array();
                                foreach ($blarray AS $key => $value) {
                                    if(preg_match("/".$value."/i",$content)) {
                                        $blmacthed[] = $key;
                                        $blintersected[] = $value;
                                    }
                                }
                                //  echo "<pre>".$output[$i]['filename']."----".print_r($blmacthed);
                                $wlintersected = array_unique($wlintersected);
                                $blintersected = array_unique($blintersected);
                                $output[$i]['blkeyword'] = implode(',', $blintersected); //exit;
                                $output[$i]['wlkeyword'] = implode(',', $wlintersected); //exit;
                                $output[$i]['singlefile'] = "no";
                                //writing output to html file////
                                $outputcontent .= "<tr><td>".$output[$i]['filename']."</td><td><font color='red'>".$output[$i]['blkeyword']."</font></td><td>".$output[$i]['wlkeyword']."</td><td>".$output[$i]['nospace']."</td></tr>";
                                $ext=pathinfo($output[$i]['filename']);
                                $txtfile=$unzipdir.$ext['filename'].'.txt';
                                unlink($txtfile);
                                $result[] = 'no';
                            }else{
                                $result[] = 'no';
                            }
                            $i++;
                        }
                    }
                    $outputcontent .= "</table>";
                    $html_end='</body></html>';
                    if(empty($output[$i]['blkeyword'])  && empty($output[$i]['wlkeyword']) && empty($output[$i]['nospace']))
                        $result[] = 'yes';
                    else
                        $result[] = 'no';
                }
            }
            $outputfile = fopen($htmlfilepath, "w") or die("Unable to open file!");
            fwrite($outputfile, $html_start.$outputcontent.$html_end);
            fclose($outputfile);
            //echo $outputcontent;exit;
            $this->_view->articleId = $articleId;
            $this->_view->participationId = $participationId;
            if($crtpartcipate_id != ''){
                $this->_view->crt_participationId = $crtpartcipate_id;
            }
            else
                $this->_view->crt_participationId = '';
            $this->_view->singlefile = "no";
            $this->_view->blwloutput = $output;
            $this->_view->articlepath = $zippath;
            $this->_view->articleName = $userfilename;
            $this->_view->zipfilename = $userfilename;
            $this->_view->blwlwcheckoutputfile = $realfilepath;
            closedir($unzipdir);//echo "<pre>";print_r($result); exit;
            $html_data=$this->_view->renderHtml("Contrib_hotel_wlblcheck");
            $file_data=str_replace('$$$$file_data$$$',$outputcontent,$html_data);
        }else
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
             $mimetype = finfo_file($finfo, $articleDir.$articleName);
            $filedetails = pathinfo($articleName);
            $typearr = array('application/vnd.ms-office', 'application/zip', 'application/octet-stream');
            if(!in_array($mimetype, $typearr) || (!in_array($filedetails['extension'],$typeext))){
                $docfileerror = 'yes';
                $result[] = 'no';
            }
            else
            {
               // mb_internal_encoding('UTF-8');
                $output['nospace'] = $output['blkeyword'] = $output['wlkeyword'] = '';
                $antiword_obj = new Ep_Antiword_Antiword($articleDir.$articleName);
                $content = $antiword_obj->getContent();
                //$content = utf8_decode($content);
                $text = $this->readDocx($articleDir.$articleName);
                $content_array = $this->process_xmlData(utf8_decode($text));
                //echo "<br>".$content = $this->process_xmlData($text); print_r($content);
                foreach ($spacekeywordarray as $ky)
                {
                    for($i=0; $i<count($content_array); $i++)
                    {
                        $contentres = preg_replace('!\s+!', ' ', $content_array[$i]);
                        $keywords[$i] = preg_split("/[\s]+/", $contentres);
                        //print_r($keywords[$i]);
                        if(in_array($ky,$keywords[$i]))
                            $badspace[] = "&laquo; ".$ky." &raquo;  ligne ".$i;
                        $sentence = implode(" ", $keywords[$i]);
                        if(preg_match('/\d{3}/',$sentence))
                        {
                             for($n=0; $n<count($keywords[$i]); $n++)
                             {
                                 if(preg_match('/\d{3}/',$keywords[$i][$n]))
                                 {
                                     if(is_numeric($keywords[$i][$n-1]))
                                         $badspace[] = "&laquo; ".$keywords[$i][$n]." &raquo;  ligne ".$i;
                                 }
                             }
                        }
                    }
                } //echo "<pre>";
                $badspace = array_unique($badspace);
                $output['nospace'] = implode("<br>",$badspace);
               // $bwlcontent = $this->checkHotelBlackWhiteListKeyword($content);
               // $textfilepath = $articleDir . $textfile[0] . '.txt';
                //$blacklistpath = ROOT_PATH . "FO/articles/blacklist/".$client_id."/blacklist_".$artLang.".txt";
                //$whitelistpath = ROOT_PATH . "FO/articles/blacklist/".$client_id."/whitelist_".$artLang.".txt";
                //$blarray = $this->textToArray($blacklistpath, NULL);
                //$wlarray = $this->textToArray($whitelistpath, NULL); //print_r($wlarray);echo "<br>";
                $blarray = $hotelkeywords_obj->getBlWlKeywords('blacklist',$artLang);
                $wlarray = $hotelkeywords_obj->getBlWlKeywords('whitelist',$artLang);
               // echo "<pre>";
               // print_r($blarray); //exit;
                // $content = utf8_decode($content);
                $output['filename'] = utf8_decode($userfilename);
                $wlmacthed = array();
                foreach ($wlarray AS $wkey => $wvalue) {
                    if(!preg_match("/".$wvalue."/i",$content)) {
                        $wlmacthed[] = $wkey;
                        $wlintersected[] = $wvalue;
                    }
                }
                $blmacthed = array();
               // error_reporting(E_ALL);
                foreach ($blarray AS $key => $value) {
                    if(preg_match("/".$value."/i",$content)) {
                        $blmacthed[] = $key;
                        $blintersected[] = $value;
                    }
                }
                 // print_r($blmacthed); print_r($blintersected); echo "---"; print_r($wlintersected); exit;
                $wlintersected = array_unique($wlintersected);
                $blintersected = array_unique($blintersected);
                foreach ($blintersected AS $key => $value) {
                    $blintersected[$key] = str_replace('<','&lt;',$value);
                }
                foreach ($blintersected AS $key => $value) {
                    $blintersected[$key] = str_replace('>','&gt;',$value);
                }
                $output['blkeyword'] = (implode(', ', $blintersected));// exit;
               // $output['blkeyword'] = utf8_encode(implode(', ', $blintersected));
                $output['wlkeyword'] = implode(', ', $wlintersected); //exit;
                if($output['blkeyword'] == '' && $output['wlkeyword'] == '' && $output['nospace'] == '')
                    $result[] = 'yes';
                else
                    $result[] = 'no';
                $this->_view->singlefile = "yes";
                $this->_view->blwloutput = $output;
                $this->_view->articlepath = $articleDir.$articleName;
                $this->_view->articleName = $articleName;
                $this->_view->articleId = $articleId;
                $this->_view->participationId = $participationId;
                if($crtpartcipate_id != ''){
                    $this->_view->crt_participationId = $crtpartcipate_id;
                }
                else
                    $this->_view->crt_participationId = '';
                $this->_view->blwlwcheckoutputfile = $realfilepath;
                $html_start = "<!DOCTYPE html><html lang=\"en\"><head><meta charset='UTF-8'></head><body>";
                $outputcontent = "<table class='table table-bordered'><tr><th>Nom du fichier</th><th>Mots-cl&eacute;s interdits</th><th>Mots-cl&eacute;s autoris&eacute;s</th><th>Probl&egrave;mes d'espace</th></tr>";
                $outputcontent .= "<tr><td>".$output['filename']."</td><td><font color='red'>".$output['blkeyword']."</font></td><td>".$output['wlkeyword']."</td><td>".$output['nospace']."</td></tr>";
                $outputcontent .= "</table>";
                $html_end= "</body></html>";
                $outputfile = fopen($htmlfilepath, "w") or die("Unable to open file!");
                fwrite($outputfile, $html_start.$outputcontent.$html_end);
                fclose($outputfile);
                $html_data=$this->_view->renderHtml("Contrib_hotel_wlblcheck");
                $file_data=str_replace('$$$$file_data$$$',$outputcontent,$html_data);
            }
        }
       // print_r($output);
      //  print_r($result);  echo $filetypeerror; echo $docfileerror; exit;
       // echo $file_data;exit;
        if(!in_array('no',$result)){
            return 'pass';
        }elseif($filetypeerror == 'yes'){
            return  'filetypeerror';
        }elseif($docfileerror == 'yes'){
            return  'docfileerror';
        }
        else{
           // ini_set('default_charset', 'UTF-8');
            return $file_data;// $this->_view->renderHtml("Contrib_hotel_wlblcheck");
        }
    }
    private function writeExcel($arr_data,$filename){
        print_r($arr_data); echo $filename;
        /*if(file_exists(LIB_PATH."script/PHPExcel"))
            echo "yes";
        else
            echo "no";  echo "hi"; exit;*/
        include(LIB_PATH."script/");
        $excel = new PHPExcel;
        $list = $excel->setActiveSheetIndex(0);
        $rowcounter = 1;
        foreach($arr_data as $key=>$d){
            $chr = "A";
            foreach($d as $d1){
                $list->setCellValue($chr.$rowcounter,$d1);
                $chr++;
            }
            $rowcounter++;
        }
        echo pathinfo($filename, PATHINFO_EXTENSION);
        if(pathinfo($filename, PATHINFO_EXTENSION) == "xls"){
            $writer = new PHPExcel_Writer_Excel5($excel);
        }else if(pathinfo($filename, PATHINFO_EXTENSION) == "xlsx"){ echo "hell";
            $writer = new PHPExcel_Writer_Excel2007($excel);
        }else{
            echo "File is Neither XLS Nor XLSX ";
	    }
        $writer->save($filename);
    }
     /**Send Articles of contributor to Ep team**/
    public function sendarticleAction()
    {
       error_reporting(E_ERROR | E_PARSE);
        $participation=new Ep_Participation_Participation();
        $corrector_obj=new Ep_Participation_CorrectorParticipation();
        $article_obj=new Ep_Article_Article();
        $userIdentifier=$this->contrib_identifier;
        $autoEmails=new Ep_Ticket_AutoEmails();

        $missionParams=$this->_request->getParams();
        $participation_id=$missionParams['participation_id'];
        $client_id=$missionParams['clientId'];
     

        if($this->_helper->EpCustom->checksession())
        {
            $articles=$_FILES;
            
            
            foreach($articles as $participationkey=>$article)
            {
                if($article['name']!='' && $participation_id!='')
                {
                   
                    $participationId=$participation_id;
                    $file=pathinfo($article['name']);
                    //print_r($file);exit;
                    $extension=$file['extension'];
                    $participationDetails=$participation->getParticipationDetails($participationId);
          
                    $participation_status=$participationDetails[0]['status'];
                    //echo $participationId."--".$participation_status;exit;
                    
                    if($participation_status=='bid' || $participation_status=='disapproved' || $participation_status=='disapprove_client')
                    {
                    
                      $articleDir=$this->articles_path.$participationDetails[0]['article_id']."/";
                      if(!is_dir($articleDir))
                        mkdir($articleDir,TRUE);
                      chmod($articleDir,0777);
                      $articleName=$participationDetails[0]['article_id']."_".$participationDetails[0]['user_id']."_".mt_rand(10000,99999).".".$extension;
                      $article_path=$articleDir.$articleName;
                      
                       
                      if (move_uploaded_file($article['tmp_name'], $article_path))
                      {   
                          chmod($article_path,0777);
                          $delivery=new Ep_Article_Delivery();
                          $delivery_details=$delivery->getDeliveryDetails($participationDetails[0]['article_id']);
                           // if($client_id == "150128182032170")  //150128182032170- venere test id testkcompay- 142415937584986
                            /*if($delivery_details[0]['blwl_check'] == "yes")  //150128182032170- venere test id
                            {
                                $checkblwl = $this->checkBlWlLists($articleDir, $articleName, $userIdentifier, $article['name'], $participationId, $participationDetails[0]['article_id'], $client_id);
                                if ($checkblwl == 'pass') {
                                    $autoEmails->messageToEPMail($userIdentifier, 155, $parameters = NULL);
                                }
                                elseif ($checkblwl == 'filetypeerror') {
                                    echo json_encode(array('status'=>'blwlerrormessage', 'result'=>null));
                                    exit;
                                }elseif ($checkblwl == 'docfileerror') {
									if($extension == 'zip' || $extension == 'rar'){
										echo json_encode(array('status'=>'docerrormessage', 'result'=>'multi'));
									}else{
										echo json_encode(array('status'=>'docerrormessage', 'result'=>'single'));
									}
                                    exit;
                                }else {
                                     echo json_encode(array('status'=>'blwlerror', 'result'=>rawurlencode($checkblwl)));
                                     exit;
                                }
                            
                            }*/
			                //hotel development //
                            if($client_id == "120218054512919")  //hotel.com dev
                            {
                                $checkblwl = $this->checkHotelDevLists($articleDir, $articleName, $userIdentifier, $article['name'], $participationId, $participationDetails[0]['article_id'], $client_id, $crtpartcipate_id=null);
                                if ($checkblwl == 'pass') {
                                   // $autoEmails->messageToEPMail($userIdentifier, 155, $parameters = NULL);
                                }
                                elseif ($checkblwl == 'filetypeerror') {
                                    echo json_encode(array('status'=>'blwlerrormessage', 'result'=>null));
                                    exit;
                                }elseif ($checkblwl == 'docfileerror') {
                                    if($extension == 'zip' || $extension == 'rar'){
                                        echo json_encode(array('status'=>'docerrormessage', 'result'=>'multi'));
                                    }else{
                                        echo json_encode(array('status'=>'docerrormessage', 'result'=>'single'));
                                    }
                                    exit;
                                }
                                else {
                                    echo json_encode(array('status'=>'blwlerror', 'result'=>rawurlencode($checkblwl)));
                                    exit;
                                }
			                }
                          //exit;  
                        $participationUpdate['updated_at']=date('Y-m-d h:i:s');
                         // $participationUpdate['article_send_at']=date('Y-m-d h:i:s');
                        $premium=$delivery->checkPremiumAO($participationDetails[0]['article_id']);
                        $locked=$participation->getLockStatus($participationDetails[0]['article_id']);
                        $missionTest=$delivery_details[0]['missiontest'];

                        $client_paid=$delivery_details[0]['paid_status'];
                        /**plagairism check*/
                          $plagiarism_check=$delivery_details[0]['plagiarism_check'];
                        $correctorAO=$article_obj->checkCorrectorAO($participationDetails[0]['article_id']);
                        if($correctorAO=='YES')
                        {
                           
                          /**check whether article is already assigned to a corrector or not**/
                          $corrector_participation_id=$corrector_obj->checkCorrectorParticipationExists($participationDetails[0]['article_id']);
                          
                          $corrector_participation_details=$corrector_obj->getCorrectorParticipationDetails($participation_id);
                          
                          if($this->corrector_type=='senior')
                          {
                            if($delivery_details[0]['correction_sc_submission'])
                            $correction_submission=$delivery_details[0]['correction_sc_submission'];
                            else
                            $correction_submission=$this->config['correction_sc_submission'];
                          }
                          else
                          {
                            if($delivery_details[0]['correction_jc_submission'])
                            $correction_submission=$delivery_details[0]['correction_jc_submission'];
                            else
                            $correction_submission=$this->config['correction_jc_submission'];
                          }
                          
                          if($corrector_participation_id!="NO")
                          {
                            
                            //$expires=time()+(60*60*$correction_submission);
                            $expires=time()+(60*$correction_submission);
                            $cparticipationUpdate['participate_id']=$participationId;
                            $cparticipationUpdate['corrector_submit_expires']=$expires;
                            $corrector_obj->updateParticipationDetails($cparticipationUpdate,$corrector_participation_id);
                            
                              /**send mail to corrector*/
                              $autoEmail=new Ep_Ticket_AutoEmails();
                              $correctorId=$corrector_participation_details[0]['corrector_id'];
                              $refused_at=$participationDetails[0]['updated_at'];
                              $parameters['article_title']=$deliveryDetails[0]['articleName'];
                              $parameters['updated_at']=date("d-m-Y H:i:s",strtotime($refused_at));
                              $parameters['ongoinglink']='/contrib/mission-corrector-deliver?article_id='.$participationDetails[0]['article_id'];
                              //$autoEmail->messageToEPMail($correctorId,62,$parameters);
                              
                              
                          }
                          else
                          {
                            if($delivery_details[0]['correction_participation'])
                            {
                              $article_obj=new Ep_Article_Article();
                              $expires=time()+(60*$delivery_details[0]['correction_participation']);
                              $data_array = array("correction_participationexpires"=>$expires);////////updating
                              $query=" id='".$participationDetails[0]['article_id']."'";
                              $article_obj->updateArticle($data_array,$query);
                            }
                            
                          }

                        }
                        if($locked=='NO')
                        {
                          if($premium=='YES')
                            $participationUpdate['status']='under_study';
                          else if($premium=='NO')
                          {
                             $participationUpdate['status']='under_study';
                          }
                        }
                        else if($locked=='YES')
                        {
                          $participationUpdate['status']='under_study';
                        }
                        if($premium=='YES')
                        {
                          if($correctorAO=='YES')
                            $participationUpdate['current_stage']='corrector';
                          else
                            $participationUpdate['current_stage']='stage1';
                        }
                        else if($premium=='NO')
                          $participationUpdate['current_stage']='client';

                        if($plagiarism_check=='yes')
                        {
                          $participationUpdate['current_stage']='stage0';
                          $participationUpdate['status']='plag_exec';
                        }  

                        if($client_paid=='paid' && $premium=='NO')
                        {
                          $article_obj=new Ep_Article_Article();
                          $downloadtime=date("Y-m-d H:i:s");
                          $update_array = array("downloadtime"=>$downloadtime);////////updating
                          $query=" id='".$participationDetails[0]['article_id']."'";
                          $article_obj->updateArticle($update_array,$query);

                        }

                        //Antiword obj to get content from uploaded article 
                         $antiword_obj=new Ep_Antiword_Antiword($article_path);
                         $article_doc_content=$antiword_obj->getContent();

                         //echo  $article_doc_content;exit;

                         $article_words_count=$antiword_obj->count_words($article_doc_content);
                         
                         // $participationUpdate['article_path']=$participationDetails[0]['article_id']."/".$articleName;
                        $participation->updateParticipationDetails($participationUpdate,$participationId);
                        /**Insert into Article Process Table**/
                        $ArticleProcess=new EP_Article_ArticleProcess();
                        $maxversion=$ArticleProcess->getLatestVersion($participationId);
                        if($maxversion[0]['latestVersion'])
                          $version=$maxversion[0]['latestVersion'];
                        else 
                          $version=1;
                        $ArticleProcess->participate_id=$participationId;
                        $ArticleProcess->user_id=$participationDetails[0]['user_id'];
                        $ArticleProcess->article_path=$participationDetails[0]['article_id']."/".$articleName;
                        $ArticleProcess->version=$version;
                        $ArticleProcess->article_doc_content=utf8dec(nl2br($article_doc_content));                       
                        $ArticleProcess->article_words_count=$article_words_count;
                        if($premium=='YES')
                        {
                          if($correctorAO=='YES')
                            $ArticleProcess->stage='corrector';
                          else
                            $ArticleProcess->stage='s1';
                        }
                        else if($premium=='NO')
                        {
                          $ArticleProcess->stage='client';
                          $ArticleProcess->status='under_study';
                        }

                        $ArticleProcess->stage='contributor';

                        $ArticleProcess->article_name=str_replace(" ",'_',$this->utf8dec(trim($article['name'])));
                         
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
                              //$userp_obj=new Ep_User_UserPlus();
                              //$detailsC=$userp_obj->getUsername($this->_view->clientidentifier);
                            $contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$this->contrib_identifier.'" target="_blank"><b>'.$this->_view->client_email.'</b></a>';
                            $actionmessage=strip_tags($sentence9);
                            eval("\$actionmessage= \"$actionmessage\";");
                            $history9['stage']=$participationUpdate['current_stage'];
                            $history9['action']='article_sent';
                            $history9['action_sentence']=$actionmessage;
                            $hist_obj->insertHistory($history9);

                      /**Inserting Royalties if payment paid for the article and updating price and changing the
                         Status to closed for other participation's and approved status in article process**/

                        if($premium=='NO')
                        {
                          $this->publishNonPremiumArticle($participationId,$participationDetails[0]['article_id']);
                        }    


                         
                        if($correctorAO=='YES' && $corrector_participation_id=="NO")
                        {
                          
                          $emailSend=$delivery_details[0]['corrector_mail'];
                          $corrector_list=$delivery_details[0]['corrector_list'];
                          $parameters['article_title']=$delivery_details[0]['articleName'];
                          $parameters['corrector_ao_link']='/contrib/aosearch';
                          //$parameters['correction_jc_submission']=$delivery_details[0]['correction_jc_submission'];
                          //$parameters['correction_sc_submission']=$delivery_details[0]['correction_sc_submission'];
                          $parameters['participation_expires']=$expires;
                          $parameters['missiontest']=$missionTest;
                            
                          if($emailSend=='yes' && $plagiarism_check!='yes')
                            $this->sendCorrectionAOCreationMail($parameters,$corrector_list);
                        }
                        
                        $uploaded_status=1;
                         

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
                          
                          $mail_params['comment_bo_link']='/followup/delivery?client_id='.$delivery_details[0]['user_id'].'&ao_id='.$delivery_details[0]['id'].'&submenuId=ML3-SL10';

                          $autoEmails->messageToEPMail($bo_user,101,$mail_params,TRUE);
                        }  
                        //exit;
                      }
                      else
                      {
                       echo json_encode(array('status'=>'error'));
                        exit;
                      }  
                    }
                    else
                    {
                      echo json_encode(array('status'=>'file_sent'));
                        exit;
                    }
                }
            }
            /**Code to extend the Time 6hr for senior contributor**/
            if($uploaded_status)
            {
                $time=$this->config['sc_bonus'];//extending 6hrs for other participation's of user*/
                $expires=(60*60*$time);
                $data_array = array("article_submit_expires"=>new Zend_Db_Expr('article_submit_expires+'.$expires));////////updating
                $query=" status='bid' and user_id='".$userIdentifier."'";
                $participation->updateArticleSubmitExpires($data_array,$query);

                echo json_encode(array('status'=>'success'));
            }
            else
              echo json_encode(array('status'=>'error'));
            
            //$this->_helper->FlashMessenger('Article sent');
            //$this->_redirect("/contrib/ongoing");
        }
    }
    /**download article of  a version from article Process**/
    public function downloadVersionArticleAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $articleParams=$this->_request->getParams();
            $identifier= $this->EP_Contrib_reg->clientidentifier;
            $articleProcessObj=new EP_Article_ArticleProcess();
            $attachment=new Ep_Ticket_Attachment();
            $article_path=$articleProcessObj->getArticlePath($articleParams['processid']);
            if($article_path!='NO')
            {
                if(file_exists($this->articles_path.$article_path[0]['article_path']))
                {
                    $attachment->downloadAttachment($this->articles_path.$article_path[0]['article_path'],"attachment",$article_path[0]['article_name']);
                }
            }
        }
    }
    
    ////cheching the whitelist keyword is missing or not//if misses returns true or false
    protected function checkWhiteListKeywordMissed($filepath, $wlkeyword)
    {
        ///checking for the white list keyword in each paragraphs
        $filedetails = pathinfo($filepath);
        $antiword_obj=new Ep_Antiword_Antiword($filepath);
         $content=$antiword_obj->getContent();
         $content = preg_replace('!\s+!', ' ', $content);

        preg_match_all("/(PARAGRAPH.*?)\]/si", $content, $matches);
        $paraarray = $matches[0];
        foreach($paraarray as $k=>$v)
        {
            if(preg_match("/".$wlkeyword."/i", $v)) {
                $wlpresent[] = 'yes';
            } else {
                $wlpresent[] = 'no'; //white keyword is missing
            }
        }
        preg_match_all("/(Title.*?)\]/si", $content, $tmatches);
        $titlearray = $tmatches[0];
        foreach($titlearray as $tk=>$tv)
        {
            if(preg_match("/".$wlkeyword."/i", $tv)) {
                $wlpresent[] = 'yes';
            } else {
                $wlpresent[] = 'no'; //white keyword is missing
            }
        }
        // get the not covered white list words//
        if(in_array('no',$wlpresent))
            return true; ///missed the keyword
        else
            return false; ///keyword is not missing{no problem}
    }
    public function sendarticlewithwlnewkeywordAction()
    {
        $participation = new Ep_Participation_Participation();
        $corrector_obj = new Ep_Participation_CorrectorParticipation();
        $article_obj = new Ep_Article_Article();
        $userIdentifier = $this->contrib_identifier;
        $missionParams = $this->_request->getParams();
        /*print_r($missionParams); echo "<br>";
        print_r($missionParams['newkeyword']);exit;*/
        if ($missionParams['single'] == 'yes') {
            $wlnewkeyword = $missionParams['newkeyword'];
            $participationId = $missionParams['participation_id'];
            $article_path = $missionParams['articlepath'];
            $articleName = $missionParams['articleName'];
            $article['name'] = $missionParams['filename'];
            $wlresult = $this->checkWhiteListKeywordMissed($article_path, $wlnewkeyword);
            if ($wlresult == true) //new keyword is missing in anyone of paragraphs in documents
            {
                echo json_encode(array('status' => 'wlnwkwmissing'));
                exit;
            }
            ////forming the whitelist keyword with filename in articleprocess table////
            $articlefilewithwlkeyword = $article['name'] . "/" . utf8_decode($wlnewkeyword);

        } else {
            // $missionParams['proposewl'][0];
            $participationId = $missionParams['participationId'][0];
            $article_path = $missionParams['articlepath'][0];
            $filearray = explode(".", $article_path);
            $workfolder = $filearray[0];
            // $articleName=$missionParams['articleName'][0];
            $articleName = $workfolder . "zip";
            $wordcount = $missionParams['wordcount'][0];
            $article['name'] = $missionParams['zipfilename'][0];
            $articlefilewithwlkeyword = '';
            $nkword = explode(",", $missionParams['nkword']);
            for ($i = 0; $i < count($nkword); $i++) {
                $filepath = ROOT_PATH . "FO/articles/" . $workfolder . "/" . $missionParams['filename'][$i];
                // $filepath = $workfolder."/".$missionParams['filename'][$i];
                $wlresult = $this->checkWhiteListKeywordMissed($filepath, $nkword[$i]);
                if ($wlresult == true) //new keyword is missing in anyone of paragraphs in documents
                {
                    echo json_encode(array('status' => 'wlnwkwmissing'));
                    exit;
                }
                ////forming the whitelist keyword with filename in articleprocess table////
                $articlefilewithwlkeyword .= $missionParams['filename'][$i] . "/" . utf8_decode($nkword[$i]) . "|";
            }

        }
        // echo $articlefilewithwlkeyword; exit;


        $participationDetails = $participation->getParticipationDetails($participationId);

        $participationUpdate['updated_at'] = date('Y-m-d h:i:s');
        // $participationUpdate['article_send_at']=date('Y-m-d h:i:s');
        $delivery = new Ep_Article_Delivery();
        $premium = $delivery->checkPremiumAO($participationDetails[0]['article_id']);
        $locked = $participation->getLockStatus($participationDetails[0]['article_id']);
        $delivery_details = $delivery->getDeliveryDetails($participationDetails[0]['article_id']);
        $missionTest = $delivery_details[0]['missiontest'];

        $client_paid = $delivery_details[0]['paid_status'];
        /**plagairism check*/
        $plagiarism_check = $delivery_details[0]['plagiarism_check'];
        $correctorAO = $article_obj->checkCorrectorAO($participationDetails[0]['article_id']);
        if ($correctorAO == 'YES') {

            /**check whether article is already assigned to a corrector or not**/
            $corrector_participation_id = $corrector_obj->checkCorrectorParticipationExists($participationDetails[0]['article_id']);

            $corrector_participation_details = $corrector_obj->getCorrectorParticipationDetails($participationId);

            if ($this->corrector_type == 'senior') {
                if ($delivery_details[0]['correction_sc_submission'])
                    $correction_submission = $delivery_details[0]['correction_sc_submission'];
                else
                    $correction_submission = $this->config['correction_sc_submission'];
            } else {
                if ($delivery_details[0]['correction_jc_submission'])
                    $correction_submission = $delivery_details[0]['correction_jc_submission'];
                else
                    $correction_submission = $this->config['correction_jc_submission'];
            }

            if ($corrector_participation_id != "NO") {

                //$expires=time()+(60*60*$correction_submission);
                $expires = time() + (60 * $correction_submission);
                $cparticipationUpdate['participate_id'] = $participationId;
                $cparticipationUpdate['corrector_submit_expires'] = $expires;
                $corrector_obj->updateParticipationDetails($cparticipationUpdate, $corrector_participation_id);

                /**send mail to corrector*/
                $autoEmail = new Ep_Ticket_AutoEmails();
                $correctorId = $corrector_participation_details[0]['corrector_id'];
                $refused_at = $participationDetails[0]['updated_at'];
                $parameters['article_title'] = $deliveryDetails[0]['articleName'];
                $parameters['updated_at'] = date("d-m-Y H:i:s", strtotime($refused_at));
                $parameters['ongoinglink'] = '/contrib/mission-corrector-deliver?article_id=' . $participationDetails[0]['article_id'];
                //$autoEmail->messageToEPMail($correctorId,62,$parameters);


            } else {
                if ($delivery_details[0]['correction_participation']) {
                    $article_obj = new Ep_Article_Article();
                    $expires = time() + (60 * $delivery_details[0]['correction_participation']);
                    $data_array = array("correction_participationexpires" => $expires);////////updating
                    $query = " id='" . $participationDetails[0]['article_id'] . "'";
                    $article_obj->updateArticle($data_array, $query);
                }

            }

        }
        if ($locked == 'NO') {
            if ($premium == 'YES')
                $participationUpdate['status'] = 'under_study';
            else if ($premium == 'NO') {
                $participationUpdate['status'] = 'under_study';
            }
        } else if ($locked == 'YES') {
            //$participationUpdate['status']='on_hold';
            $participationUpdate['status'] = 'under_study';
        }
        if ($premium == 'YES') {
            if ($correctorAO == 'YES')
                $participationUpdate['current_stage'] = 'corrector';
            else
                $participationUpdate['current_stage'] = 'stage1';
        } else if ($premium == 'NO')
            $participationUpdate['current_stage'] = 'client';

        if ($plagiarism_check == 'yes') {
            $participationUpdate['current_stage'] = 'stage0';
            $participationUpdate['status'] = 'plag_exec';
        }

        if ($client_paid == 'paid' && $premium == 'NO') {
            $article_obj = new Ep_Article_Article();
            $downloadtime = date("Y-m-d H:i:s");
            $update_array = array("downloadtime" => $downloadtime);////////updating
            $query = " id='" . $participationDetails[0]['article_id'] . "'";
            $article_obj->updateArticle($update_array, $query);

        }

        //Antiword obj to get content from uploaded article
        if (!$wordcount) {
            $antiword_obj = new Ep_Antiword_Antiword($article_path);
            $article_doc_content = $antiword_obj->getContent();
            //echo  $article_doc_content;exit;
            $article_words_count = $antiword_obj->count_words($article_doc_content);
        } else {
            $article_words_count = $wordcount;
        }

        // $participationUpdate['article_path']=$participationDetails[0]['article_id']."/".$articleName;
        $participation->updateParticipationDetails($participationUpdate, $participationId);
        /**Insert into Article Process Table**/
        $ArticleProcess = new EP_Article_ArticleProcess();
        $maxversion = $ArticleProcess->getLatestVersion($participationId);
        if ($maxversion[0]['latestVersion'])
            $version = $maxversion[0]['latestVersion'];
        else
            $version = 1;
        $ArticleProcess->participate_id = $participationId;
        $ArticleProcess->user_id = $participationDetails[0]['user_id'];
        $ArticleProcess->article_path = $participationDetails[0]['article_id'] . "/" . $articleName;
        $ArticleProcess->version = $version;
        $ArticleProcess->article_doc_content = utf8dec(nl2br($article_doc_content));
        $ArticleProcess->article_words_count = $article_words_count;
        $ArticleProcess->whitelist_newkeywords = $articlefilewithwlkeyword;

        if ($premium == 'YES') {
            if ($correctorAO == 'YES')
                $ArticleProcess->stage = 'corrector';
            else
                $ArticleProcess->stage = 's1';
        } else if ($premium == 'NO') {
            $ArticleProcess->stage = 'client';
            $ArticleProcess->status = 'under_study';
        }

        $ArticleProcess->stage = 'contributor';

        $ArticleProcess->article_name = str_replace(" ", '_', $this->utf8dec(trim($article['name'])));

        $ArticleProcess->insert();

        //Article History insertion
        $hist_obj = new Ep_Article_ArticleHistory();
        $action_obj = new Ep_Article_ArticleActions();
        $history9 = array();
        $history9['user_id'] = $this->contrib_identifier;
        $history9['article_id'] = $participationDetails[0]['article_id'];
        $sentence9 = $action_obj->getActionSentence(9);
        $ArtDetails = $article_obj->getArticleAOdetails($participationDetails[0]['article_id']);
        $article_name = '<b>' . $ArtDetails[0]['title'] . '</b>';
        //$userp_obj=new Ep_User_UserPlus();
        //$detailsC=$userp_obj->getUsername($this->_view->clientidentifier);
        $contributor_name = '<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId=' . $this->contrib_identifier . '" target="_blank"><b>' . $this->_view->client_email . '</b></a>';
        $actionmessage = strip_tags($sentence9);
        eval("\$actionmessage= \"$actionmessage\";");
        $history9['stage'] = $participationUpdate['current_stage'];
        $history9['action'] = 'article_sent';
        $history9['action_sentence'] = $actionmessage;
        $hist_obj->insertHistory($history9);

        /**Inserting Royalties if payment paid for the article and updating price and changing the
         * Status to closed for other participation's and approved status in article process**/

        if ($premium == 'NO') {
            $this->publishNonPremiumArticle($participationId, $participationDetails[0]['article_id']);
        }


        if ($correctorAO == 'YES' && $corrector_participation_id == "NO") {

            $emailSend = $delivery_details[0]['corrector_mail'];
            $corrector_list = $delivery_details[0]['corrector_list'];
            $parameters['article_title'] = $delivery_details[0]['articleName'];
            $parameters['corrector_ao_link'] = '/contrib/aosearch';
            //$parameters['correction_jc_submission']=$delivery_details[0]['correction_jc_submission'];
            //$parameters['correction_sc_submission']=$delivery_details[0]['correction_sc_submission'];
            $parameters['participation_expires'] = $expires;
            $parameters['missiontest'] = $missionTest;

            if ($emailSend == 'yes' && $plagiarism_check != 'yes')
                $this->sendCorrectionAOCreationMail($parameters, $corrector_list);
        }

        $uploaded_status = 1;


        //Inserting Recent Activities
        $activity_obj = new Ep_User_RecentActivities();
        $client_id = $delivery_details[0]['user_id'];
        if ($delivery_details[0]['premium_option'] == '0') {
            if ($client_id) {
                $activity_array['type'] = 'sentarticle';
                $activity_array['created_at'] = date("Y-m-d H:i:s");
                $activity_array['user_id'] = $client_id;
                $activity_array['activity_by'] = $userIdentifier;
                $activity_array['article_id'] = $participationDetails[0]['article_id'];
                $activity_obj->insertRecentActivities($activity_array);
            }
        }

        //sending notify emails to bo user when writersends the article
        $writer_notify = $delivery_details[0]['writer_notify'];
        if ($writer_notify == 'yes') {
            $autoEmails = new Ep_Ticket_AutoEmails();
            $ticket_obj = new Ep_Ticket_Ticket();

            $bo_user = $delivery_details[0]['created_user'];

            $mail_params['AO_title'] = $delivery_details[0]['deliveryTitle'];
            $mail_params['article_name'] = $delivery_details[0]['articleName'];
            $mail_params['contributor_name'] = $this->_view->client_email;
            $mail_params['bo_user'] = $ticket_obj->getUserName($bo_user, TRUE);

            $mail_params['comment_bo_link'] = '/followup/delivery?client_id='. $delivery_details[0]['user_id'] . '&ao_id=' . $delivery_details[0]['id'] . '&submenuId=ML3-SL10';

            $autoEmails->messageToEPMail($bo_user, 101, $mail_params, TRUE);
        }
    }
    /// finding the proposed words in document and if not found submit the file
    public function sendarticleHotelsDevAction()
    {
        $participation=new Ep_Participation_Participation();
        $corrector_obj=new Ep_Participation_CorrectorParticipation();
        $article_obj=new Ep_Article_Article();
        $userIdentifier=$this->contrib_identifier;
        $missionParams=$this->_request->getParams();
       // print_r($missionParams); echo "<br>";
       // print_r($missionParams['newkeyword']);exit;

        $participationId = $missionParams['participation_id'];
        $article_path = $missionParams['articlepath'];
        $articleName = $missionParams['articleName'];
        $article['name'] = $missionParams['filename'];
        ////forming the whitelist keyword with filename in articleprocess table////
        $hotelsblwlcheckoutput = $missionParams['outputfilename'];

        $participationDetails=$participation->getParticipationDetails($participationId);

        $participationUpdate['updated_at']=date('Y-m-d h:i:s');
        // $participationUpdate['article_send_at']=date('Y-m-d h:i:s');
        $delivery=new Ep_Article_Delivery();
        $premium=$delivery->checkPremiumAO($participationDetails[0]['article_id']);
        $locked=$participation->getLockStatus($participationDetails[0]['article_id']);
        $delivery_details=$delivery->getDeliveryDetails($participationDetails[0]['article_id']);
        $missionTest=$delivery_details[0]['missiontest'];

        $client_paid=$delivery_details[0]['paid_status'];
        /**plagairism check*/
        $plagiarism_check=$delivery_details[0]['plagiarism_check'];
        $correctorAO=$article_obj->checkCorrectorAO($participationDetails[0]['article_id']);
        if($correctorAO=='YES')
        {

            /**check whether article is already assigned to a corrector or not**/
            $corrector_participation_id=$corrector_obj->checkCorrectorParticipationExists($participationDetails[0]['article_id']);

            $corrector_participation_details=$corrector_obj->getCorrectorParticipationDetails($participationId);

            if($this->corrector_type=='senior')
            {
                if($delivery_details[0]['correction_sc_submission'])
                    $correction_submission=$delivery_details[0]['correction_sc_submission'];
                else
                    $correction_submission=$this->config['correction_sc_submission'];
            }
            else
            {
                if($delivery_details[0]['correction_jc_submission'])
                    $correction_submission=$delivery_details[0]['correction_jc_submission'];
                else
                    $correction_submission=$this->config['correction_jc_submission'];
            }

            if($corrector_participation_id!="NO")
            {

                //$expires=time()+(60*60*$correction_submission);
                $expires=time()+(60*$correction_submission);
                $cparticipationUpdate['participate_id']=$participationId;
                $cparticipationUpdate['corrector_submit_expires']=$expires;
                $corrector_obj->updateParticipationDetails($cparticipationUpdate,$corrector_participation_id);

                /**send mail to corrector*/
                $autoEmail=new Ep_Ticket_AutoEmails();
                $correctorId=$corrector_participation_details[0]['corrector_id'];
                $refused_at=$participationDetails[0]['updated_at'];
                $parameters['article_title']=$article['name'];
                $parameters['updated_at']=date("d-m-Y H:i:s",strtotime($refused_at));
                $parameters['ongoinglink']='/contrib/mission-corrector-deliver?article_id='.$participationDetails[0]['article_id'];
                //$autoEmail->messageToEPMail($correctorId,62,$parameters);


            }
            else
            {
                if($delivery_details[0]['correction_participation'])
                {
                    $article_obj=new Ep_Article_Article();
                    $expires=time()+(60*$delivery_details[0]['correction_participation']);
                    $data_array = array("correction_participationexpires"=>$expires);////////updating
                    $query=" id='".$participationDetails[0]['article_id']."'";
                    $article_obj->updateArticle($data_array,$query);
                }

            }

        }
        if($locked=='NO')
        {
            if($premium=='YES')
                $participationUpdate['status']='under_study';
            else if($premium=='NO')
            {
                $participationUpdate['status']='under_study';
            }
        }
        else if($locked=='YES')
        {
            $participationUpdate['status']='under_study';
        }
        if($premium=='YES')
        {
            if($correctorAO=='YES')
                $participationUpdate['current_stage']='corrector';
            else
                $participationUpdate['current_stage']='stage1';
        }
        else if($premium=='NO')
            $participationUpdate['current_stage']='client';

        if($plagiarism_check=='yes')
        {
            $participationUpdate['current_stage']='stage0';
            $participationUpdate['status']='plag_exec';
        }

        if($client_paid=='paid' && $premium=='NO')
        {
            $article_obj=new Ep_Article_Article();
            $downloadtime=date("Y-m-d H:i:s");
            $update_array = array("downloadtime"=>$downloadtime);////////updating
            $query=" id='".$participationDetails[0]['article_id']."'";
            $article_obj->updateArticle($update_array,$query);

        }

        //Antiword obj to get content from uploaded article
        if(!$wordcount){
            $antiword_obj=new Ep_Antiword_Antiword($article_path);
            $article_doc_content=$antiword_obj->getContent();
            //echo  $article_doc_content;exit;
            $article_words_count = $antiword_obj->count_words($article_doc_content);
        }else{
            $article_words_count = $wordcount;
        }

        // $participationUpdate['article_path']=$participationDetails[0]['article_id']."/".$articleName;
        $participation->updateParticipationDetails($participationUpdate,$participationId);
        /**Insert into Article Process Table**/
        $ArticleProcess=new EP_Article_ArticleProcess();
        $maxversion=$ArticleProcess->getLatestVersion($participationId);
        if($maxversion[0]['latestVersion'])
            $version=$maxversion[0]['latestVersion'];
        else
            $version=1;
        $ArticleProcess->participate_id=$participationId;
        $ArticleProcess->user_id=$participationDetails[0]['user_id'];
        $ArticleProcess->article_path=$participationDetails[0]['article_id']."/".$articleName;
        $ArticleProcess->version=$version;
        $ArticleProcess->article_doc_content=utf8dec(nl2br($article_doc_content));
        $ArticleProcess->article_words_count=$article_words_count;
        $ArticleProcess->whitelist_newkeywords=$hotelsblwlcheckoutput;

        if($premium=='YES')
        {
            if($correctorAO=='YES')
                $ArticleProcess->stage='corrector';
            else
                $ArticleProcess->stage='s1';
        }
        else if($premium=='NO')
        {
            $ArticleProcess->stage='client';
            $ArticleProcess->status='under_study';
        }

        $ArticleProcess->stage='contributor';

        $ArticleProcess->article_name=str_replace(" ",'_',$this->utf8dec(trim($article['name'])));
        // $ArticleProcess->article_name=trim($article['name']);

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
        //$userp_obj=new Ep_User_UserPlus();
        //$detailsC=$userp_obj->getUsername($this->_view->clientidentifier);
        $contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$this->contrib_identifier.'" target="_blank"><b>'.$this->_view->client_email.'</b></a>';
        $actionmessage=strip_tags($sentence9);
        eval("\$actionmessage= \"$actionmessage\";");
        $history9['stage']=$participationUpdate['current_stage'];
        $history9['action']='article_sent';
        $history9['action_sentence']=$actionmessage;
        $hist_obj->insertHistory($history9);

        /**Inserting Royalties if payment paid for the article and updating price and changing the
        Status to closed for other participation's and approved status in article process**/

        if($premium=='NO')
        {
            $this->publishNonPremiumArticle($participationId,$participationDetails[0]['article_id']);
        }



        if($correctorAO=='YES' && $corrector_participation_id=="NO")
        {

            $emailSend=$delivery_details[0]['corrector_mail'];
            $corrector_list=$delivery_details[0]['corrector_list'];
            $parameters['article_title']=$delivery_details[0]['articleName'];
            $parameters['corrector_ao_link']='/contrib/aosearch';
            //$parameters['correction_jc_submission']=$delivery_details[0]['correction_jc_submission'];
            //$parameters['correction_sc_submission']=$delivery_details[0]['correction_sc_submission'];
            $parameters['participation_expires']=$expires;
            $parameters['missiontest']=$missionTest;

            if($emailSend=='yes' && $plagiarism_check!='yes')
                $this->sendCorrectionAOCreationMail($parameters,$corrector_list);
        }

        $uploaded_status=1;


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

            $mail_params['comment_bo_link']='/followup/delivery?client_id='.$delivery_details[0]['user_id'].'&ao_id='.$delivery_details[0]['id'].'&submenuId=ML3-SL10';

            $autoEmails->messageToEPMail($bo_user,101,$mail_params,TRUE);
        }
        /**Code to extend the Time 6hr for senior contributor**/
        if($uploaded_status)
        {
            $time=$this->config['sc_bonus'];//extending 6hrs for other participation's of user*/
            $expires=(60*60*$time);
            $data_array = array("article_submit_expires"=>new Zend_Db_Expr('article_submit_expires+'.$expires));////////updating
            $query=" status='bid' and user_id='".$userIdentifier."'";
            $participation->updateArticleSubmitExpires($data_array,$query);

            echo json_encode(array('status'=>'success'));
        }
    }


    /**download hotels.com blwl check output of  a version from article Process**/
    public function downloadHotelsVersionAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $articleParams=$this->_request->getParams();
            $identifier= $this->EP_Contrib_reg->clientidentifier;
            $articleProcessObj=new EP_Article_ArticleProcess();
            $attachment=new Ep_Ticket_Attachment();
            if($articleParams['type'] = 'hotleblwlcheck')
            {
                $filepath = '/home/sites/site5/web/FO/articles/';
                $article_path=$filepath.$articleParams['blwlpath'];

                if(file_exists($article_path))
                {
                    $attachment->downloadAttachment($article_path,"attachment",'hotels blwl check output');
                }
            }

        }
    }
    /**Inserting Royalties if payment paid for the article and updating price and changing the
       Status to closed for other participation's and approved status in article process**/
    public function publishNonPremiumArticle($participationId,$articleId)
    {
            
            $participate_obj=new Ep_Participation_Participation();
            $article_obj=new Ep_Article_Article();
            $autoEmails=new Ep_Ticket_AutoEmails();
            $delivery=new Ep_Article_Delivery();
            $partId = $participationId ;
            $artId = $articleId;
            
            $payment_status=$article_obj->checkPaymentStatus($artId);
            $recentversion= $participate_obj->getRecentVersion($partId);
          if($payment_status=='notpaid')
                $data = array("current_stage"=>"client", "status"=>"under_study");////////updating
          else if($payment_status=='paid')
            {
                $data = array("current_stage"=>"client", "status"=>"under_study");////////updating
                /*$paricipationdetails=$participate_obj->getParticipateDetails($partId);
                $royalty_obj=new Ep_Royalty_Royalties();
                if($royalty_obj->checkRoyaltyExists($paricipationdetails[0]['article_id'])=='NO')
                {
                    $royalty_obj->participate_id=$paricipationdetails[0]['participateId'];
                    $royalty_obj->article_id=$paricipationdetails[0]['article_id'];
                    $royalty_obj->user_id=$paricipationdetails[0]['user_id'];
                    $royalty_obj->price=$paricipationdetails[0]['price_user'];
                    $royalty_obj->insert();
                }*/
             }
            $query = "article_id= '".$artId."' AND id = '".$partId."'";
            $participate_obj->updateParticipation($data,$query);
            ////udate status participation table for status///////
               /*$data = array("status"=>"closed", "current_stage"=>"client");////////updating
               $query = "article_id= '".$artId."' AND id != '".$partId."'";
               $participate_obj->updateParticipation($data,$query);*/

           ///////update in article///////////
            /*$data = array("file_path"=>$recentversion[0]['article_path']);////////updating
            $query = "id= '".$artId."'";
            $article_obj->updateArticle($data,$query);*/
            
             /////udate status article process table///////
                 /*$data = array("status"=>"autoapproved", "stage"=>'client');////////updating
                 $query = "participate_id= '".$partId."' AND version='".$recentversion[0]['version']."'";
                 $participate_obj->updateArticleProcess($data,$query);*/
            ////updating trigger ///////
            //$trigger=$article_obj->updateParticipationTrigger($artId);
            /**Sending Mails***/
                $paricipationdetails=$participate_obj->getParticipateDetails($partId);
        
                $contribDetails=$autoEmails->getUserDetails($paricipationdetails[0]['user_id']);
                if($contribDetails[0]['username']!=NULL)
                  $parameters['contributor_name']= $contribDetails[0]['username'];
                else
                  $parameters['contributor_name']= $contribDetails[0]['email'];
                $parameters['created_date']=date("d/m/Y",strtotime($paricipationdetails[0]['created_at']));
                $parameters['document_link']="/client/ongoingnopremium";
                $parameters['invoice_link']="/client/invoice";
                $parameters['royalty']=$paricipationdetails[0]['price_user'];
                $parameters['article_title']=$paricipationdetails[0]['title'];
                $parameters['ongoinglink']='/client/quotes?id='.$articleId;
                $clientDetails=$autoEmails->getUserDetails($paricipationdetails[0]['clientId']);
                if($clientDetails[0]['username']!=NULL)
                   $parameters['client_name']= $clientDetails[0]['username'];
                else
                  $parameters['client_name']= $clientDetails[0]['email'];
                //$this->sendAutoEmail($to,1,'validated','client',$parameters);
                $deldetails=$delivery->getDeliveryDetails($artId);
                /**sending mail to Client**/
                   
                   if($deldetails[0]['mail_send']=='yes')
                    {
                        //mail to ep mail box
                        $autoEmails->messageToEPMail($paricipationdetails[0]['clientId'],34,$parameters);
                    }
                /**sending mail to Contributor**/
                //$autoEmails->messageToEPMail($paricipationdetails[0]['user_id'],3,$parameters);
                                
                /*if($deldetails[0]['id'])
                {
                    $checkLastAO=$delivery->checkLastArticleAO($deldetails[0]['id']);
                    
                    if($checkLastAO=="YES")
                    {
                        //$autoEmails->sendAutoEmail($clientDetails[0]['email'],2,$parameters);
                        if($deldetails[0]['mail_send']=='yes')
                        $autoEmails->messageToEPMail($paricipationdetails[0]['clientId'],2,$parameters);
                    }
                }*/
                /**ENDED**/
    }


    //Mission published Article Details
    public function missionPublishedAction()
    {
      if($this->_helper->EpCustom->checksession())
      {
           $missionParams=$this->_request->getParams();
           $article_id=$missionParams['article_id'];
           
           if($article_id)
           {
              $published_params['search_article_id']=$article_id;
              $publishedArticle=$this->publishedArticles($published_params);
              
              if(is_array($publishedArticle) && count($publishedArticle)>0)
              {
                     //Comment Details
                    $comment_type='article';
                    $comments_obj=new Ep_Comments_Adcomments();
                    $commentDetails=$comments_obj->getAdComments($article_id,$comment_type);
                    if(count($commentDetails)>0)
                    {
                        $commentDetails=$this->formatCommentDetails($commentDetails);                  
                    }    
                    $deliveryName=$publishedArticle[0]['deliveryName'];
                    $participation_id=$publishedArticle[0]['participate_id'];


                    /**getting All versions of Articles w.r.t User**/
                    if($participation_id)
                    {
                      $articleProcessObj=new EP_Article_ArticleProcess();
                      $participation_obj=new Ep_Participation_Participation();
                      $versionArticleDetails= $participation_obj->getParticipationDetails($participation_id);  ///getting the paricipate id from participate atable/
                      $participationId = $versionArticleDetails[0]['participate_id'];
                      $AllVersionArticles=$articleProcessObj->getAllVersionDetails($participation_id) ;
                      if($AllVersionArticles!="NO" && is_array($AllVersionArticles))
                      {
                          foreach($AllVersionArticles as $key=>$varticle)
                          {
                            
                              $file_full_path=$this->articles_path.$varticle['article_path'];
                              $AllVersionArticles[$key]['file_size']=formatSizeUnits($file_full_path);
                              if(file_exists($file_full_path))
                                  $AllVersionArticles[$key]['file_exists']='yes';
                            
                          }


                          $this->_view->AllVersionArticles=$AllVersionArticles;
                      }
                    } 


                    $this->_view->missionDetails=$publishedArticle;
                    $this->_view->comment_type= $comment_type;
                    $this->_view->identifier=$article_id;
                    $this->_view->commentDetails=$commentDetails;

                    //echo "<pre>";print_r($publishedArticle);exit;
                   if($deliveryName)
                    $this->_view->meta_title="Project Management [$deliveryName]";

                    $this->render("Contrib_mission_published");
              }
              else
                $this->_redirect("/contrib/ongoing");

              
           }
           else
             $this->_redirect("/contrib/ongoing");
        }   
    }
    //Mission Corrector Deliver Action
    public function missionCorrectorDeliverAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
           $missionParams=$this->_request->getParams();
           $article_id=$missionParams['article_id'];
           
           if($article_id)
           {
              $contrib_identifier=$this->contrib_identifier;
              $participation=new Ep_Participation_CorrectorParticipation();
              
              $ongoingArticles=$participation->ongoingArticles($contrib_identifier,'popup',$article_id);
			  //echo "<pre>";print_r($ongoingArticles);exit;
              
              $cpcnt=0;

              foreach($ongoingArticles as $key=>$Article)
              {
                  if($Article['article_id']==$article_id)
                  {
                      $participationDetails[$cpcnt]=$Article;

                        $current_time=time();
                        $time_expires=$Article['corrector_submit_expires'];
                        $timestap_diff= $current_time - $time_expires;
                        $participationDetails[$cpcnt]['status']=$Article['status'];
                        $participationDetails[$cpcnt]['status_trans']='';
                        if($Article['writer_status']=='bid' || $Article['writer_status']=='disapproved' || !$Article['participate_id'])//newly added for simultaneous correction
                        {
                           $participationDetails[$cpcnt]['status']='writing_ongoing';
                           $participationDetails[$cpcnt]['status_trans']='R&eacute;daction en cours';
                        }
                        else if($Article['writer_status']!='under_study' ||($Article['writer_status']=='under_study' && $Article['writer_stage']=='stage0'))
                        {
                            $Article['status']='under_study';
                            $participationDetails[$cpcnt]['status']='under_study';
                        }
                        else if(($Article['status']=='bid' || $Article['status']=='disapproved') && $timestap_diff > 0 )
                        {
                            $participationDetails[$cpcnt]['status']='time_out';

                        }
                        //echo $participationDetails[$cpcnt]['status']."--".$participationDetails[$cpcnt]['status_trans'];
                        if(!$participationDetails[$cpcnt]['status_trans'])
                        {
                        $participationDetails[$cpcnt]['status_trans']=$this->getAOStatus($participationDetails[$cpcnt]['status']);
                        }

                      $cparticipation_id=$Article['participationId'];
                      $deliveryName=$Article['deliveryName'];
						/*ebookers stencils*/
						$files_pack=$Article['files_pack'];
						$stencils_ebooker=$Article['stencils_ebooker'];
						$sample_text_id=$Article['ebooker_sampletxt_id'];
						$token_ids=$Article['ebooker_tokenids'];
						$product_type=$Article['product'];
                      /* *** added on 25.02.216 *** */
                      // bnp Related fields for later use in function//

                      $bnp_mission=$Article['bnp_mission'];
                      $bnp_sample_text_id=$Article['bnp_sampletext_id'];
                      $bnp_city_id=$Article['bnp_city_id'];
                      $bnp_no_of_articles=$Article['bnp_no_of_articles'];
                  }
              }
                //echo "<pre>";print_r($ongoingArticles);exit;
               if(!count($participationDetails)>0)
                  $this->_redirect("/contrib/ongoing");   
                else
                  $participationDetails=$this->formatCorrectionDetials($participationDetails);

                
                             
                //ebooker update
				if($cparticipation_id && $files_pack && $stencils_ebooker=='yes')
				{
					$articleProcessObj=new EP_Article_ArticleProcess();
                    $versionArticleDetails= $participation->getParticipationDetails($cparticipation_id);  ///getting the paricipate id from participate atable/
                    $wparticipationId = $versionArticleDetails[0]['participate_id'];

                    $wparticipation=new Ep_Participation_Participation();
                    $wDetails= $wparticipation->getParticipationDetails($wparticipationId);
                    $writer_id=$wDetails[0]['user_id'];

                    $user_versions=array($writer_id,$contrib_identifier);
					$stencilsVersions=$articleProcessObj->getLatestEbookerCorrectorVersionDetails($wparticipationId,$user_versions);
					//echo "<pre>";print_r($stencilsVersions);exit;
					$stencilsDetails=array();
					if($stencilsVersions)
              {
						$article_doc_content=$stencilsVersions[0]['article_doc_content'];
						$stencils_text=explode("###$$$###",$article_doc_content);
						for ($s = 0; $s < $files_pack; $s++) {
								$stencilsDetails[$s]=$stencils_text[$s];
							}
					}
					else{
						for ($s = 0; $s < $files_pack; $s++) {
							$stencilsDetails[$s]='';
						}	
					}				
					/*correction validation popup as html*/
					$this->_view->correction_ebooker_validation_details=$correction_ebooker_details=$this->correctorEbookerArticle($article_id,$cparticipation_id);
					$this->_view->stencilsDetails=$stencilsDetails;
					
					//get sample texts and tokens
					$ebooker_obj=new Ep_Article_Ebooker();
					
					//getting translation stencils to display
					if($product_type=='translation')
					{
						$translateStencils=$ebooker_obj->getStencilstoTranslate($article_id);
						if($translateStencils)
							$this->_view->translateStencils=$translateStencils;
							
						//echo "<pre>";print_r($translateStencils);exit;
					}
					
					
					
					if($sample_text_id)
						$this->_view->sample_text=$sample_text=$ebooker_obj->getSampleText($sample_text_id);
					if($token_ids)					
						$tokens=$ebooker_obj->getTokens($token_ids);
					//echo "<Pre>";print_r($tokens);exit;
					$mandatory_tokens=array();
					$optional_tokens=array();
					if($tokens)
					{	
						$mandatory_tokens=$tokens[0];
						$optional_tokens=$tokens[1];
						$js_tokens_array = json_encode($mandatory_tokens);
					}
					else{
						$js_tokens_array = json_encode(array());
						$token_array=array();
					}

					//whitelist keywords$ebookerObj=new Ep_Article_Ebooker();
					$whiteListKeywords=$ebooker_obj->getWhiteListKeywords();
					if($whiteListKeywords)
					{
						$js_wl_keywords = json_encode($whiteListKeywords);
					}else{
						$js_wl_keywords =  json_encode(array());
					}
                    $mandatory_tokens=array();
                    $optional_tokens=array();
                    $js_wl_keywords =  json_encode(array());
                    $js_tokens_array = json_encode(array());
					
					$this->_view->js_tokens_array=$js_tokens_array;
					$this->_view->js_wl_keywords=$js_wl_keywords;
					$this->_view->mandatory_tokens=$mandatory_tokens;
					$this->_view->optional_tokens=$optional_tokens;
					$this->_view->product_type=$product_type;
					
					//echo "<pre>";print_r($tokens);exit;
				}
                elseif($cparticipation_id && $files_pack && $bnp_mission=='yes'){
                    $bnp_obj = new Ep_Bnp_Bnp();
                    $articleProcessObj=new EP_Article_ArticleProcess();
                    $versionArticleDetails= $participation->getParticipationDetails($cparticipation_id);  ///getting the paricipate id from participate atable/
                    $wparticipationId = $versionArticleDetails[0]['participate_id'];

                    $wparticipation=new Ep_Participation_Participation();
                    $wDetails= $wparticipation->getParticipationDetails($wparticipationId);
                    $writer_id=$wDetails[0]['user_id'];

                    $user_versions=array($writer_id,$contrib_identifier);
                    $Versions=$articleProcessObj->getLatestEbookerCorrectorVersionDetails($wparticipationId,$user_versions);
                    //echo "<pre>";print_r($stencilsVersions);exit;
                    $bnp_city_id = explode('|',$bnp_city_id);
                    $bnp_no_of_articles= explode('|',$bnp_no_of_articles);
                    $bnp_sample_text_id= explode('|',$bnp_sample_text_id);
                    $this->_view->bnp_sample_text_id=$bnp_sample_text_id;
                    $this->_view->bnp_city_id=$bnp_city_id;
                    $this->_view->bnp_no_of_articles=$bnp_no_of_articles;
                    $bnpDetails=array();
                    if($Versions)
                    {
                        $article_doc_content=$Versions[0]['article_doc_content'];
                        $bnp_text=explode("###$$$###",$article_doc_content);
                        $count_no_of_art = count($bnp_no_of_articles);
                        $s=0;
                        $int=0;
                        $end=0;
                        for($a=0;$a<$count_no_of_art;$a++){
                            $end+=$bnp_no_of_articles[$a];
                            for ($s=$int; $s < $end; $s++) {
                                $bnpDetails[$a][$s]=$bnp_text[$s];
                            }
                            $int+=$bnp_no_of_articles[$a];
                        }
                    }
                    else{
                        $count_no_of_art = count($bnp_no_of_articles);
                        $int=0;
                        $end=0;
                        for($a=0;$a<$count_no_of_art;$a++){
                            $end+=$bnp_no_of_articles[$a];
                            for ($s=$int; $s < $end; $s++) {
                                $bnpDetails[$a][$s]=$s;
                            }
                            $int+=$bnp_no_of_articles[$a];
                        }
                    }
                    $this->_view->textcount=count($bnpDetails,1)-count($bnpDetails);
                    /*correction validation popup as html*/
                    $this->_view->correction_ebooker_validation_details=$correction_ebooker_details=$this->correctorEbookerArticle($article_id,$cparticipation_id);
                    $this->_view->bnpDetails=$bnpDetails;

                    //get sample texts and tokens
                    $ebooker_obj=new Ep_Article_Ebooker();
                    $citys = (array) $bnp_obj->getCity();
                    $scitys = array();
                    foreach($citys as $city)
                    {
                        $scitys[$city['city_id']] = $city['city_name'];
                    }
                    $this->_view->scitys = $scitys;
                    $sample_texts = (array) $bnp_obj->getSampleTextIds();
                    $ssample_texts = array();
                    foreach($sample_texts as $sample_text)
                    {
                        $ssample_texts[$sample_text['sample_id']] =  "<h3>".$sample_text['title']."</h3><br/>".$sample_text['description'];
                    }
                    $this->_view->ssample_text=$ssample_texts;

                    //getting translation stencils to display
                    /*if($product_type=='translation')
                    {
                        $translateStencils=$ebooker_obj->getStencilstoTranslate($article_id);
                        if($translateStencils)
                            $this->_view->translateStencils=$translateStencils;

                        //echo "<pre>";print_r($translateStencils);exit;
                    }*/



//                    if($bnp_sample_text_id)
////                        $this->_view->sample_text=$sample_text=$bnp_obj->getSampleText($bnp_sample_text_id);
//                    if($token_ids)
//                        $tokens=$ebooker_obj->getTokens($token_ids);
//                    //echo "<Pre>";print_r($tokens);exit;
//                    $mandatory_tokens=array();
//                    $optional_tokens=array();
//                    if($tokens)
//                    {
//                        $mandatory_tokens=$tokens[0];
//                        $optional_tokens=$tokens[1];
//                        $js_tokens_array = json_encode($mandatory_tokens);
//                    }
//                    else{
//                        $js_tokens_array = json_encode(array());
//                        $token_array=array();
//                    }
//
//                    //whitelist keywords$ebookerObj=new Ep_Article_Ebooker();
//                    $whiteListKeywords=$ebooker_obj->getWhiteListKeywords();
//                    if($whiteListKeywords)
//                    {
//                        $js_wl_keywords = json_encode($whiteListKeywords);
//                    }else{
//                        $js_wl_keywords =  json_encode(array());
//                    }

                    $mandatory_tokens=array();
                    $optional_tokens=array();
                    $js_wl_keywords =  json_encode(array());
                    $js_tokens_array = json_encode(array());
                    $this->_view->js_tokens_array=$js_tokens_array;
                    $this->_view->js_wl_keywords=$js_wl_keywords;
                    $this->_view->mandatory_tokens=$mandatory_tokens;
                    $this->_view->optional_tokens=$optional_tokens;
                    $this->_view->product_type=$product_type;

                    //echo "<pre>";print_r($tokens);exit;
                }

                else if($cparticipation_id)/**getting All versions of Articles w.r.t User**/
                {
					$articleProcessObj=new EP_Article_ArticleProcess();
					$versionArticleDetails= $participation->getParticipationDetails($cparticipation_id);  ///getting the paricipate id from participate atable/
					$wparticipationId = $versionArticleDetails[0]['participate_id'];
					
					$wparticipation=new Ep_Participation_Participation();
					$wDetails= $wparticipation->getParticipationDetails($wparticipationId);
					$writer_id=$wDetails[0]['user_id'];
					
					$users=array($writer_id,$contrib_identifier);
					
					if($ongoingArticles[0]['missiontest']=='yes')
							$AllVersionArticles=$articleProcessObj->getAllVersionDetailsCorrector($wparticipationId,$users) ;
					else 
							$AllVersionArticles=$articleProcessObj->getAllVersionDetailsCorrector($wparticipationId) ;
					
					if($AllVersionArticles!="NO" && is_array($AllVersionArticles))
					{
						foreach($AllVersionArticles as $key=>$varticle)
						{
						  
							$file_full_path=$this->articles_path.$varticle['article_path'];
							
							$AllVersionArticles[$key]['file_size']=formatSizeUnits($file_full_path);
						  
						}


						$this->_view->AllVersionArticles=$AllVersionArticles;
					}
                                        
              }  
              
              

              //Comment Details
              $comment_type='correction';
              $comments_obj=new Ep_Comments_Adcomments();
              $commentDetails=$comments_obj->getAdComments($article_id,$comment_type);
              if(count($commentDetails)>0)
              {
                  $commentDetails=$this->formatCommentDetails($commentDetails);                  
              }    
              


				$this->_view->missionDetails=$participationDetails;
				$this->_view->CorrectorParticipationId=$cparticipation_id;
				$this->_view->comment_type= $comment_type;
				$this->_view->identifier=$article_id;
				$this->_view->commentDetails=$commentDetails;
              
				if($deliveryName)
                   $this->_view->meta_title="Project Management [$deliveryName]";


				if($stencils_ebooker=='yes')
					$this->render("Contrib_mission_corrector_deliver_ebooker");
                elseif($bnp_mission=='yes')
					$this->render("Contrib_mission_corrector_deliver_bnp");
				else				
					$this->render("Contrib_mission_corrector_deliver");
            } 
            else
              $this->_redirect("/contrib/ongoing");           
        } 
    }

    /**POP up to send a  corrector aarticle **/
    public function correctorArticlePopupAction()
    {
        $correctionParams=$this->_request->getParams();
        $contrib_identifier= $this->contrib_identifier;
        $cparticipation_obj=new Ep_Participation_CorrectorParticipation();
        $artProcess_obj = new Ep_Article_ArticleProcess();
        $article_obj = new Ep_Article_Article();

        $article_id=$correctionParams['article_id'];
        $details = $article_obj->getArticleAOdetails($article_id);
        $clientdetails = $article_obj->getClientArticleAOdetails($article_id);
        $cparticipation_id=$correctionParams['cparticipation_id'];

        if($article_id && $cparticipation_id && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
            $ongoingArticles=$cparticipation_obj->ongoingArticles($contrib_identifier,'popup',$article_id);
              
              $cpcnt=0;

              foreach($ongoingArticles as $key=>$Article)
              {
                  if($Article['article_id']==$article_id)
                  {
                      $participationDetails[$cpcnt]=$Article;

                      $cparticipation_id=$Article['participationId'];
                      $deliveryName=$Article['deliveryName'];
                  }
              }
               if(!count($participationDetails)>0)
                  $this->_redirect("/contrib/ongoing");   
                else
                  $participationDetails=$this->formatCorrectionDetials($participationDetails);

                
                             
              /**getting All versions of Articles w.r.t User**/
              if($cparticipation_id)
              {
                $articleProcessObj=new EP_Article_ArticleProcess();
                $versionArticleDetails= $cparticipation_obj->getParticipationDetails($cparticipation_id);  ///getting the paricipate id from participate atable/
                $wparticipationId = $versionArticleDetails[0]['participate_id'];
                
                
                $wparticipation=new Ep_Participation_Participation();
                $wDetails= $wparticipation->getParticipationDetails($wparticipationId);
                $writer_id=$wDetails[0]['user_id'];
                
                $users=array($writer_id,$contrib_identifier);


                if($ongoingArticles[0]['missiontest']=='yes')
                    $AllVersionArticles=$articleProcessObj->getAllVersionDetailsCorrector($wparticipationId,$users) ;
                else 
                    $AllVersionArticles=$articleProcessObj->getAllVersionDetailsCorrector($wparticipationId) ;

                
                if($AllVersionArticles!="NO" && is_array($AllVersionArticles))
                {
                    foreach($AllVersionArticles as $key=>$varticle)
                    {
                      
                        $file_full_path=$this->articles_path.$varticle['article_path'];
                        
                        $AllVersionArticles[$key]['file_size']=formatSizeUnits($file_full_path);
                        $AllVersionArticles[$key]['hotel_blwloutput']=$varticle['whitelist_newkeywords'];
                    }


                    $this->_view->AllVersionArticles=$AllVersionArticles;
                }             
            }
            ///////////get the prveious marks given//////////////////////////////////
            $recentversion= $artProcess_obj->getRecentVersion($wparticipationId);
            if($recentversion[0]['reasons_marks'] == '')
            {
                $rreasons = explode("|",$details[0]['refusalreasons']);
                foreach($rreasons as $keys)
                {
                    $res = $artProcess_obj->refuseValidTemplates($keys);
                    $refreason[$keys]  = $res[0]['title'];
              } 
                $this->_view->refreasons=$refreason;
                $this->_view->s1reasons=$refreason;
                $this->_view->s1markscount=0;
                $this->_view->previousdetails=$recentversion[0]['reasons_marks'];
                $this->_view->rreasonscount=count($refreason);
            }
            else
            {
                $rreasons = explode(",", $recentversion[0]['reasons_marks']);
                $s1reasons = array();
                for ($i = 0; $i < count($rreasons); $i++)
                {
                    $array = explode("|", $rreasons[$i]);
                    $res = $artProcess_obj->refuseValidTemplates(trim($array[0]));
                    $s1reasons[$array[0]] = $res[0]['title'];
                    //$s1reasons[$i] = $array[0];
                    $s1marks[$i] = $array[1];
                }
                $this->_view->refreasons=$s1reasons;
                $this->_view->s1reasons=$s1reasons;
                $this->_view->s1marks=$s1marks;
                $this->_view->s1markscount=array_sum($s1marks);
                $this->_view->previousdetails=$recentversion[0]['reasons_marks'];
                $this->_view->rreasonscount=count($rreasons);
            }

            /////////////////////////////////////////////
            //Get refuse and Definitive refuse Templates  

            $refuse_templates=$cparticipation_obj->getAllTemplates($details[0]['product']);
            //echo "<pre>";print_r($refuse_templates);
            $this->_view->refuseTemplates=$refuse_templates;


            $this->_view->missionDetails=$participationDetails;
            $this->_view->article_id=$article_id;
            $this->_view->clientId=$clientdetails[0]['user_id'];
            $this->_view->CorrectorParticipationId=$cparticipation_id;
            if($clientdetails[0]['user_id'] == '120218054512919'){
                $this->render("Contrib_hotels_correction_validation_popup"); }
            else
            $this->render("Contrib_correction_validation_popup");
        }
        else
          $this->_redirect("/contrib/ongoing");          
    }
	/**correction for ebooker stencils**/
    public function correctorEbookerArticle($article_id,$cparticipation_id)
    {   
        $contrib_identifier= $this->contrib_identifier;
        $cparticipation_obj=new Ep_Participation_CorrectorParticipation();
        $artProcess_obj = new Ep_Article_ArticleProcess();
        $article_obj = new Ep_Article_Article();
        $details = $article_obj->getArticleAOdetails($article_id);	
        $clientdetails = $article_obj->getClientArticleAOdetails($article_id);
        if($article_id && $cparticipation_id)
        {
            $ongoingArticles=$cparticipation_obj->ongoingArticles($contrib_identifier,'popup',$article_id);
            $cpcnt=0;
            foreach($ongoingArticles as $key=>$Article)
            {
                if($Article['article_id']==$article_id)
                {
                    $participationDetails[$cpcnt]=$Article;
                    $cparticipation_id=$Article['participationId'];
                    $deliveryName=$Article['deliveryName'];
                }
            }
            if(!count($participationDetails)>0)
                $this->_redirect("/contrib/ongoing");
            else
                $participationDetails=$this->formatCorrectionDetials($participationDetails);
            /**getting All versions of Articles w.r.t User**/
            if($cparticipation_id)
            {
                $articleProcessObj=new EP_Article_ArticleProcess();
                $versionArticleDetails= $cparticipation_obj->getParticipationDetails($cparticipation_id);  ///getting the paricipate id from participate atable/
                $wparticipationId = $versionArticleDetails[0]['participate_id'];
                $wparticipation=new Ep_Participation_Participation();
                $wDetails= $wparticipation->getParticipationDetails($wparticipationId);
                $writer_id=$wDetails[0]['user_id'];
                $users=array($writer_id,$contrib_identifier);
                if($ongoingArticles[0]['missiontest']=='yes')
                    $AllVersionArticles=$articleProcessObj->getAllVersionDetailsCorrector($wparticipationId,$users) ;
                else
                    $AllVersionArticles=$articleProcessObj->getAllVersionDetailsCorrector($wparticipationId) ;
                if($AllVersionArticles!="NO" && is_array($AllVersionArticles))
                {
                    foreach($AllVersionArticles as $key=>$varticle)
                    {
                        $file_full_path=$this->articles_path.$varticle['article_path'];
                        $AllVersionArticles[$key]['file_size']=formatSizeUnits($file_full_path);
                        $AllVersionArticles[$key]['hotel_blwloutput']=$varticle['whitelist_newkeywords'];
                    }
                    $this->_view->AllVersionArticles=$AllVersionArticles;
                }
            }
            ///////////get the prveious marks given//////////////////////////////////
            $recentversion= $artProcess_obj->getRecentVersion($wparticipationId);
            if($recentversion[0]['reasons_marks'] == '')
            {
                $rreasons = explode("|",$details[0]['refusalreasons']);
                foreach($rreasons as $keys)
                {
                    $res = $artProcess_obj->refuseValidTemplates($keys);
                    if($res!='NO')
		    	$refreason[$keys]  = $res[0]['title'];
		    else	
	               $refreason=$res;		
                }
                $this->_view->refreasons=$refreason;
                $this->_view->s1reasons=$refreason;
                $this->_view->s1markscount=0;
                $this->_view->previousdetails=$recentversion[0]['reasons_marks'];
                $this->_view->rreasonscount=count($refreason);
            }
            else
            {
                $rreasons = explode(",", $recentversion[0]['reasons_marks']);
                $s1reasons = array();
                for ($i = 0; $i < count($rreasons); $i++)
                {
                    $array = explode("|", $rreasons[$i]);
                    $res = $artProcess_obj->refuseValidTemplates(trim($array[0]));
                    if($res!='NO')
		    {
     	 		    $s1reasons[$array[0]] = $res[0]['title'];
                    //$s1reasons[$i] = $array[0];
                    $s1marks[$i] = $array[1];
		    }
		    else
		    	$s1reasons=$res;
                }
                $this->_view->refreasons=$s1reasons;
                $this->_view->s1reasons=$s1reasons;
                $this->_view->s1marks=$s1marks;
                $this->_view->s1markscount=array_sum($s1marks);
                $this->_view->previousdetails=$recentversion[0]['reasons_marks'];
                $this->_view->rreasonscount=count($rreasons);
            }
            /////////////////////////////////////////////
            //Get refuse and Definitive refuse Templates  
            $refuse_templates=$cparticipation_obj->getAllTemplates($details[0]['product']);
            //echo "<pre>";print_r($refuse_templates);
            $this->_view->refuseTemplates=$refuse_templates;
            $this->_view->missionDetailsCorrector=$participationDetails;
            $this->_view->article_id=$article_id;
            $this->_view->clientId=$clientdetails[0]['user_id'];
            $this->_view->CorrectorParticipationId=$cparticipation_id;
			//return $html=$this->_view->renderHtml("Contrib_correction_validation_popup_ebooker");
            //$this->render("Contrib_correction_validation_popup_ebooker");
        }        
    }

    public function getTemplateContentAction()
    {

        $templateParams=$this->_request->getParams();
        $template_id=$templateParams['template_id'];
        if($template_id)
        {
            $cparticipation_obj=new Ep_Participation_CorrectorParticipation();
            $templateDetails= $cparticipation_obj->getTemplates('refuse',$template_id);  
            if($templateDetails)
            {

              $template_content='<div class="template_content_'. $template_id.'">';
              $template_content.=stripslashes(utf8_encode($templateDetails[0]['content']));
              $template_content.='</div>';

              echo  $template_content;
            }
        }    
    }



     /**function to get the category name**/
    public function getCategoryName($category_value)
    {
        $category_name='';
        $categories=explode(",",$category_value);
        $categories_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        $cnt=0;
        foreach($categories as $category)
        {
            if($cnt==4)
                break;
            $category_name.=$categories_array[$category].", ";
            $cnt++;
        }
        $category_name=substr($category_name,0,-2);
        return $category_name;
    }
    /**function to get the Article type name**/
    public function getArticleTypeName($type_value)
    {
        $article_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_TYPE", $this->_lang);
        return $article_array[$type_value];
    }
    /**function to get the Article type name**/
    public function getCountryName($country_value)
    {
        $country_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);
        return $country_array[$country_value];
    }
    public function getAllArticleTypes()
    {
        $article_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_TYPE", $this->_lang);
        return $article_array;
    }
    /**function to get the language type name**/
    public function getLanguageName($lang_value)
    {
        $language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        return $language_array[$lang_value];
    }
    /**function to get the  AO Status type name**/
    public function getAOStatus($status_value)
    {
        $status_array=$this->_arrayDb->loadArrayv2("EP_AO_STATUS", $this->_lang);
        return $status_array[$status_value];
    }
    /** function get days between current date and end date*/
    public function getDaysDiff($deliveryDate)
    {
        $differ_string='';
        $startDate=strtotime(date('Y-m-d H:i:s'));
        $deliveryDate=$deliveryDate." 23:59:59";
        
        $endDate=strtotime(date('Y-m-d H:i:s',strtotime($deliveryDate)));
        
        $difference=$endDate-$startDate;
        $daysDiff=floor($difference/(60*60*24));
        $hoursDiff =floor(($difference / (60*60))%24);
        $minutesDiff =floor(($difference % (60*60))/60);
        if($daysDiff!=0)
            $differ_string.=$daysDiff." jours,<br>";
        if($hoursDiff!=0)
            $differ_string.=$hoursDiff." h, ";
        if($minutesDiff!=0)
            $differ_string.=$minutesDiff." mns";
        return $differ_string;
        
    }

    //Calculate and Update Profile Percentage
    public function calculateProfilePercentage()
    {
          //setlocale(LC_TIME, "fr_FR");
          $contrib_identifier=$this->contrib_identifier;

          $profilePercentage=0;

          
            $profileplus_obj = new Ep_Contrib_ProfilePlus();
            $profileContrib_obj = new Ep_Contrib_ProfileContributor();
            $profile_identifier_info=$profileplus_obj->checkProfileExist($contrib_identifier);
            if($profile_identifier_info!='NO')
            {
               $profile_identifier=$profile_identifier_info[0]['user_id'];
               $profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
               $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);
         
                /**Mon identité**/
                if($profileinfo[0]['initial'] && $profileinfo[0]['first_name'] && $profileinfo[0]['last_name'])
                    $profilePercentage+=15; 

                //Ma date de naissance  
                if($profile_contribinfo[0]['dob'])           
                    $profilePercentage+=5;

                //Photo du profil  
                $app_path=APP_PATH_ROOT;
                $profilePicture=$this->_config->path->contrib_profile_pic_path.$contrib_identifier.'/'.$contrib_identifier."_p.jpg";
                $pic_exists= file_exists($app_path.$profilePicture);
                if($pic_exists)
                {
                  $profilePercentage+=10;
                }

                //texte de présentation
                if(strip_tags($profile_contribinfo[0]['self_details']))
                    $profilePercentage+=10;


                //Langues & niveau de maitrise
                if($profile_contribinfo[0]['language'])
                     $profilePercentage+=15;

                
                //Compétences & niveau de maitrise
                if($profile_contribinfo[0]['category_more'])
                {
                    $categories_more=unserialize($profile_contribinfo[0]['category_more']);   
                    if(count($categories_more)>0)
                        $profilePercentage+=15;
                }   

                //Expériences professionnelles
                $experience_obj=new Ep_Contrib_Experience();
                $jobDetails=$experience_obj->getExperienceDetails($this->contrib_identifier,'job');
                if($jobDetails!="NO")
                {
                  $profilePercentage+=15;
                }

                //Formation
                $educationDetails=$experience_obj->getExperienceDetails($this->contrib_identifier,'education');
                if($educationDetails!="NO")
                    $profilePercentage+=10;


                //Informations personnelles
                if($profileinfo[0]['address'] || $profileinfo[0]['city'] || $profileinfo[0]['country'] || $profile_contribinfo[0]['nationality'])
                    $profilePercentage+=2;  

                //Informations de facturation       
                //if($profile_contribinfo[0]['pay_info_type'])
                if($profile_contribinfo[0]['entreprise'])
                    $profilePercentage+=2;

                //Choix de rémunération
                if($profile_contribinfo[0]['payment_type'])
                    $profilePercentage+=1;
                    
                if($profilePercentage > 100)
                   $profilePercentage=100;                        

                return $profilePercentage;
              
          }
          else
             return 0;              
      
    }
     /**UTF8 DECODE function work for msword character also**/
    public function utf8dec($s_String)
    {
       $s_String=str_replace("e&#769;","&eacute;",$s_String);
     $s_String=str_replace("E&#769;","&Eacute;",$s_String);
        $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
        return substr($s_String, 0, strlen($s_String)-1);
    }
   
    //Check corrector spec files exists
    function checkCorrectorBrief($article_id)
    {
        $delivery=new Ep_Article_Delivery();
        $partcipation=new Ep_Participation_CorrectorParticipation();
        if($article_id)
        {
            $articleIdentifier=$article_id;
            $articleDetails=$delivery->getCorrectorArticleBrief($articleIdentifier);
            $client_spec_full_path=SPEC_FILE_PATH. $articleDetails[0]['filepath'];
            $corrector_spec_full_path=CORRECTOR_SPEC_FILE_PATH. $articleDetails[0]['correction_file'];
            $article_path=$partcipation->getArticlePath($articleIdentifier);
            $article_path=$this->articles_path.$article_path;
            
            // OR (file_exists($article_path) && !is_dir($article_path))
            if((file_exists($client_spec_full_path) && $articleDetails[0]['filepath']!=NULL && !is_dir($corrector_spec_full_path))
                OR (file_exists($corrector_spec_full_path) && $articleDetails[0]['correction_file']!=NULL && !is_dir($corrector_spec_full_path))
            )
            {
               return "yes";
            }
            else
              return "no";

        }
        else
              return "no";
    }       
    
   /**Send Corrector Articles of contributor to Ep team**/
     public function sendCorrectorArticleAction()
    {
        //error_reporting(E_ERROR | E_PARSE);
        error_reporting(E_ALL);
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
    
        if($this->_helper->EpCustom->checksession())
        {            
            $action_function=$corrector_params['function'];
            //$marks=$corrector_params['entity_score'];
            $marks=$corrector_params['marksvald'];
            $marksreasons = $corrector_params["marksvaldwithreason"];
            $client_id = $corrector_params['client_id'];
            $comments=$corrector_params['corrector-comment'];
            if($action_function=='approve')
            {
                $articles=$_FILES;
                foreach($articles as $participationkey=>$article)
                {
                    if($article['name']!='')
                    {
                      //echo "juuu".
                        if($corrector_params['cparticipation_id'] == '')
                        $participationId=str_replace("file_",'',$participationkey);
                        else
                            $participationId = $corrector_params['cparticipation_id'];
                        $file=pathinfo($article['name']);
                        //print_r($file);
                        
                        $extension=$file['extension'];
                        $corrector_participationDetails=$corrector_participation->getParticipationDetails($participationId);
                        $articleDir=$this->articles_path.$corrector_participationDetails[0]['article_id']."/";
                        if(!is_dir($articleDir))
                            mkdir($articleDir,TRUE);
                        chmod($articleDir,0777);
                        $articleName=$corrector_participationDetails[0]['article_id']."_".$corrector_participationDetails[0]['corrector_id']."_".mt_rand(10000,99999).".".$extension;
                        $article_path=$articleDir.$articleName;
                        if (move_uploaded_file($article['tmp_name'], $article_path))
                        {
                            chmod($article_path,0777);
                            $participationUpdate['updated_at']=date('Y-m-d H:i:s');
                            // $participationUpdate['article_send_at']=date('Y-m-d h:i:s');
                            $delivery=new Ep_Article_Delivery();
                            $deliveryDetails = $delivery_obj->getDeliveryDetails($corrector_participationDetails[0]['article_id']);
                           // print_r($corrector_params); exit;
                            //hotel development //
                            if($client_id == "120218054512919" && !isset($corrector_params['outputfilename']))  //hotel.com dev
                            {
                                $checkblwl = $this->checkHotelDevLists($articleDir, $articleName, $userIdentifier, $article['name'], $participationId, $corrector_params['article_id'], $client_id, $crtpartcipate_id);
                                if ($checkblwl == 'pass') {
                                    // $autoEmails->messageToEPMail($userIdentifier, 155, $parameters = NULL);
                                }
                                elseif ($checkblwl == 'filetypeerror') {
                                    echo json_encode(array('status'=>'blwlerrormessage', 'result'=>null));
                                    exit;
                                }elseif ($checkblwl == 'docfileerror') {
                                    if($extension == 'zip' || $extension == 'rar'){
                                        echo json_encode(array('status'=>'docerrormessage', 'result'=>'multi'));
                                    }else{
                                        echo json_encode(array('status'=>'docerrormessage', 'result'=>'single'));
                                    }
                                    exit;
                                }
                                else {
                                    echo json_encode(array('status'=>'blwlerror', 'result'=>rawurlencode($checkblwl)));
                                    exit;
                                }
                                    exit;
                            }

                            $missionTest=$deliveryDetails[0]['missiontest'];

                            if($missionTest=='yes')
                            {
                              $participationUpdate['status']='under_study';
                              $participationUpdate['current_stage']='mission_test'; 
                            }
                            else
                            {
                              $participationUpdate['status']='under_study';
                              $participationUpdate['current_stage']='stage2';
                            }  

                            //Antiword obj to get content from uploaded article
                            $antiword_obj=new Ep_Antiword_Antiword($article_path);
                            $article_doc_content=$antiword_obj->getContent();
                            $article_words_count=$antiword_obj->count_words($article_doc_content);
                            
                            $corrector_participation->updateParticipationDetails($participationUpdate,$participationId);

                            /***get contributor Participation **/
                            $contrib_participation_details=$participation->getContribParticipation($corrector_participationDetails[0]['article_id']);
                            $contrib_participation_id= $contrib_participation_details[0]['id'];
                            $contrib_participationUpdate['updated_at']=date('Y-m-d H:i:s');
                            if($missionTest=='yes')
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
                            $participation->updateParticipationDetails($contrib_participationUpdate,$contrib_participation_id);
                            
                            /**get latest version*/
                            $ArticleProcess=new EP_Article_ArticleProcess();
                            $latest_version=$ArticleProcess->getLatestVersion($contrib_participation_id) ;
                            /**Insert into Article Process Table**/
                            $ArticleProcess->participate_id=$contrib_participation_id;
                            $ArticleProcess->user_id=$corrector_participationDetails[0]['corrector_id'];
                            $ArticleProcess->article_path=$corrector_participationDetails[0]['article_id']."/".$articleName;
                            $ArticleProcess->version=$latest_version[0]['latestVersion'];
                            $ArticleProcess->article_doc_content=utf8dec(nl2br($article_doc_content));
                            $ArticleProcess->article_words_count=$article_words_count;

                            /*if($missionTest=='yes')
                              $ArticleProcess->stage='mission_test';
                            else  
                              $ArticleProcess->stage='s2';*/

                            $ArticleProcess->stage='corrector';

                            $ArticleProcess->article_name=str_replace(" ",'_',$this->utf8dec(trim($article['name'])));
                            $ArticleProcess->comments=$this->utf8dec(nl2br($comments));
                            $ArticleProcess->marks=$marks;
                            $ArticleProcess->reasons_marks=$corrector_params["marksvaldwithreason"] ;
                            
                            $ArticleProcess->insert();
                            /**sending mail to AO created User**/                            
                            $ao_created_user= $deliveryDetails[0]['created_user'];
                            $parameters_valid['article_title']=$deliveryDetails[0]['articleName'];
                            $parameters_valid['AO_title']=$deliveryDetails[0]['deliveryTitle'];
                            //$bo_user_Details=$autoEmails->getUserDetails($ao_created_user);
                            //$contrib_user_Details=$autoEmails->getUserDetails($participatedetails[0]['user_id']);
                            //$corrector_user_Details=$autoEmails->getUserDetails($userIdentifier);
                            //$parameters_valid['bo_user']=$bo_user_Details[0]['username'];
                            //$parameters_valid['contributor_name']=$contrib_user_Details[0]['username'];
                            //$parameters_valid['corrector_name']=$corrector_user_Details[0]['username'];
                            $ticket_obj=new Ep_Ticket_Ticket();
                            $parameters_valid['bo_user']= $ticket_obj->getUsername($ao_created_user,true);
                            $parameters_valid['contributor_name']=$ticket_obj->getUsername($participatedetails[0]['user_id']);
                            $parameters_valid['corrector_name']=$ticket_obj->getUsername($userIdentifier);
                            $autoEmails->messageToEPMail($ao_created_user,65,$parameters_valid,true);
                        }
                        $this->_helper->FlashMessenger('Article sent');
                    }
                    else
                        $this->_helper->FlashMessenger('please upload a article');

                    //action sentence
                    $contributor_identifier=$participatedetails[0]['user_id'];
                    $action_sentence_id=39;
                }
            }
            /////when dissaporved by corrector//////////
            else if($action_function=='disapprove')
            {

                $deliveryDetails = $delivery_obj->getDeliveryDetails($corrector_params['article_id']);
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
					$parameters['article_link']="http://ep-test.edit-place.co.uk/contrib/ongoing";
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
					$parameters['article_link']="http://ep-test.edit-place.co.uk/contrib/ongoing";
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
                elseif($action_function=='disapprove')
                {
                   $reasons=implode(",",$corrector_params['refuse_template']);
                   $type='temporaire';
                }


                $reason_obj->participate_id=$partcipate_id;
                $reason_obj->refused_by=$userIdentifier;
                $reason_obj->contributor=$contributor_identifier;
                $reason_obj->stage='corrector';
                $reason_obj->reasons=$reasons;
                $reason_obj->edited_content=$this->utf8dec($comments);
                $reason_obj->type=$type;
                $reason_obj->created_at=date("Y-m-d H:i:s");

                $reason_obj->insert();
            }

//echo "333";exit;

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
              $mail_params['comment_bo_link']='/followup/delivery?client_id='.$deliveryDetails[0]['user_id'].'&ao_id='.$deliveryDetails[0]['id'].'&submenuId=ML3-SL10';

              $autoEmails->messageToEPMail($bo_user,102,$mail_params,TRUE);
            }  
  
            
            $this->_redirect("/contrib/ongoing");
       }
    }
    /// finding the proposed words in document and if not found submit the file
    public function sendcorrectorarticleHotelsDevAction()
    {
        $participation=new Ep_Participation_Participation();
        $corrector_participation=new Ep_Participation_CorrectorParticipation();
        $article_obj=new Ep_Article_Article();
        $delivery_obj=new Ep_Article_Delivery();
        $autoEmails=new Ep_Ticket_AutoEmails();
        $userIdentifier=$this->contrib_identifier;
        $missionParams=$this->_request->getParams();
        // print_r($missionParams); exit;
        // print_r($missionParams['newkeyword']);exit;
        $marks=$missionParams['marksvald'];
        $marksreasons=$missionParams['marksvaldwithreason'];
        $comments=$missionParams['corrector-comment'];
        $client_id = $missionParams['client_id'];
        $article_id = $missionParams['article_id'];
        $crtparticipationId = $missionParams['cparticipation_id'];
        $article_path = $missionParams['articlepath'];
        $articleName = $missionParams['articleName'];
        $article_file_name = $missionParams['uploadfilename'];
        $hotelsblwlcheckoutput = $missionParams['outputfilename'];

        $participatedetails= $corrector_participation->getParticipationDetails($crtparticipationId);  ///getting the paricipate id from participate atable/
        $crtpartcipate_id = $crtparticipationId;
        $partcipate_id = $participatedetails[0]['participate_id'];
        $corrector_participationDetails=$corrector_participation->getParticipationDetails($crtparticipationId); //print_r($corrector_participationDetails); exit;
        $deliveryDetails = $delivery_obj->getDeliveryDetails($article_id);
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

        //Antiword obj to get content from uploaded article
        $antiword_obj=new Ep_Antiword_Antiword($article_path);
        $article_doc_content=$antiword_obj->getContent();
        $article_words_count=$antiword_obj->count_words($article_doc_content);

        $corrector_participation->updateParticipationDetails($participationUpdate,$crtparticipationId);
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

        //echo "<pre>";print_r($contrib_participationUpdate);exit;

        $participation->updateParticipationDetails($contrib_participationUpdate,$contrib_participation_id);
        //echo "in ffgfg"; echo $corrector_participationDetails[0]['corrector_id']; exit;
        /**get latest version*/
        $ArticleProcess=new EP_Article_ArticleProcess();
        $latest_version=$ArticleProcess->getLatestVersion($contrib_participation_id) ;
        /**Insert into Article Process Table**/
        $ArticleProcess->participate_id=$contrib_participation_id;
        $ArticleProcess->user_id=$corrector_participationDetails[0]['corrector_id'];
        $ArticleProcess->article_path=$article_id."/".$articleName;
        $ArticleProcess->version=$latest_version[0]['latestVersion'];
        $ArticleProcess->article_doc_content=utf8dec(nl2br($article_doc_content));
        $ArticleProcess->article_words_count=$article_words_count;

        /*if($missionTest=='yes')
          $ArticleProcess->stage='mission_test';
        else
          $ArticleProcess->stage='s2';*/

        $ArticleProcess->stage='corrector';

        $ArticleProcess->article_name=str_replace(" ",'_',$this->utf8dec(trim($article_file_name)));
        $ArticleProcess->comments=$this->utf8dec(nl2br($comments));
        $ArticleProcess->marks=$marks;
        $ArticleProcess->reasons_marks=$marksreasons;
        $ArticleProcess->whitelist_newkeywords=$missionParams["outputfilename"] ;

        $ArticleProcess->insert();

        /**sending mail to AO created User**/
        $ao_created_user= $deliveryDetails[0]['created_user'];
        $parameters_valid['article_title']=$deliveryDetails[0]['articleName'];
        $parameters_valid['AO_title']=$deliveryDetails[0]['deliveryTitle'];
        //$bo_user_Details=$autoEmails->getUserDetails($ao_created_user);
        //$contrib_user_Details=$autoEmails->getUserDetails($participatedetails[0]['user_id']);
        //$corrector_user_Details=$autoEmails->getUserDetails($userIdentifier);
        //$parameters_valid['bo_user']=$bo_user_Details[0]['username'];
        //$parameters_valid['contributor_name']=$contrib_user_Details[0]['username'];
        //$parameters_valid['corrector_name']=$corrector_user_Details[0]['username'];
        $ticket_obj=new Ep_Ticket_Ticket();
        $parameters_valid['bo_user']= $ticket_obj->getUsername($ao_created_user,true);
        $parameters_valid['contributor_name']=$ticket_obj->getUsername($participatedetails[0]['user_id']);
        $parameters_valid['corrector_name']=$ticket_obj->getUsername($userIdentifier);
        $autoEmails->messageToEPMail($ao_created_user,65,$parameters_valid,true);

        $this->_helper->FlashMessenger('Article envoy&eacute;');

        //action sentence
        $contributor_identifier=$participatedetails[0]['user_id'];
        $action_sentence_id=39;
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
            $history_array['article_id']=$article_id;
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
            $mail_params['comment_bo_link']='/followup/delivery?client_id='.$deliveryDetails[0]['user_id'].'&ao_id='.$deliveryDetails[0]['id'].'&submenuId=ML3-SL10';

            $autoEmails->messageToEPMail($bo_user,102,$mail_params,TRUE);
        }
        $this->_redirect("/contrib/ongoing");
    }
    public function getconfirmboxAction()
    { 
      ////getting the confirm box alert ///
      $correctorconfirm_params=$this->_request->getParams();
      $crtparticipation=new Ep_Participation_CorrectorParticipation();
      $delivery_obj = new Ep_Article_Delivery();
        if($correctorconfirm_params['button'] == "disapprove")
        { 
          $userprofile = $crtparticipation->getContributorDetails($correctorconfirm_params['crtpartiId']);
          $deldetails = $delivery_obj->getDeliveryDetails($correctorconfirm_params['artId']);
          
          if($userprofile[0]['profile_type'] == "senior")
            $confirmtime = $deldetails[0]['sc_resubmission'];
          else if($userprofile[0]['profile_type'] == "junior")
            $confirmtime = $deldetails[0]['jc_resubmission'];
          else
            $confirmtime = $deldetails[0]['jc0_resubmission']; 
			
    			if($confirmtime>=60) 
    				$confirmtime=($confirmtime/60)." heure(s)";
    			else
    				$confirmtime=$confirmtime." minutes";

    			echo "Je suis disponible dans maximum ".$confirmtime." pour corriger l'article du corrig&eacute; par le r&eacute;dacteur";
    			exit;
              
        }
        if($correctorconfirm_params['button'] == "closed")
        {
            $userprofile = $crtparticipation->getContributorDetails($correctorconfirm_params['crtpartiId']);
            $deldetails = $delivery_obj->getDeliveryDetails($correctorconfirm_params['artId']);
            /*if($deldetails[0]['senior_time'] > $deldetails[0]['junior_time'])
              $confirmtime = $deldetails[0]['senior_time']+$deldetails[0]['participation_time'];
            else
              $confirmtime = $deldetails[0]['junior_time']+$deldetails[0]['participation_time'];*/
              if($userprofile[0]['profile_type'] == "senior")
                $confirmtime = $deldetails[0]['senior_time']+$deldetails[0]['participation_time'];
              else if($userprofile[0]['profile_type'] == "junior")
                $confirmtime = $deldetails[0]['junior_time']+$deldetails[0]['participation_time'];
              else
                $confirmtime = $deldetails[0]['subjunior_time']+$deldetails[0]['participation_time'];
			
      			if($confirmtime>=60) 
      				$confirmtime=round($confirmtime/60)." heure(s)";
      			else
      				$confirmtime=$confirmtime." minutes"; 


      			echo "Je suis disponible dans maximum ".$confirmtime." pour corriger l'article du prochain contributeur  s&eacute;lectionn&eacute;";
      			exit;			
        }
    }
    public function sendCorrectionAOCreationMail($parameters,$corrector_list)
    {
        $contributor_obj=new EP_Contrib_Registration();
        $contributors=$contributor_obj->getAOCorrectors($corrector_list);
        if($contributors!="NO" && count($contributors)>0)
        {
              foreach($contributors as $corrector){
                  $autoEmails=new Ep_Ticket_AutoEmails();
                  /*if($corrector['profile_type2']=='senior')
                      $parameters['submit_hours']= $parameters['correction_sc_submission'];
                  else
                      $parameters['submit_hours']= $parameters['correction_jc_submission'];
                  if($parameters['submit_hours']>=60) 
                           $parameters['submit_hours']=($parameters['submit_hours']/60)." heure(s)";
                      else
                           $parameters['submit_hours']=$parameters['submit_hours']." mns";*/                    

                  
                  $parameters['submit_hours']= "<b>".date("Y-m-d",$parameters['participation_expires'])." &agrave; ".date("H:i",$parameters['participation_expires'])."</b>";
                   
                  if($parameters['missiontest']=='yes')
                    $email_id=104;
                  else
                    $email_id=21;

                  if($corrector['identifier']!=$this->contrib_identifier)
                    $autoEmails->messageToEPMail($corrector['identifier'],$email_id,$parameters);
              }
        }
    }
    public function askMoreTimeAction()
    {
      if($this->_helper->EpCustom->checksession() && ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest' || $this->_request-> isPost()) )
      {
          $time_params=$this->_request->getParams();

          $article_id=$time_params['article_id'];
          $sendto_user=$time_params['sendto_user'];
          $comments=$time_params['extend_time_comments'];
          $ao_type=$time_params['ao_type'];

          $ao_obj=new Ep_Article_Delivery();
          $ticket_obj=new Ep_Ticket_Ticket();
          $auto_mail=new Ep_Ticket_AutoEmails();

          if($this->_request-> isPost() && $article_id && $sendto_user && $comments)  
          {
              $aoDetails=$ao_obj->getDeliveryDetails($article_id);
              $sender=$this->contrib_identifier;
              $receiverId=$sendto_user;
              $object=$this->utf8dec('Deadline extension request for the project <b>'.$aoTitle.'</b>');
              $message=$this->utf8dec($comments);
              $message=stripslashes($message);
              

              //$auto_mail->sendMailEpMailBox($receiverId,$object,$message,$sender);              

             // if($aoDetails[0]['mail_send']=='yes')
              //{

                if($aoDetails[0]['premium_option']!='0' && $aoDetails[0]['premium_option']!='' )
                {
                    $parameters['ongoing_bolink']='/followup/delivery?submenuId=ML3-SL10&client_id='.$aoDetails[0]['user_id'].'&ao_id='.$aoDetails[0]['id'];
                     $parameters['article_title']='<a href="/followup/delivery?submenuId=ML3-SL10&client_id='.$aoDetails[0]['user_id'].'&ao_id='.$aoDetails[0]['id'].'">'.$aoDetails[0]['articleName'].'</a>';
                }    
                else
                {
                    $parameters['ongoinglink']='/client/order1?id='.$article_id;
                    $parameters['article_title']='<a href="/client/order1?id='.$article_id.'">'.$aoDetails[0]['articleName'].'</a>';
                }    

                
                 $parameters['contributor_name']=$ticket_obj->getUserName($this->contrib_identifier);                 
                 $parameters['comments']='<b><u>Commentaire  du r&eacute;dacteur </u></b> <br>'.$message;
                 $parameters['sender_id']=$this->contrib_identifier;
                 $auto_mail->messageToEPMail($sendto_user,45,$parameters);
              //}
              
            
               $this->_redirect("/contrib/mission-deliver?article_id=$article_id");
          }

          else if($article_id && $ao_type)
          {
              

              $aoDetails=$ao_obj->getDeliveryDetails($article_id);

              if($aoDetails!="NO" && is_array($aoDetails))
              {
                if($ao_type=='premium' OR $ao_type=='nopremium') 
                {
                  if($aoDetails[0]['premium_option']!='0' && $aoDetails[0]['premium_option']!='' )
                    $type='premium';
                  else
                    $type='nopremium';
                }  
                if($type=='premium')
                   $sendto_user=$aoDetails[0]['created_user'];//'111201092609847';
                else if($type=='nopremium')
                   $sendto_user= $aoDetails[0]['user_id']; 

                 $client_name=$ticket_obj->getUserName($aoDetails[0]['user_id']);


                $this->_view->article_id= $article_id;
                $this->_view->aoTitle= $aoDetails[0]['title'];
                $this->_view->sendto_user= $sendto_user;
                $this->_view->client_name= $client_name;
              }              

             $this->render("Contrib_askmoretime");  
          }
          else
              $this->_redirect("/contrib/ongoing");            
      }  
    }
    public function addcontribAction()
    {
  //      print_r($this->_request->getParams());
//exit;		
		if($this->_request-> isPost())
        {
            $contrib_params=$this->_request->getParams();
            $contrib_obj = new EP_Contrib_Registration();
            $user_obj=new Ep_User_User();
            $auto_email=new Ep_Ticket_AutoEmails();
            $contrib_obj->email=strip_tags($contrib_params['email']);
            $contrib_obj->password=$contrib_params['password'];
            $contrib_obj->status='Active';
            $contrib_obj->type='contributor';
            $contrib_obj->verification_code=md5("edit-place_".$contrib_obj->email);
            $contrib_obj->verified_status='YES';
            $contrib_obj->profile_type='sub-junior';
           
            $res= $user_obj->checkClientMailid($contrib_params['email']);
            if($res && $contrib_params['email'] && $contrib_params['password'])
            {
              try
              {
                    $contrib_obj->insert();
					$contrib_indentifier=$contrib_obj->getIdentifier();
					$contrib_obj->addContributor($contrib_indentifier,$contrib_params['language']);

                      $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
                      $contrib_indentifier=$contrib_obj->getIdentifier();
                      $this->EP_Contrib_reg->clientidentifier =$contrib_indentifier;
                      $this->EP_Contrib_reg->clientemail =$contrib_params['email'];

                      //SENDING MAIL TO EP MAIL BOX
                      $auto_email->messageToEPMail($contrib_indentifier,2,'');
                      $this->_redirect("/contrib/modify-profile");
                     //$this->_redirect("/contrib/onprocess");
                

                  exit;
              }
              catch(Zend_Exception $e)
              {
                  $this->_view->error_msg =$e->getMessage()." D&eacute;sol&eacute;! Mise en erreur.";
                  exit;
              }
            }
            else  
              $this->_redirect("/index");
        }
        else {
            $this->_redirect("/contrib/index");
        }
    }
    /*terms and conditions to add new contributor***/
    public function termsAction()
    {
		$dom = new DOMDocument;
		$dom->load(APP_PATH_ROOT."/cgu/terms.xml");
		
		$titles = $dom->getElementsByTagName('title');
		foreach ($titles as $title) 
			$this->_view->termtitle=$title->nodeValue;
		
		$contents = $dom->getElementsByTagName('content');
		foreach ($contents as $content) 
			$this->_view->termcontent=$content->nodeValue; 
        
		$this->render("Contrib_terms");
    }

    //get unread message count calling from ajax to get favicon
    public function getUnreadMessageCountAction()
    {
        if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
          /**Unread message count in inbox**/
            $ticket=new Ep_Ticket_Ticket();
            echo $ticket->getUnreadCount('contributor',$this->contrib_identifier); 
        }    
    }
    //get unread message count calling from ajax to get favicon
    public function getAllFavAoCountAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
          $this->_forward('aosearch',null, null, array('favicon' => 'ao_details'));
        } 
        
    }

  //Added w.r.t profile payment validation when opening invoice details pagein royalties**/
  public function checkPaymentInfoAction()
  {
    
    if($this->_helper->EpCustom->checksession())
    {
      /***Profile Info***/
      $profileplus_obj = new Ep_Contrib_ProfilePlus();
      $profileContrib_obj = new Ep_Contrib_ProfileContributor();
      $contrib_identifier= $this->contrib_identifier;
      if($profileplus_obj->checkProfileExist($contrib_identifier)!='NO')
      {
        $profile_identifier_info=$profileplus_obj->checkProfileExist($contrib_identifier);
        $profile_identifier=$profile_identifier_info[0]['user_id'];
        $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);
        $contribinfo=$profile_contribinfo[0];
        $profileplus_contribinfo=$profileplus_obj->getProfileInfo($profile_identifier);

        
        
       // if($contribinfo['pay_info_type'] && (($contribinfo['pay_info_type']=='out_uk') OR ($contribinfo['pay_info_type']=='freelance')))
        if($contribinfo['pay_info_type'] && $contribinfo['entreprise'])
        {
            
            $rib_id=str_replace("|","",$contribinfo['rib_id']);
            if($contribinfo['payment_type'] && (($contribinfo['paypal_id'] OR $rib_id)))
              echo "YES";
            else
              echo "NO";
        }
        else
        {
          echo "NO";
        }


      }
      else
      {
        echo "NO";
      }
    }    
  }  

  //function to get upcoming AO & Polls
  public function upcomingDeliveries($articleParams)
  {
      $searchParameters['profile_type']=$this->profileType; 
      $searchParameters['black_status']=$this->black_status;
      $searchParameters['upcoming']=true;
      $searchParameters['cnt_nextday']=$articleParams['cnt_nextday'];
      
      $searchParameters['uorderByTitle']=$articleParams['uorderByTitle'];
      $searchParameters['uorderByLang']=$articleParams['uorderByLang'];      
      $searchParameters['uorderBytime']=$articleParams['uorderBytime'];  

      $article=new Ep_Article_Article();
      //$articleDetails=$article->getArticleDetails($searchParameters);
      $articleDetails=$article->getArticleSearchDetails($searchParameters);

      if(count($articleDetails)>0)
          $articleDetails=$this->formatArticleDetails($articleDetails);
      else  
           $articleDetails=array();

      /***********getting POll AO Details**************/
                $pollDetails=$this->pollAoSearch($searchParameters);
                $pollCount=count($pollDetails); 
                if($pollCount==0)  
                    $pollDetails=array();
                if(count($pollCount)>0)
                {
                  $cnt=0;
                  foreach($pollDetails as $count_poll)
                  {
                    $pollDetails[$cnt]['timestamp']=$count_poll['publish_time'];
                    $filter_category.=$count_poll['category_key'].",";                    
                    $filter_language.=$count_poll['language'].",";
                    $cnt++;
                  }
                }                   
          
        /**correction AO's**/
        $correctionAoDetails=$this->correctionAoSearch($searchParameters,NULL,true);
        $correctionCount=count($correctionAoDetails);
        if($correctionCount==0)
            $correctionAoDetails=array();
        //Private icons
        $co=0;
        foreach($correctionAoDetails as $corr_offer)
        {
            if($correctionAoDetails[$co]['correction_type']=='private')
            {
                $private_icon=$this->private_icon;
                $writers_count=count(explode(",",$correctionAoDetails[$co]['corrector_privatelist']));
                if($writers_count>1)
                    $toolTitle=$this->ptoolTitleMulti;
                else
                    $toolTitle=$this->ptoolTitle;
                $toolTitle=str_replace('X',$writers_count,$toolTitle);
                $private_icon=str_replace('$toolTitle',$toolTitle,$private_icon);
                $correctionAoDetails[$co]['picon']=$private_icon;
            }
            else
                $correctionAoDetails[$co]['picon']='';
            $co++;
        }
        if($pollCount>0 || count($articleDetails)>0 || $correctionCount>0)
      {
            $articleDetails=array_merge($articleDetails,$pollDetails,$correctionAoDetails);
      }
      if($searchParameters['uorderByTitle']=='asc')
        usort($articleDetails, 'sortByTitleASC');
      elseif($searchParameters['uorderByTitle']=='desc')
        usort($articleDetails, 'sortByTitleDESC');
      elseif($searchParameters['uorderBytime']=='desc')
        usort($articleDetails, 'sortByTimestampDESC');        
      else
        usort($articleDetails, 'sortByTimestamp');              

      

       return  $articleDetails;
  }

  //Ao Alert subscription
  public function aoAlertSubscribeAction()
  {
      if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
      {    
          $alert_obj=new Ep_Article_DeliveryAlert();
           
          $subscribe_params=$this->_request->getParams();          

          $contrib_identifier=$this->contrib_identifier;
          $deliveryId=$subscribe_params['deliveryId'];
          $subscribe=$subscribe_params['subscribe'];
          $mission_type=$subscribe_params['mission_type'];

            if($mission_type=='article-correction')
            {
                $mission_type='article';
                $type='correction';
            }
            else
            {
                $type='writing';
            }
            $checkAlertExist=$alert_obj->checkDeliveryAlert($deliveryId,$contrib_identifier,$type);
          if($checkAlertExist=="NO")
          {
              $alert_array['user_id']=$contrib_identifier;
              $alert_array['delivery_id']=$deliveryId;
              $alert_array['mission_type']=$mission_type;
                $alert_array['type']=$type;
              $alert_array['alert']=$subscribe;
              $alert_array['created_at']=date("Y-m-d H:i:s");

              $alert_obj->insertDeliveryAlert($alert_array); 

          }
          else
          {
              $data['alert']=$subscribe;
              $where=" user_id='".$contrib_identifier."' AND delivery_id='".$deliveryId."'";
              $alert_obj->updateDeliveryAlertDetails($data,$where);
          }



      }  
  }

   //function to get finished AO & Polls
  public function finishedDeliveries($articleParams)
  {
      $searchParameters['profile_type']=$this->profileType; 
      $searchParameters['finished']=true;
      
      
      $searchParameters['torderByTitle']=$articleParams['torderByTitle'];
      $searchParameters['torderByLang']=$articleParams['torderByLang'];      
      $searchParameters['torderByQuotePrice']=$articleParams['torderByQuotePrice']; 

      $article=new Ep_Participation_Participation();
      $articleDetails=$article->finishedArticles($searchParameters);

      if(count($articleDetails)>0)
          $articleDetails=$this->formatArticleDetails($articleDetails);
      else  
           $articleDetails=array();

      /***********getting POll AO Details**************/
                /*$pollDetails=$this->pollAoSearch($searchParameters);
                $pollCount=count($pollDetails); 
                if($pollCount==0)  
                    $pollDetails=array();
                if(count($pollCount)>0)
                {
                  $cnt=0;
                  foreach($pollDetails as $count_poll)
                  {
                    $pollDetails[$cnt]['timestamp']=$count_poll['publish_time'];
                    $filter_category.=$count_poll['category_key'].",";                    
                    $filter_language.=$count_poll['language'].",";
                    $cnt++;
                  }
                }                   
                
      if($pollCount>0 || count($articleDetails)>0)
      {
          $articleDetails=array_merge($articleDetails,$pollDetails);          
      }
      */
      if($searchParameters['torderByTitle']=='asc')
        usort($articleDetails, 'sortByTitleASC');
      elseif($searchParameters['torderByTitle']=='desc')
        usort($articleDetails, 'sortByTitleDESC');
      elseif($searchParameters['torderByQuotePrice']=='desc')
         usort($articleDetails,'sortByPriceDESC'); 
      else if($searchParameters['torderByQuotePrice']=='asc')
         usort($articleDetails,'sortByPriceASC');

       return  $articleDetails;
  }

  //function to save poll question responses
  public function savePollResponseAction()
  {
      if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
      {    
              
          $responseParams=$this->_request->getParams(); 
          
          $poll_id=$responseParams['poll_id'];
          $user_id=$this->contrib_identifier;
          
          unset($this->EP_Contrib_Cart->poll_response[$poll_id]);

            foreach($responseParams as $key=>$value)
            {
                $pollResponseObj=new Ep_Poll_UserResponse();

                $responseArray=array();
                $responseArray['poll_id']=$poll_id;
                $responseArray['user_id']=$user_id;
                $responseArray['created_at']=date("Y-m-d H:i:s");

                if (preg_match("/question_(\d+)/", $key, $match)) {

                    $responseArray['question_id']=$match[1];

                    if($responseParams["timing_".$match[1]]=='hour')
                      $responseArray['response']=$responseParams[$key]*60;
                    else if($responseParams["timing_".$match[1]]=='day')
                      $responseArray['response']=$responseParams[$key]*60*24;
                    else
                      $responseArray['response']=$responseParams[$key];

                    if(is_array($responseArray['response']) && count($responseArray['response'])>0)
                      $responseArray['response']=implode("|",$responseArray['response']);
                }
                else if (preg_match("/^price_user_(\d+)/", $key, $match)) {

                   $responseArray['question_id']=$match[1];
                   $responseArray['response']=$responseParams[$key];

                   $this->EP_Contrib_Cart->poll_price[$poll_id]=$responseArray['response'];
                }
                else if (preg_match("/^bulk_price_user_(\d+)/", $key, $match)) {

                   $responseArray['question_id']=$match[1];
                   $responseArray['response']=$responseParams[$key];
                }
                else if (preg_match("/^date_(\d+)/", $key, $match)) {

                   $responseArray['question_id']=$match[1];
                   $responseArray['response']=date("Y-m-d",strtotime(str_replace("/","-",$responseParams[$key])));
                   
                }

                if($responseArray['question_id']!='' && $responseArray['response']!='')
                {

                    //check already response recorded for this question or not
                    $question_exist=$pollResponseObj->checkQuestionResponse($user_id,$poll_id,$responseArray['question_id']);

                    if($question_exist) //update poll response
                    {
                        unset($responseArray['created_at']);
                        $responseArray['updated_at']=date("Y-m-d H:i:s");
                        $query=" user_id='".$user_id."' and question_id='".$responseArray['question_id']."' and poll_id='".$poll_id."'";
                        
                        //$pollResponseObj->updatePollResponse($responseArray,$query);
                        
                        
                    }
                    else //insert response
                    {    
                         $this->EP_Contrib_Cart->poll_response[$poll_id][$responseArray['question_id']]=$responseArray;
                        //$pollResponseObj->InsertPollResponse($responseArray);
                    }    
                }  
               
                unset($responseArray);
                
            }

            
                   
          echo json_encode(array("status"=>"success"));
      }    
  }
	 /////////converting minuter to houres
    public function minutesToHours($mins)
    {
        if ($mins < 0) {
            $min = Abs($mins);
        } else {
            $min = $mins;
        }
        $H = Floor($min / 60);
        $M = ($min - ($H * 60)) / 100;
        $hours = $H +  $M;
        if ($mins < 0) {
            $hours = $hours * (-1);
        }
        $expl = explode(".", $hours);
        $H = $expl[0];
        if (empty($expl[1])) {
            //$expl[1] = 00;
        }
        $M = $expl[1];
        if (strlen($M) < 2 && $M) {
            $M = $M . 0;
        }
        if($M)
            $hours = $H . ":" . $M;
        else
            $hours = $H;
        return $hours;
    }
	
	public function WriterParticipationExpire($artId)
    {
        ////update the artlcle table with partcipation time/////////
        $delivery_obj = new Ep_Article_Delivery();
        $article_obj = new Ep_Article_Article();
        $deliveryDetails = $delivery_obj->getArtDeliveryDetails($artId);
        $expires=time()+(60*$deliveryDetails[0]['participation_time']);
        $data = array("participation_expires"=>$expires, "bo_closed_status"=>NULL, "send_to_fo"=>"yes");////////updating
        $query = "id= '".$artId."'";
        $article_obj->updateArticle($data,$query);
    }
	
	/****   republish mails ****/
    public function sendMailToContribs($art_id)
    {
        $delivery_obj = new Ep_Article_Delivery();
        $user_obj = new Ep_User_User();
        $automail=new Ep_Ticket_AutoEmails();
        $participate_obj = new EP_Participation_Participation();
        $partcrt_obj = new Ep_Participation_CorrectorParticipation();
        $ao_id = $delivery_obj->getDeliveryID($art_id);
        $aoDetails=$delivery_obj->getPrAoDetails($ao_id);
        $getpartusers = $participate_obj->getParticipationsUserIds($art_id);
        if($getpartusers != 'NO')
        {
            foreach($getpartusers as $notsendmail)
            {
                $nomailsendlist[] = $notsendmail['user_id'];
            }
        }
        $getpartcrts = $partcrt_obj->getNotRefusedCrtParticipationsUserIds($art_id);
        if($getpartcrts != 'NO')
        {
            foreach($getpartcrts as $notsendmailcrts)
            {
                $nomailsendlist[] = $notsendmailcrts['corrector_id'];
            }
        }
        /* Sending mail to client when publish **/
        $delartdetails = $delivery_obj->getArticlesOfDel($ao_id);
        $expires=time()+(60*$delartdetails[0]['participation_time']);
        $aoDetails=$delivery_obj->getPrAoDetailsWithArtid($art_id);
        
        $parameters['AO_title']=$aoDetails[0]['artname'];
        //$parameters['submitdate_bo']=$aoDetails[0]['submitdate_bo'];
        //$parameters['submitdate_bo']=date('d/m/Y H:i', $expires);
        $parameters['submitdate_bo']=strftime("%d/%m/%Y &agrave; %H:%M",$expires);
		$parameters['client_name']=$aoDetails[0]['company_name'];
		
		/*$parameters['AO_end_date']=$aoDetails[0]['delivery_date'];
        
        $parameters['noofarts']=$aoDetails[0]['noofarts'];
        if($aoDetails[0]['deli_anonymous']=='0')
            $parameters['article_link']="/contrib/aosearch?client_contact=".$aoDetails[0]['user_id'];
        else
            $parameters['article_link']="/contrib/aosearch?client_contact=anonymous";
        $parameters['aoname_link'] = "/contrib/aosearch";
        $parameters['clientartname_link'] = "/client/quotes?id=".$aoDetails[0]['articleid'];*/
        $object = $aoDetails[0]['republish_object'];
        $message = ($aoDetails[0]['republish_mail']);
        if($aoDetails[0]['mail_send_contrib']=='yes')
        {
            //Priority contributors mail
            /*if($aoDetails[0]['priority_contributors']!="")
            {
                $prior_contribs=explode(",",$aoDetails[0]['priority_contributors']);
                $prior_parameters['poll_link']='<a href="/contrib/aosearch">Cliquant-ici</a>';
                $prior_parameters['hours']=$aoDetails[0]['priority_hours'];
                foreach($prior_contribs as $pcontrib)
                {
                    $contrib_poll=$delivery_obj->getPollcontribDetails($aoDetails[0]['poll_id'],$pcontrib);
                    $prior_parameters['poll']=$contrib_poll[0]['title'];
                    $prior_parameters['date']=$contrib_poll[0]['poll_date'];
                    $prior_parameters['price']=$contrib_poll[0]['price_user'];
                      if(!in_array($pcontrib,$nomailsendlist)) ///sending to only non participants
                            $automail->messageToEPMail($pcontrib,15,$prior_parameters);///
                            $automail->sendMailEpMailBox($pcontrib,$object,$message);
                }
            }*/
			
            //Only for poll not linked AOs
            if($aoDetails[0]['poll_id']=="")
            {
                if($aoDetails[0]['AOtype']=='private')
                {
                    $contributors=array_unique(explode(",",$aoDetails[0]['article_contribs']));
                    if(is_array($contributors) && count($contributors)>0)
                    {
						if($aoDetails[0]['premium_option']=='0')
							$automailid=88;
						else
						{
							if(count($contributors)==1)
								$automailid=128;	
							else
								$automailid=87;	
						}	
						   foreach($contributors as $contributor)
							{
								if(!in_array($contributor,$nomailsendlist)) ///sending to only non participants
									$automail->messageToEPMail($contributor,$automailid,$parameters);
							}
                    }
                }
                elseif($aoDetails[0]['AOtype']=='public')
                {
                    if($aoDetails[0]['created_by'] != 'BO')
                    {
                        $contributors=$user_obj->getSeniorContributors();
                        if(is_array($contributors) && count($contributors)>0)
                        {
                            $sclimit=$this->config['sc_limit'];
							
							if($aoDetails[0]['premium_option']=='0')
								$automailid=86;
							else
								$automailid=85;
								
						   foreach($contributors as $contributor)
                            {
                                $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);
                                if($sclimit > $countofparts[0]['partscount'])
                                {
                                    if(!in_array($contributor['identifier'],$nomailsendlist)) ///sending to only non participants
                                       $automail->messageToEPMail($contributor['identifier'],$automailid,$parameters);
                                }
                            }
                        }
                    }
                    else
                    {
                        $delviews = $delivery_obj->getDeliveryDetails($art_id);
                        $profiles = explode(",", $delviews[0]['view_to']);

                        $profiles = implode(",", $profiles);
                        $contributors=$delivery_obj->getContributorsAO('public',$aoDetails[0]['fav_category'],$profiles);
						//print_r($contributors);exit; 
                        if(is_array($contributors) && count($contributors)>0)
                        {
                            $jclimit=$this->config['jc_limit'];
                            $sclimit=$this->config['sc_limit'];

                            if($aoDetails[0]['premium_option']=='0')
								$automailid=86;
							else
								$automailid=85;
								
							foreach($contributors as $contributor)
                            {
                                if($contributor['profile_type'] == 'junior' || $contributor['profile_type'] == 'sub-junior')
                                {
                                    $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);
                                    if($jclimit > $countofparts[0]['partscount'])
                                    {
                                        if(!in_array($contributor['identifier'],$nomailsendlist)) ///sending to only non participants
                                            $automail->messageToEPMail($contributor['identifier'],$automailid,$parameters);
                                    }
                                }
                                else
                                {
                                    $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);

                                    if($sclimit > $countofparts[0]['partscount'])
                                    {
                                        if(!in_array($contributor['identifier'],$nomailsendlist)) ///sending to only non participants
                                             $automail->messageToEPMail($contributor['identifier'],$automailid,$parameters);
                                    }
                                }
                            }
							
                        }
                    }
                }
            }
        }
    }
}