<?php

/**
 * Edit Place controller action extension class
 *
 * @category MyFramework
 * @author vinay
 * @package Controller
 * @version
 * @copyright MyFrameWork
 */

abstract class Ep_Controller_Action extends Zend_Controller_Action
{

    /**
     * View object
     * @var Zend_View_Interface
     */
    protected $_view;

     /**
     * ArrayDb object
     * @var ArrayDb
     */
    protected $_arrayDb;

     /**
     * Language
     * @var String
     */
    protected $_lang;

     /**
     * Config object
     * @var Zend_Config_Ini
     */
    protected $_config;

     /**
     * Balance
     * @var Boolean
     */
    protected $_balance;

	/**
	 * Initialization
	 *
	 * Register common view
	 *
	 * @author farid
	 *
	 * @return void
	 *
	 */
	public function init()
	{
	    parent::init();
		$this->_view = Zend_Registry::get('_view');
		$this->_arrayDb = Zend_Registry::get('_arrayDb');
		$this->_lang = Zend_Registry::get('_country');
		$this->_config = Zend_Registry::get('_config');
		$this->_balance = Zend_Registry::get('_balance');
		$this->_view->balance = $this->_balance;

		if ($_SERVER['HTTPS']){
			$this->_view->staticbase = $this->_config->www->staticbasessl;
			$this->_view->staticbase2 = $this->_config->www->staticbasessl;
			$this->_view->on_https = true;
		}else{
			$this->_view->staticbase = $this->_config->www->staticbase;
			$this->_view->staticbase2 = $this->_config->www->staticbase2;
			$this->_view->on_https = false;
		}
        ////// fetch all universal arrays////////
        $this->category_array  = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        $this->_view->category_array = $this->category_array;
        $this->signtype_array   = $this->_arrayDb->loadArrayv2("EP_ARTICLE_SIGN_TYPE", $this->_lang);
        $this->_view->signtype_array = $this->signtype_array;
        $this->type_array  = $this->_arrayDb->loadArrayv2("EP_ARTICLE_TYPE", $this->_lang);
        $this->_view->type_array = $this->type_array;
        $this->language_array  = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        $this->_view->language_array = $this->language_array;
        $this->profession_array  = $this->_arrayDb->loadArrayv2("CONTRIB_PROFESSION", $this->_lang);
        $this->_view->profession_array = $this->profession_array;
        $this->country_array  = $this->_arrayDb->loadArrayv2("countryList", $this->_lang);
        $this->_view->country_array = $this->country_array;
        $this->nationality_array  = $this->_arrayDb->loadArrayv2("Nationality", $this->_lang);

       // print_r($this->nationality_array); print_r($this->country_array); print_r($this->language_array);
      /*  if($this->nationality_array)
            array_unshift($this->nationality_array, " ");*/
        $this->_view->nationality_array = $this->nationality_array;
        ////////////////to display list dropdown in search criteria///////////////
        $art_obj = new EP_Delivery_Article();
        $del_obj = new Ep_Delivery_Delivery();
        $client_info_obj = new Ep_User_User();
        $contrib_obj = new EP_User_Contributor();
        $client_info= $client_info_obj->GetclientList();
        $contrib_info= $client_info_obj->getContributorsList();
        $incharge_info= $client_info_obj->getAllBoUsers();//print_r($incharge_array); exit;
        $ao_info= $del_obj->getAllAos();
        $art_info= $art_obj->getArticleList();
        $client_list=array();
        $contrib_list=array();
        foreach($client_info as $key=>$value)
        {
            if($value['company_name'] == '')
			{
                if($value['first_name']=="")
					$value['first_name']="--";
				if($value['last_name']=="")
					$value['last_name']="--";

				$client_list[$value['identifier']] = strtoupper($value['email'].' ( '.$value['first_name'].' , '.$value['last_name'].' ) ');
            }
			else
                $client_list[$value['identifier']] = strtoupper($value['email'].' ( '.$value['company_name'].' ) ');
        }

        foreach($contrib_info as $key=>$value)
        {
            $contrib_list[$value['identifier']]=utf8_encode(strtoupper($value['email'].'('.$value['first_name'].','.$value['last_name'].')'));
        } //print_r($contrib_list);
        foreach($ao_info as $key=>$value)
        {
            $aoList[$value['id']]=strtoupper($value['title']);
        }
        foreach($incharge_info as $key=>$value)
        {
            $aoIncharge[$value['identifier']]=strtoupper($value['login']);
        }
        if($art_info != 'NO')
        {
            foreach($art_info as $key=>$value)
            {
                $artList[$value['id']]=strtoupper($value['title']);
            }
        }
        if($client_list)
            asort($client_list);
        /*if($contrib_list)
            asort($contrib_list);*/
        if($aoList)
            asort($aoList);
        if($aoIncharge)
            asort($aoIncharge);
        if($artList)
            asort($artList);
        /*if($client_list)
            array_unshift($client_list, " ");*/
        /*if($contrib_list)
            array_unshift($contrib_list, " ");*/
        if($aoList)
            array_unshift($aoList, " ");
        if($artList)
            array_unshift($artList, " ");
       /* if($aoIncharge)
            array_unshift($aoIncharge, " ");*/
        //print_r($contrib_list); exit;
        $this->contriblist_array=$contrib_list;
        $this->_view->article_array = $artList;
        $this->_view->delivery_array = $aoList;
        $this->_view->client_array=$client_list;
        $this->_view->contributor_array=$contrib_list;
        $this->_view->incharge_array=$aoIncharge; //print_r($aoIncharge); exit;
        $this->client_list = $client_list;
        ///count of all contributo based on type///
        /*$this->_view->sc_count=$contrib_obj->getContributorcount('senior');
        $this->_view->jc_count=$contrib_obj->getContributorcount('junior');
        $this->_view->jc0_count=$contrib_obj->getContributorcount('sub-junior');*/
		///////////activate mainlinks and submenulinks ///////////////////////////////////////
		$this->adminLogin = Zend_Registry::get('adminLogin');
        //echo $this->adminLogin->userId; exit;
       /* echo $this->adminLogin->userId;
        if($this->adminLogin->userId == '' && $_SERVER['REQUEST_URI']== '/index')
        { echo $_SERVER['REQUEST_URI']; echo "ddd";exit; }*/
                $this->_view->loginuserId = $this->adminLogin->userId;
                $this->_view->groupId = $this->adminLogin->groupId;
                $this->_view->menulist = $this->adminLogin->menuId;
                $this->_view->groupmenulist = $this->adminLogin->groupmenuId;
		        $this->_view->loginName = $this->adminLogin->loginName;
                $this->_view->language = $this->adminLogin->language;
				$this->_view->accessCode = $this->adminLogin->accessCode;
				$this->_view->sectionHeadings = $this->adminLogin->sectionHeadings;
				$this->_view->permission = $this->adminLogin->permission;
				$this->_view->sectionHeadNew = $this->adminLogin->sectionHeadNew;
				$this->_view->user_type = $this->adminLogin->type;
				$p = new Ep_Controller_Page();
		$sections = $p->getSectionpages();
		$this->_view->objPage = $sections;
		$this->_view->pageObj = $p;
		$this->mainMenu = Zend_Registry::get('mainMenu');
		$this->_view->menuId = $this->mainMenu->menuId;
		$this->mainMenu->submenuId = $this->_request->getParam('submenu');
	    $this->_view->submenuId = $this->mainMenu->submenuId;



        $this->commonAction();//////////including main menu and left panel content
        $this->configval=$this->getConfiguredval();

        ////to show message when action performed in all pages////////
        if($this->_helper->FlashMessenger->getMessages()) {
            $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            //echo "<pre>";print_r($this->_view->actionmessages);
        }
        ///getting the count of to be validated leaves by managers and desplayed in header//
        //$hrmleaves_obj=new Ep_Hrm_HrmLeaves();
        //$leaverequests = $hrmleaves_obj->inprocessLeaves($this->adminLogin->userId);
        $this->_view->waitingleaverequests  = $leaverequests[0]['inprocesscount'];
    }

	/* *
	 * Render a view
	 *
	 * Dispatch common static header html module
	 *
	 * @author farid
	 * @return void
	 * @param string name
	 *
	 */

	public function render($name)
	{
	    $begin = microtime(true);
    	$response = $this->getResponse();
    	$response->clearBody();
    	$response->append('main', $this->_view->render($name));
	}

	/*
	 * Render a view for ajax call
	 *
	 * Dispatch only particular file's content
	 *
	 * @author Ratnadeep
	 * @return void
	 * @param string name
	 *
	 */
	public function ajaxRender($name)
	{
	    $begin = microtime(true);
    	$response = $this->getResponse();
    	$response->clearBody();
    	$response->append('main', $this->_view->renderAjax($name));
	}
	public function WriterParticipationExpire($artId)
    {
        ////update the artlcle table with partcipation time/////////
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new EP_Delivery_Article();
        $deliveryDetails = $delivery_obj->getArtDeliveryDetails($artId);
        $expires=time()+(60*$deliveryDetails[0]['participation_time']);
        $data = array("participation_expires"=>$expires, "bo_closed_status"=>NULL, "send_to_fo"=>"yes");////////updating
        $query = "id= '".$artId."'";
        $article_obj->updateArticle($data,$query);
    }
    public function CorrectorParticipationExpire($artId)
    {
        ////update the artlcle table with partcipation time/////////
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new EP_Delivery_Article();
        $deliveryDetails = $delivery_obj->getArtDeliveryDetails($artId);
        $expires=time()+(60*$deliveryDetails[0]['correction_participation']);
        $data = array("correction_participationexpires"=>$expires, "correction_closed_status"=>NULL, "send_to_fo"=>"yes");////////updating
        $query = "id= '".$artId."'";
        $article_obj->updateArticle($data,$query);
    }
    /////////////Fetching Configuration
	public function getConfiguredval($constraint=NULL)
	{
		$conf_obj=new Ep_Delivery_Configuration();
		//$conresult=$conf_obj->getConfiguration($constraint);
		 $conresult=$conf_obj->ListConfiguration($constraint);
         foreach ($conresult as $key => $value) {
            $res[$value['configure_name']]   = $value['configure_value'];
         }
        return $res;

	}

   ////assaing the expire time with submit time  based on user profiles////
   function writerExpireTime($artId, $userId)
   {
   	    $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
   		$delivery_details=$delivery_obj->getArtDeliveryDetails($artId);
        $user_details=$user_obj->getAllUsersDetails($userId);

         if($user_details[0]['profile_type']=='senior')
         {
            if($delivery_details[0]['senior_time'])
               $time=$delivery_details[0]['senior_time'];//2days
            else
               $time=$this->configval["sc_time"];//2days
         }
         else if($user_details[0]['profile_type'] == 'junior')
         {
            if($delivery_details[0]['junior_time'])
               $time=$delivery_details[0]['junior_time'];//2days
            else
               $time=$this->configval["jc_time"];
         }
         else if($user_details[0]['profile_type'] == 'sub-junior')
         {
             if($delivery_details[0]['subjunior_time'])
                $time=$delivery_details[0]['subjunior_time'];//2days
             else
                $time=$this->configval["jc0_time"];//2days
         }
        	return $expires=time()+(60*$time);
   }
    ////assaing the expire time with submit time  based on user profiles////
    function writerSubmitTime($artId, $userId)
    {
        $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
        $delivery_details=$delivery_obj->getArtDeliveryDetails($artId);
        $user_details=$user_obj->getAllUsersDetails($userId);

        if($user_details[0]['profile_type']=='senior')
        {
            if($delivery_details[0]['senior_time'])
                $time=$delivery_details[0]['senior_time'];//2days
            else
                $time=$this->configval["sc_time"];//2days
        }
        else if($user_details[0]['profile_type'] == 'junior')
        {
            if($delivery_details[0]['junior_time'])
                $time=$delivery_details[0]['junior_time'];//2days
            else
                $time=$this->configval["jc_time"];
        }
        else if($user_details[0]['profile_type'] == 'sub-junior')
        {
            if($delivery_details[0]['subjunior_time'])
                $time=$delivery_details[0]['subjunior_time'];//2days
            else
                $time=$this->configval["jc0_time"];//2days
        }
        return $time;
    }
    ////assaing the expire time with re-submit time based on user profiles ////
    function writerExpireResubmitTime($artId, $userId)
    {
        $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
        $delivery_details=$delivery_obj->getArtDeliveryDetails($artId);
        $user_details=$user_obj->getAllUsersDetails($userId);
        if($user_details[0]['profile_type'] == 'sub-junior')
        {
            if($delivery_details[0]['jc0_resubmission'])
                $subtime=$delivery_details[0]['jc0_resubmission'];//2days
            else
                $subtime = $this->configval["jc0_resubmission"];
        }
        else if($user_details[0]['profile_type'] == 'junior')
        {
            if($delivery_details[0]['jc_resubmission'])
                $subtime=$delivery_details[0]['jc_resubmission'];//2days
            else
                $subtime = $this->configval["jc_resubmission"];
        }
        else if($user_details[0]['profile_type'] == 'senior')
        {
            if($delivery_details[0]['sc_resubmission'])
                $subtime=$delivery_details[0]['sc_resubmission'];//2days
            else
                $subtime = $this->configval["sc_resubmission"];
        }
        return $expires=time()+(60*$subtime);
    }
    ////assaing the expire time with re-submit time based on user profiles ////
    function writerResubmitTime($artId, $userId)
    {
        $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
        $delivery_details=$delivery_obj->getArtDeliveryDetails($artId);
        $user_details=$user_obj->getAllUsersDetails($userId);
        if($user_details[0]['profile_type'] == 'sub-junior')
        {
            if($delivery_details[0]['jc0_resubmission'])
                $resubtime=$delivery_details[0]['jc0_resubmission'];//2days
            else
                $resubtime = $this->configval["jc0_resubmission"];
        }
        else if($user_details[0]['profile_type'] == 'junior')
        {
            if($delivery_details[0]['jc_resubmission'])
                $resubtime=$delivery_details[0]['jc_resubmission'];//2days
            else
                $resubtime = $this->configval["jc_resubmission"];
        }
        else if($user_details[0]['profile_type'] == 'senior')
        {
            if($delivery_details[0]['sc_resubmission'])
                $resubtime=$delivery_details[0]['sc_resubmission'];//2days
            else
                $resubtime = $this->configval["sc_resubmission"];
        }
        return $resubtime;
    }
    ////assaing the expire time with submit time  based on user profiles////
    function correctorExpireTime($artId, $userId)
    {
        $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
        $delivery_details=$delivery_obj->getArtDeliveryDetails($artId);
        $user_details=$user_obj->getAllUsersDetails($userId);
        if($user_details[0]['profile_type2']=='senior')
        {
            if($delivery_details[0]['correction_sc_submission'])
                $time=$delivery_details[0]['correction_sc_submission'];//2days
            else
                $time=$this->configval["correction_sc_submission"];
        }
        else if($user_details[0]['profile_type2']=='junior')
        {
            if($delivery_details[0]['correction_jc_submission'])
                $time=$delivery_details[0]['correction_jc_submission'];//2days
            else
                $time=$this->configval["correction_jc_submission"];//2days
        }
        return $expires=time()+(60*$time);
    }
    ////assaing the expire time with submit time  based on user profiles////
    function correctorSubmitTime($artId, $userId)
    {
        $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
        $delivery_details=$delivery_obj->getArtDeliveryDetails($artId);
        $user_details=$user_obj->getAllUsersDetails($userId);
        if($user_details[0]['profile_type2']=='senior')
        {
            if($delivery_details[0]['correction_sc_submission'])
                $time=$delivery_details[0]['correction_sc_submission'];//2days
            else
                $time=$this->configval["correction_sc_submission"];
        }
        else if($user_details[0]['profile_type2']=='junior')
        {
            if($delivery_details[0]['correction_jc_submission'])
                $time=$delivery_details[0]['correction_jc_submission'];//2days
            else
                $time=$this->configval["correction_jc_submission"];//2days
        }
        return $time;
    }
    ////assaing the expire time with submit time  based on user profiles////
    function correctorResubmitTime($artId, $userId)
    {
        $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
        $delivery_details=$delivery_obj->getArtDeliveryDetails($artId);
        $user_details=$user_obj->getAllUsersDetails($userId);
        if($user_details[0]['profile_type2']=='senior')
        {
            if($delivery_details[0]['correction_sc_resubmission'])
                $time=$delivery_details[0]['correction_sc_resubmission'];//2days
            else
                $time=$this->configval["correction_sc_resubmission"];
        }
        else if($user_details[0]['profile_type2']=='junior')
        {
            if($delivery_details[0]['correction_jc_resubmission'])
                $time=$delivery_details[0]['correction_jc_resubmission'];//2days
            else
                $time=$this->configval["correction_jc_resubmission"];//2days
        }
        return $time;
    }
    ////assaing the expire time with re-submit time based on user profiles ////
    function correctorExpireResubmitTime($artId, $userId)
    {
        $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
        $delivery_details=$delivery_obj->getArtDeliveryDetails($artId);
        $user_details=$user_obj->getAllUsersDetails($userId);
        if($user_details[0]['profile_type'] == 'sub-junior')
        {
            if($delivery_details[0]['jc0_resubmission'])
                $subtime=$delivery_details[0]['jc0_resubmission'];//2days
            else
                $subtime = $this->configval["jc0_resubmission"];
        }
        else if($user_details[0]['profile_type'] == 'junior')
        {
            if($delivery_details[0]['jc_resubmission'])
                $subtime=$delivery_details[0]['jc_resubmission'];//2days
            else
                $subtime = $this->configval["jc_resubmission"];
        }
        else if($user_details[0]['profile_type'] == 'senior')
        {
            if($delivery_details[0]['sc_resubmission'])
                $subtime=$delivery_details[0]['sc_resubmission'];//2days
            else
                $subtime = $this->configval["sc_resubmission"];
        }
        return $expires=time()+(60*$subtime);
    }
    /////////mails send in republish development////////////
    function sendMailToContribs($art_id)
    {
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new EP_Delivery_Article();
        $user_obj = new Ep_User_User();
        $automail=new Ep_Message_AutoEmails();
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
        $autoEmails=new Ep_Message_AutoEmails();
        $parameters['AO_title']=$aoDetails[0]['title'];
        $parameters['AO_end_date']=$aoDetails[0]['delivery_date'];
        //$parameters['submitdate_bo']=$aoDetails[0]['submitdate_bo'];
        $parameters['submitdate_bo']=date('d/m/Y H:i', $expires);

        $parameters['noofarts']=$aoDetails[0]['noofarts'];
        if($aoDetails[0]['deli_anonymous']=='0')
            $parameters['article_link']="/contrib/aosearch?client_contact=".$aoDetails[0]['user_id'];
        else
            $parameters['article_link']="/contrib/aosearch?client_contact=anonymous";
        $parameters['aoname_link'] = "/contrib/aosearch";
        $parameters['clientartname_link'] = "/client/quotes?id=".$aoDetails[0]['articleid'];
        $object = $aoDetails[0]['republish_object'];
        $message = ($aoDetails[0]['republish_mail']);
        if($aoDetails[0]['mail_send_contrib']=='yes')
        {
            //Priority contributors mail
            if($aoDetails[0]['priority_contributors']!="")
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
                            //$automail->messageToEPMail($pcontrib,15,$prior_parameters);///
                            $automail->sendMailEpMailBox($pcontrib,$object,$message);
                }
            }
            //Only for poll not linked AOs
            if($aoDetails[0]['poll_id']=="")
            {
                if($aoDetails[0]['AOtype']=='private')
                {
                    $contributors=array_unique(explode(",",$aoDetails[0]['article_contribs']));
                    if(is_array($contributors) && count($contributors)>0)
                    {
                        foreach($contributors as $contributor)
                        {
                            if(!in_array($contributor,$nomailsendlist)) ///sending to only non participants
                                $automail->sendMailEpMailBox($contributor,$object,$message);
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
                            $sclimit=$this->configval['sc_limit'];
                            foreach($contributors as $contributor)
                            {
                                $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);
                                if($sclimit > $countofparts[0]['partscount'])
                                {
                                    if(!in_array($contributor['identifier'],$nomailsendlist)) ///sending to only non participants
                                        $automail->sendMailEpMailBox($contributor['identifier'],$object,$message);
                                }
                            }
                        }
                    }
                    else
                    {
                        $delviews = $delivery_obj->getDeliveryDetails($ao_id);
                        $profiles = explode(",", $delviews[0]['view_to']);

                        $profiles = implode(",", $profiles);
                        $contributors=$delivery_obj->getContributorsAO('public',$aoDetails[0]['fav_category'], $profiles);
                        if(is_array($contributors) && count($contributors)>0)
                        {
                            $jclimit=$this->configval['jc_limit'];
                            $sclimit=$this->configval['sc_limit'];

                            foreach($contributors as $contributor)
                            {
                                if($contributor['profile_type'] == 'junior' || $contributor['profile_type'] == 'sub-junior')
                                {
                                    $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);
                                    if($jclimit > $countofparts[0]['partscount'])
                                    {
                                        if(!in_array($contributor['identifier'],$nomailsendlist)) ///sending to only non participants
                                            $automail->sendMailEpMailBox($contributor['identifier'],$object,$message);
                                    }
                                }
                                else
                                {
                                    $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);

                                    if($sclimit > $countofparts[0]['partscount'])
                                    {
                                        if(!in_array($contributor['identifier'],$nomailsendlist)) ///sending to only non participants
                                            $automail->sendMailEpMailBox($contributor['identifier'],$object,$message);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    ///sending mails to correctors and writers when article is republished in corrector aspect////////
    function sendMailToCorrectors($art_id)
    {
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new EP_Delivery_Article();
        $user_obj = new Ep_User_User();
        $autoEmails = new Ep_Message_AutoEmails();
        $participate_obj = new Ep_Participation_CorrectorParticipation();
        $part_obj = new EP_Participation_Participation();
        $ao_id = $delivery_obj->getDeliveryID($art_id);
        $getpartcrts = $participate_obj->getCrtParticipationsUserIdsAll($art_id);
        $getpartwrts = $part_obj->getNotRefusedParticipationsUserIds($art_id);
        foreach($getpartcrts as $notsendmail)
        {
            $nomailsendlist[] = $notsendmail['corrector_id'];
        }
        foreach($getpartwrts as $notsendmailwrts)
        {
            $nomailsendlist[] = $notsendmailwrts['user_id'];
        }
        /*  Sending mail to client when publish **/
        $delartdetails = $delivery_obj->getArticlesOfDel($ao_id);
        $expires=time()+(60*$delartdetails[0]['correction_participation']);
        $aoDetails=$delivery_obj->getPrAoDetailsWithArtid($art_id);
        $autoEmails=new Ep_Message_AutoEmails();
        $parameters['AO_title']=$aoDetails[0]['title'];
        $parameters['article_title'] = $aoDetails[0]['artname'];
        $parameters['AO_end_date']=$aoDetails[0]['delivery_date'];
        //$parameters['submitdate_bo']=$aoDetails[0]['submitdate_bo'];
        $parameters['submitdate_bo']=date('d/m/Y', $expires)." &agrave; ".date('H:i', $expires);

        $parameters['noofarts']=$aoDetails[0]['noofarts'];
        if($aoDetails[0]['deli_anonymous']=='0')
            $parameters['article_link']="/contrib/aosearch?client_contact=".$aoDetails[0]['user_id'];
        else
            $parameters['article_link']="/contrib/aosearch?client_contact=anonymous";
        $parameters['aoname_link'] = "/contrib/aosearch";
        $parameters['clientartname_link'] = "/client/quotes?id=".$aoDetails[0]['articleid'];

        ////if the mission is for test then///
        if($aoDetails[0]['missiontest'] == 'yes')
            $mailId = 104;
        else
            $mailId = 21;

        if($aoDetails[0]['corrector_privatelist']!= NULL)
        {
            $correctors=array_unique(explode(",",$aoDetails[0]['corrector_privatelist']));
            if(is_array($correctors) && count($correctors)>0)
            {
                foreach($correctors as $corrector)
                {
                    if(!in_array($corrector,$nomailsendlist)) ///sending to only non participants
                        $autoEmails->messageToEPMail($corrector,$mailId,$parameters);
                }
            }
        }
        elseif($aoDetails[0]['corrector_privatelist']== NULL)
        {
            $delviews = $delivery_obj->getDeliveryDetails($ao_id);
            $profiles = explode(",", $delviews[0]['corrector_list']);
            $profiles = implode(",", $profiles);
            // $contributors=$delivery_obj->getContributorsAO($profiles);
            /*added by naseer on 03.12.2015*/
            // check if the delivery is writer or translation//
            if($delviews[0]['product'] === 'translation'){
                //call this new function if the product is translation//
                $correctorswriters=$delivery_obj->getCorrectorsWritersAOTranslation($profiles, $aoDetails[0]['artLanguage']);
                $articleDetails = $article_obj->getArticleDetails($art_id);
                if($articleDetails[0]['sourcelang_nocheck_correction'] != 'yes'){
                    $ao=0;
                    $i = 0;
                    foreach($correctorswriters as $correctorswriter) {
                        $flag = true;//default value at begining
                        //checking if source laug is compulsury to check, if source laung is compulsary to check then unserialize launge_more and check if the laung fluence is above 50%//
                            $language_more = unserialize($correctorswriter['language_more']);
                            $flag = fasle;
                            foreach ($language_more as $key => $value) {
                                if ($key === $articleDetails[0]['language_source'] && (int)$value >= 50) {
                                    $flag = true;//false true if the source launge is in lang more of contributor
                                }
                            }
                        if($flag === true) {
                            $correctorswriters_temp[$i++] = $correctorswriters[$ao];// initailizing recent offerts to temp variable//
                        }
                        $ao++;
                    }
                    $correctorswriters = $correctorswriters_temp;//inititailze temp array to original array after filtering the array//
                }
            }
            else{
                // if procut is redarection then call the old function //
                $correctorswriters=$delivery_obj->getCorrectorsWritersAO($profiles, $aoDetails[0]['artLanguage']);
            }
            //echo "<pre>";print_r($correctorswriters); exit;
            if(is_array($correctorswriters) && count($correctorswriters)>0)
            {
                foreach($correctorswriters as $correctorswriter)
                {
                    if(!in_array($correctorswriter['identifier'],$nomailsendlist)) ///sending to only non participants
                    {
                        $autoEmails->messageToEPMail($correctorswriter['identifier'],$mailId,$parameters);///
                        $arrayuser[] = $correctorswriter['identifier'];
                    }

                }

            }
        }
    }
    ///sending mails to correctors and writers when article is republished in corrector aspect////////
    function sendMailToRepublishedCorrectors($art_id, $params)
    {
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new EP_Delivery_Article();
        $user_obj = new Ep_User_User();
        $autoEmails = new Ep_Message_AutoEmails();
        $participate_obj = new Ep_Participation_CorrectorParticipation();
        $part_obj = new EP_Participation_Participation();
        $ao_id = $delivery_obj->getDeliveryID($art_id);
        $getpartcrts = $participate_obj->getCrtParticipationsUserIdsnotcycle0($art_id);
        $getpartwrts = $part_obj->getNotRefusedParticipationsUserIds($art_id);
        foreach($getpartcrts as $notsendmail)
        {
            $nomailsendlist[] = $notsendmail['corrector_id'];
        }
        foreach($getpartwrts as $notsendmailwrts)
        {
            $nomailsendlist[] = $notsendmailwrts['user_id'];
        }
        /*  Sending mail to client when publish **/
        $delartdetails = $delivery_obj->getArticlesOfDel($ao_id);
        $expires=time()+(60*$delartdetails[0]['correction_participation']);
        $aoDetails=$delivery_obj->getPrAoDetailsWithArtid($art_id);
        $autoEmails=new Ep_Message_AutoEmails();
        $parameters['AO_title']=$aoDetails[0]['title'];
        $parameters['article_title'] = $aoDetails[0]['artname'];
        $parameters['AO_end_date']=$aoDetails[0]['delivery_date'];
        //$parameters['submitdate_bo']=$aoDetails[0]['submitdate_bo'];
        $parameters['submitdate_bo']=date('d/m/Y', $expires)." &agrave; ".date('H:i', $expires);

        $parameters['noofarts']=$aoDetails[0]['noofarts'];
        if($aoDetails[0]['deli_anonymous']=='0')
            $parameters['article_link']="/contrib/aosearch?client_contact=".$aoDetails[0]['user_id'];
        else
            $parameters['article_link']="/contrib/aosearch?client_contact=anonymous";
        $parameters['aoname_link'] = "/contrib/aosearch";
        $parameters['clientartname_link'] = "/client/quotes?id=".$aoDetails[0]['articleid'];

        ////if the mission is for test then///
        if($aoDetails[0]['missiontest'] == 'yes')
            $mailId = 104;
        else
            $mailId = 21;

        if($aoDetails[0]['corrector_privatelist']!= NULL)
        {
            $correctors=array_unique(explode(",",$aoDetails[0]['corrector_privatelist']));
            if(is_array($correctors) && count($correctors)>0)
            {
                foreach($correctors as $corrector)
                {
                    if(!in_array($corrector,$nomailsendlist)) ///sending to only non participants
                        $autoEmails->sendMailEpMailBoxPM($corrector,$params['republishsubject'],$params['republishmail'],$params['sendfrom']);

                    //$autoEmails->messageToEPMail($corrector,$mailId,$parameters);
                }
            }
        }
        elseif($aoDetails[0]['corrector_privatelist']== NULL)
        {
            $delviews = $delivery_obj->getDeliveryDetails($ao_id);
            $profiles = explode(",", $delviews[0]['corrector_list']);
            $profiles = implode(",", $profiles);
            // $contributors=$delivery_obj->getContributorsAO($profiles);
            if($params['crtselectedlang']!='null')
			{
                //$crtselectedlang = explode(",",$params['crtselectedlang']);  ///when languages is selected from publish popup///
				if(is_array($params['crtselectedlang']))
				$crtselectedlang = implode(",", $params['crtselectedlang']);
				else
				$crtselectedlang = 	$params['crtselectedlang'];
			}
            else
                $crtselectedlang = $aoDetails[0]['artLanguage'];
			//print_r($params);
            $correctorswriters=$delivery_obj->getCorrectorsWritersAO($profiles, $crtselectedlang);
            if(is_array($correctorswriters) && count($correctorswriters)>0)
            {
                foreach($correctorswriters as $correctorswriter)
                {
                    if(!in_array($correctorswriter['identifier'],$nomailsendlist)) ///sending to only non participants
                    {
                        //$autoEmails->messageToEPMail($correctorswriter['identifier'],$mailId,$parameters);///
                        $autoEmails->sendMailEpMailBoxPM($correctorswriter['identifier'],$params['republishsubject'],$params['republishmail'],$params['sendfrom']);
                        $arrayuser[] = $correctorswriter['identifier'];
                    }

                }

            }
            // echo "<pre>".print_r($arrayuser); print_r($nomailsendlist); exit;
        }
    }
    ////////function for unlock system//////////
    public function unlockonactionAction($artId)
    {
        $lock_obj = new Ep_User_LockSystem();
        $user_id=$this->adminLogin->userId ;
        $data = array("lock_status"=>"no");////////updating
        $query = "article_id= '".$artId."' AND user_id='".$user_id."'";
        /////udate status participation table///////
        $lock_obj->updateLockSystem($data,$query);

    }
    ////////check whether lock is exist for not//////////
    public function checklockAction($artId)
    {
        $lock_obj = new Ep_User_LockSystem();
        $user_id=$this->adminLogin->userId ;
        $lock = $lock_obj->lockExistByUser($artId, $user_id);
        return $lock[0]['lock_status'];
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
            $expl[1] = 00;
        }
        $M = $expl[1];
        if (strlen($M) < 2) {
            $M = $M . 0;
        }
        $hours = $H . ":" . $M;
        return $hours;
    }
    ////////function for republish//////////
    public function republish($artId)
    {
        $participate_obj = new EP_Participation_Participation();
        ////update the artlcle table with partcipation time/////////
        $this->WriterParticipationExpire($artId);
        ///////check the cycle count in participation tabel and increament//////////
        $cycleCount = $participate_obj->getParticipationCycles($artId);
        $cycleCount1 = $cycleCount[0]['cycle']+1;
        /////udate status participation table with article id///////
        $data = array("cycle"=>$cycleCount1, "selection_type"=>NULL);////////updating
        $query = "article_id= '".$artId."' and cycle=0";
        $participate_obj->updateParticipation($data,$query);
    }
    ////////function for republish//////////
    public function correctorRepublish($artId)
    {
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        ////update the artlcle table with partcipation time/////////
        $this->CorrectorParticipationExpire($artId);
        ///////check the cycle count in participation tabel and increament//////////
        $cycleCount = $crtparticipate_obj->getCrtParticipationCycles($artId);
        $cycleCount1 = $cycleCount[0]['cycle']+1;
        /////udate status participation table with article id///////
        $data = array("cycle"=>$cycleCount1, "selection_type"=>NULL);////////updating
        $query = "article_id= '".$artId."' and cycle=0";
        $crtparticipate_obj->updateCrtParticipation($data,$query);
    }
    public function modifychar($name)
    {
        $name = str_replace("ï¿½","oe",$name);
        $name = str_replace("ï¿½","Oe",$name);
        $name = str_replace("â‚¬","&euro;",$name);
        return $name;
    }
    ////store all the history for articles//////
    public function articleshistory($artId, $stage, $action,$reason=NULL, $actionsentence=NULL)
    {
        $arthistory_obj = new Ep_Delivery_ArticleHistory();
        $userId = $this->adminLogin->userId;
        $arthistory_obj->article_id=$artId;
        $arthistory_obj->user_id=$userId;
        $arthistory_obj->stage=$stage;
        $arthistory_obj->action=$action;
        $arthistory_obj->action_at=date("Y-m-d h:i:s");
        $arthistory_obj->reasons=$reasons;
        $arthistory_obj->action_sentence=$actionsentence;
        $arthistory_obj->insert();

    }
    ////article history ////
    public function articleHistory($actionId, $actparams)
    {  // echo $actionId;  print_r($actparams); exit;
        //////////passed variables///////////////////
        $stage = $actparams['stage'];
        $action = $actparams['action'];
        $participation_count = $actparams['participation_count'];
        //////////////////////////////////////////////////
        $article_obj = new EP_Delivery_Article();
        $ao_obj = new Ep_Delivery_Delivery();
        $participate_obj = new EP_Participation_Participation();
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $user_obj = new Ep_User_User();
        $history_obj = new Ep_Delivery_ArticleHistory();
        $action_obj = new EP_Delivery_ArticleActions();
        $articleId = $actparams['artId'];
        ////common variables///////////
        if($actparams['aoId']) ///////with delivery id
        {  //echo "jim"; exit;
            $deldetials = $ao_obj->getPrAoDetails($actparams['aoId']);
            $article_name = '<b>'.$deldetials[0]['artname'].'</b>';
            $AO_name = '<b>'.$deldetials[0]['aoName'].'</b>';
            $client_name = '<b>'.$deldetials[0]['company_name'].'</b>';
            $articleId = $deldetials[0]['articleid'];
            $mail_ticket_id = '<a data-target="#refusal_reasons" data-toggle="modal" href="/ongoing/get-mail-content?ticket_id='.$actparams['mail_ticket_id'].'"><b>Voir Plus</b></a>';
        }
        if($actparams['artId']) ///////with article id
        {
            $deldetials = $ao_obj->getPrAoDetailsWithArtid($actparams['artId']);
            $article_name = '<b>'.$deldetials[0]['artname'].'</b>';
            $article_downloadlink = '<a href="/proofread/downloadfile?submenuId=ML3-SL2&path='.$actparams['article_download'].'"><b>'.$deldetials[0]['artname'].'</b></a>';
            $AO_name = '<b>'.$deldetials[0]['title'].'</b>';
            $client_name = '<b>'.$deldetials[0]['company_name'].'</b>';
            $articleId = $deldetials[0]['articleid'];
            $refusal_reasons = $actparams['refusereasontitles'];
            $refusal_reasonslink = '<a data-target="#refusal_reasons" data-toggle="modal" href="/ongoing/get-refusal-reason?reason_id='.$actparams['refusereason'].'"><b>Voir Plus</b></a>';
            $mail_ticket_id = '<a data-target="#refusal_reasons" data-toggle="modal" href="/ongoing/get-mail-content?ticket_id='.$actparams['mail_ticket_id'].'"><b>Voir Plus</b></a>';
        }
        if($actparams['contributorId']) ///////get contributor name with userid
        {
            $userdetials = $user_obj->getAllUsersDetails($actparams['contributorId']);
            $contributor_name = '<a class="writer" href="/user/contributor-edit?submenuId=ML10-SL1&tab=viewcontrib&userId='.$actparams['contributorId'].'"><b>'.$userdetials[0]['first_name'].' '.$userdetials[0]['last_name'].'</b></a>';
        }
        if($actparams['correctorId']) ///////get corrector name with userid
        {
            $userdetials = $user_obj->getAllUsersDetails($actparams['correctorId']);
            $corrector_name = '<a class=""corrector" href="/user/contributor-edit?submenuId=ML10-SL1&tab=viewcontrib&userId='.$actparams['correctorId'].'"><b>'.$userdetials[0]['first_name'].' '.$userdetials[0]['last_name'].'</b></a>';
        }
        $project_manager_name = '<b>'.$this->adminLogin->loginName.'</b>';
        $old_writer_price = $actparams['old_writer_price'];
        $new_writer_price = $actparams['new_writer_price'];
		$old_corrector_price = $actparams['old_corrector_price'];
        $new_corrector_price = $actparams['new_corrector_price'];
        $old_article_writing_price_range = $actparams['old_article_writing_price_range'];
        $new_article_writing_price_range = $actparams['new_article_writing_price_range'];
        $old_article_correction_price_range = $actparams['old_article_correction_price_range'];
        $new_article_correction_price_range = $actparams['new_article_correction_price_range'];
        $old_correction = $actparams['old_correction'];
        $new_correction = $actparams['new_correction'];
        $currency = '&euro;';
       
        $sentence=$action_obj->getActionSentence($actionId);
        $actionmessage=strip_tags($sentence[0]['Message']);
        eval("\$actionmessage= \"$actionmessage\";");

        ////inserting detials into articlehistory table///////////
        $history=array();
        $history['user_id']=$this->adminLogin->userId;
        $history['article_id']=$articleId;
        $history['stage']=$stage;
        $history['action']=$action;        
        $history['action_sentence']=$actionmessage;
        $history_obj->insertHistory($history);
		unset($history_obj);

    }
    public function unserialisearray($value)
    {
        $catorlag = unserialize($value);
        $i=0;
        if($catorlag != '')
        {
            foreach($catorlag as $key => $value)
            {
                $res[$i]=$key."(".$value.")";
                $i++;
            }
            if($res != '')
                return implode(",",$res);
        }
    }
    ////////////////inserting a new record into article process tabel when any action done in correction stages ////////////////////////
    public function insertStageRecord($partId,$version,$stage,$status)
    {
        $artProcess_obj = new EP_Delivery_ArticleProcess();
        $recentDetials = $artProcess_obj->getVersionDetailsByVersion($partId, $version);
        $artProcess_obj->participate_id=$partId ;
        $artProcess_obj->user_id=$this->adminLogin->userId;
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



}
