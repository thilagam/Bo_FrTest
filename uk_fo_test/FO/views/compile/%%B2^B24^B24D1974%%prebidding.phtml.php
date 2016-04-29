<?php /* Smarty version 2.6.19, created on 2014-03-07 09:39:48
         compiled from Client/prebidding.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'utf8_decode', 'Client/prebidding.phtml', 7, false),)), $this); ?>
<section id="prebidding" class="clearfix">
	<div class="span9 offset1">
		<h2><?php echo $this->_tpl_vars['participationcount']; ?>
 rédacteurs vous ont proposé un tarif</h2>
		<h3 class="sectiondivider pull-center"><span>Ils ont rédigé pour...</span></h3>
		<br />
			<?php $_from = $this->_tpl_vars['customerstrust']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ckey'] => $this->_tpl_vars['clogo']):
?>
				<img src="<?php echo $this->_tpl_vars['clogo']; ?>
" rel="tooltip" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['ckey'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
" data-placement="top">
			<?php endforeach; endif; unset($_from); ?>
		<hr /><br>
		<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Voir les devis</button>
	</div>
</section>