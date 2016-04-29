<?php /* Smarty version 2.6.19, created on 2015-08-06 15:05:51
         compiled from Contrib/recruit_contract.phtml */ ?>
<?php echo '
<script type="text/javascript">
$(function(){
		bootstrap_alert = function() {}
		bootstrap_alert.error = function(message) {
            $(\'#alert_placeholder1\').html(\'<div class="alert  alert-error"><button data-dismiss="alert" class="close" type="button">&times;</button><span><ul>\'+message+\'</ul></span></div>\')
        }
		//Recruitment Contract Validation
		$("#recruitment-contract-submit").click(function(){
		
			var errorcount=0;
			var checkedcount=0;
			
			var contract_text=$("#contract").html();
			$("#contract_text").html(contract_text);
			
			
			if($(\'input:radio[name=contract]:checked\').val()==\'yes\')
				$(this).parents("label:first").removeClass("error");
			else if($(\'input:radio[name=contract]:checked\').val()==\'no\')
			{
				checkedcount+=1;
			}
			else
			{
				bootstrap_alert.error(\'Merci de cocher une case\');
					errorcount+=1;
			}			
			
			//alert(errorcount + \'--\'+checkedcount);
			if (checkedcount > 0 && errorcount==0)
			{
				var confirm_message=\'Etes-vous certain(e) de refuser les termes du contrat et de renoncer &agrave; vos participations ?\';
				var heading=\'Confirm\';
				openRecConfirmModal(heading,confirm_message);
				//return false;
			}
			else if(errorcount > 0 && checkedcount<1)
			{
				return false;
			}
			else
			{			
				$("#recruitment-contract").submit();
				return true;
			}
		
		});		
});	
(function ($) {
    $.fn.extend({
        //pass the options variable to the function
        confirmRecModal: function (options) {
            var html = \'<div class="modal" id="confirmContainer"><div class="modal-header"><a class="close" data-dismiss="modal">×</a>\' +
            \'<h3>#Heading#</h3></div><div class="modal-body">\' +
            \'#Body#</div><div class="modal-footer">\' +
            \'<a class="btn btn-primary" id="confirmYesBtn">Confirmer</a>\' +
            \'<a class="btn" id="confirmCancelBtn">Annuler</a></div></div>\';

            var defaults = {
                heading: \'Please confirm\',
                body:\'Body contents\',
                callback : null
            };
            
            var options = $.extend(defaults, options);
            html = html.replace(\'#Heading#\',options.heading).replace(\'#Body#\',options.body);
            $(this).html(html);
            $(this).modal(\'show\');
            var context = $(this); 
            $(\'#confirmYesBtn\',this).click(function(){
                
                $("#recruitment-contract").submit();
				$(context).modal(\'hide\');
            });
			$(\'#confirmCancelBtn\',this).click(function(){
				$(context).modal(\'hide\');
            });
        }
    });

})(jQuery);	
function openRecConfirmModal(heading,message) 
{
	$("#confirmDiv").confirmRecModal({	
		heading: heading,
		body: message		
	});	
}
</script>
'; ?>



<div class="span11">
	<!-- end, status -->
    <h3>Writer's Agreement<div class="pull-right" id="link-print"><a href="#print-contractForm" class="btn btn-small" ><i class="icon-print"></i> Print the contract</a></div></h3>
   
  
	<div class="mod">
		<div class="contractform well well-large" id="contract">
		
		<span>This AGREEMENT IS ENTERED INTO on the <?php echo $this->_tpl_vars['day']; ?>
 of <?php echo $this->_tpl_vars['month']; ?>
 2015</span></p>
		<p>
		<span>BETWEEN</span></p>
		<ol>
			<li><p>
			<span>[Edit-Place.co.uk] Whose Registered office is
			situated at 67-70 Charlotte Road – LONDON EC2A 3PE (the 'Company')</span></p>
		</ol>
		<ol START=2>
			<li><p>
			<span><strong><?php echo $this->_tpl_vars['FirstName']; ?>
 <?php echo $this->_tpl_vars['LastName']; ?>
</strong>
			whose address is situated at <strong><?php echo $this->_tpl_vars['address']; ?>
</strong> ('the Author') </span>
			</p>
		</ol>
		<p>
		<span>RECITALS</span></p>
		<ol TYPE=A>
			<li><p>
			<span>This Agreement sets out the terms by which the Author, as an 
			independent contractor, will provide content to the Company ('the Content'),
			and will receive the payments as set out in this Agreement.</span></p>
			</li>
			<li><p>
			<span>All Content presented to the Company will be reviewed and/or edited 
			by representatives of the Company prior to publication or other use pursuant 
			to the terms of this Agreement and the Company does not guarantee that all 
			or any items of Content submitted will be purchased by the Company.</span></p>
			</li>
			<li><p>
			<span>The Company's editorial staff reserves the right to edit or 
			remove any Content in whole or in part that, upon later review, is 
			determined to be non-compliant with these terms and conditions or any 
			other conditions that have been notified to the Author by e-mail or other 
			method of communication (for example publication on the Company website)</span></p>
			</li>
			<li><p>
			<span>TThe Company's principal business activity is to provide written 
			content to third parties in specific subject areas that have been optimized 
			for SEO (Search Engine Optimisation) purposes.</span></p>
			</li>
		</ol>
		<p>
		<span>This Agreement is effective on the date set out above, and will remain 
		in effect unless terminated in accordance with this Agreement or until a new 
		Agreement is signed.</span></p>
		<p>
		<span>IT IS HEREBY AGREED</span></p>
		<p>
		<span>1. TERMS AND RIGHTS GUARANTEED</span></p>
		<p STYLE="margin-bottom: 0.14in"><span>1.1	</span><span>Pursuant to the considerations set out below in this Agreement, the Author assigns absolutely to the Company with full title guarantee the following rights throughout the world: (a) the entire copyright and all other rights in the nature of copyright subsisting in respect of the Content and future Content; (b) any database right subsisting in the Content and the future Content; (c) all other rights in the Content and future Content of whatever nature, whether now known or created in the future, to which the Author is now, or at any time after the date of this Agreement may be, entitled by virtue of laws in force in the United Kingdom and in any other party of the world, in each case for the whole term including any renewals, reversions, revivals and extensions and together with all related rights and powers arising or accrued, including the right to bring, make, oppose, defend, appeal and obtain relief (and to retain any damages recovered) in respect of any infringement, or any other cause of action arising from ownership, of any of these assigned rights, whether occurring before, on, or after the date of this agreement. </span></p>
		<p STYLE="margin-bottom: 0.14in"><span>The purpose of this assignment is to enable the Company to allow public access to Content on the Internet, and to allow the sale of such information to third party, websites and / or applications of client's of the Company and Author, therefore also grants the following specific exclusive rights:</span></p>
		<p>
		<span>a)	The right to publish the Content in any format or in any media 
		(whether now known or hereafter devised), on its own or with other related 
		material throughout the world;  </span>
		</p>
		<p>
		<span>b)	The right to scan, store and reproduce the Content on any digital 
		network, that is to say, any computer network, internet, WAP, mobile, intranet;</span></p>
		<p>
		<span>c)	The right to reproduce the Content and adapt as electronic publishing, 
		particularly in CD, Photo CD, CD- I, or any other similar process, or to come;</span></p>
		<p>
		<span>d)	The right to reproduce all or part of the Content on and to adapt for 
		reading terminals and digital networks, electronic or optometric, especially on the 
		internet networks and Web information servers, WAP, hard disks of computers, servers, 
		tablets, PDAs, mobile phones and smartphone or any other device capable of storing 
		digital data existing and future;</span></p>
		<p>
		<span>e)	The right to adapt the Content to the search engine optimization rules on the
		Internet, so that the Content will appear in the main search engines based on user queries;</span></p>
		<p>
		<span>f)	The right to communicate and make available to the public the Content or its 
		adaptations, translations into all languages and in all countries, for all distribution 
		processes, words, sounds and images, including digital networks, electronic or optometric,
		especially on the internet networks and applications, iPhone or iPad or any other similar 
		process or future Web information servers, WAP, mobile networks;</span></p>
		<p>
		<span>g)	Any dissemination by any means of telecommunication, including terrestrial,
		satellite, television and by means of a cable distribution;</span></p>
		<p>
		<span>h)	Representation of the Content in whole or in part, in any public event,
		festival, show, event, cultural association, information, professional or commercial;</span></p>
		<p>
		<span>i)	The Company retains the perpetual right to use excerpts of the Content, 
		as well as the Author's name, likeness and approved biography in any materials created 
		to promote the Content in any medium;</span></p>
		<p>
		<span>j)	</span><span>The right to grant licenses to third parties to do any of the above.</span></p>
		<p>
		<span>1.3	This Agreement shall remain in effect until terminated by either party at 
		any time on written notice sent to the other party at the email addresses of record, 
		or until a new Agreement is published.</span></p>
		<p>
		<span>1.4	The Author agrees to adhere to the editorial guidelines and policies 
		issued by the Company from time to time.</span></p>
		<p>
		
		<span>2. MORAL RIGHTS</span></p>
		<p>
		<span>2.1	The Author, being the sole author of the Content, waives absolutely his 
		moral rights arising under the Copyright, Designs and Patents Act 1988 and, so far as 
		is legally possible, any broadly equivalent rights he may have in any territory of 
		the world and shall provide to the Company absolute waivers of all moral rights in 
		each Future Work promptly on its creation. </span>
		</p>
		
		<span>3. WARRANTY AND CONFIDENTIALITY</span></p>
		<p>
		<span>3.1	The Author warrants that s/he is the copyright holder of the Content and 
		the Author has the rights to assign the copyright in the Content to the Company. 
		The Content is the Author's original work, it has not been previously published 
		(in print or electronic form), is not currently under consideration by any third 
		party organization, it will not be submitted for publication to any other party 
		and if published the Company will be the first publisher.  </span>
		</p>
		<p>
		<span>3.2	No part of the Content is copied from other work.</span>
		</p>
		<p>
		<span>3.3	You have obtained all permissions required (for print and electronic use) 
		for any material you have used from other copyrighted publications in the Content.</span>
		</p>
		<p>
		<span>3.4	You have exercised reasonable care to ensure that the Content is accurate and
		does not contain anything which is libelous, or obscene, or infringes on anyone's copyright, 
		right of privacy or other rights or is in anyway unlawful.  </span>
		</p>
		<p>
		<span>3.5	In the case of multi-authored Content the Author warrants that he is authorized
		by the co-authors to enter into this assignment.</span></p>
		<p>
		<span>3.6	The Author agrees to notify the Company of any third party claims against the Content.</span></p>
		<p>
		<span>3.7	The Company is not obliged to display, backup or archive the Content. 
		The creation and maintenance of an original backup version of the Content shall be 
		solely the Author's responsibility.</span></p>
		<p>
		<span>3.8	The warranties provided to the Company may be enforced by any third party to
		whom the Company grants a license to use the Content.</span></p>
		<p>
		<span>3.9	The Author agrees that any information supplied by the Company shall be confidential
		and may not be disclosed at any time for any reason to any person or entity. Such confidential 
		information shall include, but is not limited to, the Website design, documentation, research,
		procedures, marketing timetables, strategies, development plans, contract details, communication,
		and other technical, business, operating and financial information.</span></p>
		
		<p>
		<span>4. COMPENSATION</span></p>
		<p>
		<span>4.1	The Company will pay the Author a fixed sum of </span>
		<span><b>
		<?php if ($this->_tpl_vars['euro_amount']): ?> <?php echo $this->_tpl_vars['euro_amount']; ?>
 Euros <?php endif; ?>
		<?php if ($this->_tpl_vars['euro_amount'] && $this->_tpl_vars['pound_amount']): ?> & <?php endif; ?>
		<?php if ($this->_tpl_vars['pound_amount']): ?> <?php echo $this->_tpl_vars['pound_amount']; ?>
 &pound; (Pounds Sterling)<?php endif; ?></b></span><span> for each item of
		Content selected by the Company for purchase.</span></p>
		<p>
		<span>4.2	Any amounts owed to the Author, shall be
		accumulated until the total exceeds &pound;20 (twenty Pounds Sterling), after which time it will 
		be paid to the Author via PayPal or bank transfer, once content has been validated by the client.</span></p>
		<p>
		<span>4.3	The amount due will be credited to the Author's account, all payments incur a 2% 
		transfer/admin fee on the total invoice amount.  The Author can choose to be paid on the 15th 
		of the following month (this incurs an 8% charge for fast payment), or in the subsequent month 
		(this incurs no additional fees).</span></p>
		<p>
		<span>4.4	The Company will accrue the Author earnings until this minimum sum has been 
		accumulated and that the correct account details are provided.</span></p>
		<p>
		<span>4.5	If the Author has not provided correct account details (valid PayPal address
		or bank transfer details) within one hundred and eighty (180) days of payment becoming due, 
		the Writer's balance may be forfeited to the Company.</span></p>
		<p>
		<span>4.6 	The Author is solely responsible for remitting all taxes, withholdings, 
		duties and charges relating to payment received from the Company.</span></p>

		<p>
		<span>5. INDEMNIFICATION</span></p>
		<p>
		<span>5.1	The Author agrees to indemnify and hold harmless the Company, its officers,
		directors, employees, agents, and customers from claims that any Content submitted by the 
		Author to the Company defames or infringes the rights of any other person, including but
		not limited to any copyright, trademark, other proprietary rights or confidential 
		information of any other person.</span></p>
		<p>
		<span>6. MISCELLANEOUS</span></p>
		<p>
		<span>6.1	This Agreement, and individual instructions provided by the Company set forth
		the entire agreement between the Author and the Company.</span></p>
		<p>
		<span>6.2	These documents supersede all prior written or oral agreements. This Agreement 
		shall be binding upon the Author and may not be assigned by the Author without the prior written 
		consent of the Company, which may be withheld at the Company's discretion. This Agreement shall 
		be binding on the Company, its successors and assigns.</span></p>
		<p>
		<span>6.3	No modification or waiver of this Agreement shall be binding on either party 
		unless agreed to by both parties.</span></p>
		<p>
		<span>6.4	If the Company makes modifications to this Agreement, the Author will be 
		notified immediately upon logging in to the Website and/or by email.</span></p>
		<p>
		<span>6.5	If the Author continues to provide Content to the Company after the changes 
		are effective, the Author agrees to have been deemed to have accepted such changes to the Agreement.</span></p>
		<p>
		<span>6.6	This Agreement shall be governed by the laws of England and Wales and the parties 
		irrevocably agree to the exclusive jurisdiction of THE COURTS OF England and Wales to determine all issues, 
		whether at law or in equity, arising from this Agreement.</span></p>
		
<p>&nbsp;</p>
<p>&nbsp;</p>
		</div>
		<div class="row-fluid">
			<div id ="alert_placeholder1"></div>
			<form class="form-horizontal" method="POST" action="/recruitment/sign-contract" id="recruitment-contract">
				<div class="control-group">
					<label class="control-label span7 offset1" for="contract">I agree to the terms and conditions of the current contract</label>
					<div class="controls span4">
						<label class="radio inline">
							<input type="radio" name="contract" value="yes">
							Yes
						</label>
						<label class="radio inline">
							<input type="radio" name="contract" value="no">
							No
						</label>
					</div>
				</div>
				<hr>
				<div class="control-group">
					<h3 style="text-align:center;line-height: 45px">
						You have <b><?php echo $this->_tpl_vars['recruit']['article_submit_time_text']; ?>
</b> to write your test article and send it back.<br>
						<?php if ($this->_tpl_vars['recruit']['free_article'] == 'yes'): ?>
							<b> This test article is free</b> <br>
							if you are selected for this mission, edit-place will pay you <b><?php echo $this->_tpl_vars['recruit']['proposed_cost']; ?>
 &<?php echo $this->_tpl_vars['recruit']['currency']; ?>
;</b> per article
						<?php else: ?>	
							if you are selected, edit-place will pay you <b><?php echo $this->_tpl_vars['recruit']['proposed_cost']; ?>
 &<?php echo $this->_tpl_vars['recruit']['currency']; ?>
;</b>
						<?php endif; ?>
					</h3>
				</div>
				
				<div class="control-group">					
					<label class="control-label span4"></label>
					<div class="controls span6">
						<a class="btn btn-link" href="/contrib/download-file?type=clientbrief&article_id=<?php echo $_GET['article_id']; ?>
"><i class="icon-download"></i> Download the brief</a>	
						<a class="btn btn-primary" id="recruitment-contract-submit">Validate</a>
					</div>
				</div>
				<input type="hidden" name="participation_id" value="<?php echo $this->_tpl_vars['participation_id']; ?>
">
				<input type="hidden" id="recruitment_id" name="recruitment_id" value="<?php echo $_GET['recruitment_id']; ?>
">
				<textarea name="contract_text" id="contract_text" style="display:none"></textarea>				
			</form>
			<div id = "confirmDiv"></div>
	</div>
</div>
</div>