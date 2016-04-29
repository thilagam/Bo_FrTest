<?php

/*
 * IndexController - The default controller class
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Action.php';

class TariffController extends Ep_Controller_Action
{
    private $text_admin;

    public function init()
    {
        parent::init();
        $this->_view->lang = $this->_lang;
        $this->adminLogin  = Zend_Registry::get('adminLogin');
        $this->_view->userId = $this->adminLogin->userId;
        $this->sid         = session_id();
        if($this->adminLogin->loginName == '' && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')   {
           echo "session expired...please <a href='http://admin-ep-test.edit-place.com/index'>click here</a> to login"; exit;
        }
    }

    public function addTariffAction()
    {
        $params=$this->_request->getParams();
        $lang_array = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        $categories_array  = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        $obj = new Ep_Tariff_Tariff() ;
        
        //echo '<pre>';print_r($categories_array);exit;
        foreach ($this->twodarrayvals($obj->getTariffColumn('category')) as $categories_arr)
            unset($categories_array[$categories_arr]) ;
        foreach ($this->twodarrayvals($obj->getTariffColumn('language')) as $language_arr)
            unset($lang_array[$language_arr]) ;
        $categories_array_keys  =   array_keys($categories_array);
        $this->_view->catArr=$categories_array; asort($lang_array);
        $this->_view->langArr=$lang_array;
        $this->_view->cat_def=$categories_array_keys[0];

        $client_info_obj = new Ep_User_User();
        $client_info = $client_info_obj->GetclientList();
        $client_list = array();

        for ($c = 0; $c < count($client_info); $c++) {
            $client_list[$c]['identifier'] = $client_info[$c]['identifier'];

            $name = $client_info[$c]['email'];
            $nameArr = array(str_replace("'",'',$client_info[$c]['company_name']), str_replace("'",'',$client_info[$c]['first_name']), str_replace("'",'',$client_info[$c]['last_name']));
            $nameArr = array_filter($nameArr);
            if (count($nameArr) > 0)
                $name .= "(" . implode(", ", $nameArr) . ")";

            $client_list[$c]['name'] = strtoupper($name);
        }
        asort($client_list);
        $this->_view->client_list = $client_list;//echo '<pre>';print_r($client_list);exit;
        
        $this->_view->cl_def=$client_list[0]['name'];
        $ao_info = $obj->getAOlist($client_list[0]['identifier'], $categories_array_keys[0]);
        foreach($ao_info as $key=>$value)
            $ao_list[$value['id']]=strtoupper($value['title']);
        if(count($ao_list)>0)
            asort($ao_list);
        $this->_view->mission_list=$ao_list;    //echo '<pre>';print_r($ao_list);exit;
        @$ao_list_vals   =   array_values($ao_list);
        @$ao_list_keys   =   array_keys($ao_list);
        $this->_view->ao_def   =   trim($ao_list_vals[0]);
        $this->_view->ao_def_id   =   trim($ao_list_keys[0]);

        if($params['submit']) :
            //echo '<pre>';print_r($params);exit;
            $tariff['type']    =   ($params['type']==2) ? 'language' : 'category' ;
            $tariff['category']    =   ($params['type']!=2) ? $params['category'] : '' ;
            $tariff['language']    =   ($params['type']==2) ? $params['language'] : '' ;
            $tariff['price_word_1_month']    =   $params['price1'] ;
            $tariff['price_word_3_month']    =   $params['price2'] ;
            $tariff['price_total']    =   $params['price3'] ;
            $tariff['urgency_interval']    =   $params['urgency_interval'] ;
            $tariff['urgency_percent']    =   $params['urgency_percent'] ;
            //$tariff['fo_text']    =   mysql_escape_string($params['fo_text']) ;
            $tariff['fo_text']    =   stripslashes($params['fo_text']) ;
            //$tariff['fo_text']    =   str_replace("\\r\\n", "", $tariff['fo_text']) ;
            //$tariff['fo_text']    =   str_replace("\r\n", "<br>", $tariff['fo_text']) ;
            
            //for ($i=0; $i < $params['tariffCount']; $i++) {
            for ($i=0; $i < sizeof($params['client']); $i++) {
                $tariffdetails[$i]['client_id'] = $params['client'][$i];
                $tariffdetails[$i]['client_name'] = $params['client_name'][$i];
                $tariffdetails[$i]['delivery_id'] = $params['ao'][$i];
                $tariffdetails[$i]['delivery_name'] = $params['ao_name'][$i];
                $tariffdetails[$i]['articles_count'] = $params['num_articles'][$i];
                $tariffdetails[$i]['avg_price_per_word'] = $params['avg_price_word'][$i];
                $tariffdetails[$i]['min_price'] = $params['price_min'][$i];
                $tariffdetails[$i]['max_price'] = $params['price_max'][$i];
                $tariffdetails[$i]['contrib_price_sum'] = $params['writer_price_sum'][$i];
                $tariffdetails[$i]['article_total_price_sum'] = $params['article_total_price_sum'][$i];
                $tariffdetails[$i]['delivery_time'] = $params['delivery_time'][$i];
                $tariffdetails[$i]['delivery_time_option'] = $params['date_option'][$i];
            }
            
            $obj->insertTariff($tariff, $tariffdetails) ;
            header('Location:/tariff/list-tariff?submenuId=ML3-SL12');
        endif ;
        
        $this->_view->render("add_tariff");
    }

    public function editTariffAction()
    {
        $params=$this->_request->getParams();
        $lang_array = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        $categories_array  = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        
        $obj = new Ep_Tariff_Tariff() ;
        $tariffDetails  =   $obj->getTariff($params['id']);
        
        $j = 0; //echo '<pre>';
        foreach($tariffDetails[1] as $tfd)
        {
            /* ao select box */
            $ao_info1 = $obj->getAOlist($tfd['client_id'], $tariffDetails[0]['category'], $tariffDetails[0]['language']);
            foreach($ao_info1 as $key=>$value)
                $ao_list1[$value['id']]=strtoupper($value['title']);
            if(count($ao_list1)>0)
                asort($ao_list1);
            $select[$j] = '';
            if(sizeof($ao_list1)>0)
            {
                $i=0;
                foreach ($ao_list1 as $key => $value) {
                    $select[$j] .= '<option value="'.$key.'" ' . (($tfd['delivery_id']==$key) ? 'selected' : '') . '>'.$value.'</option>';
                    $i++;
                }
            }   //print_r($ao_list1);
            unset($ao_list1);
            $j++;
            /* ao select box */
        }   //print_r($select);exit;
        
        foreach ($this->twodarrayvals($obj->getTariffColumn('category')) as $categories_arr)
            if($categories_arr!=$tariffDetails[0]['category'])
                unset($categories_array[$categories_arr]) ;
        foreach ($this->twodarrayvals($obj->getTariffColumn('language')) as $language_arr)
            if($language_arr!=$tariffDetails[0]['language'])
                unset($lang_array[$language_arr]) ;
            
        $this->_view->catArr=$categories_array; asort($lang_array);
        $this->_view->langArr=$lang_array;
        $this->_view->cat_def=$categories_array_keys[0];

        $client_info_obj = new Ep_User_User();
        $client_info = $client_info_obj->GetclientList();
        $client_list = array();

        for ($c = 0; $c < count($client_info); $c++) {
            $client_list[$c]['identifier'] = $client_info[$c]['identifier'];

            $name = $client_info[$c]['email'];
            $nameArr = array(str_replace("'",'',$client_info[$c]['company_name']), str_replace("'",'',$client_info[$c]['first_name']), str_replace("'",'',$client_info[$c]['last_name']));
            $nameArr = array_filter($nameArr);
            if (count($nameArr) > 0)
                $name .= "(" . implode(", ", $nameArr) . ")";

            $client_list[$c]['name'] = strtoupper($name);
        }
        asort($client_list);
        $this->_view->client_list = $client_list;//echo '<pre>';print_r($client_list);exit;
        
        $this->_view->cl_def=$client_list[0]['name'];
        $ao_info = $obj->getAOlist($client_list[0]['identifier'], $categories_array_keys[0]);
        foreach($ao_info as $key=>$value)
            $ao_list[$value['id']]=strtoupper($value['title']);
        if(count($ao_list)>0)
            asort($ao_list);
        $this->_view->mission_list=$ao_list;    //echo '<pre>';print_r($ao_list);exit;
        @$ao_list_vals   =   array_values($ao_list);
        @$ao_list_keys   =   array_keys($ao_list);
        $this->_view->ao_def   =   trim($ao_list_vals[0]);
        $this->_view->ao_def_id   =   trim($ao_list_keys[0]);

        if($params['submit']) :
            //echo '<pre>';print_r($params);exit;
            $tariff['type']    =   ($params['type']==2) ? 'language' : 'category' ;
            $tariff['category']    =   ($params['type']!=2) ? $params['category'] : '' ;
            $tariff['language']    =   ($params['type']==2) ? $params['language'] : '' ;
            $tariff['price_word_1_month']    =   $params['price1'] ;
            $tariff['price_word_3_month']    =   $params['price2'] ;
            $tariff['price_total']    =   $params['price3'] ;
            $tariff['urgency_interval']    =   $params['urgency_interval'] ;
            $tariff['urgency_percent']    =   $params['urgency_percent'] ;
            $tariff['fo_text']    =   stripslashes($params['fo_text']) ;
            //$tariff['fo_text']    =   str_replace("\r\n", "<br>", $tariff['fo_text']) ;
            //for ($i=0; $i < $params['tariffCount']; $i++) {
            for ($i=0; $i < sizeof($params['client']); $i++) {
                $tariffdetails[$i]['id'] = $params['tariffdataid'][$i];
                $tariffdetails[$i]['client_id'] = $params['client'][$i];
                $tariffdetails[$i]['client_name'] = $params['client_name'][$i];
                $tariffdetails[$i]['delivery_id'] = $params['ao'][$i];
                $tariffdetails[$i]['delivery_name'] = $params['ao_name'][$i];
                $tariffdetails[$i]['articles_count'] = $params['num_articles'][$i];
                $tariffdetails[$i]['avg_price_per_word'] = $params['avg_price_word'][$i];
                $tariffdetails[$i]['min_price'] = $params['price_min'][$i];
                $tariffdetails[$i]['max_price'] = $params['price_max'][$i];
                $tariffdetails[$i]['contrib_price_sum'] = $params['writer_price_sum'][$i];
                $tariffdetails[$i]['article_total_price_sum'] = $params['article_total_price_sum'][$i];
                $tariffdetails[$i]['delivery_time'] = $params['delivery_time'][$i];
                $tariffdetails[$i]['delivery_time_option'] = $params['date_option'][$i];
            }
			
            $obj->updateTariff($params['id'], $tariff, $tariffdetails) ;
            header('Location:/tariff/list-tariff?submenuId=ML3-SL12');
        endif ;
        //echo '<pre>';print_r($obj->getTariff($params['id']));exit;
        $tariffDetails[0]['fo_text'] = stripslashes($tariffDetails[0]['fo_text']);
        $this->_view->tariff    =   $tariffDetails[0] ;
        $this->_view->tariffdata    =   $tariffDetails[1] ;
        $this->_view->aoselect    =   $select ;
        if($_REQUEST['debug']){echo '<pre>';print_r($tariffDetails[0]);print_r($select);print_r($tariffDetails[1]);exit;}
        $this->_view->render("edit_tariff");
    }

    public function deleteTariffAction()
    {
        $params=$this->_request->getParams();
        $obj = new Ep_Tariff_Tariff() ;
        if($params['tid']){
            $obj->deleteTariffData($params['tid']);
        }
        else
        {
            $obj->deleteTariff($params['id']);
            header('Location:/tariff/list-tariff?submenuId=ML3-SL12');
        }
        
    }

    public function listTariffAction()
    {
        $params=$this->_request->getParams();
        $lang_array = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        $categories_array  = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        
        $obj = new Ep_Tariff_Tariff() ;
        $tarrifflist_c = $obj->listTariffs('category') ;
        $tarrifflist_l = $obj->listTariffs('language') ;
        
        foreach ($tarrifflist_c as $key=>$tarrifflist) :
            $tarrifflist_c[$key]['category'] = $categories_array[$tarrifflist['category']];
            $tarrifflist_c[$key]['language'] = $lang_array[$tarrifflist['language']];
        endforeach;
        
        foreach ($tarrifflist_l as $key=>$tarrifflist) :
            $tarrifflist_l[$key]['category'] = $categories_array[$tarrifflist['category']];
            $tarrifflist_l[$key]['language'] = $lang_array[$tarrifflist['language']];
        endforeach;
        
        $this->_view->tarrifflist_c = $tarrifflist_c ;
        $this->_view->tarrifflist_l = $tarrifflist_l ;
        
        if($params['add'])
            $this->_view->successMsg    =   "Citer cr&#233;&#233; avec succ&#232;s" ;
        elseif($params['edit'])
            $this->_view->successMsg    =   "Citer correctement mis &#224; jour" ;
        //echo '<pre>'; print_r($obj->listTariffs('category')); print_r($obj->listTariffs('language')); exit('ggg');
        $this->_view->render("list_tariff");
    }

    public function twodarrayvals($arr)
    {
        foreach ($arr as $key => $value) {
            foreach ($value as $k => $v)
                $arr_[] = $v;
        }
        return array_values(array_unique($arr_)) ;
    }

    public function getAvgPricesAction()
    {
        $params=$this->_request->getParams();
        $obj = new Ep_Tariff_Tariff() ;
        $prices = $obj->getPrices($params['col'], $params['val']);
        if(!$prices)    exit('NA#NA#NA');
        $prices = array_map('trim', $prices);
        exit(number_format($prices['m1'], 2, '.', '') . '#' . number_format($prices['m3'], 2, '.', '') . '#' . number_format($prices['m'], 2, '.', ''));
    }
	
    public function filterByCatOrLanAction()
    {   
        $params=$this->_request->getParams();
        $obj = new Ep_Tariff_Tariff() ;
        $prices = $obj->getPrices($params['col'], $params['val']);
        if(!$prices)
		{
			$prices = array('NA','NA','NA');
		}
		else {
			$prices = array_map('trim', $prices);
            $prices = array(number_format($prices['m1'], 2, '.', ''), number_format($prices['m3'], 2, '.', ''), number_format($prices['m'], 2, '.', ''));
		}
        $client_ = explode("z", $params['clids']);
        $clientIds = array_filter(explode("|", $client_[0]));//echo '<pre>'; print_r($clientIds); exit('ggg');
        
        if(sizeof($clientIds)>0)
        {
    		foreach ($clientIds as $clientId) {
    			$clients[] = $this->getAoSelectBox($clientId, (($params['col']=='category') ? $params['val'] : ''), (($params['col']=='language') ? $params['val'] : ''));
    		}
    		foreach ($clients as $client) {
    			$aodata[] = $obj->getAoArticlesInfo($client['def_ao_id']);
    		}
        }
		print(json_encode(array('prices'=>$prices, 'clients'=>$clients, 'aodata'=>$aodata)));
		exit;
    }

    public function getClientsSelectAction()
    {
        $params=$this->_request->getParams();
        $client_info_obj = new Ep_User_User();
        $client_info = $client_info_obj->GetclientList();
        $client_list = array();

        for ($c = 0; $c < count($client_info); $c++) {
            $client_list[$c]['identifier'] = $client_info[$c]['identifier'];

            $name = $client_info[$c]['email'];
            $nameArr = array($client_info[$c]['company_name'], $client_info[$c]['first_name'], $client_info[$c]['last_name']);
            $nameArr = array_filter($nameArr);
            if (count($nameArr) > 0)
                $name .= "(" . implode(", ", $nameArr) . ")";

            $client_list[$c]['name'] = strtoupper($name);
        }
        asort($client_list);
        $options = '<select name="client[]" id="client'.$params['idx'].'" class="chzn_a" data-placeholder="S&eacute;lectionner" onchange="aos(this.value, this.options[this.selectedIndex].text, '.$params['idx'].')"><option value="">S&eacute;lectionner</option>';
        if(sizeof($client_list)>0)
        {
            $i=0;
            foreach ($client_list as $key => $value) {
                if($params['sel'])
                {
                    if($value['identifier']==$params['sel'])    $def_cl_text=$value['name'];
                    $options .= '<option value="' . $value['identifier'] . '"' . (($value['identifier']==$params['sel']) ? " selected" : "") . '>' . $value['name'] . '</option>';
                }
                else
                {
                    $options .= '<option value="' . $value['identifier'] . '">' . $value['name'] . '</option>';
                }
                $i++;
            }
        }
        if($params['sel'])
            exit($options.'</select>#'.$def_cl_text);
        else
            exit($options.'</select>');
    }

    public function getAosSelectAction()
    {
        $params=$this->_request->getParams();
        $obj = new Ep_Tariff_Tariff() ;
        $ao_info = $obj->getAOlist($params['id'], $params['cat'], $params['lang']);
        foreach($ao_info as $key=>$value)
            $ao_list[$value['id']]=strtoupper($value['title']);
        if(count($ao_list)>0)
            asort($ao_list);
        $this->_view->mission_list=$ao_list;
        $options = '<select name="ao[]" id="ao'.$params['idx'].'" class="chzn_b" data-placeholder="S&eacute;lectionner" onchange="aodata(this.value, this.options[this.selectedIndex].text, '.$params['idx'].')"><option value="">S&eacute;lectionner</option>';  //$lbls = '';
        if(sizeof($ao_list)>0)
        {
            $i=0;
            foreach ($ao_list as $key => $value) {
                if($i<1){$def_cl_id = $key;$def_cl_text = $value;}
                $options .= '<option value="'.$key.'">'.$value.'</option>';
                $i++;
            }
        }
        exit($options.'</select>#'.$def_cl_text.'#'.$def_cl_id);
    }

    public function getAoSelectBox($clid, $cat, $lan)
    {
        $clids = explode('z', $clid);
        $params=$this->_request->getParams();
        $obj = new Ep_Tariff_Tariff();
        $ao_info = $obj->getAOlist($clids[0], $cat, $lan);
        foreach($ao_info as $key=>$value)
            $ao_list[$value['id']]=strtoupper($value['title']);
        if(count($ao_list)>0)
            asort($ao_list);
        $this->_view->mission_list=$ao_list;
        $select = '<select name="ao[]" id="ao'.$clids[1].'" class="chzn_b" data-placeholder="S&eacute;lectionner" onchange="aodata(this.value, this.options[this.selectedIndex].text, '.$clids[1].')">';
        if(sizeof($ao_list)>0)
        {
            $i=0;
            foreach ($ao_list as $key => $value) {
                if($i<1){$def_ao_id = $key;$def_ao_text = $value;}
                $select .= '<option value="'.$key.'">'.$value.'</option>';
                $i++;
            }
        }
        $select .='</select>';//exit($select.'$'.$cat.'$'.$lan);
		return array('selectmenu'=>$select, 'def_ao_id'=>$def_ao_id, 'def_ao_text'=>$def_ao_text);
    }

    public function getAoDataAction()
    {
        $params=$this->_request->getParams();
        $obj = new Ep_Tariff_Tariff() ;
        //$ao_info = $obj->getAoArticlesInfo($params['id']);
        print json_encode($obj->getAoArticlesInfo($params['id']));
        exit ;
    }
    
    public function adduserAction()
    {
        $user_det=$this->_request->getParams();
        $user_obj=new Ep_User_User();
        $clident=$user_obj->InsertnewUser($user_det);
        exit($clident);
    }
}
