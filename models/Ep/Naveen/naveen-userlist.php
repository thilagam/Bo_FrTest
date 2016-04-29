<?php

class Ep_Quote_Naveen_Userlist extends Ep_Db_Identifier
{
	protected $_name = 'User';
	private $parameters = array();

	public function loadData($array)
	{
		$this->parameters=$array ;
        return $this;
	}

     ///////Users List//////////////
   publix function getUserList(){
   
    $query = "SELECT pageId FROM ".$this->_name." order by created_at desc";
		if(($result = $this->getQuery($query,false)) != NULL)
        {
           return $result;
        }
   
   }


}

