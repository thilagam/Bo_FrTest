<?php

class Ep_User_Client extends Ep_Db_Identifier
{
	protected $_name = 'Client';
	
	public function UpdateCompany($client,$clientarr)
	{
		$ChckClient="SELECT user_id FROM ".$this->_name." WHERE user_id='".$client."' ";
		$resultclient = $this->getQuery($ChckClient,true);
		
		$Carray=array();
		
		if(count($resultclient)>0)
		{
			$Carray['company_name']=$this->utf8dec($clientarr['companyname']);
			/*$Carray['logotype']=$clientarr['logotype'];
			
			if($clientarr['logotype']=="twt")
				$Carray['twitterid']=$clientarr['twitterid'];
			elseif($clientarr['logotype']=="file")	
				$Carray['twitterid']="";*/
			$Wherec=" user_id='".$client."'";
			$this->updateQuery($Carray,$Wherec);
		}
		else
		{
			//insert
			$Carray['user_id']=$client;
			$Carray['company_name']=$this->utf8dec($clientarr['companyname']);
			/*$Carray['logotype']=$clientarr['logotype'];
			
			if($clientarr['logotype']=="twt")
				$Carray['twitterid']=$clientarr['twitterid'];
			elseif($clientarr['logotype']=="file")	
				$Carray['twitterid']="";	*/
			
			$this->insertQuery($Carray);
		}
	}
	
	public function UpdateClientContact($client,$clientarr)
	{
		$ChckClient="SELECT user_id FROM ".$this->_name." WHERE user_id='".$client."' ";
		$resultclient = $this->getQuery($ChckClient,true);
		
		$Carray=array();
		
		if(count($resultclient)>0)
		{
			$Carray['contact_firstname']=$this->utf8dec($clientarr['contact_firstname']);
			$Carray['contact_lastname']=$this->utf8dec($clientarr['contact_lastname']);
			$Carray['contact_email']=$clientarr['contact_email'];
			$Carray['contact_phone']=$clientarr['contact_phone'];
			
			$Wherec=" user_id='".$client."'";
			$this->updateQuery($Carray,$Wherec);
		}
		else
		{
			//insert
			$Carray['user_id']=$client;
			$Carray['contact_firstname']=$this->utf8dec($clientarr['contact_firstname']);
			$Carray['contact_lastname']=$this->utf8dec($clientarr['contact_lastname']);
			$Carray['contact_email']=$clientarr['contact_email'];
			$Carray['contact_phone']=$clientarr['contact_phone'];
			
			$this->insertQuery($Carray);
		}
	}
	public function updateClient($uarray,$id)
	{
		$chkQuery="SELECT user_id FROM ".$this->_name." WHERE user_id='".$id."'";	
		$resultclient = $this->getQuery($chkQuery,true);
		
		if(count($resultclient)>0)
		{
			$where=" user_id='".$id."'";
			$this->updateQuery($uarray,$where);
		}
		else
		{
			$uarray['user_id']=$id;
			$this->insertQuery($uarray);
		}
			
	}
	
	public function getClientdetails($client)
	{
		$chkQuery="SELECT * FROM ".$this->_name." WHERE user_id='".$client."'";	
		$resultclient = $this->getQuery($chkQuery,true);
		return $resultclient;
	}
	
	public function getSuperClientDetails($client)
	{
		$SclientQuery="SELECT * FROM ".$this->_name." WHERE user_id='".$client."'";	
		$Sclientresult = $this->getQuery($SclientQuery,true);
		return $Sclientresult;
	}
	
	public function getSuperClientsites($clients)
	{
		$SclientQuery1="SELECT company_name,user_id FROM ".$this->_name." WHERE user_id IN (".$clients.")";	
		$Sclientresult1 = $this->getQuery($SclientQuery1,true);
		return $Sclientresult1;
	}
	
	public function getEPincharge($client)
	{
		$deli_access="SELECT access_clients_list,access_lang_list,access_article_status  FROM Client  WHERE user_id='".$client."'";
		$Accessdetails= $this->getQuery($deli_access,true);
		
		//Language
		$lang=explode(",",$Accessdetails[0]['access_lang_list']);
		$langstr=implode("','",$lang);
		
		$permissions=explode(",",$Accessdetails[0]['access_article_status']);
				$permissioncondi=array();
					for($p=0;$p<count($permissions);$p++)
					{
						if($permissions[$p]=='participation_ongoing')
							$permissioncondi[]="(a.participation_expires<=UNIX_TIMESTAMP() OR (SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0')='0')";
						elseif($permissions[$p]=='pending_selection')
							$permissioncondi[]="(SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0' AND status IN ('bid_premium','bid_nonpremium'))>0";
						elseif($permissions[$p]=='writing_progress')
							$permissioncondi[]="p.status='bid' AND p.article_submit_expires<=UNIX_TIMESTAMP()";
						elseif($permissions[$p]=='time_out')	
							$permissioncondi[]="(p.status='time_out' OR (p.status='bid' AND p.article_submit_expires>UNIX_TIMESTAMP()) )";
						elseif($permissions[$p]=='disapproved')
							$permissioncondi[]="p.status='disapproved'";
						elseif($permissions[$p]=='correction_ongoing')
							$permissioncondi[]="p.current_stage='corrector'";
						elseif($permissions[$p]=='stage2')	
							$permissioncondi[]="p.current_stage IN ('stage1','stage2')";
						elseif($permissions[$p]=='published_client')	
							$permissioncondi[]="(p.status='published' AND p.current_stage!='client')";
						elseif($permissions[$p]=='published')
							$permissioncondi[]="(p.status='published' AND p.current_stage='client')";
						elseif($permissions[$p]=='closed')		
							$permissioncondi[]="p.status='closed'";
					}
					
					if(count($permissioncondi)>0)
					{
						$wherecondi.=" AND p.cycle='0' AND (";
						$wherecondi.=implode(" OR ", $permissioncondi);
						$wherecondi.=" ) ";
					}
					
		$SclientQuery2="SELECT 
							DISTINCT(u.user_id),CONCAT(u.first_name,' ',u.last_name) as epname 
						FROM 
							UserPlus u INNER JOIN Delivery d ON u.user_id=d.created_user
							INNER JOIN Article a ON a.delivery_id=d.id
							LEFT JOIN Participation p ON p.article_id=a.id
						WHERE 
							d.user_id IN (".$Accessdetails[0]['access_clients_list'].") AND a.language IN ('".$langstr."') ".$wherecondi." GROUP BY 
						d.id";
							
		$Sclientresult2 = $this->getQuery($SclientQuery2,true);//echo $SclientQuery2;
		return $Sclientresult2;
	}
	
	public function getBoEPincharge($client)
	{
		$deli_access="SELECT access_client_list,access_language_list,access_permissions FROM ScBoUserPermissions WHERE bo_user='".$client."'";
		$Accessdetails= $this->getQuery($deli_access,true);
		
		//Language
		$lang=explode(",",$Accessdetails[0]['access_language_list']);
		$langstr=implode("','",$lang);
		
		$permissions=explode(",",$Accessdetails[0]['access_permissions']);
				$permissioncondi=array();
					for($p=0;$p<count($permissions);$p++)
					{
						if($permissions[$p]=='participation_ongoing')
							$permissioncondi[]="(a.participation_expires<=UNIX_TIMESTAMP() OR (SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0')='0')";
						elseif($permissions[$p]=='pending_selection')
							$permissioncondi[]="(SELECT count(*) FROM Participation WHERE article_id=a.id AND cycle='0' AND status IN ('bid_premium','bid_nonpremium'))>0";
						elseif($permissions[$p]=='writing_progress')
							$permissioncondi[]="p.status='bid' AND p.article_submit_expires<=UNIX_TIMESTAMP()";
						elseif($permissions[$p]=='time_out')	
							$permissioncondi[]="(p.status='time_out' OR (p.status='bid' AND p.article_submit_expires>UNIX_TIMESTAMP()) )";
						elseif($permissions[$p]=='disapproved')
							$permissioncondi[]="p.status='disapproved'";
						elseif($permissions[$p]=='correction_ongoing')
							$permissioncondi[]="p.current_stage='corrector'";
						elseif($permissions[$p]=='stage2')	
							$permissioncondi[]="p.current_stage IN ('stage1','stage2')";
						elseif($permissions[$p]=='published_client')	
							$permissioncondi[]="(p.status='published' AND p.current_stage!='client')";
						elseif($permissions[$p]=='published')
							$permissioncondi[]="(p.status='published' AND p.current_stage='client')";
						elseif($permissions[$p]=='closed')		
							$permissioncondi[]="p.status='closed'";
					}
					
					if(count($permissioncondi)>0)
					{
						$wherecondi.=" AND p.cycle='0' AND (";
						$wherecondi.=implode(" OR ", $permissioncondi);
						$wherecondi.=" ) ";
					}
					
		$SclientQuery2="SELECT 
							DISTINCT(u.user_id),CONCAT(u.first_name,' ',u.last_name) as epname 
						FROM 
							UserPlus u INNER JOIN Delivery d ON u.user_id=d.created_user
							INNER JOIN Article a ON a.delivery_id=d.id
							LEFT JOIN Participation p ON p.article_id=a.id
						WHERE 
							d.user_id IN (".$Accessdetails[0]['access_client_list'].") AND a.language IN ('".$langstr."') ".$wherecondi." GROUP BY 
						d.id";
							
		$Sclientresult2 = $this->getQuery($SclientQuery2,true);//echo $SclientQuery2;
		return $Sclientresult2;
	}
	
	public function getOdigeoincharge($sclient)
	{
		$SclientQuery6="SELECT DISTINCT(s.bo_user),CONCAT(u.first_name,' ',u.last_name) as odigeoname 
						FROM UserPlus u INNER JOIN ScBoUserPermissions s ON u.user_id=s.bo_user
						WHERE s.superclient='".$sclient."'";	
		$Sclientresult6 = $this->getQuery($SclientQuery6,true);
		return $Sclientresult6;
	}
	
	public function getClientEPincharge($client)
	{
		$SclientQuery3="SELECT DISTINCT(u.user_id),CONCAT(u.first_name,' ',u.last_name) as epname FROM UserPlus u INNER JOIN Delivery d ON u.user_id=d.created_user
						WHERE d.user_id='".$client."'";	
		$Sclientresult3 = $this->getQuery($SclientQuery3,true);
		return $Sclientresult3;
	}
	
	public function getSuperClientPermissions($client)
	{
		$SclientQuery4="SELECT * FROM Client WHERE user_id='".$client."'";	
		$Sclientresult4 = $this->getQuery($SclientQuery4,true);
		return $Sclientresult4;
	}
	
	public function getBOUserPermissions($client)
	{
		$SclientQuery5="SELECT * FROM ScBoUserPermissions WHERE bo_user='".$client."'";	
		$Sclientresult5 = $this->getQuery($SclientQuery5,true);
		return $Sclientresult5;
	}
	
	public function getClientFulldetails($user)
	{
		$SclientQuery6="SELECT * FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Client c ON u.identifier=c.user_id WHERE u.identifier='".$user."'";	
		$Sclientresult6 = $this->getQuery($SclientQuery6,true);
		return $Sclientresult6;
	}
	public function utf8dec($s_String)
    {
            $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
            return substr($s_String, 0, strlen($s_String)-1); 
    }
	
	public function checkclientprofile($client)
	{
		$SclientQuery7="SELECT category,job FROM Client WHERE user_id='".$client."'";	
		$Sclientresult7 = $this->getQuery($SclientQuery7,true);
		return $Sclientresult7;
		
	}
	
	public function getClientRecord($cid)
	{
		$query=" SELECT * From Client WHERE user_id='".$cid."'";	
		
		if($clientDetails=$this->getQuery($query,true))
           return $clientDetails;
        else
            return NULL;
	}
}	