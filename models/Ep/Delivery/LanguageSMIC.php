<?php

class Ep_Delivery_LanguageSMIC extends Ep_Db_Identifier
{
	protected $_name = 'LanguageSMIC';
	
	public function updateSMIC($sarray)
	{
		for($s=0;$s<count($sarray['id']);$s++)
		{
			$Where = " id='".$sarray['id'][$s]."'";
			$Larray = array();
			$Larray['SMIC'] = str_replace(",",".",$sarray['SMIC'][$s]);
			$this->updateQuery($Larray,$Where);
		}
	}
	
	public function ListLanguageSMIC()
	{
		$SMICQuery="SELECT * FROM ".$this->_name;
		if(($SMICresult=$this->getQuery($SMICQuery,true))!=NULL)
			return $SMICresult;
	}
}	