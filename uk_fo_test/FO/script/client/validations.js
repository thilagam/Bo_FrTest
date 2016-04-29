//AO creation step2
function validate_liberte2()
{
	var option=$("input[name='optionsRadios']:checked").val();
	var login = $("#loginemail").val();
	var login_password = $("#loginpassword").val();
		
	if(option=='option2')
	{
		var err=0;
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
       
		if(login == '') {			
			$('#emailerrlib2').html("Please enter Email address");			
            err++;
		} 
        else if(!emailReg.test(login)) {
			$('#emailerrlib2').html("Please enter valid Email address");
			err++;
		}
		else  {
			$('#emailerrlib2').html("");
        }
		
		if(login_password == '') {			
			$('#passerrlib2').html("Please enter your Password");
			err++;
		}        
		else  {
			$('#passerrlib2').html("");
        }
		
		if(err>0)
		{
			return false;
		}
		else
		{
			$.ajax({
				url: "/client/checkloginuseremail",
				global: false,
				type: "POST",
				data: ({login_name : login,login_password:login_password}),
				dataType: "html",
				async:false,
				success: function(msg){
				
					if (msg == "false") {
						$('#emailerr').html("Email or Password incorrect")
						return false;
					}
					else
					{
						document.registerform.submit();
						return true;
					}
				}
			});
		}
	}
	return false;
}

	//For loading contributor profile in modal popup	
	function loadcontribprofile(part)
	{    
		  $.ajax({
			type : 'post', 
			url : '/client/userprofile', 
			data :  'partid='+part, 
			   
			success : function(r)
		   {
			if(r=="expired")
				window.location="/index/index";
			else	
			{	
				$('#userprofile').html(r);
				$('html,body').animate({scrollTop:100}, 500);
			}	
		   }
		});
	}
	
	//Devis premium
	function pollparticipationactive(partid,status)
	{
		var poll=$("#pollid").val();
		$.ajax({
				  type: 'GET',
				  url: '/client/pollparticipationstatus',
				  data: 'partid=' + partid +'&status='+status+'&poll='+poll,
				  
				  success: function(data)
				  {  //alert(data);
						var poll_obj = $.parseJSON(data);
						$('#partstatus_'+partid).html(poll_obj[0].text);
						
						$('#maxprice').html(poll_obj[0].max+'&euro; max');
						$('#minprice').html(poll_obj[0].min+'&euro; min');
						$('#avgprice').html(poll_obj[0].avg+'&euro; moy.');
				  }
			  });
	}

	// Aide in Order pages
	function leadaide()
	{
		window.location="/client/compose-mail?serviceid=111201092609847";
	}
	
	//Delete Article comment
	function deletecomment(commentid)
	{
		var countc=$("#commentcount").val();
		
		if(confirm("Do you really want to delete this comment?"))
		{
			$.ajax({
					url: "/client/deleteaocomment",
					global: false,
					type: "POST",
					data: ({cid : commentid}),
					dataType: "html",
					async:false,
					success: function(msg){ 
						if(msg=="YES")
						{
							
							$("#comment_"+commentid).hide();
							countval=countc-1;
							$("#commentcount").val(countval);
							$('#commentcountdisp').html(countval);
							return false;
						}
					}
				});
		}
		else
			return false; 
	}
	
	//Profile user connexion
	function modifyusersetting(user)
	{
		$.ajax({
			type : 'post', 
			url : '/client/userconnexion', 
			data :  'userid='+user, 
			   
			success : function(r)
		   {
			$('#useSettingModal').html(r);
		   }
		});
	}
	
	//Profile
	function switchbrowse(vall)
	{
		if(vall=='file')
		{
			$("#fileupload").show();
			$("#twitterupload").hide();
		}
		else
		{
			$("#fileupload").hide();
			$("#twitterupload").show();
		}		
	}
	
	//Quote selection pop up
	function loadprofile(part,art,index)
	{    
		if(index=="next")
		{
			var user=$("#quote").val();
			var partid=$("#next_"+user).val();
		}
		else if(index=="previous")
		{
			var user=$("#quote").val();
			var partid=$("#previous_"+user).val();	
		}
		else
			var partid=part;  
			
		  $.ajax({
			type : 'post', 
			url : '/client/quoteselection', 
			data :  'partid='+partid+'&artid='+art, 
			   
			success : function(r)
		   {
			$('#profilecontent').html(r);
		   }
		});

	}
	
	//Liberte1
	function showparticipationtime()
	{
		var parttime=$("#participationtime").val();
		parttime=parttime.replace(/[^0-9\.]/g, '');
		$("#participationtime").val(parttime);
		
		var partoption=$("#participationtime_option").val();
	//	var parttime=$("#participationtime").val();
			
		if(parttime!="")
		{
		$.ajax({
				url: "/client/getparticipationlimit",
				global: false,
				type: "POST",
				data: ({option : partoption, value:parttime}),
				dataType: "html",
				async:false,
				success: function(msg){ 
					$("#participationlimit").html(msg);
				}
			});
		}	
	}

	//Liberte2 form switching
	function switchloginform(opt)
	{alert(opt);
		if(opt=="option1")
		{
			$("#form1fields").show();
			$("#form2fields").hide();
		}
		else if(opt=="option2")
		{
			$("#form1fields").hide();
			$("#form2fields").show();
		}
	}
	
	//Forgot password liberte2
	function forgotpasswordmail()
	{
		var email=$("#forgotemail").val();
		
		if(email=="")
		{
			alert("Please enter Email address");
			return false;
		}
		
		$.ajax({
				url: "/client/changepassword",
				global: false,
				type: "POST",
				data: ({email : email}),
				dataType: "html",
				async:false,
				success: function(msg){ //alert(msg);
					if(msg=="sent")
						$("#forgot_text").html('<font color="rgb(255, 0, 0)">Thank you! Your password has been sent to your email address</font>');
					else if(msg=="no")
						$("#forgot_text").html('<font color="rgb(0, 153, 0)">Email address <u>'+email+'</u> does not exist in our database</font>');
					return false;
				}
			});
	}
	
	function selectALL()
	{
	   if($("#select_all").attr('checked'))
	   {
		  var $b = $('input[type=checkbox]');
		  $b.attr('checked', true);
	   }
	   else
	   {
		   var $b = $('input[type=checkbox]');
		   $b.attr('checked', false);
	   }
	}
	
	function updateParticipationall()
	{
		var checked = $("input[type=checkbox]:checked");
		var nbChecked = checked.size(); 
		var poll=$("#pollid").val();
		
			if(nbChecked==0)
			{
				alert("Please check atleast once checkbox");
				return false;
			}
			else
			{
					document.pollform.action ="/client/devispremium?id="+poll;
					document.pollform.submit();
					return true;
			}
	}
	
	function addfavoritecontrib()
	{
		/*var checked = $("input[type=checkbox]:checked");
		var nbChecked = checked.size(); 
		var pollid=$("#pollid").val();
		
			if(nbChecked==0)
			{
				alert("Merci de cocher au moins une case");
				return false;
			}
			else
			{*/
				 /*var selectedGroups  = new Array();
					$("input[name='contribtype[]']:checked").each(function() {
						selectedGroups.push($(this).val());
					});*/
			var pollid=$("#pollid").val();
			
				 $.ajax({
					type : 'post', 
					url : '/client/addfavoritecontribs', 
					data :  'poll='+pollid, 
					   
					success : function(r)
				   {
					$("#favaddalert").show();
					$('html,body').animate({scrollTop:100}, 500);
					
					var $b = $('input[type=checkbox]');
					$b.attr('checked', false);
				   }
				});	
			//}
	}
	
	function loaddevisprofile(part,poll,indx)
	{    
		if(indx=="next")
		{
			var user=$("#polluser").val();
			var partid=$("#next_"+user).val();
		}
		else if(indx=="previous")
		{
			var user=$("#polluser").val();
			var partid=$("#previous_"+user).val();	
		}
		else
			var partid=part;  
			
		
		  $.ajax({
			type : 'post', 
			url : '/client/devisuserprofile', 
			data :  'partid='+partid+'&pollid='+poll, 
			   
			success : function(r)
		   {
			$('#profilecontent').html(r);
		   }
		});

	}
	
	function Addfavlist(fid,action)
	{
		 $.ajax({
			type : 'post', 
			url : '/client/sessionfavcontribs', 
			data :  'contribid='+fid+'&action='+action, 
			   
			success : function(r)
		   {
			
		   }
		});
	}
	
	function CloseArticle(art)
	{
		if(confirm("Would you like to cancel this mission ?")) 
		{	
		 $.ajax({
			type : 'post', 
			url : '/client/closearticle', 
			data :  'article='+art, 
			   
			success : function(r)
		   {
				alert("Mission closed !"); 
				window.location.reload();
		   }
		});
		}
		else
			return false;
	}
	
	function addprice(type)
	{
		var ep_percent=$("#eppercent").val();
		if(type=='min')
		{
			var price=$("#price_min_total").val();
			var totalprice=(price*(100-ep_percent))/100;
			$("#price_min").val(totalprice);
		}
		else if(type=='max')
		{
			var price=$("#price_max_total").val();
			var totalprice=(price*(100-ep_percent))/100;
			$("#price_max").val(totalprice);
		}
	}
	
	function togglepriceinfo()
	{
		if($("input[name='privatecontrib']:checked").val()=="on")
			$("#priceinfo").show("slow");
		else
			$("#priceinfo").hide("slow");
	}