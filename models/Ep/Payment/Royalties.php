<?php
/**
 * Ep_Participation_Participation
 * @author Admin
 * @package Participation
 * @version 1.0
 */
class Ep_Payment_Royalties extends Ep_Db_Identifier
{
	protected $_name = 'Royalties';
	private $id;
	private $participate_id;
    private $crt_participate_id;
	private $article_id;
	private $user_id;
	private $price;
	private $created_at;
    private $updated_at;
    private $correction;

	public function loadData($array)
	{
        $this->participate_id=$array["participate_id"];
        $this->crt_participate_id=$array["crt_participate_id"];
		$this->article_id=$array["article_id"];
		$this->user_id=$array["user_id"];
		$this->price=$array["price"];
		$this->created_at=$array["created_at"];
        $this->updated_at=$array["updated_at"];
        $this->correction=$array["correction"];
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["id"] = $this->getIdentifier();
		$array["participate_id"] = $this->participate_id;
        $array["crt_participate_id"] = $this->crt_participate_id;
		$array["article_id"] = $this->article_id;
		$array["user_id"] = $this->user_id;
		$array["price"] = $this->price;
		$array["created_at"] = $this->created_at;
        $array["correction"] = $this->correction;
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }
    public function __get($name){
            return $this->$name;
    }
    public function getAllRoyalties($userIdentifier)
    {
        $query="select * from ".$this->_name." where user_id='".$userIdentifier."'";
        //echo $query;exit;
        if(($count=$this->getNbRows($query))>0)
        {
            $royalties=$this->getQuery($query,true);
            return $royalties;
        }
    }
    public function getTotalRoyalty($userIdentifier)
    {
        $query="select sum(price) as royalty from ".$this->_name." where user_id='".$userIdentifier."'";
        $royalties=$this->getQuery($query,true);
        return $royalties[0]['royalty'];
    }
    public function getInvoiceDetails($invoiceId)
    {
        $invoiceCondition=" r.invoiceId='".$invoiceId."'";
        $articleQuery="select i.payment_info_type,i.user_id,i.invoice_path,i.created_at,up.first_name,up.last_name,d.title as DeliveryTitle,a.title as AOTitle,a.id as articleId,r.price,r.created_at  from Royalties r
                                INNER JOIN Article a ON  a.id=r.article_id
                                INNER JOIN Delivery d ON d.id=a.delivery_id
                                LEFT JOIN Invoice i ON r.invoiceId=i.invoiceId
                                LEFT JOIN UserPlus up ON up.user_id=i.user_id
                                WHERE ".$invoiceCondition;
        /*$articleQuery="select ap.article_path,d.title as DeliveryTitle,a.title as AOTitle,r.price,r.created_at from Royalties r INNER JOIN Article a ON a.id=r.article_id INNER JOIN Delivery d ON d.id=a.delivery_id
                        INNER JOIN Participation p ON p.id=r.participate_id
                        INNER JOIN ArticleProcess ap ON p.id=ap.participate_id
                        LEFT JOIN ArticleProcess ap1 ON (p.id=ap1.participate_id AND ap.version<ap1.version)
                        WHERE r.article_id='".$articleIdentifier."' and ap1.id IS NULL";*/
        //echo $articleQuery;
        if(($count=$this->getNbRows($articleQuery))>0)
        {
            $invoiceDetails=$this->getQuery($articleQuery,true);
            return $invoiceDetails;
        }
        else
             return "NOT EXIST";
    }
    public function getInvoicePDFPath($invoiceid)
    {
        $invoiceid='ep_invoice_'.$invoiceid;
         $invoiceQuery="select i.invoice_path from ".$this->_name." r LEFT JOIN Invoice i ON r.invoiceId=i.invoiceId where
                       i.invoiceId='".$invoiceid."' GROUP BY i.invoiceId  ORDER BY r.created_at DESC";
        //echo $invoiceQuery;exit;
        if(($count=$this->getNbRows($invoiceQuery))>0)
        {
            $invoicePath=$this->getQuery($invoiceQuery,true);
            return $invoicePath[0]['invoice_path'];
        }
        else
             return "NOT EXIST";
    }
    public function getInvoiceDetails2($useridentifier,$invoiceId=NULL)
    {
        if($invoiceId){
            $invoiceId = "ep_invoice_".$invoiceId;
                $invoiceCondition=" r.invoiceId='".$invoiceId."'";
        }
        else
            $invoiceCondition=" r.invoiceId IS NULL";
        $articleQuery="select i.payment_info_type, i.payment_type, i.payment_info_id,i.refuse_count, i.invoice_path,d.title as DeliveryTitle,a.title as AOTitle,a.id as articleId,r.price,r.created_at,i.vat_check,a.created_at as article_created_date,
                                d.user_id as client_id,i.created_at as requested_date,i.ep_admin_fee,i.ep_admin_fee_percentage,i.pay_later_month,i.pay_later_percentage,        
                                IF(i.updated_at,i.updated_at,i.created_at) AS invoiceDate,i.bank_account_name,
                                a.product,a.type,a.language_source,a.language,d.contract_mission_id,a.files_pack
                              from Royalties r
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
	/*public function deleteRoyality($art,$part)
	{
		$existsQuery="select id from ".$this->_name." where article_id='".$art."' AND participate_id='".$part."'";
        if(($count=$this->getNbRows($existsQuery))>0)
        {
			$whereQuery="article_id='".$art."' AND participate_id='".$part."'";
			$this->deleteQuery($whereQuery);
		}
		
	}*/
	
	public function deleteRoyality($art,$part)
	{
		$royalityQuery="select id,invoiceId from ".$this->_name." where article_id='".$art."' AND participate_id='".$part."'";
        $royalityset=$this->getQuery($royalityQuery,true);
		
		if(count($royalityset)>0)
        {
			if($royalityset[0]['invoiceId']!="")
			{
				$existsQuery="select invoiceId from Invoice where  invoiceId='".$royalityset[0]['invoiceId']."' AND status='Not paid'";
					if(($count=$this->getNbRows($existsQuery))>0)
					{
						$this->_name='Invoice';
						$whereQueryinv="invoiceId='".$royalityset[0]['invoiceId']."'";
						$this->deleteQuery($whereQueryinv);
					}
			}
			$this->_name='Royalties';
			$whereQuery="article_id='".$art."' AND participate_id='".$part."'";
			$this->deleteQuery($whereQuery);
		}
	}
}
