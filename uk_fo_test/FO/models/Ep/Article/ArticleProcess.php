<?php
/**
 * Registration Model
 * This Model  is responsible for Registration actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Article_ArticleProcess extends Ep_Db_Identifier
{
	protected $_name = 'ArticleProcess';
	private $id;
	private $participate_id;
	private $user_id;
	private $stage;
    private $status;
	private $article_path;
    private $article_name;
    private $whitelist_newkeywords;
    private $version;
    private $article_sent_at;
    private $article_doc_content;
    private $article_words_count;
    private $comments;
    private $marks;
    private $reasons_marks;


	public function loadData($array)
	{
		$this->id=$array["id"];
        $this->participate_id=$array["participate_id"];
        $this->user_id=$array["user_id"];
        $this->stage=$array["stage"];
        $this->status=$array["status"];
        $this->article_path=$array["article_path"];
        $this->article_name=$array["article_name"];
        $this->whitelist_newkeywords=$array["whitelist_newkeywords"];
        $this->version=$array["version"];
        $this->article_sent_at=$array["article_sent_at"];
        $this->article_doc_content=$array["article_doc_content"];
        $this->article_words_count=$array["article_words_count"];
        $this->comments=$array["comments"];
        $this->marks=$array["marks"];
        $this->reasons_marks=$array["reasons_marks"];

		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["id"] = $this->getIdentifier();
        $array["participate_id"] = $this->participate_id;
        $array["user_id"] = $this->user_id;
        $array["stage"] = $this->stage;
        $array["status"] = $this->status;
        $array["article_path"] = $this->article_path;
        $array["article_name"] = $this->article_name;
        $array["whitelist_newkeywords"] = $this->whitelist_newkeywords;
        $array["version"] = $this->version;
        $array["article_sent_at"] = $this->article_sent_at;
        $array["article_doc_content"] = $this->article_doc_content;
        $array["article_words_count"]=$this->article_words_count;
        $array["comments"]=$this->comments;
        $array["marks"]=$this->marks;
        $array["reasons_marks"]=$this->reasons_marks;

		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
/////////checking whether row with given part id is there are not///////////////
	public function checkParticipateId($partId)
	{
		$query = "select participate_id from ".$this->_name." WHERE participate_id='".$partId."'";//." where ".$whereQuery;
	    if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}


    ////////////udate the articleProcess table//////////////////////
    public function updateArticleProcess($data,$query)
    {
      // echo $query;
        //print_r($data1);exit;

       $this->updateQuery($data,$query);

    }
    public function getLatestVersion($participation)
    {
        $query="select max(version)+1 as latestVersion from ArticleProcess where participate_id='".$participation."'";
         if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
         else 
            return 1;
    }
    public function getLatestVersionDetails($participation)
    {
        $query="select * from ArticleProcess where version!=0 and participate_id='".$participation."' ORDER BY version DESC limit 1";
        //echo $query;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
    }
    /////////getting the details of all of particular participation verisons displayed in box ///////////////
    public function getAllVersionDetails($partId,$user_id=NULL)
    {
        // $query = "delete FROM ".$this->_name." where WHERE participate_id=".$partId;
        if($user_id!=NULL)
        {
            if(is_array($user_id))
            {
                $user_id="'".implode("','",$user_id)."'";
                $addQuery=" and ap.user_id in ($user_id)";
            }   
            else
                $addQuery=" and ap.user_id='".$user_id."'";
        }

        $query = "SELECT ap.id, ap.participate_id, ap.user_id, ap.stage, ap.status, ap.article_path, ap.article_name,
	                ap.version, ap.article_sent_at, ap.article_doc_content, ap.article_words_count, ap.comments, ap.marks, u.login, u.email,
	                u.type, up.user_id, up.first_name,up.last_name,u.blackstatus FROM ".$this->_name." ap
	                LEFT JOIN User u ON u.identifier=ap.user_id
	                LEFT JOIN UserPlus up ON up.user_id=ap.user_id WHERE
                    ap.version!=0 and
                    participate_id='".$partId."' ".$addQuery." AND stage IN ('contributor')
                    ORDER BY ap.article_sent_at ASC ";//." where ".$whereQuery;
        // exit;
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /**get article path of a process*/
    public function getArticlePath($processId)
    {
        $query = "select article_path,article_name from ".$this->_name." WHERE id='".$processId."'";//." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	 /////////getting the details of all of particular participation verisons displayed in box ///////////////
    public function getAllVersionDetailsCorrector($partId,$user_id=NULL)
    {
        // $query = "delete FROM ".$this->_name." where WHERE participate_id=".$partId;
        if($user_id!=NULL)
        {
            if(is_array($user_id))
            {
                $user_id="'".implode("','",$user_id)."'";
                $addQuery=" and ap.user_id in ($user_id)";
            }   
            else
                $addQuery=" and ap.user_id='".$user_id."'";
        }

        $query = "SELECT ap.id, ap.participate_id, ap.user_id, ap.stage, ap.status, ap.article_path, ap.article_name, ap.whitelist_newkeywords,
	                ap.version, ap.article_sent_at, ap.article_doc_content, ap.article_words_count, ap.comments, ap.marks, u.login, u.email,
	                u.type, up.user_id, up.first_name,up.last_name,u.blackstatus FROM ".$this->_name." ap
	                LEFT JOIN User u ON u.identifier=ap.user_id
	                LEFT JOIN UserPlus up ON up.user_id=ap.user_id WHERE
                    ap.version!=0 and
                    participate_id='".$partId."' $addQuery AND stage IN ('contributor','corrector')
                    ORDER BY ap.article_sent_at ASC ";//." where ".$whereQuery;
        // exit;
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ////////getting the details of recent verison in process///////////////
    public function getRecentVersion($partId)
    {
        $query = "SELECT id, participate_id, user_id, stage, status, article_path, article_name, version, article_sent_at, marks, comments, reasons_marks FROM ".$this->_name." WHERE
		         participate_id=".$partId." AND version=(select max(version) FROM ".$this->_name." WHERE participate_id=".$partId.")";//." where ".$whereQuery;
        //echo $query;// exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get each email template details///////////////////////////
    public function refuseValidTemplates($id)
    {
        $query = "select identifier, title, content  FROM Template WHERE identifier='".$id."' AND active='yes'";
        // echo "<br>".$query;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	//getting ebookers stencils by latest version of a writer
	 public function getLatestEbookerVersionDetails($partId,$user_id=NULL)
    {
        // $query = "delete FROM ".$this->_name." where WHERE participate_id=".$partId;
        if($user_id!=NULL)
        {
            if(is_array($user_id))
            {
                $user_id="'".implode("','",$user_id)."'";
                $addQuery=" and ap.user_id in ($user_id)";
            }   
            else
                $addQuery=" and ap.user_id='".$user_id."'";
        }

        $query = "SELECT ap.id, ap.participate_id, ap.user_id, ap.stage, ap.status, ap.article_path, ap.article_name,
	                ap.version, ap.article_sent_at, ap.article_doc_content, ap.article_words_count, ap.comments, ap.marks, u.login, u.email,
	                u.type, up.user_id, up.first_name,up.last_name,u.blackstatus FROM ".$this->_name." ap
	                LEFT JOIN User u ON u.identifier=ap.user_id
	                LEFT JOIN UserPlus up ON up.user_id=ap.user_id WHERE
                    ap.version!=0 and
                    participate_id='".$partId."' ".$addQuery." AND stage IN ('contributor')
                    ORDER BY ap.version DESC LIMIT 1 ";//." where ".$whereQuery;
        // exit;
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return NULL;
    }
	//getting ebookers stencils by latest version of a corrector
	 public function getLatestEbookerCorrectorVersionDetails($partId,$user_id=NULL)
    {
        // $query = "delete FROM ".$this->_name." where WHERE participate_id=".$partId;
        if($user_id!=NULL)
        {
            if(is_array($user_id))
            {
                $user_id="'".implode("','",$user_id)."'";
                $addQuery=" and ap.user_id in ($user_id)";
            }   
            else
                $addQuery=" and ap.user_id='".$user_id."'";
        }
        $query = "SELECT ap.id, ap.participate_id, ap.user_id, ap.stage, ap.status, ap.article_path, ap.article_name,
	                ap.version, ap.article_sent_at, ap.article_doc_content, ap.article_words_count, ap.comments, ap.marks, u.login, u.email,
	                u.type, up.user_id, up.first_name,up.last_name,u.blackstatus FROM ".$this->_name." ap
	                LEFT JOIN User u ON u.identifier=ap.user_id
	                LEFT JOIN UserPlus up ON up.user_id=ap.user_id WHERE
                    ap.version!=0 and
                    participate_id='".$partId."' ".$addQuery." AND stage IN ('contributor','corrector')
                    ORDER BY ap.version DESC LIMIT 1 ";//." where ".$whereQuery;
        // exit;
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return NULL;
    }
/////////getting the details of  verison where user id will be writers///////////////
    public function getVersionDetailsByVersion($partId, $version)
    {
        $query = "SELECT ap.*, u.email FROM ".$this->_name." ap INNER JOIN User u ON ap.user_id = u.identifier WHERE
		         ap.participate_id=".$partId." AND ap.version=".$version."";//." where ".$whereQuery;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
}

