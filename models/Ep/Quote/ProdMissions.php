<?php
/**
 * Ep_Quote_ProdMissions
 * @author Arun
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_ProdMissions extends Ep_Db_Identifier
{
	protected $_name = 'ProdMissions';

	//Insert Quote data
	public function insertProdMission($data)
	{
		$data['identifier']=$this->identifierString();
		$this->insertQuery($data);	
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
	public function updateProdMission($data,$identifier)
    {
        //print_r($data);exit;
		$where=" identifier='".$identifier."'";
        $this->updateQuery($data,$where);
    }
	function getProdMissionDetails($searchParameters=NULL,$limit=NULL)
	{
		if($searchParameters['quote_mission_id'])
			$condition.=" AND p.quote_mission_id='".$searchParameters['quote_mission_id']."'";		
		
		$missionQuery="SELECT p.*
							FROM ProdMissions p							
							WHERE 1=1 $condition	
							ORDER BY field(p.product, 'redaction', 'translation', 'proofreading','autre')
							";

		//echo $missionQuery."<br>";//exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;
	}
	function getProdCostDetails($quote_mission_id)
	{
		$costQuery="SELECT p.*
							FROM ProdMissions p							
							WHERE p.quote_mission_id='".$quote_mission_id."'
							ORDER BY p.product ASC,p.identifier DESC
							";

		//echo $missionQuery."<br>";//exit;		
		if(($count=$this->getNbRows($costQuery))>0)
        {           
           $prodMissionDetails=$this->getQuery($costQuery,true);           	
       		return $prodMissionDetails;
		}
		else
        	return NULL;

	}

	function prodTurnover($quote_id)
    {
    	$turnoverQuery="SELECT SUM(p.cost*m.volume) as turnover FROM ProdMissions p
    					INNER JOIN QuoteMissions m ON p.quote_mission_id=m.identifier
    					WHERE m.quote_id='".$quote_id."'
    					";	
    	//echo $turnoverQuery;exit;
    	if($prodTurnover=$this->getQuery($turnoverQuery,true))
    	{    		
    		return $prodTurnover[0]['turnover'];
    	} 	
    	else return 0;				
    }

    //delete prod mission
	public function deleteProdMission($identifier)
    {
       $where=" identifier='".$identifier."'";    
		if($identifier)	
        echo $this->deleteQuery($where);

    }

     //insert Prod mission details into Prodmissionsversions table
    public function insertMissionVersion($mission_id)
    {
        $insertVersionQuery="INSERT INTO  ProdMissionsVersions 
                                SELECT NULL,p.*
                                FROM ProdMissions p
                             WHERE p.identifier ='".$mission_id."'";
        //echo $insertVersionQuery;exit;                     

        $this->execQuery($insertVersionQuery,'insert');
    }

    //get previous version /all mission version details
    public function getMissionVersionDetails($mission_id,$version=NULL)
    {
    	if($version)
			$condition.=" AND p.version='".$version."'";	

    	
		if($mission_id)
			$condition.=" AND p.identifier='".$mission_id."'";		
		
		$missionQuery="SELECT p.*
							FROM ProdMissionsVersions p							
							WHERE 1=1 $condition	
							ORDER BY field(p.product, 'redaction', 'translation', 'proofreading','autre')
							";

		//echo $missionQuery."<br>";//exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;

    }

    //get prod details based on Quote id
    public function getProdQuoteDetails($quote_id)
    {
    	$missionQuery="SELECT p.*
							FROM ProdMissions p
							INNER JOIN QuoteMissions qm ON p.quote_mission_id=qm.identifier
							INNER JOIN Quotes q ON q.identifier=qm.quote_id
							WHERE q.identifier='".$quote_id."'";						
							

		//echo $missionQuery."<br>";//exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;
    }
//get prod version details to show deleted missions
    function getProdVersionCostDetails($quote_mission_id,$version)
	{
		$costQuery="SELECT p.*
							FROM ProdMissionsVersions p							
							WHERE p.quote_mission_id='".$quote_mission_id."'
							AND p.version='".$version."'
							GROUP BY p.identifier,p.version
							ORDER BY field(p.product, 'redaction', 'translation', 'proofreading','autre'),p.identifier DESC
							";

		//echo $missionQuery."<br>";//exit;		
		if(($count=$this->getNbRows($costQuery))>0)
        {           
           $prodMissionDetails=$this->getQuery($costQuery,true);           	
       		return $prodMissionDetails;
		}
		else
        	return NULL;

	}

}