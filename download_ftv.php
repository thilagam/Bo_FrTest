<?php
$file = explode('-',$_REQUEST['ftvfile']);
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
else{
    echo "Sorry. File does not exist";
}
?>
