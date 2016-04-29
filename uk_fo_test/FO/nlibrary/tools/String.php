<?php

class String
{
	//Private properties
	var $string;             //current string value
	
/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
	function String($string="")
	{
		//Initialization of properties
		$this->_setString($string);
	}
	function getString()
	{
		return $this->string;
	}
	function equal($string)
	{
		if ($this->getString() == $string)
		return true;
		return false;
	}
	function isNull()
	{
		if (trim($this->getString()) == "")
		return true;
		return false;	
	}
	//convert the String in Alphanumeric String
	function convertToFileName()
	{
		$mot=strtolower($this->_getString());
		$mot=str_replace("'","_",$mot);
		$mot=str_replace("\"","_",$mot);
		$mot=str_replace("’","_",$mot);
		$mot=str_replace("(","_",$mot);
		$mot=str_replace(")","_",$mot);
		$mot=str_replace("[","_",$mot);
		$mot=str_replace("]","_",$mot);
		$mot=str_replace("{","_",$mot);
		$mot=str_replace("}","_",$mot);
		$mot=str_replace("«","_",$mot);
		$mot=str_replace("»","_",$mot);	
		$mot=str_replace("“","_",$mot);
		$mot=str_replace("”","_",$mot);
		$mot=str_replace("&","_",$mot);
		$mot=str_replace(",","_",$mot);
		$mot=str_replace(";","_",$mot);
		$mot=str_replace("/","_",$mot);
		$mot=str_replace("\\","_",$mot);
		$mot=str_replace("`","_",$mot);
		$mot=str_replace("<","_",$mot);
		$mot=str_replace(">","_",$mot);
		$mot=str_replace(":","_",$mot);
		$mot=str_replace("*","_",$mot);
		$mot=str_replace("|","_",$mot);
		$mot=str_replace("?","_",$mot);
		$mot=str_replace("‘","_",$mot);
		$mot=str_replace("-","_",$mot);
		$mot=str_replace("–","_",$mot);
		$this->_setString($mot);
	}
	//return the String splitted by word in an array field
	function getArrayKeyword($size=4,$isNum=1,$maxSize=10)
	{
		$this->replace("-"," ");
		$mots=split(" ",$this->_getString());
		$MOT = array();
		if ($isNum)
		{
			for ($z = 0;$z<count($mots);$z++)
				if	(strlen($mots[$z]) > $size || ereg("^[0-9]+$", $mots[$z]) )
					$MOT[] = str_replace(" ","",$mots[$z]);
		}
		else
		{
			for ($z = 0;$z<count($mots);$z++)
				if	(strlen($mots[$z]) > $size)
					$MOT[] = str_replace(" ","",$mots[$z]);
		}		
		if(count($MOT))array_splice($MOT,$maxSize);	
		return $MOT;
	}
	//return the String splitted by word in an array field
	function getArrayKeywordBis($size=4,$isNum=1,$maxSize=10)
	{
		$this->replace("-"," ");
		$mots=split(" ",$this->_getString());
		
		if ($isNum)
		{
			for ($z = 0;$z<count($mots);$z++)
				if	(strlen($mots[$z]) > $size || ereg("^[0-9]+$", $mots[$z]) )
					$MOT[] = str_replace(" ","",$mots[$z]);
		}
		else
		{
			for ($z = 0;$z<count($mots);$z++)
				if	(strlen($mots[$z]) > $size)
					$MOT[] = str_replace(" ","",$mots[$z]);
		}		
		array_splice($MOT,$maxSize);	
		return $MOT;
	}	
	//limit words number on a line
	function limitWord($nbWordMax)
	{
		$array = $this->getArrayKeyword(0);
		foreach($array as $a)
		{
			$i++;
			if(($i%$nbWordMax)==0)
			$word[] = '<br/>';
			$word[] = "$a ";
		}
		$this->_setString(implode(" ",$word));
	}

	//return the String splitted by word in an array field
	function getKeyword($size=0,$isNum=1,$glue=" ",$needle="")
	{
		$this->_replaceHtmlCaracters();
		$this->_fuckWord();
		$this->_replaceAH();
		$this->_replaceAC();
		$this->_replaceAA();
		$this->_fuckTheBreaks();
		if(is_array($needle))$this->removeWord($needle);
		$mots = $this->getArrayKeyword($size,$isNum,1000);
		return strtolower(implode($glue,$mots));
	}
	//return the String splitted by word in an array field
	function getKeyword2($size=0,$isNum=1,$glue=" ",$needle="")
	{
		$this->_replaceHtmlCaracters();
		$this->_fuckWord();
		$this->_replaceAH();
		$this->_replaceAA();
		$this->_fuckTheBreaks();
		if(is_array($needle))$this->removeWord($needle);
		$mots = $this->getArrayKeyword($size,$isNum,1000);
		return strtolower(implode($glue,$mots));
	}
	
	//return the String splitted by word in an array field
	//Added by Ram.
	function getKeyword2_ram($size=0,$isNum=1,$glue=" ",$needle="",$limit)
	{
		$this->_replaceHtmlCaracters();
		$this->_fuckWord();
		$this->_replaceAH();
		$this->_replaceAA();
		$this->_fuckTheBreaks();
		if(is_array($needle))$this->removeWord($needle);
		$mots = $this->getArrayKeyword($size,$isNum,$limit);
		return strtolower(implode($glue,$mots));
	}
	//return the String splitted by word in an array field
	function getKeywordBis($size=2,$isNum=1,$glue=" ")
	{
		$this->_replaceAA();
		$this->_fuckTheBreaks();
		$mots = $this->getArrayKeyword($size,$isNum);
		return strtolower(implode($glue,$mots));
	}	
	//limit the size of the string
	function replaceToUrl($size=0,$isNum=1,$needle="")
	{
		$this->_replaceHtmlCaracters();
		$this->_fuckWord();
		$this->_replaceAH();		
		$this->_replaceAC();
		$this->_replaceAB();
		$this->_fuckTheBreaks();
		if(is_array($needle))$this->removeWord($needle);
		$mots = $this->getArrayKeyword($size,$isNum);
		if($mots=="")$mots = $this->getArrayKeyword($size,$isNum);
		return strtolower(implode("-",$mots));
	}
	//limit the size of the string
	function limitSize($size)
	{
		$char = substr(trim($this->_getString()),0,$size);
		$s = strlen(strrchr($char," "));
		if(!$s)strlen(strrchr($char,"+"));
		$this->_setString(substr($char,0,$size-$s));
	}
	//limit the size of the string and if necessary add 3 points at the end 
	function limitSizeAdd3Points($size)
	{
		$length = strlen($this->_getString());
		$this->limitSize($size);
		if( strlen($this->_getString()) != $length)
		$this->_setString($this->_getString()."...");
	}
	//convert the string in compatible keyword string format
	function replaceToKeyword()
	{
		$mot=stripslashes($this->_getString());
		$mot=trim($mot);
		$mot=eregi_replace(" ","+",$mot);	
		$this->_setString($mot);
	}
	//check if the string value is alphanumeric
	function isAlnum()
	{
		$this->_replaceAC();
		$this->_replaceAD();
		if (ctype_alnum($this->_getString()))return true;
		return false;
	}
	//check if the string value is alphabetic
	function isAlpha()
	{
		$this->_replaceAC();
		$this->_replaceAD();
		if (ctype_alpha($this->_getString()))return true;
		return false;
	}
	//check if the string value is numeric
	function isNumeric()
	{
		if (is_numeric($this->_getString()))return true;
		return false;
	}
	//check if the string length is equal to the parameter value
	function isLength($length)
	{
		if (strlen($this->_getString())==$length)return true;
		return false;	
	}
	//check if the string length is superior to the parameter value
	function isSuperior($length)
	{
		if (strlen($this->_getString())>$length)return true;
		return false;
	}
		//check if the string length is superior to the parameter value
	function isInferior($length)
	{
		if (strlen($this->_getString())<$length)return true;
		return false;
	}
	//check if the email adress are valid
	function isEmail()
	{
 
        // on teste d'abord la syntaxe de l'adresse
        if (eregi("^[_\.0-9a-z\-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$",$this->_getString()))
        {
        	return true;
                /*if ( getmxrr ( $domain, $mxhosts, $weight ) )
                {
                        $nbremx = count($mxhosts);
                        asort ($weight);
                        $poids = current($weight);
                        // $iter nous servira à compter le nombre de serveurs testés 
                        // avant d'obtenir une réponse SMTP positive
                        $iter = 0;
                        // on se cale sur $weight trié pour faire un tour dans 
                        // le bon sens des serveurs mx
                        while (list($i, $val) = each ($weight))
                        {
                                $fp = fsockopen ( $mxhosts[$i], 25, $errno, $errstr, 30);
                                if ( $fp )
                                {
                                        /*$s = 0;
                                        $out = "";
                                        $out = fgets ( $fp, 512 );
                                        if ( ereg ( "^220", $out ) )
                                        {
                                                // on a obtenu une réponse du serveur, on tente de 
                                                // discuter avec lui en SMTP
                                                fputs ( $fp, "HELO ".$_SERVER["SERVER_NAME"]."\r\n" );
                                                $output = fgets ( $fp, 512 );
                                                fputs ( $fp, "MAIL FROM: <info@".$domaine_sender.">\r\n" );
                                                $output = fgets ( $fp, 512 );
                                                fputs ( $fp, "RCPT TO: <".$this->_getString().">\r\n" );                         
                                                $output = fgets ( $fp, 512 );
                                                if ( ereg ( "^250", $output ) )
                                                {
                                                        // le serveur nous répond et connait notre destinataire
       							return TRUE;
                                                }
                                                fputs ( $fp, "QUIT\r\n" );
                                                fgets ($fp,512);
                                                fclose( $fp );
                                        }
					return true;
                                }

                                $iter++;
                        } // fin de la boucle sur le tableau des serveurs
                }*/
        }
		return false;
	}
	function isPhoto()
	{
		$list_extension = array(".jpg",".bmp",".gif",".JPG",".BMP",".GIF");
	
		$extension = strrchr($this->_getString(),".");
	
		foreach($list_extension as $ext)
			if ($extension == $ext)
				return true;

		return false;
	}
	function isDocument()
	{
		$list_extension = array(".zip",".rar",".tar",".gz",".ace",".mp3",".ogg",".wav",".wma",".cda",".wmv",".mpeg",".mpg",".avi",".asf",".jpg",".jpeg",".gif",".png",".ico",".divx",".html",".htm",".pdf",".doc",".docx",".rtf",".ppt",".pptx",".pps",".zip",".txt",".odt",".xls",".xlsx",".odp",".sxw",".sxi",".wps");
	
		$extension = strrchr($this->_getString(),".");
	
		foreach($list_extension as $ext)
			if ($extension == $ext)
				return true;

		return false;
	}
	function isImage()
	{
		$list_extension = array(".gif",".jpg",".png");
	
		$extension = strrchr($this->_getString(),".");
	
		foreach($list_extension as $ext)
			if ($extension == $ext)
				return true;

		return false;
	}
	function extractExtension()
	{
		return strrchr($this->_getString(),".");
	}
	function replace($needle,$haystack)
	{
		$this->_setString(str_replace($needle,$haystack,$this->_getString()));
	}
	function prefixIP($nb=2)
	{
		$cl = split("\.",$this->getString());
		$separator = "";
		for($i=0;$i<$nb;$i++)
		{
			$IP .= $separator.$cl[$i];
			$separator = ".";
		}
		$this->_setString($IP);	
	}
	function randomstring($len=15)
	{
		$str = "";
		$i = 0;
		srand(date("s") + srand(50) );
		while($i<$len)
		{
			$str.=chr((rand()%26)+97);
			$i++;
		}
	
		$str=$str.substr(uniqid (""),0,22);
		return substr($str, -$len, strlen($str));
	}
    function getXmlValidFormat()
	{
		$this->replace(";",",");
		$this->replace("<","");
		$this->replace(">","");
		$this->replace("&","et");
		$this->_fuckWord();
	}
   	function stripTags()
	{
		$this->_setString(strip_tags($this->getString()));
	}
   	function exist($string)
	{
		if(eregi($string,$this->getString()))
		return true;
		return false;
	}

	function decode_utf8($string)
    {		
    	$string = $this->getString();
		//$string = utf8_decode($string);
		
    	$source = array ("â€™","Ã±","Ã¼","Ã©","Ã¨","Ãª","Ã»","Ã¢","Ã¶","Ã€","Ã®","Ã«","Ã§","Ã‰","Ã ","Ã´","Â°","Ã¹","Ã¯","Å“");
 		$out = array ("’","ñ","ü","é","è","ê","û","â","ô","à","î","ë","ç","é","à","ô","°","ù","ï","œ",);
    	
 		$string = str_replace($source,$out,$string);
 		    	
 		$source = array("[wt]","[t]","[cw]","[e]","[ao]","[ap]","[ww]");
    	$out = array("´","–","’","&","«","»","‘");
    	
    	$string = str_replace($source,$out,$string);

		for($i=0;$i<strlen($string)-4;$i++)
			if($string[$i]=="?")$string[$i]="'";
 		
		$this->_setString($string);			
		return $string;
    }
	
    
    function encodeXmlEntities()
    {
    	$xml = $this->getString();
    	
        $xml = str_replace(array('ü', 'Ü', 'ö',
                                 'Ö', 'ä', 'Ä',
                                 'ß', '<', '>',
                                 '"'
                                ),
                           array('&#252;', '&#220;', '&#246;',
                                 '&#214;', '&#228;', '&#196;',
                                  '&#223;', '&lt;', '&gt;',
                                  '&quot;'
                                ),
                           $xml
                          );

        $xml = preg_replace(array("/\&([a-z\d\#]+)\;/i",
                                  "/\&/",
                                  "/\#\|\|([a-z\d\#]+)\|\|\#/i",
                                  "/([^a-zA-Z\d\s\<\>\&\;\.\:\=\"\-\/\%\?\!\'\(\)\[\]\{\}\$\#\+\,\@_])/e"
                                 ),
                            array("#||\\1||#",
                                  "&amp;",
                                  "&\\1;",
                                  "'&#'.ord('\\1').';'"
                                 ),
                            $xml
                           );

 		$this->_setString($xml);
    }
	//rajoute un zéro si décimal non complète
	function addZero($number)
	{
		$n = explode(".",$number);
		//si décimal
		if(isset($n[1]))
		{	
			if ((strlen($number) - strpos($number, ".")) == 2)
				$number .= "0";
		}
		return $number;
	}
	//renvoi les mots en gras du texte
	function getBoldWord()
	{
		$string = $this->getString();
		$arraystring = split("<b>",$string);
		
		foreach($arraystring as $s)
		{
			$str = new String($s);
			$str->_replacePunctuation();
			
			if(!$str->isNull())
			{
				if(eregi("</b>",$str->getString()))
				{
					$arrayString = split("</b>",$str->getString());
					$word[] = $arrayString[0];
				}
			}
		}
		return $word;
	}
	//met les mot contenu dans needle en gras dans String
	function setBoldWord($needle)
	{
		$string = $this->getString();
		if(count($needle)>0 && is_array($needle))
		{
				foreach($needle as $n)
			{
				if(eregi("[[:<:]]".$n."[[:>:]]",$string))
					$string = eregi_replace("[[:<:]]".$n."[[:>:]]","<b>$n</b>",$string);
				else
					$string = eregi_replace($n,"<b>$n</b>",$string);		
			}
		}
		$this->_setString($string);
	}
	//met les mot contenu dans needle en gras dans String
	function setItalicWord($needle)
	{
		$string = $this->getString();
		
		foreach($needle as $n)
		{
			$authorize = true;
			if(is_numeric($n))if($n<300)$authorize = false;
			
			if($authorize)
			{
				if(eregi("[[:<:]]".$n."[[:>:]]",$string))
					$string = eregi_replace("[[:<:]]".$n."[[:>:]]","<i>$n</i>",$string);
				else
					$string = eregi_replace($n,"<i>$n</i>",$string);	
			}	
		}
		$this->_setString($string);
	}
	
	//supprime les mot contenus dans needle dans String
	function removeWord($needle)
	{
		$string = $this->getString();
		$string1 = explode(" ",strtolower($string));

		$separator = "";
		foreach($string1 as $n)
		{
			if($n)
			{
				if(!in_array(strtolower($n),$needle))
					$string2 .= $separator.$n;
					$separator = " ";
			}
		}

		$this->_setString($string2);
	}

	public function toString()
	{
		return $this->string;
	}
/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/

	//replace accentuated characters in the string value
	function _replaceAC()
	{
		$char = stripslashes($this->_getString());
		$char = strtr($char,
		"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", 
		"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn"); 
		$char=trim($char);
		
		$this->_setString($char);
	}
	
	//FOR PD LINK
	function _replaceAB()
	{
		$char = stripslashes($this->_getString());
		$char=str_replace("'"," ",$char);
		$char=str_replace("\""," ",$char);
		$char=str_replace("’"," ",$char);
		$char=str_replace("("," ",$char);
		$char=str_replace(")"," ",$char);
		$char=str_replace("["," ",$char);
		$char=str_replace("]"," ",$char);
		$char=str_replace("{"," ",$char);
		$char=str_replace("}"," ",$char);
		$char=str_replace("«"," ",$char);
		$char=str_replace("»"," ",$char);	
		$char=str_replace("“"," ",$char);
		$char=str_replace("”"," ",$char);
		$char=str_replace("&"," ",$char);
		$char=str_replace(","," ",$char);
		$char=str_replace(";"," ",$char);
		$char=str_replace("/"," ",$char);
		$char=str_replace("\\"," ",$char);
		$char=str_replace("`"," ",$char);
		$char=str_replace("<"," ",$char);
		$char=str_replace(">"," ",$char);
		$char=str_replace(":"," ",$char);
		$char=str_replace("*"," ",$char);
		$char=str_replace("|"," ",$char);
		$char=str_replace("?"," ",$char);
		$char=str_replace("‘"," ",$char);
		$char=str_replace("%"," ",$char);
		$char = str_replace("."," ",$char);
		$char = str_replace("…"," ",$char);
		$char = str_replace("°"," ",$char);
		$char = str_replace("’"," ",$char);
		$char = str_replace("¿"," ",$char);
		$char = str_replace("´"," ",$char);
		$char = str_replace("®"," ",$char);
		$char = str_replace("™"," ",$char);
		$char = str_replace("‚"," ",$char);
		$char = str_replace("§"," ",$char);
		$char = str_replace("€"," ",$char);
		$char = str_replace("$"," ",$char);
		$char = str_replace("+"," ",$char);
		$char = str_replace("!"," ",$char);
		$char = str_replace("!"," ",$char);
		$char = str_replace("¡"," ",$char);
		$char = str_replace("×"," ",$char);
		$char = str_replace("©"," ",$char);
		$char = str_replace("º"," ",$char);
		$char = str_replace("ª"," ",$char);
		$char = str_replace("µ"," ",$char);
		$char = str_replace("@","a",$char);
		$char = str_replace("-"," ",$char);
		$char = str_replace(" — "," ",$char);
		$char = str_replace("—"," ",$char);
		$char = str_replace("="," ",$char);
		//$char = str_replace(" s "," ",$char);
		$char=trim($char);		
		$this->_setString($char);
	}
	
	//FOR VDS LINK
	function _replaceAA()
	{
		$char = stripslashes($this->_getString());
		$char=str_replace("'"," ",$char);
		$char=str_replace("\""," ",$char);
		$char=str_replace("’"," ",$char);
		$char=str_replace("("," ",$char);
		$char=str_replace(")"," ",$char);
		$char=str_replace("["," ",$char);
		$char=str_replace("]"," ",$char);
		$char=str_replace("{"," ",$char);
		$char=str_replace("}"," ",$char);
		$char=str_replace("«"," ",$char);
		$char=str_replace("»"," ",$char);	
		$char=str_replace("“"," ",$char);
		$char=str_replace("”"," ",$char);
		$char=str_replace("&"," ",$char);
		$char=str_replace(","," ",$char);
		$char=str_replace(";"," ",$char);
		$char=str_replace("/"," ",$char);
		$char=str_replace("\\"," ",$char);
		$char=str_replace("`"," ",$char);
		$char=str_replace("<"," ",$char);
		$char=str_replace(">"," ",$char);
		$char=str_replace(":"," ",$char);
		$char=str_replace("*"," ",$char);
		$char=str_replace("|"," ",$char);
		$char=str_replace("?"," ",$char);
		$char=str_replace("‘"," ",$char);
		$char=str_replace("%"," ",$char);
		$char = str_replace("."," ",$char);
		$char = str_replace("…"," ",$char);
		$char = str_replace("°"," ",$char);
		$char = str_replace("’"," ",$char);
		$char = str_replace("¿"," ",$char);
		$char = str_replace("´"," ",$char);
		$char = str_replace("®"," ",$char);
		$char = str_replace("™"," ",$char);
		$char = str_replace("‚"," ",$char);
		$char = str_replace("§"," ",$char);
		$char = str_replace("€"," ",$char);
		$char = str_replace("$"," ",$char);
		$char = str_replace("+"," ",$char);
		$char = str_replace("!"," ",$char);
		$char = str_replace("!"," ",$char);
		$char = str_replace("¡"," ",$char);
		$char = str_replace("×"," ",$char);
		$char = str_replace("©"," ",$char);
		$char = str_replace("º"," ",$char);
		$char = str_replace("ª"," ",$char);
		$char = str_replace("µ"," ",$char);
		$char = str_replace(" s "," ",$char);
		$char = str_replace("@","a",$char);
		$char=trim($char);		
		$this->_setString($char);
	}
	//replace original characters in the string value
	function _replaceAD()
	{
		$char = stripslashes($this->_getString());
		$char=str_replace("-","",$char);
		$char=str_replace(" ","",$char);
		$char=str_replace("'","",$char);
		$char=trim($char);		
		$this->_setString($char);
	}
	
	//replace original characters in the string value	
	function _replaceAH()
	{
		$char = stripslashes($this->_getString());
		$char = str_replace("&#333;"," ",$char);
		$char = str_replace("&#42;"," ",$char);
		$char = str_replace("&#1072;","a",$char);
		$char = str_replace("&#8594;"," ",$char);
		$char = str_replace("e&#769;","é",$char);
		$char = str_replace("o&#770;","ô",$char);
		$char = str_replace("&#8208;"," ",$char);
		$char = str_replace("&#305;","i",$char);
		$char = str_replace("&#8232;"," ",$char);
		$char = str_replace("&#1071;","R",$char);
		$char = str_replace("&#252;","ü",$char);
		$char = str_replace("&#220;","Ü",$char);
		$char = str_replace("&#257;","a",$char);
		$char = str_replace("e&#768;","è",$char);
		$char = str_replace("c&#807;","ç",$char);
		$char = str_replace("a&#768;","à",$char);
		$char = str_replace("&#946;","ss",$char);
		$char = str_replace("&#769;"," ",$char);
		$char = str_replace("&#8233;"," ",$char);
		$char = str_replace("&#65279;"," ",$char);
		$char = str_replace("&#700;"," ",$char);
		$char = str_replace("&#322;"," ",$char);
		$char = str_replace("&#61636;"," ",$char);
		$char = str_replace("&#10146;"," ",$char);
		$char = str_replace("&#61657;"," ",$char);
		$char = str_replace("&#61645;"," ",$char);
		$char = str_replace("&#61611;"," ",$char);
		$char = str_replace("&#61683;"," ",$char);
		$char = str_replace("&#8800;"," ",$char);
		$char = str_replace("&#61662;"," ",$char);
		$char = str_replace("&#8722;"," ",$char);
		$char = str_replace("&#8710;"," ",$char);
		$char = str_replace("&#61607;"," ",$char);
		$char = str_replace("&#61623;"," ",$char);
		$char = str_replace("&#146;"," ",$char);
		$char = str_replace("&#8727;"," ",$char);
		$char = str_replace("&#8195;"," ",$char);
		$char = str_replace("&#61664;"," ",$char);
		$char = str_replace("&#65306;"," ",$char);
		$char = str_replace("&#963;"," ",$char);
		$char = str_replace("&#960;"," ",$char);
		$char = str_replace("&#61552;"," ",$char);
		$char = str_replace("&#61692;"," ",$char);
		$char = str_replace("&#947;"," ",$char); 
		$char = str_replace("&#8658;"," ",$char);
		$char = str_replace("&#8776;"," ",$char);
		$char = str_replace("&#935;","X",$char);
		$char = str_replace("&#1030;","I",$char);
		$char = str_replace("&#8213;"," ",$char);
		$char = str_replace("&#314;","l",$char);
		$char = str_replace("&#9642;"," ",$char);
		$char = str_replace("&#9472;"," ",$char);
		$char = str_replace("&#955;"," ",$char); 
		$char = str_replace("&#956;"," ",$char); 
		$char = str_replace("&#61472;"," ",$char);
		$char = str_replace("&#8805;"," ",$char);
		$char = str_replace("&#61558;"," ",$char);
		$char = str_replace("&#972;"," ",$char);
		$char = str_replace("&#8212;"," ",$char);
		$char = str_replace("&#8217;"," ",$char);
		$char = str_replace("&#339;","oe",$char);
		$char = str_replace("&#8200;"," ",$char);
		$char = str_replace("&#299;","i",$char);
		$char = str_replace("&#8453;"," ",$char);
		$char = str_replace("&#61680;"," ",$char);
		$char = str_replace("&#61485;"," ",$char);
		$char = str_replace("&#8680;"," ",$char);
		$char = str_replace("&#61656;"," ",$char);
		$char = str_replace("&#61674;"," ",$char);
		$char = str_replace("&#61609;"," ",$char);
		$char = str_replace("&#56256;"," ",$char);
		$char = str_replace("&#56451;"," ",$char);
		$char = str_replace("&#8259;"," ",$char);
		$char = str_replace("&#61548;"," ",$char);
		$char = str_replace("&#949;","e",$char);
		$char = str_replace("&#8804;"," ",$char);
		$char = str_replace("&#61668;"," ",$char);
		$char = str_replace("&#61672;"," ",$char);
		$char = str_replace("&#9679;"," ",$char);
		$char = str_replace("&#10132;"," ",$char);
		$char = str_replace("&#61635;"," ",$char);
		$char = str_replace("&#61626;"," ",$char);
		$char = str_replace("&#945;","a",$char);
		$char = str_replace("&#920;","T",$char);
		$char = str_replace("&#61553;"," ",$char);
		$char = str_replace("&#8230;"," ",$char);
		$char = str_replace("&#9658;"," ",$char);
		$char = str_replace("&#8211;"," ",$char);
		$char = str_replace("&#8216;"," ",$char);
		$char = str_replace("&#8220;"," ",$char);
		$char = str_replace("&#8221;"," ",$char);
		$char = str_replace("&#8364;"," ",$char);
		$char = str_replace("&#61616;"," ",$char);
		$char = str_replace("&#61507;"," ",$char);
		$char = str_replace("&#61508;"," ",$char);
		$char = str_replace("&#56503;"," ",$char);
		$char = str_replace("&#345;"," ",$char);
		$char = str_replace("&#916;"," ",$char); 
		$char = str_replace("&#61482;"," ",$char);
		$char = str_replace("&#27743;&#25144;"," ",$char);
		$char = str_replace("&#26481;&#20140;"," ",$char);
		$char = str_replace("&#61660;"," ",$char);
		$char = str_replace("&#278;"," ",$char);
		$char = str_replace("&#61538;"," ",$char);
		$char = str_replace("&#9702;"," ",$char);
		$char = str_replace("&#65532;"," ",$char);
		$char = str_replace("&#56473;"," ",$char);
		$char = str_replace("&#56510;"," ",$char);
		$char = str_replace("&#1056;","P",$char);
		$char = str_replace("&#281;","é",$char);
		$char = str_replace("&#321;","L",$char);
		$char = str_replace("&#328;","ñ",$char);
		$char = str_replace("&#64257;"," ",$char);
		$char = str_replace("&#61523;"," ",$char);
		$char = str_replace("&#61615;"," ",$char);
		$char = str_replace("&#962;","ç",$char);
		$char = str_replace("&#8223;"," ",$char);
		$char = str_replace("&#8242;"," ",$char);
		$char = str_replace("&#966;"," ",$char);
		$char = str_replace("&#65292;"," ",$char);
		$char = str_replace("&#8260;"," ",$char);
		$char = str_replace("u&#768;","ú",$char);
		$char = str_replace("&#776;"," ",$char);
		$char = str_replace("&#363;","u",$char);
		$char = str_replace("&#263;","c",$char);
		$char = str_replace("&#379;","Z",$char);
		$char = str_replace("&#1040;","A",$char);
		$char = str_replace("&#1061;","X",$char);
		$char = str_replace("&#61531;"," ",$char);
		$char = str_replace("&#61533;"," ",$char);
		$char = str_replace("&#61546;"," ",$char);
		$char = str_replace("&#61547;"," ",$char);
		$char = str_replace("&#61549;"," ",$char);
		$char = str_replace("&#61550;"," ",$char);
		$char = str_replace("&#61551;"," ",$char);
		$char = str_replace("&#61554;"," ",$char);
		$char = str_replace("&#61555;"," ",$char);
		$char = str_replace("&#952;"," ",$char);
		$char = str_replace("&#61474;"," ",$char);
		$char = str_replace("&#9827;"," ",$char);
		$char = str_replace("&#61486;"," ",$char);
		$char = str_replace("&#61614;"," ",$char);
		$char = str_replace("&#61590;"," ",$char);
		$char = str_replace("&#8243;"," ",$char);
		$char = str_replace("&#8470;"," ",$char);
		$char = str_replace("&#8206;"," ",$char);
		$char = str_replace("&#8207;"," ",$char);
		$char = str_replace("&#8209;"," ",$char);
		$char = str_replace("&#9633;"," ",$char);
		$char = str_replace("&#61600;"," ",$char);
		$char = str_replace("&#61496;"," ",$char);
		$char = str_replace("&#7915;","ù",$char);
		$char = str_replace("&#61504;"," ",$char);
		$char = str_replace("&#61596;"," ",$char);
		$char = str_replace("&#61575;"," ",$char);
		$char = str_replace("&#61599;"," ",$char);
		$char = str_replace("&#8532;"," ",$char);
		$char = str_replace("&#61613;"," ",$char);
		$char = str_replace("&#275;","e",$char);
		$char = str_replace("&#61586;"," ",$char);
		$char = str_replace("&#9675;","o",$char);
		$char = str_replace("&#56319;&#56321;"," ",$char); 
		$char = str_replace("&#268;"," ",$char);
		$char = str_replace("&#61612;"," ",$char);
		$char = str_replace("&#63728;"," ",$char);
		$char = str_replace("&#61511;"," ",$char);
		$char = str_replace("&#61473;"," ",$char);
		$char = str_replace("&#8219;"," ",$char);
		$char = str_replace("&#61691;"," ",$char);
		$char = str_replace("&#954;","k",$char);
		$char = str_replace("&#64258;","fl",$char);
		$char = str_replace("&#8531;","1/3",$char);
		$char = str_replace("&#1057;","C",$char);
		$char = str_replace("&#9830;","•",$char);
		$char = str_replace("&#65339;"," ",$char);
		$char = str_replace("&#65341;"," ",$char);
		$char = str_replace("&#61569;"," ",$char);
		$char = str_replace("&#351;"," ",$char);
		$char = str_replace("&#8199;"," ",$char);
		$char = str_replace("&#903;"," ",$char);
		$char = str_replace("&#957;"," ",$char);
		$char = str_replace("&#961;"," ",$char);
		$char = str_replace("&#1105;","ë",$char);
		$char = str_replace("&#923;"," ",$char);
		$char = str_replace("&#7879;","ê",$char);
		$char = str_replace("&#894;"," ",$char);
		$char = str_replace("&#287;","g",$char);
		$char = str_replace("&#959;","o",$char);
		$char = str_replace("&#8764;"," ",$char);
		$this->_setString($char);
	}
	//replace original characters in the string value
	function _replaceAE()
	{
		$char = stripslashes($this->_getString());		
		$char = str_replace("“",'"',$char);
		$char = str_replace("”",'"',$char);
		$char = str_replace("’","'",$char);
		$char = str_replace("‘","'",$char);
		$char = str_replace("–","-",$char);
		$char = str_replace("—","-",$char);
		$char = str_replace("…","...",$char);
		$char = str_replace("°"," ",$char);
		$char = str_replace("¿"," ",$char);
		$char = str_replace("´","'",$char);
		$char = str_replace("®"," ",$char);
		$char = str_replace("™"," ",$char);
		$char = str_replace("§"," ",$char);
		$char = str_replace("×"," ",$char);
		$char = str_replace("©"," ",$char);
		$char = str_replace("œ","oe",$char);
		$char = str_replace("Œ","Oe",$char);

		$char = str_replace("€","&#8364;",$char);
		
		// mini replacement
		$char = str_replace("½","oe",$char);
		$char = str_replace("`a","à",$char);
		
		$this->_setString($char);
	}
    function _fuckTheBreaks()
	{
		$char = stripslashes($this->_getString());
		$char=str_replace("<br>"," ",$char);
		$char=str_replace("</br>"," ",$char);
		$char=str_replace("\r\n"," ",$char);
		$char=str_replace("\n"," ",$char);
		$char=str_replace("\r"," ",$char);
		$char=str_replace("\t"," ",$char);
		$this->_setString($char);
	}
    function _fuckWord()
	{
		$this->replace("’","'");
		$this->replace("–","-");
		$this->replace("Œ","Oe");
		$this->replace("œ","oe");
		$this->replace("…","...");
		$this->replace("“","\"");
		$this->replace("”","\"");
		$this->replace("„","\"");
		$this->replace("ß","ss");
	}
    function _replacePunctuation()
	{
		$char = stripslashes($this->_getString());
		$char=str_replace(";"," ",$char);
		$char=str_replace(","," ",$char);
		$char=str_replace("."," ",$char);
		$this->_setString($char);
	}
    function _replaceHtmlCaracters()
	{
		$char = stripslashes($this->_getString());
		$char = html_entity_decode($char);
		$this->_setString($char);
	}

	// set methods
	function _setString($string)
	{
		$this->string = $string;
	}
	
	// get methods
	function _getString()
	{
		return $this->string;
	}
	
	function getridof()
	{
		$this->replace("&#8216;","\"");
		$this->replace("&#8217;","\"");	
	}
}
?>