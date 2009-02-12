<div class="note">
	<p><?php __('Tips') ?></p>
	<ul>
	<li><?php echo sprintf(
		__('Please review the %s for submitting to the Cookbook to ensure consistency.', true),
		$html->link(__('Guidelines', true), array('controller' => 'nodes', 'action' => 'view', '482'))) ?></li>
<?php if ($this->params['lang'] != Configure::read('Languages.default')): ?>
	<li><?php echo sprintf(
		__('Before being accepted, new content must be available in %s. Please submit your suggestion in %s if possible', true),
		$html->link(__('English', true), am($this->passedArgs, array('lang' => Configure::read('Languages.default')))),
		$html->link(__('English', true), am($this->passedArgs, array('lang' => Configure::read('Languages.default'))))
	) ?></li>
<?php endif; ?>
	</ul>
</div>