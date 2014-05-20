<h1 class="headingleft">Gift Certificates</h1>

<div class="headingright">
  <a href="<?php echo site_url('/admin/shop/add_gift_certificate'); ?>" class="button">Add Gift Certificate</a>
</div>
<div class="clear"></div>

<?php if($this->session->flashdata('success')): ?>
  <div class="message">
    <p><?php echo $this->session->flashdata('success'); ?></p>
  </div>
<?php endif; ?>

<?php if ($shop_gift_certificates): ?>

<?php echo $this->pagination->create_links(); ?>

<table class="default clear<?php echo ($catID) ? ' order' : ''; ?>">
  <thead>
    <tr>
      <th class="tiny">ID</th>
      <th class="narrow">&nbsp;</th>
      <th class="wider">Name</th>
      <th class="tiny">Amount</th>
      <th class="tiny">&nbsp;</th>
      <th class="tiny">&nbsp;</th>		
    </tr>
  </thead>
  <tbody>
	<?php foreach ($shop_gift_certificates as $gift): ?>
      <tr>
        <td><?php echo $gift['id']; ?></td>
        <td>
          <img src="<?php echo site_url('/resize.php?src='.site_url('/static/uploads/'.$gift['image']).'&w=150'); ?>" />
        </td>
        <td>
          <?php echo $gift['name']; ?> <br />
          <strong>Reference Code: </strong><?php echo $gift['reference_code']; ?>
        </td>
        <td><?php echo number_format($gift['amount'], 2); ?></td>
        <td class="col8 tiny">
          <?php echo anchor('/admin/shop/edit_gift_certificate/'.$gift['id'], 'Edit'); ?>
        </td>
        <td class="col9 tiny">
          <?php echo anchor('/admin/shop/delete_gift_certificate/'.$gift['id'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
        </td>
      </tr>
	<?php endforeach; ?>
  </tbody>
</table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p>No gift certificates yet.</p>


<?php endif; ?>

