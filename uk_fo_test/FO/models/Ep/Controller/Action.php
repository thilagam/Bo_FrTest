<?php
/**
 * Edit Place controller action extension class
 * @category MyFramework
 * @author Rakesh
 * @package Controller
 * @version 1.0
 * @copyright MyFrameWork
 */

abstract class Ep_Controller_Action extends Zend_Controller_Action 
{

    /**
     * View object
     * @var Zend_View_Interface
     */
    protected $_view;
  
     /**
     * ArrayDb object
     * @var ArrayDb
     */
    protected $_arrayDb;
    
     /**
     * Language
     * @var String
     */
    protected $_lang;

     /**
     * Config object
     * @var Zend_Config_Ini
     */
    protected $_config; 
 
     /**
     * Balance
     * @var Boolean
     */
    protected $_balance;
    
	/**
	 * Initialization
	 *
	 * Register common view
	 * 
	 * @author farid
	 *
	 * @return void
	 *
	 */
	public function init()
	{
	    parent::init();
		$this->_view = Zend_Registry::get('_view');
		$this->_arrayDb = Zend_Registry::get('_arrayDb');
		$this->_lang = Zend_Registry::get('_country');
		$this->_config = Zend_Registry::get('_config');
		$this->_balance = Zend_Registry::get('_balance');
		$this->_view->balance = $this->_balance;
		
		//added by arun
		$this->EP_Cache=Zend_Registry::get('EP_Cache');

		if ($_SERVER['HTTPS']){
			$this->_view->staticbase = $this->_config->www->staticbasessl;
			$this->_view->staticbase2 = $this->_config->www->staticbasessl;
			$this->_view->on_https = true;
		}else{
			$this->_view->staticbase = $this->_config->www->staticbase;
			$this->_view->staticbase2 = $this->_config->www->staticbase2;
			$this->_view->on_https = false;
		}
		
		$language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        natsort($language_array);
		foreach ($language_array as $id => $val)
			$language_array[$id]=$val;
		$this->_view->ep_language_list=$language_array;	
		
	}
	
	///////////critsend mail sending action/////
    public function critsendMail($emailfrom, $emailto, $subject, $msg)
    {
        require('BO/nlibrary/script/critsendmailconnector.php');
        $mxm = new MxmConnect();

        $content = array('subject'=> $subject, 'html'=> $msg, 'text' =>'');

        $param = array('tag'=>array('invoice1'), 'mailfrom'=> $emailfrom, 'mailfrom_friendly'=> 'Edit-Place', 'replyto'=>$emailfrom, 'replyto_filtered'=> 'true');

        $emails[0] = array('email'=>$emailto, 'field1'=> 'test');

        try {

            $mxm->sendCampaign($content, $param, $emails);
        } catch (MxmException $e) {
            echo $e->getMessage();     }
    }
	
	/**
	 * Render a view
	 *
	 * Dispatch common static header html module
	 * 
	 * @author farid
	 * @return void
	 * @param string name
	 *
	 */
	 
	public function render($name)
	{
    	$response = $this->getResponse();
    	$response->clearBody();
    	$response->append('main', $this->_view->render($name));
	}
}
