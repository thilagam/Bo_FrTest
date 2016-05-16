<?php

/**
 * Profile Contributor Model
 * This Model  is responsible for Profile actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_User_Contributor extends Ep_Db_Identifier
{
	protected $_name = 'Contributor';
	private $user_id;
    private $dob;
    private $profession;
    private $university;
    private $education;
    private $degree;
    private $language;
    private $nationality;
    private $favourite_category;
    private $payment_type;

	public function loadData($array)
	{
		$this->user_id=$array["user_id"] ;
        $this->dob=$array["dob"] ;
		$this->profession=$array["profession"] ;
		$this->university=$array["university"] ;
		$this->education=$array["education"] ;
        $this->degree=$array["degree"] ;
		$this->language=$array["language"] ;
		$this->nationality=$array["nationality"] ;
		$this->favourite_category=$array["favourite_category"] ;
        $this->payment_type=$array["payment_type"] ;
        return $this;
	}

	public function loadintoArray()
	{
		$array = array();
        $array["user_id"] =  $this->user_id;
        $array["dob"] = $this->dob;
		$array["profession"] = $this->profession;
		$array["university"] = $this->university;
        $array["education"] = $this->education;
		$array["degree"] = $this->degree;
        $array["language"] = $this->language;
		$array["nationality"] = $this->nationality;
    	$array["favourite_category"] = $this->favourite_category;
		$array["payment_type"]=$this->payment_type;
        return $array;
	}

	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    ////////fetch the contributor info in popup//////////////
    public function getContributorInfo($profile_identifier, $partId)
    {
        if($partId != NULL)
            $whereQuery = "c.user_id = '".$profile_identifier."' AND p.id = '".$partId."' AND ap.user_id = '".$profile_identifier."' AND p.status NOT IN ('bid_premium')";
         else
            $whereQuery = "c.user_id = '".$profile_identifier."' AND p.status NOT IN ('bid_premium')";
	      $profileQuery = "select c.profession,c.profession_other, c.degree, c.language, c.favourite_category, count(p.id) AS no_paritcipations, p.price_user, u.identifier, u.email, u.type2, u.profile_type2, up.first_name, up.last_name, max(ap.article_sent_at) AS recentTime  from ".$this->_name." c
	                     INNER JOIN User u ON c.user_id=u.identifier INNER JOIN UserPlus up ON c.user_id=up.user_id
	                     INNER JOIN Participation p ON u.identifier=p.user_id
	                     LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id
	                     where ".$whereQuery;

        $profileQuery2 = "select count(id) AS no_approved from Participation where user_id = '".$profile_identifier."' AND status='published'";
        $profileQuery3 = "select count(id) AS no_disapproved from Participation where user_id = '".$profile_identifier."' AND status='disapproved'";

		$result = $this->getQuery($profileQuery,true);
		$result2 = $this->getQuery($profileQuery2,true);
        $result3 = $this->getQuery($profileQuery3,true);
        $result4=array_merge($result, $result2, $result3);
        return $result4;
	}
    ////////fetch the corrector info in tool tip popup//////////////
    public function getCorrectorInfo($profile_identifier, $partId)
    {
        if($partId != NULL)
            $whereQuery = "c.user_id = '".$profile_identifier."' AND cp.id = '".$partId."' AND ap.user_id = '".$profile_identifier."' AND cp.status NOT IN ('bid_corrector')";
        else
            $whereQuery = "c.user_id = '".$profile_identifier."' AND cp.status NOT IN ('bid_corrector')";
       echo  $profileQuery = "select c.profession,c.profession_other, c.degree, c.language, c.favourite_category, count(cp.id) AS no_paritcipations, cp.price_user, u.identifier, u.email, up.first_name, up.last_name, max(ap.article_sent_at) AS recentTime  from ".$this->_name." c
	                     INNER JOIN User u ON c.user_id=u.identifier INNER JOIN UserPlus up ON c.user_id=up.user_id
	                     INNER JOIN CorrectorParticipation cp ON u.identifier=cp.corrector_id
	                     LEFT JOIN ArticleProcess ap ON cp.id=ap.participate_id
	                     where ".$whereQuery;

        $profileQuery2 = "select count(id) AS no_approved from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='published'";
        $profileQuery3 = "select count(id) AS no_disapproved from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='disapproved'";

        $result = $this->getQuery($profileQuery,true);
        $result2 = $this->getQuery($profileQuery2,true);
        $result3 = $this->getQuery($profileQuery3,true);
        $result4=array_merge($result, $result2, $result3);
        return $result4;
    }
    ////////fetch the contributor info in popup//////////////
    public function getGroupProfilesInfo($profile_identifier, $partId, $Artid)
    {
         $whereQuery = "c.user_id = '".$profile_identifier."' AND p.id='".$partId."'";
        $profileQuery = "select c.profession,c.profession_other, c.degree, c.language, c.favourite_category, c.category_more,c.language_more,c.contributortest,c.contributortestcomment,c.contributortestmarks,
                     p.id AS partId, p.status, p.current_stage, p.price_user,p.created_at, p.accept_refuse_at,u.created_at AS doj,
                     d.delivery_date, d.id AS delId, u.identifier, u.email, u.blackstatus, u.profile_type, up.first_name,d.poll_id,d.premium_option,
                     up.last_name,a.id,a.title,a.price_min,a.price_max,a.contrib_percentage,a.participation_expires,p.cycle,a.product,c.translator_type from ".$this->_name." c
                     INNER JOIN User u ON c.user_id=u.identifier
                     INNER JOIN UserPlus up ON c.user_id=up.user_id
                     INNER JOIN Participation p ON u.identifier=p.user_id
                     INNER JOIN Article a ON a.id=p.article_id
                     INNER JOIN Delivery d ON d.id=a.delivery_id
                     where ".$whereQuery." order by p.cycle DESC";
        $profileQuery2 = "select count(id) AS no_approved from Participation where user_id = '".$profile_identifier."' AND status='published'";
        $profileQuery3 = "select count(id) AS no_disapproved from Participation where user_id = '".$profile_identifier."' AND status='disapproved'";
        $profileQuery4 = "select count(user_id) AS no_paritcipations from Participation where user_id = '".$profile_identifier."'";
        $profileQuery5 = "select count(user_id) AS not_validated from Participation where user_id = '".$profile_identifier."' AND status='bid_premium'";
        $profileQuery6 = "select count(user_id) AS validated from Participation where user_id = '".$profile_identifier."' AND status='bid'";
        $profileQuery7 = "select count(user_id) AS under_s1 from Participation where user_id = '".$profile_identifier."' AND status='under_study' AND current_stage='stage1'";
        $profileQuery8 = "select count(user_id) AS under_s2 from Participation where user_id = '".$profile_identifier."' AND status='under_study' AND current_stage='stage2'";
		$profileQuery9 = "select count(user_id) AS time_out from Participation where user_id = '".$profile_identifier."' AND status='time_out'";
		$profileQuery10 = "select pp.price_user AS poll_price from Delivery d INNER JOIN Article a ON d.id=a.delivery_id INNER JOIN Poll_Participation pp ON d.poll_id=pp.poll_id where pp.user_id = '".$profile_identifier."' AND a.id=".$Artid;
        $profileQuery11 = "select count(DISTINCT p.id) AS closed from Participation p INNER JOIN ArticleProcess ap ON p.id = ap.participate_id where p.user_id = '".$profile_identifier."' AND p.current_stage IN ('stage1','stage2') AND p.status='closed'";
        $result = $this->getQuery($profileQuery,true);
		$result2 = $this->getQuery($profileQuery2,true);
        $result4 = $this->getQuery($profileQuery4,true);
        $result3 = $this->getQuery($profileQuery3,true);
        $result5 = $this->getQuery($profileQuery5,true);
        $result6 = $this->getQuery($profileQuery6,true);
        $result7 = $this->getQuery($profileQuery7,true);
        $result8 = $this->getQuery($profileQuery8,true);
        $result9 = $this->getQuery($profileQuery9,true);
		$result10 = $this->getQuery($profileQuery10,true);
        $result11 = $this->getQuery($profileQuery11,true);
        $result12=array_merge($result, $result2, $result3, $result4, $result5, $result6, $result7, $result8, $result9,$result10,$result11);
        return $result12; exit;
	}
    /////get the contributro no. of participations in perticular ao////
    public function contribPartsInAo($userId, $delId)
    {
        $query = "SELECT count(article_id) AS partscount FROM Participation p INNER JOIN Article a ON a.id=p.article_id
                INNER JOIN Delivery d ON d.id = a.delivery_id WHERE p.user_id='".$userId."' AND d.id='".$delId."' AND
             p.status  IN ('bid', 'under_study','published','plag_exec', 'disapproved', 'disapproved_temp', 'disapproved_client')";
            if(($result = $this->getQuery($query,true)) != NULL)
                return $result;
            else
                return "NO";
    }
    /////get the contributro no. of participations in perticular ao////
    public function contribCrtPartsInAo($userId, $delId)
    {
        $query = "SELECT count(article_id) AS partscount FROM CorrectorParticipation cp INNER JOIN Article a ON a.id=cp.article_id
                INNER JOIN Delivery d ON d.id = a.delivery_id WHERE cp.corrector_id='".$userId."' AND d.id='".$delId."' AND
             cp.status IN ('bid', 'under_study','published','plag_exec', 'disapproved', 'disapproved_temp', 'disapproved_client')";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ////////fetch the correctors info in popup//////////////
    public function getGroupCrtProfilesInfo($profile_identifier, $partId, $Artid)
    {
        $whereQuery = "c.user_id = '".$profile_identifier."' AND cp.id='".$partId."' ";

        $profileQuery = "select c.profession,c.profession_other, c.degree, c.language, c.favourite_category, c.category_more,c.language_more,
                     cp.id AS partId, cp.status, cp.current_stage, cp.price_corrector,cp.created_at, cp.accept_refuse_at, u.created_at AS doj,
                     d.delivery_date, u.identifier, u.email, u.blackstatus, u.profile_type,u.profile_type2, u.type2, up.first_name,d.poll_id,d.premium_option,
                     up.last_name,a.id,a.title,a.price_min,a.price_max,a.correction_pricemin,a.correction_pricemax,a.contrib_percentage,a.correction_participationexpires,cp.cycle from ".$this->_name." c
                     INNER JOIN User u ON c.user_id=u.identifier
                     INNER JOIN UserPlus up ON c.user_id=up.user_id
                     INNER JOIN CorrectorParticipation cp ON u.identifier=cp.corrector_id
                     INNER JOIN Article a ON a.id=cp.article_id
                     INNER JOIN Delivery d ON d.id=a.delivery_id
                     where ".$whereQuery." order by cp.created_at";
        $profileQuery2 = "select count(id) AS no_approved from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='published'";
        $profileQuery3 = "select count(id) AS no_disapproved from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='disapproved'";
        $profileQuery4 = "select count(corrector_id) AS no_paritcipations from CorrectorParticipation where corrector_id = '".$profile_identifier."'";
        $profileQuery5 = "select count(corrector_id) AS not_validated from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='bid_corrector'";
        $profileQuery6 = "select count(corrector_id) AS validated from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='bid'";
        $profileQuery7 = "select count(corrector_id) AS under_s1 from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='under_study' AND current_stage='stage1'";
        $profileQuery8 = "select count(corrector_id) AS under_s2 from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='under_study' AND current_stage='stage2'";
        $profileQuery9 = "select count(corrector_id) AS time_out from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='time_out'";
        $profileQuery10 = "select pp.price_user AS poll_price from Delivery d INNER JOIN Article a ON d.id=a.delivery_id INNER JOIN Poll_Participation pp ON d.poll_id=pp.poll_id where pp.user_id = '".$profile_identifier."' AND a.id=".$Artid;
        $result = $this->getQuery($profileQuery,true);
        $result2 = $this->getQuery($profileQuery2,true);
        $result4 = $this->getQuery($profileQuery4,true);
        $result3 = $this->getQuery($profileQuery3,true);
        $result5 = $this->getQuery($profileQuery5,true);
        $result6 = $this->getQuery($profileQuery6,true);
        $result7 = $this->getQuery($profileQuery7,true);
        $result8 = $this->getQuery($profileQuery8,true);
        $result9 = $this->getQuery($profileQuery9,true);
        $result10 = $this->getQuery($profileQuery10,true);
        $result11=array_merge($result, $result2, $result3, $result4, $result5, $result6, $result7, $result8, $result9,$result10);
        return $result11;
    }
    /////contributors with companies///////////
    public function getContribWorkedCompanies($profile_identifier)
    {
        $profileQuery = "select c.company_name AS contrib_workedwith from Participation p
                                INNER JOIN Article a ON a.id=p.article_id
                                INNER JOIN Delivery d ON d.id=a.delivery_id
                                INNER JOIN Client c ON c.user_id=d.user_id Where p.user_id =  '".$profile_identifier."' GROUP BY c.company_name";
        if(($result = $this->getQuery($profileQuery,true)) != NULL)
        {
            for($i=0; $i<=count($result); $i++)
            {
                $resarr[$i] = $result[$i]['contrib_workedwith'];
            }
            $result =  implode(",",$resarr);
            return $result;
        }
        else
            return "NO";
    }
    public function updateprofile($data,$identifier)
    {
        $where=" user_id='".$identifier."'";
        //print_r($data);exit;
        echo $this->updateQuery($data,$where);
    }

    /* Listing contributors info including AO & profile info */
    public function ListContributorsinfo()
    {
        $query="SELECT u.identifier,up.first_name,up.last_name,u.email,
                 u.profile_type,u.created_at,u.last_visit
                    FROM User u
                    INNER JOIN UserPlus up ON u.identifier = up.user_id
                    INNER JOIN Contributor c ON c.user_id = u.identifier
                    WHERE u.type = 'contributor'
                    AND u.status = 'Active'
                    ORDER BY u.updated_at DESC";

        /* Adding AO infos & date formatting */
        foreach ( $this->getQuery($query,true) as $contrib ) :
            $aoInfo =   $this->contribAoCount($contrib['identifier']) ;
            $contrib['ao_count']        =   $aoInfo['ao_count'] ;
            $contrib['ao_id']           =   $aoInfo['ao_id'] ;
            $contrib['company_name']    =   $aoInfo['company_name'] ;
            $contrib['created_at']      =   date( "d/m/Y", strtotime( $contrib['created_at'] ) ) ;
            $contrib['last_visit']      =   date( "d/m/Y" , strtotime( $contrib['last_visit'] ) ) ;
            $result[]   =   $contrib ;
        endforeach ;

        return $result;
    }

    /* Returns AO Count for a given user Id */
    public function contribAoCount($user_id)
    {
        $query="SELECT id AS ao_id, COUNT(*) AS ao_count, title AS company_name
                    FROM Delivery
                    WHERE contribs_list LIKE '%" . $user_id . "%'";
        $result = $this->getQuery($query,true);
        return $result[0];
    }

    public function getContributorcount($ptype)
    {
        $ContribQuery = "select count(*) as count FROM User WHERE profile_type IN ('".$ptype."') AND status='active' AND blackstatus='no'" ;
        if(($Contribresult = $this->getQuery($ContribQuery,true)) != NULL)
            return $Contribresult[0]['count'];
        else
            return 0;
    }

    public function getWritercount($ptype)
    {
        $ContribQuery = "select count(*) as count FROM User WHERE profile_type2 IN ('".$ptype."') AND status='active' AND blackstatus='no'" ;
        if(($Contribresult = $this->getQuery($ContribQuery,true)) != NULL)
            return $Contribresult[0]['count'];
        else
            return 0;
    }
    public function getWriterCountOnLang($ptype, $langs)
    {
        $langs = explode(",",$langs);
        $langs=implode("','",$langs);

        $ContribQuery = "select count(u.identifier) as count FROM User u INNER JOIN Contributor c ON u.identifier=c.user_id
                           WHERE u.profile_type IN ('".$ptype."') AND u.status='active' AND u.blackstatus='no' AND c.language IN ('".$langs."')" ;
						
        if(($Contribresult = $this->getQuery($ContribQuery,true)) != NULL)
            return $Contribresult[0]['count'];
        else
            return 0;
    }

    /**Author:Thilagam**/
    /**Date:13/5/2016**/
    /**Function:To get the list of participants who have already participated in the article**/
    public function getWriterRepublish($ptype,$langs)
    {
        $langs = explode(",",$langs);
        $langs=implode("','",$langs);

        $ContribQuery = "select u.identifier  FROM User u INNER JOIN Contributor c ON u.identifier=c.user_id
                           WHERE u.profile_type IN ('".$ptype."') AND u.status='active' AND u.blackstatus='no' AND c.language IN ('".$langs."')" ;

        if(($Contribresult = $this->getQuery($ContribQuery,true)) != NULL)
            return $Contribresult;
        else
            return array();
    }
    public function getContribIds($params)
    {
        $cat = explode("|", $params['categ']);
        if(sizeof($cat)>0)
        {
            foreach ($cat as $categ_) {
                $categ = explode("=", $categ_);
                $categs[str_replace("percentage_", "", $categ[0])]=$categ[1];
            }
        }
        $lngg = explode("|", $params['lng2']);
        if(sizeof($lngg)>0)
        {
            foreach ($lngg as $lng2_) {
                $lng2 = explode("=", $lng2_);
                $lng2s[str_replace("lang_percentage_", "", $lng2[0])]=$lng2[1];
            }
        }
        //print_r($categs);exit($params['categ']);
        @$ptype1 = $params['w'];
        @$ptype2 = $params['c'];
        $languageCond = $params['language'] ? (" AND c.language IN ('" . str_replace("\'", "'", $params['language']) . "')") : "" ;
        //$categoryCond = $params['category'] ? (" AND c.favourite_category like '%" . $params['category'] . "%'") : "" ;
        $categoryCond = ((sizeof($categs)>0) ? " AND (c.favourite_category LIKE '%" . implode("%' OR c.favourite_category LIKE '%", array_keys($categs)) . "%')" : "");
//exit($languageCond);
        if($ptype1)
        {
            $ContribQuery = "SELECT u.identifier, c.category_more, c.language_more FROM User u INNER JOIN UserPlus up ON u.identifier = up.user_id INNER JOIN Contributor c ON c.user_id = u.identifier WHERE u.type = 'contributor' AND u.status = 'Active' AND u.blackstatus='no' {$languageCond} {$categoryCond} AND (u.profile_type IN ('".str_replace(",", "','", $ptype1)."')" ;
            if($ptype2)
                $ContribQuery .= " AND ( u.profile_type IN ('senior','junior') AND u.profile_type2 IN ('".str_replace(",", "','", $ptype2)."') )" ;
            $ContribQuery .= ") ORDER BY u.updated_at DESC" ;
        }
        else if($ptype2)
        {
            $ContribQuery = "SELECT u.identifier, c.category_more, c.language_more FROM User u INNER JOIN UserPlus up ON u.identifier = up.user_id INNER JOIN Contributor c ON c.user_id = u.identifier WHERE u.type = 'contributor' AND u.status = 'Active' AND u.blackstatus='no' {$languageCond} {$categoryCond} AND u.profile_type IN ('senior','junior') AND u.profile_type2 IN ('".str_replace(",", "','", $ptype2) . "') ORDER BY u.updated_at DESC" ;
        }  
        else 
            $ContribQuery = "SELECT u.identifier, c.category_more, c.language_more FROM User u INNER JOIN UserPlus up ON u.identifier = up.user_id INNER JOIN Contributor c ON c.user_id = u.identifier WHERE u.type = 'contributor' AND u.status = 'Active' AND u.blackstatus='no' {$languageCond} {$categoryCond} ORDER BY u.updated_at DESC" ;
        
//echo($ContribQuery);
        if(($Contribresult = $this->getQuery($ContribQuery,true)) != NULL)
        {
            //echo '<pre>1';print_r($Contribresult);
            if(sizeof($categs)>0)
            {
                foreach($categs as $key => $value)
                {
                    $categ1[]=  $key."=".$value;
                }
                //print_r($categ1);echo '***';
                for($i=0; $i<count($Contribresult); $i++)
                {
                    if($Contribresult[$i]['category_more'] != '' && $Contribresult[$i]['category_more'] != 'N;')
                    {
                        $Contribresult[$i]['cates'] = unserialize($Contribresult[$i]['category_more']);
                    }
                    $final_array[$i]=$Contribresult[$i];
                    //echo '#'.($i+1).'#';print_r($final_array[$i]);print_r($Contribresult[$i]['cates']);
                    for($j=0; $j<count($categ1); $j++)
                    {
                        $catdetails = explode("=",$categ1[$j]);
                        $catindex = $catdetails[0];
                        $catvalue = $catdetails[1];
                        if(($Contribresult[$i]['cates'] != '') && (sizeof($Contribresult[$i]['cates'])>0))
                        {
                            if(array_key_exists($catindex, $Contribresult[$i]['cates']))
                            {//echo '|'.$Contribresult[$i]['cates'][$catindex].'|'.$catvalue.'|';
                                if($Contribresult[$i]['cates'][$catindex] > $catvalue)
                                {
                                    unset($final_array[$i]);
                                    //break;
                                }
                            }
                            else
                            {
                                unset($final_array[$i]);
                                //break;
                            }
                        }
                        else
                        {
                            unset($final_array[$i]);
                            //break;
                        }
                    }

                }
                if($final_array != NULL)
                {
                    unset($Contribresult);
                    $Contribresult = array_values($final_array);
                }
            }
//echo '<pre>';print_r($Contribresult);exit;

            if(sizeof($lng2s)>0)
            {
                foreach($lng2s as $key => $value)
                {
                    $lang2[]=  $key."=".$value;
                }
                
                for($i=0; $i<count($Contribresult); $i++)
                {
                    if($Contribresult[$i]['language_more'] != '' && $Contribresult[$i]['language_more'] != 'N;')
                    {
                        $Contribresult[$i]['ln2s'] = unserialize($Contribresult[$i]['language_more']);
                    }
                    $final_array2[$i]=$Contribresult[$i];
                    
                    for($j=0; $j<count($lang2); $j++)
                    {
                        $lang2details = explode("=",$lang2[$j]);
                        $lang2index = $lang2details[0];
                        $lang2value = $lang2details[1];
                        if(($Contribresult[$i]['ln2s'] != '') && (sizeof($Contribresult[$i]['ln2s'])>0))
                        {
                            if(array_key_exists($lang2index, $Contribresult[$i]['ln2s']))
                            {//echo '|'.$Contribresult[$i]['cates'][$catindex].'|'.$catvalue.'|';
                                if($Contribresult[$i]['ln2s'][$lang2index] > $lang2value)
                                {
                                    unset($final_array2[$i]);
                                    //break;
                                }
                            }
                            else
                            {
                                unset($final_array2[$i]);
                                //break;
                            }
                        }
                        else
                        {
                            unset($final_array2[$i]);
                            //break;
                        }
                    }
                }
                if($final_array2 != NULL)
                {
                    unset($Contribresult);
                    $Contribresult = array_values($final_array2);
                }
//echo '<pre>';print_r($lang2);print_r($final_array2);print_r($Contribresult);exit;
            }
            //echo '<pre>';print_r($Contribresult);//echo '<pre>';print_r($Contribresult);exit;
            foreach ($Contribresult as $key => $value) {
                $return[]=$value['identifier'];
            }
            //echo '---';print_r($return);exit;
            return $return;
       }
        else
            return 0;
    }
    
    public function getContribsList($params)
    {
        if($params['categ']!="")
		{
			$cat = explode("|", $params['categ']);
			if(sizeof($cat)>0)
			{
				foreach ($cat as $categ_) {
					$categ = explode("=", $categ_);
					$categs[str_replace("percentage_", "", $categ[0])]=$categ[1];
				}
			}
		}
        $lngg = explode("|", $params['lng2']);
        $lngg = array_filter($lngg); 
        if(sizeof($lngg)>0)
        {
            foreach ($lngg as $lng2_) {
                $lng2 = explode("=", $lng2_);
                $lng2s[str_replace("lang_percentage_", "", $lng2[0])]=$lng2[1];
            }
        }
        
        @$ptype1 = $params['w'];
        @$ptype2 = $params['c'];
        $languageCond = $params['language'] ? (" AND c.language IN ('" . str_replace("\'", "'", $params['language']) . "')") : "" ;
        //$categoryCond = $params['category'] ? (" AND c.favourite_category like '%" . $params['category'] . "%'") : "" ;
        $categoryCond = ((sizeof($categs)>0) ? " AND (c.favourite_category LIKE '%" . implode("%' OR c.favourite_category LIKE '%", array_keys($categs)) . "%')" : "");
		
		//Contributor test condition
		if($params['contributortest']!="no" && $params['contributortest']!="both")
		{
			$marks=explode("|",$params['contributortest']);
			
			$testCond=" AND c.contributortest='yes' AND c.contributortestmarks>=".$marks[0]." AND c.contributortestmarks<=".$marks[1];
		}
		elseif($params['contributortest']=="no")
			$testCond=" AND c.contributortest='no' ";
		else
			$testCond=" AND c.contributortest IN ('yes','no') ";
		
//exit($languageCond);
        if($ptype1)
        {
            $ContribQuery = "SELECT u.identifier,up.first_name,up.last_name, u.email,u.profile_type,u.created_at,  c.category_more, c.language_more, u.last_visit FROM User u LEFT JOIN UserPlus up ON u.identifier = up.user_id LEFT JOIN Contributor c ON c.user_id = u.identifier WHERE u.type = 'contributor' AND u.status = 'Active' AND u.blackstatus='no' {$languageCond} {$categoryCond} {$testCond} AND (u.profile_type IN ('".str_replace(",", "','", $ptype1)."')" ;
            if($ptype2)
                $ContribQuery .= " AND ( u.profile_type IN ('senior','junior') AND u.profile_type2 IN ('".str_replace(",", "','", $ptype2)."') )" ;
            $ContribQuery .= ") ORDER BY u.updated_at DESC" ;
        }
        else if($ptype2)
        {
            $ContribQuery = "SELECT u.identifier,up.first_name,up.last_name, u.email,u.profile_type,u.created_at,  c.category_more, c.language_more,u.last_visit FROM User u LEFT JOIN UserPlus up ON u.identifier = up.user_id LEFT JOIN Contributor c ON c.user_id = u.identifier WHERE u.type = 'contributor' AND u.status = 'Active' AND u.blackstatus='no' {$languageCond} {$categoryCond} {$testCond} AND u.profile_type IN ('senior','junior') AND u.profile_type2 IN ('".str_replace(",", "','", $ptype2) . "') ORDER BY u.updated_at DESC" ;
        }  
        else 
            $ContribQuery = "SELECT u.identifier,up.first_name,up.last_name,u.email, u.profile_type,u.created_at, c.category_more, c.language_more,u.last_visit FROM User u LEFT JOIN UserPlus up ON u.identifier = up.user_id LEFT JOIN Contributor c ON c.user_id = u.identifier WHERE u.type = 'contributor' AND u.status = 'Active' AND u.blackstatus='no' {$languageCond} {$categoryCond} {$testCond} ORDER BY u.updated_at DESC" ;
        //echo $ContribQuery;
//exit($ContribQuery);
        if(($Contribresult = $this->getQuery($ContribQuery,true)) != NULL)
        {
            //echo '<pre>';//print_r($lng2s);
           if($params['categ']!="")
            {
                foreach($categs as $key => $value)
                {
                    $categ1[]=  $key."=".$value;
                }
                for($i=0; $i<count($Contribresult); $i++)
                {
                    //if($Contribresult[$i]['category_more'] != '')
                    if($Contribresult[$i]['category_more'] != '' && $Contribresult[$i]['category_more'] != 'N;')
                    {
                        $Contribresult[$i]['cates'] = unserialize($Contribresult[$i]['category_more']);
                    }
                    
                    $final_array[$i]=$Contribresult[$i];
                    
                    for($j=0; $j<count($categ1); $j++)
                    {
                        $catdetails = explode("=",$categ1[$j]);
                        $catindex = $catdetails[0];
                        $catvalue = $catdetails[1];
                        //if($Contribresult[$i]['cates'] != '')
                        if(($Contribresult[$i]['cates'] != '') && (sizeof($Contribresult[$i]['cates'])>0))
                        {
                            if(array_key_exists($catindex, $Contribresult[$i]['cates']))
                            {
                                if($Contribresult[$i]['cates'][$catindex] < $catvalue)
                                {
                                    unset($final_array[$i]);
                                    //break;
                                }
                            }
                            else
                            {
                                unset($final_array[$i]);
                                //break;
                            }
                        }
                        else
                        {
                            unset($final_array[$i]);
                            //break;
                        }
                    }

                }
                //print_r($final_array);exit;
                if($final_array != NULL)
                {
                    unset($Contribresult);
                    $Contribresult = array_values($final_array);
                }
            }
            
            //print_r($Contribresult);echo '---';
            
            if(sizeof($lng2s)>0)
            {
                foreach($lng2s as $key => $value)
                {
                    $lang2[]=  $key."=".$value;
                }
                
                for($i=0; $i<count($Contribresult); $i++)
                {
                    if($Contribresult[$i]['language_more'] != '' && $Contribresult[$i]['language_more'] != 'N;')
                    {
                        $Contribresult[$i]['ln2s'] = unserialize($Contribresult[$i]['language_more']);
                        
                        $final_array2[$i]=$Contribresult[$i];//print_r($final_array2);
                        for($j=0; $j<count($lang2); $j++)
                        {
                            $lang2details = explode("=",$lang2[$j]);
                            $lang2index = $lang2details[0];
                            $lang2value = $lang2details[1];
                            if(($Contribresult[$i]['ln2s'] != '') && (sizeof($Contribresult[$i]['ln2s'])>0))
                            {
                                //echo '<br>#'.$i.' --> '.'<br>';print_r($Contribresult[$i]['ln2s']);
                                if(array_key_exists($lang2index, $Contribresult[$i]['ln2s']))
                                {//echo '|'.$Contribresult[$i]['cates'][$catindex].'|'.$catvalue.'|';
                                    if($Contribresult[$i]['ln2s'][$lang2index] < $lang2value)
                                    {
                                        unset($final_array2[$i]);
                                        //break;
                                    }
                                }
                                else
                                {
                                    unset($final_array2[$i]);
                                    //break;
                                }
                            }
                            else
                            {
                                unset($final_array2[$i]);
                                //break;
                            }
                        }
                    }
                    else
                    {
                        
                    }
                }
                //echo '<pre>';print_r($lang2);print_r($final_array2);exit();
                //echo '<pre>';print_r($categ1);print_r($final_array);exit();
                //if($final_array2 != NULL)
                {
                    unset($Contribresult);
                    $Contribresult = array_values($final_array2);
                }
            }
            //print_r($Contribresult);exit($ContribQuery);
            return $Contribresult;
       }
        else
            return 0;
    }
    
    public function getContribsCount($params, $crt)
    {
        //$uids = $this->getContribIds($params);
		if($params['categ']!="")
		{
			$cat = explode("|", $params['categ']);
			if(sizeof($cat)>0)
			{
				foreach ($cat as $categ_) {
					$categ = explode("=", $categ_);
					$categs[str_replace("percentage_", "", $categ[0])]=$categ[1];
				}
			}
		}
        @$ptype1 = $params['w'];
        @$ptype2 = $params['c'];
        $crtCond = ($crt ? " AND u.type2='corrector'" : "") ;
        $languageCond = $params['language'] ? (" AND c.language IN ('" . str_replace("\'", "'", $params['language']) . "')") : "" ;
        //$categoryCond = $params['category'] ? (" AND c.favourite_category like '%" . $params['category'] . "%'") : "" ;
        $categoryCond = ((sizeof($categs)>0) ? " AND (c.favourite_category LIKE '%" . implode("%' OR c.favourite_category LIKE '%", array_keys($categs)) . "%')" : "");
        
        $contribCountCond = "SELECT count(*) AS count, u.profile_type FROM User u LEFT JOIN Contributor c ON c.user_id = u.identifier WHERE u.profile_type != '' AND" ;
        
        //$uidsCountCond = " AND u.identifier IN ('".implode("','", $uids)."') " ;
        
        if($ptype1)
        {
            $ContribQuery = "{$contribCountCond} u.type = 'contributor'{$crtCond} AND u.status = 'Active' AND u.blackstatus='no' {$languageCond} {$categoryCond} {$uidsCountCond} AND (u.profile_type IN ('".str_replace(",", "','", $ptype1)."')" ;
            if($ptype2)
                $ContribQuery .= " AND ( u.profile_type IN ('senior','junior') AND u.profile_type2 IN ('".str_replace(",", "','", $ptype2)."') )" ;
            $ContribQuery .= ")" ;
        }
        else if($ptype2)
        {
            $ContribQuery = "{$contribCountCond} u.type = 'contributor'{$crtCond} AND u.status = 'Active' AND u.blackstatus='no' {$languageCond} {$categoryCond} {$uidsCountCond} AND u.profile_type IN ('senior','junior') AND u.profile_type2 IN ('".str_replace(",", "','", $ptype2) . "')" ;
        }  
        else 
            $ContribQuery = "{$contribCountCond} u.type = 'contributor'{$crtCond} AND u.status = 'Active' AND u.blackstatus='no' {$languageCond} {$categoryCond} {$uidsCountCond}" ;
            
        $ContribQuery .=  " GROUP BY u.profile_type" ; 
        
//exit($ContribQuery);
        if(($Contribresult = $this->getQuery($ContribQuery,true)) != NULL)
            return $Contribresult;
        else
            return 0;
    }

	public function getConfiguration($columns)
    {
        $SelConfg="SELECT configure_value FROM Configurations WHERE configure_name='".$columns."' ";
        if(($resultconfg = $this->getQuery($SelConfg,true)) != NULL)
            return $resultconfg[0]['configure_value'];
        else
            return "NO";
    }

     public function getProfileInfo($profile_identifier)
    {
        $whereQuery = "user_id = '".$profile_identifier."'";
        $profileQuery = "select * from ".$this->_name." where ".$whereQuery;

        $result = $this->getQuery($profileQuery,true);
        return $result;
    }
	
	public function ListContributors()
	{
		$Cquery="SELECT 
						u.identifier,u.email,u.profile_type,u.created_at,
						up.first_name,up.last_name,
						c.favourite_category
					FROM User u
					LEFT JOIN UserPlus up ON u.identifier = up.user_id
					LEFT JOIN Contributor c ON c.user_id = u.identifier
					WHERE u.type = 'contributor'
					AND u.status = 'Active'
					ORDER BY u.updated_at";
		$Cresult = $this->getQuery($Cquery,true);
        return $Cresult;
	}
	
	public function checkUpgradeDowngrade($contrib,$type)
	{
		$validated=$this->getConfiguration('auto_articles_validated');
		$refusals=$this->getConfiguration('auto_articles_refusal');
		$participation_count=$this->getConfiguration('auto_participation_count');
		$participation_month=$this->getConfiguration('auto_participation_duration');
		
		$Condquery1="SELECT count(*) as validatedcount FROM Participation WHERE user_id='".$contrib."' AND status='published'";
		$Result1 = $this->getQuery($Condquery1,true);
	
		$Condquery2="SELECT count(*) as refusedcount FROM Participation WHERE user_id='".$contrib."' AND status='closed'";
		$Result2 = $this->getQuery($Condquery2,true);
	
		$Condquery3="SELECT count(*) as participationcount FROM Participation WHERE user_id='".$contrib."' AND  created_at>=DATE_SUB(CURRENT_DATE(),  INTERVAL 2 MONTH )OR created_at<=CURRENT_DATE()";
		$Result3 = $this->getQuery($Condquery3,true);
		
		if($Result1[0]['validatedcount']>=$validated && $Result2[0]['refusedcount']<=$refusals && $Result3[0]['participationcount']>=$participation_count && $type=='junior')
			return "up";
		else if($Result1[0]['validatedcount']<$validated && $Result2[0]['refusedcount']>$refusals && $Result3[0]['participationcount']<$participation_count && $type=='senior')
			return "down";
		else
			return "NO";
	}
    ////////fetch the contributor stats //////////////
    public function getContributorStats($params)
    {
        if($params['statstype'] == 'moyennes')
        {
            if($params['usertype'] != '0')
            {
               if($params['usertype'] == '1')
                    $condition = "   u.identifier = '".$params['userid']."' AND ap.stage = 'corrector' ";
                else
                    $condition = "   u.identifier = '".$params['userid']."' AND ap.stage != 'corrector' ";
            }
            if($params['lang'] != '')
            {
                $condition = "   u.identifier = '".$params['userid']."' AND a.language = '".$params['lang']."' ";
            }
            if($params['categ'] != '')
            {
                $condition = "   u.identifier = '".$params['userid']."' AND a.category = '".$params['categ']."' ";
            }
        }
        elseif($params['type'] == 'stats')
        {
            $lang = '';
            $categ = '';
        }
        elseif($params['type'] == 'graph')
        {
            $lang = '';
            $categ = '';
        }
        $query = "SELECT p.marks AS partmarks, ap.marks FROM User u
	                     INNER JOIN UserPlus up ON u.identifier=up.user_id
	                     INNER JOIN Participation p ON u.identifier=p.user_id
	                     INNER JOIN Article a ON a.id = p.article_id
	                     LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id
	                     WHERE ".$condition; //exit;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";

    }
    ////////fetch the corrector stats //////////////
    public function getCorrectorStats($params)
    {
        if($params['statstype'] == 'moyennes')
        {
            if($params['usertype'] != '0')
            {
                if($params['usertype'] == '1')
                    $condition = "   u.identifier = '".$params['userid']."' AND ap.stage = 'corrector' ";
                else
                    $condition = "   u.identifier = '".$params['userid']."' AND ap.stage != 'corrector' ";
            }
            if($params['lang'] != '')
            {
                $condition = "   u.identifier = '".$params['userid']."' AND a.language = '".$params['lang']."' ";
            }
            if($params['categ'] != '')
            {
                $condition = "   u.identifier = '".$params['userid']."' AND a.category = '".$params['categ']."' ";
            }
        }
        elseif($params['type'] == 'stats')
        {
            $lang = '';
            $categ = '';
        }
        elseif($params['type'] == 'graph')
        {
            $lang = '';
            $categ = '';
        }
        $query = "SELECT ap.marks FROM User u
	                     INNER JOIN UserPlus up ON u.identifier=up.user_id
	                     INNER JOIN CorrectorParticipation cp ON u.identifier=cp.corrector_id
	                     INNER JOIN Article a ON a.id = cp.article_id
	                     LEFT JOIN ArticleProcess ap ON cp.id=ap.participate_id
	                     WHERE ".$condition; //echo $query; exit;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";

    }
	
	public function getContributorcountwithLang($ptype,$lang)
    {
        $langarr=explode(",",$lang);
		$langstr=implode("','",$langarr);
		
		$ContribQuery = "select count(*) as count FROM User u INNER JOIN Contributor c ON u.identifier=c.user_id WHERE u.profile_type IN ('".$ptype."') AND c.language IN ('".$langstr."') AND u.status='active' AND u.blackstatus='no'" ;
        
		if(($Contribresult = $this->getQuery($ContribQuery,true)) != NULL)
            return $Contribresult[0]['count'];
        else
            return 0;
    }
}
