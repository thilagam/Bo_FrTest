<?php

/**
 * Ep_Db_ArrayDb
 * 
 * This class provides all necessary methods to maintain php files of all country directories
 * 
 * @date 10 july 08
 * @category EditPlace
 * @author Shiva
 * @package Db
 * @version 1.0
 */


class Ep_Db_ArrayDb extends Ep_Db_Xml
{
	protected $_language = array();
	protected $_filePathName;

	/**
	 * Constructor
	 * this is used to set xml file path and root node
	 * @param String $filePath file path
	 * @param String $rootName root node name
	 * @return void
	 */
	function __construct($xmlfilePath, $rootName = '')
	{
		parent::__construct($xmlfilePath, $rootName);
	}

	/**
	 * findFileextension
	 * returns the extension for the file , its useful when user doesnt specify the file extension 
	 * @param String $fileName file name
	 * @return String $exts
	 */
	public function findFileextension($filename)
	{
		$filename = strtolower($filename);
		$exts = split("[/\\.]", $filename);
		$n = count($exts) - 1;
		$exts = $exts[$n];
		return $exts;
	}

	/**
	 * createNewFile
	 * this is used to create each country directory and php files to maintain array's
	 * @param String $fileName file to be created
	 * @param Array $availableLanguage consists set of available languages
	 * @return void
	 */
	public function createNewFile($fileName, $availableLanguage)
	{
		$this->_filePathName = $fileName;
		$this->_language = $availableLanguage;
		
		foreach ($availableLanguage as $l)
		{
			if ($l != "")
			{
				if (! file_exists('./' . $l))
				{
					mkdir('./' . $l, 0777);
				}
				$ext = $this->findFileextension($fileName);
				if ($ext == 'php')
				{
					$fh = @fopen('./' . $l . '/' . $fileName, "a");
				} else
				{
					$fh = @fopen('./' . $l . '/' . $fileName . '.php', "a");
				}
				$contents = '<?';
				@fwrite($fh, $contents);
				@fclose($fh);
			}
		}
	}

	/**
	 * deleteFile
	 * this is used to delete specified php file
	 * @param String $file file to be deleted
	 * @param Array $language consists set of available languages
	 * @return void
	 */
	public function deleteFile($file, $language)
	{
		$arrayLang = split('\|', $language);
		$this->_language = $arrayLang;
		$this->_filePathName = $file;
		foreach ($arrayLang as $country)
		{
			if ($country != "")
			{
				unlink('./' . $country . '/' . $file);
			}
		}
	}

	/**
	 * modifyFiles
	 * this is used to update specified php file
	 * @param String $filename file to be updated
	 * @param Array $language consists set of available languages
	 * @param String $string string to be inserted into the file
	 * @return void
	 */
	public function modifyFiles($language, $filename, $string, $perm)
	{
		$fh = @fopen("./" . $language . "/" . $filename, $perm);
		@fwrite($fh, $string);
		@fclose($fh);
	}

	/**
	 * deleteArray
	 * this is used to delete specified array from the specified php file
	 * @param String $filename php file to be in which array to be searched
	 * @param Array $countryLang consists set of available languages
	 * @param String $nodeNameDeleted array/simple variable name to be deleted from the php file
	 * @param String $type type of variable array/s
	 * @return void
	 */
	public function deleteArray($filename, $countryLang, $nodeNameDeleted, $type)
	{
		$arrayLang = split('\|', $countryLang);
		$this->_language = $countryLang;
		$this->_filePathName = $filename;
		
		if ($type == 'Array')
			$pos2 = ');'; else
			$pos2 = '";';
		
		foreach ($arrayLang as $country)
		{
			$i = 0;
			if ($country != "")
			{
				$string[$i] = file_get_contents('./' . $country . '/' . $filename);
				$posPT = strpos($string[$i], $nodeNameDeleted);
				if ($posPT != FALSE)
				{
					$endPos = strpos($string[$i], $pos2, $posPT);
					$lastPos = $endPos - $posPT;
					$lastPos += 2;
					$stringPT = substr_replace($string[$i], "", $posPT, $lastPos);
					$this->modifyFiles($country, $filename, $stringPT, "w");
				}
				$i ++;
			}
		}
	}

	/**
	 * insertString
	 * inserts string after the particular expression/string in a string
	 * @param String $str string to be inserted
	 * @param String $expression string to be inserted
	 * @param String $mystring string to be modified
	 * @return String $mod_string string after the modification
	 */
	public function insertString($str, $expression, $mystring)
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
	 * converts file to string 
	 * @param String $dir filepath directory name 
	 * @param String $filename file name
	 * @return String $mod_string string after the conversion
	 */
	public function filetoString($dir, $filename)
	{
		$mystring = file_get_contents('./' . $dir . '/' . $filename);
		return $mystring;
	}

	/**
	 * writeMainArray
	 * writes a specified array from xml file to the php file
	 * @param String $filename php file name
	 * @param String $arrayName array node name
	 * @param Array $attribute contains an array of attributes name and value
	 * @param String $descr array/simple node value
	 * @return void
	 */
	public function writeMainArray($filename, $arrayName, $attribute, $descr)
	{
		$arrayLang = split('\|', $attribute['language']);
		foreach ($arrayLang as $country)
		{
			if ($country != "")
			{
				if ($attribute['type'] == 'Array')
				{
					$str = "\n\n" . '// ' . $descr . "\n";
					$str .= '$' . $arrayName . ' = array(' . "\n" . ');';
					$this->modifyFiles($country, $filename, $str, "a");
				} else
				{
					$simpleStr = "\n" . '// ' . $descr . "\n";
					$simpleStr .= '$' . $arrayName . ' = "";';
					$this->modifyFiles($country, $filename, $simpleStr, "a");
				}
			}
		}
	}

	/**
	 * writeArray
	 * writes a index/simple from xml file to the specified array to the php file
	 * @param String $filename php file name
	 * @param String $arrayName array node name
	 * @param String $indexName index/simple name
	 * @return void
	 */
	public function writeArray($filename, $arrayName, $indexName)
	{
		$xml = simplexml_load_file('XMLFiles/shiva.xml');

		if ($indexName != '')
			$result = $xml->xpath('/root/' . $filename . '/' . $arrayName); else
			$result = $xml->xpath('/root/' . $filename);
		
		$type = $result[0]['type'];
		$index = $result[0]['index'];
		$availableLang = $result[0]['language'];
		$arrayLang = split('\|', $availableLang);
		
		$i = 0;
		foreach ($arrayLang as $country)
		{
			if ($country != "")
			{
				$mystring = file_get_contents('./' . $country . '/' . $filename);
				if ($type == 'Array')
				{
					$val = $result[0]->$indexName->$country;
					if ($index == 'No')
					{
						$indexName = str_replace("element", "", $indexName);
					}
					$strCountry = "\n" . '"' . $indexName . '" => "' . $val . '",';
					$expression = '$' . $arrayName . ' = array(';
					$arr_string = $this->insertString($strCountry, $expression, $mystring);
					$this->modifyFiles($country, $filename, $arr_string, "w");
				} 
				else
				{
					$val = $result[0]->$arrayName->$country;
					$expression = '$' . $arrayName . ' = "';
					$arr_string = $this->insertString($val, $expression, $mystring);
					$this->modifyFiles($country, $filename, $arr_string, "w");
				}
				$i ++;
			}
		}
	}

	/**
	 * updateArray
	 * updates the specified array details present in the php file by accessing the details from the xml file
	 * @param String $action delete/update operation
	 * @param String $filename php file name
	 * @param String $arrName array node name
	 * @param String $indexName index node name
	 * @param Array $countryLang list of countries php files to be modified
	 * @param String $index is there indexing exists or not
	 * @param String $type Array/Simple type
	 * @param Array $newValue list of new values for the each index
	 * @return void
	 */
	public function updateArray($action, $filename, $arrName, $indexName, $countryLang, $index, $type, $newValue)
	{
		if ($index == 'No')
		{
			$indexName = str_replace("element", "", $indexName);
		}
		
		foreach ($countryLang as $country)
		{
			if ($country != "")
			{
				$str = $this->filetoString($country, $filename);
				
				$posStart = strpos($str, $arrName);
				
				if ($type == 'Array')
				{
					if ($posStart != FALSE)
					{
						$midPos = strpos($str, '"' . $indexName . '"', $posStart);
						$endPos = strpos($str, '",', $midPos);
						$lastPos = $endPos - $midPos;
						$lastPos += 3;
						if ($action == 'delete')
						{
							$string = substr_replace($str, "", ($midPos - 1), $lastPos);
							$this->modifyFiles($country, $filename, $string, "w");
						}
						if ($action == "update")
						{
							$string = substr_replace($str, "", ($midPos - 1), $lastPos);
							$string = substr_replace($string, "\n" . '"' . $indexName . '" => "' . stripslashes($newValue[$country]) . '",', ($midPos - 1), 0);
							$this->modifyFiles($country, $filename, $string, "w");
						}
					}
				} else
				{
					if ($posStart != FALSE)
					{
						$midPos = strpos($str, $arrName, $posStart);
						$endPos = strpos($str, '";', $midPos);
						$lastPos = $endPos - $midPos;
						$lastPos += 3;
						if ($action == 'delete')
						{
							$string = substr_replace($str, "", ($midPos - 1), $lastPos);
							$string = substr_replace($string, "\n" . $arrName . ' = "";', ($midPos - 1), 0);
							$this->modifyFiles($country, $filename, $string, "w");
						}
						if ($action == "update")
						{
							$string = substr_replace($str, "", ($midPos - 1), $lastPos);
							$string = substr_replace($string, "\n" . $arrName . ' = "' . stripslashes($newValue[$country]) . '";', ($midPos - 1), 0);
							$this->modifyFiles($country, $filename, $string, "w");
						}
					}
				}
			}
		}
	}
	
	/**
	 * updateMainArray
	 * writes a specified array from xml file to the php file
	 * @param String $filename php file name
	 * @param String $arrayName array node name
	 * @param Array $attribute contains an array of attributes name and value
	 * @param String $descr array/simple node value
	 * @return void
	 */
	public function updateMainArray($filename, $attribute, $oldComment, $newComment)
	{
		$arrayLang = split('\|', $attribute['language']);
		foreach ($arrayLang as $country)
		{
			if ($country != "")
			{
				$str = $this->filetoString($country, $filename);
				$str = str_replace('// '.$oldComment, '// '.$newComment, $str);
				$this->modifyFiles($country, $filename, $str, "w");
			}
		}
	}

	/**
	 * setDatas
	 * 
	 * this method will set $data array as attributes for the specified node
	 */
	public function setDatas()
	{
	}
	/**
	 * getDatas
	 * 
	 * this method will return $data array as attributes for the specified node
	 * 
	 */
	public function getDatas()
	{
	}
}

?>
