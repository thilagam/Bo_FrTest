<?php

class NaveencontractController extends Ep_Controller_Action
{

		 public function init(){
		 parent::init();
			Zend_Loader::loadClass('Ep_Document_DocTrack');
			$this->session =  new Zend_Session_Namespace('users');
			$this->_view->lang = $this->_lang;
			$this->adminLogin	= Zend_Registry::get ( 'adminLogin' );
			$this->session = $this->adminLogin;
			$this->_view->loginName = $this->adminLogin->loginName;
			////if session expires/////
			if($this->adminLogin->loginName == '' && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
				echo "session expired...please <a href='/index'>click here</a> to login"; exit;
			}	
			$this->_view->fo_path=$this->fo_path=$this->_config->path->fo_path;
			$this->_view->fo_root_path=$this->fo_root_path=$this->_config->path->fo_root_path;
			
		}
		
		public function contractListAction(){
			
			$contractlist_obj=new Ep_Naveen_NaveenContractlist();
			$contractlist_data=$contractlist_obj->contractlist();
			echo "<pre>";
			print_r($contractlist_data);
			exit;
		
		}
		
		
		public function contractFormAction(){
		 
		$language_array=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        natsort($language_array);
        $this->_view->ep_language_list=$language_array;
		
		 $this->_view->render('naveencontractlist');
		
		}
		  

  
}

?>