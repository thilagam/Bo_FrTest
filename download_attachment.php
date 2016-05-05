<?php

$con = mysql_connect("localhost","epweb","293PA3Y4577KjVUM");
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("dev_editplace1", $con);
$result = mysql_query("SELECT attachment from Message where id ='".$_REQUEST['m']."'");
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
?>
