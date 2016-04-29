<?php
class Ep_Scraper_Broken {
	
	protected $allURLs = array();	
	protected $domain;	
	protected $notFoundUrl=array();	
	protected $crawl_type=1;	
	protected $images=array();
	protected $css_links=array();
	protected $js_links=array();
	protected $all_file_links=array();
	protected $result;
    protected $message;

	// Set actions to run when the class is instantiated
	function __construct($url,$crawl_type=1){
		
		// Set the maximum execution time of the script to unlimited so that it can grab all the articles if there are a lot of them to scrape
		set_time_limit(0);
		
		// Set the root domain of the URL to concatinate with URLs later
			
		if (preg_match("#https?://#", $url) === 0) {
			$url = 'http://'.$url;
		}
		
		$this->setDomain($url);
		$this->url=$url;	
		$this->crawl_type=$crawl_type;					
		
		//Parse and get all URLs from the page.		
		$this->parseAndGetURLs($url,$this->crawl_type);	
		
		//all images,css and js files
		$this->images=array_values(array_unique(array_filter($this->images)));
		$this->css_links=array_values(array_unique(array_filter($this->css_links)));
		$this->js_links=array_values(array_unique(array_filter($this->js_links)));
		
		//merging all file links in to single Array
			$this->all_file_links=array_merge($this->images,$this->css_links,$this->js_links);
		
		//Check Broken links for images,css and js files
		if(count($this->all_file_links)>0)
		$this->checkBrokenFiles();
		
		
		//echo "<pre>";print_r($this->images);exit;
		//echo "<pre>";print_r($this->css_links);exit;
		//echo "<pre>";print_r($this->js_links);exit;
		//echo "<pre>";print_r($this->all_file_links);
		//exit;
		
		$this->notFoundUrl=array_values(array_unique(array_filter($this->notFoundUrl)));
		
		//echo "<pre>";print_r($this->notFoundUrl);
		//exit;
		
		//create XLS and Display Data
		$this->WriteXLS();
		
		$result=$this->displayBrokenURL($this->url);
		$this->result.=$result;
		$this->result.=$this->displayALLURL();
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
		$this->domain = $scheme.$domain;		
		//echo $domain."--".$scheme;
	}
	
	//Parse the Original URL content and get ALL URLs in that page
	function parseAndGetURLs($url,$all=1,$recursive=true)
	{
		if($this->isCurl())
		{
			if($this->get_httpcode($url)!=404 && !$this->in_array_r($url,$this->notFoundUrl))
				$body=$this->getContentCURL($url);
		}	
		else
		{
			if($this->get_httpcode($url)!=404 && !$this->in_array_r($url,$this->notFoundUrl))
				$body=$this->getContent($url);
		}			
						
		if($body)
		{						
			$body = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $body);
			$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);	

			$regex='|<a.*?href="(.*?)"|';
			preg_match_all($regex,$body,$parts);
			$links=$parts[1];
			
			
			if($recursive)
			{
				foreach($links as $link){
					$parse = parse_url($link);
					$parse_domain=parse_url($this->domain);
					if($parse['path'] && $parse['path']!="/" && $parse['scheme']!='mailto' && $parse['scheme']!='callto' && $parse['scheme']!='javascript')
					{			
						if(strstr(str_replace('www','',$parse['host']),str_replace('www','',$parse_domain['host'])) || !$parse['host'])
						{
							if(substr($parse['path'],0,1)=="/")
								$url=$this->domain.$parse['path'];
							else		
								$url=$this->domain."/".$parse['path'];				
							if($parse['query'])	
								$url.="?".$parse['query'];
												
							if(!$this->in_array_r($url,$this->allURLs))
							{
								$this->allURLs[]=$url;
								if($this->get_httpcode($url)==404)
									$this->notFoundUrl[]=$url;
							
								//if all is true then get URL from 
								if($all==2 && $recursive)
								{						
									$this->allURLs[]=$this->parseAndGetURLs($url,2,true);						
									
								}
								else if($all==1)
								{
									$this->allURLs[]=$this->parseAndGetURLs($url,1,false);	
								}
							}	
						}	
						else
						{
							if($parse['scheme'])
								$durl=$parse['scheme']."://";
							if($parse['host'])
								$durl.=$parse['host'];
							if($parse['path'])
								$durl.=$parse['path'];
							if($this->get_httpcode($durl)==404)
									$this->notFoundUrl[]=$durl;
						}
					}	
				}	
			}	
		}			
	}
	//check broken links in js,images and css array
	function checkBrokenFiles()
	{
		$all_file_links=$this->all_file_links;
		foreach($all_file_links as $link){
			$parse = parse_url($link);	
			$parse_domain=parse_url($this->domain);		
			if($parse['path'] && $parse['path']!="/" && $parse['scheme']!='mailto' && $parse['scheme']!='callto' && $parse['scheme']!='javascript')
			{			
				if(strstr(str_replace('www','',$parse['host']),str_replace('www','',$parse_domain['host'])) || !$parse['host'])
				{
					if(substr($parse['path'],0,1)=="/")
						$url=$this->domain.$parse['path'];
					else		
						$url=$this->domain."/".$parse['path'];				
					if($parse['query'])	
						$url.="?".$parse['query'];
					
					if($this->get_httpcode($url)==404)
						$this->notFoundUrl[]=$url;	
				}
				else
				{
					if($parse['scheme'])
						$url=$parse['scheme']."://";
					if($parse['host'])
						$url.=$parse['host'];
					if($parse['path'])
						$url.=$parse['path'];
					if($this->get_httpcode($url)==404)
							$this->notFoundUrl[]=$url;
				}
				
				//echo $url."<br>";
			}	
		}	
		
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
			//echo file_get_html($url)->plaintext; 			
			/* $body=$html->find('body', 0)->innertext;
			$body = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $body);
			$body = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $body);
			$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);			
			return  $body;	 */
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
	
	function displayBrokenURL($url)
	{
		$allURLs=$this->notFoundUrl;
		$length=count($allURLs);
		
		$table='<table class="table table-bordered table-striped" id="smpl_tbl" summary="Borken URLs">
				<caption>
				"'.$length.'" Broken URL(s) Found.</caption>
				<tr>
					<th scope="col" abbr="Word">URL</th>					
				</tr>';
		//for($i=0;$i<$length;$i++)
		if($length>0)
		{
			foreach($allURLs as $key=>$url)
			{			
				$table.='<tr><td><a target="_blank" href="'.$url.'">'.$url.'</a></td></tr>';
			}
		}	
		else
		{
			$table.='<tr><td>No URL Found.</td></tr>';
		}
		$table.='</table>';
		return $table;
	}
	function displayALLURL()
	{
		$allURLs=$this->allURLs;		
		$length=count($allURLs)-count($this->notFoundUrl);		
		
		//for($i=0;$i<$length;$i++)
		if($length>0)
		{
			$table='<table class="table table-bordered table-striped" id="smpl_tbl" summary="Other URLs">
				<caption>
				"'.$length.'" Active URL(s) Found.</caption>
				<tr>
					<th scope="col" abbr="Word">URL</th>					
				</tr>';
				
			foreach($allURLs as $key=>$url)
			{			
				if(!in_array($url,$this->notFoundUrl))
				$table.='<tr><td><a target="_blank" href="'.$url.'">'.$url.'</a></td></tr>';
			}
			$table.='</table>';
		}			
		return $table;
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
		$filename="results_broken_".time();
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
			$sheet =&$excel->addWorksheet('Broken URL(s)');		
			$sheet->setInputEncoding('utf-8');
		  
			$rowCount=2;		  
			$i=0;
			$sheet->write(0, 0, "URL :".$this->url,$header);
			
			$sheet->write(1, 0, "Broken URL",$header);
			
			foreach ($this->notFoundUrl as $key => $value) {				
			$sheet->writeUrl($rowCount,0,$value,'',$cell);
				$rowCount++;
			}		
		
		//exit;
		
		// save file to disk
		if ($excel->close() === true) {
		  //echo 'Spreadsheet successfully saved! <a href="?action=download&file='.$filename.'">Cick here to download</a>';
			$this->message= 'Spreadsheet successfully saved! <a href="/BO/download_seo.php?saction=download&file='.$filename.'&ext=xls">Cick here to download</a>';
            		
		  //header("Location:crawl_word.php?msg=success&file=".$filename);
		} else {
		  //echo 'ERROR: Could not save spreadsheet.';
		  header("Location:/seotool/broken-url?submenuId=ML8-SL10");
		}

	}
	
}	
?>