<?php /* Smarty version 2.6.19, created on 2015-02-04 07:46:39
         compiled from common/create_contribhp.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'common/create_contribhp.phtml', 205, false),)), $this); ?>
<?php echo '
<script language="javascript" type="text/javascript">

function validate_registercontrib()
	{
				var option=$("input[name=\'optionsRadios\']:checked").val();
				if(option=="option1")
				{
					//form validation rules
					$("#registerform").validate({
						onkeyup:true,
						rules: {
							email:  {
								required: true,
								email: true,
								remote: "/client/checknewuseremail"
											
							},
							password: {
								required: true,
								minlength: 6
							},
							confirmpassword: {
								required: true,
								minlength: 6,
								equalTo:"#password"
							},
							termscheck: "required"
						},
						messages: {
							email: {
								required:"Please enter Email address",
								email:"Please enter valid Email address",
								remote:"Email address already exists!"
							},
							password: {
								required:"Please enter Password",
								minlength:"Password length should be greater than 6 characters"
							},
							confirmpassword: {
								required:"Please enter Confirm Password",
								minlength:"Confirm Password length should be greater than 6 characters",
								equalTo: "Confirm Password should be same as Password"
							},
							termscheck: "Pease validate Terms"						
						}
						
					});
					
				}
	}			
	
function userlogin()
{
	var login_name = $("#email_login").val();
	var login_password = $("#password_login").val();
	var err_count=0;
	err_msg="";
	if(login_name=="")
		$("#passerr").html();
	$.ajax({
		url: "/index/uservalidationajax",
		global: false,
		type: "POST",
		data: ({login_name : login_name,login_password:login_password}),
		dataType: "html",
		async:false,
		success: function(msg){
			if (msg == "NO") {
				$(\'#passerr\').html("Email or Password incorrect!")
				return false;
			}
			else if(msg==\'client\')
			{
			window.location = "/client/profile";
				}
			else if(msg==\'contributor\')
			{
				window.location = "/contrib/profile";
			}
			else
			{
				$(\'#passerr\').html("Email or Password incorrect");
				return false;
			}
		}
	});
	return false;
} 
(function($,W,D)
	{
		var JQUERY4U = {};

		JQUERY4U.UTIL =
		{
			setupFormValidation: function()
			{
				//form validation rules1
				$("#login_form").validate({
					onkeyup: false,
					onfocusout: false,
					rules: {
						email_login:  {
							required: true,
							email: true
						},
						password_login: "required"
					},
					messages: {
						email_login: {
								required:"Please enter Email address",
								email:"Please enter valid Email address"								
							},
						password_login: "Please enter Password",
					},
					submitHandler: function(form) { 
						userlogin();
					}
					
				});
			}
		}

		//when the dom has loaded setup form validation rules
		$(D).ready(function($) {
			JQUERY4U.UTIL.setupFormValidation();
		});

	})(jQuery, window, document);
function switchloginform(opt)
{
	if(opt=="option1")
	{
	$("#option1fields").show();
	$("#option2fields").hide();
	}
	else if(opt=="option2")
	{
	$("#option1fields").hide();
	$("#option2fields").show();
	}
} 

	
			
	
	
	
</script>

'; ?>


	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel"><i class="icon-user"></i> LOG IN</h3>   
	</div>
	
		<p style="padding:10px;">Become a Writer for Edit-place and Access Hundreds of Missions and Quote requests every month!</p>
		
		<div class="row">
			<div class="col-xs-8" style="padding-left:120px">	
				<div class="radio">
					<label for="optionsRadios2">
						<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2" checked>
						I already have an account with Edit-place
					</label>
				</div>
				<div class="radio">
					<label for="optionsRadios1">
						<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1">
						<strong>New? Create an account</strong> 
					</label>
				</div>
			</div>
		</div>
		
		<hr>  
		
		<div id="option1fields" style="display:none;">
		<form method="POST" class="form-horizontal" name="registerform" id="registerform" action="/contrib/addcontrib">			
			<div class="modal-body">
			<div class="form-group">
				<label for="login_email" class="col-sm-4 control-label">Email address</label>
				<div class="col-xs-12 col-md-6">
					<input type="text" class="form-control" id="login_email" name="email">	
					<label class="error" for="email" generated="true"></label>
				</div>
			</div>			
			<div class="form-group">
				<label class="col-sm-4  control-label" for="password" >Create a password</label>
				<div class="col-xs-12 col-md-6">
					<input type="password" class="form-control" id="password" name="password">						
				</div>
			</div>				
			<div class="form-group">
				<label for="inputEmail" class="col-sm-4 control-label">Confirm password</label>
				<div class="col-xs-12 col-md-6">
					<input type="password" class="form-control" id="confirmpassword" name="confirmpassword">						
				</div>
			</div>					 
			<div class="form-group">
				<label for="inputEmail" class="col-sm-4 control-label">Mother tongue</label>
				<div class="col-xs-12 col-md-6">
					<select name="language" id="language" class="form-control">
						<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['ep_language_list'],'selected' => 'uk'), $this);?>

					</select>	
				</div>
			</div>	
			<input type="hidden" name="newuser" id="newuser" value="1" />	
			<div class="form-group">		
					<label class="col-sm-4  control-label" ></label>
					<div class="col-xs-12 col-md-8">
						<input type="checkbox" name="termscheck" id="termscheck">I agree to 
						<a target="_blank" href="/contrib/terms">Edit-place's terms and conditions </a>							
						<label class="error" for="termscheck" generated="true"></label>
					</div>
			</div>	
			</div>
			<div class="modal-footer">
				<button type="submit" name="register_contrib" class="btn btn-primary" value="submit" onClick="return validate_registercontrib();">Create my account</button>
			</div>
		</form>
		</div>
		
		<div id="option2fields" >
			<form method="POST" class="form-horizontal" name="login_form" id="login_form" action="" >
				<div class="modal-body">
				<div class="form-group">
					<label class="col-sm-4 control-label" for="email_login">Your email</label>
					<div class="col-xs-12 col-md-6">
						<input type="text" name="email_login" id="email_login" placeholder="Email" class="form-control" />
						<div class="error" id="emailerr"></div>
					</div>
				</div>
				<div class="form-group" style="margin-bottom:10px">
					<label class="col-sm-4 control-label" for="password_login">Your password</label>
					<div class="col-xs-12 col-md-6">
						<input type="password" name="password_login" id="password_login" placeholder="Password" class="form-control" />
						<div class="error" id="passerr"></div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-4 control-label"></label>	
					<div class="col-xs-12 col-md-6">
						<a data-target="#lost-password" data-toggle="modal" class="killcurrentmodal">Forgotten your password ?</a>
					</div>
				</div>
				</div>
				<div class="modal-footer">
					<button type="submit"  class="btn btn-primary">Log in</button>
					<button class="btn" data-dismiss="modal" aria-hidden="true" type="button">Cancel</button>
				</div>
			</form>
		</div>				
	<!--
	<div id="terms" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
						<button type="button" class="close" onclick="$('#terms').modal('hide');" aria-hidden="true">&times;</button>
						<h3 id="myModalLabel">Terms of Use</h3>
				</div>
				<div class="modal-body " style="text-align:justify">
				</div>
				<div class="modal-footer">
					<button class="btn" aria-hidden="true" onclick="$('#terms').modal('hide');">Close</button>
				</div>
			</div>
		</div>		
	</div>
	-->