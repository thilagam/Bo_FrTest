<?php
/**
 * Ep_Quote_DeliveryRepeat
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_DeliveryRepeat extends Ep_Db_Identifier
{
	protected $_name = 'DeliveryRepeat';
	
	public function insertRepeatDelivery($repeat_array)
 	{
 		$repeat_array["repeat_id"] = $this->getIdentifier();
		
		$this->insertQuery($repeat_array);
		return $repeat_array["repeat_id"];
	}
	public function updateRepeatDelivery($data,$repeat_id)
    {        
		$where=" repeat_id='".$repeat_id."'";
        $this->updateQuery($data,$where);
    }
    public function RepeatDeliveryDetails($delivery_id)
    {        
		$query=" SELECT * From DeliveryRepeat Where delivery_id ='".$delivery_id."' LIMIT 1";
				
		//echo $query;exit;
		
		if(($count=$this->getNbRows($query))>0)
        {
            $repeatDetails=$this->getQuery($query,true);      

            return $repeatDetails;
        }
        else
            return NULL;
    }
}	