<?php /* Smarty version 2.6.19, created on 2015-08-19 09:11:13
         compiled from Contrib/recruitment-participation-challenge2.phtml */ ?>
<?php if ($this->_tpl_vars['recruit']['link_quiz'] == 'yes' && $this->_tpl_vars['recruit']['quiz_partcipation_id']): ?>
	<h1>Quiz</h1>
	<div class="about mod" id="quiz_block">
		
		<?php if ($this->_tpl_vars['recruit']['qualified'] == 'yes'): ?>
			<h3 class="text-success" style="text-align: center">Congratulations! You passed the preselection quiz</h3>
		<?php elseif ($this->_tpl_vars['recruit']['qualified'] == 'no'): ?>
			<h3 class="text-error">Sorry! We cannot preselect you for this mission</h3>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php if (( $this->_tpl_vars['recruit']['link_quiz'] == 'yes' && $this->_tpl_vars['recruit']['qualified'] == 'yes' ) || ( $this->_tpl_vars['recruit']['link_quiz'] != 'yes' )): ?>
	<?php if ($this->_tpl_vars['recruit']['status'] == 'bid_pending' && ( $this->_tpl_vars['recruit']['contract_signed'] == '' || $this->_tpl_vars['recruit']['contract_signed'] == 'no' )): ?>
		
		<br><br><h1>Article test</h1>
		<p class="lead" style="text-align: center">You have reached the last selection step. We now want to test you on a test article production.</p>
		<div class="about mod" id="sign-contract">		
			
			<!-- contract getting from some other file-->
			<?php echo $this->_tpl_vars['recruit']['contract']; ?>
			
			
		</div>
	<?php endif; ?>
<?php endif; ?>