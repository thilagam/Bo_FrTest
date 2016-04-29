<?php /* Smarty version 2.6.19, created on 2015-03-02 14:40:19
         compiled from Contrib/mission-badStatus.phtml */ ?>
<section id="contributorBadge">
	<div class="metroline">
		<?php if ($this->_tpl_vars['profile_type'] == 'sub-junior'): ?>
			<div class="leveltext  active">
				<div class="contrib-profile">
					<img src="<?php echo $this->_tpl_vars['profile_pic']; ?>
" />
					You ! 
				</div>
		<?php else: ?><div class="leveltext"><?php endif; ?>			
			<span class="label label-level"><i class="icon-bookmark"></i>Beginner</span>
			<p class="muted">No articles accepted <br> on Edit-place yet</p>
		</div>

		<?php if ($this->_tpl_vars['profile_type'] == 'junior'): ?>
			<div class="leveltext  active">
				<div class="contrib-profile">
					<img src="<?php echo $this->_tpl_vars['profile_pic']; ?>
" />
					You ! 
				</div>
		<?php else: ?><div class="leveltext"><?php endif; ?>
			<span class="label label-level"><i class="icon-bookmark"></i>Junior</span>
			<p class="muted">At least 1 article accepted <br> on Edit-place</p>
		</div>
		<?php if ($this->_tpl_vars['profile_type'] == 'senior'): ?>
		<div class="leveltext  active">
				<div class="contrib-profile">
					<img src="<?php echo $this->_tpl_vars['profile_pic']; ?>
" />
					You ! 
				</div>
		<?php else: ?><div class="leveltext"><?php endif; ?>
			<span class="label label-level"><i class="icon-bookmark"></i>Senior</span>
			<p class="muted">*You are a confirmed <br> writer</p>
		</div>
		<p class="muted"><small>*Status given at the discretion of Edit-place.</small></p>
	</div>
</section>