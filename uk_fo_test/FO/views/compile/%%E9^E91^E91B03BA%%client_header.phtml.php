<?php /* Smarty version 2.6.19, created on 2015-01-07 13:07:44
         compiled from Client/pattern/client_header.phtml */ ?>
<?php echo '
<script language="javascript">
	$("#menu_home").removeClass("active"); 
	$("#menu_profile").removeClass("active");
	$("#menu_mailbox").removeClass("active");
	$("#menu_billing").removeClass("active");
</script>

<style>
	.error { padding-bottom:6px; color:red; margin:0; }
</style>
'; ?>
	
	
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
				<ul class="nav">
					<li><a href="/client/premium">Solution PREMIUM</a></li>
					<li><a href="/our-references">Our clients</a></li>
					<!--<li><a href="http://www.edit-place.com/blog/">Blog <span class="badge badge-warning">New</span></a></li>-->	
					<li><a href="/client/quotes-1" role="button" class="btn btn-mini btn-primary"><i class="icon-edit icon-white"></i>Ask for a quote</a></li>
				</ul>
				<ul class="nav pull-right">  
					<?php if ($this->_tpl_vars['clientidentifier'] == ""): ?>	
						<li><a  href="" data-target="#create-user" data-toggle="modal"><i class="icon-user icon-white"></i> Writer access</a></li>    
					<?php endif; ?>
					<li>
						<?php if ($this->_tpl_vars['clientidentifier'] != ""): ?>	
						 <a data-toggle="dropdown" class="btn btn-mini btn-login dropdown-toggle">
							<i class="icon-user icon-white"></i> <?php echo $this->_tpl_vars['clientname']; ?>
 <span class="caret"></span>
						  </a>
							<!-- Link or button to toggle dropdown -->
						  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							<li><a tabindex="-1" href="/client/home">My client Space</a></li>
							<li><a tabindex="-1" href="/client/profile">Manage my account</a></li>
							<li><a tabindex="-1" href="/client/billing">My invoices</a></li>
							<li class="divider"></li>
							<li><a tabindex="-1" href="/client/inbox">My messages</a></li>
							<li><a tabindex="-1" href="/client/logout">Log out</a></li>
						  </ul>
						<?php else: ?>
							<a href="" data-target="#login" data-toggle="modal"><i class="icon-briefcase icon-white"></i> Log in</a>
						<?php endif; ?>	
				  </li>             
                </ul>    
			  </div><!--/.nav-collapse -->
			  </div>
			   <?php if ($this->_tpl_vars['clientidentifier'] != ""): ?>
			   <!-- secondary nav -->
				<div class="container" id="secondary-nav">
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li><a href="/client/home" id="menu_home">MY CLIENT SPACE</a></li> 
							<li><a href="/client/profile" id="menu_profile">MY ACCOUNT</a></li>
							<li><a href="/client/inbox" id="menu_mailbox">MESSAGES <?php if ($this->_tpl_vars['unreadmessage'] > 0): ?><span class="badge badge-warning"><?php echo $this->_tpl_vars['unreadmessage']; ?>
</span><?php endif; ?></a> </li>
							<li><a href="/client/billing" id="menu_billing">INVOICES</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
				<?php endif; ?>
		  </div>
		</div>
    </header>
	
	<!-- ***** Modal collections -->

	<div id="login" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/libertelogin.phtml", 'smarty_include_vars' => array()));
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
			<div class="alert alert-info" id="alert">Enter your email and we will send you your password.</div>
			<form class="form-horizontal">
				<div class="control-group"> 
				<label class="control-label" for="inputEmail" style="margin:0px">Your email</label>
					<div class="controls">
						<input type="text" name="forgotpwdemail" id="forgotpwdemail" placeholder="Email" class="input-xlarge" />
						<div class="error" id="forgotpwdemailerr"></div>
						<br><br>
						<button type="button" class="btn btn-primary" onClick="return forgotpasswordmailindex();">Confirm</button>
						<button class="btn" data-dismiss="modal" aria-hidden="true" type="Cancel">Annuler</button>
					</div>
				</div>
			</form>
		</div>
	</div>