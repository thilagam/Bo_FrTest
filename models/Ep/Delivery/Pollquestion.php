<?php

class Ep_Delivery_Pollquestion extends Ep_Db_Identifier
{
	protected $_name = 'Poll_question';
	
	public function insertpollQuestions($pid,$parray)
	{
		for($p=1;$p<=count($parray);$p++)
		{
			if(in_array(($parray['quesid_'.$p]),$parray['selques']))
			{
				$array=array();
				$array['id']=$this->createIdentifier(); 
				$array['pollid']=$pid; 
				$array['title']=stripslashes($parray['title_'.$p]);
				$array['type']=$parray['type_'.$p];
				if($array['type']=="timing")
					$array['option']=$parray['timingoption_'.$p];
				elseif($array['type']=="radio" || $array['type']=="checkbox")
					$array['option']=$parray['option_'.$p];
				elseif($array['type']=="price" || $array['type']=="range_price" || $array['type']=="timing")
					$array['maximum']=$parray['maximum_'.$p];
					
				if($array['type']=="range_price" || $array['type']=="timing")	
					$array['minimum']=$parray['minimum_'.$p];
				$array['questionorder']=$parray['order_'.$p];
				$array['configureid']=$parray['quesid_'.$p];
				$array['linkedid']=$parray['link_'.$p];
				//print_r($array);
				$this->insertQuery($array);
			}
		}
	}
	
	public function getQuestions($poll)
	{
		$QuesQuery="SELECT * FROM ".$this->_name." WHERE pollid='".$poll."' ORDER BY id";
		if(($Quesresult=$this->getQuery($QuesQuery,true))!=NULL)
			return $Quesresult;
	}
	
	public function getQuestStats($poll,$smic="")
	{
		$smicCond="";
		if($smic==1)
		{
			$smicval=$this->getSMICvalue($poll);
			//$smicval=11;
			$smicCond=" ((pq.type='price' AND pr.response>".$smicval.") OR pq.type!='price') AND ";
		}	
		$QuesQuery="SELECT pq.id,pq.title, pq.type, pq.option,count(pr.id) as partcount, max(CAST(pr.response AS DECIMAL( 8, 2 ))) as max, min(CAST(pr.response AS DECIMAL( 8, 2 ))) as min, avg(pr.response) as avg 
					FROM 
					Poll_question pq LEFT JOIN PollUserResponse pr ON pq.id=pr.question_id  INNER JOIN Poll_Participation pp ON pp.user_id=pr.user_id AND pp.poll_id=pr.poll_id
					WHERE ".$smicCond."
					pq.pollid='".$poll."' AND (pp.status='active' OR pp.status IS NULL)  GROUP BY pq.id";
		//echo $QuesQuery; //exit; 
		if(($Quesresult=$this->getQuery($QuesQuery,true))!=NULL)
			return $Quesresult;
	}
	
	public function getSMICvalue($poll)
	{
		$SMICQuery="SELECT l.SMIC,c.percentage FROM Poll p INNER JOIN  LanguageSMIC l ON p.language=l.id INNER JOIN  CategoryDifficultyPercent c ON p.category=c.id WHERE p.id='".$poll."'";
		if(($SMICresult=$this->getQuery($SMICQuery,true))!=NULL)
			return $SMICresult[0]['SMIC'] * ($SMICresult[0]['percentage']/100);
	}
	
	public function getRadioresponse($poll,$ques,$var)
	{
		$RaioQuery="SELECT 
						count(*) as rcount 
					FROM 
						PollUserResponse pr LEFT JOIN Poll_Participation pp ON pr.user_id=pp.user_id AND pr.poll_id=pp.poll_id
					WHERE 
						pr.poll_id='".$poll."' AND pr.question_id='".$ques."' AND pr.response LIKE '%".$var."%' AND (pp.status='active' OR pp.status IS NULL)";
		//echo $RaioQuery."<br>";
		if(($Radioresult=$this->getQuery($RaioQuery,true))!=NULL)
			return $Radioresult[0]['rcount'];
	}
	
	public function createIdentifier()
    {
        $d = new Date();
		return $d->getSubDate(5,14).mt_rand(100000,999999);
  	}
}