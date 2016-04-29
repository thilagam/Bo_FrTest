<?php
/**
 * EditPlaceController
 * This controller is responsible for Contributor Session Cart Operations*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
 require_once('ContribController.php');
Class CartController Extends ContribController
{
    public function init()
	{
		parent::init();
	    $this->_view->livesite = $this->_config->www->baseurl;
		$this->_view->livesite_ssl = $this->_config->www->baseurlssl;
		$this->_view->lang = $this->_lang;
        $this->attachment_path=APP_PATH_ROOT.$this->_config->path->attachments;

		//print_r($this->_view);
        /**Get The user email and profile logo using session**/
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $profileplus_obj = new Ep_Contrib_ProfilePlus();
        $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
		$this->contrib_identifier=$contrib_identifier;
         if(($profile=$profileplus_obj->checkProfileExist($contrib_identifier))!='NO' && $contrib_identifier!='' )
         {
               $this->_view->client_email=ucfirst($profile[0]['first_name'])." ".($profile[0]['last_name']);
         }
        else if($this->EP_Contrib_reg->clientemail!='')
		    $this->_view->client_email=strtolower($this->EP_Contrib_reg->clientemail);

        $this->client_email=$this->_view->client_email;

         
       /**Start Cart Session**/
        $this->EP_Contrib_Cart = Zend_Registry::get('EP_Contrib_Cart');
        /**To enable StepLinks**/
            if(!isset($this->EP_Contrib_Cart->maxpage))
                        $this->EP_Contrib_Cart->maxpage=1;
        //echo $this->EP_Contrib_Cart->maxpage;

			/**Checking for existing participation's Count**/
            $participation_count_obj=new Ep_Participation_Participation();
            $current_participation_count=$participation_count_obj->getCurrentParticipationCount($contrib_identifier);
            $this->total_participation_count=$current_participation_count;

			
             /**Unread message count in inbox**/
            $ticket=new Ep_Ticket_Ticket();
            $this->_view->unreadCount=$ticket->getUnreadCount('contributor',$contrib_identifier);   

            $this->EP_Cache->clean(Zend_Cache::CLEANING_MODE_ALL);
           //Loading Configuration Settings if a cache already exists:
            if( ($configurations = $this->EP_Cache->load('configurations')) === false ) {
                  $config_obj=new Ep_User_Configuration();
                $configurations=$config_obj->getAllConfigurations();
                $this->EP_Cache->save($configurations, 'configurations');         
            } 
            $this->config=$configurations;

            //statistic if a cache already exists:
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
            $this->_view->profileType=$this->profileType;
            $this->_view->corrector=$this->corrector;

            $total_participation_count=$this->total_participation_count;
            
            if($this->profileType=="senior")
            {
               // echo $this->config['sc_limit']."-".$total_participation_count;
		$this->participationLeft=$this->config['sc_limit']-$total_participation_count;
                if($this->participationLeft<0)
                    $this->participationLeft=0;
            }
            else
            {
                $this->participationLeft=$this->config['jc_limit']-$total_participation_count;
                if($this->participationLeft<0)
                    $this->participationLeft=0;
            }            

            /**Selected AO in CART*/
             $selected_ao_count=(count($this->EP_Contrib_Cart->cart)+count($this->EP_Contrib_Cart->poll)+count($this->EP_Contrib_Cart->correction));
            
            $this->_view->selected_ao_count=$selected_ao_count;
        



	}
	 	
    public function indexAction()
    {
      // if($this->_helper->EpCustom->checksession())
      // {
        //$this->init();

        

        $cart_params=$this->_request->getParams();

            $action=$cart_params['cart_action'];
            $articleId=$cart_params['article_id'];

            switch($action)
            {
                case "add":    if(!isset($this->EP_Contrib_Cart->cart[$articleId]))
                                  $this->EP_Contrib_Cart->cart[$articleId]=1;
                               break;
                case "remove":    if(isset($this->EP_Contrib_Cart->cart[$articleId]))
                                  unset($this->EP_Contrib_Cart->cart[$articleId]);
                                  if(isset($this->EP_Contrib_Cart->price[$articleId]))
                                  unset($this->EP_Contrib_Cart->price[$articleId]);
                                  if(isset($this->EP_Contrib_Cart->accept[$articleId]))
                                  unset($this->EP_Contrib_Cart->accept[$articleId]);
                                  if(isset($this->EP_Contrib_Cart->cart_valid_date[$articleId]))
                                  unset($this->EP_Contrib_Cart->cart_valid_date[$articleId]);

                              
                                  break;

                case "p_add":    if(!isset($this->EP_Contrib_Cart->poll[$articleId]))
                                  $this->EP_Contrib_Cart->poll[$articleId]=1;            
                                  break;
                case "p_remove":  if(isset($this->EP_Contrib_Cart->poll[$articleId]))
                                  unset($this->EP_Contrib_Cart->poll[$articleId]);
                                  
                                  if(isset($this->EP_Contrib_Cart->poll_price[$articleId]))
                                  unset($this->EP_Contrib_Cart->poll_price[$articleId]);
                                  
                                  if(isset($this->EP_Contrib_Cart->poll_accept[$articleId]))
                                  unset($this->EP_Contrib_Cart->poll_accept[$articleId]);
                                  
                                  if(isset($this->EP_Contrib_Cart->poll_valid_date[$articleId]))
                                  unset($this->EP_Contrib_Cart->poll_valid_date[$articleId]);
                                  break;                  

                case "c_add":    if(!isset($this->EP_Contrib_Cart->correction[$articleId]))
                                  $this->EP_Contrib_Cart->correction[$articleId]=1;            
                                  break;
                case "c_remove":  if(isset($this->EP_Contrib_Cart->correction[$articleId]))
                                  unset($this->EP_Contrib_Cart->correction[$articleId]);
                                  
                                  if(isset($this->EP_Contrib_Cart->correction_price[$articleId]))
                                  unset($this->EP_Contrib_Cart->correction_price[$articleId]);
                                  
                                  if(isset($this->EP_Contrib_Cart->correction_accept[$articleId]))
                                  unset($this->EP_Contrib_Cart->correction_accept[$articleId]);
                                  
                                  if(isset($this->EP_Contrib_Cart->correction_valid_date[$articleId]))
                                  unset($this->EP_Contrib_Cart->correction_valid_date[$articleId]);
                               break;                

                               
                case "empty":  unset($this->EP_Contrib_Cart->cart);
                               unset($this->EP_Contrib_Cart->poll);     
                               unset($this->EP_Contrib_Cart->price);
                               unset($this->EP_Contrib_Cart->poll_price);

                               break;

            }
        
        $cart_count=count($this->EP_Contrib_Cart->cart)+count($this->EP_Contrib_Cart->correction);


		if($cart_params['from']=='menu')
        {
		    	$profile_completion=$this->calculateProfilePercentage();

            if($cart_count > $this->participationLeft)   
            {
                 unset($this->EP_Contrib_Cart->cart[$articleId]);
                 unset($this->EP_Contrib_Cart->correction[$articleId]);
                echo "exceed";
            }
            /*else if($profile_completion <80)
            {
                unset($this->EP_Contrib_Cart->cart[$articleId]);
                unset($this->EP_Contrib_Cart->correction[$articleId]);
                unset($this->EP_Contrib_Cart->poll[$articleId]);
            }*/

             echo "$$$$";
                $this->displayMenuCart();
            echo "$$$$";
            echo $this->profileType;

            if($this->profileType=='senior')
                $limit=$this->config['sc_limit'];
            else
                $limit=$this->config['jc_limit'];

            echo "$$$$";
            echo $limit;
            echo "$$$$";
            echo $profile_completion;

        }    
		else if($cart_params['from']=='main')
			$this->displaycartpart1Action();		
            
      // }

       
    }
    public function displayMenuCart()
    {

        if(count($this->EP_Contrib_Cart->cart)>0 || count($this->EP_Contrib_Cart->poll)>0 || count($this->EP_Contrib_Cart->correction)>0 )
        {
            $menuCart='<a class="btn btn-mini" role="button" href="/cart/cart-selection"><i class="icon-list-alt"></i> My selection <span class="badge badge-warning">'.(count($this->EP_Contrib_Cart->cart)+count($this->EP_Contrib_Cart->poll)+count($this->EP_Contrib_Cart->correction)).'</span></a>';
        }
        else
            $menuCart='<a class="btn btn-mini" role="button"><i class="icon-list-alt"></i> My selection <span class="badge">0</span></a>';

        echo $menuCart."####".(count($this->EP_Contrib_Cart->cart)+count($this->EP_Contrib_Cart->poll)+count($this->EP_Contrib_Cart->correction));

    }
	public function displaycartpart1Action()
	{

		if(count($this->EP_Contrib_Cart->cart)>0 || count($this->EP_Contrib_Cart->poll)>0 || count($this->EP_Contrib_Cart->correction)>0  )
		{
				//echo count($this->EP_Contrib_Cart->cart)."--".count($this->EP_Contrib_Cart->poll);
                $this->displayMenuCart();
        }

		
	}
    
   
    //cart page with price selection
    public function cartSelectionAction()
    {
        $profileplus_obj = new Ep_Contrib_ProfilePlus();
        $contrib_identifier=$this->contrib_identifier;


        if($this->_helper->EpCustom->checksession())
        {

            $profile_completion=$this->calculateProfilePercentage();
            if($profileplus_obj->checkProfileExist($contrib_identifier)!='NO' && $profile_completion >= 80)
            {
                $cart_count=count($this->EP_Contrib_Cart->cart);
                $poll_cart_count=count($this->EP_Contrib_Cart->poll);
                $correction_cart_count=count($this->EP_Contrib_Cart->correction);
                
                
                if($cart_count > $this->participationLeft)
                {
                    if($this->profileType=='senior')
                        $msg=("You have already applied for ".$this->config['sc_limit']." articles : send your articles and wait for our team to validate them before applying for the next ones");
                    else if($this->profileType=='sub-junior')
                        $msg=("You have already applied for ".$this->config['jc0_limit']." article(s) : wait for our team to validate them before applying for the next ones");                    
                    else
                        $msg=("You have already applied for ".$this->config['jc_limit']." article(s) : wait for our team to validate them before applying for the next ones");
                    

                    $this->_view->actionmessages=array($msg);
                }
                if($this->_helper->FlashMessenger->getMessages()) {
                    $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
                }

                if($cart_count>0 OR $poll_cart_count >0 OR $correction_cart_count > 0 )
                {    
                    if($cart_count>0)
                    {
                        $article=new Ep_Article_Article();
                        $cartdetails=array();
                        
                        $cartItems=0;
                        $articleIds='';
                        foreach($this->EP_Contrib_Cart->cart as $articleId=>$cart_count)
                        {

                            $article_params['articleId']=$articleId;
                            $articleDetails=$article->getArticleDetails($article_params);

                            if($articleDetails[0]['articleid'])
                            {   

                                $cartdetails[$cartItems]=$articleDetails[0];
                                $cartdetails[$cartItems]['title']=$articleDetails[0]['title'];      
                                $cartdetails[$cartItems]['articleid']=  $articleDetails[0]['articleid'] ;
                                $cartdetails[$cartItems]['price_min']=  $articleDetails[0]['price_min'] ;
                                $cartdetails[$cartItems]['price_max']=  $articleDetails[0]['price_max'] ;
                                $cartdetails[$cartItems]['price_user']=$this->EP_Contrib_Cart->price[$articleId];
                                $cartdetails[$cartItems]['AOtype']=$articleDetails[0]['AOtype'] ;

                                //net price Calculation
                                if(!$articleDetails[0]['premium_option'])
                                {
                                  $netprice=number_format((($cartdetails[$cartItems]['price_user']*100)/$articleDetails[0]['contrib_percentage']),2,'.','');
                                  $cartdetails[$cartItems]['netprice']=$netprice;
                                }
                                
                                 //private icon
                               if($articleDetails[0]['AOtype']=='private')
                               {
                                  $private_icon=$this->private_icon;
                                  $writers_count=count(explode(",",$articleDetails[0]['contribs_list']));
                                  if($writers_count>1)
                                    $toolTitle=$this->ptoolTitleMulti;
                                  else
                                    $toolTitle=$this->ptoolTitle;

                                    $toolTitle=str_replace('X',$writers_count,$toolTitle);
                                    $private_icon=str_replace('$toolTitle',$toolTitle,$private_icon);
                                                
                                   $cartdetails[$cartItems]['picon']=$private_icon;
                                  
                                }  
                               else
                                 $cartdetails[$cartItems]['picon']='';

                                //quiz icon
                              if($articleDetails[0]['link_quiz']=='yes' && $articleDetails[0]['quiz'] && !in_array($articleDetails[0]['quiz'],$this->qualifiedQuiz))
                               $cartdetails[$cartItems]['qicon']=$this->quiz_icon;
                              else
                               $cartdetails[$cartItems]['qicon']='';

                                //getting acceptance from cart session
                                if($this->EP_Contrib_Cart->accept[$articleId]=='yes')
                                    $cartdetails[$cartItems]['accept_yes']=' checked';
                                else if($this->EP_Contrib_Cart->accept[$articleId]=='no')
                                    $cartdetails[$cartItems]['accept_no']=' checked';
                                
                                //getting valid da from cart session
                                if($this->EP_Contrib_Cart->cart_valid_date[$articleId])
                                    $cartdetails[$cartItems]['date_limit']=$this->EP_Contrib_Cart->cart_valid_date[$articleId];
                                else 
                                    $cartdetails[$cartItems]['date_limit']=date("d-m-Y",strtotime("+3 day"));


                                /**submit and resubmit times**/
                                if($this->profileType=='senior')
                                {
                                    if($articleDetails[0]['senior_time'])
                                        $cartdetails[$cartItems]['article_submit_time']=$articleDetails[0]['senior_time'];
                                    else
                                        $cartdetails[$cartItems]['article_submit_time']=$this->config['sc_time'];

                                    if($articleDetails[0]['sc_resubmission'])
                                        $cartdetails[$cartItems]['article_resubmit_time']=$articleDetails[0]['sc_resubmission'];
                                    else
                                        $cartdetails[$cartItems]['article_resubmit_time']=$this->config['sc_resubmission'];

                                }
                                else if($this->profileType=='junior')
                                {
                                    if($articleDetails[0]['junior_time'])
                                        $cartdetails[$cartItems]['article_submit_time']=$articleDetails[0]['junior_time'];
                                    else
                                        $cartdetails[$cartItems]['article_submit_time']=$this->config['jc_time'];

                                    if($articleDetails[0]['jc_resubmission'])
                                        $cartdetails[$cartItems]['article_resubmit_time']=$articleDetails[0]['jc_resubmission'];
                                    else
                                        $cartdetails[$cartItems]['article_resubmit_time']=$this->config['jc_resubmission'];

                                }
                                else if($this->profileType=='sub-junior')
                                {
                                    if($articleDetails[0]['subjunior_time'])
                                        $cartdetails[$cartItems]['article_submit_time']=$articleDetails[0]['subjunior_time'];
                                    else
                                        $cartdetails[$cartItems]['article_submit_time']=$this->config['jco_time'];

                                    if($articleDetails[0]['jco_resubmission'])
                                        $cartdetails[$cartItems]['article_resubmit_time']=$articleDetails[0]['jco_resubmission'];
                                    else
                                        $cartdetails[$cartItems]['article_resubmit_time']=$this->config['jc0_resubmission'];
                                }

                                //Submit and Re-submit Time in Mins
                                if($cartdetails[$cartItems]['article_submit_time']>=60)
                                   $cartdetails[$cartItems]['article_submit_time_text']=($cartdetails[$cartItems]['article_submit_time']/60)." h";
                                else
                                   $cartdetails[$cartItems]['article_submit_time_text']=$cartdetails[$cartItems]['article_submit_time']." mn";  

                                 if($cartdetails[$cartItems]['article_resubmit_time']>=60)
                                   $cartdetails[$cartItems]['article_resubmit_time_text']=($cartdetails[$cartItems]['article_resubmit_time']/60)." h";
                                else
                                   $cartdetails[$cartItems]['article_resubmit_time_text']=$cartdetails[$cartItems]['article_resubmit_time']." mn";  


                                if($articleDetails[0]['participation_time']>=60)
                                    $cartdetails[$cartItems]['participation_time_text']=($articleDetails[0]['participation_time']/60)."h";
                                else
                                    $cartdetails[$cartItems]['participation_time_text']=$articleDetails[0]['participation_time']."mns";


                                if($articleDetails[0]['participation_expires'])
                                    $cartdetails[$cartItems]['timestamp']= $articleDetails[0]['participation_expires'];
                               // else
                                 //   $cartdetails[$cartItems]['timestamp']= strtotime(date('Y-m-d H:i:s',strtotime($articleDetails[0]['delivery_date']." 23:59:59")));
								 $cartdetails[$cartItems]['pricedisplay']= $articleDetails[0]['pricedisplay'];

                                    $cartItems++;                                
                            }
                            else
                            {
                                unset($this->EP_Contrib_Cart->cart[$articleId]);
                            }
                        }                   

                    }
                    else
                        $cartdetails=array();

                    //get details w.r.t Polls
                    if($poll_cart_count >0)
                    {
                        $poll_obj=new Ep_Poll_Participation();    
                        $poll_params['poll_ids']="'".implode("','",array_keys($this->EP_Contrib_Cart->poll))."'";
                        $pollDetails=$poll_obj->getPollDetails($poll_params);
                        if(count($pollDetails)>0)
                        {
                            $pollDetails=$this->formatPollDetials($pollDetails); 
                            //echo "<pre>";print_r($pollDetails);
                            $pcnt=0;
                            foreach($pollDetails as $poll)
                            {
                                //echo $this->EP_Contrib_Cart->poll_price[$poll['pollId']]."--".$poll['pollId']."<br>";
                                $pollDetails[$pcnt]['poll_price_user']=$this->EP_Contrib_Cart->poll_price[$poll['pollId']];

                                //getting acceptance from cart session

                                if($this->EP_Contrib_Cart->poll_accept[$poll['pollId']]=='yes')
                                    $pollDetails[$pcnt]['accept_yes']=' checked';
                                else if($this->EP_Contrib_Cart->poll_accept[$poll['pollId']]=='no')
                                    $pollDetails[$pcnt]['accept_no']=' checked';

                                //getting valid da from cart session
                                if($this->EP_Contrib_Cart->poll_valid_date[$poll['pollId']])
                                    $pollDetails[$pcnt]['date_limit']=$this->EP_Contrib_Cart->poll_valid_date[$poll['pollId']];
                                else
                                    $pollDetails[$pcnt]['date_limit']=date("d-m-Y");

                                $pcnt++;
                            }    

                        }    

                    }
                    else
                        $pollDetails=array();

                    //get details w.r.t Corrections
                    if($correction_cart_count >0)
                    {
                        
                        $article_obj=new Ep_Article_Article(); 
                        $correction_params['article_ids']="'".implode("','",array_keys($this->EP_Contrib_Cart->correction))."'";

                        $correction_params['profile_type']=$this->profileType;
                        $correction_params['corrector']=$this->corrector;
                        $correction_params['corrector_type']=$this->corrector_type;
                        $correctionDetails=$article_obj->getCorrectorAOs($correction_params);
                        if(count($correctionDetails)>0)
                        {
                            $correctionDetails=$this->formatCorrectionDetials($correctionDetails);                       
                            $ccnt=0;
                            foreach($correctionDetails as $correction)
                            {
                                //echo $this->EP_Contrib_Cart->poll_price[$poll['pollId']]."--".$poll['pollId']."<br>";
                                $correctionDetails[$ccnt]['price_corrector']=$this->EP_Contrib_Cart->correction_price[$correction['articleid']];

                                //getting acceptance from cart session

                                if($this->EP_Contrib_Cart->correction_accept[$correction['articleid']]=='yes')
                                    $correctionDetails[$ccnt]['accept_yes']=' checked';
                                else if($this->EP_Contrib_Cart->correction_accept[$correction['articleid']]=='no')
                                    $correctionDetails[$ccnt]['accept_no']=' checked';

                                //getting valid da from cart session
                                if($this->EP_Contrib_Cart->correction_valid_date[$correction['articleid']])
                                    $correctionDetails[$ccnt]['date_limit']=$this->EP_Contrib_Cart->correction_valid_date[$correction['articleid']];
                                else
                                    $correctionDetails[$ccnt]['date_limit']=date("d-m-Y");

                                //Correction submission in Mins
                                if($correctionDetails[$ccnt]['correction_submission']>=60)
                                    $correctionDetails[$ccnt]['correction_submission_text']=($correctionDetails[$ccnt]['correction_submission']/60)." h";
                                else
                                    $correctionDetails[$ccnt]['correction_submission_text']=$correctionDetails[$ccnt]['correction_submission']." mns";

                                if($correctionDetails[$ccnt]['correction_resubmission']>=60)
                                    $correctionDetails[$ccnt]['correction_resubmission_text']=($correctionDetails[$ccnt]['correction_resubmission']/60)." h";
                                else
                                    $correctionDetails[$ccnt]['correction_resubmission_text']=$correctionDetails[$ccnt]['correction_resubmission']." mns";  

                                $ccnt++;
                            }    

                        }    
                    }
                    else
                        $correctionDetails=array();



                      //echo "<pre>";print_r($correctionDetails);exit;

                    //Merging Article,correction and poll Details  
                    $cartdetails=array_merge($cartdetails,$pollDetails,$correctionDetails);                                           
                      


                }    
                else
				{
                   unset($this->EP_Contrib_Cart->correction);
				   unset($this->EP_Contrib_Cart->cart);
				   $this->_redirect("/contrib/aosearch");
                }
				if(count($cartdetails)==0)
				{
                    unset($this->EP_Contrib_Cart->correction);
					unset($this->EP_Contrib_Cart->cart);
					$this->_redirect("/contrib/aosearch");
				}
                    
                $this->_view->cartItems=$cartdetails;
                $this->_view->viewPage=$this->EP_Contrib_Cart->maxpage;
                $this->_view->RelatedArticles=$related_articles;
                $this->_view->contributor_identifier=$this->contrib_identifier;
                
                //echo "<pre>";print_r($cartdetails);exit;
                   
                $this->render("Contrib_cart_selection");


            } 
            else  
                $this->_redirect("/contrib/modify-profile"); 
        }    

    }
    //save cart-selection
    public function saveCartAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            
            $cart_total_count=count($this->EP_Contrib_Cart->cart);
            $cart_poll_count=count($this->EP_Contrib_Cart->poll);
            $correction_cart_count=count($this->EP_Contrib_Cart->correction);

            if($cart_total_count>0 || $cart_poll_count > 0 || $correction_cart_count > 0)
            {
                if($this->_request-> isPost() OR $this->EP_Contrib_Cart->maxpage>1 )
                {
                    $part1_params=$this->_request->getParams();
                  
                    if($cart_total_count>0)
                    {
                        /**validating article participations**/
                        foreach($this->EP_Contrib_Cart->cart as $articleId=>$cart_count)
                        {
							
							
								$error1="You must give your rate for each of the selected offers before proceeding to stage 2";
								$error2="<br> Please tick a box";
								if($part1_params["contrib_price_".$articleId]!=NULL && $part1_params["accept_".$articleId]=='yes')
								{
									//$regex="/^(?:\d+|\d*[\.]\d+)$/";
									$regex="/^[0-9]+(\.?[0-9]+)?$/";
									$price_article='';
									$price_article=str_replace(",",".",$part1_params["contrib_price_".$articleId]);
									$this->EP_Contrib_Cart->price[$articleId]=$price_article;
									$this->EP_Contrib_Cart->accept[$articleId]=$part1_params["accept_".$articleId];
									if(preg_match($regex,$price_article))
									{
										/**price validation**/
                                        $article=new Ep_Article_Article();
                                        $searchParameters['articleId']=$articleId;
                                        $articleDetails=$article->getArticleDetails($searchParameters); 
											$price['price_min']=number_format($articleDetails[0]['price_min'], 2, '.', '');
											$price['price_max']=number_format($articleDetails[0]['price_max'], 2, '.', '');
											
										   if((($articleDetails[0]['premium_option']!='0' && $articleDetails[0]['premium_option']!='') || ($articleDetails[0]['AOtype']=='private' && $articleDetails[0]['price_max'])) && $articleDetails[0]['pricedisplay']=="yes")
											{
												if($price_article >=$price['price_min'] && $price_article<=$price['price_max'])
													$this->EP_Contrib_Cart->price[$articleId]=$part1_params["contrib_price_".$articleId];
												else
												{
													$this->_helper->FlashMessenger($error1);
													$this->_redirect("/cart/cart-selection");
												}

												//Added for mission liberte Private
												if($articleDetails[0]['AOtype']=='private' && $articleDetails[0]['price_max'] && ($articleDetails[0]['premium_option']=='0' || $articleDetails[0]['premium_option']=='' ))
												{
													
												  if($part1_params["date_limit_".$articleId])
													  $part1_params["date_limit_".$articleId]=str_replace("/","-",$part1_params["date_limit_".$articleId]);
												  $this->EP_Contrib_Cart->cart_valid_date[$articleId]=$part1_params["date_limit_".$articleId];

												}
											}    
											else
											{
												$this->EP_Contrib_Cart->price[$articleId]=$part1_params["contrib_price_".$articleId];
												if($part1_params["date_limit_".$articleId])
													$part1_params["date_limit_".$articleId]=str_replace("/","-",$part1_params["date_limit_".$articleId]);
												$this->EP_Contrib_Cart->cart_valid_date[$articleId]=$part1_params["date_limit_".$articleId];
											}
									}    
									else
									{
										$this->_helper->FlashMessenger($error1);
										$this->_redirect("/cart/cart-selection");
									}
								}
								else
								{
									$this->_helper->FlashMessenger($error1.$error2);
									$this->_redirect("/cart/cart-selection");
								}
							}
                    } 
                 
                    if($cart_poll_count>0)   
                    {

                        /**validating Poll participations**/

                        foreach($this->EP_Contrib_Cart->poll as $pollId=>$cart_count)
                        {
                            $error1="You must give your rate for each of the selected offers before proceeding to stage 2";
                            $error2="<br> Please tick a box";
                            if($part1_params["poll_price_".$pollId]!=NULL && $part1_params["pollaccept_".$pollId]=='yes')
                            {
                                //$regex="/^(?:\d+|\d*[\.]\d+)$/";
                                $regex="/^[0-9]+(\.?[0-9]+)?$/";
                                $price_poll='';
                                $price_poll=str_replace(",",".",$part1_params["poll_price_".$pollId]);
                                $this->EP_Contrib_Cart->poll_price[$pollId]=$price_poll;
                                $this->EP_Contrib_Cart->poll_accept[$pollId]=$part1_params["pollaccept_".$pollId];
                                if(preg_match($regex,$price_poll))
                                {
                                    /**price validation**/
                                        $poll_obj=new Ep_Poll_Participation();
                                        $searchParameters['pollid']=$pollId;
                                        $pollDetails=$poll_obj->getPollDetails($searchParameters);
                                        $poll_price['price_min']=number_format($pollDetails[0]['price_min'], 2, '.', '');
                                        $poll_price['price_max']=number_format($pollDetails[0]['price_max'], 2, '.', '');
                                        
                                        
                                        if($pollDetails[0]['ao_type']=='poll_premium')
                                        {
                                            if(($price_poll >=$poll_price['price_min'] && $price_poll<=$poll_price['price_max']) || (intval($poll_price['price_max'])<=0 && $price_poll>=0))
                                                $this->EP_Contrib_Cart->poll_price[$pollId]=$part1_params["poll_price_".$pollId];
                                            else
                                            {
                                                $this->_helper->FlashMessenger($error1);
                                                $this->_redirect("/cart/cart-selection");
                                            }
                                        }
                                        else  if($pollDetails[0]['ao_type']=='poll_nopremium')
                                        {
                                             $this->EP_Contrib_Cart->poll_price[$pollId]=$part1_params["poll_price_".$pollId]; 
                                             if($part1_params["poll_date_limit_".$pollId])   
                                                $part1_params["poll_date_limit_".$pollId]=str_replace("/","-",$part1_params["poll_date_limit_".$pollId]);
                                             $this->EP_Contrib_Cart->poll_valid_date[$pollId]=$part1_params["poll_date_limit_".$pollId];
                                        }     
                                }    
                                else
                                {
                                    $this->_helper->FlashMessenger($error1);
                                    $this->_redirect("/cart/cart-selection");
                                }
                            }
                            else
                            {
                                $this->_helper->FlashMessenger($error1.$error2);
                                $this->_redirect("/cart/cart-selection");
                            }
                        }
                    }   


                    // Correction articles session
                    
                    if($correction_cart_count>0)   
                    {

                        /**validating Correction participations**/

                        foreach($this->EP_Contrib_Cart->correction as $correctionId=>$cart_count)
                        {
							
								$error1="You must give your rate for each of the selected offers before proceeding to stage 2";
								$error2="<br> Please tick a box";
								if($part1_params["corrector_price_".$correctionId]!=NULL && $part1_params["correctionaccept_".$correctionId]=='yes')
								{
									//$regex="/^(?:\d+|\d*[\.]\d+)$/";
									$regex="/^[0-9]+(\.?[0-9]+)?$/";
									$price_correction='';
									$price_correction=str_replace(",",".",$part1_params["corrector_price_".$correctionId]);
									$this->EP_Contrib_Cart->correction_price[$correctionId]=$price_correction;
									$this->EP_Contrib_Cart->correction_accept[$correctionId]=$part1_params["correctionaccept_".$correctionId];
									if(preg_match($regex,$price_correction))
									{
										/**price validation**/
                                        $correction_obj=new Ep_Article_Article();
                                        $correction_search_params['articleId']=$correctionId;
                                        $correction_search_params['profile_type']=$this->profileType;
                                        $correction_search_params['corrector']=$this->corrector;
                                        $correction_search_params['corrector_type']=$this->corrector_type;
                                        $correctionDetails=$correction_obj->getCorrectorAOs($correction_search_params);
											$correction_price['price_min']=number_format($correctionDetails[0]['correction_pricemin'], 2, '.', '');
											$correction_price['price_max']=number_format($correctionDetails[0]['correction_pricemax'], 2, '.', '');
											
											
											if($price_correction >=$correction_price['price_min'] && $price_correction<=$correction_price['price_max'])
												$this->EP_Contrib_Cart->correction_price[$correctionId]=$part1_params["corrector_price_".$correctionId];
											else
											{
												$this->_helper->FlashMessenger($error1);
												$this->_redirect("/cart/cart-selection");
											}
											
									}    
									else
									{
										$this->_helper->FlashMessenger($error1);
										$this->_redirect("/cart/cart-selection");
									}
								}
								else
								{
									$this->_helper->FlashMessenger($error1.$error2);
									$this->_redirect("/cart/cart-selection");
								}
                        }
                    } 
                    //print_r($this->EP_Contrib_Cart->correction_price);exit;

                    if($part1_params['maxpage'] && $part1_params['maxpage'] > $this->EP_Contrib_Cart->maxpage)
                        $this->EP_Contrib_Cart->maxpage=$part1_params['maxpage'];
                   // $this->_redirect("/cart/confirmpart2");
                    //print_r($this->EP_Contrib_Cart->poll);exit;
                    
                }
                else if(!count($this->EP_Contrib_Cart->price)>0 && !count($this->EP_Contrib_Cart->poll_price)>0 && !count($this->EP_Contrib_Cart->correction_price)>0)
                {
                    $this->_redirect("/cart/cart-selection");
                }
            }    
            else
            {
                $this->_redirect("/cart/cart-selection");
            }
            
            //echo "<pre>";print_r($this->EP_Contrib_Cart->price);print_r($this->EP_Contrib_Cart->poll_price);exit;

            if($correction_cart_count==0 && $cart_poll_count>0 && $cart_total_count==0) 
                $this->_redirect("/cart/save-participation?contract=yes&contract1=yes&carttype=devisonly");                
            else
					
				$this->_redirect("/cart/contract");
                
        }     
    
    }
    //contract page
    public function contractAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $profileplus_obj = new Ep_Contrib_ProfilePlus();
            $profileContrib_obj = new Ep_Contrib_ProfileContributor();

            $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
            $profile_contribinfo=$profileContrib_obj->getProfileInfo($contrib_identifier);
            $profileinfo=$profileplus_obj->getProfileInfo($contrib_identifier);

			$euro_amount=0;
			$pound_amount=0;
			
			if($this->_helper->FlashMessenger->getMessages()) {
				$this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
			}  
			
			//print_r($this->EP_Contrib_Cart->accept);exit;
            if(count($this->EP_Contrib_Cart->cart) > $this->participationLeft || $this->participationLeft==0)
            {
                $this->_redirect("/cart/cart-selection");
            }
			else if(count($this->EP_Contrib_Cart->price)>0 OR count($this->EP_Contrib_Cart->poll_price)>0  OR count($this->EP_Contrib_Cart->correction_price)>0)
            {
                $article_obj=new Ep_Article_Article();
                //print_r($this->EP_Contrib_Cart->cart);
               if(count($this->EP_Contrib_Cart->cart)>0 OR count($this->EP_Contrib_Cart->correction)>0)
               {
                    if(!$this->EP_Contrib_Cart->cart)$this->EP_Contrib_Cart->cart=array();
                    if(!$this->EP_Contrib_Cart->correction)$this->EP_Contrib_Cart->correction=array();

                    $article_ids=array_merge(array_keys($this->EP_Contrib_Cart->cart),array_keys($this->EP_Contrib_Cart->correction));

                    $articleIds=implode(",",$article_ids);

                    //echo $articleIds;exit;

                    $Articles_selected=$article_obj->getArticleContract($articleIds);

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
                                           <div style="width:98%;float:right">'.$Article['title'].'<span style="color:#FF6600"> ( '.$Article['deliveryTitle'].' )</span></div>
                                            </div>
                                    </li>';

                    //echo $this->EP_Contrib_Cart->price[$Article['id']]."--".$Article['id']."<br>";
                     $ArticlesWithPrice.='<p>Versement d&rsquo;un forfait fixe de <span style="color:#FF6600;font-weight:bold"> '.$this->EP_Contrib_Cart->price[$Article['id']].' Euros</span> comme convenu entre le  R&eacute;dacteur et le Client au titre du Contenu <b>'.$Article['title'].'</b></p>';
					 
						//Pound and euro amount calculations
						if($Article['currency']=='euro')
						{
							if($this->EP_Contrib_Cart->price[$Article['id']])
								$euro_amount+=$this->EP_Contrib_Cart->price[$Article['id']];
							elseif($this->EP_Contrib_Cart->price[$Article['id']])	
								$euro_amount+=$this->correction_price->correction_price[$Article['id']];
								
						}	
						else
						{							
							if($this->EP_Contrib_Cart->price[$Article['id']])
								$pound_amount+=$this->EP_Contrib_Cart->price[$Article['id']];
							elseif($this->EP_Contrib_Cart->price[$Article['id']])	
								$pound_amount+=$this->correction_price->correction_price[$Article['id']];
						}	

                    }
                    $Articles.='</ol>';
                    $ArticlesWithAO.='</ol>';
                }    

                $this->_view->meta_title="Author contract";
                $this->_view->viewPage=$this->EP_Contrib_Cart->maxpage;

                /**Name and Address Details*/
                $this->_view->LastName=$profileinfo[0]['last_name'];
                $this->_view->FirstName=$profileinfo[0]['first_name'];
                $this->_view->dob=date("d/m/Y",strtotime($profile_contribinfo[0]['dob']));
                $this->_view->address=$profileinfo[0]['address'].', '.$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city'];
                $this->_view->pays=$this->getCountryName($profileinfo[0]['country']);

                /**Articles Details**/
                $this->_view->ArticlesDetails=$Articles;
                $this->_view->ArticlesDetailsWithAO=$ArticlesWithAO;
                $this->_view->ArticlesDetailsWithPrice=$ArticlesWithPrice;
                $this->_view->startDate=date("d/m/Y");
				
				$this->_view->euro_amount=$euro_amount;
				$this->_view->pound_amount=$pound_amount;
				
				$this->_view->month=date("M");
				$this->_view->day=date("d");

	
                $this->render("Contrib_contract");

            }
            else
            {
               
                $this->_redirect("/cart/cart-selection");
            }
        }          

    }


    /**Create Watchlist and Save user Participation Details**/
    public function saveParticipationAction()
    {
        if($this->_helper->EpCustom->checksession())
        {
            $userIdentifier=$this->contrib_identifier;
            $contract_params=$this->_request->getParams();

            $contract_text=utf8dec(stripslashes($contract_params['contract_text']));
            //echo "<pre>";print_r($contract_params);exit;

            if($this->_request-> isPost() OR ($contract_params['carttype']=='devisonly'))
            {
                
                if($contract_params['contract']=='yes' && $contract_params['contract1']=='yes' )
                {
                    if(count($this->EP_Contrib_Cart->price)>0)
                    {
                        
                        $user_obj=new EP_Contrib_Registration();
                        $user_details=$user_obj->getUserInfo($userIdentifier);
                        $user_profile_type=$user_details[0]['profile_type'];
                        
                        if(!$watchListId)
                        {

                          $watchList=new Ep_Participation_Watchlist();
                          $watchList->user_id=$userIdentifier;
                          $watchList->contract='1';
                          $watchList->status='bid';
                          $watchList->created_at=date('Y-m-d H:i:s');
                          $watchList->contract_text=$contract_text;
                          
                          $watchList->insert();
                          $watchListId= $watchList->getIdentifier();
                        }  
                        
                        try
                        {
                            
                            if($watchListId)
                            {
                               // $watchListId= $watchList->getIdentifier();
                                $articleCount=count($this->EP_Contrib_Cart->price);
                                $acount=0;
                                $article_obj=new Ep_Article_Article();
                                $article_details=$article_obj->getArticleDetails($params);
                                $cnt=0;
                                foreach($article_details as $user_article)
                                {
                                    $articles[$cnt]=$user_article['articleid'];
                                    $cnt++;
                                }
                                unset($article_obj);
                                foreach($this->EP_Contrib_Cart->price as $articleId=>$bid_price)
                                {
                                    $bid_price=str_replace(",",".",$bid_price);
									
                                    if(in_array($articleId,$articles))
                                    {
                                        $participation=new Ep_Participation_Participation();
                                        $delivery=new Ep_Article_Delivery();
                                        $automail=new Ep_Ticket_AutoEmails();
                                        
                                        $participation->article_id=$articleId;
                                        $participation->watchlist_id=$watchListId;
                                        $participation->user_id=$userIdentifier;
                                        $participation->price_user=str_replace(",",".",$bid_price);
                                        
                                        $premium=$delivery->checkpublicPremiumAO($articleId);
                                        
                                        $delivery_details=$delivery->getDeliveryDetails($articleId);

                                        //added w.r.t AO Mission Test correction
                                        $missionTest=$delivery_details[0]['missiontest'];
                                        
                                        if($premium=='private')
                                        {
                                            $participation->status='bid';
                                            $this->EP_Contrib_Cart->private[$articleId]=1;
                                        }
                                        else if($premium=='premium')
                                        {
                                            if($missionTest=='yes')
                                                $participation->status='bid';
                                            else  
                                              $participation->status='bid_premium';

                                            $this->EP_Contrib_Cart->public[$articleId]=1;
                                        }
                                        else if($premium=='nonpremium')
                                        {
                                            $participation->status='bid_nonpremium';
                                            $this->EP_Contrib_Cart->nonpremium[$articleId]=1;
                                        }
                                        $participation->created_at=date('Y-m-d H:i:s');
                                        $participation->accept_specifications=$this->EP_Contrib_Cart->accept[$articleId];

                                        if($this->EP_Contrib_Cart->cart_valid_date[$articleId])
                                            $participation->valid_date=date('Y-m-d',strtotime($this->EP_Contrib_Cart->cart_valid_date[$articleId]));
										
										$participation->ipaddress=$_SERVER['REMOTE_ADDR'];

                                        //print_r($participation);exit;
                                        if($participation->insert())
                                        {                                            

                                            $participation_id=$participation->getIdentifier();

                                            //added w.r.t AO Mission Test correction
                                            // Stop participation (update participation time in article table)  
                                            if($missionTest=='yes')
                                            {
                                              $article_obj=new Ep_Article_Article();
                                              $pexpires=time()-(60*3);
                                              $data_array = array("participation_expires"=>$pexpires);////////updating
                                              $query=" id='".$articleId."'";
                                              $article_obj->updateArticle($data_array,$query);
                                            }  


                                            /**Article Submission Expires usually 2days for senior & 1day for junior**/
                                            if($user_profile_type=='senior')
                                            {
                                                if($delivery_details[0]['senior_time'])
                                                {
                                                    $time=$delivery_details[0]['senior_time'];//2days
                                                }
                                                else
                                                    $time=$this->config['sc_time'];//2days
                                            }    
                                            else if($user_profile_type=='junior')
                                            {
                                                 if($delivery_details[0]['junior_time'])
                                                {
                                                    $time=$delivery_details[0]['junior_time'];//2days
                                                }
                                                else
                                                    $time=$this->config['jc_time'];//1day
                                            }
                                            else if($user_profile_type=='sub-junior')
                                            {
                                                if($delivery_details[0]['subjunior_time'])
                                                {
                                                    $time=$delivery_details[0]['subjunior_time'];//2days
                                                }
                                                else
                                                    $time=$this->config['jc0_time'];//1day
                                            }
                                            
                                            //added w.r.t AO Mission Test correction
                                            // Add article submit time to the writer as writer automatically selected
                                            if($missionTest=='yes')
                                            {  
                                              $expires=time()+(60*$time);
                                              $data_array = array("article_submit_expires"=>new Zend_Db_Expr('article_submit_expires+'.$expires));////////updating
                                              $query="id='".$participation_id."'";
                                              $participation->updateArticleSubmitExpires($data_array,$query);
                                            }  


                                            //Sending Auto Emails
                                            $client_indentifier=$delivery_details[0]['user_id'];
                                            $clientDetails=$automail->getUserDetails($client_indentifier);
                                            $parameters['client_name']=$clientDetails[0]['username'];
                                            $parameters['article_title']='<a href="/contrib/ongoing">'.$delivery_details[0]['articleName'].'</a>';

                                            if($premium=='nonpremium')
                                            {

                                                $parameters['AO_title']=$delivery_details[0]['articleName'];     

                                                $ticket=new Ep_Ticket_Ticket();    
                                                $parameters['contributor_name']=$ticket->getUserName($userIdentifier);                                       
                                                                                           
                                                $parameters['ongoinglink']='/client/quotes?id='.$articleId;

                                                if($this->profileType=="senior")
                                                    $parameters['participation_limit']=$this->config['sc_limit'];
                                                else
                                                    $parameters['participation_limit']=$this->config['jc_limit'];
                                                $parameters['AO_end_date']=date("d-m-Y H:i:s",strtotime($delivery_details[0]['delivery_date']));
                                                //sending mail to client
                                                if($delivery_details[0]['mail_send']=='yes')
                                                    $automail->messageToEPMail($client_indentifier,7,$parameters);
                                                //sending mail to writer
                                                $automail->messageToEPMail($userIdentifier,22,$parameters);
                                            }
                                            else if($premium=='premium')
                                            {
                                                //sending mail to writer
                                                $contrib_list=$delivery_details[0]['contribs_list'];
                                                $contrib_list=explode(",",$contrib_list);
                                                if($delivery_details[0]['AOtype']=='private' && count($contrib_list)==1)
                                                {
                                                    $automail->messageToEPMail($userIdentifier,75,$parameters);
                                                }
                                                else
                                                {
                                                    $automail->messageToEPMail($userIdentifier,23,$parameters);    
                                                }                                                
                                            }

                                            
                                            

                                             //Inserting Recent Activities
                                             $activity_obj=new Ep_User_RecentActivities();                                     
                                             $client_id=$delivery_details[0]['user_id'];

                                              if($client_id)
                                              {  
                                                $activity_array['type']='quote';
                                                $activity_array['created_at']=date("Y-m-d H:i:s");
                                                $activity_array['user_id']=$client_id;
                                                $activity_array['activity_by']=$userIdentifier;
                                                $activity_array['article_id']=$articleId;                            
                                                $activity_obj->insertRecentActivities($activity_array);
                                              } 


                                              //Insert action in history table
                                                $action_obj=new Ep_Article_ArticleActions();
                                                $history_obj=new Ep_Article_ArticleHistory();

                                                $action_sentence= $action_obj->getActionSentence(30);

                                                $ticket=new Ep_Ticket_Ticket();                                                  
                                                $contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$userIdentifier.'" target=_blank""><b>'.$this->_view->client_email.'</b></a>';
                                                $article_name='<b>'.$delivery_details[0]['articleName'].'</b>';
												$currency=$delivery_details[0]['currency'];
                                                $price_user=str_replace(",",".",$bid_price)." $currency(s)";

                                                eval("\$action_sentence= \"$action_sentence\";");

                                                if($action_sentence)
                                                {  
                                                  $history_array['article_id']=$articleId;                                                
                                                  $history_array['user_id']=$userIdentifier;
                                                  $history_array['stage']='FO'; 
                                                  $history_array['action']='participation';
                                                  $history_array['action_at']=date("Y-m-d H:i:s");
                                                  $history_array['action_sentence']=$action_sentence;                                  

                                                  $history_obj->insertHistory($history_array);
                                                } 


                                                //added w.r.t AO Mission Test correction
                                                //sending accept mail automatically
                                                if($missionTest=='yes')
                                                {
                                                    sleep(1);
                                                    $this->userSelectionAutoEmails($participation_id,$userIdentifier,$expires);
                                                }                                                

                                        }
                                        $acount++;
                                        if($articleCount==$acount)
                                        {
                                           $this->EP_Contrib_Cart->Send_artcileId=$articleId;
                                           $this->EP_Contrib_Cart->ParticipationId=$participation_id;
                                        }
                                        
                                    }    
                                }
                                
                                unset($this->EP_Contrib_Cart->cart);
                                unset($this->EP_Contrib_Cart->price);
                                unset($this->EP_Contrib_Cart->accept);
                                unset($this->EP_Contrib_Cart->maxpage);
                                unset($this->EP_Contrib_Cart->cart_valid_date);
                                //exit;
                                $this->_helper->FlashMessenger('Bid Successfully.');
                                //$this->_redirect("/cart/participation-success");
                            }
                        }
                        catch(Zend_Exception $e)
                        {
                            echo $e->getMessage();exit;                           
                            
                        }
                        
                    }

                    //Inseting Poll Participations
                    if(count($this->EP_Contrib_Cart->poll_price)>0)
                    {
                        $poll_obj=new Ep_Poll_Participation();
                        $searchParameters['profile_type']=$this->profileType;
                        $pollDetails=$poll_obj->getPollDetails($searchParameters);   

                        if(count($pollDetails)>0)
                        {
                            $cnt=0;
                            foreach($pollDetails as $user_poll)
                            {
                                $polls[$cnt]=$user_poll['pollId'];
                                $cnt++;
                            }
                        } 
                        unset($poll_obj);
                        
                        foreach($this->EP_Contrib_Cart->poll_price as  $poll_id =>$poll_price)
                        {
                            
                           // if(in_array($poll_id,$polls))
                            if (false !== $pkey = array_search($poll_id, $polls)) 
                            {
                                 $poll_obj=new Ep_Poll_Participation();        
                                 $poll_obj->poll_id=$poll_id;
                                 $poll_obj->user_id=$userIdentifier;
                                 $poll_obj->price_user=str_replace(",",".",$poll_price);
                                 $poll_obj->per_week=(int)$poll_Params['per_week'];
                                 $poll_obj->possible_hours=0;
                                 $poll_obj->status="active";
                                 if($this->EP_Contrib_Cart->poll_valid_date[$poll_id])
                                            $poll_obj->availability=date('Y-m-d',strtotime($this->EP_Contrib_Cart->poll_valid_date[$poll_id]));
                                 
                                 try
                                {
                                     //echo "<pre>".print_r($poll_obj)."</pre>";                                     
                                     //exit;
                                    $poll_obj->insert();

                                    //Inserting Recent Activities
                                     $activity_obj=new Ep_User_RecentActivities();                                     
                                     $client_id=$pollDetails[$pkey]['client'];                                     

                                      if($client_id)
                                      {  
                                        $activity_array['type']='pollquote';
                                        $activity_array['created_at']=date("Y-m-d H:i:s");
                                        $activity_array['user_id']=$client_id;
                                        $activity_array['activity_by']=$userIdentifier;
                                        $activity_array['article_id']=$poll_id;                            
                                        $activity_obj->insertRecentActivities($activity_array);
                                      } 


                                    //Insert action in history table
                                      $action_obj=new Ep_Article_ArticleActions();
                                      $history_obj=new Ep_Article_ArticleHistory();

                                      $action_sentence= $action_obj->getActionSentence(31);

                                      $ticket=new Ep_Ticket_Ticket();                                                  
                                      $contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$userIdentifier.'" target=_blank""><b>'.$this->_view->client_email.'</b></a>';
                                      $poll_name='<b>'.$pollDetails[$pkey]['title'].'</b>';
                                      $poll_price=str_replace(",",".",$poll_price);

                                      eval("\$action_sentence= \"$action_sentence\";");

                                      if($action_sentence)
                                      {  
                                        $history_array['article_id']=$poll_id;                                                
                                        $history_array['user_id']=$userIdentifier;
                                        $history_array['stage']='FO'; 
                                        $history_array['action']='poll_participation';
                                        $history_array['action_at']=date("Y-m-d H:i:s");
                                        $history_array['action_sentence']=$action_sentence;                                  

                                        $history_obj->insertHistory($history_array);
                                      }      

                                      

                                      //insert poll response if exists
                                      if(is_array($this->EP_Contrib_Cart->poll_response[$poll_id]))
                                      {
                                         $responseArray= array();                                         
                                         $responseArray=$this->EP_Contrib_Cart->poll_response[$poll_id];
                                         
                                         foreach($responseArray as $responsePoll)
                                         {
                                           $pollResponseObj=new Ep_Poll_UserResponse();
                                           if(count($responsePoll)>0)
                                              $pollResponseObj->InsertPollResponse($responsePoll);
                                         }   
                                      }
                                      


                                     //Check and update Poll response Price 
                                     $responsePollObj=new Ep_Poll_UserResponse();
                                     $checkResponseExist=$responsePollObj->checkPollResponse($userIdentifier,$poll_id);                                       
                                     if($checkResponseExist!="NO")
                                     {
                                        //get question id to update the price
                                        $question_id=$responsePollObj->getPriceQuestion($poll_id);                                        
                                        if($question_id!="NO")  
                                        {
                                          $data = array("response"=>$poll_price);////////updating
                                          $query = "question_id= '".$question_id."' and poll_id='".$poll_id."' and user_id='".$userIdentifier."'";
                                          $responsePollObj->updatePollPrice($data,$query);
                                        }  
                                     }

                                    $this->_helper->FlashMessenger('Survey saved.');
                                    
                                }
                                catch(Zend_Exception $e)
                                {
                                    echo $e->getMessage();exit;                             

                                }

                            }     
                        }
                        unset($this->EP_Contrib_Cart->poll);
                        unset($this->EP_Contrib_Cart->poll_price);
                        unset($this->EP_Contrib_Cart->poll_accept);
                        unset($this->EP_Contrib_Cart->poll_valid_date);
                        unset($this->EP_Contrib_Cart->poll_response);
                        unset($this->EP_Contrib_Cart->maxpage);
                    }    

                    //Inserting Correction Participation
                    
                    if(count($this->EP_Contrib_Cart->correction_price)>0)
                    {
                        if(!$watchListId)
                        {

                          $watchList=new Ep_Participation_Watchlist();
                          $watchList->user_id=$userIdentifier;
                          $watchList->contract='1';
                          $watchList->status='bid';
                          $watchList->created_at=date('Y-m-d H:i:s');
                          $watchList->contract_text=$contract_text;
                          
                          $watchList->insert();
                          $watchListId= $watchList->getIdentifier();
                        } 
                        

                        $article=new Ep_Article_Article();
                        $correction_params='';
                        $correction_params['profile_type']=$this->profileType;
                        $correction_params['articleId']=$article_id;
                        $correction_params['corrector']=$this->corrector;
                        $correction_params['corrector_type']=$this->corrector_type;
                        $articleDetails=$article->getCorrectorAOs($correction_params);  

                        foreach($articleDetails as $index=>$correction_article)
                        {
                            $correction_articles[$index]=$correction_article['articleid'];
                        }

                        //echo "<pre>";print_r($correction_articles);exit;

                        foreach($this->EP_Contrib_Cart->correction_price as  $article_id => $price_corrector)
                        {
                            $price_corrector=str_replace(",",".",$price_corrector);
                          if(in_array($article_id,$correction_articles))
                          {
                              $accept_spec=$this->EP_Contrib_Cart->correction_accept[$article_id] ;

                              $delivery=new Ep_Article_Delivery();
                              $delivery_details=$delivery->getDeliveryDetails($article_id);

                            //added w.r.t AO Mission Test correction
                            $missionTest=$delivery_details[0]['missiontest'];
                              $correctionType=$delivery_details[0]['correctionType'];
                            

                            $cparticipation=new Ep_Participation_CorrectorParticipation();
                            $paricipation_obj=new Ep_Participation_Participation();
                            /**get contributor participation id based on article**/
                              $participate_id='';
                            $contributor_partcipation_details=$paricipation_obj->getContribParticipation($article_id);
                              if($contributor_partcipation_details!='NO')
                            $participate_id=$contributor_partcipation_details[0]['id'];
                            $automail=new Ep_Ticket_AutoEmails();
                            $cparticipation->article_id=$article_id;
                            $cparticipation->corrector_id=$userIdentifier;
                            $cparticipation->participate_id=$participate_id;
                            $cparticipation->price_corrector=str_replace(",",".",$price_corrector);

                            $cparticipation->watchlist_id=$watchListId;

                            

                              if($missionTest=='yes' && $correctionType=='multi_external')
                              $cparticipation->status='bid';
                            else
                              $cparticipation->status='bid_corrector';

                            $cparticipation->created_at=date('Y-m-d H:i:s');
                            $cparticipation->accept_specifications=$accept_spec;
							
							$cparticipation->ipaddress=$_SERVER['REMOTE_ADDR'];

                             
                            if($cparticipation->insert())
                            {
                                $corrector_participation_id=$cparticipation->getIdentifier();
                                //added w.r.t AO Mission Test correction
                                // Add article submit time to the writer as writer automatically selected
                                  if($missionTest=='yes' && $correctionType=='multi_external')
                                {  
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
                                    $expires=time()+(60*$correction_submission);
                                    $data_array = array("corrector_submit_expires"=>$expires);////////updating
                                    $query="id='".$corrector_participation_id."'";
                                    $cparticipation->updateArticleSubmitExpires($data_array,$query);
                                }                                
                                $this->_helper->FlashMessenger('Participation enregistr&eacute;e.');
                                
                            }

                             //Sending Auto Emails                              
                              $client_indentifier=$delivery_details[0]['user_id'];
                              $clientDetails=$automail->getUserDetails($client_indentifier);
                              $parameters['client_name']=$clientDetails[0]['username'];
                              $parameters['article_title']='<a href="/contrib/ongoing">'.$delivery_details[0]['articleName'].'</a>';
                              $automail->messageToEPMail($userIdentifier,80,$parameters);  



                              //Insert action in history table
                              $action_obj=new Ep_Article_ArticleActions();
                              $history_obj=new Ep_Article_ArticleHistory();

                              $action_sentence= $action_obj->getActionSentence(32);

                              $ticket=new Ep_Ticket_Ticket();                                                  
                                $corrector_name='<a class="corrector" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$userIdentifier.'" target=_blank""><b>'.$this->_view->client_email.'</b></a>';
                              $article_name='<b>'.$delivery_details[0]['articleName'].'</b>';
							  $currency=$delivery_details[0]['currency'];                                                
                              $price_corrector=str_replace(",",".",$price_corrector)." $currency(s)";

                              eval("\$action_sentence= \"$action_sentence\";");

                              if($action_sentence)
                              {  
                                $history_array['article_id']=$article_id;                                                
                                $history_array['user_id']=$userIdentifier;
                                $history_array['stage']='FO'; 
                                $history_array['action']='corrector_participation';
                                $history_array['action_at']=date("Y-m-d H:i:s");
                                $history_array['action_sentence']=$action_sentence;                                  

                                $history_obj->insertHistory($history_array);
                              }                                                 

                              //added w.r.t AO Mission Test correction
                              //sending accept mail automatically
                                if($missionTest=='yes' && $correctionType=='multi_external')
                              {
                                  sleep(1);				
								 //echo $corrector_participation_id."--".$userIdentifier."--".$expires."--".$this->corrector_type;
                                  $this->correctorSelectionAutoEmails($corrector_participation_id,$userIdentifier,$expires,$this->corrector_type);
                              } 

                          }
                            //echo "<pre>";print_r($this->EP_Contrib_Cart->correction_price);
                        }    
                        //exit;
                        unset($this->EP_Contrib_Cart->correction);
                        unset($this->EP_Contrib_Cart->correction_price);
                        unset($this->EP_Contrib_Cart->correction_accept);
                        unset($this->EP_Contrib_Cart->correction_valid_date);
                        unset($this->EP_Contrib_Cart->maxpage);
                    }    

                    //exit;

                    
                }
                else
                {
                    $this->_redirect("/cart/contract");
                }
                
            }
            else
            {
                $this->_redirect("/cart/contract");
            }

            $this->_redirect("/cart/participation-success");
            
        }
    }
     //Participation success page
    public function participationSuccessAction()
    {
        //$this->render("Contrib_participation_ok");exit;
        if($this->_helper->EpCustom->checksession())
        {
            if($this->_helper->FlashMessenger->getMessages())
            {
                unset($this->EP_Contrib_Cart->public);
                unset($this->EP_Contrib_Cart->private);
                unset($this->EP_Contrib_Cart->nonpremium);

                $this->render("Contrib_participation_ok");
            }    
            else
            {
                $this->_redirect("/contrib/aosearch");
            }
        }   
    
    }
    //up to this edited
    

     public function confirmpart4Action()
    {
        if($this->_helper->EpCustom->checksession())
        {


            if($this->_helper->FlashMessenger->getMessages())
            {
                if(count($this->EP_Contrib_Cart->public)>0)
                {
                     
                    $article=new Ep_Article_Article();
                    $articleIds=implode(",",array_keys($this->EP_Contrib_Cart->public));

                     if(count($this->EP_Contrib_Cart->nonpremium)>0)
                        $premiumArticles=$article->getMultipleArticleNames($articleIds,",");
                     else
                        $premiumArticles=$article->getMultipleArticleNames($articleIds);

                    //echo $premiumArticles;
                    $this->_view->premiumArticles=$premiumArticles;
                    unset($this->EP_Contrib_Cart->public);

                }
                if(count($this->EP_Contrib_Cart->private)>0)
                {

                    $article_private=new Ep_Article_Article();
                    $articleIds=implode(",",array_keys($this->EP_Contrib_Cart->private));
                    $privateArticles=$article_private->getMultipleArticleNames($articleIds);
                    //echo "p==".$privateArticles;
                    $this->_view->privateArticles=$privateArticles;

                   /**SENDING CONDIRMATION MAIL**/
                   // $automail=new Ep_Ticket_AutoEmails();
                    

                    unset($this->EP_Contrib_Cart->private);

                }
                if(count($this->EP_Contrib_Cart->nonpremium)>0)
                {

                    $article_private=new Ep_Article_Article();
                    $articleIds=implode(",",array_keys($this->EP_Contrib_Cart->nonpremium));
                    $nonpremiumArticles=$article_private->getMultipleArticleNames($articleIds,",");
                    //echo "p==".$privateArticles;
                    $this->_view->nonpremiumArticles=$nonpremiumArticles;

                   /**SENDING CONDIRMATION MAIL**/
                   // $automail=new Ep_Ticket_AutoEmails();


                    unset($this->EP_Contrib_Cart->nonpremium);

                }


                $articleId=$this->EP_Contrib_Cart->Send_artcileId;
                $participationId=$this->EP_Contrib_Cart->ParticipationId;
                $this->_view->meta_title="Author-ConfirmCartPart4";
                $this->_view->articleId=$articleId;
                $this->_view->participationId=$participationId;

                $this->render("EP_Contrib_Confirm_Cart_Part4");


            }
            else
            {
                $this->_redirect("/contrib/aosearch");
            }
        }
    }   
    
   
    /**sending auto accept when user participated for mission test AO**/
    public function userSelectionAutoEmails($participationId,$userid,$expires)
    {
        
          $participation=new Ep_Participation_Participation();
          $automail=new Ep_Ticket_AutoEmails();
          $participationDetails=$participation->getParticipateDetails($participationId);
          $parameters['AO_end_date']=date("d/m/Y",$expires)." &agrave; ".date("H:i:s",$expires);//date("d/m/Y",strtotime($participationDetails[0]['submitdate_bo']));
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
          $parameters['royalty']=$participationDetails[0]['price_user'];
          $parameters['resubmit_time']=$this->config['sc_resubmission'];
                    
          $automail->messageToEPMail($userid,25,$parameters);
                
          //Insert action in history table
          $action_obj=new Ep_Article_ArticleActions();
          $history_obj=new Ep_Article_ArticleHistory();

          $action_sentence= $action_obj->getActionSentence(3);

          $ticket=new Ep_Ticket_Ticket();                                                  
          $contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$userid.'" target=_blank""><b>'.$ticket->getUserName($userid,TRUE).'</b></a>';
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
      
    }

    /**sending auto accept when corrector participated for mission test AO**/
    public function correctorSelectionAutoEmails($participationId,$userid,$expires,$corrector_type)
    {
              
      $participation=new Ep_Participation_CorrectorParticipation();
      $automail=new Ep_Ticket_AutoEmails();
      $participationDetails=$participation->getParticipateDetails($participationId);
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
      $parameters['ongoinglink']="/contrib/mission-corrector-deliver?article_id=".$participationDetails[0]['article_id'];
      $parameters['royalty']=$participationDetails[0]['price_corrector'];
      if($corrector_type=='senior')
          $parameters['resubmit_time']=$participationDetails[0]['correction_sc_resubmission'];
      else
          $parameters['resubmit_time']=$participationDetails[0]['correction_jc_resubmission'];
      
          $automail->messageToEPMail($userid,28,$parameters);

      //Insert action in history table
        $action_obj=new Ep_Article_ArticleActions();
        $history_obj=new Ep_Article_ArticleHistory();

        $action_sentence= $action_obj->getActionSentence(15);

        $ticket=new Ep_Ticket_Ticket();                                                  
        $contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$userid.'" target=_blank""><b>'.$ticket->getUserName($userid,TRUE).'</b></a>';
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
      
    }

}
