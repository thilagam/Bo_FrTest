<?php

class Ep_Delivery_PlagStuckComments extends Ep_Db_Identifier
{
	protected $_name = 'PlagStuckComments';
	private $identifier;
	private $article_id;
    private $artprocess_id;
    private $user_id;
    private $comments;
	private $created_at;
	private $php;
	private $ruby;

	public function loadData($array)
	{
		$this->article_id=$array["article_id"] ;
        $this->artprocess_id=$array["artprocess_id"] ;
        $this->user_id=$array["user_id"] ;
		$this->comments=$array["comments"];
       	$this->created_at=$array["created_at"] ;
		$this->php=$array["php"] ;
		$this->ruby=$array["ruby"] ;
        
    	return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["id"] = $this->getIdentifier();
        $array["article_id"] =  $this->article_id;
        $array["artprocess_id"] = $this->artprocess_id;
		$array["user_id"] = $this->user_id;
		$array["comments"] = $this->comments;
    	$array["created_at"] = $this->created_at;
    	$array["php"] = $this->php;
        $array["ruby"] = $this->ruby;

        return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    ////////getting the comments of plag///////////////
    public function getAllPlagComments($artprocId)
    {
        $query = "SELECT psc.*, up.first_name, up.last_name  FROM ".$this->_name." psc
                LEFT JOIN UserPlus up ON psc.user_id=up.user_id WHERE artprocess_id = ".$artprocId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    ////////getting php errors count///////////////
    public function getAllPhpErrors($artprocId)
    {
        $query = "SELECT count(*) as phpcount FROM ".$this->_name." WHERE php = '1' AND artprocess_id = ".$artprocId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ////////getting ruby errors count///////////////
    public function getAllRubyErrors($artprocId)
    {
        $query = "SELECT count(*) as rubycount FROM ".$this->_name." WHERE ruby = '1' AND artprocess_id = ".$artprocId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

	public function updatePlagStuckComments($data,$comment_id)
    {
        $where=" identifier='".$comment_id."'";
        //print_r($data);echo $where;exit;
        $this->updateQuery($data,$where);
    }


}