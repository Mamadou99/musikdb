<a href="<?php echo $coverUrl ?>">
	<img width="88" height="88" src="<?php echo $coverUrl ?>" alt="" />
</a>

<script type="text/javascript">
	$(function() {
		$('#cover a').lightBox({
			imageLoading: '<?php echo Yii::app()->request->baseUrl; ?>/images/ajax-loader_whitebg.gif',
			imageBlank: '<?php echo Yii::app()->request->baseUrl; ?>/images/lightbox/blank.gif',
			imageBtnClose: '<?php echo Yii::app()->request->baseUrl; ?>/images/lightbox/btn-close.gif',
			imageBtnPrev: '<?php echo Yii::app()->request->baseUrl; ?>/images/lightbox/btn-prev.gif',
			imageBtnNext: '<?php echo Yii::app()->request->baseUrl; ?>/images/lightbox/btn-next.gif',
		});
	});
</script>