<?php

class filecontent
{
    protected $status;
	public function __construct($file_in,$file_out='',$out_file_type='txt')
    {

        $file=pathinfo($file_in);
        $ext= $file['extension'];
		if(!$file_out)
        $file_out=$file['dirname']."/".$file['filename'].".".$out_file_type;

        if($ext=='doc')
		{
			$ret=$this->o_docToTxt($file_in, $file_out);
			$this->status=$ret;
        }
		else if($ext=='docx')
		{
			$ret=$this->o_docxToTxt($file_in, $file_out);
			$this->status=$ret;
        }    
		else if($ext=='xls')
		{
			$ret=$this->o_xlsToTxt($file_in, $file_out);
			$this->status=$ret;
        }
		else if($ext=='xlsx')
		{
			$ret=$this->o_xlsxToTxt($file_in, $file_out);
			$this->status=$ret;
        }
		else if($ext=='txt')
		{
			$this->status=1;
        }
        

    }
	public function getStatus()
	{
		return $this->status;
	}
   
    public function o_docToTxt($filein, $fileout)
    {
        $doc2txt = "/usr/bin/antiword  -m UTF-8.txt  ";
		
		$cmd = $doc2txt." ".$filein." > ".$fileout."";
         $ret = 0;
        
        if(file_exists($filein))
        {
            $text = shell_exec($cmd);
			if(file_exists($fileout) && filesize($fileout)>0)
				$ret=1;
			else
				$ret=-1;
            //$text = utf8_encode($text);
        }
        else
        {
          $ret = -1;
        }
        return $ret;
    }

    public function o_docxToTxt($path, $outpath)
    {
        if (!file_exists($path))
            return -1;
        
        $zh = zip_open($path);
        $content = "";
        while (($entry = zip_read($zh)))
        {
            $entry_name = zip_entry_name($entry);
            if (preg_match('/word\/document\.xml/im', $entry_name))
            {
                $content = zip_entry_read($entry, zip_entry_filesize($entry));
                break;
            }
        }
        $text_content = "";
        if ($content)
        {
            $xml = new XMLReader();
            $xml->XML($content);
            while($xml->read())
            {
                if ($xml->name == "w:t" && $xml->nodeType == XMLReader::ELEMENT)
                {
                    $text_content .= $xml->readInnerXML();
                    $space = $xml->getAttribute("xml:space");
                    if ($space && $space == "preserve")
                    $text_content .= " ";
                }
                if (($xml->name == "w:p" || $xml->name == "w:br" || $xml->name == "w:cr") && $xml->nodeType == XMLReader::ELEMENT)
                    $text_content .= "\n";
                if (($xml->name == "w:tab") && $xml->nodeType == XMLReader::ELEMENT)
                    $text_content .= "\t";
            }
            $text_content=($text_content);
			file_put_contents($outpath, $text_content);
            //echo $text_content."--".$path."--".$outpath;
            return 1;
        }
        return -1;
    }
	
	public function o_xlsToTxt($file_path, $outpath)
	{
		require_once 'reader.php';		
		
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('Windows-1252');
		$data->read($file_path);
		$text_content=$data->dumpTxt(TRUE,TRUE);
		if($text_content)
		{
			$text_content=strip_tags($text_content);
			$text_content=utf8_encode($text_content);
			$text_content=str_replace("&nbsp;\n","",$text_content);
			$text_content=str_replace("\t","",$text_content);
			$text_content=html_entity_decode($text_content);
			file_put_contents($outpath,$text_content);
			
			return 1;
		}	
		else
			return -1;
			
	}
	public function o_xlsxToTxt($file_path, $outpath)
	{
		require_once('simplexlsx.class.php');	
		
		$xlsx = new SimpleXLSX($file_path);
		$text_content='';
		for($j=1;$j <= $xlsx->sheetsCount();$j++){
			
			$text_content.='<table id="xlsxTable">';
			list($cols) = $xlsx->dimension($j);
			
			if(count($xlsx->rows($j))>0)
			{
				foreach( $xlsx->rows($j) as $k => $r) {
					if ($k == 0){
						$trOpen		= '<th';
						$trClose	= '</th>';
						$tbOpen		= '<thead>';
						$tbClose	= '</thead>';
					}else{
						$trOpen		= '<td';
						$trClose	= '</td>';
						$tbOpen		= '<tbody>';
						$tbClose	= '</tbody>';
					}
					$text_content.=$tbOpen;
					$text_content.= '<tr>';
					for( $i = 0; $i < $cols; $i++)
						//Display data
						$text_content.= $trOpen.'>'.( (isset($r[$i])) ? $r[$i]."\n" : '&nbsp;' ).$trClose;
					$text_content.= '</tr>';
					$text_content.= $tbClose;
				}
			}
			$text_content.= '</table>';
		}
		//echo $text_content;exit;
			
		if($text_content)
		{
			$text_content=strip_tags($text_content);
			$text_content=utf8_decode($text_content);
			$text_content=str_replace("&nbsp;\n","",$text_content);
			$text_content=str_replace("\t","",$text_content);
			$text_content=str_replace("\n\n","",$text_content);
			$text_content=html_entity_decode($text_content);
			file_put_contents($outpath,$text_content);
			
			return 1;
		}	
		else
			return -1;
			
	}
	
    public function count_words($string)
    {
        $string = htmlspecialchars_decode(strip_tags($string));
        if (strlen($string)==0)
            return 0;
        $t = array(' '=>1, '_'=>1, "\x20"=>1, "\xA0"=>1, "\x0A"=>1, "\x0D"=>1, "\x09"=>1, "\x0B"=>1, "\x2E"=>1, "\t"=>1, '='=>1, '+'=>1, '-'=>1, '*'=>1, '/'=>1, '\\'=>1, ','=>1, '.'=>1, ';'=>1, ':'=>1, '"'=>1, '\''=>1, '['=>1, ']'=>1, '{'=>1, '}'=>1, '('=>1, ')'=>1, '<'=>1, '>'=>1, '&'=>1, '%'=>1, '$'=>1, '@'=>1, '#'=>1, '^'=>1, '!'=>1, '?'=>1); // separators
            $count= isset($t[$string[0]])? 0:1;
        if (strlen($string)==1)
            return $count;
        for ($i=1;$i<strlen($string);$i++)
            if (isset($t[$string[$i-1]]) && !isset($t[$string[$i]]))
                $count++;

         return $count;
    }

}

