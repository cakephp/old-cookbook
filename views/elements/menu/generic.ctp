<?php /* SVN FILE: $Id: generic.ctp 683 2008-10-25 23:05:10Z AD7six $ */
if (!isset($session)) {
	return;
}
$auth = $session->read('Auth');
if (!isset($auth['User']['Level'])) {
	$auth['User']['Level'] = 0;
}
if (isset($this->params['admin'])) {
	if ($auth['User']['Level'] >= EDITOR) {
		$menu->add(array(
			'section' => 'Main Options',
			'title' => 'Nodes',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'index', 'admin' => true)
		));
		$menu->add(array(
			'section' => 'Main Options',
			'title' => 'Revisions',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'pending', 'admin' => true)
		));
		$menu->add(array(
			'section' => 'Main Options',
			'title' => 'Comments',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'comments', 'action' => 'index', 'admin' => true)
		));
		$menu->add(array(
			'section' => 'Main Options',
			'title' => 'Images/files',
			'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'attachments', 'action' => 'index', 'admin' => true)
		));

		if ($this->name == 'Nodes') {
			$nodeId = null;
			if (isset($currentPath[0])) {
				$nodeId = $currentPath[count($currentPath) - 1]['Node']['id'];
			}
			$menu->add(array(
				'section' => 'Main Options',
				'title' => 'TOC',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'toc', 'admin' => true,
				$nodeId),
				'under' => 'Nodes'
			));
		} elseif ($this->name == 'Revisions') {
			$menu->add(array(
				'section' => 'Main Options',
				'title' => 'All',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'index', 'admin' => true),
				'under' => 'Revisions'
			));

			$menu->add(array(
				'section' => 'Main Options',
				'title' => 'Pending',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'pending', 'admin' => true),
				'under' => 'Revisions'
			));
			$menu->add(array(
				'section' => 'Main Options',
				'title' => 'Recently published',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'index', 'admin' => true,
				'status' => 'current', 'sort' => 'created', 'direction' => 'desc', 'lang:' . $this->params['lang'], 'lang' => false),
				'under' => 'Revisions'
			));
			$menu->add(array(
				'section' => 'Main Options',
				'title' => 'Invalid',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'invalid'),
				'under' => 'Revisions'
			));

		} elseif ($this->name == 'Comments') {
			$menu->add(array(
				'section' => 'Main Options',
				'title' => 'Recent',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'comments', 'action' => 'index', 'admin' => true,
				'sort' => 'created', 'direction' => 'asc'),
				'under' => 'Comments'
			));

			$menu->add(array(
				'section' => 'Main Options',
				'title' => 'Unpublished',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'comments', 'action' => 'index', 'admin' => true,
				'published' => 0),
				'under' => 'Comments'
			));
		}
	}
	if ($auth['User']['Level'] >= ADMIN) {
		if ($this->name == 'Nodes') {
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Verify Tree',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'verify_tree', 'admin' => true),
			));
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Recover Tree',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'recover_tree', 'admin' => true),
			));
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Reset Depths',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'reset_depths', 'admin' => true),
			));
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Reset Sequences',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'reset_sequences', 'admin' => true),
			));
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Export',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'export', 'admin' => true),
			));
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Import',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'import', 'admin' => true),
			));
		} elseif ($this->name == 'Revisions') {
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Reset Slugs',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'reset_slugs', 'admin' => true),
			));
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Build Search Index',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'build_index', 'admin' => true),
			));
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Delete and Build Search Index',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'build_index', 'admin' => true, 'reset'),
			));
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Check and fix current revisions',
				'url' => array('prefix' => null, 'plugin' => null, 'controller' => 'revisions', 'action' => 'reset'),
			));
		} elseif ($this->name == 'Attachments') {
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Export',
				'url' => array('prefix' => null, 'plugin' => null, 'action' => 'export', 'admin' => true),
			));
			$menu->add(array(
				'section' => 'Admin functions',
				'title' => 'Import',
				'url' => array('prefix' => null, 'plugin' => null, 'action' => 'import', 'admin' => true),
			));
		}
	}
}
?>