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
class Ep_User_UserGroupAccess extends Ep_Db_Identifier
{
	protected $_name = 'UserGroupAccess';
	private $id;
	private $groupName;
	private $pageId;
	private $status;
	   
	public function loadData($array)
	{
		$this->id=$array["id"] ;
		$this->groupName=$array["groupName"];
		$this->pageId=$array["pageId"];
		$this->status=$array["status"] ;
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();		
		$array["id"] = $this->getId();
		$array["groupName"] = $this->groupName;
        $array["pageId"] = $this->pageId;
		$array["status"] = $this->status;

       	return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }	
    /////to get the pages id of group
    public function getGroupPageId($logName)
	{
		$query = "SELECT g.pageId, g.menuId FROM ".$this->_name." g INNER JOIN User u ON g.id = u.groupId WHERE u.login='".$logName."'";
		if(($result = $this->getQuery($query,false)) != NULL)
        {
            return $result;
			//print_r($result);
        }
		else
			return false;
	}

     /////to get the groups
    public function getAllGroups()
	{
		$query = "SELECT * FROM ".$this->_name;
		if(($result = $this->getQuery($query,false)) != NULL)
        {
            return $result;
			//print_r($result);
        }
		else
			return false;
	}
    public function getAllUserGroupNames()
    {
        $query = "SELECT groupName FROM ".$this->_name;
        if(($result = $this->getQuery($query,true)) != NULL)

            return $result;

        else

            return "NO";
    }

    public function getGroup($sel_group)
	{
	    $query = "SELECT * FROM ".$this->_name." WHERE id=".$sel_group;
		if(($result = $this->getQuery($query,false)) != NULL)
        {
            return $result;
			//print_r($result);
        }
		else
			return false;
	}
    public function updateGroup($data,$query)
    {
          $this->updateQuery($data,$query);
    }
}

