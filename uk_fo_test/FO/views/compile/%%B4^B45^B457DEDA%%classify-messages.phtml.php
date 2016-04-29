<?php /* Smarty version 2.6.19, created on 2016-04-21 10:02:45
         compiled from Contrib/classify-messages.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'Contrib/classify-messages.phtml', 42, false),)), $this); ?>
<?php echo '
<script type="text/javascript">

 $(document).ready(function() {	
	
	$("#menu_classifybox").addClass("active");
	$("#menu_compose").addClass("disabled");
	
	// call write email function
	$(\'#mail_message\').wysihtml5({locale: "fr-FR"});
	
});
</script>
'; ?>
	
	
<div class="container">
	<section id="maibox">
		<form  method="post" action="/contrib/send-reply" enctype="multipart/form-data">
			<div class="row">    
				<div class="span12">
					<h1>Messagerie priv&eacute;e</h1>
					<hr>
				</div>

				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/pattern/mail_leftmenu.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

				<div class="span9">
					<section id="emailView">
						<div class="btn-toolbar">
							<div class="btn-group">
								<a href="#goback" class="backnow btn btn-small"><i class="icon-arrow-left"></i></a>
							</div>							
						</div>
						
						<?php $_from = $this->_tpl_vars['archieve_messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['message']):
?>
							<div class="mod">
								<div class="row-fluid">
									<div id="print_mail">
										<h3><?php echo $this->_tpl_vars['message']['Subject']; ?>
</h3>
										<div class="status clearfix">
											<div class="span10"><strong>De: <?php echo $this->_tpl_vars['message']['sendername']; ?>
</strong> <span class="date">, 
											<?php if (((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")) == ((is_array($_tmp=$this->_tpl_vars['message']['receivedDate'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y"))): ?>
												<?php echo ((is_array($_tmp=$this->_tpl_vars['message']['receivedDate'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Hh%M") : smarty_modifier_date_format($_tmp, "%Hh%M")); ?>

											<?php elseif (((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")) == ((is_array($_tmp=$this->_tpl_vars['message']['receivedDate'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y"))): ?>
												<?php echo ((is_array($_tmp=$this->_tpl_vars['message']['receivedDate'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d %b %R") : smarty_modifier_date_format($_tmp, "%d %b %R")); ?>

											<?php else: ?>
												<?php echo ((is_array($_tmp=$this->_tpl_vars['message']['receivedDate'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%y %R") : smarty_modifier_date_format($_tmp, "%d/%m/%y %R")); ?>

											<?php endif; ?>
											</span>
											</div>											
										</div>									
										<div class="mailcontent">
											<?php echo $this->_tpl_vars['message']['content']; ?>

											<?php if ($this->_tpl_vars['message']['attachment_name'] != ''): ?>
												
												<div ><strong>Pi&egrave;ce jointe : </strong></div>
												<?php $_from = $this->_tpl_vars['attachments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['files'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['files']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['attachment_file']):
        $this->_foreach['files']['iteration']++;
?>
											
													<div>
														<?php echo $this->_tpl_vars['attachment_file']; ?>
 		
														<a href="/contrib/download-file?type=viewattachment&index=<?php echo ($this->_foreach['files']['iteration']-1); ?>
&attachment=<?php echo $this->_tpl_vars['message']['id']; ?>
">T&eacute;l&eacute;charger</a>
													</div>
												<?php endforeach; endif; unset($_from); ?>

											<?php endif; ?>
										</div>
									</div>	
									<hr>
									<?php if ($_GET['type'] == 'inbox' && $this->_tpl_vars['message']['auto_mail'] != 'yes'): ?>
									<div class="well clearfix" id="replybox">
										<div class="span1">
											<a href="/contrib/modify-profile"><img src="<?php echo $this->_tpl_vars['profile_picture']; ?>
" title="<?php echo $this->_tpl_vars['client_email']; ?>
"></a>
										</div>
										<div class="span11">
											<div class="muted">A : <strong><?php echo $this->_tpl_vars['message']['sendername']; ?>
</strong>
											<!--, <a href="#" class="muted">Ajouter</a>-->
											</div>
											<hr>
											<textarea id="mail_message" name="mail_message" class="textarea" placeholder="Votre message..."></textarea>
											<label for="mail_message" class="error" id="mail_message_error"></label>
											<br>
											<button class="btn btn-small btn-primary" id="send_message">Envoyer</button>
										</div>
									</div>
									<input type="hidden" name="ticket_id" value="<?php echo $this->_tpl_vars['message']['ticket_id']; ?>
"/>
									<?php endif; ?>
								</div>
							</div>	
							<?php endforeach; endif; unset($_from); ?>	
							<div class="mod">
								<div class="row-fluid">
									<div class="well clearfix" id="replybox">
										<div class="span1">
											<a href="/contrib/modify-profile"><img src="<?php echo $this->_tpl_vars['profile_picture']; ?>
" title="<?php echo $this->_tpl_vars['client_email']; ?>
"></a>
										</div>
										<div class="span11">
											<!--<div class="muted">A : <strong><?php echo $this->_tpl_vars['message']['sendername']; ?>
</strong>											
											</div>-->
											<hr>
											<textarea id="mail_message" name="mail_message" class="textarea" placeholder="Votre message..."></textarea>
											<label for="mail_message" class="error" id="mail_message_error"></label>
											<br>
											<button class="btn btn-small btn-primary" id="send_message">Envoyer</button>
										</div>
									</div>
									<input type="hidden" name="ticket_id" value="<?php echo $_GET['ticket']; ?>
"/>
								</div>
							</div>	
						
					</section>
				</div>
			</div>
		</form>
	</section>
</div>