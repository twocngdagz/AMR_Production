<?php if (!$this->core->is_ajax()): ?>
	<h1><?php echo (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Categories</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

  <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default" enctype="multipart/form-data">

	<label for="catName">Title:</label>
	<?php echo @form_input('catName', $data['catName'], 'class="formelement" id="catName"'); ?>
	<br class="clear" />
    
    <label for="catImage">Category Image:</label>
    <div class="uploadfile">
      <input type="file" id="image" size="16" value="" name="image" class="">
    </div>
    <span class="tip">Maximum image dimension of 290px by 210px. Accepts jpg, png and gif filetype.</span>
    <br class="clear" />	
    <?php if($data['catImage']): ?>
      <img src="<?php echo site_url('static/uploads/'.$data['catImage']); ?>" />
      <br class="clear" /><br />
    <?php endif; ?>
    
    <label for="catImage">Category Banner Image:</label>
    <div class="uploadfile">
      <input type="file" id="image" size="16" value="" name="catBanner" class="">
    </div>
    <span class="tip">Maximum image dimension of 960px by 160px. Accepts jpg, png and gif filetype.</span>
    <br class="clear" />
    <?php if($data['catBanner']): ?>
      <img src="<?php echo site_url('static/uploads/'.$data['catBanner']); ?>" />
      <br class="clear" /><br />
    <?php endif; ?>
      
    <?php
      $color_arr = array(
          'orange'      => 'Orange',
          'light-green' => 'Light Green',
          'pink'        => 'Pink',
          'yellow'      => 'Yellow',
          'light-blue'  => 'Light Blue',
          'red'         => 'Red'
      );
    ?>
    <label for="catColor">Overlay Color:</label>
    <?php echo form_dropdown('catColor', $color_arr, $data['catColor'], 'class="formelement"'); ?>
    <br class="clear" />
    
	<label for="templateID">Parent:</label>
	<?php
		$options = '';		
		$options[0] = 'Top Level';
		if ($parents):	
			foreach ($parents as $parent):
				if ($parent['catID'] != @$data['catID']) $options[$parent['catID']] = $parent['catName'];
			endforeach;
		endif;
		
		echo @form_dropdown('parentID',$options,$data['parentID'],'id="parentID" class="formelement"');
	?>	
	<br class="clear" />
	
	<label for="home">Show on homepage:</label>
    <?php 
	$home_options = array('0'=>"Hide", '1'=>"Show");
	echo form_dropdown('home', $home_options, $data['home'], 'class="formelement"');
	?>
    <br class="clear" />
	
	<label for="description">Description:</label>
	<?php echo @form_textarea('description',set_value('description', $data['description']), 'id="description" class="formelement short"'); ?>
	<br class="clear" /><br />
		
	<input type="submit" value="Save Changes" class="button nolabel" />
	<input type="button" value="Cancel" id="cancel" class="button grey" />
	
</form>

<br class="clear" />
