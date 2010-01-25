<h2>Missing Behavior File</h2>
<p class="error">
	<strong>Error: </strong>
	The behavior file was not found.
</p>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('Create the class %s in file: %s', "<em>" . $behaviorClass . "</em>", APP_DIR . DS . "models" . DS . "behaviors" . DS . $file);?>
</p>
<pre>
&lt;?php
class <?php echo $behaviorClass;?> extends ModelBehavior {<br />

}
?&gt;
</pre>
<?php echo $this->element('trace', array('paths' => 'behaviors'));
$this->layout = 'fatal_error';