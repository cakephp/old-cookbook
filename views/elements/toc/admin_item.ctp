<?php
extract($data);
extract($Revision);
extract($Node);
echo $html->link($sequence . ' ' . $title, array($id), array ('title' => 'view \'' . $title . '\'.')) . ' ';
$links = array();
$links[] = $html->link('view', array ('admin' => false, 'action' => 'view', $id, $slug), array ('title' => 'View public content'));
$links[] = $html->link('edit', array ('admin' => false, 'action' => 'edit', $id, $slug), array ('title' => 'Edit content'));
$links[] = $html->link('history', array ('controller' => 'revisions', 'action' => 'history', $id, $slug), array ('title' => 'See History'));
if ($auth['User']['Level'] > COMMENTER) {
	$links[] = $html->link('props', array ('action' => 'edit', $id), array ('title' => 'Edit the stuctural properties'));
	if ($Node['parent_id']) {
		if ($Node['depth'] > 1) {
			$links[] = $html->link('←', array ('action' => 'promote', $id), array ('title' => 'Promote, along with any children'));
		}
	}
}
if (!$firstChild) {
	$links[] = $html->link('↑', array ('action' => 'move_up', $id), array ('title' => 'Move Previous - Up the tree'));
	$links[] = $html->link('↑↑↑', array ('action' => 'move_up', $id, 100), array ('title' => 'Move First - Up the tree'));
}
if (!$lastChild) {
	$links[] = $html->link('↓', array ('action' => 'move_down', $id), array ('title' => 'Move After - Down the tree'));
	$links[] = $html->link('↓↓↓', array ('action' => 'move_down', $id, 100), array ('title' => 'Move Last - Down the tree'));
}
if ($Node['parent_id']) {
	$links[] = $html->link('move anywhere', array ('action' => 'move', $id), array ('title' => 'Move Somewhere else'));
}
$links[] = $html->link('merge', array ('action' => 'merge', $id), array ('title' => 'Move this content to a different place'));
if ($auth['User']['Level'] >= COMMENTER) {
	$links[] = $html->link('delete', array ('action' => 'delete', $id), array ('title' => 'delete node \'' . $title . '\' and all children.'));
	if ($Node['parent_id']) {
		if ($hasChildren) {
			$links[] = $html->link('remove', array ('action' => 'remove',	$id, 'true'), array ('title' => 'remove node \'' . $title . '\' from the tree reparenting children and delete \'' . $title . '\'.'));
		}
	}
}

if ($links) {
	echo '<ul class=\'tree-options\'><li>' . implode ($links, '</li><li>') . '</li></ul>';
}
?>