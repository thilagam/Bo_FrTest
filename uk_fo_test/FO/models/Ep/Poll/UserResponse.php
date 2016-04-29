<?php
/**
 * Ep_Poll_UserResponse
 * @author Arun
 * @package Articles
 * @version 1.0
 */
class Ep_Poll_UserResponse extends Ep_Db_Identifier
{
	protected $_name = 'PollUserResponse';
	
	//inserting Response
	public function InsertPollResponse($responseArray)
	{
		$Aarray = array();
		$Aarray['id']=$this->getIdentifier(); 
		$Aarray['poll_id']=$responseArray['poll_id'];
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
	//check whether response already recorded or not for a poll
	public function checkPollResponse($user_id,$poll_id)
	{
		$responseQuery="SELECT *
					  FROM  $this->_name
  	 				  WHERE user_id='".$user_id."' and poll_id='".$poll_id."'";

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
	public function checkQuestionResponse($user_id,$poll_id,$question_id)
	{
		$responseQuery="SELECT *
					  FROM  $this->_name
  	 				  WHERE user_id='".$user_id."' and question_id='".$question_id."' and poll_id='".$poll_id."'";

		//echo $responseQuery;exit;

		if(($count=$this->getNbRows($responseQuery))>0)
        {
           // $responseArray=$this->getQuery($responseQuery,true);            
	        return TRUE;
        }
        else
            return FALSE;
	}


	//get Question of a user price
	public function getPriceQuestion($poll_id)
	{
		$priceQuery="SELECT question_id
					  FROM  $this->_name pr
					  INNER JOIN  Poll_question pq ON pq.id=pr.question_id
  	 				  WHERE poll_id='".$poll_id."' and  pq.type='price'";

		//echo $priceQuery;

		if(($count=$this->getNbRows($priceQuery))>0)
        {
            $priceQuery=$this->getQuery($priceQuery,true);            
	        return $priceQuery[0]['question_id'];
        }
        else
            return "NO";
	}	
	//update poll price
	public function updatePollPrice($data,$query)
	{
		$this->updateQuery($data,$query);
	}
	//Update Poll Response
	public function updatePollResponse($data,$query)
    {
         $this->updateQuery($data,$query);
    }
}
  