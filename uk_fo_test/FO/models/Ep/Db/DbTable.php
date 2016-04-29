<?php

/**
 * Ep_Db_DbTable
 * 
 * @abstract This abstract class will provide all necessary methods for child classes
 * to access db 
 * @author Milan
 * @package Db
 * @version 3.0
 */

require_once 'Zend/Db/Table/Abstract.php';

abstract class Ep_Db_DbTable extends Zend_Db_Table_Abstract
{
	
	protected $_name = '';	// table name
	
	private $dbController_obj;

	/**
	 * __construct()
	 * constructor will be initializing class object of dbConntroller
	 */
	public function __construct($tableName=NULL)
	{
		Zend_Loader::loadClass(Ep_Db_DbController);
		$this->dbController_obj = new Ep_Db_DbController();
		
		$this->_name = $tableName;			
	}

	/**
	 * _setupTableName
	 * This method will be used to assign name of table to variable _name
	 * @param tableName String Name of the table
	 * 
	 */
	protected function _setupTableName($tableName=NULL)
    {
    	// may need to use quote for tableName
    	Zend_Loader::loadClass("Ep_Db_DbController");
		$this->dbController_obj = new Ep_Db_DbController(FALSE);
        $this->_name = $tableName;
    }
   	
    /**
     * getQuery
     * This method will return objects or arrays with help of $returnType by executing
     * $query
     * 
     * @param String query It will be used to set where conditions for gathering data
     * 
     * Usage:- $query = 'select columnName (as p1) from tableName,tableName1 
     * where columnName1 = value orderby tableName1.columnName'
     * 
     * @param boolean returnType It will be used to set mode for returning $result
     * 
     * Usage:- $returnType=FALSE, method will return array of objects
     * Usage:- $returnType=TRUE, method will return array of associative array
     * 
     * @return Object if returnType is FALSE
     * 
     * Usage:- $result[0]->columnName
     * 
     * @return Array if returnType is TRUE
     * 
     * Usage:- $result[0]['columnName']
     */
	public function getQuery($query=NULL,$returnType=FALSE)
	{		
		if ($_GET['sql_debug']){
			echo "<pre style='text-align:left;font-family:consolas,monospace;padding:10px;background:#CCFFCC;color:black;font-weight:bold;overflow:auto;margin:10px;'>"; 
			echo $query."\n";
			$e = new Exception();
			print_r(str_replace('/home/sites/site6', '', $e->getTraceAsString()));
			echo "</pre>";
		}
		/****** establish connection ******/
		try
		{
			$this->_setAdapter($this->dbController_obj->dbConnectAction(NULL,NULL,NULL,NULL,NULL));
		}
		catch(Exception $e)
		{
			return false;
		}
				 
		/******* changing the fetch mode ******/
		if($returnType == FALSE || $returnType == NULL) 
		// disable this one if you want resultset as an associative array - true
		$this->getAdapter()->setFetchMode(Zend_Db::FETCH_OBJ);  // for objects - false

		/***** executing query *****/
		$result = $this->getAdapter()->fetchAll($query);
				
		/****** close connection ******/
		$this->getAdapter()->closeConnection();

		return $result;
	}

    /**
     * getNbRows
     * This method will return number of results of a query
     * $query
     * 
     * @param String query It will be used to set where conditions for gathering data
     * 
     * Usage:- $query = 'select columnName (as p1) from tableName,tableName1 
     * where columnName1 = value orderby tableName1.columnName'
     * 
     * 
     * @return Integer Results number
     * 
     * 
     */
	protected function getNbRows($query=NULL)
	{
		/****** establish connection ******/
		try
		{
			$this->_setAdapter($this->dbController_obj->dbConnectAction(NULL,NULL,NULL,NULL,NULL));
		}
		catch(Exception $e)
		{
			return false;
		}
			
		// executing query
		$result = $this->getAdapter()->fetchAll($query);
				
		/****** close connection ******/
		$this->getAdapter()->closeConnection();

		return count($result);
	
	}

    /**
     * loadByQuery
     * This method will return loaded data from database row to class instanciation
     * and return true if row exist, else false
     * $query
     * 
     * @param String query It will be used to set where conditions for gathering data
     * 
     * Usage:- $query = 'select columnName (as p1) from tableName,tableName1 
     * where columnName1 = value orderby tableName1.columnName'
     * 
     * 
     * @return Boolean Test result
     * 
     * 
     */
	protected function loadByQuery($query)
	{
		//$query = "SELECT D.*,P.customerPrice as price,P.customerPrice2 as price2,E.totalAverage as rating FROM DOCUMENT D,PRICE P,EVALUATION E WHERE D.identifier = P.documentId AND E.identifier = D.identifier AND D.identifier = '39221'";
		
		$array = $this->getQuery($query,true);  
		//echo "\n\n in db table = ".print_r($array);
		//echo "\n\n query in db table = ".$query;
		//echo "\n\n count in db table = ".count($array);
		
		$this->loadData($array[0]);
		if(count($array))return true;
		else return false;
	}
	
    /**
     * selectByQuery
     * This method will return loaded data from database row to array of class instanciation
     * 
     * $query
     * 
     * @param String query It will be used to set where conditions for gathering data
     * 
     * Usage:- $query = 'select columnName (as p1) from tableName,tableName1 
     * where columnName1 = value orderby tableName1.columnName'
     * 
     * 
     * @return Array class result
     * 
     * 
     */
	protected function selectByQuery($query)
	{
		$array = $this->getQuery($query,true);
		foreach($array as $a)
		{
			$this->loadData($a);
			$return[] = clone($this);
		}
		return $return;
	}
	
	/**
	 * loadData
	 * This method is used to load data from an array
	 * 
	 * @param Array Associative array
	 */
	abstract protected function loadData($array);

	/**
	 * loadIntoArray
	 * This method is used to load data from an array
	 * 
	 * @return Array Associative array
	 */
	abstract protected function loadIntoArray();
		
	/**
	 * insertRecord
	 * This method will be used to insert a particular record to the table
	 * 
	 * @param Array data  This data array will have all the values needed to be inserted
	 * 
	 * Usage:- $data = array('columnName_1' => 'value_1','columnName_2' => 'value_2')
	 * 
	 * @return status boolean returns true when data is inserted successfully
	 */
	protected function insertQuery($data=NULL)
	{
		/****** establish connection ******/
		try
		{
			$this->_setAdapter($this->dbController_obj->dbConnectAction(NULL,NULL,NULL,NULL,NULL));
		}
		catch(Exception $e)
		{
			return false;
		}
		//executing insert query
		$return = $this->getAdapter()->insert($this->_name,$data);

		/****** close connection ******/
		/*added by arun w.r.t to return last inserted id**/
		//$lastid=$this->getAdapter()->lastInsertId();
		
		$this->getAdapter()->closeConnection();
		
		//return $lastid;

		return $return;
	}

	/**
	 * updateQuery
	 * This method will update particular row in database with $data based on $query
	 * 
	 * @param Array data  This data array will have all the values needed to be updated
	 * 
	 * Usage:- $data = array('columnName_1' => 'value_1','columnName_2' => 'value_2')
	 * 
	 * @param String query where clause of query
	 * 
	 * Usage:- $query = 'columnName = value and/or columnName1 = value1' 
	 * 
	 * @return status boolean returns true when data is updated successfully
	 */
	protected function updateQuery($data=NULL,$query=NULL)
	{
		/****** establish connection ******/
		try
		{
			$this->_setAdapter($this->dbController_obj->dbConnectAction(NULL,NULL,NULL,NULL,NULL));
		}
		catch(Exception $e)
		{
			return false;
		}
		
		//executing update query
		$return = $this->getAdapter()->update($this->_name,$data,$query);

		/****** close connection ******/
		$this->getAdapter()->closeConnection();
		
		return $return;
	}
	
	/**
	 * deleteQuery
	 * This method will be used to delete a particular record from the table
	 * 
	 * @param String where Condition string for the table
	 * Usage:- $where = 'columnName_1 = value_1 and/or columnName_2 = value_2'
	 * 
	 * @return status boolean Returns true if deleted successfully
	 */
	protected function deleteQuery($whereQuery=NULL)
	{
		/****** establish connection ******/
		try
		{
			$this->_setAdapter($this->dbController_obj->dbConnectAction(NULL,NULL,NULL,NULL,NULL));
		}
		catch(Exception $e)
		{
			return false;
		}
		
		/******* query *********/
		if($whereQuery != NULL & $whereQuery != '')
			$return = $this->getAdapter()->delete($this->_name,$whereQuery);
		else
			throw new Exception('where string empty');

		/****** close connection ******/
		$this->getAdapter()->closeConnection();
		
		return $return;
	}

	/**
	 * exist
	 * 
	 * Checks if the result of the query exists (checks if it is valid)
	 *
	 * @param string $query
	 * @return true/false
	 */
	public function exist($query)
	{
		
		$a = $this->getQuery($query,TRUE);
		if(count($a) > 0)
				return true;
			else 
				return false;
	}
		
}	// class dbTable ends


