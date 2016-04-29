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
class Ep_Ftv_FtvDocuments extends Ep_Db_Identifier
{
    protected $_name = 'FtvDocuments';
    private $identifier;
    private $request_id;
    private $document_by;
    private $document;
    private $created_at;

    public function loadData($array)
    {
        $this->identifier=$array["identifier"] ;
        $this->request_id=$array["request_id"];
        $this->document_by=$array["document_by"];
        $this->document=$array["document"] ;
        $this->created_at=$array["created_at"] ;
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["identifier"] = $this->getIdentifier();
        $array["request_id"] = $this->request_id;
        $array["document_by"] = $this->document_by;
        $array["document"] = $this->document;
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
    public function getDocumentsByRequests($requestId)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE request_id = '".$requestId."' ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";

    }
    //get only recent document by BO user//
    public function getRecentDocument($requestId)
    {
        $query = "SELECT document FROM ".$this->_name."
                     WHERE request_id = '".$requestId."' ORDER BY created_at DESC LIMIT 1 ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";

    }
    //get only recent document by BO user//
    public function checkRecentDocument($requestId)
    {
        $query = "SELECT document FROM ".$this->_name."
                     WHERE request_id = '".$requestId."' ORDER BY created_at DESC LIMIT 1 ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";

    }


    public function updateFtvDocuments($data,$query)
    {
        //$where=" user_id='".$identifier."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->updateQuery($data,$query);

    }
}

