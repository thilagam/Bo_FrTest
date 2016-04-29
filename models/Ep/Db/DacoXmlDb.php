<?php

/**
 * DacoXmlDb
 * This abstract class provides all necessary xml methods
 * @date 14 mars 08
 * @category EditPlace
 * @author Shiva
 * @package Xml
 * @version 1.0
 */

abstract class Ep_Db_DacoXmlDb
{
	
	/**
	 * String Variable
	 * @var String $_filePathName holds the xml file path
	 */
	protected $_filePathName;
	
	protected $_newPath = "/home/sites/site6/users/xmldb/dacodoc";
	protected $_livePath = "/home/sites/site6/users/xmldb/dupLive";
    protected $_duplivePath = "/home/sites/site6/users/xmldb/dacodoc";
    
	/**
	 * Array Variable
	 * 
	 * @var Array $data holds the attributes for a node
	 */
	protected $data = array();
	
	/**
	 * SimpleXMLElement object
	 * @var SimpleXMLElement $_xmlElement 
	 */
	protected $_xmlElement;
	
	function __construct($filePath = '')
	{
		if($filePath!='')
		{
			$this->_filePathName = $filePath;
			$this->setXmlElement(@simplexml_load_file($filePath));
		}
	}

	/**
	 * Set XmlElement
	 * this will set the SimpleXMLElement object
	 * @param SimpleXmlElement $sxe
	 */
	private function setXmlElement($sxe)
	{
		$this->_xmlElement = $sxe;
	}

	/**
	 * Get XmlElement
	 * this will provide SimpleXMLElement object
	 * @param void
	 * @return SimpleXmlElement $xmlElement
	 */
	protected function getXmlElement()
	{
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
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $xmlFile->asXML());
		@fclose($fp);
	}

	/**
	 * fileNameforArray
	 * gives File Name for the supplied input array 
	 * @param String $arrayName array
	 * @return String fileName else return false
	 */
	public function fileNameforArray($arrayName)
	{
		$xml = $this->getXmlElement();
		foreach ($xml->children() as $second_gen)
		{
			foreach ($second_gen->children() as $grand_gen)
			{
				if ($grand_gen->getName() == $arrayName)
				{
					return $second_gen->getName();
				}
			}
			return false;
		}
	}
	
	/**
	 * insertNode
	 * inserts a single new child node into the filename node
	 * @param String $nodeName child node to be inserted
	 * @param Array $nodeAttribute contains an array of attribute name and value
	 * @return integer $i if node is inserted then it is set else it is set to 0
	 */
	public function insertNode($nodeName,$nodeAttribute, $language)
	{
		$xml = $this->getXmlElement();
		
		$nodeName = str_replace(" ","_",$nodeName);
		if(is_numeric(substr($nodeName,0,1)) == "true")
		$nodeName = "_".$nodeName;
		
		$i = 1;
		foreach ($xml->children() as $child)
		{
			if ($child->getName() == $nodeName)
			{
				$i = 0;
				return $i;
			}
		}
		
		if ($i == 1)
		{
			$xml->addChild($nodeName);
			foreach ($language as $v)
			$xml->$nodeName->addChild($v);
			foreach ($nodeAttribute as $key => $keyv)
			{
				if($key == 'desc')
				{
					$xml->$nodeName->addAttribute($key, utf8_encode(urlencode($keyv)));
				}
				else
				$xml->$nodeName->addAttribute($key, $keyv);
			}
			$this->saveXMLChanges($xml);
		}
		return $i;
	}

	/**
	 * updateFileNode
	 * updates a specified node from the xml file
	 * @param String $filename child node to be updated
	 * @param Array $nodeAttribute new attribute values
	 * @return integer $i if node is updated then it is set else 0
	 */
	public function updateFileNode($filename,$nodeAttribute)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $filename;
		$result = $xml->xpath($strTemp);
		foreach ($nodeAttribute as $key => $val)
		{
			if ($val != '')
			{
			if($key == 'desc')
				$result[0][$key] = utf8_encode(urlencode($val));
				else 
				$result[0][$key] = $val;
			}
		}
		$this->saveXMLChanges($xml);
	}
	
	/**
	 * addArrayNodesDetails
	 * inserts a single array node into the root node and then a set of child nodes into the array node
	 * @param String $nodeName filename node 
	 * @param String $arrayNodeName array/simple node to be inserted
	 * @param String $nodeValue description for array/simple node to be inserted
	 * @param Array $nodeAttribute contains an array of attribute name and value
	 * @return boolan if node is inserted then returned as true else false
	 */
	public function addArrayNodesDetails($nodeName, $arrayNodeName, $nodeAttribute,$allLang)
	{
		$xml = $this->getXmlElement();
		$arrayLang = split('\|', $allLang);
		foreach ($arrayLang as $currentLang)
		{
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $nodeName .'/'.$currentLang;
			$result = $xml->xpath($strTemp);
			$result[0]->addChild($arrayNodeName);
			foreach($nodeAttribute as $key => $keyv)
			{
				if($key == 'desc')
				$result[0]->$arrayNodeName->addAttribute($key, utf8_encode(urlencode($keyv)));
				else
				$result[0]->$arrayNodeName->addAttribute($key, $keyv);
			}
			$this->saveXMLChanges($xml);
		}
	}
	
	public function getAllArrayDetails($nodeName, $currentLang)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $nodeName .'/'.$currentLang;
		$result = $xml->xpath($strTemp);
		return $result;
	}

	/**
	 * checkChildNodeExistence
	 * @param String $filename filename node 
	 * @param String $nodeName array/simple node to be inserted
	 * @return integer $i if node is inserted then it is set to 1 else it is set to 0
	 */
	public function checkChildNodeExistence($filename, $currentLang, $arrayNodeName)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
		$result = $xml->xpath($strTemp);
		if(empty($result))
		return false;
		else
		return true;
	}
	
	/**
	 * updateArrayNode
	 * updates a specified node from the xml file
	 * @param String $filename filename node
	 * @param String $childNode new child node value for updation
	 * @param Array $nodeAttribute new attribute values
	 * @return integer $i if node is updated then it is set else 0
	 */
	public function updateArrayNode($filename, $arrayNodeName, $allLang, $nodeAttribute)
	{
		$xml = $this->getXmlElement();
		$arrayLang = split('\|', $allLang);
		foreach($arrayLang as $currentLang)
		{
			$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			//echo 'Funny '.$result[0]['language'];
			if(empty($result))
			{
				$strTemp = '/root/' . $filename .'/'.$currentLang;
				$result = $xml->xpath($strTemp);
				$result[0]->addChild(utf8_encode(urlencode($arrayNodeName)));
				$this->saveXMLChanges($xml);
				$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
				$result = $xml->xpath($strTemp);
			}
			
			foreach($nodeAttribute as $key => $val)
			{
				if($key == 'desc')
				$result[0][$key] = utf8_encode(urlencode($val));
				else
				$result[0][$key] = $val;
			}
		}
		$this->saveXMLChanges($xml);
	}
	
	/**
	 * returnChildNodeComment
	 * this will return old array node comment(value)
	 * @param String $filename filename node 
	 * @param String $nodeName array/simple node name
	 * @return $grand_gen node value if exists else false
	 */
	public function returnChildNodeComment($filename, $nodeName)
	{
		$xml = $this->getXmlElement();
		
		foreach ($xml->children() as $second_gen)
		{
			if ($second_gen->getName() == $filename)
			{
				foreach ($second_gen->children() as $grand_gen)
				{
					if ($grand_gen->getName() == $nodeName)
					{
						return $grand_gen;
					}
				}
				return false;
			}
		}
	}

	/**
	 * checkGrandChildNodeExistence
	 * this will return old array node comment(value)
	 * @param String $filename filename node 
	 * @param String $nodeName array/simple node name
	 * @param String $indexName index node name
	 * @return true if node name exists else false
	 */
	public function checkIndexNodeExistence($filename, $arrayNodeName, $indexName, $currentLang)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName.'/'.$indexName;
		$result = $xml->xpath($strTemp);
		if(empty($result))
		return false;
		else
		return true;
	}
	
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
		
		if (! $dom_sxe)
		{
			echo 'Error while converting XML';
			exit();
		}
		$dom = new DOMDocument();
		$dom_ = $dom->importNode($dom_sxe, true);
		$dom->appendChild($dom_);
		$items = $dom->getElementsByTagName($nodeName);
		
		$j = 0;
		
		if ($items->length != 0)
		{
			for($i = 0; $i < $items->length; $i ++)
			{
				$elem = $items->item($i);
				$dom->documentElement->removeChild($elem);
			}
			$j ++;
		} else
			return $j;
		
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $dom->saveXML());
		@fclose($fp);
		
		return $j;
	}

	/**
	 * deleteArrayNode
	 * deletes a specified array/simple node for a particular parent node from the xml file
	 * @param String $childnodeName child node to be deleted
	 * @param String $parentNode particular parent node
	 * @return integer $j if node is deleted then it is set else 0
	 */
	public function deleteArrayNode($filename, $arrayNodeName, $allLang)
	{
		$xml = $this->getXmlElement();
		$arrayLang = split('\|', $allLang);
		foreach ($arrayLang as $currentLang)
		{
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $filename .'/'.$currentLang;
			$result = $xml->xpath($strTemp);
			foreach($result[0]->children() as $a)
			if($a[0]->getName() == $arrayNodeName)
			{
				unset($a[0]);
				break;
			}
			$this->saveXMLChanges($xml);
		}
		return true;
	}

	/**
	 * deleteIndexNode
	 * deletes a specified array/simple node for a particular parent node from the xml file
	 * @param String $childnodeName child node to be deleted
	 * @param String $parentNode particular parent node
	 * @return integer $j if node is deleted then it is set else 0
	 */
	public function deleteIndexNode($filename, $arrayNodeName, $indexName, $allLang)
	{
		$xml = $this->getXmlElement();
		foreach ($allLang as $currentLang)
		{
			$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			foreach ($result[0]->children() as $a)
			if($a[0]->getName() == $indexName)
			{
				unset($a[0]);
				break;
			}
		}
		$this->saveXMLChanges($xml);
	}
	
	public function deleteContractNode($filename, $arrayNodeName, $indexvalue, $allLang)
	{
		$xml = $this->getXmlElement();
		foreach ($allLang as $currentLang)
		{
			$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			foreach ($result[0]->children() as $a)
			if($indexvalue == $a[0])
			{
				unset($a[0]);
				break;
			}
		}
		$this->saveXMLChanges($xml);
	}
	
	public function addContractNode($filename, $arrayNodeName, $value, $currentLang)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName.'/*';
		$result = $xml->xpath($strTemp);
		$newNodeName = 0;
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName.'/element'.$newNodeName;
		$res = $xml->xpath($strTemp);
		while(!empty($res))
		{
			$newNodeName = $newNodeName + 1;
			$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName.'/element'.$newNodeName;
			$res = $xml->xpath($strTemp);
		}
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$result2 = $xml->xpath($strTemp);
		$result2[0]->addChild('element'.$newNodeName, utf8_encode(urlencode($value)));
		$this->saveXMLChanges($xml);
	}
	
	public function deleteIndexNodeForIndexNo($filename, $arrayNodeName, $indexName, $allLang)
	{
		$xml = $this->getXmlElement();
		foreach ($allLang as $currentLang)
		{
			$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			if(!empty($result))
			foreach ($result[0]->children() as $a)
			if($a[0]->getName() == $indexName)
			{
				$result[0]->$indexName = "";
				break;
			}
		}
		$this->saveXMLChanges($xml);
		
		unset($xml);
		
		$xml = $this->getXmlElement();
		foreach ($allLang as $currentLang)
		{
			if($currentLang != '')
			{
			$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			$kk = 0;
			$prev = 0;
			//$last = count($result[0])-1;
			foreach($result[0]->children() as $a)
			{
				$str = str_replace("element", "", $a[0]->getName());
				if($kk == 1)
				{
					//echo 'assinging ';
					$result[0]->$prev = $a[0];
					$prev = $a[0]->getName();
				}
				
				if($a[0]->getName() == $indexName & $kk != 1)
				{
					//echo 'Found ';
					$prev = $a[0]->getName();
					$kk = 1;
				}
			}
				if(!empty($a[0]))
				unset($a[0]);
				$this->saveXMLChanges($xml);
			}
		}
		
	}
	
	/**
	 * addGrandChildNodes
	 * inserts a single index node into the root node and then a set of child nodes into the index node
	 * @param String $filename filename node
	 * @param String $nodeName array node
	 * @param String $newNodeName index node to be inserted
	 * @param Array $nodeAttribute contains an array of country node name and value
	 * @return integer $i if node is inserted then it is set else it is set to 0
	 */
	public function addIndexNodes($filename, $arrayNodeName, $newNodeName, $allLang, $value="")
	{
		$xml = $this->getXmlElement();
		
		foreach($allLang as $currentLang)
		{
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			if(is_numeric(substr($newNodeName,0,1)) == "true" & is_numeric($newNodeName) == "true")
			{
				$newNodeName = 'element'.$newNodeName;
			}
			$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName.'/'.$newNodeName;
			$result2 = $xml->xpath($strTemp);
			if(empty($result2))
			$result[0]->addChild($newNodeName, utf8_encode(urlencode($value)));
			$this->saveXMLChanges($xml);
		}
	}
	
	/**
	 * updateIndexNode
	 * updates a specified node from the xml file
	 * @param String $filename filename node
	 * @param String $childNode array/simple node name
	 * @param String $grandChildnodeName index node name to be updated
	 * @param Array $grandChildnodeAttribute index node details
	 * @return integer $i if node is updated then it is set else 0
	 */
	public function updateIndexNode($filename, $arrayNodeName, $indexName, $updatedArray, $nodevalue="")
	{
		$xml = $this->getXmlElement();
		foreach($updatedArray as $currentLang => $value)
		{
			/*$tempArr = array("need_help","join","currency","remunerationP","the_total_amount_of_the_transactions_carried_out_today_exceeds_50_please_contact_our_customer_service");
			if(in_array($indexName,$tempArr))
			break;*/
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			$result[0]->$indexName = utf8_encode(urlencode($value));
			$this->saveXMLChanges($xml);
		}
	}
	
	//for simple xml purpose
	public function updateIndexNode2($filename, $arrayNodeName, $updatedArray)
	{
		$xml = $this->getXmlElement();
		foreach($updatedArray as $currentLang => $value)
		{
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $filename .'/'.$currentLang;
			$result = $xml->xpath($strTemp);
			$result[0]->$arrayNodeName = utf8_encode(urlencode($value));
			$this->saveXMLChanges($xml);
		}
	}
	
	//fro new search and update entire file
	public function updateEachIndexNode2($filename, $arrayNodeName, $indexDetailArray, $indexName)
	{
		$xml = $this->getXmlElement();
		foreach($indexDetailArray as $currentLang => $value)
		{
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			$result[0]->$indexName = stripslashes(utf8_encode($value));
			$this->saveXMLChanges($xml);
		}
	}
	
	public function updateSimpleIndexNode2($filename, $arrayNodeName, $updatedArray)
	{
		$xml = $this->getXmlElement();
		foreach($updatedArray as $currentLang => $value)
		{
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $filename .'/'.$currentLang;
			$result = $xml->xpath($strTemp);
			$result[0]->$arrayNodeName = utf8_encode($value);
			$this->saveXMLChanges($xml);
		}
	}
	
	public function updateEachIndexNode($filename, $arrayNodeName, $indexDetailArray, $currentLang)
	{
		$xml = $this->getXmlElement();		
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$result = $xml->xpath($strTemp);
		if(!empty($result))
		if(!empty($indexDetailArray))
		foreach($indexDetailArray as $indexName => $indexValue)
		{
			$result[0]->$indexName = utf8_encode(urlencode($indexValue));
			$this->saveXMLChanges($xml);
		}
	}
	
	public function updateEachIndexNodeNewNew($filename, $arrayNodeName, $indexDetailArray, $currentLang)
	{
		$this->_filePathName = $this->_newPath."/$arrayNodeName.xml";
		$this->setXmlElement(@simplexml_load_file($this->_filePathName));
		$xml = $this->getXmlElement();		
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$result = $xml->xpath($strTemp);
		if(!empty($result))
		if(!empty($indexDetailArray))
		foreach($indexDetailArray as $indexName => $indexValue)
		{
			$result[0]->$indexName = utf8_encode(urlencode($indexValue));
			$this->saveXMLChanges($xml);
		}
	}
	
	
	public function updateEachIndexNodePart2($filename, $arrayNodeName, $indexDetailArray, $currentLang)
	{
		$this->_filePathName = $this->_newPath."/$arrayNodeName.xml";
		$this->setXmlElement(@simplexml_load_file($this->_filePathName));
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $filename .'/'.$currentLang .'/'.$arrayNodeName;
		$result = $xml->xpath($strTemp);
		if(!empty($result))
		if(!empty($indexDetailArray))
		foreach($indexDetailArray as $indexName => $indexValue)
		{
			$result[0]->$indexName = $indexValue;
			$this->saveXMLChanges($xml);
		}
	}
	
	public function updateEachIndexNode2Part2($filename, $arrayNodeName, $indexDetailArray, $indexName, $pathLive = NULL)
	{
		if($pathLive == NULL || $pathLive =="")
        $this->_filePathName = $this->_newPath."/$arrayNodeName.xml";
        if($pathLive =="liveup") //load live path to modify the things
        $this->_filePathName = $this->_livePath."/$arrayNodeName.xml";
        
		$this->setXmlElement(@simplexml_load_file($this->_filePathName));
		$xml = $this->getXmlElement();
		
		foreach($indexDetailArray as $currentLang => $value)
		{
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $filename .'/'.$currentLang.'/'.$arrayNodeName;
			$result = $xml->xpath($strTemp);
			$result[0]->$indexName = stripslashes(utf8_encode($value));
			$this->saveXMLChanges($xml);
		}
	}
	
	public function updateSimpleIndexNode2Part2($filename, $arrayNodeName, $updatedArray, $pathLive = NULL)
	{
		if($pathLive == NULL || $pathLive =="")
        $this->_filePathName = $this->_newPath."/$arrayNodeName.xml";
        if($pathLive =="liveup") //load live path to modify the things
        $this->_filePathName = $this->_livePath."/$arrayNodeName.xml";
        
		$this->setXmlElement(@simplexml_load_file($this->_filePathName));
		$xml = $this->getXmlElement();
		
		foreach($updatedArray as $currentLang => $value)
		{
			if($currentLang == '')
			break;
			$strTemp = '/root/' . $filename .'/'.$currentLang;
			$result = $xml->xpath($strTemp);
			$result[0]->$arrayNodeName = stripslashes(utf8_encode($value));
			$this->saveXMLChanges($xml);
		}
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
			if ($child == $nodeName)
			{
				return $cv;
			}
		}
		return null;
	}

	/**
	 * getAllNodes
	 * get all nodes of xml file returns SimpleXML Element Object
	 * @param void
	 * @return SimpleXmlElement $xml 
	 */
	public function getAllNodes()
	{
		$xml = $this->getXmlElement();
		return $xml;
	}
	
	public function getsearchNodes()
	{
		$xml = $this->getXmlElement();
		return $xml;
	}

	/**
	 * simplexml_to_array
	 * converts the SimpleXmlElement object to Array
	 * @param SimpleXmlElement $xml
	 * @return Array $ar converted array
	 */
	public function simplexml_to_array($xml)
	{
		$ar = array();
		foreach ($xml->children() as $k => $v)
		{
			// recurse the child
			$child = $this->simplexml_to_array($v);
			// if it's not an array, then it was empty, thus a value/string
			if (count($child) == 0)
			{
				$child = (string) $v;
			}
			// add the childs attributes as if they where children
			foreach ($v->attributes() as $ak => $av)
			{
				// if the child is not an array, transform it into one
				if (! is_array($child))
				{
					$child = array('value' => $child);
				}
				$child[$ak] = (string) $av;
			}
			// if the $k is already in our children list, we need to transform
			// it into an array, else we add it as a value
			if (! in_array($k, array_keys($ar)))
			{
				$ar[$k] = $child;
			} elseif (@in_array(0, @array_keys($ar[$k])))
			{
				$ar[$k][] = $child;
			} else
			{
				$ar[$k] = array($ar[$k]);
				$ar[$k][] = $child;
			}
		}
		return $ar;
	}
	
	public function updateArrayDetails($parentNode, $childnodeName, $nodeValue, $nodeAttribute)
	{
		$xml = $this->getXmlElement();
		
		//imports simplexml object $xml into DOM object $dom_sxe
		$dom_sxe = dom_import_simplexml($xml);
		
		if (!$dom_sxe)
		{
			echo 'Error while converting XML';
			exit();
		}
		$dom = new DOMDocument();
		$dom_ = $dom->importNode($dom_sxe, true);
		$dom->appendChild($dom_);
		
		foreach ($dom->documentElement->childNodes as $parent)
		{
			//if node is an element (nodeType == 1) and the name is $parentNode loop further
			if ($parent->nodeType == 1 && $parent->nodeName == $parentNode)
			{
				foreach ($parent->childNodes as $item)
				{
					$items = $parent->getElementsByTagName($childnodeName);
					
					foreach ($nodeAttribute as $key => $val)
					{
						$newValue = utf8_encode(urlencode($val));
						$items->item(0)->setAttribute($key, $val);
						$items->item(0)->nodeValue = $newValue;
					}
				}
			}
		}
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $dom->saveXML());
		@fclose($fp);
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
			if ($key == $nodeName)
			{
				$this->_nodeName = $key;
				$this->_nodeValue = (string) $child;
				
				foreach ($child->attributes() as $a => $b)
					$this->data[$a] = (string) $b;
				
				$this->getDatas();
			}
		}
		return null;
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
		
		foreach ($xml->children() as $key => $child)
		{
			$this->_nodeName = $key;
			$this->_nodeValue = (string) $child;
			
			foreach ($child->attributes() as $a => $b)
				$this->data[$a] = (string) $b;
			
			$this->getDatas();
			$array[] = clone $this;
		}
		
		return $array;
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
	 * insert
	 * Inserts a single new child
	 * @return integer $i if node is inserted then it is set else 0
	 */
	public function insert()
	{
		$this->setDatas();
		$array = $this->data;
		return $this->insertNode($this->_nodeName, $this->_nodeValue, $this->data);
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
		
		foreach ($this->data as $key => $value)
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
		
		$k = 0;
		foreach ($xml->$nodeName->attributes() as $key => $keyvalue)
		{
			$name[$k] = $key;
			$attributes_value[$k] = $keyvalue;
			$k ++;
		}
		$this->data = $attributes_value;
		
		return $attributes_value;
	}
	
	/**
	 * loadArrayv2
	 * gives the index's and values details for the specified array name in all langauges
	 * 
	 * @param String $arryName name of the array
	 * @param String $language language
	 * @return Array $final
	 */
	public function loadArrayv2($arryName, $language)
	{
		//echo 'path '.$this->_newPath."/$arryName.xml.$language";
		$this->setXmlElement(@simplexml_load_file($this->_newPath."/$arryName.xml"));
		$xml = $this->getXmlElement();
		if(!is_object($xml))
		{
			echo "Array Name - $arryName , doesnt exists ask to Shiva - <br/>";
			//return null;
		}
		$result = $xml->xpath('/*/*/'.$language.'/'.$arryName);
		$typeofIndex = $result[0]['index'];
		$type = $result[0]['type'];
		if($type == 'Simple')
		{
			$final = utf8_decode(urldecode($result[0]));
		}
		else
		{
			if($typeofIndex == 'Yes')
			{
				foreach ($result as $kv)
		    	foreach ($kv as $k => $v)
		    	{
		    		$k1 = str_replace("element", "", $k);
		    		if($k1 == '')
		    		$key[] = $k;
		    		else 
		    		$key[] = $k1;
		    		$val[] = utf8_decode(urldecode((string)$v));
		    	}	
			}
			else 
			{
				foreach ($result as $kv)
		    	foreach ($kv as $k => $v)
		    	{
		    		$k = str_replace("element", "", $k);
		    		$key[] = $k;
		    		$val[] = utf8_decode(urldecode((string)$v));
		    	}
			}
			$final = array();
			if(count($key))	$final = array_combine($key, $val);
		}
		
		return $final;
	}
	
	public function liveloadArray($arryName, $language)
	{
		//echo 'path '.$this->_newPath."/$arryName.xml.$language";
		$this->setXmlElement(@simplexml_load_file($this->_livePath."/$arryName.xml"));
		$xml = $this->getXmlElement();
		if(!is_object($xml))
		{
			//echo "Array Name - $arryName , doesnt exists ask to Shiva - <br/>";
			return null;
		}
		$result = $xml->xpath('/*/*/'.$language.'/'.$arryName);
		$typeofIndex = $result[0]['index'];
		$type = $result[0]['type'];
		if($type == 'Simple')
		{
			$final = utf8_decode(urldecode($result[0]));
		}
		else
		{
			if($typeofIndex == 'Yes')
			{
				foreach ($result as $kv)
		    	foreach ($kv as $k => $v)
		    	{
		    		$k1 = str_replace("element", "", $k);
		    		if($k1 == '')
		    		$key[] = $k;
		    		else 
		    		$key[] = $k1;
		    		$val[] = utf8_decode(urldecode((string)$v));
		    	}	
			}
			else 
			{
				foreach ($result as $kv)
		    	foreach ($kv as $k => $v)
		    	{
		    		$k = str_replace("element", "", $k);
		    		$key[] = $k;
		    		$val[] = utf8_decode(urldecode((string)$v));
		    	}
			}
			$final = array();
			if(count($key))	$final = array_combine($key, $val);
		}
		
		return $final;
	}
	
	
	public function searchdetails($filename, $tempArr,$index, $lang)
	{
		$xml = $this->getXmlElement();
		foreach ($lang as $l)
		{
			if($index != '')
			$strTemp = '/root/' . $filename .'/'.$l.'/'.$tempArr.'/'.$index;
			else
			$strTemp = '/root/' . $filename .'/'.$l.'/'.$tempArr;
			$result = $xml->xpath($strTemp);
			$finResult[$l] = (string)$result[0]; 
		}
		
		return $finResult;
	}
	
	public function searchdetails2($filename, $tempArr, $lang)
	{
		$xml = $this->getXmlElement();
		foreach ($lang as $l)
		{
			$strTemp = '/root/' . $filename .'/'.$l.'/'.$tempArr;
			$result = $xml->xpath($strTemp);
			$finResult[$l] = (string)$result[0]; 
		}
		return $finResult;
	}
	
	public function searchdetailsPart2($filename, $tempArr,$index, $lang)
	{
		$this->_filePathName = $this->_newPath."/$tempArr.xml";
		$this->setXmlElement(@simplexml_load_file($this->_filePathName));
		$xml = $this->getXmlElement();
		
		foreach ($lang as $l)
		{
			if($index != '')
			$strTemp = '/root/' . $filename .'/'.$l.'/'.$tempArr.'/'.$index;
			else
			$strTemp = '/root/' . $filename .'/'.$l.'/'.$tempArr;
			$result = $xml->xpath($strTemp);
			$finResult[$l] = (string)$result[0]; 
		}
		
		return $finResult;
	}
	
	public function searchdetails2Part2($filename, $tempArr, $lang)
	{
		$this->_filePathName = $this->_newPath."/$tempArr.xml";
		$this->setXmlElement(@simplexml_load_file($this->_filePathName));
		$xml = $this->getXmlElement();
		
		foreach ($lang as $l)
		{
			$strTemp = '/root/' . $filename .'/'.$l.'/'.$tempArr;
			$result = $xml->xpath($strTemp);
			$finResult[$l] = (string)$result[0]; 
		}
		return $finResult;
	}

}
