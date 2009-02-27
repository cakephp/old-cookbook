<div class="container">
<h2>Change Log</h2>
<ul id="results">
<?php
$pass = $this->passedArgs;
$paginator->options(array('url' => $pass));
foreach ($data as $row) {
	echo '<li>';
	if (in_array($row['Revision']['status'], array('current', 'previous'))) {
		echo '<h3>' . $html->link($row['Revision']['title'], array('controller' => 'revisions', 'action' => 'view', $row['Revision']['id'])) . '</h3>';
	} else {
		echo '<h3>' . $row['Revision']['title'] . '</h3>';
	}
	echo '<ul>';
	if ($row['Change']['status_from'] == 'new') {
		echo '<li>' . sprintf(__('change submitted by %s, %s', true),
			isset($row['User']['username'])?$row['User']['username']:'unknown',
			$time->niceShort($row['Change']['created'])) . '</li>';
	} else {
		switch ($row['Change']['status_to']) {
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
				$to = __($row['Change']['status_to'], true);
		}
		echo '<li>' . sprintf(__('changed from %s to %s by %s, %s', true),
			$row['Change']['status_from'],
			$to,
			isset($row['User']['username'])?$row['User']['username']:'unknown',
			$time->niceShort($row['Change']['created'])) . '</li>';
		$author = isset($row['Author']['username'])?$row['Author']['username']:'unknown';
		echo '<li>' . sprintf(__('submitted by %s', true), $author) . '</li>';
	}
	echo '<li>' . $html->clean($row['Change']['comment']) . '</li>';
	echo '</ul></li>';
}
?>
</ul>
<?php echo $this->element('paging'); ?></div>