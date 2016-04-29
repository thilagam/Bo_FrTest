<?

class Ep_User_Jobs extends Ep_Db_Identifier
{
	protected $_name = 'Jobs';
	
	public function createJob($post)
	{
		//Inserting Jobs
		$this->_name='Jobs';
		$jobarray=array();
		
		$jobarray['title']=$post['job_title'];
		$jobarray['created_by']=$post['created_by'];
		$jobarray['status']=$post['status'];
		
		if($post['summary']!="")
			$jobarray['summary']=$post['summary'];
		else
			$jobarray['summary']=NULL;
		
		if($post['aboutep_title']!="" && $post['aboutep_desc']!="")
		{
			$jobarray['aboutep_title']=$post['aboutep_title'];
			$jobarray['aboutep_desc']=$post['aboutep_desc'];
		}
		else
		{
			$jobarray['aboutep_title']=NULL;
			$jobarray['aboutep_desc']=NULL;
		}
			
		
		if($post['mission_title']!="" && $post['mission_desc']!="")
		{
			$jobarray['mission_title']=$post['mission_title'];
			$jobarray['mission_desc']=$post['mission_desc'];
		}
		else
		{
			$jobarray['mission_title']=NULL;
			$jobarray['mission_desc']=NULL;
		}
		
		if($post['team_title']!="" && $post['team_desc']!="")
		{
			$jobarray['team_title']=$post['team_title'];
			$jobarray['team_desc']=$post['team_desc'];
		}
		else
		{
			$jobarray['team_title']=NULL;
			$jobarray['team_desc']=NULL;
		}
		
		
		if($post['willdo_title']!="" && $post['willdo_desc']!="")
		{
			$jobarray['willdo_title']=$post['willdo_title'];
			$jobarray['willdo_desc']=$post['willdo_desc'];
		}
		else
		{
			$jobarray['willdo_title']=NULL;
			$jobarray['willdo_desc']=NULL;
		}
		
		if($post['willnotdo_title']!="" && $post['willnotdo_desc']!="")
		{
			$jobarray['willnotdo_title']=$post['willnotdo_title'];
			$jobarray['willnotdo_desc']=$post['willnotdo_desc'];
		}
		else
		{
			$jobarray['willnotdo_title']=NULL;
			$jobarray['willnotdo_desc']=NULL;
		}
				
		if($post['profile_title']!="" && $post['profile_desc']!="")
		{
			$jobarray['profile_title']=$post['profile_title'];
			$jobarray['profile_desc']=$post['profile_desc'];
		}
		else
		{
			$jobarray['profile_title']=NULL;
			$jobarray['profile_desc']=NULL;
		}
		
		if($post['skills_title']!="" && $post['skills_desc']!="")
		{
			$jobarray['skills_title']=$post['skills_title'];
			$jobarray['skills_desc']=$post['skills_desc'];
		}
		else
		{
			$jobarray['skills_title']=NULL;
			$jobarray['skills_desc']=NULL;
		}
		
		if($post['jobid']!="")
		{
			$where=" id=".$post['jobid'];
			$this->updateQuery($jobarray,$where);
		}
		else
		{
			$jobarray['id']=$this->getIdentifier();
			$this->insertQuery($jobarray);
		}
	}
	
	public function getJobs($id=NULL)
	{
		if($id)
			$where=" WHERE id='".$id."'";
			
		$jobQuery = "SELECT * FROM Jobs".$where;
        $jobDetails = $this->getQuery($jobQuery, true);
        return $jobDetails;
    }
	
	public function deletejob($id)
	{
		$whereQuery ="id ='".$id."'";
		$this->deleteQuery($whereQuery);
	}
}