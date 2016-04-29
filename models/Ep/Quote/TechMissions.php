<?php
/**
 * Ep_Quote_TechMissions
 * @author Arun
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_TechMissions extends Ep_Db_Identifier
{
	protected $_name = 'TechMissions';
	public $missionIdentifier;

	//Insert Quote data
	public function insertTechMission($data)
	{
		$this->missionIdentifier=$data['identifier']=$this->identifierString();
		$this->insertQuery($data);	
	}
	public function updateTechMission($data,$identifier)
    {
        //print_r($data);exit;
		$where=" identifier='".$identifier."'";
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
	function getTechMissionDetails($searchParameters=NULL,$limit=NULL)
	{
		if($searchParameters['mission_id'])
			$condition.=" AND t.mission_id='".$searchParameters['quote_id']."'";		
		if($searchParameters['quote_id'])
			$condition.=" AND find_in_set(t.identifier, (select techmissions_assigned From Quotes where identifier='".$searchParameters['quote_id']."') )>0";
		if($searchParameters['identifier'])
			$condition.=" AND t.identifier='".$searchParameters['identifier']."'";	

		if($searchParameters['include_final'])
			$condition.=" AND t.include_final='".$searchParameters['include_final']."'";

		if($searchParameters['prod_linked'])
			$condition.=" AND t.prod_linked='all_prod'";
		if($searchParameters['prod_linked_id'])
			$condition.=" AND t.prod_linked='".$searchParameters['prod_linked_id']."'";
		if($searchParameters['before_prod'])
			$condition.=" AND t.before_prod='".$searchParameters['before_prod']."'";

		if($searchParameters['all_linked'])
			$condition.=" AND t.prod_linked!='".$searchParameters['all_linked']."' && t.prod_linked!=''";

		if($searchParameters['before_prod_all'])
			$condition.=" AND (t.before_prod!='".$searchParameters['before_prod_all']."' AND (t.prod_linked='all_prod' OR t.prod_linked='' OR t.prod_linked IS NULL) OR (t.before_prod='yes' OR t.before_prod='no') )  ";

		$missionQuery="SELECT t.*
							FROM TechMissions t							
							WHERE 1=1 $condition							
							ORDER BY t.identifier ASC
							";

		//echo $missionQuery;	
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;
	}

	function techTurnover($quote_id)
    {
    	$turnoverQuery="SELECT SUM(t.cost) as turnover,t.margin_percentage FROM TechMissions t
    					WHERE find_in_set(t.identifier, (select techmissions_assigned From Quotes where identifier='".$quote_id."') )>0
    					";	
    	//echo $turnoverQuery;exit;
    	if($techTurnover=$this->getQuery($turnoverQuery,true))
    	{
    		if($techTurnover[0]['turnover'])
            {
    			$tech_turnover=number_format(($techTurnover[0]['turnover']/(1-($techTurnover[0]['margin_percentage']/100))),2, '.', '');

                return $tech_turnover;
            }
    		else return 0;

    	} 	
    	else return 0;				
    }

    //delete Tecg mission
	public function deleteTechMission($identifier)
    {
       $where=" identifier='".$identifier."'";    
		if($identifier)	
        echo $this->deleteQuery($where);

    }

     //insert Tech mission details into Techmissionsversions table
    public function insertMissionVersion($mission_id)
    {
        $insertVersionQuery="INSERT INTO  TechMissionsVersions 
                                SELECT NULL,t.*
                                FROM TechMissions t
                             WHERE t.identifier ='".$mission_id."'";
        //echo $insertVersionQuery;exit;                     

        $this->execQuery($insertVersionQuery,'insert');
    }

    function getMissionVersionDetails($mission_id,$quote_id,$version=NULL)
	{
		$condition.=" AND t.identifier='".$mission_id."'";		

		if($quote_id)
		{
			if($version)
				$condition.=" AND find_in_set(t.identifier, (select techmissions_assigned From QuotesVersions where version= $version AND identifier='".$quote_id."' LIMIT 1))>0 ";
			else	
				$condition.=" AND find_in_set(t.identifier, (select techmissions_assigned From QuotesVersions where identifier='".$quote_id."' LIMIT 1))>0 ";
		}
		$missionQuery="SELECT t.*
							FROM TechMissionsVersions t							
							WHERE 1=1 $condition							
							ORDER BY t.identifier ASC
							";

		//echo $missionQuery;exit;		
		if(($count=$this->getNbRows($missionQuery))>0)
        {           
           $missionDetails=$this->getQuery($missionQuery,true);           	
       		return $missionDetails;
		}
		else
        	return NULL;
	}
	 function getQuoteVersionDetails($quote_id,$version)
	{
				

		
		$condition.=" AND t.version=$version AND find_in_set(t.identifier, (select techmissions_assigned From QuotesVersions where version= $version AND identifier='".$quote_id."' LIMIT 1))>0 ";
			
		
		$missionQuery="SELECT t.*
							FROM TechMissionsVersions t							
							WHERE 1=1 $condition							
							ORDER BY t.identifier ASC
							";

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