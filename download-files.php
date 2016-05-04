<?php
    require_once("dbconfig.php");//config file
    class downloadFiles{
        //dint find in controller//
        public function downloadSourceContract(){
            $filename = $_GET['filename'];
            $path_file=$_SERVER['DOCUMENT_ROOT']."/BO/epcontractsource/".$filename;

            $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT']."/BO/epcontractsource/".$filename);
            $filename = str_replace(" ","",basename($filename));
            if($filename=="")
                $filename = $pathinfo['filename'];
            if(file_exists($path_file))
            {
                /** Author: Thilagam **/
                /** Date:29/04/2016 **/
                /** Reason: Code optimization **/

               /* header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/".strtolower($pathinfo['extension']));
                header("Content-Disposition: attachment; filename=".$filename);
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT']."/BO/epcontractsource/".$filename));
                ob_end_flush();
                readfile($path_file);*/
               $this->download($path_file);
                exit;
            }
        }//testfunction//
        //dint find in controller//
        public function downloadBrief0(){
            if($_REQUEST['ao_id'])
            {
                include $_SERVER['DOCUMENT_ROOT'].'/BO/dbconfig.php';
                $res = mysql_query("SELECT filepath  from Delivery where id ='".$_REQUEST['ao_id']."'");
                if(mysql_num_rows($res))
                {
                    $row = mysql_fetch_row($res);
                    $value = $row[0];

                    $pathinfo = pathinfo(fopath."/client_spec".$value);
                    $filename = $pathinfo['filename'];
                    header("Pragma: public"); // required
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private",false); // required for certain browsers
                    header("Content-Type: application/".strtolower($pathinfo['extension']));
                    header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8').".".strtolower($pathinfo['extension']));
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: ".filesize(fopath."/client_spec".$value));
                    ob_end_flush();
                    readfile(fopath."/client_spec".$value);
                }
            }
        }//downloadBrief//


        public function downloadInvoice($fname=false,$cid=false,$final=false){
            $path = '/BO/contract_mission_invoice/';
            if($cid)
            {
                include $_SERVER['DOCUMENT_ROOT'].'/BO/dbconfig.php';

                if($final=='yes')
                {
                    $res = mysql_query("SELECT file_path,invoice_number from ContractMissionInvoice WHERE contract_id='".$cid."' AND archive=0 AND invoice_type='final'");
                }
                else
                {
                    $res = mysql_query("SELECT file_path,invoice_number from ContractMissionInvoice WHERE contract_id='".$cid."' AND archive=0 AND invoice_type!='final'");
                }

                if(mysql_num_rows($res))
                {
                    $zip = new ZipArchive();
                    $zipname = $_SERVER['DOCUMENT_ROOT'].$path."contract-inv-".$cid.'.zip';
                    $zip->open($zipname, ZipArchive::CREATE);

                    while($row = mysql_fetch_assoc($res))
                    {
                        if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$row['file_path']))
                            $zip->addFile($_SERVER['DOCUMENT_ROOT'].$path.$row['file_path'],$row['invoice_number'].".xlsx");
                    }
                    $zip->close();
                    if(file_exists($zipname)){
                        header("Pragma: public");
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: private",false);
                        header('Content-type: application/zip');
                        header('Content-Disposition: attachment; filename="'.basename($zipname).'"');
                        readfile($zipname);
                        unlink($zipname);
                    }
                }
            }
            else
            {
                $filename = $fname;
                $reqfilea = explode("/",$filename);
                $reqfile = $reqfilea[count($reqfilea)-1];
                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/xlsx");
                header("Content-Disposition: attachment; filename=".$reqfile);
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT'].$path.$filename));
                ob_end_flush();
                readfile($_SERVER['DOCUMENT_ROOT'].$path.$filename);
            }
        }

        /** Author: Thilagam **/
        /** Date:04/05/2016 **/
        /** Reason: Code optimization **/
        public function downloadPO($cid){
            $path = '/BO/contractDocuments/';
            if ($_REQUEST['cid']) {
                include $_SERVER['DOCUMENT_ROOT'] . '/BO/dbconfig.php';
                $res = mysql_query("SELECT contractfilepaths,contractcustomfilenames,contractname from QuoteContracts WHERE quotecontractid='" . $_REQUEST['cid'] . "'");
                if (mysql_num_rows($res)) {
                    $zip = new ZipArchive();
                    $row = mysql_fetch_row($res);
                    $zipname = $_SERVER['DOCUMENT_ROOT'] . $path . $row[2] . '.zip';
                    $zip->open($zipname, ZipArchive::CREATE);
                    $exploaded = explode('|', $row[0]);
                    $exploadedfn = explode('|', $row[1]);
                    foreach ($exploaded as $key => $value) {
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $value) && !is_dir($_SERVER['DOCUMENT_ROOT'] . $path . $value)) {
                            $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $value);
                            $filename = str_replace(" ", "", basename($exploadedfn[$key]));
                            if ($filename == "")
                                $filename = $pathinfo['filename'];
                            $filename .= "." . strtolower($pathinfo['extension']);
                            $zip->addFile($_SERVER['DOCUMENT_ROOT'] . $path . $value, $filename);
                        }
                    }
                    $zip->close();
                    $this->download($zipname);
                }
            }
        }//downloadPO//

        /** Author: Thilagam **/
        /** Date:04/05/2016 **/
        /** Reason: Code optimization **/
        public function downloadQuote($index=false,$contract_id=false,$type=false,$task_id=false,$mission_id=false,$quote_id=false,$logid=false,$filename=false)
        {
            ob_start();
            include $_SERVER['DOCUMENT_ROOT'] . '/BO/dbconfig.php';
            $index = $_REQUEST['index'];
            $type = $_REQUEST['type'];
            if ($type == "seo_mission") {
                $path = "/BO/misssionDocuments/";
                $identifier = $_REQUEST['mission_id'];
                $res = mysql_query("SELECT documents_path,documents_name from QuoteMissions WHERE  identifier='" . $identifier . "'");
            } else if ($type == "quote" || $type == "cm_tech" || $type == "cm_seo" || $type == "cm_prod" || $type == "cm_staff") {
                $path = "/BO/quote_documents/";
                $identifier = $_REQUEST['quote_id'];
                $res = mysql_query("SELECT documents_path,documents_name from Quotes WHERE identifier='" . $identifier . "'");
            } elseif ($type == 'saleslog') {
                $path = "/BO/quote_documents/";
                $logid = $_REQUEST['logid'];
                $sale = mysql_query("SELECT custom from QuotesLog WHERE id='" . $logid . "'");
            } elseif ($type == "saleslogdown") {
                $path = "/BO/quote_documents/";
                $quote_id = $_REQUEST['quote_id'];
                $file_name = $_REQUEST['filename'];
                $sales_down = mysql_query("SELECT sales_final_documents_path,sales_final_documents_names from Quotes WHERE identifier='" . $quote_id . "'");
            } else if ($type == "contract") {
                $path = "/BO/contractDocuments/";
                $identifier = $_REQUEST['contract_id'];
                $res = mysql_query("SELECT contractfilepaths,contractcustomfilenames from QuoteContracts WHERE quotecontractid='" . $identifier . "'");
            } else if ($type == "tech_mission") {
                $path = "/BO/misssionDocuments/";
                $identifier = $_REQUEST['mission_id'];
                $res = mysql_query("SELECT documents_path,documents_name from TechMissions WHERE  identifier='" . $identifier . "'");
            } else if ($type == "staff_mission") {
                $path = "/BO/misssionDocuments/";
                $identifier = $_REQUEST['mission_id'];
                $res = mysql_query("SELECT documents_path,documents_name from StaffMissions WHERE  staff_missionId='" . $identifier . "'");
            } else if ($type == "testarticle") {
                $path = "/BO/recruitmentDocuments/";
                $identifier = $_REQUEST['contract_id'];
                $res = mysql_query("SELECT test_art_files_path,test_art_files_name from Recruitment WHERE recruitment_id='" . $identifier . "'");
            } else if ($type == "recruitment") {
                $path = "/BO/recruitmentDocuments/";
                $identifier = $_REQUEST['contract_id'];
                $res = mysql_query("SELECT files_path,files_name from Recruitment WHERE recruitment_id='" . $identifier . "'");
            } else if ($type == "task") {
                $path = "/BO/taskDocuments/";
                $identifier = $_REQUEST['task_id'];
                $res = mysql_query("SELECT documents_path,documents_name from MissionTasks WHERE task_id ='" . $identifier . "'");
            } elseif ($type == "prod_mission") {
                $path = "/BO/misssionDocuments/";
                $identifier = $_REQUEST['mission_id'];
                $res = mysql_query("SELECT documents_path,documents_name from ContractMissions WHERE  contractmissionid='" . $identifier . "'");
            } elseif ($type = 'turnover') {
                $filename = $_REQUEST['filename'];
                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false); // required for certain browsers
                header("Content-Type: application/" . strtolower($filename));
                header("Content-Disposition: attachment; filename=" . html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . "/BO/quotes_weekly_report/$filename"));
                ob_end_flush();
                readfile($_SERVER['DOCUMENT_ROOT'] . "/BO/turnover-report/$filename");
            }
            if ($index == "-1" && ($type == "cm_tech" || $type == "cm_seo" || $type == "cm_staff")) {
                $zipname = $_SERVER['DOCUMENT_ROOT'] . "/BO/quotexls/" . strtoupper($type) . "_" . $identifier . ".zip";
                $zip = new ZipArchive();
                $zip->open($zipname, ZipArchive::OVERWRITE);
                if (mysql_num_rows($res)) {
                    $row = mysql_fetch_row($res);
                    $documents_paths = array_filter(explode("|", $row[0]));
                    $documents_names = explode("|", $row[1]);
                    foreach ($documents_paths as $key => $value) {
                        $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $value);
                        $filename = str_replace(" ", "", basename($documents_names[$key]));
                        if ($filename == "")
                            $filename = $pathinfo['filename'];
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $value))
                            $zip->addFile($_SERVER['DOCUMENT_ROOT'] . $path . $value, html_entity_decode($filename, ENT_COMPAT, 'UTF-8') . "." . strtolower($pathinfo['extension']));
                    }
                }
                if ($type == "cm_tech") {
                    $path = "/BO/misssionDocuments/";
                    $identifier = $_REQUEST['mission_id'];
                    $res = mysql_query("SELECT documents_path,documents_name from TechMissions WHERE  identifier='" . $identifier . "'");
                } elseif ($type == "cm_seo") {
                    $path = "/BO/misssionDocuments/";
                    $identifier = $_REQUEST['mission_id'];
                    $res = mysql_query("SELECT documents_path,documents_name from QuoteMissions WHERE  identifier='" . $identifier . "'");
                } else {
                    $path = "/BO/misssionDocuments/";
                    $identifier = $_REQUEST['mission_id'];
                    $res = mysql_query("SELECT documents_path,documents_name from StaffMissions WHERE  staff_missionId='" . $identifier . "'");
                }
                if (mysql_num_rows($res)) {
                    $row = mysql_fetch_row($res);
                    $documents_paths = array_filter(explode("|", $row[0]));
                    $documents_names = explode("|", $row[1]);
                    foreach ($documents_paths as $key => $value) {
                        $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $value);
                        $filename = str_replace(" ", "", basename($documents_names[$key]));
                        if ($filename == "")
                            $filename = $pathinfo['filename'];
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $value))
                            $zip->addFile($_SERVER['DOCUMENT_ROOT'] . $path . $value, html_entity_decode($filename, ENT_COMPAT, 'UTF-8') . "." . strtolower($pathinfo['extension']));
                    }
                }
                $zip->close();
                if (file_exists($zipname)) {
                    chmod($zipname, 0777);
                    header("Pragma: public"); // required
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private", false); // required for certain browsers
                    header("Content-Type: application/zip");
                    header("Content-Disposition: attachment; filename=" . basename($zipname));
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: " . filesize($zipname));
                    ob_end_flush();
                    readfile($zipname);
                    unlink($zipname);
                }
            } elseif ($index == "-1" && $type == "cm_prod") {
                $zipname = $_SERVER['DOCUMENT_ROOT'] . "/BO/quotexls/" . strtoupper($type) . "_" . $identifier . ".zip";
                $zip = new ZipArchive();
                $zip->open($zipname, ZipArchive::OVERWRITE);
                if (mysql_num_rows($res)) {
                    $row = mysql_fetch_row($res);
                    $documents_paths = array_filter(explode("|", $row[0]));
                    $documents_names = explode("|", $row[1]);
                    foreach ($documents_paths as $key => $value) {
                        $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $value);
                        $filename = str_replace(" ", "", basename($documents_names[$key]));
                        if ($filename == "")
                            $filename = $pathinfo['filename'];
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $value))
                            $zip->addFile($_SERVER['DOCUMENT_ROOT'] . $path . $value, html_entity_decode($filename, ENT_COMPAT, 'UTF-8') . "." . strtolower($pathinfo['extension']));
                    }
                }
                $path = "/BO/misssionDocuments/";
                $quote_id = $_REQUEST['quote_id'];
                $res = mysql_query("SELECT t.documents_path,t.documents_name from TechMissions t WHERE find_in_set(t.identifier, (select techmissions_assigned From Quotes where identifier='" . $quote_id . "') )>0 AND t.include_final='yes'");
                while ($row = mysql_fetch_array($res)) {
                    $documents_paths = array_filter(explode("|", $row[0]));
                    $documents_names = explode("|", $row[1]);
                    foreach ($documents_paths as $key => $value) {
                        $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $value);
                        $filename = str_replace(" ", "", basename($documents_names[$key]));
                        if ($filename == "")
                            $filename = $pathinfo['filename'];
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $value))
                            $zip->addFile($_SERVER['DOCUMENT_ROOT'] . $path . $value, html_entity_decode($filename, ENT_COMPAT, 'UTF-8') . "." . strtolower($pathinfo['extension']));
                    }
                }
                $res = mysql_query("SELECT m.documents_path,m.documents_name
							FROM QuoteMissions m
							INNER JOIN Quotes q ON q.identifier=m.quote_id
							WHERE 1=1  AND m.quote_id='" . $quote_id . "' AND m.include_final='yes' AND m.product IN ('seo_audit','smo_audit') AND (m.misson_user_type='sales' OR m.misson_user_type='seo')
							ORDER BY field(m.misson_user_type, 'sales', 'seo'), m.identifier ASC ");
                while ($row = mysql_fetch_array($res)) {
                    $documents_paths = array_filter(explode("|", $row[0]));
                    $documents_names = explode("|", $row[1]);
                    foreach ($documents_paths as $key => $value) {
                        $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $value);
                        $filename = str_replace(" ", "", basename($documents_names[$key]));
                        if ($filename == "")
                            $filename = $pathinfo['filename'];
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $value))
                            $zip->addFile($_SERVER['DOCUMENT_ROOT'] . $path . $value, html_entity_decode($filename, ENT_COMPAT, 'UTF-8') . "." . strtolower($pathinfo['extension']));
                    }
                }
                if ($_REQUEST['mission_id']) {
                    $path = "/BO/misssionDocuments/";
                    $identifier = $_REQUEST['mission_id'];
                    $res = mysql_query("SELECT documents_path,documents_name from ContractMissions WHERE  contractmissionid='" . $identifier . "'");
                    while ($row = mysql_fetch_array($res)) {
                        $documents_paths = array_filter(explode("|", $row[0]));
                        $documents_names = explode("|", $row[1]);
                        foreach ($documents_paths as $key => $value) {
                            $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $value);
                            $filename = str_replace(" ", "", basename($documents_names[$key]));
                            if ($filename == "")
                                $filename = $pathinfo['filename'];
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $value))
                                $zip->addFile($_SERVER['DOCUMENT_ROOT'] . $path . $value, html_entity_decode($filename, ENT_COMPAT, 'UTF-8') . "." . strtolower($pathinfo['extension']));
                        }
                    }
                }
                $zip->close();
                if (file_exists($zipname)) {
                    chmod($zipname, 0777);
                    header("Pragma: public"); // required
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private", false); // required for certain browsers
                    header("Content-Type: application/zip");
                    header("Content-Disposition: attachment; filename=" . basename($zipname));
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: " . filesize($zipname));
                    ob_end_flush();
                    readfile($zipname);
                    unlink($zipname);
                }
            } elseif (mysql_num_rows($res)) {
                $row = mysql_fetch_row($res);
                $documents_paths = array_filter(explode("|", $row[0]));
                $documents_names = explode("|", $row[1]);
                if ($index == "-1") {
                    $zipname = $_SERVER['DOCUMENT_ROOT'] . "/BO/quotexls/" . strtoupper($type) . "_" . $identifier . ".zip";
                    $zip = new ZipArchive();
                    $zip->open($zipname, ZipArchive::OVERWRITE);
                    foreach ($documents_paths as $key => $value) {
                        $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $value);
                        $filename = str_replace(" ", "", basename($documents_names[$key]));
                        if ($filename == "")
                            $filename = $pathinfo['filename'];
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $value))
                            $zip->addFile($_SERVER['DOCUMENT_ROOT'] . $path . $value, html_entity_decode($filename, ENT_COMPAT, 'UTF-8') . "." . strtolower($pathinfo['extension']));
                        // $zip->addFile($fname);
                    }
                    $zip->close();
                    if (file_exists($zipname)) {
                        chmod($zipname, 0777);
                        header("Pragma: public"); // required
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: private", false); // required for certain browsers
                        header("Content-Type: application/zip");
                        header("Content-Disposition: attachment; filename=" . basename($zipname));
                        header("Content-Transfer-Encoding: binary");
                        header("Content-Length: " . filesize($zipname));
                        ob_end_flush();
                        readfile($zipname);
                        unlink($zipname);
                    }
                } else {
                    $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $documents_paths[$index]);
                    $pathinfo2 = pathinfo($documents_names[$index]);
                    $filename = str_replace(" ", "", basename($documents_names[$index]));
                    if ($filename == "")
                        $filename = $pathinfo['filename'];
                    header("Pragma: public"); // required
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private", false); // required for certain browsers
                    header("Content-Type: application/" . strtolower($pathinfo['extension']));
                    header("Content-Disposition: attachment; filename=" . html_entity_decode($filename, ENT_COMPAT, 'UTF-8') . "." . strtolower($pathinfo['extension']));
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . $path . $documents_paths[$index]));
                    ob_end_flush();
                    readfile($_SERVER['DOCUMENT_ROOT'] . $path . $documents_paths[$index]);
                }
            } elseif (mysql_num_rows($sale)) {
                $row = mysql_fetch_row($sale);
                $documents_paths = array_filter(explode("|", $row[0]));
                $documents_names = explode("|", $row[1]);
                $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $documents_paths[$index]);
                $pathinfo2 = pathinfo($documents_names[$index]);
                $filename = str_replace(" ", "", basename($documents_names[$index]));
                if ($filename == "")
                    $filename = $pathinfo['filename'];
                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false); // required for certain browsers
                header("Content-Type: application/" . strtolower($pathinfo['extension']));
                header("Content-Disposition: attachment; filename=" . html_entity_decode($filename, ENT_COMPAT, 'UTF-8') . "." . strtolower($pathinfo['extension']));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . $path . $documents_paths[$index]));
                ob_end_flush();
                readfile($_SERVER['DOCUMENT_ROOT'] . $path . $documents_paths[$index]);
            } elseif (mysql_num_rows($sales_down)) {
                $row_sale = mysql_fetch_row($sales_down);
                $currentFile = $quote_id . '/' . $file_name;
                $sales_paths = array_filter(explode("|", $row_sale[0]));
                $sales_names = explode("|", $row_sale[1]);
                if (in_array($currentFile, $sales_paths)) {
                    $key = array_search($currentFile, $sales_paths);
                }
                $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'] . $path . $sales_paths[$key]);
                $pathinfo2 = pathinfo($documents_names[$index]);
                $filename = str_replace(" ", "", basename($sales_names[$key]));
                if ($filename == "")
                    $filename = $pathinfo['filename'];
                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false); // required for certain browsers
                header("Content-Type: application/" . strtolower($pathinfo['extension']));
                header("Content-Disposition: attachment; filename=" . html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: " . filesize($_SERVER['DOCUMENT_ROOT'] . $path . $sales_paths[$key]));
                ob_end_flush();
                readfile($_SERVER['DOCUMENT_ROOT'] . $path . $sales_paths[$key]);
            }
        }
        //dint find in controller//
        public function downloadQuoteTestXls(){
            ob_start();

            $session_id=$_GET['session_id'];
            $path = "/BO/missionxls/";
            $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$session_id);

            $filename = str_replace(" ","",basename($session_id));
            if($filename=="")
                $filename = $pathinfo['filename'];

            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false); // required for certain browsers
            header("Content-Type: application/".strtolower($pathinfo['extension']));
            header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT'].$path.$session_id));
            ob_end_flush();
            readfile($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
            unlink($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
        }//downloadQuoteTestXls//
        public function downloadQuoteXls($art_id=false,$session_id=false){
            ob_start();

            if($art_id)
            {
                $session_id=$art_id;
                $path = "/BO/participantsxls/";
                $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
            }
            else
            {
                $session_id=$session_id;
                $path = "/BO/quotexls/";
                $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
            }
            $filename = str_replace(" ","",basename($session_id));
            if($filename=="")
                $filename = $pathinfo['filename'];

            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false); // required for certain browsers
            header("Content-Type: application/".strtolower($pathinfo['extension']));
            header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT'].$path.$session_id));
            ob_end_flush();
            readfile($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
            //unlink($_SERVER['DOCUMENT_ROOT'].$path.$session_id);
        }//DownloadQuoteXls//
        public function downloadSurvey($filename=false,$recruitmenttestartid=false,$recruitmenttestart=false,$testart=false){
            ob_start();
            if($_GET['filename'])
            {
                if($_GET['testart'])
                {
                    $file1 = $file_name = $_SERVER['DOCUMENT_ROOT'].'/BO/recruitmentDocuments/'.$_GET['filename'];
                    $pathinfo = pathinfo($file_name);
                    $file_name = substr($pathinfo['filename'],0,-4).".".$pathinfo['extension'];
                }
                elseif($_GET['recruitmenttestartid'])
                {
                    $file1 = $file_name =  '/home/sites/site5/web/FO/recruitmentTestArticles/'.$_GET['recruitmenttestart'];
                    $pathinfo = pathinfo($file_name);
                    $file_name = $_GET['filename'];
                }
                else
                {
                    $file1 = $file_name = '/home/sites/site5/web/FO/poll_spec/'.$_GET['filename'];
                    $pathinfo = pathinfo($file_name);
                    $file_name = substr($pathinfo['filename'],0,strrpos($pathinfo['filename'],"_")).".".$pathinfo['extension'];
                }

                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/".strtolower($pathinfo['extension']));
                header("Content-Disposition: attachment; filename=".html_entity_decode($file_name, ENT_COMPAT, 'UTF-8'));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($file1));
                ob_end_flush();
                readfile($file1);
            }
        }//downloadSurvey//
        public function downloadTurnoverReport($type=false,$filename=fasle){
            ob_start();
            $type = $type;
            if($type='turnover')
            {
                $filename=$filename;
                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/".strtolower($filename));
                header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT']."/BO/turnover-report/$filename"));
                ob_end_flush();
                readfile($_SERVER['DOCUMENT_ROOT']."/BO/turnover-report/$filename");
                exit;
            }
        }//downloadTurnoverReport//


        public function downloadXlsx(){
            ob_start();
            /** Author: Thilagam **/
            /** Date:03/05/2016 **/
            /** Reason: Code optimization **/
            if( isset($_REQUEST['fullpath']) ) {
                $file = $_REQUEST['fullpath'];
                $filename = explode("/",$file);
                //temparary downloading method//
                if( file_exists($file) )
                {
                    //header('Location: http://ep-test.edit-place.com/FO/invoice/client/xls/'.$filename[count($filename)-1]);
                    $this->download($file);
                }

                /*
                header("Content-type: application/xlsx");
                //header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                header("Content-Disposition: attachment; filename=".$filename[count($filename)-1]);
                header("Content-Length: ".filesize($file));
                ob_clean();
                flush();
                readfile($file);
                */
                unset($_SESSION['file']);
            }
            else{
                echo "file name in session not found";
            }
        }//download xlsx//
        //dint find in controller//
        public function downloadArticle($article_id=false,$type=false){

            $artId=$article_id;
            $type = $type;
            /*$result = mysql_query("SELECT ap.id, ap.article_path, ap.article_name FROM ArticleProcess ap
                                INNER JOIN Participation p ON p.id = ap.participate_id
                                INNER JOIN Article a ON a.id = p.article_id
                                WHERE a.id='".$artId."' AND p.status NOT IN ('bid_refused','closed') AND ap.version=(select max(ap.version)
                                FROM ArticleProcess ap
                                INNER JOIN Participation p ON p.id = ap.participate_id
                                INNER JOIN Article a ON a.id = p.article_id WHERE a.id='".$artId."' AND p.status NOT IN ('bid_refused','closed'))");*/
            if($type == 'corrector')
                $query = "SELECT id, stage, status, article_path, article_name, version FROM ArticleProcess
                            WHERE participate_id = (select p.id FROM Participation p
                            INNER JOIN Article a ON a.id = p.article_id WHERE a.id='".$artId."'
                            AND p.status NOT IN ('bid_refused','closed')) AND stage = 'corrector' AND status IS NULL version != 0
                            ORDER BY version DESC LIMIT 1";

            else
                $query = "SELECT id, stage, status, article_path, article_name, version FROM ArticleProcess
                            WHERE participate_id = (select p.id FROM Participation p
                            INNER JOIN Article a ON a.id = p.article_id WHERE a.id='".$artId."'
                            AND p.status NOT IN ('bid_refused','closed')) AND stage = 'contributor' AND status IS NULL
                            ORDER BY version DESC LIMIT 1";



            $result = mysql_query($query);


            while($row = mysql_fetch_array($result))
            {
                $pathId=$row['article_path'];
                $fileName = $row['article_name'];
            }
//echo $pathId; echo $fileName; exit;
//////////////////////////////////////////////////
            if($pathId)
            {
                $oldserver_path = "/home/sites/site9/web/FO/articles/";
                $server_path = "/home/sites/site5/web/FO/articles/";
                $server_path2 = "/home/sites/site6/web/FO/articles/";
                $dwfile= $server_path.$pathId;
                $dwfile2= $server_path.$pathId;
                $olddwfile= $oldserver_path.$pathId;
                if(file_exists($dwfile2))
                    $fullPath = $dwfile2;
                if(file_exists($dwfile))
                    $fullPath = $dwfile;
                elseif(file_exists($olddwfile))
                    $fullPath = $olddwfile;
                else{
                    echo "File not Exists";exit;  }

                // echo $fullPath; exit;
                // Must be fresh start
                if( headers_sent() )
                    die('Headers Sent');

                // Required for some browsers
                if(ini_get('zlib.output_compression'))
                    ini_set('zlib.output_compression', 'Off');

                // File Exists?
                if(file_exists($fullPath))
                {

                    // Parse Info / Get Extension
                    $fsize = filesize($fullPath);
                    $path_parts = pathinfo($fullPath);
                    $ext = strtolower($path_parts["extension"]);

                    if(!$fileName)
                        $fileName=basename($fullPath);
                    //print_r($path_parts);exit;

                    // Determine Content Type
                    switch ($ext) {
                        case "pdf": $ctype="application/pdf"; break;
                        case "exe": $ctype="application/octet-stream"; break;
                        case "zip": $ctype="application/zip"; break;
                        case 'doc': $ctype="application/msword";break;
                        case 'docx':$ctype="application/vnd.openxmlformats-officedocument.wordprocessingml.document";break;
                        case "xls":
                        case "xlsx":$ctype="application/vnd.ms-excel"; break;
                        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                        case "gif": $ctype="image/gif"; break;
                        case "png": $ctype="image/png"; break;
                        case "jpeg":
                        case "jpg": $ctype="image/jpg"; break;
                        default: $ctype="application/force-download";
                    }

                    //Determine view or download
                    switch($display)
                    {
                        case "inline": $dtype="inline";break;
                        case "attachment" : $dtype="attachment";break;
                        default: $dtype="attachment";
                    }
//echo $dtype;exit;
                    header("Pragma: public"); // required
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private",false); // required for certain browsers
                    header("Content-Type: $ctype");
                    header("Content-Disposition: $dtype; filename=\"".$fileName."\";" );
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: ".$fsize);
                    ob_clean();
                    flush();
                    readfile($fullPath);
                    exit;
                } else
                    die('File Not Found');
            }
//////////////////////////////////////////////////////////////////////

            /*$file_path=explode("|",$file_path);
            $zipname = '/home/sites/site9/web/FO/client_spec/Zip/brief_'.$artId.'.zip';
            $zip=new ZipArchive();
            $zip->open($zipname, ZipArchive::OVERWRITE);
            foreach ($file_path as $key => $value)
            {
                 $fname="/home/sites/site9/web/FO/client_spec".$value;
                 $new_filename = substr($fname,strrpos($fname,'/') + 1);
                //$new_filename = urldecode($new_filename);
                 //$new_filename = iconv("UTF-8", "ISO-8859-1", $new_filename);
                $zip->addFile($fname,$new_filename);
                // $zip->addFile($fname);
            }
                $zip->close();

            if(file_exists($zipname))
            {
                    //echo $zipname;  exit;
                chmod($zipname,0777);

                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/zip");
                header("Content-Disposition: attachment; filename=".basename($zipname));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($zipname));
                ob_end_flush();
                readfile($zipname);
                exit;
            }
            else
            {
                echo "Zip not Exists";exit;
            }  */

        }//downloadArticle//
        public function downloadArticleLatestversion($article_id=false,$type=false){

            $artId=$article_id;
            $type = $type;
            /*$result = mysql_query("SELECT ap.id, ap.article_path, ap.article_name FROM ArticleProcess ap
                                INNER JOIN Participation p ON p.id = ap.participate_id
                                INNER JOIN Article a ON a.id = p.article_id
                                WHERE a.id='".$artId."' AND p.status NOT IN ('bid_refused','closed') AND ap.version=(select max(ap.version)
                                FROM ArticleProcess ap
                                INNER JOIN Participation p ON p.id = ap.participate_id
                                INNER JOIN Article a ON a.id = p.article_id WHERE a.id='".$artId."' AND p.status NOT IN ('bid_refused','closed'))");*/
            if($type == 'corrector'){
                $query = "SELECT id, stage, status, article_path, article_name, version FROM ArticleProcess
                            WHERE participate_id = (select p.id FROM Participation p
                            INNER JOIN Article a ON a.id = p.article_id WHERE a.id='".$artId."'
                            AND p.status NOT IN ('bid_refused','closed')) AND stage = 'corrector' AND status IS NULL AND version !=0
                            ORDER BY version DESC LIMIT 1";
            }else{

                $query = "SELECT id, stage, status, article_path, article_name, version FROM ArticleProcess
                            WHERE participate_id = (select p.id FROM Participation p
                            INNER JOIN Article a ON a.id = p.article_id WHERE a.id='".$artId."'
                            AND p.status NOT IN ('bid_refused','closed')) AND stage = 'contributor' AND status IS NULL
                            ORDER BY version DESC LIMIT 1";
            }

            $result = mysql_query($query);


            while($row = mysql_fetch_array($result))
            {
                $pathId=$row['article_path'];
                $fileName = $row['article_name'];
            }
//echo $pathId; echo $fileName; exit;
//////////////////////////////////////////////////
            if($pathId)
            {
                $server_path = "/home/sites/site5/web/FO/articles/";
                $dwfile= $server_path.$pathId;
                if(file_exists($dwfile))
                    $fullPath = $dwfile;
                else{
                    echo "File not Exists";exit;  }

                // echo $fullPath; exit;
                // Must be fresh start
                if( headers_sent() )
                    die('Headers Sent');

                // Required for some browsers
                if(ini_get('zlib.output_compression'))
                    ini_set('zlib.output_compression', 'Off');

                // File Exists?
                if(file_exists($fullPath))
                {

                    // Parse Info / Get Extension
                    $fsize = filesize($fullPath);
                    $path_parts = pathinfo($fullPath);
                    $ext = strtolower($path_parts["extension"]);

                    if(!$fileName)
                        $fileName=basename($fullPath);
                    //print_r($path_parts);exit;

                    // Determine Content Type
                    switch ($ext) {
                        case "pdf": $ctype="application/pdf"; break;
                        case "exe": $ctype="application/octet-stream"; break;
                        case "zip": $ctype="application/zip"; break;
                        case 'doc': $ctype="application/msword";break;
                        case 'docx':$ctype="application/vnd.openxmlformats-officedocument.wordprocessingml.document";break;
                        case "xls":
                        case "xlsx":$ctype="application/vnd.ms-excel"; break;
                        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                        case "gif": $ctype="image/gif"; break;
                        case "png": $ctype="image/png"; break;
                        case "jpeg":
                        case "jpg": $ctype="image/jpg"; break;
                        default: $ctype="application/force-download";
                    }

                    //Determine view or download
                    switch($display)
                    {
                        case "inline": $dtype="inline";break;
                        case "attachment" : $dtype="attachment";break;
                        default: $dtype="attachment";
                    }
//echo $dtype;exit;
                    header("Pragma: public"); // required
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private",false); // required for certain browsers
                    header("Content-Type: $ctype");
                    header("Content-Disposition: $dtype; filename=\"".$fileName."\";" );
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: ".$fsize);
                    ob_clean();
                    flush();
                    readfile($fullPath);
                    exit;
                } else
                    die('File Not Found');
            }
//////////////////////////////////////////////////////////////////////

            /*$file_path=explode("|",$file_path);
            $zipname = '/home/sites/site9/web/FO/client_spec/Zip/brief_'.$artId.'.zip';
            $zip=new ZipArchive();
            $zip->open($zipname, ZipArchive::OVERWRITE);
            foreach ($file_path as $key => $value)
            {
                 $fname="/home/sites/site9/web/FO/client_spec".$value;
                 $new_filename = substr($fname,strrpos($fname,'/') + 1);
                //$new_filename = urldecode($new_filename);
                 //$new_filename = iconv("UTF-8", "ISO-8859-1", $new_filename);
                $zip->addFile($fname,$new_filename);
                // $zip->addFile($fname);
            }
                $zip->close();

            if(file_exists($zipname))
            {
                    //echo $zipname;  exit;
                chmod($zipname,0777);

                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/zip");
                header("Content-Disposition: attachment; filename=".basename($zipname));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($zipname));
                ob_end_flush();
                readfile($zipname);
                exit;
            }
            else
            {
                echo "Zip not Exists";exit;
            }  */
        }//donwloadArticleLatestversion//
        public function downloadAttachment($m){

            $result = mysql_query("SELECT attachment from Message where id ='".$m."'");
            while($row = mysql_fetch_array($result))
            {
                $attachment=$row['attachment'];
            }

            $filename   =   str_replace(' ', '_', $attachment) ;
            $path_file  =   "/home/sites/site9/web/FO/attachments/" . $attachment  ;

            if(file_exists($path_file))
            {
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Length: ".filesize($path_file));
                ob_clean();
                flush();
                readfile("$path_file");
                exit;
            }
        }//downloadAttachment//
        //found in href//
        public function downloadBrief(){
            $ao_id=$_REQUEST['ao_id'];
            $result = mysql_query("SELECT filepath  from Delivery where id =".$ao_id);
            while($row = mysql_fetch_array($result))
            {
                $file_path=$row['filepath'];
            }
            $file_path=explode("|",$file_path);
            $zipname = '/home/sites/site9/web/FO/client_spec/Zip/brief_'.$ao_id.'.zip';
            $zip=new ZipArchive();
            $zip->open($zipname, ZipArchive::OVERWRITE);
            foreach ($file_path as $key => $value)
            {
                $fname="/home/sites/site9/web/FO/client_spec".$value;
                $new_filename = substr($fname,strrpos($fname,'/') + 1);
                //$new_filename = urldecode($new_filename);
                //$new_filename = iconv("UTF-8", "ISO-8859-1", $new_filename);
                $zip->addFile($fname,$new_filename);
                // $zip->addFile($fname);
            }
            $zip->close();

            if(file_exists($zipname))
            {
                /** Author: Thilagam **/
                /** Date:03/05/2016 **/
                /** Reason: Code optimization **/
                //echo $zipname;  exit;
                chmod($zipname,0777);

                /*header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/zip");
                header("Content-Disposition: attachment; filename=".basename($zipname));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($zipname));
                ob_end_flush();
                readfile($zipname);*/
                $this->download($zipname);
                exit;
            }
            else
            {
                echo "Zip not Exists";exit;
            }
            /*chmod($zipname,0777);
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename='.basename($zipname));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipname));
                //echo filesize($zipname);
                ob_clean();
                readfile($zipname);
                exit;
            */
        }//downloadBrief1//
        //dint find in controller//
        public function downloadBrief2(){
            include('/home/sites/site9/web/BO/nlibrary/script/xmldb.php');

            $poll_id=$_REQUEST['poll'];
            $result = mysql_query("SELECT *  FROM Poll p INNER JOIN Poll_brief2 pb ON p.id=pb.pollid WHERE pb.pollid='".$poll_id."'");

            if ($result) {
                ob_start();
                while($row=mysql_fetch_array($result))
                {
                    echo '<table border="1" style="text-align:left;"> ';

                    echo '<tr>
								<td valign="top" style="font-weight:bold;">A-t-on d&eacute;j&agrave; travaill&eacute; avec le client </td>
								<td style="text-align:left;">'.$row['work'].'</td>
							</tr>';

                    if($row['work'] == 'yes')
                    {
                        $xml_obj = new XMLdb();
                        $art_type_array=$xml_obj->loadArrayv2("EP_ARTICLE_TYPE",'fr');

                        echo '<tr>
								<td valign="top" >En quelle ann&eacute;e ?</td>
								<td style="text-align:left;">'.$row['year'].'</td>
							</tr>
							<tr>
								<td valign="top" >Volum&eacute;trie de l\'ancien contrat ?</td>
								<td style="text-align:left;">'.$row['volume'].'</td>
							</tr>
							<tr>
								<td valign="top" >Type d\'articles de l\'ancien contrat ?</td>
								<td style="text-align:left;">'.$art_type_array[$row['articletype']].'</td>
							</tr>
							<tr>
								<td valign="top" >Tarif de vente moyen par article ?</td>
								<td style="text-align:left;">'.$row['price'].'</td>
							</tr>
							<tr>
								<td valign="top" >Quel &eacute;tait le niveau d\'exigence du client ?</td>
								<td style="text-align:left;">'.$row['level'].'</td>
							</tr>';
                    }

                    if($row['clientype'] == 'seo')
                        $clienttype = 'SEO';
                    else
                        $clienttype = 'Edito';

                    if($row['missionmange'] == 'contract')
                        $missionmange = 'Signataire du contrat';
                    else
                        $missionmange = '&eacute;quipe de r&eacute;daction interne';

                    echo '<tr>
								<td valign="top"  style="font-weight:bold;">Volum&eacute;trie potentielle de ce client sur l\'ann&eacute;e</td>
								<td style="text-align:left;">'.$row['potential'].'</td>
							</tr>
							<tr>
								<td valign="top"  style="font-weight:bold;">Y a-t-il un potentiel annexe &agrave; cette mission</td>
								<td style="text-align:left;">'.$row['potentialannexe'].'</td>
							</tr>
							<tr>
								<td valign="top"  style="font-weight:bold;">Type de clients ?</td>
								<td style="text-align:left;">'.$clienttype.'</td>
							</tr>
							<tr>
								<td valign="top" style="font-weight:bold;">Qui va g&eacute;rer la mission chez le client ? </td>
								<td style="text-align:left;">'.$missionmange.'</td>
							</tr>
							<tr>
								<td valign="top" style="font-weight:bold;">Date de d&eacute;marrage souhait&eacute;e</td>
								<td style="text-align:left;">'.date("d/m/Y H:i:s",strtotime($row['start_date'])).'</td>
							</tr>
							<tr>
								<td valign="top" style="font-weight:bold;">Temps imparti pour la mission (en nombre de jours) </td>
								<td style="text-align:left;">'.$row['daylimit'].'</td>
							</tr>
							<tr>
								<td valign="top" style="font-weight:bold;">Commentaire libre </td>
								<td style="text-align:left;">'.$row['comment'].'</td>
							</tr>
								';
                    echo '</table>';
                    $filename = str_replace(" ","_",$row['title']);
                }
                // Send Header
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");;
                header("Content-Disposition: attachment;filename=".$filename."_brief2.xls ");
                header("Content-Transfer-Encoding: binary ");
            };

        }//downloadBrief2//
        public function downloadContractAgreement($type=false,$user_id=false,$file=false){


            if($type=='zip')
            {
                $zipname = '/home/sites/site6/web/BO/contract_agreements/'.$user_id.'/contract_agreements.zip';
            }
            else if($type=='pdf' && $file )
            {
                $zipname = '/home/sites/site6/web/BO/contract_agreements/'.$user_id.'/'.$file.".pdf";
            }

//echo $zipname;exit;

            if(file_exists($zipname))
            {
                //echo $zipname;  exit;
                chmod($zipname,0777);

                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/$type");
                header("Content-Disposition: attachment; filename=".basename($zipname));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($zipname));
                ob_end_flush();
                readfile($zipname);
                exit;
            }
            else
            {
                echo "Zip not Exists";exit;
            }
        }//downloadContractAgreement//
        //dint find in controller//
        public function downloadExcelformat(){
            $file='/home/sites/site4/web/BO/client_excel/sample.xls';
            //ob_start();

            //echo "TITRE_ARTICLE";
            $filename='Format.xls';
            // Send Header
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");;
            header("Content-Disposition: attachment;filename=".$filename." ");
            header("Content-Transfer-Encoding: binary ");
            readfile($file);
        }//downloadExcelformat//
        //dint find in controller//
        public function downloadExpenseInvoice(){

            $expId=$_REQUEST['expId'];
            $type = $_REQUEST['type'];
            $result = mysql_query("SELECT * FROM Expenses WHERE identifier='".$expId."'");


            while($row = mysql_fetch_array($result))
            {
                $pathId=$row['expense_invoice'];
                $fileName = $row['expense_name'];
                $userid = $row['created_by'];
            }
//////////////////////////////////////////////////

            if($pathId)
            {
                $oldserver_path = "/home/sites/site9/web/FO/expenses/".$userid."/";
                $server_path = "/home/sites/site5/web/FO/expenses/".$userid."/";
                $dwfile= $server_path.$pathId;
                $olddwfile= $oldserver_path.$pathId;
                if(file_exists($dwfile))
                    $fullPath = $dwfile;
                elseif(file_exists($olddwfile)){
                    $fullPath = $olddwfile;
                }
                else{
                    echo "File not Exists";exit;  }

                // echo $fullPath; exit;
                // Must be fresh start
                if( headers_sent() )
                    die('Headers Sent');

                // Required for some browsers
                if(ini_get('zlib.output_compression'))
                    ini_set('zlib.output_compression', 'Off');

                // File Exists?
                if(file_exists($fullPath))
                {

                    // Parse Info / Get Extension
                    $fsize = filesize($fullPath);
                    $path_parts = pathinfo($fullPath);
                    $ext = strtolower($path_parts["extension"]);

                    if(!$fileName)
                        $fileName=basename($fullPath);
                    //print_r($path_parts);exit;

                    // Determine Content Type
                    switch ($ext) {
                        case "pdf": $ctype="application/pdf"; break;
                        case "exe": $ctype="application/octet-stream"; break;
                        case "zip": $ctype="application/zip"; break;
                        case 'doc': $ctype="application/msword";break;
                        case 'docx':$ctype="application/vnd.openxmlformats-officedocument.wordprocessingml.document";break;
                        case "xls":
                        case "xlsx":$ctype="application/vnd.ms-excel"; break;
                        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                        case "gif": $ctype="image/gif"; break;
                        case "png": $ctype="image/png"; break;
                        case "jpeg":
                        case "jpg": $ctype="image/jpg"; break;
                        default: $ctype="application/force-download";
                    }

                    //Determine view or download
                    switch($display)
                    {
                        case "inline": $dtype="inline";break;
                        case "attachment" : $dtype="attachment";break;
                        default: $dtype="attachment";
                    }
//echo $dtype;exit;
                    $fileName.='.'.$ext;
                    header("Pragma: public"); // required
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private",false); // required for certain browsers
                    header("Content-Type: $ctype");
                    header("Content-Disposition: $dtype; filename=\"".$fileName."\";" );
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: ".$fsize);
                    ob_clean();
                    flush();
                    readfile($fullPath);
                    exit;
                } else
                    die('File Not Found');
            }
//////////////////////////////////////////////////////////////////////

            /*$file_path=explode("|",$file_path);
            $zipname = '/home/sites/site9/web/FO/client_spec/Zip/brief_'.$artId.'.zip';
            $zip=new ZipArchive();
            $zip->open($zipname, ZipArchive::OVERWRITE);
            foreach ($file_path as $key => $value)
            {
                 $fname="/home/sites/site9/web/FO/client_spec".$value;
                 $new_filename = substr($fname,strrpos($fname,'/') + 1);
                //$new_filename = urldecode($new_filename);
                 //$new_filename = iconv("UTF-8", "ISO-8859-1", $new_filename);
                $zip->addFile($fname,$new_filename);
                // $zip->addFile($fname);
            }
                $zip->close();

            if(file_exists($zipname))
            {
                    //echo $zipname;  exit;
                chmod($zipname,0777);

                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/zip");
                header("Content-Disposition: attachment; filename=".basename($zipname));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($zipname));
                ob_end_flush();
                readfile($zipname);
                exit;
            }
            else
            {
                echo "Zip not Exists";exit;
            }  */

        }//donwloadExpenseInvoice//
        public function downloadFtv($ftvfile){
            $file = explode('-',$ftvfile);
            $path_file  =    '/home/sites/site5/web/FO/ftv_documents/'.$file[0].'/'.$file[1]  ; //exit;
            $filename1 = $file[1];
            if(file_exists($path_file))
            {
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"".$filename1."\"");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($path_file));
                ob_end_flush();
                @readfile($path_file);
            }
        }
        //dint find in controller//
        public function downloadInvoice1(){
            ob_start();
            $post_array=$_POST;
            $post_array['hide_total']=str_replace('select_rows,','',$post_array['hide_total']);
            //print_r($post_array);exit;
            $ao_id=$_REQUEST['invoice_id'];
            $merge_pdf=$_REQUEST['merge_pdf'];


            //$ckecks = explode(',', $post_array['hide_total']);
            $ckecks = explode(',', $_REQUEST['invoice_id']);

            $i=0;
            foreach($ckecks as $key=>$value)
            {
                $invoice_id_array=explode("_",$value);
                //$value=str_replace('R','',end($invoice_id_array));
                $value=preg_replace('/R[?\d{1}]?/','',end($invoice_id_array));

                //added w.r.t date change in invoice name
                if(strstr($value, '/'))
                {
                    $invoiceId_array=explode("-",$value);
                    $date_array=explode("/",$invoiceId_array[0]);
                    $value=$date_array[2]."-".$date_array[1]."-".$date_array[0]."-".$invoiceId_array[1];
                }

                $invoiceids[$i]=$value;
                $i++;
            }
            //echo "<pre>";
            //print_r($invoiceids); exit;

            for($j=0; $j<count($invoiceids); $j++)
            {
                $invoiceid='ep_invoice_'.$invoiceids[$j];
                $invoiceQuery="select i.user_id,i.invoice_path from Royalties r LEFT JOIN Invoice i ON r.invoiceId=i.invoiceId
		   where i.invoiceId='".$invoiceid."' GROUP BY i.invoiceId  ORDER BY r.created_at DESC";
                $result = mysql_query($invoiceQuery);
                while($row = mysql_fetch_array($result))
                {
                    $invoice_path[$j]=$row['invoice_path'];
                    $user_id[$j]=$row['user_id'];
                }
                if($invoice_path[$j])
                    $invoicePDFPath[$j]="/home/sites/site9/web/FO/invoice/".$invoice_path[$j];
                $file_path[$j]=$invoicePDFPath[$j];
                $file_user_id[$j]=$user_id[$j];
            }
            //print_r($file_path);

            if(count($file_path)>0 &&  $merge_pdf=='yes')
            {

                $input_files=implode(" ",$file_path);

                $merge_pdf="/home/sites/site9/web/FO/invoice/merge_files/".date("YmdHis").".pdf";

                //$merge_pdf="/home/sites/site9/web/FO/invoice/merge_files/20140415093637.pdf";


                $cmd="gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite  -sOutputFile=$merge_pdf $input_files";
                exec($cmd,$output);


                sleep(5);


                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename='.basename($merge_pdf));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($merge_pdf));
                readfile($merge_pdf);
                exit;

            }
            else if(count($file_path)>0)
            {
                $zipname = "/home/sites/site9/web/FO/invoice/zip/invoice_".rand(1000, 9999).".zip";
                $zip=new ZipArchive();
                $zip->open($zipname, ZipArchive::OVERWRITE);
                foreach ($file_path as $key => $value)
                {
                    $fname=$value;
                    $new_filename = substr($fname,strrpos($fname,'/') + 1);

                    //Added for invoice id date changes
                    $invoiceId_array=explode("-",$new_filename);
                    $new_filename=$invoiceId_array[2]."-".$invoiceId_array[1]."-".$invoiceId_array[0]."-".$invoiceId_array[3];
                    //ended


                    $user_full_name=str_replace(" ","_",trim(getUserName($file_user_id[$key])));

                    $new_filename = frenchCharsToEnglish($user_full_name)."_".$new_filename;

                    //echo $fname."--".$new_filename."<br>";
                    $zip->addFile($fname,$new_filename);
                    // $zip->addFile($fname);
                }
                //exit;
                $zip->close();
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($zipname));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipname));
                readfile($zipname);
                exit;
            }

            function getUserName($user)
            {
                //CONCAT(first_name,' ',UPPER(SUBSTRING(last_name, 1,1)))) as sendername

                $userQuery="select CONCAT(first_name,' ',last_name) as full_name from User u
						INNER JOIN UserPlus up ON u.identifier=up.user_id
						where identifier='".$user."'";
                //echo $senderQuery;exit;
                $result = mysql_fetch_array(mysql_query($userQuery));
                return $result['full_name'];


            }
            function frenchCharsToEnglish($word)
            {
                $pattern = array("'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "';'");

                $replace = array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'i', 'i', 'i', 'I', 'I', 'I', 'I', 'I', 'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', 'y', 'y', 'Y', 'o', 'O', 'a', 'A', 'A', 'c', 'C', 'A', 'I', 'I', '', '', '', '');

                $pattern_1	=	array("e�", "(", ")", "[", "]", "{", "}", "/", "!", "@", "#", "$", "^", "&", "*", "-", "__", "'", '"', "%");

                $replace_1 = array("e","_","_","_","_","_","_","_","_","_","_","_","_","_","_","_","_","_","_","");

                $replaced	=	str_replace($pattern_1, $replace_1, preg_replace($pattern, $replace, $word)) ;
                //added By arun
                $replaced= utf8_decode($replaced);
                $replaced   =   str_replace("?",'',$replaced);

                return	$replaced ;
            }
        }//downloadInvoice1//
        //dint find in controller//
        public function downloadPollxls(){
            $poll_id=$_REQUEST['id'];

//ini_set('display_errors', '1');
            include('/home/sites/site9/web/BO/nlibrary/script/xmldb.php');
            $xml_obj = new XMLdb();
            $country_array=$xml_obj->loadArrayv2("countryList",'fr');
            $profession_array=$xml_obj->loadArrayv2("CONTRIB_PROFESSION",'fr');
            $language_array=$xml_obj->loadArrayv2("EP_LANGUAGES",'fr');

            $poll_details = mysql_fetch_assoc(mysql_query("SELECT p.title,u.first_name,u.last_name FROM Poll p LEFT JOIN UserPlus u ON p.client=u.user_id	WHERE p.id='".$poll_id."'"));

            $pollcontribslist = mysql_query("SELECT
						p.price_user,
						u.email,u.status,DATE_FORMAT(u.created_at, '%d/%m/%Y') as created_at,u.profile_type,u.blackstatus,
						up.first_name,up.last_name,up.initial,up.address,up.city,up.state,up.zipcode,up.country,up.phone_number,
						DATE_FORMAT(c.dob, '%d/%m/%Y') as dob1,c.*
					FROM
						Poll_Participation p INNER JOIN User u ON p.user_id=u.identifier
						LEFT JOIN UserPlus up ON u.identifier=up.user_id
						LEFT JOIN Contributor c ON up.user_id=c.user_id
					WHERE p.poll_id='".$poll_id."' AND p.status='active'");

            if ($pollcontribslist) {
                ob_start();
                echo '<table border="1">';
                echo '<tr>
		<th>NAME</th>
		<th>EMAIL</th>
		<th>INITIAL</th>
		<th>STATUS</th>
		<th>DATE OF JOIN</th>
		<th>PROFILE TYPE</th>
		<th>BLACK STATUS</th>
		<th>ADDRESS</th>
		<th>CITY</th>
		<th>STATE</th>
		<th>COUNTRY</th>
		<th>PINCODE</th>
		<th>PHONE</th>
		<th>DOB</th>
		<th>UNIVERSITY</th>
		<th>PROFESSION</th>
		<th>LANGUAGE</th>
		<th>CATEGORY</th>
		<th>DESCRIPTION</th>
		<th>PRICE</th>
	</tr>';

                while($poll_contribs=mysql_fetch_array($pollcontribslist))
                {
                    $name=$poll_contribs['first_name'].' '.$poll_contribs['last_name'];
                    $poll_contribs['self_details']=str_replace("<br />","",$poll_contribs['self_details']);

                    if($poll_contribs['initial']=='mr')
                        $initial="M";
                    else
                        $initial="F";

                    //Job
                    $jobdetails=mysql_fetch_assoc(mysql_query("SELECT title FROM ContributorExperience WHERE user_id='".$poll_contribs['user_id']."' AND type='job' AND to_year='0'"));

                    //Education
                    $edudetails=mysql_query("SELECT title,institute FROM ContributorExperience WHERE user_id='".$poll_contribs['user_id']."' AND type='education'");
                    $education="";
                    while($edulist=mysql_fetch_array($edudetails))
                    {
                        if($e>0)
                            $education.=" / ";

                        $education.=$edulist['title'].", ".$edulist['institute'];
                    }

                    echo '<tr>
					<td valign="top">'.$name.'</td>
					<td valign="top">'.$poll_contribs['email'].'</td>
					<td valign="top">'.$initial.'</td>
					<td valign="top">'.$poll_contribs['status'].'</td>
					<td valign="top">'.$poll_contribs['created_at'].'</td>
					<td valign="top">'.$poll_contribs['profile_type'].'</td>
					<td valign="top">'.$poll_contribs['blackstatus'].'</td>
					<td valign="top">'.$poll_contribs['address'].'</td>
					<td valign="top">'.$poll_contribs['city'].'</td>
					<td valign="top">'.$poll_contribs['state'].'</td>
					<td valign="top">'.$country_array[$poll_contribs['country']].'</td>
					<td valign="top">'.$poll_contribs['zipcode'].'</td>
					<td valign="top">'.$poll_contribs['phone_number'].'</td>
					<td valign="top">'.$poll_contribs['dob1'].'</td>
					<td valign="top">'.$education.'</td>
					<td valign="top">'.$jobdetails['title'].'</td>
					<td valign="top">'.$language_array[$poll_contribs['language']].'</td>
					<td valign="top">'.$xml_obj->getCategoryName($poll_contribs['favourite_category']).'</td>
					<td valign="top">'.stripslashes($poll_contribs['self_details']).'</td>
					<td valign="top">'.$poll_contribs['price_user'].'</td>
				</tr>';
                }
                echo '</table>';
                //exit;
                $title=str_replace(" ","_",$poll_details['title']);
                $name=str_replace(" ","_",$poll_details['first_name']);

                $filename=$title.'_'.$name.'.xls';
                // Send Header
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");;
                header("Content-Disposition: attachment;filename=".$filename." ");
                header("Content-Transfer-Encoding: binary ");

            };
        }
        //dint find in controller//
        public function downloadProposal(){
            $filename=$_REQUEST['file'];
            $path_file="seo_download/".$_REQUEST['tool']."/".$filename;
//echo $path_file;exit;
            if(file_exists($path_file))
            {
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=$filename");
                ob_clean();
                flush();
                readfile("$path_file");
                exit;
            }
        }//downloadProposal//
        //dint find in controller//
        public function downloadQuesioncontrib(){
            $poll_id=$_REQUEST['poll'];
            $user_id=$_REQUEST['contrib'];

            $SMICresult = mysql_fetch_assoc(mysql_query("SELECT l.SMIC,c.percentage FROM Poll p INNER JOIN  LanguageSMIC l ON p.language=l.id INNER JOIN  CategoryDifficultyPercent c ON p.category=c.id WHERE p.id='".$poll_id."'"));
            $smic = $SMICresult['SMIC'] * ($SMICresult['percentage']/100);

            $pollqdetails = mysql_query("SELECT
					p.title,p.language,p.category,pq.title as question,pq.type,pq.option,pu.response,u.first_name,u.last_name
				FROM
					Poll p LEFT JOIN PollUserResponse pu ON p.id=pu.poll_id
					LEFT JOIN Poll_question pq ON p.id=pq.pollid AND pu.question_id=pq.id
					LEFT JOIN UserPlus u ON u.user_id=pu.user_id
				WHERE
					p.id='".$poll_id."'  AND pu.user_id='".$user_id."' ");

            if ($pollqdetails) {
                ob_start();
                $i=0;
                while($poll_qdetails=mysql_fetch_array($pollqdetails))
                {
                    if($i==0)
                    {
                        echo '<table border="1"> ';
                        echo '<tr>
					<th>POLL</th>
					<td colspan="2">'.$poll_qdetails['title'].'</td>
				</tr>
				<tr>
					<th>USER</th>
					<td colspan="2">'.$poll_qdetails['first_name'].'&nbsp;'.$poll_qdetails['last_name'].'</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<th>Questions:</th>
					<td colspan="2"></td>
				</tr>
				';
                        $title=str_replace(" ","_",$poll_qdetails['title']);
                        $name=str_replace(" ","_",$poll_qdetails['first_name']);
                    }

                    echo '<tr>
					<td valign="top">'.$poll_qdetails['question'].'</td>
					<td valign="top">'.$poll_qdetails['response'].'</td>';

                    if($poll_qdetails['type']=='price' || $poll_qdetails['type']=='bulk_price' || $poll_qdetails['type']=='range_price')
                    {
                        if($poll_qdetails['response']>$smic)
                            echo '<td valign="top">SMIC OK</td>';
                        else
                            echo '<td valign="top">SMIC NOK</td>';
                    }
                    else
                        echo '<td></td>';

                    echo '</tr>';
                    $i++;
                }
                echo '</table>';
                //exit;

                $filename=$title.'_'.$name.'.xls';
                // Send Header
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");;
                header("Content-Disposition: attachment;filename=".$filename." ");
                header("Content-Transfer-Encoding: binary ");
            };

        }//downloadQuesioncontrib//

        /*Used in hrm module*/
        public function downloadSamplecsv(){
            $path_file  =    '/home/sites/site6/web/BO/holiday_csv/Sample.csv' ;
            if(file_exists($path_file))
            {
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"Sample.csv\"");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($path_file));
                ob_end_flush();
                @readfile($path_file);
            }
        }//downloadSamplecsv//
        //found in href//
        public function downloadSeo(){
            if(isset($_GET['saction']) && $_GET['saction']=='download' && isset($_GET['file']) && isset($_GET['ext']))
            {
                $filename=$_GET['file'].".".$_GET['ext'];
                if($_GET['tool']=='gsuggest')
                    $path_file="/home/sites/site6/web/BO/seo_download/gsuggest/".$filename;
                else
                    $path_file="/home/sites/site6/web/BO/seo_download/scraper/".$filename;
                if(file_exists($path_file))
                {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename='.basename($path_file));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($path_file));
                    readfile($path_file);
                    exit;
                }
                else
                {
                    echo "File $path_file Not Exists";
                }
            }
        }//downloadSeo//
        public function downloadSeoPosition($saction=false,$file=false,$ext=fasle){
            if(isset($saction) && $saction=='download' && isset($file) && isset($ext))
            {
                $filename=$file.".".$ext;
                $path_file="/home/sites/site6/web/BO/seo_download/position/".$filename;
                if(file_exists($path_file))
                {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename='.basename($path_file));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($path_file));
                    readfile($path_file);
                    exit;
                }
                else
                {
                    echo "File Not Exists";
                }
            }
        }//downloadSeoPosition//
        public function downloadSeoresult($filename=false,$tool=false,$ext=false){
            $filename=$filename.".".$ext ;
            $path_file="seo_download/".$tool."/".$filename;
//echo $path_file;exit;
            if($ext == 'xls' || $ext == 'xlsx')
                $app = $ext;
            else
                $app = "octet-stream";

            if(file_exists($path_file))
            {
                header("Content-type: application/".$app);
                header("Content-Disposition: attachment; filename=$filename");
                ob_clean();
                flush();
                readfile("$path_file");
                exit;
            }
        }//downloadSeoresult//
        //dint find in controller//
        public function downloadSsnFile(){
            $userId=$_REQUEST['userId'];
            $type = $_REQUEST['type'];
            $result = mysql_query("SELECT * FROM BoUser WHERE user_id='".$userId."'");

            while($row = mysql_fetch_array($result))
            {
                $pathId=$row['ssn_file'];
                $ext = explode(".",$row['ssn_file']);
                $fileName = $row['ssn'].".".$ext[1];
            }
//////////////////////////////////////////////////

            if($pathId)
            {
                $oldserver_path = "/home/sites/site9/web/FO/ssn_files/".$userId."/";
                $server_path = "/home/sites/site5/web/FO/ssn_files/".$userId."/";
                $dwfile= $server_path.$pathId;
                $olddwfile= $oldserver_path.$pathId;
                if(file_exists($dwfile))
                    $fullPath = $dwfile;
                elseif(file_exists($olddwfile)){
                    $fullPath = $olddwfile;
                }
                else{
                    echo "File not Exists";exit;  }

                // echo $fullPath; exit;
                // Must be fresh start
                if( headers_sent() )
                    die('Headers Sent');

                // Required for some browsers
                if(ini_get('zlib.output_compression'))
                    ini_set('zlib.output_compression', 'Off');

                // File Exists?
                if(file_exists($fullPath))
                {

                    // Parse Info / Get Extension
                    $fsize = filesize($fullPath);
                    $path_parts = pathinfo($fullPath);
                    $ext = strtolower($path_parts["extension"]);

                    if(!$fileName)
                        $fileName=basename($fullPath);
                    //print_r($path_parts);exit;

                    // Determine Content Type
                    switch ($ext) {
                        case "pdf": $ctype="application/pdf"; break;
                        case "exe": $ctype="application/octet-stream"; break;
                        case "zip": $ctype="application/zip"; break;
                        case 'doc': $ctype="application/msword";break;
                        case 'docx':$ctype="application/vnd.openxmlformats-officedocument.wordprocessingml.document";break;
                        case "xls":
                        case "xlsx":$ctype="application/vnd.ms-excel"; break;
                        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                        case "gif": $ctype="image/gif"; break;
                        case "png": $ctype="image/png"; break;
                        case "jpeg":
                        case "jpg": $ctype="image/jpg"; break;
                        default: $ctype="application/force-download";
                    }

                    //Determine view or download
                    switch($display)
                    {
                        case "inline": $dtype="inline";break;
                        case "attachment" : $dtype="attachment";break;
                        default: $dtype="attachment";
                    }
//echo $dtype;exit;
                    header("Pragma: public"); // required
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private",false); // required for certain browsers
                    header("Content-Type: $ctype");
                    header("Content-Disposition: $dtype; filename=\"".$fileName."\";" );
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: ".$fsize);
                    ob_clean();
                    flush();
                    readfile($fullPath);
                    exit;
                } else
                    die('File Not Found');
            }
//////////////////////////////////////////////////////////////////////

            /*$file_path=explode("|",$file_path);
            $zipname = '/home/sites/site9/web/FO/client_spec/Zip/brief_'.$artId.'.zip';
            $zip=new ZipArchive();
            $zip->open($zipname, ZipArchive::OVERWRITE);
            foreach ($file_path as $key => $value)
            {
                 $fname="/home/sites/site9/web/FO/client_spec".$value;
                 $new_filename = substr($fname,strrpos($fname,'/') + 1);
                //$new_filename = urldecode($new_filename);
                 //$new_filename = iconv("UTF-8", "ISO-8859-1", $new_filename);
                $zip->addFile($fname,$new_filename);
                // $zip->addFile($fname);
            }
                $zip->close();

            if(file_exists($zipname))
            {
                    //echo $zipname;  exit;
                chmod($zipname,0777);

                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/zip");
                header("Content-Disposition: attachment; filename=".basename($zipname));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($zipname));
                ob_end_flush();
                readfile($zipname);
                exit;
            }
            else
            {
                echo "Zip not Exists";exit;
            }  */

        }//downloadSsnFile//
        public function downloadUserXls(){
            include("nconfig/path.php");
            //include class definition file
            include("nconfig/class.php");
//include session file
            include("nconfig/session.php");

            $content=$_SESSION['content'];

//echo $content;exit;

            $file = 'excelFile-'.date("Y-M-D")."-".time().'.xls';
            if($content)
            {
                unset($_SESSION['content']);
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

        }//donwloadUserXls//
        //found in href//
        public function downloadWiki(){
            if(isset($_GET['saction']) && $_GET['saction']=='download' && isset($_GET['file']) && isset($_GET['ext']))
            {
                $filename=$_GET['file'].".".$_GET['ext'];
                $path_file="/home/sites/site4/web/BO/seo_download/wiki/".$filename;

                if(file_exists($path_file))
                {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename='.basename($path_file));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($path_file));
                    readfile($path_file);
                    exit;
                }
                else
                {
                    echo "File $path_file Not Exists";
                }
            }
        }//downloadWiki//
        //dint find in controller//
        public function downloadApfile(){
            $result = mysql_query("SELECT article_name,article_path from ArticleProcess where id ='".$_REQUEST['path']."'");
            while($row = mysql_fetch_array($result))
            {
                $attachment=$row['article_name'];
                $filepath=$row['article_path'];
            }

            $filename   =   str_replace(' ', '_', $attachment) ;
            $path_file  =   "/home/sites/site9/web/FO/articles/".$filepath ;

            if(file_exists($path_file))
            {
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=$attachment");
                header("Content-Length: ".filesize($path_file));
                ob_clean();
                flush();
                readfile("$path_file");
                exit;
            }
        }//downloadApfile//
        //found in href//
        public function downloadPremiumquote(){

            $quote=$_REQUEST['id'];
            include('/home/sites/site6/web/BO/nlibrary/script/xmldb.php');
            $xml_obj = new XMLdb();

            $lang_array=$xml_obj->loadArrayv2("EP_LANGUAGES",'fr');
            $category_array=$xml_obj->loadArrayv2("EP_ARTICLE_CATEGORY",'fr');
            $type_array=array("seo"=>"Article seo","desc"=>"Descriptifs produit","blog"=>"Article de blog","news"=>"News","guide"=>"Guide","other"=>"Autre");
            //$obj_array=array("1"=>"Je veux augmenter mon trafic Google","2"=>"Je veux du contentu qui buzz","3"=>"Je veux changer l'image de mon site","4"=>"Autre");
            $obj_array=array("premium"=>"Je veux &ecirc;tre recontact&eacute; par Edit-place pour parler de mon projet avant publication","liberte"=>"Je veux directment entrer en contact avec les r&eacute;dacteurs/traducteurs d'Edit-place","liberteprivate"=>"Je veux proposer un projet &agrave; un de mes r&eacute;dacteurs/traducteurs favoris","dontknow"=>"Je ne sais pas");
            $frequency_array=array("once"=>"En 1 seule fois","day"=>"Par jour","week"=>"Par semaine","month"=>"Par mois");
            $job_array=array("1"=>"PDG ou g&eacute;rant","2"=>"Commercial","3"=>"Marketing","4"=>"Directeur technique","5"=>"Web designer","6"=>"Chef de projet","7"=>"SEO manager","8"=>"Autre");
            $mots_array=array("seo"=>"130","desc"=>"80 mots","blog"=>"500 mots","news"=>"200 mots","guide"=>"500 mots","other"=>"-");

            $quote_details = mysql_fetch_assoc(mysql_query("SELECT
						p.*,c.company_name,c.category,c.company_name,c.website,u.email,up.first_name,up.last_name,up.phone_number,c.job
				FROM
					PremiumQuotes p INNER JOIN User u ON p.user_id=u.identifier
					LEFT JOIN UserPlus up ON u.identifier=up.user_id
					LEFT JOIN Client c ON u.identifier=c.user_id
				WHERE
					p.id='".$quote."' "));


            if($quote_details)
            {
                ob_start();
                echo "<table border=1>
					<tr>
						<th colspan=4 style=background-color:#357EBD>D&eacute;tails de la demande de devis</th>
					</tr>
					<tr>
						";

                if($quote_details['con_type']=='translation')
                {
                    echo "<td><b>Traduction de contenu</b></td>
					 <td colspan=3>".$lang_array[$quote_details['from_language']]." -> ".$lang_array[$quote_details['to_language']]."</td>";
                }
                else
                {
                    echo "<td>R&eacute;daction de contenu</td>
					 <td>".$lang_array[$quote_details['from_language']]."</td>";
                }

                $typear=explode("|",$quote_details['type']);
                $numar=explode("|",$quote_details['total_article']);
                $freqar=explode("|",$quote_details['frequency']);

                for($t=0;$t<count($typear);$t++)
                {
                    if($typear[$t]=="other")
                        $type="Autres - ".$quote_details['other_type'];
                    else
                        $type=$type_array[$typear[$t]];

                    echo "	<tr><td valign=top><b>Type de contenu </b></td>
							<td>".$type."</td>
							<td colspan=2>".$mots_array[$typear[$t]]."</td>
						<tr>
							<td>Volume</td>
							<td colspan=3 style=text-align:left;>".$numar[$t]."</td>
						</tr>
						<tr>
							<td>R&eacute;currence</td>
							<td colspan=3>".$frequency_array[$freqar[$t]]."</td>
						</tr>";
                }

                if($quote_details['dontknowcheck']=="yes")
                    $checktext="Oui";
                else
                    $checktext="Non";

                echo "
				 <tr>
					<td><b>Je ne sais pas, je cherche juste un bon r&eacute;dacteur pour mon site</b></td>
					<td colspan=3>".$checktext."</td>
				</tr>";

                if(count($quote_details['objective'])>0)
                {
                    //$objj=explode(",",$quote_details['objective']);
                    echo "<tr><td valign=top><b>Mes attentes </b></td>";

                    /*for($o=0;$o<count($objj);$o++)
                    {
                        if($o!=0)
                            echo "<tr>";
                        echo "	<td colspan=3>".$obj_array[$objj[$o]]."</td>
                            </tr>";
                    }*/
                    echo "<td colspan=3>".$obj_array[$quote_details['objective']]."</td></tr>";
                    //if($quote_details['other_objective']!="")
                    //echo "<tr><td colspan=4>".$quote_details['other_objective']."</td></tr>";
                }

                echo "<tr><td colspan=4></td></tr><tr><td colspan=4></td></tr>";

                echo "<tr><th colspan=4 style=background-color:#357EBD>D&eacute;tails client</th></tr>
				<tr>
					<td><b>Nom de l'entreprise</b></td>
					<td colspan=3>".$quote_details['company_name']."</td>
				</tr>
				<tr>
					<td><b>URL du site internet</b></td>
					<td colspan=3>".$quote_details['website']."</td>
				</tr>
				<tr>
					<td><b>Nom</b></td>
					<td colspan=3>".$quote_details['last_name']."</td>
				</tr>
				<tr>
					<td><b>Pr&eacute;nom</b></td>
					<td colspan=3>".$quote_details['first_name']."</td>
				</tr>
				<tr>
					<td><b>Email</b></td>
					<td colspan=3>".$quote_details['email']."</td>
				</tr>
				<tr>
					<td><b>T&eacute;l&eacute;phone</b></td>
					<td colspan=3 style=text-align:left>#".$quote_details['phone_number']."</td>
				</tr>
				<tr>
					<td><b>Fonction</b></td>
					<td colspan=3>".$job_array[$quote_details['job']]."</td>
				</tr>

				";

                echo "</table>";

                $quote_details['company_name']=str_replace(" ","_",$quote_details['company_name']);


                $xlsFile =$quote_details['user_id'].'_'.$quote_details['company_name'].'_'.$quote.'.xls';

                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");;
                header("Content-Disposition: attachment;filename=".$xlsFile);
                header("Content-Transfer-Encoding: binary ");
            }
            else
                echo "No data";
        }//downloadPremiumquote//
        /***** by thilagam on 29.04.2016 *****/
        //function edited and code updated//
        public function downloadSalesReportXlsx(){
            ob_start();
            if(isset($_REQUEST['filename']) && $_REQUEST['filename'] != '' ) {
                $filename = $_REQUEST['filename'];
                 $fullPath= $_SERVER['DOCUMENT_ROOT']."/BO/quotexls/$filename";
                $this->download($fullPath);
                /***** commented by thilagam *****/
                //ceommented cox of code upgrade//
                /*header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/".strtolower($filename));
                header("Content-Disposition: attachment; filename=".html_entity_decode($filename, ENT_COMPAT, 'UTF-8'));
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT']."/BO/quotexls/$filename"));
                ob_end_flush();
                readfile($_SERVER['DOCUMENT_ROOT']."/BO/quotexls/$filename");*/
                exit;
            }
            else{
                echo "file name in session not found";
            }
        }//download downloadSalesReportXlsx//
        //function to download zip file//
        public function downloadFile(){
            $fullPath= $_REQUEST['fullPath'];
            $this->download($fullPath);
        }
        /* *** added on 13.04.2016 *** */
        //function to download latest aricle ralted to the clicked client//
        public function downloadLastestArticleZip(){
            $fullPath= $_REQUEST['fullPath'];
            $this->download($fullPath);
        }
        // common funcation to download any files with full path//
        //function to download all details which have been validated realted to BNP
        public function download($fullPath,$fileName=false){
            if(file_exists($fullPath))
            {

                // Parse Info / Get Extension
                $fsize = filesize($fullPath);
                $path_parts = pathinfo($fullPath);
                $ext = strtolower($path_parts["extension"]);

                if(!$fileName)
                    $fileName=basename($fullPath);
                //print_r($path_parts);exit;

                // Determine Content Type
                switch ($ext) {
                    case "pdf": $ctype="application/pdf"; break;
                    case "exe": $ctype="application/octet-stream"; break;
                    case "zip": $ctype="application/zip"; break;
                    case 'doc': $ctype="application/msword";break;
                    case 'docx':$ctype="application/vnd.openxmlformats-officedocument.wordprocessingml.document";break;
                    case "xls":
                    case "xlsx":$ctype="application/vnd.ms-excel"; break;
                    case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                    case "gif": $ctype="image/gif"; break;
                    case "png": $ctype="image/png"; break;
                    case "jpeg":
                    case "jpg": $ctype="image/jpg"; break;
                    default: $ctype="application/force-download";
                }

                //Determine view or download
                switch($display)
                {
                    case "inline": $dtype="inline";break;
                    case "attachment" : $dtype="attachment";break;
                    default: $dtype="attachment";
                }
//echo $dtype;exit;
                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: $ctype");
                header("Content-Disposition: $dtype; filename=\"".$fileName."\";" );
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".$fsize);
                ob_clean();
                flush();
                readfile($fullPath);
                exit;
            } else
                die('File Not Found');
        }// common funcation to download any files with full path//


    }//class//
    $download_obj = new downloadFiles();
    $download_obj->$_REQUEST['function']();
    //echo $_REQUEST['function'];

?>

