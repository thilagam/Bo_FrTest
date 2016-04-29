<?php

class Ep_Scraper_Orphan {
	
	protected $allURLs = array();	
	protected $domain;	
	protected $notFoundUrl=array();	
	protected $crawl_type=1;		
	protected $result;
	protected $message;
	protected $urlofurl=array();
	protected $xls_array=array();
	
	// Set actions to run when the class is instantiated
	function __construct($url_array,$crawl_type=1){
		
		// Set the maximum execution time of the script to unlimited so that it can grab all the articles if there are a lot of them to scrape
		set_time_limit(0);		
		// Set the root domain of the URL to concatinate with URLs later			
		if (preg_match("#https?://#", $url) === 0) {
			$url = 'http://'.$url;
		}
		$this->allURLs=$url_array;		
		//Parse and get all URLs from the page.		
		$this->parseAndGetURLs();		
		//check each URL and display with status
		$this->result.=$this->checkAndDisplayStaus();		
		//create XLS 
		$this->WriteXLS();
	}
    
	function getResult()
	{
		return $this->result;
	}
    
	function getMessage()
	{
		return $this->message;
	}
	
	//get domain from given URL
	function setDomain($url)
	{
		$domain=parse_url($url, PHP_URL_HOST);
		$scheme=parse_url($url, PHP_URL_SCHEME);
		if($domain==NULL)
			$domain=$url;
		if($scheme)	
			$scheme=$scheme."://";
		else	
			$scheme="http://";
		$udomain = $scheme.$domain;		
		return $udomain;
		
	}
	
	//Parse the Original URL content and get ALL URLs in that page
	function parseAndGetURLs()
	{
		foreach($this->allURLs as $glink)	
		{	
			$glink=trim($glink);
			$body='';
			if($this->isCurl())
			{
				if($this->get_httpcode($glink)!=404 && !$this->in_array_r($glink,$this->notFoundUrl))
					$body=$this->getContentCURL($glink);
				else	
					$this->notFoundUrl[]=$glink;
			}	
			else
			{
				
				if($this->get_httpcode($glink)!=404 && !$this->in_array_r($glink,$this->notFoundUrl))
					$body=$this->getContent($glink);
				else	
					$this->notFoundUrl[]=$glink;		
			}				
							
			if($body)
			{						
				$body = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $body);
				$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);	

				$links=array();
				$regex='|<a.*?href="(.*?)"|';				
				preg_match_all($regex,$body,$parts);
				$links=$parts[1];					
				
				
				foreach($links as $link){
					$parse = parse_url($link);	
					$gdomain=$this->setDomain($glink);
					$parse_domain=parse_url($gdomain);						
					if($parse['path'] && $parse['path']!="/" && $parse['scheme']!='mailto' && $parse['scheme']!='callto' && $parse['scheme']!='javascript')
					{	if(strstr(str_replace('www','',$parse['host']),str_replace('www','',$parse_domain['host'])) || !$parse['host'])
						{
							if(substr($parse['path'],0,1)=="/")
								$url=$gdomain.$parse['path'];
							else		
								$url=$gdomain."/".$parse['path'];
							if($parse['query'])	
								$url.="?".$parse['query'];

							if($glink!==$url)
							$this->urlofurl[$glink][]=$url;
						}
						else
						{
							if($parse['scheme'])
								$durl=$parse['scheme']."://";
							if($parse['host'])
								$durl.=$parse['host'];
							if($parse['path'])
								$durl.=$parse['path'];

							if($glink!==$url)
							$this->urlofurl[$glink][]=$durl;
						}						
						$this->urlofurl[$glink]=array_values(array_unique(array_filter($this->urlofurl[$glink])));
					}	
				}
			}		
		}		
	}

	//check checkAndDisplayStaus
	function checkAndDisplayStaus()
	{
		$this->notFoundUrl=array_values(array_unique(array_filter($this->notFoundUrl)));
		$result = call_user_func_array('array_merge', $this->urlofurl);
		$url_count_array=$this->arrCountValueCI($result);

		$table='<table class="table table-bordered table-striped" id="smpl_tbl">
				<tr>
					<th scope="col" abbr="URL">URL</th>
					<th scope="col" abbr="Status">Status</th>
					<th scope="col" abbr="Internal URL Count">Internal URL Count</th>
					<th scope="col" abbr="External URL Count">External URL Count</th>
				</tr>';
		//for($i=0;$i<$length;$i++)
		$this->xls_array[0][0]="URL";
		$this->xls_array[0][1]="Status";
		$this->xls_array[0][2]="Internal URL Count";
		$this->xls_array[0][3]="External URL Count";

		foreach($this->allURLs as $key=>$url)
		{			
			$url=trim($url);
			$internal_count=$url_count_array[$url]?$url_count_array[$url]:0;
			$external_count=count($this->urlofurl[$url]);
			
			$status='';
			if(in_array($url,$this->notFoundUrl))
				$status='Broken';
			else if($internal_count == 0)	
				$status='Orphan';
			else
				$status='Active';

			$xls_index=$key+1;
			$this->xls_array[$xls_index][0]=$url;
			$this->xls_array[$xls_index][1]=$status;
			$this->xls_array[$xls_index][2]=$internal_count;
			$this->xls_array[$xls_index][3]=$external_count;
			
			
			$table.='<tr>
						<td><a href="'.$url.'">'.$url.'</a></td>
						<td>'.$status.'</td>
						<td>'.$internal_count.'</td>
						<td>'.$external_count.'</td>
						</tr>';
		}
		$table.='</table>';
		//echo "<pre>";print_r($this->xls_array);
		//echo $table;exit;
		return $table;	
		
	}
	//get count of each URL case-insensitive
	function arrCountValueCI($array) {
			$countArr = array();
			foreach($array as $value) {
				foreach($countArr as $key2 => $value2) {
					if(strtolower($key2) == strtolower($value)) {
						$countArr[$key2]++;
						continue 2;
					}
				}
				$countArr[$value] = 1;
			}
			return $countArr;
	}
	 
	//get COntent with CURL;
	function getContentCURL($url){				
		// Instantiate cURL to grab the HTML page.
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_HEADER, false);
		curl_setopt($c, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($c, CURLOPT_FAILONERROR, true);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($c, CURLOPT_AUTOREFERER, true);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_TIMEOUT, 0);
		// Add curl_setopt here to grab a proxy from your proxy list so that you don't get 403 errors from your IP being banned by the site
		
		// Grab the data.
		$html = curl_exec($c);
		
			
		// Check if the HTML didn't load right, if it didn't - report an error
		if (!$html) {
			echo "<p>cURL error number: " .curl_errno($c) . " on URL: " . $url ."</p>" .
				 "<p>cURL error: " . curl_error($c) . "</p>";
			$this->notFoundUrl[]=$url;
			return false;	
		}
		
		
		// Close connection.
		curl_close($c);
		// Parse the HTML information and return the results.
		 $dom = new DOMDocument(); 
		 libxml_use_internal_errors(true);
		 @$dom->loadHtml($html);			
		
		
		// To hold all image links...
		$image_links = array();
		// Get all images
		$imgs = $dom->getElementsByTagName("img");
		foreach($imgs as $img) {
		  // Check the src attr of each img
		  $src = "";
		  $src = $img->getAttribute("src");
		  // Add it onto your $links array.
		  $image_links[] = $src;
		}
		$this->images=$image_links;
		
		
		// To hold all css links...
		$css_links = array();
		//css files
		$link_tags = $dom->getElementsByTagName('link');
		foreach($link_tags as $link_tag)
		{
		  if($link_tag->getAttribute("rel")=='stylesheet')
		  {
			$href = "";		 
			$href = $link_tag->getAttribute("href");
			// Add it onto your $links array.
			$css_links[] = $href;
		  }		
		}
		$this->css_links=$css_links;
		
		
		// To hold all js links...
		$js_links = array();
		//js files
		$js_tags = $dom->getElementsByTagName('script');
		foreach($js_tags as $js_tag)
		{
		  if($src = $js_tag->getAttribute("src"))
		  {
			// Add it onto your $links array.
			$js_links[] = $src;
		  }		
		}
		$this->js_links=$js_links;
		
		
		$xpath = new DOMXPath($dom);
		$body = $xpath->query('/html/body');		
		return ($dom->saveXml($body->item(0)));
			
	}	
	function getContent($url){				
			
		//$html = file_get_html($url);
		$html = file_get_contents($url);
		// Parse the HTML information and return the results.		
		if (!$html) {
			$this->notFoundUrl[]=$url;
			return false;		
		}
		else
		{			
			$dom = new DOMDocument();
			@$dom->loadHtml($html);			
			 
			// Get all images
			$imgs = $dom->getElementsByTagName("img");
			foreach($imgs as $img) {
			  // Check the src attr of each img
			  $src = "";
			  $src = $img->getAttribute("src");
			  // Add it onto your $links array.
			  $image_links[] = $src;
			}
			$this->images=$image_links;
            
			// To hold all your links...
			$css_links = array();
			//css files
			$link_tags = $dom->getElementsByTagName('link');
			foreach($link_tags as $link_tag)
			{
			  if($link_tag->getAttribute("rel")=='stylesheet')
			  {
				$href = "";		 
				$href = $link_tag->getAttribute("href");
				// Add it onto your $links array.
				$css_links[] = $href;
			  }		
			}
			$this->css_links=$css_links;
			
			
			while (($r = $dom->getElementsByTagName("script")) && $r->length) {
				$r->item(0)->parentNode->removeChild($r->item(0));
			}
			
			$xpath = new DOMXPath($dom);
			$body = $xpath->query('/html/body');
			return ($dom->saveXml($body->item(0)));
		}
	}
	//check URL is broken or not by response header code
	function get_httpcode($url) {
		$headers = get_headers($url, 0);
		// Return http status code
		return substr($headers[0], 9, 3);
	}

	function isCurl(){
		return function_exists('curl_version');
	}
	function encode_items(&$item, $key)
	{
		$item = utf8_encode($item);
	}
	
	//in_arry for two dimensional array
	function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
		return false;
	}
	/**Create XLS File**/

	function WriteXLS()
	{
		// include package
		include 'Spreadsheet/Excel/Writer.php';

		// create empty file		
		$filename="results_orphan_".time();
		$excel = new Spreadsheet_Excel_Writer("/home/sites/site4/web/BO/seo_download/scraper/".$filename.".xls");
		$excel->setVersion(8);			
		
		// create format for header row
		// bold, red with black lower border
		$header_f=array(
				'bold'=>'1',
				'size' => '10',
				'FgColor'=>'yellow',
				'color'=>'black',
				'border'=>'1',
				'align' => 'center'); 
		$header =& $excel->addFormat($header_f);
		$cell_f=array(
				'color'=>'black',
				'border'=>'0',
				'align' => 'left'); 
		$cell =& $excel->addFormat($cell_f);

			  			
		// add worksheet
		$sheet =&$excel->addWorksheet('URL Status');
		$sheet->setInputEncoding('utf-8');
		
		$rowCount=0;
		foreach ($this->xls_array as $row) {
			foreach ($row as $key => $value) {
				if($rowCount==0)
					$sheet->write($rowCount, $key, $value,$header);
				else if($key==0 && $rowCount >0)	
					$sheet->writeUrl($rowCount,$key,$value,'',$cell);
				else
					$sheet->write($rowCount, $key, $value,$cell);
		  }
		  $rowCount++;
		}
		
		// save file to disk
		if ($excel->close() === true) {
		  //echo 'Spreadsheet successfully saved! <a href="?action=download&file='.$filename.'">Cick here to download</a>';
			$this->message= 'Spreadsheet successfully saved! <a href="/BO/download_seo.php?saction=download&file='.$filename.'&ext=xls">Cick here to download</a>';
			
		  //header("Location:crawl_word.php?msg=success&file=".$filename);
		} else {
		  $this->message='ERROR: Could not save spreadsheet.';
		}

	}
	
}