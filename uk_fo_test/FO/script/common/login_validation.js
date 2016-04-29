/*
document.onkeydown = function(evt) {
	evt = evt || window.event;
	if (evt.keyCode == 13) 
	{
		//$("#loginform").submit();
		$('#login').removeClass("modal hide fade").addClass("modal");
		submitlogin();
	}
	};
*/	
	function loginscreen()
	{
		$.ajax({
			type : 'post', 
			url : '/index/login', 
			   
			success : function(r)
		   {
			$('#login').html(r);
		   }
		});
	}
	
function submitlogin()
{
	var login_name = $("#login_name").val();
	var login_password = $("#login_password").val();
	var returl=$("#returl").val();
	var err_count=0;
	err_msg="";
	
	if(login_name=="")
		$("#nameerr").html();
			
			$.ajax({
				url: "/index/uservalidationajax",
				global: false,
				type: "POST",
				data: ({login_name : login_name,login_password:login_password}),
				dataType: "html",
				async:false,
				success: function(msg){
					if (msg == "NO") {
						$('#nameerr').html("Email or Password incorrect");  
						return false;
					}
					else if(msg=='client')
					{
						if(returl!="")
							window.location = returl;
						else
							window.location = "/client/home";
					}
					else if(msg=='contributor')
					{
						if(returl!="")
							window.location = returl;
						else
							window.location = "/contrib/home";
					}
					else
					{
						$('#nameerr').html("Email or Password incorrect");
						return false;
					}
				}
			});
		
		return false;
}

	//Forgot password liberte2
	function forgotpasswordmailindex()
	{
		var email=$("#forgotpwdemail").val();
		
		if(email=="")
		{
			$("#forgotpwdemailerr").html("Please enter email address");
			return false;
		}
		else
		{
			$("#forgotpwdemailerr").html("");
		$.ajax({
				url: "/client/changepassword",
				global: false,
				type: "POST",
				data: ({email : email}),
				dataType: "html",
				async:false,
				success: function(msg){ //alert(msg);
					if(msg=="sent")
						$("#alert").html('<button type="button" class="close" data-dismiss="alert">&times;</button>Thank you! Your password has been sent to your email address').removeClass("alert-error").addClass("alert-info");
					else if(msg=="no")
						$("#alert").html('<button type="button" class="close" data-dismiss="alert">&times;</button>Email address <u>'+email+'</u> does not exist in our database').removeClass("alert-info").addClass("alert-error");
					return false;
				}
			});
		}	
	}