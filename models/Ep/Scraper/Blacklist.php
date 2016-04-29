<?php
ob_start();
class Ep_Scraper_Blacklist{

 public function stopwords()
   {
	   $filename = "/home/sites/site4/web/BO/documents/stopwords_scraper.txt";
       $filehandle = fopen($filename, "r");	   
       if(filesize($filename)>1)
       {
           $contents = fread($filehandle, filesize($filename));

           fclose($filehandle);
           //echo $contents;
           $file_array=explode(" ",trim($contents));
           //	 preg_replace('/\s+/', ' ', $text)
           $op=str_replace(' ',', ',preg_replace('/\s+ /', ' ',implode(" ",array_unique($file_array)))) ;
           $op_pass="<font color=red>".wordwrap($op, 200, "<br />\n")."</font>";
           $op_new=str_replace(',',' ',$op);
           
       }
       $op_new=str_replace('  ',', ',$op_new);			 
		return  utf8_encode(stripslashes($op_new));
        //$this->_view->stopwords = stripslashes($op_new);
        //$this->_view->render("proofread_stopwords");
   }
   public function getstopwords()
   {
       
	   $file_array=array();
	   
	   $filename = "/home/sites/site4/web/BO/documents/stopwords_scraper.txt";
	   
       $filehandle = fopen($filename, "r");	   
       if(filesize($filename)>1)
       {
           $contents = fread($filehandle, filesize($filename));

           fclose($filehandle);
           //echo $contents;
           $file_array=explode(" ",trim($contents));
		}       
		return $file_array;        
   }
    ///////////adding the words to stop words to omit the words from key word density///////////////
    public function addstopword($word)
    {
		$filename = "/home/sites/site4/web/BO/documents/stopwords_scraper.txt";		
		file_put_contents($filename," ".str_replace('-',' ',$this->utf8dec($word)),FILE_APPEND);		
        $filehandle = fopen($filename, "r");
       if(filesize($filename)>1)
       {
           $contents = fread($filehandle, filesize($filename));
           fclose($filehandle);

           $file_array=explode(" ",trim($contents));           
           $op=str_replace(' ',', ',preg_replace('/\s+ /', ' ',implode(" ",array_unique($file_array)))) ;
           $op_pass="<font color=red>".wordwrap($op, 200, "<br />\n")."</font>";
           $op_new=str_replace(',',' ',$op);           
           file_put_contents($filename," ".$op_new." ");
       }
       
        $op_new=str_replace('  ',', ',$op_new);
		echo utf8_encode(stripslashes($op_new));

       exit;
    }
    ///////////removing the words from stop words to omit the words from key word density///////////////
    public function removestopword()
    {
       $filename = "/home/sites/site4/web/BO/documents/stopwords_scraper.txt";
       $filehandle = fopen($filename, "r");
       if(filesize($filename)>1)
       {
           $contents = fread($filehandle, filesize($filename));
           fclose($filehandle);
           //echo $contents;
           $file_array=explode(" ",trim($contents));
           //print_r($file_array);exit;
		   	$str=explode(" ",str_replace('-',' ',$this->utf8dec($_GET['filter'])));
			
           //echo "<br/>";
           //print_r($str);
           //echo count($file_array);
           for($i=0;$i<count($str);$i++)
           {
               //$op_new=str_replace($str[$i],' ', $op_new );
               //echo $str[$i]."";
               for($j=0;$j<count($file_array)+count($str);$j++)
               {
                   //echo $file_array[$j]."<br/>";
                   if($str[$i]==$file_array[$j])
                   {
                       //echo $str[$i].":".$file_array[$j]."</br>";
                       unset($file_array[$j]);
                   }
               }
           }
           
           $op=str_replace(' ',',',preg_replace('/\s+ /', ' ',implode(" ",array_unique($file_array)))) ;
           //echo "<font color=red>$op</font>";
           $op_new=str_replace(',',' ',$op);

           $str=explode(" ",str_replace('-',' ',$_GET['filter']));

           /*for($i=0;$i<count($str);$i++)
           {
               //$op_new=str_replace($str[$i],' ', $op_new );
               for($j=0;$j<count($file_array);$j++)
               {
                   if($str[$i]==$file_array[$j])
                   {
                       unset($file_array[$j]);
                   }
               }
           }*/
           file_put_contents($filename," ".$op_new);
       }
		$op_new=str_replace('   ',', ',$op_new);
		$op_new=str_replace('  ',', ',$op_new);
		$op_new=str_replace(' ',', ',$op_new);
		$op_new=str_replace(',,',', ',$op_new);
	   echo utf8_encode(stripslashes($op_new));
       exit;
    }	
	public function utf8dec($s_String)
    {
       $s_String=str_replace("e&#769;","&eacute;",$s_String);
		$s_String=str_replace("E&#769;","&Eacute;",$s_String);
        $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
        return substr($s_String, 0, strlen($s_String)-1);
    }
}	
?>
	