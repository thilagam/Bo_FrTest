<?php /* Smarty version 2.6.19, created on 2015-07-15 08:58:46
         compiled from common/pattern/index_pattern.phtml */ ?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $this->_tpl_vars['page_title']; ?>
</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?php echo $this->_tpl_vars['meta_desc']; ?>
">
		<meta name="keywords" content="<?php echo $this->_tpl_vars['keywords']; ?>
">
		<meta name="author" content="">

		<?php $_from = $this->_tpl_vars['javascriptList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['javascript']):
?>
			<script language="JavaScript" type="text/javascript" src="http://ep-test.edit-place.co.uk/FO/script/<?php echo $this->_tpl_vars['javascript']; ?>
"></script>
		<?php endforeach; endif; unset($_from); ?>

		<?php $_from = $this->_tpl_vars['cssList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['css']):
?>
			<link href="http://ep-test.edit-place.co.uk/FO/css/<?php echo $this->_tpl_vars['css']; ?>
" type="text/css" rel="stylesheet" />
		<?php endforeach; endif; unset($_from); ?>
		<!-- Le styles -->

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<!--[if IE]>
			<link rel="stylesheet" type="text/css" href="/FO/css/common/ep_ie.css" />
		<![endif]-->
		
		<link href="/FO/css/common/ep.css" rel="stylesheet" type="text/css" />
		<link href='/FO/css/common/googleapi_fonts.css' rel='stylesheet' type='text/css'>
		<link href="/FO/css/common/custom.css" rel="stylesheet" type="text/css" />
		<script src="/FO/script/common/jquery.min.js"></script>
				 
	</head>

	<body>
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
  
	<script src="/FO/script/common/bootstrap.min.js"></script>
	<script src="/FO/script/common/login_validation.js"></script>
	<script src="/FO/script/common/jquery.validate.min.js"></script>
	<?php echo '
	<script>
		// tooltip activation
		$("[rel=tooltip]").tooltip();
		$("[rel=popover]").popover();
	</script>
	<style>
			.dropdown-menu {top:30px; right:50px !important}
		</style>
	'; ?>

  </body>
</html>
