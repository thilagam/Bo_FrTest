<?php

/**
 * Ep_Db_Xml
 *
 * This abstract class provides all necessary xml methods
 *
 * @date 14 mars 08
 * @category EditPlace
 * @author Shiva
 * @package Db
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
	protected $_xmlElement;

	/**
	 * Constructor
	 * loads the contents of a XML file and creates an instance of the SimpleXMLElement class
	 * @param String $filePath holds file path name for the xml file to be loaded
	 * @param String $rootName root node name
	 * @return SimpleXmlElement
	 */
	function __construct($filePath, $rootName = '')
	{
		try
		{
			$fs = @fopen($filePath, "r");
		} catch (exception $e)
		{
			echo $e->getTrace();
		}
		if ($fs == false)
		{
			//@fclose($fs);
			$fh = @fopen($filePath, 'w+');
			$contents = '<?xml version="1.0" encoding="UTF-8"?>';
			$contents .= '<' . $rootName . '> </' . $rootName . '>';
			//$contents = '<'.$rootName.'> </'.$rootName.'>';
			@fwrite($fh, $contents);

			$this->_filePathName = $filePath;
			$xml = simplexml_load_file($filePath);
			$this->setXmlElement(@simplexml_load_file($filePath));
			@fclose($fh);
			return $xml;
		} else
		{
		    $this->_filePathName = $filePath;
			simplexml_load_file($filePath);
			$this->setXmlElement(@simplexml_load_file($filePath));
			return $xml;
		}
	}

	/**
	 * updatefile
	 *
	 * Updates a file
	 *
	 * @param string $file
	 */
	public function updatefile($file)
	{
		$fh = fopen('./en/' . $file, "a");
		@fwrite($fh, $contents);
		fclose($fh);
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
	 *
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
	 * loadArray
	 * gives the index's and values details for the specified array name in all langauges
	 *
	 * @param String $arryName name of the array
	 * @param String $language language
	 * @param Boolean $typeofIndex array index true or false
	 * @return Array $final
	 */
	public function loadArray($arryName, $language, $typeofIndex="true")
	{
		$xml = $this->getXmlElement();
		$result = $xml->xpath('/*/*/'.$arryName);
		if($typeofIndex == true)
		{
			foreach ($result as $key)
				foreach ($key as $key1 => $val)
				{
					foreach ($val as $key2 => $val1)
					{
						if($key2 == $language)$array1[] = $key1;
					}
				}
		}
		else
		{
			foreach ($result as $key)
			foreach ($key as $key1 => $val)
			{
				$array1[] = str_replace("element", "", $key1);
			}
		}
		$result2 = $xml->xpath('/*/*/'.$arryName.'/*/'.$language);
		foreach ($result2 as $key => $val)
		{
			$array2[] = (string)$val;
		}
		$final = array_combine($array1, $array2);
		return $final;
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
		$xml = $this->getXmlElement();
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

	/**
	 * insertNode
	 * inserts a single new child node into the root node
	 *
	 * @param String $nodeName child node to be inserted
	 * @param String $nodeValue child node value to be inserted
	 * @param Array $nodeAttribute contains an array of attribute name and value
	 * @return integer $i if node is inserted then it is set else it is set to 0
	 */
	public function insertNode($nodeName, $nodeValue, $nodeAttribute)
	{
		$xml = $this->getXmlElement();
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
			$xml->addChild($nodeName, $nodeValue);
			if (! empty($nodeAttribute)) //added
			foreach ($nodeAttribute as $key => $keyv)
			{
				$xml->$nodeName->addAttribute($key, $keyv);
			}
			$this->saveXMLChanges($xml);
		}
		return $i;
	}

	/**
	 * findLang
	 *
	 * Find the language
	 *
	 * @param Simple XML object $xml
	 * @param string $file
	 * @return array $result
	 */
	public function findLang($xml, $file)
	{
		$result = $xml->xpath ('/root/'.$file);
		return $result[0]['language'];
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
	 * deleteFile
	 *
	 * Deletes a file
	 *
	 * @param file $file
	 * @param string $languag1
	 */
	public function deleteFile($file, $languag1)
	{
		$arrayLang = split('\|', $languag1);
		foreach ($arrayLang as $languag1)
		{
			if ($languag1 == "fr")
				$fr = 'fr';
			if ($languag1 == "pt")
				$pt = 'pt';
			if ($languag1 == "en")
				$en = 'en';
			if ($languag1 == "in")
				$in = 'in';
		}

		if($fr == 'fr')
		{
			unlink('./fr/' . $file);
		}
		if($pt == 'pt')
		{
			unlink('./pt/' . $file);
		}
		if($in == 'in')
		{
			unlink('./in/' . $file);
		}
		if($en == 'en')
		{
			unlink('./en/' . $file);
		}
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
	 * modifyFiles
	 *
	 * Use this method to modify a file
	 *
	 * @param string $dir
	 * @param string $filename
	 * @param string $string
	 */
	public function modifyFiles($dir, $filename, $string)
	{
		$fh = @fopen("./" . $dir . "/" . $filename, "w");
		@fwrite($fh, $string);
		@fclose($fh);
	}

	/**
	 * deleteArray
	 *
	 * Deletes an array
	 *
	 * @param string $filename
	 * @param string $nodeNameDeleted
	 * @param string $type
	 */
	public function deleteArray($filename, $nodeNameDeleted, $type)
	{
		$stringPT = file_get_contents('./pt/' . $filename);
		$stringEN = file_get_contents('./en/' . $filename);
		$stringFR = file_get_contents('./fr/' . $filename);
		$stringIN = file_get_contents('./in/' . $filename);

		if ($type == 'Array')
			$pos2 = ');'; else
			$pos2 = '";';

		if ($stringPT != FALSE)
		{
			$posPT = strpos($stringPT, $nodeNameDeleted);
			if ($posPT != FALSE)
			{
				$endPos = strpos($stringPT, $pos2, $posPT);
				$lastPos = $endPos - $posPT;
				$lastPos += 2;
				$stringPT = substr_replace($stringPT, "", $posPT, $lastPos);
				$this->modifyFiles('pt', $filename, $stringPT);
			}
		}
		if ($stringEN != FALSE)
		{
			$posEN = strpos($stringEN, $nodeNameDeleted);
			if ($posEN != FALSE)
			{
				$endPos = strpos($stringEN, $pos2, $posEN);
				$lastPos = $endPos - $posEN;
				$lastPos += 2;
				$stringEN = substr_replace($stringEN, "", $posEN, $lastPos);
				$this->modifyFiles('en', $filename, $stringEN);
			}
		}
		if ($stringFR != FALSE)
		{
			$posFR = strpos($stringFR, $nodeNameDeleted);
			if ($posFR != FALSE)
			{
				$endPos = strpos($stringFR, $pos2, $posFR);
				$lastPos = $endPos - $posFR;
				$lastPos += 2;
				$stringFR = substr_replace($stringFR, "", $posFR, $lastPos);
				$this->modifyFiles('fr', $filename, $stringFR);
			}
		}
		if ($stringIN != FALSE)
		{
			$posFR = strpos($stringIN, $nodeNameDeleted);
			if ($posFR != FALSE)
			{
				$endPos = strpos($stringIN, $pos2, $posFR);
				$lastPos = $endPos - $posFR;
				$lastPos += 2;
				$stringIN = substr_replace($stringIN, "", $posFR, $lastPos);
				$this->modifyFiles('in', $filename, $stringIN);
			}
		}
	}

	/**
	 * deleteNode
	 * deletes a specified child node for a particular parent node from the xml file
	 *
	 * @param String $childnodeName child node to be deleted
	 * @param String $parentNode particular parent node
	 * @return integer $j if node is deleted then it is set else 0
	 */
	public function deleteArrayNode($childnodeName, $parentNode, $type)
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

		foreach ($dom->documentElement->childNodes as $parent)
		{
			//if node is an element (nodeType == 1) and the name is $parentNode loop further
			if ($parent->nodeType == 1 && $parent->nodeName == $parentNode)
			{
				foreach ($parent->childNodes as $item)
				{
					$items = $parent->getElementsByTagName($childnodeName);
					if ($items->length != 0)
					{
						for($i = 0; $i < $items->length; $i ++)
						{
							$elem = $items->item($i);
							$parent->removeChild($elem);
						}
					}
				}
			}
		}
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $dom->saveXML());
		@fclose($fp);
		$this->deleteArray($parentNode, '$' . $childnodeName, $type);
	}

	/**
	 * deleteGrandChildNode
	 *
	 * Deletes a Grand Child Node
	 *
	 * @param string $parentNode
	 * @param string $childnodeName
	 * @param string $grandChildNode
	 */
	public function deleteGrandChildNode($parentNode, $childnodeName, $grandChildNode)
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

		foreach ($dom->documentElement->childNodes as $parent)
		{
			if ($parent->nodeName == $parentNode)
			{
				foreach ($parent->childNodes as $child)
				{
					if ($child->nodeName == $childnodeName)
					{
						foreach ($child->childNodes as $item)
						{
							$items = $child->getElementsByTagName($grandChildNode);
							if ($items->length != 0)
							{
								for($i = 0; $i < $items->length; $i ++)
								{
									$elem = $items->item($i);
									$child->removeChild($elem);
								}
							}
						}
					}
				}
			}
		}
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $dom->saveXML());
		@fclose($fp);
	}

	/**
	 * updateNode
	 * updates a specified node from the xml file
	 * @param String $nodeName child node to be updated
	 * @param String $nodeValue new child node value for updation
	 * @return integer $i if node is updated then it is set else 0
	 */
	public function updateNode($nodeName, $nodeValue, $nodeAttribute)
	{
		$xml = $this->getXmlElement();
		$i = 0;
		foreach ($xml->children() as $child)
		{
			if ($child->getName() == $nodeName)
			{
				$i = 1;
				$xml->$nodeName = $nodeValue;
				$k = 0;
				foreach ($xml->$nodeName->attributes() as $a => $b)
				{
					$n[$k] = $a;
					$v[$k] = $b;
					$k ++;
				}
				$j = 0;
				foreach ($nodeAttribute as $key => $keyv)
				{
					if ($n[$j] != $key && $v[$j] != $keyv)
					{
						$xml->$nodeName->addAttribute($key, $keyv);
					}
					$j ++;
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
			if ($child == $nodeName)
			{
				return $cv;
			}
		}
		return null;
	}

	/**
	 * getAllNodes
	 *
	 * Loads all the nodes to $xml
	 *
	 * @return Simple XML Object
	 */
	public function getAllNodes()
	{
		$xml = $this->getXmlElement();

		return $xml;
	}

	/**
	 * domupdateNode
	 *
	 * Updates the node
	 *
	 * @param string $nodeName
	 * @param string $nodeValue
	 * @param array $nodeAttribute
	 */
	public function domupdateNode($nodeName, $nodeValue, $nodeAttribute)
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
		foreach ($nodeAttribute as $key => $val)
		{
			$items->item(0)->setAttribute($key, $val);
			$items->item(0)->nodeValue = $nodeValue;
		}
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $dom->saveXML());
		@fclose($fp);
	}

	/**
	 * addArrayNodesDetails
	 *
	 * Gets the details of the array nodes
	 *
	 * @param string $nodeName
	 * @param string $newNodeName
	 * @param string $nodeValue
	 * @param array $nodeAttribute
	 * @return integer $i
	 */
	public function addArrayNodesDetails($nodeName, $newNodeName, $nodeValue, $nodeAttribute)
	{
		$xml = $this->getXmlElement();
		$i = 1;
		foreach ($xml->children() as $second_gen)
		{
			if ($second_gen->getName() == $nodeName)
			{
				foreach ($second_gen->children() as $child)
				{
					if ($child->getName() == $newNodeName)
					{
						$i = 0;
						return $i;
					}
				}
				$second_gen->addChild($newNodeName, $nodeValue);
				if (! empty($nodeAttribute))
				foreach ($nodeAttribute as $key => $keyv)
				{
					$second_gen->$newNodeName->addAttribute($key, $keyv);
				}
				$this->saveXMLChanges($xml);
				return $i;
			}
		}
	}

	/**
	 * simplexml_to_array
	 *
	 * Converts a xml to array
	 *
	 * @param Simple XML object $xml
	 * @return array $ar
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

	/**
	 * addArray
	 *
	 * Adds array details to a file
	 *
	 * @param string $str
	 * @param string $expression
	 * @param string $mystring
	 * @return string
	 */
	public function addArray($str, $expression, $mystring)
	{
		$pieces = explode($expression, $mystring);
		$mod_string = $pieces[0] . $expression . $str . $pieces[1];

		$n = count($pieces);
		for($i = 2; $i < $n; $i ++)
		{
			$mod_string .= $expression . $pieces[$i];
		}
		return $mod_string;
	}

	/**
	 * filetoString
	 *
	 * Gets the contents of the file on to a string
	 *
	 * @param string $dir
	 * @param string $filename
	 * @return string
	 */
	public function filetoString($dir, $filename)
	{
		$mystring = file_get_contents('./' . $dir . '/' . $filename);
		return $mystring;
	}

	/**
	 * writeArray
	 *
	 * Writes a index/simple from xml file to the specified array to the php file
	 *
	 * @param Simple XML Object $xml
	 * @param array $arr
	 * @param string $filename
	 * @param string $nodeName
	 * @param string $newNodeName
	 * @param array $nodeAttribute
	 */
	public function writeArray($xml, $arr, $filename, $nodeName, $newNodeName, $nodeAttribute)
	{
		$var = $filename;
		$var2 = $nodeName;
		$var3 = $newNodeName;
		$res = $arr[$var][$var2][$var3];

		$strUS = "";
		$strFR = "";
		$strBR = "";
		$strIN = "";

		$result = $xml->xpath ('/root/'.$var.'/'.$var2);

		$type = $result[0]['type'];
		$index = $result[0]['index'];

		if($type == 'Array')
		{
			if($index == 'No')
			{
				$var3 = str_replace("element","",$var3);
			}
			foreach ($res as $key => $val)
			{
				if ($key == 'fr' && $val != "")
					$strFR = "\n" . '"' . $var3 . '" => "' . $val . '",';
				if ($key == 'pt' && $val != "")
					$strBR = "\n" . '"' . $var3 . '" => "' . $val . '",';
				if ($key == 'en' && $val != "")
					$strUS = "\n" . '"' . $var3 . '" => "' . $val . '",';
				if ($key == 'in' && $val != "")
					$strIN = "\n" . '"' . $var3 . '" => "' . $val . '",';
			}
		}
		else
		{
			foreach ($res as $key => $val)
			{
				if ($key == 'en' && $val != "")
					$strUS = $val;
				if ($key == 'fr' && $val != "")
					$strFR = $val;
				if ($key == 'pt' && $val != "")
					$strBR = $val;
				if ($key == 'in' && $val != "")
					$strIN = $val;
			}
		}

		$mystringUS = file_get_contents('./en/' . $filename);
		$mystringFR = file_get_contents('./fr/' . $filename);
		$mystringBR = file_get_contents('./pt/' . $filename);
		$mystringIN = file_get_contents('./in/' . $filename);

		if($type == 'Array')
		{
			$expression = '$' . $var2 . ' = array(';
			$arr_stringUS = $this->addArray($strUS, $expression, $mystringUS);
			$arr_stringFR = $this->addArray($strFR, $expression, $mystringFR);
			$arr_stringBR = $this->addArray($strBR, $expression, $mystringBR);
			$arr_stringIN = $this->addArray($strIN, $expression, $mystringIN);
		}
		else
		{
			$expression = '$' . $var2 . ' = "';
			$arr_stringUS = $this->addArray($strUS, $expression, $mystringUS);
			$arr_stringFR = $this->addArray($strFR, $expression, $mystringFR);
			$arr_stringBR = $this->addArray($strBR, $expression, $mystringBR);
			$arr_stringIN = $this->addArray($strIN, $expression, $mystringIN);
		}

		if ($strUS != "")
		{
			$fh = @fopen("./en/" . $var, "w");
			@fwrite($fh, $arr_stringUS);
			@fclose($fh);
		}
		if ($strFR != "")
		{
			$fh = @fopen("./fr/" . $var, "w");
			@fwrite($fh, $arr_stringFR);
			@fclose($fh);
		}
		if ($strBR != "")
		{
			$fh = @fopen("./pt/" . $var, "w");
			@fwrite($fh, $arr_stringBR);
			@fclose($fh);
		}
		if ($strIN != "")
		{
			$fh = @fopen("./in/" . $var, "w");
			@fwrite($fh, $arr_stringIN);
			@fclose($fh);
		}
	}

	/**
	 * writeMainArray
	 *
	 * Writes a index/simple from xml file to the specified main array to the php file
	 *
	 * @param string $filename
	 * @param array $data1
	 * @param string $lang
	 * @param string $descr
	 */
	public function writeMainArray($filename, $data1, $lang, $descr)
	{
		$str = "\n\n" . '// '. $descr;
		$str .= "\n" . '$'. $data1['arrayname'] . ' = array(' . "\n" . ');';

		if ($lang['type'] == 'Array')
		{
			if (strstr($lang['language'], 'fr'))
			{
				$fh = @fopen("./fr/" . $filename, "a");
				fwrite($fh, $str);
				@fclose($fh);
			}
			if (strstr($lang['language'], 'pt'))
			{
				$fh = @fopen("./pt/" . $filename, "a");
				fwrite($fh, $str);
				@fclose($fh);
			}
			if (strstr($lang['language'], 'en'))
			{
				$fh = @fopen("./en/" . $filename, "a");
				fwrite($fh, $str);
				@fclose($fh);
			}
			if (strstr($lang['language'], 'in'))
			{
				$fh = @fopen("./in/" . $filename, "a");
				fwrite($fh, $str);
				@fclose($fh);
			}
		}

		else

		{
			$simpleStr = "\n". '// '. $descr ."\n";
			$simpleStr .= "\n\n" . '$' . $data1['arrayname'] . ' = "";';
			if (strstr($lang['language'], 'fr'))
			{
				$fh = @fopen("./fr/" . $filename, "a");
				fwrite($fh, $simpleStr);
				@fclose($fh);
			}
			if (strstr($lang['language'], 'pt'))
			{
				$fh = @fopen("./pt/" . $filename, "a");
				fwrite($fh, $simpleStr);
				@fclose($fh);
			}
			if (strstr($lang['language'], 'en'))
			{
				$fh = @fopen("./en/" . $filename, "a");
				fwrite($fh, $simpleStr);
				@fclose($fh);
			}
			if (strstr($lang['language'], 'in'))
			{
				$fh = @fopen("./in/" . $filename, "a");
				fwrite($fh, $simpleStr);
				@fclose($fh);
			}
		}
	}

	/**
	 * takeArray
	 *
	 * Picks up the specified array from a file
	 *
	 * @param array $arr
	 * @param string $filename
	 * @param string $nodeName
	 * @param string $newNodeName
	 * @return string
	 */
	public function takeArray($arr, $filename, $nodeName, $newNodeName)
	{
		$var = $filename;
		$var2 = $nodeName;
		$var3 = $newNodeName;
		$res = $arr[$var][$var2][$var3];

		$str = "\n" . '"' . $var3 . '"=>array(' . "\n";
		foreach ($res as $key => $val)
			if ($key != 'value')
				$str .= '"' . $key . '"=>"' . $val . '",' . "\n";
		$str .= '),' . "\n";
		return $str;
	}

	/**
	 * newArray
	 *
	 * Creates a new array
	 *
	 * @param array $arr
	 * @param string $newNodeName
	 * @return string
	 */
	public function newArray($arr, $newNodeName)
	{
		$str = "\n" . '"' . $newNodeName . '"=>array(' . "\n";
		foreach ($arr as $key => $val)
			if ($key != 'value')
				$str .= '"' . $key . '"=>"' . $val . '",' . "\n";
		$str .= '),' . "\n";
		return $str;
	}

	/**
	 * addGrandChildNodes
	 *
	 * Adds grand child nodes
	 *
	 * @param string $filename
	 * @param string $nodeName
	 * @param string $newNodeName
	 * @param string $nodeValue
	 * @param array $nodeAttribute
	 */
	public function addGrandChildNodes($filename, $nodeName, $newNodeName, $nodeValue, $nodeAttribute)
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
						$grand_gen->addChild($newNodeName, '');
						$this->saveXMLChanges($xml);
						foreach ($grand_gen->children() as $moregrand_gen)
						{
							if ($moregrand_gen->getName() == $newNodeName)
							{
								foreach ($nodeAttribute as $key => $keyv)
								{
									$moregrand_gen->addChild($key, $keyv);
								}
							}
						}
						$this->saveXMLChanges($xml);
					}
				}
			}
		}
		$arr = $this->simplexml_to_array($xml);
		$this->writeArray($xml, $arr, $filename, $nodeName, $newNodeName, $nodeAttribute);
	}

	/**
	 * updateArrayDetails
	 *
	 * update the array details
	 *
	 * @param string $parentNode
	 * @param string $childnodeName
	 * @param string $nodeValue
	 * @param array $nodeAttribute
	 */
	public function updateArrayDetails($parentNode, $childnodeName, $nodeValue, $nodeAttribute)
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
						$items->item(0)->setAttribute($key, $val);
						$items->item(0)->nodeValue = $nodeValue;
					}
				}
			}
		}
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $dom->saveXML());
		@fclose($fp);
	}

	/**
	 * updateArrayFiles
	 *
	 * updates the array file
	 *
	 * @param string $dir
	 * @param string $type
	 * @param string $action
	 * @param string $filename
	 * @param string $arrName
	 * @param string $vIndex
	 * @param string $newValue
	 */
	public function updateArrayFiles($dir, $type, $action, $filename, $arrName, $vIndex, $newValue)
	{
			$str = $this->filetoString($dir, $filename);

			$posPT = strpos($str, $arrName);

			if($type == 'Array')
			{
				if ($posPT != FALSE)
				{
					$midPos = strpos($str, '"' . $vIndex . '"', $posPT);
					$endPos = strpos($str, '",', $midPos);
					$lastPos = $endPos - $midPos;
					$lastPos += 3;
					if($action == 'delete')
					{
						$stringPT = substr_replace($str, "", ($midPos - 1), $lastPos);
						$this->modifyFiles($dir, $filename, $stringPT);
					}
					if ($action == "update")
					{
						$stringPT = substr_replace($str, "", ($midPos - 1), $lastPos);
						$stringPT = substr_replace($stringPT, "\n" . '"' . $vIndex . '" => "' . $newValue['v'.$dir] . '",', ($midPos - 1), 0);
						$this->modifyFiles($dir, $filename, $stringPT);
					}
				}
			}
			else
			{
				if ($posPT != FALSE)
				{
					$midPos = strpos($str, $arrName, $posPT);
					$endPos = strpos($str, '";', $midPos);
					$lastPos = $endPos - $midPos;
					$lastPos += 3;
					if($action == 'delete')
					{
						$stringPT = substr_replace($str, "", ($midPos - 1), $lastPos);
						$stringPT = substr_replace($stringPT, "\n" . $arrName .' = "";', ($midPos - 1), 0);
						$this->modifyFiles($dir, $filename, $stringPT);
					}
					if ($action == "update")
					{
						$stringPT = substr_replace($str, "", ($midPos - 1), $lastPos);
						$stringPT = substr_replace($stringPT, "\n" . $arrName .' = "' . $newValue['v'.$dir] . '";', ($midPos - 1), 0);
						$this->modifyFiles($dir, $filename, $stringPT);
					}
				}
			}
	}

	/**
	 * updateArray
	 *
	 * Adds new index to array
	 *
	 * @param string $action
	 * @param string $filename
	 * @param string $arrName
	 * @param string $vIndex
	 * @param array $countryArray
	 * @param string $index
	 * @param string $type
	 * @param string $newValue
	 */
	public function updateArray($action, $filename, $arrName, $vIndex, $countryArray, $index, $type, $newValue = '')
	{
		if($index == 'No')
		{
			$vIndex = str_replace("element","",$vIndex);
		}

		if (strstr($countryArray, 'pt'))
		{
			$this->updateArrayFiles('pt', $type, $action, $filename, $arrName, $vIndex, $newValue);
		}

		if (strstr($countryArray, 'fr'))
		{
			$this->updateArrayFiles('fr', $type, $action, $filename, $arrName, $vIndex, $newValue);
		}

		if (strstr($countryArray, 'en'))
		{
			$this->updateArrayFiles('en', $type, $action, $filename, $arrName, $vIndex, $newValue);
		}

		if (strstr($countryArray, 'in'))
		{
			$this->updateArrayFiles('in', $type, $action, $filename, $arrName, $vIndex, $newValue);
		}
	}

	/**
	 * domupdateGrandChild
	 *
	 * Update grand child node
	 *
	 * @param string $filename
	 * @param string $childNode
	 * @param string $grandChildnodeName
	 * @param string $grandChildnodeValue
	 * @param array $grandChildnodeAttribute
	 */
	function domupdateGrandChild($filename, $childNode, $grandChildnodeName, $grandChildnodeValue, $grandChildnodeAttribute)
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

		foreach ($dom->documentElement->childNodes as $parent)
		{
			if ($parent->nodeName == $filename)
			{
				foreach ($parent->childNodes as $child)
				{
					if ($child->nodeName == $childNode)
					{
						foreach ($child->childNodes as $item)
						{
							$items = $child->getElementsByTagName($grandChildnodeName);
							foreach ($grandChildnodeAttribute as $key => $val)
							{
								$items->item(0)->nodeValue = $val;
							}
						}
					}
				}
			}
		}
		$fp = @fopen($this->_filePathName, "w");
		@fwrite($fp, $dom->saveXML());
		@fclose($fp);
	}

	/**
	 * updateGrandChild
	 *
	 * Update a grand child
	 *
	 * @param string $filename
	 * @param string $childNode
	 * @param string $grandChildnodeName
	 * @param array $grandChildnodeAttribute
	 */
	public function updateGrandChild($filename, $childNode, $grandChildnodeName, $grandChildnodeAttribute)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/root/' . $filename . '/' . $childNode . '/' . $grandChildnodeName;
		$result = $xml->xpath($strTemp);
		foreach ($grandChildnodeAttribute as $key => $val)
		{
			if($val != '')
			$result[0]->$key = $val;
		}
		$this->saveXMLChanges($xml);
		/*$fp = @fopen('XMLFiles/shiva.xml', "w");
		@fwrite($fp, $xml->saveXML());
		@fclose($fp);*/
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
		$return = 0;

		foreach ($xml->children() as $key => $child)
		{
		    if($key == $nodeName)
		    {
				$this->_nodeName = $key;
				$this->_nodeValue = (string)$child;

				foreach($child->attributes() as $a => $b)
				$this->data[$a]=(string)$b;

				$this->getDatas();
				$return = 1;
		    }
		}
		return $return;
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
		return $this->insertNode($this->_nodeName,$this->_nodeValue,$this->data);
	}

	/**
	 * update
	 * Updates a single new child
	 * @return integer $i if node is updated then it is set else 0
	 */
	public function update()
	{
		$this->setDatas();
		$array = $this->data;
		return $this->domupdateNode($this->_nodeName,$this->_nodeValue,$this->data);
	}

	/**
	 * delete
	 * Deletes a single new child
	 * @return integer $i if node is deleted then it is set else 0
	 */
	public function delete()
	{
		$this->setDatas();
		$array = $this->data;
		return $this->deleteNode($this->_nodeName);
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

	/**
	 * moduleVersion
	 *
	 * returns all module versions details
	 *
	 * @return SimpleXML Object $result
	 */
	public function moduleVersion()
	{
		$xml = $this->getXmlElement();
		$strTemp = '/versions/*';
		$result = $xml->xpath($strTemp);
		return $result;
	}

	/**
	 * getmoduleInfo
	 *
	 * returns all details of a particular module
	 *
	 * @return SimpleXML Object $result
	 */
	public function getmoduleInfo($modName)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/module/'.$modName;
		$result = $xml->xpath($strTemp);
		return $result;
	}

	/**
	 * searchActiveforVersion
	 *
	 * it will search all the module versions and if its Active attribute is 1 then it will make it 0
	 * @param String $versionName version name
	 */
	public function searchActiveforVersion($versionName)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/versions/*[@active=1]';
		$result = $xml->xpath($strTemp);
		if(!empty($result))
		foreach($result as $order)
		{
			if(strstr($order[0]->getName(), $versionName))
			{
				$order[0]['active'] = 0;
			}
		}
		$this->saveXMLChanges($xml);
	}

	/**
	 * setOrderforVersion
	 *
	 * it will search $fileName module version and if its order attribute is 0 then it will make it 1
	 *
	 * @param String $dirName version name
	 * @param String $fileName file name
	 */
	public function setOrderforVersion($dirName, $fileName)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/script/'.$dirName.'/'.$fileName;
		$result = $xml->xpath($strTemp);
		$result[0]['online'] = 1;
		$this->saveXMLChanges($xml);
	}

	/**
	 * setActiveforVersion
	 *
	 * it will search $versionName script version and if its active attribute is 0 then it will make it 1
	 *
	 * @param String $versionName version name
	 */
	public function setActiveforVersion($versionName)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/versions/'.$versionName;
		$result = $xml->xpath($strTemp);
		$result[0]['active'] = 1;
		$this->saveXMLChanges($xml);
	}

	/**
	 * getVersionDetail
	 *
	 * returns the module version details of a particular supplied module name
	 *
	 * @param String $verName version name
	 * @return SimpleXML Object $result
	 */
	public function getVersionDetail($verName)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/versions/' . $verName;
		$result = $xml->xpath($strTemp);
		return $result;
	}

	/**
	 * scriptVersion
	 *
	 * It will return all the script versions details for supplied directory name
	 *
	 * @param String $dirName directory name
	 * @return SimpleXML Object $result
	 */
	public function scriptVersion($dirName)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/script/' . $dirName . '/*';
		$result = $xml->xpath($strTemp);
		return $result;
	}

	/**
	 * setparentNode
	 *
	 * @param String $name node value
	 */
	public function setparentNode($value)
	{
		return $this->_parentNode = $value;
	}

	/**
	 * getparentNode
	 *
	 * @return String $name node value
	 */
	public function getparentNode()
	{
		return $this->_parentNode;
	}

	/**
	 * insertChild
	 *
	 * Inserts a single new child
	 *
	 * @return integer $i if node is inserted then it is set else 0
	 */
	public function insertChild()
	{
		$this->setChildDatas();
		$array = $this->data;
		return $this->addArrayNodesDetails($this->_parentNode, $this->_nodeName, $this->_nodeValue, $this->data);
	}

	/**
	 * searchOrderforVersion
	 *
	 * it will search all the script versions and if its order attribute is 1 then it will make it 0
	 * @param String $dirName version name
	 * @param String $fileName file name
	 */
	public function searchOrderforVersion($dirName, $fileName)
	{
		$xml = $this->getXmlElement();
		$strTemp = '/script/'.$dirName.'/*[@online=1]';
		$result = $xml->xpath($strTemp);
		if(!empty($result))
		foreach($result as $order)
			{
				if(strstr($order[0]->getName(), $fileName))
				{
					$order[0]['online'] = 0;
				}
			}
			$this->saveXMLChanges($xml);
	}

}

