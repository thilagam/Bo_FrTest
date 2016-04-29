<?php
/**
 * EditPlaceController
 * This controller is responsible for Poll Operations*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
 
Class PollController Extends Ep_Controller_Action
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
         if(($profile=$profileplus_obj->checkProfileExist($contrib_identifier))!='NO' && $contrib_identifier!='' )
         {
               $this->_view->client_email=ucfirst($profile[0]['first_name']);
         }
        else if($this->EP_Contrib_reg->clientemail!='')
		    $this->_view->client_email=strtolower($this->EP_Contrib_reg->clientemail);

         if($contrib_identifier!='')
         {
            $app_path=APP_PATH_ROOT;
            $profiledir=$this->_config->path->contrib_profile_pic_path.$contrib_identifier.'/';
             $pic=$contrib_identifier."_h.jpg";

             if(file_exists($app_path.$profiledir.$pic))
             {
                 $this->_view->contrib_home_picture="/FO/".$profiledir.$pic;
             }
             else
             {
                $this->_view->contrib_home_picture="/FO/images/Contrib/profile-img-def.png";
             }

         }
        /**Loading Configuration Settings**/
        $config_obj=new Ep_User_Configuration();
        $config=$config_obj->getAllConfigurations();
        $this->config=$config;
        /**getting Profile whether senior / junior**/
            $user_obj = new EP_Contrib_Registration();
            $profile_contribinfo=$user_obj->getUserInfo($contrib_identifier);
            $this->profileType=$profile_contribinfo[0]['profile_type'];
            $this->_view->profileType=$this->profileType;
	}
    public function participationAction()
    {

      if($this->_helper->EpCustom->checksession())
      {

          if($this->_helper->FlashMessenger->getMessages()) {
                $this->_view->actionmessages=$this->_helper->FlashMessenger->getMessages();
            }

          $poll_obj=new Ep_Poll_Participation();

        $poll_search_params['profile_type']=$this->profileType;
        $pollDetails=$poll_obj->getAllPollAODetails($poll_search_params);

            $pollCount=count($pollDetails);

            if($pollCount>0)
            {
                  $cnt=0;

                  foreach($pollDetails as $details)
                  {
                     $pollDetails[$cnt]['client_pic']= $this->getClientPicPath($details['client']);
                     $pollDetails[$cnt]['category']= $this->getCategoryName($details['category']);
                     $pollDetails[$cnt]['type']= $this->getArticleTypeName($details['type']);
                     $pollDetails[$cnt]['language']= $this->getLanguageName($details['language']);
                     $pollDetails[$cnt]['delivery_date']= date('d/m/Y',strtotime($details['delivery_date']));
                     $pollDetails[$cnt]['DaysDifference']= $this->getDaysDiff($details['delivery_date']);
                     if($details['participation_expires'])
                        $pollDetails[$cnt]['timestamp']= $details['participation_expires'];
                     else
                        $pollDetails[$cnt]['timestamp']= strtotime(date('Y-m-d H:i:s',strtotime($details['delivery_date'])));
                     //$pollDetails[$cnt]['participants']= $participants->getParticipantCount($details['articleid']);

                     $checkParticipate=$poll_obj->checkPollParticipation($details['pollId'],$this->EP_Contrib_reg->clientidentifier,$this->profileType);
                      if($checkParticipate=='NO')
                          $pollDetails[$cnt]['action']='new';
                      else
                          $pollDetails[$cnt]['action']='update';

                      
                     $cnt++;

                  }

                /**sorting array for displaying trod articles in last**/
                  foreach ($pollDetails as $key => $row) {
                      $polls_array[$key]  = $row[0];
                      // of course, replace 0 with whatever is the date field's index
                  }

                  array_multisort($polls_array, SORT_DESC, $pollDetails);

                 // echo "<pre>";print_r($pollDetails);echo"</pre>";

                $this->_view->searchCount= $pollCount;

                /**search Pagination**/
                $page = $this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($pollDetails);
                $paginator->setItemCountPerPage($this->config['pagination_fo']);
                $paginator->setCurrentPageNumber($page);
                //$this->_view->pagination=print_r($paginator->getPages(),true);
                $patterns='/[? &]page=[0-9]{1,2}/';
                $replace="";


                $this->_view->contributor_identifier=  $this->EP_Contrib_reg->clientidentifier;
                $this->_view->polls = $paginator;
                $this->_view->pages = $paginator->getPages();
                $this->_view->pageURL=preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
                //echo preg_replace($patterns, $replace,$_SERVER['REQUEST_URI']);
				$this->_view->now=date("Y-m-d H:i:s");

            }


        $this->_view->meta_title="Contributor-Poll Participation";

		$this->render("EP_Contrib_Poll_Partcipation");
      }
    }
    /**Displaying Poll spec Pop up**/
    public function pollspecAction()
    {
        $polls_obj=new Ep_Poll_Participation();
        $pollParams=$this->_request->getParams();
        if($pollParams['pollid']!=NULL)
        {
            $pollIdentifier=$pollParams['pollid'];
            $pollDetails=$polls_obj->getPollBrief($pollIdentifier);

            $full_path=POLL_SPEC_FILE_PATH."/".$pollDetails[0]['file_name'];
            if(file_exists($full_path) && $pollDetails[0]['file_name']!=NULL && !is_dir($full_path))
            {

                 $this->_view->docexists="YES";
            }

            $this->_view->articleBrief=$pollDetails;
            $this->_view->type='polls';
            $this->render("EP_Contrib_PopUp");
        }
        /**Download Article Brief doc**/
        else if($pollParams['attachment']!=NULL)
        {
            $pollIdentifier=$pollParams['attachment'];
            $pollDetails=$polls_obj->getPollBrief($pollIdentifier);
            $full_path=POLL_SPEC_FILE_PATH."/".$pollDetails[0]['file_name'];
            //echo $articleDetails[0]['filepath'];exit;
            if(file_exists($full_path) && $pollDetails[0]['file_name']!=NULL && !is_dir($full_path))
            {


                $attachment=new Ep_Ticket_Attachment();
                $attachment->downloadAttachment($full_path,"attachment");
            }
            else
            {
                echo "File Not Found";
            }
        }

    }
    /**POLL participation pop up*/
    public function pollPopupAction()
    {
        $poll_obj=new Ep_Poll_Participation();

        $poll_Params=$this->_request->getParams();
        $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
        $poll_identifier=$poll_Params['pollid'];
        $poll_brief=$poll_obj->getPollBrief($poll_identifier);
			
        if($poll_obj->checkPollParticipation($poll_identifier,$contrib_identifier,$this->profileType)!="NO")
        {
            $poll_details=$poll_obj->checkPollParticipation($poll_identifier,$contrib_identifier,$this->profileType);
            $this->_view->poll_user_week=$poll_details[0]['per_week'];
            $this->_view->poll_user_price=$poll_details[0]['price_user'];
            $this->_view->poll_user_comments=$poll_details[0]['comments'];
            $this->_view->poll_participation_id=$poll_details[0]['id'];
			$this->_view->poll_possible_hours=$poll_details[0]['possible_hours'];
			if($poll_details[0]['availability']!="")
				$this->_view->poll_availability=$poll_details[0]['availability'];
			$this->_view->slot_from=$poll_details[0]['slot_from'];
			$this->_view->slot_to=$poll_details[0]['slot_to'];
			$this->_view->slot_ampm=$poll_details[0]['slot_ampm'];
			$this->_view->slot_confirm=$poll_details[0]['slot_confirm'];
        }
		else
			$this->_view->slot_confirm="no";
        
			$percent=$poll_brief[0]['contrib_percentage']/100;
			$this->_view->price_min=($percent) * ($poll_brief[0]['price_min']);
			$this->_view->price_max=($percent) * ($poll_brief[0]['price_max']);
			$this->_view->show_slot=$poll_brief[0]['show_slot'];
			$this->_view->show_range=$poll_brief[0]['noprice'];
		
		if(!$poll_brief[0]['poll_duration'])
             $poll_brief[0]['poll_duration']=24;
        $this->_view->poll_duration=$poll_brief[0]['poll_duration'];
        $this->_view->poll_id=$poll_identifier;
        $this->render("Ep_Popup_Poll");
    }
    
	/**POLL participation Upadtion*/
    public function updatePollAction()
    {
        if($this->_request-> isPost())
        {
            $poll_Params=$this->_request->getParams();
            $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;
            $poll_identifier=$poll_Params['poll_id'];
			//print_r($poll_Params);exit;
            if($poll_identifier!='' && $contrib_identifier!='')
            {
                $poll_obj=new Ep_Poll_Participation();

                 if($poll_obj->checkPollParticipation($poll_identifier,$contrib_identifier,$this->profileType)!="NO")
                 {
                     $poll_details=$poll_obj->checkPollParticipation($poll_identifier,$contrib_identifier,$this->profileType);

                     $poll_data['poll_id']=$poll_identifier;
                     $poll_data['user_id']=$contrib_identifier;
						$poll_Params['price']=str_replace(",",".",$poll_Params['price']);
                     $poll_data['price_user']=(float)$poll_Params['price'];
                     $poll_data['comments']=$this->utf8dec($poll_Params['comments']);
                     $poll_data['per_week']=(int)$poll_Params['per_week'];
                     $poll_data['updated_at']=date("Y-m-d H:i:s");
                     $poll_data['status']="active";
                     $poll_data['possible_hours']=$poll_Params['possible_hours'];
                     $poll_data['availability']=date("Y-m-d H:i:s",strtotime($poll_Params['availability']));
                     $poll_data['slot_from']=$poll_Params['slot_from'];
                     $poll_data['slot_to']=$poll_Params['slot_to'];
                     
                     if($poll_Params['slot_from']!="" || $poll_Params['slot_to']!="")
					 {
						$poll_data['slot_ampm']=$poll_Params['slot_ampm'];
						$poll_data['slot_confirm']=$poll_Params['slot_confirm'];
					 }	
                     $query = "id= '".$poll_details[0]['id']."'";

                     $poll_obj->updatePollParticipation($poll_data,$query);

                     $this->_helper->FlashMessenger('Sondage enregistr&eacute;.');
                     $this->_redirect("/poll/participation");
                 }
                 else
                 {

                     $poll_obj->poll_id=$poll_identifier;
                     $poll_obj->user_id=$contrib_identifier;
						$poll_Params['price']=str_replace(",",".",$poll_Params['price']);
					 $poll_obj->price_user=(float)$poll_Params['price'];
                     $poll_obj->comments=$this->utf8dec($poll_Params['comments']);
                     $poll_obj->per_week=(int)$poll_Params['per_week'];
                     $poll_obj->status="active";
					 $poll_obj->possible_hours=$poll_Params['possible_hours'];	
					 $poll_obj->availability=date("Y-m-d H:i:s",strtotime($poll_Params['availability']));
					 //$poll_obj->availability=$poll_Params['availability'];
					 $poll_obj->slot_from=$poll_Params['slot_from'];
                     $poll_obj->slot_to=$poll_Params['slot_to'];
                     
					 if($poll_Params['slot_from']!="" || $poll_Params['slot_to']!="")
					 {
						$poll_obj->slot_ampm=$poll_Params['slot_ampm'];
						$poll_obj->slot_confirm=$poll_Params['slot_confirm'];
					 }
                     try
                        {
                            // echo "<pre>".print_r($profileContrib_obj)."</pre>";
                             //echo "<br><pre>".print_r($profileplus_obj)."</pre>";
                            // exit;
                            $poll_obj->insert();
                            $this->_helper->FlashMessenger('Sondage enregistr&eacute;.');
                            $this->_redirect("/poll/participation");
                            exit;
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
            else
            {
                $this->_redirect("/poll/participation");
            }
            
        }
        else
            $this->_redirect("/poll/participation");
    }
  /**UTF8 DECODE function work for msword character also**/
    public function utf8dec($s_String)
    {
       $s_String=str_replace("é","é",$s_String);
	   $s_String=str_replace("É","É",$s_String);

        $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
        return substr($s_String, 0, strlen($s_String)-1);
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
        //echo date('Y-m-d H:i:s');
        $endDate=strtotime(date('Y-m-d H:i:s',strtotime($deliveryDate)));
        //echo date('Y-m-d H:i:s')."--".$startDate."--".$deliveryDate."--".$endDate."<br>";
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
     /*Function to get the picture of a client**/
    public function getClientPicPath($identifer,$action='home')
    {

        $app_path=APP_PATH_ROOT;
        $profiledir=$this->_config->path->client_profile_pic_path.$identifer.'/';
        if($action=='home')
            $pic=$identifer."_ao.jpg";
        else if($action=='Reg')
            $pic=$identifer."_h.jpg";
        else
            $pic=$identifer."_p.jpg";

        if(file_exists($app_path.$profiledir.$pic))
        {
            $pic_path="/FO/".$profiledir.$pic;
        }
        else
        {
            $pic_path="/FO/images/images-c/profile-img-def.png";
        }
        return $pic_path;
    }
      ////////////display pop up with detail of multiple contributors who made biding when the participant number link clicked///////////////////
    public function showgroupprofilesAction()
    {
        $contrib_obj=new EP_Contrib_ProfileContributor();
        $participate_obj=new Ep_Poll_Participation();
        $partParams=$this->_request->getParams();
        $ep_lang_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
		$categories_array=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
		$prof=$this->_arrayDb->loadArrayv2("CONTRIB_PROFESSION", $this->_lang);
        if($partParams['pollId']!=NULL)
        {

             $pollId=$partParams['pollId'];
             $participants=$participate_obj->getGroupParticipants($pollId);
             for($i=0; $i<count($participants);$i++)
             {
                 $contribDetails[$i]=$contrib_obj->getGroupProfilesPollInfo($participants[$i]['user_id'], $participants[$i]['id']);
                 $cnt=0;
                 if($contribDetails[$i]!='NO')
                 {
                     foreach($contribDetails[$i] as $details)
                     {
                         if(!$details['first_name'])
                         {
                             $val['email']=explode("@",$details['email']);
                             $contribDetails[$i][$cnt]['first_name']=$val['email'][0];

                         }

                         $contribDetails[$i][$cnt]['profession']= $prof[$details['profession']];
                         $contribDetails[$i][$cnt]['language']= $ep_lang_array[$details['language']];
                         $cat_list=explode(',',$details['favourite_category']) ;
                            foreach ($cat_list as $key1 => $value1)
                            {
                                $cat_list[$key1]=$categories_array[$value1];
                            }
                            $contribDetails[$i][$cnt]['favourite_category']=implode(',',$cat_list);

                         /*Assign profile picture**/
                        $app_path=APP_PATH_ROOT;
                        $profiledir=$this->_config->path->contrib_profile_pic_path.$details['identifier'].'/';
                         $pic=$details['identifier']."_p.jpg";

                         if(file_exists($app_path.$profiledir.$pic))
                         {
                             $contribDetails[$i][$cnt]['profile_picture']="/FO/".$profiledir.$pic;
                         }
                         else
                         {
                            $contribDetails[$i][$cnt]['profile_picture']="/FO/images/Contrib/prof-img-def.png";
                         }


                        $cnt++;
                     }
                 }
             }
            //print_r($contribDetails);exit;

            $this->_view->contribDetails =  $contribDetails;
            $this->_view->render("Ep_Poll_Group_participate_info");
          }

    }
}