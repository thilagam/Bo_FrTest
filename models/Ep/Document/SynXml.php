<?
/**
 * EDITPLACE::SYNXML
 *
 * Purpose
 *
 *	  Display a data synopsis based on XML tree
 *
 *
 * @author  Farid Machrou <fmachrou@oboulo.com>
 * @package EDITPLACE
 * @version $Version$ - 1.0
 */

require_once '/synopsis/XML/PARSER/Parser.php';
require_once '/synopsis/XML/XML_TREE/Node.php';
require_once '/synopsis/XML/XML_TREE/Tree.php';

// our hello world class
class Ep_Document_SynXml extends XML_Tree
{
    /**
     * Constructor
     *
     * @param  string  filename  Filename where to read the XML
     * @param  string  version   XML Version to apply
     */
    function SynXml($filename = '', $version = '1.0')
    {
    	parent::XML_Tree($filename,$version);
    }
  
    function getSynopsis()
    {
    	global $_mysql_db;
       	global $_cust;
       	global $_path;
       			
		// on récupére tous les fils du noeud racine
		$children = $this->getElementsByTagNameFromNode("DOCUMENT", $this->root);
		if(!count($children))$children = $this->getElementsByTagNameFromNode("document", $this->root);
		
		if(!count($children))return $arrayDoc["exist"][2];
		
		$i=0;
		$arrayDoc = array();
		$arrayDoc["exist"][1]=0;
		$arrayDoc["exist"][0]=0;
		$arrayDoc["update"][1]=0;
		$arrayDoc["update"][0]=0;
	
		foreach($children as $c)
		{
			$d = new DocumentPartner($_mysql_db);
			$d->setCustomerId($_cust->getIdentifier());
			$d->setId($c->getAttribute("identifier"));

			$browser = new BrowserHtml();
			$browser->root = $c;
			
			if($d->exist())$arrayDoc["exist"][1]++;
			else $arrayDoc["exist"][0]++;
			$arrayDoc["result"][$i] = $browser;
			if($browser->isNotValid())$arrayDoc["error"][$i] = $browser->isNotValid();
			if($browser->exist($_path."synopsis/".$d->getIdentifier().".xml"))$arrayDoc["update"][1]++;
			else $arrayDoc["update"][0]++;
			
			$i++;	
		}
		return $arrayDoc;
    }
 
    function decodeXml($string)
    {
    	$source = array("[wt]","[t]","[cw]","[e]","[ao]","[ap]","[ww]");
    	$out = array("´","–","’","&","«","»","‘");
    	
    	$string = str_replace($source,$out,$string);
    	
    	$source = array("Â…","Â’","â€™","Ã±","Ã¼","Ã©","Ã¨","Ãª","Ã»","Ã¢","Ã¶","Ã€","Ã®","Ã«","Ã§","Ã‰","Ã ","Ã´","Â°","Ã¹","Ã¯","Å“");
    	$out = array("…","'","'","ñ","ü","é","è","ê","û","â","ô","à","î","ë","ç","é","à","ô","°","ù","ï","œ");
		
		return str_replace($source,$out,$string);
    }
    
}

?>
