<?php

$artId=$_REQUEST['article_id'];
$type = $_REQUEST['type'];
$con = mysql_connect("localhost","ep_fr","8tJEzHnFCh9B3VbS");
if (!$con)
{
	die('Could not connect: ' . mysql_error());
}
mysql_select_db("ep_fr", $con);
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

?>