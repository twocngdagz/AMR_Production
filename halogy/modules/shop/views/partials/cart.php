<?php if($cart): ?>
<tbody>
  <?php foreach ($cart as $key => $item): ?>
  <?php
  
    $variationHTML = '';
    // get variation 1
    if ($item['variation1']) $variationHTML .= ' ('.$this->site->config['shopVariation1'].': '.$item['variation1'].')';
    // get variations 2
    if ($item['variation2']) $variationHTML .= ' ('.$this->site->config['shopVariation2'].': '.$item['variation2'].')';
    // get variations 3
    if ($item['variation3']) $variationHTML .= ' ('.$this->site->config['shopVariation3'].': '.$item['variation3'].')';

    $key = $this->core->encode($key);
    
	$backorder = "";
	if(!empty($item['variation1Backorder'])) { $backorder = "You have 'On Backorder' items in your cart. Our team will contact you once the items are available."; }
	if(!empty($item['variation2Backorder'])) { $backorder = "You have 'On Backorder' items in your cart. Our team will contact you once the items are available."; }
	if(!empty($item['variation3Backorder'])) { $backorder = "You have 'On Backorder' items in your cart. Our team will contact you once the items are available."; }
	
    // product image
    
    $prod_image = $this->uploads->load_image($item['productID'], true, true);
    
    // Set price
    
    if($item['sale_price'] != '0.00')
    {
      $price = $item['sale_price'];
    }
    else
    {
      $price = $item['price'];
    }
    
  ?>
<tr>
  <td>
    <a href="<?php echo site_url('/shop/cart/remove/'.$key); ?>" onclick="return confirm('Are you sure you want to remove this product in your cart? ');">
      <img src="<?php echo site_url('/static/images/front/icon-close.png'); ?>" alt="Close" />
    </a>
  </td>
  <td>
    <img src="<?php echo site_url('resize.php?src='.site_url($prod_image['src']).'&w=100&h=75'); ?>" class="prod-image" />
  </td>
  <td>
    <a href="<?php echo site_url('/shop/'.$item['productID'].'/'.strtolower(url_title($item['productName']))); ?>">
      <?php echo $item['productName']; ?>
      <?php echo $variationHTML; ?>
    </a>
	<?php echo "<p style='font-size:10px'>".$backorder."<p>"; ?>
  </td>
  <td>
    <?php echo currency_symbol().number_format($price, 2); ?>
  </td>
  <td>
    <?php if($this->uri->segment(2) == 'checkout'): ?>
    <?php echo $item['quantity']; ?>
    <?php else: ?>
    <input class="quantity" name="quantity[<?php echo $key; ?>]" type="text" maxlength="2" value="<?php echo $item['quantity']; ?>" />
    <?php endif; ?>
  </td>
  <td>
	<?php echo $item['variation2']; ?>
  </td>
  <td>
    <?php echo currency_symbol(); ?><?php echo number_format(($price * $item['quantity']), 2); ?>
  </td>
</tr>

<?php endforeach; ?>
</tbody>
<?php if ($this->session->userdata('cart_donation') > 0): 	// find out if there is a donation (adding it after the postage) ?>
<tr>
	<td>Donation</td>
	<td>1 <a href="/shop/cart/remove_donation/">[remove]</a></td>
	<td><?php echo currency_symbol(); ?><?php echo number_format($this->session->userdata('cart_donation'), 2); ?></td>
</tr>
<?php endif; ?>
<?php endif; ?>