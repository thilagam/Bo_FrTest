<?php
/**
 * Ep_Quote_Client
 * @author Arun
 * @package Client
 * @version 1.0
 */
class Ep_Quote_Client extends Ep_Db_Identifier
{
	protected $_name = 'Client';
	        
    public function updateClientProfile($data,$identifier)
    {
        //print_r($data);exit;
		$this->_name = 'Client';
		$where=" user_id='".$identifier."'";
        $this->updateQuery($data,$where);
    }

	//Insert Super client data
	public function insertClient($data)
	{
		$this->_name = 'Client';
		$this->insertQuery($data);	
	}
	// Check and update/insert userplusdetails
	function checkupdate_up($plus_data,$user_id)
	{
		$this->_name = 'UserPlus';
		$query = "SELECT user_id FROM UserPlus WHERE user_id='".$user_id."'";
		if(($count=$this->getNbRows($query))>0)
		{
			$where=" user_id='".$user_id."'";
			$this->updateQuery($plus_data,$where);
		}
		else
		{
			$plus_data['user_id'] = $user_id;
			$this->insertQuery($plus_data);	
		}
	}
	
	// Client Details
	public function getClientDetails($client_id)
	{
		$query=" SELECT * From Client c
					LEFT JOIN User u ON c.user_id=u.identifier
					LEFT JOIN UserPlus up ON up.user_id=u.identifier
				WHERE u.type='client' AND u.identifier='".$client_id."' LIMIT 1";	
		//echo $query;exit;
		if(($count=$this->getNbRows($query))>0)
        {
            $clientDetails=$this->getQuery($query,true);
            return $clientDetails;
        }
        else
            return "NO";		
	
	}		
	
	//get All company list
	public function getAllCompanyList($searchparams=NULL)
	{		
		
		//TEST CLIETN CONDITION
		if($this->adminLogin->type!='superadmin')
		{
			$conf_obj=new Ep_Delivery_Configuration();
			$test_client_id=$conf_obj->getConfiguration('test_client_id');			
			$testCondition=" AND u.identifier!='".$test_client_id."'";
		}

		$conf_obj=new Ep_Delivery_Configuration();
		$new_filter_date=$conf_obj->getConfiguration('client_new_filter_date');

		$condition='';		

		if(in_array('new',$searchparams['client_type']))
		{
			//$condition.=" OR (u.email LIKE '%_new%' AND u.created_by='backend')";
			$condition.=" OR (DATE(u.created_at) >= '$new_filter_date' AND u.created_by='backend')";
		}
		if(in_array('other',$searchparams['client_type']))
		{
			//$condition.=" OR (u.email NOT LIKE '%_new%' AND u.created_by='backend') ";
			$condition.=" OR (DATE(u.created_at) < '$new_filter_date' AND u.created_by='backend')";
		}
		if(in_array('liberte',$searchparams['client_type']))
		{
			$condition.=" OR u.created_by='frontend' ";
		}

		if($condition)
		{
			$condition = substr($condition,3);
			$condition = ' AND ('. $condition.')';
		}


		$query=" SELECT * From Client c 
				INNER JOIN  User u ON u.identifier=c.user_id
				Where u.type='client' and c.company_name!='' AND u.status='Active'
				$condition $testCondition
				ORDER BY company_name ASC";	
		//echo $query;exit;
		
		if(($count=$this->getNbRows($query))>0)
        {
            $companyList=$this->getQuery($query,true);

            foreach($companyList as $company)
            {
            	$company_list[$company['user_id']]=$company['company_name']." (".$company['email'].") " ;
            }

            return $company_list;
        }
        else
            return "NO";
	}
//get Client websites
	public function getClientWebsites($client_id)
	{
		$query=" SELECT * From Client 
				Where user_id='".$client_id."' LIMIT 1";
				
		//echo $query;
		
		if(($count=$this->getNbRows($query))>0)
        {
            $siteList=$this->getQuery($query,true);      

            return $siteList;
        }
        else
            return "NO";
	}
	//get Quote by user details
	public function getQuoteUserDetails($user_id)
	{
		$query=" SELECT * From User u 
					LEFT JOIN UserPlus up ON up.user_id=u.identifier
				WHERE u.identifier='".$user_id."' LIMIT 1";	
		//echo $query."<br>";
		if(($count=$this->getNbRows($query))>0)
        {
            $userDetails=$this->getQuery($query,true);
            return $userDetails;
        }
        else
            return "NO";		
	
	}	

	//get BO users list with type
	 public function getEPContacts($type)
    {
        
        $query="select
                    u.identifier,CONCAT(first_name,' ',last_name) as contact_name,u.email
                from User u
                    left JOIN UserPlus  up ON u.identifier=up.user_id
                where  FIELD(`type`, ".$type.")
				and u.status='Active'
                Group BY u.identifier,contact_name
                ORDER BY contact_name
                ";
        if(($get_EP_contacts=$this->getQuery($query,true))!=NULL)
        {
            if(count($get_EP_contacts)>0)
			{	                
				foreach($get_EP_contacts as $contact)
                {
                    if($contact['contact_name']!=NULL)
					{
                        $EP_contacts[$contact['identifier']]=$contact['contact_name'];
						$assign_contacts[$contact['identifier']]=$contact['contact_name'];
					}	
                    else
                    {
                        $contact['email']=explode("@",$contact['email']);
                        $EP_contacts[$contact['identifier']]=$contact['email'][0];
						$assign_contacts[$contact['identifier']]=$contact['email'][0];
                    }
                }
			}
            return $assign_contacts;
        }
        else
            return "Not Exists";

    }
	
	// All clients created from Workplace
	function getClients($searchParameters=NULL)
	{
		$having_condition='';
		$where='';
		
		if($searchParameters['premium']=='yes')
		{
			$having_condition= ($having_condition ? $having_condition.' AND ' : '').' premium_count > 0';
		}
		else if($searchParameters['premium']=='no')
		{
			$having_condition= ($having_condition ? $having_condition.' AND ' : '').' premium_count=0';
		}
		else
		{
			$having_condition= ($having_condition ? $having_condition.' AND ' : '').' premium_count > 0';
		}
		
		
		if($searchParameters['online']=='yes')
		{
			$having_condition= ($having_condition ? $having_condition.' AND ' : '').' online_count > 0';
		}
		else if($searchParameters['online']=='no')
		{
			$having_condition= ($having_condition ? $having_condition.' AND ' : '').' online_count=0';
		}		
		$havingQuery=$having_condition ? 'Having'.$having_condition : ''; 
		
		/*where conditions*/
		if($searchParameters['ctype']=='p')
		{
			$where= ($where ? $where.' AND ' : '')."( c.client_code LIKE 'p%' OR c.client_code='' OR c.client_code IS NULL)";
		}
		elseif($searchParameters['ctype']=='c')
		{
			$where= ($where ? $where.' AND ' : '')."( c.client_code LIKE 'c%')";
		}
		if($searchParameters['created_user'])
		{
			$where= ($where ? $where.' AND ' : '')." u.created_user='".$searchParameters['created_user']."'";
		}
		if($searchParameters['startdate'] && $searchParameters['enddate'] )
		{
			$where= ($where ? $where.' AND ' : '')." u.created_at BETWEEN '".$searchParameters['startdate']."' AND '".$searchParameters['enddate']."' ";
		}
		else
		{
			if($searchParameters['startdate'])
			{
				$where= ($where ? $where.' AND ' : '')." u.created_at>='".$searchParameters['startdate']."'";			
			}
			if($searchParameters['enddate'])
			{
				$where= ($where ? $where.' AND ' : '')." u.created_at<='".$searchParameters['enddate']."'";			
			}
		}	
		$where=$where ? 'AND'.$where : ''; 
		
			
		$query = "SELECT u.identifier,u.email,u.created_user,up.first_name,c.company_name,u.created_at,
		IFNULL((SELECT IFNULL(up1.first_name,u1.email) as created_by FROM User u1 LEFT JOIN UserPlus up1 ON up1.user_id=u1.identifier WHERE u1.identifier = u.created_user),'FO') as created_by,		
		(SELECT cc.first_name FROM ClientContacts cc JOIN Client c2 ON c2.user_id=cc.client_id WHERE cc.client_id = u.identifier ORDER BY main_contact ASC Limit 0,1) as contactname,
		(SELECT count(*) FROM Delivery d1 JOIN Client c1 ON c1.user_id=d1.user_id WHERE d1.premium_option='0' AND c1.user_id=u.identifier) as online_count,
		(SELECT count(*) FROM ClientContacts cc1 WHERE cc1.client_id = u.identifier) as premium_count,
		c.client_code
		FROM `Client` c 
		JOIN User u ON u.identifier = c.user_id 
		JOIN UserPlus up ON up.user_id = u.identifier 
		WHERE  u.type='client' 
		$where	
		$havingQuery
		ORDER BY `created_at` DESC";
		
		//echo $query;exit;
		/* and c.user_id IN(SELECT client_id FROM ClientContacts GROUP BY client_id)		 */
		//(SELECT count(*) FROM Delivery d2 JOIN Client c3 ON c3.user_id=d2.user_id WHERE d2.premium_option!=0 AND c3.user_id=u.identifier) as premium_count,
		if(($count=$this->getNbRows($query))>0)
        {
            $clients=$this->getQuery($query,true);
            return $clients;
        }
        else
            return "NO";
	}
	//used in client list filters
	function getClientCreatorUsers()
	{
		$creatorsQuery="SELECT DISTINCT(IFNULL((SELECT IFNULL(up1.first_name,u1.email) as created_by FROM User u1 LEFT JOIN UserPlus up1 ON up1.user_id=u1.identifier WHERE u1.identifier = u.created_user),'FO')) as created_by,u.created_user
		FROM User u 
		JOIN UserPlus up ON up.user_id = u.identifier 
		WHERE u.created_user!='FO'
		GROUP BY u.created_user
		";
		
		if(($count=$this->getNbRows($creatorsQuery))>0)
        {
            $userCreators=$this->getQuery($creatorsQuery,true);

            foreach($userCreators as $user)
            {
            	$clientCreators[$user['created_user']]=$user['created_by'];
            }

            return $clientCreators;
        }
        else
            return NULL;
	}
	
	function getAllClientCodes()
	{
		$query="SELECT c.client_code 
				FROM `Client` c 
				JOIN User u ON u.identifier = c.user_id
				JOIN UserPlus up ON up.user_id = u.identifier
				WHERE c.client_code!=''
				";
				
		if(($count=$this->getNbRows($query))>0)
        {
            $clientDetails=$this->getQuery($query,true);

            foreach($clientDetails as $client)
            {
            	$clientCodes[]=$client['client_code'];
            }

            return $clientCodes;
        }
        else
            return NULL;		
				
	}
}
