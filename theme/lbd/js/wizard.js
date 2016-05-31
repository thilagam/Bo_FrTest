searchVisible = 0;
transparent = true;

$(document).ready(function(){
	

	//$.validator.setDefaults({ ignore: ".hidden" });
			$validator= $("#create_quote_mission").validate({
				
				submitHandler : function(form) {
		        form.submit();
				},
				rules: {
					language:  { required :function(element) {
									return true;
										}    },
					tech_title:  { required:function(element) {
										return $('input:radio[name=product]:checked').val()=='tech';
										}   },
					languagedest:  { required:function(element) {
										return $('input:radio[name=product]:checked').val()=='traduction';
											
										} 
									},
					producttype:  { required: true },	
					producttypeother:  { required:function(element) {
										return $('#producttype').val()=='autre';
									} },			
		          	nb_words: 
		          	{
							required : true,
							number: true,
							min: $('#nb_words').attr('min'),
							max: $('#nb_words').attr('max')
					},
					mission_length:{
							required : true,
							number: true
						},
					tech_mission_length:{
							required : true,
							number: true
						},
					mission_cost:{
							required : true,
							number: true
						},
					volume:{
							required : true,
							number: true
						},
					volume_no:{
							required : true,
							number: true
						},
					volume_yes:{
							required : true,
							number: true
						},
					tempo_length:{
							required : true,
							number: true
						},
					volume_max:{
							required : true,
							number: true
						},
					prodmissionslist:{
							required:function(element) {
										return $('#prod_mission_selected').val()=='Yes';
									} 
							},
					strategy_mission_cost:{
							required:function(element) {
										return $('#product:checked').val()=='content_strategy';
									} ,
							number: function(element) {
										return $('#product:checked').val()=='content_strategy';
									}
							}
					
							
				},
				messages: {
					language: { required: "Please select the language" },
					tech_title: { required: "Please select the title" },
					languagedest: { required: "Please select the language2" },
					producttype: { required : "Select Produit" },
					nb_words: {
						required: "Please enter the words"
						},
					tech_mission_length:{
						required:  "Please enter the duration"},
					mission_length: {
						required:  "Please enter the duration"},
					mission_cost: {
						required:  "Please enter the Cost"},
					volume: {
						required:  "Please enter the volume"
						},
					tempo_length:{
						required:  "Please enter the tempo length"
						},
					volume_max:{
						required: "Please enter the Volume max"
						},
					prodmissionslist:{
						required: "Please select a prod mission"
						},
					strategy_mission_cost:{
						required: "Enter the mission cost"
					},
					producttypeother:{
						required: "Please enter the text"
					}
				},		
				  debug: true,
				  errorClass:'has-error error',
				  validClass:'success',
				  errorElement:'span',
				  highlight: function (element, errorClass, validClass) { 
					$(element).parents("div.form-group").addClass(errorClass).removeClass(validClass); 

				  }, 
				  unhighlight: function (element, errorClass, validClass) { 
						  $(element).parents(".error").removeClass(errorClass).addClass(validClass); 
				  }
				 
				  
				  //validate choosen select (select with search)
		           //ignore: ":hidden:not(select)" 
		           //ignore: ':hidden:not(.chzn-done)'
		           //  ignore: '*:not([name])'		
			     
			});
				
	
    /*  Activate the tooltips      */
    $('[rel="tooltip"]').tooltip();
      
    $('.wizard-card').bootstrapWizard({
        'tabClass': 'nav nav-pills',
        'nextSelector': '.btn-next',
        'previousSelector': '.btn-previous',
         
         onInit : function(tab, navigation, index){
            
           //check number of tabs and fill the entire row
           var $total = navigation.find('li').length;
           $width = 100/$total;
           
           $display_width = $(document).width();
           
           if($display_width < 600 && $total > 3){
               $width = 50;
           }
           
           navigation.find('li').css('width',$width + '%');
           
        },
        onNext: function(tab, navigation, index){
          
          if(!$("#create_quote_mission").valid())
          {

	          	var product_val=$("#product:checked").val();
	            var language_=$("#language").val();
	            var languagedest_=$("#languagedest").val();
	            var tech_title=$("#tech_title").val();
	            var producttype=$("#producttype").val();
	            var producttypeother=$("#producttypeother").val();
	            
	            if(product_val=='tech')
				{
					
					if(tech_title=="")
					{
					$('#tech_title').closest('.form-group').addClass('has-error error');
					$('#tech_title').closest('.form-group').append('<span id="tech_title-error" class="has-error error">Please select a title</span>');
					}
								
				}
				else if(product_val=='translation')
				{

					if(language_=="" && languagedest_!="")
					{
					$('#language').closest('.form-group').addClass('has-error error');
					$('.all_languages').append('<span id="language-error" class="has-error error">Please select a language</span>');
									
					}
					else if(languagedest_=='' && language_!="")
					{
					$('#languagedest').closest('.form-group').addClass('has-error error');
					$('.all_languages').append('<span id="languagedest-error" class="has-error error">Please select a language2</span>');
					}
					else if(languagedest_=='' && language_=="")
					{
					$('#language').closest('.form-group').addClass('has-error error');
					$('.all_languages').append('<span id="language-error" class="has-error error">Please select a language</span>');	
					$('.all_languages').append('<span id="languagedest-error" class="has-error error">Please select a language2</span>');
					}
					else if(languagedest_==language_)
					{
					$('#languagedest-error').remove();
					$('#languagedest').closest('.form-group').addClass('has-error error');
					$('.all_languages').append('<span id="languagedest-error" class="has-error error">Please select different language</span>');
					}
					
				}
				else if(product_val=='redaction')
				{
					if(language_=="")
					{
						
					$('#language').closest('.form-group').addClass('has-error error');
					$('#language').closest('.form-group').append('<span id="language-error" class="has-error error">Please select a language</span>');
					}
				}
				else if(producttype=="")
				{
					$('#producttype').closest('.form-group').addClass('has-error error');
					$('#producttype').closest('.form-group').append('<span id="producttype-error" class="has-error error">Please select a product</span>');
					return false;
				}
				else if(producttypeother=="")
				{
					$('#producttypeother').closest('.form-group').addClass('has-error error');
					$('#producttypeother').closest('.form-group').append('<span id="producttypeother-error" class="has-error error">Please enter the text</span>');
					return false;
				}
					$validator.focusInvalid();
					return false;
			}
			else 
			{
				$("#producttype").selectpicker();
				return true;
			}    
        },
        onTabClick : function(tab, navigation, index){
            // Disable the posibility to click on tabs
            return false;
        }, 
        onTabShow: function(tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index+1;
            
            var wizard = navigation.closest('.wizard-card');
            var product_val=$("#product:checked").val();
            var language_=$("#language").val();
            var languagedest_=$("#languagedest").val();
            var tech_title=$("#tech_title").val();
            var producttype=$("#producttype").val();
            var producttypeother=$("#producttypeother").val();
	            
            

			if(product_val=='tech' && $current==2)
			{
				if(language_=="")
				{
				$('#language').closest('.form-group').addClass('has-error error');
				$('#language').closest('.form-group').append('<span id="language-error" class="has-error error">Please select a language</span>');
				$('ul.nav-pills li a[href="#mission-step1"]').tab('show');
				$current=1;	
				}
				
			
			}
			else if(product_val=='content_strategy' && $current==2)
			{
				if(language_=="")
				{
				$('#language').closest('.form-group').addClass('has-error error');
				$('#language').closest('.form-group').append('<span id="language-error" class="has-error error">Please select a language</span>');
				$('ul.nav-pills li a[href="#mission-step1"]').tab('show');
				$current=1;	
				}
				else {
				$('ul.nav-pills li a[href="#mission-step4"]').tab('show');
				$current=$total;
				}
				
			
			}
			else if(product_val=='translation' && $current==2)
			{
				if(language_=="")
				{
					$('#language-error').remove();
				$('#language').closest('.form-group').addClass('has-error error');
				$('.all_languages').append('<span id="language-error" class="has-error error">Please select a language</span>');
				$('ul.nav-pills li a[href="#mission-step1"]').tab('show');

				$current=1;	
				}
				else if(languagedest_=='' )
				{
					$('#languagedest-error').remove();
				$('#languagedest').closest('.form-group').addClass('has-error error');
				$('.all_languages').append('<span id="languagedest-error" class="has-error error">Please select a language2</span>');
				$('ul.nav-pills li a[href="#mission-step1"]').tab('show');
				$current=1;
				}
				else if(languagedest_==language_ )
				{
					$('#languagedest-error').remove();
				$('#languagedest').closest('.form-group').addClass('has-error error');
				$('.all_languages').append('<span id="languagedest-error" class="has-error error">Please select a different language</span>');
				$('ul.nav-pills li a[href="#mission-step1"]').tab('show');
				$current=1;
				}
				
			}
			else if(product_val=='redaction' && $current==2)
			{
				if(language_=="")
				{
				$('#language').closest('.form-group').addClass('has-error error');
				$('#language').closest('.form-group').append('<span id="language-error" class="has-error error">Please select a language</span>');
				$('ul.nav-pills li a[href="#mission-step1"]').tab('show');
				$current=1;	
				}
			}
			else if(producttype=="" && $current==3 && product_val!='tech' && product_val!='content_strategy')
			{

			$('#producttype').closest('.form-group').addClass('has-error error');
			$('#producttype').closest('.form-group').append('<span id="producttype-error" class="has-error error">Please select a product</span>');
			$('ul.nav-pills li a[href="#mission-step2"]').tab('show');	
			}
			else if(producttypeother=="" && producttype=="autre" && $current==3 && product_val!='tech' && product_val!='content_strategy')
			{

			$('#producttypeother').closest('.form-group').addClass('has-error error');
			$('#producttypeother').closest('.form-group').append('<span id="producttypeother-error" class="has-error error">Please enter the text</span>');
			$('ul.nav-pills li a[href="#mission-step2"]').tab('show');	
			}
			else if(product_val=='tech' && $current==3)
			{
				var to_linked=$("#prod_mission_selected").val();
				var prodmissionval=$("#prodmissionslist").val();
				if(to_linked=='Yes' && prodmissionval=="")
				{
				$('#prodmissionslist').closest('.form-group').addClass('has-error error');
				$('#prodmissionslist').closest('.form-group').append('<span id="prodmissionslist-error" class="has-error error">Please select a prod mission</span>');
				$('ul.nav-pills li a[href="#mission-step2"]').tab('show');	
				}
				
			}
			else if(product_val=='tech' && $current==4 )
			{
								
				if(tech_title=='')
				{
				$('#tech_title').closest('.form-group').addClass('has-error error');
				$('#tech_title').closest('.form-group').append('<span id="tech_title-error" class="has-error error">Please select a title</span>');
				$('ul.nav-pills li a[href="#mission-step3"]').tab('show');	
				$current=3;
				}
				
			}
			
			if($current==1)
			{
			$(wizard).find('.btn-previous').hide();
			}
			else
			{
				$(wizard).find('.btn-previous').show();
			}
            // If it's the last tab then hide the last button and show the finish instead
            if($current >= $total) {
                $(wizard).find('.btn-next').hide();
                $(wizard).find('.btn-finish').show();
            } else {
            	$(wizard).find('.btn-next').show();
                $(wizard).find('.btn-finish').hide();

            }
        }
    });

      
    
    $('[data-toggle="wizard-radio"]').click(function(){
        wizard = $(this).closest('.wizard-card');
        wizard.find('[data-toggle="wizard-radio"]').removeClass('active');
        $(this).addClass('active');
        $(wizard).find('[type="radio"]').removeAttr('checked');
        $(this).find('[type="radio"]').attr('checked','true');
    });
    
    $('[data-toggle="wizard-checkbox"]').click(function(){
        if( $(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).find('[type="checkbox"]').removeAttr('checked');
        } else {
            $(this).addClass('active');
            $(this).find('[type="checkbox"]').attr('checked','true');
        }
    });
    
    $height = $(document).height();
    $('.set-full-height').css('height',$height);
    
    
});

function validateFirstStep(){
	
    /*  $('#create_quote_mission').validationEngine();*/
  	
	
	
	return true;
}

function validateSecondStep(){
  
    $("#create_quote_mission").validate({
		rules: {
			nb_words_:"required",
		},
		messages: {
			nb_words_: "Please enter the volume",
		}
	}); 
	
	if(!$("#create_quote_mission").valid()){
    	return false;
	}
	return true; 
    
}

function validateThirdStep(){
    //code here for third step
    
    
}

 //Function to show image before upload