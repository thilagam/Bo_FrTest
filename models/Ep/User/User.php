<?php
/**
 * Ep_User_User
 * @author admin
 * @package Ticket
 * @version 1.0
 */
 /*Status
        0 => sent by sender / received by recipient
        1 => received by sender / sent by recipient
        2 => classified by sender
        3 => classified by recipient
*/
class Ep_User_User extends Ep_Db_Identifier
{
    protected $_name = 'User';
    private $identifier;
    private $login;
    private $email;
    private $password;
    private $status;
    private $type;
    private $profile_type;
    private $groupId;
    private $menuId;
    private $pageId;
    private $created_at;
    private $updated_at;
    private $last_visit;


    public function loadData($array)
    {
        $this->identifier = $array["identifier"];
        $this->login = $array["login"];
        $this->email = $array["email"];
        $this->password = $array["password"];
        $this->status = $array["status"];
        $this->type = $array["type"];
        $this->profile_type = $array["profile_type"];
        $this->groupId = $array["groupId"];
        $this->menuId = $array["menuId"];
        $this->pageId = $array["pageId"];
        $this->created_at = $array["created_at"];

        return $this;
    }

    public function loadintoArray()
    {
        $array = array();
        $array["identifier"] = $this->getIdentifier();
        $array["login"] = $this->login;
        $array["email"] = $this->email;
        $array["password"] = $this->password;
        $array["status"] = $this->status;
        $array["type"] = $this->type;
        $array["profile_type"] = $this->profile_type;
        $array["groupId"] = $this->groupId;
        $array["menuId"] = $this->menuId;
        $array["pageId"] = $this->pageId;
        $array["created_at"] = $this->created_at;
        return $array;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    //Function to check profile exists
    public function checkProfileExist($identifier)
    {
        $whereQuery = "identifier = '" . $identifier . "'";
        $existsQuery = "select * from " . $this->_name . " where " . $whereQuery;

        if (($result = $this->getQuery($existsQuery, true)) != NULL)
            return $result[0]["identifier"];
        else
            return "NO";

    }

    // make login into site //
    public function login($login, $passwd)
    {
        $query = "SELECT " . $this->_name . ".* FROM " . $this->_name . " WHERE " . $this->_name . ".login = '$login' AND " . $this->_name . ".password = '$passwd' AND " . $this->_name . ".identifier = " . $this->_name . ".identifier AND status='active'";
		//echo $query;//exit;
        if (($result = $this->getQuery($query, true)) != NULL)		
		{
           
			if($result[0]['login']==$login &&  $result[0]['password']==$passwd)
				return true;
			else
				return false;
		}
        else
            return false;
    }

    /// page ids of user adn stored in the session so the accessability to pages //
    public function getPageId($logName)
    {
        $query = "SELECT pageId FROM " . $this->_name . " WHERE login = '" . $logName . "'";
        if (($result = $this->getQuery($query, false)) != NULL) {
            return $result;
        } else
            return false;
    }

    ///////get pages ids on identifier////
    public function getUserPages($ids)
    {
        $query = "SELECT pageId FROM " . $this->_name . " WHERE identifier = '" . $ids . "'";
        if (($result = $this->getQuery($query, false)) != NULL) {
            return $result;
        } else
            return false;
    }

    /// menu ids of user adn stored in the session so the accessability to menus //
    public function getUserMenus($ids)
    {
        $query = "SELECT menuId FROM " . $this->_name . " WHERE identifier = '" . $ids . "'";
        if (($result = $this->getQuery($query, false)) != NULL) {
            return $result;
        } else
            return false;
    }

    ///////check the email is exits////
    public function getExistingEmail($email)
    {
        $query = "SELECT identifier FROM " . $this->_name . " WHERE email = '" . $email . "'";
        if (($result = $this->getQuery($query, false)) != NULL) {
            return "yes";
        } else
            return "no";
    }

    // get contributors and client list //
    public function getUsers()
    {
        $query = "SELECT identifier, login, email FROM " . $this->_name . " WHERE type NOT IN ('contributor','client')";
        if (($result = $this->getQuery($query, false)) != NULL) {
            return $result;
        } else
            return false;
    }

    // get all internal user (BO users) //
    public function getBoUsersList()
    {
        $query = "SELECT u.identifier, u.email, up.first_name, up.last_name FROM  " . $this->_name . " u INNER JOIN UserPlus up ON u.identifier = up.user_id
                    WHERE type NOT IN ('contributor','client')";
        if (($result = $this->getQuery($query, false)) != NULL) {
            return $result;
        } else
            return false;
    }

    //////////get all user details
    public function getAllUsersDetails($userid)
    {
        $query = "SELECT u.identifier, u.login, u.profile_type, u.profile_type2, u.type2, u.email, up.first_name, up.last_name, u.menuId,
	 	             up.country, u.groupId, up.city,up.phone_number	FROM " . $this->_name . " u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.identifier = " . $userid;
        //echo $query;exit;
        if (($result = $this->getQuery($query, true)) != NULL) {
            return $result;
        } else
            return false;
    }
//////////get count of all bo users/////////////
    public function getAllBoUsers()
    {
        $Query = "select u.identifier, u.login, up.first_name, up.last_name from User u LEFT JOIN UserPlus up ON up.user_id = u.identifier
                    WHERE u.type NOT IN ('contributor','client','chiefodigeo','superclient')";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    // fetch all the managers///
    public function getAllManagers()
    {
        $query = "select identifier, email FROM " . $this->_name . " WHERE groupId IN ('1','2','3','4')";
        if (($result = $this->getQuery($query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /*Get contributors who are seniors**/
    public function getSeniorContributors()
    {
        $query = "select identifier FROM " . $this->_name . " WHERE profile_type ='senior' AND status = 'Active'";
        if (($result = $this->getQuery($query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /*Get contributors who are seniors who has french as mother tongue**/
    public function getSeniorFrContributors($lang)
    {
        $query = "select u.identifier FROM " . $this->_name . " u INNER JOIN Contributor c ON u.identifier=c.user_id WHERE u.profile_type ='senior' AND u.status = 'Active' AND c.language='" . $lang . "'";
        if (($result = $this->getQuery($query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    // all the user details to maintained into sessions ///
    public function getLoggedUserDetails($logName)
    {
        $query = "SELECT identifier, email, status, type, groupId, menuId, last_visit FROM " . $this->_name . " WHERE login = '" . $logName . "'";
        if (($result = $this->getQuery($query, false)) != NULL) {
            return $result;
        } else
            return false;
    }

    public function updateUser($data, $query)
    {
        $data['updated_at'] = date("Y-m-d H:i:s", time());
        return $this->updateQuery($data, $query);
    }

    public function InsertUser($uarray)
    {
        $uarray['identifier'] = $this->getIdentifier();
        $this->insertQuery($uarray);
        return $uarray['identifier'];
    }

    public function getclientList()
    {
        $Query = "select u.identifier, u.invoiced, u.email,c.company_name,up.first_name,up.last_name from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Client c ON u.identifier=c.user_id
		/*LEFT JOIN Delivery d ON u.identifier=d.user_id
		LEFT JOIN Article a ON a.delivery_id=d.id*/
		where u.type='client' ";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    public function user_update_invoiced($invoiced, $idf)
    {
        //echo $invoiced." ".$idf;exit;
        $Query = "UPDATE User SET invoiced='" . $invoiced . "'
				  WHERE identifier  ='" . $idf . "'";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return true;
        else
            return false;
    }

    public function getClientCount()
    {
        $Query = "select u.identifier,count(u.identifier) AS clientscount from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Client c ON u.identifier=c.user_id
		/*LEFT JOIN Delivery d ON u.identifier=d.user_id
		LEFT JOIN Article a ON a.delivery_id=d.id*/
		where u.type='client' ";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    //////////get Contributor list/////////////
    public function getContributorsList()
    {
        $Query = "select u.identifier,u.email,up.first_name,up.last_name,u.profile_type from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Contributor cb ON u.identifier=cb.user_id
		/*LEFT JOIN Delivery d ON u.identifier=d.user_id
		LEFT JOIN Article a ON a.delivery_id=d.id*/
		where u.type='contributor' ";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    //////////get writers  list who are with corrector profile too /////////////
    public function getCorrectorList()
    {
        $Query = "select u.identifier,u.email,up.first_name,up.last_name from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Contributor cb ON u.identifier=cb.user_id
		/*LEFT JOIN Delivery d ON u.identifier=d.user_id
		LEFT JOIN Article a ON a.delivery_id=d.id*/
		where u.type2='corrector' ";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    //////get experience details for contributor////
    public function getExperienceDetailsold($identifier, $type)
    {
        $stillworkingquery = "select still_working from ContributorExperience where
            type='" . $type . "' and user_id='" . $identifier . "' order by still_working ASC LIMIT 1";
        $result = $this->getQuery($stillworkingquery, true);
        if ($result[0]['still_working'] == 'yes') {
            $experienceQuery = "select * from ContributorExperience where type='" . $type . "' and user_id='" . $identifier . "' AND still_working='yes'";
        } else {
            $experienceQuery = "select * from ContributorExperience where type='" . $type . "' and user_id='" . $identifier . "' order by to_year DESC LIMIT 1";
        }
        //echo "<br>".$experienceQuery;  echo "<br>".$this->getNbRows($experienceQuery);
        if (($count = $this->getNbRows($experienceQuery)) > 0) {    //echo "hee";
            $experienceDetails = $this->getQuery($experienceQuery, true);
            return $experienceDetails;
        } else {
            return "NO";
        }
    }

    //////get experience details for contributor////
    public function getExperienceDetails($identifier, $type)
    {
        $experienceQuery = "select * from ContributorExperience where type='" . $type . "' and user_id='" . $identifier . "' order by to_year DESC LIMIT 1";
        //echo "<br>".$experienceQuery;  echo "<br>".$this->getNbRows($experienceQuery);
        if (($experienceDetails = $this->getQuery($experienceQuery, true)) != NULL)
            return $experienceDetails;
        else
            return "NO";

    }

    public function InsertnewUser($Uarray)
    {
        $id = $this->getIdentifier();

        //User
        $this->_name = 'User';
        $U_array = array();
        $U_array['identifier'] = $id;
        $U_array['email'] = $Uarray['newemail'];
        $U_array['password'] = $Uarray['newpwd'];
        $U_array['status'] = 'Active';
        $U_array['invoiced'] = $Uarray['invoiced'];

        $U_array['type'] = 'Client';

        $U_array['verified_status'] = 'YES';
        $U_array['created_by'] = 'backend';
        $this->insertQuery($U_array);

        //UserPlus
        $this->_name = 'UserPlus';
        $Up_array = array();
        $Up_array['user_id'] = $id;
        $this->insertQuery($Up_array);

        $this->_name = 'Client';
        $C_array = array();
        $C_array['user_id'] = $id;
        $C_array['company_name'] = $Uarray['company_name'];
        $this->insertQuery($C_array);

        return $id;
    }

    public function checkClientMailid($mailid)
    {
        $whereQuery = "email = '" . $mailid . "'";
        $query = "select * from User where " . $whereQuery;

        if (($result = $this->getQuery($query, true)) != NULL)
            return "false";
        else
            return "true";
    }

    // full details of any user based on type
    public function getUserdetails($user)
    {
        $checkuser = $this->getQuery("SELECT type FROM User WHERE identifier='" . $user . "'", true);

        if ($checkuser[0]['type'] == 'client') {
            $SelectuserQuery = "SELECT * FROM User u LEFT JOIN UserPlus up on u.identifier=up.user_id LEFT JOIN Client c ON u.identifier=c.user_id  WHERE u.identifier='" . $user . "'";
        } elseif ($checkuser[0]['type'] == 'contributor') {
            $SelectuserQuery = "SELECT * FROM UserPlus up LEFT JOIN User u on u.identifier=up.user_id
			                LEFT JOIN Contributor c ON up.user_id=c.user_id
			                LEFT JOIN ContributorExperience ce ON up.user_id = ce.user_id WHERE up.user_id='" . $user . "'";

        }

        $resultudetails = $this->getQuery($SelectuserQuery, true);
        if (count($resultudetails) == 0) {
            $SelectuserQuery = "SELECT * FROM User WHERE identifier='" . $user . "'";
            $resultudetails = $this->getQuery($SelectuserQuery, true);
        }
        $resultudetails[0]['type'] = $checkuser[0]['type'];
        return $resultudetails;
    }

    public function getContributordetails($user)
    {
        $SelectuserQuery = "SELECT * , u.identifier AS primary_id FROM User u
                            LEFT JOIN UserPlus up on u.identifier=up.user_id
			                LEFT JOIN Contributor c ON u.identifier=c.user_id
			                LEFT JOIN ContributorExperience ce ON up.user_id = ce.user_id WHERE u.identifier='" . $user . "'";
        if (($result = $this->getQuery($SelectuserQuery, true)) != NULL) {
            return $result;
        } else
            return false;
    }

    public function getClientProfile($uid)
    {
        $SelectallContrib = "SELECT u.identifier,c.company_name FROM User u LEFT JOIN Client c ON u.identifier=c.user_id where u.identifier='" . $uid . "'";
        $resultall = $this->getQuery($SelectallContrib, true);
        return $resultall;
    }

    //////get all user groups///////
    public function getUserGroups()
    {
        $query = "SELECT id, groupName FROM UserGroupAccess";
        if (($result = $this->getQuery($query, true)) != NULL) {
            return $result;
        } else
            return false;
    }

    ////////////update the contributor//////////////
    public function updatecontribUser($conarray)
    {
        $where1 = " user_id='" . $conarray['userId'] . "' ";

        $this->_name = "UserPlus";
        $userarr = array();
        //$userarr['first_name']=$conarray['first_name'];
        //$userarr['last_name']=$conarray['last_name'];
        $userarr['address'] = $conarray['address'];
        $userarr['city'] = $conarray['city'];
        $userarr['zipcode'] = $conarray['zipcode'];
        $userarr['country'] = $conarray['country'];
        $userarr['phone_number'] = $conarray['phone_number'];
        $this->updateQuery($userarr, $where1);

        $this->_name = "Contributor";
        $contribarr = array();
        $contribarr['dob'] = $conarray["Date_Year"] . "-" . $conarray["Date_Month"] . "-" . $conarray["Date_Day"];
        $contribarr['profession'] = $conarray['profession'];
        $contribarr['profession_other'] = $conarray['profession_other'];
        $contribarr['university'] = $conarray['university'];
        $contribarr['education'] = $conarray['education'];
        $contribarr['degree'] = $conarray['degree'];
        $contribarr['language'] = $conarray['language'];

        $contribarr['nationality'] = $conarray['nationality'];

        if ($conarray['favourite_category'] != "") {
            $contribarr['favourite_category'] = implode(",", $conarray['ep_category']);
        }
        if (count($conarray['ep_category']) > 0) {
            $category_more = $conarray['ep_category'];
            $category_sliders_more = $conarray['category_slider_more'];
            foreach ($category_more as $key => $category) {
                if ($category)
                    $moreCategories[$category] = str_replace("%", "", $category_sliders_more[$key]);
            }
            $contribarr['category_more'] = serialize($moreCategories);
        }

        if (count($conarray['language_more']) > 0) {
            $language_more = $conarray['language_more'];
            $lang_sliders_more = $conarray['lang_slider_more'];
            foreach ($language_more as $key => $lang) {
                if ($lang)
                    $moreLanguages[$lang] = str_replace("%", "", $lang_sliders_more[$key]);
            }
            $contribarr['language_more'] = serialize($moreLanguages);
        }
        $contribarr['self_details'] = nl2br($conarray['self_details']);
        /* added by naseer on 30-07-2015 */
        /* fetches the content of form and stores it according to the options */
        $options_flag = $conarray['options_flag'];
        if ($options_flag == 'reg_check') {
            $contribarr["options_flag"] = $conarray['options_flag'];
            $contribarr["passport_no"] = $conarray['passport_no'];
            $contribarr["id_card"] = $conarray['id_card'];

        } elseif ($options_flag == 'com_check') {
            $contribarr["options_flag"] = $conarray['options_flag'];
            $contribarr["com_name"] = $conarray['com_name'];
            $contribarr["com_country"] = $conarray['com_country'];
            $contribarr["com_address"] = $conarray['com_address'];
            $contribarr["com_phone"] = $conarray['com_phone'];
            $contribarr["com_city"] = $conarray['com_city'];
            $contribarr["com_zipcode"] = $conarray['com_zipcode'];
            $contribarr["com_siren"] = $conarray['com_siren'];
            $contribarr["com_tva_number"] = $conarray['com_tva_number'];
        } elseif ($options_flag == 'tva_check') {
            $contribarr["options_flag"] = $conarray['options_flag'];
            $contribarr["siren_number"] = $conarray['siren_number'];
            $contribarr["denomination_sociale"] = $conarray['denomination_sociale'];
            $contribarr["tva_number"] = $conarray['tav_number'];
        }
        /* end of added by naser on 03-08-2015 */
        $contribarr['staus_self_details_updated'] = 'no';

        $contribarr['payment_type'] = $conarray["payment_type"];
        /* *Inserting Pay info details**/
        $contribarr['pay_info_type'] = $conarray["pay_info_type"];
        /*$contribarr['SSN'] = $conarray["ssn"];
        $contribarr['company_number'] = $conarray["company_number"];
        $contribarr['vat_check'] = $conarray["vat_check"];
        $contribarr['VAT_number'] = $conarray["VAT_number"];*/
        /* * Inserting Paypal and RIB info**/
        $contribarr['paypal_id'] = $conarray["paypal_id"];
        if ($conarray["pay_info_type"] == 'out_france' || $conarray['virement_country'] != 38) {
            $contribarr['rib_id'] = $conarray["rib_id_6"] . "|" . $conarray["rib_id_7"];
        } else {
            $contribarr['rib_id'] = $conarray["rib_id_1"] . "|" . $conarray["rib_id_2"] . "|" . $conarray["rib_id_3"] . "|" .
                $conarray["rib_id_4"] . "|" . $conarray["rib_id_5"];
        }

        //Contributor test
        $contribarr['contributortest'] = $conarray["contributortest"];
        if ($conarray["contributortest"] == "yes") {
            $contribarr['contributortestcomment'] = $conarray["contributortestcomment"];
            $contribarr['contributortestmarks'] = $conarray["contributortestmarks"];
        } else {
            $contribarr['contributortestcomment'] = "";
            $contribarr['contributortestmarks'] = 0;
        }
        $this->updateQuery($contribarr, $where1);

        $where2 = " identifier='" . $conarray['userId'] . "' ";
        $this->_name = "User";
        $uarr = array();
        //$uarr['email']=$conarray['email'];
        //$uarr['password']=$conarray['password'];
        $uarr['status'] = $conarray['status'];
        $uarr['profile_type'] = $conarray['profile_type'];
        $uarr['subscribe'] = $conarray['subscribe'];
        $uarr['alert_subscribe'] = $conarray['alert_subscribe'];
        if ($conarray['type2'] == 'yes') {
            $uarr['type2'] = 'corrector';
            $uarr['profile_type2'] = $conarray['profile_type2'];
        } else {
            $uarr['type2'] = NULL;
            $uarr['profile_type2'] = "";
        }

        $uarr['blackstatus'] = $conarray['blackstatus'];
        $this->updateQuery($uarr, $where2);
    }

    ////////////update the contributor//////////////
    public function updateBriefContribUser($conarray)
    {
        $where1 = " user_id='" . $conarray['userId'] . "' ";

        $this->_name = "UserPlus";
        $userarr = array();
        $userarr['first_name'] = $conarray['first_name'];
        $userarr['last_name'] = $conarray['last_name'];
        $this->updateQuery($userarr, $where1);

        $this->_name = "Contributor";

        $where2 = " identifier='" . $conarray['userId'] . "' ";
        $this->_name = "User";
        $uarr = array();
        $uarr['email'] = $conarray['email'];
        $uarr['password'] = $conarray['password'];
        $this->updateQuery($uarr, $where2);
    }

    public function getContribPartinfo($uu)
    {
        $profileQuery1 = "select count(id) AS no_participations from Participation where user_id = '" . $uu . "'";
        $profileQuery2 = "select count(id) AS no_approved from Participation where user_id = '" . $uu . "' AND status='published'";
        $profileQuery3 = "select count(id) AS no_disapproved from Participation where user_id = '" . $uu . "' AND status='disapproved'";

        $result = $this->getQuery($profileQuery1, true);
        $result2 = $this->getQuery($profileQuery2, true);
        $result3 = $this->getQuery($profileQuery3, true);
        $result4 = array_merge($result, $result2, $result3);//print_r($result4);
        return $result4;
    }

    public function ListallfavContribs($client)
    {
        $ContriballQuery = "SELECT
							u.identifier,u.email,up.first_name,up.last_name
						FROM
							User u LEFT JOIN UserPlus up ON u.identifier=up.user_id
							INNER JOIN Favourite_contributor f ON u.identifier=f.contrib_id
						WHERE
							u.type='contributor' AND f.client_id='" . $client . "' AND f.status='1'";

        $ContriballResult = $this->getQuery($ContriballQuery, true);
        return $ContriballResult;
    }

    public function listusers($attr)
    {
        if ($attr == '0')
            $where = "WHERE u.type IN ('client','contributor')";
        elseif ($attr == '1')
            $where = " WHERE u.type='client'";
        elseif ($attr == '2')
            $where = " WHERE u.type='contributor'";

        $listQuery = "SELECT u.identifier,up.first_name,up.last_name,u.email,c.company_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id LEFT JOIN Client c ON up.user_id=c.user_id " . $where . " order by up.first_name";
        $resultu = $this->getQuery($listQuery, true);
        return $resultu;
    }

    public function updateclientUser($clarray)
    {
        $where1 = " user_id='" . $clarray['userId'] . "' ";

        $this->_name = "UserPlus";
        $userarr = array();
        $userarr['first_name'] = ($clarray['first_name']);
        $userarr['last_name'] = $clarray['last_name'];
        $userarr['address'] = $clarray['address'];
        $userarr['city'] = $clarray['city'];
        $userarr['zipcode'] = $clarray['zipcode'];
        $userarr['country'] = $clarray['country'];
        $userarr['phone_number'] = $clarray['phone_number'];

        //$this->updateQuery($userarr,$where1);

        $clientcheck = $this->Checkifexists($clarray['userId'], 'UserPlus');

        if ($clientcheck == "yes") {
            $this->updateQuery($userarr, $where1);
        } else {
            $userarr['user_id'] = $clarray['userId'];
            $this->insertQuery($userarr);
        }

        $this->_name = "Client";
        $clientarr = array();
        $clientarr['company_name'] = $clarray['company_name'];
        $clientarr['rcs'] = $clarray['rcs'];
        $clientarr['vat'] = $clarray['vat'];
        $clientarr['fax_number'] = $clarray['fax_number'];
        /*$clientarr['poll_rights']=$clarray['poll_rights'];

        if($clarray['poll_rights']=="yes")
            $clientarr['poll_contrib_percentage']=$clarray['poll_contrib_percentage'];
        elseif($clarray['poll_rights']=="no")
            $clientarr['contrib_percentage']=$clarray['contrib_percentage'];*/

        $clientcheck = $this->Checkifexists($clarray['userId'], 'Client');

        $clientarr['premiumdisplay'] = $clarray['premiumdisplay'];
        $clientarr['nopremiumdisplay'] = $clarray['nopremiumdisplay'];
        $clientarr['paypercent'] = $clarray['paypercent'];
        $clientarr['contrib_percentage'] = $clarray['contrib_percentage'];
        $clientarr['privatepublish'] = $clarray['privatepublish'];
        $clientarr['contributortestrequired'] = $clarray['contributortestrequired'];
        $clientarr['urlsexcluded'] = $clarray['urlsexcluded'];
        $clientarr['prod_desc'] = $clarray['prod_desc'];

        if ($clientcheck == "yes") {
            $this->updateQuery($clientarr, $where1);
        } else {
            $clientarr['user_id'] = $clarray['userId'];
            $this->insertQuery($clientarr);
        }

        $where2 = " identifier='" . $clarray['userId'] . "' ";

        $this->_name = "User";
        $uarr = array();
        $uarr['email'] = $clarray['email'];
        $uarr['password'] = $clarray['password'];
        $uarr['status'] = $clarray['status'];
        $uarr['alert_subscribe'] = $clarray['alert_subscribe'];
        $this->updateQuery($uarr, $where2);

        //Favourite contributors
        $this->_name = 'Favourite_contributor';

        //inative all fav contribs
        $farr = array();
        $farr['status'] = 0;
        $wheref = " client_id='" . $clarray['userId'] . "'";
        $this->updateQuery($farr, $wheref);

        if (count($clarray['favcontribs']) > 0) {
            for ($f = 0; $f < count($clarray['favcontribs']); $f++)
                $this->addfavcontrib($clarray['favcontribs'][$f], $clarray['userId']);
        }

        //insert contacts
        if (count($clarray['first_name_contact']) > 0) {
            for ($c = 0; $c < count($clarray['first_name_contact']); $c++) {
                $contuarray = array();
                $contuarray['password'] = $clarray['password_contact'][$c];

                $contuparray = array();
                $contuparray['first_name'] = $clarray['first_name_contact'][$c];
                $contuparray['last_name'] = $clarray['last_name_contact'][$c];

                if ($clarray['identifier_contact'][$c] != "") {
                    //Update user
                    $this->_name = "User";
                    $wherecontu = " identifier='" . $clarray['identifier_contact'][$c] . "'";
                    $this->updateQuery($contuarray, $wherecontu);

                    //Update userplus
                    $this->_name = "UserPlus";
                    $wherecontup = " user_id='" . $clarray['identifier_contact'][$c] . "'";
                    $this->updateQuery($contuparray, $wherecontup);
                } else {
                    //insert user
                    $this->_name = "User";
                    $contuarray['email'] = $clarray['email_contact'][$c];
                    $contuarray['client_reference'] = $clarray['userId'];
                    $contuarray['status'] = "Active";
                    $vcode = md5("edit-place_" . $clarray['email_contact'][$c]);
                    $contuarray['verification_code'] = $vcode;
                    $contuarray['verified_status'] = "YES";
                    $contuarray['type'] = "clientcontact";
                    $contuarray["identifier"] = $this->getIdentifier();
                    $this->insertQuery($contuarray);

                    //UserPlus insertion
                    $this->_name = "UserPlus";
                    $contuparray["user_id"] = $contuarray["identifier"];
                    $this->insertQuery($contuparray);
                }
            }
        }

    }

    public function Checkifexists($user, $table)
    {
        $checkQuery = "SELECT * FROM " . $table . " WHERE user_id='" . $user . "'";
        $checkresult = $this->getQuery($checkQuery, true);

        if (count($checkresult) > 0)
            return "yes";
        else
            return "no";

    }

    public function addfavcontrib($contrib, $client)
    {   //print_r($contrib); echo  $client; exit;
        $this->_name = 'Favourite_contributor';

        $chkQuery = "SELECT * FROM " . $this->_name . " WHERE client_id='" . $client . "' AND contrib_id='" . $contrib . "'";

        if (($resultfav = $this->getQuery($chkQuery, true)) != NULL) {
            $wherefav = "client_id='" . $client . "' AND contrib_id='" . $contrib . "' ";

            $remfav = array();
            $remfav["status"] = 1;
            $this->updateQuery($remfav, $wherefav);
        } else {
            $adfav = array();
            $adfav["contrib_id"] = $contrib;
            $adfav["client_id"] = $client;
            $this->insertQuery($adfav);
        }
    }

    //////////get stats Contributor list/////////////
    public function getStatsContributorsList()
    {
        $Query = "select u.identifier, u.email, u.password, u.created_at, u.blackstatus, u.profile_type, u.status, up.first_name,up.last_name, cb.favourite_category, cb.category_more, cb.language as mother_tongue from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Contributor cb ON u.identifier=cb.user_id
	    where u.type='contributor' ";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    ///////get contributor list who involved in particular ao//////////////
    public function getContribsInAo($aoid)
    {
        $msg_query = "select distinct p.user_id from  Participation p
		    INNER JOIN Article a ON a.id = p.article_id
		    INNER JOIN Delivery d ON d.id = a.delivery_id
	        where d.id='" . $aoid . "'";
        if (($result = $this->getQuery($msg_query, true)) != NULL) {
            $countIds = count($result);
            for ($i = 0; $i < $countIds; $i++) {
                $ids[$i] = $result[$i]['user_id'];
            }
            return $idstring = implode(',', $ids);
        } else
            return 0;
    }

    ////////get the contributors who are suited for the criteria selected in statistics search contributor //////////
    public function getContibsIdsForSearch($whereQuery)
    {
        $existsQuery = "SELECT p.user_id AS user_id FROM Participation p LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id " . $whereQuery;
        if (($result = $this->getQuery($existsQuery, true)) != NULL) {
            $countIds = count($result);
            for ($i = 0; $i < $countIds; $i++) {
                $ids[$i] = $result[$i]['user_id'];
            }
            return $idstring = implode(',', $ids);
        } else
            return 0;
    }


    //////////get stats searched Contributor list /////////////
    public function getSearchedContributorsList($params)
    {           //print_r($params);    exit;
        $where = '';
        if ($params['searchsubmit'] == 'Search') {
            if ($params['aotitle'] != '0') {
                $contribsIdsList = $this->getContribsInAo($params['aotitle']);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['contrib'] != '0') {
                $where .= " AND u.identifier=" . $params['contrib'];
            }
            if ($params['type'] != '0') {
                $where .= " AND u.profile_type='" . $params['type'] . "'";
            }
            if ($params['type2'] != '0') {
                if ($params['type2'] == 'writer')
                    $where .= " AND u.type2 IS NULL";
                else
                    $where .= " AND u.type2='" . $params['type2'] . "'";
            }
            if ($params['status'] != '0') {
                $where .= " AND u.status='" . $params['status'] . "'";
            }
            if ($params['blacklist'] != '0') {
                $where .= " AND u.blackstatus='" . $params['blacklist'] . "'";
            }
            if ($params['nationalism'] != '0') {
                $where .= " AND cb.nationality=" . $params['nationalism'];
            }
            if ($params['minage'] != '') {
                // $where.= " AND cb.dob='".$minage."'";
                $where .= " AND ((DATEDIFF(NOW(),cb.dob))/365) >='" . $params['minage'] . "' AND ((DATEDIFF(NOW(),cb.dob))/365) <='" . $params['maxage'] . "'";
            }
            if ($params['minartsvalid'] != '') {
                $whereQuery = " WHERE status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minartsvalid'] . " and " . $params['maxartsvalid'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['mintotalparts'] != '') {
                $whereQuery = " GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['mintotalparts'] . " and " . $params['maxtotalparts'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['minartssent'] != '') {
                $whereQuery = " WHERE status IN ('under_study','disapproved', 'published')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minartssent'] . " and " . $params['maxartssent'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['minpartsrefused'] != '') {
                $whereQuery = " WHERE status IN ('bid_refused')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minpartsrefused'] . " and " . $params['maxpartsrefused'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['minartsrefused'] != '') {
                $whereQuery = " WHERE status IN ('closed')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minartsrefused'] . " and " . $params['maxartsrefused'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['noofdisapproved'] != '') {
                $whereQuery = " WHERE refused_count > 0
                            GROUP BY user_id  HAVING COUNT(user_id) <= " . $params['noofdisapproved'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['category'] != 0) {
                $str1 = " REGEXP ('";
                $find_ini = '';
                foreach ($params['category'] as $catid => $catvalue) {
                    $exp = $catvalue;
                    for ($i = $catvalue; $i < 100; $i++) {
                        $exp .= "|" . ($i + 1);
                    }

                    $str1 .= "(" . $catid . "@" . $exp . ")|";
                    $category1[] = $str1;
                }
                $str1 = substr($str1, 0, -1) . "')";
                $where .= " AND cb.category_more " . $str1 . " AND find_in_set('" . $catid . "', cb.favourite_category)>0";
            }
            if ($params['contribquiz'] != 0) {
                $where .= " AND qp.quiz_id = " . $params['contribquiz'];
            }
            if ($params['language'] != '0') {
                $where .= " AND cb.language = '" . $params['language'] . "'";
            }
            if ($params['language2'] != '0') {
                $langstr1 = " REGEXP ('";
                foreach ($params['language2'] as $lang2id => $lang2value) {
                    $exp = $lang2value;
                    for ($i = $lang2value; $i < 100; $i++) {
                        $exp .= "|" . ($i + 1);
                    }
                    $langstr1 .= "(" . $lang2id . "@" . $exp . ")|";
                    $language2[] = $langstr1;
                }
                $langstr1 = substr($langstr1, 0, -1) . "')";
                $where .= " AND cb.language_more " . $langstr1;
                //$where.= " AND cb.language_more = '".$language2."'";
            }
            if ($params['start_date'] != '' && $params['end_date'] != '') {
                $start_date = str_replace('/', '-', $params['start_date']);
                $end_date = str_replace('/', '-', $params['end_date']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $where .= " AND DATE_FORMAT(u.created_at, '%Y-%m-%d')  BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
            if ($params['act_start_date'] != '' && $params['act_end_date'] != '') {
                $act_start_date = str_replace('/', '-', $params['act_start_date']);
                $act_end_date = str_replace('/', '-', $params['act_end_date']);
                $act_start_date = date('Y-m-d', strtotime($act_start_date));
                $act_end_date = date('Y-m-d', strtotime($act_end_date));
                $where .= " AND DATE_FORMAT(p.created_at, '%Y-%m-%d')  BETWEEN '" . $act_start_date . "' AND '" . $act_end_date . "'";
            }
        } elseif ($params['total_contribs'] == 'yes')
            $where .= " ";
        elseif ($params['never_participated'] == 'yes')
            $where .= " AND u.identifier NOT IN (select user_id from Participation)";
        elseif ($params['never_sent'] == 'yes')
            $where .= " AND u.identifier IN (select user_id from Participation where  status NOT IN ('closed','disapproved','published', 'under_study', 'disapprove_client','closed_client'))";
        elseif ($params['never_validated'] == 'yes') {
            $where .= " AND u.identifier  IN (select user_id from Participation where  status NOT IN
             ('bid','under_study','time_out','validated','published','refused','ignored','disapproved','approved','closed','on_hold','bid_temp','bid_refused_temp','bid_nonpremium_timeout','bid_premium_timeout','disapproved_temp','closed_temp','plag_exec','closed_client_temp','disapprove_client','closed_client'))";
        } elseif ($params['once_validated'] == 'yes')
            $where .= " AND u.identifier  IN (select user_id from Participation where  status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold'))";
        elseif ($params['once_published'] == 'yes')
            $where .= " AND u.identifier  IN (select user_id from Participation where  status IN ('published'))";
        else
            $where = " ";

        $Query = "select  distinct u.identifier,u.email, u.created_at, u.blackstatus, u.profile_type, u.status, up.initial, u.password,
         up.first_name, up.address, up.city, up.state, up.zipcode, up.country, up.phone_number, up.last_name, cb.university,
         cb.dob, cb.profession, cb.favourite_category, cb.category_more, cb.language_more, cb.language, cb.payment_type, cb.self_details from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Contributor cb ON u.identifier=cb.user_id
		LEFT JOIN Participation p ON p.user_id = cb.user_id
		LEFT JOIN QuizParticipation qp ON qp.user_id = cb.user_id
	    where u.type='contributor' " . $where . "";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    // loading the contributors for ajax load //
    public function loadContributor($sWhere, $sOrder, $sLimit, $params)
    {
        $where = '';
        if ($params['searchsubmit'] == 'Search') {
            if ($params['aotitle'] != '0') {
                $contribsIdsList = $this->getContribsInAo($params['aotitle']);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['contrib'] != '0') {
                $where .= " AND u.identifier=" . $params['contrib'];
            }
            if ($params['selfdetails'] != '') {
                $experience = '';
                $experience .= " LEFT JOIN ContributorExperience ce ON u.identifier=ce.user_id ";
                $selfdetailsarr = $params['selfdetails'];
                $search_arr = explode(' ', $selfdetailsarr);
                $count = count($search_arr);
                $or = "";
                $where .= " AND (";
                foreach ($search_arr as $term) {
                    if ($count > 1)
                        $or = "OR";
                    else
                        $or = "  ";
                    $where .= "cb.self_details like '%$term%' OR ce.title like '%$term%' " . $or . " ";
                    $count--;
                }
                $where .= ")";
                // echo $experience; exit;
            }
            if ($params['type'] != '0') {
                $where .= " AND u.profile_type='" . $params['type'] . "'";
            }
            if ($params['type2'] != '0') {
                if ($params['type2'] == 'writer')
                    $where .= " AND u.type2 IS NULL";
                else
                    $where .= " AND u.type2='" . $params['type2'] . "'";
            }
            if ($params['status'] != '0') {
                $where .= " AND u.status='" . $params['status'] . "'";
            }
            if ($params['blacklist'] != '0') {
                $where .= " AND u.blackstatus='" . $params['blacklist'] . "'";
            }
            if ($params['nationalism'] != '') {
                $where .= " AND cb.nationality=" . $params['nationalism'];
            }
            if ($params['minage'] != '') {
                // $where.= " AND cb.dob='".$minage."'";
                $where .= " AND ((DATEDIFF(NOW(),cb.dob))/365) >='" . $params['minage'] . "' AND ((DATEDIFF(NOW(),cb.dob))/365) <='" . $params['maxage'] . "'";
            }
            if ($params['minartsvalid'] != '') {
                $whereQuery = " WHERE status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minartsvalid'] . " and " . $params['maxartsvalid'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['mintotalparts'] != '') {
                $whereQuery = " GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['mintotalparts'] . " and " . $params['maxtotalparts'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['minartssent'] != '') {
                $whereQuery = " WHERE status IN ('under_study','disapproved', 'published')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minartssent'] . " and " . $params['maxartssent'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['minpartsrefused'] != '') {
                $whereQuery = " WHERE status IN ('bid_refused')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minpartsrefused'] . " and " . $params['maxpartsrefused'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['minartsrefused'] != '') {
                $whereQuery = " WHERE status IN ('closed')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minartsrefused'] . " and " . $params['maxartsrefused'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['noofdisapproved'] != '') {
                $whereQuery = " WHERE refused_count > 0
                            GROUP BY user_id  HAVING COUNT(user_id) <= " . $params['noofdisapproved'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['writeravgmarks'] != '') {
                $whereQuery = " WHERE (SELECT avg(ap.marks) FROM Participation p LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id) >= " . $params['writeravgmarks'] . "  GROUP BY user_id";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['correctoravgmarks'] != '') {
                $whereQuery = " WHERE (SELECT avg(ap.marks) FROM Participation p LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id) >= " . $params['correctoravgmarks'] . "  GROUP BY user_id";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['contribquiz'] != 0) {
                $quizz = " LEFT JOIN QuizParticipation qp ON qp.user_id = cb.user_id AND qp.quiz_id = " . $params['contribquiz'];
            }
            if ($params['language'] != '0') {
                $where .= " AND cb.language = '" . $params['language'] . "'";
            }
            /*if($params['language2']!='0')
            {
                $langstr1 = " REGEXP ('";
                foreach($params['language2'] as $lang2id => $lang2value)
                {
                    $exp =  $lang2value;
                    for($i=$lang2value; $i<100; $i++)
                    {
                        $exp.= "|".($i+1);
                    }
                    $langstr1.= "(".$lang2id."@".$exp.")|";
                    $language2[]   = $langstr1;
                }
                $langstr1 = substr($langstr1, 0, -1)."')";
                $where.= " AND cb.language_more ".$langstr1;
                //$where.= " AND cb.language_more = '".$language2."'";
            }*/
            if ($params['start_date'] != '' && $params['end_date'] != '') {
                $start_date = str_replace('/', '-', $params['start_date']);
                $end_date = str_replace('/', '-', $params['end_date']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $where .= " AND DATE_FORMAT(u.created_at, '%Y-%m-%d')  BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
            if ($params['act_start_date'] != '' && $params['act_end_date'] != '') {
                $act_start_date = str_replace('/', '-', $params['act_start_date']);
                $act_end_date = str_replace('/', '-', $params['act_end_date']);
                $act_start_date = date('Y-m-d', strtotime($act_start_date));
                $act_end_date = date('Y-m-d', strtotime($act_end_date));
                $participatewhere = " LEFT JOIN Participation p ON p.user_id = cb.user_id AND DATE_FORMAT(p.created_at, '%Y-%m-%d')  BETWEEN '" . $act_start_date . "' AND '" . $act_end_date . "'";
            }
            if ($params['contributortest'] != '0') {
                $where .= " AND cb.contributortest='" . $params['contributortest'] . "'";
            }
			
			
        } elseif ($params['total_contribs'] == 'yes')
            $where .= " AND u.status='active' AND u.blackstatus='no' ";
        elseif ($params['never_participated'] == 'yes')
            $where .= " AND u.status='active' AND u.blackstatus='no' AND u.identifier NOT IN (select user_id from Participation)";
        elseif ($params['never_sent'] == 'yes')
            $where .= " AND u.status='active' AND u.blackstatus='no' AND u.identifier IN (select user_id from Participation where  status NOT IN ('closed','disapproved','published', 'under_study', 'disapprove_client','closed_client'))";
        elseif ($params['never_validated'] == 'yes') {
            $where .= " AND u.status='active' AND u.blackstatus='no' AND u.identifier  IN (select user_id from Participation where  status NOT IN
             ('bid','under_study','time_out','validated','published','refused','ignored','disapproved','approved','closed','on_hold','bid_temp','bid_refused_temp','bid_nonpremium_timeout','bid_premium_timeout','disapproved_temp','closed_temp','plag_exec','closed_client_temp','disapprove_client','closed_client'))";
        } elseif ($params['once_validated'] == 'yes')
            $where .= " AND u.status='active' AND u.blackstatus='no' AND u.identifier  IN (select user_id from Participation where  status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold'))";
        elseif ($params['once_published'] == 'yes')
            $where .= " AND u.status='active' AND u.blackstatus='no' AND u.identifier  IN (select user_id from Participation where  status IN ('published'))";
        else
            $where = " AND u.status='active' AND u.blackstatus='no'";  
            //$where = " AND u.status='active' AND u.blacklist='no'";
        /* *** added on 15.12.2015 *** */
        if( $params['hanging'] == "yes" || $params['hanging'] == 'no' )
            $where .= " AND cb.hanging = '".$params['hanging']."'";
        elseif($params['hanging'] == "0")
			$where .= " ";
		else
            $where .= " AND cb.hanging = 'no'";
        
        $Query = "select  distinct u.identifier,u.email, u.created_at, u.blackstatus, u.profile_type, u.status, up.initial, u.password,
         CONCAT(up.first_name,' ',up.last_name) as full_name, up.address, up.city, up.state, up.zipcode, up.country, up.phone_number,  cb.university,
         cb.dob, cb.profession, cb.favourite_category, cb.category_more, cb.language_more, cb.language, cb.payment_type,cb.contributortest,cb.contributortestcomment,cb.contributortest,cb.contributortestmarks from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Contributor cb ON u.identifier=cb.user_id" . $experience . " " . $participatewhere . " " . $quizz . "
        where u.type='contributor' " . $where . " " . $sWhere . " " . $sOrder . " " . $sLimit . ""; //echo $Query;
        $result = $this->getQuery($Query, true);
        if ($result != NULL) {
            if ($params['categ'] != '') {
                for ($i = 0; $i < count($result); $i++) {
                    $final_array[$i] = $result[$i];
                    if ($result[$i]['category_more'] != '') {
                        $result[$i]['cates'] = unserialize($result[$i]['category_more']);
                    }
                    for ($j = 0; $j < count($params['categ']); $j++) {
                        $catdetails = explode("=", $params['categ'][$j]);
                        $catindex = $catdetails[0];
                        $catvalue = $catdetails[1];
                        if ($result[$i]['cates'] != '') {
                            if (array_key_exists($catindex, $result[$i]['cates'])) {
                                if ($result[$i]['cates'][$catindex] < $catvalue) {
                                    unset($final_array[$i]);
                                    break;
                                }
                            } else {
                                unset($final_array[$i]);
                                break;
                            }
                        } else {
                            unset($final_array[$i]);
                            break;
                        }
                    }

                }
                //print_r($final_array);exit;
                if ($final_array != NULL) {
                    $cat_result = array_values($final_array);
                }
            } elseif ($params['categ'] == '') {
                $cat_result = $result;
            }
            if ($params['lange'] != '') {                         //print_r($cat_result);
                for ($m = 0; $m < count($cat_result); $m++) {
                    $final_array2[$m] = $cat_result[$m];
                    if ($cat_result[$m]['language_more'] != NULL) {
                        $cat_result[$m]['langes'] = unserialize($cat_result[$m]['language_more']);
                    }
                    for ($n = 0; $n < count($params['lange']); $n++) {
                        $landetails = explode("=", $params['lange'][$n]);
                        $lanindex = $landetails[0];
                        $lanvalue = $landetails[1]; //print_r($cat_result[$m]['langes']);
                        if ($lanindex == $cat_result[$m]['language']) {
                            continue;
                        }
                        if ($cat_result[$m]['langes'] != '') {
                            if (array_key_exists($lanindex, $cat_result[$m]['langes'])) {
                                if ($cat_result[$m]['langes'][$lanindex] < $lanvalue) {
                                    unset($final_array2[$m]);
                                    break;
                                }
                            } else {
                                unset($final_array2[$m]);
                                break;
                            }
                        } else {
                            unset($final_array2[$m]);
                            break;
                        }
                    }
                }
                if ($final_array2 != NULL)
                    return $lang_result = array_values($final_array2);
                else
                    return $lang_result = array();
            }
            return $cat_result;
        } else {

            return "NO";
        }

    }

    /// when searched for contributors we need the total for value of $iTotal variable///
    public function loadedContributorCount($params)
    {
        $where = '';
        if ($params['searchsubmit'] == 'Search') {
            if ($params['aotitle'] != '0') {
                $contribsIdsList = $this->getContribsInAo($params['aotitle']);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['contrib'] != '0') {
                $where .= " AND u.identifier=" . $params['contrib'];
            }
            if ($params['type'] != '0') {
                $where .= " AND u.profile_type='" . $params['type'] . "'";
            }
            if ($params['type2'] != '0') {
                if ($params['type2'] == 'writer')
                    $where .= " AND u.type2 IS NULL";
                else
                    $where .= " AND u.type2='" . $params['type2'] . "'";
            }
            if ($params['status'] != '0') {
                $where .= " AND u.status='" . $params['status'] . "'";
            }
            if ($params['blacklist'] != '0') {
                $where .= " AND u.blackstatus='" . $params['blacklist'] . "'";
            }
            if ($params['nationalism'] != '0') {
                $where .= " AND cb.nationality=" . $params['nationalism'];
            }
            if ($params['minage'] != '') {
                // $where.= " AND cb.dob='".$minage."'";
                $where .= " AND ((DATEDIFF(NOW(),cb.dob))/365) >='" . $params['minage'] . "' AND ((DATEDIFF(NOW(),cb.dob))/365) <='" . $params['maxage'] . "'";
            }
            if ($params['minartsvalid'] != '') {
                $whereQuery = " WHERE status IN ('bid', 'under_study', 'disapproved', 'time_out', 'published', 'closed', 'on_hold')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minartsvalid'] . " and " . $params['maxartsvalid'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['mintotalparts'] != '') {
                $whereQuery = " GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['mintotalparts'] . " and " . $params['maxtotalparts'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['minartssent'] != '') {
                $whereQuery = " WHERE status IN ('under_study','disapproved', 'published')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minartssent'] . " and " . $params['maxartssent'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['minpartsrefused'] != '') {
                $whereQuery = " WHERE status IN ('bid_refused')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minpartsrefused'] . " and " . $params['maxpartsrefused'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['minartsrefused'] != '') {
                $whereQuery = " WHERE status IN ('closed')
                            GROUP BY user_id  HAVING COUNT(user_id) Between " . $params['minartsrefused'] . " and " . $params['maxartsrefused'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }
            if ($params['noofdisapproved'] != '') {
                $whereQuery = " WHERE refused_count > 0
                            GROUP BY user_id  HAVING COUNT(user_id) <= " . $params['noofdisapproved'] . "";
                $contribsIdsList = $this->getContibsIdsForSearch($whereQuery);
                $where .= " AND u.identifier IN (" . $contribsIdsList . ")";
            }

            if ($params['category'] != 0) {
                $str1 = " REGEXP ('";
                $find_ini = '';
                foreach ($params['category'] as $catid => $catvalue) {
                    $exp = $catvalue;
                    for ($i = $catvalue; $i < 100; $i++) {
                        $exp .= "|" . ($i + 1);
                    }

                    $str1 .= "(" . $catid . "@" . $exp . ")|";
                    $category1[] = $str1;
                }
                $str1 = substr($str1, 0, -1) . "')";
                $where .= " AND cb.category_more " . $str1 . " AND find_in_set('" . $catid . "', cb.favourite_category)>0";
            }
            if ($params['contribquiz'] != 0) {
                $where .= " AND qp.quiz_id = " . $params['contribquiz'];
            }
            if ($params['language'] != '0') {
                $where .= " AND cb.language = '" . $params['language'] . "'";
            }
            if ($params['language2'] != '0') {
                $langstr1 = " REGEXP ('";
                foreach ($params['language2'] as $lang2id => $lang2value) {
                    $exp = $lang2value;
                    for ($i = $lang2value; $i < 100; $i++) {
                        $exp .= "|" . ($i + 1);
                    }
                    $langstr1 .= "(" . $lang2id . "@" . $exp . ")|";
                    $language2[] = $langstr1;
                }
                $langstr1 = substr($langstr1, 0, -1) . "')";
                $where .= " AND cb.language_more " . $langstr1;
                //$where.= " AND cb.language_more = '".$language2."'";
            }
            if ($params['start_date'] != '' && $params['end_date'] != '') {
                $start_date = str_replace('/', '-', $params['start_date']);
                $end_date = str_replace('/', '-', $params['end_date']);
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $where .= " AND DATE_FORMAT(u.created_at, '%Y-%m-%d')  BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            }
            if ($params['act_start_date'] != '' && $params['act_end_date'] != '') {
                $act_start_date = str_replace('/', '-', $params['act_start_date']);
                $act_end_date = str_replace('/', '-', $params['act_end_date']);
                $act_start_date = date('Y-m-d', strtotime($act_start_date));
                $act_end_date = date('Y-m-d', strtotime($act_end_date));
                $where .= " AND DATE_FORMAT(p.created_at, '%Y-%m-%d')  BETWEEN '" . $act_start_date . "' AND '" . $act_end_date . "'";
            }
        } else
            $where = "  ";
        $Query = "select  count(distinct u.identifier) AS searchcontribcount from User u
		LEFT JOIN UserPlus up ON u.identifier=up.user_id
		LEFT JOIN Contributor cb ON u.identifier=cb.user_id
		LEFT JOIN Participation p ON p.user_id = cb.user_id
		LEFT JOIN QuizParticipation qp ON qp.user_id = cb.user_id
	    where u.type='contributor' " . $where . " ";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    public function countContributor()
    {
        $Query = "select  COUNT(identifier) AS contcontrib from User  Where type='contributor' ";
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    public function getUsersByGroup($type)
    {
        if ($type != 'all')
            $condition = " AND type='" . $type . "'";
        if ($type == '')
            $condition = "";
        $Query = "select u.identifier, u.email, up.first_name, up.last_name from User u
                LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE status='active' " . $condition;
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    public function UpdateContributortype($contrib, $type)
    {
        $wherec = " identifier='" . $contrib . "'";

        $Carray = array();

        if ($type == "up")
            $Carray['profile_type'] = 'senior';
        elseif ($type == "down")
            $Carray['profile_type'] = 'junior';

        $this->updateQuery($Carray, $wherec);
    }

    public function getBOuser($id)
    {
        $UserQuery = "SELECT login FROM User WHERE identifier='" . $id . "'";
        if (($Userresult = $this->getQuery($UserQuery, true)) != NULL)
            return $Userresult[0]['login'];
        else
            return "NO";
    }

    public function getUsername($user)
    {
        $query = "SELECT u.email,up.first_name,up.last_name, up.country FROM " . $this->_name . " u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.identifier='" . $user . "'";

        if (($result = $this->getQuery($query, true)) != NULL) {
            if ($result[0]['first_name'] != "")
                return $result[0]['first_name'];
            else
                return $result[0]['email'];
        } else
            return "NO";
    }

    /////get all type profiled contributors////
    public function contributorsByType($type)
    {
        $query = "SELECT identifier FROM " . $this->_name . " WHERE status='Active' AND profile_type='" . $type . "'";
        if (($result = $this->getQuery($query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////////get senior contributor list//////////////////////////
    public function  seniorContributors()
    {
        $query = "SELECT identifier FROM " . $this->_name . " WHERE status='Active' AND profile_type='senior'";
        if (($result = $this->getQuery($query, true)) != NULL) {
            for ($i = 0; $i < count($result); $i++) {
                $result[$i] = $result[$i]['identifier'];
            }
            return $result;
        } else
            return "NO";
    }

    /////get all senior profiled contributors////
    public function contributorsByTypeLang($type, $lang)
    {
        $where1 = "";
        if ($lang != 'null' && $lang != '') {
            $lang = implode("','", $lang);
            $where1 .= " AND c.language IN ('" . $lang . "')";
        }
        $Query = "select u.identifier FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id WHERE u.status='Active' AND u.profile_type='" . $type . "' " . $where1;
        if (($result = $this->getQuery($Query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////get all senior contributors except selected contributors to send mail////
    public function scContributorsNotSelected($selectedusers)
    {
        $query = "SELECT identifier FROM " . $this->_name . " WHERE status='Active' AND profile_type='senior' AND identifier NOT IN(" . $selectedusers . ")";
        if (($result = $this->getQuery($query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /////get type of profile of contributors////
    public function getProfileType($userid)
    {
        $query = "SELECT profile_type FROM " . $this->_name . " WHERE identifier='" . $userid . "'";
        //echo  $query."<br>";
        if (($result = $this->getQuery($query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    public function UpdateContributortypeMarks($contrib, $para, $type, $bouser)
    {
        $wherec = " identifier='" . $contrib . "'";

        $Carray = array();

        if ($para == "senior")
            $Carray['profile_type'] = 'senior';
        elseif ($para == "sub-junior")
            $Carray['profile_type'] = 'sub-junior';
        elseif ($para == "junior")
            $Carray['profile_type'] = 'junior';

        $Carray['changestatus_by'] = $bouser;
        $Carray['changestatus_at'] = date('Y-m-d H:i:s');
        $this->updateQuery($Carray, $wherec);
    }

    //Super Client Details
    public function getSuperClientContacts($client_id)
    {
        $query = " SELECT u.identifier,u.email,u.password,up.first_name,up.last_name,c.* From User u
					LEFT JOIN UserPlus up ON up.user_id=u.identifier					
					LEFT JOIN Client c ON c.user_id=u.identifier
				WHERE u.type='chiefodigeo' AND u.superclient_reference='" . $client_id . "'";
        //echo $query;
        if (($count = $this->getNbRows($query)) > 0) {
            $clientContactDetails = $this->getQuery($query, true);
            return $clientContactDetails;
        } else
            return "NO";

    }

    //delete contact of super client
    public function deleteScContact($identifier)
    {
        $where = " identifier='" . $identifier . "'";
        if ($identifier)
            echo $this->deleteQuery($where);

    }

    public function updatePaypercentChangeLog($userId, $adminUserId)
    {
        $this->_name = "Client";
        $clientarr = array();
        $where = " user_id='" . $userId . "' ";
        $clientarr['paypercent_updater'] = $adminUserId;
        $clientarr['paypercent_update_date'] = date('Y-m-d H:i:s', strtotime('now'));
        //echo $where;exit;
        $this->updateQuery($clientarr, $where);
    }

    public function ScUserDetails($userid)
    {
        $query = "SELECT email,password,sc_name FROM " . $this->_name . " WHERE identifier='" . $userid . "'";

        if (($result = $this->getQuery($query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    ////////fetch the bo user stats //////////////
    public function getBoUserStats($params)
    {
        if ($params['statstype'] == 'moyennes') {
            if ($params['userid'] != '') {
                $condition = "   d.created_user = '" . $params['userid'] . "'";
            }
            if ($params['lang'] != '') {
                $condition = "   d.created_user = '" . $params['userid'] . "' AND a.language = '" . $params['lang'] . "' ";
            }
            if ($params['categ'] != '') {
                $condition = "   d.created_user = '" . $params['userid'] . "' AND a.category = '" . $params['categ'] . "' ";
            }
        }
        $query = "SELECT ap.marks FROM User u
                 INNER JOIN Participation p ON u.identifier=p.user_id
                 INNER JOIN Article a ON a.id = p.article_id
                 INNER JOIN Delivery d ON d.id = a.delivery_id
                 LEFT JOIN ArticleProcess ap ON p.id=ap.participate_id
                 WHERE " . $condition;
        // echo  $query; //exit;

        if (($result = $this->getQuery($query, true)) != NULL)
            return $result;
        else
            return "NO";

    }

    /** Check encrypted Email and Password Details and Auto Login in to Site ***/
    public function checkEmailLoginDetails($email, $password, $type)
    {
        $whereQuery = "md5(CONCAT('ep_login_',login)) = '" . $email . "' and md5(CONCAT('ep_login_',password))='" . $password . "'
                            and status='Active' and menuId = '' ";
        $loginQuery = "select * from " . $this->_name . " where " . $whereQuery;

        //   echo $loginQuery;

        if (($result = $this->getQuery($loginQuery, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    public function getclientcontact($clientid)
    {
        $contactQuery = "SELECT u.identifier,u.email,u.password,up.first_name,up.last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.client_reference='" . $clientid . "'";
        $contactresult = $this->getQuery($contactQuery, true);
        return $contactresult;
    }

    public function ListallBousers()
    {
        $bouserQuery = "SELECT u.identifier,u.email,up.first_name,up.last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.type NOT IN ('client','contributor','chiefodigeo','clientcontact')";
        $bouserresult = $this->getQuery($bouserQuery, true);
        return $bouserresult;
    }

    ///// get the mission related statistics /////
    public function  extractData($userid)
    {
        $query1 = "SELECT  p.id, SUM(ap.marks) as s1marks, avg(ap.marks) as s1avg, GROUP_CONCAT(ap.comments SEPARATOR  '||') AS s1comt, COUNT(p.id) AS s1total
                        FROM Participation p LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id WHERE p.user_id = '" . $userid . "' AND ap.stage = 's1' AND ap.marks != ''";
        $query2 = "SELECT   p.id, SUM(ap.marks) as s2marks, avg(ap.marks) as s2avg , GROUP_CONCAT(ap.comments SEPARATOR  '||') AS s2comt, COUNT(p.id) AS s2total
                        FROM  Participation p LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id WHERE p.user_id = '" . $userid . "' AND ap.stage = 's2' AND ap.marks != ''";
        $query3 = "SELECT   p.id,  SUM(ap.marks) as crtmarks, avg(ap.marks) as crtavg , GROUP_CONCAT(ap.comments SEPARATOR  '||') AS crtcomt, COUNT(p.id) AS crttotal
                        FROM  Participation p LEFT JOIN ArticleProcess ap ON ap.participate_id = p.id WHERE p.user_id = '" . $userid . "' AND ap.stage = 'corrector' AND ap.marks != ''";

        $result1 = $this->getQuery($query1, true);
        $result2 = $this->getQuery($query2, true);
        $result3 = $this->getQuery($query3, true);
        $result4 = array_merge($result1, $result2, $result3);
        return $result4;
    }

    //get users from their groupids///
    public function getUserDetailOnGrpIds($grparray)
    {
        $grpidstr = $aos = implode("','", $grparray);
        $bouserQuery = "SELECT u.identifier,u.email,up.first_name,up.last_name, u.type FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.parentId is null AND u.groupId IN ('" . $grpidstr . "')";
        $bouserresult = $this->getQuery($bouserQuery, true);
        return $bouserresult;
    }

    //get child users////
    public function getUserChildren($parentId)
    {
        $bouserQuery = "SELECT u.identifier,u.email,up.first_name,up.last_name, u.type FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.parentId='" . $parentId . "' ";
        $bouserresult = $this->getQuery($bouserQuery, true);
        return $bouserresult;
    }

    //get child users////
    public function getChildUsers()
    {
        //$bouserQuery = "SELECT u.identifier,u.email,up.first_name,up.last_name, u.type, u.groupId, u.parentId FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.status = 'Active' AND u.type NOT IN ('contributor','client')";
        $bouserQuery = "SELECT u.identifier,u.email,up.first_name,up.last_name, u.type, u.groupId, u.parentId FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE u.status = 'Active' AND u.type IN ('superadmin','salesuser')";
        $bouserresult = $this->getQuery($bouserQuery, true);
        return $bouserresult;
    }

    public function getEmailUser($user)
    {
        $query = "SELECT email,login,first_name,last_name FROM User u LEFT JOIN UserPlus up ON u.identifier=up.user_id WHERE identifier='" . $user . "'";
        if (($result = $this->getQuery($query, true)) != NULL)
            return $result;
        else
            return "NO";
    }

    /********* create bulk *********/
    public function CreateBulkContribUser($conarray)
    {
        $this->_name = "User";
        $uarr = array();
        $uarr['email'] = trim($conarray['email']);
        $uarr['password'] = $conarray['writerpassword'];
        $uarr['type'] = 'contributor';
        $uarr['status'] = 'Active';
        $uarr['verified_status'] = 'YES';
        $uarr['created_by'] = 'backend';
        $uarr['created_user'] = $conarray['created_user'];
        $uarr['profile_type'] = 'sub-junior';
        $uarr['subscribe'] = 'yes';
        $uarr['alert_subscribe'] = 'yes';
        $uarr['identifier'] = $this->getIdentifier();
        $this->insertQuery($uarr);

        $this->_name = "UserPlus";
        $userarr = array();
        $userarr['user_id'] = $uarr['identifier'];
        $this->insertQuery($userarr);

        $this->_name = "Contributor";
        $contribarr = array();
        $contribarr['user_id'] = $uarr['identifier'];
        $contribarr['language'] = $conarray['writerlanguage'];
        $this->insertQuery($contribarr);

    }



    /* by naseer on 06.08.2015  */
    /*public function checkRowExist($table,$where){
        $query = "SELECT COUNT(*) AS COUNT FROM ".$table." WHERE ".$where;
        $result = $this->getQuery($query, true);
        if($result[0][COUNT] === '0')
            return false;
        else
            return true;
    }*/
    public function checkCreateUserPlus($user_id){
        $this->_name = 'UserPlus';
        $query = "SELECT COUNT(*) AS COUNT FROM $this->_name WHERE `user_id` = '".$user_id."'";
        $result = $this->getQuery($query, true);
        if($result[0][COUNT] === '0'){
            $default['user_id'] = $user_id;
            $this->insertQuery($default);
            return true;
        }
        else
            return true;
    }
    public function checkCreateContributor($user_id){
        $this->_name = 'Contributor';
        $query = "SELECT COUNT(*) AS COUNT FROM $this->_name WHERE `user_id` = '".$user_id."'";
        $result = $this->getQuery($query, true);
        if($result[0][COUNT] === '0'){
            $default['user_id'] = $user_id;
            $this->insertQuery($default);
            return true;
        }
        else
            return true;

    }
    /*added by naseer on 11-11-2015*/
    // adding new function to be compactible with userlogs//

    public function updateContribBoOnlyUser($userId,$uarr){
        //echo "<pre>";print_r($conarray);exit;
        /* user table update */
        $this->_name = "User";
        $where2 = " identifier='" . $userId . "' ";
        $result = $this->updateQuery($uarr, $where2);
        if($result === '1')
            return true;
        else
            return fasle;
    }
    public function updateContribBoOnlyContrib($userId,$contribarr){
            /* Contributor table updates */
        $this->_name = "Contributor";
        $where1 = " user_id='" .$userId. "' ";
            //Contributor test
        $result = $this->updateQuery($contribarr, $where1);
        if($result === '1')
            return true;
        else
            return fasle;
    }
    public function updateContribUserBasicUserPlus($userId,$userarr)
    {
        //echo "<pre>";print_r($conarray);exit;
        $where1 = " user_id='" . $userId . "' ";
        //userPlus updates//
        $this->_name = "UserPlus";
        $result = $this->updateQuery($userarr, $where1);
        if($result === '1')
            return true;
        else
            return fasle;

    }
    public function updateContribUserBasicContributor($userId,$contribarr)
    {
        $where1 = " user_id='" . $userId . "' ";
        // contributor table updates//
        $this->_name = "Contributor";
        $result = $this->updateQuery($contribarr, $where1);
        if($result === '1')
            return true;
        else
            return fasle;
    }
    public function updateContribUserBasicUser($userId,$uarr){
        //User table updates//
        $where2 = " identifier='" . $userId . "' ";
        $this->_name = "User";
        $result = $this->updateQuery($uarr, $where2);
        if($result === '1')
            return true;
        else
            return fasle;
    }
    public function updateContribCategoriesAndLang($userId,$contribarr)
    {
        // contributor table updates//
        $where1 = " user_id='" . $userId . "' ";
        $this->_name = "Contributor";

        $result = $this->updateQuery($contribarr, $where1);
        if($result === '1')
            return true;
        else
            return fasle;
    }
    public function updateContribPaymentInfo($userId,$contribarr){
        //echo "<pre>";print_r($conarray);exit;
        // contributor table updates//
        /* *Inserting Pay info details**/
        // contributor table updates//
        $this->_name = "Contributor";
        $where1 = " user_id='" . $userId . "' ";

        $result = $this->updateQuery($contribarr, $where1);
        if($result === '1')
            return true;
        else
            return fasle;
    }
    public function updateContribMoreInfo($userId,$contribarr){
        //echo "<pre>";print_r($conarray);exit;
        // contributor table updates//
        $this->_name = "Contributor";
        $where1 = " user_id='" . $userId . "' ";

        $result = $this->updateQuery($contribarr, $where1);
        if($result === '1')
            return true;
        else
            return fasle;
    }

    /* added by naseer on 29-09-2015  */
    //update Bo user's last vist time//
    public function updateLastLogin($userId){
        $this->_name = "User";
        $where = " identifier ='" . $userId . "' ";
        $updataArray['last_visit'] = date('Y-m-d H:i:s');
        $this->updateQuery($updataArray, $where);
    }
    /* end of added by naseer on 29-09-2015  */
    /* added by naseer on 10-11-2015 */
    //fetch the user type//
    public function getUserType($user_id)
    {
        $query = "SELECT `type` FROM `User` WHERE `identifier`='$user_id'";
        if(($result = $this->getQuery($query,true)) != NULL)
            return $result[0]['type'];
        else
            return "NO";
    }

    /* end of added by naseer on 10-11-2015 */
	
	 /////get all senior profiled contributors////
    public function contributorsByTypeLangTranslation($type, $lang, $sourcelang,$srcnocheck) 
    {
        $where1 = "";
        if ($lang != 'null' && $lang != '') {
            $lang = implode("','", $lang);
            $where1 .= " AND c.language IN ('" . $lang . "')";
        }
        $Query = "select u.identifier,c.language_more FROM User u LEFT JOIN Contributor c ON u.identifier=c.user_id WHERE u.status='Active' AND  c.translator='yes' AND c.translator_type='" . $type . "' " . $where1;
		
        if (($result = $this->getQuery($Query, true)) != NULL)
		{
            if($srcnocheck=='yes')
				return $result;
			else
			{
				$resultall_new=array();
				foreach($result as $writer)
				{
					$sourcearray=unserialize($writer['language_more']);
					if($sourcearray[$sourcelang]>=50)
						$resultall_new[]=$writer;
				}
				return $resultall_new;
			}
		}
        else
            return "NO";
    }
    /**Author:Thilagam**/
    /**Date:24/5/2016**/
    /**Function:To get the list of extracted Contributors**/
    public function downloadContributorXls()
    {
        /*$query = "SELECT 
		User.identifier,UserPlus.first_name, UserPlus.last_name, User.created_at, User.email, User.profile_type,User.last_visit, 
		Contributor.translator, Contributor.language, Contributor.language_more,Contributor.writer_preference,Contributor.translator,Contributor.translator_type,Contributor.tva_number,
		(SELECT count(Participation.user_id) from Participation WHERE Participation.user_id = User.identifier ) as times,
		(SELECT Participation.created_at from Participation WHERE Participation.user_id = User.identifier GROUP BY User.identifier ORDER BY Participation.created_at DESC ) as last,
        (SELECT count(Participation.user_id) from Participation WHERE Participation.user_id = User.identifier and Participation.status = 'published' and Participation.current_stage = 'client') as selected,
        (SELECT max(Participation.updated_at) from Participation WHERE Participation.user_id = User.identifier and Participation.status = 'published' and Participation.current_stage = 'client' GROUP BY User.identifier ORDER BY Participation.updated_at DESC) as select_date,
		(select sum(price) from Royalties where user_id= User.identifier ) as royalty
		FROM User 
		LEFT JOIN UserPlus on User.identifier = UserPlus.user_id
		LEFT JOIN Contributor on UserPlus.user_id = Contributor.user_id
		LEFT JOIN Participation on UserPlus.user_id = Participation.user_id
		where User.type = 'contributor' 
		GROUP BY User.identifier
		ORDER BY Participation.created_at DESC" ;*/
        $query = "SELECT 
        User.identifier,UserPlus.first_name, UserPlus.last_name, User.created_at, User.email, User.profile_type,User.last_visit, 
        Contributor.translator, Contributor.language, Contributor.language_more,Contributor.writer_preference,Contributor.translator,Contributor.translator_type,Contributor.tva_number,
        count(Participation.user_id) as times,Participation.created_at as last,
        (select sum(price) from Royalties where user_id= User.identifier ) as royalty
        FROM User 
        LEFT JOIN UserPlus on User.identifier = UserPlus.user_id
        LEFT JOIN Contributor on UserPlus.user_id = Contributor.user_id
        LEFT JOIN Participation on UserPlus.user_id = Participation.user_id
        where User.type = 'contributor' 
        GROUP BY User.identifier
        ORDER BY Participation.created_at DESC";
		$result = $this->getQuery($query, true);
		return $result;
    }
}

