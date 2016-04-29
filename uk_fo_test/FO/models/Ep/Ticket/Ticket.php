<?php
/**
 * Ep_Ticket_Ticket
 * @author Arun
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
    private $bo_user_action_type;
    private $article_id;

    private $tidentifier;

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
        $this->bo_user_action_type=$array["bo_user_action_type"] ;
        $this->article_id=$array["article_id"] ;
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
        $array["bo_user_action_type"]=$this->bo_user_action_type ;
        $array["article_id"]=$this->article_id ;
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
        if($type=='client')
        {
            $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
            $user=$this->EP_Contrib_reg->clientidentifier;
            $query="select
                    u.identifier,company_name as contact_name,u.email
                from User u
                    LEFT JOIN UserPlus  up ON u.identifier=up.user_id
                    LEFT JOIN Client  c ON u.identifier=c.user_id
                where u.type='".$type."' and u.status='Active' 
                and u.identifier in (Select DISTINCT d.user_id from Participation p
                                    INNER JOIN Article a ON a.id=p.article_id
                                    INNER JOIN Delivery d ON d.id=a.delivery_id
                                    Where p.user_id='".$user."' and d.premium_option='0')
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                ";
        }
        else
        {
            $this->EPClient_reg = Zend_Registry::get('EP_Client');
		    $user=$this->EPClient_reg->clientidentifier;

            $query="select
                    u.identifier,CONCAT(first_name,' ',UPPER(SUBSTRING(last_name, 1,1))) as contact_name,u.email
                from User u
                    INNER JOIN UserPlus  up ON u.identifier=up.user_id
                where u.type='".$type."' and u.status='Active' 
                and u.identifier in (Select DISTINCT p.user_id from Participation p
                                    INNER JOIN Article a ON a.id=p.article_id
                                    INNER JOIN Delivery d ON d.id=a.delivery_id
                                    Where d.user_id='".$user."')
                Group BY u.identifier,contact_name
                ORDER BY contact_name    
                ";
        }    
       // echo $query;exit;
        if(($clients=$this->getQuery($query,true))!=NULL)
            return $clients;
        else
            return "Not Exists";

    }
    public function ongoingClients()
    {

        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
        if(!$userIdentifier)
        {
            $privateQuery="(d.AOtype='public')";
        }
        else
        {
            $privateUser=$userIdentifier;
            $privateQuery="((d.AOtype='public' AND ((UNIX_TIMESTAMP()<=d.published_at+(60*60*d.priority_hours) AND find_in_set($privateUser, a.priority_contributors)>0) OR (UNIX_TIMESTAMP()>=d.published_at+(60*60*d.priority_hours)))) OR (d.AOtype='private' and find_in_set($privateUser, a.contribs_list )>0))";
        }
       $condition=" and a.id not in( select article_id from Participation pa where pa.user_id='".$userIdentifier."' or pa.status='published')";
       $participationJoin=" LEFT JOIN Participation pa ON pa.article_id = a.id  ";

        
        $query="SELECT u.identifier,if( d.deli_anonymous = '1', 'Anonyme', company_name ) AS contact_name,
                       if( d.deli_anonymous = '1', 'Anonyme', u.email ) AS email, d.deli_anonymous,
                       GROUP_CONCAT(a.category) as filter_category,GROUP_CONCAT(a.language) as filter_language,a.type 
                 FROM Delivery d
                 INNER JOIN  User u ON u.identifier=d.user_id
                 LEFT JOIN  Client c  ON u.identifier=c.user_id
                 INNER JOIN Payment p ON p.delivery_id=d.id
                 INNER JOIN Article a ON a.delivery_id=d.id
                 ".$participationJoin."
                 where p.status='Paid' and a.status!='validated' and d.status_bo='active' ".$condition."
                 and ".$privateQuery."  AND d.submitdate_bo >= CURDATE()  AND (a.participation_expires=0 OR a.participation_expires > UNIX_TIMESTAMP())
                GROUP BY u.identifier,contact_name
                ORDER BY contact_name";

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
                where u.type='".$type."' and u.status='Active'
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                ";*/
				$query="select
                    u.identifier,CONCAT(first_name,' ',last_name) as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                where  FIELD(`type`, ".$type.")
				and u.status='Active' and u.email in('sales@edit-palce.com','partner@edit-place.com','care@edit-place.com','facturation@edit-place.com')
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                ";
       //echo $query;exit;
        if(($ep_users=$this->getQuery($query,true))!=NULL)
            return $ep_users;
        else
            return "Not Exists";

    }
    public function getUserInbox($type,$user)
    {
        /*$join=" INNER JOIN Message m ON m.ticket_id=t.id
                LEFT JOIN UserPlus up ON up.user_id=t.recipient_id OR up.user_id=t.sender_id
                INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                ";
        $whereQuery=" where ((t.recipient_id='".$user."' and m.type='0') OR (t.sender_id='".$user."' and m.type='1'))
                      and u.type='".$type."' Group By t.sender_id,t.recipient_id,m.id ORDER BY m.status ASC, m.created_at DESC";
        $msg_query="select  CONCAT(first_name,' ',last_name) as sendername, m.id as messageId,m.type,ticket_id,sender_id,recipient_id,IF(m.type=0,sender_id,recipient_id) as userid,email, title as Subject,content,DATE_FORMAT(m.created_at , '%d/%m/%Y') as receivedDate,m.status from
                  ".$this->_name." t ".$join.$whereQuery ;*/
      $msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,IF(m.type='1',recipient_id,sender_id) as userid,
                        email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%h:%i %p'),m.created_at) as receivedDate,
                        m.status,
                        u.type,t.article_id
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        
                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                    where ((t.recipient_id='".$user."' and m.type='0') OR (t.sender_id='".$user."' and m.type='1'))
                        and u.type!='".$type."' and t.status in ('0','1') and m.approved='yes' and (m.created_at >= now()-interval 3 month)
                    Group By t.sender_id,t.recipient_id,m.id ORDER BY  m.created_at DESC";
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
      
    }
	function getUserName($user,$full_name=NULL)
	{
		//CONCAT(first_name,' ',UPPER(SUBSTRING(last_name, 1,1)))) as sendername

        $senderQuery="select IF(u.type='client',company_name,first_name) as sendername ,email,CONCAT(first_name,' ',last_name) as full_name from User u
		                    LEFT JOIN UserPlus up ON u.identifier=up.user_id
		                    LEFT JOIN Client c ON u.identifier=c.user_id
		                    where identifier='".$user."'";
		//echo $senderQuery;exit;
		if(($result=$this->getQuery($senderQuery,true))!=NULL)
		{
			if($full_name)
                return $result[0]['full_name'];
            else if($result[0]['sendername']!=NULL)
				return $result[0]['sendername'];
			else
            {
				$email=explode("@",$result[0]['email']);
                return $email[0];
            }
		}
           
	}
    
     public function getUserSentBox($type,$user)
    {
        /*$join=" INNER JOIN Message m ON m.ticket_id=t.id
                LEFT JOIN UserPlus up ON up.user_id=t.sender_id OR up.user_id=t.recipient_id
                INNER JOIN User u ON u.identifier=t.sender_id OR u.identifier=t.recipient_id
                ";
        $whereQuery=" where ((t.recipient_id='".$user."' and m.type='1') OR (t.sender_id='".$user."' and m.type='0'))
                      and u.type='".$type."' Group By t.sender_id,m.id ORDER BY m.created_at DESC";
        $msg_query="select m.id as messageId,ticket_id,recipient_id,email, title as Subject,content,DATE_FORMAT(m.created_at , '%d/%m/%Y') as receivedDate,m.status from
                  ".$this->_name." t ".$join.$whereQuery ;*/
        $msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,
                        IF(m.type='0',recipient_id,sender_id) as userid,
                        email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%h:%i %p'),m.created_at) as receivedDate,
                        m.status,
                        u.type,t.article_id
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        
                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                    where ((t.recipient_id='".$user."' and m.type='1') OR (t.sender_id='".$user."' and m.type='0'))
                        and u.type!='".$type."' and t.status in ('0','1')
                    Group By t.sender_id,t.recipient_id,m.id ORDER BY m.created_at DESC";
       // echo $msg_query;exit;

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
            return "NO";



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
                $ticket_query="select CONCAT(up.first_name,' ',up.last_name) as username ,u.email, recipient_id as userid,title as Subject,t.id as ticketid from ".$this->_name." t ".$join.$where;
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
        $msg_query="select
                        count(m.id) as msgCount
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id

                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                    where ((t.recipient_id='".$user."' and m.type='0') OR (t.sender_id='".$user."' and m.type='1')) and (m.created_at >= now()-interval 3 month)
                        and u.type!='".$type."' and m.status='0' and t.status in ('1','0') and m.approved='yes'
                    ";
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result[0]['msgCount'];
        else
            return 0;
       
    }
    public function getUserClassifyBox($ticket,$user)
    {
        $msg_query="select
                        m.id as messageId,m.type,ticket_id,sender_id,recipient_id,                        
                        IF(m.type='1',recipient_id,sender_id) as userid,
                        IF(m.type='1',sender_id,recipient_id) as receiverId,
                         email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%h:%i %p'),m.created_at) as receivedDate,
                         m.status
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                    where
                        (t.recipient_id='".$user."' OR t.sender_id='".$user."')
                         and t.id='".$ticket."' and
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
        $msg_query="select
                        m.id as messageId,m.type,m.attachment,ticket_id,sender_id,recipient_id,
                        IF(m.type='1',recipient_id,sender_id) as userid, email, title as Subject,content,
                        IF(DATE(m.created_at)=DATE(NOW()),DATE_FORMAT(m.created_at , '%h:%i %p'),m.created_at) as receivedDate,
                         m.status
                    from Ticket t
                        INNER JOIN Message m ON m.ticket_id=t.id
                        INNER JOIN User u ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                    where
                        (t.recipient_id='".$user."' OR t.sender_id='".$user."')
                         and t.id='".$ticket."' and
                         t.status in ('0','1') and m.approved='yes'
                         Group By t.sender_id,t.recipient_id,m.id ORDER BY m.created_at DESC";
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }
    public function getClassifyTicket($user)
    {

         $msg_query="select
                            id as ticket_id,title as Subject,classified_by,created_at as updated_at
                     from   Ticket t
                     where (t.recipient_id='".$user."' OR t.sender_id='".$user."')
                             and status in ('2','3') order by updated_at DESC";
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";
    }
    public function getSenderRecipientType($ticketIdentifier)
    {
        $query="SELECT u.type FROM User u
                INNER JOIN Ticket t ON u.identifier=t.recipient_id OR u.identifier=t.sender_id
                where t.id='".$ticketIdentifier."'";
        if(($result=$this->getQuery($query,true))!=NULL)
            return $result;
    }
    public function getBoUserType($Identifier)
    {
        $query="SELECT u.type FROM User u where u.identifier='".$Identifier."'";
        if(($result=$this->getQuery($query,true))!=NULL)
            return $result[0]['type'];
    }

    //FO compose ongoing articles 
    public function ongoingWriterContacts($identifier)
    {
        $statusQuery=" ((p.status in ('bid','under_study','time_out','on_hold','bid_premium','bid_nonpremium','bid_temp','bid_refused_temp',
                            'disapproved_temp','closed_temp','disapproved','disapprove_client','closed_client_temp','plag_exec'))
                             OR (p.status in ('closed','closed_client') AND p.updated_at >= ( CURDATE() - INTERVAL 2 DAY )))";

        // IF(d.created_user,d.created_user,d.user_id) as created_user
        $ongoingContactsQuery="SELECT a.title,a.id as created_user 

                             FROM Participation p
                             INNER JOIN Article a ON a.id=p.article_id
                             INNER JOIN Delivery d ON a.delivery_id=d.id
                             LEFT JOIN  Client c  ON d.user_id=c.user_id                             
                             where ".$statusQuery." and p.user_id='".$identifier."' 
                             AND ((p.article_submit_expires=0 OR p.article_submit_expires > UNIX_TIMESTAMP())OR(p.article_submit_expires <= UNIX_TIMESTAMP() AND p.status in ('bid','under_study','plag_exec')) OR (p.status in ('closed','closed_client') AND p.updated_at >= ( CURDATE() - INTERVAL 2 DAY )))
                             GROUP BY p.id
                             ORDER BY a.title,p.created_at DESC";
        //echo $ongoingArticleQuery;
        if(($count=$this->getNbRows($ongoingContactsQuery))>0)
        {
            $ongoingContacts=$this->getQuery($ongoingContactsQuery,true);           

            return $ongoingContacts;
        }
        else
            return array();
        
    }
    //FO compose ongoing corrector articles
    public function ongoingCorrectorContacts($identifier)
    {
        $statusQuery=" (p.status in ('bid','under_study','bid_corrector','validated','disapproved') and cp.status in ('under_study','closed_temp','disapproved_temp','disapproved','plag_exec','closed'))";
        
        // IF(d.created_user,d.created_user,d.user_id) as created_user
        $ongoingCorrectorContactsQuery="SELECT a.title,a.id as created_user
                             FROM CorrectorParticipation p
                             INNER JOIN Article a ON a.id=p.article_id
                             INNER JOIN Delivery d ON a.delivery_id=d.id
                             INNER JOIN Participation cp ON cp.id=p.participate_id                             
                             where ".$statusQuery." and p.corrector_id='".$identifier."'  and cp.status in ('under_study','closed_temp','disapproved_temp','disapproved','plag_exec','closed')
                             AND ((p.corrector_submit_expires=0 OR p.corrector_submit_expires > UNIX_TIMESTAMP())OR(p.corrector_submit_expires <= UNIX_TIMESTAMP() AND p.status in ('bid','under_study') ))
                             GROUP BY p.id
                             ORDER BY p.status,p.created_at DESC";
        //echo $ongoingCorrectorArticlesQuery;exit;
        if(($count=$this->getNbRows($ongoingCorrectorContactsQuery))>0)
        {
            $ongoingCorrectorContacts=$this->getQuery($ongoingCorrectorContactsQuery,true);            
            
            return $ongoingCorrectorContacts;
        }
         else
            return array();
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

