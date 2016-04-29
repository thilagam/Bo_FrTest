<?php

class Ep_User_SaveSearch extends Ep_Db_Identifier
{
	protected $_name = 'SaveSearch';
	private $id;
	private $user_id;
    private $search_name;
    private $url;

	public function loadData($array)
	{
		$this->user_id=$array["user_id"];
        $this->search_name=$array["search_name"];
        $this->url=$array["url"];
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
	    $array["user_id"] = $this->user_id;
        $array["search_name"] = $this->search_name;
        $array["url"] = $this->url;
        return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    /////////get all saved searched urls of loged in user ///////////////////////////
    public function getSearchedUrls($userId)
    {
        $query = "SELECT id, search_name, url FROM ".$this->_name." WHERE active = 'yes' AND user_id=".$userId;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    public function updateSaveSearch($data,$query)
    {
          $this->updateQuery($data,$query);
    }
}

