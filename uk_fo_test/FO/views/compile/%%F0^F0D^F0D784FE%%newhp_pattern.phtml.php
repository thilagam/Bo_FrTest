<?php /* Smarty version 2.6.19, created on 2014-11-13 14:33:24
         compiled from common/pattern/newhp_pattern.phtml */ ?>
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
<!-- Le styles -->
<link href="/FO/css/bootstrap3/bootstrap.min.css" rel="stylesheet">
<link href='/FO/css/bootstrap3/googleapi_fonts.css' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="/FO/css/bootstrap3/ekko-lightbox.min.css">
<link href="/FO/css/bootstrap3/ep-theme.css" rel="stylesheet">
<link rel="stylesheet" href="/FO/css/bootstrap3/animate.min.css">
<link href="/FO/plugins/skins/square/blue.css" rel="stylesheet">
<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<script src="/FO/script/common/jquery.min.js"></script>
<script src="/FO/script/bootstrap3/bootstrap.min.js"></script>
<script src="/FO/script/bootstrap3/ekko-lightbox.min.js"></script>

<script src="/FO/script/common/jquery.validate.min.js"></script>
<script src="/FO/script/common/login_validation.js"></script> 		


</head>
<body class="home">
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

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<?php echo '
<script src="/FO/script/bootstrap3/wow.min.js"></script>
<script src="/FO/script/bootstrap3/icheck.min.js"></script>
<script src="/FO/script/bootstrap3/bootstrap-select.min.js"></script>
<script src="/FO/script/bootstrap3/ep.js"></script>

<script>
/***  lightbox plugin   ***/
$(document).delegate(\'*[data-toggle="lightbox"]\', \'click\', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});
</script>
<style>
	.dropdown-menu {top:30px; right:50px !important}  
</style>
'; ?>

</body>

</html>