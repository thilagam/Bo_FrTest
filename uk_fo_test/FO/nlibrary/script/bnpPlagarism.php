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
class bnpPlagarism {
	
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
		/* Explode both strings with space as delimiter */ 	
		$str_arr1=explode(" ",$str1);
		$str_arr2=explode(" ",$str2);
		
		/* Create Chunks of the exploded array with chunk size */
		$chunk_arr1=$this->chunk_it($str_arr1);
		//echo "<pre>";print_r($chunk_arr1);exit;
		$chunk_arr2=$this->chunk_it($str_arr2);
		
		return($this->chunk_diff($chunk_arr1,$chunk_arr2));
		
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
			if(count($chunks)==1)
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
