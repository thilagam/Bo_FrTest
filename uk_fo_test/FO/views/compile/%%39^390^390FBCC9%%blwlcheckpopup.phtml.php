<?php /* Smarty version 2.6.19, created on 2015-03-09 14:18:38
         compiled from Contrib/blwlcheckpopup.phtml */ ?>
<!-- display result for the bl wl check -->
<div class="modal-header">
    <button type="button" class="close" id="reload" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php if ($this->_tpl_vars['blwloutput']['blkeyword'] != '' || $this->_tpl_vars['blblock'] != 'no'): ?>Blacklist Alert<?php else: ?>Whitelist Alert<?php endif; ?></h3>
</div>
<div class="modal-body">
<div class="row-fluid">
	<div class="span12">
      <?php if ($this->_tpl_vars['singlefile'] == 'yes'): ?>
         <?php if ($this->_tpl_vars['blwloutput']['blkeyword'] != ''): ?>
         <div class="span12 pull-center">
             <h4 class="clearfix">We have found blacklisted keywords in your file(s)</h4>
             <p>As per the client's brief, the following words cannot be used in your file(s)</p>
         </div>
         <table id="bllist" class="table table-bordered">
            <thead>
            <tr>
                <th>File Name</th>
                <th>Keywords</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $this->_tpl_vars['blwloutput']['filename']; ?>
<br><?php echo $this->_tpl_vars['documenttitle']; ?>
</td>
                <td><?php echo $this->_tpl_vars['blwloutput']['blkeyword']; ?>
</td>
                </td>
            </tr>
            </tbody>
         </table>
         <div class="form-group span6">
            <button type="button" class="btn btn-info pull-right" id="blackclose" name="blackclose" onclick="" >Close</button>
         </div>
         <?php endif; ?>
         <?php if ($this->_tpl_vars['blwloutput']['wlkeyword'] != ''): ?>
         <div class="span12 pull-center">
            <h4 class="clearfix">Warning ! Main keyword missing or modified</h4>
            <p>As per the client's brief, the main keyword has to appear at least once in each paragraph of your article, but this has not been detected. </p>
		<p>Have you corrected the given main keyword with another one in your articles?</p>
         </div>
         <table id="wllist" class="table table-bordered">
            <thead>
            <tr>
                <th>File Name</th>
                <th>Keywords Missed</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $this->_tpl_vars['blwloutput']['filename']; ?>
</td>
                <td><?php echo $this->_tpl_vars['blwloutput']['wlkeyword']; ?>

                    <input type="text" id="proposewl" class="propose" name="proposewl" placeholder="Enter the corrected keyword" />
                </td>
            </tr>
            </tbody>
         </table>
         <div class="form-group span12">
            <div class="pull-center">
                <button type="button" class="btn btn-info" id="blackclose" name="blackclose" onclick="" >Close</button>
                <button type="button" class="btn btn-success "  id="addnewkeywordsingle" name="addnewkeywordsingle"   >Validate</button>
            </div>
         </div>
         <?php endif; ?>
            <input type="hidden" id="articlepath" name="articlepath" value="<?php echo $this->_tpl_vars['articlepath']; ?>
" />
            <input type="hidden" id="articlename" name="articlename" value="<?php echo $this->_tpl_vars['articleName']; ?>
" />
            <input type="hidden" id="filename" name="filename" value="<?php echo $this->_tpl_vars['blwloutput']['filename']; ?>
" />
            <input type="hidden" id="participationId" name="participationId" value="<?php echo $this->_tpl_vars['participationId']; ?>
" />
      <?php else: ?>
        <form method="POST"  id="newwlkeywords" action="/contrib/sendarticlewithwlnewkeyword/">
          <?php if ($this->_tpl_vars['blblock'] != 'no'): ?>
             <div class="pull-center">
                <h4 class="clearfix">We have found blacklisted keywords in your file(s)</h4>
			<p>As per the client's brief, the following words cannot be used in your file(s)</p>
                
             </div>
             <table id="bllist" class="table table-bordered">
                <thead>
                <tr>
                    <th>File Name</th>
                    <th>Keywords</th>
                </tr>
                </thead>
                <tbody>
                <?php $_from = $this->_tpl_vars['output']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['blword']):
?>
                    <?php if ($this->_tpl_vars['blword']['blkeyword']): ?>
                    <tr>
                        <td><?php echo $this->_tpl_vars['blword']['filename']; ?>
</td>
                        <td><?php echo $this->_tpl_vars['blword']['blkeyword']; ?>
</td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?>
                </tbody>
             </table>
             <div class="form-group span6">
                <button type="button" class="btn btn-info pull-right" id="blackclose" name="blackclose"  >Close</button>
             </div>
          <?php endif; ?>
          <?php if ($this->_tpl_vars['blblock'] == 'no' && $this->_tpl_vars['errormessage'] == ''): ?>
          <div class="pull-center">
            <h4 class="clearfix">Warning ! Main keyword missing or modified</h4>
            <p>As per the client's brief, the main keyword has to appear at least once in each paragraph of your article, but this has not been detected. </p>
		<p>Have you corrected the given main keyword with another one in your articles?</p>
          </div>
          <table id="wllist" class="table table-bordered">
            <thead>
            <tr>
                <th>File Name</th>
                <th>Keywords Missed</th>
            </tr>
            </thead>
            <tbody>
                <?php $_from = $this->_tpl_vars['output']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['wblwords'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['wblwords']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['wblword']):
        $this->_foreach['wblwords']['iteration']++;
?>
                    <?php $this->assign('index', ($this->_foreach['wblwords']['iteration']-1)+1); ?>
                    <?php if ($this->_tpl_vars['wblword']['wlkeyword']): ?>
                    <tr>
                        <td><?php echo $this->_tpl_vars['wblword']['filename']; ?>
</td>
                        <td><?php echo $this->_tpl_vars['wblword']['wlkeyword']; ?>

                            <input type="text" placeholder="Enter the corrected keyword" id="proposewl_<?php echo $this->_tpl_vars['index']; ?>
" class="propose" name="proposewl[]" />
                            <input type="hidden" id="articlepath" name="articlepath[]" value="<?php echo $this->_tpl_vars['wblword']['articlepath']; ?>
" />
                            <input type="hidden" id="articlename" name="articlename[]" value="<?php echo $this->_tpl_vars['wblword']['articleName']; ?>
" />
                            <input type="hidden" id="filename" name="filename[]" value="<?php echo $this->_tpl_vars['wblword']['filename']; ?>
" />
                            <input type="hidden" id="zipfilename" name="zipfilename[]" value="<?php echo $this->_tpl_vars['wblword']['zipfilename']; ?>
" />
                            <input type="hidden" id="wordcount" name="wordcount[]" value="<?php echo $this->_tpl_vars['wblword']['wordcount']; ?>
" />
                            <input type="hidden" id="participationId" name="participationId[]" value="<?php echo $this->_tpl_vars['wblword']['participationId']; ?>
" /></td>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?>
            </tbody>
          </table>
          <div class="form-group span12">
            <div class="pull-center">
                <button type="button" class="btn btn-info" id="blackclose" name="blackclose" onclick="" >Close</button>
                <button type="button" class="btn btn-success" id="addnewkeyword" name="addnewkeyword"   >Validate</button>
            </div>
          </div>
          <?php endif; ?>
        </form>
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
    $(\'#addnewkeyword\').click(function() {
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
        /*if($(\'#proposewl\').val() == \'\'){
            bootbox.alert("Please enter the new white list keyword");
        }else{*/
            /*var target_page = "/Contrib/sendarticlewithwlnewkeyword?newkeyword="+newkeyword+"&articlepath="+articlepath
                +"&articleName="+articleName+"&filename="+filename+"&participation_id="+participationId;*/
            var target_page = "/Contrib/sendarticlewithwlnewkeyword?nkword="+arr+"&data="+$("#newwlkeywords").serialize();
            $.post(target_page, function(data){   //alert(data);
                var obj = $.parseJSON(data);
                if(obj.status=="success"){
                    location.reload();
                }else if(obj.status=="wlnwkwmissing"){
                    bootbox.alert("The proposed keyword is not present in your document");
                }

            });
        //}
    });
    $(\'#addnewkeywordsingle\').click(function() {
        var newkeyword = $(\'#proposewl\').val();
         var articlepath = $(\'#articlepath\').val();
         var articleName = $(\'#articlename\').val();
         var participationId = $(\'#participationId\').val();
         var filename = $(\'#filename\').val();
        if($(\'#proposewl\').val() == \'\'){
            bootbox.alert("Please enter the new white list keyword");
        }else{
            var target_page = "/Contrib/sendarticlewithwlnewkeyword?newkeyword="+newkeyword+"&articlepath="+articlepath
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
        }
    });


</script>    
'; ?>