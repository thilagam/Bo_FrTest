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
   
    ///////get dureation for paused for particular time////
    public function getPauseDuration($requestId)
    {
        $query = "SELECT  * FROM ".$this->_name." WHERE ftvrequest_id = '".$requestId."' AND resume_at is not null ";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            $diff = 0;
            foreach($result as $key=>$val)
            {
                $ptime = strtotime($result[$key]['pause_at']);
                $rtime = strtotime($result[$key]['resume_at']);
                $diff+= $rtime - $ptime;
                /*$pd[$key] = floor($diff[$key]/86400);
                $pdays[$key] = ($pd[$key] < 10 ? '0' : '').$pd[$key];
                $ph[$key] = floor(($diff[$key]-$pd[$key]*86400)/3600);
                $phours[$key] = ($ph[$key] < 10 ? '0' : '').$ph[$key];
                $pm[$key] = floor(($diff[$key]-($pd[$key]*86400+$ph[$key]*3600))/60);
                $pminutes[$key] = ($pm[$key] < 10 ? '0' : '').$pm[$key];*/
            }
              /* $pause['pdays'] = array_sum($pdays);
               $pause['phours'] = array_sum($phours);
               $pause['pminutes'] = array_sum($pminutes);*/
                return $pause = $diff;// = array_sum($pdays)."-".array_sum($phours)."-".array_sum($pminutes);
        }
        else{
            /*$pause['pdays'] = 0;
            $pause['phours'] = 0;
            $pause['pminutes'] = 0;*/
            return $pause = 0; // = "0-0-0";
        }

    }
    ///////the duration how much request has been paused for total times////
    public function getAllPauseDuration($requestId)
    {
        $query = "SELECT  * FROM ".$this->_name." WHERE ftvrequest_id = '".$requestId."' AND resume_at is not null ";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return "NO";

    }
    ///////get if its paused////
    public function inPause($requestId)
    {
        $query = "SELECT  * FROM ".$this->_name." WHERE ftvrequest_id = '".$requestId."' AND resume_at is null ORDER BY pause_at desc limit 0,1";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return "yes";
        }
        else{
            return "no";
        }

    }
    ///////get if the request is pause or not////
    public function pausedRequest($requestId)
    {
        $query = "SELECT  * FROM ".$this->_name." WHERE ftvrequest_id = '".$requestId."'";
        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return "yes";
        }
        else{
            return "no";
        }

    }
    
    
    public function updateFtvPauseTime($data,$query)
    {
        //$where=" user_id='".$identifier."'";
        /* print_r($data);exit;    echo  $query;
         echo $this->updateQuery($data,$query);    exit;*/
        $this->updateQuery($data,$query);

    }
    public function deleteFtvPauseTime($query)
    {
        $this->deleteQuery($query);
    }
}

