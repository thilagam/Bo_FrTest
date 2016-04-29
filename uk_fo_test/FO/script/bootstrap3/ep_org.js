// JavaScript Document for ep

    wow = new WOW(
      {
        animateClass: 'animated',
        offset:       100
      }
    );
   // wow.init(); 

/** activate select picker **/
$('.selectpicker').selectpicker();	
	
/** activate icheck plugin **/
 $('input').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue',
    //increaseArea: '20%' // optional
  });

$('input#objectives_4').on('ifChecked', function(event){
	   $('textarea#objOtherText').addClass("in showcollapse");
});
$('input#objectives_4').on('ifUnchecked', function(event){
	   $('textarea#objOtherText').removeClass("in showcollapse");
});

$('input:checkbox.group1').on('ifChecked', function(event){
	addarticleblock(this.value);
});
$('input:checkbox.group1').on('ifUnchecked', function(event){
	removearticleblock(this.value);
});
		
$('input#remindcheck').on('ifChecked', function(event){
	   $('#ep_callbackrequest_time').addClass("in showcollapse");
});
$('input#remindcheck').on('ifUnchecked', function(event){
	   $('#ep_callbackrequest_time').removeClass("in showcollapse");
});

$('input#ep-fixquotetime').on('ifChecked', function(event){
	   $('#ep-fixquotetimeText').addClass("in showcollapse");
});
$('input#ep-fixquotetime').on('ifUnchecked', function(event){
	   $('#ep-fixquotetimeText').removeClass("in showcollapse");
});

$('input#ep-tuned-contrib').on('ifChecked', function(event){
	   $('#ep-contrib-table').addClass("in content-block");
});
$('input#ep-tuned-contrib').on('ifUnchecked', function(event){
	   $('#ep-contrib-table').removeClass("in content-block");
});

$('input#ep-tuned-price').on('ifChecked', function(event){
	   $('#ep-tuned-price-check').addClass("in showcollapse");
});
$('input#ep-tuned-price').on('ifUnchecked', function(event){
	   $('#ep-tuned-price-check').removeClass("in showcollapse");
});

$('input#quotetype-other').on('ifChecked', function(event){
	   $('#textforother-block').addClass("in showcollapse");
});
$('input#quotetype-other').on('ifUnchecked', function(event){
	   $('#textforother-block').removeClass("in showcollapse");
});

$('input#translation').on('ifChecked', function(event){
	$('form#quotes1form').get(0).setAttribute('action','/client/quotes-2'); 
});
$('input#translation').on('ifUnchecked', function(event){
	$('form#quotes1form').get(0).setAttribute('action','/client/quotes2liberte'); 
});
				
$('input#checkall').on('ifChecked', function(event){
	var $b = $('.overflow .icheckbox_square-blue');
	$b.addClass('checked');
	$('.overflow  input[type=checkbox]').attr('checked', true);	  
});
$('input#checkall').on('ifUnchecked', function(event){
	var $b = $('.overflow .icheckbox_square-blue');
	$b.removeClass('checked');
	$('.overflow  input[type=checkbox]').attr('checked', false);
});

$('input#optionsRadios2').on('ifChecked', function(event){
	   $("#option1fields").hide();
		$("#option2fields").show();
});
$('input#optionsRadios1').on('ifUnchecked', function(event){
	  	$("#option1fields").show();
		$("#option2fields").hide();
});
