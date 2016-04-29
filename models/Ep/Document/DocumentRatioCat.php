<?php
/**
 * @author RAM
 * @package Document
 * @version 1.0
 */
class Ep_Document_DocumentRatioCat extends Ep_Db_Identifier
{
	protected $_name = 'DOCUMENTRATIOCAT';
	private $ratio;	       //current average rating value
	private $category;	       //current average rating value
	private $nbpage;

	public function __construct()
	{
		parent::__construct();
	}
	
	public function loadData($array)
	{	
		$this->setCategory($array["category"]);
		$this->setNbPage($array["nbpage"]);
		$this->setRatio($array["ratio"]);
		return $this;
	}
	//return an array from the instancied Class
	public function loadIntoArray()
	{
		$array = array();
		$array["nbpage"] = $this->getNbPage();
		$array["category"] = $this->getCategory();
		$array["ratio"] = $this->getRatio();
		return $array;
	}
	//set methods
	public function selectRatio($category,$nbpage)
	{
		if ($nbpage > 10 )$nbpage=10;
		$query = "SELECT * FROM ". $this->_name." WHERE category = '$category' AND nbpage = $nbpage";
		return $this->loadByQuery($query);
	}
	
	public function setRatio($ratio)
	{
		$this->ratio = $ratio;
	}
	public function setCategory($category)
	{
		$this->category = $category;
	}
	public function setNbPage($nbpage)
	{
		$this->nbpage = $nbpage;
	}
	
	//get methods
	public function getRatio()
	{
		return $this->ratio;
	}
	public function getNbPage()
	{
		return $this->nbpage;
	}
	public function getCategory()
	{
		return $this->category;
	}
}
?>