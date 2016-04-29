<?php
/**
  * User: Arun
 * Date: 7/18/11
 * Time: 12:42 PM
 */
 
Class Ep_Contrib_Mail extends Ep_Db_Identifier
{
    protected $_name = 'Ep_messages';

    public function getClients()
    {
        $query="select
                    u.identifier,up.first_name as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                where u.type='client'
                Group BY u.identifier,contact_name
                ORDER BY contact_name    
                ";
        if(($clients=$this->getQuery($query,true))!=NULL)
            return $clients;
        else
            return "Not Exists";

    }
    public function sendMessageToClient($mail_params)
    {
        $this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        
        $message_params['ID']=$this->getIdentifier();
        $message_params['Subject']=utf8_decode($mail_params['msg_object']);
        $message_params['Text']=utf8_decode($mail_params['mail_message']);
        $message_params['Date']=date('Y-m-d H:i:s');
        $message_params['Sender']=$this->EP_Contrib_reg->clientidentifier;
        $message_params['Type']='client';

        $recipients=$mail_params['contact_list'];

        if($this->insertQuery($message_params)!=NULL)
        {
            $this->_name="Ep_messages_recipients";
            $receiver['Message_ID']=$message_params['ID'];
            $receiver['Sender']=$message_params['Sender'];
            $receiver['New']='1';
            foreach($recipients as $recipient)
            {
                $receiver['Recipient']=$recipient;
                $result=$this->insertQuery($receiver);
            }

            return "Success";

        }

        else

            return "Error:";

    }
    public function getContribInbox($contributor)
    {
        $join=" INNER JOIN Ep_messages_recipients r ON Message_ID=ID
                LEFT JOIN Contrib_profile ON r_identifier=r.Sender
                ";
        $whereQuery=" where r.Sender='".$contributor."' and Type='client' Group By r.Sender,r.Message_ID ORDER BY New DESC, Date DESC LIMIT 0,4";
        $msg_query="select CONCAT(r_fname,' ',r_lname) as sendername, Subject,Text,DATE_FORMAT(date, '%d/%m/%Y') as receivedDate,New from
                  ".$this->_name.$join.$whereQuery ;
        //echo $msg_query;exit;

       if(($result=$this->getQuery($msg_query,true))!=NULL)
            return $result;
       else
           return "No Messages Found";


        
    }

}