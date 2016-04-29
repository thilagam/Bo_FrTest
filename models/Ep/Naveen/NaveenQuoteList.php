<?php

class Ep_Naveen_NaveenQuoteList extends Ep_Db_Identifier
{
	protected $_name = 'Quotes';
	

     ///////Users List//////////////
   public function getQuotesList(){
   
    $query = "SELECT q.*,c.company_name FROM ".$this->_name." as q left join Client as c on q.client_id=c.user_id  order by created_at  desc";
		if(($result = $this->getQuery($query, true)) != NULL)
        {		
           return $result;
        }
		else{
		return "No Records Found";
		}
           
   
   }
   
   
   
 }

