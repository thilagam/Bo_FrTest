<?php

class Ep_User_Configuration extends Ep_Db_Identifier
{
	protected $_name = 'Configurations';
	
	public function getConfiguration($columns)
	{
		$SelConfg="SELECT configure_value FROM ".$this->_name." WHERE configure_name='".$columns."' ";
		
		if(($resultconfg = $this->getQuery($SelConfg,true)) != NULL)
			return $resultconfg[0]['configure_value'];
		else
			return "NO";
		
	}
	public function getAllConfigurations()
	{
		$AllConfigs="SELECT * FROM ".$this->_name;
		
		if(($ConfigDetails = $this->getQuery($AllConfigs,true)) != NULL)
		{
			$config_array=array();
			
			foreach($ConfigDetails as $setting)
			{
				$config_array[$setting['configure_name']]=$setting['configure_value'];
			}
			return $config_array;
		}	
		else
			return "NO";
	
	}
	
}