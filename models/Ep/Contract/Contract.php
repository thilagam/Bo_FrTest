<?php
/**
 * Ep_Contact_Contract
 * @author Arun
 * @package Contract
 * @version 1.0
 */
class Ep_Contract_Contract extends Ep_Db_Identifier
{
	protected $_name = 'Contracts';
	private $id;
	private $client_id;
	private $title;
	private $contract_date;
	private $lang;
	private $created_at;
    
	public function loadData($array)
	{
		$this->client_id=$array["client_id"];
		$this->title=$array["title"];
		$this->contract_date=$array["contract_date"];
		$this->lang=$array["lang"];
		$this->created_at=$array["created_at"];
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["client_id"] = $this->client_id;
		$array["title"] = $this->title;
		$array["contract_date"] = $this->contract_date;
		$array["lang"] = $this->lang;
		$array["created_at"] = $this->created_at;
		return $array;
	}
	public function __set($name, $value) {
           $this->$name = $value;
    }
   public function __get($name){
            return $this->$name;
    }
    public function getContacts($type)
    {
        $query="select
                    u.identifier,CONCAT(first_name,' ',last_name) as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                where u.type='".$type."' and u.status='Active' and u.verified_status='YES'
                Group BY u.identifier,contact_name
                ORDER BY contact_name,u.email
                ";
        // echo $query;exit;
        if(($clients=$this->getQuery($query,true))!=NULL)
            return $clients;
        else
            return "Not Exists";
    }
    public function getContractList($clientId=NULL)
    {
        if($clientId)
           $where= " WHERE client_id='".$clientId."'";
        $query="select
                    *
                from ".$this->_name.
                $where
                ;
        // echo $query;exit;
        if(($contracts=$this->getQuery($query,true))!=NULL)
            return $contracts;
        else
            return "NO";
    }
    public function getAllContracts()
    {
        $contractDetails="select c.client_id,c.id,c.title,c.contract_date,u.email,cl.company_name
                FROM ".$this->_name." c
                INNER JOIN User u ON c.client_id=u.identifier
                LEFT JOIN Client cl ON u.identifier=cl.user_id
                Group BY c.id
                ORDER BY c.contract_date DESC";
        //echo $contractDetails;exit;
        if(($contracts=$this->getQuery($contractDetails,true))!=NULL)
            return $contracts;
        else
            return "NO";
    }
    public function getContractDetails($contractid=NULL)
    {
        if($contractid)
            $where= " WHERE id='".$contractid."'";
        $query="select
                    *
                from ".$this->_name.
            $where
        ;
        // echo $query;exit;
        if(($contracts=$this->getQuery($query,true))!=NULL)
            return $contracts;
        else
            return "Not Exists";
    }
    public function updateContract($data,$cid)
    {
        $where=" id=".$cid;
        //print_r($data);exit;
        echo $this->updateQuery($data,$where);
    }
}
