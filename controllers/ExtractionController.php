<?php
/**
 * @package Controller
 * @version 1
 */



class ExtractionController extends Zend_Controller_Action
 {

 	var $mail_from = "work@edit-place.com";
	var $work_mail_from = "work@edit-place.com";
	protected $_arrayDb;
	public function init()
	{
	    parent::init();
	     $this->product_array=array(
    							"redaction"=>"R&eacute;daction",
								"translation"=>"Traduction",
								"autre"=>"Autre",
								"proofreading"=>"Correction"
        						);
		$this->producttype_array=array(
    							"article_de_blog"=>"Article de blog",
								"descriptif_produit"=>"Desc.Produit",
								"article_seo"=>"Article SEO",
								"guide"=>"Guide",
								"news"=>"News",
								"wordings"=>"Wording",
								"autre"=>"Autres"
        						);
		 $this->seo_product_array=array(
        						"seo_audit"=>"SEO audit",
        						"smo_audit"=>"SMO audit",
    							"redaction"=>"R&eacute;daction",
								"translation"=>"Traduction",
								"proofreading"=>"Correction",
								"autre"=>"Autre"
        						);
       $this->closedreason = array(
								"too_expensive"=>'Trop cher',
								"no_reason_client"=>'Pas de r&eacute;ponse du client',
								"project_cancelled"=>'Projet annul&eacute;',
								"delivery_time_long"=>'D&eacute;lai livraison trop long',
								"test_art_prob"=>'Probl&egrave;me article test',
								"quote_permanently_lost"=>'Devis d&eacute;finitivement perdu'
							);
		$this->_view->tempo_duration_array=$this->duration_array=array(
						"days"=>"jours",
						"week"=>"semaine",
						"month"=>"mois",
						"year"=>"an"
					);	
		$this->_view->tempo_array=$this->tempo_array=array(
							"max"=>"Max",
							"fix"=>"Fixe"
						);	
		$this->_view->duration_array=array(
							"days"=>"Jours"
						);	
		$this->_view->volume_option_array=$this->volume_option_array=array(
							"every"=>"tous les",
							"within"=>"sous"
						);						
		$this->_arrayDb = Zend_Registry::get('_arrayDb');
		$this->month_days=array( "01"=>"31", "02"=>"28", "03"=>"31", "04"=>"30", "05"=>"31", "06"=>"30", "07"=>"31", "08"=>"31", "09"=>"30","10"=>"31", "11"=>"30", "12"=>"31" );
		$this->_view->month_array_val=$this->month_array_val=array("01"=> ucfirst(strftime("%b", mktime(0, 0, 0, '01'))), "02"=>ucfirst(strftime("%b", mktime(0, 0, 0, '02'))),
			"03"=>ucfirst(strftime("%b", mktime(0, 0, 0, '03'))),"04"=> ucfirst(strftime("%b", mktime(0, 0, 0, '04'))),"05"=>ucfirst(strftime("%b", mktime(0, 0, 0, '05'))),
			"06"=> ucfirst(strftime("%b", mktime(0, 0, 0, '06'))),"07"=> ucfirst(strftime("%b", mktime(0, 0, 0, '07'))),"08"=> ucfirst(strftime("%b", mktime(0, 0, 0, '08'))),
			  "09"=> ucfirst(strftime("%b", mktime(0, 0, 0, '09'))),"10"=>ucfirst(strftime("%b", mktime(0, 0, 0, '10'))), 
			  "11"=>ucfirst(strftime("%b", mktime(0, 0, 0, '11'))),"12"=> ucfirst(strftime("%b", mktime(0, 0, 0, '12'))) );
	}
 

 	function getCustomName($type,$name)
	{
		$categories_array = $this->_arrayDb->loadArrayv2($type,  'fr');
		return $categories_array[$name];
	}
	public function getLanguageName($lang_value)
    {
        $language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES",  'fr');
        return $language_array[$lang_value];
    }



    /*proofreading Theorical cost only for signed quote*/
	function proofreadingTheoicalCostoldAction()
	{
		$request = $this->_request->getParams();
		if($request['year'])
			$search['year']=$request['year'];
		else
			$search['year']=date('Y');

		$quoteextract_obj = new Ep_Quote_Extract();
		$proofmissionDetails=$quoteextract_obj->getProofreadingCost($search);
		$language = array();
		//echo "<pre>"; print_r($proofmissionDetails); exit;

		$Html='<table border=1>
							<tr>	
								<th></th>';
								foreach ($this->month_array_val as $key => $value) {
									$Html .='<th>'.$value.' '.$search['year'].'</th>';
								}
								
					$Html .='</tr>';
			
			foreach($proofmissionDetails as $prodmission)			
			{
								
								
				foreach($this->month_array_val as $key => $value)
				{	
					if($search['year']==$prodmission['year'])
					{
						$yearmonth=$search['year'].'-'.$key;
							
								$language[$prodmission['language']][$yearmonth] +=$prodmission['cost'];
							

						$language[$prodmission['language']]['currency'] =$prodmission['currency'];
						$language[$prodmission['language']]['language']=$this->getLanguageName($prodmission['language_source']);
					}
					
				}

				
			}

			foreach($language as $displaylang)
			{
				
				
					$Html .="<tr>";
					$Html .="<td>".$displaylang['language']."</td>";
					foreach($this->month_array_val as $month => $val)
					{
						$showyearmonth=$search['year'].'-'.$month;
						$Html .="<td>".$displaylang[$showyearmonth]." &".$displaylang['currency'].";</td>";
					}

					$Html .="</tr>";
				

			}
		$Html .="</table>";
		echo $Html;
		
		

	}

	/*proofreading Theorical cost only for signed quote*/
	function proofreadingTheoicalCostAction()
	{
		$request = $this->_request->getParams();
		if($request['year'])
			$search['year']=$request['year'];
		else
			$search['year']=date('Y');

		$quoteextract_obj = new Ep_Quote_Extract();
		$proofmissionDetails=$quoteextract_obj->getProofreadingCost($search);
		$language = array();
		//echo "<pre>"; print_r($proofmissionDetails); exit;

		$Html='<table border=1>
							<tr>	
								<th></th>';
								foreach ($this->month_array_val as $key => $value) {
									$Html .='<th><b>'.$value.' '.$search['year'].'</b></th>';
								}
								
					$Html .='<th>language source</th><th>language desitnation</th></tr>';
			
			foreach($proofmissionDetails as $prodmission)			
			{
								
								
				foreach($this->month_array_val as $key => $value)
				{	
					$yearmonth=$search['year'].'-'.$key;
					$prodmission['expected_end_date']=date('Y-m-d', strtotime($prodmission['expected_launch_date']. ' + '.$prodmission['mission_length'].' days'));
					//echo $prodmission['expected_end_date'];
					if($search['year']==$prodmission['year'])
					{
												

						if($prodmission['mission_product']=='translation')
						{
							$language[$prodmission['language_source'].'-'.$prodmission['language_dest']]['languageexact']=$this->getLanguageName($prodmission['language_source']).' -> '.$this->getLanguageName($prodmission['language_dest']).' (translation)';	
							$language[$prodmission['language_source'].'-'.$prodmission['language_dest']]['currency'] =$prodmission['sales_suggested_currency'];
								if(date("mY",strtotime($prodmission['expected_launch_date'])) == date("mY",strtotime($prodmission['expected_end_date'])) && $prodmission['yearmonth']==$yearmonth)
								{
									$language[$prodmission['language_source'].'-'.$prodmission['language_dest']][$prodmission['yearmonth']] +=$prodmission['totalprice'];
								}
								else
								{

									if(date("Y-m",strtotime($prodmission['expected_launch_date']))==$yearmonth )
									{
										
										$language[$prodmission['language_source'].'-'.$prodmission['language_dest']][$yearmonth] +=($prodmission['totalprice']/$prodmission['mission_length'])*($this->month_days[$key]-date("d",strtotime($prodmission['expected_launch_date'])));	
									}
									elseif(date("Y-m",strtotime($prodmission['expected_end_date']))==$yearmonth)
									{
										
										$language[$prodmission['language_source'].'-'.$prodmission['language_dest']][$yearmonth] +=($prodmission['totalprice']/$prodmission['mission_length'])*(date("d",strtotime($prodmission['expected_end_date'])));	
									}
									else
									{
										if($yearmonth>=$prodmission['yearmonth'] && date("Y-m",strtotime($prodmission['expected_end_date']))>=$yearmonth)
											$language[$prodmission['language_source'].'-'.$prodmission['language_dest']][$yearmonth] +=($prodmission['totalprice']/$prodmission['mission_length'])*($this->month_days[$key]);	
										
									}
								}
						}
						else
						{
							$language[$prodmission['language_source']]['languageexact']=$this->getLanguageName($prodmission['language_source']).' (writing)';
							$language[$prodmission['language_source']]['currency'] =$prodmission['sales_suggested_currency'];

								if(date("mY",strtotime($prodmission['expected_launch_date'])) == date("mY",strtotime($prodmission['expected_end_date'])) && $prodmission['yearmonth']==$yearmonth )
								{
								$language[$prodmission['language_source']][$prodmission['yearmonth']] +=$prodmission['totalprice'];
								}
								else
								{
									if(date("Y-m",strtotime($prodmission['expected_launch_date']))==$yearmonth)
									{
										
										$language[$prodmission['language_source']][$yearmonth] +=($prodmission['totalprice']/$prodmission['mission_length'])*($this->month_days[$key]-date("d",strtotime($prodmission['expected_launch_date'])));	
									}
									elseif(date("Y-m",strtotime($prodmission['expected_end_date']))==$yearmonth)
									{
										
										$language[$prodmission['language_source']][$yearmonth] +=($prodmission['totalprice']/$prodmission['mission_length'])*(date("d",strtotime($prodmission['expected_end_date'])));	
									}
									else
									{
										if($yearmonth>=$prodmission['yearmonth'] && date("Y-m",strtotime($prodmission['expected_end_date']))>=$yearmonth)
											$language[$prodmission['language_source']][$yearmonth] +=($prodmission['totalprice']/$prodmission['mission_length'])*($this->month_days[$key]);	

									}
								}
						}
							
								
				
					}
					
				}

				
			}
			//echo "<pre>"; print_r($language); exit;
			foreach($language as $lan=>$displaylang)
			{
				
				
					$Html .="<tr>";
					$Html .="<td><b>".$displaylang['languageexact']."</b></td>";
					foreach($this->month_array_val as $month => $val)
					{
						$showyearmonth=$search['year'].'-'.$month;
						if(zero_cut($displaylang[$showyearmonth])!='0,00')
							$Html .="<td>".zero_cut($displaylang[$showyearmonth])." &".$displaylang['currency'].";</td>";
						else
							$Html .="<td></td>";

					}
					$sortlang=explode('-',$lan);
					$Html .="<td><b>".$sortlang[0]."</b></td>";
					if($sortlang[1])
						$Html .="<td><b>".$sortlang[1]."</b></td>";
					else
						$Html .="<td><b>".$sortlang[0]."</b></td>";	

					$Html .="</tr>";
				

			}
		$Html .="</table>";
		//echo $Html; exit;
		/*generate and download file*/
		$proofreadingtheorical=time()."-proofreadingtheorical.xlsx";
		$comparepath=$_SERVER['DOCUMENT_ROOT']."/BO/turnover-report/$proofreadingtheorical";
					if($Html!="")
					{
						
						chmod($comparepath,0777);
						convertHtmltableToXlsx($Html,$comparepath,True);
						$this->_redirect("/BO/download-turnover-report.php?type=turnover&filename=$proofreadingtheorical");
					}
		

	}


 }


 ?>