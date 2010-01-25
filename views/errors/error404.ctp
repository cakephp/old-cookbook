<?php
$this->pageTitle = __('Not Found', true);
$this->layout = 'fatal_error';
?>
<h2><?php echo $name; ?></h2>
<p class="error">
	<strong><?php __('Error'); ?>: </strong>
	<?php echo sprintf(__("The requested address %s was not found on this server.", true), "<strong>'{$message}'</strong>")?>
</p>