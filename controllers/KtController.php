<?php

/**
 * BnpController - The controller class for Bnp main menu
 *
 * @author
 * @version
 */
class KtController extends Ep_Controller_Action
{
    public function welcomebackAction(){

        $obj = new Ep_Kt_Model();
        $result = $obj->WelcomeMessage();
        $this->_view->list=$result;
        $this->_view->listCount=count($result);
        $this->render('kt-welcome');
    }

/*	private $text_admin;
	public function init()
	{
		parent::init();
		$this->_view->lang = $this->_lang;
		$this->adminLogin = Zend_Registry::get('adminLogin');
        $this->searchSession = Zend_Registry::get('searchSession');
        $this->sid = session_id();
        $this->commonAction();//////////including main menu and left panel content

        ////////////////////////////////////////////////////////////////////////////////
        $category=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        array_unshift($category, "S&eacute;lectionner");
        $this->_view->categories_array = $category;
		$nationality=$this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
         array_unshift($nationality, "S&eacute;lectionner");
        for($i=-1; $i<=count($nationality); $i++)
        {
            if($i == -1)
            {
                $nationality1[-1] = "S&eacute;lectionner";
            }
            $nationality1[$i] = $nationality[$i+1];
        }  //print_r($nationality1);
        $this->_view->nationality_array = $nationality1;
        $languages=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        asort($languages);
        array_unshift($languages, "S&eacute;lectionner");
        $this->_view->languages_array = $languages;

	}
    public function welcomeKtAction(){
        $obj_kt = new Ep_Kt_User();
        $obj_kt->WelcomeMessage();
        echo "yes an in controller";
        $this->render('kt-welcome');
    }*/

}