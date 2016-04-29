<?php
/**
 * Bugs controller Class
 * 
 * Functions related to bugs are present in this controller 
 * 
 * @package    Client Devs
 * @copyright  Edit-place
 * @license    Edit-place
 * @version    1.0
 * @category   Library Class
 * @author 	   Vinayak Kadolkar
 * @email 	   vinayak@edit-place.com
 */
class plagarism {
	
	/* global declarations */
	public  $data;
	public $error_log=array();
	public  $options=array(
					'chunk_size'=>5,
					'highlight'=>true,
					'replacements'=>array('<span class="duplicatedText">','</span>')
				);
	
	/*
	 * Function init
	 * Used to load default values, Checking loginf inforamation and set data for using the conroller 
	 *
	 * @param nil
	 * @return nil
	 * 
	 */
	function plagarism(){
		// parent::init();
	}
	
	
	/*
	 * Function str_match
	 * Used to compare 2 strings 
	 * 
	 * @param string $str1
	 * @param string str2
	 * @param array $options 
	 * @return array $res=array(
	 * 				[0]=> 100%
	 * 				[1]=> text with matched tags <em> like this </em>
	 * 				[2]=> error array 
	 * 			)
	 * 
	 */
	public function str_match($str1,$str2){
		if($str1=='' && $str2==''){
			$error_log="One or both strings empty";
		}
		/* // Explode both strings with space as delimiter  	
		$str1=preg_replace('/\s+/', ' ',$str1);
		$str2=preg_replace('/\s+/', ' ',$str2);
		$str_arr1=explode(" ",$str1);
		$str_arr2=explode(" ",$str2);
		
		//Create Chunks of the exploded array with chunk size
		$chunk_arr1=$this->chunk_it($str_arr1);		
		$chunk_arr2=$this->chunk_it($str_arr2);		
		return($this->chunk_diff($chunk_arr1,$chunk_arr2,$str_arr1)); */
		return($this->get_matching_percentage($str1,$str2,$this->options['chunk_size']));
		
	}
	/*new logic added to calculate the percentage*/
	
	
	function get_matching_percentage($client_text,$competitor_text,$min_sequence_size)
	{
		$client_text=preg_replace('/\s+/', ' ',$client_text);
		$competitor_text=preg_replace('/\s+/', ' ',$competitor_text);

		$client_words = explode(" ",$client_text);
		$no_of_client_text_words = count($client_words);
		$no_of_common_seq_words = 0;
		$i=0;
		$word_matches = '';
		
		while (($i+$min_sequence_size) <= $no_of_client_text_words)
		{	
			$temp_sequence = implode(" ",array_slice($client_words,$i,$min_sequence_size));
			$temp_sequence_size = $min_sequence_size;
			
			if(strpos($competitor_text,$temp_sequence) !== false)
			{	
				while(strpos($competitor_text,$temp_sequence) !== false)
				{				
					if(($i+$temp_sequence_size+1) > $no_of_client_text_words) break;
					
					$temp_sequence = implode(" ",array_slice($client_words,$i,($temp_sequence_size+1)));// client_words[i,temp_sequence_size+1].join(" ")
					$temp_sequence_size += 1;
				}				
				$match_array[]='/'.$temp_sequence.'/';
				$replace_array[]=$this->options['replacements'][0].$temp_sequence.$this->options['replacements'][1];
				$word_matches.=$temp_sequence.' ';
				$i+= $temp_sequence_size;
				$no_of_common_seq_words += $temp_sequence_size;
			
			}
			else{
				$i+= 1;
			}	
		}
		//echo $word_matches."<pre>";print_r($match_array);print_r($replace_array);
		$matched_text=preg_replace($match_array,$replace_array,$client_text);
		return array(($no_of_common_seq_words/$no_of_client_text_words) * 100,$matched_text,$this->error_log);
	}
	
	/*
	 * Function chunk it
	 * Used to create chunks of string with said limit
	 * Gives array of chunk_size arrays
	 * @param array $chunks
	 * @return array $chunk_arr
	 * 
	 */
	public function chunk_it($chunks){
		if(empty($chunks)){
			$error_log="Chunk Empty";
			return false; 
		}	
		$chunk_arr=array();
		$chunk_temp=array();
		
		for($i=0;$i<count($chunks);$i++)
		{
			$chunk_temp[]=$chunks[$i];
			if(count($chunks)!=1)
			{
				for($j=($i+1);$j<count($chunks);$j++)
				{
					$chunk_temp[]=$chunks[$j];
					if(count($chunk_temp)==$this->options['chunk_size']){
						$chunk_arr[]=$chunk_temp;	
						
					}	
				}
			}
			else{
				$chunk_arr[]=$chunk_temp;
			}	

			
			$chunk_temp=array();
		}
		
		/* foreach($chunks as $key =>$value){
			
			$chunk_temp[]=$value;
			//Create chunk if size matches 
			if(count($chunk_temp)==$this->options['chunk_size']){
					$chunk_arr[]=$chunk_temp;
					$chunk_temp=array();
			}			
		} */
		if(!empty($chunk_temp)){
			$chunk_arr[]=$chunk_temp;
		}
		
		return $chunk_arr;
	}
	/*
	 * Function chunk diff
	 * Used to find similer chunks from one array of chunks to other  
	 * Gives percentage match , highlighted string if enabled , error array 
	 * @param array $x array of chunks 1
	 * @param array $y array of chunks 2
	 * @return array $result (percentage , highlighted string , error array )
	 * 
	 */
	function chunk_diff($x,$y){
		$percent=0;
		$final_all='';
		$final_xv=array();
		$final_matched=array();
		if(count($x)>0){
			$chunk_percent=0;
			
			/* Run for 1 set of chunks */
			foreach($x as $xk => $xv){
				$chunk_temppercent=0;
				$matched_chunk=array();
				/* look each in 2nd chunks */
				foreach($y as $yk=>$yv){
					/* get unique values in both array to avoid double check */
					$xv=$result = array_unique($xv);
					$yv=$result = array_unique($yv);
					/* calculate percentage multipler based on size of chunk array */
					$multiplier= (100/count($xv));
					/* get similer matches */
					$temp_arr=array_intersect($xv,$yv);
					/* get the heighest matched chunk with its percentage */
					if($chunk_temppercent<=(count($temp_arr)*$multiplier)){
						$chunk_temppercent=(count($temp_arr)*$multiplier);
						
						$matched_chunk=$temp_arr;
						
					}

				}							
				//echo "<Pre>";print_r($xv);print_r($yv);exit;
				
				/* Check if Highlight option enabled then create highlited string */
				/* if($this->options['highlight']){
						
						foreach($xv as $key => $value){
							
							if(!in_array($value,$matched_chunk)){
								$final_all.=$value." ";
							}else{
								$final_all.=$this->options['replacements'][0].$value.$this->options['replacements'][1]." ";
							}
							
						}
						
					
				} */
				$final_xv=array_merge($final_xv,$xv);
				$final_matched=array_merge($final_matched,$matched_chunk);
				
				$chunk_percent=($chunk_percent+$chunk_temppercent);
			}
			if($this->options['highlight']){
						
				foreach($x as $key => $array){
					
					foreach($array as $k => $value){
					
						if(!in_array($value,$final_matched)){
							$final_all.=$value." ";
						}else{
							$final_all.=$this->options['replacements'][0].$value.$this->options['replacements'][1]." ";
						}
					}						
				}
			}
			//echo "<Pre>";print_r($final_matched);exit;
			/* get Avg of percentage for all chunks */	
			$percent=($chunk_percent/count($x));
			
		}else{
			$error_log[]="Chunks are empty";
		}
		$final_all=str_replace($this->options['replacements'][1]." ".$this->options['replacements'][0],' ',$final_all);
		
		return array($percent,$final_all,$this->error_log);
	}  
	
}
?>
