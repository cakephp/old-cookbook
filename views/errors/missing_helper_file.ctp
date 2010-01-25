<h2>Missing Helper File</h2>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf("The helper file %s can not be found or does not exist.", APP_DIR . DS . "views" . DS . "helpers" . DS . $file);?>
</p>
<p  class="error">
	<strong>Error: </strong>
	<?php echo sprintf('Create the class below in file: %s', APP_DIR . DS . "views" . DS . "helpers" . DS . $file);?>
</p>
<pre>
&lt;?php
class <?php echo $helperClass;?> extends AppHelper {

}
?&gt;
</pre>
<?php echo $this->element('trace', array('paths' => 'helpers'));
$this->layout = 'fatal_error';