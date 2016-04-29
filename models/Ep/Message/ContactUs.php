<?php
/**
 * Contact  Model
 * This Model  is responsible for Contact actions*
 * @author Chandu
 * @editor Chandu
 * @version 1.0
 */
class EP_Message_ContactUs extends Ep_Db_Identifier
{
	protected $_name = 'ContactUs';
	private $identifier;
    private $name;
    private $email;
    private $msg_object;
    private $message;
    private $created_at;
    private $status;


	public function loadData($array)
	{
        $this->name=$array["name"] ;
        $this->email=$array["email"] ;
		$this->msg_object=$array["msg_object"] ;
        $this->message=$array["message"];
        $this->created_at=$array["created_at"];
        $this->status=$array["status"];
    	return $this;
	}
	public function loadintoArray()
	{
		$array = array();
        $array["identifier"] =$this->getIdentifier();
        $array["name"] =  $this->name;
        $array["email"] = $this->email;
		$array["msg_object"] = $this->msg_object;
		$array["message"] = $this->message;
        $array["created_at"] = $this->created_at;
        $array["status"]=$this->status;
        return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    /////////get all contact us details///////////////////////////
	public function contactUsDetails()
	{
       $query = "select *  FROM ".$this->_name." WHERE status IN ('0','1') ORDER BY created_at DESC";
       if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    public function getContactUsMessage($messageId)
    {
        $query = "select * from ".$this->_name." where identifier=".$messageId;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
    }
     ////////////udate the contact us table//////////////////////
    public function updateContactUs($data,$query)
    {
        //  echo $query;
       //print_r($data);exit;
        $this->updateQuery($data,$query);
    }

}
