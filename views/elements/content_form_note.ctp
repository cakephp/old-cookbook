<div class="note">
	<p><?php __('Tips') ?></p>
	<ul>
	<li><?php echo sprintf(
		__('Please review %1$s to ensure consistency.', true),
		$html->link(__('the guidelines for submitting to the Cookbook', true),
		array('controller' => 'nodes', 'action' => 'view', '482'))
	) ?></li>
<?php if ($this->action == 'add' && $this->params['lang'] !== Configure::read('Languages.default')): ?>
	<li><?php echo sprintf(
		__('Before being accepted, new content must be available in %1$s. Please submit your suggestion in %2$s if possible', true),
		$html->link(__('English', true), am($this->passedArgs, array('lang' => Configure::read('Languages.default')))),
		$html->link(__('English', true), am($this->passedArgs, array('lang' => Configure::read('Languages.default'))))
	) ?></li>
<?php endif; ?>
	</ul>
</div>