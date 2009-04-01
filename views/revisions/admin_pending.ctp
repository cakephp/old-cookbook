<h1>Pending <?php echo up($language) ?> Submissions</h1>
<div class="container">
<?php
foreach ($counts as $lang => $count) {
	$menu->add(array(
		'section' => 'Options' ,
		'title' => sprintf(__n('%s pending %s submission', '%s pending %s submissions', $count, true), $count, up($lang)),
		'url' => array('language' => $lang),
		'under' => 'Pending'
	));
}
$this->set('modelClass', 'Revision');
?>
<table>
<?php
$pass = $this->passedArgs;
$pass['action'] = str_replace(Configure::read('Routing.admin') . '_', '', $this->action);
$paginator->options(array('url' => $pass));
$th = array(
	$paginator->sort('id'),
	'Book',
	$paginator->sort('Section', 'Node.sequence'),
	$paginator->sort('Title', 'slug'),
	$paginator->sort('User','User.username'),
	$paginator->sort('Email', 'User.email'),
	$paginator->sort('created'),
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
	if(empty($row['Revision']['node_id']) && !empty($row['UnderNode']['sequence']) ){
	$sequence = $html->link('{'.$row['UnderNode']['sequence'].'}', am($pass, array('page' => 1, 'node_id' => $row['UnderNode']['id'])), array('title' => 'New Section: under - '.$row['UnderNode']['sequence'].' after: '.$row['AfterNode']['sequence'] ));
	} else {
		$sequence = $html->link($row['Node']['sequence'], am($pass, array('page' => 1, 'node_id' => $row['Node']['id'])));
	}
	$tr = array (
		$html->link($row['Revision']['id'], array('action' => 'view', $row['Revision']['id'])),
		$book . ' (' . $collection . ')',
		$sequence,
		$html->link($row['Revision']['title'],array('action'=>'view',$row['Revision']['id'])),
		$row['User']?$html->link($row['User']['username'], am($pass, array('page' => 1, 'user_id' => $row['Revision']['user_id']))):'',
		$row['User']?'<a href="mailto:' . $row['User']['email'] . '">' . $row['User']['email'] . '</a>':'',
		$html->link($row['Revision']['created'], am($pass, array('page' => 1, 'created' => $row['Revision']['created']))),
	);
	echo $html->tableCells($tr);
}
?>
</table>
<?php echo $this->element('paging'); ?>
</div>