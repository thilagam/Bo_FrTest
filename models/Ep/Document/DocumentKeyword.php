<?

/**
 * Ep_Admin_DocumentKeyword
 *  
 * @author Ratnadeep
 * @package Admin
 * @version 1.0
 */

class Ep_Document_DocumentKeyword extends Ep_Db_Identifier  
{
	/**
	 * The default table name 
	 */
	protected $_name = 'DOCUMENTKEYWORDS';
	
	//Private properties
	//var $identifier;
	var $documentId;
	var $keywords;
	var $documentIdDest;
	var $active;
	
	public function __construct($tablename=NULL)
	{
		if($tablename)
			$this->_name=$tablename;
		parent::__construct();
	}
	public function setDocumentId($docId)
	{
		$this->documentId = $docId;
	}
	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
	}
	public function setActive($active)
	{
		$this->active = $active;
	}
	public function setDocumentIdDest($docId)
	{
		$this->documentIdDest = $docId;
	}
	public function getDocumentId()
	{
		return $this->documentId;
	}
	public function getKeywords()
	{
		return $this->keywords;
	}
	public function getDocumentIdDest()
	{
		return $this->documentIdDest;
	}
	public function getActive()
	{
		return $this->active;
	}
	/**
	 * createIdentifier
	 * this method is used to create an identifier value for the SENDDOC
	 */
	public function createIdentifier()
    {
		$s = new String();
		$d = new date();
		$this->setIdentifier($s->randomString(15));
  	}
  	
	public function loadData($data)
	{
		$this->setIdentifier($data["identifier"]);
		$this->setDocumentId($data["documentId"]);
		$this->setKeywords($data["keywords"]);
		$this->setDocumentIdDest($data["documentIdDest"]);
		$this->setActive($data["active"]);
	}

	/**
	 * loadIntoArray
	 * Loads the array $array with respective records fetched from the database
	 *
	 * @return array $array
	 */
	public function loadIntoArray()
	{
		$data = array();
		$data["identifier"] = $this->getIdentifier();
		$data["documentId"] = $this->getDocumentId();
		$data["keywords"] = $this->getKeywords();
		$data["documentIdDest"] = $this->getDocumentIdDest();
		$data["active"] = $this->getActive();
		return $data;
	}
	/**
	 * selectAll
	 * select All records from the table depending on the criteria
	 *
	 * @return array $data
	 */
	public function getRecordsPagination($start=0,$limit=20, $conditions=null,$sort=null)
	{
		//$query="SELECT KY.identifier, KY.documentId, DC.title FROM ".$this->_name." AS KY INNER JOIN DOCUMENT AS DC ON(DC.identifier=KY.documentId)";
		$query="SELECT DISTINCT KY.documentId, DC.title FROM ".$this->_name." AS KY INNER JOIN DOCUMENT AS DC ON(DC.identifier=KY.documentId)";
		$countQuery="SELECT count(DISTINCT KY.documentId) as number FROM ".$this->_name." AS KY INNER JOIN DOCUMENT AS DC ON(DC.identifier=KY.documentId)";
		if(!empty($conditions))
		{
			if(!empty($conditions['title']))
				$condQuery[]= "DC.title LIKE '%".$conditions['title']."%'";
			if(!empty($conditions['docId']))
				$condQuery[]= "KY.documentId =".$conditions['docId'];
			if(isset($conditions['active']))
				$condQuery[]= "KY.active =".$conditions['active'];
			$subQuery=" WHERE ".implode(" AND ",$condQuery);
			$query.=$subQuery;
			$countQuery.=$subQuery;
		}
		//$query.=' GROUP BY KY.documentId';		
		if(!empty($sort))
		{
			if($sort['field']=='title')
				$sort['field']='DC.title';
			elseif($sort['field']=='keywords')
				$sort['field']="KY.".$sort['field'];
			$query .= " ORDER BY ".$sort['field']." ".$sort['order'];
		}
		$query .= " LIMIT ".$start.",".$limit;	
		$records=$this->getQuery($query,false);
		if(!empty($records))
		{
			for($i=0;$i<count($records);$i++)
				$records[$i]->title=stripslashes($records[$i]->title);
			$data['records']=$records;
		}
		$count=$this->getQuery($countQuery,false);
		if(!empty($count))
			$data['count']=$count[0]->number;
		if(!empty($data))
			return $data;
	}
	public function updateKeyword($upddata, $where)
	{
		return $this->updateQuery($upddata,$where);
	}
	public function deleteKeywordByDoc($docId=null)
	{
		return $this->deleteQuery('documentId = '.$docId);
	}
	public function deleteKeywordByKey($keyword=null)
	{
		return $this->deleteQuery("keywords = '".$keyword."'");
	}
	public function getDocumnetLinks($docId=null)
	{
		$query="select KY.keywords, KY.documentIdDest, DCS.title as sourceTitle, DCS.description, DCD.title as destTitle, DCD.url from ".$this->_name." AS KY INNER JOIN DOCUMENT AS DCD ON(DCD.identifier=KY.documentIdDest) INNER JOIN DOCUMENT AS DCS ON(DCS.identifier=KY.documentId) WHERE KY.documentId = ".$docId;
		$records=$this->getQuery($query,true);
		if(!empty($records))
		{
			//$result['docId']=$docId;
			$result['description']=$records[0]['description'];
			$result['title']=$records[0]['sourceTitle'];
			$keywords = array();
			foreach ($records as $rec)
				if(!$this->in_arrayi($rec['keywords'],$keywords))
				{
					$keywords[]=$rec['keywords'];
				}
			for($i=0;$i<count($keywords);$i++)
			{
				$values[$i]['keyword']=$keywords[$i];
				$urls=array();
				foreach ($records as $rec)
					if($keywords[$i]==$rec['keywords'])
					{
						$values[$i]['docs'][]=array('id'=>$rec['documentIdDest'],'title'=>$rec['destTitle']);
						//$url='<a class="link_oth" href="/'.$rec['url'].'">'.$keywords[$i].'</a>';
						$url=$rec['url'];
						$values[$i]['urls'][$rec['documentIdDest']]=$url;
					}
			}
			$result['values']=$values;
			return $result;
		}
	}
	private function in_arrayi($needle, $haystack)
	{
		foreach ($haystack as $value)
		{
			if (strtolower($value) == strtolower($needle))
				return true;
		}
		return false;
	} 
}