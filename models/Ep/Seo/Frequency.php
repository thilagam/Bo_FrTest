<?php

/**
 * Profile Contributor Model
 * This Model  is responsible for Profile actions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class EP_Seo_Frequency extends Ep_Db_Identifier
{
	protected $_name = 'frequency';

	public function __set($name, $value) {
            $this->$name = $value;
    }

    public function __get($name){
            return $this->$name;
    }
    
    public function insertSchedule($frequency, $datas)
    {
        $frequencyData  =   $frequency['days'] ;
        $frequencyData['title']    =   $frequency['title'] ;
        $frequencyData['email']    =   $frequency['email'] ;
        $frequencyData['end_date']    =   $frequency['end_date'] ;
        $frequencyData['user_id']    =   $frequency['user_id'] ;
        $frequencyData['frequency_file']    =   $frequency['frequency_file'];
        
        $frequency_id = $this->insertQuery($frequencyData)  ;
        
        foreach($datas as $data)
        {
            $pg = $this->getQuery("SELECT id FROM pagerank where url='".$data[0]."' AND created_at LIKE '" . date('Y-m-d') . "%'") ;
            if(!$pg[0]->id)
            {
                $this->_name = 'pagerank' ;
                $pagerank['url'] = $data[0] ;
                $pagerank['pagerank'] = $data[1] ;
                $pagerank['created_at'] = $data[2] ;
                $pagerank_id = $this->insertQuery($pagerank) ;
            }
            else {
                $pagerank_id = $pg[0]->id  ;
            }
            $this->_name = 'frequency_process' ;
            $frequency_process['frequency_id'] = $frequency_id ;
            $frequency_process['url_id'] = $pagerank_id ;
            $this->insertQuery($frequency_process)  ;
        }
        //echo '<pre>'; print_r($frequencyData); print_r($datas); exit($frequency_id);
    }
    
    public function updatePRstatus($pageranks)
    {
        foreach($pageranks as $fid=>$pagerank)
        {
            foreach($pagerank as $pagerank_)
            {
                $pg = $this->getQuery("SELECT id FROM pagerank where url='".$pagerank_['url']."' AND created_at LIKE '" . date('Y-m-d') . "%'") ;
                if(!$pg[0]->id)
                {
                    $this->_name = 'pagerank' ;
                    $pr['url'] = $pagerank_['url'] ;
                    $pr['pagerank'] = $pagerank_['pagerank'] ;
                    $pr['created_at'] = $pagerank_['created_at'] ;
                    $fp['url_id'] = $this->insertQuery($pr) ;
                }
                else {
                    $fp['url_id'] = $pg[0]->id  ;
                }
                
                $fpQuery = $this->getQuery("SELECT id FROM frequency_process where frequency_id='" . $fid . "' AND url_id = '" . $fp['url_id'] . "'") ;
                if(!$fpQuery[0]->id)
                {
                    $this->_name = 'frequency_process' ;
                    $fp['frequency_id'] = $fid ;
                    $this->insertQuery($fp) ;
                }
            }
        }
    }
    
    public function updateNewPRstatus($pageranks)
    {
        foreach($pageranks as $fid=>$pagerank)
        {
            foreach($pagerank as $pagerank_)
            {
                $pg = $this->getQuery("SELECT id FROM pagerank where url='".$pagerank_['url']."' AND created_at LIKE '" . date('Y-m-d') . "%'") ;
                if(!$pg[0]->id)
                {
                $this->_name = 'pagerank' ;
                $pr['url'] = $pagerank_['url'] ;
                $pr['pagerank'] = $pagerank_['pagerank'] ;
                $pr['created_at'] = $pagerank_['created_at'] ;
                $fp['url_id'] = $this->insertQuery($pr) ;
                    
                }
                else {
                    $fp['url_id'] = $pg[0]->id  ;
                }
                
                $fpQuery = $this->getQuery("SELECT id FROM frequency_process where frequency_id='" . $fid . "' AND url_id = '" . $fp['url_id'] . "'") ;
                if(!$fpQuery[0]->id)
                {
                    $this->_name = 'frequency_process' ;
                    $fp['frequency_id'] = $fid ;
                    $this->insertQuery($fp) ;
                }
                
            }
        }
    }
    
    public function getSchedules()
    {
        $pr['n'] = $this->getQuery("SELECT id, frequency_file FROM frequency WHERE id NOT IN (SELECT DISTINCT frequency_id FROM frequency_process) AND id IN (SELECT id FROM frequency WHERE " . strtolower(date('l')) . "='1' AND end_date >= CURDATE())") ;
        
        foreach($this->getQuery("SELECT id FROM frequency f WHERE id IN (SELECT DISTINCT frequency_id FROM frequency_process) AND id IN (SELECT id FROM frequency WHERE " . strtolower(date('l')) . "='1' AND end_date >= CURDATE())") as $fid)
        {
            $pr['e'][$fid->id] = $this->getQuery("SELECT DISTINCT p.url FROM frequency_process fp INNER JOIN pagerank p ON fp.url_id=p.id WHERE fp.frequency_id='" . $fid->id . "'") ;
        }
        return $pr ;
    }
    
    public function getSchedulesToMail($frequencies)
    {
        return $this->getQuery("SELECT p.url, p.pagerank, p.created_at, fp.frequency_id FROM pagerank p INNER JOIN frequency_process fp ON fp.url_id=p.id WHERE fp.frequency_id IN (" . implode(",", $frequencies) . ") ORDER BY p.id DESC") ;
        //return $this->getQuery("SELECT p.url, p.pagerank, p.created_at, fp.frequency_id FROM pagerank p INNER JOIN frequency_process fp ON fp.url_id=p.id WHERE fp.frequency_id IN (" . implode(",", $frequencies) . ") GROUP BY p.url, DATE_FORMAT(p.created_at,'%Y-%m-%d') ORDER BY p.id DESC") ;
    }
    
    public function getFrequencyData($id)
    {
        return $this->getQuery("SELECT * FROM frequency WHERE id='" . $id . "'") ;
    }
    
    public function getFrequencyDates($id)
    {
        $process_dates = $this->getQuery("SELECT DATE_FORMAT(p.created_at,'%Y-%m-%d') as process_date FROM frequency_process fp INNER JOIN pagerank p ON fp.url_id=p.id WHERE fp.frequency_id='" . $id . "'") ;
        foreach($process_dates as $process_date)
        {
            $dates[] = $process_date->process_date ;
        }
        return array_values(array_unique($dates)) ;
    }
    
}
