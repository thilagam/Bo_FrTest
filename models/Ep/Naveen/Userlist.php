<?php

class Ep_Naveen_Userlist extends Ep_Db_Identifier
{
	protected $_name = 'User';
	
     ///////Users List//////////////
   public function getUserList(){
   
    $query = "SELECT us.*,up.phone_number FROM ".$this->_name." as us LEFT JOIN UserPlus as up ON us.identifier=up.user_id where us.type NOT IN('client','contributor')  order by us.created_at asc";
		if(($result = $this->getQuery($query, true)) != NULL)
        {		
           return $result;
        }
		else{
		return "No Records Found";
		}
           
   
   }
   
   public function user_list_insert($array){
   
   $id=$this->getIdentifier();
   $this->_name='User';
   $array["identifier"] = $this->getIdentifier();
        $array["login"] = $array['login'];
		$array["email"] = $array['email'];
        $array["password"] = $array['password'];
		$array["created_at"] = $array['created_at'];
		
        $this->insertQuery($array);
   
   }
   
   public function getBoUserList(){
   
    $query = "SELECT us.*,up.* FROM ".$this->_name." us Left Join UserPlus as up ON us.identifier=up.user_id 
	where us.type='client' order by us.created_at desc";
	
		if(($result = $this->getQuery($query, true)) != NULL)
        {		
           return $result;
        }
		else{
		return "No Records Found";
		}   
   }
   
   public function getEditUserList(){
   
    $query = "SELECT us.*,up.* FROM ".$this->_name." us Left Join UserPlus as up ON us.identifier=up.user_id 
	where us.type='contributor' order by us.created_at desc";
	
		if(($result = $this->getQuery($query, true)) != NULL)
        {		
           return $result;
        }
		else{
		return "No Records Found";
		}
      
   }
   
   public function getsingleclient($user_id){
   
   $query = "SELECT us.*,up.*,cl.* FROM ".$this->_name." us Left Join UserPlus as up ON us.identifier=up.user_id Left Join Client as cl ON us.identifier=cl.user_id
	where us.identifier=".$user_id." ";
	
		if(($result = $this->getQuery($query, true)) != NULL)
        {		
           return $result;
        }
		else{
		return "No Records Found";
		}   
   }
   
    public function getsinglecontributor($user_id){
   
   $query = "SELECT us.*,up.*,cn.* FROM ".$this->_name." us Left Join UserPlus as up ON us.identifier=up.user_id Left Join Contributor as cn ON us.identifier=cn.user_id
	where us.identifier=".$user_id." ";
	
		if(($result = $this->getQuery($query, true)) != NULL)
        {		
           return $result;
        }
		else{
		return "No Records Found";
		}   
   }
   
   


}

