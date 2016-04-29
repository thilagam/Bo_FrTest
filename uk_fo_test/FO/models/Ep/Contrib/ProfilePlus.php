<?php
/**
 * Profile Plus Model
 * This Model  is responsible for Profile actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Contrib_ProfilePlus extends Ep_Db_Identifier
{
	protected $_name = 'UserPlus';
	private $user_id;
    private $initial;
    private $first_name;
    private $last_name;
    private $address;
    private $city;
    private $phone_number;
    private $zipcode;
    private $country;

	public function loadData($array)
	{
		$this->user_id=$array["user_id"] ;
        $this->initial=$array["initial"] ;
        $this->first_name=$array["first_name"] ;
		$this->last_name=$array["last_name"] ;
        $this->address=$array["address"];
        $this->city=$array["city"] ;
        $this->zipcode=$array["zipcode"] ;
        $this->country=$array["country"] ;
        $this->phone_number=$array["phone_number"] ;
        
    	return $this;
	}
	public function loadintoArray()
	{
		$array = array();
        $array["user_id"] =  $this->user_id;
        $array["initial"] = $this->initial;
		$array["first_name"] = $this->first_name;
		$array["last_name"] = $this->last_name;
        $array["address"] = $this->address;
        $array["city"] = $this->city;
        $array["phone_number"]=$this->phone_number;
		$array["zipcode"] = $this->zipcode;
        $array["country"] = $this->country;

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
        $whereQuery = "u.user_id = '".$identifier."'";
		$existsQuery = "select u.user_id,first_name,last_name,payment_type,self_details,staus_self_details_updated,c.user_id as contributor,c.language from ".$this->_name." u		                
                        LEFT JOIN Contributor c ON u.user_id=c.user_id
		                where ".$whereQuery;
        
		//echo $existsQuery;exit;
		if(($result = $this->getQuery($existsQuery,true)) != NULL)
			return $result;//[0]["user_id"];
		else
			return "NO";

    }
    public function getProfileInfo($profile_identifier)
    {
        $whereQuery = "user_id = '".$profile_identifier."'";
		$profileQuery = "select * from ".$this->_name." up
                         INNER JOIN User u ON u.identifier=up.user_id
                        where ".$whereQuery;
        $result = $this->getQuery($profileQuery,true);
        return $result;
	}
    public function updateprofile($data,$identifier)
    {
        $where=" user_id='".$identifier."'";
                
        //print_r($data);exit;

        echo $this->updateQuery($data,$where);


    }

}