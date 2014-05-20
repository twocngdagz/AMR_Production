<?php
	$route['goto/(:any)'] = "emailer/tracker/tracklink/$1";
	$route['viewemail/(:num)'] = "emailer/tracker/viewemail/$1";
	$route['subs/(:any)'] = "emailer/subscriptions/$1";
?>