<?php
/**
 * Ep_User_UserPlus
 * @author admin
 * @package Ticket
 * @version 1.0
 */
 /*Status
        0 => sent by sender / received by recipient
        1 => received by sender / sent by recipient
        2 => classified by sender
        3 => classified by recipient
*/
class Ep_User_RibBoUser extends Ep_Db_Identifier
{
	protected $_name = 'RibBoUser';
    private $id;
    private $user_id;
	private $rib_number;
	private $rib_name;
	private $rib_file;
	private $default_val;

	public function loadData($array)
	{
        $this->id=$array["id"] ;
        $this->user_id=$array["user_id"] ;
		$this->rib_number=$array["rib_number"];
		$this->rib_name=$array["rib_name"];
		$this->rib_file=$array["rib_file"] ;
		$this->default_val=$array["default_val"] ;

        return $this;
	}
	public function loadintoArray()
	{
		$array = array();
        $array["id"] = $this->getIdentifier();
		$array["user_id"] = $this->user_id;
		$array["rib_number"] = $this->rib_number;
        $array["rib_name"] = $this->rib_name;
		$array["rib_file"] = $this->rib_file;
		$array["default_val"] = $this->default_val;

        return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }


    public function updateRibBoUser($data,$query)
    {
        //$where=" user_id='".$identifier."'";
       // print_r($data);exit;
        echo $this->updateQuery($data,$query);
    }
    ////delete the rib detials of the user///
    public function deleteRibBoUser($identifier)
    {
        $where=" user_id='".$identifier."'";
        // print_r($data);exit;
        echo $this->deleteQuery($where);
    }
    /////////////ribbouser details ////////////
    public function getRidDetails($userid)
    {
        $msg_query="select * FROM ".$this->_name." where user_id=".$userid;
        //echo $msg_query;exit;
        if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
        else
            return "NO";

    }
    /* *** added on 12.01.2015 *** */
    ////delete the rib detials of the user///
    public function delRibBoUser($identifier)
    {
        $where=" user_id='".$identifier."'";
        // print_r($data);exit;
        $this->deleteQuery($where);
    }
}

