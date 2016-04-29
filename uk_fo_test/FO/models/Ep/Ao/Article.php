<?php



class Ep_Ao_Article extends Ep_Db_Identifier

{

	protected $_name = 'Article';

	

	public function InsertArticle($deli,$funnel1,$per)

	{

		$Aarray = array();

		$Aarray['id']=$this->getIdentifier(); 

		$Aarray['delivery_id']=$deli;

		$Aarray['title']=$this->utf8dec($funnel1['title']);	//print_r($Aarray);exit;

		$Aarray['contrib_percentage']=$per;

		$Aarray['category']="other";

		$Aarray['language']="uk";

		

		//Forming Delivery time

		

			if($funnel1['deliverytime_option']=='min')

				$delivery_time=$funnel1['deliverytime'];

			elseif($funnel1['deliverytime_option']=='hour')

				$delivery_time=$funnel1['deliverytime']*60;

			elseif($funnel1['deliverytime_option']=='day')	

				$delivery_time=$funnel1['deliverytime']*60*24;

			

			$Aarray["senior_time"]=$delivery_time;

			$Aarray["junior_time"]=$delivery_time/2;

			$Aarray["submit_option"]=$funnel1['deliverytime_option'];

		

			//$Aarray["senior_time"]=$this->getConfiguration('sc_time');

			//$Aarray["junior_time"]=$this->getConfiguration('jc_time');

			//$Aarray["submit_option"]="min";

		

		

		//Forming participation time

		

			if($funnel1['participationtime_option']=='min')

				$participation_time=$funnel1['participationtime'];

			elseif($funnel1['participationtime_option']=='hour')

				$participation_time=$funnel1['participationtime']*60;

			elseif($funnel1['participationtime_option']=='day')

				$participation_time=$funnel1['participationtime']*60*24;

			

			$Aarray["participation_time"]=$participation_time;

		

			//$Aarray["participation_time"]=$this->getConfiguration('participation_time');

				

		//private contribs

		if(count($funnel1['contribselect'])>0 && $funnel1['privatecontrib']=="on")

			$Aarray['contribs_list']=implode(",",$funnel1['contribselect']);

		

		//Price

		if($funnel1['price_min_total']!="")

			$Aarray['price_min']=$funnel1['price_min_total'];

		

		if($funnel1['price_max_total']!="")

		{

			$Aarray['price_max']=$funnel1['price_max_total'];

			$Aarray['price_final']=$funnel1['price_max_total'];

		}	

		

		if($funnel1['privatepublish']=="yes" && $funnel1['privatecontrib']=="on")

			$Aarray['participation_expires']=time()+(60*$participation_time);

		

		if($this->insertQuery($Aarray))

			return $this->getIdentifier(); 

		 else

			return "NO";

	}

	

	public function InsertLiberteArticle($deli,$funnel1,$funnel2)

	{

		$Aarray = array();

		$Aarray['id']=$this->getIdentifier(); 

		$Aarray['delivery_id']=$deli;

		$Aarray['title']=$this->utf8dec($funnel1['aotitleart']);	//print_r($Aarray);exit;

		$Aarray['contrib_percentage']=$funnel1['contrib_percentage'];

		$Aarray['category']="other";

		$Aarray['language']="uk";

		$Aarray['type']=$funnel1['typeart'];

		$Aarray['view_to']='sc';

		$Aarray['num_min']=$funnel1['min_sign'];		

		$Aarray['num_max']=$funnel1['max_sign'];	

		

		//Forming Delivery time

		if($funnel2['deliverytime']!="")	

		{

			if($funnel2['delivery_option']=='min')

				$delivery_time=$funnel2['deliverytime'];

			elseif($funnel2['delivery_option']=='hour')

				$delivery_time=$funnel2['deliverytime']*60;

			elseif($funnel2['delivery_option']=='day')	

				$delivery_time=$funnel2['deliverytime']*60*24;

			

			$Aarray["senior_time"]=$delivery_time;

			$Aarray["junior_time"]=$delivery_time/2;

			$Aarray["submit_option"]=$funnel2['delivery_option'];

		}

		else

		{

			$Aarray["senior_time"]=$this->getConfiguration('sc_time');

			$Aarray["junior_time"]=$this->getConfiguration('jc_time');

			$Aarray["submit_option"]='min';

		}

			$participation_time=$this->getConfiguration('participation_time');

			$Aarray["participation_time"]=$this->getConfiguration('participation_time');

				

		//private contribs

		if(count($funnel2['contribselect'])>0 && $funnel2['privatecontrib']=="1")

			$Aarray['contribs_list']=implode(",",$funnel2['contribselect']);

		

		//Price

		if($funnel2['pricecheck']=="1")

		{

			if($funnel2['price_min_total']!="")

			{

				$funnel2['price_min_total']=str_replace(",",".",$funnel2['price_min_total']);

				$Aarray['price_min']=$funnel2['price_min_total'];

			}

			if($funnel2['price_max_total']!="")

			{

				$funnel2['price_max_total']=str_replace(",",".",$funnel2['price_max_total']);

				$Aarray['price_max']=$funnel2['price_max_total'];

				$Aarray['price_final']=$funnel2['price_max_total'];

			}	

		}

		if($funnel1['privatepublish']=="yes" && $funnel2['privatecontrib']=="1")

			$Aarray['participation_expires']=time()+(60*$participation_time);

		

		$Aarray['frequency']=$funnel1['frequencyart'];

		

		if($this->insertQuery($Aarray))

			return $this->getIdentifier(); 

		 else

			return "NO";

	}

	

	public function ArtDownloadpath($id)

	{

		$ArtdwnldQuery="	SELECT 

						a.title,a.file_path,ap.article_path

					FROM 

						Article a INNER JOIN Participation p ON a.id=p.article_id 

						LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id 

					WHERE 

						a.id='".$id."' AND a.paid_status='paid' ORDER BY ap.version DESC";

		//echo $ArtdwnldQuery;exit;

		if(($Artdwnld= $this->getQuery($ArtdwnldQuery,true)) != NULL)

            return $Artdwnld;

	}

	

	public function ArtDownloadpathap($id)

	{

		$ArtdwnldQuery="	SELECT 

						a.title,a.file_path,a.id,ap.article_path,ap.article_name

					FROM 

						Article a INNER JOIN Participation p ON a.id=p.article_id 

						LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id 

					WHERE 

						ap.id='".$id."' AND a.paid_status='paid' ORDER BY ap.version DESC";

		//echo $ArtdwnldQuery;exit;

		if(($Artdwnld= $this->getQuery($ArtdwnldQuery,true)) != NULL)

            return $Artdwnld;

	}

	

	public function getArticledetails($id)

	{

		$ArtQuery="SELECT * FROM ".$this->_name." WHERE id='".$id."'";

		

		if(($Artdetails= $this->getQuery($ArtQuery,true)) != NULL)

            return $Artdetails;

	}

	

	public function updatePayarticle($in,$payar)

	{

		$wherear="id=".$payar[2];



		$Aarray = array(); 

		$Aarray["paid_status"] = "paid"; 

		$Aarray["invoice_id"] = $in; 

		$Aarray["price_payed"] = $payar[3]; 

		$Aarray["downloadtime"] = date("Y-m-d H:i:s");  
		$Aarray["updated_at"] = date("Y-m-d H:i:s");  

		$this->updateQuery($Aarray,$wherear);

		//$this->updateFilepath($ar[$pp]);

	}

	

	public function updateArticle($Aarr,$Awhere)

	{
		$Aarr["updated_at"] = date("Y-m-d H:i:s");  
		$this->updateQuery($Aarr,$Awhere);

	}

	

	public function utf8dec($s_String)

    {

            $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));

            return substr($s_String, 0, strlen($s_String)-1); 

    }

	

	public function checkArticleExists($art,$client)

	{

		$ArtQuery="SELECT * FROM ".$this->_name." a INNER JOIN Delivery d ON d.id=a.delivery_id WHERE a.id='".$art."' AND d.user_id='".$client."'";

		$Artdetails= $this->getQuery($ArtQuery,true);

        if(count($Artdetails)>0)   

		   return "YES";

		else

			return "NO";

	}

	

	public function checkArticlePaid($art)

	{

		$ArtQuerypaid="SELECT * FROM ".$this->_name." WHERE id='".$art."' AND paid_status='paid'";

		$Artdetailspaid= $this->getQuery($ArtQuerypaid,true);

        if(count($Artdetailspaid)>0)   

		   return "YES";

		else

			return "NO";

	}

	

	public function getArticlePricedetails($Aid)

	{

		$ArtQuery="SELECT a.title, a.id, d.id as did, SUM(a.price_final+d.premium_total) as amount  

						FROM 

						Delivery d INNER JOIN Article a ON d.id=a.delivery_id WHERE a.id='".$Aid."' AND a.paid_status='paid'";

		$Artdetails= $this->getQuery($ArtQuery,true);

        if(count($Artdetails)>0)   

		   return $Artdetails;

		else

			return "NO";

	}

	

	public function getSuperClientArticles($client,$search)

	{

		$deli_access="SELECT access_clients_list,access_lang_list,user_visibility,access_article_status  FROM Client WHERE user_id='".$client."'";

		$Accessdetails= $this->getQuery($deli_access,true);

		

		//Language

		$lang=explode(",",$Accessdetails[0]['access_lang_list']);

		$langstr=implode("','",$lang);

		

		$wherecondi="";

		

			if($search['site']!="")

				$wherecondi.=" AND d.user_id='".$search['site']."'";

			else

				$wherecondi.=" AND d.user_id IN (".$Accessdetails[0]['access_clients_list'].")";

				

			if($search['language']!="")	

				$wherecondi.=" AND a.language='".$search['language']."'";

			else

				$wherecondi.=" AND a.language IN ('".$langstr."')";

				

			if($search['ep_incharge']!="")	

				$wherecondi.=" AND d.created_user='".$search['ep_incharge']."'";

			if($search['status']!="")		

			{

				$wherecondi.=" AND (p.cycle='0' OR p.cycle IS NULL)";

				if($search['status']=='participation_ongoing')

					$wherecondi.=" AND (a.participation_expires>UNIX_TIMESTAMP() OR (a.participation_expires>UNIX_TIMESTAMP() AND  (SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0')='0'))";

				elseif($search['status']=='pending_selection')

					$wherecondi.=" AND (SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0' AND status IN ('bid_premium','bid_nonpremium'))>0";

				elseif($search['status']=='writing_progress')

					$wherecondi.=" AND p.status='bid' AND p.article_submit_expires>UNIX_TIMESTAMP()";

				elseif($search['status']=='time_out')	

					$wherecondi.=" AND (p.status='time_out' OR (p.status='bid' AND p.article_submit_expires<UNIX_TIMESTAMP()) )";

				elseif($search['status']=='disapproved')

					$wherecondi.=" AND p.status='disapproved'";

				elseif($search['status']=='correction_ongoing')

					$wherecondi.=" AND p.current_stage='corrector'";

				elseif($search['status']=='stage2')	

					$wherecondi.=" AND p.current_stage IN ('stage1','stage2')";

				elseif($search['status']=='published_client')	

					$wherecondi.=" AND (p.status='published' AND p.current_stage!='client')";

				elseif($search['status']=='published')

					$wherecondi.=" AND (p.status='published' AND p.current_stage='client')";

				elseif($search['status']=='closed')		

					$wherecondi.=" AND p.status='closed'";

			}

		

			if($search['status']=="")		

			{

				$permissions=explode(",",$Accessdetails[0]['access_article_status']);

				$permissioncondi=array();

					for($p=0;$p<count($permissions);$p++)

					{

						if($permissions[$p]=='participation_ongoing')

							$permissioncondi[]="(a.participation_expires>UNIX_TIMESTAMP() OR (a.participation_expires>UNIX_TIMESTAMP() AND (SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0')='0'))";

						elseif($permissions[$p]=='pending_selection')

							$permissioncondi[]="(SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0' AND status IN ('bid_premium','bid_nonpremium'))>0";

						elseif($permissions[$p]=='writing_progress')

							$permissioncondi[]="(p.status='bid' AND p.article_submit_expires>UNIX_TIMESTAMP())";

						elseif($permissions[$p]=='time_out')	

							$permissioncondi[]="(p.status='time_out' OR (p.status='bid' AND p.article_submit_expires<UNIX_TIMESTAMP()) )";

						elseif($permissions[$p]=='disapproved')

							$permissioncondi[]="p.status='disapproved'";

						elseif($permissions[$p]=='correction_ongoing')

							$permissioncondi[]="p.current_stage='corrector'";

						elseif($permissions[$p]=='stage2')	

							$permissioncondi[]="p.current_stage IN ('stage1','stage2')";

						elseif($permissions[$p]=='published_client')	

							$permissioncondi[]="(p.status='published' AND p.current_stage!='client')";

						elseif($permissions[$p]=='published')

							$permissioncondi[]="(p.status='published' AND p.current_stage='client')";

						elseif($permissions[$p]=='closed')		

							$permissioncondi[]="p.status='closed'";

					}

					

					if(count($permissioncondi)>0)

					{

						$wherecondi.=" AND (p.cycle='0' OR p.cycle IS NULL) AND (";

						$wherecondi.=implode(" OR ", $permissioncondi);

						$wherecondi.=" ) ";

					}

			}

		

		$ArtQuery="SELECT 

						d.lot,a.id,a.delivery_id,a.participation_expires,c.company_name,a.title,a.language,a.sub_title,ui.first_name,ui.last_name,p.article_submit_expires, 

						(SELECT CONCAT(u.first_name,'#',u.last_name) FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed') AND p.cycle='0')  as writer, 

						(SELECT p.status FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed') AND p.cycle='0') as status,

						(SELECT p.current_stage FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed') AND p.cycle='0') as current_stage,

						(SELECT CONCAT(up.first_name,'#',up.last_name) FROM CorrectorParticipation cp INNER JOIN UserPlus up ON cp.corrector_id=up.user_id WHERE cp.article_id=a.id AND cp.status IN ('bid','under_study','published','disapproved') GROUP BY a.id) as corrector	

					FROM 

						Article a INNER JOIN Delivery d ON a.delivery_id=d.id 

						LEFT JOIN Client c ON d.user_id=c.user_id 

						LEFT JOIN UserPlus ui ON d.created_user=ui.user_id 

						LEFT JOIN Participation p ON p.article_id=a.id

					WHERE 

						1=1 ".$wherecondi."

					GROUP BY 

						a.id ORDER By d.created_at DESC";

						//echo $ArtQuery;exit;

		$Artdetails= $this->getQuery($ArtQuery,true);

        return $Artdetails;

		

	}

	

	public function getSCBOArticles($client,$search)

	{

		$deli_access="SELECT access_client_list,access_language_list,access_permissions,writer_info,access_deliveries FROM ScBoUserPermissions WHERE bo_user='".$client."'";

		$Accessdetails= $this->getQuery($deli_access,true);

		

		//Language

		$lang=explode(",",$Accessdetails[0]['access_language_list']);

		$langstr=implode("','",$lang);

		

		$wherecondi="";

		

			if($Accessdetails[0]['access_deliveries']!="all")

				$wherecondi.=" AND d.id IN  (".$Accessdetails[0]['access_deliveries'].")";

			

			if($search['site']!="")

				$wherecondi.=" AND d.user_id='".$search['site']."'";

			else

				$wherecondi.=" AND d.user_id IN (".$Accessdetails[0]['access_client_list'].")";

				

			if($search['language']!="")	

				$wherecondi.=" AND a.language='".$search['language']."'";

			else

				$wherecondi.=" AND a.language IN ('".$langstr."')";

				

			if($search['ep_incharge']!="")	

				$wherecondi.=" AND d.created_user='".$search['ep_incharge']."'";

			if($search['status']!="")		 

			{

				$wherecondi.=" AND (p.cycle='0' OR p.cycle IS NULL)";

				if($search['status']=='participation_ongoing')

					$wherecondi.=" AND (a.participation_expires>UNIX_TIMESTAMP() OR (a.participation_expires>UNIX_TIMESTAMP() AND (SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0')=0))";

				elseif($search['status']=='pending_selection')

					$wherecondi.=" AND (SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0' AND status IN ('bid_premium','bid_nonpremium'))>0";

				elseif($search['status']=='writing_progress')

					$wherecondi.=" AND p.status='bid' AND p.article_submit_expires>UNIX_TIMESTAMP()";

				elseif($search['status']=='time_out')	

					$wherecondi.=" AND (p.status='time_out' OR (p.status='bid' AND p.article_submit_expires<UNIX_TIMESTAMP()) )";

				elseif($search['status']=='disapproved')

					$wherecondi.=" AND p.status='disapproved'";

				elseif($search['status']=='correction_ongoing')

					$wherecondi.=" AND p.current_stage='corrector'";

				elseif($search['status']=='stage2')	

					$wherecondi.=" AND p.current_stage IN ('stage1','stage2')";

				elseif($search['status']=='published_client')	

					$wherecondi.=" AND (p.status='published' AND p.current_stage!='client')";

				elseif($search['status']=='published')

					$wherecondi.=" AND (p.status='published' AND p.current_stage='client')";

				elseif($search['status']=='closed')		

					$wherecondi.=" AND p.status='closed'";

			}

		

			if($search['status']=="" && $Accessdetails[0]['access_deliveries']=="all")		

			{

				$permissions=explode(",",$Accessdetails[0]['access_permissions']);

				$permissioncondi=array();

					for($p=0;$p<count($permissions);$p++)

					{

						if($permissions[$p]=='participation_ongoing')

							$permissioncondi[]="(a.participation_expires>UNIX_TIMESTAMP() OR (a.participation_expires>UNIX_TIMESTAMP() AND (SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0')=0))";

						elseif($permissions[$p]=='pending_selection')

							$permissioncondi[]="(SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0' AND status IN ('bid_premium','bid_nonpremium'))>0";

						elseif($permissions[$p]=='writing_progress')

							$permissioncondi[]="(p.status='bid' AND p.article_submit_expires>UNIX_TIMESTAMP())";

						elseif($permissions[$p]=='time_out')	

							$permissioncondi[]="(p.status='time_out' OR (p.status='bid' AND p.article_submit_expires < UNIX_TIMESTAMP()))";

						elseif($permissions[$p]=='disapproved')

							$permissioncondi[]="p.status='disapproved'";

						elseif($permissions[$p]=='correction_ongoing')

							$permissioncondi[]="p.current_stage='corrector'";

						elseif($permissions[$p]=='stage2')	

							$permissioncondi[]="p.current_stage IN ('stage1','stage2')";

						elseif($permissions[$p]=='published_client')	

							$permissioncondi[]="(p.status='published' AND p.current_stage!='client')";

						elseif($permissions[$p]=='published')

							$permissioncondi[]="(p.status='published' AND p.current_stage='client')";

						elseif($permissions[$p]=='closed')		

							$permissioncondi[]="p.status='closed'";

					}

					//print_r($permissioncondi);

					if(count($permissioncondi)>0)

					{

						$wherecondi.=" AND (p.cycle='0' OR p.cycle IS NULL) AND (";

						$wherecondi.=implode(" OR ", $permissioncondi);

						$wherecondi.=" ) ";

					}

					//exit;

			}

		

		$ArtQuery="SELECT 

						d.lot,a.id,a.delivery_id,a.participation_expires,c.company_name,a.title,a.language,a.sub_title,ui.first_name,ui.last_name,p.article_submit_expires, 

						(SELECT CONCAT(u.first_name,'#',u.last_name) FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed') AND p.cycle='0')  as writer, 

						(SELECT p.status FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed') AND p.cycle='0') as status,

						(SELECT p.current_stage FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed') AND p.cycle='0') as current_stage,

						(SELECT CONCAT(up.first_name,'#',up.last_name) FROM CorrectorParticipation cp INNER JOIN UserPlus up ON cp.corrector_id=up.user_id WHERE cp.article_id=a.id AND cp.status IN ('bid','under_study','published','disapproved') GROUP BY a.id) as corrector	

					FROM 

						Article a INNER JOIN Delivery d ON a.delivery_id=d.id 

						LEFT JOIN Client c ON d.user_id=c.user_id 

						LEFT JOIN UserPlus ui ON d.created_user=ui.user_id 

						LEFT JOIN Participation p ON p.article_id=a.id

					WHERE 

						1=1 ".$wherecondi."

					GROUP BY 

						a.id ORDER By d.created_at DESC";

						//echo $ArtQuery;//exit;

		$Artdetails= $this->getQuery($ArtQuery,true);

        return $Artdetails;

		

	}

	

	public function getClientArticles($client,$search) 

	{

		if(count($search)>0)

		{

			$wherecondi="";

			if($search['site']!="")

				$wherecondi.=" AND d.user_id='".$search['site']."'";

			if($search['language']!="")	

				$wherecondi.=" AND a.language='".$search['language']."'";

			if($search['ep_incharge']!="")	

				$wherecondi.=" AND d.created_user='".$search['ep_incharge']."'";

			if($search['status']!="")		

			{

				if($search['status']=='participation_ongoing')

					$wherecondi.=" AND (a.participation_expires>UNIX_TIMESTAMP() OR (SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0')='0')";

				elseif($search['status']=='writing_progress')

					$wherecondi.=" AND p.status='bid' AND p.article_submit_expires>UNIX_TIMESTAMP()";

				elseif($search['status']=='time_out')	

					$wherecondi.=" AND (p.status='time_out' OR (p.status='bid' AND p.article_submit_expires<UNIX_TIMESTAMP()) ";

				elseif($search['status']=='disapproved')

					$wherecondi.=" AND p.status='disapproved'";

				elseif($search['status']=='correction_ongoing')

					$wherecondi.=" AND p.current_stage='corrector'";

				elseif($search['status']=='stage2')	

					$wherecondi.=" AND p.current_stage IN ('stage1','stage2')";

				elseif($search['status']=='published_client')	

					$wherecondi.=" AND (p.status='published' AND p.current_stage!='client')";

				elseif($search['status']=='published')

					$wherecondi.=" AND (p.status='published' AND p.current_stage='client')";

				elseif($search['status']=='closed')		

					$wherecondi.=" AND p.status='closed'";

			}

		}

		

		$ArtQuery="SELECT 

						d.lot,a.id,a.delivery_id,a.participation_expires,c.company_name,a.title,a.language,a.sub_title,ui.first_name,ui.last_name,p.article_submit_expires, 

						(SELECT CONCAT(u.first_name,'#',u.last_name) FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed'))as writer, 

						(SELECT p.status FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed'))as status, 

						(SELECT p.current_stage FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed'))as current_stage, 

						(SELECT CONCAT(up.first_name,'#',up.last_name) FROM CorrectorParticipation cp INNER JOIN UserPlus up ON cp.corrector_id=up.user_id WHERE cp.article_id=a.id AND cp.status IN ('bid','under_study','published','dissaproved','time_out')) as corrector

					FROM 

						Article a INNER JOIN Delivery d ON a.delivery_id=d.id 

						INNER JOIN Client c ON d.user_id=c.user_id 

						INNER JOIN UserPlus ui ON d.created_user=ui.user_id 

						LEFT JOIN Participation p ON p.article_id=a.id

					WHERE 

						d.user_id='".$client."' ".$wherecondi."

					GROUP BY 

						a.id";

						//echo $ArtQuery;exit;

		$Artdetails= $this->getQuery($ArtQuery,true);

        return $Artdetails;

	}

	

	function getOngoingArticleDetails($searchParameters=NULL)

	{

		

		if($searchParameters['ao_id'])

			$condition.=" AND d.id='".$searchParameters['ao_id']."'";



			$ongoingQuery="SELECT (SELECT count(pa.id) 

								FROM Participation pa INNER JOIN Article a1 ON a1.id=pa.article_id 

								WHERE pa.article_id=a.id AND cycle=0) as totalParticipations,

							

							(SELECT count(cp.id) 

								FROM CorrectorParticipation cp INNER JOIN Article a2 ON a2.id=cp.article_id 

								WHERE cp.article_id=a.id AND cycle=0) as totalCorrectionParticipations,	

							a.*,

							

							(SELECT login FROM User u where u.identifier=IF(d.created_by='BO',d.created_user,d.user_id))as incharge,

							(SELECT p.id FROM Participation p 

								WHERE p.status IN ('bid','time_out', 'under_study', 'disapproved','disapproved_temp','closed_temp','published','plag_exec') AND p.cycle=0 AND  p.article_id=a.id LIMIT 1

							) as writerParticipation,

							

							(SELECT ex.id From Participation ex 

								WHERE ex.id=writerParticipation and (ex.status='time_out' OR (ex.status in ('bid','disapproved')))

							) as expiredWriterParticipation,	



							(SELECT cp.id FROM CorrectorParticipation cp 

								WHERE cp.status IN ('bid', 'under_study','disapproved_temp', 'disapproved','closed_temp', 'published','closed') AND cp.cycle=0 AND  cp.article_id=a.id LIMIT 1

							) as correctorParticipation,

								

							(SELECT ex1.id From CorrectorParticipation ex1 

								WHERE ex1.id=correctorParticipation and (ex1.status='time_out' OR (ex1.status in ('bid','disapproved')))

							) as expiredcorrectorParticipation,

							

							(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN Participation p ON p.user_id=up.user_id

								WHERE p.id=writerParticipation LIMIT 1) 

							as writerName,

							

							(SELECT CONCAT_WS(' ',up.first_name,up.last_name) FROM UserPlus up INNER JOIN CorrectorParticipation cp ON cp.corrector_id=up.user_id

								WHERE cp.id=correctorParticipation LIMIT 1) 

							as correctorName,

							d.publishtime

							

							FROM Article a  

							INNER JOIN Delivery d ON d.id=a.delivery_id

							INNER JOIN User u ON u.identifier=d.user_id						

							WHERE 1=1 $condition						

							ORDER BY a.title ASC

							";

					

		//echo $ongoingQuery;exit; 				

		    

        if(($count=$this->getNbRows($ongoingQuery))>0)

        {

            $ongoingAO=$this->getQuery($ongoingQuery,true);

            return $ongoingAO;



        }

        else

        	return NULL;

	}

	

	public function publishedArticleDetail($article)

	{

		$ArtQuery="SELECT 

			d.file_name,a.id,a.delivery_id,a.category,a.title,a.language,ui.first_name,ui.last_name,us.email,p.id as participateid,

			(SELECT CONCAT(u.first_name,'#',u.last_name) FROM Participation p INNER JOIN UserPlus u ON p.user_id=u.user_id WHERE p.article_id=a.id AND p.status IN ('bid','under_study','published','disapproved','time_out','closed'))as writer, 

			(SELECT CONCAT(up.first_name,'#',up.last_name) FROM CorrectorParticipation cp INNER JOIN UserPlus up ON cp.corrector_id=up.user_id WHERE cp.article_id=a.id AND cp.status IN ('bid','under_study','published','disapproved') GROUP BY a.id) as corrector	

		FROM 

			Article a INNER JOIN Delivery d ON a.delivery_id=d.id 

			LEFT JOIN Client c ON d.user_id=c.user_id 

			LEFT JOIN UserPlus ui ON d.created_user=ui.user_id 

			LEFT JOIN User us ON ui.user_id=us.identifier

			LEFT JOIN Participation p ON p.article_id=a.id

		WHERE 

			a.id='".$article."' AND p.status IN ('published','under_study')

		GROUP BY 

			a.id";

			//echo $ArtQuery;//exit;

		$Artdetails= $this->getQuery($ArtQuery,true);

        return $Artdetails;

	}

	

	public function updatedownloadtime($article)

	{

		$Seltime="SELECT downloadtime FROM Article WHERE id='".$article."' ";

		$resulttime = $this->getQuery($Seltime,true);

		if($resulttime[0]['downloadtime']=="")

		{

			$darray["downloadtime"] = date("Y-m-d H:i:s"); 
			$darray["updated_at"] = date("Y-m-d H:i:s");  
			$wherear=" id='".$article."'";

			$this->updateQuery($darray,$wherear);

		}

	}

	

	public function checkPublishArticle()

	{

		$Seltime="SELECT a.id,p.id as partid,d.user_id FROM Article a INNER JOIN Participation p ON a.id=p.article_id INNER JOIN Delivery d ON d.id=a.delivery_id WHERE p.status IN ('under_study') AND p.current_stage IN ('client') AND a.paid_status='paid' AND a.downloadtime IS NOT NULL AND ADDDATE( a.downloadtime, INTERVAL 2 DAY )<CURRENT_TIMESTAMP()";

		$resulttime = $this->getQuery($Seltime,true);

		return $resulttime;

	}

	

	public function getSimilarQuotes($cat,$lang,$tot)

	{

		$SelquotebyCat="SELECT price_final FROM Article WHERE category='".$cat."' AND language='".$lang."' ORDER BY created_at DESC"; 

		$resultquotebyCat = $this->getQuery($SelquotebyCat,true);

		if(count($resultquotebyCat)>=$tot)

		{

			return array_sum($resultquotebyCat);

		}

		else

		{

			$SelquotebyLang="SELECT sum(price) as pricesum FROM (SELECT price_final as price FROM Article WHERE language='fr' LIMIT ".$tot." ) t1";

			$resultquotebyLang = $this->getQuery($SelquotebyLang,true);

			return $resultquotebyLang[0]['pricesum'];

		}

	}

	

	public function articleParticipationExpired()

	{

		$Selectquery="SELECT 

						d.user_id,a.id,up.first_name,up.last_name 

					FROM 

						Article a INNER JOIN Delivery d ON a.delivery_id=d.id 

						LEFT JOIN UserPlus up ON d.user_id=up.user_id

					WHERE 

						participation_expires>UNIX_TIMESTAMP() AND

						((SELECT count(*) FROM Participation where article_id=a.id)=0) or 

						((select count(*) from Participation where article_id=a.id AND status IN ('bid_nonpremium'))>0) 

						AND participationexpirehistory='no'";

		$result = $this->getQuery($Selectquery,true);

		return $result;

	}

	

	//Fetching Configuration

	public function getConfiguration($columns)

    {

        $SelConfg="SELECT configure_value FROM Configurations WHERE configure_name='".$columns."' ";

       

		if(($resultconfg = $this->getQuery($SelConfg,true)) != NULL)

            return $resultconfg[0]['configure_value'];

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

    /////////get all article of the  project manager///////////////////////////

    public function  getAllArticles()

    {

        $query = "select id FROM ".$this->_name." WHERE progressbar_percent != 100  ";

        if(($result = $this->getQuery($query,true)) != NULL)

        {

            foreach($result as $k=>$v)

            {

                $results[] =  $v['id'];

            }

            return $results;

        }



        else

            return "NO";

    }



	

}	