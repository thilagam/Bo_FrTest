<?php

/**
 * Ep_Xml_Db
 * This abstract class provides all necessary xml methods 
 * @date 14 mars 08
 * @category EditPlace
 * @author Shiva
 * @package Xml
 * @version 1.0
 */

abstract class Ep_Db_Xml 
{
	
	/**
	 * String Variable
	 * @var String $_filePathName holds the xml file path
	 */
	protected $_filePathName;
	
	/**
	 * Array Variable
	 * @var Array $data holds the attributes for a node
	 */
	protected $data = array();
	
	/**
	 * SimpleXMLElement object
	 * @var SimpleXMLElement $_xmlElement 
	 */
	protected  $_xmlElement;
	
	/**
	 * String Variable
	 * @var String $_nodeName 
	 */
	protected  $_nodeName;

	/**
	 * String Variable
	 * @var String $_nodeName 
	 */
	protected  $_nodeValue;
	
	/**
	 * Constructor
	 * loads the contents of a XML file and creates an instance of the SimpleXMLElement class
	 * @param String $filePath holds file path name for the xml file to be loaded
	 * @param String $rootName root node name
	 * @return SimpleXmlElement
	 */
	function __construct($filePath,$rootName='') 
	{
		try
		{
		$fs = @fopen($filePath, "r");
		}
		catch (exception $e)
		{
			echo $e->getTrace();
		}
		if($fs==false)
		{
			//@fclose($fs);
			$fh = @fopen($filePath,'w+');
			$contents = '<?xml version="1.0" encoding="UTF-8"?>';
			$contents .= '<'.$rootName.'> </'.$rootName.'>';
			//$contents = '<'.$rootName.'> </'.$rootName.'>';
			@fwrite($fh,$contents);
			
			$this->_filePathName = $filePath;
			$xml = simplexml_load_file($filePath);
			$this->setXmlElement(simplexml_load_file($filePath));
			@fclose($fh);
			return $xml;
		}	
		else 
		{
			$this->_filePathName = $filePath;
			simplexml_load_file($filePath);
			$this->setXmlElement(simplexml_load_file($filePath));
			return $xml;
		}
	}
	
	/**
	 * Set XmlElement
	 * this will set the SimpleXMLElement object
	 * @param SimpleXmlElement $sxe
	 */
	private function setXmlElement($sxe) {
		$this->_xmlElement = $sxe;	
	}
	
	/**
	 * Get XmlElement
	 * this will provide SimpleXMLElement object
	 * @param void
	 * @return SimpleXmlElement $xmlElement
	 */
	private function getXmlElement() {
		return $this->_xmlElement;
	}
	
	/**
	 * saveXMLChanges
	 * Function to save changes to xml files
	 * @param simpleXmlElement $xmlFile  
	 * @return void
	 */
	public function saveXMLChanges($xmlFile)
	{
		$fp = @fopen($this->_filePathName,"w");
		@fwrite($fp,$xmlFile->asXML());
		@fclose($fp);	
	}
	
	/**
	 * insertNode
	 * inserts a single new child node into the root node
	 * @param String $nodeName child node to be inserted
	 * @param String $nodeValue child node value to be inserted 
	 * @param Array $nodeAttribute contains an array of attribute name and value
	 * @return integer $i if node is inserted then it is set else 0
	 */
	public function insertNode($nodeName, $nodeValue, $nodeAttribute) 
	{		
		$xml = $this->getXmlElement();
		$i = 1;
		foreach ($xml->children() as $child)
		{
		    if($child->getName()== $nodeName)
		    {
			    $i = 0;
			    break;
		    }
		}
		if($i == 1)
		{
			$xml->addChild($nodeName, $nodeValue);
			
			foreach($nodeAttribute as $key => $keyv)
			{ 
				$xml->$nodeName->addAttribute($key, $keyv);
			}
		    $this->saveXMLChanges($xml);
		}
		return $i;
	}

	/**
	 * insert
	 * Inserts a single new child
	 * @return integer $i if node is inserted then it is set else 0
	 */
	public function insert() 
	{	
		$this->setDatas();
		$array = $this->data;
		return $this->insertNode($this->_nodeName,$this->_nodeValue,$this->data);
	}
	
	/**
	 * setAttributes
	 * inserts an array of attributes for the specified node from data array
	 * @param String $nodeName node to which attributes are inserted
	 * @return void
	 */
	public function setAttributes($nodeName)
	{
		$xml = $this->getXmlElement();
		
		$this->setDatas();

		foreach($this->data as $key=>$value)
		{
			$xml->$nodeName->addAttribute($key, $value);
		}
		$this->saveXMLChanges($xml);
	}
	
	/**
	 * getAttributes
	 * gets an array of attributes for a specified node
	 * @param String $nodeName node from which attributes are retrieved
	 * @return Array $attributes_value returns an array of attributes
	 */
	public function getAttributes($nodeName)
	{
		$xml = $this->getXmlElement();

		$k =0;
		foreach($xml->$nodeName->attributes() as $key => $keyvalue) 
		{
		   $name[$k] = $key;
		   $attributes_value[$k] = $keyvalue;
		   $k++;
		}
		$this->data = $attributes_value;
			
		return $attributes_value;
	}
	
	/**
	 * setDatas abstract method
	 * this method will set $data array as attributes for the specified node
	 */
	abstract public function setDatas();
	
	/**
	 * getDatas abstract method
	 * this method will return $data array as attributes for the specified node
	 */	
	abstract public function getDatas();
	
	/**
	 * deleteNode
	 * deletes a specified node from the xml file
	 * @param String $nodeName child node to be deleted
	 * @return integer $j if node is deleted then it is set else 0
	 */
	public function deleteNode($nodeName) 
	{		
		$xml = $this->getXmlElement();
		
		//imports simplexml object $xml into DOM object $dom_sxe
		$dom_sxe = dom_import_simplexml($xml);
		
		if (!$dom_sxe) 
		{
		    echo 'Error while converting XML';
		    exit;
		}
		$dom = new DOMDocument();
		$dom_ = $dom->importNode($dom_sxe, true);
		$dom->appendChild($dom_);
		$items = $dom->getElementsByTagName($nodeName);
		
		$j = 0;
		
		if($items->length != 0)
		{
			for ($i = 0; $i < $items->length; $i++)
			{
				$elem = $items->item($i);
				$dom->documentElement->removeChild($elem);
			}
			$j++;
		}
		else return $j;
		
		$fp = @fopen($this->_filePathName,"w");
		@fwrite($fp,$dom->saveXML());
		@fclose($fp);

		return $j;
	}

	/**
	 * deleteNode
	 * deletes a specified child node for a particular parent node from the xml file
	 * @param String $childnodeName child node to be deleted
	 * @param String $parentNode particular parent node
	 * @return integer $j if node is deleted then it is set else 0
	 */
	public function deleteChildNode($childnodeName, $parentNode) 
	{		
		$xml = $this->getXmlElement();
		
		//imports simplexml object $xml into DOM object $dom_sxe
		$dom_sxe = dom_import_simplexml($xml);
		
		if (!$dom_sxe)
		{
		    echo 'Error while converting XML';
		    exit;
		}
		$dom = new DOMDocument();
		$dom_ = $dom->importNode($dom_sxe, true);
		$dom->appendChild($dom_);
		
		foreach ($dom->documentElement->childNodes as $parent) 
		{
    	//if node is an element (nodeType == 1) and the name is $parentNode loop further
    		if ($parent->nodeType == 1 && $parent->nodeName == $parentNode) 
    		{
        		foreach ($parent->childNodes  as $item) 
        		{
            		$items = $parent->getElementsByTagName($childnodeName);	
        			if($items->length != 0)
					{
						for ($i = 0; $i < $items->length; $i++)
						{
							$elem = $items->item($i);
							$parent->removeChild($elem);
						}
					}
        		}
    		}
		}
		$fp = @fopen($this->_filePathName,"w");
		@fwrite($fp,$dom->saveXML());
		@fclose($fp);
	}
	
	/**
	 * updateNode
	 * updates a specified node from the xml file
	 * @param String $nodeName child node to be updated
	 * @param String $nodeValue new child node value for updation
	 * @return integer $i if node is updated then it is set else 0
	 */
	public function updateNode($nodeName,$nodeValue,$nodeAttribute) 
	{
		$xml = $this->getXmlElement();
		$i = 0;
		foreach ($xml->children() as $child )
		{
		    if($child->getName()== $nodeName)
		    {
		    	$i = 1;
		    	$xml->$nodeName = $nodeValue;
		    	$k =0;
		    	foreach($xml->$nodeName->attributes() as $a => $b) 
		    	{
				    $n[$k] = $a;
				    $v[$k] = $b;
				    $k++;
				}
				$j = 0;
		    	foreach($nodeAttribute as $key => $keyv)
				{
					if($n[$j] != $key && $v[$j] != $keyv)
					{
						$xml->$nodeName->addAttribute($key, $keyv);
					}
					$j++;
				}
			    $this->saveXMLChanges($xml);
			    break;
			}
		}
		return $i;
	}

	/**
	 * getNode
	 * gets node value for a specified node name
	 * @param String $nodeName node name for retrieving it's value
	 * @return String $cv if node is found then it is set else 0
	 */
	public function getNode($nodeName)
	{
		$xml = $this->getXmlElement();
		
		foreach ($xml->children() as $child => $cv)
		{
		    if($child == $nodeName)
		    {
		    	return $cv;
		    }
		}
		return null;
	}

	/**
	 * getNodeMap
	 * gets node value for a specified node name
	 * @param String $nodeName node name for retrieving it's value
	 * @return String $cv if node is found then it is set else 0
	 */
	public function getNodeMap($nodeName)
	{
		$xml = $this->getXmlElement();
		
		foreach ($xml->children() as $key => $child)
		{
		    if($key == $nodeName)
		    {
				$this->_nodeName = $key;
				$this->_nodeValue = (string)$child;
				
				foreach($child->attributes() as $a => $b)
				$this->data[$a]=(string)$b;
				
				$this->getDatas();
		    }
		}
		return null;
	}
	

	public function getAllNodes()
	{
		$xml = $this->getXmlElement();
		
		return $xml;
	}

	/**
	 * getAllNodeMap
	 * 
	 * gets all node values
	 * String $nodeName node name for retrieving it's value
	 * @return Array $array return an array of child class
	 * 
	 */
	
	public function getAllNodesMap()
	{
		$xml = $this->getXmlElement();
		$array = array();
		
		foreach($xml->children() as $key=>$child)
		{
			$this->_nodeName = $key;
			$this->_nodeValue = (string)$child;
			
			foreach($child->attributes() as $a => $b)
				$this->data[$a]=(string)$b;
				
			$this->getDatas();
			$array[] = clone $this;
		}

		return $array;
	}
	
	public function domupdateNode($nodeName,$nodeValue,$nodeAttribute) 
	{		
		$xml = $this->getXmlElement();
		
		//imports simplexml object $xml into DOM object $dom_sxe
		$dom_sxe = dom_import_simplexml($xml);
		
		if (!$dom_sxe) 
		{
		    echo 'Error while converting XML';
		    exit;
		}
		$dom = new DOMDocument();
		$dom_ = $dom->importNode($dom_sxe, true);
		$dom->appendChild($dom_);
		$items = $dom->getElementsByTagName($nodeName);
		foreach ($nodeAttribute as $key => $val)
		{
			$items->item(0)->setAttribute($key, $val);
			$items->item(0)->nodeValue=$nodeValue;
		}
		$fp = @fopen($this->_filePathName,"w");
		@fwrite($fp,$dom->saveXML());
		@fclose($fp);
	}
	
	public function addChildNodesDetails($nodeName, $newNodeName, $nodeValue, $nodeAttribute)
	{
		$xml = $this->getXmlElement();
		
		foreach ($xml->children() as $second_gen)
		{
			if($second_gen->getName()== $nodeName)
		    {
			    $second_gen->addChild($newNodeName, $nodeValue);
			
				foreach($nodeAttribute as $key => $keyv)
				{ 
					$second_gen->$newNodeName->addAttribute($key, $keyv);
				}
			    $this->saveXMLChanges($xml);
		    }
		}
	}
	
	
	public function domupdateChild($parentNode, $childnodeName, $nodeValue, $nodeAttribute) 
	{		
		$xml = $this->getXmlElement();
		
		//imports simplexml object $xml into DOM object $dom_sxe
		$dom_sxe = dom_import_simplexml($xml);
		
		if (!$dom_sxe) 
		{
		    echo 'Error while converting XML';
		    exit;
		}
		$dom = new DOMDocument();
		$dom_ = $dom->importNode($dom_sxe, true);
		$dom->appendChild($dom_);
		//$items = $dom->getElementsByTagName($nodeName);
		
		foreach ($dom->documentElement->childNodes as $parent) 
		{
    	//if node is an element (nodeType == 1) and the name is $parentNode loop further
    		if ($parent->nodeType == 1 && $parent->nodeName == $parentNode) 
    		{
        		foreach ($parent->childNodes  as $item) 
        		{
            		$items = $parent->getElementsByTagName($childnodeName);
            		
        			foreach ($nodeAttribute as $key => $val)
					{
						$items->item(0)->setAttribute($key, $val);
						$items->item(0)->nodeValue = $nodeValue;
					}
        		}
    		}
		}
		$fp = @fopen($this->_filePathName,"w");
		@fwrite($fp,$dom->saveXML());
		@fclose($fp);
	}
	
	/**
	 * getName
	 * 
	 * @return String $name node name
	 */
	public function getNodeName()
	{
		return $this->_nodeName;
	}
	/**
	 * getName
	 * 
	 * @return String $name node value
	 */
	public function getNodeValue()
	{
		return $this->_nodeValue;
	}
	/**
	 * getName
	 * 
	 * @param String $name node name
	 */
	public function setNodeName($name)
	{
		$this->_nodeName = $name;
	}
	/**
	 * setName
	 * 
	 * @param String $name node value
	 */
	public function setNodeValue($value)
	{
		return $this->_nodeValue = $value;
	}
}