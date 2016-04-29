<?php
/**
 * Ep_Quote_ ClientContacts
 * @author ARun
 * @package  ClientContacts
 * @version 1.0
 */
class Ep_Quote_ClientContacts extends Ep_Db_Identifier
{
	protected $_name = 'ClientContacts';  
	
	//Insert Client contact
	public function insertContact($data)
	{
		$data["identifier"] = $this->getIdentifier();

		//echo "<pre>";print_r($data);exit;

		$this->insertQuery($data);	
	}

	//Client contact Details
	public function getClientContacts($client_id)
	{
		$query=" SELECT * From ".$this->_name." WHERE client_id='".$client_id."'
		ORDER BY main_contact ASC";	
		//echo $query;
		
		if(($count=$this->getNbRows($query))>0)
        {
            $clientContactDetails=$this->getQuery($query,true);
            return $clientContactDetails;
        }
        else
            return "NO";		
	
	}
    /* *** added on 25.01.2016*** */
	//Client contact Details with job description
	public function getClientContactsDetails($client_id)
	{
		$query=" SELECT CC.*,CJ.job_title From ".$this->_name." AS CC
		LEFT JOIN ClientJobs AS CJ ON CJ.id = CC.job_position
		WHERE client_id='".$client_id."'
		ORDER BY main_contact ASC";
		//echo $query;

		if(($count=$this->getNbRows($query))>0)
        {
            $clientContactDetails=$this->getQuery($query,true);
            return $clientContactDetails;
        }
        else
            return NULL;

	}
	//Client Main contact Details
	public function getClientMainContacts($client_id)
	{
		$query=" SELECT c.*,j.job_title From ".$this->_name." c
				LEFT JOIN ClientJobs j ON j.id=c.job_position
			 	WHERE c.client_id='".$client_id."' and c.main_contact='yes' LIMIT 1";	
		//echo $query;
		
		if(($count=$this->getNbRows($query))>0)
        {
            $clientContactDetails=$this->getQuery($query,true);
            return $clientContactDetails;
        }
        else
            return "NO";		
	
	}


	public function updateClientContact($data,$identifier)
    {
        //print_r($data);exit;
		$where=" identifier='".$identifier."'";
        $this->updateQuery($data,$where);
    }
    //delete contact of  client
	public function deleteClientContact($identifier)
    {
        $where=" identifier='".$identifier."'";    
		if($identifier)	
        echo $this->deleteQuery($where);

    }
    //Insert Client contact jobs
	public function insertClientJobs($data)
	{
		$this->_name='ClientJobs';
		//$data["identifier"] = $this->getIdentifier();
		//echo "<pre>";print_r($data);exit;
		return  $this->insertQuery($data);	

		//return $data["identifier"];

	}
	//get all client jobs
	
	public function getClientJobs($status=true)
	{
		if($status)
		$query=" SELECT * From ClientJobs WHERE status=1 ORDER BY id ASC";	
		else
		$query=" SELECT * From ClientJobs ORDER BY id ASC";	
		//echo $query;
		
		if(($count=$this->getNbRows($query))>0)
        {
            $contactJobs=$this->getQuery($query,true);

            foreach($contactJobs as $jobs)
            {
            	$contact_jobs[$jobs['id']]=$jobs['job_title'];
            }

            return $contact_jobs;
        }
        else
            return "NO";		
	
	}
	
	function searchJob($search="")
	{
		$query="SELECT * From ClientJobs WHERE status=1 AND job_title LIKE '".$search."' ORDER BY id ASC";	
		if(($count=$this->getNbRows($query))>0)
        {
			$contactJobs=$this->getQuery($query,true);
			return $contactJobs[0]['id'];
		}	
		else
			return 0;
	}
	/*get all details of a contact*/
	function getContactDetails($client_contact_id)
	{
		$query=" SELECT CC.*,CJ.job_title From ".$this->_name." AS CC
		LEFT JOIN ClientJobs AS CJ ON CJ.id = CC.job_position
		WHERE CC.identifier='".$client_contact_id."'";
		
		//echo $query;

		if(($count=$this->getNbRows($query))>0)
        {
            $clientContactDetails=$this->getQuery($query,true);
            return $clientContactDetails;
        }
        else
            return NULL;
		
	}
}
