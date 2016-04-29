<?php /* Smarty version 2.6.19, created on 2016-03-10 08:41:57
         compiled from Client/devispremium.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'seventy_price', 'Client/devispremium.phtml', 82, false),array('modifier', 'zero_cut', 'Client/devispremium.phtml', 82, false),array('modifier', 'count', 'Client/devispremium.phtml', 120, false),array('modifier', 'utf8_decode', 'Client/devispremium.phtml', 238, false),array('modifier', 'strlen', 'Client/devispremium.phtml', 250, false),array('modifier', 'truncate', 'Client/devispremium.phtml', 257, false),array('function', 'math', 'Client/devispremium.phtml', 85, false),)), $this); ?>
<?php echo '
<script type="text/javascript" charset="utf-8" src="/FO/script/client/countdown.js"></script>
<script>
	$(document).ready(function(){
		//////////show timer article_submit_expires//////////
		
			$("#poll_time").countdown({
				timestamp	: $("#polltime").val(), 
				callback	: function(days, hours, minutes, seconds){

					var message = "";
					if(days!="0")
						hours=hours+(days*24);
						
					if(hours!="0")
						message += hours + "h" +"&nbsp;";
					if(minutes!="0")
						message += minutes + "mn";
					
					$("#poll_time").html(message);
					if(days==0 && hours==0 && minutes==0 && seconds==0)
					{
						$("#poll_time").html("Deadline expired");
						$("#addfav").show();
					}
				}
			});
	});
	var cur_date=$("#now").val();
	var js_date=(new Date().getTime())/ 1000;
	var diff_date=Math.floor(js_date-cur_date);
	
	function filterbyprice()
	{
		var Price=$("#filter_price").val().replace(",",".");
		if(Price=="")
		{	
			alert("Please insert a valid price");
			return false;
		}
		var id=$("#pollid").val();
		
		window.location="/client/devispremium?id="+id+"&filter="+Price;
		
	}
</script>
'; ?>


<br>
<div class="container">
	<section id="summary">
		<div class="row-fluid">
			<div class="span7">
				<h1>Premium quote <span><?php echo $this->_tpl_vars['pollsdetail'][0]['title']; ?>
</span></h1>
			</div>
			<div class="span2 stat">
				<p>Quotes received</p>
				<p class="num-large"><?php echo $this->_tpl_vars['pollsdetail'][0]['participationcount']; ?>
</p>
			</div>
				<div class="span3 stat">
			<p>Receiving end in</p>
				<p class="num-large less24" id="poll_time"></p>   
			</div>
		</div>
	</section>
	
	<div class="row-fluid" id="favaddalert" style="display:none;">       
		<div class="alert alert-success">
			<button class="close" data-dismiss="alert" type="button">&times;</button>
			<i class="icon-ok icon-white"></i>
			Contributor (s) added (s) to your favorites!
		</div>
	</div>
	
	<div class="row-fluid">    
		<div class="span9">
		<input type="hidden" name="polltime" id="polltime" value="<?php echo $this->_tpl_vars['pollendtime']; ?>
" />
			<?php if ($this->_tpl_vars['pollsdetail'][0]['participationcount'] > 0): ?>
			<section id="sort-by">   
				<div class="filtering clearfix dp" style="padding-top: 5px; background: #f5f5f5">
					<div class="span4 title"><!--<?php echo $this->_tpl_vars['pollsetcount']; ?>
 tarifs propos&eacute;s : -->
						<span class="label label-warning" id="maxprice"><?php if ($this->_tpl_vars['pollprice'][0]['maxprice'] != '0'): ?><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['pollprice'][0]['maxprice'])) ? $this->_run_mod_handler('seventy_price', true, $_tmp, $this->_tpl_vars['pollprice'][0]['contrib_percentage']) : smarty_modifier_seventy_price($_tmp, $this->_tpl_vars['pollprice'][0]['contrib_percentage'])))) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
<?php else: ?><?php echo $this->_tpl_vars['pollprice'][0]['maxprice']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['pollprice'][0]['currency'] == 'pound'): ?>&pound;<?php else: ?>&euro;<?php endif; ?> max</span> 
						<span class="label label-success" id="minprice"><?php if ($this->_tpl_vars['pollprice'][0]['minprice'] != '0'): ?><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['pollprice'][0]['minprice'])) ? $this->_run_mod_handler('seventy_price', true, $_tmp, $this->_tpl_vars['pollprice'][0]['contrib_percentage']) : smarty_modifier_seventy_price($_tmp, $this->_tpl_vars['pollprice'][0]['contrib_percentage'])))) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
<?php else: ?><?php echo $this->_tpl_vars['pollprice'][0]['minprice']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['pollprice'][0]['currency'] == 'pound'): ?>&pound;<?php else: ?>&euro;<?php endif; ?> min</span> 
						<?php if ($this->_tpl_vars['pollprice'][0]['participation'] > 0): ?>
							<?php echo smarty_function_math(array('x' => $this->_tpl_vars['pollprice'][0]['sumprice'],'y' => $this->_tpl_vars['pollprice'][0]['participation'],'equation' => "(x/y)",'assign' => 'average'), $this);?>

						<?php endif; ?>
						<span class="label label-reverse" id="avgprice"><?php if ($this->_tpl_vars['pollprice'][0]['sumprice'] != '0'): ?><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['average'])) ? $this->_run_mod_handler('seventy_price', true, $_tmp, $this->_tpl_vars['pollprice'][0]['contrib_percentage']) : smarty_modifier_seventy_price($_tmp, $this->_tpl_vars['pollprice'][0]['contrib_percentage'])))) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
<?php else: ?><?php echo $this->_tpl_vars['pollprice'][0]['sumprice']; ?>
<?php endif; ?> <?php if ($this->_tpl_vars['pollprice'][0]['currency'] == 'pound'): ?>&pound;<?php else: ?>&euro;<?php endif; ?>moy.</span>
					</div>
					
					<div class="span3">   
						<div class="input-append" id="devispremiumtable_filter"> 
							<input class="span7" id="filter_price" name="filter_price" type="text" placeholder="Votre prix" aria-controls="devispremiumtable">
							<button class="btn btn-small" type="button"  style="height: 30px" onClick="filterbyprice();">Filter</button>
						</div>
					</div>
					
					<div class="span5">Sort by : 
						<a class="btn btn-link <?php if ($_GET['sort'] == 'dateasc' || $_GET['sort'] == 'datedesc'): ?>disabled<?php endif; ?>" href="<?php echo $this->_tpl_vars['datesort_link']; ?>
" style="cursor:pointer;">
							Date
							<?php if ($_GET['sort'] == 'dateasc'): ?>
								<i class="icon-circle-arrow-up"></i>
							<?php elseif ($_GET['sort'] == 'datedesc'): ?>
								<i class="icon-circle-arrow-down"></i>
							<?php endif; ?>
						</a> 
						<a class="btn btn-link <?php if ($_GET['sort'] == 'priceasc' || $_GET['sort'] == 'pricedesc'): ?>disabled<?php endif; ?>" href="<?php echo $this->_tpl_vars['pricesort_link']; ?>
" style="cursor:pointer;">
							Prix
							<?php if ($_GET['sort'] == 'priceasc'): ?>
								<i class="icon-circle-arrow-up"></i>
							<?php elseif ($_GET['sort'] == 'pricedesc'): ?>
								<i class="icon-circle-arrow-down"></i>
							<?php endif; ?>
						</a>
						<a class="btn btn-link" href="/client/devispremium?id=<?php echo $_GET['id']; ?>
">Default</a> 
					</div>
				</div>
			</section> 
			<?php endif; ?>
			
			<?php if (count($this->_tpl_vars['pollset']) > 0): ?>
			<section id="quote-listing-table">
				<div class="mod">   
					<form method="POST" name="pollform">
					
					<table class="table table-hover">
						<thead>
							<tr>
								<th><input type="checkbox" id="select_all" name="select_all" value="all" rel="tooltip" onclick="selectALL();" data-original-title="Select all"></th>
								<th>Photo</th>
								<th>Name of the writer</th>
								<th>Rate</th>
								<th style="text-align:center">Language</th>
								<th>Date of dispatch of quote</th>
								<th></th>
							</tr>
						</thead>
						
						<?php $_from = $this->_tpl_vars['pollset']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['pollloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['pollloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['poll']):
        $this->_foreach['pollloop']['iteration']++;
?>
							<tr>
								<td>
									<?php if (in_array ( $this->_tpl_vars['poll']['user_id'] , $this->_tpl_vars['favarray'] )): ?>
										<input type="checkbox" name="contribtype[]" value="<?php echo $this->_tpl_vars['poll']['id']; ?>
#<?php echo $this->_tpl_vars['poll']['user_id']; ?>
" onClick="Addfavlist(<?php echo $this->_tpl_vars['poll']['user_id']; ?>
,'remove')" checked />
									<?php else: ?>	
										<input type="checkbox" name="contribtype[]" value="<?php echo $this->_tpl_vars['poll']['id']; ?>
#<?php echo $this->_tpl_vars['poll']['user_id']; ?>
" onClick="Addfavlist(<?php echo $this->_tpl_vars['poll']['user_id']; ?>
,'add')" />
									<?php endif; ?>
								</td>
								<td class="media">
									<a onclick="loaddevisprofile('<?php echo $this->_tpl_vars['poll']['user_id']; ?>
','<?php echo $this->_tpl_vars['poll']['poll_id']; ?>
','<?php echo ($this->_foreach['pollloop']['iteration']-1); ?>
');" class="pull-left imgframe" style="cursor:pointer;" role="button" data-toggle="modal" data-target="#viewDevisProfile-ajax">
										<img class="media-object" src="<?php echo $this->_tpl_vars['poll']['profilepic']; ?>
" width="60" height="60"/>
									</a>
								</td>
								<td class="title">
									<a onclick="loaddevisprofile('<?php echo $this->_tpl_vars['poll']['user_id']; ?>
','<?php echo $this->_tpl_vars['poll']['poll_id']; ?>
','<?php echo ($this->_foreach['pollloop']['iteration']-1); ?>
');" style="cursor:pointer;" role="button" data-toggle="modal" data-target="#viewDevisProfile-ajax"><?php echo $this->_tpl_vars['poll']['name']; ?>
</a>
									<div class="muted"><?php echo $this->_tpl_vars['poll']['totalparticipation']; ?>
 participations<br><?php echo $this->_tpl_vars['poll']['selectedparticipation']; ?>
 selected times</div></td>
								<td class="price"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['poll']['price_user'])) ? $this->_run_mod_handler('seventy_price', true, $_tmp, $this->_tpl_vars['poll']['contrib_percentage']) : smarty_modifier_seventy_price($_tmp, $this->_tpl_vars['poll']['contrib_percentage'])))) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 <?php if ($this->_tpl_vars['pollprice'][0]['currency'] == 'pound'): ?>&pound;<?php else: ?>&euro;<?php endif; ?></td>
								<td style="text-align:center">
									<img class="flag flag-<?php echo $this->_tpl_vars['poll']['language']; ?>
" src="/FO/images/shim.gif">
								</td>
								<td><?php echo $this->_tpl_vars['poll']['created_at']; ?>
</td>
								<td id="partstatus_<?php echo $this->_tpl_vars['poll']['id']; ?>
">
									<?php if ($this->_tpl_vars['poll']['status'] == 'active'): ?>
										<a href="javascript:void(0);" onClick="pollparticipationactive(<?php echo $this->_tpl_vars['poll']['id']; ?>
,'<?php echo $this->_tpl_vars['poll']['status']; ?>
');" class="btn btn-small">Exclude</a>
									<?php elseif ($this->_tpl_vars['poll']['status'] == 'inactive'): ?>	
										<a href="javascript:void(0);" onClick="pollparticipationactive(<?php echo $this->_tpl_vars['poll']['id']; ?>
,'<?php echo $this->_tpl_vars['poll']['status']; ?>
');" class="btn btn-small">Include</a>
									<?php endif; ?>
								</td> 
							</tr>
								
								
						<?php endforeach; endif; unset($_from); ?>
						<?php $_from = $this->_tpl_vars['pollset1']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['poll1']):
?>
							<input type="hidden" name="previous_<?php echo $this->_tpl_vars['poll1']['user_id']; ?>
" id="previous_<?php echo $this->_tpl_vars['poll1']['user_id']; ?>
" value="<?php echo $this->_tpl_vars['poll1']['previous']; ?>
" />
							<input type="hidden" name="next_<?php echo $this->_tpl_vars['poll1']['user_id']; ?>
" id="next_<?php echo $this->_tpl_vars['poll1']['user_id']; ?>
" value="<?php echo $this->_tpl_vars['poll1']['next']; ?>
" /> 
						<?php endforeach; endif; unset($_from); ?>
					</table>
					
					<hr> 
					
					<div class="btn-group">
						<button class="btn btn-small" type="submit" name="inactivate_all" id="inactivate_all" value="Exclude all" onClick="return updateParticipationall('inactive');">Exclude all</button>
						<button class="btn btn-small" type="submit" name="activate_all" id="activate_all" value="Include all" onClick="return updateParticipationall('active');">Include all</button>
						
						<a class="btn btn-small"  href="/client/downloadpollxls?id=<?php echo $_GET['id']; ?>
" style="margin-left:20px;">Export</a>
						<input type="hidden" id="pollid" name="pollid" value="<?php echo $_GET['id']; ?>
" /> 
						<button class="btn btn-small" type="button" name="addfav" id="addfav" value="addfav" onClick="return addfavoritecontrib();" style="margin-left:20px;display:none;">Confirm selection</button>
					</div>
					</form>
					
				</div>    
			</section>
			<div class="span12">
					<!---Pagination start-->
						<div class="pagination pull-right">
							<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Client/pagination.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						</div>	
					</div>
			<?php else: ?>
				<?php if ($_GET['filter'] != "" && $this->_tpl_vars['pollsdetail'][0]['participationcount'] > 0): ?>
					<h4>No quote for this price</h4>
				<?php else: ?>
					<h4>You have not yet received a quote</h4>
				<?php endif; ?>	
			<?php endif; ?> 
			
			
			<section id="a_o" style="clear:both"> 
				<div class="mod">
					<div class="summary clearfix">
						<h4>DETAILS OF THE PROJECT</h4>
						<ul class="unstyled">
							<li><strong>Call to writen</strong> <span class="bullet">&bull;</span></li>
							<li> Language : <img src="/FO/images/shim.gif" class="flag flag-<?php echo $this->_tpl_vars['pollsdetail'][0]['language']; ?>
"> <span class="bullet">&bull;</span></li>
							<?php $this->assign('cat', $this->_tpl_vars['pollsdetail'][0]['category']); ?>
							<li>Category : <?php echo $this->_tpl_vars['category_array'][$this->_tpl_vars['cat']]; ?>
 <span class="bullet">&bull;</span></li> 
							<li>Nb. of words : <?php echo $this->_tpl_vars['pollsdetail'][0]['min_sign']; ?>
&nbsp;-&nbsp;<?php echo $this->_tpl_vars['pollsdetail'][0]['max_sign']; ?>
 </li>
							<?php if ($this->_tpl_vars['pollsdetail'][0]['file_name'] != ""): ?>
								<li class="pull-right">
									<a class="btn btn-small btn-success" href="/client/downloadpollbrief?id=<?php echo $_GET['id']; ?>
">
									<i class="icon-white icon-circle-arrow-down"></i> Download the client brief
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</section>
			
		</div>

		<div class="span3">
			<aside>
				<div class="aside-bg">
					<?php if (count($this->_tpl_vars['customerstrust']) > 0): ?>
					<div id="we-trust" class="aside-block">
					   <h4>They trust them</h4>
					   <ul class="unstyled <?php if (count($this->_tpl_vars['customerstrust']) > 3): ?>pre-scrollable<?php endif; ?>">
							<?php $_from = $this->_tpl_vars['customerstrust']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ckey'] => $this->_tpl_vars['clogo']):
?>  
								<li><img src="<?php echo $this->_tpl_vars['clogo']; ?>
" rel="tooltip" data-original-title="<?php echo ((is_array($_tmp=$this->_tpl_vars['ckey'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
" data-placement="left"></li>
							<?php endforeach; endif; unset($_from); ?>
					   </ul>
					</div>
					<?php endif; ?>
					
					<div id="quote-ongoing" class="aside-block">
						<h4>OTHER OFFERS</h4>
								<ul class="nav nav-tabs nav-stacked <?php if (count($this->_tpl_vars['quotes']) > 9): ?>pre-scrollable<?php endif; ?>">
							<?php if (count($this->_tpl_vars['quotes']) > 0): ?>
								<?php $_from = $this->_tpl_vars['quotes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['quote']):
?>
									<li>
										<?php if (smarty_modifier_strlen($this->_tpl_vars['quote']['title']) > 28): ?>
											<a href="/client/quotes?id=<?php echo $this->_tpl_vars['quote']['id']; ?>
" rel="tooltip" data-original-title="<?php echo $this->_tpl_vars['quote']['title']; ?>
" data-placement="left">
												<?php if ($this->_tpl_vars['quote']['valid'] == 'yes'): ?>
													<span class="badge pull-right badge-warning">1</span>
												<?php else: ?>	
													<span class="badge pull-right"><?php echo $this->_tpl_vars['quote']['participations']; ?>
</span>
												<?php endif; ?>
											<?php echo ((is_array($_tmp=$this->_tpl_vars['quote']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 28, "...", true) : smarty_modifier_truncate($_tmp, 28, "...", true)); ?>

											</a>
										<?php else: ?>
											<a href="/client/quotes?id=<?php echo $this->_tpl_vars['quote']['id']; ?>
">	
												<?php if ($this->_tpl_vars['quote']['valid'] == 'yes'): ?>
													<span class="badge pull-right badge-warning">1</span>
												<?php else: ?>	
													<span class="badge pull-right"><?php echo $this->_tpl_vars['quote']['participations']; ?>
</span>
												<?php endif; ?>

											<?php echo $this->_tpl_vars['quote']['title']; ?>
</a>
										<?php endif; ?>	
										
									</li>
								<?php endforeach; endif; unset($_from); ?>
							<?php else: ?>
								<li><b>No Quotes</b></li>
							<?php endif; ?>
						</ul>	
						<ul class="nav nav-tabs nav-stacked">
							<li><a href="/client/liberte1"><i class="icon-edit"></i> <strong>Ask for a new quote</strong></a></li>
						</ul>
					</div>
					
					<div class="aside-block" id="garantee">
						<h4>YOUR GUARANTEES</h4>

						<dl>
							<dt><span class="umbrella"></span>Edit-place is your mediator</dt>
							<dd>In the event of a dispute/issue (delay in delivery, article rewrites, refund…)</dd>
							<dt><span class="locked"></span>Secure payment</dt>
							<dd>Our online payment solution guarantees you a hassle-free transaction</dd>
						</dl>
					</div>
				</div>
			</aside>  
		</div>
	</div>
</div>

<!-- ajax use start --> 
<div id="viewDevisProfile-ajax" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<?php if ($this->_tpl_vars['pollsetcount'] > 1): ?>
		<a class="lft" onclick="loaddevisprofile('<?php echo $this->_tpl_vars['poll']['user_id']; ?>
','<?php echo $_GET['id']; ?>
','previous');" role="button" style="cursor:pointer;top:300px;">&lsaquo;</a>
		<a class="rgt" onclick="loaddevisprofile('<?php echo $this->_tpl_vars['poll']['user_id']; ?>
','<?php echo $_GET['id']; ?>
','next');" role="button" style="cursor:pointer;top:300px;">&rsaquo;</a>  
	<?php endif; ?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Writer's profile</h3>
	</div>
	<div class="modal-body">
		<div id="profilecontent">
	
		</div>
	</div>
</div>
<!-- ajax use end -->