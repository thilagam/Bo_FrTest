	/**profile validation*/
 $(document).ready(function() {			 
			jQuery.validator.addMethod("telephone", function(phone_number, element) {
				phone_number = phone_number.replace(/\s+/g, ""); 
				return this.optional(element) || phone_number.length > 9 &&
				phone_number.match(/^([0-9\(\)\/\+ \-]*)$/);
			}, "Please enter a valid phone number");
			
			jQuery.validator.addMethod("ssnRegex", function(ssn, element) {
				ssn = ssn.replace(/\s+/g, ""); 
				return this.optional(element) || ssn.length == 15 &&
				ssn.match(/^([0-9]{15})?$/);
			}, "Please enter a valid social security number");
			
			jQuery.validator.addMethod("company_numberRegex", function(company_number, element) {
				company_number = company_number.replace(/\s+/g, ""); 
				return this.optional(element) || company_number.length == 14 &&
				company_number.match(/^([0-9]{14})?$/);
			}, "Please enter a valid SIRET");
			
			jQuery.validator.addMethod("country_france", function(country, element) {
				
				var payinfo=$('input:radio[name=payinfo]:checked').val();
				if(payinfo=='out_uk' && country==156)
					return false;
				else
					return true;
			}, "Please specify a country other than UK");
			
			jQuery.validator.addMethod("zip_code_regex", function(zipcode, element) {
				
				var country=$("#country").val();
				if(country==156)
					return this.optional(element) || zipcode.match(/^([0-9A-Za-z]{5,6,7})?$/);
				else
					return true; 				
			}, "Please enter a valid postcode");
			
			jQuery.validator.addMethod("bicRegex", function(rib_id_6, element) {
				rib_id_6 = rib_id_6.replace(/\s+/g, ""); 
				return this.optional(element) || rib_id_6.length == 8 || rib_id_6.length == 11 &&
				rib_id_6.match(/^([0-9A-Za-z\-]{8,11})?$/);
			}, "Please enter a Valid BIC code");
			
			jQuery.validator.addMethod("pwcheck", function(value) {
			   return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value)
			});
			
			var ribCheck2 = {
				required: function(element) {
							return $('input:radio[name=payment_type]:checked').val()=='virement';
				}
			};
			var ribCheckMessage= {
				required:"Please add your bank details"
			};
		 
		 $("#contribProfile").validate({
				rules:{
					initial:"required",
					password: {
                                required: true,   
								/*pwcheck: true,*/
                                minlength: 6
                            },
					password2: {
                                required: true,
								minlength: 6,
                                equalTo: "#password"
                            },		
					first_name:"required",
					last_name:"required",
					//self_details:{required:true,minlength: 100},
					language:"required",					
					nationality:"required",
					address:"required",
					phone_number:{required:true,telephone:true},
					city:"required",
					zipcode:{
							required:function(element) {
								return $("#country").val()!=156;
							},
							zip_code_regex:function(element) {
										return $("#country").val();
									}			
							},
					zipcode1:{
							required:function(element) {
								return $("#country").val()==156;
							},
							minlength:2			
							},
					zipcode2:{
							required:function(element) {
								return $("#country").val()==156;
							},
							minlength:3			
							},
					country:{required:true,
							country_france:function(element) {
										return $('input:radio[name=payinfo]:checked').val()=='out_uk';
									}		
						},
					entreprise:"required" ,					
					payment_type:"required",	
					paypal_id:	{required: function(element) {
							return $('input:radio[name=payment_type]:checked').val()=='paypal';
							},email:true
						},					
					rib_id_6: {required: function(element) {
										return $('input:radio[name=payment_type]:checked').val()=='virement';
									},
								bicRegex: function(element) {
									return $('input:radio[name=payment_type]:checked').val()=='virement';
								}
							 },
					rib_id_7: ribCheck2,
					bank_account_name: ribCheck2					
					
				},
				
				messages:{
					initial:"Gender compulsory",
					password: {
                                required: "Password compulsory",                                
                                minlength: "password must be longer than 6 characters"
                            },
					password2: {
                                required: "Password compulsory",
								minlength: "password must be longer than 6 characters",
                                equalTo: "password must be the same"
                            },		
					first_name:"First name compulsory",
					last_name:"Last name compulsory",
					/*self_details:{	required:"Merci d'ins&eacute;rer un texte de pr&eacute;sentation",
									minlength:"100 caract&egrave;res minimum"
								 },*/		
					language:"Mother tongue compulsory",
					nationality:"Nationality compulsory",
					address:"Address compulsory",
					phone_number:{required:"Phone compulsory",
								  telephone:"Please enter a valid phone number"		
								 },	
					city:"City compulsory",
					zipcode:{
							required:"Postcode compulsory",
							zip_code_regex:"Please enter a valid postcode"
							},
					zipcode1:{
							required:"Postcode compulsory",
							minlength:"Minimum length should be 2"
							},
					zipcode2:{
							required:"Postcode compulsory",
							minlength:"Minimum length should be 3"
							},
					country:{required:"Country compulsory",country_france:"Please specify a country other than UK"},
					
					entreprise:"Business compulsory",					
					payment_type:"Invoicing information",
					paypal_id:"Please add your Paypal email",					
					rib_id_6:ribCheckMessage,
					rib_id_7:ribCheckMessage,
					bank_account_name: ribCheckMessage
				},
				//errorClass: "help-inline",
				errorClass: "error",
				errorElement: "label",
				errorPlacement: function (error, element) {
					if (element.is("input:radio")) {
							element.parents("div:first").append(error);
						} 						
						else if ($.inArray(element.attr("id"),['rib_id_1','rib_id_2','rib_id_3','rib_id_4','rib_id_5','rib_id_6','rib_id_7','paypal_id','bank_account_name'])> -1) {
							$("#payment_error").html(error);
						}
						else if($.inArray(element.attr("id"), ['ssn','company_number','VAT_number'])> -1) {						
							$("#payinfo_error").html(error);
						}	
						else if($.inArray(element.attr("id"), ['JobName_1','CompanyName_1','ep_job_1','still_working_1','start_year_1','end_year_1'])> -1) {						
							$("#job_error").html(error);
						}
						else if($.inArray(element.attr("id"), ['trainingName_1','trainingSchoolName_1','still_training_1','start_train_year_1','end_train_year_1'])> -1) {						
							$("#edu_error").html(error);
						}
						else {
							element.after(error);
						}
				},
				highlight: function(label) {
					$(label).addClass('error');
					$(label).removeClass('success');
				},
				success: function(label) {
					//label.addClass('success');
					//label.removeClass('error');
				}	
				/* highlight: function(label) {
					$(label).closest('.control-group').addClass('error');
					$(label).closest('.control-group').removeClass('success');
				},
				success: function(label) {
					label.text('').addClass('valid').closest('.control-group').addClass('success');
					label.closest('.control-group').removeClass('error'); 
				}	*/
				
			}); 
			$("#ep_category_0").rules("add", {
				required:true,
				 messages: {
					required: "Skills compulsory"
				}	
			});
			
			//Job details Validation for first Div
			$("#job_more_1 :input").each(function() {				
				
				if($(this).attr('name')=='end_year[]')
				{
					$(this).rules('add', {
						required: function(element) {
									return !$("#still_working_1").is(":checked");
							},
						messages: {
						   required: "required field"						   
						 }		
					});
				}
				else if($(this).attr('name')=='still_working[]')
				{
					$(this).rules('add', {
						required: function(element) {
									if($("#end_year_1").val())
										return false;
									else
										return true;
							},
						messages: {
						   required: "required field"						   
						 }		
					});
				}
				else				
				{
					$(this).rules('add', {
						required: true,
						messages: {
						   required: "required field"						   
						 }						
					});
				}
			});
			//Education details Validation for first Div
			$("#training_more_1 :input").each(function() {				
				
				if($(this).attr('name')=='end_train_year[]')
				{
					$(this).rules('add', {
						required: function(element) {
									return !$("#still_training_1").is(":checked");
							},
						messages: {
						   required: "required field"						   
						 }		
					});
				}
				else if($(this).attr('name')=='still_training[]')
				{
					$(this).rules('add', {
						required: function(element) {
									if($("#end_train_year_1").val())
										return false;
									else
										return true;
							},
						messages: {
						   required: "required field"						   
						 }		
					});
				}
				else				
				{
					$(this).rules('add', {
						required: true,
						messages: {
						   required: "required field"						   
						 }						
					});
				}
			});		
						
			
			
			$("input:radio[name=payinfo]").click(function(){
				var selected=$(this).val();
				
				if(selected=='comp_num')
				{
					$('label[for="ssn"]').hide();
					$('label[for="company_number"]').show();
					if(!$("#ssn").val().match(/^([0-9]{15})?$/))
						$("#ssn").val('');
				}	
				else if(selected=='ssn')
				{
					$('label[for="company_number"]').hide();	
					$('label[for="ssn"]').show();
					if(!$("#company_number").val().match(/^([0-9]{14})?$/))
						$("#company_number").val('');
				}	
				else
				{
					$('label[for="company_number"]').hide();	
					$('label[for="ssn"]').hide();
				}	
				
			});
	});		