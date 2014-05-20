<h1 class="headingleft">Partner Products</h1>

<div class="headingright">
  <a href="<?php echo site_url('/admin/partner-products/add'); ?>" class="button">Add New</a>
</div>

<?php if($this->session->flashdata('success-message')): ?>
  <div class="message clear">
    <p><?php echo $this->session->flashdata('success-message'); ?></p>
  </div>
<?php endif; ?>

<?php if ($partner_products): ?>

<?php echo $this->pagination->create_links(); ?>
  <table class="default clear">
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th class="narrow">Image</th>
      <th class="tiny">&nbsp;</th>
      <th class="tiny">&nbsp;</th>
    </tr>
  <?php foreach ($partner_products as $part_prod): ?>
    <tr class="">
      <td><?php echo $part_prod['id']; ?></td>
      <td><?php echo $part_prod['title']; ?></td>
      <td><img src="<?php echo site_url('resize.php?src='.site_url('/static/uploads/'.$part_prod['image']).'&w=200&s=1'); ?>" /></td>
      <td class="tiny">
        <?php echo anchor('/admin/partner-products/edit/'.$part_prod['id'], 'Edit'); ?>
      </td>
      <td class="tiny">			
        <?php echo anchor('/admin/partner-products/delete/'.$part_prod['id'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </table>

<?php echo $this->pagination->create_links(); ?>

<p style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>

<?php else: ?>

<p class="clear">There are no products yet.</p>

<?php endif; ?>