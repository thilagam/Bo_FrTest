<?php

class Ep_Delivery_CategoryDifficultyPercent extends Ep_Db_Identifier
{
	protected $_name = 'CategoryDifficultyPercent';
	
	public function updateCatDiff($carray)
	{
		for($c=0;$c<count($carray['id']);$c++)
		{
			$Where = " id='".$carray['id'][$c]."'";
			$Darray = array();
			$Darray['percentage'] = $carray['percentage'][$c];
			$this->updateQuery($Darray,$Where);
		}
		
	}
	
	public function ListCategoryDifficulty()
	{
		$CatDiffQuery="SELECT * FROM ".$this->_name;
		if(($CatDiffresult=$this->getQuery($CatDiffQuery,true))!=NULL)
			return $CatDiffresult;
	}
}	