<?php

class Ep_epcontract_Contract extends Ep_Db_Identifier
{
	protected $_name = 'Ep_contract';
    public function insertContract($contract)
    { 
        $this->_name = 'Ep_contract' ;
        //$this->createIdentifier() ;
        $contract['id']    =    $this->getIdentifier() ;
        //exit('id='.$this->getIdentifier());
        $this->insertQuery($contract) ;
    }
    public function insertMission($mission, $recurr)
    { 
        $this->_name = 'Missions_archieve' ;
        if($recurr)
        {
            for ($i=0; $i < $recurr; $i++) {
                $mission['id']=$this->createIdentifier1() ;
                //$mission['id']=$this->getIdentifier() ;
                $this->insertQuery($mission) ;
            }
        }
        else {
            $mission['id']    =    $this->getIdentifier() ;
            $this->insertQuery($mission) ;
        }
    }
    public function updateContract($id, $contract)
    {
        $this->_name = 'Ep_contract' ;
        $contractwhere  =   " id='".$id."'" ;
        $this->updateQuery($contract, $contractwhere) ;
    }
    public function updateMission($id, $mission)
    {
        $this->_name = 'Missions_archieve' ;
        $missionwhere  =   " id='".$id."'" ;
        $this->updateQuery($mission, $missionwhere) ;
    }
    public function deleteContract($id)
    {
        $this->_name = 'Ep_contract' ;
        $this->deleteQuery(" id = '".$id."'") ;
        $this->_name = 'tariffdetails' ;
        $this->deleteQuery(" tariff_id = '".$id."'") ;
    }
    public function listContracts()
    {
        return $this->getQuery("SELECT * FROM ".$this->_name."",true) ;
    }
    public function loadListContracts($sWhere, $sOrder, $sLimit, $condition)
    {
        return $this->getQuery("SELECT * FROM ".$this->_name." ".(!empty($condition) ? 'AND ' . $condition : '')."
                    ".$sWhere." ".$sOrder." ".$sLimit."",true);
    }
    public function listMissions($contract_id)
    {
        //exit("SELECT * FROM Missions_archieve " . ($contract_id ? "where contract_id='" . $contract_id . "'" : ''));
        return $this->getQuery("SELECT * FROM Missions_archieve " . ($contract_id ? "where contract_id='" . $contract_id . "'" : ''),true) ;
    }
    public function loadListMissions($sWhere, $sOrder, $sLimit, $condition)
    {
        //exit("SELECT * FROM Missions_archieve " . ($contract_id ? "where contract_id='" . $contract_id . "'" : ''));
       /* echo "SELECT * FROM Missions_archieve " . ($contract_id ? "where contract_id='" . $contract_id . "'" : '')."
                               ".(!empty($condition) ? 'AND ' . $condition : '')." ".$sWhere." ".$sOrder." ".$sLimit.""; exit;*/
      /* echo "SELECT ma.id, ma.title, ma.mission_length, ma.starting_date, ma.selling_price, ma.article_length, ma.margin_before_signature, ma.margin_after_signature, ma.num_of_articles, c.client_name, c.contract_name FROM Missions_archieve ma LEFT JOIN Ep_contract c ON ma.contract_id = c.id
                               ".(!empty($condition) ? 'AND ' . $condition : '')." ".$sWhere." ".$sOrder." ".$sLimit.""; exit;*/

       /*return $this->getQuery("SELECT ma.*, c.client_name, c.contract_name FROM Missions_archieve ma LEFT JOIN Ep_contract c ON ma.contract_id = c.id
                               ".(!empty($condition) ? 'AND ' . $condition : '')." ".$sWhere." ".$sOrder." ".$sLimit."",true);*/
        $query="SELECT ma.id, ma.title, ma.mission_length, ma.starting_date, ma.selling_price, ma.article_length, ma.margin_before_signature, ma.margin_after_signature, ma.num_of_articles, c.client_name, c.contract_name FROM Missions_archieve ma LEFT JOIN Ep_contract c ON ma.contract_id = c.id
                               ".(!empty($condition) ? 'AND ' . $condition : '')." ".$sWhere." ".$sOrder." ".$sLimit."";
        //echo $query;  exit;
        /* Adding AO infos & date formatting */
        if(($result=$this->getQuery($query,true))!=NULL)
            return $result;
        else
            return "NO";
    }
    public function getContracts()
    {
        return $this->getQuery("SELECT id, contract_name FROM ".$this->_name."",true) ;
    }
    public function getContract($id)
    {
        return $this->getQuery("SELECT * FROM ".$this->_name." WHERE id='".$id."'",true) ;
    }
    public function getMission($id)
    {
        return $this->getQuery("SELECT m.*, c.client_id, c.turnover_currency FROM Missions_archieve m inner join Ep_contract c ON m.contract_id = c.id WHERE m.id=" . $id,true) ;
    }
    public function getClientNcurr($id)
    {
        $result = $this->getQuery("SELECT client_id, turnover_currency FROM Ep_contract WHERE id=" . $id,true) ;
        return $result[0]['client_id'].'#'.$result[0]['turnover_currency'];
    }
	
	public function createIdentifier1()
    {
        $d = new Date();
		return $d->getSubDate(5,14).mt_rand(100000,999999);
  	}
	
}	
