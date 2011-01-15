<script type="text/javascript">
	// Immediately apply the new settings
	crossfadeTime = <?php echo $model->crossfadeTime ?>;
	transcodingBitrate = <?php echo $model->transcodingBitrate ?>;
	alwaysTranscode = <?php echo ($model->alwaysTranscode) ? '1' : '0' ?>;
	serverBaseUrl = "<?php echo $model->server->baseUrl ?>";

	// Hide status message after a couple of seconds
	$("#saveStatus").show();
	$("#saveStatus").delay(2000).fadeOut(2000);
</script>

<?php if($status===0): ?>
<p><strong>The settings have been saved successfully!</strong></p>
<?php endif; ?>

<?php if($status===1): ?>
<p class="error">An error occured. Please check your input.</p>
<?php endif; ?>

<?php if($status===null): ?>
<p class="error">An unknown error occured. Please contact the developer.</p>
<?php endif; ?>