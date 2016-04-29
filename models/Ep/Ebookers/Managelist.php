<?php

class Ep_Ebookers_Managelist extends Ep_Db_Identifier
{
    protected $_name = 'EB_themes';
    public function fecthThemesOption(){
        $query = "SELECT `theme_id`,`theme_name` FROM `EB_themes` WHERE `status` = 'active'";
        if (($result = $this->getQuery($query, true)) != NULL) {
            $options = '';
            $i=0;
            while($result[$i]) {
                $options .= '<option value="' . $result[$i]['theme_id'] . '">' . $result[$i]['theme_name'] . '</option>';
                $i++;
            }
           return $options;
        }else
            return false;
    }
    public function fecthCategoryOption(){
        $query = "SELECT `cat_id`,`category_name` FROM `EB_category` WHERE `status` = 'active'";
        if (($result = $this->getQuery($query, true)) != NULL){
            $options = '';
            $i=0;
            while($result[$i]) {
                $options .= '<option value="' . $result[$i]['cat_id'] . '">' . $result[$i]['category_name'] . '</option>';
                $i++;
            }
            return $options;
        }
        else
            return false;
    }
    //one function to insert in  EB_category,EB_sampletext, EB_themes, EB_token table//
    public function updateManagelist($data){
        //if con. to insert in themes table
        if($data['submit'] == 'themes_submit') {
                $this->_name = 'EB_themes';
                $data['theme_name'] = utf8_decode($data['theme_name']);
                $data['description'] = utf8_decode($data['description']);
                unset($data['submit']);//delete the unwanted array before insertation
                $this->insertQuery($data);//directly sending the requet array->$data to avoid using variables
        }
        //to insert in category table
        elseif($data['submit'] == 'category_submit') {
            $this->_name = 'EB_category';
            $insertdata['category_name'] = utf8_decode($data['category_name']);
            $insertdata['description'] = utf8_decode($data['description']);
            if(is_array($data['themes_id'])){
                $j=0;
                while($data['themes_id'][$j]){
                    $insertdata['themes_id'] = $data['themes_id'][$j];
                    $this->insertQuery($insertdata);
                    $j++;
                }
            }
        }
        elseif($data['submit'] == 'tokens_submit') {
            $this->_name = 'EB_token';
            for($i=0;$i<$data['no_tokens'];$i++){
                $insertdata['category_id'] = $data['category_id'];
                $insertdata['token_name'] =utf8_decode($data['token_name_'.$i]);
                $insertdata['token_code'] =utf8_decode($data['token_code_'.$i]);
                $insertdata['description'] =utf8_decode($data['description_'.$i]);
                $insertdata['token_type'] = $data['token_type_'.$i];
                if(is_array($data['category_id'])){
                    $j=0;
                    while($data['category_id'][$j]){
                        $insertdata['category_id'] = $data['category_id'][$j];
                        $this->insertQuery($insertdata);
                        $j++;
                    }
                }
            }
        }
        elseif($data['submit'] == 'sample_text_submit') {
            $this->_name = 'EB_sampletext';
            unset($data['submit']);//delete the unwanted array before insertation
            unset($data['themes_id']);//delete the unwanted array before insertation
            for($i=0;$i<$data['no_sampple_text'];$i++){
                $insertdata['description'] = utf8_decode($data['description_'.$i]);
                if(is_array($data['category_id'])){
                    $j=0;
                    while($data['category_id'][$j]){
                        $insertdata['category_id'] = $data['category_id'][$j];
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
    public function loadCategory(){
        $query = "SELECT cat . * , theme.`theme_name` , @curRow := @curRow +1 AS sl_no
                FROM `EB_category` AS cat
                LEFT JOIN `EB_themes` AS theme ON cat.`themes_id` = theme.`theme_id`
                JOIN (
                  SELECT @curRow :=0
                )r
                WHERE cat.`status` = 'active'";//used "@curRow :=0" for an extra row as SL.NO
        if (($result = $this->getQuery($query, true)) != NULL) {
            $i = 0;
            while($result[$i]){
                $result[$i]['category_name'] = utf8_encode($result[$i]['category_name']);
                $result[$i]['theme_name'] = utf8_encode($result[$i]['theme_name']);
                $result[$i]['description'] = utf8_encode($result[$i]['description']);
                $i++;
            }
           return $result;
        }
        else
            return false;
    }
    // to load the all the List of tokens and display in the datatable//
    public function loadTokens(){
        $query = "SELECT token. * ,cat.`themes_id`,  cat.`category_name` ,themes.`theme_name`,
                @curRow := @curRow +1 AS sl_no
                FROM `EB_token` AS token
                LEFT JOIN `EB_category` AS cat ON token.`category_id` = cat.`cat_id`
                LEFT JOIN `EB_themes` AS themes ON cat.`themes_id` = themes.`theme_id`
                JOIN (

                SELECT @curRow :=0
                )r
                WHERE token.`status` = 'active'";//used "@curRow :=0" for an extra row as SL.NO
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
    // to load the all the List of sample_text and display in the datatable//
    public function loadSampletext(){
        $query = "SELECT sampletext. * , cat.`category_name` ,themes.`theme_name`,
                @curRow := @curRow +1 AS sl_no
                FROM `EB_sampletext` AS sampletext
                LEFT JOIN `EB_category` AS cat ON sampletext.`category_id` = cat.`cat_id`
                LEFT JOIN `EB_themes` AS themes ON cat.`themes_id` = themes.`theme_id`
                JOIN (

                SELECT @curRow :=0
                )r
                WHERE sampletext.`status` = 'active'";//used "@curRow :=0" for an extra row as SL.NO
        if (($result = $this->getQuery($query, true)) != NULL) {
            $i = 0;
            while($result[$i]){
                $result[$i]['category_name'] = utf8_encode($result[$i]['category_name']);
                $result[$i]['description'] = utf8_encode($result[$i]['description']);
                $result[$i]['theme_name'] = utf8_encode($result[$i]['theme_name']);
                $i++;
            }
           return $result;
        }
        else
            return false;
    }
    //to chanage the status from active to delete in  EB_category,EB_sampletext, EB_themes, EB_token table//
    public function deleteList($data){
        if($data['type'] == 'region') {
            $this->_name = 'BNP_region';
            $where = " region_id='" . $data['id'] . "' ";
            $deletedata['status'] = 'deleted';
        }
        //to delete in category table
        elseif($data['type'] == 'category') {
            $this->_name = 'EB_category';
            $where = " cat_id='" . $data['id'] . "' ";
            $deletedata['status'] = 'deleted';
        }
        elseif($data['type'] == 'tokens') {
            $this->_name = 'EB_token';
            $where = " token_id='" . $data['id'] . "' ";
            $deletedata['status'] = 'deleted';
        }
        elseif($data['type'] == 'sample_text') {
            $this->_name = 'EB_sampletext';
            $where = " sample_id='" . $data['id'] . "' ";
            $deletedata['status'] = 'deleted';
        }
        $this->updateQuery($deletedata, $where);
        print_r($data);
    }
    //to fetch data from  EB_category,EB_sampletext, EB_themes, EB_token table and display into a form for editing//
    public function viewEditList($data){
        if($data['type'] == 'themes') {
            $query = "SELECT * FROM `EB_themes` WHERE `theme_id` = '".$data['id']."' ";
            if (($result = $this->getQuery($query, true)) != NULL) {
                $i = 0;
                while($result[$i]){
                    $result[$i]['type'] = $data['type'];
                    $result[$i]['theme_name'] = utf8_encode($result[$i]['theme_name']);
                    $result[$i]['description'] = utf8_encode($result[$i]['description']);
                    $i++;
                }
                return $result;

            }
            else
                return false;
        }
        elseif($data['type'] == 'category') {
            $query = "SELECT cat.* , theme.`theme_name`
                FROM `EB_category` AS cat
                LEFT JOIN `EB_themes` AS theme ON cat.`themes_id` = theme.`theme_id`
                WHERE
                cat.`cat_id` = '".$data['id']."' ";
            if (($result = $this->getQuery($query, true)) != NULL) {
                $i = 0;
                while($result[$i]){
                    $result[$i]['type'] = $data['type'];
                    $result[$i]['category_name'] = utf8_encode($result[$i]['category_name']);
                    $result[$i]['description'] = utf8_encode($result[$i]['description']);
                    $result[$i]['themes_option'] = $this->loadThemesOption($result);
                    $i++;
                }
                return $result;
            }
            else
                return false;
        }
        // this will display edit form for tokens//
        elseif($data['type'] == 'tokens') {
            $query = "SELECT token.* , cat.`category_name`, cat.`themes_id`
                FROM `EB_token` AS token
                LEFT JOIN `EB_category` AS cat ON token.`category_id` = cat.`cat_id`
                WHERE
                token.`token_id` = '".$data['id']."' ";
            if (($result = $this->getQuery($query, true)) != NULL) {
                $i = 0;
                while($result[$i]){
                    $result[$i]['type'] = $data['type'];
                    $result[$i]['token_name'] = utf8_encode($result[$i]['token_name']);
                    $result[$i]['token_code'] = utf8_encode($result[$i]['token_code']);
                    $result[$i]['description'] = utf8_encode($result[$i]['description']);
                    $result[$i]['themes_option'] = $this->loadThemesOption($result);
                    $result[$i]['category_option'] = $this->loadCategory2Option($result);
                    $i++;
                }
                return $result;
            }
            else
                return false;
        }
        elseif($data['type'] == 'sample_text') {
            $query = "SELECT sampletext.* , cat.`category_name`, cat.`themes_id`
                FROM `EB_sampletext` AS sampletext
                LEFT JOIN `EB_category` AS cat ON sampletext.`category_id` = cat.`cat_id`
                WHERE
                sampletext.`sample_id` = '".$data['id']."' ";
            if (($result = $this->getQuery($query, true)) != NULL) {
                $i = 0;
                while($result[$i]){
                    $result[$i]['type'] = $data['type'];
                    $result[$i]['description'] = utf8_encode($result[$i]['description']);
                    $result[$i]['themes_option'] = $this->loadThemesOption($result);
                    $result[$i]['category_option'] = $this->loadCategory2Option($result);
                    $i++;
                }
                return $result;
            }
            else
                return false;
        }
    }
    //to edit/update data in  EB_category,EB_sampletext, EB_themes, EB_token//
    public function editManagelist($data){
        if($data['type'] == 'themes') {
            $this->_name = 'EB_themes';
            $where = " theme_id='" . $data['id'] . "' ";
            $editdata['theme_name'] = utf8_decode($data['theme_name']) ;
            $editdata['description'] = utf8_decode($data['description']) ;
        }
        //to delete in category table
        elseif($data['type'] == 'category') {
            $this->_name = 'EB_category';
            $where = " cat_id='" . $data['id'] . "' ";
            $editdata['themes_id'] = utf8_decode($data['themes_id']) ;
            $editdata['category_name'] = utf8_decode($data['category_name']) ;
            $editdata['description'] = utf8_decode($data['description']) ;


        }
        elseif($data['type'] == 'tokens') {
            $this->_name = 'EB_token';
            $where = " token_id='" . $data['id'] . "' ";
            //$editdata['themes_id'] = utf8_decode($data['themes_id']) ;
            $editdata['category_id'] = utf8_decode($data['category_id']) ;
            $editdata['token_name'] = utf8_decode($data['token_name']) ;
            $editdata['token_code'] = utf8_decode($data['token_code']) ;
            $editdata['description'] = utf8_decode($data['description']) ;
            $editdata['token_type'] = $data['token_type'];
        }
        elseif($data['type'] == 'sample_text') {
            $this->_name = 'EB_sampletext';
            $where = " sample_id='" . $data['id'] . "' ";
            //$editdata['themes_id'] = utf8_decode($data['themes_id']) ;
            $editdata['category_id'] = utf8_decode($data['category_id']) ;
            $editdata['description'] = utf8_decode($data['description']) ;
        }
        $this->updateQuery($editdata, $where);
        print_r($editdata);
    }
    //to load theme options using ajax(everytime a new entry is made the theme option has to be updated)//
    public function loadThemesOption($data){
        $query = "SELECT `theme_id`,`theme_name` FROM `EB_themes` WHERE `status` = 'active' ";
        if (($result = $this->getQuery($query, true)) != NULL){
            $options = '';
            $i=0;
            $themes_id = $data[$i]['themes_id'];
            while($result[$i]){
                if($result[$i]['theme_id'] == $themes_id)
                    $options .= '<option value="' . $result[$i]['theme_id'] . '" selected>' . utf8_encode($result[$i]['theme_name']) . '</option>';
                else
                    $options .= '<option value="' . $result[$i]['theme_id'] . '">' . utf8_encode($result[$i]['theme_name']) . '</option>';
                $i++;
            }
            return $options;
        }
        else
            return false;
    }
    //to load category options  based on theme selection using ajax(everytime a theme is slecet and csubordinate category option has to be updated)//
    public function loadCategoryOption($data){
        $query = "SELECT `cat_id`,`category_name` FROM `EB_category` WHERE `themes_id` = '".$data['theme_id']."' AND `status` = 'active' ";
        if (($result = $this->getQuery($query, true)) != NULL){
            $options = '';
            $i=0;
            while($result[$i]) {
                $options .= '<option value="' . $result[$i]['cat_id'] . '">' . utf8_encode($result[$i]['category_name']) . '</option>';
                $i++;
            }
            return $options;
        }
        else
            return false;
    }
    //to load category options  based on theme selection using ajax(only during editing of tokens/sampletext)(everytime a theme is slecet and csubordinate category option has to be updated)//
    public function loadCategory2Option($data){
        $query = "SELECT `cat_id`,`category_name` FROM `EB_category` WHERE `themes_id` = '".$data[0]['themes_id']."' AND `status` = 'active' ";
        if (($result = $this->getQuery($query, true)) != NULL){
            $options = '';
            $i=0;
            $category_id = $data[$i]['category_id'];
            while($result[$i]) {
                if($result[$i]['cat_id'] == $category_id)
                    $options .= '<option value="' . $result[$i]['cat_id'] . '" selected>' . utf8_encode($result[$i]['category_name']) . '</option>';
                else
                    $options .= '<option value="' . $result[$i]['cat_id'] . '">' . utf8_encode($result[$i]['category_name']) . '</option>';
                $i++;
            }
            return $options;
        }
        else
            return false;
    }
}