<?php /* Smarty version 2.6.19, created on 2015-07-14 16:11:54
         compiled from Client/liberte3.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Client/liberte3.phtml', 25, false),)), $this); ?>
<section id="free_form">
	<div class="container">
		<div class="row">
			<div class="span12" style="position: relative">
				<h1>Edit-place <span>Freedom</span></h1>
				<small>Looking for a journalist to work on your project content? <br>Post your ad and receive quotes within 24 h</small>
				<div class="freequote">blabla</div>
				<div id="state">
					<ul class="unstyled">
						<li rel="tooltip" title="Post your project"><span>Create offer</span></li>
						<li rel="tooltip" title="Our writers take a look at your offer and you receive quotes"><span class="online">Publication of offer on Edit-place</span></li> 
						<li class="hightlight" rel="tooltip" title="Choose the writer who will work on your project"><span class="writer_select">Writer selection</span></li>
					</ul>
				</div>
				<div class="row">   
					<div class="span11 well">
						<p class="checked">Announcement sent for distribution</p>
						<h3 class="pull-center"><span>Congratulations! Your ad is being broadcast</span></h3>
						<p class="lead pull-center">It will be distributed to our <?php echo $this->_tpl_vars['countributorcount']; ?>
 editors <span class="label label-inverse"><?php echo $this->_tpl_vars['onlinelimit']; ?>
</span></p>
						<p class="lead pull-center">You will receive an email at the start of the announcement and for each quote received.</p>
					</div>
				</div>
				<div class="row">
					<div class="span6">
						<?php if (count($this->_tpl_vars['newquotes']) > 0): ?>
							<h4>Mes derniers devis</h4>
							<ul>
								<?php $_from = $this->_tpl_vars['newquotes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['quote']):
?>
									<li><a href="/client/quotes?id=<?php echo $this->_tpl_vars['quote']['id']; ?>
"><?php echo $this->_tpl_vars['quote']['title']; ?>
</a></li>
								<?php endforeach; endif; unset($_from); ?>
							</ul>
							<a href="/client/home?type=new" class="btn btn-small">Voir tout</a>
						<?php endif; ?>
					</div> 
					<div class="span6">
						<?php if ($this->_tpl_vars['firstao'] == 'YES'): ?>
							<h4>D&eacute;couvrir mon espace client</h4>
							<p>Tout d'abord, merci d'avoir choisi edit-place pour vos besoins en contenu de qualit&eacute; !<br>Votre espace client est votre tableau de bord pour recevoir vos devis, s&eacute;lectionner votre journaliste et t&eacute;l&eacute;charger vos contenus.</p>
							<a href="/client/profile" class="btn btn-small">Je d&eacute;couvre mon espace client</a>
						<?php else: ?>
							<a href="/client/profile" class="btn btn-small">espace client</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>