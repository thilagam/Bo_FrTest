<?php
/**
 * Registration Model
 * This Model  is responsible for Registration actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Delivery_ArticleReassignReasons extends Ep_Db_Identifier
{
	protected $_name = 'ArticleReassignReasons';
	private $identifier;
	private $participate_id;
	private $refused_by;
	private $contributor;
    private $stage;
    private $reasons;
    private $edited_content;
    private $type;
    private $created_at;


	public function loadData($array)
	{
	    $this->identifier=$array["identifier"];
        $this->participate_id=$array["participate_id"];
        $this->refused_by=$array["refused_by"];
        $this->contributor=$array["contributor"];
        $this->stage=$array["stage"];
        $this->reasons=$array["reasons"];
        $this->edited_content=$array["edited_content"];
        $this->type=$array["type"];
        $this->created_at=$array["created_at"];

		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["identifier"] = $this->getIdentifier();
        $array["participate_id"] = $this->participate_id;
        $array["refused_by"] = $this->refused_by;
        $array["contributor"] = $this->contributor;
        $array["stage"] = $this->stage;
        $array["reasons"] = $this->reasons;
        $array["edited_content"] = $this->edited_content;
        $array["type"] = $this->type;
        $array["created_at"] = $this->created_at;
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
		$query = "SELECT participate_id FROM ".$this->_name." WHERE participate_id=".$partId;//." where ".$whereQuery;
	    if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////fecthing the commets by client done on particlipation when refused///////////////
	public function getComments($partId)
	{
		 $query = "SELECT reasons FROM ".$this->_name." WHERE participate_id=".$partId;//." where ".$whereQuery;
	    if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}


    /////////fecthing all the comments///////////////
	public function getReasonDetails()
	{
	    $query = "SELECT r.participate_id AS partId, r.edited_content, r.type, r.created_at, r.stage, a.id AS articleId,
	               up.first_name, up.last_name, u.email, a.title, d.title AS delTitle FROM ".$this->_name." r
		          INNER JOIN Participation p ON p.id=r.participate_id
		          LEFT JOIN Article a ON a.id=p.article_id
		          LEFT JOIN Delivery d ON d.id=a.delivery_id
		          LEFT JOIN UserPlus up ON up.user_id=r.refused_by
		          LEFT JOIN User u ON u.identifier=r.refused_by WHERE 1=1 GROUP BY a.id,r.contributor " ;//." where ".$whereQuery;
         $query1 = "SELECT  up.first_name AS fName, up.last_name AS lName, u.email AS cEmail FROM ".$this->_name." r
		         INNER JOIN Participation p ON p.id=r.participate_id
		          LEFT JOIN Article a ON a.id=p.article_id
		          LEFT JOIN Delivery d ON d.id=a.delivery_id
		         INNER JOIN UserPlus up ON up.user_id=r.contributor
		          INNER JOIN User u ON u.identifier=r.contributor WHERE 1=1 GROUP BY a.id,r.contributor " ;//." where ".$whereQuery;
	    $result = $this->getQuery($query,true);
        $result1 = $this->getQuery($query1,true);
           //$result = $result1+$result2;
        for($i=0; $i<count($result1); $i++)
        {
            $result[$i]['Cfirstname'] = $result1[$i]['fName'];
            $result[$i]['Clastname'] = $result1[$i]['lName'];
            $result[$i]['Cemail'] = $result1[$i]['cEmail'];
        }
        if($result != NULL)
			return $result;
		else
			return "NO";
	}
      /////////fecthing the commets by client or corrector done on particlipation when refused///////////////
	public function getRefusalReasonsDetails($artId, $partId)
	{
		  $query = "SELECT r.participate_id, r.edited_content, r.type, r.created_at, r.stage, a.id AS articleId, up.first_name, up.last_name, u.email, a.title FROM ".$this->_name." r
		          INNER JOIN Participation p ON p.id=r.participate_id
		          LEFT JOIN Article a ON a.id=p.article_id
		          LEFT JOIN UserPlus up ON up.user_id=r.refused_by
		          LEFT JOIN User u ON u.identifier=r.refused_by WHERE r.participate_id=".$partId." ORDER BY r.created_at DESC" ;//." where ".$whereQuery;

	    if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
      /////////fecthing the commets by corrector done on particlipation at correction///////////////
	public function getCorrectorComments($partId)
	{
	    $query = "SELECT r.participate_id, r.edited_content, r.type, r.created_at, r.stage, a.id AS articleId, up.first_name, up.last_name, u.email, a.title FROM ".$this->_name." r
		          INNER JOIN Participation p ON p.id=r.participate_id
		          LEFT JOIN Article a ON a.id=p.article_id
		          LEFT JOIN UserPlus up ON up.user_id=r.refused_by
		          LEFT JOIN User u ON u.identifier=r.refused_by WHERE r.participate_id=".$partId." AND r.type='comment' ORDER BY r.created_at DESC" ;//." where ".$whereQuery;

	    if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////fecthing the commets by client or corrector done on particlipation when refused///////////////
    public function getLatestReason($partId)
    {
        $query = "SELECT identifier, reasons FROM ".$this->_name."
		          WHERE participate_id=".$partId." ORDER BY created_at DESC LIMIT 0,1" ;// exit;//." where ".$whereQuery;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }


//get reson data based on reason Id
    public function getRefusalReason($reason_id)
    {
        $query = "SELECT * FROM ".$this->_name."
		          WHERE identifier='".$reason_id."'";// exit;//." where ".$whereQuery;
		//echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get all refused reassigned reasons of bo users///////////////////////////
    public function  getBoUserRefusedReasons($userId, $reasontype)
    {
        $condition = '';
        if($reasontype == 'temp')
            $condition.= " AND type = 'temporaire'";
        else
            $condition.= "AND type = 'permanent'";
         $query = "select reasons FROM ".$this->_name."  WHERE refused_by = ".$userId." AND stage!='corrector'  ".$condition." ";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

    ////////////udate the articl reassign reasons table//////////////////////
    public function updateArticleReassignReasons($data,$query)
    {
      // echo $query;
        //print_r($data1);exit;
       $this->updateQuery($data,$query);

    }



}
