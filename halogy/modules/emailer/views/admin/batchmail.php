<script type="text/javascript">
var sURL = unescape(window.location.pathname);
function refresh(){
	window.location.href = sURL;
	timeoutID = setTimeout(refresh, 60000);
}

$(function(){
	timeoutID = setTimeout(refresh, 60000);
});
</script>

<?php echo $message; ?>