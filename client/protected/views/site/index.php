<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/css/welcome.css");

$script = <<<POPUP
	element = $("#launch_app");
	element.click(function() {

		if($('#popup_check').is(':checked')) {
			// Open popup
			window.open(element.attr('href'), 'webapp',
				'width=1000,height=650,scrollbars=yes,resizable=yes');
			return false;
		}
		else {
			// Use the same window
			window.location(element.attr('href'));
		}
	});
POPUP;

Yii::app()->clientScript->registerScript('popup', $script);
?>

<div id="welcome" class="centerscreen">
	<h1>Welcome</h1>
	<div id="menu">
		<p><a class="button" id="launch_app" href="<?php echo Yii::app()->createUrl('/site/app'); ?>">Web Player</a></p>
		<div class="launch_in_popup">
			<input id="popup_check" name="popup_check" type="checkbox" value="" />
			<label for="popup_check">Launch in popup</label>
		</div>
	</div>
	<p class="secondary_menu"><?php if(Yii::app()->user->isAdmin()): ?>
		<a href="<?php echo Yii::app()->request->baseUrl.'/backend.php' ?>">Administration</a> |<?php endif; ?>
	<a href="<?php echo Yii::app()->createUrl('/site/logout'); ?>">Logout</a></p>
</div>