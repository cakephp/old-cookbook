<?php /* SVN FILE: $Id: admin_index.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
<h1><?php echo up($language) ?> Comments</h1>
<div class="container">
<?php
$links = array();
foreach ($counts as $lang => $count) {
	$links[] = $html->link(sprintf(__n('%s %s comment', '%s %s comments', $count, true), $count, up($lang)), array('lang' => $lang));
}
if ($links) {
	echo '<p>' . implode($links, ', ') . '</p>';
}
$pass = $this->passedArgs;
$pass['action'] = str_replace(Configure::read('Routing.admin') . '_', '', $this->action); // temp
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
	$actions[] = $html->link('V', array('action' => 'view', $Comment['id']), array('title' => 'view'));
	$actions[] = $html->link('E', array('action' => 'edit', $Comment['id']), array('title' => 'edit'));
	$actions[] = $html->link('X', array('action' => 'delete', $Comment['id']), array('title' => 'delete'));
	$actions = implode(' - ', $actions);
	$tr = array(
		$book . ' (' . $collection . ')',
		$Node?$html->link($Node['sequence'] . ' ' . $Revision['title'], am($pass, array('page' => 1, 'node_id' => $Comment['node_id']))):'',
		$html->link($Comment['title'], array('action' => 'view', $Comment['id'])),
		$User?$html->link($User['username'], am($pass, array('page' => 1, 'user_id' => $Comment['user_id']))):'',
		$html->link($Comment['email'], am($pass, array('page' => 1, 'email' => $Comment['email']))),
		$html->link($Comment['published'], am($pass, array('page' => 1, 'published' => $Comment['published']))),
		$html->link($Comment['created'], am($pass, array('page' => 1, 'created' => $Comment['created']))),
		$actions
	);
	echo $html->tableCells($tr, array('class' => 'odd'), array('class' => 'even'));
}
?>
</table>
<?php echo $this->element('paging'); ?></div>