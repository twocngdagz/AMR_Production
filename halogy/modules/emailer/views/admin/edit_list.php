<?php if (!$this->core->is_ajax()): ?>
	<h1>Edit List</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default clear">

	<label for="listName">List Name:</label>
	<?php echo @form_input('listName', set_value('listName', $data['listName']), 'class="formelement"'); ?>
	<br class="clear" />
	
	<label for="subscribers">Subscribers:</label>
	<?php echo @form_textarea('subscribers', set_value('subscribers', $data['subscribers']), 'class="formelement"'); ?>
	<br class="clear" />
	<span class="tip nolabel">Add subscribers in the format: "email@address.com, Full Name"</span>
	<br class="clear" /><br />
		
	<input type="submit" value="Save Changes" class="button" />
	<a href="<?php echo site_url('/admin'); ?>" class="button cancel grey">Cancel</a>
	<br class="clear" />
	
</form>

