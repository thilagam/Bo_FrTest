<?php /* Smarty version 2.6.19, created on 2015-06-30 09:30:41
         compiled from Contrib/popup_devis-q.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'Contrib/popup_devis-q.phtml', 109, false),)), $this); ?>
<link rel="stylesheet" type="text/css" href="/FO/script/contrib/validation-engine/validationEngine.jquery.css"/>
<script language="JavaScript" type="text/javascript" src="/FO/script/contrib/validation-engine/jquery.validationEngine-fr.js"></script>
<script language="JavaScript" type="text/javascript" src="/FO/script/contrib/validation-engine/jquery.validationEngine.js"></script>
<script language="JavaScript" type="text/javascript" src="/FO/script/common/bootstrap-datepicker.js"></script>
<?php echo '
<script type="text/javascript">
$(document).ready(function(){ 	
	
	$("#poll_validate").click(function(e){	
		//e.preventDefault();	
		
		var poll_id=$("#poll_id").val();
		var poll_cookie=\'poll_brief_\'+poll_id;
		//alert(poll_cookie);
		var poll_cookie_value=cookieManager.readCookie(poll_cookie);		
		if(poll_cookie_value==1)
		{
			$("#poll_questions").validationEngine({
				ajaxFormValidation: true,
				ajaxFormValidationMethod: \'post\',
				ajaxFormValidationURL: \'/contrib/save-poll-response\',
				onAjaxFormComplete: ajaxValidationCallback
			
			});			
		}
		else
		{
			bootbox.alert("You must download and read the client\'s brief before adding this article to your selection (top right hand in green).");			
			return false;
		}
	});
    
});
// Called once the server replies to the ajax form validation request
function ajaxValidationCallback(status, form, json, options){
		//alert(json);
	if (status === true) {
		var poll_id=$("#poll_id").val();
		//alert(poll_id);
		fnCartDevisModifiers(\'p_add\',poll_id,\'\',\'yes\');	
	}
}
</script>
'; ?>

<br />
<?php if ($this->_tpl_vars['poll_questions'] | @ count > 0): ?>
<div class="mod" id="poll_questions_div">
	<h3 class="offset1" id="question">Questionnaire/ pre-selection quote</h3>
	<p class="offset1 lead"></p>
	<form class="offset1 span9" id="poll_questions" method="POST">				
		<fieldset>
		<?php $_from = $this->_tpl_vars['poll_questions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['poll_question'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['poll_question']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['question']):
        $this->_foreach['poll_question']['iteration']++;
?>
			<?php $this->assign('question_index', ($this->_foreach['poll_question']['iteration']-1)+1); ?>			
			<?php $this->assign('poll_id', $this->_tpl_vars['question']['pollid']); ?>
			<?php if ($this->_tpl_vars['question']['type'] == 'price'): ?>
				<label><strong><?php echo $this->_tpl_vars['question']['title']; ?>
</strong></label>
				<input type="text" class="span2 validate[required,custom[price] <?php if ($this->_tpl_vars['question']['maximum']): ?>,max[<?php echo $this->_tpl_vars['question']['maximum']; ?>
]<?php endif; ?>" name="price_user_<?php echo $this->_tpl_vars['question']['id']; ?>
" id="price_user" value="<?php echo $this->_tpl_vars['question']['answer']; ?>
"><span class="text-middle"> <?php echo $this->_tpl_vars['question']['currency']; ?>
(s) </span>

			<?php elseif ($this->_tpl_vars['question']['type'] == 'bulk_price'): ?>
				<label><strong><?php echo $this->_tpl_vars['question']['title']; ?>
</strong></label>
				<input type="text" class="span2 validate[required,custom[bulk_price]<?php if ($this->_tpl_vars['question']['maximum']): ?>,max[<?php echo $this->_tpl_vars['question']['maximum']; ?>
]<?php endif; ?>,priceRange[#price_user]]" name="bulk_price_user_<?php echo $this->_tpl_vars['question']['id']; ?>
" id="bulk_price_user" value="<?php echo $this->_tpl_vars['question']['answer']; ?>
"> <span class="text-middle"> <?php echo $this->_tpl_vars['question']['currency']; ?>
(s) </span>
			<?php elseif ($this->_tpl_vars['question']['type'] == 'timing'): ?>
				<label><strong><?php echo $this->_tpl_vars['question']['title']; ?>
</strong></label>
				<input type="text" class="span1 validate[required,custom[time]]" name="question_<?php echo $this->_tpl_vars['question']['id']; ?>
" id="question_<?php echo $this->_tpl_vars['question']['id']; ?>
" value="<?php echo $this->_tpl_vars['question']['answer']; ?>
"> 
				<span class="text-middle">
				<?php if ($this->_tpl_vars['question']['option'] == 'min'): ?>  Minute(s)
				<?php elseif ($this->_tpl_vars['question']['option'] == 'hour'): ?>  Heure(s)
				<?php elseif ($this->_tpl_vars['question']['option'] == 'day'): ?>  Jour(s)
				<?php endif; ?>
				</span>	
				<input type="hidden" name="timing_<?php echo $this->_tpl_vars['question']['id']; ?>
" id="timing_<?php echo $this->_tpl_vars['question']['id']; ?>
" value="<?php echo $this->_tpl_vars['question']['option']; ?>
">				
			<?php elseif ($this->_tpl_vars['question']['type'] == 'radio'): ?>
				<label><strong><?php echo $this->_tpl_vars['question']['title']; ?>
</strong></label>
				<?php $_from = $this->_tpl_vars['question']['option']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['radioGroup'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['radioGroup']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['radio'] => $this->_tpl_vars['option']):
        $this->_foreach['radioGroup']['iteration']++;
?>
					<?php $this->assign('radio_index', ($this->_foreach['radioGroup']['iteration']-1)+1); ?>
					<label class="radio">
				<input type="radio" class="validate[required]" name="question_<?php echo $this->_tpl_vars['question']['id']; ?>
" id="radio_<?php echo $this->_tpl_vars['radio_index']; ?>
" value="<?php echo $this->_tpl_vars['radio']; ?>
" <?php if ($this->_tpl_vars['question']['answer'] == $this->_tpl_vars['radio']): ?> checked<?php endif; ?>>
						<?php echo $this->_tpl_vars['option']; ?>

					</label>
				<?php endforeach; endif; unset($_from); ?>			
			<?php elseif ($this->_tpl_vars['question']['type'] == 'checkbox'): ?>
				<label><strong><?php echo $this->_tpl_vars['question']['title']; ?>
</strong></label>
				<?php $_from = $this->_tpl_vars['question']['option']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['checkGroup'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['checkGroup']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['checkbox'] => $this->_tpl_vars['option']):
        $this->_foreach['checkGroup']['iteration']++;
?>
				<?php $this->assign('check_index', ($this->_foreach['checkGroup']['iteration']-1)+1); ?>				
					<label class="checkbox"><input type="checkbox" class="validate[minCheckbox[1]]" id="check_<?php echo $this->_tpl_vars['check_index']; ?>
" name="question_<?php echo $this->_tpl_vars['question']['id']; ?>
[]" value="<?php echo $this->_tpl_vars['checkbox']; ?>
" <?php if (in_array ( $this->_tpl_vars['checkbox'] , $this->_tpl_vars['question']['answer'] )): ?>checked<?php endif; ?>><?php echo $this->_tpl_vars['option']; ?>
</label>					
				<?php endforeach; endif; unset($_from); ?>	
			<?php elseif ($this->_tpl_vars['question']['type'] == 'range_price'): ?>
				<label><strong><?php echo $this->_tpl_vars['question']['title']; ?>
</strong></label>
				 <div class="input-prepend input-append">
					<span class="add-on"><?php echo $this->_tpl_vars['question']['minimum']; ?>
 
					<?php if ($this->_tpl_vars['question']['currency'] == 'euro'): ?>
						&euro;
					<?php else: ?>
						&pound;
					<?php endif; ?>
					</span>
					<input class="span2 validate[required,custom[number],max[<?php echo $this->_tpl_vars['question']['maximum']; ?>
],min[<?php echo $this->_tpl_vars['question']['minimum']; ?>
]] " id="question_<?php echo $this->_tpl_vars['question']['id']; ?>
" name="question_<?php echo $this->_tpl_vars['question']['id']; ?>
" type="text" value="<?php echo $this->_tpl_vars['question']['answer']; ?>
">
					<span class="add-on"><?php echo $this->_tpl_vars['question']['maximum']; ?>
 
					<?php if ($this->_tpl_vars['question']['currency'] == 'euro'): ?>
						&euro;
					<?php else: ?>
						&pound;
					<?php endif; ?>
					</span>
				</div>
			<?php elseif ($this->_tpl_vars['question']['type'] == 'calendar'): ?>	
				<label><strong><?php echo $this->_tpl_vars['question']['title']; ?>
</strong></label>
				<?php if ($this->_tpl_vars['question']['answer']): ?>
					<div class="input-append date" id="date_limit_<?php echo $this->_tpl_vars['question']['id']; ?>
" data-date="<?php echo ((is_array($_tmp=$this->_tpl_vars['question']['answer'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%d/%m/%Y') : smarty_modifier_date_format($_tmp, '%d/%m/%Y')); ?>
" data-date-format="dd/mm/yyyy" date-startdate="<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, '%d/%m/%Y') : smarty_modifier_date_format($_tmp, '%d/%m/%Y')); ?>
">
						<input class="validate[required]" type="text" id="idate_limit_<?php echo $this->_tpl_vars['question']['id']; ?>
" name="date_<?php echo $this->_tpl_vars['question']['id']; ?>
" readonly value="<?php echo ((is_array($_tmp=$this->_tpl_vars['question']['answer'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%d/%m/%Y') : smarty_modifier_date_format($_tmp, '%d/%m/%Y')); ?>
">
						<span class="add-on"><i class="icon-calendar"></i></span>
					</div>
				<?php else: ?>
					<div class="input-append date" id="date_limit_<?php echo $this->_tpl_vars['question']['id']; ?>
" data-date="<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, '%d/%m/%Y') : smarty_modifier_date_format($_tmp, '%d/%m/%Y')); ?>
" data-date-format="dd/mm/yyyy" date-startdate="<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, '%d/%m/%Y') : smarty_modifier_date_format($_tmp, '%d/%m/%Y')); ?>
">
						<input class="validate[required]" type="text" id="idate_limit_<?php echo $this->_tpl_vars['question']['id']; ?>
" name="date_<?php echo $this->_tpl_vars['question']['id']; ?>
" readonly>
						<span class="add-on"><i class="icon-calendar"></i></span>
					</div>			
				<?php endif; ?>	
			<?php endif; ?>				
		<?php endforeach; endif; unset($_from); ?>	
		
   
		<input type="hidden" id="poll_id" name="poll_id" value="<?php echo $this->_tpl_vars['poll_id']; ?>
">		
			<hr>

			<button id="poll_validate" type="submit" class="btn btn-primary btn-large">Confirm</button>
		</fieldset>
	</form>
</div>
<div class="mod" id="poll_questions_div_ok" style="display:none">
	<div id="free_form">
		<p class="checked">End of questionnaire</p>
		<h3 class="pull-center"><span>Thank you. Your answers have been registered</span></h3>
		<br /><br />
		<p class="pull-center">
			<button aria-hidden="true" data-dismiss="modal" class="btn">Go back to offers</button>
			<a class="btn btn-primary" href="/cart/cart-selection"><i class="icon-list-alt icon-white"></i> See my selection <span class="badge badge-warning" id="cart-selection">0</span></a>
		</p>
	</div>
</div>
<?php endif; ?>

<?php echo '
<script type="text/javascript">
		$("[id^=date_limit_]").each(function(i) {
		var myDate=new Date();		
		myDate.setDate(myDate.getDate()-1);		
			$(this).datepicker({
				format: \'dd/mm/yyyy\',
				startDate: myDate
			});		
		});
</script>
'; ?>