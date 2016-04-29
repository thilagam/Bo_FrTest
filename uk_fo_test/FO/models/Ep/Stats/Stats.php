<?php

class Ep_Stats_Stats extends Ep_Db_Identifier
{
	public function getAllStatistics($options=NULL)
	{
		$stats['totalUploadedArticles']=$this->getNumberOfArticlesUploaded($options);
		$stats['totalCreatedArtilces']=$this->getNumberOfArticlesCreated($options);
		$stats['totalValidatedArticles']=$this->getNumberOfArticlesValidated($options);
		$stats['newWrites']=$this->getNumberOfNewWriters($options);
		$stats['participants']=$this->getNumberOfParticipants($options); 
		$stats['donation']=$this->getAmountDonated($options); 
		return $stats;
	}
	public function getNumberOfArticlesUploaded($options=NULL)
	{
		
		if($options['stats_display']=='month')
			$condition=" and date_format(p.updated_at, '%Y-%m')=date_format(now(), '%Y-%m')";
		else if($options['stats_display']=='day' && $options['stats_days_value'] )		
			$condition=" and p.updated_at >= ( CURDATE() - INTERVAL ".($options['stats_days_value']-1)." DAY )";
		$query="Select count(*) as totalUploadedArticles
				From Article a
				INNER JOIN Participation p ON a.id=p.article_id
				WHERE p.status in ('under_study','on_hold','disapproved','disapproved_temp','closed_temp','plag_exec')
				$condition
				";
		
		if($articleDetails=$this->getQuery($query,true))
        {
           return $articleDetails[0]['totalUploadedArticles'];
        }
        else
             return 0;
	}
	public function getNumberOfArticlesCreated($options=NULL)
	{
		if($options['stats_display']=='month')
			$where=" WHERE date_format(created_at, '%Y-%m')=date_format(now(), '%Y-%m')";
		else if($options['stats_display']=='day' && $options['stats_days_value'] )	
			$where=" WHERE created_at >= ( CURDATE() - INTERVAL ".($options['stats_days_value']-1)." DAY )";
		$query="select count(*) as totalCreatedArtilces 
				From Article
				".$where					
				;			
		if($articleDetails=$this->getQuery($query,true))
        {
           return $articleDetails[0]['totalCreatedArtilces'];
        }
        else
             return 0;
	}
	public function getNumberOfArticlesValidated($options=NULL)
	{
		if($options['stats_display']=='month')
			$condition=" and date_format(r.created_at, '%Y-%m')=date_format(now(), '%Y-%m')";	
		else if($options['stats_display']=='day' && $options['stats_days_value'] )			
			$condition=" and r.created_at >= ( CURDATE() - INTERVAL ".($options['stats_days_value']-1)." DAY )";
		$query="Select count(*) as totalValidatedArticles
				From Article a
				INNER JOIN Participation p ON a.id=p.article_id
				INNER JOIN Royalties r ON p.id=r.participate_id
				WHERE p.status='published' $condition
				";		
		
		if($articleDetails=$this->getQuery($query,true))
        {
           return $articleDetails[0]['totalValidatedArticles'];
        }
        else
             return 0;
	}
	public function getNumberOfNewWriters($options=NULL)
	{
		if($options['stats_display']=='month')
			$condition=" and date_format(created_at, '%Y-%m')=date_format(now(), '%Y-%m')";
		else if($options['stats_display']=='day' && $options['stats_days_value'] )			
			$condition=" and created_at >= ( CURDATE() - INTERVAL ".($options['stats_days_value']-1)." DAY )";
		$query="Select count(*) as newWrites
				From User 
				WHERE type='contributor' and status='Active' and blackstatus='no'
				$condition
				";
		//echo $query;exit;
		
		if($userDetails=$this->getQuery($query,true))
        {
           return $userDetails[0]['newWrites'];
        }
        else
             return 0;
	}
	public function getNumberOfParticipants($options=NULL)
	{
		if($options['stats_display']=='month')
			$condition="  date_format(created_at, '%Y-%m')=date_format(now(), '%Y-%m')";
		else if($options['stats_display']=='day' && $options['stats_days_value'] )			
			$condition="  created_at >= ( CURDATE() - INTERVAL ".($options['stats_days_value']-1)." DAY )";
		$query="Select count(*) as participants
				From Participation 
				WHERE 
				$condition
				";
		//echo $query;exit;
		
		if($userDetails=$this->getQuery($query,true))
        {
           return $userDetails[0]['participants'];
        }
        else
             return 0;
	}
	
	public function getAmountDonated($options=NULL)
	{
		if($options['stats_display']=='month')
			$condition="  and date_format(n.updated_at, '%Y-%m')=date_format(now(), '%Y-%m')";
		else if($options['stats_display']=='day' && $options['stats_days_value'] )			
			$condition="  and n.updated_at >= ( CURDATE() - INTERVAL ".($options['stats_days_value']-1)." DAY )";
		$query="Select sum(r.price) as donation
				From Royalties r LEFT JOIN Invoice n ON r.invoiceId=n.invoiceId 
				WHERE n.status='Paid' 
				$condition
				";
		//echo $query;exit;
		
		if($userDetails=$this->getQuery($query,true))
        {
           if($userDetails[0]['donation']==null)
			$userDetails[0]['donation']=0;
		   return $userDetails[0]['donation'];
        }
        else
             return 0;
	}
}