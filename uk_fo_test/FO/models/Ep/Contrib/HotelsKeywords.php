<?php
/**
 * Contact  Model
 * This Model  is responsible for hotels dev bl and wl keywords*
 * @author Chandu
 * @editor Chnadu
 * @version 1.0
 */
class EP_Contrib_HotelsKeywords extends Ep_Db_Identifier
{
	protected $_name = 'HotelsKeywords';
	private $id;
    private $type;
    private $language;
	private $keywords_list;

	public function loadData($array)
	{
        $this->id=$array["id"] ;
        $this->type=$array["type"] ;
        $this->language=$array["language"] ;
		$this->keywords_list=$array["keywords_list"] ;

    	return $this;
	}
	public function loadintoArray()
	{
		$array = array();
        $array["id"] = $this->id;
        $array["type"] =  $this->type;
        $array["language"] =  $this->language;
        $array["keywords_list"] = $this->keywords_list;

        return $array;
	}

    ///////getting the keyword with the type and language ///////////////
    public function getBlWlKeywords($type, $language)
    {
       // mb_internal_encoding('UTF-8');
       // ini_set('default_charset', 'UTF-8');
       /* //mb_internal_encoding('UTF-8');
        //ini_set('default_charset', 'UTF-8');
        //$this->getQuery("SET NAMES 'utf8'");
        echo '<pre>';
        var_dump($this->getAdapter());*/
        //$this->query('set names "utf8"');
        $query = " SELECT *, COMPRESS(keywords_list) AS zip_keyword_list FROM HotelsKeywords WHERE  type='".$type."' AND language='".$language."' ";//." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL){
            //echo utf8_encode($result[0]['keywords_list']);
           //echo "<br>".mb_convert_encoding($result[0]['keywords_list'], "utf-8", "Windows-1251");
             $kl = $result[0]['zip_keyword_list'];
            $kl = gzuncompress(substr($kl, 4));
            return explode("|",$kl);
           // return explode("|",$result[0]['keywords_list']);
        }
        else
            return "NO";
    }

}
