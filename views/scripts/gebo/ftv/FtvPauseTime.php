<?php

class Ep_Ftv_FtvPauseTime extends Ep_Db_Identifier
{
    protected $_name = 'FtvPauseTime';
    private $ftvrequest_id;
    private $pause_at;
    private $resume_at;
    
    public function loadData($array)
    {
        $this->ftvrequest_id=$array["ftvrequest_id"] ;
        $this->pause_at=$array["pause_at"];
        $this->resume_at=$array["resume_at"];
        
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["ftvrequest_id"] = $this->ftvrequest_id;
        $array["pause_at"] = $this->pause_at;
        $array["resume_at"] = $this->resume_at;
       
        return $array;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }
   
    ///////get all detials////
    public function getPauseTime($requestId)
    {
        $query = "SELECT * FROM ".$this->_name." WHERE ftvrequest_id = '".$requestId."'";
        if(($result = $this->getQuery($query,false)) != NULL)
        {
            return $result;
        }
        else
            return "no";
    }
    
    
    public function updateFtvPauseTime($data,$query)
    {
        //$where=" user_id='".$identifier."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->updateQuery($data,$query);

    }
}

