<h2>Missing Layout</h2>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf("The layout file %s can not be found or does not exist.", "<em>" . $file . "</em>");?>
</p>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('Confirm you have created the file: %s', "<em>" . $file . "</em>");?>
</p>
<?php echo $this->element('trace', array('paths' => 'views'));
$this->layout = 'fatal_error';