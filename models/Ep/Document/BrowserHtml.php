<?
/**
 * EDITPLACE::BROWSER
 *
 * Purpose
 *
 *	  Display a data browser based on XML tree
 *
 *
 * @author  Farid Machrou <fmachrou@oboulo.com>
 * @package EDITPLACE
 * @version $Version$ - 1.0
 */
require_once 'synopsis/PEAR/Pear.php';
require_once 'synopsis/XML/PARSER/Parser.php';
require_once 'synopsis/XML/XML_TREE/Node.php';
require_once 'synopsis/XML/XML_TREE/Tree.php';


// our hello world class
class Ep_Document_BrowserHtml extends XML_Tree
{
	public $file;
    /**
     * Constructor
     *
     * @param  string  filename  Filename where to read the XML
     * @param  string  version   XML Version to apply
     */
    public function BrowserHtml($filename = '', $version = '1.0')
    {
    	parent::XML_Tree($filename,$version);
    }
    
    function addChild($path,$name)
    {
    	//si profondeur inf�rieur � 4
    	if(count(explode("/",$path)) < 3)
    	{
	    	//r�cup�ration du noeud
	    	if(!$path)$node =& $this->root;
	    	else
	    	$node =& $this->getNodeAt($path);
	    	
	    	//r�cup�ration de l'identifiant du noeud p�re
	    	$id = $node->getAttribute("identifier");
	    	//d�finition de la position du prochain noeuf � ins�rer
	    	$position = count($this->getElementsByTagNameFromNode("part", $node)) + 1;
	    	//d�finition de la nomenclature du noeud � ins�rer
			$identifier = "part".$id.$position;
	       	//d�finition de son identifiant
	       	$attribute=array("identifier"=>$id.$position);
	    	//insertion du noeud fils
	       	$child =& $node->addChild($identifier,"",$attribute);
	
	       	//d�finition du contenu du noeud
	       	$child->addChild("name",$this->encodeXml($name));
	       	
	       	//sauvegarde du browser
	       	$this->save();
    	}
       	
  		// on retourne l'arbre		
 		return $id.$position;
    }  
    function removeChild($path,$pos)
    {
    	 // on recherche le noeud p�re
    	 $parent =& $this->getNodeAt($path);
 		//on supprime le noeud
         $parent->removeChild($pos);
    	// on sauvegarde l'arbre
       	$this->save();
       	// on retourne l'arbre	
   		return $this->getTree();
    }  
    function updateChild($path)
    {
    	// si diff�rent du noeud root
    	if($path!="DOCUMENT")
    	{
	    	// r�cup�ration du noeud name correspondant
	    	$node =& $this->getNodeAt($path."/name");
			// modification du content
			$node->content = $content;
	    	// on sauvegarde l'arbre
    	}
       	$this->save();
       	// on retourne l'arbre	
 		return $this->getTree();
    } 
    function load($filename)
    {
    	$this->filename = $filename;
    	
		if(is_file($filename))
			$this->getTreeFromFile();
		else
			$node =& $this->addRoot("DOCUMENT");
		
		$this->save();
       	
       	// on retourne l'arbre	
 		return $this->getTree();
    }
	  
    function getTree()
    {
    	$deep = 0;
    	$path = "";
		$out = "\n";
		$align = "\n";
		$pos = 1;
		
		$out = $this->getNode($this->root,$deep,$path,$pos);
		
		return $out;	
	}
	
    function save()
    {	
    	$fp = fopen($this->filename,"w");
  		fwrite($fp,$this->get());
  		fclose($fp);
    }
    
    function getNode($node,$deep,$path,$pos)
    {
    	$out = "";
    	if($node->name)
    	{	
    		//D�termination du chemin XPATH du noeud
  			$pathX = $path;
    		if($deep==0)$slash="";
    		else $slash="/";
    		$path .= $slash.$node->name;
   		
	    	$out = "<li>";
	    	 	
	    	$deep++;
		    
			// on r�cup�re tous les fils du noeud
			$children = $this->getElementsByTagNameFromNode("part", $node);
    		// on r�cup�re le contenu du noeud
			$array = $this->getElementsByTagNameFromNode("name", $node);
			if(count($array)>0)$content = $this->decodeXml($array[0]->getcontent());
			else
			$content = $node->name;
			
			$id = $node->getAttribute("identifier");
			
	    	if (count($children) > 0)
	    	{
	       		$out .= "<input readonly id=\"$id\" name=\"$id\" type=\"text\" value=\"$content\" class=\"editlevel$deep\" onFocus=\"selectNode(this,'$path','$pathX',$pos)\">";
	       		$out .= "<ol class=\"ollevel$deep\">";
	       		if($deep==1)$pos = 1;
	       		else $pos = 3;
	       		
		    	foreach ($children as $child) 
		        {
		            $out .= $this->getNode($child,$deep,$path,$pos);
		            $pos += 2;
		        }
		        $out .= "</ol>";
	    	}
	    	else
	    	{
	       		$out .= "<input readonly id=\"$id\" name=\"$id\" type=\"text\" value=\"$content\" class=\"editlevel$deep\" onFocus=\"selectNode(this,'$path','$pathX',$pos)\">";
	    	}
	    	$out .= "</li>";
    	}
    	return $out;

    }
    
    public function showTree()
    {
    	$deep = 0;
    	$path = "";
		$out = "\n";
		$align = "\n";
		$pos = 1;
		
		$out = $this->getDisplayNode($this->root,$deep,$path,$pos);
		
		return $out;
		
	}

   function displayText()
    {
    	$deep = 0;
    	$path = "";
		$out = "\n";
		$align = "\n";
		$pos = 1;
		
		$out = $this->getDisplayText($this->root,$deep,$path,$pos);
		
		return $out;
		
	}

    function getDisplayText($node,$deep,$path,$pos)
    {
    	$out = "";
    	if($node->name)
    	{	
    		//D�termination du chemin XPATH du noeud
  			$pathX = $path;
    		if($deep==0)$slash="";
    		else $slash="/";
    		$path .= $slash.$node->name;
      	 	
	    	$deep++;
		    
			// on r�cup�re tous les fils du noeud
			$children = $this->getElementsByTagNameFromNode("part", $node);
    		// on r�cup�re le contenu du noeud
			$array = $this->getElementsByTagNameFromNode("name", $node);
			if(count($array)>0)$content = new String($this->decodeXml($array[0]->getcontent()));
			else
			$content = new String($node->name);
			
			$content->encodeXmlEntities();
			
			$id = $node->getAttribute("identifier");

			if($content->getString() != "DOCUMENT")
			$out = $content->getString().".\n";
	
	    	if (count($children) > 0)
	    	{
	    		if($deep==1)$pos = 1;
	       		else $pos = 3;
	   
		    	foreach ($children as $child) 
		        {
  		            $out .= $this->getDisplayText($child,$deep,$path,$pos);          
		            $pos += 2;
		        }
	    	}
    	}
    	return $out;

    }
    
   function getDisplayNode($node,$deep,$path,$pos)
    {
    	$out = "";
    	if($node->name)
    	{	
    		//D�termination du chemin XPATH du noeud
  			$pathX = $path;
    		if($deep==0)$slash="";
    		else $slash="/";
    		$path .= $slash.$node->name;
      	 	
	    	$deep++;
		    
			// on r�cup�re tous les fils du noeud
			$children = $this->getElementsByTagNameFromNode("part", $node);
    		// on r�cup�re le contenu du noeud
			$array = $this->getElementsByTagNameFromNode("name", $node);
			if(count($array)>0)$content = new String($this->decodeXml($array[0]->getcontent()));
			else
			$content = new String($node->name);
			
			$content->encodeXmlEntities();
			
			$id = $node->getAttribute("identifier");

			if($content->getString() != "DOCUMENT")
			$out = "<li>".$content->getString();"\n";
	
			
	    	if (count($children) > 0)
	    	{
	       		$out .= "<ol class=\"ollevel$deep\">\n";

	    		if($deep==1)$pos = 1;
	       		else $pos = 3;
	   
		    	foreach ($children as $child) 
		        {
  		              $out .= $this->getDisplayNode($child,$deep,$path,$pos);          
		            $pos += 2;
		        }
		        $out .= "</ol>";
	    	}
	    	
			if($content->getString() != "DOCUMENT")
	    	$out .= "</li>\n";
    	}
    	return $out;

    }

    function checkBrowser()
    {
    	$test = 1;
		
		$out = $this->getCheckBrowser($this->root,$test);
		
		return $out;
		
	}
	function isNotValid()
	{
		if (!$this->checkBrowser($this->root,$test)) return "SYN0";
		else return "";
	}
	function exist($path)
	{
		if(file_exists($path))
		return 1;
		return 0;
	}

   function getCheckBrowser($node,$test)
    {
    	if($node->name)
    	{	
    		$content = "";
			// on r�cup�re tous les fils du noeud
			$children = $this->getElementsByTagNameFromNode("part", $node);
    		// on r�cup�re le contenu du noeud
			$array = $this->getElementsByTagNameFromNode("name", $node);
			if(count($array)>0)$content = $this->decodeXml($array[0]->getcontent());
			else
			$test = 0;
			
			// si noeud valid
			if($content=="")$test = 0; 
	
		    foreach ($children as $child) 
		    {
  		       $test = $this->getCheckBrowser($child,$test);          
		    }
    	}
    	return $test;
    }
    function encodeXml($string)
    {
    	/*$source = array("[wt]","[t]","[cw]","[e]","[ao]","[ap]","[ww]");
    	$out = array("�","�","�","&","�","�","�");
    	
    	$string = str_replace($out,$source,$string);
    	
    	//$source = array("A�","Ç","’","ñ","ü","é","è","ê","û","â","ö","À","î","ë","ç","É","à","ô","°","ù","ï","œ");
    	//$out = array("�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�");

      	//$string = str_replace($out,$source,$string);*/
    			      	  			
		return $string;
    }
    function decodeXml($string)
    {		
		$source = array ('œ',"…","’","‘","Œ");
		$out = array("oe","...","'","'","Oe");

		$string = str_replace($source,$out,$string);
			    	
 		$source = array("[wt]","[t]","[cw]","[e]","[ao]","[ap]","[ww]");
    	$out = array("�","�","�","&","�","�","�");
    	$string = str_replace($source,$out,$string);

	
	//pdf special cars
    	$source = array("â","à","è","ê","ç","î","ô","û","ù","e�","�");
	$test = true;
    	foreach($source as $a)
		if(strpos($string,$a))$test = false;
    	$out = array("�","�","�","�","�","�","�","�","�","�","");
    	$string = str_replace($source,$out,$string);


		//case utf8 string
		if($test)return utf8_decode($string);
		//case pdf string
		else return $string;
		
    }
    
}

?>
