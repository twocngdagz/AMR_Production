	<script language="javascript" type="text/javascript">
	$(function(){
		doLoad = function(emailID) {
			$('div.hidden').load('<?php echo site_url('/admin/emailer/viewsend_ajax'); ?>/'+emailID, function(data){
				$('div#test').css({ width : data+'%' });
			});
		}

		$('select#selectemail').change(function(){
			var emailID = ($(this).val());
			repeatLoad(emailID);
		});

		var repeat;

		repeatLoad = function(emailID) {
			doLoad(emailID);
			clearInterval(repeat);
			repeat = setInterval('doLoad('+emailID+')', 60000);
		}

		var emailID = <?php echo $emailID; ?>;
		
		repeatLoad(emailID);	
	});
	</script>

<h1>View Email Send Progress <small>(<a href="<?php echo site_url('/admin/emailer/campaigns'); ?>">Back to Campaigns</a>)</small></h1>

<br />

<div class="hidden">
	<?php if ($errors = validation_errors()): ?>
		<div class="error">
			<?php echo $errors; ?>
		</div>
	<?php endif; ?>
</div>

<div style="width: 100%; height: 25px; border: 1px solid #ccc;">
	<div id="test" style="width: 0px; height: 25px; background: #42AF41;"></div>
</div>

