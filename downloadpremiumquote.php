<?php
	
	$quote=$_REQUEST['id']; 
	include('/home/sites/site6/web/BO/nlibrary/script/xmldb.php');
	$xml_obj = new XMLdb();	
	
	$lang_array=$xml_obj->loadArrayv2("EP_LANGUAGES",'fr');
	$category_array=$xml_obj->loadArrayv2("EP_ARTICLE_CATEGORY",'fr');
	$type_array=array("seo"=>"Article seo","desc"=>"Descriptifs produit","blog"=>"Article de blog","news"=>"News","guide"=>"Guide","other"=>"Autre");
	//$obj_array=array("1"=>"Je veux augmenter mon trafic Google","2"=>"Je veux du contentu qui buzz","3"=>"Je veux changer l'image de mon site","4"=>"Autre");
	$obj_array=array("premium"=>"Je veux &ecirc;tre recontact&eacute; par Edit-place pour parler de mon projet avant publication","liberte"=>"Je veux directment entrer en contact avec les r&eacute;dacteurs/traducteurs d'Edit-place","liberteprivate"=>"Je veux proposer un projet &agrave; un de mes r&eacute;dacteurs/traducteurs favoris","dontknow"=>"Je ne sais pas");
	$frequency_array=array("once"=>"En 1 seule fois","day"=>"Par jour","week"=>"Par semaine","month"=>"Par mois");
	$job_array=array("1"=>"PDG ou g&eacute;rant","2"=>"Commercial","3"=>"Marketing","4"=>"Directeur technique","5"=>"Web designer","6"=>"Chef de projet","7"=>"SEO manager","8"=>"Autre");
	$mots_array=array("seo"=>"130","desc"=>"80 mots","blog"=>"500 mots","news"=>"200 mots","guide"=>"500 mots","other"=>"-");
		
	$con = mysql_connect("localhost","ep_fr","8tJEzHnFCh9B3VbS"); 
	if (!$con) 
		die('Could not connect: ' . mysql_error());

	mysql_select_db("ep_fr", $con); 

	$quote_details = mysql_fetch_assoc(mysql_query("SELECT 
						p.*,c.company_name,c.category,c.company_name,c.website,u.email,up.first_name,up.last_name,up.phone_number,c.job
				FROM 
					PremiumQuotes p INNER JOIN User u ON p.user_id=u.identifier
					LEFT JOIN UserPlus up ON u.identifier=up.user_id
					LEFT JOIN Client c ON u.identifier=c.user_id	
				WHERE 
					p.id='".$quote."' "));  

		 
		if($quote_details)
		{	
			ob_start();
			echo "<table border=1>
					<tr>
						<th colspan=4 style=background-color:#357EBD>D&eacute;tails de la demande de devis</th>
					</tr>
					<tr>
						";
			
			if($quote_details['con_type']=='translation')
			{
				echo "<td><b>Traduction de contenu</b></td>
					 <td colspan=3>".$lang_array[$quote_details['from_language']]." -> ".$lang_array[$quote_details['to_language']]."</td>"; 
			}
			else
			{
				echo "<td>R&eacute;daction de contenu</td>
					 <td>".$lang_array[$quote_details['from_language']]."</td>"; 
			}
			
			$typear=explode("|",$quote_details['type']);
			$numar=explode("|",$quote_details['total_article']);
			$freqar=explode("|",$quote_details['frequency']);
			
			for($t=0;$t<count($typear);$t++)
			{
				if($typear[$t]=="other")
					$type="Autres - ".$quote_details['other_type'];
				else
					$type=$type_array[$typear[$t]];
				
				echo "	<tr><td valign=top><b>Type de contenu </b></td>
							<td>".$type."</td>
							<td colspan=2>".$mots_array[$typear[$t]]."</td>
						<tr>
							<td>Volume</td>
							<td colspan=3 style=text-align:left;>".$numar[$t]."</td>
						</tr>
						<tr>
							<td>R&eacute;currence</td>
							<td colspan=3>".$frequency_array[$freqar[$t]]."</td>
						</tr>";
			}	
			 
			if($quote_details['dontknowcheck']=="yes")
				$checktext="Oui";
			else
				$checktext="Non";
				
			echo "
				 <tr>
					<td><b>Je ne sais pas, je cherche juste un bon r&eacute;dacteur pour mon site</b></td>
					<td colspan=3>".$checktext."</td>
				</tr>";
				
			if(count($quote_details['objective'])>0)
			{
				//$objj=explode(",",$quote_details['objective']);
				echo "<tr><td valign=top><b>Mes attentes </b></td>";	
				
				/*for($o=0;$o<count($objj);$o++)
				{
					if($o!=0)
						echo "<tr>";
					echo "	<td colspan=3>".$obj_array[$objj[$o]]."</td>
						</tr>";
				}*/
				echo "<td colspan=3>".$obj_array[$quote_details['objective']]."</td></tr>";
				//if($quote_details['other_objective']!="")
					//echo "<tr><td colspan=4>".$quote_details['other_objective']."</td></tr>";
			}
			
			echo "<tr><td colspan=4></td></tr><tr><td colspan=4></td></tr>";
			
			echo "<tr><th colspan=4 style=background-color:#357EBD>D&eacute;tails client</th></tr>
				<tr>
					<td><b>Nom de l'entreprise</b></td>
					<td colspan=3>".$quote_details['company_name']."</td>
				</tr>
				<tr>
					<td><b>URL du site internet</b></td>
					<td colspan=3>".$quote_details['website']."</td>
				</tr>
				<tr>
					<td><b>Nom</b></td>
					<td colspan=3>".$quote_details['last_name']."</td>
				</tr>
				<tr>
					<td><b>Pr&eacute;nom</b></td>
					<td colspan=3>".$quote_details['first_name']."</td>
				</tr>
				<tr>
					<td><b>Email</b></td>
					<td colspan=3>".$quote_details['email']."</td>
				</tr>
				<tr>
					<td><b>T&eacute;l&eacute;phone</b></td>
					<td colspan=3 style=text-align:left>#".$quote_details['phone_number']."</td>
				</tr>
				<tr>
					<td><b>Fonction</b></td>
					<td colspan=3>".$job_array[$quote_details['job']]."</td>
				</tr>
					
				";
			 
			echo "</table>";	
			
			$quote_details['company_name']=str_replace(" ","_",$quote_details['company_name']);
			
	
			$xlsFile =$quote_details['user_id'].'_'.$quote_details['company_name'].'_'.$quote.'.xls';
			 
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");;
			header("Content-Disposition: attachment;filename=".$xlsFile);
			header("Content-Transfer-Encoding: binary ");
		}
		else
			echo "No data";
