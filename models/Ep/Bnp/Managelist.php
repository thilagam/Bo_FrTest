<?php

class Ep_Bnp_Managelist extends Ep_Db_Identifier
{
    public function fecthRegionOption(){
        $query = "SELECT `region_id`,`region_name` FROM `BNP_region` WHERE `status` = 'active'";
        if (($result = $this->getQuery($query, true)) != NULL) {
            $options = '';
            $i=0;
            while($result[$i]) {
                $options .= '<option value="' . $result[$i]['region_id'] . '">' . $result[$i]['region_name'] . '</option>';
                $i++;
            }
           return $options;
        }else
            return false;
    }
    public function fecthCityOption(){
        $query = "SELECT `city_id`,`city_name` FROM `BNP_city` WHERE `status` = 'active'";
        if (($result = $this->getQuery($query, true)) != NULL){
            $options = '';
            $i=0;
            while($result[$i]) {
                $options .= '<option value="' . $result[$i]['city_id'] . '">' . $result[$i]['city_name'] . '</option>';
                $i++;
            }
            return $options;
        }
        else
            return false;
    }
    //one function to insert in  EB_category,BNP_sampletext, EB_themes, EB_token table//
    public function updateManagelist($data){
        //if con. to insert in themes table
        if($data['submit'] == 'region_submit') {
                $this->_name = 'BNP_region';
                $data['region_name'] = utf8_decode($data['region_name']);
                $data['description'] = utf8_decode($data['description']);
                unset($data['submit']);//delete the unwanted array before insertation
                $this->insertQuery($data);//directly sending the requet array->$data to avoid using variables
        }
        //to insert in category table
        elseif($data['submit'] == 'city_submit') {
            $this->_name = 'BNP_city';
            $insertdata['city_name'] = utf8_decode($data['city_name']);
            $insertdata['description'] = utf8_decode($data['description']);
            if(is_array($data['region_id'])){
                $j=0;
                while($data['region_id'][$j]){
                    $insertdata['region_id'] = $data['region_id'][$j];
                    $this->insertQuery($insertdata);
                    $j++;
                }
            }
        }elseif($data['submit'] == 'sample_text_submit') {
            $this->_name = 'BNP_sampletext';
            unset($data['submit']);//delete the unwanted array before insertation
            unset($data['region_id']);//delete the unwanted array before insertation
            for($i=0;$i<$data['no_sample_text'];$i++){
                $insertdata['description'] = utf8_decode($data['description_'.$i]);
                if(is_array($data['city_id'])){
                    $j=0;
                    while($data['city_id'][$j]){
                        $insertdata['city_id'] = $data['city_id'][$j];
                        $this->insertQuery($insertdata);
                        $j++;
                    }
                }
            }
        }
        print_r($data);
    }
    public function loadManageList(){
        $query = "SELECT token. * ,cat.`themes_id`,  cat.`category_name` ,themes.`theme_name`,
                @curRow := @curRow +1 AS sl_no
                FROM `EB_token` AS token
                LEFT JOIN `EB_category` AS cat ON token.`category_id` = cat.`cat_id`
                LEFT JOIN `EB_themes` AS themes ON cat.`themes_id` = themes.`theme_id`
                JOIN (

                SELECT @curRow :=0
                )r
                WHERE token.`status` = 'active'
                ORDER BY themes.`theme_id`,cat.`cat_id` ";//used "@curRow :=0" for an extra row as SL.NO
        if (($result = $this->getQuery($query, true)) != NULL) {
            $i = 0;
            while($result[$i]){
                $result[$i]['theme_name'] = utf8_encode($result[$i]['theme_name']);
                $result[$i]['category_name'] = utf8_encode($result[$i]['category_name']);
                $result[$i]['token_code'] = utf8_encode($result[$i]['token_code']);;
                $result[$i]['token_name'] = utf8_encode($result[$i]['token_name']);
                $result[$i]['description'] = utf8_encode($result[$i]['description']);
                $i++;
            }
            return $result;
        }
        else
            return false;
    }
    // to load the all the List of themes and display in the datatable//
    public function loadRegion(){
        $query = "SELECT *,@curRow := @curRow + 1 AS sl_no FROM `BNP_region`
            JOIN    (SELECT @curRow := 0) r
             WHERE `status` = 'active' ";//used "@curRow :=0" for an extra row as SL.NO
        if (($result = $this->getQuery($query, true)) != NULL) {
            $i = 0;
            while($result[$i]){
                $result[$i]['region_name'] = utf8_encode($result[$i]['region_name']);
                $result[$i]['description'] = utf8_encode($result[$i]['description']);
                $i++;
            }
            return $result;
        }
        else
            return false;
    }
    // to load the all the List of category and display in the datatable//
    public function loadCity(){
        $query = "SELECT city. * , region .`region_name` , @curRow := @curRow +1 AS sl_no
                FROM `BNP_city` AS city
                LEFT JOIN `BNP_region` AS region ON city.`region_id` = region.`region_id`
                JOIN (
                  SELECT @curRow :=0
                )r
                WHERE city.`status` = 'active'";//used "@curRow :=0" for an extra row as SL.NO
        if (($result = $this->getQuery($query, true)) != NULL) {
            $i = 0;
            while($result[$i]){
                $result[$i]['city_name'] = utf8_encode($result[$i]['city_name']);
                //$result[$i]['region_name'] = utf8_encode($result[$i]['region_name']);
                $result[$i]['description'] = utf8_encode($result[$i]['description']);
                $i++;
            }
           return $result;
        }
        else
            return false;
    }
    // to load the all the List of sample_text and display in the datatable//
    public function loadSampletext(){
        $query = "SELECT sampletext. * , city.`city_name` ,region.`region_name`,
                @curRow := @curRow +1 AS sl_no
                FROM `BNP_sampletext` AS sampletext
                LEFT JOIN `BNP_city` AS city ON sampletext.`city_id` = city.`city_id`
                LEFT JOIN `BNP_region` AS region ON city.`region_id` = region.`region_id`
                JOIN (

                SELECT @curRow :=0
                )r
                WHERE sampletext.`status` = 'active'";//used "@curRow :=0" for an extra row as SL.NO
        if (($result = $this->getQuery($query, true)) != NULL) {
            $i = 0;
            while($result[$i]){
                $result[$i]['city_name'] = utf8_encode($result[$i]['city_name']);
                $result[$i]['description'] = utf8_encode($result[$i]['description']);
                //$result[$i]['region_name'] = utf8_encode($result[$i]['region_name']);
                $i++;
            }
           return $result;
        }
        else
            return false;
    }
    //to chanage the status from active to delete in  EB_category,BNP_sampletext, EB_themes, EB_token table//
    public function deleteList($data){
        if($data['type'] == 'region') {
            $this->_name = 'BNP_region';
            $where = " region_id='" . $data['id'] . "' ";
            $deletedata['status'] = 'deleted';
        }
        //to delete in category table
        elseif($data['type'] == 'city') {
            $this->_name = 'BNP_city';
            $where = " city_id='" . $data['id'] . "' ";
            $deletedata['status'] = 'deleted';
        }
        elseif($data['type'] == 'sample_text') {
            $this->_name = 'BNP_sampletext';
            $where = " sample_id='" . $data['id'] . "' ";
            $deletedata['status'] = 'deleted';
        }
        $this->updateQuery($deletedata, $where);
        print_r($data);
    }
    //to fetch data from  EB_category,BNP_sampletext, EB_themes, EB_token table and display into a form for editing//
    public function viewEditList($data){
        if($data['type'] == 'region') {
            $query = "SELECT * FROM `BNP_region` WHERE `region_id` = '".$data['id']."' ";
            if (($result = $this->getQuery($query, true)) != NULL) {
                $i = 0;
                while($result[$i]){
                    $result[$i]['type'] = $data['type'];
                    $result[$i]['region_name'] = utf8_encode($result[$i]['region_name']);
                    $result[$i]['description'] = utf8_encode($result[$i]['description']);
                    $i++;
                }
                return $result;

            }
            else
                return false;
        }
        elseif($data['type'] == 'city') {
            $query = "SELECT city.* , region.`region_name`
                FROM `BNP_city` AS city
                LEFT JOIN `BNP_region` AS region ON city.`region_id` = region.`region_id`
                WHERE
                city.`city_id` = '".$data['id']."' ";
            if (($result = $this->getQuery($query, true)) != NULL) {
                $i = 0;
                while($result[$i]){
                    $result[$i]['type'] = $data['type'];
                    $result[$i]['city_name'] = utf8_encode($result[$i]['city_name']);
                    $result[$i]['description'] = utf8_encode($result[$i]['description']);
                    $result[$i]['region_option'] = $this->loadRegionOption($result);
                    $i++;
                }
                return $result;
            }
            else
                return false;
        }
        elseif($data['type'] == 'sample_text') {
            $query = "SELECT sampletext.* , city.`city_name`, city.`region_id`
                FROM `BNP_sampletext` AS sampletext
                LEFT JOIN `BNP_city` AS city ON sampletext.`city_id` = city.`city_id`
                WHERE
                sampletext.`sample_id` = '".$data['id']."' ";
            if (($result = $this->getQuery($query, true)) != NULL) {
                $i = 0;
                while($result[$i]){
                    $result[$i]['type'] = $data['type'];
                    $result[$i]['description'] = utf8_encode($result[$i]['description']);
                    $result[$i]['region_option'] = $this->loadRegionOption($result);
                    $result[$i]['city_option'] = $this->loadCity2Option($result);
                    $i++;
                }
                return $result;
            }
            else
                return false;
        }
    }
    //to edit/update data in  EB_category,BNP_sampletext, EB_themes, EB_token//
    public function editManagelist($data){
        if($data['type'] == 'region') {
            $this->_name = 'BNP_region';
            $where = " region_id='" . $data['id'] . "' ";
            $editdata['region_name'] = utf8_decode($data['region_name']) ;
            $editdata['description'] = utf8_decode($data['description']) ;
        }
        //to delete in category table
        elseif($data['type'] == 'city') {
            $this->_name = 'BNP_city';
            $where = " city_id='" . $data['id'] . "' ";
            $editdata['region_id'] = utf8_decode($data['region_id']) ;
            $editdata['city_name'] = utf8_decode($data['city_name']) ;
            $editdata['description'] = utf8_decode($data['description']) ;
        }
        elseif($data['type'] == 'sample_text') {
            $this->_name = 'BNP_sampletext';
            $where = " sample_id='" . $data['id'] . "' ";
            //$editdata['themes_id'] = utf8_decode($data['themes_id']) ;
            $editdata['category_id'] = utf8_decode($data['category_id']) ;
            $editdata['description'] = utf8_decode($data['description']) ;
        }
        $this->updateQuery($editdata, $where);
        print_r($editdata);
    }
    //to load theme options using ajax(everytime a new entry is made the theme option has to be updated)//
    public function loadRegionOption($data){
        $query = "SELECT `region_id`,`region_name` FROM `BNP_region` WHERE `status` = 'active' ";
        if (($result = $this->getQuery($query, true)) != NULL){
            $options = '';
            $i=0;
            $region_id = $data[$i]['region_id'];
            while($result[$i]){
                if($result[$i]['region_id'] == $themes_id)
                    $options .= '<option value="' . $result[$i]['region_id'] . '" selected>' . utf8_encode($result[$i]['region_name']) . '</option>';
                else
                    $options .= '<option value="' . $result[$i]['region_id'] . '">' . utf8_encode($result[$i]['region_name']) . '</option>';
                $i++;
            }
            return $options;
        }
        else
            return false;
    }
    //to load category options  based on theme selection using ajax(everytime a theme is slecet and csubordinate category option has to be updated)//
    public function loadCityOption($data){
        $query = "SELECT `city_id`,`city_name` FROM `BNP_city` WHERE `region_id` = '".$data['region_id']."' AND `status` = 'active' ";
        if (($result = $this->getQuery($query, true)) != NULL){
            $options = '';
            $i=0;
            while($result[$i]) {
                $options .= '<option value="' . $result[$i]['city_id'] . '">' . utf8_encode($result[$i]['city_name']) . '</option>';
                $i++;
            }
            return $options;
        }
        else
            return false;
    }
    //to load category options  based on theme selection using ajax(only during editing of tokens/sampletext)(everytime a theme is slecet and csubordinate category option has to be updated)//
    public function loadCity2Option($data){
        $query = "SELECT `city_id`,`city_name` FROM `BNP_city` WHERE `region_id` = '".$data[0]['region_id']."' AND `status` = 'active' ";
        if (($result = $this->getQuery($query, true)) != NULL){
            $options = '';
            $i=0;
            $city_id = $data[$i]['city_id'];
            while($result[$i]) {
                if($result[$i]['city_id'] == $city_id)
                    $options .= '<option value="' . $result[$i]['city_id'] . '" selected>' . utf8_encode($result[$i]['city_name']) . '</option>';
                else
                    $options .= '<option value="' . $result[$i]['city_id'] . '">' . utf8_encode($result[$i]['city_name']) . '</option>';
                $i++;
            }
            return $options;
        }
        else
            return false;
    }
}