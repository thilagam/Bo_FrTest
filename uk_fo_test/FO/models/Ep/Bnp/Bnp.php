<?php

class Ep_Bnp_Bnp extends Ep_Db_Identifier
{
    public function getCity(){
        $query = "SELECT `city_id`,`city_name` FROM `BNP_city` WHERE `status` = 'active' ";
        if (($result = $this->getQuery($query, true)) != NULL){
            return $result;
        }
        else
            return false;
    }
    function getSampleText($text_id)
    {
        if($text_id=="all")
            $sampleQuery="SELECT *
					  FROM BNP_sampletext";
        else
            $sampleQuery="SELECT description
					  FROM BNP_sampletext
					  WHERE sample_id='".$text_id."'";
        if(($result = $this->getQuery($sampleQuery,true)) != NULL)
            return $result[0]['description'];
        else
            return NULL;
    }

    function getArticleDetails($article_id)
    {
        $query="SELECT * FROM Article WHERE id='".$article_id."'";
        //echo $query;exit;

        if(($result = $this->getQuery($query,true)) != NULL)
        {
            return $result;
        }
        else
            return NULL;
    }
    function getAllDBStencils($article_id,$ebooker_cat_id=NULL,$language=NULL)
    {

        $condition ='';
        if($language)
            $condition.=" AND a.language='".$language."'";

        $dbStencilsQuery="SELECT ap.article_doc_content as db_text

							FROM ArticleProcess ap

							JOIN Participation p ON ap.participate_id=p.id

							JOIN Article a ON a.id=p.article_id

							JOIN Delivery d on a.delivery_id=d.id
                            JOIN ContractMissions AS CM ON  CM.contractmissionid = d.contract_mission_id

							WHERE  CM.bnp_mission ='yes' AND article_doc_content!='' AND a.id!='".$article_id."'
                            AND ap.version = (
                                SELECT MAX( version )
                                FROM `ArticleProcess`
                                WHERE participate_id = ap.participate_id
                            )
							$condition
							GROUP BY ap.article_doc_content
						";

        //echo $dbStencilsQuery;exit;

        if(($result = $this->getQuery($dbStencilsQuery,true)) != NULL)
        {
            $stencilsArray=array();
            foreach($result as $stencils)
            {
                $stencils['db_text'] = (strip_tags(str_replace(array(')','(',"\\",'/',"\n","\t"),'',$stencils['db_text'])));
                $stencilsArray[]=$stencils['db_text'];

            }

            $stencilsDBText=implode("###$$$###",$stencilsArray);
            //$stencilsDBText=utf8_encode(preg_replace('/\s+/', ' ', $stencilsDBText));
            $stencilsDBArray=array_unique(explode('###$$$###',$stencilsDBText));
            //echo "<pre>";print_r($stencilsDBArray);exit;
            return array_values($stencilsDBArray);
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

    public function getSampleTextIds(){
        $query = "SELECT * FROM `BNP_sampletext` GROUP BY `city_id`";
        if (($result = $this->getQuery($query, true)) != NULL){
            return $result;
        }
        else
            return false;
    }
    function getAllSampleText($bnp_sample_text_ids=NULL)
    {
        $condition = '';
        if($bnp_sample_text_ids !== '')
            $condition=" WHERE s.sample_id IN($bnp_sample_text_ids)";

        $sampleTextsQuery="SELECT s.description
							FROM BNP_sampletext s $condition";
        //echo $sampleTextsQuery;exit;
        if(($result = $this->getQuery($sampleTextsQuery,true)) != NULL)
        {
            $sampleArray=array();
            foreach($result as $sample)
            {
                //html_entity_decode($sample['description'],ENT_COMPAT,"UTF-8"
                $sampleArray[]= (strip_tags(str_replace(array(')','(',"\\",'/',"\n","\t"),'',$sample['description'])));
            }
            $sampleDBText=implode("###$$$###",$sampleArray);
            $sampleDBText=utf8_encode(preg_replace('/\s+/', ' ', $sampleDBText));
            $sampleDBArray=array_unique(explode('###$$$###',$sampleDBText));
            //echo "<pre>";print_r($sampleDBArray);exit;
            return array_values($sampleDBArray);
        }
        else
            return NULL;
    }
}