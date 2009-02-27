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
	$paginator->sort('sequence'),
	$paginator->sort('Title', 'Revision.title'),
	'last Author',
	'flags',
);
echo $html->tableHeaders($th);
foreach ($data as $row) {
	$collection = $book = '-';
	foreach ($collections as $c) {
		if ($c['Node']['lft'] <= $row['Node']['lft'] && $c['Node']['rght'] >= $row['Node']['rght']) {
			$collection = $html->link($c['Revision']['title'], am($pass, array('restrict_to' => $c['Node']['id'])));
			$collection = $html->link($c['Revision']['title'], array('restrict_to' => $c['Node']['id']));
			break;
		}
	}
	foreach ($books as $b) {
		if ($b['Node']['lft'] <= $row['Node']['lft'] && $b['Node']['rght'] >= $row['Node']['rght']) {
			$book = $html->link($b['Revision']['title'], am($pass, array('restrict_to' => $b['Node']['id'])));
			break;
		}
	}
	$author = isset($users[$row['Revision']['user_id']])?$html->link($users[$row['Revision']['user_id']], am($pass, array('Revision.user_id' => $row['Revision']['user_id']))):'';
	$status = array();
	if (in_array($row['Node']['id'], $pendingUpdates)) {
		$status[] = $html->link('change pending', array('controller' => 'revisions', 'action' => 'history', $row['Node']['id'], 'status' =>
			'pending'));
	}
	$status = implode ($status, ' ');
	$tr = array(
		$html->link($row['Node']['id'], array('action' => 'view', $row['Node']['id'])),
		$book . ' (' . $collection . ')',
		$html->link($row['Node']['sequence'], am($pass, array('restrict_to' => $row['Node']['id']))),
		$html->link($row['Revision']['title'], array('action' => 'view', $row['Node']['id'])),
		$author,
		$status,
	);
	echo $html->tableCells($tr, array('class' => 'odd'), array('class' => 'even'));
}
?>
</table>
<?php echo $this->element('paging'); ?></div>