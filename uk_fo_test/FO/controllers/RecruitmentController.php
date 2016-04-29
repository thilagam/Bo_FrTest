<?php
/**
 * EditPlaceController
 * This controller is responsible for Recruitment Operations*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
 require_once('ContribController.php');
Class RecruitmentController Extends ContribController
{
    public function init()
	{
		parent::init();

	 $this->producttype_array=array(
    							"article_de_blog"=>"Blog article",
								"descriptif_produit"=>"Product description",
								"article_seo"=>"SEO article",
								"guide"=>"Guide",
								"news"=>"News",
								"autre"=>"Others"
        						);
		$this->product_array=array(
    							"redaction"=>"Writing",
								"translation"=>"Translation",
								"autre"=>"Other",
								"proofreading"=>"Correction"
        						);
		$this->time_array=array(
								"min"=>"Min(s)",
								"hour"=>"Hour(s)",
								"day"=>"Day(s)"
								);
		$this->duration_array=array(
							"days"=>"Days",
							"week"=>"Week",
							"month"=>"Month",
							"year"=>"Year"
						);
		$this->recruitmentTestArticles=APP_PATH_ROOT.$this->_config->path->recruitmentTestArticles;
		

		$this->EP_Contrib_reg->profileType=$this->profileType;
	}

	public function participationChallengeAction()
	{

		if($this->_helper->EpCustom->checksession())
        {
			$recruitObj=new Ep_Recruitment_Participation();
			
			$recruitmentParams=$this->_request->getParams();

			$recruitmentIdentifier=$recruitmentParams['recruitment_id'];
            $article_id=$recruitmentParams['article_id'];

			//Check particiaption_expires //added by Kavitha
			$ParticipationNotExpired=$recruitObj->CheckParticipationExpired($recruitmentIdentifier);
			if($ParticipationNotExpired[0]['artcount']<=0 || $ParticipationNotExpired[0]['stoprecruitment']=='stop')
			{
				 $this->_helper->FlashMessenger('<b>Sorry but recruitment of this mission is completed. We invite you to consult the other missions available on the platform. See you soon!</b>');
				 $this->_view->participationexpired='yes';
				//$this->_redirect("/contrib/home");	
			}
			
			if($this->_helper->FlashMessenger->getMessages()) {
                $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }
			
			if($recruitmentIdentifier)
			{
				

				$recruitmentDetails=$recruitObj->getRecruitmentDetails($recruitmentIdentifier,$article_id);

				//echo "<pre>";print_r($recruitmentDetails);exit;

				if(count($recruitmentDetails)>0)
				{
					$recruitmentDetails=$this->formatRecruitmentDetais($recruitmentDetails);

					$participation_id=$recruitmentDetails[0]['recruitmentParticipationId'];
                    /**getting All versions of Articles w.r.t User**/
                    if($participation_id)
                    {
                        if($recruitmentDetails[0]['contract_signed']=='' || $recruitmentDetails[0]['contract_signed']=='no')
                        {
                            $contract=$this->getRecruitmentContract($recruitmentDetails[0]['article_id'],$participation_id,$recruitmentDetails);                           

                             $recruitmentDetails[0]['contract']= $contract;
                        }     
                    }


                    $this->_view->recruitmentDetails=$recruitmentDetails;
					//echo "<pre>";print_r($recruitmentDetails);exit;
					$this->render("recruitment-participation-challenge");
				}	
				else
					$this->_redirect("/contrib/home");	

				
				
			}
			else
				$this->_redirect("/contrib/home");
		}	
	}

    //get recruitment contract w.r.t article
    function getRecruitmentContract($recruitment_article_id,$participation_id,$recruitmentDetails)
    {
        $profileplus_obj = new Ep_Contrib_ProfilePlus();
        $profileContrib_obj = new Ep_Contrib_ProfileContributor();

        $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
        $profile_contribinfo=$profileContrib_obj->getProfileInfo($contrib_identifier);
        $profileinfo=$profileplus_obj->getProfileInfo($contrib_identifier);


        if($recruitment_article_id && $participation_id)
        {
            $article_obj=new Ep_Article_Article();
            
            $Articles_selected=$article_obj->getArticleContract($recruitment_article_id);


            $participationObj=new Ep_Recruitment_Participation();
            $participationDetails=$participationObj->getParticipationDetails($participation_id);
            //echo "<pre>";print_r($participationDetails);exit;
            //$Articles_selected=explode("$$$###$$$",$Articles_selected);

            $Articles='<ol style="list-style:none;font-weight:bold;">';
			$ArticlesWithAO='<ol style="list-style:none;font-weight:bold;">';
			$ArticlesWithPrice='';
			foreach($Articles_selected as $Article)
			{
				$Articles.='<li>
								   <div style="float:left;width:100%">
									<div style="width:1%;float:left">- </div>
									<div style="width:98%;float:right">'.$Article['title'].'</div>
									</div>
							</li>';

				$ArticlesWithAO.='<li>
								   <div style="float:left;width:100%">
									<div style="width:1%;float:left">- </div>
                                   <div style="width:98%;float:right">'.$Article['title'].'<span style=""> ( '.$Article['deliveryTitle'].' )</span></div>
									</div>
							</li>';


                 $ArticlesWithPrice.='<p>Versement d&rsquo;un forfait fixe de <span style="font-weight:bold"> '.$participationDetails[0]['price_user'].' Euros</span> comme convenu entre le  R&eacute;dacteur et le Client au titre du Contenu <b>'.$Article['title'].'</b></p>';
			 
				//Pound and euro amount calculations
				if($Article['currency']=='euro')
				{					
					$euro_amount+=$participationDetails[0]['price_user'];
						
				}	
				else
				{	
					$pound_amount+=$participationDetails[0]['price_user'];
				}		

			}
			$Articles.='</ol>';
			$ArticlesWithAO.='</ol>';
            
            /**Name and Address Details*/
			$this->_view->LastName=$profileinfo[0]['last_name'];
			$this->_view->FirstName=$profileinfo[0]['first_name'];
            /* $this->_view->dob=date("d/m/Y",strtotime($profile_contribinfo[0]['dob']));
			$this->_view->address=$profileinfo[0]['address'].', '.$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city'];
			$this->_view->pays=$this->getCountryName($profileinfo[0]['country']);
			$this->_view->siren_number= $profile_contribinfo[0]['siren_number'];
            $this->_view->tva_number= $profile_contribinfo[0]['tva_number']; */
			$text = "";
			if($profile_contribinfo[0]['options_flag']=="com_check")
			{
				$text = "SOCIETE <br>";
				$text .= $profile_contribinfo[0]['com_name']?"D&eacute;nomination sociale : ".$profile_contribinfo[0]['com_name']."<br>":"";
				$text .= $profile_contribinfo[0]['com_address']?"Adresse : ".$profile_contribinfo[0]['com_address']."<br>":"";
				$text .= $profile_contribinfo[0]['com_city']?"Ville : ".$profile_contribinfo[0]['com_city']."<br>":"";
				$text .= $profile_contribinfo[0]['com_zipcode']?"Code postal : ".$profile_contribinfo[0]['com_zipcode']."<br>":"";
				$text .= $profile_contribinfo[0]['com_country']?"Pays : ". $this->getCountryName($profile_contribinfo[0]['com_country'])."<br>": "";
				$text .= $profile_contribinfo[0]['com_siren']?"Siren : ".$profile_contribinfo[0]['com_siren']."<br>":"";
				$text .= $profile_contribinfo[0]['com_tva_number']?"Num&eacute;ro de TVA Intracommunautaire : ".$profile_contribinfo[0]['com_tva_number']."<br>":"";
				$text .= $profile_contribinfo[0]['com_phone']?"T&eacute;l&eacute;phone : ".$profile_contribinfo[0]['com_phone']."<br>":"";
			}
			elseif($profile_contribinfo[0]['options_flag']=="reg_check")
			{
				$text = $profile_contribinfo[0]['dob']?"Date  de naissance : ".date("d/m/Y",strtotime($profile_contribinfo[0]['dob']))."<br>":"";
				$text .= "Adresse : ".$profileinfo[0]['address'].', '.$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city']."<br>";
				$text .= $this->getCountryName($profileinfo[0]['country'])?"Pays : ".$this->getCountryName($profileinfo[0]['country'])."<br>":"";
					$text .= "Num&eacute;ro de passeport : ".$profile_contribinfo[0]['passport_no']."<br>";
				$text .= $profile_contribinfo[0]['id_card']?"Carte d'identit&eacute; : ".$profile_contribinfo[0]['id_card']."<br>":"";
			}
			else
			{
				$text = "Date  de naissance : ".date("d/m/Y",strtotime($profile_contribinfo[0]['dob']))."<br>";
				$text .= "Adresse : ".$profileinfo[0]['address'].', '.$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city']."<br>";
				$text .= $profileinfo[0]['country']?"Pays : ".$this->getCountryName($profileinfo[0]['country'])."<br>":"";
				$text .= $profile_contribinfo[0]['tva_number']?"Num&eacute;ro de TVA Intracommunautaire : ".$profile_contribinfo[0]['tva_number']."<br>":"";
			}
			$this->_view->user_info = $text;
			/**Articles Details**/
			$this->_view->ArticlesDetails=$Articles;
			$this->_view->ArticlesDetailsWithAO=$ArticlesWithAO;
			$this->_view->ArticlesDetailsWithPrice=$ArticlesWithPrice;
			$this->_view->startDate=date("d/m/Y");
			//echo "<pre>";print_r($recruitmentDetails);exit;
			$this->_view->recruit=$recruitmentDetails[0];
			
			$this->_view->LastName=$profileinfo[0]['last_name'];
			$this->_view->FirstName=$profileinfo[0]['first_name'];
			$this->_view->dob=date("d/m/Y",strtotime($profile_contribinfo[0]['dob']));
			$this->_view->address=$profileinfo[0]['address'].', '.$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city'];
			$this->_view->pays=$this->getCountryName($profileinfo[0]['country']);
			
			$this->_view->euro_amount=$euro_amount;
			$this->_view->pound_amount=$pound_amount;
			
			$this->_view->month=date("M");
			$this->_view->day=date("d");
            $this->_view->startDate=date("d/m/Y");
            $this->_view->participation_id=$participation_id;


            $contract = $this->_view->renderHtml('recruitment-contract');

        } 
        return  $contract;

    }

    //save contract
    function signContractAction()
    {
        if($this->_helper->EpCustom->checksession() && $this->_request-> isPost())
        {
            $contractParams=$this->_request->getParams();
            $participation_id=$contractParams['participation_id'];
            $recruitmentIdentifier=$contractParams['recruitment_id'];

            $participationObj=new Ep_Recruitment_Participation();
            $participationDetails=$participationObj->getParticipationDetails($participation_id);
            //echo "<pre>";print_r($contractParams);exit;


            if($participation_id && $participationDetails && $recruitmentIdentifier)
            {               
                $article_id=$participationDetails[0]['article_id'];

                //updating contract text
                $watchlist_id=$participationDetails[0]['watchlist_id'];
                if($watchlist_id)
                {
                    $watchList=new Ep_Participation_Watchlist();
                    $contract_text=utf8dec(stripslashes($contractParams['contract_text']));    
                    $update_watchlist['contract_text']=$contract_text;
                    $watchList->updateWatchlist($update_watchlist,$watchlist_id);    
                }
                
                //updating participation
                $contract=$contractParams['contract'];
                $contract1=$contractParams['contract'];

                if($contract=='yes')
                {
                    $contract_signed='yes';
                    $update_participation['status']='bid';                    
                }    
                else
                {
                    $contract_signed='no';
                    $update_participation['status']='bid_pending';
                }                
                $update_participation['contract_signed']=$contract_signed;


                //update article submit time
                $recruitmentDetails=$participationObj->getRecruitmentDetails($recruitmentIdentifier);

                if($this->profileType=='senior')
                {
                    if($recruitmentDetails[0]['senior_time'])
                        $submit_time=$recruitmentDetails[0]['senior_time'];
                    else
                        $submit_time=$this->config['sc_time'];
                }
                else if($this->profileType=='junior')
                {
                    if($recruitmentDetails[0]['junior_time'])
                        $submit_time=$recruitmentDetails[0]['junior_time'];
                    else
                        $submit_time=$this->config['jc_time'];

                }
                else if($this->profileType=='sub-junior')
                {
                    if($recruitmentDetails[0]['subjunior_time'])
                        $submit_time=$recruitmentDetails[0]['subjunior_time'];
                    else
                        $submit_time=$this->config['jco_time'];

                }                
                $upload_time= $submit_time*60;
                $article_submit_expires=time()+$upload_time;
                

                if($contract=='yes')
                    $update_participation['article_submit_expires']=$article_submit_expires;

                
                //echo "<pre>";print_r($update_participation);exit;

                $participationObj->updateParticipation($update_participation,$participation_id);


                //redirect to article submission page
                if($contract_signed=='yes')
                    $this->_redirect("/contrib/mission-deliver?article_id=$article_id");
                else
                    $this->_redirect("/contrib/aosearch");
                
            }
            else
                $this->_redirect("/contrib/aosearch");            
        }
        else
            $this->_redirect("/contrib/aosearch");
    }

	//format Recruitment Details
    public function formatRecruitmentDetais($recruitmentDetails)
    {
    	
        if(count($recruitmentDetails)>0)
    	{
    		$ticket_obj= new Ep_Ticket_Ticket();

    		foreach($recruitmentDetails as $key=>$recruitment)
    		{
    			$recruitmentDetails[$key]['client_pic']= $this->getClientPicPath($recruitment['client_id']);
	           	$recruitmentDetails[$key]['category']= $this->getCategoryName($recruitment['category']);
	           	$recruitmentDetails[$key]['product_type_name']= $this->producttype_array[$recruitment['type']];
	           	$recruitmentDetails[$key]['language_source_name']= $this->getLanguageName($recruitment['language']);
                $recruitmentDetails[$key]['product_name']=$recruitment['product']=='redaction' ? 'Write' : 'Translate';
				
				$recruitmentDetails[$key]['type_name']=$recruitment['product']=='redaction' ? 'Writers' : 'Translators';
                
				$recruitmentDetails[$key]['client_name']=str_ireplace("_new",'',$ticket_obj->getUserName($recruitment['client_id']));	           	

	           	$recruitmentDetails[$key]['bo_user_pic']= $this->getPicPath($recruitment['created_user'],'bo_user');
                $recruitmentDetails[$key]['bo_user']=$ticket_obj->getUserName($recruitment['created_user'],TRUE);

                if($recruitment['article_submit_expires'])
                    $recruitmentDetails[$key]['recruit_expires']=$recruitment['article_submit_expires'];
                else
                    $recruitmentDetails[$key]['recruit_expires']=$recruitment['participation_expires'];
				//time_ago
				$recruitmentDetails[$key]['time_ago']=time_ago($recruitment['recruitment_created_at']);

                //test article submit time
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

                if($recruitment['submit_option']=='day')
                    $recruitmentDetails[$key]['article_submit_time_text']=($submit_time/60*24)." ".$this->time_array[$recruitment['submit_option']];
                else if($recruitment['submit_option']=='hour')
                    $recruitmentDetails[$key]['article_submit_time_text']=($submit_time/60)." ".$this->time_array[$recruitment['submit_option']];                    
                if($recruitment['submit_option']=='min')
                    $recruitmentDetails[$key]['article_submit_time_text']=($submit_time)." ".$this->time_array[$recruitment['submit_option']];                                    


                
                $remaining_time=$submit_time*60;  

                $recruitmentDetails[$key]['remaining_time']=secondsToTime($remaining_time);

                if($recruitment['delivery_period']=='days')
					$recruitmentDetails[$key]['total_weeks']=(($recruitment['delivery_time_frame']/7));
                    else if($recruitment['delivery_period']=='month')
                        $recruitmentDetails[$key]['total_weeks']=round(($recruitment['delivery_time_frame']*30)/4);
                    else
                        $recruitmentDetails[$key]['total_weeks']=round($recruitment['delivery_time_frame']);

				if(!$recruitmentDetails[$key]['total_weeks'])
					$recruitmentDetails[$key]['total_weeks']=1;

				$recruitmentDetails[$key]['day_fr'] = $this->duration_array[$recruitment['delivery_period']];

                $recruitmentDetails[$key]['turnover']=($recruitment['price_max']*$recruitment['mission_volume']);
				$recruitmentDetails[$key]['delivery_period_text']=$this->duration_array[$recruitment['delivery_period']];
				//($recruitment['price_max']*$recruitmentDetails[$key]['total_weeks']*$recruitment['max_articles_per_contrib']);

    		}

            //getting quiz details            
            if($recruitment['link_quiz']=='yes' && $recruitment['quiz_id'] && $recruitment['recruitmentParticipationId'])
            {
                $quiz_obj=new Ep_Quiz_Participation();
                $quizParticipationDetails=$quiz_obj->getQuizParticipationDetails($recruitment['quiz_id'],$this->EP_Contrib_reg->clientidentifier,$recruitment['article_id']);
                //echo "<pre>";print_r($quizParticipationDetails);exit;
                if($quizParticipationDetails)
                {
                    $recruitmentDetails[$key]['quiz_partcipation_id']=$quizParticipationDetails[0]['id'];
                    $recruitmentDetails[$key]['qualified']=$quizParticipationDetails[0]['qualified'];
                }

                

            }
            //echo "<pre>";print_r($recruitmentDetails);exit;

    		return $recruitmentDetails;

    	}

    }	

    //save Recruitment participation challenge
    public function saveParticipationAction()
    {
		if($this->_helper->EpCustom->checksession() && $this->_request-> isPost())
		{
			$participationParams=$this->_request->getParams();

            //echo $this->EP_Contrib_reg->quiz_qualified;
            //echo "<pre>";print_r($participationParams);exit;

			$recruitmentIdentifier=$participationParams['recruitment_id'];
			$articles_per_week=intval($participationParams['articles_per_week']);
			$proposed_cost=currencyToDecimal($participationParams['proposed_cost']);


			if($recruitmentIdentifier)
			{
				$quizParticipationId=$this->EP_Contrib_reg->quizParticipationId;


                $recruitment_article_id=$this->EP_Contrib_reg->recruitment_article_id;
                if(!$recruitment_article_id)
                {
                    //get article id that was not assigned to a user
                    $recruitObj=new Ep_Recruitment_Participation();
                    $getArticleDetails= $recruitObj->getNotAssignedArticle($recruitmentIdentifier);
                    //echo "<pre>";print_r($getArticleDetails);exit;
                    if($getArticleDetails)
                    {
                        $recruitment_article_id=$getArticleDetails[0]['id'];
                    }
                }
				$recruitObj=new Ep_Recruitment_Participation();
				$participationExists=$recruitObj->checkParticipationExist($this->EP_Contrib_reg->clientidentifier,$recruitmentIdentifier);
				//echo $participationExists;exit;
                
				if($participationExists=='NO')
				{

                $watchList=new Ep_Participation_Watchlist();
                $watchList->user_id=$this->EP_Contrib_reg->clientidentifier;
                $watchList->contract='1';
                $watchList->status='bid';
                $watchList->created_at=date('Y-m-d H:i:s');
                $watchList->contract_text='';

                $watchList->insert();
                $watchListId= $watchList->getIdentifier();




				//updating test article submit time
				/*$recruitmentDetails=$recruitObj->getRecruitmentDetails($recruitmentIdentifier);
				if($recruitmentDetails[0]['is_test_article']==1)//test article submit time 
				{					
					if($recruitmentDetails[0]['max_time_option']=='hour')
						$upload_time=$recruitmentDetails[0]['max_time_to_upload']*60*60;
					else
						$upload_time=$recruitmentDetails[0]['max_time_to_upload']*60;

					$article_submit_expires=time()+$upload_time;

					$insertRecruitArray['article_submit_expires']=$article_submit_expires;
				}	*/

                if($watchListId)
                {

    				$insertRecruitArray['recruitment_article_id']=$recruitment_article_id;
                    $insertRecruitArray['watchlist_id']=$watchListId;
    				$insertRecruitArray['user_id']=$this->EP_Contrib_reg->clientidentifier;
    				$insertRecruitArray['articles_per_week']=$articles_per_week;
    				$insertRecruitArray['proposed_cost']=$proposed_cost;
                    $insertRecruitArray['article_submit_expires']=0;


                    //refusing if quiz not qualified
                    if($this->EP_Contrib_reg->quiz_qualified=='no')
                        $insertRecruitArray['status']='bid_refused';
                    else  
                        $insertRecruitArray['status']='bid_pending';                    

    				//echo "<pre>";print_r($insertRecruitArray);exit;

    				//Inserting into RecruitmentParticipation Table               
                    $ParticipationId=$recruitObj->InsertParticipation($insertRecruitArray);
                }  
                unset($this->EP_Contrib_reg->recruitment_article_id);
                unset($this->EP_Contrib_reg->quizParticipationId);


                // $this->_redirect("/contrib/ongoing");
                if($quizParticipationId)
                    $this->_redirect("/recruitment/participation-challenge?recruitment_id=".$recruitmentIdentifier."&article_id=".$recruitment_article_id."#quiz_block");
                else
                    $this->_redirect("/recruitment/participation-challenge?recruitment_id=".$recruitmentIdentifier."&article_id=".$recruitment_article_id."#sign-contract");
				}
				else{
					$this->_redirect("/contrib/ongoing");
				}
			}	
		}	
    } 
    //price and articles per week validation
    public function getPricerangeAction()
    {   
        if($this->_helper->EpCustom->checksession())
        {
            $recParams=$this->_request->getParams();
            $recruitmentIdentifier= $recParams['recruitment_id'];
            $recruitObj=new Ep_Recruitment_Participation();
            $recruitmentDetails=$recruitObj->getRecruitmentDetails($recruitmentIdentifier);
            //print_r($articleDetails);exit;
            if($recruitmentDetails)
            {
                $price['price_min']=number_format($recruitmentDetails[0]['price_min'], 2, '.', '');
                $price['price_max']=number_format($recruitmentDetails[0]['price_max'], 2, '.', '');
                $price['max_articles_per_contrib']=$recruitmentDetails[0]['max_articles_per_contrib'];

                
                echo json_encode($price);
            } 
            else
                echo json_encode(array("error"=>"time_out"));

        }
        
    }  

}