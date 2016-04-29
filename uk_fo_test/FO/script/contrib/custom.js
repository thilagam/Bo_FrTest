var bigLoader='<img src="/FO/images/big-loader.gif"/>';
 $(document).ready(function() {	
 
	// tooltip activation
    $("[rel=tooltip]").tooltip();
	$("[rel=popover]").popover();
	 
	/**timer js**/
	$("[id^=time_]").each(function(i) {
			var article=$(this).attr('id').split("_");
			 
			var ts=article[2];
			 
			$("#time_"+article[1]+"_"+article[2]).countdown({
				timestamp	: ts,
				callback	: function(days, hours, minutes, seconds){

					var message = "";

					if(days>0)
						message += "<em>"+days + "</em> d" +" ";
					if(hours>0)	
						message += "<em>"+hours + "</em> h" +" ";
					if(minutes > 0)
					message += "<em>"+minutes + "</em> mn"+ " ";
					if(minutes < 1)
					message += "<em>"+seconds + "</em> s" + " ";
					$("#text_"+article[1]+"_"+article[2]).html(message);
					if(days==0 && hours==0 && minutes==0 && seconds==0)
					{
						//window.location.reload();
						
					}	
					
				}
				
			});
			
		});
		
		/*contract print*/
		$( "#link-print a" ).click(function(){
			$( "#contract" ).print();
		}); 	
		
		/**add more languages with sliders**/
		
		var lang=0;
		
		$("[id^=language_close_]").live('click', function() {
			
			var parentDiv=$(this).parents("div:first").attr('id');
			var closeDiv=$("#"+parentDiv).children(".close").attr('id');
			if($("[id^=language_more_]").size()>1)
			{
				$("#"+parentDiv).html('<center>'+bigLoader+' Deleting... </center>');
				$("#"+parentDiv).remove();
			}	
			else
			{
				$("#"+closeDiv).hide();
			}
				
				
		});			
		
		
		$( ".lang_slider" ).each(function(i) {
	            // read initial values from markup and remove that
				var value = parseInt( $( this ).text(), 10 );
				if(isNaN(value))
					value=50;
				//alert(i+'--'+value);
	            $( this ).empty().slider({
	                range: "min",
					value: value,
	                min: 1,
	           		max: 100,
	                slide: function( event, ui ) {
						$( "#slider-language_number_"+i ).val( ui.value +"%");
					}
	            });
				 $( "#slider-language_number_"+i ).val( $( "#slider-language_"+i ).slider( "value" ) +"%" );
				 lang=i;
	        });

		
		
		$("#addmore_lang_link").click(function(){
			
			var cloned = $("#addmore_lang_link");//$('#language_more_'+lang);
			$("#language_more_0").clone().attr('id', 'language_more_'+(++lang) ).insertBefore( cloned );
			
			var slider_number=$('#language_more_'+lang+' input');
			var slider=$('#language_more_'+lang+' .lang_slider');
			$('#language_more_'+lang+' .muted').attr('for','slider-language_number_'+lang);
			slider.attr('id','slider-language_'+lang);
			slider_number.attr('id','slider-language_number_'+lang);
			
			$('#language_more_'+lang+' .close').attr('id','language_close_'+lang);
			$('#language_more_'+lang+' .close').show();
			
			/**resetting the div**/
			clearChildren(document.getElementById('language_more_'+lang));	
			
			var slider_val=lang;
			$( "#slider-language_"+slider_val).html('');	
			// Slider language		
			$( "#slider-language_"+slider_val).slider({
				range: "min",
				value: 10,
				min: 1,
				max: 100,
				slide: function( event, ui ) {
					$( "#slider-language_number_"+slider_val ).val( ui.value +"%");
				}
			});
			$( "#slider-language_number_"+slider_val).val( $( "#slider-language_"+slider_val ).slider( "value" ) +"%" );
			
		});
		
		/**add more categories with sliders**/
		var skill=0;
		$("[id^=skill_close_]").live('click', function() {
			
			var parentDiv=$(this).parents("div:first").attr('id');
			var closeDiv=$("#"+parentDiv).children(".close").attr('id');
			if($("[id^=skill_more_]").size()>1)
			{
				$("#"+parentDiv).html('<center>'+bigLoader+' Deleting... </center>');
				$("#"+parentDiv).remove();
			}	
			else
			{
				$("#"+closeDiv).hide();
			}
				
				
		});
		
		
		$( ".skill_slider" ).each(function(j) {
	            // read initial values from markup and remove that
				var value = parseInt( $( this ).text(), 10 );
				if(isNaN(value))
					value=50;
					//alert(i+'--'+value);
	            $( this ).empty().slider({
	                range: "min",
					value: value,
	                min: 1,
	           		max: 100,
	                slide: function( event, ui ) {
						$( "#slider-skill_number_"+j ).val( ui.value +"%");
					}
	            });
				 $( "#slider-skill_number_"+j ).val( $( "#slider-skill_"+j ).slider( "value" ) +"%" );
				 skill=j;
	        });
		$("#addmore_skill_link").click(function(){
			
			var cloned =$("#addmore_skill_link");// $('#skill_more_'+skill);
			$("#skill_more_0").clone().attr('id', 'skill_more_'+(++skill) ).insertBefore( cloned );
			
			/*unselect the selected one**/
			$('#skill_more_'+skill+' select').attr('id','ep_category_'+skill);
			$('#skill_more_'+skill+' option:selected').removeAttr("selected");
			
			
			var slider_number=$('#skill_more_'+skill+' input');
			var slider=$('#skill_more_'+skill+' .skill_slider');
			$('#skill_more_'+skill+' .muted').attr('for','slider-skill_number_'+skill);
			slider.attr('id','slider-skill_'+skill);
			slider_number.attr('id','slider-skill_number_'+skill);	
			$( "#slider-skill_"+skill).html('');
			
			$('#skill_more_'+skill+' .close').attr('id','skill_close_'+skill);
			$('#skill_more_'+skill+' .close').show();
			
			/**resetting the div**/
				clearChildren(document.getElementById('skill_more_'+skill));		
			// Slider Skills		
			var slider_val=skill;
			$( "#slider-skill_"+slider_val).slider({
				range: "min",
				value: 10,
				min: 1,
				max: 100,
				slide: function( event, ui ) {
					$( "#slider-skill_number_"+slider_val ).val( ui.value +"%");
				}
			});
			$( "#slider-skill_number_"+slider_val).val( $( "#slider-skill_"+slider_val ).slider( "value" ) +"%" );
			
		});
		
/****************add more jobs*********************/
		var job=1;
		
		$("[id^=job_close_]").live('click', function() {
			var DivId = $(this).attr('id');
			var parentDiv=$(this).parents("div:first").attr('id');
			var job_identifier=$(this).attr('rel');
			var closeDiv=$("#"+parentDiv).children(".close").attr('id');
			if(!job_identifier)
				$("#"+parentDiv).remove();
			else
			{
				if($("[id^=job_more_]").size()>1)
				{
					$("#"+parentDiv).html('<center>'+bigLoader+' Deleting... </center>');
					ajaxProfileUpdate('job',job_identifier,parentDiv);
				}	
				else
				{
					$("#"+closeDiv).hide();
				}
			}	
				
		});	
		
		$("[id^=job_more_]" ).each(function(z) {
			job=++z;
		});
		$("#addmore_job_link").click(function(){
			
			var cloned =$("#addmore_job_link");
			$("#job_more_1").clone().attr('id', 'job_more_'+(++job) ).insertBefore( cloned );
			
			$('#job_more_'+job+' .collapse').addClass('in');
			$('#job_more_'+job+' .close').attr('id','job_close_'+job);
			$('#job_more_'+job+' .close').show();
			$('#job_more_'+job+' .close').attr('rel','');
			$('#job_more_'+job+' .collapse').attr('id','stillWorkingThere_'+job);
			$('#job_more_'+job+' input:checkbox').attr('data-target','#stillWorkingThere_'+job);
			
			clearChildren(document.getElementById('job_more_'+job));
		
		});	
		
/****************add more Trainings****************/
		var training=1;
		$("[id^=training_more_]" ).each(function(t) {
			training=++t;
		});
		$("[id^=training_close_]").live('click', function() {
			var DivId = $(this).attr('id');
			var parentDiv=$(this).parents("div:first").attr('id');
			var training_identifier=$(this).attr('rel');
			var closeDiv=$("#"+parentDiv).children(".close").attr('id');
			
			
			if(!training_identifier)
				$("#"+parentDiv).remove();
			else
			{
				
				if($("[id^=training_more_]").size()>1)
				{
					$("#"+parentDiv).html('<center>'+bigLoader+' Deleting... </center>');
					ajaxProfileUpdate('education',training_identifier,parentDiv);
				}	
				else
				{					
					$("#"+closeDiv).hide();
				}
			}	
				
		});
		$("#addmore_training_link").click(function(){
			
			var cloned =$("#addmore_training_link");
			$("#training_more_1").clone().attr('id', 'training_more_'+(++training) ).insertBefore( cloned );
			
			$('#training_more_'+training+' .collapse').addClass('in');
			$('#training_more_'+training+' .close').attr('id','training_close_'+training);
			$('#training_more_'+training+' .close').show();
			$('#training_more_'+training+' .close').attr('rel','');
			$('#training_more_'+training+' .collapse').attr('id','stillTrainingThere_'+training);
			$('#training_more_'+training+' input:checkbox').attr('data-target','#stillTrainingThere_'+training);
			clearChildren(document.getElementById('training_more_'+training));
		
		});	
				
			
		
		
		//alert($('input:radio[name=payinfo]:checked').val());
		
		//Royalties pop up css change
		var top=$(window).scrollTop();
		$('#billing-ajax').css('top', (parseInt(60) + ((top*2)/$(window).height())) + '%');
		
		//destroy the Modal object before subsequent toggles
		$('body').on('hidden', '.modal', function () {
			$(this).removeData('modal');
			$(".datepicker").hide();
		});	

		//date picker in cart page	
		$("[id^=date_limit_]").each(function(i) {
		var myDate=new Date();
		myDate.setDate(myDate.getDate()-1);
		
			$(this).datepicker({
				format: 'dd/mm/yyyy',
				startDate: myDate
			});		
		});	
		//date picker in cart page
		$("[id^=poll_date_limit_]").each(function(i) {
		var myDate=new Date();
		myDate.setDate(myDate.getDate()-1);
			$(this).datepicker({
				format: 'dd/mm/yyyy',
				startDate: myDate
			});		
		});	
		
		//Prev Next in Offer pop up
		$("[id^=to_modal_]").live('click',function(e) {
			e.preventDefault();
			var href = $(this).attr('href');						
			$("#viewOffer-ajax").removeData('modal');		
			$('#viewOffer-ajax .modal-body').load(href);
			$("#viewOffer-ajax").modal();
			$(".modal-backdrop:gt(0)").remove();
		});
		
});	
	
	
/*reset all childs of a div**/	
function clearChildren(element) {
   for (var i = 0; i < element.childNodes.length; i++) {
      var e = element.childNodes[i];
      if (e.tagName) switch (e.tagName.toLowerCase()) {
         case 'input':
            switch (e.type) {
               case "radio":
               case "checkbox": e.checked = false; break;
               case "button":
               case "submit":
               case "image": break;
               default: e.value = ''; break;
            }
            break;
         case 'select': e.selectedIndex = 0; break;
         case 'textarea': e.innerHTML = ''; break;
         default: clearChildren(e);
      }
   }
}	

/** ajax function to delete profile data**/
function ajaxProfileUpdate(type,identifier,divid)
{
        var target_page = "/contrib/delte-profile-data?type="+type+"&identifier="+identifier;
		$.post(target_page, function(data){						
				sleep(4000);
				$("#"+divid).remove();
								
			});
}
function sleep(ms)
{
	var dt = new Date();
	dt.setTime(dt.getTime() + ms);
	while (new Date().getTime() < dt.getTime());
}
function startTimer(time,text)
{
	/**timer js**/
	$("[id^="+time+"_]").each(function(i) {
			var article=$(this).attr('id').split("_");
			var ts=article[2];
			 
			$("#"+time+"_"+article[1]+"_"+article[2]).countdown({
				timestamp	: ts,
				callback	: function(days, hours, minutes, seconds){

					var message = "";

					if(days>0)
						message += "<em>"+days + "</em> d" +" ";
					if(hours>0)	
						message += "<em>"+hours + "</em> h" +" ";
					if(minutes > 0)
						message += "<em>"+minutes + "</em> mn"+ " ";
					if(minutes < 1)
						message += "<em>"+seconds + "</em> s" + " ";
					$("#"+text+"_"+article[1]+"_"+article[2]).html(message);
					if(days==0 && hours==0 && minutes==0 && seconds==0)
					{
						//window.location.reload();
						
					}	
					
				}
				
			});
			
		});
}
//timer in mission delivery page
function startMissionTimer(time,text)
{		
	$("[id^="+time+"_]").each(function(i) {
			var article=$(this).attr('id').split("_");
			var ts=article[2];
			
			$("#"+time+"_"+article[1]+"_"+article[2]).countdown({
				timestamp	: ts,
				callback	: function(days, hours, minutes, seconds){

					var message = "";

					if(days>0)
					{
						//message += days + "d" +" ";
						hours=hours+(days*24);
					}	
						
					if(hours>0)	
						message += hours + "h" +" ";
					if(minutes > 0)
						message += minutes + "mn"+ " ";
					if(minutes < 1)
						message += seconds + "s" + " ";				 	
					
					$("#"+text+"_"+article[1]+"_"+article[2]).html(message);
					if(days==0 && hours==0 && minutes==0 && seconds==0)
					{
						//window.location.reload();
						
					}	
					
				}
				
			});
			
		});
}
/**Cart Modifier function**/
function fnCartModifiers(action,articleID,from,deliveryID)
{
     if (typeof from == "undefined" || from=='') {
            from = "menu";
    
        }   
    var target_page;
	
	//var article_cookie='client_brief_'+articleID;
	var article_cookie='client_brief_'+deliveryID;
	var article_cookie_value=cookieManager.readCookie(article_cookie);		
	
	if(from=='menu')
		target_page = "/cart?from=menu&cart_action="+action+"&article_id="+articleID;
	else if(from=='main')
		target_page = "/cart?from=main&cart_action="+action+"&article_id="+articleID;
		
	var select='<button class="btn btn-large btn-primary" onClick="fnCartModifiers(\'add\',\''+articleID+'\',\''+from+'\',\''+deliveryID+'\');">' +
        'Add to my selection</button>';
	
    var selected='<button class="btn btn-large btn-danger" onClick="fnCartModifiers(\'remove\',\''+articleID+'\',\''+from+'\',\''+deliveryID+'\');">' +
        'Post added</button>';
	
	if(action=='remove' && from=='main')
		$("#cart_load_"+articleID).html('<center>'+bigLoader+'</center>');
	
    
	
	if((article_cookie_value==1 && action=='add') || (action=='remove'))
	{
		//added to check whether the given AO is linked to Quiz or not	
		var quizCheck;
		var quizCheckPage="/quiz/check-quiz-ao?article_id="+articleID;
		//alert(quizCheckPage);
		$.get(quizCheckPage,  function(response) {
			
			quizCheck=response.status;				
			if(quizCheck=="YES" && action=='add' )
			{
				$('#gotoQuizz').modal();
			}
			else
			{
				$.post(target_page, function(data){		 
					if(from=='menu')
					{
						if(data)
						{
							data=data.split("$$$$");
							var html_data=data[1].split("####");	
							var cart_count=html_data[1];	
							$("#cartmenu").html(html_data[0]);	
							$("#cart-selection").html(cart_count);
						}
						
						/* if(data[4] < 80)
						{
							$("#confirmDiv").confirmModal({	
								heading: 'Confirm',
								body: 'Merci de compl&eacute;ter votre profil',
								callback: function () {
									window.location="/contrib/modify-profile/";
								}	
							});	
						}
						else */
						if(data[0]!='exceed')
						{
							if(action=='add')
								$("#tender_select_"+articleID).html(selected);
							else if(action=='remove')
								$("#tender_select_"+articleID).html(select);
							if(cart_count>0)
							$('#gotoSelection').modal('toggle');	
						}
						else if(data[0]=='exceed')
						{
							
							if(data[2]=='senior')
							{

								if (typeof articleID != "undefined" && articleID!='')
								{
									$("#confirmContainer").remove();
									bootbox.alert('You have already applied for '+data[3]+' article(s) : send your articles and wait for our team to validate them before applying for the next ones');
								}
							}
							else
							{
								if (typeof articleID != "undefined" && articleID!='')
								{
									$("#confirmContainer").remove();
									bootbox.alert('You have already applied for '+data[3]+' article(s) : wait for our team to validate them before applying for the next ones');
								}
							}
						}
						

							
						
					}
					else if(from=='main')
					{
						
						if(!data)
							window.location="/contrib/aosearch";
						else
						{
							sleep(1000);
							$("#cartmenu").html(data);
							 if(action=='remove')
							$("#cart_item_"+articleID).remove();
							
						}    
					}

				});       
			}
		}, "json");	
	}
	else
	{
		bootbox.alert("You must download and read the client's brief before adding this article to your selection (top right hand in green).");
	}	
			
}
/**Cart Modifier for Devis function**/
function fnCartDevisModifiers(action,articleID,from,response)
{
     if (typeof from == "undefined" || from=='') {
            from = "menu";
    
        }   
	if(typeof(response)==='undefined'){response = '';}		
	
    var target_page;
	
	var poll_cookie='poll_brief_'+articleID;
	var poll_cookie_value=cookieManager.readCookie(poll_cookie);	
	
	if(from=='menu')
		target_page = "/cart?from=menu&cart_action="+action+"&article_id="+articleID;
	else if(from=='main')
		target_page = "/cart?from=main&cart_action="+action+"&article_id="+articleID;
		
	var select='<button class="btn btn-large btn-primary" onClick="fnCartDevisModifiers(\'p_add\',\''+articleID+'\',\''+from+'\');">' +
        'Add to my selection</button>';
	
    var selected='<button class="btn btn-large btn-danger" onClick="fnCartDevisModifiers(\'p_remove\',\''+articleID+'\',\''+from+'\');">' +
        'Post added</button>';
	
	if(action=='p_remove' && from=='main')
		$("#cart_load_"+articleID).html('<center>'+bigLoader+'</center>');	
    
	
	if((poll_cookie_value==1 && action=='p_add') || (action=='p_remove'))
	{
		$.post(target_page, function(data){		 
			if(from=='menu')
			{
				if(data)
				{
					
					data=data.split("$$$$");				
					var html_data=data[1].split("####");	
					var cart_count=html_data[1];	
					$("#cartmenu").html(html_data[0]);	
					$("#cart-selection").html(cart_count);
					$(".cart-selection").html(cart_count);
				}			
				if(data[4] < 70)
				{
					$("#confirmDiv").confirmModal({	
						heading: 'Confirm',
						body: 'Merci de compl&eacute;ter votre profil',
						callback: function () {
							window.location="/contrib/modify-profile/";
						}	
					});	
				}
				else if(data[0]!='exceed')
				{
					if(action=='p_add')
						$("#tender_select_"+articleID).html(selected);
					else if(action=='p_remove')
						$("#tender_select_"+articleID).html(select);
					if(cart_count>0)
					{						
						
						if(response=='yes')
						{
							$("#poll_questions_div").hide();
							$("#poll_questions_div_ok").show();
						}
						else
						{
							$('#gotoSelection').modal('toggle');
							$(".cart-selection").html(cart_count);
						}	
						
					}	
				}
				
			}
			else if(from=='main')
			{
				
				if(!data)
					window.location="/contrib/aosearch";
				else
				{
					sleep(1000);
					$("#cartmenu").html(data);
					 if(action=='p_remove')
					$("#cart_item_"+articleID).remove();
					
				}    
			}

		});
	}
	else
	{
		bootbox.alert("You must download and read the client's brief before adding this article to your selection (top right hand in green).");
	}	
        
			
}

/**Cart Modifier for Devis function**/
function fnCartCorrectionModifiers(action,articleID,from,deliveryID)
{
     if (typeof from == "undefined" || from=='') {
            from = "menu";
    
        }   
    var target_page;
	
	//var correctioin_cookie='correction_brief_'+articleID;
	var correctioin_cookie='correction_brief_'+deliveryID;
	var correctioin_cookie_value=cookieManager.readCookie(correctioin_cookie);
	
	if(from=='menu')
		target_page = "/cart?from=menu&cart_action="+action+"&article_id="+articleID;
	else if(from=='main')
		target_page = "/cart?from=main&cart_action="+action+"&article_id="+articleID;
		
	var select='<button class="btn btn-large btn-primary" onClick="fnCartCorrectionModifiers(\'c_add\',\''+articleID+'\',\''+from+'\',\''+deliveryID+'\');">' +
        'Add to my selection</button>';
	
    var selected='<button class="btn btn-large btn-danger" onClick="fnCartCorrectionModifiers(\'c_remove\',\''+articleID+'\',\''+from+'\',\''+deliveryID+'\');">' +
        'Post added</button>';
	
	if(action=='c_remove' && from=='main')
		$("#cart_load_"+articleID).html('<center>'+bigLoader+'</center>');
		
    
	//alert(target_page);
	if((correctioin_cookie_value==1 && action=='c_add') || (action=='c_remove'))
	{
		$.post(target_page, function(data){		 
			if(from=='menu')
			{
				if(data)
				{
					data=data.split("$$$$");
					var html_data=data[1].split("####");	
					var cart_count=html_data[1];	
					$("#cartmenu").html(html_data[0]);	
					$("#cart-selection").html(cart_count);
				}
				
				if(data[4] < 70)
				{
					$("#confirmDiv").confirmModal({	
						heading: 'Confirm',
						body: 'Merci de compl&eacute;ter votre profil',
						callback: function () {
							window.location="/contrib/modify-profile/";
						}	
					});	
				}
				else if(data[0]!='exceed')
				{
					if(action=='c_add')
						$("#tender_select_"+articleID).html(selected);
					else if(action=='c_remove')
						$("#tender_select_"+articleID).html(select);
					if(cart_count>0)
					$('#gotoSelection').modal('toggle');	
				}
				else if(data[0]=='exceed')
				{
					
					if(data[2]=='senior')
					{

						if (typeof articleID != "undefined" && articleID!='')
						{
							$("#confirmContainer").remove();
							bootbox.alert('You have already applied for '+data[3]+' articles : send your article(s) and wait for our team to validate them before applying for the next ones');
						}
					}
					else
					{
						if (typeof articleID != "undefined" && articleID!='')
						{
							$("#confirmContainer").remove();
							bootbox.alert('You have already applied for '+data[3]+' article(s) : wait for our team to validate them before applying for the next ones');
						}
					}
				}			
				
			}
			else if(from=='main')
			{
				
				if(!data)
					window.location="/contrib/aosearch";
				else
				{
					sleep(1000);
					$("#cartmenu").html(data);
					 if(action=='c_remove')
					$("#cart_item_"+articleID).remove();
					
				}    
			}

		});
	}
	else
	{
		bootbox.alert("You must download and read the client's brief before adding this article to your selection (top right hand in green).");
	}	
        
			
}
			
var cookieManager = (function () {
    return {
        createCookie: function (name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            }
            document.cookie = name + "=" + value + expires + "; path=/";
        },
        writeSessionCookie: function (cookieName, cookieValue) {
            document.cookie = cookieName + "=" + cookieValue + "; path=/";
        },
        readCookie: function (name) {
            var nameEq = name + "=";
            var ca = document.cookie.split(';');			
            var i;

            for (i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') { c = c.substring(1, c.length); }				
                if (c.indexOf(nameEq) === 0) { return c.substring(nameEq.length, c.length); }
            }

            return null;
        },
        deleteCookie: function (name) {
            this.createCookie(name, null, -1);
        }
    };
}());
			
//timer in QuizPopup
function starQuizTimer(time,text)
{		
	$("[id^="+time+"_]").each(function(i) {
			var quiz=$(this).attr('id').split("_");
			var ts=quiz[2];
			
			$("#"+time+"_"+quiz[1]+"_"+quiz[2]).countdown({
				timestamp	: ts,
				callback	: function(days, hours, minutes, seconds){

					var message = "";

					if(days>0)
					{
						//message += days + "j" +" ";
						hours=hours+(days*24);
					}	
						
					if(hours>0)	
						message += hours + " : ";
					if(minutes > 0)
						message += minutes + " : ";
					else	
						message += "00" + " : ";
					if(seconds < 10 )
						message += "0"+seconds ;
					else if(seconds >= 10 )
						message += seconds ;
					
					$("#"+text+"_"+quiz[1]+"_"+quiz[2]).html(message);
					if(days==0 && hours==0 && minutes==0 && seconds==0)
					{						
						$.post("/quiz/cancle-quiz?reason=time_out",$("#quiz").serialize(),function(message){										
							$("#playQuizz-ajax").removeData('modal');		
							$('#playQuizz-ajax .modal-body').html(message);
							$("#playQuizz-ajax").modal();
							$(".modal-backdrop:gt(0)").remove();
							$( "#recruitment-challenge1" ).submit();							
							
						});						
					}	
					
				}
				
			});
			
		});
}

//function to subscribe alert of AO
function subscribeAOAlert(deliveryId,subscribe,mission_type)
{
	if (typeof deliveryId != "undefined" && deliveryId!='') 
	{
		var alert_subscribe_page="/contrib/ao-alert-subscribe?deliveryId="+deliveryId+"&subscribe="+subscribe+"&mission_type="+mission_type;
		
		var not_subscribe_btn='<a class="btn btn-small btn-primary" onclick="subscribeAOAlert(\''+deliveryId+'\',\'no\',\''+mission_type+'\');"><i class="icon-remove icon-white"></i> Alert set</a>';
	
		var subscribe_btn='<button class="btn" onclick="subscribeAOAlert(\''+deliveryId+'\',\'yes\',\''+mission_type+'\');"><i class="icon-bell"></i> Receive an alert</button>';
		
		$.post(alert_subscribe_page, function(data){
			
			var correction='';
			
			if(mission_type=='article-correction')			
				correction='correction_';
			
			
			
			//alert(alert_subscribe_page+'--'+data);
			if(subscribe=='yes')
			{
				$("#alert_"+correction+deliveryId).html(not_subscribe_btn);				
				$(".alert_"+correction+deliveryId).html(not_subscribe_btn);
			}	
			else if(subscribe=='no')
			{
				$("#alert_"+correction+deliveryId).html(subscribe_btn);	
				$(".alert_"+correction+deliveryId).html(subscribe_btn);				
			}	
				
		});
	
	}
}


			