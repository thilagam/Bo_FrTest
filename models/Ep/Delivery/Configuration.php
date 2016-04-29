<?php

class Ep_Delivery_Configuration extends Ep_Db_Identifier
{
	protected $_name = 'Configurations';

	public function UpdateCongiguration($request)
	{
		unset($request['submit_config']);
		unset($request['blocknum']);
		
		$ConArray=array();
		foreach($request as $key=>$val)
		{
			$Conwhere="configure_name='".$key."'";
			$ConArray['configure_value']=$val;
			$this->updateQuery($ConArray,$Conwhere);
		}
	}
	public function ListConfiguration()
	{
		$selConfg="SELECT * FROM ".$this->_name;
		if(($resultcon = $this->getQuery($selConfg,true)) != NULL)
			return $resultcon;
		else
			return "NO";
	}
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
