<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default" enctype="multipart/form-data">

  <h1 class="headingleft">Update Partner Products</h1>

  <div class="headingright">
	<input type="submit" value="Save Changes" class="button" />
  </div>
  <div class="clear"></div>

  <?php if ($errors = validation_errors()): ?>
    <div class="error">
      <?php echo $errors; ?>
    </div>
  <?php endif; ?>


  <label>Title: </label>
  <?php echo @form_input('title', set_value('title', ($prod_info->title ? $prod_info->title : $this->input->post('title'))), 'id="title" class="formelement"'); ?>
  <br class="clear" />

  <label>URL (Tracking Code): </label>
  <?php echo @form_input('url', set_value('url', ($prod_info->url ? $prod_info->url : $this->input->post('url'))), 'id="url" class="formelement"'); ?>
  <br class="clear" />
  
  <label>Image: </label>
  <div class="uploadfile">
    <input type="file" id="image" size="16" value="" name="image" class="">		
  </div><br class="clear" />
  <img src="<?php echo site_url('resize.php?src='.site_url('/static/uploads/'.$prod_info->image).'&w=200&s=1'); ?>" />
  <br class="clear" /><br />

  <label>Alternate Image: </label>
  <div class="uploadfile">
    <input type="file" id="alt_image" size="16" value="" name="alt_image" class="">   
  </div><br class="clear" />
  <img src="<?php echo site_url('resize.php?src='.site_url('/static/uploads/'.$prod_info->alt_image).'&w=200&s=1'); ?>" />
  <br class="clear" /><br />

  <label>Alternate URL (Tracking Code): </label>
  <?php echo @form_input('alt_url', set_value('alt_url', ($prod_info->alt_url ? $prod_info->alt_url : $this->input->post('alt_url'))), 'id="alt_url" class="formelement"'); ?>
  <br class="clear" />
		
  <p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
