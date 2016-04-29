<?php
class Ep_Article_Ebooker extends Ep_Db_Identifier
{
	private $regex_expression='/[\?\,\.\!]/';
	
	function getSampleText($text_id)
	{
		$sampleQuery="SELECT description 
					  FROM EB_sampletext
					  WHERE sample_id='".$text_id."'";
					  
		
		//echo $sample_text_id;exit;
		
		if(($result = $this->getQuery($sampleQuery,true)) != NULL)
			return $result[0]['description'];
		else
			return NULL;
	}
	
	function getAllSampleText($ebooker_cat_id=NULL)
	{
		
		if($ebooker_cat_id)
			$condition=" WHERE c.cat_id='".$ebooker_cat_id."'";
		
		$sampleTextsQuery="SELECT s.description 
							FROM EB_sampletext s
							INNER JOIN EB_token t ON s.token_id=t.token_id
							INNER JOIN EB_category c ON c.cat_id=t.category_id
							$condition
					  ";
		
		//echo $sampleTextsQuery;exit;
		
		if(($result = $this->getQuery($sampleTextsQuery,true)) != NULL)
		{			
			$sampleArray=array();
			foreach($result as $sample)
			{
				$sampleArray[]=$sample['description'];
			}
			$sampleDBText=implode("###$$$###",$sampleArray);
			$sampleDBText=utf8_encode(preg_replace('/\s+/', ' ', $sampleDBText));
			$sampleDBArray=array_unique(explode('###$$$###',$sampleDBText));			
			//echo "<pre>";print_r($stencilsDBArray);exit;
			return array_values($sampleDBArray);
		}
		else
			return NULL;
	}
	
	function getTokens($tokenids)
	{
		$mandatory_tokens=array();
		$optional_tokens=array();
		
		$tokensQuery="SELECT token_name ,token_type
					  FROM  EB_token
					  WHERE FIND_IN_SET (token_id,'$tokenids') AND token_type in ('mandatory','optional')";
					  
		
		//echo $tokensQuery;exit;
		
		if(($token_result = $this->getQuery($tokensQuery,true)) != NULL)
		{
			foreach($token_result as $token)
			{
				$token['token_name']=preg_replace('/\s+/', ' ',$token['token_name']);
				$token_text=rawurlencode(utf8_encode($token['token_name']));//($token['token_name']);
				$token_text=str_replace('%3F',"?",$token_text);
				$combine_tokens=explode("%23%23",$token_text);				
				if($token['token_type']=='mandatory')
				{					
					foreach($combine_tokens as $token)
					{
						$mandatory_tokens[]=$token;
					}	
					
				}	
				else if($token['token_type']=='optional')
				{
					foreach($combine_tokens as $token)
					{
						$optional_tokens[]=$token;
					}
				}	
			}
			$tokens=array($mandatory_tokens,$optional_tokens);
			
			//echo "<Pre>";print_r($tokens);exit;
			return $tokens;
		}			
		else
			return NULL;
	}
	
	//get tokens based on Article id
	function getArticleTokens($article_id)
	{
		$tokensQuery="SELECT token_name 
					  FROM  EB_token
					  WHERE FIND_IN_SET
						(token_id,(SELECT ebooker_tokenids FROM Article WHERE id='".$article_id."'))
					AND token_type in ('mandatory','optional')";
					  
		
		//echo $tokensQuery;exit;
		
		if(($token_result = $this->getQuery($tokensQuery,true)) != NULL)
		{
			foreach($token_result as $token)
			{
				//$tokens[]=rawurlencode(utf8_encode($token['token_name']));
				$token_text=$token['token_name'];
				$combine_tokens=explode("##",$token_text);	
				foreach($combine_tokens as $token)
				{
					$tokens[]=$token;
				}
				//$tokens[]=$token['token_name'];
			}
			return $tokens;
		}			
		else
			return NULL;
	}
	
	//get ALL DB stencils other than given article
	function getAllDBStencils($article_id,$ebooker_cat_id=NULL,$language=NULL)
	{		
		if($ebooker_cat_id)
			$condition=" AND a.ebooker_cat_id='".$ebooker_cat_id."'";
		if($language)
			$condition.=" AND a.language='".$language."'";

		$dbStencilsQuery="SELECT ap.article_doc_content as db_text
							FROM ArticleProcess ap
							JOIN Participation p ON ap.participate_id=p.id
							JOIN Article a ON a.id=p.article_id
							JOIN Delivery d on a.delivery_id=d.id
							WHERE d.stencils_ebooker='yes' AND article_doc_content!='' AND a.id!='".$article_id."'
							$condition
							GROUP BY ap.article_doc_content
						";
					  
		
		//echo $dbStencilsQuery;exit;
		
		if(($result = $this->getQuery($dbStencilsQuery,true)) != NULL)
		{			
			$stencilsArray=array();
			foreach($result as $stencils)
			{
				$stencilsArray[]=$stencils['db_text'];
			}
			$stencilsDBText=implode("###$$$###",$stencilsArray);
			$stencilsDBText=utf8_encode(preg_replace('/\s+/', ' ', $stencilsDBText));
			$stencilsDBArray=array_unique(explode('###$$$###',$stencilsDBText));			
			//echo "<pre>";print_r($stencilsDBArray);exit;
			return array_values($stencilsDBArray);
		}			
		else
			return NULL;
	}
	function getWhiteListKeywords($language='uk')
	{
		$whiteListKeywords=array();		
		
		$keywordQuery="SELECT keyword 
					  FROM  EB_WhiteList
					  WHERE language='".$language."' AND active=1
					  ORDER BY CHAR_LENGTH(keyword) DESC
					  ";
					  
		
		//echo $tokensQuery;exit;
		
		if(($keyword_result = $this->getQuery($keywordQuery,true)) != NULL)
		{
			foreach($keyword_result as $keyword)
			{
				$keyword_text=rawurlencode(utf8_encode($keyword['keyword']));//($token['token_name']);
				$keyword_text=str_replace('%3F',"?",$keyword_text);
				
				$keyword_text=preg_replace($this->regex_expression,'',$keyword_text);
				
				$whiteListKeywords[]=$keyword_text;
				
			}
			//echo "<Pre>";print_r($whiteListKeywords);exit;
			return $whiteListKeywords;
		}			
		else
			return NULL;
	}
	function getArticleDetails($article_id)
	{
		$query="SELECT * FROM Article WHERE id='".$article_id."'";

		if(($result = $this->getQuery($query,true)) != NULL)
		{
			return $result;
		}
		else
			return NULL;
	}
	
	function getStencilstoTranslate($article_id)
	{
		$query="SELECT v.stencil_content
				FROM  ValidStencils v 
				INNER JOIN Article a ON FIND_IN_SET(v.id,a.stencils_translate) > 0 
				WHERE a.id='".$article_id."'
				ORDER BY v.created_at ASC
				";				
		//echo $query;exit;		
		if(($result = $this->getQuery($query,true)) != NULL)
		{
			foreach($result as $stencil)
			{
				$translateStencils[]=$stencil['stencil_content'];
			}
			return $translateStencils;
		}
		else
			return NULL;
	}
	
	
}