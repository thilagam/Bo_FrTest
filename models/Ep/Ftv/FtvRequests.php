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
class Ep_Ftv_FtvRequests extends Ep_Db_Identifier
{
    protected $_name = 'FtvRequests';
    private $identifier;
    private $request_by;
    private $other_contacts;
    private $assigned_to;
    private $request_object;
    private $duration;
    private $modify_broadcast;
    private $modify_contains;
    private $status;
    private $created_at;
    private $assigned_at;
    private $ftvtype;

    public function loadData($array)
    {
        $this->identifier=$array["identifier"] ;
        $this->request_by=$array["request_by"];
        $this->other_contacts=$array["other_contacts"];
        $this->assigned_to=$array["assigned_to"];
        $this->request_object=$array["request_object"];
        $this->duration=$array["duration"] ;
        $this->modify_broadcast=$array["modify_broadcast"] ;
        $this->modify_contains=$array["modify_contains"] ;
        $this->status=$array["status"] ;
        $this->created_at=$array["created_at"] ;
        $this->assigned_at=$array["assigned_at"] ;
        $this->ftvtype=$array["ftvtype"] ;
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["identifier"] = $this->getIdentifier();
        $array["request_by"] = $this->request_by;
        $array["other_contacts"] = $this->other_contacts;
        $array["assigned_to"] = $this->assigned_to;
        $array["request_object"] = $this->request_object;
        $array["duration"] = $this->duration;
        $array["modify_broadcast"] = $this->modify_broadcast;
        $array["modify_contains"] = $this->modify_contains;
        $array["status"] = $this->status;
        $array["created_at"] = $this->created_at;
        $array["assigned_at"] = $this->assigned_at;
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
    public function getAllRequests($ftvtype)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE active = 'yes'"  ;
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    //Function to check profile exists
    public function requestDetailsById($requestId)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE identifier='".$requestId."'";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    //Function to check profile exists
    public function getAllRequestsDetails($params, $type)
    {  // print_r($params); //exit;
        $this->adminLogin	= Zend_Registry::get ( 'adminLogin' );
        $where = " WHERE 1=1 ";
        if($this->adminLogin->userId != '110823103540627')
             $where = " WHERE r.assigned_to = ".$this->adminLogin->userId." "; //exit;
        if($params['search'] == 'search')
        {
            $condition = '';
            if($params['startdate'] !='' && $params['enddate']!='')
            {
                $start_date = str_replace('/','-',$params['startdate']);
                $end_date = str_replace('/','-',$params['enddate']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $condition.= " AND r.created_at BETWEEN '".$start_date."' AND DATE_ADD('".$end_date."', INTERVAL 1 DAY)";
            }
            if($params['contactId']!='0')
            {
                $condition.= " AND c.identifier =".$params['contactId']." ";
            }
            if($params['broadcastId']!='0')
            {
                $condition.= " AND find_in_set('".$params['broadcastId']."', r.modify_broadcast) ";
            }
            if($params['quandId']!='0')
            {
                $condition.= " AND find_in_set('".$params['quandId']."', r.duration) ";
            }
            if($params['containsId']!='0')
            {
                $condition.= " AND find_in_set('".$params['containsId']."', r.modify_contains) ";
            }
            if(isset($params['dayrange']) && $params['dayrange']!='0' )
            {
               // if($params['dayrange'] == 'green')
               //     $condition.= " AND  DAYOFWEEK(r.created_at) IN ('1','6','7') AND (HOUR(r.created_at) > 18 OR HOUR(r.created_at) < 9) ";
               // else
                //    $condition.= " AND  DAYOFWEEK(r.created_at) IN ('2','3','4','5') AND (HOUR(r.created_at) > 18 OR HOUR(r.created_at) < 9) ";
                $condition.= " AND (HOUR(r.created_at) > 18 OR HOUR(r.created_at) < 9) ";
            }
        }

          $query = "SELECT r.*,c.first_name, pt.* FROM  ".$this->_name." r
                  INNER JOIN FtvContacts c ON c.identifier = r.request_by
                  LEFT JOIN FtvPauseTime pt ON pt.ftvrequest_id = r.identifier and pt.resume_at is null
                   ".$where." ".$condition." AND r.ftvtype='".$type."' AND r.active = 'yes'  GROUP BY r.identifier ORDER BY r.created_at DESC ";

        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    //get all the request of edito ftv on ajax pagination///
    public function getAllEditoRequestsDetails($sWhere, $sOrder, $sLimit, $params)
    {  // print_r($params); //exit;
        $this->adminLogin	= Zend_Registry::get ( 'adminLogin' );
        $where = " WHERE 1=1 ";
        if($this->adminLogin->userId != '110823103540627')
            $where = " WHERE r.assigned_to = ".$this->adminLogin->userId." "; //exit;
        if($params['search'] == 'search')
        {
            $condition = '';
            if($params['startdate'] !='' && $params['enddate']!='')
            {
                $start_date = str_replace('/','-',$params['startdate']);
                $end_date = str_replace('/','-',$params['enddate']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $condition.= " AND r.created_at BETWEEN '".$start_date."' AND DATE_ADD('".$end_date."', INTERVAL 1 DAY)";
            }
            if($params['contactId']!='0')
            {
                $condition.= " AND c.identifier =".$params['contactId']." ";
            }
            if($params['broadcastId']!='0')
            {
                $condition.= " AND find_in_set('".$params['broadcastId']."', r.modify_broadcast) ";
            }
            if($params['quandId']!='0')
            {
                $condition.= " AND find_in_set('".$params['quandId']."', r.duration) ";
            }
            if($params['containsId']!='0')
            {
                $condition.= " AND find_in_set('".$params['containsId']."', r.modify_contains) ";
            }
            if(isset($params['dayrange']) && $params['dayrange']!='0' )
            {
                // if($params['dayrange'] == 'green')
                //     $condition.= " AND  DAYOFWEEK(r.created_at) IN ('1','6','7') AND (HOUR(r.created_at) > 18 OR HOUR(r.created_at) < 9) ";
                // else
                //    $condition.= " AND  DAYOFWEEK(r.created_at) IN ('2','3','4','5') AND (HOUR(r.created_at) > 18 OR HOUR(r.created_at) < 9) ";
                $condition.= " AND (HOUR(r.created_at) > 18 OR HOUR(r.created_at) < 9) ";
            }
        }
        /*edited by naseer on 15-09-2015 */
        $query = "SELECT r.*,c.first_name, pt.* FROM  ".$this->_name." r
                  INNER JOIN FtvContacts c ON c.identifier = r.request_by
                  LEFT JOIN FtvPauseTime pt ON pt.ftvrequest_id = r.identifier and pt.resume_at is null
                   ".$where." ".$condition." AND r.ftvtype='edito' AND r.active = 'yes'  ".$sWhere." ".$sOrder." ".$sLimit." ";
        /*end of edited by naseer on 15-09-2015 */
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    //get all the request of chaine///
    public function getAllChaineRequestsDetails($sWhere, $sOrder, $sLimit, $params)
    {  // print_r($params); //exit;
        $this->adminLogin	= Zend_Registry::get ( 'adminLogin' );
        $where = " WHERE 1=1 ";
        if($this->adminLogin->userId != '110823103540627')
            $where = " WHERE r.assigned_to = ".$this->adminLogin->userId." "; //exit;
        if($params['search'] == 'search')
        {
            $condition = '';
            if($params['startdate'] !='' && $params['enddate']!='')
            {
                $start_date = str_replace('/','-',$params['startdate']);
                $end_date = str_replace('/','-',$params['enddate']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $condition.= " AND r.created_at BETWEEN '".$start_date."' AND DATE_ADD('".$end_date."', INTERVAL 1 DAY)";
            }
            if($params['contactId']!='0')
            {
                $condition.= " AND c.identifier =".$params['contactId']." ";
            }
            if($params['broadcastId']!='0')
            {
                $condition.= " AND find_in_set('".$params['broadcastId']."', r.modify_broadcast) ";
            }
            if($params['quandId']!='0')
            {
                $condition.= " AND find_in_set('".$params['quandId']."', r.duration) ";
            }
            if($params['containsId']!='0')
            {
                $condition.= " AND find_in_set('".$params['containsId']."', r.modify_contains) ";
            }
            if(isset($params['dayrange']) && $params['dayrange']!='0' )
            {
                // if($params['dayrange'] == 'green')
                //     $condition.= " AND  DAYOFWEEK(r.created_at) IN ('1','6','7') AND (HOUR(r.created_at) > 18 OR HOUR(r.created_at) < 9) ";
                // else
                //    $condition.= " AND  DAYOFWEEK(r.created_at) IN ('2','3','4','5') AND (HOUR(r.created_at) > 18 OR HOUR(r.created_at) < 9) ";
                $condition.= " AND (HOUR(r.created_at) > 18 OR HOUR(r.created_at) < 9) ";
            }
        }
           $query = "SELECT r.*,c.first_name, pt.* FROM  ".$this->_name." r
                  INNER JOIN FtvContacts c ON c.identifier = r.request_by
                  LEFT JOIN FtvPauseTime pt ON pt.ftvrequest_id = r.identifier and pt.resume_at is null
                   ".$where." ".$condition." AND r.ftvtype='chaine' AND r.active = 'yes'  ".$sWhere." ".$sOrder." ".$sLimit." ";

        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    //Function to check profile exists
    public function getRequestsDetails($requestId)
    {  // print_r($params); //exit;
        $query = "SELECT r.*,c.first_name, pt.* FROM  ".$this->_name." r
                  INNER JOIN FtvContacts c ON c.identifier = r.request_by
                  LEFT JOIN FtvPauseTime pt ON pt.ftvrequest_id = r.identifier and pt.resume_at is null
                  Where r.identifier= '".$requestId."' AND r.active = 'yes' ";

        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    // get the recent record //
    public function getRecentInsertedId()
    {
        $query = "SELECT identifier FROM ".$this->_name." ORDER BY created_at DESC LIMIT 1";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //Function to check profile exists
    public function getRequestById($requestId)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE identifier=".$requestId;
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }

    public function updateFtvRequests($data,$query)
    {
        //$where=" user_id='".$identifier."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->updateQuery($data,$query);

    }
    public function fetchTimeline($data){
        $from = ( $data['pdstart_date'] != '' ) ? date('Y-m-d H:i:s',strtotime($data['pdstart_date']) ) : '2014-01-01 00:00:00';
        $to = ( $data['pdend_date'] != '' ) ? date('Y-m-d H:i:s',strtotime($data['pdend_date']) ) : date('Y-m-d H:i:s');
        $request_by = implode(",",$data['contactname']);
        $request_by = rtrim($request_by,',');
        $condition1 = ($request_by != '') ? "FR.`request_by` IN (".$request_by.") AND " : '';
        $condition2 = ( $data['sel_type'] != '' ) ?  "FR.`ftvtype` = '".$data['sel_type']."' AND " : '';
        $query = "SELECT FR.`identifier` , FR.`request_by` , CONCAT( FC.`first_name` , ' ', FC.`last_name` ) AS contactname,
        FR.`assigned_at` , FPT.`pause_at` , FPT.`resume_at` , FR.`closed_at` , FR.`cancelled_at` , FR.`request_object` ,
        FR.`assigned_to` , CONCAT( UP.`first_name` , ' ', UP.`last_name` ) AS assignedname,FPT.`ftvrequest_id`,
        FR.`ftvtype`,FR.`active`
        FROM `FtvRequests` AS FR
        LEFT OUTER JOIN `FtvPauseTime` AS FPT ON FR.`identifier` = FPT.`ftvrequest_id`

        INNER JOIN `FtvContacts` AS FC ON FC.`identifier` = FR.`request_by`
        INNER JOIN `UserPlus` AS UP ON UP.`user_id` = FR.`assigned_to`
        WHERE
        ".$condition1.$condition2."
            ( FR.`assigned_at` BETWEEN '$from' AND '$to' )
        ORDER BY FR.`identifier` ASC;
        ";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
}

