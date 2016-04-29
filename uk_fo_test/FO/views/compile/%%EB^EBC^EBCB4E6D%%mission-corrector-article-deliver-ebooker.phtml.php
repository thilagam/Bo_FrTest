<?php /* Smarty version 2.6.19, created on 2015-12-23 08:50:45
         compiled from Contrib/mission-corrector-article-deliver-ebooker.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Contrib/mission-corrector-article-deliver-ebooker.phtml', 169, false),array('modifier', 'htmlentities', 'Contrib/mission-corrector-article-deliver-ebooker.phtml', 201, false),array('modifier', 'urldecode', 'Contrib/mission-corrector-article-deliver-ebooker.phtml', 209, false),array('modifier', 'utf8_decode', 'Contrib/mission-corrector-article-deliver-ebooker.phtml', 209, false),)), $this); ?>
<link rel="stylesheet" type="text/css" href="/FO/css/common/bootstrap-wysihtml5.css"></link>
<script src="/FO/script/common/wysihtml5-0.3.0.min.js"></script>
<script src="/FO/script/common/bootstrap-wysihtml5.js"></script> 
<script src="/FO/script/common/locales/bootstrap-wysihtml5.fr-FR.js"></script>
<!--  ratings -->
<script type="text/javascript" charset="utf-8" src="/FO/script/common/jquery.raty.min.js"></script>
<?php echo '
<script type="text/javascript">
	
	var tokensArray = '; ?>
<?php echo $this->_tpl_vars['js_tokens_array']; ?>
<?php echo ';
	var whiteListArray = '; ?>
<?php echo $this->_tpl_vars['js_wl_keywords']; ?>
<?php echo ';
	
	/*Ebookers stencils validations*/
	function auto_grow(element) {
		//alert(element.scrollHeight)
		if(element.scrollHeight > 85)
		{
			element.style.height = "100px";
			element.style.height = (element.scrollHeight)+"px";
		}	
	}	
	function wordCount(val)
	{	
		var wordCount=0;
		if(val==\'\')
		{
			return {
				charactersNoSpaces : 0,
				characters         : 0,
				words              : wordCount,
				lines              : 0
			}
		}	
		else
		{
			/*if(whiteListArray.length>0)
			{
				val=val.replace(/[\\.\\,\\!\\?]/g,\'\');
				//var stencil_text=encodeURI(val);
				var regexp = new RegExp(decodeURI(whiteListArray.join( \'|\' )), \'ig\');
				//alert(val+\'--\'+decodeURI(whiteListArray.join( \'|\' )));
				val = val.replace(regexp, \'\');
			}*/		
			val = val.replace(/\\\\s+/gi, \' \');	
			if(val==\'\' || val==\' \')
				wordCount=0;
			else
				wordCount=val.trim().replace(/\\\\s+/gi, \' \').split(\' \').length;
			
			return {
				charactersNoSpaces : val.replace(/\\\\s+/g, \'\').length,
				characters         : val.length,
				words              : wordCount,
				lines              : val.split(/\\\\r*\\n/).length
			}
		}	
	}
	
	function htmlEntities(str) {
		return String(str).replace(/&/g, \'&amp;\').replace(/</g, \'&lt;\').replace(/>/g, \'&gt;\').replace(/"/g, \'&quot;\');
	}
	
	$(function(){	
		/*toggle blocks*/
		$(".boxTitle a > i").click(function(){
			$(this).toggleClass(\'icon-chevron-down\');
			$(this).parents(".boxTitle").toggleClass(\'boxtitleToggled\');			
			$(this).parents(".boxTitle").next().toggle();
		});
		
		/*auto resize of textarea onload*/
		$("[id^=stencil_text_]" ).each(function(u) {
			auto_grow(this);
			if($(this).val())
			{
				var c = wordCount($(this).val());				
				$(this).next(\'.wordCount\').html(c.words +\' Words\');
			}
		});
		/*rating function*/
		$("[id^=starval]" ).each(function(u) {			
			var index=parseInt(u+1);
			$(this).raty({
				scoreName : \'entity_score\',
				number    : 5,
				path: \'/FO/images/\',			  
				target     : \'#precision-target\'+index,
				targetKeep : true,
				targetType : \'number\'
			});			
		});	
		$("[id^=starclose]" ).each(function(u) {
			var index=parseInt(u+1);
			$(this).raty({
				scoreName : \'entity_score\',
				number    : 5,
				path: \'/FO/images/\',
				//  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][0]; ?>
<?php echo ',
				target     : \'#precision-target-close\'+index,
				targetKeep : true,
				targetType : \'number\'
			});			
		});
	});	
</script>
<style type="text/css">
.sample-management-cont{
    background-color: #f8f8f8;
    border-radius: 4px 4px 0 0;
    box-shadow: 0 20px 8px -18px rgba(0, 0, 0, 0.3), 0 1px 0 #fff inset;    
}
.topset1{
	margin-top:20px
}
textarea {
  resize: vertical; 
}

</style>
'; ?>

<div class="container">
	<br>
<?php if ($this->_tpl_vars['missionDetails'] | @ count > 0): ?>
	<?php $_from = $this->_tpl_vars['missionDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['details'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['details']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['details']['iteration']++;
?>	
	<ul class="breadcrumb">
	<li><a href="/contrib/home">Home</a> <span class="divider">/</span></li>
	<li><a href="/contrib/ongoing">My contributions</a> <span class="divider">/</span></li>
	<li class="active"><?php echo $this->_tpl_vars['article']['title']; ?>
</li>
	</ul> 
	<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">&times;</button>
		<i class="icon-ok icon-white"></i> Congratulations <?php echo $this->_tpl_vars['client_email']; ?>
 ! You have been selected as a writer for this mission. Please respect deadlines. 
	</div>
	<!-- start, Summary -->
	<section id="summary">
		<div class="row-fluid">
			<div class="span7">
				<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
					<span class="label label-test-mission">Proofreading Test Mission</span>
				<?php else: ?>
					<span class="label label-correction">Proofreading Mission</span>
				<?php endif; ?>
				<h1><?php echo $this->_tpl_vars['article']['title']; ?>
</h1>
			</div>
			<div class="span2 stat">
				<p>Delivery deadline</p>
				<!-- add classname "less24" if time is < 24h  -->
				<!-- p class="num-large less24">Livr�e</p-->
				<?php if ($this->_tpl_vars['article']['status'] == 'bid' || $this->_tpl_vars['article']['status'] == 'disapproved'): ?>
					<p class="num-large less24">
						<span id="dtime_<?php echo $this->_tpl_vars['article']['article_id']; ?>
_<?php echo $this->_tpl_vars['article']['corrector_submit_expires']; ?>
">
							<span id="dtext_<?php echo $this->_tpl_vars['article']['article_id']; ?>
_<?php echo $this->_tpl_vars['article']['corrector_submit_expires']; ?>
"><?php echo $this->_tpl_vars['article']['corrector_submit_expires']; ?>
</span>
						</span>
					</p>
				<?php else: ?>
					<p class="num-large less24" style="font-size:18px"><?php echo $this->_tpl_vars['article']['status_trans']; ?>
</p>
				<?php endif; ?>	
			</div>
			<div class="span2 stat">
				<p>Rate</p>
				<p class="num-large"><?php echo $this->_tpl_vars['article']['price_corrector']; ?>
 
				<?php if ($this->_tpl_vars['article']['currency'] == 'euro'): ?>
						&euro;
					<?php else: ?>
						&pound;
					<?php endif; ?>
				</p>
			</div>
			<div class="span1 stat">
				<div class="icon-comment-large new"><a href="#comment" class="scroll" id="comment_count"><?php echo count($this->_tpl_vars['commentDetails']); ?>
</a></div>
				<!--  to active if new comment, add classname "new" -->
				<!--div class="icon-comment-large new"><a href="#comment">3</a></div-->
			</div>
		</div>
	</section>
	<!-- end, summary --> 
	<div class="row-fluid"> 
		<div class="span9">
			<?php if ($this->_tpl_vars['product_type'] == 'translation'): ?>
				<section id="ebStencilTranslation">
			<?php else: ?>	
				<section id="stencilWrapper">
			<?php endif; ?>
				<div class="mod">
					<div class="summary clearfix">
						<h4>PROJECT DETAILS
							<?php if ($this->_tpl_vars['article']['spec_exists'] == 'yes'): ?>
								<span class="pull-right"><a href="/contrib/download-file?type=correctionbrief&article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" class="btn btn-small btn-success"><i class="icon-white icon-circle-arrow-down"></i> T&eacute;l&eacute;charger le brief client</a></span>
							<?php endif; ?>
						</h4>
					</div>
				</div>
						<div class="row-fluid">  
					<h3>Please proofread the <?php echo $this->_tpl_vars['article']['files_pack']; ?>
 versions of sentences. They all contain some mandatory words. Do not delete them.</h3>
				</div>
				<?php if ($this->_tpl_vars['product_type'] == 'redaction'): ?>
					<div class="row-fluid">
						<div class="textSampleWrapper">
							<div class="span12">
								<h5>SAMPLE TEXT</h5>
								<p>
									<?php echo ((is_array($_tmp=$this->_tpl_vars['sample_text'])) ? $this->_run_mod_handler('htmlentities', true, $_tmp) : smarty_modifier_htmlentities($_tmp)); ?>

								</p>
							</div>	
							<div class="requiredWordsList">
								<dl>
									<dt>Mandatory Words:</dt>
									<?php if ($this->_tpl_vars['mandatory_tokens'] | @ count > 0): ?>
										<?php $_from = $this->_tpl_vars['mandatory_tokens']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['token'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['token']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['token']):
        $this->_foreach['token']['iteration']++;
?>
											<dd><span class="label"><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['token'])) ? $this->_run_mod_handler('urldecode', true, $_tmp) : urldecode($_tmp)))) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)))) ? $this->_run_mod_handler('htmlentities', true, $_tmp) : smarty_modifier_htmlentities($_tmp)); ?>
</span></dd>
										<?php endforeach; endif; unset($_from); ?>
									<?php endif; ?>								
								</dl>
								<dl>
									<dt>Optional Words:</dt>
									<?php if ($this->_tpl_vars['optional_tokens'] | @ count > 0): ?>
										<?php $_from = $this->_tpl_vars['optional_tokens']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['token'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['token']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['token']):
        $this->_foreach['token']['iteration']++;
?>
											<dd><span class="label"><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['token'])) ? $this->_run_mod_handler('urldecode', true, $_tmp) : urldecode($_tmp)))) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)))) ? $this->_run_mod_handler('htmlentities', true, $_tmp) : smarty_modifier_htmlentities($_tmp)); ?>
</span></dd>
										<?php endforeach; endif; unset($_from); ?>
									<?php endif; ?>
								</dl>
							</div>					
						</div>
					</div>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['article']['status'] == 'bid' || $this->_tpl_vars['article']['status'] == 'disapproved'): ?>
					<div class="row-fluid file-management-cont"> 						
						<?php if ($this->_tpl_vars['product_type'] == 'translation'): ?>
							<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/m-corrector-validation-popup-ebooker-translations.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<?php else: ?>
							<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/m-corrector-validation-popup-ebooker.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<?php endif; ?>
					</div>	
				<?php else: ?>			
					<?php if ($this->_tpl_vars['product_type'] == 'translation'): ?>	
						<div class="stencilTableWrapper">
							<table class="table">							
								<tbody>
									<?php if ($this->_tpl_vars['stencilsDetails'] | @ count > 0): ?>
										<?php $_from = $this->_tpl_vars['stencilsDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['stencil'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['stencil']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['stencil']):
        $this->_foreach['stencil']['iteration']++;
?>
											<?php $this->assign('text_index', ($this->_foreach['stencil']['iteration']-1)+1); ?>
											<?php $this->assign('nindex', ($this->_foreach['stencil']['iteration']-1)); ?>
												
												<div class="translationBox-default">
													<span class="boxTitle">
														<h5>Sentence <?php echo $this->_tpl_vars['text_index']; ?>
</h5>
														<a href="javascript:void(0);"><i class="icon-chevron-up"></i></a>
													</span>
													<div class="boxContent slidingDiv">
														<div class="span6 txtOrignal">
															<label for="">Orignal sentence</label>
															<p><?php echo $this->_tpl_vars['translateStencils'][$this->_tpl_vars['nindex']]; ?>
</p>
														</div>
														<div class="span6">
															<label for="">Your translation</label>
															<textarea  name="stencil_text[]" id="stencil_text_<?php echo $this->_tpl_vars['text_index']; ?>
" rows="3" onkeyup="auto_grow(this)" placeholder="Please enter you translation here"><?php echo $this->_tpl_vars['stencil']; ?>
</textarea>
															<span class="wordCount"></span>
															<p class="missingToken"></p>
															<p class="duplicateContentAlert"></p>
															<a href="" class="duplicateShowCTA" target="_blank" style="display:none">See version with plagirism	</a>
															<div class="duplicateShowContent"></div>
														</div>
													</div>								
												</div>
										<?php endforeach; endif; unset($_from); ?>							
									<?php else: ?>
										<tr><td colspan="4"></td></tr>
									<?php endif; ?>	
										<tr>
											<td colspan="4">
												<span class="span12 error" id="plag_error"></span>
											</td>
										</tr>
								</tbody>
							</table>
						</div>
					<?php else: ?>	
						<div class="stencilTableWrapper">
							<table class="table">									
								<table class="table">							
									<tbody>
										<?php if ($this->_tpl_vars['stencilsDetails'] | @ count > 0): ?>
											<?php $_from = $this->_tpl_vars['stencilsDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['stencil'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['stencil']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['stencil']):
        $this->_foreach['stencil']['iteration']++;
?>
												<?php $this->assign('text_index', ($this->_foreach['stencil']['iteration']-1)+1); ?>
												<tr>
													<td class="stencilNum"><span><?php echo $this->_tpl_vars['text_index']; ?>
</span></td>
													<td class="stencilTextArea ">
														<textarea  name="stencil_text[]" id="stencil_text_<?php echo $this->_tpl_vars['text_index']; ?>
" rows="3" onkeyup="auto_grow(this)"><?php echo $this->_tpl_vars['stencil']; ?>
</textarea>
														<span class="wordCount"></span>
														<p class="missingToken"></p>
														<p class="duplicateContentAlert"></p>
														<a href="" class="duplicateShowCTA" target="_blank" style="display:none">See version with plagirism	</a>
														<div class="duplicateShowContent"></div>
													</td>
												</tr>
											<?php endforeach; endif; unset($_from); ?>									
										<?php else: ?>
											<tr><td colspan="4"></td></tr>
										<?php endif; ?>	
										<tr>
											<td colspan="4">
												<span class="span12 error" id="plag_error"></span>
											</td>
										</tr>
									</tbody>
								</table> 
							</table>
						</div>					
					<?php endif; ?>
				<?php endif; ?>	
			</section>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/article-comments.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		<div class="span3">
		<!--  right column  -->
			<aside>
				<div class="aside-bg">
					<div id="selected-editor" class="aside-block">
						<div class="editor-container">
							<h4>The Writer</h4>
							<a href="/contrib/public-profile?user_id=<?php echo $this->_tpl_vars['article']['writer']; ?>
" data-target="#viewProfile-ajax" data-toggle="modal" role="button"><img src="<?php echo $this->_tpl_vars['article']['writer_pic_profile']; ?>
" alt="<?php echo $this->_tpl_vars['article']['writer_name']; ?>
"></a>							
							<p class="editor-name"><?php echo $this->_tpl_vars['article']['writer_name']; ?>
</p>
						</div>
					</div>


					<div class="aside-block" id="garantee">
						<h4>Your Guarantees</h4>
						<dl>
							<dt><span class="umbrella"></span>Edit-place is your mediator</dt>
							<dd>In case of disputes/issues (delivery deadline, article rewrite/amendment, refund...)</dd>
							<dt><span class="locked"></span>Secure payment</dt>
							<dd>Our online payment solution guarantees a hassle-free transaction</dd>
						</dl>
					</div>
				</div>
			</aside>
		</div>
	</div>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
</div>
<?php echo '
<script type="text/javascript">
var cur_date=0,js_date=0,diff_date=0;
cur_date='; ?>
<?php echo time(); ?>
<?php echo ';
var category_name="'; ?>
<?php echo $this->_tpl_vars['missionDetails'][0]['category_name']; ?>
<?php echo '";
 //console.log(category_name);
js_date=(new Date().getTime())/ 1000;
diff_date=Math.floor(js_date-cur_date);
	$("#menu_ongoing").addClass("active");
	startMissionTimer(\'dtime\',\'dtext\');	

	$(\'body\').removeClass(\'homepage\');
	$(\'body\').addClass(\'mission\');
</script>
'; ?>

<!-- ***** Modal collections -->
<!-- ajax use start -->
<div id="fix-article" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Proofreading of article</h3>
	</div>
	<div class="modal-body">
	</div>
</div>
	<!-- ajax use start -->
	<div id="viewProfile-ajax" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">Public Profile</h3>
			</div>
		<div class="modal-body">
		</div>
	</div>
	<!-- ajax use end -->