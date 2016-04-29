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
class Ep_Ftv_FtvContacts extends Ep_Db_Identifier
{
    protected $_name = 'FtvContacts';
    private $identifier;
    private $email_id;
    private $password;
    private $first_name;
    private $last_name;
    private $created_at;
    private $client_name;
    private $ftvtype;

    public function loadData($array)
    {
        $this->identifier=$array["identifier"] ;
        $this->email_id=$array["email_id"];
        $this->password=$array["password"];
        $this->first_name=$array["first_name"] ;
        $this->last_name=$array["last_name"] ;
        $this->created_at=$array["created_at"] ;
        $this->client_name=$array["client_name"] ;
        $this->ftvtype=$array["ftvtype"] ;
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["identifier"] = $this->getIdentifier();
        $array["email_id"] = $this->email_id;
        $array["password"] = $this->password;
        $array["first_name"] = $this->first_name;
        $array["last_name"] = $this->last_name;
        $array["created_at"] = $this->created_at;
        $array["client_name"] = $this->client_name;
        $array["ftvtype"] = $this->ftvtype;
        return $array;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }
    //Function to check profile exists
    public function checkProfileExist($identifier)
    {
        $whereQuery = "identifier = '".$identifier."'";
        $existsQuery = "select * from ".$this->_name." where ".$whereQuery;

        if(($result = $this->getQuery($existsQuery,true)) != NULL)
            return $result[0]["identifier"];
        else
            return "NO";

    }
    ///////check the email is exits////
    public function getExistingEmail($email)
    {
        $query = "SELECT identifier FROM ".$this->_name." WHERE email_id = '".$email."'";
        if(($result = $this->getQuery($query,false)) != NULL)
        {
            return "yes";
        }
        else
            return "no";
    }
    // get requesting for contact creation //
    public function getRequestCreatedContacts()
    {
        $query = "SELECT c.identifier, c.first_name, r.identifier AS request_id FROM ".$this->_name." c
                INNER JOIN FtvRequests  r ON r.request_by = c.identifier GROUP BY c.identifier";
        if(($result = $this->getQuery($query,ture)) != NULL)
        {
            foreach($result as $key=>$value)
            {
                $contact_list[$value['identifier']]=strtoupper($value['email'].'('.$value['first_name'].')');
            }
            if($contact_list)
                asort($contact_list);
            if($contact_list)
                array_unshift($contact_list, "S&eacute;lectionner");
            return $contact_list;
        }
        else
            return "no";
    }
    ///////check the email is exits////
    public function getExistingEmailUpdate($contactId, $email)
    {
        $query = "SELECT identifier FROM ".$this->_name." WHERE email_id = '".$email."' AND identifier != '".$contactId."' ";
        if(($result = $this->getQuery($query,false)) != NULL)
            return "yes";
        else
            return "no";
    }
    // get the detials of single contact ///
    public function getFtvContactDetails($contactId)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE identifier = '".$contactId."' ";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    // get all the ftv contacts on based on type //
    public function getFtvContacts($type)
    {
         $query = "SELECT * FROM ".$this->_name." WHERE ftvtype IN ('".$type."','both') ";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    public function updateFtvContacts($data,$query)
    {
        //$where=" user_id='".$identifier."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->updateQuery($data,$query);

    }
}

