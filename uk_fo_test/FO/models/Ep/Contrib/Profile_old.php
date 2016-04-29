<?php
/**
 * Registration Model
 * This Model  is responsible for Profile actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Contrib_Profile extends Ep_Db_Identifier
{
	protected $_name = 'Contrib_profile';
	private $profile_identifier;
    private $r_identifier;
    private $r_civ;
    private $r_fname;
    private $r_lname;
    private $r_dob;
    private $r_profession;
    private $r_university;
    private $r_edu_level;
    private $r_degree;
    private $r_language;
    private $r_nationality;
    private $r_address;
    private $r_telephone;
    private $r_postalcode;
    private $r_pays;
    private $r_category;
    private $r_profilce_picture;
    private $r_created_date;
	private $r_updated_date;


	public function loadData($array)
	{
		$this->r_identifier=$array["r_identifier"] ;
        $this->r_civ=$array["r_civ"] ;
        $this->r_fname=$array["r_fname"] ;
		$this->r_lname=$array["r_lname"] ;
		$this->r_dob=$array["r_dob"] ;
		$this->r_profession=$array["r_profession"] ;
		$this->r_university=$array["r_university"] ;
		$this->r_edu_level=$array["r_edu_level"] ;
        $this->r_degree=$array["r_degree"] ;

		$this->r_language=$array["r_language"] ;
		$this->r_nationality=$array["r_nationality"] ;
        $this->r_address=$array["r_address"] ;
        $this->r_telephone=$array["r_telephone"] ;
		$this->r_postalcode=$array["r_postalcode"] ;
		$this->r_pays=$array["r_pays"] ;
        $this->r_category=$array["r_category"] ;
        $this->r_created_date=$array["r_created_date"] ;
		$this->r_updated_date=$array["r_updated_date"] ;
		//$this->r_profilce_picture=$array["r_profilce_picture"] ;
		
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
        if($this->profile_identifier=='')
		    $array["profile_identifier"] = $this->getIdentifier();
        $array["r_identifier"] = $this->r_identifier;
		$array["r_civ"] = $this->r_civ;
		$array["r_fname"] = $this->r_fname;
		$array["r_lname"] = $this->r_lname;
		$array["r_dob"] = $this->r_dob;
		$array["r_profession"] = $this->r_profession;
		$array["r_university"] = $this->r_university;
        $array["r_edu_level"] = $this->r_edu_level;
		$array["r_degree"] = $this->r_degree;
        $array["r_language"] = $this->r_language;
		$array["r_nationality"] = $this->r_nationality;
		$array["r_address"] = $this->r_address;
        $array["r_telephone"]=$this->r_telephone;
		$array["r_postalcode"] = $this->r_postalcode;
        $array["r_pays"] = $this->r_pays;
		$array["r_category"] = $this->r_category;
		$array["r_created_date"] = $this->r_created_date;
		$array["r_updated_date"] = $this->r_updated_date;
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    public function checkProfileExist($identifier)
    {
        $whereQuery = "r_identifier = '".$identifier."'";
		$existsQuery = "select * from ".$this->_name." where ".$whereQuery;
        
		if(($result = $this->getQuery($existsQuery,true)) != NULL)
			return $result[0]["profile_identifier"];
		else
			return "NO";

    }
    public function getProfileInfo($profile_identifier)
    {
        $whereQuery = "profile_identifier = '".$profile_identifier."'";
		$profileQuery = "select * from ".$this->_name." where ".$whereQuery;

		$result = $this->getQuery($profileQuery,true);
        return $result;
	}
    public function updateprofile($data,$identifier)
    {
        $where=" profile_identifier='".$identifier."'";
                
        //print_r($data);exit;

        echo $this->updateQuery($data,$where);


    }

}