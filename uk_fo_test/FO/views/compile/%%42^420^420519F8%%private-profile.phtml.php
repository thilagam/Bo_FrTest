<?php /* Smarty version 2.6.19, created on 2015-03-18 08:47:22
         compiled from Contrib/private-profile.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Contrib/private-profile.phtml', 90, false),array('modifier', 'zero_cut', 'Contrib/private-profile.phtml', 223, false),)), $this); ?>
<?php echo '
<script type="text/javascript">
$("#menu_profile").addClass("active");

/**cv uploading**/
$(function(){
		var btnUpload=$(\'#upload_cv\');
		var status=$(\'#cv_status\');
		new AjaxUpload(btnUpload, {
			action: \'upload-cv\',
			name: \'cv_file\',
			onSubmit: function(file, ext){
				 if (! (ext && /^(doc|docx|pdf)$/.test(ext))){ 
                    // extension is not allowed 
					status.text(\'Only doc,docx,pdf files are allowed\').css(\'color\',\'#FF0000\');
					return false;
					}

				 /*if (! (ext && /^(doc|docx|rtf)$/.test(ext))){ 
                    // extension is not allowed 
					status.html(\'Only doc,docx,rtf files are allowed\').css(\'color\',\'#FF0000\');
					return false;
					}*/
				status.html(\'<img src="/FO/images/icon-generic.gif" /> uploading..\');
			},
			onComplete: function(file, response){
				//On completion clear the status
				status.html(\'\');
				
				var obj = $.parseJSON(response);
				if(obj.status=="success"){
					
					status.html(\'CV publi&eacute; le : <time datetime="\'+obj.published+\'">\'+obj.published+\'</time>.\');		
					$("#download_cv").show();		
					
				}				
				else{
					status.html(\'Error in upload\').css(\'color\',\'#FF0000\');
				}
			}
		});			
	});
</script>
'; ?>

<div class="container">
 
	<section id="status">
		<div class="row-fluid">
			<div class="profilehead-mod">
				<div class="span2">
					<div class="editor-container">
						<?php if ($this->_tpl_vars['profileType'] == 'senior'): ?>
							<span data-original-title="Senior level" data-content="I have at least 5 articles approved by Edit-place. I am an approved contributor for Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>Senior</span>
						<?php elseif ($this->_tpl_vars['profileType'] == 'junior'): ?>
							<span data-original-title="Available to Junior" data-content="At least 1 article accepted on Edit-place" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>Junior</span>
						<?php elseif ($this->_tpl_vars['profileType'] == 'sub-junior'): ?>
							<span data-original-title="Available to Beginner" data-content="No articles accepted on Edit-place yet" rel="popover" data-trigger="hover" data-placement="right" class="label label-level"><i class="icon-bookmark"></i>Beginner</span>	
						<?php endif; ?>	
							
						<a class="imgframe-large" href="/contrib/modify-profile"><img src="<?php echo $this->_tpl_vars['profile_image']; ?>
" title="<?php echo $this->_tpl_vars['client_email']; ?>
"></a>
					</div>
				</div>
				<div class="span7 profile-name">
					<h3><?php echo $this->_tpl_vars['client_email']; ?>
</h3>
					<p class="" style=""><?php echo $this->_tpl_vars['ep_contrib_age']; ?>
 years old  <span class="muted">&bull;</span>  <?php echo $this->_tpl_vars['allCategories']; ?>
  <span class="muted">&bull;</span> <?php echo $this->_tpl_vars['ep_language']; ?>
 <?php if ($this->_tpl_vars['allLanguages']): ?> , <?php echo $this->_tpl_vars['allLanguages']; ?>
<?php endif; ?></p>
					<blockquote>
						<i class="icon-leaf"></i> <?php echo $this->_tpl_vars['ep_contrib_profile_self_details']; ?>

					</blockquote>
				</div>

				<div class="span3 stat">
					<div class="progress progress-success">
						<div class="bar" style="width: <?php echo $this->_tpl_vars['profile_percentage']; ?>
%"></div>
					</div>
					Your profile is <strong><?php echo $this->_tpl_vars['profile_percentage']; ?>
%</strong> complete
					<hr>
					<p class=""><a class="btn btn-small btn-block btn-primary" href="/contrib/modify-profile"><i class="icon-refresh icon-white"></i> Update my profile</a></p>
					<p><a href="/contrib/public-profile" role="button" data-toggle="modal" data-target="#viewProfile-ajax" class="btn btn-small btn-block"> See my public profile</a></p>
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
					<h4>Skills/ Areas Of Expertise</h4>

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
								Type de contrat : <?php if ($this->_tpl_vars['job']['contract'] == 'cdi'): ?>Full-time<?php elseif ($this->_tpl_vars['job']['contract'] == 'cdd'): ?>Part-time<?php elseif ($this->_tpl_vars['job']['contract'] == 'freelance'): ?>Freelance<?php elseif ($this->_tpl_vars['job']['contract'] == 'intern'): ?>Temporary<?php elseif ($this->_tpl_vars['job']['contract'] == 'stage'): ?>Placement/Training<?php endif; ?>
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
				<div class="mod">
					<h4>Personal Information</h4>
					<address>
						<strong><?php echo $this->_tpl_vars['ep_contrib_profile_fname']; ?>
 <?php echo $this->_tpl_vars['ep_contrib_profile_lname']; ?>
</strong><br>
						<?php echo $this->_tpl_vars['ep_contrib_profile_address']; ?>
<br>
						<?php echo $this->_tpl_vars['ep_contrib_profile_city']; ?>
, <?php echo $this->_tpl_vars['ep_contrib_profile_postalcode']; ?>
<br>
						<abbr title="Phone">Tel:</abbr> <?php echo $this->_tpl_vars['ep_contrib_profile_telephone']; ?>

					</address>
					Nationality : <?php echo $this->_tpl_vars['nationality']; ?>

				</div>
				<div class="mod"><a href="/contrib/modify-profile" class="btn btn-primary pull-right"><i class="icon-refresh icon-white"></i> Update my profile</a>
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
					<div class="aside-block" id="shipping">
						<h4>Invoicing Information</h4>

						<dl>
									
							
							<dt>Preferred method of payment</dt>
							<dd class="muted">
								<p class="btn btn-small disabled">
									<?php if ($this->_tpl_vars['ep_contrib_profile_payment_type'] == 'paypal'): ?>
										Paypal
									<?php elseif ($this->_tpl_vars['ep_contrib_profile_payment_type'] == 'cheque'): ?>
										Cheque
									<?php elseif ($this->_tpl_vars['ep_contrib_profile_payment_type'] == 'virement'): ?>
										BANK TRANSFER
									<?php endif; ?>	
								</p>
							</dd>
							<dt>Your royalties</dt>
							<dd class="muted">
								Earnings since last invoice : <strong><a href="/contrib/royalties">
								<?php echo ((is_array($_tmp=$this->_tpl_vars['userRoyalty'][0]['royalty'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['userRoyalty'][0]['currency']; ?>
;
								<?php if ($this->_tpl_vars['userRoyalty'][0]['royalty'] && $this->_tpl_vars['userRoyalty'][1]['royalty']): ?> , <?php endif; ?>
								<?php if ($this->_tpl_vars['userRoyalty'][1]['royalty']): ?>
										<?php echo ((is_array($_tmp=$this->_tpl_vars['userRoyalty'][1]['royalty'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['userRoyalty'][1]['currency']; ?>
;
								<?php endif; ?>
								</a></strong>
							</dd>
						</dl>
					</div>
					<div class="aside-block" id="cv">
						<h4>My CV</h4>
						Your CV is viewed by Edit-place or the client when you have been selected for a mission. Remember to update it regularly.
						
						<p class="muted" id="cv_status">
							<?php if ($this->_tpl_vars['cv_exists'] == 'yes'): ?>
								CV updated on : <time datetime="<?php echo $this->_tpl_vars['cv_uploaded_at']; ?>
"><?php echo $this->_tpl_vars['cv_uploaded_at']; ?>
</time>.
							<?php endif; ?>	
						</p>	
						<div class="btn-group">
							
								<a class="btn btn-small" id="download_cv" href="/contrib/download-file?type=cv" <?php if ($this->_tpl_vars['cv_exists'] != 'yes'): ?> style="display:none" <?php endif; ?>><i class="icon-download"></i> Downlaod</a>
								<a class="btn btn-small" id="upload_cv"><i class="icon-upload"></i> New version</a>
						</div>
					</div>
				</div>
			</aside>  
		</div>
	</div>
</div>