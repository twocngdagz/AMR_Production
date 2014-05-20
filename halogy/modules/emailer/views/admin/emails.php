<h1 class="headingleft">Emails for '<?php echo @$campaign['campaignName']; ?>'</h1>

<div class="headingright">
	<?php if (in_array('emailer_edit', $this->permission->permissions)): ?>
		<?php echo anchor('/admin/emailer/add_email/'.$campaignID, 'Add Email', array('class'=>'button')); ?>
	<?php endif; ?>
</div>

<h2 class="clear">Saved Drafts</h2>

<?php if ($emailDrafts): ?>
	
	<table class="default clear">
		<tr>
			<th>Email Name</th>
			<th>Date Created</th>
			<th class="tiny">&nbsp;</th>
			<th class="tiny">&nbsp;</th>		
		</tr>
		<?php foreach ($emailDrafts as $email): ?>
			<tr class="draft">
				<td><?php echo (in_array('emailer_edit', $this->permission->permissions)) ? anchor('/admin/emailer/edit_email/'.$email['emailID'], $email['emailName']) : $email['emailName']; ?></td>
				<td><?php echo dateFmt($email['dateCreated'], '', '', TRUE); ?></td>
				<td class="tiny">
					<?php if (in_array('emailer_edit', $this->permission->permissions)): ?>
						<?php echo anchor('/admin/emailer/edit_email/'.$email['emailID'], 'Edit'); ?>
					<?php endif; ?>
				</td>
				<td class="tiny">
					<?php if (in_array('emailer_delete', $this->permission->permissions)): ?>
						<?php echo anchor('/admin/emailer/delete_email/'.$email['emailID'].'/'.$campaign['campaignID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>	

<?php else: ?>

	<p class="clear">You have no saved drafts for this campaign.</p>

<?php endif; ?>

<br class="clear" />

<?php if ($pendingEmails): ?>

	<h2 class="headingleft">Pending Emails</h2>
	
	<table class="default clear">
		<tr>
			<th>Email Name</th>
			<th>Date Created</th>
			<th>Date to Deploy</th>
			<th class="tiny">&nbsp;</th>
			<th class="tiny">&nbsp;</th>		
		</tr>
		<?php foreach ($pendingEmails as $email): ?>
			<tr>
				<td><?php echo (in_array('emailer_edit', $this->permission->permissions)) ? anchor('/admin/emailer/edit_email/'.$email['emailID'], $email['emailName']) : $email['emailName']; ?></td>
				<td><?php echo dateFmt($email['dateCreated'], '', '', TRUE); ?></td>
				<td><span style="color:orange;"><?php echo dateFmt($email['deployDate'], '', '', TRUE); ?></span></td>
				<td class="tiny">
					<?php if (in_array('emailer_edit', $this->permission->permissions)): ?>
						<?php echo anchor('/admin/emailer/edit_email/'.$email['emailID'], 'Edit'); ?>
					<?php endif; ?>
				</td>
				<td class="tiny">
					<?php if (in_array('emailer_delete', $this->permission->permissions)): ?>
						<?php echo anchor('/admin/emailer/delete_email/'.$email['emailID'].'/'.$campaign['campaignID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	
	<br class="clear" />

<?php else: ?>

	<h2 class="headingleft">Pending Emails</h2>
	<p class="clear">You have no pending emails for this campaign.</p>

<?php endif; ?>