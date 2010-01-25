<h2>Missing Database Connection</h2>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('%s requires a database connection', $model);?>
</p>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('Confirm you have created the file : %s.', APP_DIR.DS.'config'.DS.'database.php');?>
</p>
<?php echo $this->element('trace');
$this->layout = 'fatal_error';