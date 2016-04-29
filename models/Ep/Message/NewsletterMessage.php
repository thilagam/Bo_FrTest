<?php

class EP_Message_NewsletterMessage extends Ep_Db_Identifier
{ 
	protected $_name = 'NewsletterMessage';
	private $id;
	private $mail_from;
	private $subject;
    private $message;
	private $attachment;
	private $created_at;
    

    public function loadData($array)
	{
         $this->id=$array["id"] ;
         $this->mail_from=$array["mail_from"] ;
		 $this->subject=$array["subject"] ;
		 $this->message=$array["message"] ;
         $this->attachement=$array["attachment"] ;
         //$this->created_at=$array["created_at"] ;

		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
         $array["id"] = $this->getIdentifier();
		 $array["mail_from"] = $this->mail_from;
		 $array["subject"] = $this->subject;
		 $array["message"] = $this->message;
         $array["attachment"] = $this->attachment;
         //$array["created_at"] = $this->created_at;
		
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }

    

}
