<?php
session_start();
header("content-type:text/html;charset=utf-8");
include("connect.php");

ini_set('max_input_nesting_level',200);
//unset($_SESSION['carsData']);

$scrape = new Scraper('http://www.autovit.ro/autoturisme/');

class Scraper {
	protected $makers = array();
	protected $models = array();
	protected $carsURL= array();
	protected $carsData= array();
	protected $domain;
	
	// Set actions to run when the class is instantiated
	function __construct($url){
		// Set the maximum execution time of the script to unlimited so that it can grab all the articles if there are a lot of them to scrape
		set_time_limit(0);
		
		// Set the root domain of the URL to concatinate with URLs later
		$this->domain = explode("/", $url);
		$this->domain = 'http://' . $this->domain[2];
		
			
		
		// Pass the page URL you want to start scraping and start scraping through the section pages
		$this->getCarMakersUrls($url);
		
		echo count($this->makers) . ' - total car makers.<br>';
		
	
		//car makers array
		foreach($this->makers as $maker)
		{
			$url=$maker['url'];
			$count=preg_match('/\([0-9]+\)/',$maker['title'],$matches);
			$count=preg_replace('/[\(\)]/','',$matches[0]);
			
			
			$model_name=preg_match('/([a-zA-Z]+)/',$maker['title'],$matches_name);
			$model_name=preg_replace('/[\(\)]/','',$matches_name[0]);
						
			if($count>0)
			{
				
				$m_query="select model_id from autoland_model where lower(model_name)='".strtolower ($model_name)."'";
				
				$model_query=mysql_fetch_array(mysql_query($m_query));
				if($model_query[0])
					$model_id=$model_query[0];
				else	
				{
					$ins_query="INSERT INTO autoland_model(model_name,grp_type) values ('".$model_name."','1')";
					mysql_query($ins_query);
					$model_id=mysql_insert_id();
				}	
				//start scraping to get models of each car maker
					$this->getModelUrls($url,$model_id);
				
			}	
		
		}   
		echo count($this->models) . ' - total models.<br>';
	
		//cars of each car model
		$cnt=0;
		foreach($this->models as $model)
		{
			$url=$model['url'];
			$count=preg_match('/\([0-9]+\)/',$model['title'],$matches);
			$count=preg_replace('/[\(\)]/','',$matches[0]);
			
			$model_nuber_name=preg_match('/([a-zA-Z0-9]+)/',$model['title'],$matches_name);
			$model_nuber_name=$matches_name[0];
			
			if($count>0)
			{
				$m_query="select model_id,model_number_id from autoland_model_number where lower(number)='".strtolower ($model_nuber_name)."'";
				
				$model_query=mysql_fetch_array(mysql_query($m_query));
				if($model_query[0])
				{
					$model_id=$model_query[0];
					$model_number_id=$model_query[1];
				}	
				else	
				{
					$ins_query="INSERT INTO autoland_model_number(model_id,number,grp_type) values (".$model['model_id'].",'".$model_nuber_name."','1')";
					mysql_query($ins_query);
					$model_id=$model['model_id'];
					$model_number_id=mysql_insert_id();
				}
				$this->getCarUrls($url,$model_id,$model_number_id);		
				/* $cnt++;
				if($cnt>2)
					break; */
				
				
				
			}	
		}
				
		echo count($this->carsURL) . ' - total cars.<br>';
		
		//data of each car
		foreach($this->carsURL as $car)
		{
			$url=$car['url'];
			$model_id=$car['model_id'];
			$model_number_id=$car['model_number_id'];
						
			$m_query="select url from autoland_listing where url='{$url}'";
			$model_query=mysql_fetch_array(mysql_query($m_query));
			
			if(!$model_query['url'])
			$this->getCarData($url,$model_id,$model_number_id);
				
		}
			
		echo count($this->carsData) . ' - total cars.<br>';
		
		if($this->carsData)
		{
			$this->insertData($this->carsData);
			
		}
		
		
		
		
		
		// Add function here to start adding items in the article array with articles to a database
	}
	
	// Start Get Car Maker URLS
	private function getCarMakersUrls($url){
		// Instantiate next page variable to check at the end
		$nextPageUrl = NULL;
		
		// Instantiate cURL to grab the HTML page.
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_HEADER, false);
		curl_setopt($c, CURLOPT_USERAGENT, $this->getUserAgent());
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
		}
		
		// Close connection.
		curl_close($c);
		
	
		// Parse the HTML information and return the results.
		$dom = new DOMDocument(); 
		libxml_use_internal_errors(true);
		@$dom->loadHtml($html);
		
		$Selector = "//aside/nav[@class='omCategoryTree']/ul/li";
		
		$xpath = new DOMXPath($dom);
		libxml_use_internal_errors(false);

		
		// Get a list of car makers from the  page
		$carMakersList = $xpath->query($Selector);
		
		
		
		// Add car maker to the makers array
		foreach ($carMakersList as $item){
			$this->makers[] = array(
										'url' => $this->domain . $item->getElementsByTagName('a')->item(0)->getAttribute('href'),
										'title' => $item->getElementsByTagName('a')->item(0)->nodeValue,
										
									);
		}	
			
	}
	
	// End Get Car Makers Urls
	
	// Start Get Car Models URLS of a Car maker
	private function getModelUrls($url,$model_id){
		// Instantiate next page variable to check at the end
		$nextPageUrl = NULL;
		
		// Instantiate cURL to grab the HTML page.
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_HEADER, false);
		curl_setopt($c, CURLOPT_USERAGENT, $this->getUserAgent());
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
		}
		
		// Close connection.
		curl_close($c);
		
	
		// Parse the HTML information and return the results.
		$dom = new DOMDocument(); 
		libxml_use_internal_errors(true);
		@$dom->loadHtml($html);
		
		$Selector = "//aside/nav[@class='omCategoryTree']/ul/li/ul/li";
		
		$xpath = new DOMXPath($dom);
		libxml_use_internal_errors(false);

		
		// Get a list of car models from the  page
		$carMakersList = $xpath->query($Selector);
		
		
		
		// Add model to the model array
		foreach ($carMakersList as $item){
			$this->models[] = array(
										'url' => $this->domain . $item->getElementsByTagName('a')->item(0)->getAttribute('href'),
										'title' => $item->getElementsByTagName('a')->item(0)->nodeValue,
										'model_id'=> $model_id
										
									);
		}				
				
		
	}
	// End Get car model URLS
	
	// Start Get Car URLS of a Car Models
	private function getCarUrls($url,$model_id,$model_number_id){
		// Instantiate next page variable to check at the end
		$nextPageUrl = NULL;
		
		// Instantiate cURL to grab the HTML page.
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_HEADER, false);
		curl_setopt($c, CURLOPT_USERAGENT, $this->getUserAgent());
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
		}
		
		// Close connection.
		curl_close($c);
		
	
		// Parse the HTML information and return the results.
		$dom = new DOMDocument(); 
		libxml_use_internal_errors(true);
		@$dom->loadHtml($html);
		
		$Selector = "//article/h3";
		
		$xpath = new DOMXPath($dom);
		libxml_use_internal_errors(false);

		
		// Get a list of cars from the  page
		$carMakersList = $xpath->query($Selector);
		
		
		
		// Add cars to the carsURL array
		foreach ($carMakersList as $item){
			$this->carsURL[] = array(
										'url' => $this->domain . $item->getElementsByTagName('a')->item(0)->getAttribute('href'),
										'title' => $item->getElementsByTagName('a')->item(0)->nodeValue,
										'model_id'=>$model_id,
										'model_number_id'=>$model_number_id
										
									);
		}				
				
		
		 $pageSelector="//nav/menu[@class='omPager']/span/a";
		$nextPageUrl = $xpath->query($pageSelector);
		$nexturl='';
		foreach ($nextPageUrl as $item){
		
			$nexpage=$item->getElementsByTagName('span')->item(0)->nodeValue;
			if($nexpage=='Inainte')
			{
				foreach ($item->attributes as $attrName => $attrNode) {
                   
                       if('href'==$attrName)
					   {
					   
							$patterns='/[? &]p=[0-9]{1,2}/';
							$url=preg_replace($patterns,'',$url);
							$nexturl=$url.$attrNode->value;
					   }	
					   if($attrName=='class' && $attrNode->value=='hidden')	
						{
							$nexturl='';
						}
					   
							
                    }
			
			}
							
									
		}

		if ($nexturl){
				
				$this->getCarUrls($nexturl,$model_id,$model_number_id);
		} 	
		
	}
	// End Get car model URLS
	// Start Get Car Data of each Car URL
	public function getCarData($url,$model_id,$model_number_id){
		// Instantiate next page variable to check at the end
		$nextPageUrl = NULL;
		
		// Instantiate cURL to grab the HTML page.
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_HEADER, false);
		curl_setopt($c, CURLOPT_USERAGENT, $this->getUserAgent());
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
		}
		
		// Close connection.
		curl_close($c);
		
	
		// Parse the HTML information and return the results.
		$dom = new DOMDocument(); 
		libxml_use_internal_errors(true);
		@$dom->loadHtml($html);
		
		/**Xpath for getting car summary**/
		
		$Selector = "//section[@class='omOfferSummary']/dl";
		$xpath = new DOMXPath($dom);
		libxml_use_internal_errors(false);
		$carinfo=array();
		// Get Car data summary from the  page
		$carData = $xpath->query($Selector);
		
		foreach ($carData as $item){
		
			$carinfo['price']=$item->parentNode->getElementsByTagName('p')->item(0)->getElementsByTagName('span')->item(0)->nodeValue;
			$carinfo['model_id']=$model_id;
			$carinfo['model_number_id']=$model_number_id;
			
			$loopcnt=0;
			foreach($item->getElementsByTagName('dd') as $field)
			{	
				$index=$item->getElementsByTagName('dt')->item($loopcnt)->nodeValue;
				$index=strtolower(preg_replace('/[,:]/','',$index));
				$index=preg_replace('/\s+/','_',$index);
				if($loopcnt==0)
				{
					$carinfo[$index]= $item->getElementsByTagName('dd')->item($loopcnt)->getElementsByTagName('span')->item(0)->nodeValue;//.trim($item->getElementsByTagName('dd')->item($loopcnt)->getElementsByTagName('span')->item(1)->nodeValue);
					//$carinfo[$index]= preg_replace('/[,:]/','',$carinfo[$index]);
					//$carinfo['model']=
				}	
				else	
					$carinfo[$index]=	$item->getElementsByTagName('dd')->item($loopcnt)->getElementsByTagName('span')->item(0)->nodeValue	;
				$loopcnt++;
				
			}
		}	
		
		
		/**Xpath for getting image Paths **/
		$image_Selector = "//section[@class='omOfferInfo']";
		$xpath1 = new DOMXPath($dom);
		libxml_use_internal_errors(false);
		$images=array();
		$phone='';
		
		// Get imagedata data summary from the  page
		$imageData = $xpath1->query($image_Selector);
		foreach ($imageData as $item){
		
			$images=$item->getElementsByTagName('div')->item(0)->getAttribute('data-photos');
			$images=preg_replace('/[\]\[\"]/','',stripslashes($images));
			$images=str_replace('/60x45/','/original/',stripslashes($images));
			$images=explode(",",$images);
		
		}
				
		/**Xpath for getting other info Paths **/
		$otherInfo=array();
		$info_Selector = "//div[@class='omOfferDetails cf']";
		$xpath_info = new DOMXPath($dom);
		libxml_use_internal_errors(false);
		// Get other information
		$info_data = $xpath_info->query($info_Selector);
		
		foreach ($info_data as $item){
		
			$otherInfo[]=$item->getElementsByTagName('section')->item(0)->nodeValue;
			$otherInfo[]=$item->getElementsByTagName('section')->item(1)->nodeValue;
					
		}
		
		/**Xpath for getting phonenumber info **/
		$phone_Selector = "//dl[@class='phone']/dd";
		$xpath_phone = new DOMXPath($dom);
		libxml_use_internal_errors(false);
		// Get other information
		$phone_data = $xpath_info->query($phone_Selector);
		
		foreach ($phone_data as $item){
		
			$phone=$item->getElementsByTagName('span')->item(0)->nodeValue;		
					
		}
		
		
		
		$car_details=$carinfo;
		$car_details['url']=$url;
		$car_details['images']=$images;
		$car_details['phone']=$phone;
		$car_details['description']=$otherInfo;
		
		$this->carsData[]=$car_details;
		//echo "<pre>";print_r($this->carsData);echo "</pre>";
		
	}
	
	// End Get car Data
	
	
	//Insetrting the Car data
	public function insertData($carData)
	{
		foreach($carData as $data)
		{
			$data['model_caroserie']=mysql_escape_string(str_replace(", ",'',$data['model_caroserie']));
			$data['rulaj']=(int)preg_replace('/[a-zA-Z\s ,]/','',$data['rulaj']);
			$data['price']=(float) preg_replace('/[a-zA-Z\s]/','',$data['price']);
			$data['description']=implode("<br>",$data['description']);
			$data['description']=mysql_escape_string(html_entity_decode(htmlentities($data['description'],ENT_COMPAT, 'UTF-8')));
			$data['created_on']=date("Y-m-d h:i:s");
			
			
			//Inserting car listnig in autoland_listing table
			
			$insert_car="INSERT INTO `autoland_listing` ( `webuser_id`, `model_id`, `model_number_id`, `full_title`, `listing_category_id`, `vehicle_category_id`, `year_of_manufacture`, `mileage`, `price_in_EUR`, `vehicle_damaged`, `description`, `country`, `large_image`, `optional_data_table`, `grp_type`, `grp_type_version`, `created_on`, `status`, `view_count`, `is_featured_by_admin`,`url`) VALUES (14, '{$data['model_id']}',{$data['model_number_id']}, '{$data['model_caroserie']}', 1, '20', '{$data['fabricatie']}', {$data['rulaj']}, {$data['price']}, 'no', '{$data['description']}', 'Romania', '', NULL, '1', 0, '{$data['created_on']}', 1, 0, 0,'{$data['url']}')";
			
			//echo $insert_car;exit;
			mysql_query($insert_car);
			$listing_id=mysql_insert_id();
		
			
			foreach($data['images'] as $image)
			{
				$image_thumb=str_replace('/original/','/128x96/',$image);
				
				$pathinfo=pathinfo($image);
				$parse = parse_url($pathinfo['dirname']);
				$root=dirname( __FILE__ );
				$path=$root."/autoland.ro/images/listing-image/";///autoland.ro
				$path_thumb=$root."/autoland.ro/images/listing-image/thumb/";///autoland.ro
				$img_file=$pathinfo['basename'];
					
				if(!is_dir($path))
				{
				   mkdir($path,0777,TRUE);
				}	 
				 chmod($path,0777);
				 if(!is_dir($path_thumb))
				{
				   mkdir($path_thumb,0777,TRUE);
				}	 
				 chmod($path_thumb,0777);
				  
				 
				 $image_file_name=uniqid().".".$pathinfo['extension'];
				 $image_create_name=$path.$image_file_name;
				 $image_create_thumb_name=$path_thumb.$image_file_name;
				
				if(!file_exists($image_create_name))
				{
					$img_content=file_get_contents($image);
					
					if($img_content)
					{
						file_put_contents($image_create_name,$img_content);	
											
						//file_put_contents($image_create_thumb_name,file_get_contents($image_thumb));	
						//insert image listing	
						$insert_image="INSERT INTO `autoland_image_list` (`webuser_id`, `listing_id`, `image`, `status`, `created_on`) VALUES (14, {$listing_id},'{$image_file_name}', 1, '".date("Y-m-d h:i:s")."')";
						mysql_query($insert_image);
						
						chmod($image_create_name,0777);
						//remove and add watermark
						$logo_png=dirname( __FILE__ ).'/logo.png';
						$this->removeWaterMark($image_create_name,$logo_png);
						
					}	
				}				
					
			}
			
			
		}
		
	
	}
	public function removeWaterMark($img_jpg,$logo_png)
	{

		$src = imagecreatefrompng($logo_png);
		$dest = imagecreatefromjpeg($img_jpg);

		list($width, $height) = getimagesize($img_jpg);
		list($newwidth, $newheight) = getimagesize($logo_png);


		imagealphablending($src, false);
		imagesavealpha($src, true);
		// Copy and merge
		imagecopymerge($dest, $src, 20, $height-48, 0, 0,$newwidth, $newheight,100);

		// Output and free from memory
		//header('Content-Type: image/jpeg');
		imagejpeg($dest,$img_jpg,100);

		imagedestroy($dest);
		imagedestroy($src);
	}
	
	
	// Start Get Browser User Agent
	private function getUserAgent(){
		// Set an array with different browser user agents
		 $agents = array(
		 					"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; bgft)",
							"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; GTB5; User-agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; http://bsalsa.com) ; .NET CLR 2.0.50727)",
							"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Tablet PC 2.0)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; Orange 8.0; GTB6.3; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; Embedded Web Browser from: http://bsalsa.com/; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30618; OfficeLiveConnector.1.3; OfficeLivePatch.1.3)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 1.1.4322; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648)",
							"Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.3) Gecko/20100401 Firefox/4.0 (.NET CLR 3.5.30729)",
							"Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2.8) Gecko/20100722 BTRS86393 Firefox/3.6.8 ( .NET CLR 3.5.30729; .NET4.0C)",
							"Mozilla/5.0 (Windows; U; MSIE 9.0; WIndows NT 9.0; en-US)",
							"Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)",
							"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; Media Center PC 6.0; InfoPath.3; MS-RTC LM 8; Zune 4.7)",
							"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 4.0; InfoPath.3; MS-RTC LM 8; .NET4.0C; .NET4.0E)",
							"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
							"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
							"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 2.0.50727; SLCC2; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 4.0; Tablet PC 2.0; InfoPath.3; .NET4.0C; .NET4.0E)",
							"Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 5.1; Trident/5.0)",
							"Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.2; Trident/4.0; Media Center PC 4.0; SLCC1; .NET CLR 3.0.04320)",
							"Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 1.1.4322)",
							"Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727)",
							"Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
							"Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.0; Trident/4.0; InfoPath.1; SV1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 3.0.04506.30)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; Media Center PC 6.0; InfoPath.2; MS-RTC LM 8)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; InfoPath.2)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 3.0)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; msn OptimizedIE8;ZHCN)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8; InfoPath.3; .NET4.0C; .NET4.0E)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8; .NET4.0C; .NET4.0E; Zune 4.7; InfoPath.3)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; Zune 4.0)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; OfficeLiveConnector.1.4; OfficeLivePatch.1.3; yie8)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; OfficeLiveConnector.1.3; OfficeLivePatch.0.0; Zune 3.0; MS-RTC LM 8)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; OfficeLiveConnector.1.3; OfficeLivePatch.0.0; MS-RTC LM 8; Zune 4.0)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; MS-RTC LM 8)",
							"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; FDM; OfficeLiveConnector.1.4; OfficeLivePatch.1.3; .NET CLR 1.1.4322)",
							"Opera/9.99 (Windows NT 5.1; U; pl) Presto/9.9.9",
							"Opera/9.80 (J2ME/MIDP; Opera Mini/5.0 (Windows; U; Windows NT 5.1; en) AppleWebKit/886; U; en) Presto/2.4.15",
							"Opera/9.70 (Linux ppc64 ; U; en) Presto/2.2.1",
							"Opera/9.70 (Linux i686 ; U; zh-cn) Presto/2.2.0",
							"Opera/9.70 (Linux i686 ; U; en-us) Presto/2.2.0",
							"Opera/9.70 (Linux i686 ; U; en) Presto/2.2.1",
							"Opera/9.70 (Linux i686 ; U; en) Presto/2.2.0",
							"Opera/9.70 (Linux i686 ; U; ; en) Presto/2.2.1",
							"Opera/9.70 (Linux i686 ; U; ; en) Presto/2.2.1",
							"Mozilla/5.0 (Linux i686 ; U; en; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 9.70",
							"Mozilla/4.0 (compatible; MSIE 6.0; Linux i686 ; en) Opera 9.70",
							"Opera/9.64(Windows NT 5.1; U; en) Presto/2.1.1",
							"Opera/9.64 (X11; Linux x86_64; U; pl) Presto/2.1.1",
							"Opera/9.64 (X11; Linux x86_64; U; hr) Presto/2.1.1",
							"Opera/9.64 (X11; Linux x86_64; U; en-GB) Presto/2.1.1",
							"Opera/9.64 (X11; Linux x86_64; U; en) Presto/2.1.1",
							"Opera/9.64 (X11; Linux x86_64; U; de) Presto/2.1.1",
							"Opera/9.64 (X11; Linux x86_64; U; cs) Presto/2.1.1",
							"Opera/9.64 (X11; Linux i686; U; tr) Presto/2.1.1",
							"Opera/9.64 (X11; Linux i686; U; sv) Presto/2.1.1",
							"Opera/9.64 (X11; Linux i686; U; pl) Presto/2.1.1",
							"Opera/9.64 (X11; Linux i686; U; nb) Presto/2.1.1",
							"Opera/9.64 (X11; Linux i686; U; Linux Mint; nb) Presto/2.1.1",
							"Opera/9.64 (X11; Linux i686; U; Linux Mint; it) Presto/2.1.1",
							"Opera/9.64 (X11; Linux i686; U; en) Presto/2.1.1",
							"Opera/9.64 (X11; Linux i686; U; de) Presto/2.1.1",
							"Opera/9.64 (X11; Linux i686; U; da) Presto/2.1.1",
							"Opera/9.64 (Windows NT 6.1; U; MRA 5.5 (build 02842); ru) Presto/2.1.1",
							"Opera/9.64 (Windows NT 6.1; U; de) Presto/2.1.1",
							"Opera/9.64 (Windows NT 6.0; U; zh-cn) Presto/2.1.1",
							"Opera/9.64 (Windows NT 6.0; U; pl) Presto/2.1.1",
							"Opera 9.7 (Windows NT 5.2; U; en)",
							"Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-HK) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
							"Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
							"Mozilla/5.0 (Windows; U; Windows NT 6.0; tr-TR) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
							"Mozilla/5.0 (Windows; U; Windows NT 6.0; nb-NO) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
							"Mozilla/5.0 (Windows; U; Windows NT 6.0; fr-FR) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
							"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; zh-cn) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
							"Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_2_1 like Mac OS X; de-de) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5",
							"Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_2_1 like Mac OS X; da-dk) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5",
							"Mozilla/5.0 (iPad; U; CPU OS 4_2_1 like Mac OS X; ja-jp) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5",
							"Mozilla/5.0 (X11; U; Linux x86_64; en-ca) AppleWebKit/531.2+ (KHTML, like Gecko) Version/5.0 Safari/531.2+",
							"Mozilla/5.0 (Windows; U; Windows NT 6.1; ja-JP) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Windows; U; Windows NT 6.1; es-ES) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Windows; U; Windows NT 6.0; ja-JP) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_5_8; ja-jp) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_4_11; fr) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; zh-cn) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; ru-ru) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; ko-kr) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; it-it) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; en-us) AppleWebKit/534.1+ (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; en-au) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; el-gr) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; ca-es) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; zh-tw) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; ja-jp) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; it-it) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; fr-fr) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
							"Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; es-es) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16"
						);
						
		return "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1092.0 Safari/536.6";
	}
	// End Get Browser User Agent
}
// End Scraper Class
?>