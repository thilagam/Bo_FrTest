<?php /* Smarty version 2.6.19, created on 2015-07-13 16:27:57
         compiled from Contrib/ongoing.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Contrib/ongoing.phtml', 31, false),array('modifier', 'strlen', 'Contrib/ongoing.phtml', 44, false),array('modifier', 'stripslashes', 'Contrib/ongoing.phtml', 44, false),array('modifier', 'truncate', 'Contrib/ongoing.phtml', 44, false),)), $this); ?>
<?php echo '
<script type="text/javascript">
var cur_date=0,js_date=0,diff_date=0;
cur_date='; ?>
<?php echo time(); ?>
<?php echo ';
js_date=(new Date().getTime())/ 1000;
diff_date=Math.floor(js_date-cur_date);
$("#menu_ongoing").addClass("active");
startMissionTimer(\'dtime\',\'dtext\');

var mtype=\''; ?>
<?php echo $_GET['mission_type']; ?>
<?php echo '\';
var mid=\''; ?>
<?php echo $_GET['mission_identifier']; ?>
<?php echo '\';

if(mtype && mid )
{
	$.get(\'/contrib/article-details?req_from=ongoing&misson_type=\'+mtype+\'&mission_identifier=\'+mid, function(data) {
		$("#viewOffer-ajax .modal-body").html(data);
		$("#viewOffer-ajax").modal(\'show\');
	});
}


</script>
'; ?>

 <div class="container">
	<div class="row-fluid"><h1>My contributions</h1>  </div>
 
	<div class="row-fluid">
		<div class="span8">
			<section id="quick-classified">
				<div class="mod">
					<h4>Missions Awaiting Allocation <span class="badge"><?php echo count($this->_tpl_vars['awaitingArticles']); ?>
</span></h4>
					<table class="table table-hover">
						<?php if ($this->_tpl_vars['awaitingArticles'] | @ count > 0): ?>	
							<?php $_from = $this->_tpl_vars['awaitingArticles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['articledetails'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['articledetails']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['articledetails']['iteration']++;
?>
							<tr>
								<?php if ($this->_tpl_vars['article']['ao_type'] == 'correction'): ?>
									<td>
										<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
											<span class="label label-test-mission" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right"><i class="icon-star icon-white"></i>  Staffing</span>
										<?php else: ?>
											<span class="label label-correction" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Proofreading  Mission</span>
										<?php endif; ?>
									</td>
									<td class="title"><a href="/contrib/article-details?req_from=ongoing&misson_type=correction&mission_identifier=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span> <?php echo $this->_tpl_vars['article']['picon']; ?>
 </a></td>
								<?php elseif ($this->_tpl_vars['article']['ao_type'] == 'poll_premium'): ?>
										<td><span class="label label-quote-premium" data-original-title="This offer may become a Premium mission" rel="tooltip" data-placement="right">Premium Quote</span></td>
										<td class="title"><a href="/contrib/article-details?req_from=ongoing&misson_type=poll_premium&mission_identifier=<?php echo $this->_tpl_vars['article']['pollId']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span></a></td>
								<?php elseif ($this->_tpl_vars['article']['ao_type'] == 'poll_nopremium'): ?>
									<td><span class="label label-quote" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right">Freedom Quote</span></td>
									<td class="title"><a href="/contrib/article-details?req_from=ongoing&misson_type=poll_nopremium&mission_identifier=<?php echo $this->_tpl_vars['article']['pollId']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span></a></td>		
								<?php elseif ($this->_tpl_vars['article']['premium_option']): ?>
									<td>
										<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
										<span class="label label-test-mission" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right"><i class="icon-star icon-white"></i>  Staffing</span>
										<?php else: ?>
										<span class="label label-premium" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Premium Mission</span>
										<?php endif; ?>
									</td>
									<td class="title"><a href="/contrib/article-details?req_from=ongoing&misson_type=premium&mission_identifier=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span> <?php echo $this->_tpl_vars['article']['picon']; ?>
 <?php echo $this->_tpl_vars['article']['qicon']; ?>
</a></td>
								<?php else: ?>	
									<td>
										<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
											<span class="label label-test-mission" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right"><i class="icon-star icon-white"></i>  Staffing</span>
										<?php else: ?>
											<span class="label label-quote" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right">Freedom Mission</span>
										<?php endif; ?>
									</td>
									<td class="title"><a href="/contrib/article-details?req_from=ongoing&misson_type=nopremium&mission_identifier=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span> <?php echo $this->_tpl_vars['article']['picon']; ?>
 <?php echo $this->_tpl_vars['article']['qicon']; ?>
</a></td>
								<?php endif; ?>							
								
								<td class="countdown">
									<?php if ($this->_tpl_vars['article']['ao_type'] == 'correction'): ?>
										<span id="time_<?php echo $this->_tpl_vars['article']['article_id']; ?>
-correction_<?php echo $this->_tpl_vars['article']['correction_participationexpires']; ?>
">
											<span id="text_<?php echo $this->_tpl_vars['article']['article_id']; ?>
-correction_<?php echo $this->_tpl_vars['article']['correction_participationexpires']; ?>
"><?php echo $this->_tpl_vars['article']['correction_participationexpires']; ?>
</span>
										</span>
									<?php elseif ($this->_tpl_vars['article']['ao_type'] == 'poll_nopremium' || $this->_tpl_vars['article']['ao_type'] == 'poll_premium'): ?>	
										<span id="time_<?php echo $this->_tpl_vars['article']['articleid']; ?>
_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
">
											<span id="text_<?php echo $this->_tpl_vars['article']['articleid']; ?>
_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
"><?php echo $this->_tpl_vars['article']['timestamp']; ?>
</span>
										</span>	
									<?php else: ?>
										<span id="time_<?php echo $this->_tpl_vars['article']['article_id']; ?>
_<?php echo $this->_tpl_vars['article']['participation_expires']; ?>
">
											<span id="text_<?php echo $this->_tpl_vars['article']['article_id']; ?>
_<?php echo $this->_tpl_vars['article']['participation_expires']; ?>
"><?php echo $this->_tpl_vars['article']['participation_expires']; ?>
</span>
										</span>
									<?php endif; ?>	
								</td>
							</tr>	
							<?php endforeach; endif; unset($_from); ?>
						<?php else: ?>
							<tr>
								<td colspan="3">
									<span class="no-data">There are no articles awaiting  allocation</span>
								</td>
							</tr>
						<?php endif; ?>						
					</table>
				</div>

				<!-- List of classifieds that contributor might like -->
				<div class="mod">
					<span class="pull-right icon-waiting-quote" data-original-title="Quick, respond by sending in your quote" rel="tooltip"></span>
					<h4>These Missions May be of interest to you...</h4>
					<table class="table table-hover">
						<?php if (count($this->_tpl_vars['recent_AO_Offers']) > 0): ?>
							<?php $_from = $this->_tpl_vars['recent_AO_Offers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['offer']):
?>
								<tr>
									<?php if ($this->_tpl_vars['offer']['ao_type'] == 'correction'): ?>
										<td>
											<?php if ($this->_tpl_vars['offer']['missiontest'] == 'yes'): ?>
												<span class="label label-test-mission" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Proofreading Test Mission</span>
											<?php else: ?>
												<span class="label label-correction" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Proofreading Mission</span>
											<?php endif; ?>
										</td>
										<td class="title"><a href="/contrib/article-details?misson_type=correction&mission_identifier=<?php echo $this->_tpl_vars['offer']['articleid']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span> <?php echo $this->_tpl_vars['offer']['picon']; ?>
</a></td>
									<?php elseif ($this->_tpl_vars['offer']['ao_type'] == 'poll_premium'): ?>
										<td><span class="label label-quote-premium" data-original-title="This offer may become a Premium mission" rel="tooltip" data-placement="right">Premium Quote</span></td>
										<td class="title"><a href="/contrib/article-details?misson_type=poll_premium&mission_identifier=<?php echo $this->_tpl_vars['offer']['pollId']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span></a></td>
									<?php elseif ($this->_tpl_vars['offer']['ao_type'] == 'poll_nopremium'): ?>
										<td><span class="label label-quote" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right">Freedom Quote</span></td>
										<td class="title"><a href="/contrib/article-details?misson_type=poll_nopremium&mission_identifier=<?php echo $this->_tpl_vars['offer']['pollId']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span></a></td>	
									<?php elseif ($this->_tpl_vars['offer']['premium_option']): ?>	
										<td>
											<?php if ($this->_tpl_vars['offer']['missiontest'] == 'yes'): ?>
												<span class="label label-test-mission" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right"><i class="icon-star icon-white"></i>  Staffing</span>
											<?php else: ?>
												<span class="label label-premium" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Premium Mission</span>
											<?php endif; ?>
										</td>
										<td class="title">
											<?php if ($this->_tpl_vars['offer']['missiontest'] == 'yes'): ?>
												<a href="/recruitment/participation-challenge?recruitment_id=<?php echo $this->_tpl_vars['offer']['deliveryid']; ?>
"><span <?php if (((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 55): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>>
													<b><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 70, "...", true) : smarty_modifier_truncate($_tmp, 70, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</b>
												</a>
											<?php else: ?>
												<a href="/contrib/article-details?misson_type=premium&mission_identifier=<?php echo $this->_tpl_vars['offer']['articleid']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 70): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 70, "...", true) : smarty_modifier_truncate($_tmp, 70, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span> <?php echo $this->_tpl_vars['offer']['picon']; ?>
 <?php echo $this->_tpl_vars['offer']['qicon']; ?>
</a>
											<?php endif; ?>
										</td>
									<?php elseif (! $this->_tpl_vars['offer']['premium_option']): ?>			
										<td>
											<?php if ($this->_tpl_vars['offer']['missiontest'] == 'yes'): ?>
												<span class="label label-test-mission" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right"><i class="icon-star icon-white"></i>  Staffing</span>
											<?php else: ?>
												<span class="label label-quote" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right">Freedom Mission</span>
											<?php endif; ?>
										</td>
										<td class="title"><a href="/contrib/article-details?misson_type=nopremium&mission_identifier=<?php echo $this->_tpl_vars['offer']['articleid']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span> <?php echo $this->_tpl_vars['offer']['picon']; ?>
 <?php echo $this->_tpl_vars['offer']['qicon']; ?>
</a></td>
									<?php endif; ?>
									<td class="countdown">
										<?php if ($this->_tpl_vars['offer']['ao_type'] == 'correction'): ?>
											<span id="time_<?php echo $this->_tpl_vars['offer']['articleid']; ?>
-correction_<?php echo $this->_tpl_vars['offer']['timestamp']; ?>
">
												<span id="text_<?php echo $this->_tpl_vars['offer']['articleid']; ?>
-correction_<?php echo $this->_tpl_vars['offer']['timestamp']; ?>
"><?php echo $this->_tpl_vars['offer']['timestamp']; ?>
</span>
											</span>
										<?php else: ?>
										<span id="time_<?php echo $this->_tpl_vars['offer']['articleid']; ?>
_<?php echo $this->_tpl_vars['offer']['timestamp']; ?>
">
											<span id="text_<?php echo $this->_tpl_vars['offer']['articleid']; ?>
_<?php echo $this->_tpl_vars['offer']['timestamp']; ?>
"><?php echo $this->_tpl_vars['offer']['timestamp']; ?>
</span>
										</span>
										<?php endif; ?>	
										
									</td>
								</tr>
							
							<?php endforeach; endif; unset($_from); ?>
						<?php else: ?>
							<tr>
								<td colspan="3">
									<span class="no-data">There are no calls for orders at the moment</span>
								</td>	
							</tr>
						<?php endif; ?>
					</table>
					<?php if (count($this->_tpl_vars['recent_AO_Offers']) > 0): ?>
								<a href="/contrib/aosearch"><div class="btn btn-block btn-small">See all the offers</div></a>
					<?php endif; ?>	
				</div>


			</section>
		</div>
		
		<div class="span4">
		<!--  right column  -->
	  
			<aside id="participation">
				<div class="aside-bg mission">
					<h3>My missions</h3>
					<div  id="ongoing-stack">	
						<?php if ($this->_tpl_vars['encoursArticles'] | @ count > 0): ?>	
							<?php $_from = $this->_tpl_vars['encoursArticles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['ongoingarticle'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['ongoingarticle']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['ongoingarticle']['iteration']++;
?>
								
								<div class="aside-block mission">
									<div class="row-fluid">
									<?php if ($this->_tpl_vars['article']['ao_type'] == 'correction'): ?>
										<div class="span7">
										<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
											<span data-placement="right" rel="tooltip" data-original-title="Mission guaranteed by Edit-place" class="label label-test-mission"><i class="icon-star icon-white"></i> Proofreading test</span>
										<?php endif; ?>	
										<a href="/contrib/mission-corrector-deliver?article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
"><strong><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 60): ?> data-original-title="<?php echo $this->_tpl_vars['article']['title']; ?>
" rel="tooltip" data-placement="top"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 60, "...", true) : smarty_modifier_truncate($_tmp, 60, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span></strong></a></div>
									<?php else: ?>
										<div class="span7">
										<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
											<span data-placement="right" rel="tooltip" data-original-title="Mission guaranteed by Edit-place" class="label label-test-mission"><i class="icon-star icon-white"></i>  <i class="icon-star icon-white"></i>  Staffing</span>
											<?php if ($this->_tpl_vars['article']['contract_signed'] == '' || $this->_tpl_vars['article']['contract_signed'] == 'no'): ?>
												<a href="/recruitment/participation-challenge?recruitment_id=<?php echo $this->_tpl_vars['article']['id']; ?>
&article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
#sign-contract"><strong><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 60): ?> data-original-title="<?php echo $this->_tpl_vars['article']['title']; ?>
" rel="tooltip" data-placement="top"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 60, "...", true) : smarty_modifier_truncate($_tmp, 60, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span></strong></a>
											<?php else: ?>
												<a href="/contrib/mission-deliver?article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
"><strong><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 60): ?> data-original-title="<?php echo $this->_tpl_vars['article']['title']; ?>
" rel="tooltip" data-placement="top"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 60, "...", true) : smarty_modifier_truncate($_tmp, 60, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span></strong></a>
											<?php endif; ?>	
											</div>
										<?php else: ?>
											<a href="/contrib/mission-deliver?article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
"><strong><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 60): ?> data-original-title="<?php echo $this->_tpl_vars['article']['title']; ?>
" rel="tooltip" data-placement="top"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 60, "...", true) : smarty_modifier_truncate($_tmp, 60, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span></strong></a></div>
										<?php endif; ?>	
									<?php endif; ?>	
									
									<?php if ($this->_tpl_vars['article']['status'] == 'bid' || $this->_tpl_vars['article']['status'] == 'disapproved' || $this->_tpl_vars['article']['status'] == 'disapprove_client'): ?>	
										<div class="span4 btn-group btn-group-vertical">
											<?php if ($this->_tpl_vars['article']['ao_type'] == 'correction'): ?>
												<a href="/contrib/mission-corrector-deliver?article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
"  class="btn btn-small" data-placement="left" data-original-title="Status" rel="tooltip">
												<?php if ($this->_tpl_vars['article']['status'] == 'bid'): ?>
													Proofread
												<?php else: ?>
													<?php echo $this->_tpl_vars['article']['status_trans']; ?>

												<?php endif; ?>	
												</a>
											<?php else: ?>
												<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
													<?php if ($this->_tpl_vars['article']['contract_signed'] == ''): ?>
														<a href="/recruitment/participation-challenge?recruitment_id=<?php echo $this->_tpl_vars['article']['id']; ?>
&article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
#sign-contract"  class="btn btn-small" data-placement="left" data-original-title="Status" rel="tooltip"> <?php echo $this->_tpl_vars['article']['status_trans']; ?>
</a>
													<?php else: ?>
														<a href="/contrib/mission-deliver?article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
"  class="btn btn-small" data-placement="left" data-original-title="Status" rel="tooltip"> <?php echo $this->_tpl_vars['article']['status_trans']; ?>
</a>
													<?php endif; ?>	
												<?php else: ?>
													<a href="/contrib/mission-deliver?article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
"  class="btn btn-small" data-placement="left" data-original-title="Statut" rel="tooltip"> <?php echo $this->_tpl_vars['article']['status_trans']; ?>
</a>
												<?php endif; ?>	
											<?php endif; ?>	
											<a  class="btn btn-small def-cur" data-placement="left" data-original-title="Delivery date" rel="tooltip"><i class=" icon-time"></i>
											<?php if ($this->_tpl_vars['article']['ao_type'] == 'correction'): ?>
												<span id="time_<?php echo ($this->_foreach['ongoingarticle']['iteration']-1); ?>
_<?php echo $this->_tpl_vars['article']['corrector_submit_expires']; ?>
">
													<span id="text_<?php echo ($this->_foreach['ongoingarticle']['iteration']-1); ?>
_<?php echo $this->_tpl_vars['article']['corrector_submit_expires']; ?>
"></span>
												</span>
											<?php else: ?>
												<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
													<?php if ($this->_tpl_vars['article']['article_submit_expires'] > 0): ?>
												<span id="time_<?php echo ($this->_foreach['ongoingarticle']['iteration']-1); ?>
_<?php echo $this->_tpl_vars['article']['article_submit_expires']; ?>
">
													<span id="text_<?php echo ($this->_foreach['ongoingarticle']['iteration']-1); ?>
_<?php echo $this->_tpl_vars['article']['article_submit_expires']; ?>
"></span>
												</span>
													<?php else: ?>
														<span id="time_<?php echo ($this->_foreach['ongoingarticle']['iteration']-1); ?>
_<?php echo $this->_tpl_vars['article']['participation_expires']; ?>
">
															<span id="text_<?php echo ($this->_foreach['ongoingarticle']['iteration']-1); ?>
_<?php echo $this->_tpl_vars['article']['participation_expires']; ?>
"></span>
														</span>
													<?php endif; ?>
												<?php else: ?>
													<span id="time_<?php echo ($this->_foreach['ongoingarticle']['iteration']-1); ?>
_<?php echo $this->_tpl_vars['article']['article_submit_expires']; ?>
">
														<span id="text_<?php echo ($this->_foreach['ongoingarticle']['iteration']-1); ?>
_<?php echo $this->_tpl_vars['article']['article_submit_expires']; ?>
"></span>
													</span>
												<?php endif; ?>	
											<?php endif; ?>	
											
											</a>
										</div>
									<?php else: ?>
										<div class="span4 btn-group">
										<?php if ($this->_tpl_vars['article']['ao_type'] == 'correction'): ?>
											<a  href="/contrib/mission-corrector-deliver?article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" class="btn btn-small" data-placement="left" data-original-title="Status" rel="tooltip"><?php echo $this->_tpl_vars['article']['status_trans']; ?>
</a>
										<?php else: ?>
											<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
													<a href="/recruitment/participation-challenge?recruitment_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
"  class="btn btn-small" data-placement="left" data-original-title="Status" rel="tooltip"> <?php echo $this->_tpl_vars['article']['status_trans']; ?>
</a>
											<?php else: ?>
												<a  href="/contrib/mission-deliver?article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" class="btn btn-small" data-placement="left" data-original-title="Statut" rel="tooltip"><?php echo $this->_tpl_vars['article']['status_trans']; ?>
</a>
											<?php endif; ?>	
										<?php endif; ?>	
										</div>	
									<?php endif; ?>	
									</div>
								</div>	
								
							<?php endforeach; endif; unset($_from); ?>
						<?php else: ?>
							<div class="aside-block mission">
									<div class="row-fluid">
										<span class="no-data">There are no ongoing articles</span>
									</div>
							</div>
						<?php endif; ?>		
					</div>	

					<div class="aside-block" id="previous-work">
						<h4>All My Work</h4>
						<ul class="nav nav-tabs nav-stacked">
						<?php if ($this->_tpl_vars['publishedArticles'] | @ count > 0): ?>	
							
								<?php $_from = $this->_tpl_vars['publishedArticles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['publishedarticle'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['publishedarticle']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['publishedarticle']['iteration']++;
?>	
									<?php if (($this->_foreach['publishedarticle']['iteration']-1) < 100): ?>
										<li>
										<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>											
											<?php if ($this->_tpl_vars['article']['crt_participate_id']): ?>
												<a href="/contrib/mission-published?<?php if ($this->_tpl_vars['article']['crt_participate_id']): ?>type=correction&<?php endif; ?> 	article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
">
													<span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 40): ?> data-original-title="<?php echo $this->_tpl_vars['article']['title']; ?>
" rel="tooltip" data-placement="top"<?php endif; ?>>
													<span data-placement="right" rel="tooltip" data-original-title="Mission guaranteed by Edit-place" class="label label-test-mission"><i class="icon-star icon-white"></i> Mission de correction test</span>
													<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 40, "...", true) : smarty_modifier_truncate($_tmp, 40, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span>
												</a>
											<?php else: ?>
												<a href="/contrib/mission-published?<?php if ($this->_tpl_vars['article']['crt_participate_id']): ?>type=correction&<?php endif; ?> article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
">
													<span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 40): ?> data-original-title="<?php echo $this->_tpl_vars['article']['title']; ?>
" rel="tooltip" data-placement="top"<?php endif; ?>>
													<span data-placement="right" rel="tooltip" data-original-title="Mission guaranteed by Edit-place" class="label label-test-mission"><i class="icon-star icon-white"></i>  <i class="icon-star icon-white"></i>  Staffing</span>
													<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 40, "...", true) : smarty_modifier_truncate($_tmp, 40, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span>
												</a>	
											<?php endif; ?>
										<?php else: ?>
											<a href="/contrib/mission-published?<?php if ($this->_tpl_vars['article']['crt_participate_id']): ?>type=correction&<?php endif; ?> article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
">
												<span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 40): ?> data-original-title="<?php echo $this->_tpl_vars['article']['title']; ?>
" rel="tooltip" data-placement="top"<?php endif; ?>>
												<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 40, "...", true) : smarty_modifier_truncate($_tmp, 40, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span>
											</a>
										<?php endif; ?>	
										</li>							
									<?php endif; ?>
								<?php endforeach; endif; unset($_from); ?>								
						<?php else: ?>
							
							<li>
								<span class="no-data">There are no published articles</span>
							</li>
							
						<?php endif; ?>
						</ul>
											
					</div>
				</div>
			</aside>  	
		</div>
		
	</div>
</div>