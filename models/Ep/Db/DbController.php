<?php

/**
 * Ep_Db_DbController
 *
 * this controller will provide all necessary methods for interacting with database
 *
 * @author milan
 * @creation 19th March, 2008
 * @package Db
 * @version 2.0
 *
 */

require_once 'Zend/Db.php';
require_once 'Zend/Db/Table.php';

class Ep_Db_DbController
{

	/**
	 * String variables used for connecting to the database
	 *
	 * @var string $dbName The database name
	 * @var string $hostName The host name
	 * @var integer $port The port number
	 * @var string $userName The username to connect to the database
	 * @var string $password The password to connect to the database
	 */
	private $dbName;
	private $hostName;
	private $port;
	private $userName;
	private $password;


	public function __construct($connect=true)
	{
		/**
		 if $connect is true then there will be connection established to the database
		 if $connect is false then there will be no connection established. In that case
		 the person has to call dbConnectAction explictly
		*/

		if($connect)
			$this->dbConnectAction(NULL,NULL,NULL,NULL,NULL);
	}

	/**
 	* dbConnectAction
 	* establishes connection with database
 	* @param dbName String
 	* @param hostName String
 	* @param port integer
 	* @param userName String
 	* @param password String
 	* @return dbAdapter connection adapter
 	*/
	public function dbConnectAction($dbName,$hostName,$port,$userName,$password,$pdoOpt=false)
	{

		// taken from ep.ini file
		//$epConfig = new Zend_Config_Ini('ep.ini', 'default');
		$epConfig = Zend_Registry::get('_config');
		$this->dbName = $epConfig->db->dbname;
		$this->hostName = $epConfig->db->hostname;
		$this->port = $epConfig->db->port;	// there is no port #
		$this->userName = $epConfig->db->username;
		$this->password = $epConfig->db->password;


		// set default values if empty
		$dbName = ($dbName == NULL) ? $this->dbName : $dbName;
		$hostName = ($hostName == NULL) ? $this->hostName : $hostName;
		$port = ($port == NULL)? $this->port : $port;
		$userName = ($userName == NULL) ? $this->userName : $userName;
		$password = ($password == NULL) ? $this->password : $password;
		//***//
		$pdoOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"); 
		$conArr=array(
			'host'     => $hostName,
    		'username' => $userName,
    		'password' => $password,
    		'dbname'   => $dbName,
    		//'driver_options'  => $pdoOptions 
			);
		/* if($pdoOpt){
			$conArr['driver_options']=$pdoOptions;
		} */
		
		try 
		{
		$this->dbAdapter = Zend_Db::factory('PDO_MYSQL',$conArr);
		
		// generates connection of the database
		$this->dbAdapter->getConnection();
		}

		/* try
		{
		$this->dbAdapter = Zend_Db::factory('PDO_MYSQL', array(
    	'host'     => $hostName,
    	'username' => $userName,
    	'password' => $password,
    	'dbname'   => $dbName,
        'charset' => 'UTF8',
		'unix_socket' => '/tmp/mysql.sock'
		));

		// generates connection of the database
		$this->dbAdapter->getConnection();
		} */
		catch (Zend_Db_Adapter_Exception $e)
		{
    	// perhaps a failed login credential, or perhaps the RDBMS is not running
    	echo "<br>Unable to Log into database. Please type correct username, password and Please make sure database is running";
    	return NULL;
		}
		catch (Zend_Exception $e)
		{
   	 	// perhaps factory() failed to load the specified Adapter class
   	 	echo "<br> Unable to load the adapter class. Factory method failed.<br> Message: ".$e;
   	 	return NULL;
		}

		Zend_Db_Table::setDefaultAdapter($this->dbAdapter);

		return $this->dbAdapter;	// CAN STORE IT IN REGISTRY OR CONFIG FILE TOO
	}		// dbConnectAction ends

	/**
	 * dbDisconnectAction
	 * disconnects the connection to database and flushes the object
	 * @param dbAdapter database connection adapter
	 * @return successStatus returns true on disconnection, else false
	 */
	public function dbDisconnectAction($dbAdapter)
	{
		try
		{
		// closing database connection
		$dbAdapter->closeConnection();
		}
		catch (Zend_Db_Adapter_Exception $e)
		{
		echo "Unable to close the connection.<br> Message: ".$e;
		return FALSE;
		}

		return TRUE;

	}	// dbDisconnectAction ends

}	// dbController ends
