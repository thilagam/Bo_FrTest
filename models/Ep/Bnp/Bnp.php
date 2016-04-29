<?php

class Ep_Bnp_Bnp extends Ep_Db_Identifier
{
	public function getCity(){
        $query = "SELECT `city_id`,`city_name` FROM `BNP_city` WHERE `status` = 'active' ";
        if (($result = $this->getQuery($query, true)) != NULL){
            return $result;
        }
        else
            return false;
	}
    public function getBnpSampleText($param=NULL){
        $query = "SELECT * FROM `BNP_sampletext` WHERE `city_id` = '".$param['city_id']."'";
        if (($result = $this->getQuery($query, true)) != NULL){
            return $result;
        }
        else
            return false;
    }
}