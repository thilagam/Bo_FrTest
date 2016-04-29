<?php
class DeliveryDashboardController extends Ep_Controller_Action
{

	public function init(){
				
		parent::init();		
			
			$this->_view->lang = $this->_lang;
			$this->adminLogin = Zend_Registry::get('adminLogin');
			$this->_view->userId = $this->adminLogin->userId;
			$this->_view->user_type= $this->adminLogin->type ;
			$this->sid = session_id();	
			$this->email_from = 'work@edit-place.com';
			$this->url = $this->_config->path->bo_base_path;
			$this->quote_documents_path=APP_PATH_ROOT.$this->_config->path->quote_documents;
			$this->mission_documents_path=APP_PATH_ROOT.$this->_config->path->mission_documents;
			$this->_view->fo_path=$this->fo_path=$this->_config->path->fo_path;
			$this->_view->fo_root_path=$this->fo_root_path=$this->_config->path->fo_root_path;
				
				$this->_view->product=$this->product_array=array(
										"redaction"=>"R&eacute;daction",
										"translation"=>"Traduction",
										"autre"=>"Autre",
										"seo_audit"=>"SEO audit",
										"smo_audit"=>"SMO audit",
										"proofreading"=>"Correction",
										"content_strategy"=>"Content strategy"
										);
				//'redaction','translation','autre','seo_audit','smo_audit','proofreading','content_strategy'
				//'article_seo','descriptif_produit','article_de_blog','news','guide','autre','analyse_content_seo','wordings'
				
				$this->_view->producttype_array=$this->producttype_array=array(
				                        "article_seo"=>"Article SEO",
										"descriptif_produit"=>"Desc.Produit",
										"article_de_blog"=>"Article de blog",
										"news"=>"News",
										"guide"=>"Guide",
										"autre"=>"Autres"
										);

			   $this->status_array=array(
										"auto_skipped"=>"Auto skipped",
										"skipped"=>"Skipped",
										"challenged"=>"Challenged",
										"not_done"=>"Not reviewed",
										"validated"=>"Validated",
										"closed"=>"Closed"
										);        

				$this->closedreason = array(
										"too_expensive"=>'Trop cher',
										"no_reason_client"=>'Pas de r&eacute;ponse du client',
										"quote_permanently_lost"=>'Devis d&#233;finitivement perdu',
										"project_cancelled"=>'Projet annul&eacute;',
										"delivery_time_long"=>'D&eacute;lai livraison trop long',
										"test_art_prob"=>'Probl&egrave;me article test',
										
									);
									
				$this->_view->tempo_duration_array=$this->duration_array=array(
									"days"=>"Jours",
									"week"=>"Semaine",
									"month"=>"Mois",
									"year"=>"An"
								);					
									
				$this->_view->duration_array=array(
									"days"=>"Jours"
								);	
				$this->_view->volume_option_array=$this->volume_option_array=array(
									"every"=>"Tous les",
									"within"=>"Sous"
								);	
								
				$this->_view->tempo_array=$this->tempo_array=array(
									"fix"=>"Fixe",
									"max"=>"Max"						
								);						
				
	}

/*Function to get the search missions by client and contract */
	public function missionListAction()
	{
		$contract_obj=new Ep_DeliveryDashboard_MissionList();
		
		$client_incontract=$contract_obj->getClintInContract();
		$clientsList=array();
			foreach($client_incontract as $client)
			{
				$clientsList[$client['clientId']]=$client['clientName'];
			}
			
		$quote_incontract=$contract_obj->getquotesInContract();
		$quoteList=array();
			foreach($quote_incontract as $quote)
			{
				$quoteList[$quote['QuoteId']]=$quote['QuoteTitle'];
			}
		
		
		$searchParameters=$this->_request->getParams();
		$client_id=$searchParameters['allclient'];
		$contract_id=$searchParameters['allcontract'];
			if($client_id && $contract_id){		
				$searchmissionObj=new Ep_DeliveryDashboard_MissionList();
				$resultProSeoMission=$searchmissionObj->serachProSeoMission($client_id,$contract_id);
				
					if($resultProSeoMission)
					{
						foreach($resultProSeoMission as $k=>$mission)
						{
							$resultProSeoMission[$k]['language_source_name']=$this->getCustomName("EP_LANGUAGES",$mission['language_source']);
							$resultProSeoMission[$k]['language_dest_name']=$this->getCustomName("EP_LANGUAGES",$mission['language_dest']);
						}
					}	
				$resultTechMission=$searchmissionObj->serachTechMission($client_id,$contract_id);
				
					if($resultTechMission)
					{
						foreach($resultTechMission as $k=>$mission)
						{
							$resultTechMission[$k]['language_source_name']=$this->getCustomName("EP_LANGUAGES",$mission['language']);
						}
					}	
								
				$this->_view->proSeo_list=$resultProSeoMission;
				$this->_view->tech_list=$resultTechMission;
			}
			
		if(!$client_id){
			$client_id=146174760359921;
		}
		
		$this->_view->client_id=$client_id;
	    $this->_view->contract_id=$contract_id;
		$this->_view->client_list=$clientsList;
		$this->_view->quote_list=$quoteList;
		$this->_view->render("mission-list-dashboard");
	}

/*Function to get the contract based on client*/
	public function clientQuoteAction()
	{
		if($client_id=$this->_request->getParam('clientId')){
			$clientContract_obj=new Ep_DeliveryDashboard_MissionList();	
			$searchParameters['client_id']=$client_id;
			$resultcontract=$clientContract_obj->getquotesInContract($searchParameters);
			$qselect='';      
				foreach($resultcontract as $res) 
				{
					$qselect.='<option value='.$res['QuoteId'].'>'.$res['QuoteTitle'].'</option>';
				} 
			echo $qselect;exit;
		}
	}
		
/* General function to get the name from XML file based on filetype */
	function getCustomName($type,$name)
	{
		$categories_array = $this->_arrayDb->loadArrayv2($type, $this->_lang);
		return $categories_array[$name];
	}
}
?>