<?php /* Smarty version 2.6.19, created on 2014-11-18 12:07:36
         compiled from Client/pattern/quotespattern.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substr', 'Client/pattern/quotespattern.phtml', 16, false),)), $this); ?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $this->_tpl_vars['page_title']; ?>
</title>
		<meta name="description" content="<?php echo $this->_tpl_vars['meta_desc']; ?>
">
		<meta name="author" content=""> 
		<link rel="shortcut icon" type="image/x-icon" href="/FO/favicon.ico" />
		<?php $this->assign('cssfiles', ''); ?>
		<?php $_from = $this->_tpl_vars['cssList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['css']):
?>
			<link type="text/css" rel="stylesheet" href="/FO/css/<?php echo $this->_tpl_vars['css']; ?>
"/>  
		<?php endforeach; endif; unset($_from); ?>
		<!--<?php $this->assign('cssfiles', ($this->_tpl_vars['cssfiles']).($this->_tpl_vars['css']).","); ?>-->
		<link type="text/css" rel="stylesheet" href="/min/b=FO/css&f=<?php echo ((is_array($_tmp=$this->_tpl_vars['cssfiles'])) ? $this->_run_mod_handler('substr', true, $_tmp, -1) : smarty_modifier_substr($_tmp, -1)); ?>
"/>
		 <link href="/FO/plugins/skins/square/blue.css" rel="stylesheet"> 
		 
		<!-- Le styles -->
		
			
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="/FO/script/bootstrap3/bootstrap.min.js"></script>
		
		<script src="/FO/script/common/login_validation.js"></script> 
		<script src="/FO/script/common/jquery.validate.min.js"></script> 
		
	</head>

	<body class="quote">
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
		
		<script src="/FO/script/bootstrap3/wow.min.js"></script>
		<script src="/FO/script/bootstrap3/icheck.min.js"></script>
		<script src="/FO/script/bootstrap3/bootstrap-select.min.js"></script>
		<script src="/FO/script/bootstrap3/ep.js"></script>
		<?php echo '
		<style>
			.dropdown-menu {top:30px; right:50px !important}  
		</style>
		'; ?>

  </body>
</html>
