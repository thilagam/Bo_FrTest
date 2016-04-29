<?php /* Smarty version 2.6.19, created on 2016-03-11 13:57:46
         compiled from Client/paymentcc.phtml */ ?>
<?php echo '
<script type="text/javascript">
function validate_form()
{
	document.pay_form.submit();
}
</script>
'; ?>

		<body onLoad="validate_form()" >
			<form action=https://epage.payandshop.com/epage.cgi method=post onSubmit="return validate_payments()" name="pay_form" id="pay_form">
					<input type=hidden name="MERCHANT_ID" value="<?php echo $this->_tpl_vars['merchantid']; ?>
">
					<input type=hidden name="ORDER_ID" value="<?php echo $this->_tpl_vars['orderid']; ?>
">
					<input type=hidden name="CURRENCY" value="<?php echo $this->_tpl_vars['curr']; ?>
">
					<input type=hidden name="AMOUNT" value="<?php echo $this->_tpl_vars['amount']; ?>
">
					<input type=hidden name="TIMESTAMP" value="<?php echo $this->_tpl_vars['timestamp']; ?>
">
					<input type=hidden name="MD5HASH" value="<?php echo $this->_tpl_vars['md5hash']; ?>
">
					<input type="hidden" name="SHA1HASH" value="<?php echo $this->_tpl_vars['sha_hash']; ?>
">
					<input type=hidden name="AUTO_SETTLE_FLAG" value="1">
					<input type=hidden value="Proceed to secure server">
					
					<!-- vars for Article payment----->
					<input type=hidden name="client" value="<?php echo $this->_tpl_vars['client']; ?>
">
					<input type=hidden name="article" value="<?php echo $this->_tpl_vars['article']; ?>
">
					<input type=hidden name="art_amount" value="<?php echo $this->_tpl_vars['art_amount']; ?>
">
					<input type=hidden name="delivery" value="<?php echo $this->_tpl_vars['delivery']; ?>
">
					
					<div align="center" style="padding-bottom:250px;"> 
					<br>
					<img src="/FO/images/loading-b.gif" />
					<br>
					<br>
						<strong>You will redirected to our secure payment page.</strong>
					</div>
			</form>
		</body>