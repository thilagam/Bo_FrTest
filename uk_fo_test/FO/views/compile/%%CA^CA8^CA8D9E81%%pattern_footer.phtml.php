<?php /* Smarty version 2.6.19, created on 2014-11-25 11:01:45
         compiled from common/pattern/pattern_footer.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substr', 'common/pattern/pattern_footer.phtml', 14, false),)), $this); ?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title><?php echo $this->_tpl_vars['meta_title']; ?>
</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $this->_tpl_vars['meta_desc']; ?>
">
    <meta name="author" content="">
	<link rel="shortcut icon" type="image/x-icon" href="/FO/favicon.ico" />
	<?php $this->assign('cssfiles', ''); ?>
	<?php $_from = $this->_tpl_vars['cssList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['css']):
?>
		<?php $this->assign('cssfiles', ($this->_tpl_vars['cssfiles']).($this->_tpl_vars['css']).","); ?>
	<?php endforeach; endif; unset($_from); ?>
	<link type="text/css" rel="stylesheet" href="/min/b=FO/css&f=<?php echo ((is_array($_tmp=$this->_tpl_vars['cssfiles'])) ? $this->_run_mod_handler('substr', true, $_tmp, -1) : smarty_modifier_substr($_tmp, -1)); ?>
"/>
	
	<!--[if lt IE 9]>
      <script src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/bootstrap3/html5shiv.js"></script>
      <script src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/bootstrap3/respond.min.js"></script>
    <![endif]-->
	
	<?php $_from = $this->_tpl_vars['javascriptList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['jsfiles']):
?>
		<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/<?php echo $this->_tpl_vars['jsfiles']; ?>
"></script>
	<?php endforeach; endif; unset($_from); ?>
	<?php echo '
	<style type="text/css">
	.modal.fade.in {      
		top: 0;   
	}
	.dropdown-menu {top:30px; right:50px !important}         
	</style>
	'; ?>

</head>

  <body class="page">
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
		<h3 id="myModalLabel">D&eacute;tail de l'annonce</h3>
	</div>
	<div class="modal-body">

	</div>
</div>
<!-- ajax use end --> 	
 </body>
</html>