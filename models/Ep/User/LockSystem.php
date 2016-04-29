<?php
/**
 * Ep_User_User
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
class Ep_User_LockSystem extends Ep_Db_Identifier
{
	protected $_name = 'LockSystem';
	private $id;
	private $user_id;
    private $article_id;
	private $lock_status;

	public function loadData($array)
	{
		$this->id=$array["id"] ;
		$this->user_id=$array["user_id"];
        $this->article_id=$array["article_id"];
		$this->lock_status=$array["lock_status"] ;
        return $this;
	}
	public function loadintoArray()
	{
		$array = array();
	    $array["id"] = $this->getIdentifier();
	    $array["user_id"] = $this->user_id;
        $array["article_id"] = $this->article_id;
        $array["lock_status"] = $this->lock_status;
       	return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
/////////check lock status ///////////////////////////
	public function checkLockStatus($partId)
	{
        $this->adminLogin = Zend_Registry::get('adminLogin');
         $query = "SELECT lock_status FROM ".$this->_name." WHERE participate_id='".$partId."' AND user_id='".$this->adminLogin->userId."' AND
                  version=(SELECT MAX(version) FROM ".$this->_name." WHERE participate_id='".$partId."' AND user_id='".$this->adminLogin->userId."' )";

		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}

    /////////get latest lock version ///////////////////////////
	public function lockExist($artId)
	{
        $query = "SELECT id FROM ".$this->_name." WHERE article_id=".$artId;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////get latest lock version ///////////////////////////
    public function lockExistByUser($artId, $userId)
    {
        $query = "SELECT lock_status FROM ".$this->_name." WHERE user_id = ".$userId." AND article_id=".$artId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get users who locked the articles in all stages ///////////////////////////
	public function getUserLocked($artId)
	{
        $query = "SELECT u.login, l.user_id FROM ".$this->_name." l INNER JOIN User u ON u.identifier=l.user_id WHERE article_id=".$artId." AND l.lock_status='yes'" ;

		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    public function updateLockSystem($data,$query)
    {
          $this->updateQuery($data,$query);
    }
}

