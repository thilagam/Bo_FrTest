<?php

class Ep_Tariff_tariff extends Ep_Db_Identifier
{
	protected $_name = 'tariff';
    public function insertTariff($tariff, $tariffdetails)
    { 
        $this->_name = 'tariff' ;
        $this->createIdentifier() ;
        $tariff['id']    =    $this->getIdentifier() ;
        $this->insertQuery($tariff) ;
        
        $this->_name = 'tariffdetails' ;
        for ($i=0; $i < sizeof($tariffdetails); $i++) {
            $this->createIdentifier() ;
            $tariffdata['id']    =    $this->getIdentifier() ;
            $tariffdata['client_id'] = $tariffdetails[$i]['client_id'];
            $tariffdata['client_name'] = $tariffdetails[$i]['client_name'];
            $tariffdata['delivery_id'] = $tariffdetails[$i]['delivery_id'];
            $tariffdata['delivery_name'] = $tariffdetails[$i]['delivery_name'];
            $tariffdata['articles_count'] = $tariffdetails[$i]['articles_count'];
            $tariffdata['avg_price_per_word'] = $tariffdetails[$i]['avg_price_per_word'];
            $tariffdata['min_price'] = $tariffdetails[$i]['min_price'];
            $tariffdata['max_price'] = $tariffdetails[$i]['max_price'];
            $tariffdata['contrib_price_sum'] = $tariffdetails[$i]['contrib_price_sum'];
            $tariffdata['article_total_price_sum'] = $tariffdetails[$i]['article_total_price_sum'];
            $tariffdata['delivery_time'] = $tariffdetails[$i]['delivery_time'];
            $tariffdata['delivery_time_option'] = $tariffdetails[$i]['delivery_time_option'];
            $tariffdata['tariff_id'] = $tariff['id'];
            $this->insertQuery($tariffdata) ;
            unset($tariffdata);
        }
    }
    public function updateTariff($id, $tariff, $tariffdetails)
    {
        $this->_name = 'tariff' ;
        $tariffwhere  =   " id='".$id."'" ;
        $this->updateQuery($tariff, $tariffwhere) ;
		
        $this->_name = 'tariffdetails' ;
		if(sizeof($tariffdetails)>0)
		{
			$results =   $this->getQuery("SELECT id FROM ".$this->_name." WHERE tariff_id='$id'",true) ;
			foreach ($results as $result) {
				$tariffdetails_arr[] = $result['id'];
			}
			$tariffdetails_arr = array_unique(array_filter($tariffdetails_arr));
			//echo '<pre>';print_r($tariff);print_r($tariffdetails);print_r($tariffdetails_arr);
	        for ($i=0; $i < sizeof($tariffdetails); $i++) {
	            $tariffdata['client_id'] = $tariffdetails[$i]['client_id'];
	            $tariffdata['client_name'] = $tariffdetails[$i]['client_name'];
	            $tariffdata['delivery_id'] = $tariffdetails[$i]['delivery_id'];
	            $tariffdata['delivery_name'] = $tariffdetails[$i]['delivery_name'];
	            $tariffdata['articles_count'] = $tariffdetails[$i]['articles_count'];
	            $tariffdata['avg_price_per_word'] = $tariffdetails[$i]['avg_price_per_word'];
	            $tariffdata['min_price'] = $tariffdetails[$i]['min_price'];
	            $tariffdata['max_price'] = $tariffdetails[$i]['max_price'];
	            $tariffdata['contrib_price_sum'] = $tariffdetails[$i]['contrib_price_sum'];
	            $tariffdata['article_total_price_sum'] = $tariffdetails[$i]['article_total_price_sum'];
	            $tariffdata['delivery_time'] = $tariffdetails[$i]['delivery_time'];
	            $tariffdata['delivery_time_option'] = $tariffdetails[$i]['delivery_time_option'];
				
				if(in_array($tariffdetails[$i]['id'], $tariffdetails_arr) && ($tariffdetails[$i]['id']))
				{   //exit('<br>#1: '.$id);
					$tariffdatawhere  =   " id='".$tariffdetails[$i]['id']."' AND tariff_id='".$id."'" ;
		            $this->updateQuery($tariffdata, $tariffdatawhere) ;
                    $tariffdetails_ids_new[] = $tariffdetails[$i]['id'];
				}
				else if($tariffdata['contrib_price_sum'] > 0)
	            {
			        $this->createIdentifier() ;
			        $tariffdata['id']    =    $this->getIdentifier() ;
	            	$tariffdata['tariff_id'] = $id;   //print_r($tariffdata);exit('<br>#2: '.$id.$this->_name);
	            	$this->insertQuery($tariffdata) ;
                    $tariffdetails_ids_new[] = $tariffdata['id'];
	            }
	            unset($tariffdata);
	        }
			$tariffdetails_ids_new = array_unique(array_filter($tariffdetails_ids_new));
			$tariffdataIds = implode(',', $tariffdetails_ids_new);

            if(!empty($tariffdataIds))
            {
                //exit('#2:'." tariff_id = '".$id."' AND id NOT IN (".$tariffdataIds.")");
                $this->deleteQuery(" tariff_id = '".$id."' AND id NOT IN (".$tariffdataIds.")") ;
            }
            else
            {
                //exit('#1:'." tariff_id = '".$id."'");
                $this->deleteQuery(" tariff_id = '".$id."'") ;
            } 
        }
		else {
			$this->deleteQuery(" tariff_id = '".$id."'") ;
		}
    }
    public function deleteTariff($id)
    {
        $this->_name = 'tariff' ;
        $this->deleteQuery(" id = '".$id."'") ;
        $this->_name = 'tariffdetails' ;
        $this->deleteQuery(" tariff_id = '".$id."'") ;
    }
    public function deleteTariffData($tid)
    {
        $this->_name = 'tariffdetails' ;
        $this->deleteQuery(" id = '".$tid."'") ;
    }
    public function listTariffs($type)
    {
        return $this->getQuery("SELECT * FROM ".$this->_name." WHERE type='$type'",true) ;
    }
    public function getTariff($id)
    {
        $result1 =   $this->getQuery("SELECT * FROM ".$this->_name." WHERE id='$id'",true) ;
        $result2 =   $this->getQuery("SELECT * FROM tariffdetails WHERE tariff_id='$id' ORDER BY id DESC",true) ;
        return array($result1[0], $result2);
    }
    public function getTariffColumn($column)
    {
        return $this->getQuery("SELECT $column FROM ".$this->_name,true) ;
    }
    //////////////
    public function getPrices($col, $val)
    {
        $this->_name = 'Article' ;
        $m1 = date("Y-m-d", strtotime( date( 'Y-m-d' )." -1 months"));
        $m3 = date("Y-m-d", strtotime( date( 'Y-m-d' )." -3 months"));//
        
        $query = "SELECT count(*) AS count, AVG(num_min) as min_sum, AVG(num_max) as max_sum, AVG(price_final) as price_final FROM ".$this->_name." WHERE $col ='$val'" ;
        
        $results =   $this->getQuery($query, true) ;
        if($results[0]['count']>0)
        {
            $return['m']   =   ($results[0]['price_final'] / (($results[0]['min_sum'] + $results[0]['max_sum'])/2)) ;
        }
        
        $results =   $this->getQuery($query . " AND created_at > '$m1'",true) ;
        if($results[0]['count']>0)
        {
            $return['m1']   =   ($results[0]['price_final'] / (($results[0]['min_sum'] + $results[0]['max_sum'])/2)) ;
        }

        $results =   $this->getQuery($query . " AND created_at > '$m3'",true) ;
        if($results[0]['count']>0)
        {
            $return['m3']   =   ($results[0]['price_final'] / (($results[0]['min_sum'] + $results[0]['max_sum'])/2)) ;
        }
        //echo '<pre>';print_r($return);exit;
    	return $return ? $return : false;
    }
    public function getAoArticlesInfo($id)
    {
        $query1 =   "SELECT count(*) AS articles_count, AVG(a.num_min) AS min_wrds, AVG(a.num_max) AS max_wrds, AVG(a.price_final) AS avg_price_final, SUM(a.price_final) AS sum_price_final, d.price_min AS price_min_d, d.price_max AS price_max_d, d.created_at AS creation_date FROM Delivery d RIGHT JOIN Article a ON d.id = a.delivery_id WHERE d.id='$id' GROUP BY a.delivery_id" ;
        
        $query2 =   "SELECT SUM(p.price_user) AS price_p FROM Participation p WHERE p.article_id IN (SELECT id FROM Article WHERE delivery_id='$id') AND p.status IN('bid', 'time_out', 'under_study', 'disapproved', 'disapproved_temp', 'closed_temp', 'plag_exec', 'published')" ;
        
        $query3 =   "SELECT AVG(p.price_user) AS price_b FROM Participation p WHERE p.article_id IN (SELECT id FROM Article WHERE delivery_id='$id') AND p.status IN('bid_premium','bid_nonpremium') GROUP BY p.article_id" ;
        
        $query4 =   "SELECT count(*) AS published_count, max(updated_at) AS latest_published_date FROM Participation p WHERE p.article_id IN (SELECT id FROM Article WHERE delivery_id='$id') AND p.status IN('published') GROUP BY p.article_id" ;
        
        //exit($query1 . ' # '.$query2 . ' # '.$query3);35
        $results1 = $this->getQuery($query1,true) ;
        $results2 = $this->getQuery($query2,true) ;
        $results3 = $this->getQuery($query3,true) ;
        $results4 = $this->getQuery($query4,true) ;
        
        $return['articles_count'] = $results1[0]['articles_count'];
        @$return['avg_price_per_word'] = number_format(($results1[0]['avg_price_final'] / (($results1[0]['min_wrds']+$results1[0]['max_wrds'])/2)), 2, '.', '') + 0;
        $avg_words = intval(($results1[0]['min_wrds'] + $results1[0]['max_wrds'])/2);
        $return['avg_price_per_word_text'] = $results1[0]['avg_price_final'] . '&#8364; / ' . $avg_words . ' words' ;
        $return['min_price_d'] = number_format($results1[0]['price_min_d'], 2, '.', '') + 0;
        $return['max_price_d'] = number_format($results1[0]['price_max_d'], 2, '.', '') + 0;
        $return['writer_price_sum'] = $results2[0]['price_p'];
        foreach ($results3 as $key => $value) {
            $return['writer_price_sum'] += $value['price_b'];
        }
        $return['writer_price_sum'] = number_format($return['writer_price_sum'], 2, '.', '') + 0;
        $return['sum_price_final'] = number_format($results1[0]['sum_price_final'], 2, '.', '') + 0;
        //if($results1['articles_count']==$results4['published_count'])
        $published_date = ($results1[0]['articles_count']==$results4[0]['published_count']) ? $results4[0]['latest_published_date'] : date('Y-m-d H:i:s');
        $creation_date = $results1[0]['creation_date'];
        //exit($creation_date.'#'.$published_date.'<br>'.date('Y-m-d H:i:s').'<br>'.$results1[0]['articles_count'].'|'.$results4[0]['published_count']);
        $delivery_time_sec = strtotime($published_date) - strtotime($creation_date);
        $return['delivery_time_hrs'] = (intval($delivery_time_sec / 60 /  60) > 0) ? intval($delivery_time_sec / 60 /  60) : '' ;
        $return['delivery_time_days'] = (intval($delivery_time_sec / 60 /  60 / 24) > 0) ? intval($delivery_time_sec / 60 /  60 / 24) : '' ;
        $return['writer_price_sum'] = $results2[0]['price_p'];
        //echo '<pre>';print_r($return);print_r($results1);print_r($results2);print_r($results3);print_r($results4);exit('#');
        
        return $return;
        
    }
    
    public function getAOlist($clnt=NULL,$cat=NULL,$lang=NULL,$paid=NULL)
    {
        if($clnt)
            $where.=" AND d.user_id='".$clnt."'";
        if($paid)
            $where.=" AND p.status='Paid'";
        if($cat!='')
            $where.=" AND d.category='".$cat."'";
        if($lang!='')
            $where.=" AND d.language='".$lang."'";
        if($where)
            $where=" WHERE 1=1 ".$where;

        $SelQuery="SELECT d.id,d.title FROM Delivery d LEFT JOIN Payment p ON d.id=p.delivery_id $where ";
        //exit($SelQuery);
        $resultamt = $this->getQuery($SelQuery,true);
        return $resultamt;
    }
}	