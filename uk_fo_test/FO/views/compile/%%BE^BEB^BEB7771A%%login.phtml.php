<?php /* Smarty version 2.6.19, created on 2014-11-13 14:33:24
         compiled from common/login.phtml */ ?>
<?php echo '
<script language="javascript">
	(function($,W,D)
	{ 
		var JQUERY4U = {};

		JQUERY4U.UTIL =
		{
			setupFormValidation: function()
			{
				//form validation rules1
				$("#loginform").validate({
					onkeyup: false,
					onfocusout: false,
					rules: {
						login_name:  {
							required: true,
							email: true
						},
						login_password: "required"
					},
					messages: {
						login_name: "Please enter valid email address",
						login_password: "Please enter Password",
					},
					submitHandler: function(form) { 
						submitlogin();
					}
					
				});
			}
		}

		//when the dom has loaded setup form validation rules
		$(D).ready(function($) {
			JQUERY4U.UTIL.setupFormValidation();
		});

	})(jQuery, window, document);
	
</script>
<style>
	.error { font-size:15px;color:#FF0000; margin:0; float:left; }
	
</style>
'; ?>
	
	<form method="POST" class="form-horizontal" name="loginform" id="loginform" action="" >
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel"><i class="icon-user"></i> LOG IN</h3>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label class="col-sm-4 control-label" for="login_name"><b>Your email</b></label>
			<div class="col-xs-12 col-md-6">
				<input type="text" name="login_name" id="login_name" placeholder="Email" class="form-control" />
			</div>
			<label class="error" for="login_name" generated="true" style="padding-left:210px;"></label>
		</div>
		
		<div class="form-group">
			<label class="col-sm-4 control-label" for="login_password"><b>Your password</b></label>
			<div class="col-xs-12 col-md-6">
				<input type="password" name="login_password" id="login_password" placeholder="Password" class="form-control" />
			</div>
			<label class="error" for="login_password" generated="true" style="padding-left:210px;"></label>
		</div>
		
		<div id="nameerr" class="error" style="padding-left: 200px;"></div>
		
		<div class="form-group">
			<label class="col-sm-4 control-label" for="login_password"></label>
			<div class="col-xs-12 col-md-6">
				<a data-target="#lost-password" data-toggle="modal" class="killcurrentmodal">Forgotten your password ?</a>
			</div>
		</div>
		<input type="hidden" name="returl" id="returl" value="<?php echo $this->_tpl_vars['page_url']; ?>
" /> 
	</div>
	<div class="modal-footer">
		<button type="submit" class="btn btn-primary">Log in</button>
		<button class="btn btn-default" data-dismiss="modal" aria-hidden="true" type="button">Cancel</button>
	</div>
	</form>