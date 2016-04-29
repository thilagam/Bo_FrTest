<?php



class Ep_Ao_QuotesHistory extends Ep_Db_Identifier

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

			

		$Query1="SELECT prod_cost,margin,variation,selling_price FROM ".$this->_name." WHERE 

				type='".$inputArr['type']."'  AND content_type='".$inputArr['content_type']."'

				AND volume='".$inputArr['volume']."'".$langcond;	
		
		$result1 = $this->getQuery($Query1,true);

		return $result1;

	}

}