<?php /* Smarty version 2.6.19, created on 2016-03-09 10:09:08
         compiled from Contrib/contrib-mission-corrector-deliver-bnp.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Contrib/contrib-mission-corrector-deliver-bnp.phtml', 192, false),)), $this); ?>
<link rel="stylesheet" type="text/css" href="/FO/css/common/bootstrap-wysihtml5.css"></link>
<script src="/FO/script/common/wysihtml5-0.3.0.min.js"></script>
<script src="/FO/script/common/bootstrap-wysihtml5.js"></script> 
<script src="/FO/script/common/locales/bootstrap-wysihtml5.fr-FR.js"></script>
<!--  ratings -->
<script type="text/javascript" charset="utf-8" src="/FO/script/common/jquery.raty.min.js"></script>
<script src="/FO/script/common/ckeditor/ckeditor.js" xmlns="http://www.w3.org/1999/html"></script>
<script src="/FO/script/common/smoke/smoke.min.js" xmlns="http://www.w3.org/1999/html"></script>
<link rel="stylesheet" href="/FO/script/common/smoke/smoke.css" type="text/css"/>
<link rel="stylesheet" href="/FO/script/common/smoke/themes/gebo.css" type="text/css"/>
<?php echo '
<script type="text/javascript">
    $(document).ready(function() {
        CKEDITOR.replaceClass = \'ckeditor\';
    });
	var tokensArray = '; ?>
<?php echo $this->_tpl_vars['js_tokens_array']; ?>
<?php echo ';
	var whiteListArray = '; ?>
<?php echo $this->_tpl_vars['js_wl_keywords']; ?>
<?php echo ';
	
	/*Ebookers stencils validations*/
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
				//var stencil_text=encodeURI(val);
				var regexp = new RegExp(decodeURI(whiteListArray.join( \'|\' )), \'ig\');
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
		/*toggle blocks*/
		$(".boxTitle a > i").click(function(){
			$(this).toggleClass(\'icon-chevron-down\');
			$(this).parents(".boxTitle").toggleClass(\'boxtitleToggled\');			
			$(this).parents(".boxTitle").next().toggle();
		});
		
		/*auto resize of textarea onload*/
		$("[id^=stencil_text_]" ).each(function(u) {
			auto_grow(this);
			if($(this).val())
			{
				var c = wordCount($(this).val());				
				$(this).next(\'.wordCount\').html(c.words +\' Words\');
			}
		});
		/*rating function*/
		$("[id^=starval]" ).each(function(u) {			
			var index=parseInt(u+1);
			$(this).raty({
				scoreName : \'entity_score\',
				number    : 5,
				path: \'/FO/images/\',			  
				target     : \'#precision-target\'+index,
				targetKeep : true,
				targetType : \'number\'
			});
		});	
		$("[id^=starclose]" ).each(function(u) {
			var index=parseInt(u+1);
			$(this).raty({
				scoreName : \'entity_score\',
				number    : 5,
				path: \'/FO/images/\',
				//  score     : '; ?>
<?php echo $this->_tpl_vars['s1marks'][0]; ?>
<?php echo ',
				target     : \'#precision-target-close\'+index,
				targetKeep : true,
				targetType : \'number\'
			});			
		});
	});	
</script>
<style type="text/css">
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
	<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">&times;</button>
		<i class="icon-ok icon-white"></i> Congratulations <?php echo $this->_tpl_vars['client_email']; ?>
 ! You have been selected as a writer for this mission. Please respect deadlines. 
	</div>
	<!-- start, Summary -->
	<section id="summary">
		<div class="row-fluid">
			<div class="span7">
				<?php if ($this->_tpl_vars['article']['missiontest'] == 'yes'): ?>
					<span class="label label-test-mission">Proofreading Test Mission</span>
				<?php else: ?>
					<span class="label label-correction">Proofreading Mission</span>
				<?php endif; ?>
				<h1><?php echo $this->_tpl_vars['article']['title']; ?>
</h1>
			</div>
			<div class="span2 stat">
				<p>Delivery deadline</p>
				<!-- add classname "less24" if time is < 24h  -->
				<!-- p class="num-large less24">Livr�e</p-->
				<?php if ($this->_tpl_vars['article']['status'] == 'bid' || $this->_tpl_vars['article']['status'] == 'disapproved'): ?>
					<p class="num-large less24">
						<span id="dtime_<?php echo $this->_tpl_vars['article']['article_id']; ?>
_<?php echo $this->_tpl_vars['article']['corrector_submit_expires']; ?>
">
							<span id="dtext_<?php echo $this->_tpl_vars['article']['article_id']; ?>
_<?php echo $this->_tpl_vars['article']['corrector_submit_expires']; ?>
"><?php echo $this->_tpl_vars['article']['corrector_submit_expires']; ?>
</span>
						</span>
					</p>
				<?php else: ?>
					<p class="num-large less24" style="font-size:18px"><?php echo $this->_tpl_vars['article']['status_trans']; ?>
</p>
				<?php endif; ?>	
			</div>
			<div class="span2 stat">
				<p>Rate</p>
				<p class="num-large"><?php echo $this->_tpl_vars['article']['price_corrector']; ?>
 
				<?php if ($this->_tpl_vars['article']['currency'] == 'euro'): ?>
						&euro;
					<?php else: ?>
						&pound;
					<?php endif; ?>
				</p>
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
			<?php if ($this->_tpl_vars['product_type'] == 'translation'): ?>
				<section id="ebStencilTranslation">
			<?php else: ?>	
				<section id="stencilWrapper">
			<?php endif; ?>
				<div class="mod">
					<div class="summary clearfix">
						<h4>PROJECT DETAILS
							<?php if ($this->_tpl_vars['article']['spec_exists'] == 'yes'): ?>
								<span class="pull-right"><a href="/contrib/download-file?type=correctionbrief&article_id=<?php echo $this->_tpl_vars['article']['article_id']; ?>
" class="btn btn-small btn-success"><i class="icon-white icon-circle-arrow-down"></i> T&eacute;l&eacute;charger le brief client</a></span>
							<?php endif; ?>
						</h4>
					</div>
				</div>
                <div class="row-fluid">
					<h3>
                        Vous avez des r&eacute;&eacute;critures d&#39;articles master &agrave; corriger. Cliquez sur le master (nom de la ville) sur lequel vous souhaitez travaillez. Le master s&#39;affichera dans la fen&ecirc;tre. Vous verrez &agrave; côt&eacute; de la fen&ecirc;tre le nombre de r&eacute;&eacute;criture que vous devez corriger (R&eacute;&eacute;criture1, R&eacute;&eacute;criture2, etc.) Cliquez sur chaque r&eacute;&eacute;criture pour acc&eacute;der &agrave; chaque r&eacute;&eacute;criture. Apportez vos corrections. Utilisez les boutons gras, italique, bullet point pour faire la bonne mise en page comme expliquez dans votre document "exemple de mise en page". Vous pouvez t&eacute;l&eacute;charger le brief &eacute;ditorial, le guide de mise en page et une liste de mots-cl&eacute;s en cliquant sur le bouton vert. Bon travail !
                        <!--    Une d&eacute;clinaison est une r&eacute;&eacute;criture d&#39;un article. Merci de corriger les d&eacute;clinaisons du texte master ci-dessous. Utilisez les boutons gras, italic et bullet point pour faire la bonne mise en page comme expliqu&eacute;e dans votre brief et document "exemple mise en page article". Vous pouvez t&eacute;l&eacute;charger le brief &eacute;ditorial, le guide de mise en page et une liste de mot-cl&eacute; en cliquant sur le bouton vert. Bon travail !
                        -->
                    </h3>
                </div>
				<?php if ($this->_tpl_vars['product_type'] == 'redaction'): ?>
									<?php endif; ?>
				<?php if ($this->_tpl_vars['article']['status'] == 'bid' || $this->_tpl_vars['article']['status'] == 'disapproved'): ?>
					<div class="row-fluid file-management-cont">
							<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/contrib-mission_corrector-deliver-bnp-popup.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						</div>
				<?php else: ?>
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
                                                <div class="ckeditor" id="sample" style="min-height: 480px;">
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
                        </div>
                    </div>

				<?php endif; ?>
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
					<div id="selected-editor" class="aside-block">
						<div class="editor-container">
							<h4>The Writer</h4>
							<a href="/contrib/public-profile?user_id=<?php echo $this->_tpl_vars['article']['writer']; ?>
" data-target="#viewProfile-ajax" data-toggle="modal" role="button"><img src="<?php echo $this->_tpl_vars['article']['writer_pic_profile']; ?>
" alt="<?php echo $this->_tpl_vars['article']['writer_name']; ?>
"></a>							
							<p class="editor-name"><?php echo $this->_tpl_vars['article']['writer_name']; ?>
</p>
						</div>
					</div>


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
<?php echo '
<script type="text/javascript">
var cur_date=0,js_date=0,diff_date=0;
cur_date='; ?>
<?php echo time(); ?>
<?php echo ';
var category_name="'; ?>
<?php echo $this->_tpl_vars['missionDetails'][0]['category_name']; ?>
<?php echo '";
 //console.log(category_name);
js_date=(new Date().getTime())/ 1000;
diff_date=Math.floor(js_date-cur_date);
	$("#menu_ongoing").addClass("active");
	startMissionTimer(\'dtime\',\'dtext\');	

	$(\'body\').removeClass(\'homepage\');
	$(\'body\').addClass(\'mission\');
</script>
'; ?>

<!-- ***** Modal collections -->
<!-- ajax use start -->
<div id="fix-article" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Proofreading of article</h3>
	</div>
	<div class="modal-body">
	</div>
</div>
	<!-- ajax use start -->
	<div id="viewProfile-ajax" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">Public Profile</h3>
			</div>
		<div class="modal-body">
		</div>
	</div>
	<!-- ajax use end -->