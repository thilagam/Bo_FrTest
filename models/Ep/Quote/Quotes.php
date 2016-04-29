<?php
/**
 * Ep_Quote_Quotes
 * @author Arun
 * @package Client
 * @version 1.0
 */
class Ep_Quote_Quotes extends Ep_Db_Identifier
{
	protected $_name = 'Quotes';

	//Insert Quote data
	public function insertQuote($data)
	{
		$data['identifier']=$this->getIdentifier();
		$this->insertQuery($data);	
	}
	public function updateQuote($data,$identifier)
    {
        //print_r($data);exit;
		$where=" identifier='".$identifier."'";
        $this->updateQuery($data,$where);
    }
    //delete quotes permenently
    public function deleteQuote($identifier)
	{
		if($identifier!=""){
		$whereQuery ="identifier ='".$identifier."'";
		$this->deleteQuery($whereQuery);
		}
	}
	public function getMonthlyCount($client_id)
    {
        $date=date("Y-m-");
        $quote_cnt_query="SELECT count(*) as count_num From ".$this->_name."
                                WHERE created_at  Like '".$date."%'
                                        and client_id ='".$client_id."'";
       // echo $invoice_cnt_query;exit;
        if(($result=$this->getQuery($quote_cnt_query,true))!=NULL)
        {
            if($result[0]['count_num']!=NULL)
                return $result[0]['count_num'];
            else
                return 0;
        }

    }

    function getAllQuotesList($searchParams=NULL)
    {
    	$userType=$this->adminLogin->type;
		$userId=$this->adminLogin->userId;
		/*added w.r.t new system*/
		if($searchParams['new_quote_system']=='yes')
		{

			if($userType=='seouser' || $userType=='seomanager' )
			{
				$condition.=" AND ((seo_review!='auto_skipped' AND seo_review!='skipped') OR (sales_review='briefing')) AND find_in_set('".$userId."',brief_email_notify)";
			}
			else if($userType=='techuser'  || $userType=='techmanager')
			{
				$condition.=" AND ((tec_review!='auto_skipped' AND tec_review!='skipped') OR (sales_review='briefing')) AND find_in_set('".$userId."',brief_email_notify)";
			}
			else if($userType=='produser' || $userType=='prodsubmanager' || $userType=='multilingue' || $userType=='prodmanager' )
			{
				$condition.=" AND ((prod_review!='auto_skipped' AND prod_review!='skipped') OR (sales_review='briefing'))  AND find_in_set('".$userId."',brief_email_notify )";
			}
            else if($userType=='salesuser')
            {
                $condition.=" AND (quote_by='".$userId."' OR find_in_set('".$userId."',brief_email_notify))";
            }
		}	
		else{
			
			if($userType=='seouser' || $userType=='seomanager' )
			{
				$condition.=" AND seo_review!='auto_skipped' AND seo_review!='skipped'";
			}
			else if($userType=='techuser'  || $userType=='techmanager')
			{
				$condition.=" AND tec_review!='auto_skipped' AND tec_review!='skipped'";
			}
			else if($userType=='produser' || $userType=='prodsubmanager' || $userType=='multilingue' || $userType=='prodmanager')
			{
				$condition.=" AND prod_review!='auto_skipped' AND prod_review!='skipped'";
			}
			
		}
		
		if($searchParams['client_id'])
		{
			$condition.=" AND q.client_id='".$searchParams['client_id']."'";
		}
		if($searchParams['new_quote_system']=='yes')
		{
			$condition.=" AND is_new_quote=1 OR q.migrated_quote='yes'";
		}
		else{
			$condition.=" AND is_new_quote=0";
		}
		

        //TEST CLIETN CONDITION
        if($this->adminLogin->type!='superadmin')
        {
            $conf_obj=new Ep_Delivery_Configuration();
            $test_client_id=$conf_obj->getConfiguration('test_client_id');          
            $testCondition=" AND q.client_id!='".$test_client_id."'";
        }


       $query="SELECT q.*,c.company_name,IF(q.final_turnover,q.final_turnover,q.sales_suggested_price) as turnover,
        (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action='sales_closed' AND ql.quote_id = q.identifier LIMIT 1) AS releaceraction,
        (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action='sales_validated_ontime' AND ql.quote_id = q.identifier order by ql.action_at desc LIMIT 1) AS quotesvalidated
          FROM ".$this->_name." q
    			INNER JOIN Client c ON c.user_id=q.client_id
                WHERE 1=1 $condition  $testCondition 
                ORDER BY q.created_at DESC,q.signed_at DESC
    			";
    	
    	//echo $query;exit;

    	if(($quoteList=$this->getQuery($query,true))!=NULL)
        {      
            return $quoteList;           
        }		
        else
        	return NULL;

    }

    /*Ajax newly added for ajax thing*/
    function getAllQuotesListAjax($searchParams=NULL)
    {

        $userType=$this->adminLogin->type;
        $userId=$this->adminLogin->userId;
        $orderby='';
       $search=addslashes($searchParams['search']);
            $page=$searchParams['page'];
            $end=$searchParams['limit'];
            $page=$page-1;
            if($page>0)
            {
                $page=($page*$end); 
            }
            


               
            if($userType=='seouser' || $userType=='seomanager' )
            {
                //$condition.=" AND ((seo_review!='auto_skipped' AND seo_review!='skipped') OR (sales_review='briefing')) AND find_in_set('".$userId."',brief_email_notify)";
                $condition.=" AND find_in_set('".$userId."',brief_email_notify)";
            }
            else if($userType=='techuser'  || $userType=='techmanager')
            {
                //$condition.=" AND ((tec_review!='auto_skipped' AND tec_review!='skipped') OR (sales_review='briefing')) AND find_in_set('".$userId."',brief_email_notify)";
                $condition.=" AND find_in_set('".$userId."',brief_email_notify)";
            }
            else if($userType=='produser' || $userType=='prodsubmanager' || $userType=='multilingue' || $userType=='prodmanager' )
            {
                //$condition.=" AND ((prod_review!='auto_skipped' AND prod_review!='skipped') OR (sales_review='briefing'))  AND find_in_set('".$userId."',brief_email_notify )";
                $condition.=" AND find_in_set('".$userId."',brief_email_notify )";
            }
            else if($userType=='salesuser')
            {
                $condition.=" AND (quote_by='".$userId."' OR find_in_set('".$userId."',brief_email_notify))";
            }
         
        
        if($searchParams['client_id'])
        {
            $condition.=" AND q.client_id='".$searchParams['client_id']."'";
        }

        if($searchParams['new_quote_system']=='yes')
        {
            $condition.=" AND (is_new_quote=1 OR migrated_quote='yes')";
        }
                
       

        //TEST CLIETN CONDITION
        if($this->adminLogin->type!='superadmin')
        {
            $conf_obj=new Ep_Delivery_Configuration();
            $test_client_id=$conf_obj->getConfiguration('test_client_id');          
            $testCondition=" AND q.client_id!='".$test_client_id."'";
        }
        $relancequuery="";
        if($searchParams['sales_review']=='validated')
        {
            $condition.=" AND sales_review='".$searchParams['sales_review']."' AND sign_expire_timeline < CURRENT_TIMESTAMP";
            $limit=" LIMIT $page,$end";
        }
        elseif($searchParams['sales_review']=='deleted' || $searchParams['sales_review']=='briefing' || $searchParams['sales_review']=='signed')
        {
         $condition.=" AND sales_review='".$searchParams['sales_review']."' ";
         $limit=" LIMIT $page,$end";
        }
        elseif($searchParams['sales_review']=='closed')
        {
        
         $condition.=" AND (sales_review='".$searchParams['sales_review']."' AND boot_customer IS NULL AND  CURDATE() <= (SELECT DATE_ADD(ql.action_at,INTERVAL 1 MONTH) FROM QuotesLog ql WHERE ql.action='sales_closed' AND ql.quote_id = q.identifier LIMIT 1) OR closed_reason='quote_permanently_lost' )";
         $relancequuery=", (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action='sales_closed' AND ql.quote_id = q.identifier LIMIT 1) AS releaceraction,(SELECT ql.action_at FROM QuotesLog ql WHERE ql.action='sales_validated_ontime' AND ql.quote_id = q.identifier order by ql.action_at desc LIMIT 1) AS quotesvalidated";
         $limit=" LIMIT $page,$end";
           
        }
        elseif($searchParams['sales_review']=='relance')
        {
         $condition.=" AND (sales_review='closed' AND  (boot_customer IS NOT NULL OR CURDATE()> (SELECT DATE_ADD(ql.action_at,INTERVAL 1 MONTH) FROM QuotesLog ql WHERE ql.action='sales_closed' AND ql.quote_id = q.identifier LIMIT 1)) OR (sales_review='validated' && CURRENT_TIMESTAMP() > sign_expire_timeline ) )";
         $relancequuery=", (SELECT ql.action_at FROM QuotesLog ql WHERE ql.action='sales_closed' AND ql.quote_id = q.identifier LIMIT 1) AS releaceraction,(SELECT ql.action_at FROM QuotesLog ql WHERE ql.action='sales_validated_ontime' AND ql.quote_id = q.identifier order by ql.action_at desc LIMIT 1) AS quotesvalidated";
         $limit=" LIMIT $page,$end";
           if($searchParams['quote_id']!='')
                 $orderby.=' ,q.identifier='.$searchParams['quote_id'];
        }
        elseif($searchParams['sales_review']=='ongoing')
        {
         $condition.=" AND (sales_review= 'not_done' OR sales_review = 'challenged' OR sales_review = 'to_be_approve') ";
         $limit=" LIMIT $page,$end";
        }

        if($searchParams['search']!="")
        {
        $condition.=" AND (title LIKE '%".$search."%' OR  c.company_name LIKE '%".$search."%' )";    
        }
        

       $query="SELECT q.*,c.company_name,IF(q.final_turnover,q.final_turnover,q.sales_suggested_price) as turnover
        $relancequuery
         FROM ".$this->_name." q
                INNER JOIN Client c ON c.user_id=q.client_id
                WHERE 1=1 $condition  $testCondition 
                ORDER BY q.updated_at DESC,q.created_at DESC $orderby $limit";

        //echo $query;exit;

        if(($quoteList=$this->getQuery($query,true))!=NULL)
        {     
        //echo '<pre>'; print_r($quoteList); exit;
            return $quoteList;           
        }       
        else
            return NULL;

    }

    function getQuoteDetails($quote_id)
    {
    	$query="SELECT up.*,up.phone_number as client_phone,q.*,c.company_name,c.ca_number,IF(q.final_turnover,q.final_turnover,q.sales_suggested_price) as turnover,c.siret,c.siret_applicable,c.client_code
                FROM ".$this->_name." q
    			JOIN Client c ON c.user_id=q.client_id
                LEFT JOIN UserPlus up ON up.user_id=c.user_id
    			WHERE identifier='".$quote_id."' LIMIT 1
    			";
    	
    	//echo $query;exit;

    	if(($quoteDetails=$this->getQuery($query,true))!=NULL)
        {      
            return $quoteDetails;           
        }		
        else
        	return NULL;

    }
    //insert quote details into version table
    public function insertQuoteVersion($quote_id)
    {
        $insertVersionQuery="INSERT INTO QuotesVersions
                                SELECT NULL,q.*
                                FROM Quotes q
                             WHERE q.identifier ='".$quote_id."'";
        //echo $insertVersionQuery;exit;                     

        $this->execQuery($insertVersionQuery,'insert');
    }

    //get quote version
    public function getQuoteVersion($quote_id)
    {
        $versionQuery="SELECT version FROM $this->_name
                        WHERE identifier='".$quote_id."'";
                        
        if(($quoteDetails=$this->getQuery($versionQuery,true))!=NULL)
        {      
            return $quoteDetails[0]['version'];           
        }       
        else
            return 1;                
    }

     //get previous version /all Quote version details
    public function getQuoteVersionDetails($quote_id,$version=NULL)
    {
        if($version)
            $condition.=" AND q.version='".$version."'";    

        
        if($quote_id)
            $condition.=" AND q.identifier='".$quote_id."'";      
        
        $quoteQuery="SELECT q.*
                            FROM QuotesVersions q                         
                            WHERE 1=1 $condition    
                            ORDER BY q.created_at ASC
                            ";

        //echo $quoteQuery."<br>";exit;     
        if(($count=$this->getNbRows($quoteQuery))>0)
        {           
           $quoteDetails=$this->getQuery($quoteQuery,true);             
            return $quoteDetails;
        }
        else
            return NULL;

    }

    /* To get contract count */
        function checkcontractexist($quoteid)
        {
            $query = "SELECT qc.contractname as contractname,qc.quotecontractid FROM `QuoteContracts` qc WHERE  qc.quoteid ='".$quoteid."'";
            if(($result = $this->getQuery($query,true))!=NULL)
                return $result;
            else
                return NULL;
        }


    /* To get tech title count */
    function techtitles()
    {
        $search="";
        
        $query = "SELECT * FROM `TechMissionTypes` ";
    
            if(($result = $this->getQuery($query,true))!=NULL)
                return $result;
            else
                return NULL;

    }


     /* To get tech details  */
        function techtitleDetails($tid)
        {
            $query = "SELECT * FROM `TechMissionTypes` where tid='".$tid."'";
            if(($result = $this->getQuery($query,true))!=NULL)
                return $result;
            else
                return NULL;
        }

        /*get all manager*/
        function getManagersList()
        {
            $query = " SELECT u.identifier,u.email,up.* FROM `User` as u  
            LEFT JOIN UserPlus up ON up.user_id=u.identifier
            WHERE u.status='Active' AND u.type 
            IN ('techmanager', 'seomanager', 'prodsubmanager', 'salesmanager', 'prodmanager','salesuser','superadmin')";

            if(($result = $this->getQuery($query,true))!=NULL)
                return $result;
            else
                return NULL;

        }

}
