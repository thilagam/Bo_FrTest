<?php /* Smarty version 2.6.19, created on 2015-07-07 13:32:09
         compiled from Contrib/popup_mission.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'zero_cut', 'Contrib/popup_mission.phtml', 104, false),)), $this); ?>
<!--$finished to display finshed Ao style
$upcmoing to check upcoming ao block styles--> 
<?php echo '
<script type="text/javascript">
	var cur_date='; ?>
<?php echo time(); ?>
<?php echo ';
	var js_date=(new Date().getTime())/ 1000;
	var diff_date=Math.floor(js_date-cur_date);
// tooltip activation
    $("[rel=tooltip]").tooltip();
	$("[rel=popover]").popover();	
	startTimer(\'time1\',\'text1\'); //timer ids starting with
	startTimer(\'time2\',\'text2\');//timer ids starting with
</script>
'; ?>

<?php if ($this->_tpl_vars['articleDetails'] | @ count > 0): ?>
	<?php $_from = $this->_tpl_vars['articleDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['details'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['details']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['details']['iteration']++;
?>
		<section id="a_o">
			<?php if ($this->_tpl_vars['mission_type'] == 'premium'): ?>
				<div class="mp">
					<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
						<span class="label label-test-mission" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right"><i class="icon-star icon-white"></i>  Staffing</span>
					<?php else: ?>
						<span class="label label-premium" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Premium Mission</span>
					<?php endif; ?>
			<?php elseif ($this->_tpl_vars['mission_type'] == 'nopremium'): ?>	
				<div class="dl">
					<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
						<span class="label label-test-mission" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right"><i class="icon-star icon-white"></i>  Staffing</span>
					<?php else: ?>
						<span class="label label-quote" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right">Freedom Mission</span>
					<?php endif; ?>
			<?php endif; ?>	
			<?php if ($this->_tpl_vars['article']['view_to'] == 'sc'): ?>
				<span data-original-title="Senior level" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>Senior</span>
			<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc'): ?>
				<span data-original-title="Available to Junior" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>Junior</span>
			<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc0'): ?>
				<span data-original-title="Available to Beginner" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>Beginner</span>	
			<?php endif; ?>			
			<?php if ($this->_tpl_vars['article']['link_quiz'] == 'yes' && $this->_tpl_vars['article']['quiz']): ?>
				<span class="label label-inverse" data-original-title="Participation soumis &agrave; un Quizz" rel="tooltip" data-placement="right">Quizz</span>
			<?php endif; ?>
				<h2><?php echo $this->_tpl_vars['article']['title']; ?>
 <?php echo $this->_tpl_vars['article']['picon']; ?>
</h2>				

				<div class="summary clearfix">
					<ul class="unstyled">
						<li><strong>Call to writers</strong> <span class="bullet">&#9679;</span></li>
						<li> Language : <img class="flag flag-<?php echo $this->_tpl_vars['article']['language']; ?>
" src="/FO/images/shim.gif" data-placement="left" rel="tooltip" data-original-title="<?php echo $this->_tpl_vars['article']['language_name']; ?>
"> <span class="bullet">&#9679;</span></li>
						<li>Category : <?php echo $this->_tpl_vars['article']['category']; ?>
 <span class="bullet">&#9679;</span></li>
						<li>Nb. Of words : <?php echo $this->_tpl_vars['article']['num_min']; ?>
 - <?php echo $this->_tpl_vars['article']['num_max']; ?>
 / article<span class="bullet">&#9679;</span></li>
						<li><a href="#comment" class="scroll" id="comment_count_1"><i class="icon-comment"></i> <?php echo $this->_tpl_vars['commentCount']; ?>
</a></li>
						<?php if ($this->_tpl_vars['article']['spec_exists'] == 'yes'): ?>
							<li class="pull-right"><a href="/contrib/download-file?type=clientbrief&article_id=<?php echo $this->_tpl_vars['article']['articleid']; ?>
" class="btn btn-small btn-success"><i class="icon-white icon-circle-arrow-down"></i> Download the client brief</a></li>
						<?php endif; ?>	
					</ul>
				</div>

				<div class="a_o-details">
					<!-- start, colonne generale -->
					<div class="row-fluid">
						<div class="span7">
							<!-- start, colonne stat -->
							<div class="row-fluid stat-block">
								<div class="span4 section">Time remaining
								<p>
									<?php if ($this->_tpl_vars['upcoming'] == 'yes'): ?>
										&#9679;&#9679;&#9679;
										
									<?php elseif ($this->_tpl_vars['finished'] == 'yes'): ?>
										Termin&eacute;
									<?php else: ?>	
										<span id="time1_<?php echo $this->_tpl_vars['article']['articleid']; ?>
_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
">
											<span id="text1_<?php echo $this->_tpl_vars['article']['articleid']; ?>
_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
"><?php echo $this->_tpl_vars['article']['timestamp']; ?>
</span>
										</span>
									<?php endif; ?>	
								</p>
								</div>
								<div class="span4 section">Production time
								<p>
									<?php if ($this->_tpl_vars['upcoming'] == 'yes'): ?>
										&#9679;&#9679;&#9679;
									<?php else: ?>
										<?php echo $this->_tpl_vars['article']['article_submit_time_text']; ?>

									<?php endif; ?>	
								</p>
								</div>
								<div class="span4 section">Client<p><img src="<?php echo $this->_tpl_vars['article']['client_pic']; ?>
"  class="logo"/></p></div>
							</div>
							<div class="row-fluid note">
								<div class="span12">
									<?php if ($this->_tpl_vars['mission_type'] == 'premium'): ?>
										<p><i class="icon-info-sign icon-white"></i> This mission is guaranteed by Edit-place. If you are selected, you are guaranteed to write immediately</p>
									<?php elseif ($this->_tpl_vars['mission_type'] == 'nopremium'): ?>																			
										<p><i class="icon-info-sign icon-white"></i> <strong>Try your luck! Please note that this may be a simple information-gathering exercise or a full writing mission.</p>
									<?php endif; ?>		
								</div>
							</div>
							<!-- end, colonne stat -->
						</div>
						<div class="span5">
							<table class="table table-hover">
							<?php if ($this->_tpl_vars['upcoming'] == 'yes'): ?>
								<?php if ($this->_tpl_vars['mission_type'] == 'premium' || ( $this->_tpl_vars['article']['AOtype'] == 'private' && $this->_tpl_vars['article']['price_max'] )): ?>
									<tr><td class="title">Price Range</td><td class="countdown"><em><?php echo ((is_array($_tmp=$this->_tpl_vars['article']['price_min'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['article']['price_max'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 
									<?php if ($this->_tpl_vars['article']['currency'] == 'euro'): ?>
										&euro;
									<?php else: ?>
										&pound;
									<?php endif; ?>
									</em></td></tr>
								<?php endif; ?>
								<tr><td class="title">Open offer</td><td class="countdown"><?php echo $this->_tpl_vars['article']['publishtime_format']; ?>
</td></tr>
								<tr>
									<td colspan="2" class="cta" >
										<span id="alert_<?php echo $this->_tpl_vars['article']['deliveryid']; ?>
">											
											<?php if ($this->_tpl_vars['article']['alert_subscribe'] == 'no'): ?>
												<button class="btn" onclick="subscribeAOAlert('<?php echo $this->_tpl_vars['article']['deliveryid']; ?>
','yes','article');"><i class="icon-bell"></i> Receive an alert</button>
											<?php elseif ($this->_tpl_vars['article']['alert_subscribe'] == 'yes'): ?>
												<a class="btn btn-small btn-primary" onclick="subscribeAOAlert('<?php echo $this->_tpl_vars['article']['deliveryid']; ?>
','no','article');"><i class="icon-remove icon-white"></i> Alert set</a>
											<?php endif; ?>
										</span>
										
									</td>
								</tr>
							<?php else: ?>
								<tr><td class="title">Contributors <?php echo $this->_tpl_vars['article']['picon']; ?>
</td><td class="countdown"><em><a href="#" rel="popover" data-original-title="Contributor information" data-placement="left" data-html="true" <?php if ($this->_tpl_vars['article']['participants_pic']): ?> data-content="<?php echo $this->_tpl_vars['article']['participants_pic']; ?>
" data-trigger="hover"<?php endif; ?>><?php echo $this->_tpl_vars['article']['participants']; ?>
</a></em></td></tr>							
								
								<?php if ($this->_tpl_vars['mission_type'] == 'premium' || ( $this->_tpl_vars['article']['AOtype'] == 'private' && $this->_tpl_vars['article']['price_max'] )): ?>
									<tr><td class="title">Price Range</td><td class="countdown"><?php if ($this->_tpl_vars['article']['pricedisplay'] == 'yes'): ?><em><?php echo ((is_array($_tmp=$this->_tpl_vars['article']['price_min'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['article']['price_max'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 
									<?php if ($this->_tpl_vars['article']['currency'] == 'euro'): ?>
										&euro;
									<?php else: ?>
										&pound;
									<?php endif; ?>
									</em><?php else: ?>/<?php endif; ?></td></tr>
								<?php endif; ?>
								<?php if ($this->_tpl_vars['article']['pricedisplay'] == 'yes'): ?>
								<tr><td class="title">Last rate offered</td><td class="countdown"><em><a href="#" rel="popover" data-original-title="All offered rates" data-placement="left" data-html="true" <?php if ($this->_tpl_vars['article']['participants_price']): ?> data-content="<?php echo $this->_tpl_vars['article']['participants_price']; ?>
" data-trigger="hover"<?php endif; ?>>
								<?php if ($this->_tpl_vars['article']['latestPrice']): ?> 
									<?php echo $this->_tpl_vars['article']['latestPrice']; ?>
 
									<?php if ($this->_tpl_vars['article']['currency'] == 'euro'): ?>
										&euro;
									<?php else: ?>
										&pound;
									<?php endif; ?>
								<?php else: ?>
									NA
								<?php endif; ?>	
								</a></em></td></tr>	
								<?php endif; ?>	
								<?php if ($this->_tpl_vars['finished'] == 'yes'): ?>
									<tr class="warning">
										<td colspan="2" class="cta"><strong><i class="icon-warning-sign"></i> Participation deadline expired</strong></td>
									</tr>
								<?php elseif ($this->_tpl_vars['no_permission'] == 'yes'): ?>
									<tr class="warning">
											<td colspan="2" class="cta"><strong><i class="icon-warning-sign"></i> Sorry! The offer is reserved exclusively to 
											<?php if ($this->_tpl_vars['article']['view_to'] == 'sc'): ?>
												<span class="label label-level"><i class="icon-bookmark"></i>Senior</span>
											<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc'): ?>
												<span class="label label-level"><i class="icon-bookmark"></i>Junior</span>
											<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc0'): ?>
												<span class="label label-level"><i class="icon-bookmark"></i>Beginner</span>												
											<?php elseif ($this->_tpl_vars['article']['view_to'] == 'sc,jc' || $this->_tpl_vars['article']['view_to'] == 'jc,sc'): ?>
												<span class="label label-level"><i class="icon-bookmark"></i>Senior</span><span class="label label-level"><i class="icon-bookmark"></i>Junior</span>
											<?php endif; ?>
											</strong></td>
									</tr>
								<?php elseif (! $this->_tpl_vars['request_from']): ?>
									<tr><td colspan="2" class="cta" id="tender_select_<?php echo $this->_tpl_vars['article']['articleid']; ?>
">
									
									<?php if ($this->_tpl_vars['article']['display'] == 'no' || $this->_tpl_vars['disabled'] == 'yes'): ?>
										<button class="btn btn-large btn-primary disabled">Post added</button>	
									<?php elseif ($this->_tpl_vars['article']['attended'] == 'YES'): ?>								
										<button  onClick="fnCartModifiers('remove','<?php echo $this->_tpl_vars['article']['articleid']; ?>
','','<?php echo $this->_tpl_vars['article']['deliveryid']; ?>
');" class="btn btn-large btn-danger">Post added</button>
									<?php elseif ($this->_tpl_vars['article']['attended'] == 'NO'): ?>
										<button  onClick="fnCartModifiers('add','<?php echo $this->_tpl_vars['article']['articleid']; ?>
','','<?php echo $this->_tpl_vars['article']['deliveryid']; ?>
');" class="btn btn-large btn-primary">Add to my selection</button>
									<?php endif; ?>	
									</td></tr>
								<?php endif; ?>	
							<?php endif; ?>	
							</table>
						</div>
					</div>
					<!-- end, colonne generale -->
				</div>
				<?php if ($this->_tpl_vars['no_permission'] == 'yes'): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/mission-badStatus.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>
				<?php if (! $this->_tpl_vars['finished'] && ! $this->_tpl_vars['no_permission']): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/article-comments.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>	
			</div>
		</section>
		<a href="#brand" class="pull-right btn btn-small disabled anchor-top scroll"><i class="icon-arrow-up"></i></a>
		<div id = "confirmDiv"></div>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/popup_confirm_selection.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<!--Quiz Confirmation modal-->
		<div id="gotoQuizz" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel">Pre-selection quiz</h3>
			</div>
			<div class="modal-body">
				<p> To add this offer to your selection, you must complete a quiz on the related theme.</p>
				<p><i class="icon-time"></i>  You will have <strong><?php echo $this->_tpl_vars['article']['quiz_duration']; ?>
 minutes</strong> to answer the questions. Ready ?</p> 
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
				<a class="btn btn-primary" data-target="#playQuizz-ajax" data-toggle="modal" role="button" href="/quiz/participate-quiz?article_id=<?php echo $this->_tpl_vars['article']['articleid']; ?>
&quiz_identifier=<?php echo $this->_tpl_vars['article']['quiz']; ?>
" id="btnPlayQuizz">Start the quiz</a>
				
			</div>
		</div>
		
	<?php endforeach; endif; unset($_from); ?>
<?php else: ?>
<section>
	<div class="row-fluid">
		<div class="pull-center span10 offset1">
			<p class="text-error lead">You cannot take part in this mission.</p>
		</div>
	</div>
</section>
<?php endif; ?>
<?php echo '
<script>
 	$(".scroll").click(function(event){		
		event.preventDefault();
		$(\'html,body\').animate({scrollTop:$(this.hash).offset().top}, 500);
	});
	$(\'#btnPlayQuizz\').click(function () {
		
		/* $(\'#playQuizz-ajax\').modal(
		{
				remote: \'quizz.html\'
		}
    	);	
		$(\'#playQuizz-ajax\').modal(\'show\'); */
		$(\'#gotoQuizz\').modal(\'hide\');
		$(\'#viewOffer-ajax\').modal(\'hide\');
		
		
		});	
</script>	
'; ?>