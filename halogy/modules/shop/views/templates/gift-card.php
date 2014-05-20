{include:header}
  <div id="main-content">
    <div class="container padding-adjust">
      
	  {include:left-sidebar}
      
	  <div class="twelve columns">
        <form name="" method="POST" action="" id="" class="form-data">
          	<h1>Send a Gift Card to your family or friends</h1>
			<p>{msg}</p>
			
			{if sent}
				<h3>Your Gift Card is sent!</h3>
			{else}
		  	<label for="gift_certificate_id">Select Gift Certificate: </label>
			{gc_options}
			<br class='clear' />
			
			<label for="sender_name">Your Name:</label>
			{sender_name}
			<br class="clear" />
			
			<label for="recipient_name">Recipient's Name:</label>
			{recipient_name}
			<br class="clear" />
			
			<label for="recipient_email">Recipient's Email:</label>
			{recipient_email}
			<br class="clear" />
			
			<label for="sender_message">Optional Message:</label>
			{sender_message}
			<br class="clear" />
			
			<input type="submit" value="Send Gift Card" class="" />
			{/if}
			
        </form>

      </div>
	  
    </div>
  </div>
{include:footer}