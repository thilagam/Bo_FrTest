<?php

class Ep_User_UserLogs extends Ep_Db_Identifier
{
	protected $_name = 'UserLogs';
	
	public function InsertLogs($data){
        $data['id']=$this->getIdentifier();
        $this->insertQuery($data);
	}
    public function getUserLogs($user_id){
        $where = "WHERE `user_id` = '".$user_id."' AND (`new_value` IS NOT NULL AND new_value != '' ) ";
        $query = "SELECT `updated_at` as updated_at,`field`,`old_value`,`new_value`,`log_type`  FROM `UserLogs` $where ORDER BY `updated_at` DESC";
        if (($result = $this->getQuery($query, true)) != NULL) {
            return $result;
        } else
            return false;
    }
}