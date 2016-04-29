<?php

class Ep_Article_XmlDecoder
{
	//Private properties
	var $parser;         //current xml parser
	
/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
	function XmlDecoder()
	{
		//Initialization of properties
	}
	
	// decode the XML result file and return it into array
	function decode($xmlResult,$nbReturn=0)
	{
		$values = array();
		$this->_setParser(xml_parser_create());
		xml_parse_into_struct($this->_getParser(),$xmlResult,$values);
		xml_parser_free($this->_getParser());
		if(!$nbReturn)$nbReturn=count($values);
		$return = array();
		$y=-1;
		$z = 0;
		for ($i=0; $i<count($values) && $y<$nbReturn;$i++)
		{	
			if($values[$i]["tag"] == "RES")
			{
				if($values[$i]["attributes"]["EN"])$return[0]["nbResult"] = $values[$i]["attributes"]["EN"];
			}
			if($values[$i]["type"]=="complete" && $values[$i-1]["type"]=="open")$y++;
			if($values[$i]["type"]=="complete")$return[$y][$values[$i]["tag"]]= $values[$i]["value"];		
		}
		//sort($return);
		return $return;
	}
	//decode a google mini xml result and return an array of data
	function getRelatedDocsFromFile($file_path,$lang,$html=false,$firstDoc=false,$nbDoc=6)
	{
		//initializing properties
		$docs= Array();
		$count = 0;
		$i=0;
		$doc = new Ep_Document_Document();
		
		$xml_path = SSD_CACHE_PATH . substr($file_path,1);
		if(!file_exists($xml_path))return $docs;
		
		$xml = file_get_contents($xml_path);
		$tab = $this->decode($xml);
		
		if($firstDoc==true)
		{
			$ed = $nbDoc-1;
			$fd = -1;
		}
		else
		{
			$fd = 0;
			$ed = $nbDoc;
		}
				
		foreach ($tab as $ele)
		{
			$bd = array();
			//identifier
			if ($ele['U'])
			{
				$url = $ele['U'];
				preg_match('/_(\d+)_\d+_[A-Z]+\.html/', $ele['U'], $matches);
				$id = $matches[1];
				$title = $doc->getResourceTitle($id,$lang);
				$url = $doc->getResourceUrl($id,$lang);
				$s = new String($ele['T']);
				$s1 = new String($title);
				$s1->setBoldWord($s->getBoldWord());
					
				if ($count > $fd && $count < $ed)
				{ 
					if($html)$link = $url;
					else $link = '/summary?id='.$id;
					$docs[] = array(
						'id'=>$id, 
						'title'=>$title,
						'url'=> $link,
						'titleGoogle'=>$s1->getString()
					);
					$i++;
				}
				$count+=1;
			}
		}
		return $docs;
	}


	//decode a google mini xml result and return an array of data
	function getRelatedDocs($docId,$lang,$html=false,$firstDoc=false,$nbDoc=6)
	{
		//initializing properties
		$time_start = microtime(true);
		$docs= Array();
		$count = 0;
		$i=0;
		$doc = new Ep_Document_Document();
		
		$xml_path = SSD_CACHE_PATH . $lang . "/" . $docId . ".xml";
		if(!file_exists($xml_path))return $docs;
		
		$xml = file_get_contents($xml_path);
		$tab = $this->decode($xml);
		
		if($firstDoc==true)
		{
			$ed = $nbDoc-1;
			$fd = -1;
		}
		else
		{
			$fd = 0;
			$ed = $nbDoc;
		}
				
		//echo 'Total time consumed: '.(microtime(true) - $time_start)."<br />";
		$time_total = 0;
		foreach ($tab as $ele)
		{
			$bd = array();
			//identifier
			if ($ele['U'])
			{
					
				if ($count > $fd && $count < $ed)
				{ 
					$url = $ele['U'];
					preg_match('/_(\d+)_\d+_[A-Z]+\.html/', $ele['U'], $matches);
					$id = $matches[1];
					$time_loop_start = microtime(true);
					$title = $doc->getResourceTitle($id,$lang);
					$url = $doc->getResourceUrl($id,$lang);
					$time_total += (microtime(true)-$time_loop_start);
					$s = new String($ele['T']);
					$s1 = new String($title);
					$s1->setBoldWord($s->getBoldWord());
					if($html)$link = $url;
					else $link = '/summary?id='.$id;
					$docs[] = array(
						'id'=>$id, 
						'title'=>$title,
						'url'=> $link,
						'titleGoogle'=>$s1->getString()
					);
					$i++;
				}
				$count+=1;
			}
		}
		//var_dump($docs);
		//echo 'loop i/o time:'.$time_total."<br />";
		//echo 'Total time consumed: '.(microtime(true) - $time_start)."<br />";
		return $docs;
	}
	

	// decode a google mini xml result and return an array of data
	function getRelatedDocsOld($docId,$lang,$html=false,$nbDoc=6)
	{
		global $_mysql_db;
		$xml_path = "/home/sites/site6/users/cache/" . $lang . "/" . $docId . ".xml";
		$xml = file_get_contents($xml_path);
		$tab = $this->decode($xml);
		$docs= Array();
		$count = 0;
		$doc = new document($_mysql_db);
		
		foreach ($tab as $ele)
		{
			if ($ele['U'])
			{
				$url = $ele['U'];
				preg_match('/_(\d+)_\d+_[A-Z]+\.html/', $ele['U'], $matches);
				$id = $matches[1];
				$title = $doc->getResourceTitle($id,$lang);
				$url = $doc->getResourceUrl($id,$lang);
				
				if ($count>0 && $count <$nbDoc)
				{ 
					if($html)$link = $url;
					else $link = $this->_config->www->baseurl.'/summary?id='.$id;
					$docs[] = array(
						'id'=>$id, 
						'title'=>$title,
						'url'=> $link
					);
				}
				$count+=1;
			}
		}
		return $docs;
	}
	
	
/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/
	//set methods
	function _setParser($parser)
	{
		$this->parser = $parser;
	}
	
	//get methods
	function _getParser()
	{
		return $this->parser;
	}
}
?>
