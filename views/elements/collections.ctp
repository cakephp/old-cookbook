<div id="main_nav">
<ul class="navigation">
<?php
$collections = cache('views/collection_' . $this->params['lang']);
if ($collections) {
	$collections = unserialize($collections);
} else {
	$__cache = Configure::read('Cache.check');
	Configure::write('Cache.check', false);
	$collections = $this->requestAction(array('plugin' => null, 'prefix' => null, 'controller' => 'nodes',
		'action' => 'collections', 'lang' => $this->params['lang']));
	Configure::write('Cache.check', $__cache);
}
$currentCollection = isset($currentPath[1])?$currentPath[1]:array('Node' => array('id' => false));
foreach ($collections as $row) {
	if ($currentCollection['Node']['id'] == $row['Node']['id']) {
		$options = array('class' => 'active');
	} else {
		$options = array();
	}
	$links[] = $html->link($row['Revision']['title'],
		array('prefix' => null, 'plugin' => null, 'controller' => 'nodes', 'action' => 'view', $row['Node']['id'], $row['Revision']['slug']),
		$options
	);
}
echo '<li>' . implode($links, '</li><li>') . '</li>';
?>
</ul>
</div>