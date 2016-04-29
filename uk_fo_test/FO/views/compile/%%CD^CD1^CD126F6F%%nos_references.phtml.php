<?php /* Smarty version 2.6.19, created on 2015-09-24 14:09:48
         compiled from common/nos_references.phtml */ ?>
<section class="gray">
    <div class="container padding pull-top">
		<div class="center-block">
			<h1>Our clients</h1>
		</div>
		<div class="page-inner">
			<h2>Our clients</h2>				
			<hr>
			<?php $_from = $this->_tpl_vars['referencelist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ref_item']):
?>			
				<div class="mod media">
					<a  class="pull-right" target="_blank" href="http://<?php echo $this->_tpl_vars['ref_item']['website']; ?>
">
						<img class="media-object"  src="/FO/images/logos/references/<?php echo $this->_tpl_vars['ref_item']['logoname']; ?>
" alt="Aufeminin.fr" title="Aufeminin.fr" width="245" height="45"/>
					</a>
				
					<div class="media-body">
						<h4 class="media-heading"><?php echo $this->_tpl_vars['ref_item']['name']; ?>
</h4>
						<p class="org muted"><?php echo $this->_tpl_vars['ref_item']['website']; ?>
</p>
						<?php echo $this->_tpl_vars['ref_item']['description']; ?>

					</div>
				</div>
			<?php endforeach; endif; unset($_from); ?>
		<p>&nbsp;</p>		 
		</div>   
	</div>   
</section>