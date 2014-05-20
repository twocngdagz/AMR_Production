<?php if (!$lists): ?>

<h1>Add Email</h1>

<br />

<div class="error">
	<p>You have not yet set up any mailing lists and you will need a list in order to send emails. You can create mailing lists <a href="<?php echo site_url('/admin/emailer/lists'); ?>">here</a>.</p>
</div>

<?php else: ?>

<script type="text/javascript">
	$(function(){
		$("input.datebox").datebox();
		$('a.toggle').click(function(event){ 
			event.preventDefault();		
			$('div.hidden').slideToggle('400');
		});
		$('ul.innernav a').click(function(event){
			event.preventDefault();
			$pos = $(this).attr('href');
			$.scrollTo('form', { duration: 200 });
			$('div.panes').scrollTo(
				$pos, { duration: 400, axis: 'x'}
			);
		});
		function changeTemplate() {
			var templateID = ($('#templateID').val());
			if (templateID){
				$('#preview').attr('src', '<?php echo site_url('/admin/emailer/view_template'); ?>/'+templateID+'/<?php echo $data['emailID']; ?>');
				window.frames['preview.src'] = '<?php echo site_url('/admin/emailer/view_template'); ?>/'+templateID+'/<?php echo $data['emailID']; ?>'; 
			} else {
				$('#preview').attr('src', 'about:blank');
			}
		}
		$('ul.innernav a').click(function(event){
			event.preventDefault();
			$(this).parent().siblings('li').removeClass('selected'); 
			$(this).parent().addClass('selected');
		});
		$('select#templateID').change(function(){
			if (confirm("By doing this you may lose unsaved data.\n\nAre you sure you want to do this?")){
				changeTemplate();
			}
		});
		$('input#cancel').click(function(){
			if (confirm("By doing this you may lose unsaved data.\n\nAre you sure you want to do this?")){
				return true;
			}
			else {
				return false;
			}
		});	
		$('input#deployButton').click(function(){
			if ($('select#listID').val() == 0){
				alert('You have not selected a mailing list');
				return false;
			}
			return confirm('Are you sure you want to deploy this email?');
		});
		$('input#saveButton').click(function(){
			var requiredField = 'input#emailName';
			if (!$(requiredField).val()) {
				$('div.panes').scrollTo(
					0, { duration: 400, axis: 'x' }
				);
				$(requiredField).addClass('error').prev('label').addClass('error');
				$(requiredField).focus(function(){
					$(requiredField).removeClass('error').prev('label').removeClass('error');
				});
				return false;
			}
		});
		changeTemplate();
		$('div.panes').scrollTo(
			0, { duration: 400, axis: 'x'}
		);
	});
</script>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<div class="hidden">		
		<label for="testEmails">Emails:</label>
		<?php echo @form_textarea('testEmails', '', 'class="formelement short" id="emails"'); ?>
		<br class="clear" />
		<span class="tip nolabel">Add emails in the format: "email@address.com, Full Name"</span>
		<br class="clear" /><br />
			
		<input type="submit" value="Send Test" class="button" />
		<a href="<?php echo site_url('/admin'); ?>" class="button cancel grey">Cancel</a>
		<br class="clear" />
	</div>

	<h1 class="headingleft">Edit Email <small>(<?php echo ($data['campaignID']) ? anchor('/admin/emailer/emails/'.$data['campaignID'], 'Back to Campaign') : anchor('/admin/emailer/campaigns', 'Back to Campaigns'); ?>)</small></h1>

	<div class="headingright">
		<?php if (!@$data['deploy']): ?>
			<a href="#" class="button blue toggle">Send Test</a>
		<?php endif; ?>
		<input type="submit" id="saveButton" value="Save Changes" class="button" />
		<?php if (!@$data['deploy']): ?>
			<input type="submit" id="deployButton" name="deployButton" value="Deploy" class="button orange" />
		<?php endif; ?>
	</div>
	
	<div class="clear"></div>
	
	<?php if ($errors = validation_errors()): ?>
		<div class="error">
			<?php echo $errors; ?>
		</div>
	<?php endif; ?>
	<?php if (isset($message)): ?>
		<div class="message">
			<?php echo $message; ?>
		</div>
	<?php endif; ?>

	<ul class="innernav clear">
		<li class="selected"><a href="#pane1">Details</a></li>
		<li><a href="#pane2">Plain Text</a></li>
		<li><a href="#pane3">HTML</a></li>
	</ul>

	<div class="panes">
		<div class="paneslide" style="width: 5000px;">
			<div id="pane1" class="pane">
	
				<h2 class="underline">Details</h2>
			
				<label for="emailName">Email name:</label>
				<?php echo @form_input('emailName', set_value('emailName', $data['emailName']), 'id="emailName" class="formelement"'); ?>
				<span class="tip">This is the name of the email, for your information only.</span>
				<br class="clear" />
			
				<label for="emailName">Email subject:</label>
				<?php echo @form_input('emailSubject', set_value('emailSubject', $data['emailSubject']), 'id="emailSubject" class="formelement"'); ?>
				<br class="clear" />
			
				<label for="campaignID">Campaign:</label>
				<?php
				if ($campaigns):
					$options = '';
					foreach ($campaigns as $campaign):
						$options[$campaign['campaignID']] = $campaign['campaignName'];
					endforeach;
					
					echo @form_dropdown('campaignID',$options, set_value('campaignID', $data['campaignID']),'id="campaignID" class="formelement"');
				endif;
				?>	
				<br class="clear" />
				
				<label for="listID">Mailing List:</label>
				<?php
					$options = array(0 => 'Select a mailing list...');
					if ($lists):
						foreach ($lists as $list):
							$options[$list['listID']] = $list['listName'];
						endforeach;
					endif;
					echo @form_dropdown('listID',$options, set_value('listID', $data['listID']),'id="listID" class="formelement"');
				?>	
				<br class="clear" />
							
				<label for="templateID">Template:</label>
				<?php
					$options = array('' => 'No HTML');
					if ($templates):
						foreach ($templates as $template):
							$options[$template['templateID']] = $template['templateName'];
						endforeach;
					endif;
					
					echo @form_dropdown('templateID',$options, set_value('templateID', $data['templateID']),'id="templateID" class="formelement"');
				?>	
				<br class="clear" />
				
				<label for="deployDate">Deploy Date:</label>
				<?php echo @form_input('deployDate', date('d M Y', strtotime($data['deployDate'])), 'id="deployDate" class="formelement datebox" readonly="readonly"'); ?>
				<br class="clear" />
				
			</div>
			
			<div id="pane2" class="pane">
			
				<h2 class="underline">Plain Text</h2>
			
				<label for="bodyText">Text Body:</label>
				<?php echo @form_textarea('bodyText', set_value('bodyText', $data['bodyText']), 'id="bodyText" class="formelement"'); ?>
				<br class="clear" />
				<span class="tip nolabel">This is the plain text part of the email which is always recommended. To add a HTML part then you need to select a template.</span>
				<br />
			
			</div>

			<div id="pane3" class="pane">
			
				<iframe name="preview" id="preview" src="about:blank" frameborder="0" marginheight="0" marginwidth="0"></iframe>
	
			</div>	

		</div>
	</div>
	
	<p style="text-align: right;">
		<a href="#" class="button grey" id="totop">Back to top</a>
	</p>
	
</form>
<?php endif; ?>