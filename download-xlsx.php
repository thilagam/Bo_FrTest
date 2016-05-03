<?php
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
?>