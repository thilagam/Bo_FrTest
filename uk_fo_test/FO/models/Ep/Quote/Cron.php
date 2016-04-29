<?php

class Ep_Quote_Cron extends Ep_Db_Identifier
{	
	//Tech  answer Challenge time OVer quotes
	public function getTechAnswerChallengeTimeOverQuotes()
	{
		$techTimeOverQuery="SELECT l.user_id,l.quote_id,q.quote_by,q.title From QuotesLog l

					INNER JOIN Quotes q ON q.identifier=l.quote_id

					Where  q.tech_timeline < now() AND q.tec_review='challenged' AND l.action='tech_challenged'
					GROUP BY l.quote_id
					ORDER BY l.action_at DESC;
					";
		if(($count=$this->getNbRows($techTimeOverQuery))>0)
        {           
           $techTimeOVerQuotes=$this->getQuery($techTimeOverQuery,true);             
            return $techTimeOVerQuotes;
        }
        else
            return NULL;			
	}
//SEO  answer Challenge time OVer quotes
	public function getSeoAnswerChallengeTimeOverQuotes()
	{
		$seoTimeOverQuery="SELECT l.user_id,l.quote_id,q.quote_by,q.title From QuotesLog l

					INNER JOIN Quotes q ON q.identifier=l.quote_id

					Where  q.seo_timeline < now() AND q.seo_review='challenged' AND l.action='seo_challenged'
					GROUP BY l.quote_id
					ORDER BY l.action_at DESC;
					";
		if(($count=$this->getNbRows($seoTimeOverQuery))>0)
        {           
           $seoTimeOVerQuotes=$this->getQuery($seoTimeOverQuery,true);             
            return $seoTimeOVerQuotes;
        }
        else
            return NULL;			
	}

	//Prod  answer Challenge time OVer quotes
	public function getprodAnswerChallengeTimeOverQuotes()//AND q.prod_timeline >= (UNIX_TIMESTAMP()-(1*60))
	{
		$seoTimeOverQuery="SELECT l.user_id,l.quote_id,q.quote_by,q.title From QuotesLog l

					INNER JOIN Quotes q ON q.identifier=l.quote_id

					Where  q.prod_timeline < UNIX_TIMESTAMP()   AND q.prod_review='challenged' AND l.action in ('prod_saved','prod_validated_ontime','prod_validated_delay')
					GROUP BY l.quote_id
					ORDER BY l.action_at DESC;
					";
		if(($count=$this->getNbRows($seoTimeOverQuery))>0)
        {           
           $seoTimeOVerQuotes=$this->getQuery($seoTimeOverQuery,true);             
            return $seoTimeOVerQuotes;
        }
        else
            return NULL;			
	}


}