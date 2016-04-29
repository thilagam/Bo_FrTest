<?php
/* *
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
class Ep_Ftv_FtvComments extends Ep_Db_Identifier
{
    protected $_name = 'FtvComments';
    private $identifier;
    private $request_id;
    private $comment_by;
    private $comments;
    private $user_type;
    private $created_at;

    public function loadData($array)
    {
        $this->identifier=$array["identifier"] ;
        $this->request_id=$array["request_id"];
        $this->comment_by=$array["comment_by"];
        $this->comments=$array["comments"] ;
        $this->user_type=$array["user_type"] ;
        $this->created_at=$array["created_at"] ;
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["identifier"] = $this->getIdentifier();
        $array["request_id"] = $this->request_id;
        $array["comment_by"] = $this->comment_by;
        $array["comments"] = $this->comments;
        $array["user_type"] = $this->user_type;
        $array["created_at"] = $this->created_at;
        return $array;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }
    //Function to check profile exists
    //Function to check profile exists
    //get all comments of request///
    public function getCommentsByRequests($requestId)
    {
         $query = "SELECT c.*,ct.first_name,up.first_name AS bo_user FROM ".$this->_name." c
                    LEFT JOIN FtvContacts ct ON c.comment_by = ct.identifier
                    LEFT JOIN UserPlus up ON c.comment_by = up.user_id
                    WHERE c.request_id = '".$requestId."' ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";

    }
    //get only recent comment by BO user//
    public function getRecentCommentsByBoUser($requestId)
    {
        $query = "SELECT comments FROM ".$this->_name."
                     WHERE request_id = '".$requestId."' ORDER BY created_at DESC LIMIT 1 ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";

    }


    public function updateFtvComments($data,$query)
    {
        //$where=" user_id='".$identifier."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->updateQuery($data,$query);

    }
}

