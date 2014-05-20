<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<h1 class="headingleft">Edit Template <small>(<a href="<?php echo site_url('/admin/emailer/templates'); ?>">Back to Templates</a>)</small></h1>
	
	<div class="headingright">
		<input type="submit" value="Save Changes" class="button nolabel" />
	</div>

	<?php if ($errors = validation_errors()): ?>
		<div class="error">
			<?php echo $errors; ?>
		</div>
	<?php endif; ?>

	<label for="templateName">Template name:</label>
	<?php echo @form_input('templateName',set_value('templateName', $data['templateName']), 'id="templateName" class="formelement"'); ?>
	<br class="clear" />

	<label for="linkStyle">Link style:</label>
	<?php echo @form_input('linkStyle',set_value('linkStyle', $data['linkStyle']), 'id="linkStyle" class="formelement"'); ?>
	<br class="clear" />	

	<label for="templateHTML">HTML:</label>
	<?php echo @form_textarea('body',set_value('body', $data['body']), 'id="body" class="code"'); ?>
	
</form>

<br class="clear" />

<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
