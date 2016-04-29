<?php
/**
 * Ep_Contract_Article
 * @author Arun
 * @package Contract
 * @version 1.0
 */
class Ep_Contract_Article extends Ep_Db_Identifier
{
    protected $_name = 'C_Article';
    private $id;
    private $client_id;
    private $delivery_id;
    private $title;
    private $type;
    private $category;
    private $words;
    private $article_path;
    private $author;
    private $created_at;
    private $delivery_date;
    public function loadData($array)
    {
        $this->client_id=$array["client_id"];
        $this->title=$array["title"];
        $this->delivery_id=$array["delivery_id"];
        $this->type=$array["type"];
        $this->category=$array["category"];
        $this->words=$array["words"];
        $this->author=$array["author"];
        $this->article_path=$array["article_path"];
        $this->delivery_date=$array["delivery_date"];
        $this->article_path=$array["article_path"];
        return $this;
    }
    public function loadintoArray()
    {
        $array = array();
        $array["client_id"] = $this->client_id;
        $array["title"] = $this->title;
        $array["delivery_id"] = $this->delivery_id;
        $array["type"] = $this->type;
        $array["category"] = $this->category;
        $array["words"] = $this->words;
        $array["author"] = $this->author;
        $array["created_at"] = $this->created_at;
        $array["delivery_date"] = $this->delivery_date;
         $array["article_path"]=$this->article_path;
        return $array;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }
    public function __get($name){
        return $this->$name;
    }
    public function getAllArticles()
    {
        $articleDetails="select c.client_id,a.id,a.title,d.title as delivery,u.email,cl.company_name,a.delivery_date
                FROM ".$this->_name." a
                INNER JOIN C_Delivery d ON d.id=a.delivery_id
                INNER JOIN Contracts c ON c.id=d.contract_id
                INNER JOIN User u ON c.client_id=u.identifier
                LEFT JOIN Client cl ON u.identifier=cl.user_id
                Group BY a.id
                ORDER BY d.delivery_date DESC";
        //echo $deliveryDetails;exit;
        if(($articles=$this->getQuery($articleDetails,true))!=NULL)
            return $articles;
        else
            return "NO";
    }
    public function getArticleDetails($articleid=NULL)
    {
        if($articleid)
            $where= " WHERE id='".$articleid."'";
        $query="select
                    *
                from ".$this->_name.
            $where
        ;
        //echo $query;exit;
        if(($artilces=$this->getQuery($query,true))!=NULL)
            return $artilces;
        else
            return "Not Exists";
    }
    public function updateArticle($data,$aid)
    {
        $where=" id=".$aid;
        //print_r($data);exit;
        echo $this->updateQuery($data,$where);
    }
    public function getArticleList($deliveryId)
    {
        if($deliveryId)
            $where= " WHERE delivery_id='".$deliveryId."'";
        $query="select
                    *
                from ".$this->_name.
            $where
        ;
        //echo $query;exit;
        if(($artilces=$this->getQuery($query,true))!=NULL)
            return $artilces;
        else
            return "Not Exists";
    }
}
