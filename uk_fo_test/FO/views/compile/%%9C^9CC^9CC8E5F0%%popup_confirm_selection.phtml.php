<?php /* Smarty version 2.6.19, created on 2014-12-01 13:03:08
         compiled from Contrib/popup_confirm_selection.phtml */ ?>
<?php echo ' 	
<script type="text/javascript">
	$(".killcurrentmodal").click(function(e){	
		e.preventDefault();	
		$(\'#gotoSelection\').modal(\'hide\');
	});

	
    </script>
'; ?>

<div id="gotoSelection" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close killcurrentmodal" aria-hidden="true">&times;</button>
		<h3 id="ModalLabel">Post added</h3>
	</div>
	<div class="modal-body">
		<p>You can now set a rate for each offer selected or continue browsing the clients' offers.</p>
	</div>
	<div class="modal-footer">
		<button class="btn killcurrentmodal" aria-hidden="true">Go back to offers</button>
		<a class="btn btn-primary" href="/cart/cart-selection"><i class="icon-list-alt icon-white"></i> See my selection <span class="badge badge-warning cart-selection" id="cart-selection">0</span></a>
	</div>
</div>