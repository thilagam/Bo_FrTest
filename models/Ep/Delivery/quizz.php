<?php

class Ep_Delivery_quizz extends Ep_Db_Identifier
{
	protected $_name = 'quizz';
	
	public function quizzdetails($id)
	{
		$quizzQuery="SELECT * FROM ".$this->_name." WHERE id='".$id."'";
		$quizzdetails= $this->getQuery($quizzQuery,true);
		return $quizzdetails;
	}
	
	public function ListQuizzbyCategory($cat)
	{
		if($cat!="all")
			$wherecat=" AND category like '%".$cat."%'";
		else
			$wherecat="";
			
		$quizzcatQuery="SELECT * FROM ".$this->_name." WHERE status='1'".$wherecat;
		$quizzcatdetails= $this->getQuery($quizzcatQuery,true);
		return $quizzcatdetails;
	}
	
	public function faliedcontribs($quiz)
	{
		$quizzQuery="SELECT user_id FROM QuizParticipation WHERE quiz_id='".$quiz."' AND qualified='no'";
        $quizzdetails= $this->getQuery($quizzQuery,true);
        return $quizzdetails;
	}
}