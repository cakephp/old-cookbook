<?php /* SVN FILE: $Id: admin_index.ctp 673 2008-10-06 14:05:17Z AD7six $ */ ?>
<h1>Revisions</h1>
<div class="container">
<?php echo $this->element('filter', array(
	'Node.sequence',
	'title',
	'lang',
	'status'
)); ?>
<table>
<?php
$pass = $this->passedArgs;
$pass['action'] = str_replace(Configure::read('Routing.admin') . '_', '', $this->action); // temp
$paginator->options(array('url' => $pass));
$th = array(
	$paginator->sort('id'),
	'Book',
	$paginator->sort('Section', 'Node.sequence'),
	$paginator->sort('title'),
	$paginator->sort('lang'),
	//$paginator->sort('under_node_id'),
	//$paginator->sort('after_node_id'),
	$paginator->sort('User', 'User.username'),
	$paginator->sort('Email', 'User.email'),
	$paginator->sort('status'),
	$paginator->sort('created'),
	//$paginator->sort('modified'),
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
	$actions = array();
	$actions[] = $html->link('V', array('action' => 'view', $Revision['id']), array('title' => 'view'));
	$actions[] = $html->link('E', array('action' => 'edit', $Revision['id']), array('title' => 'edit'));
	//$actions[] = $html->link('X', array('action' => 'delete', $Revision['id']), array('title' => 'delete'));
	/*
	if ($Revision['status'] == 'current') {
		$actions[] = $html->link('Hide', array('action' => 'hide', $Revision['id']));
	} elseif ($Revision['status'] == 'pending') {
		$actions[] = $html->link('Approve', array('action' => 'approve', $Revision['id']));
	} elseif ($Revision['status'] == 'previous') {
		$actions[] = $html->link('Revert', array('action' => 'approve', $Revision['id']));
	}
	 */
	$actions = implode(' - ', $actions);
	$tr = array(
		$html->link($Revision['id'], array('action' => 'view', $Revision['id'])),
		$book . ' (' . $collection . ')',
		$Node?$html->link($Node['sequence'], am($pass, array('page' => 1, 'node_id' => $Revision['node_id']))):'',
		//$html->link($Revision['under_node_id'], am($pass, array('page' => 1, 'under_node_id' => $Revision['under_node_id']))),
		//$html->link($Revision['after_node_id'], am($pass, array('page' => 1, 'after_node_id' => $Revision['after_node_id']))),
		$html->link($Revision['title'], array('action' => 'view', $Revision['id'])),
		$html->link($Revision['lang'], am($pass, array('page' => 1, 'lang:' . $Revision['lang']))),
		$User?$html->link($User['username'], am($pass, array('page' => 1, 'user_id' => $Revision['user_id']))):'',
		$User?'<a href="mailto:' . $User['email'] . '">' . $User['email'] . '</a>':'',
		$html->link($Revision['status'], am($pass, array('page' => 1, 'status' => $Revision['status']))),
		$html->link($Revision['created'], am($pass, array('page' => 1, 'created' => $Revision['created']))),
		//$html->link($Revision['modified'], am($pass, array('page' => 1, 'modified' => $Revision['modified']))),
		$actions
	);
	echo $html->tableCells($tr, array('class' => 'odd'), array('class' => 'even'));
}
?>
</table>
<?php echo $this->element('paging'); ?></div>