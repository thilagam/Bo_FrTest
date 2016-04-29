<?php
/**
 * Ep_Participation_Participation
 * @author Admin
 * @package Participation
 * @version 1.0
 */
class Ep_Royalty_Royalties extends Ep_Db_Identifier
{
	protected $_name = 'Royalties';
	private $id;
	private $participate_id;
	private $article_id;
	private $user_id;
	private $price;
	private $created_at;
    private $updated_at;
    private $crt_participate_id;
    private $correction;

		
	public function loadData($array)
	{
		$this->participate_id=$array["participate_id"];
		$this->article_id=$array["article_id"];
		$this->user_id=$array["user_id"];
		$this->price=$array["price"];
		$this->created_at=$array["created_at"];
        $this->updated_at=$array["updated_at"];
        if(isset($array["crt_participate_id"]) && $array["crt_participate_id"] != '')
            $this->crt_participate_id=$array["crt_participate_id"];
        if(isset($array["correction"]) && $array["correction"] != '')
            $this->correction=$array["correction"];
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["id"] = $this->getIdentifier();
		$array["participate_id"] = $this->participate_id;
		$array["article_id"] = $this->article_id;
		$array["user_id"] = $this->user_id;
		$array["price"] = $this->price;
		$array["created_at"] = $this->created_at;
        if($this->crt_participate_id )
            $array["crt_participate_id"] = $this->crt_participate_id;
        if($this->correction)
            $array["correction"] = $this->correction;
		
		
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    public function getAllRoyaltiesWithInvoice($userIdentifier)
    {
        $query="SELECT {fn NOW()} as created_at,sum(r.price) as price ,r.article_id,r.invoiceId,i.created_at as invoicedate,i.status,
                i.total_invoice_paid,i.currency,i.ep_admin_fee,i.ep_admin_fee_percentage,i.pay_later_month,i.pay_later_percentage    
                    from
                ".$this->_name." r INNER JOIN Invoice i ON r.invoiceId=i.invoiceId where r.user_id='".$userIdentifier."'
                GROUP BY r.invoiceId
                ORDER BY r.created_at DESC";
        //echo $query;exit;

        if(($count=$this->getNbRows($query))>0)
        {
            $royalties=$this->getQuery($query,true);
            return $royalties;
        }
    }
    public function getInvoicePDFPath($invoiceid,$userIdentifier)
    {
        $invoiceid='ep_invoice_'.$invoiceid;
        $invoiceQuery="select i.invoice_path
                    from
                ".$this->_name." r INNER JOIN Invoice i ON r.invoiceId=i.invoiceId where r.user_id='".$userIdentifier."'
                and i.invoiceId='".$invoiceid."'
                GROUP BY i.invoiceId
                ORDER BY r.created_at DESC";
        //echo $invoiceQuery;exit;
        if(($count=$this->getNbRows($invoiceQuery))>0)
        {
            $invoicePath=$this->getQuery($invoiceQuery,true);
            return $invoicePath[0]['invoice_path'];
        }
        else
             return "NOT EXIST";
    }
    public function getTotalRoyalty($userIdentifier)
    {
       $query="select sum(price) as royalty,currency from ".$this->_name." where user_id='".$userIdentifier."' and invoiceID IS NULL
				Group By currency";
		if(($count=$this->getNbRows($query))>0)
		{		
			$royalties=$this->getQuery($query,true);
			return $royalties;
		}
		else
		{
			$royalties[0]['royalty']=0;
			$royalties[0]['currency']='pound';
			return $royalties;
		}	
    }
     public function getTotalUserRoyalty($userIdentifier)
    {
        $query="select sum(price) as royalty,currency from ".$this->_name." where user_id='".$userIdentifier."' Group By currency";
        if(($count=$this->getNbRows($query))>0)
		{		
			$royalties=$this->getQuery($query,true);
			return $royalties;
		}
		else
		{
			$royalties[0]['royalty']=0;
			$royalties[0]['currency']='pound';
			return $royalties;
		}
    }
    public function getArticleDetails($articleIdentifier,$invoiceId)
    {
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $contrib_identifier= $this->EP_Contrib_reg->clientidentifier;

        if($invoiceId)
            $invoiceCondition=" r.invoiceId='ep_invoice_".$invoiceId."'";
        else
            $invoiceCondition=" r.invoiceId IS NULL";

        $articleQuery="select  d.title as DeliveryTitle,a.title as AOTitle,a.id as articleId,r.price,{fn NOW()} as created_at,
                                r.created_at as publishedDate,i.created_at as invoicedate  from Royalties r
                                INNER JOIN Article a ON a.id=r.article_id
                                INNER JOIN Delivery d ON d.id=a.delivery_id
                                LEFT JOIN Invoice i ON r.invoiceId=i.invoiceId
                                WHERE ".$invoiceCondition." and r.user_id='".$contrib_identifier."'";
        /*$articleQuery="select ap.article_path,d.title as DeliveryTitle,a.title as AOTitle,r.price,r.created_at from Royalties r INNER JOIN Article a ON a.id=r.article_id INNER JOIN Delivery d ON d.id=a.delivery_id
                        INNER JOIN Participation p ON p.id=r.participate_id
                        INNER JOIN ArticleProcess ap ON p.id=ap.participate_id
                        LEFT JOIN ArticleProcess ap1 ON (p.id=ap1.participate_id AND ap.version<ap1.version)
                        WHERE r.article_id='".$articleIdentifier."' and ap1.id IS NULL";*/

        //echo $articleQuery;
        if(($count=$this->getNbRows($articleQuery))>0)
        {
            $articleDetails=$this->getQuery($articleQuery,true);
            return $articleDetails;
        }
        else
             return "NOT EXIST";
    }
    public function getArticlePath($articleIdentifier,$userIdentifier)
    {
        $articleQuery="select ap.article_path,ap.version,p.user_id,d.title as DeliveryTitle,a.title as AOTitle,r.price,r.created_at
                        from Royalties r
                        INNER JOIN Article a ON a.id=r.article_id INNER JOIN Delivery d ON d.id=a.delivery_id
                        INNER JOIN Participation p ON p.id=r.participate_id
                        INNER JOIN ArticleProcess ap ON p.id=ap.participate_id
                        LEFT JOIN ArticleProcess ap1 ON (p.id=ap1.participate_id AND ap.version < ap1.version)
                        WHERE r.article_id='".$articleIdentifier."' and p.user_id='".$userIdentifier."' and ap1.id IS NULL";

        //echo $articleQuery;exit;
        if(($count=$this->getNbRows($articleQuery))>0)
        {
            $articleDetails=$this->getQuery($articleQuery,true);
            return $articleDetails[0]['article_path'];
        }
        else
             return "NOT EXIST";
    }
    public function getInvoiceDetails($useridentifier,$invoiceId=NULL,$currency=NULL)
    {
        if($invoiceId)
                $invoiceCondition=" r.invoiceId='".$invoiceId."'";
        else
            $invoiceCondition=" r.invoiceId IS NULL";
			
		if($currency)
                $currencyCondition=" AND r.currency='".$currency."'";	

        $articleQuery="select i.payment_info_type,i.invoice_path,d.title as DeliveryTitle,a.title as AOTitle,
                                a.id as articleId,r.price,r.created_at as royalty_added_at,i.status,i.refuse_count,i.payment_type,
                                if(i.updated_at,i.updated_at,i.created_at) as Invoice_date,i.created_at,i.invoiceId,r.currency,a.created_at as article_created_date,
                                d.user_id as client_id,i.ep_admin_fee,i.ep_admin_fee_percentage,i.pay_later_month,i.pay_later_percentage,i.bank_account_name    
                       FROM Royalties r
                                INNER JOIN Article a ON a.id=r.article_id
                                INNER JOIN Delivery d ON d.id=a.delivery_id
                                LEFT JOIN Invoice i ON r.invoiceId=i.invoiceId
                                WHERE r.user_id='".$useridentifier."' $currencyCondition and ".$invoiceCondition."								
								ORDER By r.currency";
								
        /*$articleQuery="select ap.article_path,d.title as DeliveryTitle,a.title as AOTitle,r.price,r.created_at from Royalties r INNER JOIN Article a ON a.id=r.article_id INNER JOIN Delivery d ON d.id=a.delivery_id
                        INNER JOIN Participation p ON p.id=r.participate_id
                        INNER JOIN ArticleProcess ap ON p.id=ap.participate_id
                        LEFT JOIN ArticleProcess ap1 ON (p.id=ap1.participate_id AND ap.version<ap1.version)
                        WHERE r.article_id='".$articleIdentifier."' and ap1.id IS NULL";*/

       // echo $articleQuery;exit;
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
		$data['updated_at']=date("Y-m-d H:i:s");
        $this->updateQuery($data,$where);
    }

    public function updateRoyalty($invoiceId,$data)
    {
        $where="invoiceId='".$invoiceId."'";
		$data['updated_at']=date("Y-m-d H:i:s");
        $this->updateQuery($data,$where);
    }


    /**Check royalty already exists for article**/
    public function checkRoyaltyExists($articleId)
    {
        $existsQuery="select id from ".$this->_name." where  article_id='".$articleId."'";
        if(($count=$this->getNbRows($existsQuery))>0)
        {
            return "YES";
        }
        else
             return "NO";
    }

}
