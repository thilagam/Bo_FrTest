<?php /* Smarty version 2.6.19, created on 2014-11-17 14:36:37
         compiled from Client/profile.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'Client/profile.phtml', 164, false),array('modifier', 'count', 'Client/profile.phtml', 191, false),array('modifier', 'strlen', 'Client/profile.phtml', 195, false),array('modifier', 'truncate', 'Client/profile.phtml', 202, false),array('modifier', 'ucfirst', 'Client/profile.phtml', 253, false),)), $this); ?>
<?php echo '
<script language="javascript">
	$("#menu_profile").addClass("active");
	
	(function($,W,D)
	{
		var JQUERY4U = {};

		JQUERY4U.UTIL =
		{
			setupFormValidation: function()
			{
				//form validation rules
				$("#profileform").validate({
					onkeyup:false,
					rules: {
						first_name: "required",
						last_name: "required",
						phone_number: "required",
						company_name: "required",
						address: "required",
						city: "required",
						zipcode: "required",
						country: "required",
						rcs: "required",
						vat: "required",
					},
					messages: {
						first_name: "Last name compulsory",
						last_name: "First name compulsory",
						phone_number: "Phone number compulsory",
						company_name: "Please add your company name",
						address: "Address compulsory",
						city: "City/Town compulsory",
						zipcode: "Postcode compulsory (ex: SW10 2NP)",
						country: "Choice of country compulsory ",
						rcs: "RCS compulsory",
						vat: "Please add your VAT number",
					},
					
				});
			}
		}

		//when the dom has loaded setup form validation rules
		$(D).ready(function($) {
			JQUERY4U.UTIL.setupFormValidation();
		});

	})(jQuery, window, document); 
	
	//upload twitter
	/*function uploadtwitterlogo()
	{
		var twt=$("#twitterid").val(); 
		
		if(twt!="")
		{
			$("#fileloading").html(\'<div align="center"><img src="/FO/images/loading-b.gif" /></div>\');
		$.ajax({
				url: "/client/twitterlogo",
				global: false,
				type: "POST",
				data: ({twitter : twt}),
				dataType: "html",
				async:false,
				success: function(msg){ 
					if(msg=="nofile")
					{
						$("#fileloading").html("Invalid Twitter id");
					}
					else
					{
						msg1=msg.split("#");
						var profilepic="/FO/profiles/clients/logos/"+msg1[0]+"/"+msg1[0]+"_global.jpg"+\'?\'+ (new Date()).getTime();
						$("#clientlogo").attr("src",profilepic);
						$("#fileloading").html(\'\');
					}
					return false;
				}
			});
		}
		else
		{
			$("#fileloading").html("Merci d\'ins&eacute;rer un compte twitter valide");
			return false;
		}	
		
	}*/
	

		
</script>

'; ?>
	 

<div class="container">
	<div class="row">
		<div class="span12">
			<h1>My settings</h1>
		</div>
	</div>

	<div class="row">   
		<form name="profileform" id="profileform" method="POST" action="/client/profile"  enctype="multipart/form-data" >
		<div class="span9">
			<section id="profile-form">
				<div class="mod">
					<div class="row-fluid">
						<div class="control-group">
							<label for="" class="control-label span3 offset1"><strong>Contact information</strong></label>    
							<div class="controls span8">
								<label><input type="text" placeholder="Last name" name="first_name" id="first_name"  value="<?php echo $this->_tpl_vars['user_details'][0]['first_name']; ?>
" class="span6"></label>
								<label><input type="text" placeholder="First name" name="last_name" id="last_name" value="<?php echo $this->_tpl_vars['user_details'][0]['last_name']; ?>
" class="span6"></label>
								<label><input type="text" placeholder="Phone number (Inc. Area code)" name="phone_number" id="phone_number" value="<?php echo $this->_tpl_vars['user_details'][0]['phone_number']; ?>
" class="span6"></label>
								<label><input type="text" name="email" id="email" class="span6" disabled value="<?php echo $this->_tpl_vars['user_details'][0]['email']; ?>
"></label>
								<a onclick="modifyusersetting('<?php echo $this->_tpl_vars['user_details'][0]['user_id']; ?>
}');" role="button" data-toggle="modal" data-target="#useSettingModal" style="cursor:pointer;" >Change my password</a>
							</div>
						</div> 
						<div class="control-group">
							<label for="" class="control-label span3 offset1"><strong>Abonnement emails automatiques</strong></label> 
							<div class="controls span8">
								<label class="radio inline"><input type="radio" name="alert_subscribe"  value="yes" <?php if ($this->_tpl_vars['user_details'][0]['alert_subscribe'] == 'yes'): ?>checked<?php endif; ?>> Oui&nbsp;</label>
								<label class="radio inline"><input type="radio" name="alert_subscribe"  value="no" <?php if ($this->_tpl_vars['user_details'][0]['alert_subscribe'] == 'no'): ?>checked<?php endif; ?>> Non</label>
							</div>
						</div>
					</div>
				</div>
				<div class="mod">
					<div class="row-fluid">
						<div class="control-group">
							<label for="" class="control-label span3 offset1"><strong>Company details</strong></label>    
							<div class="controls span8">
								<label><input type="text" name="company_name" id="company_name" value="<?php echo $this->_tpl_vars['user_details'][0]['company_name']; ?>
" class="span6" placeholder="Company name" value="Topito"></label>
								<div class="row-fluid" id="company-logo">
									<div class="span6">
										<!--<label>
											<select name="logotype" id="logotype" class="span12" onChange="switchbrowse(this.value)">
												<option value="twt" >logo du compte twitter</option>
												<option value="file" <?php if ($this->_tpl_vars['user_details'][0]['logotype'] == 'file'): ?>selected<?php endif; ?>>Fichier sur mon poste</option> 
											</select>
										</label>-->
										<label>
											<!--<div id="twitterupload" <?php if ($this->_tpl_vars['user_details'][0]['logotype'] == 'file'): ?>style="display:none;"<?php endif; ?>>
												<input type="text" class="span8 twitter-input" name="twitterid" id="twitterid" value="<?php echo $this->_tpl_vars['user_details'][0]['twitterid']; ?>
">
												<div style="display:inline-block;vertical-align:top;margin-top:-2px"><button class="btn btn-small" type="button" onClick="return uploadtwitterlogo();">Actualiser</button></div>  
												<span id="fileloading" class="error"></span> 
											</div>-->
											<div id="fileupload">  
												<input type="file" class="span9" name="file" id="file">
												<span class="help-block" id="file_name"></span>  
											</div>
										</label>
									</div>
									<div class="span3 offset3">
										<img src="<?php echo $this->_tpl_vars['user_details'][0]['logopath']; ?>
" id="clientlogo" style="padding:5px;">
									</div> 
								</div>
								<label><textarea type="text" placeholder="Address" name="address" id="address" class="span6"><?php echo $this->_tpl_vars['user_details'][0]['address']; ?>
</textarea></label>
								<label><input type="text" placeholder="City/Town" name="city" id="city" value="<?php echo $this->_tpl_vars['user_details'][0]['city']; ?>
" class="span6"></label>
								<label><input type="text" placeholder="Postcode (ex: SW10 2NP)" name="zipcode" id="zipcode" value="<?php echo $this->_tpl_vars['user_details'][0]['zipcode']; ?>
" class="span6"></label>
								<label>
									<select name="country" id="country" class="span6">
										 <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['country_array'],'selected' => $this->_tpl_vars['user_details'][0]['country']), $this);?>

									</select>
								</label>
								<label><input type="text" placeholder="RCS" name="rcs" id="rcs" value="<?php echo $this->_tpl_vars['user_details'][0]['rcs']; ?>
" class="span6"></label>
								<label><input type="text" placeholder="VAT" name="vat" id="vat" value="<?php echo $this->_tpl_vars['user_details'][0]['vat']; ?>
" class="span6"></label>
								<label><input type="text" placeholder="Fax" name="fax_number" id="fax_number" value="<?php echo $this->_tpl_vars['user_details'][0]['fax_number']; ?>
" class="span6"></label>
							</div>
						</div> 
					</div>
				</div>
				<input type="hidden" name="frompage" id="frompage" value="<?php echo $_GET['from']; ?>
" />
				<input type="hidden" name="article" id="article" value="<?php echo $_GET['article']; ?>
" />
			<div class="mod">
				<div class="pull-right">
					<button class="btn inline">Cancel</button> 
					<button type="submit" name="update_profile" value="update" class="btn btn-primary inline"><i class="icon-refresh icon-white"></i> Register</button>
				</div>
			</div>
			</section>
		</div>
		
		<div class="span3">
			<!--  right column  -->
			<aside>
				<div class="aside-bg">
					<div id="quote-ongoing" class="aside-block">
						<h4>ONGOING QUOTES</h4>
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
								<li><b>No quotes</b></li>
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
		</form>
	</div>
</div>


	<?php if ($this->_tpl_vars['writerscount'] > 0): ?>
	<section id="known-users">
		<div class="container">
			<div class="row">
				<h3 class="sectiondivider pull-center"><span>They have collaborated with you !</span></h3>
				
				<?php $_from = $this->_tpl_vars['writers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['writer']):
?>
					<div class="span3">
						<div class="editor-container">
							<a  class="imgframe-large" onclick="loadcontribprofile('<?php echo $this->_tpl_vars['writer']['user_id']; ?>
');" role="button" data-toggle="modal" data-target="#viewContribProfile" style="cursor:pointer;">
								<img src="<?php echo $this->_tpl_vars['writer']['profileimage']; ?>
">
							</a>
							<p class="editor-name"><a onclick="loadcontribprofile('<?php echo $this->_tpl_vars['writer']['user_id']; ?>
');" role="button" data-toggle="modal" data-target="#viewContribProfile" style="cursor:pointer;"><?php echo ((is_array($_tmp=$this->_tpl_vars['writer']['name'])) ? $this->_run_mod_handler('ucfirst', true, $_tmp) : smarty_modifier_ucfirst($_tmp)); ?>
</a></p>
						</div>
					</div>
				<?php endforeach; endif; unset($_from); ?> 
			</div>
		</div>
	</section>
	<?php endif; ?>
 
	<!-- Image profile -->
	<!--<div id="cropprofile" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form method="POST" name="loginform" id="loginform" action="" >
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">IMAGE PROFIL</h3>
		</div>
		<div class="modal-body">
			"Client/Client_Popup_ProfilePic.phtml"
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Annuler</button>
		</div>
		</form>	
	</div>-->

	<!-- contrib profile -->
	<div id="viewContribProfile" class="modal container hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">Writer's profile</h3> 
		</div>
		<div class="modal-body">
			<div id="userprofile">
		
			</div>
		</div>
	</div>
	
	<!-- profile settings modify--> 
	<div id="useSettingModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	</div>
	
	
<?php echo '
<script language="javascript">
 $(function(){
		var btnUpload=$(\'#fileupload\'); 
		var status=$(\'#file_name\');
		new AjaxUpload(btnUpload, {
			action: \'uploadclientgloballogo\',
			name: \'uploadfile\',
			onSubmit: function(file, ext){
				 if (! (ext && /^(jpg|jpeg|png|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text(\'Upload only JPEG, GIF or PNG files\').css(\'color\',\'#FF0000\');
					return false;
				}
				
				status.html(\'<div align="center"><img src="/FO/images/loading-b.gif" /></div>\'); 
			},
			onComplete: function(file, response){//alert(response); 
			//On completion clear the status
				status.text(\'\');
				$(\'#file_name\').html(\'\');
				
				//Add uploaded file to list
				var obj = $.parseJSON(response);
				var approot="/FO/";
				
				if(obj.status=="success"){
					/*$(\'#spec_file_name\').val(file);
					
					$("#cropprofile").modal();
					$("#cropbox").attr("src","");
					$("#cropbox").attr("src",approot+obj.path+obj.identifier+"_crop."+obj.ext+ \'?\' + (new Date()).getTime());
					$("#cropbox").hide();
                    $(\'#cropbox\').Jcrop({
                        aspectRatio: 0,
                        setSelect: [ 60, 110, 210, 170 ],
                        onSelect: updateCoords
                    });*/
			
					var profilepic=approot+obj.path+obj.identifier+"_global."+obj.ext+ \'?\' + (new Date()).getTime();
					$("#clientlogo").attr("src",profilepic);
					
					$(\'.customfile-feedback\').val(file.substr(0,25)+" Uploaded");
				}
				else if(obj.status=="smallfile"){
				
					$(\'#file_name\').html("Error in upload image too small. The image is too small, thank you to upload another.").css(\'color\',\'#FF0000\');
				}
				else{
					$(\'#file_name\').html(\'Error in upload\').css(\'color\',\'#FF0000\');
				}
			}
		});
		jQuery(\'img\').each(function(){
			jQuery(this).attr(\'src\',jQuery(this).attr(\'src\')+ \'?\' + (new Date()).getTime());
		});	
		});
	 
			//image file upload
	$(\'#file\').customFileInput({ 
        button_position : \'left\'
    });
</script>
'; ?>