<?php
$doc2txt = "/usr/bin/antiword  -m UTF-8.txt  ";
//$filein=$_SERVER['DOCUMENT_ROOT']."/FO/test/test.doc";
$fileout="test.txt";
/*$cmd = $doc2txt." ".$filein." > ".$fileout."";
$ret = 0;

if(file_exists($filein))
{
	$text = shell_exec($cmd);
	//$text = utf8_encode($text);
	if( filesize($fileout) == 0 ) :
	chmod($filein,0777);
	$handle = fopen($filein, "rb") ;
	@$handle1 = fopen($fileout, "w+") ;
	$contents = fread($handle, filesize($filein)) ;
	fwrite($handle1, $this->cleanString($contents)) ;
	fclose($handle) ;
	endif ;
	$ret=1;

}
else
{
	$ret = -1;
}*/
$s=file_get_contents($fileout);
//$s=utf8_decode($s);
$s = mb_convert_encoding($s, 'HTML-ENTITIES', "UTF-8");
$s= str_replace('&rsquo;',"'",$s);
$s= str_replace('&ocirc;',"ô",$s);
echo $s;
echo "<br/>";


$s2="chambre d'hôte hotel";
echo $s2;
echo "<br/>";
echo $result = strcasecmp($s, $s2);
//echo $result = strcmp($s, $s2);


if (strpos($s,$s2) == true) {
    echo 'sd';
}

if (strpos($s2,$s) != false) {
    echo 'sds';
}



?>