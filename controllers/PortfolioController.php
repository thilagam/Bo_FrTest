<?php

/**
 * BnpController - The controller class for Bnp main menu
 *
 * @author
 * @version
 */
class PortfolioController extends Ep_Controller_Action
{
	private $text_admin;
	public function init()
	{
		parent::init();
		$this->_view->lang = $this->_lang;
		$this->adminLogin = Zend_Registry::get('adminLogin');
        $this->_view->userId = $this->adminLogin->userId;
        $this->_view->user_type= $this->adminLogin->type ;
        $this->searchSession = Zend_Registry::get('searchSession');
        $this->sid = session_id();
        $this->commonAction();//////////including main menu and left panel content

        ////////////////////////////////////////////////////////////////////////////////
        $category=$this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        array_unshift($category, "S&eacute;lectionner");
        $this->_view->categories_array = $category;
		$nationality=$this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
         array_unshift($nationality, "S&eacute;lectionner");
        for($i=-1; $i<=count($nationality); $i++)
        {
            if($i == -1)
            {
                $nationality1[-1] = "S&eacute;lectionner";
            }
            $nationality1[$i] = $nationality[$i+1];
        }  //print_r($nationality1);
        $this->_view->nationality_array = $nationality1;
        $languages=$this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        asort($languages);
        array_unshift($languages, "S&eacute;lectionner");
        $this->_view->languages_array = $this->languages_array = $languages;

        //fetch all client list
        $client_obj = new Ep_Portfolio_Manage();
        $this->_view->client_invoiced=$client_obj->getclientInvoicedList();

        $del_obj = new Ep_Delivery_Delivery();
        $contriblistall=$del_obj->getAllContribAO(0);
        $this->_view->contrib_array=array();
        $contriblistall1=array();
        for ($i=0;$i<count($contriblistall);$i++)
        {
            $contriblistall1[]=$contriblistall[$i];
            $name=$contriblistall1[$i]['email'];
            $nameArr=array($contriblistall1[$i]['first_name'],$contriblistall1[$i]['last_name']);
            $nameArr=array_filter($nameArr);
            if(count($nameArr)>0)
                $name.=" (".implode(", ",$nameArr).")";
            $contriblistall1[$i]['name']=strtoupper($name);
        }
        $this->_view->contriblistall1=$contriblistall1;
        $this->_view->fo_path = "http://ep-test.edit-place.com/FO/";
        $this->_view->type_array = $this->_arrayDb->loadArrayv2("EP_ARTICLE_TYPE", $this->_lang);
    }
    public function homePageAction(){

            $this->_view->error = 'no';
            $this->render('portfolio-homepage');

    }
    public function searchPortfolioAction(){
        $params=$this->_request->getParams();
//            echo "<pre>";print_r($params);exit;
            //create object reqired classes
            $obj_manage = new Ep_Portfolio_Manage();
            $type = 'initial_load';
            $results = $obj_manage->searchPortfolio($params,$type);
            if ($results){//if match found redirect to search result page
                $countPerLang = $countPerStatus =$results;
                $countPerLangarray = $this->countPerLang($countPerLang);
                $this->_view->countPerLangTable = $this->countPerLangTable($countPerLangarray);
                //$this->_view->results = $results; //send full list to view
                //sending only specified number of list to view//;
                $_SESSION['results'] = $results;
                $_SESSION['start'] = $start = 0;
                $_SESSION['end'] = $_SESSION['interval'] = $end = $interval=  20;//change this to chnage the number of contributors to be loaded//
                $_SESSION['total'] = $total = count($results);
                $this->fetchBestImage($start,$interval);
                $this->_view->results = array_slice($_SESSION['results'], $_SESSION['start'], $_SESSION['end']);
                $this->_view->count_results = $total;
                $this->_view->url = 'https://secure.edit-place.fr';
                $this->_view->page = 'initial_load';//reference to page load of ajax
                $this->_view->call_type = 'non_ajax_call';//reference to page load of ajax
                $this->_view->params = $params;//params to display in the select fields
                $this->render('portfolio-search-result');
            }
            else{//redirect to same page with error message
                $this->_view->error = 'yes';
                $this->render('portfolio-homepage');
            }
    }
    public function fetchBestImage($start,$interval){

        //echo "entering fetchbestimages with $start and $interval";
        //{$url}/profiles/contrib/pictures/{$result.identifier}/{$result.identifier}_p.jpg profile_pic
        //to check images exist and choose the best qulity images
        $url = 'https://secure.edit-place.fr';
        $img_url = $url.'/profiles/contrib/pictures/';

        $temp_results = $_SESSION['results'];
//        echo "to check= <pre>";print_r($temp_results);echo "</pre>";
        for($j=$start;$j<($start+$interval);$j++){
            if($_SESSION['total'] > $j) {
//                $imageArray = getimagesize($img_url . $temp_results[$j]['identifier'] . "/" . $temp_results[$j]['identifier'] . ".jpg");
//                if ($imageArray[0] && ($imageArray[0] != 60 && $imageArray[1] != 60)) {
//                    //return best image
//                    $temp_results[$j]['profile_pic'] = $img_url . $temp_results[$j]['identifier'] . "/" . $temp_results[$j]['identifier'] . ".jpg";
//                } else {
//                    //else check 2nd best image
//                    $imageArray = getimagesize($img_url . $temp_results[$j]['identifier'] . "/" . $temp_results[$j]['identifier'] . "_port.jpg");
//                    if ($imageArray[0] && ($imageArray[0] != 60 && $imageArray[1] != 60)) {
                        $temp_results[$j]['profile_pic'] = $img_url . $temp_results[$j]['identifier'] . "/" . $temp_results[$j]['identifier'] . "_port.jpg";

//                    } else {
//                        //else return 3rd best image//
//                    $imageArray = getimagesize($img_url . $temp_results[$j]['identifier'] . "/" . $temp_results[$j]['identifier'] . "_crop.jpg");
//                        if ($imageArray[0] && ($imageArray[0] != 60 && $imageArray[1] != 60)) {
//                            $temp_results[$j]['profile_pic'] = $img_url . $temp_results[$j]['identifier'] . "/" . $temp_results[$j]['identifier'] . "_crop.jpg";
//
//                        } else {
//                            //else return 4th best image//
//                            $temp_results[$j]['profile_pic'] = $img_url . $temp_results[$j]['identifier'] . "/" . $temp_results[$j]['identifier'] . "_h.jpg";
//
//                        }
//                    }
//                }
//            echo "<pre>";print_r($temp_results[$j]);echo "</pre>";
                $_SESSION['results'][$j]['profile_pic'] = $temp_results[$j]['profile_pic'];
            }//if
        }//for

    }
    public function fetchBestImageForView($result){
        //echo "entering fetchbestimages with $start and $interval";
        //{$url}/profiles/contrib/pictures/{$result.identifier}/{$result.identifier}_p.jpg profile_pic
        //to check images exist and choose the best qulity images
        $url = 'https://secure.edit-place.fr';
        $img_url = $url.'/profiles/contrib/pictures/';
        $j = 0;
//        $imageArray = getimagesize($img_url . $result[$j]['identifier'] . "/" . $result[$j]['identifier'] . ".jpg");
//        if ($imageArray[0] && ($imageArray[0] != 60 && $imageArray[1] != 60)) {
//            //return best image
//            $result[$j]['profile_pic'] = $img_url . $result[$j]['identifier'] . "/" . $result[$j]['identifier'] . ".jpg";
//        } else {
            //else check 2nd best image
//            $imageArray = getimagesize($img_url . $result[$j]['identifier'] . "/" . $result[$j]['identifier'] . "_port.jpg");
//            if ($imageArray[0] && ($imageArray[0] != 60 && $imageArray[1] != 60)) {
                $result[$j]['profile_pic'] = $img_url . $result[$j]['identifier'] . "/" . $result[$j]['identifier'] . "_port.jpg";

//            } else {
//                //else return 3rd best image//
//                $imageArray = getimagesize($img_url . $result[$j]['identifier'] . "/" . $result[$j]['identifier'] . "_crop.jpg");
//                if ($imageArray[0] && ($imageArray[0] != 60 && $imageArray[1] != 60)) {
//                    $result[$j]['profile_pic'] = $img_url . $result[$j]['identifier'] . "/" . $result[$j]['identifier'] . "_crop.jpg";
//
//                } else {
//                    //else return 4th best image//
//                    $result[$j]['profile_pic'] = $img_url . $result[$j]['identifier'] . "/" . $result[$j]['identifier'] . "_h.jpg";

//                }
//            }
//        }
        //            echo "<pre>";print_r($temp_results[$j]);echo "</pre>";
        return $result[$j]['profile_pic'];

    }

    public function searchPortfolioOnFilterAction(){
        $params=$this->_request->getParams();
            //print_r($params);exit;
        //create object reqired classes
        $obj_manage = new Ep_Portfolio_Manage();
        $type = 'initial_load';
        $results = $obj_manage->searchPortfolio($params,$type);
        if ($results){//if match found redirect to search result page
            $countPerLang = $countPerStatus =$results;
            $countPerLangarray = $this->countPerLang($countPerLang);
            $this->_view->countPerLangTable = $this->countPerLangTable($countPerLangarray);
            //$this->_view->results = $results; //send full list to view
            //sending only specified number of list to view//;
            $_SESSION['results'] = $results;
            $_SESSION['start'] = $start = 0;
            $_SESSION['end'] = $_SESSION['interval'] = $end = $interval=  20;//change this to chnage the number of contributors to be loaded//
            $_SESSION['total'] = $total = count($results);
            $this->fetchBestImage($start,$interval);
            $this->_view->results = array_slice($_SESSION['results'], $_SESSION['start'], $_SESSION['end']);
            $this->_view->count_results = $total;
            $this->_view->url = 'https://secure.edit-place.fr';
            $this->_view->page = 'initial_load';//reference to page load of ajax
            $this->_view->call_type = 'ajax_call';//reference to page load of ajax
            $this->_view->params = $params;//params to display in the select fields
            //print_r($params);exit;
            $this->render('portfolio-search-result-ajax');
        }
        else{//redirect to same page with error message
            echo "<h1>No Results for slected filters</h1>";
        }
    }
    public function sendPartedResultAction()
    {
            $start = $_SESSION['start'] += $_SESSION['interval'];
            $interval = $_SESSION['interval'];
            //function to load best image;
            $this->fetchBestImage($start,$interval);
            $temp_result = $_SESSION['results'];
            if($_SESSION['total'] >= $_SESSION['start']) {
                $this->_view->page = 'load_contributor_on_ajax';//reference to page load of ajax
                $this->_view->url = 'https://secure.edit-place.fr';
                $this->_view->results =  $resultium = array_slice($temp_result, $start, $interval);
                $this->_view->count_results = count($resultium);
                $this->render('portfolio-search-result-ajax');
            }
            else {
                echo 0;
            }

    }
    public function loadChartsAction(){
        $params=$this->_request->getParams();
//            echo "<pre>";print_r($params);exit;
        //create object reqired classes
        $obj_manage = new Ep_Portfolio_Manage();
        $type = 'load_charts';
        $results = $obj_manage->searchPortfolio($params,$type);
        if ($results){//if match found redirect to search result page
            $countPerLang = $countPerBusy = $countPerStatus =$results;
            $countPerLangarray = $this->countPerLang($countPerLang);
            $countPerBusyarray = $this->countPerBusy($countPerBusy); //calculate the number of contributor busy.
            $this->_view->countPerLangTable = $this->countPerLangTable($countPerLangarray,$countPerBusyarray);
            $this->_view->page = 'load_charts';//reference to page load of ajax
            $this->render('portfolio-search-result-ajax');
        }
    }
    public function countPerLang($countPerLang){
        //conditions to calculate count/langauge//
        //If I select
        //writer -> writer level type (as jc/sc) (user table)
        //corrector -> corrector level type (as jc/sc) (user table)
        //translator -> corrector level type (as jc/sc) (contributor table)
        //
        //writer / corrector -> writer level type (as jc/sc) (user table)
        //writer / translator -> writer level type (as jc/sc) (user table)
        //corrector / translator -> corrector level type (as jc/sc) (user table)
        //corrector / translator -> corrector level type (as jc/sc) (user table)
        if( in_array('writer',$params['type'])  ) { //|| !in_array('writer',$params['type'])//
            $fieldName = 'lang_profile_type';
        }
        elseif(in_array('corrector',$params['type'])){
            $fieldName = 'lang_profile_type2';
        }
        elseif(in_array('translator',$params['type']) ){
            $fieldName = 'lang_translator_type';
        }
        elseif(in_array('corrector-translator',$params['type'])){
            $fieldName = 'lang_translator_corrector_type';
        }
        else{
            $fieldName = 'lang_profile_type';
        }

        //multidimentional array to single diementioal array//
        foreach ($countPerLang as &$value)
            $value = $value[$fieldName];
        //end multidimentional array to single diementioal array//
        return array_count_values($countPerLang);
    }
    public function countPerBusy($countPerBusy){
        if( in_array('writer',$params['type'])  ) { //|| !in_array('writer',$params['type'])//
            $fieldName = 'lang_profile_type';
        }
        elseif(in_array('corrector',$params['type'])){
            $fieldName = 'lang_profile_type2';
        }
        elseif(in_array('translator',$params['type']) ){
            $fieldName = 'lang_translator_type';
        }
        elseif(in_array('corrector-translator',$params['type'])){
            $fieldName = 'lang_translator_corrector_type';
        }
        else{
            $fieldName = 'lang_profile_type';
        }
        //multidimentional array to single diementioal array//
        foreach ($countPerBusy as &$value)
            $value = $value[$fieldName].'_'.( ($value['busy_status'] > 0) ? 'yes' : 'no' );
        //end multidimentional array to single diementioal array//
        return array_count_values($countPerBusy);
    }
    public function countPerLangTable($countPerLangarray,$countPerBusyarray){
        $languages_array = $this->languages_array;
        $table = '';
        $senior_free =$senior_busy = $junior_free = $junior_busy =  $sub_junior_free =  $sub_junior_busy = 0;
        foreach ($languages_array as $key => $value) {
            if($countPerLangarray[$key.'_senior'] || $countPerLangarray[$key.'_junior'] || $countPerLangarray[$key.'_sub-junior'] ){
                $table .= '<tr>
                    <td class="smallTableContribFlag">
                        <img src="/BO/theme/lbd/img/flag/' . $key . '.gif" alt="flag">
                        <div>'.utf8_encode($value).'</div>
                    </td>
                    <td>
                        '.( ($countPerLangarray[$key.'_senior']) ? ($countPerLangarray[$key.'_senior']) : '0' ) .'
                    </td>
                    <td>
                        '.( ($countPerLangarray[$key.'_junior']) ? ($countPerLangarray[$key.'_junior']) : '0' ).'
                    </td>
                    <td>
                        '.( ($countPerLangarray[$key.'_sub-junior']) ? ($countPerLangarray[$key.'_sub-junior']) : '0' ).'
                    </td>
                </tr>';
                // counting all the number of senior/junior/subjunior and keeping the same to display in pie chart
                if($countPerLangarray[$key.'_senior']) {
                    $senior_free += ( $countPerBusyarray[$key.'_senior_no']);
                    $senior_busy += $countPerBusyarray[$key.'_senior_yes'];
                }
                if($countPerLangarray[$key.'_junior']) {
                    //$junior+=$countPerLangarray[$key.'_junior'];
                    $junior_free += ( $countPerBusyarray[$key.'_junior_no']);
                    $junior_busy += $countPerBusyarray[$key.'_junior_yes'];
                }
                if($countPerLangarray[$key.'_sub-junior']){
                    //$sub_junior+=$countPerLangarray[$key.'_sub-junior'];
                    $sub_junior_free += ( $countPerBusyarray[$key.'_sub-junior_no']);
                    $sub_junior_busy += $countPerBusyarray[$key.'_sub-junior_yes'];
                }
            }
        }
//        echo "<pre>";print_r($countPerLangarray);echo "</pre>";
//        echo "<pre>";print_r($countPerBusyarray);echo "</pre>";
        $this->_view->csenior = $senior_free;
        $this->_view->cjunior =$junior_free;
        $this->_view->csub_junior = $sub_junior_free;
        $this->_view->c_busy = $senior_busy+$junior_busy+$sub_junior_busy;
        return $table;
    }
//    /function to view profile of particular contributor
    public function viewPortfolioAction(){
        $params=$this->_request->getParams();
        if($params['id']) {
            $obj_manage = new Ep_Portfolio_Manage();
            $result = $obj_manage->getPortfolio($params);
            //echo "<pre>";print_r($result);exit;
            if ($result){//if match found redirect to search result page
                $start= 0;$interval = 1;$condition='viewProtfolio';

                $result[0]['profile_pic'] = $this->fetchBestImageForView($result);
                $this->_view->result = $result[0];
                $job_result = $obj_manage->getJobs($params['id']);//function to get job details
                $this->_view->job_result = $job_result;
                $this->_view->url = $url = 'https://secure.edit-place.fr';
                $icon_list = array(
                    'kitchen' => 'restaurant_menu',
                    'ecomm' => 'shopping_cart',
                    'edu' => 'school',
                    'tech' => 'settings_input_hdmi',
                    'finance' => 'account_balance',
                    'computer' => 'laptop_mac',
                    'fashion' => 'shopping_basket',
                    'ppl' => 'star_rate',
                    'meet' =>'favorite',
                    'corpsite' =>'markunread_mailbox',
                    'sports' => 'casino',
                    'ls' => 'local_cafe ',
                    'travel' => 'flight',
                    'other' => 'public',
                    'assurancevie' => 'add_alert',
                    'Epargne' => 'attach_money',
                    'banque_au_quotidien' => 'local_atm',
                    'MatiÃ¨res_PremiÃ¨res' => 'local_offer',
                    'Immobilier' => 'business',
                    'CrÃ©dit' => 'work',
                    'ImpÃ´t' => 'change_history',
                    'Retraite' => 'accessible'
                );
                $this->_view->icon_list = $icon_list;
                //download images to origin domain//
//                {$url}/profiles/contrib/pictures/{$result[0].identifier
//                echo $url.'/profiles/contrib/pictures/'.$result[0]['identifier'].'/'.$result[0]['identifier'].'_p.jpg';
//                echo 'using realtive path => <img id="profile_pic" class="img-responsive" width="150" alt="" src="'.PROFILE_PIC_PATH.'/120206122716503_p.jpg" alt="'.PROFILE_PIC_PATH.'/120206122716503_p.jpg"">';
//                echo 'using regular path => <img id="profile_pic" class="img-responsive" width="150" alt="" src="/120206122716503_p.jpg">';
//                copy($url.'/profiles/contrib/pictures/'.$result[0]['identifier'].'/'.$result[0]['identifier'].'_p.jpg', $_SERVER['DOCUMENT_ROOT'].'/BO/assets/temp_img/'.$result[0]['identifier'].'/'.$result[0]['identifier'].'_p.jpg');
                $this->render('portfolio-view-portfolio');
            }
            else{//redirect to same page with error message
                $this->_view->error = 'yes';
                $this->render('portfolio-homepage');
            }
        }
        else{
            echo "Error!!!";
        }
    }

    public function downloadImgToServerAction(){
        //deleting all existing temperary images//
//        $dir = ASSETS."temp_img";
//        //randomly empty the temp images
//        if(rand(0, 10) === '10') {
//            foreach (glob($dir . "/*.*") as $filename) {
//                if (is_file($filename)) {
//                    unlink($filename);
//                }
//            }
//        }
        //end of deleting all existing temperary images//
        $params=$this->_request->getParams();
        $client_pics = json_decode($params['client_pics_string']);
        $url = $params['profile_pic'];
        $urlfilename = explode("/",$url);$filename = $urlfilename[count($urlfilename)-1];
        $filepath = ASSETS."temp_img/$filename";
        $data = file_get_contents($url);
        file_put_contents($filepath, $data);
        echo $filename;
    }
    public function downloadClientImgToServerAction(){
        $params=$this->_request->getParams();
        $count = $params['count'];
        for($i = 0 ;$i<$count;$i++){
            $url = $params["client_pics_".$i];
            $urlfilename = explode("/",$url);$filename = $urlfilename[count($urlfilename)-1];
            $filepath = ASSETS."temp_img/$filename";
            $data = file_get_contents($url);
            file_put_contents($filepath, $data);
            $result[$i] = $filename;
        }
        echo json_encode($result);
    }
    public function downloadLastestArticleZipAction(){
        $params=$this->_request->getParams();
        $obj_manage = new Ep_Portfolio_Manage();
        $result = $obj_manage->getLastestArticles($params);
        if($result){
            $file_array = array();
            for ($z = 0; $z < count($result); $z++) {
                    $file_array[] = FO_ARTICLE_PATH.$result[$z]['article_path'];
            }
            //print_r($file_array);
            $downloadfilename = ASSETS.'temp_zip/' . $result[0]['company_name'] . '_Latest_articles_' . date() . '.zip';
            //echo $downloadfilename;
            $zip = $this->create_zip($file_array, $downloadfilename);
            if($zip) {
               $this->_redirect("/BO/download-files.php?function=downloadLastestArticleZip&fullPath=$downloadfilename");
            }
        }
        else{
            echo "<h1>download not available, yet to write article to this client</h1>";
        }

    }
    //function to create a zip file and save it on server to later download purpose//
    public function create_zip($files = array(), $destination = '', $overwrite = true)
    {
        if (file_exists($destination) && !$overwrite) {
            return false;
        }

        $valid_files = array();

        if (is_array($files)) {
            foreach ($files as $file) {

                if (file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }

        if (count($valid_files)) {
            $zip = new ZipArchive();
            if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }

            foreach ($valid_files as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->close();
            return file_exists($destination);
        } else {
            return false;
        }
    }
    //this function to create canvas and save it
    public function exportMultiPortfolioAction(){
        $params=$this->_request->getParams();
        if($params['id']) {
            $obj_manage = new Ep_Portfolio_Manage();
            $result = $obj_manage->getPortfolio($params);
            //echo "<pre>";print_r($result);exit;
            if ($result){//if match found redirect to search result page
                $start= 0;$interval = 1;$condition='viewProtfolio';

                $result[0]['profile_pic'] = $this->fetchBestImageForView($result);
                $this->_view->result = $result[0];
                $job_result = $obj_manage->getJobs($params['id']);//function to get job details
                $this->_view->job_result = $job_result;
                $this->_view->url = $url = 'https://secure.edit-place.fr';
                $icon_list = array(
                    'kitchen' => 'restaurant_menu',
                    'ecomm' => 'shopping_cart',
                    'edu' => 'school',
                    'tech' => 'settings_input_hdmi',
                    'finance' => 'account_balance',
                    'computer' => 'laptop_mac',
                    'fashion' => 'shopping_basket',
                    'ppl' => 'star_rate',
                    'meet' =>'favorite',
                    'corpsite' =>'markunread_mailbox',
                    'sports' => 'casino',
                    'ls' => 'local_cafe ',
                    'travel' => 'flight',
                    'other' => 'public',
                    'assurancevie' => 'add_alert',
                    'Epargne' => 'attach_money',
                    'banque_au_quotidien' => 'local_atm',
                    'MatiÃ¨res_PremiÃ¨res' => 'local_offer',
                    'Immobilier' => 'business',
                    'CrÃ©dit' => 'work',
                    'ImpÃ´t' => 'change_history',
                    'Retraite' => 'accessible'
                );
                $this->_view->icon_list = $icon_list;
                //download images to origin domain//
//                {$url}/profiles/contrib/pictures/{$result[0].identifier
//                echo $url.'/profiles/contrib/pictures/'.$result[0]['identifier'].'/'.$result[0]['identifier'].'_p.jpg';
//                echo 'using realtive path => <img id="profile_pic" class="img-responsive" width="150" alt="" src="'.PROFILE_PIC_PATH.'/120206122716503_p.jpg" alt="'.PROFILE_PIC_PATH.'/120206122716503_p.jpg"">';
//                echo 'using regular path => <img id="profile_pic" class="img-responsive" width="150" alt="" src="/120206122716503_p.jpg">';
//                copy($url.'/profiles/contrib/pictures/'.$result[0]['identifier'].'/'.$result[0]['identifier'].'_p.jpg', $_SERVER['DOCUMENT_ROOT'].'/BO/assets/temp_img/'.$result[0]['identifier'].'/'.$result[0]['identifier'].'_p.jpg');
                $this->_view->page = 'download_png';//reference to page load of ajax
                $this->render('portfolio-search-result-ajax');
            }
            else{//redirect to same page with error message
                $this->_view->error = 'yes';
                $this->render('portfolio-homepage');
            }
        }
        else{
            echo "Error!!!";
        }
    }
    public function saveImageToServerAction(){
        $img = $this->_request->getParam('dataURL');
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $imgName = $this->_request->getParam('imgName');
        $filename = ASSETS . "temp_img/".$imgName;
        chmod($filename,0777);
        file_put_contents($filename, $data);
        chmod($filename,0777);
        echo $filename;
    }
    public function createZipForExportAction(){
        $params=$this->_request->getParams();
        $export_files = explode(",",$params['export_files']);
        $downloadfilename = ASSETS.'temp_zip/export_contributors_' . time() . '.zip';
        //echo $downloadfilename;
        $zip = $this->create_zip($export_files, $downloadfilename);
        if($zip){
            echo '<a href="/BO/download-files.php?function=downloadFile&fullPath='.$downloadfilename.'" id="downloadZipLink">Click to download ZIP file</a>';
        }
        else{
            echo '<a href="javascript:void();" id="downloadZipLink"></a>';
        }
    }
}