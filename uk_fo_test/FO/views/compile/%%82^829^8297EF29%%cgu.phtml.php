<?php /* Smarty version 2.6.19, created on 2015-09-24 10:12:59
         compiled from common/cgu.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'utf8_decode', 'common/cgu.phtml', 7, false),)), $this); ?>
<section class="gray">
    <div class="container padding pull-top">
		<div class="center-block">
			<h1>Terms of Service</h1>
		</div>
		<div class="page-inner">
			<h2><?php echo ((is_array($_tmp=$this->_tpl_vars['cgutitle'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
</h2>
			<hr>				
			<?php echo $this->_tpl_vars['cgucontent']; ?>

				<p>&nbsp;</p>
		</div>	 
	</div>   
</section>