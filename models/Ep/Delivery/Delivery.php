<?php

class Ep_Delivery_Delivery extends Ep_Db_Identifier
{
	protected $_name = 'Delivery';
    public function insertDelivery($step1)
 	{
 		$step1["id"] = $this->getIdentifier();
		
		$this->insertQuery($step1);
		return $step1["id"];
	}

	public function getDeliveryID($articleId)
    {
        $query="select delivery_id from Article where id='".$articleId."'";
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result[0]['delivery_id'];
		else
			return "NO";
    }

    /////////get delivery full detials  w.r.t delivery id  ///////////////////////////
	public function getDeliveryDetails($delId)
	{
		 $query = "select d.*, a.*, d.id AS delId, d.title AS deliveryTitle, do.option_id, d.corrector_list, d.product,d.correction_type as correctiontype, a.paid_status from Delivery d
		            LEFT JOIN Article a ON a.delivery_id = d.id
		            LEFT JOIN DeliveryOptions do ON d.id=do.delivery_id WHERE d.id=".$delId;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}

	/////////get delivery full detials  w.r.t article id  ///////////////////////////
	public function getArtDeliveryDetails($articleId)
	{
	     $query = "select d.*,a.*, d.id, a.id AS artId, a.title as articleName, a.publish_language AS publang, d.mail_send, d.title AS deliveryTitle, d.premium_option, d.correction_type AS delcrttype,a.participation_time as articleparttime,a.senior_time as st,a.junior_time as jt,a.subjunior_time as sjt,a.correction as articlecorrection,
	 	 d.submitdate_bo, a.contribs_list AS contribslist, a.participation_expires,
		 a.correction_jc_submission as cjt,a.correction_sc_submission as cst,d.contract_mission_id, a.sourcelang_nocheck as sourcelang_nocheckart
		 from ".$this->_name." d
		 INNER JOIN Article a ON d.id=a.delivery_id
    	 WHERE a.id=".$articleId;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////get delivery full detials  w.r.t article id  ///////////////////////////
    public function getArtDelDetails($aoId)
    {
          $query = "select d.*, d.id, a.id AS artId, a.title as articleName,d.mail_send, d.title AS deliveryTitle, d.premium_option, d.submitdate_bo, d.price_min, d.price_max, d.correction_type AS delcrttype, d.status_bo AS del_status_bo, d.published_at,
            d.participation_time, d.language, d.view_to, d.AOtype, d.total_article, a.contribs_list AS contribslist, a.language AS artLanguage, a.corrector_privatelist, a.participation_expires, d.product, a.paid_status from ".$this->_name." d
		 INNER JOIN Article a ON d.id=a.delivery_id
    	 WHERE d.id=".$aoId. " GROUP BY d.id";
		//echo $query."<br />";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////////get all deliveries count ///////////
    public function getAllDeliveryCount()
	{
		$query = "select count(id) AS alldelcount from ".$this->_name;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /// check any left over article which not validated by client ///
	public function checkLastArticleAO($deliveryId)
    {
         $query="SELECT count(*) as ArticleCount,(SELECT count(*) FROM Participation p
                    INNER JOIN Article a ON a.id=p.article_id
                    INNER JOIN Delivery d ON d.id=a.delivery_id
                where d.id='".$deliveryId."' and p.status='published') ParticipationCount FROM Article WHERE delivery_id='".$deliveryId."'";

        if(($result = $this->getQuery($query,true)) != NULL)
        {
            if($result[0]['ArticleCount']==$result[0]['ParticipationCount'])
                return "YES";
            else
                return "NO";
        }
        else
            return "NO";
    }
    // check whether article is premium or not ///
    public function checkPremiumAO($articleId)
    {
        $query="select premium_option, AOtype from ".$this->_name." d INNER JOIN Article a ON d.id=a.delivery_id
                where a.id='".$articleId."'";
        if(($count=$this->getNbRows($query))>0)
        {
            $premium=$this->getQuery($query,true);
            if($premium[0]['premium_option']=='no')
                return "NO";
            else
                return "YES";
        }
    }
    /////////get delivery full detials  w.r.t delivery id  ///////////////////////////
	public function getPrAoDetails($delId)
	{
		  $query = "SELECT d.*, a.*, d.category AS del_category, a.id as articleid, d.title AS aoName, a.participation_time AS participation_time, d.senior_time AS senior_time, 						                   a.title as artname, cl.company_name, u.email, u.identifier,
		          GROUP_CONCAT(DISTINCT a.category) as fav_category, GROUP_CONCAT(DISTINCT a.contribs_list) as article_contribs, d.contribs_list,d.missiontest,
				  IF(d.created_by='BO',d.created_user,d.user_id) as created_user
		          FROM ".$this->_name." d
		          INNER JOIN  Article a ON a.delivery_id=d.id
		          LEFT JOIN DeliveryOptions o ON o.delivery_id=d.id
		          LEFT JOIN Client cl ON d.user_id=cl.user_id
		          INNER JOIN User u ON d.user_id=u.identifier
		          WHERE d.id ='".$delId."'";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}

   /////////get delivery full detials  w.r.t article id  ///////////////////////////
    public function getPrAoDetailsWithArtid($artId)
    {
        /*$query = "SELECT d.*, a.id as articleid, a.title as artname, a.price_min, a.price_max, a.contribs_list,a.corrector_privatelist,
                  a.priority_contributors, a.participation_expires, count(a.id) as noofarts, cl.company_name, u.email, u.identifier, p.status, p.id as pid,
		          GROUP_CONCAT(DISTINCT a.category) as art_category,
				  GROUP_CONCAT(DISTINCT a.contribs_list) as article_contribs,
				  GROUP_CONCAT(DISTINCT a.corrector_privatelist) as private_correctors
		          FROM ".$this->_name." d
		          INNER JOIN  Article a ON a.delivery_id=d.id
		          INNER JOIN Participation  p ON (p.article_id=a.id AND p.cycle=0)
		          LEFT JOIN DeliveryOptions o ON o.delivery_id=d.id
		          LEFT JOIN Client cl ON d.user_id=cl.user_id
		          INNER JOIN User u ON d.user_id=u.identifier
		          WHERE a.id ='".$artId."'";//echo $query ;*/
        $query ="SELECT d.*, a.id as articleid, a.title as artname, a.price_min, a.price_max, a.contribs_list,a.corrector_privatelist,d.missiontest,a.language AS artLanguage,
                a.priority_contributors, a.republish_object, a.republish_mail, a.participation_expires, count(a.id) as noofarts, cl.company_name, u.email,a.estimated_worktime,a.estimated_workoption,
                u.identifier,GROUP_CONCAT(DISTINCT a.category) as art_category,GROUP_CONCAT(DISTINCT a.category) as fav_category, GROUP_CONCAT(DISTINCT a.contribs_list) as article_contribs,
                GROUP_CONCAT(DISTINCT a.corrector_privatelist) as private_correctors,a.correction as articlecorrection,
                (select p.status FROM Participation p Where p.cycle=0 and p.article_id=a.id ORDER BY IF(p.updated_at!='',p.updated_at,p.created_at) DESC LIMIT 0,1) as status
                FROM  Delivery d
                INNER JOIN Article a ON a.delivery_id=d.id
                LEFT JOIN DeliveryOptions o ON o.delivery_id=d.id
               LEFT JOIN Client cl ON d.user_id=cl.user_id
                INNER JOIN User u ON d.user_id=u.identifier
                WHERE a.id ='".$artId."'";
          // echo $query;exit;
         //LEFT JOIN (SELECT status, article_id FROM Participation WHERE cycle=0 GROUP BY article_id ORDER BY created_at ASC LIMIT 1 ) p ON p.article_id=a.id
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get delivery full detials  w.r.t article id in corrector aspect ///////////////////////////
    public function getCrtPrAoDetailsWithArtid($artId)
    {
        $query ="SELECT d.*, a.id as articleid, a.title as artname, a.price_min, a.price_max, a.contribs_list,a.corrector_privatelist, a.correction_participationexpires,
                a.priority_contributors, a.republish_object, a.republish_mail, a.participation_expires, count(a.id) as noofarts, cl.company_name, u.email,
                u.identifier,GROUP_CONCAT(DISTINCT a.category) as art_category, GROUP_CONCAT(DISTINCT a.contribs_list) as article_contribs,
                GROUP_CONCAT(DISTINCT a.corrector_privatelist) as private_correctors,
                (select cp.status FROM CorrectorParticipation cp Where cp.cycle=0 and cp.article_id=a.id ORDER BY IF(cp.updated_at!='',cp.updated_at,cp.created_at) DESC LIMIT 0,1) as status
                FROM  Delivery d
                INNER JOIN Article a ON a.delivery_id=d.id
                LEFT JOIN DeliveryOptions o ON o.delivery_id=d.id
               LEFT JOIN Client cl ON d.user_id=cl.user_id
                INNER JOIN User u ON d.user_id=u.identifier
                WHERE a.id ='".$artId."'";
        // echo $query;exit;
        //LEFT JOIN (SELECT status, article_id FROM Participation WHERE cycle=0 GROUP BY article_id ORDER BY created_at ASC LIMIT 1 ) p ON p.article_id=a.id
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    ////////////udate the delivery table//////////////////////
    public function updateDelivery($data,$query)
    {
        $data['updated_at']=date("Y-m-d", time());
        $this->updateQuery($data,$query);
    }
    public function insertDeliveries($data)
    {
        $this->insertQuery($data);
    }
    ////////get deliveries of created Fo users///////////
    public function getArticlesOfDel($delId)
    {
        $query="SELECT d.id, d.title, d.user_id, a.participation_time, GROUP_CONCAT( a.title
            SEPARATOR '|') AS artTitles, GROUP_CONCAT( a.id SEPARATOR '@') AS artIds,a.correction_participation FROM ".$this->_name." d
            LEFT JOIN  Article a ON a.delivery_id=d.id
            WHERE d.id = ".$delId;
        //echo $query;exit;
        $result = $this->getQuery($query,true);
        return $result;
    }

	//Get Priority contributor details
	public function getPollcontribDetails($poll,$contrib)
	{
		$Pollquery = "SELECT DATE_FORMAT(p.poll_date, '%d/%m/%y %H:%i:%s') AS poll_date,p.title,pp.price_user FROM Poll p INNER JOIN Poll_Participation pp ON p.id=pp.poll_id
		          WHERE p.id =".$poll." AND pp.user_id=".$contrib;

		if(($resultpoll = $this->getQuery($Pollquery,true)) != NULL)
			return $resultpoll;
	else
			return "NO";
	}

	/////////get delivery list for listing them in dropdown///////////
    public function getAllAos()
    {
        $query="select d.id, d.title from ".$this->_name." d WHERE ".$this->visibility."";
        //echo $query;exit;
         $result = $this->getQuery($query,true);
			return $result;

    }

	/////////get delivery full detials  w.r.t delivery id  ///////////////////////////
	public function getParentOption($delId)
	{
		 $query = "select premium_option from ".$this->_name." WHERE id=".$delId;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
	 /**Get contributors to send mails when approved an AO**/
    public function getContributorsAO($type,$condition1,$profiles)
    {
		$profs=explode(",",$profiles);
		$proflist=array();
		for($p=0;$p<count($profs);$p++)
		{
			if($profs[$p]=="jc")
				$proflist[]="junior";
			elseif($profs[$p]=="sc")
				$proflist[]="senior";
			elseif($profs[$p]=="jc0")
			$proflist[]="sub-junior";
		}
		$profile1=implode("','",$proflist);
	    if($type=='public')
        {
            $condition="WHERE status='Active' AND blackstatus='no' AND (favourite_category Like '%".str_replace(",","%' OR favourite_category Like '%",$condition1)."%') AND profile_type IN ('".$profile1."')";
           $query="select DISTINCT u.identifier, u.profile_type  FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition;

        }
       if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
	   else
			return "NO";
    }
    // get all the contirbutor of all profiles jc0, jc, sc
    public function getAllContribAO($type)
	{
		$black=$this->getConfiguration('blacked_writers');
 		$where1="";
		if($black=='no')
			$where1=" AND blackstatus='no' ";
		if($type=='1')
			$where1.=" AND profile_type='senior' ";
        elseif($type=='2')
            $where1.=" AND profile_type='junior' ";
        elseif($type=='3')
            $where1.=" AND profile_type='sub-junior' ";
        elseif($type=='1,2')
            $where1.=" AND ( profile_type IN ('senior','junior') ) ";
        else
             $where1.=" AND ( profile_type IN ('senior','junior','sub-junior' ) )";
		$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u INNER JOIN UserPlus up ON u.identifier=up.user_id where u.type='contributor' AND u.status='Active' ".$where1;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
	// mail to be sent to contributors
	public function getMailContribs($type)
	{
		$black=$this->getConfiguration('blacked_writers');
 		$where1="";
		if($black=='no')
			$where1=" AND blackstatus='no' ";
		
		$type=implode("','",$type);
		$where1.=" AND  u.profile_type IN ('".stripslashes($type)."')";
		 
		$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id where u.type='contributor' AND u.status='Active' ".$where1;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
	
	public function getAllCorrectors()
	{
		$black=$this->getConfiguration('blacked_writers');
 		$where1="";
		if($black=='no')
			$where1=" AND blackstatus='no' ";
		
		$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id where u.type='contributor' AND u.status='Active' AND u.type2='corrector'".$where1;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
	
	public function getContribsByType($type,$cat="all",$lang="all")
	{
		$black=$this->getConfiguration('blacked_writers');
		$where1="";

		if($black=='no')
			$where1=" AND u.blackstatus='no' ";
		$where1.=" AND ( u.profile_type IN ('".stripslashes($type)."'))";
		//echo $lang;
		if(is_array($lang)){
			//echo "ITS AN ARRAY";
			
			
				$lang=implode("','",$lang);
			
			$where1.=" AND c.language IN ('".$lang."')";
		}else{
		if(count($lang)>0 && $lang!='all')
			{
				$lang1=explode(",",$lang);
				$lang2=implode("','",$lang1);
				$where1.=" AND c.language IN ('".$lang2."')";
			}
		}
		if($cat!="all" && $cat!="")
			$where1.=" AND c.favourite_category like '%".$cat."%'";
		$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Contributor c ON u.identifier=c.user_id where u.type='contributor' AND u.status='Active' ".$where1;//echo $SelectallContrib;exit;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
	
	public function getContribsByTypeTest($type,$cat="all",$lang="all",$test,$marks)
	{
		$black=$this->getConfiguration('blacked_writers');
		$where1="";

		if($black=='no')
			$where1=" AND u.blackstatus='no' ";
		$where1.=" AND ( u.profile_type IN ('".stripslashes($type)."'))";
		//echo $lang;
		if(is_array($lang)){
			//echo "ITS AN ARRAY";
			
			
				$lang=implode("','",$lang);
			
			$where1.=" AND c.language IN ('".$lang."')";
		}else{
		if(count($lang)>0 && $lang!='all')
			{
				$lang1=explode(",",$lang);
				$lang2=implode("','",$lang1);
				$where1.=" AND c.language IN ('".$lang2."')";
			}
		}
		if($cat!="all" && $cat!="")
			$where1.=" AND c.favourite_category like '%".$cat."%'";
			
		if($test=="yes")
		{
			$where1.=" AND c.contributortest='yes'";
			
			if($marks!="")
				$where1.=" AND c.contributortestmarks>='".$marks."'";
		}		
		$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Contributor c ON u.identifier=c.user_id where u.type='contributor' AND u.status='Active' ".$where1; //echo $SelectallContrib;exit;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
	
    public function getContribsByTypeLangCat($type,$cat="all",$lang)
    {
        $black=$this->getConfiguration('blacked_writers');
        $where1="";
        if($black=='no')
            $where1=" AND u.blackstatus='no' ";
        $where1.=" AND ( u.profile_type IN ('".stripslashes($type)."'))";
        if($lang!='NULL' && $lang!='')
        {
            $lang=explode(",",$lang);
            $lang=implode("','",$lang);
            $where1.=" AND c.language IN ('".$lang."')";
        }
        if($cat!="all" && $cat!="")
            $where1.=" AND c.favourite_category like '%".$cat."%'";
        $SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Contributor c ON u.identifier=c.user_id where u.type='contributor' AND u.status='Active' ".$where1;
        $resultall = $this->getQuery($SelectallContrib,true);
        return $resultall;
    }
	public function getContribsByLang($lang="all",$type)
	{	
		$black=$this->getConfiguration('blacked_writers');
		$where1="";

		if($black=='no')
			$where1=" AND u.blackstatus='no' ";
		//$where1.=" AND ( u.profile_type IN ('senior'))";
		if(count($lang)>0)
		{
			//$lang1=explode(",",$lang);
			$lang2=implode("','",$lang);
			$where1.=" AND c.language IN ('".$lang2."')";
		}	
		$type=implode("','",$type);
		$where1.=" AND  u.profile_type IN ('".stripslashes($type)."')";
		
		$SelectallContrib="SELECT identifier,email,first_name,last_name,c.contributortest,c.contributortestmarks FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Contributor c ON u.identifier=c.user_id where u.type='contributor' AND u.status='Active' ".$where1;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
	
	public function getAOlist($clnt=NULL,$paid=NULL)
    {
        if($clnt)
            $where.=" AND d.user_id='".$clnt."' AND ".$this->visibility."";

        if($paid)
            $where.=" AND p.status='Paid' AND ".$this->visibility."";
        if($where)
        	$where=" WHERE 1=1 AND ".$this->visibility." ".$where;


         $SelQuery="SELECT d.id,d.title FROM Delivery d LEFT JOIN Payment p ON d.id=p.delivery_id $where ";
        $resultamt = $this->getQuery($SelQuery,true);
        return $resultamt;
    }

	/**Get contributors with respect to language **/
    public function getContributorsLanguage($type,$profiles,$langs)
    {
        $profs=explode(",",$profiles);
        $proflist=array();
        for($p=0;$p<count($profs);$p++)
        {
            if($profs[$p]=="jc")
                $proflist[]="junior";
            elseif($profs[$p]=="sc")
                $proflist[]="senior";
            elseif($profs[$p]=="jc0")
                $proflist[]="sub-junior";
        }
        $profile1=implode("','",$proflist);
        $langs1=implode(",",$langs);

        if($type=='public')
        {
            $condition="WHERE status='Active' AND blackstatus='no' AND profile_type IN ('".$profile1."') AND (language Like '%".str_replace(",","%' OR language Like '%",$langs1)."%')";
            $query="select DISTINCT u.identifier, u.profile_type  FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition;
        }
		//echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get delivery created Bo user details///////////
    public function getDelCreatedUser($delId)
    {
        $query="SELECT d.id, d.title, d.user_id, d.created_user, u.email, up.first_name, up.last_name FROM ".$this->_name." d
            LEFT JOIN User u ON u.identifier=d.created_user
            LEFT JOIN UserPlus up ON up.user_id=d.created_user
            LEFT JOIN  Article a ON a.delivery_id=d.id
            LEFT JOIN Participation p ON p.article_id=a.id
             WHERE d.id = ".$delId;
        //echo $query;exit;
        $result = $this->getQuery($query,true);
        return $result;
    }
	/*Get contributors with respect to language */
    public function getContributorsOfAllCategories($type,$profiles)
    {
        $profs=explode(",",$profiles);
        $proflist=array();
        for($p=0;$p<count($profs);$p++)
        {
            if($profs[$p]=="jc")
                $proflist[]="junior";
            elseif($profs[$p]=="sc")
                $proflist[]="senior";
            elseif($profs[$p]=="jc0")
                $proflist[]="sub-junior";
        }
        $profile1=implode("','",$proflist);
        if($type=='public')
        {
            $condition="WHERE type='contributor' AND status='Active' AND blackstatus='no' AND profile_type IN ('".$profile1."')";
            $query="select DISTINCT u.identifier, u.profile_type  FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition;
        }

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get delivery list for new ao list page///////////
   /* public function getNewNonpremAos()
    {
        $query="select id, title from ".$this->_name." WHERE premium_option = '0' AND status_bo IS NULL";
        //echo $query;exit;
        $result = $this->getQuery($query,true);
        return $result;
    }*/
    /////////get delivery list for new ao list page which are ongoing///////////
    public function getNewNonpremAos($type)
    {
        if($type == '')
            $condition = " d.status_bo IS NULL ";
        else
            $condition = " d.status_bo = '".$type."' AND ".$this->visibility." ";
        $query="SELECT d.id, d.title, d.delivery_date, d.total_article, d.created_at, d.language, u.email,
              u.login,d.user_id, up.first_name,d.total_amount,d.created_by,d.quoteid, cl.company_name, u.created_at AS doj
                FROM ".$this->_name." d
                INNER JOIN User u ON u.identifier=d.user_id
                LEFT JOIN UserPlus up ON up.user_id=d.user_id
                LEFT JOIN Client cl ON cl.user_id = u.identifier
                WHERE d.premium_option = '0' AND ".$condition." AND d.created_by !='BO' ORDER BY d.created_at ASC";
        //echo $query;
        $result = $this->getQuery($query,true);
        return $result;
    }
    /////////get delivery list for new ao list page which are ongoing///////////
    public function loadgetNewNonpremAos($sWhere, $sOrder, $sLimit, $type)
    {
        /* *** edited on 12.05.2016 *** */
        // issue realted to ticket id = A01333 //
        $subQuery = '';
        $subJoin ='';
        if($type == '') {
            $condition = " d.status_bo IS NULL ";
        }
        elseif($type == 'active') {
            $subJoin = 'LEFT JOIN Participation p ON p.article_id=a.id';
            $condition = "p.status IN ('bid','under_study','bid_nonpremium_timeout','bid_nonpremium')
                        AND d.status_bo IN ('active', 'valid')
                        AND a.status NOT IN ('closed_client','closed')
                       ";
            $condition2 = "d.id IN
            (
                SELECT `delivery_id` FROM `Article`
                WHERE status_bo  = 'active' AND `id` NOT IN(
                SELECT `article_id` FROM `Participation`
                )
            )
             AND
                            d.status_bo IN ('active', 'valid')
                            AND a.status NOT IN ('closed_client','closed')
            ";
            $query2="SELECT DISTINCT(d.id), d.id as delId, d.title, d.delivery_date, d.total_article, d.created_at, d.language, d.product, d.liberte_comments AS comments, u.email,
              u.login,d.user_id, up.first_name,d.total_amount,d.created_by,d.quoteid, cl.company_name, u.created_at AS doj,
              IF(cl.company_name = '', u.email, cl.company_name) as clientName
                FROM ".$this->_name." d
                INNER JOIN Article a ON a.delivery_id = d.id
                INNER JOIN User u ON u.identifier=d.user_id
                LEFT JOIN UserPlus up ON up.user_id=d.user_id
                LEFT JOIN Client cl ON cl.user_id = u.identifier
                WHERE d.premium_option = '0'  AND ".$condition2." AND d.created_by !='BO'  ".$sWhere." ".$sOrder." ".$sLimit;
            //echo "<pre>".$query;exit(0);
            $result2 = $this->getQuery($query2,true);
            //return $result2;
            //p.status IN ('bid','under_study','bid_nonpremium_timeout','bid_nonpremium') AND a.status NOT IN ('closed','closed_client','published')
        }
        elseif($type == 'valid') {
            $subJoin = 'LEFT JOIN Participation p ON p.article_id=a.id';
            $condition = "p.status='published' AND
                            d.status_bo IN ('active', 'valid')
                             AND a.paid_status = 'paid' ";
        }
        elseif($type == 'cancel') {
            $subJoin = 'LEFT JOIN Participation p ON p.article_id=a.id';
            $condition = "p.status  IN ('closed','closed_client')
                            AND
                            d.status_bo IN ('active', 'valid')
                             ";
            $condition2 = "( d.status_bo = '" . $type . "' OR a.status IN ('closed_client','closed') )";
            $query2="SELECT DISTINCT(d.id), d.id as delId, d.title, d.delivery_date, d.total_article, d.created_at, d.language, d.product, d.liberte_comments AS comments, u.email,
              u.login,d.user_id, up.first_name,d.total_amount,d.created_by,d.quoteid, cl.company_name, u.created_at AS doj,
              IF(cl.company_name = '', u.email, cl.company_name) as clientName
                FROM ".$this->_name." d
                INNER JOIN Article a ON a.delivery_id = d.id
                INNER JOIN User u ON u.identifier=d.user_id
                LEFT JOIN UserPlus up ON up.user_id=d.user_id
                LEFT JOIN Client cl ON cl.user_id = u.identifier
                WHERE d.premium_option = '0'  AND ".$condition2." AND d.created_by !='BO'  ".$sWhere." ".$sOrder." ".$sLimit;
            $result2 = $this->getQuery($query2,true);
        }
        else{
            $condition = " d.status_bo = '" . $type . "' ";
        }
        $query="SELECT DISTINCT(d.id), d.id as delId, d.title, d.delivery_date, d.total_article, d.created_at, d.language, d.product, d.liberte_comments AS comments, u.email,
              u.login,d.user_id, up.first_name,d.total_amount,d.created_by,d.quoteid, cl.company_name, u.created_at AS doj,
              IF(cl.company_name = '', u.email, cl.company_name) as clientName
                FROM ".$this->_name." d
                INNER JOIN Article a ON a.delivery_id = d.id
                ".$subJoin."
                INNER JOIN User u ON u.identifier=d.user_id
                LEFT JOIN UserPlus up ON up.user_id=d.user_id
                LEFT JOIN Client cl ON cl.user_id = u.identifier
                WHERE d.premium_option = '0' AND ".$condition." AND d.created_by !='BO'  ".$sWhere." ".$sOrder." ".$sLimit;
        //echo "<pre>".$query;exit(0);
        $result = $this->getQuery($query,true);
        if($type == 'active' OR $type== 'cancel'){
            $result = array_merge($result,$result2);
        }
        return $result;
    }
    /////////get delivery list for new ao list page which are ongoing///////////
    public function getNonpremAllAos($type)
    {
        if($type == '')
            $condition = " d.status_bo IS NULL ";
        else
            $condition = " d.status_bo = '".$type."' ";
        $query="SELECT d.id, d.id as delId, d.title, d.delivery_date, d.total_article, d.created_at, d.language, u.email,a.invoice_id,
              u.login,d.user_id, up.first_name,d.total_amount,d.created_by,d.quoteid, cl.company_name, u.created_at AS doj, d.liberte_action_by,
              IF(cl.company_name = '', u.email, cl.company_name) as clientName, a.id as artId
                FROM ".$this->_name." d
                INNER JOIN Article a ON a.delivery_id=d.id
                INNER JOIN User u ON u.identifier=d.user_id
                LEFT JOIN UserPlus up ON up.user_id=d.user_id
                LEFT JOIN Client cl ON cl.user_id = u.identifier
                WHERE d.premium_option = '0' AND ".$condition." AND d.created_by !='BO' ";
        //echo $query;
        $result = $this->getQuery($query,true);
        return $result;
    }
    /*added by naseer on 24-09-2015 */
    // optimized getNonpremAllAos query //
    /////////get delivery list for new ao list page which are ongoing///////////
    public function getNonpremAllAosNew($type)
    {

        if($type == '')
            $condition = " d.status_bo IS NULL ";
        else if($type == 'valid')
            $condition = " d.status_bo  = 'valid' AND a.paid_status = 'paid' ";
        else
            $condition = " d.status_bo = '".$type."' ";
        $query="SELECT d.id, d.id AS delId, d.title, d.delivery_date, d.total_article, d.created_at, d.language, d.user_id, d.total_amount, d.created_by, d.quoteid, d.liberte_action_by,
                    u.email, a.invoice_id, u.login, u.created_at AS doj,
                    up.first_name,
                    cl.company_name,
                    IF( cl.company_name = '', u.email, cl.company_name ) AS clientName, a.id AS artId,
                    (
                        SELECT count( p2.id )
                        FROM Participation p2
                        INNER JOIN Article a2 ON a2.id = p2.article_id
                        INNER JOIN Delivery d2 ON d2.id = a2.delivery_id
                        LEFT JOIN UserPlus up2 ON up2.user_id = p2.user_id
                        WHERE p2.status
                        IN (
                        'bid_nonpremium', 'bid_nonpremium_timeout', 'bid', 'under_study', 'published'
                        )
                        AND d2.id = d.id
                    ) AS partCount,
                    (
                        SELECT
                            CASE
                            WHEN COUNT( p3.article_id ) = '0'
                            THEN 'no'
                            ELSE 'yes'
                            END
                        FROM Participation p3
                        INNER JOIN Article a3 ON a3.id = p3.article_id
                        WHERE p3.status = 'published'
                        AND p3.current_stage = 'client'
                        AND p3.article_id = a.id
                    ) AS missionvalid,
                    (
                         SELECT DATE_FORMAT(`created_at`,'%d-%m-%Y')
                         FROM Payment_article WHERE id=a.invoice_id

                    ) AS paidornot,
                    (
                        select  DATE_FORMAT(ap4.`article_sent_at`,'%d-%m-%Y')
                        FROM Participation p4
                        INNER JOIN ArticleProcess ap4 ON ap4.participate_id = p4.id
                        WHERE
                        p4.status NOT IN ('bid_nonpremium', 'bid_nonpremium_timeout', 'bid')
                        AND p4.article_id = a.id

                    ) AS uploaddate,
                    (
                        SELECT
                        d5.published_at

                        FROM Delivery d5
                        INNER JOIN Article a5 ON d5.id=a5.delivery_id
                        WHERE d5.id= delId
                        GROUP BY d5.id
                    )  AS published_at,
                     (
                        SELECT DATE_FORMAT(p6.`accept_refuse_at`,'%d-%m-%Y')
                        FROM Participation p6
                        INNER JOIN Article a6 ON a6.id=p6.article_id
                        WHERE
                        p6.status != 'bid_nonpremium'
                        AND p6.article_id = a.id
                    ) AS accept_refuse_at,
                    (
                        SELECT u7.login
                        FROM User u7 LEFT JOIN UserPlus up7 ON u7.identifier=up7.user_id
                        WHERE u7.identifier =  d.liberte_action_by
                    ) as liberte_action_login
                    FROM Delivery d
                    INNER JOIN Article a ON a.delivery_id = d.id
                    INNER JOIN User u ON u.identifier = d.user_id
                    LEFT JOIN UserPlus up ON up.user_id = d.user_id
                    LEFT JOIN Client cl ON cl.user_id = u.identifier
                    WHERE d.premium_option = '0'
                    AND ".$condition."
                    AND d.created_by != 'BO'";
        //echo $query;exit;
        $result = $this->getQuery($query,true);
        return $result;
    }
    /////////get delivery list for new ao list page which are ongoing///////////
    public function getPaidNewAos()
    {
        $query="SELECT d.id, d.total_article FROM ".$this->_name." d WHERE d.premium_option = '0' AND d.status_bo = 'published'  AND d.created_by !='BO' AND ".$this->visibility." ";
        //echo $query;
        $result = $this->getQuery($query,true);
        return $result;
    }
    /////////get delivery list for new ao list page which are paid by client///////////
    public function loadgetPaidNewAos($sWhere, $sOrder, $sLimit)
    {
         $query="SELECT d.id, d.total_article FROM ".$this->_name." d
                    INNER JOIN Article a ON a.delivery_id=d.id
                    INNER JOIN Participation p ON a.id=p.article_id
        WHERE d.premium_option = '0' AND p.status='published' AND p.current_stage='client'  AND d.created_by !='BO' AND ".$this->visibility." ".$sWhere." ".$sOrder." ".$sLimit."";
       // echo $query;
        $result = $this->getQuery($query,true);
        return $result;
    }
    /////////get delivery list for new ao list page which are ongoing///////////
    public function getNewNonpremPublishAos($aoIds)
    {
        $ids = join(',',$aoIds);
        $query="SELECT d.id, d.id as delId, d.title, d.delivery_date, d.total_article, d.created_at, d.language, u.email,
              u.login,d.user_id, up.first_name,d.total_amount,d.created_by,d.quoteid, cl.company_name, u.created_at AS doj
                FROM ".$this->_name." d
                INNER JOIN User u ON u.identifier=d.user_id
                LEFT JOIN UserPlus up ON up.user_id=d.user_id
                LEFT JOIN Client cl ON cl.user_id = u.identifier
                WHERE d.id IN (".$ids.") ORDER BY d.created_at ASC"; //
        //echo $query; exit;
        $result = $this->getQuery($query,true);
        return $result;
    }

     ///////////get premium delivery list/////////////////////
   public function nonPremAosList($params)
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

           if($params['clientId']!='0')
           {
               $condition.= " AND d.user_id =".$params['clientId']."";
           }
       }
      $query="SELECT d.id, d.title, d.delivery_date, d.total_article, d.created_at, u.email,
              u.login,d.user_id, up.first_name,d.total_amount,d.created_by, cl.company_name, u.created_at AS doj
                FROM ".$this->_name." d
                INNER JOIN User u ON u.identifier=d.user_id
                LEFT JOIN UserPlus up ON up.user_id=d.user_id
                LEFT JOIN Client cl ON cl.user_id = u.identifier
                WHERE  d.e IS NULL  ".$condition." AND d.created_by !='BO'  ORDER BY d.created_at ASC";
        //echo $query;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
   }
    /*Get contributors to send mails when approved an AO**/
    public function getViewToOfAO($profiles)
    {
         $condition="WHERE u.status='Active' AND u.blackstatus='no' AND u.profile_type IN ('".$profiles."')";
         $query="select DISTINCT count(u.identifier) AS AoContributors, u.profile_type  FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /*Get contributors to send mails when approved an AO**/
    public function getViewToOfCrtAO($profiles)
    {
        $condition="WHERE u.status='Active' AND u.blackstatus='no' AND u.profile_type IN ('".$profiles."') AND u.profile_type2 IN ('".$profiles."')";
        $query="select DISTINCT count(u.identifier) AS AoCorrectors, u.profile_type, u.profile_type2  FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	public function getDeliverylist()
	{
		$SelQuery="SELECT id,title FROM Delivery";
		$resultamt = $this->getQuery($SelQuery,true);
		return $resultamt;
	}
	
	/**To List ALL Missions**/
    public function listAllMissions($searchParameters)//
    {

        $where='';
        if($searchParameters['pay_status']!=NULL && $searchParameters['pay_status']!='all')
        {
            $where.=" AND a.paid_status='".$searchParameters['pay_status']."'";
        }
        if($searchParameters['mission_list']!=NULL && $searchParameters['mission_list']!='0')
        {
            //$aolist = implode(',',$searchParameters['mission_list']);
            $where.= " AND d.id  IN (".$searchParameters['mission_list'].")";
            // echo $where.=" AND d.id IN ('".$searchParameters['ao_list']."')";
        }

        if($searchParameters['client_list']!=NULL && $searchParameters['client_list']!='0')
        {
            $where.=" AND d.user_id='".$searchParameters['client_list']."'";
        }
        
        if($searchParameters['start_date']!=NULL && $searchParameters['end_date']!=NULL)
        {
            $searchParameters['start_date'];
            $searchParameters['end_date'];
            $start_date = str_replace('/','-',$searchParameters['start_date']);
            $end_date = str_replace('/','-',$searchParameters['end_date']);
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));

            $where.= " AND (d.created_at BETWEEN '".$start_date."' AND '".$end_date."')";
        }
        if($searchParameters['sorttype']!='all')   
        {
            if($searchParameters['sorttype']=="ongoing")
				$where.=" AND p.status IN ('bid','under_study','disapproved','plag_exec')";
			elseif($searchParameters['sorttype']=="new")	
				$where.=" AND a.id NOT IN (SELECT a.id FROM Article a INNER JOIN Participation p ON a.id=p.article_id)";
			elseif($searchParameters['sorttype']=="published")	
				$where.=" AND p.status IN ('published')";
			elseif($searchParameters['sorttype']=="upcoming")	
				$where.=" AND (d.status_bo IS NULL OR (d.publishtime IS NOT NULL AND d.publishtime>UNIX_TIMESTAMP()))";	
        }
          $allAODetails="select a.id AS art_id, a.title, a.price_final, SUM(a.price_final*(a.contrib_percentage/100)) as margin, p.price_user, up.first_name, up.last_name, d.id AS delid, d.title AS delname, d.publishtime,d.status_bo,a.created_at,
                    cl.company_name, d.user_id, p.id AS partId, u.email, u.password,  count(p.user_id) AS userCount, GROUP_CONCAT(p.status SEPARATOR '@') AS status, p.cycle, a.invoice_id, a.paid_status, a.contrib_percentage FROM Article a
                INNER JOIN Delivery d ON d.id=a.delivery_id
                LEFT JOIN Participation p ON a.id=p.article_id
                LEFT JOIN User u ON d.user_id=u.identifier
                LEFT JOIN UserPlus up ON up.user_id=p.user_id
                LEFT JOIN Client cl ON u.identifier=cl.user_id
                WHERE d.premium_option ='0' AND a.mission_closed IS NULL ".$where."
                Group BY a.id
                ORDER BY a.created_at DESC";
       
			
        if(($result = $this->getQuery($allAODetails,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	 ////////get article proposed price////
    public function getartproposedprice($article_id)//
    {
        if(($result = $this->getQuery("select price_user FROM Participation WHERE article_id = '".$article_id."' AND status NOT IN ('bid_premium','bid_nonpremium') AND cycle = '0' ",true)) != NULL)
        {
            return $result;
        }
        else
        {
            if(($result = $this->getQuery("select max(price_user) AS price_user FROM Participation WHERE article_id = '".$article_id."' AND status IN ('bid_premium','bid_nonpremium') AND cycle = '0' ",true)) != NULL)
            {
                return $result;
            }
            else
            {
                return "NO";
            }
        }
    }
	
	 ////////get article payment details////
    public function getartpaymentinfo($invoice_id)//
    {
        $query="select * FROM Payment_article WHERE id = '".$invoice_id."'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	 ////////get participation detials on status and artid////
    public function getpartdetialsonstatus($artId, $status)//
    {
        $query="select *, count(user_id) AS userCount FROM Participation WHERE article_id = '".$artId."' AND status ='".$status."' AND cycle='0'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	
	public function getMissionlist($clnt=NULL,$paid=NULL)
    {
        $where=" WHERE d.premium_option='0'";
		if($clnt)
            $where.=" AND d.user_id='".$clnt."'";

        if($paid)
            $where.=" AND p.status='Paid'";
       


        $SelQuery="SELECT d.id,d.title FROM Delivery d LEFT JOIN Payment p ON d.id=p.delivery_id $where ";
        $resultamt = $this->getQuery($SelQuery,true);
        return $resultamt;
    }
    /* get ao list for client*/
    public function getAOviewinfo ($id)
    {
        if($id)
        {
            $query = "SELECT id, title, user_id, total_article, created_at, status_bo, premium_option, AOtype  FROM Delivery WHERE user_id= '" . $id . "'";
        }
        else
        {
            $query = "SELECT id, title, user_id, total_article, created_at, status_bo, premium_option, AOtype FROM Delivery ";
        }
        /* Fetching AO details */
        foreach ( $this->getQuery($query,true) as $ao ) :
            $ao['created_at']    =   date( "d/m/Y", strtotime( $ao['created_at'] ) ) ;
            $ao['premium_option']    =   $ao['premium_option'] ? 'premium' : 'non-premium' ;
           // $ao['client']    =   $this->getClientName( $ao['user_id'] ) ;
            $result[]   =   $ao ;
        endforeach ;
        return $result;
    }
    
    /* *Get contributors to send mails when approved an AO**/
    public function getCorrectorsWritersAO($profiles, $artlang)
    {
		//echo $profiles." ".$artlang;
        $profs=explode(",",$profiles);
        $proflist=array();
        for($p=0;$p<count($profs);$p++)
        {
            if($profs[$p]=="CB"){
                $proflistcrt[]="junior";
                $proflistcrt[]="senior"; }
            elseif($profs[$p]=="CJC")
                $proflistcrt[]="junior";
            elseif($profs[$p]=="CSC")
                $proflistcrt[]="senior";
            if($profs[$p]=="WB"){
                $proflistwrt[]="junior";
                $proflistwrt[]="senior"; }
            elseif($profs[$p]=="WJC")
                $proflistwrt[]="junior";
            elseif($profs[$p]=="WSC")
                $proflistwrt[]="senior";
        }
        $profilecrt = implode("','",$proflistcrt);
        $profilewrt = implode("','",$proflistwrt);
       // $langcrt = implode("','",$artlang);
        $langcrt = $artlang;
        $condition1="WHERE u.status='Active' AND u.blackstatus='no' AND u.type = 'contributor' AND u.type2 = 'corrector' AND u.profile_type2 IN ('".$profilecrt."') AND c.language IN ('".$langcrt."') ";
        $condition2="WHERE status='Active' AND blackstatus='no' AND type = 'contributor' AND type2 IS NULL AND profile_type IN ('".$profilewrt."')";
        $query="(select DISTINCT u.identifier, u.email, u.type2, u.profile_type2, u.profile_type  FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition1.") UNION
                    (select DISTINCT u.identifier, u.email, u.type2, u.profile_type2, u.profile_type  FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition2.")";
		
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	/* added by naseer on 03.12.2015 */
    //Get contributors who are translator to send mails when approved an AO//
    public function getCorrectorsWritersAOTranslation($profiles, $artlang,$sourcelang_nocheck_correction=NULL)
    {
       // echo $profiles." ".$artlang;
        $profs=explode(",",$profiles);
        $proflist=array();
        for($p=0;$p<count($profs);$p++)
        {
            if($profs[$p]=="CB"){
                $proflistcrt[]="junior";
                $proflistcrt[]="senior"; }
            elseif($profs[$p]=="CJC")
                $proflistcrt[]="junior";
            elseif($profs[$p]=="CSC")
                $proflistcrt[]="senior";
            if($profs[$p]=="WB"){
                $proflistwrt[]="junior";
                $proflistwrt[]="senior"; }
            elseif($profs[$p]=="WJC")
                $proflistwrt[]="junior";
            elseif($profs[$p]=="WSC")
                $proflistwrt[]="senior";
        }
        $profilecrt = implode("','",$proflistcrt);
        $profilewrt = implode("','",$proflistwrt);
        // $langcrt = implode("','",$artlang);
        $langcrt = $artlang;
        $condition1="WHERE u.status='Active' AND u.blackstatus='no' AND u.type = 'contributor' AND u.type2 = 'corrector' AND u.profile_type2 IN ('".$profilecrt."') AND c.language IN ('".$langcrt."') AND c.translator = 'yes'";
        $condition2="WHERE status='Active' AND blackstatus='no' AND type = 'contributor' AND type2 IS NULL AND profile_type IN ('".$profilewrt."')";
        $query="(select DISTINCT u.identifier, u.email, u.type2, u.profile_type2, u.profile_type,
                    c.language_more
                    FROM User u
                    LEFT JOIN Contributor c ON u.identifier=c.user_id
                    ".$condition1.") UNION
                    (select DISTINCT u.identifier, u.email, u.type2, u.profile_type2, u.profile_type,
                        c.language_more
                        FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id ".$condition2.")";
        //echo "$query";exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /* end of added by naseer on 03.12.2015 */
	public function checkfbpost($client)
	{
		$today=date("Y-m-d");
		$tommorrow=date('Y-m-d', strtotime("+1 days"));
		$ChkQuery="SELECT * FROM Delivery WHERE ((publishtime IS NOT NULL AND FROM_UNIXTIME(publishtime, '%Y-%m-%d')='".$today."') OR (publishtime IS NULL AND DATE_FORMAT(created_at, '%Y-%m-%d')='".$today."')) AND postoftheday='yes' AND user_id='".$client."'";
		$Chkresult = $this->getQuery($ChkQuery,true);
		if(count($Chkresult)>0)
			return "yes";
		else
			return "no";
	}	

    public function getCorrectorsByLang($lang="all")
    {
        $black=$this->getConfiguration('blacked_writers');
        $where1="";

        if($black=='no')
            $where1=" AND u.blackstatus='no' ";       
        if(count($lang)>0 && $lang!='all')
        {
            $lang1=explode(",",$lang);
            $lang2=implode("','",$lang1);
            $where1.=" AND c.language IN ('".$lang2."')";
        }   
        
        $SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u 
                            LEFT JOIN UserPlus up ON u.identifier=up.user_id
                            LEFT JOIN Contributor c ON u.identifier=c.user_id
                            where u.type='contributor' AND u.status='Active' AND u.type2='corrector' ".$where1;
        $resultall = $this->getQuery($SelectallContrib,true);
        return $resultall;
    }
    /*added by naseer on 04.12.2015*/
   /* public function getTranslatorCorrectorsByLang($lang="all")
    {
        $black=$this->getConfiguration('blacked_writers');
        $where1="";

        if($black=='no')
            $where1=" AND u.blackstatus='no' ";
        if(count($lang)>0 && $lang!='all')
        {
            $lang1=explode(",",$lang);
            $lang2=implode("','",$lang1);
            $where1.=" AND c.language IN ('".$lang2."')";
        }

        $SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u
                            LEFT JOIN UserPlus up ON u.identifier=up.user_id
                            LEFT JOIN Contributor c ON u.identifier=c.user_id
                            where u.type='contributor' AND u.status='Active' AND u.type2='corrector' AND c.translator = 'yes'".$where1;
        $resultall = $this->getQuery($SelectallContrib,true);
        return $resultall;
    }*/
	
	 public function getTranslatorCorrectorsByLang($type,$lang="all",$srclang,$srccheck)
    {
        $black=$this->getConfiguration('blacked_writers');
        $where1="";

        if($black=='no')
            $where1=" AND u.blackstatus='no' ";
        if(count($lang)>0 && $lang!='all')
        {
            $lang1=explode(",",$lang);
            $lang2=implode("','",$lang1);
            $where1.=" AND c.language IN ('".$lang2."')";
        }
		
		if($type!=NULL && is_array($type))
		{
			$profile_type = $type;
				if(in_array('sc',$profile_type) || in_array('CB',$profile_type) || in_array('CSC',$profile_type))
				$where .= " OR u.profile_type2='senior'";
				if(in_array('jc',$profile_type) || in_array('CB',$profile_type) || in_array('CJC',$profile_type))
				$where .= " OR u.profile_type2='junior'";				
			$where=substr($where,4);
			$where1.=" AND ($where)";
		}
		
        if($srccheck=='yes')
		{
			$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u
								LEFT JOIN UserPlus up ON u.identifier=up.user_id
								LEFT JOIN Contributor c ON u.identifier=c.user_id
								where u.type='contributor' AND u.status='Active' AND u.type2='corrector' AND c.translator = 'yes'".$where1;
			$resultall = $this->getQuery($SelectallContrib,true);
			return $resultall;
		}
		else
		{
			$SelectallContrib="SELECT identifier,email,first_name,last_name,c.language_more FROM User u
								LEFT JOIN UserPlus up ON u.identifier=up.user_id
								LEFT JOIN Contributor c ON u.identifier=c.user_id
								where u.type='contributor' AND u.status='Active' AND u.type2='corrector' AND c.translator = 'yes'".$where1;
			$resultall = $this->getQuery($SelectallContrib,true);
			$var=0;
				foreach($resultall as $writer)
				{
					$sourcearray=unserialize($writer['language_more']);
					if($sourcearray[$srclang]>=50)
					{
						$resultArray[$var]['identifier']=$writer['identifier'];
						$resultArray[$var]['email']=$writer['email'];
						$resultArray[$var]['first_name']=$writer['first_name'];
						$resultArray[$var]['last_name']=$writer['last_name'];
						$var++;
					}
				}
				return $resultArray;
		}
    }
	
	public function getPremiumQuotes($id="")
	{
		$where="";
		if($id!="")
			$where=" WHERE p.id='".$id."'";
			
		$query1="SELECT 
						p.*,u.email,up.last_name,up.first_name,c.company_name,c.website,up.phone_number,c.category
				FROM 
					PremiumQuotes p INNER JOIN User u ON p.user_id=u.identifier
					LEFT JOIN UserPlus up ON u.identifier=up.user_id
					LEFT JOIN Client c ON u.identifier=c.user_id".$where." ORDER BY p.created_at DESC"; 
       
		if(($result1 = $this->getQuery($query1,true)) != NULL)
            return $result1;
	}
	
	public function missionarticlelist($ao)
	{
		$query3="SELECT a.id,a.title, d.title as dtitle FROM Article a INNER JOIN Delivery d ON d.id=a.delivery_id WHERE a.delivery_id='".$ao."' GROUP BY a.id";
       
		$result3 = $this->getQuery($query3,true);
        return $result3;
	}
	
	public function missionselectedcontribs($article)
	{
		$query2="SELECT 
						d.id,d.title as dtitle,a.id,a.title,p.status,p.price_user,u.profile_type,up.first_name,up.last_name,p.selection_type 
				FROM 
						Delivery d INNER JOIN Article a ON d.id=a.delivery_id 
						LEFT JOIN Participation p ON a.id=p.article_id 
						LEFT JOIN User u ON u.identifier=p.user_id 
						LEFT JOIN UserPlus up ON up.user_id=u.identifier 
				WHERE 
						a.id='".$article."' AND (p.status IS NULL OR (p.status IN ('bid','under_study','time_out','published','plag_exec','disapproved', 'disapproved_temp', 'disapproved_client','closed_temp'))) AND (p.cycle IS NULL OR p.cycle=0) GROUP BY a.id";
       
		$result2 = $this->getQuery($query2,true);
        return $result2;
	}
	
	public function missionselectedcorrectors($article)
	{
		$query2="SELECT 
						d.id,d.title as dtitle,a.id,a.title,p.status,p.price_corrector,u.profile_type2,up.first_name,up.last_name,p.selection_type
							
				FROM 
						Delivery d INNER JOIN Article a ON d.id=a.delivery_id 
						LEFT JOIN CorrectorParticipation p ON a.id=p.article_id 
						LEFT JOIN User u ON u.identifier=p.corrector_id  
						LEFT JOIN UserPlus up ON up.user_id=u.identifier 
				WHERE 
						a.id='".$article."' AND (p.status IS NULL OR (p.status IN ('bid','under_study','published','disapproved'))) AND (p.cycle IS NULL OR p.cycle=0) GROUP BY a.id";
       
		$result2 = $this->getQuery($query2,true);
        return $result2;
	}
	
	public function getAllContribArticleTest($type,$cat="all",$lang="all",$test,$marks)
	{   
		$black=$this->getConfiguration('blacked_writers');
		$where1="";
		
		$type=implode("','",$type);
		if($black=='no')
			$where1=" AND u.blackstatus='no' ";
		$where1.=" AND ( u.profile_type IN ('".stripslashes($type)."'))";
		//echo $lang;
		if(is_array($lang)){
			//echo "ITS AN ARRAY";
			
			
				$lang=implode("','",$lang);
			
			$where1.=" AND c.language IN ('".$lang."')";
		}else{
		if(count($lang)>0 && $lang!='all')
			{
				$lang1=explode(",",$lang);
				$lang2=implode("','",$lang1);
				$where1.=" AND c.language IN ('".$lang2."')";
			}
		}
		if($cat!="all" && $cat!="")
			$where1.=" AND c.favourite_category like '%".$cat."%'";
			
		if($test=="yes")
		{
			$where1.=" AND c.contributortest='yes'";
			
			if($marks!="")
				$where1.=" AND c.contributortestmarks>='".$marks."'";
		}		
		$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Contributor c ON u.identifier=c.user_id where u.type='contributor' AND u.status='Active' ".$where1; //echo $SelectallContrib;exit;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
	
	public function getAllContribArticle($type,$cat="all",$lang="all")
	{   
		$black=$this->getConfiguration('blacked_writers');
		$where1="";
		
		$type=implode("','",$type);
		if($black=='no')
			$where1=" AND u.blackstatus='no' ";
		$where1.=" AND ( u.profile_type IN ('".stripslashes($type)."'))";
		
		if(is_array($lang)){
			$lang=implode("','",$lang);
			$where1.=" AND c.language IN ('".$lang."')";
		}else{
		if(count($lang)>0 && $lang!='all')
			{
				$lang1=explode(",",$lang);
				$lang2=implode("','",$lang1);
				$where1.=" AND c.language IN ('".$lang2."')";
			}
		}
		if($cat!="all" && $cat!="")
			$where1.=" AND c.favourite_category like '%".$cat."%'";
			
			
		$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Contributor c ON u.identifier=c.user_id where u.type='contributor' AND u.status='Active' ".$where1; //echo $SelectallContrib;exit;
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
    /////////get delivery full detials  w.r.t article id  ///////////////////////////
    public function getAllContributorPartOnLiberteAo($aoId)
    {
         $query = "select a.id AS artId, a.title as articleName, u.identifier, u.email, up.first_name, up.last_name,
                  p.price_user, p.valid_date, p.created_at, a.contrib_percentage from Article a
		 INNER JOIN Participation p ON p.article_id=a.id
		 INNER JOIN User u ON p.user_id=u.identifier
		 INNER JOIN UserPlus up ON up.user_id=u.identifier
    	 WHERE a.delivery_id=".$aoId. " ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /*public function getAllContributorPartOnLiberteAo($aoId)
    {
        $query = "select d.id, a.id AS artId, a.title as articleName, d.title AS deliveryTitle, u.identifier, u.email, up.first_name, up.last_name, p.price_user from ".$this->_name." d
		 INNER JOIN Article a ON d.id=a.delivery_id
		 INNER JOIN Participation p ON p.article_id=a.id
		 INNER JOIN User u ON p.user_id=u.identifier
		 INNER JOIN UserPlus up ON up.user_id=u.identifier
    	 WHERE d.id=".$aoId. " group by d.id ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }*/
    /* added by naseer on 16.08.2015 */
    public function checkStencilsEbooker($aoId){
        $query = "SELECT `stencils_ebooker`
        FROM  `Delivery`
        WHERE `id` = '".$aoId."'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result[0]['stencils_ebooker'];
        else
            return "NO";
    }
    /* end of added by naseer on 16.08.2015 */
	
	 public function getContribsByTypeLangCatTranslation($type,$cat="all",$lang,$srclang,$srccheck)
    {
        $black=$this->getConfiguration('blacked_writers');
        $where1="";
        if($black=='no')
            $where1=" AND u.blackstatus='no' ";
        $where1.=" AND ( c.translator_type IN ('".stripslashes($type)."'))";
        if($lang!='NULL' && $lang!='')
        {
            $lang=explode(",",$lang);
            $lang=implode("','",$lang);
            $where1.=" AND c.language IN ('".$lang."')";
        }
        if($cat!="all" && $cat!="")
            $where1.=" AND c.favourite_category like '%".$cat."%'";
			
			if($srccheck=='yes')
			{	
				$SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Contributor c ON u.identifier=c.user_id where u.type='contributor' AND u.status='Active' AND c.translator='yes'".$where1;
				$resultall = $this->getQuery($SelectallContrib,true);
				return $resultall;
			}
			else
			{
				$SelectallContrib="SELECT identifier,email,first_name,last_name,c.language_more FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Contributor c ON u.identifier=c.user_id where u.type='contributor' AND u.status='Active' AND c.translator='yes'".$where1;
				$resultall = $this->getQuery($SelectallContrib,true);
				$resultArray=array();
				
				$var=0;
				foreach($resultall as $writer)
				{
					$sourcearray=unserialize($writer['language_more']);
					if($sourcearray[$srclang]>=50)
					{
						$resultArray[$var]['identifier']=$writer['identifier'];
						$resultArray[$var]['email']=$writer['email'];
						$resultArray[$var]['first_name']=$writer['first_name'];
						$resultArray[$var]['last_name']=$writer['last_name'];
						$var++;
					}
				}
				return $resultArray;
			}
    }
	
	public function getCorrectorsByLangType($type,$lang="all")
    {
        $black=$this->getConfiguration('blacked_writers');
        $where1="";

        if($black=='no')
            $where1=" AND u.blackstatus='no' ";       
        if(count($lang)>0 && $lang!='all')
        {
            $lang1=explode(",",$lang);
            $lang2=implode("','",$lang1);
            $where1.=" AND c.language IN ('".$lang2."')";
        }   
        
		if($type!=NULL && is_array($type))
		{
			$profile_type = $type;
				if(in_array('sc',$profile_type))
				$where .= " OR u.profile_type2='senior'";
				if(in_array('jc',$profile_type))
				$where .= " OR u.profile_type2='junior'";
			$where=substr($where,4);

			$where1.=" AND ($where)";
		}	
		
		
        $SelectallContrib="SELECT identifier,email,first_name,last_name FROM User u 
                            LEFT JOIN UserPlus up ON u.identifier=up.user_id
                            LEFT JOIN Contributor c ON u.identifier=c.user_id
                            where u.type='contributor' AND u.status='Active' AND u.type2='corrector' ".$where1;
        $resultall = $this->getQuery($SelectallContrib,true);
        return $resultall;
    }
}




