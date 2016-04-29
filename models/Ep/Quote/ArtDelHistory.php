<?php
/**
 * Ep_Quote_ArtDelHistory
 * @package Quote
 * @version 1.0
 */
class Ep_Quote_ArtDelHistory extends Ep_Db_Identifier
{
	protected $_name = 'ArticleHistory';
	
	public function getAOHistory($params)
    {
		$condition = "1=1";
		if($params['cmid'])
		{
            $condition="article_id IN(SELECT d.id FROM Delivery d WHERE d.contract_mission_id='".$params['cmid']."' ) OR article_id IN (SELECT a.id FROM Article a JOIN Delivery d ON a.delivery_id=d.id WHERE d.contract_mission_id='".$params['cmid']."' )";
		}
		
		if($params['did'])
		{
			$condition="article_id ='".$params['did']."' OR article_id IN (SELECT a.id FROM Article a WHERE a.delivery_id='".$params['did']."' )";
		}
		
        $query="SELECT ah.*,up.first_name,up.last_name,u.type
                 FROM $this->_name ah
				 INNER JOIN User u ON u.identifier=ah.user_id 
				 LEFT JOIN UserPlus up ON up.user_id = ah.user_id	
                 WHERE $condition
                 ORDER BY ah.action_at DESC";
	
        if(($count=$this->getNbRows($query))>0)
        {
            $aoHistory=$this->getQuery($query,true);
            return $aoHistory;
        }
        else
            return NULL;        
    }
	
	public function getAOHistoryProd($params)
    {
		$condition = "1=1";
		if($params['cmid'])
		{
            $condition="(article_id IN(SELECT d.id FROM Delivery d WHERE d.contract_mission_id='".$params['cmid']."') OR article_id IN (SELECT a.id FROM Article a JOIN Delivery d ON a.delivery_id=d.id WHERE d.contract_mission_id='".$params['cmid']."' )) AND ((ah.action='profile_accepted' OR ah.action='profile accepted' OR ah.action='correction_profile_accept') OR (ah.stage='stage2' AND ah.action='validated') OR (ah.stage='stage0' AND ah.action='article_sent') OR ah.action=' refused definite' OR ah.action='contrib_comment' OR ah.action='article_not_sent' OR ah.action='bid_time_over')";
		}
		
		if($params['did'])
		{
			$condition="(( article_id ='".$params['did']."' OR article_id IN (SELECT a.id FROM Article a WHERE a.delivery_id='".$params['did']."' )) AND ((ah.action='profile_accepted' OR ah.action='profile accepted' OR ah.action='correction_profile_accept') OR (ah.stage='stage2' AND ah.action='validated') OR (ah.stage='stage0' AND ah.action='article_sent') OR ah.action=' refused definite' OR ah.action='contrib_comment' OR ah.action='article_not_sent' OR ah.action='bid_time_over') )";
		}
		
        $query="SELECT ah.*,up.first_name,up.last_name,u.type
                 FROM $this->_name ah
				 INNER JOIN User u ON u.identifier=ah.user_id 
				 INNER JOIN UserPlus up ON up.user_id = ah.user_id	
                 WHERE $condition
                 ORDER BY ah.action_at DESC";

        if(($count=$this->getNbRows($query))>0)
        {
            $aoHistory=$this->getQuery($query,true);
            return $aoHistory;
        }
        else
            return NULL;        
    }
}
?>