<?php
/**
 * Ep_Ticket_Ticket
 * @author Admin
 * @package Ticket
 * @version 1.0
 */
 /*Status
        0 => sent by sender / received by recipient
        1 => received by sender / sent by recipient
        2 => classified by sender
        3 => classified by recipient
*/
class Ep_Ticket_Ticket extends Ep_Db_Identifier
{
	protected $_name = 'Ticket';
	private $id;
	private $sender_id;
	private $recipient_id;
	private $title;
	private $template_type;
	private $status;
	private $created_at;
	private $updated_at;

	public function loadData($array)
	{
		$this->id=$array["id"] ;
		$this->sender_id=$array["sender_id"];
		$this->recipient_id=$array["recipient_id"];
		$this->title=$array["title"] ;
		$this->template_type=$array["template_type"] ;
		$this->status=$array["status"] ;
		$this->created_at=$array["created_at"] ;
		$this->updated_at=$array["updated_at"] ;
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();		
		$array["id"] = $this->getIdentifier();
		$array["sender_id"] = $this->sender_id;
        $array["recipient_id"] = $this->recipient_id;
		$array["title"] = $this->title;
		$array["template_type"] = $this->template_type;
		$array["status"] = $this->status;
		$array["created_at"] = $this->created_at;
		$array["updated_at"] = $this->updated_at;		
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }	
	public function getContacts($type)
    {
        $query="select
                    u.identifier,up.first_name as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                where u.type='".$type."'
                Group BY u.identifier,contact_name
                ORDER BY contact_name    
                ";
        if(($clients=$this->getQuery($query,true))!=NULL)
            return $clients;
        else
            return "Not Exists";

    }
    public function getUserInbox($type,$user)
    {
        $join=" INNER JOIN Message m ON m.ticket_id=t.id
                LEFT JOIN UserPlus up ON up.user_id=t.recipient_id
                INNER JOIN User u ON u.identifier=t.recipient_id
                ";
        $whereQuery=" where t.recipient_id='".$user."' and u.type='".$type."' Group By t.sender_id,m.id ORDER BY m.status DESC, m.created_at DESC";
        $msg_query="select m.id as messageId,ticket_id,sender_id,email, title as Subject,content,DATE_FORMAT(m.created_at , '%d/%m/%Y') as receivedDate,m.status from
                  ".$this->_name." t ".$join.$whereQuery ;
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
      
    }
	function getSenderName($sender)
	{
		$senderQuery="select CONCAT(first_name,' ',last_name) as sendername ,email from User u LEFT JOIN UserPlus ON identifier=user_id where identifier='".$sender."'";
		
		if(($result=$this->getQuery($senderQuery,true))!=NULL)
		{
			if($result[0]['sendername']!=NULL)
				return $result[0]['sendername'];
			else	
				return $result[0]['email'];
		}
           
	}
    function getRecipientName($sender)
	{
		$senderQuery="select CONCAT(first_name,' ',last_name) as sendername ,email from User u LEFT JOIN UserPlus ON identifier=user_id where identifier='".$sender."'";

		if(($result=$this->getQuery($senderQuery,true))!=NULL)
		{
			if($result[0]['sendername']!=NULL)
				return $result[0]['sendername'];
			else
				return $result[0]['email'];
		}

	}
     public function getUserSentBox($type,$user)
    {
        $join=" INNER JOIN Message m ON m.ticket_id=t.id
                LEFT JOIN UserPlus up ON up.user_id=t.sender_id
                INNER JOIN User u ON u.identifier=t.sender_id
                ";
        $whereQuery=" where t.sender_id='".$user."' and u.type='".$type."' Group By t.sender_id,m.id ORDER BY m.created_at DESC";
        $msg_query="select m.id as messageId,ticket_id,recipient_id,email, title as Subject,content,DATE_FORMAT(m.created_at , '%d/%m/%Y') as receivedDate,m.status from
                  ".$this->_name." t ".$join.$whereQuery ;
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";

    }
    public function getUserTypeTicket($ticketId,$identifier)
    {

        $where=" where id='".$ticketId."' Limit 0,1";
        $checkTypeQuery="select sender_id ,recipient_id from ".$this->_name.$where;

        if(($result=$this->getQuery($checkTypeQuery,true))!=NULL)
        {
            if($result[0]['sender_id']==$identifier)
            {
                $result[0]['usertype']='sender';
                return $result;
            }

            else if($result[0]['recipient_id']==$identifier)
            {    
                $result[0]['usertype']='recipient';
                return $result;
            }

        }
        else
            return "not exist";



    }
    public function getTicketDetails($ticketId,$identifier)
    {
        $details=$this->getUserTypeTicket($ticketId,$identifier);
        if(is_array($details))
        {
            if($details[0]['usertype']=='recipient')
            {
                $join=" LEFT JOIN UserPlus up ON up.user_id=t.sender_id
                        INNER JOIN User u ON u.identifier=t.sender_id";

                $where=" where t.id='".$ticketId."' and t.sender_id='".$details[0]['sender_id']."' LIMIT 0,1";
                $ticket_query="select CONCAT(up.first_name,' ',up.last_name) as username ,u.email, sender_id as userid,title as Subject,t.id as ticketid from ".$this->_name." t ".$join.$where;
            }
            else
            {
                 $join=" LEFT JOIN UserPlus up ON up.user_id=t.recipient_id
                        INNER JOIN User u ON u.identifier=t.recipient_id";

                $where=" where t.id='".$ticketId."' and t.recipient_id='".$details[0]['recipient_id']."' LIMIT 0,1";
                $ticket_query="select CONCAT(up.first_name,' ',up.last_name) as username ,u.email, recipient_id as userid,title as Subject,t.id as ticketid as Subject from ".$this->_name." t ".$join.$where;
            }
            if(($ticket_details=$this->getQuery($ticket_query,true))!=NULL)
            {
                return $ticket_details;
            }
            else
                return "NO";
        }
        else
           return "NO";

    }
    public function updateTicketStatus($ticketID,$data)
    {
        $where=" id='".$ticketID."'";

        $this->updateQuery($data,$where);
    }
}

