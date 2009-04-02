<h1><?php echo up($language) ?> Comments</h1>
<div class="container">
<?php
$links = array();
foreach ($counts as $lang => $count) {
	$menu->add(array(
		'section' => 'Options',
		'title' => sprintf(__n('%1$s %2$s comment', '%1$s %2$s comments', $count, true), $count, up($lang)),
		'url' => array('language' => $lang),
		'under' => 'Comments'
	));
}
$pass = $this->passedArgs;
$paginator->options(array('url' => $pass));
?>
<table>
<?php
$th = array(
	'Book',
	$paginator->sort('Section', 'Node.sequence'),
	$paginator->sort('Comment Title', 'title'),
	$paginator->sort('User', 'User.username'),
	$paginator->sort('email'),
	$paginator->sort('published'),
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
	$tr = array(
		$book . ' (' . $collection . ')',
		$row['Node']?$html->link($row['Node']['sequence'] . ' ' . $row['Revision']['title'], am($pass, array('page' => 1, 'node_id' => $row['Comment']['node_id']))):'',
		$html->link($row['Comment']['title'], array('action' => 'view', $row['Comment']['id'])),
		$User?$html->link($User['username'], am($pass, array('page' => 1, 'user_id' => $row['Comment']['user_id']))):'',
		$html->link($row['Comment']['email'], am($pass, array('page' => 1, 'email' => $row['Comment']['email']))),
		$html->link($row['Comment']['published'], am($pass, array('page' => 1, 'published' => $row['Comment']['published']))),
		$html->link($row['Comment']['created'], am($pass, array('page' => 1, 'created' => $row['Comment']['created']))),
	);
	echo $html->tableCells($tr, array('class' => 'odd'), array('class' => 'even'));
}
?>
</table>
<?php echo $this->element('paging'); ?></div>