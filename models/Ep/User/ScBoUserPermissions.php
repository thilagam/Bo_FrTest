<?php


class Ep_User_ScBoUserPermissions extends Ep_Db_Identifier
{
	protected $_name = 'ScBoUserPermissions';
	
	public function InsertScContacts($contacts)
	{
		$contacts['id']=$this->getIdentifier();
		$this->insertQuery($contacts);	
	}
	
	public function getScBoUserPermissions($sc)
	{
		$query = "SELECT *,group_concat(DISTINCT access_client_list separator ',') as clients,group_concat(access_deliveries SEPARATOR '|' ) AS deliveries FROM ScBoUserPermissions WHERE reference='".$sc."' GROUP BY bo_user";
       
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
	}
	
	public function deleteScBoUserPermissions($identifier)
    {
        $where=" reference='".$identifier."'";    
		if($identifier)	
			echo $this->deleteQuery($where);

    }
	
	public function getScBoUserPermissionsbyClient($bou,$cl)
	{
		$query1 = "SELECT id,access_deliveries,reference FROM ScBoUserPermissions WHERE bo_user='".$bou."' AND access_client_list='".$cl."'";
       
        $result1 = $this->getQuery($query1,true);
        return $result1;
      
	}
	
	public function UpdateScContacts($Uparray)
	{
		//$query3 = "UPDATE ScBoUserPermissions SET reference=concat(reference,',".$Uparray['reference']."'),access_deliveries='".$Uparray['access_deliveries']."' WHERE id='".$Uparray['id']."'";
      // echo $query3;exit;
        //$result3 = $this->getQuery($query3,true);
        //return $result2;
		$data=array();
		$data['access_deliveries']=$Uparray['access_deliveries'];
		$where="bo_user ='".$Uparray['bo_user']."' AND access_client_list ='".$Uparray['client']."'"; 
		$result3 = $this->updateQuery($data,$where);
	}
	
	public function deleteScPerm($ref)
	{
		$whereq=" reference='".$ref."'";
		$this->deleteQuery($whereq);
	}
	
	public function getBousers($id)
	{
		$query2 = "SELECT 
						DISTINCT(s.bo_user) as bo_user,u.email,u.password,up.first_name,up.last_name
					FROM 
						ScBoUserPermissions s INNER JOIN User u ON s.bo_user=u.identifier
						INNER JOIN UserPlus up ON u.identifier=up.user_id
					WHERE 
						s.reference='".$id."'";
       $result2 = $this->getQuery($query2,true);
        return $result2;
	}
}