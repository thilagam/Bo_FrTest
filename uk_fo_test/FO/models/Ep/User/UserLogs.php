<?php

class Ep_User_UserLogs extends Ep_Db_Identifier
{
    protected $_name = 'UserLogs';

    public function InsertLogs($data)
    {
        $data['id']=$this->getIdentifier();
        $this->insertQuery($data);
    }
}
