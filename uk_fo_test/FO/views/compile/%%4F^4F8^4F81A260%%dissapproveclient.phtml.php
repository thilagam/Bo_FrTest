<?php /* Smarty version 2.6.19, created on 2016-02-19 14:38:17
         compiled from Client/dissapproveclient.phtml */ ?>
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
					$("#disapform").validate({
						onKeyup:false,
						rules: {
							dissapprovevalue:  {
								required: true,
								number: true
							},
							dissapprovecomment: "required"
						},
						messages: {
							dissapprovevalue: {
								required:"Please specify an additional period",
								number:"Please enter digit",
							},
							dissapprovecomment: "Please add a comment"						
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
	<div class="span9">
		<form method="POST" name="disapform" id="disapform" action="/client/order4?id=<?php echo $_GET['id']; ?>
">
			<div class="well">
				<label><i class="icon-hand-right"></i> List amendments required</label>
				<textarea name="dissapprovecomment" id="dissapprovecomment" class="textarea-ask4update span12" rows="3" placeholder="Share updates required with <?php echo $this->_tpl_vars['contact'][0]['name']; ?>
"></textarea>
				<hr>
				<h4>GRANT <?php echo $this->_tpl_vars['contact'][0]['name']; ?>
 WITH A DEADLINE EXTENSION FOR THE AMENDMENTS</h4>
				<div class="input-prepend">
					<label>Deadline extension :</label>
					<span class="add-on"><i class="icon-plus"></i></span>
					<input class="span1" id="dissapprovevalue" name="dissapprovevalue" type="text" />
					<select class="span2" id="dissapprovetype" name="dissapprovetype">
						<option value="min">Min(s)</option>
						<option value="hour" selected>Hour(s)</option>
						<option value="day">Day(s)</option>
					</select>
					<label class="error" for="dissapprovevalue" generated="true"></label>
				</div>
			</div>
			<div class="clearfix">
				<button aria-hidden="true" data-dismiss="modal" class="btn" type="button">Cancel</button>
				<button class="btn btn-primary" type="sumbit" name="disspprove_submit" value="disspprove_submit">Send</button>
			</div>
			<input type="hidden" name="contribid" id="contribid" value="<?php echo $this->_tpl_vars['contact'][0]['identifier']; ?>
" />
		</form>
	</div>
	<div class="span3">
		<div class="alert alert-info"><i class="icon-info-sign"></i> You want to ask a recovery from <?php echo $this->_tpl_vars['contact'][0]['name']; ?>
. Please give reasons as precisely as possible and fix an additional period.</div>
		<div class="mod">
			<div class="editor-container">
				<h4>Your writer</h4>
				<a class="imgframe-large" onclick="loadcontribprofile('<?php echo $this->_tpl_vars['contact'][0]['identifier']; ?>
');" role="button" data-toggle="modal" data-target="#viewContribProfile" style="cursor:pointer;">
					<img src="<?php echo $this->_tpl_vars['contact'][0]['profilepic']; ?>
" alt="<?php echo $this->_tpl_vars['contact'][0]['name']; ?>
">
				</a>
				<p class="editor-name"><?php echo $this->_tpl_vars['contact'][0]['name']; ?>
</p>
			</div>
		</div>
	</div>
</div>
        
<a class="pull-right btn btn-small disabled anchor-top scroll" href="#brand"><i class="icon-arrow-up"></i></a>