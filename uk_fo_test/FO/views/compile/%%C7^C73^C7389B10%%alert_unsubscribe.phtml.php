<?php /* Smarty version 2.6.19, created on 2015-10-16 12:56:52
         compiled from common/alert_unsubscribe.phtml */ ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#3a3a3a" height="60">
	<tr>
		<td bgcolor="#3a3a3a" width="100%">    
		   <table width="1000" cellpadding="0" cellspacing="0" border="0" align="center" style="table-layout:fixed">
				<tr>
					<td width="50%"><a href="http://www.edit-place.co.uk"><img src="/FO/images/newsletter/logo.png" alt="edit-place logo" width="163" height="25" border="0"></a></td>
					<td width="114" style="padding-right:2px"></td>
					<td width="50%" align="right" nowrap style="color: #999999; font-size: 12px;"> <span id="wrname" ></span>  
					</td>
				</tr>
			</table>   
		</td>
	</tr>  
	</table>
	<div align="center" style="padding-top:20px;">
	<h3>
	<?php if ($_GET['uaction'] == 'unsubscribe'): ?>
		Following your request, you have been unsubscribed from receiving automated notifications from Edit-Place. <br>
		Was this a mistake ?  <a href="/user/alert-unsubscribe?uaction=subscribe&user=<?php echo $_GET['user']; ?>
">Click here</a> to subscribe once again.
	<?php endif; ?>

	<?php if ($_GET['uaction'] == 'subscribe'): ?>
		You have now subscribed and will receive further automated notifications from Edit-Place.
	<?php endif; ?>
	</h3>
</div>