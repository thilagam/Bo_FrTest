<?php

class EP_Delivery_ArticleProcess extends Ep_Db_Identifier
{
	protected $_name = 'ArticleProcess';
	private $id;
	private $participate_id;
	private $user_id;
	private $stage;
    private $status;
    private $marks;
    private $reasons_marks;
    private $comments;
	private $article_path;
    private $article_name;
    private $version;
    private $article_sent_at;
    private $article_doc_content;
    private $article_words_count;
    private $client_comments;
    private $plag_percent;
    private $plagxml;
    private $moderate_epdecision;
    private $art_file_size_limit_email;

	public function loadData($array)
	{
		$this->id=$array["id"];
        $this->participate_id=$array["participate_id"];
        $this->user_id=$array["user_id"];
        $this->stage=$array["stage"];
        $this->status=$array["status"];
        $this->marks=$array["marks"];
        $this->reasons_marks=$array["reasons_marks"];
        $this->comments=$array["comments"];
        $this->article_path=$array["article_path"];
        $this->article_name=$array["article_name"];
        $this->version=$array["version"];
        $this->article_sent_at=$array["article_sent_at"];
        $this->article_doc_content=$array["article_doc_content"];
        $this->article_words_count=$array["article_words_count"];
        $this->client_comments=$array["client_comments"];
        $this->plag_percent=$array["plag_percent"];
        $this->plagxml=$array["plagxml"];
        $this->moderate_epdecision=$array["moderate_epdecision"];
        $this->art_file_size_limit_email=$array["art_file_size_limit_email"];
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
        $array["marks"] = $this->marks;
        $array["reasons_marks"] = $this->reasons_marks;
        $array["comments"] = $this->comments;
        $array["article_path"] = $this->article_path;
        $array["article_name"] = $this->article_name;
        $array["version"] = $this->version;
        $array["article_sent_at"] = $this->article_sent_at;
        $array["article_doc_content"] = $this->article_doc_content;
        $array["article_words_count"] = $this->article_words_count;
        $array["client_comments"] = $this->client_comments;
        $array["plag_percent"] = $this->plag_percent;
        $array["plagxml"] = $this->plagxml;
        $array["moderate_epdecision"] = $this->moderate_epdecision;
        $array["art_file_size_limit_email"] = $this->art_file_size_limit_email;
		return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
   ////////getting the details of recent verison in process///////////////
	public function getRecentVersion($partId)
	{
	    $query = "SELECT id, participate_id, user_id, stage, status, article_path, article_name, version, article_sent_at, marks, comments, reasons_marks FROM ".$this->_name." WHERE
		         participate_id=".$partId." AND version=(select max(version) FROM ".$this->_name." WHERE participate_id=".$partId.")";//." where ".$whereQuery;
        //echo $query; exit;
	    if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    ////////getting the details of recent verison in process///////////////
    public function getRecentVersionWithMarks($partId)
    {
        $query = "SELECT id, participate_id, user_id, stage, status, article_path, article_name, version, article_sent_at, marks, comments, reasons_marks FROM ".$this->_name." WHERE
		         participate_id=".$partId." AND version=(select max(version) FROM ".$this->_name." WHERE participate_id=".$partId." AND marks IS NOT NULL AND stage != 'corrector')";//." where ".$whereQuery;
        //echo $query;// exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
     ////////getting the article process id of recent verison in article process///////////////
	public function getRecentVersionId($partId)
	{
	   $query = "SELECT ap.id, ap.comments, u.email, u.login, up.first_name, up.last_name FROM ".$this->_name." ap
	             INNER JOIN User u ON u.identifier=ap.user_id
                 INNER JOIN UserPlus up ON up.user_id=ap.user_id
		         WHERE participate_id=".$partId." AND version=(select max(version) FROM ".$this->_name."
		         WHERE participate_id=".$partId.")";//." where ".$whereQuery;

	    if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    ////////getting the contributor's details from recent ///////////////
    public function getRecentContributorVersionId($partId)
    {
        $query = "SELECT ap.id, ap.comments, u.email, u.login, up.first_name, up.last_name FROM ".$this->_name." ap
	             INNER JOIN User u ON u.identifier=ap.user_id
                 INNER JOIN UserPlus up ON up.user_id=ap.user_id
		         WHERE participate_id=".$partId." AND stage='contributor' AND version=(select max(version) FROM ".$this->_name."
		         WHERE participate_id=".$partId." AND stage='contributor')";//." where ".$whereQuery;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ////////getting the details of recent verison in article process based on the article id///////////////
    public function getRecentVersionOnArtid($artId)
    {
        $query = "SELECT ap.id FROM ".$this->_name." ap
                    INNER JOIN Participation p ON p.id = ap.participate_id
                    INNER JOIN Article a ON a.id = p.article_id
                    WHERE a.id=".$artId." AND p.status NOT IN ('bid_refused','closed') AND ap.version=(select max(ap.version) FROM ".$this->_name." ap INNER JOIN Participation p ON p.id = ap.participate_id
                    INNER JOIN Article a ON a.id = p.article_id WHERE a.id=".$artId." AND p.status NOT IN ('bid_refused','closed'))";//." where ".$whereQuery;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
/////////getting the details of First verison where user id will be writers///////////////
	public function getFristVersion($partId)
	{
		$query = "SELECT ap.participate_id, ap.user_id, ap.stage, ap.status, ap.article_path, ap.article_name, ap.version, ap.article_sent_at, u.email FROM ".$this->_name." ap INNER JOIN User u ON ap.user_id = u.identifier WHERE
		         ap.participate_id=".$partId." AND ap.version=1";//." where ".$whereQuery;

	    if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
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
    /////////getting date of First verison where contriibutor sent the article///////////////
    public function getFristVersionDate($partId)
    {
         $query = "SELECT ap.article_sent_at as article_date, ap.plag_percent, ap.marks, ap.comments, ap.reasons_marks FROM ".$this->_name." ap
                  INNER JOIN User u ON ap.user_id = u.identifier WHERE
		          ap.participate_id=".$partId." AND ap.version=(select max(version) FROM ".$this->_name." WHERE participate_id=".$partId.")";//." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    ////////////udate the articleProcess table//////////////////////
    public function updateArticleProcess($data,$query)
    {
         //echo $query;
        $data['article_doc_content'] = utf8_decode($data['article_doc_content']);
        //echo "<pre>";print_r($data);exit;

        $this->updateQuery($data,$query);
    }

	public function insertArticleProcess($data)
	{
	 $data['id']=$this->getIdentifier();
	 $this->insertQuery($data);
	}

	public function getParticipateid($user,$art)
	{
		$query = "SELECT id FROM Participation WHERE user_id='".$user."' AND article_id='".$art."'";
		$result = $this->getQuery($query,true);
		return $result[0]['id'];
	}

    /////////getting the details of all of particular participation verisons displayed in box ///////////////
	public function getVersionDetails($partId)
	{
	    $query = "SELECT ap.id, ap.participate_id, ap.user_id, ap.stage, ap.status, ap.article_path, ap.article_name, ap.whitelist_newkeywords, ap.client_comments, ap.reasons_marks,
	                ap.version, ap.article_sent_at, ap.article_doc_content, ap.article_words_count, ap.comments, ap.plag_percent, ap.marks, u.login, u.email,
	                u.type, up.user_id, up.first_name, u.blackstatus FROM ".$this->_name." ap
	                LEFT JOIN User u ON u.identifier=ap.user_id
	                LEFT JOIN UserPlus up ON up.user_id=ap.user_id WHERE participate_id=".$partId;//." where ".$whereQuery;
	    //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////getting the details of all of particular participation verisons displayed in box ///////////////
    public function getVersionModerationDetails($partId)
    {
        $query = "SELECT ap.*,p.status as participationstatus, p.current_stage, u.login, u.email,
	                u.type, up.user_id, up.first_name, u.blackstatus FROM ".$this->_name." ap
	                INNER JOIN Participation p ON p.id = ap.participate_id
	                LEFT JOIN User u ON u.identifier=ap.user_id
	                LEFT JOIN UserPlus up ON up.user_id=ap.user_id WHERE participate_id=".$partId;//." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////////getting the article path for down load the article in box ///////////////
	public function getArticlePath($artprocessId)
	{
       // $query = "delete FROM ".$this->_name." where WHERE participate_id=".$partId;
	     $query = "SELECT article_path, version, article_name, article_doc_content, whitelist_newkeywords, article_words_count, participate_id  FROM ".$this->_name."  WHERE id=".$artprocessId;//." where ".$whereQuery;
	  // echo $query;exit;
	     if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}

    /******************************* Plagiarisms **************************/
    public function articledetail($apid)
    {
        $query ="SELECT
                        a.title,ap.article_name,ap.article_doc_content,up.first_name,up.last_name,p.status
                FROM
                        Article a INNER JOIN Participation p ON a.id=p.article_id
                        INNER JOIN ArticleProcess ap ON p.id=ap.participate_id
                        LEFT JOIN UserPlus up ON ap.user_id=up.user_id
                WHERE
                        ap.id='".$apid."'";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /******************************* Plagiarisms **************************/
    public function getArticleFileSizeLimitEmailStatus($artPath)
    {
        $query ="SELECT art_file_size_limit_email FROM ArticleProcess WHERE article_path='".$artPath."'";
        $result = $this->getQuery($query,true);
        return $result[0]['art_file_size_limit_email'];
    }
    // plagiarism list to display in the stage correction ///
	public function plagiarismlist($apid)
	{
		$query ="SELECT
						a.title,ap.article_name,ap.article_doc_content,up.first_name,up.last_name,p.status
				FROM
						Article a INNER JOIN Participation p ON a.id=p.article_id
						INNER JOIN ArticleProcess ap ON p.id=ap.participate_id
						LEFT JOIN UserPlus up ON ap.user_id=up.user_id
				WHERE
						ap.id not in ('".$apid."')";

		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
	}
	
	 ////////getting the article process id of recent verison in article process///////////////
    public function partidInArticleProcess($partId)
    {
        $query = "SELECT id, participate_id, user_id, stage, status, version FROM ".$this->_name." WHERE participate_id=".$partId;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ////////getting the article process id of recent verison in article process///////////////
    public function getRecentVersionByTime($partId)
    {
        $query = "SELECT id, participate_id, article_sent_at, marks FROM ".$this->_name." WHERE
		         participate_id=".$partId." AND article_sent_at=(select max(article_sent_at) FROM ".$this->_name." WHERE participate_id=".$partId.")";//." where ".$whereQuery;
        //echo $query; exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";

    }
	// article details by article process id //
	public function getArticlebyApid($id)
	{
		$query = "SELECT id, marks, article_path,article_name,article_sent_at FROM ".$this->_name." WHERE id='".$id."'";//echo $query;
		$result = $this->getQuery($query,true);
        return $result;
    }
    /////////getting marks given by the user ///////////////
    public function getMarksByUser($partId, $userId)
    {
        $query = "SELECT marks, comments FROM ".$this->_name." WHERE participate_id=".$partId." AND user_id = ".$userId." AND stage = 'corrector'
                   AND version=(select max(version) FROM ".$this->_name." WHERE participate_id=".$partId.")";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////getting the latest version for the corrector ///////////////
    public function getLatestWriterArticle($artId)
    {
        $query = "SELECT id, stage, status, article_path, article_name, version, article_sent_at FROM ArticleProcess
                            WHERE participate_id = (select p.id FROM Participation p
                            INNER JOIN Article a ON a.id = p.article_id WHERE a.id='" . $artId . "'
                            AND p.status NOT IN ('bid_premium','bid_nonpremium','bid_refused','closed','bid_temp', 'bid_refused_temp') AND p.cycle=0) AND stage = 'contributor'
                            ORDER BY version DESC LIMIT 1";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////getting the latest version for the corrector ///////////////
    public function getLatestCorrectionArticle($artId)
    {
        $query = "SELECT id, stage, status, article_path, article_name, version, article_sent_at FROM ArticleProcess
                            WHERE participate_id = (select p.id FROM Participation p
                            INNER JOIN Article a ON a.id = p.article_id WHERE a.id='" . $artId . "'
                            AND p.status NOT IN ('bid_premium','bid_nonpremium','bid_refused','closed', 'bid_temp', 'bid_refused_temp') AND p.cycle=0) AND stage = 'corrector' AND status IS NULL AND version != 0
                            ORDER BY version DESC LIMIT 1";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    ///article stuck up versions////
    public function getPlagStuckVersions()
    {
        $query = "SELECT ap.*, u.email,
	                u.type,d.id as aoid, a.title, up.user_id, up.first_name, u.blackstatus FROM ".$this->_name." ap
	                INNER JOIN Participation p ON p.id = ap.participate_id
	                INNER JOIN Article a ON a.id = p.article_id
	                INNER JOIN Delivery d ON d.id = a.delivery_id
	                LEFT JOIN User u ON u.identifier=ap.user_id
	                LEFT JOIN UserPlus up ON up.user_id=ap.user_id WHERE ap.plag_stuck = 'yes' "; //." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////getting the file upload date by contributor ///////////////
    public function getFileUploadDate($partId)
    {
        $query = "SELECT created_at FROM ".$this->_name." ap
	               WHERE status = 'contributor' AND participate_id=".$partId;//." where ".$whereQuery;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
}
