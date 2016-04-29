<?php /* Smarty version 2.6.19, created on 2015-04-22 11:57:21
         compiled from Client/quotes3liberte.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Client/quotes3liberte.phtml', 37, false),)), $this); ?>
<section class="quoteform" id="step3">
	<div class="container padding">
		<div class="center-block">
			<div class="alert alert-success">Thanks ! We have taken your request into account.</div>
			<p>Max. date for ad to be online : <span class="label label-warning" style="font-size:14px"><?php echo $this->_tpl_vars['time48']; ?>
</span></p>
			<p>Your ad will be displayed to our writers specialized in :<?php echo $this->_tpl_vars['category_array'][$this->_tpl_vars['category']]; ?>
.</p>  
			<p>You'll receive a confirmation at this address : <em><?php echo $this->_tpl_vars['client_email']; ?>
</em>.</p>
		</div>
		<div class="content-block quotelist">     
			<h4><strong>Name of the project</strong> <br>
			<?php echo $this->_tpl_vars['missiontitle']; ?>
</h4>      
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
						<td><?php echo $this->_tpl_vars['lang_array'][$this->_tpl_vars['quote']['writing_lang']]; ?>
</td>
						<td><?php echo $this->_tpl_vars['quote']['objectives']; ?>
</td>
					</tr>
					<?php endforeach; endif; unset($_from); ?>
				</table>
			</div>
			<p><a href="/client/home?type=new" class="btn btn-default">More details</a></p> 
		</div>

		<?php if (count($this->_tpl_vars['categorywriters']) > 0): ?>
		<div class="row">
			<div class="col-xs-12 col-md-8">
				<div class="content-block recap-profile">
					<h4>Ex. of writers in the <?php echo $this->_tpl_vars['category_array'][$this->_tpl_vars['category']]; ?>
 sector</h4>
					<ul style="padding-right:10px">
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
			</div>
			<div class="col-xs-12 col-md-4">
				<div class="content-block">
					<h4><span class="glyphicon glyphicon-info-sign"></span> A propos des tarifs</h4>
					Edit-place prend une <strong>commission de 35%</strong> sur chaque mission réalisée par un rédacteur.
					Pensez à bien intégrer cette composante lorsque vous négociez votre tarif avec le rédacteur.
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>  
</section>

<section class="dashit"  id="garantee">
	<div class="container padding">
		<div class="center-block">
			<h2>Edit-place guarantee</h2>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-4 col-md-offset-1 wow fadeInLeft">
				<div class="center-block"><img src="/FO/images/imageB3/mediateur.png" width="67" height="75" alt="Moderateur">
					<h4>Edit-place is your mediator</h4>
					<p>In the event of a dispute/issue (delay in delivery, article rewrites, refund...)</p>
				</div>
			</div> 
			<div class="col-xs-12 col-md-4 col-md-offset-2 wow fadeInRight">
				<div class="center-block">
					<img src="/FO/images/imageB3/secured.png" width="75" height="75" alt="Paiement sécurisé">
					<h4>Secure payment</h4>
					<p>Our online payment solution guarantees you a hassle-free transaction</p>
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