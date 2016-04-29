<?php /* Smarty version 2.6.19, created on 2015-06-30 09:33:00
         compiled from Contrib/compose_mail.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'Contrib/compose_mail.phtml', 80, false),array('modifier', 'count', 'Contrib/compose_mail.phtml', 81, false),)), $this); ?>
<?php echo '
<script language="JavaScript" type="text/javascript" src="/FO/script/common/jquery.MultiFile.js"></script>
<script type="text/javascript">

 $(document).ready(function() {	
	
	$("#menu_mailbox").addClass("active");
	$("#menu_compose").addClass("disabled");
	
	// call write email function
	$(\'#mail_message\').wysihtml5();
	

	$("#filePJ").MultiFile ();
	//compose mail validation
	/* jQuery.validator.addMethod("htmltext", function(mail_message, element) {
				return false;
				//return mail_message.textContent||mail_message.innerText;
			}, "Message obligatoire");
			
			$("#composeMail").validate({
				rules:{
					sendto:"required",
					mail_object:"required",
					mail_message:{required:true,htmltext:true}	
				},
				messages:{
					sendto:"sendto obligatoire",
					mail_object:"Objet obligatoire",
					mail_message:"Message obligatoire"
				},
				//errorClass: "help-inline",
				errorClass: "error",
				errorElement: "label",
				
				highlight: function(label) {
					$(label).addClass(\'error\');
					$(label).removeClass(\'success\');
				},
				success: function(label) {
					label.addClass(\'success\');
					label.removeClass(\'error\');
				}			
			}); */
	});	

	
	
</script>
'; ?>
	

<div class="container">
	<?php if ($this->_tpl_vars['actionmessages'][0]): ?>
		<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">&times;</button>
			<i class="icon-ok icon-white"></i> <?php echo $this->_tpl_vars['actionmessages'][0]; ?>
.
		</div>  
	<?php endif; ?>
	<section id="maibox">
		<form name="compose_mail" id="composeMail" method="post" action="/contrib/send-mail" enctype="multipart/form-data">
			<div class="row">    
				<div class="span12">
					<h1>Private messages</h1>
					<hr>
				</div>
				
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/pattern/mail_leftmenu.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

				<div class="span9">
					<div class="mod row-fluid">
						<h4><i class="icon-pencil"></i> New message</h4>
						<div class="well clearfix" id="replybox">
							<div class="span1">
								<a href="/contrib/modify-profile"><img src="<?php echo $this->_tpl_vars['profile_picture']; ?>
" title="<?php echo $this->_tpl_vars['client_email']; ?>
"></a>
							</div>
							<div class="span11">
								<div class="muted" id="to">
									<label>TO :
									<select class="input-xxlarge" name="sendto" id="sendto">
										<option value="">Select</option>
										<!--<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['ongoingContacts'],'selected' => $this->_tpl_vars['touser']), $this);?>
-->
										<?php if (count($this->_tpl_vars['ongoingContacts']) > 0): ?>
											<optgroup label="Articles" name="service_contact">
												<?php $_from = $this->_tpl_vars['ongoingContacts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['article']):
?>
													<option value="<?php echo $this->_tpl_vars['article']['created_user']; ?>
" <?php if ($this->_tpl_vars['article']['created_user'] == $this->_tpl_vars['touser']): ?> selected<?php endif; ?> ><?php echo $this->_tpl_vars['article']['title']; ?>
</option>
												<?php endforeach; endif; unset($_from); ?>			
											</optgroup>	
										<?php endif; ?>										
										<optgroup label="Other" name="Other">
											<option value="110823103540627">Other</option>
										</optgroup>
									</select>
									<label for="sendto" class="error" id="sendto_error"></label>
									<!--, <a href="#" class="muted">+ ajouter</a>-->
									</label> 
								</div>
								<hr>
								<input name="mail_object" id="mail_object" type="text" value="<?php echo $this->_tpl_vars['mail_object']; ?>
" class="span12 inline input" placeholder="Subject...">
								<label for="mail_object" class="error" id="mail_object_error"></label>
								<textarea name="mail_message" id="mail_message" class="textarea" placeholder="Your message..."></textarea>
								<label for="mail_message" class="error" id="mail_message_error"></label>
								
								
								<p class="btn-link disabled" data-target="#addfile" data-toggle="collapse">Add a file</p>
								<div id="addfile" class="collapse out">
									<input type="file" name="attachment[]" id="filePJ">					
									<hr>
								</div>								
								<button class="btn btn-small btn-primary" id="send_message">Send</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>