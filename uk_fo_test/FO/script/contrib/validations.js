
$(function(){
		
		bootstrap_alert = function() {}
		bootstrap_alert.error = function(message) {
            $('#alert_placeholder').html('<div class="alert  alert-error"><button data-dismiss="alert" class="close" type="button">&times;</button><span><ul>'+message+'</ul></span></div>')
        }
		//cart validation
		$("#cart-submit").click(function(){
			
			var error=0,top=200,price_min=0,price_max=0,pricedisplay='yes';
			var obj;
			var msg1='',msg2='';
			
			var expiredkeys = [];
			
			//			Article Price Validation
			
			$("[id^=contrib_price_]").each(function(i) {
					var errtype='';
					var article=$(this).attr('id').split("_");

					var articleid=article[2];
					var ao_type=article[3];

					var contrib_price=$(this).val().replace(",",".");
					var priceRegex=/^[0-9]+(\.?[0-9]+)?$/;
					var priceRegex1=/^[0-9]+(,?[0-9]+)?$/;
					
					if(ao_type=='premium' || ao_type=='privatenopremium')
					{
						var target_page = "/contrib/getarticlepricerange?articleid="+articleid;
						var request = $.ajax({
							url: "/contrib/getarticlepricerange",
							type: "POST",
							data: {articleid : articleid},
							async: false,
							dataType: 'json',
							success: function(data) {
								if(data.error=="time_out")
								{
									errtype="time_out";
									expiredkeys.push(article[2]);
								}	
								else
								{
									price_min=data.price_min;
									price_max=data.price_max;						  
									pricedisplay=data.pricedisplay;		
								}	
								
							}
						});					
					}	
					else if(ao_type=='nopremium')
					{
						var date=$("#idate_limit_"+articleid).val();					
					}
					contrib_price=parseFloat(contrib_price);


					if(((contrib_price>=price_min && contrib_price<=price_max && (priceRegex1.test($(this).val()) || priceRegex.test($(this).val()))) || (ao_type=='nopremium' && contrib_price > 0) || (pricedisplay=='no' && contrib_price > 0)))// && errtype!="time_out"
					{
						$(this).addClass("valid");
					}
					else
					{
							if(errtype!="time_out")
							{
								$(this).addClass("error");
								msg1="<li>You must give your rate for each of the selected offers before proceeding to stage 2</li>";
								error=error+1;
							}
						
					}    

				});
				//	Poll Price Validation
				$("[id^=poll_price_]").each(function(i) {

					var poll=$(this).attr('id').split("_");
					var poll_price_min=0,poll_price_max=0;

					var pollid=poll[2];
					var poll_type=poll[3];

					var poll_price=$(this).val().replace(",",".");
					var priceRegex=/^[0-9]+(\.?[0-9]+)?$/;
					var priceRegex1=/^[0-9]+(,?[0-9]+)?$/;
					
					if(poll_type=='premium')
					{
						var request = $.ajax({
							url: "/contrib/getpollpricerange",
							type: "POST",
							data: {pollid : pollid},
							async: false,
							dataType: 'json',
							success: function(data) {
								poll_price_min=data.poll_price_min;
								poll_price_max=data.poll_price_max;	
																
							}
						});					
					}	
					else if(poll_type=='nopremium')
					{
						var date=$("#ipoll_limit_"+pollid).val();
						
					}
					
					poll_price=parseFloat(poll_price);
					//alert(poll_price+'--'+poll_price_min+'--'+poll_price_max+'--'+pollid);

					if((poll_price>=poll_price_min && poll_price<=poll_price_max && (priceRegex1.test($(this).val()) || priceRegex.test($(this).val()))) || (poll_type=='nopremium' && poll_price > 0) || (poll_type=='premium' &&  poll_price>=0  && (!poll_price_max || poll_price_max==0 || poll_price_max=='')))
					{
						$(this).addClass("valid");
					}
					else
					{
						$(this).addClass("error");
						error=error+1;
						msg1="<li>You must give your rate for each of the selected offers before proceeding to stage 2</li>";
					}    

				});
				
				//	Correction Price Validation
				$("[id^=corrector_price_]").each(function(i) {
					var errtype='';
					var correction=$(this).attr('id').split("_");
					var correction_price_min=0,correction_price_max=0;

					var correctionid=correction[2];
					var correction_type=correction[3];

					var correction_price=$(this).val().replace(",",".");
					var priceRegex=/^[0-9]+(\.?[0-9]+)?$/;
					var priceRegex1=/^[0-9]+(,?[0-9]+)?$/;
					
					if(correction_type=='premium')
					{
						var request = $.ajax({
							url: "/contrib/getcorrectorarticlepricerange",
							type: "POST",
							data: {articleid : correctionid},
							async: false,
							dataType: 'json',
							success: function(data) {
								if(data.error=="time_out")
								{
									errtype="time_out";
									expiredkeys.push(correction[2]);
								}	
								else
								{
									correction_price_min=data.price_min;
									correction_price_max=data.price_max;	
								}							
							}
						});					
					}	
					
					correction_price=parseFloat(correction_price);


					if((correction_price>=correction_price_min && correction_price<=correction_price_max && (priceRegex1.test($(this).val()) || priceRegex.test($(this).val()))) || (correction_type=='nopremium' && correction_price > 0))
					{
						$(this).addClass("valid");
					}
					else
					{ 
						if(errtype!="time_out")
						{
							$(this).addClass("error");
							error=error+1;
							msg1="<li>You must give your rate for each of the selected offers before proceeding to stage 2</li>";
						}
					}    

				});
				
				
				var names = {};
				$('input:radio').each(function()
				{
					names[$(this).attr('name')] = true;
				});
				var count = 0;
				$.each(names, function(key, value)
				{
					count+=1;
					var article=key.split("_");
					var errorid=article[1];

					if($('input[name='+key+']').is(':checked'))
					{
					   var checked_value=$('input[name='+key+']:checked').val();
						//alert(checked_value);
						if(checked_value=='yes')
						{
							$('#accept_'+errorid).parents("label:first").removeClass("error");
							$('#pollaccept_'+errorid).parents("label:first").removeClass("error");
							$('#correctionaccept_'+errorid).parents("label:first").removeClass("error");
						}
						else
						{
							if(jQuery.inArray(errorid, expiredkeys) == -1)
							{
								$('#accept_'+errorid).parents("label:first").addClass("error");
								$('#pollaccept_'+errorid).parents("label:first").addClass("error");
								$('#correctionaccept_'+errorid).parents("label:first").addClass("error");
								error=error+1;
								msg2='<li>Please tick a box</li>';
							}
						}

					}
					else
					{
						if(jQuery.inArray(errorid, expiredkeys) == -1)
						{
							$('#accept_'+errorid).parents("label:first").addClass("error");
							$('#pollaccept_'+errorid).parents("label:first").addClass("error");
							$('#correctionaccept_'+errorid).parents("label:first").addClass("error");
							error=error+1;
							msg2='<li>Please tick a box</li>';
						}
					}
				});

				if(error>0)
				{
					$('html, body').animate( { scrollTop: top }, 'slow' );
					bootstrap_alert.error(msg1+msg2);
					return false;
				}
				else
				{
					if(count==1 && expiredkeys.length==1)
					{
						bootstrap_alert.error("Unfortunately, the participation time is out, you cannot participate to this mission anymore");
						return false;
					}
					else
						return true;
				}
			
			//return false;				
		});
		//Contract Validation
		$("#contract-submit").click(function(){
		
			var errorcount=0;
			
			var contract_text=$("#contract").html();	
			
			
			if($('input:radio[name=contract]:checked').val()=='yes')
				$(this).parents("label:first").removeClass("error");
			else
			{
				$(this).parents("label:first").addClass("error");
				bootstrap_alert.error('Please tick a box');
					errorcount+=1;
			}
			if($('input:radio[name=contract1]:checked').val()=='yes')
				$(this).parents("label:first").removeClass("error");
			else
			{
				$(this).parents("label:first").addClass("error");
				bootstrap_alert.error('Please tick a box');
				errorcount+=1;
			}
			if (errorcount > 0)
			{
				var confirm_message='Are you sure you want to refuse the terms and conditions of the contract and end your participations ?';
				var heading='Confirm';
				openConfirmModal(heading,confirm_message);
				return false;
			}
			else
			{
				$("#contract_text").val(contract_text);	
				return true;
			}
		
		});
		
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
					$("#sendto_error").html('');
				}	
				else
				{
					sendto.addClass("error");
					$("#sendto_error").html('send to compulsory');
					error+=1;
				}
			}
			if(mail_object.length)
			{	
				if($.trim(object).length <1 || object=='')
				{
					mail_object.addClass("error");
					$("#mail_object_error").html('Subject  compulsory');
					error+=1;
				}	
				else
				{
					$("#mail_object_error").html('');
					mail_object.removeClass("error");
				}
			}	
			if($.trim(message).length <1 || message=="")
			{
				mail_message.addClass("error");
				$("#mail_message_error").html('Message compulsory ');
				error++;
			}
			else
			{
				mail_message.removeClass("error");
				$("#mail_message_error").html('');
			}	
			if(error>0)
			{
				if(sendto.length)
				{
					$('html, body').animate( { scrollTop: top }, 'slow' );
				}	
				return false;
			}	
			else
				return true;

		});		
		
});	

function openConfirmModal(heading,message) 
{
	$("#confirmDiv").confirmModal({	
		heading: heading,
		body: message,
		callback: function () {
			window.location="/contrib/home/";
		}
	});
}	

