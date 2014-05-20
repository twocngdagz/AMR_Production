<script type="text/javascript">
$(function(){
	$('a.showform').click(function(event){ 
		event.preventDefault();
		$('div.hidden div.inner').load('<?php echo site_url('/admin/emailer/add_campaign'); ?>', function(){ $('div.hidden').slideToggle('400'); });
	});
});
</script>

<h1 class="headingleft">Email Campaigns</h1>

<div class="headingright">
	<?php if (in_array('emailer_campaigns_edit', $this->permission->permissions)): ?>
		<a href="<?php echo site_url('/admin/emailer/add_campaign'); ?>" class="button blue showform">Add Campaign</a>
	<?php endif; ?>
</div>

<br class="clear" />

<div class="hidden"></div>

<?php if ($email_campaigns): ?>

	<?php echo $this->pagination->create_links(); ?>
	
	<table class="default clear">
		<tr>
			<th>Campaigns</th>
			<th class="tiny">&nbsp;</th>
			<th class="tiny">&nbsp;</th>		
		</tr>
	<?php foreach ($email_campaigns as $campaign): ?>
		<tr>
			<td><?php echo (in_array('emailer_campaigns_edit', $this->permission->permissions)) ? anchor('/admin/emailer/emails/'.$campaign['campaignID'], $campaign['campaignName']) : $campaign['campaignName']; ?></td>
			<td class="tiny">
				<?php if (in_array('emailer_campaigns_edit', $this->permission->permissions)): ?>
					<?php echo anchor('/admin/emailer/edit_campaign/'.$campaign['campaignID'], 'Edit', 'class="showform"'); ?>
				<?php endif; ?>
			</td>
			<td class="tiny">
				<?php if (in_array('emailer_campaigns_edit', $this->permission->permissions)): ?>
					<?php echo anchor('/admin/emailer/delete_campaign/'.$campaign['campaignID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	
	<?php echo $this->pagination->create_links(); ?>
	
	<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

	<p>There are no email campaigns set up yet.</p>

<?php endif; ?>

