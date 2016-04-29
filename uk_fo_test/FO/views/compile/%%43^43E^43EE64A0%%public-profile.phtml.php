<?php /* Smarty version 2.6.19, created on 2016-03-21 12:05:23
         compiled from Contrib/public-profile.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Contrib/public-profile.phtml', 51, false),)), $this); ?>
<section id="status">
	<div class="row-fluid">
		<div class="profilehead-mod">
			<div class="span2">
				<div class="editor-container">
					<a class="imgframe-large" href="#"><img src="<?php echo $this->_tpl_vars['profile_image']; ?>
" title="<?php echo $this->_tpl_vars['client_email']; ?>
"></a>
				</div>
			</div>
			<div class="span7 profile-name">
				<h3><?php echo $this->_tpl_vars['ep_contrib_profile_fname']; ?>
 <?php echo $this->_tpl_vars['ep_contrib_profile_lname']; ?>
</h3>
				<p class="" style=""><?php echo $this->_tpl_vars['ep_contrib_age']; ?>
 ans  <span class="muted">&bull;</span>  <?php echo $this->_tpl_vars['allCategories']; ?>
  <span class="muted">&bull;</span> <?php echo $this->_tpl_vars['ep_language']; ?>
 , <?php echo $this->_tpl_vars['allLanguages']; ?>
</p>
				<blockquote>
						<i class="icon-leaf"></i> <?php echo $this->_tpl_vars['ep_contrib_profile_self_details']; ?>

				</blockquote>
			</div>
			<div class="span3 stat contact-info">
			<!-- 
			* display the contact details only if :
			- the contributor is checking out his own public profile
			-  the client has already worked with this contributor
			* to hide this block, add the class "hide" : <div class="span3 stat contact-info hide">  
			-->
				<h4><i class="icon-user"></i> Contact Details</h4>
				<!--<address>
					<i class="icon-phone"></i> <?php echo $this->_tpl_vars['ep_contrib_profile_telephone']; ?>
<br>
					<a href="mailto:#"><i class="icon-email"></i> <?php echo $this->_tpl_vars['email']; ?>
</a>
				</address>-->
				<?php if ($_GET['user_id']): ?>
					<address>
						/
					</address>	
				<?php else: ?>
					<address>
						Published only to the client who has selected you
					</address>
					<a class="btn btn-small" href="#">Contact <?php echo $this->_tpl_vars['ep_contrib_profile_fname']; ?>
</a>
				<?php endif; ?>	
				
			</div>
		</div>
	</div>
</section>
<!-- end, contributor status --> 
 
<div class="row-fluid"> 
	<div class="span8">
		<section id="skills">
			<div class="mod">
				<h4>Languages</h4>
				<strong><?php echo $this->_tpl_vars['ep_language']; ?>
</strong> (mother tongue)
				<?php if (count($this->_tpl_vars['language_more']) > 0): ?>
					<?php $_from = $this->_tpl_vars['language_more']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
						<div class="skillstat row-fluid">
							<div class="span6">
								<p><strong><?php echo $this->_tpl_vars['lang']['name']; ?>
</strong>  <?php echo $this->_tpl_vars['lang']['percent']; ?>
%</p>
								<div class="progress">
									<div class="bar" style="width: <?php echo $this->_tpl_vars['lang']['percent']; ?>
%"></div>
								</div>
								<span class="pull-left legend muted">Beginner</span> <span class="pull-right legend muted">Bilingual</span>
							</div>
						</div>
					<?php endforeach; endif; unset($_from); ?>
				<?php endif; ?>	
			</div>

			<div class="mod">
				<h4>Skills/Areas of Expertise</h4>

				<?php if (count($this->_tpl_vars['category_more']) > 0): ?>
					<?php $_from = $this->_tpl_vars['category_more']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['category']):
?>
						<div class="skillstat row-fluid">
							<div class="span6">
								<p><strong data-original-title="<?php echo $this->_tpl_vars['category']['name']; ?>
" rel="tooltip"><?php echo $this->_tpl_vars['category']['name']; ?>
</strong>  <?php echo $this->_tpl_vars['category']['percent']; ?>
%</p>
								<div class="progress">
									<div class="bar" style="width: <?php echo $this->_tpl_vars['category']['percent']; ?>
%"></div>
								</div>
								<span class="pull-left legend muted">Beginner</span> <span class="pull-right legend muted">Expert</span>
							</div>
						</div>		
					<?php endforeach; endif; unset($_from); ?>
				<?php endif; ?>			
			</div>
			<div class="mod">
				<h4>Work Experience</h4>
				<?php if (count($this->_tpl_vars['jobDetails']) > 0): ?>
				<dl>
					<?php $_from = $this->_tpl_vars['jobDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['job_details'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['job_details']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['job']):
        $this->_foreach['job_details']['iteration']++;
?>
						<dt><?php echo $this->_tpl_vars['job']['title']; ?>
</dt>
						<dd class="company"><?php echo $this->_tpl_vars['job']['institute']; ?>
</dd>
						<dd class="muted">
							Type de contrat : <?php if ($this->_tpl_vars['job']['contract'] == 'cdi'): ?>Full-time<?php elseif ($this->_tpl_vars['job']['contract'] == 'cdd'): ?>Part-time<?php elseif ($this->_tpl_vars['job']['contract'] == 'freelance'): ?>Freelance<?php elseif ($this->_tpl_vars['job']['contract'] == 'intern'): ?>Placement/Training<?php elseif ($this->_tpl_vars['job']['contract'] == 'stage'): ?>Placement/Training<?php endif; ?>
						</dd>
						<dd class="muted">
							<time datetime="<?php echo $this->_tpl_vars['job']['start_date']; ?>
"><?php echo $this->_tpl_vars['job']['start_date']; ?>
</time> - <time datetime="<?php echo $this->_tpl_vars['job']['end_date']; ?>
"><?php echo $this->_tpl_vars['job']['end_date']; ?>
</time>
						</dd>
					<?php endforeach; endif; unset($_from); ?>							
				</dl>
				<?php endif; ?>
			</div>
			<div class="mod">
				<h4>Training/Education</h4>
				<?php if (count($this->_tpl_vars['educationDetails']) > 0): ?>
				<dl>	
					<?php $_from = $this->_tpl_vars['educationDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['edu_details'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['edu_details']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['education']):
        $this->_foreach['edu_details']['iteration']++;
?>
						<dt><?php echo $this->_tpl_vars['education']['title']; ?>
</dt>
						<dd class="company"><?php echo $this->_tpl_vars['education']['institute']; ?>
</dd>
						<dd class="muted">
							<time datetime="<?php echo $this->_tpl_vars['education']['start_date']; ?>
"><?php echo $this->_tpl_vars['education']['start_date']; ?>
</time> - <time datetime="<?php echo $this->_tpl_vars['education']['end_date']; ?>
"><?php echo $this->_tpl_vars['education']['end_date']; ?>
</time>
						</dd>
						<?php endforeach; endif; unset($_from); ?>
				</dl>
				<?php endif; ?>

			</div>				
		</section>
	</div>

	<div class="span4">
	<!--  right column  -->
		<aside>
			<div class="aside-bg">
				<div class="aside-block" id="we-trust">
					<h4>Your Work</h4>
					<?php if ($this->_tpl_vars['publishedClients'] | @ count > 0): ?>
						<ul class="unstyled">
							<?php $_from = $this->_tpl_vars['publishedClients']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pclient']):
?>
								<li><img title="<?php echo $this->_tpl_vars['pclient']['company_name']; ?>
" src="<?php echo $this->_tpl_vars['pclient']['client_pic']; ?>
"></li>
							<?php endforeach; endif; unset($_from); ?>
							
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</aside>  
	</div>
</div>
        <a class="pull-right btn btn-small disabled anchor-top" href="#brand"><i class="icon-arrow-up"></i></a>