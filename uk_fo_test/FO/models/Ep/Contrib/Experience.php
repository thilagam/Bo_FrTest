<?php
/**
 * Contributor Experience Model
 * This Model  is responsible for Experience actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Contrib_Experience extends Ep_Db_Identifier
{
	protected $_name = 'ContributorExperience';
	private $identifier;
	private $user_id;
    private $title;
    private $institute;
    private $contract;
	private $type;
    private $from_month;
    private $from_year;
    private $to_month;
    private $to_year;
    private $still_working;
	private $created_at;
	private $updated_at;

	public function loadData($array)
	{
		$this->user_id=$array["user_id"] ;
        $this->title=$array["title"] ;
        $this->institute=$array["institute"] ;
		$this->contract=$array["contract"] ;
		$this->type=$array["type"] ;
        $this->from_month=$array["from_month"];
        $this->from_year=$array["from_year"] ;
        $this->to_month=$array["to_month"] ;
        $this->to_year=$array["to_year"] ;
        $this->still_working=$array["still_working"] ;
		$this->created_at=$array["created_date"] ;
		$this->updated_at=$array["updated_date"] ;
        
    	return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["identifier"] = $this->getIdentifier();
        $array["user_id"] =  $this->user_id;
        $array["title"] = $this->title;
		$array["institute"] = $this->institute;
		$array["contract"] = $this->contract;
		$array["type"] = $this->type;
        $array["from_month"] = $this->from_month;
        $array["from_year"] = $this->from_year;
        $array["to_month"]=$this->to_month;
		$array["to_year"] = $this->to_year;
        $array["still_working"] = $this->still_working;
		$array["updated_at"] = $this->updated_at;

        return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }   
	public function getExperienceDetails($identifier,$type)
	{
		$experienceQuery="select * from ".$this->_name." where type='".$type."' and user_id='".$identifier."' order by still_working DESC,identifier DESC";	
		
		//echo $experienceQuery;exit;
		if(($count=$this->getNbRows($experienceQuery))>0)
		{
			$experienceDetails=$this->getQuery($experienceQuery,true);
			return $experienceDetails;
		}
		else
		{
			return "NO";
		}
	}
    /*added by naseer on 11-11-2015*/
    //get individul experince/training exp //
    public function getIndividualExperienceDetails($job_identifier,$type)
    {
        $experienceQuery="select * from ".$this->_name." where type='".$type."' and identifier='".$job_identifier."' ";

        //echo $experienceQuery;exit;
        if(($count=$this->getNbRows($experienceQuery))>0)
        {
            $experienceDetails=$this->getQuery($experienceQuery,true);
            return $experienceDetails;
        }
        else
        {
            return "NO";
        }
    }
    /* end of added by naseer on 11-11-2015*/
	
	 public function updateExperience($data,$identifier)
    {
        $where=" identifier='".$identifier."'";       

        echo $this->updateQuery($data,$where);


    }
	 public function deleteExperience($identifier)
    {
        $where=" identifier='".$identifier."'";       

        echo $this->deleteQuery($where);


    }

}
