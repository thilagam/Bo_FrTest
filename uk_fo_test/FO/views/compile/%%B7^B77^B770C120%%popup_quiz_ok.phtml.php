<?php /* Smarty version 2.6.19, created on 2015-08-06 12:38:00
         compiled from Contrib/popup_quiz_ok.phtml */ ?>
<section id="quizz-ok">
	<div class="row-fluid">
		<div class="pull-center span10 offset1">
			<i class="check"></i>
			<h1>Congratulations!</h1>
			<p class="text-success lead">You have answered <?php echo $this->_tpl_vars['num_correct']; ?>
 out of <?php echo $this->_tpl_vars['num_total']; ?>
 questions correctly.</p>
			<hr>
			The Premium mission<br><strong><?php echo $this->_tpl_vars['article_title']; ?>
</strong> <br>has been added to your selection
			<hr>
			<div class="clearfix">
				<a href="/contrib/aosearch" class="btn">See more offers</a>
				<a class="btn btn-primary" href="/cart/cart-selection">See my selection <span class="badge badge-warning"><?php echo $this->_tpl_vars['selected_ao_count']; ?>
</span></a>
			</div>

		</div>
	</div>
</section>

<a href="#brand" class="pull-right btn btn-small disabled anchor-top scroll"><i class="icon-arrow-up"></i></a>

<?php echo '
<script>
$(".scroll").click(function(event){		
		event.preventDefault();
		$(\'html,body\').animate({scrollTop:$(this.hash).offset().top}, 500);
	});

// tooltip activation
    $("[rel=tooltip]").tooltip();
	 $("[rel=popover]").popover();
</script>
'; ?>
