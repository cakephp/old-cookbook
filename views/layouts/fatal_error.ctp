<?php
/**
 * This file has no dependencies beyond the html helper
 * This ensures that irrespective of the error, it can be displayed
 * It's used in production model for a serious error such as no DB connection
 */

/**
 * Reset buffer level - Clear any existing buffered output so the error is readable
 * (e.g. From a request action call, or from an error triggered in a view)
 */
while(ob_get_level()) {
	ob_end_clean();
}
ob_start();

echo $html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php echo $html->charset(); ?>
		<title><?php echo htmlspecialchars($title_for_layout); ?></title>
		<?php
		echo $html->meta('icon');
		echo $html->css('default');
		?>
	</head>
	<body>
		<div id="container">
			<div id='header' class='clearfix'>
				<div id='navcontainer'><?php
				?></div>
			</div>
			<div id="lowerMenu"><?php
				if (Configure::read() && !empty($plugin)) {
					echo $this->element('plugin_check');
				}
				echo $content_for_layout;
			?></div>
		</div>
	</body>
</html>