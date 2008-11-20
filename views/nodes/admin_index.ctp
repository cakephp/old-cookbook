<?php /* SVN FILE: $Id: admin_index.ctp 673 2008-10-06 14:05:17Z AD7six $ */ ?>
<h1>Nodes</h1>
<div class="container">
<?php
$pass = $this->passedArgs;
$pass['action'] = str_replace(Configure::read('Routing.admin') . '_', '', $this->action); // temp
$paginator->options(array('url' => $pass));
?>
<table>
<?php
$th = array(
	$paginator->sort('id'),
	'Book',
	//$paginator->sort('lft'),
	//$paginator->sort('rght'),
	//$paginator->sort('parent_id'),
	//$paginator->sort('comment_level'),
	//$paginator->sort('edit_level'),
	//$paginator->sort('view_level'),
	//$paginator->sort('depth'),
	$paginator->sort('sequence'),
	$paginator->sort('Title', 'Revision.title'),
	//$paginator->sort('created'),
	//$paginator->sort('modified'),
	'last Author',
	'flags',
	'actions'
);
echo $html->tableHeaders($th);
foreach ($data as $row) {
	extract($row);
	$collection = $book = '-';
	foreach ($collections as $c) {
		if ($c['Node']['lft'] <= $Node['lft'] && $c['Node']['rght'] >= $Node['rght']) {
			$collection = $html->link($c['Revision']['title'], am($pass, array('restrict_to' => $c['Node']['id'])));
			$collection = $html->link($c['Revision']['title'], array('restrict_to' => $c['Node']['id']));
			break;
		}
	}
	foreach ($books as $b) {
		if ($b['Node']['lft'] <= $Node['lft'] && $b['Node']['rght'] >= $Node['rght']) {
			$book = $html->link($b['Revision']['title'], am($pass, array('restrict_to' => $b['Node']['id'])));
			break;
		}
	}
	$author = isset($users[$Revision['user_id']])?$html->link($users[$Revision['user_id']], am($pass, array('Revision.user_id' => $Revision['user_id']))):'';
	$status = array();
	if (in_array($Node['id'], $pendingUpdates)) {
		$status[] = $html->link('change pending', array('controller' => 'revisions', 'action' => 'history', $Node['id'], 'status' =>
			'pending'));
	}
	$status = implode ($status, ' ');
	$actions = array();
	$actions[] = $html->link('P', array('admin' => false, 'action' => 'view', $Node['id'], $Revision['title']), array('title' => 'see public
		version'));
	$actions[] = $html->link('V', array('action' => 'view', $Node['id']), array('title' => 'view'));
	$actions[] = $html->link('E', array('admin' => false, 'action' => 'edit', $Node['id']), array('title' => 'edit'));
	$actions[] = $html->link('X', array('action' => 'delete', $Node['id']), array('title' => 'delete'));
	$actions = implode(' - ', $actions);
	$tr = array(
		$html->link($Node['id'], array('action' => 'view', $Node['id'])),
		$book . ' (' . $collection . ')',
		//$html->link($Node['lft'], am($pass, array('page' => 1, 'lft' => $Node['lft']))),
		//$html->link($Node['rght'], am($pass, array('page' => 1, 'rght' => $Node['rght']))),
		//$html->link($Node['parent_id'], am($pass, array('page' => 1, 'parent_id' => $Node['parent_id']))),
		//$html->link($Node['status'], am($pass, array('page' => 1, 'status' => $Node['status']))),
		//$html->link($Node['comment_level'], am($pass, array('page' => 1, 'comment_level' => $Node['comment_level']))),
		//$html->link($Node['edit_level'], am($pass, array('page' => 1, 'edit_level' => $Node['edit_level']))),
		//$html->link($Node['view_level'], am($pass, array('page' => 1, 'view_level' => $Node['view_level']))),
		//$html->link($Node['depth'], am($pass, array('page' => 1, 'depth' => $Node['depth']))),
		$html->link($Node['sequence'], am($pass, array('restrict_to' => $Node['id']))),
		$html->link($Revision['title'], array('action' => 'view', $Node['id'])),
		//$html->link($Node['created'], am($pass, array('page' => 1, 'created' => $Node['created']))),
		//$html->link($Node['modified'], am($pass, array('page' => 1, 'modified' => $Node['modified']))),
		$author,
		$status,
		$actions
	);
	echo $html->tableCells($tr, array('class' => 'odd'), array('class' => 'even'));
}
?>
</table>
<?php echo $this->element('paging'); ?></div>