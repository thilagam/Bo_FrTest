<?php
/**
 * EditPlaceController
 * This controller is responsible for Contributor Quiz Operations*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
 require_once('ContribController.php');
Class QuizController Extends ContribController
{
    //Action to check whether AO is linked to Quiz or not by using Article Id
    public function checkQuizAoAction()
    {
        if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
              unset($this->EP_Contrib_Quiz->answers);
              unset($this->EP_Contrib_Quiz->quiz_marks);
              unset($this->EP_Contrib_Quiz->question);             


            $QuizParams=$this->_request->getParams();

            $article_id=$QuizParams['article_id'];       
            
            if($article_id)
            {
                $quiz_obj=new Ep_Quiz_Quiz();
                $checkQuiz=$quiz_obj->checkQuizAO($article_id);

                echo json_encode(array("status"=>$checkQuiz));
            }    
            else
            {
                echo json_encode(array("status"=>"NO"));
            }
        }    

        
    }
    //quiz participation popup
    public function participateQuizAction()
    {
       
       if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
            $QuizParams=$this->_request->getParams();

            $article_id=$QuizParams['article_id'];
            $quiz_identifier=$QuizParams['quiz_identifier'];
            $question_id=$QuizParams['question_id'];

            //Setting start time of the Quiz
            if(!$this->EP_Contrib_Quiz->startTime[$quiz_identifier])
                $this->EP_Contrib_Quiz->startTime[$quiz_identifier]=time();

            if($article_id && $quiz_identifier)
            {
                $quiz_obj=new Ep_Quiz_Quiz();

                $Questions=$quiz_obj->getQustions($quiz_identifier,$article_id);

                if($Questions!="NO" && is_array($Questions) && count($Questions)>0)
                {
                    $qcnt=0;
                    foreach($Questions as $question)
                    {
                        $Questions[$qcnt]['options']=$quiz_obj->getOptions($question['id']);
                        $Questions[$qcnt]['timestamp']=$this->EP_Contrib_Quiz->startTime[$quiz_identifier]+$question['quiz_duration']*60;

                        //Adding answers to session
                        $this->EP_Contrib_Quiz->answers[$question['id']]=$question['ans_id'];
                        //Adding how many questions need to be correct for this quiz
                        $this->EP_Contrib_Quiz->quiz_marks[$question['quizz_id']]=$question['quiz_marks'];

                        $qcnt++;
                    }
                    if(!$question_id)
                        $question_id=$Questions[0]['id'];

                    $qarray=quiz_paginate($Questions,$question_id);
                    $current=$qarray['current']+1;
                    $next_id=$qarray['next'];
                    $totalQuestions=$qarray['total'];
                    $current_question[]=$Questions[$qarray['current']];

                    //echo "<pre>";print_r($qarray);   
                    //echo "<pre>";print_r($this->EP_Contrib_Quiz->answers);                       
                    //echo "<pre>";print_r($current_question); 
                    
                    $this->_view->current_question=$current_question;                 
                    $this->_view->current=$current;                 
                    $this->_view->totalQuestions=$totalQuestions;                 
                    $this->_view->next_id=$next_id;
                }        
                $this->render("Contrib_quiz_popup");        
            }    
        }    
    }
	/*recruitment quiz participation popup*/
    public function participateRecruitmentQuizAction()
    {
       if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
            $QuizParams=$this->_request->getParams();
            $recruitment_id=$QuizParams['recruitment_id'];
            $quiz_identifier=$QuizParams['quiz_identifier'];
            $question_id=$QuizParams['question_id'];
            //Setting start time of the Quiz
            if(!$this->EP_Contrib_Quiz->startTime[$quiz_identifier])
                $this->EP_Contrib_Quiz->startTime[$quiz_identifier]=time();
            if($recruitment_id && $quiz_identifier)
            {
                $quiz_obj=new Ep_Quiz_Quiz();
                $Questions=$quiz_obj->getRecruitmentQuestions($quiz_identifier,$recruitment_id);
                if($Questions!="NO" && is_array($Questions) && count($Questions)>0)
                {
                    $qcnt=0;
                    foreach($Questions as $question)
                    {
                        $Questions[$qcnt]['options']=$quiz_obj->getOptions($question['id']);
                        $Questions[$qcnt]['timestamp']=$this->EP_Contrib_Quiz->startTime[$quiz_identifier]+$question['quiz_duration']*60;
                        //Adding answers to session
                        $this->EP_Contrib_Quiz->answers[$question['id']]=$question['ans_id'];
                        //Adding how many questions need to be correct for this quiz
                        $this->EP_Contrib_Quiz->quiz_marks[$question['quizz_id']]=$question['quiz_marks'];
                        $qcnt++;
                    }
                    if(!$question_id)
                        $question_id=$Questions[0]['id'];
                    $qarray=quiz_paginate($Questions,$question_id);
                    $current=$qarray['current']+1;
                    $next_id=$qarray['next'];
                    $totalQuestions=$qarray['total'];
                    $current_question[]=$Questions[$qarray['current']];
                   // echo "<pre>";print_r($qarray);   
                    //echo "<pre>";print_r($this->EP_Contrib_Quiz->answers);                       
                    //echo "<pre>";print_r($current_question); 
                    $this->_view->current_question=$current_question;                 
                    $this->_view->current=$current;                 
                    $this->_view->totalQuestions=$totalQuestions;                 
                    $this->_view->next_id=$next_id;
                }        
                $this->render("Contrib_recruitment_quiz_popup");        
            }    
        }    
    }	
    //add answere to the session when user clicked on option in quiz popup
    public function addOptionAction()
    {
        if($this->_helper->EpCustom->checksession())// && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
            $answerParams=$this->_request->getParams();   

            $selectedOption=$answerParams['qoptions'];
            $quiz=$answerParams['quiz'];
            $question=$answerParams['question'];
            $article_id=$answerParams['article_id'];

            if($selectedOption && $quiz && $question && $article_id)
            {

                //saving selected option in session
                $answer_array=array('id'=>$question,'selected_option'=>$selectedOption,'quiz_id'=>$quiz,'article_id'=>$article_id);
                $this->EP_Contrib_Quiz->question[$question]=$answer_array;               
            }            
			/*saving responses and participations of users*/
			$totalQuestions=count($this->EP_Contrib_Quiz->answers);
			$correctCnt=0;
			$wrngCnt=0;
			foreach($this->EP_Contrib_Quiz->question as $question=>$answers)   
			{
				if($answers['selected_option']==$this->EP_Contrib_Quiz->answers[$question])
				{
					$correctCnt++;
				}
				else
				{
					$wrngCnt++;
				}
				$minQuestionCorrect=$this->EP_Contrib_Quiz->quiz_marks[$quiz];
			}
			/*checking min required and correct count*/
			$quizArray['num_correct']=$correctCnt;
			$quizArray['num_total']=$totalQuestions;
			$pecent=round(($correctCnt/$totalQuestions)*100);				
			$quizArray['percent']=$pecent;			
			if($correctCnt>=$minQuestionCorrect)
				$quizArray['qualified']='yes';
			else
				$quizArray['qualified']='no';
			if($answerParams['recruitment']=='yes')
				$quizArray['type']='recruitment';
			else
				$quizArray['type']='article';			
			$quiz_obj=new Ep_Quiz_Participation();
			if(!$this->EP_Contrib_Quiz->quizParticipationId)
			{
				/*getting delivery id of article*/				
				if($answerParams['recruitment']=='yes')
				{
					$recruitObj=new Ep_Recruitment_Participation();				
					$recruitmentDetails=$recruitObj->getRecruitmentDetails($article_id);
					$delivery_id=$recruitmentDetails[0]['recruitment_id'];
				}
				else{
					$article_obj=new Ep_Article_Article();
					$article_params['articleId']=$article_id;
					$articleDetails=$article_obj->getArticleDetails($article_params);
					$delivery_id=$articleDetails[0]['deliveryid'];
				}
				$quizArray['quiz_id']=$quiz;
				$quizArray['delivery_id']=$delivery_id;
				$quizArray['article_id']=$article_id;				
				$quizArray['user_id']=$this->contrib_identifier;
				$quizArray['reason']='partial';
				$quizParticipationId=$quiz_obj->InsertQuizParticipation($quizArray);
				$this->EP_Contrib_Quiz->quizParticipationId=$quizParticipationId;
			}
			else if($this->EP_Contrib_Quiz->quizParticipationId){
				$quizParticipationId=$this->EP_Contrib_Quiz->quizParticipationId;
				$quiz_obj->updateQuizParticipation($quizArray,$quizParticipationId);
			}
			if($quizParticipationId && $quiz && $question)
			{				
				//inserting or updating QUiz Response
				$response_obj=new Ep_Quiz_UserResponse();
				$checkResponse=$response_obj->checkQuestionResponse($quizParticipationId,$this->contrib_identifier,$quiz,$question);			
				if($checkResponse)
				{
					$responseid=$checkResponse[0]['id'];
					$updateresponseArray['response']=$selectedOption;
					$response_obj->updateQuizResponse($updateresponseArray,$responseid);
				}
				else{
					$responseArray['participation_id']=$quizParticipationId;
					$responseArray['quiz_id']=$quiz;
					$responseArray['question_id']=$question;
					$responseArray['user_id']=$this->contrib_identifier;
					$responseArray['response']=$selectedOption;
					$responseArray['created_at']=date("Y-m-d H:i:s");
					$response_obj->InsertQuizResponse($responseArray);
				}			
			}
			//$this->EP_Contrib_reg->quizParticipationId=$quizParticipationId;	
            //echo "<pre>";print_r($this->EP_Contrib_Quiz->question);print_r($quizArray);
        }    
    }
    public function saveQuizAction()
    {
        if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
			 $answerParams=$this->_request->getParams(); 
			 $quiz_obj=new Ep_Quiz_Participation();
             $totalQuestions=count($this->EP_Contrib_Quiz->answers);
		

             $correctCnt=0;
             $wrngCnt=0;

             foreach($this->EP_Contrib_Quiz->question as $question=>$answers)   
             {
                if($answers['selected_option']==$this->EP_Contrib_Quiz->answers[$question])
                {
                    $correctCnt++;
                }
                else
                {
                    $wrngCnt++;
                }
                $minQuestionCorrect=$this->EP_Contrib_Quiz->quiz_marks[$answers['quiz_id']];
                $quiz_id=$answers['quiz_id'];
                $article_id=$answers['article_id'];
			 }
			if($answerParams['recruitment']=='yes')
			{
				$participationExist=$quiz_obj->checkQuizParticipationExist($article_id,$quiz_id,$this->EP_Contrib_reg->clientidentifier);
             }

			//print_r($participationExist);echo $participationExist;exit;
			
			/* if($participationExist=='NO' || $answerParams['recruitment']!='yes')
			{ */
             /*echo "<pre>";print_r($this->EP_Contrib_Quiz->question);
             echo "<pre>";print_r($this->EP_Contrib_Quiz->answers);
             echo "<pre>";print_r($this->EP_Contrib_Quiz->quiz_marks);
             echo "<pre>";print_r($correctCnt);
             echo "<pre>";print_r($totalQuestions);
             exit;*/



            if($answerParams['recruitment']=='yes')//Added w.r.t Recruitment Quiz
			{
				$recruitObj=new Ep_Recruitment_Participation();				
				$recruitmentDetails=$recruitObj->getRecruitmentDetails($article_id);
				$delivery_id=$recruitmentDetails[0]['recruitment_id'];				
				//get article id that was not assigned to a user
				$recruitObj=new Ep_Recruitment_Participation();
				$getArticleDetails= $recruitObj->getNotAssignedArticle($delivery_id);
				//echo "<pre>";print_r($getArticleDetails);exit;
				if($getArticleDetails)
				{
					$article_id=$getArticleDetails[0]['id'];
				}
				$quizArray['type']='recruitment';
				$this->EP_Contrib_reg->recruitment_article_id=$article_id;				
			}
			else
			{	//getting article and delivry details
             $article_obj=new Ep_Article_Article();
             $article_params['articleId']=$article_id;
             $articleDetails=$article_obj->getArticleDetails($article_params);
             $delivery_id=$articleDetails[0]['deliveryid'];
				$quizArray['type']='article';
			}	
             $quizArray['quiz_id']=$quiz_id;
             $quizArray['delivery_id']=$delivery_id;
             $quizArray['article_id']=$article_id;
             $quizArray['user_id']=$this->contrib_identifier;
             $quizArray['num_correct']=$correctCnt;
             $quizArray['num_total']=$totalQuestions;
				 $quizArray['reason']='validated';
              //echo $correctCnt."--".$minQuestionCorrect;exit; 
             if($correctCnt>=$minQuestionCorrect)
             {
                $pecent=round(($correctCnt/$totalQuestions)*100);               
                
                $article_title=$articleDetails[0]['title'];
                $quizArray['qualified']='yes';
                $quizArray['percent']=$pecent;

               //Inserting into QuizParticipation Table
			   //echo "<pre>";print_r($quizArray);exit;			   
					if($this->EP_Contrib_Quiz->quizParticipationId)
					{
						$quizParticipationId=$this->EP_Contrib_Quiz->quizParticipationId;
						$quiz_obj->updateQuizParticipation($quizArray,$quizParticipationId);
					}
					else{
                $quizParticipationId=$quiz_obj->InsertQuizParticipation($quizArray);
					}
                $this->_view->percent=$pecent;
                $this->_view->num_correct=$correctCnt;
                $this->_view->num_total=$totalQuestions;
                $this->_view->article_title=$article_title;
                if($answerParams['recruitment']!='yes') //Added w.r.t Recruitment Quiz
				{
                //adding this article to the cart
                $this->EP_Contrib_Cart->cart[$article_id]=1;
                $selected_ao_count=(count($this->EP_Contrib_Cart->cart)+count($this->EP_Contrib_Cart->poll)+count($this->EP_Contrib_Cart->correction));
                $this->_view->selected_ao_count=$selected_ao_count;   
				}	
                $this->render("Contrib_quiz_ok"); 
             }  
             else
             {
                $pecent=round(($correctCnt/$totalQuestions)*100);
                $quizArray['qualified']='no';
                $quizArray['percent']=$pecent;

                //Inserting into QuizParticipation Table
					if($this->EP_Contrib_Quiz->quizParticipationId)
					{
						$quizParticipationId=$this->EP_Contrib_Quiz->quizParticipationId;
						$quiz_obj->updateQuizParticipation($quizArray,$quizParticipationId);
					}
					else{
                $quizParticipationId=$quiz_obj->InsertQuizParticipation($quizArray);
					}					

                $this->_view->percent=$pecent;
                $this->_view->num_correct=$correctCnt;
                $this->_view->num_total=$totalQuestions;
                $this->render("Contrib_quiz_notok"); 
             }
			$this->EP_Contrib_reg->quizParticipationId=$quizParticipationId; //Added w.r.t recruitment
			$this->EP_Contrib_reg->quiz_qualified=$quizArray['qualified'];
            /* }
			else{
				$this->EP_Contrib_reg->quizParticipationId=$participationExist[0]['id'];//Added w.r.t recruitment
				$this->EP_Contrib_reg->quiz_qualified=$participationExist[0]['qualified'];
				$this->EP_Contrib_reg->recruitment_article_id=$participationExist[0]['article_id'];
			} */
              unset($this->EP_Contrib_Quiz->answers);
              unset($this->EP_Contrib_Quiz->quiz_marks);
              unset($this->EP_Contrib_Quiz->question);
              unset($this->EP_Contrib_Quiz->startTime);
			unset($this->EP_Contrib_Quiz->quizParticipationId);
             
        }
    }
    public function cancleQuizAction()
    {
        if($this->_helper->EpCustom->checksession() && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
            $cancleParams=$this->_request->getParams(); 

            //echo "<pre>";print_r($cancleParams);
            $quiz=$cancleParams['quiz'];       
            $article_id=$cancleParams['article_id'];
			$reason=$cancleParams['reason'];
            if($quiz && $article_id)
            {
				$correctCnt=0;
				$wrngCnt=0;
				foreach($this->EP_Contrib_Quiz->question as $question=>$answers)   
				{
					if($answers['selected_option']==$this->EP_Contrib_Quiz->answers[$question])
					{
						$correctCnt++;
					}
					else
					{
						$wrngCnt++;
					}
					$minQuestionCorrect=$this->EP_Contrib_Quiz->quiz_marks[$answers['quiz_id']];
				}				
               $quiz_obj=new Ep_Quiz_Quiz();
				if($cancleParams['recruitment']=='yes')//Added w.r.t Recruitment Quiz
					$Questions=$quiz_obj->getRecruitmentQuestions($quiz,$article_id);
				else
               $Questions=$quiz_obj->getQustions($quiz,$article_id);
               if($Questions!="NO")
                  $totalQuestions=count($Questions);
               else
                  $totalQuestions=0;

               $quiz_obj=new Ep_Quiz_Participation();
				if($cancleParams['recruitment']=='yes')
				{
					$participationExist=$quiz_obj->checkQuizParticipationExist($article_id,$quiz_id,$this->EP_Contrib_reg->clientidentifier);
				}	
				//print_r($participationExist);
				//echo $participationExist;exit;
				/* if($participationExist=='NO' || $answerParams['recruitment']!='yes')
				{ */
                if($cancleParams['recruitment']=='yes')//Added w.r.t Recruitment Quiz
				{
					$recruitObj=new Ep_Recruitment_Participation();				
					$recruitmentDetails=$recruitObj->getRecruitmentDetails($article_id);
					$delivery_id=$recruitmentDetails[0]['recruitment_id'];				
					//get article id that was not assigned to a user
					$recruitObj=new Ep_Recruitment_Participation();
					$getArticleDetails= $recruitObj->getNotAssignedArticle($delivery_id);
					//echo "<pre>";print_r($getArticleDetails);exit;
					if($getArticleDetails)
					{
						$article_id=$getArticleDetails[0]['id'];
					}
					$quizArray['type']='recruitment';
					$this->EP_Contrib_reg->recruitment_article_id=$article_id;	
				}
				else
				{   
               //getting article and delivry details

               $article_obj=new Ep_Article_Article();
               $article_params['articleId']=$article_id;
               $articleDetails=$article_obj->getArticleDetails($article_params);
               $delivery_id=$articleDetails[0]['deliveryid'];
				   $quizArray['type']='article';
				}   
					if($correctCnt>=$minQuestionCorrect && $correctCnt > 0)
						$quizArray['qualified']='yes';	
					else
						$quizArray['qualified']='no';
					$pecent=round(($correctCnt/$totalQuestions)*100);					
					$quizArray['percent']=$pecent;
               $quizArray['quiz_id']=$quiz;
               $quizArray['delivery_id']=$delivery_id;
               $quizArray['article_id']=$article_id;
               $quizArray['user_id']=$this->contrib_identifier;
				   $quizArray['num_correct']=$correctCnt;
               $quizArray['num_total']=$totalQuestions;
					if($reason=='time_out')
						$quizArray['reason']='time_out';
					else
						$quizArray['reason']='cancelled';
				   //$quizArray['qualified']='no';
				   //$quizArray['percent']=0;

               //echo "<pre>";print_r($quizArray);exit;
                //Inserting into QuizParticipation Table               
					if($this->EP_Contrib_Quiz->quizParticipationId)
					{
						$quizParticipationId=$this->EP_Contrib_Quiz->quizParticipationId;
						$quiz_obj->updateQuizParticipation($quizArray,$quizParticipationId);
					}
					else{
                $quizParticipationId=$quiz_obj->InsertQuizParticipation($quizArray);
					}
					//$quizParticipationId=$quiz_obj->InsertQuizParticipation($quizArray);
				$this->EP_Contrib_reg->quizParticipationId=$quizParticipationId; //Added w.r.t recruitment
					$this->EP_Contrib_reg->quiz_qualified=$quizArray['qualified'];
					$this->_view->percent=$pecent;
					$this->_view->num_correct=$correctCnt;
                $this->_view->num_total=$totalQuestions;
					if($quizArray['qualified']=='yes')
						$this->render("Contrib_quiz_ok");
					else				
                $this->render("Contrib_quiz_notok"); 
				//}
				/* else{
					$this->EP_Contrib_reg->quizParticipationId=$participationExist[0]['id'];//Added w.r.t recruitment
					$this->EP_Contrib_reg->quiz_qualified=$participationExist[0]['qualified'];
					$this->EP_Contrib_reg->recruitment_article_id=$participationExist[0]['article_id'];
				} */
                unset($this->EP_Contrib_Quiz->answers);
                unset($this->EP_Contrib_Quiz->quiz_marks);
                unset($this->EP_Contrib_Quiz->question);
                unset($this->EP_Contrib_Quiz->startTime);
				unset($this->EP_Contrib_Quiz->quizParticipationId);
            }    
        }
    }
}   