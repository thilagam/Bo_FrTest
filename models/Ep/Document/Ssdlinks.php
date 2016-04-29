<?php

/**
 * Ep_Document_Ssdlinks
 * 
 * this class will be used to access ssdlinks table 
 * 
 * @author Milan
 * @package Document
 * @version 1.0
 */

class Ep_Document_Ssdlinks extends Ep_Db_Identifier {
	/**
	 * The default table name 
	 */
	protected $_name = 'SSDLINKS';
	
	/**
	 * Column Names
	 * 
	 * @var string $keywords keywords based on documents
	 * @var string $month months of year
	 * @var integer $year year
	 * @var string $category various categories
	 * @var integer $status status of each row. status = 0 if result returned is 0, status = 1 if result returned = 1, statue = 2 if result returned >= 2
	 */
	
	//column names
	private $keywords;
	private $year;
	private $week;
	private $status;
	private $url;
	
	/**
	 * __construct
	 * This method will initialize the tablename if provided. If none is provided that it is
	 * been set to default
	 * @param String $name Name of table
	 */
	public function __construct($tablename = NULL) {
		parent::__construct ();
		if ($tablename){
			$this->_setupTableName ( $tablename );
		}else{
			$this->_setupTableName('SSDLINKS');
		}
	}
	
	/**
	 * loadData
	 * 
	 * Sets the array elements fetched from the database
	 *
	 * @param array $array
	 */
	protected function loadData($array) {
		$this->setIdentifier ( $array ["identifier"] );
		$this->setWeek($array["week"]);
		$this->setKeywords($array["keywords"]);
		$this->setYear($array["year"]);
		$this->setStatus($array["status"]);
		$this->setUrl($array['url']);
	}
	
	/**
	 * loadIntoArray
	 * 
	 * Loads the array $array with respective records fetched from the database
	 *
	 * @return array
	 */
	protected function loadIntoArray() 
	{
		$array = array ( );
		$array ["identifier"] = $this->getIdentifier ();
		$array["keywords"] = $this->getKeywords();
		$array["week"] = $this->getWeek();
		$array["year"] = $this->getYear();
		$array["status"] = $this->getStatus();
		$array['url'] = $this->getUrl();
		
		return $array;
	}

	/**
	 * createIdentifier
	 * this method is used to create an identifier value for the SSDLINKS
	 */
	public function createIdentifier()
    {
		$s = new String();
		$d = new date();
		$this->setIdentifier($s->randomString(15));
  	}
	public function setUrl($url){
		$this->url = $url;
	}
	
	public function setKeywords($a)
	{
		$this->keywords = $a;
	}
	
	public function setYear($a)
	{
		$this->year = $a;
	}
	
	public function setStatus($a)
	{
		$this->status = $a;
	}
	
	public function setWeek($a)
	{
		$this->week = $a;
	}
	
	public function getWeek()
	{
		return $this->week;
	}
	
	public function getYear()
	{
		return $this->year;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function getKeywords()
	{
		return $this->keywords;
	}

	public function getUrl(){
		return $this->url;
	}
	
	/**
	 * Insert
	 * This method will insert a row
	 */
	public function insert()
	{
		$data = $this->loadIntoArray();
		
		if(parent::insertQuery($data))				//return parent::insertQuery($data);
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Update
	 * This method will update a row
	 */
	public function update()
	{
		$data = $this->loadIntoArray();
		$query = "identifier = '".$this->getIdentifier()."'";
		return parent::updateQuery($data,$query);
	}
	
	public function selectKeyword($yr,$mn)
	{
		$query = "select keywords,category from SSDLINKS where year='".$yr."' and month='".$mn."' and status!=0";
		$res =  parent::getQuery($query,TRUE);	
		return $res;
	}
	public function selectByUrl($url){
		$query = "SELECT * FROM SSDLINKS WHERE url='".$url."'";
		$res = parent::getQuery($query, TRUE);
		return $res;
	}

	public function selectByOffset($offset, $limit){
		$query = "SELECT * FROM SSDLINKS LIMIT {$offset}, $limit";
		return parent::getQuery($query, TRUE);
	}
	
	public function loadByUrl($url){
		$query = "SELECT * FROM SSDLINKS WHERE url='".$url."'";
		$res = parent::getQuery($query, TRUE);
		$this->loadData($res[0]);
	}

	public function updateSsdlinks($upddata, $where)
	{
		return $this->updateQuery($upddata,$where);
	}
	
	public function getSsdlinks($year, $week, $start, $limit){
		$query = "SELECT keywords, url FROM SSDLINKS WHERE year=$year AND week=$week AND status = 2 LIMIT $start,$limit;";
		//var_dump($query);
		//echo $query;
		return parent::getQuery($query, true);
	}

	public function getCount($year, $week){
		$query = "SELECT COUNT(url) cnt FROM SSDLINKS WHERE status = 2 AND year=$year AND week=$week;";
		//echo $query;
		$res = parent::getQuery($query, true);
		return $res[0]['cnt'];
	}

	public function getYearRange(){
		$query = "SELECT MIN(year) AS min_year, MAX(year) AS max_year FROM SSDLINKS WHERE status=2";
		$res = parent::getQuery($query, true);
		$result = array('min_year'=>$res[0]['min_year'], 'max_year'=>$res[0]['max_year']);
		return $result;
	}
	
}
