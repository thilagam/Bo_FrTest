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
         $this->content=utf8_decode($content);


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
            //echo $text_content."--".$path."--".$outpath;
            return $text_content;
        }
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

