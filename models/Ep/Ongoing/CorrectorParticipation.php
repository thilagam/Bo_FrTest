<?php
/**
 *  This Model  is responsible for ongoing operations *
 *  @author Arun *
 *  @editor Arun *
 *  @version 1.0 */
 class EP_Ongoing_CorrectorParticipation extends Ep_Db_Identifier{
	 protected $_name = 'CorrectorParticipation';
	 //get Bidding details of corrector
	 public function getBiddingDetails($correctorParticipation)	{
		 /*$bidQuery=	"Select *,p.status as corrector_status,ap.id as process_id From CorrectorParticipation p
		   INNER JOIN ArticleProcess ap ON p.participate_id=ap.participate_id
		   Where p.id='".$correctorParticipation."' AND ap.user_id=p.corrector_id AND ap.article_path IS NOT NULL
		   Group BY p.id
		   ORDER BY ap.version DESC LIMIT 0,1";*/
		   $bidQuery=  "Select *,p.status as corrector_status,
		   (select max(cycle+1) as latest_cycle From CorrectorParticipation p1 where p1.article_id=p.article_id ) as latest_cycle
		   From CorrectorParticipation p
		   Where p.id='".$correctorParticipation."'";
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
		public function getFacturationDetails($correctorParticipation,$article_id)
		{
			$paymentQuery=	"Select price_corrector,
			(select FORMAT((SUM(price_corrector)/count(id)),2) From CorrectorParticipation where article_id=p.article_id) as avg_price,
			i.status, r.id as royalty
			From CorrectorParticipation p
			LEFT JOIN Royalties r ON r.user_id=p.corrector_id
			LEFT JOIN  Invoice i ON r.invoiceId=i.invoiceId AND r.article_id=p.article_id
			Where p.id='".$correctorParticipation."' and p.article_id='".$article_id."'
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
		/**Get over Due articles List**/
		public function getOverDueArticles($participation_id)
		{
			$addQuery=" AND p.id='".$participation_id."'";
			$limit=" LIMIT 1";
			$overDueQuery="SELECT p.id,a.title,d.title as deliveryTitle,p.corrector_submit_expires,
			CONCAT(u.first_name,' ',UPPER(SUBSTRING(u.last_name, 1,1))) as first_name,p.article_id
			FROM Article a
			INNER JOIN CorrectorParticipation p ON a.id=p.article_id
			INNER JOIN Delivery d ON d.id=a.delivery_id
			INNER JOIN UserPlus u ON u.user_id=p.corrector_id
			WHERE p.corrector_submit_expires!=0 AND ((p.status in('bid','disapproved')) OR p.status='time_out') ".$addQuery."
			GROUP BY p.id,a.id
			ORDER BY p.corrector_submit_expires ASC".$limit;
			//AND ((p.corrector_submit_expires < UNIX_TIMESTAMP() AND p.status in('bid','disapproved')) OR p.status='time_out')
			//echo   $overDueQuery;exit;
			if(($result = $this->getQuery($overDueQuery,true)) != NULL)
			return $result;
			else
			return "NO";
		}
		////get the corrector price /////
		public function getCorrectorPrice($artId){
			$query = "select cp.*, u.login, up.first_name, up.last_name
			FROM ".$this->_name." cp
			INNER JOIN User u ON cp.corrector_id=u.identifier
			LEFT JOIN  UserPlus up ON up.user_id=u.identifier WHERE cp.article_id = '".$artId."' AND cp.status IN ('bid','under_study','disapproved','disapproved_temp','closed_temp') and cycle=0";
			//echo $query;exit;
			if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
			else
			return "NO";
		}
		public function updateParticipation($data,$query)
		{
            $data['updated_at']=date("Y-m-d H:i:s", time());
            $this->updateQuery($data,$query);
		}
		//get correctors list to send email (refused,closed)
		public function sendEmailCorrectors($article_id,$type)    {
			if($type=='refused')
			$status_query=" status in ('bid_corrector','bid_refused','bid_refused_temp')";
			elseif($type=='closed')
			$status_query=" status in ('bid','under_study','disapproved')";
			$query="SELECT corrector_id FROM ".$this->_name."
			WHERE cycle=0 AND article_id='".$article_id."' AND $status_query";
			//echo $query;exit;
			if(($result = $this->getQuery($query,true)) != NULL)
			return $result;
			else
			return NULL;
		}
		//get max+1 cycle to update the cycle
		public function getMaxCycle($article_id)
		{
			$query="SELECT max(cycle) as cycle FROM $this->_name WHERE article_id='".$article_id."'";
			//echo $query;exit;
			if(($result = $this->getQuery($query,true)) != NULL)
			return $result[0]['cycle'];
			else
			return 0;
		}
		//get previous cycle participants
		public function getPrevCycleUsers($article_id)
		{
			$query="SELECT DISTINCT (corrector_id) FROM $this->_name WHERE article_id='".$article_id."' AND cycle!=0";
			//echo $query;exit;
			if(($correctors = $this->getQuery($query,true)) != NULL)
			return $correctors;
			else
			return NULL;
		}
		//get all watchlist id's from Correctorparticipation w.r.t user
		function getParticipationWatchlist($user_id=NULL,$article_id=NULL)
		{
			if($user_id)
			$wheredQuery.=" AND p.corrector_id='".$user_id."'";
			if($article_id)
			$wheredQuery.=" AND p.article_id='".$article_id."'";
			$watchlistQuery="SELECT DISTINCT(p.watchlist_id)
			FROM $this->_name p
			INNER JOIN Watchlist w ON w.id=p.watchlist_id
			WHERE 1=1 $wheredQuery";
			// echo   $watchlistQuery;exit;
			if(($result = $this->getQuery($watchlistQuery,true)) != NULL)
			return $result;
			else
			return array();

		}
		
		//get process details
		public function getCorrectorParticipationDetails($participation)
		{
			$processQuery=	"SELECT * FROM  CorrectorParticipation WHERE id='".$participation."'";

			if(($count=$this->getNbRows($processQuery))>0)
			{
				$processDetails=$this->getQuery($processQuery,true);
				return $processDetails;
			}
			else
				return NULL;
		}	
}
