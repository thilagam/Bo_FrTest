<?php

class Ep_Ebookers_Stencils extends Ep_Db_Identifier
{
	protected $_name = 'EB_themes';
	
	function getStencils($search = NULL)
	{
		$where = " t.status = 'active'";
		$groupby = $select = $join = "";
		if($search['theme'])
		$groupby = " GROUP BY t.theme_id";
		if($search['theme_id'])
		{
			$select = ",c.*";
			$where .=" AND t.theme_id = $search[theme_id] AND c.status='active'";
			$join = " JOIN EB_category c ON c.themes_id = t.theme_id";
			$groupby = " GROUP BY c.cat_id";
		}
		if($search['cat_id'])
		{
			$select = ",st.*,c.category_name";
			$where .=" AND st.category_id = $search[cat_id] AND st.status='active' AND c.status='active'";
			$join = " JOIN EB_category c ON c.themes_id = t.theme_id
					  JOIN EB_sampletext st ON st.category_id = c.cat_id";
			$groupby = " GROUP BY st.sample_id";
		}
		if($search['sample_id'])
			$where .=" AND st.sample_id = $search[sample_id]";
		$query = "SELECT t.* $select FROM EB_themes t
				  $join
				  WHERE $where
				  $groupby 
				  ";
		if(($result = $this->getQuery($query, true)) != NULL)	
		   return $result;
		else
		return NULL;
	}
	
	function getTokens($array = NULL)
	{
		$where = "1=1";
		if($array['cat_id'])
		{
			$where = " category_id = $array[cat_id]";
		}
		$query = "SELECT *
				  FROM EB_token 				 
				  WHERE $where
				  ";
		if(($result = $this->getQuery($query, true)) != NULL)	
		   return $result;
		else
		return NULL;
	}
	
	function getTexts($array = NULL)
	{
		$where = "1=1";
		if($array['pid'])
			$where = "  ap.participate_id='".$array['pid']."'";
		$order_by = " ORDER BY ap.version DESC LIMIT 1";
		$query = "SELECT ap.* FROM ArticleProcess ap 
				  WHERE $where
				  $order_by
				 ";
	/* 	 echo $query;
		exit;  */
		if(($result = $this->getQuery($query, true)) != NULL)	
		   return $result;
		else
		return NULL;
	}
    function insertStencil($data){
        $this->_name = 'ValidStencils';
        $data['id'] = number_format(microtime(true),0,'','').mt_rand(10000,99999);
        $this->insertQuery($data);
    }
}
?>