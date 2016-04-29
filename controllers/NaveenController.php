<?php

class NaveenController extends Ep_Controller_Action
{

	  public function init(){
		parent::init();
		Zend_Loader::loadClass('Ep_Document_DocTrack');
		$this->session =  new Zend_Session_Namespace('users');
		$this->_view->lang = $this->_lang;
		$this->adminLogin	= Zend_Registry::get ( 'adminLogin' );
		$this->session = $this->adminLogin;
		$this->_view->loginName = $this->adminLogin->loginName;
        ////if session expires/////
        if($this->adminLogin->loginName == '' && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
            echo "session expired...please <a href='/index'>click here</a> to login"; exit;
        }	
		$this->_view->fo_path=$this->fo_path=$this->_config->path->fo_path;
		$this->_view->fo_root_path=$this->fo_root_path=$this->_config->path->fo_root_path;
		
			
	  }
   public function userListAction()
    {
	    $userlist_obj= new Ep_Naveen_Userlist();
		
		$userlist_data= $userlist_obj->getUserList();
		if(!empty($userlist_data)){
		$this->_view->datalisted = $userlist_data;
		}
		
		$bouserlist_data= $userlist_obj->getBoUserList();
		if(!empty($bouserlist_data)){
		$this->_view->bouserlist = $bouserlist_data;
		}
		
		
		$edituserlist_data= $userlist_obj->getEditUserList();
		if(!empty($edituserlist_data)){
		$this->_view->edituserlist = $edituserlist_data;
		}
		$this->_view->render('userlist');
    }
	
	public function quotesListAction(){
	
		$quoteslist_obj= new Ep_Naveen_NaveenQuoteList();
		$quoteslist_data= $quoteslist_obj->getQuotesList();
		if(!empty($quoteslist_data)){
		$this->_view->quoteslist = $quoteslist_data;
		
		
		$this->_view->render('naveenquotelist');
		}
	
	}
	public function clientUsersAction(){
	
	$userId=$this->_request->getParam('userId');
	
	$userlist_obj= new Ep_Naveen_Userlist();
	$userclient_data=$userlist_obj->getsingleclient($userId);
	$categories=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang) ;
			$language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
			$nationality_array=$this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
			$pays_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);
    if(array_key_exists($userclient_data[0]['country'],$nationality_array)){
	 
	 $country=$nationality_array[$userclient_data[0]['country']];
	 }
	 
				 $wesite_array=explode('|',$userclient_data[0]['website']);
				 $wesitename_array=explode('|',$userclient_data[0]['website_names']);
				 $webcombine=array_combine($wesite_array,$wesitename_array);
				 foreach($webcombine as $key=>$valweb){
				 $websitename .="<a href='".$key."'>".$key."</a> ".$valweb."<br>";
				 }
	
	echo '<div class="row-fluid">
             <div class="span12">
                           <table class="table table-bordered table-striped table_vam">
						   <tr><td><strong>Full Name</strong>:</td>
						   <td><span class="span8"><h3>'.$userclient_data[0]['first_name'].' '.$userclient_data[0]['last_name'].'</h3></span><span class="span4"><img class="img-circle" src="http://ep-test.edit-place.com/FO/profiles/clients/logos/'.$userclient_data[0]['identifier'].'/'.$userclient_data[0]['identifier'].'_global.png" />
						   </span></td></tr>
						   <tr><td> <strong>Address</strong>:</td><td><p>'.$userclient_data[0]['address'].'<p></td></tr>
						      <tr>   <td> <strong>City</strong>:</td><td>  <p>'.$userclient_data[0]['city'].'<p></td></tr>
						      <tr>   <td> <strong>State</strong>:</td><td>  <p>'.$userclient_data[0]['state'].'<p></td></tr>
						      <tr>   <td> <strong>Country</strong>:</td><td>  <p>'.$country.'<p></td></tr>
						      <tr>   <td> <strong>ZipCode</strong>:</td><td>  <p>'.$userclient_data[0]['zipcode'].'<p></td></tr>
						      <tr>   <td> <strong>TelePhone</strong>:</td><td>  <p>'.$userclient_data[0]['contact_phone'].'<p></td></tr>
							  
							  
						    <tr> <td> <strong>Website</strong>:</td><td>  <p>'.$websitename.'</span><p></td> </tr>
							<tr> <td> <strong>Client Code</strong>:</td><td>  <p>'.$userclient_data[0]['client_code'].'<p></td> </tr>
						 
						   </table>
					           </div>
                                </div>';
	
	exit();
	
	}
	
	public function contributorDetailsAction() {
	
	$userId=$this->_request->getParam('userId');
	
	$userlist_obj= new Ep_Naveen_Userlist();
	$usercon_data=$userlist_obj->getsinglecontributor($userId);
	
	$categories=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang) ;
			$language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
			$nationality_array=$this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
			$pays_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);
	
	 if(array_key_exists($usercon_data[0]['language'],$language_array)){
	 
	 $defaultLang=$language_array[$usercon_data[0]['language']];
	 }
	
	 if(array_key_exists($usercon_data[0]['nationality'],$nationality_array)){
	 
	 $country=$nationality_array[$usercon_data[0]['nationality']];
	 }
	 
	//Language Know Loop
	 $morelang=unserialize($usercon_data[0]['language_more']);
	 $knownlng="";
			 foreach($morelang as $key=>$val){
			 if(array_key_exists($key,$language_array)) $knownlng .=$language_array[$key].'('.$val.'%)<br>';
			 }
	
	//Categories loop
	
	$categorieser=unserialize($usercon_data[0]['category_more']);
					foreach($categorieser as $key=>$value){
							if(array_key_exists($key,$categories)) $categoriestrpe .= $categories[$key]."(".$value."%)<br>";
					}
				
	echo "<div class='row-fluid'>
             <div class='span12'>
                           <table class='table table-bordered table-striped table_vam'>
						   <tr><td><strong>Full Name</strong>:</td>
						   <td><span class='span8'><h3>".$usercon_data[0]['first_name']." ".$usercon_data[0]['last_name']."</h3></span>
						   <span class='span4'><img class='img-circle' src='http://ep-test.edit-place.com/FO/profiles/contributor/logos/".$usercon_data[0]['identifier']."/".$usercon_data[0]['identifier']."-p.jpg' />
						   </span></td></tr>
						   <tr><td> <strong>Nationality</strong>:</td><td><p>".$country."<p></td></tr>
						   <tr><td> <strong>Favorite Language</strong>:</td><td><p>".$defaultLang."<p></td></tr>
						   <tr><td> <strong>Languages Known</strong>:</td><td><p>".$knownlng."<p></td></tr>
						   
						   <tr><td> <strong>Categories</strong>:</td><td><p>".$categoriestrpe."<p></td></tr>
						   
						   <tr><td> <strong>Address</strong>:</td><td><p>".$usercon_data[0]['address']."<p></td></tr>
						      <tr>   <td> <strong>City</strong>:</td><td>  <p>".$usercon_data[0]['city']."<p></td></tr>
						      <tr>   <td> <strong>State</strong>:</td><td>  <p>".$usercon_data[0]['state']."<p></td></tr>
						      <tr>   <td> <strong>Country</strong>:</td><td>  <p>".$country."<p></td></tr>
						      <tr>   <td> <strong>ZipCode</strong>:</td><td>  <p>".$usercon_data[0]['zipcode']."<p></td></tr>
						      <tr>   <td> <strong>TelePhone</strong>:</td><td>  <p>".$usercon_data[0]['contact_phone']."<p></td></tr>
								 
						   </table>
					           </div>
                                </div>";
	
	exit();
	}
	
	
	public function createUserAction(){
	
	if($this->_request->isPost())
		{
		$createuser_parameters=$this->_request->getParams();
		$userlist_obj= new Ep_Naveen_Userlist();
		
        $postarray["login"] = $this->_request->getPost('first_name');
		$postarray["email"] = $this->_request->getPost('email');
        $postarray["password"] = $this->_request->getPost('password');
		$postarray["created_at"] = $this->_request->getPost('created_at');
		$userlist_obj->user_list_insert($postarray);
		
		}
	 $this->_view->render('userlist');
	
	}
	
	public function naveenSalesFinalValidationAction()
	{
	
	
		$prod_parameters=$this->_request->getParams();

		$quote_id=$prod_parameters['quote_id'];

		$quote_obj=new Ep_Quote_Quotes();		

		if($quote_id)
		{
			//prod details to view in a tab
			$this->_view->prod_view_details=$this->prodViewDetails($quote_id);


			$quoteDetails=$quote_obj->getQuoteDetails($quote_id);
			if($quoteDetails)
			{
				$q=0;
				foreach($quoteDetails as $quote)
				{
					$quoteDetails[$q]['category_name']=trim($this->getCategoryName($quote['category']));
					$quoteDetails[$q]['websites']=explode("|",$quote['websites']);
					
					if($quote['documents_path'])
					{
						$related_files='';
						$documents_path=explode("|",$quote['documents_path']);
						$documents_name=explode("|",$quote['documents_name']);

						foreach($documents_path as $k=>$file)
						{
							if($documents_name[$k])
								$file_name=$documents_name[$k];
							else
								$file_name=basename($file);

							$related_files.='<a href="/quote/download-document?type=quote&index='.$k.'&quote_id='.$quote_id.'">'.$file_name.'</a><br>';
						}

					}

					$quoteDetails[$q]['related_files']=trim($related_files);

					$quoteDetails[$q]['sales_suggested_price_format']=number_format($quote['sales_suggested_price'], 2, ',', ' ');
					$quoteDetails[$q]['comment_time']=time_ago($quote['created_at']);
					

					//get client contact details
					$contact_obj=new Ep_Quote_ClientContacts();
					$contactDetails=$contact_obj->getClientMainContacts($quote['client_id']);
					if($contactDetails!='NO')
					{
						$quoteDetails[$q]['client_contact_name']=$contactDetails[0]['first_name'];
						$quoteDetails[$q]['client_contact_email']=$contactDetails[0]['email'];
						$quoteDetails[$q]['client_contact_phone']=$contactDetails[0]['office_phone'];
						$quoteDetails[$q]['job_title']=$contactDetails[0]['job_title'];
					}


					//bo user details
					$quote_by=$quote['quote_by'];
					$client_obj=new Ep_Quote_Client();
					$bo_user_details=$client_obj->getQuoteUserDetails($quote_by);
					if($bo_user_details!='NO')
					{
						$quoteDetails[$q]['quote_user_name']=$bo_user_details[0]['first_name'].' '.$bo_user_details[0]['last_name'];
						$quoteDetails[$q]['email']=$bo_user_details[0]['email'];
						$quoteDetails[$q]['phone_number']=$bo_user_details[0]['phone_number'];
					}	

					//getting mission details
					$searchParameters['quote_id']=$quote_id;
					$searchParameters['include_final']='yes';

					$quoteMission_obj=new Ep_Quote_QuoteMissions();
					$missonDetails=$quoteMission_obj->getMissionDetails($searchParameters);
					
					$total_unitprice=0;
					$total_turnover=0;
					$total_internalcost=0;
					if($missonDetails)
					{
						$m=0;
						$mission_length_array=array();
						$prior_length_array=array();
						foreach($missonDetails as $mission)
						{
							$missonDetails[$m]['product_name']=$this->seo_product_array[$mission['product']];			
							$missonDetails[$m]['language_source_name']=$this->getLanguageName($mission['language_source']);
							$missonDetails[$m]['product_type_name']=$this->producttype_array[$mission['product_type']];
							if($mission['language_dest'])
								$missonDetails[$m]['language_dest_name']=$this->getLanguageName($mission['language_dest']);						

							$missonDetails[$m]['comment_time']=time_ago($mission['created_at']);

							if($mission['product']=='seo_audit' || $mission['product']=='smo_audit' )
							{
								if($mission['internal_cost']>0)
								{
									$missonDetails[$m]['internal_cost']=$mission['internal_cost'];
									$audit=$mission['product']=='seo_audit' ?'SEO':'SMO';
									$missonDetails[$m]['internalcost_details']="$audit Audit : ".zero_cut($mission['cost'],2)." &".$quote['sales_suggested_currency'].";<br>";
									$total_internalcost+=$mission['internal_cost'];
								}
								else
								{
									$missonDetails[$m]['internal_cost']=$mission['cost'];
									$audit=$mission['product']=='seo_audit' ?'SEO':'SMO';
									$missonDetails[$m]['internalcost_details']="$audit Audit : ".zero_cut($mission['cost'],2)." &".$quote['sales_suggested_currency'].";<br>";
									$total_internalcost+=$mission['cost'];
								}	
								$missonDetails[$m]['required_writes']=1;

								//if mission is prior to prod
								if($mission['before_prod']=='yes')
									$prior_length_array[]=$mission['mission_length'];
							}
							else
							{

								//get Internal cost of a mission
								$prodMissionObj=new Ep_Quote_ProdMissions();
								$prodMissionDetails=$prodMissionObj->getProdCostDetails($mission['identifier']);
								//echo "<pre>";print_r($prodMissionDetails);
								if($prodMissionDetails)
								{
									$internalcost=0;
									$internalcost_details='';
									$staff_time=array();
									$prod_delivery_time=array();
									//$required_writes=1;

									foreach($prodMissionDetails as $prodMission)
									{
										$internalcost=$internalcost+$prodMission['cost'];
										$internalcost_details.=$this->seo_product_array[$prodMission['product']]. " : ".zero_cut($prodMission['cost'],2)." &".$prodMission['currency'].";<br>";

										
										//adding proof reading time too for mission total time

										if($prodMission['product']!='proofreading')
										{
											if($prodMission['staff_time_option']=='hours')
											{
												$staff_mission_stetup=($prodMission['staff_time']/24);
											}
											else
												$staff_mission_stetup=($prodMission['staff_time']);

											if($prodMission['delivery_option']=='hours')
											{
												$prod_delivery_stetup=($prodMission['delivery_time']/24);
											}
											else
												$prod_delivery_stetup=($prodMission['delivery_time']);
										}

										if($prodMission['product']=='proofreading')
										{
											if($prodMission['staff_time_option']=='hours')
											{
												$staff_mission_stetup+=($prodMission['staff_time']/24);
											}
											else
												$staff_mission_stetup+=($prodMission['staff_time']);

											if($prodMission['delivery_option']=='hours')
											{
												$prod_delivery_stetup+=($prodMission['delivery_time']/24);
											}
											else
												$prod_delivery_stetup+=($prodMission['delivery_time']);
										}										

										

										$staff_time[]=$staff_mission_stetup;
										$prod_delivery_time[]=$prod_delivery_stetup;

										//getting required writers	
										if($prodMission['product']=='redaction' || $prodMission['product']=='translation' || (!$required_writes && $prodMission['product']=='autre' ))
										{											
											$required_writes=$prodMission['staff'];
										}										

									}
									//required Writres
									$missonDetails[$m]['required_writes']=$required_writes;

									//echo "<pre>";print_r($prodMissionDetails);		
									/*if($mission['internal_cost']>0)
									{
										$missonDetails[$m]['internal_cost']=$mission['internal_cost'];
										$total_internalcost+=$mission['internal_cost'];
									}
									else						
									{*/
										$missonDetails[$m]['internal_cost']=$internalcost;
										$total_internalcost+=$internalcost;
									//}
									$missonDetails[$m]['internalcost_details']=$internalcost_details;


									//Adding prod staff setup time to mission length
									$prod_team_setup=max($staff_time)+max($prod_delivery_time);

									/*removed w.r.t tempo*/
									//$missonDetails[$m]['mission_length']=round($prod_team_setup);
									
								}
								else if($mission['internal_cost']>0)
								{
									//$missonDetails[$m]['internal_cost']=$mission['internal_cost'];
									//$missonDetails[$m]['internalcost_details'].="Internal cost : ".zero_cut($mission['internal_cost'],2)." &".$quote['sales_suggested_currency'].";<br>";
									$archmission_obj=new Ep_Quote_Mission();
									$archParameters['mission_id']=$mission['sales_suggested_missions'];
									$suggested_mission_details=$archmission_obj->getMissionDetails($archParameters);
									if($suggested_mission_details)
									{										
											
										$nb_words=($mission['nb_words']/$suggested_mission_details[0]['article_length']);
										$redactionCost=$nb_words*($suggested_mission_details[0]['writing_cost_before_signature']);
										$correctionCost=$nb_words*($suggested_mission_details[0]['correction_cost_before_signature']);
										$otherCost=$nb_words*($suggested_mission_details[0]['other_cost_before_signature']);

										$internalcost=($redactionCost+$correctionCost+$otherCost);
										$internalcost=number_format($internalcost,2,'.','');

										$missonDetails[$m]['internal_cost']=$internalcost;
									    $missonDetails[$m]['internalcost_details'].=$this->seo_product_array['redaction']. " : ".zero_cut($redactionCost,2)." &".$quote['sales_suggested_currency'].";<br>";
									    $missonDetails[$m]['internalcost_details'].=$this->seo_product_array['translation']. " : ".zero_cut($correctionCost,2)." &".$quote['sales_suggested_currency'].";<br>";
									    if($otherCost)
									    	$missonDetails[$m]['internalcost_details'].=$this->seo_product_array['autre']. " : ".zero_cut($otherCost,2)." &".$quote['sales_suggested_currency'].";<br>";


									    //pre-fill staff calculations

										//total mission words
										$mission_volume=$mission['volume'];
										$mission_nb_words=$mission['nb_words'];
										$total_mission_words=($mission_volume*$mission_nb_words);
								
										//words that can write per writer with in delivery weeks
										$sales_delivery_time=$quote['sales_delivery_time_option']=='hours' ? ($quote['sales_delivery_time']/24) : $quote['sales_delivery_time'];
										$sales_delivery_week=ceil($sales_delivery_time/7);

										$mission_product=$mission['product_type'];
										if($mission['product_type']=='autre')
												$mission_product='article_seo';
										$articles_perweek=$this->configval['max_writer_'.$mission_product];
										$words_perweek_peruser=$articles_perweek*250;
										$words_peruser_perdelivery=$sales_delivery_week*$words_perweek_peruser;

										//wrting and proofreading staff calculations
										$writing_staff=round($total_mission_words/$words_peruser_perdelivery);
										if(!$writing_staff || $writing_staff <1)
											$writing_staff=1;	

										$missonDetails[$m]['required_writes']=$writing_staff;

									}						

								}
								else
									$missonDetails[$m]['internal_cost']=0;
								
								//array of mission lengths
								$mission_length_array[]=$missonDetails[$m]['mission_length'];//$mission['mission_length'];
							}	


							$missonDetails[$m]['unit_price']=number_format(($missonDetails[$m]['internal_cost']/(1-($mission['margin_percentage']/100))),2, '.', '');
							//echo $missonDetails[$m]['unit_price']."--".$missonDetails[$m]['internal_cost']."--".$mission['margin_percentage']."<br>";
							$missonDetails[$m]['turnover']=$missonDetails[$m]['unit_price']*$mission['volume'];

							//total turnover and total unit price
							if($missonDetails[$m]['turnover'])	
							{
								$total_unitprice+=$missonDetails[$m]['unit_price'];
								$total_turnover+=$missonDetails[$m]['turnover'];
							}								

							//if mission is prior to prod
							if($mission['before_prod']=='yes')
								$prior_length_array[]=$mission['mission_length'];


							//versioning
							//mission versionings if version is gt 1
							if($quote['version']>1)
							{
								$previousVersion=($quote['version']-1);

								$quoteMissionObj=new Ep_Quote_QuoteMissions();
								$previousMissionDetails=$quoteMissionObj->getMissionVersionDetails($mission['identifier'],$previousVersion);
								//echo "<pre>";print_r($previousMissionDetails);exit;
								if($previousMissionDetails)
								{
									foreach($previousMissionDetails as $key=>$vmission)
									{
										$previousMissionDetails[$key]['product_name']=$this->seo_product_array[$vmission['product']];			
										$previousMissionDetails[$key]['language_source_name']=$this->getLanguageName($vmission['language_source']);
										$previousMissionDetails[$key]['product_type_name']=$this->producttype_array[$vmission['product_type']];
										if($vmission['language_dest'])
											$previousMissionDetails[$key]['language_dest_name']=$this->getLanguageName($vmission['language_dest']);

									}	

									//Get All version details of a mission									
									$allVersionMissionDetails=$quoteMissionObj->getMissionVersionDetails($mission['identifier']);
									if($allVersionMissionDetails)
									{
										$table_start='<table class="table quote-history table-striped">';
										$table_end='</table>';
										$language_versions=$product_type_versions=$volume_versions=$nb_words_versions='';
										$margin_versions=$internal_cost_versions=$turnover_versions=$price_versions=$mission_length_versions='';

										foreach($allVersionMissionDetails as $versions)
										{
										 	if($versions['product']=='translation')
										  		$language= $this->getLanguageName($versions['language_source'])." > ".$this->getLanguageName($vmission['language_dest']);
										  	else
										  		$language= $this->getLanguageName($versions['language_source']);
										  	
										  	$created_at=date("d/m/Y", strtotime($versions['created_at']));

										  	$version_text='v'.$versions['version'];

										  	$language_versions.="<tr><td>$language</td><td>$created_at</td><td>$version_text</td></tr>";
										  	$product_type_versions.="<tr><td>".$this->producttype_array[$versions['product_type']]."</td><td>$created_at</td><td>$version_text</td></tr>";
										  	$volume_versions.="<tr><td>".$versions['volume']."</td><td>$created_at</td><td>$version_text</td></tr>";
										  	$nb_words_versions.="<tr><td>".$versions['nb_words']."</td><td>$created_at</td><td>$version_text</td></tr>";
										  	$price_versions.="<tr><td>".zero_cut($versions['unit_price'],2)." &". $versions['sales_suggested_currency'].";</td><td>$created_at</td><td>$version_text</td></tr>";

										  	$mission_length_option=$this->duration_array[$versions['mission_length_option']];//$versions['mission_length_option']=='days' ? ' Jours' : ' Hours';

										  	$mission_length_versions.="<tr><td>".$versions['mission_length']." $mission_length_option</td><td>$created_at</td><td>$version_text</td></tr>";

										  	$turnover_versions.="<tr><td>".zero_cut($versions['turnover'],2)." &". $versions['sales_suggested_currency'].";</td><td>$created_at</td><td>$version_text</td></tr>";

										  	$internal_cost_versions.="<tr><td>".zero_cut($versions['internal_cost'],2)." &". $versions['sales_suggested_currency'].";</td><td>$created_at</td><td>$version_text</td></tr>";
										  	
										  	$margin_versions.="<tr><td>".$versions['margin_percentage']."</td><td>$created_at</td><td>$version_text</td></tr>";
										}										
									}


									//checking the version differences
									if($mission['language_source'] !=$previousMissionDetails[0]['language_source'])
									{
										$missonDetails[$m]['language_difference']='yes';
										$missonDetails[$m]['language_versions']=$table_start.$language_versions.$table_end;
									}

									if($mission['language_dest'] !=$previousMissionDetails[0]['language_dest'])
									{
										$missonDetails[$m]['language_difference']='yes';
										$missonDetails[$m]['language_versions']=$table_start.$language_versions.$table_end;
									}

									if($mission['product_type'] !=$previousMissionDetails[0]['product_type'])
									{
										$missonDetails[$m]['product_type_difference']='yes';
										$missonDetails[$m]['product_type_versions']=$table_start.$product_type_versions.$table_end;
									
									}

									if($missonDetails[$m]['turnover'] !=$previousMissionDetails[0]['turnover'])
									{
										$missonDetails[$m]['turnover_difference']='yes';
										$missonDetails[$m]['turnover_versions']=$table_start.$turnover_versions.$table_end;
									}	

									$current_internal_cost=number_format($missonDetails[$m]['internal_cost'],2);
									$prev_internal_cost=number_format($previousMissionDetails[0]['internal_cost'],2);

									if($current_internal_cost != $prev_internal_cost)
									{
										//echo $current_internal_cost."---".$prev_internal_cost."<br>";
										$missonDetails[$m]['internal_cost_difference']='yes';
										$missonDetails[$m]['internal_cost_versions']=$table_start.$internal_cost_versions.$table_end;
									}	

									if($mission['margin_percentage'] !=$previousMissionDetails[0]['margin_percentage'])
									{
										$missonDetails[$m]['margin_difference']='yes';
										$missonDetails[$m]['margin_versions']=$table_start.$margin_versions.$table_end;
									}

									if($mission['volume'] !=$previousMissionDetails[0]['volume'])
									{
										$missonDetails[$m]['volume_difference']='yes';
										$missonDetails[$m]['volume_versions']=$table_start.$volume_versions.$table_end;
									}
									
									if($mission['nb_words'] !=$previousMissionDetails[0]['nb_words'])
									{
										$missonDetails[$m]['nb_words_difference']='yes';
										$missonDetails[$m]['nb_words_versions']=$table_start.$nb_words_versions.$table_end;
									}
									
									//echo $missonDetails[$m]['unit_price']."--".$previousMissionDetails[0]['unit_price']."<br>";
									if($missonDetails[$m]['unit_price'] !=$previousMissionDetails[0]['unit_price'])
									{
										$missonDetails[$m]['unit_price_difference']='yes';
										$missonDetails[$m]['price_versions']=$table_start.$price_versions.$table_end;
									}

									$current_mission_lenght=$mission['mission_length_option']=='hours' ? ($missonDetails[$m]['mission_length']/24) : $missonDetails[$m]['mission_length'];
									$previous_mission_lenght=$previousMissionDetails[0]['mission_length_option']=='hours' ? ($previousMissionDetails[0]['mission_length']/24) : $previousMissionDetails[0]['mission_length'];
									//echo $current_mission_lenght."--".$previous_mission_lenght."<br>";
									if($current_mission_lenght !=$previous_mission_lenght)
									{
										$missonDetails[$m]['mission_length_difference']='yes';	
										$missonDetails[$m]['mission_length_versions']=$table_start.$mission_length_versions.$table_end;
									}



									$missonDetails[$m]['previousMissionDetails']=$previousMissionDetails;
								}	

							}

							//calculating team price
							if(!$missonDetails[$m]['team_fee'])
							{
								$teamPrice=0;							
								$teamPrice=350;//(ceil($missonDetails[$m]['required_writes']/3))*350;								
								$missonDetails[$m]['team_fee']=$teamPrice;
							}
							if(!$missonDetails[$m]['team_packs'])
							{
								//$missonDetails[$m]['team_packs']=(ceil($missonDetails[$m]['required_writes']/3));	
								$missonDetails[$m]['team_packs']=$missonDetails[$m]['required_writes'];
							}

							//calculate user turnover for user package
							if(!$missonDetails[$m]['user_fee'])
								$missonDetails[$m]['user_fee']=350;

							$missonDetails[$m]['user_package_turnover']=(($missonDetails[$m]['required_writes']*$missonDetails[$m]['user_fee']));
							//$missonDetails[$m]['team_package_turnover']=(($missonDetails[$m]['turnover']+$missonDetails[$m]['team_fee']));
							$missonDetails[$m]['team_package_turnover']=($missonDetails[$m]['turnover']+($missonDetails[$m]['team_fee']*$missonDetails[$m]['team_packs']));

							if($missonDetails[$m]['package']=='team')
								$total_turnover+=($missonDetails[$m]['team_package_turnover']-$missonDetails[$m]['turnover']);
							elseif($missonDetails[$m]['package']=='user')
								$total_turnover+=$missonDetails[$m]['user_package_turnover'];
							//echo $total_turnover;exit;	

							$missonDetails[$m]['tempo_length_option_text']=$this->duration_array[$mission['tempo_length_option']];

							$m++;
						}						
						//echo "<pre>";print_r($missonDetails);exit;
						$quoteDetails[$q]['mission_details']=$missonDetails;
					}
					/***************getting Tech mission details******************/
					$tech_obj=new Ep_Quote_TechMissions();
					$searchParameters['quote_id']=$quote_id;
					$searchParameters['include_final']='yes';
					$techMissionDetails=$tech_obj->getTechMissionDetails($searchParameters);
					if($techMissionDetails)
					{
						$t=0;
						$mission=array();
						foreach($techMissionDetails as $mission)
						{
							
							$techMissionDetails[$t]['internal_cost']=$mission['cost'];
							$techMissionDetails[$t]['unit_price']=number_format(($techMissionDetails[$t]['internal_cost']/(1-($mission['margin_percentage']/100))),2, '.', '');
						    $techMissionDetails[$t]['turnover']=$techMissionDetails[$t]['unit_price']*$mission['volume'];

						    $total_internalcost+=$mission['cost'];
						    $total_unitprice+=$techMissionDetails[$t]['unit_price'];
							$total_turnover+=$techMissionDetails[$t]['turnover'];


							$mission_length_array[]=$mission['delivery_time'];

							//if mission is prior to prod
							if($mission['before_prod']=='yes')
								$prior_length_array[]=$mission['delivery_time'];
							

							//calculating team price
							$techMissionDetails[$t]['required_writes']=1;
							if(!$techMissionDetails[$t]['team_fee'])
							{
								$teamPrice=0;							
								$teamPrice=350;//(ceil($techMissionDetails[$t]['required_writes']/3))*350;
								$techMissionDetails[$t]['team_fee']=$teamPrice;									
							}
							if(!$techMissionDetails[$t]['team_packs'])
							{
								//$techMissionDetails[$t]['team_packs']=(ceil($techMissionDetails[$t]['required_writes']/3));	
								$techMissionDetails[$t]['team_packs']=$techMissionDetails[$t]['required_writes'];
							}

							//calculate user turnover for user package
							if(!$techMissionDetails[$t]['user_fee'])
								$techMissionDetails[$t]['user_fee']=350;

							$techMissionDetails[$t]['user_package_turnover']=(($techMissionDetails[$t]['required_writes']*$techMissionDetails[$t]['user_fee']));
							//$techMissionDetails[$t]['team_package_turnover']=(($techMissionDetails[$t]['turnover']+$techMissionDetails[$t]['team_fee']));
							$techMissionDetails[$t]['team_package_turnover']=($techMissionDetails[$t]['turnover']+($techMissionDetails[$t]['team_fee']*$techMissionDetails[$t]['team_packs']));
							
							//mission versionings if version is gt 1
							if($quoteDetails[0]['version']>1)
							{
								$previousVersion=($quoteDetails[0]['version']-1);

								$techMissionObj=new Ep_Quote_TechMissions();
								$previousMissionDetails=$techMissionObj->getMissionVersionDetails($mission['identifier'],$quoteDetails[0]['identifier'],$previousVersion);
								
								if($previousMissionDetails)
								{						
									//Get All version details of a mission									
									$allVersionMissionDetails=$techMissionObj->getMissionVersionDetails($mission['identifier'],$quoteDetails[0]['identifier']);
									if($allVersionMissionDetails)
									{
										$table_start='<table class="table quote-history table-striped">';
										$table_end='</table>';								
										$price_versions=$mission_length_versions='';
										$title_versions='';

										foreach($allVersionMissionDetails as $versions)
										{
										 	
										  	
										  	$created_at=date("d/m/Y", strtotime($versions['created_at']));

										  	$version_text='v'.$versions['version'];
										  	
										  	$title_versions.="<tr><td>".$versions['title']."</td><td>$created_at</td><td>$version_text</td></tr>";

										  	$price_versions.="<tr><td>".zero_cut($versions['cost'],2)." &". $versions['currency'].";</td><td>$created_at</td><td>$version_text</td></tr>";

										  	$mission_length_option=$versions['delivery_option']=='days' ? ' Jours' : ' Hours';

										  	$mission_length_versions.="<tr><td>".$versions['delivery_time']." $mission_length_option</td><td>$created_at</td><td>$version_text</td></tr>";
										}										
									}


									//checking the version differences
									if($mission['title'] !=$previousMissionDetails[0]['title'])
									{
										$techMissionDetails[$t]['title_difference']='yes';
										$techMissionDetails[$t]['title_versions']=$table_start.$title_versions.$table_end;
									}


									if($mission['cost'] !=$previousMissionDetails[0]['cost'])
									{
										$techMissionDetails[$t]['cost_difference']='yes';
										$techMissionDetails[$t]['price_versions']=$table_start.$price_versions.$table_end;
									}

									$current_mission_lenght=$mission['delivery_option']=='hours' ? ($mission['delivery_time']/24) : $mission['delivery_time'];
									$previous_mission_lenght=$previousMissionDetails[0]['delivery_option']=='hours' ? ($previousMissionDetails[0]['delivery_time']/24) : $previousMissionDetails[0]['delivery_time'];
									if($current_mission_lenght !=$previous_mission_lenght)
									{
										$techMissionDetails[$t]['mission_length_difference']='yes';	
										$techMissionDetails[$t]['mission_length_versions']=$table_start.$mission_length_versions.$table_end;
									}



									$techMissionDetails[$t]['previousMissionDetails']=$previousMissionDetails;
								}	

							}
							$t++;
						}		

						$quoteDetails[$q]['tech_mission_details']=$techMissionDetails;
					}

					//echo "<pre>";print_r($techMissionDetails);exit;


					//total cost details
					$quoteDetails[$q]['total_internalcost']=$total_internalcost;
					$quoteDetails[$q]['total_unitprice']=$total_unitprice;
					$quoteDetails[$q]['total_turnover']=$total_turnover;
					$quoteDetails[$q]['over_all_margin']=number_format(((100-($quoteDetails[$q]['total_internalcost']/$quoteDetails[$q]['total_unitprice'])*100)),2);
					
					//commented w.r.t Tempo
					/*if(!$quoteDetails[$q]['final_mission_length'])
					{
						
						 if(count($prior_length_array)>0)
							$quoteDetails[$q]['total_mission_length']=max($mission_length_array)+max($prior_length_array);
						else 
							$quoteDetails[$q]['total_mission_length']=max($mission_length_array);
					}
					else*/
						if($quoteDetails[$q]['final_mission_length'])
							$quoteDetails[$q]['total_mission_length']=$quoteDetails[$q]['final_mission_length'];	
						else
							$quoteDetails[$q]['total_mission_length']='';

					//Quote versioning	
					if($quote['version']>1)
					{
						$previousVersion=($quote['version']-1);

						$quoteObj=new Ep_Quote_Quotes();
						$previousQuoteDetails=$quoteObj->getQuoteVersionDetails($quote['identifier'],$previousVersion);

						if($previousQuoteDetails)
						{
							//Get All Quote version Details
							$allVersionQuoteDetails=$quoteObj->getQuoteVersionDetails($quote['identifier']);							
							if($allVersionQuoteDetails)
							{
								$table_start='<table class="table quote-history table-striped">';
								$table_end='</table>';								
								$final_margin_versions=$final_turnover_versions=$final_mission_length_versions='';

								foreach($allVersionQuoteDetails as $versions)
								{
								 	
								  	$created_at=date("d/m/Y", strtotime($versions['created_at']));

								  	$version_text='v'.$versions['version'];								  	

								  	$mission_length_option=$versions['final_mission_length_option']=='days' ? ' Jours' : ' Hours';

								  	$final_mission_length_versions.="<tr><td>".$versions['final_mission_length']." $mission_length_option</td><td>$created_at</td><td>$version_text</td></tr>";

								  	$final_turnover_versions.="<tr><td>".zero_cut($versions['final_turnover'],2)." &". $versions['sales_suggested_currency'].";</td><td>$created_at</td><td>$version_text</td></tr>";								  	
								  	
								  	$final_margin_versions.="<tr><td>".$versions['final_margin']."</td><td>$created_at</td><td>$version_text</td></tr>";
								}

								//echo $quoteDetails[0]['total_turnover']."--".$previousQuoteDetails[0]['final_turnover'];
								if($quoteDetails[0]['total_turnover'] !=$previousQuoteDetails[0]['final_turnover'])
								{
									$quoteDetails[$q]['final_turnover_difference']='yes';
									$quoteDetails[$q]['final_turnover_versions']=$table_start.$final_turnover_versions.$table_end;
								}


								if($quoteDetails[0]['over_all_margin'] !=$previousQuoteDetails[0]['final_margin'])
								{
									$quoteDetails[$q]['final_margin_difference']='yes';
									$quoteDetails[$q]['final_margin_versions']=$table_start.$final_margin_versions.$table_end;
								}

								$current_quote_lenght=$quote['final_mission_length_option']=='hours' ? ($quoteDetails[$q]['total_mission_length']/24) : $quoteDetails[$q]['total_mission_length'];
								$previous_quote_lenght=$previousQuoteDetails[0]['final_mission_length_option']=='hours' ? ($previousQuoteDetails[0]['final_mission_length']/24) : $previousQuoteDetails[0]['final_mission_length'];								
								if($current_quote_lenght !=$previous_quote_lenght)
								{
									$quoteDetails[$q]['final_mission_length_difference']='yes';	
									$quoteDetails[$q]['final_mission_length_versions']=$table_start.$final_mission_length_versions.$table_end;
								}


							}
						}

						//Deleted mission version details
						
						$previousVersion=($quote['version']-1);
						$deletedMissionVersions=$this->deletedMissionVersions($quote['identifier'],$previousVersion);
						if($deletedMissionVersions)
							$quoteDetails[$q]['deletedMissionVersions']=$deletedMissionVersions;

						
						
					}

					$q++;
				}
				//echo "<pre>";print_r($quoteDetails);exit;
				$this->_view->quoteDetails=$quoteDetails;
			}
		}
	
		//echo "<pre>";print_r($quoteDetails);exit;

		if($prod_parameters['ajax']=='yes' && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
		{
			$this->render('popup-sales-final-validation');
		}
		else
			$this->render('sales-final-validation');
	
	
	
	
	}
	
	
	
	//Prod details view in final validation and followup
	public function prodViewDetails($quote_id)
	{
		$quote_obj=new Ep_Quote_Quotes();

		if($quote_id)
		{
			$quoteDetails=$quote_obj->getQuoteDetails($quote_id);
			if($quoteDetails)
			{
				$q=0;
				foreach($quoteDetails as $quote)
				{
					$quoteDetails[$q]['category_name']=$this->getCategoryName($quote['category']);
					$quoteDetails[$q]['websites']=explode("|",$quote['websites']);
					
					if($quote['documents_path'])
					{
						/* $related_files='';
						$documents_path=explode("|",$quote['documents_path']);
						$documents_name=explode("|",$quote['documents_name']);

						
						foreach($documents_path as $k=>$file)
						{
							if(file_exists($this->quote_documents_path.$documents_path[$k]) && !is_dir($this->quote_documents_path.$documents_path[$k]))
							{
							if($documents_name[$k])
								$file_name=$documents_name[$k];
							else
								$file_name=basename($file);

							$related_files.='
							<a href="/quote/download-document?type=quote&index='.$k.'&quote_id='.$quote_id.'">'.$file_name.'</a><br>';
							}
						} */
						$files = array('documents_path'=>$quote['documents_path'],'documents_name'=>$quote['documents_name'],'quote_id'=>$quote_id,'delete'=>false);
						$related_files = $this->getQuoteFiles($files);
					}

					$quoteDetails[$q]['related_files']=$related_files;

					$quoteDetails[$q]['sales_suggested_price_format']=number_format($quote['sales_suggested_price'], 2, ',', ' ');
					$quoteDetails[$q]['comment_time']=time_ago($quote['created_at']);
					

					//bo user details
					$quote_by=$quote['quote_by'];
					$client_obj=new Ep_Quote_Client();
					$bo_user_details=$client_obj->getQuoteUserDetails($quote_by);
					if($bo_user_details!='NO')
					{
						$quoteDetails[$q]['quote_user_name']=$bo_user_details[0]['first_name'].' '.$bo_user_details[0]['last_name'];
						$quoteDetails[$q]['email']=$bo_user_details[0]['email'];
						$quoteDetails[$q]['phone_number']=$bo_user_details[0]['phone_number'];
										
					}

					//getting mission details
					$searchParameters['quote_id']=$quote_id;
					$searchParameters['misson_user_type']='sales';
					$quoteMission_obj=new Ep_Quote_QuoteMissions();
					$missonDetails=$quoteMission_obj->getMissionDetails($searchParameters);
					if($missonDetails)
					{
						$m=0;
						foreach($missonDetails as $mission)
						{
							$missonDetails[$m]['product_name']=$this->product_array[$mission['product']];			
							$missonDetails[$m]['language_source_name']=$this->getLanguageName($mission['language_source']);
							$missonDetails[$m]['product_type_name']=$this->producttype_array[$mission['product_type']];
							if($mission['language_dest'])
								$missonDetails[$m]['language_dest_name']=$this->getLanguageName($mission['language_dest']);

							$quoteDetails[$q]['missions_list'][$mission['identifier']]='Mission '.($m+1).' - '.$missonDetails[$m]['product_name'];

							$missonDetails[$m]['comment_time']=time_ago($mission['created_at']);
							//mission versionings if version is gt 1
							if($quote['version']>1)
							{
								$previousVersion=($quote['version']-1);

								$quoteMissionObj=new Ep_Quote_QuoteMissions();
								$previousMissionDetails=$quoteMissionObj->getMissionVersionDetails($mission['identifier'],$previousVersion,'sales');
								
								if($previousMissionDetails)
								{
									foreach($previousMissionDetails as $key=>$vmission)
									{
										$previousMissionDetails[$key]['product_name']=$this->seo_product_array[$vmission['product']];			
										$previousMissionDetails[$key]['language_source_name']=$this->getLanguageName($vmission['language_source']);
										$previousMissionDetails[$key]['product_type_name']=$this->producttype_array[$vmission['product_type']];
										if($vmission['language_dest'])
											$previousMissionDetails[$key]['language_dest_name']=$this->getLanguageName($vmission['language_dest']);

									}	

									//Get All version details of a mission									
									$allVersionMissionDetails=$quoteMissionObj->getMissionVersionDetails($mission['identifier'],NULL,'sales');
									if($allVersionMissionDetails)
									{
										$table_start='<table class="table quote-history table-striped">';
										$table_end='</table>';
										$language_versions=$product_type_versions=$volume_versions=$nb_words_versions='';
										$price_versions=$mission_length_versions='';

										foreach($allVersionMissionDetails as $versions)
										{
										 	if($versions['product']=='translation')
										  		$language= $this->getLanguageName($versions['language_source'])." > ".$this->getLanguageName($vmission['language_dest']);
										  	else
										  		$language= $this->getLanguageName($versions['language_source']);
										  	
										  	$created_at=date("d/m/Y", strtotime($versions['created_at']));
										  	$version_text='v'.$versions['version'];

										  	$language_versions.="<tr><td>$language</td><td>$created_at</td><td>$version_text</td></tr>";
										  	$product_type_versions.="<tr><td>".$this->producttype_array[$versions['product_type']]."</td><td>$created_at</td><td>$version_text</td></tr>";
										  	$volume_versions.="<tr><td>".$versions['volume']."</td><td>$created_at</td><td>$version_text</td></tr>";
										  	$nb_words_versions.="<tr><td>".$versions['nb_words']."</td><td>$created_at</td><td>$version_text</td></tr>";
										  	$price_versions.="<tr><td>".zero_cut($versions['unit_price'],2)." &". $versions['sales_suggested_currency'].";</td><td>$created_at</td><td>$version_text</td></tr>";

										  	$mission_length_option=$this->duration_array[$versions['mission_length_option']];//$versions['mission_length_option']=='days' ? ' Jours' : ' Hours';

										  	$mission_length_versions.="<tr><td>".$versions['mission_length']." $mission_length_option</td><td>$created_at</td><td>$version_text</td></tr>";
										}										
									}


									//checking the version differences
									if($mission['language_source'] !=$previousMissionDetails[0]['language_source'])
									{
										$missonDetails[$m]['language_difference']='yes';
										$missonDetails[$m]['language_versions']=$table_start.$language_versions.$table_end;
									}

									if($mission['language_dest'] !=$previousMissionDetails[0]['language_dest'])
									{
										$missonDetails[$m]['language_difference']='yes';
										$missonDetails[$m]['language_versions']=$table_start.$language_versions.$table_end;
									}

									if($mission['product_type'] !=$previousMissionDetails[0]['product_type'])
									{
										$missonDetails[$m]['product_type_difference']='yes';
										$missonDetails[$m]['product_type_versions']=$table_start.$product_type_versions.$table_end;
									
									}

									if($mission['volume'] !=$previousMissionDetails[0]['volume'])
									{
										$missonDetails[$m]['volume_difference']='yes';
										$missonDetails[$m]['volume_versions']=$table_start.$volume_versions.$table_end;
									}
									
									if($mission['nb_words'] !=$previousMissionDetails[0]['nb_words'])
									{
										$missonDetails[$m]['nb_words_difference']='yes';
										$missonDetails[$m]['nb_words_versions']=$table_start.$nb_words_versions.$table_end;
									}
									
									if($mission['unit_price'] !=$previousMissionDetails[0]['unit_price'])
									{
										$missonDetails[$m]['unit_price_difference']='yes';
										$missonDetails[$m]['price_versions']=$table_start.$price_versions.$table_end;
									}

									$current_mission_lenght=$mission['mission_length_option']=='hours' ? ($mission['mission_length']/24) : $mission['mission_length'];
									$previous_mission_lenght=$previousMissionDetails[0]['mission_length_option']=='hours' ? ($previousMissionDetails[0]['mission_length']/24) : $previousMissionDetails[0]['mission_length'];
									if($current_mission_lenght !=$previous_mission_lenght)
									{
										$missonDetails[$m]['mission_length_difference']='yes';	
										$missonDetails[$m]['mission_length_versions']=$table_start.$mission_length_versions.$table_end;
									}



									$missonDetails[$m]['previousMissionDetails']=$previousMissionDetails;
								}	

							}


							//Get seo missions related to a mission
							$searchParameters['quote_id']=$quote_id;
							$searchParameters['misson_user_type']='seo';
							$searchParameters['related_to']=$mission['identifier'];
							$searchParameters['product']=$mission['product'];
							//echo "<pre>";print_r($searchParameters);
							$quoteMission_obj=new Ep_Quote_QuoteMissions();
							$seoMissonDetails=$quoteMission_obj->getMissionDetails($searchParameters);
							//echo "<pre>";print_r($seoMissonDetails);exit;
							if($seoMissonDetails)
							{
								$s=0;
								foreach($seoMissonDetails as $smission)
								{									
									$client_obj=new Ep_Quote_Client();
									$bo_user_details=$client_obj->getQuoteUserDetails($smission['created_by']);
									$seoMissonDetails[$s]['seo_user_name']=$bo_user_details[0]['first_name'].' '.$bo_user_details[0]['last_name'];

									$seoMissonDetails[$s]['comment_time']=time_ago($smission['created_at']);

									$seoMissonDetails[$s]['product_type_name']=$this->producttype_array[$smission['product_type']];

									$prodMissionObj=new Ep_Quote_ProdMissions();

									$searchParameters['quote_mission_id']=$smission['identifier'];
									$prodMissionDetails=$prodMissionObj->getProdMissionDetails($searchParameters);
									//echo "<pre>";print_r($prodMissionDetails);exit;

									if($prodMissionDetails)
									{
										$seoMissonDetails[$s]['prod_mission_details']=$prodMissionDetails;	
									}
									else
									{

										//getting suggested mission Details for seo missions
										if($smission['sales_suggested_missions'])
										{
											$archmission_obj=new Ep_Quote_Mission();
											$archParameters['mission_id']=$smission['sales_suggested_missions'];
											$suggested_mission_details=$archmission_obj->getMissionDetails($archParameters);										
											if($suggested_mission_details)
											{
												foreach($suggested_mission_details as $key=>$suggested_mission)
												{
													$sug_mission_length=$smission['volume']*($smission['nb_words']/$suggested_mission['article_length']);
													$prod_mission_length=round($suggested_mission['mission_length']*($sug_mission_length/$suggested_mission['num_of_articles']));
													$suggested_mission_details[$key]['mission_length']=$prod_mission_length;

													$suggested_mission_details[$key]['mission_length']=round(($smission['mission_length']*90)/100);
													$staff_setup_length=ceil(($smission['mission_length']*10)/100);
													
													$staff_setup_length=$staff_setup_length ? $staff_setup_length :1;
													$staff_setup_length=$staff_setup_length < 10 ? $staff_setup_length :10;
													
													$suggested_mission_details[$key]['staff_setup_length']=$staff_setup_length < 10 ? $staff_setup_length :10;

													//pre-fill staff calculations

													//total mission words
													$mission_volume=$smission['volume'];
													$mission_nb_words=$smission['nb_words'];
													$total_mission_words=($mission_volume*$mission_nb_words);
											
													//words that can write per writer with in delivery weeks
													$sales_delivery_time=$smission['mission_length_option']=='hours' ? ($smission['mission_length']/24) : $smission['mission_length'];
													$sales_delivery_week=ceil($sales_delivery_time/7);

													$mission_product=$smission['product_type'];
													$articles_perweek=$this->configval['max_writer_'.$mission_product];
													$words_perweek_peruser=$articles_perweek*250;
													$words_peruser_perdelivery=$sales_delivery_week*$words_perweek_peruser;

													//wrting and proofreading staff calculations
													$writing_staff=round($total_mission_words/$words_peruser_perdelivery);
													if(!$writing_staff || $writing_staff <1)
														$writing_staff=1;													

													$suggested_mission_details[$key]['writing_staff']=$writing_staff;
													
												}
												
												$seoMissonDetails[$s]['suggested_mission_details']=$suggested_mission_details;

												//staff time details
												$seoMissonDetails[$s]['staff_time']=$staff_setup_length;	
											}
											//echo "<pre>";print_r($seoMissonDetails);exit;
										}	
									}	

									$s++;	
								}

								$missonDetails[$m]['seoMissions']=$seoMissonDetails;
							}
							//echo "<pre>";print_r($missonDetails);exit;

							$prodMissionObj=new Ep_Quote_ProdMissions();

							$searchParameters['quote_mission_id']=$mission['identifier'];
							$prodMissionDetails=$prodMissionObj->getProdMissionDetails($searchParameters);
							//echo "<pre>";print_r($prodMissionDetails);exit;

							if($prodMissionDetails)
							{
								$p=0;
								foreach($prodMissionDetails as $mission)
								{
									//mission versionings if version is gt 1
									if($quote['version']>1)
									{
										$previousVersion=($quote['version']-1);

										$prodMissionObj=new Ep_Quote_ProdMissions();
										$previousMissionDetails=$prodMissionObj->getMissionVersionDetails($mission['identifier'],$previousVersion);
										
										if($previousMissionDetails)
										{						
											//Get All version details of a mission									
											$allVersionMissionDetails=$prodMissionObj->getMissionVersionDetails($mission['identifier']);

											if($allVersionMissionDetails)
											{
												$table_start='<table class="table quote-history table-striped">';
												$table_end='</table>';								
												$price_versions=$mission_length_versions='';
												$staff_versions=$staff_length_versions='';

												foreach($allVersionMissionDetails as $versions)
												{
												 	
												  	
												  	$created_at=date("d/m/Y", strtotime($versions['created_at']));
												  	$version_text='v'.$versions['version'];											  	
												  	
												  	$staff_versions.="<tr><td>".$versions['staff']."</td><td>$created_at</td><td>$version_text</td></tr>";

												  	$price_versions.="<tr><td>".zero_cut($versions['cost'],2)." &". $versions['currency'].";</td><td>$created_at</td><td>$version_text</td></tr>";

												  	$staff_length_option=$versions['staff_time_option']=='days' ? ' Jours' : ' Hours';

												  	$staff_length_versions.="<tr><td>".$versions['staff_time']." $staff_length_option</td><td>$created_at</td><td>$version_text</td></tr>";

												  	$mission_length_option=$versions['delivery_option']=='days' ? ' Jours' : ' Hours';

												  	$mission_length_versions.="<tr><td>".$versions['delivery_time']." $mission_length_option</td><td>$created_at</td><td>$version_text</td></tr>";
												}										
											}


											//checking the version differences										
											

											if($mission['cost'] !=$previousMissionDetails[0]['cost'])
											{
												$prodMissionDetails[$p]['cost_difference']='yes';
												$prodMissionDetails[$p]['price_versions']=$table_start.$price_versions.$table_end;
											}
											if($mission['staff'] !=$previousMissionDetails[0]['staff'])
											{
												$prodMissionDetails[$p]['staff_difference']='yes';
												$prodMissionDetails[$p]['staff_versions']=$table_start.$staff_versions.$table_end;
											}

											$current_mission_lenght=$mission['delivery_option']=='hours' ? ($mission['delivery_time']/24) : $mission['delivery_time'];
											$previous_mission_lenght=$previousMissionDetails[0]['delivery_option']=='hours' ? ($previousMissionDetails[0]['delivery_time']/24) : $previousMissionDetails[0]['delivery_time'];
											if($current_mission_lenght !=$previous_mission_lenght)
											{
												$prodMissionDetails[$p]['mission_length_difference']='yes';	
												$prodMissionDetails[$p]['mission_length_versions']=$table_start.$mission_length_versions.$table_end;
											}

											$current_staff_lenght=$mission['staff_time_option']=='hours' ? ($mission['staff_time']/24) : $mission['staff_time'];
											$previous_staff_lenght=$previousMissionDetails[0]['staff_time_option']=='hours' ? ($previousMissionDetails[0]['staff_time']/24) : $previousMissionDetails[0]['staff_time'];
											if($current_staff_lenght !=$previous_staff_lenght)
											{
												$prodMissionDetails[$p]['staff_length_difference']='yes';	
												$prodMissionDetails[$p]['staff_length_versions']=$table_start.$staff_length_versions.$table_end;
											}



											$prodMissionDetails[$p]['previousMissionDetails']=$previousMissionDetails;
										}	

									}
									$p++;
								}


								$missonDetails[$m]['prod_mission_details']=$prodMissionDetails;	
							}
							else
							{
								//getting suggested mission Details for quote missions
								if($mission['sales_suggested_missions'])
								{
									$archmission_obj=new Ep_Quote_Mission();
									$archParameters['mission_id']=$mission['sales_suggested_missions'];
									$suggested_mission_details=$archmission_obj->getMissionDetails($archParameters);
									if($suggested_mission_details)
									{
										foreach($suggested_mission_details as $key=>$suggested_mission)
										{
											
											if($suggested_mission['writing_cost_before_signature_currency']!=$quote['sales_suggested_currency'])
											{
												$conversion=$quote['conversion'];
												$suggested_mission_details[$key]['writing_cost_before_signature']=($suggested_mission['writing_cost_before_signature']*$conversion);
												$suggested_mission_details[$key]['correction_cost_before_signature']=($suggested_mission['correction_cost_before_signature']*$conversion);
												$suggested_mission_details[$key]['other_cost_before_signature']=($suggested_mission['other_cost_before_signature']*$conversion);
												$suggested_mission_details[$key]['unit_price']=($suggested_mission['selling_price']*$conversion);
											}
											else
												$suggested_mission_details[$key]['unit_price']=($suggested_mission['selling_price']);
											
											
											$suggested_mission_details[$key]['mission_length']=round(($mission['mission_length']*90)/100);
											$staff_setup_length=ceil(($mission['mission_length']*10)/100);
											
											$staff_setup_length=$staff_setup_length ? $staff_setup_length :1;
											$staff_setup_length=$staff_setup_length < 10 ? $staff_setup_length :10;
													
											$suggested_mission_details[$key]['staff_setup_length']=$staff_setup_length < 10 ? $staff_setup_length :10;

											//pre-fill staff calculations

											//total mission words
											$mission_volume=$mission['volume'];
											$mission_nb_words=$mission['nb_words'];
											$total_mission_words=($mission_volume*$mission_nb_words);
									
											//words that can write per writer with in delivery weeks
											$sales_delivery_time=$mission['mission_length_option']=='hours' ? ($mission['mission_length']/24) : $mission['mission_length'];
											$sales_delivery_week=ceil($sales_delivery_time/7);

											$mission_product=$mission['product_type'];
											if($mission['product_type']=='autre')
												$mission_product='article_seo';

											$articles_perweek=$this->configval['max_writer_'.$mission_product];
											$words_perweek_peruser=$articles_perweek*250;
											$words_peruser_perdelivery=$sales_delivery_week*$words_perweek_peruser;

											//wrting and proofreading staff calculations
											$writing_staff=round($total_mission_words/$words_peruser_perdelivery);
											if(!$writing_staff || $writing_staff <1)
												$writing_staff=1;

											$proofreading_staff=round($total_mission_words/($words_peruser_perdelivery*5));
											if(!$proofreading_staff || $proofreading_staff <1)
												$proofreading_staff=1;

											$suggested_mission_details[$key]['writing_staff']=$writing_staff;
											$suggested_mission_details[$key]['proofreading_staff']=$proofreading_staff;

											//ENDED
										}


										$missonDetails[$m]['suggested_mission_details']=$suggested_mission_details;	
										
										//staff time details
										$missonDetails[$m]['staff_time']=$staff_setup_length;	
									}
								}
							}	

							$m++;
						}						
						//echo "<pre>";print_r($missonDetails);exit;
						$quoteDetails[$q]['mission_details']=$missonDetails;
					}
					if($quote['version']>1)
					{
						$previousVersion=($quote['version']-1);
						$deletedMissionVersions=$this->deletedMissionVersions($quote['identifier'],$previousVersion,'sales');
						if($deletedMissionVersions)
							$quoteDetails[$q]['deletedMissionVersions']=$deletedMissionVersions;
					}

					//client aims
						$client_aims=explode(",",$quote['client_aims']);
						$client_prio=explode(",",$quote['client_prio']);
						$client_aims_text='';
						if(count($client_aims)>0 && is_array($client_aims))
						{
							
							foreach($client_aims as $i=>$aim)
							{
								$client_aims_text.='<b>'.ucfirst($aim).'</b> - Prio '.$client_prio[$i].'<br>';
							}

						}
						$quoteDetails[$q]['client_aims_text']=$client_aims_text;		

					$q++;
				}
			}
			$this->_view->quoteDetails=$quoteDetails;

			//echo "<pre>";print_r($quoteDetails);exit;			

			//getting tech mission details
			$tech_obj=new Ep_Quote_TechMissions();
			$searchParameters['quote_id']=$quote_id;
			$techMissionDetails=$tech_obj->getTechMissionDetails($searchParameters);
			//echo "<pre>";print_r($techMissionDetails);exit;
			if($techMissionDetails)
			{
				$t=0;
				foreach($techMissionDetails as $mission)
				{
					$client_obj=new Ep_Quote_Client();
					$bo_user_details=$client_obj->getQuoteUserDetails($mission['created_by']);
					$techMissionDetails[$t]['tech_user_name']=$bo_user_details[0]['first_name'].' '.$bo_user_details[0]['last_name'];
					$techMissionDetails[$t]['comment_time']=time_ago($mission['created_at']);

					//mission versionings if version is gt 1
					if($quoteDetails[0]['version']>1)
					{
						$previousVersion=($quoteDetails[0]['version']-1);

						$techMissionObj=new Ep_Quote_TechMissions();
						$previousMissionDetails=$techMissionObj->getMissionVersionDetails($mission['identifier'],$quoteDetails[0]['identifier'],$previousVersion);
						
						if($previousMissionDetails)
						{						
							//Get All version details of a mission									
							$allVersionMissionDetails=$techMissionObj->getMissionVersionDetails($mission['identifier'],$quoteDetails[0]['identifier']);
							if($allVersionMissionDetails)
							{
								$table_start='<table class="table quote-history table-striped">';
								$table_end='</table>';								
								$price_versions=$mission_length_versions='';
								$title_versions='';

								foreach($allVersionMissionDetails as $versions)
								{
								 	
								  	
								  	$created_at=date("d/m/Y", strtotime($versions['created_at']));
								  	$version_text='v'.$versions['version'];
								  	
								  	$title_versions.="<tr><td>".$versions['title']."</td><td>$created_at</td><td>$version_text</td></tr>";

								  	$price_versions.="<tr><td>".zero_cut($versions['cost'],2)." &". $versions['currency'].";</td><td>$created_at</td><td>$version_text</td></tr>";

								  	$mission_length_option=$versions['delivery_option']=='days' ? ' Jours' : ' Hours';

								  	$mission_length_versions.="<tr><td>".$versions['delivery_time']." $mission_length_option</td><td>$created_at</td><td>$version_text</td></tr>";
								}										
							}


							//checking the version differences
							if($mission['title'] !=$previousMissionDetails[0]['title'])
							{
								$techMissionDetails[$t]['title_difference']='yes';
								$techMissionDetails[$t]['title_versions']=$table_start.$title_versions.$table_end;
							}
							

							if($mission['cost'] !=$previousMissionDetails[0]['cost'])
							{
								$techMissionDetails[$t]['cost_difference']='yes';
								$techMissionDetails[$t]['price_versions']=$table_start.$price_versions.$table_end;
							}

							$current_mission_lenght=$mission['delivery_option']=='hours' ? ($mission['delivery_time']/24) : $mission['delivery_time'];
							$previous_mission_lenght=$previousMissionDetails[0]['delivery_option']=='hours' ? ($previousMissionDetails[0]['delivery_time']/24) : $previousMissionDetails[0]['delivery_time'];
							if($current_mission_lenght !=$previous_mission_lenght)
							{
								$techMissionDetails[$t]['mission_length_difference']='yes';	
								$techMissionDetails[$t]['mission_length_versions']=$table_start.$mission_length_versions.$table_end;
							}



							$techMissionDetails[$t]['previousMissionDetails']=$previousMissionDetails;
						}	

					}
					
					$techMissionDetails[$t]['files'] = "";
					if($mission['documents_path'])
					{
						$filesarray = array('documents_path'=>$mission['documents_path'],'documents_name'=>$mission['documents_name'],'id'=>$mission['identifier'],'delete'=>false);
						$files = $this->getTechFiles($filesarray);
						$techMissionDetails[$t]['files'] = $files;
					}

					$t++;
				}				
				
				$this->_view->techMissionDetails=$techMissionDetails;
			}

			//ALL language list
			$language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        	natsort($language_array);
        	$this->_view->ep_language_list=$language_array;

			//getting seo mission details
			//getting mission details
			unset($searchParameters);
			$searchParameters['quote_id']=$quote_id;
			$searchParameters['misson_user_type']='seo';
			$quoteMission_obj=new Ep_Quote_QuoteMissions();
			$seoMissionDetails=$quoteMission_obj->getMissionDetails($searchParameters);
			if($seoMissionDetails)
			{
				$s=0;
				foreach($seoMissionDetails as $mission)
				{
					if($mission['documents_path'])
					{
						$filesarray = array('documents_path'=>$mission['documents_path'],'documents_name'=>$mission['documents_name'],'id'=>$mission['identifier'],'delete'=>false);
						$files = $this->getSeoFiles($filesarray);
						$seoMissionDetails[$s]['files'] = $files;
					}
					$client_obj=new Ep_Quote_Client();
					$bo_user_details=$client_obj->getQuoteUserDetails($mission['created_by']);
					$seoMissionDetails[$s]['seo_user_name']=$bo_user_details[0]['first_name'].' '.$bo_user_details[0]['last_name'];

					$seoMissionDetails[$s]['comment_time']=time_ago($mission['created_at']);

					$seoMissionDetails[$s]['product_name']=$this->seo_product_array[$mission['product']];

					//mission versionings if version is gt 1
					if($quoteDetails[0]['version']>1)
					{
						$previousVersion=($quoteDetails[0]['version']-1);

						$quoteMissionObj=new Ep_Quote_QuoteMissions();
						$previousMissionDetails=$quoteMissionObj->getMissionVersionDetails($mission['identifier'],$previousVersion,'seo');
						
						if($previousMissionDetails)
						{
							foreach($previousMissionDetails as $key=>$vmission)
							{
								$previousMissionDetails[$key]['product_name']=$this->seo_product_array[$vmission['product']];			
								$previousMissionDetails[$key]['language_source_name']=$this->getLanguageName($vmission['language_source']);
								$previousMissionDetails[$key]['product_type_name']=$this->producttype_array[$vmission['product_type']];
								if($vmission['language_dest'])
									$previousMissionDetails[$key]['language_dest_name']=$this->getLanguageName($vmission['language_dest']);

							}	

							//Get All version details of a mission									
							$allVersionMissionDetails=$quoteMissionObj->getMissionVersionDetails($mission['identifier'],NULL,'seo');
							if($allVersionMissionDetails)
							{
								$table_start='<table class="table quote-history table-striped">';
								$table_end='</table>';
								$product_versions=$language_versions=$product_type_versions=$volume_versions=$nb_words_versions='';
								$price_versions=$mission_length_versions='';

								foreach($allVersionMissionDetails as $versions)
								{
								 	if($versions['product']=='translation')
								  		$language= $this->getLanguageName($versions['language_source'])." > ".$this->getLanguageName($vmission['language_dest']);
								  	else
								  		$language= $this->getLanguageName($versions['language_source']);
								  	
								  	$created_at=date("d/m/Y", strtotime($versions['created_at']));
								  	$version_text='v'.$versions['version'];

								  	$language_versions.="<tr><td>$language</td><td>$created_at</td><td>$version_text</td></tr>";
								  	$product_versions.="<tr><td>".$this->seo_product_array[$versions['product']]."</td><td>$created_at</td><td>$version_text</td></tr>";
								  	$product_type_versions.="<tr><td>".$this->producttype_array[$versions['product_type']]."</td><td>$created_at</td><td>$version_text</td></tr>";
								  	$volume_versions.="<tr><td>".$versions['volume']."</td><td>$created_at</td><td>$version_text</td></tr>";
								  	$nb_words_versions.="<tr><td>".$versions['nb_words']."</td><td>$created_at</td><td>$version_text</td></tr>";
								  	$price_versions.="<tr><td>".zero_cut($versions['cost'],2)." &". $versions['sales_suggested_currency'].";</td><td>$created_at</td><td>$version_text</td></tr>";

								  	$mission_length_option=$this->duration_array[$versions['mission_length_option']];//$versions['mission_length_option']=='days' ? ' Jours' : ' Hours';

								  	$mission_length_versions.="<tr><td>".$versions['mission_length']." $mission_length_option</td><td>$created_at</td><td>$version_text</td></tr>";
								}										
							}


							//checking the version differences
							if($mission['language_source'] !=$previousMissionDetails[0]['language_source'])
							{
								$seoMissionDetails[$s]['language_difference']='yes';
								$seoMissionDetails[$s]['language_versions']=$table_start.$language_versions.$table_end;
							}

							if($mission['language_dest'] !=$previousMissionDetails[0]['language_dest'])
							{
								$seoMissionDetails[$s]['language_difference']='yes';
								$seoMissionDetails[$s]['language_versions']=$table_start.$language_versions.$table_end;
							}
							if($mission['product'] !=$previousMissionDetails[0]['product'])
							{
								$seoMissionDetails[$s]['product_difference']='yes';
								$seoMissionDetails[$s]['product_versions']=$table_start.$product_versions.$table_end;
							
							}


							if($mission['product_type'] !=$previousMissionDetails[0]['product_type'])
							{
								$seoMissionDetails[$s]['product_type_difference']='yes';
								$seoMissionDetails[$s]['product_type_versions']=$table_start.$product_type_versions.$table_end;
							
							}

							if($mission['volume'] !=$previousMissionDetails[0]['volume'])
							{
								$seoMissionDetails[$s]['volume_difference']='yes';
								$seoMissionDetails[$s]['volume_versions']=$table_start.$volume_versions.$table_end;
							}
							
							if($mission['nb_words'] !=$previousMissionDetails[0]['nb_words'])
							{
								$seoMissionDetails[$s]['nb_words_difference']='yes';
								$seoMissionDetails[$s]['nb_words_versions']=$table_start.$nb_words_versions.$table_end;
							}
							
							if($mission['cost'] !=$previousMissionDetails[0]['cost'])
							{
								$seoMissionDetails[$s]['unit_price_difference']='yes';
								$seoMissionDetails[$s]['price_versions']=$table_start.$price_versions.$table_end;
							}

							$current_mission_lenght=$mission['mission_length_option']=='hours' ? ($mission['mission_length']/24) : $mission['mission_length'];
							$previous_mission_lenght=$previousMissionDetails[0]['mission_length_option']=='hours' ? ($previousMissionDetails[0]['mission_length']/24) : $previousMissionDetails[0]['mission_length'];
							if($current_mission_lenght !=$previous_mission_lenght)
							{
								$seoMissionDetails[$s]['mission_length_difference']='yes';	
								$seoMissionDetails[$s]['mission_length_versions']=$table_start.$mission_length_versions.$table_end;
							}



							$seoMissionDetails[$s]['previousMissionDetails']=$previousMissionDetails;
						}	

					}

					$s++;
				}	
				$this->_view->seoMissionDetails=$seoMissionDetails;
			}
			
		}
		//echo "<pre>";print_r($seoMissionDetails);exit;

		return $html=$this->_view->renderHtml('prod-quote-view-details'); 

		
	}
	
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
    /**function to get the language type name**/
    public function getLanguageName($lang_value)
    {
        $language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        return $language_array[$lang_value];
    }
    /**function to get the country name**/
    public function getCountryName($country_value)
    {
        $country_array=$this->_arrayDb->loadArrayv2("countryList", $this->_lang);
        return $country_array[$country_value];
    }
	
	/* To get Quote Files */
	function getQuoteFiles($quote=array())
	{
		$files='<table class="table">';
		$documents_path=array_filter(explode("|",$quote['documents_path']));
		$documents_name=explode("|",$quote['documents_name']);
		$quote_id = $quote['quote_id'];
		$zip = "";
		
		if(!$quote['delete']):
		foreach($documents_path as $k=>$file)
		{
			$file_path=$this->quote_documents_path.$documents_path[$k];
			if(file_exists($file_path) && !is_dir($this->quote_documents_path.$documents_path[$k]))
			{
				$zip = true;
				if($documents_name[$k])
					$file_name=$documents_name[$k];
				else
					$file_name=basename($file);
				$ofilename = pathinfo($file_path);
				$files .= '<tr><td width="30%">'.$file_name.'</td><td width="35%">'.substr($ofilename['filename'],0,-3).".".$ofilename['extension'].'</td><td width="20%">'.formatSizeUnits(filesize($file_path)).'</td><td>Sales</td><td align="center" width="15%"><a href="/quote/download-document?type=quote&quote_id='.$quote_id.'&index='.$k.'"><i style="margin-right:5px" class="splashy-download"></i></a><td></tr>';
			}
		}
		endif;
		
		if($zip)
			$zip = '<thead><tr><td colspan="5"><a href="/quote/download-document?type=quote&index=-1&quote_id='.$quote_id.'" class="btn btn-small pull-right">Download Zip</a></td></tr></thead>';
		$files .=$zip."</table>";
		return $files;
	}
	
	/* To get Tech Files */
	function getTechFiles($mission = array())
	{
		$exploded_file_paths = array_filter(explode("|",$mission['documents_path']));
		$exploded_file_names = explode("|",$mission['documents_name']);
		$zip = "";
		
		$files = '<table class="table">';
		$k=0;
		if($mission['delete']):
		foreach($exploded_file_paths as $row)
		{
			$file_path=$this->mission_documents_path.$row;
			if(file_exists($file_path) && !is_dir($file_path))
			{
				$zip = true;
				$fname = $exploded_file_names[$k];
				if($fname=="")
					$fname = basename($row);
				$ofilename = pathinfo($file_path);
				$files .= '<tr><td width="30%">'.$fname.'</td><td width="35%">'.substr($ofilename['filename'],0,-3).".".$ofilename['extension'].'</td><td width="20%">'.formatSizeUnits(filesize($file_path)).'</td><td>Tech</td><td align="center" width="15%"><a href="/quote/download-document?type=tech_mission&mission_id='.$mission['id'].'&index='.$k.'"><i style="margin-right:5px" class="splashy-download"></i></a><span class="deletetech" rel="'.$k.'_'.$mission['id'].'"> <i class="icon-adt_trash"></i></span></td></tr>';	
			}
			$k++;
		}
		else:
		foreach($exploded_file_paths as $row)
		{
			$file_path=$this->mission_documents_path.$row;
			if(file_exists($file_path) && !is_dir($file_path))
			{
				$zip = true;
				$fname = $exploded_file_names[$k];
				if($fname=="")
					$fname = basename($row);
				$ofilename = pathinfo($file_path);
				$files .= '<tr><td width="30%">'.$fname.'</td><td width="35%">'.substr($ofilename['filename'],0,-3).".".$ofilename['extension'].'</td><td width="20%">'.formatSizeUnits(filesize($file_path)).'</td><td>Tech</td><td align="center" width="15%"><a href="/quote/download-document?type=tech_mission&mission_id='.$mission['id'].'&index='.$k.'"><i style="margin-right:5px" class="splashy-download"></i></a></td></tr>';	
			}
			$k++;
		}
		endif;			
		if($zip)
			$zip = '<thead><tr><td colspan="5"><a href="/quote/download-document?type=tech_mission&index=-1&mission_id='.$mission['id'].'" class="btn btn-small pull-right">Download Zip</a></td></tr></thead>';
		$files .=$zip."</table>";
		return $files;
	}
	
	/* To get SEO files */
	function getSeoFiles($mission=array())
	{
		$exploded_file_paths = array_filter(explode("|",$mission['documents_path']));
		$exploded_file_names = explode("|",$mission['documents_name']);
		$zip = "";
		
		
		
		$files = '<table class="table">'.$zip;
		$k=0;
		if($mission['delete']):
		foreach($exploded_file_paths as $row)
		{
			$file_path=$this->mission_documents_path.$row;
			if(file_exists($file_path) && !is_dir($file_path))
			{
					$zip = true;
					$fname = $exploded_file_names[$k];
					if($fname=="")
						$fname = basename($row);
					$ofilename = pathinfo($file_path);
					$files .= '<tr><td width="30%">'.$fname.'</td><td width="35%">'.substr($ofilename['filename'],0,-3).".".$ofilename['extension'].'</td><td width="20%">'.formatSizeUnits(filesize($file_path)).'</td><td>SEO</td><td align="center" width="15%"><a href="/quote/download-document?type=seo_mission&mission_id='.$mission['id'].'&index='.$k.'"><i style="margin-right:5px" class="splashy-download"></i></a><span class="delete" rel="'.$k.'_'.$mission['id'].'"> <i class="icon-adt_trash"></i></span></td></tr>';	
					
			}
			$k++;
		} 
		
		else:
		foreach($exploded_file_paths as $row)
		{
			$file_path=$this->mission_documents_path.$row;
			if(file_exists($file_path) && !is_dir($file_path))
			{
					$zip = true;
					$fname = $exploded_file_names[$k];
					if($fname=="")
						$fname = basename($row);
					$ofilename = pathinfo($file_path);
					$files .= '<tr><td width="30%">'.$fname.'</td><td width="35%">'.substr($ofilename['filename'],0,-3).".".$ofilename['extension'].'</td><td width="20%">'.formatSizeUnits(filesize($file_path)).'</td><td>SEO</td><td align="center" width="15%"><a href="/quote/download-document?type=seo_mission&mission_id='.$mission['id'].'&index='.$k.'"><i style="margin-right:5px" class="splashy-download"></i></a></td></tr>';	
					
			}
			$k++;
		} 
		
		endif;
		if($zip)
			$zip = '<thead><tr><td colspan="5"><a href="/quote/download-document?type=seo_mission&index=-1&mission_id='.$mission['id'].'" class="btn btn-small pull-right">Download Zip</a></td></tr></thead>';
		$files .= $zip.'</table>';
		return $files;
	}
	
	
	
}

?>