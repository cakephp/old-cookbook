<h2>Missing Controller</h2>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('%s could not be found.', "<em>" . $controller . "</em>");?>
</p>
<p class="error">
	<strong>Error: </strong>
	Bake your controller, and the model if missing, with the following commands:
</p>
<?php
$theme = 'mi';
$controller = Inflector::underscore(str_replace('Controller', '', $controller));
if (in_array($controller, array('contact', 'users'))) {
	$theme = $controller;
}
?>
<br />
<pre>
cake bake model <?php echo Inflector::singularize($controller);?> -theme <?php echo $theme;?>

cake bake controller <?php echo $controller;?> admin -theme <?php echo $theme;?>
</pre>
<?php echo $this->element('trace', array('paths' => 'controllers'));
$this->layout = 'error';