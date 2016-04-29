<?php

class EP_Message_Newsletter extends Ep_Db_Identifier
{ 
	protected $_name = 'Newsletter';
	private $id;
    private $newsletter_message_id;
	private $mail_to;
    private $mail_at;
	private $status;
    private $created_at;
    

    public function loadData($array)
	{
         //$this->id=$array["id"] ;
         $this->newsletter_message_id=$array["newsletter_message_id"] ;
         $this->mail_to=$array["mail_to"] ;
         $this->mail_at=$array["mail_at"] ;
		 //$this->status=$array["status"] ;

		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
        //$array["id"] = $this->getIdentifier();
         $array["newsletter_message_id"] = $this->newsletter_message_id;
		 $array["mail_to"] = $this->mail_to;
         $array["mail_at"] = $this->mail_at;
		 //$array["status"] = $this->status;

		return $array;
	}
    /////////get all contracts whose delivery statuses are pending and new//////
    public function getnotsentnewsletter()
    {
        $query = "SELECT n.id as newsletterId, n.newsletter_message_id, n.mail_to, n.status, nm.*, u.email FROM ".$this->_name." n
			 INNER JOIN NewsletterMessage nm ON nm.id = n.newsletter_message_id
			 INNER JOIN User u ON u.identifier = n.mail_to
			 WHERE n.status IN  ('not sent') AND (mail_at > DATE_ADD(NOW(), INTERVAL -1 HOUR) AND mail_at < NOW())";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }

    ////////////udate the Participation table//////////////////////
    public function updateNewsletter($data,$query)
    {
        //echo $query;
        //print_r($data);
        $this->updateQuery($data,$query);
    }

}
