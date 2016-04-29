<?php

class Ep_User_WhitebookDownloads extends Ep_Db_Identifier
{
	protected $_name = 'WhitebookDownloads';
	
    public function insertdownload($warray)
	{
		$this->insertQuery($warray);
	}
	
	public function updateDownload($warray,$id)
	{
		$idsplit=explode("#",$id);
		$warray["updated_at"]=date("Y-m-d H:i:S");
		$warray["count"]=$idsplit[1]+1;
		$where=" id='".$idsplit[0]."'";
		$this->updateQuery($warray,$where);
	}
	
	public function checkEmailindb($email)
	{
		$ChkQuery="SELECT id,count FROM WhitebookDownloads WHERE email='".$email."'";
		$Chkresult = $this->getQuery($ChkQuery,true);
		if($Chkresult[0]['id']!="")
			return $Chkresult[0]['id'].'#'.$Chkresult[0]['count'];
		else
			return "no";
	}
}