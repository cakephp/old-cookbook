<?php
/**
 * Show checked paths
 * @TODO for view/element/layout errors - add the subfolder
 * @TODO no indication of plugin
 */
if (isset($paths)) {
	$paths = App::path($paths);
	echo '<br /><h3>The following paths have been checked:</h3>';
	echo '<ul><li>' . implode($paths, '</li><li>') . '</li></ul>';
}
?>
<br />
<h3>How'd you get here:</h3>
<?php
/**
 * Strip the error processing from the trace
 */
$trace = Debugger::trace();
$trace = preg_replace('@.*Object::cakeError[^\n]*@s', '', $trace);
debug($trace); // @ignore