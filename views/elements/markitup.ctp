<?php /* SVN FILE: $Id: markitup.ctp 673 2008-10-06 14:05:17Z AD7six $ */
$miJavascript->link('markitup/jquery.markitup.js', false);
$miJavascript->link('markitup/sets/default/set.js', false);
$html->css(array('/js/markitup/skins/markitup/style.css', '/js/markitup/sets/default/style.css'), null, array(), false);
$miJavascript->codeBlock(
'$(document).ready(function() {
	   $("' . $process . '").markItUp(mySettings);
});', array('inline' => false));
?>