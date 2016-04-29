<?php

/********Begin filter requested data*********/
foreach ($_REQUEST as $key=>$value) 
{
	if(!is_array($value))
	{
		$value = strip_tags($value);
		$value = str_replace("<","",$value);
		$value = str_replace(">","",$value);
		$_REQUEST[$key] = $value;
	}
}
/********End filter requested data*********/

$domainName = array(
"fr"=>"edit-place.oboulo.com"
);
$domainNameSSL = array(
"fr"=>"edit-place.oboulo.com"
);

$_country = "fr";