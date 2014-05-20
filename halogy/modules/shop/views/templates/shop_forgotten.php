{include:header}
  <div id="main-content">
    <div class="container padding-adjust">
      
      {include:left-sidebar}
      
      <div class="twelve columns">
        
        <h1>Forgotten Password</h1>

        {if errors}
          <div class="error">
            {errors}
          </div>
        {/if}
        
        {if message}
          <div class="success">
            {message}
          </div>
        {else}
        
          <p>Enter the email which you used to sign up and we will send out an email with instructions on how to reset your password.</p>

          <form method="post" action="{page:uri}" class="default">

            <div>
              <label for="email">Email Address:</label>
              <input type="text" name="email" class="formelement" />
            </div>
            <div>
              <label>&nbsp;</label>
              <input type="submit" value="Reset Password" class="" />
            </div>


          </form>

        {/if}


      </div>
    </div>
  </div>
  
{include:footer}