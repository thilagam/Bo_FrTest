<?php /* Smarty version 2.6.19, created on 2015-01-07 13:06:20
         compiled from Client/pattern/quotes_header.phtml */ ?>
<header class="dashit">

	<div id="header">

		<div id="topnav">

			<div class="container">

				<div class="pull-right">    

					<span id="switch-lang" class="dropdown">

						<a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="index.html"><span class="flag flag-gb"></span> UK <span class="caret"></span></a>

						<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">

							<li><a href="http://ep-test.edit-palce.com"><span class="flag flag-fr"></span> FR</a></li>

						</ul>

					</span>

					| 

					

					<?php if ($this->_tpl_vars['usertype'] == 'client'): ?>		 

					 <a data-toggle="dropdown" class="btn btn-mini btn-login dropdown-toggle">

						<i class="icon-user icon-white"></i> <?php echo $this->_tpl_vars['clientname']; ?>
 <span class="caret"></span>

					  </a>  

					  

						<!-- Link or button to toggle dropdown -->

					  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">

						<li><a tabindex="-1" href="/client/home">My client space</a></li>

						<li><a tabindex="-1" href="/client/profile">Manage my account</a></li>

						<li><a tabindex="-1" href="/client/billing">My invoices</a></li>

						<li class="divider"></li>

						<li><a tabindex="-1" href="/client/inbox">My messages</a></li>

						<li><a tabindex="-1" href="/client/logout">Log out</a></li>

					  </ul>

					   

					<?php elseif ($this->_tpl_vars['usertype'] == 'contributor'): ?>	 

						<a data-toggle="dropdown" class="btn btn-mini btn-login dropdown-toggle">

							<i class="icon-user icon-white"></i> <?php echo $this->_tpl_vars['client_email']; ?>
 <span class="caret"></span>

						</a>

						<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">

							<li><a tabindex="-1" href="/contrib/home">My Space</a></li>

							<li><a tabindex="-1" href="/contrib/modify-profile">Edit my profile</a></li>  

							<li class="divider"></li>

							<li><a tabindex="-1" href="/contrib/logout"><i class="icon-remove-sign"></i> Log out</a></li>

						</ul>

						

					<?php else: ?>

						<a href="" data-target="#create-user" data-toggle="modal"><span class="glyphicon glyphicon-pencil"></span> Contributor access</a> | 

						<a href="" data-target="#login" data-toggle="modal"><span class="glyphicon glyphicon-user"></span> Log in</a>

					<?php endif; ?>

				</div>

			</div>

		</div>

		<nav class="navbar navbar_ep" role="navigation">

			<div class="container">

				<!-- Brand and toggle get grouped for better mobile display -->

				<div class="navbar-header">

					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">

						<span class="sr-only">Navigation</span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>

					</button>

					<a class="navbar-brand" href="/index"><img src="/FO/images/imageB3/svg/logo-ep.svg" width="201" height="34" class="img-responsive"></a>

				</div>



				<!-- Collect the nav links, forms, and other content for toggling -->

				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

					<ul class="nav navbar-nav navbar-right">

						<li class="ep-effect"><a href="/index">Home</a></li>

						<li class="ep-effect"><a href="/index/about-us">Meet the team</a></li>

						<li class="ep-effect"><a href="/index/our-references">Our clients</a></li>

						<!--<li class="ep-effect"><a href="http://www.edit-place.com/blog/">Blog</a></li>-->

						<li><a class="navbar-btn btn btn-primary btn-sm" href="/client/quotes-1">Our tariffs</a></li>

					</ul>

				</div>

			</div>

		</nav>

	</div>

	 <div id="funnel">

    	<div class="container">

    		<div class="center-block">

    		<ul class="nav nav-pills nav-justified">

				<li <?php if ($this->_tpl_vars['pagemenu'] == 1): ?>class="active animated bounceIn"<?php endif; ?>><a href="#"><img src="/FO/images/imageB3/sprites/quote-step-1.png" width="50" height="50">1. Quote</a></li>

				<li <?php if ($this->_tpl_vars['pagemenu'] == 2): ?>class="active animated bounceIn"<?php endif; ?>><a href="#2"><img src="/FO/images/imageB3/sprites/quote-step-2.png" width="50" height="50">2. Who are you ?</a></li>

				<li <?php if ($this->_tpl_vars['pagemenu'] == 3): ?>class="active animated bounceIn"<?php endif; ?>><a href="#3"><img src="/FO/images/imageB3/sprites/quote-step-3.png" width="50" height="50">3. Confirmation</a></li>

			</ul>

    		</div>

    	</div>

    </div>

</header>



<div id="login" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/login.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		</div>

	</div>	

</div>



<div id="create-user" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/create_contribhp.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		</div>

	</div>	

</div>



<div id="lost-password" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<form>

				<div class="modal-header">

					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

					<h3 id="myModalLabel"><i class="icon-user"></i> Forgotten your password ?</h3>

				</div>

				<div class="modal-body">

					<div class="alert alert-info" id="alert"><button type="button" class="close" data-dismiss="alert">&times;</button>Enter your email and we will send you your password.</div>

					<div class="row">  

						<div class="col-xs-12 col-md-6">

							<input type="text" name="forgotpwdemail" id="forgotpwdemail" placeholder="Your Email" class="form-control" />

						</div>

					</div>

					<div class="error" id="forgotpwdemailerr"></div>

				</div>

				<div class="modal-footer">

					<button type="button" class="btn btn-primary" onClick="return forgotpasswordmailindex();">Confirm</button>

					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true" type="button">Cancel</button>

				</div>

			</form>

		</div>

	</div>

</div>