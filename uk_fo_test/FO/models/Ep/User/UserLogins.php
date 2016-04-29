<?php

class Ep_User_UserLogins extends Ep_Db_Identifier
{
	protected $_name = 'UserLogins';
	
	public function InsertLogin($data)
	{
		$data['id']=$this->getIdentifier();
		$this->insertQuery($data);
	}
}