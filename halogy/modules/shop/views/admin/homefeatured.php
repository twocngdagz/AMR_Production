<?php

?>

<form method='post' enctype='multipart/form-data'>
	
	<h2>New Product</h2>
	
	<label for="np_title">Title:</label>
	<?php echo @form_input('np_title',set_value('np_title', $np_title), 'id="np_title" class="formelement"'); ?>
	<br class="clear" />
    
	<div class="uploadfile" style="margin-top: 10px; margin-left: 30px;">
      <input type="file" name="np_image" value="" size="16" id="image" class="">		
    </div>
	<br class="clear" />
	
	<label for="np_url">URL:</label>
	<?php echo @form_input('np_url',set_value('np_url', $np_url), 'id="np_url" class="formelement"'); ?>
	<br class="clear" />
	
	<br /><br />
	
	<h2>Products We Love</h2>
	
	<label for="pw_title">Title:</label>
	<?php echo @form_input('pw_title',set_value('pw_title', $pw_title), 'id="pw_title" class="formelement"'); ?>
	<br class="clear" />
    
	<div class="uploadfile" style="margin-top: 10px; margin-left: 30px;">
      <input type="file" name="pw_image" value="" size="16" id="image" class="">		
    </div>
	<br class="clear" />
	
	<label for="pw_url">URL:</label>
	<?php echo @form_input('pw_url',set_value('pw_url', $pw_url), 'id="pw_url" class="formelement"'); ?>
	<br class="clear" />

	<br />
	<p><?php echo $msg ?></p>
	<button type='submit'>SAVE</button>
</form>