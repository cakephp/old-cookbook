<?php
$params = array();
if (!isset($pathIds) && isset ($currentPath)) {
	$pathIds = Set::extract($currentPath, '{n}.Node.id');
	$this->set('pathIds', $pathIds);
}
$params['id'] = 'toc-' . $data['Revision']['slug'] . '-' . $data['Node']['id'];
if (isset($pathIds)) {
	if ($data['Node']['id'] == $pathIds[count($pathIds) - 1]) {
		$params['class'] = 'selected';
	}
}
if ($this->action == 'complete' && $data['Node']['lft'] >= $currentNode['lft'] && $data['Node']['rght'] <= $currentNode['rght']) {
	echo $html->link($data['Node']['sequence'] . ' ' . $data['Revision']['title'], '#' . $data['Revision']['slug'] . '-' . $data['Node']['id'],
		$params);
	return;
}
echo $html->link($data['Node']['sequence'] . ' ' . $data['Revision']['title'],
	array('action'=>'view', $data['Node']['id'], $data['Revision']['slug']), $params);
?>