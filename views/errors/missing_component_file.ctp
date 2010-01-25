<h2>Missing Component File</h2>
<p class="error">
	<strong>Error: </strong>
	The component file was not found.
</p>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('Create the class %s in file: %s', "<em>" . $component . "Component</em>", APP_DIR . DS . "controllers" . DS . "components" . DS . $file);?>
</p>
<pre>
&lt;?php
class <?php echo $component;?>Component extends Object {<br />

}
?&gt;
</pre>
<?php echo $this->element('trace', array('paths' => 'components'));
$this->layout = 'fatal_error';