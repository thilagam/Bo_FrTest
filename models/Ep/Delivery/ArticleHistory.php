<?php

class Ep_Delivery_ArticleHistory extends Ep_Db_Identifier
{
	protected $_name = 'ArticleHistory';
	private $id;
	private $article_id;
    private $user_id;
	private $stage;
    private $action;
    private $action_at;
    private $reasons;
    private $action_sentence;

	public function loadData($array)
	{
		$this->id=$array["id"] ;
        $this->article_id=$array["article_id"];
        $this->user_id=$array["user_id"];
        $this->stage=$array["stage"];
        $this->action=$array["action"];
		$this->action_at=$array["action_at"] ;
        $this->reasons=$array["reasons"] ;
        $this->action_sentence=$array["action_sentence"] ;
        return $this;
	}
	public function loadintoArray()
	{
		$array = array();
	    $array["id"] = $this->getIdentifier();
        $array["article_id"] = $this->article_id;
        $array["user_id"] = $this->user_id;
        $array["stage"] = $this->stage;
        $array["action"] = $this->action;
        $array["action_at"] = $this->action_at;
        $array["reasons"] = $this->reasons;
        $array["action_sentence"] = $this->action_sentence;
       	return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
	
	//Insert Article History
    public function insertHistory($inarray){
		$inarray['id']=$this->getIdentifier();
        $this->insertQuery($inarray); 
    }

    /////////get all history on this article ///////////////////////////
	public function articleHistoryDetails($artId)
	{
         $query = "SELECT ah.*, up.first_name FROM ".$this->_name." ah

                LEFT JOIN User u ON u.identifier=ah.user_id
                Left JOIN UserPlus up ON up.user_id=ah.user_id WHERE ah.article_id=".$artId." ORDER BY ah.action_at ASC";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    ////get latest record/////
    public  function getLatestHistory($artId, $userId)
    {
       $query = "SELECT id, MAX(user_in), action_sentence FROM ".$this->_name." WHERE article_id=".$artId." AND user_id=".$userId." group by id order by user_in DESC limit 1";
       //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ////get latest record/////
    public  function getLatestHistoryStatus($artId)
    {
        $query = "SELECT action_sentence FROM ".$this->_name." WHERE article_id=".$artId." ORDER BY action_at DESC limit 1";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    public function updateArticleHistory($data,$query)
    {
          $this->updateQuery($data,$query);
    }

    //get history of AO in ongoing AO
    public function getAOHistory($params)
    {
        if($params['article_id'] && $params['ao_id'])
            $condition=" article_id='".$params['article_id']."' OR article_id='".$params['ao_id']."' ";
        else if($params['article_id'])
            $condition=" article_id='".$params['article_id']."'";

        else if($params['ao_id'])
            $condition=" article_id='".$params['ao_id']."' OR article_id in (select id from Article Where delivery_id='".$params['ao_id']."') ";


         $historyQuery="SELECT action_at,action_sentence,action
                    FROM $this->_name ah                    
                WHERE  $condition
                ORDER BY  action_at DESC";

        if(($count=$this->getNbRows($historyQuery))>0)
        {
            $aoHistory=$this->getQuery($historyQuery,true);
            return $aoHistory;

        }
        else
            return NULL;        
    }
}

