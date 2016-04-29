<?php /* Smarty version 2.6.19, created on 2015-09-24 14:09:34
         compiled from common/jobs.phtml */ ?>
<section class="gray">
    <div class="container padding pull-top">
		<div class="center-block">
			<h1>Jobs</h1>
		</div>
		<div class="page-inner">
	<?php $_from = $this->_tpl_vars['joblist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['job_item']):
?>		
				<hr>  
				<h3><?php echo $this->_tpl_vars['job_item']['title']; ?>
</h3>  
				<hr>  
				
				<?php if ($this->_tpl_vars['job_item']['summary'] != ""): ?>
					<?php echo $this->_tpl_vars['job_item']['summary']; ?>

				<?php endif; ?>
				
				<?php if ($this->_tpl_vars['job_item']['aboutep_title'] != "" && $this->_tpl_vars['job_item']['aboutep_desc'] != ""): ?>
					<h4><?php echo $this->_tpl_vars['job_item']['aboutep_title']; ?>
</h4>
					<?php echo $this->_tpl_vars['job_item']['aboutep_desc']; ?>

				<?php endif; ?>
				
				<?php if ($this->_tpl_vars['job_item']['mission_title'] != "" && $this->_tpl_vars['job_item']['mission_desc'] != ""): ?>
					<h4><?php echo $this->_tpl_vars['job_item']['mission_title']; ?>
</h4>
					<?php echo $this->_tpl_vars['job_item']['mission_desc']; ?>

				<?php endif; ?>
				
				<?php if ($this->_tpl_vars['job_item']['team_title'] != "" && $this->_tpl_vars['job_item']['team_desc'] != ""): ?>
					<h4><?php echo $this->_tpl_vars['job_item']['team_title']; ?>
</h4>
					<?php echo $this->_tpl_vars['job_item']['team_desc']; ?>

				<?php endif; ?>
				
				<?php if ($this->_tpl_vars['job_item']['willdo_title'] != "" && $this->_tpl_vars['job_item']['willdo_desc'] != ""): ?>
					<h4><?php echo $this->_tpl_vars['job_item']['willdo_title']; ?>
</h4>
					<?php echo $this->_tpl_vars['job_item']['willdo_desc']; ?>

				<?php endif; ?>
				
				<?php if ($this->_tpl_vars['job_item']['willnotdo_title'] != "" && $this->_tpl_vars['job_item']['willnotdo_desc'] != ""): ?>
					<h4><?php echo $this->_tpl_vars['job_item']['willnotdo_title']; ?>
</h4>
					<?php echo $this->_tpl_vars['job_item']['willnotdo_desc']; ?>

				<?php endif; ?>
				
				<?php if ($this->_tpl_vars['job_item']['profile_title'] != "" && $this->_tpl_vars['job_item']['profile_desc'] != ""): ?>
					<h4><?php echo $this->_tpl_vars['job_item']['profile_title']; ?>
</h4>
					<?php echo $this->_tpl_vars['job_item']['profile_desc']; ?>

				<?php endif; ?>
				
				<?php if ($this->_tpl_vars['job_item']['skills_title'] != "" && $this->_tpl_vars['job_item']['skills_desc'] != ""): ?>
					<h4><?php echo $this->_tpl_vars['job_item']['skills_title']; ?>
</h4>
					<?php echo $this->_tpl_vars['job_item']['skills_desc']; ?>

				<?php endif; ?>
				
			<?php endforeach; endif; unset($_from); ?>
			 <p>&nbsp; </p>	
			 
		</div>	 
	</div>   
</section>