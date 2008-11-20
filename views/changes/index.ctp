<?php /* SVN FILE: $Id: index.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
<div class="container">
<h2>Change Log</h2>
<ul id="results">
<?php
$pass = $this->passedArgs;
$paginator->options(array('url' => $pass));
foreach ($data as $row) {
	extract($row);
	echo '<li>';
	if (in_array($Revision['status'], array('current', 'previous'))) {
		echo '<h3>' . $html->link($Revision['title'], array('controller' => 'revisions', 'action' => 'view', $Revision['id'])) . '</h3>';
	} else {
		echo '<h3>' . $Revision['title'] . '</h3>';
	}
	echo '<ul>';
	if ($Change['status_from'] == 'new') {
		echo '<li>' . sprintf(__('change submitted by %s, %s', true),
			isset($User['username'])?$User['username']:'unknown',
			$time->niceShort($Change['created'])) . '</li>';
	} else {
		switch ($Change['status_to']) {
			case 'accepted';
				$to = __('accepted', true);
				break;
			case 'rejected';
				$to = __('not accepted', true);
				break;
			case 'pending';
				$to = __('pending', true);
				break;
			default:
				$to = __($Change['status_to'], true);
		}
		echo '<li>' . sprintf(__('changed from %s to %s by %s, %s', true),
			$Change['status_from'],
			$to,
			isset($User['username'])?$User['username']:'unknown',
			$time->niceShort($Change['created'])) . '</li>';
		$author = isset($Author['username'])?$Author['username']:'unknown';
		echo '<li>' . sprintf(__('submitted by %s', true), $author) . '</li>';
	}
	echo '<li>' . $html->clean($Change['comment']) . '</li>';
	echo '</ul></li>';
}
?>
</ul>
<?php echo $this->element('paging'); ?></div>