<?php

class Ep_Delivery_Pollconfiguration extends Ep_Db_Identifier
{
	protected $_name = 'Poll_configuration';
	
	public function getPollquestions($id=null,$searchparameters=NULL)
    {
        $where="";
		if($id!="")
			$where = "WHERE id = '".$id."'";
			
		if($searchparameters['type'])
			$where = " WHERE type IN(".$searchparameters['type'].") AND status='active'";
			
		$QuesQuery="SELECT * FROM ".$this->_name." ".$where." order by id";
		
		if(($Quesresult=$this->getQuery($QuesQuery,true))!=NULL)
			return $Quesresult;
		else
				return NUll;
    }
	
	public function UpdatePollQuestion($parray)
	{
		$array=array();
		$array['title']=$parray['title'];
		$array['type']=$parray['type'];
		
		//Radio or Check
		if($parray['type'] == 'radio' || $parray['type'] == 'checkbox')
		{
			if(count($parray['option'])>0)
			{
				$parray['option']=array_filter($_POST['option']);
				$opt = implode("|",$parray['option']);
				$array['option'] = $opt;
			}
		}		
		
		if($parray['quesid']!="")
		{
			$where="id='".$parray['quesid']."'";
			$this->updateQuery($array,$where);
		}
		else
		{
			$array['id']=$this->getIdentifier();
			$this->insertQuery($array);
		}	
			
	}
	
	public function DeletePollQuestion($ques)
	{
		$whered = "id = '".$ques."'";
		$this->deleteQuery($whered);
	}
}