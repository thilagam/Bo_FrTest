<?php

//TODO:write the file with cache header. 
//TODO:case sensitive issue

$cd = dirname($_SERVER['SCRIPT_NAME'])."/resources";

$type=$_GET["type"];

if($type=="emptyhtml")
{
	header("Last-Modified: " . gmdate('D, d M Y H:i:s', time()) . 'GMT');
	header("Expires: " . gmdate('D, d M Y H:i:s', time() + 2592000) . 'GMT');
	header("Content-Type: text/html");
	echo("<html><head></head><body></body></html>");
	exit(200);
}
else if($type=="script")
{
	//header("Content-Type: application/oct-stream");
	
	header("Content-Type: application/oct-stream");
	header("Last-Modified: " . gmdate('D, d M Y H:i:s', time()) . 'GMT');
	header("Expires: " . gmdate('D, d M Y H:i:s', time() + 2592000) . 'GMT');
	//header("Cache-Control: must-revalidate, max-age=176400");
	header("Cache-Control: public, max-age=176400");
	/*
	header("Content-Encoding: gzip");
	print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
	echo $_SERVER['SCRIPT_FILENAME'];
	ob_start();
	*/
	?>
	
	if(!window.CuteWebUI_AjaxUploader_OnPostback)
	window.CuteWebUI_AjaxUploader_OnPostback=function()
	{
		var uploader=this;
		for(var e=uploader;e!=null;e=e.parentNode)
		{
			if(e.nodeName=="FORM")
			{
				e.submit();
				return;
			}
		}
	}
	
	<?php
	readfile(dirname($_SERVER['SCRIPT_FILENAME'])."/resources/uploader.js");
	/*
	$js_content = ob_get_contents();
	ob_end_clean();
	echo gzcompress($js_content, 9);
	*/
	//echo $js_content;
}
else if($type=="license")
{
	header("Content-Type: application/oct-stream"); 
	$licensefile=dirname($_SERVER['SCRIPT_FILENAME'])."/license/phpuploader.lic";
	$size=filesize($licensefile);
	$handle=fopen($licensefile,"r");
	$data=fread($handle,$size);
	fclose($handle);
	echo(bin2hex($data));
}
else if($type=="serverip")
{
	$ip=@$_SERVER['SER'.'VER_AD'.'DR'];
    if($ip==null)$ip=@$_SERVER['LOC'.'AL_AD'.'DR'];
    header("Content-Type: text/plain"); 
    echo($ip);
}
else
{
	$file=$_GET["file"];
	$lower=strtolower($file);
	if($lower=="silverlight.xap")
	{
		//the server may do not understand the xap file
		//show just render it to client directly.
		header("Content-Type: application/oct-stream");
		readfile(dirname($_SERVER['SCRIPT_FILENAME'])."/resources/silverlight.xap");
	}
	else if($lower=="continuous.gif"||$lower=="blocks.gif")
	{
		header("Last-Modified: " . gmdate('D, d M Y H:i:s', time()) . 'GMT');
		header("Expires: " . gmdate('D, d M Y H:i:s', time() + 2592000) . 'GMT');
		header("Cache-Control: public");
		header("Content-Type: image/gif");
		readfile(dirname($_SERVER['SCRIPT_FILENAME'])."/resources/$file");
	}
	else
	{
		header("Cache-Control: public");
		header("Content-Type: application/oct-stream"); 
		header("Location: $cd/$file");
	}
}

exit(200);

?>
