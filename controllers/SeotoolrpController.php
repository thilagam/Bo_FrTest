<?php
/**
 * SeotoolController
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Action.php';

class SeotoolrpController extends Ep_Controller_Action {
	
    private $text_admin;
    var $type; // 
    var $format; // Position format
    var $option; // Options
    var $domain; // Domain name
    var $competitor;
    var $client; // Client name
    var $title; // Title
    var $days; // Frequency days
    var $end_date; // end date
    var $frequency_option; // Frequency option
    var $output_type; // Output type
    var $site_id; // Site id
    var $limit;
    var $contract;
    var $from_date;
    var $to_date;
    var $ssh2_server;
    var $ssh2_user_name;
    var $ssh2_user_pass;
    var $gsuggest_excel_array;
    var $cron_run_time;
    var $cron_email;
    var $os;
    var $sheet_count;

    public function init() {

        parent::init();
        
        $this->_view->lang = $this->_lang; // Site language
        $this->adminLogin = Zend_Registry::get('adminLogin'); // Get admin login info from zend registry 
        $this->sid = session_id();

        /*server details**/
        $this->ssh2_server = SSH2_SERVER; // host name
        $this->ssh2_user_name = SSH2_USER; // username
        $this->ssh2_user_pass = SSH2_PWD; // password
        
        // Upload file directory
        $this->seo_upload_files = Zend_Registry::get('seo_upload_files');
        
        // operating system
        $this->os = $this->getOS($_SERVER['HTTP_USER_AGENT']);
    }
    
    /* Position tool upload action */
    public function posssh2uploadAction() {
        
        $pos_params = $this->_request->getParams();
        
        if (isset($pos_params['submit'])) {
			
            // Response message
            $response = $this->responseMsg('', 0, 1, '', 0);
            
            $word_type = $pos_params['word_type']; // word type(text or file)

            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file

            $this->type = $pos_params['type'];
            $this->option = $pos_params['option'];
            $this->domain = $pos_params['domain_type'];
            $this->competitor = $pos_params['comp_type'];
            $this->client = $pos_params['client']; // Client name
            $this->title = $pos_params['title']; // Title for position
            $this->days = ((sizeof($pos_params['day'])>0) ? implode("|", $pos_params['day']) : '');
            $this->end_date = $pos_params['enddate']; // end date
            $this->frequency_option = $pos_params['frequency']; // frequency check
            $this->output_type = $pos_params['op_type']; // Results output format
            $this->site_id = $pos_params['site']; // site id
            $this->limit = $pos_params['limit']; // Result limit

            if ($pos_params['posSchedule'] == 1) :
            
                $this->cron_run_time = trim($pos_params['scheduleDate']); // Cron schedule date
                $this->cron_email = trim($pos_params['scheduleEmail']); // email to send

                if (empty($this->cron_run_time) || empty($this->cron_email))
                    $response = $this->responseMsg(0, 21); // Response message
                elseif (empty($this->client) || empty($this->title))
                    $response = $this->responseMsg(0, 22); // Response message

                if ($response['type'] == 'error') :
                    print json_encode($response); // Resonse array encoded in json format
                    exit ;
                endif ;
            endif ;

            if ($word_type == 2) {
                $kw_text = trim($pos_params['kw']);
                if (($this->os == 'Windows'))
                    $kw_text = utf8_decode($kw_text);

                if ($kw_text && $this->type) {
                    $kw_text1 = explode("\n", $kw_text);
                    $csv_file_name = "csv_" . time() . ".csv"; // Input csv file
                    $srcFile = SEO_UPLOAD_POS . $csv_file_name;
                    
                    // creating csv file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, str_replace("\'", "'", $kw_text));
                    fclose($fp);

                    $frequency = $this->checkFrequency(); // frequency check
                    
                    if ($frequency == 'process') {
                        if ($pos_params['posSchedule'] == 1)
                            $response = $this->posscheduleuploadAndProcess($srcFile, $csv_file_name); // Processing position tool for schedule
                        else
                            $response = $this->posuploadAndProcess($srcFile, $csv_file_name); // Processing position tool 
                    } else {
						// Response message
                        $response = $this->responseMsg(0, 0, $word_type, $frequency);
                    }
                    $response['word_type'] = $word_type; // word type(text or file)
                } else {
                    if (!$kw_text)
                        $response = $this->responseMsg(0, 3, $word_type); // Response message
                    elseif (!$this->type)
                        $response = $this->responseMsg(0, 2, $word_type); // Response message
                }
            } else if ($word_type == 1) {
                if ((($_FILES['keyword_file']['type'] == 'text/comma-separated-values') || ($_FILES['keyword_file']['type'] == 'text/csv') || ($_FILES['keyword_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['keyword_file']['type'] == 'application/x-msexcel') || ($_FILES['keyword_file']['type'] == 'application/xls')) && $this->type) {
                    $file_info = pathinfo($_FILES['keyword_file']['name']);
                    $extension = $file_info['extension'];

                    if ($extension == 'xls') {
						
						// Read xls file to an array
                        $xls_array = $this->readInXLS($_FILES['keyword_file']['tmp_name']);
                        $u_file_name = str_replace(" ", "_", $file_info['filename']) . ".csv"; // Input csv file
                        $srcFile = SEO_UPLOAD_POS . $u_file_name;
                        $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                    } else {
                        $srcFile = $_FILES['keyword_file']['tmp_name'];
                        $u_file_name = str_replace(" ", "_", $_FILES['keyword_file']['name']);
                    }

                    $frequency = $this->checkFrequency(); // frequency check

                    if ($frequency == 'process') {
                        if ($pos_params['posSchedule'] == 1)
                            $response = $this->posscheduleuploadAndProcess($srcFile, $u_file_name); // Processing position tool  for schedule
                        else
                            $response = $this->posuploadAndProcess($srcFile, $u_file_name); // Processing position tool 
                    } else {
						// Response message
                        $response = $this->responseMsg(0, 0, $word_type, $frequency);
                    }
                    $response['word_type'] = $word_type; // word type(text or file)
                } else {
                    if (!$_FILES['keyword_file']['tmp_name'])
                        $response = $this->responseMsg(0, 1, $word_type); // Response message
                    elseif (!$this->type)
                        $response = $this->responseMsg(0, 2, $word_type); // Response message
                }
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }
    /* Position tool 2 upload action */
    public function posssh2upload235Action() {
		
        $pos_params = $this->_request->getParams();
        if (isset($pos_params['submit'])) {

            $response = array('type' => '', 'message' => '');
            
            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file

            $word_type = $pos_params['word_type']; // word type(text or file)
            $this->title = trim($pos_params['title']); // Title for position
            $this->output_type = $pos_params['op_type']; // Results output format
            $this->site_id = $pos_params['site']; // site id
            $this->type = $pos_params['type'];
            $this->format = 1;
            $this->limit = $pos_params['limit']; // Result limit

            if ($word_type == 2) {
                $url_text = trim($pos_params['url_text']);
                if ($this->type == 4 || $this->type == 5)
                    $comp_url_text = trim($pos_params['comp_url_text']);

                $kw_text = trim($pos_params['kw']);

                if ($this->os == 'Windows') {
                    $kw_text = utf8_decode($kw_text);
                    $url_text = utf8_decode($url_text);
                    if ($this->type == 4 || $this->type == 5)
                        $comp_url_text = utf8_decode($comp_url_text);
                }

                if ($kw_text && $url_text) {
                    $kws = explode("\n", $kw_text);
                    $kwtext = $url_text . ($comp_url_text ? (';' . $comp_url_text) : '');
                    foreach ($kws as $kw) {
                        $kwtext .= ';' . trim($kw);
                    }

                    $csv_file_name = "textArea_" . str_replace(' ', '_', $this->title) . time() . ".csv"; // Input csv file
                    $srcFile = SEO_UPLOAD_POS . $csv_file_name;
                    
                    // creating csv file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, $kwtext);
                    fclose($fp);

                    $frequency = $this->checkFrequency(); // frequency check

                    if ($frequency == 'process') {
						 // Processing position tool 
                        $response = $this->posuploadAndProcess($srcFile, $csv_file_name);
                    } else {
						// Response message
                        $response = $this->responseMsg(0, 0, $word_type, $frequency);
                    }
                    $response['word_type'] = $word_type; // word type(text or file)
                } else {
					// Response message
                    $response = $this->responseMsg(0, 3, $word_type);
                }
            } else if ($word_type == 1) {
                if ((($_FILES['keyword_file']['type'] == 'text/comma-separated-values') || ($_FILES['keyword_file']['type'] == 'text/csv') || ($_FILES['keyword_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['keyword_file']['type'] == 'application/x-msexcel') || ($_FILES['keyword_file']['type'] == 'application/xls')) && $this->type) {
                    $file_info = pathinfo($_FILES['keyword_file']['name']);
                    $extension = $file_info['extension'];

                    if ($extension == 'xls') {
						// Read xls file to an array
                        $xls_array = $this->readInXLS($_FILES['keyword_file']['tmp_name']);
                        $u_file_name = str_replace(" ", "_", $file_info['filename']) . ".csv"; // Input csv file
                        $srcFile = SEO_UPLOAD_POS . $u_file_name;
                        $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                    } else {
                        $srcFile = $_FILES['keyword_file']['tmp_name'];
                        $u_file_name = str_replace(" ", "_", $_FILES['keyword_file']['name']);
                    }

                    $csvArr = array();
                    
                    // Reading csv data to store into an array
                    foreach ($this->getCSV($srcFile) as $key => $val) :
                        array_push($csvArr, $val[0]);
                    endforeach;

                    $url_text = trim($csvArr[0]);
                    unset($csvArr[0]);

                    if ($this->type == 4 || $this->type == 5) :
                        $comp_url_text = trim($csvArr[1]);
                        unset($csvArr[1]);
                    endif ;

                    $kws = array_unique($csvArr);

                    $kwtext = $url_text . ($comp_url_text ? (';' . $comp_url_text) : '');
                    foreach ($kws as $kw) {
                        $kwtext .= ';' . trim($kw);
                    }

                    if ($this->os == 'Windows') {
                        $kwtext = utf8_decode($kwtext);
                        $url_text = utf8_decode($url_text);
                        if ($this->type == 4 || $this->type == 5)
                            $comp_url_text = utf8_decode($comp_url_text);
                    }
					
					// creating csv file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, $kwtext);
                    fclose($fp);

                    $frequency = $this->checkFrequency(); // frequency check

                    if ($frequency == 'process') {
						 // Processing position tool 
                        $response = $this->posuploadAndProcess($srcFile, $u_file_name);
                    } else {
						// Response message
                        $response = $this->responseMsg(0, 0, $word_type, $frequency);
                    }
                    $response['word_type'] = $word_type; // word type(text or file)
                } else {
                    if (!$_FILES['keyword_file']['tmp_name'])
                        $response = $this->responseMsg(0, 1, $word_type); // Response message
                    elseif (!$this->type)
                        $response = $this->responseMsg(0, 2, $word_type); // Response message
                }
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }
    /* Position tool action (Interface and results view) */
    public function positionAction() {
        $pos_params = $this->_request->getParams();
        
        // Download processed file.
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($pos_params['file']) && isset($pos_params['ext']))
            $this->posdownloadFile($pos_params['file'], $pos_params['ext']);

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($pos_params['file']) && isset($pos_params['ext'])) {
			
            $filename = $pos_params['file'] . "." . $pos_params['ext'];
            $path_file = SEO_DOWNLOAD_POS . $filename;

            if (file_exists($path_file)) {
                $data = $this->getCSV($path_file); // Read csv file to an array
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), 'spl') ;
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $pos_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
            
        } else {

            if (@$pos_params['class'])
                $this->_view->class = $pos_params['class']; // For meassage type

            $_POST['word_type'] = 1;
            $this->_view->word_type = $pos_params['word_type']; // word type(text or file)

            if (@$msg)
                $this->_view->msg = $msg;
            $client_info_obj = new Ep_User_User();
            $client_info = $client_info_obj->GetclientList(); // Getting all client list from database
            $client_list = array();

            for ($c = 0; $c < count($client_info); $c++) {
                $client_list[$c]['identifier'] = $client_info[$c]['identifier'];

                $name = $client_info[$c]['email']; // Client email id
                $nameArr = array($client_info[$c]['company_name'], $client_info[$c]['first_name'], $client_info[$c]['last_name']);
                $nameArr = array_filter($nameArr);
                if (count($nameArr) > 0)
                    $name .= "(" . implode(", ", $nameArr) . ")";

                $client_list[$c]['name'] = strtoupper($name);
            }
            asort($client_list);
            $this->_view->client_list = $client_list;
            
            // Processes a view script and returns the output.
            $this->render("seotool_position");
        }
    }
    /* Position tool 2 action (Interface and results view) */
    public function position2Action() {
        $pos_params = $this->_request->getParams();
        
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($pos_params['file']) && isset($pos_params['ext']))
            $this->posdownloadFile($pos_params['file'], $pos_params['ext']);

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($pos_params['file']) && isset($pos_params['ext'])) {
			
            $filename = $pos_params['file'] . "." . $pos_params['ext'];
            $path_file = SEO_DOWNLOAD_POS . $filename;

            if (file_exists($path_file)) {
                $data = $this->getCSV($path_file); // Read csv file to an array
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), 'spl') ;
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $pos_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
            
        } else {

            if (@$pos_params['class'])
                $this->_view->class = $pos_params['class'];

            $this->_view->type = $pos_params['type'];
            $this->_view->limit = $pos_params['limit']; // Result limit

            if (@$msg)
                $this->_view->msg = $msg;
                
			// Processes a view script and returns the output.
            $this->render("seotool_position1");
            
        }
    }
    /* CSV file contents to array */
    function getCSV($file) {
        setlocale(LC_ALL, 'fr_FR');
        $data_array = array();
        $row = 1;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $num = count($data);

                for ($c = 0; $c < $num; $c++) {
                    $data_array[$row][$c] = $data[$c];
                }
                $row++;
            }
            fclose($handle);
        }
        return $data_array;
    }

    //function to check frequency
    function checkFrequency() {
        $error = '';
        if ($this->frequency_option == 1) {
            if (!$this->client)
                $error .= 'Please enter client name.<br>';
            if (!$this->title)
                $error .= 'Please enter title.<br>';
            if (!$this->days)
                $error .= 'Please select atleast one frequency day.<br>';
            if (!$this->end_date) // end date
                $error .= 'Please select end date of frequency.<br>';

            if ($error)
                return $error;
            else
                return "process";
        } else
            return "process";
    }

    //function to check frequency
    function checkSearchFrequency() {
        $error = '';

        if (!$this->client)
            $error .= 'Please Select client .<br>';
        if (!$this->contract)
            $error .= 'Please Select contract.<br>';
        if (!$this->from_date)
            $error .= 'Please select from date.<br>';
        if (!$this->to_date)
            $error .= 'Please select end date.<br>';
        if (!$this->days)
            $error .= 'Please select any one of the frequency day.<br>';

        if ($error)
            return $error;
        else
            return "process";
    }

    /* Position tool function to connect to the linode server, uploading the csv/xls and processing the output file **/
    function posuploadAndProcess($srcFile, $u_file_name) {
        try {
            /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            //Path to execute ruby command
            $file_exec_path = $sftp->exec(SEO_POSITION_EXEC);
            
            switch($this->type) {
                case 5 :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD5); // seo input file upload path
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD5); // output file download path
                    break;
                case 4 :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD4); // seo input file upload path
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD4); // output file download path
                    break;
                case 3 :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD3); // seo input file upload path
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD3); // output file download path
                    break;
                case 2 :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD2);
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD2); // output file download path
                    break;
                default :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD); // seo input file upload path
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD); // output file download path
                    break;
            }

            if($this->frequency_option==1)
            {
                $file_upload_path = $sftp->exec(SEO_FREQUENCY_UPLOAD); // seo input file upload path
                $file_download_path = $sftp->exec(SEO_FREQUENCY_DOWNLOAD); // output file download path
            }

            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp

            /**passing file name**/
            $src = pathinfo($u_file_name);
            $download_fname = $src['filename'] . "_" . time();
            $dstfile = $download_fname . "." . $src['extension'];

            switch($this->type)
            {
				 // Ruby file for processing
                case 5:
                    $ruby_file = SEO_POSITION_RB5;
                    break;
                case 4:
                    $ruby_file = SEO_POSITION_RB4;
                    break;
                case 3:
                    $ruby_file = SEO_POSITION_RB3;
                    break;
                case 2:
                    $ruby_file = SEO_POSITION_RB2;
                    break;
                default:
                    $ruby_file = SEO_POSITION_RB;
                    break;
            }

            $encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');
            $limitt = $this->limit; // Result limit
            $clientt = $this->client; // Client name
            $titlee = $this->title; // Title for position
            $dayss = $this->days;
            $end_datee = $this->end_date; // end date
            $site_idd = $this->site_id; // site id
            $format = $this->format ? 2 : 1;
            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId;  // Logged in userid

			// Ruby command for data processing
            if ($this->frequency_option == 1)
                $cmd = "$ruby_file $site_idd $u_file_name $dstfile $limitt \'$clientt\' \'$titlee\' \"$dayss\" \"$end_datee\" \"$encoding\" $userId $loginName 2>&1 ";
            else
                $cmd = "$ruby_file $site_idd $u_file_name $dstfile $limitt \"$encoding\" \"$format\" $userId $loginName 2>&1 ";

            $sftp->setTimeout(300); // sftp timeout
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
            
            $remoteFile = trim($file_download_path) . "/" . $dstfile;
            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_POS . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];
            $sftp->get($dstfile, $localFile); //downloading result file from linode server(using sftp)
            
            if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
                $csv_data = $this->getCSV($localFile); // Read csv file to an array
                if ($this->output_type == 2) {
                    $ext = "xls";
                    $output_file = SEO_DOWNLOAD_POS . $fname . "." . $ext;

                    $this->WriteXLS($csv_data, $output_file); // Output file in xls format
                }
                $posAction = $this->format ? 'position2' : 'position';
                $typeParam = ($this->type && $this->format) ? '&type=' . $this->type : '';
                
                // Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1."-".$cmd, BO_PATH . "/download_seo_position.php?saction=download&file=" . $fname . "&ext=" . $ext, SEO_DOWN_OP_FILE, $posAction . '?action=view&file=' . $fname . $typeParam . '&ext=csv', SEO_VIEW_RESULTS);
                
            } else if (trim($output) == SEO_RVM_NOTATION && $frequency_option == 1) {
				// Response message
                $response = $this->responseMsg(1, 6);
            } else {
                throw new Exception($output);
            }
        } catch(Exception $e) {
			// Response message
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage()));
        }
        return $response;
    }

    /* Position schedule tool function to connect to the linode server, uploading the csv/xls 
     * and processing the output file **/
    function posscheduleuploadAndProcess($srcFile, $u_file_name) {
        try {
            /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            //Path to execute ruby command
            $file_exec_path = $sftp->exec(SEO_POSITION_EXEC);
            
            switch($this->type) {
                case 5 :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD5); // seo input file upload path
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD5); // output file download path
                    break;
                case 4 :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD4); // seo input file upload path
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD4); // output file download path
                    break;
                case 3 :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD3); // seo input file upload path
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD3); // output file download path
                    break;
                case 2 :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD2); // seo input file upload path
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD2); // output file download path
                    break;
                default :
                    $file_upload_path = $sftp->exec(SEO_POSITION_UPLOAD); // seo input file upload path
                    $file_download_path = $sftp->exec(SEO_POSITION_DOWNLOAD); // output file download path
                    break;
            }

            if($this->frequency_option==1)
            {
                $file_upload_path = $sftp->exec(SEO_FREQUENCY_UPLOAD); // seo input file upload path
                $file_download_path = $sftp->exec(SEO_FREQUENCY_DOWNLOAD); // output file download path
            }

            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp

            /**passing file name**/
            $src = pathinfo($u_file_name);
            $download_fname = $src['filename'] . "_" . time();
            $dstfile = $download_fname . "." . $src['extension'];
            $encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');

            $limitt = $this->limit; // Result limit
            $clientt = 'client name';
            $titlee = $this->title; // Title for postion
            $dayss = $this->days;
            $end_datee = $this->end_date; // end date
            $site_idd = $this->site_id; // site id
            $format = $this->format ? 2 : 1;
            $cron_run_time = str_replace('/', '-', $this->cron_run_time);
            $cron_email = $this->cron_email; // cron email

            switch($this->type)
            {
                case 5:
                    $option = 12;
                    break;
                case 4:
                    $option = 11;
                    break;
                case 3:
                    $option = 10;
                    break;
                case 2:
                    $option = 7;
                    break;
                default:
                    $option = 1;
                    break;
            }
            
            $ruby_file = SEO_POSITION_SCHEDULE_RB; // Ruby file for processing
            
            // Ruby command for data processing
            $cmd = "$ruby_file $site_idd $u_file_name $dstfile \"$clientt\" \"$titlee\" $option \"$encoding\" \"$cron_run_time\" \"$cron_email\"";

            $sftp->setTimeout(300); // sftp timeout
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");

            /**processed file path**/
            $remoteFile = trim($file_download_path) . "/" . $dstfile;
            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_POS . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];

            //downloading the file from remote server
            $sftp->get($dstfile, $localFile);
            
            $response = $this->responseMsg(1, 17); // Response message
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
        }
        return $response;
    }
    
    /* Function to find the operating system */
    function getOS($userAgent) {
        // Create list of operating systems with operating system name as array key
        $oses = array('iPhone' => '(iPhone)', 'Windows' => 'Win16', 'Windows' => '(Windows 95)|(Win95)|(Windows_95)', // Use regular expressions as value to identify operating system
        'Windows' => '(Windows 98)|(Win98)', 'Windows' => '(Windows NT 5.0)|(Windows 2000)', 'Windows' => '(Windows NT 5.1)|(Windows XP)', 'Windows' => '(Windows NT 5.2)', 'Windows' => '(Windows NT 6.0)|(Windows Vista)', 'Windows' => '(Windows NT 6.1)|(Windows 7)', 'Windows' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)', 'Windows' => 'Windows ME', 'Open BSD' => 'OpenBSD', 'Sun OS' => 'SunOS', 'Linux' => '(Linux)|(X11)', 'Safari' => '(Safari)', 'Macintosh' => '(Mac_PowerPC)|(Macintosh)', 'QNX' => 'QNX', 'BeOS' => 'BeOS', 'OS/2' => 'OS/2', 'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)');

        foreach ($oses as $os => $pattern) {// Loop through $oses array

            // Use regular expressions to check operating system type
            if (strpos($userAgent, $os)) {// Check if a value in $oses array matches current user agent.
                return $os;
                // Operating system was matched so return $oses key
            }
        }
        return 'Unknown';
        // Cannot find operating system so return Unknown
    }

    /**function to read XLS file and return as array**/
    function readXLS($file) {
        /***********Getting File1 Data**********************/
        require_once SEO_XLS_READER; // Spreadsheet excel reader file
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('Windows-1252');
        $data->read($file);

        $sheets = sizeof($data->sheets);

        for ($i = 0; $i < $sheets; $i++) {
            if ($data->sheets[$i]['numRows']) {
                $x = 1;
                while ($x <= $data->sheets[$i]['numRows']) {

                    $y = 1;
                    while ($y <= $data->sheets[$i]['numCols']) {

                        $xls_array[$i][$x][$y] = isset($data->sheets[0]['cells'][$x][$y]) ? iconv("ISO-8859-1", "UTF-8", $data->sheets[$i]['cells'][$x][$y]) : '';
                        if ($this->os == 'Windows')
                            $xls_array[$i][$x][$y] = utf8_decode($xls_array[$i][$x][$y]);
                        $y++;
                    }
                    $x++;
                }
            }
        }
        return $xls_array;
    }

    /**function to read XLS file and return as array**/
    function readInXLS($file) {
		
        /***********Getting File1 Data**********************/
        $data = new Spreadsheet_Excel_Reader();
        $data->read($file);

        if ($data->sheets[0]['numRows']) {
            $x = 1;
            while ($x <= $data->sheets[0]['numRows']) {
                $y = 1;
                while ($y <= $data->sheets[0]['numCols']) {
                    if (($this->site_id == 10 || $this->site_id == 11) && ($this->os != 'Windows')) {
                        $xls_array[$x][$y] = isset($data->sheets[0]['cells'][$x][$y]) ? (html_entity_decode($data->sheets[0]['cells'][$x][$y], ENT_QUOTES, 'iso-8859-1')) : '';
                    } else {
                        if ($this->site_id != 10 && $this->site_id != 11) {
                            $xls_array[$x][$y] = isset($data->sheets[0]['cells'][$x][$y]) ? (utf8_encode($data->sheets[0]['cells'][$x][$y])) : '';
                        } else {
                            $xls_array[$x][$y] = isset($data->sheets[0]['cells'][$x][$y]) ? iconv("ISO-8859-1", "UTF-8", $data->sheets[0]['cells'][$x][$y]) : '';
                            if ($this->os == 'Windows')
                                $xls_array[$x][$y] = utf8_decode($xls_array[$x][$y]);
                        }
                    }
                    $y++;
                }
                $x++;
            }
        }
        return $xls_array;
    }

    /**function to create CSV file**/
    function writeCSV($list, $file) {
        $fp = fopen($file, 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields, ";");
        }
        fclose($fp);
    }

    /**function to create XLS file**/
    function WriteXLS($data, $file_name) {
        // include package
        include_once SEO_XLS_WRITER_INCLUDE;

        // create empty file
        //if(!class_exists('Spreadsheet_Excel_Writer'))
        $excel = new Spreadsheet_Excel_Writer($file_name);

        // add worksheet
        $sheet = &$excel->addWorksheet();
        //$sheet->setInputEncoding('ISO-8859-1');
        // create format for header row
        // bold, red with black lower border
        $firstRow = &$excel->addFormat();
        $firstRow->setBold();
        $firstRow->setSize(12);
        $firstRow->setBottom(1);
        $firstRow->setBottomColor('black');

        // add data to worksheet
        $rowCount = 0;
        foreach ($data as $row) {
            foreach ($row as $key => $value) {

                if ($this->os != 'Windows')
                    $value = utf8_decode($value);

                if ($rowCount == 0)
                    $sheet->write($rowCount, $key, $value, $firstRow);
                else
                    $sheet->write($rowCount, $key, $value);
            }
            $rowCount++;
        }
        // save file to disk
        $excel->close();
    }
    
    // Show csv results
    function showCSV($data) {
        /*$table = SEO_TBL_TG;

        $i = 0;
        foreach ($data as $row) {
            if ($i == 0){$table .= '<thead>';}
            $table .= '<tr>';
            foreach ($row as $td) {
                if ($i == 0)
                    $table .= '<th>' . utf8_encode($td) . '</th>';
                else
                    $table .= '<td>' . $td . '</td>';
            }
            $table .= '</tr>';
            if ($i == 0){$table .= '</thead><tbody>';}
            $i++;
        }
        $table .= '</tbody>' . SEO_TBL_TG_;*/
        return $this->viewResultsGrid($data, '');
    }
    
    // Google suggest view results function 
    function googlesuggestresults($file) {
		
		// Read csv file to an array
        $data = $this->getCSV(SEO_DOWNLOAD_GSUGGEST . $file);
        //echo '<pre>';
        if (count($data) > 1) {
            $table = SEO_TBL_TG . '<thead><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></thead><tbody>';$i=0;
            foreach ($data as $key => $word) {
                $btag = (trim($word[0]) == 'keyword' || trim($word[0]) == 'language' || trim($word[0]) == 'site') ? '<b>' : '';
                $etag = (trim($word[0]) == 'keyword' || trim($word[0]) == 'language' || trim($word[0]) == 'site') ? '</b>' : '';
                if ($this->site_id == 10) {
                    $table .= '<tr><td>' . $btag . html_entity_decode($word[1], ENT_QUOTES, 'iso-8859-1') . $etag . '</td><td>' . $btag . html_entity_decode($word[2], ENT_QUOTES, 'iso-8859-1') . $etag . '</td><td>' . $btag . html_entity_decode($word[3], ENT_QUOTES, 'iso-8859-1') . $etag . '</td><td>' . $btag . html_entity_decode($word[4], ENT_QUOTES, 'iso-8859-1') . $etag . '</td><td>' . $btag . html_entity_decode($word[5], ENT_QUOTES, 'iso-8859-1') . $etag . '</td></tr>';
                } else {
                    if (($this->os == 'Windows'))
                        $table .= '<tr><td>' . $btag . ($word[1]) . $etag . '</td><td>' . $btag . ($word[2]) . $etag . '</td><td>' . $btag . ($word[3]) . $etag . '</td><td>' . $btag . ($word[4]) . $etag . '</td><td>' . $btag . ($word[5]) . $etag . '</td></tr>';
                    else
                        $table .= '<tr><td>' . $btag . utf8_decode($word[1]) . $etag . '</td><td>' . $btag . utf8_decode($word[2]) . $etag . '</td><td>' . $btag . utf8_decode($word[3]) . $etag . '</td><td>' . $btag . utf8_decode($word[4]) . $etag . '</td><td>' . $btag . utf8_decode($word[5]) . $etag . '</td></tr>';
                }
                $i++;
            }
            $table .=  '</tbody>' . SEO_TBL_TG_;
        }
        return $table;
    }
    
    // Download position tool result file
    function posdownloadFile($filename, $extension) {
        $filename = $filename . "." . $extension;
        $path_file = SEO_DOWNLOAD_POS . $filename;
        if (file_exists($path_file)) {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=$filename");
            ob_clean();
            flush();
            readfile("$path_file");
            exit ;
        } else {
            $this->class = "error";
            $this->msg = "File not Exist";
        }
    }
    
    /* plagiarism tool action (Interface and download results file) */
    public function plagiarismAction() {        
        $plag_params = $this->_request->getParams();
        
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($plag_params['file']) && isset($plag_params['ext']))
            $this->plagdownloadFile($plag_params['file'], $plag_params['ext']);
        if ($plag_params['class'])
            $this->class = $plag_params['class'];
            
        $_POST['word_type'] = 1;
        $this->_view->word_type = $plag_params['word_type']; // word type(text or file)
        
        if (@$msg)
            $this->_view->msg = $msg;
        
        // Processes a view script and returns the output.  
        $this->render("seotool_plagiarism");
    }
    
    /* plagiarism tool 2 action (Interface, view and download results file) */
    public function plagiarism2Action() {        
        $plag_params = $this->_request->getParams();

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($plag_params['file']) && isset($plag_params['ext'])) {
			
            $filename = $plag_params['file'] . "." . $plag_params['ext'];
            $path_file = SEO_DOWNLOAD_PLAG_ . $filename;

            if (file_exists($path_file)) {
                $data = $this->readXLS($path_file); // Read excel(xls) file data to an array

                $sheetcount = 1;
                foreach ($data as $sheets) {
                    $i = 0; if($_REQUEST['debug']){echo '<pre>'; print_r($sheets);}
                    $cols = sizeof(max($sheets)) ;

                    foreach ($sheets as $key=>$row) {
                        if(sizeof($row)<$cols){
                            $sheets[$key] = array_merge($sheets[$key], array_fill((sizeof($row)-1), ($cols-(sizeof($row))), ''));
                        }
                    }
                    $table .= '<table class="table table-striped table-bordered dTableR" id="smpl_tbl'.$sheetcount.'">';
                    foreach ($sheets as $row) {
                        $table .= ($i==0 ? '<thead>' : ($i==1 ? '<tbody>' : '')).($i==0 ? '' : '<tr>');
                        foreach ($row as $td) {
                            if ($this->os != 'Windows') {
                                if ($i == 0)
                                    $table .= '<th>' . utf8_decode($td) . '</th>';
                                else
                                    $table .= '<td>' . utf8_decode($td) . '</td>';
                            } else {
                                if ($i == 0)
                                    $table .= '<th>' . ($td) . '</th>';
                                else
                                    $table .= '<td>' . ($td) . '</td>';
                            }
                        }
                        $table .= ($i==0 ? '</thead>' : ($i==0 ? '' : '</tr>'));
                        $i++;
                    }
                    $table .= '</tbody>';
                    $table .= SEO_TBL_TG_;
                    $sheetcount++;
                }
            }
            $this->_view->sheet_count = range(1,$sheetcount-1);
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $plag_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
        } else {
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($plag_params['file']) && isset($plag_params['ext']))
                $this->plagdownloadFile($plag_params['file'], $plag_params['ext']);
            if ($plag_params['class'])
                $this->class = $plag_params['class'];
                
            $_POST['word_type'] = 1;
            $this->_view->word_type = $plag_params['word_type']; // word type(text or file)
            
            if (@$msg)
                $this->_view->msg = $msg;
                
			// Processes a view script and returns the output.
            $this->render("seotool_plagiarism2");
        }
    }
    // Function to download plagiarism results file
    function plagdownloadFile($filename, $extension) {
        $filename = $filename . "." . $extension;
        $path_file = SEO_DOWNLOAD_PLAG . $filename;
        if (file_exists($path_file)) {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=$filename");
            ob_clean();
            flush();
            readfile("$path_file");
            exit ;
        } else {
            $this->class = "error";
            $this->msg = "File not Exist";
        }
    }
    /* Plagiarism tool 2 upload action */
    public function plag2ssh2uploadAction() {
        
        $response = $this->responseMsg(0, 0, 1, ''); // Response message

		 // Get all request variable posted for plagiarism tool
        $plag_params = $this->_request->getParams();
        
        $loginName = $this->adminLogin->loginName; // Logged in username
        $userId = $this->adminLogin->userId;  // Logged in userid
        $word_type = $plag_params['word_type']; // word type(text or file)
        
        //$this->output_type = $plag_params['op_type']; // Results output format

        if ($word_type == 2) {
            $urls = explode("\n", trim($plag_params['url']));
            $urls = array_map('trim', $urls);
            $url = implode("|", $urls);
        } else if ($word_type == 1) {

            if (($_FILES['url_file']['type'] == 'text/comma-separated-values') || ($_FILES['url_file']['type'] == 'text/csv') || ($_FILES['url_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['url_file']['type'] == 'application/x-msexcel') || ($_FILES['url_file']['type'] == 'application/xls')) {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file

                $file_info = pathinfo($_FILES['url_file']['name']);
                $extension = $file_info['extension'];

                if ($extension == 'xls') {
					// Read xls file to an array
                    $urls_array = $this->readInXLS($_FILES['url_file']['tmp_name']);
                } else {
					// Read csv file to an array
                    $urls_array = $this->getCSV($_FILES['url_file']['tmp_name']);
                }

                foreach ($urls_array as $row) :
                    foreach ($row as $value) :
                        $urlArr[] = $value;
                    endforeach;
                endforeach;

                $urlArr = array_map('trim', $urlArr);
                $url = implode("|", array_filter($urlArr));
            } else {
                $response = $this->responseMsg(0, 1, $word_type); // Response message
            }
        }

        $crawltype = $plag_params['crawl_type']; // Crawling type/option

        if (empty($url)) {
            $response = $this->responseMsg(0, 9); // Response message
        } elseif (!$crawltype) {
            $response = $this->responseMsg(0, 2); // Response message
        } else {
            try {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                require_once SEO_SFTP_FILE; // php - sftp file

				/**creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }

                //Path to execute ruby command
                $file_exec_path = trim($sftp->exec(SEO_PLAG_EXEC));
                $csv_file = "results_" . time();
                $csv_file_name = $csv_file . ".xls";
                $ruby_file = SEO_PLAG2_RB; // Ruby file for processing
                
                // Ruby command for data processing
                $cmd = "$ruby_file $userId $loginName '$url' '$csv_file_name' $crawltype ";
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                $file_download_path = $sftp->exec(SEO_PLAG_DOWNLOAD); // output file download path

                $remoteFile = trim($file_download_path) . "/" . $csv_file_name;
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                $localFile = SEO_DOWNLOAD_PLAG . $csv_file_name;
                $sftp->get($remoteFile, $localFile); //downloading result file from linode server(using sftp)
                
                if (file_exists($localFile)) {
                    $xls_array = $this->readInXLS($localFile); // Read xls file to an array
                    $this->writeCSV($xls_array, SEO_PLAG_DOWNLOAD . $csv_file . ".csv"); // Write data in csv format

					// Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG2.$cmd, BO_DOMAIN_ . BO_PATH_ . "download_seoresult.php?filename=" . $csv_file . "&ext=xls&tool=plagiarism", SEO_DOWN_OP_FILE, 'plagiarism2?action=view&file=' . $csv_file . '&ext=xls', SEO_VIEW_RESULTS);
                    
                } else {
                    //throw new Exception($output);
                    $response = $this->responseMsg(0, 0, 0, ($cmd)); // Response message
                }
            } catch(Exception $e) {
                $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
            }
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }
    /* Plagiarism tool upload action */
    public function plagssh2uploadAction() {
        
         // Get all request variable posted for plagiarism tool
        $plag_params = $this->_request->getParams();
        
        if (isset($plag_params['submit'])) {
            $response = $this->responseMsg('', 0, 1, '', ''); // Response message
            $this->type = $plag_params['word_type']; // word type(text or file)
            
            require_once SEO_SFTP_FILE; // php - sftp file
            require_once SEO_FILE_CONVERTION;

            if ($this->type == 2) {
                $kw_text = trim($plag_params['kw']);
                $kw_text = ($kw_text);
                if ($kw_text) {
                    $fname = "File_" . time();
                    $txt_file_name = $fname . ".txt";
                    $srcFile = SEO_UPLOAD_PLAG . $txt_file_name;
                    
                    // creating txt file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, $kw_text);
                    fclose($fp);
                    
                    // Processing plagiarism tool
                    $response = $this->plaguploadAndProcess($srcFile, $txt_file_name);
                    $response['word_type'] = $this->type; // word type(text or file)
                    
                } else {
                    $response = $this->responseMsg(0, 26, $this->type); // Response message
                }
            } else if ($this->type == 1) {
                
                if (sizeof($_FILES['keyword_file']['name'])>0) {
                
                    $zip = new ZipArchive();
                    // Load zip library
                    $zip_name = SEO_UPLOAD_PLAG . "zip_" . time() . ".zip";
                    // Zip name
                    if ($zip->open($zip_name, ZIPARCHIVE::CREATE) !== TRUE) {
                        // Opening zip file to load files
                        $error .= "* Sorry ZIP creation failed at this time";
                    }
                    
                    foreach($_FILES['keyword_file']['name'] as $k=>$val) {
                    
                        $tmpFile = $_FILES['keyword_file']['tmp_name'][$k];
                        $u_file_name = str_replace(" ", "_", frenchCharsToEnglish(utf8_encode($_FILES['keyword_file']['name'][$k])));
                        $srcFile = SEO_UPLOAD_PLAG . "$u_file_name";
                        move_uploaded_file($tmpFile, $srcFile);

                        /**getting content of uploaded  File**/
                        $file = pathinfo($srcFile);
                        $ext = $file['extension'];

                        if ($ext == 'zip' || $ext == 'rar') {
                            if($ext=='rar')
                            {
                                $zip_file=pathinfo($srcFile);
                                $zip_file['filename']   =   str_replace(" ","-",$zip_file['filename']) ;
                                $path   =   $zip_file['dirname']."/".$zip_file['filename'].".rar" ;
                                $rar_file = rar_open($path);
                                $list = rar_list($rar_file);
                                foreach($list as $file) {       
                                    preg_match('/RarEntry for file "(.*)"/', $file, $matches) ;
                                    if(strstr($file, 'RarEntry for file'))
                                    {
                                        $entry = rar_entry_get($rar_file, $matches[1]) or die("Failed to find such entry") ;
                                        $entry->extract(false, $zip_file['dirname']."/".$zip_file['filename']."/".(str_replace(" ","-",frenchCharsToEnglish($matches[1]))));
                                    }
                                    
                                }
                                rar_close($rar_file);
                                $unzip_dir  =   $zip_file['dirname']."/".$zip_file['filename'] ;
                                chmod($unzip_dir,0777) ;
                            }
                            else
                            {
                                chmod($srcFile, 0777);
                                $unzip_dir = $this->unzip($srcFile);
                            }

                            if ($handle = opendir($unzip_dir)) {

                                while (false !== ($entry = readdir($handle))) {

                                    if ($entry != "." && $entry != "..") {

                                        unset($content);
                                        unset($status);
                                        $unzip_file = pathinfo("$unzip_dir/$entry");
                                        $unzip_ext = $unzip_file['extension'];
                                        if(in_array($unzip_ext, array('doc', 'docx', 'xls', 'xlsx')))
                                        {
                                            $content = new filecontent($unzip_dir . "/" . $entry);
                                            $status = $content->getStatus();
        
                                            if ($status == 1) {
                                                $srcFile = $unzip_dir . "/" . $unzip_file['filename'] . ".txt";
                                                $u_file_name = $unzip_file['filename'] . ".txt";
                                                $u_file_name = frenchCharsToEnglish($u_file_name);
                                                $zip->addFile($srcFile, $u_file_name);
                                            }
                                        }
                                    }
                                }

                                foreach ( scandir($unzip_dir) as $itm)
                                {
                                    unset($content) ;   unset($status) ;
                                    
                                    $itm_info= pathinfo($itm) ;
                                    if(in_array($itm_info['extension'], array('doc', 'docx', 'xls', 'xlsx')))
                                    {
                                        $content=new filecontent($unzip_dir."/".$itm) ;
                                        $status=$content->getStatus() ;
                    
                                        if($status==1)
                                        {
                                            $srcFile=$unzip_dir."/".$itm_info['filename'].".txt" ;
                                            chmod($srcFile,0777) ;
                                            $u_file_name=$itm_info['filename'].".txt" ;
                                            $u_file_name = frenchCharsToEnglish($u_file_name);
                                            $zip->addFile($srcFile, str_replace(' ', '', trim($u_file_name))) ;
                                        }
                                    }
                                }
                                closedir($handle);
                            }
                        } else {
                            $content = new filecontent($srcFile);
                            $status = $content->getStatus();

                            if ($status == 1) {
                                $srcFile = SEO_UPLOAD_PLAG . $file['filename'] . ".txt";
                                $u_file_name = frenchCharsToEnglish($file['filename']) . ".txt";
                                $zip->addFile($srcFile, $u_file_name);
                            }
                        }
                    }
                    $zip->close();
                    
                    // Processing plagiarism tool
                    $response = $this->plaguploadAndProcess($zip_name, 'many') ;
                } else {
                    $response = $this->responseMsg(0, 28); // Response message
                }
                $response['word_type'] = $this->type; // word type(text or file)
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }

    /* Plagiarism tool function to connect to the linode server, uploading the csv/xls 
     * and processing the output file **/
    function plaguploadAndProcess($srcFile, $u_filename) {
        ini_set('max_execution_time',0);
        error_reporting(0);
        try {
            /**creating ssh component object**/            
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            //Path to execute ruby command
            $file_exec_path = $sftp->exec(SEO_PLAG_EXEC);
            $file_upload_path = $sftp->exec(SEO_PLAG_UPLOAD); // seo input file upload path
            $file_download_path = $sftp->exec(SEO_PLAG_DOWNLOAD); // output file download path

            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server

            if ($u_filename == 'many')
                $u_file_name = strrev(substr(strrev($srcFile), 0, strpos(strrev($srcFile), '/')));
            else
                $u_file_name = $u_filename;

            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            $src = pathinfo($u_file_name);
            $download_fname = $src['filename'] . "_" . time();
            $dstfile = $download_fname . "." . $src['extension'];
            $dstfile_xml = $download_fname . ".xml";
            $format = $this->format ? 2 : 1;
            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId;  // Logged in userid
            $ruby_file = SEO_PLAG_RB; // Ruby file for processing
			
			// Ruby command for data processing
            if ($u_filename == 'many')
                $cmd = "$ruby_file '$u_file_name' 'many' '$dstfile_xml' $userId $loginName 2>&1 ";
            else
                $cmd = "$ruby_file '$u_file_name' '$dstfile' '$dstfile_xml' $userId $loginName 2>&1 ";

            $sftp->setTimeout(300); // sftp timeout

            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");

            /**processed file path**/
            $remoteFile = trim($file_download_path) . "/" . $dstfile_xml;

            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_PLAG . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];

            //downloading the file from remote server
            $sftp->get($dstfile_xml, $localFile);

            if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
                $xml_data = file_get_contents($localFile);
                
                // Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1.$cmd, "plagiarism?action=download&file=" . $fname . "&ext=" . $ext, SEO_DOWN_OP_FILE, 0, 0, '<div id="plagresults" class="alert alert-block alert-info" style="height:auto; position:relative;top:20px;float:left;"><h3>Plagiarism Results</h3>' . $xml_data.'</div>');
            } else {
                if(strlen(trim(strip_tags(nl2br($output))))==strlen('Using /home/oboulo/.rvm/gems/ruby-1.9.3-head File Size is more Than 800kb'))
                {
                    $response['type'] = 'error';
                    $response['message'] = 'File Size is more Than 800kb';
                }
                else {
                    throw new Exception($output);
                }
            }

        } catch(Exception $e) {
            // Response message
            $response = $this->responseMsg(0, 0, 0, "Ftp connection failed.. Host : " . $this->ssh2_server . " Username : " . $this->ssh2_user_name . " Password : " . $this->ssh2_user_pass);
        }
        return $response;
    }
    
    /* Google news tool (Interface and results view) */
    public function googleNewsAction() {
		
		 // Get all request variable posted for google news tool
        $gnews_params = $this->_request->getParams();

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($gnews_params['file']) && isset($gnews_params['ext'])) {
            $filename = $gnews_params['file'] . "." . $gnews_params['ext'];
            $path_file = SEO_DOWNLOAD_GNEWS . $filename;

            if (file_exists($path_file)) {
                header('Content-Type: text/html; charset=utf-8');
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), 'gnews') ;
            }
            //exit($table);
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $gnews_params['word_type']; // word type(text or file)

            // Processes a view script and returns the output.
            $this->render("seotool_view");
        } else {
            if ($gnews_params['class'])
                $this->_view->class = $gnews_params['class'];

            $_POST['word_type'] = 1;
            $this->_view->word_type = $gnews_params['word_type']; // word type(text or file)

			// Processes a view script and returns the output.
            $this->render("seotool_googlenews" . ($gnews_params['debug'] ? '_test' : ''));
        }
    }
    
    // Function for google news results file download
    function gnewsdownloadFile($filename, $extension) {
        $filename = $filename . "." . $extension;
        $path_file = SEO_DOWNLOAD_GNEWS . $filename;
        if (file_exists($path_file)) {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=$filename");
            ob_clean();
            flush();
            readfile("$path_file");
            exit ;
        } else {
            $this->class = "error";
            $this->msg = "File not Exist";
        }
    }
    
    /* Google news upload action */
    public function gnewsssh2uploadAction() {
		
		 // Get all request variable posted for google news tool
        $gnews_params = $this->_request->getParams();
        
        if (isset($gnews_params['submit'])) {
			
            // response hash
            $response = $this->responseMsg('', 0, 1, '', '');
            $this->type = $gnews_params['word_type']; // word type(text or file)

            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file

            $this->output_type = $gnews_params['op_type']; // Results output format
            $this->site_id = $gnews_params['site']; // site id
            $this->limit = $gnews_params['limit']; // Result limit

            if ($this->type == 2) {
                $kw_text = trim($gnews_params['kw']);
                /*if (($this->os == 'Windows'))
                    $kw_text = utf8_decode($kw_text);*/

                if ($kw_text) {
                    $kw_text1 = explode("\n", $kw_text);

                    $csv_file_name = "csv_" . time() . ".csv"; // Input csv file
                    $srcFile = SEO_UPLOAD_GNEWS . $csv_file_name;
                    
                    // creating csv file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, $kw_text);
                    fclose($fp);
                    
                    // Processing google news tool
                    $response = $this->gnewsuploadAndProcess($srcFile, $csv_file_name);
                    $response['word_type'] = $this->type; // word type(text or file)
                    
                } else {
                    $response = array('type' => 'error', 'message' => 'Please enter URL&keywords in box (CSV Format)', 'word_type' => $this->type);
                }

            } else if ($this->type == 1) {
                if (($_FILES['keyword_file']['type'] == 'text/comma-separated-values') || ($_FILES['keyword_file']['type'] == 'text/csv') || ($_FILES['keyword_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['keyword_file']['type'] == 'application/x-msexcel') || ($_FILES['keyword_file']['type'] == 'application/xls')) {
                    $file_info = pathinfo($_FILES['keyword_file']['name']);
                    $extension = $file_info['extension'];
                    if ($extension == 'xls') {
						// Read xls file to an array
                        $xls_array = $this->readInXLS($_FILES['keyword_file']['tmp_name']);
                        $u_file_name = str_replace(" ", "_", $file_info['filename']) . ".csv"; // Input csv file
                        $srcFile = SEO_UPLOAD_GNEWS . $u_file_name;
                        $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                    } else {
                        $srcFile = $_FILES['keyword_file']['tmp_name'];
                        $u_file_name = str_replace(" ", "_", $_FILES['keyword_file']['name']);
                    }
					// Processing google news tool
                    $response = $this->gnewsuploadAndProcess($srcFile, $u_file_name);
                } else {
                    $response = $this->responseMsg(0, 1); // Response message
                }
                $response['word_type'] = $this->type; // word type(text or file)
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }

    /* Google news function to connect to the linode server, uploading the csv/xls 
     * and processing the output file **/
    function gnewsuploadAndProcess($srcFile, $u_file_name) {
        try {
			/**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            $src = pathinfo($u_file_name);
            $download_fname = $src['filename'] . "_" . time();
            $this->seo_upload_files->gnews = $download_fname;
            $dstfile = $download_fname . "." . $src['extension'];

            $file_exec_path = $sftp->exec(SEO_GNEWS_EXEC); //Path to execute ruby command
            $file_upload_path = $sftp->exec(SEO_GNEWS_UPLOAD); // seo input file upload path
            $file_download_path = $sftp->exec(SEO_GNEWS_DOWNLOAD); // output file download path
            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            $encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');

            $limitt = $this->limit; // Result limit
            $site_idd = $this->site_id; // site id
            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId; // Logged in userid
            $ruby_file = SEO_GNEWS_RB; // Ruby file for processing
            
            // Ruby command for data processing
            $cmd = "$ruby_file $site_idd $u_file_name $dstfile $limitt \"$encoding\" 1 $userId $loginName 2>&1 ";
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
            
            $remoteFile = trim($file_download_path) . "/" . $dstfile;

            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_GNEWS . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];
            $sftp->get($dstfile, $localFile); //downloading result file from linode server(using sftp)

            if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
                if ($this->output_type == 2) {
                    $ext = "xls";
                    $output_file = SEO_DOWNLOAD_GNEWS . $fname . "." . $ext;
                    $csv_data = $this->gnewsgetCSV($localFile); // Read csv file to an array
                    $this->WriteXLS($csv_data, $output_file); // Output file in xls format
                }
                
				// Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seoresult.php?ext=' . $ext . '&filename=' . $fname . '&tool=gnews', SEO_DOWN_OP_FILE, 'google-news?action=view&file=' . $fname . '&ext=csv', SEO_VIEW_RESULTS);
            } else if (trim($output) == SEO_RVM_NOTATION && $option3 == 1) {
                $response = $this->responseMsg(1, 6); // Response message
            } else {
                throw new Exception($output);
            }
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
        }
        return $response;
    }
    
    // Google news csv contents as array
    function gnewsgetCSV($file) {
        $data_array = array();
        $row = 1;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $num = count($data);

                for ($c = 0; $c < $num; $c++) {
                    $data_array[$row][$c] = $data[$c];
                }
                $row++;
            }
            fclose($handle);
        }
        return $data_array;
    }

    public function frequencyAction() {
		
		 // Get all request variable posted for frequency tool
        $frequency_params = $this->_request->getParams();
        
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($frequency_params['file']) && isset($frequency_params['ext']))
            $this->frequencydownloadFile($frequency_params['file'], $frequency_params['ext']);

        require_once SEO_SFTP_FILE; // php - sftp file
        
        /**creating ssh component object**/
        $sftp = new Net_SFTP($this->ssh2_server);
        if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
            throw new Exception('Login Failed');
        }

        //Path to execute php command
        $file_exec_path = $sftp->exec(SEO_FREQUENCY_EXEC);
        
        // PHP command for data processing
        $cmd = "php getContracts.php 2>&1 ";

        $sftp->setTimeout(300); // sftp timeout
        
        $file_exec_path = trim($file_exec_path); //Path to execute ruby command
        $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
        
        // Output by executing sftp command (using php command string)
        $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");

        $output = str_replace(SEO_RVM_NOTATION, "", $output);
        $output = explode("$$$#####$$$", $output);

        $this->_view->clients = $output[0]; // Client names
        $this->_view->contracts = $output[1];
        if (@$frequency_params['class'])
            $this->_view->class = $frequency_params['class'];
            
        $_POST['word_type'] = 1;
        $this->_view->word_type = $frequency_params['word_type']; // word type(text or file)

        if (@$msg)
            $this->_view->msg = $msg;

		// Processes a view script and returns the output.
        $this->render("seotool_frequency");
    }
    
    // Function for frequency results file download
    function frequencydownloadFile($filename, $extension) {

        $filename = $filename . "." . $extension;
        $path_file = SEO_DOWNLOAD_FREQUENCY . $filename;
        if (file_exists($path_file)) {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=$filename");
            ob_clean();
            flush();
            readfile("$path_file");
            exit ;
        } else {
            $class = "error";
            $msg = "File not Exist";
        }
    }
    
    // Function for links results file download
    function linksdownloadFile($filename, $extension) {
        $filename = $filename . "." . $extension;
        $path_file = SEO_DOWNLOAD_LINKS . $filename;
        if (file_exists($path_file)) {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=$filename");
            ob_clean();
            flush();
            readfile("$path_file");
            exit ;
        } else {
            $class = "error";
            $msg = "File not Exist";
        }
    }
    
    /* Frequency tool upload action */
    public function frequencyssh2uploadAction() {
		
		 // Get all request variable posted for frequency tool
        $frequency_params = $this->_request->getParams();
        
        if (isset($frequency_params['submit'])) {
            $response = array('type' => '', 'message' => '');

            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file

            $this->client = $frequency_params['client']; // Client name
            $this->contract = $frequency_params['contract'];
            $this->from_date = $frequency_params['from_date'];
            $this->to_date = $frequency_params['to_date'];
            $this->days = implode("|", $frequency_params['day']);

            $frequency = $this->checkSearchFrequency();
            if ($frequency == 'process') {
				// Processing frequency tool
                $response = $this->frequencyuploadAndProcess();
            } else {
                $response = $this->responseMsg(0, 0, 0, $frequency); // Response message
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }

    /* Frequency tool function to connect to the linode server, uploading the csv/xls 
     * and processing the output file **/
    function frequencyuploadAndProcess() {
        try {
            /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            $file_exec_path = $sftp->exec(SEO_FREQUENCY_EXEC); //Path to execute ruby command
            $file_download_path = $sftp->exec(SEO_FREQUENCY_DOWNLOAD); // output file download path
            $dstfile = str_replace(" ", "_", $this->contract) . "_" . time() . ".zip";
            $from_datee = $this->from_date;
            $to_datee = $this->to_date;
            $contractt = $this->contract;
            $dayss = $this->days;
            $ruby_file = SEO_FREQUENCY_RB; // Ruby file for processing
            
            // Ruby command for data processing
            $cmd = "$ruby_file \"$from_datee\" \"$to_datee\" \"$contractt\" \"$dayss\" \"$dstfile\" 2>&1 ";
            
            $sftp->setTimeout(300); // sftp timeout
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
            
            $remoteFile = trim($file_download_path) . "/" . $dstfile;
            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_FREQUENCY . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];
            $sftp->get($dstfile, $localFile); //downloading result file from linode server(using sftp)
            
            if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
				// Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg('', "/seotool/frequency?action=download&file=" . $fname . "&ext=" . $ext, SEO_DOWN_RESULT_FILE);
            } else {
                throw new Exception($output);
            }
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
        }
        return $response;
    }

    /* SEO Tool status to show progress bar */
    function seotoolstatusAction() {

        mysql_connect('50.116.62.9', 'editplace', 'ep123') or die('cant connect to 50.116.62.9'); // Database connection
        mysql_select_db('editplace'); // Select database

        $table = $this->seo_upload_files->gnews;
        //"test";
        $sql = mysql_query("select CONCAT(COUNT(*), '*', (select COUNT(*) from $table where processed = '1')) AS result from $table") or die("$table");
        $result = mysql_fetch_object($sql);
        exit($result->result);
    }

    /* Google suggest tool action (Interface, php processing, view results and download results) */
    public function googleSuggestAction() {
		
		 // Get all request variable posted for google suggest tool
        $gsuggest_params = $this->_request->getParams();
        
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($gsuggest_params['file']))
            $this->googlesuggestdownloadXLS($gsuggest_params['file']);

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($gsuggest_params['file']) && isset($gsuggest_params['ext'])) {
			
            $this->site_id = $gsuggest_params['siteid']; // site id
            
            // View results with template stored into smarty variable
            $this->_view->table = $this->googlesuggestresults($gsuggest_params['file'] . "." . $gsuggest_params['ext']);
            
            // Processes a view script and returns the output.
            $this->render($gsuggest_params['siteid'] ? "seotool_utfview" : "seotool_view");
            exit ;
        }

        $this->_view->word_type = $gsuggest_params['word_type']; // word type(text or file)
        
        $this->_view->kw = stripslashes(trim(strip_tags($gsuggest_params['kw'])));

        if (isset($gsuggest_params['submit'])) {
            if (@$msg)
                $this->_view->msg = $msg;
            $type = $gsuggest_params['word_type']; // word type(text or file)
            $site = $gsuggest_params['site'];

            switch($site) {
                case 'fr' :
                    $url = 'google.fr';
                    break;
                case 'uk' :
                    $url = 'google.co.uk';
                    break;
                case 'com' :
                    $url = 'google.com';
                    break;
                case 'de' :
                    $url = 'google.de';
                    break;
                case 'in' :
                    $url = 'google.co.in';
                    break;
                case 'it' :
                    $url = 'google.it';
                    break;
                case 'es' :
                    $url = 'google.es';
                    break;
                case 'pt' :
                    $url = 'google.pt';
                    break;
                case 'br' :
                    $url = 'google.com.br';
                    break;
                default :
                    $url = 'google.fr';
                    break;
            }
            //Only Source or Combinations
            $combination = $gsuggest_params['combination'];
            if ($type == 1) {
                if (($_FILES['keyword_file']['type'] == 'text/comma-separated-values') || ($_FILES['keyword_file']['type'] == 'text/csv') || ($_FILES['keyword_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['keyword_file']['type'] == 'application/x-msexcel') || ($_FILES['keyword_file']['type'] == 'application/xls')) {
                    $file_info = pathinfo($_FILES['keyword_file']['name']);
                    $extension = $file_info['extension'];

                    if ($extension == 'xls') {
						// Read xls file to an array
                        $data_array = $this->readInXLS($_FILES['keyword_file']['tmp_name']);
                    } else {
                        $data_array = array();
                        $row = 1;
                        if (($handle = fopen($_FILES['keyword_file']['tmp_name'], "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                $num = count($data);
                                if ($data[0]) {
                                    for ($c = 0; $c < $num; $c++) {
                                        if ($data[$c] != '')
                                            $data_array[$row][$c] = $data[$c];
                                    }
                                    $row++;
                                }
                            }
                            fclose($handle);

                            $rows = count($data_array);
                            $cols = $num;
                        }
                    }
                    if (count($data_array) > 0) {
                        $words = $data_array;
                        $j = 1;
                        $this->gsuggest_excel_array[0][0] = "Keyword";
                        $this->gsuggest_excel_array[0][1] = "No Results";

                        foreach ($words as $word) {
                            $word = trim($word[0]);
                            $this->googleSuggest($url, $word);

                            if ($combination == 2) {
                                foreach (range('a','z') as $i) {
                                    $query = '';
                                    $query = $word . ' ' . $i;
                                    $this->googleSuggest($url, $query);
                                }
                            }
                        }
                        $this->googlesuggestWriteXLS($this->gsuggest_excel_array, 'suggest'); // Output file in xls format
                    }
                }
            } else if ($type == 2) {
                $text = trim($gsuggest_params['kw']);
                $textAr = explode("\n", $text);
                $words = array_filter($textAr, 'trim');
                if (count($words) > 0) {
                    $j = 1;
                    $this->gsuggest_excel_array[0][0] = "Keyword";
                    $this->gsuggest_excel_array[0][1] = "No Results";

                    foreach ($words as $word) {
                        $word = trim($word);
                        $this->googleSuggest($url, $word);

                        if ($combination == 2) {
                            foreach (range('a','z') as $i) {
                                $query = '';
                                $query = $word . ' ' . $i;
                                $this->googleSuggest($url, $query);
                            }
                        }
                    }
                    //write the data into XLS
                    $this->googlesuggestWriteXLS($this->gsuggest_excel_array, 'suggest');
                }
            }
        }

        if (count($this->gsuggest_excel_array) > 1) {
            $length = count($this->gsuggest_excel_array) - 1;
            $table = SEO_TBL_TG . '
                    <caption>URL:' . $url . '<br>
                    "' . $length . '" Suggestions for given keyword(s).</caption>
                    <tr>
                        <th scope="col" abbr="Keyword">Keyword</th>
                        <th scope="col" abbr="Number of Results">N&deg; Results</th>
                    </tr>';
            //for($i=0;$i<$length;$i++)
            foreach ($this->gsuggest_excel_array as $key => $word) {
                if ($key > 0)
                    $table .= '<tr><td>' . utf8_decode($word[0]) . '</td><td>' . $word[1] . '</td></tr>';
            }
            $table .= SEO_TBL_TG_;

            $this->_view->gsuggest_excel = $table;
        }
        $this->_view->gurl = $_SESSION['gurl'] ? $_SESSION['gurl'] : '0';
        
        // Processes a view script and returns the output.
        $this->render($gsuggest_params['linode'] ? 'seotool_googlesuggest' : 'seotool_googlesuggest_tor');
    }
    
    /* Google suggest tool (Uploading and ruby processing) */
    public function googlesuggesttorAction()
    {
		// Get all request variable posted for google suggest tool
        $gsuggestParams = $this->_request->getParams();
        
        $type = $gsuggestParams['word_type']; // word type(text or file)
        $site = implode('|', $gsuggestParams['site']);
        $site_ext = $gsuggestParams['site_ext'];
        $this->site_id = $site_ext; // site id
        $combination = $gsuggestParams['combination'];
        $loginName = $this->adminLogin->loginName; // Logged in username
        $userId = $this->adminLogin->userId; // Logged in userid
        $outputfilename = 'results_' . time();
        $outputcsvfile = 'results_' . time() . '.csv'; // Output csv file
        $outputxlsfile = 'results_' . time() . '.xls';
        $srcFile = SEO_UPLOAD_GSUGGEST . $outputcsvfile;

        if (isset($gsuggestParams['submit'])) {
            if ($site == '|' || $site == '') {
                $response = $this->responseMsg(0, 29); // Response message
            } elseif ($type == 2) {
                $kw_text = trim($gsuggestParams['kw']);

                if (!empty($kw_text)) {
                    /*if ($this->os == 'Windows' && ($this->site_id == 10) && ($this->site_id == 11))
                        $kw_text = utf8_decode($kw_text);*/

                    $kw_text1 = explode("\n", $kw_text);
                    
                    // creating csv file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, str_replace("\'", "'", $kw_text));
                    fclose($fp);
                } else {
                    $response = $this->responseMsg(0, 16); // Response message
                }
            } else if ($type == 1) {
                $file_info = pathinfo($_FILES['keyword_file']['name']);
                $extension = $file_info['extension'];

                if ($extension == 'xls') {
                    require_once SEO_XLS_READER; // Spreadsheet excel reader file
                    move_uploaded_file($_FILES['keyword_file']['tmp_name'], SEO_UPLOAD_GSUGGEST . $outputfilename . ".xls");
                    
                    // Read xls file to an array
                    $data = $this->readInXLS(SEO_UPLOAD_GSUGGEST . $outputfilename . ".xls");

                    $this->writeCSV($data, $srcFile); // Write data in csv format
                } elseif ($extension == 'csv') {
                    move_uploaded_file($_FILES['keyword_file']['tmp_name'], $srcFile);
                } else {
                    $response = $this->responseMsg(0, 1); // Response message
                }
            }
            if (file_exists($srcFile)) {
                require_once SEO_SFTP_FILE; // php - sftp file

				/**creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }
                
                $encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');
                $file_exec_path = $sftp->exec(SEO_GSUGGEST_EXEC); //Path to execute ruby command
                $file_upload_path = $sftp->exec(SEO_GSUGGEST_UPLOAD); // seo input file upload path
                $file_download_path = $sftp->exec(SEO_GSUGGEST_DOWNLOAD); // output file download path
                $file_exec_path = trim($file_exec_path); //Path to execute ruby command
                $file_upload_path = trim($file_upload_path); // seo input file upload path
                $file_download_path = trim($file_download_path); // output file download path
                $ruby_file = SEO_GSUGGEST_RB; // Ruby file for processing

                $sftp->setTimeout(300); // sftp timeout
                
                // Ruby command for data processing
                $cmd = "$ruby_file '$site_ext' $outputcsvfile $outputcsvfile '$site' \"$encoding\" $combination $userId $loginName";
                $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
                $sftp->put($outputcsvfile, SEO_UPLOAD_GSUGGEST . $outputcsvfile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp

                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $out_put = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd;");
                
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server

                //downloading the files from remote server
                $sftp->get($outputcsvfile, SEO_DOWNLOAD_GSUGGEST . $outputcsvfile); //downloading result file from linode server(using sftp)
                $sftp->get($outputxlsfile, SEO_DOWNLOAD_GSUGGEST . $outputxlsfile); //downloading result file from linode server(using sftp)
            } else {
				// Response message
                $response = $this->responseMsg(0, 30);
            }

            if (trim($out_put) == SEO_RVM_NOTATION) :
				// Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg('', BO_PATH . "/download_seoresult.php?filename=" . $outputfilename . "&tool=gsuggest&ext=xls", SEO_DOWN_OP_FILE, 'google-suggest?action=view&file=' . $outputfilename . 
                '&ext=csv&siteid=' . $this->site_id, SEO_VIEW_RESULTS);
            else :
                $response = $this->responseMsg(0, 0, 0, "cmd=" . $cmd . "output=" . trim($out_put)); // Response message
            endif ;
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }
    
    /* Write xls for google suggest tool */
    function googlesuggestWriteXLS($data, $name)
    {
        // include package
        include SEO_XLS_WRITER_INCLUDE;

        // create empty file
        $filename = uniqid() . "_" . str_replace(' ', '_', $name);
        $excel = new Spreadsheet_Excel_Writer(SEO_DOWNLOAD_GSUGGEST . $filename . ".xls");

        // add worksheet
        $sheet = &$excel->addWorksheet();
        $sheet->setInputEncoding('utf-8');
        // create format for header row
        // bold, red with black lower border
        $header_f = array('bold' => '1', 'size' => '10', 'FgColor' => 'yellow', 'color' => 'black', 'border' => '1', 'align' => 'center');
        $header = &$excel->addFormat($header_f);
        $cell_f = array('color' => 'black', 'border' => '1', 'align' => 'left');
        $cell = &$excel->addFormat($cell_f);

        // add data to worksheet
        $rowCount = 0;
        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                if ($rowCount == 0)
                    $sheet->write($rowCount, $key, $value, $header);
                else
                    $sheet->write($rowCount, $key, utf8_decode($value), $cell);
            }
            $rowCount++;
        }
        // save file to disk
        if ($excel->close() === true) {
            $this->_view->msg = 'Spreadsheet successfully saved! <a href="/download_seo.php?saction=download&tool=gsuggest&file=' . $filename . '&ext=xls">Download XLS</a>';
            $this->_view->class = 'success';
        } else {
            $this->_view->msg = 'ERROR: Could not save spreadsheet.';
            $this->_view->class = 'error';
        }

    }
    
    /* Download results for google suggest tool */
    function googlesuggestdownloadXLS($filename) {
        $filename = $filename . ".xls";
        $path_file = SEO_DOWNLOAD_GSUGGEST . $filename;
        if (file_exists($path_file)) {
            header("Content-type: application/xls");
            header("Content-Disposition: attachment; filename=$filename");
            ob_clean();
            flush();
            readfile("$path_file");
            exit ;
        }
    }

    /* Google suggest function for processing in php */
    function googleSuggest($site, $query) {
        switch($site) {
            case 'google.fr' :
                $lang = 'fr';
                break;
            case 'google.co.uk' :
                $lang = 'en-uk';
                break;
            case 'google.com' :
                $lang = 'en';
                break;
            case 'google.de' :
                $lang = 'de';
                break;
            case 'google.co.in' :
                $lang = 'en';
                break;
            case 'google.it' :
                $lang = 'it';
                break;
            case 'google.es' :
                $lang = 'es';
                break;
            case 'google.pt' :
                $lang = 'pt';
                break;
            case 'google.com.br' :
                $lang = 'com.br';
                break;
            default :
                $lang = 'fr';
                break;
        }
        $query = rawurlencode(utf8_encode($query)) ;
        $_SESSION['gurl'] = $url = 'http://' . $site . '/complete/search?q=' . $query . '&output=toolbar&ie=UTF-8&oe=UTF-8&lr=lang_' . $lang . '&hl=' . $lang;

        //echo $url."<br>";
        $xml = new DOMDocument;
        $xml->load($url);
        $thedocument = $xml->documentElement;
        $list = $thedocument->getElementsByTagName('CompleteSuggestion');

        foreach ($list as $domElement) {
            foreach ($domElement->childNodes as $node) {
                if ($node->getAttribute('data'))
                    $suggest = $node->getAttribute('data');
                if ($node->getAttribute('int'))
                    $num_queries = $node->getAttribute('int');
                else
                    $num_queries = "-";
            }
            $this->gsuggest_excel_array[] = array($suggest, $num_queries);
        }
    }
    
    /* Plagiarism xml results view */ 
    public function plagcontentsAction() {
        
         // Get all request variable posted for plagiarism view
        $plag_params = $this->_request->getParams();
        
        $plgFile = $plag_params['file'] ? $plag_params['file'] : $plag_params['s0plagfile'];

        //Added for compare URL date with txt file
        $txtFile = $plag_params['file'] ? $plag_params['file'] : $plag_params['s0plagfile'];
        $txtFile = explode("_", $txtFile);
        array_pop($txtFile);
        $txtFile = implode("_", $txtFile) . ".txt";
        $plagTxtFile_path = SEO_UPLOAD_PLAG . $txtFile;

        if (!empty($plgFile) && !empty($plag_params['idx'])) :

            $xmldata = simplexml_load_file(BO_DOMAIN_ . BO_PATH_ . ($plag_params['s0plagfile'] ? SEO_PLAG_ : SEO_DOWNLOAD_PLAG_) . $plgFile);
            $plgs = array();

            foreach ($xmldata->children() AS $child) {
                foreach ($child->results->children() AS $child1) {
                    foreach ($child1->url->children() AS $child2) {
                        if ($child2->getName() == 'p')
                            $plgs['url'][] = (string)$child2;
                    }
                    foreach ($child1->content->children() AS $child2) {
                        if ($child2->getName() == 'p')
                            $plgs['content'][] = (string)$child2;
                    }
                    foreach ($child1->percentage->children() AS $child2) {
                        if ($child2->getName() == 'p')
                            $plgs['percentage'][] = (string)$child2;
                    }
                }
            }
            if ($plag_params['s0plagfile']) {
                $pActualContent = @file_get_contents(BO_DOMAIN_ . FO_PATH_ . SEO_ARTICLES . substr($plag_params['s0plagfile'], 0, strpos($plag_params['s0plagfile'], '_')) . '/' . str_replace('.xml', '.txt', $plag_params['s0plagfile']));
                $words = $pActualContent;
                $file_content = $pActualContent;
            } else {
                if (file_exists($plagTxtFile_path)) {
                    $words = @file_get_contents($plagTxtFile_path);
                    $file_content = $words;
                } else
                    $words = $plgs['content'][$plag_params['idx'] - 1];
            }
            $text = @file_get_contents($plgs['url'][$plag_params['idx'] - 1]);
            $text = $this->cleanString(html_entity_decode($text, ENT_QUOTES, "UTF-8")); // Remove unwanted quotes from string
            $text = preg_replace('/\s+/', ' ', $text);
            $text = str_replace("<i>", "", $text);
            $text = str_replace("</i>", "", $text);
            //added
            $text = stripslashes($text);
            $words = html_entity_decode($words, ENT_QUOTES, "UTF-8");

            $words = str_replace("&rsquo;", "'", $this->cleanString($words)); // Remove unwanted quotes from string
            $words = str_replace("&lsquo;", "'", $words);
            $words = preg_replace('/\s+/', ' ', $words);
            //added
            $words = stripslashes($words);

            $this->_view->pUrl = $plgs['url'][$plag_params['idx'] - 1];

            //added
            if ($file_content)
                $this->_view->pContent = $file_content;
            else
                $this->_view->pContent = $plgs['content'][$plag_params['idx'] - 1];

            $this->_view->pPercentage = $plgs['percentage'][$plag_params['idx'] - 1];
            $this->_view->plagText = $this->plagsHighlight($text, $words);
        else :
            $this->_view->plagText = 'missing - plag arguments';
        endif ;

		// Processes a view script and returns the output.
        $this->render("seotool_plags_view");
    }
    
    /* Words highlight in plagiarsm results view */
    function plagsHighlight($text, $words) {

        preg_match_all('/[^\s]+\s[^\s]+\s[^\s]+\s[^\s]+\s[^\s]+/', $words, $m);
        //+\s[^\s]+\s[^\s]
        if (!$m)
            return $text;
        $re = '~\\b(' . implode('|', $m[0]) . ')\\b~';
        foreach ($m[0] as $m_) :
            $text = preg_replace("/$m_/", '<mmm>' . $m_ . '</mmm>', $text);
        endforeach;
        return $text;
    }
    
    /* Function to remove unwanted quotes from string */
    function cleanString($string) {

        $find[] = '“';
        // left side double smart quote
        $find[] = '”';
        // right side double smart quote
        $find[] = "‘";
        // left side single smart quote
        $find[] = "’";
        // right side single smart quote
        $find[] = '…';
        // elipsis
        $find[] = '—';
        // em dash
        $find[] = '–';

        $replace[] = '"';
        $replace[] = '"';
        $replace[] = "'";
        $replace[] = "'";
        $replace[] = '...';
        $replace[] = '-';
        $replace[] = '-';

        return str_replace($find, $replace, $string);
    }

    // Smart quotes conversion function
    function convert_smart_quotes($string) {
        $search = array(chr(145), chr(146), chr(147), chr(148), chr(151), chr(230), chr(156));

        $replace = array("'", "'", '"', '"', '-', 'ae', 'oe');
        return str_replace($search, $replace, $string);
    }
    
    /* Content error check tool */
    public function contentserrorcheckAction() {
		
		 // Get all request variable posted for contents error check
        $err_params = $this->_request->getParams();
        
        $this->_view->err1 = $err_params['err1'];
        $this->_view->err2 = $err_params['err2'];
        @$lang1 = $err_params['lang'];
        $this->_view->lang1 = ((!empty($lang1)) ? $lang1 : '');

		// Processes a view script and returns the output.
        $this->render("textcontentserror_check");
    }
    
    /* Tag validation tool processing */
    public function validatetagAction()
    {
        require_once SEO_SFTP_FILE; // php - sftp file

        try {
			/**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }
            //Path to execute ruby command
            $file_exec_path = $sftp->exec(SEO_TAG_EXEC);//ruby execution path
            $ruby_file = SEO_TAG_RB; // Ruby file for processing
            
            // Ruby command for data processing
            $cmd = "$ruby_file";
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");

            $response = $this->responseMsg(1, 7); // Response message
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }
    
    /* Save tag info tool (Interface) */
    public function savetaginfoAction() {
		
		// Get all request variable posted for save tag tool
        $tag_params = $this->_request->getParams();
        
        $client_info_obj = new Ep_User_User();
        $client_info = $client_info_obj->GetclientList(); // Getting all client list from database
        $client_list = array();
        $ao_obj = new Ep_Ao_EditAO();

        foreach ($client_info as $key => $value) {
            $name = $value['email']; // client email id
            $nameArr = array($value['company_name'], $value['first_name'], $value['last_name']);
            $nameArr = array_filter($nameArr);

            if (count($nameArr) > 0)
                $name .= "(" . implode(", ", $nameArr) . ")";

            $client_list[$value['identifier']] = strtoupper($name);
        }
        asort($client_list);
        array_unshift($client_list, "S&eacute;lectionner");
        $this->_view->show_ao = "display:none;";

        if ($tag_params['client'] != "") {
            $this->_view->show_ao = "";
            $ao_list = $ao_obj->getAOlist($tag_params['client'], 1);
            $this->_view->ao_list = $ao_list;
            $this->_view->def_user = $tag_params['client'];
        }

		// Processes a view script and returns the output.
        $this->render("seotool_savetaginfo");
    }
    
    /* Save tag processing */
    public function savetagAction() {

		// Get all request variable posted for save tag tool
        $tag_params = $this->_request->getParams();
        
        $articleId = $tag_params['ao_list'];
        $clientId = $tag_params['client_list'];
        $clientUrl = $tag_params['url'];
        $clientTag = addslashes($tag_params['tag']); // tags
        $expiry = $tag_params['validate_till']; // expiry date
        $incharge_email = $this->adminLogin->loginEmail; // incharge's email id

        require_once SEO_SFTP_FILE; // php - sftp file
        try {
			/**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            //Path to execute ruby command
            $file_exec_path = $sftp->exec(SEO_TAG_EXEC);
            $ruby_file = SEO_TAG_SAVE_RB; // Ruby file for processing
            
            // Ruby command for data processing
            $cmd = "$ruby_file $articleId $clientId $clientUrl \"$clientTag\" \"$expiry\" $incharge_email";
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ; cd $file_exec_path; $cmd ;");
            
            $response = $this->responseMsg(1, 8); // Response message
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }

    /* Longtail keyword (Interface and view results) */
    public function longtailkwsAction() {
		
		// Get all request variable posted for longtail kw tool
        $longtailkw_params = $this->_request->getParams();

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($longtailkw_params['file']) && isset($longtailkw_params['ext'])) {
			
            $filename = $longtailkw_params['file'] . "." . $longtailkw_params['ext'];
            $path_file = SEO_DOWNLOAD_LKWS . $filename;

            if (file_exists($path_file)) {
                $data = $this->getCSV($path_file); // Read csv file to an array
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), 'spl') ;
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $longtailkw_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
        } else {

            if (@$longtailkw_params['class'])
                $this->_view->class = $longtailkw_params['class'];

            $_POST['word_type'] = 1;
            $this->_view->word_type = $longtailkw_params['word_type']; // word type(text or file)

            if (@$msg)
                $this->_view->msg = $msg;
                
			// Processes a view script and returns the output.
            $this->render("seotool_longtailkws");
        }
    }
    
    /* Longtail keyword upload action */
    public function longtailkwuploadAction() {
		
		// Get all request variable posted for longtail kw tool
        $pos_params = $this->_request->getParams();
        
        if (isset($pos_params['submit'])) {
            
            $response = $this->responseMsg('', 0, 1, '', ''); // Response message
            
            $word_type = $pos_params['word_type']; // word type(text or file)
            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file
            $this->output_type = $pos_params['op_type']; // Results output format
            $this->site_id = $pos_params['site']; // site id
            $this->limit = $pos_params['limit']; // Result limit

            if ($word_type == 2) {
                $kw_text = trim($pos_params['kw']);
                if (($this->os == 'Windows'))
                    $kw_text = utf8_decode($kw_text);

                if ($kw_text) {
                    $kw_text1 = explode("\n", $kw_text);
                    $csv_file_name = "csv_" . time() . ".csv"; // Input csv file
                    $srcFile = SEO_UPLOAD_LKWS . $csv_file_name;
                    
                    // creating csv file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, str_replace("\'", "'", $kw_text));
                    fclose($fp);

                    $frequency = $this->checkFrequency();
                    if ($frequency == 'process') {
						// Processing longtailn kw tool
                        $response = $this->longtailkwuploadAndProcess($srcFile, $csv_file_name);
                    } else {
                        $response = $this->responseMsg(0, 0, 0, $frequency); // Response message
                    }
                    $response['word_type'] = $word_type; // word type(text or file)
                } else {
                    $response = $this->responseMsg(0, 16, $word_type); // Response message
                }
            } else if ($word_type == 1) {
                if (($_FILES['keyword_file']['type'] == 'text/comma-separated-values') || ($_FILES['keyword_file']['type'] == 'text/csv') || ($_FILES['keyword_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['keyword_file']['type'] == 'application/x-msexcel') || ($_FILES['keyword_file']['type'] == 'application/xls')) {
                    $file_info = pathinfo($_FILES['keyword_file']['name']);
                    $extension = $file_info['extension'];

                    if ($extension == 'xls') {
						// Read xls file to an array
                        $xls_array = $this->readInXLS($_FILES['keyword_file']['tmp_name']);
                        $u_file_name = str_replace(" ", "_", $file_info['filename']) . ".csv"; // Input csv file
                        $srcFile = SEO_UPLOAD_LKWS . $u_file_name;
                        $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                    } else {
                        $srcFile = $_FILES['keyword_file']['tmp_name'];
                        $u_file_name = str_replace(" ", "_", $_FILES['keyword_file']['name']);
                    }

                    $frequency = $this->checkFrequency();
                    if ($frequency == 'process') {
						// Processing longtail kw tool
                        $response = $this->longtailkwuploadAndProcess($srcFile, $u_file_name);
                    } else {
                        $response = $this->responseMsg(0, 0, 0, $frequency); // Response message
                    }
                } else {
                    $response = $this->responseMsg(0, 1); // Response message
                }
                $response['word_type'] = $word_type; // word type(text or file)
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }
    
    /* Longtail keyword tool function to connect to the linode server, uploading the csv/xls 
     * and processing the output file **/
    function longtailkwuploadAndProcess($srcFile, $u_file_name) {
        try {
            /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            //Path to execute ruby command
            $file_exec_path = $sftp->exec(SEO_LONG_KW_EXEC);
            $file_upload_path = $sftp->exec(SEO_GNEWS_UPLOAD); // seo input file upload path
            $file_download_path = $sftp->exec(SEO_GNEWS_DOWNLOAD); // output file download path
            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            $src = pathinfo($u_file_name);
            $download_fname = $src['filename'] . "_" . time();
            $dstfile = $download_fname . "." . $src['extension'];
            $encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');
            $limitt = $this->limit; // Result limit
            $site_idd = $this->site_id; // site id
            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId; // Logged in userid
            $ruby_file = SEO_LONG_KW_RB; // Ruby file for processing
            
            // Ruby command for data processing
            $cmd = "$ruby_file $site_idd $u_file_name $dstfile $limitt \"$encoding\" 1 $userId $loginName ";
            //$sftp->setTimeout(300); // sftp timeout
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
            
            $remoteFile = trim($file_download_path) . "/" . $dstfile;
            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_LKWS . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];
            $sftp->get($dstfile, $localFile); //downloading result file from linode server(using sftp)

            if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
                $csv_data = $this->getCSV($localFile); // Read csv file to an array
                if ($this->output_type == 2) {
                    $ext = "xls";
                    $output_file = SEO_DOWNLOAD_LKWS . $fname . "." . $ext;

                    $this->WriteXLS($csv_data, $output_file); // Output file in xls format
                }
                // Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seoresult.php?ext=' . $ext . '&filename=' . $fname . '&tool=longtailkws', SEO_DOWN_OP_FILE, 'longtailkws?action=view&file=' . $fname . $typeParam . '&ext=csv', SEO_VIEW_RESULTS);

            } else {
                throw new Exception($output);
            }
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
        }
        return $response;
    }

    /* Web keyword scraper function (Interface and view results) */
    public function scraperAction()
    {
        ini_set("display_errors", 0);
        
        // Get all request variable posted for scraper tool
        $scrape_params = $this->_request->getParams();

        if (@$scrape_params['class'])
            $this->_view->class = $scrape_params['class'];
        if (@$msg)
            $this->_view->msg = $msg;

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($scrape_params['file']) && isset($scrape_params['ext'])) {
			
            $filename = $scrape_params['file'] . "." . $scrape_params['ext'];
            $path_file = SEO_DOWNLOAD_SCRAPER . $filename;

            if (file_exists($path_file)) {
                $data = $this->readXLS($path_file); // Read excel(xls) file data to an array
                
                $sheetcount = 1;
                foreach ($data as $sheets) {
                    $i = 0;
                    $cols = sizeof(max($sheets)) ;

                    foreach ($sheets as $key=>$row) {
                        if(sizeof($row)<$cols){
                            $sheets[$key] = array_merge($sheets[$key], array_fill((sizeof($row)-1), ($cols-(sizeof($row))), ''));
                        }
                    }
                    
                    $table .= '<table class="table table-striped table-bordered dTableR" id="smpl_tbl'.$sheetcount.'">';
                    foreach ($sheets as $row) {
                        $table .= ($i==0 ? '<thead>' : ($i==1 ? '<tbody>' : '')).($i==0 ? '' : '<tr>');
                        foreach ($row as $td) {
                            if ($this->os != 'Windows') {
                                if ($i == 0)
                                    $table .= '<th>' . utf8_decode($td) . '</th>';
                                else
                                    $table .= '<td>' . utf8_decode($td) . '</td>';
                            } else {
                                if ($i == 0)
                                    $table .= '<th>' . ($td) . '</th>';
                                else
                                    $table .= '<td>' . ($td) . '</td>';
                            }
                        }
                        $table .= ($i==0 ? '</thead>' : ($i==0 ? '' : '</tr>'));//exit($table);
                        $i++;
                    }
                    $table .= '</tbody>';
                    $table .= SEO_TBL_TG_;
                    $sheetcount++;
                }
            }
            $this->_view->sheet_count = range(1,$sheetcount-1);
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $scrape_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");

        } elseif ($this->_request->isPost()) {

            $url = $scrape_params['url'];
            
             // Crawling type/option
            if ($scrape_params['crawl_type'])
                $crawl_type = $scrape_params['crawl_type'];
            else
                $crawl_type = 1;
                
			// Scraper model for crawling
            $scrape = new Ep_Scraper_Scraper($url, $crawl_type);
            $result = $scrape->getResult();
            $brokenURLs = $scrape->brokenURLs();
            $this->_view->result = $result;
            $this->_view->url = $url;
            $this->_view->crawl_type = $crawl_type; // Crawling type/option
            
            // Processes a view script and returns the output.
            $this->render('keyword_scraper');
        } else
            $this->render(!$scrape_params['linode'] ? 'linode_keyword_scraper' : 'keyword_scraper'); // Processes a view script and returns the output.
    }

    /* keyword scraper processing */
    public function keywordscraperAction()
    {
        $response = $this->responseMsg(0, 0, 1, ''); // Response message

		// Get all request variable posted for scraper tool
        $scrape_params = $this->_request->getParams();
        
        $loginName = $this->adminLogin->loginName; // Logged in username
        $userId = $this->adminLogin->userId; // Logged in userid
        $word_type = $scrape_params['word_type']; // word type(text or file)
        $this->output_type = $scrape_params['op_type']; // Results output format

        if ($word_type == 2) {
            $urls = explode("\n", trim($scrape_params['url']));
            $urls = array_map('trim', $urls);
            $url = implode("|", $urls);
        } else if ($word_type == 1) {

            if (($_FILES['url_file']['type'] == 'text/comma-separated-values') || ($_FILES['url_file']['type'] == 'text/csv') || ($_FILES['url_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['url_file']['type'] == 'application/x-msexcel') || ($_FILES['url_file']['type'] == 'application/xls')) {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file

                $file_info = pathinfo($_FILES['url_file']['name']);
                $extension = $file_info['extension'];

                if ($extension == 'xls') {
					// Read xls file to an array
                    $urls_array = $this->readInXLS($_FILES['url_file']['tmp_name']);
                } else {
					// Read csv file to an array
                    $urls_array = $this->getCSV($_FILES['url_file']['tmp_name']);
                }

                foreach ($urls_array as $row) :
                    foreach ($row as $value) :
                        $urlArr[] = $value;
                    endforeach;
                endforeach;

                $urlArr = array_map('trim', $urlArr);
                $url = implode("|", array_filter($urlArr));
            } else {
                $response = $this->responseMsg(0, 1, $word_type); // Response message
            }
        }

        $crawltype = $scrape_params['crawl_type']; // Crawling type/option
        $contentType = $scrape_params['content_type'];
        $exectime = (int)trim($scrape_params['exectime']);
        $exectimelimit = $scrape_params['exectimelimit'];
        $crawlcount = (int)trim($scrape_params['crawlcount']);

        if ($contentType[0] && $contentType[1]) :
            $crawlContentType = 'both';
        else :
            $crawlContentType = $contentType[0];
        endif ;

        if (empty($url)) {
            $response = $this->responseMsg(0, 9); // Response message
        } elseif (!$crawltype) {
            $response = $this->responseMsg(0, 10); // Response message
        } elseif (!$crawlContentType) {
            $response = $this->responseMsg(0, 11); // Response message
        } else {
            try {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                require_once SEO_SFTP_FILE; // php - sftp file

				/**creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }

                //Path to execute ruby command
                $file_exec_path = trim($sftp->exec(SEO_SCRAPER_EXEC));
                $csv_file = "results_" . time();
                $csv_file_name = $csv_file . ".xls";
                $ruby_file = SEO_SCRAPER_RB; // Ruby file for processing
                
                // Ruby command for data processing
                $cmd = "$ruby_file $userId $loginName $csv_file_name '$url' $crawltype '$crawlContentType' $crawlcount $exectime '$exectimelimit'";

                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                
                $file_download_path = $sftp->exec(SEO_SCRAPER_DOWNLOAD); // output file download path

                $remoteFile = trim($file_download_path) . "/" . $csv_file_name;
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                $localFile = SEO_DOWNLOAD_SCRAPER . $csv_file_name;
                $sftp->get($remoteFile, $localFile); //downloading result file from linode server(using sftp)

                if (file_exists($localFile)) {
					
					// Read xls file to an array
                    $xls_array = $this->readInXLS($localFile);
                    $this->writeCSV($xls_array, SEO_DOWNLOAD_SCRAPER . $csv_file . ".csv"); // Write data in csv format

					// Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG2, BO_DOMAIN_ . BO_PATH_ . "download_seo.php?saction=download&amp;file=" . $csv_file . "&ext=" . (($this->output_type != 2) ? 'csv' : 'xls'), SEO_DOWN_OP_FILE, 'scraper?action=view&file=' . $csv_file . '&ext=xls', SEO_VIEW_RESULTS);
                    
                } else {
                    throw new Exception($output);
                }

            } catch(Exception $e) {
                $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
            }
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }

    /* Scraper download action */
    public function downloadScraperAction()
    {
		// Get all request variable posted for scraper
        $scraper_params = $this->_request->getParams();
        
        if (isset($scraper_params['saction']) && $scraper_params['saction'] == 'download' && isset($scraper_params['file']))
            $this->scraperdownloadFile($scraper_params['file'], "xls");
        exit ;
    }
    
    /* Scraper download function */
    function scraperdownloadFile($filename, $extension) {

        $filename = $filename . "." . $extension;
        $path_file = SEO_DOWNLOAD_SCRAPER . $filename;
        if (file_exists($path_file)) {
            $attachment = new Ep_Message_Attachment();
            $attachment->downloadAttachment($path_file, 'attachment', $filename);
            exit ;
        } else {
            $class = "error";
            $msg = "File not Exist";
        }
        exit ;
    }
    
    /* Stop Words action */
    public function stopWordsAction() {
		
        $blackList = new Ep_Scraper_Blacklist();
        $stopwords = $blackList->stopwords();

		// // Get all request variable posted for stopwords
        $stopwords_params = $this->_request->getParams();

        if ($stopwords_params['saction'] == 'add' && $stopwords_params['filter']) {
            $blackList->addstopword($stopwords_params['filter']);
            exit ;
        } else if ($stopwords_params['saction'] == 'remove' && $stopwords_params['filter']) {

            $blackList->removestopword();
            exit ;
        } else {
            $stopwords = $blackList->stopwords();
        }

        $this->_view->stopwords = $stopwords;
        
        // Processes a view script and returns the output.
        $this->render('stopwords_scraper');
    }

    /* Broken url tool (Interface and results view) */
    public function brokenUrlAction()
    {
        ini_set("display_errors", 1);
        
        // Get all request variable posted for broken url tool
        $broken_params = $this->_request->getParams();

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($broken_params['file']) && isset($broken_params['ext'])) {

            $filename = $broken_params['file'] . "." . $broken_params['ext'];
            $path_file = SEO_DOWNLOAD_SCRAPER . $filename;

            if (file_exists($path_file)) {
                $data = $this->getCSV($path_file); // Read csv file to an array
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), 'spl') ;
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $broken_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
            
        } elseif ($this->_request->isPost()) {
            $response = $this->responseMsg('', 0, 0, '', ''); // Response message
            $url = $broken_params['url'];
            
            if ($url) {
				
				 // Crawling type/option
                if ($broken_params['crawl_type'])
                    $crawl_type = $broken_params['crawl_type'];
                else
                    $crawl_type = 1;
                
                // Scraper model for crawling to find broken urls
                $scrape = new Ep_Scraper_Broken($url, $crawl_type);
                
                $result = $scrape->getResult();
                $message = $scrape->getMessage();
                $response = $this->responseMsg(1, 0, 0, $message, $result); // Response message
            } else {
                $response = $this->responseMsg(0, 23); // Response message
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        } else
            $this->render($broken_params['linode'] ? 'linode_broken_url_finder' : 'broken_url_finder'); // Processes a view script and returns the output.
    }

    //broken url processing
    public function findbrokenurlAction() {
		
		// Get all request variable posted for broken url tool
        $broken_params = $this->_request->getParams();
        
        $loginName = $this->adminLogin->loginName; // Logged in username
        $userId = $this->adminLogin->userId; // Logged in userid

        $url = trim($broken_params['url']);
        $crawltype = $broken_params['crawl_type']; // Crawling type/option

        if (empty($url)) {
            $response = $this->responseMsg(0, 9); // Response message
        } elseif (!$crawltype) {
            $response = $this->responseMsg(0, 10); // Response message
        } else {
            try {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                require_once SEO_SFTP_FILE; // php - sftp file

				/**creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }

                //Path to execute ruby command
                $file_exec_path = trim($sftp->exec(SEO_BROKEN_URLS_EXEC));
                $csv_file = "results_broken_" . time();
                $csv_file_name = $csv_file . ".csv"; // Input csv file
                $ruby_file = SEO_BROKEN_URLS_RB; // Ruby file for processing
				
				// Ruby command for data processing
                $cmd = "$ruby_file $loginName $userId $csv_file_name '$url' $crawltype ";

                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                
                $file_download_path = $sftp->exec(SEO_BROKEN_URLS_DOWNLOAD); // output file download path
                $remoteFile = trim($file_download_path) . "/" . $csv_file_name;
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                $localFile = SEO_DOWNLOAD_SCRAPER . $csv_file_name;
                $sftp->get($remoteFile, $localFile); //downloading result file from linode server(using sftp)
                $csv_data = $this->getCSV($localFile); // Read csv file to an array
                $output_file = SEO_DOWNLOAD_SCRAPER . $csv_file . ".xls";
                $this->WriteXLS($csv_data, $output_file); // Output file in xls format

                if (file_exists($localFile)) {
					// Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG2, "download_seo.php?saction=download&amp;file=" . $csv_file . "&ext=xls", SEO_DOWN_OP_FILE, 'broken-url?action=view&file=' . $csv_file . '&ext=csv', SEO_VIEW_RESULTS);
                } else {
                    throw new Exception($output);
                }

            } catch(Exception $e) {
                $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
            }
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }

    /* Orphan url tool (Interface and results view) */
    public function orphanUrlAction() {
		
		// Get all request variable posted for orphan url tool
        $orphan_params = $this->_request->getParams();
        
        $loginName = $this->adminLogin->loginName; // Logged in username
        $userId = $this->adminLogin->userId; // Logged in userid

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($orphan_params['file']) && isset($orphan_params['ext'])) {

            $filename = $orphan_params['file'] . "." . $orphan_params['ext'];
            $path_file = SEO_DOWNLOAD_SCRAPER . $filename;

            if (file_exists($path_file)) {
                $data = $this->getCSV($path_file); // Read csv file to an array
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), 'spl') ;
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $orphan_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
            
        } elseif ($this->_request->isPost()) {
            $response = $this->responseMsg('', 0, 0, '', ''); // Response message

            $url = $orphan_params['url'];

            if ($url) {
				
				 // Crawling type/option
                if ($orphan_params['crawl_type'])
                    $crawl_type = $orphan_params['crawl_type'];
                else
                    $crawl_type = 1;

                $url_array = explode("\n", ($orphan_params['url']));

				// Scraper model for crawling to find orphan urls
                $orphan = new Ep_Scraper_Orphan($url_array, $crawl_type);
                
                $result = $orphan->getResult();
                $message = $orphan->getMessage();
                $response = $this->responseMsg(1, 0, 0, $message, $result); // Response message
            } else {
                $response = $this->responseMsg(0, 23); // Response message
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        } else
            $this->render($orphan_params['linode'] ? 'linode_orphan_url_finder' : 'orphan_url_finder'); // Processes a view script and returns the output.
    }

    //orphan url processing
    public function findorphanurlAction() {        
        
        // Get all request variable posted for orphan url tool
        $orphan_params = $this->_request->getParams();

        $url = str_replace("\n", "' '", trim($orphan_params['url']));
        
         // Crawling type/option
        if ($orphan_params['crawl_type'])
            $crawltype = $orphan_params['crawl_type'];
        else
            $crawltype = 1;

        if (empty($url)) {
            $response = $this->responseMsg(0, 9); // Response message
        } else {
            try {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                require_once SEO_SFTP_FILE; // php - sftp file
                
				/**creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }

                //Path to execute ruby command
                $file_exec_path = trim($sftp->exec(SEO_ORPHAN_URLS_EXEC));
                $csv_file = "results_orphan_" . time();
                $csv_file_name = $csv_file . ".csv"; // Input csv file
                $ruby_file = SEO_ORPHAN_URLS_RB; // Ruby file for processing

				// Ruby command for data processing
                $cmd = "$ruby_file $loginName $userId $csv_file_name '$url' ";
                
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                
                $file_download_path = $sftp->exec(SEO_ORPHAN_URLS_DOWNLOAD); // output file download path
                $remoteFile = trim($file_download_path) . "/" . $csv_file_name;
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                $localFile = SEO_DOWNLOAD_SCRAPER . $csv_file_name;
                $sftp->get($remoteFile, $localFile); //downloading result file from linode server(using sftp)
                $csv_data = $this->getCSV($localFile); // Read csv file to an array
                $output_file = SEO_DOWNLOAD_SCRAPER . $csv_file . ".xls";
                $this->WriteXLS($csv_data, $output_file); // Output file in xls format

                if (file_exists($localFile)) {
					// Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG2, "download_seo.php?saction=download&file=" . $csv_file . "&ext=xls", SEO_DOWN_OP_FILE, 'orphan-url?action=view&file=' . $csv_file . '&ext=csv', SEO_VIEW_RESULTS);
                } else {
                    throw new Exception($output);
                }

            } catch(Exception $e) {
                $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
            }
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }

    /* Compare links tool action (Interface and download processed file) */
    public function compareLinksAction() {
		
		// Get all request variable posted for link compare tool
        $link_params = $this->_request->getParams();
        
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($link_params['file']) && isset($link_params['ext']))
            $this->linksdownloadFile($link_params['file'], $link_params['ext']);

        if (@$link_params['class'])
            $this->_view->class = $link_params['class'];
        if (@$msg)
            $this->_view->msg = $msg;
            
		// Processes a view script and returns the output.
        $this->render('comparelinks');
    }

    public function validatelinksAction() {
		
		// Get all request variable posted for link validation tool
        $pos_params = $this->_request->getParams();
        
        if (isset($pos_params['submit'])) {

            $response = $this->responseMsg('', 0, 0, '', ''); // Response message
            require_once SEO_SFTP_FILE; // php - sftp file

            $this->url_text = trim($pos_params['url_text']);
            $this->comp_url_text = trim($pos_params['comp_url_text']);

            if ($this->url_text && $this->comp_url_text) {
                $url_text = "'" . $pos_params['url_text'] . "'";
                $comp_urls = explode("\n", $this->comp_url_text);
        $comp_urls = array_map('trim', $comp_urls);
                $comp_url_cmd = "'" . implode("' '", $comp_urls) . "'";
                $csv_file_name = "links_" . time() . ".xls";

                try {
					/**creating ssh component object**/
                    $sftp = new Net_SFTP($this->ssh2_server);
                    if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                        throw new Exception('Login Failed');
                    }

                    //Path to execute ruby command
                    $file_exec_path = trim($sftp->exec(SEO_LINKS_EXEC));
                    $ruby_file = SEO_LINKS_RB; // Ruby file for processing
                    
                    // Ruby command for data processing
                    $cmd = "$ruby_file $csv_file_name $url_text $comp_url_cmd ";
					$cmd = str_replace('\/', '/', $cmd); // Ruby command for data processing
                    $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                    
                    // Output by executing sftp command (using ruby command string)
                    $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                    
                    $file_download_path = $sftp->exec(SEO_LINKS_DOWNLOAD); // output file download path
                    $remoteFile = trim($file_download_path) . "/" . $csv_file_name;
                    $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                    $file_path = pathinfo($remoteFile);
                    $localFile = SEO_DOWNLOAD_LINKS . $csv_file_name;
                    $serverfile = $file_path;
                    $fname = $file_path['filename'];
                    $ext = $file_path['extension'];
                    $sftp->get($remoteFile, $localFile); //downloading result file from linode server(using sftp)

                    if (file_exists($localFile)) {
						// Response for success message (display with download and/or view results)
                        $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seoresult.php?ext=' . $ext . '&filename=' . $fname . '&tool=links', SEO_DOWN_OP_FILE);
                        
                    } else {
                        //throw new Exception($output);
						$response = $this->responseMsg(0, 0, 0, 'cmd=' . $cmd); // Response message
                    }

                } catch(Exception $e) {
                    $response = $this->responseMsg(0, 0, 0, ($e->getMessage() . $cmd)); // Response message
                }
            } else {
                $response = $this->responseMsg(0, 24, $word_type); // Response message
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }

    // Date format convertion
    function convert_date_format($value, $format) {
        $date = new DateTime($value);
        return $date->format($format);
    }
    
    public function twitterToolAction() {
        
        // Get all request variable posted for twitter tool
        $tw_params = $this->_request->getParams();
        
        if (isset($tw_params['submit'])) {
            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId; // Logged in userid
            $from_date = $this->convert_date_format($tw_params['from_date'], 'Y-m-d');
            $to_date = $this->convert_date_format($tw_params['to_date'], 'Y-m-d');
            $location = $tw_params['location'];
            $days = ((sizeof($tw_params['day'])>0) ? implode("|", $tw_params['day']) : '');
        
            $dts = explode("/", $tw_params['enddate']);
            $tmp=$dts[2]; $tmp1=$dts[0]; $dts[0]=$tmp; $dts[2]=$dts[1]; $dts[1]=$tmp1;
            $tw_params['enddate']= implode("-", $dts);//exit($tw_params['enddate']);
            $end_date = $tw_params['enddate']; // end date
            
            $frequency_option = $tw_params['frequency'];//echo '<pre>';print_r($tw_params);exit($from_date);
            $client = $tw_params['client'];
            $feedname = $tw_params['feedname'];
            $email = $tw_params['email']; // Email to send
            //$location ='nil';
            
            require_once SEO_SFTP_FILE; // php - sftp file
            $response = $this->responseMsg('', 0, 0, '', ''); // Response message
            
            $rand = "tw_".time();
            $csv_file_name = $rand . ".csv"; // Input csv file
            $outputfile = $rand . ".xlsx";
            
            if($tw_params['word_type']==2)
            {
                $kw_text = trim($tw_params['kw']);
                if ($this->os == 'Windows')
                    $kw_text = utf8_decode($kw_text);
                $kw_text1 = explode("\n", $kw_text);
                //$csv_file_name = "csv_" . time() . ".csv";
                $srcFile = SEO_UPLOAD_TWTL . $csv_file_name;
                
                // creating csv file
                $fp = fopen($srcFile, 'w');
                fwrite($fp, str_replace("\'", "'", $kw_text));
                fclose($fp);
            }
            else
            {
                $file_info = pathinfo($_FILES['keyword_file']['name']);
                $extension = $file_info['extension'];

                if ($extension == 'xls') {
					// Read xls file to an array
                    $xls_array = $this->readInXLS($_FILES['keyword_file']['tmp_name']);

                    $srcFile = SEO_UPLOAD_TWTL . $csv_file_name;
                    $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                } else {
                    $srcFile = $_FILES['keyword_file']['tmp_name'];
                    //$csv_file_name = str_replace(" ", "_", $_FILES['keyword_file']['name']);
                }
            }

            try {
				/**creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }

                $file_upload_path = $sftp->exec(SEO_TWTL_UPLOAD); // seo input file upload path
                $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
                $sftp->put($csv_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            
                //Path to execute ruby command
                $file_exec_path = trim($sftp->exec(SEO_TWTL_EXEC));
                
                $ruby_file = SEO_TWTL_RB; // Ruby file for processing
                $ruby_frequency_file = SEO_TWTL_FREQ_RB; // Ruby file for frequency processing
                
                // Ruby command for data processing
                if ($frequency_option)
                    $cmd = "$ruby_frequency_file $csv_file_name '$feedname' \"$days\"  \"$end_date\" '$email' '$location' $userId $loginName ";
                else
                    $cmd = "$ruby_file $userId $loginName $csv_file_name $outputfile '$from_date' '$to_date' '$location'";

                $cmd = str_replace('\/', '/', $cmd); // Ruby command for data processing
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                
                $file_download_path = $sftp->exec(SEO_TWTL_DOWNLOAD); // output file download path
                $remoteFile = trim($file_download_path) . "/" . $outputfile;
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                $file_path = pathinfo($remoteFile);
                $localFile = SEO_DOWNLOAD_TWTL . $outputfile;
                $serverfile = $file_path;
                $fname = $file_path['filename'];
                $ext = $file_path['extension'];
                
                if ($frequency_option)
                {
                    if (trim($output) == SEO_RVM_NOTATION) {
						// Response for success message (display with download and/or view results)
                        $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG4.'  cmd=' . $cmd, '', '');

                    } else {
                        //throw new Exception($output);
                        // Response message
                        $response = $this->responseMsg(0, 0, 0, 'cmd=' . $cmd.'<br>op=#'.trim($output).'#<br>opfile='.$localFile);
                    }
                }
                else
                {
                   $sftp->get($remoteFile, $localFile); //downloading result file from linode server(using sftp)

                    if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
						// Response for success message (display with download and/or view results)
                        $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1.'  cmd=' . $cmd, BO_PATH . '/download_seoresult.php?ext=' . $ext . '&filename=' . $fname . '&tool=twittertool', SEO_DOWN_OP_FILE);
                        
                    } else {
                        //throw new Exception($output);
                        // Response message
                        $response = $this->responseMsg(0, 0, 0, 'cmd=' . $cmd.'<br>op=#'.trim($output).'#<br>opfile='.$localFile);
                    }
                }
            } catch(Exception $e) {
				// Response message
                $response = $this->responseMsg(0, 0, 0, ($e->getMessage() . $cmd));
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
        else
        {
            /*$client_info_obj = new Ep_User_User();
            $client_info = $client_info_obj->GetclientList(); // Getting all client list from database
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
            $this->_view->client_list = $client_list;*/
            
            ini_set('max_execution_time', 1000);
            
            if($tw_params['load'] || $tw_params['edit'] || $tw_params['update'] || $tw_params['delete'])
            {
                require_once SEO_SFTP_FILE; // php - sftp file
                
                /**creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }        
                //Path to execute php command
                $file_exec_path = $sftp->exec(SEO_TWTL_EXEC);
            }
            
            if($tw_params['load'])
            {
				// PHP command for data processing
                $cmd = "php tweets.php 2>&1 ";
                $file_exec_path = trim($file_exec_path); //Path to execute ruby command
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using php command string)
                $output = $sftp->exec("cd $file_exec_path;$cmd ;");
                
                $outputs = unserialize($output);
                //echo '<pre>';print_r($outputs);exit();
                $htm = '';
                $idx = 1;
                foreach($outputs as $pr)
                {
                    $date = (($pr['created_at'] != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($pr['created_at'])) : 'NA');
                    $htm .= '<tr id=tr'.$pr['id'].'><td>'.$idx.'</td><td>'.utf8_encode($pr['feed_name']).'</td><td>'.$pr['bo_user_name'].'</td><td>'.$date.'</td><td>'.('<a data-hint="Edit list" class="hint--left hint--info" href="/seotool/twitter-tool-edit?id='.$pr['id'].'"><img class="splashy-folder_modernist_edit"></a>&nbsp;&nbsp;<a onclick="' . "deleteTw('".$pr['id']."')".';" href="javascript:void(0);"><i class="splashy-folder_modernist_remove"></i></a>').'</td></tr>';
                    $idx++;
                }
                exit($htm);
            }
            elseif($tw_params['update'])
            {
                $response = $this->responseMsg('', 0, 0, '', '');// Response message
                $freq_params['bo_user_name'] = $this->adminLogin->loginName; // Logged in username
                $freq_params['bo_user_id'] = $this->adminLogin->userId; // Logged in userid
                $freq_params['userlocation'] = $tw_params['location'];

                $dts = explode("/", $tw_params['enddate']);
                $tmp=$dts[0]; $dts[0]=$dts[2]; $tmp1=$dts[1]; $dts[1]=$tmp; $dts[2]=$tmp1;
                $freq_params['end_date'] = implode("-", $dts); // end date
                
                $freq_params['feed_name'] = utf8_decode($tw_params['feedname']);
                $freq_params['emailid'] = $tw_params['email']; // Frequency email
                $freq_params['updated_at'] = date('Y-m-d H:i:s');
                
                foreach (array("sunday","monday","tuesday","wednesday","thursday","friday","saturday") as $day)
                    $freq_params['days'][$day] = 0;
                
                if(sizeof($tw_params['day'])>0)
                    foreach ($tw_params['day'] as $key => $value)
                        $freq_params['days'][$value] = 1;
                
                
                $rand = "tw_".time();
                $csv_file_name = $rand . ".csv"; // Input csv file
                
                if($tw_params['word_type']==2)
                {
                    $kw_text = utf8_decode(trim($tw_params['kw']));
                    if ($this->os == 'Windows')
                        $kw_text = utf8_decode($kw_text);
                    $kw_text1 = explode("\n", $kw_text);
                    if(sizeof($kw_text1)>0)
                    {
                        $srcFile = SEO_UPLOAD_TWTL . $csv_file_name;//print_r($kw_text1);exit($srcFile);
                        
                        // creating csv file
                        $fp = fopen($srcFile, 'w');
                        fwrite($fp, str_replace("\'", "'", $kw_text));
                        fclose($fp);
                    }
                    else
                    {
                        $csv_file_name=0;
                    }
                }
                else
                {
                    if($_FILES['keyword_file']['name'])
                    {
                        $file_info = pathinfo($_FILES['keyword_file']['name']);
                        $extension = $file_info['extension'];
        
                        if ($extension == 'xls') {
							// Read xls file to an array
                            $xls_array = $this->readInXLS($_FILES['keyword_file']['tmp_name']);
                            $srcFile = SEO_UPLOAD_TWTL . $csv_file_name;
                            $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                        } else {
                            $srcFile = $_FILES['keyword_file']['tmp_name'];
                        }
                    }
                    else
                    {
                        $csv_file_name=0;
                    }
                }

                if($csv_file_name)
                {
                    $file_upload_path = $sftp->exec(SEO_TWTL_UPLOAD); // seo input file upload path
                    $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
                    $sftp->put($csv_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
                    $freq_params['file_path'] = $csv_file_name;
                }
                $freq_params['id'] = $tw_params['id'];
                //echo '<pre>';print_r($freq_params);exit;

				// PHP command for data processing
                $cmd = "php tweets.php update ".base64_encode(serialize($freq_params))." 2>&1 ";
                
                $file_exec_path = trim($file_exec_path); //Path to execute ruby command
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using php command string)
                $output = $sftp->exec("cd $file_exec_path;$cmd ;");
                
                if($output=='updated')
                {
					// Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG4, '', '');
                }
                print json_encode($response); // Resonse array encoded in json format
                exit ;
            }
            elseif($tw_params['delete'])
            {
				// PHP command for data processing
                $cmd = "php tweets.php delete ".$tw_params['id']." 2>&1 ";
                $file_exec_path = trim($file_exec_path); //Path to execute ruby command
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $output = $sftp->exec("cd $file_exec_path;$cmd ;");
                
                exit($output);
            }
            elseif($tw_params['list'])
            {
                $this->_view->lnk = "twitter-tool";
                $this->_view->lbl = "Add";
                $this->render('twitter_list'); // Processes a view script and returns the output.
            }
            else
            {
                $this->_view->lnk = "twitter-tool-list";
                $this->_view->lbl = "List";
                $this->render('twitter_tool'); // Processes a view script and returns the output.
            }
        } 
    }
    
    public function twitterToolListAction() {
        
        $this->_view->lnk = "twitter-tool";
        $this->_view->lbl = "Add";
        
        // Processes a view script and returns the output.
        $this->render('twitter_list');
    }
    
    public function twitterToolEditAction() {

		// Get all request variable posted for twitter edit
        $tw_params = $this->_request->getParams();
        
        ini_set('max_execution_time', 1000);
        
        require_once SEO_SFTP_FILE; // php - sftp file
        
        /**creating ssh component object**/
        $sftp = new Net_SFTP($this->ssh2_server);
        if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
            throw new Exception('Login Failed');
        }
        //Path to execute php command
        $file_exec_path = $sftp->exec(SEO_TWTL_EXEC);
        
        // PHP command for data processing
        $cmd = "php tweets.php edit ".$tw_params['id']." 2>&1 ";
        $file_exec_path = trim($file_exec_path); //Path to execute php command
        $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
        
        // Output by executing sftp command (using ruby command string)
        $output = $sftp->exec("cd $file_exec_path;$cmd ;");
        
        $outputs = unserialize($output);
        $dts = explode("-", substr($outputs[0]['end_date'], 0, strpos($outputs[0]['end_date'], ' ')));
        $tmp=$dts[0]; $dts[0]=$dts[1]; $dts[1]=$dts[2]; $dts[2]=$tmp;
        $outputs[0]['end_date'] = implode("/", $dts); // end date
        $outputs[0]['kws'] = str_replace("|", "\n", ($outputs['kws']));
        $this->_view->outputs = $outputs[0];
        $this->_view->lnk = "twitter-tool-list";
        $this->_view->lbl = "List";

		// Processes a view script and returns the output.
        $this->render('twitter_edit');
    }

    public function googleurlAction() {
		
		// Get all request variable posted for google url
        $googleurl_params = $this->_request->getParams();
        
        if (@$googleurl_params['class'])
            $this->_view->class = $googleurl_params['class'];
        if (@$msg)
            $this->_view->msg = $msg;
		
		// Processes a view script and returns the output.
        $this->render('updategoogleurl');
    }

    public function updategoogleurlAction() {
		
		// Get all request variable posted for google url
        $googleurl_params = $this->_request->getParams();
        
        if (isset($googleurl_params['submit'])) {
            $response = $this->responseMsg('', 0, 0, '', '');// Response message
            require_once SEO_SFTP_FILE; // php - sftp file
            $url = trim($googleurl_params['url_text']);

            if (!empty($url)) {
                try {
                    /**creating ssh component object**/
                    $sftp = new Net_SFTP($this->ssh2_server);
                    if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                        throw new Exception('Login Failed');
                    }

                    //Path to execute ruby command
                    $file_exec_path = $sftp->exec(SEO_UPDATE_GOOGLE_URL_EXEC);
                    $country = array('1' => 'france', '2' => 'general', '3' => 'portuguese', '4' => 'india', '5' => 'united kingdom');
                    $tool = $googleurl_params['tool'];
                    $site = $googleurl_params['site'];
                    $country_name = $country[$site];
                    $ruby_file = SEO_UPDATE_GOOGLE_URL_RB; // Ruby file for processing
                    $cmd = "$ruby_file $tool $site $country_name \"$url\""; // Ruby command for data processing
                    $sftp->setTimeout(300); // sftp timeout
                    $file_exec_path = trim($file_exec_path); //Path to execute ruby command
                    $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                    
                    // Output by executing sftp command (using ruby command string)
                    $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                    
                    $response = $this->responseMsg(1, 12); // Response message

                } catch(Exception $e) {
                    $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
                }
            } else {
                $response = $this->responseMsg(0, 25); // Response message
            }
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }

    public function validateclientspageAction() {
		
		// Get all request variable posted for client validation page
        $tag_params = $this->_request->getParams();
        
        if (@$tag_params['class'])
            $this->_view->class = $tag_params['class'];
        if (@$msg)
            $this->_view->msg = $msg;
		
		// Processes a view script and returns the output.
        $this->render('validateclientspage');
    }

    public function tagscriptAction() {
		
		// Get all request variable posted for tag script
        $tag_params = $this->_request->getParams();

        if (isset($tag_params['submit'])) {
            require_once SEO_SFTP_FILE; // php - sftp file

            $url = trim($tag_params['url_text']);
            $tag = $tag_params['tag_text'];
            $tag = trim(str_replace('\"', '"', $tag));
            $email = trim($tag_params['email']); // Email to send

            if (!empty($url) && !empty($tag)) {
                /**creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }

                //Path to execute ruby command
                $file_exec_path = $sftp->exec(SEO_TAG_EXEC);
                $file_exec_path = trim($file_exec_path);
                $sftp->setTimeout(300); // sftp timeout
                $ruby_file = SEO_TAG_RB; // Ruby file for processing
                $cmd = "$ruby_file '$url' '$tag' '$email'"; // Ruby command for data processing
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output modified after executing sftp command (using ruby command string)
                $out_put = trim(str_replace(SEO_RVM_NOTATION, '', $sftp->exec("$ruby_switch_prefix;cd $file_exec_path;$cmd;")));

                if ($out_put) :
                    $response = $this->responseMsg(1, 13); // Response message
                else :
                    $response = $this->responseMsg(0, 14); // Response message
                endif ;
            } else {
                $response = $this->responseMsg(0, 15); // Response message
            }
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }

    public function fbTwitterLikeShareCountAction() {
		
		// Get all request variable posted for facebook-twitter tool
        $fbtwitter_params = $this->_request->getParams();
        
        /*if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($fbtwitter_params['file']) && isset($fbtwitter_params['ext']))
            $this->_redirect(BO_PATH_ . 'download_seoresult.php?ext=' . $fbtwitter_params['ext'] . '&filename=' . $fbtwitter_params['file'] . '&tool=fbtwitter');*/

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($fbtwitter_params['file']) && isset($fbtwitter_params['ext'])) {
			
            $filename = $fbtwitter_params['file'] . "." . $fbtwitter_params['ext'];
            $path_file = SEO_DOWNLOAD_FBTWITTER . $filename;

            if (file_exists($path_file)) {
                $data = $this->getCSV($path_file); // Read csv file to an array
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), 'spl') ;
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $fbtwitter_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
            
        } else {

            if (@$fbtwitter_params['class'])
                $this->_view->class = $fbtwitter_params['class'];

            $_POST['word_type'] = 1;
            $this->_view->word_type = $fbtwitter_params['word_type']; // word type(text or file)

            if (@$msg)
                $this->_view->msg = $msg;

			// Processes a view script and returns the output.
            $this->render("fbtwitterlikesharecount");
        }
    }

    public function fbTwitterAction() {
		
		// Get all request variable posted for facebook-twitter tool
        $pos_params = $this->_request->getParams();
        
        if (isset($pos_params['submit'])) {
            $response = $this->responseMsg('', 0, 1, '', ''); // Response message

            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file
            
            $word_type = $pos_params['word_type']; // word type(text or file)
            $this->output_type = $pos_params['op_type']; // Results output format
            if ($word_type == 2) {
                $kw_text = trim($pos_params['kw']);
                if (($this->os == 'Windows'))
                    $kw_text = utf8_decode($kw_text);

                if ($kw_text) {
                    $kw_text1 = explode("\n", $kw_text);
                    $csv_file_name = "fb" . time() . ".csv"; // Input csv file
                    $srcFile = SEO_UPLOAD_FBTWITTER . $csv_file_name;
                    
                    // creating csv file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, str_replace("\'", "'", $kw_text));
                    fclose($fp);
					
					// Processing facebook-twitter tool
                    $response = $this->fbTwitterProcess($srcFile, $csv_file_name);
                    $response['word_type'] = $word_type; // word type(text or file)
                } else {
                    $response = $this->responseMsg(0, 16, $word_type); // Response message
                }
            } else if ($word_type == 1) {
                if (($_FILES['keyword_file']['type'] == 'text/comma-separated-values') || ($_FILES['keyword_file']['type'] == 'text/csv') || ($_FILES['keyword_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['keyword_file']['type'] == 'application/x-msexcel') || ($_FILES['keyword_file']['type'] == 'application/xls')) {
                    $file_info = pathinfo($_FILES['keyword_file']['name']);
                    $extension = $file_info['extension'];

                    if ($extension == 'xls') {
						// Read xls file to an array
                        $xls_array = $this->readInXLS($_FILES['keyword_file']['tmp_name']);
                        $u_file_name = str_replace(" ", "_", $file_info['filename']) . ".csv"; // Input csv file
                        $srcFile = SEO_UPLOAD_FBTWITTER . $u_file_name;
                        $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                    } else {
                        $srcFile = $_FILES['keyword_file']['tmp_name'];
                        $u_file_name = str_replace(" ", "_", $_FILES['keyword_file']['name']);
                    }
					
					// Processing facebook-twitter tool
                    $response = $this->fbTwitterProcess($srcFile, $u_file_name);
                    $response['word_type'] = $word_type; // word type(text or file)
                } else {
                    $response = $this->responseMsg(0, 1, $word_type); // Response message
                }
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }

	// Function for processing facebook-twitter tool
    function fbTwitterProcess($srcFile, $u_file_name) {
        try {
            /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            //Path to execute ruby command
            $file_exec_path = $sftp->exec(SEO_FB_TWITTER_EXEC);
            $file_upload_path = $sftp->exec(SEO_FB_TWITTER_UPLOAD); // seo input file upload path
            $file_download_path = $sftp->exec(SEO_FB_TWITTER_DOWNLOAD); // output file download path

            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            $src = pathinfo($u_file_name);
            $download_fname = $src['filename'] . "_" . time();
            $dstfile = $download_fname . "." . $src['extension'];
            $encoding = 'UTF-8';
            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId; // Logged in userid
            $ruby_file = SEO_FB_TWITTER_RB; // Ruby file for processing
			
			// Ruby command for data processing
            $cmd = trim("$ruby_file $u_file_name $dstfile '$encoding' $userId $loginName");

            $sftp->setTimeout(300); // sftp timeout
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");

            $remoteFile = trim($file_download_path) . "/" . $dstfile;

            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_FBTWITTER . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];

            //downloading the file from remote server
            $sftp->get($dstfile, $localFile);

            if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
                $csv_data = $this->getCSV($localFile); // Read csv file to an array
                if ($this->output_type == 2) {
                    $ext = "xls";
                    $output_file = SEO_DOWNLOAD_FBTWITTER . $fname . "." . $ext;
                    $this->WriteXLS($csv_data, $output_file); // Output file in xls format
                }
                // Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seoresult.php?ext=' . $ext . '&filename=' . $fname . '&tool=fbtwitter', SEO_DOWN_OP_FILE, 'fb-twitter-like-share-count?action=view&file=' . $fname . $typeParam . '&ext=csv', SEO_VIEW_RESULTS);

            } else {
                throw new Exception($output);
            }
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
        }
        return $response;
    }

    function unzip($file) {
        $zip_file = pathinfo($file);
        $zip_file['filename'] = str_replace(" ", "-", $zip_file['filename']);
        $path = $zip_file['dirname'] . "/" . $zip_file['filename'];
        if (!is_dir($path))
            mkdir($path, 0777, TRUE);

        chmod($path, 0777);

        $zip = new ZipArchive;
        $res = $zip->open($file);
        if ($res === TRUE) {
            // extract it to the path we determined above
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if ((substr($entry, -1) == '/') || strstr($entry, '__MACOSX'))
                    continue;

                $entry1 = frenchCharsToEnglish(str_replace(' ', '_', $entry));
                $fp = $zip->getStream($entry);
                $ofp = fopen($path . '/' . basename($entry1), 'w');
                if ($fp) {
                    while (!feof($fp))
                        fwrite($ofp, fread($fp, 8192));
                }
                fclose($fp);
                fclose($ofp);
            }
            return $path;
        } else {
            echo "Doh! I couldn't open $file";
        }
    }

    function is_empty_dir($dir) {
        if (($files = @scandir($dir)) && count($files) <= 2) {
            return true;
        }
        return false;
    }

    public function seoCompareAction() {
		
		// Get all request variable posted for seo compare tool
        $seocompare_params = $this->_request->getParams();
        
        /*if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($seocompare_params['file']) && isset($seocompare_params['ext']))
            $this->_redirect(BO_PATH_ . 'download_seoresult.php?ext=' . $seocompare_params['ext'] . '&filename=' . $seocompare_params['file'] . '&tool=seocompare');*/

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($seocompare_params['file']) && isset($seocompare_params['ext'])) {
			
            $filename = $seocompare_params['file'] . "." . $seocompare_params['ext'];

            $path_file = SEO_DOWNLOAD_COMP . $filename;

            if (file_exists($path_file)) {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                $data = $this->readInXLS($path_file); // Read xls file to an array
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->readInXLS($path_file), 'scompare') ;
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
        } else {
            $this->render("seocompare"); // Processes a view script and returns the output.
        }
    }

    public function seoPositionCompareAction() {
		
		// Get all request variable posted for seo position compare tool
        $seopos_params = $this->_request->getParams();
        
        /*if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'download' && isset($seopos_params['file']) && isset($seopos_params['ext']))
            $this->_redirect(BO_PATH_ . 'download_seoresult.php?ext=' . $seopos_params['ext'] . '&filename=' . $seopos_params['file'] . '&tool=seopositioncompare');*/

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($seopos_params['file']) && isset($seopos_params['ext'])) {
			
			/* View results */
            $filename = $seopos_params['file'] . "." . $seopos_params['ext'];

            if (file_exists(SEO_DOWNLOAD_POS_COMP . $filename)) {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                
                // Read xls file to an array
                $data = $this->readInXLS(SEO_DOWNLOAD_POS_COMP . $filename);
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
        } else {
			
			// Processes a view script and returns the output.
            $this->render("seopositioncompare");
        }
    }

    public function seoCompareProcessAction() {
		
		// Get all request variable posted for seo compare process
        $seo_compare_params = $this->_request->getParams();
        
        require_once SEO_SFTP_FILE; // php - sftp file
        try {
            /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            $file_exec_path = trim($sftp->exec(SEO_COMPARE_EXEC)); //Path to execute ruby command
            $file_download_path = trim($sftp->exec(SEO_COMPARE_DOWNLOAD)); // output file download path
            $urls = explode("\n", $seo_compare_params['urls']);
            $urls = array_values(array_filter(array_map('trim', $urls)));
            $url_text = implode('|', $urls);

            if ((sizeof($urls) > 4) || (sizeof($urls) < 2)) {
				// Response message
                $response = $this->responseMsg(0, 18); 
            } elseif (!empty($url_text) && (sizeof($seo_compare_params['options']) > 0)) {
                $options = implode('|', $seo_compare_params['options']);
                $op_file_name = "results_" . time() . ".xls";
                $file_path = pathinfo($op_file_name);
                if (($this->os == 'Windows'))
                    $url_text = utf8_decode($url_text);
                $loginName = $this->adminLogin->loginName; // Logged in username
                $userId = $this->adminLogin->userId; // Logged in userid
                $ruby_file = SEO_COMPARE_RB; // Ruby file for processing

				// Ruby command for data processing
                $cmd = trim("$ruby_file $userId $loginName $op_file_name '$url_text' '$options'");
                
                $sftp->setTimeout(300); // sftp timeout
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                $localFile = SEO_DOWNLOAD_COMP . $op_file_name;
                $sftp->get($op_file_name, $localFile); //downloading result file from linode server(using sftp)

                if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
					// Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG3, BO_PATH . '/download_seoresult.php?ext=' . $file_path['extension'] . '&filename=' . $file_path['filename'] . '&tool=seocompare', SEO_DOWN_OP_FILE, 'seo-compare?action=view&file=' . $file_path['filename'] . '&ext=' . $file_path['extension'], SEO_VIEW_RESULTS);

                } else {
                    throw new Exception($output);
                }
            } else {
				// Response message
                $response = $this->responseMsg(0, 19);
            }

        } catch(Exception $e) {
			// Response message
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage()));
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }

    public function seoPositionCompareProcessAction() {
		
		// Get all request variable posted for seo position compare process
        $seo_compare_params = $this->_request->getParams();
        
        $word_type = $seo_compare_params['word_type']; // word type(text or file)
        $op_file = "results_" . time();
        $op_file_csv = $op_file . ".csv"; // Output csv file
        $op_file_xls = $op_file . ".xls";
        $output_csv = SEO_UPLOAD_POS_COMP . $op_file_csv;
        $output_xls = SEO_UPLOAD_POS_COMP . $op_file_xls;
        $csv_file_path = pathinfo($output_csv);
        $xls_file_path = pathinfo($xls_file_path);

        if ($word_type == 2) {
            $kw_text = trim($seo_compare_params['kw']);

            if (!empty($kw_text)) {
				
                $kw_text = html_entity_decode($kw_text,ENT_QUOTES,"UTF-8");
                $kw_text1 = explode("\n", $kw_text);
                
                // creating csv file
                $fp = fopen($output_csv, 'w');
                fwrite($fp, str_replace("\'", "'", $kw_text));
                fclose($fp);
                
            } else {
				// Response message
                $response = $this->responseMsg(0, 16);
            }
        } else if ($word_type == 1) {
            $file_info = pathinfo($_FILES['keyword_file']['name']);
            $extension = $file_info['extension'];

            if ($extension == 'xls') {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                move_uploaded_file($_FILES['keyword_file']['tmp_name'], $output_xls);
                
                // Read xls file to an array
                $data = $this->readInXLS($output_xls);
                
                $this->writeCSV($data, $output_csv); // Write data in csv format
            } elseif ($extension == 'csv') {
                move_uploaded_file($_FILES['keyword_file']['tmp_name'], $output_csv);
            } else {
				// Response message
                $response = $this->responseMsg(0, 1);
            }
        }

        if ($response['type'] != 'error') {
            require_once SEO_SFTP_FILE; // php - sftp file
            try {
                /**creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }

                $file_exec_path = trim($sftp->exec(SEO_POSITION_COMPARE_EXEC)); //Path to execute ruby command
                $file_download_path = trim($sftp->exec(SEO_POSITION_COMPARE_DOWNLOAD)); // output file download path
                $file_upload_path = trim($sftp->exec(SEO_POSITION_COMPARE_UPLOAD)); // seo input file upload path
                $site = implode('|', $seo_compare_params['site']);

                if (sizeof($seo_compare_params['site']) > 0) {
                    if (($this->os == 'Windows'))
                        $url_text = utf8_decode($url_text);
                    $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
                    $sftp->put($op_file_csv, $output_csv, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
                    $loginName = $this->adminLogin->loginName; // Logged in username
                    $userId = $this->adminLogin->userId; // Logged in userid
                    $ruby_file = SEO_POSITION_COMPARE_RB; // Ruby file for processing
                    
                    // Ruby command for data processing
                    $cmd = trim("$ruby_file '$site' $op_file_csv $op_file_csv 100 'UTF-8' 1 $userId $loginName");
                    
                    $sftp->setTimeout(300); // sftp timeout
                    $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                    
                    // Output by executing sftp command (using ruby command string)
                    $out_put = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                    
                    $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                    $sftp->get($op_file_csv, SEO_DOWNLOAD_POS_COMP . $op_file_csv); //downloading result file from linode server(using sftp)

                    if (file_exists($output_csv) && trim($out_put) == SEO_RVM_NOTATION) {
                        $csv_data = $this->getCSV(SEO_DOWNLOAD_POS_COMP . $op_file_csv); // Read csv file to an array
                        $this->WriteXLS($csv_data, SEO_DOWNLOAD_POS_COMP . $op_file_xls); // Output file in xls format
                        
                        // Response for success message (display with download and/or view results)
                        $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG3, BO_PATH . '/download_seoresult.php?ext=xls&filename=' . $op_file . '&tool=seopositioncompare', SEO_DOWN_OP_FILE, 'seo-position-compare?action=view&file=' . $op_file . '&ext=xls', SEO_VIEW_RESULTS);
                    } else {
                        //$response = $this->responseMsg(0, 0, 0, ($out_put . '--' . SEO_DOWNLOAD_POS_COMP . $output_csv));
                        throw new Exception($output);
                    }
                } else {
                    $response = $this->responseMsg(0, 20); // Response message
                }
            } catch(Exception $e) {
                $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
            }
        }
        print json_encode($response); // Resonse array encoded in json format
        exit ;
    }

    public function keywordXlsAction()
    {
		// Get all request variable posted for black list kw tool
        $kw_params = $this->_request->getParams();
        
        /* View results */
        if(isset($_REQUEST['action']) && $_REQUEST['action']=='view' && isset($kw_params['file']) && isset($kw_params['ext']))
        {
            error_reporting(0);
            if($kw_params['ext'] == 'xls')
            {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                $filename=$kw_params['file'].".".$kw_params['ext'];
                $path_file=SEO_DOWNLOAD_SCRAPER . $filename;

                if(file_exists($path_file))
                {
                    $data = new Spreadsheet_Excel_Reader();
                    $data->read($path_file);
                    if($data->sheets[0]['numRows'])
                    {
                        $x=1;
                        while($x<=$data->sheets[0]['numRows']) {
                            $y=1;
                            while($y<=$data->sheets[0]['numCols']) {
                                $xls_array[$x][$y]   =   $data->sheets[0]['cells'][$x][$y] ;
                                $y++;
                            }
                            $x++;
                        }
                    }
                    $table=SEO_TBL_TG;
                    $i=0;
                    foreach($xls_array as $row)
                    {
                        $table .= ($i==0 ? '<thead>' : ($i==1 ? '<tbody>' : '<tr>')) ;
                        foreach($row as $td)
                        {
                            $table.='<td>'.utf8_decode($td).'</td>';
                        }
                        $table.= ($i==0 ? '</thead>' : '</tr>');
                        $i++;
                    }
                    $table.='</tbody>' . SEO_TBL_TG_;
                }
            }
            $this->_view->table =  $table ; // View results with template stored into smarty variable
            $this->_view->word_type =  $kw_params['word_type'] ; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
        }
        else
        {
            if($_REQUEST['msg']=='success' && $kw_params['file'])
            {                
                $this->_view->msg = "File Successfully uploaded and processed.<br>" ;
                $this->_view->msg.='<a href="/' . BO_PATH_ . 'download_seo.php?saction=download&file='.$_REQUEST['file'].'&ext='.$_GET['ext'].'">Cick here to download</a>' ;
                if($_GET['ext'] == 'xls')
                    $this->_view->msg.=' / <a target="_result" href="/seotool/keyword-xls?action=view&file='.$_REQUEST['file'].'&ext=xls">View result</a>' ;
                $this->_view->class = 'success' ;
            }
            else
            {
                $this->_view->class = '' ;
                $this->_view->msg = '' ;
            }
            
            // Processes a view script and returns the output.
            $this->render('keyword_count_xls');
        }   
    }

    public function keywordXlsUploadAction()
    {
		// Get all request variable posted for black list kw process
        $kw_params = $this->_request->getParams();
        
        if(isset($kw_params['submit']))
        {
            $response = $this->responseMsg('', 0, 0, '', ''); // Response message
            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            $kw_text=trim($kw_params['kw']);
            
            // Processing for black list keyword with the file uploaded (doc, docx, xls or xlsx)
            if($_FILES['keyword_file']['name'])
            {
				/// Processing with the given keywords
                if($kw_text)
                {
                    $reference=$kw_params['index_reference'] ;
                    $kw_text=explode("\n",($kw_text)) ;
                    $file_info=pathinfo($_FILES['keyword_file']['name']) ;
                    $extension=$file_info['extension'] ;
                    
                    // If the file input is xls
                    if($extension == 'xls')
                    {
                        $data = new Spreadsheet_Excel_Reader();
                        
                        // Reading all xls cel values to an array
                        $data->read($_FILES['keyword_file']['tmp_name']);
                        if($data->sheets[0]['numRows'])
                        {
                            $x=1;
                            while($x<=$data->sheets[0]['numRows']) {
                                $y=1;
                                while($y<=$data->sheets[0]['numCols']) {
                                    $xls_array[$x][$y]   =   $data->sheets[0]['cells'][$x][$y] ;
                                    $y++;
                                }
                                $x++;
                            }
                        }
                        $xls_array=array_filter($xls_array) ;
                        
                        // Processing black list keyword functionality for excel cell values
                        $final_array=$this->processKeywordXLS($xls_array,$kw_text,$reference) ;
                        
                        $filename="results_".time() ;
                        $filename_result = SEO_DOWNLOAD_SCRAPER . $filename . ".xls" ;
                        
                        //  Saving result file
                        $this->WriteKeywordXLS(array_values($final_array),$filename_result,$filename) ;
                    }
                    // If the file input is doc/docx
                    elseif($extension == 'docx' || $extension == 'doc')
                    {
                        $filename  =   "results_".time() ;
                        $file = SEO_DOWNLOAD_SCRAPER.$filename.".".$extension ;
                        $txtFile = SEO_DOWNLOAD_SCRAPER.$filename.".txt" ;
                        $docFile = SEO_DOWNLOAD_SCRAPER.$filename.".doc" ;
                        $htmlFile = SEO_DOWNLOAD_SCRAPER.$filename.".html" ;
                        copy($_FILES['keyword_file']['tmp_name'], $file) ;
                        
                        // Converting doc/doc contents to txt before processing
                        if($extension == 'doc')
                            $this->o_docToTxt($file,$txtFile) ;
                        elseif($extension == 'docx')
                            $this->o_docxToTxt($file,$txtFile) ;

						// Processing black list keyword functionality txt file content
                        $results   =   $this->processKwWord( $txtFile, $kw_text, $extension ) ;
                        
                        $txtFileContents = file_get_contents($txtFile);
                        if($extension == 'docx')
                            $txtFileContents = utf8_decode($txtFileContents) ;

						// creating txt file
                        $fp = fopen($txtFile, 'w+');
                        fwrite($fp, $this->convert_smart_quotes($txtFileContents.($results)));
                        fclose($fp);
						
						// creating html file
                        $fp = fopen($htmlFile, 'w+');
                        fwrite($fp, HTML_UTF_HEADER);
                        fwrite($fp, utf8_encode(file_get_contents($txtFile)));
                        fclose($fp);

                        require_once SEO_HTML_TO_DOC ;

						//  Saving result file in doc format
                        chmod("$htmlFile",0777);
                        $htmltodoc= new HTML_TO_DOC();
                        $htmltodoc->createDoc("$htmlFile",str_replace('.doc', '', $docFile));
                        chmod($docFile,0777);
                        header("Location:/seotool/keyword-xls?msg=success&file=".$filename."&ext=".(($extension == 'doc' || $extension == 'docx') ? 'doc' : $extension)."&submenuId=ML8-SL12");
                    }
                    // If the file input is xlsx
                    elseif($extension == 'xlsx')
                    {
                        include SEO_XLSX_READER ;
                        $xlsx = new SimpleXLSX($_FILES['keyword_file']['tmp_name']);
                        //echo "<pre>";print_r($xlsx);exit;
                        for($j=1;$j <= $xlsx->sheetsCount();$j++){
                            list($cols) = $xlsx->dimension($j);
                            if(count($xlsx->rows($j))>0)
                            {
                                $row=0;
                                foreach( $xlsx->rows($j) as $k => $r) {
                                    for( $i = 0; $i < $cols; $i++) {
                                        $xlsArr[$row+1][$i+1] = ( isset($r[$i]) ? ($this->convert_smart_quotes(utf8_decode($r[$i]))) : '' ) ;
                                    }
                                    $row++;
                                }
                            }
                        }
                        $xls_array=array_filter($xlsArr) ;
                        
                        // Processing black list keyword functionality for excel cell values
                        $final_array=$this->processKeywordXLS($xls_array,$kw_text,$reference) ;
                        
                        $filename="results_".time() ;
                        $filename_result = SEO_DOWNLOAD_SCRAPER.$filename.".xls" ;
                        
                        //  Saving result file
                        $this->WriteKeywordXLS(array_values($final_array),$filename_result,$filename) ;
                    }

                    if($extension == 'xls' || $extension == 'xlsx')
                        $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seo.php?saction=download&ext=xls&file=' . $filename, SEO_DOWN_OP_FILE, 'keyword-xls?action=view&file=' . $filename . '&ext=xls', SEO_VIEW_RESULTS, ''); // Response for success message (display with download and/or view results)
                    else
                        $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seo.php?saction=download&ext=doc&file=' . $filename, SEO_DOWN_OP_FILE, '', '', ''); // Response for success message (display with download and/or view results)

                    print json_encode($response); // Resonse array encoded in json format
                    exit ;
                }
            }
        }
    }

	// Function for processing black list keyword functionality for excel cell values
    public function processKeywordXLS($xls_array,$keyword_array,$reference)
    {
        $cnt=0;
        $final_array=array();
        
        // Looping through each 
        foreach($xls_array as $key=>$karray)
        {
            $content_words=array();
            $next_column='';
            $karray=array_values(($karray));
            $column_count=count($karray);
            
            //getting all keywords from content
            $final_array[$key]=$karray ;
            $ref_content=$final_array[$key][$reference-1];
            $ref_content    =   strtolower($ref_content);

			// Blck list keyword check only if content not empty and excluding first row
            if($ref_content && $cnt>0)
            {
                foreach($keyword_array as $kword)
                {
                    $kword=($kword);
                    $kword=strtolower(trim($kword));
                    $count  =   0 ;
                    
                    // Getting substring count for keyword from reference content
                    if(strstr($ref_content, " ".$kword." ") || (strstr($ref_content, substr($content, 0, strlen($ref_content))) && (substr($ref_content, 0, strlen($kword)) == $kword)) || (strstr($ref_content, substr($ref_content, (strlen($ref_content) - strlen($kword)), strlen($ref_content))) && (substr($ref_content, (strlen($ref_content) - strlen($kword)), strlen($ref_content)) == $kword)))
                    {
                        $next_column.=$kword." - ".substr_count($ref_content, $kword)."\n" ;
                    }
                    $kword1 = $kword.'s' ;
                    
                    // Getting substring count for keyword(appended with "s") from reference content
                    if(strstr($ref_content, " ".$kword1." ") || (strstr($ref_content, substr($content, 0, strlen($ref_content))) && (substr($ref_content, 0, strlen($kword1)) == $kword1)) || (strstr($ref_content, substr($ref_content, (strlen($ref_content) - strlen($kword1)), strlen($ref_content))) && (substr($ref_content, (strlen($ref_content) - strlen($kword1)), strlen($ref_content)) == $kword1)))
                    {
                            $next_column.=$kword." - ".substr_count($ref_content, $kword)."\n" ;
                    }
                }
                // Adding keyword count info to result array
                array_unshift($final_array[$key],$next_column);
            }
            else{
                if($cnt == 0)
                    array_unshift($final_array[$key],'Black list kw count'); // Header column for keyword count
            }
            $cnt++ ;
        }
        return $final_array;
    }
    //////internal plagiarism for excel files only//
    public function internalPlagExcelAction()
    {
        // Get all request variable posted for black list kw tool
        $kw_params = $this->_request->getParams();

        /* View results */
        if(isset($_REQUEST['action']) && $_REQUEST['action']=='view' && isset($kw_params['file']) && isset($kw_params['ext']))
        {
            error_reporting(0);
            if($kw_params['ext'] == 'xls')
            {
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                $filename=$kw_params['file'].".".$kw_params['ext'];
                $path_file=SEO_DOWNLOAD_SCRAPER . $filename;

                if(file_exists($path_file))
                {
                    $data = new Spreadsheet_Excel_Reader();
                    $data->read($path_file);
                    if($data->sheets[0]['numRows'])
                    {
                        $x=1;
                        while($x<=$data->sheets[0]['numRows']) {
                            $y=1;
                            while($y<=$data->sheets[0]['numCols']) {
                                $xls_array[$x][$y]   =   $data->sheets[0]['cells'][$x][$y] ;
                                $y++;
                            }
                            $x++;
                        }
                    }
                    $table=SEO_TBL_TG;
                    $i=0;
                    foreach($xls_array as $row)
                    {
                        $table .= ($i==0 ? '<thead>' : ($i==1 ? '<tbody>' : '<tr>')) ;
                        foreach($row as $td)
                        {
                            $table.='<td>'.utf8_decode($td).'</td>';
                        }
                        $table.= ($i==0 ? '</thead>' : '</tr>');
                        $i++;
                    }
                    $table.='</tbody>' . SEO_TBL_TG_;
                }
            }
            $this->_view->table =  $table ; // View results with template stored into smarty variable
            $this->_view->word_type =  $kw_params['word_type'] ; // word type(text or file)

            // Processes a view script and returns the output.
            $this->render("seotool_view");
        }
        else
        {
            if($_REQUEST['msg']=='success' && $kw_params['file'])
            {  echo "hello"; exit;
                $this->_view->msg = "File Successfully uploaded and processed.<br>" ;
                $this->_view->msg.='<a href="/' . BO_PATH_ . 'download_seo.php?saction=download&file='.$_REQUEST['file'].'&ext='.$_GET['ext'].'">Cick here to download</a>' ;
                if($_GET['ext'] == 'xls')
                    $this->_view->msg.=' / <a target="_result" href="/seotool/keyword-xls?action=view&file='.$_REQUEST['file'].'&ext=xls">View result</a>' ;
                $this->_view->class = 'success' ;
            }
            else
            {
                $this->_view->class = '' ;
                $this->_view->msg = '' ;
            }

            // Processes a view script and returns the output.
            $this->render('seotool_internal_plag_excel');
        }
    }
    public function internalPlagxlsUploadAction()
    {
       /* $response = $this->responseMsg('', 0, 0, '', ''); // Response message
        $localFile = "/home/sites/site6/web/BO/seo_download/internalplag/SS15pourtraduction_1423210801.xlsx";
        $filename = "SS15pourtraduction_1423210801";
                if (file_exists($localFile)) { //echo "ddd";
                    // Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg($cmd, BO_DOMAIN_ . BO_PATH_ . "download_seoresult.php?filename=" . $filename . "&ext=xlsx&tool=internalplag", SEO_DOWN_OP_FILE);
                } else {
                    //throw new Exception($output);
                    $response = $this->responseMsg(0, 0, 0, ($cmd)); // Response message
                }
        //$this->_view->msg = $response;
       // $this->render('seotool_internal_plag_excel');
        print json_encode($response); // Resonse array encoded in json format
        exit ;
        echo "hello"; exit;*/
        // Get all request variable posted for black list kw process
        $intplag_params = $this->_request->getParams();

        if(isset($intplag_params['submit']))
        {
            $density = str_replace(",","|",$intplag_params['density']);
            $response = $this->responseMsg('', 0, 0, '', ''); // Response message
            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file
       //  print_r($intplag_params); exit;
            $row_index = $intplag_params['column_id'];
            $column_index = $intplag_params['column_index'];
            $numchar = $intplag_params['numchar'];
            $numword = $intplag_params['numword'];
            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId; // Logged in userid

 		    $file_info = pathinfo($_FILES['internalplag_file']['name']);
            $extension = strtolower($file_info['extension']);

            if ($extension == 'csv') {

                $srcFile = $_FILES['internalplag_file']['tmp_name'];
                $u_file_name = str_replace(" ", "_", $_FILES['internalplag_file']['name']);
                $output_csv = SEO_UPLOAD_INTPLAG . $u_file_name;
                move_uploaded_file($srcFile, $output_csv);

                $src = pathinfo($u_file_name);
                $download_fname = $src['filename'] . "_" . time();
                $dstfile = $download_fname . ".xlsx" ;
                /* *creating ssh component object**/
                $sftp = new Net_SFTP($this->ssh2_server);
                if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                    throw new Exception('Login Failed');
                }
                ////moving the bo uploaded file to linode server for porcessing//
                $file_upload_path = $sftp->exec(SEO_INTERNALPLAG_UPLOAD); // seo input file upload path in linode
                $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
                $sftp->put($u_file_name, $output_csv, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp

                //Path to execute ruby command
               $file_exec_path = trim($sftp->exec(SEO_INTERNALPLAG_EXEC));
                $csv_file = "results_" . time();
                $csv_file_name = $u_file_name;
                $ruby_file = SEO_INTERNALPLAG_RB; // Ruby file for processing

                // Ruby command for data processing
               $cmd = "$ruby_file $userId $loginName '$csv_file_name' '$dstfile' $row_index $column_index $numchar $numword '$density'";
               $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)

                // Output by executing sftp command (using ruby command string)
               $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
               $file_download_path = $sftp->exec(SEO_INTERNALPLAG_DOWNLOAD); // output file download path
                $remoteFile = trim($file_download_path) . "/" . $dstfile;
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                $file_path = pathinfo($remoteFile);
                $localFile = SEO_DOWNLOAD_INTPLAG . $file_path['basename'];
                $serverfile = $file_path;
                $fname = $file_path['filename'];
                $ext = $file_path['extension'];
                $sftp->get($dstfile, $localFile); //downloading result file from linode server(using sftp)
                if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
                    $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG2.$cmd, BO_DOMAIN_ . BO_PATH_ . "download_seoresult.php?filename=" . $fname . "&ext=xlsx&tool=internalplag", SEO_DOWN_OP_FILE);
                   // $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG2.$cmd, BO_DOMAIN_ . BO_PATH_ . "download_seoresult.php?filename=" . $fname . "&ext=xlsx&tool=plagiarism", SEO_DOWN_OP_FILE, 'internal-plag-excel?action=view&file=' . $fname . '&ext=xlsx', SEO_VIEW_RESULTS);
                } else {
                    $response = $this->responseMsg(0, 0, 0, ($cmd)); // Response message
                }
                print json_encode($response); // Resonse array encoded in json format
                exit ;
            }
        }
    }

    public function processKwWord($txtFile, $keyword_array, $extension)
    {
        $results='<br>' ;        
        $ref_content    =   (strtolower(file_get_contents($txtFile))) ;
        
        // Char encoding only with docx file content
        if($extension== 'docx')
            $ref_content    =   utf8_decode($ref_content) ;
        
        // Blck list keyword check only if content not empty
        if($ref_content)
        {
			// Checking substring count for each keywords
            foreach($keyword_array as $kword)
            {
                $kword=(strtolower(trim($kword)));
                
                // Getting substring count for keyword from reference content
                if(strstr($ref_content, " ".$kword." ") || (strstr($ref_content, substr($content, 0, strlen($ref_content))) && (substr($ref_content, 0, strlen($kword)) == $kword)) || (strstr($ref_content, substr($ref_content, (strlen($ref_content) - strlen($kword)), strlen($ref_content))) && (substr($ref_content, (strlen($ref_content) - strlen($kword)), strlen($ref_content)) == $kword)))
                {
                    $results.="<br>Number of <u>".($kword)."</u> Words :: ".substr_count($ref_content, $kword)."<br>" ;
                }
                $kword1 = $kword.'s' ;
                
                // Getting substring count for keyword(appended with "s") from reference content
                if(strstr($ref_content, " ".$kword1." ") || (strstr($ref_content, substr($content, 0, strlen($ref_content))) && (substr($ref_content, 0, strlen($kword1)) == $kword1)) || (strstr($ref_content, substr($ref_content, (strlen($ref_content) - strlen($kword1)), strlen($ref_content))) && (substr($ref_content, (strlen($ref_content) - strlen($kword1)), strlen($ref_content)) == $kword1)))
                {
                    $results.="<br>Number of <u>".($kword)."</u> Words :: ".substr_count($ref_content, $kword)."<br>" ;
                }
            }
        }
        return $results;
    }


    /**function to create XLS file**/
    function WriteKeywordXLS($data,$file_name,$filename)
    {    
        // include package
        include SEO_XLS_WRITER_INCLUDE;

        // create empty file        
        $excel = new Spreadsheet_Excel_Writer($file_name);
        $excel->setVersion(8); 

        // add worksheet
        $sheet =& $excel->addWorksheet();
        $sheet->setInputEncoding('UTF-8');
        
        //$sheet->setInputEncoding('ISO-8859-1');
        // create format for header row
        // bold, red with black lower border
        $firstRow =& $excel->addFormat();
        $firstRow->setBold();
        $firstRow->setSize(12);
        $firstRow->setBottom(1);
        $firstRow->setBottomColor('black');

        // add data to worksheet
        $rowCount=0;
        foreach ($data as $row) {
        $sheet->setColumn(0,count($row),20);
          foreach ($row as $key => $value) {
            $sheet->write($rowCount, $key, utf8_encode($value)) ;
          }
          $rowCount++;
        }
        // save file to disk
        if ($excel->close() === true) {
            //exit("/seotool/keyword-xls?msg=success&file=".$filename."&submenuId=ML8-SL12");
          header("Location:/seotool/keyword-xls?msg=success&file=".$filename."&ext=xls&submenuId=ML8-SL15");
        }
    }

    //loading black list kws per client
    public function loadblkwsAction() 
    {   
        exit(file_get_contents(ROOT_PATH . BO_PATH_ . BLKWS_PATH . ($_REQUEST['client']).".txt")) ;
    }

    public function loadtemplatesAction(){
		$client=$_GET['client'];
		$temp_obj=new Ep_SEO_Mkwbl();
        $templates=$temp_obj->loadTemplates(array('id','title'),$client);
       // print_r($templates);
		$htm='';
		$htm.="<option value=''>Select Template</option>";
        foreach($templates as $key => $value){
			$htm.="<option value='".$value->id."'>".$value->title."</option>";
		}
		echo $htm;   
	}

    public function editkwsAction(){

		$text = $_POST['kws'];
		$text = preg_replace('/\s+/',',',str_replace(array("\r\n","\r","\n"),' ',trim($text)));
		$open = fopen(ROOT_PATH . BO_PATH_ . BLKWS_PATH . ($_REQUEST['client']).".txt","w+"); 
		fwrite($open, $text); 
		fclose($open);

		exit(file_get_contents(ROOT_PATH . BO_PATH_ . BLKWS_PATH . ($_REQUEST['client']).".txt")) ;
	}

    function o_docToTxt($filein, $fileout)
    {
        $doc2txt = "/usr/bin/antiword ";
        
        // Shell command to excecute antiword
        $cmd = $doc2txt." ".$filein." > ".$fileout."";
        
        $ret = 0;
        if(file_exists($filein))
        {
            $output = array();
            shell_exec($cmd);
        } 
        else 
        {
          $ret = -1;
        }
        return $ret;
    }

    function o_docxToTxt($path, $outpath)
    {
        if (!file_exists($path))
            return -1;
        $zh = zip_open($path);
        $content = "";
        while (($entry = zip_read($zh))){
            $entry_name = zip_entry_name($entry);
            if (preg_match('/word\/document\.xml/im', $entry_name)){
                $content = zip_entry_read($entry, zip_entry_filesize($entry));
                break;
            }
        }
        $text_content = "";
        if ($content){
            $xml = new XMLReader();
            $xml->XML($content);
            while($xml->read()){
                if ($xml->name == "w:t" && $xml->nodeType == XMLReader::ELEMENT){
                    $text_content .= $xml->readInnerXML();
                    $space = $xml->getAttribute("xml:space");
                    if ($space && $space == "preserve")
                        $text_content .= " ";
                }
                if (($xml->name == "w:p" || $xml->name == "w:br" || $xml->name == "w:cr") && $xml->nodeType == XMLReader::ELEMENT)
                    $text_content .= "\n";
                if (($xml->name == "w:tab") && $xml->nodeType == XMLReader::ELEMENT)
                    $text_content .= "\t";
            }
            file_put_contents($outpath, $text_content);
            return 0;
        }
        return -1;
    }

    public function pageRankAnalysisAction()
    {
		// Get all request variable posted for page rank analysis tool
        $pr_params=$this->_request->getParams();
        
        /* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($pr_params['file']) && isset($pr_params['ext'])) {
            $filename = $pr_params['file'] . "." . $pr_params['ext'];
            $path_file = SEO_DOWNLOAD_PR . $filename;

            if (file_exists($path_file)) {
                header('Content-Type: text/html; charset=utf-8');
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), 'pagerank') ;
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $pr_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
        } else {
            if ($pr_params['class'])
                $this->_view->class = $pr_params['class'];

            $_POST['word_type'] = 1;
            $this->_view->word_type = $pr_params['word_type']; // word type(text or file)

			// Processes a view script and returns the output.
            $this->render('page_rank_analysis');
        }
    }

    public function keywordGeneratorAction()
    {
		// Get all request variable posted for meta kw generation tool
        $key_gen_params=$this->_request->getParams();
        
        /* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($key_gen_params['file']) && isset($key_gen_params['ext'])) {
            $filename = $key_gen_params['file'] . "." . $key_gen_params['ext'];
            $path_file = SEO_DOWNLOAD_KW_GENERATOR . $filename;

            if (file_exists($path_file)) {
                header('Content-Type: text/html; charset=utf-8');
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), 'kw_generator') ;
            }
            //exit($table);
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $key_gen_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
        } else {
            if ($pr_params['class'])
                $this->_view->class = $key_gen_params['class'];

            $_POST['word_type'] = 1;
            $this->_view->word_type = $key_gen_params['word_type']; // word type(text or file)

			// Processes a view script and returns the output.
            $this->render('kw_generator');
        }
    }

    public function keywordGenerationProcessAction()
    {
		// Get all request variable posted for meta kw generation process
        $key_gen_params=$this->_request->getParams();
        
        if (isset($key_gen_params['submit'])) {
            // response hash
            $response = $this->responseMsg('', 0, 1, '', ''); // Response message

            $this->type = $key_gen_params['word_type']; // word type(text or file)
            $this->output_type = $key_gen_params['op_type']; // Results output format
            $this->site_id = $key_gen_params['site']; // site id
            $this->limit = $key_gen_params['limit']; // Result limit

            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file

            if ($this->type == 2) {
                $kw_text = trim($key_gen_params['kw']);
                if (($this->os == 'Windows'))
                    $kw_text = utf8_decode($kw_text);

                if ($kw_text) {
                    $kw_text1 = explode("\n", $kw_text);

                    $csv_file_name = time() . ".csv"; // Input csv file
                    $srcFile = SEO_UPLOAD_KW_GENERATOR . $csv_file_name;
                    
                    // creating csv file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, $kw_text);
                    fclose($fp);
                    
                    // Processing meta info generator tool
                    $response = $this->keyGenUploadAndProcess($srcFile, $csv_file_name);
                    $response['word_type'] = $this->type; // word type(text or file)
                } else {
                    $response = array('type' => 'error', 'message' => 'Please enter URL(s)', 'word_type' => $this->type);
                }
            } else if ($this->type == 1) {
                if (($_FILES['keyword_file']['type'] == 'text/comma-separated-values') || ($_FILES['keyword_file']['type'] == 'text/csv') || ($_FILES['keyword_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['keyword_file']['type'] == 'application/x-msexcel') || ($_FILES['keyword_file']['type'] == 'application/xls')) {
                    $file_info = pathinfo($_FILES['keyword_file']['name']);
                    $extension = $file_info['extension'];
                    if ($extension == 'xls') {
						
						// Read xls file to an array
                        $xls_array = $this->readInXLS($_FILES['keyword_file']['tmp_name']);
                        $u_file_name = str_replace(" ", "_", $file_info['filename']) . ".csv"; // Input csv file
                        $srcFile = SEO_UPLOAD_KW_GENERATOR . $u_file_name;
                        $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                    } else {
                        $srcFile = $_FILES['keyword_file']['tmp_name'];
                        $u_file_name = str_replace(" ", "_", $_FILES['keyword_file']['name']);
                    }
                    // Processing meta info generator tool
                    $response = $this->keyGenUploadAndProcess($srcFile, $u_file_name);
                } else {
                    $response = $this->responseMsg(0, 1); // Response message
                }
                $response['word_type'] = $this->type; // word type(text or file)
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }

    public function pageRankAnalysisProcessAction()
    {
		// Get all request variable posted for page rank analysis process
        $pr_params=$this->_request->getParams();
        
        if (isset($pr_params['submit'])) {
            // Response message
            $response = $this->responseMsg('', 0, 1, '', ''); 
            
            $this->type = $pr_params['word_type']; // word type(text or file)
            $this->title = $pr_params['title']; // Title for page rank
            $this->output_type = $pr_params['op_type']; // Results output format
            $this->frequency_option = $pr_params['frequency'];
            $this->end_date = $pr_params['enddate']; // end date
            $this->creation_date = date('Y-m-d H:i:s');
            $this->cron_email = $pr_params['email']; // cron email
            @$days = array_flip(array_values($pr_params['day'])) ;
            foreach($days as $key=>$val) $days[$key] = 1;
            $this->days = (sizeof($pr_params['day'])>0 ? $days : '') ;

            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file

            if ($this->type == 2) {
                $url_text = trim($pr_params['urls']);
                if ($this->os == 'Windows')
                    $url_text = utf8_decode($url_text);

                if ($url_text) {
                    $url_text1 = explode("\n", $url_text);

                    $csv_file_name = time() . ".csv"; // Input csv file
                    $srcFile = SEO_UPLOAD_PR . $csv_file_name;
                    
                    // creating csv file
                    $fp = fopen($srcFile, 'w');
                    fwrite($fp, $url_text);
                    fclose($fp);
                    
                    // Processing page rank tool
                    $response = $this->prUploadAndProcess($srcFile, $csv_file_name);
                    $response['word_type'] = $this->type; // word type(text or file)
                } else {
                    $response = array('type' => 'error', 'message' => 'Please enter URL(s)', 'word_type' => $this->type);
                }
            } else if ($this->type == 1) {
                if (($_FILES['url_file']['type'] == 'text/comma-separated-values') || ($_FILES['url_file']['type'] == 'text/csv') || ($_FILES['url_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['url_file']['type'] == 'application/x-msexcel') || ($_FILES['url_file']['type'] == 'application/xls')) {
                    $file_info = pathinfo($_FILES['url_file']['name']);
                    $extension = $file_info['extension'];
                    if ($extension == 'xls') {
						
						// Read xls file to an array
                        $xls_array = $this->readInXLS($_FILES['url_file']['tmp_name']);
                        $u_file_name = str_replace(" ", "_", $file_info['filename']) . ".csv"; // Input csv file
                        $srcFile = SEO_UPLOAD_PR . $u_file_name;
                        $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                    } else {
                        $srcFile = $_FILES['url_file']['tmp_name'];
                        $u_file_name = str_replace(" ", "_", $_FILES['url_file']['name']);
                    }
                    // Processing page rank tool
                    $response = $this->prUploadAndProcess($srcFile, $u_file_name);
                } else {
					// Response message
                    $response = $this->responseMsg(0, 1);
                }
                $response['word_type'] = $this->type; // word type(text or file)
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
    }

	// Function to process page rank tool
    function prUploadAndProcess($srcFile, $u_file_name) {
        try {
            $csv_data = $this->getCSV($srcFile) ; // Read csv file to an array
            $file_path = pathinfo($u_file_name);
                        
            if($this->frequency_option==1)
            {
                $frequency['frequency_file'] = $u_file_name;
                $frequency['user_id'] = $this->adminLogin->userId;  // Logged in userid
                $frequency['title'] =   $this->title ; // Title for page rank
                $frequency['days'] =  $days  =  $this->days ;
                $frequency['end_date'] =   $this->end_date ; // end date
                $frequency['creation_date'] =   $this->creation_date ;
                $frequency['email'] =   $this->cron_email ; // cron email
                
                if($days[strtolower(date('l'))])
                {
                    $i = 1 ;
                    foreach($csv_data as $url)
                    {
                        $data[$i][0]   =   $csv_data[$i][0];
                        $data[$i][1]   =   $this->get_google_pagerank($url[0]) ;
                        $data[$i][2]   =   date('Y-m-d H:i:s');
                        $i++ ;
                    }
                }
                $frequencyObj = new Ep_Seo_Frequency() ;
                $frequencyObj->insertSchedule($frequency, $data) ;
                $response = $this->responseMsg(1, 31) ; // Response message
            }
            else
            {
                $data[1][0] =   'Url';   $data[1][1] =   'Page Rank';   $i = 1 ;
                foreach($csv_data as $url)
                {
                    $data[$i+1][0]   =   $csv_data[$i][0];
                    $data[$i+1][1]   =   $this->get_google_pagerank($url[0]) ;
                    $i++ ;
                }
                $this->writeCSV($data, SEO_DOWNLOAD_PR . $u_file_name) ; // Write data in csv format
                if($this->output_type==2)
                {
                    $this->WriteXLS($data, SEO_DOWNLOAD_PR . $file_path['filename'] . ".xls") ; // Output file in xls format
                }
                // Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seoresult.php?ext=' . ($this->output_type==2 ? 'xls' : 'csv') . '&filename=' . $file_path['filename'] . '&tool=pagerank', SEO_DOWN_OP_FILE, 'page-rank-analysis?action=view&file=' . $file_path['filename'] . '&ext=csv', SEO_VIEW_RESULTS) ;
            }            
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage().$cmd)); // Response message
        }
        return $response;
    }

    public function blacklistsAction()
    {
		// Get all request variable posted for blacklists tool
        $blacklists_params=$this->_request->getParams();
        
        /* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($blacklists_params['file']) && isset($blacklists_params['ext'])) {
			
			// Processes a view script and returns the output.
            $this->render("seotool_view");
            
        } else {            
            require_once SEO_SFTP_FILE; // php - sftp file
            
            /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }
            
            //Path to execute php command
            $file_exec_path = $sftp->exec(SEO_BLIST_EXEC);
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $cmd = "php list.php p 2>&1 "; // PHP command for data processing
            
            // Output by executing sftp command (using php command string)
            $output = $sftp->exec("cd $file_exec_path;$cmd ;");
            
            $output = str_replace(SEO_RVM_NOTATION, "", $output);
            $this->_view->kws = utf8_decode($output);
            
            // Processes a view script and returns the output.
            $this->render('blacklists');
        }
    }

    public function blacklistsProcessAction()
    {
		// Get all request variable posted for blacklists process
        $blacklists_params=$this->_request->getParams();
        
        $type=$blacklists_params['type'];
        $csv_file_name = "blist" . time() . ".csv"; // Input csv file
        $srcFile1 = SEO_UPLOAD1_BLIST . $csv_file_name;
        $srcFile2 = SEO_UPLOAD2_BLIST . $csv_file_name;
        
        if(in_array(1, $blacklists_params['type']))
        {
			// creating csv file
            $fp = fopen($srcFile1, 'w');
            fwrite($fp, str_replace("\'", "'", $blacklists_params['keyword']));
            fclose($fp);
        }
        if(in_array(2, $blacklists_params['type']))
        {
			// creating csv file
            $fp = fopen($srcFile2, 'w');
            fwrite($fp, str_replace("\'", "'", $blacklists_params['keyword']));
            fclose($fp);
        }

        require_once SEO_SFTP_FILE; // php - sftp file
        
        /**creating ssh component object**/
        $sftp = new Net_SFTP($this->ssh2_server);
        if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
            throw new Exception('Login Failed');
        }
        
        //Path to execute php command
        $file_exec_path = $sftp->exec(SEO_BLIST_EXEC);
        $file_exec_path = trim($file_exec_path); //Path to execute ruby command
        
        if(in_array(1, $blacklists_params['type']))
        {
            $file_upload_path = $sftp->exec(SEO_BLIST_UPLOAD1); // seo input file upload path
            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($csv_file_name, $srcFile1, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
        }
        if(in_array(2, $blacklists_params['type']))
        {
            $file_upload_path = $sftp->exec(SEO_BLIST_UPLOAD2); // seo input file upload path
            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($csv_file_name, $srcFile2, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
        } 
        
        $type = (in_array(1, $blacklists_params['type']) ? 'normal' :'');
        $type1 = (in_array(2, $blacklists_params['type']) ? 'anchor' :'');
        
        // PHP command for data processing  
        $cmd = "php insert.php $csv_file_name $type $type1 2>&1 ";

        $sftp->setTimeout(300); // sftp timeout
        $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
        
        // Output by executing sftp command (using ruby command string)
        $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
        
        $output = trim(str_replace(SEO_RVM_NOTATION, "", $output));

        if($output == 'inserted')
        {
			// PHP command for data processing
            $cmd = "php list.php p 2>&1 ";
            
            // Output by executing sftp command (using php command string)
            $output = $sftp->exec("cd $file_exec_path;$cmd ;");
            $output = str_replace(SEO_RVM_NOTATION, "", $output);//exit($output);
        }
        // Response for success message (display with download and/or view results)
        $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG3, '', '');
        $response['data'] = $output;

        print json_encode($response); // Resonse array encoded in json format
        exit;
    }

    public function blacklistsDeleteAction()
    {
		// Get all request variable posted for blacklists delete process
        $blacklists_params=$this->_request->getParams();
        
        $id=$blacklists_params['id'];

        require_once SEO_SFTP_FILE; // php - sftp file
        
        /**creating ssh component object**/
        $sftp = new Net_SFTP($this->ssh2_server);
        if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
            throw new Exception('Login Failed');
        }
        
        //Path to execute php command
        $file_exec_path = $sftp->exec(SEO_BLIST_EXEC);
        $file_exec_path = trim($file_exec_path); //Path to execute ruby command

        $cmd = "php delete.php $id 2>&1 "; // PHP command for data processing
        $sftp->setTimeout(300); // sftp timeout
        $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
        
        // Output by executing sftp command (using php command string)
        $output = trim($sftp->exec("cd $file_exec_path;$cmd ;"));
        
        if($output == 'deleted')
        {
            $cmd = "php list.php p 2>&1 "; // PHP command for data processing
            
            // Output by executing sftp command (using php command string)
            $output = $sftp->exec("cd $file_exec_path;$cmd ;");
            $output = str_replace(SEO_RVM_NOTATION, "", $output);
        }
        exit($output);
        
        // Response for success message (display with download and/or view results)
        $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG3, '', '');
        $response['data'] = $output;
        //echo '<pre>';print_r($response);exit;
        $response = array_map('utf8_decode', $response);
        print json_encode($response); // Resonse array encoded in json format
        exit;
    }
    
    public function pageRankFrequencyProcessAction()
    {
		// Get all request variable posted for page rank frequency process
        $pr_params=$this->_request->getParams();
        
        $frequencyObj = new Ep_Seo_Frequency() ;
        $schedules   =   $frequencyObj->getSchedules() ;

        foreach($schedules['e'] as $fid=>$schedules_)
        {
            $i = 0 ;
            foreach($schedules_ as $url)
            {
                $pagerank[$fid][$i]['url'] = $url->url ;
                $pagerank[$fid][$i]['pagerank'] = $this->get_google_pagerank($url->url) ;
                $pagerank[$fid][$i]['created_at'] = date('Y-m-d H:i:s');
                $i++ ;
            }
            $frequencies[] = $fid ;
        }

        $frequencyObj->updatePRstatus($pagerank) ;

        foreach($schedules['n'] as $newSchedule)
        {
			// Read csv file to an array
            $csv_data = $this->getCSV(SEO_UPLOAD_PR . $newSchedule->frequency_file) ; 
            
            $i = 1 ;
            foreach($csv_data as $url)
            {
                $data[$newSchedule->id][$i]['url'] = $csv_data[$i][0] ;
                $data[$newSchedule->id][$i]['pagerank'] = $this->get_google_pagerank($url[0]) ;
                $data[$newSchedule->id][$i]['created_at'] = date('Y-m-d H:i:s');
                $i++ ;
            }
            $frequencies[] = $newSchedule->id ;
        }
        $frequencies = array_unique($frequencies) ;
        $frequencyObj->updateNewPRstatus($data) ;
        
        //echo '<pre>#';print_r($pagerank);exit;

        foreach($frequencyObj->getSchedulesToMail($frequencies) as $schedule)
            $urls[$schedule->frequency_id][$schedule->url][date('Y-m-d', strtotime($schedule->created_at))]  =   $schedule->pagerank ;
        
        //echo '<pre>'; print_r($frequencies); print_r($urls);
        
        foreach($urls as $process_id=>$urls_)
        {
            $frequency   =   $frequencyObj->getFrequencyData($process_id) ;
            $frequencyDates   =   $frequencyObj->getFrequencyDates($process_id) ;
            $frequency_filename =   $process_id . '_' . $frequency[0]->user_id . '_' . date('Ymd', strtotime($frequency[0]->end_date)) ;
            $data[1][0] =   'Url';   $data[1][1] =   'Page Rank'; $i = 1 ;
            foreach($frequencyDates as $key=>$val)  $data[1][$key+1] =   $val;
            
            //print_r($frequencyDates);
            foreach($urls_ as $url_=>$urls_val)
            {
                $data[$i+1][0]   =   $url_;
                foreach($frequencyDates as $key1=>$val1)
                {
                    $data[$i+1][$key1+1]   =   $urls_val[$val1] ;
                }
                $i++ ;
            }
            
            //print_r($data);exit($frequency_filename);
            $this->writeCSV($data, SEO_DOWNLOAD_PR . $frequency_filename . '.csv'); // Write data in csv format
            $this->WriteXLS($data, SEO_DOWNLOAD_PR . $frequency_filename . '.xls'); // Output file in xls format
            
            $zip = new ZipArchive() ;
            $zip->open(SEO_DOWNLOAD_PR . $frequency_filename . '.zip',  ZIPARCHIVE::CREATE) ;
            $zip->addFile(SEO_DOWNLOAD_PR . $frequency_filename . '.csv', $frequency_filename . '.csv') ;
            $zip->addFile(SEO_DOWNLOAD_PR . $frequency_filename . '.xls', $frequency_filename . '.xls') ;
            $zip->close();
            
            //  Sending email
            $mail = new Zend_Mail();
            $mail->addHeader('Reply-To','support@edit-place.com');
            $mail->setBodyHtml('testing zend mail attachment..')
                ->setFrom('support@edit-place.com')
                ->addTo($frequency[0]->email)
                ->setSubject('PR checker results (' . $frequency[0]->title . ') on ' . date('m/d/Y'));

        //'anoopchandanathope@gmail.com'

            $at = new Zend_Mime_Part(file_get_contents(SEO_DOWNLOAD_PR . $frequency_filename . '.zip'));
            $at->type        =  'application/zip'; //Zend_Mime::MULTIPART_RELATED
            $at->filename = basename(SEO_DOWNLOAD_PR . $frequency_filename . '.zip');
            $at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
            $at->encoding = Zend_Mime::ENCODING_BASE64;
            $mail->addAttachment($at);
            $mail->send();
        }
        
        
        //echo '<pre>'; print_r($schedules); exit(strtolower(date('l')));
    }
    
    // Function to process meta info generator tool
    function keyGenUploadAndProcess($srcFile, $u_file_name) {
        try {
			/**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            $src = pathinfo($u_file_name);
            $download_fname = $src['filename'] . "_" . time();
            $dstfile = $download_fname . ".xls" ;

            $file_exec_path = $sftp->exec(SEO_KW_GENERATOR_EXEC); //Path to execute ruby command
            $file_upload_path = $sftp->exec(SEO_KW_GENERATOR_UPLOAD); // seo input file upload path
            $file_download_path = $sftp->exec(SEO_KW_GENERATOR_DOWNLOAD); // output file download path
            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            $encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');

            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId;  // Logged in userid
            $limit =   $this->limit ; // Result limit
            $site_id =   $this->site_id ; // site id
            $ruby_file = SEO_KW_GENERATOR_RB; // Ruby file for processing

			// Ruby command for data processing
            $cmd = "$ruby_file $loginName $userId $site_id $u_file_name $dstfile $limit \"$encoding\" 2>&1 ";

            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
            
            if($this->frequency_option==1)
            {
                return $this->responseMsg(1, 31); // Response message
            }
            
            $remoteFile = trim($file_download_path) . "/" . $dstfile;
            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_KW_GENERATOR . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];
            $sftp->get($dstfile, $localFile); //downloading result file from linode server(using sftp)

            if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
				
				// Read xls file to an array
				$xls_array = $this->readInXLS(SEO_DOWNLOAD_KW_GENERATOR . $fname . ".xls");
				$this->writeCSV($xls_array, SEO_DOWNLOAD_KW_GENERATOR . $fname . ".csv"); // Write data in csv format
                    
				// Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seoresult.php?ext=' . ($this->output_type==2 ? 'xls' : 'csv') . '&filename=' . $fname . '&tool=kwgenerator', SEO_DOWN_OP_FILE, 'keyword-generator?action=view&file=' . $fname . '&ext=csv', SEO_VIEW_RESULTS);
            } else {
                throw new Exception($output);
            }
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage().$cmd)); // Response message
        }
        return $response;
    }

    public function backlinksAction()
    {
		// Get all request variable posted for blacklinks tool
        $backlink_params=$this->_request->getParams();
        
        /* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($backlink_params['file']) && isset($backlink_params['ext'])) {            
            $filename = $backlink_params['file'] . "." . $backlink_params['ext'];
            $path_file = SEO_DOWNLOAD_BKL . $filename;

            if (file_exists($path_file)) {
                header('Content-Type: text/html; charset=utf-8');
                
                // Read csv file to an array to show the results in grid
                $table = $this->viewResultsGrid($this->getCSV($path_file), '') ;
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $backlink_params['file_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
            
        } else {
            if($backlink_params['submit'])
            {
				// Response message
                $response = $this->responseMsg('', 0, 1, '', '');

                $this->type = $backlink_params['file_type'];
                $this->option = $backlink_params['run_for'];
    
                require_once SEO_XLS_READER; // Spreadsheet excel reader file
                require_once SEO_SFTP_FILE; // php - sftp file
    
                if ($this->type == 2) {
                    $url_text = trim($backlink_params['url']);
                    if ($this->os == 'Windows')
                        $url_text = utf8_decode($url_text);
    
                    if ($url_text) {
                        $url_text1 = explode("\n", $url_text);
                        
                        $time = time();
                        $csv_file_name = $time . ".csv"; // Input csv file
                        $srcFile = SEO_UPLOAD_BKL . $csv_file_name;
                        
                        // creating csv file
                        $fp = fopen($srcFile, 'w');
                        fwrite($fp, $url_text);
                        fclose($fp);
                        
                        // Processing black list kw tool
                        $response = $this->bklUploadAndProcess($srcFile, $csv_file_name);
                        $response['file_type'] = $this->type;
                    } else {
                        $response = array('type' => 'error', 'message' => 'Please enter URL(s)', 'word_type' => $this->type);
                    }
                } else if ($this->type == 1) {
                    if (($_FILES['url_file']['type'] == 'text/comma-separated-values') || ($_FILES['url_file']['type'] == 'text/csv') || ($_FILES['url_file']['type'] == 'application/vnd.ms-excel') || ($_FILES['url_file']['type'] == 'application/x-msexcel') || ($_FILES['url_file']['type'] == 'application/xls')) {
                        $file_info = pathinfo($_FILES['url_file']['name']);
                        $extension = $file_info['extension'];
                        if ($extension == 'xls') {
							
							// Read xls file to an array
                            $xls_array = $this->readInXLS($_FILES['url_file']['tmp_name']);
                            $u_file_name = str_replace(" ", "_", $file_info['filename']) . ".csv"; // Input csv file
                            $srcFile = SEO_UPLOAD_BKL . $u_file_name;
                            $this->writeCSV($xls_array, $srcFile); // Write data in csv format
                        } else {
                            $srcFile = $_FILES['url_file']['tmp_name'];
                            $u_file_name = str_replace(" ", "_", $_FILES['url_file']['name']);
                        }
                        // Processing black list kw tool
                        $response = $this->bklUploadAndProcess($srcFile, $u_file_name);
                    } else {
                        $response = $this->responseMsg(0, 1); // Response message
                    }
                    $response['file_type'] = $this->type;
                }
                print json_encode($response); // Resonse array encoded in json format
                exit ;
            }
            // Processes a view script and returns the output.
            $this->render('backlinks');
        }
    }

	// Function for rocessing black list kw tool
    function bklUploadAndProcess($srcFile, $u_file_name) {
        try {
			/**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            $src = pathinfo($u_file_name);
            $download_fname = $src['filename'] . "_" . time();
            $dstfile = $download_fname . ".xls" ;

            $file_exec_path = $sftp->exec(SEO_BKL_EXEC); //Path to execute ruby command
            $file_upload_path = $sftp->exec(SEO_BKL_UPLOAD); // seo input file upload path
            $file_download_path = $sftp->exec(SEO_BKL_DOWNLOAD); // output file download path
            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            //$encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');

            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId;  // Logged in userid
            $option = $this->option;
            $ruby_file = SEO_BKL_RB; // Ruby file for processing

			// Ruby command for data processing
            $cmd = "$ruby_file $u_file_name $dstfile $option $userId $loginName 2>&1 ";

            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
            
            $remoteFile = trim($file_download_path) . "/" . $dstfile;
            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_BKL . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];
            $sftp->get($dstfile, $localFile); //downloading result file from linode server(using sftp)
            
            if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
				// Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seoresult.php?filename=' . $download_fname . '&ext=xls&tool=backlists', SEO_DOWN_OP_FILE);
            } else {
                throw new Exception($output);
            }
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
        }
        return $response;
    }

    public function boulangerProductUrlAction()
    {
		// Get all request variable posted for boulanger Product Url tool
        $bpu_params=$this->_request->getParams();
        
        if($bpu_params['submit'])
        {
            $response = $this->responseMsg('', 0, 1, '', ''); // Response message
            require_once SEO_XLS_READER; // Spreadsheet excel reader file
            require_once SEO_SFTP_FILE; // php - sftp file
            $ref_text = trim($bpu_params['reference']);
            if ($this->os == 'Windows')
                $ref_text = utf8_decode($ref_text);

            if ($ref_text) {
                $ref_text1 = explode("\n", $ref_text);
                $ref_text1 = array_map("trim", $ref_text1);
                //echo '<pre>';print_r($ref_text1);exit('#'.implode(",", $ref_text1).'#');
                $time = time();
                $csv_file_name = $time . ".txt";
                $srcFile = SEO_UPLOAD_BPU . $csv_file_name;
                
                // creating txt file
                $fp = fopen($srcFile, 'w');
                fwrite($fp, implode(",", $ref_text1));
                fclose($fp);
                
                // Processing boulanger product url tool
                $response = $this->bpuUploadAndProcess($srcFile, $csv_file_name);
            } else {
                $response = array('type' => 'error', 'message' => 'Please enter References(s)');
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
        // Processes a view script and returns the output.
        $this->render('boulanger1');
    }

	// Function for processing boulanger product url tool
    function bpuUploadAndProcess($srcFile, $u_file_name) {
        try {
			
			/**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            $src = pathinfo($u_file_name);
            $download_fname = $src['filename'] . "_" . time();
            $dstfile = $download_fname . ".xls" ;

            $file_exec_path = $sftp->exec(SEO_BPU_EXEC); //Path to execute ruby command
            $file_upload_path = $sftp->exec(SEO_BPU_UPLOAD); // seo input file upload path
            $file_download_path = $sftp->exec(SEO_BPU_DOWNLOAD); // output file download path
            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            //$encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');

            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId;  // Logged in userid
            $ruby_file = SEO_BPU_RB; // Ruby file for processing

			// Ruby command for data processing
            $cmd = "$ruby_file $u_file_name $dstfile $userId $loginName 2>&1 ";

            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
            
            $remoteFile = trim($file_download_path) . "/" . $dstfile;
            $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
            $file_path = pathinfo($remoteFile);
            $localFile = SEO_DOWNLOAD_BPU . $file_path['basename'];
            $serverfile = $file_path;
            $fname = $file_path['filename'];
            $ext = $file_path['extension'];
            $sftp->get($dstfile, $localFile); //downloading result file from linode server(using sftp)
            
            if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION) {
				// Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seoresult.php?filename=' . $download_fname . '&ext=xls&tool=boulangerproducturl', SEO_DOWN_OP_FILE);
            } else {
                throw new Exception($output);
            }
        } catch(Exception $e) {
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage())); // Response message
        }
        return $response;
    }

    public function subjectToolAction()
    {
		// Get all request variable posted for subject tool
        $subject_tool_params=$this->_request->getParams();
        
        /* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($blacklists_params['file']) && isset($blacklists_params['ext'])) {
			
			// Processes a view script and returns the output.
            $this->render("seotool_view");
            
        } else {
            if(isset($subject_tool_params['submit']))
            {
                require_once SEO_SFTP_FILE; // php - sftp file
                foreach ($subject_tool_params['site'] as $key => $value) {
                    $ur = str_replace('http://', '', str_replace('https://', '', str_replace('www.', '', $subject_tool_params['site'][$key])));
                    $op = substr($ur, 0, strpos($ur, '.')) . "_" . time() . ".xlsx";
                    $arr[] = array($subject_tool_params['site'][$key], $subject_tool_params['limit'][$key], $subject_tool_params['version'], $subject_tool_params['email'], ($subject_tool_params['proxy'] ? '1' : '0'), $op);
                    unset($ur);unset($op);
                }
                $csv_file = "results_" . time();
                $csvfilename = $csv_file . ".csv" ; // Input csv file
                $srcFile = SEO_UPLOAD_SUBJECT_TOOL . $csvfilename ;
                $this->writeCSV($arr, $srcFile); // Write data in csv format
                
                // Processing subject tool
                $response = $this->subjectToolProcess($srcFile, $csvfilename);
                print json_encode($response); // Resonse array encoded in json format
                exit ;
            }
            // Processes a view script and returns the output.
            $this->render('subject_tool');
        }
    }
    
    // Function for processing subject tool
    function subjectToolProcess($srcFile, $u_file_name) {
        try {
			/**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }

            $file_exec_path = $sftp->exec(SEO_SUBJECT_TOOL_EXEC); //Path to execute ruby command
            $file_upload_path = $sftp->exec(SEO_SUBJECT_TOOL_UPLOAD); // seo input file upload path
            $file_download_path = $sftp->exec(SEO_SUBJECT_TOOL_DOWNLOAD); // output file download path
            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($u_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            //$encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');

            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId;  // Logged in userid
            $ruby_file = SEO_SUBJECT_TOOL_RB; // Ruby file for processing

            // Ruby command for data processing
            $cmd = "$ruby_file $userId $loginName $u_file_name 2>&1 ";
            
            //ruby import_subject_tool_data.rb userid Username input.csv
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using ruby command string)
            $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
            
            if($output){
                // Response for success message (display with download and/or view results)
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG3, '', '');
            } else {
                throw new Exception($output);
            }
        } catch(Exception $e) {
			// Response message
            $response = $this->responseMsg(0, 0, 0, ($e->getMessage().$cmd));
        }
        return $response;
    }

    public function subjectProposalsAction()
    {
        ini_set('max_execution_time', 1000);
        
        // Get all request variable posted for subject proposal tool
        $subject_proposal_params=$this->_request->getParams();
        
        if($subject_proposal_params['load'] || $subject_proposal_params['download'])
        {
            require_once SEO_SFTP_FILE; // php - sftp file
            
            /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }
    
            //Path to execute php command
            $file_exec_path = $sftp->exec(SEO_SUBJECT_TOOL_EXEC);
            $file_download_path = $sftp->exec(SEO_SUBJECT_TOOL_DOWNLOAD); // output file download path
        }
        
        if($subject_proposal_params['load'])
        {
			// PHP command for data processing
            $cmd = "php proposals.php 2>&1 ";
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using php command string)
            $output = $sftp->exec("cd $file_exec_path;$cmd ;");//exit($output);
            $outputs = unserialize($output);
            //echo '<pre>';print_r($outputs);exit();
            $htm = '';
            $idx = 1;
            foreach($outputs as $pr)
            {
                $date = (($pr['created_at'] != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($pr['created_at'])) : 'NA');
                
                // Subject tool data list
                $htm .= '<tr><td>'.$idx.'</td><td>'.$pr['site'].'</td><td>'.$pr['url_limit'].'</td><td>'.$pr['email'].'</td><td>'.$pr['version'].'</td><td>'.$date.'</td><td>'.($pr['processed'] ? ('<a onclick="pdownload('.$pr['id'].');" href="javascript:void(0);" class="label label-success" id="dwn'.$pr['id'].'">Download</a><div id="del'.$pr['id'].'" class="ploader"></div>') : ('<label class="label label-warning">Pending</label>')).'</td></tr>';
                $idx++;
            }
            exit($htm);
        }
        elseif($subject_proposal_params['download'])
        {
            $id = $subject_proposal_params['id'];
            $cmd = "php proposals.php $id 2>&1 "; // PHP command for data processing
            $file_exec_path = trim($file_exec_path); //Path to execute ruby command
            $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
            
            // Output by executing sftp command (using php command string)
            $output = $sftp->exec("cd $file_exec_path;$cmd ;");
            //exit($output);
            if(!empty($output))
            {
                $remoteFile = trim($file_download_path) . "/" . $output;
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                $file_path = pathinfo($remoteFile);
                $localFile = SEO_DOWNLOAD_SUBJECT_TOOL . $file_path['basename'];
                if(!file_exists($localFile))
                    $sftp->get($remoteFile, $localFile); //downloading result file from linode server(using sftp)
            }
            exit($output);
        }        
        $this->_view->proposals = $outputs;
        
        // Processes a view script and returns the output.
        $this->render('subject_proposals');
    }

    public function inurlAction()
    {
        // Get all request variable posted for inurl tool
        $inurl_params=$this->_request->getParams();
        
        if($inurl_params['submit'])
        {
			// Response message
            $response = $this->responseMsg('', 0, 1, '', '');
            $search_engine=$inurl_params['search_engine'];
            $limit=$inurl_params['limit'];
            $options=implode('|', $inurl_params['option']);
            $encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8'); // Changing encoding based on os
            
            $urls = trim($inurl_params['urls']);
            $urls = explode("\n", $urls);
            $urls = array_filter($urls);
            foreach ($urls as $url) {
                $urltext .=  trim($url) . ',';
            }
            // Urls text seperated by comma
            $urltext = substr($urltext, 0, (strlen($urltext)-1));

			// Inurl kw texts
            $kws = explode("\n", $inurl_params['keyword']);
            $kws = array_filter($kws);
            foreach ($kws as $kw) {
                $kwtext .=  trim($kw) . ',';
            }
            $kwtext = substr($kwtext, 0, (strlen($kwtext)-1));            
            $kwtext = html_entity_decode(iconv("ISO-8859-1", "UTF-8", $kwtext), ENT_QUOTES, "UTF-8") ;

            $time = time();
            $csv_file_name = $time . ".csv"; // Input csv file
            $xlsx_file_name = $time . ".xlsx";
            $srcFile = SEO_UPLOAD_INURL . $csv_file_name;
            
            // Craeting csv file with urls and keywords
            $fp = fopen($srcFile, 'w');
            fwrite($fp, $urltext.';'.($kwtext));
            fclose($fp);
                       
            require_once SEO_SFTP_FILE; // php - sftp file
            
            /**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server);
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                throw new Exception('Login Failed');
            }
            
            //Path to execute php command
            $file_exec_path = $sftp->exec(SEO_INURL_EXEC);
            $file_upload_path = $sftp->exec(SEO_INURL_UPLOAD); // seo input file upload path
            $file_download_path = $sftp->exec(SEO_INURL_DOWNLOAD); // output file download path
            
            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            $sftp->put($csv_file_name, $srcFile, NET_SFTP_LOCAL_FILE); // Uploading to linode server via sftp
            //$encoding = (($this->os=='Windows') ? 'WINDOWS-1252' : 'UTF-8');

            $loginName = $this->adminLogin->loginName; // Logged in username
            $userId = $this->adminLogin->userId;  // Logged in userid

            if($inurl_params['result_type']==2 && !empty($inurl_params['email']))
            {
                $cmail = $inurl_params['email']; // cron email
                $ruby_file = SEO_INURL_CRON_RB; // Ruby file for processing
                
                // Ruby command for data processing
                $cmd = "$ruby_file $userId $loginName $search_engine $csv_file_name $xlsx_file_name 'inurl' $limit $cmail $encoding 2>&1 ";//exit($cmd);

                $file_exec_path = trim($file_exec_path); //Path to execute ruby command
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                
                if (trim($output) == SEO_RVM_NOTATION)
                {
					// Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg($cmd.SEO_SUCCESS_MSG5.$output,"","");
                } else {
					// Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg('SEO_SUCCESS_MSG-'.$output);
                }
            }
            elseif($inurl_params['result_type']==1) {
                
                $ruby_file = SEO_INURL_RB; // Ruby file for processing

				// Ruby command for data processing
                $cmd = "$ruby_file $userId $loginName $search_engine $csv_file_name $xlsx_file_name '$options' $limit $encoding 2>&1 ";
                
                $file_exec_path = trim($file_exec_path); //Path to execute ruby command
                $ruby_switch_prefix = SEO_RB_SWITCH_PREFIX; // Ruby switch prefix (used with ruby command)
                
                // Output by executing sftp command (using ruby command string)
                $output = $sftp->exec("$ruby_switch_prefix ;cd $file_exec_path;$cmd ;");
                
                $localFile = SEO_DOWNLOAD_INURL . $xlsx_file_name ;
                $sftp->chdir(trim($file_download_path)); // Enter download directory in remote server
                $sftp->get($xlsx_file_name, $localFile); //downloading result file from linode server(using sftp)
                
                if (file_exists($localFile) && trim($output) == SEO_RVM_NOTATION)
                {
                    // Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1."-".$cmd, BO_PATH . "/download_seoresult.php?filename=" . $time . "&ext=xlsx&tool=inurl", SEO_DOWN_OP_FILE);
                } else {
                    throw new Exception($output);
                }
            }
            print json_encode($response); // Resonse array encoded in json format
            exit ;
        }
        
        // Processes a view script and returns the output.
        $this->render('inurl');
    }

    public function subjectProposals1Action()
    {
        ini_set('max_execution_time', 1000);
        $subject_proposal_params=$this->_request->getParams();
        
        // Processes a view script and returns the output.
        $this->render('subject_proposals');
    }
    
    // Response messages
    function responseMsg($type, $code, $word_type=0, $msg='', $data=0) {
        
        $response['type'] = (!empty($type) ? ($type ? 'success' : 'error') : '');
        if($word_type)
            $response['word_type'] = $word_type; // word type(text or file)
        if($data)
            $response['data'] = $data;
        switch($code)
        {
            case 1 :
                $response['message'] = 'Please upload csv or xls files.';
                break;
            case 2 :
                $response['message'] = 'Please select an option.';
                break;
            case 3 :
                $response['message'] = 'Please enter URL & keywords in box (CSV Format)';
                break;
            case 4 :
                $response['message'] = 'File read error.re-upload the file!!!';
                break;
            case 5 :
                $response['message'] = 'Please upload file having any one of these format(doc,docx,xls,xlsx,txt).';
                break;
            case 6 :
                $response['message'] = "File has been added for frequency position tracking.";
                break;
            case 7 :
                $response['message'] = "Command executed successfully.";
                break;
            case 8 :
                $response['message'] = "Validate script saved successfully.";
                break;
            case 9 :
                $response['message'] = 'Please enter url.';
                break;
            case 10 :
                $response['message'] = 'Please select crawl option.';
                break;
            case 11 :
                $response['message'] = 'Please select content type.';
                break;
            case 12 :
                $response['message'] = "Search url updated successfully.";
                break;
            case 13 :
                $response['message'] = 'Client page validated successfully.';
                break;
            case 14 :
                $response['message'] = 'Validation failed.';
                break;
            case 15 :
                $response['message'] = 'Please enter URL and tag.';
                break;
            case 16 :
                $response['message'] = 'Please enter keywords.';
                break;
            case 17 :
                $response['message'] = "File has been scheduled for position tracking.";
                break;
            case 18 :
                $response['message'] = 'Please enter a minimum of 2 and maximum of 4 urls.';
                break;
            case 19 :
                $response['message'] = 'Please enter url and select option(s).';
                break;
            case 20 :
                $response['message'] = 'Please select site(s).';
                break;
            case 21 :
                $response['message'] = 'Please enter schedule date and email';
                break;
            case 22 :
                $response['message'] = 'Client name and title are required.';
                break;
            case 23 :
                $response['message'] = 'Please enter URL(s)';
                break;
            case 24 :
                $response['message'] = 'Please enter URL & Competitor URLs';
                break;
            case 25 :
                $response['message'] = 'Please enter URL';
                break;
            case 26 :
                $response['message'] = 'Please enter Text in box';
                break;
            case 27 :
                $response['message'] = 'File read error.re-upload the file!!!';
                break;
            case 28 :
                $response['message'] = 'Please upload file having any one of these format(doc,docx,xls,xlsx,txt).';
                break;
            case 29 :
                $response['message'] = 'Please select site.';
                break;
            case 30 :
                $response['message'] = "Please select keyword csv/xls";
                break;
            case 31 :
                $response['message'] = "Frequency scheduled.";
                break;
            default :
                $response['message'] = $msg;
                break;
        }
        return $response ;
    }
	// Response for success message (display with download and/or view results)
    function displaySuccessMsg($msg, $downUrl, $downLabel, $viewUrl='', $viewLabel='', $data='') {
        $response['type'] = 'success';
        if($data)
            $response['data'] = $data;
        $response['message'] = "";
        if($msg)
            $response['message'] = $msg . "<br>";
        if($downUrl)
            $response['message'] .= "<a href=\"" . $downUrl . "\">" . $downLabel . "</a>";
        if($viewUrl)
            $response['message'] .= " / <a href=\"javascript:void(0);\" onclick=\"window.open('" . BO_DOMAIN_ . "seotool/".$viewUrl."', '_blank');\">" . $viewLabel . '</a>';
        return $response ;
    }

    public function testGoogleNewsAction() {
		
        $gnews_params = $this->_request->getParams(); // Google news data posted

		/* View results */
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view' && isset($gnews_params['file']) && isset($gnews_params['ext'])) {
            $filename = $gnews_params['file'] . "." . $gnews_params['ext'];
            $path_file = SEO_DOWNLOAD_GNEWS . $filename;

            if (file_exists($path_file)) {
                header('Content-Type: text/html; charset=utf-8');
                $data = $this->getCSV($path_file); // Read csv file to an array
            }
            $this->_view->table = $table; // View results with template stored into smarty variable
            $this->_view->word_type = $gnews_params['word_type']; // word type(text or file)
            
            // Processes a view script and returns the output.
            $this->render("seotool_view");
        }
    }

	// Grid view of data for each tool
    function viewResultsGrid($data, $tool) {
        if($_REQUEST['debug']){echo '<pre>';echo(sizeof(max($data)));}
        $table = SEO_TBL_TG. '<thead>';
        $cols = sizeof(max($data)) ;

        foreach ($data as $key=>$row) {
            if(sizeof($row)<$cols){
                $data[$key] = array_merge($data[$key], array_fill((sizeof($row)-1), ($cols-(sizeof($row))), ''));
            }
        }
        
        for($idx=0; $idx < $cols; $idx++)
        {
            $td=$data[1][$idx];
            if($tool == 'gnews') {
                if (!mb_check_encoding($td, 'UTF-8'))
                    $td = iconv("ISO-8859-1", "UTF-8", $td);
                $td = (($j == 1) ? '<a href="' . $row[1] . '" target="_blank">' . $td . '</a>' : $td);
            } elseif($tool == 'spl') {
                if ($this->os != 'Windows') {
                    $td = utf8_decode($td);
                }
            } elseif($tool == 'scompare') {
                if ($this->os != 'Windows')
                    $td = htmlentities(utf8_decode($td));
                else
                    $td = htmlentities(($td));
            }
            $table .= '<th>' . $td . '</th>' ;
        }
        $table .= '</thead><tbody>' ;
        
        if($_REQUEST['debug']){ print_r($data); print_r($data[0]);echo(sizeof(max($data)));exit;}
        $i = 0;
        foreach ($data as $row) {
            $j = 1;
            foreach ($row as $td) {
                if($i>0)
                {
                    if($tool == 'gnews') {
                        if (!mb_check_encoding($td, 'UTF-8'))
                            $td = iconv("ISO-8859-1", "UTF-8", $td);
                        $td = (($j == 1) ? '<a href="' . $row[1] . '" target="_blank">' . $td . '</a>' : $td);
                    } elseif($tool == 'spl') {
                        if ($this->os != 'Windows') {
                            $td = utf8_decode($td);
                        }
                    } elseif($tool == 'scompare') {
                        if ($this->os != 'Windows')
                            $td = htmlentities(utf8_decode($td));
                        else
                            $td = htmlentities(($td));
                    }
                    $table .= (($j == 1) ? '<tr>' : '') . '<td>' . $td . '</td>' . (($j == $cols) ? '</tr>' : '');
                    //$table .= (($j == 1) ? '<tr>' : '') . '<td>' . (strstr($td, ')') ? str_replace('")', '', substr($td, (strpos($td, '","') + 3))) : $td) . '</td>' . (($j == $cols) ? '</tr>' : '');
                }
                $j++;
            }
            $i++;
        }
        
        $table .= '</tbody>' . SEO_TBL_TG_;//exit($table);
        
        return $table ;
    }
	
	// Function to get google page rank info
    public function get_google_pagerank($url) {
		
		// Api url to get google page rank info
        $query="http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=".$this->CheckHash($this->HashURL($url)). "&features=Rank&q=info:".$url."&num=100&filter=0";
        @$data=file_get_contents($query);
        $pos = strpos($data, "Rank_");
        if($pos === false){} else{
            $pagerank = substr($data, $pos + 9);
            return $pagerank;
        }
    }
    
    public function StrToNum($Str, $Check, $Magic)
    {
        $Int32Unit = 4294967296; // 2^32
        $length = strlen($Str);
        for ($i = 0; $i < $length; $i++) {
            $Check *= $Magic;
            if ($Check >= $Int32Unit) {
                $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
                $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
            }
            $Check += ord($Str{$i});
        }
        return $Check;
    }
    
    public function HashURL($String)
    {
        $Check1 = $this->StrToNum($String, 0x1505, 0x21);
        $Check2 = $this->StrToNum($String, 0, 0x1003F);
        $Check1 >>= 2;
        $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
        $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
        $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);
        $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
        $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );
        return ($T1 | $T2);
    }
    
    public function CheckHash($Hashnum)
    {
        $CheckByte = 0;
        $Flag = 0;
        $HashStr = sprintf('%u', $Hashnum) ;
        $length = strlen($HashStr);
        for ($i = $length - 1; $i >= 0; $i --) {
            $Re = $HashStr{$i};
            if (1 === ($Flag % 2)) {
                $Re += $Re;
                $Re = (int)($Re / 10) + ($Re % 10);
            }
            $CheckByte += $Re;
            $Flag ++;
        }
        $CheckByte %= 10;
        if (0 !== $CheckByte) {
            $CheckByte = 10 - $CheckByte;
            if (1 === ($Flag % 2) ) {
                if (1 === ($CheckByte % 2)) {
                    $CheckByte += 9;
                }
                $CheckByte >>= 1;
            }
        }
        return '7'.$CheckByte.$HashStr;
    }
    
    /**
     * Function multi Conditional Blacklist Keyword Action
     * @package EditplaceBO
     * @author Vinayak K
     * @return nill
     * 
     * Controller to check multiple conditional words in given xlsx file 
     *
     * */
     
    public function multiconditionalBlacklistKeywordAction(){
            $kw_params = $this->_request->getParams();
            
            if(isset($_REQUEST['action']) && $_REQUEST['action']=='view' && isset($kw_params['file']) && isset($kw_params['ext']))
            {
                error_reporting(0);
               
                $this->_view->table =  $table ;
                $this->_view->word_type =  $kw_params['word_type'] ;
                
                // Processes a view script and returns the output.
                $this->render("seotool_view");
            }
            else
            {

				if($_POST['submit'] && $_POST['submit']=='load_kw'){
                  
                    $kwords=$_POST['kw1'];
                    $data['keywords_orig']=$kwords;
                    $data['keywords']=explode("\r\n",$kwords);
                    $data['kws']=array();
                    $data['client']=$_POST['client_name'];
                   // echo "<pre>";print_r($data);exit;
				   //$temp_obj=new Ep_SEO_Mkwbl();
                  // $data['templates']=$temp_obj->loadTemplates('*','');
                  // echo "<pre>"; print_r($data);exit;
                    $this->_view->data=$data;
                
                }else if($_POST['templateselect']!=''){
					$client=$_POST['client_name'];
					$template=$_POST['templateselect'];
					$temp_obj=new Ep_SEO_Mkwbl();
					$template_data=$temp_obj->getTemplate($template,$client);
					$kws=json_decode($template_data[0]->template_data);
					$data['kws']=$kws;//$this->objectToArray($kws);//objecttoarray($kws);
					$data['client']=$client;
					$kwords=$_POST['kw1'];
					$data['keywords_orig']=$kwords;
					
					$this->_view->data=$data;
					//echo "<pre>"; print_r($data);
					//echo "POSTED TEMPLATE";exit;

				}
                else if($_REQUEST['msg']=='success' && isset($_REQUEST['file']))
                {
                    $this->_view->msg = "File Successfully uploaded and processed.<br>" ;
                    $this->_view->msg.='<a href="/' . BO_PATH_ . 'download_seo.php?saction=download&file='.$_REQUEST['file'].'&ext=xlsx">Cick here to download</a>' ;
                    if($_GET['ext'] == 'xls')
                        $this->_view->msg.=' / <a target="_result" href="/seotool/cond-keyword-xls-upload?action=view&file='.$_REQUEST['file'].'&ext=xls">View result</a>' ;
                    $this->_view->class = 'success' ;
                }else if($_REQUEST['msg']=='success' && isset($_REQUEST['template'])){
						 $this->_view->msg = "Template Successfully Saved.<br>" ;
						 $this->_view->class = 'success' ;
				}
                else
                {
                    $this->_view->class = '' ;
                    $this->_view->msg = '' ;
                }
                
                // Processes a view script and returns the output.
                $this->render('multiconditional_blacklist_keyword');
            }   
    }

  
    public function savetemplateAction(){
		$this->render('save_mblk_template');
	}

	function objectToArray ($object) {
    if(!is_object($object) && !is_array($object))
        return $object;

    return array_map('objectToArray', (array) $object);
	}
    
    public function condKeywordXlsUploadAction()
    {   
       
        $kw_params = $this->_request->getParams();

        if(isset($kw_params['submit']))
        {

			if($kw_params['submit']=='save_template'){
				 $title=$_POST['template_name'];
				 $client=$_POST['client_name'];
				 $this->mkblSaveTemplate($kw_params,$title,$client);
				 $this->_redirect('seotoolrp/multiconditional-blacklist-keyword?msg=success&template=saved');
					
				
			}else if($kw_params['submit']=='check_save'){
				 $title=$_POST['template_name'];
				 $client=$_POST['client_name'];
				 $this->mkblSaveTemplate($kw_params,$title,$client);
				 $this->readfilemkbl();//exit;
				 $newArr=$this->mkwbl_process($xlsArr,$data);

				 $xlsArr=$this->readfilemkbl();
					$data=$this->mkblRestructureData($kw_params);
					$newArr=$this->mkwbl_process($xlsArr,$data);
					//echo "<pre>"; print_r($newArr);
					//exit;
					//echo "<pre>";print_r($error);exit;
					
					/** PHPExcel_Writer_Excel2007 */
					include_once ROOT_PATH . BO_PATH_ . 'nlibrary/script/PHPExcel/Writer/Excel2007.php';
					
					// Create new PHPExcel object
					$objPHPExcel = new PHPExcel();
					
					// Set properties
					$objPHPExcel->getProperties()->setCreator("Anoop");
					
					// Add some data
					$objPHPExcel->setActiveSheetIndex(0);
					
					$rowCount=0;
					foreach ($newArr as $row)
					{
						$col = 'A';
						$colIdx = 0;
						$row_ref=$row;
						//print_r($row_ref);
						//echo $row_ref['cond_ful'];
						foreach ($row as $key => $value)
						{                
							if ($this->getmyOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
							{             
							
							$value = iconv("ISO-8859-1", "UTF-8", $value) ;
							$value = str_replace("", htmlentities("œ"), $value) ;
							$value = str_replace("", "'", $value) ;
							$value = str_replace("", "'", $value) ;
							$value = html_entity_decode(htmlentities($value,  ENT_QUOTES, 'UTF-8'), ENT_QUOTES ,mb_detect_encoding($value));
							$value=html_entity_decode($value);
							
							}
							$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
							
							if($row_ref['cond_ful']=='error'){
							//echo "HERE";
								$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount+1))->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => 'EDEA18')));
							}
							
							$col++;
							$colIdx++;
						}
						$rowCount++;
					}
					// Rename sheet
					$objPHPExcel->getActiveSheet()->setTitle('Sheet1');
					$objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
					
					$filename="results_".time() ;
					$file_path = SEO_DOWNLOAD_SCRAPER.$filename.".xlsx" ;
					//echo $file_path;
					
					// Save Excel 2007 file
					$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
					$objWriter->save($file_path);
					chmod($file_path,0777) ;
					$response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seo.php?saction=download&ext=xlsx&file=' . $filename, SEO_DOWN_OP_FILE, '', '', '');
					//print_r($response);
					$this->_redirect('seotoolrp/multiconditional-blacklist-keyword?file='.$filename.'&msg=success');
			}else{
				//$response = $this->responseMsg('', 0, 0, '', '');
					$xlsArr=$this->readfilemkbl();
					$data=$this->mkblRestructureData($kw_params);
					$newArr=$this->mkwbl_process($xlsArr,$data);
					//echo "<pre>"; print_r($newArr);
					//exit;
					//echo "<pre>";print_r($error);exit;
					
					/** PHPExcel_Writer_Excel2007 */
					include_once ROOT_PATH . BO_PATH_ . 'nlibrary/script/PHPExcel/Writer/Excel2007.php';
					
					// Create new PHPExcel object
					$objPHPExcel = new PHPExcel();
					
					// Set properties
					$objPHPExcel->getProperties()->setCreator("Anoop");
					
					// Add some data
					$objPHPExcel->setActiveSheetIndex(0);
					
					$rowCount=0;
					foreach ($newArr as $row)
					{
						$col = 'A';
						$colIdx = 0;
						$row_ref=$row;
						//print_r($row_ref);
						//echo $row_ref['cond_ful'];
						foreach ($row as $key => $value)
						{
							if ($this->getmyOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
							{             
							
							$value = iconv("ISO-8859-1", "UTF-8", $value) ;
							$value = str_replace("", htmlentities("œ"), $value) ;
							$value = str_replace("", "'", $value) ;
							$value = str_replace("", "'", $value) ;
							$value = html_entity_decode(htmlentities($value,  ENT_QUOTES, 'UTF-8'), ENT_QUOTES ,mb_detect_encoding($value));
							$value=html_entity_decode($value);
							
							}
								$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
							
							if($row_ref['cond_ful']=='error'){
							//echo "HERE";
								$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount+1))->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => 'EDEA18')));
							}
							
							$col++;
							$colIdx++;
						}
						$rowCount++;
					}
					// Rename sheet
					$objPHPExcel->getActiveSheet()->setTitle('Sheet1');
					$objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
					
					$filename="results_".time() ;
					$file_path = SEO_DOWNLOAD_SCRAPER.$filename.".xlsx" ;
					//echo $file_path;
					
					// Save Excel 2007 file
					$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
					$objWriter->save($file_path);
					chmod($file_path,0777) ;
					$response = $this->displaySuccessMsg(SEO_SUCCESS_MSG1, BO_PATH . '/download_seo.php?saction=download&ext=xlsx&file=' . $filename, SEO_DOWN_OP_FILE, '', '', '');
					//print_r($response);
					$this->_redirect('seotoolrp/multiconditional-blacklist-keyword?file='.$filename.'&msg=success');
					//print json_encode($response);
					//exit ;
					//echo "<pre>";print_r($kw_params);print_r($error);print_r($xls_array);exit($file_path);
				}
			}
		
    }

    /**
     * mkblSaveTemplate is function to save the template generated from Form for later use
     *
     * The Template has been saved in Database 
     * @package Edit-placeBO
     * @author Vinayak Kadolkar
     * @email vinayak@edit-place.com
     * @param array $rawdata post data came from the form
     * @return boolean
     *
     * */
	 function mkblSaveTemplate($rawdata,$title,$client){
		//echo "<pre>"; print_r($rawdata);exit;
		$data=$this->mkblRestructureData($rawdata);
		$jdata=json_encode($data);
		$temp_obj=new Ep_SEO_Mkwbl();
		$insertData['title']=$title;
		$insertData['template']=$jdata;
		$insertData['created_by']= $this->adminLogin->userId;
		$insertData['client']=$client;
		//echo "<pre>"; print_r($insertData);exit;
		$temp_obj->insertTemplate($insertData);
		//echo "<pre>"; print_r($jdata);exit;
	 }

	 /**
     * mkblRestructureData is function to restructure raw data & Organise in proper way
     *
     * The structured data has been returned back
     * @package Edit-placeBO
     * @author Vinayak Kadolkar
     * @email vinayak@edit-place.com
     * @param array $rawdata post data came from the form
     * @return array $data restructured data 
     *
     * */
	 function mkblRestructureData($rawdata){
		$data=array();
		$index=1;
		foreach($rawdata['main_kw'] as $key => $value ){
			$newdata=array();
			$newdata['main_kw']=$value;
			$newdata['simpletype']=(isset($rawdata['simpletype'.$index])) ? $rawdata['simpletype'.$index] : '';
			$newdata['main_kw_ref']=$rawdata['main_kw_ref'][$index-1];
			$j=1;
			foreach($rawdata['condn'.$index] as $conkey => $conval){
				$tempdata=array();
				$tempdata['condition']=(isset($rawdata['conditions'.$index.$j]))? $rawdata['conditions'.$index.$j] :'';
				$tempdata['cond_kw']=$rawdata['cond_kw'.$index][$j-1];
				$tempdata['condn']=$rawdata['condn'.$index][$j-1];
				$tempdata['main_cond_ref']=$rawdata['main_cond_ref'.$index][$j-1];
				$newdata['conditions'][]=$tempdata;
				$j++;
			}
			$index++;
			$data[]=$newdata;
		}
		//echo "<pre>"; print_r($data);exit;
		return $data;
	  }

	/**
     * checkmkbl is function to check keywords based on conditions for single row 
     * if any matches occur error note in differnet array
     * and return it
     * @package Edit-placeBO
     * @author Vinayak Kadolkar
     * @email vinayak@edit-place.com
     * @param array $data structured data of conditions
     * @param array $row one row of xlsx sheet for processing
     * @return array $error result of matching  
     *
     * */
	  function checkmkbl($data,$row){
			$error=array();
			foreach($data as $key => $value){
				$main_ref=$value['main_kw_ref'];
				$keepCheck=true;
				//Check if Main kw is present in string & search is simple
				if($value['simpletype']==1 && strpos($row[$main_ref],$value['main_kw']) !== false){
					$keepCheck=true;
				}else if($value['simpletype']=='' && $row[$main_ref]==$value['main_kw']) {
					$keepCheck=true;
				}else{
					$keepCheck=false;
				}

				if($keepCheck){
					foreach($data['conditions'] as $conkey => $conval){
						$innerCheck=true;
						$cond_ref=$conval['main_cond_ref'];
						if($conval['condn']==1){
							if($conval['condition']==1 && strpos($row[$cond_ref],$conval['cond_kw']) !== false){
								$innerCheck=true;
							}else if($conval['condition']=='' && $conval['cond_kw']==$row[$cond_ref]){
								$innerCheck=true;
							}else{
								$innerCheck=false;
							}
						}else{
							if($conval['condition']==1 && strpos($row[$cond_ref],$conval['cond_kw']) !== true){
								$innerCheck=true;
							}else if($conval['condition']=='' && $conval['cond_kw']!=$row[$cond_ref]){
								$innerCheck=true;
							}else{
								$innerCheck=false;
							}
						}

						if(!$innerCheck){
							$error[]=$row[$cond_ref];
						}
					}
				}
				
			}
			return $error;
	  }

	  function mkwbl_process($xlsdata,$mkbldata){
		  $newMatch=array();
		 // echo "<pre>"; print_r($xlsdata);
		// echo "<pre>"; print_r($mkbldata);exit;
			if(!empty($xlsdata) && !empty($mkbldata)){
				
				foreach($xlsdata as $key => $val){
					$val['keywords']='';
					$val['cond_ful']='';
					foreach($mkbldata as $k => $list){
						$found1=false;
						if($list['simpletype']==1){
							//echo "<br />".$val[$list['main_kw_ref']-1]."=".$list['main_kw'];
							if(preg_match("/\b".strtolower($list['main_kw'])."\b/i", strtolower($val[$list['main_kw_ref']-1]))){
								$found1=true;	
							}
						}else{
							//echo "<br />".$val[$list['main_kw_ref']-1]."=".$list['main_kw'];
							if (stripos(strtolower($val[$list['main_kw_ref']-1]), strtolower($list['main_kw'])) !== false) {
								$found1=true;
							}
						}
					if($found1){echo "TRUE";}
						if($found1){
							$index=0;
							//echo "<br />".$val[$condition['main_cond_ref']-1]."=".$condition['cond_kw'];
							foreach($list['conditions'] as $ck => $condition){
									$found2=false;
									
									if($condition['condition']==1){
										if(preg_match("/\b".strtolower($condition['cond_kw'])."\b/i", strtolower($val[$condition['main_cond_ref']-1]))){
											$found2=true;	
										}
									}else{
										if (stripos(strtolower($val[$condition['main_cond_ref']-1]),strtolower($condition['cond_kw'])) !== false) {
											$found2=true;
										}
									}
									if($found2){ echo "FOUND2";}
									if($found2){
										if($condition['condn']!=1){
											$val['cond_ful']="error";
											$val['keywords'].=$condition['cond_kw']." at col".$condition['main_cond_ref']." , ";
										}
									}else{
										if($condition['condn']==1){
											$val['cond_ful']="error";
											$val['keywords'].="No ".$condition['cond_kw']." at col".$condition['main_cond_ref']." , ";
										}
									}
									$index++;
							}
				
						}
						
						
					}
					$newMatch[]=$val;
				}
			}else{
				return false;
			}
			return $newMatch;
	  }

	  function readfilemkbl(){
			require_once SEO_XLS_READER;
				//print_r($_FILES);
				$xls_array=array();
				if($_FILES['keyword_file1']['name'])
				{
					$file_info=pathinfo($_FILES['keyword_file1']['name']) ;
					$extension=$file_info['extension'] ;
					if($extension == 'xls')
					{
						$data = new Spreadsheet_Excel_Reader();
						$data->read($_FILES['keyword_file1']['tmp_name']);
						if($data->sheets[0]['numRows'])
						{
							$x=1;
							while($x<=$data->sheets[0]['numRows']) {
								$y=1;
								while($y<=$data->sheets[0]['numCols']) {
									$xls_array[$x][$y]   =   $data->sheets[0]['cells'][$x][$y] ;
									$y++;
								}
								$x++;
							}
						}
						$xls_array=array_filter($xls_array) ;
					}
					elseif($extension == 'xlsx')
					{
						//echo "READING XLSX";
						include_once ROOT_PATH . BO_PATH_ . 'nlibrary/script/PHPExcel/IOFactory.php';
						//echo "READING XLSX";
						$objReader = PHPExcel_IOFactory::createReader('Excel2007');
						$objReader->setReadDataOnly(true);
						$objPHPExcel = $objReader->load($_FILES['keyword_file1']['tmp_name']);
						$sheetname = $objPHPExcel->getSheetNames();
						
						//print_r($sheetname);
						foreach($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
							$xls_array[] = $objWorksheet->toArray(null,true,true,false);
						}
						$newArr=array();
						//echo "<pre>";print_r($xls_array);
						foreach($xls_array[0] as $key => $val){
							$temp=array();					
							foreach($val as $k => $v){
								$temp[]=$this->specialCharEncode($v);
							}
							$newArr[]=$temp;
						}
						$xls_array=$newArr;
					 }
					//echo "<pre>";print_r($xls_array); exit;
					 return $xls_array;
			}
	}

	function specialCharEncode($contents){
			if ($this->getmyOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
			{
				$contents = isset($contents) ? ((mb_detect_encoding($contents) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $contents) : $contents) : '';
				$contents = isset($contents) ? html_entity_decode($contents,ENT_QUOTES,"UTF-8") : '';
			}else{
				$contents=html_entity_decode(htmlentities($contents, ENT_QUOTES, 'UTF-8'), ENT_QUOTES , 'ISO-8859-1');
			}
			return $contents;

		}
	function getmyOS($userAgent){
        // Create list of operating systems with operating system name as array key
        $oses = array('iPhone' => '(iPhone)', 'Windows' => 'Win16', 'Windows' => '(Windows 95)|(Win95)|(Windows_95)', // Use regular expressions as value to identify operating system
        'Windows' => '(Windows 98)|(Win98)', 'Windows' => '(Windows NT 5.0)|(Windows 2000)', 'Windows' => '(Windows NT 5.1)|(Windows XP)', 'Windows' => '(Windows NT 5.2)', 'Windows' => '(Windows NT 6.0)|(Windows Vista)', 'Windows' => '(Windows NT 6.1)|(Windows 7)', 'Windows' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)', 'Windows' => 'Windows ME', 'Open BSD' => 'OpenBSD', 'Sun OS' => 'SunOS', 'Linux' => '(Linux)|(X11)', 'Safari' => '(Safari)', 'Macintosh' => '(Mac_PowerPC)|(Macintosh)', 'QNX' => 'QNX', 'BeOS' => 'BeOS', 'OS/2' => 'OS/2', 'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)');

        foreach ($oses as $os => $pattern) {// Loop through $oses array

            // Use regular expressions to check operating system type
            if (strpos($userAgent, $os)) {// Check if a value in $oses array matches current user agent.
                return $os;
                // Operating system was matched so return $oses key
            }
        }
        return 'Unknown';
        // Cannot find operating system so return Unknown
    }	

}
