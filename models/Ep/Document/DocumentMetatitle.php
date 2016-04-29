<?

/**
 * Ep_Document_DocumentMetatitle
 *  
 * @author Shiva
 * @package Admin
 * @version 1.0
 */

class Ep_Document_DocumentMetatitle extends Ep_Db_Identifier  
{
	/**
	 * The default table name 
	 */
	protected $_name = 'METATITLE';
	
	//Private properties
	private $metatitle;
	private $correctorId;
  	private $date;
  	private $status;
	
	public function __construct($name=NULL)
	{
		if($name == NULL)
			$this->_setupTableName($this->_name);
		else
			$this->_setupTableName($name);
	}
	
	protected function loadData($array)
	{
		$this->setIdentifier($array["identifier"]);
		$this->setMetatitle($array["metatitle"]);
		$this->setCorrectorId($array["correctorId"]);
    	$this->setStatus($array["status"]);
    	$this->setDate($array["date"]);
    	return $this;
	}

	/**
	 * loadIntoArray
	 * Loads the array $array with respective records fetched from the database
	 *
	 * @return array $array
	 */
	public function loadIntoArray()
	{
		$array = array();
		$array["identifier"] = $this->getIdentifier();
		$array["metatitle"] = $this->getMetatitle();
		$array["correctorId"] = $this->getCorrectorId();
    	$array["status"] = $this->getStatus();
    	$array["date"] = $this->getDate();
		return $array;
	}

	public function setMetatitle($metatitle)
	{
		$this->metatitle = $metatitle;
	}
	public function setCorrectorId($correctorId)
  	{
    	$this->correctorId = $correctorId;
  	}
  	public function setDate($date)
  	{
    	$this->date = $date;
  	}
  	public function setStatus($status)
  	{
    	$this->status = $status;
  	}
	
	public function getMetatitle()
	{
		return $this->metatitle;
	}
  	public function getCorrectorId()
  	{
    	return $this->correctorId;
  	}
  	public function getDate()
  	{
    	return $this->date;
  	}
  	public function getStatus()
  	{
    	return $this->status;
  	}
	
  	//for ajax
	/*public function getDDMetatitle($metatitle=null)
	{
		//$metatitle=mysql_escape_string($metatitle);
		$query="SELECT * FROM ".$this->_name." WHERE metatitle = '".$metatitle."'";
		$records=$this->getQuery($query,true);
		if(!empty($records))
			return $records;
		else
			return false;
	}*/
	
	public function getDDMetatitle($title=null)
	{
		//$metatitle=mysql_escape_string($metatitle);
		$title = new String(stripslashes($title));
		$title->_fuckWord();
		$title->_fuckTheBreaks();
		$title->_replaceAB();
		$title->replace('"',"");
		$title = $title->getArrayKeyword(2);
		$count = count($title);
		$i = 0;
		foreach($title as $val){
			$where.= " metatitle LIKE '%".addslashes($val)."%'";
			if($i < $count-1)
			$where.= " AND";
			$i++;
		}
		
		$query="SELECT * FROM ".$this->_name." WHERE $where ";
		$records=$this->getQuery($query,true);
		if(!empty($records))
			return $records;
		else
			return false;
	}	
	
	//while displaying meta in FO
	public function getDocMetatitle($id=null)
	{
		$metatitle=mysql_escape_string($id);
		$query="SELECT * FROM ".$this->_name." WHERE identifier = $id AND status = 1";
		$records=$this->getQuery($query,true);
		if(!empty($records))
		return $records;
		else
		return false;
	}
	
	//while displaying in page
	public function getOnMetatitle()
	{
		$query="SELECT * FROM ".$this->_name." WHERE 1 = 1";
		$records=$this->getQuery($query,true);
		if(!empty($records))
			return $records;
			else
			return false;
	}
	
	public function find($id)
	{
		$query="SELECT * FROM ".$this->_name." WHERE identifier = $id";
		return $this->exist($query);
	}
	
}