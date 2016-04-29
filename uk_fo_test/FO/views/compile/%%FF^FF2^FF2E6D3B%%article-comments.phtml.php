<?php /* Smarty version 2.6.19, created on 2014-12-01 13:03:08
         compiled from Contrib/article-comments.phtml */ ?>
<?php echo '
<script type="text/javascript">
//Comments submission
$(document).ready(function() {
		$("#comment_submit").click(function(){
				
			$.post("/contrib/save-comments", $("#comment_form").serialize(),
				function(data) {
					//alert(data);
					var obj = $.parseJSON(data);
					$("#comments_list").html(obj.comments);
					$("#comment_count").text(obj.count);
					$("#comment_count_1").html(\'<i class="icon-comment"></i> \'+obj.count);
					$("#article_comments").val(\'\');
				}
			);	
		});
		
		//refreshing comments
		
		var comment_type=$("#comment_type").val();
		var identifier=$("#identifier").val();
		 /* setInterval(function() {
			//$(\'#comments_list\').load(\'/contrib/save-comments?comment_type=\'+comment_type+\'&identifier=\'+identifier);
			$.getJSON(\'/contrib/save-comments?comment_type=\'+comment_type+\'&identifier=\'+identifier,
				{
					format: "json"
				},
				function(data) {
					$("#comments_list").html(data.comments);
					$("#comment_count").text(data.count);
					$("#comment_count_1").html(\'<i class="icon-comment"></i> \'+data.count);
					$("#article_comments").val(\'\');
				});
		}, 60000); */ 
		
		//changing active status of a comment
		$("[id^=delete_comment_]").live(\'click\', function() {
			
			var parentLi=$(this).parents("li:first").attr(\'id\');
			var deleteButton=$(this).attr(\'id\').split("_");
			var comment_id=deleteButton[2];
			deleteComments(comment_id,comment_type,identifier);
				
		});
	});	
/** ajax function to delete/inactive comment data**/
function deleteComments(comment_id,comment_type,identifier)
{
        var target_page = \'/contrib/save-comments?comment_action=delete&comment_id=\'+comment_id+\'&comment_type=\'+comment_type+\'&identifier=\'+identifier;
		
		$.post(target_page, function(data){									
				
				var obj = $.parseJSON(data);
				$("#comments_list").html(obj.comments);
				$("#comment_count").text(obj.count);
				$("#comment_count_1").html(\'<i class="icon-comment"></i> \'+obj.count);
				$("#article_comments").val(\'\');							
			});
}
	
</script>
'; ?>

<div class="mod">
	 <h4 id="comment"><i class="icon-comment"></i> Comments</h4>
	 <ul class="media-list" id="comments_list">
	 <?php if ($this->_tpl_vars['commentDetails'] | @ count > 0): ?>
		 	<?php $_from = $this->_tpl_vars['commentDetails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['comment']):
?>
				<li class="media" id="comment_<?php echo $this->_tpl_vars['comment']['identifier']; ?>
">
					<?php if ($this->_tpl_vars['comment']['delete'] == 'yes'): ?>
						<button  class="close" type="button" id="delete_comment_<?php echo $this->_tpl_vars['comment']['identifier']; ?>
">&times;</button>
					<?php endif; ?>	
					<a class="pull-left imgframe" href="#" role="button" data-toggle="modal" data-target="#viewProfile-ajax">
						<img alt="Topito" class="media-object" width="60px" src="<?php echo $this->_tpl_vars['comment']['profile_pic']; ?>
">
					</a>
					<div class="media-body">
						<h4 class="media-heading">
							<a  role="button" data-toggle="modal" data-target="#viewProfile-ajax"><?php echo $this->_tpl_vars['comment']['profile_name']; ?>
</a></h4>
							<?php echo $this->_tpl_vars['comment']['comments']; ?>

						<p class="muted"><?php echo $this->_tpl_vars['comment']['time']; ?>
</p>
					</div>
				</li>		
			<?php endforeach; endif; unset($_from); ?>	
		
	<?php endif; ?>	
	</ul>
	<div class="row-fluid">
		<div class=" well">
			<form action="" method="POST" id="comment_form">
				<h4>Comment/Ask a Question</h4>
				<fieldset>
					<textarea class="span12" placeholder="Write a comment" name="article_comments" id="article_comments"></textarea>
					<button type="button" id="comment_submit" name="comment_submit" class="btn">Send</button>
					<input type="hidden" name="comment_type" value="<?php echo $this->_tpl_vars['comment_type']; ?>
" id="comment_type">
					<input type="hidden" name="identifier" value="<?php echo $this->_tpl_vars['identifier']; ?>
" id="identifier">
				</fieldset>
			</form>
		</div>
	</div>
</div>