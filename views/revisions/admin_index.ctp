<h1>Revisions</h1>
<div class="container">
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
	$tr = array(
		$html->link($row['Revision']['id'], array('action' => 'view', $row['Revision']['id'])),
		$book . ' (' . $collection . ')',
		$row['Node']?$html->link($row['Node']['sequence'], am($pass, array('page' => 1, 'node_id' => $row['Revision']['node_id']))):'',
		$html->link($row['Revision']['title'], array('action' => 'view', $row['Revision']['id'])),
		$html->link($row['Revision']['lang'], am($pass, array('page' => 1, 'lang:' . $row['Revision']['lang']))),
		$User?$html->link($User['username'], am($pass, array('page' => 1, 'user_id' => $row['Revision']['user_id']))):'',
		$User?'<a href="mailto:' . $User['email'] . '">' . $User['email'] . '</a>':'',
		$html->link($row['Revision']['status'], am($pass, array('page' => 1, 'status' => $row['Revision']['status']))),
		$html->link($row['Revision']['created'], am($pass, array('page' => 1, 'created' => $row['Revision']['created']))),
	);
	echo $html->tableCells($tr, array('class' => 'odd'), array('class' => 'even'));
}
?>
</table>
<?php echo $this->element('paging'); ?></div>