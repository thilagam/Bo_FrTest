<?php
/**
 * fblikeshare Controller
 *
 * @author
 * @version
 */
 
//require_once 'Zend/Controller/Action.php' ;
require_once('SeotoolController.php');

class FblikeshareController extends SeotoolController
{
	protected $allURLs = array(); // Array @all urls
	public $titleURLs = array(); // Array @title urls
	public $titlefromurl = array(); // Array @title from urls
	public $sitecharset = array(); // Array @site charset urls
    public $domain; // Array @all urls
    public $scrapinfo; // Array @scrap info
    public $limit; // Array @limit
    public $fbtwittercount; // fb twitter count var
	protected $notFoundUrl=array(); // Array @not found Url
	protected $crawl_type=1; // Crawling type/option
	protected $images=array(); // image array
	protected $css_links=array(); // css links array
	protected $js_links=array(); // js links array
    protected $all_file_links=array(); // all file links array
	protected $fbApiUrl; // facebook api url
	
	// Set actions to run when the class is instantiated
	public function init()
    {
        parent::init();
		// Set the maximum execution time of the script to unlimited so that it can grab all the articles if there are a lot of them to scrape
		set_time_limit(0);
        $this->fbApiUrl = FBAPI ; // Facebook api url 
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
	}
	
	//Parse the Original URL content and get ALL URLs in that page
	function parseAndGetURLs($url,$all=1,$recursive=true)
	{
		if($this->isCurl())
		{
			// Consider only if it's a valid url
			if($this->get_httpcode($url)!=404 && !$this->in_array_r($url,$this->notFoundUrl))
            {
			    if($all==3)
                      $this->allURLs[]=$url;
                else  
				    $body=$this->getContentCURL($url); // Get url contents using curl
            }    
		}	
		else
		{
			// Consider only if it's a valid url
			if($this->get_httpcode($url)!=404 && !$this->in_array_r($url,$this->notFoundUrl))
            {
				if($all==3)
                      $this->allURLs[]=$url;
                else  
                    $body=$this->getContent($url); // Parse the HTML information and return the results.
            }    
		}			
		

		if($body)
		{
			// Removing all style and script items from html
			$body = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $body);
			$body = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $body);

			$regex='|<a.*?href="(.*?)"|'; // Reg expression to parse url
			preg_match_all($regex,$body,$parts);
			$links=$parts[1];
			
			if($recursive)
			{
				$parse_url = parse_url($url);
                $hostCond   =    $parse_url['host'] . (!empty($parse_url['path']) ? $parse_url['path'] : '').(!empty($parse_url['query']) ? '?'.$parse_url['query'] : '' ) ;

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
                            
                            if( (($all==2) && strstr($url, $hostCond) && (strstr(ltrim( $parse['path'], '/' ), ltrim($parse_url['path'], '/' )) || (trim($parse_url['path']) == ''))) || ($all!=2) )
                            {
    							if(!$this->in_array_r($url,$this->allURLs))
    							{
    								$this->allURLs[]=$url; // Storing all urls to an array
    								
    								if($this->get_httpcode($url)==404)
    									$this->notFoundUrl[]=$url; // Storing all not found urls to an array
                                    
    								//if all is true then get URL from 
    								if($all==2 && $recursive)
    								{    						
    									$this->allURLs[]=$this->parseAndGetURLs($url,2,true); // Parse the URL and get ALL URLs in that page
    								}
    								else if($all==1)
    								{
    									$this->allURLs[]=$this->parseAndGetURLs($url,1,false); // Parse the URL and get ALL URLs in that page
    								}
    							}
							}
						}	
						else
						{
							// creating full url from schema, host and path names
							if($parse['scheme'])
								$durl=$parse['scheme']."://";
							if($parse['host'])
								$durl.=$parse['host'];
							if($parse['path'])
								$durl.=$parse['path'];
								
							if($this->get_httpcode($durl)==404)
									$this->notFoundUrl[]=$durl; // Storing all not found urls to an array
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
					// creating url using path and query
					if(substr($parse['path'],0,1)=="/")
						$url=$this->domain.$parse['path'];
					else		
						$url=$this->domain."/".$parse['path'];
					if($parse['query'])	
						$url.="?".$parse['query'];

					if($this->get_httpcode($url)==404)
						$this->notFoundUrl[]=$url; // Storing all not found urls to an array
				}
				else
				{
					// creating full url from schema, host and path names
					if($parse['scheme'])
						$url=$parse['scheme']."://";
					if($parse['host'])
						$url.=$parse['host'];
					if($parse['path'])
						$url.=$parse['path'];
					if($this->get_httpcode($url)==404)
							$this->notFoundUrl[]=$url; // Storing all not found urls to an array
				}
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

	// Function to get url contents using php function - file_get_content	
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
			return ($dom->saveXml($body->item(0))) ;
		}	
			
	}
	
	//check URL is broken or not by response header code
	function get_httpcode($url) {
		$headers = get_headers($url, 0);
		// Return http status code
		return substr($headers[0], 9, 3);
	}
	
	// Function parse urls and get fb twitter like/share count
    function fbLikeShareCount($url,$crawl_type=1)
    {
        $this->allURLs = array() ;
        $this->notFoundUrl = array() ;
        $this->all_file_links = array() ;
        
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
        
        $this->notFoundUrl=array_values(array_unique(array_filter($this->notFoundUrl)));
        /* From contructor */
        
        $this->allURLs  =   array_values(array_filter($this->allURLs)) ;
        $this->notFoundUrl  =   array_values(array_filter($this->notFoundUrl)) ;
        $allURLs=$this->allURLs;
        $length=count($allURLs)-count($this->notFoundUrl);

        if($length>0)
        {
            $parsedUrl  =   parse_url($this->domain) ;
            $i=1;
            foreach($allURLs as $key=>$url)
            {
                if(!in_array($url,$this->notFoundUrl)) :
                    $parseUrls[]    =   $url ;
                endif ;
				
				// Limiting fb query results with a max of 200 urls
                if($i%200==0)
                {
                    $xml_file   =   SEO_FB_XML . $parsedUrl['host'] . $i . time() .($this->fbtwittercount ? '.xml' : '.txt') ;
                    $xml_files[]   =   $xml_file ;
                    
                    // Parsing and saving facebook xml data for like, share, comment count
                    $fp = fopen($xml_file, 'w');
                    fwrite($fp, ($this->fbtwittercount ? file_get_contents( $this->fbApiUrl . "('" . implode("','", $parseUrls) . "')" ) : implode('#url#', $parseUrls))) ;
                    fclose($fp);
                    
                    unset($parseUrls);
                }
                $i++;
            }

            $xml_file   =   SEO_FB_XML . $parsedUrl['host'] . $i . time() .($this->fbtwittercount ? '.xml' : '.txt') ;
            $xml_files[]   =   $xml_file ;
            
            // Parsing and saving facebook xml data for like, share, comment count
            $fp = fopen($xml_file, 'w');
            fwrite($fp, ($this->fbtwittercount ? file_get_contents( $this->fbApiUrl . "('" . implode("','", $parseUrls) . "')" ) : implode('#url#', $parseUrls))) ;
            fclose($fp);
        }
		// get all excel data to an array
        return  array( 'xlsArr' => array($parsedUrl['host'] => $this->getXlsArray($xml_files)), 'displayContent' => $this->tableDisplay($xml_files) ) ;

    }

	// Function parse urls and get fb twitter like/share count for a given url
    function fbLikeShareGivenUrlCount($urls)
    {
        foreach ($urls as $url)
        {
            // Set the root domain of the URL to concatinate with URLs later            
            if (preg_match("#https?://#", $url) === 0) {
                $url = 'http://'.$url;
            }
    
            $this->setDomain($url);
            $this->url=$url;
            $this->crawl_type=3;
            $this->allURLs[]    =   $url ; // All urls array
        }
    
        $allURLs=$this->allURLs;
        $length=count($allURLs)-count($this->notFoundUrl);
        //echo 'pre';
        //print_r($allURLs);echo(count($allURLs));exit;
        //echo '<br>allURLs-count = ';echo(count($allURLs));
        if($length>0)
        {
            $i=1;
            foreach($allURLs as $key=>$url)
            {
                if(!in_array($url,$this->notFoundUrl)) :
                    $parseUrls[]    =   trim($url) ;
                endif ;
				
				// Limiting fb query results with a max of 200 urls
                if($i%200==0)
                {
                    $xml_file   =   SEO_FB_XML . "urls_" . $i . time() .($this->fbtwittercount ? '.xml' : '.txt') ;
                    //echo '<br>xml_file'.$i.' = '.$xml_file ;
                    $xml_files[]   =   $xml_file ;
                    
                    // Parsing and saving facebook xml data for like, share, comment count
                    $fp = fopen($xml_file, 'w');
                    fwrite($fp, ($this->fbtwittercount ? file_get_contents( $this->fbApiUrl . "('" . implode("','", $parseUrls) . "')" ) : implode('#url#', $parseUrls))) ;
                    fclose($fp);
                    
                    unset($parseUrls);
                }
                $i++;
            }
            $xml_file   =   SEO_FB_XML . "urls_" . $i . time() .($this->fbtwittercount ? '.xml' : '.txt') ;
            //echo '<br>xml_file = '.$xml_file ;
            $xml_files[]   =   $xml_file ;
            
            // Parsing and saving facebook xml data for like, share, comment count
            $fp = fopen($xml_file, 'w');
            fwrite($fp, ($this->fbtwittercount ? file_get_contents( $this->fbApiUrl . "('" . implode("','", $parseUrls) . "')" ) : implode('#url#', $parseUrls))) ;
            fclose($fp);
        }
        // get all excel data to an array
        return  array( 'xlsArr' => $this->getXlsArray($xml_files), 'displayContent' => $this->tableDisplay($xml_files) ) ;
    }

	function isCurl(){
		return function_exists('curl_version') ;
	}
    
	function encode_items(&$item, $key)
	{
		$item = utf8_encode($item) ;
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


    /**function to create XLS file**/
    function WriteXLS($datas,$file_name)
    {
        error_reporting(1) ;
        // include package
        include SEO_XLS_WRITER_INCLUDE;

        // create empty file        
        $excel = new Spreadsheet_Excel_Writer($file_name);
        
        // create format for header row
        // bold, red with black lower border
        $firstRow =& $excel->addFormat();
        $firstRow->setBold();
        $firstRow->setSize(12);
        $firstRow->setBottom(1);
        $firstRow->setBottomColor('black');
        
        $i=1;
        foreach($datas as $sheetname=>$data)
        {
            $sheet  =   'sheet'.$i ;
            // add worksheet
            $$sheet =& $excel->addWorksheet($sheetname);
            //$sheet->setInputEncoding('ISO-8859-1');
            // add data to worksheet
            $rowCount=0;

			// Header data with/without fbtwittercount/scrapinfo
            $headArr    =   array('URL','Meta title') ;
            if($this->fbtwittercount)
                array_push($headArr,'Share count','Like count','Comment count','Tweets','Total count') ;
            if($this->scrapinfo)
                array_push($headArr,'Scrap info') ;

            array_unshift($data, $headArr) ;

            foreach ($data as $row) {
              foreach ($row as $key => $value) {
                      
                //$value  =  html_entity_decode($value, ENT_QUOTES, "ISO-8859-1") ;
                //$value  =  utf8_decode(($value)) ;
                  
                if($rowCount==0)
                    $$sheet->write($rowCount, $key, $value,$firstRow);
                else
                    $$sheet->write($rowCount, $key, $value);
              }
              $rowCount++;
            }
            $i++ ;
        }
            
        // save file to disk
        $excel->close() ;
    }

	// Function to display all xml data in html(table) format
    function tableDisplay($xml_files)
    {
        $data=SEO_TBL_TG.'
            <thead><th abbr="Word" scope="col">URL</th><th abbr="Word" scope="col">Title</th>' ; // table header with main data names
		
		// Twitter infos header
        if($this->fbtwittercount)
            $data.='<th abbr="Word" scope="col">Share count</th><th abbr="Word" scope="col">Like count</th>
            <th abbr="Word" scope="col">Comment count</th><th abbr="Word" scope="col">Tweets</th>
            <th abbr="Word" scope="col">Total count</th>' ;
		
		// Scraper infos header
        if($this->scrapinfo)
            $data.='<th abbr="Word" scope="col">Scrap info</th>' ;
        
		$data.='</thead><tbody>' ;
        
        // Parsing each xml file to get required fb, twitter and scraper informations
        foreach($xml_files as $xml_file)
        {
            $i=1;
            if($this->fbtwittercount) // preparing results view with twitter count option
            {
                $xml = simplexml_load_file($xml_file) ;
                foreach($xml->link_stat as $link_stat)
                {
                    $tweet = json_decode(file_get_contents(TWITERAPI.$link_stat->url)) ;
                    $title  = $this->convert_smart_quotes($this->cleanString($this->getTitle($link_stat->url))) ; // Remove unwanted quotes from string
        			
                    if($this->getCharset($link_stat->url) == 'iso-8859-1')
                        $title  = utf8_decode(utf8_encode($title)) ;
                    else
                        $title  = utf8_decode($title) ;
                    
                    // keywords from title
        			if($this->scrapinfo)
        			    $keywords_data  = nl2br(utf8_decode($this->getKeywordsTitle($this->convert_smart_quotes($this->getTitle($link_stat->url))))) ;
                    
                    $data.='<tr id="'.$i.'"><td><a href="'.$link_stat->url.'" target="_blank">'.$link_stat->url.'</a></td><td>'.$title.'</td><td>'.$link_stat->share_count.'</td><td>'.$link_stat->like_count.'</td><td>'.$link_stat->comment_count.'</td><td>'.$tweet->count.'</td><td>'.(((string)$link_stat->total_count)+ $tweet->count).'</td>' ;
                    if($this->scrapinfo)
        			    $data.='<td>'.$keywords_data .'</td>' ;
        			$data.='</tr>' ;
                    $i++;
                }
            }
            else
            {
                $links  =   explode('#url#', file_get_contents($xml_file)) ;
                foreach ($links as $link) {
					
					 // Remove unwanted quotes from string
                    $title  = $this->convert_smart_quotes($this->cleanString($this->getTitle($link))) ; // Remove unwanted quotes from string
                    
                    if($this->getCharset($link) == 'iso-8859-1')
                        $title  = utf8_decode(utf8_encode($title)) ;
                    else
                        $title  = utf8_decode($title) ;
                    
                    // keywords from title
                    if($this->scrapinfo)
                        $keywords_data  = nl2br(utf8_decode($this->getKeywordsTitle($this->convert_smart_quotes($this->getTitle($link))))) ;
                    
                    $data.='<tr id="'.$i.'"><td><a href="'.$link.'" target="_blank">'.$link.'</a></td><td>'.$title.'</td>' ;
                    if($this->scrapinfo)
                        $data.='<td>'.$keywords_data .'</td>' ;
                    $data.='</tr>' ;
                    
                    $i++;
                }
            }
        }
        $data.='</tbody>'.SEO_TBL_TG_ ;
        return  $data ;
    }

	// Function to get all excel data to an array
    function getXlsArray($xml_files)
    {
        foreach($xml_files as $xml_file)
        {
            $i=1;

            if($this->fbtwittercount)
            {
                $xml = simplexml_load_file($xml_file) ;
                foreach($xml->link_stat as $link_stat)
                {
                    $tweet = json_decode(file_get_contents(TWITERAPI.$link_stat->url)) ;
                    
                    // Remove unwanted quotes from string
                    $title  = $this->convert_smart_quotes($this->cleanString($this->getTitle((string)$link_stat->url))) ;

                    if($this->getCharset((string)$link_stat->url) == 'iso-8859-1')
                        $title  = utf8_decode(utf8_encode($title)) ;
                    else
                        $title  = utf8_decode($title) ;
					
					// Infos such as url, share count, like count, comment count, twitter count
                    $items  =   array( (string)$link_stat->url, $title, (string)$link_stat->share_count, (string)$link_stat->like_count, (string)$link_stat->comment_count, $tweet->count, (((string)$link_stat->total_count)+ $tweet->count) ) ;
                    
                    if($this->scrapinfo)
                    {
						// keywords from title
                        $keywords_data=utf8_decode($this->getKeywordsTitle($this->getTitle((string)$link_stat->url))) ;
                        array_push( $items, $keywords_data ) ;
                    }
                    
                    $xls[]  =   $items ;
                    $i++;
                }
            }
            else
            {
                $links  =   explode('#url#', file_get_contents($xml_file)) ;
                foreach ($links as $link) {
					
					 // Remove unwanted quotes from string
                    $title  = $this->convert_smart_quotes($this->cleanString($this->getTitle($link))) ;
                    
                    if($this->getCharset($link) == 'iso-8859-1')
                        $title  = utf8_decode(utf8_encode($title)) ;
                    else
                        $title  = utf8_decode($title) ;
                    
                    // fb Url and title 
                    $items  =   array( $link, $title ) ;
                    
                    if($this->scrapinfo)
                    {
						// keywords from title
                        $keywords_data=utf8_decode($this->getKeywordsTitle($this->getTitle($link))) ;
                        array_push( $items, $keywords_data ) ;
                    }                    
                    $xls[]  =   $items ;
                    $i++;
                }
            }
            
        }
        return  $xls ;
    }
    
	/* Function to find  keywords from title info */
	function getKeywordsTitle($title)
	{
		// Extract kws from title
		$keywords=$this->getwordCountWithSort($title);
		$keywords_data='';
		
		if(count($keywords)>0)
		{
			// Each kw with count concantenated with new line
			foreach($keywords as $word=>$count)
			{
				$keywords_data.=$word." - ".$count."\n";
			}
		}
		return $keywords_data;
	}
	
	//count and sort the array
	function getwordCountWithSort($string)
	{
		$words=$this->str_word_count_utf8($string);
		if(count($words)==0)
				$words=$this->str_word_count_utf8(utf8_encode($string));
		
		$words=$this->arrCountValueCI($words);//get count of each word case-insensitive
		
		foreach ($words as $key => $row)
		{
			$final_words[$key] = $row;
		}
		//array_multisort($final_words, SORT_DESC, $words);
		
		return $final_words;
	
	}
	
	//get words from a string
	function str_word_count_utf8($str) 
	{ 		
		preg_match_all("/\\p{L}[\\p{L}\\p{Mn}\\p{Pd}\'\.\\x{2019}]*/u", $str, $matches);		
		//preg_match_all("/\S+/u", $str, $matches);		
		
		return $matches[0];
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

	// facebook twitter tool output message with download link
    function fbLikeShareCountOutput($xlsFile, $table)
    {
		$xlsFileInfo	=	pathinfo($xlsFile) ;
        return '<div class="success">Spreadsheet successfully saved! <a href="?action=download&file='.($xlsFileInfo['filename']).'">Cick here to download</a></div>'.$table ;
    }
    
    // Function to parse title text for a given url
    function getTitle($Url){
        
        if($this->titlefromurl[$Url]) {
            return $this->titlefromurl[$Url] ;
        }        
        $str = file_get_contents($Url) ;

        if(strlen($str)>0){
            preg_match('#<title(.*?)>(.*?)</title>#is',$str,$title);
            $title = array_values(array_filter(array_map('trim', $title))) ;// Regex to parse title text from url
            $r_title    =   $this->cleanString(trim($title[2] ? $title[2] : $title[1])) ; // Remove unwanted quotes from string
            $this->titlefromurl[$Url] = $r_title ;
            return  $r_title ;
        }
    }
    
    // Function to parse charset for a given url
    function getCharset($Url) {
        
            //$urls   =   implode('|', array_unique(array_filter(explode("\n", trim($fbParams['url']))))) ;
        if($this->sitecharset[$Url]) {
            return $this->sitecharset[$Url] ;
        }
        $filecnt = file_get_contents($Url);
        
        if(strlen($filecnt)>0){
            $wcharset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i", $filecnt, $temp) ? strtolower($temp[1]) : "" ;
            $this->sitecharset[$Url] = trim($wcharset) ;
            return trim($wcharset) ;
        }
    }
    
    // Default function/action for fbtwitter tool
    function indexAction(){
        //header('Content-Type: text/html; charset=utf-8');  
        error_reporting(0);
        $fbParams=$this->_request->getParams();
        $this->_view->msg = $fbParams['msg'] ;
        $this->_view->url = $fbParams['url'] ;

		// title scraper option check
        if($fbParams['titlescraper'])
            $this->scrapinfo    =   1 ;
        else
            $this->scrapinfo    =   0 ;
		
		// fb twitter count option check
        if($fbParams['fbtwittercount'])
            $this->fbtwittercount    =   1 ;
        else
            $this->fbtwittercount    =   0 ;
            
        $checked   =   trim($fbParams['crawl_type']) ; // title crawlewr type option
        
        if($checked == 2) {$this->_view->checked2 = 'checked' ;} elseif($checked==1) {$this->_view->checked1 = 'checked' ;}else{$this->_view->checked3 = 'checked' ;}

		// Link to download seo(fb twitter tool) result
        if(isset($_REQUEST['action']) && $_REQUEST['action']=='download' && isset($_REQUEST['file']))
        {
            $this->_redirect(BO_PATH_.'download_seoresult.php?filename='.$fbParams['file'].'&tool=fblikeshare&ext=xls') ;
        }
        elseif($fbParams['url'])
        {
            $url=$fbParams['url'];
            if($fbParams['crawl_type'])
                $crawl_type=$fbParams['crawl_type'];
            else
                $crawl_type=1;
                
            $urls   =   array_unique(array_filter(explode("\n", $url ))) ;
            $table  =   '' ;
            
            //echo '<pre>url-count = ';echo(count($urls));//print_r($urls);
            if($crawl_type == 3) : 
				// Parsing urls to get fb twitter like/share count for a given url
                $return   =   $this->fbLikeShareGivenUrlCount($urls) ;
                $table  .=  $return['displayContent'] ;
                $xlsArr[''] =   $return['xlsArr'] ;
            else :
                foreach($urls as $urls_) :   
					// Parsing urls to get fb twitter like/share count
                    $return   =   $this->fbLikeShareCount(trim($urls_),$crawl_type) ;
                    $table  .=  $return['displayContent'] ;

                    foreach($return['xlsArr'] as $key=>$val) :
                      $xlsArr[$key] =   $val ;
                    endforeach ;
                endforeach ;
            endif ;

            $xlsFile    =   'results_'.time().'.xls' ;
            $this->WriteXLS($xlsArr, SEO_DOWNLOAD_FBLIKESHARE.$xlsFile) ; // Write results to an excel file
            
            // Setting smarty varible for facebook twitter tool output message with download link
            $this->_view->opmsg =   $this->fbLikeShareCountOutput($xlsFile, $table) ;
        }
        // Processes a view script and returns the output.
        $this->render("fblikeshare");
    }     
    
    function torAction(){
        //header('Content-Type: text/html; charset=utf-8');  
        error_reporting(0);
        
        $fbParams=$this->_request->getParams();
        $this->_view->msg = $fbParams['msg'] ;
        $this->_view->url = $fbParams['url'] ;
        
        if($fbParams['crawl_type'])
            $crawl_type=$fbParams['crawl_type'] ;
        else
            $crawl_type=1 ;

		// fb twitter count option check
        if($fbParams['fbtwittercount'])
            $this->fbtwittercount    =   1 ;
        else
            $this->fbtwittercount    =   0 ;
        
        $checked   =   trim($fbParams['crawl_type']) ;
        if($checked == 2) {$this->_view->checked2 = 'checked' ;} elseif($checked==1) {$this->_view->checked1 = 'checked' ;}else{$this->_view->checked3 = 'checked' ;}
        
        // Processes a view script and returns the output.
        $this->render("fblikesharetor");
    }
    
    function torprocessAction(){
        //header('Content-Type: text/html; charset=utf-8');  
        error_reporting(0);

        $fbParams=$this->_request->getParams();

		// Crawling type/option
        if($fbParams['crawl_type'])
            $crawl_type=$fbParams['crawl_type'] ;
        else
            $crawl_type=1 ;
        
		// title scraper option check
        if($fbParams['titlescraper'])
            $titlescraper=1 ;
        else
            $titlescraper=0 ;
        
        // fb twitter count option check
        if($fbParams['fbtwittercount'])
            $fbtwittercount=1 ;
        else
            $fbtwittercount=0 ;
        
        $limit  =   ($titlescraper && $fbParams['kwdensity']) ? $fbParams['limit'] : 0 ;
        
        if(!empty($fbParams['url']))
        {
            require_once SEO_SFTP_FILE; // php - sftp file
            
            $urls = '' ;
            foreach(array_unique(array_filter(explode("\n", trim($fbParams['url'])))) as $val) {
                $val = trim($val) ; if (strpos($val,'http://') === false) $val = 'http://'.$val ;
                $urls .= ('|'.$val) ;
            }
            $urls = ltrim($urls, '|') ;
            $outputfilename =   'results_'.time() ;
            
            if($this->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
                $urls=utf8_decode($urls) ;
                
			// Saving urls in text file
            $fp = fopen(SEO_UPLOAD_FBLIKESHARE . $outputfilename.".txt", 'w') ;
            fwrite($fp, $urls) ;
            fclose($fp) ;

			/**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server) ;
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass)) {
                 throw new Exception('Login Failed') ;
            }
           
            $php_file="fblikesharecount.php"; // php file to process fb like share count from linode using tor
            
            //Path to execute php command
            $file_exec_path=$sftp->exec(SEO_FB_TWITTER_PHP_EXEC) ; //ruby execution path
            $file_upload_path=$sftp->exec(SEO_FB_TWITTER_PHP_UPLOAD) ; // seo input file upload path
            $file_download_path=$sftp->exec(SEO_FB_TWITTER_PHP_DOWNLOAD) ; // output file download path
            $file_exec_path=trim($file_exec_path) ;
            $file_download_path=trim($file_download_path) ; // output file download path

            $sftp->chdir(trim($file_upload_path)); // Enter upload directory in remote server
            
             // Uploading to linode server via sftp
            $sftp->put($outputfilename.".txt", SEO_UPLOAD_FBLIKESHARE . $outputfilename.".txt", NET_SFTP_LOCAL_FILE) ;
            
            // Excecution command for fb twitter like-share count processing
            $cmd    =   "php $php_file $crawl_type $outputfilename $outputfilename $titlescraper $fbtwittercount $limit" ;
            
            // Processing later
            if($fbParams['result_type']==2 && !empty($fbParams['email']))
            {
                $crons = explode("|", file_get_contents(SEO_CRON_FBLIKESHARE));
                $crons = array_filter(array_unique($crons));
                array_push($crons, $cmd.'###'.$fbParams['email'].'###'.$outputfilename.'###NO');
                
                // Saving cron info for fb like share tool details in text file
                $fp = fopen(SEO_CRON_FBLIKESHARE, 'w') ;
                fwrite($fp, implode("|", $crons));
                fclose($fp) ;
                
                // Response for success message
                $response = $this->displaySuccessMsg(SEO_SUCCESS_MSG5);
            }
            // For immediate processing
            elseif($fbParams['result_type']==1) {
				
				// Output by executing sftp command (using php command string)
                $out_put= $sftp->exec("cd $file_exec_path;$cmd;") ;
                $sftp->chdir(trim($file_download_path)) ; // Enter upload directory in remote server
                $sftp->get($outputfilename.".xls", SEO_DOWNLOAD_FBLIKESHARE.$outputfilename.".xls");
    
                if($out_put) :
					// Read excel(xls) file data to an array
                    $xlsArr =   $this->readXLS(SEO_DOWNLOAD_FBLIKESHARE.$outputfilename.".xls") ;
                    
                    foreach($xlsArr as $xls)
                    {
                        $responseData.=$this->showCSV($xls);// Show csv results
                    }
                    // Response for success message (display with download and/or view results)
                    $response = $this->displaySuccessMsg('', BO_PATH . "/download_seoresult.php?filename=".$out_put."&tool=fblikeshare&ext=xls", SEO_DOWN_OP_FILE, '', '', $responseData);
                else :
                    $response = $this->responseMsg(0, 1); // Response message
                endif ;
            }
            
        }else{
            $response = $this->responseMsg(0, 2); // Response message
        }        
        print json_encode($response); // Resonse array encoded in json format
        exit;
    }
    
    // Function to process facebook twitter cron
    function processFbtwitterCronAction()
    {
        //header('Content-Type: text/html; charset=utf-8');  
        //error_reporting(0);

        $fbParams=$this->_request->getParams();
        
        $crons = explode("|", file_get_contents(SEO_CRON_FBLIKESHARE));
        $crons = array_filter(array_unique($crons));
        
        if(sizeof($crons)>0)
        {
            require_once SEO_SFTP_FILE; // php - sftp file

			/**creating ssh component object**/
            $sftp = new Net_SFTP($this->ssh2_server) ;
            if (!$sftp->login($this->ssh2_user_name, $this->ssh2_user_pass))
            {
                 throw new Exception('Login Failed') ;
            }
            //Path to execute php command
            $file_exec_path=$sftp->exec(SEO_FB_TWITTER_PHP_EXEC) ; //ruby execution path
            $file_upload_path=$sftp->exec(SEO_FB_TWITTER_PHP_UPLOAD) ; // seo input file upload path
            $file_download_path=$sftp->exec(SEO_FB_TWITTER_PHP_DOWNLOAD) ; // output file download path
            $file_exec_path=trim($file_exec_path) ;
            $file_download_path=trim($file_download_path) ; // output file download path
        }
        
        // Getting all email and commands to be executed for cron
        foreach ($crons as $cron)
        {
            $cmd_email_xls = explode('###', $cron);
            if(!empty($cmd_email_xls[0]) && strstr($cmd_email_xls[1], '@') && !empty($cmd_email_xls[2]))
            {
                $cmd[] = $cmd_email_xls[0]; $email[] = $cmd_email_xls[1]; $xls[] = $cmd_email_xls[2];
                $mail_send[] = trim($cmd_email_xls[3]);
            }
        }
        
        $cron_update = array();
        
        for ($i=0; $i < sizeof($cmd); $i++)
        {
                //echo $email[$i].'---'.$xls[$i].'--'.$mail_send[$i].'<br>';
            if($mail_send[$i]=='NO')
            {
				// Output by executing sftp command (using php command string)
                $out_put= $sftp->exec("cd $file_exec_path;".$cmd[$i].";") ;
                
                $sftp->chdir(trim($file_download_path)) ; // Enter upload directory in remote server
                $sftp->get($xls[$i].".xls", SEO_DOWNLOAD_FBLIKESHARE.$xls[$i].".xls");
                
                if(file_exists(SEO_DOWNLOAD_FBLIKESHARE.$xls[$i].".xls"))
                    @chmod(SEO_DOWNLOAD_FBLIKESHARE.$xls[$i].".xls", 0777) ;
                
                @chmod(SEO_DOWNLOAD_FBLIKESHARE.$xls[$i].".xls", 0777) ;
                
                // Sending the file processed with cron to user's email 
                if($out_put && file_exists(SEO_DOWNLOAD_FBLIKESHARE.$xls[$i].".xls")) :
    echo '<br>Output -->' . $email[$i] . ', ' . $out_put.' ++ ';
                    $to = $email[$i];
                    $subject = "Fb twitter processed file";
                    
                    $message = "<div style='width: 70%;'><table width='100%' cellspacing=5 cellpadding=5><tr><td>Bonjour,\r\n</td></tr><tr><td>PFA (".$xls[$i].".xls)</td></tr>".
                    "<tr><td>\r\nCordialement,</td></tr>".
                    "<tr><td>\r\nToute l'équipe d'Edit-place</td></tr></table></div>";
                    
                    $message=str_replace("é",'&eacute;',$message);
        
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: support@edit-place.com" . "\r\n";
                    //exit(SEO_DOWNLOAD_FBLIKESHARE.$xls[$i].".xls");
                    $headers .= $this->prepareAttachment(SEO_DOWNLOAD_FBLIKESHARE.$xls[$i].".xls");
                    //echo SEO_DOWNLOAD_FBLIKESHARE.$xls[$i].".xls".'<br><br>';
                    mail($to,$subject,$message,$headers);
                    //mail("anoopoboulo@gmail.com",$subject,$message,$headers);
                else :
                    //echo 'No op-->' . $out_put.'<br>';
                endif ;
            }
            array_push($cron_update, $cmd[$i].'###'.$email[$i].'###'.$xls[$i].'###YES');
            //array_push($cron_update, $cmd[$i].'###'.$email[$i].'###'.$xls[$i].'###NO');
        }
        
        // Saving cron info for fb like share tool details in text file
        $fp = fopen(SEO_CRON_FBLIKESHARE, 'w') ;
        fwrite($fp, implode("|", $cron_update));
        fclose($fp) ;
    }
    
    function prepareAttachment($path) {
        $rn = "\r\n";

        if (file_exists($path)) {
            //$finfo = finfo_open(FILEINFO_MIME_TYPE);
            //$ftype = finfo_file($finfo, $path);
            $file = fopen($path, "r");
            $attachment = fread($file, filesize($path));
            $attachment = chunk_split(base64_encode($attachment));
            fclose($file);
//exit(basename($path));
            
            /*
            //$msg = 'Content-Type: \'' . $ftype . '\'; name="' . basename($path) . '"' . $rn;
            $msg = 'Content-Type:  application/xls; name="' . basename($path) . '"' . $rn;
            $msg .= "Content-Transfer-Encoding: base64" . $rn;
            $msg .= 'Content-ID: <' . basename($path) . '>' . $rn;
//            $msg .= 'X-Attachment-Id: ebf7a33f5a2ffca7_0.1' . $rn;
            $msg .= $rn . $attachment . $rn . $rn;
            */
            
            $header .= "Content-Type: application/octet-stream; name=\"".basename($path)."\"\r\n"; // use different content types here
            $header .= "Content-Transfer-Encoding: base64\r\n";
            $header .= "Content-Disposition: attachment; filename=\"".basename($path)."\"\r\n\r\n";
            $header .= $attachment."\r\n\r\n";
            return $header;
        } else {
            return false;
        }
    }
    
    /**function to read XLS file and return as array**/
    function readXLS($file)
    {
        require_once SEO_XLS_READER; // SpreadSheet reader file from PEAR
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('Windows-1252');
        $data->read($file);
        $sheets=sizeof($data->sheets) ;

        for($i=0;$i<$sheets;$i++)
        {
            if($data->sheets[$i]['numRows'])    
            {
                $x=1;
                while($x<=$data->sheets[$i]['numRows']) {
                    
                    $y=1;
                    while($y<=$data->sheets[$i]['numCols']) {
                    
                        $xls_array[$i][$x][$y]  =   isset($data->sheets[0]['cells'][$x][$y]) ? iconv("ISO-8859-1","UTF-8",$data->sheets[$i]['cells'][$x][$y]) : '';
                        if($this->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
                            $xls_array[$i][$x][$y]   =   ($xls_array[$i][$x][$y]) ;
                        $y++;
                    }
                    $x++;
                }
            }
        }
        return  $xls_array ;
    }

	/* Function to remove unwanted quotes from string */
    function cleanString($string) {
        
        $find[] = '“';  // left side double smart quote
        $find[] = '”';  // right side double smart quote
        $find[] = "‘";  // left side single smart quote
        $find[] = "’";  // right side single smart quote
        $find[] = '…';  // elipsis
        $find[] = '—';  // em dash
        $find[] = '–';
        
        $replace[] = '"';
        $replace[] = '"';
        $replace[] = "'";
        $replace[] = "'";
        $replace[] = '...';
        $replace[] = '-';
        $replace[] = '-';
        
        return str_replace($find, $replace, $string);
    }

	// Smart quotes conversion function
    function convert_smart_quotes($string)
    { 
        $search = array(chr(145), 
                        chr(146), 
                        chr(147), 
                        chr(148), 
                        chr(151),
                        chr(230),
                        chr(156));
        $replace = array("'", 
                         "'", 
                         '"', 
                         '"', 
                         '-',
                         'ae',
                         'oe');
        return str_replace($search, $replace, $string);
    }
    
    /*function showCSV($data)
    {
        $table=SEO_TBL_TG;
        $i=0;
        foreach($data as $row)
        {
            $table.='<tr>';
            foreach($row as $td)
            {
                if($i==0) {
                    $table.='<th>'.utf8_encode($td).'</th>';
                }else{
                    if($this->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')    
                        $table.='<td>'.html_entity_decode($td, ENT_NOQUOTES, "ISO-8859-1").'</td>';
                    else
                        $table.='<td>'.$td.'</td>';
                }
            }   
            $table.='</tr>';
            $i++;
        }
        $table.=SEO_TBL_TG_;
        return $table;
    }*/
    
    // Show csv results
    function showCSV($data)
    {
        $table=SEO_TBL_TG;
        $i=0;
        foreach($data as $row)
        {
            $table.='<tr>';
            foreach($row as $td)
            {
                if($i==0) {
                    $table.='<th>'.utf8_encode($td).'</th>';
                }else{
                    //$td = str_replace('–', '-', $td) ;
                    //$td = isset($td) ? ((mb_detect_encoding($td) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $td) : $td) : '';
                    /*if(strlen($td)>strlen(utf8_decode($td)))
                        $td = isset($td) ? html_entity_decode($td,ENT_QUOTES,"UTF-8") : '';
                    else
                        $td = isset($td) ? utf8_encode($td) : '';*/
                    //$td = utf8_encode($td);
                    $table.='<td>'.$td.'</td>';
                }
            }   
            $table.='</tr>';
            $i++;
        }
        $table.=SEO_TBL_TG_;
        return $table;
    }

	// Response messages function
    function responseMsg($type, $code, $word_type=0, $msg='', $data=0) {

        $response['type'] = (!empty($type) ? ($type ? 'success' : 'error') : '');
        if($word_type)
            $response['word_type'] = $word_type;
        if($data)
            $response['data'] = $data;
            
		// Error message based on error code
        switch($code) {
            case 1 :
                $response['message'] = 'Sorry, Some error occured.';
                break;
            case 2 :
                $response['message'] = 'Please enter URL(s).';
                break;
            default :
                $response['message'] = $msg;
                break;
        }
        return $response ;
    }

	// Response for success message (display with download and/or view results)
    function displaySuccessMsg($msg, $downUrl, $downLabel, $viewUrl='', $viewLabel='', $data='') {
        $response['type'] = 'success';
        if($data)
            $response['data'] = $data;
        $response['message'] = "";
        if($msg)
            $response['message'] = $msg . "<br>";
        if($downUrl)
            $response['message'] .= "<a href=\"" . $downUrl . "\">" . $downLabel . "</a>";
        if($viewUrl)
            $response['message'] .= " / <span onclick=\"window.open('" . BO_DOMAIN_ . "seotool/".$viewUrl."', '_blank');\">" . $viewLabel . '</span>';
        
        return $response ;
    }
}

?>
