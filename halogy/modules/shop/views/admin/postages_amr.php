<script type="text/javascript">
$(function(){
	$.listen('click', 'a.showform', function(event){showForm(this,event);});
	$.listen('click', 'input#cancel', function(event){hideForm(this,event);});
});
</script>

<h1 class="headingleft">Shipping Costs</h1>

<div class="headingright">	
	<a href="<?php echo site_url('/admin/shop/postages_amr/add'); ?>" class="showform button blue">Add Shipping Rate</a>
</div>

<div class="clear"></div>
<div class="hidden"></div>

<?php if(!empty($message)): ?>
	<div class='message'><?php echo $message; ?></div>
<?php endif; ?>

<?php if (!empty($shop_postages_amr)): ?>
<table class="default">
	<tr>
		<th>Name</th>
		<th>First Item Cost (Dom)</th>
		<th>Succeeding Item Cost (Dom)</th>
		<th>Maximum Cost (Dom)</th>
		<th>First Item Cost (Int'l)</th>
		<th>Succeeding Item Cost (Int'l)</th>
		<th>Maximum Cost (Int'l)</th>
		<th>Created</th>
		<th class="tiny"></th>		
		<th class="tiny"></th>
	</tr>
	<?php foreach($shop_postages_amr as $postage): ?>
		<?php $created = date("d-M-y h:i A", strtotime($postage['created'])); ?>
		<tr>
			<td><?php echo $postage['name'] ?></td>
			<td><?php echo currency_symbol(); ?><?php echo number_format($postage['first_cost'], 2); ?></td>
			<td><?php echo currency_symbol(); ?><?php echo number_format($postage['succeeding_cost'], 2); ?></td>
			<td><?php echo currency_symbol(); ?><?php echo number_format($postage['max_cost'], 2); ?></td>
			<td><?php echo currency_symbol(); ?><?php echo number_format($postage['first_cost_i'], 2); ?></td>
			<td><?php echo currency_symbol(); ?><?php echo number_format($postage['succeeding_cost_i'], 2); ?></td>
			<td><?php echo currency_symbol(); ?><?php echo number_format($postage['max_cost_i'], 2); ?></td>
			<td><?php echo $created; ?></td>
			<td><?php echo anchor('/admin/shop/postages_amr/add/'.$postage['id'], 'Edit', 'class="showform"'); ?></td>
			<td><?php echo anchor('/admin/shop/postages_amr/delete/'.$postage['id'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\');"'); ?></td>
		</tr>
	<?php endforeach; ?>
</table>

<?php else: ?>

<p>You have not yet set up your shipping costs yet.</p>

<?php endif; ?>