<?php

class EP_Delivery_Article extends Ep_Db_Identifier
{
	protected $_name = 'Article';
	
    public function insertArticle($did,$step2)
	{	//print_r($step2);
		$count=count($step2['art_title']);
        $cor_counter=1; 
		for($p=0;$p<$count;$p++)
		{ 
			//Insert Article
			$Aarray = array(); 
			$Aarray["id"]=$this->createIdentifier(); 
			//$Aarray["id"]=$this->getIdentifier(); 
			$Aarray["delivery_id"] = $did; 
			$Aarray["title"] = $step2['art_title'][$p]; 
			$Aarray["language"] = $step2['step1language']; 
			$Aarray["category"] = $step2['category'][$p]; 
			$Aarray["type"] = $step2['type'][$p];  
			$Aarray["sign_type"] = $step2['signtype'][$p]; 
			$Aarray["view_to"]=$step2['view_to'];
			
			$step2['num_min'][$p]=str_replace(",",".",$step2['num_min'][$p]);
			$Aarray["num_min"] = $step2['num_min'][$p]; 
			
			$step2['num_max'][$p]=str_replace(",",".",$step2['num_max'][$p]);
			$Aarray["num_max"] = $step2['num_max'][$p];  
			
			if($step2['price_min'][$p]!="")
			{
				$step2['price_min'][$p]=str_replace(",",".",$step2['price_min'][$p]);
				$Aarray["price_min"] = $step2['price_min'][$p];  
			}
			
			if($step2['price_max'][$p]!="")
			{
				$step2['price_max'][$p]=str_replace(",",".",$step2['price_max'][$p]);
				$Aarray["price_max"] = $step2['price_max'][$p];  
				$Aarray["price_final"] = $step2['price_max'][$p]; 
			}
						
			$Aarray["status"] = "new";  
			$Aarray["created_by"]='BO';
			//if(count($step2['publish_language'])>0)
				//$Aarray["publish_language"]=implode(",",$step2['publish_language']);
			
			$Aarray["publish_language"]=implode(",",$step2['language'][$p]);
			
			$step2['contrib_percentage'][$p]=str_replace(",",".",$step2['contrib_percentage'][$p]);
			$Aarray["contrib_percentage"]=$step2['contrib_percentage'][$p];
			
			//Contributors list
			
			if($step2['AOtype']=="private")
			{
			  if($step2['missiontest']=="on")
				$Aarray["contribs_list"]=implode(",",$step2["favcontribcheck"][$p]);
			  else
				$Aarray["contribs_list"]=$step2["favcontribchecklist"][$p];
			  
			}
            if($step2["wl_kws"][$p]!="")
				$Aarray["wl_kws"] = mysql_escape_string(utf8_decode($step2["wl_kws"][$p])) ;
            
			if($step2["bl_kws"][$p]!="")
				$Aarray["bl_kws"] = mysql_escape_string(utf8_decode($step2["bl_kws"][$p])) ;
                          
			//Priority contribs
			if($step2['linkpoll']=="on")
			  $Aarray["priority_contributors"]=implode(",",$step2['priorcontrib']);
				
			//Correction
			if($step2['correction_'.$cor_counter]=="on" && $step2['missiontest']!="on")
			{
				$Aarray["correction"]="yes";
                $Aarray["correction_type"]='extern';
                $Aarray["correction_cost"]='';
				
				$Aarray["correction_pricemin"]=$step2['correction_pricemin'][$p];
				$Aarray["correction_pricemax"]=$step2['correction_pricemax'][$p];
				$Aarray["correction_participation"]=$step2['correction_participation'];
				
				$Aarray["correction_jc_resubmission"]=$step2['correction_jc_resubmission'];
				$Aarray["correction_sc_resubmission"] = $step2['correction_sc_resubmission'];
				$Aarray["correction_resubmit_option"] = $step2['correction_resubmit_option'];
				
				$Aarray["correction_jc_submission"] = $step2['correction_jc_submission'];
				$Aarray["correction_sc_submission"] = $step2['correction_sc_submission'];
				$Aarray["correction_submit_option"] = $step2['correction_submit_option'];
				//if(count($step2["corrector_privatelist"])>0)
					//$Aarray["corrector_privatelist"]=implode(",",$step2["corrector_privatelist"]);
				if(count($step2["correctorcheck"][$p])>0)	
					$Aarray["corrector_privatelist"]=implode(",",$step2['correctorcheck'][$p]); 
				$Aarray["nomoderation"]=$step2['nomoderation'];
				
				$Aarray["correction_participationexpires"]=$step2['correction_participationexpires'];
			}
           
		   if($step2['missiontest']=="on")
            { 
                $Aarray["correction"]="yes";
                $Aarray["correction_type"]='extern';
                $Aarray["correction_cost"]='';
				
				$Aarray["correction_pricemin"]=$step2['correction_pricemin'][$p];
				$Aarray["correction_pricemax"]=$step2['correction_pricemax'][$p];
				$Aarray["correction_participation"]=$step2['correction_participation'];
				
				$Aarray["correction_jc_resubmission"]=$step2['correction_jc_resubmission'];
				$Aarray["correction_sc_resubmission"] = $step2['correction_sc_resubmission'];
				$Aarray["correction_resubmit_option"] = $step2['correction_resubmit_option'];
				
				//Submit time
				if($step2['correction_submit_option'][$p]=='min')
					$subcorr_multiple = 1;
				elseif($step2['correction_submit_option'][$p]=='hour')
					$subcorr_multiple = 60;
				elseif($step2['correction_submit_option'][$p]=='day')
					$subcorr_multiple = 60*24;
				
				$Aarray["correction_jc_submission"] = $step2['correction_jc_submission'][$p]*$subcorr_multiple;
                $Aarray["correction_sc_submission"] = $step2['correction_sc_submission'][$p]*$subcorr_multiple;
                $Aarray["correction_submit_option"] = $step2['correction_submit_option'][$p];
                 if(count($step2["correctorcheck"][$p])>0)
                        $Aarray["corrector_privatelist"]=implode(",",$step2["correctorcheck"][$p]);
                
            }
			if($step2['correction_'.$cor_counter]=="intern"  || $step2['correction_'.$cor_counter]=="freelance")
			{
				$Aarray["correction"]="no";
				$Aarray["correction_type"]=$step2['correction_'.$cor_counter];
				$Aarray["correction_cost"]=$step2['correction_cost'][$p];
			}
			
			$Aarray["participation_expires"]=$step2["participation_expires"];
			
			//Submit time
			if($step2['submit_option'][$p]=='min')
				$sub_multiple = 1;
			elseif($step2['submit_option'][$p]=='hour')
				$sub_multiple = 60;
			elseif($step2['submit_option'][$p]=='day')
				$sub_multiple = 60*24;
	
			$Aarray["submit_option"]=$step2['submit_option'][$p];
			$Aarray["junior_time"] = $step2['junior_time'][$p]*$sub_multiple;
			$Aarray["senior_time"] = $step2['senior_time'][$p]*$sub_multiple;
			$Aarray["subjunior_time"] = $step2['subjunior_time'][$p]*$sub_multiple;
				
			//Resubmit time
			if($step2['resubmit_option'][$p]=='min')
				$sub_multiple = 1;
			elseif($step2['resubmit_option'][$p]=='hour')
				$sub_multiple = 60;
			elseif($step2['resubmit_option'][$p]=='day')
				$sub_multiple = 60*24;
	
			$Aarray["resubmit_option"]=$step2['resubmit_option'][$p];
			$Aarray["jc_resubmission"] = $step2['jc_resubmission'][$p]*$sub_multiple;
			$Aarray["sc_resubmission"] = $step2['sc_resubmission'][$p]*$sub_multiple;
			$Aarray["jc0_resubmission"] = $step2['jc0_resubmission'][$p]*$sub_multiple;
			
			$Aarray["participation_time"] = $step2['participation_time'];
			$Aarray["estimated_worktime"] = ($step2['estimated_worktime']*60)+$step2['estimated_worktime_min'];
			if($step2['estimated_worktime_option'])
			$Aarray["estimated_workoption"] = $step2['estimated_worktime_option'];
			else
			$Aarray["estimated_workoption"] = 'min';
		
			//Sub titles
			if($step2['articletype']=='lot')
			{
				if(count($step2['subtitle'][$p])>0)
				{
					$Aarray["sub_title"]=implode("|@|",$step2['subtitle'][$p]);
				}
			}
			$Aarray["column_xls"]=$step2['column_xls'][$p];
			$Aarray["testrequired"]=$step2['testrequired'];
			if($step2['testrequired']=="yes")
				$Aarray["testmarks"]=$step2['testmarks'];
			
			$Aarray["product"]=$step2['product'];
			$Aarray["refusalreasons"]=$step2['refusalreasons'];
				
			//echo "<pre>";print_r($Aarray);exit;
            $cor_counter++;
			$this->insertQuery($Aarray);//exit;
		}//exit;
        //unset($_SESSION['white']) ;   unset($_SESSION['black']) ;
    }
	
	public function createIdentifier()
    {
        $d = new Date();
		return $d->getSubDate(5,14).mt_rand(100000,999999);
  	}
	
	public function updatePaidarticle($d_id,$invoiceId)
	{
		$Where3= "delivery_id=".$d_id;
		
		$Part3Array=array();
		$Part3Array['paid_status']='paid';
		$Part3Array['invoice_id']=$invoiceId;
		
		$this->updateQuery($Part3Array,$Where3);
	}
	/* *** optimized on 22.03.2016 *** */
    //////get all records for 1st stage in deliveries ///////////////////////////
    public function stageDeliveries($params)
    {
        if($params['search'] == 'search')
        {
            $condition = '';
            if($params['startdate'] !='' && $params['enddate']!='')
            {
                $start_date = str_replace('/','-',$params['startdate']);
                $end_date = str_replace('/','-',$params['enddate']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $condition.= " AND d.created_at BETWEEN '".$start_date."' AND DATE_ADD('".$end_date."', INTERVAL 1 DAY)";
            }
            if($params['aoId']!='0')
            {
                $condition.= " AND d.id =".$params['aoId']."";
            }
            if($params['inchargeId']!='0')
            {
                $condition.= " AND d.created_user =".$params['inchargeId']."";
            }
            if($params['clientId']!='0')
            {
                $condition.= " AND d.user_id =".$params['clientId']."";
            }
            if($params['closed']!='0')
            {
                if($params['closed'] != 'all')
                    $condition.= " AND d.id IN (".$params['searchaosarray'].")";
                else
                    $condition.= " ";
            }
        }
        if($params['loginUserType'] != 'superadmin')
        {
            if($params['profilelist'] == 'own')
               $condition.= " AND d.created_user='".$params['loginUserId']."'";
        }else{
            $condition.= " ";
        }
        if($params['closed']!='0' && $params['search'] == 'search')
        {
            $query = "select a.id AS artId, a.title,a.delivery_id, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords, d.created_at AS delcreatedat,
                 a.sign_type, d.title AS deliveryTitle, d.delivery_date, d.total_article, d.category AS del_category, d.user_id as client,up.first_name as incharge,
                 u.email, u.login, up.first_name, p.id as partId, p.created_at, ap.plag_percent,
                    (
                    select count(a1.id) AS stageCount from ".$this->_name." a1 INNER JOIN Delivery d1 ON d1.id=a1.delivery_id
                    INNER JOIN Participation p1 ON p1.article_id=a1.id WHERE d1.id=d.id AND p1.status IN ('under_study', 'plag_exec') AND p1.cycle=0 AND p1.current_stage= p.current_stage
                    ) AS atraiter,
                    (
                    select count(a2.id) AS validatedCount from ".$this->_name." a2 INNER JOIN Delivery d2 ON d2.id=a2.delivery_id
                    INNER JOIN Participation p2 ON p2.article_id=a2.id WHERE d2.id=d.id AND p2.status IN ('published', 'closed') AND cycle=0
                    ) AS traiter,
                    (
                        select COUNT(DISTINCT p3.id)  FROM Participation p3
                        INNER JOIN ArticleProcess ap3  ON ap3.participate_id=p3.id
                        INNER JOIN Article as a3 ON  p3.article_id = a3.id
                        INNER JOIN Delivery as d3 ON a3.delivery_id = d3.id
                                            WHERE p3.current_stage='stage0' AND p3.cycle=0 AND p3.status IN ('plag_exec') AND ap3.version IN (SELECT MAX(version) FROM ArticleProcess WHERE participate_id= p3.id)
                                            AND ap.plag_percent is NULL AND  d.id= d.id

                    )AS not_plagprocessed
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 INNER JOIN Participation p ON p.article_id=a.id
                 LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id
                 INNER JOIN User u ON u.identifier=d.created_user
                 INNER JOIN UserPlus up ON up.user_id=u.identifier
                 WHERE  d.premium_option!='0' AND d.premium_option IS NOT NULL
                 AND d.premium_option !='' ".$condition." AND ".$this->visibility." GROUP BY d.id";
        }
        else{
             $query = "select a.id AS artId, a.title,a.delivery_id, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords, d.created_at AS delcreatedat,
                 a.sign_type, d.title AS deliveryTitle, d.delivery_date, d.total_article, d.category AS del_category, d.user_id as client,up.first_name as incharge,
                 u.email, u.login, up.first_name, p.id as partId, p.created_at, ap.plag_percent,
                    (
                    select count(a1.id) AS stageCount from ".$this->_name." a1 INNER JOIN Delivery d1 ON d1.id=a1.delivery_id
                    INNER JOIN Participation p1 ON p1.article_id=a1.id WHERE d1.id=d.id AND p1.status IN ('under_study', 'plag_exec') AND p1.cycle=0 AND p1.current_stage= p.current_stage
                    ) AS atraiter,
                    (
                    select count(a2.id) AS validatedCount from ".$this->_name." a2 INNER JOIN Delivery d2 ON d2.id=a2.delivery_id
                    INNER JOIN Participation p2 ON p2.article_id=a2.id WHERE d2.id=d.id AND p2.status IN ('published', 'closed') AND cycle=0
                    ) AS traiter,
                    (
                        select COUNT(DISTINCT p3.id)  FROM Participation p3
                        INNER JOIN ArticleProcess ap3  ON ap3.participate_id=p3.id
                        INNER JOIN Article as a3 ON  p3.article_id = a3.id
                        INNER JOIN Delivery as d3 ON a3.delivery_id = d3.id
                                            WHERE p3.current_stage='stage0' AND p3.cycle=0 AND p3.status IN ('plag_exec') AND ap3.version IN (SELECT MAX(version) FROM ArticleProcess WHERE participate_id= p3.id)
                                            AND ap.plag_percent is NULL AND  d.id= d.id

                    )AS not_plagprocessed
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 INNER JOIN Participation p ON p.article_id=a.id
                 INNER JOIN ArticleProcess ap ON ap.participate_id = p.id
                 INNER JOIN User u ON u.identifier=d.created_user
                 INNER JOIN UserPlus up ON up.user_id=u.identifier
                 WHERE p.status IN ('under_study','plag_exec') AND p.cycle=0 AND d.premium_option!='0' AND d.premium_option IS NOT NULL
                 AND d.premium_option !='' ".$condition." AND ".$this->visibility." AND p.current_stage in ('".$params['stage']."') GROUP BY d.id";
        }
     /* $query = "select a.id AS artId, a.title,a.delivery_id, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords, d.created_at AS delcreatedat,
                 a.sign_type, d.title AS deliveryTitle, d.delivery_date, d.total_article, d.user_id as client,up.first_name as incharge,
                 u.email, u.login, up.first_name, p.id as partId, p.created_at, ap.plag_percent
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 INNER JOIN Participation p ON p.article_id=a.id
                 LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id
                 INNER JOIN User u ON u.identifier=d.created_user
                 INNER JOIN UserPlus up ON up.user_id=u.identifier
                 WHERE p.status IN ('under_study','plag_exec') AND d.premium_option!='0' AND d.premium_option IS NOT NULL
                 AND d.premium_option !='' ".$condition." AND (a.id in (select article_id from Participation where current_stage in ('".$params['stage']."'))) GROUP BY d.id";*/

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
          else
            return "NO";
    }
    //////get all records for 1st stage in articles ///////////////////////////
	public function stageArts($params)
	{
        if($params['loginUserType'] != 'superadmin')
        {
            if($params['profilelist'] == 'own')
               $condition = " d.id='".$params['aoId']."' AND d.created_user='".$params['loginUserId']."'";
            else
               $condition = " d.id='".$params['aoId']."' ";
        }else
        {
            $condition = " d.id='".$params['aoId']."'";
        }
        $query = "select a.id AS artId, a.title,a.delivery_id, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords, d.created_at AS delcreatedat,
                 a.sign_type, d.title AS deliveryTitle, d.delivery_date, d.user_id as owner,up.first_name as incharge, a.created_at,
                 u.identifier, u.email, u.login, l.lock_status, l.user_id AS lockedUser, u.profile_type, up.first_name, up.last_name, p.id as partId, p.created_at, ap.plag_percent,
                 a.delivered
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 INNER JOIN Participation p ON p.article_id=a.id
                 INNER JOIN ArticleProcess ap ON ap.participate_id = p.id
                 INNER JOIN User u ON u.identifier=p.user_id
                 INNER JOIN UserPlus up ON up.user_id=p.user_id
                 LEFT JOIN LockSystem l on a.id=l.article_id
                 WHERE  ".$condition." AND p.cycle=0 AND (a.id in (select article_id from Participation where current_stage in ('".$params['stage']."') AND cycle=0)) GROUP BY a.id";
		  if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		  else
			return "NO";
	}
    //////get all records for 2st stage ///////////////////////////
    public function stage2Arts($conditionParams)
    {
        if($conditionParams['loginUserType'] != 'superadmin')
        {
            if($conditionParams['profilelist'] == 'own')
            {
                $condition = " AND d.created_user='".$conditionParams['loginUserId']."'";
            }
        }else{
            $condition = '';
        }
         $this->adminLogin = Zend_Registry::get('adminLogin');
         $query = "select a.id AS artId, a.title,a.delivery_id, count(a.title) AS artCount, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords,
                 a.sign_type, d.title AS deliveryTitle, d.delivery_date, d.user_id as owner,up.first_name as incharge,
                 u.email, u.login, up.first_name, p.id as partId, p.created_at, ap.plag_percent
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 INNER JOIN Participation p ON p.article_id=a.id
                 LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id
                 INNER JOIN User u ON u.identifier=p.user_id
                 INNER JOIN UserPlus up ON up.user_id=p.user_id
                 WHERE p.status='under_study' AND d.premium_option!='0' AND d.premium_option IS NOT NULL
                 AND d.premium_option !='' ".$condition." AND (a.id in (select article_id from Participation where current_stage in ('stage2'))) GROUP BY a.id";
          if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
          else
            return "NO";
    }
    /////////get all records for 0th stage ///////////////////////////
    public function stage0Arts($where)
    {
        $this->adminLogin = Zend_Registry::get('adminLogin');
         $query = "select a.id AS artId, a.title,a.delivery_id, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords,
                 a.sign_type, d.title AS deliveryTitle, d.delivery_date, l.lock_status,d.user_id as owner,
                 l.user_id AS lockedUser, u.email, u.login, up.first_name, p.id as partId, p.created_at, ap.plag_percent
                 FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 INNER JOIN Participation p ON p.article_id=a.id
                 LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id
                 INNER JOIN User u ON u.identifier=p.user_id
                 INNER JOIN UserPlus up ON up.user_id=p.user_id
                 LEFT JOIN LockSystem l on a.id=l.article_id
                 WHERE p.status IN ('under_study', 'plag_exec') AND d.premium_option!='0' AND d.premium_option IS NOT NULL
                 AND d.premium_option !='' ".$where." AND (a.id in (select article_id from Participation where current_stage in ('stage0'))) GROUP BY a.id";
      if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /*edited by naseer on 03.12.2015*/
    /////////get aritcle and participation deatails w.r.t participation id  ///////////////////////////
	public function getArticleDetails($artId)
	{
	     $query = "select a.id, a.title, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords, a.sign_type, a.num_min, a.num_max, a.price_min, a.price_max, a.correction_pricemin, a.correction_pricemax, a.corrector_privatelist, a.contribs_list, a.progressbar_percent, a.participation_expires,
                    a.correction_closed_status, a.correction,a.client_validated,a.validated_by,a.refusalreasons, a.product, d.id AS deliveryId, d.title AS deliveryTitle, d.user_id AS clientId, d.filepath, d.created_user, d.missiontest,u.email, up.first_name, up.last_name, a.correction_jc_resubmission,a.correction_sc_resubmission,a.correction_participationexpires,
                    a.language_source,
                    a.sourcelang_nocheck,a.sourcelang_nocheck_correction,a.delivered,UNIX_TIMESTAMP(a.delivered_updated_at) as delivered_updated_at,
                    a.delivered,d.type
                    from ".$this->_name." a
		           INNER JOIN Delivery d ON a.delivery_id=d.id
                   LEFT JOIN User u ON u.identifier=d.created_user
                   LEFT JOIN UserPlus up ON up.user_id = u.identifier WHERE a.id=".$artId;//." where ".$whereQuery;
		//echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////get aritcle  deatails w.r.t ao id  ///////////////////////////
    public function getArticleDetailsWithAoid($params)
    {
        $aoId = $params['aoId'];
        if($params['status'] != '')
        {
            if($params['status'] == 'affect')
            {
               $condition = " INNER JOIN Participation p ON p.article_id=a.id WHERE p.status  IN ('bid', 'under_study','published','plag_exec', 'disapproved', 'disapproved_temp', 'disapproved_client' ) AND p.cycle = 0 AND d.id=".$aoId;
            }
            else if($params['status'] == 'encours')
            {
               $condition = " INNER JOIN Participation p ON p.article_id=a.id WHERE p.status IN ('bid_premium', 'bid_temp') AND p.cycle = 0 AND d.id=".$aoId;
            }
            else if($params['status'] == 'reaffect')
            {
              //  $condition = " LEFT JOIN Participation p ON p.article_id=a.id WHERE (a.id NOT IN (SELECT article_id FROM Participation) OR p.cycle != 0)AND d.id=".$aoId;
			    $condition = " LEFT JOIN Participation p ON p.article_id=a.id WHERE (a.id NOT IN (SELECT article_id FROM Participation Where status IN ('bid_premium', 'bid_temp','bid', 'under_study','published','plag_exec', 'disapproved', 'disapproved_temp', 'disapproved_client') and cycle=0 )) AND d.id='".$aoId."'";
            }
            $query = "select DISTINCT a.id AS artId, a.title AS artName, a.category, a.submitdate_bo, d.price_min, d.price_max, a.price_bo, a.type, a.nbwords, a.sign_type, a.num_min,a.bo_closed_status,
                    a.num_max, a.correction, a.participation_expires,a.correction_participationexpires, d.id AS deliveryId, d.title AS deliveryTitle, d.filepath, d.created_at, l.lock_status, l.user_id AS lockedUser from ".$this->_name." a
		           INNER JOIN Delivery d ON a.delivery_id=d.id
		           LEFT JOIN LockSystem l on a.id=l.article_id ".$condition;
        }
        elseif($params['crtstatus'] != '')
        {
            if($params['crtstatus'] == 'affect')
            {
                $condition = " INNER JOIN CorrectorParticipation cp ON cp.article_id=a.id WHERE cp.status  IN ('bid', 'under_study','published','plag_exec', 'disapproved', 'disapproved_temp', 'disapproved_client' ) AND cp.cycle = 0 AND d.id=".$aoId;
            }
            else if($params['crtstatus'] == 'encours')
            {
                $condition = " INNER JOIN CorrectorParticipation cp ON cp.article_id=a.id WHERE cp.status IN ('bid_corrector', 'bid_temp') AND cp.cycle = 0 AND d.id=".$aoId;

            }
            else if($params['crtstatus'] == 'reaffect')
            {
                //  $condition = " LEFT JOIN Participation p ON p.article_id=a.id WHERE (a.id NOT IN (SELECT article_id FROM Participation) OR p.cycle != 0)AND d.id=".$aoId;
                 $condition = " LEFT JOIN CorrectorParticipation cp ON cp.article_id=a.id WHERE (a.id NOT IN (SELECT article_id FROM CorrectorParticipation Where status IN ('bid_corrector', 'bid_temp','bid', 'under_study','published','plag_exec', 'disapproved', 'disapproved_temp', 'disapproved_client') and cycle=0 )) AND d.id='".$aoId."'";

            }
            $query = "select DISTINCT a.id AS artId, a.title AS artName, a.category, a.submitdate_bo, d.price_min, d.price_max, a.price_bo, a.type, a.nbwords, a.sign_type, a.num_min,a.bo_closed_status,
                    a.num_max, a.correction, a.participation_expires,a.correction_participationexpires, d.id AS deliveryId, d.title AS deliveryTitle, d.filepath, d.created_at, l.lock_status, l.user_id AS lockedUser from ".$this->_name." a
		           INNER JOIN Delivery d ON a.delivery_id=d.id
		           LEFT JOIN LockSystem l on a.id=l.article_id ".$condition;
        }
        else
        {
            $query = "select DISTINCT a.id AS artId, a.title AS artName, a.category, a.submitdate_bo, d.price_min, d.price_max, a.price_bo, a.type, a.nbwords, a.sign_type, a.num_min,a.bo_closed_status,
                    a.num_max, a.correction, a.participation_expires,a.correction_participationexpires, d.id AS deliveryId, d.title AS deliveryTitle, d.filepath, d.created_at, l.lock_status, l.user_id AS lockedUser from ".$this->_name." a
		           INNER JOIN Delivery d ON a.delivery_id=d.id
                   LEFT JOIN LockSystem l on a.id=l.article_id WHERE d.id=".$aoId;
        }


        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get aritcle list ///////////////////////////
	public function getArticleList()
	{
	    $query = "select id, title from ".$this->_name;
		//echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    ///get paid article count in the ao///
    public function getPaidArticleCount($aoId)
    {
        $query="SELECT count(id) AS paidartcount FROM ".$this->_name." WHERE delivery_id = '".$aoId."' AND paid_status = 'paid'  ";
       // echo $query;
        $result = $this->getQuery($query,true);
        return $result;
    }
    /////////get aritcles count in respective stage in delivery///////////////////////////
    public function getArticleCountStage($aoId, $stage)
    {
        $query = "select count(a.id) AS stageCount from ".$this->_name." a INNER JOIN Delivery d ON d.id=a.delivery_id
                INNER JOIN Participation p ON p.article_id=a.id WHERE d.id='".$aoId."' AND p.status IN ('under_study', 'plag_exec') AND p.cycle=0 AND p.current_stage='".$stage."'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get aritcles count in respective stage in delivery ///////////////////////////
    public function getValidatedArticleCount($aoId)
    {
        $query = "select count(a.id) AS validatedCount from ".$this->_name." a INNER JOIN Delivery d ON d.id=a.delivery_id
                INNER JOIN Participation p ON p.article_id=a.id WHERE d.id='".$aoId."' AND p.status IN ('published', 'closed') AND cycle=0";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }


    /////////get aritcles a traiter(to be corrected) count of all deliveries///////////////////////////
    public function getToBeCorrected($stage)
    {
        $query = "select count(a.id) AS totalstageCount from ".$this->_name." a INNER JOIN Delivery d ON d.id=a.delivery_id
                INNER JOIN Participation p ON p.article_id=a.id WHERE p.status IN ('under_study', 'plag_exec') AND p.cycle=0 AND p.current_stage='".$stage."'";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get aritcle details for plagiarism performed when cron run and send to jullien in xls  ///////////////////////////
    public function getPlagResultDetails($artId)
    {
        $query = "select a.id AS artId, a.title,a.delivery_id, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords, a.sign_type,
                   d.title AS deliveryTitle, d.delivery_date, d.user_id as owner, u.email, u.login, up.first_name, up.last_name, p.id as partId, p.created_at
                  FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                  INNER JOIN Participation p ON p.article_id=a.id
                  INNER JOIN User u ON u.identifier=p.user_id
                  INNER JOIN UserPlus up ON up.user_id=p.user_id
                  WHERE a.id = ".$artId;
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ////////////udate the articles table//////////////////////
    public function updateArticle($data,$query)
    {
      //echo $query;
      //print_r($data);exit;
      $data['updated_at']=date("Y-m-d H:i:s", time());
       $this->updateQuery($data,$query);

    }
     ////insert the article when missiom is splited////
    public function insertSplitArticles($data)
    {
        $this->insertQuery($data);
    }
    /////////get all articles which are rejected by clients after approved in stage 2 ///////////////////////////
    public function clientRejectedArts()
    {
        $this->adminLogin = Zend_Registry::get('adminLogin');
          $query = "select a.id AS artId, a.title, a.category, a.status, d.title AS deliveryTitle, d.delivery_date, cl.company_name, l.lock_status, l.user_id AS lockedUser, u.email, u.login,
                 up.first_name, p.id AS partId, p.created_at, p.updated_at FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id
                 INNER JOIN Participation p ON p.article_id=a.id
                 INNER JOIN User u ON u.identifier=p.user_id
                 INNER JOIN UserPlus up ON up.user_id=p.user_id
                 LEFT JOIN Client cl ON cl.user_id = d.user_id
                 LEFT JOIN LockSystem l on a.id=l.article_id  WHERE  p.status ='closed_client_temp' AND p.current_stage ='client' GROUP BY a.id";
//exit;
        //echo $query;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function validRefusedArts($sWhere, $sOrder, $sLimit)
	{
	 	$query = "select a.id AS artId, a.title,a.delivery_id, d.title AS deliveryTitle, d.delivery_date, d.user_id as owner, u.email, u.login, 
		       CONCAT(up.first_name,' ',up.last_name) AS full_name, p.id as partId, p.updated_at, p.user_id AS contributor,
			   (select id FROM ArticleProcess WHERE participate_id = p.id order by version desc limit 0, 1) AS artprocessId
			  FROM Article a  INNER JOIN Delivery d ON a.delivery_id=d.id
			  INNER JOIN Participation p ON p.article_id=a.id
			  INNER JOIN User u ON u.identifier=p.user_id
			  INNER JOIN UserPlus up ON up.user_id=p.user_id
			  WHERE p.status in ('published') AND p.current_stage in ('client') ".$sWhere."  ".$sOrder." ".$sLimit." ";
		  if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		  else
			return "NO";
	}
    /////////get all records for moderation when corrector dissaproces the article from FO ///////////////////////////
    public function CorrectorDisapprovals($condition)
    {
        $query = "select a.id AS artId, a.title,a.delivery_id, a.category, a.submitdate_bo, a.price_bo, a.type, a.nbwords,
                    a.sign_type, d.title AS deliveryTitle, d.delivery_date, d.user_id as owner,
                    u.email, u.login, up.first_name, p.id as partId, p.created_at, p.updated_at, p.status, p.user_id as contributor,
                    p.corrector_id as corrector FROM ".$this->_name." a
                 INNER JOIN Delivery d ON a.delivery_id=d.id
                 LEFT JOIN Participation p ON p.article_id=a.id
                 LEFT JOIN CorrectorParticipation cp ON cp.article_id=a.id
                 INNER JOIN User u ON u.identifier=p.user_id
                 LEFT JOIN UserPlus up ON up.user_id=p.user_id
                 WHERE p.status IN ('disapproved_temp', 'closed_temp') AND p.cycle='0'".$condition." GROUP BY a.id";
				// echo $query;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function getArticles($did)
	{
		$ArtQuery="SELECT id FROM Article WHERE delivery_id='".$did."'";
		$Artresult = $this->getQuery($ArtQuery,true);
		return $Artresult;
	}
	
	public function getTestMissionArticles($recruitment_id=NULL)
	{
		if($recruitment_id)//Added by arun w.r.t recruitment
			$condition=" AND d.id='".$recruitment_id."'";


		$TmQuery="SELECT 
					d.title,d.min_mark,a.id,a.title as atitle,up.user_id,up.first_name,up.last_name,us.profile_type,p.id as participate_id,p.status,avg(ap.marks) as marks
				FROM 
					Delivery d INNER JOIN Article a ON d.id=a.delivery_id 
					INNER JOIN Participation p ON p.article_id=a.id 
					INNER JOIN UserPlus up ON p.user_id=up.user_id 
					INNER JOIN User us ON us.identifier=p.user_id
					INNER JOIN ArticleProcess ap ON ap.participate_id=p.id 
				WHERE 
					d.missiontest='yes' AND p.current_stage IN ('mission_test','client') AND p.status IN ('under_study','published') 
					$condition
				GROUP BY a.id";
		$Tmresult = $this->getQuery($TmQuery,true);
		return $Tmresult;		
	}
	
	public function getWriterdetail($part)
	{
		$WriQuery="SELECT p.user_id,p.article_id,p.status,p.price_user,up.first_name,up.last_name,u.profile_type,d.min_mark,ap.marks,ap.article_path,ap.comments,ap.id 
					FROM
						Participation p INNER JOIN UserPlus up ON p.user_id=up.user_id
						INNER JOIN User u ON up.user_id=u.identifier
						INNER JOIN Article a ON a.id=p.article_id
						INNER JOIN Delivery d ON d.id=a.delivery_id
						INNER JOIN ArticleProcess ap ON ap.participate_id=p.id AND ap.user_id=p.user_id
					WHERE p.id='".$part."'";
		$Wriesult = $this->getQuery($WriQuery,true);
		return $Wriesult;	
	}
	
	public function getCorrectiondetail($part)
	{
		$CorrQuery="SELECT cp.corrector_id,cp.price_corrector,up.first_name,up.last_name,ap.marks,ap.article_path,ap.comments,ap.id 
					FROM
						CorrectorParticipation cp INNER JOIN UserPlus up ON cp.corrector_id=up.user_id
						INNER JOIN ArticleProcess ap ON ap.participate_id=cp.participate_id AND ap.user_id=cp.corrector_id
					WHERE cp.participate_id='".$part."'";
		$Corrresult = $this->getQuery($CorrQuery,true);
		return $Corrresult;	
	}
	
	public function LoadArticles($aos)
	{
		$aos=implode("','",$aos);
		
		$ArtQuery="SELECT title,id FROM Article WHERE delivery_id IN ('".$aos."')";
		$Artesult = $this->getQuery($ArtQuery,true);
		return $Artesult;	
	}
    ///// get the mission related statistics /////
    public function  getBoUserMissionsStatistics($params)
    {
        $userId = $params['userid'];
        $condition = '';
        if($params['langs'] != '')
        {
            $condition.= " AND a.language = '".$params['langs']."' ";
        }
        if($params['categs'] != '')
        {
            $condition.= " AND a.category = '".$params['categs']."' ";
        }
        $query1 =  "select count(a.id) AS publicCount  FROM ".$this->_name." a
                        INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'public' ".$condition;
        $query2 =  "select count(a.id) AS privateCount  FROM ".$this->_name." a
                        INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'private' ".$condition;
        $query3 =  "select count(a.id) AS publicBoCount  FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'public' AND d.created_by = 'BO' AND a.correction != 'no' ".$condition;
        $query4 =  "select count(a.id) AS privateBoCount  FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'private' AND d.created_by = 'BO' AND a.correction != 'no' ".$condition;
        $query5 =  "select count(a.id) AS publicFoPubCount  FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'public' AND d.created_by = 'BO' AND a.correction != 'yes' AND a.corrector_privatelist IS NULL ".$condition;
        $query6 =  "select count(a.id) AS publicFoPrvCount  FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'public' AND d.created_by = 'BO' AND a.correction != 'yes' AND a.corrector_privatelist IS NOT NULL ".$condition;
        $query7 =  "select count(a.id) AS privateFoPubCount  FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'private' AND d.created_by = 'BO' AND a.correction != 'yes' AND a.corrector_privatelist IS NULL ".$condition;
        $query8 =  "select count(a.id) AS privateFoPrvCount  FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'private' AND d.created_by = 'BO' AND a.correction != 'yes' AND a.corrector_privatelist IS NOT NULL ".$condition;
        $query9 =  "SELECT a.language, COUNT(*) AS frequentLanguage FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                        WHERE d.created_user=".$userId." GROUP BY a.language ORDER BY frequentLanguage DESC LIMIT 1 ";
        $query10 =  "SELECT a.category, COUNT(*) AS frequentCategory FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                        WHERE d.created_user=".$userId." GROUP BY a.category ORDER BY frequentCategory DESC LIMIT 1 ";
        $query11 =  "select count(a.id) AS totalArtCount  FROM ".$this->_name." a
                        INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." ".$condition;
        $query12 =  "select TIME_TO_SEC(AVG(TIMEDIFF(p.created_at, p.accept_refuse_at))) AS timespend FROM Participation p LEFT JOIN ".$this->_name." a
                        ON a.id = p.article_id INNER JOIN Delivery d ON d.id = a.delivery_id WHERE d.created_user=".$userId." ".$condition;
        $result1 = $this->getQuery($query1,true);
        $result2 = $this->getQuery($query2,true);
        $result3 = $this->getQuery($query3,true);
        $result4 = $this->getQuery($query4,true);
        $result5 = $this->getQuery($query5,true);
        $result6 = $this->getQuery($query6,true);
        $result7 = $this->getQuery($query7,true);
        $result8 = $this->getQuery($query8,true);
        $result9 = $this->getQuery($query9,true);
        $result10 = $this->getQuery($query10,true);
        $result11 = $this->getQuery($query11,true);
        $result12 = $this->getQuery($query12,true);
        if(empty($result1))
             $result1[1]['publicCount'] = 0;
        if(empty($result2))
            $result2[2]['privateCount'] = 0;
         if(empty($result3))
             $result3[3]['publicBoCount'] = 0;
        if(empty($result4))
            $result4[4]['privateBoCount'] = 0;
        if(empty($result5))
            $result5[5]['publicFoPubCount'] = 0;
        if(empty($result6))
          $result6[6]['publicFoPrvCount'] = 0;
        if(empty($result7))
           $result7[7]['privateFoPubCount'] = 0;
        if(empty($result8))
            $result8[8]['privateFoPrvCount'] = 0;
        if(empty($result11))
            $result11[11]['totalArtCount'] = 0;
        if(empty($result12))
            $result11[12]['timespend'] = 0;
        $result13=array_merge($result1, $result2, $result3, $result4, $result5, $result6, $result7, $result8, $result9, $result10, $result11, $result12);
        return $result13;
    }
    ///get all the viewto of public ao in over all delivery aspect//
    public function boUserViewto($params)
    {
        $userId = $params['userid'];
        $condition = '';
        if($params['langs'] != '')
        {
            $condition.= " AND a.language = '".$params['langs']."' ";
        }
        if($params['categs'] != '')
        {
            $condition.= " AND a.category = '".$params['categs']."' ";
        }
          $query11 =  "select d.view_to FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'public' ".$condition." GROUP BY d.id";
        if(($result = $this->getQuery($query11,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ///get all the viewto of public ao in selection aspect//
    public function selectedProfileType($params)
    {
        $userId = $params['userid'];
        $condition = '';
        if($params['langs'] != '')
        {
            $condition.= " AND a.language = '".$params['langs']."' ";
        }
        if($params['categs'] != '')
        {
            $condition.= " AND a.category = '".$params['categs']."' ";
        }
        $query11 =  "select p.user_id  FROM ".$this->_name." a INNER JOIN Delivery d ON d.id = a.delivery_id
                       INNER JOIN Participation p ON p.article_id = a.id
                       WHERE d.created_user=".$userId." AND d.AOtype = 'public' ".$condition." GROUP BY d.id";
        if(($result = $this->getQuery($query11,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all clients w r t project manager///////////////////////////
    public function  getBoUserClientLIst($params)
    {
        $userId = $params['userid'];
        $query = "select cl.company_name FROM ".$this->_name." a
                         INNER JOIN Delivery d ON d.id = a.delivery_id
                         INNER JOIN User u ON u.identifier = d.user_id
                         INNER JOIN Client cl ON cl.user_id = u.identifier
                         WHERE d.created_user=".$userId." GROUP BY cl.user_id";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all article of the  project manager///////////////////////////
    public function  allArticlesOfBoUser($params)
    {
        $userId = $params['userid'];
        $query = "select d.id, a.id AS articleId, d.title AS deliveryName, a.title AS artName, cl.user_id AS clientId, cl.company_name, a.republish_count, a.language, a.category FROM ".$this->_name." a
                         INNER JOIN Delivery d ON d.id = a.delivery_id
                         INNER JOIN User u ON u.identifier = d.user_id
                         INNER JOIN Client cl ON cl.user_id = u.identifier
                         WHERE d.created_user=".$userId." ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all article of the  project manager///////////////////////////
    public function  getAllArticleIds($aoId)
    {
        $query = "select a.id AS artId FROM ".$this->_name." a  INNER JOIN Delivery d ON a.delivery_id=d.id WHERE d.id=".$aoId." GROUP BY a.id";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////////get all records for 0th stage ///////////////////////////
    public function getCorrectionChangeArticles($artId)
    {
          $query = "select a.id  FROM ".$this->_name." a
         LEFT JOIN Participation p ON p.article_id=a.id
         WHERE p.current_stage  IN ('stage1', 'stage2', 'client') AND p.article_id=".$artId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

}
