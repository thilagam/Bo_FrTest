<?php
/**
 * Ep_Participation_Participation
 * @author Admin
 * @package Participation
 * @version 1.0
 */
class Ep_Royalty_Invoice extends Ep_Db_Identifier
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
    private $payment_type;
    private $payment_info_id;
    private $created_at;
    private $updated_at;
    private $currency;

    private $ep_admin_fee;
    private $ep_admin_fee_percentage;
    private $pay_later_month;
    private $pay_later_percentage;
    private $bank_account_name;

		
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
	        $this->payment_type=$array["payment_type"];
	        $this->payment_info_id=$array["payment_info_id"];
		$this->created_at=$array["created_at"];
       		$this->updated_at=$array["updated_at"];	
		$this->currency=$array["currency"];

        $this->ep_admin_fee=$array["ep_admin_fee"];
        $this->ep_admin_fee_percentage=$array["ep_admin_fee_percentage"];
        $this->pay_later_month=$array["pay_later_month"];
        $this->pay_later_percentage=$array["pay_later_percentage"];
        $this->bank_account_name=$array["bank_account_name"];

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
	        $array["payment_type"]=$this->payment_type;
	        $array["payment_info_id"]=$this->payment_info_id;
		$array["created_at"] = $this->created_at;
		$array["currency"] = $this->currency;

        $array["ep_admin_fee"] = $this->ep_admin_fee;
        $array["ep_admin_fee_percentage"] = $this->ep_admin_fee_percentage;
        $array["pay_later_month"] = $this->pay_later_month;
        $array["pay_later_percentage"] = $this->pay_later_percentage;
        $array["bank_account_name"] = $this->bank_account_name;
		
		
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    public function getMonthlyCount($contrib_identifier)
    {
        $date=date("Y-m-");
        $invoice_cnt_query="SELECT count(*) as count_num From ".$this->_name."
                                WHERE invoiceId Like 'ep_invoice_".$date."%'
                                        and user_id='".$contrib_identifier."'";
       // echo $invoice_cnt_query;exit;
        if(($result=$this->getQuery($invoice_cnt_query,true))!=NULL)
        {
            if($result[0]['count_num']!=NULL)
                return $result[0]['count_num'];
            else
                return 0;
        }

    }
    public function updateInvoiceDetails($invoiceId,$data)
    {
        $where="invoiceId='".$invoiceId."'";
		$data['updated_at']=date("Y-m-d H:i:s");
        $this->updateQuery($data,$where);
    }

    public function getMonthlyUnpaidInvoices($contrib_identifier,$currency)
    {
        $current_month= date("Y-m-");
        $month_invoice_query="SELECT * From ".$this->_name."
                                WHERE invoiceId Like 'ep_invoice_".$current_month."%'
                                AND user_id='".$contrib_identifier."'
                                AND status='Not paid'
                                AND refuse_count=0
				AND currency='".$currency."'
                                ";
	      // echo $month_invoice_query;exit;

        if(($result=$this->getQuery($month_invoice_query,true))!=NULL)
        {
           return $result;           
        } 
         else
            return NULL;  

    }
    //delete Invoice
    public function DeleteInvoice($id)
    {
        
        if($id)
        {
            $whereQuery ="invoiceId ='".$id."'";
            $this->deleteQuery($whereQuery);
        }    
    }

    //Cron function to get monthly unpaid invocies to change to in process
    public function getMonthlyProcessInvoices()
    {
        $date=date("Y-m-01");
        $process_query="SELECT *,MONTH(i.created_at + INTERVAL (i.pay_later_month) MONTH) as pay_month From ".$this->_name." i
                            INNER JOIN Royalties r ON i.invoiceId=r.invoiceId
                            WHERE date_format(i.created_at, '%Y-%m-%d')<'".$date."' AND i.Status='Not Paid'                            
                            GROUP BY i.invoiceId 
                            Having pay_month=date_format(now(), '%m')
                            ORDER BY i.created_at DESC";
        //echo $process_query;exit;
        if(($result=$this->getQuery($process_query,true))!=NULL)
        {
            return $result;
        }
        else
            return NULL;

    }

    public function getPreviousMonthInvoice($curr)
	{
		$query="SELECT 
					SUM( `total_invoice_paid` ) as total_invoice_paid,
					SUM( `total_invoice` ) as total_invoice,
					SUM(((`ep_admin_fee_percentage`/100)* `total_invoice`) +((`pay_later_percentage`/100)* (`total_invoice` - ((`ep_admin_fee_percentage`/100)* `total_invoice`)))  ) as fees
				FROM 
					`Invoice` WHERE currency='".$curr."' AND YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
				AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";	
		
		if($ResultDetails=$this->getQuery($query,true))
           return $ResultDetails;
        else
            return NULL;
	}

	public function getInvoicetopaydetails($monthgap,$curr)
	{
		$query="SELECT 
					SUM( `total_invoice_paid` ) as total_invoice_paid,
					SUM( `total_invoice` ) as total_invoice,
					SUM(((`ep_admin_fee_percentage`/100)* `total_invoice`) +((`pay_later_percentage`/100)* (`total_invoice` - ((`ep_admin_fee_percentage`/100)* `total_invoice`)))  ) as fees
				FROM 
					`Invoice` WHERE currency='".$curr."' AND YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
				AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND pay_later_month='".$monthgap."' ";	
		
		if($ResultDetails=$this->getQuery($query,true))
           return $ResultDetails; 
        else
            return NULL;
	}
}