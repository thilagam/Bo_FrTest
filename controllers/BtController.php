<?

class BtController extends Ep_Controller_Action
{
	
    private $data;

	public function init()
	{
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

    }

    public function dashboardAction(){
    	$this->_view->render("bugtracker_dashboard");
    }
  }