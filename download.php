<?php
    class downloadFiles{
        public function downloadSourceContract(){
            $filename = $_GET['filename'];
            $path_file=$_SERVER['DOCUMENT_ROOT']."/BO/epcontractsource/".$filename;

            $pathinfo = pathinfo($_SERVER['DOCUMENT_ROOT']."/BO/epcontractsource/".$filename);
            $filename = str_replace(" ","",basename($filename));
            if($filename=="")
                $filename = $pathinfo['filename'];
            if(file_exists($path_file))
            {
                header("Pragma: public"); // required
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private",false); // required for certain browsers
                header("Content-Type: application/".strtolower($pathinfo['extension']));
                header("Content-Disposition: attachment; filename=".$filename);
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT']."/BO/epcontractsource/".$filename));
                ob_end_flush();
                readfile($path_file);
                exit;
            }
        }//testfunction//
        public function downloadBrief(){
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
        public function downloadInvoice(){
            $path = '/BO/contract_mission_invoice/';
            if($_REQUEST['cid'])
            {
                include $_SERVER['DOCUMENT_ROOT'].'/BO/dbconfig.php';

                if($_REQUEST['final']=='yes')
                {
                    $res = mysql_query("SELECT file_path,invoice_number from ContractMissionInvoice WHERE contract_id='".$_REQUEST['cid']."' AND archive=0 AND invoice_type='final'");
                }
                else
                {
                    $res = mysql_query("SELECT file_path,invoice_number from ContractMissionInvoice WHERE contract_id='".$_REQUEST['cid']."' AND archive=0 AND invoice_type!='final'");
                }

                if(mysql_num_rows($res))
                {
                    $zip = new ZipArchive();
                    $zipname = $_SERVER['DOCUMENT_ROOT'].$path."contract-inv-".$_REQUEST['cid'].'.zip';
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
                $filename = $_REQUEST['fname'];
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
        public function downloadXlsx(){
            ob_start();
            if( isset($_SESSION['file']) ) {
                $file = $_SESSION['file'];
                $filename = explode("/",$file);
                //temparary downloading method//
                if( file_exists($file) )
                    header('Location: http://ep-test.edit-place.com/FO/invoice/client/xls/'.$filename[count($filename)-1]);
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
    }//class//
?>
