$(document).ready(function(){

	 $('body').on('hidden.bs.modal', '.modal', function () {
	 $(this).removeData('bs.modal');
	 $('.modal-backdrop').remove();
		});

	//$("#language").selectpicker();
	$("#language").chosen({ allow_single_deselect: false,search_contains: true});
	$("#mission_length_option").selectpicker();
	$("#delivery_volume_option").selectpicker();
	$("#tempo_length_option").selectpicker();
	$("#tempo_type").selectpicker();


	
		
	$('body').on('change','#duration_dont_know', function (event) {
		
		if($(this).is(":checked")==true)
		{
			$("#mission_duration_details").addClass('hidden');
		}	
		else
		{
		$("#mission_duration_details").removeClass('hidden');
		}
		
		//oneshotVolume('change');
	});
	
	/*$('body').on('change','.dont_know', function (event) {
		var id=this.id.split("_");
		var index=id[3];		
		$("#mission_duration_details").removeClass('hidden');
		oneshotVolume('change');
	});*/
		
	$('body').on('change','[id^=oneshot]', function (event) 
	{
	
		var value=this.value;
		
		if(value=='no')
		{
			$("#tempo_details").show();
			$(".oneshot-no").removeClass('hidden');
			$(".oneshot-yes").addClass('hidden');
			$(".oneshot-no").show();
			volume=$('#volume-no').val();
			$("#volume").val(volume);
			$("#volume-yes").val(volume);
			oneshotVolume('change');
			
		}
		else	
		{
			calculatedDurationonConfig()
			$("#tempo_details").hide();
			$(".oneshot-yes").removeClass('hidden');
			$(".oneshot-no").addClass('hidden');
			$(".oneshot-no").hide();
			$(".btn-next").removeAttr('disabled');
		}
	});
	
	//$('body').on('change','[id^=product]', function (event) 
	$('input[name=product]').change(function(event)
	{
		//var valuedes=this.value;
		var valuedes=$('input[name=product]:checked').val();		
		//alert(valuedes);
		
		if(valuedes=='translation')
		{
			$("#languagedest").chosen({ allow_single_deselect: false,search_contains: true});
			 $(".language-label").removeClass('hidden');	
			$("#languagedest").next().removeClass('hidden');
			 $(".tech-miss").addClass('hidden');
			$(".tech-mission").addClass('hidden');	
			$("#tech_mission_length").addClass('hidden');	
			$("#mission_cost").addClass('hidden');	
		 $(".mission-default").removeClass('hidden');	
		 $(".tran-language").addClass('hidden');
		 $("#mission_cost").addClass('hidden');	
		 $("#tech_mission_length_option").addClass('hidden');	
		 $("#tech_mission_length").addClass('hidden');
		 $('.content_strategist').addClass('hidden');
		}
		else if(valuedes=='tech')
		{
			$("#tech_mission_length_option").removeClass('hidden');	
			$("#tech_mission_length_option").selectpicker();
			$(".oneshot-yes").addClass('hidden');
			$(".language-label").addClass('hidden');	
			$(".tech-miss").removeClass('hidden');	
			$("#mission_cost").removeClass('hidden');	
			$("#tech_mission_length").removeClass('hidden');
			
			$(".mission-default").addClass('hidden');
			$("#mission_cost").removeClass('hidden');
			$("#languagedest").next().addClass('hidden');
			$("#languagedest-error").addClass('hidden');
			$("#languagedest_chosen").addClass('hidden');
			$("#languagedest_chosen").removeClass("chzn-done" ).addClass("");	
			$('.content_strategist').addClass('hidden');	

			prod_mission_val=$('#prod_mission_selected').val();
				if(prod_mission_val)
				{
					techtitleval=$('#tech_type').val();
					if(techtitleval !="")
					{
					techtypeid="&typeid="+$('#tech_type').attr('rel');	
					}
					else
					{
					techtypeid="";
					}
				$("#tech_title").selectpicker();	
					/*var target_page = "/quote-new/tech-title-select?prod_mission_val="+prod_mission_val;
					$.post(target_page, function(data){					
					var select = $('#tech_title');
						select.empty().html(data);
					$('#tech_title').attr("data-placeholder","Please select a title")
					
					$('#tech_title').trigger("chosen:updated");
					$("#tech_title").addClass('hidden');
					
						});				*/

				}
			$(".tech-mission").removeClass('hidden');		 
		}
		else if(valuedes=='content_strategy')
		{
			$("#seo_product").selectpicker();
			$("#strategy_mission_length_option").selectpicker();
			$(".language-label").addClass('hidden');	
			$("#languagedest").next().addClass('hidden');
			$("#languagedest-error").addClass('hidden');
			$("#languagedest_chosen").addClass('hidden');
			$("#languagedest_chosen").removeClass("chzn-done" ).addClass("");	
			$("#mission_cost").addClass('hidden');	
			 $("#tech_mission_length_option").addClass('hidden');	
			 $("#tech_mission_length").addClass('hidden');
			 $(".tech-miss").addClass('hidden');
			 $(".tech-mission").addClass('hidden');	
			$(".tran-language").addClass('hidden');
			$(".tran-language").hide(); 
			 $(".mission-default").addClass('hidden');
			 $('.content_strategist').removeClass('hidden');	
		}
		else
		{	
		$("#mission_cost").addClass('hidden');	
		 $("#tech_mission_length_option").addClass('hidden');	
		 $("#tech_mission_length").addClass('hidden');	
			$(".language-label").removeClass('hidden');	
			$("#languagedest").next().addClass('hidden');
			$("#languagedest-error").addClass('hidden');
			$("#languagedest_chosen").addClass('hidden');
			$("#languagedest_chosen").removeClass("chzn-done" ).addClass("");
			 $(".tech-miss").addClass('hidden');
			 $(".tech-mission").addClass('hidden');	
			$(".tran-language").addClass('hidden');
			$(".tran-language").hide();
			$('.content_strategist').addClass('hidden');
			$(".mission-default").removeClass('hidden');	
		}
	});				
	
	$("body").on('change keyup keypress',"[id^=volume-no],[id^=tempo_type],[id^=volume_max],[id^=tempo_length],[id^=tempo_length_option]", function() {
   
		oneshotVolume('change');
		
	});

	$("body").on('change keyup keypress',"[id^=tech_mission_length],[id^=tech_volume_max],[id^=tech_tempo_length],[id^=tech_tempo_length_option]", function() {
   
		techoneshotVolume('change');
		
	});

	if($("#oneshot:checked").val()=='yes')
	{
		$("body").on('change keyup keypress',"#volume-yes", function() {
			$("#volume").val($(this).val());
			$('#volume-no').val($(this).val())
			
			calculatedDurationonConfig();
		});
	}

	$("body").on('change keyup keypress',"#mission_length", function() {

			duration_missionval=$(this).val();
			
				volumeMax=$('#volume_max').val();
				deleveryvolumeoption=$('#delivery_volume_option').val();
				tempoLength=$('#tempo_length').val();
				tempoLengthoption=$('#tempo_length_option').val();
				durationknow=$('#duration_dont_know').is(":checked");
				$(this).find(".alert-danger").remove();
				
				if(tempoLengthoption=='days'){
					 tempo_callenth=tempoLength;
				}else if(tempoLengthoption=='week')	{
					tempo_callenth=tempoLength*7;
				}
				else if(tempoLengthoption=='month')	{
					tempo_callenth=tempoLength*30;
					}
				else if(tempoLengthoption=='year')	{
					tempo_callenth=tempoLength*365;
					}

			if(durationknow==false) caltotval=Math.round((duration_missionval/tempo_callenth)*volumeMax);
					else  caltotval= volumeMax;	

			if(volumeMax!="" && deleveryvolumeoption!="")
			{
				if($.isNumeric(caltotval) && caltotval!=0)
				{
					$('#volume-no').val(caltotval);
					$('#volume').val(caltotval);
				}
			}

		});
	



	
		$(".btn-next").on("click",function()
		{
			
			var product_val=$("#product:checked").val();

			$('#product').radio();
			//var language_val=$("#language span.filter-option").text();
			var language_val=$("#language_chosen a span").text();
			
			
			var producttype_val=$("#producttype span.filter-option").text();
			var words_val=$("#nb_words").val();
			
				if($("#oneshot:checked").val()=='yes')
				{
				oneshot_val='oneshot';
				$(".oneshot-yes").removeClass('hidden');
				$(".oneshot-no").addClass('hidden');
				$(".oneshot-no").hide();
				var volume_val=$("#volume-yes").val();
				$("#volume").val(volume_val);
				}
				else
				{
				$(".oneshot-no").removeClass('hidden');
				$(".oneshot-no").show();
				$(".oneshot-yes").addClass('hidden');
				volume_val=$("#volume-no").val();
				oneshotVolume('change');
				$("#volume").val(volume_val);
				var volmax=$("#volume_max").val();
				var tempotype=$("#tempo_type span.filter-option").text();
				var delivery_vol=$("#delivery_volume_option span.filter-option").text();
				var tempo_lenth=$("#tempo_length").val();
				var tempo_lenth_op=$("#tempo_length_option span.filter-option").text();
				oneshot_val="<p>Recurring</p> <p>volume "+volmax+" "+tempotype+" "+delivery_vol+" "+ tempo_lenth +" "+tempo_lenth_op+"</p>";	
				}

			
			
			
			
			  if(product_val=='tech')
			  {
			  	$("#tech_mission_length_option").selectpicker();
			  	$("#tech_delivery_volume_option").selectpicker();
				$("#tech_tempo_length_option").selectpicker();
				$("#tech_tempo_type").selectpicker();
				$(".oneshot-yes").addClass('hidden');
				 $(".language-label").addClass('hidden');	
				 $(".tech-miss").removeClass('hidden');	
			  	$("#mission_cost").removeClass('hidden');	
				 $("#tech_mission_length").removeClass('hidden');	
			 	$(".mission-default").addClass('hidden');	
				$("#tech_mission_length_option").removeClass('hidden');	
				$("#mission_cost").removeClass('hidden');
				$("#languagedest").addClass('hidden');
				$("#languagedest-error").addClass('hidden');
				$("#languagedest_chosen").addClass('hidden');
				$("#languagedest_chosen").removeClass("chzn-done" ).addClass("");	
				$('.content_strategist').addClass('hidden');
			  	$(".oneshot-yes").addClass('hidden');
				$('#mission-type').html('Tech & Misc');
				$(".tech-mission").removeClass('hidden');
				var missin_title=$("#tech_title span.filter-option").text();  
				var missin_cost=$("#mission_cost").val();  
				var missin_language=$("#language_chosen  a span").text();  
				var volume_val=$("#tech_volume").val();  

				var duration_val=$("#tech_mission_length").val()+' '+$("#tech_tempo_length_option span.filter-option").text();  
				
				
				linkedto=$("#prod_mission_selected").val();
				if(linkedto=='Yes')
				{
					var prodMissionval=$("#prodmissionslist span.filter-option").text();

					linkedtoValue=prodMissionval;
				}
				else
				{
					linkedtoValue=linkedto;
				}
				oneshot=$("#tech_oneshot:checked").val();
				if(oneshot=='yes')
				{
					mission_length_val='One shot';  
					$(".tech_volume_div").removeClass('hidden');
					$(".tech_mission_length_div").addClass('hidden');
				}
				else
				{
				$(".tech_mission_length_div").removeClass('hidden');
				$(".tech_volume_div").addClass('hidden');
				techoneshotVolume('change');
				var volmax=$("#tech_volume_max").val();
				var tempotype=$("#tech_tempo_type span.filter-option").text();
				var delivery_vol=$("#tech_delivery_volume_option span.filter-option").text();
				var tempo_lenth=$("#tech_tempo_length").val();
				var tempo_lenth_op=$("#tech_tempo_length_option span.filter-option").text();
				mission_length_val="<p>Recurring</p> <p>volume "+volmax+" "+tempotype+" "+delivery_vol+" "+ tempo_lenth +" "+tempo_lenth_op+"</p>";	
					 
				}
				$("#mission-title").html(missin_title); 

				var to_perform=$("#to_perform:checked").val()+' prod mission';  
				$("#oneshot-tempo").html(mission_length_val);
				$("#mission-perform").html(to_perform);
				$("#tech-mission-cost").html(missin_cost); 
				$("#mission-language1").html(missin_language);
				$('#total-vol').html(volume_val);
				$('#duration-val').html(duration_val);
				$("#linkedto").html(linkedtoValue);
				$('.content-strategy').addClass('hidden');
			  }
			  else if(product_val=='content_strategy')
			  {
			  	$('.content-strategy').show();
			  	$('.mission-default').addClass('hidden');
			  	$('.content-strategy').removeClass('hidden');
			  	$('#mission-type').html('Content Strategy'); 
			  	$('#content-language1').html(language_val);
			  	
			  	seomission_length_val=$('#strategy_mission_length').val();
			  	seomission_length_option=$('#strategy_mission_length_option  span.filter-option').text();
			  	//alert(seomission_length_option);
			  	$('#content-duration').html(seomission_length_val+' '+seomission_length_option); 
			  	mission_prod=$('#seo_product span.filter-option').text();
			  	$('#seo-mission-cost').html(mission_prod);
			  }
			  else
			  {
				  if(product_val=='redaction')
				  {
					$('#mission-type').html('Writing');  
					$("#producttype").find('[value="wordings"]').remove();
				  }
				  else
				  {
					$('#mission-type').html('Translation'); 

					//var languagedest_val=$("#languagedest span.filter-option").text();
					var languagedest_val=$("#languagedest_chosen a span").text();

					 if($('#producttype option[value="wordings"]').length==0)
					 {
						$("#producttype option:last").before('<option configwords="650" value="wordings">Wording</option>'); 	
					 }
					
					
					
					//	$("#producttype").append('<option configwords="650" value="wordings">Wordings</option>');
					
					
					$(".tran-language").show();
					$(".tran-language").removeClass('hidden');
					$('#mission-language2').html(languagedest_val);
				
				  }
				  if($("#mission_duration_details").hasClass('hidden'))
				  {
	    			  mission_length_val="Je ne sais pas";
	    		   }
				  else
				   {
     				var mission_length_val=$("#mission_length").val()+' '+$("#mission_length_option span.filter-option").text();
					}
				
				$('#mission-language1').html(language_val);
				$('#product-lang').html(producttype_val);
				$('#pro-words').html(words_val);
				$('#oneshot-tempo').html(oneshot_val);
				$('#total-vol').html(volume_val);
				$('#duration-val').html(mission_length_val); 
				$('.content-strategy').addClass('hidden');
			  }
			  
			  
			  
			});
		
			$(".btn-previous").on("click",function(){
				$(".btn-next").removeAttr('disabled');
				var product_val=$("#product:checked").val();
				if(product_val=='content_strategy')
				{	
					$('ul.nav-pills li a[href="#mission-step1"]').tab('show');
				}
			});
			
			
});
    
    function checklanguage(){

	
		/*var current_id = $(this).attr("id");
		var current_index = current_id.substring(current_id.indexOf("_")+1);
		var current_type = $("#product_"+current_index).val();*/
		var current_type = $("#product").val();
		if(current_type=="translation")
		{
			$('.checklanguage').closest(".form-group").find(".alert-danger").remove();
			if($("#language").val()==$(this).val())
			{
				$('.checklanguage').closest(".form-group").append("<div class='alert-danger' style='padding:0 5px;'>Merci de s&eacute;lectionner une langue diff&eacute;rente de la langue m&egrave;re</div>");
				$("#product_chzn .chzn-single").focus();
				language_error = false;	
				return false;
			}
			else
				language_error = true;
		}
	
	return true;
}

function fnCheckProductType(element)
{	
	var productType=element.value;
	
	/*var mission_details=(element.id).split("_");
	var mission_id=mission_details[1];*/
	
	$("#producttypeotherdiv").hide();
	
	if(productType=='autre')
	{
		$("#nb_words").removeAttr("min").removeAttr("max");
		$("#producttypeotherdiv").show();
		/*$("#nb_words").removeAttr("class");*/
	}
	else if(productType=='article_de_blog'){
		$("#nb_words").attr("min",'250');
		$("#nb_words").attr("max",'500');
	   }
	   else if(productType=='news'){
		$("#nb_words").attr("min",'50');
		$("#nb_words").attr("max",'200');
		   }
	   else if(productType=='descriptif_produit'){
		$("#nb_words").attr("min",'30');
		$("#nb_words").attr("max",'150');
		   }
	   else if(productType=='guide'){
		$("#nb_words").attr("min",'500');
		$("#nb_words").attr("max",'1500');
		   }
	   else if(productType=='wordings'){
		$("#nb_words").attr("min",'1');
		$("#nb_words").attr("max",'1');
		   }
		else{
			$("#nb_words").removeAttr("min").removeAttr("max");
		}
		
}


function calculatedDurationonConfig()
{
				volume=$("#volume").val();
				nbwords=$("#nb_words").val();
				calculatedVal=nbwords*volume;
				prodval=$("#producttype option:selected").val();
				configprod=$("#producttype option:selected").attr("configwords");
				max_writer=$("#nb_words").attr('rel');
				configureval=max_writer*configprod;
				durationVal=Math.ceil(calculatedVal/configureval);
				//alert(' calculate '+calculatedVal+' config '+ configureval+' final '+durationVal);
				if(durationVal<=0)
						durationVal=1;
				/*assign mission length*/
				if(prodval!='autre')
				$("#mission_length").val(durationVal);
}

function oneshotVolume(status)
{
	
	$('.oneshotVolume').each(function() {
		
		/*var current_id = $(this).attr("id");
		var current_details = (current_id).split("_");*/
	    var current_index="";
	    var oneshotval= $("input:radio[name=oneshot"+ current_index +"]:checked").val();
		
		if(oneshotval=='no' ){
			var nb_words=$("#nb_words").val();
			duration_missionval=$('#mission_length'+current_index).val();
			totalvolumeval=$('#volume-no').val();
			volumeMax=$('#volume_max'+current_index).val();
			tempotype=$('#tempo_type'+current_index).val();
			tempotypeconfig=$('#tempo_type option:selected').attr('tempoconfig');
			configval=tempotypeconfig.split('-');
			

			deleveryvolumeoption=$('#delivery_volume_option'+current_index).val();
			tempoLength=$('#tempo_length'+current_index).val();
			tempoLengthoption=$('#tempo_length_option'+current_index).val();
			durationknow=$('#duration_dont_know'+current_index).is(":checked");
			$(this).find(".alert-danger").remove();
			
			if(tempoLengthoption=='days'){
				 tempo_callenth=tempoLength;
			}else if(tempoLengthoption=='week')	{
				tempo_callenth=tempoLength*7;
			}
			else if(tempoLengthoption=='month')	{
				tempo_callenth=tempoLength*30;
				}
			else if(tempoLengthoption=='year')	{
				tempo_callenth=tempoLength*365;
				}
				calculatedtval=(parseInt(configval[0])/parseInt(configval[1]));
				configcal=(calculatedtval)*tempo_callenth;
				tempocalculated=(nb_words*volumeMax);
				//alert(configcal+ ' test '+tempocalculated);
				
				 if(durationknow==false) caltotval=Math.round((duration_missionval/tempo_callenth)*volumeMax);
					else  caltotval= volumeMax;

					duration_missionval=Math.round((totalvolumeval)*tempo_callenth/volumeMax)
				
					if(volumeMax!="" && deleveryvolumeoption!="")
					{			
						if(status=='change' && $.isNumeric(duration_missionval) && duration_missionval!=0)
						{
							$('#mission_length').val(duration_missionval);
							
							totalvolumeval=caltotval;	
						}
						
						if(caltotval!=totalvolumeval && $.isNumeric(caltotval))
						{
								$(this).find('.col-md-12').after("<div class='alert-danger col-md-7 col-md-offset-3' style='padding:0 5px;'>Le tempo indiqu&#233; ne correspond pas au volume et &#224; la  dur&#233;e de la mission</div>");
								volume_error = false;
								$(".btn-next").attr('disabled','disable');
								return false;						
						}
						else if(configcal<tempocalculated)
						{
								$(this).find('.col-md-12').after("<div class='alert-danger col-md-7 col-md-offset-3' style='padding:0 5px;'> Massive volume for only one writer ("+configval[0]+" words "+configval[1]+" days) </div>");
								volume_error = false;
								$(".btn-next").attr('disabled','disable');
								return false;
						}
						else
						{ 
							$(".btn-next").removeAttr('disabled');
							volume_error = true;
						}
					}
						
							
			}
	});
	return true;
}


function techoneshotVolume(status)
{

	$('.techoneshotVolume').each(function() {
		
		/*var current_id = $(this).attr("id");
		var current_details = (current_id).split("_");*/
	    var current_index="";
	    var oneshotval= $("input:radio[name=tech_oneshot]:checked").val();
		
		if(oneshotval=='no'){
			
			duration_missionval=$('#tech_mission_length').val();
			totalvolumeval=$('#tech_volume').val();
			volumeMax=$('#tech_volume_max'+current_index).val();
			tempotype=$('#tech_tempo_type'+current_index).val();
			deleveryvolumeoption=$('#tech_delivery_volume_option'+current_index).val();
			tempoLength=$('#tech_tempo_length'+current_index).val();
			tempoLengthoption=$('#tech_tempo_length_option'+current_index).val();
			$(this).find(".alert-danger").remove();
			
			if(tempoLengthoption=='days'){
				 tempo_callenth=tempoLength;
			}else if(tempoLengthoption=='week')	{
				tempo_callenth=tempoLength*7;
			}
			else if(tempoLengthoption=='month')	{
				tempo_callenth=tempoLength*30;
				}
			else if(tempoLengthoption=='year')	{
				tempo_callenth=tempoLength*365;
				}
				
				 caltotval=Math.round((duration_missionval/tempo_callenth)*volumeMax);

				 duration_missionval=Math.round((totalvolumeval)*tempo_callenth/volumeMax)
					if(status=='change' && $.isNumeric(caltotval) && caltotval!=0)
					{
						
						$('#tech_volume'+current_index).val(caltotval);
						totalvolumeval=$('#tech_volume'+current_index).val();	
					}
				
					if(caltotval!=totalvolumeval && $.isNumeric(caltotval))
					{
							$(this).find('.col-md-12').after("<div class='alert-danger col-md-7 col-md-offset-3' style='padding:0 5px;'>Le tempo indiqu&#233; ne correspond pas au volume et &#224; la  dur&#233;e de la mission</div>");
									volume_error = false;
							return false;						
					}
					else volume_error = true;
						
							
			}
	});
	return true;
}

	
	
