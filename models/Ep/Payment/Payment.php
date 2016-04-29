<?php
/**
 * Ep_Article_Options
 * @author Admin
 * @package Options
 * @version 1.0
 */
 
class Ep_Payment_Payment extends Ep_Db_Identifier
{
	protected $_name = 'Payment';
	
		public function insertPayment($Parr)
		{ 
			$Parray=array();
			$Parray["id"] = $this->getIdentifier(); 
			$Parray['delivery_id']=$Parr['delivery_id'];
			$Parr['amount_paid']=str_replace(",",".",$Parr['amount_paid']);
			$Parray['amount_paid']=$Parr['amount_paid'];
			$Parray['amount']=$Parr['amount_paid'];
			$Parray['status']=$Parr['status'];
			$Parray['pay_type']='BO';
			
			if($Parr['ref_bo']!='')
				$Parray['ref_bo']=$Parr['ref_bo'];
			
			if($this->insertQuery($Parray))
			   return $this->getIdentifier();
			else
			   return 'No';		
		}
		
		public function insertPayment_article($pay_arr)
        {
                    $this->_name="Payment_article";
                    $pay_arr["id"] = $this->getIdentifier();
                    $this->insertQuery($pay_arr);
                    return $this->getIdentifier();
        }

		public function listInvoice($cl,$inv)
		{
			if($inv==2)
			{
				$SelectQuery="SELECT DATE_FORMAT(p.created_at, '%d/%m/%y') as created_at,p.created_at as createdat,p.amount,p.amount_paid,p.delivery_id,p.id,p.status FROM Delivery d INNER JOIN Payment p ON d.id=p.delivery_id WHERE d.created_by!='BO' and p.amount_paid>0 and d.user_id=".$cl;
				$res_array = $this->getQuery($SelectQuery,true);
			}
			elseif($inv==1)
			{
				$SelectQuery_pa="SELECT 
								id,total_article,DATE_FORMAT(delivery_date, '%d/%m/%y') delivery_date,premium_total
							  FROM 
									Delivery
							  WHERE 
									user_id='".$cl	."'";	
		    
				$resultsetpaya = $this->getQuery($SelectQuery_pa,true); 
				$res_array=array();
				$n=0;
				for($p=0;$p<count($resultsetpaya);$p++)
				{
				$SelectQuery_artcnt="SELECT count(*) as pubcnt, sum(a.price_final+".$resultsetpaya[$p]['premium_total'].") as artsum
								  FROM 
										Article a
								  INNER JOIN 
										Participation p
								  ON
										a.id=p.article_id
								  WHERE 
										a.delivery_id='".$resultsetpaya[$p]['id']."' AND p.status='published' AND a.paid_status='paid'";	
				$resultset1= $this->getQuery($SelectQuery_artcnt,true); 
				//echo $SelectQuery_artcnt."<br>";
				if($resultset1[0]['pubcnt']==$resultsetpaya[$p]['total_article'])
				{
					$res_array[$n]['id']=$resultsetpaya[$p]['id'];
					$res_array[$n]['created_at']=$resultsetpaya[$p]['delivery_date'];
					$res_array[$n]['amount']=$resultset1[0]['artsum'];
					$n++;
				}
				}
			}
			if(count($res_array)>0)
				return $res_array;
			else
				return "NO";
			
		}
		
		public function listInvoicearticle($ccid)
		{
			$SelectQuery_pa="SELECT 
										id,amount,DATE_FORMAT(created_at, '%d/%m/%y') as created_at,created_at as createdat
								  FROM 
										Payment_article
								  WHERE 
										user_id='".$ccid."'";	
				
				$resultsetpaya = $this->getQuery($SelectQuery_pa,true); 
				//return $resultsetpaya;
				if(count($resultsetpaya)>0)
					return $resultsetpaya;
				else
					return "NO";
		}
		public function getPaydetails($pid,$uid)
		{
		/*$SelectQuery_pdetails="SELECT
									p.amount,p.created_at,a.title,(a.price_final+d.premium_total) as price_final,a.id
							  FROM 
									Payment_article p
							  INNER JOIN Article a ON p.id=a.invoice_id
							  INNER JOIN Delivery d ON d.id=a.delivery_id
							  WHERE 
									p.id='".$pid."' AND p.user_id='".$uid."'";	*/
									
			$SelectQuery_pdetails="SELECT
									p.amount,p.created_at,a.title,(a.price_final+d.premium_total) as price_final,a.id,p.pay_type,a.price_payed
							  FROM 
									Payment_article p
							  INNER JOIN Article a ON p.id=a.invoice_id
							  INNER JOIN Delivery d ON d.id=a.delivery_id
							  INNER JOIN Participation Pa ON a.id=Pa.article_id				
							  WHERE 
									p.id='".$pid."' AND p.user_id='".$uid."' AND Pa.status='published'";
									
		    //echo $SelectQuery_pdetails;exit;
			$resultsetpay = $this->getQuery($SelectQuery_pdetails,true); 
				return $resultsetpay;	
		}
		
		public function getPayaodetails($pid,$uid)
		{
			$SelectQuery_pdetails="SELECT 
										p.amount,p.amount_paid,p.created_at,d.title,d.total_article,p.delivery_id,d.created_by
								  FROM 
										Payment p
								  INNER JOIN 
										Delivery d
								  ON
										p.delivery_id=d.id
								  WHERE 
										p.id='".$pid."' AND d.user_id='".$uid."'";	
				//echo $SelectQuery_pdetails;
				$resultsetpay = $this->getQuery($SelectQuery_pdetails,true); 
				return $resultsetpay;	
		}
		
		public function getPaydetailscomplete($pid)
		{
				$SelectQuery_pdetails="SELECT
										d.title as dtitle, p.amount,d.delivery_date,a.title,(a.price_final+d.premium_total) as price_final,a.price_payed,a.id,d.created_by
								  FROM 
										Article a 
								  INNER JOIN Delivery d ON d.id=a.delivery_id
								  INNER JOIN Payment p ON p.delivery_id=d.id
								  WHERE 
										d.id='".$pid."'";
										
				//echo $SelectQuery_pdetails;exit;
				$resultsetpay = $this->getQuery($SelectQuery_pdetails,true); 
				return $resultsetpay;	
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
		
		public function getArtSum($ddid)
		{
			$SelectQuery1="SELECT sum( a.price_final + d.premium_total ) AS paym
				FROM Article a
				INNER JOIN Delivery d ON a.delivery_id = d.id
				WHERE a.delivery_id=".$ddid;	
			$resultd = $this->getQuery($SelectQuery1,true);
			return $resultd[0]['paym'];
		}
    public function insertPayments($data)
    {
        $this->insertQuery($data);
    }
    public function getInvoices($ao)
    {
        $SelectQuery   =   "SELECT p.article_id, a.invoice_id, d.user_id FROM Article a INNER JOIN Participation p ON a.id = p.article_id INNER JOIN Delivery d ON d.id = a.delivery_id WHERE p.status='published' AND (a.invoice_id > 0) AND d.id = ".$ao ;
        return $this->getQuery($SelectQuery,true) ;
    }
		
}	