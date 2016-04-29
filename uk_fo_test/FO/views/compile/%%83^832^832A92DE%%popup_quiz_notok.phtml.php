<?php /* Smarty version 2.6.19, created on 2015-07-03 13:12:19
         compiled from Contrib/popup_quiz_notok.phtml */ ?>
<section id="quizz-not-ok">
	<div class="row-fluid">
		<div class="pull-center span10 offset1">
			<div class="smiley">:(</div>
			<h1>Lost...</h1>
			<p class="text-error lead">You have answered <?php echo $this->_tpl_vars['num_correct']; ?>
 out of <?php echo $this->_tpl_vars['num_total']; ?>
 questions correctly.</p>
			<p>We regret to inform you that you will not be preselected for this mission.</p>
			<hr>
			<div class="clearfix">
				<a href="/contrib/aosearch" class="btn">See other offers</a>
			</div>
		</div>
	</div>
</section>