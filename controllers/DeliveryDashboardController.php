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
										
				$this->_view->producttype_array=$this->producttype_array=array(
										"article_seo"=>"Article SEO",
										"descriptif_produit"=>"Desc.Produit",
										"article_de_blog"=>"Article de blog",
										"news"=>"News",
										"guide"=>"Guide",
										"autre"=>"Autres",
										"analyse_content_seo"=>"Analyse SEO content ",
										"wordings"=>"Wordings"
										);
					
			   $this->_view->tempo_duration_array=$this->tempo_duration_array=array(
										"days"=>"Jours",
										"week"=>"Semaine",
										"month"=>"Mois",
										"year"=>"An"
										);
								
			$this->_view->mission_status=$this->mission_status_array=array(
									"ongoing"=>"Ongoing",
									"validated"=>"Validated",
									"closed"=>"Closed",
									"deleted"=>"Deleted"
									);						
	}

/*Function to List missions based on Client and Contract*/
	public function missionListAction()
	{
		$contract_obj=new Ep_DeliveryDashboard_MissionList();
		/*Get Client list*/
		$client_incontract=$contract_obj->getClient();
		$clientsList=array();
			foreach($client_incontract as $client)
			{
				$clientsList[$client['clientId']]=$client['clientName'];
			}
		
		/*Get Contract list*/	
		$contract_list=$contract_obj->getContract($searchParameters);
		$quoteList=array();
			foreach($contract_list as $contract)
			{
				$quoteList[$contract['QuoteId']]=$contract['ContractTitle'];
			}
		
		$searchMissionParameters=$this->_request->getParams();
		$client_id=$searchMissionParameters['allclient'];
	    $contract_id=$searchMissionParameters['allcontract'];
		
			if($client_id && $contract_id)
			{	
				$searchmissionObj=new Ep_DeliveryDashboard_MissionList();
				/*Get Product and SEO missions*/
				$resultProSeoMission=$searchmissionObj->serachProSeoMission($searchMissionParameters);
		
					if($resultProSeoMission)
					{
						foreach($resultProSeoMission as $k=>$mission)
						{
							$resultProSeoMission[$k]['language_source_name']=$this->getCustomName("EP_LANGUAGES",$mission['language_source']);
							$resultProSeoMission[$k]['language_dest_name']=$this->getCustomName("EP_LANGUAGES",$mission['language_dest']);
						}
					}
				/*Get Tech missions*/
				$resultTechMission=$searchmissionObj->serachTechMission($searchMissionParameters);
				
					if($resultTechMission)
					{
						foreach($resultTechMission as $k=>$mission)
						{
							$resultTechMission[$k]['language_source_name']=$this->getCustomName("EP_LANGUAGES",$mission['language']);
							/*Get Tech missions Status*/
							$resultTechMission[$k]['tech_status']=$this->getTechStatus($mission['identifier']);
						}
						
					}	
								
				$this->_view->proSeo_list=$resultProSeoMission;
				$this->_view->tech_list=$resultTechMission;
				
				$searchParameters['client_id']=$client_id;
		        $contract_list=$searchmissionObj->getContract($searchParameters);
				
					$quoteList=array();
						foreach($contract_list as $contract)
						{
							$quoteList[$contract['QuoteId']]=$contract['ContractTitle'];
						}
			}
		
		$this->_view->client_id=$client_id;
	    $this->_view->contract_id=$contract_id;
		$this->_view->client_list=$clientsList;
		$this->_view->quote_list=$quoteList;
		$this->_view->render("mission-list-dashboard");
	}
	
/* Function to get Tech mission status*/	
	function getTechStatus($techmissionId)
	{
		$missionObj=new Ep_DeliveryDashboard_MissionList();
		$missionDetails=$missionObj->tracktechMission($techmissionId);
		return $missionDetails[0][missionStatus];
	}

/*Function to get the contract based on client*/
	public function clientQuoteAction()
	{
		if($client_id=$this->_request->getParam('clientId')){
			$clientContract_obj=new Ep_DeliveryDashboard_MissionList();	
			$searchParameters['client_id']=$client_id;
			/*Get contracts list*/
			$resultcontract=$clientContract_obj->getContract($searchParameters);
			$qselect='<option value=""></option>';      
				foreach($resultcontract as $res) 
				{
					$qselect.='<option value='.$res['QuoteId'].'>'.$res['ContractTitle'].'</option>';
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

/* Function to track Mission details*/
	function missionTrackingAction()
	{
		$searchParameters=$this->_request->getParams();
		$mission_id=$searchParameters['mission_id'];
		$type=$searchParameters['type'];
		$view['page']=$searchParameters['submenuId'];
		
			if($mission_id && $type=='proseo')
			{
				$missionObj=new Ep_DeliveryDashboard_MissionList();
			/*Trak Product and SEO mission Details*/
				$missionDetails=$missionObj->serachProSeoMission($searchParameters);
				
				if($missionDetails)
					{
						foreach($missionDetails as $k=>$mission)
						{
							$missionDetails[$k]['language_source_name']=$this->getCustomName("EP_LANGUAGES",$mission['language_source']);
							$missionDetails[$k]['language_dest_name']=$this->getCustomName("EP_LANGUAGES",$mission['language_dest']);
						}
					}
			
			$cmid=$missionDetails[0]['cmid'];
			$missionType=$missionDetails[0]['missionType'];
			
			/*Calculate % of prod mission Completed*/
				if($missionType=='prod')
				{
					$view['volume'] = $missionDetails[0]['volume'];
					$deliveryObj=new Ep_DeliveryDashboard_MissionList();
						
						/* Getting count of unpublished articles */
							$total_details = $deliveryObj->getProd(array('cmid'=>$cmid));
							
						/* Getting count of published articles */
							$published_details = $deliveryObj->getProd(array('cmid'=>$cmid),true);
							$view['total_articles'] = $total_details[0]['total_art'];
							$view['total_price'] = $total_details[0]['total_price'];
								if($published_details)
								{
									$view['published_articles'] = $published_details[0]['total_art'];
								}
								else
								{
									$view['published_articles'] = 0;
								}
									
							if($view['published_articles']>$view['volume'])
								
							$view['published_articles'] = $view['volume'];
						
							$view['percentage'] = round(($view['published_articles']/$view['volume'])*100);
							
							
					/*Calculate % of writing Completed*/
                  			
						$writing_details= $deliveryObj->getwriting($mission_id);
					
						$view['writing'] =$writing_details[0]['total_art_write'];
					
						if($view['writing'])
						{
							$view['writing_percentage'] =round(($view['writing']/$view['volume'])*100);
						}
						else
						{
							$view['writing']=0;
							$view['writing_percentage'] =0;
						}
					
					/*Calculate % of proofreading Completed*/					
						$proofreading_external= $deliveryObj->getproofreading($mission_id);
						$proofreading_internal = $deliveryObj->getproofreading($mission_id,true);
						$count_external=$proofreading_external[0]['total_art_proofreading'];
						$count_internal=$proofreading_internal[0]['total_art_proofreading'];
						//$view['proofreading'] =$proofreading_external[0]['total_art_proofreading'];
						$view['proofreading'] =$count_external+$count_internal;
					
						if($view['proofreading'])
						{
							$view['proofreading_percentage'] =round(($view['proofreading']/$view['volume'])*100);
						}
						else
						{
							$view['proofreading']=0;
							$view['proofreading_percentage'] =0;
						}

					/*Calculate total number of words*/
						$view['wordscount_writing'] = $view['writing']*$missionDetails[0]['nb_words'];
						$view['wordscount_proofreading'] = $view['proofreading']*$missionDetails[0]['nb_words'];
						$view['wordscount_validation'] = $view['published_articles']*$missionDetails[0]['nb_words'];
					
					/*Get all writers wrt mission*/
						$writers_details= $deliveryObj->getwriters($cmid);
						$this->_view->writers_details=$writers_details;
						
					/*Get published articles wrt mission*/
						$article_details= $deliveryObj->getwriters($cmid,true);
						$this->_view->article_details=$article_details;
				}
				
			/*Calculate % of seo mission Completed*/
				if($missionType=='seo')
				{
					$seoObj=new Ep_DeliveryDashboard_MissionList();
					$seo_taskDetails= $seoObj->getTasks($cmid);
					$totlSeo_task=count($seo_taskDetails);
					
					$seoStatus=$missionDetails[0]['missionStatus'];
						if($seoStatus =='validated')
						{
							$view['percentage']=100;
						}	
				}
			}
			
		if($mission_id && $type=='tech')
		{
			$taskObj=new Ep_DeliveryDashboard_MissionList();
		/*Trak Tech mission Details*/
			$missionDetails=$taskObj->tracktechMission($mission_id);
			$cmid=$missionDetails[0]['cmid'];
				if($missionDetails)
					{
						foreach($missionDetails as $k=>$mission)
						{
							$missionDetails[$k]['language_source_name']=$this->getCustomName("EP_LANGUAGES",$mission['language']);
						}
					}
					
			/*Calculate % of tech mission Completed*/
				$tech_taskDetails= $taskObj->getTasks($cmid);
				$totlTech_task=count($tech_taskDetails);
				$techTask_done=0;
					foreach($tech_taskDetails as $k=>$techTask)
						{
							if($techTask['m_status'])
							{
								$techTask_done+=1;
							}	
						}
				$techmissionStatus=$missionDetails[0]['missionStatus'];
					if($techmissionStatus =='validated')
					{
						$view['percentage']=100;
					}
					else
					{
						$view['percentage'] = round(($techTask_done/$totlTech_task)*100);
					}
			}
			$articleObj=new Ep_DeliveryDashboard_MissionList();
			$article_details= $articleObj->getarticles($mission_id);
			$num_publishedarticles=count($article_details);
			$view['num_publishedarticles'] =$num_publishedarticles;
			
				$this->_view->view=$view;
				$this->_view->mission_details=$missionDetails;
				$this->_view->render("mission-tracking");
	}

/* Function Get content of Published Articles*/
	function articleContecntAction()
	{ 
		$mission_id=$this->_request->getParam('missionid');
		$i=$this->_request->getParam('articleNumber')-1;
			
		$articleObj=new Ep_DeliveryDashboard_MissionList();
		$article_details= $articleObj->getarticles($mission_id);
		$num_publishedarticles=count($article_details);
			
		$fopath=$this->fo_root_path;
		
		$article_path=$fopath.'articles/'.$article_details[$i]['articlePath'];
		$article_folder=$article_details[$i]['articleid'];
		$article_name=$article_details[$i]['articleName'];
		
		//$article_folder='120083935257578';
		//$article_name='118131542878088_110722102141889_95407.xls';
		//$article_name='rose_dailytask.xlsx';
		
			//$article_path=$fopath.'articles/120083935257578/120083935257578_110722102141889_59454/helloWorld2.doc';
			//$article_path=$fopath.'articles/120083935257578/120083935257578_110722102141889_59454/helloWorld.docx';
			//$article_path=$fopath.'articles/120083935257578/120083935257578_110722102141889_59454/demoxl3.xls';
			//$article_path=$fopath.'articles/120083935257578/120083935257578_110722102141889_59454/demoxl4.xlsx';
			//$article_path=$fopath.'articles/120083935257578/120083935257578_110722102141889_37834.zip';
			//$article_path=$fopath.'articles/120083935257578/rose_dailytask.rar';
			//$article_path=$fopath.'articles/203113318514965/203113318514965_111202081601320_12586.pdf';
			//echo phpinfo();
			
			if (!file_exists($article_path))
			{
				echo "File Not Exist";
			}
			else
			{
				$path=pathinfo($article_path);
				$extnsn= $path['extension'];
				
					if($extnsn=='zip' || $extnsn=='rar' )
					{
		
						if($extnsn=='zip')
						{
						$zip = new ZipArchive;
							if ($zip->open($article_path) === TRUE) 
							{
								$zip->extractTo($fopath.'articles/'.$article_folder.'/'.$path['filename']);
								$zip->close();
							}
						}
						if($extnsn=='rar')
						{//Call to undefined function rar_open() in
							$rar_file = rar_open($article_path);
							$list = rar_list($rar_file);
								foreach($list as $file) {
									$entry = rar_entry_get($rar_file, $file);
									$entry->extract("."); // extract to the current dir
								}
							rar_close($rar_file);
							
						}
						$article_path=$fopath.'articles/'.$article_folder.'/'.$path['filename'].'/'.$article_name;
						
							$content=new getarticlecontent($article_path);
							$contentDetails=$content->getContent();
							echo $contentDetails;
 					}
					
					elseif($extnsn=='pdf')
					{
						$url=urlencode('http://ep-test.edit-place.com/FO/articles/203113318514965/203113318514965_111202081601320_12586.pdf');
						echo "<iframe src='http://docs.google.com/viewer?embedded=true&url=$url' width='100%' height='200%' frameborder='0'></iframe>";
					}
					
					else
					{
						$content=new getarticlecontent($article_path);
						$contentDetails=$content->getContent();
						echo $contentDetails;
					}
					
			}
	}
	
	public function articledownloadAction(){
	
		$mission_id=$this->_request->getParam('missionId');
		$downloadObj=new Ep_DeliveryDashboard_MissionList();
		$result= $downloadObj->getarticles($mission_id);
		
		$fopath=$this->fo_root_path;
	
			if($result){
				$file_array = array();
					for ($i= 0; $i < count($result); $i++) {
						$file_array[] = $fopath.'articles/'.$result[$i]['articlePath'];
					}
				$downloadfilename = ASSETS.'temp_zip/'.$mission_id.'_Articles.zip';
				$zip = $this->create_zip($file_array, $downloadfilename);
				
				if($zip) { 
					$this->_redirect("/BO/download-files.php?function=downloadFile&fullPath=$downloadfilename");
					exit;
				}
			}
			else{
				echo "No Files";
			}
    }
  
    public function create_zip($files = array(), $destination = '', $overwrite = true)
    {
		if (file_exists($destination) && !$overwrite) 
		{
            return false;
        }
        $valid_files = array();
			if (is_array($files))
			{
				foreach ($files as $file) 
				{
					if (file_exists($file)) 
					{
						$valid_files[] = $file;	
					}
				}
			}

        if (count($valid_files)) 
		{
            $zip = new ZipArchive();
			
			$res = $zip->open($destination, ZipArchive::CREATE); 
				if ($res === TRUE) 
				{
					foreach ($valid_files as $file) 
					{
						if (file_exists($file))  
						{ 
						$zip->addFile($file, basename($file)); 
						}
						
					}
			
					$zip->close();
		
					return file_exists($destination);
				} 
				else 
				{
					return false;
				}
        } 
		else 
		{
            return false;
        }
    }

}
?>