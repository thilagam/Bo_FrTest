<?php
/**
 * Ep_Participation_Participation
 * @author Admin
 * @package Participation
 * @version 1.0
 */
class Ep_Payment_Invoice extends Ep_Db_Identifier
{
	protected $_name = 'Invoice';
    private $invoiceId;
	private $user_id;
	private $total_invoice;
    private $total_invoice_paid;
    private $tax;
    private $payment_info_type;
    private $vat_check;
    private $invoice_path;
    private $status;
	private $created_at;
    private $updated_at;
	public function loadData($array)
	{
		$this->user_id=$array["user_id"];
        $this->total_invoice=$array["total_invoice"];
        $this->total_invoice_paid=$array["total_invoice_paid"];
		$this->tax=$array["tax"];
		$this->payment_info_type=$array["payment_info_type"];
        $this->vat_check=$array["vat_check"];
        $this->invoice_path=$array["invoice_path"];
        $this->status=$array["status"];
		$this->created_at=$array["created_at"];
        $this->updated_at=$array["updated_at"];
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["invoiceId"] = $this->invoiceId;
		$array["user_id"] = $this->user_id;
		$array["total_invoice"] = $this->total_invoice;
        $array["total_invoice_paid"] = $this->total_invoice_paid;
		$array["tax"] = $this->tax;
		$array["payment_info_type"] = $this->payment_info_type;
        $array["vat_check"] = $this->vat_check;
        $array["invoice_path"] = $this->invoice_path;
        $array["status"] = $this->status;
		$array["created_at"] = $this->created_at;
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }
    public function __get($name){
            return $this->$name;
    }
    /////////get contributors who need to get paid for their write ups ///////////////////////////
    public function contribsPayableList($where)
    {
        $query = "select i.invoiceId, i.user_id, i.created_at, i.total_invoice_paid, i.payment_type, CONCAT(up.first_name,' ',up.last_name) as first_name,i.status,i.refuse_count,i.updated_at,i.ep_admin_fee,i.ep_admin_fee_percentage,i.pay_later_month,i.pay_later_percentage
                  from ".$this->_name." i
	              INNER JOIN Royalties r ON i.invoiceId=r.invoiceId LEFT JOIN UserPlus up ON i.user_id=up.user_id WHERE 1=1 ".$where." GROUP BY i.invoiceId ORDER BY i.created_at DESC";
        //echo $query;exit;
        /* $result1 = $this->getQuery($query,true);
         $query2 = "select count(invoiceId) as paidinvoices FROM ".$this->_name." WHERE status='Paid'  ";
         $result2 = $this->getQuery($query2,true);
         $query3 = "select count(invoiceId) as notpaidinvoices FROM ".$this->_name." WHERE status='Not paid'  ";
         $result3 = $this->getQuery($query3,true);
         $result = array_merge($result1, $result2, $result3);
         if($result != NULL)*/
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////////get invoivces of contributors which already got paid for their write ups ///////////////////////////
	public function paidInvoices($sWhere, $sOrder, $sLimit, $params)
	{
        $where = '';
        if($params['searchsubmit'] == 'search')
        {
            if($params['invoicename']!='')
            {
                $where.= " AND i.invoiceId='ep_invoice_".$params['invoicename']."'";
            }
            if(!is_null($params['contribname']) &&  $params['contribname']!=0)
            {
                //$where = " up.first_name LIKE '%".$contribname."%'";
                $where.= " AND up.user_id ='".$params['contribname']."'";
            }
            if($params['sel_type']!='All' && $params['sel_type']!='')
            {
                 $where.= " AND i.payment_type='".$params['sel_type']."'";
            }
            if($params['start_date']!='' && $params['end_date']!='')
            {
                $start_date = str_replace('/','-',$params['start_date']);
                $end_date = str_replace('/','-',$params['end_date']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $where.= " AND DATE_FORMAT(i.created_at, '%Y-%m-%d')  BETWEEN '".$start_date."' AND '".$end_date."'";
            }
            if($params['pdstart_date']!='' && $params['pdend_date']!='')
            {
                $start_date = str_replace('/','-',$params['pdstart_date']);
                $end_date = str_replace('/','-',$params['pdend_date']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $where.= " AND DATE_FORMAT(i.updated_at, '%Y-%m-%d')  BETWEEN '".$start_date."' AND '".$end_date."'";
            }
        }
        $query = "select i.invoiceId, i.user_id,IF(i.updated_at,i.updated_at, i.created_at) as created_at, i.created_at AS invoicedate, i.updated_at AS paiddate,
                  i.total_invoice_paid, i.payment_type, i.status, CONCAT(up.first_name,' ',up.last_name) as first_name, up.user_id AS identifier
                  from ".$this->_name." i
	              INNER JOIN UserPlus up ON i.user_id=up.user_id
	              WHERE i.status IN ('Paid') ".$where." ".$sWhere." ".$sOrder." ".$sLimit."";
	 // echo $query;//exit;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////get payments details in stats ///////////////////////////
	public function payments($searchParameters)
	{
      /* $query1 = "select d.id, d.title, d.created_at, d.total_article, up.first_name, sum(a.price_final) as total_paid from Delivery d
                 INNER JOIN UserPlus up ON d.user_id=up.user_id
                 INNER JOIN Article a ON d.id=a.delivery_id WHERE".$where." GROUP BY d.id";
       $query2 = "select d.id, p.amount, sum(a.price_final)-p.amount as tobe_paid from Delivery d
                 INNER JOIN Article a ON d.id=a.delivery_id
                 INNER JOIN Payment p ON d.id=p.delivery_id
                 WHERE a.paid_status='notpaid' AND p.status='Paid' AND ".$where." GROUP BY d.id";
        $query3 = "select d.id, p.amount, sum(a.price_final)+p.amount as amount_paid from Delivery d
                 INNER JOIN Article a ON d.id=a.delivery_id
                 INNER JOIN Payment p ON d.id=p.delivery_id
                 WHERE a.paid_status='paid' AND p.status='Paid' AND ".$where." GROUP BY d.id";
        $result1 = $this->getQuery($query1,true);
        $result2 = $this->getQuery($query2,true);
        $result3 = $this->getQuery($query3,true);
       for($i=0; $i<count($result1);$i++)
       {
         // if($result1[$i]['id']==$result2[$i]['id'])
         // {
              $result[$i]['id'] = $result1[$i]['id'];
              $result[$i]['title'] = $result1[$i]['title'];
              $result[$i]['created_at'] = $result1[$i]['created_at'];
              $result[$i]['total_article'] = $result1[$i]['total_article'];
              $result[$i]['first_name'] = $result1[$i]['first_name'];
              $result[$i]['total_paid'] = $result1[$i]['total_paid'];
              $result[$i]['tobe_paid'] = $result2[$i]['tobe_paid'];
              $result[$i]['amount_paid'] = $result3[$i]['amount_paid'];
        //  }
       }*/
        $where='';
        $having='';
        if($searchParameters['pay_status']!=NULL)
        {
            if($searchParameters['pay_status']=='paid')
                $having.=' HAVING tobe_paid=0';
            else if($searchParameters['pay_status']=='Not paid')
                $having.=' HAVING tobe_paid> 0';
        }
        if($searchParameters['ao_list']!=NULL && $searchParameters['ao_list']!='all')
        {
            $where.=" AND d.id='".$searchParameters['ao_list']."'";
        }
         if($searchParameters['client_list']!=NULL && $searchParameters['client_list']!='all')
        {
            $where.=" AND d.user_id='".$searchParameters['client_list']."'";
        }
        if($searchParameters['ep_category']!=NULL && $searchParameters['ep_category']!='all')
        {
            $where.=" AND a.category='".$searchParameters['ep_category']."'";
        }
        if($searchParameters['language']!=NULL && $searchParameters['language']!='all')
        {
            $where.=" AND a.language='".$searchParameters['language']."'";
        }
        if($searchParameters['start_date']!=NULL && $searchParameters['end_date']!=NULL)
        {
                $start_date = date('Y-m-d', strtotime($searchParameters['start_date']));
                $end_date = date('Y-m-d', strtotime($searchParameters['end_date']));
                $where = " AND DATE_FORMAT(d.created_at, '%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'";
        }
        $query="SELECT c.user_id, c.company_name as first_name,d.created_at,d.delivery_date,d.title,d.id,count(*) as total_article,
                sum(a.price_final+d.premium_total) as total_paid,
                (SUM(IF(paid_status='paid' ,price_final+d.premium_total,0))+(IF (a.created_by!='BO',p.amount,0))) as amount_paid,
                (sum(a.price_final+d.premium_total))-(SUM(IF(paid_status='paid' ,price_final+d.premium_total,0))+(IF (a.created_by!='BO',p.amount,0))) as tobe_paid
                FROM
                  Delivery d
                INNER JOIN Article a ON a.delivery_id=d.id
                INNER JOIN Payment p ON p.delivery_id=d.id
                INNER JOIN Client c  ON c.user_id=d.user_id
                where p.status='Paid'".$where."
                GROUP BY a.delivery_id
                ".$having."
                ORDER BY d.created_at DESC";
        //echo  $query;exit;
        $result = $this->getQuery($query,true);
        if($result  != NULL)
			return $result;
		else
			return "NO";
	}
     /////////get payments details in stats ///////////////////////////
	public function paymentsnew($searchParameters)
	{
      /* $query1 = "select d.id, d.title, d.created_at, d.total_article, up.first_name, sum(a.price_final) as total_paid from Delivery d
                 INNER JOIN UserPlus up ON d.user_id=up.user_id
                 INNER JOIN Article a ON d.id=a.delivery_id WHERE".$where." GROUP BY d.id";
       $query2 = "select d.id, p.amount, sum(a.price_final)-p.amount as tobe_paid from Delivery d
                 INNER JOIN Article a ON d.id=a.delivery_id
                 INNER JOIN Payment p ON d.id=p.delivery_id
                 WHERE a.paid_status='notpaid' AND p.status='Paid' AND ".$where." GROUP BY d.id";
        $query3 = "select d.id, p.amount, sum(a.price_final)+p.amount as amount_paid from Delivery d
                 INNER JOIN Article a ON d.id=a.delivery_id
                 INNER JOIN Payment p ON d.id=p.delivery_id
                 WHERE a.paid_status='paid' AND p.status='Paid' AND ".$where." GROUP BY d.id";
        $result1 = $this->getQuery($query1,true);
        $result2 = $this->getQuery($query2,true);
        $result3 = $this->getQuery($query3,true);
       for($i=0; $i<count($result1);$i++)
       {
         // if($result1[$i]['id']==$result2[$i]['id'])
         // {
              $result[$i]['id'] = $result1[$i]['id'];
              $result[$i]['title'] = $result1[$i]['title'];
              $result[$i]['created_at'] = $result1[$i]['created_at'];
              $result[$i]['total_article'] = $result1[$i]['total_article'];
              $result[$i]['first_name'] = $result1[$i]['first_name'];
              $result[$i]['total_paid'] = $result1[$i]['total_paid'];
              $result[$i]['tobe_paid'] = $result2[$i]['tobe_paid'];
              $result[$i]['amount_paid'] = $result3[$i]['amount_paid'];
        //  }
       }*/
        $where='';
        $having='';
        if($searchParameters['pay_status']!=NULL)
        {
            if($searchParameters['pay_status']=='paid')
                $having.=' HAVING tobe_paid=0';
            else if($searchParameters['pay_status']=='Not paid')
                $having.=' HAVING tobe_paid> 0';
        }
        if($searchParameters['ao_list']!=NULL && $searchParameters['ao_list']!='all')
        {
            $where.=" AND d.id='".$searchParameters['ao_list']."'";
        }
         if($searchParameters['client_list']!=NULL && $searchParameters['client_list']!='all')
        {
            $where.=" AND d.user_id='".$searchParameters['client_list']."'";
        }
        if($searchParameters['ep_category']!=NULL && $searchParameters['ep_category']!='all')
        {
            $where.=" AND a.category='".$searchParameters['ep_category']."'";
        }
        if($searchParameters['language']!=NULL && $searchParameters['language']!='all')
        {
            $where.=" AND a.language='".$searchParameters['language']."'";
        }
        if($searchParameters['start_date']!=NULL && $searchParameters['end_date']!=NULL)
        {
                $start_date = date('Y-m-d', strtotime($searchParameters['start_date']));
                $end_date = date('Y-m-d', strtotime($searchParameters['end_date']));
                $where = " AND DATE_FORMAT(d.created_at, '%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'";
        }
          $query="(SELECT p1.id, p1.amount, p1.created_at, p1.delivery_id as del_id, u.identifier as key1,
                      d.title as title, u.email as client, 'payments' as type, p1.pay_type as paymode  FROM Payment p1, Delivery d, User u
                       where p1.delivery_id=d.id and d.user_id=u.identifier and (p1.amount='0'  or p1.pay_type!='BO'))
                      UNION
                     (SELECT p2.id, p2.amount, p2.created_at, 'virtual' as del_id, p2.user_id as key1, 'Basket' as title,
                      u1.email as client, 'payments_article' as type, p2.pay_type as paymode FROM  Payment_article p2, User u1
                      where p2.user_id=u1.identifier and p2.amount!=0) order by created_at  desc";
        //echo  $query;exit;
        $result = $this->getQuery($query,true);
        if($result  != NULL)
			return $result;
		else
			return "NO";
	}
    /////////get contributors id  ///////////////////////////
	public function getContributorId($invoiceid)
	{
	     $query = "select user_id from ".$this->_name." WHERE invoiceId = 'ep_invoice_".$invoiceid."'";
	   //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    public function getInvoiceDetails($useridentifier,$invoiceId=NULL)
    {
        if($invoiceId)
                $invoiceCondition=" r.invoiceId='".$invoiceId."'";
        else
            $invoiceCondition=" r.invoiceId IS NULL";
        $articleQuery="select i.payment_info_type,i.invoice_path,d.title as DeliveryTitle,a.title as AOTitle,a.id as articleId,r.price,r.created_at  from Royalties r
                                INNER JOIN Article a ON a.id=r.article_id
                                INNER JOIN Delivery d ON d.id=a.delivery_id
                                LEFT JOIN Invoice i ON r.invoiceId=i.invoiceId
                                WHERE r.user_id='".$useridentifier."' and ".$invoiceCondition;
         //echo $articleQuery;
        if(($count=$this->getNbRows($articleQuery))>0)
        {
            $invoiceDetails=$this->getQuery($articleQuery,true);
            return $invoiceDetails;
        }
        else
             return "NOT EXIST";
    }
    public function updateInvoice($data,$where)
    {
       // $where="invoiceId='".$identifier."'";
       // print_r($data);exit;
        $data['updated_at']=date("Y-m-d H:i:s", time());
         $this->updateQuery($data,$where);
    }
    public function getContacts($type)
    {
        if($type=='client')
        {
            $query="select
                    u.identifier,company_name as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                    left JOIN Client  c ON u.identifier=c.user_id
                where u.type='".$type."' and u.status='Active' and u.verified_status='YES'
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                ";
        }
        else
        {
            $query="select
                    u.identifier,CONCAT(first_name,' ',last_name) as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                where u.type='".$type."' and u.status='Active' and u.verified_status='YES'
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                ";
        }
       // echo $query;exit;
        if(($users=$this->getQuery($query,true))!=NULL)
            return $users;
        else
            return "Not Exists";
    }
    public function getAllAOList()
    {
        $query="select
                    d.title,d.id
                from Delivery d
                    INNER JOIN Payment p On p.delivery_id=d.id
                where p.status='Paid' and d.title!=''
                ORDER BY d.id";
        // echo $query;exit;
        if(($AoList=$this->getQuery($query,true))!=NULL)
            return $AoList;
        else
            return "Not Exists";
    }
    public function getInvoiceStatus($invoiceId)
    {
        $invoiceId='ep_invoice_'.$invoiceId;
        $statusQuery="Select status From Invoice WHERE invoiceId='".$invoiceId."'";
        if(($status=$this->getQuery($statusQuery,true))!=NULL)
            return $status[0]['status'];
        else
            return "Not Exists";
    }
    /* by naseer on 16-07-2015 */
    /////////get invoivces of contributors which already got paid for their write ups ///////////////////////////
    public function clientInvoices($conditions)
    {
        $WHERE = '';
        $ORDER = 'ORDER BY `PA`.`user_id` DESC,PA.`created_at` DESC';
        if($conditions['search'] == 'search'){
            $WHERE .= ($conditions['sel_type'] == 'PP' || $conditions['sel_type'] == 'CC' || $conditions['sel_type'] == 'Paypal' || $conditions['sel_type'] == 'FO') ? " AND `pay_type` = '".$conditions['sel_type']."'" : "AND ( `pay_type` = 'CC' OR `pay_type` =  'PP' OR `pay_type` =  'Paypal' OR `pay_type` =  'FO')";
            $clientnames = $conditions['clientname'];
            $WHERE .= ( !empty($clientnames) ) ? " AND PA.`user_id` IN (".implode(",",$conditions['clientname']).")" : '';
            if($conditions['pdstart_date']!=NULL && $conditions['pdend_date']!=NULL)
            {
                $start_date = date('Y-m-d', strtotime($conditions['pdstart_date']));
                $end_date = date('Y-m-d', strtotime($conditions['pdend_date']));
                $WHERE .= " AND DATE_FORMAT(PA.created_at, '%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'";
                $ORDER = " ORDER BY `PA`.`user_id` DESC,PA.`created_at` DESC ";
            }

        }
        else{
            $WHERE .= " AND ( `pay_type` = 'CC' OR `pay_type` =  'PP' OR `pay_type` =  'Paypal' OR `pay_type` =  'FO' )";
        }

        $query = "SELECT
                    PA.`id`, PA.`user_id`,  PA.`amount_paid`, PA.`type`,
                    DATE_FORMAT(PA.`created_at`, \"%d-%m-%Y\") AS created_at, PA.`pay_type`, PA.`mc_currency`, PA.`tax`,
                    U.email,
                    CONCAT(UP.`first_name`,' ', UP.`last_name`) AS client_name,
                    C.`company_name`,
                    ART.`title` as article_title, ART.`id` as article_id, ART.`price_final` as amount
                    FROM `Payment_article` AS PA
                    LEFT JOIN `User` AS U ON PA.`user_id` = U.`identifier`
                    LEFT JOIN `UserPlus` AS UP ON UP.`user_id` = U.`identifier`
                    LEFT JOIN `Client` AS C ON C.`user_id` = U.`identifier`
                    LEFT JOIN `Article` AS ART ON ART.`invoice_id` = PA.`id` 
					INNER JOIN Participation p ON p.article_id=ART.id
                    WHERE  PA.`id` != '0' AND p.status='published' AND p.cycle=0 ".$WHERE.$ORDER;
        //echo $query; exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

}
