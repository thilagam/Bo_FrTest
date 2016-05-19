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
class Ep_Message_Ticket extends Ep_Db_Identifier
{
	protected $_name = 'Ticket';
	private $id;
	private $sender_id;
	private $recipient_id;
    private $group_id;
	private $title;
	private $template_type;
	private $status;
	private $created_at;
    private $updated_at;
    private $bo_user_action_type;
	
	private $tidentifier;

	public function loadData($array)
	{
		$this->id=$array["id"] ;
		$this->sender_id=$array["sender_id"];
		$this->recipient_id=$array["recipient_id"];
		$this->group_id=$array["group_id"];
		$this->title=$array["title"] ;
		$this->template_type=$array["template_type"] ;
		$this->status=$array["status"] ;
		$this->created_at=$array["created_at"] ;
		$this->updated_at=$array["updated_at"] ;
        $this->bo_user_action_type=$array["bo_user_action_type"] ;
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["id"] = $this->getIdentifier();
		$array["sender_id"] = $this->sender_id;
        $array["recipient_id"] = $this->recipient_id;
        $array["group_id"] = $this->group_id;
		$array["title"] = $this->title;
		$array["template_type"] = $this->template_type;
		$array["status"] = $this->status;
		$array["created_at"] = $this->created_at;
		$array["updated_at"] = $this->updated_at;
        $array["bo_user_action_type"]=$this->bo_user_action_type ;
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
                    u.identifier,CONCAT(first_name,' ',last_name) as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                where u.type='".$type."' and u.status='Active' AND u.blackstatus='no' and u.verified_status='YES'
                Group BY u.identifier,contact_name
                ORDER BY contact_name,u.email
                ";
       // echo $query;exit;
        if(($clients=$this->getQuery($query,true))!=NULL)
            return $clients;
        else
            return "Not Exists";

    }
    public function getEPContacts($type)
    {
        
        /* $query="select
                    u.identifier,CONCAT(first_name,' ',last_name) as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                where  FIELD(`type`, ".$type.")
				and u.status='Active'
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                "; */
		$query="select
                    u.identifier,CONCAT(first_name,' ',last_name) as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                where  u.type not in ('client','contributor')
				and u.status='Active' AND u.blackstatus='no' 
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                ";		
       //echo $query;exit;
        if(($ep_users=$this->getQuery($query,true))!=NULL)
            return $ep_users;
        else
            return "Not Exists";

    }
    public function getEPContactsMaster($type)
    {
        /* $query="select
                    u.identifier,CONCAT(first_name,' ',last_name) as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
               where  u.type not in ('client','contributor')
				and u.status='Active' and u.email in('sales@edit-palce.com','partner@edit-place.com','care@edit-place.com','facturation@edit-place.com')
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                "; */
		$query="select
                    u.identifier, CONCAT(first_name,' ',last_name) as contact_name, u.login as login_name, u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
               where  u.type not in ('client','contributor','chiefodigeo','superclient')
				and u.status='Active' AND u.blackstatus='no'
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                ";		
       //echo $query;exit;
        if(($ep_users=$this->getQuery($query,true))!=NULL)
            return $ep_users;
        else
            return "Not Exists";

    }
    public function getApproveMails()
    {
        $msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,
                        IF(m.type='0',sender_id,recipient_id) as senderId,
                        IF(m.type='0',recipient_id,sender_id) as recipientID,

                        email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%h:%i %p'),DATE_FORMAT(m.created_at , '%d/%m/%Y %h:%i %p')) as receivedDate,
                        m.created_at as receivedDate_sort,
						m.status
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id

                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                    where (m.type='1' OR m.type='0') and t.status in ('0','1') and m.approved IS NULL
                    Group By t.sender_id,t.recipient_id,m.id ORDER BY m.created_at ASC";
       // echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }

   
	function getUserName($user)
	{
		//$senderQuery="select CONCAT(first_name,' ',last_name) as sendername ,email from User u LEFT JOIN UserPlus ON identifier=user_id where identifier='".$user."'";
		
		$senderQuery="select IF(u.type='client',company_name,CONCAT(first_name,' ',last_name)) as sendername ,email,CONCAT(first_name,' ',last_name) as full_name from User u
		                    LEFT JOIN UserPlus up ON u.identifier=up.user_id
		                    LEFT JOIN Client c ON u.identifier=c.user_id
		                    where identifier='".$user."'";
		
		//echo $senderQuery;exit;
		if(($result=$this->getQuery($senderQuery,true))!=NULL)
		{
			if($result[0]['sendername']!=NULL)
				return $result[0]['sendername'];
			else
            {
				$email=explode("@",$result[0]['email']);
                return $email[0];
            }
		}

	}
    function getLoginName($user)
	{
		$senderQuery="select CONCAT(first_name,' ',last_name) as sendername ,email,login,u.type from User u LEFT JOIN UserPlus ON identifier=user_id where identifier='".$user."'";
		//echo $senderQuery;exit;
		if(($result=$this->getQuery($senderQuery,true))!=NULL)
		{
			return $result;
		}

	}
    
    public function getUserTypeTicket($ticketId,$identifier)
    {

        $where=" where id='".$ticketId."' Limit 0,1";
        $checkTypeQuery="select sender_id ,recipient_id,bo_user_action_type,title from ".$this->_name.$where;
    //echo $checkTypeQuery."--".$identifier;exit;
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
            else if($result[0]['bo_user_action_type'])
            {
                $result[0]['usertype']=$result[0]['bo_user_action_type'];
                $result[0]['mail_group']='yes';
                return $result;
            }
            else
            {
                return $result;
            }

        }
        else
            return "NO";



    }
    public function getTicketDetails($ticketId,$identifier)
    {
        $details=$this->getUserTypeTicket($ticketId,$identifier);
         $this->adminLogin = Zend_Registry::get('adminLogin');
        $type=$this->adminLogin->type;
        //print_r($details);
        if(is_array($details))
        {
            if($details[0]['usertype']=='recipient' && !$details[0]['mail_group'])
            {
                $join=" INNER JOIN UserPlus up ON up.user_id=t.sender_id
                        INNER JOIN User u ON u.identifier=t.sender_id";

                $where=" where t.id='".$ticketId."' and t.sender_id='".$details[0]['sender_id']."' LIMIT 0,1";
                $ticket_query="select CONCAT(up.first_name,' ',up.last_name) as username ,u.email,u.identifier,u.type as user_type, sender_id as userid,title as Subject,t.id as ticketid from ".$this->_name." t ".$join.$where;
            }
            else if($details[0]['usertype']=='sender' && !$details[0]['mail_group'])
            {
                 $join=" INNER JOIN UserPlus up ON up.user_id=t.recipient_id
                        INNER JOIN User u ON u.identifier=t.recipient_id";

                $where=" where t.id='".$ticketId."' and t.recipient_id='".$details[0]['recipient_id']."' LIMIT 0,1";
                $ticket_query="select CONCAT(up.first_name,' ',up.last_name) as username ,u.email,u.identifier,u.type as user_type, recipient_id as userid,title as Subject,t.id as ticketid from ".$this->_name." t ".$join.$where;
            }
            else
            {
                $join=" INNER JOIN UserPlus up ON up.user_id=t.sender_id
                        INNER JOIN UserPlus up1 ON up1.user_id=t.recipient_id
                        INNER JOIN User u ON u.identifier=t.sender_id
                        INNER JOIN User u1 ON u1.identifier=t.recipient_id
                        INNER JOIN Message m on m.ticket_id=t.id";

                $where=" where t.id='".$ticketId."' and (m.bo_user_type='".$type."' and (m.locked_user is NULL OR m.locked_user='".$identifier."')) LIMIT 0,1";
                $ticket_query="select CONCAT(up.first_name,' ',up.last_name) as username ,u.email,u.identifier,u.type as user_type, recipient_id as userid,title as Subject,t.id as ticketid from ".$this->_name." t ".$join.$where;
                
            }
            //echo $ticket_query;exit;
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
    public function getUnreadCount($type,$user)
    {
       /*  if($type=='superadmin')
			$condition="((t.recipient_id in (select identifier from User where type not in('client','contributor') and m.type='0')) OR (t.sender_id in (select identifier from User where type not in('client','contributor')  and m.type='1'))) ";
		else */
			$condition=" ((t.recipient_id='".$user."' and m.type='0') OR (t.sender_id='".$user."' and m.type='1')) and u.type='".$type."'  ";
		$msg_query="select
                        count( DISTINCT m.id ) as msgCount
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id

                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                    where $condition and m.status='0' and t.status in ('1','0') and m.auto_mail!='yes'					
                    ";
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result[0]['msgCount'];
        else
            return 0;

    }
	//archieve tickets
	public function MasterArchieveTicket($user=NULL)
    {
        
		if($user)
			$condition="(t.recipient_id='".$user."' OR t.sender_id='".$user."') AND";

         $msg_query="select
                            DISTINCT t.id as ticket_id,title as Subject,t.status,t.updated_at,t.bo_user_action_type,
                            t.classified_by,t.sender_id,t.recipient_id
                     from   Ticket t
                     INNER JOIN Message m ON m.ticket_id=t.id
                     where $condition t.status in ('2','3') order by t.updated_at DESC";
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }
	//master archieve mails box	
    public function MasterArchieveMails($ticket,$user=NULL)
    {
        if($user)
			$condition="(t.recipient_id='".$user."' OR t.sender_id='".$user."') and ";
		
		$msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,
                        IF(m.type='1',recipient_id,sender_id) as userid,
						IF(m.type='1',sender_id,recipient_id) as receiverId,
						email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%h:%i %p'),DATE_FORMAT(m.created_at , '%d/%m/%Y %h:%i %p')) as receivedDate,
                         m.status
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id 
                    where $condition
                         t.id='".$ticket."' and
                         t.status in ('2','3')
                         Group By t.sender_id,t.recipient_id,m.id ORDER BY m.created_at ASC";
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }
    public function getUserReplyMails($ticket,$user)
    {
        $this->adminLogin = Zend_Registry::get('adminLogin');
        $type=$this->adminLogin->type;
        $msg_query="select
                        m.id as messageId,m.type,m.attachment,ticket_id,sender_id,recipient_id,
                        IF(m.type='1',recipient_id,sender_id) as userid,bo_replied_user,u.email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%h:%i %p'),m.created_at) as receivedDate,
                         m.status,m.bo_replied_user
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id
                        INNER JOIN User u1 ON u1.identifier=t.sender_id
                    where
                         t.id='".$ticket."' and
                         t.status in ('0','1') and m.approved='yes'
                         Group By t.sender_id,t.recipient_id,m.id ORDER BY m.created_at DESC";
//(t.recipient_id='".$user."' OR t.sender_id='".$user."') and
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }    

    /***Get Master Inbox***/
    public function getMasterInbox($user=NULL)
    {

       if($user)
           $condition="((t.recipient_id='".$user."' and m.type='0') OR (t.sender_id='".$user."' and m.type='1'))";
       else
            $condition="(m.type='0' OR  m.type='1')";

      $msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,IF(m.type='1',recipient_id,sender_id) as userid,
                        IF(m.type='1',sender_id,recipient_id) as receiverId,
                        email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%H:%i'),DATE_FORMAT(m.created_at , '%d/%m/%Y %H:%i')) as receivedDate,
                        m.status  from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                    where ".$condition." and t.status in ('0','1')
                    Group By t.sender_id,t.recipient_id,m.id ORDER BY  m.created_at DESC";
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";

    }
    /***Get Master Inbox OF all EP Users***/
    public function getMasterInboxEP($user=NULL)
    {

       if($user)
           $condition="((t.recipient_id='".$user."' and m.type='0') OR (t.sender_id='".$user."' and m.type='1'))";
       else
            $condition="((t.recipient_id in (select identifier from User where type not in('client','contributor') and m.type='0')) OR (t.sender_id in (select identifier from User where type not in('client','contributor')  and m.type='1')))";

      /*$msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,IF(m.type='1',recipient_id,sender_id) as userid,
                        IF(m.type='1',sender_id,recipient_id) as receiverId,m.bo_user_type,m.locked_user,
                        u.email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%H:%i'),DATE_FORMAT(m.created_at , '%d/%m/%Y %H:%i')) as receivedDate,
                        m.created_at as receivedDate_sort,t.assigned_before,
						m.status  from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id
                        INNER JOIN User u1 ON u1.identifier=t.sender_id
                    where ".$condition." and t.status in ('0','1') and m.bo_replied_staus!='yes'
                    Group By t.sender_id,t.recipient_id,m.id ORDER BY  m.created_at DESC"; */
        /* *** optimized on 18.05.2016 *** */
        $msg_query= "select
                          (
						  select IF(u.type='client',company_name,CONCAT(first_name,' ',last_name)) as sendername from User u
		                    LEFT JOIN UserPlus up ON u.identifier=up.user_id
		                    LEFT JOIN Client c ON u.identifier=c.user_id
		                    where identifier= (IF(m.type='1',recipient_id,sender_id))
                          ) as sendername,
						 (
						 select IF(u.type='client',company_name,CONCAT(first_name,' ',last_name)) as sendername from User u
		                    LEFT JOIN UserPlus up ON u.identifier=up.user_id
		                    LEFT JOIN Client c ON u.identifier=c.user_id
		                    where identifier= (IF(m.type='1',sender_id,recipient_id))
						 ) as recipient,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%H:%i'),DATE_FORMAT(m.created_at , '%d/%m/%Y %H:%i')) as receivedDate,
                        m.id as messageId,m.type,m.ticket_id,t.sender_id,t.recipient_id,
                        m.bo_user_type,(
						select login
						from User u
						where identifier= m.locked_user
						)AS locked_user_login,
                        t.title as Subject,m.content,
                        m.created_at as receivedDate_sort,t.assigned_before,
						m.status  from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        where
                        ".$condition." and t.status in ('0','1') and m.bo_replied_staus!='yes'
                        Group By t.sender_id,t.recipient_id,m.id
                        ORDER BY  m.created_at DESC";

        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";

    }
    /***Get Master Inbox OF all FO Users***/
    public function getMasterInboxFOUsers($user=NULL)
    {

       if($user)
            $condition="m.auto_mail!='yes' and ((t.recipient_id='".$user."' and m.type='1') OR (t.sender_id='".$user."' and m.type='0'))";
           //$condition="m.auto_mail!='yes' and ((t.recipient_id='".$user."' and m.type='0') and (t.sender_id in (select identifier from User where type not in('client','contributor'))))";
       else
            $condition="m.auto_mail!='yes' and ((t.recipient_id in (select identifier from User where type in('client','contributor') and m.type='0')) and (t.sender_id in (select identifier from User where type not in('client','contributor'))))
                        OR ((t.sender_id in (select identifier from User where type in('client','contributor') and m.type='1')) and (t.recipient_id in (select identifier from User where type not in('client','contributor'))))";

      $msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,IF(m.type='1',recipient_id,sender_id) as userid,
                        IF(m.type='1',sender_id,recipient_id) as receiverId,
                        u.email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%H:%i'),DATE_FORMAT(m.created_at , '%d/%m/%Y %H:%i')) as receivedDate,
                         m.created_at as receivedDate_sort,
                        m.status  from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id
                        INNER JOIN User u1 ON u1.identifier=t.sender_id
                    where ".$condition." and t.status in ('0','1') 
                    Group By t.sender_id,t.recipient_id,m.id ORDER BY  m.created_at DESC";

        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";

    }
    /***Get Master Inbox OF all FO Users***/
    public function getMasterInboxFOUsersFiltered($parmas, $user_type, $user_id)
    {
        if($user_id)
            $parmas['ep_user_id'] = $user_id;
            
        //echo "<pre>";print_r($parmas);//exit();
            
       if(($parmas['type']=='contributor') || ($parmas['type']=='contributor' && ($parmas['language'] || $parmas['category'] || $parmas['crtype'] ||$parmas['wrtype'])))
       {//echo '#1#';
           if($parmas['language'] || $parmas['category'])
           {
               $languageCond = ($parmas['language'] ? " AND c.language ='".$parmas['language']."'" : "");
               //$categoryCond = ($parmas['category'] ? " AND c.favourite_category LIKE '%".$parmas['category']."%'" : "");
               
               $categoryCond = ((sizeof($parmas['categ'])>0) ? " AND (c.favourite_category LIKE '%" . implode("%' OR c.favourite_category LIKE '%", array_keys($parmas['categ'])) . "%')" : "");
               $userIdCond = ($parmas['ep_user_id'] ? " AND identifier='".$parmas['ep_user_id']."'" : "");
               
               if($parmas['wrtype'])
                    $wrCond = ((sizeof($parmas['wrtype'])>0) ? (" AND u.profile_type IN ('".implode("','", $parmas['wrtype'])."') ") : "1=1");
               if($parmas['crtype'])
                    $crCond = ((sizeof($parmas['crtype'])>0) ? (" AND u.profile_type2 IN ('".implode("','", $parmas['crtype'])."') ") : "1=1");
               
               $condition="m.auto_mail!='yes' and ((t.recipient_id in (select u.identifier from User u INNER JOIN Contributor c ON u.identifier=c.user_id where u.type in('contributor'){$languageCond}{$categoryCond} {$wrCond} {$crCond}) and m.type='0' ) and (t.sender_id in (select identifier from User where type not in('client','contributor') {$userIdCond}))) OR ((t.sender_id in (select u.identifier from User u INNER JOIN Contributor c ON u.identifier=c.user_id where u.type in('contributor'){$languageCond}{$categoryCond} {$wrCond} {$crCond}) and m.type='1') and (t.recipient_id in (select identifier from User where type not in('client','contributor') {$userIdCond}))) ";
           }
           else
           {
               $wrCond = ((sizeof($parmas['wrtype'])>0) ? (" u.profile_type IN ('".implode("','", $parmas['wrtype'])."') ") : "1=1");
               $crCond = ((sizeof($parmas['crtype'])>0) ? (" profile_type2 IN ('".implode("','", $parmas['crtype'])."') ") : "1=1");
               
               $userIdCond = ($parmas['ep_user_id'] ? " AND identifier='".$parmas['ep_user_id']."'" : "");
               
               $condition="m.auto_mail!='yes' and ((t.recipient_id in (select identifier from User where type in('contributor') AND {$wrCond} AND {$crCond}) and m.type='0') and (t.sender_id in (select identifier from User where type not in('client','contributor'){$userIdCond}))) OR ((t.sender_id in (select identifier from User where type in('contributor') AND {$wrCond} AND {$crCond} ) and m.type='1') and (t.recipient_id in (select identifier from User where type not in('client','contributor'){$userIdCond})))";
           }
       }
       /*elseif($user_id)
       {
            $condition="((t.recipient_id='".$parmas['ep_user_id']."' and m.type='1') OR (t.sender_id='".$parmas['ep_user_id']."' and m.type='0') OR (m.bo_replied_user='".$parmas['ep_user_id']."')) AND (u.type='".$parmas['type']."' OR u1.type='".$parmas['type']."')";
       }*/
       else if($parmas['ep_user_id'] && ($parmas['type']=='contributor' || $parmas['type']=='client')  && $parmas['ep_user_id']!='all')
       {
           //echo '#2#';
            $condition="m.auto_mail!='yes' and ((t.recipient_id='".$parmas['ep_user_id']."' and m.type='1') OR (t.sender_id='".$parmas['ep_user_id']."' and m.type='0')) AND (u.type='".$parmas['type']."' OR u1.type='".$parmas['type']."')";
       }
       else if($parmas['type']=='client')
       {//echo '#3#';
           $condition="m.auto_mail!='yes' and ((t.recipient_id in (select identifier from User where type in('client')) and m.type='0') and (t.sender_id in (select identifier from User where type not in('client','contributor'))))
                        OR ((t.sender_id in (select identifier from User where type in('client')) and m.type='1') and (t.recipient_id in (select identifier from User where type not in('client','contributor'))))                        
             ";
       }
       else if($parmas['ep_user_id'] && $parmas['ep_user_id']!='all' )
       {
           //echo '#4#';
            $condition="m.auto_mail!='yes' and ((t.recipient_id='".$parmas['ep_user_id']."' and m.type='1') OR (t.sender_id='".$parmas['ep_user_id']."' and m.type='0'))";
       }
       else
            $condition="m.auto_mail!='yes' and ((t.recipient_id in (select identifier from User where type in('client','contributor') and m.type='0')) and (t.sender_id in (select identifier from User where type not in('client','contributor'))))
                        OR ((t.sender_id in (select identifier from User where type in('client','contributor') and m.type='1')) and (t.recipient_id in (select identifier from User where type not in('client','contributor'))))                        
             ";

      $msg_query="select ".((sizeof($parmas['categ'])>0) ? " cb.category_more, " : "")."
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,IF(m.type='1',recipient_id,sender_id) as userid,
                        IF(m.type='1',sender_id,recipient_id) as receiverId,
                        u.email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%H:%i'),DATE_FORMAT(m.created_at , '%d/%m/%Y %H:%i')) as receivedDate,
                         m.created_at as receivedDate_sort,
                        m.status  from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id
                        INNER JOIN User u1 ON u1.identifier=t.sender_id ".
                   ((sizeof($parmas['categ'])>0) ? " LEFT JOIN Contributor cb ON u.identifier=cb.user_id" : "")
                   ." where ".$condition." and t.status in ('0','1') 
                    Group By t.sender_id,t.recipient_id,m.id ORDER BY  m.created_at DESC";

       //echo $msg_query."</pre>";
       //exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
       {
           if(sizeof($parmas['categ'])>0)
            {
                foreach($parmas['categ'] as $key => $value)
                {
                    $categ1[]=  $key."=".$value;
                }
                for($i=0; $i<count($result); $i++)
                {
                    $final_array[$i]=$result[$i];
                    if($result[$i]['category_more'] != '')
                    {
                        $result[$i]['cates'] = unserialize($result[$i]['category_more']);
                    }
                    for($j=0; $j<count($categ1); $j++)
                    {
                        $catdetails = explode("=",$categ1[$j]);
                        $catindex = $catdetails[0];
                        $catvalue = $catdetails[1];
                        if($result[$i]['cates'] != '')
                        {
                            if(array_key_exists($catindex, $result[$i]['cates']))
                            {
                                if($result[$i]['cates'][$catindex] >= $catvalue)
                                {
                                    unset($final_array[$i]);
                                    break;
                                }
                            }
                            else
                            {
                                unset($final_array[$i]);
                                break;
                            }
                        }
                        else
                        {
                            unset($final_array[$i]);
                            break;
                        }
                    }

                }
                //print_r($final_array);exit;
                if($final_array != NULL)
                {
                    $result = array_values($final_array);
                }
            }
            return $result;
       }
       else
           return "No Messages Found";

    }
	public function getMasterSentBox($type,$user,$where=NULL)
    {
        /*$join=" INNER JOIN Message m ON m.ticket_id=t.id
                LEFT JOIN UserPlus up ON up.user_id=t.sender_id OR up.user_id=t.recipient_id
                INNER JOIN User u ON u.identifier=t.sender_id OR u.identifier=t.recipient_id
                ";
        $whereQuery=" where ((t.recipient_id='".$user."' and m.type='1') OR (t.sender_id='".$user."' and m.type='0'))
                      and u.type='".$type."' Group By t.sender_id,m.id ORDER BY m.created_at DESC";
        $msg_query="select m.id as messageId,ticket_id,recipient_id,email, title as Subject,content,DATE_FORMAT(m.created_at , '%d/%m/%Y') as receivedDate,m.status from
                  ".$this->_name." t ".$join.$whereQuery ;*/
		if($where)			
			$where=" and ".$where;
		else	
			$where='';

        $msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,up.first_name,recipient_id,
                        IF(m.type='0',recipient_id,sender_id) as receiverId,
						IF(m.type='0',sender_id,recipient_id) as userid,
                        email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%h:%i %p'),DATE_FORMAT(m.created_at , '%d/%m/%Y %h:%i %p')) as receivedDate,
                         m.created_at as receivedDate_sort,
						m.status
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id

                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                         INNER JOIN UserPlus up ON u.identifier=up.user_id
                    where (((t.recipient_id='".$user."' and m.type='1') OR (t.sender_id='".$user."' and m.type='0'))
                    OR(m.bo_replied_user='".$user."'))
                        and u.type!='".$type."' ".$where." AND t.status in ('0','1') and m.auto_mail!='yes'
                    Group By t.sender_id,t.recipient_id,m.id ORDER BY m.created_at DESC";
       // echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";

    }
    /***Get Master Inbox OF all FO Users Internal***/
    public function getMasterInboxFOUsersInter($user=NULL)
    {

       if($user)
           $condition="((t.recipient_id='".$user."' and m.type='0')and (t.sender_id in (select identifier from User where type in('client','contributor'))))";
       else
            $condition="((t.recipient_id in (select identifier from User where type in('client','contributor'))) and (t.sender_id in (select identifier from User where type in('client','contributor'))))";

      $msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,IF(m.type='1',recipient_id,sender_id) as userid,
                        IF(m.type='1',sender_id,recipient_id) as receiverId,
                        u.email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%H:%i'),DATE_FORMAT(m.created_at , '%d/%m/%Y %H:%i')) as receivedDate,
                         m.created_at as receivedDate_sort,
						m.status  from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id
                        INNER JOIN User u1 ON u1.identifier=t.sender_id
                    where ".$condition." and t.status in ('0','1')
                    Group By t.sender_id,t.recipient_id,m.id ORDER BY  m.created_at DESC";

        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";

    }
    public function MasterupdateMessageStatus($ticketID,$data)
    {
        $this->_name="Message";
        $where=" ticket_id='".$ticketID."'";

        $this->updateQuery($data,$where);
    }
	 public function getMasterUserReplyMails($ticket)
    {
        $msg_query="select
                        m.id as messageId,m.type,m.attachment,ticket_id,sender_id,recipient_id,
                        IF(m.type='1',recipient_id,sender_id) as userid, IF(m.type='1',sender_id,recipient_id) as receiverId, u.email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%h:%i %p'),m.created_at) as receivedDate,
                        m.status,m.bo_replied_user,
						IF(m.type='1',u.type,u1.type)as sender_type,
						IF(m.type='1',u1.type,u.type)as recipient_type
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id
                        INNER JOIN User u1 ON u1.identifier=t.sender_id
                    where
                         t.id='".$ticket."' and
                         t.status in ('0','1') 
                         Group By t.sender_id,t.recipient_id,m.id ORDER BY m.created_at DESC";
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }
    public function getServiceClient()
    {
        $serviceClientQuery="select identifier from User where email='care@edit-place.com'";
         if(($result=$this->getQuery($serviceClientQuery,true))!=NULL)
            return $result[0]['identifier'];
       else
           return "No";
    }
	
	public function getTicket($ticket_identifier)
    {
        $tickerQuery="select * from Ticket where id='".$ticket_identifier."'";
         if(($result=$this->getQuery($tickerQuery,true))!=NULL)
            return $result;
       else
           return "No";
    } 
	
	public function insertTicket($Tarray)
	{
		$Tarray['id']=$this->getIdentifier();
		$this->insertQuery($Tarray);
		return $Tarray['id'];
	}
	public function Ticket()
    {
        
        $this->createIdentifier();
    }
    public function getIdentifier()
    {
        return $this->tidentifier;
    }   
    
    public function createIdentifier()
    {
         $s=new String();
        $this->tidentifier=$s->randomString(15);
    }
}

