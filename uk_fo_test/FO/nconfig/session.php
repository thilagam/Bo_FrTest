<?

/************ session registration *************/
//multiple domain cookie
session_set_cookie_params(0,'/',$config->www->domain);
session_name('PHPSESSIDTEST');
if(isset($_COOKIE['PHPSESSIDTEST']))session_id($_COOKIE['PHPSESSIDTEST']);

// Process session by farid
$userSession = new Zend_Session_Namespace('session');
Zend_Registry::set('session', $userSession);

// Start Session by ram
$userSession = new Zend_Session_Namespace('userSession');
Zend_Registry::set('userSession', $userSession);

// Start Session by ram
$customerSession = new Zend_Session_Namespace('customerSession');
Zend_Registry::set('customerSession', $customerSession);

// Start Session - milan
$rsession = new Zend_Session_Namespace('resultSession');
Zend_Registry::set('resultSession', $rsession);


//start Session - shiva
$adminLogin = new Zend_Session_Namespace('adminLogin');
Zend_Registry::set('adminLogin', $adminLogin);

$EPClient_reg = new Zend_Session_Namespace('EP_Client');
Zend_Registry::set('EP_Client', $EPClient_reg);

//start Session(Contributor) - Arun
$EP_Contrib_reg = new Zend_Session_Namespace('EP_Contrib_reg');
Zend_Registry::set('EP_Contrib_reg', $EP_Contrib_reg);

$EP_Contrib_Cart = new Zend_Session_Namespace('EP_Contrib_Cart');
Zend_Registry::set('EP_Contrib_Cart', $EP_Contrib_Cart);

$EP_Contrib_Quiz = new Zend_Session_Namespace('EP_Contrib_Quiz');
Zend_Registry::set('EP_Contrib_Quiz', $EP_Contrib_Quiz);
