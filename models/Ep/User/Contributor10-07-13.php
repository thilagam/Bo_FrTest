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
        $profileQuery = "select c.profession,c.profession_other, c.degree, c.language, c.favourite_category, c.category_more,c.language_more,
                     p.id AS partId, p.status, p.current_stage, p.price_user,p.created_at, p.accept_refuse_at,u.created_at AS doj,
                     d.delivery_date, d.id AS delId, u.identifier, u.email, u.blackstatus, u.profile_type, up.first_name,d.poll_id,d.premium_option,
                     up.last_name,a.id,a.title,a.price_min,a.price_max,a.contrib_percentage,a.participation_expires,p.cycle from ".$this->_name." c
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
        $profileQuery11 = "select count(user_id) AS closed from Participation where user_id = '".$profile_identifier."' AND status='closed'";
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
    ////////fetch the correctors info in popup//////////////
    public function getGroupCrtProfilesInfo($profile_identifier, $partId, $cond, $Artid)
    {
        if($cond == 1)
            $whereQuery = "c.user_id = '".$profile_identifier."' AND cp.id='".$partId."' AND cp.status NOT IN ('bid_corrector','bid_refused')";
        else
            $whereQuery = "c.user_id = '".$profile_identifier."' AND cp.id='".$partId."' ";

        $profileQuery = "select c.profession,c.profession_other, c.degree, c.language, c.favourite_category,
                     cp.id AS partId, cp.status, cp.current_stage, cp.price_corrector,cp.created_at, cp.accept_refuse_at,
                     d.delivery_date, u.identifier, u.email, u.blackstatus, u.profile_type,u.profile_type2, u.type2, up.first_name,d.poll_id,d.premium_option,
                     up.last_name,a.id,a.title,a.price_min,a.price_max,a.contrib_percentage,a.correction_participationexpires,cp.cycle from ".$this->_name." c
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

	public function getConfiguration($columns)
    {
        $SelConfg="SELECT configure_value FROM Configurations WHERE configure_name='".$columns."' ";
        if(($resultconfg = $this->getQuery($SelConfg,true)) != NULL)
            return $resultconfg[0]['configure_value'];
        else
            return "NO";
    }

}
