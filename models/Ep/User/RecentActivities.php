<?php

class Ep_User_RecentActivities extends Ep_Db_Identifier
{
	protected $_name = 'RecentActivities';
	
	public function insertRecentActivities($activityarray)
	{
		$this->insertQuery($activityarray);
	}
	
	public function ListRecentActivitiesCount($client)
	{
		$SelectcntQuery="SELECT 
							count(*) as activitycount 
						FROM 
							".$this->_name." r INNER JOIN Article a ON a.id=r.article_id 
							INNER JOIN Delivery d ON d.id=a.delivery_id
							INNER JOIN User u ON r.activity_by=u.identifier 
							LEFT JOIN UserPlus up ON u.identifier=up.user_id 
						WHERE 
							d.user_id='".$client."' 
						ORDER BY 
							r.created_at DESC 
						LIMIT 30";
		//echo $SelectcntQuery;exit;
		$ResultCnt = $this->getQuery($SelectcntQuery,true);
		return $ResultCnt[0]['activitycount'];
	}
	
	public function ListRecentActivities($var,$client)
	{
		$SelectQuery="SELECT 
							r.*,a.title as atitle,p.title as polltitle,p.id as pollid,u.email,up.first_name,up.last_name,DATE_FORMAT( r.created_at, '%d/%m/%Y %H:%i:%s' ) AS createdat,d.premium_option,u.type as usertype,
							TIMESTAMPDIFF( MINUTE , r.created_at, now( ) ) as minutediff, TIMESTAMPDIFF( HOUR , r.created_at, now( ) ) as hourdiff,c.identifier,c.user_id as comment_userid,c.comments,c.active
						FROM ".$this->_name." r LEFT JOIN Article a ON a.id=r.article_id 
							LEFT JOIN AdComments c ON r.commentid=c.identifier
							LEFT JOIN Delivery d ON d.id=a.delivery_id
							LEFT JOIN Poll p ON p.id=r.article_id 
							INNER JOIN User u ON r.activity_by=u.identifier 
							LEFT JOIN UserPlus up ON u.identifier=up.user_id 
						WHERE 
							r.user_id='".$client."' order by r.created_at DESC Limit ".$var.",10";
		$ResultSet = $this->getQuery($SelectQuery,true);
		return $ResultSet;
	}
}