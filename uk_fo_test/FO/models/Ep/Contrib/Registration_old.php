<?php
/**
 * Registration Model
 * This Model  is responsible for Registration actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Contrib_Registration extends Ep_Db_Identifier
{
	protected $_name = 'R_users';
	private $r_identifier;
	private $r_email;
	private $r_password;
	private $r_status;
	private $r_created_date;
	private $r_updated_date;
	private $r_last_visit;

	public function loadData($array)
	{
		$this->r_email=$array["r_email"] ;
		$this->r_password=$array["r_password"] ;
		$this->r_status=$array["r_status"] ;
		$this->r_created_date=$array["r_created_date"] ;
		$this->r_updated_date=$array["r_updated_date"] ;
		$this->r_last_visit=$array["r_last_visit"] ;
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["r_identifier"] = $this->getIdentifier();
		$array["r_email"] = $this->r_email;
		$array["r_password"] = $this->r_password;
		$array["r_status"] = $this->r_status;
		$array["r_created_date"] = $this->r_created_date;
		$array["r_updated_date"] = $this->r_updated_date;
		$array["r_last_visit"] = $this->r_last_visit;
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }

	public function checkContribMailid($mailid)
	{
		$whereQuery = "r_email = '".$mailid."'";
		$query = "select * from ".$this->_name." where ".$whereQuery;
		if(($result = $this->getQuery($query,true)) != NULL)
			return "YES";
		else
			return "NO";
	}
    public function checkContribMailidLogin($mailid,$password)
	{
		$whereQuery = "r_email = '".$mailid."' and r_password='".$password."'";
		$query = "select * from ".$this->_name." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL)
			return "YES";
		else
			return "NO";
	}
    public function getContribMailidLogin($mailid,$password)
	{
		$whereQuery = "r_email = '".$mailid."' and r_password='".$password."'";
		$query = "select * from ".$this->_name." where ".$whereQuery;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result[0]["r_identifier"];
		else
			return "NO";
	}
    public function getLatestContributors($currentIdentifier)
    {
        $join=" INNER JOIN Contrib_profile cp ON u.r_identifier=cp.r_identifier ";
        $whereQuery=" where u.r_identifier!='".$currentIdentifier."' order by u.r_created_date DESC limit 3";
        $getquery="select u.r_identifier,cp.r_fname,cp.r_lname,cp.r_category from ".$this->_name." u".$join.$whereQuery;

        if(($count=$this->getNbRows($getquery))>0)
        {
            $latest_contributors=$this->getQuery($getquery,true);
            return $latest_contributors;
        }


    }




}




