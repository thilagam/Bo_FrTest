<?php /* Smarty version 2.6.19, created on 2015-01-07 13:07:44
         compiled from Client/pattern/client_footer.phtml */ ?>
	<section id="stats">
	 <div class="container">
		 <header>
			<h3 class="sectiondivider"><span>Edit-place live</span></h3>
		 </header>
		 <div class="row-fluid">
			<span class="span3"><p><?php echo $this->_tpl_vars['stats']['totalUploadedArticles']; ?>
</p>Written articles</span>
			<span class="span3"><p><?php echo $this->_tpl_vars['stats']['totalCreatedArtilces']; ?>
</p>Quotes submitted</span>
			<span class="span3"><p><?php echo $this->_tpl_vars['stats']['totalValidatedArticles']; ?>
</p>Approved articles</span>
			<span class="span3"><p><?php echo $this->_tpl_vars['stats']['newWrites']; ?>
</p>New writers</span>
		 </div>
	 </div>  
	</section>   

	<footer class="footer">
		<div class="container">
			<p class="pull-right">
				<a class="pull-right btn btn-small disabled anchor-top scroll" href="#brand">
					<i class="icon-arrow-up"></i>
				</a>
			</p>
			<div style="float:left;padding-right:90px;">
				<ul class="footer-links">
				  <li><a href="/index/terms-of-service">Terms of Service</a></li>
				  <li><a href="/index/jobs">Jobs</a></li>
				  <li><a href="/index/our-partners">Our partners</a></li> 
				  <li><a href="/index/about-us">Meet the team</a></li>
				  <li><a href="/index/contact">Contact Us</a></li>
				  <li><a href="/index/our-references">Our clients</a></li>
				</ul>
			</div>
			<div>
				<a href="http://www.edit-place.com" rel="tooltip" data-original-title="French"><img class="flag flag-fr" src="/FO/images/shim.gif"></a>
				<a href="http://www.edit-place.co.uk" rel="tooltip" data-original-title="English UK"><img class="flag flag-gb" src="/FO/images/shim.gif"></a>
			</div>
        </div> 
    </footer>
	
<?php echo '
<script>
    // placeholder mgt for old browsers
	$(\'input, textarea\').placeholder();
	
	$(".scroll").click(function(event){		
		event.preventDefault();
		$(\'html,body\').animate({scrollTop:$(this.hash).offset().top}, 500);
		return false;		
	}); 
	
	$(".killcurrentmodal").click(function(event){	
		$(\'#login\').modal(\'hide\');
	});
</script> 
'; ?>

       
  