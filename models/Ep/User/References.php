<?

class Ep_User_References extends Ep_Db_Identifier
{
	protected $_name = 'References';
	
	public function createReference($post)
	{
		$refarray=array();
		$refarray['name']=$post['name'];
		$refarray['website']=$post['website'];
		$refarray['description']=$post['description'];
		$refarray['logoname']=$post['logoname'];
		$refarray['created_by']=$post['created_by'];
		$refarray['status']=$post['status'];
		
		if($post['referenceid']!="")
		{
			$where=" id=".$post['referenceid'];
			$this->updateQuery($refarray,$where);
			return $post['referenceid'];
		}
		else
		{
			$refarray['id']=$this->getIdentifier();
			$this->insertQuery($refarray);
			return $refarray['id'];
		}
	}
	
	public function getReference($id=NULL)
	{
		if($id)
			$where=" WHERE id='".$id."'";
			
		$RefQuery = "SELECT * FROM  `References`".$where; 
        $RefDetails = $this->getQuery($RefQuery, true);
        return $RefDetails;
    }
	

}