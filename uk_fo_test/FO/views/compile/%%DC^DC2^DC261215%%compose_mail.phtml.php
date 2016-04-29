<?php /* Smarty version 2.6.19, created on 2016-02-19 14:37:16
         compiled from Client/compose_mail.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'Client/compose_mail.phtml', 108, false),)), $this); ?>
<?php echo '
<script type="text/javascript">

 $(document).ready(function() {	
	
	$("#menu_mailbox").addClass("active");
	$("#menu_compose").addClass("disabled");
	
	// call write email function
	//$(\'#mail_message\').wysihtml5({"html":true,locale: "fr-FR"});
	$(\'#mail_message\').wysihtml5();
	//compose email validation
		$("#send_message").click(function(){
			var error=0,top=200;
			var sendto=$("#sendto");
			var mail_object=$("#mail_object");
			var object=mail_object.val();
			var	mail_message=$("#mail_message");
			var message=mail_message.val();
				message = message.replace(/(<([^>]+)>)/ig,"");
				message = message.replace(/&nbsp;/gi,"");
			if(sendto.length)
			{
				if(sendto.val())
				{
					sendto.removeClass("error");
					$("#sendto_error").html(\'\');
				}	
				else
				{
					sendto.addClass("error");
					$("#sendto_error").html(\'Send to compulsory\');
					error+=1;
				}
			}
			if(mail_object.length)
			{	
				if($.trim(object).length <1 || object==\'\')
				{
					mail_object.addClass("error");
					$("#mail_object_error").html(\'Object compulsory\');
					error+=1;
				}	
				else
				{
					$("#mail_object_error").html(\'\');
					mail_object.removeClass("error");
				}
			}	
			if($.trim(message).length <1 || message=="")
			{
				mail_message.addClass("error");
				$("#mail_message_error").html(\'Your message compulsory\');
				error++;
			}
			else
			{
				mail_message.removeClass("error");
				$("#mail_message_error").html(\'\');
			}	
			if(error>0)
			{
				if(sendto.length)
				{
					$(\'html, body\').animate( { scrollTop: top }, \'slow\' );
				}	
				return false;
			}	
			else
				return true;

		});	
	
	
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
		<form name="compose_mail" id="composeMail" method="post" action="/client/send-mail" enctype="multipart/form-data">
			<div class="row">    
				<div class="span12">
					<h1>Private messages</h1>
					<hr>
				</div>
				
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Client/pattern/mail_leftmenu.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

				<div class="span9">
					<div class="mod row-fluid">
						<h4><i class="icon-pencil"></i> New message</h4>
						<div class="well clearfix" id="replybox">
							<div class="span1">
								<a href="/client/profile"><img src="<?php echo $this->_tpl_vars['profile_picture']; ?>
" title="<?php echo $this->_tpl_vars['client_email']; ?>
"></a>
							</div>
							<div class="span11">
								<div class="muted" id="to">
									<label>A :
									<select class="input-medium" name="sendto" id="sendto">
										<option value="">Select</option>
										<optgroup label="Edit-place" name="service_contact">
											<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['EP_contacts'],'selected' => $_GET['serviceid']), $this);?>

										</optgroup>
										<optgroup label="Your editors" name="client_contact">
											<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['Cients_contacts'],'selected' => $this->_tpl_vars['toClientId']), $this);?>
 
										</optgroup>
									</select>
									<label for="sendto" class="error" id="sendto_error"></label>
									<!--, <a href="#" class="muted">+ ajouter</a>-->
									</label> 
								</div>
								<hr>
								<input name="mail_object" id="mail_object" type="text" class="span12 inline input" placeholder="Subject of your email...">
								<label for="mail_object" class="error" id="mail_object_error"></label>
								<textarea name="mail_message" id="mail_message" class="textarea" placeholder="Your message..."></textarea>
								<label for="mail_message" class="error" id="mail_message_error"></label>
								<button class="btn btn-small btn-primary" id="send_message">Send</button>
								<input type="hidden" name="ordercl" id="ordercl" value="<?php echo $_GET['ord']; ?>
" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</section>
</div>