<?php


class Ep_Delivery_Pollarticle extends Ep_Db_Identifier
{
	protected $_name = 'Poll_article';
	
	public function insertpollArticles($pid,$Aarr)
	{
		$count=count($Aarr['art_title']);
		
		$Aid=array();
		for($p=0;$p<$count;$p++)
		{ 
			$Aarray = array(); 
			$d = new Date();
			$num = $d->getSubDate(2,14).rand($p,999);
			
			$Aarray["id"]=$this->createIdentifier(); 
				$Aid[]=$num;
			$Aarray["poll_id"] = $pid; 
			
			$Aarray["title"] = $Aarr['art_title'][$p]; 
			$Aarray["language"] = $Aarr['art_lang'][$p]; 
			$Aarray["category"] = $Aarr['art_cat'][$p]; 
			$Aarray["type"] = $Aarr['art_type'][$p];  
			$Aarray["sign_type"] = $Aarr['art_sign_type'][$p]; 
			
			$Aarr['art_num_of_min'][$p]=str_replace(",",".",$Aarr['art_num_of_min'][$p]);
			$Aarray["num_min"] = $Aarr['art_num_of_min'][$p]; 
			
			$Aarr['art_num_of_max'][$p]=str_replace(",",".",$Aarr['art_num_of_max'][$p]);
			$Aarray["num_max"] = $Aarr['art_num_of_max'][$p];  
		
			$this->insertQuery($Aarray);
		}
		return $Aid;
	}
	
	public function createIdentifier()
    {
        $d = new Date();
		return $d->getSubDate(5,14).mt_rand(100000,999999);
  	}
	
	public function getPollarticles($poll)
	{
		$getPoll="SELECT id,title,language,type,category FROM ".$this->_name." WHERE poll_id=".$poll;
		
		$resultPoll = $this->getQuery($getPoll,true);

		return $resultPoll;
	}
}