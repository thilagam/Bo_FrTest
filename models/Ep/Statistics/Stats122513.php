<?php

class Ep_Statistics_Stats extends Ep_Db_Identifier
{
	//get Client and contributors Active count
	public function getUserCount()
	{
		$query="SELECT SUM(IF(type='client',1,0)) as ClientCount ,
					   SUM(IF(type='contributor',1,0)) as writerCount
			   FROM User
			   where Status='Active'
				";
		
		if($userCount=$this->getQuery($query,true))
        {
           return $userCount[0];
        }
        else
             return 0;
	
	}
	//get Total Deliveries and Articles
	public function getDeliveriresAndArticlesCount()
	{
		$query="Select count(DISTINCT d.id) as TotalDeliveries,
					   count(DISTINCT a.id) as TotalArticles,
					   SUM(IF(d.status_bo IS NULL && d.created_by !='BO' ,1,0 )) as TotalNewAO
				From Article a
				INNER JOIN Delivery d ON a.delivery_id=d.id

				";
		
		if($deliveryCount=$this->getQuery($query,true))
        {
           return $deliveryCount[0];
        }
        else
             return 0;
	
	}
	
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
    
    public function newsletters()
    {
        if($newsletters=$this->getQuery("Select CONCAT(up.first_name,' ',up.last_name) as username,count(ns.id) as total_opened, (select count(*) From DailyNewsletter dn where dn.created_at >= u.created_at) as total_nl, ns.email, ns.user_id from NewsletterStats ns INNER JOIN User u ON u.identifier=ns.user_id INNER JOIN UserPlus up ON u.identifier=up.user_id Group By ns.user_id",true))
        {
            return $newsletters;
        }
        else
             return 0;
    }
    
    public function getNewsletterUserOptions($userid)
    {
        $userOption = '' ;
        if($newsletters=$this->getQuery("SELECT ns.* FROM NewsletterStats ns GROUP BY ns.user_id",true))
        {
            foreach($newsletters as $key=>$newsletter)
            {
                if($dailynewsletter=$this->getQuery("SELECT first_name, last_name FROM UserPlus WHERE user_id='".$newsletter['user_id']."'",true))
                {
                    $userOption .= '<option value="'.$newsletter['user_id'].'" ' . ($newsletter['user_id'] == $userid ? 'selected' : '') . '>' . $newsletter['email'] . '(' . $dailynewsletter[0]['first_name'] . ' ' . $dailynewsletter[0]['last_name'] . ')' . '</option>' ;
                }
            }
            return $userOption;
        }
    }

    public function getUserNewsletter($user_id, $newsletter_id)
    {
        if($dailynewsletters=$this->getQuery("select dn.created_at,(select ns.viewed_at From NewsletterStats ns where ns.newsletter_id=dn.id and ns.user_id=u.identifier) as viewed_at from DailyNewsletter dn,User u where dn.created_at>=u.created_at and u.identifier='".$user_id."' Order BY dn.created_at ASC",true))
        {
            return $dailynewsletters;
        }
        else
             return 0;
    }
    
    public function getUserInfo($userid)
    {
        return $userInfo=$this->getQuery("SELECT up.first_name, up.last_name, u.email FROM UserPlus up INNER JOIN User u on u.identifier=up.user_id WHERE up.user_id='".$userid."'",true) ;
    }
    
    public function getUsersCountStat()
    {
        //$userInfo['d'] = $this->getQuery("SELECT COUNT(*) AS count , DATE_FORMAT(created_at, '%m/%d/%Y') AS date FROM User WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 10 DAY) GROUP BY YEAR( created_at ) , MONTH( created_at ) , DAY( created_at )",true) ;
        //$userInfo['w'] = $this->getQuery("SELECT COUNT(*) AS count , DATE_FORMAT(created_at, '%m/%d/%Y') AS date FROM User WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 10 WEEK) GROUP BY WEEK( created_at )",true) ;
        
        //$userInfo['m'] = $this->getQuery("SELECT COUNT(*) AS count , DATE_FORMAT(created_at, '%m/%d/%Y') AS date FROM User WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 10 MONTH) GROUP BY YEAR( created_at ) , MONTH( created_at )",true) ;
        
        $dDates[] = date('Y-m-d');
        for($i=1; $i<=10; $i++) $dDates[] = date('Y-m-d', strtotime("- " . $i . " Days"));
        $i=0;
        foreach($dDates as $dDate)
        {
            if($dDates[$i+1])
            {
                $dayQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE (DATE(created_at)= '".$dDates[$i]."')) AS '" . ($dDates[$i]) . "'") ;
            }
            $i++;
        }
        //print_r($dayQueries);
        $userInfo['d'] = $this->getQuery('SELECT ' . implode(' , ', $dayQueries),true) ;

        $wDates[] = date('Y-m-d');
        for($i=1; $i<=10; $i++) $wDates[] = date('Y-m-d', strtotime("- " . $i . " Week"));
        $i=0;
        foreach($wDates as $wDate)
        {
            if($wDates[$i+1])
            {
                $weekQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE (created_at <= '".$wDates[$i]."' AND created_at >= '".$wDates[$i+1]."')) AS '" . ($wDates[$i]) . "'") ;
            }
            $i++;
        }
        $userInfo['w'] = $this->getQuery('SELECT ' . implode(' , ', $weekQueries),true) ;
        
        $mDates[] = date('Y-m');
        //for($i=1; $i<=10; $i++) $mDates[] = date('Y-m-d', strtotime("- " . $i . " Month"));
        for ($i = 1; $i <= 10; $i++) {$mDates[] = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));}
        //print_r($mDates);
        $i=0;
        foreach($mDates as $mDate)
        {
            if($mDates[$i+1])
            {
                //$mnthQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE (created_at <= '".$mDates[$i]."' AND created_at >= '".$mDates[$i+1]."')) AS '" . ($mDates[$i]) . "'") ;
                $mnthQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE (date_format(created_at, '%Y-%m')= '".$mDates[$i]."')) AS '" . ($mDates[$i]) . "'") ;
            }
            $i++;
        }
        $userInfo['m'] = $this->getQuery('SELECT ' . implode(' , ', $mnthQueries),true) ;
        
        return $userInfo ;
    }
    
    public function getUsersCountStats($userType)
    {   
        $dDates[] = date('Y-m-d');
        for($i=1; $i<=10; $i++) $dDates[] = date('Y-m-d', strtotime("- " . $i . " Days"));
        $i=0;
        foreach($dDates as $dDate)
        {
            if($dDates[$i+1])
            {
                $dayQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE type='$userType' AND (DATE(created_at)= '".$dDates[$i]."')) AS '" . ($dDates[$i]) . "'") ;
            }
            $i++;
        }
        //print_r($dayQueries);
        $userInfo['d'] = $this->getQuery('SELECT ' . implode(' , ', $dayQueries),true) ;

        $wDates[] = date('Y-m-d');
        for($i=1; $i<=10; $i++) $wDates[] = date('Y-m-d', strtotime("- " . $i . " Week"));
        $i=0;
        foreach($wDates as $wDate)
        {
            if($wDates[$i+1])
            {
                $weekQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE type='$userType' AND (created_at <= '".$wDates[$i]."' AND created_at >= '".$wDates[$i+1]."')) AS '" . ($wDates[$i]) . "'") ;
            }
            $i++;
        }
        $userInfo['w'] = $this->getQuery('SELECT ' . implode(' , ', $weekQueries),true) ;
        
        $mDates[] = date('Y-m');
        //for($i=1; $i<=10; $i++) $mDates[] = date('Y-m-d', strtotime("- " . $i . " Month"));
        for ($i = 1; $i <= 10; $i++) {$mDates[] = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));}
        //print_r($mDates);
        $i=0;
        foreach($mDates as $mDate)
        {
            if($mDates[$i+1])
            {
                //$mnthQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE (created_at <= '".$mDates[$i]."' AND created_at >= '".$mDates[$i+1]."')) AS '" . ($mDates[$i]) . "'") ;
                $mnthQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE type='$userType' AND (date_format(created_at, '%Y-%m')= '".$mDates[$i]."')) AS '" . ($mDates[$i]) . "'") ;
            }
            $i++;
        }
        $userInfo['m'] = $this->getQuery('SELECT ' . implode(' , ', $mnthQueries),true) ;
        
        return $userInfo ;
    }
}

