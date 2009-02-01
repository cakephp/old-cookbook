<div id="main_nav"><?php
$auth = $session->read('Auth');
if (!isset($auth['User']['Level'])) {
	$auth['User']['Level'] = 0;
}
$menu->settings('Top', array('activeMode' => 'controller', 'class' => 'navigation'));
$menu->add(array(
	'section' => 'Top',
	'title' => 'Nodes',
	'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'index', 'admin' => true)
));
$menu->add(array(
	'title' => 'Revisions',
	'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'pending', 'admin' => true)
));
$menu->add(array(
	'title' => 'Comments',
	'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'comments', 'action' => 'index', 'admin' => true)
));
$menu->add(array(
	'title' => 'Images/files',
	'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'attachments', 'action' => 'index', 'admin' => true)
));
if ($this->name == 'Nodes') {
	$nodeId = null;
	if (isset($currentPath[0])) {
		$nodeId = $currentPath[count($currentPath) - 1]['Node']['id'];
	}
	$menu->add(array(
		'section' => 'Options',
		'title' => 'TOC',
		'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'toc', 'admin' => true,
		$nodeId),
	));
} elseif ($this->name == 'Revisions') {
	$menu->add(array(
		'section' => 'Options',
		'title' => 'All',
		'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'index', 'admin' => true),
	));
	$menu->add(array(
		'section' => 'Options',
		'title' => 'Pending',
		'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'pending', 'admin' => true),
	));
	$menu->add(array(
		'section' => 'Options',
		'title' => 'Invalid',
		'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'invalid'),
	));
} elseif ($this->name == 'Comments') {
	$menu->add(array(
		'section' => 'Options',
		'title' => 'Recent',
		'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'comments', 'action' => 'index', 'admin' => true,
		'sort' => 'created', 'direction' => 'asc'),
	));

	$menu->add(array(
		'section' => 'Options',
		'title' => 'Unpublished',
		'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'comments', 'action' => 'index', 'admin' => true,
		'published' => 0),
	));
}
$sections = $menu->sections();
if (in_array('This ' . $modelClass, $sections)) {
	$menu->settings('This ' . $modelClass, array('order' => -1)); // render first
}
if ($auth['User']['Level'] >= ADMIN) {
	$menu->settings('Admin', array('order' => 99)); // render last
	if ($this->name == 'Nodes') {
		$menu->add(array(
			'section' => 'Admin', // __('Admin')
			'title' => 'Verify Tree',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'verify_tree', 'admin' => true),
		));
		$menu->add(array(
			'title' => 'Recover Tree',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'recover_tree', 'admin' => true),
		));
		$menu->add(array(
			'title' => 'Reset Depths',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'reset_depths', 'admin' => true),
		));
		$menu->add(array(
			'title' => 'Reset Sequences',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'reset_sequences', 'admin' => true),
		));
		$menu->add(array(
			'title' => 'Export',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'export', 'admin' => true),
		));
		$menu->add(array(
			'title' => 'Import',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'import', 'admin' => true),
		));
	} elseif ($this->name == 'Revisions') {
		$menu->add(array(
			'section' => 'Admin',
			'title' => 'Reset Slugs',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'reset_slugs', 'admin' => true),
		));
		$menu->add(array(
			'title' => 'Build Search Index',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'build_index', 'admin' => true),
		));
		$menu->add(array(
			'title' => 'Delete and Build Search Index',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'build_index', 'admin' => true, 'reset'),
		));
		$menu->add(array(
			'title' => 'Check and fix current revisions',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'reset'),
		));
	} elseif ($this->name == 'Attachments') {
		$menu->add(array(
			'section' => 'Admin',
			'title' => 'Export',
			'url' => array('prefix' => null, 'plugin' => null, 'action' => 'export', 'admin' => true),
		));
		$menu->add(array(
			'title' => 'Import',
			'url' => array('prefix' => null, 'plugin' => null, 'action' => 'import', 'admin' => true),
		));
	}
}
echo $menu->display('Top');
?></div>