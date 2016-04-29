<?php
/**
 * Ep_Article_DeliveryOptions
 * @author Admin
 * @package DeliveryOptions
 * @version 1.0
  */
class Ep_Article_DeliveryOptions extends Ep_Db_Identifier
{
	protected $_name = 'DeliveryOptions';
	private $delivery_id;
	private $option_id;

	public function loadData($array)
	{
		$this->setDelivery_id($array["delivery_id"]) ;
		$this->setOption_id($array["option_id"]) ;
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["delivery_id"] = $this->getDelivery_id();
		$array["option_id"] = $this->getOption_id();
		return $array;
	}
	////////////////////////////////////////////////////////////Set methods ////////////////////////////////////////////////////////////////////////////////	
	public function  setDelivery_id($x = NULL) { $this->delivery_id = $x; } 
	public function  setOption_id($x = NULL) { $this->option_id = $x; }	
	////////////////////////////////////////////////////////////Get methods //////////////////////////////////////////////////////////////////////////////	
	public function  getDelivery_id() { return $this->delivery_id; } 
	public function  getOption_id() { return $this->option_id; }
}

