<?php
/**
 * Ep_Ticket_Message
 * @author Arun
 * @package Message
 * @version 1.0
 */
class Ep_Ticket_Message extends Ep_Db_Identifier
{
	protected $_name = 'Message';
	private $id;
	private $ticket_id;
	private $content;
	private $type;
	private $status;
	private $created_at;
    private $attachment;
    private $approved;

    private $bo_user_type;
    private $auto_mail;

    private $midentifier;

	public function loadData($array)
	{
		$this->id=$array["id"] ;
		$this->ticket_id=$array["ticket_id"];
		$this->content=$array["content"];
		$this->type=$array["type"] ;
		$this->status=$array["status"] ;
		$this->created_at=$array["created_at"] ;
		$this->attachment=$array["attachment"] ;
        $this->approved=$array["approved"] ;

        $this->bo_user_type=$array["bo_user_type"] ;
        $this->auto_mail=$array["auto_mail"];

    
		
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["id"] = $this->getIdentifier();
		$array["ticket_id"] = $this->ticket_id;
		$array["content"] = $this->content;
		$array["type"] = $this->type;
		$array["status"] = $this->status;
		$array["created_at"] = $this->created_at;
		$array["attachment"] = $this->attachment;
        $array["approved"] = $this->approved;

        $array["bo_user_type"]=$this->bo_user_type;
        //$array["auto_mail"]=$this->auto_mail;
		
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    
    public function checkMessageInbox($identifier,$messageId,$ticketId)

    {
        $joinQuery=" INNER JOIN Ticket t ON t.id=m.ticket_id

                     INNER JOIN User u ON u.identifier=t.recipient_id  OR u.identifier=t.sender_id  ";

        $whereQuery=" Where m.id='".$messageId."' and m.ticket_id='".$ticketId."' and
                      ((t.recipient_id='".$identifier."' and m.type='0') OR (t.sender_id='".$identifier."' and m.type='1'))
                      Group By t.sender_id,t.recipient_id,m.id ";

        $checkQuery="select IF(m.type='0',sender_id,recipient_id) as userid,u.type as user_type,
                    email, title as Subject,content,m.created_at as receivedDate,m.* from ".$this->_name." m ".$joinQuery.$whereQuery;

        //echo $checkQuery;exit;

        if(($result=$this->getQuery($checkQuery,true))!=NULL)
		{
			if($result[0]['id']!=NULL)
				return $result;
			else
				return NULL;
		}
    }
    public function checkMessageSentbox($identifier,$messageId,$ticketId)
    {
        $joinQuery=" INNER JOIN Ticket t ON t.id=m.ticket_id

                     INNER JOIN User u ON u.identifier=t.recipient_id  OR u.identifier=t.sender_id  ";

        $whereQuery=" Where m.id='".$messageId."' and m.ticket_id='".$ticketId."' and
                      ((t.recipient_id='".$identifier."' and m.type='1') OR (t.sender_id='".$identifier."' and m.type='0'))
                      Group By t.sender_id,t.recipient_id,m.id ";

        $checkQuery="select IF(m.type='0',recipient_id,sender_id) as userid,u.type as user_type,email, title as Subject,content,
        m.created_at  as receivedDate,m.* from ".$this->_name." m ".$joinQuery.$whereQuery;

        

        //echo $checkQuery;exit;

        if(($result=$this->getQuery($checkQuery,true))!=NULL)
		{
			if($result[0]['id']!=NULL)
				return $result;
			else
				return NULL;
		}
    }
    public function checkMessageClass($identifier,$messageId,$ticketId)
    {
           $joinQuery=" INNER JOIN Ticket t ON t.id=m.ticket_id

                     INNER JOIN User u ON u.identifier=t.recipient_id  OR u.identifier=t.sender_id  ";

        $whereQuery=" Where m.id='".$messageId."' and m.ticket_id='".$ticketId."' and
                      (t.recipient_id='".$identifier."'  OR t.sender_id='".$identifier."') and t.status in ('2','3')
                      Group By t.sender_id,t.recipient_id,m.id ";

        $checkQuery="select sender_id,recipient_id,IF(m.type='1',recipient_id,sender_id) as userid,email, title as Subject,content,
        m.created_at as receivedDate,m.* from ".$this->_name." m ".$joinQuery.$whereQuery;



        //echo $checkQuery;exit;

        if(($result=$this->getQuery($checkQuery,true))!=NULL)
		{
			if($result[0]['id']!=NULL)
				return $result;
			else
				return NULL;
		}

    }
    public function updateMessageStatus($messageId,$status='1')
    {
        $message['status']=$status;

        $where=" id='".$messageId."'";

        $this->updateQuery($message,$where);
    }
    public function getAttachmentName($messageID)
    {
        $join= " INNER JOIN Ticket t ON t.id=m.ticket_id";
        $where=" Where m.id='".$messageID."'";
        $attachmentQuery="select m.attachment,m.id from ".$this->_name." m ".$join.$where;

        if(($result=$this->getQuery($attachmentQuery,true))!=NULL)
		{
			if($result[0]['id']!=NULL)
				return $result;
			else
				return NULL;
		}

    }
    public function Message()
    {
        
        $this->createIdentifier();
    }
    public function getIdentifier()
    {
        return $this->midentifier;
    }   
    
    public function createIdentifier()
    {
         $s=new String();
        $this->midentifier=$s->randomString(15);
    }
       
}

