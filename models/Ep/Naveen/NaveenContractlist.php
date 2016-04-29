<?php

class Ep_Naveen_NaveenContractlist extends Ep_Db_Identifier
{
	protected $_name = 'Contracts';
	private $id;
	private $client_id;
	private $title;
	private $contract_date;
	private $lang;
	private $created_at;
	
	
	public function contractlist(){
	
		  $contractDetails="select c.client_id,c.id,c.title,c.contract_date,u.email,cl.company_name
					FROM ".$this->_name." c
					INNER JOIN User u ON c.client_id=u.identifier
					LEFT JOIN Client cl ON u.identifier=cl.user_id
					Group BY c.id
					ORDER BY c.contract_date DESC";
			//echo $contractDetails;exit;
			if(($contracts=$this->getQuery($contractDetails,true))!=NULL)
				return $contracts;
			else
				return "No Contract Found";
		
	}
	
	
	
}

?>