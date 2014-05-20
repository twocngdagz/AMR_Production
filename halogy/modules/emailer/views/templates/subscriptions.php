{include:header}

<h1>Email List Subscriptions</h1>

{if errors}
	<div class="error">
		{errors}
	</div>
{/if}
{if message}
	<div class="message">
		{message}
	</div>
{/if}

{if emailer:lists}
	<form method="post" action="{page:uri}" class="default">
	
		<input type="hidden" name="email" value="{userdata:email}" />
		<input type="hidden" name="name" value="{userdata:name}" />
	
		{emailer:lists}
			<input type="checkbox" name="lists[]" value="{list:id}" id="list{list:id}" {list:subscribed} />
			<label for="list{list:id}" class="checkbox">{list:title}</label>
			<br class="clear" />
		{/emailer:lists}
	
		<br />
	
		<input type="submit" value="Save Changes" class="button" />
		<br class="clear" />
	
	</form>
	
	<p><a href="/emailer/unsubscribe/{emailer:key}">Unsubscribe From All</a></p>
{else}

	<p>There are no lists to subscribe to.</p>

{/if}

{include:footer}