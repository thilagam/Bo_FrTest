<?php /* Smarty version 2.6.19, created on 2016-03-08 16:18:08
         compiled from Contrib/mission-article-deliver-bnp.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'utf8_decode', 'Contrib/mission-article-deliver-bnp.phtml', 93, false),array('modifier', 'count', 'Contrib/mission-article-deliver-bnp.phtml', 128, false),array('modifier', 'zero_cut', 'Contrib/mission-article-deliver-bnp.phtml', 280, false),)), $this); ?>
<?php echo '
<script src="/FO/script/common/ckeditor/ckeditor.js" xmlns="http://www.w3.org/1999/html"></script>
<script src="/FO/script/common/smoke/smoke.min.js" xmlns="http://www.w3.org/1999/html"></script>
<link rel="stylesheet" href="/FO/script/common/smoke/smoke.css" type="text/css"/>
<link rel="stylesheet" href="/FO/script/common/smoke/themes/gebo.css" type="text/css"/>
<script type="text/javascript">
    $(document).ready(function() {
        CKEDITOR.replaceClass = \'ckeditor\';
    });
    $(\'body\').removeClass(\'homepage\');
    $(\'body\').addClass(\'mission\');
</script>
<style type="text/css">
.btn-success_custom
{
	background-color: #5BB75B;    
    border-width: 0;
    color: #FFFFFF;
	font-weight:600
	background-image: linear-gradient(to bottom, #62C462, #51A351);
    background-repeat: repeat-x;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
}
.btn-success_custom:hover, .btn-success_custom:active, .btn-success_custom.active, .btn-success_custom.disabled, .btn-success_custom[disabled] {
    background-color: #51A351;
    color: #FFFFFF;
}
.btn-success
{
margin-top: 0;
padding:5px 13px;
}
.sample-management-cont{
    background-color: #f8f8f8;
    border-radius: 4px 4px 0 0;
    box-shadow: 0 20px 8px -18px rgba(0, 0, 0, 0.3), 0 1px 0 #fff inset;
}
.topset1{
	margin-top:20px
}
textarea {
  resize: vertical; 
}
    .stencilError{
        border: #FF6600 2px solid;border-radius: 4px 4px 0 0; box-shadow: 0 20px 8px -18px rgba(0, 0, 0, 0.3), 0 1px 0 #fff inset;border-right:0px;
    }
    .isValidated{
        border: #62C462 2px solid;border-radius: 4px 4px 0 0; box-shadow: 0 20px 8px -18px rgba(0, 0, 0, 0.3), 0 1px 0 #fff inset;border-right:0px;
    }
    .cke_bottom{display:none;}

.nav .active .icons{display: block;}
.icons{display: none;}
.danger{color: #FF0000;}
.tabbable-bordered  .tab-content {
    -webkit-border-radius: 0 0 4px 4px;
    -moz-border-radius: 0 0 4px 4px;
    border-radius: 0 0 4px 4px;
    border-width: 0 1px 1px;
    border-color: #ddd;
    border-style: solid;
}
</style>
'; ?>


<div class="container">
<br>
<?php if ($this->_tpl_vars['missionDetails'] | @ count > 0): ?>
	<?php $_from = $this->_tpl_vars['missionDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['details'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['details']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['article']):
        $this->_foreach['details']['iteration']++;
?>
	<ul class="breadcrumb">
		<li><a href="/contrib/home">Home</a> <span class="divider">/</span></li>
		<li><a href="/contrib/ongoing">My contributions</a> <span class="divider">/</span></li>
		<li class="active"><?php echo $this->_tpl_vars['article']['title']; ?>
</li>
	</ul> 
	<?php if ($this->_tpl_vars['article']['status'] == 'disapproved'): ?>
	<div class="alert alert-warning">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<a class="btn-link disabled btn-mini pull-right" href="#">I will not be able to adhere to these deadlines.</a>
		<i class="icon-exclamation-sign"></i> As your articles need rewriting/amendments, your deadline has been extended by <?php echo $this->_tpl_vars['article']['article_resubmit_time_text']; ?>
 to update them. 
	</div>
	<?php endif; ?>
	
	<!-- start, Summary -->
	<section id="summary">
		<div class="row-fluid">
			<div class="span8">
				<?php if ($this->_tpl_vars['article']['ao_type'] == 'premium'): ?>
					<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
						<span class="label label-test-mission"><i class="icon-star icon-white"></i> Staffing</span>
					<?php else: ?>
						<span class="label label-premium">Premium Mission</span>&nbsp;&nbsp;managed by <b><?php echo $this->_tpl_vars['article']['bo_user']; ?>
</b>. <a style="color:#fff;font-weight:bold;text-decoration:underline;" href="/contrib/compose-mail?senduser=<?php echo $this->_tpl_vars['article']['article_id']; ?>
">Contact us !</a>
					<?php endif; ?>
					<h1><?php echo ((is_array($_tmp=$this->_tpl_vars['article']['title'])) ? $this->_run_mod_handler('utf8_decode', true, $_tmp) : smarty_modifier_utf8_decode($_tmp)); ?>
</h1>	
				<?php else: ?>	
					<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
						<span class="label label-test-mission"><i class="icon-star icon-white"></i> Staffing</span>
					<?php else: ?>
						<span class="label label-quote">Freedom Mission</span>
					<?php endif; ?>
					<h1><?php echo $this->_tpl_vars['article']['title']; ?>
</h1>
				<?php endif; ?>	
			</div>
			<div class="span3 stat">
				<p>Delivery date</p>
				<!-- add classname "less24" if time is < 24h  -->
				<!-- p class="num-large less24">Livrée</p-->
				<?php if ($this->_tpl_vars['article']['status'] == 'bid' || $this->_tpl_vars['article']['status'] == 'disapproved' || $this->_tpl_vars['article']['status'] == 'disapprove_client'): ?>
					<p class="num-large less24">
						<span id="dtime_<?php echo $this->_tpl_vars['article']['article_id']; ?>
_<?php echo $this->_tpl_vars['article']['article_submit_expires']; ?>
">
							<span id="dtext_<?php echo $this->_tpl_vars['article']['article_id']; ?>
_<?php echo $this->_tpl_vars['article']['article_submit_expires']; ?>
"><?php echo $this->_tpl_vars['article']['article_submit_expires']; ?>
</span>
						</span>
					</p>
				<?php else: ?>
					<p class="num-large less24" style="font-size:18px"><?php echo $this->_tpl_vars['article']['status_trans']; ?>
</p>
				<?php endif; ?>	
			</div>
						<div class="span1 stat">
				<div class="icon-comment-large new"><a href="#comment" class="scroll" id="comment_count"><?php echo count($this->_tpl_vars['commentDetails']); ?>
</a></div>
			<!--  to active if new comment, add classname "new" -->
			<!--div class="icon-comment-large new"><a href="#comment">3</a></div-->


			</div>
		</div>
	</section>
	<!-- end, summary --> 
 
	<div class="row-fluid"> 
		<div class="span9">
			<section id="stencilWrapper">
				<div class="mod">
					<div class="summary clearfix">
						<h4>PROJECT DETAILS												
							<?php if ($this->_tpl_vars['article']['spec_exists'] == 'yes'): ?>
								<span class="pull-right"><a href="/contrib/download-file?type=clientbrief&article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" class="btn btn-small btn-success"><i class="icon-white icon-circle-arrow-down"></i> Download the client brief</a></span>
							<?php endif; ?>	
						</h4>							
					</div>
				</div>
				<div class="row-fluid">					
					<h3>
                        Vous avez de <?php echo $this->_tpl_vars['textcount']; ?>
 articles master &agrave; r&eacute;&eacute;crire le nombre de fois demand&eacute;. Cliquez sur le nom de la ville (master) sur lequel vous souhaitez travailler. Le master s&#39;affichera dans la fen&ecirc;tre. Les onglets &agrave; gauche affichent le nombre de r&eacute;&eacute;critures &agrave; faire du master (R&eacute;&eacute;criture1, R&eacute;&eacute;criture2, etc.) Cliquez sur chaque r&eacute;&eacute;criture pour acc&eacute;der &agrave; la fen&ecirc;tre o&ugrave; vous devez r&eacute;diger votre r&eacute;&eacute;criture du master. Utilisez les boutons gras, italique, bullet point pour faire la bonne mise en page comme expliquez dans votre document "exemple de mise en page". Une fois la mise en page et toutes vous r&eacute;&eacute;critures faites, cliquez sur Valider pour les soumettre &agrave; la platform Edit-Place (cela peut prendre quelques minutes). Vous pouvez t&eacute;l&eacute;charger le brief &eacute;ditorial, le guide de mise en page et une liste de mots-cl&eacute;s en cliquant sur le bouton vert. Bon travail

                        <!--    Une d&eacute;clinaison est une r&eacute;&eacute;criture d&#39;un article. Merci de r&eacute;diger les d&eacute;clinaisons du texte master ci-dessous en ins&eacute;rant chaque r&eacute;&eacute;criture dans la bonne fen&ecirc;tre en utilisant les boutons gras, italic et bullet point pour faire la bonne mise en page comme expliqu&eacute;e dans votre brief et document "exemple mise en page article". Vous pouvez t&eacute;l&eacute;charger le brief &eacute;ditorial, le guide de mise en page et une liste de mot-cl&eacute; en cliquant sur le bouton vert. Bon travail !
                        -->
                    </h3>
                </div>
								<div class="row-fluid">				
					<form name="stencil-form" id="bnp-form-upload" action="" method="POST">
						<div class="stencilTableWrapper" style="padding:10px;">
                            <h3 class="heading">Ecrivez pour BNP Paribas</h3>
                            <div class="tabbable tabbable-bordered">
                                <ul class="nav nav-tabs">

                                    <?php $_from = $this->_tpl_vars['bnp_city_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key_id'] => $this->_tpl_vars['city_id']):
?>
                                        <li id="li_<?php echo $this->_tpl_vars['key_id']; ?>
" class=" <?php if ($this->_tpl_vars['key_id'] == '0'): ?>active<?php endif; ?>"><a href="#tab_br<?php echo $this->_tpl_vars['city_id']; ?>
" data-toggle="tab"><?php echo $this->_tpl_vars['scitys'][$this->_tpl_vars['city_id']]; ?>
</a></li>
                                    <?php endforeach; endif; unset($_from); ?>
                                    <!--<li class="active"><a href="#tab_br1" data-toggle="tab">Section 1</a></li>
                                    <li><a href="#tab_br2" data-toggle="tab">Section 2</a></li>
                                    <li><a href="#tab_br3" data-toggle="tab">Section 3</a></li>-->
                                </ul>
                                <div class="tab-content">
                                    <?php $_from = $this->_tpl_vars['bnp_city_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key_id'] => $this->_tpl_vars['city_id']):
?>
                                        <div class="tab-pane <?php if ($this->_tpl_vars['key_id'] == '0'): ?>active<?php endif; ?>" id="tab_br<?php echo $this->_tpl_vars['city_id']; ?>
">
                                            <div class="tabbable tabs-left">
                                                <ul class="nav nav-tabs">
                                                    <li class="active"><a href="#tab_lm<?php echo $this->_tpl_vars['key_id']; ?>
" data-toggle="tab">Master<i class="icon-green icon-circle-arrow-right icons"  style="float:right;"></i></a></li>
                                                    <?php $_from = $this->_tpl_vars['bnpDetails'][$this->_tpl_vars['key_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key2'] => $this->_tpl_vars['bnp']):
?>
                                                    <li id="tab_bnp_text_<?php echo $this->_tpl_vars['key2']+2; ?>
" class="">
                                                        <a href="#tab_l<?php echo $this->_tpl_vars['key2']+2; ?>
" data-toggle="tab" >
                                                            R&eacute;&eacute;criture <?php echo $this->_tpl_vars['key2']+1; ?>
<i class="icon-green icon-circle-arrow-right icons"  style="float:right;"></i>
                                                        </a>
                                                    </li>
                                                    <?php endforeach; endif; unset($_from); ?>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab_lm<?php echo $this->_tpl_vars['key_id']; ?>
">
                                                        <div class="" id="sample" style="min-height: 480px;">
                                                            <?php $this->assign('index1', $this->_tpl_vars['bnp_sample_text_id'][$this->_tpl_vars['key_id']]); ?>
                                                            <?php echo $this->_tpl_vars['ssample_text'][$this->_tpl_vars['index1']]; ?>

                                                        </div>
                                                        <div>&nbsp;<span style="float:right;font-weight: bold;" id="count_sample"></span></div>
                                                    </div>
                                                    <?php if ($this->_tpl_vars['bnpDetails'] | @ count > 0): ?>
                                                    <?php $_from = $this->_tpl_vars['bnpDetails'][$this->_tpl_vars['key_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['bnp'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['bnp']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key2'] => $this->_tpl_vars['bnp']):
        $this->_foreach['bnp']['iteration']++;
?>
                                                    <div class="tab-pane" id="tab_l<?php echo $this->_tpl_vars['key2']+2; ?>
">
                                                        <textarea  class="ckeditor" name="bnp_text[]" id="bnp_text_<?php echo $this->_tpl_vars['key2']+2; ?>
" onchange="wordCount(this.val());" ><?php echo $this->_tpl_vars['bnp']; ?>
</textarea>
                                                        <?php echo '
                                                        <style>
                                                            #cke_'; ?>
<?php echo $this->_tpl_vars['key2']+1; ?>
<?php echo '_contents { height:480px !important; }
                                                        </style>
                                                        '; ?>

                                                        <div id="temp_bnp_text_<?php echo $this->_tpl_vars['key2']+2; ?>
">
                                                            <input class="classofmenu" type="hidden" value="li_<?php echo $this->_tpl_vars['key_id']; ?>
">
                                                            <div>&nbsp;<span style="float:right;font-weight: bold;" id="count_bnp_text_<?php echo $this->_tpl_vars['key2']+2; ?>
"></span></div>
                                                            <span class="wordCount"></span>
                                                            <p class="missingToken"></p>
                                                            <p class="duplicateContentAlert"></p>
                                                            <a href="" class="duplicateShowCTA" target="_blank" style="display:none">See version with plagirism	</a>
                                                            <div class="duplicateShowContent"></div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; endif; unset($_from); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; endif; unset($_from); ?>
                                    <?php echo '
                                        <script>
                                            $("#li_0,#tab_br0").addClass(\'active\');
                                        </script>
                                    '; ?>

                                </div>
                                <div id="error_status" class="alert alert-error" style="display:none"><strong>There is an error in one of your content,Please cross verify</strong></div>
                            </div>

						</div>
					
					<!-- call to action set -->
						<div class="pull-center">
							<div class="btn-group">
						<?php if ($this->_tpl_vars['article']['status'] == 'bid' || $this->_tpl_vars['article']['status'] == 'disapproved' || $this->_tpl_vars['article']['status'] == 'disapprove_client' || $this->_tpl_vars['article']['status'] == 'time_out'): ?>
							<button href="/contrib/ask-more-time?ao_type=<?php echo $this->_tpl_vars['article']['ao_type']; ?>
&article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" role="button" data-toggle="modal" data-target="#moretime-ajax" class="btn" rel="tooltip" data-original-title="Ask for a deadline extension"><i class="icon-time"></i><sup>+</sup> Ask for a deadline extension</button>
						<?php endif; ?>	
						<a data-original-title="Aide" rel="tooltip" class="btn" href="/contrib/compose-mail?senduser=111201092609847"><i class="icon-question-sign"></i> Help</a>
					</div>
							
							<?php if ($this->_tpl_vars['article']['status'] == 'bid' || $this->_tpl_vars['article']['status'] == 'disapproved' || $this->_tpl_vars['article']['status'] == 'disapprove_client'): ?>
								<button type="button" class="btn btn-success" name="submit_article" id="submit_article" data-loading-text="Checking plagiarism..." >Send</button>
								<input type="hidden" name="participation_id" value="<?php echo $this->_tpl_vars['participation_id']; ?>
" id="send_participate_id">
								<input type="hidden" name="clientId" value="<?php echo $this->_tpl_vars['clientId']; ?>
" id="clientId">
								<input type="hidden" name="article_id" value="<?php echo $_GET['article_id']; ?>
">
							<?php endif; ?>
				</div>
					</form>
				</div>	
			</section>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/article-comments.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>

		<div class="span3">
		<!--  right column  -->
			<aside>
				<div class="aside-bg">
					<div class="editor-price">
						<p class="quote-price">Royalties :<strong><?php if ($this->_tpl_vars['article']['free_article'] == 'yes'): ?>Free<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['article']['price_user'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['article']['currency']; ?>
;<?php endif; ?></strong></p>
						<?php if ($this->_tpl_vars['article']['ao_type'] != 'premium'): ?>
							<ul class="unstyled stripe">
								<li> Edit-place Commission included : <?php echo $this->_tpl_vars['article']['ep_commission']; ?>
%</li>
								<li>Total price paid by the client  : <?php echo ((is_array($_tmp=$this->_tpl_vars['article']['final_price'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
 &<?php echo $this->_tpl_vars['article']['currency']; ?>
;</li>
							</ul>
						<?php endif; ?>	
					</div> 
					<div id="selected-editor" class="aside-block">
						<div class="editor-container">
							<h4>Your Client</h4>
							<img src="<?php echo $this->_tpl_vars['article']['client_pic']; ?>
" title="<?php echo $this->_tpl_vars['article']['company_name']; ?>
">
							<p class="editor-name"><?php echo $this->_tpl_vars['article']['company_name']; ?>
</p>
							<?php if ($this->_tpl_vars['article']['ao_type'] == 'premium'): ?>
								<a href="/contrib/compose-mail?senduser=110923143523902" class="btn btn-small">Contact us</a>
							<?php else: ?>
								<p>Tel : <?php echo $this->_tpl_vars['article']['phone_number']; ?>
</p> 
								<!--<a href="/contrib/compose-mail?senduser=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" class="btn btn-small">Envoyer un message</a>-->
								<p>Email : <?php echo $this->_tpl_vars['article']['email']; ?>
</p> 
							<?php endif; ?>	
						</div>
					</div>
					<?php if ($this->_tpl_vars['article']['ao_type'] != 'premium'): ?>
					<div class="aside-block" id="liberte-guide" style="margin-top: 40px">
						<h4>Guide du r&eacute;dacteur</h4>
						<div class="pull-center inc"><span class="label label-quote">mission liberte</span></div>
						<p><strong>Avant de d&eacute;marrer... </strong></p>
						<ul>
							<li>Contactez le client par email ou t&eacute;l&eacute;phone d&egrave;s que vous &ecirc;tes s&eacute;lectionn&eacute;(e).     </li>
							<li>Demandez une confirmation du brief, du timing de rendu et du nombre d&rsquo;articles.</li>
						</ul>
						<p><strong>Une fois les articles r&eacute;dig&eacute;s...</strong></p>
						<ul>
							<li>T&eacute;l&eacute;chargez les &eacute;l&eacute;ments sur la plateforme.</li>
							<li>N'envoyez jamais votre travail complet par email au client.</li>
						</ul>
						<hr>
						<p class="pull-center"><a href="/contrib/download-file?type=guide_mission_liberte" class="btn btn-small"><i class="icon-download"></i> T&eacute;l&eacute;charger le guide complet</a></p>
					</div>
					<?php endif; ?>
					<div class="aside-block" id="garantee">
						<h4>Your Guarantees</h4>
						<dl>
							<dt><span class="umbrella"></span>Edit-place is your mediator</dt>
							<dd>In case of disputes/issues (delivery deadline, article rewrite/amendment, refund...)</dd>
							<dt><span class="locked"></span>Secure payment</dt>
							<dd>Our online payment solution guarantees a hassle-free transaction</dd>
						</dl>
					</div>
				</div>
			</aside>  
		</div>
	</div>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>	
</div>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/css/common/bootstrap-wysihtml5.css"></link>
<script src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/common/wysihtml5-0.3.0.min.js"></script>
<script src="<?php echo $this->_tpl_vars['livesite']; ?>
/FO/script/common/bootstrap-wysihtml5.js"></script> 
<?php echo '
<script type="text/javascript">
    var cur_date='; ?>
<?php echo time(); ?>
<?php echo ';
    var category_name="'; ?>
<?php echo $this->_tpl_vars['missionDetails'][0]['category_name']; ?>
<?php echo '";
	var js_date=(new Date().getTime())/ 1000;
	var diff_date=Math.floor(js_date-cur_date);
	$("#menu_ongoing").addClass("active");
	startMissionTimer(\'dtime\',\'dtext\');

	
	/*Ebookers stencils validations*/
	
	var tokensArray = '; ?>
<?php echo $this->_tpl_vars['js_tokens_array']; ?>
<?php echo ';
	var whiteListArray = '; ?>
<?php echo $this->_tpl_vars['js_wl_keywords']; ?>
<?php echo ';
	
	function auto_grow(element) {
		//alert(element.scrollHeight)
		if(element.scrollHeight > 85)
		{
			element.style.height = "100px";
			element.style.height = (element.scrollHeight)+"px";
		}	
	}
	function wordCount(val)
	{
		var wordCount=0;
		if(val==\'\')
		{
			return {
				charactersNoSpaces : 0,
				characters         : 0,
				words              : wordCount,
				lines              : 0
			}
		}	
		else
		{
			/*if(whiteListArray.length>0)
			{
				val=val.replace(/[\\.\\,\\!\\?]/g,\'\');
				var regexp = new RegExp(decodeURI(whiteListArray.join( \'|\' ).replace(/[\\.\\,\\!\\?]/g,\'\')), \'ig\');
				//alert(val+\'--\'+decodeURI(whiteListArray.join( \'|\' )));
				val = val.replace(regexp, \'\');
			}*/		
			val = val.replace(/\\\\s+/gi, \' \');
			if(val==\'\' || val==\' \')
				wordCount=0;
			else
				wordCount=val.trim().replace(/\\\\s+/gi, \' \').split(\' \').length;

            return {
				charactersNoSpaces : val.replace(/\\\\s+/g, \'\').length,
				characters         : val.length,
				words              : wordCount,
				lines              : val.split(/\\\\r*\\n/).length
			}
		}	
	}	
	
	function htmlEntities(str) {
		return String(str).replace(/&/g, \'&amp;\').replace(/</g, \'&lt;\').replace(/>/g, \'&gt;\').replace(/"/g, \'&quot;\');
	}
	
	$(function(){
		/*auto resize of textarea onload*/
		$("[id^=bnp_text_]" ).each(function(u) {
			auto_grow(this);
			if($(this).val())
			{
				var c = wordCount($(this).val());				
				$(this).next(\'.wordCount\').html(c.words +\' Words\');
			}					
		});
		var load_img=\'<img src="/FO/images/loading-b.gif">\';
		
		var error=0;
		var scrollTop=0;
		
		/*add validation only in bid,disaaproved status*/		
		'; ?>

			<?php if ($this->_tpl_vars['article']['status'] == 'bid' || $this->_tpl_vars['article']['status'] == 'disapproved' || $this->_tpl_vars['article']['status'] == 'disapprove_client'): ?>
		<?php echo '
			/*bind focus*/
			$("[id^=bnp_text_]" ).bind(\'focus\',function(u) {
				//$(this).parent().parent().removeAttr("class");
				$(this).parent().parent().addClass(\'isEdited\');				
			});	
			
			/*calculate word count on keyup*/
			$("[id^=bnp_text_]").on(\'input\', function(){
				var c = wordCount($(this).val());				
				$(this).next(\'.wordCount\').html(c.words +\' Words\');
			});	
				
			/*text validation on blur*/
			$("[id^=bnp_text_]" ).bind(\'blur\',function(u) {
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
                    /*else if((c.words < 500 || c.words >550) ){
                        $(this).parent().parent().removeAttr("class");
                        $(this).parent().parent().addClass(\'stencilError\');
                        $(this).nextAll(\'.missingToken\').html(c.words+\' Your text should contain at least 500 words and 550 max\');
                        error=error+1;
                        if(scrollTop==0)
                            scrollTop=$(this).offset().top;

                    }*/
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
        function checkContent(data,obj){
            var obj = $("#"+obj);
            var stencil_text=$(obj).val();
            stencil_text=stencil_text.replace(/\\\\t/gi,\' \');
            stencil_text = stencil_text.replace(/ +(?= )/g,\' \');
            stencil_text=stencil_text.replace(/(\\\\r\\\\n|\\\\n|\\\\r)/gm," ");
            stencil_text=encodeURI(stencil_text);
            stencil_text=stencil_text.replace(\'%E2%80%99\',"\'");
            stencil_text=decodeURI(stencil_text);
            stencil_text=$.trim(stencil_text);
            $(obj).val(stencil_text);
            stencil_text=$(stencil_text).text();
            var ids = "#temp_"+$(obj).attr(\'id\');
            var tab_ids = "#tab_"+$(obj).attr(\'id\');

            //$(ids+\' .missingToken\').html(\'Please enter your text\');
//            console.log(stencil_text);

            var c = wordCount(stencil_text);

            if(stencil_text==\'\')
            {

                //$(obj).parent().parent().removeAttr("class");
                //$(obj).parent().parent().addClass(\'stencilError\');
                $(tab_ids ).addClass(\'stencilError\');
                $("#error_status").show();
                $(ids+\' .missingToken\').html(\'Please enter your text\');
                error=error+1;
                if(scrollTop==0)
                    scrollTop=$(obj).offset().top;
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
//                    $(obj).parent().parent().removeAttr("class");
//                    $(obj).parent().parent().addClass(\'stencilError\');
                    $(tab_ids).removeClass(\'isValidated\');
                    $(tab_ids).addClass(\'stencilError\');
                    $("#error_status").show();
                    var missing_text=\'Missing term: \'+missing_words;
                    $(ids+\' .missingToken\').html(missing_text);
                    $(ids+\' .duplicateContentAlert\').html(\'\');
                    $(ids+\' .duplicateShowContent\').html(\'\');
                    $(ids+\' .duplicateShowCTA\').hide();
                    if(scrollTop==0)
                        scrollTop=$(obj).offset().top;

                    error=error+1;
                }
                else if((c.words < 479  || c.words >550) ){
//                 $(obj).parent().parent().removeAttr("class");
//                 $(obj).parent().parent().addClass(\'stencilError\');
                    $(tab_ids).removeClass(\'isValidated\');
                    $(tab_ids).addClass(\'stencilError\');
                    $("#error_status").show();
                    $(ids+\' .missingToken\').html(\'<span class="danger">\'+c.words+\' Your text should contain at least 479  words and 550 max</span>\');
                     error=error+1;
                     if(scrollTop==0)
                     scrollTop=$(obj).offset().top;
                }
                else{
//                    $(obj).parent().parent().removeAttr("class");
//                    $(obj).parent().parent().addClass(\'isValidated\');
                    $(tab_ids).addClass(\'isValidated\');
                    $(tab_ids).removeClass(\'stencilError\');
                    $("#error_status").hide();
                    $(ids+\' .missingToken\').html(\'\');
                    $(ids+\' .duplicateContentAlert\').html(\'\');
                    $(ids+\' .duplicateShowContent\').html(\'\');
                    $(ids+\' .duplicateShowCTA\').hide();
                }
            }
        }
        CKEDITOR.on(\'instanceCreated\', function(e) {
            e.editor.on(\'contentDom\', function() {
                e.editor.document.on(\'keyup\', function(event) {
                        var myinstances = [];
                        for(var i in CKEDITOR.instances) {
                            if(i !== \'\') {
                                /* this updates the value of the textarea from the CK instances.. */
                                CKEDITOR.instances[i].updateElement();
                                myinstances[CKEDITOR.instances[i].name] = CKEDITOR.instances[i].getData();
                                var data = (myinstances[CKEDITOR.instances[i].name])
                                data=data.replace(/\\\\t/gi,\' \');
                                data = data.replace(/ +(?= )/g,\' \');
                                data=data.replace(/(\\\\r\\\\n|\\\\n|\\\\r)/gm," ");
                                data=encodeURI(data);
                                data=data.replace(\'%E2%80%99\',"\'");
                                data=decodeURI(data);
                                data=$.trim(data);
                                data=$(data).text();
                                var c = wordCount(data);
                                var count_ids = "#count_"+i;
                                $(count_ids).html(c.words);
                            }
                        }
                    }
                );
            });
        });
			$("#submit_article").click(function(e){
				var btn = $(this);
				error=0;
				scrollTop=0;
                var myinstances = [];

//this is the foreach loop
                for(var i in CKEDITOR.instances) {
                    if(i !== \'sample\') {
                        /* this updates the value of the textarea from the CK instances.. */
                        CKEDITOR.instances[i].updateElement();
                        /* this retrieve the data of each instances and store it into an associative array with
                         the names of the textareas as keys... */
                        myinstances[CKEDITOR.instances[i].name] = CKEDITOR.instances[i].getData();
                        var data = (myinstances[CKEDITOR.instances[i].name])
                        checkContent(data,i);
                        //console.log(data);
                    }
                }
				/*$("[id^=bnp_text_]" ).each(function(u) {
					var bnp_text=$(this).val();
					$(this).blur();
				});*/
				//alert(scrollTop);
				if(scrollTop)
				{
					$(\'html, body\').animate({
						scrollTop: scrollTop
					}, 500);
					    }
				//alert(error);

				if(error==0)
				{
					btn.button(\'loading\');
					btn.html(\'Checking plagiarism \'+load_img);
					$("#stencilModal").modal(\'show\');
                    //$(\'#bnp-form-upload\').attr(\'action\',\'/bnp/check-stencil-plagarism\').trigger(\'submit\');
					var targerURL=\'/bnp/check-stencil-plagarism\';
					var stencilData=$(\'#bnp-form-upload\').serialize();
                    //alert(stencilData);
					$.post(targerURL,stencilData,function(result){
                        console.log(result);
                        var output=$.parseJSON(result);
						var plag_error_count=0;
						var positionTop=0;
						//console.log(output);
						if(output.status==\'plag_error\')
						{
                            smoke.alert("D&eacute;sol&eacute;, nous avons rep&eacute;r&eacute; du plagiat dans vos textes. Veuillez reprendre vos r&eacute;&eacute;critures : onglets entour&eacute;s en orange");
                            $("#error_status").show();
                            plag_error_count=plag_error_count+1;
							$("#plag_error").html(output.error_msg);
							$("#plag_error").show();
					    }
						else
						{
                            $("#plag_error").html(\'\');
							$("#plag_error").hide();
                            error_display_flag = false;
							$.each(output,function(i,stencil){
								var stencild_id="#temp_bnp_text_"+(parseInt(i)+parseInt(1));
								var tab_stencild_id="#tab_bnp_text_"+(parseInt(i)+parseInt(1));
								var plagarised_text=\'<p>\'+stencil.text+\'</p>\';
								var matched_version=stencil.version;
								var plagarised=stencil.plagarised;
								$(stencild_id+\' .duplicateShowCTA\').hide(); //hide plag URL by default
								if(plagarised==\'yes\')
								{
                                    $(tab_stencild_id).removeAttr("class");
                                    $(tab_stencild_id).addClass(\'stencilError\');
                                    if(!error_display_flag){
                                        smoke.alert("D&eacute;sol&eacute;, nous avons rep&eacute;r&eacute; du plagiat dans vos textes. Veuillez reprendre vos r&eacute;&eacute;critures : onglets entour&eacute;s en orange");
                                        error_display_flag = true;//turn to true if alert is displayed 1s.
                                    }
                                    $("#error_status").show();
									var missing_text=\'\';
									//stencild_id.parent().parent().removeAttr("class");
									//stencild_id.parent().parent().addClass(\'duplicateContent\');
                                    //$(stencild_id).addClass(\'duplicateContent\');
									if(matched_version==\'db\')
										missing_text=\'<label class="danger">We found plaginarism with Internal DB </label> \';//+plagarised_text;
									else if(matched_version==\'web\')
									{
										plagarised_url=stencil.url;
										$(stencild_id+\' .duplicateShowCTA\').show();
                                        $(stencild_id+\' .duplicateShowCTA\').attr(\'href\',plagarised_url);
										missing_text=\'<label>We found plaginarism with this version </label> \';//+plagarised_text;
									}
									else
										missing_text=\'<label>We found plaginarism with version\'+matched_version+\' </label> \';//+plagarised_text;
                                    $(stencild_id+\' .duplicateContentAlert\').html(\'We cannot validate this version as it is considered as a duplicate one. Please modify it\');
                                    $(stencild_id+\' .duplicateShowContent\').html(missing_text);

									plag_error_count=plag_error_count+1;

//									if(positionTop==0)
//										positionTop=stencild_id.offset().top;
								}
								else{
									//stencild_id.parent().parent().removeAttr("class");
									//stencild_id.parent().parent().addClass(\'isValidated\');
//                                    console.log(tab_stencild_id);
                                    $(tab_stencild_id).removeAttr("class");
									$(tab_stencild_id).addClass(\'isValidated\');
                                    $("#error_status").hide();
                                    $(stencild_id+\' .missingToken\').html(\'\');
                                    $(stencild_id+\' .duplicateContentAlert\').html(\'\');
                                    $(stencild_id+\' .duplicateShowContent\').html(\'\');
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
						$("#submit_article").button(\'reset\');
						$("#stencilModal").modal(\'hide\');

						if(error==0)
                        {
//                            $(\'#bnp-form-upload\').attr(\'action\',\'/bnp/send-stencils\').trigger(\'submit\');
                            var targerURL=\'/bnp/send-stencils\';
                            var stencilData=$(\'#bnp-form-upload\').serialize();
                            $.post(targerURL,stencilData,function(result) {
                                console.log(result);
                                if(result.trim() === \'success\')
                                    window.location.reload();
                                else
                                    smoke.alert("unknow error,please try agian later.");
                            });
                        }

					});
				}
				
			});
		'; ?>
			
			<?php endif; ?>			
		<?php echo '			
});	
	
</script>
'; ?>

<div id = "confirmDiv"></div>
<div id="moretime-ajax" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 id="myModalLabel">Deadline extension request</h3>
</div>
<div class="modal-body">

</div>

</div>

<!--///show loading time for file uploading ///-->
<div id="stencilModal" class="modal hide fade" tabindex="-1" >
    <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">&nbsp;</h3>
       </div>
       <div class="modal-body">
           <h3> Merci d&#39;avoir propos&eacute; vos r&eacute;&eacute;critures.</h3>
				<p>
                    Nous v&eacute;rifions actuellement vos textes, veuillez ne pas quittez cette page, cela peut prendre plusieurs minutes.
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
<script>

    $(\'#reload\').click(function() {
        location.reload();
    });
    $(\'#blackclose\').click(function() {
        location.reload();
    });
   

</script>
'; ?>