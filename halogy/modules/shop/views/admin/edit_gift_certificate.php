<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

  <h1 class="headingleft">
    Edit Gift Certificate <small>(<a href="<?php echo site_url('/admin/shop/gift_certificates'); ?>">Back to Gift Certificates</a>)</small>
  </h1>

  <div class="headingright">
    <input type="submit" value="Save Changes" class="button save" />
  </div>
  <div class="clear"></div>

  <?php if ($errors = validation_errors()): ?>
    <div class="error">
      <?php echo $errors; ?>
    </div>
  <?php endif; ?>


  <div id="details" class="tab">

	<h2 class="underline">Details</h2>
	
    <label>Reference Code: </label>
    <?php echo $gift_certificate_info->reference_code; ?>
	<br class="clear" />
    
    <label>Image: </label>
    <div class="uploadfile">
      <input type="file" id="image" size="16" value="" name="image" class="">		
    </div>
	<br class="clear" />
    
	<label>Name: </label>
	<?php echo @form_input('name', set_value('name', ($this->input->post('name') ? $this->input->post('name') : $gift_certificate_info->name)), 'class="formelement"'); ?>
	<br class="clear" />
    
    <label>Amount: </label>
    <span class="price"><strong>$</strong></span>
    <?php echo @form_input('amount', set_value('amount', ($this->input->post('amount') ? $this->input->post('amount') : number_format($gift_certificate_info->amount, 2))), 'class="formelement small"'); ?>
	<br class="clear" />
    
    
    
    
</div>



 



<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
