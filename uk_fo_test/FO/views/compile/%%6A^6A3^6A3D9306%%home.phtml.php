<?php /* Smarty version 2.6.19, created on 2015-06-30 14:11:18
         compiled from Contrib/home.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'zero_cut', 'Contrib/home.phtml', 55, false),array('modifier', 'count', 'Contrib/home.phtml', 76, false),array('modifier', 'strlen', 'Contrib/home.phtml', 94, false),array('modifier', 'stripslashes', 'Contrib/home.phtml', 94, false),array('modifier', 'truncate', 'Contrib/home.phtml', 94, false),)), $this); ?>
<?php echo '
<script type="text/javascript">
var cur_date=0,js_date=0,diff_date=0;
cur_date='; ?>
<?php echo time(); ?>
<?php echo ';
js_date=(new Date().getTime())/ 1000;
diff_date=Math.floor(js_date-cur_date);
$("#menu_home").addClass("active");
</script>
'; ?>

<div class="container">
	<!-- start, status -->
		<div class="row-fluid">		
			<div id="getstarted" class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>GET OFF TO A GOOD START!</h4>
				<p class="pull-right"><a href="#help" class="btn scroll">How does it work ?</a></p> 
				<ul class="unstyled">
					<li><i class="icon icon-ok"></i> Only 1 writer selected per mission</li>
					<li><i class="icon icon-ok"></i> Only start writing once you have been selected by Edit-place or the client</li>
					<li><i class="icon icon-ok"></i> Follow the editorial guidelines/brief and the deadlines</li>
			   </ul>
			</div>		 
			<!-- start, get contributor status -->
			<section id="status">
				<div class="row-fluid">
					<div class="profilehead-mod">
						<div class="span5 profile-name">
							<a href="/contrib/private-profile" class="pull-left imgframe">
								<img src="<?php echo $this->_tpl_vars['contrib_home_picture']; ?>
" title="<?php echo $this->_tpl_vars['client_email']; ?>
" class="media-object">
							</a>
							<?php if ($this->_tpl_vars['profileType'] == 'senior'): ?>
								<span data-original-title="Senior" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>Senior</span>
							<?php elseif ($this->_tpl_vars['profileType'] == 'junior'): ?>
								<span data-original-title="Junior" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>Junior</span>
							<?php elseif ($this->_tpl_vars['profileType'] == 'sub-junior'): ?>
								<span data-original-title="Beginner" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>Beginner</span>	
							<?php endif; ?>
							<h3><?php echo $this->_tpl_vars['client_email']; ?>
</h3>
						</div>
						
						<div class="span3 stat">
							<div class="progress progress-success">
							<div class="bar" style="width: <?php echo $this->_tpl_vars['profile_percentage']; ?>
%"></div>
							</div>
							Your profile is <strong><?php echo $this->_tpl_vars['profile_percentage']; ?>
%</strong> complete
							<div class="btn-link"><a href="/contrib/modify-profile">Update</a></div>
						</div>

						<div class="span2 stat">
							<p>Ongoing contributions</p>
							<p class="num-large"><a href="/contrib/ongoing"><?php echo $this->_tpl_vars['participation_Count']; ?>
</a></p>
						</div>
						
						<div class="span2 stat"><p>Total royalties</p><p class="num-large"><a href="/contrib/royalties">
						<?php echo ((is_array($_tmp=$this->_tpl_vars['userRoyalty'][0]['royalty'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['userRoyalty'][0]['currency']; ?>
;
						<?php if ($this->_tpl_vars['userRoyalty'][0]['royalty'] && $this->_tpl_vars['userRoyalty'][1]['royalty']): ?> , <?php endif; ?>
						<?php if ($this->_tpl_vars['userRoyalty'][1]['royalty']): ?>
								<?php echo ((is_array($_tmp=$this->_tpl_vars['userRoyalty'][1]['royalty'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['userRoyalty'][1]['currency']; ?>
;
						<?php endif; ?>
						</a></p></div>
					</div>
				</div>

			</section>
			  <!-- end, contributor status --> 
		 
			<div class="row-fluid"> 
				<div class="span8">
						
					<section id="quick-classified">
						<div class="mod">
							<span class="pull-right icon-waiting-quote" data-original-title="Don't waste any time! Make an offer quickly" rel="tooltip"></span>
							<h4>RESPOND TO THESE NEW OFFERS</h4>
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
												<?php if ($this->_tpl_vars['offer']['view_to'] == 'sc'): ?>
													<span data-original-title="Available to Seniors" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
												<?php elseif ($this->_tpl_vars['offer']['view_to'] == 'jc'): ?>
													<span data-original-title="Available to Junior" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
												<?php elseif ($this->_tpl_vars['offer']['view_to'] == 'jc0'): ?>
													<span data-original-title="Available to Beginner" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
												<?php endif; ?>
												</td>
												<td class="title"><a href="/contrib/article-details?misson_type=correction&mission_identifier=<?php echo $this->_tpl_vars['offer']['articleid']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span> <?php echo $this->_tpl_vars['offer']['picon']; ?>
 </a></td>
											
											<?php elseif ($this->_tpl_vars['offer']['ao_type'] == 'poll_premium'): ?>
												<td><span class="label label-quote-premium" data-original-title="This offer may become a Premium mission" rel="tooltip" data-placement="right">Premium quote</span>
												<?php if ($this->_tpl_vars['offer']['contributors'] == '0'): ?>
													<span data-original-title="Senior level" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
												<?php elseif ($this->_tpl_vars['offer']['contributors'] == '1'): ?>
													<span data-original-title="Available to Junior" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
												<?php elseif ($this->_tpl_vars['offer']['contributors'] == '3'): ?>
													<span data-original-title="Available to Beginner" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
												<?php endif; ?>
												</td>
												<td class="title"><a href="/contrib/article-details?misson_type=poll_premium&mission_identifier=<?php echo $this->_tpl_vars['offer']['pollId']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 75): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 75, "...", true) : smarty_modifier_truncate($_tmp, 75, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span></a></td>
											<?php elseif ($this->_tpl_vars['offer']['ao_type'] == 'poll_nopremium'): ?>
												<td><span class="label label-quote" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right">Freedom Quote</span>
												<?php if ($this->_tpl_vars['offer']['contributors'] == '0'): ?>
													<span data-original-title="Senior level" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
												<?php elseif ($this->_tpl_vars['offer']['contributors'] == '1'): ?>
													<span data-original-title="Available to Junior" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
												<?php elseif ($this->_tpl_vars['offer']['contributors'] == '3'): ?>
													<span data-original-title="Available to Beginner" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
												<?php endif; ?>
												</td>
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

													
												<?php if ($this->_tpl_vars['offer']['view_to'] == 'sc'): ?>
													<span data-original-title="Senior level" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
												<?php elseif ($this->_tpl_vars['offer']['view_to'] == 'jc'): ?>
													<span data-original-title="Available to Junior" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
												<?php elseif ($this->_tpl_vars['offer']['view_to'] == 'jc0'): ?>
													<span data-original-title="Available to Beginner" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
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
													<?php if ($this->_tpl_vars['offer']['view_to'] == 'sc'): ?>
														<span data-original-title="Available to Senior" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
													<?php elseif ($this->_tpl_vars['offer']['view_to'] == 'jc'): ?>
														<span data-original-title="Available to Junior" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
													<?php elseif ($this->_tpl_vars['offer']['view_to'] == 'jc0'): ?>
														<span data-original-title="Available to Beginner" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
													<?php endif; ?>
												</td>
												<td class="title"><a href="/contrib/article-details?misson_type=nopremium&mission_identifier=<?php echo $this->_tpl_vars['offer']['articleid']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 70): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['offer']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 70, "...", true) : smarty_modifier_truncate($_tmp, 70, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
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
					<aside>				  
						<div class="aside-bg" style="padding:0px">
							<!--<div class="aside-block" id="browse-by-cat">
								<h4>Recherche par cat&eacute;gorie</h4>
								<ul class="nav nav-tabs nav-stacked">
									<?php $_from = $this->_tpl_vars['ep_categories_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['category']):
?>
										<li><a href="/contrib/aosearch?search_category=<?php echo $this->_tpl_vars['key']; ?>
">
										<?php if ($this->_tpl_vars['ep_category_articles_count'][$this->_tpl_vars['key']] > 0): ?>
											<span class="badge badge-warning pull-right"><?php echo $this->_tpl_vars['ep_category_articles_count'][$this->_tpl_vars['key']]; ?>
</span>
										<?php else: ?>		
											<span class="badge pull-right"><?php echo $this->_tpl_vars['ep_category_articles_count'][$this->_tpl_vars['key']]; ?>
</span>
										<?php endif; ?>	
										<?php echo $this->_tpl_vars['category']; ?>
</a></li>									
									<?php endforeach; endif; unset($_from); ?>	
								</ul>	
							</div>-->
							<div id="ao-next" class="aside-block">
								<div class="pull-center">
									<h4 class="date"><span>Tomorrow,</span> <?php echo $this->_tpl_vars['publish_date']; ?>
 <i class="icon-calendar icon-white"></i></h4>
									<div class="main">
										<span class="nb"><a href="/contrib/aosearch#soon"><?php echo $this->_tpl_vars['upcoming_count']; ?>
</a></span><a href="/contrib/aosearch#soon"> NEW OFFERS OPEN TO CONTRIBUTIONS</a>
									</div>
								</div>
								You can already : 
								<ul>
								  <li>Read about these offers</li>
								  <li>Receive an alert once contributions are open</li>
								</ul>
								<div class="pull-center"><a href="/contrib/aosearch#soon" class="btn btn-small">See the next offers</a></div>
							</div>
						</div>				  
					</aside>
				</div>
			</div>
	 </div>
</div>		