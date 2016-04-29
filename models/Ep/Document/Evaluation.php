<?php

/**
 * Ep_Document_Evaluation
 *  
 * @author Vamsee
 * @package Document
 * @version 1.0
 */

class Ep_Document_Evaluation extends Ep_Db_Identifier
{
	/**
	 * The default table name 
	 */
	protected $_name = 'EVALUATION';
	
	/**
	 * Column names
	 *
	 * @var text $comment Comment
	 * @var double $note current note (from 0 to 5)
	 * @var string $customerId The customer Id
	 * @var integer $documentId The document Id
	 * @var timpstamp $date Date
	 * @var tinyint $state
	 */
	private $nb;
	private $totalAverage;
	private $identifier;
	//abstract method
	

	/**
	 * loadData
	 * 
	 * Sets the array elements fetched from the database
	 *
	 * @param array $array
	 * @return $this
	 */
	protected function loadData($array)
	{
		$this->setNb($array["nb"]);
		$this->setTotalAverage($array["totalAverage"]);
		return $this;
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
		$array = array();
		$array ["identifier"] = $this->getIdentifier ();
		$array["nb"] = $this->getNb();
		$array["totalAverage"] = $this->getTotalAverage();
		return $array;
	}

	//shiva -get methods
	public function setSpelling($spelling)
	{
		$this->spelling = $spelling;
	}
	public function setStyle($style)
	{
		$this->style = $style;
	}
	public function setContent($content)
	{
		$this->content = $content;
	}
	public function setIdentifier($identi)
	{
		$this->identifier = $identi;
	}
	//shiva end
	/**
	 * setNb
	 * sets value of nb variable
	 *
	 * @param text $comment
	 */
	public function setNb($nb)
	{
		$this->nb = $nb;
	}

	/**
	 * setTotalAverage
	 * sets value of totalAverage
	 *
	 * @param double $note
	 */
	public function setTotalAverage($totalAverage)
	{
		$this->totalAverage = $totalAverage;
	}

	
	/**
	 * getNb
	 * this method will be used to obtain nb value
	 *
	 * @return integer
	 */
	public function getNb()
	{
		return $this->nb;
	}

	/**
	 * getTotalAverage
	 * this method will be used to obtain "totalAverage" value
	 *
	 * @return double
	 */
	public function getTotalAverage()
	{
		return $this->totalAverage;
	}
	
	
	//shiva -get methods
	public function getSpelling()
	{
		return $this->spelling;
	}
	public function getStyle()
	{
		return $this->style;
	}
	public function getContent()
	{
		return $this->content;
	}
	
	public function getIdentifier()
	{
		return $this->identifier;
	}
	
	/**
	 * insert
	 * this method will insert data into the pre-selected table
	 * 
	 * @abstract insertRecord
	 * 
	 * @return boolean returns true if data successfully inserted
	 */
	public function insert()
	{
		$dataplus=$this->loadIntoArray();
		if($this->insertQuery($dataplus))
			return true;
		else
			return false;
	}
	
	//shiva end

	public function loadById($id)
	{
		$query = "SELECT * FROM $this->_name WHERE identifier=$id";
		$results = $this->getQuery($query, false);
		if ($results)
		{
			$this->setIdentifier($id);
			$this->setNb($results[0]->nb);
			$this->setTotalAverage($results[0]->totalAverage);
		}
	}

	
	public function updateNote($note)
	{
		$this->setTotalAverage(($this->getTotalAverage() * $this->getNb() + $note) / ($this->getNb() + 1));
		$this->setNb($this->getNb() + 1);
		$data = array();
		$data['totalAverage'] = $this->getTotalAverage();
		$data['nb'] = $this->getNb();
		$this->updateQuery($data, " identifier = " . $this->getIdentifier());
	}

}
