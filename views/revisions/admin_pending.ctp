<?php /* SVN FILE: $Id: admin_pending.ctp 672 2008-10-06 14:03:23Z AD7six $ */ ?>
<h1>Pending Submissions</h1>
<div class="container">
<?php echo
$this->set('modelClass', 'Revision');
$this->element('filter', array('filters' => array(
	'Node.sequence',
	'title',
	'lang',
	'status'
))); ?>
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
	$paginator->sort('lang'),
	$paginator->sort('User','User.username'),
	$paginator->sort('Email', 'User.email'),
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
	$actions[] = $html->link('Approve', array('action' => 'approve', $Revision['id']));
	$actions[] = $html->link('Reject', array('action' => 'reject', $Revision['id']));
	$actions[] = $html->link('Edit', array('action' => 'edit', $Revision['id']));
	//$actions[] = $html->link('Delete', array('action' => 'delete', $Revision['id']));
	if(empty($Revision['node_id']) && !empty($UnderNode['sequence']) ){
	$sequence = $html->link('{'.$UnderNode['sequence'].'}', am($pass, array('page' => 1, 'node_id' => $UnderNode['id'])), array('title' => 'New Section: under - '.$UnderNode['sequence'].' after: '.$AfterNode['sequence'] ));
	} else {
		$sequence = $html->link($Node['sequence'], am($pass, array('page' => 1, 'node_id' => $Node['id'])));
	}
	$tr = array (
		$html->link($Revision['id'], array('action' => 'view', $Revision['id'])),
		$book . ' (' . $collection . ')',
		$sequence,
		$html->link($Revision['title'],array('action'=>'view',$Revision['id'])),
		$html->link($Revision['lang'], am($pass, array('page' => 1, 'lang' => $Revision['lang']))),
		$User?$html->link($User['username'], am($pass, array('page' => 1, 'user_id' => $Revision['user_id']))):'',
		$User?'<a href="mailto:' . $User['email'] . '">' . $User['email'] . '</a>':'',
		$html->link($Revision['created'], am($pass, array('page' => 1, 'created' => $Revision['created']))),
		implode($actions, ' - ')
	);
	echo $html->tableCells($tr);
}
?>
</table>
<?php echo $this->element('paging'); ?>
</div>