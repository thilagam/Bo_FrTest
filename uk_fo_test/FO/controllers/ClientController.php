<?php

class ClientController extends Ep_Controller_Action 
{
	public function init() 
	{
		parent::init();
		$this->EP_Client = Zend_Registry::get('EP_Client');
		$this->_view->client_email=$this->EP_Client->clientemail;
		$this->_view->clientidentifier=$this->EP_Client->clientidentifier;
		$this->clientidentifier=$this->EP_Client->clientidentifier; 
		//$this->_view->paginationlimit = $this->getConfiguredval("pagination_fo");
		$user_obj=new Ep_User_User();
		$this->_view->clientname=$user_obj->getClientname($this->_view->clientidentifier);
		
		if($this->_view->clientidentifier!="")
		{	
			$ticket=new Ep_Ticket_Ticket();
            $this->_view->unreadmessage=$ticket->getUnreadCount('client',$this->EP_Client->clientidentifier); 
            $this->_view->unreadCount=$this->_view->unreadmessage;
			$this->_view->usertype='client';
		}

		$this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');	
		if($this->EP_Contrib_reg->clientidentifier!="")
		{
			$profileplus_obj = new Ep_Contrib_ProfilePlus();
            $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
            if(($profile=$profileplus_obj->checkProfileExist($contrib_identifier))!='NO' && $contrib_identifier!='' )
            {
                $this->_view->client_email=ucfirst($profile[0]['first_name'])." ".($profile[0]['last_name']);
            }
            else if($this->EP_Contrib_reg->clientemail!='')
              $this->_view->client_email=strtolower($this->EP_Contrib_reg->clientemail);
			$this->_view->usertype='contributor';
		}
		
		/**cache for statistics && config**/    
            $this->EP_Cache->clean(Zend_Cache::CLEANING_MODE_ALL);
           //Loading Configuration Settings see if a cache already exists:
            if( ($configurations = $this->EP_Cache->load('configurations')) === false ) {
                  $config_obj=new Ep_User_Configuration();
                $configurations=$config_obj->getAllConfigurations();
                $this->EP_Cache->save($configurations, 'configurations');         
            } 
            $this->config=$configurations;
			$this->attachment_path=APP_PATH_ROOT.$this->_config->path->attachments;

		//Footer stats
		$stat_obj=new Ep_Stats_Stats();
		$configstat=array('stats_display' => $this->getConfiguredval('stats_display'), 'stats_days_value' => $this->getConfiguredval('stats_days_value'));
		$stats=$stat_obj->getAllStatistics($configstat);
		$this->_view->stats=$stats;
		
		$this->_view->page_title="Edit-place premium: optimised and exclusive content written by experts";
		$this->_view->page_url=$_SERVER['REQUEST_URI'];		
	}
	
	/* Home page */
	public function indexAction()
	{
		if($_GET['target'])
			$_SESSION['target']=$_GET['target'];

        $this->EP_Client = Zend_Registry::get('EP_Client');
		if($this->EP_Client->clientidentifier!="")
		{
			$this->_redirect("/client/home");
			exit;
		}
		else
		{
			$this->_redirect("/index");
			exit;
		}
	}
	/** not in use now**/
	public function premiumAction()
	{
		if($this->_view->clientidentifier!="")
		{
			$this->_view->usertype='client';
		}	
		$delivery_obj=new Ep_Ao_Delivery();
		$part_obj=new Ep_Ao_Participation();
		$lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		$categories_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		
		//Carousel1
		$Carousel1List=$part_obj->getCarousel1Contribs();
	
		$Carousel1=array();
		$n=0;
		$frcount=0;
			for($c=0;$c<count($Carousel1List) && $n<6;$c++)
			{
				$profiledir=APP_PATH_ROOT.'profiles/contrib/pictures/'.$Carousel1List[$c]['identifier'].'/'.$Carousel1List[$c]['identifier'].'_h.jpg';
					
				if(file_exists($profiledir))
				{
					$profilepercent=$this->calculateProfilePercentage($Carousel1List[$c]['identifier']);
					//echo $profilepercent."<br>";
					if($profilepercent>80)
					{
						if($Carousel1List[$c]['language']=="fr")
							$frcount++;
							
						if($Carousel1List[$c]['language']!="fr" || $frcount<=3)
						{						
							$Carousel1[$n]['percent']=$profilepercent;
							$Carousel1[$n]['profilepic']='/FO/profiles/contrib/pictures/'.$Carousel1List[$c]['identifier'].'/'.$Carousel1List[$c]['identifier'].'_h.jpg';
							$Carousel1[$n]['category']=$this->getCategoryName($Carousel1List[$c]['favourite_category']);
							$Carousel1[$n]['first_name']=$Carousel1List[$c]['first_name'];
							$Carousel1[$n]['last_name']=$Carousel1List[$c]['last_name'];
							$Carousel1[$n]['language']=$Carousel1List[$c]['language'];
							$Carousel1[$n]['languagetitle']=$lang_array[$Carousel1List[$c]['language']];
							$n++;
						}
					}
				}
				
			}
		$this->_view->Carousel1List=$Carousel1;
		
		//Carousel2
		$categories_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$rand_cat=array_rand($categories_array,2);
		
			//Category1
				$m1=0;
				$cat1=$rand_cat[0];
				$Carousel21List=$part_obj->getCarousel2Contribs($cat1);
		
				$Carousel2_1=array();
					for($c=0;$c<count($Carousel21List) && $m1<3;$c++)
					{
						$profiledir=APP_PATH_ROOT.'profiles/contrib/pictures/'.$Carousel21List[$c]['identifier'].'/'.$Carousel21List[$c]['identifier'].'_h.jpg';
							
						if(file_exists($profiledir))
						{
							$profilepercent=$this->calculateProfilePercentage($Carousel21List[$c]['identifier']);
							
							if($profilepercent>50)
							{
								$Carousel2_1[$m1]['percent']=$profilepercent;
								$Carousel2_1[$m1]['profilepic']='/FO/profiles/contrib/pictures/'.$Carousel21List[$c]['identifier'].'/'.$Carousel21List[$c]['identifier'].'_h.jpg';
								$Carousel2_1[$m1]['category']=$this->getCategoryName($Carousel21List[$c]['favourite_category']);
								$Carousel2_1[$m1]['first_name']=$Carousel21List[$c]['first_name'];
								$Carousel2_1[$m1]['last_name']=$Carousel21List[$c]['last_name'];
								$Carousel2_1[$m1]['language']=$Carousel21List[$c]['language'];
								$Carousel2_1[$m1]['languagetitle']=$lang_array[$Carousel21List[$c]['language']];
									$clients=$part_obj->getcustomersPublished($Carousel21List[$c]['identifier']);
								$Carousel2_1[$m1]['client']=$clients[0]['company_name'];
								$m1++;
							}
						}
					}
					
				$this->_view->category1=$categories_array[$cat1];
				$this->_view->Carousel21List=$Carousel2_1;
			
			//Category2
				$m2=0;
				$cat2=$rand_cat[1];
				$Carousel22List=$part_obj->getCarousel2Contribs($cat2);
		
				$Carousel2_2=array();
					for($c=0;$c<count($Carousel21List) && $m2<3;$c++)
					{
						$profiledir=APP_PATH_ROOT.'profiles/contrib/pictures/'.$Carousel22List[$c]['identifier'].'/'.$Carousel22List[$c]['identifier'].'_h.jpg';
							
						if(file_exists($profiledir))
						{
							$profilepercent=$this->calculateProfilePercentage($Carousel22List[$c]['identifier']);
							
							if($profilepercent>50)
							{
								$Carousel2_2[$m2]['percent']=$profilepercent;
								$Carousel2_2[$m2]['profilepic']='/FO/profiles/contrib/pictures/'.$Carousel22List[$c]['identifier'].'/'.$Carousel22List[$c]['identifier'].'_h.jpg';
								$Carousel2_2[$m2]['category']=$this->getCategoryName($Carousel22List[$c]['favourite_category']);
								$Carousel2_2[$m2]['first_name']=$Carousel22List[$c]['first_name'];
								$Carousel2_2[$m2]['last_name']=$Carousel22List[$c]['last_name'];
								$Carousel2_2[$m2]['language']=$Carousel22List[$c]['language'];
								$Carousel2_2[$m2]['languagetitle']=$lang_array[$Carousel22List[$c]['language']];
									$clients=$part_obj->getcustomersPublished($Carousel22List[$c]['identifier']);
								$Carousel2_2[$m2]['client']=$clients[0]['company_name'];
								$m2++;
							}
						}
					}
					
				$this->_view->category2=$categories_array[$cat2];
				$this->_view->Carousel22List=$Carousel2_2;
				
		//Current Quotes
		$quoteslist=$delivery_obj->currentquotesall();
		$this->_view->quotes=$quoteslist;
		$this->_view->page_title="Edit-place premium: optimised and exclusive content written by experts";
		$this->_view->meta_desc="Writers submit their writing rates and answer the needs of e-commerce sites, media and digital agencies, publishing and media sites, blogs and SEO/ referencing agencies";
		$this->_view->keywords="writing of online content, writing of SEO articles, natural referencing";
		$this->render("Client_premium");
	}
	
	/* Function to change password. Mail will be sent after verification with reset link to reset password */
	public function changepasswordAction()
	{
		$r_email_check=$this->_request->getParams();
		$user_obj=new Ep_User_User();
		$verificationcode=$user_obj->verifyemail($r_email_check['email']);
		//echo $verificationcode;exit;
		//Mail
		if($verificationcode!="NO")
		{
			//Mail send
			$parameters['resetlink']="<a href='http://ep-test.edit-place.co.uk/client/resetpw?hash=".$verificationcode."&username=".$r_email_check['email']."'>Reset password</a>";
			$parameters['login']=$r_email_check['email'];
			$this->sendmaildirect(3,$r_email_check['email'],$parameters);	
			echo "sent";
		}
		else
			echo "no";
	}
	
	/** Function to validate reset password form **/
	public function resetpwAction()
	{
		$arr=$this->_request->getParams();	
		$user_obj=new Ep_User_User();
		$val_res=$user_obj->validateresetlink($arr['username'],$arr['hash']);
		if($val_res=="NO")
			$this->_view->error_msg="Sorry! The link has expired.";
		else
			$this->_view->error_msg="";
	
		$this->_view->hashkey=$arr['hash'];
		$this->_view->login_id=$arr['username'];
		$this->render("Client_forgotpassword");
	}
	
	/** Function to update reset password to db **/
	public function updatepasswordAction()
	{
		$arr=$this->_request->getParams();	
		$user_obj=new Ep_User_User();
		$res=$user_obj->updateUserPw($arr['user_id'],$arr['hash_key'],$arr['pw']);
		
		if($res=="YES")
		{
			//Mail send
			$parameters['login']=$arr['user_id'];
			$parameters['password']=$arr['pw'];
			$this->sendmaildirect(71,$arr['user_id'],$parameters);	
			echo "reset";			
		}
		exit;
	}
	
	/* Client home page which shows recent activities and all quotes list */
	public function homeAction()
	{
		
		if($this->_view->clientidentifier=="")
			$this->_redirect("/index/index");

		//redirecting to the URL accessed
			 if(isset($_SESSION['target']))
             {
                 $prevurl= $_SESSION['target'];
                 unset($_SESSION['target']);
                 $this->_redirect($prevurl);
             }

		
		$obj=new Ep_User_RecentActivities();
		$delivery_obj=new Ep_Ao_Delivery();
		$part_obj=new Ep_Ao_Participation();
		$userp_obj=new Ep_User_UserPlus();
		$poll_obj=new Ep_Poll_Poll();

		//fetching Recent activities of client
		$activitiescnt=$obj->ListRecentActivitiesCount($this->_view->clientidentifier);
		$activities=$obj->ListRecentActivities(0,$this->_view->clientidentifier);

			for($r=0;$r<count($activities);$r++)
			{
				if($activities[$r]['usertype']=="client")
					$activities[$r]['profilepic']=$this->getPicPath($activities[$r]['activity_by'],'home');
				elseif($activities[$r]['usertype']=="contributor")	
					$activities[$r]['profilepic']=$this->getContribpic($activities[$r]['activity_by'],'home');
					
				//Time
				if($activities[$r]['minutediff']<=60)
					$activities[$r]['time']=$activities[$r]['minutediff'].' min(s) ago';
				elseif($activities[$r]['hourdiff']<=24)
					$activities[$r]['time']=$activities[$r]['hourdiff'].' hour(s) ago';
				else
					$activities[$r]['time']=date("d/m/Y H:i:s",strtotime($activities[$r]['created_at']));
					
				$activities[$r]['contribname']=$userp_obj->getUsername($activities[$r]['user_id']);
					
			}
		$this->_view->activities=$activities;
		$this->_view->activitiescount=$activitiescnt;
		$this->_view->show_more=10;
		$this->_view->now_showing=10;
		$this->_view->total_activities=$activitiescnt;
		
		//My Quotes
		$myquotescnt=$delivery_obj->listquotescount($this->_view->clientidentifier,'new');
		$myquotes=$delivery_obj->listquotes(0,$this->_view->clientidentifier,'new');
		
		$this->_view->myquotes=$myquotes;
		$this->_view->myquotescount=$myquotescnt;
		$this->_view->myquotes_more=10;
		$this->_view->myquotes_showing=10;
		$this->_view->total_myquotes=$myquotescnt;
		
		//Ongoing quotes
		$ongoingquotescnt=$delivery_obj->listquotescount($this->_view->clientidentifier,'ongoing');
		$ongoingquotes=$delivery_obj->listquotes(0,$this->_view->clientidentifier,'ongoing');
		
		$this->_view->ongoingquotes=$ongoingquotes;
		$this->_view->ongoingquotescount=$ongoingquotescnt;
		$this->_view->ongoingquotes_more=10;
		$this->_view->ongoingquotes_showing=10;
		$this->_view->total_ongoingquotes=$ongoingquotescnt;
		
		//Published quotes
		$publishedquotescnt=$delivery_obj->listquotescount($this->_view->clientidentifier,'published');
		$publishedquotes=$delivery_obj->listquotes(0,$this->_view->clientidentifier,'published');
		
		$this->_view->publishedquotes=$publishedquotes;
		$this->_view->publishedquotescount=$publishedquotescnt;
		$this->_view->publishedquotes_more=10;
		$this->_view->publishedquotes_showing=10;
		$this->_view->total_publishedquotes=$publishedquotescnt;
		
		//Closed quotes
		$closedquotescnt=$delivery_obj->listquotescount($this->_view->clientidentifier,'closed');
		$closedquotes=$delivery_obj->listquotes(0,$this->_view->clientidentifier,'closed');
		
		$this->_view->closedquotes=$closedquotes;
		$this->_view->closedquotescount=$closedquotescnt;
		$this->_view->closedquotes_more=10;
		$this->_view->closedquotes_showing=10;
		$this->_view->total_closedquotes=$closedquotescnt;
		
		//Current Polls
		$polllist=$poll_obj->ListPoll($this->_view->clientidentifier);
		$this->_view->polllist=$polllist;
		
		//Writers worked
		$writers=$part_obj->clientWriters($this->_view->clientidentifier);
	 
		for($w=0;$w<count($writers);$w++)
		{	
			$writers[$w]['name']=strtolower($writers[$w]['first_name']).'&nbsp;'.ucfirst(substr($writers[$w]['last_name'],0,1));
			$writers[$w]['profileimage']=$this->getContribpic($writers[$w]['user_id'],'profile');
		}
		
		$this->_view->writers=$writers;
		$this->_view->writerscount=count($writers);
		$this->_view->page_title="Edit-place : Client area";
		$this->_view->render("Client_home");
	}

	/* To load more recent activities in client home page */
	public function loadmoreactivitiesAction()
	{
		if($this->_view->clientidentifier=="")
			echo "expired";
		else
		{	
			$obj=new Ep_User_RecentActivities();
			$userp_obj=new Ep_User_UserPlus();
			$params2=$this->_request->getParams();
		
			$activitiescnt=$obj->ListRecentActivitiesCount($this->_view->clientidentifier);
			$activities=$obj->ListRecentActivities($params2['now_showing'],$this->_view->clientidentifier);
		
			$moreactivity='';	
				for($r=0;$r<count($activities);$r++)
				{
					if($activities[$r]['usertype']=="client")
						$activities[$r]['profilepic']=$this->getPicPath($activities[$r]['activity_by'],'home');
					elseif($activities[$r]['usertype']=="contributor")	
						$activities[$r]['profilepic']=$this->getContribpic($activities[$r]['activity_by'],'home');
						
					//Time
					if($activities[$r]['minutediff']<=60)
						$activities[$r]['time']=$activities[$r]['minutediff'].' min(s) ago';
					elseif($activities[$r]['hourdiff']<=24)
						$activities[$r]['time']=$activities[$r]['hourdiff'].' hour(s) ago';
					else
						$activities[$r]['time']=date("d/m/Y H:i:s",strtotime($activities[$r]['created_at']));

					if($activities[$r]['premium_option']=="0")
						$prem="freedom";
					else
						$prem="premium";
					
					$activities[$r]['contribname']=$userp_obj->getUsername($activities[$r]['user_id']);
					
					//Logos based on user type
					if($activities[$r]['type']=="bopublish")
					{
						$moreactivity.='<li class="media editplace-write"><a class="pull-left">
								<img class="media-object" src="/FO/images/ep-feed-logo_90x90.png" >
							</a>';
					}
					else
					{
						if($activities[$r]['usertype']=="contributor")
						{
							$moreactivity.='<li class="media">
							<a class="pull-left imgframe" role="button" onclick="loadcontribprofile('.$activities[$r]['activity_by'].');" data-toggle="modal" data-target="#viewContribProfile" style="cursor:pointer;">
								<img class="media-object" src="'.$activities[$r]['profilepic'].'" >
							</a>';
						}
						else
						{
							$moreactivity.='<li class="media client">
							<a class="pull-left imgframe"  data-toggle="modal" data-target="#viewContribProfile">
								<div class="media-object container-logo">
									<img class="max" src="'.$activities[$r]['profilepic'].'">
								</div>
							</a>';
						}						
					}	
					$moreactivity.='<div class="media-body">
							<h4 class="media-heading">';
						if($activities[$r]['usertype']=="contributor")
						{	
							$moreactivity.='<a onclick="loadcontribprofile('.$activities[$r]['activity_by'].');" role="button" data-toggle="modal" data-target="#viewContribProfile" style="cursor:pointer;">';
						}
						else
						{
							$moreactivity.='<a data-toggle="modal" data-target="#viewContribProfile">';
						}	
						
						if($activities[$r]['type']=="bopublish")	
							$moreactivity.='Edit-place';
						else
						{
							if($activities[$r]['first_name']!="")
								$moreactivity.=utf8_encode($activities[$r]['first_name']);
							else
								$moreactivity.=$activities[$r]['email'];
						}
						
					$moreactivity.='</a>
							</h4>';
					
					//action sentences based on type of action
					if($activities[$r]['type']=="download")
						$moreactivity.='downloaded items from the mission '.$prem.' "<a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.$activities[$r]['atitle'].'</a>"';
					elseif($activities[$r]['type']=="invoice")
						$moreactivity.='generated an invoice on the mission '.$prem.' "<a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.$activities[$r]['atitle'].'</a>"';
					elseif($activities[$r]['type']=="validate")
						$moreactivity.='validated delivery "'.$activities[$r]['contribname'].'" on the mission '.$prem.' "<a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.utf8_encode($activities[$r]['atitle']).'</a>"';
					elseif($activities[$r]['type']=="comment")	
						$moreactivity.='commented mission '.$prem.' "<a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.utf8_encode($activities[$r]['atitle']).'</a>"';
					elseif($activities[$r]['type']=="extend")	
						$moreactivity.='granted more time "'.$activities[$r]['contribname'].'" on the mission '.$prem.' "<a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.utf8_encode($activities[$r]['atitle']).'</a>"';
					elseif($activities[$r]['type']=="resubmit")	
						$moreactivity.='requested recovery "'.$activities[$r]['contribname'].'" on the mission '.$prem.' "<a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.utf8_encode($activities[$r]['atitle']).'</a>"';
					elseif($activities[$r]['type']=="refuse")	
						$moreactivity.='refused delivery "'.$activities[$r]['contribname'].'" on the mission '.$prem.' "<a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.utf8_encode($activities[$r]['atitle']).'</a>"';
					elseif($activities[$r]['type']=="sentarticle")	
						$moreactivity.='sent the item of mission "'.$activities[$r]['contribname'].'" on the mission '.$prem.' "<a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.utf8_encode($activities[$r]['atitle']).'</a>"';
					elseif($activities[$r]['type']=="quote")	
						$moreactivity.='has offered a rate for the mission '.$prem.'  "<a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.utf8_encode($activities[$r]['atitle']).'</a>"';
					elseif($activities[$r]['type']=="pollquote")	
						$moreactivity.='proposed a tariff for premium quotes <a class="btn-link" href="/client/devispremium?id='.$activities[$r]['pollid'].'">'.utf8_encode($activities[$r]['polltitle']).'</a>"';
					elseif($activities[$r]['type']=="bopublish")	
						$moreactivity.='has sent the article(s) for the mission '.$prem.' <a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">'.utf8_encode($activities[$r]['atitle']).'</a>"';
						
					$moreactivity.='<p class="muted">'.$activities[$r]['time'].'</p>';
					
					if($activities[$r]['type']=="comment")
					{
						$moreactivity.='<div class="media comment">
								<i class="icon-comment"></i>
									'.utf8_encode($activities[$r]['comments']).'
								<p><a class="btn-link" href="/client/quotes?id='.$activities[$r]['article_id'].'">View All</a></p>
							</div>';
					}
					
			$moreactivity.='</div>
					</li>';	
				}
			echo $moreactivity;	
		}
	}
	
	/* Loading more quotes in Client home page on click on load more*/
	public function loadmoremyquotesAction()
	{
		if($this->_view->clientidentifier=="")
			echo "expired";
		else
		{	
			$delivery_obj=new Ep_Ao_Delivery();
			$params2=$this->_request->getParams();
			
			if($params2['quotetype']=='ongoing')
				$nextcount=$params2['ongoingquotes_showing'];
			elseif($params2['quotetype']=='published')
				$nextcount=$params2['publishedquotes_showing'];
			elseif($params2['quotetype']=='closed')
				$nextcount=$params2['closedquotes_showing'];	
			else
				$nextcount=$params2['myquotes_showing'];	
			
			$quotescnt=$delivery_obj->listquotescount($this->_view->clientidentifier,$params2['quotetype']);
			$quotes=$delivery_obj->listquotes($nextcount,$this->_view->clientidentifier,$params2['quotetype']);
		
			$morequotes='';	
				if($params2['quotetype']=='new')
				{
					for($r=0;$r<count($quotes);$r++)
					{
						$morequotes.='<tr>
										<td class="countdown" id="participationtimer_'.$quotes[$r]['id'].'"></td>
										<td><a href="/client/quotes?id='.$quotes[$r]['id'].'">'.utf8_encode($quotes[$r]['title']).'</a></td> 
									</tr>
									<input type="hidden" name="participationtime_'.$quotes[$r]['id'].'" id="participationtime_'.$quotes[$r]['id'].'" value="'.$quotes[$r]['participation_expires'].'" />';
					}
				}
				elseif($params2['quotetype']=='ongoing')
				{
					for($r=0;$r<count($quotes);$r++)
					{
						$morequotes.='<tr>
										<td class="countdown" id="submittimer_'.$quotes[$r]['id'].'"></td>
										<td><a href="/client/quotes?id='.$quotes[$r]['id'].'">'.utf8_encode($quotes[$r]['title']).'</a></td> 
									</tr>
									<input type="hidden" name="submittime_'.$quotes[$r]['id'].'" id="submittime_'.$quotes[$r]['id'].'" value="'.$quotes[$r]['article_submit_expires'].'" />';
					}
				}
				elseif($params2['quotetype']=='published')
				{
					for($r=0;$r<count($quotes);$r++)
					{
						$morequotes.='<tr>
										<td><a href="/client/quotes?id='.$quotes[$r]['id'].'">'.utf8_encode($quotes[$r]['title']).'</a></td> 
										<td><a class="btn btn-small" href="/client/downloadarticlezip?id='.$quotes[$r]['id'].'">T�l�charger</a></td>
									</tr>
									';
					}
				}
				else
				{
					for($r=0;$r<count($quotes);$r++)
					{
						$morequotes.='<tr>
										<td class="countdown"></td>
										<td><a href="/client/quotes?id='.$quotes[$r]['id'].'">'.utf8_encode($quotes[$r]['title']).'</a></td> 
									</tr>';
					}
				}				
			echo $morequotes;	
		}
	}
	
	//Function to Logout of client account
	public function logoutAction()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
		Zend_Session::destroy('EP_Client');
		$this->_redirect("/index/index");
	}

	/* Function for login validation*/
	public function checkloginuseremailAction()
	{
		$user_params=$this->_request->getParams();
		$user_obj = new Ep_User_User();	
		$res=$user_obj->checkClientMailidLogin($user_params['login_name'],$user_params['login_password']);
		
		if($res=="NO")
			echo "false";
		else
			echo "true";
	}

	/* Function for login validation*/
	public function checknewuseremailAction()
	{
		if($this->_view->clientidentifier=="")
		{
			$emailcheck_params_duplicate=$this->_request->getParams();
			$obj = new Ep_User_User();	
			$res= $obj->checkClientMailid($emailcheck_params_duplicate['email']);
			echo $res;
		}
		else
			echo "true";
		exit;		
	}
	
	/* Function for valid email validation*/
	public function checkvalidurlAction()
	{
		$url=$_REQUEST['company_url'];
		//$regex = '/(?:https?:\/\/)?(?:[a-zA-Z0-9.-]+?\.(?:com|net|org|gov|edu|mil)|\d+\.\d+\.\d+\.\d+)/';
		$regex = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
		//$regex = "#^(https?://)?.+#";
		if(preg_match($regex, $url)) 
			echo "true"; 
		else 
			echo "false"; 
	}
	
	/* Function for password validation*/
	public function checkuserpasswordAction()
	{
		$user_paramas=$this->_request->getParams();
		$obj = new Ep_User_User();	
		$res= $obj->checkClientPassword($user_paramas['current_password'],$this->_view->clientidentifier);
		echo $res;
		exit;		
	}
	
	/* Page with client profile details, to update and view*/
	public function profileAction()
	{
		//copy('https://si0.twimg.com/profile_images/2284174872/7df3h38zabcvjylnyfe3_bigger.png', '/home/sites/site7/web/FO/images/twitter.png');
		
		if($this->_view->clientidentifier=="")
		{
			$this_url = $_SERVER['REQUEST_URI'];	
			$this->_redirect("/index/index?return_url=".urlencode($this_url));
		}
		$user_obj=new Ep_User_User();
		$userp_obj=new Ep_User_UserPlus();
		$client_obj=new Ep_User_Client();
		$delivery_obj=new Ep_Ao_Delivery();
		$part_obj=new Ep_Ao_Participation();
		
		//User connexion modify to change password
			if($_REQUEST['submit_user']!="")
			{
				$user_obj->Updatepassword($_REQUEST['new_password'],$this->_view->clientidentifier);
				//Mail send
				$parameters['password']=$_REQUEST['new_password'];
				$parameters['login']=$this->_view->client_email;
				$this->sendmaildirect(71,$this->_view->client_email,$parameters);	
				$this->_redirect("/client/profile");
			}
			
		//updating db tables User,UserPlus,Client
		if($_POST['first_name']!="")
		{
			$user_params=$this->_request->getParams(); 
			
			//update User
			$Usarray=array("alert_subscribe" => $user_params['alert_subscribe']);
			$UsWhere=" identifier='".$this->_view->clientidentifier."'";
			$user_obj->updateUser($Usarray,$UsWhere);
			
			//update UserPlus
			$Uarray=array("first_name" => $user_params['first_name'],"last_name" => $user_params['last_name'], "address" => $user_params['address'], "city" => $user_params['city'], "zipcode" => $user_params['zipcode'], "country" => $user_params['country'], "phone_number" => $user_params['phone_number'] );
			$userp_obj->updateUserplus($Uarray,$this->_view->clientidentifier);
			
			//Update client
			$Carray=array("company_name" =>$user_params['company_name'],"rcs" =>$user_params['rcs'],"vat" =>$user_params['vat'],"fax_number" =>$user_params['fax_number'],"logotype" =>$user_params['logotype']);
			$client_obj->updateClient($Carray,$this->_view->clientidentifier);
			
			if($_POST['frompage']=='payment')
				$this->_redirect("/client/order3?id=".$_POST['article']);
			if($_POST['frompage']=='order4')
				$this->_redirect("/client/order4?id=".$_POST['article']);
		}
		
		$user_details=$user_obj->getClientdetails($this->_view->clientidentifier);
		$user_details[0]['logopath']='/FO/profiles/clients/logos/'.$this->_view->clientidentifier.'/'.$this->_view->clientidentifier.'_global.png'; 
		
		if($user_details[0]['country']=="")
			$user_details[0]['country']="156";
		$this->_view->user_details=$user_details;
		
		$country_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);
		$this->_view->country_array=$country_array;
		
		//Current Quotes
		$quoteslist=$delivery_obj->currentquotes($this->_view->clientidentifier);
		$quotes=array();
		$n=0;
		for($q=0;$q<count($quoteslist);$q++)
		{
			if($quoteslist[$q]['publish']=="NO")
			{
				$quotes[$n]['id']=$quoteslist[$q]['id'];
				$quotes[$n]['title']=$quoteslist[$q]['title'];
				$quotes[$n]['valid']=$quoteslist[$q]['valid'];
				$quotes[$n]['partcount']=$quoteslist[$q]['partcount'];
				$quotes[$n]['participations']=$quoteslist[$q]['participations'];
				$n++;
			}
		}
		
		$this->_view->quotes=$quotes;
	
		//Writers worked
		$writers=$part_obj->clientWriters($this->_view->clientidentifier);
	 
		for($w=0;$w<count($writers);$w++)
		{	
			$writers[$w]['name']=strtolower($writers[$w]['first_name']).'&nbsp;'.ucfirst(substr($writers[$w]['last_name'],0,1));
			$writers[$w]['profileimage']=$this->getContribpic($writers[$w]['user_id'],'profile');
		}
		
		$this->_view->writers=$writers;
		$this->_view->writerscount=count($writers);
		
		$this->_view->page_title="edit-place : Espace client";
		$this->_view->render("Client_profileedit");
	}
	
	/* Uploading client logo to the server*/
	public function uploadclientlogoAction()
	{  
		$realfilename=$_FILES['uploadfile']['name'];
		$ext=$this->findexts($realfilename);
		
			if($this->_view->clientidentifier=="")
			{	//without client login
				$client_id=mt_rand(1, 99999);
				$profiledir='clientprofile_temp/templogo/';
				$uploaddir=APP_PATH_ROOT.'clientprofile_temp/templogo/';
				$newfilename=$client_id.".".$ext;
					
				if(!is_dir($uploaddir))
					{   
						mkdir($uploaddir,0777);
						chmod($uploaddir,0777);
					}

				$file = $uploaddir.$client_id.".jpg"; 
				$client_picture_crop= $uploaddir.$client_id."_crop.jpg";
				list($width, $height)  = getimagesize($_FILES['uploadfile']['tmp_name']);

				if($width>=150 && $height>=60)
				{
					if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file))
					{
						$newimage_crop= new EP_Contrib_Image();
						$newimage_crop->load($file);
						list($width, $height) = getimagesize($file);
						if($width>400)
							$newimage_crop->resizeToWidth(400);
						elseif($height>160)
							$newimage_crop->resizeToHeight(160);
						else
							$newimage_crop->resize($width,$height);
						$newimage_crop->save($client_picture_crop);
						chmod($client_picture_crop,0777);

						$array=array("status"=>"success","identifier"=>$client_id,"path"=>$profiledir,"ext"=>"jpg");
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
			else
			{	//with client login
				$client_id=$this->_view->clientidentifier;
				$this->_view->client_id=$this->_view->clientidentifier;
				$profiledir='profiles/clients/logos/'.$client_id.'/';
				$uploaddir = APP_PATH_ROOT.'profiles/clients/logos/'.$client_id.'/'; 
				$newfilename=$client_id.".".$ext;
				$clntid=$this->_view->clientidentifier;

					if(!is_dir($uploaddir))
					{   
						mkdir($uploaddir,0777);
						chmod($uploaddir,0777);
					}

				$file = $uploaddir.$client_id.".jpg"; 
				$client_picture_crop= $uploaddir.$client_id."_crop.jpg";
				list($width, $height)  = getimagesize($_FILES['uploadfile']['tmp_name']);

				if($width>=150 && $height>=60)
				{
					if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file))
					{
						$newimage_crop= new EP_Contrib_Image();
						$newimage_crop->load($file);
						list($width, $height) = getimagesize($file);
						if($width>400)
							$newimage_crop->resizeToWidth(400);
						elseif($height>160)
							$newimage_crop->resizeToHeight(160);
						else
							$newimage_crop->resize($width,$height);
						$newimage_crop->save($client_picture_crop);
						chmod($client_picture_crop,0777);

						$array=array("status"=>"success","identifier"=>$clntid,"path"=>$profiledir,"ext"=>"jpg");
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
		
	}
    
	/* Uploading client logo to the server*/
	public function uploadclientgloballogoAction()
	{
		$realfilename=$_FILES['uploadfile']['name'];
		$ext=$this->findexts($realfilename);
		
			if($this->_view->clientidentifier=="")
			{
				//when client logo is uploaded from quotes creation without login
				//logo is saved in temporary folder
				$client_id=mt_rand(1, 99999);
				$profiledir='clientprofile_temp/templogo/';
				$uploaddir=APP_PATH_ROOT.'clientprofile_temp/templogo/';
					
				if(!is_dir($uploaddir))
					{   
						mkdir($uploaddir,0777);
						chmod($uploaddir,0777);
					}

				$file = $uploaddir.$client_id.".png"; 
				$file_global1= $uploaddir.$client_id."_global.png";
				$file_global2= $uploaddir.$client_id."_global1.png";
				
				list($width, $height)  = getimagesize($_FILES['uploadfile']['tmp_name']);

				if($width>=90 || $height>=90)
				{
					if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file))
					{
						//73
						$newimage_crop= new EP_Contrib_Image();
						$newimage_crop->load($file);
						list($width, $height) = getimagesize($file);
						if($width>$height)
							$newimage_crop->resizeToWidth(73);
						elseif($height>$width)
							$newimage_crop->resizeToHeight(73);
						else
							$newimage_crop->resize(73,73);
						
						$newimage_crop->save($file_global1);
						chmod($file_global1,0777);

						$array=array("status"=>"success","identifier"=>$client_id,"path"=>$profiledir,"ext"=>"png");
						echo json_encode($array);
						
						//90
						$newimage_crop1= new EP_Contrib_Image();
						$newimage_crop1->load($file);
						list($width, $height) = getimagesize($file);
						if($width>$height)
							$newimage_crop1->resizeToWidth(90);
						elseif($height>$width)
							$newimage_crop1->resizeToHeight(90);
						else
							$newimage_crop1->resize(90,90);
						
						$newimage_crop1->save($file_global2);
						chmod($file_global2,0777);
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
			else
			{
				//when client logo is uploaded from quotes creation after login
				//logo is saved in profiles/clients/logos folder
				$client_id=$this->_view->clientidentifier;
				$this->_view->client_id=$this->_view->clientidentifier;
				$profiledir='profiles/clients/logos/'.$client_id.'/';
				$uploaddir = APP_PATH_ROOT.'profiles/clients/logos/'.$client_id.'/'; 
				
				$clntid=$this->_view->clientidentifier;

					if(!is_dir($uploaddir))
					{   
						mkdir($uploaddir,0777);
						chmod($uploaddir,0777);
					}

				$file = $uploaddir.$client_id.".png"; 
				$file_global1= $uploaddir.$client_id."_global.png";
				$file_global2= $uploaddir.$client_id."_global1.png";
				list($width, $height)  = getimagesize($_FILES['uploadfile']['tmp_name']);

				if($width>=90 || $height>=90)
				{
					if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file))
					{
						$newimage_crop= new EP_Contrib_Image();
						$newimage_crop->load($file);
						list($width, $height) = getimagesize($file);
						if($width>$height)
							$newimage_crop->resizeToWidth(73);
						elseif($height>$width)
							$newimage_crop->resizeToHeight(73);
						else
							$newimage_crop->resize(73,73);
							
						$newimage_crop->save($file_global1);
						chmod($file_global1,0777);
						
						//90
						$newimage_crop1= new EP_Contrib_Image();
						$newimage_crop1->load($file);
						list($width, $height) = getimagesize($file);
						if($width>$height)
							$newimage_crop1->resizeToWidth(90);
						elseif($height>$width)
							$newimage_crop1->resizeToHeight(90);
						else
							$newimage_crop1->resize(90,90);
						
						$newimage_crop1->save($file_global2);
						chmod($file_global2,0777);
						
						//Update client logotype
						/*$client_obj=new Ep_User_Client();
						$Carray=array("logotype" =>'file',"twitterid"=>'');
						$client_obj->updateClient($Carray,$this->_view->clientidentifier);*/
						
						$array=array("status"=>"success","identifier"=>$clntid,"path"=>$profiledir,"ext"=>"png");
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
	}
	
	/* not in use*/
	/**Cropping client logo - from My profile**/
    public function cropprofilepicAction()
    { 
		if($this->_request-> isPost())
		{
			$image_params=$this->_request->getParams();
            $function=$image_params['func'];
            $new_x=$image_params['x'];
            $new_y=$image_params['y'];
            $post_width=$image_params['w'];
            $post_height=$image_params['h'];
	
			$client_id=$this->_view->clientidentifier;
			$ext="jpg";
			$app_path=APP_PATH_ROOT;
			$profiledir='profiles/clients/logos/'.$client_id.'/';
			$uploaddir = APP_PATH_ROOT.'profiles/clients/logos/'.$client_id.'/';

			$file = $uploaddir.$client_id.".jpg";
			$file_home=$uploaddir.$client_id."_h.jpg";
			$file_profile=$uploaddir.$client_id."_p.jpg";
			$file_ao_search=$uploaddir.$client_id."_ao.jpg";
			$file_index=$uploaddir.$client_id."_m.jpg";
			$file_73=$uploaddir.$client_id."_global.jpg";
			$client_picture_crop= $uploaddir.$client_id."_crop.jpg";  

		   if($function=="saveimage")
		   {
			 /*Cleint home image with 60x60**/
				$newimage_h= new EP_Contrib_Image();
				$newimage_h->load($client_picture_crop);
				$newimage_h->cropImage($new_x,$new_y,60,60,$post_width,$post_height);
				$newimage_h->save($file_home);
				unset($newimage_h);

			   /*Client Profile image with 90x90**/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($client_picture_crop);
				$newimage_p->cropImage($new_x,$new_y,90,90,$post_width,$post_height);
				$newimage_p->save($file_profile);
				//chmod($file_profile,777);
				unset($newimage_p);

				/*Client logo 73 X 73 **/
				/*$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($client_picture_crop);
				$newimage_p->cropImage($new_x,$new_y,73,73,$post_width,$post_height);
				$newimage_p->save($file_73);
				unset($newimage_p);*/
				
				/*Client ao search with width 90**/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($client_picture_crop);
				if($post_width>$post_height)
				{
					$crop_x=73;
					$crop_y=$crop_x*($post_height/$post_width);
				}
				else
				{
					$crop_y=73;
					$crop_x=$crop_y*($post_width/$post_height);
				}				
				$newimage_p->cropImage($new_x,$new_y,$crop_x,$crop_y,$post_width,$post_height);
				$newimage_p->save($file_ao_search);
				unset($newimage_p);
				
				//test 73 by 73
				$src = imagecreatefromjpeg($file_ao_search);
				$dest = imagecreatefromjpeg(APP_PATH_ROOT.'/images/bgrectangle.jpg');

				// Copy and merge
				//list($sh,$sw)=getimagesize($file_ao_search);
				$x=73-$crop_x;
				$y=73-$crop_y;
				imagecopymerge($dest, $src, $x/2, $y/2, 0, 0, $crop_x, $crop_y, 75);

				imagejpeg($dest, $uploaddir.$client_id.'_global.jpg');
				//imagedestroy($dest);
				//imagedestroy($src);
				
				//Update client logotype
				$client_obj=new Ep_User_Client();
				$Carray=array("logotype" =>'file');
				$client_obj->updateClient($Carray,$this->_view->clientidentifier);
			 } 
			 elseif($function=="original")
			 {
				/*Cleint home image with 60x60**/
				$newimage_h= new EP_Contrib_Image();
				$newimage_h->load($file);
				$newimage_h->resize(60,60);
				$newimage_h->save($file_home);
				unset($newimage_h);

			   /*Client Profile image with 90x90**/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($file);
				$newimage_p->resize(90,90);
				$newimage_p->save($file_profile);
				//chmod($file_profile,777);
				unset($newimage_p);

				/*Client logo 73 X 73 **/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($file);
				$newimage_p->resize(73,73);
				$newimage_p->save($file_73);
				unset($newimage_p);
				
				//Update client logotype
				$client_obj=new Ep_User_Client();
				$Carray=array("logotype" =>'file');
				$client_obj->updateClient($Carray,$this->_view->clientidentifier);
			 }
            /**Unlink the Original file**/
            if(file_exists($file) && !is_dir($file))
                unlink($file);

            $array=array("identifier"=>$client_id,"path"=>$profiledir,"ext"=>$ext);
            echo json_encode($array);
		}
    }
	
	/* not in use*/
	/**Cropping client logo - from My quotes creation**/
	public function cropprofilepicliberteAction()
	{
		if($this->_request-> isPost())
		{
			$image_params=$this->_request->getParams();
            $function=$image_params['func'];
            $new_x=$image_params['x'];
            $new_y=$image_params['y'];
            $post_width=$image_params['w'];
            $post_height=$image_params['h'];
	
			$client_id=$image_params['logoidentifier'];
			$ext="jpg";
			$app_path=APP_PATH_ROOT;
			$profiledir='clientprofile_temp/templogo/';
			$uploaddir=APP_PATH_ROOT.'clientprofile_temp/templogo/';

			$file = $uploaddir.$client_id.".jpg";
			$file_home=$uploaddir.$client_id."_h.jpg";
			$file_profile=$uploaddir.$client_id."_p.jpg";
			$file_ao_search=$uploaddir.$client_id."_ao.jpg";
			$file_index=$uploaddir.$client_id."_m.jpg";
			$file_73=$uploaddir.$client_id."_global.jpg";
			$client_picture_crop= $uploaddir.$client_id."_crop.jpg";  

		   if($function=="saveimage")
		   {
			 /*Cleint home image with 60x60**/
				$newimage_h= new EP_Contrib_Image();
				$newimage_h->load($client_picture_crop);
				$newimage_h->cropImage($new_x,$new_y,60,60,$post_width,$post_height);
				$newimage_h->save($file_home);
				unset($newimage_h);

			   /*Client Profile image with 90x90**/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($client_picture_crop);
				$newimage_p->cropImage($new_x,$new_y,90,90,$post_width,$post_height);
				$newimage_p->save($file_profile);
				//chmod($file_profile,777);
				unset($newimage_p);

				/*Client Profile image with 150x60**/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($client_picture_crop);
				$newimage_p->cropImage($new_x,$new_y,80,50,$post_width,$post_height);
				$newimage_p->save($file_index);
				unset($newimage_p);


				/*Client ao search with width 90**/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($client_picture_crop);
				$newimage_p->cropImage($new_x,$new_y,80,24,$post_width,$post_height);
				$newimage_p->save($file_ao_search);
				unset($newimage_p);
				
				/*Client logo 73 X 73 **/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($client_picture_crop);
				$newimage_p->cropImage($new_x,$new_y,73,73,$post_width,$post_height);
				$newimage_p->save($file_73);
				unset($newimage_p);
			 } 
			 elseif($function=="original")
			 {
				/*Cleint home image with 60x60**/
				$newimage_h= new EP_Contrib_Image();
				$newimage_h->load($file);
				$newimage_h->resize(60,60);
				$newimage_h->save($file_home);
				unset($newimage_h);

			   /*Client Profile image with 90x90**/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($file);
				$newimage_p->resize(90,90);
				$newimage_p->save($file_profile);
				//chmod($file_profile,777);
				unset($newimage_p);

				/*Client Profile image with 150x60**/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($file);
				$newimage_p->resize(80,50);
				$newimage_p->save($file_index);
				unset($newimage_p);


				/*Client ao search with width 90**/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($file);
				$newimage_p->resize(80,24);
				$newimage_p->save($file_ao_search);
				unset($newimage_p);
				
				/*Client logo 73 X 73 **/
				$newimage_p= new EP_Contrib_Image();
				$newimage_p->load($file);
				$newimage_p->resize(73,73);
				$newimage_p->save($file_73);
				unset($newimage_p);
			}
            /**Unlink the Original file**/
            if(file_exists($file) && !is_dir($file))
                unlink($file);

            $array=array("identifier"=>$client_id,"path"=>$profiledir,"ext"=>$ext);
            echo json_encode($array);
		}
	}
	
	/* not in use*/
	public function twitterlogoAction()
	{
		if($_REQUEST['twitter']!="")
		{
			if($this->_view->clientidentifier=="")
			{
				$clientid=$_REQUEST['twitter'];
				$type="temp";
				$path=APP_PATH_ROOT.'clientprofile_temp/'.date("Y-m-d").'/';
			}
			else
			{
				$clientid=$this->_view->clientidentifier;
				$type="client";
				$path=APP_PATH_ROOT.'profiles/clients/logos/'.$this->_view->clientidentifier.'/';
			}
			if(!is_dir($path))
			{   
				mkdir($path,0777);
				chmod($path,0777);
			}
			
			//profile image 
			$twtfile=$path.$clientid.".jpg";
			$twtsitefile="http://api.twitter.com/1/users/profile_image?screen_name=".$_REQUEST['twitter']."&size=bigger";
			$file_headers = @get_headers($twtsitefile);
			
			if($file_headers[0] == 'HTTP/1.0 404 Not Found')
			{
				echo "nofile";
			}
			else
			{
				if($this->_view->clientidentifier!="")
				{
					//Update client logotype
					$client_obj=new Ep_User_Client();
					$Carray=array("logotype" =>'twt',"twitterid"=>$_REQUEST['twitter']);
					$client_obj->updateClient($Carray,$this->_view->clientidentifier);
				}
			
			$content_p = file_get_contents($twtsitefile);
			$fp_p = fopen($twtfile, "w");
			chmod($twtfile, 0777);
			fwrite($fp_p, $content_p);
			fclose($fp_p);
			
				/*// Resize to 90 X 90
				$resize_p= new EP_Contrib_Image();
				$resize_p->load($twtfile);
				$resize_p->resize(90,90);
				$resize_p->save($path.$clientid."_p.jpg");
				
				// Resize to 60 X 60
				$resize_h= new EP_Contrib_Image();
				$resize_h->load($twtfile);
				$resize_h->resize(60,60);
				$resize_h->save($path.$clientid."_h.jpg");*/
				
				// Resize to 73 X 73
				$resize_73= new EP_Contrib_Image();
				$resize_73->load($twtfile);
				$resize_73->resize(73,73);
				$resize_73->save($path.$clientid."_global.jpg");
				
				if(file_exists($twtfile) && !is_dir($twtfile))
					unlink($twtfile);
				
				echo $clientid."#".$type;
				exit;
			}
			
				
		}
	}
	
	/* Function to get participation limit of writer based on his profile type */
	public function getparticipationlimitAction()
	{
		$params=$this->_request->getParams();
		
		if($params['option']=="day")
			$part_time=date("Y-m-d H:i", strtotime('+'.$params['value'].' days'));
		elseif($params['option']=="hour")
			$part_time=date("Y-m-d H:i", strtotime('+'.$params['value'].' hour'));
		elseif($params['option']=="min")
			$part_time=date("Y-m-d H:i", strtotime('+'.$params['value'].' minute'));	
		
		$date=date("Y-m-d H:i:s", strtotime($part_time));
		$timepart=strtotime($date);
		setlocale(LC_TIME, "en_US");
		echo date("jS F h:i A", ($timepart+(12*3600)));	
		exit;
			
	}
	/* not in use - old quote creation page*/
	public function liberte1Action()
	{ 	//echo substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		//chmod('/home/sites/site7/web/FO/spec_temp/2013-01-18/',0777);
		//exec('rm -rf /home/sites/site7/web/FO/spec_temp/2013-01-18/');
		$this->EP_Client = Zend_Registry::get('EP_Client');
		
		$delivery_obj=new Ep_Ao_Delivery();
		$client_obj=new Ep_User_Client();
		$client_vals=$client_obj->getClientdetails($this->_view->clientidentifier);
		if($client_vals[0]['contrib_percentage']!="")
			$contribper=$client_vals[0]['contrib_percentage'];
		else
			$contribper=$this->getConfiguredval('nopremium_contribpercent');
		$this->_view->eppercent=100-$contribper;
		
		//Pre-loading values if creating duplicate mission
		if($_REQUEST['duplicate_mission']!="")
		{
			$deliverydetails=$delivery_obj->Deliverydetails($_REQUEST['editliberte']);
			
			$this->_view->title=$deliverydetails[0]['title'];
			
			
			//participation time
			if($deliverydetails[0]['participation_time']>=60 && $deliverydetails[0]['participation_time']<1440)
			{
				$this->_view->participation=$deliverydetails[0]['participation_time']/60;
				$this->_view->participation_option="hour";
			}
			elseif($deliverydetails[0]['participation_time']>=1440)			
			{
				$this->_view->participation=$deliverydetails[0]['participation_time']/(60*24);
				$this->_view->participation_option="day";
			}
			else
			{
				$this->_view->participation=$deliverydetails[0]['participation_time'];
				$this->_view->participation_option="min";
			}
			
			//submit time
			
			if($deliverydetails[0]['submit_option']=='hour')
				$this->_view->delivery=$deliverydetails[0]['senior_time']/60;
			elseif($deliverydetails[0]['submit_option']=='day')
				$this->_view->delivery=$deliverydetails[0]['senior_time']/(60*24);
			else
				$this->_view->delivery=$deliverydetails[0]['senior_time'];
			
			$this->_view->delivery_option=$deliverydetails[0]['submit_option'];
			
			$this->_view->whois=$deliverydetails[0]['client_type'];
			
			if($deliverydetails[0]['price_min']!="")
			{
				$this->_view->pricecheck="on";
				
				$this->_view->price_min_total=$deliverydetails[0]['price_min'];
				$this->_view->price_max_total=$deliverydetails[0]['price_max'];	
				
				$this->_view->price_min=($deliverydetails[0]['price_min']*$contribper)/100;
				$this->_view->price_max=($deliverydetails[0]['price_max']*$contribper)/100;	
									
				
			}
			
			//spec files
			$this->_view->brief_uploaded_files=explode("|",utf8_encode($deliverydetails[0]['file_name'])); 
			
			if($_REQUEST['createprivate']!="")
			{
				$this->_view->privatecontrib="on";
				$this->_view->selectedcontribs=array("0"=>$_REQUEST['createprivate']);  
				//Insert favourite contributor
				$fav_obj = new Ep_Ao_Favouritecontributor();
				$fav_obj->addfavcontrib($_REQUEST['createprivate'],$this->_view->clientidentifier);
			}
			else
			{
				if($deliverydetails[0]['AOtype']=="private") 
				{
					$this->_view->privatecontrib="on";
					$this->_view->selectedcontribs=explode(",",$deliverydetails[0]['contribs_list']);  
				}
				else	
					$this->_view->selectedcontribs=array();
			}
			
		}
		
		//Navigation previous page 
		
			
		if($this->EP_Client->funnel_1['title']!="")
		{
			$this->_view->title=$this->EP_Client->funnel_1['title'];
	
			
				$this->_view->participation=$this->EP_Client->funnel_1['participationtime'];
				$this->_view->participation_option=$this->EP_Client->funnel_1['participationtime_option'];
				
				if($this->EP_Client->funnel_1['participationtime_option']=="day")
					$part_time=date("Y-m-d H:i", strtotime('+'.$this->_view->participation.' days'));
				elseif($this->EP_Client->funnel_1['participationtime_option']=="hour")
					$part_time=date("Y-m-d H:i", strtotime('+'.$this->_view->participation.' hour'));
				elseif($this->EP_Client->funnel_1['participationtime_option']=="min")
					$part_time=date("Y-m-d H:i", strtotime('+'.$this->_view->participation.' minute'));
			
				//$particip=$this->getConfiguredval('participation_time');
				//$part_time=date("Y-m-d H:i", strtotime('+'.$particip.' minute'));
			
	
				$this->_view->delivery=$this->EP_Client->funnel_1['deliverytime'];
				$this->_view->delivery_option=$this->EP_Client->funnel_1['deliverytime_option'];
			
			$this->_view->whois=$this->EP_Client->funnel_1['whoIs'];
		
			if($this->EP_Client->funnel_1['whoIs']=="professional")
				$this->_view->company=$this->EP_Client->funnel_1['companyname'];
				
			$this->_view->filelist=	$this->EP_Client->funnel_1['filename'];
			$filelist=	$this->EP_Client->funnel_1['filename'];

			//Brief uploaded file listing
				$today=date('Y-m-d');
				if(count($this->EP_Client->funnel_1['filename'])>0)
				{
					foreach ($this->EP_Client->funnel_1['filename'] as $key => $value) 
					{
						$value=utf8_decode($value); 
						//echo "<br>".$value;
						if (!file_exists('/home/sites/site7/web/FO/spec_temp/'.$today.'/'.$value)) 
							unset($this->EP_Client->funnel_1['filename'][$key]);
					}
				}
				$this->_view->brief_uploaded_files=$this->EP_Client->funnel_1['filename'];
				$this->_view->today=$today;		
		
			//$this->_view->twitterid=$this->EP_Client->funnel_1['twitterid'];
			
			$this->_view->logotype=$this->EP_Client->funnel_1['logotype'];
			$this->_view->logoidentifier=$this->EP_Client->funnel_1['logoidentifier'];
			
			if($this->_view->clientidentifier=="")
			{
				if($this->EP_Client->funnel_1['logotype']=='twt')
					$this->_view->logo="/FO/clientprofile_temp/".date("Y-m-d")."/".$this->EP_Client->funnel_1['twitterid']."_global.png";
				elseif($this->EP_Client->funnel_1['logotype']=='file')
					$this->_view->logo="/FO/clientprofile_temp/templogo/".$this->EP_Client->funnel_1['logoidentifier']."_global.png";
			}
			
			//Private contributors
			$this->_view->privatecontrib=$this->EP_Client->funnel_1['privatecontrib'];
			//$this->_view->selectedcontribs=array();
			//if($this->EP_Client->funnel_1['privatecontrib']=="on")
				//$this->_view->selectedcontribs=$this->EP_Client->funnel_1['contribselect'];
		}
		else	
		{
			if($_REQUEST['duplicate_mission']=="")
			{
				$this->_view->brief_uploaded_files=array();
				$this->_view->selectedcontribs=array();
				$this->_view->privatecontrib="on";	
				$this->_view->checkedall="yes";	
			}
			
			$particip=$this->getConfiguredval('participation_time');
			$part_time=date("Y-m-d H:i", strtotime('+'.$particip.' minute'));
			
			$this->_view->company=$client_vals[0]['company_name'];
			//$this->_view->twitterid=$client_vals[0]['twitterid'];
			$this->_view->logotype=$client_vals[0]['logotype'];
			$this->_view->logo="/FO/images/customer-no-logo.png";
			
		}		
		
		if($this->_view->clientidentifier!="")
		{	
			$this->_view->company=$client_vals[0]['company_name'];
			$this->_view->logo="/FO/profiles/clients/logos/".$this->_view->clientidentifier."/".$this->_view->clientidentifier."_global.png";
		}
		
		//Sample brief block
		$brief_sample=array();
		if ($handle = opendir('/home/sites/site7/web/FO/brief_sample/')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$brief_sample[]=$entry;
				}
			}
			closedir($handle);
		}
		
		//Private contributors
		$user_obj=new Ep_Ao_Participation();
		$contribarray=array();
		$contriblist=$user_obj->ListallfavContribs($this->_view->clientidentifier);
			for($c=0;$c<count($contriblist);$c++)
			{
				if($contriblist[$c]['first_name']!="")
					$contriblist[$c]['name']=ucfirst($contriblist[$c]['first_name']).'&nbsp;'.ucfirst(substr($contriblist[$c]['last_name'],0,1));
				else
					$contriblist[$c]['name']=$contriblist[$c]['email'];
					
				$contriblist[$c]['totalparticipation']=$user_obj->participationcount($contriblist[$c]['identifier'],'total');
				$contriblist[$c]['selectedparticipation']=$user_obj->participationcount($contriblist[$c]['identifier'],'selected');	
				$contriblist[$c]['profilepic']=$this->getContribpic($contriblist[$c]['identifier'],'home');
			}
		$this->_view->contriblist=$contriblist;	
		$this->_view->editliberte=$_REQUEST['editliberte'];	
		
		$this->_view->currency=$this->getConfiguredval('currency');
		$date=date("Y-m-d H:i:s", strtotime($part_time));
		$timepart=strtotime($date);
		setlocale(LC_TIME, "en_US");
		$this->_view->participationlimit=date("jS F h:i A", ($timepart+(12*3600)));
		
		$this->_view->onlinelimit=date("jS F h:i A", strtotime('+12 hours'));
		$this->_view->currdate=date("Y-m-d");
		$this->_view->brief_sample=$brief_sample;
		$this->_view->page_title="Edit-place Freedom: Choose and work directly with a writer on your project";
		$this->_view->meta_desc="Publish your offer on Edit-place for free and receive quotes from hundreds of writers - experts";
		$this->_view->render("Client_liberte1");
	}
	
	/* not in use*/
	//Downloading spec file stored in temporary folder without login
	public function downloadtempspecAction()
	{
		$today=date('Y-m-d');
		$filename=APP_PATH_ROOT. 'spec_temp/'.$today.'/'.utf8_decode($_REQUEST['file']);
		
		$file=str_replace(" ","_",utf8_decode($_REQUEST['file']));
		//echo $file=utf8_decode($_REQUEST['file']);
		//echo $filename; // exit;
			//Download zip file
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$file);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			readfile($filename);	
	}
	
	/* not in use*/
	//Saving liberte1 
	public function saveliberte1Action()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
		
		//Storing funnel 1 values in session
		$params1=$this->_request->getParams();	
	
		$this->EP_Client->funnel_1=$params1;
		$this->EP_Client->funnel_1['title']=utf8_decode($params1['title']);
		$this->EP_Client->funnel_1['privatecontrib']="on";
		
		$this->EP_Client->funnel_1['whoIs']="professional";
		//echo $params1['client'];
		exit;
	}
	
	/* not in use*/
	public function liberte2Action()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
	
		//Storing funnel 1 values in session
		$params1=$this->_request->getParams();	
	
		if($params1['title']=="")
			$this->_redirect("/client/liberte1");
			
		if($params1['title']!="")
			$this->EP_Client->funnel_1=$params1;
//print_r($this->EP_Client->funnel_1);
		//Redirect to funnel 3 if user already logged in
		if($this->_view->clientidentifier!="")
			$this->_redirect("/client/liberte3");
		
		if($this->EP_Client->funnel_1['title']!="")
			$this->_view->navigate=1;
		 
		
			
				if($this->EP_Client->funnel_1['participationtime_option']=="day")
					$part_time=date("Y-m-d H:i", strtotime('+'.$this->EP_Client->funnel_1['participationtime'].' days'));
				elseif($this->EP_Client->funnel_1['participationtime_option']=="hour")
					$part_time=date("Y-m-d H:i", strtotime('+'.$this->EP_Client->funnel_1['participationtime'].' hour'));
				elseif($this->EP_Client->funnel_1['participationtime_option']=="min")
					$part_time=date("Y-m-d H:i", strtotime('+'.$this->EP_Client->funnel_1['participationtime'].' minute'));	
			
				//$particip=$this->getConfiguredval('participation_time');
				//$part_time=date("Y-m-d H:i", strtotime('+'.$particip.' minute'));
			
		
		$date=date("Y-m-d H:i:s", strtotime($part_time));
		$timepart=strtotime($date);
		setlocale(LC_TIME, "en_US");
		$this->_view->participationlimit=date("jS F h:i A", ($timepart+(12*3600)));
		
		$this->_view->onlinelimit=date("jS F h:i A", strtotime('+12 hours'));
		$this->_view->title=$this->EP_Client->funnel_1['title'];
		$this->_view->page_title="Edit-place FREEDOM: Create your offer and recruit a writer for your project";
		$this->_view->render("Client_liberte2");
	}

	/* not in use*/
	public function liberte3Action()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
		$user_obj = new Ep_User_User();	
		// Login or New client Register
		if($_REQUEST['register_client']!="" || $_REQUEST['loginemail']!="" )
		{
			$user_params=$this->_request->getParams();
			
			if($user_params['newuser']=="1")
			{
				$array=array();
				$array['email']=$user_params['email'];
				$array['password']=$user_params['libertepassword'];
				$array['status']="Active";
					$vcode=md5("edit-place_".$user_params['email']);
				$array['verification_code']=$vcode;
				$array['verified_status']="YES";
				$array['type']="client";
				//print_r($array);exit;
				$identifier=$user_obj->InsertUser($array);
			
				$username=$user_params['email'];
				$password=$user_params['libertepassword'];
				
				//Mail send
				$newclient=$user_params['email'];
				$parameters['sitelink']='<a href="http://ep-test.edit-place.co.uk">www.edit-place.com</a>';
				$journalcount=$user_obj->journalcount();
				$parameters['writercount']='<b>'.$journalcount.'</b>';
				
				$this->sendmaildirect(1,$newclient,$parameters);
				$this->messageToEPMail($identifier,1,$parameters);
			}
			elseif($user_params['newuser']=="0")
			{
				$username=$user_params['loginemail'];
				$password=$user_params['loginpassword'];
			}
			
			//Login
				$res=$user_obj->checkClientMailidLogin($username,$password);

				if($res!="NO")
				{
					$this->EP_Client = Zend_Registry::get('EP_Client');
					$this->EP_Client->clientidentifier =$res;
					$this->EP_Client->clientemail =$username;			
					
						//twitterlogo rename and move
							/*if($this->EP_Client->funnel_1['twitterid']!="" && $this->EP_Client->funnel_1['logotype']=='twt')
							{
								$logodir="/home/sites/site7/web/FO/profiles/clients/logos/".$this->EP_Client->clientidentifier."/";
								
								if(!is_dir($logodir))
								{
									mkdir($logodir,0777);
									chmod($logodir,0777);
								}
								
								//moving from clientprofile_temp to clients logos
								$sourceh="/home/sites/site7/web/FO/clientprofile_temp/".date("Y-m-d")."/".$this->EP_Client->funnel_1['twitterid']."_global.png";
								$desth=$logodir."/".$this->EP_Client->clientidentifier."_global.png";
								copy($sourceh,$desth);
								
								exec('rm -rf /home/sites/site7/web/FO/clientprofile_temp/2013-01-18/');
							}
							else if($this->EP_Client->funnel_1['logoidentifier']!="" && $this->EP_Client->funnel_1['logotype']=='file')
							{*/
								$logodir="/home/sites/site7/web/FO/profiles/clients/logos/".$this->EP_Client->clientidentifier."/";
								
								if(!is_dir($logodir))
								{
									mkdir($logodir,0777);
									chmod($logodir,0777);
								}
								
								//moving from templogo to clients logos
								$sourceh="/home/sites/site7/web/FO/clientprofile_temp/templogo/".$this->EP_Client->funnel_1['logoidentifier']."_global.png";
								$desth=$logodir."/".$this->EP_Client->clientidentifier."_global.png";
								copy($sourceh,$desth);
								
								$sourceh1="/home/sites/site7/web/FO/clientprofile_temp/templogo/".$this->EP_Client->funnel_1['logoidentifier']."_global1.png";
								$desth1=$logodir."/".$this->EP_Client->clientidentifier."_global1.png";
								copy($sourceh1,$desth1);
								
								exec('rm -rf /home/sites/site7/web/FO/clientprofile_temp/templogo/');
							//}
					
					$this->_redirect("/client/liberte3");
					exit;
				}
		}

		if($this->_view->clientidentifier=="" || $this->EP_Client->funnel_1['title']=="")
			$this->_redirect("/client/liberte1");

		//Db Insertion
		$delivery_obj=new Ep_Ao_Delivery();
		$article_obj=new Ep_Ao_Article();
		$pay_obj=new Ep_Ao_Payment();
		$payart_obj=new Ep_Ao_PaymentArticle();
		
		$client_obj=new Ep_User_Client();
	
			//Inserting Delivery
			$delivery_id=$delivery_obj->InsertDelivery($this->_view->clientidentifier,$this->EP_Client->funnel_1);
			//exit;
			if($delivery_id!="NO")
			{
				//Inserting Article
				$client_vals=$client_obj->getClientdetails($this->_view->clientidentifier);
				if($client_vals[0]['contrib_percentage']!="")
					$contribper=$client_vals[0]['contrib_percentage'];
				else
					$contribper=$this->getConfiguredval('nopremium_contribpercent');
				
				$article_obj->InsertArticle($delivery_id,$this->EP_Client->funnel_1,$contribper);
			
				//Updating company in Client
				if($this->EP_Client->funnel_1['companyname']!="")
				{
					$client_array=array();
					$client_array['companyname']=$this->EP_Client->funnel_1['companyname'];
					//$client_array['logotype']=$this->EP_Client->funnel_1['logotype'];
					//$client_array['twitterid']=$this->EP_Client->funnel_1['twitterid'];
					$client_obj->UpdateCompany($this->EP_Client->clientidentifier,$client_array);	
				}
				//Inserting Payment
				$pay_obj->InsertPayment($delivery_id);
				
				if($client_vals[0]['paypercent']=='0')
				{
					$payed_id=$payart_obj->insertpayedclient($this->_view->clientidentifier);
					$Aarray=array();
					$Aarray['paid_status']='paid';
					$Aarray['invoice_id']=$payed_id;
					$whereA= "delivery_id='".$delivery_id."'";
					$article_obj->updateArticle($Aarray,$whereA);
				}
				//Copy files 
				if(count($this->EP_Client->funnel_1['filename'])>0)
				{
					//spec file directory
					$copdir="/home/sites/site7/web/FO/client_spec/".$this->EP_Client->clientidentifier."/";

					//create dir if not exists
					if(!is_dir($copdir))
					{
						mkdir($copdir,0777);
						chmod($copdir,0777);
					}
					//moving spec files from temp location to client_spec
					for($f=0;$f<count($this->EP_Client->funnel_1['filename']);$f++)
					{
						$this->EP_Client->funnel_1['filename'][$f]=utf8_decode($this->EP_Client->funnel_1['filename'][$f]);
						if($this->EP_Client->funnel_1['editliberte']!="")
						{
							$source="/home/sites/site7/web/FO/client_spec/".$this->EP_Client->clientidentifier."/".$this->EP_Client->funnel_1['filename'][$f];
							if(!file_exists($source))
								$source="/home/sites/site7/web/FO/spec_temp/".date("Y")."-".date("m")."-".date("d")."/".$this->EP_Client->funnel_1['filename'][$f];
								
						}
						else
							$source="/home/sites/site7/web/FO/spec_temp/".date("Y")."-".date("m")."-".date("d")."/".$this->EP_Client->funnel_1['filename'][$f];
						$dest="/home/sites/site7/web/FO/client_spec/".$this->EP_Client->clientidentifier."/".$this->EP_Client->funnel_1['filename'][$f];
						copy($source,$dest);
					}
				}
				
				//Mail send alert
				$to=$this->getConfiguredval('aoemail_alert');
				//$parameters['link']='<a href="http://admin-ep-test-new.edit-place.com/processao/listprm?submenuId=ML2-SL1">ici</a>';
				$parameters['link']='<a href="http://admin-test.edit-place.co.uk/processao/liberte-newaos?submenuId=ML2-SL1">ici</a>';
				$parameters['client']='<b>'.$this->_view->clientname.'</b>';
				$this->sendmaildirect(70,$to,$parameters);
				
				//Mail send client
				$parameters1['AO_title']='<b>'.utf8_decode($this->EP_Client->funnel_1['title']).'</b>';
				$this->messageToEPMail($this->_view->clientidentifier,4,$parameters1);
				
				unset($this->EP_Client->funnel_1);
			}
		
		$this->_view->countributorcount=$user_obj->journalcount();
		
		//New quotes
		$this->_view->newquotes=$delivery_obj->listnewquotes($this->_view->clientidentifier);
		
		setlocale(LC_TIME, "fr_FR");
		$this->_view->onlinelimit=strftime("%d %B %H:%M", strtotime('+30 hours'));
		$this->_view->firstao=$delivery_obj->checkfirstao($this->_view->clientidentifier);
		$this->_view->page_title="Edit-place FREEDOM: Create your offer and recruit a writer for your project";
		$this->_view->render("Client_liberte3");
	}

	/* Function to brief sample files - right side block*/
	public function briefsampleAction()
	{ 
		$id=$_REQUEST['briefid'];
	
		//get Sample brief files
		/*$brief_sample=array();
		if ($handle = opendir('/home/sites/site7/web/FO/brief_sample/')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$brief_sample[]=$entry;
				}
			}
			closedir($handle);
		}*/
		
		$brief_sample=array('Editorial_guidelines_-_Ecommerce_site.doc','Editorial_guidelines_-_high-tech.doc','Database_updating.doc');

		$dwlfile= '/home/sites/site7/web/FO/brief_sample/'.$brief_sample[$id];
		$ext=$this->findexts($brief_sample[$id]);
	
		if($ext=="zip")
			$type='ZIP';
		else
			$type="msword";
		
		header('Content-type: APPLICATION/'.$type.'; charset=utf-8');
		header('Content-disposition: attachment;filename='.$brief_sample[$id]);
		readfile($dwlfile);
	}
	
	/* prebidding pop up info which shows published clients of writers participated in an article */
	public function prebiddingAction()
	{
		$part_obj=new Ep_Ao_Participation();
		$quoteslist=$part_obj->getAoParticipations($_REQUEST['id']);
		$uarray=array();
		
			for($a=0;$a<count($quoteslist);$a++)
			{
				//Customers
				$customerstrust=$part_obj->getcustomersPublished($quoteslist[$a]['user_id']);
				
					for($c=0;$c<=10;$c++)
					{
						if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png"))
							$uarray[$customerstrust[$c]['company_name']]="/FO/profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png";
						else
							$uarray[$customerstrust[$c]['company_name']]="/FO/images/customer-no-logo90.png";
					}
			}
			$this->_view->customerstrust=$uarray;
			$this->_view->participationcount=count($quoteslist);
			
		$this->_view->render("Client_prebidding");
	}
	
	/* Quotes selection page, which lists out all participated writers and selection can be made */
	public function quotesAction()
	{
		if($this->_view->clientidentifier=="")
		{
			$this_url = $_SERVER['REQUEST_URI'];	
			$this->_redirect("/index/index?return_url=".urlencode($this_url));
		}
			
		//Check Article exists or not
		$art_obj=new Ep_Ao_Article();	
		if($art_obj->checkArticleExists($_REQUEST['id'],$this->_view->clientidentifier)=="NO")
			$this->_redirect("/client/home");
			
		//Extend participation time of an article
		if($_REQUEST['extendparticipation_submit']!="")
		{
			//Update article participation expires in Article
			if(time()>$_REQUEST['submit_time'])
				$time=time();
			else
				$time=$_REQUEST['submit_time'];
				
			if($_REQUEST['extendtype']=="min")
			{
				$extendtime=$time+(60*$_REQUEST['extendvalue']);
				$extend='<b>'.$_REQUEST['extendvalue'].'</b> Mins(s)';
			}
			elseif($_REQUEST['extendtype']=="hour")
			{
				$extendtime=$time+(60*60*$_REQUEST['extendvalue']);
				$extend='<b>'.$_REQUEST['extendvalue'].'</b> Heure(s)';
			}
			elseif($_REQUEST['extendtype']=="day")
			{	
				$extendtime=$time+(60*60*24*$_REQUEST['extendvalue']);
				$extend='<b>'.$_REQUEST['extendvalue'].'</b> Jour(s)';
			}
			
			$extend_where=" id='".$_REQUEST['id']."'";
			$extend_data=array("participation_expires"=>$extendtime);
			$art_obj->updateArticle($extend_data,$extend_where);	
			
			$this->_redirect("/client/quotes?id=".$_GET['id']."&showpop=no");
		}	
			
		$ao_obj=new Ep_Ao_Delivery();
		$part_obj=new Ep_Ao_Participation();
		$comm_obj=new Ep_Ao_AdComments();
		
		//Delivery Details
		$aodetails=$ao_obj->Deliverydetails($_REQUEST['id']);
		$checkvalid=$ao_obj->getvalidquote($aodetails[0]['article_id']);
		$checkrefusal=$part_obj->checkbidrefusal($aodetails[0]['article_id']);
	
		//Redirecting to Order1 if writer is selected for an article  
			if($checkvalid=="yes" && $checkrefusal=="yes")
				$this->_redirect("/client/order1?id=".$_REQUEST['id']);
				
		//Getting Participation time difference
		if($aodetails[0]['participation_expires']>0)
		{
			if($aodetails[0]['minutediff']>60)
			{	
				$hour=round($aodetails[0]['minutediff']/60,2);
				$hourdiff=intval($hour);
				$mindiff=$aodetails[0]['minutediff']-($hourdiff*60);

				$totaldiff='';
				if($hourdiff>0)
					$totaldiff.=$hourdiff.' h ';
				if($mindiff>0)	
					$totaldiff.=$mindiff.' mn';	
				$aodetails[0]['hourdiff']=$hourdiff;	
				$aodetails[0]['delivery_timediff']=$totaldiff;
			}
			else
				$aodetails[0]['delivery_timediff']=$aodetails[0]['minutediff'].' mn';
		}
		else
			$aodetails[0]['delivery_timediff']=0;
			
		$aodetails[0]['participation_expires']=$aodetails[0]['participation_expires']-(118);
		
		if($aodetails[0]['language']=="")
			$aodetails[0]['language']='fr';
		
		$this->_view->aodetails=$aodetails;

		$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$this->_view->category_array=$cat_array;
		
		//Writers already worked
		$writers=$part_obj->clientWriters($this->_view->clientidentifier);
		$pubwriters=$part_obj->publishedWriters($this->_view->clientidentifier);
		
		$writerarray=array();
			for($w=0;$w<count($pubwriters);$w++)
				$writerarray[$w]=$pubwriters[$w]['user_id'];
	
		//Participation list
		$quoteslist=$part_obj->getAoParticipations($_REQUEST['id'],$_REQUEST['sort']);
		$uarray=array();
			for($a=0;$a<count($quoteslist);$a++)
			{
				$quoteslist[$a]['name']=ucfirst($quoteslist[$a]['first_name']).'&nbsp;'.ucfirst(substr($quoteslist[$a]['last_name'],0,1));
				$quoteslist[$a]['part_date']=date('M d',strtotime($quoteslist[$a]['created_at']));
				$quoteslist[$a]['profilepic']=$this->getContribpic($quoteslist[$a]['user_id'],'profile');
				
				if($quoteslist[$a]['contrib_percentage']=="")
					$quoteslist[$a]['contrib_percentage']=$this->getConfiguredval('nopremium_contribpercent');
					
				if($quoteslist[$a]['contrib_percentage']!=0)
					$quoteslist[$a]['price_user']=($quoteslist[$a]['price_user']*100)/$quoteslist[$a]['contrib_percentage'];
				else
					$quoteslist[$a]['price_user']=0;
				
				//to check previous selection
				if(in_array($quoteslist[$a]['user_id'],$writerarray))
					$quoteslist[$a]['prevsel']=1;
				else
					$quoteslist[$a]['prevsel']=0;
			
				//Valida date
				if($quoteslist[$a]['valid_date']!="" && $quoteslist[$a]['valid_date']!="0000-00-00 00:00:00")
				{
					setlocale(LC_TIME, "en_US");
					$quoteslist[$a]['valid_until']=date("jS F Y",strtotime($quoteslist[$a]['valid_date']));
					
					if($quoteslist[$a]['valid_date']<date("Y-m-d"))
						$quoteslist[$a]['valid_expired']="yes";
				}	
				else
					$quoteslist[$a]['valid_until']="no";
			
				//Customers
				$customerstrust=$part_obj->getcustomersPublished($quoteslist[$a]['user_id']);
				
					for($c=0;$c<count($customerstrust);$c++)
					{
						if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png"))
							$uarray[$customerstrust[$c]['company_name']]="/FO/profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png";
						else
							$uarray[$customerstrust[$c]['company_name']]="/FO/images/customer-no-logo90.png";
					}
				
				if($a!=0)
					$quoteslist[$a]['previous']=$quoteslist[$a-1]['user_id'];
				else
					$quoteslist[$a]['previous']=$quoteslist[count($quoteslist)-1]['user_id'];
				
				if($a==count($quoteslist)-1)
					$quoteslist[$a]['next']=$quoteslist[0]['user_id'];
				else
					$quoteslist[$a]['next']=$quoteslist[$a+1]['user_id'];
			}
			$this->_view->customerstrust=$uarray;
			
			
			$this->_view->quotesidlist = $quoteslist;
				
			if(count($quoteslist)>0)
			{	
					$page = $this->_getParam('page',1);
					$paginatorq = Zend_Paginator::factory($quoteslist);
					$paginatorq->setItemCountPerPage(6);
					$paginatorq->setCurrentPageNumber($page);
					$patterns='/[? &]page=[0-9]{1,2}/';
					$replace="";
					$this->_view->quoteslist = $paginatorq;
					$this->_view->pages = $paginatorq->getPages();
					$this->_view->pageURL=preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
					$this->_view->quoteslistcnt = count($quoteslist);
			}
		//print_r($this->_view->quoteslist);
			
		//Current Quotes
		$quoteslist=$ao_obj->currentquotes($this->_view->clientidentifier);
		$quotes=array();
		$n=0;
		for($q=0;$q<count($quoteslist);$q++)
		{
			if($quoteslist[$q]['publish']=="NO")
			{
				$quotes[$n]['id']=$quoteslist[$q]['id'];
				$quotes[$n]['title']=$quoteslist[$q]['title'];
				$quotes[$n]['valid']=$quoteslist[$q]['valid'];
				$quotes[$n]['partcount']=$quoteslist[$q]['partcount'];
				$quotes[$n]['participations']=$quoteslist[$q]['participations'];
				$n++;
			}
		}
		$this->_view->quotes=$quotes;
		
		//Comments
		$commentlist=$comm_obj->getComments($_REQUEST['id']);
			for($c=0;$c<count($commentlist);$c++)
			{
				$commentlist[$c]['profilepic']=$this->getCommentPic($commentlist[$c]['user_id'],$commentlist[$c]['type']);
				//Time
				if($commentlist[$c]['minutediff']==0)
					$commentlist[$c]['time']='Now';
				elseif($commentlist[$c]['minutediff']<=60)
					$commentlist[$c]['time']=$commentlist[$c]['minutediff'].'min(s) ago';
				elseif($commentlist[$c]['hourdiff']<=24)
					$commentlist[$c]['time']=$commentlist[$c]['hourdiff'].'hour(s) ago';
				else
					$commentlist[$c]['time']=date("d/m/Y H:i:s",strtotime($commentlist[$c]['created_at']));
			}
		$this->_view->commentlist=$commentlist;
		$this->_view->now=strtotime("now");
		$this->_view->page_title="Edit-place : Client quote selection area";
		$this->_view->render("Client_quotes");
	}

	/* Quote selection pop up which shows writer profile details and price bid with a writer selection button. Once a writer
	is selected, all other writers would be automatically refused*/
	public function quoteselectionAction()
	{	
		if($this->_view->clientidentifier=="")
		{
			$this_url = $_SERVER['REQUEST_URI'];	
			$this->_redirect("/index/index?return_url=".urlencode($this_url));
		}
		
		
		$user_obj=new Ep_Ao_Participation();
		$user_details=$user_obj->getProfileDetails($_POST['partid'],$_POST['artid']);
	
		$lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
			$this->_view->language_array=$lang_array;
		$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
			$this->_view->category_array=$cat_array;
	
		$user_details[0]['first_name']=strtolower($user_details[0]['first_name']);
		$user_details[0]['last_name']=strtolower($user_details[0]['last_name']);
		$user_details[0]['name']=ucfirst($user_details[0]['first_name']).'&nbsp;'.ucfirst(substr($user_details[0]['last_name'],0,1));
		$user_details[0]['profilepic']=$this->getContribpic($user_details[0]['user_id'],'profile');
		//Age
		$user_details[0]['age']=$user_details[0]['curryear']-$user_details[0]['byear'];
		if($user_details[0]['contrib_percentage']!=0)
			$user_details[0]['price_user_total']=($user_details[0]['price_user']*100)/$user_details[0]['contrib_percentage'];
		else
			$user_details[0]['price_user_total']=0;
		$user_details[0]['ep_percent']=100-$user_details[0]['contrib_percentage'];
		
		if($user_details[0]['valid_date']!="" && $user_details[0]['valid_date']!="0000-00-00 00:00:00" && $user_details[0]['valid_date']<date("Y-m-d"))
			$user_details[0]['datevalid']="no";
		else
			$user_details[0]['datevalid']="yes";
		
		if($user_details[0]['valid_date']!="" && $user_details[0]['valid_date']!="0000-00-00 00:00:00")
		{
			setlocale(LC_TIME, "en_US");
			$user_details[0]['valid_until']=date("jS F Y",strtotime($user_details[0]['valid_date']));
		}	
		else
			$user_details[0]['valid_until']="no";
			
		//Language
		if($user_details[0]['language_more']!="")
		{
			//forming array with lang id as index and percent as value
			$str=explode("\"",$user_details[0]['language_more']);
			$language=array();
			for($s=0;$s<count($str);$s=$s+4)
			{
				$index=$str[$s+1];
				if($index!="")
					$language[$index]=$str[$s+3];
			}
		}
		$this->_view->langpercent=$language;
		$user_details[0]['langstr']=$lang_array[$user_details[0]['language']];

		if($user_details[0]['language_more']!=NULL)
		{
			$languagekey=array_keys($language);
			if(count($languagekey)>0)
			{
				for($l=0;$l<count($languagekey);$l++)
					$user_details[0]['langstr'].=", ".$lang_array[$languagekey[$l]];
			}
		}
	
		//Clients
		$carray=array();
		$uarray=array();
		for($c=0;$c<count($user_details[0]['clients']);$c++)
		{
			if($user_details[0]['clients'][$c]['company_name']!="")
				$carray[]=$user_details[0]['clients'][$c]['company_name'];
				
			if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$user_details[0]['clients'][$c]['user_id']."/".$user_details[0]['clients'][$c]['user_id']."_global1.png"))
				$uarray[$user_details[0]['clients'][$c]['company_name']]="/FO/profiles/clients/logos/".$user_details[0]['clients'][$c]['user_id']."/".$user_details[0]['clients'][$c]['user_id']."_global1.png";
			else
				$uarray[$user_details[0]['clients'][$c]['company_name']]="/FO/images/customer-no-logo90.png";	
		}
		$user_details[0]['clientlist']=implode(", ",$carray);
		$user_details[0]['clientlogo']=$uarray;
		
		//Category
		$user_details[0]['catstr']=$this->getCategoryName($user_details[0]['favourite_category']);
		
		$user_details[0]['cats']=explode(",",$user_details[0]['favourite_category']);

		if($user_details[0]['category_more']!="")
		{
			//forming array with cat id as index and percent as value
			$str=explode("\"",$user_details[0]['category_more']);
			$category=array();
			for($s=0;$s<count($str);$s=$s+4)
			{
				$index=$str[$s+1];
				if($index!="")
					$category[$index]=$str[$s+3];
			}
		}
		$this->_view->catpercent=$category;
		
		//contrat type
		$contract=array("cdi"=>"CDI","cdd"=>"CDD","freelance"=>"Freelance","interim"=>"Interim");	
		
		//Experience and Education
		$exp_details=$user_obj->getContribexp($_POST['partid'],'job');
			for($x=0;$x<count($exp_details);$x++)
			{
				//setlocale(LC_TIME, "fr_FR");
				$exp_details[$x]['from_month']= strftime('%B', mktime(0, 0, 0, $exp_details[$x]['from_month']));
				$exp_details[$x]['to_month']= strftime('%B', mktime(0, 0, 0, $exp_details[$x]['to_month']));
				$exp_details[$x]['contract']=$contract[$exp_details[$x]['contract']];
			}
		$this->_view->exp_details=$exp_details;
		
		$education_details=$user_obj->getContribexp($_POST['partid'],'education');
			for($x=0;$x<count($education_details);$x++)
			{
				//setlocale(LC_TIME, "fr_FR");
				$education_details[$x]['from_month']= strftime('%B', mktime(0, 0, 0, $education_details[$x]['from_month']));
				$education_details[$x]['to_month']= strftime('%B', mktime(0, 0, 0, $education_details[$x]['to_month']));
			}
		$this->_view->education_details=$education_details;
		
		$this->_view->contribprofile=$user_details;
		//client contact info
		$client_obj=new Ep_User_Client();
		$this->_view->clientcontact=$client_obj->getClientdetails($this->_view->clientidentifier);
		$this->_view->render("Client_quoteselection");
	}

	/* Stage when a writer is selected for an article and not yet submitted his article*/
	public function order1Action()
	{
		if($this->_view->clientidentifier=="")
		{
			$this_url = $_SERVER['REQUEST_URI'];	
			$this->_redirect("/index/index?return_url=".urlencode($this_url));
		}
		
		//Check Article exists or not
		$art_obj=new Ep_Ao_Article();	
		if($art_obj->checkArticleExists($_REQUEST['id'],$this->_view->clientidentifier)=="NO")
			$this->_redirect("/client/home");
			
		$ao_obj=new Ep_Ao_Delivery();
		$part_obj=new Ep_Ao_Participation();		
		$comm_obj=new Ep_Ao_AdComments();
		$pay_obj=new Ep_Ao_PaymentArticle();
		$user_obj=new Ep_User_User();
	
		$quote_params=$this->_request->getParams();

		//Update participation on writer selection
		if($quote_params['quote']!="")
		{
			//Update client contact
			$client_obj=new Ep_User_Client();
			$client_obj->UpdateClientContact($this->_view->clientidentifier,$quote_params);
			
			//get details
			$ArticleDetails=$ao_obj->getMaildetails($quote_params['article']);
		
			if($ArticleDetails[0]['profile_type']=='senior')
			{
                $time=$ArticleDetails[0]['senior_time'];
				$parameter['resubmission']=$this->getConfiguredval('sc_resubmission');
            }
			else
            {    
				$time=$ArticleDetails[0]['junior_time'];
				$parameter['resubmission']=$this->getConfiguredval('jc_resubmission');
            }      
			//$expires=time()+(60*60*$time);
			$expires=time()+(60*$time);
			
			//Accept participation	
			$accept_where=" user_id='".$quote_params['quote']."' AND article_id='".$quote_params['article']."'";
			$accept_data=array("status"=>"bid","accept_refuse_at"=>date("Y-m-d H:i:s", time()),"article_submit_expires"=>$expires,"selection_type"=>"client");
			$part_obj->updateparticipation($accept_data,$accept_where);
			
			//Update price in Article
			$art_where=" id='".$quote_params['article']."'";
			$art_price=$this->totalclientprice($quote_params['contribprice'],$quote_params['article']);
			$art_data=array("price_final"=>$art_price);
			$art_obj->updateArticle($art_data,$art_where);
			
			//Update payment article if article is paid
			if($ArticleDetails[0]['paid_status']=="paid" && $ArticleDetails[0]['invoice_id']!="")
			{
				$parray=array();
				$parray['amount']=$art_price+$ArticleDetails[0]['premium_total'];
				$parray['amount_paid']=$parray['amount']*1.196;
				$pwhere=" id='".$ArticleDetails[0]['invoice_id']."'";
				$pay_obj->updatePaymentarticle($parray,$pwhere);
			}
			
			if($ArticleDetails[0]['currency']=='euro')
				$curr='&euro;';
			else
				$curr='&pound;';
			$parameters['articlelink']="<a href='http://ep-test.edit-place.co.uk/contrib/ongoing'><b>".$ArticleDetails[0]['title']."</b></a>";
			$parameters['AO_end_date']='<b>'.date('d/m/Y H:i:s',$expires).'</b>';
			$parameters['ongoinglink']="<a href='http://ep-test.edit-place.co.uk/contrib/ongoing'>cliquez-ici</a>";
			$parameters['clientcontact']="<a href='http://ep-test.edit-place.co.uk/contrib/compose-mail?senduser=".$this->_view->clientidentifier."'>click here</a>";
			$parameters['royalty']='<b>'.$quote_params['contribprice'].$curr.'</b>';
			$parameters['deliverlink']="<a href='http://mmm-new.edit-place.com/contrib/mission-deliver?article_id=".$quote_params['article']."'>ici</a>";
			$this->messageToEPMail($quote_params['quote'],108,$parameters);
	
			//Refuse remaining participations
			$parameter['article']='<b>'.$ArticleDetails[0]['title'].'</b>';
			$parameter['client']='<b>'.$this->_view->clientname.'</b>';
			
			$refuse_participations=$part_obj->Participationtorefuse($quote_params['article']);
				for($p=0;$p<count($refuse_participations);$p++)
					$this->messageToEPMail($refuse_participations[$p]['user_id'],26,$parameter);
					
			$refuse_where=" article_id='".$quote_params['article']."' AND status IN ('bid_premium','bid_nonpremium')";
			$refuse_data=array("status"=>"bid_refused","accept_refuse_at"=>date("Y-m-d H:i:s", time()),"selection_type"=>"client");
			$part_obj->updateparticipation($refuse_data,$refuse_where);	
			
			//Insert Recent Activities
			$act_obj=new Ep_User_RecentActivities();
			$ract=array("type" => "validate","user_id"=>$quote_params['quote'],"activity_by"=>$this->_view->clientidentifier,"article_id"=>$quote_params['article']);
			$act_obj->insertRecentActivities($ract);
			
			//Insert favourite contributor
			$fav_obj = new Ep_Ao_Favouritecontributor();
			$fav_obj->addfavcontrib($quote_params['quote'],$this->_view->clientidentifier);
			
			//ArticleHistory Insertion
			$hist_obj = new Ep_Article_ArticleHistory();
			$action_obj = new Ep_Article_ArticleActions();
			$history2=array();
			$history2['user_id']=$this->_view->clientidentifier;
			$history2['article_id']=$quote_params['article'];
				$sentence2=$action_obj->getActionSentence(2);
				$article_name='<a href="/ongoing/ao-details?client_id='.$this->_view->clientidentifier.'&ao_id='.$ArticleDetails[0]['id'].'&submenuId=ML2-SL4" target="_blank"><b>'.$ArticleDetails[0]['title'].'</b></a>';
					$cname=$user_obj->getUsername($quote_params['quote']);
				$contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$quote_params['quote'].'" target=_blank""><b>'.$cname.'</b></a>';
				$project_manager_name='<b>'.$this->_view->clientname.'(Client)</b>';
				$actionmessage=strip_tags($sentence2);
				eval("\$actionmessage= \"$actionmessage\";");
			$history2['stage']='selectionprofiles';
			$history2['action']='profile_accepted';
			$history2['action_sentence']=$actionmessage;
			$hist_obj->insertHistory($history2);
		
			$this->_redirect("/client/order1?id=".$_GET['id']);
		}
		
		//Extend writer submission time
		if($_REQUEST['extend_submit']!="")
		{
			$ArticleDetails=$ao_obj->getMaildetails($_REQUEST['id']);
			//Update article submit expires in Participation
			if(time()>$_REQUEST['submit_time'])
				$time=time();
			else
				$time=$_REQUEST['submit_time'];
				
			if($_REQUEST['extendtype']=="min")
			{
				$extendtime=$time+(60*$_REQUEST['extendvalue']);
				$extend='<b>'.$_REQUEST['extendvalue'].'</b> Mins(s)';
			}
			elseif($_REQUEST['extendtype']=="hour")
			{
				$extendtime=$time+(60*60*$_REQUEST['extendvalue']);
				$extend='<b>'.$_REQUEST['extendvalue'].'</b> Hour(s)';
			}
			elseif($_REQUEST['extendtype']=="day")
			{	
				$extendtime=$time+(60*60*24*$_REQUEST['extendvalue']);
				$extend='<b>'.$_REQUEST['extendvalue'].'</b> Day(s)';
			}
			
			$extend_where=" user_id='".$_REQUEST['contribid']."' AND article_id='".$_REQUEST['id']."'";
			$extend_data=array("article_submit_expires"=>$extendtime);
			$part_obj->updateparticipation($extend_data,$extend_where);	
			
			//Mail send contrib
			$parameters['clientcomment']=utf8_decode($_REQUEST['clientcomment']);
			$parameters['extend_hours']=$extend;
			$parameters['ongoinglink']="<a href='http://ep-test.edit-place.co.uk/contrib/ongoing'>click here</a>";
			$this->messageToEPMail($_REQUEST['contribid'],37,$parameters);
			
			//Insert Recent Activities
			$act_obj=new Ep_User_RecentActivities();
			$ract=array("type" => "extend","user_id"=>$_REQUEST['contribid'],"activity_by"=>$this->_view->clientidentifier,"article_id"=>$_REQUEST['id']);
			$act_obj->insertRecentActivities($ract);
			
			//ArticleHistory Insertion
			$hist_obj = new Ep_Article_ArticleHistory();
			$action_obj = new Ep_Article_ArticleActions();
			$history6=array();
			$history6['user_id']=$this->_view->clientidentifier;
			$history6['article_id']=$_REQUEST['id'];
			$history6['stage']='ongoing';
			$history6['action']='submittime_extended';
				$sentence6=$action_obj->getActionSentence(6);
				$project_manager_name='<b>'.$this->_view->clientname.'(Client)</b>';
				$article_name='<a href="/ongoing/ao-details?client_id='.$this->_view->clientidentifier.'&ao_id='.$ArticleDetails[0]['id'].'&submenuId=ML2-SL4" target="_blank"><b>'.$ArticleDetails[0]['title'].'</b></a>';
					$cname=$user_obj->getUsername($_REQUEST['contribid']);
				$contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$quote_params['quote'].'" target=_blank""><b>'.$cname.'</b></a>';
				$actionmessage=strip_tags($sentence6);
				eval("\$actionmessage= \"$actionmessage\";");
			$history6['action_sentence']=$actionmessage;
			$hist_obj->insertHistory($history6);
			
			$this->_redirect("/client/order1?id=".$_GET['id']);
		}
		
		$aoparticipation=$part_obj->selectedparticipation($_REQUEST['id']);
		$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$this->_view->category_array=$cat_array;
			
			//Redirecting to Order2 if Article is received 
			if(($aoparticipation[0]['article_path']!="" && $aoparticipation[0]['premium_option']=="0") || ($part_obj->CheckPublished($aoparticipation[0]['article_id'])=="YES" && $aoparticipation[0]['premium_option']!="0"))
				$this->_redirect("/client/order2?id=".$_GET['id']);
			
			//Delivery time difference
			if($aoparticipation[0]['minutediff']>60)
			{
				$hour=round($aoparticipation[0]['minutediff']/60,2);
				$hourdiff=intval($hour);
				$mindiff=$aoparticipation[0]['minutediff']-($hourdiff*60);
			
				$totaldiff='';
				if($hourdiff>0)
					$totaldiff.=$hourdiff.' h ';
				if($mindiff>0)	
					$totaldiff.=$mindiff.' mn';	
				$aoparticipation[0]['hourdiff']=$hourdiff;	
				$aoparticipation[0]['delivery_timediff']=$totaldiff;
			}
			else
			{
				$aoparticipation[0]['hourdiff']=0;	
				$aoparticipation[0]['delivery_timediff']=$aoparticipation[0]['minutediff'].' mn';
			}
			
		if($aoparticipation[0]['contrib_percentage']!=0)
			$aoparticipation[0]['price_user1']=($aoparticipation[0]['price_user']*100)/$aoparticipation[0]['contrib_percentage'];
		else
		{
			$aoparticipation[0]['price_user1']=$aoparticipation[0]['price_user'];
			$aoparticipation[0]['price_user']=0;
		}
		
		$aoparticipation[0]['price_user_total']=$aoparticipation[0]['price_user1']+$aoparticipation[0]['premium_total'];
		$aoparticipation[0]['ep_percent']=100-$aoparticipation[0]['contrib_percentage'];
		$aoparticipation[0]['article_submit_expires']=$aoparticipation[0]['article_submit_expires']-(118);
		$this->_view->aoparticipation=$aoparticipation;
	
		//Comments
		$commentlist=$comm_obj->getComments($_REQUEST['id']);
			for($c=0;$c<count($commentlist);$c++)
			{
				$commentlist[$c]['profilepic']=$this->getCommentPic($commentlist[$c]['user_id'],$commentlist[$c]['type']);
				//Time
				if($commentlist[$c]['minutediff']==0)
					$commentlist[$c]['time']='Now';
				elseif($commentlist[$c]['minutediff']<=60)
					$commentlist[$c]['time']=$commentlist[$c]['minutediff'].'min(s) ago';
				elseif($commentlist[$c]['hourdiff']<=24)
					$commentlist[$c]['time']=$commentlist[$c]['hourdiff'].'hour(s) ago';
				else
					$commentlist[$c]['time']=date("d/m/Y H:i:s",strtotime($commentlist[$c]['created_at']));
			}
		$this->_view->commentlist=$commentlist;
		$this->_view->commentcount=count($commentlist);		
	
		//Votre contact
		$contact=$part_obj->writercontact($_REQUEST['id']);
		$contact[0]['profilepic']=$this->getContribpic($contact[0]['identifier'],'profile');
		$contact[0]['name']=ucfirst($contact[0]['first_name']).'&nbsp;'.ucfirst(substr($contact[0]['last_name'],0,1));	
		$this->_view->contact=$contact;
		
		//customers
		$customerstrust=$part_obj->getcustomersPublished($contact[0]['identifier']);
		$uarray=array();
			for($c=0;$c<count($customerstrust);$c++)
			{
				if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png"))
					$uarray[$customerstrust[$c]['company_name']]="/FO/profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png";
				else
					$uarray[$customerstrust[$c]['company_name']]="/FO/images/customer-no-logo90.png";	
			}
		$this->_view->customerstrust=$uarray;
		$this->_view->now=strtotime("now");
		$this->_view->current_date=date("Y-m-d");	
		$this->_view->page_title="edit-place : Espace client - content box - No file available";
		$this->_view->render("Client_order1");
	}

	/* Stage when a article is submitted and not paid by client*/
	public function order2Action()
	{
		if($this->_view->clientidentifier=="")
		{
			$this_url = $_SERVER['REQUEST_URI'];	
			$this->_redirect("/index/index?return_url=".urlencode($this_url));
		}

		//Check Article exists or not
		$art_obj=new Ep_Ao_Article();	
		if($art_obj->checkArticleExists($_REQUEST['id'],$this->_view->clientidentifier)=="NO")
			$this->_redirect("/client/home");
			
		$part_obj=new Ep_Ao_Participation();		
		$comm_obj=new Ep_Ao_AdComments();
		
		$aoparticipation=$part_obj->selectedparticipation($_REQUEST['id']);

		//Redirecting to Order1 if Article is not received 
			if(($aoparticipation[0]['article_path']=="" && $aoparticipation[0]['premium_option']=="0") || ($part_obj->CheckPublished($aoparticipation[0]['article_id'])!="YES" && $aoparticipation[0]['premium_option']!="0"))
				$this->_redirect("/client/order1?id=".$_GET['id']);
				
		//Redirecting to Order4 if Article is paid 
			if($aoparticipation[0]['paid_status']=="paid")
				$this->_redirect("/client/order4?id=".$_GET['id']);	
		
		$aoparticipation[0]['article_sent_at']=date('d/m/Y H:i',strtotime($aoparticipation[0]['article_sent_at']));
		$ext=$this->findexts($aoparticipation[0]['article_name']);		
		$path='/home/sites/site7/web/FO/articles/'.$aoparticipation[0]['article_path'];
		
		if($ext=="zip")
		{
				$za = new ZipArchive();
				$za->open($path); 
				$filescount=$za->numFiles;	
					for( $i = 0; $i < $za->numFiles; $i++ ){
							$stat = $za->statIndex( $i );
							//print_r(iconv('IBM850', 'UTF-8', basename( $stat['name'])));
							$files[$i]['name']=iconv('IBM850', 'UTF-8',basename( $stat['name']));
							if($stat['size']>1000)
								$files[$i]['size']=round($stat['size']/1000).' kb';
							else
								$files[$i]['size']=$stat['size'].' b';
						} 
		}
		else
		{
			$files[0]['name']=$aoparticipation[0]['article_name'];
	
			if(file_exists($path))
				$filesize=filesize($path);

			if($filesize>=1000)
				$files[0]['size']=round($filesize/1000).' kb';
			elseif($filesize<1000 && $filesize>0)
				$files[0]['size']=$filesize.' b';
			else		
				$files[0]['size']="NA";
				
			$filescount=1;
		}
	
		$this->_view->files=$files;
		$this->_view->filescount=$filescount;
		if($aoparticipation[0]['contrib_percentage']!=0)
			$aoparticipation[0]['price_user1']=($aoparticipation[0]['price_user']*100)/$aoparticipation[0]['contrib_percentage'];
		else
		{
			$aoparticipation[0]['price_user1']=$aoparticipation[0]['price_user'];
			$aoparticipation[0]['price_user']=0;
		}
		
		$aoparticipation[0]['price_user_total']=$aoparticipation[0]['price_user1']+$aoparticipation[0]['premium_total'];
		$aoparticipation[0]['ep_percent']=100-$aoparticipation[0]['contrib_percentage'];
		$this->_view->aoparticipation=$aoparticipation;
		
		$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$this->_view->category_array=$cat_array;
		
		//Comments
		$commentlist=$comm_obj->getComments($_REQUEST['id']);
			for($c=0;$c<count($commentlist);$c++)
			{
				$commentlist[$c]['profilepic']=$this->getCommentPic($commentlist[$c]['user_id'],$commentlist[$c]['type']);
				//Time
				if($commentlist[$c]['minutediff']==0)
					$commentlist[$c]['time']='Now';
				elseif($commentlist[$c]['minutediff']<=60)
					$commentlist[$c]['time']=$commentlist[$c]['minutediff'].'min(s) ago';
				elseif($commentlist[$c]['hourdiff']<=24)
					$commentlist[$c]['time']=$commentlist[$c]['hourdiff'].'hour(s) ago';
				else
					$commentlist[$c]['time']=date("d/m/Y H:i:s",strtotime($commentlist[$c]['created_at']));
			}
		$this->_view->commentlist=$commentlist;
		$this->_view->commentcount=count($commentlist);		
		
		//Votre contact
		$contact=$part_obj->writercontact($_REQUEST['id']);
		$contact[0]['profilepic']=$this->getContribpic($contact[0]['identifier'],'profile');
		$contact[0]['name']=ucfirst($contact[0]['first_name']).'&nbsp;'.ucfirst(substr($contact[0]['last_name'],0,1));	
		$this->_view->contact=$contact;
		
		//customers
		$customerstrust=$part_obj->getcustomersPublished($contact[0]['identifier']);
		$uarray=array();
			for($c=0;$c<count($customerstrust);$c++)
			{
				if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png"))
					$uarray[$customerstrust[$c]['company_name']]="/FO/profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png";
				else
					$uarray[$customerstrust[$c]['company_name']]="/FO/images/customer-no-logo90.png";	
			}
		$this->_view->customerstrust=$uarray;
		
		$this->_view->page_title="edit-place : Espace client - content box - File available, pay to download";
		$this->_view->render("Client_order2");
	}

	/* Stage where article payment is done by the client*/
	public function order3Action()
	{
		if($this->_view->clientidentifier=="")
		{
			$this_url = $_SERVER['REQUEST_URI'];	
			$this->_redirect("/index/index?return_url=".urlencode($this_url));
		}
		
		//Check Article exists or not
		$art_obj=new Ep_Ao_Article();	
		if($art_obj->checkArticleExists($_REQUEST['id'],$this->_view->clientidentifier)=="NO")
			$this->_redirect("/client/home");
			
		$part_obj=new Ep_Ao_Participation();		
		$comm_obj=new Ep_Ao_AdComments();
		$user_obj=new Ep_User_User();

		//Fetching selected writer details
		$aoparticipation=$part_obj->selectedparticipation($_REQUEST['id']);
		
		//Redirecting to Order1 if Article is not received 
		if(($aoparticipation[0]['article_path']=="" && $aoparticipation[0]['premium_option']=="0") || ($part_obj->CheckPublished($aoparticipation[0]['article_id'])!="YES" && $aoparticipation[0]['premium_option']!="0"))
			$this->_redirect("/client/order1?id=".$_GET['id']);
			
		//Redirecting to Order4 if Article is paid 
			if($aoparticipation[0]['paid_status']=="paid")
				$this->_redirect("/client/order4?id=".$_GET['id']);	
				
		if($aoparticipation[0]['contrib_percentage']!=0)
			$aoparticipation[0]['price_user1']=($aoparticipation[0]['price_user']*100)/$aoparticipation[0]['contrib_percentage'];
		else
		{
			$aoparticipation[0]['price_user1']=$aoparticipation[0]['price_user'];
			$aoparticipation[0]['price_user']=0;
		}
		$aoparticipation[0]['price_user_total']=$aoparticipation[0]['price_user1']+$aoparticipation[0]['premium_total'];
		$aoparticipation[0]['ep_percent']=100-$aoparticipation[0]['contrib_percentage'];
		$this->_view->aoparticipation=$aoparticipation;
		
		//Payment credentials
		if($aoparticipation[0]['client_type']=='personal')
			$taxval="0";
		else
			$taxval="0.2";
		
		$tax=$taxval*ceil($aoparticipation[0]['price_user_total']);
		$this->EP_Client->pay[$_REQUEST['id']]=$aoparticipation[0]['price_user_total']+$tax;
	
		//check profile update
		$clientprofs=$user_obj->getClientdetails($this->_view->clientidentifier); 
		$this->_view->first_name=$clientprofs[0]['first_name'];
		
		//Comments
		$commentlist=$comm_obj->getComments($_REQUEST['id']);
		$this->_view->commentcount=count($commentlist);		
	
		//Votre contact
		$contact=$part_obj->writercontact($_REQUEST['id']);
		$contact[0]['profilepic']=$this->getContribpic($contact[0]['identifier'],'profile');
		$contact[0]['name']=ucfirst($contact[0]['first_name']).'&nbsp;'.ucfirst(substr($contact[0]['last_name'],0,1));	
		$this->_view->contact=$contact;
		
		//customers
		$customerstrust=$part_obj->getcustomersPublished($contact[0]['identifier']);
		$uarray=array();
			for($c=0;$c<count($customerstrust);$c++)
			{
				if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png"))
					$uarray[$customerstrust[$c]['company_name']]="/FO/profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png";
				else
					$uarray[$customerstrust[$c]['company_name']]="/FO/images/customer-no-logo90.png";	
			}
		$this->_view->customerstrust=$uarray;
		
		$this->_view->tax=$tax;
		$this->_view->page_title="edit-place : Espace client - content box - purchase";
		$this->_view->render("Client_order3");
	}

	/* Stage where article recieved and paid by the client / published*/
	public function order4Action()
	{
		if($this->_view->clientidentifier=="")
		{
			$this_url = $_SERVER['REQUEST_URI'];	
			$this->_redirect("/index/index?return_url=".urlencode($this_url));
		}
		
		//Check Article exists or not
		$art_obj=new Ep_Ao_Article();	
		if($art_obj->checkArticleExists($_REQUEST['id'],$this->_view->clientidentifier)=="NO")
			$this->_redirect("/client/home");
			
		$part_obj=new Ep_Ao_Participation();	
		$comm_obj=new Ep_Ao_AdComments();
		$user_obj=new Ep_User_User();
		$ap_obj=new EP_Article_ArticleProcess();
			
		//Redirect to quotes if it is refused and online again
		$Checkresult=$part_obj->CheckOnlineAgain($_REQUEST['id']);
		
		if($Checkresult=="YES")
			$this->_redirect("/client/quotes?id=".$_REQUEST['id']);
		
		//Check article paid	
		if($art_obj->checkArticlePaid($_REQUEST['id'])=="NO")
			$this->_redirect("/client/order2?id=".$_REQUEST['id']);
		
		//Refuse Definite
		if($_REQUEST['refuse_submit']!="")
		{
			//Update participation status
			$where_art=" article_id='".$_REQUEST['id']."' AND user_id='".$_REQUEST['contribid']."'";
			$arr_art=array("status"=>"closed_client_temp","refusecomment"=>utf8_decode($_REQUEST['refusecomment']));
			$part_obj->updateparticipation($arr_art,$where_art);
			
			//Mail send contrib
			//$parameters['clientcomment']=utf8_decode($_REQUEST['refusecomment']);
			//$this->messageToEPMail($_REQUEST['contribid'],39,$parameters);
			
			//Insert Recent Activities
			$act_obj=new Ep_User_RecentActivities();
			$ract=array("type" => "refuse","user_id"=>$_REQUEST['contribid'],"activity_by"=>$this->_view->clientidentifier,"article_id"=>$_REQUEST['id']);
			$act_obj->insertRecentActivities($ract);
			
			$this->_redirect("/client/order4?id=".$_GET['id']);
		}
		
		//Dissapproving an article
		if($_REQUEST['disspprove_submit']!="")
		{
			//Update article submit expires in Participation
			if($_REQUEST['dissapprovetype']=="min")
			{
				$extendtime=time()+(60*$_REQUEST['dissapprovevalue']);
				$extend='<b>'.$_REQUEST['dissapprovevalue'].'</b> Min(s)';
			}
			elseif($_REQUEST['dissapprovetype']=="hour")
			{
				$extendtime=time()+(60*60*$_REQUEST['dissapprovevalue']);
				$extend='<b>'.$_REQUEST['dissapprovevalue'].'</b> Hour(s)';
			}
			elseif($_REQUEST['dissapprovetype']=="day")
			{
				$extendtime=time()+(60*60*24*$_REQUEST['dissapprovevalue']);
				$extend='<b>'.$_REQUEST['dissapprovevalue'].'</b> Day(s)';
			}
			$extend_where=" user_id='".$_REQUEST['contribid']."' AND article_id='".$_REQUEST['id']."'";
			$extend_data=array("article_submit_expires"=>$extendtime);
			$part_obj->updateparticipation($extend_data,$extend_where);	
			
			//Update participation status
			$where_art=" article_id='".$_REQUEST['id']."' AND user_id='".$_REQUEST['contribid']."'";
			$arr_art=array("status"=>"disapprove_client");
			$part_obj->updateparticipation($arr_art,$where_art);
			
			//Update ArticleProcess 
			$where_artp=" id='".$_REQUEST['artproId']."' ";
			$arr_artp=array("client_comments"=>utf8_decode($_REQUEST['dissapprovecomment']));
			$ap_obj->updateArticleProcess($arr_artp,$where_artp);
			
			//Mail send contrib
			$parameters1['clientcomment']=utf8_decode($_REQUEST['dissapprovecomment']);
			$parameters1['extend_hours']=$extend;
			$parameters1['ongoinglink']="<a href='http://ep-test.edit-place.co.uk/contrib/ongoing'>Click here</a>";
			$this->messageToEPMail($_REQUEST['contribid'],43,$parameters1);
			
			//Insert Recent Activities
			$act_obj=new Ep_User_RecentActivities();
			$ract=array("type" => "resubmit","user_id"=>$_REQUEST['contribid'],"activity_by"=>$this->_view->clientidentifier,"article_id"=>$_REQUEST['id']);
			$act_obj->insertRecentActivities($ract);
			
			$this->_redirect("/client/order4?id=".$_GET['id']);
		}
		
		//Extend writer submission time on disapprove
		if($_REQUEST['extend_submit']!="")
		{
			//Update article submit expires in Participation
			if(time()>$_REQUEST['submit_time'])
				$time=time();
			else
				$time=$_REQUEST['submit_time'];
			
			if($_REQUEST['extendtype']=="min")
			{
				$extendtime=$time+(60*$_REQUEST['extendvalue']);
				$extendt='<b>'.$_REQUEST['extendvalue'].'</b> Min(s)';
			}
			elseif($_REQUEST['extendtype']=="hour")
			{
				$extendtime=$time+(60*60*$_REQUEST['extendvalue']);
				$extendt='<b>'.$_REQUEST['extendvalue'].'</b> Hour(s)';
			}
			elseif($_REQUEST['extendtype']=="day")
			{
				$extendtime=$time+(60*60*24*$_REQUEST['extendvalue']);
				$extendt='<b>'.$_REQUEST['extendvalue'].'</b> Day(s)';
			}
			$extend_where=" user_id='".$_REQUEST['contribid']."' AND article_id='".$_REQUEST['id']."'";
			$extend_data=array("article_submit_expires"=>$extendtime);
			$part_obj->updateparticipation($extend_data,$extend_where);	
			
			//Mail send contrib
			$parameters2['clientcomment']=utf8_decode($_REQUEST['clientcomment']);
			$parameters2['extend_hours']=$extendt;
			$parameters2['ongoinglink']="<a href='http://ep-test.edit-place.co.uk/contrib/ongoing'>click here</a>";
			$this->messageToEPMail($_REQUEST['contribid'],37,$parameters2);
			
			//Insert Recent Activities
			$act_obj=new Ep_User_RecentActivities();
			$ract=array("type" => "extend","user_id"=>$_REQUEST['contribid'],"activity_by"=>$this->_view->clientidentifier,"article_id"=>$_REQUEST['id']);
			$act_obj->insertRecentActivities($ract);
			
			$this->_redirect("/client/order4?id=".$_GET['id']);
		}
		
		//genrerate invoice after payment
		if($_GET['invoice']==1)
		{
			$this->generateInvoice($_GET['id']);
		}
		
		$aoparticipation=$part_obj->selectedparticipation($_REQUEST['id']);
		
		//Redirecting to Order1 if Article is not received 
		if(($aoparticipation[0]['article_path']=="" && $aoparticipation[0]['premium_option']=="0") || ($part_obj->CheckPublished($aoparticipation[0]['article_id'])!="YES" && $aoparticipation[0]['premium_option']!="0"))
			$this->_redirect("/client/order1?id=".$_GET['id']);
		
		for($a=0;$a<count($aoparticipation);$a++)
		{
			$path='/home/sites/site7/web/FO/articles/'.$aoparticipation[$a]['article_path'];
				
				if($aoparticipation[$a]['article_name']!="" && file_exists($path))
				{
					//$aoparticipation[$a]['article_sent_at']=date('d/m/Y H:i',strtotime($aoparticipation[$a]['article_sent_at']));	
					$aoparticipation[$a]['article_sent_at']=date('d/m/Y',strtotime($aoparticipation[$a]['article_sent_at']));	
					$files[0]['name']=$aoparticipation[$a]['article_name'];
					$filesize=filesize($path);

					if($filesize>=1000)
						$files[0]['size']=round($filesize/1000).' kb';
					elseif($filesize<1000 && $filesize>0)
						$files[0]['size']=$filesize.' b';
					else		
						$files[0]['size']="NA";
						
					$aoparticipation[$a]['files']=$files;
					$aoparticipation[0]['filescount']+=1;
				}
			
		}
		if($aoparticipation[0]['contrib_percentage']!=0)
			$aoparticipation[0]['price_user1']=($aoparticipation[0]['price_user']*100)/$aoparticipation[0]['contrib_percentage'];
		else
		{
			$aoparticipation[0]['price_user1']=$aoparticipation[0]['price_user'];
			$aoparticipation[0]['price_user']=0;
		}
			
		$aoparticipation[0]['price_user_total']=$aoparticipation[0]['price_user1']+$aoparticipation[0]['premium_total'];	
		$aoparticipation[0]['ep_percent']=100-$aoparticipation[0]['contrib_percentage'];
		
		$royalty_obj=new Ep_Royalty_Royalties();
		if($royalty_obj->checkRoyaltyExists($_REQUEST['id'])=='NO' || $part_obj->CheckPublished($_REQUEST['id'])=="NO")
			$aoparticipation[0]['royalty']="YES";
		else
			$aoparticipation[0]['royalty']="NO";
		
		//Delivery time difference
			if($aoparticipation[0]['minutediff']>60)
			{
				$hour=round($aoparticipation[0]['minutediff']/60,2);
				$hourdiff=intval($hour);
				$mindiff=$aoparticipation[0]['minutediff']-($hourdiff*60);
				
				$totaldiff='';
				if($hourdiff>0)
					$totaldiff.=$hourdiff.' h ';
				if($mindiff>0)	
					$totaldiff.=$mindiff.' mn';	
				$aoparticipation[0]['hourdiff']=$hourdiff;	
				$aoparticipation[0]['delivery_timediff']=$totaldiff;
			}
			else
			{
				$aoparticipation[0]['hourdiff']=0;	
				$aoparticipation[0]['delivery_timediff']=$aoparticipation[0]['minutediff'].' mn';
			}
		$aoparticipation[0]['article_submit_expires']=$aoparticipation[0]['article_submit_expires']-(118);
		$this->_view->aoparticipation=$aoparticipation;
		
		$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$this->_view->category_array=$cat_array;
		
		//check profile update
		$clientprofs=$user_obj->getClientdetails($this->_view->clientidentifier); 
		$this->_view->first_name=$clientprofs[0]['first_name'];
		
		//Comments
		$commentlist=$comm_obj->getComments($_REQUEST['id']);
			for($c=0;$c<count($commentlist);$c++)
			{
				$commentlist[$c]['profilepic']=$this->getCommentPic($commentlist[$c]['user_id'],$commentlist[$c]['type']);
				
				//Time
				if($commentlist[$c]['minutediff']==0)
					$commentlist[$c]['time']='Now';
				elseif($commentlist[$c]['minutediff']<=60)
					$commentlist[$c]['time']=$commentlist[$c]['minutediff'].'min(s) ago';
				elseif($commentlist[$c]['hourdiff']<=24)
					$commentlist[$c]['time']=$commentlist[$c]['hourdiff'].'hour(s) ago';
				else
					$commentlist[$c]['time']=date("d/m/Y H:i:s",strtotime($commentlist[$c]['created_at']));
			}
		$this->_view->commentlist=$commentlist;
		$this->_view->commentcount=count($commentlist);	
	
		//Votre contact
		$contact=$part_obj->writercontact($_REQUEST['id']);
		$contact[0]['profilepic']=$this->getContribpic($contact[0]['identifier'],'profile');
		$contact[0]['name']=ucfirst($contact[0]['first_name']).'&nbsp;'.ucfirst(substr($contact[0]['last_name'],0,1));	
		$this->_view->contact=$contact;
			
		//customers
		$customerstrust=$part_obj->getcustomersPublished($contact[0]['identifier']);
		$uarray=array();
			for($c=0;$c<count($customerstrust);$c++)
			{
				if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png"))
					$uarray[$customerstrust[$c]['company_name']]="/FO/profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png";
				else
					$uarray[$customerstrust[$c]['company_name']]="/FO/images/customer-no-logo90.png";	
			}
		$this->_view->customerstrust=$uarray;
		$this->_view->now=strtotime("now");
		$this->_view->current_date=date("Y-m-d");	
		$this->_view->page_title="edit-place : Espace client - content box - Download now";
		$this->_view->render("Client_order4");
	}

	/* submitting comments by client in each stage of article*/
	public function submitaocommentAction()
	{
		if($this->_view->clientidentifier=="")
			$this->_redirect("/index/index");
	
		$comt_obj=new Ep_Ao_AdComments();	
		$art_obj=new Ep_Ao_Article();
		$part_obj=new Ep_Ao_Participation();
		$comm_obj=new Ep_Comments_Adcomments();
		$user_obj=new Ep_User_User();
		$comment_params=$this->_request->getParams();
	
			$commentarray=array();
			$commentarray['user_id']=$this->_view->clientidentifier;
			$commentarray['type']="article";
			$commentarray['type_identifier']=$comment_params['article'];
			$commentarray['comments']=utf8_decode($comment_params['commentuser']);
			$addcomment=$comt_obj->InsertComment($commentarray);
		
		//Insert Recent Activities
		$act_obj=new Ep_User_RecentActivities();
		$ract=array("type" => "comment","user_id"=>$this->_view->clientidentifier,"activity_by"=>$this->_view->clientidentifier,"article_id"=>$comment_params['article'],"commentid"=>$addcomment);
		$act_obj->insertRecentActivities($ract);
		
		//Send mail to contribs
			$parameter['ongoinglink']="<a href='http://ep-test.edit-place.co.uk/contrib/ongoing'>click here</a>";
			$articledetails=$art_obj->getArticledetails($comment_params['article']);
			$parameter['article']='<b>'.$articledetails[0]['title'].'</b>';
				$Clientdetail=$user_obj->getClientdetails($this->_view->clientidentifier);
				if($Clientdetail[0]['company_name']!="")
					$Clientname=$Clientdetail[0]['company_name'];
				elseif($Clientdetail[0]['first_name']!="")
					$Clientname=ucfirst($Clientdetail[0]['first_name']).'&nbsp;'.ucfirst(substr($Clientdetail[0]['last_name'],0,1));	
				else
					$Clientname="anonymous";
					
			$parameter['client']='<b>'.utf8_decode($Clientname).'</b>';
			$contribslist=$part_obj->getmailcontribs($comment_params['article']);
			
			if($_REQUEST['quotes']==1)
				$commentcontriblist=$comm_obj->getcommentedlist($comment_params['article']);
			else
				$commentcontriblist=array();
				
			$contribslist1=array_merge($contribslist,$commentcontriblist);
			$contribslist2=array_map('unserialize', array_unique(array_map('serialize', $contribslist1)));
			//print_r($contribslist);print_r($contribslist2);exit;
			
			for($m=0;$m<count($contribslist2);$m++)
			{
				$this->messageToEPMail($contribslist2[$m]['user_id'],79,$parameter);
			}
		$newcomment="";
		//echo "123";exit;
		if($addcomment!="NO")
		{
		$count=$comment_params['commentcount']+1;
		$user_obj=new Ep_User_User();
		$name=$user_obj->getUsername($this->_view->clientidentifier);
		$pic=$this->getPicPath($this->_view->clientidentifier,'home');
		$newcomment.='<li class="media" id="comment_'.$addcomment.'">
							<a data-target="#viewProfile-ajax" data-toggle="modal" role="button" href="" class="pull-left">
								<img src="'.$pic.'" class="media-object" alt="Topito">
							</a>
							<div class="media-body">
								<h4 class="media-heading"><a data-target="#viewProfile-ajax" data-toggle="modal" role="button" href="http://ued.sebcdesign.com/edit-place/client/profile_topito.html">'.$name.'</a>
								<div class="pull-right"><button type="button" class="close" onClick="deletecomment('.$addcomment.');">&times;</button></div>
								</h4>
								'.stripslashes($comment_params['commentuser']).'
								<p class="muted">Now</p>
							</div>
						</li>';	
		}
		
		echo json_encode(array('status'=>1,'html'=>$newcomment,'count'=>$count));				
	}
	
	/* Deleting comments added by client*/
	public function deleteaocommentAction()
	{
		$comt_obj=new Ep_Ao_AdComments();	
		$comment_params=$this->_request->getParams();
		$setcomment=$comt_obj->inactivateComment($comment_params['cid']);
		echo $setcomment;
		exit;
	}
	
	// For disspprove and refuse an article
	public function refusedefinitearticleAction()
	{
		$part_obj=new Ep_Ao_Participation();	
		$art_params=$this->_request->getParams();
		
		$where_art=" article_id='".$art_params['article']."' AND user_id='".$art_params['part_user']."'";
		$arr_art=array();
		if($art_params['refuse']=='definite')
			$arr_art['status']='closed_client_temp';
		elseif($art_params['refuse']=='disapprove')
			$arr_art['status']='disapprove_client';
			
		$part_obj->updateparticipation($arr_art,$where_art);
		exit;
	}
	
	/* Adding royalties on validation of article if it is not yet added anywhere from BO*/
	public function addroyaltiesAction()
	{
		$part_obj=new Ep_Ao_Participation();
		$participate_obj=new Ep_Participation_Participation();
		$royalty_obj=new Ep_Royalty_Royalties();
			
			$paricipationdetails=$participate_obj->getParticipateDetails($_REQUEST['partid']);
			//check whether royalties already exists for an article in db
			if($royalty_obj->checkRoyaltyExists($_REQUEST['article'])=='NO')
			{
				$royalty_obj->participate_id=$paricipationdetails[0]['participateId'];
				$royalty_obj->article_id=$paricipationdetails[0]['article_id'];
				$royalty_obj->user_id=$paricipationdetails[0]['user_id'];
				$royalty_obj->price=$paricipationdetails[0]['price_user'];
				$royalty_obj->currency=$paricipationdetails[0]['currency'];
				$royalty_obj->insert();
			}
			
			//Update Participation
			$where_art=" id='".$_REQUEST['partid']."'";
			$arr_art=array();
			$arr_art['status']='published';
			$arr_art['current_stage']='client';
			$part_obj->updateparticipation($arr_art,$where_art);
			
			//Update Delivery
			$where_del=" id='".$paricipationdetails[0]['deliveryId']."'";
			$del_obj=new Ep_Ao_Delivery();	
			$arr_del=array();
			$arr_del['status_bo']='valid';
			$del_obj->updateDelivery($arr_del,$where_del);
			
			//Update profile_type if sub-junior
			$user_obj = new Ep_User_User();
			$user_obj->updatesubjunior($paricipationdetails[0]['user_id']);			
			
			$art_obj = new Ep_Ao_Article();
			$artdetails=$art_obj->getArticledetails($paricipationdetails[0]['article_id']);
			//Mail send contrib
			$parameters['article']='<b>'.$artdetails[0]['title'].'</b>';
			if($paricipationdetails[0]['currency']=='euro')
				$curr='&euro;';
			else
				$curr='&pound;';
			$parameters['royalty']='<b>'.$paricipationdetails[0]['price_user'].$curr.'</b>';
			$this->messageToEPMail($paricipationdetails[0]['user_id'],44,$parameters);
			
			//generate invoice
			$this->generateInvoice($_REQUEST['article']);
		exit;	
	}
	
	/* Function to download spec file of an ao/article in zip  */
	public function downloadbriefAction()
	{
		if($this->_view->clientidentifier=="")
		$this->_redirect("/index/index");

		$client_identifier=$this->_view->clientidentifier;

		$fileParams=$this->_request->getParams();
		$article_id=$fileParams['id'];

		$delivery=new Ep_Article_Delivery();

		$articleDetails=$delivery->getArticleBrief($article_id);
		$filearray=explode("|",$articleDetails[0]['filepath']);
		
		$files_array=array();
		for($f=0;$f<count($filearray);$f++)
			$files_array[]=SPEC_FILE_PATH. $filearray[$f];
		
		//Creating zip
		$articleDetails[0]['title']=str_replace(" ","_",$articleDetails[0]['title']);
		$filename=APP_PATH_ROOT.'articles/Zip/'.$articleDetails[0]['title'].'.zip';
		//print_r($files_array); echo $filename; exit;
		$result = $this->create_zip($files_array,$filename);

			//Download zip file
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$articleDetails[0]['title'].'.zip');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			readfile($filename);
            
    }
	
	/* Function to download spec file of Poll */
	public function downloadpollbriefAction()
	{
		if($this->_view->clientidentifier=="")
		$this->_redirect("/index/index");

		$client_identifier=$this->_view->clientidentifier;

		$fileParams=$this->_request->getParams();
		$poll_id=$fileParams['id'];

		$poll=new Ep_Poll_Poll();

		$pollDetails=$poll->Polldetail($poll_id);
		
		//Creating zip
		$pollDetails[0]['title']=str_replace(" ","_",$pollDetails[0]['title']);
		$filename=APP_PATH_ROOT.'poll_spec/'.$pollDetails[0]['file_name'];
		
			//Download zip file
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$pollDetails[0]['file_name']);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			readfile($filename);
            
    }
	
	/**************************************************** Payment ******************************************************/
	/* Function for payment initiation Paypal/CC */
	public function paypalpaymentAction()
	{
		$payParams=$this->_request->getParams();

		if($payParams['article']!="")
		{
			if($_GET['paytype']=='PP')
			{
			//paypal payment
			require_once('EP_paypal.class.php');  // include the class file
			$p = new paypal_class;             // initiate an instance of the class
			//$p->paypal_url ='https://www.paypal.com/cgi-bin/webscr';
			$p->paypal_url ='https://www.sandbox.paypal.com/cgi-bin/webscr';
			$this_script = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
			
			if($payParams['currency']=='euro')
				$curr='EUR';
			else
				$curr='GBP';
				
			// if there is not action variable, set the default action of 'process'
			if (empty($_GET['action'])) $_GET['action'] = 'process';  
				switch ($_GET['action']) {
					case 'process':      // Process and order...
						  $p->add_field('business', 'arunravuri@test.com');
						  $p->add_field('return', 'http://ep-test.edit-place.co.uk/client/order4?id='.$payParams['delivery']);
						  $p->add_field('cancel_return', 'http://ep-test.edit-place.co.uk/client/quotes?id='.$payParams['delivery']);
						  $p->add_field('notify_url', 'http://ep-test.edit-place.co.uk/client/notifypaynew');
						  $p->add_field('currency_code', $curr);
						  $p->add_field('lc', 'GB');
						  $p->add_field('cmd', '_cart');
						  $p->add_field('page_style', 'Edit-place');
						  $p->add_field('upload', '1');
						  $p->add_field('no_shipping', '1');
						  $p->add_field('no_note', '1');
						  $p->add_field('item_name', 'Pack edit-place');
						  $p->add_field('item_name_1', '');//dynamic plan name
						  $p->add_field('amount_1', '500');//dynamic plan price
						  //$p->add_field('amount', '');
						  $ccost=$payParams['amount_topay'];
						  $ccost=round($ccost,2);
						  $p->add_field('amount_1',$ccost);
						  $p->add_field('item_number_1', 'Basket');
						  $p->add_field('item_name_1', 'Article');
			  
						  if($_REQUEST['pack_type']=="plan2")
						  {
							$p->add_field('amount_1', '499');
							$p->add_field('item_number_1', 'Pack1002');
							$p->add_field('item_name_1', 'Pack cles en mains');
						  }

						  if($_REQUEST['pack_type']=="plan3")
						  {
							$p->add_field('amount_1', '899');
							$p->add_field('item_number_1', 'Pack1003');
							$p->add_field('item_name_1', 'Pack expert');
						  }
						// main customised variable in paypal payment where we pass all variables required for the db insertion & reference
						  $p->add_field('custom',$this->_view->clientidentifier."|".$payParams['delivery']."|".$payParams['article']."|".$payParams['amount']);
						  $p->submit_paypal_post(); // submit the fields to paypal
						  break;

					case 'success':      // Order was successful...
						echo "<html><head><title>Success</title></head><body><h3>Thank you for your order.</h3>";
							foreach ($_POST as $key => $value) { echo "$key: $value<br>"; }
						echo "</body></html>";
						break;
     
					case 'cancel':       // Order was canceled...
						print_r($_SESSION['client']);
						exit;
						// The order was canceled before being completed. 
						echo "<html><head><title>Canceled</title></head><body><h3>The order was canceled.</h3>";
						$time = 3; //Time (in seconds) to wait. 
						$url = "http://ep-test.edit-place.co.uk/package/"; //Location to send to. 
						//header("Refresh: $time; url=$url");  
						echo "</body></html>";	
						// header("location:index.php"); 
						break;
    
					case 'ipn':          // Paypal is calling page for IPN validation...
						if ($p->validate_ipn()) {
							// For this example, we'll just email ourselves ALL the data.
							$subject = 'Instant Payment Notification - Recieved Payment';
							//$to = 'mailpearls@gmail.com';    //  your email
							//$to = 'mfouris@edit-place.com';  
							$to = $this->getConfiguredval('paypalid');
							$body =  "An instant payment notification was successfully recieved\n";
							$body .= "from ".$p->ipn_data['payer_email']." on ".date('m/d/Y');
							$body .= " at ".date('g:i A')."\n\nDetails:\n";
							foreach ($p->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
							mail($to, $subject, $body);
						}
						break;
				}	   
			}
			elseif($_GET['paytype']=='CC')
			{
				//Credit card payment
				//Replace these with the values you receive from Realex Payments
				//need to set all credentials and parameters required for CC payment
				$merchantid = "editplace";
				$secret = "yhlrXX5KiS";
				//$merchantid = $this->getConfiguredval('creditid');
				//$secret = $this->getConfiguredval('creditpassword');
				//The code below is used to create the timestamp format required by Realex Payments
				$timestamp = strftime("%Y%m%d%H%M%S");
				mt_srand((double)microtime()*1000000);
				$orderid = $timestamp."-".mt_rand(1, 999);
				$curr = "EUR";
				$ccost=$payParams['amount_topay'];
				$ccost=round($ccost,2);
				$amount = $ccost * 100;
				$tmp = "$timestamp.$merchantid.$orderid.$amount.$curr";
				$md5hash = md5($tmp);
				$tmp = "$md5hash.$secret";
				$md5hash = md5($tmp);
				$tmp = "$timestamp.$merchantid.$orderid.$amount.$curr";
				$sha_hash = sha1($tmp);
				$tmp = "$sha_hash.$secret";
				$sha_hash = sha1($tmp);

				$this->_view->merchantid=$merchantid;
				$this->_view->orderid=$orderid;
				$this->_view->curr=$curr;
				$this->_view->amount=$amount;
				$this->_view->timestamp=$timestamp;
				$this->_view->md5hash=$md5hash;
				$this->_view->sha_hash=$sha_hash;
				
				$this->_view->client=$this->EPClient_reg->clientidentifier;
				$this->_view->delivery=$payParams['delivery'];
				$this->_view->article=$$payParams['article'];
				$this->_view->art_amount=$payParams['amount'];
				//phtml file paymentcc.phtml which contains form for all this parameters
				$this->render("Client_paymentcc");	
			}
		}
	}
	
	/* notify url for paypal payment where required db insertion /update is done in the background on success of transaction.*/
	public function notifypaynewAction()
	{
		$ReqArr1="";
		$ReqArr=$this->_request->getParams();
		$cus_var=explode("|",$ReqArr['custom']);
		
		foreach ($ReqArr as $key => $value)
				$ReqArr1.=$key."\t".$value."\n";
		mail('kavithashree.r@gmail.com',"IPN Data edit place",$ReqArr1,'From: kavithashree.r@gmail.com');//exit;
			
		//Payment_article insertion with paypal payment transaction details
		$paymentobj = new Ep_Ao_PaymentArticle();
		$inv_id=$paymentobj->insertPayment_article($ReqArr);
		
		
		
		//Article table updation
		$art_obj = new Ep_Ao_Article();
		$art_obj->updatePayarticle($inv_id,$cus_var);
		
		$artdetails=$art_obj->getArticledetails($cus_var[2]);
		//Mail send client
		//$parameters['article']='<b>'.$artdetails[0]['title'].'</b>';
		//$this->messageToEPMail($cus_var[0],69,$parameters);
		exit;
	}
	
	/* notify url for cc payment where required db insertion /update is done in the background on success of transaction. 
	mysql core queries are written, as it wont support zend model functions */
	public function notifyccpaymentsAction()
    {
		$ReqArr1="";
		$ReqArr=$this->_request->getParams();
		//print_r($ReqArr);exit;
		$timestamp = $ReqArr['TIMESTAMP'];
		$result = $ReqArr['RESULT'];
		$orderid = $ReqArr['ORDER_ID'];
		$message = $ReqArr['MESSAGE'];
		$authcode = $ReqArr['AUTHCODE'];
		$pasref = $ReqArr['PASREF'];
		$realexmd5 = $ReqArr['MD5HASH'];
		$merchantid = "editplace";
		$secret = "yhlrXX5KiS";
		$tmp = "$timestamp.$merchantid.$orderid.$result.$message.$pasref.$authcode";
		$md5hash = md5($tmp);
		$tmp = "$md5hash.$secret";
		$md5hash = md5($tmp);

			//Check to see if hashes match or not
			if ($md5hash != $realexmd5) {
				echo "hashes don't match - response not authenticated!";
			}
			
			if($ReqArr['RESULT']==00 || $ReqArr['RESULT']=='00' )
			{
				echo "<div style='font-size:24px;font-weight:bold;font-family:Comfortaa,sans-serif'>Payment Accepted </div><br>";
				echo "<div style='font-size:18px;font-family:Comfortaa,sans-serif'>Thank you! <a href='http://ep-test.edit-place.co.uk/client/order4?id=".$ReqArr['delivery']."'>click here</a> to go back to Edit-place</div>";

				$con = mysql_connect("localhost","epweb","293PA3Y4577KjVUM");
				if (!$con)
				{
				  die('Could not connect: ' . mysql_error());
				}

				mysql_select_db("dev_editplace1", $con);

				//Payment_article insertion
				$d = new Date();
				$id=$d->getSubDate(5,14).mt_rand(100000,999999);
				$amount=$ReqArr['AMOUNT']/100; 

				$InsertQuery="INSERT INTO 
									Payment_article
									(id,user_id,amount,type,pay_type,merchant_id,order_id,result_cc,authcode,
									pasref,avspostcoderesult,avsaddressresult,cvnresult,batchid,amount_paid,tax)
							VALUES 
									('".$id."','".$ReqArr['client']."','".$ReqArr['art_amount']."','instant','CC','".$ReqArr['MERCHANT_ID']."','".$ReqArr['ORDER_ID']."',
									'".$ReqArr['RESULT']."','".$ReqArr['AUTHCODE']."','".$ReqArr['PASREF']."','".$ReqArr['AVSPOSTCODERESULT']."','".$ReqArr['AVSADDRESSRESULT']."',
									'".$ReqArr['CVNRESULT']."','".$ReqArr['BATCHID']."','".$amount."','20') ";
				//echo "Query".$InsertQuery;
				mysql_query($InsertQuery);

				//Article updation
						mysql_query("UPDATE
										Article
								SET
										paid_status = 'paid', 
										invoice_id = '".$id."', 
										price_payed = '".$ReqArr['art_amount']."'
								WHERE
										id='".$ReqArr['article']."'");
					
					$articleQuery="SELECT * FROM Article WHERE id='".$ReqArr['article']."'";
					$articleDetails = mysql_fetch_assoc( mysql_query($articleQuery) );
					
				//Mail send client
				$parameters['article']='<b>'.$articleDetails['title'].'</b>';
				$this->messageToEPMailccpay($ReqArr['client'],69,$parameters);			
					
			mysql_close($con);
			}
			else
			{
				echo "The transaction failed ";
				echo "<a href='http://ep-test.edit-place.co.uk/client/quotes?id=".$ReqArr['article']."'>Go to site!! </a>";
			}
	}
	
	/* Function to email client on successful cc payment */
	public function messageToEPMailccpay($to,$mailid,$parameters)
	{
		$article=$parameters['article'];
		
		//Get Mail 
		$mailQuery="SELECT * FROM AutoemailsNewversion WHERE Id ='".$mailid."'";
		$mailDetails = mysql_fetch_assoc( mysql_query($mailQuery) );
		
		$Object=$mailDetails['Object'];
		$Object=strip_tags($Object);
        eval("\$Object= \"$Object\";");

		$Message=$mailDetails['Message'];
        eval("\$Message= \"$Message\";");
		$Message=stripslashes($Message);

			//Ticket insertion
			$d1 = new Date();
			$ticket_id=$d1->getSubDate(5,14).mt_rand(100000,999999);
			
			$InsertTicketQuery="INSERT INTO 
								Ticket
								(id,sender_id,recipient_id,title,status)
						VALUES 
								('".$ticket_id."','111201092609847','".$to."','".$Object."','0') ";
			
			mysql_query($InsertTicketQuery);
    
			//Message insertion
			$d2 = new Date();
			$message_id=$d2->getSubDate(5,14).mt_rand(100000,999999);
			
			$InsertMessageQuery="INSERT INTO 
								Message
								(id,ticket_id,content,type,status,Approved,auto_mail)
						VALUES 
								('".$message_id."','".$ticket_id."','".$Message."','0','0','yes','yes') ";
			
			mysql_query($InsertMessageQuery);
	
       
			//To user details
			$userQuery="SELECT * FROM User WHERE identifier='".$to."'";
			$UserDetails = mysql_fetch_assoc( mysql_query($userQuery) );
			
            $email=$UserDetails['email'];
            $password=$UserDetails['password'];
            $type=$UserDetails['type'];
            
				$text_mail="<p>Dear client,<br><br>
								You have received an email from Edit-place&nbsp;!<br><br>
								Thank you <a href=\"http://ep-test.edit-place.co.uk/client/emaillogin?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$message_id."&ticket=".$ticket_id."\">click here</a> to read.<br><br>
								Yours sincerely,<br>
								<br>
								The Edit-place team</p>"
										;
					
				//Send mail		
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: Support Edit-place <support@edit-place.com>' . "\r\n";

					mail($email, $Object, $text_mail, $headers);		
	}
	
	/* Downloading individual article from order4 page*/
	public function downloadarticleAction()
	{
		if($this->_view->clientidentifier=="")
			$this->_redirect("/index/index");
	
		$art_obj=new Ep_Ao_Article();	
		$act_obj=new Ep_User_RecentActivities();
		
		if($_REQUEST['artpid']!="")
		{
			$filepath=$art_obj->ArtDownloadpathap($_REQUEST['artpid']);
			$dwlfile= APP_PATH_ROOT.'articles/'.$filepath[0]['article_path'];
			
			//Insert Recent Activities
			$ract=array("type" => "download","user_id"=>$this->_view->clientidentifier,"activity_by"=>$this->_view->clientidentifier,"article_id"=>$filepath[0]['id']);
			$act_obj->insertRecentActivities($ract);
		
			$title=$this->findfilename($filepath[0]['article_name']);
			$ext=$this->findexts($filepath[0]['article_path']);
		
			$filetitle=$title.'.'.$ext;
			if($ext=="zip")
				$type='ZIP';
			else
				$type="force-download";
			
			header('Content-type: APPLICATION/'.$type.'; charset=utf-8');
			header('Content-disposition: attachment;filename='.$filetitle);
			readfile($dwlfile);
		}
	}
	
	/* Downloading all articles in zip file from order4 page*/
	public function downloadarticlezipAction()
	{
		$art_obj=new Ep_Ao_Article();	
		$act_obj=new Ep_User_RecentActivities();
		
		$id=$_REQUEST['id'];
		$files_to_zip=$art_obj->ArtDownloadpath($id);
		//print_r($files_to_zip);exit;
		$files_to_zip[0]['title']=str_replace(" ","_",$files_to_zip[0]['title']);
		
		$files_array=array();
			for($z=0;$z<count($files_to_zip);$z++)
			{
				$files_array[]=APP_PATH_ROOT.'articles/'.$files_to_zip[$z]['article_path'];
			}

		//Creating zip
		$files_to_zip[0]['title']=str_replace(" ","_",$files_to_zip[0]['title']);
		$filename=APP_PATH_ROOT.'articles/Zip/'.$files_to_zip[0]['title'].'.zip';
		
		$result = $this->create_zip($files_array,$filename);

			//Download zip file
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$files_to_zip[0]['title'].'.zip');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			readfile($filename);
	}
	
	// Creating zip function
	public function create_zip($files = array(),$destination = '',$overwrite = true)
	{
		  //if the zip file already exists and overwrite is false, return false
		  if(file_exists($destination) && !$overwrite) { return false; }
		  //vars
		  $valid_files = array();
		  
		  //if files were passed in...
		  if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
			  //make sure the file exists
			  if(file_exists($file)) {
				$valid_files[] = $file;
			  }
			}
		  }

		  //if we have good files...
		  if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			  return false;
			}
			//add the files
			foreach($valid_files as $file) {
			  //$zip->addFile($file,$file);
			  $zip->addFile($file, iconv('ISO-8859-1', 'IBM850',basename($file)));
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			//echo $destination;
			//exit;
			//close the zip -- done!
			$zip->close();

			//check to make sure the file exists
			return file_exists($destination);
		  }
		  else
		  {
			return false;
		  }
	}
	
	/* Client invoice page where invoice of published article can be downloaded */	
	public function billingAction()
	{
		if($this->_view->clientidentifier=="")
		{
			$this_url = $_SERVER['REQUEST_URI'];	
			$this->_redirect("/index/index?return_url=".urlencode($this_url));
		}

		$delivery_obj=new Ep_Ao_Delivery();
		$part_obj=new Ep_Ao_Participation();
		$pay_obj=new Ep_Ao_PaymentArticle();
		$user_obj=new Ep_User_User();
			
		//Billing list
		$billings=$pay_obj->Listbilling($this->_view->clientidentifier);//print_r($billings);
			for($b=0;$b<count($billings);$b++)
			{
				$clientprofs=$user_obj->getClientdetails($this->_view->clientidentifier); 
				$billings[$b]['client_firstname']=$clientprofs[0]['first_name'];
			}
		
			if(count($billings)>0)
			{	
					$page = $this->_getParam('page',1);
					$paginatorq = Zend_Paginator::factory($billings);
					$paginatorq->setItemCountPerPage(10);
					$paginatorq->setCurrentPageNumber($page);
					$patterns='/[? &]page=[0-9]{1,2}/';
					$replace="";
					$this->_view->billings = $paginatorq;
					$this->_view->pages = $paginatorq->getPages();
					$this->_view->pageURL=preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
					
			}
		//$this->_view->billings=$billings;
		
		//Current Quotes
		$quoteslist=$delivery_obj->currentquotes($this->_view->clientidentifier);
		$quotes=array();
		$n=0;
		for($q=0;$q<count($quoteslist);$q++)
		{
			if($quoteslist[$q]['publish']=="NO")
			{
				$quotes[$n]['id']=$quoteslist[$q]['id'];
				$quotes[$n]['title']=$quoteslist[$q]['title'];
				$quotes[$n]['valid']=$quoteslist[$q]['valid'];
				$quotes[$n]['partcount']=$quoteslist[$q]['partcount'];
				$quotes[$n]['participations']=$quoteslist[$q]['participations'];
				$n++;
			}
		}
		
		$this->_view->quotes=$quotes;
		
		//Writers worked
		$writers=$part_obj->clientWriters($this->_view->clientidentifier);

		for($w=0;$w<count($writers);$w++)
		{
			$writers[$w]['name']=strtolower($writers[$w]['first_name']).'&nbsp;'.ucfirst(substr($writers[$w]['last_name'],0,1));
			$writers[$w]['profileimage']=$this->getContribpic($writers[$w]['user_id'],'profile');
		}
		$this->_view->writers=$writers;
		$this->_view->writerscount=count($writers);
	
		$this->_view->page_title="edit-place : Espace client";
		$this->_view->render("Client_billing");
	}
	
	/* Download invoice function which generates pdf containing facture details*/
	public function downloadinvoiceAction()
	{
		$invoiceid= $_REQUEST['id'];
        $invoicedir='/home/sites/site7/web/FO/invoice/client/'.$this->_view->clientidentifier.'/';
		   
		if(!file_exists($invoicedir.'/'.$invoiceid.'.pdf'))
		{
			$pay_obj = new Ep_Ao_PaymentArticle();
			$art_obj = new Ep_Ao_Article();
			$country_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);

		//Payment details
		$payment=$pay_obj->getpaymentdetails($invoiceid);
		
			if($payment=="NO")
			{
				$this->_redirect("/client/billing");
				exit;
			}

		   
		   if($payment[0]['invoice_generated']=="no")
		   {
			   //Update invoice generated
			   $art_obj=new Ep_Ao_Article();
				$art_array=array("invoice_generated"=>"yes");
				$art_where=" id='".$payment[0]['aid']."'";
			   $art_obj->updateArticle($art_array,$art_where);
			   
			   //Insert Recent Activities
				$act_obj=new Ep_User_RecentActivities();
				$ract=array("type" => "invoice","user_id"=>$this->_view->clientidentifier,"activity_by"=>$this->_view->clientidentifier,"article_id"=>$payment[0]['aid']);
				$act_obj->insertRecentActivities($ract);
		   }
		   
		   //Dates
			//setlocale(LC_TIME, 'fr_FR');
            $date_invoice_full= strftime("%e %B %Y",strtotime($payment[0]['delivery_date']));
            $date_invocie = date("d/m/Y",strtotime($payment[0]['delivery_date']));
            $date_invoice_ep=date("Y/m",strtotime($payment[0]['delivery_date']));


		   //Address
		    $profileinfo=$pay_obj->getClientdetails($this->_view->clientidentifier);
			$address=$profileinfo[0]['company_name'].'<br>';
            $address.=$profileinfo[0]['address'].'<br>';
            $address.=$profileinfo[0]['zipcode'].'  '.$profileinfo[0]['city'].'  '.$country_array[$profileinfo[0]['country']].'<br>';

			//Invoice details
			$invoice_details_pdf=' 
				<div align="center" style="font-size:16pt;"><b>Appel d\'offres : '.$payment[0]['title'].'</b></div>
					<table class="change_order_items">
                                    <tbody>
                                        <tr>
                                            <th>Description </th>
                                            <th>Unit Price</th>
											<th>Amount</th>
                                        </tr>';

               //echo $payment[0]['currency'];
				if($payment[0]['currency']=='euro')
					$currency='&euro;';    
				else
					$currency='&pound;'; 
					
			   $total=0;
                if($payment[0]['amount']!="")
					$total=number_format($payment[0]['amount'],2);
				else
					$total=number_format($payment[0]['amount_paid'],2);
		
				
				$invoice_details_pdf.='<tr>
                                            <td>'.$payment[0]['title'].'</td>
                                            <td class="change_order_total_col">'.number_format($total,2,',','').'</td>
											<td class="change_order_total_col">'.number_format($total,2,',','').'</td>
                                            </tr>';

			   
                $invoice_details_pdf.='<tr>
											<td style="border-top:1pt solid black;text-align:right;margin-right:10px;font-size: 12pt;" colspan="2">
												Total
											</td>
											<td style="border-top:1pt solid black;font-size: 12pt;" class="change_order_total_col" >
												'.number_format($total,2,',','').'
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
														<td class="change_order_total_col" style="border-top:1pt solid black;text-align:right;font-size: 12pt;">'.number_format($tax,2,',','').$currency.'  </td>
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
											<td style="width:18%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >'.number_format(($total+$tax),2,',','').$currency.'</td>
										</tr>
									</table>';
			if(!is_dir($invoicedir))
			{
               mkdir($invoicedir,0777);
               chmod($invoicedir,0777);
			}
			
            require_once('/home/sites/site7/web/FO/dompdf/dompdf_config.inc.php');
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

					
				   //$old_limit = ini_set("memory_limit", "16M");

					 $dompdf = new DOMPDF();
					 $dompdf->load_html( $html);
					 $dompdf->set_paper("a4");
					 $dompdf->render();


					  $pdf = $dompdf->output();

			file_put_contents($invoicedir.'/'.$invoiceid.'.pdf', $pdf);
		}
			
		header('Content-type: application/pdf');
		header('Content-disposition: attachment;filename='.$invoiceid.'.pdf');
		ob_clean();
		flush();
		readfile($invoicedir.'/'.$invoiceid.'.pdf');
	}
	
	/**********************************************Mail code ****************************************/
	public function messageToEPMail($receiverId,$mailid,$parameters)
    {
        $automail=new Ep_Ticket_AutoEmails();
	
		//Parameters
		$article=$parameters['article'];
		$articlelink=$parameters['articlelink'];
		$AO_end_date=$parameters['AO_end_date'];
		$ongoinglink=$parameters['ongoinglink'];
		$clientcontact=$parameters['clientcontact'];
		$royalty=$parameters['royalty'];
		$resubmission='<b>'.$parameters['resubmission'].'</b>';
		$AO_title=$parameters['AO_title'];
		$client=$parameters['client'];
		$clientcomment=$parameters['clientcomment'];
		$extend_hours=$parameters['extend_hours'];
		$sitelink=$parameters['sitelink'];
		$writercount=$parameters['writercount'];
		$deliverlink=$parameters['deliverlink'];
		$submitdate_bo='<b>'.$parameters['submitdate_bo'].'</b>';
		$aowithlink='<b>'.$parameters['aowithlink'].'</b>';
		$articleclient_link='<b>'.$parameters['articleclient_link'].'</b>';
		$client=$parameters['client'];
		
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
        $sender=$this->adminLogin->userId;
        $sender='111201092609847';
        $ticket=new Ep_Ticket_Ticket();
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

					$automail=new Ep_Ticket_AutoEmails();
					$UserDetails=$automail->getUserType($receiverId);
                    $email=$UserDetails[0]['email'];
                    $password=$UserDetails[0]['password'];
                    $type=$UserDetails[0]['type'];
					$this->mail_from= $this->getConfiguredval("mail_from");

					
					if($UserDetails[0]['subscribe']=='yes')
                    {
						if(!$object)
							$object="Vous avez reçu un email-Edit-place";

						$object=strip_tags($object);

						if($UserDetails[0]['type']=='client')
						{
							$text_mail="<p>Dear client,<br><br>
											You have received an email from Edit-place&nbsp;!<br><br>
											Thank you <a href=\"http://ep-test.edit-place.co.uk/client/emaillogin?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">click here</a> to Read.<br><br>
											Yours sincerely,<br>
											<br>
											The Edit-place team<br><br>
											You do not wish to receive notifications ? <a href=\"http://".$_SERVER['HTTP_HOST']."/user/alert-unsubscribe?uaction=unsubscribe&user=".MD5('ep_login_'.$email)."\">Click here</a>.</p>"
										;
						}
						else if($UserDetails[0]['type']=='contributor')
						{
							$text_mail="<p>Dear writer,<br><br>
											You have received an email from Edit-place&nbsp;!<br><br>
											Thank you <a href=\"http://ep-test.edit-place.co.uk/client/emaillogin?user=".MD5('ep_login_'.$email)."&hash=".MD5('ep_login_'.$password)."&type=".$type."&message=".$messageId."&ticket=".$ticket_id."\">click here</a> to Read.<br><br>
											Yours sincerely,<br>
											<br>
											The Edit-place team<br><br>
											You do not wish to receive notifications ? <a href=\"http://".$_SERVER['HTTP_HOST']."/user/alert-unsubscribe?uaction=unsubscribe&user=".MD5('ep_login_'.$email)."\">Click here</a>.</p>"
										;
						}
						
						$content = $this->autoLoginlinkReplace($content, $email, $password, $type);
						
						$content.="<br><br>
								You do not wish to receive notifications ? <a href=\"http://ep-test.edit-place.co.uk/user/alert-unsubscribe?uaction=unsubscribe&user=".MD5('ep_login_'.$email)."\">Click here</a>";
						if($UserDetails[0]['alert_subscribe']=='yes')
						{		
							
								$mail = new Zend_Mail();
								$mail->addHeader('Reply-To',$this->mail_from);
								$mail->setBodyHtml($content)
									 ->setFrom($this->mail_from,'Support Edit-place')
									 ->addTo($UserDetails[0]['email'])
									 //->addCc('kavithashree.r@gmail.com')
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
	
	 ///to send mail to personal mail ids with autologin option
    public function autoLoginlinkReplace($content, $email, $password, $type)
    {
        // preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $content, $matches);
        // $urls = $matches[2];
        preg_match_all("/(?<=href=(\"|'))[^\"']+(?=(\"|'))/",$content,$matches);
        $urls = $matches[0]; print_r($urls);
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
                "/contrib/royalties",                     "http://ep-test.edit-place.co.uk/contrib/royalties"   );

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
	
	/* Autologin to client account from external links sent through mail*/
	public function emailloginAction()
    {
        $email_params_login=$this->_request->getParams();
        if($email_params_login['user'] && $email_params_login['hash'] && $email_params_login['type'])
        {
            $user_obj=new Ep_User_User();

            $encrypted_email=$email_params_login['user'];
            $encrypted_password=$email_params_login['hash'];
            $type=$email_params_login['type'];
			
			//checking login details sent as parameters in link
            $details=$user_obj->checkEmailLoginDetails($encrypted_email,$encrypted_password,$type);
            if($details!="NO" && is_array($details))
            {
                if($details[0]['type']=='client')
                {
                
                    $this->EPClient_reg = Zend_Registry::get('EP_Client');
                    $this->EPClient_reg->clientidentifier =$details[0]['identifier'];
                    $this->EPClient_reg->clientemail =$details[0]['email'];

					if($email_params_login['poll']!='')
					{
						$this->_redirect("/client/devispremium?id=".$email_params_login['poll']);
					}
					else
					{
						$message=$email_params_login['message'];
						$ticket=$email_params_login['ticket'];

						if($message!='' && $ticket!='')
						{
							$this->_redirect("/client/view-mail?type=inbox&message=".$message."&ticket=".$ticket);
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

                    if($email_params_login['poll']!='')
					{
						$this->_redirect("/contrib/home");
					}
					else
					{
						$message=$email_params_login['message'];
						$ticket=$email_params_login['ticket'];

						if($message!='' && $ticket!='')
						{
							$this->_redirect("/contrib/view-mail?type=inbox&message=".$message."&ticket=".$ticket);
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
	
	/************************************************************mail box functionalities*************************************************/

    //Compose email
    public function composeMailAction()
    {
        if($this->_helper->EpCustom->checksessionclient())
        {
             $ticket=new Ep_Ticket_Ticket();
            $mail_params=$this->_request->getParams();
            $contrib_identifier=$this->clientidentifier;

            /**profile IMage*/
              $this->_view->profile_picture=$this->getPicPath($contrib_identifier);  

            //for disaplying action messages
            if($this->_helper->FlashMessenger->getMessages()) {
                    $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }

            $mail=new Ep_Ticket_Ticket();
            $get_contacts=$mail->getContacts('contributor');
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
            /**Edit-Place Contacts**/
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
                $this->_view->Cients_contacts=$clients_contacts;
            if($mail_params['clientid'])
                $this->_view->toClientId=$mail_params['clientid'];


            //classified tickets Count
            $class_tickets= $ticket->getClassifyTicket($contrib_identifier);
            if(is_array($class_tickets))
            $this->_view->class_ticket_count=count($class_tickets);


            $this->_view->meta_title="Mailbox";  
            $this->render("Client_compose_mail");
        }  
    }
    //send mail action
    public function sendMailAction()
    {
        if($this->_helper->EpCustom->checksessionclient())
        {
            if($this->_request-> isPost())
            {
                
                $sender=$this->clientidentifier;
                $ticket_params=$this->_request->getParams();
                $ticket=new Ep_Ticket_Ticket();
                $user_obj = new EP_Contrib_Registration();
                if($ticket_params["sendto"])
                {
                  $recipient=$ticket_params["sendto"];
                  $userInfo=$user_obj->getUserInfo($recipient);
                } 
                $ticket->sender_id=$sender;
                

                if($recipient && $userInfo[0]['type']=='contributor')
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
                   
                    if($ticket->insert())
                    {
                        $ticket_id=$ticket->getIdentifier();
                        $message=new Ep_Ticket_Message();
                        $message->ticket_id=$ticket_id;
                        $message->content=($this->utf8dec($ticket_params["mail_message"]));
                        $message->type='0' ;
                        $message->status='0';
                        $message->created_at=$ticket->created_at;
                        if($userInfo[0]['type']=='contributor' && $_REQUEST['ordercl']!="y1s")
                        {
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
                            if($recipient && $userInfo[0]['type']!='contributor' && $userInfo[0]['type']!='contributor')
                            {
                                $auto_mail=new Ep_Ticket_AutoEmails();
                                $auto_mail->sendNotificationEmail();
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
                            //$this->_helper->FlashMessenger('Message envoy&eacute;.');
                            $this->_helper->FlashMessenger("Your message has been sent. It will be treated by our staff within 24 hours maximum");
                            $this->_redirect("/client/sentbox");
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
        if($this->_helper->EpCustom->checksessionclient())
        {
            $contrib_identifier=$this->clientidentifier;

            $ticket=new Ep_Ticket_Ticket();
            $inbox_messages= $ticket->getUserInbox('client',$contrib_identifier);
            if($this->_helper->FlashMessenger->getMessages()) {
                $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }
            //$inbox_messages=print_r($inbox_messages,true);
            if(is_array($inbox_messages) && count($inbox_messages)>0)
            {
                $i=0;
                foreach($inbox_messages as $message)
                {
                    
                    $message['content']=$this->html2txt($message['content']);
                    if(strlen($message['content']) > 100)
                    {
                        $inbox_messages[$i]['text_message']=strip_tags(stripslashes(substr(trim($message['content']),0,99)));
                        $inbox_messages[$i]['read_more']=TRUE;
                    }
                    else{
                        $inbox_messages[$i]['text_message']=strip_tags(stripslashes(trim($message['content'])));
                        $inbox_messages[$i]['read_more']=FALSE;                        
                    }                    

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
                $this->_view->Inbox_Messages="No message";

            //classified tickets Count
            $class_tickets= $ticket->getClassifyTicket($contrib_identifier);
            if(is_array($class_tickets))
            $this->_view->class_ticket_count=count($class_tickets);  

            $this->_view->meta_title="Mailbox";

            $this->render("Client_inbox");
        }
    }  
    
	//converting html to text removing html tags
	function html2txt($document){
        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                       '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                       '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                       '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
        );
        $text = preg_replace($search, '', $document);
        return $text;
    }
    
	//sentbox action
    public function sentboxAction()
    {
        if($this->_helper->EpCustom->checksessionclient())
        {
            $ticket=new Ep_Ticket_Ticket();
            $contrib_identifier=$this->clientidentifier;
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
                    $message['content']=$this->html2txt($message['content']);
                    if(strlen($message['content']) > 100)
                    {
                        $sent_messages[$i]['text_message']=stripslashes(substr($message['content'],0,99));
                        $sent_messages[$i]['read_more']=TRUE;
                    }
                    else{
                        $sent_messages[$i]['text_message']=stripslashes($message['content']);
                        $sent_messages[$i]['read_more']=FALSE;
                    }
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
                $this->_view->sent_messages="No message"; 

             //classified tickets Count
            $class_tickets= $ticket->getClassifyTicket($contrib_identifier);
            if(is_array($class_tickets))
            $this->_view->class_ticket_count=count($class_tickets); 
           
            $this->_view->meta_title="Mailbox";
            $this->render("Client_sentbox");
        } 
    }    
   
    //classify box
    public function classifyboxAction()
    {
        if($this->_helper->EpCustom->checksessionclient())
        {
            $contrib_identifier=$this->clientidentifier;
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
                $this->_view->ticket_classes="No message";

            //classified tickets Count
            
            if(is_array($class_ticket))
            $this->_view->class_ticket_count=count($class_ticket); 
            
            $this->_view->meta_title="Mailbox";
            $this->render("Client_classifybox");
            
        } 
    }

    //view email
    public function viewMailAction()
    {
        if($this->_helper->EpCustom->checksessionclient())
        {
            $contrib_identifier=$this->clientidentifier;

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
                                    //$attachment_file=str_replace(APP_PATH_ROOT,"http://edit-place.oboulo.com/FO/",$this->attachment_path.$viewMessage[0]['attachment']);
                                    //echo  $attachment_file;
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
                    $this->_redirect("/client/inbox");
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
                                        //$attachment_file=str_replace(APP_PATH_ROOT,"http://edit-place.oboulo.com/FO/",$this->attachment_path.$viewMessage[0]['attachment']);
                                        //echo  $attachment_file;
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
                        $this->_redirect("/client/sentbox");
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
                $this->render("Client_view_mail");
             
        } 
    }

    //send reply mail
    public function sendReplyAction()
    {
        if($this->_helper->EpCustom->checksessionclient())
        {
            if($this->_request-> isPost())
            {
              $ticket_params=$this->_request->getParams();
              $identifier=$this->clientidentifier;
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
                          $message->approved='yes';
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
                     // echo "<pre>";print_r($message);exit;
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
                          $this->_redirect("/client/sentbox");
                      }
                  }
                  catch(Exception $e)
                  {
                      echo $e->getMessage();exit;
                  }
              }
              else
                  $this->_redirect("/client/compose-mail");
            }
            else
                $this->_redirect("/client/compose-mail");
              
        }      
    }
    
	public function classifyAction()
    {
        if($this->_helper->EpCustom->checksessionclient())
        {
            $mail_params=$this->_request->getParams();
            $ticket_Identifier=$mail_params['ticket'];
            $identifier=$this->clientidentifier;
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
                $this->_helper->FlashMessenger('Message rated.');
                $this->_redirect("/client/inbox");

            }
            else
                $this->_redirect("/client/inbox");
        }    
    }
    
	/*Function to get the picture of a client**/
    public function getPicPath($identifer,$action='home')
    {
        $app_path=APP_PATH_ROOT;
        $profiledir=$this->_config->path->client_profile_pic_path.$identifer.'/';
        /*if($action=='home')
            $pic=$identifer."_h.jpg";
        else
            $pic=$identifer."_p.jpg";*/
			
		  $pic=$identifer."_global.png";	
			
        if(file_exists($app_path.$profiledir.$pic))
        {
            $pic_path="/FO/".$profiledir.$pic;
        }
        else
        {
            if($action=='home')
				$pic_path="/FO/images/customer-no-logo.png";
			else
				$pic_path="/FO/images/customer-no-logo.png";
        }
        return $pic_path;
    }
	
	/*Function to get the picture of a Contributor**/
    public function getContribpic($identifer,$action='home')
    {
        $app_path=APP_PATH_ROOT;
        $profiledir='profiles/contrib/pictures/'.$identifer.'/';
        if($action=='home')
            $pic=$identifer."_h.jpg";
        else
            $pic=$identifer."_p.jpg";
			
		if(file_exists($app_path.$profiledir.$pic))
        {
            $pic_path="/FO/".$profiledir.$pic;
        }
        else
        {
            if($action=='home')
				$pic_path="/FO/images/editor-noimage.png";
			else
				$pic_path="/FO/images/editor-noimage.png";	
				
			
		}
        return $pic_path;
    }
    
	/*Function to get the profile pic of commenter**/
    public function getCommentPic($identifer,$type)
    {
        $app_path=APP_PATH_ROOT;
        if($type=="contributor")
			$profiledir='profiles/contrib/pictures/'.$identifer.'/';
		else	
			$profiledir='profiles/clients/logos/'.$identifer.'/';
			
        if($type=="contributor")
			$pic=$identifer."_h.jpg";
		else
			$pic=$identifer."_global.png";
      
		if(file_exists($app_path.$profiledir.$pic))
            $pic_path="/FO/".$profiledir.$pic;
        else
		{
            if($type=="contributor")
				$pic_path="/FO/images/ep-feed-logo.png";
			else
				$pic_path="/FO/images/customer-no-logo.png";
		}
        return $pic_path;
    }
	
	/* Pop up with writer profile & participation details*/
	public function userprofileAction()
	{
		if($this->_view->clientidentifier=="")
		{
			echo "expired";
			exit;
		}
		else
		{	
			$user_obj=new Ep_Ao_Participation();
			$user_details=$user_obj->getcontribProfileDetails($_POST['partid']);
		
			$lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
				$this->_view->language_array=$lang_array;
			$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
				$this->_view->category_array=$cat_array;
		
			$user_details[0]['first_name']=strtolower($user_details[0]['first_name']);
			$user_details[0]['last_name']=strtolower($user_details[0]['last_name']);
			$user_details[0]['name']=ucfirst($user_details[0]['first_name']).'&nbsp;'.ucfirst(substr($user_details[0]['last_name'],0,1));
			$user_details[0]['profilepic']=$this->getContribpic($user_details[0]['user_id'],'profile');
			//Age
			$user_details[0]['age']=$user_details[0]['curryear']-$user_details[0]['byear'];
		
			//Language
			if($user_details[0]['language_more']!="")
			{
				//forming array with lang id as index and percent as value
				$str=explode("\"",$user_details[0]['language_more']);
				$language=array();
				for($s=0;$s<count($str);$s=$s+4)
				{
					$index=$str[$s+1];
					if($index!="")
						$language[$index]=$str[$s+3];
				}
			}
			$this->_view->langpercent=$language;
			$user_details[0]['langstr']=$lang_array[$user_details[0]['language']];

			if($user_details[0]['language_more']!=NULL)
			{
				$languagekey=array_keys($language);
				if(count($languagekey)>0)
				{
					for($l=0;$l<count($languagekey);$l++)
						$user_details[0]['langstr'].=", ".$lang_array[$languagekey[$l]];
				}
			}
		
			//Clients
			$carray=array();
			for($c=0;$c<count($user_details[0]['clients']);$c++)
			{
				if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$user_details[0]['clients'][$c]['user_id']."/".$user_details[0]['clients'][$c]['user_id']."_global1.png"))	
					$carray[$user_details[0]['clients'][$c]['company_name']]="/FO/profiles/clients/logos/".$user_details[0]['clients'][$c]['user_id']."/".$user_details[0]['clients'][$c]['user_id']."_global1.png";
				else
					$carray[$user_details[0]['clients'][$c]['company_name']]="/FO/images/customer-no-logo90.png";
			}
			//$user_details[0]['clientlist']=implode(",",$carray);
			$user_details[0]['clientlogo']=$carray;

			//Category
			$user_details[0]['catstr']=$this->getCategoryName($user_details[0]['favourite_category']);
			$user_details[0]['cats']=explode(",",$user_details[0]['favourite_category']);

			if($user_details[0]['category_more']!="")
			{
				//forming array with cat id as index and percent as value
				$str=explode("\"",$user_details[0]['category_more']);
				$category=array();
				for($s=0;$s<count($str);$s=$s+4)
				{
					$index=$str[$s+1];
					if($index!="")
						$category[$index]=$str[$s+3];
				}
			}
			$this->_view->catpercent=$category;
			
			//contrat type
			$contract=array("cdi"=>"CDI","cdd"=>"CDD","freelance"=>"Freelance","interim"=>"Interim");	
			
			//Experience and Education
			$exp_details=$user_obj->getContribexp($_POST['partid'],'job');
				for($x=0;$x<count($exp_details);$x++)
				{
					//setlocale(LC_TIME, "fr_FR");
					$exp_details[$x]['from_month']= strftime('%B', mktime(0, 0, 0, $exp_details[$x]['from_month']));
					$exp_details[$x]['to_month']= strftime('%B', mktime(0, 0, 0, $exp_details[$x]['to_month']));
					$exp_details[$x]['contract']=$contract[$exp_details[$x]['contract']];
				}
			$this->_view->exp_details=$exp_details;
			
			$education_details=$user_obj->getContribexp($_POST['partid'],'education');
				for($x=0;$x<count($education_details);$x++)
				{
					//setlocale(LC_TIME, "fr_FR");
					$education_details[$x]['from_month']= strftime('%B', mktime(0, 0, 0, $education_details[$x]['from_month']));
					$education_details[$x]['to_month']= strftime('%B', mktime(0, 0, 0, $education_details[$x]['to_month']));
				}
			$this->_view->education_details=$education_details;
			
			$this->_view->contribprofile=$user_details;
			$this->_view->render("Client_userprofile");
		}
	}
	
	//User connexion pop up linked to client profile for password edit
	public function userconnexionAction()
	{
		$this->_view->render("Client_userconnexion");
	}
	
	/* not linked anywhere in the site*/
	/* Page to load poll partcipations list, which total display and include/exclude option as in BO*/
	public function devispremiumAction()
	{
		if($this->_view->clientidentifier=="")
		{
			$this_url = $_SERVER['REQUEST_URI'];	
			$this->_redirect("/index/index?return_url=".urlencode($this_url));
		}
		
		$poll_id=$_REQUEST['id'];	
		$poll_obj=new Ep_Poll_Poll();
		$user_obj=new Ep_Ao_Participation();
		$delivery_obj=new Ep_Ao_Delivery();
		
		if($poll_obj->checkPollExists($poll_id,$this->_view->clientidentifier)=="NO")
			$this->_redirect("/client/home");
			
		//Active All
		if($_REQUEST['activate_all']!="")
		{
			for($a=0;$a<count($_REQUEST['contribtype']);$a++)
			{
				$uid=explode("#",$_REQUEST['contribtype'][$a]);
				$poll_obj->pollpartstatus($uid[0],'inactive');
			}	
		}
		
		//Inactive All
		if($_REQUEST['inactivate_all']!="")
		{
			for($a=0;$a<count($_REQUEST['contribtype']);$a++)
			{
				$uid=explode("#",$_REQUEST['contribtype'][$a]);
				$poll_obj->pollpartstatus($uid[0],'active');
			}
		}
		
		$filt="";
		if($_REQUEST['filter']!="")
			$filt=$_REQUEST['filter'];
		
		$lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		$this->_view->language_array=$lang_array;
		
		$pollsdetail=$poll_obj->Polldetail($poll_id);
		$this->_view->pollsdetail=$pollsdetail;
			$lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
				$this->_view->language_array=$lang_array;
			$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
				$this->_view->category_array=$cat_array;
				
		$pollset=$poll_obj->ListPollPartcipation($poll_id,$_REQUEST['sort'],$filt);
		$uarray=array();
			for($p=0;$p<count($pollset);$p++)
			{
				//setlocale(LC_TIME, "fr_FR");
				$pollset[$p]['availability']=strftime("%d %b %Y",strtotime($pollset[$p]['availability']));
				$pollset[$p]['created_at']=strftime("%d %b %Y %Hh%M",strtotime($pollset[$p]['created_at']));
				$pollset[$p]['name']=ucfirst($pollset[$p]['first_name']).'&nbsp;'.ucfirst(substr($pollset[$p]['last_name'],0,1));
				$pollset[$p]['profilepic']=$this->getContribpic($pollset[$p]['user_id'],'home');
				$pollset[$p]['totalparticipation']=$user_obj->participationcount($pollset[$p]['user_id'],'total');
				$pollset[$p]['selectedparticipation']=$user_obj->participationcount($pollset[$p]['user_id'],'selected');
				$pollset[$p]['language']=$user_obj->getLanguage($pollset[$p]['user_id']);
				
				$customerstrust=$user_obj->getcustomersPublished($pollset[$p]['user_id']);
		
				for($c=0;$c<count($customerstrust);$c++)
				{
					if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png"))
						$uarray[$customerstrust[$c]['company_name']]="/FO/profiles/clients/logos/".$customerstrust[$c]['user_id']."/".$customerstrust[$c]['user_id']."_global1.png";
					else
						$uarray[$customerstrust[$c]['company_name']]="/FO/images/customer-no-logo90.png";
				}
				
				if($p!=0)
					$pollset[$p]['previous']=$pollset[$p-1]['user_id'];
				else
					$pollset[$p]['previous']=$pollset[count($pollset)-1]['user_id'];
				
				if($p==count($pollset)-1)
					$pollset[$p]['next']=$pollset[0]['user_id'];
				else
					$pollset[$p]['next']=$pollset[$p+1]['user_id'];
					
			}
		$this->_view->pollendtime=strtotime($pollsdetail[0]['poll_date']);
		$this->_view->polltitle=$pollset[0]['title'];
		$this->_view->pollid=$pollset[0]['id'];
		$this->_view->pollsetcount=count($pollset);
		$this->_view->pollset1=$pollset;
			if(count($pollset)>0)
			{	
					$page = $this->_getParam('page',1);
					$paginatorq = Zend_Paginator::factory($pollset);
					$paginatorq->setItemCountPerPage(10);
					$paginatorq->setCurrentPageNumber($page);
					$patterns='/[? &]page=[0-9]{1,2}/';
					$replace="";
					$this->_view->pollset = $paginatorq;
					$this->_view->pages = $paginatorq->getPages();
					$this->_view->pageURL=preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
			}
		
		$pollprice=$poll_obj->getPollpriceset($poll_id);
		if($pollprice[0]['participation']=="0")
		{
			$pollprice[0]['contrib_percentage']=0;
			$pollprice[0]['maxprice']=0;
			$pollprice[0]['minprice']=0;
			$pollprice[0]['sumprice']=0;
		}
		
		//Customers
		$this->_view->customerstrust=$uarray;
			
		//Current Quotes
		$quoteslist=$delivery_obj->currentquotes($this->_view->clientidentifier);
		$quotes=array();
		$n=0;
			for($q=0;$q<count($quoteslist);$q++)
			{
				if($quoteslist[$q]['publish']=="NO")
				{
					$quotes[$n]['id']=$quoteslist[$q]['id'];
					$quotes[$n]['title']=$quoteslist[$q]['title'];
					$quotes[$n]['valid']=$quoteslist[$q]['valid'];
					$quotes[$n]['partcount']=$quoteslist[$q]['partcount'];
					$quotes[$n]['participations']=$quoteslist[$q]['participations'];
					$n++;
				}
			}
		$this->_view->quotes=$quotes;
		
		$this->_view->pollprice=$pollprice;
		
		//links
		if($_GET['sort']=="dateasc")	
			$datelink="/client/devispremium?id=".$_GET['id']."&sort=datedesc";
		else
			$datelink="/client/devispremium?id=".$_GET['id']."&sort=dateasc";
		
		if($_GET['sort']=="priceasc")	
			$pricelink="/client/devispremium?id=".$_GET['id']."&sort=pricedesc";	
		else
			$pricelink="/client/devispremium?id=".$_GET['id']."&sort=priceasc";
		
		$this->_view->datesort_link=$datelink;
		$this->_view->pricesort_link=$pricelink;
		
		//Favorite contribs
		$this->_view->favarray=array();
		if(count($this->EP_Client->favcontribs)>0)
			$this->_view->favarray=array_keys($this->EP_Client->favcontribs);
			
		$this->_view->now=strtotime("now");
		$this->_view->current_date=date("Y-m-d");
		$this->_view->page_title="edit-place : Espace client quote selection";
		$this->_view->render("Client_devispremium");
	}
	
	/* not linked anywhere in the site*/
	/* Pop up with poll participant profile details*/
	public function devisuserprofileAction()
	{
		if($this->_view->clientidentifier=="")
		{
			echo "expired";
			exit;
		}
		else
		{	
			$user_obj=new Ep_Ao_Participation();
			$user_details=$user_obj->getPollProfileDetails($_POST['partid'],$_POST['pollid']);
		//print_r($user_details);
			$lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
				$this->_view->language_array=$lang_array;
			$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
				$this->_view->category_array=$cat_array;
		
			$user_details[0]['first_name']=strtolower($user_details[0]['first_name']);
			$user_details[0]['last_name']=strtolower($user_details[0]['last_name']);
			$user_details[0]['name']=ucfirst($user_details[0]['first_name']).'&nbsp;'.ucfirst(substr($user_details[0]['last_name'],0,1));
			$user_details[0]['profilepic']=$this->getContribpic($user_details[0]['user_id'],'profile');
			//Age
			$user_details[0]['age']=$user_details[0]['curryear']-$user_details[0]['byear'];
			$user_details[0]['price_user_total']=($user_details[0]['price_user']*100)/$user_details[0]['contrib_percentage'];
			
			if($user_details[0]['valid_date']!="" && $user_details[0]['valid_date']<date("Y-m-d"))
				$user_details[0]['datevalid']="no";
			else
				$user_details[0]['datevalid']="yes";
			
			if($user_details[0]['valid_date']!="")
			{
				//setlocale(LC_TIME, "fr_FR");
				$user_details[0]['valid_until']=strftime("%d %B %Y",strtotime($user_details[0]['valid_date']));
			}	
			else
				$user_details[0]['valid_until']="no";
				
			//Language
			if($user_details[0]['language_more']!="")
			{
				//forming array with lang id as index and percent as value
				$str=explode("\"",$user_details[0]['language_more']);
				$language=array();
				for($s=0;$s<count($str);$s=$s+4)
				{
					$index=$str[$s+1];
					if($index!="")
						$language[$index]=$str[$s+3];
				}
			}
			$this->_view->langpercent=$language;
			$user_details[0]['langstr']=$lang_array[$user_details[0]['language']];

			if($user_details[0]['language_more']!=NULL)
			{
				$languagekey=array_keys($language);
				if(count($languagekey)>0)
				{
					for($l=0;$l<count($languagekey);$l++)
						$user_details[0]['langstr'].=", ".$lang_array[$languagekey[$l]];
				}
			}
		
			//Clients
			$carray=array();
			$uarray=array();
			for($c=0;$c<count($user_details[0]['clients']);$c++)
			{
				if($user_details[0]['clients'][$c]['company_name']!="")
				   $carray[]=$user_details[0]['clients'][$c]['company_name'];
				
				if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$user_details[0]['clients'][$c]['user_id']."/".$user_details[0]['clients'][$c]['user_id']."_global1.png"))
					$uarray[$user_details[0]['clients'][$c]['company_name']]="/FO/profiles/clients/logos/".$user_details[0]['clients'][$c]['user_id']."/".$user_details[0]['clients'][$c]['user_id']."_global1.png";
				else
					$uarray[$user_details[0]['clients'][$c]['company_name']]="/FO/images/customer-no-logo90.png";
			}
			//$user_details[0]['clientcompany']=$carray;
			$user_details[0]['clientlogo']=$uarray;
			$user_details[0]['clientlist']=implode(", ",$carray);
			
			//Category
			$user_details[0]['catstr']=$this->getCategoryName($user_details[0]['favourite_category']);
			
			$user_details[0]['cats']=explode(",",$user_details[0]['favourite_category']);

			if($user_details[0]['category_more']!="")
			{
				//forming array with cat id as index and percent as value
				$str=explode("\"",$user_details[0]['category_more']);
				$category=array();
				for($s=0;$s<count($str);$s=$s+4)
				{
					$index=$str[$s+1];
					if($index!="")
						$category[$index]=$str[$s+3];
				}
			}
			$this->_view->catpercent=$category;
			
			//contrat type
			$contract=array("cdi"=>"CDI","cdd"=>"CDD","freelance"=>"Freelance","interim"=>"Interim");	
			
			//Experience and Education
			$exp_details=$user_obj->getContribexp($_POST['partid'],'job');
				for($x=0;$x<count($exp_details);$x++)
				{
					//setlocale(LC_TIME, "fr_FR");
					$exp_details[$x]['from_month']= strftime('%B', mktime(0, 0, 0, $exp_details[$x]['from_month']));
					$exp_details[$x]['to_month']= strftime('%B', mktime(0, 0, 0, $exp_details[$x]['to_month']));
					$exp_details[$x]['contract']=$contract[$exp_details[$x]['contract']];
				}
			$this->_view->exp_details=$exp_details;
			
			$education_details=$user_obj->getContribexp($_POST['partid'],'education');
				for($x=0;$x<count($education_details);$x++)
				{
					//setlocale(LC_TIME, "fr_FR");
					$education_details[$x]['from_month']= strftime('%B', mktime(0, 0, 0, $education_details[$x]['from_month']));
					$education_details[$x]['to_month']= strftime('%B', mktime(0, 0, 0, $education_details[$x]['to_month']));
				}
			$this->_view->education_details=$education_details;
			
			$this->_view->contribprofile=$user_details;
			$this->_view->render("Client_devisuserprofile");
		}
	}
	
	public function sessionfavcontribsAction()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
		
		if($_REQUEST['contribid']!="")
		{
			if($_REQUEST['action']=='add')
				$this->EP_Client->favcontribs[$_REQUEST['contribid']]=1;
			elseif($_REQUEST['action']=='remove')
				unset($this->EP_Client->favcontribs[$_REQUEST['contribid']]);
		}
		exit;
	}
	
	/* not in use */
	public function addfavoritecontribsAction()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
		
		$fav_obj = new Ep_Ao_Favouritecontributor();
		$poll_obj = new Ep_Poll_Poll();
		
		if(count($this->EP_Client->favcontribs)>0)
		{
			$pollcontribs=array();
			foreach ($this->EP_Client->favcontribs as $keyp => $value)
			{
				$pollcontribs[]=$keyp;
				$fav_obj->addfavcontrib($keyp,$this->_view->clientidentifier);
			}
			
			$poll_obj->updatepollfavcontribs($_REQUEST['poll'],$pollcontribs);
		}
		
		unset($this->EP_Client->favcontribs);
	}
	
	/* To swtich poll participation status between exclude/include*/
	public function pollparticipationstatusAction()
	{
		$poll_obj = new Ep_Poll_Poll();
		$ppstatus=$poll_obj->pollpartstatus($_REQUEST['partid'],$_REQUEST['status']);
		
		if($ppstatus=='active')
		{
			$actvar="active";
			$data[0]['text']='<a href="javascript:void(0);" onClick="pollparticipationactive('.$_REQUEST['partid'].',\''.$actvar.'\');" class="btn btn-small">Exclude</a>';
		}
		else
		{
			$actvar="inactive";
			$data[0]['text']='<a href="javascript:void(0);" onClick="pollparticipationactive('.$_REQUEST['partid'].',\''.$actvar.'\');" class="btn btn-small">Include</a>';
		}
		
		$priceset=$poll_obj->getPollpriceset($_REQUEST['poll']);
			
		if($priceset[0]['participation']!=0 && $priceset[0]['participation']!="")
		{
			$data[0]['max']=$this->zero_cut($priceset[0]['maxprice'],2,$priceset[0]['contrib_percentage']);	
			$data[0]['min']=$this->zero_cut($priceset[0]['minprice'],2,$priceset[0]['contrib_percentage']);	
			$avg=$priceset[0]['sumprice']/$priceset[0]['participation'];	
			$data[0]['avg']=$this->zero_cut($avg,2,$priceset[0]['contrib_percentage']);	
		}
		else
		{
			$data[0]['max']='0';	
			$data[0]['min']='0';	
			$data[0]['avg']='0';
		}
		//print_r($priceset);exit;
		echo json_encode($data);
	}
	
	/* DOwnloading xls with all participant list and details of poll*/
	public function downloadpollxlsAction()
	{
		$poll_id=$_REQUEST['id'];
		$poll_obj = new Ep_Poll_Poll();

		$poll_details=$poll_obj->pollclientdetails($poll_id);
		$poll_contribs=$poll_obj->PollPartcipationsAll($poll_id);
		
		$country_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);
		$language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		
		$downlpath=APP_PATH_ROOT.'poll_xls/'.$poll_id.'/';
		
		$file = $downlpath.$poll_details[0]['title'].'.xls';
                 ob_start();
                 echo '<table border="1"> ';
					echo '<tr>
							<th>Name / first name letter</th>
							<th>Kind</th>
							<th>Price proposed</th>
							<th>Date of creation</th>
							<th>Mother tongue</th>
							<th>City</th>
							<th>Country</th>
							<th>Postal code</th>
							<th>Date of birth</th>
							<th>Studies</th>
							<th>Professional Experience</th>
							<th>Popular categories</th>
							<th>Presentation</th>
						</tr>';
                 
                 for($p=0;$p<count($poll_contribs);$p++)
                 {
                    $name=$poll_contribs[$p]['first_name'].' '.substr($poll_contribs[$p]['last_name'],0,1);
					$poll_contribs[$p]['self_details']=str_replace("<br />","",$poll_contribs[$p]['self_details']);
					
					if($poll_contribs[$p]['initial']=='mr')
						$initial="M";
					else
						$initial="F";
					 
					//Job
					$jobdetails=$poll_obj->pollcontribjob($poll_contribs[$p]['user_id']);
					
					//Education
					$edudetails=$poll_obj->pollcontribeducation($poll_contribs[$p]['user_id']);
					$education="";	
						for($e=0;$e<count($edudetails);$e++)
						{
							if($e>0)
								$education.=" / ";
								
							$education.=$edudetails[$e]['title'].", ".$edudetails[$e]['institute'];
						}
					echo '<tr>
								<td valign="top">'.$name.'</td>
								<td valign="top">'.$initial.'</td>
								<td valign="top">'.$poll_contribs[$p]['price_user'].'</td>
								<td valign="top">'.$poll_contribs[$p]['created'].'</td>
								<td valign="top">'.$language_array[$poll_contribs[$p]['language']].'</td>
								<td valign="top">'.$poll_contribs[$p]['city'].'</td>
								<td valign="top">'.$country_array[$poll_contribs[$p]['country']].'</td>
								<td valign="top">'.$poll_contribs[$p]['zipcode'].'</td>
								<td valign="top">'.$poll_contribs[$p]['dob1'].'</td>
								<td valign="top">'.$education.'</td>
								<td valign="top">'.$jobdetails[0]['title'].'</td>
								<td valign="top">'.$this->getCategoryName($poll_contribs[$p]['favourite_category']).'</td>
								<td valign="top">'.stripslashes($poll_contribs[$p]['self_details']).'</td>
							</tr>';
                     
                 }
                 echo '</table>';
                 
				 $poll_details[0]['title']=str_replace(" ","_",$poll_details[0]['title']);
				 $poll_details[0]['first_name']=str_replace(" ","_",$poll_details[0]['first_name']);
				 
                 $content = ob_get_contents();
                 ob_end_clean();
                 header("Expires: 0");
                 header("Cache-Control: post-check=0, pre-check=0", false);
                 header("Pragma: no-cache");  header("Content-type: application/vnd.ms-excel;charset:UTF-8");
                 header('Content-length: '.strlen($content));
                 header('Content-disposition: attachment; filename='.$poll_details[0]['title'].'-'.$poll_details[0]['first_name'].'.xls');
                 echo $content;
                 exit;
		
	}
	
	/* Page to view newsletter content when it is not clear in the email content, link will be provided on top of every newsletter mail*/
	public function newsletterAction()
	{
		$nl_obj=new Ep_User_DailyNewsletter();
		$user_obj=new Ep_User_User();
		
		$date=date("Y-m-d",$_REQUEST['stamp']);
		//Newsletter Details
		$nlDetail=$nl_obj->getNLByDate($date);
		//Display content
		//echo "<!DOCTYPE html>";
		echo $nlDetail[0]['content'];
		
		//User details
		$wrDetails=$user_obj->getContributorDetail($_REQUEST['wrid']);
			if($wrDetails[0]['first_name']!='')
				$name=ucfirst($wrDetails[0]['first_name']).'&nbsp;'.ucfirst($wrDetails[0]['last_name']);
			else
			{
				$mailname=explode("@",$wrDetails[0]['email']);
				$name=$mailname[0];
			}
			
			if($wrDetails[0]['profile_type']=='senior')
				$profilesrc="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-senior-header.png";
			elseif($wrDetails[0]['profile_type']=='junior')	
				$profilesrc="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-junior-header.png";	
			else
				$profilesrc="http://ep-test.edit-place.co.uk/FO/images/newsletter/tag-debutant-header.png";	
		$this->_view->wrname=$name;
		$this->_view->wrprofile=$profilesrc;
		$this->_view->nl_id=$_REQUEST['nid'];
		
		$this->_view->user=MD5('ep_login_'.$wrDetails[0]['email']);
		$this->_view->password=MD5('ep_login_'.$wrDetails[0]['password']);
		$this->_view->type=$wrDetails[0]['type'];
		$this->_view->todaydate=date("Y-m-d");
		
		$this->_view->render("Newsletter");
	}
	
	/* Quotes creation step1 - here we can select between liberte/premium type, language and number of articles*/
	public function quotes1Action()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
		if($_REQUEST['new']==1)
		{
			unset($this->EP_Client->funnel_1);
			unset($this->EP_Client->funnel_2);
		}
		$client_obj=new Ep_User_Client();
		$this->_view->lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		
		$delivery_obj= new Ep_Ao_Delivery();
		
		//Duplicate Mission
		if($_REQUEST['duplicate_mission']!="")
		{
			$deliverydetails=$delivery_obj->DeliveryPremiumdetails($_REQUEST['editliberte']);
			//$this->_view->title=$deliverydetails[0]['title'];
			
			$this->EP_Client->funnel_1['con_type']=$deliverydetails[0]['con_type'];
			if($deliverydetails[0]['con_type']=="writing")
				$this->EP_Client->funnel_1['writing_lang']=$deliverydetails[0]['from_language'];
			else
			{
				$this->EP_Client->funnel_1['translation_from']=$deliverydetails[0]['from_language'];
				$this->EP_Client->funnel_1['translation_to']=$deliverydetails[0]['to_language'];
			}
			$this->EP_Client->funnel_1['quotetype']=explode("|",$deliverydetails[0]['type']);
			$this->EP_Client->funnel_1['textforother']=$deliverydetails[0]['other_type'];
			
			if($deliverydetails[0]['dontknowcheck']=="yes")
				$this->EP_Client->funnel_1['dontknowcheck']="1";
			else
			{
				$articlenumarr=explode("|",$deliverydetails[0]['total_article']);
				$frequencyarr=explode("|",$deliverydetails[0]['frequency']);
				$i=0;
				foreach($this->EP_Client->funnel_1['quotetype'] as $quote)
				{
					$articlenum[$quote]=$articlenumarr[$i];
					$frequency[$quote]=$frequencyarr[$i];
					$i++;
				}
				$this->EP_Client->funnel_1['articlenum']=$articlenum;
				$this->EP_Client->funnel_1['frequency']=$frequency;
			}
			
			if($deliverydetails[0]['objective']!="")
			{
				$this->EP_Client->funnel_1['objectives']=$deliverydetails[0]['objective'];
			//	$this->EP_Client->funnel_1['objOtherText']=$deliverydetails[0]['other_objective'];
			}
			
			//spec files
			$this->EP_Client->funnel_2['title']=$deliverydetails[0]['dtitle']; 
			$this->EP_Client->funnel_2['files']=explode("|",utf8_encode($deliverydetails[0]['file_name'])); 
			
			$this->EP_Client->funnel_2['delivery_option']=$deliverydetails[0]['submit_option']; 
			if($deliverydetails[0]['submit_option']=="min")
				$this->EP_Client->funnel_2['deliverytime']=$deliverydetails[0]['senior_time']; 
			elseif($deliverydetails[0]['submit_option']=="hour")
				$this->EP_Client->funnel_2['deliverytime']=($deliverydetails[0]['senior_time']/60); 
			else
				$this->EP_Client->funnel_2['deliverytime']=($deliverydetails[0]['senior_time']/(60*24)); 
			
			if($deliverydetails[0]['AOtype']=="private")
			{
				$this->EP_Client->funnel_2['privatecontrib']="checked";
				$this->EP_Client->funnel_2['contribselected']=explode(",",$deliverydetails[0]['contribs_list']);
			}
			if($deliverydetails[0]['price_min']!="")
			{
				$this->EP_Client->funnel_2['price_min_total']=$deliverydetails[0]['price_min'];
				$this->EP_Client->funnel_2['price_max_total']=$deliverydetails[0]['price_max'];
			}
			
			if($_REQUEST['createprivate']!="")
			{
				$part_obj=new Ep_Ao_Participation();
				$this->EP_Client->funnel_2['privatecontrib']="checked";
				$this->EP_Client->funnel_2['contribselected']=array("0"=>$_REQUEST['createprivate']);  
				//Insert favourite contributor
				$fav_obj = new Ep_Ao_Favouritecontributor();
				$fav_obj->addfavcontrib($_REQUEST['createprivate'],$this->_view->clientidentifier);
				
				//ArticleHistory Insertion
				$hist_obj = new Ep_Article_ArticleHistory();
				$action_obj = new Ep_Article_ArticleActions();
				$history=array();
				$history['user_id']=$this->_view->clientidentifier;
				$history['article_id']=$_REQUEST['editliberte'];
				$history['stage']='order';
				$history['action']='client_private';
					$sentence=$action_obj->getActionSentence(56);
					$client_name='<b>'.$this->_view->clientname.'</b>';
						$contact=$part_obj->writercontact($_REQUEST['createprivate']);
						$contactname=ucfirst($contact[0]['first_name']).'&nbsp;'.ucfirst(substr($contact[0]['last_name'],0,1));	
					$contributor_name='<a class="writer" href="/user/contributor-edit?submenuId=ML2-SL7&tab=viewcontrib&userId='.$_REQUEST['editliberte'].'" target=_blank""><b>'.$contactname.'</b></a>';
					$actionmessage=strip_tags($sentence);
					eval("\$actionmessage= \"$actionmessage\";");
				$history['action_sentence']=$actionmessage;
				$hist_obj->insertHistory($history);
			}
		}
		
		if($this->EP_Client->funnel_1)
		{
			$this->_view->con_type=$this->EP_Client->funnel_1['con_type'];
			$this->_view->writing_lang=$this->EP_Client->funnel_1['writing_lang'];
			$this->_view->translation_from=$this->EP_Client->funnel_1['translation_from'];
			$this->_view->translation_to=$this->EP_Client->funnel_1['translation_to'];
			
			$this->_view->quotetype=$this->EP_Client->funnel_1['quotetype'];
			$this->_view->textforother=$this->EP_Client->funnel_1['textforother'];
			$this->_view->articlenum=$this->EP_Client->funnel_1['articlenum'];
			$this->_view->frequency=$this->EP_Client->funnel_1['frequency'];
			$this->_view->dontknowcheck=$this->EP_Client->funnel_1['dontknowcheck'];
			$this->_view->objectives=$this->EP_Client->funnel_1['objectives'];
			//$this->_view->objOtherText=$this->EP_Client->funnel_1['objOtherText'];
		}
		else
		{
			$this->_view->quotetype=array();
			//$this->_view->objectives=array();
		}
		
		if($this->_view->clientidentifier!="")
		{
			$clientprofile=$client_obj->checkclientprofile($this->_view->clientidentifier);
			/*if($clientprofile[0]['category']!="" && $clientprofile[0]['job']!="")
				$this->_view->profilecomplete="yes";
			else
				$this->_view->profilecomplete="no";*/
			$this->_view->clientcategory=$clientprofile[0]['category'];
		}
		$this->_view->pagemenu=1;
		$this->_view->render("Client_quotes1");
	}
	
	/* Quotes creation step2 for premium - here if a quote is not concluded as premium/liberte in step1, we decide it based on certain criterias 
	and it is automatically redirected to quotes creation step2 liberte if it is liberte */
	public function quotes2Action()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
		$this->_view->category_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		
		$params1=$this->_request->getParams();	
		if($params1['con_type']!="")
			$this->EP_Client->funnel_1=$params1;
			
		//switch premium or liberte
		if($params1['objectives']=="liberte" || $params1['objectives']=="liberteprivate")
			$this->_redirect("/client/quotes2liberte");
		elseif($params1['objectives']=="dontknow")
		{
			if($this->EP_Client->funnel_1['con_type']=='writing' && $this->EP_Client->funnel_1['writing_lang']=='uk')
			{
				$premium=0;
				foreach($this->EP_Client->funnel_1['articlenum'] as $numkey=>$numitem)
				{
					if($this->EP_Client->funnel_1['frequency'][$numkey]=="once")
					{
						if($this->EP_Client->funnel_1['articlenum'][$numkey]>=20)
							$premium=1;
					}
					elseif($this->EP_Client->funnel_1['frequency'][$numkey]=="day")
					{
						if($this->EP_Client->funnel_1['articlenum'][$numkey]>2)
							$premium=1;
					}
					elseif($this->EP_Client->funnel_1['frequency'][$numkey]=="week")
					{
						if(($this->EP_Client->funnel_1['articlenum'][$numkey]/7)>2)
							$premium=1;
					}
					elseif($this->EP_Client->funnel_1['frequency'][$numkey]=="month")
					{
						if(($this->EP_Client->funnel_1['articlenum'][$numkey]/30)>2)
							$premium=1;
					}				
				}
				
				if($premium==0)
					$this->_redirect("/client/quotes2liberte");
			}
		}
			
			
		if($this->EP_Client->funnel_1['con_type']=="")
			$this->_redirect("/client/quotes1");
			
		$client_obj=new Ep_User_Client();
		if($this->_view->clientidentifier!="")
		{
			$clientprofile=$client_obj->checkclientprofile($this->_view->clientidentifier);
			if($clientprofile[0]['category']!="" && $clientprofile[0]['job']!="")
				$this->_redirect("/client/quotes3"); 
		}
		if($this->_view->clientidentifier!="")
		{
			$this->_view->clientvals=$client_obj->getClientFulldetails($this->_view->clientidentifier);
		}		
		if($this->EP_Client->funnel_1['con_type']=='writing')
			$this->_view->language=$this->EP_Client->funnel_1['writing_lang'];
		else
			$this->_view->language=$this->EP_Client->funnel_1['translation_from'];
		
		if($this->EP_Client->funnel_1['dontknowcheck']==1)
			$this->_view->numcount=count($this->EP_Client->funnel_1['quotetype']);
		else
			$this->_view->numcount=$this->EP_Client->funnel_1['articlenum'][$this->EP_Client->funnel_1['quotetype'][0]];
		
		$this->_view->pagemenu=2;
		$this->_view->render("Client_quotes2");
	}
	
	/* Quotes creation step3 premium - here db insertion is done for premium quote , PremiumQuotes 
	and xls id mailed with all the fields filled in */
	public function quotes3Action()
	{
		ini_set('display_errors', 0);
		$this->EP_Client = Zend_Registry::get('EP_Client');
		
		$user_obj=new Ep_User_User();
		$userp_obj=new Ep_User_UserPlus();
		$client_obj=new Ep_User_Client();
			
		if($_REQUEST['clientid']=="" && $_REQUEST['email']!="" )
		{
			//User Insertion
			$array=array();
			$array['email']=$_REQUEST['email'];
			$array['password']=$_REQUEST['quotes_password'];;
			$array['status']="Active";
				$vcode=md5("edit-place_".$_REQUEST['email']);
			$array['verification_code']=$vcode;
			$array['verified_status']="YES";
			$array['type']="client";
			//print_r($array);exit;
			$identifier=$user_obj->InsertUser($array);
			
			//UserPlus Insertion
			$uparray=array();
			$uparray['first_name']=$_REQUEST['first_name'];
			$uparray['last_name']=$_REQUEST['last_name'];
			$uparray['phone_number']=$_REQUEST['telephone'];
			$userp_obj->updateUserplus($uparray,$identifier);
	
			//CLient Insertion
			$Carray=array();	
			$Carray['company_name']=$_REQUEST['company_name'];
			$Carray['job']=$_REQUEST['ep_job']; 
			$Carray['category']=$_REQUEST['category'];
			$Carray['website']=$_REQUEST['company_url'];
			$client_obj->updateClient($Carray,$identifier);
			
			//Login
			$username=$_REQUEST['email'];
			$password=$_REQUEST['quotes_password'];;
			$res=$user_obj->checkClientMailidLogin($username,$password);
		
			if($res!="NO")
			{
				$this->EP_Client->clientidentifier =$res;
				$this->_view->clientidentifier=$res;
				$this->EP_Client->clientemail =$username;	
				$this->_view->usertype='client';	
				$this->_view->clientname=$user_obj->getClientname($this->_view->clientidentifier);		
			}

		}
		else
		{
			if($_REQUEST['first_name']!="")
			{
			//UserPlus Insertion
			$uparray=array();
			$uparray['first_name']=$_REQUEST['first_name'];
			$uparray['last_name']=$_REQUEST['last_name'];
			$uparray['phone_number']=$_REQUEST['telephone'];
			$userp_obj->updateUserplus($uparray,$_REQUEST['clientid']);
	
			//CLient Insertion
			$Carray=array();	
			$Carray['company_name']=$_REQUEST['company_name'];
			$Carray['job']=$_REQUEST['ep_job']; 
			$Carray['category']=$_REQUEST['category'];
			$Carray['website']=$_REQUEST['company_url'];
			$client_obj->updateClient($Carray,$_REQUEST['clientid']);
			}
		}
		
		if($this->_view->clientidentifier=="" || $this->EP_Client->funnel_1['con_type']=="")
			$this->_redirect("/client/quotes1");
			
		$article_obj=new Ep_Ao_Article();
		
		$client_obj=new Ep_User_Client();
		$client_vals=$client_obj->getClientdetails($this->_view->clientidentifier);
		
			//Inserting Premium quotes
				$prem_obj=new Ep_Ao_PremiumQuotes();
				$this->EP_Client->funnel_1['remindtime']=$_REQUEST['remindtime'];
				$this->EP_Client->funnel_1['aotype']='premium';
				$quoteid=$prem_obj->InsertPremium($this->_view->clientidentifier,$this->EP_Client->funnel_1); 
				
				$this->_view->lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
				$this->_view->category_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
				$type_array=array("seo"=>"SEO article","desc"=>"Product description","blog"=>"Blog article","news"=>"News","guide"=>"Guide","other"=>"Others");
				$obj_array=array("premium"=>"I'd like to enter in contact with Edit-place to talk about my project ","liberte"=>"I'd like to enter in contact directly with writers/translators ","liberteprivate"=>"I want to propose a project to one of my favorite  editors / translators","dontknow"=>"I don't know");
				
				if($this->EP_Client->funnel_1['writing_lang']!="")
					$language=$this->EP_Client->funnel_1['writing_lang'];
				else
					$language=$this->EP_Client->funnel_1['translation_from'];
				//$total_article=array_sum($this->EP_Client->funnel_1['articlenum']);
				
				//Stats
				$statarray=array();
				for($q=0;$q<count($this->EP_Client->funnel_1['quotetype']);$q++)
				{
					$statarray[$q]['type']=$this->EP_Client->funnel_1['quotetype'][$q];
					if($this->EP_Client->funnel_1['quotetype'][$q]=="other")
						$statarray[$q]['typetext']="Other - ".$this->EP_Client->funnel_1['other_type'];
					else
						$statarray[$q]['typetext']=$type_array[$this->EP_Client->funnel_1['quotetype'][$q]];
					$statarray[$q]['num']=$this->EP_Client->funnel_1['articlenum'][$this->EP_Client->funnel_1['quotetype'][$q]];
					$statarray[$q]['translation_from']=$this->EP_Client->funnel_1['translation_from'];
					$statarray[$q]['translation_to']=$this->EP_Client->funnel_1['translation_to'];
					$statarray[$q]['sector']=$_REQUEST['category'];
						/*$obj="";
						for($o=0;$o<count($this->EP_Client->funnel_1['objectives']);$o++)
						{
							if($this->EP_Client->funnel_1['objectives'][$o]=="other")
								$obj.=$this->EP_Client->funnel_1['objOtherText'];
							else
								$obj.=$obj_array[$this->EP_Client->funnel_1['objectives'][$o]];
							if($o!=count($this->EP_Client->funnel_1['objectives'])-1)
								$obj.=",";
						}*/
					$statarray[$q]['objectives']=$obj_array[$this->EP_Client->funnel_1['objectives']];
				}
				$this->_view->statarray=$statarray;
		
		//Category writers
		$part_obj=new Ep_Ao_Participation();
		$categorywriters=$part_obj->getCategorywriters($client_vals[0]['category']);  	
		$categoryprofile=array();
		foreach($categorywriters as $cat)
		{
			$profiledir=APP_PATH_ROOT.'profiles/contrib/pictures/'.$cat['user_id'].'/'.$cat['user_id'].'_h.jpg';
			if(file_exists($profiledir))
				$categoryprofile[]=$cat;
		}
		if(count($categoryprofile)>0)
		{	
			if(count($categoryprofile)>5)
				$randomkeys=array_rand($categoryprofile,6);	
			else
				$randomkeys=array_rand($categoryprofile,count($categoryprofile));	
				
			$categoryarr=array(); 
				foreach($categoryprofile as $catk=>$catv)
				{
					if(in_array($catk,$randomkeys))
						$categoryarr[]=$catv;
				}
			$this->_view->categorywriters=$categoryarr;	 
		}
		else
			$this->_view->categorywriters=array();
		//History block
		$hist_obj=new Ep_Ao_QuotesHistory();
		$aotype='premium';
		
			if($this->EP_Client->funnel_1['dontknowcheck']!=1)
				$volume=max($this->EP_Client->funnel_1['articlenum']);
			else
				$volume=rand(20,50);
						
			
		//language
		if($this->EP_Client->funnel_1['con_type']=='writing')
			$language=$this->EP_Client->funnel_1['writing_lang'];
		else
			$language=$this->EP_Client->funnel_1['translation_from'];
			
		$this->_view->lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		$category_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$this->_view->category_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$this->_view->type_array=array("seo"=>"SEO article","desc"=>"Product description","blog"=>"Blog article","news"=>"News","guide"=>"Guide","other"=>"Others");
		
		//margin
		if($this->_view->clientidentifier!="")
		{
			if($client_vals[0]['contrib_percentage']!="")
				$margin=100-$client_vals[0]['contrib_percentage'];
			else
				$margin=$quoteData[0]['margin'];
			
			if($client_vals[0]['category']!="")
				$category=$client_vals[0]['category'];
			else
				$category=array_rand($category_array,1);
		}
		else
		{
			$margin=$quoteData[0]['margin'];
			$category=array_rand($category_array,1);
		}		
		
		$pricestats=array();	
		
		for($t=0;$t<count($this->EP_Client->funnel_1['quotetype']);$t++)
		{	
			$type=$this->EP_Client->funnel_1['quotetype'][$t];
			$input=array();
			$input['volume']=$aotype;
			$input['language']=$language;
			$input['type']=$this->EP_Client->funnel_1['con_type'];
			$input['content_type']=$type;
			
			$quoteData=$hist_obj->getQuotesHistory($input);
		
			if($type=="other")
			{
				$marginArr=array(15,20,25,30,35,40,45,50);
				$productc=array(2,4,6,8,10,12,14,16,18,20);
				
				$margin=$marginArr[array_rand($marginArr)];
				$sp=$productc[array_rand($productc)]/(1-($margin/100));
				$sp=round($sp);
				$variation=rand(15,35);
			}
			else
			{
				$sp=$quoteData[0]['selling_price'];//($quoteData[0]['prod_cost'])/(1-($margin/100)); // Updated by Rakesh on 31.10.2014
				$sp=round($sp);
				$variation=$quoteData[0]['variation'];
			}
			//echo $volume;exit;
			//if($volume==1)
				//$volume=$volume+1;
				
			$price=$volume*$sp;
		
			if($price!=0)
			{
				$quoteset=array();
				$diff=($variation/100)*$price;
				$countdiff=($variation/100)*$volume;
				$countdiff=ceil($countdiff);
				
				$this->_view->quoteprice=ceil($price/$volume);
				$this->_view->count=$volume;
				$quoteset[$volume]=$this->_view->quoteprice;
				
				$this->_view->quotepricelow=ceil(($price-$diff)/$volume);
				$this->_view->countlow=round($volume+$countdiff);
				$quoteset[$this->_view->countlow]=$this->_view->quotepricelow;
				
				$this->_view->quotepricelow1=ceil(($price-(2*$diff))/$volume);
				$this->_view->countlow1=round($volume+(2*$countdiff));
				//$quoteset[$this->_view->countlow1]=$this->_view->quotepricelow1; //Commented by Rakesh on 31.10.2014
				
				$this->_view->quotepricelow2=ceil(($price-(3*$diff))/$volume);
				$this->_view->countlow2=round($volume+(3*$countdiff));
				//$quoteset[$this->_view->countlow2]=$this->_view->quotepricelow2; // Commented by Rakesh on 31.10.2014
				
				$this->_view->quotepricehigh=ceil(($price+$diff)/$volume); 
				$this->_view->counthigh=round($volume-$countdiff);
				$quoteset[$this->_view->counthigh]=$this->_view->quotepricehigh;
				
				$this->_view->quotepricehigh1=ceil(($price+(2*$diff))/$volume);
				$this->_view->counthigh1=round($volume-(2*$countdiff));
				$quoteset[$this->_view->counthigh1]=$this->_view->quotepricehigh1;
				
				$this->_view->quotepricehigh2=ceil(($price+(3*$diff))/$volume);
				$this->_view->counthigh2=round($volume-(3*$countdiff));
				//$quoteset[$this->_view->counthigh2]=$this->_view->quotepricehigh2;  // Commented by Rakesh on 31.10.2014
				
				$randaomarray=array_rand($quoteset,3);
				
					$pricestats[$t]['num1']=$randaomarray[0];
				$pricestats[$t]['price1']=$quoteset[$randaomarray[0]];
					$pricestats[$t]['num2']=$randaomarray[1];
				$pricestats[$t]['price2']=$quoteset[$randaomarray[1]];
					$pricestats[$t]['num3']=$randaomarray[2];
				$pricestats[$t]['price3']=$quoteset[$randaomarray[2]];
				
				$pricestats[$t]['type']=$type;
			}
		
		}
		$this->_view->pricestats=$pricestats;
		$this->_view->category=$category;
		$this->_view->language=$language;
		$this->_view->type=$type;
		
		$this->_view->contype=$this->EP_Client->funnel_1['con_type'];;
		$this->_view->aotype=$this->EP_Client->funnel_1['aotype'];
		
		//Sending mail
		$this->generatequotesxls($quoteid,"yes",$pricestats,$category);
		
		unset($this->EP_Client->funnel_1);
		$this->_view->category=$client_vals[0]['category'];
		$this->_view->pagemenu=3;
		$this->_view->render("Client_quotes3");
	}
	
	/* Quotes creation step2 liberte - here all liberte details are filled in: title, spec file, preivate details, price & submission time */
	public function quotes2liberteAction()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
		$this->_view->category_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		
		$client_obj=new Ep_User_Client();
		
		$client_vals=$client_obj->getClientdetails($this->_view->clientidentifier);
		if($client_vals[0]['contrib_percentage']!="")
			$contribper=$client_vals[0]['contrib_percentage'];
		else
			$contribper=$this->getConfiguredval('nopremium_contribpercent');
		$this->_view->eppercent=100-$contribper;
		
		$params1=$this->_request->getParams();	
		if($params1['con_type']!="")
			$this->EP_Client->funnel_1=$params1;
			
		if($this->EP_Client->funnel_1['con_type']=="")
			$this->_redirect("/client/quotes1");	
		
		$user_obj=new Ep_User_User();
		
		if($this->EP_Client->funnel_2)
		{
			$this->_view->title=$this->EP_Client->funnel_2['title'];
			$this->_view->brief_uploaded_files=$this->EP_Client->funnel_2['files'];
			$this->_view->delivery_option=$this->EP_Client->funnel_2['delivery_option'];
			$this->_view->deliverytime=$this->EP_Client->funnel_2['deliverytime'];
			
			$this->_view->privatecontrib=$this->EP_Client->funnel_2['privatecontrib'];
			$this->_view->contribselected=$this->EP_Client->funnel_2['contribselected'];
			$this->_view->price_min_total=$this->EP_Client->funnel_2['price_min_total'];
			$this->_view->price_max_total=$this->EP_Client->funnel_2['price_max_total'];
			$this->_view->price_min=($this->EP_Client->funnel_2['price_min_total']*(100-$this->_view->eppercent))/100;
			$this->_view->price_max=($this->EP_Client->funnel_2['price_max_total']*(100-$this->_view->eppercent))/100;
		}
		else
		{
			$this->_view->contribselected=array();
		}
		
		if($this->_view->clientidentifier!="")
		{
			$user_details=$client_obj->getClientFulldetails($this->_view->clientidentifier); 
			
			//Private contributors
			$user_obj=new Ep_Ao_Participation();
			$contribarray=array();
			$contriblist=$user_obj->ListallfavContribs($this->_view->clientidentifier);
				for($c=0;$c<count($contriblist);$c++)
				{
					if($contriblist[$c]['first_name']!="")
						$contriblist[$c]['name']=ucfirst($contriblist[$c]['first_name']).'&nbsp;'.ucfirst(substr($contriblist[$c]['last_name'],0,1));
					else
						$contriblist[$c]['name']=$contriblist[$c]['email'];
						
					$contriblist[$c]['totalparticipation']=$user_obj->participationcount($contriblist[$c]['identifier'],'total');
					$contriblist[$c]['selectedparticipation']=$user_obj->participationcount($contriblist[$c]['identifier'],'selected');	
					$contriblist[$c]['profilepic']=$this->getContribpic($contriblist[$c]['identifier'],'home');
				}
				
			$this->_view->contriblist=$contriblist;
			$this->_view->clientobjective=$this->EP_Client->funnel_1['objectives'];				
		}
		else
			$user_details=array();
		$this->_view->user_details=$user_details; 
		
		
		$this->_view->pagemenu=2;
		$this->_view->render("Client_quotes2liberte");
	}
	
	/* Quotes creation step3 liberte - liberte quote insertion is done to Delivery, Article tables */
	public function quotes3liberteAction()
	{ 
		ini_set('display_errors', 0);
		$this->EP_Client = Zend_Registry::get('EP_Client');
		
		$params2=$this->_request->getParams();	
		
		if($params2['title']!="")
			$this->EP_Client->funnel_2=$params2;
			//print_r($params2);exit;
		
		$user_obj=new Ep_User_User();
		$userp_obj=new Ep_User_UserPlus();
		$client_obj=new Ep_User_Client();
			
		if($_REQUEST['clientid']=="" && $_REQUEST['email']!="" )
		{
			//User Insertion
			$array=array();
			$array['email']=$_REQUEST['email'];
			$array['password']=$_REQUEST['quotes_password'];
			$array['status']="Active";
				$vcode=md5("edit-place_".$_REQUEST['email']);
			$array['verification_code']=$vcode;
			$array['verified_status']="YES";
			$array['type']="client";
			//print_r($array);exit;
			$identifier=$user_obj->InsertUser($array);
			
			//UserPlus Insertion
			$uparray=array();
			$uparray['first_name']=$_REQUEST['first_name'];
			$uparray['last_name']=$_REQUEST['last_name'];
			$uparray['phone_number']=$_REQUEST['telephone'];
			$userp_obj->updateUserplus($uparray,$identifier);
	
			//CLient Insertion
			$Carray=array();	
			$Carray['company_name']=$_REQUEST['company_name'];
			$Carray['job']=$_REQUEST['ep_job'];
			$Carray['category']=$_REQUEST['category'];
			$Carray['website']=$_REQUEST['company_url'];
			$client_obj->updateClient($Carray,$identifier);
			
			//Login
			$username=$_REQUEST['email'];
			$password=$_REQUEST['quotes_password'];
			$res=$user_obj->checkClientMailidLogin($username,$password);
		
			if($res!="NO")
			{
				$this->EP_Client->clientidentifier =$res;
				$this->_view->clientidentifier=$res;
				$this->EP_Client->clientemail =$username;	
				$this->_view->client_email=$this->EP_Client->clientemail;
				$this->_view->usertype='client';	
				$this->_view->clientname=$user_obj->getClientname($this->_view->clientidentifier);		
			}

		}
		else
		{
			if($_REQUEST['first_name']!="")
			{
			//UserPlus Insertion
			$uparray=array();
			$uparray['first_name']=$_REQUEST['first_name'];
			$uparray['last_name']=$_REQUEST['last_name'];
			$uparray['phone_number']=$_REQUEST['telephone'];
			$userp_obj->updateUserplus($uparray,$_REQUEST['clientid']);
	
			//CLient Insertion
			$Carray=array();	
			$Carray['company_name']=$_REQUEST['company_name'];
			$Carray['job']=$_REQUEST['ep_job']; 
			$Carray['category']=$_REQUEST['category'];
			$Carray['website']=$_REQUEST['company_url'];
			$client_obj->updateClient($Carray,$_REQUEST['clientid']);
			}
		}
		if($this->_view->clientidentifier=="" || $this->EP_Client->funnel_1['con_type']=="")
			$this->_redirect("/client/quotes1");
			
		$article_obj=new Ep_Ao_Article();
		
		$client_obj=new Ep_User_Client();
		$client_vals=$client_obj->getClientdetails($this->_view->clientidentifier);
		
			/*//spec upload
			$filecount=count($_FILES['files']);
			$filename=array();
			$filepath=array();
			for($f=0;$f<$filecount;$f++)
			{
				$realfilename=$_FILES['files']['name'][$f];
				$client_id=$this->_view->clientidentifier;
				
				$ext=$this->findexts($realfilename);
				
				$uploaddir = '/home/sites/site7/web/FO/client_spec/';
				
				if(!is_dir($uploaddir.$client_id))
				{
					mkdir($uploaddir.$client_id,0777);
					chmod($uploaddir.$client_id,0777);
				}
				$uploaddir=$uploaddir.$client_id."/";
				$realfilename=trim(utf8_decode($realfilename));
				$realfilename=str_replace(" ","_",$realfilename);
				
				$bname=basename($realfilename,".".$ext)."_".uniqid().".".$ext;
				
				$filename[]=$bname;
				$filepath[]='/'.$client_id.'/'.$bname;
				
				$file = $uploaddir . $bname;
				if (move_uploaded_file($_FILES['files']['tmp_name'][$f], $file))
					chmod($file,0777);
					
				$this->EP_Client->funnel_1['filename']=implode("|",$filename);
				$this->EP_Client->funnel_1['filepath']=implode("|",$filepath);	
			
			}*/	
			
			$wordsarray=array("seo"=>"130","desc"=>"80","blog"=>"500","news"=>"200","guide"=>"1000","other"=>"");
			
			//Inserting Delivery
			$this->EP_Client->funnel_1['privatepublish']=$client_vals[0]['privatepublish'];
			
			$statarray=array();
			
			//for($q=0;$q<count($this->EP_Client->funnel_1['quotetype']);$q++)
			//{ 
				$delivery_obj=new Ep_Ao_Delivery();
				$pay_obj=new Ep_Ao_Payment();
				$payart_obj=new Ep_Ao_PaymentArticle();
				
				//Inserting Premium quotes
				$prem_obj=new Ep_Ao_PremiumQuotes();
				$this->EP_Client->funnel_1['remindtime']=$_REQUEST['remindtime'];
				$this->EP_Client->funnel_1['aotype']='liberte';
				$this->EP_Client->funnel_1['title']=$_REQUEST['title'];
				$quoteid=$prem_obj->InsertPremium($this->_view->clientidentifier,$this->EP_Client->funnel_1); 
			
				$this->EP_Client->funnel_1['type']=$this->EP_Client->funnel_1['quotetype'][0];
				if($this->EP_Client->funnel_1['quotetype'][0]!="other")
				{
					$this->EP_Client->funnel_1['min_sign']=$wordsarray[$this->EP_Client->funnel_1['quotetype'][0]];
					$this->EP_Client->funnel_1['max_sign']=$this->EP_Client->funnel_1['min_sign'];
				}
				if($this->EP_Client->funnel_1['dontknowcheck']==1)
					$this->EP_Client->funnel_1['total_article']=count($this->EP_Client->funnel_1['quotetype']);
				else	
					$this->EP_Client->funnel_1['total_article']=$this->EP_Client->funnel_1['articlenum'][$this->EP_Client->funnel_1['type']];
					
				$this->EP_Client->funnel_1['aotitle']=$this->EP_Client->funnel_2['title'];
				$this->EP_Client->funnel_1['quoteid']=$quoteid; 
				$delivery_id=$delivery_obj->InsertLiberte($this->_view->clientidentifier,$this->EP_Client->funnel_1,$this->EP_Client->funnel_2);
				
				if($delivery_id!="NO")
				{ 
					//Inserting Article
					if($client_vals[0]['contrib_percentage']!="")
						$this->EP_Client->funnel_1['contrib_percentage']=$client_vals[0]['contrib_percentage'];
					else
						$this->EP_Client->funnel_1['contrib_percentage']=$this->getConfiguredval('nopremium_contribpercent');
					
					//for($q=0;$q<count($this->EP_Client->funnel_1['quotetype']);$q++)
					//{
						$this->EP_Client->funnel_1['typeart']=$this->EP_Client->funnel_1['quotetype'][0];
						$this->EP_Client->funnel_1['aotitleart']=$this->EP_Client->funnel_2['title'].' - '.$this->EP_Client->funnel_1['typeart'];
						
						if($this->EP_Client->funnel_1['dontknowcheck']==1)
						{
							$artcount=1;
							$this->EP_Client->funnel_1['frequencyart']="once";
						}
						else
						{
							$artcount=$this->EP_Client->funnel_1['articlenum'][$this->EP_Client->funnel_1['typeart']];
							$this->EP_Client->funnel_1['frequencyart']=$this->EP_Client->funnel_1['frequency'][$this->EP_Client->funnel_1['typeart']];
						}	
						//for($a=0;$a<$artcount;$a++)
						//{
							$art_obj=new Ep_Ao_Article();
							$artid=$art_obj->InsertLiberteArticle($delivery_id,$this->EP_Client->funnel_1,$this->EP_Client->funnel_2);
						//}
					//}
					//exit;
					//Inserting Payment
					$pay_obj->InsertPayment($delivery_id);
					
					if($client_vals[0]['paypercent']=='0')
					{
						$payed_id=$payart_obj->insertpayedclient($this->_view->clientidentifier);
						$Aarray=array();
						$Aarray['paid_status']='paid';
						$Aarray['invoice_id']=$payed_id;
						$whereA= "delivery_id='".$delivery_id."'";
						$article_obj->updateArticle($Aarray,$whereA);
					}
						
					//ArticleHistory Insertion
					$hist_obj = new Ep_Article_ArticleHistory();
					$action_obj = new Ep_Article_ArticleActions();
					$history1=array();
					$history1['user_id']=$this->_view->clientidentifier;
					$history1['article_id']=$delivery_id;
						$sentence1=$action_obj->getActionSentence(1);
						if($this->EP_Client->funnel_1['privatecontrib']=="on")
							$AO_type='<b>Private</b>';
						else
							$AO_type='<b>Public</b>';
						
						$AO_name='<a href="/ongoing/ao-details?client_id='.$this->_view->clientidentifier.'&ao_id='.$delivery_id.'&submenuId=ML2-SL4" target="_blank"><b>'.$this->EP_Client->funnel_1['title'].'</b></a>';
							$client_obj=new Ep_User_Client();
							$detailsC=$client_obj->getClientdetails($this->_view->clientidentifier);
						$client_name='<b>'.$detailsC[0]['company_name'].'</b>';
						$project_manager_name='<b>'.$this->_view->clientname.'(Client)</b>';
						$actionmessage=strip_tags($sentence1);
						eval("\$actionmessage= \"$actionmessage\";");
					$history1['stage']='creation';
					$history1['action']='creation';
					$history1['action_sentence']=$actionmessage;
					$hist_obj->insertHistory($history1);
				}
				
				//Stats
				$this->_view->lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
				$this->_view->category_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
				$type_array=array("seo"=>"SEO article","desc"=>"Product description","blog"=>"Blog article","news"=>"News","guide"=>"Guide","other"=>"Others");
				$obj_array=array("premium"=>"I'd like to enter in contact with Edit-place to talk about my project ","liberte"=>"I'd like to enter in contact directly with writers/translators ","liberteprivate"=>"I want to propose a project to one of my favorite  editors / translators","dontknow"=>"I don't know");
				
				for($q=0;$q<count($this->EP_Client->funnel_1['quotetype']);$q++)
				{
					$statarray[$q]['type']=$this->EP_Client->funnel_1['quotetype'][$q];	
					if($this->EP_Client->funnel_1['quotetype'][$q]=="other")
						$statarray[$q]['typetext']="Other - ".$this->EP_Client->funnel_1['other_type'];
					else
						$statarray[$q]['typetext']=$type_array[$this->EP_Client->funnel_1['quotetype'][$q]];
					$statarray[$q]['num']=$this->EP_Client->funnel_1['articlenum'][$this->EP_Client->funnel_1['quotetype'][$q]];
					$statarray[$q]['writing_lang']=$this->EP_Client->funnel_1['writing_lang'];
					$statarray[$q]['sector']=$_REQUEST['category'];
						/*$obj="";
						for($o=0;$o<count($this->EP_Client->funnel_1['objectives']);$o++)
						{
							if($this->EP_Client->funnel_1['objectives'][$o]=="other")
								$obj.=$this->EP_Client->funnel_1['objOtherText'];
							else
								$obj.=$obj_array[$this->EP_Client->funnel_1['objectives'][$o]];
							if($o!=count($this->EP_Client->funnel_1['objectives'])-1)
								$obj.=" , ";
						}*/
					$statarray[$q]['objectives']=$obj_array[$this->EP_Client->funnel_1['objectives']];
				}
				
				//Creating XLS
				$this->generatequotesxls($quoteid,"no");
				
				//Copy files 
				if(count($this->EP_Client->funnel_2['filename'])>0)
				{
					//spec file directory
					$copdir="/home/sites/site7/web/FO/client_spec/".$this->EP_Client->clientidentifier."/";

					//create dir if not exists
					if(!is_dir($copdir))
					{
						mkdir($copdir,0777);
						chmod($copdir,0777);
					}
					//moving spec files from temp location to client_spec
					
					for($f=0;$f<count($this->EP_Client->funnel_2['filename']);$f++)
					{
						$this->EP_Client->funnel_2['filename'][$f]=iconv("UTF-8", "ISO-8859-1//TRANSLIT",$this->EP_Client->funnel_2['filename'][$f]);
						$source="/home/sites/site7/web/FO/spec_temp/".date("Y")."-".date("m")."-".date("d")."/".$this->EP_Client->funnel_2['filename'][$f];
						$dest="/home/sites/site7/web/FO/client_spec/".$this->EP_Client->clientidentifier."/".$this->EP_Client->funnel_2['filename'][$f];
						copy($source,$dest);
					}
				}		
				
			//}
			$this->_view->statarray=$statarray;
			$this->_view->delivery=$delivery_id;
			$this->_view->missiontitle=$this->EP_Client->funnel_2['title'];
			unset($this->EP_Client->funnel_1);
			unset($this->EP_Client->funnel_2);
			
		//setlocale(LC_TIME, 'fr_FR');
		$this->_view->time48=strftime("%A %d %B %Y &agrave; %Ih ",strtotime('+2 days'));
		$this->_view->category=$client_vals[0]['category'];
		
		//Category writers
		$part_obj=new Ep_Ao_Participation();
		$categorywriters=$part_obj->getCategorywriters($client_vals[0]['category']);  	
		$categoryprofile=array();
		foreach($categorywriters as $cat)
		{
			$profiledir=APP_PATH_ROOT.'profiles/contrib/pictures/'.$cat['user_id'].'/'.$cat['user_id'].'_h.jpg';
			if(file_exists($profiledir))
				$categoryprofile[]=$cat;
		}
		if(count($categoryprofile)>0)
		{	
			if(count($categoryprofile)>4)
				$randomkeys=array_rand($categoryprofile,5);	
			else
				$randomkeys=array_rand($categoryprofile,count($categoryprofile));	
				
			$categoryarr=array(); 
				foreach($categoryprofile as $catk=>$catv)
				{
					if(in_array($catk,$randomkeys))
						$categoryarr[]=$catv;
				}
			$this->_view->categorywriters=$categoryarr;	 
		}
		else
			$this->_view->categorywriters=array();
		$this->_view->pagemenu=3;			
		$this->_view->render("Client_quotes3liberte");
	}
	
	/* Generating xls report of premium quotes*/
	public function generatequotesxls($quote,$mail,$pricearr=array(),$cat="")
	{
		//$quote='140050725324954';
		$prem_obj=new Ep_Ao_PremiumQuotes();
		$quotesdetail=$prem_obj->getPremiumQuotes($quote);
		
		$xlsFile = '/home/sites/site7/web/FO/premiumquote/'.$quotesdetail[0]['user_id'].'_'.$quotesdetail[0]['company_name'].'_'.$quote.'.xls';
		$fh = fopen($xlsFile, 'w') or die("can't open file");
		
		$lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		$category_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$type_array=array("seo"=>"SEO article","desc"=>"Product description","blog"=>"Blog article","news"=>"News","guide"=>"Guide","other"=>"Others");
		$obj_array=array("premium"=>"I'd like to enter in contact with Edit-place to talk about my project ","liberte"=>"I'd like to enter in contact directly with writers/translators ","liberteprivate"=>"I want to propose a project to one of my favorite  editors / translators","dontknow"=>"I don't know");
		$frequency_array=array("once"=>"En 1 seule fois","day"=>"Par jour","week"=>"Par semaine","month"=>"Par mois");
		$job_array=array("1"=>"PDG ou g&eacute;rant","2"=>"Commercial","3"=>"Marketing","4"=>"Directeur technique","5"=>"Web designer","6"=>"Chef de projet","7"=>"SEO manager","8"=>"Autre");
		$mots_array=array("seo"=>"130","desc"=>"80 mots","blog"=>"500 mots","news"=>"200 mots","guide"=>"500 mots","other"=>"-");
		
		if(count($quotesdetail)>0)
		{
			$XLcontent="<table border=1>
					<tr>
						<th colspan=4 style=background-color:#357EBD>PREMIUM QUOTE DETAIL</th>
					</tr>
					<tr>
						";
					
			if($quotesdetail[0]['con_type']=='translation')
			{
				$XLcontent.= "<td><b>Traduction de contenu </b></td>
					 <td colspan=3>".$lang_array[$quotesdetail[0]['from_language']]." -> ".$lang_array[$quotesdetail[0]['to_language']]."</td>"; 
			}
			else
			{
				$XLcontent.= "<td>R&eacute;daction de contenu</td>
					 <td>".$lang_array[$quotesdetail[0]['from_language']]."</td>"; 
			}
				
			$typear=explode("|",$quotesdetail[0]['type']);
			$numar=explode("|",$quotesdetail[0]['total_article']);
			$freqar=explode("|",$quotesdetail[0]['frequency']);
			
			$XLcontent.= "</tr>";
			
			for($t=0;$t<count($typear);$t++)
			{
				if($typear[$t]=="other")
					$type="Autres - ".$quotesdetail[0]['other_type'];
				else
					$type=$type_array[$typear[$t]];
				
				  $XLcontent.= "<tr><td valign=top><b>Type de contenu </b></td>	
									<td>".$type."</td>
									<td colspan=2>".$mots_array[$typear[$t]]."</td>
								</tr>
								<tr>
									<td>Volume</td>
									<td colspan=3 style=text-align:left;>".$numar[$t]."</td>
								</tr>
								<tr>
									<td>R&eacute;currence</td>
									<td colspan=3>".$frequency_array[$freqar[$t]]."</td>
								</tr>";
			}
			
			if($quotesdetail[0]['dontknowcheck']=="yes")
				$checktext="Oui";
			else
				$checktext="Non";
				
			$XLcontent.= "
				 <tr>
					<td><b>Je ne sais pas, je cherche juste un bon r&eacute;dacteur pour mon site</b></td>
					<td colspan=3>".$checktext."</td>
				</tr>";
				
			if(count($quotesdetail[0]['objective'])>0)
			{
				//$objj=explode(",",$quotesdetail[0]['objective']);
				$XLcontent.= "<tr><td valign=top><b>Targets</b></td>";	
				
				/*for($o=0;$o<count($objj);$o++)
				{
					if($o!=0)
						$XLcontent.= "<tr>";
					$XLcontent.= "	<td colspan=3>".$obj_array[$objj[$o]]."</td>
						</tr>";
				}*/
				$XLcontent.= "	<td colspan=3>".$obj_array[$quotesdetail[0]['objective']]."</td>
						</tr>";
				//if($quotesdetail[0]['other_objective']!="")
					//$XLcontent.="<tr><td colspan=4>".$quotesdetail[0]['other_objective']."</td></tr>";
			}
			
			$XLcontent.= "<tr><td colspan=4></td></tr><tr><td colspan=4></td></tr>";
			
			$XLcontent.= "<tr><th colspan=4 style=background-color:#357EBD>CLIENT DETAILS</th></tr>
				<tr>
					<td><b>Nom de l'entreprise</b></td>
					<td colspan=3>".$quotesdetail[0]['company_name']."</td>
				</tr>
				<tr>
					<td><b>URL du site internet</b></td>
					<td colspan=3>".$quotesdetail[0]['website']."</td>
				</tr>
				<tr>
					<td><b>Nom</b></td>
					<td colspan=3>".$quotesdetail[0]['last_name']."</td>
				</tr>
				<tr>
					<td><b>Pr&eacute;nom</b></td>
					<td colspan=3>".$quotesdetail[0]['first_name']."</td>
				</tr>
				<tr>
					<td><b>Email</b></td>
					<td colspan=3>".$quotesdetail[0]['email']."</td>
				</tr>
				<tr>
					<td><b>T&eacute;l&eacute;phone</b></td>
					<td colspan=3 style=text-align:left>#".$quotesdetail[0]['phone_number']."</td>  
				</tr>
				<tr>
					<td><b>Fonction</b></td>
					<td colspan=3>".$job_array[$quotesdetail[0]['job']]."</td>
				</tr>
				<tr>
					<td><b>Secteur d'activit&eacute;</b></td>
					<td colspan=3>".$$category_array[$cat]."</td>
				</tr>		
				";
			
			if(count($pricearr)>0)
			{
				$XLcontent.= "<tr><td colspan=4></td></tr><tr><td colspan=4></td></tr>";
					$XLcontent.= "<tr>
							<td></td>
							<td><b>Tarif 1</b></td>	
							<td><b>Tarif 2</b></td>	
							<td><b>Tarif 3</b></td>	
						</tr>";	
						
				foreach($pricearr as $price)
				{
					$XLcontent.= "<tr>
							<td>".$type_array[$price['type']]."</td>	
							<td>".$price['price1']."&euro; , Volume:".$price['num1']."</td>	
							<td>".$price['price2']."&euro;, Volume:".$price['num2']."</td>	
							<td>".$price['price3']."&euro;, Volume:".$price['num3']."</td>	
						</tr>";	
				}
			}
			$XLcontent.= "</table>";	
			//echo $XLcontent;//exit;
				//$XLcontent = ob_get_contents();
                fwrite($fh, $XLcontent);
				fclose($fh);
				
			if($mail=="yes")
			{
				//$email_to='jwolff@edit-place.com,mailpearls@gmail.com';
				$email_to='kavithashree.r@gmail.com';
				$from_mail='support@edit-place.com,Support Edit-place';
				$email_subject='Premium quote by '.$quotesdetail[0]['company_name'].' at UK test platform';
				$email_message='Please find the attachment';
				
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
				
					$file = $xlsFile;
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
			}	
		}
	}
	
	/* Pop up screen for writer profile & participation details*/	
	public function userprofileb3Action()
	{
		if($this->_view->clientidentifier=="")
		{
			echo "expired";
			exit;
		}
		else
		{	
			$user_obj=new Ep_Ao_Participation();
			$user_details=$user_obj->getcontribProfileDetails($_REQUEST['partid']);
		
			$lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
				$this->_view->language_array=$lang_array;
			$cat_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
				$this->_view->category_array=$cat_array;
		
			$user_details[0]['first_name']=strtolower($user_details[0]['first_name']);
			$user_details[0]['last_name']=strtolower($user_details[0]['last_name']);
			$user_details[0]['name']=ucfirst($user_details[0]['first_name']).'&nbsp;'.ucfirst(substr($user_details[0]['last_name'],0,1));
			$user_details[0]['profilepic']=$this->getContribpic($user_details[0]['user_id'],'profile');
			//Age
			$user_details[0]['age']=$user_details[0]['curryear']-$user_details[0]['byear'];
		
			//Language
			if($user_details[0]['language_more']!="")
			{
				//forming array with lang id as index and percent as value
				$str=explode("\"",$user_details[0]['language_more']);
				$language=array();
				for($s=0;$s<count($str);$s=$s+4)
				{
					$index=$str[$s+1];
					if($index!="")
						$language[$index]=$str[$s+3];
				}
			}
			$this->_view->langpercent=$language;
			$user_details[0]['langstr']=$lang_array[$user_details[0]['language']];

			if($user_details[0]['language_more']!='')
			{
				$languagekey=array_keys($language);
				if(count($languagekey)>0)
				{
					for($l=0;$l<count($languagekey);$l++)
						$user_details[0]['langstr'].=", ".$lang_array[$languagekey[$l]];
				}
			}
		
			//Clients
			$carray=array();
			for($c=0;$c<count($user_details[0]['clients']);$c++)
			{
				if(file_exists(APP_PATH_ROOT."profiles/clients/logos/".$user_details[0]['clients'][$c]['user_id']."/".$user_details[0]['clients'][$c]['user_id']."_global1.png"))	
					$carray[$user_details[0]['clients'][$c]['company_name']]="/FO/profiles/clients/logos/".$user_details[0]['clients'][$c]['user_id']."/".$user_details[0]['clients'][$c]['user_id']."_global1.png";
				else
					$carray[$user_details[0]['clients'][$c]['company_name']]="/FO/images/customer-no-logo90.png";
			}
			//$user_details[0]['clientlist']=implode(",",$carray);
			$user_details[0]['clientlogo']=$carray;

			//Category
			$user_details[0]['catstr']=$this->getCategoryName($user_details[0]['favourite_category']);
			$user_details[0]['cats']=explode(",",$user_details[0]['favourite_category']);

			if($user_details[0]['category_more']!="")
			{
				//forming array with cat id as index and percent as value
				$str=explode("\"",$user_details[0]['category_more']);
				$category=array();
				for($s=0;$s<count($str);$s=$s+4)
				{
					$index=$str[$s+1];
					if($index!="")
						$category[$index]=$str[$s+3];
				}
			}
			$this->_view->catpercent=$category;
			
			//contrat type
			$contract=array("cdi"=>"CDI","cdd"=>"CDD","freelance"=>"Freelance","interim"=>"Interim");	
			
			//Experience and Education
			$exp_details=$user_obj->getContribexp($_REQUEST['partid'],'job');
				for($x=0;$x<count($exp_details);$x++)
				{
					//setlocale(LC_TIME, "fr_FR");
					$exp_details[$x]['from_month']= strftime('%B', mktime(0, 0, 0, $exp_details[$x]['from_month']));
					$exp_details[$x]['to_month']= strftime('%B', mktime(0, 0, 0, $exp_details[$x]['to_month']));
					$exp_details[$x]['contract']=$contract[$exp_details[$x]['contract']];
				}
			$this->_view->exp_details=$exp_details;
			
			$education_details=$user_obj->getContribexp($_REQUEST['partid'],'education');
				for($x=0;$x<count($education_details);$x++)
				{
					//setlocale(LC_TIME, "fr_FR");
					$education_details[$x]['from_month']= strftime('%B', mktime(0, 0, 0, $education_details[$x]['from_month']));
					$education_details[$x]['to_month']= strftime('%B', mktime(0, 0, 0, $education_details[$x]['to_month']));
				}
			$this->_view->education_details=$education_details;
			
			$this->_view->contribprofile=$user_details;
			$this->_view->render("Client_userprofileb3");
		}
	}
	
	/* Pop up from Order4 to ask for validation from client, when an article is not yet published*/
	public function confirmpremiumAction()
	{
		$this->EP_Client = Zend_Registry::get('EP_Client');
		$confirmparam=$this->_request->getParams();	
		
		$hist_obj=new Ep_Ao_QuotesHistory();
		
		//Check premium or liberte
		if($confirmparam['objectives']=="liberte" || $confirmparam['objectives']=="liberteprivate")
			$aotype='liberte';
		elseif($confirmparam['objectives']=="dontknow")
		{
			if($confirmparam['con_type']=='writing' && $confirmparam['writing_lang']=='uk')
			{
				$premium=0;
				
				if($confirmparam['dontknowcheck']!=1)
				{
					foreach($confirmparam['articlenum'] as $numkey=>$numitem)
					{
						if($confirmparam['frequency'][$numkey]=="once")
						{
							if($confirmparam['articlenum'][$numkey]>=20)
								$premium=1;
						}
						elseif($confirmparam['frequency'][$numkey]=="day")
						{
							if($confirmparam['articlenum'][$numkey]>2)
								$premium=1;
						}
						elseif($confirmparam['frequency'][$numkey]=="week")
						{
							if(($this->EP_Client->funnel_1['articlenum'][$numkey]/7)>2)
								$premium=1;
						}
						elseif($confirmparam['frequency'][$numkey]=="month")
						{
							if(($confirmparam['articlenum'][$numkey]/30)>2)
								$premium=1;
						}				
					}
					
				}
						
				if($premium==0)
					$aotype='liberte';
				else
					$aotype='premium';
			}
			else
				$aotype='premium';
		}	
		else
			$aotype='premium';
				
		if($confirmparam['dontknowcheck']!=1)
			$volume=max($confirmparam['articlenum']);
		else
			$volume=rand(20,50);
			
		//language
		if($confirmparam['con_type']=='writing')
			$language=$confirmparam['writing_lang'];
		else
			$language=$confirmparam['translation_from'];
		
		$this->_view->lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		$category_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$this->_view->category_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$this->_view->type_array=array("seo"=>"SEO article","desc"=>"Product description","blog"=>"Blog article","news"=>"News","guide"=>"Guide","other"=>"Others");
		
		//margin
		if($this->_view->clientidentifier!="")
		{
			$client_obj=new Ep_User_Client();
			$client_vals=$client_obj->getClientdetails($this->_view->clientidentifier);
			if($client_vals[0]['contrib_percentage']!="")
				$margin=100-$client_vals[0]['contrib_percentage'];
			else
				$margin=$quoteData[0]['margin'];
			
			if($client_vals[0]['category']!="")
				$category=$client_vals[0]['category'];
			else
				$category=array_rand($category_array,1);
		}
		else
		{
			$margin=$quoteData[0]['margin'];
			$category=array_rand($category_array,1);
		}		
		
		$pricestats=array();	
		for($t=0;$t<count($confirmparam['quotetype']);$t++)
		{	
			$type=$confirmparam['quotetype'][$t];
			$input=array();
			$input['volume']=$aotype;
			$input['language']=$language;
			$input['type']=$confirmparam['con_type'];
			$input['content_type']=$type;
			
			$quoteData=$hist_obj->getQuotesHistory($input);
			
			if($type=="other")
			{
				$marginArr=array(15,20,25,30,35,40,45,50);
				$productc=array(2,4,6,8,10,12,14,16,18,20);
				
				$margin=$marginArr[array_rand($marginArr,1)];
				$sp=$productc[array_rand($productc,1)]/(1-($margin/100));
				$sp=round($sp);
				$variation=rand(15,35);
			}
			else
			{
				$sp=$quoteData[0]['selling_price'];//($quoteData[0]['prod_cost'])/(1-($margin/100)); // Commented by Rakesh on 31.10.2014
				$sp=round($sp);
				$variation=$quoteData[0]['variation'];
			}
			
			//if($volume==1)
				//$volume=$volume+1;
			
			if($sp==0)
			{
				$sp=20;
				$variation=rand(15,35);
			}
			
			$price=$volume*$sp;
			
			if($price!=0)
			{
				$quoteset=array();
				$diff=($variation/100)*$price;
				$countdiff=($variation/100)*$volume;
				
				$countdiff=ceil($countdiff);
				
				$this->_view->quoteprice=ceil($price/$volume);
				$this->_view->count=$volume;
				$quoteset[$volume]=$this->_view->quoteprice;
				
				$this->_view->quotepricelow=ceil(($price-$diff)/$volume);
				$this->_view->countlow=round($volume+$countdiff);
				$quoteset[$this->_view->countlow]=$this->_view->quotepricelow;
				
				$this->_view->quotepricelow1=ceil(($price-(1.5*$diff))/$volume);
				$this->_view->countlow1=round($volume+(2*$countdiff));
				// $quoteset[$this->_view->countlow1]=$this->_view->quotepricelow1; // Commented by Rakesh on 31.10.2014
				
				$this->_view->quotepricehigh=ceil(($price+$diff)/$volume); 
				$this->_view->counthigh=round($volume-$countdiff);
				$quoteset[$this->_view->counthigh]=$this->_view->quotepricehigh;
				
				$this->_view->quotepricehigh1=ceil(($price+(1.5*$diff))/$volume);
				$this->_view->counthigh1=round($volume-(2*$countdiff));
				$quoteset[$this->_view->counthigh1]=$this->_view->quotepricehigh1;
					
				$randaomarray=array_rand($quoteset,3);
				
				
				$pricestats[$t]['num1']=$randaomarray[0];
				$pricestats[$t]['price1']=$quoteset[$randaomarray[0]];
					$pricestats[$t]['num2']=$randaomarray[1];
				$pricestats[$t]['price2']=$quoteset[$randaomarray[1]];
					$pricestats[$t]['num3']=$randaomarray[2];
				$pricestats[$t]['price3']=$quoteset[$randaomarray[2]];
				
				$pricestats[$t]['type']=$type;
			}
		}
		$this->_view->pricestats=$pricestats;
		
		
		$this->_view->contype=$confirmparam['con_type'];;
		$this->_view->category=$category;
		$this->_view->language=$language;
		$this->_view->aotype=$aotype;
		$this->_view->render("Client_confirmpremium");
	}	

	/********************************************************* Defined Functions*****************************************************/
	/* Generating client invoice pdf */
	public function generateInvoice($article)
	{
		$invoiceid= $article;
		$invoicedir='/home/sites/site7/web/FO/invoice/client/'.$this->_view->clientidentifier.'/';
			   
		if(!file_exists($invoicedir.'/'.$invoiceid.'.pdf'))
		{
			$pay_obj = new Ep_Ao_PaymentArticle();
			$art_obj = new Ep_Ao_Article();
			$country_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);

			//Payment details
			$payment=$pay_obj->getpaymentdetails($invoiceid);
		
			//Dates
			//setlocale(LC_TIME, 'fr_FR');
			$date_invoice_full= strftime("%e %B %Y",strtotime($payment[0]['delivery_date']));
			$date_invocie = date("d/m/Y",strtotime($payment[0]['delivery_date']));
			$date_invoice_ep=date("Y/m",strtotime($payment[0]['delivery_date']));

		   //Address
			$profileinfo=$pay_obj->getClientdetails($this->_view->clientidentifier);
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
												'.number_format($total,2,',','').'
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
	
	
	//Calculate Contributor Profile Percentage
    public function calculateProfilePercentage($user_id)
    {
          //setlocale(LC_TIME, "fr_FR");
          $contrib_identifier=$user_id;

          $profilePercentage=0;

          
            $profileplus_obj = new Ep_Contrib_ProfilePlus();
            $profileContrib_obj = new Ep_Contrib_ProfileContributor();
            $profile_identifier_info=$profileplus_obj->checkProfileExist($contrib_identifier);
            if($profile_identifier_info!='NO')
            {
               $profile_identifier=$profile_identifier_info[0]['user_id'];
               $profileinfo=$profileplus_obj->getProfileInfo($profile_identifier);
               $profile_contribinfo=$profileContrib_obj->getProfileInfo($profile_identifier);
         
                /**Mon identitÃ©**/
                if($profileinfo[0]['initial'] && $profileinfo[0]['first_name'] && $profileinfo[0]['last_name'])
                    $profilePercentage+=15; 

                //Ma date de naissance  
                if($profile_contribinfo[0]['dob'])           
                    $profilePercentage+=5;

                //Photo du profil  
                                
                  $profilePercentage+=10;
               

                //texte de prÃ©sentation
                if(strip_tags($profile_contribinfo[0]['self_details']))
                    $profilePercentage+=10;


                //Langues & niveau de maitrise
                if($profile_contribinfo[0]['language'])
                     $profilePercentage+=15;

                
                //CompÃ©tences & niveau de maitrise
                if($profile_contribinfo[0]['category_more'])
                {
                    $categories_more=unserialize($profile_contribinfo[0]['category_more']);   
                    if(count($categories_more)>0)
                        $profilePercentage+=15;
                }   

                //ExpÃ©riences professionnelles
                $experience_obj=new Ep_Contrib_Experience();
                $jobDetails=$experience_obj->getExperienceDetails($contrib_identifier,'job');
                if($jobDetails!="NO")
                {
                  $profilePercentage+=15;
                }

                //Formation
                $educationDetails=$experience_obj->getExperienceDetails($contrib_identifier,'education');
                if($educationDetails!="NO")
                    $profilePercentage+=10;


                //Informations personnelles
                if($profileinfo[0]['address'] && $profileinfo[0]['city'] && $profileinfo[0]['phone_number'] &&
                    $profileinfo[0]['zipcode'] && $profileinfo[0]['country']&&$profile_contribinfo[0]['nationality'])
                    $profilePercentage+=2;  

                //Informations de facturation       
                if($profile_contribinfo[0]['pay_info_type'])
                    $profilePercentage+=2;

                //Choix de rÃ©munÃ©ration
                if($profile_contribinfo[0]['payment_type'])
                    $profilePercentage+=1;
                    
                if($profilePercentage > 100)
                   $profilePercentage=100;                        

                return $profilePercentage;
              
          }
          else
             return 0;              
      
    }
	
	public function sendmaildirect($mailid,$to,$params)
	{
		$mail_obj=new Ep_Ticket_AutoEmails();
		$mailcontent=$mail_obj->getAutoEmail($mailid); 
		$mail = new Zend_Mail();
	
		//parameters
		$link=$params['link'];
		$client=$params['client'];
		$sitelink=$params['sitelink'];
		$writercount=$params['writercount'];
		$resetlink=$params['resetlink'];
		$login=$params['login'];
		$password=$params['password'];
		$this->mail_from= $this->getConfiguredval("mail_from");
		$Message=$mailcontent[0]['Message'];
		eval("\$Message= \"$Message\";");
		
		$Object=$mailcontent[0]['Object'];
		eval("\$Object= \"$Object\";");

		$mail->setBodyHTML($Message)
		   ->setFrom('support@edit-place.com','Support Edit-place')
		   ->addTo($to)
		   //->addCc("kavithashree.r@gmail.com")
		   ->setSubject($Object);
		$mail->send();
		//$this->critsendMail($this->mail_from, $to, $Object, $Message);
		
	}
	
	/* Function to close an article from client*/
	public function closearticleAction()
	{
		$art_obj=new Ep_Ao_Article();
		$part_obj=new Ep_Ao_Participation();
		$art_array=array("status"=>"closed_client");
		$art_where=" id='".$_REQUEST['article']."'";
		$art_obj->updateArticle($art_array,$art_where);
		
		
		$writerlist=$part_obj->getparticipation($_REQUEST['article']);
		
		for($p=0;$p<count($writerlist);$p++)
		{
			//update participation status
			$parray=array();
			if($writerlist[$p]['status']=='bid_nonpremium')
				$parray['status']='bid_refused';
			elseif($writerlist[$p]['status']=='bid' || $writerlist[$p]['status']=='disapproved')
				$parray['status']='closed';
			$pwhere=" id='".$writerlist[$p]['id']."'";	
			$part_obj->updateparticipation($parray,$pwhere);	
			
			$parameters['articlelink']="<a href='http://ep-test.edit-place.co.uk/contrib/ongoing'><b>".$writerlist[$p]['title']."</b></a>";
			$this->messageToEPMail($writerlist[$p]['user_id'],107,$parameters);
		}
		
		echo "Closed";
	}
	
	public function zero_cut($str,$digits=0,$percent)
	{
        $str=($str)/($percent/100);
		$value=sprintf("%.${digits}f",$str);
		$value=number_format($str,2,',','');

        if(0==$digits)
                return $value;

        list($left,$right)=explode (",",$value);

        $len=strlen($right); 
        $k=0; 
		 for($i=$len-1;$i>=0;$i--)
		{
                if('0'==$right{$i})
                        $k++;
                else
                        break;
        }

        $right=substr($right,0,$len-$k);
		
		if(""!=$right)
                $right=",$right";

        return $left.$right;

	}
	
	public function findexts ($filename="") 
	{
		$filename = strtolower($filename) ; 
		$exts = split("[/\\.]", $filename) ; 
		$n = count($exts)-1;
		$exts = $exts[$n]; 
		return $exts;
	}
	
	public function findfilename ($filename="") 
	{
		//$filename = strtolower($filename) ; 
		$exts = split("[/\\.]", $filename) ; 
		$n = count($exts)-2;
		$file = $exts[$n]; 
		return $file;
	}
	
	public function totalclientprice($price,$art)
	{
		$art_obj=new Ep_Ao_Article();
		$art_vals=$art_obj->getArticledetails($art);
		
		$percent=$art_vals[0]['contrib_percentage'];
			
		$totprice=($price*100)/$percent;
		return $totprice;
	}
	
	public function getCategoryName($category_value)
    {
        $category_name='';
        $categories=explode(",",$category_value);
        $categories_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        $cnt=0;
        $totcnt=count($categories);

			foreach($categories as $category)
			{
				if($cnt==4)
					break;
				
				if($cnt!=0)
					$category_name.=", ";
					
				$category_name.=$categories_array[$category];
				$cnt++;
			}

        return $category_name;
    }
	
	//Fetching Configuration
	public function getConfiguredval($constraint)
	{
		$conf_obj=new Ep_User_Configuration();
		$conresult=$conf_obj->getConfiguration($constraint);
		return $conresult;
	}
	
	/**UTF8 DECODE function work for msword character also**/
    public function utf8dec($s_String)
    {
	   $s_String=str_replace("e&#769;","&eacute;",$s_String);
	   $s_String=str_replace("E&#769;","&Eacute;",$s_String);
       $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
        return substr($s_String, 0, strlen($s_String)-1);
    }
}
