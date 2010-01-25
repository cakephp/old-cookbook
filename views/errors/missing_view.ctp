<h2>Missing View</h2>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('The view for %1$s%2$s was not found.', "<em>" . $controller . "Controller::</em>", "<em>". $action . "()</em>");?>
</p>
<p class="error">
	<strong>Error: </strong>
	Bake your controller with the following command:
</p>
<?php
$theme = 'mi';
if (in_array(Inflector::underscore($controller), array('contact', 'users'))) {
	$theme = Inflector::underscore($controller);
}
?>
<br />
<pre>
cake bake view <?php echo $controller;?> $action -theme <?php echo $theme;?>
</pre>

<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('Confirm you have created the file: %s', $file);?>
</p>
<?php echo $this->element('trace', array('paths' => 'views'));
$this->layout = 'error';