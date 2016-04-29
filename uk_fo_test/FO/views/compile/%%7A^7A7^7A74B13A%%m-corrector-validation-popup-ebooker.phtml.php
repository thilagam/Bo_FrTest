<?php /* Smarty version 2.6.19, created on 2015-12-23 08:50:45
         compiled from Contrib/m-corrector-validation-popup-ebooker.phtml */ ?>
<?php if ($this->_tpl_vars['missionDetailsCorrector'] | @ count > 0): ?>
	<?php $_from = $this->_tpl_vars['missionDetailsCorrector']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['details'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['details']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['details']['iteration']++;
?>
		<ul id="correctTab" class="nav nav-tabs">
			<li class="active"><a href="#upload" data-toggle="tab"><i class="icon-upload"></i> Confirm</a></li>
			<?php if ($this->_tpl_vars['article']['missiontest'] == 'no'): ?>
			<li class=""><a href="#ask4update" data-toggle="tab"><i class="icon-refresh"></i> Request amendment/rewrite</a></li>
			<li class=""><a href="#refuse" data-toggle="tab"><i class="icon-thumbs-down"></i> Definite refusal</a></li>
			<?php endif; ?>	
		</ul>

		<div class="tab-content">
			<!--  Validate -->     
			<div id="upload" class="tab-pane active">
				<div class="row-fluid">
					<form name="sendarticle" action="/ebooker/send-corrector-stencils" enctype="multipart/form-data" method="POST" id="v_corrector_form">
                    <div class="alert alert-warning span12">
						<img src="/FO/images/info_24.png" style="float:left"/>
						<p class="span11">
							Thank you for rating according to each criteria and leave a comment.<br> Your comment will be reviewed by our team.
						</p>
					</div>
                    <div class="span12">
							<div class="stencilTableWrapper">
								<table class="table">
									<tbody>
										<?php if ($this->_tpl_vars['stencilsDetails'] | @ count > 0): ?>
											<?php $_from = $this->_tpl_vars['stencilsDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['stencil'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['stencil']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['stencil']):
        $this->_foreach['stencil']['iteration']++;
?>
												<?php $this->assign('text_index', ($this->_foreach['stencil']['iteration']-1)+1); ?>
												<tr>
													<td class="stencilNum"><span><?php echo $this->_tpl_vars['text_index']; ?>
</span></td>
													<td class="stencilTextArea ">
														<textarea  name="stencil_text[]" id="stencil_text_<?php echo $this->_tpl_vars['text_index']; ?>
" rows="3" onkeyup="auto_grow(this)"><?php echo $this->_tpl_vars['stencil']; ?>
</textarea>
														<span class="wordCount"></span>
														<p class="missingToken"></p>
														<p class="duplicateContentAlert"></p>
														<a href="" class="duplicateShowCTA" target="_blank" style="display:none">See version with plagirism	</a>
														<div class="duplicateShowContent"></div>
													</td>
												</tr>
											<?php endforeach; endif; unset($_from); ?>
										
										<?php else: ?>
											<tr><td colspan="4"></td></tr>
										<?php endif; ?>	
										<tr>
											<td colspan="4">
												<span class="span12 error" id="plag_error"></span>
											</td>
										</tr>
									</tbody>							
								</table>
							</div>
							<div class="row-fluid">
								<div class="span4">
									<div class="mod">
										<div class="editor-container">
											<h4><i class="icon-star"></i> Give the writer a rating</h4>
											<img alt="<?php echo $this->_tpl_vars['article']['writer_name']; ?>
" src="<?php echo $this->_tpl_vars['article']['writer_pic_profile']; ?>
">
											<p class="editor-name"><?php echo $this->_tpl_vars['article']['writer_name']; ?>
.</p>
										</div>
										<div style="outline: thin dotted ;  "></div>
										<?php if ($this->_tpl_vars['refreasons'] != 'NO'): ?>
											<?php $_from = $this->_tpl_vars['refreasons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['reasons_loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['reasons_loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['reasons_key'] => $this->_tpl_vars['reasons_item']):
        $this->_foreach['reasons_loop']['iteration']++;
?>
											<div class="span12 form-inline"><span class="span6" style="word-wrap:break-word"><?php if ($this->_tpl_vars['reasons_item'] != 'N'): ?><?php echo $this->_tpl_vars['reasons_item']; ?>
<?php else: ?>Overall<?php endif; ?></span>
												<div class="span7 starmarksvalid pull-right" id="starval<?php echo ($this->_foreach['reasons_loop']['iteration']-1)+1; ?>
" ></div>
												<input type="hidden" id="reasonId<?php echo ($this->_foreach['reasons_loop']['iteration']-1)+1; ?>
" value="<?php echo $this->_tpl_vars['reasons_key']; ?>
" />
												<span id="precision-target<?php echo ($this->_foreach['reasons_loop']['iteration']-1)+1; ?>
" class="precision_valid hide"></span>
											</div>
											<?php endforeach; endif; unset($_from); ?>
										<?php else: ?>
											<div class="span12 form-inline"><span class="span6" style="word-wrap:break-word">Overall</span>
												<div class="span7 starmarksvalid pull-right" id="starval1" ></div>
												<input type="hidden" id="reasonId1" value="<?php echo $this->_tpl_vars['reasons_key']; ?>
" />
												<span id="precision-target1" class="precision_valid hide"></span>
											</div>
										<?php endif; ?>
										<input type="hidden" id="marksvald" name="marksvald" vlaue="" />
										<input type="hidden" id="marksvaldwithreason" name="marksvaldwithreason"  />
									</div>
								</div>
								<div class="well span8" id='message_box_corr'>
									<div class="icon_corrector pull-right"></div>
									<p>Hello, <?php echo $this->_tpl_vars['article']['writer_name']; ?>
</p>

									<textarea name="corrector-comment" id="valid_comments" class="textarea-validate input-block-level" rows="12" placeholder="Ex : Your article met our needs and expectations. Do not hesitate to apply for future offers."></textarea>
									<p class="clearfix"><i class="icon-asterisk"></i> <strong>Comment added automatically</strong><br>
										The Edit-place team would like to thank you for your trust.</p>
								</div>
							</div>	
                            <div id="alert_approve_placeholder" class="span11"></div>
							<div class="clearfix pull-right span5">								
                                <button class="btn btn-primary" id="approve" name="approve" type="button">Send</button>
							</div>
					</div>
					<input type="hidden" id="article_id" name="article_id" value="<?php echo $this->_tpl_vars['article_id']; ?>
" />	
					<input type="hidden" id="cparticipation_id" name="cparticipation_id" value="<?php echo $this->_tpl_vars['article']['participationId']; ?>
" />	
					<input type="hidden" id="a_corrector_reparticipation" name="corrector_reparticipation" value="" />	
					<input type="hidden" name="function" value="approve" id="function">
				</form>
				</div>
			</div>
			<!--  Stop, validate --> 

			<?php if ($this->_tpl_vars['article']['missiontest'] == 'no'): ?>
			<!-- update -->
			<div id="ask4update" class="tab-pane">
				<div class="row-fluid">
				<form name="sendarticle" action="/ebooker/send-corrector-stencils" enctype="multipart/form-data" method="POST" id="r_corrector_form">
					<div class="span12" style="padding:0px 10px 0px 10px;">
                        <div class="alert alert-warning span12">
                            <!--<i class="icon-info-sign"></i>-->
							<img src="/FO/images/info_24.png" style="float:left"/>
							<p class="span11">You wish to ask <strong><?php echo $this->_tpl_vars['article']['writer_name']; ?>
</strong> for a rewrite/amendment.<br/>
                            Please give your detailed reasons. Your comment will be reviewed by our teams.</p>
							</div>
                            <div class="span4" style="margin-left:0px">
						
							<div class="mod">
                                    <p style="font-size:20px;text-align:center"><b>Refusal reasons</b></p>
									<hr style="border-top: dotted 1px;" />
                                    <div class="editor-container" style="text-align:left;padding-top:0px">
									<?php $_from = $this->_tpl_vars['refuseTemplates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['refuseTemplates'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['refuseTemplates']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['template']):
        $this->_foreach['refuseTemplates']['iteration']++;
?>
										<label class="checkbox">
											<input type="checkbox" name="refuse_template[]" id="refuse_template_<?php echo $this->_tpl_vars['template']['identifier']; ?>
" value="<?php echo $this->_tpl_vars['template']['identifier']; ?>
"><?php echo $this->_tpl_vars['template']['title']; ?>

										</label>
									<?php endforeach; endif; unset($_from); ?>
								</div>							
							</div>
						</div>	
							<div class="well span8">

								<div class="icon_corrector pull-right"></div>
								<p>Hello, <?php echo $this->_tpl_vars['article']['writer_name']; ?>
</p>

								<textarea name="corrector-comment" id="refuse_comments" class="textarea-ask4update input-block-level" rows="12" placeholder="Ex : You have forgotten to integrate the keywords matching your batch. Please review your articles."></textarea>
								<p class="clearfix"><i class="icon-asterisk"></i> <strong>Comment automatically added to the rewrite/amendment request</strong><br> Comment automatically added to the rewrite/amendment request You have 24 hours to deliver the amended articles. We would like to remind you that if the guidelines are not followed, we are within our rights to ask you to amend/rewrite your article once again. We will accept up to 2 rewrites. The third time, the article will be rejected and will automatically be republished on the platform and the article will not be paid.</p>
							</div>
							<div id="alert_disapprove_placeholder" class="span11"></div>
							<div class="clearfix pull-right">
								<button class="btn btn-primary" name="disapprove" id="disapprove" type="button">Send</button>
							</div>
							<input type="hidden" id="article_id" name="article_id" value="<?php echo $this->_tpl_vars['article_id']; ?>
" />
							<input type="hidden" id="cparticipation_id" name="cparticipation_id" value="<?php echo $this->_tpl_vars['article']['participationId']; ?>
" />
							<input type="hidden" id="d_corrector_reparticipation" name="corrector_reparticipation" value="" />
							<input type="hidden" name="function" value="disapprove" id="function">
					</div>
				</form>	
				</div>
			</div>
				<!-- stop, update --> 			

				<!--  Refus --> 
				<div id="refuse" class="tab-pane">
					<div class="row-fluid">
					<form name="sendarticle" action="/ebooker/send-corrector-stencils" enctype="multipart/form-data" method="POST" id="c_corrector_form">
					<div class="span12" style="padding:0px 10px 0px 10px;">
                        <div class="alert alert-warning span12"><!--<i class="icon-info-sign"></i>-->
							<img src="/FO/images/info_24.png" style="float:left"/>
							<p class="span11">You wish to definitely reject  <strong><?php echo $this->_tpl_vars['article']['writer_name']; ?>
</strong>'s article.<br/>Please give your detailed reasons. Your comment will be reviewed by our teams.</p></div>
							<div class="span4" style="margin-left:0px">
								<div class="mod">
									<div class="editor-container">
                                    <h4><i class="icon-star"></i> Give the writer a rating</h4>
										<img alt="<?php echo $this->_tpl_vars['article']['writer_name']; ?>
" src="<?php echo $this->_tpl_vars['article']['writer_pic_profile']; ?>
">
										<p class="editor-name"><?php echo $this->_tpl_vars['article']['writer_name']; ?>
.</p>
									</div>
                                <div style="outline: thin dotted ;  "></div>
                                <!--<span class="badge"><span id="precision-target_c"></span>/10</span>
                                <div id="star"></div>-->
									<?php if ($this->_tpl_vars['refreasons'] != 'NO'): ?>
                                <?php $_from = $this->_tpl_vars['refreasons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['reasons_loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['reasons_loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['reasons_key'] => $this->_tpl_vars['reasons_item']):
        $this->_foreach['reasons_loop']['iteration']++;
?>
                                <div class="span12 form-inline" ><span class="span6" style="word-wrap:break-word"><?php if ($this->_tpl_vars['reasons_item'] != 'N'): ?><?php echo $this->_tpl_vars['reasons_item']; ?>
<?php else: ?>Overall<?php endif; ?></span>
                                    <div class="span7 starmarksclose pull-right" id="starclose<?php echo ($this->_foreach['reasons_loop']['iteration']-1)+1; ?>
" ></div>
                                    <input type="hidden" id="reasonId-close<?php echo ($this->_foreach['reasons_loop']['iteration']-1)+1; ?>
" value="<?php echo $this->_tpl_vars['reasons_key']; ?>
" />
                                    <span id="precision-target-close<?php echo ($this->_foreach['reasons_loop']['iteration']-1)+1; ?>
" class="precision_close hide"></span>
								</div>
                                <?php endforeach; endif; unset($_from); ?>
									<?php else: ?>
									<div class="span12 form-inline" ><span class="span6" style="word-wrap:break-word">Overall</span>
										<div class="span7 starmarksclose pull-right" id="starclose1" ></div>
										<input type="hidden" id="reasonId-close1" value="<?php echo $this->_tpl_vars['reasons_key']; ?>
" />
										<span id="precision-target-close1" class="precision_close hide"></span>
									</div>
									<?php endif; ?>
                                <input type="hidden" id="marksclose" name="marksclose" vlaue="" />
                                <input type="hidden" id="marksclosevaldwithreason" name="marksclosevaldwithreason"  />
									</div>							
                        </div>
                        <div class="well span8">
                            <div class="icon_corrector pull-right"></div>
                            <p>Hello, <?php echo $this->_tpl_vars['article']['writer_name']; ?>
</p>
                            <textarea name="corrector-comment" id="close_comments" class="textarea-refuse input-block-level" rows="12" placeholder="Ex : You have forgotten to integrate the keywords matching your batch. Please review your articles."></textarea>
                            <p class="clearfix"><i class="icon-asterisk"></i> <strong>Comment added automatically</strong><br>
                                The Edit-place team would like to thank you for your trust. Unfortunately the latest version of the article still isn't satisfactory.
								The article will be republished on the platform. The current work will therefore not be paid.<br>Yours sincerely
								</p>
                        </div>
                        <div id="alert_closed_placeholder" class="span11"></div>
                        <div class="clearfix pull-right">
                            <button class="btn btn-primary" name="closed" id="closed" type="button">Send</button>
								</div>
						</div>
						<input type="hidden" id="article_id" name="article_id" value="<?php echo $this->_tpl_vars['article_id']; ?>
" />	
						<input type="hidden" id="cparticipation_id" name="cparticipation_id" value="<?php echo $this->_tpl_vars['article']['participationId']; ?>
" />	
						<input type="hidden" id="c_corrector_reparticipation" name="corrector_reparticipation" value="" />	
						<input type="hidden" name="function" value="closed" id="function">
					</form>
					</div> 

				</div>
				<!-- stop, refus --> 				
			<?php endif; ?>
			<div id = "confirmDiv"></div>
		</div>
				
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>	
<!--///show loading time for file uploading ///-->
<div id="stencilModal" class="modal hide fade" tabindex="-1" >
    <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">&nbsp;</h3>
       </div>
       <div class="modal-body">			
			<h3>Thank you for submiting your stencils</h3>
			<p>
				We are currently checking your texts,  please don't leave the page this might take several minutes.
			</p>
			<span class="epSpiner">
				<img src="/FO/images/ep-spinner.gif" alt="">
				<p class="spinerState">
					Checking files
				</p>
			</span>	
       </div>
       <div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
       </div>
</div>

<?php echo '
<script  type="text/javascript">

// active tab
$(\'#correctTab a\').click(function (e) {
    e.preventDefault();
    $(this).tab(\'show\');
    })
	
	$(\'#correctTab a:first\').tab(\'show\');


 // call write email function
	$(\'.textarea-ask4update\').wysihtml5();
	$(\'.textarea-refuse\').wysihtml5();
	$(\'.textarea-validate\').wysihtml5();
	
	$(".scroll").click(function(event){		
		event.preventDefault();
		$(\'html,body\').animate({scrollTop:$(this.hash).offset().top}, 500);
	});

 // placeholder mgt for old browsers
    $(\'input, textarea\').placeholder();	

$(function(){


	bootstrap_alert = function() {}
	bootstrap_alert.error = function(div,message) {
		$(\'#\'+div).html(\'<div class="alert  alert-error"><button data-dismiss="alert" class="close" type="button">&times;</button><span><ul>\'+message+\'</ul></span></div>\')
	}
		
	var load_img=\'<img src="/FO/images/loading-b.gif">\';
	var error=0;
	var scrollTop=0;	
	/*bind focus*/
	$("[id^=stencil_text_]" ).bind(\'focus\',function(u) {
		//$(this).parent().parent().removeAttr("class");
		$(this).parent().parent().addClass(\'isEdited\');				
	});	
	/*calculate word count on keyup*/
	$("[id^=stencil_text_]").on(\'input\', function(){				
		var c = wordCount($(this).val());				
		$(this).next(\'.wordCount\').html(c.words +\' Words\');
	});	
	/*text validation on blur*/
	$("[id^=stencil_text_]" ).bind(\'blur\',function(u) {
		var stencil_text=$(this).val();			
		stencil_text=stencil_text.replace(/\\\\t/gi,\' \');			
		stencil_text = stencil_text.replace(/ +(?= )/g,\' \');
		stencil_text=stencil_text.replace(/(\\\\r\\\\n|\\\\n|\\\\r)/gm," ");
		stencil_text=encodeURI(stencil_text);
		stencil_text=stencil_text.replace(\'%E2%80%99\',"\'");
		stencil_text=decodeURI(stencil_text);
		stencil_text=$.trim(stencil_text);
		$(this).val(stencil_text);
		
		var c = wordCount(stencil_text);	
		
		if(stencil_text==\'\')
		{
			$(this).parent().parent().removeAttr("class");
			$(this).parent().parent().addClass(\'stencilError\');
			$(this).nextAll(\'.missingToken\').html(\'Please enter your text\');
			error=error+1;
			if(scrollTop==0)
				scrollTop=$(this).offset().top;
		}		
		else{
			var missing_words=[];
			var check_text=stencil_text.replace(/[\\.\\,\\!\\?]/g,\' \');
			var encodeText=encodeURI(check_text);					
			//alert(encodeText);
			var stringArray=encodeText.split(\'%20\');//converting string to Array			
			//checking Token exists in the text or not
			if(tokensArray.length>0)
			{
				$.each( tokensArray, function( key, token ) {
					//alert(decodeURI(token))
					if ($.inArray(token,stringArray)== -1)
					{
						missing_words.push(\'<span>\'+htmlEntities(decodeURI(token))+\'</span>\');
						//alert(missing_words.length);
					}
				});
			}
			if(missing_words.length>0){
				$(this).parent().parent().removeAttr("class");
				$(this).parent().parent().addClass(\'stencilError\');						
				var missing_text=\'Missing term: \'+missing_words;
				$(this).nextAll(\'.missingToken\').html(missing_text);
				$(this).nextAll(\'.duplicateContentAlert\').html(\'\');
				$(this).nextAll(\'.duplicateShowContent\').html(\'\');
				$(this).nextAll(\'.duplicateShowCTA\').hide();
				if(scrollTop==0)
					scrollTop=$(this).offset().top;					
				error=error+1;	
			}
            /* *** added on  10.12.2015 *** */
            //  elseif condition when the catregory if weather//
            else if((category_name == \'weather\' || category_name == \'Weather\') && (c.words < 15 || c.words >40) ){
                $(this).parent().parent().removeAttr("class");
                $(this).parent().parent().addClass(\'stencilError\');
                $(this).nextAll(\'.missingToken\').html(\'Your text should contain at least 15 words and 40 max\');
                error=error+1;
                if(scrollTop==0)
                    scrollTop=$(this).offset().top;

            }
            else if((category_name != \'weather\' && category_name != \'Weather\') && (c.words < 20 || c.words >40) ){
				$(this).parent().parent().removeAttr("class");
				$(this).parent().parent().addClass(\'stencilError\');
				$(this).nextAll(\'.missingToken\').html(\'Your text should contain at least 20 words and 40 max\');
				error=error+1;
				if(scrollTop==0)
					scrollTop=$(this).offset().top;
			}
			else{
				$(this).parent().parent().removeAttr("class");
				$(this).parent().parent().addClass(\'isValidated\');						
				$(this).nextAll(\'.missingToken\').html(\'\');
				$(this).nextAll(\'.duplicateContentAlert\').html(\'\');
				$(this).nextAll(\'.duplicateShowContent\').html(\'\');
				$(this).nextAll(\'.duplicateShowCTA\').hide();
			}
		}
	});
	$("#approve,#disapprove,#closed").click(function(){
		error=0;
		scrollTop=0;
        var reasons_count = 0;
		var msg=\'\';
		var id_name=$(this).attr("id");    
		var btn = $(this);
		if(id_name==\'approve\')
		{
			$("[id^=stencil_text_]" ).each(function(u) {
				var stencil_text=$(this).val();	
				$(this).blur();				
			});			
			if(scrollTop)
			{
				$(\'html, body\').animate({
					scrollTop: scrollTop
				}, 500);
			}
			//if(error==0)
			//{
			var	valid_comments=$("#valid_comments");
			var comments=valid_comments.val();
				comments = comments.replace(/(<([^>]+)>)/ig,"");
				comments = comments.replace(/&nbsp;/gi,"");
			
			if($.trim(comments).length <1 || comments=="")
			{
				msg+="Please add a comment<br>";
				error++;
			}	
            var score = [];
            $(".precision_valid").each(function(i){
                reasons_count++;
                if($(this).text() != \'\')
                    score.push($(this).text());

			});
            if(score.length != reasons_count)
            {
                msg+="Please give the writer a rating<br>";
                error++;
			}
			if(error==0){
					$("#alert_approve_placeholder").hide();
					btn.button(\'loading\');
					btn.html(\'Checking plagiarism \'+load_img);
					$("#stencilModal").modal(\'show\');
					$(\'.nav li\').not(\'.active\').addClass(\'disabled\');
					$(\'.nav li\').not(\'.active\').find(\'a\').removeAttr("data-toggle");
					var targerURL=\'/ebooker/check-stencil-plagarism\';
					var stencilData=$(\'#v_corrector_form\').serialize();
					//alert(stencilData);
					$.post(targerURL,stencilData,function(result){
						var output=$.parseJSON(result);
						var plag_error_count=0;
						var positionTop=0;
						//console.log(output);
						if(output.status==\'plag_error\')
						{
							plag_error_count=plag_error_count+1;
							$("#plag_error").html(output.error_msg);
							$("#plag_error").show();
						}
						else
						{
							$("#plag_error").html(\'\');
							$("#plag_error").hide();
							$.each(output,function(i,stencil){							
								var stencild_id=$("#stencil_text_"+i);
								var plagarised_text=\'<p>\'+stencil.text+\'</p>\';
								var matched_version=stencil.version;
								var plagarised=stencil.plagarised;
								stencild_id.nextAll(\'.duplicateShowCTA\').hide(); //hide plag URL by default
								if(plagarised==\'yes\')
								{
									var missing_text=\'\';
									stencild_id.parent().parent().removeAttr("class");
									stencild_id.parent().parent().addClass(\'duplicateContent\');	
									if(matched_version==\'db\')								
										missing_text=\'<label>We found plaginarism with Internal DB :</label> \'+plagarised_text;
									else if(matched_version==\'web\')	
									{
										plagarised_url=stencil.url;	
										stencild_id.nextAll(\'.duplicateShowCTA\').show();
										stencild_id.nextAll(\'.duplicateShowCTA\').attr(\'href\',plagarised_url);
										missing_text=\'<label>We found plaginarism with this version :</label> \'+plagarised_text;
									}	
									else
										missing_text=\'<label>We found plaginarism with version\'+matched_version+\' :</label> \'+plagarised_text;
									stencild_id.nextAll(\'.duplicateContentAlert\').html(\'We cannot validate this version as it is considered as a duplicate one. Please modify it\');
									stencild_id.nextAll(\'.duplicateShowContent\').html(missing_text);
									plag_error_count=plag_error_count+1;
									if(positionTop==0)
										positionTop=stencild_id.offset().top;	
								}
								else{
									stencild_id.parent().parent().removeAttr("class");
									stencild_id.parent().parent().addClass(\'isValidated\');						
									stencild_id.nextAll(\'.missingToken\').html(\'\');
									stencild_id.nextAll(\'.duplicateContentAlert\').html(\'\');
									stencild_id.nextAll(\'.duplicateShowContent\').html(\'\');
								}
							});
						}	
						if(positionTop)
						{
							$(\'html, body\').animate({
								scrollTop: positionTop
							}, 500);
						}
						error=plag_error_count;
						//console.log(error);
						$("#approve").button(\'reset\');
						$("#stencilModal").modal(\'hide\');
						
						$(\'.nav li\').not(\'.active\').removeClass(\'disabled\');
						$(\'.nav li\').not(\'.active\').find(\'a\').attr("data-toggle","tab")
						if(error==0)
				$("#v_corrector_form").submit();
					});		
            }
				else{
					$("#alert_approve_placeholder").show();
				bootstrap_alert.error(\'alert_approve_placeholder\',msg);
				}	
			//}	
		}
		else if(id_name==\'disapprove\')
		{
			var artId = $(\'#article_id\').val();
			var partId = $(\'#cparticipation_id\').val(); 
			var	refuse_comments=$("#refuse_comments");
			var comments=refuse_comments.val();
				comments = comments.replace(/(<([^>]+)>)/ig,"");
				comments = comments.replace(/&nbsp;/gi,"");
							
			if($.trim(comments).length <1 || comments=="")
			{
				msg+="Please add a comment<br>";
				error++;
			}					
			
			var $b =$("[id^=refuse_template_]");// $(\'input[name=refuse_template[]]\');
			var countselected = $b.filter(\':checked\').length; 
			if(countselected == 0)
			{				
				msg+=\'Please select atleast one reason why you are requesting for recovery or refusal\';
				bootbox.alert("Please select atleast one reason why you are requesting for recovery or refusal");
				error++;
			}
			if(error==0)	
			{
				var target_page = "/Contrib/getconfirmbox?artId="+artId+"&crtpartiId="+partId+"&button=disapprove";
				 $.post(target_page, function(data){   //alert(data);
					//var r=confirm(data);
					$("#confirmDiv").confirmModal({	
						heading: \'Alert\',
						body: data,
						callback: function () {
							$("#d_corrector_reparticipation").val(\'yes\');
							$("#r_corrector_form").submit();
						}
					});
				});
			}
			else
				bootstrap_alert.error(\'alert_disapprove_placeholder\',msg);				
		
		}
		else if(id_name==\'closed\')
		{
			var artId = $(\'#article_id\').val();
			var partId = $(\'#cparticipation_id\').val(); 
			 
			var	close_comments=$("#close_comments");
			var comments=close_comments.val();
				comments = comments.replace(/(<([^>]+)>)/ig,"");
				comments = comments.replace(/&nbsp;/gi,"");
							
			if($.trim(comments).length <1 || comments=="")
			{
				msg+="Please add a comment<br>";
				error++;
			}
            var score = [];
            $(".precision_close").each(function(i){
                reasons_count++;
                if($(this).text() != \'\')
                    score.push($(this).text());
            });
			/*var score = $.map($(\'#c_corrector_form input:hidden[name^="entity_score"]\'),function(i) {
				return (i.value);
			});
			if(!score || score==\'\')
			{
				msg+=\'Merci de donner une note au r&eacute;dacteur (sous sa fiche profil &agrave; droite)<br>\';
				error++;
			}*/
            if(score.length != reasons_count)
            {
                msg+="Please give the writer a rating (top right in his/her profile page)<br>";
                error++;
            }
			
			 
			if(error==0)	
			{ 
				 var target_page = "/Contrib/getconfirmbox?artId="+artId+"&crtpartiId="+partId+"&button=closed";
				 $.post(target_page, function(data){   //alert(data);
					//var r=confirm(data);
						$("#confirmDiv").confirmModal({	
						heading: \'Alert\',
						body: data,
						callback: function () {
							$("#c_corrector_reparticipation").val(\'yes\');
							$("#c_corrector_form").submit();
						}
					});				

				});
			}	
			else
				bootstrap_alert.error(\'alert_closed_placeholder\',msg);
			
		}
	});
	
	//refuse template content
	$("[id^=refuse_template_]").die(\'click\').live(\'click\', function() {
			
		var template=$(this).attr(\'id\').split("_");
		var template_id=template[2];
		
		if($(this).is(":checked"))	
		{		
			//alert(template_id);			
			var target_page = "/contrib/get-template-content?template_id="+template_id;
			$.get(target_page, function(data){   //alert(data);
				if(data)
				{
					var	refuse_comments=$("#refuse_comments").val();
					//$("#refuse_comments").html(refuse_comments + data);					
					$(\'.textarea-ask4update\').data("wysihtml5").editor.setValue(refuse_comments + data);
				}					
			});
		}
		else
		{			
			var $currentHtml = $(\'<div>\').append($("#refuse_comments").val());
			$currentHtml.find(\'.template_content_\'+template_id).remove();			
			$(\'.textarea-ask4update\').data("wysihtml5").editor.setValue($currentHtml.html());		
		}
			
	});				
	//close template content
	$("[id^=close_template_]").die(\'click\').live(\'click\', function() {
			
		var template=$(this).attr(\'id\').split("_");
		var template_id=template[2];
		
		if($(this).is(":checked"))	
		{		
			//alert(template_id);			
			var target_page = "/contrib/get-template-content?template_id="+template_id;
			$.get(target_page, function(data){   //alert(data);
				if(data)
				{
					var	close_comments=$("#close_comments").val();
					//$("#close_comments").html(close_comments + data);					
					$(\'.textarea-refuse\').data("wysihtml5").editor.setValue(close_comments + data);
				}					
			});
		}
		else
		{			
			var $currentHtml = $(\'<div>\').append($("#close_comments").val());
			$currentHtml.find(\'.template_content_\'+template_id).remove();			
			$(\'.textarea-refuse\').data("wysihtml5").editor.setValue($currentHtml.html());		
		}
			
	});	
		
		
});
$(".starmarksvalid").click(function(){
    // var marks = [];
    var marks = 0;
    var marksparreason = [];
    var total = 0;
    $(".starmarksvalid").each(function(i) {
        i = i+1;
        // marks.push($("#precision-target"+i).text());
        var selmarks = Number($("#precision-target"+i).text());
        marks += selmarks;
        marksparreason.push($("#reasonId"+i).val() + "|" +selmarks);

        //marksparreason = $("#reasonId"+i).val() + "|" +selmarks + "," + marksparreason;
        total = i ;
    });
    var avgmarks =((marks/total)*2);
    $("#givenmarks").html(avgmarks);
    $("#totalmarks").html("/"+(total*5));
    $("#marksvald").val(avgmarks);
    $("#marksvaldwithreason").val(marksparreason.join(\', \'));

});
$(".starmarksclose").click(function(){
    // var marks = [];
    var marks = 0;
    var marksparreason = [];
    var total = 0;
    $(".starmarksclose").each(function(i) {
        i = i+1;
        // marks.push($("#precision-target"+i).text());
        var selmarks = Number($("#precision-target-close"+i).text());
        marks += selmarks;
        marksparreason.push($("#reasonId-close"+i).val() + "|" +selmarks);

        //marksparreason = $("#reasonId"+i).val() + "|" +selmarks + "," + marksparreason;
        total = i ;
    });
     var avgmarks = ((marks/total)*2);
    $("#givenmarks").html(avgmarks);
    $("#totalmarks").html("/"+(total*5));
    $("#marksclose").val(avgmarks);
    $("#marksclosevaldwithreason").val(marksparreason.join(\', \'));
});



</script>
'; ?>
