<?php

class Ep_Quote_QuotesHistory extends Ep_Db_Identifier
{
	protected $_name = 'QuotesHistory';
	
	public function getQuotesHistory($inputArr)
	{
		$langarray=array("uk","sp","de","it","pt","ptsb","us");
		if($inputArr['language']=='fr')
			$langcond=" AND language='".$inputArr['language']."'";
		elseif(in_array($inputArr['language'],$langarray))
			$langcond=" AND language='uk,sp,de,it,pt,ptsb,us'";
		else
			$langcond=" AND language='other'";
			
		$Query1="SELECT prod_cost,margin,variation FROM ".$this->_name." WHERE 
				type='".$inputArr['type']."'  AND content_type='".$inputArr['content_type']."'
				AND volume='".$inputArr['volume']."'".$langcond;	
		$result1 = $this->getQuery($Query1,true);
		return $result1;
	}
	
	public function getAllQuotesHistory()
	{
		$Query2="SELECT * FROM ".$this->_name;	
		$result2 = $this->getQuery($Query2,true);
		return $result2;
	}
	
	public function getQuotesHistoryById($id)
	{
		$Query3="SELECT * FROM ".$this->_name." WHERE id='".$id."'";	
		$result3 = $this->getQuery($Query3,true);
		return $result3;
	}
	
	public function insertHistory($HistArr)
	{
		$this->insertQuery($HistArr);
	}
	
	public function updateHistory($HistArr,$id)
	{
		$where=" id=".$id;
		$this->updateQuery($HistArr,$where);
	}
	
	public function deleteHistory($id)
	{
		$whereq=" id='".$id."'";
		$this->deleteQuery($whereq);
	}
}