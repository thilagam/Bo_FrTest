<?php
include_once("blackList.php");

$blackList=new blackList();

if($_GET['action']=='add' && $_GET['filter'])
{
	$blackList->addstopword();
	exit;
}
else if($_GET['action']=='remove' && $_GET['filter'])
{
	$blackList->removestopword();
	exit;
}
else
{
	$stopwords=$blackList->stopwords();
}	

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>Edit-place</title>
	<link href="styles.css" type="text/css" rel="stylesheet" />
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
 <script type="text/javascript" >
    function parentOrChild()
    {
        var typebox=$("#typeOption");
        var parentoption=$("#parentoptions_list").val();
        if(parentoption != 0)
         $("#typeOption").hide();
        else
         $("#typeOption").show();
    }
    function add_filter(filter)
    {
    	if(filter == null || filter=="" || filter==" ")
		{
			alert("S'il vous plaît entrer au moins 1 mot!");
			return false;
		}
		document.getElementById('stopwords_list').style.borderColor='#FF0000';
		filter=filter.replace(/ /g,'-');
		url1="stop_words.php?action=add&filter="+filter;
		$("#stopwords_list").load(url1, function(response, status, xhr) {
			document.getElementById('stopwords_list').innerHTML= response;
			document.getElementById('stopwords_list').style.borderColor='#CCCCCC';
		});
    }
    function remove_filter(remove_filter)
    {
    	if(remove_filter == null || remove_filter=="" || remove_filter==" ")
		{
			alert("S'il vous plaît entrer au moins 1 mot!");
			return false;
		}
		document.getElementById('stopwords_list').style.borderColor='#FF0000';
		remove_filter=remove_filter.replace(/ /g,'-');
		//alert(remove_filter);
		url1="stop_words.php?action=remove&filter="+remove_filter;
		$("#stopwords_list").load(url1, function(response, status, xhr) {
			document.getElementById('stopwords_list').innerHTML= response;
			document.getElementById('stopwords_list').style.borderColor='#CCCCCC';
		});
    }
 </script>
  </head>
<body> 
<div class="pre">
<div class="head_title">
	<h1>Black List keywords</h1>	
</div>
<div class="suggest">
	
	<form action="" method="post" id="stopwords" name="stopwords"  enctype="multipart/form-data">
		<div class="formfourcols">
			<div class="firstcol"> <samp id="276">Add Words</samp> :</div>

			<div class="secondcol"><input type="text" value="" name="filters" id="filters" size="30px;"/>
		   <samp id="277"> 
			<input type="button" value="Add" name="submit" id="submit" class="button" onclick="add_filter(document.getElementById('filters').value)" /></samp>
			<br /><samp id="278">(Enter Mutilpe words followed by Space)</samp> </div>
		</div>
		
	   <div class="formfourcols">
		   <div class="firstcol"> <samp id="279">Remove Words</samp> :</div>
			<div class="secondcol"><input type="text" value="" name="rem_filters" id="rem_filters" size="30px;"/>
		   <samp id="280">  <input type="button" value="Remove" name="remove" class="button" id="remove" onclick="remove_filter(document.getElementById('rem_filters').value)"/></samp>
			<br /><samp id="281">(Enter Mutilpe words followed by Space)</samp>
			</div>
		</div>

	   <div class="formfourcols">
		   <div class="firstcol"> <samp id="282">List of Stop Words </samp> :</div>
			<div class="secondcol">
				<div id="stopwords_list" style='margin-bottom:6px;border:#cccccc 2px solid;border-style:outer;width:700px; border-radius:10px;height:300px;padding:15px;font-size: 15px;' align='left' >
				 <?php echo $stopwords;?>
			</div> </div>


		</div>

	</form>
</div>
</div>
</body>
</html>
