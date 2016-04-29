<?php
/**
 * Ep_Ticket_Message
 * @author Admin
 * @package Message
 * @version 1.0
 */
class Ep_Message_UserComments extends Ep_Db_Identifier
{
	protected $_name = 'UserComments';
	private $identifier;
	private $commented_by;
    private $commented_on;
	private $comments;
	private $created_at;
	private $user_type;


	public function loadData($array)
	{
		$this->identifier=$array["identifier"] ;
		$this->commented_by=$array["commented_by"];
        $this->commented_on=$array["commented_on"];
		$this->comments=$array["comments"];
		$this->created_at=$array["created_at"] ;
		$this->user_type=$array["user_type"] ;
    	return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["identifier"] = $this->getIdentifier();
		$array["commented_by"] = $this->commented_by;
        $array["commented_on"] = $this->commented_on;
		$array["comments"] = $this->comments;
		$array["created_at"] = $this->created_at;
		$array["user_type"] = $this->user_type;
     	return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    public function getWriterName($userId)
    {
        $query = "SELECT  up.first_name, up.last_name, u.email FROM ".$this->_name." uc
		          LEFT JOIN UserPlus up ON up.user_id=uc.commented_on
		          LEFT JOIN User u ON u.identifier=uc.commented_on WHERE commented_on=".$userId;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
    }
    /////////fecthing the commets by corrector and other bo users who commented on corntributors at group selection profiles///////////////
	public function getBoUsersComments($userId)
	{
	     $query = "SELECT  uc.comments, uc.created_at, up.first_name, up.last_name, u.email FROM ".$this->_name." uc
		          LEFT JOIN UserPlus up ON up.user_id=uc.commented_by
		          LEFT JOIN User u ON u.identifier=uc.commented_by WHERE commented_on=".$userId."  ORDER BY uc.created_at DESC" ;//." where ".$whereQuery;

	    if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////fecthing the commets by corrector and other bo users who commented on corntributors at group selection profiles///////////////
    public function getBoUsersRecentComment($userId)
    {
        $query = "SELECT  uc.comments, uc.created_at, up.first_name, up.last_name, u.email FROM ".$this->_name." uc
		          LEFT JOIN UserPlus up ON up.user_id=uc.commented_by
		          LEFT JOIN User u ON u.identifier=uc.commented_by WHERE commented_on=".$userId."  ORDER BY uc.created_at DESC LIMIT 1" ;//." where ".$whereQuery;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////fecthing the commetns or present or not group selection profiles///////////////
	public function getCommentsCount($userId)
	{
	    $query = "SELECT  count(comments) as countcomments FROM ".$this->_name." WHERE commented_on=".$userId ;//." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}

}

