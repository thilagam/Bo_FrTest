<?

class HrmController extends Ep_Controller_Action
{
    private $controller = "Hrm";
    protected $session = null;

    public function init()
    {
        parent::init();
        Zend_Loader::loadClass('Ep_Document_DocTrack');
        $this->session = new Zend_Session_Namespace('
		');
        $this->_view->lang = $this->_lang;
        $this->adminLogin = Zend_Registry::get('adminLogin');
        $this->session = $this->adminLogin;
        $this->_view->loginName = $this->adminLogin->loginName;
        ////if session expires/////
        if ($this->adminLogin->loginName == '' && $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            $this->_redirect('/index/');
        }
        if ($this->adminLogin->loginName == '' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            echo "session expired...please <a href='/index'>click here</a> to login";
            exit;
        }

    }

    ///get all weekend dates in any year for accessing data from js///
    public function weekendDaysAction()
    {
        $params = $this->_request->getParams();
        $year = $params['year'];
        $weekends = array();
        $type = CAL_GREGORIAN;
        for ($m = 1; $m <= 12; $m++) {
            $month[$m] = date($m); // Month ID, 1 through to 12.
            $year = date($year); // Year in 4 digit 2009 format.
            $day_count[$m] = cal_days_in_month($type, $month[$m], $year); // Get the amount of days
            //loop through all days
            for ($i = 1; $i <= $day_count[$m]; $i++) {
                $date = $year . '/' . $month[$m] . '/' . $i; //format date
                $get_name = date('l', strtotime($date)); //get week day
                $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars
                //if not a weekend add day to array
                if ($day_name == 'Sun' || $day_name == 'Sat') {
                    $date1 = $year . '-' . $month[$m] . '-' . $i;
                    $weekends[] = date('Y-m-d', strtotime($date1));
                    // array_push($weekends, $e);
                }
            }
        }
        echo json_encode($weekends);
        ///////////////////////////////////////////////////////////////////////////////
    }

    ///list of weekend in a year to asscess them in controller///
    public function weekendDays($year)
    {
        $weekends = array();
        $type = CAL_GREGORIAN;
        for ($m = 1; $m <= 12; $m++) {
            $month[$m] = date($m); // Month ID, 1 through to 12.
            $year = date($year); // Year in 4 digit 2009 format.
            $day_count[$m] = cal_days_in_month($type, $month[$m], $year); // Get the amount of days
            //loop through all days
            for ($i = 1; $i <= $day_count[$m]; $i++) {
                $date = $year . '/' . $month[$m] . '/' . $i; //format date
                $get_name = date('l', strtotime($date)); //get week day
                $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars
                //if not a weekend add day to array
                if ($day_name == 'Sun' || $day_name == 'Sat') {
                    $date1 = $year . '-' . $month[$m] . '-' . $i;
                    $weekends[] = date('Y-m-d', strtotime($date1));
                }
            }
        }

        return $weekends;
        ///////////////////////////////////////////////////////////////////////////////
    }

    ///get all weekend dates in any year for accessing data from js///
    public function allPublicHolidaysAction()
    {
        $params = $this->_request->getParams();
        $year = $params['year'];
        $userId = $this->adminLogin->userId;
        $userplus_obj = new Ep_User_UserPlus();
        $userdetials = $userplus_obj->getUsersDetailsOnId($userId); //print_r($leavedetials); exit;
        if ($userdetials[0]['country'] == 38)
            $usercountry = 'fr';
        elseif ($userdetials[0]['country'] == 53)
            $usercountry = 'ind';
        else
            $usercountry = 'uk';
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $publicholidays = $hrmleaves_obj->publicHolidays($year, $usercountry);
        foreach ($publicholidays as $key => $value) {
            $pubholidays[] = date('Y-m-d', strtotime($value['start']));
        }
        $weekends = $this->weekendDays($year);
        $array3 = array_merge($pubholidays, $weekends); //print_r($array3);
        echo json_encode($array3);
    }
    ///get all leaves applied by the user///
    public function allUserAppliedLeavesAction()
    {
        $params = $this->_request->getParams();
        $year = $params['year'];
        $userId = $this->adminLogin->userId;
        $userplus_obj = new Ep_User_UserPlus();
        $userdetials = $userplus_obj->getUsersDetailsOnId($userId); //print_r($leavedetials); exit;
        if ($userdetials[0]['country'] == 38)
            $usercountry = 'fr';
        elseif ($userdetials[0]['country'] == 53)
            $usercountry = 'ind';
        else
            $usercountry = 'uk';
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $publicholidays = $hrmleaves_obj->publicHolidays($year, $usercountry);
        foreach ($publicholidays as $key => $value) {
            $pubholidays[] = date('Y-m-d', strtotime($value['start']));
        }
        $weekends = $this->weekendDays($year);
        $array3 = array_merge($pubholidays, $weekends); //print_r($array3);
        echo json_encode($array3);
    }


    ///get all weekend dates in any year for accessing data from js///
    public function allPublicWeekendsHolidays($year, $country)
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        if(count($year) != 2)
        {
            $publicholidays = $hrmleaves_obj->publicHolidays($year[0], $country);
            if($publicholidays != 'NO') {
                foreach ($publicholidays as $key => $value) {
                    $pubholidays[] = date('Y-m-d', strtotime($value['start']));
                }
            } else  {
                $pubholidays = array();
            }
            $weekends = $this->weekendDays($year[0]);
            return $array3 = array_merge($pubholidays, $weekends);
        }else {
            $pubholidays1 = array();
            $pubholidays2 = array();
            $publicholidays = $hrmleaves_obj->publicHolidays($year[0], $country);
            if ($publicholidays != 'NO') {
                foreach ($publicholidays as $key => $value) {
                    $pubholidays1[] = date('Y-m-d', strtotime($value['start']));
                }
            }
            $publicholidays = $hrmleaves_obj->publicHolidays($year[1], $country);
            if ($publicholidays != 'NO') {
                foreach ($publicholidays as $key => $value) {
                    $pubholidays2[] = date('Y-m-d', strtotime($value['start']));
                }
            }
            $pubholidays3 = array_merge($pubholidays1, $pubholidays2);
            $weekends1 = $this->weekendDays($year[0]);
            $weekends2 = $this->weekendDays($year[1]);
            $weekends3 = array_merge($weekends1, $weekends2);
            return $array3 = array_merge($pubholidays3, $weekends3); //print_r($array3);
        }

    }

    ///get all weekend dates in any year///
    public function userLeaves($user)
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $userholidays = $hrmleaves_obj->getUserLeaves($user);
        $leaves['plcount'] = 0;
        $leaves['slcount'] = 0;
        $leaves['mtcount'] = 0;
        $leaves['rdvcount'] = 0;
        $leaves['compoffcount'] = 0;
        $leaves['plpending'] = 0;
        $leaves['slpending'] = 0;
        $leaves['mtpending'] = 0;
        $leaves['rdvpending'] = 0;
        $leaves['compoffpending'] = 0;
        $leaves['plapproved'] = 0;
        $leaves['slapproved'] = 0;
        $leaves['mtapproved'] = 0;
        $leaves['rdvapproved'] = 0;
        $leaves['compoffapproved'] = 0;
        $leaves['plrefused'] = 0;
        $leaves['slrefused'] = 0;
        $leaves['mtrefused'] = 0;
        $leaves['rdvrefused'] = 0;
        $leaves['compoffrefused'] = 0;

        if ($userholidays != 'NO') {
            foreach ($userholidays as $key => $value) {
                $start = date('Y-m-d', strtotime($value['start']));
                $end = date('Y-m-d', strtotime($value['end']));
                if ($value['type'] == 'vacation') {
                    $value['date_details'] = rtrim($value['date_details'], ",");
                    $days = explode(',', $value['date_details']);
                    for ($i = 0; $i < count($days); $i++) {
                        $daydetail[$i] = explode('|', $days[$i]);
                        if ($daydetail[$i][1] == 'full')
                            $leaves['plcount'] += 1;
                        else if ($daydetail[$i][1] == 'half')
                            $leaves['plcount'] += 0.5;
                    }
                    if ($value['status'] == 'inprocess') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['plpending'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['plpending'] += 0.5;
                        }
                    }
                    if ($value['status'] == 'approved') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['plapproved'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['plapproved'] += 0.5;
                        }
                    }
                    if ($value['status'] == 'refused') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['plrefused'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['plrefused'] += 0.5;
                        }
                    }
                }
                if ($value['type'] == 'sick') {
                    $value['date_details'] = rtrim($value['date_details'], ",");
                    $days = explode(',', $value['date_details']); //print_r();
                    for ($i = 0; $i < count($days); $i++) {
                        $daydetail[$i] = explode('|', $days[$i]);
                        if ($daydetail[$i][1] == 'full')
                            $leaves['slcount'] += 1;
                        else
                            $leaves['slcount'] += 0.5;
                    }
                    if ($value['status'] == 'inprocess') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['slpending'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['slpending'] += 0.5;
                        }
                    }
                    if ($value['status'] == 'approved') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['slapproved'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['slapproved'] += 0.5;
                        }
                    }
                    if ($value['status'] == 'refused') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['slrefused'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['slrefused'] += 0.5;
                        }
                    }
                }
                if ($value['type'] == 'maternity') {
                    $days = explode(',', $value['date_details']);
                    for ($i = 0; $i < count($days); $i++) {
                        $daydetail[$i] = explode('|', $days[$i]);
                        if ($daydetail[$i][1] == 'full')
                            $leaves['mtcount'] += 1;
                        else if ($daydetail[$i][1] == 'half')
                            $leaves['mtcount'] += 0.5;
                    }
                    if ($value['status'] == 'inprocess') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['mtpending'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['mtpending'] += 0.5;
                        }
                    }
                    if ($value['status'] == 'approved') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['mtapproved'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['mtapproved'] += 0.5;
                        }
                    }
                    if ($value['status'] == 'refused') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['mtrefused'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['mtrefused'] += 0.5;
                        }
                    }
                }
                if ($value['type'] == 'rdv') {
                    $leaves['rdvcount']++;
                    if ($value['status'] == 'inprocess')
                        $leaves['rdvpending']++;
                    if ($value['status'] == 'approved')
                        $leaves['rdvapproved']++;
                    if ($value['status'] == 'refused')
                        $leaves['rdvrefused']++;
                }
                if ($value['type'] == 'compoff') {
                    $days = explode(',', $value['date_details']);
                    for ($i = 0; $i < count($days); $i++) {
                        $daydetail[$i] = explode('|', $days[$i]);
                        if ($daydetail[$i][1] == 'full')
                            $leaves['compoffcount'] += 1;
                        else if ($daydetail[$i][1] == 'half')
                            $leaves['compoffcount'] += 0.5;
                    }
                    if ($value['status'] == 'inprocess') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['compoffpending'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['compoffpending'] += 0.5;
                        }
                    }
                    if ($value['status'] == 'approved') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['compoffapproved'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['compoffapproved'] += 0.5;
                        }
                    }
                    if ($value['status'] == 'refused') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['compoffrefused'] += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $leaves['compoffrefused'] += 0.5;
                        }
                    }
                }
                /*if($start == $end)
                {
                    if($value['type'] == 'vacation'){
                        $leaves['plcount']++;
                        if($value['status'] == 'inprocess')
                            $leaves['plpending']++;
                        if($value['status'] == 'approved')
                            $leaves['plapproved']++;
                        if($value['status'] == 'refused')
                            $leaves['plrefused']++;
                    }
                    if($value['type'] == 'sick'){
                        $leaves['slcount']++;
                        if($value['status'] == 'inprocess')
                            $leaves['slpending']++;
                        if($value['status'] == 'approved')
                            $leaves['slapproved']++;
                        if($value['status'] == 'refused')
                            $leaves['slrefused']++;
                    }
                    if($value['type'] == 'maternity'){
                        $leaves['mtcount']++;
                        if($value['status'] == 'inprocess')
                            $leaves['mtpending']++;
                        if($value['status'] == 'approved')
                            $leaves['mtapproved']++;
                        if($value['status'] == 'refused')
                            $leaves['mtrefused']++;
                    }
                    if($value['type'] == 'rdv'){
                        $leaves['rdvcount']++;
                        if($value['status'] == 'inprocess')
                            $leaves['rdvpending']++;
                        if($value['status'] == 'approved')
                            $leaves['rdvapproved']++;
                        if($value['status'] == 'refused')
                            $leaves['rdvrefused']++;
                    }
                }
                elseif($start != $end)
                {
                   // $days = dateDiff($start, $end);
                    $days = $this->createDateRangeArray($start, $end);
                    $year = date('Y', strtotime($start));
                    $allholidays = $this->allPublicWeekendsHolidays($year);
                    for($i=0; $i<=count($days); $i++)
                    {
                        if(!in_array($days[$i], $allholidays))
                        {
                            if($value['type'] == 'vacation'){
                                $leaves['plcount']++;
                                if($value['status'] == 'inprocess')
                                    $leaves['plpending']++;
                                if($value['status'] == 'approved')
                                    $leaves['plapproved']++;
                                if($value['status'] == 'refused')
                                    $leaves['plrefused']++;
                            }
                            if($value['type'] == 'sick'){
                                $leaves['slcount']++;
                                if($value['status'] == 'inprocess')
                                    $leaves['slpending']++;
                                if($value['status'] == 'approved')
                                    $leaves['slapproved']++;
                                if($value['status'] == 'refused')
                                    $leaves['slrefused']++;
                            }
                            if($value['type'] == 'maternity'){
                                $leaves['mtcount']++;
                                if($value['status'] == 'inprocess')
                                    $leaves['mtpending']++;
                                if($value['status'] == 'approved')
                                    $leaves['mtapproved']++;
                                if($value['status'] == 'refused')
                                    $leaves['mtrefused']++;
                            }
                            if($value['type'] == 'rdv'){
                                $leaves['rdvcount']++;
                                if($value['status'] == 'inprocess')
                                    $leaves['rdvpending']++;
                                if($value['status'] == 'approved')
                                    $leaves['rdvapproved']++;
                                if($value['status'] == 'refused')
                                    $leaves['rdvrefused']++;
                            }
                        }
                    }
                }*/
            }
        }
        return $leaves;
    }

    ////geting the number of days between the 2 dates////
    function dateDiff($start, $end)
    {
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400);
    }

    function createDateRangeArray($strDateFrom, $strDateTo)
    {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.
        $aryRange = array();
        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));
        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }

    ///get any arears of PL's only from the previous year to add or detect from current year leave balance///
    public function lastYearleaveBalance($userId)
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $leavebalance = $hrmleaves_obj->lastYearPaidLeaveBalance($userId);
        return $leavebalance[0]['lastyearplcount'];
    }
    /// list of the public holidays  ////
    public function publicHolidaysAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $user_obj = new Ep_User_User();
        $userdetails = $user_obj->getAllUsersDetails($this->adminLogin->userId);
        $publicholidays = $hrmleaves_obj->getLeaves(null, '1', 'public', null); //print_r($publicholidays);

        if ($userdetails[0]['country'] == 38)
            $country = 'fr';
        if ($userdetails[0]['country'] == 53)
            $country = 'ind';
        if ($userdetails[0]['country'] == 153)
            $country = 'uk';
        if ($publicholidays != 'NO') {
            if ($userdetails[0]['groupId'] != 1) {
                foreach ($publicholidays AS $key => $value) {
                    if ($country == $publicholidays[$key]['country']) {
                        $publicholidaysres[$key]['leave'] = $publicholidays[$key]['leave'];
                        $publicholidaysres[$key]['start'] = $publicholidays[$key]['start'];
                        $publicholidaysres[$key]['mandatory'] = $publicholidays[$key]['mandatory'];
                        $publicholidaysres[$key]['country'] = $publicholidays[$key]['country'];
                        $publicholidaysres[$key]['id'] = $publicholidays[$key]['id'];
                    }
                }
                $this->_view->publicholidays = $publicholidaysres;
            } else {
                foreach ($publicholidays AS $key => $value) {
                    $publicholidaysres[$key]['leave'] = $publicholidays[$key]['leave'];
                    $publicholidaysres[$key]['start'] = $publicholidays[$key]['start'];
                    $publicholidaysres[$key]['mandatory'] = $publicholidays[$key]['mandatory'];
                    $publicholidaysres[$key]['country'] = $publicholidays[$key]['country'];
                    $publicholidaysres[$key]['id'] = $publicholidays[$key]['id'];
                }
                $this->_view->publicholidays = $publicholidaysres;
            }
        }
        $this->_view->render("hrm_publicholidays");
    }
    /// when user apply for leave  ////
    public function applyLeavesAction()
    {
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getBoUsersList();
        $this->_view->userList = $users;
        $params = $this->_request->getParams();
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $hrmcompoff_obj = new Ep_Hrm_HrmCompOff();
        $userId = $this->adminLogin->userId;
        $comps = 0;
        $userholidays = $hrmleaves_obj->getcompoffs($userId); //print_r($userholidays);
        if ($userholidays != 'NO') {
            foreach ($userholidays as $key => $value) {
                $days = explode(',', $value['date_details']);
                for ($i = 0; $i < count($days); $i++) {
                    $daydetail[$i] = explode('|', $days[$i]);
                    if ($daydetail[$i][1] == 'full')
                        $comps += 1;
                    else if ($daydetail[$i][1] == 'half')
                        $comps += 0.5;
                }
            }
        }
        $this->_view->compofflimit = $comps;
        // $publicholidays = $this->allPublicHolidays(date('Y'));
        $appliedholidays = $this->userLeaves($userId); //print_r($appliedholidays);exit;
        $userdetails = $user_obj->getAllUsersDetails($userId);
        if ($userdetails[0]['country'] == 38)
            $country = 'fr';
        if ($userdetails[0]['country'] == 53)
            $country = 'ind';
        if ($userdetails[0]['country'] == 153)
            $country = 'uk';

       // echo $country; exit;
        $this->_view->usercountry = $country;

        $this->_view->hrmsicktotal = $this->configval["hrm_sick_".$country];
        $this->_view->hrmvacationtotal = $this->configval["hrm_vacation_".$country];//2days
        $this->_view->hrmmaternaltotal = $this->configval["hrm_maternal_".$country];//2days
        $compoffs   = $this->getCompOffCount($userId);
        $this->_view->hrmcompofftotal = $compoffs;

        $this->_view->statistics = $appliedholidays;
        $this->_view->actmode = 'add';
        if ($params['mode'] == 'edit') {
            $leavedetials = $hrmleaves_obj->getLeaves($params['leaveId'], null, null, null);
            $this->_view->actmode = 'edit';
            $this->_view->leavedetials = $leavedetials;
            $this->_view->starttime = date('g:i A', strtotime($leavedetials[0]['start']));
            $this->_view->endtime = date('g:i A',strtotime( $leavedetials[0]['end']));
            $this->_view->invitees_array = explode(',', $leavedetials[0]['rdv_invitees']);
        }
        $this->_view->render("hrm_applyleave");
    }
    // ajax functionility for the ///
    public function applyLeavesAjaxAction()
    {
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getBoUsersList();
        $this->_view->userList = $users;
        $params = $this->_request->getParams();
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $userId = $this->adminLogin->userId;
        // $publicholidays = $this->allPublicHolidays(date('Y'));
        $userdetails = $user_obj->getAllUsersDetails($userId);

        $this->_view->usercountry = $userdetails[0]['country'];
        $appliedholidays = $this->userLeaves($userId);
        $this->_view->statistics = $appliedholidays;
        $this->_view->actmode = 'add';
        if ($params['mode'] == 'edit') {
            $leavedetials = $hrmleaves_obj->getLeaves($params['leaveId'], null, null, null);
            $this->_view->start = date("d-m-Y", strtotime($leavedetials[0]['start']));
            $this->_view->end = date("d-m-Y", strtotime($leavedetials[0]['end']));
            //$this->_view->datedetails = json_encode($leavedetials[0]['date_details']);
            // $datedetials =  rtrim($params['datedetails']);
            $datedet = explode(",", rtrim($leavedetials[0]['date_details'], ','));
            foreach ($datedet AS $keys => $value) {
                $detailsdates = explode("|", $datedet[$keys]);
                $dates[$keys] = $detailsdates[1];
            }
            $this->_view->actmode = 'edit';
            $this->_view->datedetails = implode(',', $dates);
            $resultarr['startdate'] = $this->_view->start;
            $resultarr['enddate'] = $this->_view->end;
            $resultarr['datedetails'] = $this->_view->datedetails;
            $resultarr['type'] = $leavedetials[0]['type'];
            echo json_encode($resultarr);
            exit;
        }

    }
    //// displaying the public holidays in validations holidays ////
    public function showPublicHolidaysAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $user_obj = new Ep_User_User();
        $userdetails = $user_obj->getAllUsersDetails($this->adminLogin->userId);
        $publicholidays = $hrmleaves_obj->getLeaves(null, '1', 'public', null);
        $evecount = count($publicholidays);
        if ($userdetails[0]['country'] == 38)
            $country = 'fr';
        if ($userdetails[0]['country'] == 53)
            $country = 'ind';
        if ($userdetails[0]['country'] == 153)
            $country = 'uk';
        if ($publicholidays != 'NO') {
            $events = array();
            for ($i = 0; $i < $evecount; $i++) {
                $e = array();
                if($userdetails[0]['groupId'] != 1) {
                    if ($country == $publicholidays[$i]['country'] && $userdetails[0]['groupId'] != 1) {
                        $e['id'] = $publicholidays[$i]['id'];
                        $e['title'] = $publicholidays[$i]['leave'];
                        $e['start'] = $publicholidays[$i]['start'];
                        $e['end'] = $publicholidays[$i]['end'];
                        if ($publicholidays[$i]['allday'] == 'false')
                            $e['allDay'] = false;
                        else
                            $e['allDay'] = true;
                        $e['color'] = '#F2090D';
                        $e['textColor'] = '#FFFFFF';
                        array_push($events, $e);
                    }
                }else{
                    $e['id'] = $publicholidays[$i]['id'];
                    $e['title'] = $publicholidays[$i]['leave'];
                    $e['start'] = $publicholidays[$i]['start'];
                    $e['end'] = $publicholidays[$i]['end'];
                    if ($publicholidays[$i]['allday'] == 'false')
                        $e['allDay'] = false;
                    else
                        $e['allDay'] = true;
                    $e['color'] = '#F2090D';
                    $e['textColor'] = '#FFFFFF';
                    array_push($events, $e);
                }
            }
        }
        echo json_encode($events);
        exit;
        // $this->_view->render("hrm_publicholidays");
    }
    /// grid to display the leaves applied ///
    public function showAppliedLeavesAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $userId = $this->adminLogin->userId;
        $appliedholidays = $hrmleaves_obj->getAppliedLeaves($userId);
        $usersdetials = $hrmleaves_obj->getAllBoUsers();

        if ($appliedholidays != 'NO') {
            $evecount = count($appliedholidays);
            $leaves = array();
            for ($i = 0; $i < $evecount; $i++) {
                $e = array();
                $e['id'] = $appliedholidays[$i]['id'];
                $e['title'] = $appliedholidays[$i]['leave'];
                $e['start'] = $appliedholidays[$i]['start'];
                $e['end'] = $appliedholidays[$i]['end'];
                $e['status'] = $appliedholidays[$i]['status'];
                $e['usergroup'] = $this->adminLogin->groupId ;
                $e['leavetype'] = $appliedholidays[$i]['type'];
                if ($appliedholidays[$i]['allday'] == 'false')
                    $e['allDay'] = false;
                else
                    $e['allDay'] = true;
                if ($appliedholidays[$i]['type'] == 'public') {
                    $e['color'] = '#F2090D';
                    $e['textColor'] = '#FFFFFF';
                } else if ($appliedholidays[$i]['type'] == 'vacation')
                    $e['color'] = '#f5aa1a';
                else if ($appliedholidays[$i]['type'] == 'sick')
                    $e['color'] = '#70a415';
                else if ($appliedholidays[$i]['type'] == 'compoff')
                    $e['color'] = '#D4F3FC';
                else if ($appliedholidays[$i]['type'] == 'rdv')
                {
                    $e['color'] = '#BCBCBC';
                    $e['allDay'] = true;
                    $e['title'] = $appliedholidays[$i]['leave']." (".date('g:i a', strtotime($appliedholidays[$i]['start']))."-".date('g:i a', strtotime($appliedholidays[$i]['end'])).")";
                }
                else if ($appliedholidays[$i]['type'] == 'maternity')
                    $e['color'] = '#41A1BF';
                if ($appliedholidays[$i]['status'] == 'refused')
                    $e['borderColor'] = '#D10202';
                else if ($appliedholidays[$i]['status'] == 'approved') {
                     if ($appliedholidays[$i]['type'] != 'compoff'){
                        $e['textColor'] = '#FFFFFF'; }
                    $e['borderColor'] = '#131313';
                }
                ///get the user details in tooltip/
                $username = $usersdetials[$appliedholidays[$i]['in_charge']][0];
                if ($appliedholidays[$i]['type'] == 'public') {
                    $e['tooltipcont'] = "<div><strong>" . $appliedholidays[$i]['leave'] . "</strong></div>";
                }
                if ($appliedholidays[$i]['type'] == 'vacation' || $appliedholidays[$i]['type'] == 'sick' || $appliedholidays[$i]['type'] == 'maternity' || $appliedholidays[$i]['type'] == 'compoff') {
                    $e['tooltipcont'] = "<div><strong>" . $appliedholidays[$i]['leave'] . "</strong>
                                     <br/>Incharge : $username<br/>
                                     Status : " . $appliedholidays[$i]['status'] . "<br/>
                                     </div>";
                }
                if ($appliedholidays[$i]['type'] == 'rdv') {
                    $e['tooltipcont'] = "<div><strong>" . $appliedholidays[$i]['leave'] . "</strong>
                                     <br/>Incharge : " . $username . "<br/>
                                     Place : " . $appliedholidays[$i]['meeting_place'] . "<br/>
                                     Status : " . $appliedholidays[$i]['status'] . "<br/>
                                     Reasons : " . $appliedholidays[$i]['rdv_reasons'] . "<br/>
                                     </div>";
                }
                ///get the user details in tooltip/
                array_push($leaves, $e);
            }
            echo json_encode($leaves);
            exit;
        }

        // $this->_view->render("hrm_publicholidays");
    }
    /// applying the public holidays ///
    public function savePublicHolidayAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $params = $this->_request->getParams();
        if ($params['edit'] == 'edit') {  //print_r($params); exit;
            $date = str_replace('/', '-', $params['create_at']);
            $date = date('Y-m-d', strtotime($date));
            $leave = $params["leave_title"];
            $country = $params["country"];
            $mandatory = $params["mandatory"];
            $data_leave = array("leave" => $leave, "start" => $date, "end" => $date, "country" => $country, "mandatory" => $mandatory);
            $query_leave = "id= '" . $params['holidayId'] . "'";
            $hrmleaves_obj->updateHrmLeaves($data_leave, $query_leave);
            $this->_redirect('/hrm/public-holidays?submenuId=ML12-SL1');
        }
        if ($params['save'] == 'save') {
            //print_r($params); exit;
            for ($i = 0; $i < count($params["leave_title"]); $i++) {
                $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
                $date[$i] = str_replace('/', '-', $params['create_at'][$i]);
                $date[$i] = date('Y-m-d', strtotime($date[$i]));
                $pholidayid[$i] = $this->genIdentifier();
                $hrmleaves_obj->id = $pholidayid[$i];
                $hrmleaves_obj->leave = $params["leave_title"][$i];
                $hrmleaves_obj->start = $date[$i];
                $hrmleaves_obj->end = $date[$i];
                $hrmleaves_obj->allday = 'allday';
                $hrmleaves_obj->created_by = '1';
                $hrmleaves_obj->country = $params["country"][$i];
                $hrmleaves_obj->type = 'public';
                $hrmleaves_obj->status = 'inprocess';
                $hrmleaves_obj->mandatory = $params["mandatory"][$i];

                $hrmleaves_obj->insert();
            }
                $this->_redirect('/hrm/public-holidays?submenuId=ML12-SL1');

        }
        if ($params['edit'] == 'show') {
            //$publicleaves = $hrmleaves_obj->publicHolidays(date("Y"));
            $leaveId = $params['holidayId'];
            $publicleaves = $hrmleaves_obj->getLeaves($leaveId, '1', 'public', null);
            $this->_view->publicleaves = $publicleaves;
            $this->_view->editholiday = 'yes';
            $this->_view->render("hrm_leavepopup");
        }
        if ($params['add'] == 'show') {
            $this->_view->editholiday = 'yes';
            $this->_view->addholiday = 'yes';
            $this->_view->render("hrm_leavepopup");
        }
    }
    /// validating the public Holidays ////
    public function validatePublicHolidayAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $params = $this->_request->getParams();
        if($params['singleedit'] == 'yes')
        {
            $country = array();
            $dates = array();
            $country[] = $params['country'];
            $dates[] = $params['create_at'];
        }else{
            $country = $params['country'];
            $dates = $params['create_at'];
        }
        //print_r($country); print_r($dates);
        $publicleaves = $hrmleaves_obj->publicHolidaysValidation(date('Y')); //print_r($publicleaves); exit;
        for($j=0; $j<count($publicleaves); $j++)
        {
            $country1[$j] = $publicleaves[$j]['country'];
            $dates1[$j] = date('d/m/Y', strtotime($publicleaves[$j]['start']));
        }
        $countries = array_merge($country, $country1);// print_r($countries);
        $dates = array_merge($dates, $dates1); //print_r($dates); exit;
        $res = array();
        $res = array_map(null, $countries, $dates);

        for ($i = 0; $i < count($res); $i++) {
            if ($res[$i][0] == 'ind')
                $arrind[$i] = $res[$i][1];
            if ($res[$i][0] == 'fr')
                $arrfr[$i] = $res[$i][1];
            if ($res[$i][0] == 'uk')
                $arruk[$i] = $res[$i][1];
        }
        /////get the public holiday limit//
        $indlimit = $this->configval["publicholidays_ind_limit"];
        $frlimit = $this->configval["publicholidays_fr_limit"];
        $uklimit = $this->configval["publicholidays_uk_limit"];
        $limitexceed = '';
        if($indlimit < count($arrind))
            $limitexceed = 'India';
        if($frlimit < count($arrfr))
            $limitexceed = 'France';
        if($uklimit < count($arruk))
            $limitexceed = 'UK';

        $ind_repeat = max(array_count_values($arrind));
        $fr_repeat =  max(array_count_values($arrfr));
        $uk_repeat = max(array_count_values($arruk));
        if ($ind_repeat > 1) {
            $redandentdate = array_search($ind_repeat, array_count_values($arrind));
        } elseif ($fr_repeat  > 1){
            $redandentdate = array_search($fr_repeat, array_count_values($arrfr));
        } elseif ($uk_repeat > 1){
            $redandentdate = array_search($uk_repeat, array_count_values($arruk));
        }else{
            $redandentdate = 'no';
        }
        if($redandentdate != 'no')
            $highlight = array_search($redandentdate, $dates);
        else
            $highlight = 'no';
        $country=''; $dates='';$country[]=''; $dates[]='';
        $result[0] = $highlight;   $result[1] = $redandentdate; $result[2] = $limitexceed;
        echo json_encode($result);
        exit;

    }
    public function validateeditPublicHolidayAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $params = $this->_request->getParams();
        if($params['singleedit'] == 'yes')
        {
            $country = array();
            $dates = array();
            $country[] = $params['country'];
            $dates[] = $params['create_at'];
            //print_r($country); print_r($dates);
            $publicleaves = $hrmleaves_obj->publicHolidaysValidation(date('Y')); //print_r($publicleaves); exit;
            for($j=0; $j<count($publicleaves); $j++)
            {
                if($publicleaves[$j]['id'] != $params['holidayId']) {
                    $country1[$j] = $publicleaves[$j]['country'];
                    $dates1[$j] = date('d/m/Y', strtotime($publicleaves[$j]['start']));
                }
            }
        }else{
            $country = $params['country'];
            $dates = $params['create_at'];
            $publicleaves = $hrmleaves_obj->publicHolidaysValidation(date('Y')); //print_r($publicleaves); exit;
            for($j=0; $j<count($publicleaves); $j++)
            {
                if($publicleaves[$j]['id'] != $params[$j]['holidayId']) {
                    $country1[$j] = $publicleaves[$j]['country'];
                    $dates1[$j] = date('d/m/Y', strtotime($publicleaves[$j]['start']));
                }
            }
        }
        $countries = array_merge($country, $country1);// print_r($countries);
        $dates = array_merge($dates, $dates1); //print_r($dates); exit;
        $res = array();
        $res = array_map(null, $countries, $dates);

        for ($i = 0; $i < count($res); $i++) {
            if ($res[$i][0] == 'ind')
                $arrind[$i] = $res[$i][1];
            if ($res[$i][0] == 'fr')
                $arrfr[$i] = $res[$i][1];
            if ($res[$i][0] == 'uk')
                $arruk[$i] = $res[$i][1];
        }
        /////get the public holiday limit//
        $indlimit = $this->configval["publicholidays_ind_limit"];
        $frlimit = $this->configval["publicholidays_fr_limit"];
        $uklimit = $this->configval["publicholidays_uk_limit"];
        $limitexceed = '';
        if($indlimit < count($arrind))
            $limitexceed = 'India';
        if($frlimit < count($arrfr))
            $limitexceed = 'France';
        if($uklimit < count($arruk))
            $limitexceed = 'UK';

        $ind_repeat = max(array_count_values($arrind));
        $fr_repeat =  max(array_count_values($arrfr));
        $uk_repeat = max(array_count_values($arruk));
        if ($ind_repeat > 1) {
            $redandentdate = array_search($ind_repeat, array_count_values($arrind));
        } elseif ($fr_repeat  > 1){
            $redandentdate = array_search($fr_repeat, array_count_values($arrfr));
        } elseif ($uk_repeat > 1){
            $redandentdate = array_search($uk_repeat, array_count_values($arruk));
        }else{
            $redandentdate = 'no';
        }
        if($redandentdate != 'no')
            $highlight = array_search($redandentdate, $dates);
        else
            $highlight = 'no';
        $country=''; $dates='';$country[]=''; $dates[]='';
        $result[0] = $highlight;   $result[1] = $redandentdate; $result[2] = $limitexceed;
        echo json_encode($result);
        exit;

    }
    /// delete the public holiday  only by super admin///
    public function deletePublicHolidayAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $params = $this->_request->getParams();
        $leavelist = explode('|', $params['leavelist']) ;
        if ($params['delete'] == 'yes')
        {  //print_r($leavelist); exit;
            for($i=0; $i<count($leavelist); $i++)
            {
                $hrmleaves_obj->deleteHrmLeaves($leavelist[$i]);
            }
            $this->_redirect('/hrm/public-holidays?submenuId=ML12-SL1');
        }
    }
    ///////////////active or inactive the comp off////////////////
    public function changecompoffstatusAction()
    {
        $params=$this->_request->getParams();
        $compoff_obj = new Ep_Hrm_HrmCompOff();
        $data = array("status"=>$params['status']);
        $query = "id= '".$params['compoff_id']."'";
        $compoff_obj->updateHrmCompOff($data,$query);

    }
    ///  functionality to edit public holidays in bulk only by super admin//
    public function bulkeditPublicHolidayAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $params = $this->_request->getParams(); //print_r($params); exit;

        if ($params['edit'] == 'edit')
        {  //print_r($params); exit;
            for($i=0; $i<count($params['leave_title']); $i++)
            {
                $date[$i] = str_replace('/', '-', $params['create_at'][$i]);
                $date[$i] = date('Y-m-d', strtotime($date[$i]));
                $hrmleaves_obj->id = $params["id"][$i];
                $leave = $params["leave_title"][$i];
                $data_leave = array("leave" => $leave, "start"=>$date[$i], "end"=>$date[$i], "country"=>$params["country"][$i], "mandatory"=>$params["mandatory"][$i]);
                $query_leave = "id= '" . $hrmleaves_obj->id . "'";
               // print_r($data_leave); exit;
                $hrmleaves_obj->updateHrmLeaves($data_leave, $query_leave);
            }
            $this->_redirect('/hrm/public-holidays?submenuId=ML12-SL1');
        }
        $leavelist = explode('|', $params['leavelist']) ;
        for($i=0; $i<count($leavelist); $i++)
        {
            $publicleaves[$i] = $hrmleaves_obj->getLeaves($leavelist[$i], '1', 'public', null);
        }
       //print_r($publicleaves); exit;
        $this->_view->editpublicleaves = $publicleaves;
        $this->_view->bulkedit = 'yes';
        $this->_view->render("hrm_leavepopup");


    }
    /// upload the public holidays via csv file //
    public function uploadartcsvAction()
    {
        //print_r($_FILES);exit;
        $user_params=$this->_request->getParams();
       /*if($user_params['download'] = 'downloadcsv'){
            $this->_redirect("/BO/download_samplecsv.php");
            exit;
        }*/
        $realfilename=$_FILES['uploadfile']['name'];
       // $ext=$this->findexts($realfilename);
        $ext = 'csv';
        $uploaddir = '/home/sites/site6/web/BO/holiday_csv/';
        $newfilename=$realfilename.".".$ext;
        $file = $uploaddir . basename($realfilename,".".$ext)."_".uniqid().".".$ext;
        if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file))
        {
            $main_array=array();
            $sub_array=array();
            $row = 1;
            if (($handle = fopen($file, "r")) !== FALSE) {
               while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                   $num = count($data);
                    $row++;

                    for ($c=0; $c < $num; $c++) {
                        //echo $data[$c];
                        //$arr=explode(";",$data[$c]);
                        array_push($sub_array,$data[$c]);
                    }
                    array_push($main_array,$sub_array);
                }//exit;
                fclose($handle);
            }
            $sub_array = array_filter($sub_array);
            unlink($file);
            echo implode('*', $sub_array);exit;

        }
        else
        {
            echo "error";
        }
    }
    // to download file for public holidays///
    public function downloadsamplecsvAction()
    {
        $user_params=$this->_request->getParams();
        $request_id = $user_params['request_id'];
        $zipname = explode("/",$user_params['filename']);
        // set example variables
        $filename = $zipname[1];
        $filepath = $request_id."-".$filename;

        $this->_redirect("/BO/download_samplecsv.php");

    }
    ///saving the leave when user applied for leave////
    public function saveLeaveAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $public_params = $this->_request->getParams(); //print_r($public_params); exit;
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getUsers();
        $this->_view->userList = $users;
        if ($public_params['save'] == 'yes') {
            $srt = explode("GMT", $public_params["start"]);
            $start = date('Y-m-d H:i:s', strtotime($srt[0]));
            $ed = explode("GMT", $public_params["end"]);
            $end = date('Y-m-d H:i:s', strtotime($ed[0]));
            $hrmleaves_obj->leave = $public_params["title"];
            $hrmleaves_obj->start = $start;
            $hrmleaves_obj->end = $end;
            $hrmleaves_obj->allday = $public_params["allDay"];
            $hrmleaves_obj->created_by = $this->adminLogin->userId;
            $hrmleaves_obj->country = 'ind';
            $hrmleaves_obj->type = $public_params["type"];
            $hrmleaves_obj->status = 'inprocess';
            $hrmleaves_obj->description = 'no description';
            $hrmleaves_obj->in_charge = $public_params["requestto"];
            $hrmleaves_obj->rdv_invitees = $public_params["invitees"];
            $hrmleaves_obj->meeting_place = $public_params["place"];
            $hrmleaves_obj->rdv_reasons = $public_params["reasons"];
            if ($hrmleaves_obj->insert()) {
                $user_obj = new Ep_User_User();
                $userdetial = $user_obj->getAllUsersDetails($this->adminLogin->userId);
                $username = $userdetial[0]['first_name'] . " " . $userdetial[0]['last_name'];
                $managerdetials = $user_obj->getAllUsersDetails($public_params["requestto"]);
                $managername = $managerdetials[0]['first_name'] . " " . $managerdetials[0]['last_name'];
                $parameters['bo_user'] = $username;
                $parameters['incharge_manager'] = $managername;
                $parameters['validate_leave'] = "/hrm/leave-validation?submenuId=ML12-SL3";
                $autoEmails = new Ep_Message_AutoEmails();
                $autoEmails->messageToEPMail($public_params["requestto"], 130, $parameters);///
            } else
                echo "action failed";
        }
        $this->_view->myleave = 'yes';
        $this->_view->render("hrm_leavepopup");
    }

    ///saving the leave when user applied for leave////
    public function saveMyLeaveAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $public_params = $this->_request->getParams(); //print_r($public_params); exit;
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getUsers();
        $this->_view->userList = $users;
        $userId = $this->adminLogin->userId;
        $comps = 0;
        $userholidays = $hrmleaves_obj->getcompoffs($userId); //print_r($userholidays);
        if ($userholidays != 'NO') {
            foreach ($userholidays as $key => $value) {
                $days = explode(',', $value['date_details']);
                for ($i = 0; $i < count($days); $i++) {
                    $daydetail[$i] = explode('|', $days[$i]);
                    if ($daydetail[$i][1] == 'full')
                        $comps += 1;
                    else if ($daydetail[$i][1] == 'half')
                        $comps += 0.5;
                }
            }
        }
        if ($public_params['savevalue'] == 'yes') {   //print_r($public_params); exit;
            $daycount = 0;
            $datesarr = $public_params['date'];
            $halfdays = $public_params['halfday'];
            $complimit = $this->getCompOffCount($userId)-$comps;
            $dates = '';
            $vacdates = '';
            if($public_params['leave_type'] != 'compoff'){
                for ($i = 0; $i < count($datesarr); $i++) {
                    if ($public_params['halfday_' . $i] == 'on')
                        $dates .= $datesarr[$i] . "|half,";
                    else
                        $dates .= $datesarr[$i] . "|full,";
                }
            }else{
                for ($i = 0; $i < count($datesarr); $i++) {
                    if ($public_params['halfday_' . $i] == 'on')
                    {
                        if($complimit > $daycount){
                            $dates .= $datesarr[$i] . "|half,";
                            $datesarrc[] = $datesarr[$i];
                            $daycount += 0.5;
                        }else{
                            $vacdates .= $datesarr[$i] . "|half,";
                            $vacdatesc[] = $datesarr[$i];
                            $daycount += 0.5;
                        }
                    }else
                    {
                        if($complimit > $daycount)
                        {
                            $diff = $complimit - $daycount;
                            if($diff == 0.5)
                            {
                                $dates .= $datesarr[$i] . "|half,";
                                $datesarrc[] = $datesarr[$i];
                                $vacdates .= $datesarr[$i] . "|half,";
                                $vacdatesc[] = $datesarr[$i];
                                $daycount += 1;
                            }else{
                                $dates .= $datesarr[$i] . "|full,";
                                $datesarrc[] = $datesarr[$i];
                                $daycount += 1;
                            }
                        }else{
                            $vacdates .= $datesarr[$i] . "|full,";
                            $vacdatesc[] = $datesarr[$i];
                            $daycount += 1;
                        }
                    }
                }
            }
            /*echo "jiii".$dates; echo "<br>".$vacdates; echo "<br>"; print_r($datesarrc);  echo "<br>";  print_r($vacdatesc);
            $lastdate = array_pop($datesarrc);echo "<br>"; print_r($lastdate);
            echo $vacstart = date('Y-m-d H:i:s', strtotime($lastdate));exit;*/
            // echo $dates; exit;
            if ($public_params['leave_type'] == 'rdv') {
                $combined_rdvsrt = $public_params['rdvstartdate'] . ' ' . $public_params['rdvtimestart'];
                $start = date('Y-m-d H:i:s', strtotime($combined_rdvsrt));
                $combined_rdvend = $public_params['rdvenddate'] . ' ' . $public_params['rdvtimeend'];
                $end = date('Y-m-d H:i:s', strtotime($combined_rdvend));
                $allday = 'false';
            } else {
                $srt = $public_params["startdate"];
                $start = date('Y-m-d H:i:s', strtotime($srt));
                if($public_params["enddate"] == null || $public_params["enddate"] == '') {
                    $ed = $public_params["startdate"];
                    $end = date('Y-m-d H:i:s', strtotime($ed));
                }
                else{
                    $ed = $public_params["enddate"];
                    $end = date('Y-m-d H:i:s', strtotime($ed));
                }

                $allday = 'true';
            }
            if ($public_params['leave_type'] == 'compoff') {
                if ($vacdates != '') {     ////when compoff selected more than limit, remaining limit to be inseted as vacation////
                    $lastdate = $vacdatesc[0];
                    $vacstart = date('Y-m-d H:i:s', strtotime($lastdate));
                    $hrmleaves_obj->leave = $public_params["leave_title"];
                    $hrmleaves_obj->start = $vacstart;
                    $hrmleaves_obj->end = $end;
                    $hrmleaves_obj->allday = $allday;
                    $hrmleaves_obj->created_by = $this->adminLogin->userId;
                    $hrmleaves_obj->country = $public_params["country"];
                    $hrmleaves_obj->type = 'vacation';
                    $hrmleaves_obj->status = 'inprocess';
                    $hrmleaves_obj->description = 'no description';
                    $hrmleaves_obj->in_charge = $public_params["requestto"];
                    $hrmleaves_obj->rdv_invitees = implode(',', $public_params["invite_user"]);
                    $hrmleaves_obj->meeting_place = $public_params["meeting_place"];
                    $hrmleaves_obj->rdv_reasons = $public_params["meeting_reason"];
                    $hrmleaves_obj->date_details = $vacdates;
                    $hrmleaves_obj->insert();
                }
                $hrmleaves_obj = new Ep_Hrm_HrmLeaves();

                if($vacdates != '') {
                    $lastdatec = array_pop($datesarrc);
                    $lastendc = date('Y-m-d H:i:s', strtotime($lastdatec));
                    $hrmleaves_obj->end = $lastendc;
                }else
                    $hrmleaves_obj->end = $end;
            }
           //echo $dates; echo "hello"; exit;
            $hrmleaves_obj->leave = $public_params["leave_title"];
            $hrmleaves_obj->start = $start;
            if ($public_params['leave_type'] != 'compoff')
                $hrmleaves_obj->end = $end;

            $hrmleaves_obj->allday = $allday;
            $hrmleaves_obj->created_by = $this->adminLogin->userId;
            $hrmleaves_obj->country = $public_params["country"];
            $hrmleaves_obj->type = $public_params["leave_type"];
            $hrmleaves_obj->status = 'inprocess';
            $hrmleaves_obj->description = 'no description';
            $hrmleaves_obj->in_charge = $public_params["requestto"];
            $hrmleaves_obj->rdv_invitees = implode(',', $public_params["invite_user"]);
            $hrmleaves_obj->meeting_place = $public_params["meeting_place"];
            $hrmleaves_obj->rdv_reasons = $public_params["meeting_reason"];
            $hrmleaves_obj->date_details = $dates;
            if ($hrmleaves_obj->insert()) {
                $user_obj = new Ep_User_User();
                $userdetial = $user_obj->getAllUsersDetails($this->adminLogin->userId);
                $username = $userdetial[0]['first_name'] . " " . $userdetial[0]['last_name'];
                $managerdetials = $user_obj->getAllUsersDetails($public_params["requestto"]);
                $managername = $managerdetials[0]['first_name'] . " " . $managerdetials[0]['last_name'];
                $parameters['bo_user'] = $username;
                $parameters['incharge_manager'] = $managername;
                $parameters['validate_leave'] = "/hrm/leave-validation?submenuId=ML12-SL3";
                $autoEmails = new Ep_Message_AutoEmails();
                $autoEmails->messageToEPMail($public_params["requestto"], 130, $parameters);///

                $this->_redirect('/hrm/apply-leaves?submenuId=ML12-SL2');
            } else
                echo "action failed";
        }
        if ($public_params['editvalue'] == 'yes') {
            $incharge = $hrmleaves_obj->getLeaves($public_params["leaveid"],null,null,null);

            $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
            $daycount = 0;
            $datesarr = $public_params['date']; // print_r($datesarr); exit;
            $halfdays = $public_params['halfday'];

            if($public_params['leave_type'] != 'compoff'){
                $dates = '';
                for ($i = 0; $i < count($datesarr); $i++) {
                    if ($public_params['halfday_' . $i] == 'on')
                        $dates .= $datesarr[$i] . "|half,";
                    else
                        $dates .= $datesarr[$i] . "|full,";
                }
            }else{
                $hrmleaves_obj->deleteHrmLeaves($public_params["leaveid"]);

                $comps = 0;
                $userholidays = $hrmleaves_obj->getcompoffs($userId); //print_r($userholidays);
                if ($userholidays != 'NO') {
                    foreach ($userholidays as $key => $value) {
                        $days = explode(',', $value['date_details']);
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $comps += 1;
                            else if ($daydetail[$i][1] == 'half')
                                $comps += 0.5;
                        }
                    }
                }
                $complimit = $this->getCompOffCount($userId)-$comps; // echo $complimit; exit;
                $dates = '';
                $vacdates = '';
                for ($i = 0; $i < count($datesarr); $i++) {
                    if ($public_params['halfday_' . $i] == 'on')
                    {
                        if($complimit > $daycount){
                            $dates .= $datesarr[$i] . "|half,";
                            $datesarrc[] = $datesarr[$i];
                            $daycount += 0.5;
                        }else{
                            $vacdates .= $datesarr[$i] . "|half,";
                            $vacdatesc[] = $datesarr[$i];
                            $daycount += 0.5;
                        }
                    }else
                    {
                        if($complimit > $daycount )
                        {
                            $diff = $complimit - $daycount;
                            if($diff == 0.5)
                            {
                                $dates .= $datesarr[$i] . "|half,";
                                $datesarrc[] = $datesarr[$i];
                                $vacdates .= $datesarr[$i] . "|half,";
                                $vacdatesc[] = $datesarr[$i];
                                $daycount += 1;
                            }else{
                                $dates .= $datesarr[$i] . "|full,";
                                $datesarrc[] = $datesarr[$i];
                                $daycount += 1;
                            }
                        }else{
                            $vacdates .= $datesarr[$i] . "|full,";
                            $vacdatesc[] = $datesarr[$i];
                            $daycount += 1;
                        }
                    }
                }
            }

           /*echo "jjj".$dates; echo "<br>".$vacdates; echo "<br>";
            $lastdate = array_pop($datesarrc);echo "<br>"; print_r($lastdate);
            echo $vacstart = date('Y-m-d H:i:s', strtotime($lastdate));   exit;*/
            // echo $dates; exit;
            if ($public_params['leave_type'] == 'rdv') {
                $combined_rdvsrt = $public_params['rdvstartdate'] . ' ' . $public_params['rdvtimestart'];
                $start = date('Y-m-d H:i:s', strtotime($combined_rdvsrt));
                $combined_rdvend = $public_params['rdvenddate'] . ' ' . $public_params['rdvtimeend'];
                $end = date('Y-m-d H:i:s', strtotime($combined_rdvend));
                $allday = 'false';
            } else {
                $srt = $public_params["startdate"];
                $start = date('Y-m-d H:i:s', strtotime($srt));
                $ed = $public_params["enddate"];
                $end = date('Y-m-d H:i:s', strtotime($ed));
                $allday = 'true';
            }// echo $allday;  exit;
            ////inserting the vacation ///
            if ($public_params['leave_type'] == 'compoff') {
                if ($vacdates != '') {     ////when compoff selected more than limit, remaining limit to be inseted as vacation////
                    $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
                    $lastdate = $vacdatesc[0];
                    $vacstart = date('Y-m-d H:i:s', strtotime($lastdate));
                    $hrmleaves_obj->leave = $public_params["leave_title"];
                    $hrmleaves_obj->start = $vacstart;
                    $hrmleaves_obj->end = $end;
                    $hrmleaves_obj->allday = $allday;
                    $hrmleaves_obj->created_by = $this->adminLogin->userId;
                    $hrmleaves_obj->country = $public_params["country"];
                    $hrmleaves_obj->type = 'vacation';
                    $hrmleaves_obj->status = 'inprocess';
                    $hrmleaves_obj->description = 'no description';
                    $hrmleaves_obj->in_charge = $public_params["requestto"];
                    $hrmleaves_obj->rdv_invitees = implode(',', $public_params["invite_user"]);
                    $hrmleaves_obj->meeting_place = $public_params["meeting_place"];
                    $hrmleaves_obj->rdv_reasons = $public_params["meeting_reason"];
                    $hrmleaves_obj->date_details = $vacdates;
                    $hrmleaves_obj->insert();
                }
                if ($vacdates != ''){
                    $lastdatec = array_pop($datesarrc);
                    $lastendc = date('Y-m-d H:i:s', strtotime($lastdatec));
                    $end = $lastendc;
                }
                else
                    $end = $end;
                //////inserting when compoff////
                $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
                $hrmleaves_obj->leave = $public_params["leave_title"];
                $hrmleaves_obj->start = $start;
                $hrmleaves_obj->end = $end;

                $hrmleaves_obj->allday = $allday;
                $hrmleaves_obj->created_by = $this->adminLogin->userId;
                $hrmleaves_obj->country = $public_params["country"];
                $hrmleaves_obj->type = $public_params["leave_type"];
                $hrmleaves_obj->status = 'inprocess';
                $hrmleaves_obj->description = 'no description';
                $hrmleaves_obj->in_charge = $public_params["requestto"];
                $hrmleaves_obj->rdv_invitees = implode(',', $public_params["invite_user"]);
                $hrmleaves_obj->meeting_place = $public_params["meeting_place"];
                $hrmleaves_obj->rdv_reasons = $public_params["meeting_reason"];
                $hrmleaves_obj->date_details = $dates;
                $hrmleaves_obj->insert();
            }else{  //echo $start; echo $end;
                $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
                ///get incharge user of leave///
                $data_leave = array("start" => $start, "end" => $end, "allday" => $allday, "country" => $public_params["country"], "type" => $public_params["leave_type"],
                    "rdv_invitees" => implode(',', $public_params["invite_user"]), "meeting_place" => $public_params["meeting_place"], "rdv_reasons" => $public_params["meeting_reason"],
                    "leave" => $public_params["leave_title"], "in_charge" => $public_params["requestto"], "date_details" => $dates);
                $query_leave = "id= '" . $public_params["leaveid"] . "'";
                $hrmleaves_obj->updateHrmLeaves($data_leave, $query_leave);
            }
            ///sending the mail to new incharge when user changed it in edit//
            if($incharge[0]['in_charge'] == $public_params["requestto"])
                $mailid = 147;
            else
                $mailid = 130;
            $user_obj = new Ep_User_User();
            $userdetial = $user_obj->getAllUsersDetails($this->adminLogin->userId);
            $username = $userdetial[0]['first_name'] . " " . $userdetial[0]['last_name'];
            $managerdetials = $user_obj->getAllUsersDetails($public_params["requestto"]);
            $managername = $managerdetials[0]['first_name'] . " " . $managerdetials[0]['last_name'];
            $parameters['bo_user'] = $username;
            $parameters['incharge_manager'] = $managername;
            $parameters['validate_leave'] = "/hrm/leave-validation?submenuId=ML12-SL3";
            $autoEmails = new Ep_Message_AutoEmails();
            $autoEmails->messageToEPMail($public_params["requestto"], $mailid, $parameters);///

            $this->_redirect('/hrm/apply-leaves?submenuId=ML12-SL2');

        }
        $this->_view->myleave = 'yes';
        //  $this->_view->render("hrm_leavepopup");
    }

    ///saving the manager's decision accept or refuse////
    public function approveRefuseLeavesAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $public_params = $this->_request->getParams();
        $selleave = $hrmleaves_obj->getLeaves($public_params['id'], null, null, null);
        if ($public_params['accept'] == 'yes' && $public_params['refuse'] == 'no') {
            $status = 'approved';
            $Message = $public_params['acceptmail'];
            $mailid = 133;
        } else {
            $status = 'refused';
            $Message = $public_params['refusemail'];
            $mailid = 131;
        }
        $data_leave = array("status" => $status);
        $query_leave = "id= '" . $public_params['id'] . "'";
        $hrmleaves_obj->updateHrmLeaves($data_leave, $query_leave);
        //////sending mail to contributor who got selected in profile selections///////////////
        $automail = new Ep_Message_AutoEmails();
        $email = $automail->getAutoEmail($mailid);//
        $Object = $email[0]['Object'];
        $receiverId = $selleave[0]['created_by'];
        $automail->sendMailEpMailBox($receiverId, $Object, $Message);

        // $this->_view->render("hrm_publicholidays");
    }
    /// edit the public holidays //
    public function editPublicHolidayAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $public_params = $this->_request->getParams();

        $hrmleaves_obj->id = $public_params["id"];
        $hrmleaves_obj->leave = $public_params["title"];
        $data_leave = array("leave" => $hrmleaves_obj->leave);
        $query_leave = "id= '" . $hrmleaves_obj->id . "'";
        $hrmleaves_obj->updateHrmLeaves($data_leave, $query_leave);

        // $this->_view->render("hrm_publicholidays");
    }
    // to display the leave details in popup //
    public function leavePopUpAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $params = $this->_request->getParams();
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getUsers();
        $this->_view->userList = $users;
        if ($params['leaveId'] != 'no') {
            $selleave = $hrmleaves_obj->getLeaves($params['leaveId'], null, null, null);
            $this->_view->leavedetails = $selleave;
            $this->_view->render("hrm_leavepopup");
        } else
            $this->_view->render("hrm_leavepopup");
    }
    // rdv(meeting) detials //
    public function rdvDetailsAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $params = $this->_request->getParams();
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getUsers();
        $this->_view->userList = $users;

        $selleave = $hrmleaves_obj->getLeaves($params['leaveId'], null, null, null);
        $invitees = explode(',', $selleave[0]['rdv_invitees']);
        $selleaves = '';
        for ($i = 0; $i < count($invitees); $i++) {
            $userdetails[$i] = $user_obj->getUsername($invitees[$i]);// print_r($userdetails); exit;
            if ($i + 1 != count($invitees))
                $selleaves .= $userdetails[$i] . ",";
            else
                $selleaves .= $userdetails[$i];
        }
        // print_r($selleaves); exit;
        $selleave[0]['rdv_invitees'] = $selleaves;
        $this->_view->leavedetails = $selleave;
        $this->_view->render("hrm_rdvdetails");

    }

    /// get the manager's view of applied leaves///
    public function leaveValidationAction()
    {
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getUsers(); //var_dump($users); exit;
        $this->_view->userList = $users;
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $userId = $this->adminLogin->userId;
        $appliedholidays = $hrmleaves_obj->getValidateStatistics($userId);

        $stats['rdvapprovalpending'] = $appliedholidays[0]['rdvapprovalpending'];
        $stats['sickleaves'] = $appliedholidays[1]['sickleaves'];
        $stats['vacations'] = $appliedholidays[2]['vacations'];
        $stats['maternityleaves'] = $appliedholidays[3]['maternityleaves'];
        $stats['rdvbreaks'] = $appliedholidays[4]['rdvbreaks'];
        $stats['slapproved'] = $appliedholidays[5]['slapproved'];
        $stats['slrejected'] = $appliedholidays[6]['slrejected'];
        $stats['slapprovalpending'] = $appliedholidays[7]['slapprovalpending'];
        $stats['plapproved'] = $appliedholidays[8]['plapproved'];
        $stats['plrejected'] = $appliedholidays[9]['plrejected'];
        $stats['plapprovalpending'] = $appliedholidays[10]['plapprovalpending'];
        $stats['mlapproved'] = $appliedholidays[11]['mlapproved'];
        $stats['mlrejected'] = $appliedholidays[12]['mlrejected'];
        $stats['mlapprovalpending'] = $appliedholidays[13]['mlapprovalpending'];
        $stats['compoffapproved'] = $appliedholidays[14]['compoffapproved'];
        $stats['compoffrejected'] = $appliedholidays[15]['compoffrejected'];
        $stats['compoffapprovalpending'] = $appliedholidays[16]['compoffapprovalpending'];
        $this->_view->statistics = $stats;
        $this->_view->render("hrm_leavevalidation");
    }

    /// get the manager's view of applied leaves///
    public function leaveValidationOLDAction()
    {
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getUsers();
        $this->_view->userList = $users;
        $userId = $this->adminLogin->userId;
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $userholidays = $hrmleaves_obj->getManagersPendingLeaves($userId);
        $leaves['plapprovalpending'] = 0;
        $leaves['slapprovalpending'] = 0;
        $leaves['mlapprovalpending'] = 0;
        $leaves['rdvapprovalpending'] = 0;
        if ($userholidays != 'NO') {
            foreach ($userholidays as $key => $value) {
                $start = date('Y-m-d', strtotime($value['start']));
                $end = date('Y-m-d', strtotime($value['end']));
                if ($value['type'] == 'vacation') {
                    $value['date_details'] = rtrim($value['date_details'], ",");
                    $days = explode(',', $value['date_details']);
                    if ($value['status'] == 'inprocess') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['plapprovalpending'] += 1;
                            else
                                $leaves['plapprovalpending'] += 0.5;
                        }
                    }
                }
                if ($value['type'] == 'sick') {
                    $value['date_details'] = rtrim($value['date_details'], ",");
                    $days = explode(',', $value['date_details']); //print_r();
                    if ($value['status'] == 'inprocess') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['slapprovalpending'] += 1;
                            else
                                $leaves['slapprovalpending'] += 0.5;
                        }
                    }
                }
                if ($value['type'] == 'maternity') {
                    $days = explode(',', $value['date_details']);
                    if ($value['status'] == 'inprocess') {
                        for ($i = 0; $i < count($days); $i++) {
                            $daydetail[$i] = explode('|', $days[$i]);
                            if ($daydetail[$i][1] == 'full')
                                $leaves['mlapprovalpending'] += 1;
                            else
                                $leaves['mlapprovalpending'] += 0.5;
                        }
                    }
                }
                if ($value['type'] == 'rdv') {
                    $leaves['rdvcount']++;
                    if ($value['status'] == 'inprocess')
                        $leaves['rdvapprovalpending']++;
                }
            }
        }
        $this->_view->statistics = $leaves;
        $this->_view->render("hrm_leavevalidation");
    }
    // approved by the manger, all kinds of leaves and rdv//
    public function leavesToBeApprovedAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $userId = $this->adminLogin->userId;
        $appliedholidays = $hrmleaves_obj->getValidateLeaves($userId);
        $usersdetials = $hrmleaves_obj->getAllBoUsers();
        $evecount = count($appliedholidays);
        if ($appliedholidays != 'NO') {
            $leaves = array();
            for ($i = 0; $i < $evecount; $i++) {
                $e = array();
                $e['id'] = $appliedholidays[$i]['id'];
                $e['title'] = $appliedholidays[$i]['leave'];
                $e['start'] = $appliedholidays[$i]['start'];
                $e['end'] = $appliedholidays[$i]['end'];
                $e['status'] = $appliedholidays[$i]['status'];
                if ($appliedholidays[$i]['allday'] == 'false')
                    $e['allDay'] = false;
                else
                    $e['allDay'] = true;

                if ($appliedholidays[$i]['status'] == 'approved') {
                    $e['textColor'] = '#FFFFFF';
                    $e['borderColor'] = '#131313';
                    // $e['className'] = 'inactiveLink';
                } else if ($appliedholidays[$i]['status'] == 'refused') {
                    $e['borderColor'] = '#E57E7E';
                    if ($appliedholidays[$i]['type'] != 'compoff')
                    $e['textColor'] = '#FFFFFF';
                    //$e['className'] = 'inactiveLink';
                } else if ($appliedholidays[$i]['status'] == 'inprocess')
                    $e['color'] = '#D3D3D3';
                if ($appliedholidays[$i]['type'] == 'public')
                    $e['color'] = '#FF9B44';
                else if ($appliedholidays[$i]['type'] == 'vacation')
                    $e['color'] = '#f5aa1a';
                else if ($appliedholidays[$i]['type'] == 'sick')
                    $e['color'] = '#70a415';
                else if ($appliedholidays[$i]['type'] == 'rdv')
                    $e['color'] = '#058dc7';
                else if ($appliedholidays[$i]['type'] == 'maternity')
                    $e['color'] = '#3EE8B2';
                else if ($appliedholidays[$i]['type'] == 'compoff')
                    $e['color'] = '#D4F3FC';
                ///get the user details in tooltip/
                $username = $usersdetials[$appliedholidays[$i]['created_by']][0];
                $sickleaves = $usersdetials[$appliedholidays[$i]['created_by']][1];
                $vacations = $usersdetials[$appliedholidays[$i]['created_by']][2];
                $maternityleaves = $usersdetials[$appliedholidays[$i]['created_by']][3];
                $rdvbreaks = $usersdetials[$appliedholidays[$i]['created_by']][4];
                $meetingplace = $appliedholidays[$i]['meeting_place'];
                $reason = $appliedholidays[$i]['rdv_reasons'];
                if ($appliedholidays[$i]['type'] == 'rdv') {
                    $e['tooltipcont'] = "<div><strong>" . $appliedholidays[$i]['leave'] . "</strong>
                                     <br/>$username<br/>
                                   Place :$meetingplace<br/>
                                   Reason :$reason<br/>
                                   Status :" . $appliedholidays[$i]['status'] . "<br/>
                                   </div>";
                } else {
                    $e['tooltipcont'] = "<div><strong>" . $appliedholidays[$i]['leave'] . "</strong>
                                         <br/>$username<br/>
                                       vacances :$vacations<br/>
                                       Maladie :$sickleaves<br/>
                                       Maternite :$maternityleaves<br/>
                                       RDV :$rdvbreaks<br/>
                                       Status :" . $appliedholidays[$i]['status'] . "<br/>
                                       </div>";
                }
                ///get the user details in tooltip/

                array_push($leaves, $e);
            }
        }
        echo json_encode($leaves);
        exit;
    }

    ////validate like accept and refuse by manager///
    public function validLeaveAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $public_params = $this->_request->getParams();
        $srt = explode("GMT", $public_params["start"]);
        $start = date('Y-m-d H:i:s', strtotime($srt[0]));
        $ed = explode("GMT", $public_params["end"]);
        $end = date('Y-m-d H:i:s', strtotime($ed[0]));
        $hrmleaves_obj->leave = $public_params["title"];
        $hrmleaves_obj->start = $start;
        $hrmleaves_obj->end = $end;
        $hrmleaves_obj->allday = $public_params["allDay"];
        $hrmleaves_obj->created_by = $this->adminLogin->userId;
        $hrmleaves_obj->country = 'ind';
        $hrmleaves_obj->type = $public_params["type"];
        $hrmleaves_obj->status = 'inprocess';
        $hrmleaves_obj->description = 'no description';
        $hrmleaves_obj->in_charge = $public_params["requestto"];
        if ($hrmleaves_obj->insert())
            echo "saved sucessfully";
        else
            echo "action failed";
        // $this->_view->render("hrm_publicholidays");
    }

    /////getting the mail content for accept and refuse the leave request////
    public function getvalidmailpopupAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $params = $this->_request->getParams();
        $leaveId = $params['leaveId'];

        $leavedetails = $hrmleaves_obj->getLeaves($leaveId, null, null, null);

        $user_obj = new Ep_User_User();
        $userdetial = $user_obj->getAllUsersDetails($leavedetails[0]['created_by']);
        $username = $userdetial[0]['first_name'] . " " . $userdetial[0]['last_name'];
        $managerdetials = $user_obj->getAllUsersDetails($leavedetails[0]['in_charge']);
        $managername = $managerdetials[0]['first_name'] . " " . $managerdetials[0]['last_name'];
        $parameters['bo_user'] = $username;
        $parameters['incharge_manager'] = $managername;
        $parameters['validate_leave'] = "/hrm/leave-validation?submenuId=ML12-SL3";
        $parameters['date_range'] = date('d/m/Y', strtotime($leavedetails[0]['start'])) . " to " . date('d/m/Y', strtotime($leavedetails[0]['end']));
        $autoEmails = new Ep_Message_AutoEmails();

        if ($params['type'] == 'accept')
            $mailid = 131;
        else
            $mailid = 133;

        $email = $autoEmails->getMailComments(NULL, $mailid, $parameters);
        echo $emailComments = utf8_encode(stripslashes(html_entity_decode($email)));
        exit;
    }

    /////getting the mail content for accept and refuse the leave request////
    public function removeeventAction()
    {
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $params = $this->_request->getParams();
        $leaveId = $params['leaveId'];
        $hrmleaves_obj->deleteHrmLeaves($leaveId);
    }
    /// this page is for user to apply leaves and info about already applied leaves //
    public function userleavesAction()
    {
        ////getting users////////
        $params = $this->_request->getParams();
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $user_obj = new Ep_User_User();
        $userId = $params['userid'];
        if ($userId) {
            $userleaves = $hrmleaves_obj->getUserLeaves($userId);
            if ($userleaves != 'NO') {
                for ($i = 0; $i < count($userleaves); $i++) {
                    $days = 0;
                    $datedetials = rtrim($userleaves[$i]['date_details'], ",");
                    $dayscount = explode(",", $datedetials); //echo $userleaves[$i]['date_details']; print_r($dayscount);
                    //echo  count($dayscount);
                    for ($j = 0; $j < count($dayscount); $j++) {
                        $daydetail[$j] = explode('|', $dayscount[$j]);
                        if ($daydetail[$j][1] == 'full')
                            $days += 1;
                        else
                            $days += 0.5;
                    }
                    $userleaves[$i]['days'] = $days;
                    $userleaves[$i]['start'] = $userleaves[$i]['start'];
                    $userleaves[$i]['end'] = $userleaves[$i]['end'];
                    $userleaves[$i]['type'] = $userleaves[$i]['type'];
                    $userleaves[$i]['status'] = $userleaves[$i]['status'];
                    $userleaves[$i]['leave'] = $userleaves[$i]['leave'];
                }
            }
            $this->_view->userleaves = $userleaves;
            $this->_view->usergridview = "yes";
            $this->_view->gridview = "no";
        } else {
            $userleaves = $hrmleaves_obj->inprocessLeavesList($this->adminLogin->userId);
            if ($userleaves != 'NO') {
                for ($i = 0; $i < count($userleaves); $i++) {
                    $days = 0;
                    $datedetials = rtrim($userleaves[$i]['date_details'], ",");
                    $dayscount = explode(",", $datedetials); //echo $userleaves[$i]['date_details']; print_r($dayscount);
                    //echo  count($dayscount);
                    for ($j = 0; $j < count($dayscount); $j++) {
                        $daydetail[$j] = explode('|', $dayscount[$j]);
                        if ($daydetail[$j][1] == 'full')
                            $days += 1;
                        else
                            $days += 0.5;
                    }
                    $users = $user_obj->getAllUsersDetails($userleaves[$i]['created_by']);
                    $userleaves[$i]['days'] = $days;
                    $userleaves[$i]['start'] = $userleaves[$i]['start'];
                    $userleaves[$i]['end'] = $userleaves[$i]['end'];
                    $userleaves[$i]['type'] = $userleaves[$i]['type'];
                    $userleaves[$i]['status'] = $userleaves[$i]['status'];
                    $userleaves[$i]['leave'] = $userleaves[$i]['leave'];
                    $userleaves[$i]['username'] = $users[0]['first_name']." ".$users[0]['last_name'];
                }
            }
            $this->_view->userleaves = $userleaves;
            $this->_view->gridview = "yes";
            $this->_view->usergridview = "no";
        }
        $this->_view->render("hrm_leavepopup");
    }
    // display the detials of the leaves appliend ...
    public function leaveDetailsAction()
    {
        ////getting users////////
        $params = $this->_request->getParams();
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $userId = $this->adminLogin->userId;
        $type = $params['type'];
        $status = $params['status'];
        $userleaves = $hrmleaves_obj->getLeaveDetails($userId, $type, $status);
        if ($userleaves != 'NO') {
            for ($i = 0; $i < count($userleaves); $i++) {
                if($type != 'rdv'){
                    $days = 0;
                    $datedetials = rtrim($userleaves[$i]['date_details'], ",");
                    $dayscount = explode(",", $datedetials); //echo $userleaves[$i]['date_details']; print_r($dayscount);
                    //echo  count($dayscount);
                    for ($j = 0; $j < count($dayscount); $j++) {
                        $daydetail[$j] = explode('|', $dayscount[$j]);
                        if ($daydetail[$j][1] == 'full')
                            $days += 1;
                        else
                            $days += 0.5;
                    }
                    $userleaves[$i]['days'] = $days;
                }else{
                    $starttime = date('g:i A', strtotime($userleaves[$i]['start']));
                    $endtime = date('g:i A', strtotime($userleaves[$i]['end']));
                    $userleaves[$i]['days'] = $starttime." - ".$endtime;
                }

            }
        }
        // print_r($userleaves);
        if($type == 'rdv')
            $this->_view->popname = 'rdv';
        else
            $this->_view->popname = 'leave';
        $this->_view->userleaves = $userleaves;
        $this->_view->leavedetails = "yes";
        $this->_view->render("hrm_leavepopup");
    }
    // to find the count difference between the 2 days ///
    public function datesinbetweenAction()
    {
        $params = $this->_request->getParams();
        $userId = $this->adminLogin->userId;
        $userplus_obj = new Ep_User_UserPlus();
        $userdetials = $userplus_obj->getUsersDetailsOnId($userId); //print_r($leavedetials); exit;
        if ($userdetials[0]['country'] == 38)
            $usercountry = 'fr';
        elseif ($userdetials[0]['country'] == 53)
            $usercountry = 'ind';
        else
            $usercountry = 'uk';
        $allholidays = array();
        $date1 = $params['start'];
        $date2 = $params['end'];
        $day = 60 * 60 * 24;
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        $days_diff = round(($date2 - $date1) / $day); // Unix time difference devided by 1 day to get total days in between

        $dates_array = array();
        $dates_array[] = date('Y-m-d', $date1);
        if($days_diff != 0) {
            for ($x = 1; $x < $days_diff; $x++) {
                $dates_array[] = date('Y-m-d', ($date1 + ($day * $x)));
            }
        }else{
            $dates_array[] = date('Y-m-d', ($date1 + ($day * 0)));
        }
        $dates_array[] = date('Y-m-d', $date2);
        $startyear = date('Y', $date1);
        $endyear = date('Y', $date2);
        if($startyear == $endyear)
            $cyear[0] = $startyear;
        else{
            $cyear[0] = $startyear;
            $cyear[1] = $endyear;
        }

        $allholidays = $this->allPublicWeekendsHolidays($cyear, $usercountry);// print_r($allholidays);  exit;
        if($allholidays == null)
            $allholidays = array();
        $final_array = array_diff($dates_array, $allholidays);
        $final_array = array_values($final_array);
        $final_array = array_unique($final_array);
        foreach ($final_array as $key => $val) {
            $final_array1[$key] = date('d-m-Y', strtotime($val));
        }
       // print_r($final_array1);
        $errors = array_filter($final_array1);
        if (!empty($errors)) {
            echo json_encode($final_array1);
        }else {
            echo "empty";
        }
        exit;

    }
    ///in case of compoffs ////
    public function datesinbetweencompoffAction()
    {
        $params = $this->_request->getParams();
        $userId = $this->adminLogin->userId;
        $userplus_obj = new Ep_User_UserPlus();
        $userdetials = $userplus_obj->getUsersDetailsOnId($userId); //print_r($leavedetials); exit;
        if ($userdetials[0]['country'] == 38)
            $usercountry = 'fr';
        elseif ($userdetials[0]['country'] == 53)
            $usercountry = 'ind';
        else
            $usercountry = 'uk';
        $allholidays = array();
        $date1 = $params['start'];
        $date2 = $params['end'];
        $day = 60 * 60 * 24;
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        $days_diff = round(($date2 - $date1) / $day); // Unix time difference devided by 1 day to get total days in between

        $dates_array = array();
        $dates_array[] = date('Y-m-d', $date1);
        if($days_diff != 0) {
            for ($x = 1; $x < $days_diff; $x++) {
                $dates_array[] = date('Y-m-d', ($date1 + ($day * $x)));
            }
        }else{
            $dates_array[] = date('Y-m-d', ($date1 + ($day * 0)));
        }
        $dates_array[] = date('Y-m-d', $date2);
        $startyear = date('Y', $date1);
        $endyear = date('Y', $date2);
        if($startyear == $endyear)
            $cyear[0] = $startyear;
        else{
            $cyear[0] = $startyear;
            $cyear[1] = $endyear;
        }

        $allholidays = array();
        $final_array = array_diff($dates_array, $allholidays);
        $final_array = array_values($final_array);
        $final_array = array_unique($final_array);
        foreach ($final_array as $key => $val) {
            $final_array1[$key] = date('d-m-Y', strtotime($val));
        }
        // print_r($final_array1);
        $errors = array_filter($final_array1);
        if (!empty($errors)) {
            echo json_encode($final_array1);
        }else {
            echo "empty";
        }
        exit;

    }
    //valite the compoff by manager///
    public function validateCompoffAction()
    {
        $hrmcompoff_obj = new Ep_Hrm_HrmCompOff();
        $params = $this->_request->getParams();
        $userId = $this->adminLogin->userId;
        $userplus_obj = new Ep_User_UserPlus();
        $year = date('Y', strtotime($params['start']));
        $compoffs   = $hrmcompoff_obj->getNotCompensatedCompOffs($userId, $year);
        $compofflectcount = count($compoffs);
        $userdetials = $userplus_obj->getUsersDetailsOnId($userId); //print_r($leavedetials); exit;
        $date1 = $params['start'];
        $date2 = $params['end'];
        $day = 60 * 60 * 24;
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
       echo  $days_diff = round(($date2 - $date1) / $day); // Unix time difference devided by 1 day to get total days in between
        if($days_diff <= $compofflectcount)
            echo "pass";
        else
            echo "fail";

        exit;
    }
    // leave statistics of all the user ///////
    public function hrmStatsAction()
    {
        ////getting users////////
        $params = $this->_request->getParams();
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $userplus_obj = new Ep_User_UserPlus();
        $userId = $this->adminLogin->userId;
        if ($params['view'] == 'show') {
            $userleaves = $hrmleaves_obj->getUserStatsLeaves($params['userid']);
            if ($userleaves != 'NO') {
                for ($i = 0; $i < count($userleaves); $i++) {
                    $days = $this->leavesInDayCount($userleaves[$i]['date_details']);
                    $userleaves[$i]['leave'] = $userleaves[$i]['leave'];
                    $userleaves[$i]['type'] = $userleaves[$i]['type'];
                    $userleaves[$i]['start'] = $userleaves[$i]['start'];
                    $userleaves[$i]['end'] = $userleaves[$i]['end'];
                    $userleaves[$i]['days'] = $days;
                }
            }
            // print_r($userleaves); exit;
            $this->_view->viewdetials = 'yes';
            $this->_view->userleavesdetials = $userleaves;
        } else {
            //$userleaves = $hrmleaves_obj->getLeaveDetails($userId, $type, $status);
            $userlist = $hrmleaves_obj->getUsersLeaveStats();
            foreach ($userlist as $k => $v) {
                $userdetials = $userplus_obj->getUsersDetailsOnId($userlist[$k]['identifier']); //print_r($leavedetials); exit;
                if ($userdetials[0]['country'] == 38)
                    $usercountry = 'fr';
                elseif ($userdetials[0]['country'] == 53)
                    $usercountry = 'ind';
                else
                    $usercountry = 'uk';
                $sickleaves = $hrmleaves_obj->getLeaveDetails($userlist[$k]['identifier'], 'sick', 'approved'); //print_r($sickleaves); exit;
                if ($sickleaves != 'NO') {
                    for ($i = 0; $i < count($sickleaves); $i++) {
                        $days = $this->leavesInDayCount($sickleaves[$i]['date_details']);
                        $userleaves[$k]['username'] = $userlist[$k]['first_name'] . " " . $userlist[$k]['last_name'];
                        $userleaves[$k]['userid'] = $userlist[$k]['identifier'];
                        $userleaves[$k]['slapproved'] += $days;
                        $userleaves[$k]['slavailable'] = $this->configval["hrm_sick_" . $usercountry] - $days;
                        $userleaves[$k]['sltotal'] = $this->configval["hrm_sick_" . $usercountry];
                    }
                } else {
                    $userleaves[$k]['username'] = $userlist[$k]['first_name'] . " " . $userlist[$k]['last_name'];
                    $userleaves[$k]['userid'] = $userlist[$k]['identifier'];
                    $userleaves[$k]['slapproved'] += 0;
                    $userleaves[$k]['slavailable'] = $this->configval["hrm_sick_" . $usercountry];   /// 12 is total sick leaves //
                    $userleaves[$k]['sltotal'] = $this->configval["hrm_sick_" . $usercountry];
                }
                $plleaves = $hrmleaves_obj->getLeaveDetails($userlist[$k]['identifier'], 'vacation', 'approved');
                if ($plleaves != 'NO') {
                    for ($i = 0; $i < count($plleaves); $i++) {
                        $days = $this->leavesInDayCount($plleaves[$i]['date_details']);
                        $userleaves[$k]['plapproved'] += $days;
                        $userleaves[$k]['plavailable'] = $this->configval["hrm_vacation_" . $usercountry] - $days; /// 15 is total paid leaves //
                        $userleaves[$k]['pltotal'] = $this->configval["hrm_vacation_" . $usercountry];
                    }
                } else {
                    $userleaves[$k]['plapproved'] += 0;
                    $userleaves[$k]['plavailable'] = $this->configval["hrm_vacation_" . $usercountry];   /// 12 is total sick leaves //
                    $userleaves[$k]['pltotal'] = $this->configval["hrm_vacation_" . $usercountry];
                }
                $plleaves = $hrmleaves_obj->getLeaveDetails($userlist[$k]['identifier'], 'compoff', 'approved');
                if ($plleaves != 'NO') {
                    for ($i = 0; $i < count($plleaves); $i++) {
                        $days = $this->leavesInDayCount($plleaves[$i]['date_details']);
                        $userleaves[$k]['compoffapproved'] += $days;
                        $userleaves[$k]['compoffavailable'] = $this->getCompOffCount($userlist[$k]['identifier']) - $days; /// 15 is total paid leaves //
                        $userleaves[$k]['compofftotal'] = $this->getCompOffCount($userlist[$k]['identifier']);
                    }
                } else {
                    $userleaves[$k]['compoffapproved'] += 0;
                    $userleaves[$k]['compoffavailable'] = $this->getCompOffCount($userlist[$k]['identifier']);   /// 12 is total sick leaves //
                    $userleaves[$k]['compofftotal'] = $this->getCompOffCount($userlist[$k]['identifier']);
                }
                $userleaves[$k]['countrycode'] = $usercountry;

                $counderlimit = 0; $cooverlimit = 0;
                if($userleaves[$k]['compoffapproved'] > $userleaves[$k]['compofftotal'])
                    $cooverlimit = $userleaves[$k]['compoffapproved'] - $userleaves[$k]['compofftotal'];
                else
                    $counderlimit =  $userleaves[$k]['compofftotal'] - $userleaves[$k]['compoffapproved'];

                $slunderlimit = 0; $sloverlimit = 0;
                if($userleaves[$k]['slapproved'] > $userleaves[$k]['sltotal'])
                    $sloverlimit = $userleaves[$k]['slapproved'] - $userleaves[$k]['sltotal'];
                else
                    $slunderlimit =  $userleaves[$k]['sltotal'] - $userleaves[$k]['slapproved'];

                $plunderlimit = 0; $ploverlimit = 0;
                if($userleaves[$k]['plapproved'] > $userleaves[$k]['pltotal'])
                    $ploverlimit = $userleaves[$k]['plapproved'] - $userleaves[$k]['pltotal'];
                else
                    $plunderlimit =  $userleaves[$k]['pltotal'] - $userleaves[$k]['plapproved'];


                $userleaves[$k]['lop'] = $cooverlimit + $sloverlimit + $ploverlimit;
                $userleaves[$k]['carryforward'] = $plunderlimit;
                /*$this->_view->hrmsicktotal = $this->configval["hrm_sick"];
                $this->_view->hrmvacationtotal = $this->configval["hrm_vacation"];
                $this->_view->hrmmaternaltotal = $this->configval["hrm_maternal"];*/
            }
            /* $this->_view->hrmsicktotal = $this->configval["hrm_sick"];//2days
             $this->_view->hrmvacationtotal = $this->configval["hrm_vacation"];//2days
             $this->_view->hrmmaternaltotal = $this->configval["hrm_maternal"];//2days*/
            $this->_view->userleaves = $userleaves;
        }
        $this->_view->render("hrm_hrmstats");
    }
    // day count between two dates //
    public function leavesInDayCount($datedetials)
    {
        $days = 0;
        $datedetials = rtrim($datedetials, ",");
        $dayscount = explode(",", $datedetials); //echo $userleaves[$i]['date_details']; print_r($dayscount);
        for ($j = 0; $j < count($dayscount); $j++) {
            $daydetail[$j] = explode('|', $dayscount[$j]);
            if ($daydetail[$j][1] == 'full')
                $days += 1;
            else
                $days += 0.5;
        }
        return $days;
    }

    /////validate the leaves count limit////
    public function leavelimitAction()
    {
        $params = $this->_request->getParams();
        $userplus_obj = new Ep_User_UserPlus();
        $hrmcompoff_obj = new Ep_Hrm_HrmCompOff();
        $hrmleaves_obj = new Ep_Hrm_HrmLeaves();
        $datesarr = explode(',',$params['date']);
        $appdates = count($datesarr);
        $halfdays = $params['halfday'] * 0.5;
        /*$appdates = '';
        for ($i = 0; $i < count($datesarr); $i++) {
            if ($params['halfday_' . $i] == 'on')
                $appdates += 0.5;
            else
                $appdates += 1;
        }*/
        $userId = $this->adminLogin->userId;
        $userdetials = $userplus_obj->getUsersDetailsOnId($userId);// print_r($userdetials); exit;
        if ($userdetials[0]['country'] == 38)
            $usercountry = 'fr';
        elseif ($userdetials[0]['country'] == 53)
            $usercountry = 'ind';
        else
            $usercountry = 'uk';
        $days = 0;
        $appliedleaves = $hrmleaves_obj->getLeaveDetails($userId, $params['ltype'], 'approved');
        if ($appliedleaves != 'NO') {
            for ($i = 0; $i < count($appliedleaves); $i++) {
                $days+= $this->leavesInDayCount($appliedleaves[$i]['date_details']);

            }
        }else{
            $days = 0;
        }

        $totalApplydays = $appdates + $days;

        if($params['ltype'] == 'sick')
            $leavelimit = $this->configval["hrm_sick_" . $usercountry];
        elseif($params['ltype'] == 'vacation')
            $leavelimit = $this->configval["hrm_vacation_" . $usercountry];
        elseif($params['ltype'] == 'maternal')
            $leavelimit = $this->configval["hrm_vacation_" . $usercountry];
        elseif($params['ltype'] == 'rdv') {
            $totalApplydays = 1;
            $leavelimit = 2;
        }
        elseif($params['ltype'] == 'compoff') {
            $appdates = $appdates - $halfdays;
            $totalApplydays = $appdates + $days;
            $compoffs   = $this->getCompOffCount($userId);
            $leavelimit = $compoffs;
            $totalApplydays;
            if ($totalApplydays > $leavelimit){
                echo  "compoffexceed";
                exit;
            }else{
                echo "inlimit";
                exit;
            }
        }
        if ($totalApplydays < $leavelimit)
            echo  "inlimit";
        else
            echo  "exceeded";

        exit;

    }
    // comp off count ///
    public function getCompOffCount($userId)
    {
        $hrmcompoff_obj = new Ep_Hrm_HrmCompOff();
        $totalcount = 0;
        $compoffs   = $hrmcompoff_obj->getNotCompensatedCompOffs($userId, date('Y')); //print_r($compoffs); echo count($compoffs); exit;
        for($j=0; $j<count($compoffs); $j++)
        {
            $days = explode(',', $compoffs[$j]['compdate_details']);
            $days = array_filter($days);
            for ($i = 0; $i < count($days); $i++) {
                $daydetail[$i] = explode('|', $days[$i]);
                if ($daydetail[$i][1] == 'full')
                    $totalcount += 1;
                else if ($daydetail[$i][1] == 'half')
                    $totalcount += 0.5;
            }
        }

        return $totalcount;
    }
    ////adding the comp off ////
    public function compOffAction()
    {
        $hrmcompoff_obj = new Ep_Hrm_HrmCompOff();
        $user_obj = new Ep_User_User();
        $public_params = $this->_request->getParams();
        $userId = $this->adminLogin->userId;
        $users = $user_obj->getBoUsersList();
        $this->_view->userList = $users;
        if($userId == '111113163826982')  // julien accout only//
        {
            $compoffs = $hrmcompoff_obj->getAppliedCompOffsCeoUser();// print_r($compoffs); exit;
            foreach($compoffs AS $key => $val)
            {
                $userdetails = $user_obj->getAllUsersDetails($compoffs[$key]['project_manager']);
                $compoffs[$key]['manager'] = $userdetails[0]['first_name']." ".$userdetails[0]['last_name'];
                $userdetails = $user_obj->getAllUsersDetails($compoffs[$key]['created_by']);
                $compoffs[$key]['requested_by'] = $userdetails[0]['first_name']." ".$userdetails[0]['last_name'];
            }
        }else{
            $compoffs = $hrmcompoff_obj->getAppliedCompOffs($userId);// print_r($compoffs); exit;
            foreach($compoffs AS $key => $val)
            {
                $userdetails = $user_obj->getAllUsersDetails($compoffs[$key]['project_manager']);
                $compoffs[$key]['manager'] = $userdetails[0]['first_name']." ".$userdetails[0]['last_name'];
            }
        }
        $this->_view->compoffs = $compoffs;
        if($public_params['save'] == 'save')
        {
            $datesarr = $public_params['date'];
            $halfdays = $public_params['halfday'];
            $dates = '';
            for ($i = 0; $i < count($datesarr); $i++) {
                if ($public_params['halfday_' . $i] == 'on')
                    $dates .= $datesarr[$i] . "|half,";
                else
                    $dates .= $datesarr[$i] . "|full,";
            }
            $srt = explode("GMT", $public_params["startdate"]);
            $start = date('Y-m-d H:i:s', strtotime($srt[0]));
            $ed = explode("GMT", $public_params["enddate"]);
            $end = date('Y-m-d H:i:s', strtotime($ed[0]));
            $hrmcompoff_obj->comp_off = $public_params["comp_title"];
            $hrmcompoff_obj->start = $start;
            $hrmcompoff_obj->end = $end;
            $hrmcompoff_obj->status = 'Inactive';
            $hrmcompoff_obj->project_manager = $public_params["requestto"];
            $hrmcompoff_obj->created_by = $this->adminLogin->userId;
            $hrmcompoff_obj->create_at = date("Y-m-d H:i:s");
            $hrmcompoff_obj->compdate_details = $dates;
            if ($hrmcompoff_obj->insert()) {
                $user_obj = new Ep_User_User();
                $userdetial = $user_obj->getAllUsersDetails($this->adminLogin->userId);
                $username = $userdetial[0]['first_name'] . " " . $userdetial[0]['last_name'];
                $managerdetials = $user_obj->getAllUsersDetails($public_params["requestto"]);
                $managername = $managerdetials[0]['first_name'] . " " . $managerdetials[0]['last_name'];
                $parameters['bo_user'] = $username;
                $parameters['incharge_manager'] = $managername;
                $parameters['validate_leave'] = "/hrm/comp-off?submenuId=ML12-SL8";
                $autoEmails = new Ep_Message_AutoEmails();
                $autoEmails->messageToEPMail('111113163826982', 149, $parameters);///
                $this->_redirect("/hrm/comp-off?submenuId=ML12-SL8");
            }else
                echo "action failed";
        }
        $this->_view->render("hrm_compoff");
    }
    // adding the expenses for bouser //
    public function myExpensesAction()
    {
        $expense_obj = new Ep_Hrm_Expenses();
        $exp_params = $this->_request->getParams();
        if ($exp_params["exp_create"] == 'save') {  //print_r($exp_params); exit;
            for ($i = 0; $i < count($exp_params["exp_name"]); $i++) {
                $batchId[$i] = $this->genIdentifier();
                $exp_date = date('Y-m-d H:i:s', strtotime($exp_params["exp_date"][$i]));
                $expid = $this->genIdentifier();
                $expense_obj->identifier = $expid;
                $expense_obj->expense_name = $exp_params["exp_name"][$i];
                $expense_obj->expense_amount = $exp_params["exp_amt"][$i];
                $expense_obj->expense_date = $exp_date;
                $expense_obj->expense_invoice = $exp_params["exp_invoice"][$i];
                $expense_obj->expense_currency = $exp_params["exp_currency"][$i];
                $expense_obj->expense_rib = $exp_params["exp_rib"];
                $expense_obj->batch_id = $batchId[$i];
                $expense_obj->created_by = $this->adminLogin->userId;
                $expense_obj->created_at = date('Y-m-d H:i:s');
                // print_r($expense_obj); exit;
                if ($expense_obj->insert()) {
                    $latest[$i] = $expense_obj->getLatestExpense();
                    $latestexpId[$i] = $latest[$i]['0']['identifier'];
                    if ($_FILES['exp_invoice']['tmp_name'][$i] != '') {
                        $tmpName = $_FILES['exp_invoice']['tmp_name'][$i];
                        $invoiceName = $_FILES['exp_invoice']['name'][$i];
                        $ext = explode('.', $invoiceName);
                        $extension = $ext[1];
                        $exp_path = $expid . "_" . rand(10000, 99999) . "." . $extension;
                        $data_exp[$i] = array("expense_invoice" => $exp_path);
                        $query_exp[$i] = "identifier= '" . $expid . "'";
                        $expense_obj->updateExpenses($data_exp[$i], $query_exp[$i]);
                        //////////////////uploading the document///////////////////////////////
                        $server_path = "/home/sites/site5/web/FO/expenses/" . $this->adminLogin->userId;
                        mkdir($server_path, 0777);

                        $expenseDir = $server_path . "/";
                        $newfile = $expenseDir . "/" . $exp_path;
                        move_uploaded_file($tmpName, $newfile);
                    }
                    $this->_helper->FlashMessenger('Expenses saved successfully');
                } else
                    $this->_helper->FlashMessenger('Process failed');
            }
            $this->_redirect("/hrm/my-expenses?submenuId=ML12-SL5");
        }
        $ribbouer_obj = new Ep_User_RibBoUser();
        $this->_view->allribs = $ribbouer_obj->getRidDetails($this->adminLogin->userId);
        $this->_view->render("hrm_myexpenses");
    }

    ///// hrm expenses payments master table////
    public function expensesGroupupAction()
    {
        ////getting users////////
        $params = $this->_request->getParams();
        $expense_obj = new Ep_Hrm_Expenses();
        $userId = $this->adminLogin->userId;
        $tobepaid = $expense_obj->getExpenses();

        $this->_view->payments = $tobepaid;
        $this->_view->render("hrm_expensespayments");
    }

    ///// hrm expenses payments master table////
    public function expensesPaymentsAction()
    {
        ////getting users////////
        $params = $this->_request->getParams();
        $expense_obj = new Ep_Hrm_Expenses();
        $userId = $this->adminLogin->userId;
        $tobepaid = $expense_obj->getExpenses();
        foreach ($tobepaid AS $key => $val) {
            $month[$key] = date('m', strtotime($tobepaid[$key]['expense_date']));
            $year[$key] = date('Y', strtotime($tobepaid[$key]['expense_date']));
            $expensesList = $expense_obj->getExpensesListToPay($tobepaid[$key]['created_by'], $month[$key], $year[$key], $tobepaid[$key]['expense_currency']);

            if ($expensesList != 'NO')
                $topay = 'yes';
            else
                $topay = 'no';
            $tobepaid[$key]['topay'] = $topay;
        }
        //print_r($tobepaid);exit;
        $this->_view->payments = $tobepaid;
        $this->_view->render("hrm_expensespayments");
    }

    ///// hrm expenses payments////
    public function expensesDetailsPopUpAction()
    {
        ////getting users////////
        $params = $this->_request->getParams();
        $expense_obj = new Ep_Hrm_Expenses();
        $userId = $this->adminLogin->userId;
        $tobepaid = $expense_obj->getExpensesDetails($params['userid'], $params['month'], $params['year'], $params['currency']);
        $this->_view->username = $tobepaid[0]['expenses_by'];
        $this->_view->useid = $params['userid'];
        $this->_view->month = $params['month'];
        $this->_view->year = $params['year'];
        $this->_view->currency = $params['currency'];
        $this->_view->payments = $tobepaid;
        $this->_view->render("hrm_expensepopup");
    }

    ///// hrm expenses payments////
    public function payExpenseAction()
    {
        ////getting users////////
        $params = $this->_request->getParams();
        $expense_obj = new Ep_Hrm_Expenses();
        $expensesList = $expense_obj->getExpensesListToPay($params['userid'], $params['month'], $params['year'], $params['currency']);
        for ($i = 0; $i < count($expensesList); $i++) {
            $data[$i] = array("status" => 'accepted');
            $query[$i] = "identifier = '" . $expensesList[$i]['identifier'] . "'";
            $expense_obj->updateExpenses($data[$i], $query[$i]);
        }
        $expdetials = $expense_obj->getExpensesDetails($params['userid'], $params['month'], $params['year'], $params['currency']);
        $parameters['user_name'] = $expdetials[0]['expenses_by'];
        $parameters['expense_pay_month'] = date("F", strtotime($expdetials[0]['expense_date']));
        $parameters['expense_amt'] = $expdetials[0]['expense_amount'];
        $autoEmails = new Ep_Message_AutoEmails();
        //  if($params['status'] == 'accepted')
        $mailid = 143;
        //  else
        //      $mailid = 144;
        //////sending mail to contributor who got selected in profile selections///////////////
        $autoEmails->messageToEPMail($expdetials[0]['created_by'], $mailid, $parameters);

        echo "done";
        /*$this->_redirect("/hrm/expenses-payments?submenuId=ML12-SL6");*/
    }

    ///// hrm expenses payments////
    public function acceptRefuseExpenseAction()
    {
        ////getting users////////
        $params = $this->_request->getParams();
        $expense_obj = new Ep_Hrm_Expenses();
        $data = array("status" => $params['status']);
        $query = "identifier= '" . $params['expenseId'] . "'";
        $expense_obj->updateExpenses($data, $query);
        $expdetials = $expense_obj->getExpenseDetials($params['expenseId']);
        $parameters['user_name'] = $expdetials[0]['expenses_by'];
        $parameters['expense_pay_month'] = date("F", strtotime($expdetials[0]['expense_date']));
        $parameters['expense_amt'] = $expdetials[0]['expense_amount'];
        $autoEmails = new Ep_Message_AutoEmails();
        if ($params['status'] == 'accepted')
            $mailid = 143;
        else
            $mailid = 144;
        //////sending mail to contributor who got selected in profile selections///////////////
        $autoEmails->messageToEPMail($expdetials[0]['created_by'], $mailid, $parameters);

        echo "done";
        /*$this->_redirect("/hrm/expenses-payments?submenuId=ML12-SL6");*/
    }

    public function genIdentifier()
    {
        //$d = new Date();
        //$this->identifier = $d->getSubDate(2,14).rand(100,999);
        return $identifier = number_format(microtime(true), 0, '', '') . mt_rand(10000, 99999);
    }

    public function defineHolidayAction()
    {
        $this->_view->render("hrm_defineholiday");
    }
    // to display the hierarchy of organisation and request via ajax ///
    public function organisationCharDataAction()
    {
        $user_obj = new EP_User_User();
        $grparr = array(1, 2);
        $params = $this->_request->getParams();
       /* if($params['parentId'] == 'no')
            $level1users = $user_obj->getUserDetailOnGrpIds($grparr);
        else*/
            $level1users = $user_obj->getChildUsers();
        if ($level1users != 'NO') {
             $level1count = count($level1users);
            for ($i = 0; $i < $level1count; $i++) {

                $levelusers[$i]['usrname'] = $level1users[$i]['first_name']." ".$level1users[$i]['last_name'];
                $levelusers[$i]['usrtype'] = $level1users[$i]['type'];
                $levelusers[$i]['identifier'] = $level1users[$i]['identifier'];
                if($level1users[$i]['groupId'] == 1 || $level1users[$i]['groupId'] == 2 || $level1users[$i]['parentId']!= null)
                    $levelusers[$i]['parentId']= $level1users[$i]['parentId'];
                else
                    $levelusers[$i]['parentId']= null;

                if($level1users[$i]['groupId'] == 1 || $level1users[$i]['groupId'] == 2)
                    $levelusers[$i]['levcolor']= 'red';
                elseif($level1users[$i]['groupId'] == 3 || $level1users[$i]['groupId'] == 4)
                    $levelusers[$i]['levcolor']= 'green';
                else
                    $levelusers[$i]['levcolor']= 'orange';
                 $url[$i] = "/home/sites/site5/web/FO/profiles/bo/".$level1users[$i]['identifier']."/logo.jpg";
                if(file_exists($url[$i]))
                    $levelusers[$i]['imgurl'] = "http://ep-test.edit-place.com/FO/profiles/bo/".$level1users[$i]['identifier']."/logo.jpg";
                else
                    $levelusers[$i]['imgurl'] = 'http://ep-test.edit-place.com/FO/profiles/bo/clogo/clogo.png';

               // $imgurl[$i] = "http://ep-test.edit-place.com/FO/profiles/bo/".$level1users[$i]['identifier']."/logo.jpg";

            }
        }
      //  print_r($levelusers); exit;
        echo json_encode($levelusers);
        exit;
    }
    //
    public function organisationCharData2Action()
    {
        $user_obj = new EP_User_User();
        $params = $this->_request->getParams();
        $level1users = $user_obj->getChildUsers($params['parentId']);

        echo json_encode($level1users);
        exit;
    }
    public function organisationCharData3Action()
    {
        $user_obj = new EP_User_User();
        $params = $this->_request->getParams();
        $level1users = $user_obj->getChildUsers($params['parentId']);
        echo json_encode($level1users);
        exit;
    }
    public function reParentingAction()
    {
        $user_obj = new EP_User_User();
        $params = $this->_request->getParams();
        $parentId = $params['parentId']; $childId = $params['childId'];
        $data_user = array("parentId"=>$parentId);
        $query_user = "identifier= '".$childId."'";
        $user_obj->updateUser($data_user,$query_user);

    }
    // rendering the chart page //
    public function organisationChartAction()
    {
        $this->_view->render("hrm_organisation-chart");
    }
}