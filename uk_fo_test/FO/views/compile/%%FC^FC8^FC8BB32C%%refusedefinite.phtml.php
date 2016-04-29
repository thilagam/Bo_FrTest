<?php /* Smarty version 2.6.19, created on 2016-02-19 14:38:17
         compiled from Client/refusedefinite.phtml */ ?>
<?php echo '
<script language="javascript">
	(function($,W,D)
	{
		var JQUERY4U = {};

		JQUERY4U.UTIL =
		{
			setupFormValidation: function()
			{
					//form validation rules
					$("#refuseform").validate({
						onKeyup:false,
						rules: {
							refusecomment: "required"
						},
						messages: {
							refusecomment: "Please add a comment"						
						}
					});
			}
		}

		//when the dom has loaded setup form validation rules
		$(D).ready(function($) {
			JQUERY4U.UTIL.setupFormValidation();
		});

	})(jQuery, window, document);
	
</script>

'; ?>


<div class="row-fluid">
	<div class="span12">
		<div class="alert alert-warning"><i class="icon-info-sign"></i> You wish to cancel the order. Please explain your reasons in detail.</div>
		<form method="POST" name="refuseform" id="refuseform" action="/client/order4?id=<?php echo $_GET['id']; ?>
">
			<div class="well">
				<label><i class="icon-hand-right"></i> Comment</label>
				<textarea name="refusecomment" id="refusecomment" class="textarea-ask4update span12" rows="12" placeholder="Share your reasons for rejecting the article(s) delivered with <?php echo $this->_tpl_vars['contact'][0]['name']; ?>
"></textarea>
			</div>
			<div class="clearfix">
				<button aria-hidden="true" data-dismiss="modal" class="btn" type="button">Cancel</button>
				<button class="btn btn-primary" type="sumbit" name="refuse_submit" value="refuse_submit">Send</button>
			</div>
			<input type="hidden" name="contribid" id="contribid" value="<?php echo $this->_tpl_vars['contact'][0]['identifier']; ?>
" />
		</form>
	</div>
</div>
       
<a class="pull-right btn btn-small disabled anchor-top scroll" href="#brand"><i class="icon-arrow-up"></i></a>

 