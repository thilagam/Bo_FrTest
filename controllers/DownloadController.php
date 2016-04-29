<?php
	class DownloadController extends Zend_Controller_Action
	{
		function downloadAction()
		{
			$parameters = $this->_request->getParams();
			$attachment=new Ep_Message_Attachment();
			if($parameters['type']=='seo_mission')
			{
				$identifier = $parameters['mission_id'];
				$quoteMission_obj=new Ep_Quote_QuoteMissions();
				$result = $quoteMission_obj->getQuoteMission($identifier);
				$documents_paths = explode("|",$result[0]['documents_path']);
				$documents_names = explode("|",$result[0]['documents_name']);
				
				$attachment->downloadAttachment(APP_PATH_ROOT."/misssionDocuments/".$documents_paths[$parameters['index']],'attachment',$documents_names[$parameters['index']]);
				//$pathinfo = pathinfo(APP_PATH_ROOT."/misssionDocuments/".$documents_paths[$parameters['index']]);
				
				// header("Pragma: public"); // required
				// header("Expires: 0");
				// header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				// header("Cache-Control: private",false); // required for certain browsers
				// header("Content-Type: application/".strtolower($pathinfo['extension']));
				// header("Content-Disposition: attachment; filename=".basename((APP_PATH_ROOT.$this->_config->path->mission_documents.$documents_paths[$parameters['index']])));
				// header("Content-Transfer-Encoding: binary");
				// header("Content-Length: ".filesize((APP_PATH_ROOT.$this->_config->path->mission_documents.$documents_paths[$parameters['index']])));
				// ob_end_flush();
				// readfile($zipname);
			
			}
			die();
		}
		
		/**************************** test ***************************/
		public function downloadfileAction()
		{
			include "/BO/FlxZipArchive.php";
			
			$the_folder = '/BO/folder1';
			$zip_file_name = 'archived_name.zip';


			$download_file= true;
			//$delete_file_after_download= true; doesnt work!!




		$za = new FlxZipArchive;
		$res = $za->open($zip_file_name, ZipArchive::CREATE);
		if($res === TRUE) 
		{
			$za->addDir($the_folder, basename($the_folder));
			$za->close();
		}
		else  { echo 'Could not create a zip archive';}

		if ($download_file)
		{
			ob_get_clean();
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-Type: application/zip");
			header("Content-Disposition: attachment; filename=" . basename($zip_file_name) . ";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($zip_file_name));
			readfile($zip_file_name);

		}
			
		}
	}
?>