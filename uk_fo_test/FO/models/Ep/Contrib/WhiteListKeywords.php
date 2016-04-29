<?php
/**
 * Contact  Model
 * This Model  is responsible for Contact actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Contrib_WhiteListKeywords extends Ep_Db_Identifier
{
	protected $_name = 'WhiteListKeywords';
	private $id;
    private $keyword;
    private $country_code;


	public function loadData($array)
	{
        $this->id=$array["id"] ;
        $this->keyword=$array["keyword"] ;
		$this->country_code=$array["country_code"] ;

    	return $this;
	}
	public function loadintoArray()
	{
		$array = array();
        $array["id"] = $this->id;
        $array["keyword"] =  $this->keyword;
        $array["country_code"] = $this->country_code;

        return $array;
	}

    ///////getting the keyword with the id ///////////////
    public function getWlKeywords($id, $countrycode, $client_id)
    {
        $query = "SELECT * FROM WhiteListKeywords WHERE  id='".$id."' AND country_code='".$countrycode."' AND clientId = '".$client_id."' ";//." where ".$whereQuery;

        if(($result = $this->getQuery($query,true)) != NULL)
            return $result;
        else
            return "NO";
    }

}
