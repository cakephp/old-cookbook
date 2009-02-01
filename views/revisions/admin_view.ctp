<div class="revisions view">
<?php
echo '<h2>Revision Details</h2>';
if(empty($data['Revision']['node_id']) && !empty($data['Revision']['under_node_id'])) {
	echo '<p>A new section under '
		. $html->link($data['UnderNode']['sequence'], array('admin' => false, 'controller' => 'nodes', 'action' => 'view', $data['UnderNode']['id']))
		. ' after '.$data['AfterNode']['sequence']. '</p>';
}
echo '<p>' . up($data['Revision']['lang']) .
	' Submission by ' .
	$html->link($user['username'], 'mailto:' . $user['email']) . ' to ' .
	$html->link($data['Node']['sequence'] . ' ' . $data['Revision']['title'],
		array('admin' => false, 'controller' => 'nodes', 'action' => 'view', 'lang' => $data['Revision']['lang'], $data['Node']['id'])) . ' ' .
	$time->timeAgoInWords($data['Revision']['created']) . '</p>';
if(!empty($data['Revision']['reason'])) {
	echo '<p>Reason: ' . htmlspecialchars($data['Revision']['reason']) . '</p>';
}
echo "<h2>" . $data['Revision']['title'] . "</h2>";
echo $this->element('node_options', array (
	'nodeData' => $data['Node'],
	'menuItems' => array (
		'toc' => array (
			'action' => 'toc',
			'#' => $data['Revision']['slug']
		)
	)
));
echo $data['Revision']['content'];
$revisionContent = htmlspecialchars_decode('<title>' . $data['Revision']['title'] . "</title>\r\n" . $data['Revision']['content']);
if ($data['Revision']['node_id'] && isset($current) && $data['Revision']['id'] != $current['Revision']['id']) {
	echo '<hr />';
	echo '<h2>{Current} ' . $current['Revision']['title'] . '</h2>';
	echo $current['Revision']['content'];
	$currentContent = htmlspecialchars_decode('<title>' . $current['Revision']['title'] . "</title>\r\n" . $current['Revision']['content']);
	echo '<hr />';
	echo '<h2>Changes</h2>';
	echo $diff->compare(htmlspecialchars($currentContent),htmlspecialchars($revisionContent));
}

echo $this->element('node_navigation');
if ($data['Revision']['node_id']) {
 $menu->add(array(
	 'section' => 'This Revision',
	 'title' => 'See History',
	 'url' => array('action' => 'history', $data['Revision']['node_id'])
	));
}
$menu->add(array(
	 'section' => 'This Revision',
	'title' => 'Edit',
	'url' => array('action' => 'edit', $data['Revision']['id'])
));
if ($data['Revision']['status'] == 'current') {
	$menu->add(array(
		'title' => 'Unapprove',
		'url' => array('action' => 'hide', $data['Revision']['id'])
	));
} elseif ($data['Revision']['status'] == 'pending') {
	$menu->add(array(
		'title' => 'Approve',
		'url' => array('action' => 'approve', $data['Revision']['id'])
	));
	$menu->add(array(
		'title' => 'Reject',
		'url' => array('action' => 'reject', $data['Revision']['id'])
	));
	$menu->add(array(
		'title' => 'Ignore',
		'url' => array('action' => 'ignore', $data['Revision']['id'])
	));
} elseif ($data['Revision']['status'] == 'previous') {
	$menu->add(array(
		'title' => 'Revert',
		'url' => array('action' => 'approve', $data['Revision']['id'])
	));
}
?>
</div>