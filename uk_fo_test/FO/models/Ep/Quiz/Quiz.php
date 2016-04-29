<?php
/**
 * Ep_Quiz_Quiz
 * @author Arun
 * @package Articles
 * @version 1.0
 */
class Ep_Quiz_Quiz extends Ep_Db_Identifier
{
  	
  	 //get quiz id based on article Id
  	 function getQUizId($article_id)
  	 {
  	 		$getQuizQuery="SELECT d.link_quiz,d.quiz
  	 					   FROM Delivery d
  	 					   INNER JOIN Article a ON a.delivery_id=d.id
  	 					  WHERE a.id='".$article_id."'"; 
  	 	    
        if(($count=$this->getNbRows($getQuizQuery))>0)
        {
            $getQuiz=$this->getQuery($getQuizQuery,true);
            if($getQuiz[0]['link_quiz']=='yes' && $getQuiz[0]['quiz']  )
                return $getQuiz[0]['quiz'];
            else
            	return "NO";
        }
        else
            return "NO";
  	 }
  	 //function to check whether AO is linked to Quiz or not by using Article Id and user is already participated or not
  	 public function checkQuizAO($article_id)
  	 {
  	 	$this->EP_Contrib_reg = Zend_Registry::get('EP_Contrib_reg');
        $userIdentifier=$this->EP_Contrib_reg->clientidentifier;
  	 	$quiz_identifier=$this->getQUizId($article_id);
  	 	
  	 	if($quiz_identifier!="NO")
  	 	{
	  	 	$checkQuizQuery="select quiz_id from  QuizParticipation qp where qp.qualified='yes' and quiz_id='".$quiz_identifier."' and qp.user_id='".$userIdentifier."'"; 
	  	 	//echo $checkQuizQuery;exit;
	  	 	    
	        if(($count=$this->getNbRows($checkQuizQuery))>0)
	        {
	            return "NO";
	            
	        }
	        else
	            return "YES";				 
	     }   
	     else
	        return "NO";	
  	 }
  	 //get all Qustions related to a Quiz
  	 public function getQustions($quiz_identifier,$article_id)
  	 {
  	 	$questionQuery="SELECT qq.*,d.quiz_duration,d.quiz_marks,a.id as article_id FROM  quizzquestions qq
  	 					INNER JOIN quizz q ON q.id=qq.quizz_id
						INNER JOIN Delivery d ON d.quiz=qq.quizz_id
						INNER JOIN Article a ON a.delivery_id=d.id
						WHERE a.id='".$article_id."' and q.id='".$quiz_identifier."'";
		//echo $questionQuery;exit;
		if(($count=$this->getNbRows($questionQuery))>0)
        {
            $questions=$this->getQuery($questionQuery,true);
	        return $questions;
        }
        else
            return "NO";					
  	 }
	 
	  //get all Qustions related to a Quiz
  	 public function getRecruitmentQuestions($quiz_identifier,$recruitment_id)
  	 {
  	 	$questionQuery="SELECT qq.*,d.quiz_duration,d.quiz_marks,d.id as recruitment_id
						FROM  quizzquestions qq
  	 					INNER JOIN quizz q ON q.id=qq.quizz_id
						INNER JOIN Delivery d ON d.quiz=qq.quizz_id													
						WHERE d.id='".$recruitment_id."' and q.id='".$quiz_identifier."'";
		//echo $questionQuery;exit;
		if(($count=$this->getNbRows($questionQuery))>0)
        {
            $questions=$this->getQuery($questionQuery,true);
	        return $questions;
        }
        else
            return "NO";					
  	 }
	 
  	 //get all Qustions related to a Quiz
  	 public function getOptions($question_id)
  	 {
  	 	$optionsQuery="SELECT qa.* FROM  quizzanswers qa
						INNER JOIN quizzquestions qq ON qa.quest_id=qq.id
						WHERE qa.quest_id='".$question_id."' ORDER BY RAND()";
		//echo $optionsQuery;exit;
		if(($count=$this->getNbRows($optionsQuery))>0)
        {
            $options=$this->getQuery($optionsQuery,true);
	        return $options;
        }
        else
            return "NO";					
  	 }
}
