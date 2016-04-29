<?php
/*
 * IndexController - The default controller class
 *
 * @author
 * @version
 */
require_once 'Zend/Controller/Action.php';
class ProcessaoController extends Ep_Controller_Action {
    private $text_admin;
    public function init() {
        parent::init();
        $this->_view->lang = $this->_lang;
        $this->adminLogin  = Zend_Registry::get('adminLogin');
        $this->_view->userId = $this->adminLogin->userId;
        $this->sid         = session_id();
        ////if session expires/////
        if($this->adminLogin->loginName == '' && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
           echo "session expired...please <a href='http://admin-ep-test.edit-place.com/index'>click here</a> to login"; exit;
        }
        if($this->adminLogin->loginName == '' && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
            echo "session expired...please <a href='http://admin-ep-test.edit-place.com/index'>click here</a> to login"; exit;
        }

    }
    ///old function///
	public function missionLibertyListAction()
    {
        $del_obj = new Ep_Delivery_Delivery();
        $art_obj = new EP_Delivery_Article();
        $ao_newaos= $del_obj->getNewNonpremAos();
        if($ao_newaos!= 'NO')
        {
            foreach($ao_newaos as $key=>$value)
            {
                $aoList[$value['id']]=strtoupper($value['title']);
            }
        }
        if($aoList)
            asort($aoList);
        if($aoList)
            array_unshift($aoList, "S&eacute;lectionner");
        $this->_view->aoList = $aoList;
        $ao_params=$this->_request->getParams();
        if($ao_params['search'] == 'search')
        {
            $condition['search'] = $ao_params['search'];
            $condition['aoId'] = $ao_params['aoId'];
            $condition['clientId'] = $ao_params['clientId'];
            $condition['startdate'] = $ao_params['startdate'];
            $condition['enddate'] = $ao_params['enddate'];
        }
        $res= $del_obj->nonPremAosList($condition);
        if ($res != "NO")
            $this->_view->paginator = $res;
        else
            $this->_view->nores = "true";
        $this->_view->render("processao_newaoslist");
    }
    // display the new liberte aos and results fetched with the ajax pagination //
    public function liberteNewaosAction()
    {
        $this->_view->render("processao_liberte-newaos");
    }
    // display the ongoing liberte aos and results fetched with the ajax pagination //
    public function liberteOngoingaosAction()
    {
        $this->_view->render("processao_liberte-ongoingaos");
    }
    // display the valid liberte aos and results fetched with the ajax pagination //
    public function liberteValidaosAction()
    {
        $this->_view->render("processao_liberte-validaos");
    }
    // display the refused liberte aos and results fetched with the ajax pagination //
    public function liberteRefusedaosAction()
    {
        $this->_view->render("processao_liberte-refusedaos");
    }
    // display the canceled liberte aos and results fetched with the ajax pagination //
    public function liberteCancelledaosAction()
    {
        $this->_view->render("processao_liberte-cancelledaos");
    }
    // for all the liberte aos fetch the through ajax and forming the data ///
    public function loadliberteallaosAction()
    {
        $libParams=$this->_request->getParams();
        $del_obj = new Ep_Delivery_Delivery();
        $art_obj = new EP_Delivery_Article();
        $arthist_obj = new Ep_Delivery_ArticleHistory();
        $part_obj = new EP_Participation_Participation();
        $user_obj = new EP_User_User();

        /// defining the columsn in the table ///
        if($libParams['type'] == 'new')
            $aColumns = array('id', 'title','clientName','created_at','deltype','language','content_type','delId','status_bo',
                'actions', 'cost');
        elseif($libParams['type'] == 'refuse')
            $aColumns = array('id', 'title','clientName','created_at','deltype','language','content_type','delId','status_bo','paid_status',
                'actions','cost');
        else
            $aColumns = array('id', 'title','clientName','created_at','deltype','language','content_type','delId','status_bo','contributors',
                'paid_status','actions','cost');
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
                    if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ] == 'deltype')
                      break;
                    if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ] == 'contributors')
                        break;
                    if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ] == 'paid_status')
                        break;
                    if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ] == 'actions')
                        break;
                    if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ] == 'cost')
                        break;
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
            for ( $i=1 ; $i<3 ; $i++ )
            {
                /*if($aColumns[$i] == 'status')
                    $aColumns[$i] = 'u.status';
                if($aColumns[$i] == 'created_at')
                    $aColumns[$i] = 'u.created_at';
                if($aColumns[$i] == 'actions')
                    break;*/

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
        $type_array=array("seo"=>"Article seo","desc"=>"Descriptifs produit","blog"=>"Article de blog","news"=>"News","guide"=>"Guide","other"=>"Autre");
        if($libParams['type'] == 'new'){  /// fetching the list of aos on type
            $type = '';
            $rResult  = $del_obj->loadgetNewNonpremAos($sWhere, $sOrder, $sLimit, $type);
        }elseif($libParams['type'] == 'active') {
            $type = 'active';
            $rResult  = $del_obj->loadgetNewNonpremAos($sWhere, $sOrder, $sLimit, $type);
        }elseif($libParams['type'] == 'valid') {
           // $rResult  = $del_obj->loadgetPaidNewAos($sWhere, $sOrder, $sLimit);
            $type = 'valid';
            $rResult  = $del_obj->loadgetNewNonpremAos($sWhere, $sOrder, $sLimit, $type);
            if($rResult!= 'NO')
            {
                /*foreach($rResult as $key=>$value)
                {
                    $paidartscount = $art_obj->getPaidArticleCount($rResult[$key]['id']);
                    if($paidartscount[0]['paidartcount'] == $rResult[$key]['total_article'])
                        $aoList[]=$rResult[$key]['id'];
                }
                if($aoList != '')
                    $rResult = $del_obj->getNewNonpremPublishAos($aoList);*/
            }
        }elseif($libParams['type'] == 'refuse') {
            $type = 'refuse';
            $rResult  = $del_obj->loadgetNewNonpremAos($sWhere, $sOrder, $sLimit, $type);
        }elseif($libParams['type'] == 'cancel') {
            $type = 'cancel';
            $rResult  = $del_obj->loadgetNewNonpremAos($sWhere, $sOrder, $sLimit, $type);
        }
        if($rResult != "NO")
        {
            foreach($rResult as $key=>$value)  // forming the result set and sent to view ///
            {
                $details = $del_obj->getDeliveryDetails($rResult[$key]['id']);
                $rResult[$key]['paid_status'] = $details[0]['paid_status'];
                $rResult[$key]['deltype'] = $type_array[$details[0]['type']];
                $rResult[$key]['content_type'] = $details[0]['product'];
                $rResult[$key]['language']  = $this->language_array[$details[0]['language']];
                $percentage = $details[0]['contrib_percentage'];
                $totalprice = $details[0]['price_final'];
                $contributorcost = ($percentage / 100) * $totalprice;
                $rResult[$key]['cost']=$details[0]['price_final']-$contributorcost ;
                $contributors = $part_obj->getLibertyMissionsParts($rResult[$key]['id']);
                if($details[0]['liberte_action_by'] != ''){
                    $bouserdetials = $user_obj->getAllUsersDetails($details[0]['liberte_action_by']);
                    $bouser = $bouserdetials[0]['login'];
                }
                if($contributors[0]['partCount'] != 1){
                    $contributor = $contributors[0]['partCount'];
                    $contributor_id = $contributors[0]['partCount'];

                }
                else{
                    $contributor = $contributors[0]['first_name']." ".$contributors[0]['last_name'];
                    $contributor_id = $contributors[0]['contributor_id'];
                }

                $rResult[$key]['contributors'] = $contributor;
                $rResult[$key]['contributor_id'] = $contributor_id;
                $lastStatus = $part_obj->getLibMissionsPartsStatus($details[0]['id']);
                if($libParams['type'] == 'new')////when new liberte aos arrived///
                {
                    $rResult[$key]['latestStatus'] = "New";
                    if($details[0]['id'] != '')
                    {
                        $lastHistory = $arthist_obj->getLatestHistoryStatus($details[0]['id']);// echo $lastHistory[0]['action_sentence']; print_r($lastHistory); exit;
                        if($lastHistory != 'NO')
                            $rResult[$key]['latestStatus'] = "New";//$lastHistory[0]['action_sentence'];//commenting since nothing is returned from table
                        else
                            $rResult[$key]['latestStatus'] = "New";
                    }
                    else
                        $rResult[$key]['latestStatus'] = "New";
                }
                elseif($libParams['type'] == 'active') ////ongoing liberte aos///
                {
                    if($details[0]['id'] != '')
                    {
                        $today = date("Y-m-d");
                        $date = new DateTime();
                        $timestampnow = $date->getTimestamp();
                        $lastStatus = $part_obj->getLibMissionsPartsStatus($details[0]['id']);// echo $lastHistory[0]['action_sentence']; print_r($lastHistory); exit;
                        if($lastStatus != 'NO')
                        {  //echo "<br>--".$details[0]['participation_expires']; echo "<br>---".$timestampnow;

                            $datetime1 = new DateTime(date("Y-m-d", strtotime($lastStatus[0]['created_at'])));
                            $datetime2 = new DateTime($today);
                            $interval = $datetime1->diff($datetime2);
                            $days = $interval->format('%a');
                            if($lastStatus[0]['status'] == 'bid_nonpremium' && $details[0]['participation_expires'] > $timestampnow)
                                $rResult[$key]['latestStatus'] = "Participations en cours";
                            elseif($lastStatus[0]['status'] == 'bid_nonpremium')
                                $rResult[$key]['latestStatus'] = "En s&eacute;lection de profil";
                            elseif($lastStatus[0]['status'] == 'bid')
                                $rResult[$key]['latestStatus'] = "En cours de redaction";
                            elseif(($lastStatus[0]['status'] == 'bid' || $lastStatus[0]['status'] == 'time_out') && $lastStatus[0]['article_submit_expires'])
                                $rResult[$key]['latestStatus'] = "non envoy&eacute;";
                            elseif($lastStatus[0]['status'] == 'under_study' && $details[0]['paid_status'] == 'notpaid' && $days < 7)
                                $rResult[$key]['latestStatus'] = "Article upload&eacute;";
                            elseif($lastStatus[0]['status'] == 'under_study' && $details[0]['paid_status'] == 'notpaid' && $days >= 7)
                                $rResult[$key]['latestStatus'] = "Unpaid";
                            /*elseif($lastStatus[0]['status'] == 'under_study' && $details[0]['paid_status'] == 'paid')
                                $rResult[$key]['latestStatus'] = "Paiement effectu&eacute;";*/
                            elseif($lastStatus[0]['status'] == 'disapprove_client')
                                $rResult[$key]['latestStatus'] = "Refus client";
                            /*elseif($lastStatus[0]['status'] == 'published')
                                $rResult[$key]['latestStatus'] = "Facture g&eacute;n&eacute;r&eacute;e";*/
                            elseif($lastStatus[0]['status'] == 'closed_client_temp')
                                $rResult[$key]['latestStatus'] = "Ferm&eacute; client";
                            else
                                $rResult[$key]['latestStatus'] = "no partipation";
                        }
                        elseif($details[0]['participation_expires'] > $timestampnow)
                            $rResult[$key]['latestStatus'] = "No Selection";
                        else
                            $rResult[$key]['latestStatus'] = "no partipation";
                    }
                    else
                        $rResult[$key]['latestStatus'] = "NILL";
                }
                /*elseif($lastStatus[0]['status']='' || $libParams['type'] == 'active')  /////valiteded liberted aos (published)//
                {
                    $rResult[$key]['latestStatus'] = "Valid&eacute";
                    if($details[0]['status_bo'] == 'active' && $details[0]['paid_status'] == 'paid')
                        $rResult[$key]['latestStatus'] = "Paiement effectu&eacute;";
                    elseif($details[0]['status_bo'] == 'valid' && $details[0]['paid_status'] == 'paid')
                        $rResult[$key]['latestStatus'] = "Facture g&eacute;n&eacute;r&eacute;e";
                }*/
                elseif($libParams['type'] == 'valid' || $libParams['type'] == 'active')  /////valiteded liberted aos (published)//
                {
                    $rResult[$key]['latestStatus'] = "Valid&eacute";
                    if($details[0]['status_bo'] == 'active' && $details[0]['paid_status'] == 'paid')
                        $rResult[$key]['latestStatus'] = "Paiement effectu&eacute;";
                    elseif($details[0]['status_bo'] == 'valid' && $details[0]['paid_status'] == 'paid')
                        $rResult[$key]['latestStatus'] = "Facture g&eacute;n&eacute;r&eacute;e";
                }
                elseif($libParams['type'] == 'refuse')  /////refused liberted aos //
                {
                    $rResult[$key]['latestStatus'] = "refus&eacute; par ".$bouser;
                }
                elseif($libParams['type'] == 'cancel') ////cancel liberte aos///
                {
//                    if($details[0]['id'] != '')
//                    {
//                        $lastHistory = $arthist_obj->getLatestHistoryStatus($details[0]['id']);// echo $lastHistory[0]['action_sentence']; print_r($lastHistory); exit;
//                        if($lastHistory != 'NO')
//                            $rResult[$key]['latestStatus'] = "Cancel par ".$bouser;//$lastHistory[0]['action_sentence'];
//                        else
//                            $rResult[$key]['latestStatus'] = "Cancel par ".$bouser;
//                    }
//                    else
                        $rResult[$key]['latestStatus'] = "Cancel par ".$bouser;
                }
            }
        }
        $rResultcount = count($rResult);
        /////total count
        $sLimit = "";
        /*if($libParams['type'] == 'valid')
            $countaos  = $del_obj->loadgetPaidNewAos($sWhere, $sOrder, $sLimit);
        else*/
        $countaos  = $del_obj->loadgetNewNonpremAos($sWhere, $sOrder, $sLimit, $type);
        $iTotal = count($countaos);

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );
        $count = 1;
        if($rResult != 'NO')
        {
            $temp = $rResult;
            for( $i=0 ; $i<$rResultcount; $i++)
            {
                $row = array();
                for ( $j=0 ; $j<count($aColumns) ; $j++ )
                {
                    if($j == 0)
                        $row[] = $count;
                    else
                    {
                        /* $aColumns = array('title','client','created_at','deltype','language','content_type','id','status_bo','contributors',
                             'paid_status','quoteid', 'cost');*/
                        if($aColumns[$j] == 'title')
                            $row[] = '<a data-target="#artprofile" role="button" href="/processao/article-profiles?submenuId=ML2-SL2&aoId='.$rResult[$i]['id'].'">'.utf8_encode($rResult[$i]['title']).'</a>';
                        elseif($aColumns[$j] == 'clientName')
                            $row[] = '<a href="/user/client-edit?submenuId=ML10-SL2&tab=viewclient&userId='.$rResult[$i]['user_id'].'" class="hint--left hint--info">'.$rResult[$i]['clientName'].'</a>';
                        elseif($aColumns[$j] == 'created_at')
                            $row[] = date("d-m-Y", strtotime($rResult[$i]['created_at']));
                        elseif($aColumns[$j] == 'deltype')
                            $row[] = $rResult[$i]['deltype'];
                        elseif($aColumns[$j] == 'language')
                            $row[] = utf8_encode($rResult[$i]['language']);
                        elseif($aColumns[$j] == 'content_type')
                            $row[] = $rResult[$i]['content_type'];
                        elseif($aColumns[$j] == 'delId')
                            $row[] = '<a href="/ongoing/ao-history?ao_id='.$rResult[$i]['delId'].'"  data-toggle="modal" data-hint="history" data-target="#ao_history" ><i class="splashy-information"></i></a>';
                        elseif($aColumns[$j] == 'status_bo'){
                            if($rResult[$i]['comments'] != NULL || $rResult[$i]['comments'] != '') {
                                //$row[] = '<a class="hint--bottom hint--info" data-hint="' . $rResult[$i]['comments'] . '">' . $rResult[$i]['latestStatus'] . '</a>';
                                $row[] = '<a class="pop_over" data-placement="left" data-original-title="Comments" data-html="true" data-content="' . $rResult[$i]['comments'] . '">' . $rResult[$i]['latestStatus'] . '</a>';
                            }
                            else
                                $row[] = $rResult[$i]['latestStatus'];
                        }

                        elseif($aColumns[$j] == 'contributors' && $libParams['type'] != 'new')
                            if($rResult[$i]["contributors"] == '0')
                                $row[] = '<span class="label label-important">'.$rResult[$i]["contributors"].'</span>';
                            else
                                $row[] = '<a  data-hint="Participation details" onClick="refreshPop('.$rResult[$i]['delId'].');" ><i class="splashy-group_green"></i></a> <span class="label label-important"><a target="_blank" href="/user/contributor-edit?submenuId=ML10-SL6&amp;tab=viewcontrib&amp;userId='.$rResult[$i]["contributor_id"].'">'.$rResult[$i]["contributors"].'</a></span>';
                        elseif($aColumns[$j] == 'paid_status' && ($libParams['type'] != 'new' || $libParams['type'] != 'refuse'))
                            $row[] = $rResult[$i]['paid_status'];
                        elseif($aColumns[$j] == 'actions'){   // echo  $rResult[$i]['identifier']; exit;
                            $rowvar = '<a id="editaopop" href="/processao/showpraoinfo?aoid='.$rResult[$i]['id'].'&mission_type='.$libParams['type'].'" &function=edit" data-target="#editao" data-toggle="modal" data-hint="Edit / Publish"><i class="splashy-pencil"></i></a>
                                <a class="hint--left hint--danger" data-hint="split mission" href="/processao/mission-split?submenuId=ML2-SL1&aoid='.$rResult[$i]['id'].'"><i class="splashy-diamonds_4"></i></a>
                                <a class="hint--left hint--danger" data-hint="Quote info" href="/BO/downloadpremiumquote.php?id='.$rResult[$i]['quoteid'].'"><i class="splashy-download"></i></a>';
                                if($libParams['type'] == 'active')
                                    $rowvar.= '<a class="hint--left hint--danger" data-hint="Extent Time" data-target="#extendlibertemission" data-toggle="modal" href="/processao/extend-liberte-mission-time?aoId='.$rResult[$i]['id'].'"><i class="splashy-sprocket_dark"></i></a>';

                            $row[] = $rowvar;
                        }
//                        elseif($aColumns[$j] == 'comments')
//                            $row[] = $rResult[$i]['comments'];

                        elseif($aColumns[$j] == 'cost')
                            $row[] = round($rResult[$i]['cost'], 2);
                        else
                            $row[] = $rResult[$i][ $aColumns[$j] ];
                    }
                }
                $output['aaData'][] = $row;
                $count++;
            }
        }
        echo json_encode( $output );
    }
    /// to display contributor details who participated in liberte ao  ///
    public function contribDetialsAction()
    {
        $aoparams=$this->_request->getParams();
        $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
        $deldetails =  $delivery_obj->getAllContributorPartOnLiberteAo($aoparams["ao_id"]);
        //print_r($deldetails);
        if($deldetails != 'NO')
        {
            foreach($deldetails as $key=>$value)
            {
                $deldetails[$key]['pricewithmargin'] = ceil(($deldetails[$key]['price_user'] / $deldetails[$key]['contrib_percentage'])*100);
                $deldetails[$key]['margin'] = 100 - $deldetails[$key]['contrib_percentage'];
            }
        }
        $this->_view->userdetials = $deldetails;
        $this->_view->render("processao_liberte_contributorpopup");
    }

    public function newaosListAction()
    {
        $del_obj = new Ep_Delivery_Delivery();
        $art_obj = new EP_Delivery_Article();
        $arthist_obj = new Ep_Delivery_ArticleHistory();
        $part_obj = new EP_Participation_Participation();
        $ao_newaos= $del_obj->getNewNonpremAos('');
        $ao_ongoingaos= $del_obj->getNewNonpremAos('active');
        $ao_validatedaos = $del_obj->getPaidNewAos();
        $ao_refusedaos = $del_obj->getNewNonpremAos('refuse');
        $ao_cancelledaos = $del_obj->getNewNonpremAos('cancel');
        $type_array=array("seo"=>"Article seo","desc"=>"Descriptifs produit","blog"=>"Article de blog","news"=>"News","guide"=>"Guide","other"=>"Autre");
        if($ao_validatedaos!= 'NO')
        {
            foreach($ao_validatedaos as $key=>$value)
            {
                $paidartscount = $art_obj->getPaidArticleCount($ao_validatedaos[$key]['id']);
                if($paidartscount[0]['paidartcount'] == $ao_validatedaos[$key]['total_article'])
                    $aoList[]=$ao_validatedaos[$key]['id'];
            }
            if($aoList != '')
                $ao_validatedaos = $del_obj->getNewNonpremPublishAos($aoList);
        }

       /* if($ao_newaos!= 'NO')
        {
            foreach($ao_newaos as $key=>$value)
            {
                $aoList[$value['id']]=strtoupper($value['title']);
            }
        }
        if($aoList)
            asort($aoList);
        if($aoList)
            array_unshift($aoList, "S&eacute;lectionner");
        $this->_view->aoList = $aoList;
        $ao_params=$this->_request->getParams();
        if($ao_params['search'] == 'search')
        {
            $condition['search'] = $ao_params['search'];
            $condition['aoId'] = $ao_params['aoId'];
            $condition['clientId'] = $ao_params['clientId'];
            $condition['startdate'] = $ao_params['startdate'];
            $condition['enddate'] = $ao_params['enddate'];
        }*/
       // $res= $del_obj->nonPremAosList($condition);
        if($ao_newaos != "NO")
        {  // print_r($ao_newaos); exit;
            foreach($ao_newaos as $key=>$value)
            {
                $details = $del_obj->getDeliveryDetails($ao_newaos[$key]['id']);
                $ao_newaos[$key]['paid_status'] = $details[0]['paid_status'];
                $ao_newaos[$key]['deltype'] = $type_array[$details[0]['type']];
                $ao_newaos[$key]['content_type'] = $details[0]['content_type'];
                $ao_newaos[$key]['language']  = $this->language_array[$details[0]['language']];
                $percentage = $details[0]['contrib_percentage'];
                $totalprice = $details[0]['price_final'];
                $contributorcost = ($percentage / 100) * $totalprice;
                $ao_newaos[$key]['cost']=$details[0]['price_final']-$contributorcost ;
                $contributors = $part_obj->getLibertyMissionsParts($ao_newaos[$key]['id']);
                if($contributors[0]['partCount'] != 1)
                    $contributor = $contributors[0]['partCount'];
                else
                    $contributor = $contributors[0]['first_name']." ".$contributors[0]['last_name'];

                $ao_newaos[$key]['contributors'] = $contributor;
                if($details[0]['id'] != '')
                {
                    $lastHistory = $arthist_obj->getLatestHistoryStatus($details[0]['id']);// echo $lastHistory[0]['action_sentence']; print_r($lastHistory); exit;
                    if($lastHistory != 'NO')
                        $ao_newaos[$key]['latestStatus'] = $lastHistory[0]['action_sentence'];
                    else
                        $ao_newaos[$key]['latestStatus'] = "NILL";
                }
                else
                    $ao_newaos[$key]['latestStatus'] = "NILL";
            }
            $this->_view->paginator = $ao_newaos;
        }
        if($ao_ongoingaos != "NO")
        {
            foreach($ao_ongoingaos as $key=>$value)
            {
                $details = $del_obj->getDeliveryDetails($ao_ongoingaos[$key]['id']);
                $ao_ongoingaos[$key]['paid_status'] = $details[0]['paid_status'];
                $ao_ongoingaos[$key]['deltype'] = $type_array[$details[0]['type']];
                $ao_ongoingaos[$key]['content_type'] = $details[0]['content_type'];
                $ao_ongoingaos[$key]['language']  = $this->language_array[$details[0]['language']];
                $percentage = $details[0]['contrib_percentage'];
                $totalprice = $details[0]['price_final'];
                $contributorcost = ($percentage / 100) * $totalprice;
                $ao_ongoingaos[$key]['cost']=$details[0]['price_final']-$contributorcost ;
                $contributors = $part_obj->getLibertyMissionsParts($ao_ongoingaos[$key]['id']); //print_r($contributors); exit;
                if($contributors[0]['partCount'] != 1)
                    $contributor = $contributors[0]['partCount'];
                else
                    $contributor = $contributors[0]['first_name']." ".$contributors[0]['last_name'];
                $ao_ongoingaos[$key]['contributors'] = $contributor;
                if($details[0]['id'] != '')
                {
                    $lastStatus = $part_obj->getLibMissionsPartsStatus($details[0]['id']);// echo $lastHistory[0]['action_sentence']; print_r($lastHistory); exit;
                    if($lastStatus != 'NO')
                    {
                        if($lastStatus[0]['status'] == 'bid_nonpremium')
                            $ao_ongoingaos[$key]['latestStatus'] = "En s&eacute;lection de profil";
                        if($lastStatus[0]['status'] == 'bid')
                            $ao_ongoingaos[$key]['latestStatus'] = "En cours de redaction";
                        if($lastStatus[0]['status'] == 'under_study')
                            $ao_ongoingaos[$key]['latestStatus'] = "En relecture client";
                        if($lastStatus[0]['status'] == 'disapprove_client')
                            $ao_ongoingaos[$key]['latestStatus'] = "Refus client";
                        if($lastStatus[0]['status'] == 'published')
                            $ao_ongoingaos[$key]['latestStatus'] = "Valid&eacute;";
                        if($lastStatus[0]['status'] == 'closed_client_temp')
                            $ao_ongoingaos[$key]['latestStatus'] = "Ferm&eacute; client";
                    }
                    else
                        $ao_ongoingaos[$key]['latestStatus'] = "Participations en cours";
                }
                else
                    $ao_ongoingaos[$key]['latestStatus'] = "NILL";
            }
            $this->_view->ongoinglist = $ao_ongoingaos;
        }
        if($ao_refusedaos != "NO")
        {
            foreach($ao_refusedaos as $key=>$value)
            {
                $details = $del_obj->getDeliveryDetails($ao_refusedaos[$key]['id']);

                    $ao_refusedaos[$key]['paid_status'] = $details[0]['paid_status'];
                    $ao_refusedaos[$key]['deltype'] = $type_array[$details[0]['type']];
                    $ao_refusedaos[$key]['content_type'] = $details[0]['content_type'];
                    $ao_refusedaos[$key]['language']  = $this->language_array[$details[0]['language']];
                    $percentage = $details[0]['contrib_percentage'];
                    $totalprice = $details[0]['price_final'];
                    $contributorcost = ($percentage / 100) * $totalprice;
                    $ao_refusedaos[$key]['cost']=$details[0]['price_final']-$contributorcost ;
                    $contributors = $part_obj->getLibertyMissionsParts($ao_refusedaos[$key]['id']);
                    if($contributors[0]['partCount'] != 1)
                        $contributor = $contributors[0]['partCount'];
                    else
                        $contributor = $contributors[0]['first_name']." ".$contributors[0]['last_name'];
                    $ao_refusedaos[$key]['contributors'] = $contributor;
                    /*if($details[0]['id'] != '')
                    {
                        $lastHistory = $arthist_obj->getLatestHistoryStatus($details[0]['id']);// echo $lastHistory[0]['action_sentence']; print_r($lastHistory); exit;
                        if($lastHistory != 'NO')
                            $ao_refusedaos[$key]['latestStatus'] = $lastHistory[0]['action_sentence'];
                        else
                            $ao_refusedaos[$key]['latestStatus'] = "NILL";
                    }
                    else*/
                    $ao_refusedaos[$key]['latestStatus'] = "refus&eacute; par BO user";
            }
            $this->_view->refusedlist = $ao_refusedaos;
        }
        if($ao_validatedaos != "NO")
        {
            foreach($ao_validatedaos as $key=>$value)
            {
                $details = $del_obj->getDeliveryDetails($ao_validatedaos[$key]['id']);
               /* $paidartscount = $art_obj->getPaidArticleCount($ao_validatedaos[$key]['id']);
                if($paidartscount == $details[0]['total_article'])
                {*/
                    $ao_validatedaos[$key]['paid_status'] = $details[0]['paid_status'];
                    $ao_validatedaos[$key]['deltype'] = $type_array[$details[0]['type']];
                    $ao_validatedaos[$key]['content_type'] = $details[0]['content_type'];
                    $ao_validatedaos[$key]['language']  = $this->language_array[$details[0]['language']];
                    $percentage = $details[0]['contrib_percentage'];
                    $totalprice = $details[0]['price_final'];
                    $contributorcost = ($percentage / 100) * $totalprice;
                    $ao_validatedaos[$key]['cost']=$details[0]['price_final']-$contributorcost ;
                    $contributors = $part_obj->getLibertyMissionsParts($ao_validatedaos[$key]['id']);
                    if($contributors[0]['partCount'] != 1)
                        $contributor = $contributors[0]['partCount'];
                    else
                        $contributor = $contributors[0]['first_name']." ".$contributors[0]['last_name'];
                    $ao_validatedaos[$key]['contributors'] = $contributor;
                $lastStatus = $part_obj->getLibMissionsPartsStatus($details[0]['id']);// echo $lastHistory[0]['action_sentence']; print_r($lastHistory); exit;
                if($lastStatus != 'NO')
                {
                    if($lastStatus[0]['status'] == 'bid_nonpremium')
                        $ao_validatedaos[$key]['latestStatus'] = "En s&eacute;lection de profil";
                    if($lastStatus[0]['status'] == 'bid')
                        $ao_validatedaos[$key]['latestStatus'] = "En cours de redaction";
                    if($lastStatus[0]['status'] == 'under_study')
                        $ao_validatedaos[$key]['latestStatus'] = "En relecture client";
                    if($lastStatus[0]['status'] == 'disapprove_client')
                        $ao_validatedaos[$key]['latestStatus'] = "Refus client";
                    if($lastStatus[0]['status'] == 'published')
                        $ao_validatedaos[$key]['latestStatus'] = "Valid&eacute;";
                    if($lastStatus[0]['status'] == 'closed_client_temp')
                        $ao_validatedaos[$key]['latestStatus'] = "Ferm&eacute; client";
                }
                else
                    $ao_validatedaos[$key]['latestStatus'] = "Participations en cours";
            }
             //   }
            $this->_view->validatedlist = $ao_validatedaos;
        }
        if($ao_cancelledaos != "NO")
        {
            foreach($ao_cancelledaos as $key=>$value)
            {
                $details = $del_obj->getDeliveryDetails($ao_cancelledaos[$key]['id']);
                $ao_cancelledaos[$key]['paid_status'] = $details[0]['paid_status'];
                $ao_cancelledaos[$key]['deltype'] = $type_array[$details[0]['type']];
                $ao_cancelledaos[$key]['content_type'] = $details[0]['content_type'];
                $ao_cancelledaos[$key]['language']  = $this->language_array[$details[0]['language']];
                $percentage = $details[0]['contrib_percentage'];
                $totalprice = $details[0]['price_final'];
                $contributorcost = ($percentage / 100) * $totalprice;
                $ao_cancelledaos[$key]['cost']=$details[0]['price_final']-$contributorcost ;
                $contributors = $part_obj->getLibertyMissionsParts($ao_cancelledaos[$key]['id']);
                if($contributors[0]['partCount'] != 1)
                    $contributor = $contributors[0]['partCount'];
                else
                    $contributor = $contributors[0]['first_name']." ".$contributors[0]['last_name'];
                $ao_cancelledaos[$key]['contributors'] = $contributor;
                if($details[0]['id'] != '')
                {
                    $lastHistory = $arthist_obj->getLatestHistoryStatus($details[0]['id']);// echo $lastHistory[0]['action_sentence']; print_r($lastHistory); exit;
                    if($lastHistory != 'NO')
                        $ao_cancelledaos[$key]['latestStatus'] = $lastHistory[0]['action_sentence'];
                    else
                        $ao_cancelledaos[$key]['latestStatus'] = "NILL";
                }
                else
                    $ao_cancelledaos[$key]['latestStatus'] = "NILL";
            }
            $this->_view->cancelledlist = $ao_cancelledaos;
        }
        $this->_view->render("processao_newaoslist");
    }
    /////////////////////display pop up with detail of premium AOs///////////////////
    public function showpraoinfoAction()
    {
        $del_obj = new Ep_Delivery_Delivery();
        $client_info_obj = new Ep_User_User();
        $aoParams=$this->_request->getParams();

    //    if(!$_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')///if modal is open directly in url///
     //   { $this->_redirect("http://admin-ep-test.edit-place.com/processao/newaos-list?submenuId=ML2-SL1"); }
        if($aoParams['aoid']!=NULL)
        {
            $aoIdentifier=$aoParams['aoid'];
            $aoDetails=$del_obj->getPrAoDetails($aoIdentifier);
           //print_r($aoDetails); exit;
        }
        $aoDetails[0]['mission_type'] = $aoParams['mission_type'];//to determis the mission new/refuse/cancel etc;
        $aoDetails[0]['title'] = utf8_encode($aoDetails[0]['title']);
        $aoDetails[0]['junior_time'] = $aoDetails[0]['junior_time'];
        $aoDetails[0]['client_type'] = $aoDetails[0]['client_type'];
        $aoDetails[0]['client_id'] = $aoDetails[0]['identifier'];
        $aoDetails[0]['file_name'] = utf8_encode(str_replace("|",",",$aoDetails[0]['file_name']));
        $aoDetails[0]['filepath'] = $aoDetails[0]['filepath'];
        $aoDetails[0]['participation_time'] = $aoDetails[0]['participation_time'];
        $aoDetails[0]['submit_option'] = $aoDetails[0]['submit_option'];

        $aoDetails[0]['language'] = $aoDetails[0]['language'];
        $aoDetails[0]['category'] = $aoDetails[0]['del_category'];
        $aoDetails[0]['signtype'] = $aoDetails[0]['signtype'];
        $aoDetails[0]['min_sign'] = $aoDetails[0]['min_sign'];
        $aoDetails[0]['max_sign'] = $aoDetails[0]['max_sign'];
        $aoDetails[0]['articleid'] = $aoDetails[0]['articleid'];
        $aoDetails[0]['price_min'] = $aoDetails[0]['price_min'];
        $aoDetails[0]['price_max'] = $aoDetails[0]['price_max'];
        $aoDetails[0]['contribs_list'] = $aoDetails[0]['contribs_list'];
        $aoDetails[0]['AOtype'] = $aoDetails[0]['AOtype'];
        $aoDetails[0]['contrib_percentage'] = $aoDetails[0]['contrib_percentage'];
        $aoDetails[0]['usergrouptype'] = $this->adminLogin->type;
        $aoDetails[0]['aoid'] = $aoParams['aoid'];
        $filename= preg_replace("/\_(.*?)\./",".",$aoDetails[0]['file_name']);
        $aoDetails[0]['file_name'] =  "<a href=/BO/download_brief.php?ao_id=".$aoParams['aoid'].">".$filename."</a>";

        if($aoDetails[0]['submit_option'] == 'min')
            $aoDetails[0]['senior_time'] = $aoDetails[0]['senior_time'];
        elseif($aoDetails[0]['submit_option'] == 'hour')
            $aoDetails[0]['senior_time'] = $aoDetails[0]['senior_time']/60;
        elseif($aoDetails[0]['submit_option'] == 'day')
            $aoDetails[0]['senior_time'] = $aoDetails[0]['senior_time']/(60*24);

        if($aoDetails[0]['contribs_list'] != '')
        {
            $contribarr = explode(",", $aoDetails[0]['contribs_list']);
            $contribcount = count($contribarr);
            $contrib_info= $client_info_obj->getContributorsList();
            foreach($contrib_info as $key=>$value)
            {
                if(in_array($value['identifier'], $contribarr))
                {
                    $contrib_list[]=utf8_encode(strtoupper($value['email'].'('.$value['first_name'].','.$value['last_name'].')'))."<br>";
                    $contrib_ids[]  = $value['identifier'];
                }
            }
            $aoDetails[0]['contribsIds'] = $contrib_ids;
            $aoDetails[0]['contribsNames'] = implode(" ",$contrib_list);
            $aoDetails[0]['contribCount'] = $contribcount;
        }
        $mailDetails = $this->publishmailcontent($aoParams['aoid'], 'no');
        $mailDetails = explode("*@#",$mailDetails);
        $aoDetails[0]['mailcontent']  = $mailDetails[0];
        $aoDetails[0]['object']  = $mailDetails[1];
        $aoDetails[0]['clientmailcontent']  = $mailDetails[2];
        $aoDetails[0]['clientobject']  = $mailDetails[3];
        /////refuse mail content to client///
        $automail=new Ep_Message_AutoEmails();
        $email=$automail->getAutoEmail(6);//
        $aoDetails[0]['refuse_object'] = utf8_encode($email[0]['Object']);
        $parameters['AO_title']=$aoDetails[0]['title'];
        $emailcontent = $automail->getMailComments(NULL,6,$parameters);
        $aoDetails[0]['refuse_mailcontent'] = utf8_encode(stripslashes(html_entity_decode($emailcontent)));

        ////////////////////////////////////
        /////published mail content for successfully published to client///
        /*$automail=new Ep_Message_AutoEmails();
        $parameters['AO_title']=$aoDetails[0]['title'];
        $parameters['submitdate_bo']=date('d/m/Y H:i', $expires);
        if($aoDetails[0]['deli_anonymous']=='0')
            $parameters['article_link']="/contrib/aosearch?client_contact=".$aoDetails[0]['user_id'];
        else
            $parameters['article_link']="/contrib/aosearch?client_contact=anonymous";
        $parameters['aoname_link'] = "/contrib/aosearch";
        $parameters['clientartname_link'] = "/client/quotes?id=".$aoDetails[0]['articleid'];
        $clientemailcontent = $automail->getMailComments(NULL,5,$parameters);
        $aoDetails[0]['clientmailcontent'] = utf8_encode(stripslashes(html_entity_decode($clientemailcontent))); */

        ////////////////////////////////////
        $this->_view->aoDetails = $aoDetails;
       // if($aoParams['function'] == 'edit')
            $this->_view->render("processao_editnewaopopup");
      //  else
       //     $this->_view->render("processao_publishaopopup");
    }

    ////////to split mission into many articles/////////
    public function missionSplitAction()
    {
        $missionParams=$this->_request->getParams();
        $delivery_obj=new Ep_Delivery_Delivery();
        $articles = $delivery_obj->getArticlesOfDel($missionParams['aoid']);
        $artstitles = explode("|",$articles[0]['artTitles']);
        $artsids = explode("@",$articles[0]['artIds']);
        for($i=0; $i<=count($artstitles); $i++)
        {
            $missions[$i]= $artstitles[$i]."*".$artsids[$i];
        }
        $missions = implode("|",$missions);
        $this->_view->artgrouptitle=$missions;
        $this->_view->deltitle=$articles[0]['title'];
        $this->_view->delId=$missionParams['aoid'];
        $this->_view->clientId=$articles[0]['user_id'];

        $this->_view->render("processao_missionsplit");
    }
    ////////to split mission into many articles/////////
    public function savemissionsAction()
    {
        $missionParams=$this->_request->getParams();           // print_r($missionParams);    exit;
        $split_no =  $missionParams["split_no"];
        $delId = $missionParams['delId'];
        $delivery_obj=new Ep_Delivery_Delivery();
        $article_obj=new EP_Delivery_Article();
        $payment_obj=new Ep_Payment_Payment();
        $deldetails = $delivery_obj->getDeliveryDetails($missionParams['delId']);
        if(isset($missionParams["sub_mission"]))
        {
            $existmissioncount = $missionParams['existmissioncount'];
            for($i=1; $i<$existmissioncount; $i++)
            {
                $data = array("title"=>$missionParams['textbox'.$i]);////////updating total articles count in delivery
                $query = "id= '".$delId."'";
                $delivery_obj->updateDelivery($data,$query);
                ////////////////////////////////////////////////
                $data = array("title"=>$missionParams['textbox'.$i]);////////updating
                $query = "id= '".$missionParams['artId'.$i]."'";
                $article_obj->updateArticle($data,$query);
            }
            for($i=2; $i<=$split_no; $i++)
            {
                $delarr = array();
                $delarr['id'] =$this->createIdentifier();
                $delarr['title'] = $missionParams['textbox'.$i];
                $delarr['user_id'] = $missionParams['clientId'];

                $delarr['view_to'] = $deldetails[0]['view_to'];
                $delarr['client_type'] = $deldetails[0]['client_type'];
                $delarr['AOtype'] = $deldetails[0]['AOtype'];
                $delarr['total_article'] = 1;
                $delarr['created_by'] = "FO";
                $delarr['file_name'] = $deldetails[0]['file_name'];
                $delarr['filepath'] = $deldetails[0]['filepath'];
                $delarr['min_sign'] = $deldetails[0]['min_sign'];
                $delarr['max_sign'] = $deldetails[0]['max_sign'];
                $delarr['signtype'] = $deldetails[0]['signtype'];
                $delarr['category'] = $deldetails[0]['category'];
                $delarr['language'] = $deldetails[0]['language'];
                if($deldetails[0]['AOtype'] == 'private')
                {
                    $delarr['contribs_list'] = $deldetails[0]['contribs_list'];
                    $delarr['price_min'] = $deldetails[0]['price_min'];
                    $delarr['price_max'] = $deldetails[0]['price_max'];
                }
                $delivery_obj->insertDeliveries($delarr);

                //////////////////////////////
                $payarr['id'] =$this->createIdentifier();
                $payarr['delivery_id'] = $delarr['id'];
                $payarr['amount'] = 0;
                $payarr['pay_type'] = "BO";
                $payarr['status'] = "Paid";
                $payment_obj->insertPayments($payarr);   //print_r($payarr); exit;
                /////////////////////////////////////
                $arr['id'] =$this->createIdentifier();
                $arr['title'] = $missionParams['textbox'.$i];
                $arr['delivery_id'] = $delarr['id'];
                $arr['subjunior_time'] = $deldetails[0]['subjunior_time'];
                $arr['junior_time'] = $deldetails[0]['junior_time'];
                $arr['senior_time'] = $deldetails[0]['senior_time'];
                $arr['participation_time'] = $deldetails[0]['participation_time'];
                if($deldetails[0]['AOtype'] == 'private')
                {
                    $arr['contribs_list'] = $deldetails[0]['contribs_list'];
                    $arr['price_min'] = $deldetails[0]['price_min'];
                    $arr['price_max'] = $deldetails[0]['price_max'];
                }
                $article_obj->insertSplitArticles($arr);
            }
            $this->_helper->FlashMessenger('Successfully Mission Splited and Saved');
            $this->_redirect("processao/newaos-list?submenuId=ML2-SL1");
        }
    }
    public function createIdentifier()
    {
        $d = new Date();
        return $d->getSubDate(5,14).mt_rand(100000,999999);
    }
    //for uploading spec file
    public function uploadspecdoceditAction()
    {
        $realfilename=$_FILES['uploadfile']['name'];

        $realfilename=$_FILES['uploadfile']['name'];
        $ext=$this->findexts($realfilename);
        if($ext=="csv")
        {
            $uploaddir = '/home/sites/site9/web/FO/client_csv/';
        }
        else
        {
            $uploaddir = '/home/sites/site5/web/FO/client_spec/';
        }

        $client_id=$_REQUEST['clientid'];
        $newfilename=$client_id.".".$ext;

        if(!is_dir($uploaddir.$client_id))
        {
            mkdir($uploaddir.$client_id,0777);
            chmod($uploaddir.$client_id,0777);
        }
        $uploaddir=$uploaddir.$client_id."/";
         $bname=basename($realfilename,".".$ext)."_".uniqid().".".$ext;
        $file = $uploaddir . $bname;

        if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file))
        {
            chmod($file,0777);
            echo "success#".$bname;
        }
        else
        {
            echo "error";
        }
    }
    public function findexts ($filename="")
    {
        $filename = strtolower($filename) ;
        $exts = split("[/\\.]", $filename) ;
        $n = count($exts)-1;
        $exts = $exts[$n];
        return $exts;
    }
    //////////when a Update button is clicked on pop up /////////////////
    public function editaoAction()
    {
        $prevurl = getenv("HTTP_REFERER");
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new EP_Delivery_Article();
        $participate_obj = new EP_Participation_Participation();
        $ao_params=$this->_request->getParams();
        $deldetails =  $delivery_obj->getDeliveryDetails($ao_params["ao_edit_id"]);
        if(isset($ao_params["submit_pop_edit"]))
        {
            //$ao->senior_time=$ao_params["senior_time"];
            if($ao_params['submitoption'] == 'min' )
                $ao->senior_time=$ao_params['senior_time'];
            elseif($ao_params['submitoption'] == 'hour')
                $ao->senior_time=60*$ao_params['senior_time'];
            elseif($ao_params['submitoption'] == 'day')
                $ao->senior_time=60*24*$ao_params['senior_time'];
            //$ao->participation_time=$ao_params["participation_time"];
            if($ao_params['parttime'] == 'min' )
                $ao->participation_time=$ao_params['participation_time'];
            elseif($ao_params['parttime'] == 'hour')
                $ao->participation_time=60*$ao_params['participation_time'];
            elseif($ao_params['parttime'] == 'day')
                $ao->participation_time=60*24*$ao_params['participation_time'];

            $ao->title=$ao_params["title_edit"] ;
            if($ao_params["client_type_edit"] == 1)
                $client_type = "professional";
            else
                $client_type = "personal";
            $ao->client_type=$client_type ;
            if($_FILES['specupload']['tmp_name'] != '')
            {
                $realfilename=$_FILES['specupload']['name'];
                $ext=pathinfo($realfilename);
                $uploaddir = '/home/sites/site5/web/FO/client_spec/';

                $client_id=$ao_params["client_id"];
                $newfilename=$client_id.".".$ext["extension"];

                if(!is_dir($uploaddir.$client_id))
                {
                    mkdir($uploaddir.$client_id,0777);
                    chmod($uploaddir.$client_id,0777);
                }
                 $uploaddir=$uploaddir.$client_id."/";
                 $bname=basename($realfilename,".".$ext["extension"])."_".uniqid().".".$ext["extension"];
                 $file = $uploaddir . $bname;

                if (move_uploaded_file($_FILES['specupload']['tmp_name'], $file))
                {
                    chmod($file,0777);
                    $ao->file_name=$bname ;
                    $ao->filepath="/".$client_id."/".$bname;
                    $ao->clientid = $ao_params["client_id"];
                }
            }
            $ao->id=$ao_params["ao_edit_id"] ;
            $ao->category=$ao_params["category"] ;
            $ao->language=$ao_params["language"] ;
            $ao->signtype=$ao_params["signtype"] ;
            $ao->submit_option=$ao_params['submitoption'] ;
            $ao->min_sign=$ao_params["min_sign"] ;
            $ao->max_sign=$ao_params["max_sign"] ;
            $ao->price_min=$ao_params["price_min"] ;
            $ao->price_max=$ao_params["price_max"] ;
            $ao->contrib_percentage=$ao_params["contrib_percentage"] ;

            //$ao->contribs_list=$ao_params["contribs_list"] ;
            $ao->contribs_list=implode(",", $ao_params["contribnames"]);
            if($_FILES['specupload']['tmp_name'] != '')
            {
                $data = array("submitdate_bo"=>$ao->submitdate_bo, "title"=>$ao->title, "client_type"=>"professional", "updated_at"=>date('Y-m-d'), "contribs_list"=>$ao->contribs_list, "publish_language"=>$ao->language, "participation_time"=>$ao->participation_time,
                    "file_name"=>$ao->file_name, "filepath"=>$ao->filepath, "category"=>$ao->category, "language"=>$ao->language, "signtype"=>$ao->signtype, "min_sign"=>$ao->min_sign, "max_sign"=>$ao->max_sign, "submit_option"=>$ao->submit_option, "senior_time"=>$ao->senior_time);////////updating
            }
            else{
                $data = array("submitdate_bo"=>$ao->submitdate_bo, "title"=>$ao->title, "client_type"=>"professional", "updated_at"=>date('Y-m-d'), "contribs_list"=>$ao->contribs_list, "publish_language"=>$ao->language, "participation_time"=>$ao->participation_time,
                    "category"=>$ao->category, "language"=>$ao->language, "signtype"=>$ao->signtype, "min_sign"=>$ao->min_sign, "max_sign"=>$ao->max_sign, "submit_option"=>$ao->submit_option, "senior_time"=>$ao->senior_time);////////updating
            }

            $query = "id= '".$ao->id."'";
            $delivery_obj->updateDelivery($data,$query);
            ////update the artlcle table with partcipation time/////////
            $delartdetails = $delivery_obj->getArticlesOfDel($ao->id);
            $data = array("title"=>$ao->title, "category"=>$ao->category, "language"=>$ao->language, "publish_language"=>$ao->language,  "sign_type"=>$ao->signtype,
                    "num_min"=>$ao->min_sign, "num_max"=>$ao->max_sign, "price_min"=>$ao->price_min, "price_max"=>$ao->price_max, "contrib_percentage"=>$ao->contrib_percentage,
                    "contribs_list"=>$ao->contribs_list, "participation_time"=>$ao->participation_time, "submit_option"=>$ao->submit_option, "senior_time"=>$ao->senior_time);////////updating
            $artIds = explode("@",$delartdetails[0]['artIds']);

            for($i=0; $i<=count($artIds); $i++)
            {
                $query = "id= '".$artIds[$i]."'";
                $article_obj->updateArticle($data,$query);
            }
            $this->_helper->FlashMessenger('Successfully updated the AO details');
            $this->_redirect($prevurl);
        }

    }
    //Updating spec file path on changing client of AO
    public function moveBreifToClientFolder($filename, $clientid, $filepath)
    {
        $dir = '/home/sites/site5/web/FO/client_spec';
        if($filename!="")
        {
            $source=$dir.'/'.$filename;
            $destination=$dir.'/'.$clientid.'/'.$filename;
            if(copy($source,$destination))
                unlink($source);
        }
    }
    //////////when a publish button is clicked on pop up/////////////////
    public function publishaoAction()
    {
        $prevurl = getenv("HTTP_REFERER");
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new EP_Delivery_Article();
        $user_obj = new Ep_User_User();
        $participate_obj = new EP_Participation_Participation();
        $ao_params=$this->_request->getParams();
       // print_r($ao_params); exit;
        $deldetails =  $delivery_obj->getDeliveryDetails($ao_params["ao_id"]);
        if(isset($ao_params["submit_pop_edit"]) || isset($ao_params["submit_pop"]))///for both edit and publish//
        {
            //$ao->senior_time=$ao_params["senior_time"];
            if($ao_params['submitoption'] == 'min' )
                $ao->senior_time=$ao_params['senior_time'];
            elseif($ao_params['submitoption'] == 'hour')
                $ao->senior_time=60*$ao_params['senior_time'];
            elseif($ao_params['submitoption'] == 'day')
                $ao->senior_time=60*24*$ao_params['senior_time'];
            //$ao->participation_time=$ao_params["participation_time"];
            if($ao_params['parttime'] == 'min' )
                $ao->participation_time=$ao_params['participation_time'];
            elseif($ao_params['parttime'] == 'hour')
                $ao->participation_time=60*$ao_params['participation_time'];
            elseif($ao_params['parttime'] == 'day')
                $ao->participation_time=60*24*$ao_params['participation_time'];

            $ao->title=$ao_params["title_edit"] ;
            if($ao_params["client_type_edit"] == 1)
                $client_type = "professional";
            else
                $client_type = "personal";
            $ao->client_type=$client_type ;
            if($_FILES['specupload']['tmp_name'] != '')
            {
                $realfilename=$_FILES['specupload']['name'];
                $ext=pathinfo($realfilename);
                $uploaddir = '/home/sites/site5/web/FO/client_spec/';

                $client_id=$ao_params["client_id"];
                $newfilename=$client_id.".".$ext["extension"];

                if(!is_dir($uploaddir.$client_id))
                {
                    mkdir($uploaddir.$client_id,0777);
                    chmod($uploaddir.$client_id,0777);
                }
                $uploaddir=$uploaddir.$client_id."/";
                $bname=basename($realfilename,".".$ext["extension"])."_".uniqid().".".$ext["extension"];
                $file = $uploaddir . $bname;

                if (move_uploaded_file($_FILES['specupload']['tmp_name'], $file))
                {
                    chmod($file,0777);
                    $ao->file_name=$bname ;
                    $ao->filepath="/".$client_id."/".$bname;
                    $ao->clientid = $ao_params["client_id"];
                }
            }
            $ao->id=$ao_params["ao_id"] ;
            $ao->category=$ao_params["category"] ;
            $ao->language=$ao_params["language"] ;
            $ao->signtype=$ao_params["signtype"] ;
            $ao->type=$ao_params["type"] ;
            $ao->content_type=$ao_params["content_type"] ;
            $ao->submit_option=$ao_params['submitoption'] ;
            $ao->min_sign=$ao_params["min_sign"] ;
            $ao->max_sign=$ao_params["max_sign"] ;
            if (strstr($ao_params["price_min"], ','))
                $ao->price_min = str_replace(',', '.', $ao_params["price_min"]);
            else
                $ao->price_min = $ao_params["price_min"];

            if (strstr($ao_params["price_max"], ','))
                $ao->price_max = str_replace(',', '.', $ao_params["price_max"]);
            else
                $ao->price_max = $ao_params["price_max"];


            $ao->contrib_percentage=$ao_params["contrib_percentage"] ;

            //$ao->contribs_list=$ao_params["contribs_list"] ;
            $ao->contribs_list=implode(",", $ao_params["contribnames"]);
            if($_FILES['specupload']['tmp_name'] != '')
            {
                $data = array("submitdate_bo"=>$ao->submitdate_bo, "title"=>$ao->title, "client_type"=>"professional", "updated_at"=>date('Y-m-d'), "contribs_list"=>$ao->contribs_list, "type"=>$ao->type, "content_type"=>$ao->content_type, "participation_time"=>$ao->participation_time,
                    "file_name"=>$ao->file_name, "filepath"=>$ao->filepath, "category"=>$ao->category, "language"=>$ao->language, "signtype"=>$ao->signtype, "min_sign"=>$ao->min_sign, "max_sign"=>$ao->max_sign, "submit_option"=>$ao->submit_option, "senior_time"=>$ao->senior_time,"publish_language"=>$ao->language);////////updating
            }
            else{
                $data = array("submitdate_bo"=>$ao->submitdate_bo, "title"=>$ao->title, "client_type"=>"professional", "updated_at"=>date('Y-m-d'), "contribs_list"=>$ao->contribs_list, "type"=>$ao->type, "content_type"=>$ao->content_type, "participation_time"=>$ao->participation_time,
                    "category"=>$ao->category, "language"=>$ao->language, "signtype"=>$ao->signtype, "min_sign"=>$ao->min_sign, "max_sign"=>$ao->max_sign, "submit_option"=>$ao->submit_option, "senior_time"=>$ao->senior_time,"publish_language"=>$ao->language);////////updating
            }

            $query = "id= '".$ao->id."'";
            $delivery_obj->updateDelivery($data,$query);
            ////update the artlcle table with partcipation time/////////
            $delartdetails = $delivery_obj->getArticlesOfDel($ao->id);
            $data = array("title"=>$ao->title, "category"=>$ao->category, "language"=>$ao->language, "sign_type"=>$ao->signtype, "type"=>$ao->type,
                "num_min"=>$ao->min_sign, "num_max"=>$ao->max_sign, "price_min"=>$ao->price_min, "price_max"=>$ao->price_max, "contrib_percentage"=>$ao->contrib_percentage,
                "contribs_list"=>$ao->contribs_list, "participation_time"=>$ao->participation_time, "submit_option"=>$ao->submit_option, "senior_time"=>$ao->senior_time, "publish_language"=>$ao->language, "type"=>$ao->type);////////updating
            $artIds = explode("@",$delartdetails[0]['artIds']);

            for($i=0; $i<=count($artIds); $i++)
            {
                $query = "id= '".$artIds[$i]."'";
                $article_obj->updateArticle($data,$query);
            }
            if(!isset($ao_params["submit_pop"])) //if only edit
            {
                $this->_helper->FlashMessenger('Successfully updated the AO details');
                $this->_redirect($prevurl);
            }
        }

        if(isset($ao_params["submit_pop"]))
        {
            //////////edit code///////////////////////////
            /////////////////end of edit code//////////////////////////////////
            $ao->id=$ao_params["ao_id"] ;
            $data = array("status_bo"=>'active', "updated_at"=>date('Y-m-d'), "published_at"=>time(), "liberte_action_by"=>$this->adminLogin->userId);////////updating
            if($ao_params["publishnow"]!="yes" && $ao_params["publishtime"]!="")
            {
                $time=explode(" ",$ao_params['publishtime']);
                $dat=explode("/",$time[0]);
                $dat1=$dat[2]."-".$dat[1]."-".$dat[0]." ".$time[1].":00";
                $data["publishtime"]=strtotime($dat1);
                $data["mailsubject"]=$ao_params["object"];
                $data["mailcontent"]=$ao_params["mailcontent"];

            }
            $query = "id= '".$ao->id."'";
            $delivery_obj->updateDelivery($data,$query);
            ////update the artlcle table with partcipation time/////////
            $delartdetails = $delivery_obj->getArticlesOfDel($ao_params["ao_id"]);
            if($ao_params["publishnow"]!="yes" && $ao_params["publishtime"]!="")
                $expires=$data["publishtime"]+(60*$delartdetails[0]['participation_time']);
            else
                $expires=time()+(60*$delartdetails[0]['participation_time']);
            $data = array("participation_expires"=>$expires, "status_bo"=>'active');////////updating
            $artIds = explode("@",$delartdetails[0]['artIds']);
            for($i=0; $i<=count($artIds); $i++)
            {
                $query = "id= '".$artIds[$i]."'";
               $article_obj->updateArticle($data,$query);
            }
            /* Sending mail to client when publish **/
            $aoDetails=$delivery_obj->getPrAoDetails($ao->id);
            $autoEmails=new Ep_Message_AutoEmails();
            $parameters['AO_title']=$aoDetails[0]['title'];
            $parameters['AO_end_date']=$aoDetails[0]['delivery_date'];
            //$parameters['submitdate_bo']=$aoDetails[0]['submitdate_bo'];
            $parameters['submitdate_bo']=date('d/m/Y H:i', $expires);

            $parameters['noofarts']=$aoDetails[0]['noofarts'];
            if($aoDetails[0]['deli_anonymous']=='0')
                $parameters['article_link']="/contrib/aosearch?client_contact=".$aoDetails[0]['user_id'];
            else
                $parameters['article_link']="/contrib/aosearch?client_contact=anonymous";
            $parameters['aoname_link'] = "/contrib/aosearch";
            $parameters['clientartname_link'] = "/client/quotes?id=".$aoDetails[0]['articleid'];
            if($deldetails[0]['mail_send']=='yes')
            {
                 if($aoDetails[0]['premium_option']=='0')
                    $autoEmails->sendMailEpMailBox($aoDetails[0]['user_id'],$ao_params["clientobject"],$ao_params["clientmailcontent"], $ao_params["sendfrom"]);
                /*if($aoDetails[0]['premium_option']=='0')
                    $autoEmails->messageToEPMail($aoDetails[0]['user_id'],5,$parameters);*/
            }
            if($aoDetails[0]['mail_send_contrib']=='yes' && $ao_params["publishnow"]=="yes")
            {
                if($aoDetails[0]['priority_contributors']!="")
                {
                    $prior_contribs=explode(",",$aoDetails[0]['priority_contributors']);
                    $prior_parameters['poll_link']='<a href="/contrib/aosearch">Cliquant-ici</a>';
                    $prior_parameters['hours']=$aoDetails[0]['priority_hours'];
                    foreach($prior_contribs as $pcontrib)
                    {
                        $contrib_poll=$delivery_obj->getPollcontribDetails($aoDetails[0]['poll_id'],$pcontrib);
                        $prior_parameters['poll']=$contrib_poll[0]['title'];
                        $prior_parameters['date']=$contrib_poll[0]['poll_date'];
                        $prior_parameters['price']=$contrib_poll[0]['price_user'];
                        $autoEmails->sendMailEpMailBox($pcontrib,$ao_params["object"],$ao_params["mailcontent"], $ao_params["sendfrom"]);
                    }
                }
                if($aoDetails[0]['poll_id']=="")
                {
                    if($aoDetails[0]['AOtype']=='private')
                    {
                        $contributors=array_unique(explode(",",$aoDetails[0]['article_contribs']));
                        if(is_array($contributors) && count($contributors)>0)
                        {
                            foreach($contributors as $contributor)
                            {
                                $autoEmails->sendMailEpMailBox($contributor,$ao_params["object"],$ao_params["mailcontent"], $ao_params["sendfrom"]);
                            }
                        }
                    }
                    elseif($aoDetails[0]['AOtype']=='public')
                    {
                        if($ao_params["publishnow"]=="yes" && $ao_params["publishtime"]=="")
                        {
                            if($deldetails[0]['created_by'] != 'BO')
                            {
                                $contributors=$user_obj->getSeniorFrContributors($ao_params["language"]);
                                if(is_array($contributors) && count($contributors)>0)
                                {
                                    $sclimit=$this->getConfiguredval('sc_limit');
                                    foreach($contributors as $contributor)
                                    {
                                        $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);
                                        if($sclimit > $countofparts[0]['partscount'])
                                        {
                                            $autoEmails->sendMailEpMailBox($contributor['identifier'],$ao_params["object"],$ao_params["mailcontent"], $ao_params["sendfrom"]);
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $delviews = $delivery_obj->getDeliveryDetails($ao_params["ao_id"]);
                                $profiles = explode(",", $delviews[0]['view_to']);

                                $profiles = implode(",", $profiles);
                                $contributors=$delivery_obj->getContributorsAO('public',$aoDetails[0]['fav_category'], $profiles);
                                if(is_array($contributors) && count($contributors)>0)
                                {
                                    $jclimit=$this->getConfiguredval('jc_limit');
                                    $sclimit=$this->getConfiguredval('sc_limit');
                                    foreach($contributors as $contributor)
                                    {
                                        if($contributor['profile_type'] == 'junior' || $contributor['profile_type'] == 'sub-junior')
                                        {
                                            $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);
                                            if($jclimit > $countofparts[0]['partscount'])
                                            {
                                                $autoEmails->sendMailEpMailBox($contributor['identifier'],$ao_params["object"],$ao_params["mailcontent"], $ao_params["sendfrom"]);
                                            }
                                        }
                                        else
                                        {
                                            $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);
                                            if($sclimit > $countofparts[0]['partscount'])
                                            {
                                                $autoEmails->sendMailEpMailBox($contributor['identifier'],$ao_params["object"],$ao_params["mailcontent"], $ao_params["sendfrom"]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //Sending mail for AO creation
            $mail = new Zend_Mail();
            $body='';
            $body.='<div>
                        Hi,<br><br>
                        New AO by Name <b>'.$aoDetails[0]['title'].'</b> with End date '.$aoDetails[0]['delivery_date'].' has been
                        Confirmed by '.ucfirst($this->adminLogin->loginName).'
                        <br><br>
                        Regards,<br>
                        EP Team.
                    </div>';
            $mail->setBodyHTML($body)
                ->setFrom('contact@edit-place.com', 'Contact Edit-place')
                ->addTo('mailpearls@gmail.com')
                ->setSubject('New AO Confirmed by EP in test');
           // $mail->send();
            //Sending mail for no spec file
            if($aoDetails[0]['file_name']=="")
            {
                $mail1 = new Zend_Mail();
                $body='';
                $body.='<div>
                            Hi,<br><br>
                            New AO by Name <b>'.$aoDetails[0]['title'].'</b> has been
                            Confirmed without spec file.
                            <br><br>
                            Regards,<br>
                            EP Team.
                        </div>';
                $mail1->setBodyHTML($body)
                    ->setFrom('contact@edit-place.com', 'Contact Edit-place')
                    ->addTo('mailpearls@gmail.com')
                    ->setSubject('New AO Confirmed without spec file in test');
              //  $mail1->send();
            }
            /////////////article history////////////////
            $actparams['aoId'] = $ao_params["ao_id"];
            $actparams['stage'] = "new ao list";
            $actparams['action'] = "ao published";
            //$this->articleHistory(33,$actparams);
            /////////////end of article history////////////////
            $this->_helper->FlashMessenger('Mission libert&egrave; publi&egrave;e');
            $this->_redirect("/processao/liberte-ongoingaos?tab=ongoingaos&submenuId=ML2-SL1");
        }
        else if(isset($ao_params["refuse_pop"])) {
            $ao->id = $ao_params["ao_id"];
            $comments = $ao_params["refusalreasons"];
            $data = array("status_bo" => 'refuse', "liberte_action_by" => $this->adminLogin->userId, "liberte_comments" => $comments);////////updating
            $query = "id= '" . $ao->id . "'";
            $delivery_obj->updateDelivery($data, $query);
            $aoDetails = $delivery_obj->getPrAoDetails($ao->id);
            /*Sending mail to client when Reject **/
            if(isset($ao_params["anouncebymail"]) == false){
			
                $autoEmails = new Ep_Message_AutoEmails();
                if ($deldetails[0]['mail_send'] == 'yes') {
                    $autoEmails->sendMailEpMailBox($aoDetails[0]['user_id'], $ao_params["refuseobject"], $ao_params["refusemailcontent"], $ao_params["sendfrom"]);
                }
                ///get recent message id////
                $message = new Ep_Message_Message();
                $messageId = $message->getRecentMessageId();
                $messageId = $messageId[0]['id'];

                /////////////article history////////////////
                $actparams['aoId'] = $ao_params["ao_id"];
                $actparams['stage'] = "new ao list";
                $actparams['action'] = "ao refused";
                $actparams['mail_ticket_id'] = $messageId;
                // $this->articleHistory(47,$actparams);
                /////////////end of article history////////////////
                /*$parameters['AO_title']=$aoDetails[0]['title'];
                if($deldetails[0]['mail_send']=='yes')
                {
                    $autoEmails->messageToEPMail($aoDetails[0]['user_id'],6,$parameters);//
                }*/
            }
            $this->_redirect("/processao/liberte-refusedaos?tab=refusedaos&submenuId=ML2-SL1");
        }
        else if(isset($ao_params["cancel_pop"]))
        {
            $aoId=$ao_params["ao_id"] ;
            $comments = $ao_params["cancelreasons"];
            $data = array("status_bo"=>'cancel', "liberte_action_by"=>$this->adminLogin->userId, "liberte_comments"=>$comments);////////updating
            $query = "id= '".$aoId."'";
            $delivery_obj->updateDelivery($data,$query);
            $aoDetails=$delivery_obj->getPrAoDetails($aoId);
            /*Sending mail to client when Reject **/
            $autoEmails=new Ep_Message_AutoEmails();
            if($deldetails[0]['mail_send']=='yes')
            {
                $autoEmails->sendMailEpMailBox($aoDetails[0]['user_id'],$ao_params["cancelobject"],$ao_params["cancelmailcontent"], $ao_params["sendfrom"]);
            }
            ///get recent message id////
            $message=new Ep_Message_Message();
            $messageId = $message->getRecentMessageId();
            $messageId = $messageId[0]['id'];

            /////////////article history////////////////
            $actparams['aoId'] = $aoId;
            $actparams['stage'] = "new ao list";
            $actparams['action'] = "ao cancel";
            $actparams['mail_ticket_id'] = $messageId; //print_r($actparams); exit;
           // $this->articleHistory(48,$actparams);
            $this->_redirect("/processao/liberte-cancelledaos?tab=cancelledaos&submenuId=ML2-SL1");
        }
    }
    //////////to delete selected AOs and also individuals /////////////////
    public function deletepublishaoAction()
    {
        $prevurl = getenv("HTTP_REFERER");
        $delivery_obj = new Ep_Ao_Delivery();
        $ao_params=$this->_request->getParams();
        $ckecks = explode(',', $ao_params['hide_total']);
        for($i=0; $i<count($ckecks); $i++)
        {
            $chks = explode('_', $ckecks[$i]);
            $ckeckboxes[$i]=$chks[1];
        }
        if(isset($ao_params["delall"]))
        {
            if(isset($ao_params['select_all']) && $ao_params['select_all']=='all')
            {
                $countvar = 1;
            }
            else
            {
                $countvar = 0;
            }
            for( $i=$countvar ; $i<count($ckeckboxes); $i++)
            {
                ////udate status_bo in delivery table for delete as trash///////
                $data = array("status_bo"=>"trash");////////updating
                $query = "id= '".$ckeckboxes[$i]."'";
                $delivery_obj->updateDelivery($data,$query);
            }
            $this->_redirect($prevurl);

        }
        else if(isset($ao_params["puball"]))
        {
            if(isset($ao_params['select_all']) && $ao_params['select_all']=='all')
            {
                $countvar = 1;
            }
            else
            {
                $countvar = 0;
            }
            for( $i=$countvar; $i<count($ckeckboxes); $i++)
            {
                $aoDetails=$delivery_obj->getPrAoDetails($ckeckboxes[$i]);
                $diff = $this->datediff($aoDetails[0]['created_at'], $aoDetails[0]['delivery_date']);
                if($diff >= 5)
                {
                    $newdate = strtotime('-2 day' , strtotime($aoDetails[0]['delivery_date'])) ;
                    $newdate = date('Y-m-d', $newdate);
                }
                else
                {
                    $newdate = $aoDetails[0]['delivery_date'];
                }
                ///udate status_bo in delivery table for delete as trash///////
                $data = array("submitdate_bo"=>$newdate, "status_bo"=>"active", "published_at"=>time());////////updating
                $query = "id= '".$ckeckboxes[$i]."'";
                $delivery_obj->updateDelivery($data,$query);

                //Sending mail
                $mail = new Zend_Mail();

                $body='';
                $body.='<div>
								Hi,<br><br>
								New AO by Name <b>'.$aoDetails[0]['title'].'</b> has been
								Confirmed by '.ucfirst($this->adminLogin->loginName).'
								<br><br>
								Regards,<br>
								EP Team.
							</div>';

                $mail->setBodyHTML($body)
                    ->setFrom('contact@edit-place.com', 'Contact Edit-place')
                    ->addTo('jwolff@edit-place.com')
                    ->setSubject('New AO Confirmed by EP in test');
                $mail->send();

                //Sending mail no spec file
                if($aoDetails[0]['file_name']=="")
                {
                    $mail1 = new Zend_Mail();
                    $body='';
                    $body.='<div>
								Hi,<br><br>
								New AO by Name <b>'.$aoDetails[0]['title'].'</b> has been
								Confirmed without spec file.
								<br><br>
								Regards,<br>
								EP Team.
							</div>';

                    $mail1->setBodyHTML($body)
                        ->setFrom('contact@edit-place.com', 'Contact Edit-place')
                        ->addTo('jwolff@edit-place.com')
                        ->addCc('mailpearls@gmail.com')
                        ->setSubject('New AO Confirmed without spec file');
                    $mail1->send();
                }
            }
            $this->_redirect($prevurl);
        }
    }
    ////////get all bidded for participations for profile selections////////////////
    public function profilesListAction() {
        $participate_obj = new EP_Participation_Participation();
        $delivery_obj    = new Ep_Delivery_Delivery();
        $condition['profilelist'] = $this->configval['selection_profiles'];
        $condition['loginUserId'] = $this->adminLogin->userId;
        $condition['loginUserType'] = $this->adminLogin->type;
        $profile_params=$this->_request->getParams();

        if($profile_params['search'] == 'search')
        {
            $condition['search'] = $profile_params['search'];
            $condition['aoId'] = $profile_params['aoId'];
            $condition['inchargeId'] = $profile_params['inchargeId'];
            $condition['clientId'] = $profile_params['clientId'];
           // $condition['closed'] = $profile_params['closed'];
            $condition['startdate'] = $profile_params['startdate'];
            $condition['enddate'] = $profile_params['enddate'];
            /*if($profile_params['closed'] != '0')
            {
                $allaos = $delivery_obj->getAllAos();
                foreach($allaos as $key=>$value)
                {
                    $allList = $participate_obj->getNotClosedSelectProfiles($value['id']);
                    if($profile_params['closed'] == 'closed')
                    {
                        if($allList == 'yes')
                        {
                            $searchaos[$key] = $value['id'];
                        }
                    }
                    elseif($profile_params['closed'] == 'notclosed')
                    {
                        if($allList == 'NO')
                        {
                            $searchaos[$key] = $value['id'];
                        }
                    }
                    else
                        $searchaos[$key] = 'all';
                }
                if($searchaos == 'all')
                    $condition['searchaosarray'] = "all";
                else
                    $condition['searchaosarray'] = join(',',$searchaos);
            }*/
        }

        $res = $participate_obj->profilesList($condition);
        if($res != 'NO')
        {
            foreach ($res as $key1 => $value1) {
            $affectart                    = $participate_obj->getAffectedArticles($res[$key1]['id']);
            $res[$key1]['affectedart']    = $affectart[0]['affectedart'];
                if($profile_params['closed'] == 'closed')
                {
                    if($res[$key1]['artCount'] == $affectart[0]['affectedart'])
                    {
                        $res1[$key1]   = $res[$key1];
                    }
                }
                elseif($profile_params['closed'] == 'all')
                {
                    $res1[$key1]   = $res[$key1];
                }
                else{
                    if($res[$key1]['artCount'] != $affectart[0]['affectedart'])
                    {
                        $res1[$key1]   = $res[$key1];
                    }
                }
                if($res1[$key1]['id'] != '') {
                    $notaffectart                 = $participate_obj->getNotAffectedArticles($res1[$key1]['id']);
                    $res1[$key1]['notaffectedart'] = $notaffectart;
                    $bidencours                   = $participate_obj->getBidEncoursArticles($res1[$key1]['id']);
                    $res1[$key1]['bidencours']     = $bidencours[0]['bidencours'];
                    $res1[$key1]['notclosedprofiles']  = $participate_obj->getNotClosedSelectProfiles($res1[$key1]['id']);
                }
            }
        }

        if ($res1 != "NO")
            $this->_view->paginator = $res1;
        else
            $this->_view->nores = "true";
        $this->_view->render("processao_profilelist");
    }
    ////////////display pop up with detail of multiple contributors who made biding when the article title is clicked///////////////////
    public function republishpopupAction()
    {
        $delivery_obj=new Ep_Delivery_Delivery();
        $article_obj = new EP_Delivery_Article();
        $participate_obj=new EP_Participation_Participation();
        $automail=new Ep_Message_AutoEmails();
        $republishParams=$this->_request->getParams();
        $artId=$republishParams['artId'];
        $artdeldetails = $delivery_obj->getArtDeliveryDetails($artId);
        if($republishParams['save'] == 'save')
        {
            //$parttime = $republishParams['parttime'];
            if($republishParams['parttime_option'] == 'min' )
                $parttime=$republishParams['parttime'];
            elseif($republishParams['parttime_option'] == 'hour')
                $parttime=60*$republishParams['parttime'];
            elseif($republishParams['parttime_option'] == 'day')
                $parttime=60*24*$republishParams['parttime'];

            $subopttime = $republishParams['subopttime'];
            if($subopttime == 'min')
            {
                $jc0time = $republishParams['jc0time'];
                $jctime = $republishParams['jctime'];
                $sctime = $republishParams['sctime'];
            }
            elseif($subopttime == 'hour')
            {
                $jc0time = $republishParams['jc0time']*60;
                $jctime = $republishParams['jctime']*60;
                $sctime = $republishParams['sctime']*60;
            }
            elseif($subopttime == 'day')
            {
                $jc0time = $republishParams['jc0time']*60*24;
                $jctime = $republishParams['jctime']*60*24;
                $sctime = $republishParams['sctime']*60*24;
            }
            $suboptresub = $republishParams['suboptresub'];
            if($suboptresub == 'min')
            {
                $jc0resub = $republishParams['jc0resub'];
                $jcresub = $republishParams['jcresub'];
                $scresub = $republishParams['scresub'];
            }
            elseif($suboptresub == 'hour')
            {
                $jc0resub = $republishParams['jc0resub']*60;
                $jcresub = $republishParams['jcresub']*60;
                $scresub = $republishParams['scresub']*60;
            }
            elseif($suboptresub == 'day')
            {
                $jc0resub = $republishParams['jc0retime']*60*24;
                $jcresub = $republishParams['jcresub']*60*24;
                $scresub = $republishParams['scresub']*60*24;
            }
             $object = $republishParams['object'];
             $message = utf8_encode($republishParams['message']);
             $publish_langs = $republishParams['pubselectedlang'];

            ///udate status_bo in delivery table for delete as trash///////
            $data = array("participation_time"=>$parttime, "submit_option"=>$subopttime, "subjunior_time"=>$jc0time, "junior_time"=>$jctime, "publish_language"=>$publish_langs,
                "senior_time"=>$sctime, "resubmit_option"=>$suboptresub, "jc0_resubmission"=>$jc0resub, "jc_resubmission"=>$jcresub, "sc_resubmission"=>$scresub, "republish_object"=>$object, "republish_mail"=>$message);////////updating
            $query = "id= '".$artId."'";
            $article_obj->updateArticle($data,$query); exit;
        }
        $artId=$republishParams['artId'];
        if($artdeldetails[0]['AOtype'] == 'public')
        {
            $profiles = explode(",", $artdeldetails[0]['view_to']);
            $profiles = implode(",", $profiles);
            $profs=explode(",",$profiles);
            $proflist=array();
            for($p=0;$p<count($profs);$p++)
            {
                if($profs[$p]=="jc")
                    $proflist[]="junior";
                elseif($profs[$p]=="sc")
                    $proflist[]="senior";
                elseif($profs[$p]=="jc0")
                    $proflist[]="sub-junior";
            }
            $pubprofiles=implode("','",$proflist);
            $aoprofiles=$delivery_obj->getViewToOfAO($pubprofiles);
            $aoprofiles = $aoprofiles[0]['AoContributors'];
        }
        else{
            $priprofiles = explode(",",$artdeldetails[0]['contribs_list']);
            $aoprofiles=count($priprofiles);
        }
        $partinart = $participate_obj->getPartsCountInArticle($artId);
        if($partinart[0]['partcountinart'] == $aoprofiles)
             $this->_view->nopartsforrepublish  = "yes";  ////there are no user to participate in article if article is republished///
        else
             $this->_view->nopartsforrepublish = "no";
        $this->_view->refusedcontributors = $participate_obj->getRefusedParts($artId);
        if($artdeldetails[0]['AOtype'] == 'public')
        {
            if($artdeldetails[0]['view_to'] == 'sc')
                $this->_view->missiontitle = "Mission SC";
            else
                $this->_view->missiontitle = "Mission publique";
        }
        else
        {
            $this->_view->missiontitle = "Mission privée";
        }
        if($artdeldetails[0]['submit_option'] == 'min' )
            $convertval = 1;
        elseif($artdeldetails[0]['submit_option'] == 'hour')
            $convertval = 60;
        elseif($artdeldetails[0]['submit_option'] == 'day')
            $convertval = 60*24;

        if($artdeldetails[0]['resubmit_option'] == 'min')
            $reconvertval = 1;
        elseif($artdeldetails[0]['resubmit_option'] == 'hour')
            $reconvertval = 60;
        elseif($artdeldetails[0]['resubmit_option'] == 'day')
            $reconvertval = 60*24;

        $artdeldetails[0]['subjunior_time'] = $artdeldetails[0]['subjunior_time']/$convertval;
        $artdeldetails[0]['junior_time'] = $artdeldetails[0]['junior_time']/$convertval;
        $artdeldetails[0]['senior_time'] = $artdeldetails[0]['senior_time']/$convertval;

        $artdeldetails[0]['jc0_resubmission'] = $artdeldetails[0]['jc0_resubmission']/$reconvertval;
        $artdeldetails[0]['jc_resubmission'] = $artdeldetails[0]['jc_resubmission']/$reconvertval;
        $artdeldetails[0]['sc_resubmission'] = $artdeldetails[0]['sc_resubmission']/$reconvertval;

        $this->_view->artdeldetails =  $artdeldetails;

        $parameters['article_title']=$artdeldetails[0]['articleName'];
        $clientDetails=$automail->getUserDetails($artdeldetails[0]['user_id']);
        if($clientDetails[0]['username']!=NULL)
            $parameters['client_name']= $clientDetails[0]['username'];
        else
        {
            $email = explode("@",$clientDetails[0]['email']);
            $parameters['client_name']= $email[0];
        }
        $expires=time()+(60*$artdeldetails[0]['participation_time']);
        $parameters['submitdate_bo']=date('d/m/Y H:i', $expires);
        $parameters['aoname_link'] = "/contrib/aosearch";
        $parameters['AO_title']= $artdeldetails[0]['deliveryTitle'];

        if($artdeldetails[0]['republish_mail'] !=  NULL)
        {
            /*$this->_view->object=$artdeldetails[0]['republish_object'];
            $this->_view->message = stripslashes($artdeldetails[0]['republish_mail']);
            $this->_view->stage = $republishParams['stage'];       ////when final refused and republished from correction stages 0,1,2///*/
            if($artdeldetails[0]['AOtype'] == 'public')
                $mailId = 15;
            else
                $mailId = 84;
            $email=$automail->getAutoEmail($mailId);
            $this->_view->object=$email[0]['Object'];
            $email = $automail->getMailComments($user_id=NULL,$mailId,$parameters);
            $this->_view->message = utf8_encode(stripslashes($email));
            $this->_view->stage = $republishParams['stage'];       ////when final refused and republished from correction stages 0,1,2///
        }
        else
        {
            if($artdeldetails[0]['AOtype'] == 'public')
                $mailId = 15;
            else
                $mailId = 84;
            $email=$automail->getAutoEmail($mailId);
            $this->_view->object=$email[0]['Object'];
            $email = $automail->getMailComments($user_id=NULL,$mailId,$parameters);
            $this->_view->message = utf8_encode(stripslashes($email));
            $this->_view->stage = $republishParams['stage'];       ////when final refused and republished from correction stages 0,1,2///
        }
        if($republishParams['close'] == 'yes') {
            $refuseemail = $automail->getMailComments($user_id=NULL,27,$parameters);
            $this->_view->refusemessage = utf8_encode(stripslashes($refuseemail));
            $this->_view->close = "yes";
           // $this->articleshistory($artId, 'selectionprofile', 'closed_published');   ///when last participants is there///
        }
        /*else
        {
            if($republishParams['nopart'] == 'no')      ///republished when no participats///
                $this->articleshistory($artId, 'selectionprofile', 'noparticipant_republish');
            else
                $this->articleshistory($artId, 'selectionprofile', 'republish');
        }*/
        $this->_view->render("processao_republishaopopup");
    }

    ///changing the ao particiption time (dynamically in republishpopup////
    public function getextendparticipationtimeAction()
    {
        $articleParams=$this->_request->getParams();
        $participation_obj=new EP_Participation_Participation();
        $automail=new Ep_Message_AutoEmails();
        $delivery_obj = new Ep_Delivery_Delivery();
        if($articleParams['publishaomail'] == 'yes')   ///when time changes in mail content in publish ao popup//
        {
           if($articleParams['now'] == 'yes')
               $submitdate_bo="<b>".strftime("%d/%m/%Y &agrave; %H:%M",$expires)."</b>";
           else
           {
               $expires+=60*60*24;
               $submitdate_bo="<b>".strftime("%d/%m/%Y &agrave; %H:%M",$expires)."</b>";
           }

        }
        $artdeldetails = $delivery_obj->getArtDeliveryDetails($articleParams['artname']);

        if(!$articleParams['part_time'])
        {
            $articleParams['part_time']=0;
        }
        if($articleParams['parttime_option'] == 'min' )
            $expires=time()+(60*$articleParams['part_time']);
        elseif($articleParams['parttime_option'] == 'hour')
            $expires=time()+(60*60*$articleParams['part_time']);
        elseif($articleParams['parttime_option'] == 'day')
            $expires=time()+(60*60*24*$articleParams['part_time']);

        $parameters['submitdate_bo']=date('d/m/Y H:i', $expires);
        $parameters['article_title']=$artdeldetails[0]['articleName'];
        $parameters['aoname_link'] = "/contrib/aosearch";
        $parameters['AO_title']= $artdeldetails[0]['deliveryTitle'];
        if($artdeldetails[0]['AOtype'] == 'public')
            $mailId = 15;
        else
            $mailId = 84;
        $email = $automail->getMailComments($user_id=NULL,$mailId,$parameters);
        $emailComments = utf8_encode(stripslashes($email));
        echo $emailComments;
        exit;

    }
    ///extend the participation time ////
    public function extendPartExpireTimeAction()
    {
        $prevurl = getenv("HTTP_REFERER");
        $articleParams=$this->_request->getParams();
        $this->_view->artId = $articleParams['artId'];
        $participation_obj=new EP_Participation_Participation();
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new Ep_Delivery_Article();
        $artdeldetails = $delivery_obj->getArtDeliveryDetails($articleParams['artId']);
        $this->_view->partexpdate = date("d-m-Y", $artdeldetails[0]['participation_expires']);
        $this->_view->partexptime = date("g:i A", $artdeldetails[0]['participation_expires']);

        if($articleParams['extensionsubmit'] == 'extsubmit')   ///when time changes in mail content in publish ao popup//
        {
            $extdate = date('m/d/Y', strtotime($articleParams['extend_partdate']));
            $exttime = date('H:i', strtotime($articleParams['ext_parttime']));
            $extenddatetime =strtotime($extdate." ".$exttime);
            $data = array("participation_expires"=>$extenddatetime);
            $query = "id= '".$articleParams['artId']."'";
            $article_obj->updateArticle($data,$query);
            $this->_redirect($prevurl);
        }
        $this->_view->extendparttime   = 'yes';
        $this->_view->extendcrtparttime   = 'no';
        $this->_view->nores = 'true';
        $this->_view->render("ongoing_extendtime_writer_popup");

    }
    ///extend the participation time ////
    public function extendLiberteMissionTimeAction()
    {
        $prevurl = getenv("HTTP_REFERER");
        $articleParams=$this->_request->getParams();
        $this->_view->aoId = $articleParams['aoId'];
        $participation_obj=new EP_Participation_Participation();
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new Ep_Delivery_Article();
        $artdeldetails = $delivery_obj->getArtDelDetails($articleParams['aoId']);
        $this->_view->extend_libertedate = date("d-m-Y", $artdeldetails[0]['participation_expires']);
        $this->_view->extend_libertetime = date("g:i A", $artdeldetails[0]['participation_expires']);
        $this->_view->artId = $artdeldetails[0]['artId'];
        $this->_view->aoId = $articleParams['aoId'];
        if($articleParams['extendlibertesubmit'] == 'extendlibertesubmit')   ///when time changes in mail content in publish ao popup//
        {
            $extdate = date('m/d/Y', strtotime($articleParams['extend_libertedate']));
            $exttime = date('H:i', strtotime($articleParams['extend_libertetime']));
            $extenddatetime =strtotime($extdate." ".$exttime);
            $data = array("participation_expires"=>$extenddatetime);
            $query = "id= '".$articleParams['artId']."'";
            $article_obj->updateArticle($data,$query);
            $this->_redirect($prevurl);
        }
        $this->_view->extendparttime   = 'no';
        $this->_view->extendcrtparttime   = 'no';
        $this->_view->editftvassigntime  = 'no';
        $this->_view->extendlibertemissiontime   = 'yes';
        $this->_view->nores = 'true';
        $this->_view->render("ongoing_extendtime_writer_popup");

    }
    /* edited by naseer on 24-09-2015*/
    //optimizing database queries//
    public function downloadLiberteMissionAction()
    {
        $libParams=$this->_request->getParams();
        $del_obj = new Ep_Delivery_Delivery();
        $part_obj = new Ep_Participation_Participation();
        $payment_obj = new Ep_Payment_PaymentArticle();
        $articleproc_obj = new Ep_Delivery_ArticleProcess();
        $user_obj = new Ep_User_User();
        /* * download XLS file***/
        $contrib_params=$this->_request->getParams();
        if(isset($libParams['download']) && $libParams['download']!='')
        {
           /* if($libParams['type'] == 'valid')
                $rResult  = $del_obj->loadgetPaidNewAos($sWhere=null, $sOrder=null, $sLimit=null);
            else*/
            //$rResult  = $del_obj->getNonpremAllAos($libParams['type']);exit;
            $rResult  = $del_obj->getNonpremAllAosNew($libParams['type']);
            //print_r($rResult); exit;
            $file = 'excelFile-'.date("Y-M-D")."-".time().'.xls';
            ob_start();
            $content= '<table border="1"> ';
            $content.= '<tr><th>SNO.</th><th>Client Name</th><th>Email Adress</th><th>Mission</th><th>Date of publication</th>';
            $content.= '<th>Number of Participation</th><th>Date of contributor selected</th><th>Mission validated</th>';
            $content.= '<th>Date of article(s) upload</th><th>Date of paiement</th><th>Current status</th>';
            $content.= '</tr>';
            for($i=0; $i<count($rResult); $i++)
            {
                /*$validMissions = $part_obj->getLibertyMissionsPartsDetials($rResult[$i]['artId']);
                if($validMissions != 'NO')
                    $missionvalid[$i] = "yes";
                else
                    $missionvalid[$i] = "no";*/

                /*$published_at = $del_obj->getArtDelDetails($rResult[$i]['delId']);
                if($published_at != 'NO' && $published_at[0]['published_at'] != 0)
                    $publisheddate[$i] = date("d-m-Y", ($published_at[0]['published_at']));
                else
                    $publisheddate[$i] = '---';*/

                /*$contribselector = $part_obj->getContributorSelectedDate($rResult[$i]['artId']);
                if($contribselector != 'NO')
                    $contribselected[$i] = date("d-m-Y", strtotime($contribselector[0]['accept_refuse_at']));
                else
                    $contribselected[$i] = "---";*/


                /*if($rResult[$i]['invoice_id'] != ''){
                    $paidornot1 = $payment_obj->getPaymentArticles($rResult[$i]['invoice_id']);
                    $paidornot[$i] = (strlen($paidornot1[0]['created_at']) > 6) ? date("d-m-Y", strtotime($paidornot1[0]['created_at'])) : "--";
                }
                else
                    $paidornot[$i] = "--"; */
                // commented since its not been used anywhere //
                /*$uploaddates = $part_obj->getFileUploadTime($rResult[$i]['artId']);
                if($uploaddates != 'NO')
                    $uploaddate[$i] = date("d-m-Y", strtotime($uploaddates[0]['article_sent_at']));
                else
                    $uploaddate[$i] = "--"; */
                /*if($rResult[$i]['liberte_action_by'] != ''){
                    $bouserdetials = $user_obj->getAllUsersDetails($rResult[$i]['liberte_action_by']);
                    $bouser = $bouserdetials[0]['login'];
                }*/
                $bouser = $rResult[$i]["liberte_action_login"];
                if($libParams['type'] == '')
                    $rResult[$i]['latestStatus'] = "new";
                elseif($libParams['type'] == 'refuse')
                    $rResult[$i]['latestStatus'] = "Refus par ".$bouser;
                elseif($libParams['type'] == 'cancel')
                    $rResult[$i]['latestStatus'] = "Cancel par ".$bouser;
                else
                {
                    $today = date("Y-m-d");
                    $date = new DateTime();
                    $timestampnow = $date->getTimestamp();
                    $lastStatus = $part_obj->getLibMissionsPartsStatus($rResult[$i]['artId']);// echo $lastHistory[0]['action_sentence']; print_r($lastHistory); exit;
                    if($lastStatus != 'NO')
                    {  //echo "<br>--".$details[0]['participation_expires']; echo "<br>---".$timestampnow;

                        $datetime1 = new DateTime(date("Y-m-d", strtotime($lastStatus[0]['created_at'])));
                        $datetime2 = new DateTime($today);
                        $interval = $datetime1->diff($datetime2);
                        $days = $interval->format('%a');
                        if($lastStatus[0]['status'] == 'bid_nonpremium' && $published_at[0]['participation_expires'] > $timestampnow)
                            $rResult[$i]['latestStatus'] = "Participations en cours";
                        elseif($lastStatus[0]['status'] == 'bid_nonpremium')
                            $rResult[$i]['latestStatus'] = "En s&eacute;lection de profil";
                        elseif($lastStatus[0]['status'] == 'bid')
                            $rResult[$i]['latestStatus'] = "En cours de redaction";
                        elseif(($lastStatus[0]['status'] == 'bid' || $lastStatus[0]['status'] == 'time_out') && $lastStatus[0]['article_submit_expires'])
                            $rResult[$i]['latestStatus'] = "Termin&eacute;";
                        elseif($lastStatus[0]['status'] == 'under_study' && $published_at[0]['paid_status'] == 'notpaid' && $days < 7)
                            $rResult[$i]['latestStatus'] = "Article upload&eacute;";
                        elseif($lastStatus[0]['status'] == 'under_study' && $published_at[0]['paid_status'] == 'notpaid' && $days >= 7)
                            $rResult[$i]['latestStatus'] = "Unpaid";
                        elseif($lastStatus[0]['status'] == 'under_study' && $published_at[0]['paid_status'] == 'paid')
                            $rResult[$i]['latestStatus'] = "Paiement effectu&eacute;";
                        elseif($lastStatus[0]['status'] == 'disapprove_client')
                            $rResult[$i]['latestStatus'] = "Refus client";
                        elseif($lastStatus[0]['status'] == 'published')
                            $rResult[$i]['latestStatus'] = "Facture g&eacute;n&eacute;r&eacute;e";
                        elseif($lastStatus[0]['status'] == 'closed_client_temp')
                            $rResult[$i]['latestStatus'] = "Ferm&eacute; client";
                        else
                            $rResult[$i]['latestStatus'] = "--";
                    }
                    else
                        $rResult[$i]['latestStatus'] = "--";

                }
                $sl = $i+1;
                $content.= '<tr><td>'.$sl.'</td><td>'.$rResult[$i]["company_name"].'</td><td>'.$rResult[$i]["email"].'</td><td>'.$rResult[$i]["title"].'</td>';
                $content.= '<td>'.( ($rResult[$i]["published_at"] != "0") ? date("d-m-Y", ($rResult[$i]["published_at"])) : '--').'</td><td>'.$rResult[$i]["partCount"].'</td>';
                $content.= '<td>'.( ($rResult[$i]["accept_refuse_at"] != "") ? $rResult[$i]["accept_refuse_at"] : '--').'</td><td>'.$rResult[$i]["missionvalid"].'</td>';
                $content.= '<td>'.( ($rResult[$i]["uploaddate"] != "") ? $rResult[$i]["uploaddate"] : '--').'</td>
                <td>'.( ($rResult[$i]["paidornot"] != "") ? $rResult[$i]["paidornot"] : '--').'</td>
                <td>'.$rResult[$i]["latestStatus"].'</td></tr>';

            }
            $content.='</table>';
            //echo $content; exit;

            ob_end_clean();
            header("Expires: 0");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header("Content-type: application/vnd.ms-excel;charset:UTF-8");
            header('Content-length: '.strlen($content));
            header('Content-disposition: attachment; filename='.basename($file));
            echo $content;
            exit;
        }
    }
    ////////get all bidded for participations for profile selections////////////////
    public function articleProfilesAction() {
        $participate_obj = new EP_Participation_Participation();
        $article_obj     = new EP_Delivery_Article();
        $delivery_obj     = new Ep_Delivery_Delivery();
        $contrib_obj     = new EP_User_Contributor();
        $user_obj     = new Ep_User_User();
        $lock_obj = new Ep_User_LockSystem();
        $partParams      = $this->_request->getParams();
        $aoId            = $partParams['aoId'];
        if ($aoId != NULL) {
            if(isset($partParams['status']))
            {
               $condition['status'] = $partParams['status'];
            }
            $condition['aoId'] = $aoId;
            $res = $article_obj->getArticleDetailsWithAoid($condition);

            $delDetails = $delivery_obj->getPrAoDetails($aoId);
            $userdetials = $user_obj->getAllUsersDetails($delDetails[0]['created_user']);
            $delDetails[0]['created_user'] = $userdetials[0]['first_name'];
            $delDetails[0]['del_category'] = $this->category_array[$delDetails[0]['del_category']];
            $this->_view->delDetails = $delDetails;
            $this->_view->aoId = $aoId;
            if ($res != "NO") {
                foreach ($res as $key1 => $value1) {
                    $status_array     = '';
                    $status_text      = '';
                    $contributor_text = '';
                    $user_array       = '';
                    $user_text        = '';
                    $status_array     = $participate_obj->getAllPartsStatusOfArt($res[$key1]['artId']);
                    if ($status_array != 'NO') {
                        foreach ($status_array as $participate_status) {
							if($delDetails[0]['product']=='translation')
								$profiletype=$participate_status['translator_type'];
							else
								$profiletype=$participate_status['profile_type'];
                            $status_text .= $participate_status['status'] . "|" . $profiletype . ",";
                            if ($participate_status['first_name'] != '')
                                $contirb_name = $participate_status['first_name'] . " " . $participate_status['last_name'];
                            else
                                $contirb_name = $participate_status['email'];
                            $contributor_text .= $participate_status['status'] . "|" . $contirb_name . "|" . $participate_status['identifier'] . ",";
                        }
                    }
                    $userCount = $participate_obj->getUserCountInArticle($res[$key1]['artId']);
                    $lastartbacktoFO = $participate_obj->getlastArticlesBackToFo($res[$key1]['artId']);
                    $lastartrepublish = $participate_obj->getDetailsForRepublish($res[$key1]['artId']);
                    $res[$key1]['lastpartcount']          = $lastartbacktoFO[0]['lastpartcount'];
                    if($lastartrepublish != 'NO'){
                    $res[$key1]['article_id']             = $lastartrepublish[0]['article_id'];
                    $res[$key1]['participate_id']         = $lastartrepublish[0]['id'];
                    $res[$key1]['user_id']                = $lastartrepublish[0]['user_id'];
                    $res[$key1]['repub_status']           = $lastartrepublish[0]['status'];   }
                    $res[$key1]['pstatus']                = $status_text;
                    $res[$key1]['contribstatus']          = $contributor_text;
                    $res[$key1]['userCount']              = $userCount[0]['userCount'];
                    $artdetials                           = $article_obj->getArticleDetails($res[$key1]['artId']);
                    $res[$key1]['price_max']              = $artdetials[0]['price_max'];
                    $res[$key1]['price_min']              = $artdetials[0]['price_min'];
                    $res[$key1]['missiontest']            = $artdetials[0]['missiontest'];
                    $res[$key1]['lockedby_name']          = $lock_obj->getUserLocked($res[$key1]['artId']);
                    $profilelistparts                     = $participate_obj->articleProfiles($res[$key1]['artId']);
                    //echo "<pre>";print_r($profilelistparts);
                    if($profilelistparts!='NO')
                    {
                        $res[$key1]['email']                  = $profilelistparts[0]['email'];
                        $res[$key1]['profile_type']           = $profilelistparts[0]['profile_type'];
                        $res[$key1]['first_name']             = $profilelistparts[0]['first_name'];
                        $res[$key1]['last_name']              = $profilelistparts[0]['last_name'];
                        $res[$key1]['step']                   = $profilelistparts[0]['step'];
                        $res[$key1]['price_user']             = $profilelistparts[0]['price_user'];
                        $res[$key1]['status']                 = $profilelistparts[0]['status'];
                        $res[$key1]['current_stage']          = $profilelistparts[0]['current_stage'];
                        $res[$key1]['selection_type']         = $profilelistparts[0]['selection_type'];
                        $res[$key1]['cycle']                  = $profilelistparts[0]['cycle'];
                        $res[$key1]['cycle0UserCount']        = $profilelistparts[0]['cycle0UserCount'];
                        $res[$key1]['article_submit_expires'] = $profilelistparts[0]['article_submit_expires'];
                        $res[$key1]['partId']                 = $profilelistparts[0]['id'];
                        $res[$key1]['contribId']                 = $profilelistparts[0]['user_id'];
                    }
                    else
                        $res[$key1]['cycle0UserCount'] =0;
                }
                $this->_view->statusarray = array(
                    'bid',
                    'bid_temp',
                    'under_study',
                    'disapproved',
                    'on_hold',
                    'time_out',
                    'published',
                    'plag_exec',
                    'closed_client_temp'
                );
                $this->_view->paginator   = $res;
               // echo "<pre>";print_r($res);
            } else
                $this->_view->nores = "true";
            $this->_view->render("processao_articleprofiles");
        }
    }
    ////////////display pop up with detail of multiple contributors who made biding when the article title is clicked///////////////////
    public function groupProfilesAction() {
        $usercomment_obj = new Ep_Message_UserComments();
        $contrib_obj     = new EP_User_Contributor();
        $participate_obj = new EP_Participation_Participation();
        $correctionpaticipation_obj = new Ep_Participation_CorrectorParticipation();
        $article_obj     = new EP_Delivery_Article();
        $delivery_obj     = new Ep_Delivery_Delivery();
        $artprocess_obj = new EP_Delivery_ArticleProcess();
        $user_obj     = new Ep_User_User();

        $partParams      = $this->_request->getParams();

        $lastartbacktoFO = $participate_obj->getlastArticlesBackToFo($partParams['artId']);
        if ($partParams['artId'] != NULL) {
            $artId        = $partParams['artId'];
            $participants = $participate_obj->getGroupParticipants($artId);
            $delDetails = $delivery_obj->getPrAoDetailsWithArtid($artId);
			
			//simultaneous correction conditions
				if($delDetails[0]['articlecorrection']=="yes")
				{
					//For public & multiple private case
					$correctors=explode(",",$delDetails[0]['private_correctors']);
					if($delDetails[0]['correction_type']== "private" && count($correctors)==1)
						$selectedcorrector=$delDetails[0]['private_correctors'];
					else
						$selectedcorrector=$correctionpaticipation_obj->getSelectedCorrector($artId);
						
						$this->_view->selectedcorrector = $selectedcorrector;
				}
				
            $delDetails[0]['art_category'] = $this->category_array[$delDetails[0]['art_category']];
            $this->_view->delDetails = $delDetails;

			$partStatus=$participate_obj->articleProfiles($artId);
			if($partStatus=='NO')
				$partStatus=array();
			$this->_view->partStatus = $partStatus;
            if(!$_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')///if modal is open directly in url///
            { $this->_redirect("http://admin-ep-test.edit-place.com/processao/article-profiles?submenuId=ML2-SL2&aoId=".$delDetails[0]['id']); }

            if ($participants == "NO") {
                $this->_view->contribDetails = NULL;
                $this->_view->render("processao_groupprofiles");
                exit;
            }

            $noarts = $participate_obj->getPartsCount($artId);
            for ($i = 0; $i < count($participants); $i++) {
                $contribDetails[$i]     = $contrib_obj->getGroupProfilesInfo($participants[$i]['user_id'], $participants[$i]['id'], $artId);
                $gobalcommentscount[$i] = $usercomment_obj->getCommentsCount($participants[$i]['user_id']);
                $cnt                    = 0;
                foreach ($contribDetails[$i] as $details) {
                    $percentage  = $contribDetails[$i][$cnt]['contrib_percentage'];
                    $minPrice    = $contribDetails[$i][$cnt]['price_min'];
                    $maxPrice    = $contribDetails[$i][$cnt]['price_max'];
                    $writerPrice = $contribDetails[$i][$cnt]['price_user'];

                    if ($percentage != NULL) {
                        $contribDetails[$i][$cnt]['price_min'] = $minPrice / 100 * $percentage;
                        $contribDetails[$i][$cnt]['price_max'] = $maxPrice / 100 * $percentage;
                    } else if($writerPrice) {
                        $contribDetails[$i][$cnt]['price_min'] = $writerPrice;
                        $contribDetails[$i][$cnt]['price_max'] = $writerPrice;
                    }
					
					//Added By Arun
					if($writerPrice && $writerPrice >= $contribDetails[$i][$cnt]['price_min'] && $writerPrice <=$contribDetails[$i][$cnt]['price_max'])
					{
						$contribDetails[$i][$cnt]['within_range']='yes';
					}
					elseif($writerPrice)
					{
						$contribDetails[$i][$cnt]['within_range']='no';
					}
						
					
                    $contribDetails[$i][$cnt]['countcomments'] = $gobalcommentscount[$i][$cnt]['countcomments'];
                    $contribDetails[$i][$cnt]['profession']    = utf8_encode($this->profession_array[$details['profession']]);
                    $contribDetails[$i][$cnt]['language']      = utf8_encode($this->language_array[$details['language']]);
                    $contribDetails[$i][$cnt]['fav_category']  = utf8_encode($this->category_array[$details['favourite_category']]);
                    $contribDetails[$i][$cnt]['education']     = $details['education'];
                    $contribDetails[$i][$cnt]['contribmarks']  = $participate_obj->getContributorMarks($details['identifier']);
                    $contribDetails[$i][$cnt]['epcontribmarks']  = $participate_obj->getEpContributorMarks($details['identifier']);
                    $contribDetails[$i][$cnt]['categories']    = $this->unserialiseCategories($details['category_more']);
                    $contribDetails[$i][$cnt]['language_more'] = $this->unserialiseLanguage($details['language_more']);
                    if($details['identifier'] != '')
                    $contribDetails[$i][$cnt]['successrate']   = $participate_obj->getContributorSuccessRate($details['identifier']);
                   // $contrib_workedwith                      = $contrib_obj->getContribWorkedCompanies($details['identifier']);

                    $contrib_parts_inao                        = $contrib_obj->contribPartsInAo($details['identifier'], $details['delId']);
                    $contribDetails[$i][$cnt]['contrib_parts_inao']  = $contrib_parts_inao[0]['partscount'];
                    if($details['partId'] != ''){
                    $cyclecount                                = $participate_obj->getParticipationCyclesOnPartId($details['partId']);
                    $contribDetails[$i][$cnt]['cyclecount'] = $cyclecount[0]['cycle']; }
                    ///working details of user////

                    $workexpDetails=$user_obj->getExperienceDetails($details['identifier'],'job');
                    if($workexpDetails != 'NO')
                        $contribDetails[$i][$cnt]['workDetails']=$workexpDetails;

                    $educationDetails=$user_obj->getExperienceDetails($details['identifier'],'education');
                    if($workexpDetails != 'NO')
                        $contribDetails[$i][$cnt]['educationDetails']=$educationDetails;
						
					if($contribDetails[$i][$cnt]['product']=='translation')	
						$contribDetails[$i][$cnt]['profiletype']=$contribDetails[$i][$cnt]['translator_type'];
					else	
						$contribDetails[$i][$cnt]['profiletype']=$contribDetails[$i][$cnt]['profile_type'];
                    $cnt++;
                }
            }		
			
            $this->_view->lastparticipant         = $lastartbacktoFO[0]['lastpartcount'];
            $this->_view->pagetype                = $cond;
            $this->_view->artid                	  =  $artId ;
            $this->_view->totalusers              = count($participants);
            $this->_view->bid_arts                = $noarts[0]['partcount'];
            $this->_view->refused_arts            = $noarts[1]['partcount'];
            $this->_view->proccesing_arts         = $noarts[2]['partcount'];
            $this->_view->contribDetails          = $contribDetails;
            $maxcycle                             = $participate_obj->getParticipationCycles($artId);
            $this->_view->maxcycle                = $maxcycle[0]['cycle'];
            $anyvalidatedContributor              = $participate_obj->anyValidatedContributor($artId);
            $this->_view->anyvalidatedContributor = $anyvalidatedContributor;
            $this->_view->render("processao_groupprofiles");
        }
    }
	
    /* *function to publish article back to FO**/
    public function publisharticlefoAction()
    {
        $prevurl = getenv("HTTP_REFERER");
        $delivery=new Ep_Delivery_Delivery();
        $article_obj=new EP_Delivery_Article();
        $autoEmails = new Ep_Message_AutoEmails();
        $participate_obj = new EP_Participation_Participation();
        $profile_params=$this->_request->getParams();
        $artId = $profile_params['art_id'];
        $partdetails =  $participate_obj->getParticipantsDetailsCycle0($artId);
          ////udate status participation table for status///////
        $data = array("status"=>"closed", "accept_refuse_at"=>date("Y-m-d H:i:s", time()),  "selection_type"=>"bo");////////updating
        $query =  "article_id= '".$artId."' AND status IN ('bid', 'disapproved', 'time_out') AND cycle='0'";
        $participate_obj->updateParticipation($data,$query);
        ////udate status participation table for status///////
        $data = array("status"=>"bid_refused", "accept_refuse_at"=>date("Y-m-d H:i:s", time()),  "selection_type"=>"bo");////////updating
        $query =  "article_id= '".$artId."' AND status IN ('bid_premium', 'bid_temp') AND cycle='0'";
        $participate_obj->updateParticipation($data,$query);
        $artdeldetails = $delivery->getArtDeliveryDetails($artId);
        $repubcount = $artdeldetails[0]['republish_count']+1;
        if($artdeldetails[0]['republish_by_at'] == NULL)
            $repubbyat = $this->adminLogin->userId."|".date('Y-m-d H:i:s');
        else
            $repubbyat = $artdeldetails[0]['republish_by_at'].",".$this->adminLogin->userId."|".date('Y-m-d H:i:s');
        ////updating the article every time when republished///////
        $data = array("republish_count"=>$repubcount,"republish_by_at"=>$repubbyat);////////updating
        $query = "id = '".$artId."'";
        $article_obj->updateArticle($data,$query);
        if($profile_params['sendtofo'] == 'no'){ // echo $profile_params['refusalmailcontent']; echo "hello"; exit;
            ////updating the article tabel article submit expire wiht zero///////
            $data = array("send_to_fo"=>"no","file_path"=>"");////////updating
            $query = "id = '".$artId."'";
            $article_obj->updateArticle($data,$query);
            //////this refusal mail is sent to participants when republished and close the article///////
            $partsUserids = $participate_obj->getActiveParicipants($artId);
            if($partsUserids != 'NO')
            {
                $email=$autoEmails->getAutoEmail(27);//
                $Object=$email[0]['Object'];
                $receiverId = $partsUserids[0]['user_id'];
                $Message =  $profile_params['refusalmailcontent'];
                $autoEmails->sendMailEpMailBox($receiverId,$Object,$Message);
            }
            $actparams['artId'] = $artId;
            $actparams['stage'] = "selection profile";
            $actparams['action'] = "closed";
            $this->articleHistory(5,$actparams);
            /////////////end of article history////////////////
        }
        else
        {
            $cycleZero = $participate_obj->findAnyCycleZero($artId);
            if($cycleZero == "NO")
            {
               ////update the artlcle table with partcipation time/////////
               $this->WriterParticipationExpire($artId);
            }
            else
            {
                ////////////updating article time to zero as article should go back FO again  ///////
                $artbacktoFO = $participate_obj->getArticlesBackToFo($artId);
                if($artbacktoFO == "NO")
                {
                    $this->republish($artId);///updating cycle and to show in FO////
                }
            }
            if($profile_params['sendmail'] == 'yes'){
                $this->sendMailToContribs($artId);
            }
            //////this refusal mail is sent to participants when republished and close the article///////
            if($profile_params['sendrefusalmail'] == 'yes'){
                $partsUserids = $participate_obj->getActiveParicipants($artId);
                if($partsUserids != 'NO')
                {
                    $email=$autoEmails->getAutoEmail(27);//FSDFSDSSDFSDF
                    $Object=$email[0]['Object'];
                    $receiverId = $partsUserids[0]['user_id'];
                    $Message =  $profile_params['refusalmailcontent'];
                    $autoEmails->sendMailEpMailBox($receiverId,$Object,$Message);
                }
            }
            /////////////article history////////////////
            $partscount = $participate_obj->getNoOfParticipants($artId);
            $actparams['participation_count'] = $partscount[0]['partsCount'];

            $actparams['artId'] = $artId;
            $actparams['stage'] = "selection Profile or stages";
            if($partdetails != 'NO')
            {
                if($partdetails[0]['status'] == 'bid' || $partdetails[0]['status'] == 'disapproved')  {
                    $actparams['contributorId'] = $partdetails[0]['user_id'];
                    $actparams['action'] = "article not sent and republished";
                    $this->articleHistory(7,$actparams);
                }
                else{
                    $actparams['action'] = "republished";
                    $this->articleHistory(4,$actparams);
                }
            }
            else{
                $actparams['action'] = "republished";
                $this->articleHistory(4,$actparams);
            }
            /////////////end of article history////////////////
        }
        /// unlock the article///////////////
        $this->unlockonactionAction($artId);
        $this->_redirect($prevurl);
    }
    /////////when u click on "to be closed" link it will closed the  profiles on that article/////////////////
    public function closeartprofileAction()
    {
        $article=new EP_Delivery_Article();
        $participate_obj = new EP_Participation_Participation();
        $profilelist_params=$this->_request->getParams();
        $artId=$profilelist_params["artid"] ;
        $closemode=$profilelist_params["closemode"] ;
        if($profilelist_params['type'] == 'bulk')
        {
            $artarry = explode(",",$profilelist_params["artid"]);
            for($i=0;$i<=count($artarry);$i++)
            {
                $data = array("bo_closed_status"=>"closed");////////updating
                $query = "id= '".$artarry[$i]."'";
                $article->updateArticle($data,$query);
            }
            exit;
            //$this->_redirect("processao/profileslist?submenuId=ML2-SL2");
        }
        if($closemode == 'close')
            $data = array("bo_closed_status"=>'closed');
        else
            $data = array("bo_closed_status"=>NULL);
        $query = "id= '".$artId."'";
        $article->updateArticle($data,$query);
        /////////////article history////////////////
        $partscount = $participate_obj->getNoOfParticipants($artId);
        $actparams['participation_count'] = $partscount[0]['partsCount'];
        $actparams['artId'] = $artId;
        $actparams['stage'] = "closed article after x participations in selection Profile";
        $this->articleHistory(5,$actparams);
        /////////////end of article history////////////////
        //$this->_redirect("processao/profileslist?submenuId=ML2-SL2");
    }
    //////////when u click on "to be closed" link it will closed the  profiles on that article in corrector closed/////////////////
    public function closeartcrtprofileAction()
    {
        $article=new EP_Delivery_Article();
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $profilelist_params=$this->_request->getParams();
        $artId = $profilelist_params["artid"];
        if($profilelist_params['type'] == 'bulk')
        {
            $artarry1 = explode(",",$artId);
            $artarry = array_diff($artarry1, array('0', 'all'));
            for($i=0;$i<=count($artarry);$i++)
            {
                $data = array("correction_closed_status"=>"closed");////////updating
                $query = "id= '".$artarry[$i]."'";
                $article->updateArticle($data,$query);
            }
            exit;
            //$this->_redirect("processao/profileslist?submenuId=ML2-SL2");
        }
        $article->correction_closed_status="closed" ;
        $article->id=$artId ;
        echo $data = array("correction_closed_status"=>$article->correction_closed_status);////////updating
        echo $query = "id= '".$article->id."'";
        $article->updateArticle($data,$query);
        /////////////article history////////////////
        $partscount = $crtparticipate_obj->getNoOfCrtParticipants($artId);
        $actparams['participation_count'] = $partscount[0]['partsCount'];
        $actparams['artId'] = $artId;
        $actparams['stage'] = "closed article after x participations in selection Profile";
        $this->articleHistory(17,$actparams);
        /////////////end of article history////////////////
        //$this->_redirect("correction/correctorprofiles?submenuId=ML2-SL18");
    }
    public function getcommentpopupAction()
    {
        $prevurl = getenv("HTTP_REFERER");
        $participate_obj = new EP_Participation_Participation();
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $user_obj = new Ep_User_User();
        $delivery_obj = new Ep_Delivery_Delivery();
        $profile_params=$this->_request->getParams();

         ////////////////////////////
        $contrib_params=$this->_request->getParams();
        $contrib_id = $contrib_params['contrib_id'];
        $particip_id = $contrib_params['particip_id'];
        $artId = $contrib_params['artid'];
        $mailid = $contrib_params['mailId'];
        $paricipationdetails=$participate_obj->getParticipateDetails($particip_id);

        ///for groupprofilepopup mails show off////
        if($artId !="")
          $expires = $this->writerExpireTime($artId, $contrib_id);

        $autoEmails=new Ep_Message_AutoEmails();

        $parameters['royalty']=$paricipationdetails[0]['price_user'];
        ///for groupprofilepopup mails show off////
        $parameters['AO_end_date']=date('d/m/Y H:i', $expires);
        //$parameters['AO_end_date']=date("d/m/Y",$expires)." &agrave; ".date("H:i:s",$expires);
        $parameters['article_title']=$paricipationdetails[0]['title'];
        $parameters['articlename_link']="http://ep-test.edit-place.com/contrib/mission-deliver?article_id=".$paricipationdetails[0]['article_id'];
        $parameters['ongoinglink']="http://ep-test.edit-place.com/contrib/ongoing";
        $parameters['correcteddate']=date("d/m/Y H:i",strtotime($paricipationdetails[0]['updated_at']));
        if($paricipationdetails[0]['deli_anonymous']=='1')
            $parameters['client_name']='inconnu';
        else
        {
            $clientDetails=$autoEmails->getUserDetails($paricipationdetails[0]['clientId']);
            if($clientDetails[0]['username']!=NULL)
                $parameters['client_name']= $clientDetails[0]['username'];
            else
            {
                $email = explode("@",$clientDetails[0]['email']);
                $parameters['client_name']= $email[0];
            }
        }
        /*$contribDetails=$autoEmails->getContribUserDetails($paricipationdetails[0]['user_id']);
        $parameters['contributor_name'] = $contribDetails[0]['firstname']." ".$contribDetails[0]['lastname'];*/
        $contribDetails=$autoEmails->getUserDetails($paricipationdetails[0]['user_id']);

        if($contribDetails[0]['username']!=NULL)
            $parameters['contributor_name']= $contribDetails[0]['username'];
        else
        {
            $email = explode("@",$contribDetails[0]['email']);
            $parameters['contributor_name']= $email[0];
        }
        //$email=$autoEmails->getAutoEmail($mailid);
        $email = $autoEmails->getMailComments(NULL,$mailid,$parameters);
        //echo  $emailComments = utf8_encode(stripslashes(html_entity_decode($email)));exit;
        echo  $emailComments = stripslashes(utf8_encode($email));exit;

    }
    ////making the category in readable formate/////
    public function unserialiseCategories($value) {
        $catorlag         = unserialize($value);
        $i                = 0;
        if ($catorlag != '') {
            foreach ($catorlag as $key => $value) {
                $key     = $this->category_array[$key];
                $res[$i] = "&nbsp;".$key."<b>(".$value.")</b>";
                $i++;
            }
            if ($res != '')
                return implode(",", $res);
        }
    }
    ////making the category in readable formate/////
    public function unserialiseLanguage($value) {
        $langlag         = unserialize($value);
        $i                = 0;
        if ($langlag != '') {
            foreach ($langlag as $key => $value) {
                $key     = $this->language_array[$key];
                $res[$i] = "&nbsp;".$key."<b>(".$value.")</b>";
                $i++;
            }
            if ($res != '')
                return implode(",", $res);
        }
    }
     //////////when a writer is selected and pop_submit and refus buttons are clicked/////////////////
    public function selectcontributorAction()
    {
        $prevurl = getenv("HTTP_REFERER");

        $participate_obj = new EP_Participation_Participation();
        $correctionpaticipation_obj = new EP_Participation_CorrectorParticipation();
        $autoEmails=new Ep_Message_AutoEmails();
        $article_obj = new EP_Delivery_Article();
        $delivery_obj = new EP_Delivery_Delivery();
        $profile_params=$this->_request->getParams();
        $contrib_id = $profile_params['contrib_id'];
        $particip_id = $profile_params['particip_id'];
        $Message = $profile_params['comment'];
        $artId = $profile_params['art_id'];
        $comments = $profile_params['comments'];
        $expires = $this->writerExpireTime($artId, $contrib_id);
        if(isset($profile_params["submit_pop"]) || $profile_params["button"]=="submit_pop")
        {

            $delDetails = $delivery_obj->getPrAoDetailsWithArtid($artId);
			
			//simultaneous correction conditions
				if($delDetails[0]['articlecorrection']=="yes")
				{
					//For public & multiple private case
					$correctors=explode(",",$delDetails[0]['private_correctors']);
					if($delDetails[0]['correction_type']== "private" && count($correctors)==1)
						$selectedcorrector=$delDetails[0]['private_correctors'];
					else
						$selectedcorrector=$correctionpaticipation_obj->getSelectedCorrector($artId);
				}
			
			if($contrib_id!=$selectedcorrector)
			{
				//Check already selected any other through cron
				$bidpresent=$participate_obj->checkRecordPresent($artId,'bid','contributor');
				if($bidpresent=="YES")
				{
					echo 'selectedwriter';exit;
				}
				$this->_view->type = 'accept';
				////udate status participation table for status///////
				$data = array("status"=>"bid", "accept_refuse_at"=>date("Y-m-d H:i:s", time()), "article_submit_expires"=>$expires, "selection_type"=>"bo");////////updating
				 $query = "user_id= '".$contrib_id."' AND id = '".$particip_id."'";
				$participate_obj->updateParticipation($data,$query);

				$refusedcontribs = $participate_obj->getRefusedContributors($artId);
				if($refusedcontribs!="NO")
				{
					for($i=0; $i<count($refusedcontribs); $i++)
					{
						////udate status participation table for status refuse remaining///////
						 $data1 = array("status"=>"bid_refused", "accept_refuse_at"=>date("Y-m-d H:i:s", time()), "selection_type"=>"bo");////////updating
						 $query1 = "user_id= '".$refusedcontribs[$i]['user_id']."' AND article_id = '".$artId."' AND cycle='0'";
						$participate_obj->updateParticipation($data1,$query1);
					}
				}
				
				
				
				//CorrectionParticipation participate_id update
				if($delDetails[0]['articlecorrection']=="yes")
				{
					$corrdata=array("participate_id"=>$particip_id);
					$CorrWhere= "corrector_id='".$selectedcorrector."' AND article_id='".$artId."'";
					$correctionpaticipation_obj->updateCrtParticipation($corrdata,$CorrWhere);
				}
				// echo $Message; exit;
				
				 /* *sending Mail**/
				//////sending mail to contributor who got selected in profile selections///////////////
				$automail=new Ep_Message_AutoEmails();
				$email=$automail->getAutoEmail(25);//
				$Object=$email[0]['Object'];
				$receiverId = $contrib_id;
				$automail->sendMailEpMailBox($receiverId,$Object,$Message);
				
				/////////////sending the emails to remaining contributors who got refused//////////////
				$paricipationdetails=$participate_obj->getParticipateDetails($particip_id);

				$parameters['article_title']=$paricipationdetails[0]['title'];
				$parameters['articlename_link']="/contrib/mission-deliver?article_id=".$artId;
				if($paricipationdetails[0]['deli_anonymous']=='1')
					$parameters['client_name']='inconnu';
				else
				{
					$clientDetails=$autoEmails->getUserDetails($paricipationdetails[0]['clientId']);
					if($clientDetails[0]['username']!=NULL)
						$parameters['client_name']= $clientDetails[0]['username'];
					else
						$parameters['client_name']= $clientDetails[0]['email'];
				}
				
				//refuse same user in correction
				$corrPart=$correctionpaticipation_obj->checkParticipationInCorrection($artId,$contrib_id);
				if($corrPart!='NO')
				{
					$data = array("status"=>"bid_refused", "accept_refuse_at"=>date("Y-m-d H:i:s", time()),  "selection_type"=>"bo");////////updating
					$query =  "article_id= '".$artId."' AND corrector_id='".$contrib_id."' AND status IN ('bid_corrector') AND cycle=0";
					$correctionpaticipation_obj->updateCrtParticipation($data,$query);
					
					//Mail
					$automail->messageToEPMail($contrib_id,29,$parameters);
				}
				
				if($refusedcontribs!="NO")
				{
					for($i=0; $i<count($refusedcontribs); $i++)
					{
						$automail->messageToEPMail($refusedcontribs[$i]['user_id'],27,$parameters);//
					}
				}
				/////////////article history////////////////
				$actparams['contributorId'] = $contrib_id;
				$actparams['artId'] = $artId;
				$actparams['stage'] = "selection profile";
				$actparams['action'] = "profile accepted";
				$this->articleHistory(2,$actparams);
				/////////////end of article history////////////////
			}
			else
			{
				echo 'selectedcorr';exit;
			}
			
				
            /// unlock the article///////////////
            $this->unlockonactionAction($artId);
            $this->_redirect($prevurl);

        }
        else if(isset($profile_params["refuse_pop"]) || $profile_params["button"]=="refuse_pop")
        {  
           ////udate status participation table for status///////
            $partdetails =  $participate_obj->getParticipateDetails($particip_id); 
            if($partdetails[0]['status'] == 'bid_premium' || $partdetails[0]['status'] == 'bid_temp' || $partdetails[0]['status'] == 'bid_refused_temp'){
                $data = array("status"=>"bid_refused", "accept_refuse_at"=>date("Y-m-d H:i:s", time()),  "article_submit_expires"=>$expires, "selection_type"=>"bo");////////updating
                 $sendmail = "forrefused";
            }
            elseif($partdetails[0]['status'] == 'bid' || $partdetails[0]['status'] == 'disapproved' || $partdetails[0]['status'] == 'time_out'){
                $data = array("status"=>"closed", "accept_refuse_at"=>date("Y-m-d H:i:s", time()),  "article_submit_expires"=>$expires, "selection_type"=>"bo");////////updating
                $sendmail = "forclosed";
            }
            $query = "user_id= '".$contrib_id."' AND id = '".$particip_id."'";
           $participate_obj->updateParticipation($data,$query);
            /////////////article history////////////////
            $partscount = $participate_obj->getNoOfParticipants($artId);
            $actparams['participation_count'] = $partscount[0]['partsCount'];
            $actparams['contributorId'] = $contrib_id;
            $actparams['artId'] = $artId;
            $actparams['stage'] = "selection profile";
            /////////////end of article history////////////////
            if($profile_params["sendtofo"] == 'yes')
            {
                $cycleZero = $participate_obj->findAnyCycleZero($artId);
                if($cycleZero == "NO")
                {
                    ////update the artlcle table with partcipation time/////////
                    $this->WriterParticipationExpire($artId);
                }
                else
                {
                    ////////////updating article time to zero as article should go back FO again  ///////
                    $artbacktoFO = $participate_obj->getArticlesBackToFo($artId);
                    if($artbacktoFO == "NO")
                    {
                        ////updating the article tabel article submit expire wiht zero///////
                        $this->WriterParticipationExpire($artId);
                        ///////check the cycle count in participation tabel and increament//////////
                        $cycleCount = $participate_obj->getParticipationCycles($artId);
                        $cycleCount1 = $cycleCount[0]['cycle']+1;
                        /////udate status participation table with article id///////
                        $data = array("cycle"=>$cycleCount1);////////updating
                        $query = "article_id= '".$artId."' and cycle=0";
                        $participate_obj->updateParticipation($data,$query);

                    }
                }
                if($profile_params["mailannoucement"] == 'sendmail')
                   $this->sendMailToContribs($artId);

                if($partdetails[0]['status'] == 'bid' || $partdetails[0]['status'] == 'disapproved')  {
                    $actparams['contributorId'] = $partdetails[0]['user_id'];
                    $actparams['action'] = "article not sent and republished";
                    $this->articleHistory(7,$actparams);
                }
                else{
                    $actparams['action'] = "republished";
                    $this->articleHistory(4,$actparams);
                }

            }
            elseif($profile_params["sendtofo"] == 'no')
            {
                ////updating the article tabel article submit expire wiht zero///////
                $data = array("send_to_fo"=>"no","file_path"=>"");////////updating
                $query = "id = '".$artId."'";
                $article_obj->updateArticle($data,$query);

            }
                //Delete royalities if any
                $Roy_obj= new Ep_Payment_Royalties();
                $Roy_obj->deleteRoyality($artId,$particip_id);
            /* * sending Mail**/
            if($sendmail == 'forrefused'){
                $email=$autoEmails->getAutoEmail(27);//
                $Object=$email[0]['Object'];
                $receiverId = $contrib_id;
                $autoEmails->sendMailEpMailBox($receiverId,$Object,$Message);
                /////////////article history////////////////
                $actparams['contributorId'] = $contrib_id;
                $actparams['artId'] = $artId;
                $actparams['stage'] = "selection profile";
                $actparams['action'] = "profile rejected";
               // $this->articleHistory(36,$actparams);
                /////////////end of article history////////////////
            }else if($sendmail == 'forclosed'){
                $paricipationdetails=$participate_obj->getParticipateDetails($particip_id);
                $parameters['article_title']=$paricipationdetails[0]['title'];
                $parameters['articlename_link']="/contrib/mission-deliver?article_id=".$artId;
                $autoEmails->messageToEPMail($contrib_id,48,$parameters);///

                if($paricipationdetails[0]['status'] == 'bid' || $paricipationdetails[0]['status'] == 'disapproved')
                {
                    $actparams['action'] = "article not sent and closed";
                    $this->articleHistory(8,$actparams);
                }
                else{
                    $actparams['action'] = "closed";
                    $this->articleHistory(5,$actparams);
                }
            }

            /// unlock the article///////////////
            $this->unlockonactionAction($artId);
            $this->_redirect("processao/profiles-list?submenuId=ML2-SL2");
        }
    }

    //////////giving the confirm box when last refusal is done/////////////////
    public function getlastartsAction()
    {
        $participate_obj = new EP_Participation_Participation();
        $article_obj=new EP_Delivery_Article();
        $refuse_params=$this->_request->getParams();
        ////////////updating article time to zero as article should go back FO again///////
        $lastartbacktoFO = $participate_obj->getlastArticlesBackToFo($refuse_params['artid']);
        if($lastartbacktoFO[0]['lastpartcount'] == "1")
           echo "yes";
        if(isset($refuse_params['sendToFo']) && $refuse_params['sendToFo']=='yes')
        {
            ////updating the article tabel article submit expire wiht zero///////
            $data = array("send_to_fo"=>"yes");////////updating
            $query = "id = '".$refuse_params['article_id']."'";
            $article_obj->updateArticle($data,$query);
        }
        if(isset($refuse_params['sendToFo']) && $refuse_params['sendToFo']=='no')
        {
            ////updating the article tabel article submit expire wiht zero///////
            $data = array("send_to_fo"=>"no");////////updating
            $query = "id = '".$refuse_params['article_id']."'";
            $article_obj->updateArticle($data,$query);
        }
    }
    /////making contirbutor black or non black////
    public function blackcontributorAction()
    {
        $user_params=$this->_request->getParams();
        $users_obj = new EP_User_User();
        $data = array("blackstatus"=>$user_params['status']);
        $query = "identifier= '".$user_params['user_id']."'";
        $users_obj->updateUser($data,$query);
    }
    ////loging credentila check in log in page////
    public function logincheck()
    {
        $log = $this->_request->getParam("log");
        $pass = $this->_request->getParam("pass");
        $user_obj = new Ep_User_User();
        $groupobj = new Ep_User_UserGroupAccess();
        $logtest = $user_obj->login($log, $pass);
        if ($logtest == true)
            return true;
        else
            return false;

    }
    public function listaoongoingAction()
    {
        $ao_obj=new Ep_Delivery_Delivery();

        $ao_list=$ao_obj->getAOlist($_GET['client'],0);

        $select_ao='';

        $select_ao.='<select name="aoId" id="aoId" data-placeholder="deliveries" style="width:360px;" OnChange="clientongoingao();">';
        $select_ao.='<option value="0" > </option>';
        for($cl=0;$cl<count($ao_list);$cl++)
        {

            $ao_list[$cl]['title']=$this->modifychar($ao_list[$cl]['title']);
            if($ao_list[$cl]['id']==$_GET['ao'])

                $select_ao.='<option value="'.$ao_list[$cl]['id'].'" selected>'.stripslashes(utf8_encode($ao_list[$cl]['title'])).'</option>';
            else

                $select_ao.='<option value="'.$ao_list[$cl]['id'].'" >'.stripslashes(utf8_encode($ao_list[$cl]['title'])).'</option>';
        }

        $select_ao.='</select>';
        echo $select_ao;
    }
    public function publishmaildynamicAction()
    {
        $publishparams =  $this->_request->getParams();
        $ao_id = $publishparams['ao_id'];
        $publish = $publishparams['publish'];
        $publish = str_replace('/','-',$publish);
      // echo  $publish = date('Y-m-d H:i', strtotime($date1));
      //   echo "<pre>".strtotime($publish); echo "<pre>".date('d/m/Y H:i', $publish); exit;
      // echo  $datetime =  $publishparams['datetime'];  exit;
        $this->publishmailcontent($ao_id, $publish);
    }
    ////////mail content for publish pop up///////////
    public function publishmailcontent($ao_id, $publish)
    {
        ////update the artlcle table with partcipation time/////////
        $delivery_obj = new Ep_Delivery_Delivery();
        $article_obj = new EP_Delivery_Article();
        $automail = new Ep_Message_AutoEmails();
        $user_obj = new Ep_User_User();
        $participate_obj = new EP_Participation_Participation();

        $delartdetails = $delivery_obj->getArticlesOfDel($ao_id);
        $expires=time()+(60*$delartdetails[0]['participation_time']);
        $data = array("participation_expires"=>$expires);////////updating
        $artIds = explode("@",$delartdetails[0]['artIds']);
        for($i=0; $i<=count($artIds); $i++)
        {
            $query = "id= '".$artIds[$i]."'";
          //  $article_obj->updateArticle($data,$query);
        }
        /*Sending mail to client when publish **/
        $aoDetails=$delivery_obj->getPrAoDetails($ao_id);
        $autoEmails=new Ep_Message_AutoEmails();
        $parameters['AO_title']=$aoDetails[0]['title'];
        $parameters['AO_end_date']=$aoDetails[0]['delivery_date'];
        //$parameters['submitdate_bo']=$aoDetails[0]['submitdate_bo'];
        if($publish=='no')
            $parameters['submitdate_bo']=date('d/m/Y H:i', $expires);
        elseif($publish=='now')
            $parameters['submitdate_bo']=date('d/m/Y H:i', $expires);
        elseif($publish=='late')
        {
            $expires+=60*60*24;
            //$parameters['submitdate_bo']=date('d/m/Y H:i', $expires);
            $parameters['submitdate_bo']=strftime("%d/%m/%Y &agrave; %H:%M",$expires);
        }
        else
        {
            //$publish = date('Y-m-d h:i', strtotime($publish));   echo "<pre>".strtotime($publish);
           // $parameters['submitdate_bo']=date('d/m/Y H:i', $publish);
            //  echo strtotime($publish); echo date('d/m/Y H:i', $publish); exit;
            $expires =strtotime($publish);
            $parameters['submitdate_bo']=strftime("%d/%m/%Y &agrave; %H:%M",$expires);
        }
        //echo  $parameters['submitdate_bo'];
        $parameters['noofarts']=$aoDetails[0]['noofarts'];
        if($aoDetails[0]['deli_anonymous']=='0')
            $parameters['article_link']="/contrib/aosearch?client_contact=".$aoDetails[0]['user_id'];
        else
            $parameters['article_link']="/contrib/aosearch?client_contact=anonymous";
        $parameters['aoname_link'] = "/contrib/aosearch";
        $parameters['clientartname_link'] = "/client/quotes?id=".$aoDetails[0]['articleid'];

        if($aoDetails[0]['AOtype']=='private')
        {
            $contributors=array_unique(explode(",",$aoDetails[0]['article_contribs']));
            if(is_array($contributors) && count($contributors)>0)
            {
                if(count($contributors)==1)
                {
                    if($aoDetails[0]['premium_option']=='0')
                        $automailid=19;//
                    else
                        $automailid=20;//
                }
                else
                {
                    if($aoDetails[0]['premium_option']=='0')
                        $automailid=17;//
                    else
                        $automailid=18;//
                }
                $emailobj=$automail->getAutoEmail($automailid);
                $Object=utf8_encode(stripslashes($emailobj[0]['Object']));
                $email = $automail->getMailComments($userid=null,$automailid,$parameters);
                $emailComments = utf8_encode(stripslashes($email));
                ////client mail content////
                $clientemailobj=$automail->getAutoEmail(5);
                $clObject=utf8_encode(stripslashes($clientemailobj[0]['Object']));
                $clientemail = $automail->getMailComments($userid=null,5,$parameters);
                $clientemailComments = utf8_encode(stripslashes($clientemail));
                $emailComments = $emailComments.'*@#'.$Object.'*@#'.$clientemailComments.'*@#'.$clObject;
            }
        }
        elseif($aoDetails[0]['AOtype']=='public')
        {
            if($deldetails[0]['created_by'] != 'BO')
            {
                $contributors=$user_obj->getSeniorFrContributors($aoDetails[0]['language']);
                if(is_array($contributors) && count($contributors)>0)
                {
                    $sclimit=$this->configval["sc_limit"];
                    foreach($contributors as $contributor)
                    {
                        $countofparts=$participate_obj->getCountOnStatus($contributor['identifier']);
                        if($sclimit > $countofparts[0]['partscount'])
                        {
                            if($aoDetails[0]['premium_option']=='0')
                                $automailid=14;//
                            else
                                $automailid=15;//
                            $emailobj=$automail->getAutoEmail($automailid);
                            $Object=utf8_encode(stripslashes($emailobj[0]['Object']));
                            $email = $automail->getMailComments($userid=null,$automailid,$parameters);
                            $emailComments = utf8_encode(stripslashes($email));
                            ////client mail content////
                            $clientemailobj=$automail->getAutoEmail(5);
                            $clObject=utf8_encode(stripslashes($clientemailobj[0]['Object']));
                            $clientemail = $automail->getMailComments($userid=null,5,$parameters);
                            $clientemailComments = utf8_encode(stripslashes($clientemail));
                            $emailComments = $emailComments.'*@#'.$Object.'*@#'.$clientemailComments.'*@#'.$clObject;
                        }
                    }
                }
            }
        }
        if($publish=='no')
            return  $emailComments;
        else
            echo  $emailComments;
    }
	/* Generate XL */
	function generatePartcipantXlsAction()
	{
		$usercomment_obj = new Ep_Message_UserComments();
        $contrib_obj     = new EP_User_Contributor();
        $participate_obj = new EP_Participation_Participation();
        $article_obj     = new EP_Delivery_Article();
        $delivery_obj     = new Ep_Delivery_Delivery();
        $artprocess_obj = new EP_Delivery_ArticleProcess();
        $user_obj     = new Ep_User_User();

        $partParams      = $this->_request->getParams();

        $lastartbacktoFO = $participate_obj->getlastArticlesBackToFo($partParams['artId']);
        if ($partParams['artId']) {
            $artId        = $partParams['artId'];
            $participants = $participate_obj->getGroupParticipants($artId);
            $delDetails = $delivery_obj->getPrAoDetailsWithArtid($artId);
	
            $art_cat = $this->category_array[$delDetails[0]['art_category']];
            $ep_languages = $this->_arrayDb->loadArrayv2('EP_LANGUAGES', $this->_lang);;
            $art_lang = $ep_languages[$delDetails[0]['artLanguage']];
            if($delDetails[0]['estimated_worktime'])
			$worktime = $delDetails[0]['estimated_worktime'];
			else
			$worktime = 0;
			if($delDetails[0]['estimated_workoption']=="min")
			$workoption = "Minutes";
			else
			$workoption = "Hours";
			
			require_once APP_PATH_ROOT.'nlibrary/tools/PHPExcel.php';
			require_once APP_PATH_ROOT.'nlibrary/tools/PHPExcel/Writer/Excel2007.php';

			$spreadsheet = new PHPExcel();
			$spreadsheet->setActiveSheetIndex(0);
			$worksheet = $spreadsheet->getActiveSheet();
			$spreadsheet->getActiveSheet()->getStyle('5')->getFont()->setBold(true);
			$spreadsheet->getActiveSheet()->getStyleByColumnAndRow(0,1)->getFont()->setBold(true);
			$spreadsheet->getActiveSheet()->getStyleByColumnAndRow(0,2)->getFont()->setBold(true);
			$spreadsheet->getActiveSheet()->getStyleByColumnAndRow(0,3)->getFont()->setBold(true);
			
			$worksheet->SetCellValueByColumnAndRow(0, 1, 'Client\'s Name');
			$worksheet->SetCellValueByColumnAndRow(0, 2, 'Project Manager');
			$worksheet->SetCellValueByColumnAndRow(0, 3, 'Date of Creation');
			
			$user_obj=new Ep_User_User();
			$user_info = $user_obj->getAllUsersDetails($delDetails[0]['user_id']);
				
			if($user_info)
			$clname = $user_info[0]['first_name']." ".$user_info[0]['last_name'];
			else
			$clname = "";
			
			$user_info = $user_obj->getAllUsersDetails($delDetails[0]['created_user']);
			if($user_info)
			$proj_mgr = $user_info[0]['first_name']." ".$user_info[0]['last_name'];
			else
			$proj_mgr = "";
			
			$worksheet->SetCellValueByColumnAndRow(1, 1, utf8_encode($clname));
			$worksheet->SetCellValueByColumnAndRow(1, 2, utf8_encode($proj_mgr));
			$worksheet->SetCellValueByColumnAndRow(1, 3, date('d-m-Y',strtotime($delDetails[0]['created_at'])));
			
			$worksheet->SetCellValueByColumnAndRow(0, 5, 'Email');
			$worksheet->SetCellValueByColumnAndRow(1, 5, 'Price Proposed');
			$worksheet->SetCellValueByColumnAndRow(2, 5, 'Status');
			$worksheet->SetCellValueByColumnAndRow(3, 5, 'Min Price of AO');
			$worksheet->SetCellValueByColumnAndRow(4, 5, 'Max Price of AO');
			$worksheet->SetCellValueByColumnAndRow(5, 5, 'Estimated Time');
			$worksheet->SetCellValueByColumnAndRow(6, 5, 'Category');
			$worksheet->SetCellValueByColumnAndRow(7, 5, 'Language');
			
			$writer = new PHPExcel_Writer_Excel2007($spreadsheet);
			
			$contribDetails = array();
			$k=6;
			if(is_array($participants)):
            for ($i = 0; $i < count($participants); $i++) {
                $contribDetails[$i]     = $contrib_obj->getGroupProfilesInfo($participants[$i]['user_id'], $participants[$i]['id'], $artId);
                $gobalcommentscount[$i] = $usercomment_obj->getCommentsCount($participants[$i]['user_id']);
                $cnt                    = 0;
				 
               // foreach ($contribDetails[$i] as $details) 
			   if($contribDetails[$i][$cnt]['cycle']==0)
			   {
                    $percentage  = $contribDetails[$i][$cnt]['contrib_percentage'];
                    $minPrice    = $contribDetails[$i][$cnt]['price_min'];
                    $maxPrice    = $contribDetails[$i][$cnt]['price_max'];
                    $writerPrice = $contribDetails[$i][$cnt]['price_user'];

                    if ($percentage != NULL) {
                        $contribDetails[$i][$cnt]['price_min'] = $minPrice / 100 * $percentage;
                        $contribDetails[$i][$cnt]['price_max'] = $maxPrice / 100 * $percentage;
                    } else {
                        $contribDetails[$i][$cnt]['price_min'] = $writerPrice;
                        $contribDetails[$i][$cnt]['price_max'] = $writerPrice;
                    }
                  
					$worksheet->SetCellValueByColumnAndRow(0, $k, utf8_encode($contribDetails[$i][$cnt]['email']));
					$worksheet->SetCellValueByColumnAndRow(1, $k, $contribDetails[$i][$cnt]['price_user']);
					$worksheet->SetCellValueByColumnAndRow(2, $k, ucfirst($contribDetails[$i][$cnt]['profile_type']));
					$worksheet->SetCellValueByColumnAndRow(3, $k, $contribDetails[$i][$cnt]['price_min']);
					$worksheet->SetCellValueByColumnAndRow(4, $k, $contribDetails[$i][$cnt]['price_max']);
					$worksheet->SetCellValueByColumnAndRow(5, $k, utf8_encode($worktime." ".$workoption));
					$worksheet->SetCellValueByColumnAndRow(6, $k, utf8_encode(trim($art_cat)));
					$worksheet->SetCellValueByColumnAndRow(7, $k, utf8_encode($art_lang));
					$k++;
				   
                }
            }
			endif;
			$cfilename = "participants_".$artId.".xlsx";			
			$file_path = $_SERVER['DOCUMENT_ROOT']."/BO/participantsxls/".$cfilename;
			
			$writer->save($file_path);
			$this->_redirect("/BO/download-quote-xls.php?art_id=".$cfilename);
        }
		
	}
    //////////its for testing/////////////////
    public function getwrongdataAction()
    {
        $participate_obj = new EP_Participation_Participation();
        $crtparticipate_obj = new EP_Participation_CorrectorParticipation();
        $article_obj=new EP_Delivery_Article();
        $artprocess_obj = new EP_Delivery_ArticleProcess();
        $partids = $participate_obj->getAllPartIds();  print_r($partids);
        if($partids != "NO")
        {
             foreach($partids as $key => $value)
             {  //$partids[$key]['id']  142113767390555

                 $artproc_array = $artprocess_obj->partidInArticleProcess($partids[$key]['id']); //echo "<pre>";print_r($artproc_array);
                 if($artproc_array != "NO") {
                     $array_count = count($artproc_array);
                     for ($n = 0; $n < $array_count; $n++) {
                         $inparticipation = $participate_obj->checkUserWithParticipation($artproc_array[$n]['participate_id'], $artproc_array[$n]['user_id']);
                         $incrtparticipation = $crtparticipate_obj->checkUserWithCrtParticipation($artproc_array[$n]['participate_id'], $artproc_array[$n]['user_id']);

                         if ($inparticipation == 'YES' && $incrtparticipation == 'NO') {
                             $res[$n]['userAS'] = 'writer';
                         } elseif ($inparticipation == 'NO' && $incrtparticipation == 'YES') {
                             $res[$n]['userAS'] = 'corrector';
                         } elseif ($inparticipation == 'NO' && $incrtparticipation == 'NO') {
                             $res[$n]['userAS'] = 'bouser';
                         }
                     }
                     for ($i = 0; $i < $array_count; $i++) {
                         $res[$i]['partId'] = $artproc_array[$i]['participate_id'];
                         $res[$i]['userId'] = $artproc_array[$i]['user_id'];
                         $inparticipation = $participate_obj->checkUserWithParticipation($artproc_array[$i]['participate_id'], $artproc_array[$i]['user_id']);
                         $incrtparticipation = $crtparticipate_obj->checkUserWithCrtParticipation($artproc_array[$i]['participate_id'], $artproc_array[$i]['user_id']);

                         if ($inparticipation == 'YES' && $incrtparticipation == 'NO') {
                             $res[$i]['userAS'] = 'writer';
                         } elseif ($inparticipation == 'NO' && $incrtparticipation == 'YES') {
                             $res[$i]['userAS'] = 'corrector';
                         } elseif ($inparticipation == 'NO' && $incrtparticipation == 'NO') {
                             $res[$i]['userAS'] = 'bouser';
                         }
                         ///actuall stage and status/////
                         if ($artproc_array[$i]['version'] == 1) {
                             $res[$i]['actual_stage'] = 'contributor';
                             $res[$i]['actual_status'] = 'null';
                             $res[$i]['actual_activity'] = $artproc_array[$i]['stage'] . "/" . $artproc_array[$i]['status'];
                         }
                         ////if writer/////
                         if ($res[$i]['userAS'] == 'writer') {
                             if($res[$i - 1]['userAS'] == 'writer' && $res[$i + 1]['userAS'] == 'writer') {
                                 $res[$i]['actual_stage'] = 'contributor';
                                 $res[$i]['actual_status'] = 'null';
                                 $res[$i]['actual_activity'] = "s0/disapproved";
                             }elseif($res[$i - 1]['userAS'] == 'writer' && $res[$i + 1]['userAS'] == 'bouser') {
                                 $res[$i]['actual_stage'] = 'contributor';
                                 $res[$i]['actual_status'] = 'null';
                                 $res[$i]['actual_activity'] = "s0/disapproved";
                             }elseif($res[$i - 1]['userAS'] == 'writer' && $res[$i + 1]['userAS'] == 'corrector') {
                                 $res[$i]['actual_stage'] = 'contributor';
                                 $res[$i]['actual_status'] = 'null';
                                 $res[$i]['actual_activity'] = "s0/uploaded";
                             }else {
                                 $res[$i]['actual_stage'] = 'contributor';
                                 $res[$i]['actual_status'] = 'null';
                                 $res[$i]['actual_activity'] = "s1-s2/refused";
                             }
                         }
                         ////if corrector/////
                         if ($res[$i]['userAS'] == 'corrector') {
                             if ($res[$i - 1]['userAS'] == 'writer' && $res[$i + 1]['userAS'] == 'corrector') { //echo "jill1"; exit;
                                 $res[$i]['actual_stage'] = 'corrector';
                                 $res[$i]['actual_status'] = 'null';
                                 $res[$i]['actual_activity'] = "s2/disapproved";
                             }elseif ($res[$i - 1]['userAS'] == 'corrector' && $res[$i + 1]['userAS'] == 'corrector') { //echo "jill"; exit;
                                 $res[$i]['actual_stage'] = 'corrector';
                                 $res[$i]['actual_status'] = 'null';
                                 $res[$i]['actual_activity'] = "s2/disapproved";
                             }elseif ($res[$i - 1]['userAS'] == 'corrector' && $res[$i + 1]['userAS'] == 'bouser') {
                                 $res[$i]['actual_stage'] = 'corrector';
                                 $res[$i]['actual_status'] = 'null';
                                 $res[$i]['actual_activity'] = "s2/refused";
                             }elseif ($res[$i - 1]['userAS'] == 'writer' && $res[$i + 1]['userAS'] == 'bouser') {
                                 $res[$i]['actual_stage'] = 'corrector';
                                 $res[$i]['actual_status'] = 'null';
                                 $res[$i]['actual_activity'] = "s2/approved";
                             }else{
                                 $res[$i]['actual_stage'] = 'corrector';
                                 $res[$i]['actual_status'] = 'null';
                                 $res[$i]['actual_activity'] = "--";
                             }
                         }
                         ////if bouser/////
                         if ($res[$i]['userAS'] == 'bouser') {
                             if ($res[$i - 1]['userAS'] == 'writer' && $res[$i + 1]['userAS'] == 'bouser') { //echo "jill1"; exit;
                                 $res[$i]['actual_stage'] = 's1';
                                 $res[$i]['actual_status'] = 'approved';
                                 $res[$i]['actual_activity'] = "s1/approved";
                             }elseif ($res[$i - 1]['userAS'] == 'writer' && $res[$i + 1]['userAS'] == 'writer') {
                                 $res[$i]['actual_stage'] = 's1';
                                 $res[$i]['actual_status'] = 'disapproved';
                                 $res[$i]['actual_activity'] = "s1/approved";
                             }elseif ($res[$i - 1]['userAS'] == 'bouser' && $res[$i + 1]['userAS'] == 'writer') {
                                 $res[$i]['actual_stage'] = 's2';
                                 $res[$i]['actual_status'] = 'disapproved';
                                 $res[$i]['actual_activity'] = "s2/approved";
                             }elseif ($res[$i - 1]['userAS'] == 'corrector' && $res[$i + 1]['userAS'] == 'corrector') {
                                 $res[$i]['actual_stage'] = 's2';
                                 $res[$i]['actual_status'] = 'disapproved';
                                 $res[$i]['actual_activity'] = "s2/approved";
                             }elseif ($res[$i - 1]['userAS'] == 'bouser') {
                                 $res[$i]['actual_stage'] = 's2';
                                 $res[$i]['actual_status'] = $artproc_array[$i]['status'];
                                 $res[$i]['actual_activity'] = "---";
                             }elseif ($res[$i - 1]['userAS'] == 'corrector') {
                                 $res[$i]['actual_stage'] = 's2';
                                 $res[$i]['actual_status'] = $artproc_array[$i]['status'];
                                 $res[$i]['actual_activity'] = "---";
                             }else {
                                 $res[$i]['actual_stage'] = 's1';
                                 $res[$i]['actual_status'] = 'app-disp';
                                 $res[$i]['actual_activity'] = "---";
                             }
                         }

                         $res[$i]['stage'] = $artproc_array[$i]['stage'];
                         $res[$i]['status'] = $artproc_array[$i]['status'];

                     }
                     $result[$i] = '<table><tr><td>----</td></tr></table><table border="1">';
                     $result[$i] .= '<tr><td>participate Id</td><td>User Id</td><td>User AS</td>
                    <td bgcolor="#FFCCCC">Stage</td><td bgcolor="#CCCCE0">Status</td><td bgcolor="#FFCCCC">Actual Stage</td><td bgcolor="#CCCCE0">Actual Status</td></tr>';
                     for ($j = 0; $j < $array_count; $j++) {
                         $result[$i] .= '<tr><td>' . $res[$j]["partId"] . '</td><td>' . $res[$j]["userId"] . '</td><td>' . $res[$j]["userAS"] . '</td>
                    <td bgcolor="#FFCCCC">' . $res[$j]["stage"] . '</td><td bgcolor="#CCCCE0">' . $res[$j]["status"] . '</td><td bgcolor="#FFCCCC">' . $res[$j]["actual_stage"] . '</td><td bgcolor="#CCCCE0">' . $res[$j]["actual_status"] . '</td></tr>';
                     }
                     $result[$i] .= '</table>';
                     echo $result[$i];
                 }
             }
        }
    }
	
	public function downloadSelectedContribxlsAction()
	{
		error_reporting(E_ALL);
			ini_set('display_errors', TRUE);
			ini_set('display_startup_errors', TRUE);
			date_default_timezone_set('Europe/London');

			include '/home/sites/site6/web/BO/nlibrary/tools/PHPExcel.php';

			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
										 ->setLastModifiedBy("Maarten Balliauw")
										 ->setTitle("Office 2007 XLSX Test Document")
										 ->setSubject("Office 2007 XLSX Test Document")
										 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("Test result file");


			// Add some data
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A1', 'Hello')
						->setCellValue('B2', 'world!')
						->setCellValue('C1', 'Hello')
						->setCellValue('D2', 'world!');
$objPHPExcel->getActiveSheet()->setTitle('Simple');
			$objPHPExcel->setActiveSheetIndex(0);


			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="selectionxls11111.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		//$this->_redirect("/BO/selection-profile-xls.php?mission=".$_REQUEST['mission']);

			exit;
		/* $ao_id=$_REQUEST['mission'];
		
		$delivery_obj = new Ep_Delivery_Delivery();
		$articles=$delivery_obj->missionarticlelist($ao_id);
		
			
		
			
				ob_start();
                 echo '<table border="1"> ';
					echo '<tr>
							<th>Title of article</th>
							<th>Name of writer</th>
							<th>Status</th>
							<th>Price proposed(&euro;)</th>
							<th>Selection Type</th>
						</tr>';
                 
                 for($p=0;$p<count($articles);$p++)
                 {
                     $details=$delivery_obj->missionselectedcontribs($articles[$p]['id']);
					 $name=$details[0]['first_name'].' '.$details[0]['last_name'];
					  
					 if($details[0]['profile_type']!="")
					 {
						if($details[0]['selection_type']=="auto" || $details[0]['selection_type']=="auto_temp")
							$selection='Auto';
						else
							$selection='Manual';
					 }
					else
					
						$selection=$details[0]['selection_type'];
					
						
					 echo '<tr>
								<td valign="top">'.$articles[$p]['title'].'</td>
								<td valign="top">'.$name.'</td>
								<td valign="top">'.$details[0]['profile_type'].'</td>
								<td valign="top">'.$details[0]['price_user'].'</td>
								<td valign="top">'.$selection.'</td>
							</tr>';
                     
                 }
                 echo '</table>';
                 //exit;
				 
				 $title=$articles[0]['dtitle'];
				 $title=str_replace(" ","_",$title);
                 $content = ob_get_contents();
                 ob_end_clean();
                 header("Expires: 0");
                 header("Cache-Control: post-check=0, pre-check=0", false);
                 header("Pragma: no-cache");  header("Content-type: application/vnd.ms-excel;charset:UTF-8");
                 header('Content-length: '.strlen($content));
                 header('Content-disposition: attachment; filename='.$title.'.xls');
                 echo $content;
                 exit;*/
				 
	}
	
	public function checkparticipationincorrectionAction()
	{
		$crtparticipate_obj = new EP_Participation_CorrectorParticipation();
		$corrPart=$crtparticipate_obj->checkParticipationInCorrection($_REQUEST['artid'],$_REQUEST['contrib_id']);
		
		if($corrPart=='NO')
			echo 'no';
		else
			echo 'yes';
			
	}
	
	public function refusecorrectionparticipationAction()
	{
		$crtparticipate_obj = new EP_Participation_CorrectorParticipation();
		$data = array("status"=>"bid_refused", "accept_refuse_at"=>date("Y-m-d H:i:s", time()),  "selection_type"=>"bo");////////updating
        $query =  "article_id= '".$_REQUEST['artid']."' AND corrector_id='".$_REQUEST['contrib_id']."' AND status IN ('bid_corrector') AND cycle=0";
        $crtparticipate_obj->updateCrtParticipation($data,$query);
	}
	

}
