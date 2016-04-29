<?php /* Smarty version 2.6.19, created on 2015-10-16 12:51:36
         compiled from Client/userconnexion.phtml */ ?>
<?php echo '
<script language="javascript">
	$("#menu_profile").addClass("active");
	
	(function($,W,D)
	{
		var JQUERY4U = {};

		JQUERY4U.UTIL =
		{
			setupFormValidation: function()
			{
				//form validation rules
				$("#connexionform").validate({
					onkeyup: false,
					rules: {
						current_password:{
							required:true,
							remote: "/client/checkuserpassword"
						},
						new_password: {
							required:true,
							minlength: 6
						},
						confirm_password: {
							required:true,
							minlength: 6,
							equalTo:"#new_password"
						}
					},
					messages: {
						current_password:{
							required:"Enter your password",
							remote:"Enter correct password"
						},
						new_password: {
							required:"Enter new password",
							minlength: "The password must contain more than 6 characters"
						},
						confirm_password: {
							required:"Enter confirmed new password",
							minlength: "The password must contain more than 6 characters",
							equalTo:"Your confirmed new password should be the same as new password"
						}
					},
					
				});
			}
		}

		//when the dom has loaded setup form validation rules
		$(D).ready(function($) {
			JQUERY4U.UTIL.setupFormValidation();
		});

	})(jQuery, window, document); 
</script>

'; ?>
	
	
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">CHANGE MY PASSWORD</h3>
		</div>
		<div class="modal-body">
			<form method="POST" name="connexionform" id="connexionform" class="form-horizontal" action="/client/profile">	 
				 <div class="control-group">
					<label class="control-label" for="old-inputPassword" style="margin:0px">Old password</label>
					<div class="controls">
						<input type="password" name="current_password" id="current_password" class="input-large"/>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="new1-inputPassword" style="margin:0px">New password</label>
					<div class="controls">
						<input type="password" name="new_password" id="new_password" class="input-large" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="new2-inputPassword" style="margin:0px">Confirm your new password</label>
					<div class="controls">
						<input type="password" name="confirm_password" id="confirm_password" class="input-large" required>
					</div>
					<div class="controls">
						<br><br>
						<button type="submit"class="btn btn-primary" name="submit_user" value="submit_user">Confirm</button> 
						<button class="btn" data-dismiss="modal" type="button" aria-hidden="true">Cancel</button>
					</div>
				</div>
		</div>
	</form>