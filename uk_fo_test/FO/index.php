<?php
	//ob_start();
	ini_set("display_errors",0);
	
	//include path configuration file
	include("nconfig/path.php");	
	
	//include class definition file
	include("nconfig/class.php");
	
	//include session file
	include("nconfig/session.php");

	//include libraries file
	include("nconfig/libraries.php");

/************************* global registration ******************************/
// Load and register Configuration
$_country='en';
$config = new Zend_Config_Ini(CONFIG_PATH . 'ep_'.$_country.'.ini', 'config');
Zend_Registry::set('_config', $config);

// register country
Zend_Registry::set('_country', $_country);


// register array database
$arrayDb = new Ep_Db_ArrayDb2();
Zend_Registry::set('_arrayDb', $arrayDb);


//register balance value
$bal = new Ep_Controller_Balance(2);
Zend_Registry::set('_balance', $bal->getRank());

// Load common view
$view = new Ep_Controller_View('front');
$view->setScriptPath(SCRIPT_VIEW_PATH);
$view->setCompilePath(COMPILE_PATH);

//register common view
Zend_Registry::set('_view', $view);

//added by shiva
$_totalLang = array("fr");
Zend_Registry::set('_totalLang', $_totalLang);


//added by arun
	 $frontendOptions = array(
		'lifetime' => 300, // cache lifetime of 5mintues
	   'automatic_serialization' => TRUE
	);
	 
	$backendOptions = array(
		//'cache_dir' => './tmp/' // Directory where to put the cache files
	);
	 
	// getting a Zend_Cache_Core object
	$cache = Zend_Cache::factory('Core',
								 'File',
								 $frontendOptions,
								 $backendOptions);
	Zend_Registry::set('EP_Cache',$cache);

//include cookies definition file
include("nconfig/cookie.php");

// Set up the front controller and dispatch
try {
	$front = Zend_Controller_Front::getInstance();
	//catch exceptions
	$front->throwExceptions(true);
	//Disable view rendererrequire_once 'application/models/String.php';
	$front->setParam('noViewRenderer', true);
	//add ControllerDirectory
	$front->addControllerDirectory(APP_PATH_ROOT . 'controllers');
	//set base url from ep.ini
	//$front->setBaseUrl($config->www->baseurl);
	//Dispatch front controller
	
	$route_config = new Zend_Config_Ini(CONFIG_PATH . 'ep_'.$_country.'.ini', 'routes');
	$router = $front->getRouter();
	$router->addConfig($route_config, 'routes');
	
	$front->dispatch();
}

//other error
/*(catch (Exception $e) 
{
	echo $e;
}*/
catch (Zend_Exception $e) 
{
		$exception=get_class($e);
		
		//echo $e->getResponse()->setHttpResponseCode(404);
		if($exception=="Zend_Db_Statement_Exception")
		{
			//echo $e;		
			echo $e->getMessage();
			//new request object
			$request_object = new Zend_Controller_Request_Http($config->www->baseurl."/error/error");
			//set parameters for 404 type
			$request_object->setParam('error_handler','DbStatementException');
			$request_object->setParam('error_message',$e);
			if($front->getRequest()->action!="index")
				$request_object->setParam('errorType','default');

				//dispatch search page
			$front->dispatch($request_object);			
		}
		else if($exception=="Zend_Controller_Dispatcher_Exception" || $exception=="Zend_Controller_Action_Exception" )
		{
			header("HTTP/1.0 404 Not Found", false, 404);
			//new request object
			$request_object = new Zend_Controller_Request_Http($config->www->baseurl."/error/error");

			//set parameters for 404 type
			$request_object->setParam('error_handler','PageNotFoundException');
			if($front->getRequest()->action!="index")
				$request_object->setParam('errorType','default');

				//dispatch search page
			$front->dispatch($request_object);
		
		}
		else
		{
			echo $e;exit;
		}
		
}
