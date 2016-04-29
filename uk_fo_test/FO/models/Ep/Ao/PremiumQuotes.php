<?php
class Ep_Ao_PremiumQuotes extends Ep_Db_Identifier
{
	protected $_name = 'PremiumQuotes';
	
	public function InsertPremium($user,$funnel)
	{
		$darray = array(); 		
		$darray["id"] = $this->getIdentifier(); 
		$darray["user_id"] = $user; 
		$darray['title']=$funnel['title']; 	
		$darray['con_type']=$funnel['con_type']; 
		$darray['type']=implode("|",$funnel['quotetype']); 	
		$darray['other_type']=$funnel['textforother'];	
		if($funnel['con_type']=="writing")
			$darray['from_language']=$funnel['writing_lang'];
		else
			$darray['from_language']=$funnel['translation_from'];
		$darray['to_language']=$funnel['translation_to'];
	
		$darray["objective"]=$funnel['objectives']; 
		//$darray["other_objective"]=$funnel['objOtherText']; 
		$darray["remindtime"]=$funnel['remindtime']; 
		if($funnel['dontknowcheck']==1)
			$darray["dontknowcheck"]="yes"; 
		else
		{
			$darray['total_article']=implode("|",$funnel['articlenum']); 
			$darray['frequency']=implode("|",$funnel['frequency']); 
		}
		$darray['aotype']=$funnel['aotype'];
		 if($this->insertQuery($darray))
			return $this->getIdentifier(); 
		 else
			return "NO";
	}  

	public function getPremiumQuotes($quote)
	{
		$query1="SELECT 
						p.*,up.first_name,up.last_name,up.phone_number,u.email,c.company_name,c.website,c.category,c.job
				FROM 
					PremiumQuotes p INNER JOIN User u ON p.user_id=u.identifier
					LEFT JOIN UserPlus up ON u.identifier=up.user_id
					LEFT JOIN Client c ON u.identifier=c.user_id	
				WHERE 
					p.id='".$quote."' ";
       
		if(($result1 = $this->getQuery($query1,true)) != NULL)
            return $result1;
	}	
}
