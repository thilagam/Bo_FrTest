<?php /* Smarty version 2.6.19, created on 2015-10-30 09:45:20
         compiled from Contrib/royalties.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'zero_cut', 'Contrib/royalties.phtml', 83, false),array('modifier', 'date_format', 'Contrib/royalties.phtml', 105, false),array('modifier', 'count', 'Contrib/royalties.phtml', 114, false),array('modifier', 'capitalize', 'Contrib/royalties.phtml', 119, false),array('modifier', 'replace', 'Contrib/royalties.phtml', 174, false),)), $this); ?>
<?php echo '
<script type="text/javascript">
	$("#menu_royalties").addClass("active");
	
	var unpaidEuroCount='; ?>
<?php echo $this->_tpl_vars['unpaidEuroCount']; ?>
<?php echo ';
	var unpaidPoundCount='; ?>
<?php echo $this->_tpl_vars['unpaidPoundCount']; ?>
<?php echo ';

	/*function to check whether payment info added or not in profile
	if not then redirect to profile page to validate payment info
	if yes will show pop up with article details */
	
/* $(function(){	
	$("#invoicedetails").click(function(){
		
		$.get(\'/contrib/check-payment-info\', function(data) {
			if(data==\'YES\')
			{
				$("#billing-ajax").modal({
					remote: "/contrib/invoicedetails"
				});
			}
			else
			{
				window.location.href=\'/contrib/modify-profile?profile=invoice#pay_info_type\';
			}
			
		});			
	});
});	 */
function fninvoicedetails(currency)
{
	var unpaidCount=0;
	if(currency==\'euro\')
			unpaidCount=unpaidEuroCount;
	else if(currency==\'pound\')
			unpaidCount=unpaidPoundCount;	
		
	$.get(\'/contrib/check-payment-info\', function(data) {
		if(data==\'YES\')
		{
			if(unpaidCount>0)
			{
				$("#confirmDiv").confirmModal({	
					heading: \'Confirm\',
					body: "The missions that you are requesting payment for will be added to the purchase order that was already generated upon your first request this month.  The payment date that you are selecting for your second request will be considered for all the missions. Do you still wish to pursue this demand?",
					callback: function () {				
						$("#billing-ajax").modal({
							remote: "/contrib/invoicedetails?currency="+currency+"&oldinvoice=yes"
						});								
						//window.location="/contrib/generatepdf/";
					}	
				});
			}
			else{
				$("#billing-ajax").modal({
					remote: "/contrib/invoicedetails?currency="+currency
				});	
			}
			
		}
		else
		{
			window.location.href=\'/contrib/modify-profile?profile=invoice#pay_info_type\';
		}
	});
}
	
</script>
'; ?>
	

<div class="container">
	<!-- start, get contributor status -->
	<section id="status">
		<div class="row-fluid">
			<div class="profilehead-mod">
				<div class="span6">
					<h1>My royalties</h1>
				</div>

				<div class="span3 stat">
					<p>Awaiting payment</p>
					<p class="num-large"><a href="javascript:void(0);">
						<?php echo ((is_array($_tmp=$this->_tpl_vars['pending_royalties'][0]['royalty'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['pending_royalties'][0]['currency']; ?>
;
						<?php if ($this->_tpl_vars['pending_royalties'][0]['royalty'] && $this->_tpl_vars['pending_royalties'][1]['royalty']): ?> , <?php endif; ?>
						<?php if ($this->_tpl_vars['pending_royalties'][1]['royalty']): ?>
								<?php echo ((is_array($_tmp=$this->_tpl_vars['pending_royalties'][1]['royalty'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['pending_royalties'][1]['currency']; ?>
;
						<?php endif; ?>
						</a>
					</p>
				</div>
				<div class="span3 stat"><p>Total royalties</p><p class="num-large"><a href="#">
				<?php echo ((is_array($_tmp=$this->_tpl_vars['total_royalties'][0]['royalty'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['total_royalties'][0]['currency']; ?>
;
				<?php if ($this->_tpl_vars['total_royalties'][1]['royalty']): ?>
						,<?php echo ((is_array($_tmp=$this->_tpl_vars['total_royalties'][1]['royalty'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['total_royalties'][1]['currency']; ?>
;
				<?php endif; ?>				
				</a></p></div>
			</div>
		</div>
	</section>
	 <!-- end, contributor status --> 
    
	<section id="royalties-not-paid">
		<div class="row-fluid">
			<div class="mod">
				<div class="muted pull-right">Last updated <?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")); ?>
</div>
				<h4>UNPAID COMPLETED MISSIONS <i class="icon icon-question-sign" rel="tooltip" data-original-title="One invoice is generated for all the work since last payment. The time to be paid (based on the type of request: advance / regular) starts when you request to be paid (min. <?php echo $this->_tpl_vars['ep_contrib_min_amount']; ?>
 euros or <?php echo $this->_tpl_vars['ep_contrib_min_amount']; ?>
 pounds)"></i> </h4>
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>Date</th><th>Completed mission</th><th>Rate</th>
						</tr>
					</thead>
					<?php $this->assign('total', 0.0); ?>
					<?php if (count($this->_tpl_vars['unpaidRoyalties']) > 0): ?>		
						<?php $_from = $this->_tpl_vars['unpaidRoyalties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['invoice'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['invoice']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['invoice']['iteration']++;
?>
							<tr>
								<td><?php echo ((is_array($_tmp=$this->_tpl_vars['article']['royalty_added_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")); ?>
</td>
								<td><a href="/contrib/mission-published?article_id=<?php echo $this->_tpl_vars['article']['articleId']; ?>
" target="_invoice">
								<?php echo $this->_tpl_vars['article']['AOTitle']; ?>
 / <?php echo ((is_array($_tmp=$this->_tpl_vars['article']['client_name'])) ? $this->_run_mod_handler('capitalize', true, $_tmp) : smarty_modifier_capitalize($_tmp)); ?>
 / <?php echo $this->_tpl_vars['article']['article_created_date']; ?>

								</a></td>
								<td><?php echo ((is_array($_tmp=$this->_tpl_vars['article']['price'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 
								<?php if ($this->_tpl_vars['article']['currency'] == 'euro'): ?>
									&euro;
								<?php else: ?>
									&pound;
								<?php endif; ?>
								</td>
							</tr>
						<?php $this->assign('total', $this->_tpl_vars['total']+$this->_tpl_vars['article']['price']); ?>
						<?php endforeach; endif; unset($_from); ?>
						
					<?php else: ?>
						
					<?php endif; ?>					
				</table>
				<?php if (count($this->_tpl_vars['unpaidRoyalties']) > 0): ?>
					<p class="alert" style="text-align: right"><strong>Total amount to pay : <?php if ($this->_tpl_vars['unpaidEuroTotal']): ?> <?php echo $this->_tpl_vars['unpaidEuroTotal']; ?>
 &euro;<?php endif; ?>
					<?php if ($this->_tpl_vars['unpaidPoundTotal'] && $this->_tpl_vars['unpaidEuroTotal']): ?> , <?php endif; ?>
					<?php if ($this->_tpl_vars['unpaidPoundTotal']): ?> <?php echo $this->_tpl_vars['unpaidPoundTotal']; ?>
 &pound;<?php endif; ?>
					</strong></p>
				<?php endif; ?>					
				<?php if ($this->_tpl_vars['unpaidEuroTotal'] >= $this->_tpl_vars['ep_contrib_min_amount'] || $this->_tpl_vars['unpaidPoundTotal'] >= $this->_tpl_vars['ep_contrib_min_amount']): ?>
					
						<p style="margin:-18px 0px 30px 0px;padding:8px 35px 8px 14px;">						
							<?php if ($this->_tpl_vars['unpaidEuroTotal'] >= $this->_tpl_vars['ep_contrib_min_amount']): ?>
								<button class="btn btn-primary pull-right" id="invoicedetails" onclick="fninvoicedetails('euro')"; name="invoicedetails" type="button" style="margin-left:5px">Request payment(&euro;)</button>
							<?php endif; ?>	
							<?php if ($this->_tpl_vars['unpaidPoundTotal'] >= $this->_tpl_vars['ep_contrib_min_amount']): ?>
								  <button class="btn btn-primary pull-right" id="invoicedetails" onclick="fninvoicedetails('pound')"; name="invoicedetails" type="button">Request payment(&pound;)</button>
							<?php endif; ?>
						</p>					
				<?php else: ?>
					<p class="alert" style="text-align: right"><strong>You can ask for your payment from <?php echo $this->_tpl_vars['ep_contrib_min_amount']; ?>
 euros or <?php echo $this->_tpl_vars['ep_contrib_min_amount']; ?>
 pounds onwards.</strong></p>
				<?php endif; ?>	
				
				<p class="alert" style="text-align: right"><strong>Payment is processed 15th of the following month post your request (fees range from 0% to 8% of the total amount paid).</strong></p>
			</div>
		</div>
	</section>
	<section id="billing-list">
		<div class="row-fluid">
			<div class="mod">
				<h4>MY INVOICES</h4>
				<table class="table table-hover">
					<thead>
						<tr><th>Invoice date</th><th>Invoice name</th><th>Total price</th><th>Status</th><th></th></tr>
					</thead>
					<tbody>
					<?php if ($this->_tpl_vars['royalties'] | @ count > 0): ?>	
						<?php $_from = $this->_tpl_vars['royalties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['royalty']):
?>
						<!-- start, row -->
						<tr>
							<td><?php echo ((is_array($_tmp=$this->_tpl_vars['royalty']['invoicedate'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")); ?>
</td>
							<td>Edit-place invoice # <?php echo ((is_array($_tmp=$this->_tpl_vars['royalty']['invoiceId'])) ? $this->_run_mod_handler('replace', true, $_tmp, 'ep_invoice_', '') : smarty_modifier_replace($_tmp, 'ep_invoice_', '')); ?>
 - <a href="/contrib/invoicedetails?invoiceid=<?php echo ((is_array($_tmp=$this->_tpl_vars['royalty']['invoiceId'])) ? $this->_run_mod_handler('replace', true, $_tmp, 'ep_invoice_', '') : smarty_modifier_replace($_tmp, 'ep_invoice_', '')); ?>
" role="button" data-toggle="modal" data-target="#billing-ajax" class="muted">Details</a></td>
							<td><?php echo ((is_array($_tmp=$this->_tpl_vars['royalty']['total_invoice_paid'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['royalty']['currency']; ?>
;</td>
							<td>
								<?php if ($this->_tpl_vars['royalty']['status'] == 'refuse'): ?>
									Refused
								<?php elseif ($this->_tpl_vars['royalty']['status'] == 'Paid'): ?>
									Paid
								<?php elseif ($this->_tpl_vars['royalty']['status'] == 'inprocess'): ?>
									In Process	
								<?php else: ?>									
									<?php if ($this->_tpl_vars['royalty']['pay_later_month_name']): ?>									
										<span rel="tooltip" data-html="true" data-original-title="Payment to be received on the 15th of  <?php echo $this->_tpl_vars['royalty']['pay_later_month_name']; ?>
">
											Unpaid
										</span>	
									<?php else: ?>
										Unpaid									
									<?php endif; ?>
								<?php endif; ?>
								
							</td>
							<td>
								<?php if ($this->_tpl_vars['royalty']['status'] == 'Paid'): ?>
									<a class="btn btn-small" href="/contrib/downloadinvoice?invoiceid=<?php echo ((is_array($_tmp=$this->_tpl_vars['royalty']['invoiceId'])) ? $this->_run_mod_handler('replace', true, $_tmp, 'ep_invoice_', '') : smarty_modifier_replace($_tmp, 'ep_invoice_', '')); ?>
"><i class="icon-file"></i> Download</a>			
								<?php endif; ?>		
							</td>
						</tr>
						<!-- end, row -->
						<?php endforeach; endif; unset($_from); ?>

					<?php endif; ?>	
					</tbody>
				</table>
				<!---Pagination start-->
					<div class="pagination pull-right">
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/pagination.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					</div>	
					<!---Pagination END-->
			</div>
		</div>			
	</section>   
</div>
<div id = "confirmDiv"></div>
<!-- ajax use start -->
<div id="billing-ajax" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Invoice Details</h3>
	</div>
	<div class="modal-body">

	</div>
</div>
<!-- ajax use end -->