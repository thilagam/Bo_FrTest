<?php /* Smarty version 2.6.19, created on 2014-11-26 12:11:35
         compiled from Client/confirmpremium.phtml */ ?>
<div class="modal-header">
	<button type="button" class="close" Onclick="dismissmodal();" aria-hidden="true">&times;</button>
	<div class="center-block">
		<h4>We have found similar quotes</h4>
		<p>These quotes are examples. You'll receive soon your personalized quote.</p>
	</div>
</div>

	<div class="center-block quote-pricing"> 
		<div class="table-responsive">  
			<table class="table table-bordered table-striped">
				<tbody>
				<tr><th></th><th>Tariff 1</th><th>Tariff 2</th><th>Tariff 3</th></tr>
				<?php $_from = $this->_tpl_vars['pricestats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['price']):
?>
				<tr>
					<td><img width="50" height="50" src="/FO/images/imageB3/ctype-<?php echo $this->_tpl_vars['price']['type']; ?>
.png" alt="Guides"><div class="title"><?php if ($this->_tpl_vars['contype'] == 'writing'): ?>Writing<?php else: ?>Translation<?php endif; ?> of <?php echo $this->_tpl_vars['type_array'][$this->_tpl_vars['price']['type']]; ?>
<br> in <?php echo $this->_tpl_vars['lang_array'][$this->_tpl_vars['language']]; ?>
</div></td>
						<td><div class="price"><?php echo $this->_tpl_vars['price']['price1']; ?>
 &pound;</div>Volume : <?php echo $this->_tpl_vars['price']['num1']; ?>
<br>Client's sector: <?php echo $this->_tpl_vars['category_array'][$this->_tpl_vars['category']]; ?>
</td>
					   <td><div class="price"><?php echo $this->_tpl_vars['price']['price2']; ?>
 &pound;</div>Volume : <?php echo $this->_tpl_vars['price']['num2']; ?>
<br>Client's sector: <?php echo $this->_tpl_vars['category_array'][$this->_tpl_vars['category']]; ?>
</td>
						<td><div class="price"><?php echo $this->_tpl_vars['price']['price3']; ?>
 &pound;</div>Volume : <?php echo $this->_tpl_vars['price']['num3']; ?>
<br>Client's sector: <?php echo $this->_tpl_vars['category_array'][$this->_tpl_vars['category']]; ?>
</td>
				</tr>
				<?php endforeach; endif; unset($_from); ?>
				</tbody>
			</table>  
		</div>
	</div>

<div class="modal-footer">
	<p class="center-block alert-info"><span class="glyphicon glyphicon-plus"></span> 
		<?php if ($this->_tpl_vars['aotype'] == 'premium'): ?>
			A content strategist will contact you soon and propose a personnalized quote in less than 24 hours.
		<?php else: ?>
			You are now going to precise your needs and display your ad on our platform.
		<?php endif; ?>
	</p>    
	<button type="button" class="btn btn-default btn-lg" Onclick="dismissmodal();">Cancel</button>
	<button class="btn btn-primary btn-lg" onClick="document.quotes1form.submit();">Ok! Got it</button>
</div>
