<?php
/**
 * Profile Contributor Model
 * This Model  is responsible for Profile actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Contrib_ProfileContributor extends Ep_Db_Identifier
{
	protected $_name = 'Contributor';
	private $user_id;
    private $dob;
    private $profession;
    private $profession_other;
    private $university;
    private $education;
    private $degree;
    private $language;
    private $language_more;
    private $nationality;
    private $favourite_category;
	private $category_more;
    private $self_details;

    private $payment_type;
    private $pay_info_type;
    private $SSN;
    private $company_number;
    private $vat_check;
    private $VAT_number;
    private $paypal_id;
    private $rib_id;

    private $staus_self_details_updated;
    private $updated_at;
    private $profile_percentage;
    private $entreprise;
    private $bank_account_name;
    


	public function loadData($array)
	{
		$this->user_id=$array["user_id"] ;
        $this->dob=$array["dob"] ;
		$this->profession=$array["profession"] ;
        $this->profession_other=$array["profession_other"] ;
		$this->university=$array["university"] ;
		$this->education=$array["education"] ;
        $this->degree=$array["degree"] ;
		$this->language=$array["language"] ;
        $this->language_more=$array["language_more"] ;
		$this->nationality=$array["nationality"] ;
		$this->favourite_category=$array["favourite_category"] ;
		$this->category_more=$array['category_more'];
        $this->self_details=$array["self_details"];
        $this->payment_type=$array["payment_type"] ;

        $this->pay_info_type=$array["pay_info_type"] ;
        $this->SSN=$array["SSN"] ;
        $this->company_number=$array["company_number"] ;
        $this->vat_check=$array["vat_check"] ;
        $this->VAT_number=$array["VAT_number"] ;

        $this->paypal_id=$array["paypal_id"] ;
        $this->rib_id=$array["rib_id"] ;
        $this->staus_self_details_updated=$array["staus_self_details_updated"] ;
		$this->updated_at=$array["updated_at"];
        $this->profile_percentage=$array["profile_percentage"];
        $this->entreprise=$array["entreprise"];
        $this->bank_account_name=$array["bank_account_name"];
         


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
        $array["language_more"] = $this->language_more;
		$array["nationality"] = $this->nationality;
		$array["favourite_category"] = $this->favourite_category;
		$array['category_more']=$this->category_more;
        $array["self_details"]=$this->self_details;
		$array["payment_type"]=$this->payment_type;
        $array["profession_other"]=$this->profession_other;


        $array["pay_info_type"]=$this->pay_info_type;
        $array["SSN"] =$this->SSN;
        $array["company_number"]=$this->company_number ;
        $array["vat_check"]=$this->vat_check ;
        $array["VAT_number"]=$this->VAT_number;

        $array["paypal_id"]=$this->paypal_id;
        $array["rib_id"]=$this->rib_id;
        $array["staus_self_details_updated"]=$this->staus_self_details_updated;
        $array["updated_at"]=$this->updated_at;
        $array["entreprise"]=$this->entreprise;
        $array["bank_account_name"]=$this->bank_account_name;

        


        /*added by naseer on 04-11-2015*/
        $array["software_list"]=$this->software_list;
        $array["writer_preference"]=$this->writer_preference;
        $array["translator"]=$this->translator;
        $array["twitter_id"]=$this->twitter_id;
        $array["facebook_id"]=$this->facebook_id;
        $array["website"]=$this->website;
        return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    
    public function getProfileInfo($profile_identifier)
    {
        $whereQuery = "user_id = '".$profile_identifier."'";
		$profileQuery = "select * from ".$this->_name." where ".$whereQuery;

		$result = $this->getQuery($profileQuery,true);
        return $result;
	}
    public function updateprofile($data,$identifier)
    {
        $where=" user_id='".$identifier."'";

        //print_r($data);exit;

        $this->updateQuery($data,$where);


    }
     ////////fetch the contributor info in popup//////////////
    public function getGroupProfilesInfo($profile_identifier, $partId)
    {
        if($cond == 1)
           $whereQuery = "c.user_id = '".$profile_identifier."' AND p.id='".$partId."' AND p.status NOT IN ('bid_premium','bid_refused')";
        else
           $whereQuery = "c.user_id = '".$profile_identifier."' AND p.id='".$partId."'";

        $profileQuery = "select c.profession,c.profession_other, c.degree, c.language, c.favourite_category,
                     p.id AS partId, p.status, p.price_user, d.delivery_date, u.identifier, u.email,
                     CONCAT(up.first_name,' ',UPPER(SUBSTRING(up.last_name, 1,1))) as first_name,
                     a.id,a.title,u.profile_type
                     from ".$this->_name." c
                     INNER JOIN User u ON c.user_id=u.identifier
                     INNER JOIN UserPlus up ON c.user_id=up.user_id
                     INNER JOIN Participation p ON u.identifier=p.user_id
                     INNER JOIN Article a ON a.id=p.article_id
                     INNER JOIN Delivery d ON d.id=a.delivery_id
                     where ".$whereQuery;
        //echo $profileQuery;exit;
        $profileQuery2 = "select count(id) AS no_approved from Participation where user_id = '".$profile_identifier."' AND status='published'";
        $profileQuery3 = "select count(id) AS no_disapproved from Participation where user_id = '".$profile_identifier."' AND status='disapproved'";
        $profileQuery4 = "select count(user_id) AS no_paritcipations from Participation where user_id = '".$profile_identifier."'";
		$result = $this->getQuery($profileQuery,true);
		$result2 = $this->getQuery($profileQuery2,true);
        $result3 = $this->getQuery($profileQuery3,true);
        $result4 = $this->getQuery($profileQuery4,true);
        $result5=array_merge($result, $result2, $result3, $result4);
        return $result5;
	}
     public function getGroupProfilesPollInfo($profile_identifier, $partId)
    {

         $whereQuery = "u.identifier = '".$profile_identifier."' AND p.id='".$partId."'";

        $profileQuery = "select p.id AS partId, p.status, p.price_user, u.identifier, u.email,p.per_week,
                     CONCAT(up.first_name,' ',UPPER(SUBSTRING(up.last_name, 1,1))) as first_name,
                     po.id,po.title,u.profile_type
                     from User u
                     LEFT JOIN UserPlus up ON u.identifier=up.user_id
                     INNER JOIN Poll_Participation p ON u.identifier=p.user_id
                     INNER JOIN Poll po ON po.id=p.poll_id
                     where ".$whereQuery;
        //echo  $profileQuery;exit;

        if(($count=$this->getNbRows($profileQuery))>0)
        {
            $profile=$this->getQuery($profileQuery,true);
            return $profile;
        }
        else
        {
            return "NO";
        }
	}
    ////////fetch the contributor info in popup//////////////
    public function getCorrectorGroupProfilesInfo($profile_identifier, $partId)
    {
        if($cond == 1)
            $whereQuery = "c.user_id = '".$profile_identifier."' AND p.id='".$partId."' AND p.status NOT IN ('bid_premium','bid_refused')";
        else
            $whereQuery = "c.user_id = '".$profile_identifier."' AND p.id='".$partId."'";

        $profileQuery = "select c.profession,c.profession_other, c.degree, c.language, c.favourite_category,
                     p.id AS partId, p.status, p.price_corrector, d.delivery_date, u.identifier, u.email,
                     CONCAT(up.first_name,' ',UPPER(SUBSTRING(up.last_name, 1,1))) as first_name,
                     a.id,a.title,u.profile_type
                     from ".$this->_name." c
                     INNER JOIN User u ON c.user_id=u.identifier
                     INNER JOIN UserPlus up ON c.user_id=up.user_id
                     INNER JOIN CorrectorParticipation p ON u.identifier=p.corrector_id
                     INNER JOIN Article a ON a.id=p.article_id
                     INNER JOIN Delivery d ON d.id=a.delivery_id
                     where ".$whereQuery;
        //echo $profileQuery;exit;
        $profileQuery2 = "select count(id) AS no_approved from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='published'";
        $profileQuery3 = "select count(id) AS no_disapproved from CorrectorParticipation where corrector_id = '".$profile_identifier."' AND status='disapproved'";
        $profileQuery4 = "select count(corrector_id) AS no_paritcipations from CorrectorParticipation where corrector_id = '".$profile_identifier."'";
        $result = $this->getQuery($profileQuery,true);
        $result2 = $this->getQuery($profileQuery2,true);
        $result3 = $this->getQuery($profileQuery3,true);
        $result4 = $this->getQuery($profileQuery4,true);
        $result5=array_merge($result, $result2, $result3, $result4);
        return $result5;
    }

	public function updateContributor($data,$query)
    {
       $this->updateQuery($data,$query);
    }
}