<?php

class Ep_Ao_PaymentArticle extends Ep_Db_Identifier
{
	protected $_name = 'Payment_article';
	
	public function insertPayment_article($pay_arr)
	{
		$Parray = array(); 
		$Parray["id"] = $this->getIdentifier(); 
			$cust=explode("|",$pay_arr['custom']);
		$Parray["user_id"] = $cust[0]; 
		
		$pay_arr['mc_gross']=str_replace(",",".",$pay_arr['mc_gross']);
		$Parray["amount_paid"] = $pay_arr['mc_gross']; //with tax
		$Parray["amount"] = $cust[3]; //without tax total
		$Parray["type"] = $pay_arr['payment_type']; 
		$Parray["pay_type"] = "Paypal";
		$Parray["ipn_track_id"]=$pay_arr['ipn_track_id']; 
		$Parray["item_number"]=$pay_arr['item_number1']; 
		$Parray["payer_id"]=$pay_arr['payer_id']; 
		$Parray["first_name"]=$pay_arr['first_name']; 
		$Parray["last_name"]=$pay_arr['last_name']; 
		$Parray["payer_status"]=$pay_arr['payer_status'];
		$Parray["verify_sign"]=$pay_arr['verify_sign']; 			
		$Parray["payer_email"]=$pay_arr['payer_email']; 
		$Parray["txn_id"]=$pay_arr['txn_id']; 
		$Parray["receiver_id"]=$pay_arr['receiver_id']; 
		$Parray["mc_currency"]=$pay_arr['mc_currency']; 
		$Parray["tax"]=20; 
		
		$this->insertQuery($Parray);
		return $this->getIdentifier();
	}
	
	public function insertpayedclient($client)
	{
		$Clarray = array(); 
		$Clarray["id"] = $this->getIdentifier(); 
		$Clarray["user_id"] = $client; 
		$Clarray["amount_paid"] = 0; //with tax
		$Clarray["amount"] = 0; //without tax total
		$Clarray["type"] = 'instant'; 
		$Clarray["pay_type"] = "FO";
		$Parray["tax"]=20; 
		$this->insertQuery($Clarray);
		return $this->getIdentifier();
	}
	
	public function updatePaymentarticle($array,$where)
	{
		$this->updateQuery($array,$where);
	}
	
	public function Listbilling($client)
	{
		$billQuery="SELECT 
						DISTINCT(p.id) as id,a.title,d.id as did,a.id as aid,a.currency,d.created_at as ddate,p.amount,p.amount_paid,p.created_at,ap.article_sent_at,LOWER(up.first_name) as first_name,up.last_name as last_name
					FROM 
						Payment_article p INNER JOIN Article a ON p.id=a.invoice_id 
						INNER JOIN Delivery d ON a.delivery_id=d.id 
						LEFT JOIN Participation pr ON pr.article_id=a.id
						LEFT JOIN ArticleProcess ap ON ap.participate_id=pr.id
						LEFT JOIN UserPlus up ON pr.user_id=up.user_id
					WHERE 
						p.user_id='".$client."' AND pr.status IN ('published') GROUP BY p.id";
		//echo $billQuery;
		if(($billSet= $this->getQuery($billQuery,true)) != NULL)
            return $billSet;				
		
	}
	
	public function getpaymentdetails($art)
	{
		$billQuery="SELECT 
						d.title as dtitle,a.title,d.id as did,d.client_type,a.id as aid,d.created_at as ddate,d.delivery_date,p.id as payid,p.amount,p.amount_paid,p.created_at,p.tax,ap.article_sent_at,
						LOWER(up.first_name) as first_name,up.last_name as last_name,a.price_payed,a.invoice_generated,a.currency
					FROM 
						Payment_article p INNER JOIN Article a ON p.id=a.invoice_id 
						INNER JOIN Delivery d ON a.delivery_id=d.id 
						LEFT JOIN Participation pr ON pr.article_id=a.id
						LEFT JOIN ArticleProcess ap ON ap.participate_id=pr.id
						LEFT JOIN UserPlus up ON pr.user_id=up.user_id
					WHERE 
						a.paid_status='paid' AND a.id='".$art."' GROUP BY p.id";
		
		if(($billSet= $this->getQuery($billQuery,true)) != NULL)
            return $billSet;
		else
			return "NO";
	}
	
	public function getClientdetails($cid)
	{
		$SelectQuery_cdetails="SELECT 
									c.company_name,u.address,u.zipcode,u.city,u.country
							  FROM 
									UserPlus u
							  INNER JOIN 
									Client c
							  ON
									u.user_id=c.user_id
							  WHERE 
									u.user_id='".$cid."'";	
		    
			$resultsettmp = $this->getQuery($SelectQuery_cdetails,true); 
			return $resultsettmp;	
	}
	
	public function getPremoption($art)
	{
			$SelectQuery_prem="SELECT d.premium_option, p.id 
													FROM Delivery d
										INNER JOIN 
													Article a ON d.id = a.delivery_id
										INNER JOIN 
													Participation p ON p.article_id = a.id
										WHERE a.id = '".$art."' AND p.status = 'published'";
										
			$resultsetprem = $this->getQuery($SelectQuery_prem,true); 
			
			return $resultsetprem;							
	}
	
	public function ListPaymentClient($month,$year)
	{
		$query="SELECT DISTINCT(d.user_id) FROM 
				Payment_article pa INNER JOIN Article a ON pa.id=a.invoice_id 
				INNER JOIN Delivery d ON a.delivery_id=d.id
				INNER JOIN Participation p ON p.article_id=a.id	
				LEFT JOIN Client c ON d.user_id=c.user_id
				LEFT JOIN UserPlus up ON d.user_id=up.user_id
			WHERE 
				d.premium_option='0' AND pa.pay_type='FO' AND MONTH(pa.created_at)='".$month."' AND YEAR(pa.created_at)='".$year."' 
				AND a.paid_status='paid' AND p.status='published'";
        
        $result = $this->getQuery($query,true);
        return $result;
	}
	
	public function ListPaymentsByMonth($client,$month,$year)
	{
		$query="SELECT a.id as article,a.title,pa.amount,pa.amount_paid,c.company_name,up.address,up.city,up.zipcode FROM 
				Payment_article pa INNER JOIN Article a ON pa.id=a.invoice_id 
				INNER JOIN Delivery d ON a.delivery_id=d.id
				INNER JOIN Participation p ON p.article_id=a.id	
				LEFT JOIN Client c ON d.user_id=c.user_id
				LEFT JOIN UserPlus up ON d.user_id=up.user_id
			WHERE 
				d.premium_option='0' AND pa.pay_type='FO' AND d.user_id='".$client."' AND MONTH(pa.created_at)='".$month."' AND YEAR(pa.created_at)='".$year."' 
				AND a.paid_status='paid' AND p.status='published'";
        
        $result = $this->getQuery($query,true);
        return $result;
	}
	
	public function getPaymentLiberte($client,$month,$year)
	{
		$query="SELECT a.id as article,a.title,pa.amount,pa.amount_paid,c.company_name,up.address,up.city,up.zipcode FROM 
				Payment_article pa INNER JOIN Article a ON pa.id=a.invoice_id 
				INNER JOIN Delivery d ON a.delivery_id=d.id
				INNER JOIN Participation p ON p.article_id=a.id	
				LEFT JOIN Client c ON d.user_id=c.user_id
				LEFT JOIN UserPlus up ON d.user_id=up.user_id
			WHERE 
				d.user_id='".$client."' AND d.premium_option='0' AND MONTH(pa.created_at)='".$month."' AND YEAR(pa.created_at)='".$year."' 
				AND a.paid_status='paid' AND p.status='published'";
        
        $result = $this->getQuery($query,true);
        return $result;
	}
}	
