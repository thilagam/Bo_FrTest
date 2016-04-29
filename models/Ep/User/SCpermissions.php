<?php

class Ep_User_SCpermissions extends Ep_Db_Identifier
{
	protected $_name = 'SCpermissions';
	
	public function InsertScPermissions($permission)
	{
		$permission['id']=$this->getIdentifier();
		$this->insertQuery($permission);	
	}
	
	public function getScPermissions($sc)
	{
		$query = "SELECT * FROM SCpermissions WHERE superclient='".$sc."' ORDER BY created_at";
       
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
	}
	
	public function deleteScPermissions($identifier)
    {
        $where=" superclient='".$identifier."'";    
		if($identifier)	
			echo $this->deleteQuery($where);

    }
}