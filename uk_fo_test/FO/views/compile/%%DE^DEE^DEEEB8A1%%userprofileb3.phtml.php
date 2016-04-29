<?php /* Smarty version 2.6.19, created on 2014-07-01 12:24:38
         compiled from Client/userprofileb3.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'ucfirst', 'Client/userprofileb3.phtml', 33, false),array('modifier', 'stripslashes', 'Client/userprofileb3.phtml', 41, false),array('modifier', 'count', 'Client/userprofileb3.phtml', 94, false),array('modifier', 'utf8_decode', 'Client/userprofileb3.phtml', 139, false),)), $this); ?>
<?php echo '
<link href="/FO/css/common/custom.css" rel="stylesheet"> 
<link href="/FO/css/common/ep.css" rel="stylesheet"> 
<script type="text/javascript" src="/FO/script/common/jquery.min.js"></script>
<script type="text/javascript" src="/FO/script/common/bootstrap.js"></script>
<script language="javascript">
	function closemodal()
	{
		window.parent.$("#viewContribProfile").removeClass("in");
		window.parent.$("#viewContribProfile").hide();
		window.parent.$("#fadeblock").removeClass("modal-backdrop fade in");
		window.parent.$("#fadeblock").hide();
		window.parent.$(\'body\').removeClass("modal-open");
	}
	</script>
<style>
.container{padding:0px;}

</style>
'; ?>

<div class="modal-header">
	<button type="button" class="close" aria-hidden="true" onClick="closemodal();" data-dismiss="modal">&times;</button><!--onClick="location.reload();"-->
	<h3 id="myModalLabel">Writer's profile</h3>
</div>
<div class="modal-body">
<section id="status">
	<div class="row-fluid">
		<!-- col 1 -->
		<div class="profilehead-mod">
			<div class="span2">
				<div class="editor-container">
					<a class="imgframe-large" href="#">
						<img src="<?php echo $this->_tpl_vars['contribprofile'][0]['profilepic']; ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['contribprofile'][0]['first_name'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['contribprofile'][0]['last_name'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
">
					</a>
				</div>
			</div>
			<div class="span9 profile-name" style="">
				<h3><?php echo $this->_tpl_vars['contribprofile'][0]['name']; ?>
</h3>
				<p class="" style=""><?php echo $this->_tpl_vars['contribprofile'][0]['age']; ?>
 <span class="muted"> &bull; </span>  <?php echo $this->_tpl_vars['contribprofile'][0]['catstr']; ?>
  <span class="muted"> &bull; </span>  <?php echo $this->_tpl_vars['contribprofile'][0]['langstr']; ?>
</p>
				<blockquote>
					<i class="icon-leaf"></i> <?php echo ((is_array($_tmp=$this->_tpl_vars['contribprofile'][0]['self_details'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>

				</blockquote>
			</div>
			<!--<div class="span3 stat contact-info" style="border:none;padding-left:0px;">
				<h4><i class="icon-user"></i> Coordonn&eacute;es</h4>
				<address>
					+<?php echo $this->_tpl_vars['contribprofile'][0]['phone_number']; ?>
<br>
					<a href="mailto:<?php echo $this->_tpl_vars['contribprofile'][0]['email']; ?>
"><?php echo $this->_tpl_vars['contribprofile'][0]['email']; ?>
</a>
				</address>
				<a href="/client/compose-mail?clientid=<?php echo $this->_tpl_vars['contribprofile'][0]['identifier']; ?>
" class="btn btn-small">contacter <?php echo ((is_array($_tmp=$this->_tpl_vars['contribprofile'][0]['first_name'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
</a>
			</div>-->
		</div>
	</div>
</section>
<!-- end, contributor status --> 

<div class="row-fluid"> 
	<div class="span8">
		<section id="skills">
			<div class="mod">
				<h4>Languages</h4>
				<?php $this->assign('language', $this->_tpl_vars['contribprofile'][0]['language']); ?>
				<strong><?php echo $this->_tpl_vars['language_array'][$this->_tpl_vars['language']]; ?>
</strong> (mother tongue)
				<?php $_from = $this->_tpl_vars['langpercent']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang'] => $this->_tpl_vars['more']):
?>
					<div class="skillstat row-fluid">	
						<div class="span6">
							<p><strong><?php echo $this->_tpl_vars['language_array'][$this->_tpl_vars['lang']]; ?>
</strong>  <?php echo $this->_tpl_vars['more']; ?>
%</p>
							<div class="progress">
								<div class="bar" style="width: <?php echo $this->_tpl_vars['more']; ?>
%"></div>
							</div>
							<span class="pull-left legend muted">Beginner</span> <span class="pull-right legend muted">Bilingual</span>
						</div>
					</div>
				<?php endforeach; endif; unset($_from); ?>	
			</div>

			<div class="mod">
				<h4>Areas of Expertise</h4>
				<?php $_from = $this->_tpl_vars['contribprofile'][0]['cats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat']):
?>
					<?php if ($this->_tpl_vars['catpercent'][$this->_tpl_vars['cat']] != ""): ?>
					<div class="skillstat row-fluid">
						<div class="span6">
							<p><strong data-original-title="Seo / marketing internet" rel="tooltip"><?php echo $this->_tpl_vars['category_array'][$this->_tpl_vars['cat']]; ?>
</strong>  <?php echo $this->_tpl_vars['catpercent'][$this->_tpl_vars['cat']]; ?>
%</p>
							<div class="progress">
							<div class="bar" style="width: <?php echo $this->_tpl_vars['catpercent'][$this->_tpl_vars['cat']]; ?>
%"></div>
							</div>
							<span class="pull-left legend muted">Beginner</span> <span class="pull-right legend muted">Expert</span>
						</div>
					</div>
					<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?>	
			</div>
			
			<?php if (count($this->_tpl_vars['exp_details']) > 0): ?>
			<div class="mod">
				<h4>Professional Experience</h4>
				<dl>
					<?php $_from = $this->_tpl_vars['exp_details']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['exp']):
?>
						<dt><?php echo $this->_tpl_vars['exp']['title']; ?>
</dt>
						<dd class="company"><?php echo $this->_tpl_vars['exp']['institute']; ?>
</dd>
						<dd class="muted">
							Type of contract : <?php echo $this->_tpl_vars['exp']['contract']; ?>

						</dd>
						<dd class="muted">
							<time> <?php echo ((is_array($_tmp=$this->_tpl_vars['exp']['from_month'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
 <?php echo $this->_tpl_vars['exp']['from_year']; ?>
</time> - <time><?php if ($this->_tpl_vars['exp']['to_year'] != '0'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['exp']['to_month'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
 <?php echo $this->_tpl_vars['exp']['to_year']; ?>
<?php else: ?>Current<?php endif; ?></time>
						</dd>
					<?php endforeach; endif; unset($_from); ?>
				</dl>
			</div>
			<?php endif; ?>
			
			<?php if (count($this->_tpl_vars['education_details']) > 0): ?>
			<div class="mod">
				<h4>Training</h4>
				<dl>
					<?php $_from = $this->_tpl_vars['education_details']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['edu']):
?>
						<dt><?php echo $this->_tpl_vars['edu']['title']; ?>
</dt>
						<dd class="company"><?php echo $this->_tpl_vars['edu']['institute']; ?>
</dd>
						<dd class="muted">
							<time> <?php echo ((is_array($_tmp=$this->_tpl_vars['edu']['from_month'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
 <?php echo $this->_tpl_vars['edu']['from_year']; ?>
</time> - <time><?php if ($this->_tpl_vars['edu']['to_year'] != '0'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['edu']['to_month'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
 <?php echo $this->_tpl_vars['edu']['to_year']; ?>
<?php else: ?>Current<?php endif; ?></time>
						</dd>
					<?php endforeach; endif; unset($_from); ?>
				</dl>
			</div>
			<?php endif; ?>
		</section>
	</div>

	
	<?php if (count($this->_tpl_vars['contribprofile'][0]['clientlogo']) > 0): ?>
	<div class="span4">
		<!--  right column  -->
		<aside>
			<div class="aside-bg">
				<div class="aside-block" id="we-trust">
					<h4>Publications</h4>
					<ul class="unstyled">
						<?php $_from = $this->_tpl_vars['contribprofile'][0]['clientlogo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['keylogo'] => $this->_tpl_vars['clogo']):
?>
							<li><img src="<?php echo $this->_tpl_vars['clogo']; ?>
" rel="tooltip" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['keylogo'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
" data-placement="left"></li>
						<?php endforeach; endif; unset($_from); ?>
					</ul>
				</div>
			</div>
		</aside>  
	</div>
	<?php endif; ?>
	
</div>


<!--<a class="pull-right btn btn-small disabled anchor-top scroll" id="scroll"><i class="icon-arrow-up"></i></a> -->
       
<?php echo '
<script>
	$("#scroll").click(function(event){		
		event.preventDefault();
		$(\'html,body\').animate({scrollTop:$(this.hash).offset().top}, 500);
		return false;		
	}); 
	$("[rel=tooltip]").tooltip();
	$("[rel=popover]").popover();
</script>
'; ?>

</div>