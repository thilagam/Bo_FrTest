<?php /* Smarty version 2.6.19, created on 2016-02-19 14:37:08
         compiled from Client/classifybox.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Client/classifybox.phtml', 28, false),array('modifier', 'truncate', 'Client/classifybox.phtml', 36, false),array('modifier', 'stripslashes', 'Client/classifybox.phtml', 36, false),array('modifier', 'date_format', 'Client/classifybox.phtml', 40, false),)), $this); ?>
<?php echo '
<script type="text/javascript">

 $(document).ready(function() {	
	$("#menu_mailbox").addClass("active");
	$("#menu_classifybox").addClass("active");	
});
</script>	
'; ?>

<div class="container">
	<section id="maibox">
		<form method="" action="">
			<div class="row">    
				<div class="span12">
					<!---Pagination start-->
					<div class="pagination pull-right">
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Client/pagination.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					</div>	
					<!---Pagination END-->
					<h1>Private messages</h1>
				<hr>
				</div>

				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Client/pattern/mail_leftmenu.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

				<div class="span9">
					<div class="mod" id="mailbox-list">
						<?php if (count($this->_tpl_vars['paginator']) > 0): ?>
							<?php $_from = $this->_tpl_vars['paginator']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['message']):
?>
								
								<!-- row -->
								<div class="clearfix read">
									<a href="/client/classify-messages&page=<?php echo $_REQUEST['page']; ?>
&ticket=<?php echo $this->_tpl_vars['message']['ticket_id']; ?>
">
										<ul class="unstyled">
											<li class="snd"><?php echo $this->_tpl_vars['message']['sendername']; ?>
</li>
											<li class="sbj"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['message']['Subject'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 55, "...", true) : smarty_modifier_truncate($_tmp, 55, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>

												<span class="m"></span>
											</li>
											<li class="d">
												<?php if (((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")) == ((is_array($_tmp=$this->_tpl_vars['message']['updated_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y"))): ?>
													<?php echo ((is_array($_tmp=$this->_tpl_vars['message']['updated_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%I.%M %p") : smarty_modifier_date_format($_tmp, "%I.%M %p")); ?>

												<?php elseif (((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")) == ((is_array($_tmp=$this->_tpl_vars['message']['updated_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y"))): ?>
													<?php echo ((is_array($_tmp=$this->_tpl_vars['message']['updated_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d %b") : smarty_modifier_date_format($_tmp, "%d %b")); ?>

												<?php else: ?>
													<?php echo ((is_array($_tmp=$this->_tpl_vars['message']['updated_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%y") : smarty_modifier_date_format($_tmp, "%d/%m/%y")); ?>

												<?php endif; ?>
											</li>
										</ul>
									</a>						
								</div>	
							<?php endforeach; endif; unset($_from); ?>						
						<?php else: ?>
							<h4 class="no-results"><?php echo $this->_tpl_vars['ticket_classes']; ?>
</h4>
						<?php endif; ?>						
					</div>
				</div>
				<div class="span12">
					<!---Pagination start-->
					<div class="pagination pull-right">
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Client/pagination.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					</div>	
					<!---Pagination END-->										
				</div>
			</div>
		</form>
	</section>
</div>