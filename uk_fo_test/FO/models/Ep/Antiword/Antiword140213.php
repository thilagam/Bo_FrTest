<?php
/**
 * Ep_Message_AutoEmails
 * @author Admin
 * @package Message
 * @version 1.0
 */
class Ep_Antiword_Antiword
{
    protected $content;

    public function __construct($file_in,$out_file_type='txt')
    {

        $file=pathinfo($file_in);
        $ext= $file['extension'];
        $file_out=$file['dirname']."/".$file['filename'].".".$out_file_type;

        //print_r($file_out);exit;

		if($ext=='doc')
		{
			$ret=$this->o_docToTxt($file_in, $file_out);
            if($ret)
            {
			    $content = file_get_contents($file_out);
                $content=str_replace(" "," ",$content);
            }

		}
		else if($ext=='docx')
		{
			$text=$this->o_docxToTxt($file_in, $file_out);
            //$content = file_get_contents($file_out);
            if($text)
            {
			    $content = $text;
			    $content=str_replace("  "," ",$content);
            }
		}
        else if($ext=='xls')
        {
            $text=$this->o_xlsToTxt($file_in, $file_out);
            if($text)
            {
                $content = $text;
                $content=str_replace("  "," ",$content);
            }
        }
        else if($ext=='xlsx')
        {
            $text=$this->o_xlsxToTxt($file_in, $file_out);
            if($text)
            {
                $content = $text;
                $content=str_replace("  "," ",$content);
            }
        }
         $this->content=$content;
        

    }
    public function getContent()
    {
        return $this->content;
    }




    public function o_docToTxt($filein, $fileout)
    {
        $doc2txt = "/usr/bin/antiword  -m UTF-8.txt  ";
        $cmd = $doc2txt." ".$filein." > ".$fileout."";
         $ret = 0;
        
        if(file_exists($filein))
        {
            $text = shell_exec($cmd);
            $ret=1;
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
            file_put_contents($outpath, $text_content);
            chmod($outpath,0777);
            //echo $text_content."--".$path."--".$outpath;
            return $text_content;
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

            return $text_content;
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

            return $text_content;
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

