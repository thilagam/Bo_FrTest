<?php
/**
 * Ep_User_Client
 * @author Admin
 * @package Client
 * @version 1.0
 */
class Ep_User_Client extends Ep_Db_Identifier
{
	protected $_name = 'Client';
	private $user_id;
	private $rcs;
	private $vat;
	private $fax_number;

	public function loadData($array)
	{
		$this->user_id($array["user_id"]) ;
		$this->rcs($array["rcs"]) ;
		$this->vat($array["vat"]) ;
		$this->fax_number($array["fax_number"]) ;
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["user_id"] = $this->getUser_id();
		$array["rcs"] = $this->rcs;
		$array["vat"] = $this->vat;
		$array["fax_number"] = $this->fax_number;
		return $array;
	}
    public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    ////////fetch the client info in popup//////////////
    public function getClientInfo($profile_identifier)
    {
        $profileQuery = "select count(d.id) AS aos_created, up.first_name,up.last_name,c.company_name,u.created_at from ".$this->_name." c
	                     INNER JOIN User u ON c.user_id=u.identifier INNER JOIN UserPlus up ON c.user_id=up.user_id
	                     INNER JOIN Delivery d ON u.identifier=d.user_id where u.identifier=".$profile_identifier;
     	$result = $this->getQuery($profileQuery,true);
	    return $result;
	}
    public function updateClientProfile($data,$identifier)
    {
        //print_r($data);exit;
		$where=" user_id='".$identifier."'";
        $this->updateQuery($data,$where);
    }
    
    /* Listing clients info including AO & profile info */
    public function ListClientsinfo()
    {
        $query="SELECT u.identifier,up.first_name,up.last_name,u.email,
                 u.profile_type,u.created_at,u.last_visit, c.company_name
                    FROM User u
                    INNER JOIN UserPlus up ON u.identifier = up.user_id
                    INNER JOIN Client c ON c.user_id = u.identifier
                    WHERE u.type = 'client'
                    AND u.status = 'Active'
                    ORDER BY u.updated_at DESC";
                    
        /* Adding AO infos & date formatting */         
        foreach ( $this->getQuery($query,true) as $client ) :
            $aoInfo =   $this->clientAoCount($client['identifier']) ;
            $client['ao_count']        =   $aoInfo['ao_count'] ;
            $client['ao_id']           =   $aoInfo['ao_id'] ;
            $client['created_at']      =   date( "d/m/Y", strtotime( $client['created_at'] ) ) ;
            $client['last_visit']      =   date( "d/m/Y" , strtotime( $client['last_visit'] ) ) ;
            $result[]   =   $client ;
        endforeach ;
        
        return $result;
    }
    
    /* Returns AO Count for a given user Id */
    public function clientAoCount($user_id)
    {
        $query="SELECT id AS ao_id, COUNT(*) AS ao_count, title AS company_name
                    FROM Delivery
                    WHERE user_id = '" . $user_id . "'";
        $result = $this->getQuery($query,true);
        return $result[0];
    }

	public function getClientName($uid)
	{	
		 $SelectallContrib="SELECT us.first_name,us.last_name,u.email,us.phone_number,c.company_name,u.identifier FROM User u
		                    LEFT JOIN UserPlus us ON us.user_id=u.identifier LEFT JOIN Client c ON us.user_id=c.user_id where u.identifier='".$uid."'";
		$resultall = $this->getQuery($SelectallContrib,true);
		return $resultall;
	}
	
	public function updateCompany($company,$user)
	{
		$clientcheck=$this->Checkifexists($user,'Client');	
		$this->_name="Client";
		$userarr=array();
		$userarr['company_name']=$company;
		if($clientcheck=="yes")
		{
			$where1="user_id=".$user;
			$this->updateQuery($userarr,$where1);
		}
		else
		{ 
			$userarr['user_id']=$user;
			$this->insertQuery($userarr);
		}
	}
	
	public function Checkifexists($user,$table)
	{
		$checkQuery = "SELECT * FROM ".$table." WHERE user_id='".$user."'";
		$checkresult = $this->getQuery($checkQuery,true);
		
		if(count($checkresult)>0)
			return "yes";
		else
			return "no";
		
	}
	
	// function to get Deliveries w.r.t Lang and Clients
	public function getDeliveries($clients,$languages)
	{
		$condition='';
		if(is_array($clients))
		{
			$clients="'".implode("','",$clients)."'";
			$condition.= " d.user_id IN ($clients)";
		}
		if(is_array($languages))
		{
			$languages="'".implode("','",$languages)."'";
			$condition.= " AND d.language IN ($languages)";
		}
		
		$query= "Select d.title,d.id From Delivery d WHERE $condition";
		
		if(($count=$this->getNbRows($query))>0)
        {
            $aoList=$this->getQuery($query,true);
            return $aoList;
        }
        else
            return "NO";
					
	
	}
	
	//Insert Super client data
	public function insertClient($data)
	{
		$this->insertQuery($data);	
	}
	
	//Super Client Details
	public function getSuperClientDetails($client_id)
	{
		$query=" SELECT * From Client c
					INNER JOIN User u ON c.user_id=u.identifier					
				WHERE u.type='superclient' AND u.identifier='".$client_id."'";	
		//echo $query;exit;
		if(($count=$this->getNbRows($query))>0)
        {
            $clientDetails=$this->getQuery($query,true);
            return $clientDetails;
        }
        else
            return "NO";		
	
	}	
	
	//get superclient and client list
	public function getSuperClientList()
	{
		$query=" SELECT * From User u
					LEFT JOIN  Client c ON c.user_id=u.identifier					
				WHERE u.type in ('superclient','client') and u.status='Active'";	
		//echo $query;
		if($clientDetails=$this->getQuery($query,true))
        {
           return $clientDetails;
        }
        else
            return NULL;
	
	}
	///////check the email is exits////
    public function getExistingClient($agency,$edit_client=NULL)
    {
		$agency=utf8_decode($agency);
		
        if($edit_client!='undefined')
		{
			$edit_client = utf8_decode($edit_client);
			$condition=" AND company_name NOT LIKE '".$edit_client."'";
		}
		$query = "SELECT user_id FROM ".$this->_name." WHERE company_name LIKE '".$agency."' $condition";
	
        if(($result = $this->getQuery($query,false)) != NULL)
        {
            return "yes";
        }
        else
            return "no";
    }
	
	// Check company name and email //
	function getExistingClientEmail($agency,$edit_client=NULL,$email)
	{
		$agency=utf8_decode(addslashes($agency));
				
		$condition = " c.company_name LIKE '".$agency."' OR u.email LIKE '".$email."' ";
        if($edit_client!='undefined')
		{
			$edit_client = utf8_decode(addslashes($edit_client));
			$condition ="( c.company_name LIKE '".$agency."' AND c.company_name NOT LIKE '".$edit_client."' ) OR  ( u.email NOT LIKE '".$email."' AND u.email LIKE '".$email."')";
		}
		
		$query = "SELECT u.identifier FROM User u LEFT JOIN Client c ON c.user_id=u.identifier WHERE  $condition";
		//echo $query;exit;
        if(($result = $this->getQuery($query,false)) != NULL)
        {
            return "yes";
        }
        else
            return "no";
	}
	// Check Contacts
	function checkContact($contact_email,$edit=false,$prev_email="")
	{
		$contact_email = utf8_decode($contact_email);
		if($edit)
		$condition=" AND email NOT LIKE '".$prev_email."'";
		
		$query = "SELECT * FROM ClientContacts WHERE email LIKE '".$contact_email."' $condition";
		
        if(($result = $this->getQuery($query,false)) != NULL)
        {
            return "yes";
        }
        else
            return "";
	}
	
	// Check Client Numer
	function checkClientNo($ca_no,$user_id=false)
	{
		$ca_no = utf8_decode($ca_no);
		if($user_id)
		$condition=" AND user_id NOT LIKE '".$user_id."'";
		
		$query = "SELECT * FROM Client WHERE client_code LIKE '".$ca_no."' $condition";
		
        if(($result = $this->getQuery($query,false)) != NULL)
        {
            return "yes";
        }
        else
            return "";
	}
	public function getClientRecord($cid)
	{
		$query="SELECT * From User u LEFT JOIN  Client c ON c.user_id=u.identifier WHERE u.identifier='".$cid."'";	
		
		if($clientDetails=$this->getQuery($query,true))
           return $clientDetails;
        else
            return NULL;
	}
	
	public function getChiefodigeo()
	{
		//$query=" SELECT * From User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.status='Active' AND u.type='chiefodigeo' AND u.superclient_reference IS NULL";	
		$query=" SELECT * From User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.status='Active' AND u.type='chiefodigeo'";	
		
		if($clientDetails=$this->getQuery($query,true))
           return $clientDetails;
        else
            return NULL;
	}
	
	public function getChiefodigeoself($sclient)
	{
		//$query="SELECT * From User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.status='Active' AND u.type='chiefodigeo' AND (u.superclient_reference IS NULL OR u.superclient_reference='".$sclient."')";	
		$query="SELECT * From User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.status='Active' AND u.type='chiefodigeo'";	
		
		if($clientDetails=$this->getQuery($query,true))
           return $clientDetails;
        else
            return NULL;
	}
	
	public function InsertSuperClient($scarray) 
	{
		$this->insertQuery($scarray);
	}
	//Client List based on Invoice Flag . if True Then get Client Else Dont
    public function getclientInvoicedList()
    {
		$Query = "select u.identifier, u.email,c.company_name,up.first_name,up.last_name from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Client c ON u.identifier=c.user_id
		/*LEFT JOIN Delivery d ON u.identifier=d.user_id
		LEFT JOIN Article a ON a.delivery_id=d.id*/
		where u.type='client' AND u.email NOT LIKE '%_new%'";
		//u.email condition added by arun
		//AND u.invoiced = 'yes' 
		if(($result = $this->getQuery($Query,true)) != NULL){
			$client_list=array();
        
			foreach($result as $key=>$value)
			{
				if($value['company_name'] == '')
				{
					if($value['first_name']=="")
						$value['first_name']="--";
					if($value['last_name']=="")
						$value['last_name']="--";
						
					$client_list[$value['identifier']] = strtoupper($value['email'].' ( '.$value['first_name'].' , '.$value['last_name'].' ) ');
				}
				else
					$client_list[$value['identifier']] = strtoupper($value['email'].' ( '.$value['company_name'].' ) ');
			}
			return $client_list;
		}else{
			return "NO";
		}
    }

	public function getBOuserdetails($bouser)
	{
		$bouserquery="SELECT u.email,u.password,up.first_name,up.last_name From User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.identifier='".$bouser."'";	
		
		if($bouserDetails=$this->getQuery($bouserquery,true))
           return $bouserDetails;
        else
            return NULL;
	}	
	
	// function to get Deliveries w.r.t Lang and Clients
	public function getDeliveriesClientwise($clients,$languages)
	{
		$condition='';
		if($clients!="")
			$condition.= " d.user_id IN ($clients)";
		
		if(is_array($languages))
		{
			$languages="'".implode("','",$languages)."'";
			$condition.= " AND d.language IN ($languages)";
		}
		
		$query= "Select d.title,d.id From Delivery d WHERE $condition";
		//echo $query;exit; 
		if(($count=$this->getNbRows($query))>0)
        {
            $aoList=$this->getQuery($query,true);
            return $aoList;
        }
        else
            return "NO";
					
	
	}
}
