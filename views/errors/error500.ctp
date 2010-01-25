<?php
$this->pageTitle = __('Internal Error', true);
$this->layout = 'fatal_error';
?>
<h2><?php echo $name; ?></h2><br />
<p><?php __("There's currently a server problem preventing your request from being processed."); ?></p>
<p><?php __("Please wait a few moments before trying again, if the problem persists - please contact us.")?></p><br />
<p><?php echo sprintf(__("Error Reference: %s", true), $uuid) ?></p>