<?php /* Smarty version 2.6.19, created on 2015-07-15 08:58:46
         compiled from common/header.phtml */ ?>
	<header>
		<div class="container navbar navbar-inverse">
		  <div class="navbar-inner">
			<div class="container">
			  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </a>
			  <a id="brand" class="brand" title="Accueil edit-place" href="/"><img id="logo" src="/FO/images/shim.gif"></a>
			  <div class="nav-collapse collapse">
				<ul class="nav pull-right">
				<?php if ($this->_tpl_vars['usertype'] != 'contributor'): ?>	
					<li><a href="/client/premium">PREMIUM Solution</a></li>
					<?php if ($this->_tpl_vars['clientidentifier'] == ""): ?>	
						<li><a  href="" data-target="#create-user" data-toggle="modal">Writer access</a></li>
					<?php endif; ?>
					<li><a href="/client/quotes-1" role="button" class="btn btn-mini btn-primary"><i class="icon-edit icon-white"></i>Ask for a quote</a></li>
				<?php endif; ?>	
				<li>
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
						<ul class="nav pull-right">
							<li id="cartmenu">
							<?php if ($this->_tpl_vars['selected_ao_count']): ?>
								<a class="btn btn-mini" role="button" href="/cart/cart-selection"><i class="icon-list-alt"></i> My selection <span class="badge badge-warning"><?php echo $this->_tpl_vars['selected_ao_count']; ?>
</span></a>
							<?php else: ?>
								<a class="btn btn-mini" role="button"><i class="icon-list-alt"></i> My selection <span class="badge">0</span></a>
							<?php endif; ?>
							</li>						
							
							<li>
								<a data-toggle="dropdown" class="btn btn-mini btn-login dropdown-toggle">
									<i class="icon-user icon-white"></i> <?php echo $this->_tpl_vars['client_email']; ?>
 <span class="caret"></span>
								</a>
								<!-- Link or button to toggle dropdown -->
								<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
									<li><a tabindex="-1" href="/contrib/home">My Space</a></li>
									<li><a tabindex="-1" href="/contrib/modify-profile">Edit my profile</a></li>
									<li class="divider"></li>
									<li><a tabindex="-1" href="/contrib/logout"><i class="icon-remove-sign"></i> Log out</a></li>
								</ul>
								 
							</li>             
						</ul>
						
					<?php else: ?>
						<a data-target="#login" data-toggle="modal" class="btn btn-mini btn-login"><i class="icon-user icon-white"></i> Log in</a>
					<?php endif; ?>	
				  </li>	
				</ul>   
			  </div><!--/.nav-collapse -->
			</div>
		  </div>
		</div>
    </header>
	
	<!-- ***** Modal collections -->

	<div id="login" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/login.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<div id="create-user" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/create_contrib.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div> 
	
	<div id="lost-password" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel"><i class="icon-user"></i> Forgotten your password ?</h3>
		</div>
		<div class="modal-body">
			<div class="alert alert-info" id="alert"><button type="button" class="close" data-dismiss="alert">&times;</button>Enter your email and we will send you your password.</div>
			<form class="form-horizontal">
				<div class="control-group">
				<label class="control-label" for="inputEmail" style="margin:0px">Your email</label>
					<div class="controls">
						<input type="text" name="forgotpwdemail" id="forgotpwdemail" placeholder="Email" class="input-xlarge" />
						<div class="error" id="forgotpwdemailerr"></div>
						<br><br>
						<button type="button" class="btn btn-primary" onClick="return forgotpasswordmailindex();">Confirm</button>
						<button class="btn" data-dismiss="modal" aria-hidden="true" type="button">Cancel</button>
					</div>
				</div>
			</form>
		</div>
	</div>