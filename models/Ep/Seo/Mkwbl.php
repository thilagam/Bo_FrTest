<?php

/**
 * Multi Key Words Black list Model
 * This Model  is responsible for Db operations related to seo multiple keywods template table
 * @author Vinayak
 * @editor Vinayak
 * @version 1.0
 */
class EP_Seo_Mkwbl extends Ep_Db_Identifier
{
	protected $_name = 'mkbl_templates';



    public function insertTemplate($data){
		
         $template['title'] = $data['title'] ;
         $template['template_data'] = $data['template'];
         $template['client'] = $data['client'];
         $template['created_by'] = $data['created_by'];
         return $this->insertQuery($template);
	}

	public function loadTemplates($fields,$client=''){
			$qry="SELECT ";
			if(!empty($conditions)){
				foreach($fields as $key => $value){
					$qry.=$value.",";
				}
				$qry=rtrim($qry,",");
			}else{
				$qry.="* ";
			}
			$qry.= "FROM mkbl_templates ";
			if($client!=''){
				$qry.="WHERE `client` = '".$client."' AND status = 1 ";
			}else{
				$qry.="WHERE status = 1";
			}
			
			//echo $qry;
			return $this->getQuery($qry) ;
	}

	public function getTemplate($template,$client){
			if($template!='' && $client!=''){

			$qry="SELECT *
				  FROM mkbl_templates
				  WHERE `client` = '".$client."'
				  AND id = '".$template."' 
				  AND status = 1 ";
			//echo $qry;
			return $this->getQuery($qry) ;
			}
	}
}
?>
