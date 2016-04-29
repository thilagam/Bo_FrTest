<?php

class Ep_Delivery_CronLock extends Ep_Db_Identifier
{
	protected $_name = 'CronLock';
    private $id;
    private $cron_name;
    private $locked;
    private $created_at;

    public function loadData($array)
    {
        $this->id=$array["id"] ;
        $this->cron_name=$array["cron_name"];
        $this->locked=$array["locked"];
        $this->created_at=$array["created_at"] ;
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["id"] = $this->getIdentifier();
        $array["cron_name"] = $this->cron_name;
        $array["locked"] = $this->locked;
        $array["created_at"] = $this->created_at;
        return $array;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }
    public function __get($name){
        return $this->$name;
    }
    //Function to check profile exists
    public function getCronLock($name)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE cron_name = '".$name."'";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";
    }
    public function updateCronLock($data,$query)
    {
        $this->updateQuery($data,$query);
    }


}	