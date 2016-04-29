<?php
/**
 * Comments Model(article comments and poll comments)
 * This Model  is responsible for comment actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class Ep_Delivery_Adcomments extends Ep_Db_Identifier
{
	protected $_name = 'AdComments';
	private $identifier;
	private $user_id;
    private $type;
    private $type_identifier;
    private $comments;
	private $created_at;
	private $updated_at;
	private $active;

	public function loadData($array)
	{
		$this->user_id=$array["user_id"] ;
        $this->type=$array["type"] ;
        $this->type_identifier=$array["type_identifier"] ;
		$this->comments=$array["comments"];
       	$this->created_at=$array["created_at"] ;
		$this->updated_at=$array["updated_at"] ;
		$this->active=$array["active"] ;
        
    	return $this;
	}
	public function loadintoArray()
	{
		$array = array();
		$array["identifier"] = $this->getIdentifier();
        $array["user_id"] =  $this->user_id;
        $array["type"] = $this->type;
		$array["type_identifier"] = $this->type_identifier;
		$array["comments"] = $this->comments;
    	$array["updated_at"] = $this->updated_at;
    	$array["active"] = $this->active;

        return $array;
	}
	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }   
	public function getAdComments($identifier,$type)
	{
		if($type=='article' OR $type=='correction')
			$typecondition=" c.type in ('article','correction')";
		else
			$typecondition=" c.type='".$type."'";

		$commentsQuery="select c.*,u.type as user_type from ".$this->_name." c 
						INNER JOIN User u ON c.user_id=u.identifier
						where c.active='yes' and $typecondition and c.type_identifier='".$identifier."' order by created_at ASC";	
		
		//echo $commentsQuery;exit;
		if(($count=$this->getNbRows($commentsQuery))>0)
		{
			$commentsDetails=$this->getQuery($commentsQuery,true);
			return $commentsDetails;
		}
		else
		{
			return array();
		}
	}
	public function checkCommentUser($comment_id,$user_id)
	{
		$commentsQuery="select user_id from ".$this->_name." 
						where identifier='".$comment_id."' and user_id='".$user_id."'";	
		
		//echo $commentsQuery;exit;
		if(($count=$this->getNbRows($commentsQuery))>0)
		{
			return "YES";
		}
		else
		{
			return "NO";
		}
	}
	public function updateCommentDetails($data,$comment_id)
    {
        $where=" identifier='".$comment_id."'";
        //print_r($data);echo $where;exit;
        $this->updateQuery($data,$where);
    }

	public function getcommentedlist($identifier)
	{
		$commentedQuery="SELECT 
							c.user_id FROM ".$this->_name." c 
							INNER JOIN User u ON c.user_id=u.identifier
						WHERE 
							c.active='yes' and c.type IN ('article','correction') and c.type_identifier='".$identifier."' AND u.type='contributor'";	
		$commentedDetails=$this->getQuery($commentedQuery,true);
		return $commentedDetails;
	}
	
	public function checkNewCommentsCount($tidentifier,$type,$user)
	{
		if($type=='article' OR $type=='correction')
			$typecondition=" c.type in ('article','correction')";
		else
			$typecondition=" c.type='".$type."'";

		$commentsQuery="select count(*) as commentcount from ".$this->_name." c 
						where c.active='yes' and $typecondition and c.type_identifier='".$tidentifier."' and c.user_id!='".$user."' order by created_at ASC";	
		
		//echo $commentsQuery;exit;
		if(($count=$this->getNbRows($commentsQuery))>0)
		{
			$commentsDetails=$this->getQuery($commentsQuery,true);
			return $commentsDetails[0]['commentcount'];
		}
		else
		{
			return 0;
		}
	
	}
}