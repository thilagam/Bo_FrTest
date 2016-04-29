<?php
/**
 * This Model  is responsible for ongoing operations
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Ongoing_Participation extends Ep_Db_Identifier
{

	protected $_name = 'Participation';

	//get Bidding details of writer
	public function getBiddingDetails($writerParticipation)
	{
		/*$bidQuery=	"Select *,p.status as writer_status,ap.id as process_id From Participation p
			             INNER JOIN ArticleProcess ap ON p.id=ap.participate_id
					Where p.id='".$writerParticipation."' AND ap.user_id=p.user_id AND article_path IS NOT NULL
					Group BY p.id
					ORDER BY ap.version DESC LIMIT 0,1";*/

        $bidQuery=  "Select *,p.status as writer_status,p.current_stage as writer_stage,
                            (select max(cycle+1) as latest_cycle From Participation p1 where p1.article_id=p.article_id ) as latest_cycle 
                           From Participation p                        
                    Where p.id='".$writerParticipation."'";
		//echo  $bidQuery;exit;        
        if(($count=$this->getNbRows($bidQuery))>0)
        {
            $WriterDetails=$this->getQuery($bidQuery,true);
            return $WriterDetails;

        }
        else
        	return NULL;			
	}
	//get facturation/price details of writer
	public function getFacturationDetails($writerParticipation,$article_id)
	{
		$paymentQuery=	"Select price_user,
						(select FORMAT((SUM(price_user)/count(id)),2) From Participation where article_id=p.article_id) as avg_price,
						i.status, r.id as royalty
					From Participation p
					LEFT JOIN Royalties r ON r.user_id=p.user_id
					LEFT JOIN  Invoice i ON r.invoiceId=i.invoiceId AND r.article_id=p.article_id
					Where p.id='".$writerParticipation."' and p.article_id='".$article_id."'
					GROUP BY p.id";

		//echo  $paymentQuery;exit;        
        if(($count=$this->getNbRows($paymentQuery))>0)
        {
            $facturationDetails=$this->getQuery($paymentQuery,true);
            return $facturationDetails;

        }
        else
        	return NULL;			
	}
    ////get the user price /////
    public function getUserPrice($artId)
    {
       $query = "select p.*, u.login, up.first_name, up.last_name
                    FROM ".$this->_name." p
					INNER JOIN User u ON p.user_id=u.identifier
					LEFT JOIN  UserPlus up ON up.user_id=u.identifier WHERE p.article_id = '".$artId."' AND p.status IN ('bid','under_study','disapproved','disapproved_temp','closed_temp') and cycle=0";
        //echo $query;exit;
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }
	//get process details
	public function getProcessDetails($process_id)
	{
		$processQuery=	"Select * From  ArticleProcess Where id='".$process_id."'";

		//echo  $paymentQuery;exit;        
        if(($count=$this->getNbRows($processQuery))>0)
        {
            $processDetails=$this->getQuery($processQuery,true);
            return $processDetails;

        }
        else
        	return NULL;
	}
	 /**Get over Due articles List**/
   public function getOverDueArticles($participation_id)
    {
      
      
            $addQuery=" AND p.id='".$participation_id."'";
            $limit=" LIMIT 1";
       


        $overDueQuery="SELECT p.id,a.title,d.title as deliveryTitle,p.article_submit_expires,
        					CONCAT(u.first_name,' ',UPPER(SUBSTRING(u.last_name, 1,1))) as first_name,p.article_id 
                        FROM Article a
                        INNER JOIN Participation p ON a.id=p.article_id
                        INNER JOIN Delivery d ON d.id=a.delivery_id
                        INNER JOIN UserPlus u ON u.user_id=p.user_id
                        WHERE p.article_submit_expires!=0 AND ((p.status in('bid','disapproved')) OR p.status='time_out') ".$addQuery."
                        GROUP BY p.id,a.id
                        ORDER BY p.article_submit_expires ASC".$limit;

         //AND ((p.article_submit_expires < UNIX_TIMESTAMP() AND p.status in('bid','disapproved')) OR p.status='time_out')
         //echo   $overDueQuery;exit;             

        if(($result = $this->getQuery($overDueQuery,true)) != NULL)
			return $result;
		else
			return "NO";

    }
    public function updateParticipation($data,$query)
    {
        $data['updated_at']=date("Y-m-d H:i:s", time());
        $this->updateQuery($data,$query);
    }

    public function checkParticipationInStage2($article_id)
    {
        $query="SELECT id FROM ".$this->_name." Where article_id='".$article_id."' and (current_stage='stage2' OR status in ('published') ) ";

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result[0]['id'];
        else
            return NULL;
    }

    //get all watchlist id's from participation w.r.t user
    function getParticipationWatchlist($user_id=NULL,$article_id=NULL)
    {
        
        if($user_id)
            $wheredQuery.=" AND p.user_id='".$user_id."'";
        if($article_id)
            $wheredQuery.=" AND p.article_id='".$article_id."'";
        
       


        $watchlistQuery="SELECT DISTINCT(p.watchlist_id)
                        FROM $this->_name p
                        INNER JOIN Watchlist w ON w.id=p.watchlist_id                        
                        WHERE 1=1 $wheredQuery";

         
         //echo   $watchlistQuery;exit;             

        if(($result = $this->getQuery($watchlistQuery,true)) != NULL)
            return $result;
        else
            return array();
    }

	//get process details
	public function getParticipationDetails($participation)
	{
		$processQuery=	"SELECT * FROM  Participation WHERE id='".$participation."'";

		if(($count=$this->getNbRows($processQuery))>0)
        {
            $processDetails=$this->getQuery($processQuery,true);
            return $processDetails;
		}
        else
        	return NULL;
	}	
		

}