<script type="text/javascript">
	$(function(){
		$('div.hidden').hide();
		$('a.showform').click(function(event){ 
			event.preventDefault();
			$('div.hidden div.inner').load('/templates/add/');		
			$('div.hidden').fadeIn();
		});

		$('p.hide a').click(function(event){ 
			event.preventDefault();		
			$(this).parent().parent().fadeOut();
		});
	});
</script>

<h1 class="headingleft">Email Templates</h1>

<div class="headingright">
	<a href="<?php echo site_url('/admin/emailer/add_template'); ?>" class="button blue">Add Template</a>
</div>

<div class="hidden">
	<p class="hide"><a href="#">x</a></p>
	<div class="inner"></div>
</div>

<?php if ($email_templates): ?>

	<?php echo $this->pagination->create_links(); ?>
	
	<table class="default clear">
		<tr>
			<th>Templates</th>
			<th class="tiny">&nbsp;</th>
			<th class="tiny">&nbsp;</th>
		</tr>
	<?php foreach ($email_templates as $template): ?>
		<tr>
			<td><?php echo anchor('/admin/emailer/edit_template/'.$template['templateID'], $template['templateName']); ?></td>
			<td><?php echo anchor('/admin/emailer/edit_template/'.$template['templateID'], 'Edit'); ?></td>
			<td><?php echo anchor('/admin/emailer/delete_template/'.$template['templateID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
	
	<?php echo $this->pagination->create_links(); ?>
	
	<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

	<p class="clear">You have no email templates yet.</p>

<?php endif; ?>
