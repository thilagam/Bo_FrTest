<?php

class Ep_Ao_Payment extends Ep_Db_Identifier
{
	protected $_name = 'Payment';

	public function InsertPayment($delivery)
	{
		$payarray=array();
		$payarray['id']=$this->getIdentifier(); 
		$payarray['delivery_id']=$delivery; 
		$payarray['amount']="0"; 
		$payarray['status']="Paid"; 
		$this->insertQuery($payarray);
	}
	
}	