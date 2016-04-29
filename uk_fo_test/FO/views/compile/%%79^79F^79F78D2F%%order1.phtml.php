<?php /* Smarty version 2.6.19, created on 2016-03-11 13:40:21
         compiled from Client/order1.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'ceil', 'Client/order1.phtml', 122, false),array('modifier', 'wordwrap', 'Client/order1.phtml', 176, false),array('modifier', 'ucfirst', 'Client/order1.phtml', 205, false),array('modifier', 'zero_cut', 'Client/order1.phtml', 246, false),array('modifier', 'count', 'Client/order1.phtml', 268, false),array('modifier', 'utf8_decode', 'Client/order1.phtml', 273, false),)), $this); ?>
<?php echo '
<script type="text/javascript" charset="utf-8" src="/FO/script/client/countdown.js"></script>
<script>
	$(document).ready(function(){
	/* The following code is executed once the DOM is loaded */
	
	/* This flag will prevent multiple comment submits: */
	var working = false;
	
	/* Listening for the submit event of the form: */
	$(\'#addCommentForm\').submit(function(e){

 		e.preventDefault();
		if(working) return false;
		
		//working = true;
		//$(\'#submit\').val(\'Working..\');
		//$(\'span.error\').remove();
		if($(\'#commentuser\').val()=="")
		{
			$(\'#commentuser\').css("border-color","#FF0000");
		}
		else
		{
			/* Sending the form fileds to submit.php: */
			$.post(\'/client/submitaocomment\',$(this).serialize(),function(msg){
	 
				//working = false;
				$(\'#submit\').val(\'Submit\');
				
				if(msg.status){
					$(msg.html).hide().insertBefore(\'#addCommentContainer\').slideDown();
					$(\'#commentuser\').val(\'\');
					$(\'#commentcountdisp\').html(msg.count);
					$(\'#commentcount\').val(\'\');
					$(\'#commentcount\').val(msg.count);
					$(\'#commentuser\').css("border-color","#CCCCCC");
				}
			
			},\'json\'); 
		}
	});
	
		//////////show timer article_submit_expires//////////

			$("#submit_time").countdown({
				timestamp	: $("#submittime").val(), 
				callback	: function(days, hours, minutes, seconds){
//alert(hours+"-"+minutes+"-"+seconds);
					var message = "";

					//add days to hours
					if(days!="0")
						message += days + "j" +"&nbsp;";
						//hours=hours+(days*24);
						
					if(hours!="0")
						message += hours + "h" +"&nbsp;";
					
					//add seconds to mins
					//if(seconds>0)
						//minutes+=1;
						
					if(minutes!="0")
						message += minutes + "mn"; 
					
					$("#submit_time").html(message);
					if(days==0 && hours==0 && minutes==0 && seconds==0)
					{
						$("#submit_time").html("Deadline expired");
					}
				}
			});
		
	});
		var cur_date=$("#now").val();
		var js_date=(new Date().getTime())/ 1000;
		var diff_date=Math.floor(js_date-cur_date);
	
</script>
'; ?>


<div class="container">
	<!-- start, status -->
	<div class="row-fluid">
		<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>
			<i class="icon-ok icon-white"></i> Project awarded <?php echo $this->_tpl_vars['contact'][0]['name']; ?>
 !
		</div>
		<div id="state2" class="span12">
			<ul class="unstyled">
				<li class="span3" rel="tooltip" data-original-title="Select the writer who will work on your project"><span class="writer_select">Choice of writer</span></li>
				<li class="hightlight span3" rel="tooltip" data-original-title="The writer selected is working on your project"><span class="ongoing">Ongoing work</span></li>
				<li class="span3" rel="tooltip" data-original-title="You pay the amount of the order as a deposit"><span class="cb">Deposit</span></li>
				<li class="span3" rel="tooltip" data-original-title="Download your delivered projects"><span class="dld">Download</span></li>
			</ul>
		</div>
	</div>
	<!-- end, status -->
 
	<!-- start, Summary -->
	<section id="summary">
		<div class="row-fluid">
			<div class="span6">
				<h1><p>Mission</p> <?php echo $this->_tpl_vars['aoparticipation'][0]['title']; ?>
</h1>
			</div>
			<div class="span3 stat">
				<p>Delivery date</p>
				<?php if ($this->_tpl_vars['aoparticipation'][0]['articlestatus'] == 'closed_client'): ?>
					Closed
				<?php else: ?>
					<?php if ($this->_tpl_vars['aoparticipation'][0]['hourdiff'] < 24): ?>
						<p class="num-large less24" id="submit_time"></p>
					<?php else: ?>
						<p class="num-large" id="submit_time"></p>  
					<?php endif; ?>
				<?php endif; ?>	
			</div>
			<input type="hidden" name="submittime" id="submittime" value="<?php echo $this->_tpl_vars['aoparticipation'][0]['article_submit_expires']; ?>
" />
			<input type="hidden" name="now" id="now" value="<?php echo $this->_tpl_vars['now']; ?>
" />
			<div class="span2 stat">
				<p>Rate</p>
				<p class="num-large"><?php echo ((is_array($_tmp=$this->_tpl_vars['aoparticipation'][0]['price_user_total'])) ? $this->_run_mod_handler('ceil', true, $_tmp) : ceil($_tmp)); ?>
 <?php if ($this->_tpl_vars['aoparticipation'][0]['currency'] == 'pound'): ?>&pound;<?php else: ?>&euro;<?php endif; ?></p>
			</div>
			<div class="span1 stat">
				<?php if ($this->_tpl_vars['commentcount'] > 0): ?>
					<div class="icon-comment-large new" id="commentcountdisp" rel="tooltip" data-original-title="Comments on this mission"><a href="#comment"><?php echo $this->_tpl_vars['commentcount']; ?>
</a></div>
				<?php else: ?>	
					<div class="icon-comment-large" id="commentcountdisp"><a href="#comment">0</a></div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<!-- end, summary --> 
 
    <div class="row-fluid"> 
		<?php if ($this->_tpl_vars['aoparticipation'][0]['premium_option'] == '0'): ?>
		<div class="statusbar clearfix">
			<div class="btn-toolbar">
				<div class="btn-group">
					<?php if ($this->_tpl_vars['aoparticipation'][0]['created_by'] == 'FO'): ?>
						<a class="btn btn-small" href="/client/quotes-1?article=<?php echo $_GET['id']; ?>
"><i class="icon-pencil"></i> Re-launch quote</a>
					<?php endif; ?>
					<?php if ($this->_tpl_vars['aoparticipation'][0]['articlestatus'] != 'closed_client'): ?>
						<a class="btn btn-small" href="/client/compose-mail?serviceid=111201092609847&object=<?php echo $this->_tpl_vars['aoparticipation'][0]['title']; ?>
" target="_blank"><i class="icon-envelope"></i> Contact Edit-place</a>
						<a class="btn btn-small" href="javascript:void(0);" onClick="CloseArticle('<?php echo $_GET['id']; ?>
');"><i class="icon-remove"></i> Close quote</a>
					<?php endif; ?>
				</div>
			</div>
		</div> 
		<?php endif; ?>
		<div class="row-fluid"> 
			<div class="span9">
				<section id="file-management">
					<div class="row-fluid file-management-cont">
						<i class="inbox"></i><h4 class="clearfix">Delivery area</h4>
 						<p>File not available</p>
						<p>—</p>
						<div class="pull-center btn-group">
						 <!-- buttons to display while client is waiting for files -->
							<?php if ($this->_tpl_vars['aoparticipation'][0]['articlestatus'] != 'closed_client' && $this->_tpl_vars['aoparticipation'][0]['premium_option'] == '0'): ?>
								<a class="btn btn-small" rel="tooltip" data-original-title="Accorder un délai supplémentaire" data-placement="bottom" data-target="#extendsubmittime" data-toggle="modal" role="button" href="give-moretime.html"><i class="icon-time"></i> Give more time</a>
							<?php endif; ?>
							<a class="btn btn-small" rel="tooltip" data-original-title="Help" data-placement="bottom" onClick="leadaide();"><i class="icon-question-sign"></i> Help</a>
						</div>
					</div>
				</section>
			 
			<section id="a_o">
				<div class="mod">
					<div class="summary clearfix">
						<h4>DETAILS OF THE PROJECT</h4>
						<ul class="unstyled">
							<li><strong>Call to write</strong> <span class="bullet">&bull;</span></li>
							<li> Language : <img src="/FO/images/shim.gif" class="flag flag-<?php echo $this->_tpl_vars['aoparticipation'][0]['language']; ?>
"> <span class="bullet">&bull;</span></li>
							<?php $this->assign('cat', $this->_tpl_vars['aoparticipation'][0]['category']); ?>
							<li>Category : <?php echo ((is_array($_tmp=$this->_tpl_vars['category_array'][$this->_tpl_vars['cat']])) ? $this->_run_mod_handler('wordwrap', true, $_tmp, 30, "<br />\n") : smarty_modifier_wordwrap($_tmp, 30, "<br />\n")); ?>
 </li>
							<li><span class="bullet">&bull;</span></li>  
							<li>Nb. of words : <?php echo $this->_tpl_vars['aoparticipation'][0]['num_min']; ?>
&nbsp;-&nbsp;<?php echo $this->_tpl_vars['aoparticipation'][0]['num_max']; ?>
 </li>
							<li><a href="#comment" class="scroll"><i class="icon-comment"></i> <?php echo $this->_tpl_vars['commentcount']; ?>
</a></li>
							<?php if ($this->_tpl_vars['aoparticipation'][0]['filepath'] != ""): ?>
								<li class="pull-right">
									<a class="btn btn-small btn-success" href="/client/downloadbrief?id=<?php echo $_GET['id']; ?>
">
									<i class="icon-white icon-circle-arrow-down"></i> Download the client brief
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</section>
 
				<section id="comment">
					<h4><i class="icon-comment"></i> Comment</h4>
					<div class="row-fluid" id="addCommentContainer">
						<ul class="media-list">
							<?php $_from = $this->_tpl_vars['commentlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['comment']):
?>
							<li class="media" id="comment_<?php echo $this->_tpl_vars['comment']['identifier']; ?>
">
								<a data-target="#viewProfile-ajax" data-toggle="modal" role="button" href="" class="pull-left">
									<img src="<?php echo $this->_tpl_vars['comment']['profilepic']; ?>
" class="media-object" alt="Topito">
								</a>
								<div class="media-body">
									<h4 class="media-heading">
										<a data-target="#viewProfile-ajax" data-toggle="modal" role="button" href="http://ued.sebcdesign.com/edit-place/client/profile_topito.html">
											<?php if ($this->_tpl_vars['comment']['first_name'] != ""): ?>
												<?php echo ((is_array($_tmp=$this->_tpl_vars['comment']['first_name'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
 
											<?php else: ?> 
												<?php echo $this->_tpl_vars['comment']['email']; ?>

											<?php endif; ?>
										</a>	
										<?php if ($this->_tpl_vars['clientidentifier'] == $this->_tpl_vars['comment']['user_id']): ?>
											<div class="pull-right"><button type="button" class="close" onClick="deletecomment('<?php echo $this->_tpl_vars['comment']['identifier']; ?>
');">&times;</button></div>
										<?php endif; ?>
									</h4>
									<?php echo $this->_tpl_vars['comment']['comments']; ?>

									<p class="muted"><?php echo $this->_tpl_vars['comment']['time']; ?>
</p>
									
								</div>
								
							</li>
							<?php endforeach; endif; unset($_from); ?>
						</ul>
						
						<div class=" well">
							<form method="post" action="" id="addCommentForm" >
								<fieldset>
									<textarea name="commentuser" id="commentuser" placeholder="Add comment" class="span12"></textarea>
									<button class="btn" name="submit" id="submit" type="submit" >Send</button>
								</fieldset>
								
								<input type="hidden" name="article" id="article" value="<?php echo $_GET['id']; ?>
" />
								<input type="hidden" name="commentcount" id="commentcount" value="<?php echo $this->_tpl_vars['commentcount']; ?>
" />
							</form>
						</div>
					</div>
				</section>
			</div>
		
			<div class="span3">
				<!--  right column  -->
				<aside>
					<div class="aside-bg">
						<?php if ($this->_tpl_vars['aoparticipation'][0]['premium_option'] == '0'): ?>
							<div class="editor-price">
								<p class="quote-price">Total Price :<strong> <?php echo ((is_array($_tmp=$this->_tpl_vars['aoparticipation'][0]['price_user_total'])) ? $this->_run_mod_handler('ceil', true, $_tmp) : ceil($_tmp)); ?>
 <?php if ($this->_tpl_vars['aoparticipation'][0]['currency'] == 'pound'): ?>&pound;<?php else: ?>&euro;<?php endif; ?></strong></p>
								<ul class="unstyled stripe">
									<li>Writer tariff : <?php echo ((is_array($_tmp=$this->_tpl_vars['aoparticipation'][0]['price_user'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 <?php if ($this->_tpl_vars['aoparticipation'][0]['currency'] == 'pound'): ?>&pound;<?php else: ?>&euro;<?php endif; ?></li>
									<li>Edit-place fees included : <?php echo ((is_array($_tmp=$this->_tpl_vars['aoparticipation'][0]['ep_percent'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
%</li>
								</ul>
								
							</div>
						<?php endif; ?>
						<div id="selected-editor" class="aside-block">
							<div class="editor-container">
								<h4>Your writer</h4> 
								<a class="imgframe-large" onclick="loadcontribprofile('<?php echo $this->_tpl_vars['contact'][0]['identifier']; ?>
');" role="button" data-toggle="modal" data-target="#viewContribProfile" style="cursor:pointer;">
									<img src="<?php echo $this->_tpl_vars['contact'][0]['profilepic']; ?>
" alt="<?php echo $this->_tpl_vars['contact'][0]['name']; ?>
"></a>
								<p class="editor-name"><a onclick="loadcontribprofile('<?php echo $this->_tpl_vars['contact'][0]['identifier']; ?>
');" role="button" data-toggle="modal" data-target="#viewContribProfile" style="cursor:pointer;"><?php echo $this->_tpl_vars['contact'][0]['name']; ?>
</a></p>
								<?php if ($this->_tpl_vars['aoparticipation'][0]['premium_option'] == '0'): ?>
									<a href="/client/compose-mail?clientid=<?php echo $this->_tpl_vars['contact'][0]['identifier']; ?>
" class="btn btn-small">contact <?php echo ((is_array($_tmp=$this->_tpl_vars['contact'][0]['first_name'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
</a>
									<address>
										<i class="icon-phone"></i> +<?php echo $this->_tpl_vars['contact'][0]['phone_number']; ?>
<br>
										<a href="mailto:<?php echo $this->_tpl_vars['contact'][0]['email']; ?>
"><i class="icon-email"></i> <?php echo $this->_tpl_vars['contact'][0]['email']; ?>
</a>
									</address>
								<?php endif; ?>
							</div>
						</div>
						
						<?php if (count($this->_tpl_vars['customerstrust']) > 0): ?>
						<div id="we-trust" class="aside-block">
							<h4>THE WRITER HAS ALREADY WORKED WITH</h4>
							<ul class="unstyled">
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
						
						<div class="aside-block" id="garantee">
							<h4>YOUR GUARANTEES</h4>
							<dl>
								<dt><span class="umbrella"></span>Edit-place is your mediator</dt>
								<dd>In the event of a dispute/issue (delay in delivery, article rewrites, refund...)</dd>
								<dt><span class="locked"></span>Secure payment</dt>
								<dd>Our online payment solution guarantees you a hassle-free transaction</dd>
							</dl>
						</div>
					</div>
				</aside>  
			</div>
		</div>
    </div>
</div>

<!-- Extend time -->
<div id="extendsubmittime" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Give more time</h3>
	</div>
	<div class="modal-body">
		<form method="POST" name="extendform" id="extendform" action="/client/order1?id=<?php echo $_GET['id']; ?>
">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Client/extendtime.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</form>
	</div>
</div>

<!-- contrib profile -->
<div id="viewContribProfile" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Writer's Profile</h3>
	</div>
	<div class="modal-body">
		<div id="userprofile">
	
		</div>
	</div>
</div>

