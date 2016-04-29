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
class Ep_Hrm_HrmCompOff extends Ep_Db_Identifier
{
    protected $_name = 'HrmCompOff';
    private $id;
    private $comp_off;
    private $start;
    private $end;
    private $project_manager;
    private $create_at;
    private $created_by;
    private $status;
    private $compdate_details;

    public function loadData($array)
    {
        $this->id=$array["id"] ;
        $this->comp_off=$array["comp_off"];
        $this->start=$array["start"];
        $this->end=$array["end"];
        $this->project_manager=$array["project_manager"];
        $this->created_by=$array["created_by"];
        $this->create_at=$array["create_at"] ;
        $this->status=$array["status"] ;
        $this->compdate_details=$array["compdate_details"] ;
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["id"] = $this->getIdentifier();
        $array["comp_off"] = $this->comp_off;
        $array["start"] = $this->start;
        $array["end"] = $this->end;
        $array["project_manager"] = $this->project_manager;
        $array["created_by"] = $this->created_by;
        $array["create_at"] = $this->create_at;
        $array["status"] = $this->status;
        $array["compdate_details"] = $this->compdate_details;
        return $array;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }

    //Function to check profile exists
    public function getAppliedCompOffs($user)
    {

        $query = "SELECT * FROM ".$this->_name." WHERE created_by = '".$user."' ";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //Function to check profile exists
    public function getAppliedCompOffsCeoUser()
    {

        $query = "SELECT * FROM ".$this->_name." WHERE 1=1 ";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //Function to get all remaining compoff which are not compensated
    public function getNotCompensatedCompOffs($user, $year)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE created_by = '".$user."' AND status = 'Active' AND year(start) = ".$year;
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //Function to get leaves applied by manager's subordinates///
    public function getValidateLeaves($user)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE in_charge IN ('".$user."') AND type IN ('public','vacation','sick','maternity','rdv')
                   AND status IN ('approved','refused','inprocess')";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    public function updateHrmCompOff($data,$query)
    {
        //$where=" user_id='".$identifier."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->updateQuery($data,$query);
    }

}

