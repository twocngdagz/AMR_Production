{include:header}
  <div id="main-content">
    <div class="container">
      <h1>Checkout</h1>
		
      {if errors}
        <div class="error">
          {errors}
        </div>
      {/if}
      
      <p>Confirm your order and your shipping address below is correct, then click on "Pay With Card" or "Checkout with Paypal" to make payment. If you want to cancel your order click on the "Cancel Order" button.</p>			
      <p>Your shopping cart contains:</p>
      
      <table class="default" width="100%" cellpadding="0" cellspacing="0" id="cart">
        <thead>
          <tr>
            <th width="5%"></th>
            <th width="10%"></th>
            <th width="40%">Product</th>
            <th width="15%">Unit Cost ({site:currency})</th>
            <th width="10%">Quantity</th>
			<th width="10%">Size</th>
            <th width="15%">Cost ({site:currency})</th>
          </tr>
        </thead>
        {cart:items}				
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
      
      <p><a href="{site:url}shop/cart">Update Order</a></p>
	
		
      <table class="default" id="cart-address" width="50%">
        <tr>
          <td width="50%" valign="top">
            <h2>Delivery Address</h2>
            
            <p>
              <strong>Full name:</strong> {user:name}
              <br />

              {if user:address1}
                <strong>Address 1:</strong> {user:address1}
                <br />
              {/if}

              {if user:address2}
                <strong>Address 2:</strong> {user:address2}
                <br />
              {/if}

              {if user:address3}
                <strong>Address 3:</strong> {user:address3}
                <br />
              {/if}

              {if user:city}
                <strong>City:</strong> {user:city}
                <br />
              {/if}

              {if user:state}
                <strong>State:</strong> {user:state}
                <br />
              {/if}

              {if user:postcode}				
                <strong>Post/ZIP code:</strong> {user:postcode}
                <br />
              {/if}

              {if user:country}
                <strong>Country:</strong> {user:country}
              {/if}
            </p>
          </td>
            <td width="50%" valign="top">
                <h2>Billing Address</h2>

                <p>
                    <strong>Full name:</strong> {user:name}
                    <br />

                    {if user:billing-address1}
                        <strong>Address 1:</strong> {user:billing-address1}
                        <br />
                    {/if}

                    {if user:billing-address2}
                        <strong>Address 2:</strong> {user:billing-address2}
                        <br />
                    {/if}

                    {if user:billing-address3}
                        <strong>Address 3:</strong> {user:billing-address3}
                        <br />
                    {/if}

                    {if user:billing-city}
                        <strong>City:</strong> {user:billing-city}
                        <br />
                    {/if}

                    {if user:billing-state}
                        <strong>State:</strong> {user:billing-state}
                        <br />
                    {/if}

                    {if user:billing-postcode}				
                        <strong>Post/ZIP code:</strong> {user:billing-postcode}
                        <br />
                    {/if}

                    {if user:billing-country}
                        <strong>Country:</strong> {user:billing-country}
                    {/if}
                </p>
            </td>
        </tr>
      </table>
      <br class="clear" />
      
      <p><a href="{site:url}shop/account/checkout">Update Address</a></p>
	
      <br />
		
      <form action="{site:url}shop/stripe_payment" method="post" class="">


          <div style="float: right; width: 48%;">
            <div class="two columns alpha cancel-order-div">
              <!--<a href="{site:url}shop/cancel">Cancel Order</a>-->
			  <input type="button" value="Cancel Order" class="" style="margin-top:-10px;" onclick="window.location.href='{site:url}shop/cancel'" />
            </div>
            <div class="three columns alpha stripe-div">
              <!-- Checkout with Stripe -->
              <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="{form:stripe_publishable_key}"
                data-amount="{stripe_amt}" 
                data-email="{user:email}"
                data-description="{site:name} Order">
              </script>
              <!-- End Stripe Implementation -->
            </div>
            <div class="three columns alpha paypal-div">
              <a href="{site:url}shop/paypal/ac">
                <img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" alt="Paypal" />
              </a>
            </div>
            <div class="clear"></div>
          </div>
          <br class="clear" />

      </form>
	
		
      <p>
        <a href="#" onclick="javascript:window.open('https://www.paypal.com/uk/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350');">
          <img src="https://www.paypal.com/en_GB/GB/i/logo/PayPal_mark_37x23.gif" alt="Payments by Paypal">
        </a> 
        <img src="{site:url}static/images/cards_visa.gif" alt="Visa Accepted" /> 
        <img src="{site:url}static/images/cards_electron.gif" alt="Visa Electron Accepted" /> 
        <img src="{site:url}static/images/cards_mastercard.gif" alt="Mastercard Accepted" />
        <img src="{site:url}static/images/cards_visadelta.gif" alt="Visa Delta Accepted" /> 
        <img src="{site:url}static/images/cards_switch.gif" alt="Switch Accepted" /> 
        <img src="{site:url}static/images/cards_maestro.gif" alt="Maestro Accepted" />
        <img src="{site:url}static/images/cards_solo.gif" alt="Solo Accepted" />
      </p>
		
		<!--
		<p>Your order will be saved on file and you will receive an email confirmation containing your order details and reference number once the payment process is completed.</p>
		
		<p>For our Postage &amp; Packing rates, Returns Procedure and other useful information please see our Terms and Conditions</a>.</p>
		-->
		
		<p>You will receive an email confirmation containing your order details and reference number once the payment process is complete. When your order ships, you will receive another email with USPS tracking information in it.</p>
      
      
      
      
        
    </div>
  </div>
{include:footer}

