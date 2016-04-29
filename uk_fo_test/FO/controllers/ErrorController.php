<?php

class ErrorController extends Ep_Controller_Action {

  public function init()
  {
	 parent::init();
	 $stats_obj=new Ep_Stats_Stats();
     $statistics=$stats_obj->getAllStatistics($configurations); 
     $this->_view->stats=$statistics;
  }
  
  public function errorAction() {

      

    $error = $this->_request->getParam('error_handler');
	$error_message = $this->_request->getParam('error_message');

      //print_r($error);exit;



    switch($error) {

      case 'PageNotFoundException':

        $this->_forward('page-not-found');

        break;
		
	 case 'DbStatementException':

        $this->_forward('db-error',null, null, array('error_message' => $error_message));

        break;	

	

      case 'NotAuthorizedException':

        $this->_forward('not-authorized');

        break;



      default:

        //put some default handling logic here

        break;

    }

  }

  public function indexAction()

  {

      $this->render("index_home");

  }

  public function pageNotFoundAction() {

    //goes here if the page was not found
      $this->render("Error_404");

  }
  public function dbErrorAction() {

    //goes here if the page was not found
	$error_message = $this->_request->getParam('error_message');
	$this->_view->error_message=$error_message;

      $this->render("EP_Error_DB");

  }
   



  public function notAuthorizedAction() {

    //goes here if the user has no access to a page

  }

}