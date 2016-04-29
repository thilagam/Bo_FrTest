<?

require_once SMARTY_PATH . 'Smarty.class.php';

 /**
 * Edit Place view action extension class
 *
 * @category EditPlace
 * @author Vinay
 * @package Controller
 * @version 0.0
 *
 */
 
class Ep_Controller_View extends Zend_View
{
     /**
     * ArrayDb object
     * @var ArrayDb
     */
    private $_arrayDb;
    
     /**
     * Language
     * @var String
     */
    private $_lang;

     /**
     * Config object
     * @var Zend_Config_Ini
     */
    private $_config;
 
     /**
     * Objet Smarty
     * @var Smarty
     */
    private $_smarty;
    
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
	}
	
	/**
	 * Render
	 *
	 * Render specific view
	 * 
	 * @author Farid
	 *
	 * @return string
	 *
	 */
	public function render($name)
	{
		$p = new Ep_Controller_Page();
		$p->getNodeMap($name);
		$viewList = array();
		$accessCode = $p->getPageAccesscode($name);
		$Metadetails = $p->getMetadetails($name);
		
		try
		{
			//echo "</br> ProcessCode = ".$accessCode;
			$viewList = $p->getModuleList($this->_lang);
			$modList = $p->getAllModule();
			//echo 'URL : '.$_SERVER['REQUEST_URI'];
			if(!count($viewList))throw new Exception("Problem with viewList");
					
			//sort by order
			if(is_array($viewList[0]))ksort($viewList[0]);
			if(is_array($viewList[1]))ksort($viewList[1]);
			if(is_array($viewList[2]))ksort($viewList[2]);
			if(is_array($viewList[3]))ksort($viewList[3]);
			
			//provide position for skeleton
			$this->mainList = $viewList[0];
			$this->headerList = $viewList[1];
			$this->footerList = $viewList[2];
			$this->rightList =  $viewList[3];

			//private access test        
	  		$userSession = Zend_Registry::get ('userSession');
			if(isset($userSession->identifier))
			$this->connected = 1;
			else
			$this->connected = 0;

			
			foreach ($modList as $mod)
			{
				if(!$this->connected&&$mod->getAccess()==1)
				{
					header('location:'.$this->_config->www->baseurlssl.'/private/newLogin?target='.urlencode($this->getControllerName()."/".$this->getActionName()));
				}
			}
			
			foreach ($modList as $mod)
			{
				if($accessCode != 0 && $this->adminLogin->permission != 0)
				if($this->adminconnected&&$mod->getAccess()==2&&!in_array($accessCode, $this->adminLogin->accessCode))
				{
					header('location:'.$this->_config->www->adminurl.'/process/processtest');
				}
			}
			
			foreach ($modList as $mod)
			{
				if(!$this->adminconnected&&$mod->getAccess()==2)
				{
					header('location:'.$this->_config->www->adminurl.'/?target='.urlencode($this->getControllerName()."/".$this->getActionName()));
				}
			}
			
			$pattern = $p->getPattern();
			$this->cssList = $pattern->getCssList();
			$this->javascriptList = $pattern->getJavascriptList();
			
			if(!$pattern->getSkeleton())throw new Exception("Problem with pattern");
			
			//default charset
			if(!$this->charset)$this->charset = "utf-8";
			$response = $this->_smarty->display($pattern->getSkeleton());
			//echo 'Pattern '.$pattern->getSkeleton();
		}
		catch (Exception $e)
		{
			echo "<strong>You can't access this page</strong><br/>";
			echo "<strong>".$e->getMessage()."<br/>";
		}
		return $response;
	} 
	
	/**
	 * Render
	 *
	 * Render common view
	 * 
	 * @author Farid
	 *
	 * @return string
	 *
	 */	
	public function renderHtml($name)
	{	
		$p = new Ep_Controller_Page();
		$p->getNodeMap($name);
		$viewList = array();

		try
		{
			//get available module
			$viewList = $p->getModuleList($this->_lang);
			$modList = $p->getAllModule();
			
			if(!count($viewList))throw new Exception("Problem with viewList");
					
			//sort by order
			if(is_array($viewList[0]))ksort($viewList[0]);
			if(is_array($viewList[1]))ksort($viewList[1]);
			if(is_array($viewList[2]))ksort($viewList[2]);
			if(is_array($viewList[3]))ksort($viewList[3]);
			
			//provide position for skeleton
			$this->mainList = $viewList[0];
			$this->headerList = $viewList[1];
			$this->footerList = $viewList[2];
			$this->rightList =  $viewList[3];

			$pattern = $p->getPattern();
			$this->cssList = $pattern->getCssList();
			$this->javascriptList = $pattern->getJavascriptList();
						
			if(!$pattern->getSkeleton())throw new Exception("Problem with pattern");
			
			//default charset
			$this->charset = "utf-8";
			
			$response = $this->_smarty->fetch($pattern->getSkeleton());
		}
		catch (Exception $e) 
		{
			echo "<strong>You can't access this page</strong><br/>";
			echo "<strong>".$e->getMessage()."<br/>";
		}
		return $response;
	} 

    /**
     * Constructeur
     *
     * @param string $viewType
     * @param array $extraParams
     * @return void
     */
    public function __construct($viewType = 'front')
    {
    	//register global value
    	$this->_arrayDb = Zend_Registry::get('_arrayDb');
		$this->_lang = Zend_Registry::get('_country');
		$this->_config = Zend_Registry::get('_config');
				
    	// register smarty class
        $this->_smarty = new Smarty();
        //$this->_smarty->caching = true;
        //$smarty->compile_check = true;
     	
         //register front plugin
        if($viewType=='front')
           	$this->_smarty->register_outputfilter(array($this, 'plugin_tpl_translate'));
         
        // register admin plugin
        if($viewType=='admin')
        	$this->_smarty->register_outputfilter(array($this, 'plugin_tpl_translate2'));
    }

	private function getControllerName()
	{
		$explode = explode("/",$_SERVER['REQUEST_URI']);
		return $explode[count($explode)-2];
	}
	private function getActionName()
	{
		$explode = explode("/",$_SERVER['REQUEST_URI']);
		return $explode[count($explode)-1];
	}
	
    /**
     * Retourne l'objet moteur de gabarit
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }

    /**
     * Affecte le dossier des scripts de gabarits
     *
     * @param string $path Le répertoire à affecter au path
     * @return void
     */
    public function setScriptPath($path)
    {
        if (is_readable($path)) {
            $this->_smarty->template_dir = $path;
            return;
        }

        throw new Exception('Unvalid template folder');
    }

    /**
     * Récupère le dossier courant des gabarits
     *
     * @return string
     */
    public function getScriptPaths()
    {
        return array($this->_smarty->template_dir);
    }

    /**
     * Affecte le dossier du compilateur de script
     *
     * @param string $path Le répertoire à affecter au path
     * @return void
     */
    public function setCompilePath($path)
    {
        if (is_readable($path)) {
            $this->_smarty->compile_dir = $path;
            return;
        }

        throw new Exception('Unvalid Compile folder');
    }
 
    /**
     * Affecte le dossier de stockage du cache
     *
     * @param string $path Le répertoire à affecter au path
     * @return void
     */
    public function setCachePath($path)
    {
        if (is_readable($path)) {
            $this->_smarty->cache_dir = $path;
            return;
        }

        throw new Exception('Unvalid Cache folder');
    }

    /**
     * Affectation une variable au gabarit
     *
     * @param string $key Le nom de la variable
     * @param mixed $val La valeur de la variable
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_smarty->assign($key, $val);
    }

    /**
     * Recherche une variable au gabarit
     *
     * @param string $key Le nom de la variable
     * @return mixed La valeur de la variable
     */
    public function __get($key)
    {
        return $this->_smarty->get_template_vars($key);
    }

    /**
     * Autorise le fonctionnement du test avec empty() and isset()
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->_smarty->get_template_vars($key));
    }

    /**
     * Autorise l'effacement de toutes les variables du gabarit
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->_smarty->clear_assign($key);
    }

    /**
     * Affectation de variables au gabarit
     *
     * Autorise une affectation simple (une clé => une valeur) 
     * OU 
     * le passage d'un tableau (paire de clé => valeur) à affecter en masse
     *
     * @see __set()
     * @param string|array $spec Le type d'affectation à utiliser (clé ou tableau de paires clé => valeur)
     * @param mixed $value (Optionel) Si vous assignez une variable nommée, utilisé ceci comme valeur
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->_smarty->assign($spec);
            return;
        }

        $this->_smarty->assign($spec, $value);
    }

    /**
     * Effacement de toutes les variables affectées
     *
     * Efface toutes les variables affectées à Zend_View via {@link assign()} ou
     * surcharge de propriété ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
    {
        $this->_smarty->clear_all_assign();
    }
  
    /**
     * Plugin de filtre qui effectue la traduction des index
     *
     * @return String source Code source modifié
     */
    public function plugin_tpl_translate($source, &$smarty)
    {
    	$config = Zend_Registry::get('_config');
    	$arrayDb = new Ep_Db_ArrayDb2();
    	
		$text =  $arrayDb->loadArrayv2("textes", $this->_lang);
		
	    // Pull out the translate index
		preg_match_all("|%(.*)%|U",$source,$out, PREG_PATTERN_ORDER);
		
		//index to translate
		$index_words = $out[1];
		
		foreach($index_words as $i)
		{
			if(isset($text[$i]))$source = str_replace("%$i%",$text[$i],$source);
		}
		if(ereg('prod',$_SERVER['SERVER_NAME']))
		{
			$source = str_replace("napplication/script","tapplication/script",$source);
			$source = str_replace("napplication/css","tapplication/css",$source);
			$source = str_replace("tapplication/library","tapplication/library",$source);
		}
		if($this->charset=="utf-8")
		{
			$str = new String($source);
			$str->_replaceAE();
			return utf8_encode($str->getString());
		}
		else return $source;
    }
    
	public function plugin_tpl_translate2($source, &$smarty)
    {
    	$config = Zend_Registry::get('_config');
    	$arrayDb = new Ep_Db_ArrayDb2();
    	
		$text =  $arrayDb->loadArrayv2("text_admin", $this->_lang);
		
	    // Pull out the translate index
		preg_match_all("|%(.*)%|U",$source,$out, PREG_PATTERN_ORDER);
		
		//index to translate
		$index_words = $out[1];
		
		foreach($index_words as $i)
		{
			if(isset($text[$i]))$source = str_replace("%$i%",$text[$i],$source);
		}
		$str = new String($source);
		$str->_replaceAE();
		return utf8_encode($str->getString());
		//return $str->getString();
    }
}
