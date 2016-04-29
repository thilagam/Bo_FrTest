<?php
/**
 * Ep_Contract_Delivery
 * @author Arun
 * @package Contract
 * @version 1.0
 */
class Ep_Contract_Delivery extends Ep_Db_Identifier
{
    protected $_name = 'C_Delivery';
    private $id;
    private $client_id;
    private $contract_id;
    private $chief_editor;
    private $title;
    private $delivery_date;
    private $spec_file_path;
    private $created_at;
    public function loadData($array)
    {
        $this->client_id=$array["client_id"];
        $this->title=$array["title"];
        $this->contract_id=$array["contract_id"];
        $this->chief_editor=$array["chief_editor"];
        $this->delivery_date=$array["delivery_date"];
        $this->spec_file_path=$array["spec_file_path"];
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["client_id"] = $this->client_id;
        $array["title"] = $this->title;
        $array["contract_id"] = $this->contract_id;
        $array["chief_editor"] = $this->chief_editor;
        $array["delivery_date"] = $this->delivery_date;
        $array["spec_file_path"] = $this->spec_file_path;
        $array["created_at"] = $this->created_at;
        return $array;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }
    public function __get($name){
        return $this->$name;
    }
    public function getDeliveryList($clientId=NULL)
    {
        if($clientId)
            $where= " WHERE client_id='".$clientId."'";
        $query="select
                    *
                from ".$this->_name.
            $where
        ;
        // echo $query;exit;
        if(($contracts=$this->getQuery($query,true))!=NULL)
            return $contracts;
        else
            return "NO";
    }
    public function getAllDeliveries()
    {
        $deliveryDetails="select d.client_id,d.id,d.title,c.title as contract,d.delivery_date,u.email,cl.company_name
                FROM ".$this->_name." d
                INNER JOIN Contracts c ON c.id=d.contract_id
                INNER JOIN User u ON c.client_id=u.identifier
                LEFT JOIN Client cl ON u.identifier=cl.user_id
                Group BY d.id
                ORDER BY d.delivery_date DESC";
        //echo $deliveryDetails;exit;
        if(($deliveries=$this->getQuery($deliveryDetails,true))!=NULL)
            return $deliveries;
        else
            return "NO";
    }
    public function getDeliveryDetails($deliveryid=NULL)
    {
        if($deliveryid)
            $where= " WHERE id=".$deliveryid;
        $query="select
                    *
                from ".$this->_name.
            $where
        ;
        // echo $query;exit;
        if(($deliveries=$this->getQuery($query,true))!=NULL)
            return $deliveries;
        else
            return "NO";
    }
    public function updateDelivery($data,$cid)
    {
        $where=" id=".$cid;
        //print_r($data);exit;
        $this->updateQuery($data,$where);
    }
}
