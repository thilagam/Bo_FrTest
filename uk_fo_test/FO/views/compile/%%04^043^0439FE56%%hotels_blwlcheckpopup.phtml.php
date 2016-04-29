<?php /* Smarty version 2.6.19, created on 2015-07-14 10:40:31
         compiled from Contrib/hotels_blwlcheckpopup.phtml */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'htmlentities', 'Contrib/hotels_blwlcheckpopup.phtml', 71, false),)), $this); ?>
<div class="modal-header">
    <button type="button" class="close" id="reload" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>R&eacute;sultats des mots/expressions whitelist&eacute;s et blacklist&eacute;s contenus dans votre fichier</h3>
</div>
<div class="modal-body">
<div class="row-fluid">
	<div class="span12">

      <?php if ($this->_tpl_vars['singlefile'] == 'yes'): ?>
         <!--<div class="span12 pull-center">
             <h4 class="clearfix">The black / White list result</h4></p>
         </div>-->
        $$$$file_data$$$
         <!--<table id="bllist" class="table table-bordered">
            <thead>
            <tr>
                <th>Nom du fichier</th>
                <th>Mots-cl&eacute;s interdits</th>
                <th>Mots-cl&eacute;s autoris&eacute;s</th>
                <th>Probl&egrave;mes d'espace</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $this->_tpl_vars['blwloutput']['filename']; ?>
<br><?php echo $this->_tpl_vars['documenttitle']; ?>
</td>
                <td><font color="red"><?php if ($this->_tpl_vars['blwloutput']['blkeyword'] != ''): ?><?php echo $this->_tpl_vars['blwloutput']['blkeyword']; ?>
<?php else: ?>--<?php endif; ?></font></td>
                <td><?php if ($this->_tpl_vars['blwloutput']['wlkeyword'] != ''): ?><?php echo $this->_tpl_vars['blwloutput']['wlkeyword']; ?>
<?php else: ?>--<?php endif; ?></td>
                <td><?php if ($this->_tpl_vars['blwloutput']['nospace'] != ''): ?><?php echo $this->_tpl_vars['blwloutput']['nospace']; ?>
<?php else: ?>--<?php endif; ?></td>
                </td>
            </tr>
            </tbody>
         </table>-->
         <div class="form-group span7 pull-right">
             <?php if ($this->_tpl_vars['crt_participationId'] != ''): ?>
               <button type="button" class="btn btn-warning " id="blackclose" name="blackclose" onclick="refreshModel('<?php echo $this->_tpl_vars['crt_participationId']; ?>
','<?php echo $this->_tpl_vars['articleId']; ?>
');" >Annuler</button>
                <button type="button" class="btn btn-success "  id="proceedcrt" name="proceedcrt" >Valider</button>
                 <input type="hidden" id="crtparticipationId" name="crtparticipationId" value="<?php echo $this->_tpl_vars['crt_participationId']; ?>
" />

             <?php else: ?>
               <button type="button" class="btn btn-warning " id="blackclose" name="blackclose" onclick="" >Annuler</button>
                <button type="button" class="btn btn-success "  id="proceed" name="proceed" >Valider</button>
             <?php endif; ?>

         </div>

            <input type="hidden" id="article_id" name="article_id" value="<?php echo $this->_tpl_vars['article_id']; ?>
" />
            <input type="hidden" id="articlepath" name="articlepath" value="<?php echo $this->_tpl_vars['articlepath']; ?>
" />
            <input type="hidden" id="articlename" name="articlename" value="<?php echo $this->_tpl_vars['articleName']; ?>
" />
            <input type="hidden" id="filenameupload" name="filenameupload" value="<?php echo $this->_tpl_vars['blwloutput']['filename']; ?>
" />
            <input type="hidden" id="outputfilename" name="outputfilename" value="<?php echo $this->_tpl_vars['blwlwcheckoutputfile']; ?>
" />
            <input type="hidden" id="participationId" name="participationId" value="<?php echo $this->_tpl_vars['participationId']; ?>
" />
      <?php else: ?>
        <!--<div class="span12 pull-center">
            <h4 class="clearfix">The black / White list result</h4></p>
        </div>-->
        $$$$file_data$$$
       <!-- <table id="bllist" class="table table-bordered">
            <thead>
            <tr>
                <th>Nom du fichier</th>
                <th>Mots-cl&eacute;s interdits</th>
                <th>Mots-cl&eacute;s autoris&eacute;s</th>
                <th>Probl&egrave;mes d'espace</th>
            </tr>
            </thead>
            <tbody>
            <?php $_from = $this->_tpl_vars['blwloutput']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['blwloutput_name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['blwloutput_name']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['blwloutput']):
        $this->_foreach['blwloutput_name']['iteration']++;
?>
            <?php if ($this->_tpl_vars['blwloutput']['filename'] != ''): ?>
            <tr>
                <td><?php echo $this->_tpl_vars['blwloutput']['filename']; ?>
<br><?php echo $this->_tpl_vars['documenttitle']; ?>
</td>
                <td ><font color="red"><?php if ($this->_tpl_vars['blwloutput']['blkeyword'] != ''): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['blwloutput']['blkeyword'])) ? $this->_run_mod_handler('htmlentities', true, $_tmp) : smarty_modifier_htmlentities($_tmp)); ?>
<?php else: ?>--<?php endif; ?></font></td>
                <td><?php if ($this->_tpl_vars['blwloutput']['wlkeyword'] != ''): ?><?php echo $this->_tpl_vars['blwloutput']['wlkeyword']; ?>
<?php else: ?>--<?php endif; ?></td>
                <td><?php if ($this->_tpl_vars['blwloutput']['nospace'] != ''): ?><?php echo $this->_tpl_vars['blwloutput']['nospace']; ?>
<?php else: ?>--<?php endif; ?></td>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>
            </tbody>
        </table>-->
        <div class="form-group span7 pull-right">
            <?php if ($this->_tpl_vars['crt_participationId'] != ''): ?>
                <button type="button" class="btn btn-warning " id="blackclose" name="blackclose" onclick="refreshModel('<?php echo $this->_tpl_vars['crt_participationId']; ?>
','<?php echo $this->_tpl_vars['articleId']; ?>
');" >Annuler</button>
                <button type="button" class="btn btn-success " id="proceedcrt" name="proceedcrt" onclick="" >Valider</button>
                <input type="hidden" id="crtparticipationId" name="crtparticipationId" value="<?php echo $this->_tpl_vars['crt_participationId']; ?>
" />

            <?php else: ?>
                <button type="button" class="btn btn-warning " id="blackclose" name="blackclose" onclick="" >Annuler</button>
                <button type="button" class="btn btn-success " id="proceed" name="proceed" onclick="" >Valider</button>
            <?php endif; ?>

        </div>
            <input type="hidden" id="article_id" name="article_id" value="<?php echo $this->_tpl_vars['article_id']; ?>
" />
            <input type="hidden" id="articlepath" name="articlepath" value="<?php echo $this->_tpl_vars['articlepath']; ?>
" />
            <input type="hidden" id="articlename" name="articlename" value="<?php echo $this->_tpl_vars['articleName']; ?>
" />
            <input type="hidden" id="filenameupload" name="filenameupload" value="<?php echo $this->_tpl_vars['zipfilename']; ?>
" />
            <input type="hidden" id="outputfilename" name="outputfilename" value="<?php echo $this->_tpl_vars['blwlwcheckoutputfile']; ?>
" />
            <input type="hidden" id="participationId" name="participationId" value="<?php echo $this->_tpl_vars['participationId']; ?>
" />
      <?php endif; ?>
	</div>
</div>
</div>
<?php echo '
<script>

    $(\'#reload\').click(function() {
        location.reload();
    });
    $(\'#blackclose\').click(function() {
        location.reload();
       // $(\'#wlblresult\').hide();
    });
    $(\'#reuploadoption1\').click(function() {
        bootbox.confirm("Are you sure with this action",function(e){
            if (e)
            {
                location.reload();
            }
            else
            {
                return false;
            }
        });
    });
    $(\'#reuploadoption2\').click(function() {
        $(\'.propose\').show();
        $(\'#addnewkeyword\').show();
        $(\'#addnewkeywordsingle\').show();
    });
    $(\'#proceedwithmultiple\').click(function() {
        var arr = [];
        var chks = document.getElementsByName(\'proposewl[]\');//here rr[] is the name of the textbox
        for (var i = 0; i < chks.length; i++)
        {
            if (chks[i].value=="")
            {
                bootbox.alert("Please enter new keywords");
                chks[i].focus();
                return false;
            }else
                arr.push(chks[i].value);
        }
        var target_page = "/Contrib/sendarticlewithwlnewkeyword?nkword="+arr+"&data="+$("#newwlkeywords").serialize();
        $.post(target_page, function(data){   //alert(data);
            var obj = $.parseJSON(data);
            if(obj.status=="success"){
                location.reload();
            }else if(obj.status=="wlnwkwmissing"){
                bootbox.alert("The proposed keyword is not present in your document");
            }

        });
    });
    $(\'#proceed\').click(function() {
         var articlepath = $(\'#articlepath\').val();
         var articleName = $(\'#articlename\').val();
         var participationId = $(\'#participationId\').val();
         var filename = $(\'#filenameupload\').val();
         var outputfilename = $(\'#outputfilename\').val();

            var target_page = "/Contrib/sendarticle-hotels-dev?articlepath="+articlepath+"&outputfilename="+outputfilename
             +"&articleName="+articleName+"&filename="+filename+"&participation_id="+participationId+"&single=yes";
            $.post(target_page, function(data){   //alert(data);
                var obj = $.parseJSON(data);
                //location.reload();
                if(obj.status=="success"){
                    location.reload();
                }else if(obj.status=="wlnwkwmissing"){
                   bootbox.alert("The proposed keyword is not present in your document");
                }

            });
    });
    $(\'#proceedcrt2\').click(function() {
        var articlepath = $(\'#articlepath\').val();
        var articleName = $(\'#articlename\').val();
        var participationId = $(\'#participationId\').val();
        var filename = $(\'#filename\').val();
        var outputfilename = $(\'#outputfilename\').val();
        var crtparticipationId = $(\'#crtparticipationId\').val();
        var article_id = $(\'#article_id\').val();
        var marks = $(\'#marks\').val();
        var marksreasons = $(\'#marksreasons\').val();
        var comments = $(\'#comments\').val();
        //var target_page = "/Contrib/sendcorrectorarticle-hotels-dev?data="+$("#blwlprocess1").serialize();
        var target_page = "/Contrib/sendcorrectorarticle-hotels-dev?articlepath="+articlepath+"&outputfilename="+outputfilename
            +"&articleName="+articleName+"&filename="+filename+"&participation_id="+participationId+"&single=yes&crtparticipation_id="+crtparticipationId
            +"&marks="+marks+"&marksreasons="+marksreasons+"&comments="+comments+"&article_id="+article_id;
        $.post(target_page, function(data){   //alert(data);
            var obj = $.parseJSON(data);
            //location.reload();

        });
    });
    $(\'#proceedcrt\').on(\'click\',function() {
        var articlepath = $(\'#articlepath\').val();
        var articleName = $(\'#articlename\').val();
        var filename1 = $(\'#filenameupload\').val();
        var outputfilename = $(\'#outputfilename\').val();
        var target_page = "/Contrib/sendcorrectorarticle-hotels-dev?data="+$("#v_corrector_form").serialize()+"&outputfilename="+outputfilename
            +"&articlepath="+articlepath+"&articleName="+articleName+"&uploadfilename="+filename1;
			$(\'#proceedcrt\').html(\'Transfert en cours...\');
			$(\'#proceedcrt\').attr(\'disabled\',\'disabled\');
        $.post(target_page, function(data){  // alert(data);
            //var obj = $.parseJSON(data);
            location.reload();

        });
        //$("#v_corrector_form").submit();
    });

</script>    
'; ?>