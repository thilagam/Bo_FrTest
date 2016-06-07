$sWhere="";
        $sOrder="";
        $sLimit="";
        $condition="";
        $rResult = $userplus_obj->ListStatsClientsinfo($sWhere, $sOrder, $sLimit, $condition);
        if(!empty($rResult))
        {
            $i=0;
            foreach($rResult as $key=>$value)
            {
                $rResult[$key]['slno']=$i+1;
                $rResult[$key]['first_name']=utf8_encode($rResult[$key]['first_name']);
                $rResult[$key]['last_name']=utf8_encode($rResult[$key]['last_name']);
                $rResult[$key]['email']=utf8_encode($rResult[$key]['email']);
                $rResult[$key]['ao_count']='<a href="client-edit?submenuId=ML2-SL7&tab=aolistclient&userId=' . $rResult[$key]['identifier'] . '" class="num-large" target="_blank">' . $rResult[$key]['art_count'] . '</a>';
                $rResult[$key]['art_count']='<label class="label label-warning">' . $rResult[$key]['art_count'] . '</label>';
                $rResult[$key]['art_pcount']='<label class="label label-warning">' . $rResult[$key]['art_pcount'] . '</label>';
                $rResult[$key]['created_at']=date("d-m-Y H:i", strtotime($rResult[$key]['created_at']));
                $rResult[$key]['type']='<label class="label label-info">' . utf8_encode($rResult[$key]['type']) . '</label>';
                $rResult[$key]['company_name']=utf8_encode($rResult[$i]['company_name']);
                $email = utf8_encode($rResult[$key]['email']);
                $password = $rResult[$key]['password'];
                if ($rResult[$key]['ao_count'] != 0)
                    $rResult[$key]['download'] = '<a href="http://ep-test.edit-place.com/getClientArticles.php?client_id=' . $rResult[$key]['identifier'] . '">Download</a>';
                else
                    $rResult[$key]['download'] = '-';
                $type = utf8_encode($rResult[$key]['type']);
                if ($type == 'client') 
                {
                    $rResult[$key]['options'] = '<a href="client-edit?submenuId=ML10-SL2&tab=editclient&userId=' . $rResult[$key]['identifier'] . '" class="hint--left hint--info" data-hint="edit profile"><i class="icon-pencil"></i> </a>
                        <a href="client-edit?submenuId=ML10-SL2&tab=viewclient&userId=' . $rResult[$key]['identifier'] . '" class="hint--left hint--info" data-hint="view profile"><i class="icon-eye-open"></i></a>
                         <a href="http://ep-test.edit-place.com/user/email-login?user=' . MD5("ep_login_" . $email) . '&hash=' . MD5("ep_login_" . $password) . '&type=' . $type . '&redirectpage=home" target="_blank"><i class="splashy-contact_blue"></i></a>';
                }
                if ($type == 'superclient')
                    $rResult[$key]['options'] = '<a href="superclientcreate-step1?submenuId=ML9-SL1&uaction=edit&userId=' . $rResult[$key]['identifier'] . '" class="hint--left hint--info" data-hint="edit profile"><i class="icon-pencil"></i> </a>';
                if ($type == 'sccontact')
                    $rResult[$key]['options'] = '';
            $i++;

            }
        }
        $iTotal=count($rResult);
        $output = array(
            "sEcho" => 1,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => $rResult
        );
        //print_r($output);
        echo json_encode($output);



        $('#clientsgrid').dataTable().fnDestroy();
    $('#clientsgrid').dataTable({
        "sAjaxSource": "/user/loadclient",
        "aoColumns": [
            {mData: 'slno'},
            {mData: 'company_name'},
            {mData: 'email'},
            {mData: 'created_at'},
            {mData: 'type'},
            {mData: 'ao_count'},
            {mData: 'art_count'},
            {mData: 'art_pcount'},
            {mData: 'download'},
            {mData: 'options'}
        ],
        "fnDrawCallback":callback
    });
    function callback() {
            $(".table").css("width","100%");
        }