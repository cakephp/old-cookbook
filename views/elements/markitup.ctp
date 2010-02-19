<?php /* SVN FILE: $Id: markitup.ctp 673 2008-10-06 14:05:17Z AD7six $ */
$asset->js('markitup/jquery.markitup.js', false);
$asset->js('markitup/sets/default/set.js', false);
$asset->css(
	array('/js/markitup/skins/markitup/style', '/js/markitup/sets/default/style'), 
	'stylesheet',
	array('media' => 'screen')
);
$asset->codeBlock(
'$(document).ready(function() {
	   $("' . $process . '").markItUp(mySettings);
});', array('inline' => false));
?>