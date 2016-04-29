<?php

/**
 * Ep_Message_Template
 * @author Chandu
 * @package Message
 * @version 1.0
 */
class Ep_Message_Template extends Ep_Db_Identifier
{
	protected $_name = 'Template';
    private $identifier;
	private $templatetype;
	private $type;
	private $maintype;
	private $title;
	private $content;
	private $active;
	private $subject;
    private $parameters;

	public function loadData($array)
	{
		$this->templatetype=$array["templatetype"];
		$this->type=$array["type"];
		$this->maintype=$array["maintype"];
		$this->title=$array["title"] ;
		$this->content=$array["content"] ;
		$this->active=$array["active"] ;
		$this->subject=$array["subject"] ;
        $this->parameters=$array["parameters"] ;

		return $this;
	}
	protected function loadIntoArray()
	{
		$array = array();
        $array["identifier"] = $this->getIdentifier();
	    $array["templatetype"] = $this->templatetype;
	    $array["type"] = $this->type;
		$array["maintype"] = $this->maintype;
		$array["title"] = $this->title;
		$array["content"] = $this->content;
		$array["active"] = $this->active;
		$array["subject"] = $this->subject;
        $array["parameters"] = $this->parameters;

		return $array;
	}
    public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    /////////get all validation templates///////////////////////////
	public function validationTemplates($templatetype)
	{
        $query = "select *  FROM ".$this->_name." WHERE  maintype='validation' AND templatetype='".$templatetype."'";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////get all email templates///////////////////////////
	public function emailTemplates()
	{
        $query = "select *  FROM ".$this->_name." WHERE  maintype='email'";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
  /////////get each validation template details///////////////////////////
	public function getValTempDetails($id)
	{
        $query = "select *  FROM ".$this->_name." WHERE identifier=".$id;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////get title from template //////////////////////////
    public function getValTempTitle($id)
    {
        $query = "select title  FROM ".$this->_name." WHERE identifier=".$id;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
    /////////get each email template details///////////////////////////
	public function getEmailTempDetails($id)
	{
        $query = "select *  FROM ".$this->_name." WHERE identifier=".$id;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
 /////////get each email template details///////////////////////////
	public function refuseValidTemplates($id, $temptype)
	{
        if($id == 'null')
        {
            $query = "select identifier, title, content  FROM ".$this->_name." WHERE type='refuse' AND active='yes' AND templatetype = '".$temptype."' ";
        }
        else
        {
             $query = "select identifier, title, content  FROM ".$this->_name." WHERE type='refuse' AND identifier='".$id."' AND active='yes' AND templatetype = '".$temptype."'";
        }
     // echo "<br>".$query;
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}
    /////////get each refuse template on id///////////////////////////
    public function refuseValidTemplatesOnId($id)
    {
        if($id == 'null')
        {
            $query = "select identifier, title, content  FROM ".$this->_name." WHERE type='refuse' AND active='yes'";
        }
        else
        {
            $query = "select identifier, title, content  FROM ".$this->_name." WHERE type='refuse' AND identifier='".$id."' AND active='yes' ";
        }
        // echo "<br>".$query;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	 ////////////udate the template table//////////////////////
    public function updateTemplate($data,$query)
    {
       $this->updateQuery($data,$query);
    }
	
	public function getActiveValidationtemplates($templatetype)
	{
		$query = "select *  FROM ".$this->_name." WHERE  maintype='validation' AND templatetype='".$templatetype."' AND active='yes'";
		if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
		else
			return "NO";
	}

}


