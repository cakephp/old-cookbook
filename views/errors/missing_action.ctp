<h2><?php echo sprintf('Missing Method in %s', $controller);?></h2>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('The action %1$s is not defined in controller %2$s', "<em>" . $action . "</em>", "<em>" . $controller . "</em>");?>
</p>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('Create %1$s%2$s in file: %3$s.', "<em>" . $controller . "::</em>", "<em>" . $action . "()</em>", APP_DIR . DS . "controllers" . DS . Inflector::underscore($controller) . ".php");?>
</p>
<pre>
&lt;?php
class <?php echo $controller;?> extends AppController {

	var $name = '<?php echo $controllerName;?>';

<strong>
	function <?php echo $action;?>() {

	}
</strong>
}
?&gt;
</pre>
<?php echo $this->element('trace');
$this->layout = 'error';