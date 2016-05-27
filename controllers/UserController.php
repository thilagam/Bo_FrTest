<?

class UserController extends Ep_Controller_Action
{


    private $controller = "user";
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
        Zend_Loader::loadClass('Ep_Document_DocTrack');
        $this->session = new Zend_Session_Namespace('users');
        $this->_view->lang = $this->_lang;
        $this->adminLogin = Zend_Registry::get('adminLogin');
        $this->session = $this->adminLogin;
        $this->_view->loginName = $this->adminLogin->loginName;
        ////if session expires/////
        if ($this->adminLogin->loginName == '' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            echo "session expired...please <a href='/index'>click here</a> to login";
            exit;
        }

    }

    public function emailExitsAction()
    {
        $user_obj = new Ep_User_User();
        $user_params = $this->_request->getParams();

        $emailexit = $user_obj->getExistingEmail($user_params['email']);
        $emailexit = trim($emailexit);
        if ($user_params['user_type'] == 'super_client') {
            $emailexit = $user_obj->getExistingEmail($user_params['fieldValue']);
            $emailexit = trim($emailexit);

            $arrayToJs = array();
            $arrayToJs[0] = $user_params['fieldId'];

            if ($emailexit == 'yes') {
                $arrayToJs[1] = false;
                echo json_encode($arrayToJs);
            } else {
                $arrayToJs[1] = true;
                echo json_encode($arrayToJs);
            }
        } else
            echo $emailexit;
        exit;
    }

    public function yahooSkypeIdsExitsAction()
    {
        $user_obj = new Ep_User_BoUser();
        $user_params = $this->_request->getParams();
        if ($user_params['messenger'] == 'yahoo_id') {
            //echo  $user_params['user_status']; echo "helo"; exit;
            $emailexit = $user_obj->getExistingIds($user_params['messenger'], $user_params['fieldValue'], $user_params['user_status'], $user_params['userId']);
            $emailexit = trim($emailexit);

            $arrayToJs = array();
            $arrayToJs[0] = $user_params['fieldId'];

            if ($emailexit == 'yes') {
                $arrayToJs[1] = false;
                echo json_encode($arrayToJs);
            } else {
                $arrayToJs[1] = true;
                echo json_encode($arrayToJs);
            }
            exit;
        } else if ($user_params['messenger'] == 'skype_id') {
            $emailexit = $user_obj->getExistingIds($user_params['messenger'], $user_params['fieldValue'], $user_params['user_status'], $user_params['userId']);
            $emailexit = trim($emailexit);

            $arrayToJs = array();
            $arrayToJs[0] = $user_params['fieldId'];

            if ($emailexit == 'yes') {
                $arrayToJs[1] = false;
                echo json_encode($arrayToJs);
            } else {
                $arrayToJs[1] = true;
                echo json_encode($arrayToJs);
            }
            exit;
        }

    }

    //check Client  exists or not
    public function clientExistsAction()
    {
        $client_obj = new Ep_User_Client();
        $client_params = $this->_request->getParams();
        $edit_client = $client_params['edit_client'];

        //$client_exist = $client_obj->getExistingClient($client_params['agency_name'],$edit_client);
        //$client_exist=trim($client_exist);
        // Check with Email pass third parameter

        $email = $this->formatEmail(($client_params['fieldValue']));

        $email = trim($email) . "@test.com";

        $client_exist = $client_obj->getExistingClientEmail($client_params['fieldValue'], $edit_client, $email);
        $client_exist = trim($client_exist);

        $arrayToJs = array();
        $arrayToJs[0] = $client_params['fieldId'];

        if ($client_exist == 'yes') {
            $arrayToJs[1] = false;
            echo json_encode($arrayToJs);
        } else {
            $arrayToJs[1] = true;
            echo json_encode($arrayToJs);
        }

        /* if($client_exist=='yes')
            echo "false";
        else
            echo "true"; */

        exit;
    }

    //check Client  exists or not
    public function clientNoExistsAction()
    {
        $client_obj = new Ep_User_Client();
        $client_params = $this->_request->getParams();

        $user_id = $client_params['user_id'];

        $client_exist = $client_obj->checkClientNo($client_params['fieldValue'], $user_id);
        $client_exist = trim($client_exist);

        $arrayToJs = array();
        $arrayToJs[0] = $client_params['fieldId'];

        if ($client_exist == 'yes') {
            $arrayToJs[1] = false;
            echo json_encode($arrayToJs);
        } else {
            $arrayToJs[1] = true;
            echo json_encode($arrayToJs);
        }

        /* if($client_exist=='yes')
            echo "false";
        else
            echo "true"; */

        exit;
    }

    //Format Email
    function formatEmail($email)
    {
        //$email = utf8_decode($email);
        $email = str_replace("'", "", $email);
        $email = frenchCharsToEnglish(str_replace(" ", ".", $email));
        $email = strtolower($email);
        $email = preg_replace('/_+/', '_', $email);
        $email = preg_replace('/\.+/', '.', $email);
        $email = preg_replace('/^\.|\.$/', ' ', $email);
        $email = preg_replace('/^_|_$/', ' ', $email);
        return $email;
    }

    public function newBoUserAction()
    {
        $user_obj = new Ep_User_User();
        $automail_obj = new Ep_Message_AutoEmails();
        $users = $user_obj->getAllManagers();
        $userspermissions = $user_obj->getAllBoUsers();
        foreach ($users as $key => $value) {
            $usermanager[$value['identifier']] = strtoupper($value['email']);
        }
        $this->_view->manager = $usermanager;
        foreach ($userspermissions as $key => $value) {
            $userpers[$value['identifier']] = strtoupper($value['login']) . " (" . $value['first_name'] . " " . $value['last_name'] . ")";
        }
        $this->_view->users = $userpers;
        if ($this->_request->isPost()) {
            $user_params = $this->_request->getParams();
            $userplus_obj = new Ep_User_UserPlus();
            $user_obj = new Ep_User_User();
            $bouser_obj = new Ep_User_BoUser();
            $group_obj = new Ep_User_UserGroupAccess();

            if (isset($_COOKIE["menuids"]) && $_COOKIE["menuids"] != '') {
                $menucookie = str_replace("chkmenu=", "", $_COOKIE["menuids"]);
                $menuids = str_replace("&", "|", $menucookie);
            } else {
                $permissions = $user_obj->getAllUsersDetails($user_params["user_permissions"]);
                $menuids = $permissions[0]['menuId'];
            }
            $emailexit = $user_obj->getExistingEmail($user_params["prof_email"]);
            if ($emailexit == 'yes') {
                $this->_helper->FlashMessenger('Email id is already exist.');
                $this->_redirect("/user/bo-users?submenuId=ML10-SL3");
            }

            ////for goroup Id in users table////
            $grouppage = $group_obj->getGroup(5);
            $user_obj->login = $user_params["login"];
            $user_obj->email = $user_params["prof_email"];
            $user_obj->password = $user_params["password"];
            $user_obj->status = $user_params["status"];
            $user_obj->type = "editor";
            $user_obj->pageId = $grouppage[0]->pageId;
            $user_obj->profile_type = "NULL";
            $user_obj->groupId = 5;
            $user_obj->menuId = $menuids;
            try {
                if ($user_obj->insert()) {
                    $user_identifier = $user_obj->getIdentifier();
                    $userplus_obj->user_id = $user_identifier;
                    $userplus_obj->initial = "mr";
                    $userplus_obj->first_name = "";
                    $userplus_obj->last_name = "";
                    $userplus_obj->address = "";
                    $userplus_obj->city = "";
                    $userplus_obj->state = "";
                    $userplus_obj->zipcode = "";
                    $userplus_obj->country = "";
                    $userplus_obj->phone_number = "";

                    if ($userplus_obj->insert()) {
                        ///////////edit the BoUser table //////////////////
                        $bouser_obj->user_id = $user_identifier;
                        $bouser_obj->email_personal = $user_params["email"];
                        $bouser_obj->birth_date = "";
                        $bouser_obj->birth_city = "";
                        $bouser_obj->telephone = "";
                        $bouser_obj->join_date = "";
                        $bouser_obj->interview = "";
                        $bouser_obj->workplace = "";
                        $bouser_obj->work_telephone = "";
                        $bouser_obj->yahoo_id = "";
                        $bouser_obj->skype_id = "";
                        $bouser_obj->contact_language = "";
                        $bouser_obj->rib = "";
                        $bouser_obj->ssn = "";
                        $bouser_obj->computer_code = $user_params["computer_code"];
                        $bouser_obj->job_title = $user_params["job_title"];
                        $bouser_obj->job_description = $user_params["job_desc"];
                        $bouser_obj->manager_incharge = $user_params["manager_incharge"];
                        $bouser_obj->bo_menuId = $menuids;
                        $bouser_obj->url = "";
                        $bouser_obj->signature = "";
                        $bouser_obj->status = "new";
                        $bouser_obj->webmail_password = $user_params["prof_password"];
                        $bouser_obj->insert();
                    }
                    $automail_obj->sendAutoPersonalEmail($user_identifier, NULL, NULL, NUll);
                    ///deleting the menus cookie///
                    unset($_COOKIE["menuids"]);
                    setcookie('menuids', null, -1, '/');
                }
                $this->_helper->FlashMessenger('Profile Created Successfully.');
                $this->_redirect("/user/bo-users?submenuId=ML10-SL3");
                //$this->render('user_adduser');
            } catch (Zend_Exception $e) {
                $this->_helper->FlashMessenger('Profile Creation Failed.');
                $this->_redirect("user/bo-users?submenuId=ML10-SL3");
            }
        }
        $this->_view->render("user_newbouser");
    }

    ///////////////edit BO user function //////////////////
    public function profileEditAction()
    {
        //echo "<pre>";print_r($_REQUEST);exit;
        //        echo date('Y-m-d', strtotime($this->_request->getParam("birth_date")));
        //        echo date('Y-m-d', strtotime($this->_request->getParam("join_date")));
        //        exit;
        ////if session expires/////
        if ($this->adminLogin->loginName == '') {
            $this->_redirect("/index");
            exit;
        }

        $userId = $this->adminLogin->userId;
        $userplus_obj = new Ep_User_UserPlus();
        $ribbouer_obj = new Ep_User_RibBoUser();
        $participate_obj = new EP_Participation_Participation();
        $changelog_obj = new Ep_User_ProfileChangeLog();
        $details = $userplus_obj->getUsersDetailsOnId($userId);
        /* checking DOB to be proper */
        $details[0]['birth_date'] =  (date("Y",strtotime($details[0]['birth_date'])) < 1900 ) ? '' : $details[0]['birth_date'];
        $details[0]['join_date'] =  (date("Y",strtotime($details[0]['join_date'])) < 1900 ) ? '' : $details[0]['join_date'];
        $user_obj = new Ep_User_User();
        $article_obj = new EP_Delivery_Article();
        $usergrouplist = $user_obj->getUserGroups();
        foreach ($usergrouplist as $key => $value) {
            /* if($value['groupName']!='superadmin')*/
            $usergroups[$value['groupName']] = $value['groupName'];
        }
        $this->_view->usergroups = $usergroups;
        $this->_view->country = $this->country_array[$details[0]['country']];
        $this->_view->Userdetails = $details;
        $this->_view->allribs = $ribbouer_obj->getRidDetails($userId);
        $this->_view->profilepic = "/FO/profiles/bo/" . $details[0]['identifier'] . "/logo.jpg";
        $clogopic = "/FO/profiles/bo/" . $details[0]['identifier'] . "/clogo/logo.jpg";
        $file = "http://ep-test.edit-place.com/FO/profiles/bo/" . $details[0]['identifier'] . "/clogo/logo.jpg";
        $this->_view->clogopic = "/FO/profiles/bo/clogo/logo.png";
        $file_headers = @get_headers($file);
        if ($file_headers[0] == 'HTTP/1.1 404 Not Found')
            $this->_view->clogopic = '<img alt="company loc pic" id="clogopic" width="120px;" class="thumbnail1" src="http://ep-test.edit-place.com/FO/profiles/bo/clogo/clogo.png">';
        else
            $this->_view->clogopic = '<img alt="user pic" id="clogopic" class="thumbnail1" src="http://ep-test.edit-place.com/FO/profiles/bo/' . $details[0]['identifier'] . '/clogo/logo.jpg">';

        ///////////////////////////////////////////////////////
        if ($this->_request->isPost()) {
            $user_params = $this->_request->getParams();
            $userplus_obj = new Ep_User_UserPlus();
            $bouser_obj = new Ep_User_BoUser();
            $user_obj = new Ep_User_User();
            $bouserdetials = $bouser_obj->getBoUserExtraDetails($userId);
            if ($bouserdetials != 'NO' && $bouserdetials[0]['status'] == 'new')
                $menus = $bouserdetials[0]['bo_menuId'];
            else
                $menus = $details[0]['menuId'];
            ////for goroup Id in users table////
            $grouparray = array('superadmin' => 1, 'ceouser' => 2, 'salesuser' => 3, 'chiefeditor' => 4, 'editor' => 5, 'seouser' => 6, 'customercare' => 7, 'partner' => 8, 'facturation' => 9, 'custom' => 10, 'multilingue' => 11, 'chiefodigeo' => 12, 'techuser' => 13, 'produser' => 14, 'techmanager' => 15, 'seomanager' => 16, 'prodsubmanager' => 17, 'salesmanager' => 18, 'prodmanager' => 19,'contractowner'=>20);
            $user_obj->login = $user_params["login"];
            $user_obj->email = $user_params["email"];
            $user_obj->password = $user_params["password"];
            $user_group = $grouparray[$user_params["type"]];
            $user_obj->groupId = $user_group;
            $data_user = array("login" => $user_obj->login, "email" => $user_obj->email, "password" => $user_obj->password, "menuId" => $menus);
            $query_user = "identifier= '" . $userId . "'";
            $user_obj->updateUser($data_user, $query_user);
            try {
                $userplus_obj->initial = $user_params["initial"];
                $userplus_obj->first_name = $user_params["first_name"];
                $userplus_obj->last_name = $user_params["last_name"];
                $userplus_obj->address = $user_params["address"];
                $userplus_obj->city = $user_params["city"];
                $userplus_obj->state = $user_params["state"];
                $userplus_obj->zipcode = $user_params["zipcode"];
                $userplus_obj->country = $user_params["country"];
                $userplus_obj->phone_number = $user_params["phone_number"];

                $data_userplus = array("initial" => $userplus_obj->initial, "first_name" => $userplus_obj->first_name, "last_name" => $userplus_obj->last_name,
                    "address" => $userplus_obj->address, "city" => $userplus_obj->city, "state" => $userplus_obj->state, "zipcode" => $userplus_obj->zipcode,
                    "country" => $userplus_obj->country, "phone_number" => $userplus_obj->phone_number);
                $query_userplus = "user_id= '" . $userId . "'";
                $userplus_obj->updateUserPlus($data_userplus, $query_userplus);
                ////upload social number proof//
                if ($_FILES['social_num']['tmp_name'] != '') {
                    $tmpName = $_FILES['social_num']['tmp_name'];
                    $socialName = $_FILES['social_num']['name'];
                    $ext = explode('.', $socialName);
                    $extension = $ext[1];
                    $snum_path = $userId . "_" . rand(10000, 99999) . "." . $extension;
                    //////////////////uploading the document///////////////////////////////
                    $server_path = "/home/sites/site5/web/FO/ssn_files/" . $userId;
                    mkdir($server_path, 0777);
                    $ssnDir = $server_path . "/";
                    $newfile = $ssnDir . "/" . $snum_path;
                    move_uploaded_file($tmpName, $newfile);
                }

                ///////////edit the BoUser table //////////////////
                ////////////////////////////////////////
                $signature = '<table cellpadding="8" cellspacing="0" width="400px">
                 <tbody>
                  <tr valign="top">
                      <td align="center" bgcolor="#3A3A3A" valign="top"><img src="http://ep-test.edit-place.com/FO/profiles/bo/' . $userId . '/logo.jpg" alt="' . $user_params["first_name"] . ' ' . $user_params["last_name"] . '" height="70" width="70"></td>
                      <td bgcolor="#3A3A3A" style="color:#999999"><p>' . $user_params["first_name"] . ' ' . $user_params["last_name"] . '<span id="title-sep">&nbsp;/</span>&nbsp;' . $user_params["job_title"] . '&nbsp;<br>
                          <a href="mailto:' . $user_params["email"] . '" style="color:#3EA1DA"  id="email-input" onclick="return rcmail.command("compose",' . $user_params["email"] . ',this)">' . $user_params["email"] . '</a><br>
                          <span id="office-sep">Mobile :&nbsp;</span><span id="office-input">' . $user_params["phone_number"] . '</span><br>
                          <span id="office-sep">Bureau :&nbsp;</span><span id="office-input">' . $user_params["telephone"] . '</span><br>
                          <br>
                          <span id="office-sep">Suivez nos actualit&eacute;s sur le&nbsp;</span><span id="office-input"><a href="http://www.edit-place.com/blog/" style="color:#FF780A"  target="_blank">blog Edit-place !</a></span></p>
                        <a href="http://www.edit-place.com/" target="_blank"><img src="http://www.edit-place.com/extra/signature/logo.png" alt="Edit-place" id="sig-logo" border="0" height="30" width="130"></a></td>
                    </tr>
                   </tbody>
                </table>';
                // print_r($user_params); exit;
                $bouser_obj->user_id = $userId;
                $bouser_obj->email_personal = $user_params["email_personal"];
                $date1 = explode("/", $user_params["birth_date"]);
                $bouser_obj->birth_date = $date1[2] . "-" . $date1[1] . "-" . $date1[0];
                $bouser_obj->birth_city = $user_params["birth_city"];
                $bouser_obj->telephone = $user_params["telephone"];
                $date2 = explode("/", $user_params["join_date"]);
                $bouser_obj->join_date = $date2[2] . "-" . $date2[1] . "-" . $date2[0];
                $bouser_obj->interview = $user_params["interview"];
                $bouser_obj->workplace = $user_params["workplace"];
                $bouser_obj->work_telephone = $user_params["work_telephone"];
                $bouser_obj->yahoo_id = $user_params["yahoo_id"];
                $bouser_obj->skype_id = $user_params["skype_id"];
                $bouser_obj->contact_language = implode(",", $user_params["contact_language"]);
                $bouser_obj->rib = $user_params["rib"];
                $bouser_obj->ssn = $user_params["ssn"];
                $bouser_obj->ssn_file = $snum_path;
                $bouser_obj->computer_code = $user_params["computer_code"];
                $bouser_obj->job_title = $user_params["job_title"];
                $bouser_obj->url = $user_params["site_url"];
                $bouser_obj->signature = $signature;
                $bouser_obj->status = 'old';
                $data_bouser = array("email_personal" => $bouser_obj->email_personal, "birth_date" => $bouser_obj->birth_date, "birth_city" => $bouser_obj->birth_city, "telephone" => $bouser_obj->telephone, "join_date" => $bouser_obj->join_date,
                    "interview" => $bouser_obj->interview, "workplace" => $bouser_obj->workplace, "work_telephone" => $bouser_obj->work_telephone, "yahoo_id" => $bouser_obj->yahoo_id, "skype_id" => $bouser_obj->skype_id, "job_title" => $bouser_obj->job_title,
                    "contact_language" => $bouser_obj->contact_language, "rib" => $bouser_obj->rib, "ssn" => $bouser_obj->ssn, "ssn_file" => $bouser_obj->ssn_file, "computer_code" => $bouser_obj->computer_code, "url" => $bouser_obj->url, "signature" => $bouser_obj->signature, "status" => $bouser_obj->status);
                $query_bouser = "user_id= '" . $userId . "'";
                if ($bouserdetials != 'NO') {
                    $bouser_obj->updateBoUser($data_bouser, $query_bouser);
                } else {
                    $bouser_obj->insert();
                }

                ///////////edit the RibBoUser table //////////////////
                $ribbouer_obj = new Ep_User_RibBoUser();
                $ribuser = $ribbouer_obj->getRidDetails($userId);
                if ($ribuser != 'NO')
                    $ribbouer_obj->deleteRibBoUser($userId);
                ////sending the mail to manager who have created this user///
                $autoemail_obj = new Ep_Message_AutoEmails();
                $managerId = $bouserdetials[0]['manager_incharge'];
                $details['first_name'] = $user_params["first_name"];
                $details['last_name'] = $user_params["last_name"];
                $details['menuId'] = $bouserdetials[0]['menuId'];;
                if ($user_params["computer_code"] != $user_params["original_computer_code"]) {
                    $details['compcode'] = $user_params["computer_code"];
                    if ($managerId != '')
                        $autoemail_obj->sendDetialsToPersonalEmail($managerId, $details);
                }
                $details['editmail'] = "yes";
                $details['userid'] = $userId;
                if ($managerId != '')
                    $autoemail_obj->sendDetialsToPersonalEmail($managerId, $details);
                for ($i = 0; $i <= $user_params["ribcount"]; $i++) {
                    $ribbouer_obj = new Ep_User_RibBoUser();
                    $ribbouer_obj->user_id = $userId;
                    $ribbouer_obj->rib_number = $user_params["rib_num"][$i];
                    $ribbouer_obj->rib_name = $user_params["rib_name"][$i];
                    $ribbouer_obj->rib_file = "";
                    $ribbouer_obj->default_val = $user_params["rib_default_val"][$i];
                    /*if($user_params["rib_default"][0] == $i)
                        $ribbouer_obj->default_val="yes" ;
                    else
                        $ribbouer_obj->default_val="no" ;*/
                    $ribbouer_obj->insert();
                }
                ////upload social number proof//
                if ($_FILES['social_num']['tmp_name'] != '') {
                    $tmpName = $_FILES['social_num']['tmp_name'];
                    $socialName = $_FILES['social_num']['name'];
                    $ext = explode('.', $socialName);
                    $extension = $ext[1];
                    $snum_path = $userId . "_" . rand(10000, 99999) . "." . $extension;
                    $data_exp[$i] = array("expense_invoice" => $snum_path);
                    $query_exp[$i] = "identifier= '" . $expid . "'";
                    $expense_obj->updateExpenses($data_exp[$i], $query_exp[$i]);
                    //////////////////uploading the document///////////////////////////////
                    $server_path = "/home/sites/site5/web/FO/expenses/" . $userId;
                    mkdir($server_path, 0777);

                    $expenseDir = $server_path . "/";
                    $newfile = $expenseDir . "/" . $exp_path;
                    move_uploaded_file($tmpName, $newfile);
                }
                if ($details[0]['email'] != $_REQUEST['email']) {
                    $changelog_obj->user_id = $_REQUEST['userId'];
                    $changelog_obj->old_email = $details[0]['email'];
                    $changelog_obj->new_email = $_REQUEST['email'];
                    $changelog_obj->changed_by = $this->adminLogin->userId;
                    $changelog_obj->insert();
                }

                $this->_helper->FlashMessenger('Profile Updated Successfully. Please re-login to avail all features');
                // $this->_redirect("/user/bo-users?submenuId=ML10-SL3");
                $this->_redirect("/user/profile-edit?tab=profileview");

                // $this->render('user_userdetails');
            } catch (Zend_Exception $e) {
                echo $e->getMessage();
                // $this->_helper->FlashMessenger('Profile Updated Failed.');
                $this->_redirect("/user/profile-edit?tab=profileview");
            }
        } else {
            $this->render('user_profile_edit');
        }
    }
    /* *** added on 12.01.2016 *** */
    //ajax call to update profile of BO users//
    public function profileEditAjaxAction(){
        $response1 = $response2 = $response3 = $response4 = 1;//values and response after making changes into DB in the below function//
        $userId = $this->adminLogin->userId;
        $user_params = $this->_request->getParams();
        $userplus_obj = new Ep_User_UserPlus();
        $bouser_obj = new Ep_User_BoUser();
        $user_obj = new Ep_User_User();
        $bouserdetials = $bouser_obj->getBoUserExtraDetails($userId);

        $user_obj->login = $user_params["login"];
        $user_obj->email = $user_params["email"];
        $user_obj->password = $user_params["password"];
        $data_user = array("login" => $user_obj->login, "email" => $user_obj->email, "password" => $user_obj->password);
        $query_user = "identifier= '" . $userId . "'";
        $response1 = $user_obj->updateUser($data_user, $query_user);
        try {
            $userplus_obj->initial = $user_params["initial"];
            $userplus_obj->first_name = $user_params["first_name"];
            $userplus_obj->last_name = $user_params["last_name"];
            $userplus_obj->address = $user_params["address"];
            $userplus_obj->city = $user_params["city"];
            $userplus_obj->state = $user_params["state"];
            $userplus_obj->zipcode = $user_params["zipcode"];
            $userplus_obj->country = $user_params["country"];
            $userplus_obj->phone_number = $user_params["phone_number"];

            $data_userplus = array("initial" => $userplus_obj->initial, "first_name" => $userplus_obj->first_name, "last_name" => $userplus_obj->last_name,
                "address" => $userplus_obj->address, "city" => $userplus_obj->city, "state" => $userplus_obj->state, "zipcode" => $userplus_obj->zipcode,
                "country" => $userplus_obj->country, "phone_number" => $userplus_obj->phone_number);
            $query_userplus = "user_id= '" . $userId . "'";
            $response2 =  $userplus_obj->updateUserPlusAjax($data_userplus, $query_userplus);
            ////upload social number proof//
            /*if ($_FILES['social_num']['tmp_name'] != '') {
                $tmpName = $_FILES['social_num']['tmp_name'];
                $socialName = $_FILES['social_num']['name'];
                $ext = explode('.', $socialName);
                $extension = $ext[1];
                $snum_path = $userId . "_" . rand(10000, 99999) . "." . $extension;
                //////////////////uploading the document///////////////////////////////
                $server_path = "/home/sites/site5/web/FO/ssn_files/" . $userId;
                mkdir($server_path, 0777);
                $ssnDir = $server_path . "/";
                $newfile = $ssnDir . "/" . $snum_path;
                move_uploaded_file($tmpName, $newfile);
            }
            exit;*/
            ///////////edit the BoUser table //////////////////
            ////////////////////////////////////////
            $signature = '<table cellpadding="8" cellspacing="0" width="400px">
                 <tbody>
                  <tr valign="top">
                      <td align="center" bgcolor="#3A3A3A" valign="top"><img src="http://ep-test.edit-place.com/FO/profiles/bo/' . $userId . '/logo.jpg" alt="' . $user_params["first_name"] . ' ' . $user_params["last_name"] . '" height="70" width="70"></td>
                      <td bgcolor="#3A3A3A" style="color:#999999"><p>' . $user_params["first_name"] . ' ' . $user_params["last_name"] . '<span id="title-sep">&nbsp;/</span>&nbsp;' . $user_params["job_title"] . '&nbsp;<br>
                          <a href="mailto:' . $user_params["email"] . '" style="color:#3EA1DA"  id="email-input" onclick="return rcmail.command("compose",' . $user_params["email"] . ',this)">' . $user_params["email"] . '</a><br>
                          <span id="office-sep">Mobile :&nbsp;</span><span id="office-input">' . $user_params["phone_number"] . '</span><br>
                          <span id="office-sep">Bureau :&nbsp;</span><span id="office-input">' . $user_params["telephone"] . '</span><br>
                          <br>
                          <span id="office-sep">Suivez nos actualit&eacute;s sur le&nbsp;</span><span id="office-input"><a href="http://www.edit-place.com/blog/" style="color:#FF780A"  target="_blank">blog Edit-place !</a></span></p>
                        <a href="http://www.edit-place.com/" target="_blank"><img src="http://www.edit-place.com/extra/signature/logo.png" alt="Edit-place" id="sig-logo" border="0" height="30" width="130"></a></td>
                    </tr>
                   </tbody>
                </table>';
            $bouser_obj->user_id = $userId;
            $bouser_obj->email_personal = $user_params["email_personal"];
            $date1 = explode("/", $user_params["birth_date"]);
            $bouser_obj->birth_date = $date1[2] . "-" . $date1[1] . "-" . $date1[0];
            $bouser_obj->birth_city = $user_params["birth_city"];
            $bouser_obj->telephone = $user_params["telephone"];
            $date2 = explode("/", $user_params["join_date"]);
            $bouser_obj->join_date = $date2[2] . "-" . $date2[1] . "-" . $date2[0];
            $bouser_obj->interview = $user_params["interview"];
            $bouser_obj->workplace = $user_params["workplace"];
            $bouser_obj->work_telephone = $user_params["work_telephone"];
            $bouser_obj->yahoo_id = $user_params["yahoo_id"];
            $bouser_obj->skype_id = $user_params["skype_id"];
            $bouser_obj->contact_language = implode(",", $user_params["contact_language"]);
            $bouser_obj->rib = $user_params["rib"];
            $bouser_obj->ssn = $user_params["ssn"];
            $bouser_obj->ssn_file = $snum_path;
            $bouser_obj->computer_code = $user_params["computer_code"];
            $bouser_obj->job_title = $user_params["job_title"];
            $bouser_obj->url = $user_params["site_url"];
            $bouser_obj->signature = $signature;
            $bouser_obj->status = 'old';
            $data_bouser = array("email_personal" => $bouser_obj->email_personal, "birth_date" => $bouser_obj->birth_date, "birth_city" => $bouser_obj->birth_city, "telephone" => $bouser_obj->telephone, "join_date" => $bouser_obj->join_date,
                "interview" => $bouser_obj->interview, "workplace" => $bouser_obj->workplace, "work_telephone" => $bouser_obj->work_telephone, "yahoo_id" => $bouser_obj->yahoo_id, "skype_id" => $bouser_obj->skype_id, "job_title" => $bouser_obj->job_title,
                "contact_language" => $bouser_obj->contact_language, "rib" => $bouser_obj->rib, "ssn" => $bouser_obj->ssn, "ssn_file" => $bouser_obj->ssn_file, "computer_code" => $bouser_obj->computer_code, "url" => $bouser_obj->url, "signature" => $bouser_obj->signature, "status" => $bouser_obj->status);
            $query_bouser = "user_id= '" . $userId . "'";
            if ($bouserdetials != 'NO') {
                $response3 =  $bouser_obj->updateBoUserAjax($data_bouser, $query_bouser);
            } else {
                $response3 =  $bouser_obj->insert();
            }

            ////sending the mail to manager who have created this user///
            $autoemail_obj = new Ep_Message_AutoEmails();
            $managerId = $bouserdetials[0]['manager_incharge'];
            $details['first_name'] = $user_params["first_name"];
            $details['last_name'] = $user_params["last_name"];
            $details['menuId'] = $bouserdetials[0]['menuId'];;
            if ($user_params["computer_code"] != $user_params["original_computer_code"]) {
                $details['compcode'] = $user_params["computer_code"];
                if ($managerId != '')
                    $autoemail_obj->sendDetialsToPersonalEmail($managerId, $details);
            }
            $details['editmail'] = "yes";
            $details['userid'] = $userId;
            if ($managerId != '')
                $autoemail_obj->sendDetialsToPersonalEmail($managerId, $details);
            ////end of sending the mail to manager who have created this user///
            ///////////edit the RibBoUser table //////////////////
            $ribbouer_obj = new Ep_User_RibBoUser();
            $ribuser = $ribbouer_obj->getRidDetails($userId);
            if ($ribuser != 'NO') {
                $ribbouer_obj->delRibBoUser($userId);
            }
            for ($i = 0; $i <= $user_params["ribcount"]; $i++) {
                $ribbouer_obj = new Ep_User_RibBoUser();
                $ribbouer_obj->user_id = $userId;
                $ribbouer_obj->rib_number = $user_params["rib_num"][$i];
                $ribbouer_obj->rib_name = $user_params["rib_name"][$i];
                $ribbouer_obj->rib_file = "";
                $ribbouer_obj->default_val = $user_params["rib_default_val"][$i];
                /*if($user_params["rib_default"][0] == $i)
                    $ribbouer_obj->default_val="yes" ;
                else
                    $ribbouer_obj->default_val="no" ;*/
                $response4 =  $ribbouer_obj->insert();
            }
            /////////// end of edit the RibBoUser table //////////////////

            ////upload social number proof//
            /*if ($_FILES['social_num']['tmp_name'] != '') {
                $tmpName = $_FILES['social_num']['tmp_name'];
                $socialName = $_FILES['social_num']['name'];
                $ext = explode('.', $socialName);
                $extension = $ext[1];
                $snum_path = $userId . "_" . rand(10000, 99999) . "." . $extension;
                $data_exp[$i] = array("expense_invoice" => $snum_path);
                $query_exp[$i] = "identifier= '" . $expid . "'";
                $expense_obj->updateExpenses($data_exp[$i], $query_exp[$i]);
                //////////////////uploading the document///////////////////////////////
                $server_path = "/home/sites/site5/web/FO/expenses/" . $userId;
                mkdir($server_path, 0777);

                $expenseDir = $server_path . "/";
                $newfile = $expenseDir . "/" . $exp_path;
                move_uploaded_file($tmpName, $newfile);
            }*/
            /*if ($details[0]['email'] != $_REQUEST['email']) {
                $changelog_obj->user_id = $_REQUEST['userId'];
                $changelog_obj->old_email = $details[0]['email'];
                $changelog_obj->new_email = $_REQUEST['email'];
                $changelog_obj->changed_by = $this->adminLogin->userId;
                $changelog_obj->insert();
            }*/
            if( ($response1  || !$response1) && ($response2  || !$response2) && ($response3  || !$response3) && ($response4  || !$response4) )//eigther ther response should be flase or true(no errors or warning)
             {
                $response = array("pass" => 1,"msg" => 'Profile Updated Successfully. Please re-login to avail all features.');
                echo json_encode($response);
                exit;
            }
            else{
                $response = array("pass" => 0,"msg" => 'Profile Updated Failed.');
                echo json_encode($response);
                exit;

            }
            //$this->_helper->FlashMessenger('Profile Updated Successfully. Please re-login to avail all features');
            //$this->_redirect("/user/bo-users?submenuId=ML10-SL3");
            //$this->_redirect("/user/profile-edit?tab=profileview");
            // $this->render('user_userdetails');
        } catch (Zend_Exception $e) {
            $response = array("pass" => 0,"msg" => 'Profile Updated Failed.');
            echo json_encode($response);
            exit;
            //$this->_helper->FlashMessenger('Profile Updated Failed.');
            //$this->_redirect("/user/profile-edit?tab=profileview");
        }
    }
    public function viewProfileAjaxAction(){
        $userId = $this->adminLogin->userId;
        $userplus_obj = new Ep_User_UserPlus();
        $ribbouer_obj = new Ep_User_RibBoUser();
        $participate_obj = new EP_Participation_Participation();
        $changelog_obj = new Ep_User_ProfileChangeLog();
        $details = $userplus_obj->getUsersDetailsOnId($userId);
        $user_obj = new Ep_User_User();
        $article_obj = new EP_Delivery_Article();
        $usergrouplist = $user_obj->getUserGroups();
        foreach ($usergrouplist as $key => $value) {
            /* if($value['groupName']!='superadmin')*/
            $usergroups[$value['groupName']] = $value['groupName'];
        }
        $this->_view->usergroups = $usergroups;
        $this->_view->country = $this->country_array[$details[0]['country']];
        $this->_view->Userdetails = $details;
        $this->_view->allribs = $ribbouer_obj->getRidDetails($userId);
        $this->_view->profilepic = "/FO/profiles/bo/" . $details[0]['identifier'] . "/logo.jpg";
        $clogopic = "/FO/profiles/bo/" . $details[0]['identifier'] . "/clogo/logo.jpg";
        $file = "http://ep-test.edit-place.com/FO/profiles/bo/" . $details[0]['identifier'] . "/clogo/logo.jpg";
        $this->_view->clogopic = "/FO/profiles/bo/clogo/logo.png";
        $file_headers = @get_headers($file);
        if ($file_headers[0] == 'HTTP/1.1 404 Not Found')
            $this->_view->clogopic = '<img alt="company loc pic" id="clogopic" width="120px;" class="thumbnail1" src="http://ep-test.edit-place.com/FO/profiles/bo/clogo/clogo.png">';
        else
            $this->_view->clogopic = '<img alt="user pic" id="clogopic" class="thumbnail1" src="http://ep-test.edit-place.com/FO/profiles/bo/' . $details[0]['identifier'] . '/clogo/logo.jpg">';
        $this->_view->ajax = 'viewProfileAjaxAction';//simply pass the action name so that its easier in phtml page to go to the if condition//
        $this->render('ajax_user');
    }
    ///////////////edit user function //////////////////
    public function userEditAction()
    {
        $userId = $this->_request->getParam('userId');
        $userplus_obj = new Ep_User_UserPlus();
        $ribbouer_obj = new Ep_User_RibBoUser();
        $participate_obj = new EP_Participation_Participation();
        $changelog_obj = new Ep_User_ProfileChangeLog();
        $details = $userplus_obj->getUsersDetailsOnId($userId);
        $user_obj = new Ep_User_User();
        $article_obj = new EP_Delivery_Article();
        $usergrouplist = $user_obj->getUserGroups();
        foreach ($usergrouplist as $key => $value) {
            /* if($value['groupName']!='superadmin')*/
            $usergroups[$value['groupName']] = $value['groupName'];
        }
        $this->_view->usergroups = $usergroups;
        $this->_view->Userdetails = $details;
        $this->_view->allribs = $ribbouer_obj->getRidDetails($userId);
        $this->_view->profilepic = "/FO/profiles/bo/" . $details[0]['identifier'] . "/logo.jpg";
        $clogopic = "/FO/profiles/bo/" . $details[0]['identifier'] . "/clogo/logo.jpg";
        $file = "http://ep-test.edit-place.com/FO/profiles/bo/" . $details[0]['identifier'] . "/clogo/logo.jpg";
        $file_headers = @get_headers($file);
        if ($file_headers[0] == 'HTTP/1.1 404 Not Found')
            $this->_view->clogopic = '<img alt="user pic" id="clogopic" width="120px;" class="thumbnail1" src="http://ep-test.edit-place.com/FO/profiles/bo/clogo/clogo.png">';
        else
            $this->_view->clogopic = '<img alt="user pic" id="clogopic" class="thumbnail1" src="http://ep-test.edit-place.com/FO/profiles/bo/' . $details[0]['identifier'] . '/clogo/logo.jpg">';
        /////////////graph part ///////////
        $params['userid'] = $userId;
        $marksforarts = $participate_obj->getMarksBoUserGraph($params); //print_r($marksforarts); exit;
        if ($marksforarts != 'NO') {

            $this->_view->dates = "var d2 = [
             ";
            if ($marksforarts)
                foreach ($marksforarts[0] as $key => $val) {
                    //echo "<pre>".$key = substr($key, 0, -3);
                    $key = substr($key, 0, -3);
                    if ($val == '')
                        $val = 0;
                    $this->_view->dates .= "[new Date('" . $key . "').getTime()," . $val . "],
                 ";
                }
            $this->_view->dates .= "];
             ";  //exit;
            //  echo $this->_view->dates; exit;
        } else {
            $this->_view->dates = "var d2 = [ " . date('Y-m') . " ];";
        }
        /////////end of graph /////////////////////////
        ///////////////contributor stats ////////////

        $params['statstype'] = 'moyennes';
        $marksdetails = $user_obj->getBoUserStats($params); //print_r($marksdetails);
        $marks = 0;
        if ($marksdetails != 'NO') {
            foreach ($marksdetails as $key => $value) {
                $marks += $marksdetails[$key]['marks'];
            }
        }
        if ($marks != '0')
            $marks = $marks / 10;
        else
            $marks = 0;

        $this->_view->marks = $marks . "/10";
        /////// all stats for the bo user////////////////////////
        $participate_obj = new EP_Participation_Participation();
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();

        $noarts = $participate_obj->getBoUserParticipantsStatistics($params);
        $clientnamesarray = $article_obj->getBoUserClientLIst($params);
        if ($clientnamesarray != 'NO') {
            foreach ($clientnamesarray AS $key => $value) {
                $clientnames[$key] = $clientnamesarray[$key]['company_name'];
            }
            $this->_view->client_list = implode(",", $clientnames);
        } else
            $this->_view->client_list = 0;
        $marks = 0;
        if ($noarts != '') {
            $this->_view->refused_arts = $noarts[0]['refusedCount'];
            $this->_view->total_parts = $noarts[1]['totalCount'];
            $this->_view->published_arts = $noarts[2]['publishedCount'];
            ////get the reasons /////
            if ($noarts[3]['botempreasons'] != '') {
                $botempreasons = $this->formRefusalReasons($noarts[3]['botempreasons'], 'frequent');
                $this->_view->freqbotempreason = $botempreasons[0];
                $this->_view->freqbotempreasoncount = $botempreasons[1];
            } else {
                $this->_view->freqbotempreason = 'nill';
                $this->_view->freqbotempreasoncount = 0;
            }
            $this->_view->total_refusalreasons = $noarts[4]['totaltemplateCount'];  // total count of refusal reasons
            if ($clientnamesarray != 'NO')
                $this->_view->num_clients = count($clientnamesarray);
            else
                $this->_view->num_clients = 0;
            if ($noarts[5]['bopermreason'] != '') {
                $bopermreasons = $this->formRefusalReasons($noarts[5]['bopermreason'], 'frequent');
                $this->_view->freqbopermreason = $bopermreasons[0];
                $this->_view->freqbopermreasoncount = $bopermreasons[1];
            } else {
                $this->_view->freqbopermreason = 'nill';
                $this->_view->freqbopermreasoncount = 0;
            }
            $this->_view->total_crtparts = $noarts[6]['crttotalCount'];
        }
        $missionsarts = $article_obj->getBoUserMissionsStatistics($params);
        if ($missionsarts != '') {
            $this->_view->publicCount = $missionsarts[0]['publicCount'];
            $this->_view->privateCount = $missionsarts[1]['privateCount'];
            $this->_view->publicBoCount = $missionsarts[2]['publicBoCount'];
            $this->_view->privateBoCount = $missionsarts[3]['privateBoCount'];
            $this->_view->publicFoPubCount = $missionsarts[4]['publicFoPubCount'];
            $this->_view->publicFoPrvCount = $missionsarts[5]['publicFoPrvCount'];
            $this->_view->privateFoPubCount = $missionsarts[6]['privateFoPubCount'];
            $this->_view->privateFoPrvCount = $missionsarts[7]['privateFoPrvCount'];
            $this->_view->freqlanguageCount = $missionsarts[8]['frequentLanguage'];
            $this->_view->freqlanguage = utf8_encode($this->language_array[$missionsarts[8]['language']]);
            $this->_view->freqcategCount = $missionsarts[9]['frequentCategory'];
            $this->_view->freqcateg = utf8_encode($this->category_array[$missionsarts[9]['category']]);
            $this->_view->total_articles = $missionsarts[10]['totalArtCount'];
            $this->_view->timespend = $this->secondsToTime($missionsarts[11]['timespend']);

            $view_to = $article_obj->boUserViewto($params);// print_r($view_to); exit;
            $viewto = array();
            $viewtosc = 0;
            $viewtojc = 0;
            $viewtojc0 = 0;
            $viewtoall = 0;
            if ($view_to != 'NO') {

                for ($j = 0; $j < count($view_to); $j++) {
                    $view_toarr[$j] = explode(",", $view_to[$j]['view_to']);
                    if (in_array('sc', $view_toarr[$j]))
                        $viewtosc++;
                    if (in_array('jc', $view_toarr[$j]))
                        $viewtojc++;
                    if (in_array('jc0', $view_toarr[$j]))
                        $viewtojc0++;
                    if (count($view_toarr[$j]) == 3)
                        $viewtoall++;
                }

            }
            $viewto['sc'] = $viewtosc;
            $viewto['jc'] = $viewtojc;
            $viewto['jc0'] = $viewtojc0;
            $viewto['all'] = $viewtoall;
            $this->_view->viewto = $viewto;
            $selview_to = $article_obj->selectedProfileType($params);
            $selviewtosc = 0;
            $selviewtojc = 0;
            $selviewtojc0 = 0;
            if ($selview_to != 'NO') {
                for ($i = 0; $i < count($selview_to); $i++) {
                    $profile = $user_obj->getAllUsersDetails($selview_to[$i]['user_id']);
                    if ($profile[0]['profile_type'] == "sub-junior")
                        $selviewtojc0++;
                    if ($profile[0]['profile_type'] == "junior")
                        $selviewtojc++;
                    if ($profile[0]['profile_type'] == "senior")
                        $selviewtosc++;
                }

            }
            $selviewto['sc'] = $selviewtosc;
            $selviewto['jc'] = $selviewtojc;
            $selviewto['jc0'] = $selviewtojc0;
            $this->_view->selviewto = $selviewto;
            $topwriter = $participate_obj->topWriterParticipated($params);
            if ($topwriter != 'NO')
                $this->_view->topwriter = $topwriter;
            $topcorrector = $crtparticipate_obj->topCorrrectorParticipated($params);
            if ($topcorrector != 'NO')
                $this->_view->topcorrector = $topcorrector;
            $clientlist = $participate_obj->clientListArtParts($params);
            if ($clientlist != 'NO') {
                foreach ($clientlist AS $key => $value) {
                    $clientlistnames[$key] = $clientlist[$key]['company_name'];
                }
                $this->_view->clientparts_list = implode(",", $clientlistnames);
            } else
                $this->_view->clientparts_list = 0;
            $this->_view->clientpartnum = count($clientlist);
            $pmpartscount = $participate_obj->getParticipantOfProjectManager($params);
            if ($pmpartscount[0]['PMpartscount'] != '')
                $this->_view->PMpartscount = $pmpartscount[0]['PMpartscount'];
            else
                $this->_view->PMpartscount = 0;
            $pmcrtpartscount = $crtparticipate_obj->getCrtParticipantOfProjectManager($params);
            if ($pmcrtpartscount[0]['PMcrtpartscount'] != '')
                $this->_view->PMcrtpartcount = $pmcrtpartscount[0]['PMcrtpartscount'];
            else
                $this->_view->PMcrtpartcount = 0;
        }
        //////////////get all ongoing aritcle of this user////
        $result = $article_obj->allArticlesOfBoUser($params);
        if ($result != 'NO') {
            foreach ($result as $key1 => $value1) {
                $result[$key1]['language'] = utf8_encode($this->language_array[$result[$key1]['language']]);
                $result[$key1]['category'] = utf8_encode($this->category_array[$result[$key1]['category']]);
                $writingparts = $participate_obj->getAcceptedParticipant($result[$key1]['articleId']);// participation in writing
                if ($writingparts != 'NO')
                    $result[$key1]['inwriting'] = count($writingparts);
                $stagesparts = $participate_obj->getS1S2Participations($result[$key1]['articleId']);// participation in s1 and s2
                if ($stagesparts != 'NO')
                    $result[$key1]['instages'] = count($stagesparts);
                $validparts = $participate_obj->getvalidatedParticipations($result[$key1]['articleId']);// participation which are published
                if ($validparts != 'NO')
                    $result[$key1]['validated'] = count($validparts);
            }
        }
        // print_r($res); exit;
        $this->_view->bouserstatsarticles = $result;
        ///////////////////////////////////////////////////////
        if ($this->_request->isPost()) {
            $user_params = $this->_request->getParams();
            $userplus_obj = new Ep_User_UserPlus();
            $bouser_obj = new Ep_User_BoUser();
            $user_obj = new Ep_User_User();
            $bouserdetials = $bouser_obj->getBoUserExtraDetails($userId);
            if ($bouserdetials != 'NO' && $bouserdetials[0]['status'] == 'new')
                $menus = $bouserdetials[0]['bo_menuId'];
            else
                $menus = $details[0]['menuId'];
            /////updating the user child to its parents///
            if ($user_params["status"] == 'Inactive') {
                $children = $user_obj->getUserChildren($userId);
                if ($children != 'NO') {
                    for ($i = 0; $i < count($children); $i++) {
                        $data_user1 = array("parentId" => $details[0]['parentId']);
                        $query_user1 = "identifier= '" . $children[$i]['identifier'] . "'";
                        $user_obj->updateUser($data_user1, $query_user1);
                    }
                }
            }
            ////
            ////for goroup Id in users table////
            $grouparray = array('superadmin' => 1, 'ceouser' => 2, 'salesuser' => 3, 'chiefeditor' => 4, 'editor' => 5, 'seouser' => 6, 'customercare' => 7, 'partner' => 8, 'facturation' => 9, 'custom' => 10, 'multilingue' => 11, 'chiefodigeo' => 12, 'techuser' => 13, 'produser' => 14, 'techmanager' => 15, 'seomanager' => 16, 'prodsubmanager' => 17, 'salesmanager' => 18, 'prodmanager' => 19,'contractowner'=>20);
            $user_obj->login = $user_params["login"];
            $user_obj->email = $user_params["email"];
            $user_obj->password = $user_params["password"];
            $user_obj->status = $user_params["status"];
            $user_obj->parentid = $user_params["parentId"];
            $user_obj->type = $user_params["type"];
            $user_group = $grouparray[$user_params["type"]];
            $user_obj->groupId = $user_group;
            $data_user = array("login" => $user_obj->login, "email" => $user_obj->email, "password" => $user_obj->password,
                "status" => $user_obj->status, "type" => $user_obj->type, "groupId" => $user_obj->groupId, "parentId" => $user_obj->parentid, "menuId" => $menus);
            $query_user = "identifier= '" . $userId . "'";
            $user_obj->updateUser($data_user, $query_user);
            try {
                $userplus_obj->initial = $user_params["initial"];
                $userplus_obj->first_name = $user_params["first_name"];
                $userplus_obj->last_name = $user_params["last_name"];
                $userplus_obj->address = $user_params["address"];
                $userplus_obj->city = $user_params["city"];
                $userplus_obj->state = $user_params["state"];
                $userplus_obj->zipcode = $user_params["zipcode"];
                $userplus_obj->country = $user_params["country"];
                $userplus_obj->phone_number = $user_params["phone_number"];

                $data_userplus = array("initial" => $userplus_obj->initial, "first_name" => $userplus_obj->first_name, "last_name" => $userplus_obj->last_name,
                    "address" => $userplus_obj->address, "city" => $userplus_obj->city, "state" => $userplus_obj->state, "zipcode" => $userplus_obj->zipcode,
                    "country" => $userplus_obj->country, "phone_number" => $userplus_obj->phone_number);
                $query_userplus = "user_id= '" . $userId . "'";
                $userplus_obj->updateUserPlus($data_userplus, $query_userplus);

                ///////////edit the BoUser table //////////////////
                ////////////////////////////////////////
                $signature = '<table cellpadding="8" cellspacing="0" width="400px">
                 <tbody>
                  <tr valign="top">
                      <td align="center" bgcolor="#3A3A3A" valign="top"><img src="http://ep-test.edit-place.com/FO/profiles/bo/' . $userId . '/logo.jpg" alt="' . $user_params["first_name"] . ' ' . $user_params["last_name"] . '" height="70" width="70"></td>
                      <td bgcolor="#3A3A3A"><p>' . $user_params["first_name"] . ' ' . $user_params["last_name"] . '<span id="title-sep">&nbsp;/</span>&nbsp;' . $user_params["job_title"] . '&nbsp;<br>
                          <a href="mailto:' . $user_params["email"] . '" id="email-input" onclick="return rcmail.command("compose",' . $user_params["email"] . ',this)">' . $user_params["email"] . '</a><br>
                          <span id="office-sep">Mobile :&nbsp;</span><span id="office-input">' . $user_params["phone_number"] . '</span><br>
                          <span id="office-sep">Bureau :&nbsp;</span><span id="office-input">' . $user_params["telephone"] . '</span><br>
                          <br>
                          <span id="office-sep">Suivez nos actualit&eacute;s sur le&nbsp;</span><span id="office-input"><a href="http://www.edit-place.com/blog/" target="_blank">blog Edit-place !</a></span></p>
                        <a href="http://www.edit-place.com/" target="_blank"><img src="http://www.edit-place.com/extra/signature/logo.png" alt="Edit-place" id="sig-logo" border="0" height="30" width="130"></a></td>
                    </tr>
                   </tbody>
                </table>';
                $bouser_obj->user_id = $userId;
                $bouser_obj->email_personal = $user_params["email_personal"];
                $bouser_obj->birth_date = $user_params["birth_date"];
                $bouser_obj->birth_city = $user_params["birth_city"];
                $bouser_obj->telephone = $user_params["telephone"];
                $bouser_obj->join_date = $user_params["join_date"];
                $bouser_obj->interview = $user_params["interview"];
                $bouser_obj->workplace = $user_params["workplace"];
                $bouser_obj->work_telephone = $user_params["work_telephone"];
                $bouser_obj->yahoo_id = $user_params["yahoo_id"];
                $bouser_obj->skype_id = $user_params["skype_id"];
                $bouser_obj->contact_language = $user_params["contact_language"];
                $bouser_obj->rib = $user_params["rib"];
                $bouser_obj->ssn = $user_params["ssn"];
                $bouser_obj->computer_code = $user_params["computer_code"];
                $bouser_obj->url = $user_params["site_url"];
                $bouser_obj->signature = $signature;
                $bouser_obj->job_title = $user_params["job_title"];
                $bouser_obj->status = 'old';
                $data_bouser = array("email_personal" => $bouser_obj->email_personal, "birth_date" => $bouser_obj->birth_date, "birth_city" => $bouser_obj->birth_city, "telephone" => $bouser_obj->telephone, "join_date" => $bouser_obj->join_date,
                    "interview" => $bouser_obj->interview, "workplace" => $bouser_obj->workplace, "work_telephone" => $bouser_obj->work_telephone, "yahoo_id" => $bouser_obj->yahoo_id, "skype_id" => $bouser_obj->skype_id,
                    "contact_language" => $bouser_obj->contact_language, "rib" => $bouser_obj->rib, "ssn" => $bouser_obj->ssn, "computer_code" => $bouser_obj->computer_code, "url" => $bouser_obj->url, "signature" => $bouser_obj->signature, "job_title" => $bouser_obj->job_title);
                $query_bouser = "user_id= '" . $userId . "'";
                if ($bouserdetials != 'NO') {
                    $bouser_obj->updateBoUser($data_bouser, $query_bouser);
                } else {
                    $bouser_obj->insert();
                }

                ///////////edit the RibBoUser table //////////////////
                $ribbouer_obj = new Ep_User_RibBoUser();
                $ribuser = $ribbouer_obj->getRidDetails($userId);
                if ($ribuser != 'NO')
                    $ribbouer_obj->deleteRibBoUser($userId);
                ////sending the mail to manager who have created this user///
                if ($user_params["computer_code"] != $user_params["original_computer_code"]) {
                    $autoemail_obj = new Ep_Message_AutoEmails();
                    $managerId = $bouserdetials[0]['manager_incharge'];
                    $details['compcode'] = $user_params["computer_code"];
                    $autoemail_obj->sendDetialsToPersonalEmail($managerId, $details);
                }
                //print_r($user_params); exit;
                for ($i = 0; $i <= $user_params["ribcount"]; $i++) {
                    $ribbouer_obj = new Ep_User_RibBoUser();
                    $ribbouer_obj->user_id = $userId;
                    $ribbouer_obj->rib_number = $user_params["rib_num"][$i];
                    $ribbouer_obj->rib_name = $user_params["rib_name"][$i];
                    $ribbouer_obj->rib_file = "";
                    if ($user_params["rib_default"][0] == $i)
                        $ribbouer_obj->default_val = "yes";
                    else
                        $ribbouer_obj->default_val = "no";
                    $ribbouer_obj->insert();
                }

                if ($details[0]['email'] != $_REQUEST['email']) {
                    $changelog_obj->user_id = $_REQUEST['userId'];
                    $changelog_obj->old_email = $details[0]['email'];
                    $changelog_obj->new_email = $_REQUEST['email'];
                    $changelog_obj->changed_by = $this->adminLogin->userId;
                    $changelog_obj->insert();
                }

                $this->_helper->FlashMessenger('Profile Updated Successfully.');
                // $this->_redirect("/user/bo-users?submenuId=ML10-SL3");
                $this->_redirect("/user/user-edit?submenuId=ML10-SL3&userId=" . $userId);

                // $this->render('user_userdetails');
            } catch (Zend_Exception $e) {
                echo $e->getMessage();
                // $this->_helper->FlashMessenger('Profile Updated Failed.');
                $this->_redirect("/user/user-edit?submenuId=ML10-SL3&userId=" . $userId);
            }
        } else {
            $this->render('user_useredit');
        }
    }

    /**function for auto email login***/
    public function emailLoginAction()
    {
        $email_params_login = $this->_request->getParams();
        $this->adminLogin = Zend_Registry::get('adminLogin');
        if ($email_params_login['user'] && $email_params_login['hash']) {
            $user_obj = new Ep_User_User();

            $encrypted_email = $email_params_login['user'];
            $encrypted_password = $email_params_login['hash'];
            $type = $email_params_login['type'];
            $objLog = new Ep_User_User();
            $groupobj = new Ep_User_UserGroupAccess();
            $details = $user_obj->checkEmailLoginDetails($encrypted_email, $encrypted_password, $type);
            if ($details != "NO") {
                $this->_view->loginName = $details[0]['login'];
                $this->adminLogin->loginName = $details[0]['email'];
                $this->adminLogin->language = "fr";
                $this->_view->language = $this->adminLogin->language;
                $this->adminLogin->logStatus = true;
                ////getting all user details after he logged in//////
                $LoggedUserDetails = $objLog->getLoggedUserDetails($details[0]['login']);
                $this->adminLogin->userId = $LoggedUserDetails[0]->identifier;
                $this->adminLogin->groupId = $LoggedUserDetails[0]->groupId;
                $this->adminLogin->type = $LoggedUserDetails[0]->type;
                $this->adminLogin->loginEmail = $LoggedUserDetails[0]->email;
                $this->adminLogin->menuId = explode("|", $LoggedUserDetails[0]->menuId);
                $access = $objLog->getPageId($details[0]['login']);
                $groupaccess = $groupobj->getGroupPageId($details[0]['login']);
                $this->adminLogin->groupmenuId = explode("|", $groupaccess[0]->menuId);
                if ($access[0]->pageId == 0) {
                    $this->adminLogin->groupaccessCode = explode("|", $groupaccess[0]->pageId);
                    $this->adminLogin->accessCode = explode("|", $access[0]->pageId);
                    $this->_view->accessCode = explode("|", $access[0]->pageId);
                    $this->_view->permission = $access[0]->pageId;
                } else {
                    $this->adminLogin->groupaccessCode = explode("|", $groupaccess[0]->pageId);
                    $this->adminLogin->accessCode = explode("|", $access[0]->pageId);
                    $this->_view->accessCode = explode("|", $access[0]->pageId);
                    $this->_view->permission = $access[0]->pageId;
                    $this->adminLogin->permission = $access[0]->pageId;
                }
                //$this->dashinfoAction();/////////to get dashboard information//////
                $this->_redirect("/user/profile-edit?tab=profileedit");

                if (!$this->mainMenu->menuId) {
                    if ($_SERVER["HTTP_REFERER"] == 'admin-ep-test.edit-place.com/index') {
                        //echo $this->adminLogin->userId;
                        $this->_view->left_menu = 'no';
                        $this->_view->render("admin_dashboard");

                    } else {
                        $urlred = str_replace("?target=", "", urldecode(getenv("HTTP_REFERER")));
                        $this->_redirect($urlred);
                    }
                }
            } else {
                $this->_redirect("/");
            }
        } else {
            $this->_redirect("/");
        }

    }

    ///converting the seconds to time
    function secondsToTime($seconds)
    {
        $seconds = str_replace("-", "", $seconds);
        // extract hours
        $hours = floor($seconds / (60 * 60));

        // extract minutes
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);

        // extract the remaining seconds
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);

        // return the final array
        $obj = array(
            "h" => (int)$hours,
            "m" => (int)$minutes,
            "s" => (int)$seconds,
        );
        return (int)$hours . "(H)" . (int)$minutes . "(M)" . (int)$seconds . "(S)";
    }

    ///////////edit the contributor/////////////////
    public function contributorEditAction()
    {
        $user_obj = new Ep_User_User();
        $changelog_obj = new Ep_User_ProfileChangeLog();
        $experience_obj = new EP_User_ContributorExperience();
        $mail_obj = new Ep_Message_AutoEmails();
        $userId = $this->_request->getParam('userId');
        $user_details = $user_obj->getContributordetails($userId);
        /* *getting User expeience details**/
        $jobDetails = $experience_obj->getExperienceDetails($userId, 'job');
        if ($jobDetails != "NO")
            $this->_view->jobDetails = $jobDetails;
        $educationDetails = $experience_obj->getExperienceDetails($userId, 'education');
        if ($educationDetails != "NO")
            $this->_view->educationDetails = $educationDetails;
        /* *iNOVICE inFO ***/
        $this->_view->payment_type = $user_details[0]['payment_type'];
        $this->_view->pay_info_type = $user_details[0]['pay_info_type'];
        $this->_view->SSN = $user_details[0]['SSN'];
        $this->_view->company_number = $user_details[0]['company_number'];
        $this->_view->vat_check = $user_details[0]['vat_check'];
        $this->_view->VAT_number = $user_details[0]['VAT_number'];
        /* *Paypal and RIB info**/
        $this->_view->paypal_id = $user_details[0]["paypal_id"];
        $RIB_ID = explode("|", $user_details[0]["rib_id"]);
        if (($user_details[0]['pay_info_type'] == 'out_france' || $user_details[0]['country'] != 38) && count($RIB_ID) == 2) {
            $this->_view->rib_id_6 = $RIB_ID[0];
            $this->_view->rib_id_7 = $RIB_ID[1];
        } else {
            $this->_view->rib_id_1 = $RIB_ID[0];
            $this->_view->rib_id_2 = $RIB_ID[1];
            $this->_view->rib_id_3 = $RIB_ID[2];
            $this->_view->rib_id_4 = $RIB_ID[3];
            $this->_view->rib_id_5 = $RIB_ID[4];
        }
        //////edit contributor////////////////////////////////////
        $this->_view->user_detail = $user_details;   //echo "<pre>";print_r($user_details); exit;
        $this->_view->self_details = utf8_encode($user_details[0]['self_details']);
        $this->_view->stats = $_GET['stats'];
        $this->_view->loguser = $this->adminLogin->userId;
        $this->_view->category_more = unserialize($user_details[0]['category_more']);
        $this->_view->language_more = unserialize($user_details[0]['language_more']);
        if ($this->_request->getParam('submit_contrib') != '') {
            $user_obj->updatecontribUser($_REQUEST);
            /* if($user_details[0]['email'] != $_REQUEST['email'])
             {
                 $changelog_obj->user_id=$_REQUEST['userId'];
                 $changelog_obj->old_email=$user_details[0]['email'];
                 $changelog_obj->new_email=$_REQUEST['email'];
                 $changelog_obj->changed_by=$this->adminLogin->userId;
                 $changelog_obj->insert();
             }*/
            $this->updateExperienceDetails($_REQUEST, 'job');
            $this->updateExperienceDetails($_REQUEST, 'education');
            //If profile type changed from junior to senior
            if ($_REQUEST['profile_type'] == 'senior' && $_REQUEST['prev_profile'] == 'junior') {
                // $parameters['jc_limit']=$this->getConfiguredval('jc_limit');
                // $parameters['sc_limit']=$this->getConfiguredval('sc_limit');
                $mail_obj->messageToEPMail($userId, 30, $parameters);
            }
            //If profile type2 in corrector aspect changed from junior to senior
            if ($_REQUEST['type2'] == 'yes' && $_REQUEST['profile_type2'] == 'senior' && $_REQUEST['prev_profile2'] == 'junior') {
                $mail_obj->messageToEPMail($userId, 110, $parameters);
            }
            $this->_helper->FlashMessenger('Profile Updated Successfully.');
            $this->_redirect("/user/contributor-edit?submenuId=ML10-SL1&tab=editcontrib&userId=" . $userId);
        }
        //////view contributor////////////////////////////////////
        $this->_view->ep_contrib_profile_language_more = explode(",", $user_details[0]['language_more']);
        $this->_view->ep_contrib_profile_category = explode(",", $user_details[0]['favourite_category']);
        $this->_view->self_details = utf8_encode($user_details[0]['self_details']);
        $this->_view->stats = $_GET['stats'];
        $this->_view->loguser = $this->adminLogin->userId;
        $contrib_picture_path = "/home/sites/site5/web/FO/profiles/contrib/pictures/" . $user_details[0]['user_id'] . "/" . $user_details[0]['user_id'] . "_h.jpg";
        if (file_exists($contrib_picture_path))
            $contrib_picture_path = "http://ep-test.edit-place.com/FO/profiles/contrib/pictures/" . $user_details[0]['user_id'] . "/" . $user_details[0]['user_id'] . "_h.jpg";
        else
            $contrib_picture_path = "http://ep-test.edit-place.com/FO/images/Contrib/profile-img-def.png";
        $this->_view->user_pic = $contrib_picture_path;

        $expCat = explode(',', preg_replace("/\([^)]+\)/", "", $this->unserialisearray($user_details[0]['category_more'])));
        preg_match_all('#\((.*?)\)#', $this->unserialisearray($user_details[0]['category_more']), $match);

        foreach ($expCat as $key => $value) {
            $impCat[] = $this->category_array[$value] . '(' . $match[1][$key] . ')';
        }
        $user_details[0]['category_more'] = implode(',', $impCat);
        unset($impCat);

        $user_details[0]['language_more'] = $this->unserialisearray($user_details[0]['language_more']);
        $workexpDetails = $user_obj->getExperienceDetails($user_details[0]['user_id'], 'job');
        if ($workexpDetails != "NO") {
            $ecnt = 0;
            foreach ($workexpDetails as $workexp) {
                $workexpDetails[$ecnt]['start_date'] = date('FY', strtotime($workexp['from_year'] . "-" . $workexp['from_month']));
                if ($workexp['still_working'] == 'yes')
                    $workexpDetails[$ecnt]['end_date'] = 'Actuel';
                else
                    $workexpDetails[$ecnt]['end_date'] = date('FY', strtotime($workexp['to_year'] . "-" . $workexp['to_month']));
                $ecnt++;
            }
            $this->_view->educationDetailsview = $workexpDetails;
        }
        //echo "<pre>"; print_r($user_details);    exit;
        $this->_view->user_detail = $user_details;
        $this->_view->country_name = $this->country_array[$user_details[0]['country']];
        $this->_view->ep_contrib_profile_language_more = explode(",", $user_details[0]['language_more']);
        $this->_view->ep_contrib_profile_category = explode(",", $user_details[0]['favourite_category']);
        $this->_view->profession = $this->profession_array[$user_details[0]['profession']];
        $this->_view->language = $this->language_array[$user_details[0]['language']];
        $this->_view->nationality = $this->nationality_array[$user_details[0]['nationality']];
        $this->_view->self_details = utf8_encode(strip_tags($user_details[0]['self_detailss']));
        //lang_more str
        $language_more = "";
        if ($user_details[0]['language_more'] != NULL) {
            $lang_more = explode(",", $user_details[0]['language_more']);

            if (count($lang_more) > 0) {
                for ($l = 0; $l < count($lang_more); $l++) {
                    $language_more .= $this->language_array[$lang_more[$l]];

                    if ($l != count($lang_more) - 1)
                        $language_more .= ",";
                }
            }
        }
        $this->_view->language_more1 = $language_more;
        //fav category str
        $favourite_category = "";
        if ($user_details[0]['favourite_category'] != NULL) {
            $fav_cat = explode(",", $user_details[0]['favourite_category']);

            if (count($fav_cat) > 0) {
                for ($l = 0; $l < count($fav_cat); $l++) {
                    $favourite_category .= $this->category_array[$fav_cat[$l]];

                    if ($l != count($fav_cat) - 1)
                        $favourite_category .= ",";
                }
            }
        }
        $this->_view->favourite_category = $favourite_category;
        $eduarray = array("1" => "Bac +1", "2" => "Bac +2", "3" => "Bac +3", "4" => "Bac +4", "5" => "Bac +5", "6" => "Bac+5 et plus");
        $this->_view->education = $eduarray[$user_details[0]['education']];

        /////////////contributor statistics /////////////////////
        $stats_params = $this->_request->getParams();
        $user_obj = new EP_User_Contributor();
        $participate_obj = new EP_Participation_Participation();
        $params['userid'] = $userId;

        /////////////writer graph part ///////////
        $params['userid'] = $userId;
        $marksforarts = $participate_obj->getMarksWriterGraph($params, 'writer'); //print_r($marksforarts); exit;
        if ($marksforarts != 'NO') {

            $this->_view->dates = "var d2 = [
             ";
            if ($marksforarts)
                foreach ($marksforarts[0] as $key => $val) {
                    //echo "<pre>".$key = substr($key, 0, -3);
                    $key = substr($key, 0, -3);
                    if ($val == '')
                        $val = 0;
                    $this->_view->dates .= "[new Date('" . $key . "').getTime()," . $val . "],
                 ";
                }
            $this->_view->dates .= "];
             ";  //exit;
            //  echo $this->_view->dates; exit;
        } else {
            $this->_view->dates = "var d2 = [ " . date('Y-m') . " ];";
        }
        /////////end of graph /////////////////////////
        /////////////corrector graph part ///////////
        $crtmarksforarts = $participate_obj->getMarksWriterGraph($params, 'corrector'); //print_r($marksforarts); exit;
        if ($crtmarksforarts != 'NO') {
            $this->_view->crtdates = "var d2 = [
             ";
            if ($crtmarksforarts)
                foreach ($crtmarksforarts[0] as $key => $val) {
                    //echo "<pre>".$key = substr($key, 0, -3);
                    $key = substr($key, 0, -3);
                    if ($val == '')
                        $val = 0;
                    $this->_view->crtdates .= "[new Date('" . $key . "').getTime()," . $val . "],
                 ";
                }
            $this->_view->crtdates .= "];
             ";  //exit;
        } else {
            $this->_view->crtdates = "var d2 = [ " . date('Y-m') . " ];";
        }
        /////////end of corrector graph /////////////////////////
        $noarts = $participate_obj->getParticipantsStatistics($params);
        $clientnamesarray = $participate_obj->getParticipantClientLIst($params);
        if ($clientnamesarray != 'NO') {
            foreach ($clientnamesarray AS $key => $value) {
                $clientnames[$key] = $clientnamesarray[$key]['company_name'];
            }
            $this->_view->client_list = implode(",", $clientnames);
        } else
            $this->_view->client_list = 0;
        $marks = 0;
        if ($noarts != '') {
            $this->_view->selected_arts = $noarts[0]['selectedCount'];
            $this->_view->refused_arts = $noarts[1]['refusedCount'];
            $this->_view->proccesing_arts = $noarts[2]['inprocessCount'];
            $this->_view->total_parts = $noarts[3]['totalCount'];
            $this->_view->published_arts = $noarts[4]['publishedCount'];
            $this->_view->republished = $noarts[5]['republishedCount'];
            $this->_view->arts_notsubmited = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['tempreasons'] != '') {
                $tempreasons = $this->formRefusalReasons($noarts[7]['tempreasons'], 'frequent');
                $this->_view->freqtempreason = $tempreasons[0];
                $this->_view->freqtempreasoncount = $tempreasons[1];
            } else {
                $this->_view->freqtempreason = 'nill';
                $this->_view->freqtempreasoncount = 0;
            }
            $this->_view->total_refusalreasons = $noarts[8]['totaltemplateCount'];  // total count of refusal reasons
            if ($clientnamesarray != 'NO')
                $this->_view->num_clients = count($clientnamesarray);
            else
                $this->_view->num_clients = 0;
            if ($noarts[9]['permreason'] != '') {
                $permreasons = $this->formRefusalReasons($noarts[9]['permreason'], 'frequent');
                $this->_view->freqpermreason = $permreasons[0];
                $this->_view->freqpermreasoncount = $permreasons[1];
            } else {
                $this->_view->freqpermreason = 'nill';
                $this->_view->freqpermreasoncount = 0;
            }

        }
        /////////////////////////////
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $noarts = $crtparticipate_obj->getCorrectorParticipantsStatistics($params);
        $writersarray = $crtparticipate_obj->getCrtParticipantWtitersLIst($params); // print_r($noarts);exit;

        $marks = 0;
        if ($noarts != 'NO') {
            $this->_view->crtselected_arts = $noarts[0]['selectedCount'];
            $this->_view->crtrefused_arts = $noarts[1]['refusedCount'];
            $this->_view->crtproccesing_arts = $noarts[2]['inprocessCount'];
            $this->_view->crttotal_parts = $noarts[3]['totalCount'];
            $this->_view->crtpublished_arts = $noarts[4]['publishedCount'];
            $this->_view->crtrepublished = $noarts[5]['republishedCount'];
            $this->_view->crtarts_notsubmited = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['crttempreasons'] != '') {
                $tempreasons = $this->formRefusalReasons($noarts[7]['crttempreasons'], 'frequent');
                $this->_view->freqcrttempreason = $tempreasons[0];
                $this->_view->freqcrttempreasoncount = $tempreasons[1];
            } else {
                $this->_view->freqcrttempreason = 'nill';
                $this->_view->freqcrttempreasoncount = 0;
            }

            $this->_view->crttotal_refusalreasons = $noarts[8]['totaltemplateCount'];  // total count of refusal reasons
            $this->_view->crtnum_writers = count($writersarray);
            if ($noarts[9]['crtpermreasons'] != '') {
                $permreasons = $this->formRefusalReasons($noarts[9]['crtpermreasons'], 'frequent');
                $this->_view->freqcrtpermreason = $permreasons[0];
                $this->_view->freqcrtpermreasoncount = $permreasons[1];
            } else {
                $this->_view->freqcrtpermreason = 'nill';
                $this->_view->freqcrtpermreasoncount = 0;
            }
        }
        /////get all the articles in writer aspect/////////////////////////////////////////////////////
        $client_obj = new Ep_User_Client();
        $user_obj = new Ep_User_User();
        $articleprocess_obj = new EP_Delivery_ArticleProcess();
        $searchParams = $this->_request->getParams();
        $res = $participate_obj->writerStatsArticles($userId, $searchParams);
        if ($res != 'NO') {
            foreach ($res as $key1 => $value1) {
                $client = $client_obj->getClientName($res[$key1]['clientid']);
                $res[$key1]['client'] = $client[0]['company_name'];
                $res[$key1]['language'] = utf8_encode($this->language_array[$res[$key1]['language']]);
                $res[$key1]['category'] = utf8_encode($this->category_array[$res[$key1]['category']]);
                $marksdetails = $articleprocess_obj->getFristVersionDate($res[$key1]['id']);
                if ($marksdetails != 'NO')
                    $res[$key1]['marks'] = $marksdetails[0]['marks'];
                $users = $user_obj->getAllUsersDetails($res[$key1]['created_user']);
                $res[$key1]['projectManager'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                $parts = $participate_obj->crtParticipationDetails($res[$key1]['id']); //print_r($parts); exit;
                if ($parts != 'NO') {
                    $users = $user_obj->getAllUsersDetails($parts[0]['corrector_id']);
                    $res[$key1]['corrector'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                }
                if ($res[$key1]['status'] == 'bid')
                    $res[$key1]['lateststatus'] = "En cours de redaction";
                if ($res[$key1]['status'] == 'under_study')
                    $res[$key1]['lateststatus'] = "En relecture client";
                if ($res[$key1]['status'] == 'disapprove_client')
                    $res[$key1]['lateststatus'] = "Refus client";
                if ($res[$key1]['status'] == 'published')
                    $res[$key1]['lateststatus'] = "Valid&eacute;";
                if ($res[$key1]['status'] == 'closed_client_temp')
                    $res[$key1]['lateststatus'] = "Ferm&eacute; client";
                if ($res[$key1]['status'] == 'plag_exec')
                    $res[$key1]['lateststatus'] = "Plagiarism";
                //else
                // $res[$key1]['lateststatus'] = $res[$key1]['status'];

            }
        }
        // print_r($res); exit;
        $this->_view->writerstatsarticles = $res;
        //////////////get all aritcle in corrector aspect
        $crtres = $crtparticipate_obj->crtStatsArticles($userId, $searchParams);
        if ($crtres != 'NO') {
            foreach ($crtres as $key1 => $value1) {
                if ($crtres[$key1]['participate_id'] != '') {
                    $client = $client_obj->getClientName($crtres[$key1]['clientid']);
                    $crtres[$key1]['client'] = $client[0]['company_name'];
                    $crtres[$key1]['language'] = utf8_encode($this->language_array[$crtres[$key1]['language']]);
                    $crtres[$key1]['category'] = utf8_encode($this->category_array[$crtres[$key1]['category']]);
                    $marksdetails = $articleprocess_obj->getMarksByUser($crtres[$key1]['participate_id'], $userId);
                    if ($marksdetails != 'NO')
                        $crtres[$key1]['marks'] = $marksdetails[0]['marks'];
                    $users = $user_obj->getAllUsersDetails($crtres[$key1]['created_user']);
                    $crtres[$key1]['projectManager'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                    $parts = $participate_obj->getParticipateDetails($crtres[$key1]['participate_id']);
                    /*edited by naseer on 15-09-2015*/
                    if ($parts != 'NO')
                        $users = $user_obj->getAllUsersDetails($parts[0]['user_id']);
                    /*end of edited by naseer on 15-09-2015*/
                    $crtres[$key1]['writer'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                    $crtres[$key1]['userId'] = $userId;
                    if ($crtres[$key1]['status'] == 'bid')
                        $crtres[$key1]['lateststatus'] = "En cours de redaction";
                    if ($crtres[$key1]['status'] == 'under_study')
                        $crtres[$key1]['lateststatus'] = "En relecture client";
                    if ($crtres[$key1]['status'] == 'disapprove_client')
                        $crtres[$key1]['lateststatus'] = "Refus client";
                    if ($crtres[$key1]['status'] == 'published')
                        $crtres[$key1]['lateststatus'] = "Valid&eacute;";
                    if ($crtres[$key1]['status'] == 'closed_client_temp')
                        $crtres[$key1]['lateststatus'] = "Ferm&eacute; client";
                    if ($crtres[$key1]['status'] == 'plag_exec')
                        $crtres[$key1]['lateststatus'] = "Plagiarism";
                }
            }
        }
        //print_r($res); exit;
        $this->_view->crtstatsarticles = $crtres;
        $this->_view->render("user_contributoredit");
    }

    ///////////edit the contributor/////////////////`
    public function briefContributorEditAction(){
        $user_obj = new Ep_User_User();
        $changelog_obj = new Ep_User_ProfileChangeLog();
        $userId = $this->_request->getParam('userId');
        $user_details = $user_obj->getContributordetails($userId);
        //////edit contributor////////////////////////////////////
        $this->_view->user_detail = $user_details;   //print_r($user_details); exit;

        if ($this->_request->getParam('submit_contrib') != '') {
            $user_obj->updateBriefContribUser($_REQUEST);
            if ($user_details[0]['email'] != $_REQUEST['email']) {
                $changelog_obj->user_id = $_REQUEST['userId'];
                $changelog_obj->old_email = $user_details[0]['email'];
                $changelog_obj->new_email = $_REQUEST['email'];
                $changelog_obj->changed_by = $this->adminLogin->userId;
                $changelog_obj->insert();
            }
            $this->_helper->FlashMessenger('Profile Updated Successfully.');
            //$this->_redirect("/user/contributor-edit?submenuId=ML10-SL1&tab=briefeditcontrib&userId=".$userId);
            $this->_redirect("/user/search-contributors?submenuId=ML10-SL6");
        }
        $this->_view->render("user_briefwriteredit");
    }

    ////form the refusal reasons //////
    public function formRefusalReasons($reasons, $type)
    {
        $result = array();
        $temp_obj = new Ep_Message_Template();
        $reasonsarray = explode(",", $reasons);
        $reasonsarray = array_filter($reasonsarray);
        if ($type == 'frequent') {
            $arr = array_count_values($reasonsarray);
            array_filter($arr);
            $maxoccurance = max($arr);
            $maxoccurance_title ='';
            $total_maxoccurance ='';
            foreach($arr as $key => $value){
                if($value === $maxoccurance){
                    $reason = $temp_obj->getValTempTitle($key);
                    $maxoccurance_title .= utf8_encode($reason[0]['title']).'('.$value.'), ';
                    $total_maxoccurance += $value;
                }
            }
            $result[0] = $maxoccurance_title;
            $result[1] = $total_maxoccurance;
            return $result;
        } else {
            $arr = array_count_values($reasonsarray);
            arsort($arr);
            foreach ($arr AS $key => $value) {
                $rtitle = $temp_obj->getValTempTitle($key);
                $result[$rtitle[0]['title']] = $value;
            }
            return $result;
        }

    }
    /* Inserting or Updating User Experince**/
    /*edited by naseer on 14-09-2015 */
    public function updateExperienceDetails($profile_params, $type)
    {
        $experience_obj = new EP_User_ContributorExperience();
        /* *Inserting or Updating User Experince**/
        $contrib_identifier = $profile_params['userId'];
        if ($type == 'job')
            $details = $profile_params['job_title'];
        else if ($type == 'education')
            $details = $profile_params['training_title'];

        //print_r($profile_params); exit;
        if (count($details) > 0) {
            foreach ($details as $key => $title) {
                if ($type == 'job') {
                    $institute = $profile_params['job_institute'][$key];
                    $contract = $profile_params['ep_job'][$key];
                    $start_month = $profile_params['start_month'][$key];
                    $start_year = $profile_params['start_year'][$key];
                    $end_month = $profile_params['end_month'][$key];
                    $end_year = $profile_params['end_year'][$key];
                    $still_working = $profile_params['still_working'][$key];
                    $job_identifier = $profile_params['job_identifier'][$key];

                    $condition = $title && $institute && $contract && $start_month && $start_year && (($end_month && $end_year) || $still_working);
                } else if ($type == 'education') {
                    $institute = $profile_params['training_institute'][$key];
                    $start_month = $profile_params['start_train_month'][$key];
                    $start_year = $profile_params['start_train_year'][$key];
                    $end_month = $profile_params['end_train_month'][$key];
                    $end_year = $profile_params['end_train_year'][$key];
                    $still_working = $profile_params['still_training'][$key];
                    $contract = '';
                    $job_identifier = $profile_params['training_identifier'][$key];

                    $condition = $title && $institute && $start_month && $start_year && (($end_month && $end_year) || $still_working);
                }
                if ($condition) {
                    $experience_obj->user_id = $contrib_identifier;
                    $experience_obj->title = utf8_decode($title);
                    $experience_obj->institute = utf8_decode($institute);
                    $experience_obj->contract = $contract;
                    $experience_obj->type = $type;
                    $experience_obj->from_month = $start_month;
                    $experience_obj->from_year = $start_year;
                    if ($still_working) {
                        $experience_obj->still_working = 'yes';
                        /*$experience_obj->to_month='0';
                        $experience_obj->to_year='0';*///commented by naseer on 13-11-2015//
                    } else {
                        $experience_obj->to_month = $end_month;
                        $experience_obj->to_year = $end_year;
                        $experience_obj->still_working = 'no';
                    }
                    //echo "<pre>";print_r($experience_obj);exit;
                    if ($job_identifier != '') {

                        $experience_obj->updated_at = date('Y-m-d h:i:s');
                        $experience_obj->identifier = $job_identifier;
                        $updateExperienceArray = $experience_obj->loadintoArray();
                        unset($updateExperienceArray['identifier']);
                        if ($still_working) {
                            unset($updateExperienceArray['to_month']);
                            unset($updateExperienceArray['to_year']);
                        }


                        /*added by naseer on 09-11-2015*/
                        //fetch the old values saved in the datbase//
                        $jobDetails = $experience_obj->getIndividualExperienceDetails($job_identifier, $type);
                        $this->updateuserlogs($jobDetails[0], $updateExperienceArray);

                        /*end of added by naseer on 09-11-2015*/

                        $response1 = $experience_obj->updateExperience($updateExperienceArray, $job_identifier);
                        //return ($response1) ? '1' : '0';
                    } else {
                        $experience_obj->insert();
                    }

                }
            }
        }

    }

    public function deleteProfileDataAction()
    {
        $profile_params = $this->_request->getParams();
        $experience_obj = new Ep_User_ContributorExperience();

        if ($profile_params['type'] && $profile_params['identifier']) {
            $identifier = $profile_params['identifier'];
            if ($profile_params['type'] == 'education' || $profile_params['type'] == 'job') {
                $experience_obj->deleteExperience($identifier);
            }
        }
    }

    ///////////edit the client/////////////////
    public function clientEditAction()
    {
        $user_obj = new Ep_User_User();
        $changelog_obj = new Ep_User_ProfileChangeLog();
        $ao_obj = new Ep_Delivery_Delivery();
        $mail_obj = new Ep_Message_AutoEmails();
        $userId = $this->_request->getParam('userId');

        $user_details = $user_obj->getUserdetails($userId);
        $this->_view->user_detail = $user_details;

        //////edit client/////////////////////////////////
        //Favourite contributors
        $favcontribslist = array();
        $favcontribs = $user_obj->ListallfavContribs($userId);
        for ($f = 0; $f < count($favcontribs); $f++)
            $favcontribslist[] = $favcontribs[$f]['identifier'];

        $this->_view->favcontribslist = $favcontribslist;

        //List of contributors
        $contribslist = array();
        $user_array = $user_obj->listusers('2');
        for ($u = 0; $u < count($user_array); $u++) {
            $name = $user_array[$u]['email'];
            $nameArr = array($user_array[$u]['company_name'], $user_array[$u]['first_name'], $user_array[$u]['last_name']);
            $nameArr = array_filter($nameArr);

            if (count($nameArr) > 0)
                $name .= "(" . implode(", ", $nameArr) . ")";
            $contribslist[$user_array[$u]['identifier']] = strtoupper($name);
        }
        $this->_view->contribslist = $contribslist;

        //Client contact
        $this->_view->clcontacts = $user_obj->getclientcontact($userId);


        if ($this->_request->getParam('submit_client') != '') {   //echo $user_details[0]['email'];  print_r($_REQUEST); exit;
            $userId = $this->_request->getParam('userId');
            $user_details = $user_obj->getUserdetails($userId);
            if ($user_details[0]['paypercent'] != $_REQUEST['paypercent']) {
                $user_obj->updatePaypercentChangeLog($userId, $this->adminLogin->userId);
            }
            $user_obj->updateclientUser($_REQUEST);
            if ($user_details[0]['email'] != $_REQUEST['email']) {
                $changelog_obj->user_id = $_REQUEST['userId'];
                $changelog_obj->old_email = $user_details[0]['email'];
                $changelog_obj->new_email = $_REQUEST['email'];
                $changelog_obj->changed_by = $this->adminLogin->userId;
                $changelog_obj->insert();
            }
            $this->_helper->FlashMessenger('Profile Updated Successfully.');
            $this->_redirect("/user/client-edit?submenuId=ML10-SL2&tab=editclient&userId=" . $userId);
        }

        //////////////view client/////////////////////////////////////////
        $client_picture_path = "/home/sites/site5/web/FO/profiles/clients/logos/" . $user_details[0]['identifier'] . "/" . $user_details[0]['identifier'] . "_global.png";
        if (file_exists($client_picture_path))
            $client_picture_path = "http://ep-test.edit-place.com/FO/profiles/clients/logos/" . $user_details[0]['identifier'] . "/" . $user_details[0]['identifier'] . "_global.png";
        else
            $client_picture_path = "http://ep-test.edit-place.com/FO/images/Contrib/profile-img-def.png";
        $this->_view->user_pic = $client_picture_path;
        //Favourite comtributors
        $favlist = $user_obj->ListallfavContribs($userId);

        $favcontrib = array();
        for ($f = 0; $f < count($favlist); $f++) {
            if ($favlist[$f]['first_name'] != "")
                $favcontrib[] = $favlist[$f]['first_name'] . '&nbsp;' . $favlist[$f]['last_name'];
            else
                $favcontrib[] = $favlist[$f]['email'];
        }
        $this->_view->favcontributors = implode(", ", $favcontrib);
        $this->_view->country_name = $this->country_array[$user_details[0]['country']];

        ///////////////////client aos list /////////////////////////////////////
        $payment_obj = new Ep_Payment_Payment();

        $ao = $ao_obj->getAOviewinfo($userId);
        if ($ao != '') {
            $i = 0;
            do {
                $details = $payment_obj->getInvoices($ao[$i]['id']);
                if (file_exists('/home/sites/site5/web/FO/invoice/client/' . $details[0]['user_id'] . '/' . $details[0]['invoice_id'] . '.pdf')) :
                    $ao[$i]['inv'] = 1;
                else :
                    $ao[$i]['inv'] = 0;
                endif;

                $i++;
            } while ($i < sizeof($ao));
            $this->_view->ao = $ao;
        }

        ///////////////////////////////////Client Invoices////////////////////////////////
        $montharray = array("1" => "January", "2" => "February", "3" => "March", "4" => "April", "5" => "May", "6" => "June", "7" => "July", "8" => "August", "9" => "September", "10" => "October", "11" => "November", "12" => "December");
        $this->_view->montharray = $montharray;
        $this->_view->month = date("m");
        $this->_view->year = date("Y");

        $pay_obj = new Ep_Payment_PaymentArticle();

        $invarray = array();
        for ($i = 1; $i <= $this->_view->month; $i++)
            $invarray[$i] = count($pay_obj->getPaymentPremium($_GET['userId'], $i, $this->_view->year));
        $this->_view->invarray = $invarray;

        $paymentarticlelist = $pay_obj->getPaymentPremiumao($_GET['userId'], $this->_view->year);
        $this->_view->paymentarticlelist = $paymentarticlelist;

        $this->_view->render("user_clientedit");
    }

    /*
    public function getPagesAction()
    {
         $userdetails=new Ep_User_UserPlus();
         $details= $userdetails->getUsersDetails();
         $this->_view->Userdetails=$details;
         $this->render('user_userdetails');
    }
    */

    /////////permissions to users/////////
    public function permissionsAction()
    {
        $p = new Ep_Controller_Page($this->pageFile);
        ////getting groups///////
        $group_obj = new Ep_User_UserGroupAccess();
        $groups = $group_obj->getAllGroups();
        $this->_view->groupList = $groups;
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getUsers();
        $this->_view->userList = $users;
        /////////////////////////////////////
        if ($this->_request->getParam('tab') == 'permissionstab') {
            $sel_group = $this->_request->getPost('sel_group');
            $sel_user = $this->_request->getPost('sel_user');
            $this->_view->GpSel = $sel_group;
            $this->_view->UsrSel = $sel_user;
            $total = $this->_request->getPost('hid_totalrows');///no of pages///
            $to_sel_user = $this->_request->getPost('to_sel_user');

            if ($this->_request->getPost('assign') == 'assign') {
                /*echo $this->_request->getParam('selectedpages'); exit;
                  echo $this->_request->getParam('chk'); exit;
                  for( $i=1 ; $i<=$total; $i++)
                  {
                      //chk_.$i = $this->_request->getParam('chk_',$i);
                      if($this->_request->getParam('chk_'.$i) == 'chk_'.$i)
                      {
                          $chkPages[] = $this->_request->getParam('chk_',$i);
                          $chkPagesIds[] = $i;
                      }
                  }
                  $chkedpageIds = implode ("|",$chkPagesIds);*/
                $selectedpages = $this->_request->getPost('selectedpages');
                $selectedpages = str_replace("on,", "", $selectedpages);
                $chkedpageIds = str_replace(",", "|", $selectedpages);
                // echo $chkedpageIds; exit;
                if ($sel_user != '0') {
                    $data = array("pageId" => $chkedpageIds);////////updating
                    $query = "identifier= '" . $sel_user . "'";
                    $user_obj->updateUser($data, $query);
                } else if ($sel_group != '0') {
                    $data = array("pageId" => $chkedpageIds);
                    $query = "id= '" . $sel_group . "'";
                    $group_obj->updateGroup($data, $query);
                }
                $this->_helper->FlashMessenger('Permissions updated Successfully.');
            }
            //////for groups
            if ($sel_group != '') {
                $grouppage = $group_obj->getGroup($sel_group);
                $this->_view->pageGpSel = explode("|", $grouppage[0]->pageId);

            }
            /////for users
            if ($sel_user != '') {
                $userpage = $user_obj->getUserPages($sel_user);
                $this->_view->pageUsrSel = explode("|", $userpage[0]->pageId);
            }
            if ($this->_request->getPost('permissionpassto') == 'yes') {
                /*for( $i=1 ; $i<=$total; $i++)
                            {
                                //chk_.$i = $this->_request->getParam('chk_',$i);
                                if($this->_request->getParam('chk_'.$i) == 'chk_'.$i)
                                {
                                    $chkPages[] = $this->_request->getParam('chk_',$i);
                                    $chkPagesIds[] = $i;
                                }
                            }
                            $chkedpageIds = implode ("|",$chkPagesIds);*/
                $selectedpages = $this->_request->getPost('selectedpages');
                $selectedpages = str_replace("on,", "", $selectedpages);
                $chkedpageIds = str_replace(",", "|", $selectedpages);
                for ($i = 0; $i < count($to_sel_user); $i++) {
                    $data = array("pageId" => $chkedpageIds);////////updating
                    $query = "identifier= '" . $to_sel_user[$i] . "'";
                    $user_obj->updateUser($data, $query);
                }
                $this->_helper->FlashMessenger('Permissions Inherited Successfully.');
            }
        }

        $pageList = $p->selectAllPagesBySegment(1);
        $this->_view->pageList = $pageList;
        $this->render('user_permissions');
    }

    public function sendpasswordtomailAction()
    {
        $autoemail_obj = new Ep_Message_AutoEmails();
        $userid = $this->_request->getParam('userId');
        if ($userid != '') {
            $details['password'] = "password";
            $autoemail_obj->sendDetialsToPersonalEmail($userid, $details);
            exit;
        }
    }

    /////////permissions to users/////////
    public function menuPermissionsAction()
    {
        ////getting groups///////
        $group_obj = new Ep_User_UserGroupAccess();
        $groups = $group_obj->getAllGroups();
        $this->_view->groupList = $groups;
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getUsers();
        $this->_view->userList = $users;

        /////////////////////////////////////
        if ($this->_request->getParam('tab') == 'permissionstab') {
            $sel_group = $this->_request->getParam('sel_group');
            $sel_user = $this->_request->getParam('sel_user');
            $this->_view->GpSel = $sel_group;
            $this->_view->UsrSel = $sel_user;
            if ($this->_request->getParam('assign') == 'assign') {
                $menus = $this->_request->getParam('menus');
                $menucookie = str_replace("chkmenu=", "", $menus);
                $chkedmenuIds = str_replace("&", "|", $menucookie);

                if ($sel_user != '0') {
                    $data = array("menuId" => $chkedmenuIds);////////updating
                    $query = "identifier= '" . $sel_user . "'";
                    $user_obj->updateUser($data, $query);
                } else if ($sel_group != '0') {
                    $data = array("menuId" => $chkedmenuIds);
                    $query = "id= '" . $sel_group . "'";
                    $group_obj->updateGroup($data, $query);
                }
                $this->_helper->FlashMessenger('Permissions updated Successfully.');

            }
            //////for groups
            if ($sel_group != '') {
                $grouppage = $group_obj->getGroup($sel_group);
                if ($grouppage != '')
                    $this->_view->menuGpSel = explode("|", $grouppage[0]->menuId);
            }
            /////for users
            if ($sel_user != '') {
                $userpage = $user_obj->getUserMenus($sel_user);
                if ($userpage != '')
                    $this->_view->menuUsrSel = explode("|", $userpage[0]->menuId);
            }
        }
        $this->render('user_menu_permissions');
    }

    /////////permissions to users/////////
    public function menuPermissionspopupAction()
    {
        $user_obj = new Ep_User_User();
        $sel_user = $this->_request->getParam('sel_user');
        /////for users
        if ($sel_user != 'no') {
            $userdetails = $user_obj->getAllUsersDetails($sel_user);
            $userpage = $user_obj->getUserMenus($sel_user);
            if ($userpage != '') {
                $this->_view->menuUsrSel = explode("|", $userpage[0]->menuId);
                $this->_view->username = $userdetails[0]['first_name'] . ' ' . $userdetails[0]['last_name'];
            }
        } else {
            $this->_view->menuUsrSel = array();
            $this->_view->username = 'no';
        }


        $this->render('user_permissionspopup');
    }

    /////////////////////display pop up with users list in dashboard user stats///////////////////
    public function dashboarduserspopupAction()
    {
        $userobj = new Ep_User_User();
        if ($this->_request->getParam('usertype') == 5) {
            $this->_view->usernames = $userobj->getBoEditorNames();
            $this->_view->usertype = "Editors";
        } else if ($this->_request->getParam('usertype') == 4) {
            $this->_view->usernames = $userobj->getBoChiefEditorNames();
            $this->_view->usertype = "Chief Editors";
        } else if ($this->_request->getParam('usertype') == 6) {
            $this->_view->usernames = $userobj->getBoSeoNames();
            $this->_view->usertype = "Seo Team";
        } else if ($this->_request->getParam('usertype') == 2) {
            $this->_view->usernames = $userobj->getBoCeoNames();
            $this->_view->usertype = "Ceo Team";
        } else if ($this->_request->getParam('usertype') == 8) {
            $this->_view->usernames = $userobj->getBoPartnerNames();
            $this->_view->usertype = "Partners";
        } else if ($this->_request->getParam('usertype') == 3) {
            $this->_view->usernames = $userobj->getBoSalesNames();
            $this->_view->usertype = "Sales Team";
        } else if ($this->_request->getParam('usertype') == 7) {
            $this->_view->usernames = $userobj->getBoCustormercareNames();
            $this->_view->usertype = "Customer Care Team";
        } else if ($this->_request->getParam('usertype') == 9) {
            $this->_view->usernames = $userobj->getBoFacturationNames();
            $this->_view->usertype = "Facturation Team";
        }

        $this->_view->render("user_userinfopopup");

    }

    /* Listing all contributors */
    public function contributorsoldAction()
    {
        $contributorobj = new Ep_User_Contributor();
        $this->_view->contributorsList = $contributorobj->ListContributorsinfo();

        $this->_view->render("contributors");
    }

    /* Listing all contributors */
    public function clientsoldAction()
    {
        $clientobj = new Ep_User_Client();
        //echo '<pre>'; print_r($clientobj->ListClientsinfo()); exit;
        $this->_view->clientsList = $clientobj->ListClientsinfo();

        $this->_view->render("clients");
    }

    /* Upload Profile Photo*/
    public function uploadprofilepicAction()
    {

        error_reporting(E_ERROR | E_PARSE);
        $path = pathinfo($_FILES['uploadpic']['name']);
        $uploadpicname = $_FILES['uploadpic']['name'];
        $ext = "jpg";//$path['extension'];//$this->findexts($uploadpicname);

        $contrib_identifier = $_REQUEST['userid'];
        $app_path = "/home/sites/site5/web/FO/";
        if ($_REQUEST['type'] == 'profile')
            $profiledir = 'profiles/bo/' . $contrib_identifier . '/';
        else
            $profiledir = 'profiles/bo/' . $contrib_identifier . '/clogo/';
        $profiledir = 'profiles/bo/' . $contrib_identifier . '/';
        $uploadpicdir = $app_path . $profiledir;
        if (!is_dir($uploadpicdir))
            mkdir($uploadpicdir, TRUE);
        chmod($uploadpicdir, 0777);
        $contrib_picture = $uploadpicdir . $contrib_identifier . "." . $ext;
        $contrib_picture_home = $uploadpicdir . $contrib_identifier . "_h." . $ext;
        $contrib_picture_profile = $uploadpicdir . $contrib_identifier . "_p." . $ext;
        $contrib_picture_offer = $uploadpicdir . $contrib_identifier . "_ao." . $ext;
        $contrib_picture_crop = $uploadpicdir . $contrib_identifier . "_crop." . $ext;
        list($width, $height) = getimagesize($_FILES['uploadpic']['tmp_name']);
        if ($width >= 90 && $height >= 90) {
            if (move_uploaded_file($_FILES['uploadpic']['tmp_name'], $contrib_picture)) {
                chmod($contrib_picture, 0777);
                /*Image for cropping**/
                $newimage_crop = new Ep_User_Image();
                $newimage_crop->load($contrib_picture);
                list($width, $height) = getimagesize($contrib_picture);
                if ($width > 400)
                    $newimage_crop->resizeToWidth(400);
                elseif ($height > 600)
                    $newimage_crop->resizeToHeight(600);
                else
                    $newimage_crop->resize($width, $height);
                $newimage_crop->save($contrib_picture_crop);
                chmod($contrib_picture_crop, 0777);
                $array = array("status" => "success", "identifier" => $contrib_identifier, "path" => $profiledir, "ext" => $ext, "pictype" => $_REQUEST['type']);
                echo json_encode($array);
                //echo "success";
            } else {
                $array = array("status" => "error");
                echo json_encode($array);
            }
        } else {
            $array = array("status" => "smallfile");
            echo json_encode($array);
        }
    }

    /* Cropping Profile images**/
    public function cropprofilepicAction()
    {
        if ($this->_request->isPost()) {
            $image_params = $this->_request->getParams();
            $function = $image_params['function'];
            $new_x = $image_params['x'];
            $new_y = $image_params['y'];
            $post_width = $image_params['w'];
            $post_height = $image_params['h'];

            $contrib_identifier = $image_params['userId'];
            $ext = "jpg";

            $app_path = "/home/sites/site5/web/FO/";
            if ($image_params['type'] == 'profile' || $image_params['pictype'] == 'profile')
                $profiledir = 'profiles/bo/' . $contrib_identifier . '/';
            else
                $profiledir = 'profiles/bo/' . $contrib_identifier . '/clogo/';
            $uploadpicdir = $app_path . $profiledir;
            $contrib_picture_home = $uploadpicdir . $contrib_identifier . "_h." . $ext;
            $contrib_picture_profile = $uploadpicdir . $contrib_identifier . "_p." . $ext;
            $contrib_picture_offer = $uploadpicdir . $contrib_identifier . "_ao." . $ext;
            $contrib_picture = $uploadpicdir . $contrib_identifier . "." . $ext;
            $contrib_picture_crop = $uploadpicdir . $contrib_identifier . "_crop." . $ext;
            $contrib_picture_logo = $uploadpicdir . "/logo.jpg";
            if ($function == "saveimage") {
                /* Contrib home image with 60x60**/
                $newimage_h = new Ep_User_Image();
                $newimage_h->load($contrib_picture_crop);
                $newimage_h->cropImage($new_x, $new_y, 60, 60, $post_width, $post_height);
                $newimage_h->save($contrib_picture_home);
                // chmod($contrib_picture_home,777);
                unset($newimage_h);
                /* Contrib Profile image with 90x90**/
                $newimage_p = new Ep_User_Image();
                $newimage_p->load($contrib_picture_crop);
                $newimage_p->cropImage($new_x, $new_y, 90, 90, $post_width, $post_height);
                $newimage_p->save($contrib_picture_profile);
                //chmod($contrib_picture_profile,777);
                unset($newimage_p);
                /* Contrib Profile image with 90x90**/

                $newimage_l = new Ep_User_Image();
                $newimage_l->load($contrib_picture_crop);
                $newimage_l->cropImage($new_x, $new_y, 90, 90, $post_width, $post_height);
                $newimage_l->save($contrib_picture_logo);
                //chmod($contrib_picture_profile,777);
                unset($newimage_l);
                /* Contrib Profile image with width 90**/
                $newimage_p = new Ep_User_Image();
                $newimage_p->load($contrib_picture_crop);
                list($width, $height) = getimagesize($contrib_picture_crop);
                $ao_image_height = (($height / $width) * 90);
                $newimage_p->cropImage($new_x, $new_y, 90, $ao_image_height, $post_width, $post_height);
                $newimage_p->save($contrib_picture_offer);
                //chmod($contrib_picture_offer,777);
                unset($newimage_p);
            } elseif ($function == "original") {
                /* Contrib home image with 60x60**/
                $newimage_h = new Ep_User_Image();
                $newimage_h->load($contrib_picture);
                $newimage_h->resize(60, 60);
                $newimage_h->save($contrib_picture_home);
                //chmod($contrib_picture_home,0777);
                unset($newimage_h);
                /*Contrib Profile image with 90x90**/
                $newimage_p = new Ep_User_Image();
                $newimage_p->load($contrib_picture);
                $newimage_p->resize(90, 90);
                $newimage_p->save($contrib_picture_profile);
                // chmod($contrib_picture_profile,0777);
                unset($newimage_h);
                $newimage_p = new Ep_User_Image();
                $newimage_p->load($contrib_picture);
                list($width, $height) = getimagesize($contrib_picture);
                $ao_image_height = (($height / $width) * 90);
                $newimage_p->resize(90, $ao_image_height);
                $newimage_p->save($contrib_picture_offer);
                // chmod($contrib_picture_offer,0777);
                unset($newimage_p);
            }
            /* Unlink the Original file**/
            if (file_exists($contrib_picture) && !is_dir($contrib_picture))
                unlink($contrib_picture);
            $array = array("identifier" => $contrib_identifier, "path" => $profiledir, "ext" => $ext, "pictype" => $image_params['pictype']);
            echo json_encode($array);
        }
    }

    public function userHistoryAction()
    {
        //echo "manage";  exit;
        $user_obj = new Ep_User_User();
        $user_details = $user_obj->getUserdetails($_GET['user']);
        $eduarray = array("1" => "Bac +1", "2" => "Bac +2", "3" => "Bac +3", "4" => "Bac +4", "5" => "Bac +5", "6" => "Bac+5 et plus");
        $favourite_category = "";
        if ($user_details[0]['favourite_category'] != NULL) {
            $fav_cat = explode(",", $user_details[0]['favourite_category']);

            if (count($fav_cat) > 0) {
                for ($l = 0; $l < count($fav_cat); $l++) {
                    $favourite_category .= utf8_encode($this->category_array[$fav_cat[$l]]);

                    if ($l != count($fav_cat) - 1)
                        $favourite_category .= ",";
                }
            }
        }
        $this->_view->user_detail = $user_details;
        $this->_view->favourite_category = $favourite_category;
        $this->_view->language = $this->language_array[$user_details[0]['language']];
        $this->_view->profession = $this->profession_array[$user_details[0]['profession']];
        $eduarray = array("1" => "Bac +1", "2" => "Bac +2", "3" => "Bac +3", "4" => "Bac +4", "5" => "Bac +5", "6" => "Bac+5 et plus");
        $this->_view->education = $eduarray[$user_details[0]['education']];
        //Participation details
        $parti_details = $user_obj->getContribPartinfo($_GET['user']);
        $this->_view->parti_detail = $parti_details;
        $this->_view->render("user_userhistory");
    }

    public function uploadclientgloballogoAction()
    {
        $realfilename = $_FILES['uploadfile']['name'];
        $ext = substr(strrev($realfilename), 0, strpos($realfilename, '.'));

        $client_id = $_REQUEST['clientid'];
        $profiledir = '/home/sites/site5/web/FO/profiles/clients/logos/' . $client_id . '/';
        $pic_path = '/profiles/clients/logos/' . $client_id . '/';
        $uploaddir = '/home/sites/site5/web/FO/profiles/clients/logos/' . $client_id . '/';
        $newfilename = $client_id . "." . $ext;
        $clntid = $this->_view->clientidentifier;

        if (!is_dir($uploaddir)) {
            mkdir($uploaddir, 0777);
            chmod($uploaddir, 0777);
        }

        $file = $uploaddir . $client_id . ".png";
        $file_global1 = $uploaddir . $client_id . "_global.png";
        $file_global2 = $uploaddir . $client_id . "_global1.png";
        list($width, $height) = getimagesize($_FILES['uploadfile']['tmp_name']);

        if ($width >= 90 || $height >= 90) {
            if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
                //73
                $newimage_crop = new EP_User_Image();
                $newimage_crop->load($file);
                list($width, $height) = getimagesize($file);
                if ($width > $height)
                    $newimage_crop->resizeToWidth(73);
                elseif ($height > $width)
                    $newimage_crop->resizeToHeight(73);
                else
                    $newimage_crop->resize(73, 73);

                $newimage_crop->save($file_global1);
                chmod($file_global1, 0777);

                //90
                $newimage_crop1 = new EP_User_Image();
                $newimage_crop1->load($file);
                list($width, $height) = getimagesize($file);
                if ($width > $height)
                    $newimage_crop1->resizeToWidth(90);
                elseif ($height > $width)
                    $newimage_crop1->resizeToHeight(90);
                else
                    $newimage_crop1->resize(90, 90);

                $newimage_crop1->save($file_global2);
                chmod($file_global2, 0777);


                $array = array("status" => "success", "identifier" => $client_id, "path" => $pic_path, "ext" => "png");
                echo json_encode($array);
            } else {
                $array = array("status" => "error");
                echo json_encode($array);
            }
        } else {
            $array = array("status" => "smallfile");
            echo json_encode($array);
        }
    }

    /**function to get the category name**/
    public function getCategoryName($category_value)
    {
        $category_name = '';
        $categories = explode(",", $category_value);
        $categories_array = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
        $cnt = 0;
        foreach ($categories as $category) {
            if ($cnt == 4)
                break;
            $category_name .= $categories_array[$category] . ", ";
            $cnt++;
        }
        $category_name = substr($category_name, 0, -2);
        return $category_name;
    }

    ///////to display the search page in stats contributor/////////
    public function searchstatscontributorsAction()
    {
        //echo $prevurl = getenv("HTTP_REFERER"); echo $_GET['searchsubmit']; echo $this->_request->getParam('searchsubmit');
        $userdetails = new Ep_User_UserPlus();
        $user_obj = new Ep_User_User();
        $statscontrib_params = $this->_request->getParams();
        $this->_view->start_date = $this->_request->getParam('start_date');
        $this->_view->end_date = $this->_request->getParam('end_date');
        $this->_view->aolist = $this->_request->getParam('aoList');
        $this->_view->arttitle = $this->_request->getParam('arttitle');
        $this->_view->contrib = $this->_request->getParam('contrib');
        $this->_view->type = $this->_request->getParam('type');
        $this->_view->status = $this->_request->getParam('status');
        $this->_view->blacklist = $this->_request->getParam('blacklist');
        $this->_view->nationalism = $this->_request->getParam('nationalism');
        $this->_view->age = $this->_request->getParam('age');
        $this->_view->category = $_REQUEST['category'];
        $this->_view->language = $this->_request->getParam('language');
        $this->_view->minage = $this->_request->getParam('minage');
        $this->_view->maxage = $this->_request->getParam('maxage');
        $this->render('stats_searchstatscontributors');
    }

    ///////////////saving search forms/////////////////
    public function savesearchAction()
    {
        $params = $this->_request->getParams();
        $user_obj = new Ep_User_User();
        $userplus_obj = new Ep_User_UserPlus();
        if (isset($params['compare']) && $params['compare'] == "yes") {
            $contribIds = $params['contribids'];
            $where = " AND u.identifier IN (" . $contribIds . ")";
            // $this->_view->compareContribs = $user_obj->getSearchedContributorsList($where);
            $contribIds = $params['contribids'];
            $contribIds = explode(",", $contribIds);
            for ($i = 0; $i < count($contribIds); $i++) {
                $compareContribs[] = $userplus_obj->getCompareContributors($contribIds[$i]);
            }

            //print_r($compareContribs);
            //$maxs = array_keys($compareContribs, max($compareContribs[0]['3']['no_paritcipations']));

            $category = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
            $nationality = $this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
            $language = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
            $this->_view->compareContribs = $compareContribs;
            $this->_view->category = $category;
            $this->_view->country = $nationality;
            $this->_view->language = $language;
            $this->render('stats_comparecontribs');
            exit;

        }
        if (isset($params['emailcontribs'])) {
            /* $contribids = $params['contribcheck'];
             for($i=0; $i<count($contribids); $i++)
             {
                 //$userdetails[] = $user_obj->getAllUsersDetails($contribIds[$i]);
                 $contribIds.= "&usercontacts[]=".$contribids[$i];
             }

             $this->_redirect("https://ep-test.edit-place.com/mails/newsletter?submenuId=ML4-SL10&selectgroup=contributor".$contribIds);*/
            $contribids = $params['userchecks'];


            $this->_redirect("http://admin-test.edit-place.com/mails/newsletter?submenuId=ML4-SL10&selectgroup=contributor&" . $contribids);
        } else {
            $prevurl = getenv("HTTP_REFERER");
            $savesearch_obj = new Ep_User_SaveSearch();
            $savesearch_obj->user_id = $this->adminLogin->userId;
            $savesearch_obj->search_name = $params['searchname'];
            $savesearch_obj->url = $prevurl;
            $savesearch_obj->insert();
            $this->_redirect($prevurl);
        }
    }

    ///////////////edit search forms/////////////////
    public function editsearchAction()
    {
        $params = $this->_request->getParams();
        $prevurl = getenv("HTTP_REFERER");
        $savesearch_obj = new Ep_User_SaveSearch();
        $savesearch_obj->user_id = $this->adminLogin->userId;
        $data = array("search_name" => $params['editsearchname']);////////updating
        $query = "id= '" . $params['searchId'] . "'";   //exit;
        $savesearch_obj->updateSaveSearch($data, $query);
        //$this->_redirect($prevurl);
        $this->_redirect("/user/search-contributors?submenuId=ML10-SL6");
    }

    ///////////////delete search ////////////////
    public function deletesearchAction()
    {
        $params = $this->_request->getParams();
        $prevurl = getenv("HTTP_REFERER");
        $savesearch_obj = new Ep_User_SaveSearch();
        $savesearch_obj->user_id = $this->adminLogin->userId;
        $data = array("active" => "no");////////updating
        $query = "id= '" . $params['searchId'] . "'";
        $savesearch_obj->updateSaveSearch($data, $query);
        $this->_redirect($prevurl);
    }

    ///////////////active or inactive the users////////////////
    public function changeuserstatusAction()
    {
        $params = $this->_request->getParams();
        $user_obj = new Ep_User_User();
        $data = array("status" => $params['status'], "changestatus_by" => $this->adminLogin->userId, "changestatus_at" => date("Y-m-d H:i:s"));
        $query = "identifier= '" . $params['user_id'] . "'";
        $user_obj->updateUser($data, $query);

    }

    public function usersListAction()
    {
        $userdetails = new Ep_User_UserPlus();
        $user_obj = new Ep_User_User();
        $usergrp_obj = new Ep_User_UserGroupAccess();
        $groups = $usergrp_obj->getAllUserGroupNames();


        /* * download XLS file***/
        $contrib_params = $this->_request->getParams();
        if (isset($contrib_params['download']) && $contrib_params['download'] != '') {
            //echo $_SERVER['QUERY_STRING'];exit;
            $condition['searchsubmit'] = $contrib_params['searchsubmit'];
            $condition['start_date'] = $contrib_params['start_date'];
            $condition['end_date'] = $contrib_params['end_date'];
            $condition['act_start_date'] = $contrib_params['activity_start_date'];
            $condition['act_end_date'] = $contrib_params['activity_end_date'];
            $condition['aolist'] = $contrib_params['aoList'];
            $condition['arttitle'] = $contrib_params['arttitle'];
            $condition['contrib'] = $contrib_params['contrib'];
            $condition['type'] = $contrib_params['type'];
            $condition['type2'] = $contrib_params['type2'];
            $condition['status'] = $contrib_params['status'];
            $condition['blacklist'] = $contrib_params['blacklist'];
            $condition['nationalism'] = $contrib_params['nationalism'];
            $condition['category'] = $contrib_params['categ'];
            $condition['language'] = $contrib_params['language'];
            $condition['language2'] = $contrib_params['language2'];
            $condition['aotitle'] = $contrib_params['aotitle'];
            $condition['minage'] = $contrib_params['minage'];
            $condition['maxage'] = $contrib_params['maxage'];
            $condition['minartsvalid'] = $contrib_params['min_arts_validated'];
            $condition['maxartsvalid'] = $contrib_params['max_arts_validated'];
            $condition['mintotalparts'] = $contrib_params['min_total_parts'];
            $condition['maxtotalparts'] = $contrib_params['max_total_parts'];
            $condition['minartssent'] = $contrib_params['min_arts_sent'];
            $condition['maxartssent'] = $contrib_params['max_arts_sent'];
            $condition['minpartsrefused'] = $contrib_params['min_parts_refused'];
            $condition['maxpartsrefused'] = $contrib_params['max_parts_refused'];
            $condition['minartsrefused'] = $contrib_params['min_arts_refused'];
            $condition['maxartsrefused'] = $contrib_params['max_arts_refused'];
            $condition['noofdisapproved'] = $contrib_params['noof_disapproved'];

            /* *** edited on 11.12.2015 *** */
            // adding condtion to download proper xls files//
            if ($contrib_params['counttype'] == 'total_contribs')
                $condition['total_contribs'] = 'yes';
            if ($contrib_params['counttype'] == 'never_participated')
                $condition['never_participated'] = 'yes';
            if ($contrib_params['counttype'] == 'never_sent')
                $condition['never_sent'] = 'yes';
            if ($contrib_params['counttype'] == 'never_validated')
                $condition['never_validated'] = 'yes';
            if ($contrib_params['counttype'] == 'once_validated')
                $condition['once_validated'] = 'yes';
            if ($contrib_params['counttype'] == 'once_published')
                $condition['once_published'] ='yes';


            /////total count
            $sLimit1 = "";
            $contributors = $user_obj->loadContributor($sWhere, $sOrder, $sLimit1, $condition);
            //echo count($contributors );exit;
            //echo "<pre>";print_r($contributors);exit;
            $category = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
            $nationality = $this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
            $language = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
            $profession = $this->_arrayDb->loadArrayv2("CONTRIB_PROFESSION", $this->_lang);
            //$contributors= $user_obj->getSearchedContributorsList($where);

            //print_r($contributors); exit;
            $file = 'excelFile-' . date("Y-M-D") . "-" . time() . '.xls';
            ob_start();
            $content = '<table border="1"> ';
            $content .= '<tr><th>Contributor Name</th><th>Email</th><th>Initial</th><th>Status</th>';
            $content .= '<th>Date of Join</th><th>Profile Type</th><th>Black Status</th><th>City</th><th>State</th>';
            $content .= '<th>Country</th><th>Pin code</th>';
            $content .= '<th>Phone Number</th><th>DoB</th><th>University</th><th>Profession</th><th>Language</th><th>More Language</th><th>Category</th>';
            $content .= '<th>Description</th><th>Contributor Test</th><th>Contributor test Comments</th><th>Contributor test Marks</th>';
            $content .= '</tr>';
            for ($i = 0; $i < count($contributors); $i++) {
                if ($contributors[$i]["language_more"] != '') {
                    $lang_list = array();
                    if ($contributors[$i]["language_more"] != '') {
                        $laninfo = unserialize($contributors[$i]['language_more']);
                        if ($laninfo != '') {
                            foreach ($laninfo as $key1 => $value1) {
                                $lang_list[] = $this->language_array[$key1] . (($value1 > 0) ? ('(' . $value1 . '%)') : '');
                            }
                        }
                        $row = implode(',', $lang_list);
                    } else
                        $row = "-";
                }
                if ($contributors[$i]["category_more"] != '') {
                    $cat_list = array();
                    if ($contributors[$i]['category_more'] != '') {
                        $catinfo = unserialize($contributors[$i]['category_more']);
                        if ($catinfo != '') {
                            foreach ($catinfo as $key2 => $value2) {
                                $cat_list[] = $this->category_array[$key2] . (($value2 > 0) ? ('(' . $value2 . '%)') : '');
                            }
                        }
                        $crow = implode(',', $cat_list);
                    } else
                        $crow = "-";


                }
                $contributors[$i]["category_more"] = $crow;
                $contributors[$i]["language_more"] = $row;
                //if($contributors[$i]["first_name"] == '') { $name =  $contributors[$i]["email"]; } else { $name =  $contributors[$i]["first_name"]; }
                $content .= '<tr><td>' . $contributors[$i]["full_name"] . '</td><td>' . $contributors[$i]["email"] . '</td><td>' . $contributors[$i]["initial"] . '</td>';
                $content .= '<td>' . $contributors[$i]["status"] . '</td><td>' . date("d-m-Y", strtotime($contributors[$i]["created_at"])) . '</td>';
                $content .= '<td>' . $contributors[$i]["profile_type"] . '</td><td>' . $contributors[$i]["blackstatus"] . '</td>';
                $content .= '<td>' . $contributors[$i]["city"] . '</td><td>' . $contributors[$i]["state"] . '</td><td>' . $nationality[$contributors[$i]["country"]] . '</td><td>' . $contributors[$i]["zipcode"] . '</td>';
                $content .= '<td>' . $contributors[$i]["phone_number"] . '</td><td>' . $contributors[$i]["dob"] . '</td><td>' . $contributors[$i]["university"] . '</td>';
                $content .= '<td>' . $profession[$contributors[$i]["profession"]] . '</td><td>' . $language[$contributors[$i]["language"]] . '</td><td>' . $contributors[$i]["language_more"] . '</td><td>' . $contributors[$i]["category_more"] . '</td>';

                $breaks = array("<br />", "<br>", "<br/>");
                $contributors[$i]["self_details"] = str_ireplace($breaks, "\r\n", $contributors[$i]["self_details"]);
                $content .= '<td>' . $contributors[$i]["self_details"] . '</td><td>' . $contributors[$i]["contributortest"] . '</td><td>' . $contributors[$i]["contributortestcomment"] . '</td><td>' . $contributors[$i]["contributortestmarks"] . '</td></tr>';
            }
            $content .= '</table>';
            // echo $content; exit;
            header("Content-Disposition: attachment; filename=\"$filetable\"");

            ob_end_clean();
            header("Expires: 0");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header("Content-type: application/vnd.ms-excel;charset:UTF-8");
            header('Content-length: ' . strlen($content));
            header('Content-disposition: attachment; filename=' . basename($file));
            echo $content;
            exit;


        }

        $this->_view->contribCount = $userdetails->getStatsContributorsCount();
        foreach ($groups as $key => $value) {
            $usergroups[$value['groupName']] = strtoupper($value['groupName']);
        }
        $this->_view->usergroups = $usergroups;
        $userId = $this->_request->getParam('userId');
        $user_obj = new Ep_User_User();

        $details = $userdetails->getUsersDetails();
        $var = 0;
        foreach ($details as $details1) {
            $details[$var]['country'] = $this->country_array[$details1['country']];
            $var++;
        }
        $this->_view->bouserdetails = $details;

        $savesearch_obj = new Ep_User_SaveSearch();
        if ($this->adminLogin->userId != '')
            $this->_view->savedSearchesUrls = $savesearch_obj->getSearchedUrls($this->adminLogin->userId);
        $quiz_obj = new Ep_Quizz_quizz();
        $quizlist = $quiz_obj->ListQuizz();
        foreach ($quizlist as $key => $value) {
            $quiz_list[$value['id']] = $value['title'];
        }
        $this->_view->quizlist = $quiz_list;

        //////////////permissions data to dispay permissions grid ////////////////////
        $p = new Ep_Controller_Page($this->pageFile);
        ////getting groups///////
        $group_obj = new Ep_User_UserGroupAccess();
        $groups = $group_obj->getAllGroups();
        $this->_view->groupList = $groups;
        ////getting users////////
        $user_obj = new Ep_User_User();
        $users = $user_obj->getUsers();
        $this->_view->userList = $users;
        /////////////////////////////////////
        //if($this->_request-> isPost())
        if ($this->_request->getParam('tab') == 'permissionstab') {
            $sel_group = $this->_request->getParam('sel_group');
            $sel_user = $this->_request->getParam('sel_user');
            $this->_view->GpSel = $sel_group;
            $this->_view->UsrSel = $sel_user;
            $total = $this->_request->getParam('hid_totalrows');///no of pages///

            if ($this->_request->getParam('assign') == 'assign') {
                for ($i = 1; $i <= $total; $i++) {
                    //chk_.$i = $this->_request->getParam('chk_',$i);
                    if ($this->_request->getParam('chk_' . $i) == 'chk_' . $i) {
                        $chkPages[] = $this->_request->getParam('chk_', $i);
                        $chkPagesIds[] = $i;
                    }
                }
                $chkedpageIds = implode("|", $chkPagesIds);
                if ($sel_user != '0') {
                    $data = array("pageId" => $chkedpageIds);////////updating
                    $query = "identifier= '" . $sel_user . "'";
                    $user_obj->updateUser($data, $query);
                } else if ($sel_group != '0') {
                    $data = array("pageId" => $chkedpageIds);
                    $query = "id= '" . $sel_group . "'";
                    $group_obj->updateGroup($data, $query);
                }
                $this->_helper->FlashMessenger('Permissions updated Successfully.');
            }
            //////for groups
            if ($sel_group != '') {
                $grouppage = $group_obj->getGroup($sel_group);
                $this->_view->pageGpSel = explode("|", $grouppage[0]->pageId);
            }
            /////for users
            if ($sel_user != '') {
                $userpage = $user_obj->getUserPages($sel_user);
                $this->_view->pageUsrSel = explode("|", $userpage[0]->pageId);
            }

            //  $this->_redirect("/user/permissions?submenuId=ML10-SL5");
        }
        $pageList = $p->selectAllPagesBySegment(1);
        $this->_view->pageList = $pageList;

        $this->render('user_userslist');
    }

    public function searchContributorsAction()
    {
        $userdetails = new Ep_User_UserPlus();
        $user_obj = new Ep_User_User();
        $usergrp_obj = new Ep_User_UserGroupAccess();
        /* * download XLS file***/
        $contrib_params = $this->_request->getParams();
        if (isset($contrib_params['download']) && $contrib_params['download'] != '') {
            foreach ($contrib_params['categ'] as $key => $value) {
                $categ[] = $key . "=" . $value;
            }
            foreach ($contrib_params['lange'] as $key => $value) {
                $lange[] = $key . "=" . $value;
            }

            $condition['searchsubmit'] = $contrib_params['searchsubmit'];
            $condition['start_date'] = $contrib_params['start_date'];
            $condition['end_date'] = $contrib_params['end_date'];
            $condition['act_start_date'] = $contrib_params['activity_start_date'];
            $condition['act_end_date'] = $contrib_params['activity_end_date'];
            $condition['aolist'] = $contrib_params['aoList'];
            $condition['arttitle'] = $contrib_params['arttitle'];
            $condition['contrib'] = $contrib_params['contrib'];
            $condition['type'] = $contrib_params['type'];
            $condition['type2'] = $contrib_params['type2'];
            $condition['status'] = $contrib_params['status'];
            $condition['blacklist'] = $contrib_params['blacklist'];
            $condition['nationalism'] = $contrib_params['nationalism'];
            $condition['category'] = $contrib_params['category'];
            $condition['categ'] = $categ;
            $condition['language'] = $contrib_params['language'];
            $condition['lange'] = $lange;
            $condition['aotitle'] = $contrib_params['aotitle'];
            $condition['minage'] = $contrib_params['minage'];
            $condition['maxage'] = $contrib_params['maxage'];
            $condition['minartsvalid'] = $contrib_params['min_arts_validated'];
            $condition['maxartsvalid'] = $contrib_params['max_arts_validated'];
            $condition['mintotalparts'] = $contrib_params['min_total_parts'];
            $condition['maxtotalparts'] = $contrib_params['max_total_parts'];
            $condition['minartssent'] = $contrib_params['min_arts_sent'];
            $condition['maxartssent'] = $contrib_params['max_arts_sent'];
            $condition['minpartsrefused'] = $contrib_params['min_parts_refused'];
            $condition['maxpartsrefused'] = $contrib_params['max_parts_refused'];
            $condition['minartsrefused'] = $contrib_params['min_arts_refused'];
            $condition['maxartsrefused'] = $contrib_params['max_arts_refused'];
            $condition['noofdisapproved'] = $contrib_params['noof_disapproved'];
            $condition['selfdetails'] = trim(urldecode($contrib_params['contrib_self_details']));
            $condition['writeravgmarks'] = $contrib_params['writer_avg_marks'];
            $condition['correctoravgmarks'] = $contrib_params['corrector_avg_marks'];
            $condition['contributortest'] = $contrib_params['contributortest'];

            if ($contrib_params['total_contribs'] == 'yes')
                $condition['total_contribs'] = $contrib_params['total_contribs'];
            if ($contrib_params['never_participated'] == 'yes')
                $condition['never_participated'] = $contrib_params['never_participated'];
            if ($contrib_params['never_sent'] == 'yes')
                $condition['never_sent'] = $contrib_params['never_sent'];
            if ($contrib_params['never_validated'] == 'yes')
                $condition['never_validated'] = $contrib_params['never_validated'];
            if ($contrib_params['once_validated'] == 'yes')
                $condition['once_validated'] = $contrib_params['once_validated'];
            if ($contrib_params['once_published'] == 'yes')
                $condition['once_published'] = $contrib_params['once_published'];
            /* *** added on 15.12.2015 *** */
            $condition['hanging'] = $contrib_params['hanging'];

            $sLimit1 = "";
            $contributors = $user_obj->loadContributor($sWhere = null, $sOrder = null, $sLimit1, $condition);

            $category = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
            $nationality = $this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
            $language = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
            $profession = $this->_arrayDb->loadArrayv2("CONTRIB_PROFESSION", $this->_lang);
            if ($nationality)
                array_unshift($nationality, "ALL");

            $file = 'excelFile-' . date("Y-M-D") . "-" . time() . '.xls';
            ob_start();
            $content = '<table border="1"> ';
            $content .= '<tr><th>Contributor Name</th><th>Email</th><th>Initial</th><th>Status</th>';
            $content .= '<th>Date of Join</th><th>Profile Type</th><th>Black Status</th><th>City</th><th>State</th>';
            $content .= '<th>Country</th><th>Pin code</th>';
            $content .= '<th>Phone Number</th><th>DoB</th><th>University</th><th>Profession</th><th>Language</th><th>More Language</th><th>Category</th>';
            $content .= '<th>Description</th><th>Contributor Test</th><th>Contributor test Comments</th><th>Contributor test Marks</th>';
            $content .= '</tr>';
            for ($i = 0; $i < count($contributors); $i++) {
                if ($contributors[$i]["language_more"] != '') {
                    $lang_list = array();
                    if ($contributors[$i]["language_more"] != '') {
                        $laninfo = unserialize($contributors[$i]['language_more']);
                        if ($laninfo != '') {
                            foreach ($laninfo as $key1 => $value1) {
                                $lang_list[] = $this->language_array[$key1] . (($value1 > 0) ? ('(' . $value1 . '%)') : '');
                            }
                        }
                        $row = implode(',', $lang_list);
                    } else
                        $row = "-";
                }
                if ($contributors[$i]["category_more"] != '') {
                    $cat_list = array();
                    if ($contributors[$i]['category_more'] != '') {
                        $catinfo = unserialize($contributors[$i]['category_more']);
                        if ($catinfo != '') {
                            foreach ($catinfo as $key2 => $value2) {
                                $cat_list[] = $this->category_array[$key2] . (($value2 > 0) ? ('(' . $value2 . '%)') : '');
                            }
                        }
                        $crow = implode(',', $cat_list);
                    } else
                        $crow = "-";


                }
                $contributors[$i]["category_more"] = $crow;
                $contributors[$i]["language_more"] = $row;
                //if($contributors[$i]["first_name"] == '') { $name =  $contributors[$i]["email"]; } else { $name =  $contributors[$i]["first_name"]; }
                $content .= '<tr><td>' . $contributors[$i]["full_name"] . '</td><td>' . $contributors[$i]["email"] . '</td><td>' . $contributors[$i]["initial"] . '</td>';
                $content .= '<td>' . $contributors[$i]["status"] . '</td><td>' . date("d-m-Y", strtotime($contributors[$i]["created_at"])) . '</td>';
                $content .= '<td>' . $contributors[$i]["profile_type"] . '</td><td>' . $contributors[$i]["blackstatus"] . '</td>';
                $content .= '<td>' . $contributors[$i]["city"] . '</td><td>' . $contributors[$i]["state"] . '</td><td>' . $nationality[$contributors[$i]["country"]] . '</td><td>' . $contributors[$i]["zipcode"] . '</td>';
                $content .= '<td>' . $contributors[$i]["phone_number"] . '</td><td>' . $contributors[$i]["dob"] . '</td><td>' . $contributors[$i]["university"] . '</td>';
                $content .= '<td>' . $profession[$contributors[$i]["profession"]] . '</td><td>' . $language[$contributors[$i]["language"]] . '</td><td>' . $contributors[$i]["language_more"] . '</td><td>' . $contributors[$i]["category_more"] . '</td>';

                $breaks = array("<br />", "<br>", "<br/>");
                $contributors[$i]["self_details"] = str_ireplace($breaks, "\r\n", $contributors[$i]["self_details"]);
                $content .= '<td>' . $contributors[$i]["self_details"] . '</td><td>' . $contributors[$i]["contributortest"] . '</td><td>' . $contributors[$i]["contributortestcomment"] . '</td><td>' . $contributors[$i]["contributortestmarks"] . '</td></tr>';
            }
            $content .= '</table>';

            header("Content-Disposition: attachment; filename=\"$filetable\"");
            header("Content-Type: application/vnd.ms-excel");

            //$content = ob_get_contents();
            //$_SESSION['content']=$content;
            //header("Location:/BO/download_users_xls.php?content=".$content);
            //$this->_redirect("/BO/download_users_xls.php?content=".$content);

            ob_end_clean();
            header("Expires: 0");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header("Content-type: application/vnd.ms-excel;charset:UTF-8");
            header('Content-length: ' . strlen($content));
            header('Content-disposition: attachment; filename=' . basename($file));
            echo $content;
            exit;
        }
        $savesearch_obj = new Ep_User_SaveSearch();
        if ($this->adminLogin->userId != '')
            $this->_view->savedSearchesUrls = $savesearch_obj->getSearchedUrls($this->adminLogin->userId);
        $quiz_obj = new Ep_Quizz_quizz();
        $quizlist = $quiz_obj->ListQuizz();
        foreach ($quizlist as $key => $value) {
            $quiz_list[$value['id']] = $value['title'];
        }
        $this->_view->quizlist = $quiz_list;
        $this->_view->contribCount = $userdetails->getStatsContributorsCount();
        $this->render('user_searchcontributors');
    }

    public function boUsersAction()
    {
        $userdetails = new Ep_User_UserPlus();
        $user_obj = new Ep_User_User();
        $usergrp_obj = new Ep_User_UserGroupAccess();
        $groups = $usergrp_obj->getAllUserGroupNames();
        $details = $userdetails->getUsersDetails();
        $var = 0;
        foreach ($details as $details1) {
            $details[$var]['country'] = $this->country_array[$details1['country']];
            $details[$var]['picture'] = "http://ep-test.edit-place.com/FO/profiles/bo/" . $details1['identifier'] . "/" . $details1['identifier'] . "_p.jpg";
            $var++;
        }
        $this->_view->bouserdetails = $details;

        $this->render('user_bousers');
    }

    public function contributorsAction()
    {
        $userdetails = new Ep_User_UserPlus();
        $user_obj = new Ep_User_User();
        $usergrp_obj = new Ep_User_UserGroupAccess();
        $groups = $usergrp_obj->getAllUserGroupNames();
        /* * download XLS file***/
        $contrib_params = $this->_request->getParams();
        if (isset($contrib_params['download']) && $contrib_params['download'] != '') {
            $condition['searchsubmit'] = $contrib_params['searchsubmit'];
            $condition['start_date'] = $contrib_params['start_date'];
            $condition['end_date'] = $contrib_params['end_date'];
            $condition['act_start_date'] = $contrib_params['activity_start_date'];
            $condition['act_end_date'] = $contrib_params['activity_end_date'];
            $condition['aolist'] = $contrib_params['aoList'];
            $condition['arttitle'] = $contrib_params['arttitle'];
            $condition['contrib'] = $contrib_params['contrib'];
            $condition['type'] = $contrib_params['type'];
            $condition['type2'] = $contrib_params['type2'];
            $condition['status'] = $contrib_params['status'];
            $condition['blacklist'] = $contrib_params['blacklist'];
            $condition['nationalism'] = $contrib_params['nationalism'];
            $condition['category'] = $contrib_params['categ'];
            $condition['language'] = $contrib_params['language'];
            $condition['language2'] = $contrib_params['language2'];
            $condition['aotitle'] = $contrib_params['aotitle'];
            $condition['minage'] = $contrib_params['minage'];
            $condition['maxage'] = $contrib_params['maxage'];
            $condition['minartsvalid'] = $contrib_params['min_arts_validated'];
            $condition['maxartsvalid'] = $contrib_params['max_arts_validated'];
            $condition['mintotalparts'] = $contrib_params['min_total_parts'];
            $condition['maxtotalparts'] = $contrib_params['max_total_parts'];
            $condition['minartssent'] = $contrib_params['min_arts_sent'];
            $condition['maxartssent'] = $contrib_params['max_arts_sent'];
            $condition['minpartsrefused'] = $contrib_params['min_parts_refused'];
            $condition['maxpartsrefused'] = $contrib_params['max_parts_refused'];
            $condition['minartsrefused'] = $contrib_params['min_arts_refused'];
            $condition['maxartsrefused'] = $contrib_params['max_arts_refused'];
            $condition['noofdisapproved'] = $contrib_params['noof_disapproved'];
            $condition['selfdetails'] = trim(urldecode($contrib_params['contrib_self_details']));
            $condition['writeravgmarks'] = $contrib_params['writer_avg_marks'];
            $condition['correctoravgmarks'] = $contrib_params['corrector_avg_marks'];

            if ($contrib_params['total_contribs'] == 'yes')
                $condition['total_contribs'] = $contrib_params['total_contribs'];
            if ($contrib_params['never_participated'] == 'yes')
                $condition['never_participated'] = $contrib_params['never_participated'];
            if ($contrib_params['never_sent'] == 'yes')
                $condition['never_sent'] = $contrib_params['never_sent'];
            if ($contrib_params['never_validated'] == 'yes')
                $condition['never_validated'] = $contrib_params['never_validated'];
            if ($contrib_params['once_validated'] == 'yes')
                $condition['once_validated'] = $contrib_params['once_validated'];
            if ($contrib_params['once_published'] == 'yes')
                $condition['once_published'] = $contrib_params['once_published'];

            $sLimit1 = "";
            $contributors = $user_obj->loadContributor($sWhere, $sOrder, $sLimit1, $condition);

            $category = $this->_arrayDb->loadArrayv2("EP_ARTICLE_CATEGORY", $this->_lang);
            $nationality = $this->_arrayDb->loadArrayv2("Nationality", $this->_lang);
            $language = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
            $profession = $this->_arrayDb->loadArrayv2("CONTRIB_PROFESSION", $this->_lang);

            $file = 'excelFile-' . date("Y-M-D") . "-" . time() . '.xls';
            ob_start();
            $content = '<table border="1"> ';
            $content .= '<tr><th>Contributor Name</th><th>Email</th><th>Initial</th><th>First Name</th><th>Last Name</th><th>Status</th>';
            $content .= '<th>Date of Join</th><th>Profile Type</th><th>Black Status</th><th>City</th><th>State</th>';
            $content .= '<th>Country</th><th>Pin code</th>';
            $content .= '<th>Phone Number</th><th>DoB</th><th>University</th><th>Profession</th><th>Language</th><th>Category</th>';
            $content .= '<th>Description</th>';
            $content .= '</tr>';
            for ($i = 0; $i < count($contributors); $i++) {
                if ($contributors[$i]["first_name"] == '') {
                    $name = $contributors[$i]["email"];
                } else {
                    $name = $contributors[$i]["first_name"];
                }
                $content .= '<tr><td>' . $name . '</td><td>' . $contributors[$i]["email"] . '</td><td>' . $contributors[$i]["initial"] . '</td><td>' . $contributors[$i]["first_name"] . '</td>';
                $content .= '<td>' . $contributors[$i]["last_name"] . '</td><td>' . $contributors[$i]["status"] . '</td><td>' . date("d-m-Y", strtotime($contributors[$i]["created_at"])) . '</td>';
                $content .= '<td>' . $contributors[$i]["profile_type"] . '</td><td>' . $contributors[$i]["blackstatus"] . '</td>';
                $content .= '<td>' . $contributors[$i]["city"] . '</td><td>' . $contributors[$i]["state"] . '</td><td>' . $nationality[$contributors[$i]["country"]] . '</td><td>' . $contributors[$i]["zipcode"] . '</td>';
                $content .= '<td>' . $contributors[$i]["phone_number"] . '</td><td>' . $contributors[$i]["dob"] . '</td><td>' . $contributors[$i]["university"] . '</td>';
                $content .= '<td>' . $profession[$contributors[$i]["profession"]] . '</td><td>' . $language[$contributors[$i]["language"]] . '</td><td>' . $category[$contributors[$i]["favourite_category"]] . '</td>';

                $breaks = array("<br />", "<br>", "<br/>");
                $contributors[$i]["self_details"] = str_ireplace($breaks, "\r\n", $contributors[$i]["self_details"]);
                $content .= '<td>' . $contributors[$i]["self_details"] . '</td></tr>';
            }
            $content .= '</table>';
            // echo $content; exit;
            $_SESSION['content'] = $content;
            header("Location:/BO/download_users_xls.php");
            exit;
        }
        $sLimit1 = "";
        //$contributors = $user_obj->loadContributor($sWhere, $sOrder, $sLimit1, $condition);
        $this->_view->contribCount = $userdetails->getStatsContributorsCount();
        $this->render('user_contributors');
    }

    function toAscii($str, $replace = array(), $delimiter = '-')
    {
        if (!empty($replace)) {
            $str = str_replace((array)$replace, ' ', $str);
        }
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    public function loadcontributorAction()
    {
        $user_obj = new Ep_User_User();
        $aColumns = array('identifier', 'full_name', 'email', 'profile_type', 'status', 'created_at', 'category_more', 'language', 'contributortest', 'actions');
        /* * Paging	 */
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . intval($_GET['iDisplayStart']) . ", " .
                intval($_GET['iDisplayLength']);
        }
        /* 	 * Ordering   	 */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= "`" . $aColumns[intval($_GET['iSortCol_' . $i])] . "` " .
                        ($_GET['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }
        $sWhere = "";
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $sWhere = " HAVING (";
            for ($i = 1; $i < 3; $i++) {
                /*if($aColumns[$i] == 'status')
                    $aColumns[$i] = 'u.status';
                if($aColumns[$i] == 'created_at')
                    $aColumns[$i] = 'u.created_at';
                if($aColumns[$i] == 'actions')
                    break;*/

                $keyword = addslashes($_GET['sSearch']);
                $keyword = preg_replace('/\s*$/', '', $keyword);
                $keyword = preg_replace('/\(|\)/', '', $keyword);
                $words = explode(" ", $keyword);
                if (count($words) > 1) {
                    $sWhere .= $aColumns[$i] . " like '%" . utf8_decode($keyword) . "%' OR ";
                    foreach ($words as $key => $word) {
                        $word = trim($word);
                        if ($word != '') {
                            $sWhere .= "" . $aColumns[$i] . " LIKE '%" . utf8_decode($word) . "%' OR ";
                        }
                    }
                } else
                    $sWhere .= "" . $aColumns[$i] . " LIKE '%" . utf8_decode($keyword) . "%' OR ";
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere = " WHERE  ";
                } else {
                    $sWhere .= " AND  ";
                }
                $sWhere .= "`" . $aColumns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
            }
        }
        ///////////////////contributor details in search and normal grid display ////////
        $contrib_params = $this->_request->getParams();


        if ($contrib_params['searchsubmit'] == 'Search') {
            $urlarr = explode("&", urldecode($contrib_params['fullurl']));
            for ($i = 0; $i < count($urlarr); $i++) {
                if (preg_match('/categ/', $urlarr[$i]) && !preg_match('/category/', $urlarr[$i])) {
                    $urlarr[$i] = str_replace("categ[", "", $urlarr[$i]);
                    $urlarr[$i] = str_replace("]", "", $urlarr[$i]);
                    $categ[] = $urlarr[$i];
                }
                if (preg_match('/lange/', $urlarr[$i])) {
                    $urlarr[$i] = str_replace("lange[", "", $urlarr[$i]);
                    $urlarr[$i] = str_replace("]", "", $urlarr[$i]);
                    $lange[] = $urlarr[$i];
                }
            }
            $condition['searchsubmit'] = $contrib_params['searchsubmit'];
            $condition['start_date'] = $contrib_params['start_date'];
            $condition['end_date'] = $contrib_params['end_date'];
            $condition['act_start_date'] = $contrib_params['activity_start_date'];
            $condition['act_end_date'] = $contrib_params['activity_end_date'];
            $condition['aolist'] = $contrib_params['aoList'];
            $condition['arttitle'] = $contrib_params['arttitle'];
            $condition['contrib'] = $contrib_params['contrib'];
            $condition['type'] = $contrib_params['type'];
            $condition['type2'] = $contrib_params['type2'];
            $condition['status'] = $contrib_params['status'];
            $condition['blacklist'] = $contrib_params['blacklist'];
            $condition['nationalism'] = $contrib_params['nationalism'];
            $condition['category'] = $contrib_params['category'];
            $condition['categ'] = $categ;
            $condition['language'] = $contrib_params['language'];
            $condition['lange'] = $lange;
            $condition['aotitle'] = $contrib_params['aotitle'];
            $condition['minage'] = $contrib_params['minage'];
            $condition['maxage'] = $contrib_params['maxage'];
            $condition['minartsvalid'] = $contrib_params['min_arts_validated'];
            $condition['maxartsvalid'] = $contrib_params['max_arts_validated'];
            $condition['mintotalparts'] = $contrib_params['min_total_parts'];
            $condition['maxtotalparts'] = $contrib_params['max_total_parts'];
            $condition['minartssent'] = $contrib_params['min_arts_sent'];
            $condition['maxartssent'] = $contrib_params['max_arts_sent'];
            $condition['minpartsrefused'] = $contrib_params['min_parts_refused'];
            $condition['maxpartsrefused'] = $contrib_params['max_parts_refused'];
            $condition['minartsrefused'] = $contrib_params['min_arts_refused'];
            $condition['maxartsrefused'] = $contrib_params['max_arts_refused'];
            $condition['noofdisapproved'] = $contrib_params['noof_disapproved'];
            $selfdetails = trim(urldecode($contrib_params['contrib_self_details']));
            $condition['selfdetails'] = utf8_encode($selfdetails);
            $condition['writeravgmarks'] = $contrib_params['writer_avg_marks'];
            $condition['correctoravgmarks'] = $contrib_params['corrector_avg_marks'];
            $condition['contributortest'] = $contrib_params['contributortest'];

            // $condition['selfdetails'] =  trim(urldecode($contrib_params['contrib_self_details']));
        }

        if ($contrib_params['total_contribs'] == 'yes')
            $condition['total_contribs'] = $contrib_params['total_contribs'];
        if ($contrib_params['never_participated'] == 'yes')
            $condition['never_participated'] = $contrib_params['never_participated'];
        if ($contrib_params['never_sent'] == 'yes')
            $condition['never_sent'] = $contrib_params['never_sent'];
        if ($contrib_params['never_validated'] == 'yes')
            $condition['never_validated'] = $contrib_params['never_validated'];
        if ($contrib_params['once_validated'] == 'yes')
            $condition['once_validated'] = $contrib_params['once_validated'];
        if ($contrib_params['once_published'] == 'yes')
            $condition['once_published'] = $contrib_params['once_published'];
        $condition['hanging'] = $contrib_params['hanging'];



        $rResult = $user_obj->loadContributor($sWhere, $sOrder, $sLimit, $condition);
        $rResultcount = count($rResult);
        /////total count
        $sLimit = "";
        $countcontribs = $user_obj->loadContributor($sWhere, $sOrder, $sLimit, $condition);
        $iTotal = count($countcontribs);

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );
        $count = 1;
        if ($rResult != 'NO') {
            for ($i = 0; $i < $rResultcount; $i++) {
                $row = array();
                for ($j = 0; $j < count($aColumns); $j++) {
                    if ($j == 0)
                        $row[] = $count;
                    else {
                        if ($aColumns[$j] == 'full_name')
                            $row[] = utf8_encode($rResult[$i]['full_name']);
                        elseif ($aColumns[$j] == 'category_more') {
                            $cat_list = array();
                            if ($rResult[$i]['category_more'] != '') {
                                $catinfo = unserialize($rResult[$i]['category_more']);
                                if ($catinfo != '') {
                                    foreach ($catinfo as $key1 => $value1) {
                                        $cat_list[] = $this->category_array[$key1] . (($value1 > 0) ? ('(' . $value1 . '%)') : '');
                                    }
                                }
                                $row[] = utf8_encode(implode(',', $cat_list));
                            } else
                                $row[] = "-";
                        } /*elseif($aColumns[$j] == 'language_more')
                        {
                            $lang_list = array();
                            if($rResult[$i]['language_more'] != ''){
                                $laninfo=unserialize($rResult[$i]['language_more']) ;
                                if($laninfo != '')
                                {
                                    foreach ($laninfo as $key1 => $value1)
                                    {
                                        $lang_list[]=$this->language_array[$key1].( ($value1 > 0) ? ('(' . $value1 . '%)') : '' ) ;
                                    }
                                }
                                $row[]=utf8_encode(implode(',',$lang_list));
                            }
                            else
                                $row[]="-";
                        }*/
                        elseif ($aColumns[$j] == 'language') {
                            $lang_list = array();
                            if ($rResult[$i]['language_more'] != '' && $rResult[$i]['language_more'] != 'N;') {
                                $laninfo = unserialize($rResult[$i]['language_more']);
                                if ($laninfo != '') {
                                    foreach ($laninfo as $key1 => $value1) {
                                        $lang_list[] = $this->language_array[$key1] . (($value1 > 0) ? ('(' . $value1 . '%)') : '');
                                    }
                                }
                                $langmore = utf8_encode(implode(',', $lang_list));
                            } else
                                $langmore = "no more languages";
                            if ($rResult[$i][$aColumns[$j]] != '')
                                $row[] = '<a href="#" class="hint--left hint--info" data-hint="' . $langmore . '" >' . utf8_encode($this->language_array[$rResult[$i][$aColumns[$j]]]) . '</a>';
                            else
                                $row[] = "-";
                        } elseif ($aColumns[$j] == 'created_at' || $aColumns[$j] == 'u.created_at')
                            $row[] = date("d-m-Y", strtotime($rResult[$i]['created_at']));
                        elseif ($aColumns[$j] == 'profile_type') {
                            if ($rResult[$i]['profile_type'] == 'junior')
                                $row[] = '<span class="label label-info">JUNIOR</span>';
                            elseif ($rResult[$i]['profile_type'] == 'senior')
                                $row[] = '<span class="label label-info">SENIOR</span>';
                            elseif ($rResult[$i]['profile_type'] == 'sub-junior')
                                $row[] = '<span class="label label-info">d&eacute;buts</span>';
                            else
                                $row[] = '-';
                        } elseif ($aColumns[$j] == 'status' || $aColumns[$j] == 'u.status') {
                            if ($rResult[$i]['payment_type'] == 'paypal')
                                $row[] = $rResult[$i]['status'];
                            else {
                                $row[] = '<span id=userstatus_' . $rResult[$i]['identifier'] . '><a href="#"  onclick="return changeStatusUser(' . $rResult[$i]['identifier'] . ', \'' . $rResult[$i]['status'] . '\');" >' . $rResult[$i]['status'] . '</a></span>';
                            }
                        } elseif ($aColumns[$j] == 'actions') {   // echo  $rResult[$i]['identifier']; exit;
                            $email = $rResult[$i]['email'];
                            $password = $rResult[$i]['password'];
                            $type = "contributor";
                            if ($this->adminLogin->groupId == 1) {
                                $row[] = '<a href="brief-contributor-edit?submenuId=ML10-SL6&userId=' . $rResult[$i]['identifier'] . '" class="hint--left hint--info" data-hint="credentials edit"><i class="splashy-contact_blue_edit"></i> </a>
                                    <a href="contributor-edit-new?submenuId=ML10-SL6&tab=editcontrib&userId=' . $rResult[$i]['identifier'] . '" class="hint--left hint--info" data-hint="edit profile"><i class="icon-pencil"></i> </a>
                                   <a href="contributor-edit-new?submenuId=ML10-SL6&tab=viewcontrib&userId=' . $rResult[$i]['identifier'] . '" class="hint--left hint--info" data-hint="view profile" ><i class="icon-eye-open"></i></a>
                                  <a href="http://ep-test.edit-place.com/user/email-login?user=' . MD5("ep_login_" . $email) . '&hash=' . MD5("ep_login_" . $password) . '&type=' . $type . '&redirectpage=home" target="_blank"><i class="splashy-contact_blue"></i></a>';
                            } else {
                                $row[] = '<a href="contributor-edit-new?submenuId=ML10-SL6&tab=editcontrib&userId=' . $rResult[$i]['identifier'] . '" class="hint--left hint--info" data-hint="edit profile"><i class="icon-pencil"></i> </a>
                                   <a href="contributor-edit-new?submenuId=ML10-SL6&tab=viewcontrib&userId=' . $rResult[$i]['identifier'] . '" class="hint--left hint--info" data-hint="view profile" ><i class="icon-eye-open"></i></a>
                                  <a href="http://ep-test.edit-place.com/user/email-login?user=' . MD5("ep_login_" . $email) . '&hash=' . MD5("ep_login_" . $password) . '&type=' . $type . '&redirectpage=home" target="_blank"><i class="splashy-contact_blue"></i></a>';
                            }

                        } else
                            $row[] = $rResult[$i][$aColumns[$j]];
                    }
                }
                $output['aaData'][] = $row;
                $count++;
            }
        }
        echo json_encode($output);
    }

    public function clientsAction()
    {
        $userdetails = new Ep_User_UserPlus();
        $user_obj = new Ep_User_User();
        $usergrp_obj = new Ep_User_UserGroupAccess();
        $groups = $usergrp_obj->getAllUserGroupNames();
        $this->render('user_clients');
    }

    public function loadclientAction()
    {
        $userplus_obj = new Ep_User_UserPlus();
        $user_obj = new Ep_User_User();
        $aColumns = array('identifier', 'company_name', 'email', 'type', 'created_at', 'ao_count', 'art_count', 'art_pcount', 'download', 'actions');
        /* * Paging	 */
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . intval($_GET['iDisplayStart']) . ", " .
                intval($_GET['iDisplayLength']);
        }
        /* 	 * Ordering   	 */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= "`" . $aColumns[intval($_GET['iSortCol_' . $i])] . "` " .
                        ($_GET['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }
        $sWhere = "";
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $sWhere = " HAVING (";
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($aColumns[$i] == 'status')
                    $aColumns[$i] = 'u.status';
                if ($aColumns[$i] == 'created_at')
                    $aColumns[$i] = 'u.created_at';
                if ($aColumns[$i] == 'ao_count')
                    break;
                if ($aColumns[$i] == 'art_count')
                    break;
                if ($aColumns[$i] == 'art_pcount')
                    break;
                if ($aColumns[$i] == 'download')
                    break;
                if ($aColumns[$i] == 'actions')
                    break;
                $keyword = addslashes($_GET['sSearch']);
                $keyword = preg_replace('/\s*$/', '', $keyword);
                $keyword = preg_replace('/\(|\)/', '', $keyword);
                $words = explode(" ", $keyword);
                if (count($words) > 1) {
                    $sWhere .= $aColumns[$i] . " like '%" . utf8_decode($keyword) . "%' OR ";
                    foreach ($words as $key => $word) {
                        $word = trim($word);
                        if ($word != '') {
                            $sWhere .= "" . $aColumns[$i] . " LIKE '%" . utf8_decode($word) . "%' OR ";
                        }
                    }
                } else
                    $sWhere .= "" . $aColumns[$i] . " LIKE '%" . utf8_decode($keyword) . "%' OR ";
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }
        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere = " WHERE  ";
                } else {
                    $sWhere .= " AND  ";
                }
                $sWhere .= "`" . $aColumns[$i] . "` LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
            }
        }
        // echo $sWhere; exit;
        $rResult = $userplus_obj->ListStatsClientsinfo($sWhere, $sOrder, $sLimit, $condition);
        $rResultcount = count($rResult);

        /////total count
        $sLimit = "";
        $countclients = $userplus_obj->ListStatsClientsinfo($sWhere, $sOrder, $sLimit, $condition);
        $iTotal = count($countclients);

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );
        $count = 1;//print_r($rResult);
        if ($rResult != 'NO') //if non relavent data is given in search column//
        {
            for ($i = 0; $i < $rResultcount; $i++) {
                $row = array();
                for ($j = 0; $j < count($aColumns); $j++) {
                    echo $aColumns[$j];
                    if ($j == 0)
                        $row[] = $count;
                    else {
                        if ($aColumns[$j] == 'ao_count')
                            $row[] = '<a href="client-edit?submenuId=ML2-SL7&tab=aolistclient&userId=' . $rResult[$i]['identifier'] . '" class="num-large" target="_blank">' . $rResult[$i]['art_count'] . '</a>';
                        elseif ($aColumns[$j] == 'art_count')
                            $row[] = '<label class="label label-warning">' . $rResult[$i]['art_count'] . '</label>';
                        elseif ($aColumns[$j] == 'art_pcount')
                            $row[] = '<label class="label label-warning">' . $rResult[$i]['art_pcount'] . '</label>';
                        elseif ($aColumns[$j] == 'created_at')
                            $row[] = date("d-m-Y H:i", strtotime($rResult[$i]['created_at']));
                        elseif ($aColumns[$j] == 'download') {
                            if ($rResult[$i]['ao_count'] != 0)
                                $row[] = '<a href="http://ep-test.edit-place.com/getClientArticles.php?client_id=' . $rResult[$i]['identifier'] . '">Download</a>';
                            else
                                $row[] = '-';
                        } elseif ($aColumns[$j] == 'type') {
                            $row[] = '<label class="label label-info">' . $rResult[$i]['type'] . '</label>';
                        } elseif ($aColumns[$j] == 'actions') {
                            $email = $rResult[$i]['email'];
                            $password = $rResult[$i]['password'];
                            $type = $rResult[$i]['type'];//"client";
                            if ($type == 'client') {
                                $row[] = '<a href="client-edit?submenuId=ML10-SL2&tab=editclient&userId=' . $rResult[$i]['identifier'] . '" class="hint--left hint--info" data-hint="edit profile"><i class="icon-pencil"></i> </a>
                                    <a href="client-edit?submenuId=ML10-SL2&tab=viewclient&userId=' . $rResult[$i]['identifier'] . '" class="hint--left hint--info" data-hint="view profile"><i class="icon-eye-open"></i></a>
                                    <a href="http://ep-test.edit-place.com/user/email-login?user=' . MD5("ep_login_" . $email) . '&hash=' . MD5("ep_login_" . $password) . '&type=' . $type . '&redirectpage=home" target="_blank"><i class="splashy-contact_blue"></i></a>';
                            }
                            if ($type == 'superclient')
                                $row[] = '<a href="superclientcreate-step1?submenuId=ML9-SL1&uaction=edit&userId=' . $rResult[$i]['identifier'] . '" class="hint--left hint--info" data-hint="edit profile"><i class="icon-pencil"></i> </a>';
                            if ($type == 'sccontact')
                                $row[] = '';
                        } 
                        elseif($aColumns[$j] == 'company_name'){
                            $row[]=utf8_encode($rResult[$i]['company_name']);
                        }else
                            $row[] = $rResult[$i][$aColumns[$j]];
                    }
                }
                $output['aaData'][] = $row;
                $count++;
            }
        }
        // print_r($output);  exit;
        echo json_encode($output);

    }

    /*************************************************** SUPER CLIENT ***************************************************/

    //Super client creation page
    public function superClientCreateAction()
    {
        $this->_redirect("/user/superclientcreate-step1?submenuId=ML9-SL1");
        $super_client_params = $this->_request->getParams();
        $super_client_id = $super_client_params['userId'];
        if (!$this->adminLogin->super_client['cstatus_visible'])
            $this->adminLogin->super_client['cstatus_visible'] = array();

        if (($super_client_id) OR $super_client_params['uaction'] == 'edit') {
            $super_client_obj = new Ep_User_Client();

            $super_client_details = $super_client_obj->getSuperClientDetails($super_client_id);

            if ($super_client_details != 'NO') {
                $this->adminLogin->super_client['email'] = $super_client_details[0]['email'];
                $this->adminLogin->super_client['agency_name'] = $super_client_details[0]['company_name'];
                $this->adminLogin->super_client['clients'] = explode(",", $super_client_details[0]['access_clients_list']);
                $this->adminLogin->super_client['last_name'] = $super_client_details[0]['last_name'];
                $this->adminLogin->super_client['first_name'] = $super_client_details[0]['first_name'];
                $this->adminLogin->super_client['password'] = $super_client_details[0]['password'];
                $this->adminLogin->super_client['delay_validation'] = $super_client_details[0]['delay_validation'];
                $this->adminLogin->super_client['delay_option'] = $super_client_details[0]['delay_option'];


                $this->adminLogin->super_client['ao_list'] = explode(",", $super_client_details[0]['access_deliveries_list']);
                $this->adminLogin->super_client['langauge_ao'] = explode(",", $super_client_details[0]['access_lang_list']);
                $this->adminLogin->super_client['user_visible'] = $super_client_details[0]['user_visibility'];
                $this->adminLogin->super_client['status_visible'] = explode(",", $super_client_details[0]['access_article_status']);
                $this->adminLogin->super_client['comments_mandatory'] = $super_client_details[0]['comments_mandatory'];

                $this->adminLogin->super_client['client_id'] = $super_client_details[0]['identifier'];


                //superclient Logo
                $uploaddir = 'http://ep-test.edit-place.com/FO/profiles/superclients/logos/' . $this->adminLogin->super_client['client_id'] . '/';
                $logo_name = $this->adminLogin->super_client['client_id'] . "_global.png";
                $logo_path = '/home/sites/site5/web/FO/profiles/superclients/logos/' . $this->adminLogin->super_client['client_id'] . '/' . $logo_name;
                if (!is_dir($logo_path) && file_exists($logo_path)) {
                    $this->adminLogin->super_client['superclient_logo'] = $uploaddir . $logo_name . "?12345";
                }


                //getting All client contacts and adding to session

                $user_obj = new Ep_User_User();

                $clientContacts = $user_obj->getSuperClientContacts($super_client_details[0]['identifier']);
                if ($clientContacts != 'NO') {
                    $i = 0;
                    foreach ($clientContacts as $contact) {
                        $sc_contacts[$i]['cfirst_name'] = $contact['first_name'];
                        $sc_contacts[$i]['clast_name'] = $contact['last_name'];
                        $sc_contacts[$i]['cemail'] = $contact['email'];
                        $sc_contacts[$i]['cpassword'] = $contact['password'];
                        $sc_contacts[$i]['contact_id'] = $contact['identifier'];
                        $this->adminLogin->super_client['cstatus_visible'][$i] = explode(",", $contact['access_article_status']);
                        $i++;
                    }
                    $this->_view->sc_contacts = $sc_contacts;
                }
                //echo "<pre>";print_r($sc_contacts);exit;

            }

        } else {

            $sc_contacts = array();
            if (is_array($this->adminLogin->super_client['cemail']) && count($this->adminLogin->super_client['cemail']) > 0) {
                $i = 0;
                foreach ($this->adminLogin->super_client['cemail'] as $contact) {
                    $sc_contacts[$i]['cfirst_name'] = $this->adminLogin->super_client['cfirst_name'][$i];
                    $sc_contacts[$i]['clast_name'] = $this->adminLogin->super_client['clast_name'][$i];
                    $sc_contacts[$i]['cemail'] = $this->adminLogin->super_client['cemail'][$i];
                    $sc_contacts[$i]['cpassword'] = $this->adminLogin->super_client['cpassword'][$i];
                    if (!$this->adminLogin->super_client['cstatus_visible'][$i])
                        $this->adminLogin->super_client['cstatus_visible'][$i] = array();

                    $i++;
                }
            }


            //superclient Logo

            $uploaddir = 'http://ep-test.edit-place.com/FO/clientprofile_temp/templogo/';
            $logo_name = $this->adminLogin->super_client['logo_id'] . "_global.png";
            $logo_path = '/home/sites/site5/web/FO/clientprofile_temp/templogo/' . $logo_name;
            if (!is_dir($logo_path) && file_exists($logo_path)) {
                $this->adminLogin->super_client['superclient_logo'] = $uploaddir . $logo_name . "?12345";
            }


            $this->_view->sc_contacts = $sc_contacts;
            unset($this->adminLogin->super_client['client_id']);
        }
        //echo "<pre>";print_r($this->adminLogin->super_client);exit;
        $this->_view->super_client = $this->adminLogin->super_client;

        $this->render('super_client_create');
    }

    //save super client details in session
    public function saveSessionSuperClientAction()
    {

        $this->uploadSuperClientLogo($_FILES, $this->adminLogin->super_client['client_id']);

        if ($this->_request->isPost()) {
            $super_client_params = $this->_request->getParams();

            //logo file
            if ($_FILES['logo_super_client']['name'])
                $this->adminLogin->super_client['logo'] = $_FILES;


            if (!$this->adminLogin->super_client['client_id'])
                $this->adminLogin->super_client['email'] = $super_client_params['email'];

            if ($super_client_params['sc_contact_id'])
                $this->adminLogin->super_client['sc_contact_id'] = $super_client_params['sc_contact_id'];

            $this->adminLogin->super_client['agency_name'] = $super_client_params['agency_name'];
            $this->adminLogin->super_client['clients'] = $super_client_params['clients'];
            $this->adminLogin->super_client['last_name'] = $super_client_params['last_name'];
            $this->adminLogin->super_client['first_name'] = $super_client_params['first_name'];
            $this->adminLogin->super_client['password'] = $super_client_params['password'];
            $this->adminLogin->super_client['delay_validation'] = $super_client_params['delay_validation'];
            $this->adminLogin->super_client['delay_option'] = $super_client_params['delay_option'];

            if (is_array($super_client_params['clast_name']))
                $this->adminLogin->super_client['clast_name'] = $super_client_params['clast_name'];
            if (is_array($super_client_params['cfirst_name']))
                $this->adminLogin->super_client['cfirst_name'] = $super_client_params['cfirst_name'];
            if (is_array($super_client_params['cemail']))
                $this->adminLogin->super_client['cemail'] = $super_client_params['cemail'];
            if (is_array($super_client_params['cpassword']))
                $this->adminLogin->super_client['cpassword'] = $super_client_params['cpassword'];

            $this->adminLogin->super_client['cstatus_visible'] = array_values($super_client_params['cstatus_visible']);


            //echo "<pre>";print_r($this->adminLogin->super_client);exit;
            $this->_redirect("/user/super-client-permissions?submenuId=ML2-SL7");


        }

    }

    //Super client Permissions page
    public function superClientPermissionsAction()
    {
        if ($this->adminLogin->super_client['email']) {
            if (!$this->adminLogin->super_client['status_visible'])
                $this->adminLogin->super_client['status_visible'] = array();
            $this->_view->super_client = $this->adminLogin->super_client;
            $this->render('super_client_permissions');
        } else
            $this->_redirect("/user/super-client-create?submenuId=ML9-SL1");


    }

    //save super client details and Permissions
    public function saveSuperClientAction()
    {
        if ($this->_request->isPost()) {
            $super_client_permissions = $this->_request->getParams();

            $user_obj = new Ep_User_User();
            $userplus_obj = new Ep_User_UserPlus();
            $client_obj = new Ep_User_Client();


            $this->adminLogin->super_client['langauge_ao'] = $super_client_permissions['langauge_ao'];
            $this->adminLogin->super_client['ao_list'] = $super_client_permissions['ao_list'];
            $this->adminLogin->super_client['user_visible'] = $super_client_permissions['user_visible'];
            $this->adminLogin->super_client['status_visible'] = $super_client_permissions['status_visible'];
            $this->adminLogin->super_client['comments_mandatory'] = $super_client_permissions['comments_mandatory'];


            //echo "<pre>";print_r($this->adminLogin->super_client);exit;
            //$this->uploadSuperClientLogo($_FILES,$this->adminLogin->super_client['client_id']);exit;

            if ($this->adminLogin->super_client['email'] && $this->adminLogin->super_client['password'] && !$this->adminLogin->super_client['client_id']) {

                $user_obj->email = strip_tags($this->adminLogin->super_client['email']);
                $user_obj->password = $this->adminLogin->super_client['password'];
                $user_obj->status = 'Active';
                $user_obj->type = 'superclient';
                $user_obj->profile_type = '';
                $user_obj->created_by = 'backend';
                $user_obj->created_at = date("Y-m-d H:i:s");

                if ($user_obj->insert()) {

                    $client_identifier = $user_obj->getIdentifier();


                    if ($this->adminLogin->super_client['logo_id']) {
                        $uploaddir = '/home/sites/site5/web/FO/clientprofile_temp/templogo/';

                        $logo_id = $this->adminLogin->super_client['logo_id'];
                        $file = $uploaddir . $logo_id . ".png";
                        $file_global1 = $uploaddir . $logo_id . "_global.png";
                        $file_global2 = $uploaddir . $logo_id . "_global1.png";

                        $uploadcdir = '/home/sites/site5/web/FO/profiles/superclients/logos/' . $client_identifier . '/';
                        if (!is_dir($uploadcdir)) {
                            mkdir($uploadcdir, 0777);
                            chmod($uploadcdir, 0777);
                        }
                        $nfile = $uploadcdir . $client_identifier . ".png";
                        $nfile_global1 = $uploadcdir . $client_identifier . "_global.png";
                        $nfile_global2 = $uploadcdir . $client_identifier . "_global1.png";

                        rename($file, $nfile);
                        rename($file_global1, $nfile_global1);
                        rename($file_global2, $nfile_global2);

                        unlink($file);
                        unlink($file_global1);
                        unlink($file_global2);
                    }

                    //updating user table
                    $suser_array['created_at'] = 'backend';
                    $suser_array['created_user'] = $this->adminLogin->userId;
                    $where = " identifier='" . $client_identifier . "'";
                    $user_obj->updateUser($suser_array, $where);


                    //inserting in Client table

                    $client_data['user_id'] = $client_identifier;
                    $client_data['company_name'] = $this->adminLogin->super_client['agency_name'];

                    if (is_array($this->adminLogin->super_client['clients']))
                        $client_data['access_clients_list'] = implode(",", $this->adminLogin->super_client['clients']);

                    if (is_array($this->adminLogin->super_client['ao_list']))
                        $client_data['access_deliveries_list'] = implode(",", $this->adminLogin->super_client['ao_list']);

                    if (is_array($this->adminLogin->super_client['langauge_ao']))
                        $client_data['access_lang_list'] = implode(",", $this->adminLogin->super_client['langauge_ao']);

                    $client_data['delay_validation'] = $this->adminLogin->super_client['delay_validation'];
                    $client_data['delay_option'] = $this->adminLogin->super_client['delay_option'];
                    $client_data['user_visibility'] = $this->adminLogin->super_client['user_visible'];

                    if (is_array($this->adminLogin->super_client['status_visible']))
                        $client_data['access_article_status'] = implode(",", $this->adminLogin->super_client['status_visible']);

                    $client_data['comments_mandatory'] = $this->adminLogin->super_client['comments_mandatory'];

                    $superclient_contact_data = $client_data;

                    $client_obj->insertClient($client_data);
                    //echo "<pre>";print_r($user_obj);
                    //echo "<pre>";print_r($userplus_obj);
                    //echo "<pre>";print_r($client_obj);

                    //Inserting super client contacts
                    if (is_array($this->adminLogin->super_client['cemail']) && count($this->adminLogin->super_client['cemail']) > 0) {
                        $i = 0;
                        foreach ($this->adminLogin->super_client['cemail'] as $contact) {
                            if ($this->adminLogin->super_client['cemail'][$i] && $this->adminLogin->super_client['cpassword'][$i]) {
                                $user_obj = new Ep_User_User();
                                $userplus_obj = new Ep_User_UserPlus();
                                $client_obj = new Ep_User_Client();

                                $user_obj->email = strip_tags($this->adminLogin->super_client['cemail'][$i]);
                                $user_obj->password = $this->adminLogin->super_client['cpassword'][$i];
                                $user_obj->status = 'Active';
                                $user_obj->type = 'sccontact';
                                $user_obj->profile_type = '';
                                $user_obj->created_by = 'backend';
                                $user_obj->created_at = date("Y-m-d H:i:s");


                                if ($user_obj->insert()) {

                                    $contact_identifier = $user_obj->getIdentifier();

                                    //updating user table
                                    $user_array['created_by'] = 'backend';
                                    $user_array['superclient_reference'] = $client_identifier;
                                    $where = " identifier='" . $contact_identifier . "'";
                                    $user_obj->updateUser($user_array, $where);

                                    if ($this->adminLogin->super_client['cfirst_name'][$i] && $this->adminLogin->super_client['clast_name'][$i]) {
                                        //INserting in Userplus table
                                        $userplus_obj->user_id = $contact_identifier;
                                        $userplus_obj->first_name = $this->adminLogin->super_client['cfirst_name'][$i];
                                        $userplus_obj->last_name = $this->adminLogin->super_client['clast_name'][$i];
                                        $userplus_obj->address = '';
                                        $userplus_obj->city = '';
                                        $userplus_obj->state = '';
                                        $userplus_obj->zipcode = '';
                                        $userplus_obj->country = '';
                                        $userplus_obj->phone_number = '';
                                        $userplus_obj->insert();
                                    }

                                    //inserting permissions w.r.t Client contact
                                    $client_contact_data = $superclient_contact_data;
                                    $client_contact_data['user_id'] = $contact_identifier;
                                    $client_contact_data['access_article_status'] = implode(",", $this->adminLogin->super_client['cstatus_visible'][$i]);
                                    $client_obj->insertClient($client_contact_data);


                                }

                            }
                            $i++;
                        }
                    }
                }
                unset($this->adminLogin->super_client);
                $this->_redirect("/ongoing/super-client-list?submenuId=ML9-SL2");

            } elseif ($this->adminLogin->super_client['client_id'])//updating the details
            {
                $client_id = $this->adminLogin->super_client['client_id'];


                $user_array['password'] = $this->adminLogin->super_client['password'];
                $user_array['updated_at'] = date("Y-m-d H:i:s");
                $where = " identifier='" . $client_id . "'";
                $user_obj->updateUser($user_array, $where);

                //inserting in Client table
                $client_data['company_name'] = $this->adminLogin->super_client['agency_name'];

                if (is_array($this->adminLogin->super_client['clients']))
                    $client_data['access_clients_list'] = implode(",", $this->adminLogin->super_client['clients']);

                if (is_array($this->adminLogin->super_client['ao_list']))
                    $client_data['access_deliveries_list'] = implode(",", $this->adminLogin->super_client['ao_list']);

                if (is_array($this->adminLogin->super_client['langauge_ao']))
                    $client_data['access_lang_list'] = implode(",", $this->adminLogin->super_client['langauge_ao']);

                $client_data['delay_validation'] = $this->adminLogin->super_client['delay_validation'];
                $client_data['delay_option'] = $this->adminLogin->super_client['delay_option'];
                $client_data['user_visibility'] = $this->adminLogin->super_client['user_visible'];

                if (is_array($this->adminLogin->super_client['status_visible']))
                    $client_data['access_article_status'] = implode(",", $this->adminLogin->super_client['status_visible']);

                $client_data['comments_mandatory'] = $this->adminLogin->super_client['comments_mandatory'];
                $superclient_contact_data = $client_data;
                $client_obj->updateClientProfile($client_data, $client_id);


                //Updating or inserting Client contacts
                if (is_array($this->adminLogin->super_client['cemail']) && count($this->adminLogin->super_client['cemail']) > 0) {
                    $i = 0;
                    foreach ($this->adminLogin->super_client['cemail'] as $contact) {

                        $user_obj = new Ep_User_User();
                        $userplus_obj = new Ep_User_UserPlus();
                        $client_obj = new Ep_User_Client();

                        if ($this->adminLogin->super_client['cemail'][$i] && $this->adminLogin->super_client['cpassword'][$i] && !$this->adminLogin->super_client['sc_contact_id'][$i]) {

                            $user_obj->email = strip_tags($this->adminLogin->super_client['cemail'][$i]);
                            $user_obj->password = $this->adminLogin->super_client['cpassword'][$i];
                            $user_obj->status = 'Active';
                            $user_obj->type = 'sccontact';
                            $user_obj->profile_type = '';
                            $user_obj->created_by = 'backend';
                            $user_obj->created_at = date("Y-m-d H:i:s");

                            if ($user_obj->insert()) {

                                $contact_identifier = $user_obj->getIdentifier();

                                //updating user table
                                $user_array['created_by'] = 'backend';
                                $user_array['superclient_reference'] = $client_id;
                                $where = " identifier='" . $contact_identifier . "'";
                                $user_obj->updateUser($user_array, $where);

                                if ($this->adminLogin->super_client['cfirst_name'][$i] && $this->adminLogin->super_client['clast_name'][$i]) {
                                    //INserting in Userplus table
                                    $userplus_obj->user_id = $contact_identifier;
                                    $userplus_obj->first_name = $this->adminLogin->super_client['cfirst_name'][$i];
                                    $userplus_obj->last_name = $this->adminLogin->super_client['clast_name'][$i];
                                    $userplus_obj->address = '';
                                    $userplus_obj->city = '';
                                    $userplus_obj->state = '';
                                    $userplus_obj->zipcode = '';
                                    $userplus_obj->country = '';
                                    $userplus_obj->phone_number = '';
                                    $userplus_obj->insert();
                                }
                                //inserting permissions w.r.t Client contact
                                $client_contact_data = $superclient_contact_data;
                                $client_contact_data['user_id'] = $contact_identifier;
                                $client_contact_data['access_article_status'] = implode(",", $this->adminLogin->super_client['cstatus_visible'][$i]);
                                $client_obj->insertClient($client_contact_data);


                            }

                        } else if ($this->adminLogin->super_client['cemail'][$i] && $this->adminLogin->super_client['cpassword'][$i] && $this->adminLogin->super_client['sc_contact_id'][$i]) {
                            $contact_id = $this->adminLogin->super_client['sc_contact_id'][$i];

                            $user_array['password'] = $this->adminLogin->super_client['cpassword'][$i];
                            $user_array['updated_at'] = date("Y-m-d H:i:s");
                            $where = " identifier='" . $contact_id . "'";
                            $user_obj->updateUser($user_array, $where);


                            $data_userplus = array("first_name" => $this->adminLogin->super_client['cfirst_name'][$i], "last_name" => $this->adminLogin->super_client['clast_name'][$i]);
                            $query_userplus = "user_id= '" . $contact_id . "'";
                            $userplus_obj->updateUserPlus($data_userplus, $query_userplus);


                            //inserting permissions w.r.t Client contact
                            $client_contact_data = $superclient_contact_data;
                            $client_contact_data['user_id'] = $contact_id;
                            $client_contact_data['access_article_status'] = implode(",", $this->adminLogin->super_client['cstatus_visible'][$i]);
                            $client_obj->updateClientProfile($client_contact_data, $contact_id);
                        }
                        $i++;
                    }
                }
            }

            unset($this->adminLogin->super_client);
            $this->_redirect("/ongoing/super-client-list?submenuId=ML9-SL2");

        }
    }


    //delete super client contact
    public function deleteScContactAction()
    {
        $profile_params = $this->_request->getParams();
        $experience_obj = new Ep_User_User();

        if ($profile_params['type'] && $profile_params['identifier']) {
            $identifier = $profile_params['identifier'];
            if ($profile_params['type'] == 'sccontact') {
                $experience_obj->deleteScContact($identifier);
            }
        }

    }

    //AJax function to get Deliveries w.r.t Lang and Clients
    public function getDeliveriesAction()
    {
        $this->SC_creation = Zend_Registry::get('SC_creation');
        $ao_params = $this->_request->getParams();

        $client_obj = new Ep_User_Client();

        $languages = $ao_params['languages'];
        $clients = $ao_params['clients'];

        if ($languages && $clients) {
            //$clients=$this->adminLogin->super_client['clients'];
            $languages = explode(",", $languages);
            $clients = explode(",", $clients);

            $aoList = $client_obj->getDeliveries($clients, $languages);

            if ($aoList != 'NO') {
                $deliveries = '<select multiple data-placeholder="Choose AO..." class="span6" name="ao[' . ($ao_params['index'] - 1) . '][]" id="ao_' . $ao_params['index'] . '">';
                foreach ($aoList as $delivery) {
                    if (is_array($this->SC_creation->scpermissions['ao'][$ao_params['index'] - 1])) {
                        if (in_array($delivery['id'], $this->SC_creation->scpermissions['ao'][$ao_params['index'] - 1])) {
                            $deliveries .= '<option value="' . $delivery['id'] . '" selected>' . utf8_encode($delivery['title']) . '</option>';
                        } else {
                            $deliveries .= '<option value="' . $delivery['id'] . '">' . utf8_encode($delivery['title']) . '</option>';
                        }
                    } else
                        $deliveries .= '<option value="' . $delivery['id'] . '">' . utf8_encode($delivery['title']) . '</option>';
                }
                $deliveries .= '</select>';
                echo $deliveries;
            } else
                echo "NO Ao";
        } else
            echo "Select Client and Language";
        exit;

    }

    //AJax function to get Articles w.r.t Ao
    public function getArticlesAction()
    {
        $this->SC_creation = Zend_Registry::get('SC_creation');
        $ao_params = $this->_request->getParams();

        $art_obj = new EP_Delivery_Article();

        $ao = $ao_params['ao'];

        if ($ao) {
            $ao = explode(",", $ao);

            $articleList = $art_obj->LoadArticles($ao);

            if ($articleList != 'NO') {
                $articles = '<select multiple data-placeholder="Choose Article..." class="span6" name="article[' . ($ao_params['index'] - 1) . '][]" id="article_' . $ao_params['index'] . '" >';
                foreach ($articleList as $article) {
                    if (is_array($this->SC_creation->scpermissions['article'][$ao_params['index'] - 1])) {
                        if (in_array($article['id'], $this->SC_creation->scpermissions['article'][$ao_params['index'] - 1])) {
                            $articles .= '<option value="' . $article['id'] . '" selected>' . utf8_encode($article['title']) . '</option>';
                        } else {
                            $articles .= '<option value="' . $article['id'] . '">' . utf8_encode($article['title']) . '</option>';
                        }
                    } else
                        $articles .= '<option value="' . $article['id'] . '">' . utf8_encode($article['title']) . '</option>';
                }
                $articles .= '</select>';
                echo $articles;
            } else
                echo "NO Articles";
        } else
            echo "Select AO";
        exit;

    }

    //AJax function to get Deliveries w.r.t Lang and Clients
    public function getDeliveriescontactAction()
    {
        $this->SC_creation = Zend_Registry::get('SC_creation');
        $ao_params = $this->_request->getParams();

        $client_obj = new Ep_User_Client();

        $languages = $ao_params['languages'];
        $clients = $ao_params['clients'];

        if ($languages && $clients) {
            //$clients=$this->adminLogin->super_client['clients'];
            $languages = explode(",", $languages);//echo $clients;exit;
            $clients = explode(",", $clients);

            $aocount = count($clients) - 1;
            $deliveries = "<table cellpadding=2 cellspacing=2>";
            for ($c = 0; $c < count($clients); $c++) {
                $aoList = $client_obj->getDeliveriesClientwise($clients[$c], $languages);
                $clientDetail = $client_obj->getClientRecord($clients[$c]);

                if ($clientDetail[0]['company_name'] != "")
                    $cname = $clientDetail[0]['company_name'];
                else
                    $cname = $clientDetail[0]['email'];

                $deliveries .= '<tr style="vertical-align:top;"><td>' . $cname . ' Aos :&nbsp;</td>';
                //if($aoList!='NO')
                //{
                $allcheck = "";
                $futurecheck = "";
                $selectcheck = "";
                $showselect = "display:none";

                if ($this->SC_creation->sccontact['ao'][$ao_params['index'] - 1][$clients[$c]] == "all")
                    $allcheck = "checked";
                elseif ($this->SC_creation->sccontact['ao'][$ao_params['index'] - 1][$clients[$c]] == "future")
                    $futurecheck = "checked";
                else {
                    $selectcheck = "checked";
                    $showselect = "";
                    $aos = explode(",", $this->SC_creation->sccontact['ao'][$ao_params['index'] - 1][$clients[$c]]);
                }
                $deliveries .= '<td><input type="radio" name="aotype[' . ($ao_params['index'] - 1) . '][' . $clients[$c] . ']" value="all" onClick="toggleselect(' . $ao_params['index'] . ',' . $clients[$c] . ')" ' . $allcheck . '><b>All&nbsp;</b></td>
									<td><input type="radio" name="aotype[' . ($ao_params['index'] - 1) . '][' . $clients[$c] . ']" value="future" onClick="toggleselect(' . $ao_params['index'] . ',' . $clients[$c] . ')" ' . $futurecheck . '><b>Future</b>&nbsp;</td>
									<td><span style="vertical-align:top;"><input type="radio" name="aotype[' . ($ao_params['index'] - 1) . '][' . $clients[$c] . ']" value="select" onClick="toggleselect(' . $ao_params['index'] . ',' . $clients[$c] . ')" ' . $selectcheck . '><b>Select&nbsp;</b></span>
									<span id="clientblock_' . $clients[$c] . '_' . $ao_params['index'] . '" style="' . $showselect . '">';

                $deliveries .= '<select multiple data-placeholder="Choose AO..." class="span6 ao" name="ao[' . ($ao_params['index'] - 1) . '][' . $clients[$c] . '][]" id="ao_' . $clients[$c] . '_' . $ao_params['index'] . '" >';
                if ($aoList != 'NO') {

                    foreach ($aoList as $delivery) {
                        if (is_array($aos)) {
                            if (in_array($delivery['id'], $aos)) {
                                $deliveries .= '<option value="' . $delivery['id'] . '" selected>' . utf8_encode($delivery['title']) . '</option>';
                            } else {
                                $deliveries .= '<option value="' . $delivery['id'] . '">' . utf8_encode($delivery['title']) . '</option>';
                            }
                        } else
                            $deliveries .= '<option value="' . $delivery['id'] . '">' . utf8_encode($delivery['title']) . '</option>';
                    }
                    $deliveries .= '</select>';

                }
                //else
                //$deliveries.="NO Ao";
                $deliveries .= '</span><span id="err_' . $clients[$c] . '_' . $ao_params['index'] . '"></span></td></tr>';
            }
            $deliveries .= '</table>';
            echo $deliveries;
        } else
            echo "Select Client and Language";
        exit;

    }

    //AJax function to get Articles w.r.t Ao
    public function getArticlescontactAction()
    {
        $this->SC_creation = Zend_Registry::get('SC_creation');
        $ao_params = $this->_request->getParams();

        $art_obj = new EP_Delivery_Article();

        $ao = $ao_params['ao'];

        if ($ao) {
            $ao = explode(",", $ao);

            $articleList = $art_obj->LoadArticles($ao);

            if ($articleList != 'NO') {
                $articles = '<select multiple data-placeholder="Choose Article..." class="span6" name="article[' . ($ao_params['index'] - 1) . '][]" id="article_' . $ao_params['index'] . '" >';
                foreach ($articleList as $article) {
                    if (is_array($this->SC_creation->sccontact['article'][$ao_params['index'] - 1])) {
                        if (in_array($article['id'], $this->SC_creation->sccontact['article'][$ao_params['index'] - 1])) {
                            $articles .= '<option value="' . $article['id'] . '" selected>' . utf8_encode($article['title']) . '</option>';
                        } else {
                            $articles .= '<option value="' . $article['id'] . '">' . utf8_encode($article['title']) . '</option>';
                        }
                    } else
                        $articles .= '<option value="' . $article['id'] . '">' . utf8_encode($article['title']) . '</option>';
                }
                $articles .= '</select>';
                echo $articles;
            } else
                echo "No Articles";
        } else
            echo "Select AO";
        exit;

    }

    /*
    //upload superclient logo
    public function uploadSuperClientLogo($_FILES,$client_id=NULL)
    {

        if($client_id)
            $uploaddir = '/home/sites/site5/web/FO/profiles/superclients/logos/'.$client_id.'/';
        else
        {
            $uploaddir='/home/sites/site5/web/FO/clientprofile_temp/templogo/';

            if($this->adminLogin->super_client['logo_id'])
            {
                $logo_id=$this->adminLogin->super_client['logo_id'];
                $file = $uploaddir.$logo_id.".png";
                $file_global1= $uploaddir.$logo_id."_global.png";
                $file_global2= $uploaddir.$logo_id."_global1.png";

                if(file_exists($file))unlink($file);
                if(file_exists($file_global1))unlink($file_global1);
                if(file_exists($file_global2))unlink($file_global2);
            }

            $client_id=mt_rand(1, 99999);
            $this->adminLogin->super_client['logo_id']=$client_id;


        }

        if(!is_dir($uploaddir))
        {
            mkdir($uploaddir,0777);
            chmod($uploaddir,0777);
        }
        $file = $uploaddir.$client_id.".png";
        $file_global1= $uploaddir.$client_id."_global.png";
        $file_global2= $uploaddir.$client_id."_global1.png";
        list($width, $height)  = getimagesize($_FILES['logo_super_client']['tmp_name']);

        if($width>=90 || $height>=90)
        {
            if (move_uploaded_file($_FILES['logo_super_client']['tmp_name'], $file))
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
            }
       }
    }
    */
    public function superclientcreateStep1Action()
    {
        $this->SC_creation = Zend_Registry::get('SC_creation');

        $super_client_params = $this->_request->getParams();
        $ref_obj = new Ep_User_DeliveryReference();

        if ($_GET['uaction'] == 'edit') {
            $deliveryref_id = $super_client_params['refId'];
            //clear sessions
            unset($this->SC_creation->scpermissions);
            unset($this->SC_creation->sccontact);

            $this->_view->deliveryrefernce = $deliveryref_id;
            $this->SC_creation->scpermissions['superclient'] = $deliveryref_id;
            //SC permissions
            $refdetail = $ref_obj->getRefRecord($deliveryref_id);
            //print_r($refdetail);exit;
            $this->SC_creation->scpermissions['reference_title'] = $refdetail[0]['title'];
            $this->SC_creation->scpermissions['clients'] = explode(",", $refdetail[0]['access_client']);
            $this->SC_creation->scpermissions['langauge_ao'] = explode(",", $refdetail[0]['access_language']);
            $this->SC_creation->scpermissions['cstatus_visible'] = explode(",", $refdetail[0]['access_permission']);
            $this->SC_creation->scpermissions['user_visible'] = $refdetail[0]['user_visibility'];

            if ($refdetail[0]['delay_validation'] != "" || $refdetail[0]['delay_validation'] != 0) {
                $this->SC_creation->scpermissions['delay_validation'] = $refdetail[0]['delay_validation'];
                $this->SC_creation->scpermissions['delay_option'] = $refdetail[0]['delay_option'];
            }


            //SC contact
            $contact_obj = new Ep_User_ScBoUserPermissions();
            $sccontact = $contact_obj->getScBoUserPermissions($deliveryref_id);
            //print_r($sccontact);
            for ($c = 0; $c < count($sccontact); $c++) {
                $this->SC_creation->sccontact['bouser'][$c] = $sccontact[$c]['bo_user'];
                $this->SC_creation->sccontact['clients'][$c] = explode(",", $sccontact[$c]['clients']);
                $this->SC_creation->sccontact['langauge_ao'][$c] = explode(",", $sccontact[$c]['access_language_list']);

                //Deliveries
                $deliveries = explode("|", $sccontact[$c]['deliveries']);

                for ($d = 0; $d < count($deliveries); $d++) {
                    if ($deliveries[$d] == "select")
                        $this->SC_creation->sccontact['ao'][$c][$this->SC_creation->sccontact['clients'][$c][$d]] = explode(",", $deliveries[$d]);
                    else
                        $this->SC_creation->sccontact['ao'][$c][$this->SC_creation->sccontact['clients'][$c][$d]] = $deliveries[$d];
                }//print_r($this->SC_creation->sccontact['clients'][$c]);print_r($this->SC_creation->sccontact['ao'][$c]);
                $this->SC_creation->sccontact['cstatus_visible'][$c] = explode(",", $sccontact[$c]['access_permissions']);
                $this->SC_creation->sccontact['user_visible'][$c] = $sccontact[$c]['writer_info'];
            }

        } else {
            if (!$_REQUEST['back']) {
                unset($this->SC_creation->scpermissions);
                unset($this->SC_creation->sccontact);
            }

        }

        if ($this->SC_creation->scpermissions) {
            $this->_view->modval = 1;

            $this->_view->superclient = $this->SC_creation->scpermissions['superclient'];
            $this->_view->clients = $this->SC_creation->scpermissions['clients'];
            $this->_view->langauge_ao = $this->SC_creation->scpermissions['langauge_ao'];
            $this->_view->cstatus_visible = $this->SC_creation->scpermissions['cstatus_visible'];
            $this->_view->user_visible = $this->SC_creation->scpermissions['user_visible'];
            $this->_view->reference_title = $this->SC_creation->scpermissions['reference_title'];
            if ($this->SC_creation->scpermissions['delay_validation'] != "" || $this->SC_creation->scpermissions['delay_validation'] != 0) {
                $this->_view->delay_validation = $this->SC_creation->scpermissions['delay_validation'];
                $this->_view->delay_option = $this->SC_creation->scpermissions['delay_option'];
            }
        } else {

            $this->_view->modval = 0;
            unset($this->SC_creation->scpermissions);
            unset($this->SC_creation->sccontact);
            $this->_view->superclient = "";
            $this->_view->cstatus_visible = array("writing_progress", "correction_ongoing", "stage2", "published_client", "published", "closed");
        }

        $this->render('superclientcreate-step1');
    }

    public function superclientcreateStep2Action()
    {
        $this->SC_creation = Zend_Registry::get('SC_creation');

        $super_client_params = $this->_request->getParams();

        if ($_POST['reference_title'] != "")
            $this->SC_creation->scpermissions = $super_client_params;

        //print_r($this->SC_creation->sccontact);
        $this->_view->refid = $this->SC_creation->scpermissions['deliveryrefernce'];

        $super_client_obj = new Ep_User_Client();

        $clients_step1 = array();

        foreach ($this->_view->client_array as $keyclt => $clt) {
            if (in_array($keyclt, $this->SC_creation->scpermissions['clients']))
                $clients_step1[$keyclt] = $clt;
        }
        $this->_view->clients_step1 = $clients_step1;


        $language = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        $language_step1 = array();

        foreach ($language as $keylang => $lang) {
            if (in_array($keylang, $this->SC_creation->scpermissions['langauge_ao']))
                $language_step1[$keylang] = $lang;
        }
        $this->_view->language_step1 = $language_step1;

        $client_obj = new Ep_User_Client();
        $this->_view->deliveries = $client_obj->getDeliveries($this->SC_creation->scpermissions['clients'], $this->SC_creation->scpermissions['langauge_ao']);

        $this->_view->status_step1 = $this->SC_creation->scpermissions['cstatus_visible'];

        if ($this->SC_creation->sccontact) {
            $this->_view->modval = 1;
            for ($s = 0; $s < count($this->SC_creation->sccontact['bouser']); $s++) {
                $bouser[] = $this->SC_creation->sccontact['bouser'][$s];
                $bouserDetails = $super_client_obj->getBOuserdetails($this->SC_creation->sccontact['bouser'][$s]);
                $bouseremail[] = $bouserDetails[0]['email'];
                $bouserpwd[] = $bouserDetails[0]['password'];
                $bolast_name[] = $bouserDetails[0]['last_name'];
                $bofirst_name[] = $bouserDetails[0]['first_name'];
                $clients[] = $this->SC_creation->sccontact['clients'][$s];
                $langauge_ao[] = $this->SC_creation->sccontact['langauge_ao'][$s];
                $ao[] = $this->SC_creation->sccontact['ao'][$s];
                $cstatus_visible[] = $this->SC_creation->sccontact['cstatus_visible'][$s];
            }
            $this->_view->bo_user = $bouser;
            $this->_view->bo_user_email = $bouseremail;
            $this->_view->bolast_name = $bolast_name;
            $this->_view->bofirst_name = $bofirst_name;
            $this->_view->bo_user_password = $bouserpwd;
            $this->_view->clients = $clients;
            $this->_view->langauge_ao = $langauge_ao;
            $this->_view->ao = $ao;
            $this->_view->cstatus_visible = $cstatus_visible;

            $this->_view->bouser_array = $super_client_obj->getChiefodigeoself($this->SC_creation->scpermissions['deliveryrefernce']);
        } else {
            $this->_view->modval = 0;
            $this->_view->bouser_array = $super_client_obj->getChiefodigeo();
        }

        $this->render('superclientcreate-step2');
    }

    public function superclientcreateStep3Action()
    {
        $this->SC_creation = Zend_Registry::get('SC_creation');
        //ini_set('error_reporting', E_ALL);
        $super_client_params = $this->_request->getParams();

        if (count($super_client_params['clients']) > 0) {
            $this->SC_creation->sccontact = $super_client_params;

            $this->SC_creation->sccontact['bouser'] = $this->SC_creation->sccontact['bouser'];
            $this->SC_creation->sccontact['bofirst_name'] = $this->SC_creation->sccontact['bofirst_name'];
            $this->SC_creation->sccontact['bolast_name'] = $this->SC_creation->sccontact['bolast_name'];
            $this->SC_creation->sccontact['boemail'] = $this->SC_creation->sccontact['boemail'];
            $this->SC_creation->sccontact['bopassword'] = $this->SC_creation->sccontact['bopassword'];


            $this->SC_creation->sccontact['clients'] = $this->SC_creation->sccontact['clients'];
            $this->SC_creation->sccontact['langauge_ao'] = $this->SC_creation->sccontact['langauge_ao'];
            $this->SC_creation->sccontact['ao'] = $this->SC_creation->sccontact['ao'];
            $this->SC_creation->sccontact['cstatus_visible'] = $this->SC_creation->sccontact['cstatus_visible'];
            $this->SC_creation->sccontact['user_visible'] = $this->SC_creation->sccontact['user_visible'];
        }


        //print_r($this->SC_creation->sccontact);exit;
        $user_obj = new Ep_User_User();
        $userplus_obj = new Ep_User_UserPlus();
        $client_obj = new Ep_User_Client();
        $uarray = array();

        if ($this->SC_creation->scpermissions['deliveryrefernce'] != "")
            $this->_view->displaytext = "Superclient updated successfully !!";
        else
            $this->_view->displaytext = "Superclient created successfully !!";


        //Insert SC permissions
        if ($this->SC_creation->scpermissions) {
            $Reference_obj = new Ep_User_DeliveryReference();
            $Parray = array();
            $Parray['access_client'] = implode(",", $this->SC_creation->scpermissions['clients']);
            $Parray['access_language'] = implode(",", $this->SC_creation->scpermissions['langauge_ao']);
            $Parray['access_permission'] = implode(",", $this->SC_creation->scpermissions['cstatus_visible']);
            $Parray['user_visibility'] = $this->SC_creation->scpermissions['user_visible'];
            $Parray['title'] = $this->SC_creation->scpermissions['reference_title'];
            if ($this->SC_creation->scpermissions['delay_validation'] != "" || $this->SC_creation->scpermissions['delay_validation'] != 0) {
                $Parray['delay_validation'] = $this->SC_creation->scpermissions['delay_validation'];
                $Parray['delay_option'] = $this->SC_creation->scpermissions['delay_option'];
            }

            if ($this->SC_creation->scpermissions['deliveryrefernce'] != "") {
                $ref_id = $this->SC_creation->scpermissions['deliveryrefernce'];
                $Reference_obj->UpdateReference($Parray, $this->SC_creation->scpermissions['deliveryrefernce']);
            } else {
                $ref_id = $Reference_obj->InsertReference($Parray);
            }
        }


        //Insert SC contact
        if ($this->SC_creation->sccontact) {
            $sccontact_obj = new Ep_User_ScBoUserPermissions();
            if (count($this->SC_creation->sccontact['clients']) > 0 && $this->SC_creation->scpermissions['deliveryrefernce'] != "")
                $sccontact_obj->deleteScBoUserPermissions($this->SC_creation->scpermissions['deliveryrefernce']);

            $contact_keys = array_keys($this->SC_creation->sccontact['clients']);
            for ($s = 0; $s < count($this->SC_creation->sccontact['clients']); $s++) {
                $k = $contact_keys[$s];
                $Carray = array();
                $Carray['reference'] = $ref_id;

                $userbo_obj = new Ep_User_User();
                $checkmail = $userbo_obj->checkClientMailid($this->SC_creation->sccontact['boemail'][$k]);
                //echo $checkmail;exit;
                if ($checkmail == "true") {
                    $userbo_obj->login = "NULL";
                    $userbo_obj->email = $this->SC_creation->sccontact['boemail'][$k];
                    $userbo_obj->password = $this->SC_creation->sccontact['bopassword'][$k];
                    $userbo_obj->status = "Active";
                    $userbo_obj->type = "chiefodigeo";
                    $userbo_obj->profile_type = "";


                    if ($userbo_obj->insert()) {
                        $user_identifier = $userbo_obj->getIdentifier();
                        $userplus_obj->user_id = $user_identifier;
                        $userplus_obj->first_name = $this->SC_creation->sccontact['bofirst_name'][$k];
                        $userplus_obj->last_name = $this->SC_creation->sccontact['bolast_name'][$k];
                        $userplus_obj->address = "NULL";
                        $userplus_obj->insert();
                    }

                    $Carray['bo_user'] = $user_identifier;
                } else {

                    $Uarray['password'] = $this->SC_creation->sccontact['bopassword'][$k];
                    $Uwhere = " email='" . $this->SC_creation->sccontact['boemail'][$k] . "'";
                    $userbo_obj->updateUser($Uarray, $Uwhere);

                    $Uparray['first_name'] = $this->SC_creation->sccontact['bofirst_name'][$k];
                    $Uparray['last_name'] = $this->SC_creation->sccontact['bolast_name'][$k];
                    $Upwhere = " user_id='" . $this->SC_creation->sccontact['bouser'][$k] . "'";
                    $userplus_obj->updateUserPlus($Uparray, $Upwhere);
                    $Carray['bo_user'] = $this->SC_creation->sccontact['bouser'][$k];
                }
                $Carray['access_language_list'] = implode(",", $this->SC_creation->sccontact['langauge_ao'][$k]);
                $Carray['access_permissions'] = implode(",", $this->SC_creation->sccontact['cstatus_visible'][$k]);
                $Carray['writer_info'] = $this->SC_creation->scpermissions['user_visible'];

                for ($l = 0; $l < count($this->SC_creation->sccontact['clients'][$k]); $l++) {
                    $contact_obj = new Ep_User_ScBoUserPermissions();

                    //Check bouser-client exists
                    //$bouserdeatil=$contact_obj->getScBoUserPermissionsbyClient($Carray['bo_user'],$this->SC_creation->sccontact['clients'][$k][$l]);
                    $overwrite = 'overwrite_' . $this->SC_creation->sccontact['clients'][$k][$l] . '_' . ($k + 1);
                    $client = $this->SC_creation->sccontact['clients'][$k][$l];

                    if ($this->SC_creation->sccontact[$overwrite] == "on") {
                        $UpCarray = array();
                        if ($this->SC_creation->sccontact['aotype'][$k][$client] != "select")
                            $UpCarray['access_deliveries'] = $this->SC_creation->sccontact['aotype'][$k][$client];
                        else
                            $UpCarray['access_deliveries'] = implode(",", $this->SC_creation->sccontact['ao'][$k][$client]);

                        $UpCarray['client'] = $client;//print_r($UpCarray);exit;
                        $UpCarray['bo_user'] = $Carray['bo_user'];//print_r($UpCarray);exit;
                        $contact_obj->UpdateScContacts($UpCarray);
                    }

                    $Carray['access_client_list'] = $this->SC_creation->sccontact['clients'][$k][$l];

                    if ($this->SC_creation->sccontact['aotype'][$k][$client] != "select")
                        $Carray['access_deliveries'] = $this->SC_creation->sccontact['aotype'][$k][$client];
                    else
                        $Carray['access_deliveries'] = implode(",", $this->SC_creation->sccontact['ao'][$k][$client]);
                    $contact_obj->InsertScContacts($Carray);

                }
            }
        }
        //print_r($this->SC_creation->scpermissions);
        //print_r($this->SC_creation->sccontact);//exit;

        unset($this->SC_creation->scpermissions);
        unset($this->SC_creation->sccontact);
        //exit;
        $this->render('superclientcreate-step3');
    }


    public function updatebouserblockAction()
    {
        $Client_obj = new Ep_User_Client();
        $bouserDetails = $Client_obj->getBOuserdetails($_REQUEST['bouser']);
        echo $_REQUEST['bouser'] . '#' . $bouserDetails[0]['email'] . '#' . $bouserDetails[0]['password'] . '#' . $bouserDetails[0]['first_name'] . '#' . $bouserDetails[0]['last_name'];
        //echo '<a href="javascript:void(0);" onclick="connect_chiefOdigeo_FO(\''.$_REQUEST['bouser'].'\',\''.$bouserDetails[0]['email'].'\',\''.$bouserDetails[0]['password'].'\');"><i class="splashy-contact_grey"></i></a>';
    }

    public function checkbouserpermissionAction()
    {
        $sccontact_obj = new Ep_User_ScBoUserPermissions();
        $getpermission = $sccontact_obj->getScBoUserPermissionsbyClient($_POST['bo_user'], $_POST['clientid']);
        if ($getpermission[0]['access_deliveries'] != "all" && $getpermission[0]['access_deliveries'] != "future")
            $deliveryperm = "select";
        else
            $deliveryperm = $getpermission[0]['access_deliveries'];

        echo count($getpermission) . "#" . $deliveryperm;
    }

    //upload superclient logo
    public function uploadSCLogo($files, $client_id = NULL)
    {

        if ($client_id)
            $uploaddir = '/home/sites/site5/web/FO/profiles/superclients/logos/' . $client_id . '/';
        else {
            $uploaddir = '/home/sites/site5/web/FO/profiles/superclients/logos/temp/';

            if ($this->SC_creation->scpermissions['logo_id']) {
                $logo_id = $this->SC_creation->scpermissions['logo_id'];
                $file = $uploaddir . $logo_id . ".png";
                $file_global1 = $uploaddir . $logo_id . "_global.png";
                $file_global2 = $uploaddir . $logo_id . "_global1.png";

                if (file_exists($file)) unlink($file);
                if (file_exists($file_global1)) unlink($file_global1);
                if (file_exists($file_global2)) unlink($file_global2);
            }

            $client_id = mt_rand(1, 99999);
            $this->SC_creation->scpermissions['logo_id'] = $client_id;
        }

        if (!is_dir($uploaddir)) {
            mkdir($uploaddir, 0777);
            chmod($uploaddir, 0777);
        }
        $file = $uploaddir . $client_id . ".png";
        $file_global1 = $uploaddir . $client_id . "_global.png";
        $file_global2 = $uploaddir . $client_id . "_global1.png";
        list($width, $height) = getimagesize($files['tmp_name']);

        if ($width >= 90 || $height >= 90) {
            if (move_uploaded_file($files['tmp_name'], $file)) {

                chmod($file, 0777);

                $newimage_crop = new EP_User_Image();
                $newimage_crop->load($file);
                list($width, $height) = getimagesize($file);
                if ($width > $height)
                    $newimage_crop->resizeToWidth(73);
                elseif ($height > $width)
                    $newimage_crop->resizeToHeight(73);
                else
                    $newimage_crop->resize(73, 73);

                $newimage_crop->save($file_global1);
                chmod($file_global1, 0777);

                //90
                $newimage_crop1 = new EP_User_Image();
                $newimage_crop1->load($file);
                list($width, $height) = getimagesize($file);
                if ($width > $height)
                    $newimage_crop1->resizeToWidth(90);
                elseif ($height > $width)
                    $newimage_crop1->resizeToHeight(90);
                else
                    $newimage_crop1->resize(90, 90);

                $newimage_crop1->save($file_global2);
                chmod($file_global2, 0777);
            }
        }
    }

    //Download liberte invoice zip
    public function downloadpremiuminvoiceAction()
    {
        $pay_obj = new Ep_Payment_PaymentArticle();
        $paymentlist = $pay_obj->getPaymentPremium($_GET['client'], $_GET['month'], $_GET['year']);
        //print_r($paymentlist);

        if (count($paymentlist) > 0) {
            //Invoice zip
            $inv_array = array();
            for ($z = 0; $z < count($paymentlist); $z++) {
                $invoicepath = '/home/sites/site5/web/FO/invoice/client/' . $_GET['client'] . '/' . $paymentlist[$z]['delivery'] . '.pdf';

                if (!file_exists($invoicepath))
                    $this->generateInvoice($paymentlist[$z]['delivery']);

                $inv_array[] = $invoicepath;

            }
            //print_r($files_array);exit;
            $paymentlist[0]['company_name'] = str_replace(" ", "_", $paymentlist[0]['company_name']);
            $zipname = $paymentlist[0]['company_name'] . '_' . date("M", mktime(0, 0, 0, $_GET['month'], 10)) . '_' . $_GET['year'] . '_invoice';
            $invoicefilename = '/home/sites/site5/web/FO/invoice/zip/' . $zipname . '.zip';

            $invoicezip = $this->create_zip($inv_array, $invoicefilename);
            /*
            //Main zip
            $files_array=array();
            //generate xls
            $this->generateInvoiceXLS($_GET['client'],$_GET['month'],$_GET['year']);
            $files_array[]='/home/sites/site5/web/FO/invoice/client/xls/'.$paymentlist[0]['company_name'].'_'.date("M", mktime(0, 0, 0, $_GET['month'], 10)).'_'.$_GET['year'].'_XLS.xls';
            $files_array[]=	$invoicefilename;

            $filename='/home/sites/site5/web/FO/invoice/zip/'.$paymentlist[0]['company_name'].'_'.date("M", mktime(0, 0, 0, $_GET['month'], 10)).'_'.$_GET['year'].'.zip';
            $mainzip = $this->create_zip($files_array,$filename);
            */


            header('Content-Type: application/zip;charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $zipname . '.zip');
            readfile($invoicefilename);
        } else
            echo "NO invoices for this month and year";
    }

    public function generateInvoiceXLS($client, $month, $year)
    {
        $pay_obj = new Ep_Payment_PaymentArticle();
        $paymentartlist = $pay_obj->getPaymentLiberte($client, $month, $year);

        $xlsFile = '/home/sites/site5/web/FO/invoice/client/xls/' . $paymentartlist[0]['company_name'] . '_' . date("M", mktime(0, 0, 0, $month, 10)) . '_' . $year . '_XLS.xls';
        $fh = fopen($xlsFile, 'w') or die("can't open file");


        $stringData .= '<table style="border: 3px solid;" width="75%">
							<tr>
								<td style="color:#0066FF;font-size:32px;font-weight:bold;" width="50%">EDIT-PLACE.COM</td>
								<td style="font-size:32px;font-weight:bold;text-align:right" width="50%">FACTURE</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									16 Rue Jesse Owens<br>
									93200 SAINT DENIS<br>
									RCS Bobigny B 521 287 193<br>
									TVA INTRA COMMUNAUTAIRE : FR43-521287193.<br>
								</td>
								<td>
									<b>DATE :</b> ' . date("F d,Y") . '<br>
									<b>N&deg; FACTURE :</b>' . date("m/Y") . '
									<br><br>
									<b>' . $paymentartlist[0]['company_name'] . '</b><br>
									' . $paymentartlist[0]['address'] . ' <br>
									' . $paymentartlist[0]['zipcode'] . ' ' . $paymentartlist[0]['city'] . '
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td colspan="3" width="100%">
								<table border="1" width="100%">
									<tr style="background:rgb(212, 227, 235);">
										<th>DESCRIPTION</th>
										<th>NBRE d\'articles</th>
										<th>Prix  H.T.(&euro;)</th>
										<th>Montant(&euro;)</th>
									</tr>
									<tr>
										<td style="background:rgb(204, 204, 204)">R&eacute;daction d\'articles - ' . $paymentartlist[0]['company_name'] . '</td>
										<td></td>
										<td></td>
										<td></td>
									</tr>';
        $total = 0;
        $totalpay = 0;
        for ($p = 0; $p < count($paymentartlist); $p++) {
            $stringData .= '<tr>
											<td nowrap>' . utf8_encode($paymentartlist[$p]['title']) . '</td>
											<td>1</td>
											<td>' . number_format($paymentartlist[$p]['amount'], 2, '.', '') . '</td>
											<td style="background:#FFCC99">' . number_format($paymentartlist[$p]['amount'], 2, '.', '') . ' &euro;</td>
										</tr>';
            $total += $paymentartlist[$p]['amount'];
            $totalpay += $paymentartlist[$p]['amount_paid'];
        }

        $taxamount = 0.2 * $total;
        $stringData .= '</table>
									</td></tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
										<td></td>
										<td align="right">
											<table width="80%">
												<tr>
													<td>SOUS-TOTAL H.T.</td>
													<td style="border:1px solid;">' . number_format($total, 2, '.', '') . ' &euro;</td>
												</tr>
												<tr>
													<td>TAUX DE T.V.A.</td>
													<td style="border:1px solid;">20%</td>
												</tr>
												<tr>
													<td>T.V.A.</td>
													<td style="background:#FFCC99;border:1px solid;">' . number_format($taxamount, 2, '.', '') . ' &euro;</td>
												</tr>
												<tr>
													<td>Acompte </td>
													<td></td>
												</tr>
												<tr>
													<td><b>TOTAL TTC</b></td>
													<td style="background:#FFFF99;border:1px solid;"><b>' . number_format($totalpay, 2, '.', '') . ' &euro;</b></td>
												</tr>
											</table>
										</td>
									</tr><tr><td>&nbsp;</td></tr>';

        $stringData .= '<tr>
										<td colspan="2">
											TVA pay&eacute;e sur les d&eacute;bits<br>
											Aucun escompte accord&eacute; en cas de paiement anticip&eacute;.<br>
											<span style="color:red;font-weight:bold;">Date d\'&eacute;ch&eacute;ance 30 jours</span> - Tout retard de paiement &agrave; &eacute;ch&eacute;ance  entra&icirc;ne le paiement d\'int&eacute;r&ecirc;ts moratoires d\'un taux &eacute;gal &agrave; trois fois<br>
											le taux d\'int&eacute;r&ecirc;t l&eacute;gal en vigueur, ainsi que le paiement de frais forfaitaires de mise en recouvrement d\'un montant de 40 Euros<br>
											BNP PARIBAS FACTOR devra &ecirc;tre avis&eacute; de toute demande de rensiegnements ou r&eacute;clamations.
										</td>
									</tr><tr><td>&nbsp;</td></tr>';

        $stringData .= '<tr align="center">
										<td colspan="2">
											<div style="padding-left:300px;">
											<table style="border:1px solid;">
												<tr>
													<td>
													<span style="font-weight:bold">Coordonn&eacute;es bancaires pour le paiement:</span><br>
													BNP PARIBAS FACTOR : 51 boulevard des Dames - 13345 Marseille<br>
													Cedex 20<br>
													RIB: 18020 00001 14851000000 66<br>
													RIB: 18020 00001 14851000000 66
													</td>
												</tr>
											</table>
											</div>
										</td>
									</tr><tr><td>&nbsp;</td></tr>';

        $stringData .= '<tr>
										<td colspan="2">
										Pour &ecirc;tre lib&eacute;ratoire, le r&egrave;glement de cette facture doit &ecirc;tre effectu&eacute; directement &agrave; l\'ordre de BNP PARIBAS FACTOR <br>
										(coordonn&eacute;es ci-dessus) qui le re&ccedil;oit par subrogation dans le cadre d\'un contrat d\'affacturage
										</td>
									</tr><tr><td>&nbsp;</td></tr>';
        $stringData .= '<tr>
										<td colspan="2" style="text-align:center;color:#0066FF;">
										EDIT-PLACE.COM - 16 rue Jesse Owens - 93200 St DENIS LA PLAINE - T&eacute;l. 33 (0) 1 77 68 64 61 - contact@edit-place.com<br>
										S.A.R.L AU CAPITAL DE 20.140 &euro; - RCS BOBIGNY 521 287 193 - N&deg;INTRACOMMUNAUTAIRE : FR 43521287193
										</td>
									</tr><tr><td>&nbsp;</td></tr>';

        $stringData .= '</table>';
        //echo $stringData;exit;
        fwrite($fh, $stringData);
        fclose($fh);
    }

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

    public function generateInvoice($article)
    {
        $invoiceid = $article;
        ob_start();
        $payment_obj = new Ep_Payment_PaymentArticle();
        $country_array = $this->_arrayDb->loadArrayv2("countryList", $this->_lang);

        //Payment details
        $payment = $payment_obj->getpaymentdetails($invoiceid);
        $invoicedir = '/home/sites/site5/web/FO/invoice/client/' . $payment[0]['user_id'] . '/';

        //Dates
        setlocale(LC_TIME, 'fr_FR');
        $date_invoice_full = strftime("%e %B %Y", strtotime($payment[0]['delivery_date']));
        $date_invocie = date("d/m/Y", strtotime($payment[0]['delivery_date']));
        $date_invoice_ep = date("Y/m", strtotime($payment[0]['delivery_date']));

        //Address
        $profileinfo = $payment_obj->getClientdetails($payment[0]['user_id']);
        $address = $profileinfo[0]['company_name'] . '<br>';
        $address .= $profileinfo[0]['address'] . '<br>';
        $address .= $profileinfo[0]['zipcode'] . '  ' . $profileinfo[0]['city'] . '  ' . $country_array[$profileinfo[0]['country']] . '<br>';

        //Invoice details
        $invoice_details_pdf = '
				<div align="center" style="font-size:16pt;"><b>Appel d\'offres : ' . $payment[0]['title'] . '</b></div>
					<table class="change_order_items">
									<tbody>
										<tr>
											<th>DESIGNATION</th>
											<th>MONTANT</th>
											<th>MONTANT PAY&Eacute;</th>
										</tr>';

        $total = 0;
        $total = number_format($payment[0]['amount'], 2);

        $invoice_details_pdf .= '<tr>
											<td>' . $payment[0]['title'] . '</td>
											<td class="change_order_total_col">' . number_format($total, 2, ',', '') . '</td>
											<td class="change_order_total_col">' . number_format($total, 2, ',', '') . '</td>
											</tr>';

        $invoice_details_pdf .= '<tr>
											<td style="border-top:1pt solid black;text-align:right;margin-right:10px;font-size: 12pt;" colspan="2">
												Total de la prestation HT
											</td>
											<td style="border-top:1pt solid black;font-size: 12pt;" class="change_order_total_col" >
												' . number_format($total, 2, ',', '') . '
											</td>
										</tr>
									</tbody>
								</table>';

        //Pay info number
        $payinfo_number = "";

        if ($payment[0]['amount'] != "" && $payment[0]['client_type'] != "personal") {
            //Tax details
            $tax = (($total * $payment[0]['tax']) / 100);
            $tax_details_pdf = '<table class="change_order_items">
												<tbody>
													<tr>
														<td>TVA</td>
														<td>taux : ' . str_replace('.', ',', $payment[0]['tax']) . '%</td>
														<td class="change_order_total_col" style="border-top:1pt solid black;text-align:right;font-size: 12pt;">' . number_format($tax, 2, ',', '') . ' &#x80; </td>
													</tr>
												</tbody>
												</table>';
        } else
            $tax = 0;

        /**Final Total**/
        $final_invoice_amount = '<table class="change_order_items" width="100%">
										<tr>
											<td  style="width:82%;font-size:12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Montant TTC</td>
											<td style="width:18%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >' . number_format(($total + $tax), 2, ',', '') . ' &#x80;</td>
										</tr>
									</table>';
        if (!is_dir($invoicedir)) {
            mkdir($invoicedir, 0777);
            chmod($invoicedir, 0777);
        }
        include('/home/sites/site5/web/FO/dompdf/dompdf_config.inc.php');
        $html = file_get_contents('/home/sites/site5/web/FO/views/scripts/Client/Client_invoice_pdf.phtml');
        $html = str_replace('$$$$invoice_details_pdf$$$$', $invoice_details_pdf, $html);
        $html = str_replace('$$$$tax_details_pdf$$$$', $tax_details_pdf, $html);
        $html = str_replace('$$$$final_invoice_amount$$$$', $final_invoice_amount, $html);
        $html = str_replace('$$$$date_invoice_full$$$$', $date_invoice_full, $html);
        $html = str_replace('$$$$date_invoice$$$$', $date_invocie, $html);
        $html = str_replace('$$$$address$$$$', $address, $html);
        $html = str_replace('$$$$payinfo_number$$$$', $payinfo_number, $html);
        $html = str_replace('$$$$date_invoice_ep$$$$', $date_invoice_ep, $html);
        $html = str_replace('$$$$invoice_identifier$$$$', $payment[0]['payid'], $html);

        if (get_magic_quotes_gpc())
            $html = stripslashes($html);

        //echo  $html;exit;

        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->set_paper("a4");
        $dompdf->render();
        error_reporting(0);

        $pdf = $dompdf->output();

        file_put_contents($invoicedir . '/' . $invoiceid . '.pdf', $pdf);
        ob_clean();
        //flush();
        //exit;

    }

    public function generateInvoicePremium($delivery)
    {
        $invoiceid = $delivery;
        ob_start();
        $payment_obj = new Ep_Payment_PaymentArticle();
        $country_array = $this->_arrayDb->loadArrayv2("countryList", $this->_lang);

        //Payment details
        $payment = $payment_obj->getpaymentdetailsDelivery($invoiceid);
        $invoicedir = '/home/sites/site5/web/FO/invoice/client/' . $payment[0]['user_id'] . '/';

        //Dates
        setlocale(LC_TIME, 'fr_FR');
        $date_invoice_full = strftime("%e %B %Y", strtotime($payment[0]['ddate']));
        $date_invocie = date("d/m/Y", strtotime($payment[0]['ddate']));
        $date_invoice_ep = date("Y/m", strtotime($payment[0]['ddate']));

        //Address
        $profileinfo = $payment_obj->getClientdetails($payment[0]['user_id']);
        $address = $profileinfo[0]['company_name'] . '<br>';
        $address .= $profileinfo[0]['address'] . '<br>';
        $address .= $profileinfo[0]['zipcode'] . '  ' . $profileinfo[0]['city'] . '  ' . $country_array[$profileinfo[0]['country']] . '<br>';

        //Invoice details
        $invoice_details_pdf = '
				<div align="center" style="font-size:16pt;"><b>Appel d\'offres : ' . $payment[0]['dtitle'] . '</b></div>
					<table class="change_order_items">
									<tbody>
										<tr>
											<th>DESIGNATION</th>
											<th>MONTANT</th>
											<th>MONTANT PAY&Eacute;</th>
										</tr>';

        $total = 0;
        $total = $payment[0]['amount'];

        $invoice_details_pdf .= '<tr>
											<td>' . $payment[0]['dtitle'] . '</td>
											<td class="change_order_total_col">' . number_format($total, 2, ',', '') . '</td>
											<td class="change_order_total_col">' . number_format($total, 2, ',', '') . '</td>
											</tr>';

        $invoice_details_pdf .= '<tr>
											<td style="border-top:1pt solid black;text-align:right;margin-right:10px;font-size: 12pt;" colspan="2">
												Total de la prestation HT
											</td>
											<td style="border-top:1pt solid black;font-size: 12pt;" class="change_order_total_col" >
												' . number_format($total, 2, ',', '') . '
											</td>
										</tr>
									</tbody>
								</table>';

        //Pay info number
        $payinfo_number = "";

        if ($payment[0]['amount'] != "" && $payment[0]['client_type'] != "personal") {
            //Tax details
            $tax = (($total * $payment[0]['tax']) / 100);
            $tax_details_pdf = '<table class="change_order_items">
												<tbody>
													<tr>
														<td>TVA</td>
														<td>taux : ' . str_replace('.', ',', $payment[0]['tax']) . '%</td>
														<td class="change_order_total_col" style="border-top:1pt solid black;text-align:right;font-size: 12pt;">' . number_format($tax, 2, ',', '') . ' &#x80; </td>
													</tr>
												</tbody>
												</table>';
        } else
            $tax = 0;

        /**Final Total**/
        $final_invoice_amount = '<table class="change_order_items" width="100%">
										<tr>
											<td  style="width:82%;font-size:12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Montant TTC</td>
											<td style="width:18%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >' . number_format(($total + $tax), 2, ',', '') . ' &#x80;</td>
										</tr>
									</table>';
        if (!is_dir($invoicedir)) {
            mkdir($invoicedir, 0777);
            chmod($invoicedir, 0777);
        }
        include('/home/sites/site5/web/FO/dompdf/dompdf_config.inc.php');
        $html = file_get_contents('/home/sites/site5/web/FO/views/scripts/Client/Client_invoice_pdf.phtml');
        $html = str_replace('$$$$invoice_details_pdf$$$$', $invoice_details_pdf, $html);
        $html = str_replace('$$$$tax_details_pdf$$$$', $tax_details_pdf, $html);
        $html = str_replace('$$$$final_invoice_amount$$$$', $final_invoice_amount, $html);
        $html = str_replace('$$$$date_invoice_full$$$$', $date_invoice_full, $html);
        $html = str_replace('$$$$date_invoice$$$$', $date_invocie, $html);
        $html = str_replace('$$$$address$$$$', $address, $html);
        $html = str_replace('$$$$payinfo_number$$$$', $payinfo_number, $html);
        $html = str_replace('$$$$date_invoice_ep$$$$', $date_invoice_ep, $html);
        $html = str_replace('$$$$invoice_identifier$$$$', $payment[0]['payid'], $html);

        if (get_magic_quotes_gpc())
            $html = stripslashes($html);

        //echo  $html;exit;

        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->set_paper("a4");
        $dompdf->render();
        error_reporting(0);

        $pdf = $dompdf->output();

        file_put_contents($invoicedir . '/' . $invoiceid . '.pdf', $pdf);
        ob_clean();
        //flush();
        //exit;

    }

    public function downloadinvoiceAction()
    {
        $invoiceid = $_REQUEST['delivery'];
        $invoicedir = '/home/sites/site5/web/FO/invoice/client/' . $_REQUEST['client'] . '/';

        if (!file_exists($invoicedir . '/' . $invoiceid . '.pdf')) {
            $pay_obj = new Ep_Payment_PaymentArticle();
            $country_array = $this->_arrayDb->loadArrayv2("countryList", $this->_lang);

            //Payment details
            $payment = $pay_obj->getpaymentdetailsDelivery($invoiceid);

            //Dates
            setlocale(LC_TIME, 'fr_FR');
            $date_invoice_full = strftime("%e %B %Y", strtotime($payment[0]['ddate']));
            $date_invocie = date("d/m/Y", strtotime($payment[0]['ddate']));
            $date_invoice_ep = date("Y/m", strtotime($payment[0]['ddate']));


            //Address
            $profileinfo = $pay_obj->getClientdetails($_REQUEST['client']);
            $address = $profileinfo[0]['company_name'] . '<br>';
            $address .= $profileinfo[0]['address'] . '<br>';
            $address .= $profileinfo[0]['zipcode'] . '  ' . $profileinfo[0]['city'] . '  ' . $country_array[$profileinfo[0]['country']] . '<br>';

            //Invoice details
            $invoice_details_pdf = '
				<div align="center" style="font-size:16pt;"><b>Appel d\'offres : ' . $payment[0]['dtitle'] . '</b></div>
					<table class="change_order_items">
                                    <tbody>
                                        <tr>
                                            <th>DESIGNATION</th>
                                            <th>MONTANT</th>
											<th>MONTANT PAY&Eacute;</th>
                                        </tr>';

            $total = 0;
            if ($payment[0]['amount'] != "")
                $total = $payment[0]['amount'];
            else
                $total = $payment[0]['amount_paid'];


            $invoice_details_pdf .= '<tr>
                                            <td>' . $payment[0]['dtitle'] . '</td>
                                            <td class="change_order_total_col">' . number_format($total, 2, ',', '') . '</td>
											<td class="change_order_total_col">' . number_format($total, 2, ',', '') . '</td>
                                            </tr>';


            $invoice_details_pdf .= '<tr>
											<td style="border-top:1pt solid black;text-align:right;margin-right:10px;font-size: 12pt;" colspan="2">
												Total de la prestation HT
											</td>
											<td style="border-top:1pt solid black;font-size: 12pt;" class="change_order_total_col" >
												' . number_format($total, 2, ',', '') . '
											</td>
                                        </tr>
                                    </tbody>
                                </table>';

            //Pay info number
            $payinfo_number = "";

            if ($payment[0]['amount'] != "" && $payment[0]['client_type'] != "personal") {
                //Tax details
                $tax = (($total * $payment[0]['tax']) / 100);
                $tax_details_pdf = '<table class="change_order_items">
												<tbody>
													<tr>
														<td>TVA</td>
														<td>taux : ' . str_replace('.', ',', $payment[0]['tax']) . '%</td>
														<td class="change_order_total_col" style="border-top:1pt solid black;text-align:right;font-size: 12pt;">' . number_format($tax, 2, ',', '') . ' &#x80; </td>
													</tr>
												</tbody>
												</table>';
            } else
                $tax = 0;

            /**Final Total**/
            $final_invoice_amount = '<table class="change_order_items" width="100%">
										<tr>
											<td  style="width:82%;font-size:12pt;font-weight:bold;background-color:#BDBDBD;border-top:1pt solid black;border-right:1pt solid black;text-align:right;padding:0.5em 1.5em 0.5em 0.5em;">Montant TTC</td>
											<td style="width:18%;font-weight:bold;border-top:1pt solid black;padding:0.5em;padding-right:10pt;font-size: 12pt;text-align: right" >' . number_format(($total + $tax), 2, ',', '') . ' &#x80;</td>
										</tr>
									</table>';
            if (!is_dir($invoicedir)) {
                mkdir($invoicedir, 0777);
                chmod($invoicedir, 0777);
            }

            require_once('/home/sites/site5/web/FO/dompdf/dompdf_config.inc.php');
            $html = file_get_contents('/home/sites/site5/web/FO/views/scripts/Client/Client_invoice_pdf.phtml');
            $html = str_replace('$$$$invoice_details_pdf$$$$', $invoice_details_pdf, $html);
            $html = str_replace('$$$$tax_details_pdf$$$$', $tax_details_pdf, $html);
            $html = str_replace('$$$$final_invoice_amount$$$$', $final_invoice_amount, $html);
            $html = str_replace('$$$$date_invoice_full$$$$', $date_invoice_full, $html);
            $html = str_replace('$$$$date_invoice$$$$', $date_invocie, $html);
            $html = str_replace('$$$$address$$$$', $address, $html);
            $html = str_replace('$$$$payinfo_number$$$$', $payinfo_number, $html);
            $html = str_replace('$$$$date_invoice_ep$$$$', $date_invoice_ep, $html);
            $html = str_replace('$$$$invoice_identifier$$$$', $payment[0]['payid'], $html);

            if (get_magic_quotes_gpc())
                $html = stripslashes($html);

            //echo  $html;exit;
            //$old_limit = ini_set("memory_limit", "16M");

            $dompdf = new DOMPDF();
            $dompdf->load_html($html);
            $dompdf->set_paper("a4");
            $dompdf->render();


            $pdf = $dompdf->output();

            file_put_contents($invoicedir . '/' . $invoiceid . '.pdf', $pdf);
        }

        header('Content-type: application/pdf');
        header('Content-disposition: attachment;filename=' . $invoiceid . '.pdf');
        ob_clean();
        flush();
        readfile($invoicedir . '/' . $invoiceid . '.pdf');
    }

    public function zero_cut($str, $digits = 0)
    {
        $value = sprintf("%.${digits}f", $str);
        $value = number_format($str, 2, ',', '');

        if (0 == $digits)
            return $value;

        list($left, $right) = explode(",", $value);

        $len = strlen($right);
        $k = 0;
        for ($i = $len - 1; $i >= 0; $i--) {
            if ('0' == $right{$i})
                $k++;
            else
                break;
        }
        $right = substr($right, 0, $len - $k);

        if ("" != $right)
            $right = ",$right";

        return $left . $right;
    }

    //// getting the moyennes detials ////////
    public function bousermoyennesAction()
    {
        $stats_params = $this->_request->getParams();
        $user_obj = new EP_User_User();

        $params['userid'] = $stats_params['userid'];
        $params['statstype'] = 'moyennes';
        if ($stats_params['bousermoy_user'] != '0')
            $params['usertype'] = $stats_params['bousermoy_user'];
        if ($stats_params['bousermoy_lang'] != '0')
            $params['lang'] = $stats_params['bousermoy_lang'];
        if ($stats_params['bousermoy_categ'] != '0')
            $params['categ'] = $stats_params['bousermoy_categ'];
        $marksdetails = $user_obj->getBoUserStats($params);
        $marks = 0;
        if ($marksdetails != 'NO') {
            foreach ($marksdetails as $key => $value) {
                $marks += $marksdetails[$key]['marks'];
            }
        }
        if ($marks != '0')
            $marks = $marks / 10;
        else
            $marks = 0;
        echo $marks . "/10";
        exit;
    }

    //// getting the moyennes detials ////////
    public function moyennesAction()
    {
        $stats_params = $this->_request->getParams();
        $user_obj = new EP_User_Contributor();

        $params['userid'] = $stats_params['userid'];
        $params['statstype'] = 'moyennes';
        if ($stats_params['moy_user'] != '0')
            $params['usertype'] = $stats_params['moy_user'];
        if ($stats_params['moy_lang'] != '0')
            $params['lang'] = $stats_params['moy_lang'];
        if ($stats_params['moy_categ'] != '0')
            $params['categ'] = $stats_params['moy_categ'];
        $marksdetails = $user_obj->getContributorStats($params);
        $marks = 0;
        if ($marksdetails != 'NO') {
            foreach ($marksdetails as $key => $value) {
                $marks += $marksdetails[$key]['marks'];
            }
        }
        if ($marks != '0')
            $marks = $marks / 10;
        else
            $marks = 0;
        echo $marks . "/10";
        exit;
    }

    //// getting the moyennes detials ////////
    public function bousergraphAction()
    {
        $stats_params = $this->_request->getParams();
        $participate_obj = new EP_Participation_Participation();
        /////////////graph part ///////////
        $params['userid'] = $stats_params['userid'];
        if ($stats_params['stats_lang'] != '0')
            $params['langs'] = $stats_params['stats_lang'];
        if ($stats_params['stats_categ'] != '0')
            $params['categs'] = $stats_params['stats_categ'];
        if ($stats_params['stats_date'] != '0')
            $params['date'] = $stats_params['stats_date'];
        if ($stats_params['type'] == 'bouser') {
            $marksforarts = $participate_obj->getMarksBoUserGraph($params);
            $dates = $this->formdates($marksforarts);
            $this->_view->dates = $dates;
            $this->render('user_graphs');
        }

        elseif($stats_params['type'] == 'writer' || $stats_params['type'] == 'corrector'){
            $this->_view->type = $stats_params['type'];
            //fecth writer's data//
            $marksforarts = $participate_obj->getMarksWriterGraph($params, 'writer'); //print_r($marksforarts); exit;
            $this->_view->dates = $this->formdates($marksforarts);
            //fecth corrector's data//
            $marksforarts = $participate_obj->getMarksWriterGraph($params, 'corrector'); //print_r($marksforarts); exit;
            $this->_view->crtdates = $this->formdates($marksforarts);
            $this->render('user_graphs');
        }
        /////////end of graph /////////////////////////
    }
    public function formdates($marksforarts){
        if ($marksforarts != 'NO') {
            $dates = "var d2 = [
             ";
            if ($marksforarts)
                foreach ($marksforarts[0] as $key => $val) {
                    //echo "<pre>".$key = substr($key, 0, -3);
                    $key = substr($key, 0, -3);
                    if ($val == '')
                        $val = 0;
                    $dates .= "[new Date('" . $key . "').getTime()," . $val . "],
                 ";
                }
            $dates .= "];
             ";  //exit;
            //  echo $this->_view->dates; exit;
        } else {
            $dates = "var d2 = [ " . date('Y-m') . " ];";
        }
        return $dates;
    }

    //// getting the corrector moyennes detials ////////
    public function correctormoyennesAction()
    {
        $stats_params = $this->_request->getParams();
        $user_obj = new EP_User_Contributor();

        $params['userid'] = $stats_params['userid'];
        $params['statstype'] = 'moyennes';
        if ($stats_params['crtmoy_user'] != '0')
            $params['usertype'] = $stats_params['crtmoy_user'];
        if ($stats_params['crtmoy_lang'] != '0')
            $params['lang'] = $stats_params['crtmoy_lang'];
        if ($stats_params['crtmoy_categ'] != '0')
            $params['categ'] = $stats_params['crtmoy_categ'];
        $marksdetails = $user_obj->getCorrectorStats($params);
        $marks = 0;
        if ($marksdetails != 'NO') {
            foreach ($marksdetails as $key => $value) {
                $marks .= $marksdetails[$key]['marks'];
            }
        }
        if ($marks != '0')
            $marks = $marks / 10;
        else
            $marks = 0;
        echo $marks . "/10";
        exit;
    }

    //// getting the Client Invoiced Action////////
    public function clientInvoicedOnAction()
    {
        $user_obj = new EP_User_User();
        $this->_view->clientList = $user_obj->getclientList();
        //print_r($clients);
        $this->render('user_invoiced_on');
    }

    //// getting the Client Invoiced Change////////
    public function changeInvoicedOnAction()
    {
        //print_r($_REQUEST);
        $user_obj = new EP_User_User();
        $invoiced = ($_REQUEST['invoiced'] == 'no') ? 'yes' : 'no';
        $data = array("invoiced" => $invoiced);////////updating
        $query = "identifier= '" . $_REQUEST['user_id'] . "'";
        if ($user_obj->updateUser($data, $query)) {
            $data['success'] = 'success';
        } else {
            $data['error'] = 'bigerror';
        }
        echo json_encode($data);
    }

    ////get the bo user statistics////////////
    public function bouserstatisticsAction()
    {
        $stats_params = $this->_request->getParams();
        $user_obj = new EP_User_Contributor();
        $article_obj = new EP_Delivery_Article();
        $params['userid'] = $stats_params["userid"];
        $params['langs'] = $stats_params["stats_lang"];
        $params['categs'] = $stats_params["stats_categ"];

        $participate_obj = new EP_Participation_Participation();
        $noarts = $participate_obj->getBoUserParticipantsStatistics($params);
        $clientnamesarray = $article_obj->getBoUserClientLIst($params);
        foreach ($clientnamesarray AS $key => $value) {
            $clientnames[$key] = $clientnamesarray[$key]['company_name'];
        }
        $marks = 0;
        if ($noarts != 'NO') {
            $result[0] = $noarts[0]['refusedCount'];
            $result[1] = $noarts[1]['totalCount'];
            $result[2] = $noarts[2]['publishedCount'];
            if ($noarts[3]['refusalreasonsCount'] != 0)
                $result[3] = count(explode(",", $noarts[3]['refusalreasonsCount']));
            else
                $result[3] = 0;
            $result[4] = $noarts[4]['totaltemplateCount'];
            if ($noarts[5]['refusalreasonscrtCount'] != 0)
                $result[5] = count(explode(",", $noarts[5]['refusalreasonscrtCount']));
            else
                $result[5] = 0;
            $result[6] = count($clientnamesarray);
            $result[7] = implode(",", $clientnames);
        }
        echo json_encode($result);
        exit;
    }

    ////get the bo user mission level statistics////////////
    public function bousermissionstatisticsAction()
    {
        $stats_params = $this->_request->getParams();
        $article_obj = new EP_Delivery_Article();
        $user_obj = new Ep_User_User();
        $params['userid'] = $stats_params["userid"];
        $params['langs'] = $stats_params["stats_lang"];
        $params['categs'] = $stats_params["stats_categ"];

        $participate_obj = new EP_Participation_Participation();
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $missionsarts = $article_obj->getBoUserMissionsStatistics($params);
        if ($missionsarts != '') {
            $result[0] = $missionsarts[0]['publicCount'];
            $result[1] = $missionsarts[1]['privateCount'];
            $result[2] = $missionsarts[2]['publicBoCount'];
            $result[3] = $missionsarts[3]['privateBoCount'];
            $result[4] = $missionsarts[4]['publicFoPubCount'];
            $result[5] = $missionsarts[5]['publicFoPrvCount'];
            $result[6] = $missionsarts[6]['privateFoPubCount'];
            $result[7] = $missionsarts[7]['privateFoPrvCount'];
            $result[8] = $missionsarts[8]['frequentLanguage'];
            $result[9] = utf8_encode($this->language_array[$missionsarts[8]['language']]);
            $result[10] = $missionsarts[9]['frequentCategory'];
            $result[11] = utf8_encode($this->category_array[$missionsarts[9]['category']]);
            $result[12] = $missionsarts[10]['totalArtCount'];
            $result[21] = $missionsarts[11]['timespend'];
            $view_to = $article_obj->boUserViewto($params);// print_r($view_to); exit;
            $viewto = array();
            $viewtosc = 0;
            $viewtojc = 0;
            $viewtojc0 = 0;
            $viewtoall = 0;
            if ($view_to != 'NO') {
                for ($j = 0; $j < count($view_to); $j++) {
                    $view_toarr[$j] = explode(",", $view_to[$j]['view_to']);
                    if (in_array('sc', $view_toarr[$j]))
                        $viewtosc++;
                    if (in_array('jc', $view_toarr[$j]))
                        $viewtojc++;
                    if (in_array('jc0', $view_toarr[$j]))
                        $viewtojc0++;
                    if (count($view_toarr[$j]) == 3)
                        $viewtoall++;
                }
            }
            $viewto['sc'] = $viewtosc;
            $viewto['jc'] = $viewtojc;
            $viewto['jc0'] = $viewtojc0;
            $viewto['all'] = $viewtoall;
            $result[13] = $viewto;
            $selview_to = $article_obj->selectedProfileType($params);
            $selviewtosc = 0;
            $selviewtojc = 0;
            $selviewtojc0 = 0;
            if ($selview_to != 'NO') {
                for ($i = 0; $i < count($selview_to); $i++) {
                    $profile = $user_obj->getAllUsersDetails($selview_to[$i]['user_id']);
                    if ($profile[0]['profile_type'] == "sub-junior")
                        $selviewtojc0++;
                    if ($profile[0]['profile_type'] == "junior")
                        $selviewtojc++;
                    if ($profile[0]['profile_type'] == "senior")
                        $selviewtosc++;
                }

            }
            $selviewto['sc'] = $selviewtosc;
            $selviewto['jc'] = $selviewtojc;
            $selviewto['jc0'] = $selviewtojc0;
            $result[14] = $selviewto;
            $topwriter = $participate_obj->topWriterParticipated($params);
            if ($topwriter != 'NO')
                $result[15] = $topwriter;
            $topcorrector = $crtparticipate_obj->topCorrrectorParticipated($params);
            if ($topcorrector != 'NO')
                $result[16] = $topcorrector;
            $clientlist = $participate_obj->clientListArtParts($params);
            if ($clientlist != 'NO') {
                foreach ($clientlist AS $key => $value) {
                    $clientlistnames[$key] = $clientlist[$key]['company_name'];
                }
                $result[17] = implode(",", $clientlistnames);
            } else
                $result[17] = 0;
            $result[18] = count($clientlist);
            $pmpartscount = $participate_obj->getParticipantOfProjectManager($params);
            if ($pmpartscount[0]['PMpartscount'] != '')
                $result[19] = $pmpartscount[0]['PMpartscount'];
            else
                $result[19] = 0;
            $pmcrtpartscount = $crtparticipate_obj->getCrtParticipantOfProjectManager($params);
            if ($pmpartscount[0]['PMcrtpartscount'] != '')
                $result[20] = $pmcrtpartscount[0]['PMcrtpartscount'];
            else
                $result[20] = 0;
        }
        echo json_encode($result);
        exit;
    }
    /*edited by naseer on 20-11-2015*/
    ////get the writer statistics////////////
    public function statisticsAction()
    {
        $stats_params = $this->_request->getParams();
        $user_obj = new EP_User_Contributor();
        $params['userid'] = $stats_params["userid"];
        $params['langs'] = $stats_params["stats_lang"];
        $params['categs'] = $stats_params["stats_categ"];
        $participate_obj = new EP_Participation_Participation();
        $noarts = $participate_obj->getParticipantsStatistics($params);
        $clientnamesarray = $participate_obj->getParticipantClientLIst($params);
        foreach ($clientnamesarray AS $key => $value) {
            $clientnames[$key] = $clientnamesarray[$key]['company_name'];
        }
        $marks = 0;

        if ($noarts != 'NO') {
            //commenting old code//
            /*$result[0] = $noarts[0]['selectedCount'];
            $result[1] = $noarts[1]['refusedCount'];
            $result[2] = $noarts[2]['inprocessCount'];
            $result[3] = $noarts[3]['totalCount'];
            $result[4] = $noarts[4]['publishedCount'];
            $result[5] = $noarts[5]['republishedCount'];
            $result[6] = $noarts[6]['notsubmittedCount'];
            // $result[6]              = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['tempreasons'] != '') {
                $tempreasons = $this->formRefusalReasons($noarts[7]['tempreasons'], 'frequent');
                $result[12] = $tempreasons[0];
                $result[7] = $tempreasons[1];
            } else {
                $result[12] = 'nill';
                $result[7] = 0;
            }
            $result[8] = $noarts[8]['totaltemplateCount'];
            $result[9] = count($clientnamesarray);
            $result[10] = implode(",", $clientnames);
            if ($noarts[11]['permreason'] != '') {
                $permreasons = $this->formRefusalReasons($noarts[11]['permreason'], 'frequent');
                $result[13] = $permreasons[0];
                $result[11] = $permreasons[1];
            } else {
                $result[13] = 'nill';
                $result[11] = 0;
            }*/
            $selected_arts = $noarts[0]['selectedCount'];
            $refused_arts = $noarts[1]['refusedCount'];
            $proccesing_arts = $noarts[2]['inprocessCount'];
            $total_parts = $noarts[3]['totalCount'];
            $published_arts = $noarts[4]['publishedCount'];
            $republished = $noarts[5]['republishedCount'];
            $arts_notsubmited = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['tempreasons'] != '') {
                $tempreasons = $this->formRefusalReasons($noarts[7]['tempreasons'], 'frequent');
                $freqtempreason = $tempreasons[0];
                $freqtempreasoncount = $tempreasons[1];
            } else {
                $freqtempreason = '-';
                $freqtempreasoncount = 0;
            }
            //$total_refusalreasons = $noarts[8]['totaltemplateCount'];  // total count of refusal reasons
            $total_temp_refusalreasons = ($noarts[7]['tempreasons'] != "") ? count( explode(",",$noarts[7]['tempreasons'])) : '0';//condition to fetch the count of the array
            $total_prem_refusalreasons = ($noarts[9]['permreason'] != "") ? count( explode(",",$noarts[9]['permreason']) ) : '0';//condition to fetch the count of the array
            if ($clientnamesarray != 'NO')
                $num_clients = count($clientnamesarray);
            else
                $num_clients = 0;
            $client_list = implode(",", $clientnames);
            if ($noarts[9]['permreason'] != '') {
                $permreasons = $this->formRefusalReasons($noarts[9]['permreason'], 'frequent');
                $freqpermreason = utf8_encode($permreasons[0]);
                $freqpermreasoncount = $permreasons[1];
            } else {
                $freqpermreason = '-';
                $freqpermreasoncount = 0;
            }
        }
    $data = '<br />
            <label class="span5 alert alert-info" >Nombre de s&eacute;lections / total participations : <span id="selected">
                    ' . $selected_arts . '/' . $total_parts . ' (' .
                            (($selected_arts && $total_parts != "" ) ? round($selected_arts / $total_parts *100,2) : '0')
                            . '%)'
                            . '</span></label>
                    <label class="span5 alert alert-danger" >Nombre d\'articles valid&eacute;s : <span id="validated">
                            ' . $published_arts . '/' . $total_parts . ' (' .
                (($published_arts && $total_parts != "") ? round($published_arts / $total_parts *100,2) : '0')
                . '%)'
                . '</span></label>
                    <label class="span5 alert alert-success" >Nombre d\'articles refus&eacute;s pour resoumission : <span id="republished">
                        ' . $republished . '/' . $total_parts . ' (' .
                (($republished && $total_parts != "") ? round($republished / $total_parts *100,2) : '0')
                . '%)'
                . '</span></label>
                    <label class="span5 alert alert-success" id="">Raison du refus pour resoumission la plus fr&eacute;quente : <span id="refusedreasons">
                            ' . $freqtempreason . '&nbsp;' . $freqtempreasoncount . '/' . $total_temp_refusalreasons . ' (' .
                (($freqtempreasoncount && $total_temp_refusalreasons != "") ? round($freqtempreasoncount / $total_temp_refusalreasons *100,2) : '0')
                . '%)'
                . '<a class="hint--bottom hint--info" data-hint="Refuse reasons"  data-target="#refusereasonslist" data-toggle="modal"  name="send_comment"  href="/user/refused-reasons?userid=' . $userId . '&type=temp" >Voir plus</a></span></label>
                        <label class="span5 alert alert-info">Nombre d\'articles refus&eacute;s :<span id="refused">
                        ' . $refused_arts . '/' . $total_parts . ' (' .
                (($refused_arts && $total_parts != "") ? round($refused_arts / $total_parts *100,2) : '0')
                . '%)'
                . '</span></label>
                    <label class="span5 alert alert-gebo">Raison du refus la plus fr&eacute;quente  : <span id="refusedreasonscrt">
                     ' . $freqpermreason . '&nbsp;' . $freqpermreasoncount . '/' . $total_prem_refusalreasons . ' (' .
                (($freqpermreasoncount && $total_prem_refusalreasons != "") ? round($freqpermreasoncount / $total_prem_refusalreasons *100,2) : '0')
                . '%)'
                . '<a class="hint--bottom hint--info" data-hint="Refuse reasons corrector"  data-target="#refusereasonslist" data-toggle="modal"  name="send_comment"  href="/user/refused-reasons?userid=' . $userId . '&type=perm" >Voir plus</a></span></label>
                    <label class="span5 alert alert-success">Nombre de retards :<span id="notsubmited">
                    ' . $arts_notsubmited . '/' . $total_parts . ' (' .
                (($arts_notsubmited && $total_parts != "") ? round($arts_notsubmited / $total_parts *100,2) : '0')
                . '%)'
                . '</span></label>
                    <label class="span5 alert alert-info">Nombre de clients avec lesquels il a travaill&eacute; :<span id="numclients"> ' . $num_clients . '</span></label>
                    <label class="span5 alert alert-info">Noms des clients : <span id="clientlist">' . $client_list . '</span></label>';
        echo $data;
        exit;
    }

    ////get the user corrector statistics////////////
    public function correctorstatisticsAction()
    {
        $stats_params = $this->_request->getParams();
        $userId = $stats_params['userid'];
        $user_obj = new EP_User_Contributor();
        $params['userid'] = $stats_params["userid"];
        $params['langs'] = $stats_params["crtstats_lang"];
        $params['categs'] = $stats_params["crtstats_categ"];
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $noarts = $crtparticipate_obj->getCorrectorParticipantsStatistics($params);
        $writersarray = $crtparticipate_obj->getCrtParticipantWtitersLIst($params);
        $marks = 0;
        if ($noarts != 'NO') {
            /*$result[0] = $noarts[0]['selectedCount'];
            $result[1] = $noarts[1]['refusedCount'];
            $result[2] = $noarts[2]['inprocessCount'];
            $result[3] = $noarts[3]['totalCount'];
            $result[4] = $noarts[4]['publishedCount'];
            $result[5] = $noarts[5]['republishedCount'];
            $result[6] = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['crttempreasons'] != '') {
                $crttempreasons = $this->formRefusalReasons($noarts[7]['crttempreasons'], 'frequent');
                $result[12] = $crttempreasons[0];
                $result[7] = $crttempreasons[1];
            } else {
                $result[12] = 'nill';
                $result[7] = 0;
            }
            $result[8] = $noarts[8]['totaltemplateCount'];
            $result[9] = count($writersarray);
            if ($noarts[10]['crtpermreason'] != '') {
                $crtpermreasons = $this->formRefusalReasons($noarts[10]['crtpermreason'], 'frequent');
                $result[13] = $crtpermreasons[0];
                $result[10] = $crtpermreasons[1];
            } else {
                $result[13] = 'nill';
                $result[10] = 0;
            }*/
            $crtselected_arts = $noarts[0]['selectedCount'];
            $crtrefused_arts = $noarts[1]['refusedCount'];
            $crtproccesing_arts = $noarts[2]['inprocessCount'];
            $crttotal_parts = $noarts[3]['totalCount'];
            $crtpublished_arts = $noarts[4]['publishedCount'];
            $crtrepublished = $noarts[5]['republishedCount'];
            $crtarts_notsubmited = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['crttempreasons'] != '') {
                $tempreasons = $this->formRefusalReasons($noarts[7]['crttempreasons'], 'frequent');
                $freqcrttempreason = $tempreasons[0];
                $freqcrttempreasoncount = $tempreasons[1];
            } else {
                $freqcrttempreason = '-';
                $freqcrttempreasoncount = 0;
            }

            $crttotal_refusalreasons = $noarts[8]['totaltemplateCount'];  // total count of refusal reasons
            $crttotal_temp_refusalreasons = ($noarts[7]['crttempreasons'] != "") ? count( explode(",",$noarts[7]['crttempreasons'])) : '0';//condition to fetch the count of the array
            $crttotal_prem_refusalreasons = ($noarts[9]['crtpermreasons'] != "") ? count( explode(",",$noarts[9]['crtpermreasons']) ) : '0';//condition to fetch the count of the array
            $crtnum_writers = count($writersarray);
            if ($noarts[9]['crtpermreasons'] != '') {
                $permreasons = $this->formRefusalReasons($noarts[9]['crtpermreasons'], 'frequent');
                $freqcrtpermreason = utf8_encode($permreasons[0]);
                $freqcrtpermreasoncount = $permreasons[1];
            } else {
                $freqcrtpermreason = '-';
                $freqcrtpermreasoncount = 0;
            }

        }
        $data = '<br />
        <label class="span4 alert alert-info" >Nombre de s&eacute;lections / total participations : <span id="crtselected">
        '.$crtselected_arts.'/'.$crttotal_parts. ' (' .
            ( ($crtselected_arts && $crttotal_parts != "") ? round($crtselected_arts/$crttotal_parts *100,2) : '0')
                . '%)'
                . '</span></label>
    <label class="span6 alert alert-danger" >Nombre d\'articles valid&eacute;s : <span id="crtvalidated">
    '.$crtpublished_arts.'/'.$crttotal_parts. ' (' .
    ( ($crtpublished_arts && $crttotal_parts != "") ? round($crtpublished_arts/$crttotal_parts *100,2) : '0')
    . '%)'
    . '</span></label>
    <label class="span4 alert alert-success" >Nombre d\'articles refus&eacute;s pour resoumission : <span id="crttimeout">
    '.$crtrepublished.'/'.$crttotal_parts. ' (' .
    ( ($crtrepublished && $crttotal_parts != "") ? round($crtrepublished/$crttotal_parts *100,2) : '0')
    . '%)'
    . '</span></label>
    <label class="span6 alert alert-success" id="">Raison du refus pour resoumission la plus fr&eacute;quente :<span id="crtrefusedreasons">
    '.$freqcrttempreason.' '.$freqcrttempreasoncount.'/'.$crttotal_temp_refusalreasons. ' (' .
    ( ($freqcrttempreasoncount && $crttotal_temp_refusalreasons != "") ? round($freqcrttempreasoncount/$crttotal_temp_refusalreasons *100,2) : '0')
    . '%)'
    . '</span><a class="hint--bottom hint--info" data-hint="Refuse reasons"  data-target="#refusereasonslist" data-toggle="modal"  name="send_comment"  href="/user/refused-reasons?userid='.$userId.'&type=crttemp" >Voir plus</a></label>
    <label class="span4 alert alert-info">Nombre d\'articles refus&eacute;s :<span id="crtrefused">
    '.$crtrefused_arts.'/'.$crttotal_parts. ' (' .
    ( ($crtrefused_arts && $crttotal_parts != "") ? round($crtrefused_arts/$crttotal_parts *100,2) : '0')
    . '%)'
    . '</span></label>
    <label class="span6 alert alert-gebo">Raison du refus la plus fr&eacute;quente :<span id="crtrefusedreasonspercent">
                '.$freqcrtpermreason.' '.$freqcrtpermreasoncount.'/'.$crttotal_prem_refusalreasons. ' (' .
    ( ($freqcrtpermreasoncount && $crttotal_prem_refusalreasons != "") ? round($freqcrtpermreasoncount/$crttotal_prem_refusalreasons *100,2) : '0')
    . '%)'
    . '</span><a class="hint--bottom hint--info" data-hint="Refuse reasons"  data-target="#refusereasonslist" data-toggle="modal"  name="send_comment"  href="/user/refused-reasons?userid='.$userId.'&type=crtperm" >Voir plus</a></label>
    <label class="span4 alert alert-success">Nombre de retards :<span id="crtnotsubmited">
    '.$crtarts_notsubmited.'/'.$crttotal_parts. ' (' .
    ( ($crtarts_notsubmited && $crttotal_parts != "") ? round($crtarts_notsubmited/$crttotal_parts *100,2) : '0')
    . '%)'
    . '</span></label>
    <label class="span6 alert alert-info">Nombre de r&eacute;dacteurs not&eacute;s :<span id="crtnumwriters"> '.$crtnum_writers.' <a class="hint--bottom hint--info" data-hint="Writers List"  data-target="#writerlist" data-toggle="modal"  name="send_comment"  href="/user/writer-list?userid='.$userId.'" >Voir plus</a></span></label>';
        echo $data;
        exit;
    }

    ////to display the refused reasons selected by the proofreader for the contributor///
    public function refusedReasonsAction()
    {
        //echo "manage";  exit;
        $stats_params = $this->_request->getParams();
        $userId = $stats_params["userid"];
        $type = $stats_params["type"];
        $reasontype = $stats_params["reasontype"];
        $refusereasons_obj = new EP_Delivery_ArticleReassignReasons();
        $participate_obj = new EP_Participation_Participation();
        $refusereasons_obj = new EP_Delivery_ArticleReassignReasons();
        $template_obj = new Ep_Message_Template();
        if ($type == 'bouser') {
            $reasonsdetails = $refusereasons_obj->getBoUserRefusedReasons($userId, $reasontype);
        } else
            $reasonsdetails = $participate_obj->getRefusedReasons($userId, $type);
        /*edited by naseer on 23-11-2015*/
        if ($reasonsdetails != 'NO' ) {
            if($reasonsdetails[0]['reasons'] != NULL )
                $result = $this->formRefusalReasons($reasonsdetails[0]['reasons'], 'all');
            elseif( $reasonsdetails[0]['crtreasons'] != NULL)
                $result = $this->formRefusalReasons($reasonsdetails[0]['crtreasons'], 'all');
        }

        if ($result != '') {
            /* $reasons = explode(",", $reasonsdetails[0]['reasons']);
             foreach($reasons AS $key=>$value)
             {
                 $result[] = $template_obj->getValTempTitle($reasons[$key]);
             }*/
            $this->_view->reasons = $result;
        } else
            $this->_view->reasons = "NO";
        $this->_view->refusereasonsblock = "yes";
        $this->_view->render("user_userhistory");
    }

    ////to get the writer names list with whom the this corrector worked///
    public function writerListAction()
    {
        $stats_params = $this->_request->getParams();
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $user_obj = new Ep_User_User();
        $writersarray = $crtparticipate_obj->getCrtParticipantWtitersLIst($stats_params);
        if ($writersarray != 'NO') {
            foreach ($writersarray AS $key => $value) {
                $result[] = $user_obj->getAllUsersDetails($writersarray[$key]['user_id']);
            }
            $this->_view->writers = $result;
        } else
            $this->_view->writers = "NO";
        $this->_view->writerlistblock = "yes";
        $this->_view->render("user_userhistory");
    }

    ////to display the graph for the contributors marks//
    public function marksContributorGraphAction()
    {
        $participate_obj = new EP_Participation_Participation();
        $stats_params = $this->_request->getParams();
        $params['userid'] = $stats_params["userid"];
        $params['langs'] = $stats_params["stats_lang"];
        $params['categs'] = $stats_params["stats_categ"];
        $marksforarts = $participate_obj->getMarksOnDateForGraph($params);
        $this->_view->dates = "var d2 = [
                            ";
        foreach ($marksforarts[0] as $key => $val) {
            $this->_view->dates .= "[new Date('" . $key . "').getTime()," . $val . "],
                            ";
        }
        $this->_view->dates .= "
                        ];";

        foreach ($marksforarts AS $key => $value) {
            $marksarr[$key] = $marksforarts[$key]['marks'];
            $datearr[$key] = $marksforarts[$key]['created_at'];
        }
        //print_r($marksarr); print_r($datearr); exit;
        // $this->_view->marksarray           = array(5,9,7,8,2,5,9);
        // $this->_view->dates           = array('02/06/2014','03/06/2014','06/06/2014','09/06/2014','12/06/2014','14/06/2014','15/06/2014');
        /* $this->_view->marksarray           = $marksarr;
         $this->_view->datesarray            = $datearr;*/
        /////////end of graph /////////////////////////
    }

    public function userexistsAction()
    {
        $user_obj = new Ep_User_User();
        $user_params = $this->_request->getParams();

        $emailexit = $user_obj->getExistingEmail($user_params['email']);
        $emailexit = trim($emailexit);
        echo $emailexit;
        exit;
    }

    public function recentcommentsmarksAction()
    {
        $user_obj = new Ep_User_User();
        $artprocess_obj = new EP_Delivery_ArticleProcess();
        $user_params = $this->_request->getParams();
        $userid = $user_params['userid'];
        $partid = $user_params['partid'];
        $type = $user_params['type'];
        if ($type == 'writer') {
            $comments = $artprocess_obj->getRecentVersion($partid);// print_r($comments); echo $comments[0]['comments']; exit;
            $this->_view->recentcomments = $comments[0]['comments'];
            $this->_view->recentcommentsblock = 'yes';
        } else {
            $comments = $artprocess_obj->getMarksByUser($partid, $userid);
            $this->_view->recentcomments = $comments[0]['comments'];
            $this->_view->recentcommentsblock = 'yes';
        }

        $this->_view->render("proofread_markscomments_popup");
    }

    public function checkuseremailexistsAction()
    {
        $emailcheck_params_duplicate = $this->_request->getParams();
        $obj = new Ep_User_User();
        $res = $obj->checkClientMailid($emailcheck_params_duplicate['email']);
        echo $res;

        exit;
    }

    /***************************** White book *************************************/

    public function whitebookDownloadAction()
    {
        $wb_obj = new EP_User_WhitebookDownloads();

        //Multple delete
        if ($_POST['deletewb'] != "") {
            if (count($_POST['deletecheck']) > 0) {
                for ($w = 0; $w < count($_POST['deletecheck']); $w++)
                    $wb_obj->deleteWhitebook($_POST['deletecheck'][$w]);
            }
        }

        //Upload white book
        if ($_POST['submitwb'] != "") {
            $uploaddir = '/home/sites/site5/web/FO/whitebook/';

            $ext = pathinfo($_FILES['uploadwb']['name']);
            if ($ext['extension'] == "pdf" || $ext['extension'] == "zip") {
                $file = $uploaddir . 'whitebook.' . $ext['extension'];
                unlink($file);
                if (move_uploaded_file($_FILES['uploadwb']['tmp_name'], $file))
                    chmod($file, 0777);
            }
        }

        $searchvals = array();
        if ($_REQUEST['search_submit'] != "") {
            $searchvals['start_date'] = $_GET['start_date'];
            $searchvals['end_date'] = $_GET['end_date'];

            $this->_view->start_date = $searchvals['start_date'];
            $this->_view->end_date = $searchvals['end_date'];
        }

        $this->_view->wlist = $wb_obj->listWdownloads($searchvals);
        $this->_view->render("user_whitebookdownload");
    }

    public function uploadWhitebookAction()
    {
        $this->_view->render("user_uploadwhitebook");
    }

    public function bulkwritercreationAction()
    {

        $language = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        $this->_view->language = $language;

        if ($_POST['email_list'] != "") {
            $emails = explode(",", $_POST['email_list']);
            if (count($emails) > 0) {
                for ($e = 0; $e < count($emails); $e++) {
                    $user_obj = new Ep_User_User();
                    $postarray = array();
                    $postarray['email'] = $emails[$e];
                    $postarray['writerpassword'] = $_POST['writerpassword'];
                    $postarray['writerlanguage'] = $_POST['writerlanguage'];
                    $postarray['created_user'] = $this->adminLogin->userId;
                    $user_obj->CreateBulkContribUser($postarray);
                }

                //Email
                if ($_POST['intimatecheck'] == "yes" && $_POST['mailobject'] != "") {
                    for ($m = 0; $m < count($emails); $m++) {
                        $email = $emails[$m];
                        $password = $_POST['writerpassword'];

                        $subject = $_POST['mailsubject'];
                        $object = $_POST['mailobject'];
                        eval("\$subject= \"$subject\";");
                        eval("\$object= \"$object\";");

                        $mail = new Zend_Mail();
                        $mail->addHeader('Reply-To', 'support@edit-place.com');
                        $mail->setBodyHtml($object)
                            ->setFrom('support@edit-place.com', 'Support Edit-place')
                            ->addTo($emails[$m])
                            ->addCc('kavithashree.r@gmail.com')
                            ->setSubject($subject);
                        $mail->send();

                    }
                }

                $this->_helper->FlashMessenger('Users created.');
                $this->_redirect("/user/bulkwritercreation?submenuId=ML10-SL10");
            }

        }

        $this->_view->render("user_bulkwritercreation");
    }

    public function emailListExistsAction()
    {
        $user_obj = new Ep_User_User();
        $user_params = $this->_request->getParams();

        $emails = explode(",", $user_params['email']);

        $existarray = array();
        for ($e = 0; $e < count($emails); $e++) {
            $emailexist = $user_obj->getExistingEmail($emails[$e]);
            if ($emailexist == "yes")
                $existarray[] = $emails[$e];

        }

        if (count($existarray) > 0)
            echo implode(",", $existarray);
        else
            echo "no";
        exit;
    }
    ///////////added by naseer on 06.08.2015/////////////////
    ///////////edit the contributor new /////////////////
    public function contributorEditNewAction()
    {
        $user_obj = new Ep_User_User();
        $changelog_obj = new Ep_User_ProfileChangeLog();
        $experience_obj = new EP_User_ContributorExperience();
        $mail_obj = new Ep_Message_AutoEmails();
        $userId = $this->_request->getParam('userId');
        $user_details = $user_obj->getContributordetails($userId);
        /* *getting User expeience details**/
        $jobDetails = $experience_obj->getExperienceDetails($userId, 'job');
        if ($jobDetails != "NO")
            $this->_view->jobDetails = $jobDetails;
        $educationDetails = $experience_obj->getExperienceDetails($userId, 'education');


        if ($educationDetails != "NO")
            $this->_view->educationDetails = $educationDetails;
        /* *iNOVICE inFO ***/
        $this->_view->payment_type = $user_details[0]['payment_type'];
        $this->_view->pay_info_type = $user_details[0]['pay_info_type'];
        $this->_view->SSN = $user_details[0]['SSN'];
        $this->_view->company_number = $user_details[0]['company_number'];
        $this->_view->vat_check = $user_details[0]['vat_check'];
        $this->_view->VAT_number = $user_details[0]['VAT_number'];
        /* *Paypal and RIB info**/
        $this->_view->paypal_id = $user_details[0]["paypal_id"];
        $RIB_ID = explode("|", $user_details[0]["rib_id"]);
        if (($user_details[0]['pay_info_type'] == 'out_france' || $user_details[0]['country'] != 38) && count($RIB_ID) == 2) {
            $this->_view->rib_id_6 = $RIB_ID[0];
            $this->_view->rib_id_7 = $RIB_ID[1];
        } else {
            $this->_view->rib_id_1 = $RIB_ID[0];
            $this->_view->rib_id_2 = $RIB_ID[1];
            $this->_view->rib_id_3 = $RIB_ID[2];
            $this->_view->rib_id_4 = $RIB_ID[3];
            $this->_view->rib_id_5 = $RIB_ID[4];
        }
        //////edit contributor////////////////////////////////////
        $this->_view->user_detail = $user_details;   //echo "<pre>";print_r($user_details); exit;

        $this->_view->dob = date('d-m-Y', strtotime($user_details[0]['dob']));
        $this->_view->self_details = utf8_encode($user_details[0]['self_details']);
        $this->_view->stats = $_GET['stats'];
        $this->_view->loguser = $this->adminLogin->userId;
        $this->_view->category_more = unserialize($user_details[0]['category_more']);
        $this->_view->language_more = unserialize($user_details[0]['language_more']);
        /* by naseer on 06.08.2015 */

        $REQ = $this->_request->getParams();
        if ($this->_request->getParam('submit_contrib_bo_only') != '') {
            $user_obj->updateContribBoOnly($REQ);
            $this->_redirect("/user/contributor-edit-new?submenuId=ML10-SL1&tab=editcontrib&userId=" . $userId);
        } elseif ($this->_request->getParam('submit_contrib_user_basic') != '') {
            $user_obj->updateContribUserBasic($REQ);
            $this->_redirect("/user/contributor-edit-new?submenuId=ML10-SL1&tab=editcontrib&userId=" . $userId);
        } elseif ($this->_request->getParam('submit_contrib_categories_and_lang') != '') {
            $user_obj->updateContribCategoriesAndLang($REQ);
            $this->_redirect("/user/contributor-edit-new?submenuId=ML10-SL1&tab=editcontrib&userId=" . $userId);
        } elseif ($this->_request->getParam('submit_contrib_experience') != '') {
            $this->updateExperienceDetails($REQ, 'job');
            $this->updateExperienceDetails($REQ, 'education');
            $this->_redirect("/user/contributor-edit-new?submenuId=ML10-SL1&tab=editcontrib&userId=" . $userId);
        } elseif ($this->_request->getParam('submit_contrib_payment_info_form') != '') {
            $user_obj->updateContribPaymentInfo($REQ);
            $this->_redirect("/user/contributor-edit-new?submenuId=ML10-SL1&tab=editcontrib&userId=" . $userId);
        } elseif ($this->_request->getParam('submit_contrib_more_info_form') != '') {
            $user_obj->updateContribMoreInfo($REQ);
            $this->_redirect("/user/contributor-edit-new?submenuId=ML10-SL1&tab=editcontrib&userId=" . $userId);
        }
        /* end of by naseer on 06.08.201*/
        if ($this->_request->getParam('submit_contrib') != '') {
            $user_obj->updatecontribUser($REQ);
            $this->updateExperienceDetails($REQ, 'job');
            $this->updateExperienceDetails($REQ, 'education');
            //If profile type changed from junior to senior
            if ($REQ['profile_type'] == 'senior' && $REQ['prev_profile'] == 'junior') {
                // $parameters['jc_limit']=$this->getConfiguredval('jc_limit');
                // $parameters['sc_limit']=$this->getConfiguredval('sc_limit');
                $mail_obj->messageToEPMail($userId, 30, $parameters);
            }
            //If profile type2 in corrector aspect changed from junior to senior
            if ($REQ['type2'] == 'yes' && $REQ['profile_type2'] == 'senior' && $REQ['prev_profile2'] == 'junior') {
                $mail_obj->messageToEPMail($userId, 110, $parameters);
            }
            $this->_helper->FlashMessenger('Profile Updated Successfully.');
            $this->_redirect("/user/contributor-edit?submenuId=ML10-SL1&tab=editcontrib&userId=" . $userId);
        }
        //////view contributor////////////////////////////////////
        $this->_view->ep_contrib_profile_language_more = explode(",", $user_details[0]['language_more']);
        $this->_view->ep_contrib_profile_category = explode(",", $user_details[0]['favourite_category']);
        $this->_view->self_details = utf8_encode($user_details[0]['self_details']);
        $this->_view->stats = $_GET['stats'];
        $this->_view->loguser = $this->adminLogin->userId;
        $contrib_picture_path = "/home/sites/site5/web/FO/profiles/contrib/pictures/" . $user_details[0]['user_id'] . "/" . $user_details[0]['user_id'] . "_h.jpg";
        if (file_exists($contrib_picture_path))
            $contrib_picture_path = "http://ep-test.edit-place.com/FO/profiles/contrib/pictures/" . $user_details[0]['user_id'] . "/" . $user_details[0]['user_id'] . "_h.jpg";
        else
            $contrib_picture_path = "http://ep-test.edit-place.com/FO/images/Contrib/profile-img-def.png";
        $this->_view->user_pic = $contrib_picture_path;

        $expCat = explode(',', preg_replace("/\([^)]+\)/", "", $this->unserialisearray($user_details[0]['category_more'])));
        preg_match_all('#\((.*?)\)#', $this->unserialisearray($user_details[0]['category_more']), $match);

        foreach ($expCat as $key => $value) {
            $impCat[] = $this->category_array[$value] . '(' . $match[1][$key] . ')';
        }
        $user_details[0]['category_more'] = implode(',', $impCat);
        unset($impCat);

        $user_details[0]['language_more'] = $this->unserialisearray($user_details[0]['language_more']);
        $workexpDetails = $user_obj->getExperienceDetails($user_details[0]['user_id'], 'job');
        if ($workexpDetails != "NO") {
            $ecnt = 0;
            foreach ($workexpDetails as $workexp) {
                $workexpDetails[$ecnt]['start_date'] = date('FY', strtotime($workexp['from_year'] . "-" . $workexp['from_month']));
                if ($workexp['still_working'] == 'yes')
                    $workexpDetails[$ecnt]['end_date'] = 'Actuel';
                else
                    $workexpDetails[$ecnt]['end_date'] = date('FY', strtotime($workexp['to_year'] . "-" . $workexp['to_month']));
                $ecnt++;
            }
            $this->_view->educationDetailsview = $workexpDetails;
        }
        //echo "<pre>"; print_r($user_details);    exit;
        $this->_view->user_detail = $user_details;
        $this->_view->country_name = $this->country_array[$user_details[0]['country']];
        $this->_view->ep_contrib_profile_language_more = explode(",", $user_details[0]['language_more']);
        $this->_view->ep_contrib_profile_category = explode(",", $user_details[0]['favourite_category']);
        $this->_view->profession = $this->profession_array[$user_details[0]['profession']];
        $this->_view->language = $this->language_array[$user_details[0]['language']];
        $this->_view->nationality = $this->nationality_array[$user_details[0]['nationality']];
        $this->_view->self_details = utf8_encode(strip_tags($user_details[0]['self_detailss']));
        //lang_more str
        $language_more = "";
        if ($user_details[0]['language_more'] != NULL) {
            $lang_more = explode(",", $user_details[0]['language_more']);

            if (count($lang_more) > 0) {
                for ($l = 0; $l < count($lang_more); $l++) {
                    $language_more .= $this->language_array[$lang_more[$l]];

                    if ($l != count($lang_more) - 1)
                        $language_more .= ",";
                }
            }
        }
        $this->_view->language_more1 = $language_more;
        //fav category str
        $favourite_category = "";
        if ($user_details[0]['favourite_category'] != NULL) {
            $fav_cat = explode(",", $user_details[0]['favourite_category']);

            if (count($fav_cat) > 0) {
                for ($l = 0; $l < count($fav_cat); $l++) {
                    $favourite_category .= $this->category_array[$fav_cat[$l]];

                    if ($l != count($fav_cat) - 1)
                        $favourite_category .= ",";
                }
            }
        }
        $this->_view->favourite_category = $favourite_category;
        $eduarray = array("1" => "Bac +1", "2" => "Bac +2", "3" => "Bac +3", "4" => "Bac +4", "5" => "Bac +5", "6" => "Bac+5 et plus");
        $this->_view->education = $eduarray[$user_details[0]['education']];
        /*added by naseer on 04-11-2015*/
        //fetch new fields "software_list"//
        $software_array = $this->_arrayDb->loadArrayv2("EP_SOFTWARE_LIST", $this->_lang);
        //explodig all the software list and saing in multidimentaional array for later use//
        foreach ($software_array as $k => $v) {
            $software_array[$k] = explode("-", $v);
        }
        $this->_view->ep_software_array = $software_array;
        //exploding and saving the values since i was imploded at the time of insertion//
        $software_list_temp = explode("###$$$###", $user_details[0]['software_list']);
        for ($i = 0; $i < count($software_list_temp); $i++) {
            $software_list[$i] = explode('|', $software_list_temp[$i]);
        }
        $this->_view->software_list = $software_list;
        $this->_view->software_list_count = count($software_list);//saving the count for later use in phtml file//
        /* end of added by naseer on 04-11-2015*/

        /////////////contributor statistics /////////////////////
        $stats_params = $this->_request->getParams();
        $user_obj = new EP_User_Contributor();
        $participate_obj = new EP_Participation_Participation();
        $params['userid'] = $userId;

        /////////////writer graph part ///////////
        $params['userid'] = $userId;
        $marksforarts = $participate_obj->getMarksWriterGraph($params, 'writer'); //print_r($marksforarts); exit;
        if ($marksforarts != 'NO') {

            $this->_view->dates = "var d2 = [
             ";
            if ($marksforarts)
                foreach ($marksforarts[0] as $key => $val) {
                    //echo "<pre>".$key = substr($key, 0, -3);
                    $key = substr($key, 0, -3);
                    if ($val == '')
                        $val = 0;
                    $this->_view->dates .= "[new Date('" . $key . "').getTime()," . $val . "],
                 ";
                }
            $this->_view->dates .= "];
             ";  //exit;
            //  echo $this->_view->dates; exit;
        } else {
            $this->_view->dates = "var d2 = [ " . date('Y-m') . " ];";
        }
        /////////end of graph /////////////////////////
        /////////////corrector graph part ///////////
        $crtmarksforarts = $participate_obj->getMarksWriterGraph($params, 'corrector'); //print_r($marksforarts); exit;
        if ($crtmarksforarts != 'NO') {
            $this->_view->crtdates = "var d2 = [
             ";
            if ($crtmarksforarts)
                foreach ($crtmarksforarts[0] as $key => $val) {
                    //echo "<pre>".$key = substr($key, 0, -3);
                    $key = substr($key, 0, -3);
                    if ($val == '')
                        $val = 0;
                    $this->_view->crtdates .= "[new Date('" . $key . "').getTime()," . $val . "],
                 ";
                }
            $this->_view->crtdates .= "];
             ";  //exit;
        } else {
            $this->_view->crtdates = "var d2 = [ " . date('Y-m') . " ];";
        }
        /////////end of corrector graph /////////////////////////
        $noarts = $participate_obj->getParticipantsStatistics($params);
        $clientnamesarray = $participate_obj->getParticipantClientLIst($params);
        if ($clientnamesarray != 'NO') {
            foreach ($clientnamesarray AS $key => $value) {
                $clientnames[$key] = $clientnamesarray[$key]['company_name'];
            }
            $this->_view->client_list = implode(",", $clientnames);
        } else
            $this->_view->client_list = 0;
        $marks = 0;
        if ($noarts != '') {
            $this->_view->selected_arts = $noarts[0]['selectedCount'];
            $this->_view->refused_arts = $noarts[1]['refusedCount'];
            $this->_view->proccesing_arts = $noarts[2]['inprocessCount'];
            $this->_view->total_parts = $noarts[3]['totalCount'];
            $this->_view->published_arts = $noarts[4]['publishedCount'];
            $this->_view->republished = $noarts[5]['republishedCount'];
            $this->_view->arts_notsubmited = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['tempreasons'] != '') {
                $tempreasons = $this->formRefusalReasons($noarts[7]['tempreasons'], 'frequent');
                $this->_view->freqtempreason = $tempreasons[0];
                $this->_view->freqtempreasoncount = $tempreasons[1];
            } else {
                $this->_view->freqtempreason = 'nill';
                $this->_view->freqtempreasoncount = 0;
            }
            $this->_view->total_refusalreasons = $noarts[8]['totaltemplateCount'];  // total count of refusal reasons
            if ($clientnamesarray != 'NO')
                $this->_view->num_clients = count($clientnamesarray);
            else
                $this->_view->num_clients = 0;
            if ($noarts[9]['permreason'] != '') {
                $permreasons = $this->formRefusalReasons($noarts[9]['permreason'], 'frequent');
                $this->_view->freqpermreason = $permreasons[0];
                $this->_view->freqpermreasoncount = $permreasons[1];
            } else {
                $this->_view->freqpermreason = 'nill';
                $this->_view->freqpermreasoncount = 0;
            }

        }
        /////////////////////////////
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $noarts = $crtparticipate_obj->getCorrectorParticipantsStatistics($params);
        $writersarray = $crtparticipate_obj->getCrtParticipantWtitersLIst($params); // print_r($noarts);exit;

        $marks = 0;
        if ($noarts != 'NO') {
            $this->_view->crtselected_arts = $noarts[0]['selectedCount'];
            $this->_view->crtrefused_arts = $noarts[1]['refusedCount'];
            $this->_view->crtproccesing_arts = $noarts[2]['inprocessCount'];
            $this->_view->crttotal_parts = $noarts[3]['totalCount'];
            $this->_view->crtpublished_arts = $noarts[4]['publishedCount'];
            $this->_view->crtrepublished = $noarts[5]['republishedCount'];
            $this->_view->crtarts_notsubmited = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['crttempreasons'] != '') {
                $tempreasons = $this->formRefusalReasons($noarts[7]['crttempreasons'], 'frequent');
                $this->_view->freqcrttempreason = $tempreasons[0];
                $this->_view->freqcrttempreasoncount = $tempreasons[1];
            } else {
                $this->_view->freqcrttempreason = 'nill';
                $this->_view->freqcrttempreasoncount = 0;
            }

            $this->_view->crttotal_refusalreasons = $noarts[8]['totaltemplateCount'];  // total count of refusal reasons
            $this->_view->crtnum_writers = count($writersarray);
            if ($noarts[9]['crtpermreasons'] != '') {
                $permreasons = $this->formRefusalReasons($noarts[9]['crtpermreasons'], 'frequent');
                $this->_view->freqcrtpermreason = $permreasons[0];
                $this->_view->freqcrtpermreasoncount = $permreasons[1];
            } else {
                $this->_view->freqcrtpermreason = 'nill';
                $this->_view->freqcrtpermreasoncount = 0;
            }
        }
        /////get all the articles in writer aspect/////////////////////////////////////////////////////
        $client_obj = new Ep_User_Client();
        $user_obj = new Ep_User_User();
        $articleprocess_obj = new EP_Delivery_ArticleProcess();
        $searchParams = $this->_request->getParams();

        $res = $participate_obj->writerStatsArticles($userId, $searchParams);
        if ($res != 'NO') {
            foreach ($res as $key1 => $value1) {
                $client = $client_obj->getClientName($res[$key1]['clientid']);
                $res[$key1]['client'] = $client[0]['company_name'];
                $res[$key1]['language'] = utf8_encode($this->language_array[$res[$key1]['language']]);
                $res[$key1]['category'] = utf8_encode($this->category_array[$res[$key1]['category']]);
                $marksdetails = $articleprocess_obj->getFristVersionDate($res[$key1]['id']);
                if ($marksdetails != 'NO')
                    $res[$key1]['marks'] = $marksdetails[0]['marks'];
                /** Author: Thilagam **/
                /** Date:16/05/2016 **/
                /** Reason: DB error was displayed while edit **/
                if($res[$key1]['created_user'])
                {
                    $users = $user_obj->getAllUsersDetails($res[$key1]['created_user']);
                    $res[$key1]['projectManager'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                }
                /**End of modification**/

                $parts = $participate_obj->crtParticipationDetails($res[$key1]['id']); //print_r($parts); exit;
                if ($parts != 'NO') {
                    $users = $user_obj->getAllUsersDetails($parts[0]['corrector_id']);
                    $res[$key1]['corrector'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                }
                if ($res[$key1]['status'] == 'bid')
                    $res[$key1]['lateststatus'] = "En cours de redaction";
                if ($res[$key1]['status'] == 'under_study')
                    $res[$key1]['lateststatus'] = "En relecture client";
                if ($res[$key1]['status'] == 'disapprove_client')
                    $res[$key1]['lateststatus'] = "Refus client";
                if ($res[$key1]['status'] == 'published')
                    $res[$key1]['lateststatus'] = "Valid&eacute;";
                if ($res[$key1]['status'] == 'closed_client_temp')
                    $res[$key1]['lateststatus'] = "Ferm&eacute; client";
                if ($res[$key1]['status'] == 'plag_exec')
                    $res[$key1]['lateststatus'] = "Plagiarism";
                //else
                // $res[$key1]['lateststatus'] = $res[$key1]['status'];

            }
        }
        // print_r($res); exit;
        $this->_view->writerstatsarticles = $res;
        //////////////get all aritcle in corrector aspect
        $crtres = $crtparticipate_obj->crtStatsArticles($userId, $searchParams);
        //print_r($crtres);exit;
        if ($crtres != 'NO') {
            foreach ($crtres as $key1 => $value1) {
                if ($crtres[$key1]['participate_id'] != '') {
                    $client = $client_obj->getClientName($crtres[$key1]['clientid']);
                    $crtres[$key1]['client'] = $client[0]['company_name'];
                    $crtres[$key1]['language'] = utf8_encode($this->language_array[$crtres[$key1]['language']]);
                    $crtres[$key1]['category'] = utf8_encode($this->category_array[$crtres[$key1]['category']]);
                    $marksdetails = $articleprocess_obj->getMarksByUser($crtres[$key1]['participate_id'], $userId);
                    if ($marksdetails != 'NO')
                        $crtres[$key1]['marks'] = $marksdetails[0]['marks'];
                    $users = $user_obj->getAllUsersDetails($crtres[$key1]['created_user']);
                    $crtres[$key1]['projectManager'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                    $parts = $participate_obj->getParticipateDetails($crtres[$key1]['participate_id']);
                    if ($parts != 'NO')
                        $users = $user_obj->getAllUsersDetails($parts[0]['user_id']);


                    $crtres[$key1]['writer'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                    $crtres[$key1]['userId'] = $userId;
                    if ($crtres[$key1]['status'] == 'bid')
                        $crtres[$key1]['lateststatus'] = "En cours de redaction";
                    if ($crtres[$key1]['status'] == 'under_study')
                        $crtres[$key1]['lateststatus'] = "En relecture client";
                    if ($crtres[$key1]['status'] == 'disapprove_client')
                        $crtres[$key1]['lateststatus'] = "Refus client";
                    if ($crtres[$key1]['status'] == 'published')
                        $crtres[$key1]['lateststatus'] = "Valid&eacute;";
                    if ($crtres[$key1]['status'] == 'closed_client_temp')
                        $crtres[$key1]['lateststatus'] = "Ferm&eacute; client";
                    if ($crtres[$key1]['status'] == 'plag_exec')
                        $crtres[$key1]['lateststatus'] = "Plagiarism";
                }
            }
        }
        // print_r($res); exit;
        /*add by naseer on 09-11-2015*/
        //fetch the userlogs with the user_id//
        $this->_view->userId = $userId;
        $this->_view->user_logs = $this->getUserLogs($userId);
        /*end of add by naseer on 09-11-2015*/
        $this->_view->crtstatsarticles = $crtres;
        $this->_view->render("user_contributoreditnew");
    }


    /*end of added on 16.11.2015*/
    /*fetch all the userLogs for individual user in readable format */
    public function getUserLogs($userId)
    {
        $userlogs_obj = new Ep_User_UserLogs();
        $user_logs = $userlogs_obj->getUserLogs($userId);
        $listfieldnames = array(
            'password' => 'Mon mot de passe',
            'initial' => 'Mon identit&eacute;',
            'first_name' => 'Mon prénom',
            'last_name' => 'Mon nom',
            'dob' => 'Ma date de naissance',
            'language' => 'Langue maternelle',
            'address' => 'Adresse',
            'city' => 'Ville',
            'country' => 'Pays',
            'nationality' => 'Nationalit&eacute;',
            'zipcode' => 'Code Postal',
            'phone_number' => 'T&eacute;l&eacute;phone',
            'self_details' => 'Texte de pr&eacute;sentation',
            'passport_no' => 'Num&eacute;ro de passeport',
            'id_card' => 'Carte d\'identit&eacute;',
            'language_more' => 'Autre(s) langue(s) parl&eacute;e(s)',
            'category_more' => 'Comp&eacute;tences & niveau de maitrise',
            'title' => 'title',
            'institute' => 'Institute',
            'contract' => 'Contract',
            'from_month' => 'From Month',
            'from_year' => 'From Year',
            'still_working' => 'Still Working',
            'to_month' => 'To Month',
            'to_year' => 'TO Year',
            'software_list' => 'Software Ownership and Experience Level',
            'tva_number' => 'Num&eacute;ro de TVA Intracommunautaire',
            'com_name' => 'D&eacute;nomination sociale',
            'com_address' => 'Adresse',
            'com_city' => 'Ville',
            'com_zipcode' => 'Code Postal',
            'com_country' => 'Pays',
            'com_siren' => 'Siren',
            'com_tva_number' => 'Num&eacute;ro de TVA Intracommunautaire',
            'com_phone' => 'T&eacute;l&eacute;phone',
            'siren_number' => 'Siren',
            'paypal_id' => 'Pay Pal Id',
            'bank_account_name' => 'Nom du bénéficiaire',
            'virement' => 'Virement',
            'rib_id' => 'Virement Details',
            'payment_type' => 'Payment Type',
            'options_flag' => 'Payment Option',
            'alert_subscribe' => 'Alert Subscribe',
            'subscribe' => 'Subscribe',
            'status' => 'Status',
            'profile_type' => 'Type ',
            'type2' => 'Corrector ',
            'profile_type2' => 'Corrector Status',
            'blackstatus' => 'Black list ',
            'contributortest' => 'Ce contributeur a &eacute;t&eacute; test&eacute;',
            'contributortestcomment' => 'Comments',
            'contributortestmarks' => 'Marks',
            'translator' => 'Translator',
            'translator_type' => 'Translator Type',
            'writer_preference' => 'Writer Preference',
            'twitter_id' => 'Twitter ID',
            'facebook_id' => 'Facebook ID',
            'website' => 'Website',
            'translator_type' => 'Translator Type',
            'Other' => 'Others',
        );//edited by naseer on 26.11.2015//
        $log_type_name = array(
            'profile' => 'Profile',
            'language_update' => 'Language Update',
            'category_update' => 'Category Update',
            'job_update' => 'job Update',
            'edu_update' => 'Education Update',
            'skill_update' => 'Skill Update',
            'payment' => 'Payment',
            'payment_type' => 'Payment Type',
            'company_entrepreneur_update' => 'company/entrepreneur Update',
            'subscription' => 'Subscription',
            'onlybo' => 'BO(only BO can Change)',
            'other' => 'Others');
        $rib = array('Nom de l\'&eacute;tablissemen|Code Banque|Code Guichet|Num&eacute;ro de compte|Cl&eacute; RIB', 'BIC|IBAN');
        $software_list = array("Software", "Level", "Own It");
        $software_array = $this->_arrayDb->loadArrayv2("EP_SOFTWARE_LIST", $this->_lang);
        //explodig all the software list and saing in multidimentaional array for later use//
        foreach ($software_array as $k => $v) {
            $software_array[$k] = explode("-", $v);
        }

        for ($i = 0; $i < count($user_logs); $i++) {
            $user_logs[$i]['field_name'] = $listfieldnames[$user_logs[$i]['field']];//fetchs the values from array $listfieldnames
            $user_logs[$i]['log_type'] = $log_type_name[$user_logs[$i]['log_type']];//fetchs the values from array $log_type_name
            if ($user_logs[$i]['field'] === 'dob') {
                $user_logs[$i]['old_value'] = date('d-m-Y', strtotime($user_logs[$i]['old_value']));
                $user_logs[$i]['new_value'] = date('d-m-Y', strtotime($user_logs[$i]['new_value']));
            } elseif ($user_logs[$i]['field'] === 'language') {
                $user_logs[$i]['old_value'] = $this->language_array[$user_logs[$i]['old_value']];
                $user_logs[$i]['new_value'] = $this->language_array[$user_logs[$i]['new_value']];
            } elseif ($user_logs[$i]['field'] === 'country' || $user_logs[$i]['field'] === 'com_country') {
                $user_logs[$i]['old_value'] = $this->country_array[$user_logs[$i]['old_value']];
                $user_logs[$i]['new_value'] = $this->country_array[$user_logs[$i]['new_value']];
            } elseif ($user_logs[$i]['field'] === 'nationality') {
                $user_logs[$i]['old_value'] = $this->nationality_array[$user_logs[$i]['old_value']];
                $user_logs[$i]['new_value'] = $this->nationality_array[$user_logs[$i]['new_value']];
            } elseif ($user_logs[$i]['field'] === 'language_more' || $user_logs[$i]['field'] === 'category_more') {
                $old_value = unserialize($user_logs[$i]['old_value']);
                $temp1 = "";
                foreach ($old_value as $key => $value) {
                    $temp1 .= $key . "($value),";
                }
                $user_logs[$i]['old_value'] = $temp1;
                //for new value//
                $new_value = unserialize($user_logs[$i]['new_value']);
                $temp2 = "";
                foreach ($new_value as $key => $value) {
                    $temp2 .= $key . "($value),";
                }
                $user_logs[$i]['new_value'] = $temp2;
            } elseif ($user_logs[$i]['field'] === 'rib_id') {
                $old_value = explode("|", $user_logs[$i]['old_value']);
                $temp1 = "";
                $names = "";
                if (count($old_value) > 2) {
                    $names = explode("|", $rib[0]);
                } else {
                    $names = explode("|", $rib[1]);
                }
                for ($j = 0; $j < count($names); $j++) {
                    $temp1 .= $names[$j] . " : " . $old_value[$j] . "<br />";
                }
                $user_logs[$i]['old_value'] = $temp1;
                //for new values
                $new_value = explode("|", $user_logs[$i]['new_value']);
                $temp2 = "";
                $names = "";
                if (count($new_value) > 2) {
                    $names = explode("|", $rib[0]);
                } else {
                    $names = explode("|", $rib[1]);
                }
                for ($j = 0; $j < count($names); $j++) {
                    $temp2 .= $names[$j] . " : " . $new_value[$j] . "<br />";
                }
                $user_logs[$i]['new_value'] = $temp2;

            } elseif ($user_logs[$i]['field'] === 'software_list') {
                $old_value = explode("###$$$###", $user_logs[$i]['old_value']);
                $str1 = '';

                for ($j = 0; $j < count($old_value) - 1; $j++) {
                    $temp1 = explode("|", $old_value[$j]);
                    $str1 .= $software_list[0] . ' : ' . $software_array[$temp1[0]][1];
                    $str1 .= "(" . $software_list[1] . ' : ' . $temp1[1] . ") - ";
                    $str1 .= $software_list[2] . ' : ' . (($temp1[2] === 'on') ? 'Yes' : 'No');

                    $str1 .= "<br />";
                }
                $user_logs[$i]['old_value'] = $str1;
                //for new values
                $new_value = explode("###$$$###", $user_logs[$i]['new_value']);
                $str1 = '';

                for ($j = 0; $j < count($new_value) - 1; $j++) {
                    $temp1 = explode("|", $new_value[$j]);
                    $str1 .= $software_list[0] . ' : ' . $software_array[$temp1[0]][1];
                    $str1 .= "(" . $software_list[1] . ' : ' . $temp1[1] . ") - ";
                    $str1 .= $software_list[2] . ' : ' . (($temp1[2] === 'on') ? 'Yes' : 'No');

                    $str1 .= "<br />";
                }
                $user_logs[$i]['new_value'] = $str1;
            }

        }
        //    echo "<pre>";print_r($user_logs);exit;
        return $user_logs;
    }

    public function loadUserLogsAction()
    {
        $userId = $this->_request->getParam('userId');
        $user_logs = $this->getUserLogs($userId);
        $data = '<table id="user_logs_table" class="table table-bordered table-striped table_vam">
                        <thead>
                        <tr>
                            <th>Log Type Changed</th>
                            <th>Field Changed</th>
                            <th>Previous values</th>
                            <th>New values</th>
                            <th>Updated At</th>
                        </tr>
                        </thead>';
        for ($i = 0; $i < count($user_logs); $i++) {
            $data .= '<tr>
                <td>' . $user_logs[$i]['log_type'] . '</td>
                <td>' . $user_logs[$i]['field_name'] . '</td>
                <td>' . (($user_logs[$i]['old_value'] !== "") ? $user_logs[$i]['old_value'] : "-") . '</td>
                <td>' . (($user_logs[$i]['new_value'] !== "") ? $user_logs[$i]['new_value'] : "-") . '</td>
                <td>' . $user_logs[$i]['updated_at'] . '</td>
            </tr>';
        }
        $data .= '</table>';
        echo $data;
        exit;
    }
    public function loadWritersartsAction(){
        $userId = $this->_request->getParam('userId');
        $client_obj = new Ep_User_Client();
        $user_obj = new Ep_User_User();
        $articleprocess_obj = new EP_Delivery_ArticleProcess();
        $searchParams = $this->_request->getParams();
        $participate_obj = new EP_Participation_Participation();
        $res = $participate_obj->writerStatsArticles($userId, $searchParams);
        if ($res != 'NO') {
            foreach ($res as $key1 => $value1) {
                $client = $client_obj->getClientName($res[$key1]['clientid']);
                $res[$key1]['client'] = $client[0]['company_name'];
                $res[$key1]['language'] = utf8_encode($this->language_array[$res[$key1]['language']]);
                $res[$key1]['category'] = utf8_encode($this->category_array[$res[$key1]['category']]);
                $marksdetails = $articleprocess_obj->getFristVersionDate($res[$key1]['id']);
                if ($marksdetails != 'NO')
                    $res[$key1]['marks'] = $marksdetails[0]['marks'];
                $users = $user_obj->getAllUsersDetails($res[$key1]['created_user']);
                $res[$key1]['projectManager'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                $parts = $participate_obj->crtParticipationDetails($res[$key1]['id']); //print_r($parts); exit;
                if ($parts != 'NO') {
                    $users = $user_obj->getAllUsersDetails($parts[0]['corrector_id']);
                    $res[$key1]['corrector'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                }
                if ($res[$key1]['status'] == 'bid')
                    $res[$key1]['lateststatus'] = "En cours de redaction";
                if ($res[$key1]['status'] == 'under_study')
                    $res[$key1]['lateststatus'] = "En relecture client";
                if ($res[$key1]['status'] == 'disapprove_client')
                    $res[$key1]['lateststatus'] = "Refus client";
                if ($res[$key1]['status'] == 'published')
                    $res[$key1]['lateststatus'] = "Valid&eacute;";
                if ($res[$key1]['status'] == 'closed_client_temp')
                    $res[$key1]['lateststatus'] = "Ferm&eacute; client";
                if ($res[$key1]['status'] == 'plag_exec')
                    $res[$key1]['lateststatus'] = "Plagiarism";
                //else
                // $res[$key1]['lateststatus'] = $res[$key1]['status'];

            }
        }
         //print_r($res); exit;
        //$this->_view->writerstatsarticles = $res;
        $writerstatsarticles = $res;
        $data = '<table id="writermissions" class="table table-bordered table-striped table_vam"  >
            <thead>
            <tr>
                <th>Nom de l\'article</th>
                <th>Nom de l\'annonce</th>
                <th>Nom du client</th>
                <th>Etat</th>
                <th>Correcteur</th>
                <th>Chef de projet</th>
                <th>Langue</th>
                <th>Cat&eacute;gories</th>
                <th>Notes</th>
            </tr>
            </thead>
            <tbody>';
            if($writerstatsarticles !== 'NO'){
                for($j=0;$j<count($writerstatsarticles);$j++) {
                    $temp1="";
                    $temp2="";
                    $temp1 = ($writerstatsarticles[$j]['corrector'] != '') ? $writerstatsarticles[$j]['corrector'] : '-';

                    if($writerstatsarticles[$j]['marks'] != '')
                        $temp2 = ($writerstatsarticles[$j]['marks'] / 10) . ' <a class="hint--bottom hint--info" data-hint="Commenter" data-target="#markscomments" data-toggle="modal"  name="send_comment"  href="/user/recentcommentsmarks?partid=' . $writerstatsarticles[$j]['id'] . '&type=writer&userid=' . $userId . '" ><i class="splashy-comments_reply"></i></a>';
                    else
                        $temp2 = 'Nill';
                    $data .= '<tr>
                            <td>'.utf8_decode($writerstatsarticles[$j]['title']).'</td>
                            <td>'.utf8_encode($writerstatsarticles[$j]['deliveryName']).'</td>
                            <td>'.utf8_decode(($writerstatsarticles[$j]['client'] != '') ? $writerstatsarticles[$j]['client'] : '-').'</td>
                            <td>'.utf8_decode(($writerstatsarticles[$j]['lateststatus'] != '') ? $writerstatsarticles[$j]['lateststatus'] : '-').'</td>
                            <td>'.$temp1.'</td>
                            <td>'.utf8_decode(($writerstatsarticles[$j]['projectManager'] != '') ? $writerstatsarticles[$j]['projectManager'] : '-').'</td>
                            <td>'.utf8_decode(($writerstatsarticles[$j]['language'] != '') ? $writerstatsarticles[$j]['language'] : '-').'</td>
                            <td>'.utf8_decode(($writerstatsarticles[$j]['category'] != '') ? $writerstatsarticles[$j]['category'] : '-').'</td>
                            <td>'.$temp2.'</td>
                        </tr>';
                    }

            }
            $data .= '</tbody>
        </table>';
        echo $data;
    }
    public function loadCorrectorartsAction(){
        $userId = $this->_request->getParam('userId');
        $client_obj = new Ep_User_Client();
        $user_obj = new Ep_User_User();
        $articleprocess_obj = new EP_Delivery_ArticleProcess();
        $searchParams = $this->_request->getParams();
        $participate_obj = new EP_Participation_Participation();
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        //////////////get all aritcle in corrector aspect
        $crtres = $crtparticipate_obj->crtStatsArticles($userId, $searchParams);
        if ($crtres != 'NO') {
            foreach ($crtres as $key1 => $value1) {
                $client = $client_obj->getClientName($crtres[$key1]['clientid']);
                $crtres[$key1]['client'] = $client[0]['company_name'];
                $users = $user_obj->getAllUsersDetails($crtres[$key1]['created_user']);
                $crtres[$key1]['projectManager'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                if ($crtres[$key1]['status'] == 'bid')
                    $crtres[$key1]['lateststatus'] = "En cours de redaction";
                if ($crtres[$key1]['status'] == 'under_study')
                    $crtres[$key1]['lateststatus'] = "En relecture client";
                if ($crtres[$key1]['status'] == 'disapprove_client')
                    $crtres[$key1]['lateststatus'] = "Refus client";
                if ($crtres[$key1]['status'] == 'published')
                    $crtres[$key1]['lateststatus'] = "Valid&eacute;";
                if ($crtres[$key1]['status'] == 'closed_client_temp')
                    $crtres[$key1]['lateststatus'] = "Ferm&eacute; client";
                if ($crtres[$key1]['status'] == 'plag_exec')
                    $crtres[$key1]['lateststatus'] = "Plagiarism";
                $crtres[$key1]['language'] = utf8_encode($this->language_array[$crtres[$key1]['language']]);
                $crtres[$key1]['category'] = utf8_encode($this->category_array[$crtres[$key1]['category']]);


                if ($crtres[$key1]['participate_id'] != '')
                {
                    $marksdetails = $articleprocess_obj->getMarksByUser($crtres[$key1]['participate_id'], $userId);
                    if ($marksdetails != 'NO')
                        $crtres[$key1]['marks'] = $marksdetails[0]['marks'];
                    $parts = $participate_obj->getParticipateDetails($crtres[$key1]['participate_id']);
                    if ($parts != 'NO')
                        $users = $user_obj->getAllUsersDetails($parts[0]['user_id']);


                    $crtres[$key1]['writer'] = $users[0]['first_name'] . " " . $users[0]['last_name'];
                    $crtres[$key1]['userId'] = $userId;

                }
            }
        }
        // print_r($res); exit;

        $crtstatsarticles = $crtres;


        $data = '<table id="correctormissions" class="table table-bordered table-striped table_vam"  >
            <thead>
            <tr>
                <th>Nom de l\'article</th>
                <th>Nom de l\'annonce</th>
                <th>Nom du client</th>
                <th>Etat</th>
                <th>R&eacute;dacteur</th>
                <th>Chef de projet</th>
                <th>Langue</th>
                <th>Cat&eacute;gories</th>
                <th>Note donn&eacute;e au r&eacute;dacteur</th>
            </tr>
            </thead>
            <tbody>';
        if($crtstatsarticles !== 'NO') {
            for ($j = 0; $j < count($crtstatsarticles); $j++) {
                $temp1="";
                $temp2="";
                $temp1 = ($crtstatsarticles[$j]['writer'] != '') ? $crtstatsarticles[$j]['writer'] : '-';

                if($crtstatsarticles[$j]['marks'] != '')
                    $temp2 = ($crtstatsarticles[$j]['marks'] / 10) . ' <a class="hint--bottom hint--info" data-hint="Commenter" data-target="#markscomments" data-toggle="modal"  name="send_comment"  href="/user/recentcommentsmarks?partid=' . $crtstatsarticles[$j]['id'] . '&type=writer&userid=' . $userId . '" ><i class="splashy-comments_reply"></i></a>';
                else
                    $temp2 = 'Nill';

                $data .= '<tr>
                <td>' . utf8_encode($crtstatsarticles[$j]['title']) . '</td>
                <td>' . utf8_encode($crtstatsarticles[$j]['deliveryName']) . '</td>
                <td>' . utf8_decode(($crtstatsarticles[$j]['client'] != '') ? $crtstatsarticles[$j]['client'] : '-') . '</td>
                <td>' . utf8_decode(($crtstatsarticles[$j]['lateststatus'] != '') ? $crtstatsarticles[$j]['lateststatus'] : '-') . '</td>
                <td>' . $temp1 . '</td>
                <td>' . utf8_decode($crtstatsarticles[$j]['projectManager']) . '</td>
                <td>' . utf8_decode($crtstatsarticles[$j]['language']) . '</td>
                <td>' . utf8_decode(($crtstatsarticles[$j]['category'] != '') ? $crtstatsarticles[$j]['category'] : '-') . '</td>
                <td>' .$temp2. '</td>
            </tr>';
            }
        }
            $data .='</tbody>
        </table>';
        echo $data;
    }
    public function loadStatisticAction()
    {
        $user_obj = new Ep_User_User();
        $changelog_obj = new Ep_User_ProfileChangeLog();
        $experience_obj = new EP_User_ContributorExperience();
        $mail_obj = new Ep_Message_AutoEmails();
        $userId = $this->_request->getParam('userId');
        $user_details = $user_obj->getContributordetails($userId);
        /* *getting User expeience details**/
        $jobDetails = $experience_obj->getExperienceDetails($userId, 'job');
        if ($jobDetails != "NO")
            $this->_view->jobDetails = $jobDetails;
        $educationDetails = $experience_obj->getExperienceDetails($userId, 'education');


        if ($educationDetails != "NO")
            $this->_view->educationDetails = $educationDetails;
        /* *iNOVICE inFO ***/
        $this->_view->payment_type = $user_details[0]['payment_type'];
        $this->_view->pay_info_type = $user_details[0]['pay_info_type'];
        $this->_view->SSN = $user_details[0]['SSN'];
        $this->_view->company_number = $user_details[0]['company_number'];
        $this->_view->vat_check = $user_details[0]['vat_check'];
        $this->_view->VAT_number = $user_details[0]['VAT_number'];
        /* *Paypal and RIB info**/
        $this->_view->paypal_id = $user_details[0]["paypal_id"];
        $RIB_ID = explode("|", $user_details[0]["rib_id"]);
        if (($user_details[0]['pay_info_type'] == 'out_france' || $user_details[0]['country'] != 38) && count($RIB_ID) == 2) {
            $this->_view->rib_id_6 = $RIB_ID[0];
            $this->_view->rib_id_7 = $RIB_ID[1];
        } else {
            $this->_view->rib_id_1 = $RIB_ID[0];
            $this->_view->rib_id_2 = $RIB_ID[1];
            $this->_view->rib_id_3 = $RIB_ID[2];
            $this->_view->rib_id_4 = $RIB_ID[3];
            $this->_view->rib_id_5 = $RIB_ID[4];
        }
        //////edit contributor////////////////////////////////////
        $this->_view->user_detail = $user_details;   //echo "<pre>";print_r($user_details); exit;

        $this->_view->dob = date('d-m-Y', strtotime($user_details[0]['dob']));
        $this->_view->self_details = utf8_encode($user_details[0]['self_details']);
        $this->_view->stats = $_GET['stats'];
        $this->_view->loguser = $this->adminLogin->userId;
        $this->_view->category_more = unserialize($user_details[0]['category_more']);
        $this->_view->language_more = unserialize($user_details[0]['language_more']);


        //////view contributor////////////////////////////////////
        $this->_view->ep_contrib_profile_language_more = explode(",", $user_details[0]['language_more']);
        $this->_view->ep_contrib_profile_category = explode(",", $user_details[0]['favourite_category']);
        $this->_view->self_details = utf8_encode($user_details[0]['self_details']);
        $this->_view->stats = $_GET['stats'];
        $this->_view->loguser = $this->adminLogin->userId;
        $contrib_picture_path = "/home/sites/site5/web/FO/profiles/contrib/pictures/" . $user_details[0]['user_id'] . "/" . $user_details[0]['user_id'] . "_h.jpg";
        if (file_exists($contrib_picture_path))
            $contrib_picture_path = "http://ep-test.edit-place.com/FO/profiles/contrib/pictures/" . $user_details[0]['user_id'] . "/" . $user_details[0]['user_id'] . "_h.jpg";
        else
            $contrib_picture_path = "http://ep-test.edit-place.com/FO/images/Contrib/profile-img-def.png";
        $this->_view->user_pic = $contrib_picture_path;

        $expCat = explode(',', preg_replace("/\([^)]+\)/", "", $this->unserialisearray($user_details[0]['category_more'])));
        preg_match_all('#\((.*?)\)#', $this->unserialisearray($user_details[0]['category_more']), $match);

        foreach ($expCat as $key => $value) {
            $impCat[] = $this->category_array[$value] . '(' . $match[1][$key] . ')';
        }
        $user_details[0]['category_more'] = implode(',', $impCat);
        unset($impCat);

        $user_details[0]['language_more'] = $this->unserialisearray($user_details[0]['language_more']);
        $workexpDetails = $user_obj->getExperienceDetails($user_details[0]['user_id'], 'job');
        if ($workexpDetails != "NO") {
            $ecnt = 0;
            foreach ($workexpDetails as $workexp) {
                $workexpDetails[$ecnt]['start_date'] = date('FY', strtotime($workexp['from_year'] . "-" . $workexp['from_month']));
                if ($workexp['still_working'] == 'yes')
                    $workexpDetails[$ecnt]['end_date'] = 'Actuel';
                else
                    $workexpDetails[$ecnt]['end_date'] = date('FY', strtotime($workexp['to_year'] . "-" . $workexp['to_month']));
                $ecnt++;
            }
            $this->_view->educationDetailsview = $workexpDetails;
        }
        //echo "<pre>"; print_r($user_details);    exit;
        $this->_view->user_detail = $user_details;
        $this->_view->country_name = $this->country_array[$user_details[0]['country']];
        $this->_view->ep_contrib_profile_language_more = explode(",", $user_details[0]['language_more']);
        $this->_view->ep_contrib_profile_category = explode(",", $user_details[0]['favourite_category']);
        $this->_view->profession = $this->profession_array[$user_details[0]['profession']];
        $this->_view->language = $this->language_array[$user_details[0]['language']];
        $this->_view->nationality = $this->nationality_array[$user_details[0]['nationality']];
        $this->_view->self_details = utf8_encode(strip_tags($user_details[0]['self_detailss']));
        //lang_more str
        $language_more = "";
        if ($user_details[0]['language_more'] != NULL) {
            $lang_more = explode(",", $user_details[0]['language_more']);

            if (count($lang_more) > 0) {
                for ($l = 0; $l < count($lang_more); $l++) {
                    $language_more .= $this->language_array[$lang_more[$l]];

                    if ($l != count($lang_more) - 1)
                        $language_more .= ",";
                }
            }
        }
        $this->_view->language_more1 = $language_more;
        //fav category str
        $favourite_category = "";
        if ($user_details[0]['favourite_category'] != NULL) {
            $fav_cat = explode(",", $user_details[0]['favourite_category']);

            if (count($fav_cat) > 0) {
                for ($l = 0; $l < count($fav_cat); $l++) {
                    $favourite_category .= $this->category_array[$fav_cat[$l]];

                    if ($l != count($fav_cat) - 1)
                        $favourite_category .= ",";
                }
            }
        }
        $this->_view->favourite_category = $favourite_category;
        $eduarray = array("1" => "Bac +1", "2" => "Bac +2", "3" => "Bac +3", "4" => "Bac +4", "5" => "Bac +5", "6" => "Bac+5 et plus");
        $this->_view->education = $eduarray[$user_details[0]['education']];
        /*added by naseer on 04-11-2015*/
        //fetch new fields "software_list"//
        $software_array = $this->_arrayDb->loadArrayv2("EP_SOFTWARE_LIST", $this->_lang);
        //explodig all the software list and saing in multidimentaional array for later use//
        foreach ($software_array as $k => $v) {
            $software_array[$k] = explode("-", $v);
        }
        $this->_view->ep_software_array = $software_array;
        //exploding and saving the values since i was imploded at the time of insertion//
        $software_list_temp = explode("###$$$###", $user_details[0]['software_list']);
        for ($i = 0; $i < count($software_list_temp); $i++) {
            $software_list[$i] = explode('|', $software_list_temp[$i]);
        }
        $this->_view->software_list = $software_list;
        $this->_view->software_list_count = count($software_list);//saving the count for later use in phtml file//
        /* end of added by naseer on 04-11-2015*/

        /////////////contributor statistics /////////////////////
        $stats_params = $this->_request->getParams();
        $user_obj = new EP_User_Contributor();
        $participate_obj = new EP_Participation_Participation();
        $params['userid'] = $userId;

        /////////////writer graph part ///////////
        $params['userid'] = $userId;
        $marksforarts = $participate_obj->getMarksWriterGraph($params, 'writer'); //print_r($marksforarts); exit;
        if ($marksforarts != 'NO') {

            $this->_view->dates = "var d2 = [
             ";
            if ($marksforarts)
                foreach ($marksforarts[0] as $key => $val) {
                    //echo "<pre>".$key = substr($key, 0, -3);
                    $key = substr($key, 0, -3);
                    if ($val == '')
                        $val = 0;
                    $this->_view->dates .= "[new Date('" . $key . "').getTime()," . $val . "],
                 ";
                }
            $this->_view->dates .= "];
             ";  //exit;
            //  echo $this->_view->dates; exit;
        } else {
            $this->_view->dates = "var d2 = [ " . date('Y-m') . " ];";
        }
        /////////end of graph /////////////////////////
        /////////////corrector graph part ///////////
        $crtmarksforarts = $participate_obj->getMarksWriterGraph($params, 'corrector'); //print_r($marksforarts); exit;
        if ($crtmarksforarts != 'NO') {
            $this->_view->crtdates = "var d2 = [
             ";
            if ($crtmarksforarts)
                foreach ($crtmarksforarts[0] as $key => $val) {
                    //echo "<pre>".$key = substr($key, 0, -3);
                    $key = substr($key, 0, -3);
                    if ($val == '')
                        $val = 0;
                    $this->_view->crtdates .= "[new Date('" . $key . "').getTime()," . $val . "],
                 ";
                }
            $this->_view->crtdates .= "];
             ";  //exit;
        } else {
            $this->_view->crtdates = "var d2 = [ " . date('Y-m') . " ];";
        }
        /////////end of corrector graph /////////////////////////
        $noarts = $participate_obj->getParticipantsStatistics($params);
        $clientnamesarray = $participate_obj->getParticipantClientLIst($params);
        if ($clientnamesarray != 'NO') {
            foreach ($clientnamesarray AS $key => $value) {
                $clientnames[$key] = $clientnamesarray[$key]['company_name'];
            }
            $client_list = implode(",", $clientnames);
        } else
            $client_list = 0;
        $marks = 0;
        if ($noarts != '') {
            $selected_arts = $noarts[0]['selectedCount'];
            $refused_arts = $noarts[1]['refusedCount'];
            $proccesing_arts = $noarts[2]['inprocessCount'];
            $total_parts =  $noarts[3]['totalCount'];
            $published_arts = $noarts[4]['publishedCount'];
            $republished = $noarts[5]['republishedCount'];
            $arts_notsubmited = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['tempreasons'] != '') {
                $tempreasons = $this->formRefusalReasons($noarts[7]['tempreasons'], 'frequent');
                $freqtempreason = $tempreasons[0];
                $freqtempreasoncount = $tempreasons[1];
            } else {
                $freqtempreason = '';
                $freqtempreasoncount = 0;
            }
            //$total_refusalreasons = $noarts[8]['totaltemplateCount'];  // total count of refusal reasons
            $total_temp_refusalreasons = ($noarts[7]['tempreasons'] != "") ? count( explode(",",$noarts[7]['tempreasons'])) : '0';//condition to fetch the count of the array
            $total_prem_refusalreasons = ($noarts[9]['permreason'] != "") ? count( explode(",",$noarts[9]['permreason']) ) : '0';//condition to fetch the count of the array
            if ($clientnamesarray != 'NO')
                $num_clients = count($clientnamesarray);
            else
                $num_clients = 0;
            if ($noarts[9]['permreason'] != '') {
                $permreasons = $this->formRefusalReasons($noarts[9]['permreason'], 'frequent');
                $freqpermreason = utf8_encode($permreasons[0]);
                $freqpermreasoncount = $permreasons[1];
            } else {
                $freqpermreason = '';
                $freqpermreasoncount = 0;
            }

        }
        /////////////////////////////
        $crtparticipate_obj = new Ep_Participation_CorrectorParticipation();
        $noarts = $crtparticipate_obj->getCorrectorParticipantsStatistics($params);
        $writersarray = $crtparticipate_obj->getCrtParticipantWtitersLIst($params); // print_r($noarts);exit;

        $marks = 0;
        if ($noarts != 'NO') {
            $crtselected_arts = $noarts[0]['selectedCount'];
            $crtrefused_arts = $noarts[1]['refusedCount'];
            $crtproccesing_arts = $noarts[2]['inprocessCount'];
            $crttotal_parts = $noarts[3]['totalCount'];
            $crtpublished_arts = $noarts[4]['publishedCount'];
            $crtrepublished = $noarts[5]['republishedCount'];
            $crtarts_notsubmited = $noarts[6]['notsubmittedCount'];
            ////get the reasons /////
            if ($noarts[7]['crttempreasons'] != '') {
                $tempreasons = $this->formRefusalReasons($noarts[7]['crttempreasons'], 'frequent');
                $freqcrttempreason = $tempreasons[0];
                $freqcrttempreasoncount = $tempreasons[1];
            } else {
                $freqcrttempreason = '';
                $freqcrttempreasoncount = 0;
            }

            //$crttotal_refusalreasons = $noarts[8]['totaltemplateCount'];  // total count of refusal reasons
            $crttotal_temp_refusalreasons = ($noarts[7]['crttempreasons'] != "") ? count( explode(",",$noarts[7]['crttempreasons'])) : '0';//condition to fetch the count of the array
            $crttotal_prem_refusalreasons = ($noarts[9]['crtpermreasons'] != "") ? count( explode(",",$noarts[9]['crtpermreasons']) ) : '0';//condition to fetch the count of the array
            $crtnum_writers = count($writersarray);
            if ($noarts[9]['crtpermreasons'] != '') {
                $permreasons = $this->formRefusalReasons($noarts[9]['crtpermreasons'], 'frequent');
                $freqcrtpermreason = utf8_encode($permreasons[0]);
                $freqcrtpermreasoncount = $permreasons[1];
            } else {
                $freqcrtpermreason = '';
                $freqcrtpermreasoncount = 0;
            }
        }
        $language_array = $this->language_array;
        $category_array = $this->category_array;
        //to fecth the langauge and category which has marks//
        $lang_category_mark = $participate_obj->getLangCategory($userId);
        $lang_category_mark = ( is_array($lang_category_mark) ) ? iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($lang_category_mark)), 0) : '';//merging of multidimentioanl array to single dimensional array//
        //fecth the langauge and category realted to Statistiques//print_r($lang_category_mark);
        $lang_category_statistiques = $participate_obj->getLangCategoryStatis($userId);
        $lang_category_statistiques = ( is_array($lang_category_statistiques) ) ? iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($lang_category_statistiques)), 0) : '';//merging of multidimentioanl array to single dimensional array//
        $crt_lang_category_statistiques = $crtparticipate_obj->getCrtLangCategoryStatis($userId);
        $crt_lang_category_statistiques = ( is_array($crt_lang_category_statistiques) ) ? iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($crt_lang_category_statistiques)), 0) : '';//merging of multidimentioanl array to single dimensional array//
        //data to be displayed//
        if($this->_request->getParam('type') === 'writer'){
            $data = '<div class="formSep">
            <div class="row-fluid">
                <div class="span4 form-inline">
                    <label class="span4">Filtrer par langue(s) </label>
                    <select  id=graph_language name=graph_language   placeholder="Select Langue" onchange="changeGraph(\'' . $userId . '\', \'writer\');">
                        <option value="0">Select Langue</option>';
    //                    {html_options  id=language options=$language_array selected=$language }
            foreach ($language_array as $key => $value) {
                $data .= ( in_array($key, $lang_category_mark) ) ? '<option value="' . $key . '">' . $value . '</option>' : '' ;
            }
            $data .= '        </select>
                </div>
                <div class="span4 form-inline">
                    <label class="span4">Filtrer par cat&eacute;gorie(s) </label>
                    <select  name="graph_category" id="graph_category" placeholder="select category" onchange="changeGraph(\'' . $userId . '\', \'writer\');">
                        <option value="">select category</option>';

            //{html_options options=$category_array selected=$user_detail[0].category}
            foreach ($category_array as $key => $value) {
                $data .= ( in_array($key, $lang_category_mark) ) ? '<option value="' . $key . '">' . $value . '</option>' : '' ;
            }
            $data .= '
                    </select>
                </div>
                <div class="span4 form-inline">
                    <label class="span4">To Date </label>
                    <input type="text" autocomplete="off" name="graph_date" onchange="changeGraph(\'' . $userId . '\', \'writer\');" placeholder="select date" id="dp1" readonly data-date-format="dd-mm-yyyy" value="' . date('d-m-Y') . '"/>
                </div>
            </div>
        </div>
        <div class="formSep" id="writergraph">
            <div class="row-fluid">
                <div class="span6" style="width: 700px;">
                    <h3 class="heading pull-center">Mark</h3>
                    <div id="monthlyGraph" style="height:270px;width:100%;margin:15px auto 0"></div>
                    <!-- <div id="fl_m" style="height:270px;width:100%;margin:15px auto 0"></div>-->
                </div>
            </div>
        </div>
        <div class="formSep">
            <div class="row-fluid">
                <div class="span6">
                    <h3 class="heading pull-center">Moyennes</h3>
                    <table class="table table-striped table-bordered table-condensed span9">
                        <tr>
                            <td class="span3"><span class="pull-right alpbold">G&eacute;n&eacute;rale :</span></td>
                            <td class="span9"><span class="pull-left ">
                                        <select  name="moy_user_type" id="moy_user_type" onchange="getMoyennes(\'' . $userId . '\')">
                                            <option value="0">ALL</option>
                                            <option value="1">Correcteur FO</option>
                                            <option value="2">Edit-Place (internal correction)</option>
                                        </select>
                                    </span><span class="alert alert-error pull-right" style="padding:10px;margin-bottom:0px;font: bold;" id="moy_stats"></span></td>
                        </tr>
                        <tr>
                            <td class="span3"><span class="pull-right alpbold">Langue(s) : </span></td>
                            <td class="span9"><span class="pull-left ">
                                        <select  name="moy_language" id="moy_language" placeholder="select language" onchange="getMoyennes(\'' . $userId . '\')">
                                            <option value="0">select language</option>';
    //                                        {html_options  id=language options=$language_array selected=$language }
            foreach ($language_array as $key => $value) {
                $data .= '<option value="' . $key . '">' . $value . '</option>';
            }
            $data .= '
                                        </select>
                                    </span><span class="alert alert-info pull-right" style="padding:10px;margin-bottom:0px;font: bold;" id="moy_lang_stats"></span></td>
                        </tr>
                        <tr>
                            <td class="span3"><span class="pull-right alpbold">Cat&eacute;gorie(s) :</span></td>
                            <td class="span9"><span class="pull-left ">
                                        <select  name="moy_category" id="moy_category" placeholder="select category" onchange="getMoyennes(\'' . $userId . '\')">
                                            <option value="0">select category</option>';

            //{html_options options=$category_array selected=$user_detail[0].language}
            foreach ($category_array as $key => $value) {
                $data .= '<option value="' . $key . '">' . $value . '</option>';
            }
            $data .= '
                                        </select>
                                    </span><span class="alert alert-success pull-right" style="padding:10px;margin-bottom:0px;font: bold;" id="moy_categ_stats"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="formSep">
            <h3 class="heading pull-center bold">Statistiques</h3>
            <div class="row-fluid">

                <div class="span6 form-inline">
                    <label class="span4">Filtrer par langue(s)</label>
                    <select  name="stats_language" id="stats_language" placeholder="select language" onchange="getStatistics(\'' . $userId . '\')">
                        <option value="">select language</option>';
            //{html_options  options=$language_array  }
            foreach ($language_array as $key => $value) {
                $data .= ( in_array($key, $lang_category_statistiques) ) ? '<option value="' . $key . '">' . $value . '</option>' : '' ;
            }
            $data .= '

                    </select>
                </div>
                <div class="span6 form-inline">
                    <label class="span4">Filtrer par cat&eacute;gorie(s)</label>
                    <select  name="stats_category" id="stats_category" placeholder="select category" onchange="getStatistics(\'' . $userId . '\')">
                        <option value="">select category</option>';
            //{html_options options=$category_array }
            foreach ($category_array as $key => $value) {
                $data .= ( in_array($key, $lang_category_statistiques) ) ? '<option value="' . $key . '">' . $value . '</option>' : '' ;
            }
            $data .= '

                    </select>
                </div>
            </div>
        </div>
        <div id="calculated_stats"><br />
            <label class="span5 alert alert-info" >Nombre de s&eacute;lections / total participations : <span id="selected">
                ' . $selected_arts . '/' . $total_parts . ' (' .
                    (($selected_arts && $total_parts != "" ) ? round($selected_arts / $total_parts *100,2) : '0')
                    . '%)'
                    . '</span></label>
            <label class="span5 alert alert-danger" >Nombre d\'articles valid&eacute;s : <span id="validated">
                    ' . $published_arts . '/' . $total_parts . ' (' .
                    (($published_arts && $total_parts != "") ? round($published_arts / $total_parts *100,2) : '0')
                    . '%)'
                    . '</span></label>
            <label class="span5 alert alert-success" >Nombre d\'articles refus&eacute;s pour resoumission : <span id="republished">
                ' . $refused_arts . '/' . $total_parts . ' (' .
                (($refused_arts && $total_parts != "") ? round($refused_arts / $total_parts *100,2) : '0')
                . '%)'
                . '</span></label>
            <label class="span5 alert alert-success" id="">Raison du refus pour resoumission la plus fr&eacute;quente : <span id="refusedreasons">
                    ' . $freqtempreason . '&nbsp;' . $freqtempreasoncount . '/' . $total_temp_refusalreasons . ' (' .
                    (($freqtempreasoncount && $total_temp_refusalreasons != "") ? round($freqtempreasoncount / $total_temp_refusalreasons *100,2) : '0')
                    . '%)'
                    . '<a class="hint--bottom hint--info" data-hint="Refuse reasons"  data-target="#refusereasonslist" data-toggle="modal"  name="send_comment"  href="/user/refused-reasons?userid=' . $userId . '&type=temp" >Voir plus</a></span></label>
                <label class="span5 alert alert-info">Nombre d\'articles refus&eacute;s :<span id="refused">
                ' . $republished . '/' . $total_parts . ' (' .
                (($republished && $total_parts != "") ? round($republished / $total_parts *100,2) : '0')
                . '%)'
                . '
                </span></label>
            <label class="span5 alert alert-gebo">Raison du refus la plus fr&eacute;quente  : <span id="refusedreasonscrt">
             ' . $freqpermreason . '&nbsp;' . $freqpermreasoncount . '/' . $total_prem_refusalreasons . ' (' .
                    (($freqpermreasoncount && $total_prem_refusalreasons != "") ? round($freqpermreasoncount / $total_prem_refusalreasons *100,2) : '0')
                    . '%)'
                    . '<a class="hint--bottom hint--info" data-hint="Refuse reasons corrector"  data-target="#refusereasonslist" data-toggle="modal"  name="send_comment"  href="/user/refused-reasons?userid=' . $userId . '&type=perm" >Voir plus</a></span></label>
            <label class="span5 alert alert-success">Nombre de retards :<span id="notsubmited">
            ' . $arts_notsubmited . '/' . $total_parts . ' (' .
                    (($arts_notsubmited && $total_parts != "") ? round($arts_notsubmited / $total_parts *100,2) : '0')
                    . '%)'
                    . '</span></label>
            <label class="span5 alert alert-info">Nombre de clients avec lesquels il a travaill&eacute; :<span id="numclients"> ' . $num_clients . '</span></label>
            <label class="span5 alert alert-info">Noms des clients : <span id="clientlist">' . $client_list . '</span></label>
        </div>';

    }
        elseif($this->_request->getParam('type') === 'corrector'){
            $data = '<div class="formSep">
        <div class="row-fluid">
            <div class="span4 form-inline">
                <label class="span4">Filtrer par langue(s) </label>
                <select  id=crtgraph_language name=crtgraph_language   placeholder="Select Langue" onchange="changeGraph(\''.$userId.'\', \'corrector\');">
                    <option value="0">Select Langue</option>';
            //                                        {html_options  id=language options=$language_array selected=$language }
            foreach ($language_array as $key => $value) {
                $data .= ( in_array($key, $lang_category_mark) ) ? '<option value="' . $key . '">' . $value . '</option>' : '' ;
            }
            $data .= '</select>
            </div>
            <div class="span4 form-inline">
                <label class="span4">Filtrer par cat&eacute;gorie(s) </label>
                <select  name="crtgraph_category" id="crtgraph_category" placeholder="select category" onchange="changeGraph(\''.$userId.'\', \'corrector\');">
                    <option value="">select category</option>';
            //{html_options options=$category_array }
            foreach ($category_array as $key => $value) {
                $data .= ( in_array($key, $lang_category_mark) ) ? '<option value="' . $key . '">' . $value . '</option>' : '' ;
            }
            $data .= '</select>
            </div>
            <div class="span4 form-inline">
                <label class="span4">To Date </label>
                <input type="text" autocomplete="off" name="crtgraph_date" onchange="changeGraph(\''.$userId.'\', \'corrector\');" placeholder="select date" id="dp" readonly data-date-format="dd-mm-yyyy" value="'.date('d-m-Y').'"/>
            </div>
        </div>
    </div>
    <div class="formSep" id="correctorgraph">
        <div class="row-fluid">
            <div class="span6" style="width: 700px;"><!--id="cstmm"-->
                <h3 class="heading pull-center">Mark</h3>
                <div id="crtmonthlyGraph" style="height:270px;width:100%;margin:15px auto 0"></div>
                <!-- <div id="fl_m" style="height:270px;width:100%;margin:15px auto 0"></div>-->
            </div>

        </div>
    </div>
    <div class="formSep">
        <div class="row-fluid">
            <div class="span6">
                <h3 class="heading pull-center">Moyennes</h3>
                <table class="table table-striped table-bordered table-condensed span9">
                    <tr>
                        <td class="span3"><span class="pull-right alpbold">G&eacute;n&eacute;rale :</span></td>
                        <td class="span9"><span class="pull-left ">
                                    <select  name="crtmoy_user_type" id="crtmoy_user_type" onchange="getCrtMoyennes(\''.$userId.'\')">
                                        <option value="0">ALL</option>
                                        <option value="1">Correcteur FO</option>
                                        <option value="2">Edit-Place (internal correction)</option>
                                    </select>
                                </span><span class="alert alert-error pull-right" style="padding:10px;margin-bottom:0px;font: bold;" id="crtmoy_stats"></span></td>
                    </tr>
                    <tr>
                        <td class="span3"><span class="pull-right alpbold">Langue(s) : </span></td>
                        <td class="span9"><span class="pull-left ">
                                    <select  name="crtmoy_language" id="crtmoy_language" placeholder="select language" onchange="getCrtMoyennes(\''.$userId.'\')">
                                        <option value="0">select language</option>';
            //                                        {html_options  id=language options=$language_array selected=$language }
            foreach ($language_array as $key => $value) {
                $data .= '<option value="' . $key . '">' . $value . '</option>';
            }
            $data .= '</select>
                                </span><span class="alert alert-info pull-right" style="padding:10px;margin-bottom:0px;font: bold;" id="crtmoy_lang_stats"></span></td>
                    </tr>
                    <tr>
                        <td class="span3"><span class="pull-right alpbold">Cat&eacute;gorie(s) :</span></td>
                        <td class="span9"><span class="pull-left ">
                                    <select  name="crtmoy_category" id="crtmoy_category" placeholder="select category" onchange="getCrtMoyennes(\''.$userId.'\')">
                                        <option value="0">select category</option>';
            //{html_options options=$category_array }
            foreach ($category_array as $key => $value) {
                $data .= '<option value="' . $key . '">' . $value . '</option>';
            }
            $data .= '
                                    </select>
                                </span><span class="alert alert-success pull-right" style="padding:10px;margin-bottom:0px;font: bold;" id="crtmoy_categ_stats"></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="formSep">
        <h3 class="heading pull-center bold">Statistiques</h3>
        <div class="row-fluid">

            <div class="span6 form-inline">
                <label class="span4">Filtrer par langue(s)</label>
                <select  name="crtstats_language" id="crtstats_language" placeholder="select language" onchange="getCrtStatistics(\''.$userId.'\')">
                    <option value="">select language</option>';
            //                                        {html_options  id=language options=$language_array selected=$language }
            foreach ($language_array as $key => $value) {
                $data .= ( in_array($key, $crt_lang_category_statistiques) ) ? '<option value="' . $key . '">' . $value . '</option>' : '' ;
            }
            $data .= '</select>
            </div>
            <div class="span6 form-inline">
                <label class="span4">Filtrer par cat&eacute;gorie(s)</label>
                <select  name="crtstats_category" id="crtstats_category" placeholder="select category" onchange="getCrtStatistics(\''.$userId.'\')">
                    <option value="">select category</option>';
            //{html_options options=$category_array }
            foreach ($category_array as $key => $value) {
                $data .= ( in_array($key, $crt_lang_category_statistiques) ) ? '<option value="' . $key . '">' . $value . '</option>' : '' ;
            }
            $data .= '
                </select>
            </div>
        </div>
    </div>
    <div id="calculated_stats2"><br />
            <label class="span4 alert alert-info" >Nombre de s&eacute;lections / total participations : <span id="crtselected">
            '.$crtselected_arts.'/'.$crttotal_parts. ' (' .
                    ( ($crtselected_arts && $crttotal_parts != "") ? round($crtselected_arts/$crttotal_parts *100,2) : '0')
                        . '%)'
                        . '</span></label>
            <label class="span6 alert alert-danger" >Nombre d\'articles valid&eacute;s : <span id="crtvalidated">
            '.$crtpublished_arts.'/'.$crttotal_parts. ' (' .
                    ( ($crtpublished_arts && $crttotal_parts != "") ? round($crtpublished_arts/$crttotal_parts *100,2) : '0')
                        . '%)'
                    . '</span></label>
            <label class="span4 alert alert-success" >Nombre d\'articles refus&eacute;s pour resoumission : <span id="crttimeout">
            '.$crtrepublished.'/'.$crttotal_parts. ' (' .
                    ( ($crtrepublished && $crttotal_parts != "") ? round($crtrepublished/$crttotal_parts *100,2) : '0')
                        . '%)'
            . '</span></label>
            <label class="span6 alert alert-success" id="">Raison du refus pour resoumission la plus fr&eacute;quente :<span id="crtrefusedreasons">
            '.$freqcrttempreason.' '.$freqcrttempreasoncount.'/'.$crttotal_temp_refusalreasons. ' (' .
                    ( ($freqcrttempreasoncount && $crttotal_temp_refusalreasons != "") ? round($freqcrttempreasoncount/$crttotal_temp_refusalreasons *100,2) : '0')
                        . '%)'
                        . '</span><a class="hint--bottom hint--info" data-hint="Refuse reasons"  data-target="#refusereasonslist" data-toggle="modal"  name="send_comment"  href="/user/refused-reasons?userid='.$userId.'&type=crttemp" >Voir plus</a></label>
            <label class="span4 alert alert-info">Nombre d\'articles refus&eacute;s :<span id="crtrefused">
            '.$crtrefused_arts.'/'.$crttotal_parts. ' (' .
                    ( ($crtrefused_arts && $crttotal_parts != "") ? round($crtrefused_arts/$crttotal_parts *100,2) : '0')
                        . '%)'
                        . '</span></label>
            <label class="span6 alert alert-gebo">Raison du refus la plus fr&eacute;quente :<span id="crtrefusedreasonspercent">
                        '.$freqcrtpermreason.' '.$freqcrtpermreasoncount.'/'.$crttotal_prem_refusalreasons. ' (' .
                    ( ($freqcrtpermreasoncount && $crttotal_prem_refusalreasons != "") ? round($freqcrtpermreasoncount/$crttotal_prem_refusalreasons *100,2) : '0')
                        . '%)'
                . '</span><a class="hint--bottom hint--info" data-hint="Refuse reasons"  data-target="#refusereasonslist" data-toggle="modal"  name="send_comment"  href="/user/refused-reasons?userid='.$userId.'&type=crtperm" >Voir plus</a></label>
            <label class="span4 alert alert-success">Nombre de retards :<span id="crtnotsubmited">
            '.$crtarts_notsubmited.'/'.$crttotal_parts. ' (' .
                    ( ($crtarts_notsubmited && $crttotal_parts != "") ? round($crtarts_notsubmited/$crttotal_parts *100,2) : '0')
                        . '%)'
                        . '</span></label>
            <label class="span6 alert alert-info">Nombre de r&eacute;dacteurs not&eacute;s :<span id="crtnumwriters"> '.$crtnum_writers.' <a class="hint--bottom hint--info" data-hint="Writers List"  data-target="#writerlist" data-toggle="modal"  name="send_comment"  href="/user/writer-list?userid='.$userId.'" >Voir plus</a></span></label>
            </div>';
        }
        echo $data;
        exit;
    }
    public function loadQuizlogsAction(){
        $userId = $this->_request->getParam('userId');
        $quiz_obj = new Ep_Quizz_quizz();
        $quiz_details = $quiz_obj->getUserQuizDetails($userId);
        $data = '<table id="quizlogs_table" class="table table-bordered table-striped table_vam">
            <thead>
                <tr>
                    <th>Quizz Title</th>
                    <th>Total Questions</th>
                    <th>Questions Attempted</th>
                    <th>Answered Correct</th>
                    <th>Cutoff Score</th>
                    <th>Result</th>
            </tr>
            </thead>
            <tbody>';
        $count = count($quiz_details);
        for($i=0;$i<$count;$i++){
            $data .= '<tr>
                <td>'.utf8_encode($quiz_details[$i]['title']).'</td>
                <td>'.$quiz_details[$i]['quest_count'].'</td>
                <td>'.$quiz_details[$i]['num_total'].'</td>
                <td>'.$quiz_details[$i]['num_correct'].'( '.$quiz_details[$i]['percent'].'% )</td>
                <td>'.$quiz_details[$i]['correct_ans_count'].'</td>
                <td>'.( ($quiz_details[$i]['qualified'] == 'no') ? "Failed" : "Passed" ).'</td>
            </tr>';
        }

        $data .=  '</tbody></table>';
        echo $data;

    }
    /*end of added on 18.11.2015*/
    public function updateContribAjaxAction()
    {
        $params = $this->_request->getParams();
        $user_obj = new Ep_User_User();
        $userId = $this->_request->getParam('userId');
        //fetch the data before any changes are made//
        $old_data = $user_obj->getContributordetails($userId);
        if ($user_obj->checkCreateUserPlus($params['userId']) && $user_obj->checkCreateContributor($params['userId'])) {
            if ($this->_request->getParam('submit') == 'submit_contrib_bo_only') {
                //$user_obj->updateContribBoOnly($params);
                $uarr = array();
                $uarr['status'] = $params['status'];
                $uarr['profile_type'] = $params['profile_type'];
                //$uarr['subscribe'] = $params['subscribe'];
                //$uarr['alert_subscribe'] = $params['alert_subscribe'];
                if ($params['type2'] == 'yes') {
                    $uarr['type2'] = 'corrector';
                } else {
                    $uarr['type2'] = NULL;
                }
                $uarr['profile_type2'] = $params['profile_type2'];
                $uarr['blackstatus'] = $params['blackstatus'];
                $this->updateUserLogs($old_data[0], $uarr);//fucntion to insert userlogs if the old data is different from the request sent//
                $response1 = $user_obj->updateContribBoOnlyUser($userId, $uarr);

                $contribarr['contributortest'] = $params["contributortest"];
                if ($params["contributortest"] == "yes") {
                    $contribarr['contributortestcomment'] = utf8_decode($params["contributortestcomment"]);
                    $contribarr['contributortestmarks'] = $params["contributortestmarks"];
                }
                /*edited by naseer on 26.11.2015*/
                $contribarr['translator'] = $params['translator'];
                $contribarr['translator_type'] = $params['translator_type'];
                $this->updateUserLogs($old_data[0], $contribarr);//fucntion to insert userlogs if the old data is different from the request sent//
                $response2 = $user_obj->updateContribBoOnlyContrib($userId, $contribarr);
                $data =  ($response1 && $response2) ? "1"/*true*/ : '0'/*false*/;
            } elseif ($this->_request->getParam('submit') == 'submit_contrib_user_basic') {
                //$user_obj->updateContribUserBasic($params);
                $userarr = array();
                //$userarr['first_name']=$params['first_name'];
                //$userarr['last_name']=$params['last_name'];
                $userarr['address'] = utf8_decode($params['address']);
                $userarr['city'] = utf8_decode($params['city']);
                $userarr['zipcode'] = $params['zipcode'];
                $userarr['country'] = utf8_decode($params['country']);
                $userarr['phone_number'] = $params['phone_number'];
                $this->updateUserLogs($old_data[0], $userarr);//fucntion to insert userlogs if the old data is different from the request sent//
                $response1 = $user_obj->updateContribUserBasicUserPlus($userId, $userarr);

                $contribarr = array();
                $contribarr['dob'] = date('Y-m-d', strtotime($params['birth_date']));
                $contribarr['language'] = $params['language'];
                $contribarr['nationality'] = $params['nationality'];
                $contribarr['self_details'] = utf8_decode(nl2br($params['self_details']));
                $this->updateUserLogs($old_data[0], $contribarr);//fucntion to insert userlogs if the old data is different from the request sent//
                $response2 = $user_obj->updateContribUserBasicContributor($userId, $contribarr);

                $uarr = array();
                if ($params['subscribe'] !== '')
                    $uarr['subscribe'] = $params['subscribe'];
                if ($params['subscribe'] !== '')
                    $uarr['alert_subscribe'] = $params['alert_subscribe'];
                $this->updateUserLogs($old_data[0], $uarr);//fucntion to insert userlogs if the old data is different from the request sent//
                $response3 = $user_obj->updateContribUserBasicUser($userId, $uarr);
                $data = ($response1 && $response2 && $response3) ? "1"/*true*/ : '0'/*false*/;

            } elseif ($this->_request->getParam('submit') == 'submit_contrib_categories_and_lang') {
                if (count($params['ep_category']) > 0) {
                    $category_more = $params['ep_category'];
                    $category_sliders_more = $params['category_slider_more'];
                    foreach ($category_more as $key => $category) {
                        if ($category)
                            $moreCategories[$category] = str_replace("%", "", $category_sliders_more[$key]);
                    }
                    $contribarr['category_more'] = serialize($moreCategories);
                }
                if (count($params['language_more']) > 0) {
                    $language_more = $params['language_more'];
                    $lang_sliders_more = $params['lang_slider_more'];
                    foreach ($language_more as $key => $lang) {
                        if ($lang)
                            $moreLanguages[$lang] = str_replace("%", "", $lang_sliders_more[$key]);
                    }
                    $contribarr['language_more'] = serialize($moreLanguages);
                }
                $this->updateUserLogs($old_data[0], $contribarr);//fucntion to insert userlogs if the old data is different from the request sent//
                $response1 = $user_obj->updateContribCategoriesAndLang($userId, $contribarr);
                $data = ($response1 ) ? '1'/*true*/ : '0'/*false*/;
            } elseif ($this->_request->getParam('submit') == 'submit_contrib_experience') {
                $response1 = $this->updateExperienceDetails($params, 'job');
                $response2 =  $this->updateExperienceDetails($params, 'education');
                //echo ($response1 && $response2) ? "1"/*true*/ : '0'/*false*/;
                $data = '1';
            } elseif ($this->_request->getParam('submit') == 'submit_contrib_payment_info_form') {
                $contribarr = array();
                $contribarr['payment_type'] = $params["payment_type"];
                $contribarr['pay_info_type'] = $params["pay_info_type"];
                /*$contribarr['SSN'] = $params["ssn"];
                $contribarr['company_number'] = $params["company_number"];
                $contribarr['vat_check'] = $params["vat_check"];
                $contribarr['VAT_number'] = $params["VAT_number"]; */
                /* * Inserting Paypal and RIB info**/
                $contribarr['paypal_id'] = $params["paypal_id"];
                $userplus_obj = new Ep_User_UserPlus();
                $result = $userplus_obj->getUsersDetailsOnId($userId);
                $country = $result[0]['country'];
                if ($country == 38) {
                    $contribarr['rib_id'] = utf8_decode($params["rib_id_1"]) . "|" . utf8_decode($params["rib_id_2"]) . "|" . utf8_decode($params["rib_id_3"]) . "|" .
                        utf8_decode($params["rib_id_4"]) . "|" . utf8_decode($params["rib_id_5"]);
                } else {
                    $contribarr['rib_id'] = utf8_decode($params["rib_id_6"]) . "|" . utf8_decode($params["rib_id_7"]);
                }
                $this->updateUserLogs($old_data[0], $contribarr);//fucntion to insert userlogs if the old data is different from the request sent//
                $response1 = $user_obj->updateContribPaymentInfo($userId, $contribarr);
                $data = ($response1 ) ? '1'/*true*/ : '0'/*false*/;
            } elseif ($this->_request->getParam('submit') == 'submit_contrib_more_info_form') {
                $contribarr = array();
                $options_flag = $params['options_flag'];
                if ($options_flag == 'reg_check') {
                    $contribarr["options_flag"] = $params['options_flag'];
                    $contribarr["passport_no"] = $params['passport_no'];
                    $contribarr["id_card"] = $params['id_card'];

                } elseif ($options_flag == 'com_check') {
                    $contribarr["options_flag"] = $params['options_flag'];
                    $contribarr["com_name"] = utf8_decode($params['com_name']);
                    $contribarr["com_country"] = utf8_decode($params['com_country']);
                    $contribarr["com_address"] = utf8_decode($params['com_address']);
                    $contribarr["com_phone"] = $params['com_phone'];
                    $contribarr["com_city"] = utf8_decode($params['com_city']);
                    $contribarr["com_zipcode"] = $params['com_zipcode'];
                    $contribarr["com_siren"] = utf8_decode($params['com_siren']);
                    $contribarr["com_tva_number"] = utf8_decode($params['com_tva_number']);
                } elseif ($options_flag == 'tva_check') {
                    $contribarr["options_flag"] = $params['options_flag'];
                    $contribarr["siren_number"] = utf8_decode($params['siren_number']);
                    $contribarr["denomination_sociale"] = utf8_decode($params['denomination_sociale']);
                    $contribarr["tva_number"] = utf8_decode($params['tav_number']);
                }
                $this->updateUserLogs($old_data[0], $contribarr);//fucntion to insert userlogs if the old data is different from the request sent//
                $response1 = $user_obj->updateContribMoreInfo($userId, $contribarr);
                $data =  ($response1 ) ? '1'/*true*/ : '0'/*false*/;
            }
            $response_data = array("response"=>$data);
            echo json_encode($response_data);
            exit;
        } else {
            echo "error updating";
        }

    }

    /* added by naseer on 05-11-2015*/
    //this function will check if values have been edited and insert into userLogs table *if any//
    public function updateUserLogs($old_data, $new_data)
    {
        $updated_by = $this->adminLogin->userId;
        //arrays that will be used for log_type field//
        $profile = array('password', 'initial', 'first_name', 'last_name', 'dob', 'language', 'address', 'city', 'country', 'nationality', 'zipcode', 'phone_number', 'self_details', 'passport_no', 'id_card','translator');//edited on 26-11-2015 by naseer//
        $language_update = array('language_more');
        $category_update = array('category_more', 'favourite_category');
        $job_edu_update = array('title', 'institute', 'contract', 'from_month', 'from_year', 'still_working', 'to_month', 'to_year');
        $skill_update = array('software_list');
        $company_entrepreneur_update = array('tav_number', 'com_name', 'com_address', 'com_city', 'com_zipcode', 'com_country', 'com_siren', 'com_tva_number', 'com_phone', 'siren_number');
        $payment = array('paypal_id', 'bank_account_name', 'virement', 'rib_id');
        $payment_type = array('payment_type', 'options_flag');
        $subscription = array('alert_subscribe', 'subscribe');
        $onlybo = array('status', 'profile_type', 'type2', 'profile_type2', 'blackstatus', 'contributortest', 'contributortestcomment', 'contributortestmarks','translator_type');
        // end arrays that will be used for log_type field//
        $userlogs_obj = new Ep_User_UserLogs();
        foreach ($new_data as $key => $value) {
            // enterprise/updated_at doesnt matter if its chained//
            if ($old_data[$key] !== $value && $key !== 'updated_at' && $key !== 'entreprise' && $key !== 'identifier' && $key !== 'staus_self_details_update' && $key !== 'controller' && $key !== 'action' && $key !== 'module' && $key !== 'userId' && $key !== 'submit') {
                if (in_array($key, $profile))
                    $log_type = 'profile';
                elseif (in_array($key, $language_update))
                    $log_type = 'language_update';
                elseif (in_array($key, $category_update))
                    $log_type = 'category_update';
                elseif (in_array($key, $job_edu_update)) {
                    if ($new_data['type'] === 'job')
                        $log_type = 'job_update';
                    elseif ($new_data['type'] === 'education')
                        $log_type = 'edu_update';
                } elseif (in_array($key, $skill_update))
                    $log_type = 'skill_update';
                elseif (in_array($key, $company_entrepreneur_update))
                    $log_type = 'company_entrepreneur_update';
                elseif (in_array($key, $payment))
                    $log_type = 'payment';
                elseif (in_array($key, $payment_type))
                    $log_type = 'payment_type';
                elseif (in_array($key, $subscription))
                    $log_type = 'subscription';
                elseif (in_array($key, $onlybo))
                    $log_type = 'onlybo';
                else
                    $log_type = 'other';
                if($key === 'type2'){
                    $value = (trim($value) === "corrector") ? $value : '-';
                }
                //fetch user type from user table //
                $user_obj = new Ep_User_User();
                $user_type = $user_obj->getUserType($old_data['primary_id']);
                $data = array("user_id" => $old_data['primary_id'], "type" => $user_type, "old_value" => utf8dec($old_data[$key]), "new_value" => utf8dec($value), "log_type" => $log_type, "field" => $key, "updated_by" => $updated_by);
                $userlogs_obj->InsertLogs($data);
                /*echo "inserted: ======>>>";
                echo "Key: $key; old value : $old_data[$key] ;new Value: $value<br /><br />\n";*/
            }
        }
    }
    /* end of added by naseer on 05-11-2015*/

    /**Author:Thilagam**/
    /**Date:24/5/2016**/
    /**Function:To download the extracted list of Contributors**/
    public function downloadContributorXlsAction(){

            $contributors = array();
            $user_obj = new Ep_User_User();
            $contributors = $user_obj->downloadContributorXls();
            $_SESSION['contributors'] =  $contributors;
            echo "refresh the page to see table";
            exit;
    }
    public function viewDownloadContributorXlsAction()
    {
        ini_set('memory_limit', '-1');
        $user_obj = new Ep_User_User();
        $language = $this->_arrayDb->loadArrayv2("EP_LANGUAGES", $this->_lang);
        //$contributors = $user_obj->downloadContributorXls();
        $contributors = $_SESSION['contributors'] ;
        $lang_count = 0;
        $lang_list = array();
        for ($i = 0; $i < count($contributors); $i++) 
        {
            if ($contributors[$i]["language_more"] != '')
            {
                if ($contributors[$i]["language_more"] != '') 
                {
                    $laninfo = unserialize($contributors[$i]['language_more']);
                    if ($laninfo != '') 
                    {
                        foreach ($laninfo as $key1 => $value1) 
                        {
                            $lang_list[] = utf8_decode($this->language_array[$key1]);
                        }
                    }
                    $row = implode(',', array_unique($lang_list));
                } 
                else
                    $row = "-";
            }
            if($contributors[$i]['language'] != '')
            {
                foreach($language as $key=>$value)
                {
                    if($contributors[$i]['language'] == $key)
                    {
                       $contributors[$i]['language']=$value; 
                    }
                }
            }
            $contributors[$i]['language_more']=$row;
            if($contributors[$i]['language'] != " ")
            {
                array_push($lang_list,$contributors[$i]['language']);
            }
            $lang = array_unique($lang_list);
            $contributors[$i]['lang_count']=count($lang);
        }
        $file = 'excelFile-' . date("Y-M-D") . "-" . time() . '.xls';
        ob_start();
        $content = " ";
        $content .="<table border='1'>";
            $content .="<tr>";
                $content .="<th>Identifier</th>";
                $content .="<th>First Name</th>";
                $content .="<th>Last Name</th>";
                $content .="<th>Email ID</th>";
                $content .="<th>NativeLanguage</th>";
                $content .="<th>Other Languages</th>";
                $content .="<th>Total Languages</th>";
                $content .="<th>Number of Participation</th>";
                $content .="<th>Date of Last Participation</th>";
                $content .="<th>Royalties</th>";
                $content .="<th>Writer</th>";
                $content .="<th>Status</th>";
                $content .="<th>Translator</th>";
                $content .="<th>Status</th>";
                //$content .="<th>No of Selected Articles</th>";
                //$content .="<th>Date of Last Selected Date</th>";
                $content .="<th>Entrepreneur</th>";
                $content .="<th>Date of Account Created</th>";
                $content .="<th>Date of Last Visited</th>";
            $content .="</tr>";
            for($j=0;$j<count($contributors);$j++):
                $content .= "<tr>";
                    $content .= "<td>".$contributors[$j]['identifier']."</td>";
                    $content .= "<td>".utf8_decode($contributors[$j]['first_name'])."</td>";
                    $content .= "<td>".utf8_decode($contributors[$j]['last_name'])."</td>";
                    $content .= "<td>".$contributors[$j]['email']."</td>";
                    $content .= "<td>".utf8_decode($contributors[$j]['language'])."</td>";
                    $content .= "<td>".$contributors[$j]['language_more']."</td>";
                    $content .= "<td>".$contributors[$j]['lang_count']."</td>";
                    $content .= "<td>".$contributors[$j]['times']."</td>";
                    $content .= "<td>".$contributors[$j]['last']."</td>";
                    $content .= "<td>".$contributors[$j]['royalty']."</td>";
                    if($contributors[$j]['writer_preference'] == 'yes')
                    {
                        $content .= "<td>Yes</td>";
                        $content .= "<td>".$contributors[$j]['profile_type']."</td>";
                    }
                    else
                    {
                        $content .= "<td>No</td>";
                        $content .= "<td></td>";
                    }
                    if($contributors[$j]['translator'] == 'yes')
                    {
                        $content .= "<td>Yes</td>";
                        $content .= "<td>".$contributors[$j]['translator_type']."</td>";
                    }
                    else
                    {
                        $content .= "<td>No</td>";
                        $content .= "<td></td>";
                    }
                    //$content .= "<td>".$contributors[$j]['writer_preference']."</td>";
                    //$content .= "<td>".$contributors[$j]['profile_type']."</td>";
                    //$content .= "<td>".$contributors[$j]['translator']."</td>";
                    //$content .= "<td>".$contributors[$j]['translator_type']."</td>";
                    //$content.="<td>".$contributors[$j]['selected']."</td>";
                    //$content.="<td>".$contributors[$j]['select_date']."</td>";
                    if($contributors[$j]['tva_number'])
                    {
                        $content.="<td>Yes</td>";
                    }
                    else
                    {
                        $content.="<td>No</td>";
                    }
                    $content .= "<td>".$contributors[$j]['created_at']."</td>";
                    $content .= "<td>".$contributors[$j]['last_visit']."</td>";
                $content .= "</tr>";
            endfor;
        $content .="</table>";
       echo $content;exit;
        ob_end_clean();
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel;charset:UTF-8");
        header('Content-length: ' . strlen($content));
        header('Content-disposition: attachment; filename=' . basename($file));
        echo $content;
        /*exit;
        $path =$_SERVER['DOCUMENT_ROOT']."/BO/assets/temp_documents/";
        $filename = 'excelFile-' . date("Y-M-D") . "-" . time() . '.xlsx';
        $cfilename = $path.$filename;
        $this->convertHtmltableToXlsx($content,$cfilename,true);
        echo $cfilename;*/
    }
    
    function convertHtmltableToXlsx($htmltable,$filename,$extract=FALSE)
    {
        require_once APP_PATH_ROOT.'nlibrary/tools/PHPExcel.php';

        $htmltable = strip_tags($htmltable, "<table><tr><th><thead><tbody><tfoot><td><br><br /><b><span>");
        $htmltable = str_replace("<br />", "\n", $htmltable);
        $htmltable = str_replace("<br/>", "\n", $htmltable);
        $htmltable = str_replace("<br>", "\n", $htmltable);
        $htmltable = str_replace("&nbsp;", " ", $htmltable);
        $htmltable = str_replace("\n\n", "\n", $htmltable);


        $dom = new domDocument;
        $dom->loadHTML($htmltable);

        if(!$dom) {
            echo "<br />Invalid HTML DOM, nothing to Export.";
            exit;
        }
        $dom->preserveWhiteSpace = false;
        $tables = $dom->getElementsByTagName('table');

        if(!is_object($tables)) {
            echo "<br />Invalid HTML Table DOM, nothing to Export.";
            exit;
        }

        $tbcnt = $tables->length - 1;


        $username = "EditPlace";
        $usermail = "user@edit-place.com";
        $usercompany = "Edit Place";
        $debug = false;

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Verdana');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
        $tm = date("YmdHis");
        $pos = strpos($usermail, "@");
        $user = substr($usermail, 0, $pos);
        $user = str_replace(".","",$user);


        $objPHPExcel->getProperties()->setCreator($username)
            ->setLastModifiedBy($username)
            ->setTitle("Sales Generation")
            ->setSubject("Sales Final Validation")
            ->setDescription("Sales Report")
            ->setKeywords("Sales")
            ->setCompany($usercompany)
            ->setCategory("Export");

        $xcol = '';
        $xrow = 1;
        $usedhdrows = 0;
        for($z=0;$z<=$tbcnt;$z++) {
            $headrows = array();
            $bodyrows = array();
            $r = 0;
            $h = 0;
            $maxcols = 0;
            $totrows = 0;
            $rows = $tables->item($z)->getElementsByTagName('tr');
            $totrows = $rows->length;

            foreach ($rows as $row) {
                $ths = $row->getElementsByTagName('th');
                if(is_object($ths)) {
                    if($ths->length > 0) {
                        $headrows[$h]['colcnt'] = $ths->length;
                        if($ths->length > $maxcols) {
                            $maxcols = $ths->length;
                        }
                        $nodes = $ths->length - 1;
                        for($x=0;$x<=$nodes;$x++) {
                            $thishdg = $ths->item($x)->nodeValue;
                            $headrows[$h]['th'][] = $thishdg;
                            $headrows[$h]['bold'][] = $this->findBoldText($this->innerHTML($ths->item($x)));
                            if($ths->item($x)->hasAttribute('style')) {
                                $style = $ths->item($x)->getAttribute('style');
                                $stylecolor = $this->findStyleColor($style);
                                if($stylecolor == '') {
                                    $headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
                                }else{
                                    $headrows[$h]['color'][] = $stylecolor;
                                }
                                $fontsize = $this->findFontSize($style);
                                if($fontsize=='')
                                    $headrows[$h]['size'][] = 11;
                                else
                                    $headrows[$h]['size'][] = $fontsize;
                            }else{
                                $headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
                                $headrows[$h]['size'][] = 11;
                            }
                            if($ths->item($x)->hasAttribute('colspan')) {
                                $headrows[$h]['colspan'][] = $ths->item($x)->getAttribute('colspan');
                            }else{
                                $headrows[$h]['colspan'][] = 1;
                            }
                            if($ths->item($x)->hasAttribute('align')) {
                                $headrows[$h]['align'][] = $ths->item($x)->getAttribute('align');
                            }else{
                                $headrows[$h]['align'][] = 'left';
                            }
                            if($ths->item($x)->hasAttribute('valign')) {
                                $headrows[$h]['valign'][] = $ths->item($x)->getAttribute('valign');
                            }else{
                                $headrows[$h]['valign'][] = 'top';
                            }
                            if($ths->item($x)->hasAttribute('bgcolor')) {
                                $headrows[$h]['bgcolor'][] = str_replace("#", "", $ths->item($x)->getAttribute('bgcolor'));
                            }else{
                                $headrows[$h]['bgcolor'][] = 'FFFFFF';
                            }
                            if($ths->item($x)->hasAttribute('class')) {
                                $headrows[$h]['class'][] = str_replace("#", "", $ths->item($x)->getAttribute('bgcolor'));
                            }else{
                                $headrows[$h]['bgcolor'][] = 'FFFFFF';
                            }
                        }
                        $h++;
                    }
                }
                /* Getting TD's */

                $tds = $row->getElementsByTagName('td');
                if(is_object($tds)) {
                    if($tds->length > 0) {
                        $bodyrows[$r]['colcnt'] = $tds->length;
                        if($tds->length > $maxcols) {
                            $maxcols = $tds->length;
                        }
                        $nodes = $tds->length - 1;
                        for($x=0;$x<=$nodes;$x++) {
                            $thistxt = $tds->item($x)->nodeValue;
                            $bodyrows[$r]['td'][] = $thistxt;
                            $bodyrows[$r]['bold'][] = $this->findBoldText($this->innerHTML($tds->item($x)));

                            if($tds->item($x)->hasAttribute('style')) {
                                $style = $tds->item($x)->getAttribute('style');
                                $stylecolor = $this->findStyleColor($style);
                                if($stylecolor == '') {
                                    $bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
                                }else{
                                    $bodyrows[$r]['color'][] = $stylecolor;
                                }
                                $fontsize = $this->findFontSize($style);
                                if($fontsize=='')
                                    $bodyrows[$r]['size'][] = 10;
                                else
                                    $bodyrows[$r]['size'][] = $fontsize;
                            }else{
                                $bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
                                $bodyrows[$r]['size'][] = 10;
                            }
                            if($tds->item($x)->hasAttribute('colspan')) {
                                $bodyrows[$r]['colspan'][] = $tds->item($x)->getAttribute('colspan');
                            }else{
                                $bodyrows[$r]['colspan'][] = 1;
                            }
                            if($tds->item($x)->hasAttribute('align')) {
                                $bodyrows[$r]['align'][] = $tds->item($x)->getAttribute('align');
                            }else{
                                $bodyrows[$r]['align'][] = 'left';
                            }
                            if($tds->item($x)->hasAttribute('valign')) {
                                $bodyrows[$r]['valign'][] = $tds->item($x)->getAttribute('valign');
                            }else{
                                $bodyrows[$r]['valign'][] = 'top';
                            }
                            if($tds->item($x)->hasAttribute('bgcolor')) {
                                $bodyrows[$r]['bgcolor'][] = str_replace("#", "", $tds->item($x)->getAttribute('bgcolor'));
                            }else{
                                $bodyrows[$r]['bgcolor'][] = 'FFFFFF';
                            }
                        }
                        $r++;
                    }
                }

                /* End of TD's */
            }

            $worksheet = $objPHPExcel->getActiveSheet();                // set worksheet we're working on
            $style_overlay = array('font' =>
                array('color' =>
                    array('rgb' => '000000'),'bold' => false,),
                'fill'  =>
                    array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCFF')),
                'alignment' =>
                    array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP),
                /*'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                   'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                   'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                   'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),*/
            );

            $heightvars = array(1=>'42', 2=>'42', 3=>'48', 4=>'52', 5=>'58', 6=>'64', 7=>'68', 8=>'76', 9=>'82');
            for($h=0;$h<count($headrows);$h++) {
                $th = $headrows[$h]['th'];
                $colspans = $headrows[$h]['colspan'];
                $aligns = $headrows[$h]['align'];
                $valigns = $headrows[$h]['valign'];
                $bgcolors = $headrows[$h]['bgcolor'];
                $colcnt = $headrows[$h]['colcnt'];
                $colors = $headrows[$h]['color'];
                $bolds = $headrows[$h]['bold'];
                $sizes = $headrows[$h]['size'];
                $usedhdrows++;
                $mergedcells = false;
                for($t=0;$t<count($th);$t++) {
                    if($xcol == '') {$xcol = 'A';}else{$xcol++;}
                    $thishdg = $th[$t];
                    $thisalign = $aligns[$t];
                    $thisvalign = $valigns[$t];
                    $thiscolspan = $colspans[$t];
                    $thiscolor = $colors[$t];
                    $thisbg = $bgcolors[$t];
                    $thisbold = $bolds[$t];
                    $thissize = $sizes[$t];
                    $strbold = ($thisbold==true) ? 'true' : 'false';
                    if($thisbg == 'FFFFFF') {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
                    }else{
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                    }
                    $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
                    $style_overlay['alignment']['horizontal'] = $thisalign;
                    $style_overlay['font']['color']['rgb'] = $thiscolor;
                    $style_overlay['font']['bold'] = $thisbold;
                    $style_overlay['font']['size'] = $thissize;
                    $style_overlay['fill']['color']['rgb'] = $thisbg;
                    $worksheet->setCellValue($xcol.$xrow, $thishdg);
                    $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);

                    if($thiscolspan > 1) {                                                // spans more than 1 column
                        $mergedcells = true;
                        $lastxcol = $xcol;
                        for($j=1;$j<$thiscolspan;$j++) {
                            $lastxcol++;
                            $worksheet->setCellValue($lastxcol.$xrow, '');
                            $worksheet->getStyle($lastxcol.$xrow)->applyFromArray($style_overlay);
                        }
                        $cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;

                        $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
                        $num_newlines = substr_count($thishdg, "\n");                       // count number of newline chars
                        if($num_newlines > 1) {
                            $rowheight = $heightvars[1];                                      // default to 35
                            if(array_key_exists($num_newlines, $heightvars)) {
                                $rowheight = $heightvars[$num_newlines];
                            }else{
                                $rowheight = 75;
                            }
                            $worksheet->getRowDimension($xrow)->setRowHeight($rowheight);     // adjust heading row height
                        }
                        $xcol = $lastxcol;
                    }
                }
                $xrow++;
                $xcol = '';
            }

            $usedhdrows++;

            for($b=0;$b<count($bodyrows);$b++) {
                $td = $bodyrows[$b]['td'];
                $colcnt = $bodyrows[$b]['colcnt'];
                $colspans = $bodyrows[$b]['colspan'];
                $aligns = $bodyrows[$b]['align'];
                $valigns = $bodyrows[$b]['valign'];
                $bgcolors = $bodyrows[$b]['bgcolor'];
                $colors = $bodyrows[$b]['color'];
                $bolds = $bodyrows[$b]['bold'];
                $sizes = $bodyrows[$b]['size'];
                for($t=0;$t<count($td);$t++) {
                    if($xcol == '') {$xcol = 'A';}else{$xcol++;}
                    $thistext = $td[$t];
                    $thisalign = $aligns[$t];
                    $thisvalign = $valigns[$t];
                    $thiscolspan = $colspans[$t];
                    $thiscolor = $colors[$t];
                    $thisbg = $bgcolors[$t];
                    $thisbold = $bolds[$t];
                    $thissize = $sizes[$t];
                    $strbold = ($thisbold==true) ? 'true' : 'false';
                    if($thisbg == 'FFFFFF') {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
                    }else{
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                    }

                    $style_overlay['alignment']['vertical'] = $thisvalign;              // set styles for cell
                    $style_overlay['alignment']['horizontal'] = $thisalign;
                    $style_overlay['font']['color']['rgb'] = $thiscolor;
                    $style_overlay['font']['bold'] = $thisbold;
                    $style_overlay['font']['size'] = $thissize;
                    $style_overlay['fill']['color']['rgb'] = $thisbg;
                    if($thiscolspan == 1) {
                        $worksheet->getColumnDimension($xcol)->setWidth(20);
                    }
                    else
                    {
                        $worksheet->getColumnDimension($xcol)->setWidth($thiscolspan*5);
                    }
                    $worksheet->setCellValue($xcol.$xrow, $thistext);

                    $worksheet->getStyle($xcol.$xrow)->applyFromArray($style_overlay);
                    if($thiscolspan > 1) {                                                // spans more than 1 column
                        $lastxcol = $xcol;
                        for($j=1;$j<$thiscolspan;$j++) {
                            $lastxcol++;
                        }
                        $cellRange = $xcol.$xrow.':'.$lastxcol.$xrow;
                        $worksheet->mergeCells($cellRange);
                        $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
                        $xcol = $lastxcol;
                    }
                }



                $xrow++;
                $xcol = '';
            }

            $azcol = 'A';
            for($x=1;$x==$maxcols;$x++) {
                $worksheet->getColumnDimension($azcol)->setAutoSize(true);
                $azcol++;
            }

        }
        // $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save($filename);
    }

    function innerHTML($node)
    {
        $doc = $node->ownerDocument;
        $frag = $doc->createDocumentFragment();
        foreach ($node->childNodes as $child) {
            $frag->appendChild($child->cloneNode(TRUE));
        }
        return $doc->saveXML($frag);
    }

    function findSpanColor($node)
    {
        $pos = stripos($node, "color:");
        if ($pos === false) {
            return '000000';
        }
        $node = substr($node, $pos);
        $start = "#";
        $end = ";";
        $node = " ".$node;
        $ini = stripos($node,$start);
        if ($ini === false) return "000000";
        $ini += strlen($start);
        $len = stripos($node,$end,$ini) - $ini;
        return substr($node,$ini,$len);
    }

    function findStyleColor($style)
    {
        $pos = stripos($style, "color:");
        if ($pos === false) {
            return '';
        }
        $style = substr($style, $pos);
        $start = "#";
        $end = ";";
        $style = " ".$style;
        $ini = stripos($style,$start);
        if ($ini === false) return "";
        $ini += strlen($start);
        $len = stripos($style,$end,$ini) - $ini;
        return substr($style,$ini,$len);
    }

    function findFontSize($style)
    {
        $pos = stripos($style, "font-size:");
        if ($pos === false) {
            return '';
        }
        $style = substr($style, $pos);
        return substr($style,stripos($style,":")+1,strlen(stripos($style,"px")));
    }

    function findBoldText($node)
    {
        $pos = stripos($node, "<b>");
        if ($pos === false) {
            return false;
        }
        return true;
    }
}

