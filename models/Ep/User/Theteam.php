<?

class Ep_User_Theteam extends Ep_Db_Identifier
{
	protected $_name = 'Theteam';
	
	public function createTeam($post)
	{
		$teamarray=array();
		$teamarray['name']=$post['name'];
		$teamarray['designation']=$post['designation'];
		$teamarray['description']=$post['description'];
		$teamarray['created_by']=$post['created_by'];
		$teamarray['status']=$post['status'];
		
		if($post['teamid']!="")
		{
			$where=" id=".$post['teamid'];
			$this->updateQuery($teamarray,$where);
		}
		else
		{
			$teamarray['id']=$this->getIdentifier();
			$this->insertQuery($teamarray);
		}
	}
	
	public function getTeam($id=NULL)
	{
		if($id)
			$where=" WHERE id='".$id."'";
			
		$teamQuery = "SELECT * FROM Theteam".$where;
        $teamDetails = $this->getQuery($teamQuery, true);
        return $teamDetails;
    }
	
	public function deleteteam($id)
	{
		$whereQuery ="id ='".$id."'";
		$this->deleteQuery($whereQuery);
	}
}