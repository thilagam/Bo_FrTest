<?php

class Ep_Delivery_Pollbrief extends Ep_Db_Identifier
{
	protected $_name = 'Poll_brief2';
	
	public function UpdatePollBrief2($barray)
	{
		$array=array();
		$array['pollid']=$barray['poll'];
		if($barray['work']=="yes"){$array['work']="yes";}else{$array['work']="no";}
		if($array['work']=="yes")
		{
			$array['year']=$barray['year'];
			$array['volume']=$barray['volume'];
			$array['articletype']=$barray['articletype'];
			$array['price']=$barray['price'];
			$array['level']=$barray['level'];
		}
		$array['potential']=$barray['potential'];
		$array['potentialannexe']=$barray['potentialannexe'];
		$array['clientype']=$barray['clientype'];
		$array['missionmange']=$barray['missionmange'];
			$dat=explode(" ",$barray['start_date']);
			$dat1=explode("/",$dat[0]);
			$dat2=$dat1[2]."-".$dat1[1]."-".$dat1[0]." ".$dat[1];
		$array['start_date']=$dat2;
		$array['daylimit']=$barray['daylimit'];
		$array['comment']=$barray['comment'];
		
		//Check existing
		$briefpoll=$this->getPollBrief2($barray['poll']);		
			if(count($briefpoll)>0)
			{
				$where="pollid='".$barray['poll']."'";
				$this->updateQuery($array,$where);
			}
			else
			{
				$array['id']=$this->getIdentifier(); 
				$this->insertQuery($array,$where);
			}
		
		
	}
	
	public function getPollBrief2($id)
    {
        $QuesQuery="SELECT * FROM ".$this->_name." WHERE pollid='".$id."'";
		
		if(($Quesresult=$this->getQuery($QuesQuery,true))!=NULL)
			return $Quesresult;
		
    }
}