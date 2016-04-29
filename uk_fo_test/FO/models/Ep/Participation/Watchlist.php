<?php
/**
 * Ep_Participation_Watchlist
 * @author Admin
 * @package Watchlist
 * @version 1.0
 */
class Ep_Participation_Watchlist extends Ep_Db_Identifier
{
	protected $_name = 'Watchlist';
	private $id;
	private $user_id;
	private $contract;
	private $status;
	private $created_at;
	private $updated_at;
		
	public function loadData($array)
	{
		$this->user_id=$array["user_id"] ;
		$this->contract=$array["contract"] ;
		$this->status=$array["status"] ;
		$this->created_at=$array["created_at"] ;
		$this->updated_at=$array["updated_at"] ;
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["id"] = $this->getIdentifier();
		$array["user_id"] = $this->user_id;
		$array["contract"] = $this->contract;
		$array["status"] = $this->status;
		$array["created_at"] = $this->created_at;
		$array["contract_text"] = $this->contract_text;

		
		
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    public function updateWatchlist($data,$watchlist_id)
    {
        $where=" id='".$watchlist_id."'";
       // print_r($data);echo $where;exit;
        return $this->updateQuery($data,$where);
    }
}

