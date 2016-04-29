<?php

class Ep_User_UserPlus extends Ep_Db_Identifier
{
	protected $_name = 'UserPlus';
	
	public function updateUserplus($uarray,$id)
	{
		$chkQuery="SELECT user_id FROM ".$this->_name." WHERE user_id='".$id."'";	
		$resultclient = $this->getQuery($chkQuery,true);
		
		if(count($resultclient)>0)
		{
			$where=" user_id='".$id."'";
			$this->updateQuery($uarray,$where);
		}
		else
		{
			$uarray['user_id']=$id;
			$this->insertQuery($uarray);
		}
			
	}
	
	public function getUsername($id)
	{
		$nameQuery="SELECT first_name,last_name FROM ".$this->_name." WHERE user_id='".$id."'";	
		$nameclient = $this->getQuery($nameQuery,true);
		return ucfirst($nameclient[0]['first_name']).'&nbsp;'.ucfirst(substr($nameclient[0]['last_name'],0,1));
	}
	
	public function getUserPlus($id)
	{
		$nameQuery="SELECT up.first_name,up.last_name,u.email FROM User u LEFT JOIN UserPlus up  ON u.identifier=up.user_id  WHERE u.identifier='".$id."'";	
		$nameclient = $this->getQuery($nameQuery,true);
		return $nameclient;
	}
	
}