<?php

/**
 * Ep_Controller_Balance
 * 
 *
 * @date 10 July 08
 * @category EditPlace
 * @author Farid
 * @package Controller
 * @version version 1.0
 *
 */

class Ep_Controller_Balance
{

	/**
	 * String Variable
	 * @var Integer $rank module access rank
	 * @var String $name module name
	 * @var Integer $division module name
	 *
	 */
	private $value;
	private $division;
	private $maxValue = 100;
	
	/**
     * Constructor
     * 
     * @param String $filePath file path
     * @param String $rootName root node name
     * @return vorank
     */
	function __construct($division) 
	{
		$this->setDivision($division);
	}
	/**
	 * getRank
	 * 
	 * @return Rankentifier $data contains modified price attribute
	 */
	public function getRank()
	{
		 if(!$this->getDivision())return 0;
		 $v = ($this->getValue()%$this->getDivision())+1;
		 return $v;
	}
	/**
	 * getDivision
	 * 
	 * @return division
	 */
	private function getDivision()
	{
		return $this->division;
	}
	/**
	 * getValue
	 * 
	 * @return Random user value
	 */
	public function getValue()
	{
		if(!isset($_COOKIE["_balance"]))
		{
			$_random = rand(0,$this->maxValue);
			setcookie("_balance", $_random,time()+(60*60*24*365*3),'/','.oboulo.com');
			$_COOKIE["_balance"] = $_random;
		}
		else
		{
			$_random = $_COOKIE["_balance"];
		}
		return $_random;
	}
	/**
	 * getValue
	 * 
	 * @param Integer force random value
	 */
	public function forceValue($value)
	{
		setcookie("_balance", $value,time()+(60*60*24*365*3),'/','.oboulo.com');
	}
	/**
	 * setDivision
	 * 
	 * @param String file
	 */
	public function setDivision($division)
	{
		$this->division = $division;
	}
}
?>
