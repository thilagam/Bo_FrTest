<?php /* Smarty version 2.6.19, created on 2015-09-03 16:21:45
         compiled from Contrib/popup_recruitment_quiz.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'Contrib/popup_recruitment_quiz.phtml', 136, false),)), $this); ?>
<?php echo '
<script type="text/javascript">
var cur_date=0,js_date=0,diff_date=0;
cur_date='; ?>
<?php echo time(); ?>
<?php echo ';
js_date=(new Date().getTime())/ 1000;
diff_date=Math.floor(js_date-cur_date);
$("#menu_ongoing").addClass("active");
starQuizTimer(\'question\',\'question\');

bootstrap_alert = function() {}
bootstrap_alert.error = function(div,message) {
	$(\'#\'+div).html(\'<div class="alert  alert-error"><button data-dismiss="alert" class="close" type="button">&times;</button><span><ul>\'+message+\'</ul></span></div>\')
}

//save each option selected and load next question if available
$("#loadQuestion").click(function(e) {
	e.preventDefault();
	
	if($("input:radio[name=\'qoptions\']").is(":checked")) {	
	
		$.get("/quiz/add-option",$("#quiz").serialize(),function(data){            
			var href = $("#loadQuestion").attr(\'href\');						
			$("#playQuizz-ajax").removeData(\'modal\');		
			$(\'#playQuizz-ajax .modal-body\').load(href);
			$("#playQuizz-ajax").modal();
			$(".modal-backdrop:gt(0)").remove();
        });			
	}
	else
	{	
		var msg=\'Merci de cocher une r&eacute;ponse\';
		bootstrap_alert.error(\'alert_quiz_placeholder\',msg);	
	}	
});

//save final question option and submit the results
$("#validateQuiz").click(function(e) {
	e.preventDefault();
	var insert=false;
	
	if($("input:radio[name=\'qoptions\']").is(":checked")) {	
	
			insert=true;		
	}
	else
	{	
		var msg=\'Merci de cocher une r&eacute;ponse\';
		bootstrap_alert.error(\'alert_quiz_placeholder\',msg);	
	}
	
	if(insert)
	{
		$.get("/quiz/add-option",$("#quiz").serialize(),function(data){ 
			$.post("/quiz/save-quiz",$("#quiz").serialize(),function(message){							
					$("#playQuizz-ajax").removeData(\'modal\');		
					//$(\'#playQuizz-ajax .modal-body\').html(message);
					//$("#playQuizz-ajax").modal();
					$(".modal-backdrop:gt(0)").remove();
					$( "#recruitment-challenge1" ).submit();
				});	
		});		
	}		
});

//Cancle Quiz default insert fail
$("#cancleQuiz,#exitQuiz").click(function(e) {
	e.preventDefault();		
	 var message="By clicking on cancel, we will consider that you failed the quiz";
	 var heading=\'Confirm\';
	if ($("#quiz").length) { 
		bootbox.confirm(message, function(result) {
			if(result)
			{
				$.post("/quiz/cancle-quiz",$("#quiz").serialize(),function(message){										
					$("#playQuizz-ajax").removeData(\'modal\');					
					$(".modal-backdrop:gt(0)").remove();
					$("#recruitment-challenge1" ).submit();					
				});
			}
		});
	}
	else
	{	
		window.location.reload();	
	}	
	

});
$(document).keyup(function(e) {
	
	if (e.keyCode == 27 && $("#quiz").length){
		
		$(\'#playQuizz-ajax\').on(\'hidden\', function () {
			if($("#quiz").serialize())
			{
				$.post("/quiz/cancle-quiz",$("#quiz").serialize(),function(message){										
					$("#playQuizz-ajax").removeData(\'modal\');		
					//$(\'#playQuizz-ajax .modal-body\').html(message);
					//$("#playQuizz-ajax").modal();
					$(".modal-backdrop:gt(0)").remove();
					$("#recruitment-challenge1" ).submit();						
				});	
			}
			else
				window.location.reload();
		});
	}
});




</script>
'; ?>

<?php if ($this->_tpl_vars['current_question'] | @ count > 0): ?>
	<?php $_from = $this->_tpl_vars['current_question']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['quiz'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['quiz']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['question']):
        $this->_foreach['quiz']['iteration']++;
?>
	<section id="quizz">
	<div class="pull-center">
		<div class="downtime"><span class="licon licon-timing"></span>
			<span id="question_<?php echo $this->_tpl_vars['question']['id']; ?>
_<?php echo $this->_tpl_vars['question']['timestamp']; ?>
"><?php echo $this->_tpl_vars['question']['quiz_duration']; ?>
</span>
		</div>
		<!-- Add class "less1" when countdown < 50s -->
		<!-- div class="downtime less1"><span class="licon licon-timing"></span> 00:59</div-->
	</div>
	<div class="row-fluid">
		<div class="well well-large span10 offset1">
			<h1><?php echo $this->_tpl_vars['question']['question']; ?>
 <div class="pull-right number"><?php echo $this->_tpl_vars['current']; ?>
<span class="on">/<?php echo $this->_tpl_vars['totalQuestions']; ?>
</span></div></h1>
			<hr>
			<form method="" action="" id="quiz">
				
				<?php $_from = $this->_tpl_vars['question']['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['option'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['option']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['options']):
        $this->_foreach['option']['iteration']++;
?>
				<?php $this->assign('qindex', ($this->_foreach['option']['iteration']-1)+1); ?>
					<label class="radio">
						<input type="radio" name="qoptions"  value="<?php echo $this->_tpl_vars['options']['id']; ?>
">
						<!--<input type="radio" name="qoptions"  value="<?php echo $this->_tpl_vars['qindex']; ?>
">-->
						<?php echo ((is_array($_tmp=$this->_tpl_vars['options']['text'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

					</label>
				<?php endforeach; endif; unset($_from); ?>			
				<div id = "alert_quiz_placeholder"></div>
				<div class="pull-right">
					<a class="btn" id="cancleQuiz">Cancel</a>
					<?php if ($this->_tpl_vars['next_id']): ?>
						<a class="btn btn-primary" id="loadQuestion" href="/quiz/participate-recruitment-quiz?recruitment_id=<?php echo $this->_tpl_vars['question']['recruitment_id']; ?>
&quiz_identifier=<?php echo $this->_tpl_vars['question']['quizz_id']; ?>
&question_id=<?php echo $this->_tpl_vars['next_id']; ?>
">Validate my answer</a>
					<?php else: ?>
						<a class="btn btn-primary" id="validateQuiz">Validate my answer <i class="icon-white icon-chevron-right"></i></a>
					<?php endif; ?>	
				</div>
				<input type="hidden" name="quiz" value="<?php echo $this->_tpl_vars['question']['quizz_id']; ?>
">
				<input type="hidden" name="question" value="<?php echo $this->_tpl_vars['question']['id']; ?>
">
				<input type="hidden" name="recruitment" value="yes">
				<input type="hidden" name="article_id" value="<?php echo $this->_tpl_vars['question']['recruitment_id']; ?>
">
				
			</form>
		</div>
	</div>
</section>
<?php endforeach; endif; unset($_from); ?>
<div id = "confirmCancle"></div>
<a href="#brand" class="pull-right btn btn-small disabled anchor-top scroll"><i class="icon-arrow-up"></i></a>
<?php endif; ?>
<?php echo '
<script>

$(".scroll").click(function(event){		
		event.preventDefault();
		$(\'html,body\').animate({scrollTop:$(this.hash).offset().top}, 500);
	});

// tooltip activation
    $("[rel=tooltip]").tooltip();
	 $("[rel=popover]").popover();
	
</script>
'; ?>