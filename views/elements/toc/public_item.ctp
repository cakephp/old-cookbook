<?php
$params = array();
extract($data);
if (!isset($pathIds) && isset ($currentPath)) {
	$pathIds = Set::extract($currentPath, '{n}.Node.id');
	$this->set('pathIds', $pathIds);
}
$params['id'] = 'toc-' . $Revision['slug'] . '-' . $Node['id'];
if (isset($pathIds)) {
	if ($Node['id'] == $pathIds[count($pathIds) - 1]) {
		//$tree->addItemAttribute('class', 'selected');
		$params['class'] = 'selected';
	}
}
if ($this->action == 'complete' && $Node['lft'] >= $currentNode['lft'] && $Node['rght'] <= $currentNode['rght']) {
	echo $html->link($Node['sequence'] . ' ' . $Revision['title'], '#' . $Revision['slug'] . '-' . $Node['id'],
		$params);
	return;
}
//if ($Node['depth'] < $viewAllLevel || !isset($lastNode)) {
	echo $html->link($Node['sequence'] . ' ' . $Revision['title'],
		array('action'=>'view', $Node['id'], $Revision['slug']), $params);
/*	$this->set('lastNode', $data);
} else {
	echo $html->link($Node['sequence'] . ' ' . $Revision['title'],
		array('action'=>'view', $lastNode['Node']['id'], $lastNode['Revision']['slug'], '#' => $Revision['slug'] . '-' . $Node['id']), $params);
}
 */
?>