{include:header}
  <div id="main-content">
    <div class="container padding-adjust">
      
      {include:left-sidebar}
      
      <div class="twelve columns">
        <h1>Existing Customers Login</h1>
		<p>Ordered from us before? Login below or you can <a href="{site:url}shop/create_account/{signupRedirect}">CLICK HERE</a> to register a new account.</p>

		{if errors}
          <div class="error">{errors}</div>
		{/if}	
		
		<form action="{page:uri}" method="post" id="log-in-form" class="default">
          
          <div>
            <label for="email">Email Address: </label>				
            {form:email}
          </div>
          <div>
            <label for="password">Password: </label>
            {form:password}
          </div>
          <div>
            <label>&nbsp;</label>
            <input type="submit" name="login" value="Login" id="login-btn" />
          </div>
          <div>
            <label>&nbsp;</label>
            Forgot password? <a href="{site:url}shop/forgotten">Reset</a> No account? <a href="{site:url}shop/create_account/{signupRedirect}">Register</a>
          </div>
          
		</form>
		<br />
        
		{if express_checkout}
		<h1>Proceed Directly To Checkout</h1>
		<p>If you want to checkout without registering, fill up fields below and submit.</p>
		
		<form method="post" action="{page:uri}" class="default">
			<h2>Your Details</h2>		
		
			<label for="firstName">First Name:</label>
			<input type="text" name="firstName" value="{form:firstName}" id="firstName" class="formelement" />
			<br class="clear" />
		
			<label for="lastName">Last Name:</label>
			<input type="text" name="lastName" value="{form:lastName}" id="lastName" class="formelement" />
			<br class="clear" />

			<label for="phone">Phone:</label>
			<input type="text" name="phone" value="{form:phone}" id="phone" class="formelement" />
			<br class="clear" />
			
			<label for="email">Email:</label>
			<input type="text" name="email" value="{form:emailreg}" id="email" class="formelement" />
			<br class="clear" />
			
			<h2>Delivery Address</h2>
			
			<label for="address1">Address 1:</label>
			<input type="text" name="address1" value="{form:address1}" id="address1" class="formelement" />
			<br class="clear" />
		
			<label for="address2">Address 2:</label>
			<input type="text" name="address2" value="{form:address2}" id="address2" class="formelement" />
			<br class="clear" />
		
			<label for="address3">Address 3:</label>
			<input type="text" name="address3" value="{form:address3}" id="address3" class="formelement" />
			<br class="clear" />
		
			<label for="city">City:</label>
			<input type="text" name="city" value="{form:city}" id="city" class="formelement" />
			<br class="clear" />

			<label for="state">State:</label>
			{select:state}
			<br class="clear" />
		
			<label for="postcode">ZIP/Post code:</label>
			<input type="text" name="postcode" value="{form:postcode}" id="postcode" class="formelement" />
			<br class="clear" />
		
			<label for="country">Country:</label>
			{select:country}
			<br class="clear" />

			<h2>Billing Address</h2>

			<p><input type="checkbox" name="sameAddress" value="1" class="checkbox" id="sameAddress" />
			My billing address is the same as my delivery address.</p>

			<div id="billing">

				<label for="billingAddress1">Address 1:</label>
				<input type="text" name="billingAddress1" value="{form:billingAddress1}" id="billingAddress1" class="formelement" />
				<br class="clear" />
			
				<label for="billingAddress2">Address 2:</label>
				<input type="text" name="billingAddress2" value="{form:billingAddress2}" id="billingAddress2" class="formelement" />
				<br class="clear" />
			
				<label for="billingAddress3">Address 3:</label>
				<input type="text" name="billingAddress3" value="{form:billingAddress3}" id="billingAddress3" class="formelement" />
				<br class="clear" />
			
				<label for="billingCity">City:</label>
				<input type="text" name="billingCity" value="{form:billingCity}" id="billingCity" class="formelement" />
				<br class="clear" />

				<label for="billingState">State:</label>
				{select:billingState}
				<br class="clear" />
			
				<label for="billingPostcode">ZIP/Post code:</label>
				<input type="text" name="billingPostcode" value="{form:billingPostcode}" id="billingPostcode" class="formelement" />
				<br class="clear" />
			
				<label for="billingCountry">Country:</label>
				{select:billingCountry}
				<br class="clear" /><br />
				
			</div>
			
			<input type="hidden" name="checkoutNoRegister" value="1" />
            <label>&nbsp;</label>
			<input type="submit" value="Checkout" class="" />
			
		</form>
		
		{else}
		<h1>New Customers Create Account</h1>
        <p>You must create an account in order to purchase from this site. </p>
        <p><a href="{site:url}shop/create_account/{signupRedirect}">Create Account</a></p>
		{/if}
		
		
      </div>
    </div>
  </div>
  
  <script type="text/javascript">
	  $(function(){
		
	});

    function hideAddress()
    {
      if (
            $('input#billingAddress1').val() == $('input#address1').val() &&
            $('input#billingAddress2').val() == $('input#address2').val() &&
            $('input#billingAddress3').val() == $('input#address3').val() &&
            $('input#billingCity').val() == $('input#city').val() &&
            $('select#billingState').val() == $('select#state').val() &&
            $('input#billingPostcode').val() == $('input#postcode').val() &&
            $('select#billingCountry').val() == $('select#country').val()
        ){
            $('div#billing').hide();
            $('input#sameAddress').attr('checked', true);
        }
    }
    $(function(){
        $('input#sameAddress').click(function(){
            $('div#billing').toggle(200);
            $('input#billingAddress1').val($('input#address1').val());
            $('input#billingAddress2').val($('input#address2').val());
            $('input#billingAddress3').val($('input#address3').val());
            $('input#billingCity').val($('input#city').val());
            $('select#billingState').val($('select#state').val());
            $('input#billingPostcode').val($('input#postcode').val());
            $('select#billingCountry').val($('select#country').val());
        });
    });
  </script>
  
{include:footer}