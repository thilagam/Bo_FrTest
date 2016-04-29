<?

/**
 * Ep_Admin_DocumentKeyword
 *  
 * @author Ratnadeep
 * @package Admin
 * @version 1.0
 */

class Ep_Document_DocumentDesc extends Ep_Db_Identifier  
{
	/**
	 * The default table name 
	 */
	protected $_name = 'DOCUMENTDESC';
	
	//Private properties
	var $description;
	
	public function __construct($name=NULL)
	{
		if($name == NULL)
			$this->_setupTableName($this->_name);
		else
			$this->_setupTableName($name);
	}
	public function setDescription($desc)
	{
		$this->description = $desc;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	protected function loadData($data)
	{
		$this->setIdentifier($data["identifier"]);
		$this->setDescription($data["description"]);
	}

	/**
	 * loadIntoArray
	 * Loads the array $array with respective records fetched from the database
	 *
	 * @return array $array
	 */
	public function loadIntoArray()
	{
		$data = array();
		$data["identifier"] = $this->getIdentifier();
		$data["description"] = $this->getDescription();
		return $data;
	}
	public function selectByKeyword($keyword=null, $identifier=null)
	{
		$keyword=mysql_escape_string($keyword);
		$query="SELECT identifier FROM ".$this->_name." WHERE description LIKE '%".$keyword."%' AND identifier != ".$identifier;
		$records=$this->getQuery($query,false);
		if(!empty($records))
			return $records;
	}
	
}