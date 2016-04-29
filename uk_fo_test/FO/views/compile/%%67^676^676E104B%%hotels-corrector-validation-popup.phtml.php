<?php /* Smarty version 2.6.19, created on 2015-07-14 13:06:09
         compiled from Contrib/hotels-corrector-validation-popup.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'Contrib/hotels-corrector-validation-popup.phtml', 254, false),)), $this); ?>
<?php echo '
<script>

$(function() {
    var btnUpload = $(\'.fileinput-button\');
    var status = $(\'#filename\');
    var cparticipation_id = $(\'#cparticipation_id\').val();
    var article_id = $(\'#article_id\').val();
    var client_id = $(\'#client_id\').val();
    var functiontype = $(\'#function\').val();
    var marksvald = $(\'#marksvald\').val();
    var marksvaldwithreason = $(\'#marksvaldwithreason\').val();
    var corrector_comment = $(\'#valid_comments\').val();
    var uploader = new AjaxUpload(btnUpload, {
        action: \'send-corrector-article\',
        name: \'send_corrector_article\',
        data: {
            cparticipation_id: cparticipation_id,
            client_id: client_id,
            article_id: article_id,
            function: functiontype,
            crtvalidcomment :corrector_comment,
            marksvald :marksvald,
            marksvaldwithreason :marksvaldwithreason
        },
        autoSubmit: false,
        onComplete: function (file, response) {  // alert(response);
            //On completion clear the status
            status.html(\'<img src="/images/icon-generic.gif" /> \' + file);
            //alert(response);
            var patt = /not readable/g;
            var result = patt.test(response);
            if (result)
                status.text(\'Lecture de votre fichier impossible\').css({
                    \'color\': \'#fff\',
                    "background": "none repeat scroll 0 0 #f47d31",
                    "border-radius": "10px",
                    "padding": "4px"
                });
            else {
                $("#loading").modal(\'hide\');
                $("#alert_approve_placeholder").hide();
                var obj = $.parseJSON(response); //alert(response);
                if (obj.status == "success") {
                    location.reload();
                }
                else if (obj.status == "file_sent") {
                    //status.text(\'File already sent for this article\').css({\'color\':\'#fff\',"background": "none repeat scroll 0 0 #f47d31","border-radius":"10px","padding": "4px"});
                    location.reload();
                }
                else if (obj.status == "blwlerror") {
                    $(\'#wlblresult\').html(decodeURIComponent(obj.result));
                    $("#wlblresult").modal(\'show\');
                }
                else if (obj.status == "blwlerrormessage") {
                    //status.html(\'your zip or rar file should only contain .doc files (not .docx)\').css({\'color\':\'#fff\',"background": "none repeat scroll 0 0 #f47d31","border-radius":"10px","padding": "4px"});
                    $("#wlblalert").modal(\'show\');
                    $("#wlblalert .modal-body").html("Votre archive zip/rar ne doit contenir que des fichiers .docx");
                    return false;
                }
                else if (obj.status == "docerrormessage") {
                    $("#wlblalert").modal(\'show\');
                    if (obj.result == \'multi\')
                        $("#wlblalert .modal-body").html("Votre archive zip/rar ne doit contenir que des fichiers .docx. Sauvegardez vos fichiers dans le format requis et les transf�rer de nouveau.");
                    else
                        $("#wlblalert .modal-body").html("Votre fichier n\'a pas &eacute;t&eacute; sauvegard&eacute; au format .docx. Merci de sauvegarder votre fichier dans le format requis.");
                    //bootbox.alert("Your file(s) have not been saved in .doc. Please saved your file(s) with the good extension and reupload.");
                    status.html(\'<img src="/images/icon-generic.gif" /> \' + file);
                    return false;
                }
                else {
                    location.reload();
                    // windows.location="/contrib/ongoing";
                }
            }
        }
    });
    $("#approve_hotel").on(\'click\', function (e) { //alert(\'ye\'); alert($(\'#clientId\').val());
        var error = 0;
        var reasons_count = 0;
        var msg = \'\';
        var valid_comments = $("#valid_comments");
        var comments = valid_comments.val();
        comments = comments.replace(/(<([^>]+)>)/ig, "");
        comments = comments.replace(/&nbsp;/gi, "");
        //$("[id^=file_]").each(function(i) {
        var attachment = $(this).val();
        var clientId = $(\'#client_id\').val();
        var partId = $(\'#cparticipation_id\').val();
        var attachment1 = $(\'#file_uploaded\').val();
        //alert(attachment1);
        if (attachment1 != \'yes\') {
            msg += "Merci d\'uploader votre article ou vos articles <br>";
            error++;
        }
        //});
        if ($.trim(comments).length < 1 || comments == "") {
            msg += "Merci d\'ins&eacute;rer un commentaire<br>";
            error++;
        }
        var score = [];
        $(".precision_valid").each(function (i) {
            reasons_count++;
            if ($(this).text() != \'\')
                score.push($(this).text());
        });
        if (score.length != reasons_count) {
            msg += "N\'oubliez pas de renseigner toutes les notes que vous souhaitez donner au r&eacute;dacteur<br>";
            error++;
        }
        if (error == 0) {
            uploader.submit();
            $("#loading").modal(\'show\');
            $(\'html,body\').animate({scrollTop: 10}, \'slow\');
            bootstrap_alert.error(\'alert_approve_placeholder\', \'\');
        }
        else
            bootstrap_alert.error(\'alert_approve_placeholder\', msg);
    });
});
    $(\'#starval1\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
      //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][0]; ?>
<?php echo ',
        target     : \'#precision-target1\',
        targetKeep : true,
        targetType : \'number\'
    });
    $(\'#starval2\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
      //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][1]; ?>
<?php echo ',
        target     : \'#precision-target2\',
        targetKeep : true,
        targetType : \'number\'
    });
    $(\'#starval3\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
      //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][2]; ?>
<?php echo ',
        target     : \'#precision-target3\',
        targetKeep : true,
        targetType : \'number\'
    });
    $(\'#starval4\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
      //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][3]; ?>
<?php echo ',
        target     : \'#precision-target4\',
        targetKeep : true,
        targetType : \'number\'
    });
    $(\'#starval5\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
      //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][4]; ?>
<?php echo ',
        target     : \'#precision-target5\',
        targetKeep : true,
        targetType : \'number\'
    });

    $(\'#starclose1\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
        //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][0]; ?>
<?php echo ',
        target     : \'#precision-target-close1\',
        targetKeep : true,
        targetType : \'number\'
    });
    $(\'#starclose2\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
        //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][1]; ?>
<?php echo ',
        target     : \'#precision-target-close2\',
        targetKeep : true,
        targetType : \'number\'
    });
    $(\'#starclose3\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
        //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][2]; ?>
<?php echo ',
        target     : \'#precision-target-close3\',
        targetKeep : true,
        targetType : \'number\'
    });
    $(\'#starclose4\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
        //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][3]; ?>
<?php echo ',
        target     : \'#precision-target-close4\',
        targetKeep : true,
        targetType : \'number\'
    });

    $(\'#starclose5\').raty({
        scoreName : \'entity_score\',
        number    : 5,
        path: \'/FO/images/\',
        //  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][4]; ?>
<?php echo ',
        target     : \'#precision-target-close5\',
        targetKeep : true,
        targetType : \'number\'
    });
</script>
'; ?>


<?php if ($this->_tpl_vars['missionDetails'] | @ count > 0): ?>
	<?php $_from = $this->_tpl_vars['missionDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['details'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['details']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['details']['iteration']++;
?>
		<ul id="correctTab" class="nav nav-tabs">
			<li class="active"><a href="#upload"><i class="icon-upload"></i> Confirm</a></li>
			<?php if ($this->_tpl_vars['article']['missiontest'] == 'no'): ?>
				<li class=""><a href="#ask4update"><i class="icon-refresh"></i> Request amendment/rewrite</a></li>
				<li class=""><a href="#refuse"><i class="icon-thumbs-down"></i> Definite refusal</a></li>
			<?php endif; ?>
		</ul>

		<div class="tab-content"  style="height:600px;">
			<!--  Validate -->
			<div id="upload" class="tab-pane active">
				<div class="row-fluid" style="padding:0px 0px 0px 0px;">
				<form name="sendarticle" action="/contrib/send-corrector-article" enctype="multipart/form-data" method="POST" id="v_corrector_form">
                    <div class="alert alert-warning span12">
					<!--<i class="icon-info-sign"></i>-->
					<img src="/FO/images/info_24.png" style="float:left"/>
							<p class="span11">Vous souhaitez valider l'article de <strong><?php echo $this->_tpl_vars['article']['writer_name']; ?>
</strong>. Tout d'abord, merci d'<b>envoyer l'article corrig&eacute;</b> en cliquant sur le bouton ci-dessous.<br/>
                        Ensuite, merci de le <b>noter</b> sur chaque crit&egrave;re et de lui <b>laisser un commentaire</b>. Votre commentaire sera relu par nos &eacute;quipes.</p></div>
                    <div class="span12">
						<div id="file-management" class="file-management-cont clearfix">
							<table class="table mod span11 offset1" style="margin-left: 4%">
								<thead>
									<tr>
										<th class="span7">Article to proofread</th>
										<th class="span1">Writer</th>
										<th class="span2">Date</th>
										<th class="span1">Size</th>
										<th class="span2"></th>
                                        <th class="span1">Rapport d'erreurs du r&eacute;dacteur</th>
									</tr>
								</thead>
								<tbody>
								<?php $_from = $this->_tpl_vars['AllVersionArticles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['articledetails'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['articledetails']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['varticle']):
        $this->_foreach['articledetails']['iteration']++;
?>
									<tr>
										<td class="span7"><i class="icon-file"></i> <a href="/contrib/download-version-article?processid=<?php echo $this->_tpl_vars['varticle']['id']; ?>
"><?php echo $this->_tpl_vars['varticle']['article_name']; ?>
</a></td>
										<td class="span2"><?php echo $this->_tpl_vars['varticle']['first_name']; ?>
</td>
										<td class="span2"><?php echo ((is_array($_tmp=$this->_tpl_vars['varticle']['article_sent_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y") : smarty_modifier_date_format($_tmp, "%d/%m/%Y")); ?>
</td>
										<td class="span1 muted"><?php echo $this->_tpl_vars['varticle']['file_size']; ?>
</td>
										<td class="span2"><a href="/contrib/download-version-article?processid=<?php echo $this->_tpl_vars['varticle']['id']; ?>
" data-original-title="T&eacute;l&eacute;charger" rel="tooltip" class="btn btn-small"><i class="icon-download"></i></a></td>
                                        <td class="span1"><?php if ($this->_tpl_vars['varticle']['hotel_blwloutput'] != ''): ?><a  data-original-title="T&eacute;l&eacute;charger WLBL check output" data-hint="download WLBL check output" rel="tooltip" class="btn btn-small" href="/contrib/download-hotels-version?type=hotleblwlcheck&blwlpath=<?php echo $this->_tpl_vars['varticle']['hotel_blwloutput']; ?>
"><i class="icon-download"></i></a><?php endif; ?></td>
									</tr>
								<?php endforeach; endif; unset($_from); ?>
								</tbody>
							</table>

							<div class="span12 pull-center">
								<div class="empty-box"></div>
								<h4>This box is empty</h4>
								<p>Please add the articles that you have proofread for this mission below</p>
                                <input type="hidden" id="client_id" name="client_id" value="<?php echo $this->_tpl_vars['clientId']; ?>
" />
                                <input type="hidden" id="file_uploaded" name="file_uploaded" value="" />
                                    <span class="btn btn-primary fileinput-button">
                                        <i class="icon-plus icon-white"></i>
                                        <span id="filename">Add a File...</span>
                                        <input type="file" class="span12" name="file_<?php echo $this->_tpl_vars['article']['participationId']; ?>
" id="file_<?php echo $this->_tpl_vars['article']['participationId']; ?>
">
                                    </span>

							</div>
						</div>
						<hr>
                            <div class="span3">
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
<?php else: ?>Note globale<?php endif; ?></span>
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
                                    <div class="span12 form-inline"><span class="span6" style="word-wrap:break-word">Note globale</span>
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
								<p>Bonjour, <?php echo $this->_tpl_vars['article']['writer_name']; ?>
</p>

								<textarea name="corrector-comment" id="valid_comments" class="textarea-validate input-block-level" rows="12" placeholder="Ex : Votre article correspondait tout &agrave; fait &agrave; notre demande. N'h&eacute;sitez pas &agrave; postuler &agrave; d'autres annonces."></textarea>
								<p class="clearfix"><i class="icon-asterisk"></i> <strong>Commentaire ajout&eacute; automatiquement</strong><br>
                                    L'&eacute;quipe d'Edit-place vous remercie de votre confiance.</p>
							</div>
                            <div id="alert_approve_placeholder" class="span11"></div>
							<div class="clearfix pull-right span3">
								<button aria-hidden="true" data-dismiss="modal" class="btn" type="button">Annuler</button>
                                <button class="btn btn-primary" id="approve_hotel" name="approve_hotel" type="button">Envoyer</button>
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
				<form name="sendarticle" action="/contrib/send-corrector-article" enctype="multipart/form-data" method="POST" id="r_corrector_form">
					<div class="span12" style="padding:0px 10px 0px 10px;">
                        <div class="alert alert-warning span12">
                            <!--<i class="icon-info-sign"></i>-->
							<img src="/FO/images/info_24.png" style="float:left"/>
							<p class="span11">Vous souhaitez demander une reprise &agrave; <strong><?php echo $this->_tpl_vars['article']['writer_name']; ?>
</strong>.<br/>
                            Merci de <b>s&eacute;lectionner les raisons de votre refus </b>parmi la liste ci-dessous et de <b>compl&eacute;ter</b> si n&eacute;cessaire. Votre commentaire sera relu par nos &eacute;quipes.</p>
                        </div>
                            <div class="span3" style="margin-left:0px">
                                <div class="mod">
                                    <p style="font-size:20px;text-align:center"><b>Raisons du refus</b></p>
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
								<p>Bonjour, <?php echo $this->_tpl_vars['article']['writer_name']; ?>
</p>

								<textarea name="corrector-comment" id="refuse_comments" class="textarea-ask4update input-block-level" rows="12" placeholder="Ex : Vous avez oubli&eacute; de mettre les mots-cl&eacute; correspondant &agrave; votre lot. Merci de reprendre vos articles."></textarea>
								<p class="clearfix"><i class="icon-asterisk"></i> <strong>Commentaire ajout&eacute; automatiquement &agrave; la demande de reprise</strong><br> Vous avez 24 heures pour nous faire parvenir vos articles modifi&eacute;s. Nous vous rappelons que si les consignes ne sont pas respect&eacute;es, nous pouvons vous demander de modifier  votre article de nouveau. Nous faisons jusqu'&agrave; 2 aller/retour. Au troisi&egrave;me refus l'article sera automatiquement remis en ligne sur la plateforme et l'article ne pourra &ecirc;tre r&eacute;tribu&eacute;.</p>
							</div>
							<div id="alert_disapprove_placeholder" class="span11"></div>
							<div class="clearfix pull-right">
								<button aria-hidden="true" data-dismiss="modal" class="btn" type="button">Annuler</button>
								<button class="btn btn-primary" name="disapprove" id="disapprove" type="button">Envoyer</button>
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
				<form name="sendarticle" action="/contrib/send-corrector-article" enctype="multipart/form-data" method="POST" id="c_corrector_form">
					<div class="span12" style="padding:0px 10px 0px 10px;">
                        <div class="alert alert-warning span12"><!--<i class="icon-info-sign"></i>-->
							<img src="/FO/images/info_24.png" style="float:left"/>
							<p class="span11">Vous souhaitez refuser d&eacute;finitivement l'article de <strong><?php echo $this->_tpl_vars['article']['writer_name']; ?>
</strong>.<br/>Merci de le <b>noter</b> sur chaque crit&egrave;re ci-dessous. Merci de lui <b>laisser un commentaire</b> ci-contre. Votre commentaire sera relu par nos &eacute;quipes.</p></div>
                        <div class="span3" style="margin-left:0px">
                            <div class="mod">
                                <div class="editor-container">
                                    <h4><i class="icon-star"></i> Noter le r&eacute;dacteur</h4>
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
<?php else: ?>Note globale<?php endif; ?></span>
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
                                <div class="span12 form-inline" ><span class="span6" style="word-wrap:break-word">Note globale</span>
                                    <div class="span7 starmarksclose pull-right" id="starclose1" ></div>
                                    <input type="hidden" id="reasonId-close1" value="<?php echo $this->_tpl_vars['reasons_key']; ?>
" />
                                    <span id="precision-target-close1" class="precision_close hide"></span>
                                </div>
                                <?php endif; ?>
                                <input type="hidden" id="marksclose" name="marksclose" vlaue="" />
                                <input type="hidden" id="marksclosevaldwithreason" name="marksclosevaldwithreason"  />
                            </div>
                            <!--<div class="mod">
                                <div class="editor-container" style="text-align:left;">
                                    <?php $_from = $this->_tpl_vars['refuseTemplates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['refuseTemplates'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['refuseTemplates']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['template']):
        $this->_foreach['refuseTemplates']['iteration']++;
?>
                                    <label class="checkbox">
                                        <input type="checkbox" name="close_template[]" id="close_template_<?php echo $this->_tpl_vars['template']['identifier']; ?>
" value="<?php echo $this->_tpl_vars['template']['identifier']; ?>
"><?php echo $this->_tpl_vars['template']['title']; ?>

                                    </label>
                                    <?php endforeach; endif; unset($_from); ?>
                                </div>
                            </div>-->
                        </div>
                        <div class="well span8">
                            <div class="icon_corrector pull-right"></div>
                            <p>Bonjour, <?php echo $this->_tpl_vars['article']['writer_name']; ?>
</p>

                            <textarea name="corrector-comment" id="close_comments" class="textarea-refuse input-block-level" rows="12" placeholder="Ex : Vous avez oubli&eacute; de mettre les mots-cl&eacute; correspondant &agrave; votre lot. Merci de reprendre vos articles."></textarea>
                            <p class="clearfix"><i class="icon-asterisk"></i> <strong>Commentaire ajout&eacute; automatiquement</strong><br>
                                L'&eacute;quipe d'Edit-place vous remercie d'avoir fait confiance. Malheureusement la nouvelle version de l'article n'est toujours pas satisfaisante.<br>
                                L'article est remis en ligne sur la plateforme. La pr&eacute;sente production ne vous sera donc pas r&eacute;mun&eacute;r&eacute;e.<br>Cordialement</p>
                        </div>
                        <div id="alert_closed_placeholder" class="span11"></div>
                        <div class="clearfix pull-right">
                            <button aria-hidden="true" data-dismiss="modal" class="btn" type="button">Annuler</button>
                            <button class="btn btn-primary" name="closed" id="closed" type="button">Envoyer</button>
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

		<a class="pull-right btn btn-small disabled anchor-top scroll" href="#brand"><i class="icon-arrow-up"></i></a>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
<!--///show loading time for file uploading ///-->
<div id="loading" class="modal hide fade" tabindex="-1" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 >Analyse du fichier en cours</h3>
    </div>
    <div class="modal-body">
        <h3>Transfert en cours...
            <img src="/FO/images/progressbar.gif"></h3>
    </div>
</div>
<!--///show loading time for file uploading ///-->
<div id="wlblresult" class="modal container hide fade" tabindex="-1" >
</div>
<div id="wlblalert" class="modal hide fade" tabindex="-1" >
    <div class="modal-header">
        <button type="button" id="reload" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Alerte</h3>
    </div>
    <div class="modal-body">
    </div>
    <div class="form-group pull-center">

    </div>
    <div class="span1" style="height:5px;"></div>
</div>
<?php echo '
<script>

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

$(\'#star\').raty({
  scoreName : \'entity_score\',
  number    : 10,
  path: \'/FO/images/\',
  target     : \'#precision-target_c\',
  targetKeep : true,
  targetType : \'number\'
});

$(\'#star-2\').raty({
  scoreName : \'entity_score\',
  number    : 10,
  path: \'/FO/images/\',
  target     : \'#precision-target\',
  targetKeep : true,
  targetType : \'number\'
});


$(function(){

	$(\'input[type=file]\').change(function(e){
			$in=$(this);
        var file=$in.val();
        var ext=getExt(file);
         if (!(ext && /^(docx|zip|rar)$/.test(ext))) {
             // extension is not allowed
             $(\'#filename\').html(\'seuls les fichiers docx,zip,rar sont accept&eacute;s\').css({
             \'color\': \'#fff\',
             "background": "none repeat scroll 0 0 #f47d31",
             "border-radius": "10px",
             "padding": "4px"
             });
             return false;
         }
         else {
                $("#filename").html($in.val());
                $("#file_uploaded").val(\'yes\');
                $(\'html,body\').animate({scrollTop:200}, 500);
                $(\'.tab-content\').animate({scrollTop:550}, 500);
         }
	});

	bootstrap_alert = function() {}
	bootstrap_alert.error = function(div,message) {
		$(\'#\'+div).html(\'<div class="alert  alert-error"><button data-dismiss="alert" class="close" type="button">&times;</button><span><ul>\'+message+\'</ul></span></div>\')
	}

    $("#disapprove,#closed").click(function(){
		var error=0;
        var reasons_count = 0;
		var msg=\'\';
		var id_name=$(this).attr("id");
		if(id_name==\'approve\')
		{
			var	valid_comments=$("#valid_comments");
			var comments=valid_comments.val();
				comments = comments.replace(/(<([^>]+)>)/ig,"");
				comments = comments.replace(/&nbsp;/gi,"");

            /*$("[id^=file_]").each(function(i) {
				var attachment=$(this).val();
                var clientId = $(\'#client_id\').val();

                if(attachment==\'\' || $.trim(attachment).length==0)
                {
                    msg+="Merci d\'uploader votre article ou vos articles <br>";
                    error++;
                }
             });*/
            var attachment1 = $(\'#file_uploaded\').val();
            if(attachment1!= \'yes\' )
            {
                msg+="Merci d\'uploader votre article ou vos articles <br>";
                error++;
            }
			if($.trim(comments).length <1 || comments=="")
			{
				msg+="Merci d\'ins&eacute;rer un commentaire<br>";
				error++;
			}
            var score = [];
            $(".precision_valid").each(function(i){
                reasons_count++;
                if($(this).text() != \'\')
                    score.push($(this).text());

            });
			/*var score = $.map($(\'#v_corrector_form input:hidden[name^="entity_score"]\'),function(i) {
				return (i.value);
			});
			if(!score || score==\'\')
			{
				msg+=\'Merci de donner une note au r&eacute;dacteur (sous sa fiche profil en haut &agrave; droite)<br>\';
				error++;
			}*/
            if(score.length != reasons_count)
            {
                msg+="N\'oubliez pas de renseigner toutes les notes que vous souhaitez donner au r&eacute;dacteur<br>";
                error++;
            }
			if(error==0){
                $("#v_corrector_form").submit();




               // uploader.submit();
            }
            else
                bootstrap_alert.error(\'alert_approve_placeholder\',msg);
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
				msg+="Merci d\'ins&eacute;rer un commentaire<br>";
				error++;
			}

			var $b =$("[id^=refuse_template_]");// $(\'input[name=refuse_template[]]\');
			var countselected = $b.filter(\':checked\').length;
			if(countselected == 0)
			{
				msg+=\'Merci de s&eacute;lectionner au moins une raison pour laquelle vous demandez la reprise  ou le refus dans la colonne de droite\';
				bootbox.alert("Merci de s&eacute;lectionner au moins une raison pour laquelle vous demandez la reprise  ou le refus dans la colonne de droite");
				error++;
			}



			if(error==0)
			{
				var target_page = "/Contrib/getconfirmbox?artId="+artId+"&crtpartiId="+partId+"&button=disapprove";
				 $.post(target_page, function(data){   //alert(data);
					//var r=confirm(data);
					$("#confirmDiv").confirmModal({
						heading: \'Alerte\',
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
				msg+="Merci d\'ins&eacute;rer un commentaire<br>";
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
                msg+="N\'oubliez pas de renseigner toutes les notes que vous souhaitez donner au r&eacute;dacteur<br>";
                error++;
            }
			/*var $b =$("[id^=close_template_]");// $(\'input[name=close_template]\');
			var countselected = $b.filter(\':checked\').length;
			if(countselected == 0)
			{
				msg+=\'Merci de s&eacute;lectionner au moins une raison pour laquelle vous demandez la reprise  ou le refus dans la colonne de droite\';
				bootbox.alert("Merci de s&eacute;lectionner au moins une raison pour laquelle vous demandez la reprise  ou le refus dans la colonne de droite");
				error++;
			}*/

			if(error==0)
			{
				 var target_page = "/Contrib/getconfirmbox?artId="+artId+"&crtpartiId="+partId+"&button=closed";
				 $.post(target_page, function(data){   //alert(data);
					//var r=confirm(data);
						$("#confirmDiv").confirmModal({
						heading: \'Alerte\',
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

function getExt(file){
    return (/[.]/.exec(file)) ? /[^.]+$/.exec(file.toLowerCase()) : \'\';
}

</script>
'; ?>
