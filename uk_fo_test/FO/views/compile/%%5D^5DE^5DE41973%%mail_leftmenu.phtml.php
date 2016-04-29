<?php /* Smarty version 2.6.19, created on 2014-11-17 14:36:45
         compiled from Client/pattern/mail_leftmenu.phtml */ ?>
<div class="span3">
	<aside>
		<p><a id="menu_compose" class="btn btn-small btn-primary btn-block" href="/client/compose-mail">New message</a></p>
		<ul class="nav nav-tabs nav-stacked">
			<li id="menu_inbox"><a href="/client/inbox"><?php if ($this->_tpl_vars['unreadCount'] > 0): ?><span class="badge pull-right"><?php echo $this->_tpl_vars['unreadCount']; ?>
</span><?php endif; ?>Inbox</a></li>
			<li id="menu_sentbox"><a href="/client/sentbox"><i id="sent_icon" style="display:none" class="icon-chevron-right pull-right"></i>Sent</a></li>
			<li id="menu_classifybox"><a href="/client/classifybox"><?php if ($this->_tpl_vars['class_ticket_count'] > 0): ?><span class="badge pull-right"><?php echo $this->_tpl_vars['class_ticket_count']; ?>
</span><?php endif; ?>Folder message</a></li>
		</ul>	
	</aside>
</div>