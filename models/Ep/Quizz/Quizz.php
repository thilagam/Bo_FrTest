<?php

class Ep_Quizz_quizz extends Ep_Db_Identifier
{
	protected $_name = 'quizz';
	
	public function insertQuizz($quizz,$qa,$author)
    { 
        $this->_name = 'quizz' ;  
        $this->createIdentifier() ;
        $quiz['id']    =    $this->getIdentifier() ;
        $quiz['title']    =   $quizz['quizztitle'] ;
        $quiz['category']    =   $quizz['category'] ;
        $quiz['status']    =   $quizz['status'] ;
        $quiz['quest_count']    =   $quizz['quest_count'] ;
        $quiz['correct_ans_count']    =   $quizz['correct_ans_count'] ;
        $quiz['setuptime']    =   $quizz['setuptime'] ; //.'|'.$quizz['setuptime_option'] ;
        $quiz['ans_count']    =   4 ;
        $quiz['creation_date']    =   date('Y-m-d') ;
        $quiz['created_by']    =   $author ;
        
        if($this->insertQuery($quiz)) :
        
            $qid    =   $this->getIdentifier() ;
            for($i=0; $i<$quizz['quest_count']; $i++) :
                if($qa['qn'.$i]!="")
				{
                $this->_name = 'quizzquestions' ;
                $this->createIdentifier() ;
                $quizq['id']    =   $this->getIdentifier() ;
                $quizq['question']    =   $qa['qn'.$i] ;
                $quizq['quizz_id']    =   $qid ;

                if($this->insertQuery($quizq)) :
                    $this->_name = 'quizzanswers';
                    for($j=0; $j<4; $j++) :
                        $this->createIdentifier() ;
                        $quiza['id']    =   $this->getIdentifier() ;
                        $quiza['text']    =   $qa['an'.$i.$j] ;
                        $quiza['quest_id']    =   $quizq['id'] ;
                        $this->insertQuery($quiza) ;
                        
                        if(($j+1) == $qa['r_an'.$i])
                        {
                            $this->_name = 'quizzquestions' ;
                            $questwhere  =   " id='".$quiza['quest_id']."' " ;
                            $quest1['ans_id']    =   $quiza['id'] ;
                            $this->updateQuery($quest1,$questwhere) ;
                            $this->_name = 'quizzanswers';
                        }                        
                    endfor ;
                endif ;
                  }                 
            endfor ;
			return $qid;
        endif ;
    }
    
    public function updateQuizz($id,$quizz,$qa,$author)
    {   //echo '<pre>'.$id; print_r($quizz); print_r($qa);
        $this->_name = 'quizz' ;
        $quizwhere  =   " id='".$id."'" ;
        $quiz['title']    =   $quizz['quizztitle'] ;
        $quiz['category']    =   $quizz['category'] ;
        $quiz['status']    =   $quizz['status'] ;
        $quiz['quest_count']    =   $quizz['quest_count'] ;
        $quiz['correct_ans_count']    =   $quizz['correct_ans_count'] ;
        $quiz['setuptime']    =   $quizz['setuptime'] ; //.'|'.$quizz['setuptime_option'] ;
        $quiz['ans_count']    =   4 ;
        $quiz['creation_date']    =   date('Y-m-d') ;
        $quiz['created_by']    =   $author ;
        
        $this->updateQuery($quiz,$quizwhere) ;
        
        $i = 0;
        while($i<$quiz['quest_count']) :
            
            $this->_name = 'quizzquestions' ;
            
            if(!empty($qa['qstid'.$i])) :
                $questwhere  =   " id='".$qa['qstid'.$i]."' " ;
                $quest['question']    =   $qa['qn'.$i] ;
                $this->updateQuery($quest,$questwhere) ;
                
                $ansSel   =   "SELECT id FROM quizzanswers WHERE quest_id='".$qa['qstid'.$i]."'";
                $anIds    =   $this->getQuery($ansSel,true) ;
    
                $j =   0 ;
                $this->_name = 'quizzanswers' ;
                foreach ($anIds as $key1 => $anId) {
                    
                    $answhere  =   " id='".$anId['id']."'" ;
                    $ans['text']    =   $qa['an'.$i.$j] ;
                    $this->updateQuery($ans,$answhere) ;
                    
                    if(($j+1) == $qa['r_an'.$i])
                    {
                        $this->_name = 'quizzquestions' ;
                        $questwhere1  =   " id='".$qa['qstid'.$i]."' " ;
                        $quest1['ans_id']    =   $anId['id'] ;
                        $this->updateQuery($quest1,$questwhere1) ;
                        $this->_name = 'quizzanswers';
                    }                    
                    $j++ ;
                }
                
            else :
                
                $this->createIdentifier() ;
                $quizq['id']    =   $this->getIdentifier() ;
                $quizq['question']    =   $qa['qn'.$i] ;
                $quizq['quizz_id']    =   $id ;

                if($this->insertQuery($quizq)) :
                    $this->_name = 'quizzanswers';
                    for($j=0; $j<4; $j++) :
                        $this->createIdentifier() ;
                        $quiza['id']    =   $this->getIdentifier() ;
                        $quiza['text']    =   $qa['an'.$i.$j] ;
                        $quiza['quest_id']    =   $quizq['id'] ;
                        $this->insertQuery($quiza) ;
                        
                        if(($j+1) == $qa['r_an'.$i])
                        {
                            $this->_name = 'quizzquestions' ;
                            $questwhere  =   " id='".$quiza['quest_id']."' " ;
                            $quest1['ans_id']    =   $quiza['id'] ;
                            $this->updateQuery($quest1,$questwhere) ;
                            $this->_name = 'quizzanswers';
                        }                        
                    endfor ;
                endif ;
                
            endif ;
            $i++;
        endwhile ;
    }

    //Delete a quizz
    public function delQuizz($id)
    {
        $this->_name = 'quizz' ;
        $this->deleteQuery(" id = '".$id."'") ;
        
        $qnsSel   =   "SELECT id FROM quizzquestions WHERE quizz_id='".$id."'";
        $qnIds    =   $this->getQuery($qnsSel,true) ;
        
        $this->_name = 'quizzanswers' ;
        foreach ($qnIds as $key => $qnId) {
            $this->deleteQuery(" quest_id = '".$qnId."'") ;
        }
        
        $this->_name = 'quizzquestions' ;
        $this->deleteQuery(" quizz_id = '".$id."'") ;
    }
    
    /* get all quizzes */
    public function GetquizzList ($condition)
    {
       $result1 = $this->getQuery("SELECT q.*, COUNT(qp.quiz_id) AS participants FROM quizz q INNER JOIN QuizParticipation qp on q.id=qp.quiz_id " . (!empty($condition) ? 'WHERE' : '') . " $condition GROUP BY qp.quiz_id ORDER BY q.creation_date DESC",true) ;
       $result2 = $this->getQuery("SELECT q.*, 0 AS participants FROM quizz q WHERE $condition " . (!empty($condition) ? 'AND' : '') . " q.id NOT IN (SELECT DISTINCT quiz_id FROM QuizParticipation) ORDER BY q.creation_date DESC",true) ;
       return array_merge((array)$result1, (array)$result2) ;
    }
    
    /* Lists of all active quizzes */
    public function quizzLists ()
    {
        $result1 = $this->getQuery("SELECT q.id, q.title, q.category, COUNT(qp.quiz_id) AS participants FROM quizz q INNER JOIN QuizParticipation qp on q.id=qp.quiz_id WHERE q.status ='1' GROUP BY qp.quiz_id",true) ;
        $result2 = $this->getQuery("SELECT id, title, category, 0 AS participants FROM quizz WHERE id NOT IN (SELECT DISTINCT quiz_id FROM QuizParticipation) AND status ='1'",true) ;
        
        return array_merge((array)$result1, (array)$result2) ;
    }
    
    /* get quizz general info */
    public function Getquizz ($id)
    {
        $result = $this->getQuery("SELECT * FROM quizz Where id = '".$id."'",true) ;
        return $result[0] ;
    }
    /* get quizz info by quizz id */
    public function Getquizzinfo ($id)
    {
        $result = $this->getQuery("SELECT qq.id, qq.ans_id, qq.question, qa.id AS aid, qa.text FROM quizzquestions qq INNER JOIN quizzanswers qa ON qq.id=qa.quest_id Where qq.quizz_id = '".$id."'",true) ;
        return $result ;
    }
    
	public function ListQuizz()
	{
		$quizzQuery="SELECT * FROM ".$this->_name." WHERE status='1'";
		$quizzdetails= $this->getQuery($quizzQuery,true);
		return $quizzdetails;
	}
    
    public function quizzdetails($id)
    {
        $quizzQuery="SELECT * FROM ".$this->_name." WHERE id='".$id."'";
        $quizzdetails= $this->getQuery($quizzQuery,true);
        return $quizzdetails;
    }
    
    public function viewQuizz($id)
    {
        $quizzdetails= $this->getQuery("SELECT q.title, qn.question, qn.id as quest_id, qn.ans_id as r_ans_id, qa.id as ans_id, qa.text as options FROM quizz q INNER JOIN quizzquestions qn ON q.id=qn.quizz_id INNER JOIN quizzanswers qa ON qa.quest_id=qn.id WHERE q.id='".$id."'",true);
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
	
    //Delete a question
	public function delQuestion($id)
	{
        $this->_name = 'quizzquestions' ;
        $this->deleteQuery(" id = '".$id."'") ;
        $this->_name = 'quizzanswers' ;
        $this->deleteQuery(" quest_id = '".$id."'") ;
	}
	
	/* Quizz participation details */
	public function GetParticipantsList($id,$ao=NULL)
    {
		if($ao)
			$whereao=" AND qp.delivery_id='".$ao."'";
		else
			$whereao="";
		$quizzQuery="SELECT qp.user_id, qp.qualified, qp.num_correct, qp.num_total, qp.percent, qp.created_at, up.first_name, up.last_name FROM QuizParticipation qp LEFT JOIN UserPlus up ON qp.user_id=up.user_id WHERE qp.quiz_id='".$id."'".$whereao;
        $quizzdetails= $this->getQuery($quizzQuery,true);
        return $quizzdetails;
    }
	
	public function test()
	{
        $this->_name = 'quizzquestions' ;
        $questwhere  =   " id='130425165724475'" ;
        $quest1['ans_id']    =   '123456789' ;
        $this->updateQuery($quest1,$questwhere) ;
	}
	
	public function getQuizzMarks($art_id, $contrib_id)
    {
        $quizzQuery="SELECT CONCAT_WS('/', num_correct, num_total) AS quizzmarks FROM  QuizParticipation WHERE article_id='".$art_id."' AND user_id='".$contrib_id."'";
        $quizzdetails= $this->getQuery($quizzQuery,true);
        return $quizzdetails;
    }
	
	public function faliedcontribs($quiz)
	{
		$quizzQuery="SELECT user_id FROM QuizParticipation WHERE quiz_id='".$quiz."' AND qualified='no'";
        $quizzdetails= $this->getQuery($quizzQuery,true);
        return $quizzdetails;
	}
	
	public function CheckQuizzLinked($quizz)
	{
		$quizzdQuery="SELECT count(*) as linkcount FROM Delivery WHERE quiz='".$quizz."'";
        $quizzddetails= $this->getQuery($quizzdQuery,true);
        return $quizzddetails[0]['linkcount'];
	}
	
	public function deleteQuizz($quizz)
	{
		$whereq=" id='".$quizz."'";
		$this->deleteQuery($whereq);
	}

	 public function updateQuizzStatus($data, $query)
    {
        $this->_name = 'quizz' ;
		$this->updateQuery($data, $query);
    }
    public function getUserQuizDetails($userId){
        $query = "SELECT QUIZ . * , QP . *
        FROM `quizz` AS QUIZ
        LEFT JOIN `QuizParticipation` AS QP ON QP.`quiz_id` = QUIZ.`id`
        WHERE QP.`user_id` = '".$userId."'
        ORDER BY QP.`created_at` ASC";
        if (($result = $this->getQuery($query, true)) != NULL) {
            return $result;
        } else
            return false;
    }
	
	public function getQuizzLinkedAO($qid)
	{
		$quizzdQuery="SELECT d.id,d.title,d.AOtype,d.created_at,d.missiontest,d.test_article,(SELECT count(id) FROM QuizParticipation WHERE quiz_id='".$qid."' AND delivery_id=d.id) as Qparticipants FROM Delivery d WHERE d.quiz='".$qid."'";
        $quizzddetails= $this->getQuery($quizzdQuery,true);
        return $quizzddetails;
	}
	
}	