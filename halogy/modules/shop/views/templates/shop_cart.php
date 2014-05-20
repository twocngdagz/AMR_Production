{include:header}
  <div id="main-content">
    <div class="container">
      <h1>Shopping Cart</h1>
		
      {if errors}
        <div class="error">
          {errors}
        </div>
      {/if}
      
      <form action="{site:url}shop/cart/update" method="post" id="cart_form" class="default">
		
        <p>Your shopping cart contains:</p>
		
        <table class="default" width="100%" cellpadding="0" cellspacing="0" id="cart">
          <thead>
            <tr>
              <th width="5%"></th>
              <th width="10%"></th>
              <th width="45%">Product</th>
              <th width="15%">Unit Cost ({site:currency})</th>
              <th width="5%">Quantity</th>
			  <th width="10%">Size</th>
              <th width="10%">Cost ({site:currency})</th>
            </tr>
          </thead>
          
          {if cart:items}
            {cart:items}				
          {else}
            <tr>
              <td colspan="3">Your cart is empty! </td>
            </tr>
          {/if}
            
            <tr class="subtotal">
              <td colspan="6" class="bottom-field">&nbsp;</td>
              <td class="bottom-value">&nbsp;</td>
            </tr>
            <tr >
              <td colspan="6" class="bottom-field">Sub total:</td>
              <td class="bottom-value">{cart:subtotal}</td>
            </tr>
            <tr>
              <td colspan="6" class="bottom-field">Shipping:</td>
              <td class="bottom-value">{cart:postage}</td>
            </tr>
          {if cart:discounts}
            <tr>
              <td colspan="6" class="bottom-field">Discounts applied:</td>
              <td class="bottom-value">({cart:discounts})</td>
            </tr>
          {/if}
		  {if cart:gc_discounts}
          <tr>
            <td colspan="6" class="bottom-field">Gift Cards applied:</td>
            <td class="bottom-value">({cart:gc_discounts})</td>
          </tr>
		  {/if}
          {if cart:tax}
            <tr>
              <td colspan="6" class="bottom-field">Tax:</td>
              <td class="bottom-value">{cart:tax}</td>
            </tr>
          {/if}
            <tr>
              <td colspan="6" class="bottom-field">TOTAL:</td>
              <td class="bottom-value">{cart:total}</td>
            </tr>					
                				
        </table>
	
<!--        <label for="shippingBand">Shipping Band:</label>
        <select name="shippingBand" id="shippingBand" onchange="document.getElementById('cart_form').submit();" class="formelement">
            {cart:bands}
        </select>
        <br class="clear" /><br />

        {if cart:modifiers}

            <label for="shippingModifier">Shipping Modifier:</label>
            <select name="shippingModifier" id="shippingModifier" onchange="document.getElementById('cart_form').submit();" class="formelement">
                {cart:modifiers}
            </select>
            <br class="clear" /><br />

        {/if}-->
        
        <div class="coupons">
          <label for="discountCode">Discount or Gift Card Code: <!--<small>Input a coupon or referral code here</small>--> </label>
          <input type="text" name="discountCode" id="discountCode" value="{form:discount-code}" class="formelement small" />
        </div>
  
        <br class="clear"><br />

			{if cart:items}
		
				<div style="float:right;">
					<input type="submit" value="Update Cart" class="" />
					<input name="checkout" type="submit" value="Checkout &gt;&gt;" class="checkout-btn" />
				</div>
				<br class="clear" />					

			{/if}
		
		</form>
        
    </div>
  </div>
{include:footer}