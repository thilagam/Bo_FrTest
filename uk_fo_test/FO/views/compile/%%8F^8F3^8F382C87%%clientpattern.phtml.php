<?php /* Smarty version 2.6.19, created on 2014-11-17 14:36:08
         compiled from Client/pattern/clientpattern.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substr', 'Client/pattern/clientpattern.phtml', 18, false),)), $this); ?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $this->_tpl_vars['page_title']; ?>
</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?php echo $this->_tpl_vars['meta_desc']; ?>
">
		<meta name="author" content="">
		<link rel="shortcut icon" type="image/x-icon" href="/FO/favicon.ico" />
		<?php $_from = $this->_tpl_vars['javascriptList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['javascript']):
?>
			<script language="JavaScript" type="text/javascript" src="/FO/script/<?php echo $this->_tpl_vars['javascript']; ?>
"></script>
		<?php endforeach; endif; unset($_from); ?>

		<?php $this->assign('cssfiles', ''); ?>
		<?php $_from = $this->_tpl_vars['cssList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['css']):
?>
			<?php $this->assign('cssfiles', ($this->_tpl_vars['cssfiles']).($this->_tpl_vars['css']).","); ?>
		<?php endforeach; endif; unset($_from); ?>
		<link type="text/css" rel="stylesheet" href="/min/b=FO/css&f=<?php echo ((is_array($_tmp=$this->_tpl_vars['cssfiles'])) ? $this->_run_mod_handler('substr', true, $_tmp, -1) : smarty_modifier_substr($_tmp, -1)); ?>
"/>
		<!-- Le styles -->

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<?php if ($this->_tpl_vars['clientidentifier'] != ""): ?>
	<body id="client" class="secondnav-on">
	<?php else: ?>
	<body >	
	<?php endif; ?>
		<?php $_from = $this->_tpl_vars['headerList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['frame']):
?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['frame']), 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endforeach; endif; unset($_from); ?>
		
		<?php $_from = $this->_tpl_vars['mainList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['frame']):
?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['frame']), 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endforeach; endif; unset($_from); ?> 	
		
		<?php $_from = $this->_tpl_vars['footerList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['frame']):
?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['frame']), 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endforeach; endif; unset($_from); ?>

	<script>
		// tooltip activation
		$("[rel=tooltip]").tooltip();
		$("[rel=popover]").popover();
	</script>
	
  </body>
</html>
