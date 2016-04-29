<?php /* Smarty version 2.6.19, created on 2015-09-03 16:04:51
         compiled from Contrib/aosearch.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strlen', 'Contrib/aosearch.phtml', 27, false),array('modifier', 'stripslashes', 'Contrib/aosearch.phtml', 27, false),array('modifier', 'truncate', 'Contrib/aosearch.phtml', 27, false),array('modifier', 'utf8_decode', 'Contrib/aosearch.phtml', 59, false),array('modifier', 'count', 'Contrib/aosearch.phtml', 64, false),)), $this); ?>
<?php echo '
<script type="text/javascript">
var cur_date=0,js_date=0,diff_date=0;
cur_date='; ?>
<?php echo time(); ?>
<?php echo ';
js_date=(new Date().getTime())/ 1000;
diff_date=Math.floor(js_date-cur_date);
$("#menu_aosearch").addClass("active");
</script>
'; ?>

<div class="container">
	<div class="row-fluid">
		<h1>All offers</h1>
		<p> Respond to the offers below with your rates before the end of the countdown.</p>
	</div>

	<div class="row-fluid">
		<div class="span12"> 		
			<?php if ($this->_tpl_vars['recruitmentDetails'] | @ count > 0): ?>
			<section id="recruitment">
				<div class="mod">					
					<?php $_from = $this->_tpl_vars['recruitmentDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['recruitmentDetails'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['recruitmentDetails']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['recruitment']):
        $this->_foreach['recruitmentDetails']['iteration']++;
?>
					<div class="row-fluid">
						<div class="span9">
							<span data-placement="right" rel="tooltip" data-original-title="Garanteed long-term Mission" class="label label-test-mission"><i class="icon-star icon-white"></i>  Staffing</span>
							<h3>
								<?php if ($this->_tpl_vars['recruitment']['recruitment_article_id']): ?>
									<a href="/recruitment/participation-challenge?recruitment_id=<?php echo $this->_tpl_vars['recruitment']['deliveryid']; ?>
&article_id=<?php echo $this->_tpl_vars['recruitment']['recruitment_article_id']; ?>
#sign-contract" <?php if (((is_array($_tmp=$this->_tpl_vars['recruitment']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 55): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['recruitment']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['recruitment']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 55, "...", true) : smarty_modifier_truncate($_tmp, 55, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</a>
								<?php else: ?>
									<a href="/recruitment/participation-challenge?recruitment_id=<?php echo $this->_tpl_vars['recruitment']['deliveryid']; ?>
" <?php if (((is_array($_tmp=$this->_tpl_vars['recruitment']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 55): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['recruitment']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['recruitment']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 55, "...", true) : smarty_modifier_truncate($_tmp, 55, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</a>
								<?php endif; ?>	
							</h3>
							<div class="muted"><?php echo $this->_tpl_vars['recruitment']['product_name']; ?>
 for <?php echo $this->_tpl_vars['recruitment']['client_name']; ?>
 <?php echo $this->_tpl_vars['recruitment']['mission_volume']; ?>
 <?php echo $this->_tpl_vars['recruitment']['product_type_name']; ?>
 in <?php echo $this->_tpl_vars['recruitment']['delivery_time_frame']; ?>
 <?php echo $this->_tpl_vars['recruitment']['delivery_period']; ?>
s.<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['recruitment']['editorial_chief_review'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 80, "...", true) : smarty_modifier_truncate($_tmp, 80, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</div>
						</div>
						<div class="span3">
							<h3 style="text-align: center">Up to <?php echo $this->_tpl_vars['recruitment']['turnover']; ?>
 &<?php echo $this->_tpl_vars['recruitment']['currency']; ?>
;*</h3>
							<a class="btn btn-primary btn-block" href="/recruitment/participation-challenge?recruitment_id=<?php echo $this->_tpl_vars['recruitment']['deliveryid']; ?>
">Participate</a>
						</div>
					</div>	
					<?php endforeach; endif; unset($_from); ?>
				</div>
			</section>
			<?php endif; ?>
			<section id="classified">
				<!-- List of classifieds that contributor might like -->
				<div class="navbar" style="padding-bottom: 0">
					<div class="navbar-inner filter-container">
						<ul class="nav">
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">
									<?php if ($_GET['search_category']): ?>
										<?php echo $this->_tpl_vars['categoryFilter']; ?>

									<?php else: ?>
										All categories
									<?php endif; ?>	
									<b class="caret"></b>
								</a>
								<ul class="dropdown-menu">
									<?php if ($_GET['search_category']): ?>
										<li><a class="active" href="/contrib/aosearch?search_category=&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
" class="active"><?php echo $this->_tpl_vars['categoryFilter']; ?>
</a></li>
									<?php else: ?>
										 <li><a href="#" class="">All categories</a></li>
									<?php endif; ?>	
								  <li class="divider"></li>
									<?php if (count($this->_tpl_vars['ep_categories_list']) > 0): ?>
										<?php $_from = $this->_tpl_vars['ep_categories_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['category']):
?>
											<?php if (in_array ( $this->_tpl_vars['key'] , $this->_tpl_vars['filter_category'] ) && $_GET['search_category'] != $this->_tpl_vars['key']): ?>
												 <li><a href="/contrib/aosearch?search_category=<?php echo $this->_tpl_vars['key']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
"><?php echo $this->_tpl_vars['category']; ?>
</a></li>
											<?php endif; ?>
										<?php endforeach; endif; unset($_from); ?>
									<?php endif; ?>
								  
								</ul>
							 </li>
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">
									<?php if ($_GET['search_language']): ?>
										<?php echo $this->_tpl_vars['languageFilter']; ?>

									<?php else: ?>
										All languages
									<?php endif; ?>	
									 <b class="caret"></b>
								</a>
								<ul class="dropdown-menu">
									<?php if ($_GET['search_language']): ?>
										<li><a  class="active" href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
" class="active"><?php echo $this->_tpl_vars['languageFilter']; ?>

											
										</a>
										</li>
									<?php else: ?>
										 <li><a href="#" class="">All languages</a></li>
									<?php endif; ?>	
								  
								  <li class="divider"></li>
								  <?php if (count($this->_tpl_vars['ep_languages_list']) > 0): ?>
										<?php $_from = $this->_tpl_vars['ep_languages_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['language']):
?>
											<?php if (in_array ( $this->_tpl_vars['key'] , $this->_tpl_vars['filter_language'] ) && $_GET['search_language'] != $this->_tpl_vars['key']): ?>
												 <li><a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $this->_tpl_vars['key']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
"><?php echo $this->_tpl_vars['language']; ?>
</a></li>
											<?php endif; ?>
										<?php endforeach; endif; unset($_from); ?>
									<?php endif; ?>
								</ul>
							</li>
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#">
									<?php if ($_GET['search_ao_type']): ?>
										<?php echo $this->_tpl_vars['aoTypeFilter']; ?>

									<?php else: ?>
										All types
									<?php endif; ?>	
									<b class="caret"></b>
								</a>
								<ul class="dropdown-menu">
								  <?php if ($_GET['search_ao_type']): ?>
										<li><a  class="active" href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=" class="active"><?php echo $this->_tpl_vars['aoTypeFilter']; ?>

											
										</a>
										</li>
									<?php else: ?>
										 <li><a href="#" class="">All types</a></li>
									<?php endif; ?>
								  <li class="divider"></li>
									<?php if (count($this->_tpl_vars['ep_languages_list']) > 0): ?>
										<?php $_from = $this->_tpl_vars['ep_aotype_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['ao_types']):
?>
											<?php if ($_GET['search_ao_type'] != $this->_tpl_vars['key']): ?>
												 <li><a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $this->_tpl_vars['key']; ?>
"><?php echo $this->_tpl_vars['ao_types']; ?>
</a></li>
											<?php endif; ?>
										<?php endforeach; endif; unset($_from); ?>
									<?php endif; ?>
								  
								</ul>
							</li>
							 <li class="divider-vertical"></li>
						</ul>
						<ul class="nav  pull-right">                     
							<li>
								<form class="navbar-form" method="GET">
									<input type="text" <?php if (! $this->_tpl_vars['searchgetText']): ?> placeholder="Keyword"<?php endif; ?> class="input-medium" name="search_article" value="<?php echo $this->_tpl_vars['searchgetText']; ?>
">
									<button class="btn btn-small" type="submit">OK</button>
								</form>
							</li>
						</ul>
					</div>
				</div>
				<div class="mod">
					<!-- about sorting  add the following tags <i class="icon-circle-arrow-down"></i> only if a user has clicked on a title head !!!
					do not display the icon by default-->
					<table class="table table-hover">
					<thead>
						<tr>
							<th><a href="#orderByType">Type</a></i></th>
							<th>
								<?php if ($_GET['orderByTitle'] == 'desc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByTitle=asc">Title</a>
									<i class="icon-circle-arrow-down"></i>
								<?php elseif ($_GET['orderByTitle'] == 'asc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByTitle=desc">Title</a>
									<i class="icon-circle-arrow-up"></i>
								<?php else: ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByTitle=asc">Title</a>	
								<?php endif; ?>	
							</th>
							<th class="pc">
								<?php if ($_GET['orderByLang'] == 'desc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByLang=asc">Languages</a>
									<i class="icon-circle-arrow-down"></i>
								<?php elseif ($_GET['orderByLang'] == 'asc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByLang=desc">Languages</a>
									<i class="icon-circle-arrow-up"></i>
								<?php else: ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByLang=asc">Languages</a>	
								<?php endif; ?>								
							</th>
							<th class="pc">
								<?php if ($_GET['orderBytime'] == 'desc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderBytime=asc">End of quote sending</a>
									<i class="icon-circle-arrow-down"></i>
								<?php elseif ($_GET['orderBytime'] == 'asc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderBytime=desc">End of quote sending</a>
									<i class="icon-circle-arrow-up"></i>
								<?php else: ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderBytime=asc">End of quote sending</a>	
								<?php endif; ?>
								
							</th>
							<th class="pc">
								<?php if ($_GET['orderByAttendee'] == 'desc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByAttendee=asc">Contributors</a>
									<i class="icon-circle-arrow-down"></i>
								<?php elseif ($_GET['orderByAttendee'] == 'asc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByAttendee=desc">Contributors</a>
									<i class="icon-circle-arrow-up"></i>
								<?php else: ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByAttendee=asc">Contributors</a>	
								<?php endif; ?>
								
							</th>
							<th class="pc">
								
								<?php if ($_GET['orderByQuotePrice'] == 'desc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByQuotePrice=asc">Last quote</a>
									<i class="icon-circle-arrow-down"></i>
								<?php elseif ($_GET['orderByQuotePrice'] == 'asc'): ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByQuotePrice=desc">Last quote</a>
									<i class="icon-circle-arrow-up"></i>
								<?php else: ?>
									<a href="/contrib/aosearch?search_category=<?php echo $_GET['search_category']; ?>
&search_article=<?php echo ((is_array($_tmp=$_GET['search_article'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
&search_type=<?php echo $_GET['search_type']; ?>
&search_language=<?php echo $_GET['search_language']; ?>
&search_ao_type=<?php echo $_GET['search_ao_type']; ?>
&orderByQuotePrice=asc">Last quote</a>	
								<?php endif; ?>						
								
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if ($this->_tpl_vars['articles'] | @ count > 0): ?>
							<?php $_from = $this->_tpl_vars['articles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['articledetails'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['articledetails']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['articledetails']['iteration']++;
?> 								
								<tr>
									<?php if ($this->_tpl_vars['article']['ao_type'] == 'correction'): ?>
										<td>
											<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
												<span class="label label-test-mission" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Proofreading Test Mission</span>
											<?php else: ?>
												<span class="label label-correction" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Proofreading Mission</span>
											<?php endif; ?>
											<?php if ($this->_tpl_vars['article']['view_to'] == 'sc'): ?>
												<span data-original-title="Available to Seniors" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place." rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
											<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc'): ?>
												<span data-original-title="Available to Juniors" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
											<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc0'): ?>
												<span data-original-title="Available to Beginners" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
											<?php endif; ?>
										</td>
										<td class="title"><a href="/contrib/article-details?misson_type=correction&mission_identifier=<?php echo $this->_tpl_vars['article']['articleid']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 55): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 55, "...", true) : smarty_modifier_truncate($_tmp, 55, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
  <?php echo $this->_tpl_vars['article']['picon']; ?>
 </span>
									<?php elseif ($this->_tpl_vars['article']['ao_type'] == 'poll_premium'): ?>
												<td><span class="label label-quote-premium" data-original-title="This offer may become a Premium mission" rel="tooltip" data-placement="right">Premium Quote</span>
												<?php if ($this->_tpl_vars['article']['contributors'] == '0'): ?>
													<span data-original-title="Available to Seniors" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place." rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
												<?php elseif ($this->_tpl_vars['article']['contributors'] == '1'): ?>
													<span data-original-title="Available to Juniors" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
												<?php elseif ($this->_tpl_vars['article']['contributors'] == '3'): ?>
													<span data-original-title="Available to Beginners" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
												<?php endif; ?>
												
												</td>
												<td class="title"><a href="/contrib/article-details?misson_type=poll_premium&mission_identifier=<?php echo $this->_tpl_vars['article']['pollId']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 55): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 55, "...", true) : smarty_modifier_truncate($_tmp, 55, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span>
									<?php elseif ($this->_tpl_vars['article']['ao_type'] == 'poll_nopremium'): ?>
												<td><span class="label label-quote" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right">Freedom Quote</span>
												<?php if ($this->_tpl_vars['article']['contributors'] == '0'): ?>
													<span data-original-title="Available to Seniors" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place." rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
												<?php elseif ($this->_tpl_vars['article']['contributors'] == '1'): ?>
													<span data-original-title="Available to Juniors" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
												<?php elseif ($this->_tpl_vars['article']['contributors'] == '3'): ?>
													<span data-original-title="Available to Beginners" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
												<?php endif; ?>
												</td>
												<td class="title"><a href="/contrib/article-details?misson_type=poll_nopremium&mission_identifier=<?php echo $this->_tpl_vars['article']['pollId']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 55): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 55, "...", true) : smarty_modifier_truncate($_tmp, 55, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
</span>		
									<?php elseif ($this->_tpl_vars['article']['premium_option']): ?>
											<td>
											<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
												<span class="label label-test-mission" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right"><i class="icon-star icon-white"></i>  Staffing</span>
											<?php else: ?>
												<span class="label label-premium" data-original-title="Mission guaranteed by Edit-place" rel="tooltip" data-placement="right">Premium Mission</span>
											<?php endif; ?>
											<?php if ($this->_tpl_vars['article']['view_to'] == 'sc'): ?>
												<span data-original-title="Available to Seniors" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place." rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
											<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc'): ?>
												<span data-original-title="Available to Juniors" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
											<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc0'): ?>
												<span data-original-title="Available to Beginners" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
											<?php endif; ?>
											</td>
											<td class="title"><a href="/contrib/article-details?misson_type=premium&mission_identifier=<?php echo $this->_tpl_vars['article']['articleid']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 55): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 55, "...", true) : smarty_modifier_truncate($_tmp, 55, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
 <?php echo $this->_tpl_vars['article']['picon']; ?>
 <?php echo $this->_tpl_vars['article']['qicon']; ?>
</span>
									<?php elseif (! $this->_tpl_vars['article']['premium_option']): ?>
										<td>
											<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
												<span class="label label-test-mission" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right"><i class="icon-star icon-white"></i>  Staffing</span>
											<?php else: ?>
												<span class="label label-quote" data-original-title="You will work directly with the client" rel="tooltip" data-placement="right">Freedom Mission</span>
											<?php endif; ?>
											<?php if ($this->_tpl_vars['article']['view_to'] == 'sc'): ?>
												<span data-original-title="Available to Seniors" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place." rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>S</span>
											<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc'): ?>
												<span data-original-title="Available to Juniors" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>J</span>
											<?php elseif ($this->_tpl_vars['article']['view_to'] == 'jc0'): ?>
												<span data-original-title="Available to Beginners" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>B</span>	
											<?php endif; ?>
										</td>
										<td class="title"><a href="/contrib/article-details?misson_type=nopremium&mission_identifier=<?php echo $this->_tpl_vars['article']['articleid']; ?>
" role="button" data-toggle="modal" data-target="#viewOffer-ajax"><span <?php if (((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : smarty_modifier_strlen($_tmp)) > 55): ?> rel="tooltip" align="top" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
"<?php endif; ?>><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 55, "...", true) : smarty_modifier_truncate($_tmp, 55, "...", true)))) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>
 <?php echo $this->_tpl_vars['article']['picon']; ?>
 <?php echo $this->_tpl_vars['article']['qicon']; ?>
</span>
									<?php endif; ?>									
									<p class="muted"><?php echo $this->_tpl_vars['article']['category']; ?>
  &bull;  Call to writers  &bull;  <?php echo $this->_tpl_vars['article']['num_min']; ?>
 - <?php echo $this->_tpl_vars['article']['num_max']; ?>
 words / article</p></a></td>
									<td class="pc"><img src="/FO/images/shim.gif" data-placement="left" rel="tooltip" data-original-title="<?php echo $this->_tpl_vars['article']['language_name']; ?>
" class="flag flag-<?php echo $this->_tpl_vars['article']['language']; ?>
"></td>
									<td class="countdown pc">
										<?php if ($this->_tpl_vars['article']['ao_type'] == 'correction'): ?>
											<span id="time_<?php echo $this->_tpl_vars['article']['articleid']; ?>
-correction_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
">
												<?php if ($this->_tpl_vars['article']['participation_expires']): ?>
													<span id="text_<?php echo $this->_tpl_vars['article']['articleid']; ?>
-correction_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
" ></span>
												<?php else: ?>
													<span id="text_<?php echo $this->_tpl_vars['article']['articleid']; ?>
-correction_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
"></span>	
												<?php endif; ?>	
											</span>	
										<?php else: ?>
										<span id="time_<?php echo $this->_tpl_vars['article']['articleid']; ?>
_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
">
											<?php if ($this->_tpl_vars['article']['participation_expires']): ?>
												<span id="text_<?php echo $this->_tpl_vars['article']['articleid']; ?>
_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
" ></span>
											<?php else: ?>
												<span id="text_<?php echo $this->_tpl_vars['article']['articleid']; ?>
_<?php echo $this->_tpl_vars['article']['timestamp']; ?>
"></span>	
											<?php endif; ?>	
										</span>	
										<?php endif; ?>	
											
									</td>
									<td class="countdown pc"><em><?php echo $this->_tpl_vars['article']['participants']; ?>
</em></td>
									<td class="countdown pc">
										<em>
										<?php if ($this->_tpl_vars['article']['pricedisplay'] == 'yes'): ?>
											<?php if ($this->_tpl_vars['article']['latestPrice']): ?>
												<?php echo $this->_tpl_vars['article']['latestPrice']; ?>

											<?php else: ?>
												0 
											<?php endif; ?>
											<?php if ($this->_tpl_vars['article']['currency'] == 'euro'): ?>
												&euro;
											<?php else: ?>
												&pound;
											<?php endif; ?>
										<?php else: ?>
											/
										<?php endif; ?>		
										</em>
									</td>
								</tr>								
							<?php endforeach; endif; unset($_from); ?>
						<?php else: ?>
							<tr>
								<td colspan="6">
									<span class="no-data">No result</span>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
					</table>
					<!---Pagination start-->
					<div class="pagination pull-right">
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/pagination.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					</div>	
					<!---Pagination END-->
				</div>			
				<!-- Block to display coming soon articles -->
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/upcoming_ao.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>				
				<!-- Block to finished articles -->
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/terminated_ao.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				
				
				
			</section>
		</div>
	</div>
</div>
<!-- ajax use end --> 