<?php /* Smarty version 2.6.19, created on 2014-11-17 14:36:45
         compiled from Client/pattern/pattern_mail.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substr', 'Client/pattern/pattern_mail.phtml', 20, false),)), $this); ?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>edit-place : <?php echo $this->_tpl_vars['meta_title']; ?>
</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

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
	<?php $_from = $this->_tpl_vars['javascriptList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jsfiles']):
?>
		<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/<?php echo $this->_tpl_vars['jsfiles']; ?>
"></script>
	<?php endforeach; endif; unset($_from); ?>


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
	<div id="viewProfile-ajax" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel">dedicated profile modal - ajax</h3>
			</div>
		<div class="modal-body">

		</div>

	</div>
	<!-- ajax use end -->

	<div id="login" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Connexion</h3>
		</div>
		<div class="modal-body">
		<p>To design</p>
		</div>
		<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button class="btn btn-primary">Save changes</button>
		</div>
	</div> 
<?php echo ' 	
<script type="text/javascript">
	
	 $(".scroll").click(function(event){		
		event.preventDefault();
		$(\'html,body\').animate({scrollTop:$(this.hash).offset().top}, 500);
		return false;		
	}); 
	
    // placeholder mgt for old browsers
	$(\'input, textarea\').placeholder();
	
	// tooltip activation
    $("[rel=tooltip]").tooltip();
	$("[rel=popover]").popover();	
	
	$(\'a.printnow\').click(function() {
		window.print();
		return false;
	});
	
	
	 $(\'a.backnow\').click(function() {
		//window.history.back(1);
		history.go(-1);return false;
		
	});
		
	
    </script>
'; ?>

  </body>
</html>