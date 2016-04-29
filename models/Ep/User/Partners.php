<?

class Ep_User_Partners extends Ep_Db_Identifier
{
	protected $_name = 'Partners';
	
	public function createPartner($post)
	{
		$partarray=array();
		$partarray['name']=$post['name'];
		$partarray['website']=$post['website'];
		$partarray['description']=$post['description'];
		$partarray['logoname']=$post['logoname'];
		$partarray['created_by']=$post['created_by'];
		$partarray['status']=$post['status'];
		
		if($post['partid']!="")
		{
			$where=" id=".$post['partid'];
			$this->updateQuery($partarray,$where);
			return $post['partid'];
		}
		else
		{
			$partarray['id']=$this->getIdentifier();
			$this->insertQuery($partarray);
			return $partarray['id'];
		}
	}
	
	public function getPartner($id=NULL)
	{
		if($id)
			$where=" WHERE id='".$id."'";
			
		$PartQuery = "SELECT * FROM Partners".$where;
        $PartDetails = $this->getQuery($PartQuery, true);
        return $PartDetails;
    }
	

}