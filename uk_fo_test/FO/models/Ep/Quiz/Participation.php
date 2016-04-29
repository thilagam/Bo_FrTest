<?php
/**
 * Ep_Quiz_Participation
 * @author Arun
 * @package Articles
 * @version 1.0
 */
class Ep_Quiz_Participation extends Ep_Db_Identifier
{
	protected $_name = 'QuizParticipation';
	
	public function InsertQuizParticipation($quizArray)
	{
		$Aarray = array();
		$Aarray['id']=$this->getIdentifier(); 
		$Aarray['quiz_id']=$quizArray['quiz_id'];
		$Aarray['user_id']=$quizArray['user_id'];
		$Aarray['delivery_id']=$quizArray['delivery_id'];
		$Aarray['article_id']=$quizArray['article_id'];
		$Aarray['type']=$quizArray['type'];
		$Aarray['qualified']=$quizArray['qualified'];
		$Aarray['percent']=$quizArray['percent'];
		$Aarray['num_correct']=$quizArray['num_correct'];
		$Aarray['num_total']=$quizArray['num_total'];
		$Aarray['created_at']=date("Y-m-d H:i:s");
		//echo "<pre>";print_r($Aarray);exit;
		$Aarray['reason']=$quizArray['reason'];
		
		if($this->insertQuery($Aarray))
			return $this->getIdentifier(); 
		 else
			return "NO";
	}
	public function getAllQuizPassed($contributor)
	{
		$qualifiedQuery="SELECT quiz_id
					  FROM  $this->_name
  	 				  WHERE user_id='".$contributor."' and qualified='yes'";
		//echo $qualifiedQuery;exit;
		if(($count=$this->getNbRows($qualifiedQuery))>0)
        {
            $qualifiedArray=$this->getQuery($qualifiedQuery,true);
            foreach($qualifiedArray as $key=>$quiz)
            {
            	$quizArray[$key]=$quiz['quiz_id'];
            }
	        return $quizArray;
        }
        else
            return array();
	}
	
	public function getQuizParticipationDetails($quiz_id,$user_id,$article_id)
	{
		$participationQuery="SELECT *
					  FROM  $this->_name
  	 				  WHERE user_id='".$user_id."' AND quiz_id='".$quiz_id."' AND article_id='".$article_id."'";
		
		//echo $participationQuery;exit;
		if(($count=$this->getNbRows($participationQuery))>0)
        {
            $details=$this->getQuery($participationQuery,true);
	        return $details;
        }
        else
            return "NO";
	
	}
	function checkQuizParticipationExist($recruitment_id,$quiz_id,$user_id)
	{
			$participationQuery="SELECT *
					  FROM  $this->_name
  	 				  WHERE user_id='".$user_id."' AND delivery_id='".$recruitment_id."' AND quiz_id='".$quiz_id."'";
		
		//echo $participationQuery;exit;
		if(($count=$this->getNbRows($participationQuery))>0)
        {
            $details=$this->getQuery($participationQuery,true);
	        return $details;
        }
        else
            return "NO";
	}
	//Update Quiz Participation
	public function updateQuizParticipation($data,$identifier)
    {
        $query=" id='".$identifier."'";
		$this->updateQuery($data,$query);
    }
}
  