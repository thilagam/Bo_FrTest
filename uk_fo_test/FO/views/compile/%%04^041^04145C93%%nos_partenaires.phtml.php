<?php /* Smarty version 2.6.19, created on 2015-09-24 14:13:24
         compiled from common/nos_partenaires.phtml */ ?>
<section class="gray">
    <div class="container padding pull-top">
		<div class="center-block">
			<h1>Our partners</h1>
		</div>
		<div class="page-inner">
			<h2>Our partners</h2>
				
			<hr>
			<?php $_from = $this->_tpl_vars['partnerlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['part_item']):
?>		
			<div class="mod media">
				<a  class="pull-left"title="Oboulo" href="http://<?php echo $this->_tpl_vars['part_item']['website']; ?>
">
					<img class="media-object" alt="Oboulo" src="/FO/images/logos/partners/<?php echo $this->_tpl_vars['part_item']['logoname']; ?>
" width="245" height="45"/>
				</a>
			
				<div class="media-body">
					<h4 class="media-heading"><a title="Oboulo" href="http://<?php echo $this->_tpl_vars['part_item']['website']; ?>
"><?php echo $this->_tpl_vars['part_item']['name']; ?>
</a></h4>
					<?php echo $this->_tpl_vars['part_item']['description']; ?>

				</div>
			</div>
			<?php endforeach; endif; unset($_from); ?>		
		</div>
		<p>&nbsp;</p>
	</div>   
</section>