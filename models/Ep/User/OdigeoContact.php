<?php
/**
 * Ep_User_Client
 * @author Admin
 * @package OdigeoContact
 * @version 1.0
 */
class Ep_User_OdigeoContact extends Ep_Db_Identifier
{
	protected $_name = 'OdigeoContact';
	private $user_id;
	private $rcs;
	private $vat;
	private $fax_number;

	public function loadData($array)
	{
		$this->user_id($array["user_id"]) ;
		
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["user_id"] = $this->getUser_id();		
		return $array;
	}
    public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
   
    public function updateContactProfile($data,$identifier)
    {
        //print_r($data);exit;
		$where=" user_id='".$identifier."'";
        $this->updateQuery($data,$where);
    }
   
	
	//Insert Odigeo contact
	public function insertContact($data)
	{
		$this->insertQuery($data);	
	}
	
}
