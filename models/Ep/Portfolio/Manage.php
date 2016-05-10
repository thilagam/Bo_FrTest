<?php

class Ep_Portfolio_Manage extends Ep_Db_Identifier
{
    public function searchPortfolio($params,$type){
        $langauge = ($params['langauge']) ? 'AND C.`language` IN ( \'' . implode("','", $params['langauge']) . '\')' : '';

        $condition = $langauge.' ';
        if( in_array('writer',$params['type'])  ) { //|| !in_array('writer',$params['type'])//
            $status = ($params['status']) ? 'AND U.`profile_type` IN ( \'' . implode("','", $params['status']) . '\')' : '';
            $condition .= ' '.$status;
        }
        if(in_array('corrector',$params['type'])){
            $status = ($params['status']) ? ' AND U.`profile_type` IN ( \'' . implode("','", $params['status']) . '\')' : '';

            $type2  = "AND type2 IN ('corrector')";
            $condition .= ' '.$status.' '.$type2;
        }
        if(in_array('translator',$params['type']) ){

            $translator = ($params['status']) ? ' AND C.`translator` = \'yes\' AND C.`translator_type` IN ( \'' . implode("','", $params['status']) . '\')' : '';
            $condition .= ' '.$translator;
        }
        if(in_array('corrector-translator',$params['type'])){
            $translator = ($params['status']) ? ' AND C.`translator` = \'yes\' AND C.`translator_type` IN ( \'' . implode("','", $params['status']) . '\')' : '';
            $type2  = "AND type2 IN ('corrector')";
            $condition .= ' '.$translator.' '.$type2;
        }
        if((in_array('translator',$params['type']) || in_array('corrector-translator',$params['type'])) && $params['language_more']){
            $language_more = '';
            $i= 0;//temperary flag sake//
            foreach($params['language_more'] as $lang_more){
                if($i === 0)
                    $language_more .= "  language_more LIKE '%\"$lang_more\"%' ";
                else
                    $language_more .= " OR language_more LIKE '%\"$lang_more\"%' ";
                $i++;
            }
            $condition .= " AND ($language_more)";
        }
        if($params['favcontribcheck']){
            $condition .= ' AND U.`identifier` IN ( \'' . implode("','", $params['favcontribcheck']) . '\')';
        }
        if($params['client_list'] || $params['article_type'] || $params['category_list']){
            $condition2 = '';
            if($params['client_list']) {
                $condition2 .= ' AND U1.`identifier` IN ( \'' . implode("','", $params['client_list']) . '\')';
            }
            if($params['article_type']){
                $condition2 .= ($params['article_type']) ? ' AND A1.`type` IN ( \'' . implode("','", $params['article_type']) . '\')' : '';

            }
            if($params['category_list']){
                $condition2 .= ' AND A1.`category` IN ( \'' . implode("','", $params['category_list']) . '\')';
            }
            $query1 = "
                        SELECT  DISTINCT P1.`user_id`
                        FROM `User` AS U1
                        INNER JOIN `Delivery` AS D1 ON D1.`user_id` = U1.`identifier`
                        INNER JOIN `Article` AS A1 ON A1.`delivery_id` = D1.`id`
                        INNER JOIN `Participation` AS P1 ON P1.`article_id` = A1.`id`
                        WHERE
                        P1.`status`
                        IN (
                            'under_study', 'published'
                        )
                        $condition2
                        ";
//            echo $query1;exit;
            if (($result1 = $this->getQuery($query1, true)) != NULL) {
                foreach ($result1 as &$value) $value = $value['user_id'];// converting two dimentional array to single dimentional array//
                $condition .= ' AND U.`identifier` IN ( \'' . implode("','", $result1 ) . '\')';
            }
        }
        if($type == 'initial_load'){
            $rowsToFetch = "(@cnt := if(@cnt IS NULL, 0,  @cnt) + 1) AS ind,
                    initial,CONCAT(UP.first_name,' ',UP.last_name) AS full_name,UP.address,UP.city,UP.state,UP.zipcode,UP.country,UP.phone_number,
                   U.`identifier` as id,U.`identifier`,U.email,U.profile_type,U.type2,U.profile_type2,C.`language`,C.`translator`,C.`translator_type`,
                   (
                   SELECT  count(P1.`user_id`) AS u_id FROM `Participation` AS P1
                        WHERE P1.`status` IN ('published','under_study') AND  P1.`current_stage` NOT IN ('contributor') AND P1.`user_id` =   U.`identifier`
                    ) AS participation_wins";
            $orderby = "ORDER BY participation_wins DESC";
        }
        elseif($type == 'load_charts'){
            $rowsToFetch = "U.`identifier`,CONCAT(C.`language`,'_',U.`profile_type`) AS lang_profile_type,
                  CONCAT(C.`language`,'_',U.`profile_type2`) AS lang_profile_type2,
                  CONCAT(C.`language`,'_',C.`translator_type`) AS lang_translator_type,
                  CONCAT(C.`language`,'_',U.`profile_type2`,'_',C.`translator`) AS lang_translator_corrector_type,
                  (
                   SELECT  count(P1.`user_id`) AS u_id FROM `Participation` AS P1
                        WHERE P1.`status` IN ('bid') AND  P1.`current_stage` IN ('contributor') AND P1.`user_id` =   U.`identifier`
                    ) AS busy_status";
            $orderby = "";
        }
        $query = "SELECT
                  $rowsToFetch
                  FROM `User` AS U
                  LEFT JOIN `Contributor` AS C ON C.`user_id` = U.`identifier`
                  LEFT JOIN UserPlus AS UP ON UP.user_id =  U.`identifier`
                  WHERE

                  U.`type` = 'contributor' ".$condition."
                  GROUP BY U.`identifier`
                  $orderby";//U.`status` LIKE 'Active' AND blackstatus NOT IN ('yes') AND
//echo "<pre>$query</pre>";
            if (($result = $this->getQuery($query, true)) != NULL) {
                $count = count($result);
                for($i=0;$i<$count;$i++){
                    $result[$i]['index']= $i;
                }
               return $result;
            }
            else
                return false;

    }
    public function getPortfolio($params){
        $id = $params['id'];
        if($params['category_list']){
            $category_list =  ' AND A.`category` IN ( \'' . implode("','", $params['category_list']) . '\')';
        }
        $query = "SELECT initial,CONCAT(UP.first_name,' ',UP.last_name) AS full_name,UP.first_name,UP.last_name,UP.address,UP.city,UP.state,UP.zipcode,UP.country,UP.phone_number,
                  U.*, DATE_FORMAT(U.created_at,'%Y') AS joined_since,
                  C.`language`,C.language_more,C.category_more,C.`translator`,C.`translator_type`,C.`self_details`,
                    (
                      SELECT count(*) FROM `Participation`
                      WHERE `status` IN ('under_study','published') AND `user_id` = '".$id."'
                    ) AS articale_written,
                    (
                        SELECT COUNT(DISTINCT U.`identifier`)
                        FROM `User` AS U
                        INNER JOIN `Delivery` AS D ON D.`user_id` = U.`identifier`
                        INNER JOIN `Article` AS A ON A.`delivery_id` = D.`id`
                        INNER JOIN `Participation` AS P ON P.`article_id` = A.`id`
                        WHERE P.`status`
                        IN (
                        'under_study', 'published'
                        )
                        AND P.`user_id` = '".$id."'
                    ) AS total_client,
                    (
                        SELECT GROUP_CONCAT(DISTINCT(U.`identifier`) separator ', ')
                        FROM `User` AS U
                        INNER JOIN `Delivery` AS D ON D.`user_id` = U.`identifier`
                        INNER JOIN `Article` AS A ON A.`delivery_id` = D.`id`
                        INNER JOIN `Participation` AS P ON P.`article_id` = A.`id`
                        WHERE P.`status`
                        IN (
                        'under_study', 'published'
                        )
                        AND P.`user_id` = '".$id."'
                        $category_list
                    ) AS random_client,
                    (
                        SELECT GROUP_CONCAT(DISTINCT(CONCAT(U.`identifier`,'--',C.`company_name`)) separator ', ')

                        FROM `User` AS U
                        INNER JOIN `Delivery` AS D ON D.`user_id` = U.`identifier`
                        INNER JOIN `Article` AS A ON A.`delivery_id` = D.`id`
                        INNER JOIN `Participation` AS P ON P.`article_id` = A.`id`
                        LEFT JOIN  `Client` AS C ON U.`identifier` = C.`user_id`
                        WHERE P.`status`
                        IN (
                        'under_study', 'published'
                        )
                        AND P.`user_id` = '".$id."'
                        $category_list
                    ) AS random_name,
                    (
                       SELECT GROUP_CONCAT(DISTINCT(A.`type`) separator ', ')
                        FROM `User` AS U
                        INNER JOIN `Delivery` AS D ON D.`user_id` = U.`identifier`
                        INNER JOIN `Article` AS A ON A.`delivery_id` = D.`id`
                        INNER JOIN `Participation` AS P ON P.`article_id` = A.`id`
                        WHERE P.`status`
                        IN (
                        'under_study', 'published'
                        )
                        AND P.`user_id` = '".$id."'
                    ) AS article_type
                  FROM `User` AS U
                  LEFT JOIN `Contributor` AS C ON C.`user_id` = U.`identifier`
                  LEFT JOIN UserPlus AS UP ON UP.user_id =  U.`identifier`
                  WHERE  U.`type` = 'contributor'
                  AND U.identifier = '".$id."' ";
//        echo "<pre>".$query;exit;
        if (($result = $this->getQuery($query, true)) != NULL) {
            //convert all neccessary fields to array before returning//
            $result[0]['language_more'] = unserialize($result[0]['language_more']);
            $result[0]['category_more'] = unserialize($result[0]['category_more']);
            $result[0]['random_client'] = ($result[0]['random_client'] != NULL) ? explode( ',',preg_replace('/\s+/','',$result[0]['random_client']) ) : array();
            $result[0]['random_name'] = ($result[0]['random_name'] != NULL) ? explode( ',',preg_replace('/\s+/','',$result[0]['random_name']) ) : array();
            //for loop to trim identifier and only have company name in the array//
            for($i=0;$i<count($result[0]['random_name']);$i++){
                $value = $result[0]['random_name'][$i];
                $valuearray = explode('--',$value);
                $result[0]['random_name'][$i] = $valuearray[1];
            }
            $result[0]['article_type'] = ($result[0]['article_type'] != NULL) ? explode( ',',preg_replace('/\s+/','',$result[0]['article_type']) ) : array();
            return $result;
        }
        else
            return false;
    }
    public function getJobs($id){
        $query = "SELECT * FROM `ContributorExperience` WHERE user_id = '$id' AND type = 'job'";
//        echo $query;exit;
        if (($result = $this->getQuery($query, true)) != NULL) {
            return $result;
        }
        else
            return false;
    }
    //function ot get latest 3 articles related to contrib and client//
    public function getLastestArticles($params){
        $query = "SELECT *,(SELECT company_name FROM Client WHERE user_id= '".$params['clinetId']."') as company_name
                    FROM `ArticleProcess`
                    WHERE participate_id
                    IN (
                        SELECT P.`id`
                        FROM `Delivery` AS D
                        INNER JOIN `Article` AS A ON A.`delivery_id` = D.`id`
                        INNER JOIN `Participation` AS P ON P.`article_id` = A.`id`
                        WHERE P.`status` = 'published'
                        AND P.`current_stage` = 'client'
                        AND P.`user_id` = '".$params['contribId']."'
                        AND D.`user_id` = '".$params['clinetId']."'
                    )
                    AND stage = 's2'
                    AND STATUS = 'approved'
                    ORDER BY `ArticleProcess`.`article_sent_at` DESC
                    LIMIT 0 , 3";
//        echo $query;exit;
        if (($result = $this->getQuery($query, true)) != NULL) {
            return $result;
        }
        else
            return false;
    }
    //Client List based on Invoice Flag . if True Then get Client Else Dont
    public function getclientInvoicedList()
    {
        $Query = "select u.identifier, u.email,c.company_name,up.first_name,up.last_name
        from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Client c ON u.identifier=c.user_id
		where u.type='client' AND u.email NOT LIKE '%_new%'
        AND (SELECT count(*) FROM ClientContacts cc1 WHERE cc1.client_id = u.identifier) > 0
        ORDER BY c.company_name";
        //u.email condition added by arun
        //AND u.invoiced = 'yes'
        if(($result = $this->getQuery($Query,true)) != NULL){
            $client_list=array();

            foreach ($result as $key => $value) {
                if($value['company_name'] !== '')
                {
                    if( $value['first_name'] !== '' && $value['last_name'] !== ''  &&  $value['first_name'] !== NULL && $value['last_name'] !== NULL  )
                        $value['$name'] =  '('.$value['first_name'].' '.$value['last_name'].')';
                    else
                        $value['$name'] = '';
                    $client_list[$value['identifier']] = strtoupper($value['company_name'].$value['$name']);
                }
                elseif( $value['first_name'] !== '' && $value['last_name'] !== ''  &&  $value['first_name'] !== NULL && $value['last_name'] !== NULL  ){
                    $client_list[$value['identifier']] = strtoupper($value['first_name'].' '.$value['last_name']);
                }
                else{
                    $client_list[$value['identifier']] = strtoupper($value['email']);
                }

            }
            $premium_client_list = $client_list;
            //fetch non premium client//
            $Query = "select u.identifier, u.email,c.company_name,up.first_name,up.last_name
                from User u
                LEFT JOIN UserPlus up ON u.identifier=up.user_id
                LEFT JOIN Client c ON u.identifier=c.user_id
                where u.type='client' AND u.email NOT LIKE '%_new%'
                AND (SELECT count(*) FROM ClientContacts cc1 WHERE cc1.client_id = u.identifier) = 0
                ORDER BY
                CASE WHEN c.company_name IS NULL OR c.company_name = '' THEN 1 ELSE 0 END,  c.company_name";
            if(($result = $this->getQuery($Query,true)) != NULL) {
                $client_list = array();

                foreach ($result as $key => $value) {
                    if($value['company_name'] !== '')
                    {
                        if( $value['first_name'] !== '' && $value['last_name'] !== ''  &&  $value['first_name'] !== NULL && $value['last_name'] !== NULL  )
                            $value['$name'] =  '('.$value['first_name'].' '.$value['last_name'].')';
                        else
                            $value['$name'] = '';
                            $client_list[$value['identifier']] = strtoupper($value['company_name'].$value['$name']);
                    }
                    elseif( $value['first_name'] !== '' && $value['last_name'] !== ''  &&  $value['first_name'] !== NULL && $value['last_name'] !== NULL  ){
                        $client_list[$value['identifier']] = strtoupper($value['first_name'].' '.$value['last_name']);
                    }
                    else{
                            $client_list[$value['identifier']] = strtoupper($value['email']);
                    }

                }
                $non_premium_client_list = $client_list;
                $final_client_list = array_merge($premium_client_list, $non_premium_client_list);
            }
            return $final_client_list;
        }else{
            return "NO";
        }
    }
    public function getTrendAnalysis(){
        $sub_result =  $this->getQuery("SELECT GROUP_CONCAT( `quoteid` SEPARATOR ',' ) AS sub_condition FROM `QuoteContracts`", true);
        $sub_condition = trim($sub_result[0]['sub_condition'],',');
        $query = "SELECT
                    (SUM(P.staff*(IF(Q.estimate_sign_percentage IS NULL,100,Q.estimate_sign_percentage))/100 )) AS staff_req,
                    SUM(P.staff ) AS staff,
                    AVG(IF(Q.estimate_sign_percentage IS NULL,100,Q.estimate_sign_percentage)) AS percentage,
                    P.product,
                    IF(P.product= 'translation', language_dest, language_source) AS language
                    FROM `Quotes` AS Q
                    JOIN  `QuoteMissions` AS QM  ON  QM.quote_id = Q.`identifier`
                    LEFT JOIN `ProdMissions` AS P ON P.`quote_mission_id` = QM.`identifier`
                    WHERE Q.sales_review IN ('validated','signed')
                    AND P.product IN ('translation','proofreading','redaction')
                    AND Q.`identifier` NOT IN ( ".$sub_condition." )
                    GROUP BY language,product";
        //echo $query;exit;
        if (($result = $this->getQuery($query, true)) != NULL) {
            return $result;
        }
        else
            return false;
    }
}
