<?php
class IndexController extends Ep_Controller_Action 
{
	public function init() 
	{
		parent::init();
		//client credentials
		$this->EP_Client = Zend_Registry::get('EP_Client');
		$this->_view->clientidentifier=$this->EP_Client->clientidentifier;
		if($this->EP_Client->clientidentifier!="")
		{
			$user_obj=new Ep_User_User();
			$this->_view->clientname=$user_obj->getClientname($this->_view->clientidentifier);
		}	
		
		//Footer stats
		$stat_obj=new Ep_Stats_Stats();
		$configstat=array('stats_display' => $this->getConfiguredval('stats_display'), 'stats_days_value' => $this->getConfiguredval('stats_days_value'));
		$stats=$stat_obj->getAllStatistics($configstat);
		$this->_view->stats=$stats;

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
			
			/**Selected AO in CART*/
			  $this->EP_Contrib_Cart = Zend_Registry::get('EP_Contrib_Cart');
			  $selected_ao_count=(count($this->EP_Contrib_Cart->cart)+count($this->EP_Contrib_Cart->poll)+count($this->EP_Contrib_Cart->correction));
			  $this->selected_ao_count=$selected_ao_count;
			  $this->_view->selected_ao_count=$this->selected_ao_count;    
			
		}	
		else if($this->EP_Client->clientidentifier!="")
		{
			$this->_view->usertype='client';
		}
		ob_start();
		
		//Based on ip	
		include("geoip.inc");
		$gi = geoip_open("GeoIP.dat",GEOIP_STANDARD);
		
		if(in_array(geoip_country_code_by_addr($gi, $_SERVER['REMOTE_ADDR']), array("FR", "LU", "BE", "CH")) && $_COOKIE['switchalert']!='yes' && $_COOKIE['switchrefresh']!='yes')
			$this->_view->showswitch="yes";
		else
			$this->_view->showswitch="no";  
			
		setcookie("switchrefresh","yes",  strtotime('today 23:59:59') ,"/",".edit-place.com");
		
		
	}
	
	public function indexAction()
	{	
		$delivery_obj=new Ep_Ao_Delivery();
		$part_obj=new Ep_Ao_Participation();
		
		//Writers count
		$user_obj=new Ep_User_User();
		$this->_view->writercount=$user_obj->journalcount();
		$lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		
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
		//print_r($Carousel1);	
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
							//echo $profilepercent."<br>";
							if($profilepercent>50)
							{
								$Carousel2_1[$m1]['percent']=$profilepercent;
								$Carousel2_1[$m1]['profilepic']='/FO/profiles/contrib/pictures/'.$Carousel21List[$c]['identifier'].'/'.$Carousel21List[$c]['identifier'].'_h.jpg';
								$Carousel2_1[$m1]['category']=$this->getCategoryName($Carousel21List[$c]['favourite_category']);
								$Carousel2_1[$m1]['first_name']=$Carousel21List[$c]['first_name'];
								$Carousel2_1[$m1]['last_name']=$Carousel21List[$c]['last_name'];
								$Carousel2_1[$m1]['language']=$Carousel21List[$c]['language'];
								$Carousel2_1[$m1]['languagetitle']=$lang_array[$Carousel21List[$c]['language']];
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
							//echo $profilepercent."<br>";
							if($profilepercent>50)
							{
								$Carousel2_2[$m2]['percent']=$profilepercent;
								$Carousel2_2[$m2]['profilepic']='/FO/profiles/contrib/pictures/'.$Carousel22List[$c]['identifier'].'/'.$Carousel22List[$c]['identifier'].'_h.jpg';
								$Carousel2_2[$m2]['category']=$this->getCategoryName($Carousel22List[$c]['favourite_category']);
								$Carousel2_2[$m2]['first_name']=$Carousel22List[$c]['first_name'];
								$Carousel2_2[$m2]['last_name']=$Carousel22List[$c]['last_name'];
								$Carousel2_2[$m2]['language']=$Carousel22List[$c]['language'];
								$Carousel2_2[$m2]['languagetitle']=$lang_array[$Carousel22List[$c]['language']];
								$m2++;
							}
						}
					}
					
				$this->_view->category2=$categories_array[$cat2];
				$this->_view->Carousel22List=$Carousel2_2;
		
		//Current Quotes
		$quoteslist=$delivery_obj->currentquotesall();
		$this->_view->quotes=$quoteslist;
		
		if($_GET['return_url']!="")
			$this->_view->return_url=urldecode($_GET['return_url']);
			
		$this->_view->page_title="Edit-place.com | writing of online content | writing of SEO articles | natural referencing";
        $this->_view->meta_desc="Edit-place - We write on-demand content - use our skills and our writer database";
        $this->_view->keywords="writing of online content, writing of SEO articles, natural referencing";
		$this->_view->render("Client_index");
	}
	
	//Calculate Contributor Profile Percentage
    public function calculateProfilePercentage($user_id)
    {
          setlocale(LC_TIME, "fr_FR");
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
         
                /**Mon identité**/
                if($profileinfo[0]['initial'] && $profileinfo[0]['first_name'] && $profileinfo[0]['last_name'])
                    $profilePercentage+=15; 

                //Ma date de naissance  
                if($profile_contribinfo[0]['dob'])           
                    $profilePercentage+=5;

                //Photo du profil  
                                
                  $profilePercentage+=10;
               

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
	
	public function setfrenchcookieAction()
	{
		setcookie("FrenchCookie", "", time()+3600); 
		echo "val".$_COOKIE['FrenchCookie'];
		exit;
	}
	
	public function indexfrAction()
	{
		//$this->_redirect("/index/indexen");
		//multilingual
		include("geoip.inc");
		$gi = geoip_open("GeoIP.dat",GEOIP_STANDARD); //"CA", "NZ", "ZA", "SG"
		//echo "cook".$_COOKIE['FrenchCookie']."<br>";
		if($_COOKIE['FrenchCookie']!='set')
		{
			if(in_array(geoip_country_code_by_addr($gi, $_SERVER['REMOTE_ADDR']), array("IN", "AU", "US", "GB","CA")) && !strstr($_SERVER['REQUEST_URI'], "http://uk.edit-place.com/"))
				$this->_redirect("http://mmm-new.edit-place.com/index/indexen");
		}
		geoip_close($gi);
		
		echo "This is FR site<br>";
		echo '<a href="http://mmm-new.edit-place.com/index/indexen" rel="tooltip" data-original-title="Fran�ais"><img class="flag flag-fr" src="/FO/images/shim.gif">EN</a>';
		$this->_view->render("Client_index");
	}
	
	public function indexenAction()
	{
		//echo "cook".$_COOKIE['FrenchCookie']."<br>";
		echo "This is EN site<br>";
		echo '<a href="http://mmm-new.edit-place.com/index/indexfr" rel="tooltip" data-original-title="Fran�ais" onClick="accessfrench();"><img class="flag flag-fr" src="/FO/images/shim.gif">FR</a>';
		$this->_view->render("Client_index");
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
	
	public function loginAction()
	{
		if (Zend_Session::sessionExists())
		{
			Zend_Session::destroy('EP_Client');
			Zend_Session::destroy('EP_Contrib_reg');
		}
		$this->_view->render("Index_login");
	}
	
	public function uservalidationajaxAction()
	{
		$emailcheck_params_login=$this->_request->getParams();//print_r($emailcheck_params_login);exit;
		$obj = new Ep_User_User();	
		
		//avoid sql injection
		$kw=array("update","delete","drop","truncate");
		$nologin='no';
			
		$logspace=explode(" ",$emailcheck_params_login['login_name']);
			
		if(count($logspace)>1)
		{
			foreach ($logspace as $logs)
			{
				if(in_array(strtolower($logs), $kw))
					$nologin='yes';
			}
		}
			
		$passspace=explode(" ",$emailcheck_params_login['login_password']);
		if(count($passspace)>1)
		{
			foreach ($passspace as $pas)
			{
				if(in_array(strtolower($pas), $kw))
					$nologin='yes';
			}
		}
			
		if($nologin=='yes')
		{
			 //Sending mail
			$mail_text='<b>Login:</b> '.$emailcheck_params_login['login_name'].'<br/><b>Password:</b> '.$emailcheck_params_login['login_password'];
			$mail = new Zend_Mail();
			$mail->addHeader('Reply-To','support@edit-place.com');
			$mail->setBodyHtml($mail_text)
				 ->setFrom('support@edit-place.com','Support Edit-place')
				 ->addTo('mailpearls@gmail.com')
				 //->addTo('kavithashree.r@gmail.com')
				 ->setSubject('Suspicious login Uk test FO');
			$mail->send();
			
			$res="NO";
			echo $res;
			exit;
		}
		
		$res= $obj->checkUserMailidLogin($emailcheck_params_login['login_name'],$emailcheck_params_login['login_password']);
		
		$result=explode("@",$res);
		
		//update last visit
		if($res!="NO")
		{
			$obj->updatevisit($result[0]);
			//Zend_Session::destroy();
		}	
		if($result[1]=='client')
		{	$username=$emailcheck_params_login['login_name'];
			$this->EP_Client = Zend_Registry::get('EP_Client');
			$this->EP_Client->clientidentifier =$result[0];
			$this->EP_Client->clientemail =$username;			
			
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
	
	public function loginvalidationliberteAction()
	{
		$emailcheck_params_login=$this->_request->getParams();
		$obj = new Ep_User_User();	
		
				//avoid sql injection
		$kw=array("update","delete","drop","truncate");
		$nologin='no';
			
		$logspace=explode(" ",$emailcheck_params_login['libertelogin_name']);
			
		if(count($logspace)>1)
		{
			foreach ($logspace as $logs)
			{
				if(in_array(strtolower($logs), $kw))
					$nologin='yes';
			}
		}
			
		$passspace=explode(" ",$emailcheck_params_login['libertelogin_password']);
		if(count($passspace)>1)
		{
			foreach ($passspace as $pas)
			{
				if(in_array(strtolower($pas), $kw))
					$nologin='yes';
			}
		}
			
		if($nologin=='yes')
		{
			 //Sending mail
			$mail_text='<b>Login:</b> '.$emailcheck_params_login['libertelogin_name'].'<br/><b>Password:</b> '.$emailcheck_params_login['libertelogin_password'];
			$mail = new Zend_Mail();
			$mail->addHeader('Reply-To','support@edit-place.com');
			$mail->setBodyHtml($mail_text)
				 ->setFrom('support@edit-place.com','Support Edit-place')
				 ->addTo('mailpearls@gmail.com')
				 //->addTo('kavithashree.r@gmail.com')
				 ->setSubject('Suspicious login UK test FO');
			$mail->send();
			
			$res="NO";
			echo $res;
			exit;
		}
		
		$res= $obj->checkUserMailidLogin($emailcheck_params_login['libertelogin_name'],$emailcheck_params_login['libertelogin_password']);
		//echo $res;exit;
		$result=explode("@",$res);
		
		//update last visit
		if($res!="NO")
			$obj->updatevisit($result[0]);
			
		if($result[1]=='client')
		{	$username=$emailcheck_params_login['libertelogin_name'];
			$this->EP_Client = Zend_Registry::get('EP_Client');
			$this->EP_Client->clientidentifier =$result[0];
			$this->EP_Client->clientemail =$username;	
			$this->_redirect("/client/liberte1");		
			
		}
		else if($result[1]=='contributor')
		{
			$username=$emailcheck_params_login['libertelogin_name'];
			$this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
			$this->EP_Contrib_reg->clientidentifier =$result[0];
			$this->EP_Contrib_reg->clientemail =strtolower($username);
			$this->_redirect("/contrib/home");	
		}

		//echo $result[1];
		exit;		
	}
	
	public function userfologinAction()
	{
		$emailcheck_params_login=$this->_request->getParams();
		$obj = new Ep_User_User();	
		$res= $obj->checkUserMailidLogin($emailcheck_params_login['login_name'],$emailcheck_params_login['login_password']);
		
		$result=explode("@",$res);
		
		//update last visit
		if($res!="NO")
		{
			$obj->updatevisit($result[0]);
			//Insert UserLogins
			$userl_obj=new Ep_User_UserLogins();
				$userl_data=array("user_id"=>$result[0],"type"=>$result[1],"login_type"=>"linkbo","ip"=>$_SERVER['REMOTE_ADDR']);
			$userl_obj->InsertLogin($userl_data);
		}
			
		if($result[1]=='client')
		{	$username=$emailcheck_params_login['login_name'];
			$this->EP_Client = Zend_Registry::get('EP_Client');
			$this->EP_Client->clientidentifier =$result[0];
			$this->EP_Client->clientemail =$username;	
            //$this->_redirect($emailcheck_params_login['return_url'] ? $emailcheck_params_login['return_url'] : "/client/home"); 
            $this->_redirect("/client/home") ; 
			
		}
		else if($result[1]=='contributor')
		{
			$username=$emailcheck_params_login['login_name'];
			$this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
			$this->EP_Contrib_reg->clientidentifier =$result[0];
			$this->EP_Contrib_reg->clientemail =strtolower($username);
			$this->_redirect("/contrib/home");
		}
				
	}
	
	public function checkuseremailAction()
	{
		$emailcheck_params_duplicate=$this->_request->getParams();
		$obj = new Ep_User_User();	
		$res= $obj->checkClientMailid($emailcheck_params_duplicate['email']);
		echo $res;
		exit;		
	}
	
	//Fetching Configuration
	public function getConfiguredval($constraint)
	{
		$conf_obj=new Ep_User_Configuration();
		$conresult=$conf_obj->getConfiguration($constraint);
		return $conresult;
	}

	/**footer cgu page**/
	public function termsOfServiceAction()
	{
		$this->_view->meta_title="Edit-place's terms of use";
		
		$dom = new DOMDocument;
		$dom->load("/home/sites/site7/web/FO/cgu/cgu.xml");
		
		$titles = $dom->getElementsByTagName('title');
		foreach ($titles as $title) 
			$this->_view->cgutitle=$title->nodeValue;
		
		$contents = $dom->getElementsByTagName('content');
		foreach ($contents as $content) 
			$this->_view->cgucontent=$content->nodeValue; 
        
        $this->render("footer_cgu");
	}
	
	/**footer jobs page**/
	public function jobsAction()
	{
        $this->_view->meta_title="Edit-place is recruiting";
		
		$user_obj=new Ep_User_User();
		$this->_view->joblist=$user_obj->getJobs();
        $this->render("footer_jobs");
    }
    
	public function ourPartnersAction()
	{
        $this->_view->meta_title="Edit-place: our partners";
		$user_obj=new Ep_User_User();
		$this->_view->partnerlist=$user_obj->getPartners();
        $this->render("footer_partners");
    }
    
	public function aboutUsAction()
	{
		$this->_view->meta_title="Edit-place: About us";
		$user_obj=new Ep_User_User();
		$this->_view->teamlist=$user_obj->getTheteam();
        $this->render("footer_aboutus");//EPFO_HP
	}
	
	public function ourReferencesAction()
	{
		$this->_view->meta_title="They have trusted Edit-place for their online content: writing, translation, adaptation";
		$this->_view->meta_desc="Find a list of all our clients and let us help you. Writers and experts write for you and present you with their rates.";
		
		$user_obj=new Ep_User_User();
		$this->_view->referencelist=$user_obj->getReferences();
		
        $this->render("footer_references");//EPFO_HP
	}
	
	public function contactAction()
	{
		if($this->_helper->FlashMessenger->getMessages()) {
                $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }
			
		$dom = new DOMDocument;
		$dom->load("/home/sites/site7/web/FO/cgu/contact.xml");
		
		$contents = $dom->getElementsByTagName('content');
		foreach ($contents as $content) 
			$this->_view->contacttext=$content->nodeValue; 
			
		$this->_view->meta_title="Contact us";		
        $this->render("footer_contactus");//EPFO_HP
	}
	
	public function captchaAction()
	{		
		session_start();
		
		$str = "";
		$length = 0;
		for ($i = 0; $i < 6; $i++) {
		// these numbers represent ASCII table (small letters)
		$str .= chr(rand(97, 122));
		}
		
		//md5 letters and saving them to session
		$letters = md5($str);
		$_SESSION['letters'] = $letters;
			
		//determine width and height for our image and create it
		$imgW = 200;
		$imgH = 60;
		$image = imagecreatetruecolor($imgW, $imgH);
		
		//setup background color and border color
		$backgr_col = imagecolorallocate($image, 238,239,239);
		$border_col = imagecolorallocate($image, 208,208,208);
		
		//let's choose color in range of purple color
		$text_col = imagecolorallocate($image, rand(70,90),rand(50,70),rand(120,140));
		
		//now fill rectangle and draw border
		imagefilledrectangle($image, 0, 0, $imgW, $imgH, $backgr_col);
		imagerectangle($image, 0, 0, $imgW-1, $imgH-1, $border_col);
		
		//save fonts in same folder where you PHP captcha script is
		//name these fonts by numbers from 1 to 3
		//we shall choose different font each time
		$fn = rand(1,3);
		$font = "fonts/arial.ttf";
		
		//setup captcha letter size and angle of captcha letters
		$font_size = "25";//$imgH / 2.2;
		$angle = rand(-15,15);
		$box = imagettfbbox($font_size, $angle, $font, $str);
		$x = (int)($imgW - $box[4]) / 2;
		$y = (int)($imgH - $box[5]) / 2;
		imagettftext($image, $font_size, $angle, $x, $y, $text_col, $font, $str);
		
		
		
		//now we should output captcha image
		header("Content-type: image/png");
		imagepng($image);
		imagedestroy ($image);
	}
	public function savecontactAction()
    {
         if($this->_request-> isPost())
        {
            require_once(LIB_PATH.'tools/captcha/recaptchalib.php');
			

            $Params=$this->_request->getParams();
            

               $privatekey  = "6Lcc-skSAAAAAM9srK-lddllbp6NGSOSK5fAfQT2";
                $resp = recaptcha_check_answer ($privatekey,
                                                $_SERVER["REMOTE_ADDR"],
                                                $Params["recaptcha_challenge_field"],
                                                $Params["recaptcha_response_field"]);
												
				session_start();
				if($_SESSION['letters'] == md5(strtolower($_POST['captcha'])))
					$is_valid=1;
				else
					$is_valid=0;
				
				
                /*if (!$resp->is_valid)*/ 
				if(!$is_valid) {

                    // What happens when the CAPTCHA was entered incorrectly
                    $url="/index/contact?captcha=no&name=".utf8dec($Params['name'])."&email=".utf8dec($Params['email'])."
                                &msg_object=".utf8dec($Params['msg_object'])."
                                &mail_message=".utf8dec($Params['mail_message']);
                    //echo $url;exit;
                   $url=preg_replace('/\s\s+/', '',$url);
                    //$url=urlencode($url);
                    $this->_redirect($url);
                    

                   }
                   else {
                   	

                       $contact=new EP_Contrib_Contact();
                       $contact->name=utf8dec($Params['name']);
                       $contact->email=utf8dec($Params['email']);
                       $contact->msg_object=utf8dec($Params['msg_object']);
                       $contact->message=nl2br(utf8dec($Params['mail_message']));
                       $contact->created_at=date('Y-m-d h:i:s');
                       $contact->status='0';

                       try
                       {
                            $contact->insert();
							//$to='contact@edit-place.com';
							//$to='arunravuri@edit-place.com';
							$to='jwolff@edit-place.com';
                            $mail = new Zend_Mail();
                            $mail->addHeader('Reply-To',$contact->email);
                            $mail->setBodyHtml($contact->message)
                                 ->setFrom($contact->email,$contact->name)
                                 ->addTo($to,'Contact Edit-place')
                                 ->setSubject($contact->msg_object);
                            $mail->send();
                            $this->_helper->FlashMessenger('Thank you for your email. You will be contacted within 48 hours by our sales team.');
                            $this->_redirect("/index/contact");
                       }
                       catch(Zend_Exception $e)
                        {
                           $url="/index/contact?captcha=no&name=".$this->utf8dec($Params['name'])."&email=".$this->utf8dec($Params['email'])."
                                &msg_object=".$this->utf8dec($Params['msg_object'])."
                                &mail_message=".$this->utf8dec($Params['mail_message']);
                             //echo $url;exit;
                             $url=preg_replace('/\s\s+/', '',$url);
                             //$url=urlencode($url);
                             $this->_redirect($url);
                       }

                }
        }
    }    
	public function hubAction()
	{
		$this->render("index_hub");
	}
	
	public function setswitchcookieAction()
	{
		//setcookie("switchalert","yes", strtotime('today 23:59:59') ,"/",".edit-place.co.uk");
		$cookietime=time() + (10 * 365 * 24 * 60 * 60);
		setcookie("switchalert","yes", $cookietime ,"/",".edit-place.com");
		exit;	
	}
	
	public function whiteBookAction()  
	{
		//$this->_view->meta_title="10 choses &agrave; changer pour booster son trafic en 2015";
		$this->render("index_whitebook");
	}
	
	public function submitwhitebookAction()
	{ 
		if($_POST['firstname']!="")
		{ 
			session_start();
			if($_SESSION['letters'] == md5(strtolower($_POST['captcha'])))
				$is_valid=1;
			else
				$is_valid=0;
			
			if(!$is_valid) {
				  echo 'notdone';
			}
			else
			{	
				$wb_obj=new Ep_User_WhitebookDownloads();
				$wbArray=array();
				$wbArray['name']=$_POST['firstname'];
				$wbArray['surname']=$_POST['surname'];
				if($_POST['contactbo']=="yes")
					$wbArray['contactbo']=$_POST['contactbo'];
				else
					$wbArray['contactbo']="no";
				$checkEmailindb=$wb_obj->checkEmailindb($_POST['email']);
				if($checkEmailindb!="no")
					$wb_obj->updateDownload($wbArray,$checkEmailindb);
				else
				{
					$wbArray['email']=$_POST['email'];
					$wbArray['status']=$this->checkEmailExists($_POST['email']);
					$wb_obj->insertdownload($wbArray);
				}
				
				//Mail
				//$to='kavithashree.r@gmail.com';
				$to=$_POST['email'];
				$mail_from=$this->getConfiguredval("mail_from");
					$encoded_email=base64_encode($_POST['email']);
				$content="<img src='http://ep-test.edit-place.co.uk/FO/images/logo-edit-place.png' /><br><br>Suite &agrave; votre demande, nous sommes ravis de vous faire parvenir notre livre blanc sur les 10 choses &agrave; changer pour booster son trafic en 2015 :<br><br>
				<a href='http://ep-test.edit-place.co.uk/index/download-whitebook?hex=".$encoded_email."'><b>Le nouveau visage du contenu web</b></a><br><br>
				Le document PDF va s'ouvrir dans votre navigateur et vous pourrez le sauvegarder facilement.<br><br>
				N'h&eacute;sitez pas &agrave; nous faire signe pour toute question ou projet de contenu, <br><br>
				A bient&ocirc;t !<br><br>
				Marie-Ad&eacute;la&iuml;de Gervis<br><br>
				<table width=400px cellspacing=0 cellpadding=8 bgcolor=#3A3A3A>
					<tbody>
						<tr valign=top>
							<td align=center valign=top ><img src='http://ep-test.edit-place.co.uk/FO/images/profile/fmadelaide.jpg' alt='Marie-Ad&eacute;la&iuml;de Gervis' width=70 height=70 /></td>
							<td style='color: #828282;' bgcolor='#3A3A3A'>
								<p>Marie-Ad&eacute;la&iuml;de Gervis<span id='title-sep'>&nbsp;/</span>&nbsp;Head of Marketing&nbsp;<br />
								<a id='email-input' href='mailto:magervis@edit-place.com'>magervis@edit-place.com</a><br />
								<span id='office-sep'>Mobile :&nbsp;</span><span id='office-input'>+352 621 176 086</span><br />
								<span id='office-sep'>Office :&nbsp;</span><span id='office-input'>+33 9 72 34 56 26</span><br /><br />
								<span id='office-sep'>Follow us on &nbsp;</span><span id='office-input'><a href='http://www.edit-place.com/blog/'>Edit-place blog !</a></span></p>
								<span id='office-sep'>Watch our video &nbsp;</span><span id='office-input'><a href='https://www.youtube.com/watch?v=qjeLGW7EQ44'>French</a>&nbsp;&&nbsp;<a href='https://www.youtube.com/watch?v=A9P5dT1Rp9Y&feature=youtu.be'>English</a></span><br /><br />
								<span id='office-sep'><img src='http://ep-test.edit-place.co.uk/FO/images/twt.png' width='15'/></span>&nbsp;<span id='office-sep'><img src='http://ep-test.edit-place.co.uk/FO/images/in.png' width='15'/></span><br /><br />
								<a href='http://www.edit-place.com/'><img id='sig-logo' src='http://www.edit-place.com/extra/signature/logo.png' alt='Edit-place' width='130' height='30' border='0' /></a>
							</td>
						</tr>
					</tbody>
				</table>
				";
				$mail = new Zend_Mail();
				$mail->addHeader('Reply-To','marketing@edit-place.com');
				$mail->setBodyHtml($content)
					 ->setFrom('marketing@edit-place.com','Marketing Edit-place')
					 ->addTo($to)
					 ->setSubject("White book");
				$mail->send();
				echo 'done';
			}
		}
		
	}
	
	public function downloadWhitebookAction()
	{
		$filename=APP_PATH_ROOT. '/whitebook/whitebook.pdf';
		
		$email=base64_decode($_REQUEST['hex']);
		$wb_obj=new Ep_User_WhitebookDownloads();
		$check=$wb_obj->checkEmailindb($email);
		
		if($check!="no")
		{
			//Download file
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=Edit-place_-_white_book.pdf');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			readfile($filename);	
		}
		else
			$this->_redirect("/index/white-book");
	}
	
	public function checkEmailExists($email)
	{
		require_once(APP_PATH_ROOT.'smtp_validateEmail.class.php');
		$sender = 'user@mydomain.com';
		$SMTP_Validator = new SMTP_validateEmail();
		$results = $SMTP_Validator->validate(array($email), $sender);
		
		// send email? 
		if ($results[$email]) 
			return 'valid';
		else
			return 'invalid';
		/*	
		include_once APP_PATH_ROOT.'class.verifyEmail.php';	
		
        $vmail = new verifyEmail();

        if ($vmail->check($email)) 
           return 'valid';
        else 
            return 'invalid';
        */
	}
}
