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
        
        //$dDates[] = date('Y-m-d');
        for($i=0; $i<=10; $i++) $dDates[] = date('Y-m-d', strtotime(date( 'Y-m-d' )." -$i Days"));
        $i=1;
        foreach($dDates as $dDate)
        {
            if($dDates[$i+1])
            {
                $dayQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE (DATE(created_at)= '".$dDates[$i-1]."')) AS '" . ($dDates[$i]) . "'") ;
            }
            $i++;
        }
        //print_r($dayQueries);
        $userInfo['d'] = $this->getQuery('SELECT ' . implode(' , ', $dayQueries),true) ;

        $wDates[] = date('Y-m-d');
        //for($i=1; $i<=10; $i++) $wDates[] = date('Y-m-d', strtotime(date( 'Y-m-d' )." -$i Week"));
        for($i=1; $i<=10; $i++)
        {
            $wDates[] = date('Y-m-d', strtotime(date( 'Y-m-d' )." -" . ($i*7) . " Days"));
        }
            
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
        //$dDates[] = date('Y-m-d');
        for($i=0; $i<=10; $i++) $dDates[] = date('Y-m-d', strtotime(date( 'Y-m-d' )." -$i Days"));
        $i=1;
        foreach($dDates as $dDate)
        {
            if($dDates[$i+1])
            {
                $dayQueries[] = ("(SELECT COUNT(*) AS count FROM User WHERE type='$userType' AND (DATE(created_at)= '".$dDates[$i-1]."')) AS '" . ($dDates[$i]) . "'") ;
            }
            $i++;
        }
        //print_r($dayQueries);
        $userInfo['d'] = $this->getQuery('SELECT ' . implode(' , ', $dayQueries),true) ;

        $wDates[] = date('Y-m-d');
        //for($i=1; $i<=10; $i++) $wDates[] = date('Y-m-d', strtotime(date( 'Y-m-d' )." -$i Week"));
        for($i=1; $i<=10; $i++) $wDates[] = date('Y-m-d', strtotime(date( 'Y-m-d' )." -" . ($i*7) . " Days"));
        
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
	//get refusal,validation and definitive refusal count
	public function getArticleStatusCountStats()
    {   
        //$dDates[] = date('Y-m-d');
        for($i=0; $i<=10; $i++) $dDates[] = date('Y-m-d', strtotime(date( 'Y-m-d' )." -$i Days"));
        $i=1;
        foreach($dDates as $dDate)
        {
            if($dDates[$i+1])
            {
                $dayValidateQueries[] = ("(SELECT COUNT(*) AS count From Participation p WHERE p.status='published' AND (DATE(p.updated_at)= '".$dDates[$i-1]."')) AS '" . ($dDates[$i]) . "'") ;
				
				$dayRefuseQueries[] = ("(SELECT COUNT(*) AS count From Participation p WHERE p.status='disapproved' AND (DATE(p.updated_at)= '".$dDates[$i-1]."')) AS '" . ($dDates[$i]) . "'") ;
				
				$dayDefiniteQueries[] = ("(SELECT COUNT(*) AS count From Participation p INNER JOIN  ArticleProcess ap ON p.id=ap.participate_id WHERE p.status='closed' AND (DATE(p.updated_at)= '".$dDates[$i-1]."')) AS '" . ($dDates[$i]) . "'") ;
            }
            $i++;
        }
        //print_r($dayQueries);
		$articleInfo['d']['validated'] = $this->getQuery('SELECT ' . implode(' , ', $dayValidateQueries),true) ;
		$articleInfo['d']['refused'] = $this->getQuery('SELECT ' . implode(' , ', $dayRefuseQueries),true) ;
		$articleInfo['d']['definite_refused'] = $this->getQuery('SELECT ' . implode(' , ', $dayDefiniteQueries),true) ;
		
		//echo "<pre>";print_r($articleInfo['d']);

         $wDates[] = date('Y-m-d');
        //for($i=1; $i<=10; $i++) $wDates[] = date('Y-m-d', strtotime(date( 'Y-m-d' )." -$i Week"));
        for($i=1; $i<=10; $i++) $wDates[] = date('Y-m-d', strtotime(date( 'Y-m-d' )." -" . ($i*7) . " Days"));
        
        $i=0;
        foreach($wDates as $wDate)
        {
            if($wDates[$i+1])
            {
                $weekValidateQueries[] = ("(SELECT COUNT(*) AS count From Participation p WHERE p.status='published' AND (p.updated_at<= '".$wDates[$i]."' AND p.updated_at >= '".$wDates[$i+1]."')) AS '" . ($wDates[$i]) . "'") ;
				
				 $weekRefuseQueries[] = ("(SELECT COUNT(*) AS count From Participation p WHERE p.status='disapproved' AND (p.updated_at<= '".$wDates[$i]."' AND p.updated_at >= '".$wDates[$i+1]."')) AS '" . ($wDates[$i]) . "'") ;
				 
				 $weekDefiniteQueries[] = ("(SELECT COUNT(*) AS count From Participation p INNER JOIN  ArticleProcess ap ON p.id=ap.participate_id WHERE p.status='closed' AND (p.updated_at<= '".$wDates[$i]."' AND p.updated_at >= '".$wDates[$i+1]."')) AS '" . ($wDates[$i]) . "'") ;
				 
            }
            $i++;
        }
        $articleInfo['w']['validated']  = $this->getQuery('SELECT ' . implode(' , ', $weekValidateQueries),true) ;
		$articleInfo['w']['refused']  = $this->getQuery('SELECT ' . implode(' , ', $weekRefuseQueries),true) ;
		$articleInfo['w']['definite_refused']  = $this->getQuery('SELECT ' . implode(' , ', $weekDefiniteQueries),true) ;
		
		//echo "<pre>";print_r($articleInfo['w']);
		
        
        /*$mDates[] = date('Y-m');
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
        $userInfo['m'] = $this->getQuery('SELECT ' . implode(' , ', $mnthQueries),true) ; */
        
        return $articleInfo ;
    }

     public function payment_stats($start,$end,$type,$search_type){
         //echo $end;exit;
        if($start==''){
            $start=date('Y-m-d H:i:s',strtotime('1970-01-01'));
        }
        if($end=='' || $end==$start){
            $end=date('Y-m-d H:i:s',strtotime(now));
        }
         if($type==0 && $type!='' && $search_type!=5 && $search_type!=6 && $search_type!=7){
            $type="`premium_option` = '".$type."' AND (
                                    d.created_at
                                    BETWEEN '".$start."' AND '".$end."'
                                ) ";
         }else if($type>0 && $type!='' && $search_type!=5 && $search_type!=6 && $search_type!=7){
            $type="`premium_option` > '0' AND (
                                    d.created_at
                                    BETWEEN '".$start."' AND '".$end."'
                                ) ";
         }else if($search_type==5){
                if($type==0 && $type!=''){
                    $type="`created_by` = 'backend' AND (
                                            u.created_at
                                            BETWEEN '".$start."' AND '".$end."'
                                        ) ";
                }else if($type>0 && $type!=''){
                    $type="`created_by` = 'frontend' AND (
                                            u.created_at
                                            BETWEEN '".$start."' AND '".$end."'
                                        ) ";
                }else{
                    $type="u.created_at BETWEEN '".$start."' AND '".$end."' ";
                }
         }else if($search_type==6){
                if($type==0 && $type!=''){
                    $type="`profile_type` = 'junior' AND (
                                            u.created_at
                                            BETWEEN '".$start."' AND '".$end."'
                                        ) ";
                }else if($type==1 && $type!=''){
                    $type="`profile_type` = 'senior' AND (
                                            u.created_at
                                            BETWEEN '".$start."' AND '".$end."'
                                        ) ";
                }else if($type==2 && $type!=''){
                    $type="`profile_type` = 'sub-junior' AND (
                                            u.created_at
                                            BETWEEN '".$start."' AND '".$end."'
                                        ) ";
                }else{
                    $type="u.created_at BETWEEN '".$start."' AND '".$end."' ";
                }
         }else if($search_type==7){
				if($type==1 && $type!=''){
                    $type="i.status = 'Paid' AND (
                                            i.created_at
                                            BETWEEN '".$start."' AND '".$end."'
                                        ) ";
                }else if($type==0 && $type!=''){
                     $type="!(i.status = 'Paid') AND (
                                            i.created_at
                                            BETWEEN '".$start."' AND '".$end."'
                                        ) ";
                }else{
                    $type="i.created_at BETWEEN '".$start."' AND '".$end."' ";
                }
		 }else{
            $type="d.created_at BETWEEN '".$start."' AND '".$end."' ";
         }

         $query='';
         $query2='';
         switch ($search_type) {
                case '0':
              
                        $query="SELECT a.id, a.title,u.identifier,u.email,up.first_name,up.last_name,d.AOtype,p.created_at, pt.status, p.amount, a.contrib_percentage, ((p.amount*a.contrib_percentage)/100) as Contrib_share,(p.amount-((p.amount*a.contrib_percentage)/100)) as EP_share
                                FROM Delivery d
                                LEFT JOIN Payment_article p ON p.user_id = d.user_id
                                LEFT JOIN Article a ON a.delivery_id = d.id
                                LEFT JOIN Participation pt ON pt.article_id = a.id
                                LEFT JOIN User u ON u.identifier = pt.user_id
                                LEFT JOIN UserPlus up ON up.user_id = u.identifier
                                LEFT JOIN Royalties r ON r.participate_id = pt.id
                                WHERE ".$type."
                                AND pt.status = 'published'
                                GROUP BY a.id
                                ";
                    break;
                case '1':
                        $query="SELECT a.id, a.title,u.identifier,u.email,up.first_name,up.last_name,d.AOtype,p.created_at, pt.status, p.amount,p.amount_paid,p.pay_type as type,(p.amount_paid-p.amount) as tax, a.contrib_percentage, ((p.amount*a.contrib_percentage)/100) as Contrib_share,(p.amount-((p.amount*a.contrib_percentage)/100)) as EP_share
                                FROM Delivery d
                                LEFT JOIN Payment_article p ON p.user_id = d.user_id
                                LEFT JOIN Article a ON a.delivery_id = d.id
                                LEFT JOIN Participation pt ON pt.article_id = a.id
                                LEFT JOIN User u ON u.identifier = d.user_id
                                LEFT JOIN UserPlus up ON up.user_id = u.identifier
                                LEFT JOIN Royalties r ON r.participate_id = pt.id
                                WHERE ".$type."
                                AND pt.status = 'published' AND p
                                GROUP BY a.id
                                ";
                    break;
                case '2':
                    $query="SELECT a.id,a.title as article_title,u.email,d.title as ao_title,pt.price_user,p.amount,d.created_at
                            FROM Delivery d
                            LEFT JOIN Article a ON a.delivery_id = d.id
                            LEFT JOIN Participation pt ON pt.article_id = a.id
                            LEFT JOIN User u ON u.identifier = pt.user_id
                            LEFT JOIN Payment_article p ON p.user_id = d.user_id
                            WHERE ".$type."
                            AND pt.status = 'published'
                            GROUP BY a.id";
                    break;
                case '3':
                    $query="SELECT a.id,a.title as article_title,u.email,d.title as ao_title,pt.price_user,d.created_at
                            FROM Delivery d
                            LEFT JOIN Article a ON a.delivery_id = d.id
                            LEFT JOIN Participation pt ON pt.article_id = a.id
                            LEFT JOIN User u ON u.identifier = pt.user_id
                            
                            WHERE ".$type."
                            AND pt.status = 'disapproved'
                            GROUP BY a.id";
                    break;
                case '4':
                     $query="SELECT a.id,a.title as article_title,u.email,d.title as ao_title,pt.price_user,d.created_at
                            FROM Delivery d
                            LEFT JOIN Article a ON a.delivery_id = d.id
                            LEFT JOIN Participation pt ON pt.article_id = a.id
                            LEFT JOIN User u ON u.identifier = pt.user_id
                            
                            WHERE ".$type."
                            AND pt.status = 'closed'
                            GROUP BY a.id";
                    break;
                case '5':
                    $query="SELECT u.email,up.first_name,up.last_name,u.verified_status,u.created_at
                            FROM User u
                            LEFT JOIN UserPlus up ON up.user_id = u.identifier
                            WHERE ".$type."
                            AND u.status = 'active'
                            AND u.type='client'
                            ORDER BY up.first_name ASC";
                    break;
                case '6':
                    $query="SELECT u.email,up.first_name,up.last_name,u.verified_status,u.profile_type,u.created_at
                            FROM User u
                            LEFT JOIN UserPlus up ON up.user_id = u.identifier
                            WHERE ".$type."
                            AND u.status = 'active'
                            AND u.type='contributor'
                            ORDER BY up.first_name ASC";
                    break;
                case '7':
                    $query="SELECT i.invoiceId,u.identifier,i.total_invoice,i.total_invoice_paid,i.tax,i.created_at,i.updated_at,i.status,u.email,c.nationality
                            FROM Invoice as i
                            LEFT JOIN User as u ON u.identifier = i.user_id
                            LEFT JOIN Contributor as c ON c.user_id = u.identifier
                            WHERE ".$type."
                            AND u.status = 'active'
                            AND u.type='contributor'
                            ORDER BY i.created_at ASC";

                    $query2="SELECT SUM(i.total_invoice) as total,SUM(i.total_invoice_paid) as paid,SUM(i.tax) as tax,c.nationality
                            FROM Invoice i
                            LEFT JOIN User u ON u.identifier = i.user_id
                            LEFT JOIN Contributor c ON c.user_id = u.identifier
                            WHERE ".$type."
                            AND u.status = 'active'
                            AND u.type='contributor'
                            GROUP BY c.nationality
                            ORDER BY i.created_at ASC";
                    break;
                default:
                    # code...
                    break;
            }

       //echo $query; //exit;
        if($data[0]=$this->getQuery($query,true))
        {  
           if($query2!=''){
            $data[1]=$this->getQuery($query2,true);
           } 
           return $data;
        }
        else
             return 0;

    }
}

