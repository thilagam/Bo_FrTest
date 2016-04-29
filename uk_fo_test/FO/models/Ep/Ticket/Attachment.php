<?php
/**
  * User: Arun
 * Date: 7/18/11
 * Time: 12:42 PM
 */
 
Class Ep_Ticket_Attachment
{
    public function uploadAttachment($path,$files,$filename)
    {
        $upload_attachment_dir=$path;
        if(!is_dir($upload_attachment_dir))
			mkdir($upload_attachment_dir,TRUE);
    		chmod($upload_attachment_dir,0777);
        $attachment=$filename;

		//echo  $upload_attachment_dir.$attachment;exit;
        if (move_uploaded_file($files['tmp_name'], $upload_attachment_dir.$attachment))
		{
            chmod($upload_attachment_dir.$attachment,0777);
            //echo  $upload_attachment_dir.$attachment."--";exit;
        }
    }
    function downloadAttachment( $fullPath ,$display,$FileName=NULL)
    {
        //echo $display;exit;
        // Must be fresh start
        if( headers_sent() )
        die('Headers Sent');

        // Required for some browsers
        if(ini_get('zlib.output_compression'))
        ini_set('zlib.output_compression', 'Off');

        // File Exists?
        if( file_exists($fullPath))
        {

            // Parse Info / Get Extension
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            //print_r($path_parts);exit;
            if(!$FileName)
                $FileName=basename($fullPath);

            // Determine Content Type
            switch ($ext) {
              case "pdf": $ctype="application/pdf"; break;
              case "exe": $ctype="application/octet-stream"; break;
              case "rar":
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
            header("Content-Disposition: $dtype; filename=\"".$FileName."\";" );
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".$fsize);
            ob_clean();
            flush();
            readfile( $fullPath );
        } else
        die('File Not Found');

    }

}