<?php

/*
 * IndexController - The default controller class
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Action.php';

class MissionController extends Ep_Controller_Action
{
    private $text_admin;

    public function init()
    {
        parent::init();
		ini_set('max_execution_time', 500);
        $this->_view->lang = $this->_lang;
        $this->adminLogin  = Zend_Registry::get('adminLogin');
        $this->_view->userId = $this->adminLogin->userId;
        $this->sid         = session_id();
        if($this->adminLogin->loginName == '' && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
           echo "session expired...please <a href='http://admin-ep-test.edit-place.com/index'>click here</a> to login";
           exit;
        }
    }

    public function addContractAction()
    {
        $params=$this->_request->getParams();
        $lang_array = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
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
        $this->_view->client_list = $client_list;
        $obj = new Ep_epcontract_Contract();

        if($params['submit']) :
            //echo $params['end_date'];;
            $ptype = array('daily', 'factor', 'direct', 'other');
            $contract['client_id']    =   $params['client'] ;
            $contract['client_name']    =   $params['client_name'] ;
            $contract['contract_name']    =   $params['contract_name'] ;
            $contract['code']    =   $params['code'] ;
            $contract['payment_type']    =   $ptype[$params['payment_type']-1] ;
            $contract['turnover']    =   $params['turnover'] ;
            $contract['turnover_currency']    =   $params['turnover_currency'] ;
            
            $params['end_date'] =   trim(str_replace('/', '-', $params['end_date'])) ;
            $params['date_of_signature'] =   trim(str_replace('/', '-', $params['date_of_signature'])) ;
            
            $date_ed = new DateTime($params['end_date']);//exit($params['end_date']);            
            $contract['end_date']    =   $date_ed->format('Y-m-d') ;
            $date_dos = new DateTime($params['date_of_signature']);
            $contract['date_of_signature']    =   $date_dos->format('Y-m-d') ;
            
            /*$params['end_date'] =   str_replace('/', '-', $params['end_date']);
            $contract['end_date']    =   date('Y-m-d', strtotime($params['end_date'])) ;
            $params['date_of_signature'] =   str_replace('/', '-', $params['date_of_signature']);
            $contract['date_of_signature']    =   date('Y-m-d', strtotime($params['date_of_signature'])) ;*/
            
            //$contract['end_date']    =   $params['end_date'] ;
            //$contract['date_of_signature']    =   $params['date_of_signature'] ;
            $contract['created_at']    =   date('Y-m-d H:i:s') ;
            
            $obj->insertContract($contract) ;
            //print_r($contract);exit;
            header('Location:/mission/list-contract?submenuId=ML2-SL26');
        endif ;
        
        $this->_view->render("ep_add_contract");
    }

    public function addMissionAction()
    {
        $params=$this->_request->getParams();
        $obj = new Ep_epcontract_Contract();
        
        if($params['ctid'] && $params['gtcl'])
            exit($obj->getClientNcurr($params['ctid']));
        
        if($params['cid'])
        {
            $this->_view->contract_ids = $params['cid'];
        }
        
        $euro = 'Euro';//'&#8364;';
        $pound = 'Pound';//'&#163;';
        $us_dollar = 'Dollar';//'$';
        
        $lang_array = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        $categories_array  = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        
        unset($categories_array['other']);
        //echo '<pre>';print_r($categories_array);exit;
        $this->_view->catArr=$categories_array; asort($lang_array);
        $this->_view->langArr=$lang_array;
        $this->_view->category='';
        $this->_view->language1='';
        $this->_view->language2='';
        
        $mail=new Ep_Message_Ticket();
        $get_EP_contacts=$mail->getEPContactsMaster('"salesuser","partner","customercare","facturation"');
        if($get_EP_contacts!="Not Exists")
        {
            foreach($get_EP_contacts as $contact)
            {
                if($contact['contact_name']!=NULL)
                    $EP_contacts[$contact['identifier']]=$contact['contact_name'];
                else
                {
                    $contact['email']=explode("@",$contact['email']);
                    $EP_contacts[$contact['identifier']]=$contact['email'][0];
                }
            }
        }
        if($get_EP_contacts!=='Not Exists')
            $this->_view->EP_contacts=$EP_contacts;
        
        $this->_view->contract_list=$obj->getContracts();
        
        if($params['id'])
        {
            $missionDetails  =   $obj->getMission($params['id']);
            //$missionDetails[0]['category']  =   (($missionDetails[0]['category']=='other') ? 'z' : $missionDetails[0]['category']);
            
            $missionDetails[0]['writing_cost_before_signature_currency_l'] = $$missionDetails[0]['writing_cost_before_signature_currency'];
            $missionDetails[0]['correction_cost_before_signature_currency_l'] = $$missionDetails[0]['correction_cost_before_signature_currency'];
            $missionDetails[0]['other_cost_before_signature_currency_l'] = $$missionDetails[0]['other_cost_before_signature_currency'];
            
            $missionDetails[0]['min_cost_mission_currency_l'] = $$missionDetails[0]['min_cost_mission_currency'];
            $missionDetails[0]['max_cost_mission_currency_l'] = $$missionDetails[0]['max_cost_mission_currency'];
            
            $missionDetails[0]['writing_cost_after_signature_currency_l'] = $$missionDetails[0]['writing_cost_after_signature_currency'];
            $missionDetails[0]['correction_cost_after_signature_currency_l'] = $$missionDetails[0]['correction_cost_after_signature_currency'];
            $missionDetails[0]['other_cost_after_signature_currency_l'] = $$missionDetails[0]['other_cost_after_signature_currency'];
            
            $missionDetails[0]['selling_price_currency_l'] = $$missionDetails[0]['selling_price_currency'];
            
            $missionDetails[0]['starting_date'] = date('d/m/Y', strtotime($missionDetails[0]['starting_date']));
            $this->_view->mission_list = $missionDetails[0];
        }
        
        //if($_REQUEST['debug']){echo '<pre>';print_r($missionDetails);}
        if($params['submit']) :
            //echo '<pre>';print_r($params);
            $ptype = array('daily', 'factor', 'direct', 'other');
            $mission['contract_id']    =   $params['contract'] ;
            $mission['title']    =   $params['title'] ;
            $mission['mission_length']    =   $params['mission_length'] ;
            $params['starting_date'] =   trim(str_replace('/', '-', $params['starting_date'])) ;
            
            $datestrt = new DateTime($params['starting_date']);
            $mission['starting_date']    =   $datestrt->format('Y-m-d') ;
            $mission['type_of_article']    =   $params['type_of_article'] ;
            $mission['num_of_articles']    =   $params['num_of_articles'] ;
            $mission['type']    =   $params['type'] ;
            $mission['language1']    =   $params['language1'] ;
            $mission['language2']    =   (($params['type'] == 'translation') ? $params['language2'] : '') ;
            $mission['category']    =   (($params['category'] != 'z') ? $params['category'] : 'other') ;
            $mission['category_other']    =   (($params['category'] == 'other') ? $params['category_other'] : '') ;
            $mission['logo']    =   $params['logo'] ;
            
            $mission['writing_cost_before_signature']    =   $params['writing_cost_before_signature'] ;
            $mission['correction_cost_before_signature']    =   $params['correction_cost_before_signature'] ;
            $mission['other_cost_before_signature']    =   $params['other_cost_before_signature'] ;
            
            $mission['min_cost_mission']    =   $params['min_cost_mission'] ;
            $mission['max_cost_mission']    =   $params['max_cost_mission'] ;
            
            $mission['writing_cost_after_signature']    =   $params['writing_cost_after_signature'] ;
            $mission['correction_cost_after_signature']    =   $params['correction_cost_after_signature'] ;
            $mission['other_cost_after_signature']    =   $params['other_cost_after_signature'] ;
            
            $mission['selling_price'] = $params['selling_price'] ;
            
            $mission['margin_before_signature']    =   str_replace('.00', '', $params['margin_before_signature']) ;
            $mission['margin_after_signature']    =   str_replace('.00', '', $params['margin_after_signature']) ;
            
            $mission['writing_cost_before_signature_currency']    =   $params['writing_cost_before_signature_currency'] ;
            $mission['correction_cost_before_signature_currency']    =   $params['correction_cost_before_signature_currency'] ;
            $mission['other_cost_before_signature_currency']    =   $params['other_cost_before_signature_currency'] ;
            
            $mission['min_cost_mission_currency']    =   $params['min_cost_mission_currency'] ;
            $mission['max_cost_mission_currency']    =   $params['max_cost_mission_currency'] ;
            
            $mission['writing_cost_after_signature_currency']    =   $params['writing_cost_after_signature_currency'] ;
            $mission['correction_cost_after_signature_currency']    =   $params['correction_cost_after_signature_currency'] ;
            $mission['other_cost_after_signature_currency']    =   $params['other_cost_after_signature_currency'] ;
            
            $mission['selling_price_currency']    =   $params['selling_price_currency'] ;
            
            $mission['article_length']    =   $params['article_length'] ;
            $mission['comments']    =   $params['comments'] ;
            $mission['bo_incharge']    =   $params['bo_incharge'] ;
            $mission['mission_users_count']    =   $params['mission_users_count'] ;
            $mission['fo_display']    =   $params['fo_display'] ;
            //print_r($mission);exit($params['id']);
            if($params['id'])
            {
                $mission['updation_date']    =   date('Y-m-d H:i:s') ;
                $obj->updateMission($params['id'], $mission) ;
            }
            else {
                $mission['creation_date']    =   date('Y-m-d H:i:s') ;
                $recurr    =   $params['recurring'] ? $params['recurr'] : 0 ;
                $obj->insertMission($mission, $recurr) ;
            }
            header('Location:/mission/list-mission?submenuId=ML2-SL27');
        endif ;
        
        $this->_view->render("ep_mission");
    }

    public function editContractAction()
    {
        $params=$this->_request->getParams();
        $lang_array = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        
        $obj = new Ep_epcontract_Contract() ;
        $contractDetails  =   $obj->getContract($params['id']);
        $contractDetails[0]['date_of_signature'] = date('d/m/Y', strtotime($contractDetails[0]['date_of_signature']));
        $contractDetails[0]['end_date'] = date('d/m/Y', strtotime($contractDetails[0]['end_date']));
        $this->_view->contract_list = $contractDetails[0];

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
        $this->_view->client_list = $client_list;

        if($params['submit']) :
            
            $ptype = array('daily', 'factor', 'direct', 'other');
            $contract['client_id']    =   $params['client'] ;
            $contract['client_name']    =   $params['client_name'] ;
            $contract['contract_name']    =   $params['contract_name'] ;
            $contract['code']    =   $params['code'] ;
            $contract['payment_type']    =   $ptype[$params['payment_type']-1] ;
            $contract['turnover']    =   $params['turnover'] ;
            $contract['turnover_currency']    =   $params['turnover_currency'] ;
            
            $params['end_date'] =   trim(str_replace('/', '-', $params['end_date'])) ;
            $params['date_of_signature'] =   trim(str_replace('/', '-', $params['date_of_signature'])) ;
            
            $date_ed = new DateTime($params['end_date']);
            $contract['end_date']    =   $date_ed->format('Y-m-d') ;
            $date_dos = new DateTime($params['date_of_signature']);
            $contract['date_of_signature']    =   $date_dos->format('Y-m-d') ;
            //echo '<pre>';print_r($contract);print_r($params);exit();
            
            /*$params['end_date'] =   str_replace('/', '-', $params['end_date']);
            $contract['end_date']    =   date('Y-m-d', strtotime($params['end_date'])) ;
            $params['date_of_signature'] =   str_replace('/', '-', $params['date_of_signature']);
            $contract['date_of_signature']    =   date('Y-m-d', strtotime($params['date_of_signature'])) ;*/
            
            $contract['updation_date']    =   date('Y-m-d H:i:s') ;
            //$contract['end_date']    =   $params['end_date'] ;
            //$contract['date_of_signature']    =   $params['date_of_signature'] ;
			//print_r($contract);exit;
            $obj->updateContract($params['id'], $contract) ;
            header('Location:/mission/list-contract?submenuId=ML2-SL26');
        endif ;
        
        $this->_view->render("ep_edit_contract");
    }

    public function deleteContractAction()
    {
        $params=$this->_request->getParams();
        $obj = new Ep_Contract_Contract() ;
        if($params['tid']){
            $obj->deleteContractData($params['tid']);
        }
        else
        {
            $obj->deleteContract($params['id']);
            header('Location:/tariff/list-tariff?submenuId=ML2-SL26');
        }
        
    }

    public function listContractAction()
    {
        $params=$this->_request->getParams();
        
        $obj = new Ep_epcontract_Contract() ;
       // $contractList = $obj->listContracts() ;

        /*$client_info_obj = new Ep_User_User();
        for ($c = 0; $c < count($contractList); $c++) {
            $clprof = $client_info_obj->getClientProfile($contractList[$c]['client_id']);
            $contractList[$c]['company_name'] = $clprof[0]['company_name'];
            $contractList[$c]['end_date'] = date('d/m/y', strtotime($contractList[$c]['end_date']));
            $contractList[$c]['date_of_signature'] = date('d/m/y', strtotime($contractList[$c]['date_of_signature']));
        }
        //echo '<pre>';print_r($contractList); exit('ggg');
        $this->_view->contractlist = $contractList ;*/
        
        if($params['add'])
            $this->_view->successMsg    =   "Contract added successfully.." ;
        elseif($params['edit'])
            $this->_view->successMsg    =   "Contract updated successfully.." ;
        
        $this->_view->render("ep_list_contract");
    }
    //// load contract list ////
    public function loadContractsAction()
    {
        $userplus_obj=new Ep_User_UserPlus();
        $user_obj = new Ep_User_User();
        $obj = new Ep_epcontract_Contract() ;
        $aColumns = array('id','client_name','contract_name','code','payment_type','turnover','date_of_signature','end_date','actions');
        /* * Paging	 */
        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
                intval( $_GET['iDisplayLength'] );
        }
        /* 	 * Ordering   	 */
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
                        ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        }
        $sWhere = "";
        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
        {
            $sWhere = " HAVING (";
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                if($aColumns[$i] == 'client_name')
                    $aColumns[$i] = 'client_name';
                if($aColumns[$i] == 'contract_name')
                    $aColumns[$i] = 'contract_name';
                if($aColumns[$i] == 'code')
                    $aColumns[$i] = 'code';
                if($aColumns[$i] == 'end_date')
                    $aColumns[$i] = 'end_date';
                if($aColumns[$i] == 'turnover')
                    $aColumns[$i] = 'turnover';
                if($aColumns[$i] == 'date_of_signature')
                    $aColumns[$i] = 'date_of_signature';
                if($aColumns[$i] == 'actions')
                    break;
                $keyword=addslashes($_GET['sSearch']);
                //$keyword=$_GET['sSearch'];
                $keyword = preg_replace('/\s*$/','',$keyword);
                $keyword=preg_replace('/\(|\)/','',$keyword);
                $words=explode(" ",$keyword);
                if(count($words)>1)
                {
                    $sWhere.=$aColumns[$i]." like '%".utf8_decode($keyword)."%' OR ";
                    foreach($words as $key=>$word)
                    {
                        $word=trim($word);
                        if($word!='')
                        {
                            $sWhere .= "".$aColumns[$i]." LIKE '%".utf8_decode($word)."%' OR ";
                        }
                    }
                }
                else
                    $sWhere .= "".$aColumns[$i]." LIKE '%".utf8_decode($keyword)."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }
        /* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                if ( $sWhere == "" )
                {
                    $sWhere = " WHERE  ";
                }
                else
                {
                    $sWhere .= " AND  ";
                }
                $sWhere .= "`".$aColumns[$i]."` LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
        }
        // echo $sWhere; exit;
        $rResult  = $obj->loadListContracts($sWhere, $sOrder, $sLimit, $condition);
        $rResultcount = count($rResult);

        /////total count
        $sLimit = "";
        $contractList  = $obj->loadListContracts($sWhere, $sOrder, $sLimit, $condition);
        $iTotal = count($contractList);

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );
        $count = 1;//print_r($rResult);
        if($rResult != 'NO') //if non relavent data is given in search column//
        {
            for( $i=0 ; $i<$rResultcount; $i++)
            {
                $row = array();
                for ( $j=0 ; $j<count($aColumns) ; $j++ )
                {
                    if($j == 0)
                        $row[] = $count;
                    else
                    {
                        if($aColumns[$j] == 'client_name')
                            $row[] = utf8_encode($rResult[$i]['client_name']);
                        elseif($aColumns[$j] == 'contract_name')
                            $row[] = utf8_encode($rResult[$i]['contract_name']);
                        elseif($aColumns[$j] == 'code')
                            $row[] = $rResult[$i]['code'];
                        elseif($aColumns[$j] == 'turnover')
                            $row[] = $rResult[$i]['turnover'];
                        elseif($aColumns[$j] == 'payment_type')
                            $row[] = $rResult[$i]['payment_type'];
                        elseif($aColumns[$j] == 'date_of_signature')
                            $row[] = date("d/m/Y", strtotime($rResult[$i]['date_of_signature']));
                        elseif($aColumns[$j] == 'end_date')
                            $row[] = date("d/m/Y", strtotime($rResult[$i]['end_date']));
                        elseif($aColumns[$j] == 'actions')
                        {
                            $row[] = '<a href="/mission/edit-contract?submenuId=ML2-SL26&id='.$rResult[$i]['id'].'" class="hint--left hint--info" data-hint="edit contract"><i class="splashy-application_windows_edit"></i> </a>
                                <a href="/mission/add-mission?submenuId=ML2-SL27&cid=viewclient&userId='.$rResult[$i]['id'].'" class="hint--left hint--info" data-hint="Add mission"><i class="splashy-application_windows_add"></i></a>
                                <a href="/mission/list-mission?submenuId=ML2-SL27&cid='.$rResult[$i]['id'].'" class="hint--left hint--info" data-hint="List missions"><i class="icon-eye-open"></i></a>';
                        }
                        else
                            $row[] = $rResult[$i][ $aColumns[$j] ];
                    }
                }
                $output['aaData'][] = $row;
                $count++;
            }
        }
        // print_r($output);  exit;
        echo json_encode( $output );

    }


    public function listMissionAction()
    {
        $params=$this->_request->getParams();
        
        $obj = new Ep_epcontract_Contract() ;
       /* $missionList = $obj->listMissions($params['cid'] ? $params['cid'] : '') ;
        //echo '<pre>';print_r($missionList); 
        //echo '<pre>';print_r($missionList); exit('ggg');

        $client_info_obj = new Ep_User_User();
        for ($c = 0; $c < count($missionList); $c++) {
            $contract_ = $obj->getContract($missionList[$c]['contract_id']);
            $clprof = $client_info_obj->getClientProfile($contract_[0]['client_id']);
            $missionList[$c]['company_name'] = $clprof[0]['company_name'];
            $missionList[$c]['contract_name'] = $contract_[0]['contract_name'];
            $missionList[$c]['contract_title'] = $contract_[0]['title'];
            $missionList[$c]['starting_date'] = date('d/m/y', strtotime($missionList[$c]['starting_date']));
        }
        //echo '<pre>';print_r($missionList); exit('ggg');
        $this->_view->missionlist = $missionList ;*/
        
        if($params['add'])
            $this->_view->successMsg    =   "Mission added successfully.." ;
        elseif($params['edit'])
            $this->_view->successMsg    =   "Mission updated successfully.." ;
        
        $this->_view->render("ep_list_mission");
    }
    //// load missions list ////
    public function loadMissionsAction()
    {
        $params=$this->_request->getParams();
        $userplus_obj=new Ep_User_UserPlus();
        $user_obj = new Ep_User_User();
        $obj = new Ep_epcontract_Contract() ;
        $aColumns = array('id','title','contract_name','client_name','mission_length','article_length','starting_date','num_of_articles','selling_price','margin_before_signature','margin_after_signature','action');
        /* * Paging	 */
        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
                intval( $_GET['iDisplayLength'] );
        }
        /* 	 * Ordering   	 */
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true")
                {
                    $sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
                        ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        }
        $sWhere = "";
        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
        {
            $sWhere = " HAVING (";
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                if($aColumns[$i] == 'action')
                    break;

                $keyword=addslashes($_GET['sSearch']);
                $keyword = preg_replace('/\s*$/','',$keyword);
                $keyword=preg_replace('/\(|\)/','',$keyword);
                $words=explode(" ",$keyword);
                if(count($words)>1)
                {
                    $sWhere.=$aColumns[$i]." like '%".utf8_decode($keyword)."%' OR ";
                    foreach($words as $key=>$word)
                    {
                        $word=trim($word);
                        if($word!='')
                        {
                            $sWhere .= "".$aColumns[$i]." LIKE '%".utf8_decode($word)."%' OR ";
                        }
                    }
                }
                else
                    $sWhere .= "".$aColumns[$i]." LIKE '%".utf8_decode($keyword)."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }
        /* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                if ( $sWhere == "" )
                {
                    $sWhere = " WHERE  ";
                }
                else
                {
                    $sWhere .= " AND  ";
                }
                $sWhere .= "`".$aColumns[$i]."` LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
        }
        // echo $sWhere; exit;
        $rResult  = $obj->loadListMissions($sWhere, $sOrder, $sLimit, $condition);
        $rResultcount = count($rResult);

        /////total count
        $sLimit = "";
        $contractList  = $obj->loadListMissions($sWhere, $sOrder, $sLimit, $condition);
        $iTotal = count($contractList);

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );

        $count = 1;//print_r($rResult);
        if($rResult != 'NO') //if non relavent data is given in search column//
        {
            for( $i=0 ; $i<$rResultcount; $i++)
            {
                $row = array();
                for ( $j=0 ; $j<count($aColumns) ; $j++ )
                {
                    if($j == 0)
                        $row[] = $count;
                    else
                    {
                        if($aColumns[$j] == 'title')
                            $row[] = utf8_encode($rResult[$i]['title']);
                        elseif($aColumns[$j] == 'mission_length')
                            $row[] = $rResult[$i]['mission_length'];
                        elseif($aColumns[$j] == 'article_length')
                            $row[] = $rResult[$i]['article_length'];
                        elseif($aColumns[$j] == 'starting_date')
                            $row[] = date("d/m/Y", strtotime($rResult[$i]['starting_date']));
                        elseif($aColumns[$j] == 'num_of_articles')
                            $row[] = $rResult[$i]['num_of_articles'];
                        elseif($aColumns[$j] == 'selling_price')
                            $row[] = $rResult[$i]['selling_price'];
                        elseif($aColumns[$j] == 'margin_before_signature')
                            $row[] = $rResult[$i]['margin_before_signature'];
                        elseif($aColumns[$j] == 'margin_after_signature')
                            $row[] = $rResult[$i]['margin_after_signature'];
                        elseif($aColumns[$j] == 'action')
                        {
                            $row[] = '<a href="/mission/add-mission?submenuId=ML2-SL27&id='.$rResult[$i]['id'].'" class="hint--left hint--info" data-hint="Edit mission"><img class="splashy-application_windows_edit"> </a>';
                        }
                        elseif($aColumns[$j] == 'client_name')
                            $row[] = utf8_encode($rResult[$i]['client_name']);
                        elseif($aColumns[$j] == 'contract_name')
                            $row[] = utf8_encode($rResult[$i]['contract_name']);

                        else
                            $row[] = $rResult[$i][ $aColumns[$j] ];
                    }
                }
                $output['aaData'][] = $row;
                $count++;
            }
        }
        // print_r($output);  exit;
        echo json_encode( $output );

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
        $obj = new Ep_Contract_Contract() ;
        $prices = $obj->getPrices($params['col'], $params['val']);
        if(!$prices)    exit('NA#NA#NA');
        $prices = array_map('trim', $prices);
        exit(number_format($prices['m1'], 2, '.', '') . '#' . number_format($prices['m3'], 2, '.', '') . '#' . number_format($prices['m'], 2, '.', ''));
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
        $options = '<select name="client" id="client" class="chzn_a" data-placeholder="S&eacute;lectionner"><option value="">S&eacute;lectionner</option>';
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
        exit($options.'</select>');
    }

    public function adduserAction()
    {
        //echo 'filef';print_r($_FILES);exit;
        $user_det=$this->_request->getParams();
        $user_obj=new Ep_User_User();
        $clident=$user_obj->InsertnewUser($user_det);
        //$user_det['clpid']
        $uploaddir = '/home/sites/site5/web/FO/clientprofile_temp/templogo/' ;
        $uploaddir_ = '/home/sites/site5/web/FO/profiles/clients/logos/'.$clident.'/' ;
        
        if(!is_dir($uploaddir_))
        {
            mkdir($uploaddir_,TRUE);
            chmod($uploaddir_,0777);
        }

        if(file_exists($uploaddir.$user_det['clpid'].'.png')){
            copy($uploaddir.$user_det['clpid'].'.png', $uploaddir_ . $clident.'.png');
            unlink($uploaddir.$user_det['clpid'].'.png');
        }
        if(file_exists($uploaddir.$user_det['clpid'].'_global.png')){
            copy($uploaddir.$user_det['clpid'].'_global.png', $uploaddir_ . $clident.'_global.png');
            unlink($uploaddir.$user_det['clpid'].'_global.png');
        }
        if(file_exists($uploaddir.$user_det['clpid'].'_global1.png')){
            copy($uploaddir.$user_det['clpid'].'_global1.png', $uploaddir_ . $clident.'_global1.png');
            unlink($uploaddir.$user_det['clpid'].'_global1.png');
        }
        
        exit($clident);
    }

    /*Upload Profile Photo*/
    public function uploadprofilepicAction()
    {      
        $uploaddir = '/home/sites/site5/web/FO/clientprofile_temp/templogo/' ;
        $client_id = ''.mt_rand(1, 99999) ;
        
        if(!is_dir($uploaddir))
        {   
            mkdir($uploaddir,0777);
            chmod($uploaddir,0777);
        }
        $file = $uploaddir.$client_id.".png";
        $file_global1= $uploaddir.$client_id."_global.png";
        $file_global2= $uploaddir.$client_id."_global1.png";
        list($width, $height)  = getimagesize($_FILES['uploadpic']['tmp_name']);

        if($width>=90 || $height>=90)
        {
            if (move_uploaded_file($_FILES['uploadpic']['tmp_name'], $file))
            {
                chmod($file,0777);
                $newimage_crop= new EP_User_Image();
                $newimage_crop->load($file);
                list($width, $height) = getimagesize($file);
                if($width>$height)
                    $newimage_crop->resizeToWidth(73);
                elseif($height>$width)
                    $newimage_crop->resizeToHeight(73);
                else
                    $newimage_crop->resize(73,73);
                    
                $newimage_crop->save($file_global1);
                chmod($file_global1,0777);
                
                //90
                $newimage_crop1= new EP_User_Image();
                $newimage_crop1->load($file);
                list($width, $height) = getimagesize($file);
                if($width>$height)
                    $newimage_crop1->resizeToWidth(90);
                elseif($height>$width)
                    $newimage_crop1->resizeToHeight(90);
                else
                    $newimage_crop1->resize(90,90);

                $newimage_crop1->save($file_global2);
                chmod($file_global2,0777);
                
                $array=array("status"=>"success","clpid"=>$client_id, 'filename'=>$_FILES['uploadpic']['name']);
                echo json_encode($array);
            }
            else
            {
                $array=array("status"=>"error"  );
                echo json_encode($array);
            }
        }
    }
}
