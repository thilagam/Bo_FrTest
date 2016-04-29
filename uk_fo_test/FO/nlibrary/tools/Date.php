<?php

class Date
{
	//Private properties
	var $date;    //current date value (default : date of day)
	
/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
	function Date($date="",$lang="fr")
	{
		global $_country;
		$this->lang=$lang;
		//Initialization of properties
		switch($_country)
		{
			case "fr" : $decal = 0;break;
			case "en" : $decal = 25200;break;
			case "pt" : $decal = 14400;break;
		}
		$mktime = mktime()-$decal;
		if($date=="")$date = date("YmdHis",$mktime);
		$this->setDate($date);
	}
	
	//return the exploded date
	function getDateArray()
	{
		$tab["year"] = substr($this->getDate(),0,4);
		$tab["month"] = substr($this->getDate(),4,2);
		$tab["day"] = substr($this->getDate(),6,2);
		$tab["hour"] = substr($this->getDate(),8,2);
		$tab["minute"] = substr($this->getDate(),10,2);
		$tab["second"] = substr($this->getDate(),12,2);
		
		return $tab;
	}
	function getPostDate($timeStamp = 604800)
	{
		$array = $this->getDateArray();
		$mktime = mktime($array["hour"],$array["minute"],$array["second"],$array["month"],$array["day"],$array["year"]);
		$time = $mktime+$timeStamp;
		$calendar = cal_from_jd(unixtojd($time), CAL_GREGORIAN);
		list($month,$day,$year) = explode("/",$calendar["date"]);
		if(strlen($day)==1)$day = "0".$day;
		if(strlen($month)==1)$month = "0".$month;
		return $year.$month.$day."000000";
	}

	function getDateFormatedforDiff()
	{
		$tab = $this->getDateArray();
		return $tab["month"]."/".$tab["day"]."/".$tab["year"];
	}
	
	function getAntDate($timeStamp = 604800)
	{
		$array = $this->getDateArray();
		$mktime = mktime($array["hour"],$array["minute"],$array["second"],$array["month"],$array["day"],$array["year"]);
		$time = $mktime-$timeStamp;
		$calendar = cal_from_jd(unixtojd($time), CAL_GREGORIAN);
		list($month,$day,$year) = explode("/",$calendar["date"]);
		if(strlen($day)==1)$day = "0".$day;
		if(strlen($month)==1)$month = "0".$month;
		return $year.$month.$day."000000";
	}
	function getSubDate($start,$length)
	{
		return substr($this->getDate(),$start,$length);
	}
	function getDateFormat()
	{
		return substr($this->getDate(),0,8);
	}
	function getTimeFormat()
	{
		return substr($this->getDate(),8,14);
	}
	function getDateFormated()
	{
		$tab = $this->getDateArray();
		if($this->lang == 'en')
		return $tab["month"]."/".$tab["day"]."/".$tab["year"];
		else
		return $tab["day"]."/".$tab["month"]."/".$tab["year"];
	}
	function getDateFormatedEN()
	{
		$tab = $this->getDateArray();
		return $tab["year"]."/".$tab["month"]."/".$tab["day"];
	}
	function getDateFormatedUS()
	{
		$tab = $this->getDateArray();
		return $tab["month"]."/".$tab["day"]."/".$tab["year"];
	}
	function getDateFormatedPt()
	{
		$tab = $this->getDateArray();
		return $tab["day"]."/".$tab["month"]."/".$tab["year"];
	}
	function getDateFormatedForAdmin()
	{
		$tab = $this->getDateArray();
		return $tab["day"]."-".$tab["month"]."-".$tab["year"];
	}
	function getTimeFormated()
	{
		$tab = $this->getDateArray();
		return $tab["hour"].":".$tab["minute"];
	}
	function getTimeFormatedEN()
	{
		$tab = $this->getDateArray();
		$hour = $tab["hour"]-12;

		if($hour>0){$hour = $hour;$ext ="pm";}
		if($hour==0){$hour = $tab["hour"];$ext ="pm";}
		if($hour<0) {$hour = $tab["hour"];$ext ="am";}
		if($hour==-12){$hour = 12;$ext ="am";}		
		return $hour.":".$tab["minute"]." ".$ext;
	}
	function getyear()
	{
		$tab = $this->getDateArray();
		return $tab["year"];
	}
	function getMonth()
	{
		$tab = $this->getDateArray();
		return $tab["month"];
	}
	function getDay()
	{
		$tab = $this->getDateArray();
		return $tab["day"];
	}
	//convert day in second
	function getSecond($day)
	{
		return $day*60*60*24;
	}
	//convert date in unix timeStamp format
	function getMktime()
	{
		$array = $this->getDateArray();
		return mktime($array["hour"],$array["minute"],$array["second"],$array["month"],$array["day"],$array["year"]);
	}
	// get methods	
	function secondToDay($timeStamp) 
	{
		$day = (int)($timeStamp/86400);
		return $day;
	}
	// get methods
	function getDate()
	{
		return $this->date;
	}
			
	// set methods
	function setDate($date)
	{
		$this->date = $date;
	}
	
/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/


}
?>