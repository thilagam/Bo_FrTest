<?php /* Smarty version 2.6.19, created on 2015-06-30 15:30:31
         compiled from Contrib/popup_correction.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'zero_cut', 'Contrib/popup_correction.phtml', 85, false),)), $this); ?>
<?php echo '
<script type="text/javascript">
// tooltip activation
    $("[rel=tooltip]").tooltip();
	$("[rel=popover]").popover();	
	startTimer(\'time1\',\'text1\'); //timer ids starting with
	startTimer(\'time2\',\'text2\');//timer ids starting with
</script>
'; ?>

<?php if ($this->_tpl_vars['correctionDetails'] | @ count > 0): ?>
	<?php $_from = $this->_tpl_vars['correctionDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['details'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['details']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['correction']):
        $this->_foreach['details']['iteration']++;
?>
	<section id="a_o">
		<div class="mc">
			<?php if ($this->_tpl_vars['correction']['missiontest'] == 'yes'): ?>
				<span class="label label-test-mission" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Proofreading Test Mission</span>
			<?php else: ?>
				<span class="label label-correction" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Proofreading Mission</span>
			<?php endif; ?>
			<h2><?php echo $this->_tpl_vars['correction']['title']; ?>
</h2>

			<div class="summary clearfix">
				<ul class="unstyled">
					<li><strong>Proofreading</strong> <span class="bullet">&#9679;</span></li>
					<li> Language : <img class="flag flag-<?php echo $this->_tpl_vars['correction']['language']; ?>
" src="/FO/images/shim.gif"> <span class="bullet">&#9679;</span></li>
					<li>Category : <?php echo $this->_tpl_vars['correction']['category']; ?>
 <span class="bullet">&#9679;</span></li>
					<li>Nb. of words : <?php echo $this->_tpl_vars['correction']['num_min']; ?>
 - <?php echo $this->_tpl_vars['correction']['num_max']; ?>
 / article<span class="bullet">&#9679;</span></li>
					<li><a href="#comment" class="scroll" id="comment_count_1"><i class="icon-comment"></i> <?php echo $this->_tpl_vars['commentCount']; ?>
</a></li>
					<?php if ($this->_tpl_vars['correction']['spec_exists'] == 'yes'): ?>
						<li class="pull-right"><a href="/contrib/download-file?type=correctionbrief&article_id=<?php echo $this->_tpl_vars['correction']['articleid']; ?>
" class="btn btn-small btn-success"><i class="icon-white icon-circle-arrow-down"></i> Download client's brief</a></li>
					<?php endif; ?>	
				</ul>
			</div>

			<div class="a_o-details mod">
				<!-- start, colonne generale -->
				<div class="row-fluid">
					<div class="span7">
						<!-- start, colonne stat -->
						<div class="row-fluid stat-block">
							<div class="span4 section">Time remaining
								<p>
									<?php if ($this->_tpl_vars['upcoming'] == 'yes'): ?>
										&#9679;&#9679;&#9679;
									<?php else: ?>
									<span id="time1_<?php echo $this->_tpl_vars['correction']['articleid']; ?>
_<?php echo $this->_tpl_vars['correction']['timestamp']; ?>
">
										<span id="text1_<?php echo $this->_tpl_vars['correction']['articleid']; ?>
_<?php echo $this->_tpl_vars['correction']['timestamp']; ?>
"><?php echo $this->_tpl_vars['correction']['timestamp']; ?>
</span>
									</span>
									<?php endif; ?>		
								</p>
								</div>
								<div class="span4 section">Proofread time
								<p>
									<?php if ($this->_tpl_vars['upcoming'] == 'yes'): ?>
										&#9679;&#9679;&#9679;
									<?php else: ?>
									<?php echo $this->_tpl_vars['correction']['correction_submission_text']; ?>

									<?php endif; ?>	
								</p>
								</div>
							<div class="span4 section">Client<p><img src="<?php echo $this->_tpl_vars['correction']['client_pic']; ?>
"  class="logo"/></p></div>
						</div>
						<div class="row-fluid note">
							<div class="span5">
								<p>
									<img src="<?php echo $this->_tpl_vars['correction']['writer_pic']; ?>
" class="pull-left photo">Author
									<div class="name">
										<?php if ($this->_tpl_vars['correction']['writer_name']): ?>
											<?php echo $this->_tpl_vars['correction']['writer_name']; ?>

										<?php else: ?>
											Inconnu
										<?php endif; ?>	
									</div>
								</p>
							</div>
							<div class="span7">
								<p><i class="icon-arrow-left icon-white"></i> You will be proofreading this author's articles</p>
							</div>
						</div>
						<!-- end, colonne stat -->
					</div>

					<div class="span5">
						<table class="table table-hover">
						<?php if ($this->_tpl_vars['upcoming'] == 'yes'): ?>
								<tr><td class="title">Price Range</td><td class="countdown"><em><?php echo ((is_array($_tmp=$this->_tpl_vars['correction']['correction_pricemin'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['correction']['correction_pricemax'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &euro;</em></td></tr>
								<tr><td class="title">Open offer</td><td class="countdown"><?php echo $this->_tpl_vars['correction']['publishtime_format']; ?>
</td></tr>
								<tr>
									<td colspan="2" class="cta" >
										<span id="alert_correction_<?php echo $this->_tpl_vars['correction']['deliveryid']; ?>
">											
											<?php if ($this->_tpl_vars['correction']['alert_subscribe'] == 'no'): ?>
												<button class="btn" onclick="subscribeAOAlert('<?php echo $this->_tpl_vars['correction']['deliveryid']; ?>
','yes','article-correction');"><i class="icon-bell"></i>Receive an alert</button>
											<?php elseif ($this->_tpl_vars['correction']['alert_subscribe'] == 'yes'): ?>
												<a class="btn btn-small btn-primary" onclick="subscribeAOAlert('<?php echo $this->_tpl_vars['correction']['deliveryid']; ?>
','no','article-correction');"><i class="icon-remove icon-white"></i>  Alert set</a>
											<?php endif; ?>
										</span>
										
									</td>
								</tr>
						<?php else: ?>
							<tr>
								<td class="title">Contributors</td><td class="countdown"><em><a href="#" rel="popover" data-original-title="Contributor information" data-placement="left" data-html="true"  <?php if ($this->_tpl_vars['correction']['participants_pic']): ?> data-content="<?php echo $this->_tpl_vars['correction']['participants_pic']; ?>
" data-trigger="hover"<?php endif; ?>><?php echo $this->_tpl_vars['correction']['participants']; ?>
</a></em></td>
							</tr>
							<tr>
								<td class="title">Price Range</td><td class="countdown">
									<em><?php echo ((is_array($_tmp=$this->_tpl_vars['correction']['correction_pricemin'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 - <?php echo ((is_array($_tmp=$this->_tpl_vars['correction']['correction_pricemax'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 
									<?php if ($this->_tpl_vars['correction']['currency'] == 'euro'): ?>
										&euro;
									<?php else: ?>
										&pound;
									<?php endif; ?>
									</em>
								</td>
							</tr>
							<tr>
								<td class="title">Last rate offered</td><td class="countdown"><em><a href="#" rel="popover" data-original-title="All offered rates" data-placement="left" data-html="true"  <?php if ($this->_tpl_vars['correction']['participants_price']): ?> data-content="<?php echo $this->_tpl_vars['correction']['participants_price']; ?>
" data-trigger="hover"<?php endif; ?>><?php echo $this->_tpl_vars['correction']['latestPrice']; ?>
 
								<?php if ($this->_tpl_vars['correction']['currency'] == 'euro'): ?>
									&euro;
								<?php else: ?>
									&pound;
								<?php endif; ?>
								</a></em></td>
							</tr>
							<?php if (! $this->_tpl_vars['request_from']): ?>
							<tr>
								<td colspan="2" class="cta" id="tender_select_<?php echo $this->_tpl_vars['correction']['articleid']; ?>
">
								<?php if ($this->_tpl_vars['correction']['display'] == 'no' || $this->_tpl_vars['disabled'] == 'yes'): ?>
									<button class="btn btn-large btn-primary disabled">Post added</button>	
								<?php elseif ($this->_tpl_vars['correction']['attended'] == 'YES'): ?>								
									<button  onClick="fnCartCorrectionModifiers('c_remove','<?php echo $this->_tpl_vars['correction']['articleid']; ?>
','','<?php echo $this->_tpl_vars['correction']['deliveryid']; ?>
');" data-loading-text="Processing addition" class="btn btn-large btn-danger">Post added</button>
								<?php elseif ($this->_tpl_vars['correction']['attended'] == 'NO'): ?>
									<button  onClick="fnCartCorrectionModifiers('c_add','<?php echo $this->_tpl_vars['correction']['articleid']; ?>
','','<?php echo $this->_tpl_vars['correction']['deliveryid']; ?>
');" data-loading-text="Processing addition" class="btn btn-large btn-primary">Add to my selection</button>
								<?php endif; ?>									
								</td>
							</tr>
							<?php endif; ?>
						<?php endif; ?>	
						</table>
					</div>
				</div>
				<!-- end, colonne generale -->
			</div>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/article-comments.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>	
		</div>
	</section>
	
	<a href="#brand" class="pull-right btn btn-small disabled anchor-top scroll"><i class="icon-arrow-up"></i></a>
	<div id = "confirmDiv"></div>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/popup_confirm_selection.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>	

<?php echo '
<script>
 	$(".scroll").click(function(event){		
		event.preventDefault();
		$(\'html,body\').animate({scrollTop:$(this.hash).offset().top}, 500);
	});
</script>	
'; ?>