<?php /* Smarty version 2.6.19, created on 2014-06-10 16:08:04
         compiled from common/newsletter.phtml */ ?>
<!DOCTYPE html>
<html>
	<body>
		<input type="hidden" id="name" value="<?php echo $this->_tpl_vars['wrname']; ?>
" />
		<input type="hidden" id="ptype" value="<?php echo $this->_tpl_vars['wrprofile']; ?>
" />
	</body>
</html>

<?php echo '
	<script>
		document.getElementById("htmllink").innerHTML="";
		
		var name=document.getElementById("name").value;
		document.getElementById("wrname").innerHTML=name;

		var profiletype=document.getElementById("ptype").value;
		document.getElementById("wrprofile").src=profiletype;
	</script>
'; ?>