<?php
/**
 * Ep_Quiz_UserResponse
 * @author Arun
 * @package Articles
 * @version 1.0
 */
class Ep_Quiz_UserResponse extends Ep_Db_Identifier
{
	protected $_name = 'QuizUserResponse';
	
	//inserting Response
	public function InsertQuizResponse($responseArray)
	{
		$Aarray = array();
		$Aarray['id']=$this->getIdentifier(); 
		$Aarray['participation_id']=$responseArray['participation_id'];
		$Aarray['quiz_id']=$responseArray['quiz_id'];
		$Aarray['question_id']=$responseArray['question_id'];
		$Aarray['user_id']=$responseArray['user_id'];
		$Aarray['response']=$responseArray['response'];	
		$Aarray['created_at']=date("Y-m-d H:i:s");

		//echo "<pre>";print_r($Aarray);exit;
		
		if($this->insertQuery($Aarray))
			return $this->getIdentifier(); 
		 else
			return "NO";
	}
	//check whether response already recorded or not for a quiz
	public function checkPollResponse($user_id,$quiz_id)
	{
		$responseQuery="SELECT *
					  FROM  $this->_name
  	 				  WHERE user_id='".$user_id."' and quiz_id='".$quiz_id."'";

		//echo $responseQuery;exit;

		if(($count=$this->getNbRows($responseQuery))>0)
        {
            $responseArray=$this->getQuery($responseQuery,true);            
	        return $responseArray;
        }
        else
            return "NO";
	}

	//check whether response already recorded or not for a Question
	public function checkQuestionResponse($quizParticipationId,$user_id,$quiz_id,$question_id)
	{
		$responseQuery="SELECT *
					  FROM  $this->_name
  	 				  WHERE user_id='".$user_id."' and question_id='".$question_id."' and quiz_id='".$quiz_id."' AND participation_id='".$quizParticipationId."'";

		//echo $responseQuery;exit;

		if(($count=$this->getNbRows($responseQuery))>0)
        {
           $responseArray=$this->getQuery($responseQuery,true);            
	        return $responseArray;
        }
        else
            return FALSE;
	}
	//Update Quiz Response
	public function updateQuizResponse($data,$identifier)
    {
         $query=" id='".$identifier."'";
		 
		 $this->updateQuery($data,$query);
    }
}
  