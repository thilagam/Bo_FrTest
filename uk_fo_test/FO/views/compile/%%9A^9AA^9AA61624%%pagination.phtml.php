<?php /* Smarty version 2.6.19, created on 2014-11-17 14:36:42
         compiled from Client/pagination.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Client/pagination.phtml', 5, false),array('function', 'math', 'Client/pagination.phtml', 18, false),)), $this); ?>
<?php if ($this->_tpl_vars['pages']->pageCount > 1): ?>
    <ul>
        <!-- Previous page link -->
        <?php if (isset ( $this->_tpl_vars['pages']->previous )): ?>
            <?php if (count($_GET) == 1 && $_GET['page']): ?>
				<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->previous; ?>
">&laquo;</a></li>
			<?php elseif (count($_GET) > 0): ?>
				<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_tpl_vars['pages']->previous; ?>
">&laquo;</a></li>
			<?php else: ?>	
				<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->previous; ?>
">&laquo;</a></li>
			<?php endif; ?>	
		<?php else: ?>
			<li class="disabled"><a href="#">&laquo;</a></li>
        <?php endif; ?>	
		
		<?php $this->assign('adjacents', 2); ?>
		<?php if ($this->_tpl_vars['pages']->pageCount < ( 7 + ( $this->_tpl_vars['adjacents'] * 2 ) )): ?>
			<?php echo smarty_function_math(array('equation' => "x+1",'x' => $this->_tpl_vars['pages']->pageCount,'assign' => 'total'), $this);?>

			<?php unset($this->_sections['counter']);
$this->_sections['counter']['name'] = 'counter';
$this->_sections['counter']['loop'] = is_array($_loop=$this->_tpl_vars['total']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['counter']['start'] = (int)1;
$this->_sections['counter']['show'] = true;
$this->_sections['counter']['max'] = $this->_sections['counter']['loop'];
$this->_sections['counter']['step'] = 1;
if ($this->_sections['counter']['start'] < 0)
    $this->_sections['counter']['start'] = max($this->_sections['counter']['step'] > 0 ? 0 : -1, $this->_sections['counter']['loop'] + $this->_sections['counter']['start']);
else
    $this->_sections['counter']['start'] = min($this->_sections['counter']['start'], $this->_sections['counter']['step'] > 0 ? $this->_sections['counter']['loop'] : $this->_sections['counter']['loop']-1);
if ($this->_sections['counter']['show']) {
    $this->_sections['counter']['total'] = min(ceil(($this->_sections['counter']['step'] > 0 ? $this->_sections['counter']['loop'] - $this->_sections['counter']['start'] : $this->_sections['counter']['start']+1)/abs($this->_sections['counter']['step'])), $this->_sections['counter']['max']);
    if ($this->_sections['counter']['total'] == 0)
        $this->_sections['counter']['show'] = false;
} else
    $this->_sections['counter']['total'] = 0;
if ($this->_sections['counter']['show']):

            for ($this->_sections['counter']['index'] = $this->_sections['counter']['start'], $this->_sections['counter']['iteration'] = 1;
                 $this->_sections['counter']['iteration'] <= $this->_sections['counter']['total'];
                 $this->_sections['counter']['index'] += $this->_sections['counter']['step'], $this->_sections['counter']['iteration']++):
$this->_sections['counter']['rownum'] = $this->_sections['counter']['iteration'];
$this->_sections['counter']['index_prev'] = $this->_sections['counter']['index'] - $this->_sections['counter']['step'];
$this->_sections['counter']['index_next'] = $this->_sections['counter']['index'] + $this->_sections['counter']['step'];
$this->_sections['counter']['first']      = ($this->_sections['counter']['iteration'] == 1);
$this->_sections['counter']['last']       = ($this->_sections['counter']['iteration'] == $this->_sections['counter']['total']);
?>
						
				<?php if ($this->_sections['counter']['index'] != $this->_tpl_vars['pages']->current): ?>
					<?php if (count($_GET) == 1 && $_GET['page']): ?>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
					<?php elseif (count($_GET) > 0): ?>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
					<?php else: ?>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
					<?php endif; ?>		
				<?php else: ?>
					 <li class="active"><a href="#"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
				<?php endif; ?>
			
			<?php endfor; endif; ?>
			
		<?php elseif ($this->_tpl_vars['pages']->pageCount > ( 7 + ( $this->_tpl_vars['adjacents'] * 2 ) )): ?>
            
                
				<?php if ($this->_tpl_vars['pages']->current < ( 1 + ( $this->_tpl_vars['adjacents'] * 2 ) )): ?>     
                
                    <?php echo smarty_function_math(array('equation' => "(4+(x* 2))",'x' => $this->_tpl_vars['adjacents'],'assign' => 'total'), $this);?>

					<?php unset($this->_sections['counter']);
$this->_sections['counter']['name'] = 'counter';
$this->_sections['counter']['loop'] = is_array($_loop=$this->_tpl_vars['total']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['counter']['start'] = (int)1;
$this->_sections['counter']['show'] = true;
$this->_sections['counter']['max'] = $this->_sections['counter']['loop'];
$this->_sections['counter']['step'] = 1;
if ($this->_sections['counter']['start'] < 0)
    $this->_sections['counter']['start'] = max($this->_sections['counter']['step'] > 0 ? 0 : -1, $this->_sections['counter']['loop'] + $this->_sections['counter']['start']);
else
    $this->_sections['counter']['start'] = min($this->_sections['counter']['start'], $this->_sections['counter']['step'] > 0 ? $this->_sections['counter']['loop'] : $this->_sections['counter']['loop']-1);
if ($this->_sections['counter']['show']) {
    $this->_sections['counter']['total'] = min(ceil(($this->_sections['counter']['step'] > 0 ? $this->_sections['counter']['loop'] - $this->_sections['counter']['start'] : $this->_sections['counter']['start']+1)/abs($this->_sections['counter']['step'])), $this->_sections['counter']['max']);
    if ($this->_sections['counter']['total'] == 0)
        $this->_sections['counter']['show'] = false;
} else
    $this->_sections['counter']['total'] = 0;
if ($this->_sections['counter']['show']):

            for ($this->_sections['counter']['index'] = $this->_sections['counter']['start'], $this->_sections['counter']['iteration'] = 1;
                 $this->_sections['counter']['iteration'] <= $this->_sections['counter']['total'];
                 $this->_sections['counter']['index'] += $this->_sections['counter']['step'], $this->_sections['counter']['iteration']++):
$this->_sections['counter']['rownum'] = $this->_sections['counter']['iteration'];
$this->_sections['counter']['index_prev'] = $this->_sections['counter']['index'] - $this->_sections['counter']['step'];
$this->_sections['counter']['index_next'] = $this->_sections['counter']['index'] + $this->_sections['counter']['step'];
$this->_sections['counter']['first']      = ($this->_sections['counter']['iteration'] == 1);
$this->_sections['counter']['last']       = ($this->_sections['counter']['iteration'] == $this->_sections['counter']['total']);
?>
						<?php if ($this->_sections['counter']['index'] != $this->_tpl_vars['pages']->current): ?>
							<?php if (count($_GET) == 1 && $_GET['page']): ?>
								<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
							<?php elseif (count($_GET) > 0): ?>
								<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
							<?php else: ?>
								<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
							<?php endif; ?>		
						<?php else: ?>
							 <li class="active"><a href="#"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
						<?php endif; ?>	 
					<?php endfor; endif; ?>
						<li class="disabled"><a href="#">...</a></li>		
						<?php if (count($_GET) == 1 && $_GET['page']): ?>
							<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
</a></li>
							<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->pageCount; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount; ?>
</a></li> 
						<?php elseif (count($_GET) > 0): ?>
							<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
</a></li>
							<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_tpl_vars['pages']->pageCount; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount; ?>
</a></li> 
						<?php else: ?>	
							<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
</a></li>
							<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->pageCount; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount; ?>
</a></li> 
						<?php endif; ?>	
						
				                
                <?php elseif (( $this->_tpl_vars['pages']->pageCount- ( $this->_tpl_vars['adjacents'] * 2 ) ) > $this->_tpl_vars['pages']->current && $this->_tpl_vars['pages']->current > ( $this->_tpl_vars['adjacents'] * 2 )): ?>
					<?php if (count($_GET) == 1 && $_GET['page']): ?>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=1">1</a></li>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=2">2</a></li>
					<?php elseif (count($_GET) > 0): ?>	
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=1">1</a></li>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=2">2</a></li>
					<?php else: ?>	
						<li> <a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=1">1</a></li>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=2">2</a></li>
					<?php endif; ?>	
                    <li class="disabled"><a href="#">...</a></li>
					<?php echo smarty_function_math(array('equation' => "x+y+1",'x' => $this->_tpl_vars['pages']->current,'y' => $this->_tpl_vars['adjacents'],'assign' => 'total'), $this);?>

					<?php echo smarty_function_math(array('equation' => "(x-y)",'x' => $this->_tpl_vars['pages']->current,'y' => $this->_tpl_vars['adjacents'],'assign' => 'start'), $this);?>

					
					<?php unset($this->_sections['counter']);
$this->_sections['counter']['name'] = 'counter';
$this->_sections['counter']['loop'] = is_array($_loop=$this->_tpl_vars['total']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['counter']['start'] = (int)$this->_tpl_vars['start'];
$this->_sections['counter']['show'] = true;
$this->_sections['counter']['max'] = $this->_sections['counter']['loop'];
$this->_sections['counter']['step'] = 1;
if ($this->_sections['counter']['start'] < 0)
    $this->_sections['counter']['start'] = max($this->_sections['counter']['step'] > 0 ? 0 : -1, $this->_sections['counter']['loop'] + $this->_sections['counter']['start']);
else
    $this->_sections['counter']['start'] = min($this->_sections['counter']['start'], $this->_sections['counter']['step'] > 0 ? $this->_sections['counter']['loop'] : $this->_sections['counter']['loop']-1);
if ($this->_sections['counter']['show']) {
    $this->_sections['counter']['total'] = min(ceil(($this->_sections['counter']['step'] > 0 ? $this->_sections['counter']['loop'] - $this->_sections['counter']['start'] : $this->_sections['counter']['start']+1)/abs($this->_sections['counter']['step'])), $this->_sections['counter']['max']);
    if ($this->_sections['counter']['total'] == 0)
        $this->_sections['counter']['show'] = false;
} else
    $this->_sections['counter']['total'] = 0;
if ($this->_sections['counter']['show']):

            for ($this->_sections['counter']['index'] = $this->_sections['counter']['start'], $this->_sections['counter']['iteration'] = 1;
                 $this->_sections['counter']['iteration'] <= $this->_sections['counter']['total'];
                 $this->_sections['counter']['index'] += $this->_sections['counter']['step'], $this->_sections['counter']['iteration']++):
$this->_sections['counter']['rownum'] = $this->_sections['counter']['iteration'];
$this->_sections['counter']['index_prev'] = $this->_sections['counter']['index'] - $this->_sections['counter']['step'];
$this->_sections['counter']['index_next'] = $this->_sections['counter']['index'] + $this->_sections['counter']['step'];
$this->_sections['counter']['first']      = ($this->_sections['counter']['iteration'] == 1);
$this->_sections['counter']['last']       = ($this->_sections['counter']['iteration'] == $this->_sections['counter']['total']);
?>
						<?php if ($this->_sections['counter']['index'] != $this->_tpl_vars['pages']->current): ?>
							<?php if (count($_GET) == 1 && $_GET['page']): ?>
								<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
							<?php elseif (count($_GET) > 0): ?>
								<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
							<?php else: ?>
								<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a> </li>	
							<?php endif; ?>		
						<?php else: ?>
							 <li class="active"><a href="#"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
						<?php endif; ?>	 
							 
					<?php endfor; endif; ?>
					<li class="disabled"><a href="#">...</a></li>		
					<?php if (count($_GET) == 1 && $_GET['page']): ?>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
</a></li>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->pageCount; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount; ?>
</a></li> 
					<?php elseif (count($_GET) > 0): ?>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
</a></li>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_tpl_vars['pages']->pageCount; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount; ?>
</a></li> 
					<?php else: ?>	
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount-1; ?>
</a></li>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->pageCount; ?>
"><?php echo $this->_tpl_vars['pages']->pageCount; ?>
</a></li> 
					<?php endif; ?>	
                
				<?php else: ?>
                
                    <?php if (count($_GET) == 1 && $_GET['page']): ?>
						<li> <a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=1">1</a></li>
						<li> <a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=2">2</a></li>
					<?php elseif (count($_GET) > 0): ?>	
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=1">1</a></li>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=2">2</a></li>
					<?php else: ?>	
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=1">1</a></li>
						<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=2">2</a></li>
					<?php endif; ?>
                    <li class="disabled"><a href="#">...</a></li>
					<?php echo smarty_function_math(array('equation' => "(x-(2+(y*2)))",'x' => $this->_tpl_vars['pages']->pageCount,'y' => $this->_tpl_vars['adjacents'],'assign' => 'start'), $this);?>

					<?php echo smarty_function_math(array('equation' => "x+1",'x' => $this->_tpl_vars['pages']->pageCount,'assign' => 'total'), $this);?>

					
					<?php unset($this->_sections['counter']);
$this->_sections['counter']['name'] = 'counter';
$this->_sections['counter']['loop'] = is_array($_loop=$this->_tpl_vars['total']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['counter']['start'] = (int)$this->_tpl_vars['start'];
$this->_sections['counter']['show'] = true;
$this->_sections['counter']['max'] = $this->_sections['counter']['loop'];
$this->_sections['counter']['step'] = 1;
if ($this->_sections['counter']['start'] < 0)
    $this->_sections['counter']['start'] = max($this->_sections['counter']['step'] > 0 ? 0 : -1, $this->_sections['counter']['loop'] + $this->_sections['counter']['start']);
else
    $this->_sections['counter']['start'] = min($this->_sections['counter']['start'], $this->_sections['counter']['step'] > 0 ? $this->_sections['counter']['loop'] : $this->_sections['counter']['loop']-1);
if ($this->_sections['counter']['show']) {
    $this->_sections['counter']['total'] = min(ceil(($this->_sections['counter']['step'] > 0 ? $this->_sections['counter']['loop'] - $this->_sections['counter']['start'] : $this->_sections['counter']['start']+1)/abs($this->_sections['counter']['step'])), $this->_sections['counter']['max']);
    if ($this->_sections['counter']['total'] == 0)
        $this->_sections['counter']['show'] = false;
} else
    $this->_sections['counter']['total'] = 0;
if ($this->_sections['counter']['show']):

            for ($this->_sections['counter']['index'] = $this->_sections['counter']['start'], $this->_sections['counter']['iteration'] = 1;
                 $this->_sections['counter']['iteration'] <= $this->_sections['counter']['total'];
                 $this->_sections['counter']['index'] += $this->_sections['counter']['step'], $this->_sections['counter']['iteration']++):
$this->_sections['counter']['rownum'] = $this->_sections['counter']['iteration'];
$this->_sections['counter']['index_prev'] = $this->_sections['counter']['index'] - $this->_sections['counter']['step'];
$this->_sections['counter']['index_next'] = $this->_sections['counter']['index'] + $this->_sections['counter']['step'];
$this->_sections['counter']['first']      = ($this->_sections['counter']['iteration'] == 1);
$this->_sections['counter']['last']       = ($this->_sections['counter']['iteration'] == $this->_sections['counter']['total']);
?>
						<?php if ($this->_sections['counter']['index'] != $this->_tpl_vars['pages']->current): ?>
							<?php if (count($_GET) == 1 && $_GET['page']): ?>
								<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
							<?php elseif (count($_GET) > 0): ?>
								<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
							<?php else: ?>
								<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_sections['counter']['index']; ?>
"><?php echo $this->_sections['counter']['index']; ?>
</a> </li>	
							<?php endif; ?>		
						<?php else: ?>
							 <li class="active"><a href="#"><?php echo $this->_sections['counter']['index']; ?>
</a></li>
						<?php endif; ?>	 
							 
					<?php endfor; endif; ?>	
					
                <?php endif; ?>
		
            
		<?php endif; ?>	
		
        <!-- Next page link -->
        <?php if (isset ( $this->_tpl_vars['pages']->next )): ?>
			<?php if (count($_GET) == 1 && $_GET['page']): ?>
				<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->next; ?>
">&raquo;</a></li>
			<?php elseif (count($_GET) > 0): ?>
				<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
&page=<?php echo $this->_tpl_vars['pages']->next; ?>
">&raquo;</a></li>
			<?php else: ?>	
				<li><a href="<?php echo $this->_tpl_vars['pageURL']; ?>
?page=<?php echo $this->_tpl_vars['pages']->next; ?>
">&raquo;</a></li>
			<?php endif; ?>	
		<?php else: ?>
		 <li class="disabled"><a href="#">&raquo;</a></li>
		
        <?php endif; ?>
	</ul>
<?php endif; ?>