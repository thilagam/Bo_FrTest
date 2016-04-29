<?php

class Ep_Article_ArticleHistory extends Ep_Db_Identifier
{
	protected $_name = 'ArticleHistory';
		
	//Insert Article History
    public function insertHistory($inarray){
		$inarray['id']=$this->getIdentifier();
        $this->insertQuery($inarray); 
    }
    
    public function updateArticleHistory($data,$query)
    {
          $this->updateQuery($data,$query);
    }
	
	public function getAOHistory($params)
    {
        if($params['article_id'] && $params['id'])
            $condition=" article_id='".$params['article_id']."' OR article_id='".$params['id']."' ";
        else if($params['article_id'])
            $condition=" article_id='".$params['article_id']."'";

        else if($params['id'])
            $condition=" article_id='".$params['id']."' OR article_id in (select id from Article Where delivery_id='".$params['id']."') ";


        $historyQuery="SELECT action_at,action_sentence,action 
                    FROM $this->_name ah                    
                WHERE  $condition
                ORDER BY  action_at DESC";  

        if(($count=$this->getNbRows($historyQuery))>0)
        {
            $aoHistory=$this->getQuery($historyQuery,true);
            return $aoHistory;

        }
        else
            return NULL;        
    }
}

