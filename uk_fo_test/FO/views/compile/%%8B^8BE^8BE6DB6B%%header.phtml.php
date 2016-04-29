<?php /* Smarty version 2.6.19, created on 2014-11-13 14:36:07
         compiled from Contrib/pattern/header.phtml */ ?>
<header>
	<div class="container navbar navbar-inverse">
		<div class="navbar-inner">
			<div class="container">
				  <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				  </a>
				<a href="/index" title="Accueil edit-place" class="brand" id="brand"><img src="/FO/images/shim.gif" id="logo-extranet"></a>
				   <div class="nav-collapse collapse">
						<ul class="nav">
						  <li><a href="http://www.edit-place.com"><i class="icon-globe icon-white"></i> Edit-place.com</a></li>
						</ul>
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
									<li><a tabindex="-1" href="/contrib/modify-profile">Edit my profile</a></li>
									<li class="divider"></li>
									<li><a tabindex="-1" href="/contrib/logout"><i class="icon-remove-sign"></i> Log out</a></li>
								</ul>
								 
							</li>             
						</ul>   
					
				  </div><!--/.nav-collapse -->
			</div>
			<!-- secondary nav -->
			<div class="container" id="secondary-nav">
			  <div class="nav-collapse collapse">
				<ul class="nav">
				<li><a href="/contrib/home" id="menu_home">Home</a></li>
				  <li><a href="/contrib/aosearch" id="menu_aosearch" >Offers</a></li>
				  <li><a href="/contrib/ongoing" id="menu_ongoing">My contributions</a></li>
				  <li><a href="/contrib/private-profile" id="menu_profile">My account</a></li>
				  <li><a href="/contrib/inbox" id="menu_mailbox">Messages <?php if ($this->_tpl_vars['unreadCount'] > 0): ?> <span class="badge badge-warning"><?php echo $this->_tpl_vars['unreadCount']; ?>
</span><?php endif; ?></a> </li>
				  <li><a href="/contrib/royalties" id="menu_royalties">My royalties</a></li>
			   
				</ul>
			  </div><!--/.nav-collapse -->
			</div>

		<!-- end -->
		</div>
	</div>
</header>