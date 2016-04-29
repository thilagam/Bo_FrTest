<?php /* Smarty version 2.6.19, created on 2016-04-21 14:14:45
         compiled from Contrib/recruitment-participation-challenge1.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Contrib/recruitment-participation-challenge1.phtml', 195, false),array('modifier', 'stripslashes', 'Contrib/recruitment-participation-challenge1.phtml', 239, false),array('modifier', 'zero_cut', 'Contrib/recruitment-participation-challenge1.phtml', 293, false),)), $this); ?>
<?php echo '
<script type="text/javascript">
var cur_date='; ?>
<?php echo time(); ?>
<?php echo ';
var js_date=(new Date().getTime())/ 1000;
var diff_date=Math.floor(js_date-cur_date);
 $(document).ready(function() {			 
    startCountDown();
	calculateReward();
	
	var confirm_message=\'By clicking on "OK", I submit my participation and my informations won\'+\'t be editable in the future. \';
	
	$(\'#recruitment-submit-modal\').click(function(event){
		event.preventDefault();
		var href=$(this).attr(\'href\');
		if(validateRecruitmentChallenge())
		{
			bootbox.confirm(confirm_message,function(e){
				if (e)
				{
					$(\'#playQuizz-ajax\').removeData(\'modal\').modal({
						remote: href
					});
				}
				else
				{
					return false;
				}	
			});			
		}		
    })
	
	
	//price validation
	$("#recruitment-submit").click(function(){		
		if(validateRecruitmentChallenge())
		{			
			bootbox.confirm(confirm_message,function(e){
				if (e)
				{
					$("#recruitment-challenge1").submit();
				}
				else
				{
					return false;
				}	
			});
			
			
		}	
	});	
	
});	

function validateRecruitmentChallenge()
{
	var error=0,top=400,price_min=0,price_max=0,errtype=\'\';
	var obj;
	var msg1=\'\',msg2=\'\';
	
	var writer_price=parseFloat($("#proposed_cost").val().replace(",","."));
	var article_per_weeks=parseInt($("#articles_per_week").val());
	var priceRegex=/^[0-9]+(\\.?[0-9]+)?$/;
	var priceRegex1=/^[0-9]+(,?[0-9]+)?$/;
	
	var recruitment_id=\''; ?>
<?php echo $_GET['recruitment_id']; ?>
<?php echo '\';
	
	//alert(recruitment_id);
	var request = $.ajax({
		url: "/recruitment/get-pricerange",
		type: "POST",
		data: {recruitment_id : recruitment_id},
		async: false,
		dataType: \'json\',
		success: function(data) {
			//alert(data);
			if(data.error=="time_out")
			{
				var errtype="time_out";
				//error=error+1;
				
			}	
			else
			{	
				price_min=parseFloat(data.price_min);
				price_max=parseFloat(data.price_max);
				max_articles_per_contrib=data.max_articles_per_contrib;						  
			}
		}
	});
	//alert(price_min+\'--\'+price_max+\'--\'+writer_price);
	//alert(writer_price<=price_max);
	if((writer_price>=0 && writer_price<=price_max && (priceRegex1.test(writer_price) || priceRegex.test(writer_price))))
	{
		$(\'#proposed_cost\').addClass("valid");
	}
	else
	{
		$(\'#proposed_cost\').addClass("error");
		error=error+1;
		if(errtype=="time_out")
			msg1="Time is out, Sorry";
		else
			msg1="You must indicate a price in the price range<br>";
	}
	
	if(article_per_weeks>0 && article_per_weeks<=max_articles_per_contrib)
	{
		$(\'#articles_per_week\').addClass("valid");
	}
	else
	{
		$(\'#articles_per_week\').addClass("error");
		error=error+1;
		if(errtype=="time_out")
			msg2="Time is out, Sorry";
		else
			msg2="You must specify a valid number of articles";
	}
	
	if(error>0)
	{
		$(\'html, body\').animate( { scrollTop: top }, \'slow\' );
		bootstrap_alert.error(msg1+msg2);
		return false;
	}
	else
	{			
		return true;
	}

}

function calculateReward()
{
	var total_weeks=parseFloat($("#total_weeks").val());
	var price_article=parseFloat($("#proposed_cost").val());
	var article_per_weeks=parseFloat($("#articles_per_week").val());
	//alert(total_weeks+\'--\'+price_article+\'--\'+article_per_weeks);
	var potential_reward=(total_weeks*price_article*article_per_weeks);
	if(isNaN(potential_reward))
		potential_reward=0;
	else
		potential_reward = potential_reward.toFixed(2);
	$("#potential_reward").text(potential_reward);	
}

function startCountDown()
{
 //////////show timer//////////
	$("[id^=time1_]").each(function(i) {
		var article=$(this).attr(\'id\').split("_");
		var ts=article[2];
		$("#time1_"+article[1]+"_"+article[2]).countdown({
			timestamp   : ts,
            diff_date  : diff_date,
			callback    : function(days, hours, minutes, seconds){
				var message = "";
				if(days>0)
					message += days + " day(s)" +" ";
				if(hours>0)	
					message += hours + " h" +" ";
				if(minutes > 0)
					message +=minutes + " mins"+ " ";
				//if(minutes < 1)
					message +=seconds +" s";
				$("#text1_"+article[1]+"_"+article[2]).html(message);
				if(days==0 && hours==0 && minutes==0 && seconds==0)
				{
					//window.location.reload();
				}
			}
		});
	});

}

function downloadTestBrief(recruitment_id)
{
	var download_href="/contrib/download-file?type=recruitment&recruitment_id="+recruitment_id;
	
	window.open(download_href, "_blank");
	setTimeout((function() {
	  window.location.reload();
	}), 250);
}
</script>
'; ?>


<?php if ($this->_tpl_vars['actionmessages'][0]): ?>
	<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">&times;</button>
		<?php echo $this->_tpl_vars['actionmessages'][0]; ?>

	</div>
<?php endif; ?>
		
<?php if (count($this->_tpl_vars['recruitmentDetails']) > 0): ?>
	<?php $_from = $this->_tpl_vars['recruitmentDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['recruit'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['recruit']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['recruit']):
        $this->_foreach['recruit']['iteration']++;
?>	
		<div class="container">
			<section id="challenge">
				<div id="status">
					<ul>
						<?php if ($this->_tpl_vars['recruit']['recruitmentParticipationId']): ?>
						<li class="done"><span><i class="icon-ok"></i></span> Getting started</li>
						  <?php else: ?>
					    <li class="active"><span>1</span> Getting started</li>
						<?php endif; ?>
					  <?php if ($this->_tpl_vars['recruit']['quiz_id']): ?>
						  						  <?php if ($this->_tpl_vars['recruit']['link_quiz'] == 'yes' && $this->_tpl_vars['recruit']['qualified'] == 'no'): ?>
						  <li class="active"><span>2</span> Quiz</li>
						  <?php elseif ($this->_tpl_vars['recruit']['recruitmentParticipationId']): ?>
						    <li class="done"><span><i class="icon-ok"></i></span> Quiz</li>
							<?php else: ?>
							 <li class=""><span>2</span> Quiz</li>
							<?php endif; ?>	
						  <li class="<?php if ($this->_tpl_vars['recruit']['link_quiz'] == 'yes' && $this->_tpl_vars['recruit']['qualified'] == 'yes' && $this->_tpl_vars['recruit']['recruitmentParticipationId']): ?>active<?php endif; ?>"><span>3</span> Article test</li>
						  <li><span>4</span> validation</li>
							<?php else: ?>
						  <li class="<?php if ($this->_tpl_vars['recruit']['recruitmentParticipationId']): ?>active<?php endif; ?>"><span>2</span> Article test</li>
						  <li><span>3</span> validation</li>
							<?php endif; ?>	
				  </ul>
				</div>
				<h1>Apply for this mission</h1><hr>
				<div class="row-fluid">
					<div class="span8">
						<div class="">
							<h2 style="font-weight:300"><?php echo $this->_tpl_vars['recruit']['recruitment_title']; ?>
</h2>
							<h4>
								<span class="muted">Job offer closes in :</span>
							<span class="time" id="time1_<?php echo $this->_tpl_vars['recruit']['recruitment_id']; ?>
_<?php echo $this->_tpl_vars['recruit']['recruit_expires']; ?>
">
								<span id="text1_<?php echo $this->_tpl_vars['recruit']['recruitment_id']; ?>
_<?php echo $this->_tpl_vars['recruit']['recruit_expires']; ?>
"><?php echo $this->_tpl_vars['recruit']['recruit_expires']; ?>
</span>
							</span>							
							</h4>
							<br><br>
							<h4>About the mission</h4>
							<p class="lead" style="line-height: 26px">
								<?php echo ((is_array($_tmp=$this->_tpl_vars['recruit']['editorial_chief_review'])) ? $this->_run_mod_handler('stripslashes', true, $_tmp) : smarty_modifier_stripslashes($_tmp)); ?>

							</p>
							<?php if ($this->_tpl_vars['recruit']['recruitment_file_name']): ?>
							<a href="/contrib/download-file?type=recruitment&recruitment_id=<?php echo $this->_tpl_vars['recruit']['recruit_art_id']; ?>
" class="btn btn-default"><i class="icon-download"></i> Download the Brief</a>
							<?php endif; ?>
							<br><br><hr>
								
									<div class="row-fluid">
								<div class="span4">
									<h4>About the royalties</h4>
									<p class="lead" style="line-height: 26px">
																		<?php echo $this->_tpl_vars['recruit']['turnover']; ?>
 &<?php echo $this->_tpl_vars['recruit']['currency']; ?>
; of royalties to be shared between <?php echo $this->_tpl_vars['recruit']['num_hire_writers']; ?>
 writers. Do you want to be one of them?
									</p>
								</div>
								<div class="span4">
									<h4>Duration</h4>
									<p class="lead" style="line-height: 26px">
										<?php echo $this->_tpl_vars['recruit']['delivery_time_frame']; ?>
 <?php if ($this->_tpl_vars['recruit']['delivery_period'] == 'days'): ?> day(s)<?php else: ?><?php echo $this->_tpl_vars['recruit']['delivery_period']; ?>
<?php endif; ?>*
									</p>
														</div>
								<div class="span4">
									<h4>Company</h4>
									<p class="lead" style="line-height: 26px">
										<img src="<?php echo $this->_tpl_vars['recruit']['client_pic']; ?>
">
									</p>
													</div>
												</div>
							<div class="row-fluid">
								<div class="promote">
									<img width="44" src="<?php echo $this->_tpl_vars['recruit']['bo_user_pic']; ?>
" class="pull-left  img-circle">
									<div class="profile-name pull-left">
										Posted by <?php echo $this->_tpl_vars['recruit']['bo_user']; ?>

										<div class="time">
											<?php echo $this->_tpl_vars['recruit']['time_ago']; ?>

													</div>
												</div>
											</div>
										</div>
											</div>
										</div>
					<div class="span4">
						<form class="form-horizontal" action="/recruitment/save-participation" id="recruitment-challenge1" method="POST" autocomplete="off">
							<input type="hidden" id="recruitment_id" name="recruitment_id" value="<?php echo $_GET['recruitment_id']; ?>
">
							<input type="hidden" id="total_weeks" name="total_weeks" value="<?php echo $this->_tpl_vars['recruit']['total_weeks']; ?>
">
							<div id="apply_form" class="about mod">
								<h3>Your proposition</h3> 
								<p class="text-error"> <?php if ($this->_tpl_vars['recruit']['active_participations']): ?> <?php echo $this->_tpl_vars['recruit']['active_participations']; ?>
 people applied for this job. <?php else: ?> You are the first to apply.<?php endif; ?></p>
								<hr>
								<h4>My royalties</h4>
								<h2><span id="potential_reward">0</span> &<?php echo $this->_tpl_vars['recruit']['currency']; ?>
;</h2>
								<label>Your price for an article &<?php echo $this->_tpl_vars['recruit']['currency']; ?>
; ?</label>
								<div class="input-prepend input-append">
									<span class="add-on"><?php echo ((is_array($_tmp=$this->_tpl_vars['recruit']['price_min'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
</span>
									<input id="proposed_cost" type="text" value="<?php echo $this->_tpl_vars['recruit']['proposed_cost']; ?>
" name="proposed_cost"  class="span8" <?php if ($this->_tpl_vars['recruit']['recruitmentParticipationId']): ?> disabled <?php endif; ?> onkeyup="calculateReward()">
									<span class="add-on"><?php echo ((is_array($_tmp=$this->_tpl_vars['recruit']['price_max'])) ? $this->_run_mod_handler('zero_cut', true, $_tmp, 2) : smarty_modifier_zero_cut($_tmp, 2)); ?>
</span>
									</div>	
								<label>Number of articles you can submit per week ?  </label>
								<input id="articles_per_week" type="text" value="<?php echo $this->_tpl_vars['recruit']['articles_per_week']; ?>
" name="articles_per_week" <?php if ($this->_tpl_vars['recruit']['recruitmentParticipationId']): ?> disabled <?php endif; ?>  onkeyup="calculateReward()" class="span10"  placeholder="<?php echo $this->_tpl_vars['recruit']['max_articles_per_contrib']; ?>
 maximum">
								<br> <br>  
									<div class="row-fluid">
										<div id = "alert_placeholder"></div>
								</div>
								<?php if ($this->_tpl_vars['participationexpired'] != 'yes'): ?>
								<?php if (! $this->_tpl_vars['recruit']['recruitmentParticipationId']): ?>
										<?php if ($this->_tpl_vars['recruit']['link_quiz'] == 'yes' && $this->_tpl_vars['recruit']['quiz_id']): ?>
																						<a class="btn btn-primary btn-large" href="/quiz/participate-recruitment-quiz?recruitment_id=<?php echo $this->_tpl_vars['recruit']['recruitment_id']; ?>
&quiz_identifier=<?php echo $this->_tpl_vars['recruit']['quiz_id']; ?>
" id="recruitment-submit-modal">Validate</a>
										<?php else: ?>
																						<input type="button" class="btn btn-primary btn-large" id="recruitment-submit" value="Validate">
										<?php endif; ?>
										<?php endif; ?>	
								<?php endif; ?>																			
									</div>
							</form>
					</div>
				</div>
				<hr>
							<?php if ($this->_tpl_vars['recruit']['recruitmentParticipationId']): ?>
								<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Contrib/recruitment-participation-challenge2.phtml", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>		
							<?php endif; ?>	
							
			</section>
		</div>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>	