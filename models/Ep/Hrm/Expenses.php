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
class Ep_Hrm_Expenses extends Ep_Db_Identifier
{
    protected $_name = 'Expenses';
    private $identifier;
    private $expense_name;
    private $expense_amount;
    private $expense_date;
    private $expense_invoice;
    private $expense_currency;
    private $batch_id;
    private $expense_rib;
    private $created_at;
    private $created_by;


    public function loadData($array)
    {
        $this->identifier=$array["identifier"] ;
        $this->expense_name=$array["expense_name"];
        $this->expense_amount=$array["expense_amount"];
        $this->expense_date=$array["expense_date"];
        $this->expense_invoice=$array["expense_invoice"];
        $this->expense_currency=$array["expense_currency"];
        $this->batch_id=$array["batch_id"];
        $this->expense_rib=$array["expense_rib"];
        $this->created_by=$array["created_by"];
        $this->created_at=$array["created_at"] ;
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["identifier"] = $this->identifier;
        $array["expense_name"] = $this->expense_name;
        $array["expense_amount"] = $this->expense_amount;
        $array["expense_date"] = $this->expense_date;
        $array["expense_invoice"] = $this->expense_invoice;
        $array["expense_currency"] = $this->expense_currency;
        $array["batch_id"] = $this->batch_id;
        $array["expense_rib"] = $this->expense_rib;
        $array["created_by"] = $this->created_by;
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
    public function getLatestExpense()
    {
       echo  $query = "SELECT identifier FROM ".$this->_name." ORDER BY created_at DESC LIMIT 1";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    //Function to check profile exists
    public function getExpenses()
    {
        $query = "(SELECT up.user_id, concat(up.first_name, ' ', up.last_name) AS expenses_by, e.batch_id, e.created_by, e.expense_currency, SUM(e.expense_amount) AS total, e.expense_date
                    FROM `Expenses` e INNER JOIN UserPlus up ON e.created_by = up.user_id WHERE e.expense_currency = 'euro' GROUP BY MONTH(e.expense_date), e.created_by ORDER BY e.created_at ASC)
                UNION
                  (SELECT up.user_id, concat(up.first_name, ' ', up.last_name) AS expenses_by, e.batch_id, e.created_by, e.expense_currency, SUM(e.expense_amount) AS total, e.expense_date
                    FROM `Expenses` e INNER JOIN UserPlus up ON e.created_by = up.user_id WHERE e.expense_currency = 'pound' GROUP BY MONTH(e.expense_date), e.created_by ORDER BY e.created_at ASC)";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    //Function to check profile exists
    public function getExpensesDetails($userid, $month, $year, $currency)
    {
        $query = "SELECT e.*, concat(up.first_name, ' ', up.last_name) AS expenses_by FROM ".$this->_name." e
                        INNER JOIN UserPlus up ON e.created_by = up.user_id WHERE e.created_by = '".$userid."'
                  And MONTH(e.expense_date) = '".$month."'  AND YEAR(e.expense_date) = '".$year."' AND e.expense_currency ='".$currency."'";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    //Function to check profile exists
    public function getExpensesListToPay($userid, $month, $year, $currency)
    {
         $query = "SELECT identifier FROM ".$this->_name." WHERE created_by = '".$userid."' AND MONTH(expense_date) = '".$month."'
                AND YEAR(expense_date) = '".$year."' AND expense_currency ='".$currency."' AND status IS NULL";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    //Function to check profile exists
    public function getExpenseDetials($expid)
    {
        $query = "SELECT e.*, concat(up.first_name, ' ', up.last_name) AS expenses_by FROM ".$this->_name." e
                 INNER JOIN UserPlus up ON e.created_by = up.user_id WHERE e.identifier=".$expid;
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    public function updateExpenses($data,$query)
    {
        //$where=" user_id='".$identifier."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->updateQuery($data,$query);
    }
    public function deleteExpenses($id)
    {
        $where="id='".$id."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->deleteQuery($where);
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

