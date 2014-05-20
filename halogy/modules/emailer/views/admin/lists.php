<script type="text/javascript">
$(function(){
	$('a.showform').click(function(event){ 
		event.preventDefault();
		$('div.hidden div.inner').load('<?php echo site_url('/admin/emailer/add_list'); ?>', function(){ $('div.hidden').slideToggle('400'); });
	});
});
</script>

<h1 class="headingleft">Mailing Lists</h1>

<div class="headingright">
	<?php if (in_array('emailer_lists', $this->permission->permissions)): ?>
		<a href="<?php echo site_url('/admin/emailer/add_list'); ?>" class="button blue showform">Add List</a>
	<?php endif; ?>
</div>

<br class="clear" />

<div class="hidden"></div>

<?php if ($email_lists): ?>

	<?php echo $this->pagination->create_links(); ?>
	
	<table class="default clear">
		<tr>
			<th>List Name</th>
			<th>Subscribers</th>
			<th class="tiny">&nbsp;</th>
			<th class="tiny">&nbsp;</th>		
		</tr>
	<?php foreach ($email_lists as $list): ?>
		<tr>
			<td><?php echo (in_array('emailer_lists', $this->permission->permissions)) ? anchor('/admin/emailer/edit_list/'.$list['listID'], $list['listName'], 'class="showform"') : $list['listName']; ?></td>
			<td>
				<?php echo $list['numSubscribers']; ?>
			</td>
			<td class="tiny">
				<?php if (in_array('emailer_lists', $this->permission->permissions)): ?>
					<?php echo anchor('/admin/emailer/edit_list/'.$list['listID'], 'Edit', 'class="showform"'); ?>
				<?php endif; ?>
			</td>
			<td class="tiny">
				<?php if (in_array('emailer_lists', $this->permission->permissions)): ?>
					<?php echo anchor('/admin/emailer/delete_list/'.$list['listID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	
	<?php echo $this->pagination->create_links(); ?>
	
	<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

	<p>There are no email lists set up yet.</p>

<?php endif; ?>

