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
class Ep_Hrm_HrmLeaves extends Ep_Db_Identifier
{
    protected $_name = 'HrmLeaves';
    private $id;
    private $leave;
    private $start;
    private $end;
    private $allday;
    private $created_at;
    private $created_by;
    private $country;
    private $type;
    private $status;
    private $description;
    private $in_charge;
    private $rdv_invitees;
    private $meeting_place;
    private $rdv_reasons;
    private $mandatory;
    private $date_details;

    public function loadData($array)
    {
        $this->id=$array["id"] ;
        $this->leave=$array["leave"];
        $this->start=$array["start"];
        $this->end=$array["end"];
        $this->allday=$array["allday"];
        $this->created_by=$array["created_by"];
        $this->created_at=$array["created_at"] ;
        $this->country=$array["country"] ;
        $this->type=$array["type"] ;
        $this->status=$array["status"] ;
        $this->description=$array["description"] ;
        $this->in_charge=$array["in_charge"] ;
        $this->rdv_invitees=$array["rdv_invitees"] ;
        $this->meeting_place=$array["meeting_place"] ;
        $this->rdv_reasons=$array["rdv_reasons"] ;
        $this->date_details=$array["date_details"] ;
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["id"] = $this->getIdentifier();
        $array["leave"] = $this->leave;
        $array["start"] = $this->start;
        $array["end"] = $this->end;
        $array["allday"] = $this->allday;
        $array["created_by"] = $this->created_by;
        $array["created_at"] = $this->created_at;
        $array["country"] = $this->country;
        $array["type"] = $this->type;
        $array["status"] = $this->status;
        $array["description"] = $this->description;
        $array["in_charge"] = $this->in_charge;
        $array["rdv_invitees"] = $this->rdv_invitees;
        $array["meeting_place"] = $this->meeting_place;
        $array["rdv_reasons"] = $this->rdv_reasons;
        $array["mandatory"] = $this->mandatory;
        $array["date_details"] = $this->date_details;
        return $array;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }
    //Function to check profile exists
    public function getLeaves($id, $user, $type, $status)
    {
        $where = '';
        if($id != null)
            $where.= " AND id = '".$id."' ";
        if($user != null)
            $where.= " AND created_by = '".$user."' ";
        if($type != null)
            $where.= " AND type = '".$type."' ";
        if($status != null)
            $where.= " AND status = '".$status."' ";
         $query = "SELECT * FROM ".$this->_name." WHERE 1=1 ".$where;
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }

    //Function to check profile exists
    public function getAppliedLeaves($user)
    {

        $query = "SELECT * FROM ".$this->_name." WHERE created_by IN ('".$user."', '1') AND type IN ('public','vacation','sick','maternity','rdv', 'compoff')";
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
        $query = "SELECT * FROM ".$this->_name." WHERE in_charge IN ('".$user."') AND type IN ('public','vacation','sick','maternity','rdv', 'compoff')
                   AND status IN ('approved','refused','inprocess')";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //Function to get leaves applied by manager's subordinates///
    public function inprocessLeaves($user)
    {
        $query = "SELECT count(id) AS inprocesscount FROM ".$this->_name." WHERE in_charge IN ('".$user."') AND status IN ('inprocess')";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //Function to get leaves applied by manager's subordinates///
    public function inprocessLeavesList($user)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE in_charge IN ('".$user."') AND status IN ('inprocess')";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //////////get count of all bo users/////////////
    public function getAllBoUsers()
    {
        $Query = "select u.identifier, up.first_name, up.last_name from User u LEFT JOIN UserPlus up ON u.identifier = up.user_id WHERE type NOT IN ('contributor','client','chiefodigeo','superclient')";
        if(($result = $this->getQuery($Query,true)) != NULL){
            foreach($result as $key=>$value)
            {
                $userstats = $this->getLeavesStatistics($value['identifier']);
                $bouser_list[$value['identifier']]= array(0=>utf8_encode($value['first_name'].' '.$value['last_name']), 1=>$userstats[1]['sickleaves'],
                                                   2=>$userstats[2]['vacations'], 3=>$userstats[3]['maternityleaves'], 4=>$userstats[4]['rdvbreaks']);

                /*$bouser_list[$value['identifier']]['sickleaves'] = $userstats[1]['sickleaves'];
                $bouser_list1[$value['identifier']]['vacations'] = $userstats[2]['vacations'];
                $bouser_list1[$value['identifier']]['maternityleaves'] = $userstats[3]['maternityleaves'];
                $bouser_list1[$value['identifier']]['rdvbreaks'] = $userstats[4]['rdvbreaks'];*/
            }
            return $bouser_list;
        }else
            return "NO";
    }
    //////////get count of all bo users/////////////
    public function getUsersLeaveStats()
    {
        $Query = "select u.identifier, up.first_name, up.last_name from User u LEFT JOIN UserPlus up ON u.identifier = up.user_id WHERE u.type NOT IN ('contributor','client','chiefodigeo','superclient') ";
        if(($result = $this->getQuery($Query,true)) != NULL){
            return $result;
        }else
            return "NO";
    }
    ///// get the no of leaves based on criteria /////
    public function  getLeavesStatistics($userid)
    {
        $query1 =  "select count(id) AS publicholidays  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'public' ";
        $query2 =  "select count(id) AS sickleaves  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'sick' ";
        $query3 =  "select count(id) AS vacations  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'vacation' ";
        $query4 =  "select count(id) AS maternityleaves  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'materinity' ";
        $query5 =  "select count(id) AS rdvbreaks  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'rdv' ";
        $query6 =  "select count(id) AS slapproved  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'sick'  AND status = 'approved'";
        $query7 =  "select count(id) AS slrejected  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'public' AND status = 'refused' ";
        $query8 =  "select count(id) AS slapprovalpending  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'sick' AND status = 'inprocess' ";
        $query9 =  "select count(id) AS plapproved  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'vacation'  AND status = 'approved'";
        $query10 =  "select count(id) AS plrejected  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'vacation' AND status = 'refused' ";
        $query11 =  "select count(id) AS plapprovalpending  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'vacation' AND status = 'inprocess' ";
        $query12 =  "select count(id) AS mlapproved  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'maternity'  AND status = 'approved'";
        $query13 =  "select count(id) AS mlrejected  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'maternity' AND status = 'refused' ";
        $query14 =  "select count(id) AS mlapprovalpending  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'maternity' AND status = 'inprocess' ";
        $query15 =  "select count(id) AS rdvapproved  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'rdv'  AND status = 'approved'";
        $query16 =  "select count(id) AS rdvrejected  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'rdv' AND status = 'refused' ";
        $query17 =  "select count(id) AS rdvapprovalpending  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'rdv' AND status = 'inprocess' ";
        $query18 =  "select count(id) AS compoffapproved  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'compoff'  AND status = 'approved'";
        $query19 =  "select count(id) AS compoffrejected  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'compoff' AND status = 'refused' ";
        $query20 =  "select count(id) AS compoffapprovalpending  FROM ".$this->_name." WHERE created_by = '".$userid."' AND type = 'compoff' AND status = 'inprocess' ";

        $result1 = $this->getQuery($query1,true);
        $result2 = $this->getQuery($query2,true);
        $result3 = $this->getQuery($query3,true);
        $result4 = $this->getQuery($query4,true);
        $result5 = $this->getQuery($query5,true);
        $result6 = $this->getQuery($query6,true);
        $result7 = $this->getQuery($query7,true);
        $result8 = $this->getQuery($query8,true);
        $result9 = $this->getQuery($query9,true);
        $result10 = $this->getQuery($query10,true);
        $result11 = $this->getQuery($query11,true);
        $result12 = $this->getQuery($query12,true);
        $result13 = $this->getQuery($query13,true);
        $result14 = $this->getQuery($query14,true);
        $result15 = $this->getQuery($query15,true);
        $result16 = $this->getQuery($query16,true);
        $result17 = $this->getQuery($query17,true);
        $result18 = $this->getQuery($query18,true);
        $result19 = $this->getQuery($query19,true);
        $result20 = $this->getQuery($query20,true);
        /*if(empty($result12))
            $result11[12]['plrejected'] = 0;*/
        $result21=array_merge($result1, $result2, $result3, $result4, $result5, $result6, $result7, $result8, $result9, $result10, $result11, $result12, $result13, $result14, $result15, $result16, $result17, $result18, $result19, $result20);
        return $result21;
    }
    ///// get the no of leaves based on criteria /////
    public function  getValidateStatistics($userid)
    {
        $query1 =  "select count(id) AS rdvapprovalpending  FROM ".$this->_name." WHERE  in_charge = '".$userid."' AND type = 'rdv' AND status =  'inprocess'";
        $query2 =  "select count(id) AS sickleaves  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'sick' ";
        $query3 =  "select count(id) AS vacations  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'vacation' ";
        $query4 =  "select count(id) AS maternityleaves  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'materinity' ";
        $query5 =  "select count(id) AS rdvbreaks  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'rdv' ";
        $query6 =  "select count(id) AS slapproved  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'sick'  AND status = 'approved'";
        $query7 =  "select count(id) AS slrejected  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'sick' AND status = 'refused' ";
        $query8 =  "select count(id) AS slapprovalpending  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'sick' AND status = 'inprocess' ";
        $query9 =  "select count(id) AS plapproved  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'vacation'  AND status = 'approved'";
        $query10 =  "select count(id) AS plrejected  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'vacation' AND status = 'refused' ";
        $query11 =  "select count(id) AS plapprovalpending  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'vacation' AND status = 'inprocess' ";
        $query12 =  "select count(id) AS mlapproved  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'maternity'  AND status = 'approved'";
        $query13 =  "select count(id) AS mlrejected  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'maternity' AND status = 'refused' ";
        $query14 =  "select count(id) AS mlapprovalpending  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'maternity' AND status = 'inprocess' ";
        $query15 =  "select count(id) AS compoffapproved  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'compoff'  AND status = 'approved'";
        $query16 =  "select count(id) AS compoffrejected  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'compoff' AND status = 'refused' ";
        $query17 =  "select count(id) AS compoffapprovalpending  FROM ".$this->_name." WHERE in_charge = '".$userid."' AND type = 'compoff' AND status = 'inprocess' ";
        $result1 = $this->getQuery($query1,true);
        $result2 = $this->getQuery($query2,true);
        $result3 = $this->getQuery($query3,true);
        $result4 = $this->getQuery($query4,true);
        $result5 = $this->getQuery($query5,true);
        $result6 = $this->getQuery($query6,true);
        $result7 = $this->getQuery($query7,true);
        $result8 = $this->getQuery($query8,true);
        $result9 = $this->getQuery($query9,true);
        $result10 = $this->getQuery($query10,true);
        $result11 = $this->getQuery($query11,true);
        $result12 = $this->getQuery($query12,true);
        $result13 = $this->getQuery($query13,true);
        $result14 = $this->getQuery($query14,true);
        $result15 = $this->getQuery($query15,true);
        $result16 = $this->getQuery($query16,true);
        $result17 = $this->getQuery($query17,true);
        /*if(empty($result12))
            $result11[12]['plrejected'] = 0;*/
        $result18=array_merge($result1, $result2, $result3, $result4, $result5, $result6, $result7, $result8, $result9, $result10, $result11, $result12, $result13, $result14, $result15, $result16, $result17);
        return $result18;
    }

    //fetch all public holidays ///
    public function publicHolidays($year, $country)
    {
        $query = "SELECT *  FROM ".$this->_name." WHERE type = 'public' AND YEAR(start) = '".$year."' AND country = '".$country."' ";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //fetch all public holidays ///
    public function publicHolidaysValidation($year)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE type = 'public' AND YEAR(start) = '".$year."' ";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //fetch all paid leaves of last year of user ///
    public function lastYearPaidLeaveBalance($user)
    {
        $lastyear = date("Y",strtotime("-1 year"));
        $query = "SELECT count(id) AS lastyearplcount  FROM ".$this->_name." WHERE created_by = '".$user."' AND YEAR(start) = '".$lastyear."' AND type IN ('vacation') ";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    public function updateHrmLeaves($data,$query)
    {
        //$where=" user_id='".$identifier."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->updateQuery($data,$query);
    }
    public function deleteHrmLeaves($id)
    {
        $where="id='".$id."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->deleteQuery($where);
    }
    //Function to check profile exists
    public function getUserLeaves($user)
    {
        /*$query = "SELECT hrm.*, u.email, up.first_name, up.last_name FROM ".$this->_name." hrm INNER JOIN User u ON u.identifier = hrm.created_by
                 INNER JOIN UserPlus up ON up_user_id = hrm.created_by   WHERE hrm.created_by ='".$user."' ";*/
         $query = "SELECT * FROM ".$this->_name." WHERE created_by = '".$user."'";

        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //Function to check profile exists
    public function getcompoffs($user)
    {
        /*$query = "SELECT hrm.*, u.email, up.first_name, up.last_name FROM ".$this->_name." hrm INNER JOIN User u ON u.identifier = hrm.created_by
                 INNER JOIN UserPlus up ON up_user_id = hrm.created_by   WHERE hrm.created_by ='".$user."' ";*/
        $query = "SELECT * FROM ".$this->_name." WHERE created_by = '".$user."' AND type = 'compoff' AND status IN ('approved', 'inprocess')";

        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //Function to check profile exists
    public function getUserStatsLeaves($user)
    {
        /*$query = "SELECT hrm.*, u.email, up.first_name, up.last_name FROM ".$this->_name." hrm INNER JOIN User u ON u.identifier = hrm.created_by
                 INNER JOIN UserPlus up ON up_user_id = hrm.created_by   WHERE hrm.created_by ='".$user."' ";*/
        $query = "SELECT * FROM ".$this->_name." WHERE created_by = '".$user."' AND status NOT IN ('inprocess', 'refused')";

        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //Function to check profile exists
    public function getManagersPendingLeaves($incharge)
    {
        /*$query = "SELECT hrm.*, u.email, up.first_name, up.last_name FROM ".$this->_name." hrm INNER JOIN User u ON u.identifier = hrm.created_by
                 INNER JOIN UserPlus up ON up_user_id = hrm.created_by   WHERE hrm.created_by ='".$user."' ";*/
        $query = "SELECT * FROM ".$this->_name." WHERE in_charge = '".$incharge."'";

        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    //get the detials of eache leave when clicked on the leaves stats in my leave page///
    public function  getLeaveDetails($userid, $type, $status)
    {
        if($status == 'total')
            $where = " AND status IN ('inprocess','refused', 'approved') ";
        else
            $where = " AND status = '".$status."' ";
        $query =  "select * FROM ".$this->_name." WHERE  created_by = '".$userid."' AND type = '".$type."' $where";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

}

