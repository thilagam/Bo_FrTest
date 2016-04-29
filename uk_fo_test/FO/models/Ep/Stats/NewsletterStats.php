<?php

class EP_Stats_NewsletterStats extends Ep_Db_Identifier
{ 
	protected $_name = 'NewsletterStats';
	private $id;
    private $user_id;
	private $newsletter_id;
    private $email;
	private $sent_at;
    private $viewed_at;
    

    public function loadData($array)
	{
         //$this->id=$array["id"] ;
         $this->user_id=$array["user_id"] ;
         $this->newsletter_id=$array["newsletter_id"] ;
         $this->email=$array["email"] ;
		 $this->sent_at=$array["sent_at"] ;
		 $this->viewed_at=$array["viewed_at"] ;

		return $this;
	}
	public function loadintoArray()
	{
		$array = array();        
         $array["user_id"] = $this->user_id;
		 $array["newsletter_id"] = $this->newsletter_id;
         $array["email"] = $this->email;
		 $array["sent_at"] = $this->sent_at;
		 $array["viewed_at"] = $this->viewed_at;	 
		return $array;
	}
    
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }    
	
	public function checkNLviewExists($newsletter_id,$user_id)
	{
		$query="select viewed_at From ".$this->_name." Where user_id='".$user_id."' and newsletter_id='".$newsletter_id."'";
		
		//echo $query;exit;
		
		if($view_exists=$this->getQuery($query,true))
        {
           return TRUE;
        }
        else
             return FALSE;
		
	}

}
