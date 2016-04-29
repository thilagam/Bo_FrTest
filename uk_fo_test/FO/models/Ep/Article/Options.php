<?php
/**
 * Ep_Article_Options
 * @author Admin
 * @package Options
 * @version 1.0
 */
class Ep_Article_Options extends Ep_Db_Identifier
{
	protected $_name = 'Options';
	private $id;
	private $option_name;
	private $option_price;
	private $description;

	public function loadData($array)
	{
		$this->setId($array["id"]) ;
		$this->setOption_name($array["option_name"]) ;				
		$this->setOption_price($array["option_price"]) ;				
		$this->setDescription($array["description"]) ;				
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["id"] = $this->getId();
		$array["option_name"] = $this->getOption_name();	
		$array["option_price"] = $this->getOption_price();
		$array["description"] = $this->getDescription();
		return $array;
	}
	////////////////////////////////////////////////////////////Set methods ////////////////////////////////////////////////////////////////////////////////	
	public function setId($id = NULL) { $this->id = $id; } 
	public function setOption_name($option_name = NULL) { $this->option_name = $option_name; } 
	public function setOption_price($option_price = NULL) { $this->option_price = $option_price; } 
	public function setDescription($description = NULL) { $this->description = $description; } 
	////////////////////////////////////////////////////////////Get methods //////////////////////////////////////////////////////////////////////////////	
	public function getId() { return $this->id; } 
	public function getOption_name() { return $this->option_name; } 
	public function getOption_price() { return $this->option_price; } 
	public function getDescription() { return $this->description; } 
	////////////////////////////////////////////////////////////Other methods //////////////////////////////////////////////////////////////////////////////	
	
	public function getParentOptions()
	{
		$query = "SELECT * FROM ".$this->_name." WHERE status='active' AND parent=0 AND belongs IN ('fo','both')";
		if(($result = $this->getQuery($query,true)) != NULL){
		  for($i=0;$i<count($result);$i++)
		  {
			$result[$i]['value']=$result[$i]['id'].'_'.$result[$i]['option_price'];
		  }
			return $result;
		 }else
			return false;
	}
	
	public function getChildOptions($part)
	{
		$query = "SELECT * FROM ".$this->_name." WHERE status='active' AND parent=".$part." AND belongs IN ('fo','both')";
		if(($result = $this->getQuery($query,true)) != NULL){
		  for($i=0;$i<count($result);$i++)
		  {
			$result[$i]['value']=$result[$i]['id'].'_'.$result[$i]['option_price'];
		  }
			return $result;
		 }else
			return false;
	}
	
	public function getAllInfo()
		{
			$query = "SELECT * FROM ".$this->_name." WHERE status='active'";
			if(($result = $this->getQuery($query,true)) != NULL){
			  for($i=0;$i<count($result);$i++)
			  {
				$result[$i]['value']=$result[$i]['id'].'_'.$result[$i]['option_price'];
			  }
				return $result;
			}else
				return false;
		}

}

