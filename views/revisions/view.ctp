<div class="nodes view">
<?php
$compare = array();
foreach ($data as $key => $row) {
	echo '<h2>{' . up($row['Revision']['id']) . '} - ' . $row['Node']['sequence'] . ' ' . h($row['Revision']['title']) . '</h2>';
	//echo $html->clean($currentNode['Revision']['content']);
	echo '<div class="summary">' . $row['Revision']['content'] . '</div>';
	$compare[] = '<title>' . $row['Revision']['title'] . "</title>\r\n" . $row['Revision']['content'];
}
if (count($compare) > 1) {
	echo '<h2>' . __('Differences', true) . '</h2>';
	echo $diff->compare(h($compare[1]), h($compare[0]));
}
?><cake:nocache><?php
if ($session->read('Auth.User.Level') == ADMIN) {
	$menu->add(array(
		'section' => 'This Revision',
		'title' => 'Admin view',
		'url' => am(array('admin' => true, 'action' => 'view'), $this->passedArgs)
	));
	if ($row['Revision']['node_id']) {
		$menu->add(array(
			'section' => 'This Revision',
			'title' => 'See History',
			'url' => am(array('admin' => true, 'action' => 'history'), $this->passedArgs)
		));
	}
	$menu->add(array(
		'section' => 'This Revision',
		'title' => 'Edit',
		'url' => am(array('admin' => true, 'action' => 'edit'), $this->passedArgs)
	));
	if ($row['Revision']['status'] == 'pending') {
		$menu->add(array(
			'title' => 'Approve',
			'url' => am(array('admin' => true, 'action' => 'approve'), $this->passedArgs),
			'class' => 'dialogs'
		));
		$menu->add(array(
			'title' => 'Reject',
			'url' => am(array('admin' => true, 'action' => 'reject'), $this->passedArgs),
			'class' => 'dialogs'
		));
		$menu->add(array(
			'title' => 'Ignore',
			'url' => am(array('admin' => true, 'action' => 'ignore'), $this->passedArgs)
		));
	}
}
?></cake:nocache>
</div>