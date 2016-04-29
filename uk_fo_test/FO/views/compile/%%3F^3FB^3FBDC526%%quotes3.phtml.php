<?php /* Smarty version 2.6.19, created on 2015-04-01 15:47:08
         compiled from Client/quotes3.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Client/quotes3.phtml', 33, false),array('modifier', 'upper', 'Client/quotes3.phtml', 50, false),)), $this); ?>
<section class="quoteform" id="step3">
	<div class="container padding">
		<div class="center-block">
			<div class="alert alert-success">Thanks ! We have taken your request into account.</div>
			<h2>An account manager will contact you shortly</h2>
		</div>
		<div class="content-block quotelist">
			<h4>Your quote</h4>
			<div class="table-responsive">     
				<table class="table table-striped">
					<tr> 
						<th>Product</th>
						<th></th>
						<th>Volume</th>
						<th>Sector</th>
						<th>Languages</th>
						<th>Aim</th>
					</tr>
					<?php $_from = $this->_tpl_vars['statarray']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['quote']):
?>
					<tr>
						<td><img src="/FO/images/imageB3/ctype-<?php echo $this->_tpl_vars['quote']['type']; ?>
.png" width="50" height="50"></td>
						<td><?php echo $this->_tpl_vars['quote']['typetext']; ?>
</td>
						<td><?php if ($this->_tpl_vars['quote']['num'] == ""): ?>N/A<?php else: ?><?php echo $this->_tpl_vars['quote']['num']; ?>
<?php endif; ?></td>
						<td><?php echo $this->_tpl_vars['category_array'][$this->_tpl_vars['category']]; ?>
</td>
						<td><?php echo $this->_tpl_vars['lang_array'][$this->_tpl_vars['quote']['translation_from']]; ?>
,<?php echo $this->_tpl_vars['lang_array'][$this->_tpl_vars['quote']['translation_to']]; ?>
</td>
						<td><?php echo $this->_tpl_vars['quote']['objectives']; ?>
</td>
					</tr>
					<?php endforeach; endif; unset($_from); ?>
				</table>
			</div>
		</div>

		<?php if (count($this->_tpl_vars['categorywriters']) > 0): ?>
		<div class="content-block recap-profile">
			<h4>Ex. of writers in the <?php echo $this->_tpl_vars['category_array'][$this->_tpl_vars['category']]; ?>
 sector</h4>
			<ul style="margin-right:40px">
				<?php $_from = $this->_tpl_vars['categorywriters']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['contrib']):
?>
					<li>
						<span class="contrib-frame"><img src="/FO/profiles/contrib/pictures/<?php echo $this->_tpl_vars['contrib']['user_id']; ?>
/<?php echo $this->_tpl_vars['contrib']['user_id']; ?>
_h.jpg" class="media-object"></span>
						<div class="contrib-name clearfix"><?php echo $this->_tpl_vars['contrib']['last_name']; ?>
</div>
					</li>
				<?php endforeach; endif; unset($_from); ?>
			</ul>        
		</div>
		<?php endif; ?>
		
			<div class="content-block quote-pricing">
			<div class="center-block">
				<h4>We have found similar Quotes</h4>
				<!--<p>Les tarifs sont indiqu&eacute;s &agrave; titre indicatif et sont des tarifs par <b><u><?php echo ((is_array($_tmp=$this->_tpl_vars['type_array'][$this->_tpl_vars['type']])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</u></b>. Vous recevrez bientôt votre devis personnalisé</p>-->
				<p>These quotes are examples. You'll receive soon your personalized quote.</p>
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
						
					</tbody></table>  
				</div>
			</div>
		</div>
	</div>
</section>

<section class="dashit"  id="strategy-teasing">
	<div class="container padding">
		<div class="center-block">
			<h2>Edit-place expertise it is also...</h2>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-6 wow fadeInLeft">
				<h4>Editorial strategy</h4>
				<ul>
					<li>Selection of original subjects</li>
					<li>Dectecting best keywords to write on</li>
					<li>Proposal of new formats optimised for social networks</li>
					<li>Analyse and follow up of your results(ranking,shares)</li>
				</ul>
			</div> 
			<div class="col-xs-12 col-md-6 wow fadeInRight">
				<h4>Editorial Solutions</h4>
				<ul>
					<li>Integration of the content</li>
					<li>Content curation</li>
					<li>Community management</li>
					<li>Copywriting on existing content</li>
					<li>Press reviews</li>
				</ul>
			</div> 
		</div>   
	</div>
</section>

