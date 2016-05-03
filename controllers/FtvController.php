<?

class FtvController extends Ep_Controller_Action
{
	private $controller = "ftv";
	protected $session = null;
	private $text_admin;
	private $my_obj;
	private $filesname;
    private $entryLang;
    private $cntry;
    private $totalLang;
    private $my_view;

	public function init()
	{
        parent::init();
        $this->_view->lang = $this->_lang;
        $this->adminLogin  = Zend_Registry::get('adminLogin');
        $this->_view->userId = $this->adminLogin->userId;
        $this->sid         = session_id();

        ////if session expires/////  challasudhakar@gmail.com
        if($this->adminLogin->loginName == '') {
            $this->_redirect('/index');
            echo "session expired...please <a href='http://admin-test.edit-place.com/index'>click here</a> to login"; exit;
        }

    }
    // check the email contact is exit or not
    public function emailExitsAction()
    {
        $ftv_obj=new Ep_Ftv_FtvContacts();
        $user_params=$this->_request->getParams();

		$emailexit = $ftv_obj->getExistingEmail($user_params['email']);
		$emailexit=trim($emailexit);
		echo $emailexit;
		exit;
    }
    //  edit the request demand by client by incharge //
    public function editrequestAction()
    {
        $this->_view->ftvId  = $this->ftvcontacts->ftvId;
        $ftvcontacts_obj = new Ep_Ftv_FtvContacts();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvcomments_obj = new Ep_Ftv_FtvComments();
        $ftvdocuments_obj = new Ep_Ftv_FtvDocuments();
        $allcontacts =  $ftvcontacts_obj->getFtvContacts('edito');

        $serverpath = '/home/sites/site5/web/FO/ftv_documents/';
        $contact_list=array();
        foreach($allcontacts as $key=>$value)
        {
            $contact_list[$value['identifier']]=utf8_encode($value['email_id']);
        }
        if($contact_list)
            asort($contact_list);

        $broadcast_array=$this->_arrayDb->loadArrayv2("EP_BROADCASTS", $this->_lang);
        $this->_view->broadcast_array = $broadcast_array;
        $this->_view->ftvcontacts  = $contact_list;
        $request_params = $this->_request->getParams();
        if($request_params['request_id'] != '' && $request_params['edit_demand'] == 'yes')
        {
            $request_id =  $request_params['request_id'];
            $othercontacts =  implode(",",$request_params["othercontacts"]);
            $quand = implode(",",$request_params["quand"]);
            $modifycontains = implode(",",$request_params["modifycontains"]);
            $modifybroadcast = implode(",",$request_params["broadcasts"]);
            $request_object = utf8_decode($request_params["request_object"]);
            $data = array("other_contacts"=>$othercontacts, "request_object"=>$request_object, "duration"=>$quand,
                "modify_broadcast"=>$modifybroadcast, "modify_contains"=>$modifycontains);
            $query = "identifier= '".$request_id."'";
            $ftvrequest_obj->updateFtvRequests($data,$query);

            $this->_redirect("/ftv/ftv-requests?submenuId=ML11-SL3");
        }
        $this->_view->ftveditoeditrequest = 'yes';
        $this->render("ftv_ftvaddcomment");
    }
    // duplicating the request demand ///
    public function duplicaterequestAction()
    {
        $ftvcontacts_obj = new Ep_Ftv_FtvContacts();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvcomments_obj = new Ep_Ftv_FtvComments();
        $ftvdocuments_obj = new Ep_Ftv_FtvDocuments();
        $allcontacts =  $ftvcontacts_obj->getFtvContacts('edito');
        $contact_list=array();
        foreach($allcontacts as $key=>$value)
        {
            $contact_list[$value['identifier']]=utf8_encode($value['email_id']);
        }
        if($contact_list)
            asort($contact_list);

        $this->_view->ftvcontacts  = $contact_list;

        $broadcast_array=$this->_arrayDb->loadArrayv2("EP_BROADCASTS", $this->_lang);
        $this->_view->broadcast_array = $broadcast_array;
        $request_params=$this->_request->getParams();
        $this->_view->edit_demand  = $request_params['edit'];
        $this->_view->request_id  = $request_params['request_id'];
        if($request_params['request_id'] != '')
        {
            $request_id =  $request_params['request_id'];
            $details = $ftvrequest_obj->getRequestById($request_id);
            $this->_view->othercontacts_array = explode(",", $details[0]['other_contacts']);
            $this->_view->quand_array = explode(",", $details[0]['duration']);
            $this->_view->contains_array = explode(",", $details[0]['modify_contains']);
            $this->_view->broadcasts_array = explode(",", $details[0]['modify_broadcast']);
            // print_r($details);  exit;
            $this->_view->requestsdetail=$details;
        }
        $this->_view->ftveditoeditrequest = 'yes';
        $this->render("ftv_ftvaddcomment");
    }
    public function ftvRequestsAction()
    {
        $this->render("ftv_ftvrequests");
    }
    /* function edited by naseer on 04-09-2015*/
    // ftv chaine request and loading via ajax //
    public function loadftvrequestsAction()
    {
        $broadcast_array=$this->_arrayDb->loadArrayv2("EP_BROADCASTS", $this->_lang);
        $ftvcontacts_obj = new Ep_Ftv_FtvContacts();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvcomments_obj = new Ep_Ftv_FtvComments();
        $ftvdocuments_obj = new Ep_Ftv_FtvDocuments();
        $ftvpausetime_obj = new Ep_Ftv_FtvPauseTime();
        $loginuser = $this->adminLogin->userId;
        $contacts = $ftvcontacts_obj->getRequestCreatedContacts(); // print_r($contacts); exit;
        $this->_view->contacts_array = $contacts;
        if($loginuser != '110823103540627')
            $aColumns = array( 'first_name', 'created_at', 'request_object','download','duration','modify_contains','modify_broadcast','demand','status','comments','time_spent','identifier');
        else
            $aColumns = array( 'first_name', 'created_at', 'request_object','download','duration','modify_contains','modify_broadcast','demand','assingnation','status','comments','time_spent','identifier');
        /* * Paging	 */
        $sLimit = "";
        $broadcast_sort_flag = true;
        $broadcast_sort = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
            $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
                intval( $_GET['iDisplayLength'] );
        }
        /* 	 * Ordering   	 */
        $col_array = array('request_object','download','duration','modify_contains', 'modify_broadcast','demand','assingnation','status','comments','time_spent');
        $sOrder = "";
      // echo  $_GET['iSortCol_0']. "----".intval( $_GET['iSortingCols'] );
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
                    if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ] === 'modify_broadcast'){
                        $to_sort = 'modify_broadcast';
                        $broadcast_sort_flag = true;
                        $broadcast_sort = ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc');
                    }
                    else if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ] === 'duration'){
                        $to_sort = 'duration';
                        $broadcast_sort_flag = true;
                        $broadcast_sort = ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc');
                    }
                    else if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ] === 'modify_contains'){
                        $to_sort = 'modify_contains';
                        $broadcast_sort_flag = true;
                        $broadcast_sort = ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc');
                    }
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
            for ( $i=0 ; $i<3; $i++ )//only first 3 fields will return proper values rest will return xml saved values
            {
                $keyword=addslashes($_GET['sSearch']);
                $keyword = preg_replace('/\s*$/','',$keyword);
                $keyword=preg_replace('/\(|\)/','',$keyword);
                $words=explode(" ",$keyword);
                    $sWhere.=$aColumns[$i]." like '%".utf8_decode($keyword)."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';

        }

        /* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns)-1 ; $i++ )
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
        if($loginuser != "")
        {
            $ftv_params=$this->_request->getParams();
            if($ftv_params['search'] == 'search')
            { //echo "mash"; exit;
                $condition['search'] = $ftv_params['search'];
                $condition['contactId'] = $ftv_params['contactId'];
                $condition['containsId'] = $ftv_params['containsId'];
                $condition['broadcastId'] = $ftv_params['broadcastId'];
                $condition['quandId'] = $ftv_params['quandId'];
                $condition['startdate'] = $ftv_params['startdate'];
                $condition['enddate'] = $ftv_params['enddate'];
                $condition['dayrange'] = $ftv_params['dayrange'];
            }
            $requestsdetail=$ftvrequest_obj->getAllEditoRequestsDetails($sWhere, $sOrder, $sLimit, $condition);
            //contains to modify///
            $contains_array = array(1=>"UNE Tournante", 2=>"Diffusion", 3=>"Article", 4=>"Ressource Livres",5=>"Ressource Voir/Ecouter",6=>"Ressource Liens et Adresses utiles", 7=>"Galerie photo", 8=>"Musique", 9=>"Autres");
            $duraiton_array = array("h"=>"Dans l'heure", "d"=>"Dans la journ&eacute;e","nd"=>"Le lendemain", "w"=>"Dans la semaine","nw"=>"La semaine prochaine");
            $this->_view->contains_array = $contains_array;
            $this->_view->quands_array = $duraiton_array;
            $demand_array = array("1"=>'Int&eacute;gration', "2"=>'Modification demand&eacute;e par FTV', "3"=>'Correction erreur EP', "4"=>'Retours');
            $this->_view->broadcast_array = $broadcast_array;
            $this->_view->demand_array = $demand_array;
           //print_r($requestsdetail); exit;
            if($requestsdetail != 'NO')
            {
                $gtdays = '';$gthours = ''; $gtminutes = '';$gtdiff='';
                foreach ($requestsdetail as $key => $value)
                {
                    $inpause  = $ftvpausetime_obj->inPause($requestsdetail[$key]['identifier']);
                    $requestsdetail[$key]['inpause'] = $inpause;
                    $durationvalue = $duraiton_array[$requestsdetail[$key]['duration']];
                    $requestsdetail[$key]['duration']   = $durationvalue;
                    $requestsdetail[$key]['demand'] = $demand_array[$requestsdetail[$key]['demand']];
                    /////getting modify contains display format///
                    $contains  = explode(",",$requestsdetail[$key]['modify_contains']);
                    $finalcontains = array();
                    foreach($contains as $code1 => $abb1)
                    {
                        $finalcontains[$code1] = $contains_array[$abb1];
                    }
                    $containvalue = implode(" / ",$finalcontains);
                    $requestsdetail[$key]['modify_contains']   = $containvalue;
                    ////getting modify broadcast display format//
                    $braodcast  = explode(",",$requestsdetail[$key]['modify_broadcast']);
                    $finalbroadcast = array();
                    foreach($braodcast as $broadkey => $broadval)
                    {
                        $finalbroadcast[$broadkey] = $broadcast_array[$broadval];
                    }
                    $broadvalue = implode(" / ",$finalbroadcast);
                    $requestsdetail[$key]['modify_broadcast']   = $broadvalue;

                    ////gettting recent comments ny BO user ////
                    $commentDetails=$ftvcomments_obj->getRecentCommentsByBoUser($requestsdetail[$key]['identifier']);
                    if($commentDetails != 'NO')
                        $requestsdetail[$key]['comments'] = $commentDetails[0]['comments'];
                    else
                        $requestsdetail[$key]['comments'] = "NILL";
                    ////gettting recent document BO user ////
                    $documentDetails=$ftvdocuments_obj->getRecentDocument($requestsdetail[$key]['identifier']);
                    if($documentDetails != 'NO')
                        $requestsdetail[$key]['recent_document'] = $documentDetails[0]['document'];
                    else
                        $requestsdetail[$key]['recent_document'] = "NILL";
                    //////color differentiation//////
                    $requestsdetail[$key]['created_at'];
                    $t=date('d-m-Y H:i', strtotime($requestsdetail[$key]['created_at']));
                    $dayandtime = explode("-", date("N-G-i",strtotime($t)));//echo $requestsdetail[$key]['request_object'];print_r($dayandtime);
                    $day = $dayandtime[0];
                    $hour = $dayandtime[1];
                    $minute = $dayandtime[2];
                    $reddays = array(1,2,3,4);  ///monday to thursday
                    $bluedays = array(5,6,7); //friday to sunday
                    if($hour >= 19 || $hour < 9)
                    {
                        if(in_array($day, $reddays))
                            $requestsdetail[$key]['dayrange']="red";
                        else
                            $requestsdetail[$key]['dayrange']="green";
                    }
                    $ptimes  = $ftvpausetime_obj->getPauseDuration($requestsdetail[$key]['identifier']);
                    $assigntime = $requestsdetail[$key]['assigned_at'];
                    if(($requestsdetail[$key]['status'] == 'done' || $inpause == 'yes') && ($requestsdetail[$key]['assigned_at'] != null && $requestsdetail[$key]['assigned_to'] != null))
                    {
                        if($requestsdetail[$key]['assigned_at'] != NULL)
                        {
                            if ($requestsdetail[$key]['status'] == "closed")
                                $time1 = ($requestsdetail[$key]['cancelled_at']); /// created time
                            elseif ($requestsdetail[$key]['status'] == "done")
                                $time1 = ($requestsdetail[$key]['closed_at']); /// created time
                            else{
                                if($inpause == 'yes') {
                                    $time1 = ($requestsdetail[$key]['pause_at']);
                                }else {
                                    $time1 = (date('Y-m-d H:i:s'));///curent time
                                }
                            }
                            // $totaltime2 = strtotime($requestsdetail[$key]['assigned_at']);
                            $pausedrequests  = $ftvpausetime_obj->pausedRequest($requestsdetail[$key]['identifier']);
                            if($pausedrequests == 'yes')
                            {
                                $time2 = $this->subDiffFromDate($requestsdetail[$key]['identifier'], $requestsdetail[$key]['assigned_at']);
                            }else{
                                $time2 = $requestsdetail[$key]['assigned_at'];
                            }
                            $totaldiff = strtotime($time1) - strtotime($time2);
                            $timestamp = new DateTime($time1);
                            $diff = $timestamp->diff(new DateTime($time2));
                            $days = $diff->format('%d');
                            $hours = $diff->format('%h');
                            $minutes = $diff->format('%i');
                            $seconds = $diff->format('%s');
                            $gtdiff+=$totaldiff;
                            $difference = '';
                            if($days != '')
                                $difference.= "<span class='label label-info' >".$days."J </span> ";
                            if($hours != '')
                                $difference.= "<span class='label label-info' >".$hours."H </span> ";
                            if($minutes != '')
                                $difference.= "<span class='label label-info' >".$minutes."M </span> ";
                            if($seconds != '')
                                $difference.= "<span class='label label-info' >".$seconds."S </span> ";
                            $requestsdetail[$key]['time_spent'] = $difference;
                        }
                        else
                            $requestsdetail[$key]['time_spent'] = "-NA-";
                    }
                    else
                        $requestsdetail[$key]['time_spent'] = "-NA-";
                    $pausedrequests  = $ftvpausetime_obj->pausedRequest($requestsdetail[$key]['identifier']);
                    if($pausedrequests == 'yes')
                    {
                        $requestsdetail[$key]['assigned_at'] = $this->subDiffFromDate($requestsdetail[$key]['identifier'], $requestsdetail[$key]['assigned_at']);
                    }else{
                        $requestsdetail[$key]['assigned_at'] = $requestsdetail[$key]['assigned_at'];
                    }
                    ////subtracting 30 sec because adding 30 secs added by javascipt ///
                    $minus30sec=new DateTime($requestsdetail[$key]['assigned_at']);
                    $minus30sec->add(new DateInterval("PT35S"));
                    $requestsdetail[$key]['assigned_at'] = $minus30sec->format('Y-m-d H:i:s');
                }
                // print_r($ptimes);
                $d = floor($gtdiff/86400);
                $gtdays = ($d < 10 ? '0' : '').$d;

                $h = floor(($gtdiff-$d*86400)/3600);
                $gthours = ($h < 10 ? '0' : '').$h;

                $m = floor(($gtdiff-($d*86400+$h*3600))/60);
                $gtminutes = ($m < 10 ? '0' : '').$m;
                $gtdifference = '';
                if($gtdays != '')
                    $gtdifference.= "".$gtdays."J ";
                if($gthours != '')
                    $gtdifference.= "".$gthours."H  ";
                if($gtminutes != '')
                    $gtdifference.= "".$gtminutes."M ";
                $this->_view->globaltime = $gtdifference;
            }
            $rResultcount = count($requestsdetail);
            /////total count
            $sLimit = "";
            $countaos  = $ftvrequest_obj->getAllEditoRequestsDetails($sWhere, $sOrder, $sLimit, $condition);
           // print_r($countaos);exit;
            $iTotal = count($countaos);
            $output = array(
                "sEcho" => intval($_GET['sEcho']),
                "iTotalRecords" => $iTotal,
                "iTotalDisplayRecords" => $iTotal,
                "aaData" => array()
            );
            $assguser_arr = array(141027195819796=>'Fanny',141027200051263=>'Djawed',130122131303109=>'Jgimenez',111017113015710=>'editor1',140655136475464=>'prashanth',110823103540627=>'Farid');
            $statrow_arr = array('pending'=>'En attente','done'=>'Trait&eacute;e','closed'=>'Annul&eacute;e');
            $count = 1;
            if($broadcast_sort_flag && $broadcast_sort === 'asc')//only if falg is set to true call this sorting function (only for broadcast array)
                usort($requestsdetail, $this->build_sorter_asc($to_sort));
            else if($broadcast_sort_flag && $broadcast_sort === 'desc')
                usort($requestsdetail, $this->build_sorter_desc($to_sort));
            if($requestsdetail != 'NO')
            {
                for( $i=0 ; $i<$rResultcount; $i++)
                {
                    $row = array();
                    for ( $j=0 ; $j<count($aColumns) ; $j++ )
                    {
                        //if($j == 0)
                        //    $row[] = $count;
                        //else
                        {
                           if($aColumns[$j] == 'first_name')
                                $row[] =  $requestsdetail[$i]['first_name'];
                            elseif($aColumns[$j] == 'created_at')
                                $row[] = date("Y-m-d", strtotime($requestsdetail[$i]['created_at']));
                            elseif($aColumns[$j] == 'request_object')
                                $row[] = '<a data-target="#chaineeditrequest" data-refresh="true"  data-toggle="modal" href="/ftvchaine/duplicaterequest?request_id='.$requestsdetail[$i]["identifier"].'&edit=yes" class="hint--bottom hint--info" data-placement="right" data-original-title="Object" data-html="true"
                                        data-hint="'.utf8_encode($requestsdetail[$i]['request_object']).'">'.utf8_encode($requestsdetail[$i]['request_object']).'</a>';
                            elseif($aColumns[$j] == 'download'){
                                $download = '<a class="hint--bottom hint--info"  data-target="#fileupload" data-refresh="true"  data-toggle="modal" data-hint="upload file"  role="button"  href="/ftvchaine/uploadfiles?request_id='.$requestsdetail[$i]["identifier"].'&view=yes">
                                            <i class="splashy-document_small_upload"></i></a>';
                                $checkRecentDocument = $ftvdocuments_obj->checkRecentDocument($requestsdetail[$i]["identifier"]);
                                $serverpath = '/home/sites/site5/web/FO/ftv_documents/';
                                if($requestsdetail[$i]["recent_document"] != '' && file_exists($serverpath.$checkRecentDocument[0]['document']))
                                $download .= '<a class="hint--bottom hint--info"  data-hint="download latest file"  role="button"  href="/ftvchaine/downloadftv?request_id='.$requestsdetail[$i]["identifier"].'&filename='.$requestsdetail[$i]["recent_document"].'">
                                            <i class="splashy-document_small_download"></i></a>';
                                $row[] = $download;
                            }

                            elseif($aColumns[$j] == 'duration')
                                $row[] = $requestsdetail[$i]['duration'];
                            elseif($aColumns[$j] == 'modify_contains')
                                $row[] = $requestsdetail[$i]['modify_contains'];
                            elseif($aColumns[$j] == 'modify_broadcast')
                                $row[] = $requestsdetail[$i]['modify_broadcast'];
                            elseif($aColumns[$j] == 'demand')
                                $row[] = $requestsdetail[$i]['demand'];
                            elseif($aColumns[$j] == 'assingnation') {
                                $assrow = '<select class="bouserasgn span12" data-placeholder="Assignation" name="bouserasgn_' . $requestsdetail[$i]['identifier'] . '" id="bouserasgn_' . $requestsdetail[$i]['identifier'] . '" onchange="assignUser(' . $requestsdetail[$i]['identifier'] . ');">
                                              <option value="" >Assign </option>';
                                foreach ($assguser_arr as $ky => $vl) {
                                    if ($requestsdetail[$i]["assigned_to"] == $ky)
                                        $assrow .= '<option value="'.$ky.'" selected>'.$vl.'</option>';
                                    else
                                        $assrow .= '<option value="'.$ky.'" >'.$vl.'</option>';
                                }
                                $assrow .= '</select>';
                                $row[] = $assrow;
                            }
                            elseif($aColumns[$j] == 'status') {
                                $statrow = '<select class="status" style="width: 130px;" data-placeholder="status" name="status_{$requestsitem.identifier}" id="status_' . $requestsdetail[$i]['identifier'] . '" onchange="changeStatus(' . $requestsdetail[$i]['identifier'] . ');">
                                              <option value="0" >select</option>';
                                foreach ($statrow_arr as $sky => $svl) {
                                    if ($requestsdetail[$i]["status"] == $sky)
                                        $statrow .= '<option value="'.$sky.'" selected >'.$svl.'</option>';
                                    else
                                        $statrow .= '<option value="'.$sky.'" >'.$svl.'</option>';
                                }
                                $statrow .= '</select>';
                                $row[] = $statrow;
                            }
                            elseif($aColumns[$j] == 'comments') {
                                $row[] = '<a  class="hint--bottom hint--info" data-hint="Add Comment" data-target="#addcomment" data-refresh="true"  data-toggle="modal"  role="button"  href="/ftvchaine/showcomments?request_id=' . $requestsdetail[$i]['identifier'] . '">
                                              <i class="splashy-comments_reply"></i></a>';
                                if ($this->loginuserId == '110823103540627')
                                    $row[] .= '<a  type="button" class="hint--bottom hint--info" data-hint="Delete request" id="deleterequest" name="deleterequest" onClick="deleteRequest('.$requestsdetail[$i]['identifier'].');"><i class="splashy-calendar_week_remove"></i></a>';
                            }elseif($aColumns[$j] == 'time_spent')
                            {
                                $rowdetail = '<input type="hidden" id="time' . $requestsdetail[$i]['identifier'] . '" value="' . $requestsdetail[$i]['assigned_to'] . '"/>';
                                if ($requestsdetail[$i]['assigned_to'] != '' && $requestsdetail[$i]['status'] != 'closed') {
                                    if ($requestsdetail[$i]['status'] == 'pending' && $requestsdetail[$i]['inpause'] == 'no') {
                                        $rowdetail.= '<div class="timer" id="time_'.$requestsdetail[$i]["identifier"].'_'.$requestsdetail[$i]["assigned_at"].'" onClick="extendTime('.$requestsdetail[$i]["identifier"].')">
                                          <span class="label label-info" id="days_'.$requestsdetail[$i]['identifier'].'"></span>
                                          <span class="label label-info" id="hours_'.$requestsdetail[$i]['identifier'].'"></span>
                                          <span class="label label-info" id="minutes_'.$requestsdetail[$i]['identifier'].'"></span>
                                          <span class="label label-info" id="seconds_'.$requestsdetail[$i]['identifier'].'"></span>
                                        </div>
                                        <button class="btn btn-warning" type="button" id="pausetime' . $requestsdetail[$i]['identifier'] . '" name="pausetime"  onClick="pauseTime(\'pause\',' . $requestsdetail[$i]['identifier'] . ');" >Pause</button>';
                                    } elseif ($requestsdetail[$i]['status'] == 'pending' && $requestsdetail[$i]['inpause'] == 'yes') {
                                        $rowdetail.= '"' . $requestsdetail[$i]['time_spent'] . '
                                        <button class="btn btn-warning"  type="button" id="resumetime' . $requestsdetail[$i]['identifier'] . '" name="resumetime" onClick="pauseTime(\'resume\',' . $requestsdetail[$i]['identifier'] . ');" >Resume</button>';
                                    } else
                                        $rowdetail.= '"' . $requestsdetail[$i]['time_spent'] . '"';
                                    if ($requestsdetail[$i]['status'] != 'closed') {
                                        $rowdetail.= '<a class="hint--bottom hint--waring"  data-hint="edit time" data-toggle="modal" data-target="#editassigntime" href="/ftvchaine/editassigntime?requestId=' . $requestsdetail[$i]['identifier'] . '"><i class="icon-time"></i> </a>';
                                    }
                                } else
                                    $rowdetail .= '-NA-';
                                $row[] = $rowdetail;
                            }
                            else
                                $row[] = $requestsdetail[$i][ $aColumns[$j] ];
                        }
                    }
                    $output['aaData'][] = $row;
                    $count++;
                }
            }
            //print_r($output);exit;
            echo json_encode( $output );
        }
    }
    function build_sorter_asc($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }
    function build_sorter_desc($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp( $b[$key],$a[$key]);
        };
    }
    // not in use /
    public function ftvchRequestsoldAction()
    {
       // $broadcast_array=$this->_arrayDb->loadArrayv2("EP_BROADCASTS", $this->_lang);
        $ftvcontacts_obj = new Ep_Ftv_FtvContacts();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvcomments_obj = new Ep_Ftv_FtvComments();
        $ftvdocuments_obj = new Ep_Ftv_FtvDocuments();
        $ftvpausetime_obj = new Ep_Ftv_FtvPauseTime();
        $loginuser = $this->adminLogin->userId;
        $contacts = $ftvcontacts_obj->getRequestCreatedContacts(); // print_r($contacts); exit;
        $this->_view->contacts_array = $contacts;
        if($loginuser != "")
        {
            $ftv_params=$this->_request->getParams();
            if($ftv_params['search'] == 'search')
            { //echo "mash"; exit;
                $condition['search'] = $ftv_params['search'];
                $condition['contactId'] = $ftv_params['contactId'];
                $condition['containsId'] = $ftv_params['containsId'];
                $condition['broadcastId'] = $ftv_params['broadcastId'];
                $condition['quandId'] = $ftv_params['quandId'];
                $condition['startdate'] = $ftv_params['startdate'];
                $condition['enddate'] = $ftv_params['enddate'];
                $condition['dayrange'] = $ftv_params['dayrange'];
            }
            $requestsdetail=$ftvrequest_obj->getAllRequestsDetails($condition, 'chaine');
            //contains to modify
            $contains_array = array(1=>"Unes tournantes", 2=>"Voir et Revoir", 3=>"Les &eacute;missions", 4=>"A d&eacute;couvrir",5=>"Les jeux",
                6=>"Une", 7=>"Top 3", 8=>"Forums", 9=>"PAGE VIDEOS", 10=>"PAGES DOCUMENTAIRES", 11=>"PAGES FRANCE 5 & VOUS", 12=>"PAGES INFOS");
            $duraiton_array = array("h"=>"Dans l'heure", "d"=>"Dans la journ&eacute;e","nd"=>"Le lendemain", "w"=>"Dans la semaine","nw"=>"La semaine prochaine");
            $this->_view->contains_array = $contains_array;
            $this->_view->quands_array = $duraiton_array;
            $broadcast_array = array("1"=>'France 2', "2"=>'France 3', "3"=>'France 4',"4"=>'France 5',"5"=>'France &Ocirc;' ,"6"=>'France TV');
            $demand_array = array("1"=>'Int&eacute;gration', "2"=>'Modification demand&eacute;e par FTV', "3"=>'Correction erreur EP', "4"=>'Retours');
            $this->_view->broadcast_array = $broadcast_array;
            $this->_view->demand_array = $demand_array;
          // print_r($requestsdetail); exit;
            if($requestsdetail != 'NO')
            {
                $gtdays = '';$gthours = ''; $gtminutes = '';$gtdiff='';
                foreach ($requestsdetail as $key => $value)
                {
                    $inpause  = $ftvpausetime_obj->inPause($requestsdetail[$key]['identifier']);
                    $requestsdetail[$key]['inpause'] = $inpause;
                    $durationvalue = $duraiton_array[$requestsdetail[$key]['duration']];
                    $requestsdetail[$key]['duration']   = $durationvalue;
                    $requestsdetail[$key]['demand'] = $demand_array[$requestsdetail[$key]['demand']];
                    /////getting modify contains display format///
                    $contains  = explode(",",$requestsdetail[$key]['modify_contains']);
                    $finalcontains = array();
                    foreach($contains as $code1 => $abb1)
                    {
                        $finalcontains[$code1] = $contains_array[$abb1];
                    }
                    $containvalue = implode(" / ",$finalcontains);
                    $requestsdetail[$key]['modify_contains']   = $containvalue;
                    ////getting modify broadcast display format//
                    $braodcast  = explode(",",$requestsdetail[$key]['modify_broadcast']);
                    $finalbroadcast = array();
                    foreach($braodcast as $broadkey => $broadval)
                    {
                        $finalbroadcast[$broadkey] = $broadcast_array[$broadval];
                    }
                    $broadvalue = implode(" / ",$finalbroadcast);
                    $requestsdetail[$key]['modify_broadcast']   = $broadvalue;
                    ////gettting recent comments ny BO user ////
                    $commentDetails=$ftvcomments_obj->getRecentCommentsByBoUser($requestsdetail[$key]['identifier']);
                    if($commentDetails != 'NO')
                        $requestsdetail[$key]['comments'] = $commentDetails[0]['comments'];
                    else
                        $requestsdetail[$key]['comments'] = "NILL";
                    ////gettting recent document BO user ////
                    $documentDetails=$ftvdocuments_obj->getRecentDocument($requestsdetail[$key]['identifier']);
                    if($documentDetails != 'NO')
                        $requestsdetail[$key]['recent_document'] = $documentDetails[0]['document'];
                    else
                        $requestsdetail[$key]['recent_document'] = "NILL";
                    //////color differentiation//////
                     $requestsdetail[$key]['created_at'];
                     $t=date('d-m-Y H:i', strtotime($requestsdetail[$key]['created_at']));
                    $dayandtime = explode("-", date("N-G-i",strtotime($t)));//echo $requestsdetail[$key]['request_object'];print_r($dayandtime);
                     $day = $dayandtime[0];
                    $hour = $dayandtime[1];
                    $minute = $dayandtime[2];
                    $reddays = array(1,2,3,4);  ///monday to thursday
                    $bluedays = array(5,6,7); //friday to sunday

                    if($hour >= 19 || $hour < 9)
                    {
                        if(in_array($day, $reddays))
                            $requestsdetail[$key]['dayrange']="red";
                        else
                            $requestsdetail[$key]['dayrange']="green";
                    }
                    $ptimes  = $ftvpausetime_obj->getPauseDuration($requestsdetail[$key]['identifier']);
                    $assigntime = $requestsdetail[$key]['assigned_at'];

                    if(($requestsdetail[$key]['status'] == 'done' || $inpause == 'yes') && ($requestsdetail[$key]['assigned_at'] != null && $requestsdetail[$key]['assigned_to'] != null))
                    {
                        if($requestsdetail[$key]['assigned_at'] != NULL)
                        {
                            if($requestsdetail[$key]['status'] == "closed")
                                $time1 = ($requestsdetail[$key]['cancelled_at']); /// created time
                            elseif ($requestsdetail[$key]['status'] == "done")
                                $time1 = ($requestsdetail[$key]['closed_at']); /// created time
                            else{
                                if($inpause == 'yes') {
                                    $time1 = ($requestsdetail[$key]['pause_at']);
                                }else {
                                    $time1 = (date('Y-m-d H:i:s'));///curent time
                                }
                            }

                           // $totaltime2 = strtotime($requestsdetail[$key]['assigned_at']);
                            $pausedrequests  = $ftvpausetime_obj->pausedRequest($requestsdetail[$key]['identifier']);
                            if($pausedrequests == 'yes')
                            {
                                $time2 = $this->subDiffFromDate($requestsdetail[$key]['identifier'], $requestsdetail[$key]['assigned_at']);
                            }else{
                                $time2 = $requestsdetail[$key]['assigned_at'];
                            }
                            $totaldiff = strtotime($time1) - strtotime($time2);
                            $timestamp = new DateTime($time1);
                            $diff = $timestamp->diff(new DateTime($time2));
                            $days = $diff->format('%d');
                            $hours = $diff->format('%h');
                            $minutes = $diff->format('%i');
                            $seconds = $diff->format('%s');
                            $gtdiff+=$totaldiff;
                            $difference = '';
                            if ($days != '')
                                $difference .= "<span class='label label-info' >" . $days . "J </span> ";
                            if ($hours != '')
                                $difference .= "<span class='label label-info' >" . $hours . "H </span> ";
                            if ($minutes != '')
                                $difference .= "<span class='label label-info' >" . $minutes . "M </span> ";
                            if($seconds != '')
                                $difference.= "<span class='label label-info' >".$seconds."S </span> ";

                            $requestsdetail[$key]['time_spent'] = $difference;
                        }
                        else
                            $requestsdetail[$key]['time_spent'] = "-NA-";
                    }
                    else
                        $requestsdetail[$key]['time_spent'] = "-NA-";
                    $pausedrequests  = $ftvpausetime_obj->pausedRequest($requestsdetail[$key]['identifier']);
                    if($pausedrequests == 'yes')
                    {
                        $requestsdetail[$key]['assigned_at'] = $this->subDiffFromDate($requestsdetail[$key]['identifier'], $requestsdetail[$key]['assigned_at']);

                    }else{
                        $requestsdetail[$key]['assigned_at'] = $requestsdetail[$key]['assigned_at'];
                    }
                    ////subtracting 30 sec because adding 30 secs added by javascipt ///
                        $minus30sec=new DateTime($requestsdetail[$key]['assigned_at']);
                        $minus30sec->add(new DateInterval("PT35S"));
                        $requestsdetail[$key]['assigned_at'] = $minus30sec->format('Y-m-d H:i:s');
                }
               // print_r($ptimes);
                $d = floor($gtdiff/86400);
                $gtdays = ($d < 10 ? '0' : '').$d;

                $h = floor(($gtdiff-$d*86400)/3600);
                $gthours = ($h < 10 ? '0' : '').$h;

                $m = floor(($gtdiff-($d*86400+$h*3600))/60);
                $gtminutes = ($m < 10 ? '0' : '').$m;

                $gtdifference = '';
                if($gtdays != '')
                    $gtdifference.= "".$gtdays."J ";
                if($gthours != '')
                    $gtdifference.= "".$gthours."H  ";
                if($gtminutes != '')
                    $gtdifference.= "".$gtminutes."M ";
                $this->_view->globaltime = $gtdifference;
               // $this->_view->requestsdetail=$requestsdetail;
                $this->_view->paginator   = $requestsdetail;
            } else
                $this->_view->nores = "true";
            $this->render("ftvchaine_ftvchrequests");
        }
    }
    //// pause time for the request///
    public function pausetimeAction()
    {
        $user_params=$this->_request->getParams();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvcontacts_obj = new Ep_Ftv_FtvContacts();
        $ftvtime_obj = new Ep_Ftv_FtvPauseTime();
        if($user_params['type'] == 'pause')
        {
            ////for goroup Id in users table////
            $ftvtime_obj->ftvrequest_id=$user_params['requestId'];
            $ftvtime_obj->pause_at=date('Y-m-d H:i:s') ;
            $ftvtime_obj->resume_at=null;
            $ftvtime_obj->insert();
        }
        else
        {
            $data = array("resume_at"=>date('Y-m-d H:i:s'));////////updating
            $query = "ftvrequest_id= '".$user_params['requestId']."' AND resume_at is null";
            $ftvtime_obj->updateFtvPauseTime($data,$query);
        }
    }
    ///substract the diff between 2 dates from another date///
    public function subDiffFromDate($requestId, $assignTime)
    {
        $ftvpausetime_obj = new Ep_Ftv_FtvPauseTime();
        $pausetimes = $ftvpausetime_obj->getAllPauseDuration($requestId);
        if($pausetimes != 'NO'){
            for($i=0; $i<count($pausetimes); $i++){
                $first_time[$i]=new DateTime($pausetimes[$i]['pause_at']);
                $second_time[$i]=new DateTime($pausetimes[$i]['resume_at']);
                $diff=$first_time[$i]->diff($second_time[$i]);
                $yrs[$i] = $diff->format('%Y');
                $moths[$i] = $diff->format('%m');
                $dayss[$i] = $diff->format('%d');
                $hrs[$i] = $diff->format('%h');
                $mins[$i] = $diff->format('%i');
                $sec[$i] = $diff->format('%s');
            }
            $yrs1 = array_sum($yrs);
            $moths1  = array_sum($moths);
            $dayss1 = array_sum($dayss);
            $hrs1 = array_sum($hrs);
            $mins1 = array_sum($mins);
            $sec1 = array_sum($sec);
            $format = "P".$yrs1."Y".$moths1."M".$dayss1."DT".$hrs1."H".$mins1."M".$sec1."S";
            $time=new DateTime($assignTime);
            $time->add(new DateInterval($format));
            return $assigntime = $time->format('Y-m-d H:i:s');
        }else
            return $assigntime = $assignTime;
    }
    // comments by the client and popup /
    public function showcommentsAction()
    {
        $comment_params=$this->_request->getParams();
        $ftvcontacts_obj = new Ep_Ftv_FtvContacts();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvcomments_obj = new Ep_Ftv_FtvComments();
        //$this->render("ftv_addcomment"); exit;
        $request_id = $comment_params['request_id'];
        $this->_view->request_id=$request_id;
        $commentDetails=$ftvcomments_obj->getCommentsByRequests($request_id);

       /*$this->_view->commentDetails=$commentDetails;
       $commentsData='';
       if($commentDetails != 'NO')
       {
           $commentsData='';
           $cnt=0;
           foreach($commentDetails as $comment)
           {
               $commentsData.=
                   '<li class="media" id="comment_'.$comment['identifier'].'">';
               $commentsData.='<div class="media-body">
                       <h4 class="media-heading">
                         <a href="#" role="button" data-toggle="modal" data-target="#viewProfile-ajax">'.utf8_encode($comment['first_name']).'</a></h4>
                         '.utf8_encode(stripslashes($comment['comments'])).'
                       <p class="muted">'.$comment['created_at'].'</p>
                     </div>
                   </li>';
           }
       }*/
        $this->_view->commentDetails = $commentDetails;

        $this->render("ftv_ftvaddcomment");

    }
    //save Comments Action
    public function savecommentsAction()
    {
        $comment_params=$this->_request->getParams();
        $ftvcontacts_obj = new Ep_Ftv_FtvContacts();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvcomments_obj = new Ep_Ftv_FtvComments();
        $automail=new Ep_Message_AutoEmails();
        if($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
            $request_id=$comment_params['request_id'];
            $comments=$this->utf8dec($comment_params['comments']);
            if($request_id != '' && $comments != '')
            {
                $ftvcomments_obj->request_id=$request_id;
                $ftvcomments_obj->comment_by=$this->adminLogin->userId;
                $ftvcomments_obj->comments=$comments;
                $ftvcomments_obj->user_type="BO";
                $ftvcomments_obj->insert();
                ////send mails to all other contacts and actual contact as its commented by BO user//
                $requestdetails =  $ftvrequest_obj->requestDetailsById($request_id);
                $parameters['ftvobject'] = $requestdetails[0]['request_object'];
                $parameters['ftvcomments'] = $comments;
                ////sending mail to contact who created demand/////
                $requestowner = $ftvcontacts_obj->getFtvContactDetails($requestdetails[0]['request_by']);
                $parameters['ftvcontactName'] = $requestowner[0]['first_name']." ".$requestowner[0]['last_name'];
                $automail->sendFtvContactsPersonalEmail($requestdetails[0]['request_by'],115,$parameters); // to client contact
                ///sending mails to other contact also/////
                $othercontacts = explode(",",$requestdetails[0]['other_contacts']);
                foreach($othercontacts as $values)
                {
                    $contactdetails = $ftvcontacts_obj->getFtvContactDetails($values);
                    $parameters['ftvcontactName'] = $contactdetails[0]['first_name']." ".$contactdetails[0]['last_name'];
                    $automail->sendFtvContactsPersonalEmail($values,115,$parameters); // to client contact
                }
            }
            /*$commentDetails=$ftvcomments_obj->getCommentsByRequests($request_id);
            $commentsData='';
            $cmtCount=count($commentDetails);
            if($cmtCount>0)
            {
                //$commentDetails=$this->formatCommentDetails($commentDetails);
                $commentsData='';
                $cnt=0;
                foreach($commentDetails as $comment)
                {
                    echo  $commentsData.=
                        '<li class="media" id="comment_'.$comment['identifier'].'">';
                    $commentsData.='<div class="media-body">
                        <h4 class="media-heading">
                          <a href="#" role="button" data-toggle="modal" data-target="#viewProfile-ajax">'.utf8_encode($comment['first_name']).'</a></h4>
                          '.utf8_encode(stripslashes($comment['comments'])).'
                        <p class="muted">'.$comment['created_at'].'</p>
                      </div>
                    </li>';
                }
            }*/

        }
    }
    // to download file for ftv///
    public function downloadftvAction()
    {
        $user_params=$this->_request->getParams();
        $request_id = $user_params['request_id'];
        $zipname = explode("/",$user_params['filename']);
        // set example variables
         $filename = $zipname[1];
         $filepath = $request_id."-".$filename;

        $this->_redirect("/BO/download_ftv.php?ftvfile=".$filepath."");


    }
    // to assign the request to BO user request for correciton///
    public function assignuserAction()
    {
        $user_params=$this->_request->getParams();
        $automail=new Ep_Message_AutoEmails();
        $ftvpausetime_obj = new Ep_Ftv_FtvPauseTime();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $data = array("assigned_to"=>$user_params['userId'], "assigned_at"=>date('Y-m-d H:i:s'), "status"=>"pending", "closed_at"=>NULL, "cancelled_at"=>NULL);////////updating
        $query = "identifier= '".$user_params['requestId']."'";
        $ftvrequest_obj->updateFtvRequests($data,$query);
        $deletequery = "ftvrequest_id= '".$user_params['requestId']."'";
        $ftvpausetime_obj->deleteFtvPauseTime($deletequery);
        $parameters['ftvType'] = "edito";
        if($user_params['userId'] != '110823103540627')  ///supposed to be johny head of BO user for FTV
            $automail->messageToEPMail($user_params['userId'],113,$parameters);//
    }
    //// changing the status according the process of requests///
    public function changestatusAction()
    {
        $user_params=$this->_request->getParams();     //print_r($user_params);exit;
        $automail=new Ep_Message_AutoEmails();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvcontacts_obj = new Ep_Ftv_FtvContacts();
        $ftvpausetime_obj = new Ep_Ftv_FtvPauseTime();
        if($user_params['status'] == 'closed')
            $data = array("status"=>$user_params['status'], "cancelled_at"=>date('Y-m-d H:i:s'));////////updating
        elseif($user_params['status'] == 'done')
            $data = array("status"=>$user_params['status'], "closed_at"=>date('Y-m-d H:i:s'));////////updating
        else
            $data = array("status"=>$user_params['status'], "closed_at"=>NULL, "cancelled_at"=>NULL);////////updating
        $query = "identifier= '".$user_params['requestId']."'";
        $requestdetails =  $ftvrequest_obj->requestDetailsById($user_params['requestId']);
        $contactId =  $requestdetails[0]['request_by'];
        $contactdetails = $ftvcontacts_obj->getFtvContactDetails($contactId);
        $contactName = $contactdetails[0]['first_name']." ".$contactdetails[0]['last_name'];
        $parameters['ftvobject'] = $requestdetails[0]['request_object'];
        $parameters['ftvcontactName'] = $contactName;
        $ftvrequest_obj->updateFtvRequests($data,$query);
        ////making the time resume if its in pause////
        $inpause  = $ftvpausetime_obj->inPause($user_params['requestId']);
        if($inpause == 'yes')
        {
            $data = array("resume_at"=>date('Y-m-d H:i:s'));////////updating
            $query = "ftvrequest_id = '".$user_params['requestId']."' AND resume_at IS NULL ";
            $ftvpausetime_obj->updateFtvPauseTime($data,$query);
        }
        if($user_params['status'] == 'done')
        {
            if($this->adminLogin->userId != '110823103540627' )  ///when not johny head of BO user for FTV changed
            {
                $parameters['ftvrequestlink'] = "/ftv/ftv-requests?submenuId=ML11-SL3";
                $parameters['ftvType'] = "edito";
                $automail->messageToEPMail('110823103540627',114,$parameters);// to johny
                $parameters['ftvrequestlink'] = "http://ep-test.edit-place.com/ftvedito/index";
                $automail->sendFtvContactsPersonalEmail($contactId,114,$parameters); // to client contact
            }
            else   // in case jhony change the status
            {
                $parameters['ftvrequestlink'] = "http://ep-test.edit-place.com/ftvedito/index";
                $automail->sendFtvContactsPersonalEmail($contactId,114,$parameters); // to client contact
            }
        }

    }
    //// delete the requests///
    public function deleterequestAction()
    {
        $user_params=$this->_request->getParams();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvcontacts_obj = new Ep_Ftv_FtvContacts();
        $data = array("active"=>"no");////////updating
        $query = "identifier= '".$user_params['requestId']."'";
        $ftvrequest_obj->updateFtvRequests($data,$query);

    }
    //upload documents////
    public function uploadfilesAction()
    {
        $doc_params=$this->_request->getParams(); //
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvdocuments_obj = new Ep_Ftv_FtvDocuments();
        $attachment=new Ep_Message_Attachment();

        $serverpath = '/home/sites/site5/web/FO/ftv_documents/';
        if($doc_params["view"] == 'yes')
        {
            $this->_view->request_id = $doc_params["request_id"];
            $this->_view->ftvtype = 'ftv';
            $previousfiles = $ftvdocuments_obj->getDocumentsByRequests($doc_params["request_id"]);
            $this->_view->previousfiles = $previousfiles;
            $this->render("ftv_ftvfileupload");
        }
        if($this->_request->isPost())
        {  // print_r($doc_params); print_r($_FILES); echo "helo";exit;
            $request_id=$doc_params['request_id'];
            if($_FILES['attachment']['name'][0]!=NULL)
            {
                $file_attachemnts='';
                $cnt=1;
                foreach($_FILES['attachment']['name'] as $file)
                {
                    $file_attachemnt[$cnt-1]=rand(10000, 99999)."_".utf8dec($file);
                    $file_attachemnts.= rand(10000, 99999)."_".utf8dec($file)."|";
                    $cnt++;
                }
                $file_attachemnts=substr($file_attachemnts,0,-1);
                $file_attachemnts = explode("|",$file_attachemnts);
                $fileCount=0;
                foreach($_FILES['attachment']['tmp_name'] as $file)
                {
                    $attachFile['tmp_name']=$file;
                    $attachment->uploadAttachment($serverpath,$attachFile,$file_attachemnts[$fileCount]);
                    $fileCount++;
                }
            }
                $files = $file_attachemnts;
                foreach ($files as $k=>$file) {
                    $zip_array[]= $serverpath.$file;
                }
                $zipname =   "file_".uniqid().".zip";
                $fullpath =  $serverpath.$request_id."/".$zipname;
                $ftvDir = $serverpath.$request_id;
                if (!is_dir($ftvDir))
                {
                    mkdir($ftvDir, 0777);
                }
                create_zip($zip_array, $ftvDir."/".$zipname);
                foreach ($files as $k=>$file) {
                    unlink($serverpath.$file);
                }
               /* $zipfile = $ftvDir."/".$zipname;
                $zip = new ZipArchive;
                if ($zip->open($zipfile, ZIPARCHIVE::CREATE) != TRUE) {
                    die ("Could not open archive");
                }
                foreach ($files as $k=>$file) {
                    $zip->addFile($serverpath.$file);
                    unlink($serverpath.$file);
                }
                $zip->close();*/
                $ftvdocuments_obj->request_id=$request_id;
                $ftvdocuments_obj->document_by=$this->adminLogin->userId;
                $ftvdocuments_obj->document=$request_id."/".$zipname;
				$ftvdocuments_obj->insert();
            //$this->_redirect("/ftv/ftv-requests?submenuId=ML11-SL3");
                $previousfiles = $ftvdocuments_obj->getDocumentsByRequests($request_id);
               // echo "<pre>";print_r($previousfiles);
              $this->_redirect("/ftv/ftv-requests?submenuId=ML11-SL3");
                $oldFiles=json_encode(array('result'=>$previousfiles));
               // echo $oldFiles;
        }
    }
    /* *UTF8 DECODE function work for msword character also**/
    public function utf8dec($s_String)
    {
        $s_String=str_replace("e&#769;","&eacute;",$s_String);
        $s_String=str_replace("E&#769;","&Eacute;",$s_String);
        $s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
        return substr($s_String, 0, strlen($s_String)-1);
    }
    public function ftvContactCreationAction()
    {
        $ftv_obj=new Ep_Ftv_FtvContacts();
        $user_params=$this->_request->getParams();
        $contactid = $user_params['contactId'];
        if(isset($contactid) && $contactid != '')
        {
            $contactdetails = $ftv_obj->getFtvContactDetails($contactid);
            $this->_view->contactdetails =  $contactdetails;
            if($user_params["edit"] == 'yes')
            {
                $emailexit = $ftv_obj->getExistingEmailUpdate($contactid, $user_params["email"]);
                $emailexit=trim($emailexit);
                if($emailexit == 'yes')
                {
                    $this->_helper->FlashMessenger('Email id is already exit.');
                    $this->_redirect("/ftv/ftv-contacts?submenuId=ML11-SL1");
                }
                else
                {
                    $accesto = $user_params["accessto"];
                    if(count($accesto) == 2)
                        $ftvtype = "both";
                    elseif(count($accesto) == 1 && $accesto[0] == 'edito')
                        $ftvtype = "edito";
                    elseif(count($accesto) == 1 && $accesto[0] == 'chaine')
                        $ftvtype = "chaine";


                    $data = array( "email_id"=>$user_params["email"], "password"=>$user_params["password"], "first_name"=>$user_params["first_name"], "last_name"=>$user_params["last_name"], "ftvtype"=>$ftvtype);
                    $query = "identifier= '".$contactid."'";
                    $ftv_obj->updateFtvContacts($data,$query);
                    $this->_helper->FlashMessenger('Contact Edited Successfully.');
                    $this->_redirect("/ftv/ftv-contacts?submenuId=ML11-SL1");
                }
            }
        }
        if($this->_request->isPost())
        {
            $emailexit = $ftv_obj->getExistingEmail($user_params["email"]);
            if($emailexit == 'yes')
            {
                $this->_helper->FlashMessenger('Email id is already exit.');
                $this->_redirect("/ftv/ftv-contacts?submenuId=ML11-SL1");
            }
            ////for goroup Id in users table////
            $accesto = $user_params["accessto"];
            if(count($accesto) == 2)
                $ftvtype = "both";
            elseif(count($accesto) == 1 && $accesto[0] == 'edito')
                $ftvtype = "edito";
            elseif(count($accesto) == 1 && $accesto[0] == 'chaine')
                $ftvtype = "chaine";
            $ftv_obj->email_id = $user_params["email"] ;
            $ftv_obj->password = $user_params["password"] ;
            $ftv_obj->first_name = $user_params["first_name"];
            $ftv_obj->last_name = $user_params["last_name"];
            $ftv_obj->ftvtype = $ftvtype;
            try
            {
                if($ftv_obj->insert())
                {
                    $this->_helper->FlashMessenger('Contact Created Successfully.');
                    $this->_redirect("/ftv/ftv-contacts?submenuId=ML11-SL1");
                }
                //$this->render('user_adduser');
            }
            catch(Zend_Exception $e)
            {
                $this->_helper->FlashMessenger('Contact Creation Failed.');
                $this->_redirect("/ftv/ftv-contacts?submenuId=ML11-SL1");
            }
        }
        $this->_view->render("ftv_ftvcontactcreation");
    }
    ///////////////edit user function //////////////////
    public function userEditAction()
	{
        $userId = $this->_request->getParam('userId');
        $userplus_obj=new Ep_User_UserPlus();
        $details= $userplus_obj->getUsersDetailsOnId($userId);
        $user_obj = new Ep_User_User();
        $usergrouplist = $user_obj->getUserGroups();
        foreach($usergrouplist as $key=>$value)
        {
            /* if($value['groupName']!='superadmin')*/
            $usergroups[$value['groupName']]=$value['groupName'];
        }

        $this->_view->usergroups =  $usergroups;
        $this->_view->Userdetails=$details;
        $this->_view->profilepic = "/FO/profiles/bo/".$details[0]['identifier']."/logo.jpg";

        if($this->_request-> isPost())
        {
            $user_params=$this->_request->getParams();  // print_r($user_params); exit;
            $userplus_obj = new Ep_User_UserPlus();
            $user_obj = new Ep_User_User();
            ////for goroup Id in users table////
            $grouparray = array('superuser'=>1,'ceouser'=>2,'salesuser'=>3, 'chiefeditor'=>4, 'editor'=>5, 'seouser'=>6, 'customercare'=>7, 'partner'=>8, 'facturation'=>9, 'custom'=>10, 'multilingue'=>11);
            $user_obj->login=$user_params["login"] ;
            $user_obj->email=$user_params["email"] ;
            $user_obj->password=$user_params["password"] ;
            $user_obj->status=$user_params["status"] ;
            $user_obj->type=$user_params["type"] ;
            $user_group = $grouparray[$user_params["type"]];
            $user_obj->groupId=$user_group;
            $data_user = array("login"=>$user_obj->login, "email"=>$user_obj->email, "password"=>$user_obj->password,
                "status"=>$user_obj->status, "type"=>$user_obj->type, "groupId"=>$user_obj->groupId);
            $query_user = "identifier= '".$userId."'";
            try
            {
                $userplus_obj->initial=$user_params["initial"] ;
                $userplus_obj->first_name=$user_params["first_name"] ;
                $userplus_obj->last_name=$user_params["last_name"] ;
                $userplus_obj->address=$user_params["address"] ;
                $userplus_obj->city=$user_params["city"] ;
                $userplus_obj->state=$user_params["state"] ;
                $userplus_obj->zipcode=$user_params["zipcode"] ;
                $userplus_obj->country=$user_params["country"] ;
                $userplus_obj->phone_number=$user_params["phone_number"] ;

                $data_userplus = array("initial"=>$userplus_obj->initial, "first_name"=>$userplus_obj->first_name, "last_name"=>$userplus_obj->last_name,
                    "address"=>$userplus_obj->address, "city"=>$userplus_obj->city, "state"=>$userplus_obj->state, "zipcode"=>$userplus_obj->zipcode,
                    "country"=>$userplus_obj->country, "phone_number"=>$userplus_obj->phone_number);
                $query_userplus = "user_id= '".$userId."'";

                $user_obj->updateUser($data_user,$query_user);
                $userplus_obj->updateUserPlus($data_userplus,$query_userplus);

                $this->_helper->FlashMessenger('Profile Updated Successfully.');
                $this->_redirect("/user/bo-users?submenuId=ML10-SL3");
                // $this->render('user_userdetails');
            }
            catch(Zend_Exception $e)
            {
                echo $e->getMessage();
                $this->_view->error_msg =$e->getMessage()." D&eacute;sol&eacute;! Mise en erreur.";
                $this->render('user_useredit');
            }
        }else
        {
            $this->render('user_useredit');
        }
    }
    // all the ftv contacts to dispay in grid  ///
    public function ftvContactsAction()
    {
        $ftvcontacts=new Ep_Ftv_FtvContacts();
        $details= $ftvcontacts->getFtvContacts();
        if($details != 'NO')
            $this->_view->ftvcontactsdetails=$details;
        $this->render('ftv_ftvcontacts');
    }
    ///edit the ftv assign time ////
    public function editassigntimeAction()
    {
        $prevurl = getenv("HTTP_REFERER");
        $user_params=$this->_request->getParams();
        $ftvrequest_obj = new Ep_Ftv_FtvRequests();
        $ftvpausetime_obj = new Ep_Ftv_FtvPauseTime();
        $requestId = $user_params['requestId'];
        $requestsdetail = $ftvrequest_obj->getRequestsDetails($requestId);
        ($user_params['editftvspentdays'] == '') ? $days=0 : $days=$user_params['editftvspentdays'];
        ($user_params['editftvspenthours'] == '') ? $hours=0 : $hours=$user_params['editftvspenthours'];
        ($user_params['editftvspentminutes'] == '') ? $minutes=0 : $minutes=$user_params['editftvspentminutes'];
        ($user_params['editftvspentseconds'] == '') ? $seconds=0 : $seconds=$user_params['editftvspentseconds'];
        /*if($user_params['addasigntime'] == 'addasigntime')   ///when time changes in mail content in publish ao popup//
        {
            /*$editdate = date('Y-m-d', strtotime($user_params['editftvassigndate']));
            $edittime = date('H:i:s', strtotime($user_params['editftvassigntime']));
            $editdatetime =$editdate." ".$edittime;
            $data = array("assigned_at"=>$editdatetime);////////updating
            $query = "identifier= '".$user_params['requestId']."'";
            $ftvrequest_obj->updateFtvRequests($data,$query);
            $parameters['ftvType'] = "chaine";
           // $newseconds = $this->allToSeconds($user_params['editftvspentdays'],$user_params['editftvspenthours'],$user_params['editftvspentminutes'],$user_params['editftvspentseconds']);
            echo "<br>".$format = "P".$days."DT".$hours."H".$minutes."M".$seconds."S";
            echo "<br>".$requestsdetail[0]['assigned_at'];
            $time=new DateTime($requestsdetail[0]['assigned_at']);
            $time->sub(new DateInterval($format));
            echo "<br>".$assigntime = $time->format('Y-m-d H:i:s');
            $data = array("assigned_at"=>$assigntime);////////updating
          echo   $query = "identifier= '".$requestId."'";
            $ftvrequest_obj->updateFtvRequests($data,$query);
            $this->_redirect($prevurl);
        }
        elseif($user_params['subasigntime'] == 'subasigntime')
        {
            $format = "P".$days."DT".$hours."H".$minutes."M".$seconds."S";
            $time=new DateTime($requestsdetail[0]['assigned_at']);
            $time->add(new DateInterval($format));
            $assigntime = $time->format('Y-m-d H:i:s');
            $data = array("assigned_at"=>$assigntime);////////updating
            $query = "identifier= '".$requestId."'";
            $ftvrequest_obj->updateFtvRequests($data,$query);
            $this->_redirect($prevurl);
        }*/
        $inpause  = $ftvpausetime_obj->inPause($requestId);
        $requestsdetail[0]['inpause'] = $inpause;
        $ptimes  = $ftvpausetime_obj->getPauseDuration($requestId);
        $assigntime = $requestsdetail[0]['assigned_at'];
        //echo $requestId;  echo  $requestsdetail[0]['assigned_at'];
        if(($requestsdetail[0]['status'] == 'done' || $inpause == 'yes') && $requestsdetail[0]['assigned_at'] != null)
        {
            if($requestsdetail[0]['status'] == "closed")
                $time1 = ($requestsdetail[0]['cancelled_at']); /// created time
            elseif ($requestsdetail[0]['status'] == "done")
                $time1 = ($requestsdetail[0]['closed_at']); /// created time
            else{
                if($inpause == 'yes') {
                    $time1 = ($requestsdetail[0]['pause_at']);
                }else {
                    $time1 = (date('Y-m-d H:i:s'));///curent time
                }
            }
            $pausedrequests  = $ftvpausetime_obj->pausedRequest($requestId);
            if($pausedrequests == 'yes')
            {
                $time2 = $this->subDiffFromDate($requestId, $requestsdetail[0]['assigned_at']);
            }else{
                $time2 = $requestsdetail[0]['assigned_at'];
            }
            $difference = $this->timeDifference($time1, $time2);
        }elseif($requestsdetail[0]['assigned_at'] != null){
            $time1 = (date('Y-m-d H:i:s'));///curent time
            $pausedrequests  = $ftvpausetime_obj->pausedRequest($requestId);
            if($pausedrequests == 'yes')
            {
                $updatedassigntime = $this->subDiffFromDate($requestId, $requestsdetail[0]['assigned_at']);
            }else{
                  $updatedassigntime = $requestsdetail[0]['assigned_at'];
            }
            $time2 = $updatedassigntime;
            $difference = $this->timeDifference($time1, $time2);
        }
        ////when user trying to edit the time spent///
        if($user_params['editftvassignsubmit'] == 'editftvassignsubmit')   ///when button submitted in popup//
        {
            $newseconds = $this->allToSeconds($days,$hours,$minutes,$seconds);
            $previousseconds = $this->allToSeconds($difference['days'],$difference['hours'],$difference['minutes'],$difference['seconds']);
            if($newseconds > $previousseconds){
                $diffseconds = $newseconds-$previousseconds;
                $difftime = $this->secondsTodayshours($diffseconds);
                $format = "P".$difftime['days']."DT".$difftime['hours']."H".$difftime['minutes']."M".$difftime['seconds']."S";
                $requestsdetail[0]['assigned_at'];
                $time=new DateTime($requestsdetail[0]['assigned_at']);
                $time->sub(new DateInterval($format));
                $assigntime = $time->format('Y-m-d H:i:s');
                $data = array("assigned_at"=>$assigntime);////////updating
                $query = "identifier= '".$requestId."'";
                $ftvrequest_obj->updateFtvRequests($data,$query);
                $this->_redirect($prevurl);
            }elseif($newseconds < $previousseconds){
                $diffseconds = $previousseconds-$newseconds;
                $difftime = $this->secondsTodayshours($diffseconds);
                $format = "P".$difftime['days']."DT".$difftime['hours']."H".$difftime['minutes']."M".$difftime['seconds']."S";
                $time=new DateTime($requestsdetail[0]['assigned_at']);
                $time->add(new DateInterval($format));
                $assigntime = $time->format('Y-m-d H:i:s');
                $data = array("assigned_at"=>$assigntime);////////updating
                $query = "identifier= '".$requestId."'";
                $ftvrequest_obj->updateFtvRequests($data,$query);
                $this->_redirect($prevurl);
            }else
                $this->_redirect($prevurl);
        }
        /*$this->_view->reqasgndate = date("d-m-Y", strtotime($reqdetails[0]['assigned_at']));
        $this->_view->reqasgntime = date("g:i A", strtotime($reqdetails[0]['assigned_at']));*/
        $this->_view->days = $difference['days'];
        $this->_view->hours = $difference['hours'];
        $this->_view->minutes = $difference['minutes'];
        $this->_view->seconds = $difference['seconds'];
        $this->_view->requestId = $user_params['requestId'];
        $this->_view->requestobject = $requestsdetail[0]['request_object'];
        $this->_view->current_duration= $difference['days']."j ".$difference['hours']."h ".$difference['minutes']."m ".$difference['seconds']."s ";
        $this->_view->extendparttime   = 'no';
        $this->_view->extendcrtparttime   = 'no';
        $this->_view->editftvassigntime  = 'yes';
        $this->_view->nores = 'true';
        $this->_view->render("ongoing_extendtime_writer_popup");
    }
    // the time difference between the 2 dates //
    public function timeDifference($time1, $time2)
    {
        $timestamp = new DateTime($time1);
        $diff = $timestamp->diff(new DateTime($time2));
        $diffarray = array();
        $diffarray['days'] = $diff->format('%d');
        $diffarray['hours'] = $diff->format('%h');
        $diffarray['minutes'] = $diff->format('%i');
        $diffarray['seconds'] = $diff->format('%s');
        return $diffarray;
    }
    //converting the days, hours, minutes, seconds to seconds////
    public function allToSeconds($days, $hours, $minutes, $seconds)
    {
        $tatalseconds = 0;
        if($days != 0)
            $tatalseconds+= $days*24*60*60;
        if($hours != 0)
            $tatalseconds+= $hours*60*60;
        if($minutes != 0)
            $tatalseconds+= $minutes*60;
        if($seconds != 0)
            $tatalseconds+= $seconds;
        return $tatalseconds;
    }
    // convert seconds to day and hours formate //
    function secondsTodayshours($ss) {
        $arr = array();
        $s = $ss%60;
        $m = floor(($ss%3600)/60);
        $h = floor(($ss%86400)/3600);
        $d = floor(($ss%2592000)/86400);
        $M = floor($ss/2592000);
        $arr['months'] = $M;
        $arr['days'] = $d;
        $arr['hours'] = $h;
        $arr['minutes'] = $m;
        $arr['seconds'] = $s;
        // return "$M months, $d days, $h hours, $m minutes, $s seconds";
        return $arr;
    }
    // convert seconds to the words //
    function secondsToWords($seconds)
    {
        $ret = "";
        /*** get the days ***/
        $days = intval(intval($seconds) / (3600*24));
        if($days> 0)
        {
            $ret .= "$days days ";
        }
        /*** get the hours ***/
        $hours = (intval($seconds) / 3600) % 24;
        if($hours > 0)
        {
            $ret .= "$hours hours ";
        }
        /*** get the minutes ***/
        $minutes = (intval($seconds) / 60) % 60;
        if($minutes > 0)
        {
            $ret .= "$minutes minutes ";
        }
        /*** get the seconds ***/
        $seconds = intval($seconds) % 60;
        if ($seconds > 0) {
            $ret .= "$seconds seconds";
        }
        return $ret;
    }
    /* added by naseer on 15-10-2015*/
    //function to calculate time differences of FTV DEVS//
    public function ftvTimelineAction()
    {
        session_start();
        $params = $this->_request->getParams();
        if($params['search'] != '') {
            $ftvrequest_obj = new Ep_Ftv_FtvRequests();
            $res = $ftvrequest_obj->fetchTimeline($params);
            $total_time = 0;
            if ($res != "NO") {
                for($i=0,$j=0;$i< count($res) ;$i++,$j++){

                    $time_diff2 = 0;
                    if (!is_null($res[$i]['pause_at']) && !is_null($res[$i]['resume_at'])) {
                        $time_diff2 = (strtotime($res[$i]['resume_at']) - strtotime($res[$i]['pause_at']));
                        $res[$i]['mid_time'] .= $res[$i]['pause_at']. ' - '.$res[$i]['resume_at'];
                        while($res[$i+1]['identifier'] == $res[$i]['identifier']) {
                            if (!is_null($res[$i+1]['pause_at']) && !is_null($res[$i+1]['resume_at'])) {
                                $time_diff2 += (strtotime($res[$i + 1]['resume_at']) - strtotime($res[$i + 1]['pause_at']));
                            }
                            $res[$i+1]['mid_time'] = $res[$i]['mid_time'].', '.$res[$i+1]['pause_at'] .' - '.$res[$i+1]['resume_at'];
                            $i++;
                        }

                    }else{
                        $res[$i]['mid_time'] = $res[$i]['pause_at']. ' - '. $res[$i]['resume_at'];
                    }
                    $time_diff1 = 0 ;
                    $temp = 0;
                    if (!is_null($res[$i]['cancelled_at']) || !is_null($res[$i]['closed_at']) ) {
                        if(!is_null($res[$i]['cancelled_at']) )
                            $time_diff1 = (strtotime($res[$i]['cancelled_at']) - strtotime($res[$i]['assigned_at']));
                        elseif(!is_null($res[$i]['closed_at']))
                            $time_diff1 = (strtotime($res[$i]['closed_at']) - strtotime($res[$i]['assigned_at']));

                        //subtracting pause time and total time only if closed or cancelled//
                        $time_diff = $time_diff1 - $time_diff2;
                        if( $time_diff < 60){
                            $temp = '00:00:'.date('s',$time_diff);
                        }
                        elseif (($time_diff >= (60)) && ($time_diff < (86400))) {
                            if($time_diff > 7200)
                                $temp = '<span style="color:#ff0000;">'.date('H:i:s',$time_diff-3600).'</span>';
                            else
                             $temp = date('H:i:s',$time_diff-3600);
                        }
                        //if time difference is greater than 60*60*60 and lesser than 60*60*60*24,then its hours
                        elseif (($time_diff >= (86400)) && ($time_diff <= (60 * 60 * 60 * 24))) {
                            $temp = '<span style="color:#ff0000;">'.date('d\d\a\y\s, H:i:s',$time_diff-86400-3600).'</span>';
                        }
                    }
                    if($temp != '0'){
                        $res[$i]['finaltime'] = $temp;
                        $total_time =  $total_time + $time_diff;
                    }
                    else{
                        $res[$i]['finaltime'] = 'NOVALUE';
                    }
                    $result[$j] = $res[$i];
                }
                //echo "<pre>";print_r($res);exit;
                // code to convert total time into readable format.
                if( $total_time < 60){
                    $temp2 = '00:00:'.date('s',$total_time);
                }
                elseif (($total_time >= (60)) && ($total_time < (86400))) {
                    if($total_time > 7200)
                        $temp2 = '<span style="color:#ff0000;">'.date('H:i:s',$total_time-3600).'</span>';
                    else
                        $temp2 = date('H:i:s',$total_time-3600);
                }
                //if time difference is greater than 60*60*60 and lesser than 60*60*60*24,then its hours
                elseif (($total_time >= (86400)) && ($total_time <= (60 * 60 * 60 * 24))) {
                    $temp2 = '<span style="color:#ff0000;">'.date('d\d\a\y\s, H:i:s',$total_time-86400-3600).'</span>';
                }
                elseif (($total_time > (60 * 60 * 60 * 24))) {
                    $temp2 = '<span style="color:#ff0000;">'.date('m\m\o\n\t\h d\d\a\y\s, H:i:s',$total_time-86400-3600).'</span>';
                }
                $this->_view->paginator = $result;
                $this->_view->total_time = $temp2;
                $_SESSION['paginator'] = $result;//saving for later use
                $_SESSION['total_time'] = $temp2;////saving for later use
                $_SESSION['params'] = $params;////saving for later use while downloding same content(save execution time)

            }
            else {
                $this->_view->nores = "true";
                $_SESSION['paginator'] = NULL;
                $_SESSION['total_time'] = NULL;
            }
            $this->_view->render("ftv_timeline");

        }
        else{
            $this->_view->nores = "true";
            $this->_view->render("ftv_timeline");
        }
    }
    public function ftvTimelineDownloadXlsxAction(){
        //check if the session is not NULL and has some values.
        if($_SESSION['paginator'] != NULL){
            $conditions =  $_SESSION['params'];
            $res = $_SESSION['paginator'];
            $total_time = $_SESSION['total_time'];
            require_once PHP_LIBRARY;
            require_once PHP_LIBRARY_EXCEL_WRITER;
            if($conditions['pdstart_date'] != '' && $conditions['pdend_date'] != '' && $conditions['sel_type'] != '') {
                $file_name = $conditions['pdstart_date'] . "_to_" . $conditions['pdend_date'] . "_" . $conditions['sel_type'] . "_" . time() . ".xlsx";
            }
            else if($conditions['pdstart_date'] != '' && $conditions['pdend_date'] != ''){
                $file_name =  $conditions['pdstart_date'] . "_to_" . $conditions['pdend_date'] . "_" .time() . ".xlsx";
            }
            else if($conditions['sel_type'] != '' ){
                $file_name =  $conditions['sel_type'] . "_" .time() . ".xlsx";
            }
            else{
                $file_name =  time() . ".xlsx";
            }
            $file = FO_INVOICE_XLS.$file_name;
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $rowCount = 1;
                $styleArray = array(
                    'font'  => array(
                        'bold'  => true,
                        'size'  => 14
                    ));
                $styleheadArray = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb'=>'FFA500'),
                    ),
                    'font'  => array(
                        'bold'  => true,
                        'size'  => 14
                    ));
                $noValeStyleArray = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb'=>'FF0000'),

                    ));
                $highlightStyleArray = array(
                    'font'  => array(
                        'bold'  => true,
                        'size'  => 12,
                        'color' => array('rgb' => 'FF0000')
                    ));
                $styletotalArray= array(
                    'font'  => array(
                        'bold'  => true,
                        'color'  => array('rgb' => 'FF0000'),
                        'size'  => 12
                    ));
                $objPHPExcel->getActiveSheet()->getStyle('A'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('B'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('C'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('D'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('E'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('F'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('G'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('H'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('I'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('J'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->getStyle('K'. $rowCount)->applyFromArray($styleheadArray);
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'Identifier');
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, 'Request by');
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, 'Assigned To');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, 'Request Object');
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, 'Assigned At');
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, 'Pause - Resume');
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, 'Closed At');
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, 'Cancelled At');
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, 'Ftv type');
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, 'Active');
                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'In hrs');
                foreach ($res as $details){
                        $rowCount++;
                        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'ID = '.$details['identifier']);
                        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $details['contactname']);
                        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $details['assignedname']);
                        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, utf8_encode($details['request_object']));
                        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, ($details['assigned_at'] != '') ? $details['assigned_at'] : '--');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $details['mid_time']);
                        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, ($details['closed_at'] != '')  ? $details['closed_at'] : '--');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, ($details['cancelled_at'] != '')  ? $details['cancelled_at'] : '--');
                        $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $details['ftvtype']);
                        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $details['active']);
                        if($details['finaltime'] == 'NOVALUE') {
                            $objPHPExcel->getActiveSheet()->getStyle('K'. $rowCount)->applyFromArray($noValeStyleArray);
                            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $details['finaltime']);
                        }else{
                            if(preg_match('/\b(span)\b/',$details['finaltime'])){
                                $objPHPExcel->getActiveSheet()->getStyle('K'. $rowCount)->applyFromArray($highlightStyleArray);
                            }
                            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, strip_tags($details['finaltime']));
                        }
                }
                $rowCount++;
                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Total Time :'.strip_tags($total_time));


            /* for loop to resize all the width of cell*/
            foreach(range('A','K') as $columnID)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save($file);
            $_SESSION['file']=$file;
            // code to download xlxs file automatically xlsx files have to be downloaded with a php script writen in web directory//
            include (APP_PATH_ROOT.'download-xlsx.php');
            /** Author: Thilagam **/
            /** Date:03/05/2016 **/
            /** Reason: Code optimization **/
            //$this->_redirect("/BO/download-files.php?function=downloadXlsx&fullpath=$file");
            //downloding for tempoary//
            exit;
        }
        else{
            echo 0;
        }
    }
    public function loadFtvcontactsAction(){
        $type =  $this->_request->getParam('type');
        $ftvcontact_obj = new Ep_Ftv_FtvContacts();
        $ftvContacts = $ftvcontact_obj->getFtvContacts($type);
        $option = '<select id="contactname" name="contactname[]"  multiple="multiple" data-placeholder="FTV Contact">';
        for($i = 0; $i < count($ftvContacts[0])-1 ; $i++){
            $option .= '<option value="'.$ftvContacts[$i]['identifier'].'">'.$ftvContacts[$i]['first_name'].' '.$ftvContacts[$i]['last_name'].'('.$ftvContacts[$i]['email_id'].')</option>';
        }
        $option .= '</select>';
        echo $option;
        exit;
    }
}
