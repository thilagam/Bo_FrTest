<?php
/**
 * Ep_Scraper_Scraper
 * @author Admin
 * @package Message
 * @version 1.0
 */
class Ep_Scraper_Scraper {
	protected $allSiteData = array();
	protected $allURLs = array();	
	protected $domain;
	protected $siteURLData=array();
	protected $black_list_array=array();
	protected $black_list_words=array();
	protected $notFoundUrl=array();	
	protected $crawl_type=1;	
	protected $result;
	
	// Set actions to run when the class is instantiated
	function __construct($url,$crawl_type=1){
		
		// Set the maximum execution time of the script to unlimited so that it can grab all the articles if there are a lot of them to scrape
		set_time_limit(0);
		
		// Set the root domain of the URL to concatinate with URLs later
			
		$this->setDomain($url);
		$this->url=$url;	
		$this->crawl_type=$crawl_type;			
		
		
		//ALL blacklist words form stopwords.txt file
		$blackList=new Ep_Scraper_Blacklist();
		$this->black_list_words=$blackList->getstopwords();
		$this->black_list_words=array_values(array_filter($this->black_list_words));
		array_walk_recursive($this->black_list_words, array($this,"encode_items"));					
		
		
		//Parse and get all URLs from the page.		
		$this->parseAndGetURLs($url,$this->crawl_type);
		
		//remove not found URL from the list
		if(count($this->allURLs)>0)
		{
			foreach($this->allURLs as $key=>$surl)
			{
				if($this->in_array_r($surl,$this->notFoundUrl))
					unset($this->allURLs[$key]);
			}
		}
		$this->allURLs=array_values(array_unique(array_filter($this->allURLs)));
		
		//echo "<pre>";print_r($this->notFoundUrl);
		//echo "<pre>";print_r($this->allURLs);
		//exit;	
		
		
		
		//get all site data and URL data
		$this->allSiteData = call_user_func_array('array_merge', $this->allSiteData);
		$this->allSiteData=$this->getwordCountWithSort($this->allSiteData);		
					
		
		//Filter And Display all site keywords count
		if(is_array($this->allSiteData) && count($this->allSiteData)>0 && $this->crawl_type!=3 )
		{
			//filter all site data
			$this->allSiteData=$this->filerBlackList($this->allSiteData);			
		}
		//Display keywords count by URL
		 if(is_array($this->siteURLData) && count($this->siteURLData)>0)
		{
			foreach($this->siteURLData as $url =>$site_data)
			{
				$this->siteURLData[$url]=$this->filerBlackList($site_data);
			}
			//echo "<pre>";print_r($this->siteURLData);exit;			
			
		}	
		
		//create XLS and Display Data
		$this->WriteXLS();
		if($this->crawl_type!=3)
			$result.=$this->displayALLData($this->url);
		$result.=$this->displayDataByURL();
		
		$this->result.=$result;
		
		
		
	}
	function getResult()
	{
		return $this->result;
	}
	function brokenURLs()
	{
		return $this->notFoundUrl;
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
		//echo $this->domain;
	}
	
	//Parse the Original URL content and get ALL URLs in that page
	function parseAndGetURLs($url,$all=1,$recursive=true)
	{
		if($this->isCurl())
			$body=$this->getContentCURL($url);
		else
			$body=$this->getContent($url);		
				
		$body = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $body);
		$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);
		
		
		
		$regex='|<a.*?href="(.*?)"|';
		preg_match_all($regex,$body,$parts);
		$links=$parts[1];				
		if($body)
		{
			$this->parseAndCount($url,$body);						
			if($recursive && $all!=3 )
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
					}	
				}	
			}	
		}			
	}
	
	//Parese and Count words in data
	function parseAndCount($url,$str)
	{				
		$str=strip_tags($str);
		$str=str_replace("<?php","",$str);
		$str=str_replace("/\$/","",$str);
		$str=preg_replace("/[\]\]\>]/","",$str);
		
		$str1=utf8_decode($str);		
		
		$Allwords=$this->str_word_count_utf8($str1);
		if(count($Allwords)==0)
			$Allwords=$this->str_word_count_utf8($str);		
		
		//echo "<pre>";print_r($Allwords)	;exit;
		
		$this->allSiteData[]=$Allwords;		
		$this->siteURLData[$url]=$this->getwordCountWithSort($Allwords);
	
	}
	//Parse ALL url data
	function parseAllURLData($allUrls)
	{
	
		foreach($allUrls as $url)
		{
			if($this->isCurl())
				$url_data=$this->getContentCURL($url);
			else
				$url_data=$this->getContent($url);
			
			if($url_data)
			{
				$this->parseAndCount($url,$url_data);	
			}	
		}
	
	}
	
	//count and sort the array
	function getwordCountWithSort($array)
	{
		$words=$this->arrCountValueCI($array);
		
		foreach ($words as $key => $row)
		{
			$final_words[$key] = $row;
		}
		array_multisort($final_words, SORT_DESC, $words);
		
		return $final_words;
	
	}

	//get words from a string
	function str_word_count_utf8($str) 
	{ 	
		preg_match_all("/\\p{L}[\\p{L}\\p{Mn}\\p{Pd}'\\x{2019}]*/u", $str, $matches);		
		return $matches[0];
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
			 
			 while (($r = $dom->getElementsByTagName("script")) && $r->length) {
				$r->item(0)->parentNode->removeChild($r->item(0));
			}
			
			$xpath = new DOMXPath($dom);
			$body = $xpath->query('/html/body');		
			return ($dom->saveXml($body->item(0)));
		}	
			
	}
	//get count of each word case-insensitive
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
	function displayALLData($url)
	{
		$allWords=$this->allSiteData;
		$length=count($allWords);
		
		$table.='<table class="table table-bordered table-striped" id="smpl_tbl" summary="Total words in all links of given URL">
				<caption>
				"'.$length.'" Words in all the links of given URL.</caption>
				<tr>
					<th scope="col" abbr="Word">Word</th>
					<th scope="col" abbr="Number of Results">N&deg; Results</th>
				</tr>';
		//for($i=0;$i<$length;$i++)
		foreach($allWords as $key=>$word)
		{			
			$table.='<tr><td>'.utf8_decode($key).'</td><td>'.($word).'</td></tr>';
		}
		$table.='</table>';
		return $table;
	}
	function displayDataByURL()
	{
		$allSites=$this->siteURLData;
		
		
		foreach($allSites as $url=>$words_array)
		{
			$length=count($words_array);
			
			$table.='<table class="table table-bordered table-striped" id="smpl_tbl" summary="Total words in URL">
					<caption>URL:'.$url.'<br>
					"'.$length.'" Total words in the above URL.</caption>
					<tr>
						<th scope="col" abbr="Word">Word</th>
						<th scope="col" abbr="Number of Results">N&deg; Results</th>
					</tr>';
			//for($i=0;$i<$length;$i++)
			foreach($words_array as $key=>$word)
			{			
				$table.='<tr><td>'.utf8_decode($key).'</td><td>'.($word).'</td></tr>';
			}
			$table.='</table>';			
		}	
		return $table;
	}
	
	// remove black list keywords
	public function filerBlackList($array_words)
	{		
		foreach($array_words as $word=>$count)
		{
			//if(in_array(utf8_decode($word),$black_list_words))
			//$chk_word=utf8_decode($word);
			$chk_word=$word;						
			if(preg_grep( "/$chk_word/i" ,$this->black_list_words))
			{
				$this->black_list_array[$word]=$count;
				unset($array_words[$word]);
			}
				
		}
		return $array_words;
		
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
		$filename="results_".time();
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

		if($this->crawl_type!=3)
		{
				  			
			// add worksheet
			$sheet =&$excel->addWorksheet('ALL Data');		
			$sheet->setInputEncoding('utf-8');
		  
			$rowCount=2;		  
			$i=0;
			$sheet->write(0, 0, "URL :".$this->url,$header);
			$sheet->write(0, 1, "",$header);
			$sheet->write(1, 0, "Keyword",$header);
			$sheet->write(1, 1, "Count",$header);
			foreach ($this->allSiteData as $key => $value) {				
			$sheet->write($rowCount,$i,$key,$cell);
			$sheet->write($rowCount,($i+1),$value,$cell);			
			$rowCount++;
			}
			$sheet->setMerge(0, 0, 0, 1);		  
			
		}
		
		
		
		// add each URL data in to a worksheet
		foreach ($this->siteURLData as $url=>$row) {					  			
		  // add worksheet
			$sheet =&$excel->addWorksheet();		
			$sheet->setInputEncoding('utf-8');
		  
		  $rowCount=2;		  
		  $i=0;
		  $sheet->write(0, 0, "URL :".$url,$header);
		  $sheet->write(0, 1, "",$header);
		  $sheet->write(1, 0, "Keyword",$header);
		  $sheet->write(1, 1, "Count",$header);
		  foreach ($row as $key => $value) {				
			$sheet->write($rowCount,$i,$key,$cell);
			$sheet->write($rowCount,($i+1),$value,$cell);
			$rowCount++;
		  }
		$sheet->setMerge(0, 0, 0, 1);
		}
        
		// save file to disk
		if ($excel->close() === true) {
		  //echo 'Spreadsheet successfully saved! <a href="?action=download&file='.$filename.'">Cick here to download</a>';
			$this->result='<div style="" id="seotool-notify" class="control-group formSep"><div class="row-fluid"><div class="span12 form-inline"><div id="seo-message" class="alert alert-success" style="display: block;">';
			$this->result.= 'Spreadsheet successfully saved! <a href="/BO/download_seo.php?saction=download&file='.$filename.'&ext=xls">Cick here to download</a>';
			$this->result.= '</div></div></div></div>';
			return true;
		} else {
		  echo 'ERROR: Could not save spreadsheet.';
		}
	}	
	
}