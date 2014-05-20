<?php if (!$this->core->is_ajax()): ?>
	<<h1>Edit Campaign</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<label for="campaignName">Campaign name:</label>

	<?php echo @form_input('campaignName', set_value('campaignName', $data['campaignName']), 'class="formelement"'); ?>
		
	<input type="submit" value="Save Changes" class="button" />
	<a href="<?php echo site_url('/admin'); ?>" class="button cancel grey">Cancel</a>
	<br class="clear" />
	
</form>
