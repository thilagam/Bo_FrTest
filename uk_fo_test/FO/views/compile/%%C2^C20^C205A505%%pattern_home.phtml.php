<?php /* Smarty version 2.6.19, created on 2014-11-13 14:36:07
         compiled from Contrib/pattern/pattern_home.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substr', 'Contrib/pattern/pattern_home.phtml', 21, false),)), $this); ?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>edit-place Author, <?php echo $this->_tpl_vars['meta_title']; ?>
</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="shortcut icon" href="/FO/favicon.ico" type="image/x-icon">

    <!-- Le styles -->

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/common/html5.js"></script>
    <![endif]-->	
	<?php $this->assign('cssfiles', ''); ?>
	<?php $_from = $this->_tpl_vars['cssList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['css']):
?>
		<?php $this->assign('cssfiles', ($this->_tpl_vars['cssfiles']).($this->_tpl_vars['css']).","); ?>
	<?php endforeach; endif; unset($_from); ?>
<link type="text/css" rel="stylesheet" href="/min/b=FO/css&f=<?php echo ((is_array($_tmp=$this->_tpl_vars['cssfiles'])) ? $this->_run_mod_handler('substr', true, $_tmp, -1) : smarty_modifier_substr($_tmp, -1)); ?>
"/>
	<!--[if IE]>
      <link rel="stylesheet" type="text/css" href="/FO/css/Contrib/custom_ie.css"/>
    <![endif]-->
	<?php $_from = $this->_tpl_vars['javascriptList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jsfiles']):
?>
		<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/<?php echo $this->_tpl_vars['jsfiles']; ?>
"></script>
	<?php endforeach; endif; unset($_from); ?>
	<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/contrib/jstz-1.0.4.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/contrib/jquery.cookie.js"></script>
	<!--<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/contrib/favicon.js"></script>-->
	<!--[if IE]>	
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/common/respond.src.js"></script>
	<![endif]-->


</head>

 <body id="contributor" class="homepage secondnav-on">
 		<!--Header Holder-->
			<?php $_from = $this->_tpl_vars['headerList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['frame']):
?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['frame']), 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endforeach; endif; unset($_from); ?>
		<!--/Header Holder-->

		<!--Content Holder-->
				<?php $_from = $this->_tpl_vars['mainList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['frame']):
?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['frame']), 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endforeach; endif; unset($_from); ?>		
		<!--/Content Holder-->	 

		<!--Footer Holder-->
				<?php $_from = $this->_tpl_vars['footerList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['frame']):
?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['frame']), 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endforeach; endif; unset($_from); ?>
		<!-- /FooterHolder -->
<!-- ***** Modal collections -->

<!-- ajax use start -->

<div id="viewOffer-ajax" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Detail of the offer</h3>
	</div>
	<div class="modal-body">
	
	</div>
</div>
<div id="playQuizz-ajax" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
		<button type="button" class="close" id="exitQuiz">&times;</button>
		<h3 id="myModalLabel">Pre-selection quiz</h3>
	</div>
	<div class="modal-body">

	</div>

</div>
<!-- ajax use end --> 	
	
<?php echo ' 	
<script type="text/javascript">
	 $(".scroll").click(function(event){		
		event.preventDefault();
		$(\'html,body\').animate({scrollTop:$(this.hash).offset().top}, 500);
		return false;		
	}); 
	
    // placeholder mgt for old browsers
    // $(\'input, textarea\').placeholder();
	
  
	$(\'#helpTab a\').click(function (e) {
    e.preventDefault();
    $(this).tab(\'show\');
    });	
	$(\'#helpTab a:first\').tab(\'show\');
	
	// tooltip activation
    $("[rel=tooltip]").tooltip();
	$("[rel=popover]").popover();
	
	var cookie_time_zone=\'local_timezone\';
	var time_zone_value=getTimezoneName();
	if(!cookieManager.readCookie(cookie_time_zone))
		cookieManager.writeSessionCookie(cookie_time_zone,time_zone_value);
	
	function getTimezoneName() {
		var timezone = jstz.determine();		
		return timezone.name();
	}
	
	
    </script>
'; ?>

  </body>
</html>