<div class="note">
	<p><?php __('Is a comment appropriate?') ?></p>
	<ul>
	<li><?php echo sprintf(
		__('Do you want to highlight a problem in the text? %s', true),
		$html->link(__('You can change it', true), am($this->passedArgs, array('controller' => 'nodes', 'action' => 'edit')))) ?></li>
	<li><?php echo sprintf(
		__('Do you want help with this topic? There are ways to %s and more appropriate places to %s for %s', true),
		$html->link(__('help yourself', true), array('controller' => 'nodes', 'action' => 'view', 9)),
		$html->link(__('ask', true), array('controller' => 'nodes', 'action' => 'view', 554)),
		$html->link(__('help', true), array('controller' => 'nodes', 'action' => 'view', 558))) ?></li>
	<li><?php echo sprintf(
		__('Do you want to highlight a problem with the application? %s or %s', true),
		$html->link(__('You can ticket it', true), 'http://thechaw.com/cakebook/tickets/add'),
		$html->link(__('You can fix it', true), 'http://thechaw.com/cakebook/fork/it')) ?></li>
	</ul>
</div>
