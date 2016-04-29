<?php
/**
 * Ep_Ticket_Message
 * @author Admin
 * @package Message
 * @version 1.0
 */
class Ep_Message_Message extends Ep_Db_Identifier
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
    private $bo_replied_user;
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
        $this->bo_replied_user=$array["bo_replied_user"];
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
        $array["bo_replied_user"]=$this->bo_replied_user;
        $array["auto_mail"]=$this->auto_mail;


		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
	
	/* public function createIdentifier()
    {
        $d = new Date();
		//return $d->getSubDate(5,14).mt_rand(100000,999999);
		return $d->getSubDate(2,14).rand(100,999);
  	} */
	
    public function checkMessageInbox($identifier,$messageId,$ticketId)

    {
        $this->adminLogin = Zend_Registry::get('adminLogin');
        $type=$this->adminLogin->type;

        $joinQuery=" INNER JOIN Ticket t ON t.id=m.ticket_id

                     INNER JOIN User u ON u.identifier=t.recipient_id  OR u.identifier=t.sender_id  ";

        $whereQuery=" Where m.id='".$messageId."' and m.ticket_id='".$ticketId."' and
                      (((t.recipient_id='".$identifier."' and m.type='0') OR (t.sender_id='".$identifier."' and m.type='1'))
                      OR(m.bo_user_type='".$type."' and (m.locked_user is NULL OR m.locked_user='".$identifier."')))
                      Group By t.sender_id,t.recipient_id,m.id ";

        $checkQuery="select IF(m.type='0',sender_id,recipient_id) as userid,
                    email, title as Subject,content,DATE_FORMAT(m.created_at , '%d/%m/%Y %h:%i %p') as receivedDate,m.* from ".$this->_name." m ".$joinQuery.$whereQuery;

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
                      (((t.recipient_id='".$identifier."' and m.type='1') OR (t.sender_id='".$identifier."' and m.type='0'))
                      OR(m.bo_replied_user='".$identifier."'))
                      Group By t.sender_id,t.recipient_id,m.id ";

        $checkQuery="select IF(m.type='0',recipient_id,sender_id) as userid,email, title as Subject,content,
        DATE_FORMAT(m.created_at , '%d/%m/%Y %h:%i %p') as receivedDate,m.* from ".$this->_name." m ".$joinQuery.$whereQuery;



        //echo $checkQuery;exit;

        if(($result=$this->getQuery($checkQuery,true))!=NULL)
		{
			if($result[0]['id']!=NULL)
				return $result;
			else
				return NULL;
		}
    }
    public function getMessage($messageId)
    {
         $query = "select content from ".$this->_name." where id=".$messageId;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
    }
    public function getClientMessage($messageId)
    {
           $query = "select content from ".$this->_name." where id LIKE '".$messageId."'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    //////////get message count of messages which are to be validated by bo users//////////////
    public function getToBeValidatedMessages()
    {
        $query = "select count(id) AS messagecount from ".$this->_name." where Approved='no'";
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
    }
    public function getRefuseMessageDetails($messageId)
    {
        $query = "SELECT m.id, t.sender_id, t.title, up.first_name, up.last_name FROM ".$this->_name." m
                   INNER JOIN Ticket t ON t.id=m.ticket_id
                   INNER JOIN UserPlus up ON t.sender_id=up.user_id WHERE m.id=".$messageId;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
    }
    public function checkMessageClass($identifier,$messageId,$ticketId)
    {
        $this->adminLogin = Zend_Registry::get('adminLogin');
        $type=$this->adminLogin->type;
        $joinQuery=" INNER JOIN Ticket t ON t.id=m.ticket_id

                     INNER JOIN User u ON u.identifier=t.recipient_id  OR u.identifier=t.sender_id  ";

        $whereQuery=" Where m.id='".$messageId."' and m.ticket_id='".$ticketId."' and
                      (t.recipient_id='".$identifier."' OR t.sender_id='".$identifier."'OR m.bo_user_type IS NULL OR  m.bo_user_type='".$type."' OR m.locked_user='".$identifier."')
                       and t.status in ('2','3')
                      Group By t.sender_id,t.recipient_id,m.id ";

        $checkQuery="select sender_id,recipient_id,IF(m.type='1',recipient_id,sender_id) as userid,email, title as Subject,content,
        DATE_FORMAT(m.created_at , '%d/%m/%Y %h:%i %p') as receivedDate,m.* from ".$this->_name." m ".$joinQuery.$whereQuery;

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
    public function checkMessageApprove($messageId,$ticketId)

    {
        $joinQuery=" INNER JOIN Ticket t ON t.id=m.ticket_id

                     INNER JOIN User u ON u.identifier=t.recipient_id  OR u.identifier=t.sender_id  ";

        $whereQuery=" Where m.id='".$messageId."' and m.ticket_id='".$ticketId."' and
                      (m.type='1' OR m.type='0') and t.status in ('0','1') and m.approved IS NULL
                      Group By t.sender_id,t.recipient_id,m.id ";

        $checkQuery="select IF(m.type='0',sender_id,recipient_id) as senderId,
                        IF(m.type='0',recipient_id,sender_id) as recipientID,
                    email, title as Subject,content,DATE_FORMAT(m.created_at , '%d/%m/%Y %h:%i %p') as receivedDate,m.* from ".$this->_name." m ".$joinQuery.$whereQuery;

        //echo $checkQuery;exit;

        if(($result=$this->getQuery($checkQuery,true))!=NULL)
		{
			if($result[0]['id']!=NULL)
				return $result;
			else
				return NULL;
		}
    }
     public function approveMessage($messageID,$data)
    {
        $where=" id='".$messageID."'";
        //print_r($data);exit;
        $this->updateQuery($data,$where);
    }
    public function updateBoReplyStatus($messageID,$data)
    {
        $where=" id='".$messageID."'";
        //print_r($data);exit;
        $this->updateQuery($data,$where);
    }
    /**check lock status of message**/
    public function checkLockstatus($messageId,$type)
    {
        $statusQuery="select bo_user_type,locked_user from Message where bo_user_type='".$type."' and id='".$messageId."'";
        //echo $statusQuery;
        if(($result=$this->getQuery($statusQuery,true))!=NULL)
		{
            if($result[0]['locked_user']!='')
				return $result[0]['locked_user'];
           	else
                return "unlocked";

		}
    }
    /**check lock status of message for Master User**/
    public function checkMasterLockstatus($messageId)
    {
        $statusQuery="select bo_user_type,locked_user from Message where id='".$messageId."'";
        //echo $statusQuery;
        if(($result=$this->getQuery($statusQuery,true))!=NULL)
		{
            if($result[0]['locked_user']!='')
				return $result[0]['locked_user'];
           	else
                return "unlocked";

		}
    }
    /**update Lock status**/
    public function updateLockstatus($messageID,$data)
    {
        $where=" id='".$messageID."'";
        //print_r($data);exit;
        $this->updateQuery($data,$where);
    }
    /**For Master Mail Account**/
    public function MastercheckMessageInbox($messageId,$ticketId)

    {
        $joinQuery=" INNER JOIN Ticket t ON t.id=m.ticket_id

                     INNER JOIN User u ON u.identifier=t.recipient_id  OR u.identifier=t.sender_id  ";

        $whereQuery=" Where m.id='".$messageId."' and m.ticket_id='".$ticketId."' and
                      (m.type='0'OR m.type='1')
                      Group By t.sender_id,t.recipient_id,m.id ";

        $checkQuery="select IF(m.type='0',sender_id,recipient_id) as userid,IF(m.type='1',sender_id,recipient_id) as recipientid,
                    email, title as Subject,content,DATE_FORMAT(m.created_at , '%d/%m/%Y %h:%i %p') as receivedDate,m.* from ".$this->_name." m ".$joinQuery.$whereQuery;

        //echo $checkQuery;exit;

        if(($result=$this->getQuery($checkQuery,true))!=NULL)
		{
			if($result[0]['id']!=NULL)
				return $result;
			else
				return NULL;
		}
    }
    public function getRecipientId($messageId)
    {
        $recipientQuery="select  IF(m.type='0',recipient_id,sender_id) as UserId From Message m
                        INNER JOIN Ticket t ON t.id=m.ticket_id
                        INNER JOIN User u ON u.identifier=t.recipient_id
                        INNER JOIN User u1 ON u1.identifier=t.sender_id
                        WHERE m.id='".$messageId."' and (u.type in ('client','contributor') OR u1.type in ('client','contributor'))";

        //echo $recipientQuery;exit;

        if(($result=$this->getQuery($recipientQuery,true))!=NULL)
		{
			if($result[0]['UserId']!=NULL)
				return $result[0]['UserId'];
			else
				return NULL;
		}
    }

	public function insertMessage($Marray)
	{
		$Marray['id']=strtotime('now');
		$this->insertQuery($Marray);
		return $Marray['id'];
	}
	
	 public function Message()
    {
        
        $this->createIdentifier();
    }
    public function getIdentifier()
    {
        return $this->midentifier;
    }
    public function getRecentMessageId()
    {
        $statusQuery="SELECT id FROM ".$this->_name." ORDER BY created_at DESC limit 1";
        if(($result=$this->getQuery($statusQuery,true))!=NULL)
            return $result;
        else
            return false;
    }
    public function createIdentifier()
    {
         $s=new String();
        $this->midentifier=$s->randomString(15);
    }
}

