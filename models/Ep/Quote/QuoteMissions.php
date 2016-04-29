<?php
/**
 * Ep_Quote_QuoteMissions
 * @author Arun
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_QuoteMissions extends Ep_Db_Identifier
{
	protected $_name = 'QuoteMissions';
    public $missionIdentifier;

	//Insert Quote data
	public function insertQuoteMission($data)
	{
		$this->missionIdentifier=$data['identifier']=$this->identifierString();        
		$this->insertQuery($data);	
	}
	public function updateQuoteMission($data,$identifier)
    {
        //print_r($data);exit;
      //  if($data['volume'])
        	//$condition=' AND volume=0';
		$where=" identifier='".$identifier."'".$condition;
        $this->updateQuery($data,$where);
    }
    function identifierString()
    {
        usleep(5000);
        $time_stamp=microtime(true);
        $time_stamp=str_replace(".",'',$time_stamp);
        if(strlen($time_stamp)==11)
            $time_stamp=$time_stamp.mt_rand(1000,9999);
        elseif(strlen($time_stamp)==12)
            $time_stamp=$time_stamp.mt_rand(100,999);   
        elseif(strlen($time_stamp)==13)
            $time_stamp=$time_stamp.mt_rand(10,99);
        elseif(strlen($time_stamp)==14)
            $time_stamp=$time_stamp.mt_rand(1,9);   
        return $time_stamp;
    }
    function getIdentifier()
    {
        return $this->missionIdentifier;
    }

	function getMissionDetails($searchParameters=NULL,$limit=NULL)
	{
		if($searchParameters['misson_user_type'])
			$condition.=" AND m.misson_user_type='".$searchParameters['misson_user_type']."'";	

		if($searchParameters['related_to'])
			$condition.=" AND m.related_to='".$searchParameters['related_to']."'";	

		if($searchParameters['product'])
			$condition.=" AND m.product='".$searchParameters['product']."'";		

		if($searchParameters['quote_id'])
			$condition.=" AND m.quote_id='".$searchParameters['quote_id']."'";		
		if($searchParameters['mission_id'])
			$condition.=" AND m.identifier='".$searchParameters['mission_id']."'";

		if($searchParameters['include_final'])
			$condition.=" AND m.include_final='".$searchParameters['include_final']."'";

		if($searchParameters['product_type_seo'])
			$condition.= " AND m.product ".$searchParameters['product_type_seo']." ('seo_audit','smo_audit','content_strategy')";

		if($searchParameters['misson_user_type_prod_seo'])
			$condition.= " AND (m.misson_user_type='sales' OR m.misson_user_type='seo')";
			
		$missionQuery="SELECT m.*,c.company_name,q.quote_by,q.sales_suggested_currency,q.conversion,q.client_id,q.category as quotecat
							FROM QuoteMissions m  
							INNER JOIN Quotes q ON q.identifier=m.quote_id
							INNER JOIN Client c ON c.user_id=q.client_id
							WHERE 1=1 $condition							
							ORDER BY field(m.misson_user_type, 'sales', 'seo'), m.identifier ASC";//ORDER BY field(m.product, 'redaction', 'translation', 'proofreading','seo_audit','smo_audit'),m.identifier ASC";
		
		//echo $missionQuery;exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;
	}
	//delete quote mission
	public function deleteQuoteMission($identifier)
    {
       $where=" identifier='".$identifier."'";    
		if($identifier)	
        echo $this->deleteQuery($where);

    }	
    function getQuoteMission($identifier)	
    {		
    	$query="SELECT documents_path,documents_name,invoice_per,indicative_turnover,unit_price,volume from QuoteMissions WHERE  identifier='".$identifier."'"; 
    	 		return $this->getQuery($query,true);	
    }		
    //seo turnover w.r.t Quote
    function seoTurnover($quote_id)
    {
    	$turnoverQuery="SELECT SUM(m.cost) as turnover,m.margin_percentage FROM QuoteMissions m
    					WHERE m.quote_id='".$quote_id."' AND m.misson_user_type='seo'
    					";	
    	//echo $turnoverQuery;exit;
    	if($seoTurnover=$this->getQuery($turnoverQuery,true))
    	{
    		if($seoTurnover[0]['turnover'])
            {
    			$seo_turnover=number_format(($seoTurnover[0]['turnover']/(1-($seoTurnover[0]['margin_percentage']/100))),2, '.', '');

                return $seo_turnover;
            }
    		else 
    			return 0;
    	} 	
    	else 
    	return 0;
    }

    //insert mission details into Quotemissionsversions table
    public function insertMissionVersion($mission_id)
    {
        $insertVersionQuery="INSERT INTO QuoteMissionsVersions 
                                SELECT NULL,m.*
                                FROM QuoteMissions m
                             WHERE m.identifier ='".$mission_id."'";
        //echo $insertVersionQuery;exit;                     

        $this->execQuery($insertVersionQuery,'insert');
    }
    //get previous version /all mission version details
    public function getMissionVersionDetails($mission_id,$version=NULL,$type=NULL)
    {
    	if($version)
			$condition.=" AND m.version='".$version."'";	
        if($type)
            $condition.=" AND m.misson_user_type='".$type."'"; 


    	$missionQuery="SELECT m.*,c.company_name,q.quote_by,q.sales_suggested_currency
							FROM QuoteMissionsVersions m  
							INNER JOIN Quotes q ON q.identifier=m.quote_id
							LEFT JOIN Client c ON c.user_id=q.client_id
							WHERE m.identifier='".$mission_id."' $condition
                            GROUP BY m.identifier,m.version							
							ORDER BY m.identifier ASC";//ORDER BY field(m.product, 'redaction', 'translation', 'proofreading','seo_audit','smo_audit'),m.identifier ASC";
		
		//echo $missionQuery."<br>";exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;
    }
	
	//get previous versions based on quote id
    public function getQuoteMissionVersionDetails($quote_id,$version,$type=NULL,$prod=false)
    {
    	if($version)
			$condition.=" AND m.version='".$version."'";	
        if($type)
            $condition.=" AND m.misson_user_type='".$type."'"; 
		if($prod)
			$condition.=" AND (m.product='redaction' OR m.product='translation')";
			
    	$missionQuery="SELECT m.*,c.company_name,q.quote_by,q.sales_suggested_currency
							FROM QuoteMissionsVersions m  
							INNER JOIN Quotes q ON q.identifier=m.quote_id
							LEFT JOIN Client c ON c.user_id=q.client_id
							WHERE m.quote_id='".$quote_id."' $condition
                            GROUP BY m.identifier,m.version							
							ORDER BY m.identifier ASC";//ORDER BY field(m.product, 'redaction', 'translation', 'proofreading','seo_audit','smo_audit'),m.identifier ASC";
		
		//echo $missionQuery."<br>";exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;
    }
	
    //get previous version /all mission version details
    public function getDeletedMissionVersionDetails($quote_id,$version=NULL,$type=NULL)
    {
        if($version)
            $condition.=" AND m.version='".$version."'";    
        if($type)
            $condition.=" AND m.misson_user_type='".$type."'"; 


        $missionQuery="SELECT m.*,c.company_name,q.quote_by,q.sales_suggested_currency
                            FROM QuoteMissionsVersions m  
                            INNER JOIN Quotes q ON q.identifier=m.quote_id
                            LEFT JOIN Client c ON c.user_id=q.client_id
                            WHERE m.identifier not in (select identifier FROM QuoteMissions WHERE quote_id='".$quote_id."') AND m.quote_id='".$quote_id."' $condition                         
                            ORDER BY m.identifier ASC";//ORDER BY field(m.product, 'redaction', 'translation', 'proofreading','seo_audit','smo_audit'),m.identifier ASC";
        
        //echo $missionQuery."<br>";exit;     
        if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           
            return $missionDetails;
        }
        else
            return NULL;
    }

    /*QUote mission extract*/
    function quoteMissionExtract()
    {

        $extractQuery="SELECT q.identifier as quote_id,q.title,q.sales_review,c.company_name,q.quote_by,q.sales_suggested_currency,m.created_at,m.identifier as mission_id,
                        m.product,m.product_type,m.internal_cost,m.language_source,m.language_dest,m.volume,m.unit_price,m.package
                        FROM QuoteMissions m  
                        INNER JOIN Quotes q ON q.identifier=m.quote_id
                        INNER JOIN Client c ON c.user_id=q.client_id
                        WHERE q.sales_review in('validated','signed') AND m.include_final='yes'
                        ORDER BY q.created_at ASC,field(m.misson_user_type, 'sales', 'seo'), m.identifier ASC,q.identifier";//ORDER BY field(m.product, 'redaction', 'translation', 'proofreading','seo_audit','smo_audit'),m.identifier ASC";
        if(($count=$this->getNbRows($extractQuery))>0)
        {           
           $extractMissions=$this->getQuery($extractQuery,true);           
            return $extractMissions;
        }
        else
            return NULL;                        

    }
     //get previous version /all mission version details
    public function getExtractMissionVersionDetails($mission_id)
    {  
        $extractmissionQuery="SELECT m.*
                            FROM QuoteMissionsVersions m
                            WHERE m.identifier='".$mission_id."'
                            GROUP BY m.identifier,m.version                         
                            ORDER BY m.version ASC";//ORDER BY field(m.product, 'redaction', 'translation', 'proofreading','seo_audit','smo_audit'),m.identifier ASC";
        
        //echo $extractmissionQuery."<br>";exit;       
        if(($count=$this->getNbRows($extractmissionQuery))>0)
        {           
           $extractMissionVersionDetails=$this->getQuery($extractmissionQuery,true);             
            return $extractMissionVersionDetails;
        }
        else
            return NULL;
    }
	
	// To get Mission Details to display in Contract
	function getMissionDetailsContract($searchParameters=NULL,$limit=NULL)
	{
		if($searchParameters['misson_user_type'])
			$condition.=" AND m.misson_user_type='".$searchParameters['misson_user_type']."'";	

		if($searchParameters['related_to'])
			$condition.=" AND m.related_to='".$searchParameters['related_to']."'";	

		if($searchParameters['product'])
			$condition.=" AND m.product='".$searchParameters['product']."'";		

		if($searchParameters['quote_id'])
			$condition.=" AND m.quote_id='".$searchParameters['quote_id']."'";		
		if($searchParameters['mission_id'])
			$condition.=" AND m.identifier='".$searchParameters['mission_id']."'";

		if($searchParameters['include_final'])
			$condition.=" AND m.include_final='".$searchParameters['include_final']."'";

		if($searchParameters['product_type_seo'])
			$condition.= " AND m.product ".$searchParameters['product_type_seo']." ('seo_audit','smo_audit','content_strategy')";

		if($searchParameters['misson_user_type_prod_seo'])
			$condition.= " AND (m.misson_user_type='sales' OR m.misson_user_type='seo')";
			/*edited by naseer on 17-11-2015*/
		$missionQuery="SELECT m.*,c.company_name,q.quote_by,q.sales_suggested_currency,q.conversion,q.client_id,q.category as quotecat,cm.contractmissionid,cm.invoice_per as cminvoiceper,cm.indicative_turnover as cmindicative_turnover,cm.cm_turnover,cm.cm_status,cm.history_bo as cmhistory_bo,cm.history_fo as cmhistory_fo,
                            cm.freeze_start_date,cm.freeze_end_date,cm.assigned_to,cm.assigned_by,cm.assigned_at,cm.edited_by,cm.edited_at,
                            (SELECT CONCAT(first_name,' ',last_name) from UserPlus WHERE user_id = m.created_by) AS created_by_name,
                            (SELECT CONCAT(first_name,' ',last_name) from UserPlus WHERE user_id = cm.edited_by) AS edited_by_name
							FROM QuoteMissions m
							INNER JOIN Quotes q ON q.identifier=m.quote_id
							INNER JOIN Client c ON c.user_id=q.client_id
							LEFT JOIN ContractMissions cm ON cm.type_id = m.identifier AND cm.contract_id='".$searchParameters['contract_id']."'
							WHERE 1=1 $condition							
							ORDER BY field(m.misson_user_type, 'sales', 'seo'), m.identifier ASC";//ORDER BY field(m.product, 'redaction', 'translation', 'proofreading','seo_audit','smo_audit'),m.identifier ASC";
		
		//echo $missionQuery;exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;
	}
}