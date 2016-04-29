<?php
/**
 * Ep_Article_Options
 * @author Admin
 * @package Options
 * @version 1.0
 */
class Ep_Delivery_Options extends Ep_Db_Identifier
{
	protected $_name = 'Options';
	private $id;
	private $option_name;
	private $option_price;
    private $option_price_bo;
    private $belongs;
    private $status;
    private $parent;
    private $type;
	private $description;

	public function loadData($array)
	{
		$this->Id($array["id"]) ;
		$this->option_name($array["option_name"]) ;
		$this->option_price($array["option_price"]) ;
        $this->option_price_bo($array["option_price_bo"]) ;
        $this->belongs($array["belongs"]) ;
         $this->status($array["status"]) ;
         $this->parent($array["parent"]) ;
         $this->type($array["type"]) ;
		$this->description($array["description"]) ;
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		//$array["id"] = $this->getId();
		$array["option_name"] = $this->option_name;
		$array["option_price"] = $this->option_price;
        $array["option_price_bo"] = $this->option_price_bo;
        $array["belongs"] = $this->belongs;
        $array["status"] = $this->status;
        $array["parent"] = $this->parent;
        $array["type"] = $this->type;
		$array["description"] = $this->description;
		return $array;
	}
    public function __set($name, $value) {
                $this->$name = $value;
        }

        public function __get($name){
                return $this->$name;
        }
	//////////////////////////////////////////////////////
	public function getParentOptionsAO()
	{
		$query = "SELECT * FROM ".$this->_name." WHERE status='active' AND parent=0 AND belongs IN ('bo','both')";
		if(($result = $this->getQuery($query,true)) != NULL){
		  for($i=0;$i<count($result);$i++)
		  {
			$result[$i]['value']=$result[$i]['id'].'_'.$result[$i]['option_price_bo'];
		  }
			return $result;
		 }else
			return false;
	}

	public function getChildOptionsAO($part)
	{
		$query = "SELECT * FROM ".$this->_name." WHERE status='active' AND parent=".$part." AND belongs IN ('bo','both')";
		if(($result = $this->getQuery($query,true)) != NULL){
		  for($i=0;$i<count($result);$i++)
		  {
			$result[$i]['value']=$result[$i]['id'].'_'.$result[$i]['option_price_bo'];
		  }
			return $result;
		 }else
			return false;
	}
	
	public function getPremService($did)
	{
		$query = "SELECT * FROM DeliveryOptions WHERE delivery_id=".$did;
		
		if(($result = $this->getQuery($query,true)) != NULL){
		  $premarray=array();
		  for($i=0;$i<count($result);$i++)
		  {
			$premarray[$i]=$result[$i]['option_id'];
		  }
		
			return $premarray;
		 }
	}

	/////////////////////////////////////////////////////
	public function getOptions()
	{
		$query = "SELECT * FROM ".$this->_name." WHERE belongs IN ('bo','both') AND status='active'";
		if(($result = $this->getQuery($query,true)) != NULL){
		  for($i=0;$i<count($result);$i++)
		  {
			$result[$i]['value']=$result[$i]['id'].'_'.$result[$i]['option_price_bo'];
		  }
			return $result;
		}else
			return false;
	}
    /////////////////////////////////////////////////////
	public function getOptionsGrid()
	{
		$query = "SELECT * FROM ".$this->_name." WHERE status IN ('active','inactive')";
		if(($result = $this->getQuery($query,true)) != NULL){
		  for($i=0;$i<count($result);$i++)
		  {
			$result[$i]['value']=$result[$i]['id'].'_'.$result[$i]['option_price_bo'];
		  }
			return $result;
		}else
			return false;
	}
    ///////////get option details on id//////////////////////////////////////////
	public function getOptionDetailsOnId($optionId)
	{
	    $query = "SELECT * FROM ".$this->_name." WHERE id=".$optionId;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
     ///////////get option details on id//////////////////////////////////////////
	public function getParentOfThisOption($optionId)
	{
	    $query = "SELECT * FROM ".$this->_name." WHERE id=".$optionId;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    ///////////get option details who are parents//////////////////////////////////////////
	public function getParentOptions()
	{
	    $query = "SELECT id,option_name FROM ".$this->_name." WHERE parent=0";
		if(($result = $this->getQuery($query,true)) != NULL)
        {
            $result1 = array();
            foreach($result as $key=>$value)
		    {
			    $result1[$value['id']]=$value['option_name'];
		    }
            return $result1;
        }
		else
        {
			return "NO";
        }
	}
///////////get option details who are children//////////////////////////////////////////
	public function getChildOptions()
	{
	    $query = "SELECT * FROM ".$this->_name." WHERE parent!=0";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
////////////udate the options table//////////////////////
    public function updateOptions($data,$query)
    {
      //echo $query;
      //print_r($data);exit;
      echo  $this->updateQuery($data,$query);
    }
}

